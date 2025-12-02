<?php
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


//recuperer tous les artistes
try {

    $ArtistsTotal = $db->executeQuery(<<<SQL
        SELECT id, name, cover FROM artist
    SQL
    );

} catch (PDOException $ex) {
    echo "Erreur requête base de données : " . $ex->getMessage();
    exit;
}

//HTML
$HtmlArtists = '<div class="row">';

foreach ($ArtistsTotal as $artist) {

    $artistName = $artist['name'];
    $artistId = $artist['id'];
    $artistCover = $artist['cover'];

    $HtmlArtists .= <<<HTML
            <div class="col-lg-3 col-md-6 mb-4">
                <a href="artist.php?id=$artistId" class="text-decoration-none text-white">
                    <div class="container py-4">
                        <img src="$artistCover" class="card-img-top rounded-circle" alt="Image 1">
                        <div class="card-body text-white text-center">
                            <h5 class="card-title">$artistName</h5>
                        </div>
                    </div>
                </a>
            </div>
HTML;
}

$HtmlArtists .= '</div>';

//HTLM final
$html = <<<HTML
<div class="container py-4">
        <a href="index.php" class="btn btn-outline-light mb-4">← Retour</a>

    <h1 class="mb-4">Artistes</h1>
    
    $HtmlArtists
    
</div>
HTML;

//affichage HTML
echo (new HTMLPage(title: "Artiste - Lowify"))
    ->setupBootstrap([
        "class" => "bg-black text-white",
        "data-bs-theme" => "dark"
    ])
    ->setupNavigationTransition()
    ->addContent($html)
    ->render();