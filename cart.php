<?php
include __DIR__ . "/header.php";

?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Winkelwagen</title>
</head>

<h1>Inhoud Winkelwagen</h1>

<?php
$cart = getCart();


if(isset($_POST['refresh'])){ //update de hoeveelheden als iemand de bestelling wil aanpassen
    foreach ($cart as $productid => $aantal){
        if(isset($_POST[$productid])) {
            if ($_POST[$productid] != $aantal && $_POST[$productid] != "") {
                addProductToCart($productid, $_POST[$productid]);
            }
        }
    }
}

if(isset($_POST['delete'])){ //verwijdert item als er op een verwijder knop is gedrukt
    unset($cart[$_POST['delete']]);
    saveCart($cart);
}


$cart = getCart();
ksort($cart);
//bug: Item info wil verschijnt niet in tabel bijvoorbeeld bij productID 220, waardoor errors onstaan en de prijs incorrect is
if(!empty($cart)) { //checkt of er items in de cart zitten, zo ja, dan print hij een tabel, anders print hij een melding
    print("    

<table id='cart_table'>
    <tr>
        <th >ProductID</th>
        <th>Naam</th>
        <th>Aantal</th>
        <th>Prijs</th>
        <th></th>
    </tr>
    <form method='post' id='cart'>

");
//Regel met product info

    $som = 0;
    foreach ($cart as $productid => $aantal) {
        if ($productid != 0 || $productid != "") {
            $row = gegevensOphalen($productid);
            $prijs = round($row['SellPrice'] * $aantal, 2);
            //de prijs formule is direct overgenomen vanuit view.php
            $naam = $row['StockItemName'];
            $som += $prijs;
            print("<tr>");
            print("<td>$productid</td>");
            print("<td>$naam</td>");
            print("<td ><input type='number' name=$productid placeholder='$aantal' min='1'></td>");
            print("<td>$prijs</td>");
            print("<td> <button id='delete' name='delete' type='submit' value=$productid >verwijder</button> </td>");
            print("</tr>");


        }

    }
    //de hoeveelheid voor verzendkosten is op dit moment een placeholder
    $verzend = round($som * 0.01, 2);
    $totaal = $som + $verzend;
    //tabel regels voor de totalen
    print("
    </form>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td>Totaal</td>
        <td> excl. verzendkosten</td>
        <td></td>
        <td>$som</td>
        <td></td>
    </tr>

    <tr>
        <td>Verzendkosten</td>
        <td></td>
        <td></td>
        <td>$verzend</td>
        <td></td>
    </tr>
    <tr>
        <td>Totaal</td>
        <td> incl. verzendkosten</td>
        <td></td>
        <td>$totaal</td>
        <td></td>
    </tr>
    <tr>
        <td> <button type='submit' form='cart' name='refresh' >Update cart</button> </td>
        <td></td>
        <td></td>
        <td></td>
        <td><button type='submit' form='cart' name='order'>Place order!</button></td>
        
    </tr>
</table>");
} else{
    print("Your cart is empty"); //voor als er niks in de cart zit
}


//gegevens per artikelen in $cart (naam, prijs, etc.) uit database halen
//totaal prijs berekenen
//mooi weergeven in html
//etc.

if (isset($_POST['order'])) {
//    createOrder($databaseConnection, 7, 2, 1, 2, 62162, "2021-11-19", "2021-11-22", 18507, 1, "Je dikke kale moeder", "Pleur door de brievenbus", "Existence is pain", 7, "2021-11-19 13:08");
    foreach ($cart as $productID => $aantal) {
        $product = ophalenProduct($databaseConnection, $productID);
        print_r($product);
        print_r($aantal);
        createOrderLine($databaseConnection, 73623, $product['StockItemID'], $product['StockItemName'], 7, $aantal, $product['UnitPrice'], $product['TaxRate'], $aantal, 7, "2021-11-30 19:30");
    }
}

?>
</html>