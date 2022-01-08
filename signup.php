<?php

include __DIR__ . "/header.php";

//verwijzing naar PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

if (isset($_SESSION['UserLogin'])) {
    header("location: index.php");
}


if (isset($_POST['signup_submit'])) {

    $databaseConnection = connectToDatabase();

    $vnaam = $_POST ["vnaam"];
    $anaam = $_POST["anaam"];
    $naam = "$vnaam $anaam";
    $postcode = $_POST["postcode"];
    $huisnummer = $_POST["huisnmr"];
    $straat = $_POST["straat"];
    $afleveradres = "$straat $huisnummer";
    $plaats = $_POST ["plaats"];
    $gbdatum = $_POST ["datum"];
    $telefoonnummer = $_POST["nmr"];
    $email = $_POST["mail"];
    $DeliveryCityID = $_POST ["plaats"];
    $wachtwoord = $_POST["pwd"];
    $wachtwoordHH = $_POST["pwd-repeat"];
    $EncryptPassword = password_hash($wachtwoord, PASSWORD_DEFAULT);
    $mailcheck = false;
    $wwcheck = false;
    $pccheck = false;
    $leegeveldencheck = false;
    $citycheck = false;
    $nieuwsbrief = $_POST['nbbox'];



#testen op geldig email adres
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mailcheck = true;
    } else {
        echo " geen geldig email "."<br>";
    }


#testen of 2x het zelfde ww
    if ($wachtwoord == $wachtwoordHH) {
        $wwcheck = true;
    } else {
        echo "wachtwoord komt niet overeen"."<br>";
    }

# controleren of postcode juiste format heeft
    if ( preg_match("/^[1-9][0-9]{3}\s[a-zA-Z]{2}$/", $postcode))  {
        $pccheck = true;
    } else {
        echo "postcode niet correct" . "<br>";
    }


# chek of alle verplichte velden zijn ingevuld
if (empty($vnaam) || empty($anaam)  || empty($huisnummer) || empty($straat) || empty($plaats) || empty($telefoonnummer)  || empty($wachtwoord) || empty($wachtwoordHH)) {
    echo "niet alle velden correct ingevuld"."<br>";
} else {
    $leegeveldencheck= true;
}


    if ( CheckCityName($databaseConnection, $plaats) == false) {
        echo "geef een geldige plaatsnaam op". "<br>";
    } else {
        $DeliveryCityID = CheckCityName($databaseConnection, $plaats);
        $citycheck = true;
    }


# controleren of alle checks true zijn en dan hoppa door na de database
    if ($mailcheck == true && $wwcheck == true && $pccheck == true && $leegeveldencheck == true && $citycheck == true) {
        //check of nieuwsbrief checkbox wel TRUE of FALSE is, als dat niet het geval is wordt nieuwsbrief NULL en insert het niet in de database vanwege de NOT NULL constraint
        if ($nieuwsbrief == FALSE) {
            $nieuwsbrief = 0;
        } elseif ($nieuwsbrief == TRUE) {
            $nieuwsbrief = 1;
            aanmeldingNieuwsbrief($mail, $email);
        } else {
            $nieuwsbrief = NULL;
            echo "Geen tekst bij nieuwsbrief mogelijk.<br>";
        }
        InsertAccount($databaseConnection, $naam, $email, $EncryptPassword, $telefoonnummer, $DeliveryCityID, $postcode, $afleveradres, $nieuwsbrief);
        header("location: login.php?succes=Account registeren gelukt!");
    }
}
?>

<html>
<main>
    <div class="accountmaken">
        <section class ="section-default">
            <h1>account creeren</h1>
            <form class="form-signup" method="post">

                <label for="vnaam">Voornaam</label>
                <input type="text" id="vnaam" name="vnaam" placeholder="Voornaam" label="Voornaam" required>

                <label for="anaam">Achternaam</label>
                <input type="text" id="anaam" name="anaam" placeholder="Achternaam" required>

                <label for="postcode">Postcode</label>
                <input type="text" id="postcode" name="postcode" placeholder="Postcode" required>

                <label for="Huisnmr">Huisnummer en toevoeging</label>
                <input type="text" id="Huisnmr" name="huisnmr" placeholder="Huisnummer en toevoeging" required>

                <label for="straat">Straatnaam</label>
                <input type="text" id="straat" name="straat" placeholder="Staatnaam" required>

                <label for="pltnaam">Plaatsnaam</label>
                <input type="text" id="plaats" name="plaats" placeholder="Plaatsnaam" required>

                <label for="telnmr">Telefoonnummer</label>
                <input type="number" id="telnmr" name="nmr" placeholder="Telefoon nummer" required>

                <h2> </h2>
                <h3> Account gegevens </h3>
                <label for="mail">e-mailadres</label>
                <input type="email" id="mail" name="mail" placeholder="e-mailadres" required>

                <label for="pwd">Wachtwoord</label>
                <input type="password" id="pwd" name="pwd" placeholder="Wachtwoord" required>

                <label type="pwdhh">Herhaal wachtwoord</label>
                <input type="password" id="pwdhh" name="pwd-repeat" placeholder="Herhaal wachtwoord" required>

                <input type="checkbox" id="nbbox" name="nbbox" value="nieuwsbrief">
                <label for="nbbox">Ik wil mijzelf aanmelden voor de nieuwsbrief.</label>

                <button type="submit" name="signup_submit">account aanmaken</button>
            </form>
        </section>
    </div>
</main>
</html>
