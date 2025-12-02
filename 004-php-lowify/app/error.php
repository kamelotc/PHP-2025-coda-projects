<?php

require_once 'inc/page.inc.php';
require_once 'inc/database.inc.php';

//message d'erreur afficher
$message = $_GET['message'] ?? "Une erreur inconnue est survenue.";
//sécuriser  l'affichage
$safeMessage = htmlspecialchars($message);

//HTML
$htmlContent = <<<HTML
<div class="container d-flex flex-column justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="text-center">
        <h1 class="display-4 text-danger fw-bold mt-3">Erreur</h1>
        <p class="lead text-white mt-3">
            $safeMessage
        </p>
        <div class="mt-5">
            <a href="artists.php" class="btn btn-outline-light btn-lg">
                &larr; Retour à la liste des artistes
            </a>
            <br><br>
            <a href="index.php" class="text-secondary text-decoration-none small">
                Retour à l'accueil
            </a>
        </div>
    </div>
</div>
HTML;

//affichage HTML
echo (new HTMLPage(title: "Erreur - Lowify"))
    ->setupBootstrap([
        "class" => "bg-black text-white",
        "data-bs-theme" => "dark"
    ])
    ->addContent($htmlContent)
    ->render();