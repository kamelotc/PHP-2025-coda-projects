<?php

/*function "generateSelectOptions"($selected = 12): string
{

    $html = "";

    $options = range(8, 42);

    foreach ($options as $value) {

        $attribute = "";
        if ((int) $value == (int) $selected) {
            $attribute = "selected";
        }

        $html .= "<option $attribute value=\"$value\">$value</option>";
    }

    return $html;
}*/




$html = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>PassGenerator</title>
</head>

<body>
<h1>Générateur de mots de passe</h1>

      <form method="POST" action="index.php">
            
            <div>
                <p><strong>Mot de passe généré: </strong></p>
                <input type="text"/>
            </div>
            
            <div>
                    <label for="size" class="form-label">Taille</label>
                    <select class="form-select" aria-label="Default select example" name="size">
                    <option value="8">8</option>
                    </select>
            </div>
            
    
            <div class="">
                <input class="" type="checkbox" id="majuscules" value="1" name="majuscules" checked />
                <label class="" >majuscules</label>
            </div>
        
            <div class="">
                <input class="" type="checkbox" id="minuscules" value="1" name="minuscules" checked />
                <label class="" for="minuscules">minuscules</label>
            </div>
      
            <div class="">
                <input class="" type="checkbox" id="chiffres" value="1" name="chiffres" checked />
                <label class="" for="chiffres">chiffres</label>
            </div>
      
            <div class="">
                <input class="" type="checkbox" id="symboles" value="1" name="symboles" checked  />
                <label class="" for="symboles">symboles</label>
            </div>
     
    
            <div class="">
                <button type="submit" class="">Générer</button>
            </div>
    
        </form>

    </body>
</html>
HTML;

echo $html;