<?php
include __DIR__ . "/header.php";
$databaseConnection = connectToDatabase();
$cart = getCart();
?>

<!DOCTYPE html>
<html lang="nl" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <title>NerdyGadgets</title>
</head>
    <body>

    <?php if(isset($_GET['error'])) { //Voor errors te laten zien ?>
        <div id="AlertOrder" class="Alert">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            <?php print(htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'));?>
        </div>
    <?php } ?>

    <?php //Als winkelwagen leeg is wordt je naar index gestuurd.
    if (empty($_SESSION['cart'])){
        header("location: index.php");
        exit();
    }

    if (isset($_SESSION['UserLogin'])){ //Adres gegevens ophalen als klant is ingelogd.
        list($KlantNaam,$KlantAdres,$KlantPlaats,$KlantPostcode,$KlantTelNmr, $KlantCityID) = AdresKlantOphalen($databaseConnection);
    }

    if (isset($_POST['DoorgaanBetalen'])) { // Zet de bedragen in sessions zodat ze niet weg worden gehaald als je de form voor order submit drukt.
        $_SESSION['Subtotaal'] = $_POST['Subtotaal'];
        $_SESSION['Verzendkosten'] = $_POST['Verzendkosten'];
        $_SESSION['Korting'] = $_POST['Korting'];
        $_SESSION['Totaal'] = $_POST['Totaal'];
    }


    if (isset($_POST['order']) || isset($_POST['GeenAccountOrder']) ) { //Order plaatsen
        if (isset($_POST['order'])){ //Als de klant ingelogd is gebruikt die de opgeslagen adres gegevens
            $DeliveryAddress = $KlantAdres;
            $DeliveryCityID = $KlantCityID;
            $DeliveryPostalCode = $KlantPostcode;
            $PhoneNumber = $KlantTelNmr;
            $UserName = $KlantNaam;
        } elseif (isset($_POST['GeenAccountOrder'])){ //als de klant niet ingelogd is gebruikt die de ingevulde adres gegevens
            if (empty($_POST['GeenUsername']) || empty($_POST['GeenAdres']) || empty($_POST['GeenPlaats']) || empty($_POST['GeenPostcode']) || empty($_POST['GeenTelNummer'])){ //check of alle velden zijn ingevuld
                header("location: order.php?error=Niet alle velden zijn ingevuld");
                exit();
            } elseif (!preg_match("/^[1-9][0-9]{3}\s[a-zA-Z]{2}$/",$_POST['GeenPostcode'])) {
                header("location: order.php?error=Geef een geldige postcode op");
                exit();
            }else {
                if (CheckCityName($databaseConnection, $_POST['GeenPlaats']) != false){ //check als de plaatsnaam wel in de database staat
                    $CityID = CheckCityName($databaseConnection, $_POST['GeenPlaats']);
                    $DeliveryAddress = $_POST['GeenAdres'];
                    $DeliveryCityID = $CityID;
                    $DeliveryPostalCode = $_POST['GeenPostcode'];
                    $PhoneNumber = $_POST['GeenTelNummer'];
                    $UserName = $_POST['GeenUsername'];
                } else{ //plaatsnaam staat niet in de database dus geeft die error mee in de header
                    header("location: order.php?error=Geef een geldige plaatsnaam op");
                    exit();
                }
            }
        } //Order in de database zetten
        mysqli_begin_transaction($databaseConnection);
        try {
            CreateNewOrder($databaseConnection, $_SESSION['Subtotaal'], $_SESSION['Verzendkosten'], $_SESSION['Korting'], $_SESSION['Totaal'], $DeliveryAddress, $DeliveryCityID, $DeliveryPostalCode, $PhoneNumber, $UserName);
            foreach ($cart as $productID => $Quantity) { //Orderlines aanmaken en stock bijwerken
                $product = ophalenProduct($databaseConnection, $productID);
                CreateNewOrderLine($databaseConnection, $product['StockItemID'], $product['StockItemName'], $Quantity, $product['UnitPrice'], $product['TaxRate']);
                gegevensUpdaten($databaseConnection, $Quantity, $productID);
            }
             mysqli_commit($databaseConnection);
            } catch (mysqli_sql_exception $exception){
                mysqli_rollback($databaseConnection);
                throw $exception;
            }
        unset($_SESSION['Subtotaal']);
        unset($_SESSION['Verzendkosten']);
        unset($_SESSION['Korting']);
        unset($_SESSION['Totaal']);
        $_SESSION['CompletedOrder'] = 1;
        header("location: ordercomplete.php");
        exit();
    }

    if (isset($_POST['verandergegevens'])) { //Order plaatsen voor ingelogde klant en adres veranderen.
        $UserName = $_POST['VeranderUsername'];
        $DeliveryAddress = $_POST['VeranderAdres'];
        $City = $_POST['VeranderPlaats'];
        $DeliveryPostalCode = $_POST['VeranderPostcode'];
        $PhoneNumber = $_POST['VeranderTelefoonnummer'];
        if (empty($DeliveryAddress) || empty($City) || empty($DeliveryPostalCode) || empty($PhoneNumber) || empty($UserName)) { //checken of er legen velden zijn
            header("location: order.php?error=Niet alle velden zijn ingevuld");
            exit();
        } elseif (CheckCityName($databaseConnection, $City) == false) { //checken of de plaats in de database staat
            header("location: order.php?error=Geef een geldige plaatsnaam op");
            exit();
        } elseif (!preg_match("/^[1-9][0-9]{3}\s[a-zA-Z]{2}$/",$DeliveryPostalCode)){
            header("location: order.php?error=Geef een geldige postcode op");
            exit();
        } else { //order plaatsen en adres gegevens veranderen
            $CityID = CheckCityName($databaseConnection, $City);
            mysqli_begin_transaction($databaseConnection);
            try {
                VeranderAdres($databaseConnection, $DeliveryAddress, $CityID, $DeliveryPostalCode, $PhoneNumber, $UserName, $_SESSION['UserLogin']);
                CreateNewOrder($databaseConnection, $_SESSION['Subtotaal'], $_SESSION['Verzendkosten'], $_SESSION['Korting'], $_SESSION['Totaal'], $DeliveryAddress, $CityID, $DeliveryPostalCode, $PhoneNumber, $UserName);
                foreach ($cart as $productID => $Quantity) {
                    $product = ophalenProduct($databaseConnection, $productID);
                    CreateNewOrderLine($databaseConnection, $product['StockItemID'], $product['StockItemName'], $Quantity, $product['UnitPrice'], $product['TaxRate']);
                    gegevensUpdaten($databaseConnection, $Quantity, $productID);
                }
                mysqli_commit($databaseConnection);
            } catch (mysqli_sql_exception $exception){
                mysqli_rollback($databaseConnection);
                throw $exception;
            }
            unset($_SESSION['Subtotaal']);
            unset($_SESSION['Verzendkosten']);
            unset($_SESSION['Korting']);
            unset($_SESSION['Totaal']);
            $_SESSION['UserName'] = $UserName;
            $_SESSION['CompletedOrder'] = 1;
            header("location: ordercomplete.php");
            exit();
        }
    }

    if (!isset($_POST['DoorgaanBetalen']) && !isset($_GET['error'])){ //Als je hier niet via de winkelmand komt wordt je naar index gestuurd.
        header("location: index.php");
        exit();
    }


    if (!isset($_SESSION['UserLogin'])){ //html print voor als de klant niet ingelogd is.
        print('
        
         <div class="OrderContainer">
            <div class="BezorgAdres">
                <h1>Bezorgadres</h1>
                <div class="PlaceOrderForm">
                <div class="AdresVeranderen">
                    <form method="POST" id="cart">
                        <label for="OrderUsername">Naam</label>
                        <input type="text" maxlength="50" id="OrderUsername" name="GeenUsername" placeholder="Naam">
                        <label for="OrderAdres">Adres</label>
                        <input type="text" maxlength="100" id="OrderAdres" name="GeenAdres" placeholder="Adres">
                        <label for="OrderPlaats">Plaats</label>
                        <input type="text" maxlength="100" id="OrderPlaats" name="GeenPlaats" placeholder="Plaats">
                        <label for="OrderPostcode">Postcode</label>
                        <input type="text" maxlength="100" id="OrderPostcode" name="GeenPostcode" placeholder="Postcode">
                        <label for="OrderTelefoon">Telefoonnummer</label>
                        <input type="text" maxlength="100" id="OrderTelefoon" name="GeenTelNummer" placeholder="Telefoonnummer">
                        <button type="submit" form="cart" name="GeenAccountOrder">Plaats bestelling</button>
                    </form>
                    </div>
                </div>
            </div>
         </div>
        
        ');
    }

    if (isset($_SESSION['UserLogin'])){ //html print voor als de klant ingelogd is.
        print('
        <div class="OrderContainer">
            <div class="BezorgAdres">
                <h1>Bezorgadres</h1>
                <h3>Kloppen deze gegevens?</h3>
                ');
        print $KlantNaam . '<br>' . $KlantAdres . '<br>' . $KlantPlaats . '<br>' . $KlantPostcode . '<br>' . "Telefoonnummer: " . $KlantTelNmr;

        print ('
                <div class="PlaceOrderForm">
                    <form method="post" id="cart">
                        <label for="cart">Ja deze gegevens kloppen</label>
                        <button type="submit" form="cart" name="order">Plaats bestelling</button>
                    </form>
                </div>
            </div>
            <div class="AdresVeranderen">
                <h3>Gegevens veranderen</h3>
                <form action="#" method="POST">
                    <label for="OrderUsername">Naam</label>
                    <input type="text" maxlength="50" id="OrderUsername" name="VeranderUsername" placeholder="Naam">
                    <label for="OrderAdres">Adres</label>
                    <input type="text" maxlength="100" id="OrderAdres" name="VeranderAdres" placeholder="Adres">
                    <label for="OrderPlaats">Plaats</label>
                    <input type="text" maxlength="100" id="OrderPlaats" name="VeranderPlaats" placeholder="Plaats">
                    <label for="OrderPostcode">Postcode</label>
                    <input type="text" maxlength="100" id="OrderPostcode" name="VeranderPostcode" placeholder="Postcode">
                    <label for="OrderTelefoon">Telefoonnummer</label>
                    <input type="text" maxlength="100" id="OrderTelefoon" name="VeranderTelefoonnummer" placeholder="Telefoonnummer">
                    <label for="VeranderGegevens">Verander gegevens en plaats bestelling</label>
                    <button type="submit" id="VeranderGegevens" name="verandergegevens"><span>Plaats bestelling</span></button>
                </form>
            </div>
        </div>
        ');
    }

    ?>



    </body>
</html>
