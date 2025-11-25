<?php

$choice = $_GET["player"] ?? "pas choisi";

$possibleChoices = ["pierre", "feuille", "ciseaux"];

$phpChoice = ($choice === "pas choisi")
    ? "pas choisi"
    : $possibleChoices[array_rand($possibleChoices)];

if ($choice === "pas choisi" || $phpChoice === "pas choisi") {
    $result = "Faites un choix pour commencer la partie !";
}
else if ($choice === $phpChoice) {
    $result = "Égalité";
}
else if (
    ($choice === 'pierre' && $phpChoice === 'ciseaux') ||
    ($choice === 'feuille' && $phpChoice === 'pierre') ||
    ($choice === 'ciseaux' && $phpChoice === 'feuille')
) {
    $result = "GG ! Vous avez gagné !";
}
else {
    $result = "PHP WIN";
}

$phpDisplay = ($phpChoice === "pas choisi") ? "..." : $phpChoice;






$html = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Document</title>
</head>

<body>
<h1>Jeu Pierre, Feuille, Ciseaux</h1>

<p><strong>Joueur :</strong> $choice</p>
<p><strong>PHP :</strong> $phpDisplay</p>

<h4>Résultat : $result</h4>

<div>
    <a href="?player=pierre"><button>Pierre</button></a>
    <a href="?player=feuille"><button>Feuille</button></a>
    <a href="?player=ciseaux"><button>Ciseaux</button></a>
    <a href="/"><button>Réinitialiser</button></a>
</div>

</body>
</html>
HTML;

echo $html;
