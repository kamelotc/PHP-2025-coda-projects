<?php
function generateSelectOptions($selected = 12): string
{
    $html = "";
    $options = range(8, 42);

    foreach ($options as $value) {

        $attribute = "";
        if ((int)$value == (int)$selected) {
            $attribute = "selected";
        }

        $html .= "<option $attribute value=\"$value\">$value</option>";
    }

    return $html;
}

function takeRandom(string $subject): string {
    $index = random_int(0, strlen($subject) - 1);
    $randomChar = $subject[$index];
    return $randomChar;
}

function generatePassword(int $size, bool $majuscule, bool $minuscule, bool $number, bool $symbols):
string {
    $majusculedef = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $minusculedef = "abcdefghijklmnopqrstuvwxyz";
    $numberdef = "0123456789";
    $symbolsdef = "!/)($&-#<>=+*}{][";

    $ajoutdef = "";

    if ($majuscule) {
        $ajoutdef .= $majusculedef;
    }
    if ($minuscule) {
        $ajoutdef .= $minusculedef;
    }
    if ($number) {
        $ajoutdef .= $numberdef;
    }
    if ($symbols) {
        $ajoutdef .= $symbolsdef;
    }

    if (strlen($ajoutdef) == 0) {
        return "Cocher une Option Minimum !";
    }

    $password = "";
    for ($i = 0; $i < $size; $i++) {
        $password .= takeRandom($ajoutdef);
    }
    return $password;
}

$generate = "...";
$size = $_POST["size"] ?? 12;
$majusculedef = $_POST['majusculedef'] ?? 0;
$minusculedef = $_POST['minusculedef'] ?? 0;
$numberdef = $_POST['numberdef'] ?? 0;
$symbolsdef = $_POST['symbolsdef'] ?? 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $generate = generatePassword($size, $majusculedef, $minusculedef, $numberdef, $symbolsdef);
}
else
{
    $majusculedef = 1;
    $minusculedef = 1;
    $numberdef = 1;
    $symbolsdef = 1;
}

$sizeSelectorOptions = generateSelectOptions($size);
$majusculedefChecked = $majusculedef == 1 ? "checked" : "";
$minusculedefChecked = $minusculedef == 1 ? "checked" : "";
$numberdefChecked = $numberdef == 1 ? "checked" : "";
$symbolsdefChecked = $symbolsdef == 1 ? "checked" : "";


$page = <<<HTML
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>PassGénérator</title>
    <style>
    
    body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            padding: 20px;
        }

        h1 {
            color: #333;
        }

        .password-box {
            border: 1px solid #444;
            padding: 15px;
            font-size: 24px;
            background: white;
            margin-bottom: 20px;
        }

        form {
            background: white;
            padding: 15px;
            border: 1px solid #ccc;
            width: 300px;
        }

        button {
            margin-top: 10px;
            padding: 8px 16px;
            cursor: pointer;
        }
        
    </style>
</head>
<body>

<h1>Générateur de mot de passe</h1>

<div style="border:1px solid #333; padding:10px; width:fit-content;">
    <h2>$generate</h2>
</div>

<h3>Options :</h3>
<form method="POST">
    <label for="size">Taille :</label>
    <select name="size">
        $sizeSelectorOptions
    </select>

    <p><input type="checkbox" name="majusculedef" value="1" $majusculedefChecked> Majuscules</p>
    <p><input type="checkbox" name="minusculedef" value="1" $minusculedefChecked> Minuscules</p>
    <p><input type="checkbox" name="numberdef" value="1" $numberdefChecked> Chiffres</p>
    <p><input type="checkbox" name="symbolsdef" value="1" $symbolsdefChecked> Symboles</p>

    <button type="submit">Générer</button>
</form>

</body>
</html>
HTML;

echo $page;