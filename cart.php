<?php
include __DIR__ . "/header.php";

?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Winkelwagen</title>
</head>
<body>
    <h1 class="HeaderWinkelmand">Winkelmandje</h1>

    <div class="CartGeheleTables">

<?php

$cart = getCart();

if(isset($_POST['refresh'])){ //update de hoeveelheden als iemand de bestelling wil aanpassen
    foreach ($cart as $productid => $aantal){
        if(isset($_POST[$productid])) {
            if ($_POST[$productid] != $aantal && $_POST[$productid] != "") {
                if ($_POST[$productid] > 0) {
                    addProductToCart($productid, $_POST[$productid]);
                }else{
                    $_POST['delete'] = $productid;
                }
            }
        }
    }
}

if(isset($_POST['delete'])){ //verwijdert item als er op een verwijder knop is gedrukt
    unset($cart[$_POST['delete']]);
    saveCart($cart);
}

if (isset($_POST['order'])) {
    $deliveryDate = date('Y-m-d', strtotime(date("Y-m-d"). ' + 3 days'));
    createOrder($databaseConnection, 7, 2, 1, 2, 62162, date("Y-m-d"), $deliveryDate, 18507, 1, "Hallo Douwe en Puja", "Pleur door de brievenbus", "Existence is pain", 7, date("Y-m-d H:i:s"));
    foreach ($cart as $productID => $aantal) {
        $product = ophalenProduct($databaseConnection, $productID);
        createOrderLine($databaseConnection, $product['StockItemID'], $product['StockItemName'], 7, $aantal, $product['UnitPrice'], $product['TaxRate'], $aantal, 7, date("Y-m-d H:i:s"));
        gegevensUpdaten($databaseConnection, $aantal, $productID);
    }unset($_SESSION['cart']);
}


$cart = getCart();
ksort($cart);

if(!empty($cart)) { //checkt of er items in de cart zitten, zo ja, dan print hij een tabel, anders print hij een melding
    print("    
<div id='WinkelmandKnopBoven' class='KnopBovenOnder'>
            <h3 class='VerderWinkelen'><a href='browse.php' class='HrefDecoration'>verder winkelen</a></h3>
            <h3 class='DoorgaanBetalen'><a href='browse.php' class='HrefDecoration'>Doorgaan naar betalen</a></h3>
        </div>

<table id='cart_table'>
    <tr>
        <th >Product</th>
        <th>Aantal</th>
        <th>Subtotaal</th>
    </tr>
    <form method='post' id='cart'>
");
//Regel met product info

    $som = 0;
    foreach ($cart as $productid => $aantal) {
        if ($productid != 0 || $productid != "") {
            $row = gegevensOphalen($productid,$databaseConnection);

            $prijs = round($row['SellPrice'] * $aantal, 2);
            //de prijs formule is direct overgenomen vanuit view.php
            $naam = $row['StockItemName'];
            $afbeelding = $row['ImagePath'];
            $beschrijving = $row['SearchDetails'];
            $belasting = round($row['TaxRate'], 1);
            $Stock = $row['QuantityOnHand'];
            $som += $prijs;
            print("<tr>");
            print("<td xmlns=\"http://www.w3.org/1999/html\"> <div class='Cart-ProductInfo'>
                            <img alt='$beschrijving' src='Public\StockItemIMG/$afbeelding'>
                            <div>
                                  <h6>$naam</h6> 
                                  <span>$beschrijving</span>
                        </div> 
                   </td>");
            print("<td >   <div class='CartAantalInfo'>
                                <label for='aantal'>Aantal:</label>
                                <input type='number' name=$productid placeholder='$aantal' value='$aantal' min='0' max='$Stock' id='aantal' step='1'>
                                <button type='submit' form='cart' name='refresh' >Update cart</button> 
                                <div class='CartVerwijderKnop'>
                                    <button id='delete' name='delete' type='submit' value=$productid >verwijderen</button>
                                </div>
                            </div>
                    </td>");
            print("<td> 
                        <div class='ProductSubtotaal'>
                            €$prijs
                           <div class='ProductBelasting'>Inclusief $belasting% BTW</div>
                        </div>
                   </td>");
            print("</tr>");


        }
    }
    print("</table>");
    print("</form>");
}


if(!empty($cart)) {
    //de hoeveelheid voor verzendkosten is op dit moment een placeholder
    $verzend = round($som * 0.01, 2);
    $totaal = $som + $verzend;
    //tabel regels voor de totalen
    print(" 
    <div class='TotaalPrijs'>
        <table>
            <tr>
                <td>Subtotaal</td>
                <td>€$som</td>
            </tr>
        
            <tr>
                <td>Verzendkosten</td>
                <td>€$verzend</td>
            </tr>
            <tr>
                <td>Totaal</td>
                <td>€$totaal</td>
            </tr>
            <!--
            <tr>
                <td> <button type='submit' form='cart' name='refresh' >Update cart</button> </td>
                <td></td>
                <td></td>
                <td></td>
                <td><button>Place order!</button></td> 
            </tr> -->
        </table>
    </div>
    
");
}

if(empty($cart)){
    if(isset($_POST['order'])){
        print("<h4 class='LeegWinkelwagen'>Bedankt voor uw bestelling</h4>");
        unset($_POST['order']);
    }else {
        print("
                <h4 class='LeegWinkelwagen'>Je winkelwagen is leeg.</h4>
            
    ");
    }//voor als er niks in de cart zit
}
//gegevens per artikelen in $cart (naam, prijs, etc.) uit database halen
//totaal prijs berekenen
//mooi weergeven in html
//etc.

if(!empty($cart)) {
    print(" 
   <div id='WinkelmandKnopOnder' class='KnopBovenOnder'>
            <h3 class='VerderWinkelen'><a href='browse.php' class='HrefDecoration'>verder winkelen</a></h3>
            <!--<h3 class='DoorgaanBetalen'><a href='browse.php' class='HrefDecoration'>Doorgaan naar betalen</a></h3> -->
            <td><button type='submit' form='cart' name='order'>Place order!</button></td>
            
        </div>
");
}


?>


        </div>
    </body>
</html>