<?php
include __DIR__ . "/header.php";
$databaseConnection = connectToDatabase();
$cart = getCart();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>NerdyGadgets</title>
</head>
    <body>
    <?php
    if ($_SESSION['CompletedOrder'] != 1){ //zodat je niet naar ordercomplete.php kunt zonder dat je een bestelling hebt geplaatst
        header("location: index.php");
    } else {
        $_SESSION['cart'] = array(); //maakt cart leeg
        unset($_SESSION['korting']);
        $_SESSION['CompletedOrder'] = 0;
    }
    ?>

    <h1>Order is geplaatst</h1>

    </body>
</html>