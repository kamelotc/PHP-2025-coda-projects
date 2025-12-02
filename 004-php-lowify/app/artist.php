<?php
//fichier inclue
require_once 'inc/page.inc.php';
require_once 'inc/database.inc.php';

//initialisation de la BDD
try {
    $db = new DatabaseManager(
        dsn: 'mysql:host=mysql;dbname=lowify;charset=utf8mb4',
        username: 'lowify',
        password: 'lowifypassword'
    );
} catch (PDOException $ex) {
    echo "Erreur requête base de données  : " . $ex->getMessage();
    exit;
}

//Quel artiste afficher
if (empty($_GET['id'])) {
    header("Location: error.php?message=Aucun-artiste-spécifié");
    exit;
}
$artistId = (int)$_GET['id'];

//recuper les infos de l'artiste
$artistDetail = $db->executeQuery(<<<SQL
        SELECT * 
         FROM artist
         WHERE id = :artistId
    SQL,
    ['artistId' => $artistId]);

if (empty($artistDetail)) {
    header("Location: error.php?message=Aucun artiste");
    exit;
}
$artist = $artistDetail[0];

//recuper le top 5 chansons
try {
    $ChansonsTop5 = $db->executeQuery(<<<SQL
        SELECT
            s.id as song_id,
            s.name as song_name,
            s.duration as song_duration,
            s.note as song_note,
            a.cover as album_cover,
            a.name as album_name
        FROM song s
        INNER JOIN album  a ON s.album_id = a.id
        WHERE s.artist_id = :artist_id
        ORDER BY s.note DESC
        LIMIT 5   
    SQL,
        ['artist_id' => $artistId]);
} catch (PDOException $ex) {
    echo "Erreur de requête: " . $ex->getMessage();
    exit;
}

//recupaire les albums, trier dans l'orde decroissant
try {
    $AlbumsDetail = $db->executeQuery(<<<SQL
    SELECT *
    FROM album
    WHERE artist_id = :artistId
    ORDER BY release_date DESC 
SQL,
        ['artistId' => $artistId]);
}catch (PDOException $ex) {
    echo "Erreur requête albums : " . $ex->getMessage();
    exit;
}


//fonction pour le nombre d'ecoute
function formatListeners($n) {
    $n = (float)$n;

    if ($n >= 1000000000) {
        $v = $n / 1000000000;
        $s = number_format($v, 1, '.', '');
        return (substr($s, -2) === '.0') ? (string)(int)$v . 'B' : $s . 'B';
    }

    if ($n >= 1000000) {
        $v = $n / 1000000;
        $s = number_format($v, 1, '.', '');
        return (substr($s, -2) === '.0') ? (string)(int)$v . 'M' : $s . 'M';
    }

    $v = $n >= 1000 ? $n / 1000 : $n;
    $s = $n >= 1000 ? number_format($v, 1, '.', '') : (string)(int)$v;
    return (substr($s, -2) === '.0') ? (string)(int)$v . ($n >= 1000 ? 'k' : '') : $s . ($n >= 1000 ? 'k' : '');
}
//fonction pour le temps des sons
function formatDuration($seconds) {
    $min = floor($seconds / 60);
    $sec = $seconds % 60;
    return sprintf("%d:%02d", $min, $sec);
}


//variables des Artiste
$nom = htmlspecialchars($artist['name']);
$bio = nl2br(htmlspecialchars($artist['bio']??''));
$cover = htmlspecialchars($artist['cover']);
$listeners = formatListeners($artist['monthly_listeners']);


//html Chansons
$htmlSongs = '<div class="list-group mb-5">';
if (empty($ChansonsTop5)) {
    $htmlSongs .= '<p class="texte-muted">Aucune Chanson</p>';
} else {
    foreach ($ChansonsTop5 as $music) {
        $musicId = htmlspecialchars($music['song_id']);
        $musicName = htmlspecialchars($music['song_name']);
        $musicDuration = formatDuration($music['song_duration']);
        $musicNote = htmlspecialchars($music['song_note']);
        $albumCover = htmlspecialchars($music['album_cover']);
        $albumName = htmlspecialchars($music['album_name']);

        $htmlSongs .= <<<HTML
        <div class=" text-white border-secondary d-flex align-items-center px-3 py-2">
            <img src="$albumCover" class="rounded me-3" style="width: 40px; height: 40px; object-fit: cover;">
            <div class="flex-grow-1 fw-bold">$musicName</div>
            <div class="text-end text-muted small">
                <span class="me-3">$musicNote/5</span>
                <span>$musicDuration</span>
            </div>
        </div>
HTML;
    }
}
$htmlSongs .= '</div>';


//html Albums
$htmlAlbums = '<div class="row">';
if (empty($AlbumsDetail)) {
    $htmlAlbums .= '<p class="texte-muted">Aucun album</p>';
} else {
    foreach ($AlbumsDetail as $album) {
        $albumId = $album['id'];
        $albumName = htmlspecialchars($album['name']);
        $albumDate = date('Y', strtotime($album['release_date']));
        $albumCover = htmlspecialchars($album['cover']);


        $htmlAlbums .= <<<HTML
        <div class="col-6 col-md-3 col-lg-2 mb-4">
            <a href="album.php?id=$albumId" class="text-decoration-none text-white">
                <div class="card bg-transparent border-0 h-100">
                    <img src="$albumCover" class="card-img-top rounded shadow mb-2">
                    <h6 class="text-truncate mb-0 text-center">$albumName</h6>
                    <small class="d-block text-center text-muted">$albumDate</small>
                </div>
            </a>
        </div>
HTML;
    }
}
$htmlAlbums .= '</div>';


//html final
$HTML = <<<HTML
<div class="container py-4">
    <a href="artists.php" class="btn btn-outline-light mb-4">← Retour</a>
    
    <div class="d-flex flex-column flex-md-row align-items-center gap-4 mb-5">
        <img src="$cover" class="rounded-circle shadow" style="width: 200px; height: 200px; object-fit: cover;">
        <div class="text-center text-md-start">
            <h1 class="display-4 fw-bold">$nom</h1>
            <p class="fs-5 text-secondary">$listeners auditeurs mensuels</p>
            <div class="lead fs-6 text-light opacity-75">$bio</div>
        </div>
    </div>

    <h3>Top Titres</h3>
    $htmlSongs
    
    <h3 class="mt-4">Albums</h3>
    $htmlAlbums
</div>
HTML;



//afficher la page
echo (new HTMLPage(title: "Artiste - Lowify"))
    ->setupBootstrap([
        "class" => "bg-black text-white",
        "data-bs-theme" => "dark"
    ])
    ->setupNavigationTransition()
    ->addContent($HTML)
    ->render();