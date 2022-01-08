<?php
include __DIR__ . "/header.php";
$databaseConnection = connectToDatabase();
$cart = getCart();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Winkelwagen</title>
</head>

<?php if(isset($_GET['error'])) { ?>
    <div class="AlertVeldInloggen">
        <?php print(htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'));?>
    </div>
<?php } ?>
    <body>

    <?php

    if(!isset($_SESSION['UserLogin']) || $_SESSION['cart'] == array()){
        header("location: index.php");
        exit();
    }

    if (isset($_SESSION['UserLogin'])) {
        $sql = "SELECT us.UserName, us.DeliveryAddress, ci.CityName, us.DeliveryPostalCode, us.PhoneNumber FROM useraccounts us JOIN cities ci ON us.DeliveryCityID = ci.CityID WHERE us.PersonID = ? LIMIT 1";
        $Statement = mysqli_stmt_init($databaseConnection);
        if (!mysqli_stmt_prepare($Statement, $sql)) {
            header("location: order.php?error=SQL error");
            exit();
        } else {
            mysqli_stmt_bind_param($Statement, "i", $_SESSION['UserLogin']);
            mysqli_stmt_execute($Statement);
            mysqli_stmt_store_result($Statement);
            if ($Statement->num_rows == 1) {
                mysqli_stmt_bind_result($Statement, $naam, $adres, $plaats, $postcode, $telefoonnummer);
                mysqli_stmt_fetch($Statement);
                mysqli_stmt_close($Statement);
            }
        }
    }

    if (isset($_POST['order'])) {
        $deliveryDate = date('Y-m-d', strtotime(date("Y-m-d"). ' + 3 days'));
        createOrder($databaseConnection, $_SESSION['UserLogin'], 2, 1, 2, 62162, date("Y-m-d"), $deliveryDate, 18507, 1, "Hallo Douwe en Puja", "Pleur door de brievenbus", "Existence is pain", 7, date("Y-m-d H:i:s"));
        foreach ($cart as $productID => $aantal) {
            $product = ophalenProduct($databaseConnection, $productID);
            createOrderLine($databaseConnection, $product['StockItemID'], $product['StockItemName'], 7, $aantal, $product['UnitPrice'], $product['TaxRate'], $aantal, 7, date("Y-m-d H:i:s"));
            gegevensUpdaten($databaseConnection, $aantal, $productID);
        }
        $_SESSION['CompletedOrder'] = 1;
        header("location: ordercomplete.php");
    }

    if(isset($_POST['verandergegevens'])){
    $PostAdres = $_POST['adres'];
    $PostPlaats = $_POST['plaats'];
    $PostPostcode = $_POST['postcode'];
    $PostTelnummer = $_POST['telefoonnummer'];
        if (empty($PostAdres) || empty($PostPlaats) || empty($PostPostcode) || empty($PostTelnummer)) {
            header("location: order.php?error=Niet alle velden zijn ingevuld");
        } else {
            $sql = "SELECT CityID FROM cities WHERE CityName LIKE ? LIMIT 1;";
            $Statement = mysqli_prepare($databaseConnection, $sql);
            mysqli_stmt_bind_param($Statement, "s", $PostPlaats);
            mysqli_stmt_execute($Statement);
            mysqli_stmt_store_result($Statement);
            if($Statement->num_rows == 1) {
                mysqli_stmt_bind_result($Statement, $CityID);
                mysqli_stmt_fetch($Statement);
                mysqli_stmt_close($Statement);
                $sql ="UPDATE useraccounts SET DeliveryAddress = ?, DeliveryCityID = ?, DeliveryPostalCode = ?, PhoneNumber = ? WHERE PersonID = ?;";
                $Statement = mysqli_prepare($databaseConnection, $sql);
                mysqli_stmt_bind_param($Statement, "sissi", $PostAdres, $CityID, $PostPostcode, $PostTelnummer, $_SESSION['UserLogin']);
                mysqli_stmt_execute($Statement);
                $deliveryDate = date('Y-m-d', strtotime(date("Y-m-d"). ' + 3 days'));
                    createOrder($databaseConnection, $_SESSION['UserLogin'], 2, 1, 2, 62162, date("Y-m-d"), $deliveryDate, 18507, 1, "Hallo Douwe en Puja", "Pleur door de brievenbus", "Existence is pain", 7, date("Y-m-d H:i:s"));
                    foreach ($cart as $productID => $aantal) {
                    $product = ophalenProduct($databaseConnection, $productID);
                    createOrderLine($databaseConnection, $product['StockItemID'], $product['StockItemName'], 7, $aantal, $product['UnitPrice'], $product['TaxRate'], $aantal, 7, date("Y-m-d H:i:s"));
                    gegevensUpdaten($databaseConnection, $aantal, $productID);
                    $_SESSION['CompletedOrder'] = 1;
                    header("location: ordercomplete.php");
                }
            } else {
                header("location: order.php?error=Geef een geldige plaatsnaam op");
            }
        }
    }


    ?>
        <div class="OrderContainer">
            <div class="BezorgAdres">
                <h1>BezorgAdres</h1>
                <h3>Kloppen deze gegevens?</h3>
                <?php
                print($naam . '<br>');
                print($adres . '<br>');
                print($postcode . ", ");
                print($plaats . '<br>');
                print("Telefoon: " . $telefoonnummer . '<br>');
                ?>
                <div class="PlaceOrderForm">
                    <form method='post' id='cart'>
                        <label for="cart">Ja deze gegevens kloppen</label>
                        <button type='submit' form='cart' name='order'>Plaats bestelling</button>
                    </form>
                </div>
            </div>
            <div class="AdresVeranderen">
                <h3>Adres gegevens veranderen</h3>
                <form action="#" method="POST">
                    <label for="OrderAdres">Adres</label>
                    <input type="text" maxlength="100" id="OrderAdres" name="adres" placeholder="Adres">
                    <label for="OrderPlaats">Plaats</label>
                    <input type="text" maxlength="100" id="OrderPlaats" name="plaats" placeholder="Plaats">
                    <label for="OrderPostcode">Postcode</label>
                    <input type="text" maxlength="100" id="OrderPostcode" name="postcode" placeholder="Postcode">
                    <label for="OrderTelefoon">Telefoonnummer</label>
                    <input type="text" maxlength="100" id="OrderTelefoon" name="telefoonnummer" placeholder="Telefoonnummer">
                    <label for="VeranderGegevens">Verander gegevens en plaats bestelling</label>
                    <button type="submit" id="VeranderGegevens" name="verandergegevens"><span>Plaats bestelling</span></button>
                </form>
            </div>
        </div>
    </body>
</html>

