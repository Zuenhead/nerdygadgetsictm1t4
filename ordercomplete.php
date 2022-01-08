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
    <body>
    <?php
    if ($_SESSION['CompletedOrder'] != 1){
        header("location: index.php");
    } else {
        $_SESSION['cart'] = array();
        $_SESSION['CompletedOrder'] = 0;
    }
    ?>

    <h1>Order is geplaatst</h1>
    </body>
</html>