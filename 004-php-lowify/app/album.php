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

//verification Id
if (empty($_GET['id'])) {
    header("Location: error.php?message=Aucun-album-spécifié");
    exit;
}
$albumId = (int)$_GET['id'];

//recupaire les albums, trier dans l'orde decroissant
try {
    $AlbumsDetail = $db->executeQuery(<<<SQL
    SELECT *
    FROM album
    WHERE id = :id
SQL,
        ['id' => $albumId]);
}catch (PDOException $ex) {
    echo "Erreur requête albums : " . $ex->getMessage();
    exit;
}


//album error
if (empty($AlbumsDetail)) {
    header("Location: error.php?message=Aucun-album");
    exit;
}
$album = $AlbumsDetail[0];
$albumId = $album ['artist_id'];


//recuper les infos de l'artiste
try {
    $artistDetail = $db->executeQuery(<<<SQL
        SELECT * 
         FROM artist
         WHERE id = $albumId
    SQL);

}catch (PDOException $ex) {
    echo "Erreur requête albums : " . $ex->getMessage();
    exit;
}

//si l'artiste a ete supprimer
if (empty($artistDetail)) {
    $artistName = "Artiste Inconnu";
    $artistId = 0;
} else {
    $artistName = htmlspecialchars($artistDetail[0]['name']);
    $artistId = $artistDetail[0]['id'];
}










//recupaire les chansons
try {
    $Chansons = $db->executeQuery(<<<SQL
        SELECT song.id AS id, song.name AS name, song.duration AS duration, song.note AS note, album.cover AS album_cover, album.name AS album_name 
        FROM song JOIN album ON song.album_id = album.id WHERE album_id = $albumId
        ORDER BY song.id ASC
    SQL);
} catch (PDOException $ex) {
    echo "Erreur de requête: " . $ex->getMessage();
    exit;
}

//fonction pour le temps des sons
function formatDuration($seconds) {
    $min = floor($seconds / 60);
    $sec = $seconds % 60;
    return sprintf("%d:%02d", $min, $sec);
}


//variables de l'album
$nomAlbum = ($album['name']);
$coverAlbum = ($album['cover']);
$dateAlbum = date('Y', strtotime($album['release_date']));


//html Chansons
$htmlSongs = '<div class="list-group mb-5">';
if (empty($Chansons)) {
    $htmlSongs .= '<p class="texte-muted">Aucune Chanson</p>';
} else {
    foreach ($Chansons as $music) {
        $musicId = $music['id'];
        $musicName = $music['name'];
        $musicDuration = $music['duration'];
        $musicNote = $music['note'];
        $albumCover = $music['album_cover'];
        $albumName = $music['album_name'];

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
        $albumName = $album['name'];
        $albumDate = date('Y', strtotime($album['release_date']));
        $albumCover = $album['cover'];


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
        <img src="$albumCover" class="rounded-circle shadow" style="width: 200px; height: 200px; object-fit: cover;">
        <div class="text-center text-md-start">
            <h1 class="display-4 fw-bold">$albumName</h1>
            <div class="lead fs-6 text-light opacity-75">$albumDate</div>
        </div>
    </div>

    <h3>Titres</h3>
    $htmlSongs
    
    <h3 class="mt-4">Albums</h3>
    $htmlAlbums
</div>
HTML;



//afficher la page
echo (new HTMLPage(title: "$albumName - Lowify"))
    ->setupBootstrap([
        "class" => "bg-black text-white",
        "data-bs-theme" => "dark"
    ])
    ->setupNavigationTransition()
    ->addContent($HTML)
    ->render();