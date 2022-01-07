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
if(isset($_SESSION["UserLogin"])){
    $customerID = $_SESSION["UserLogin"];
}else{
    $customerID = 0;
}


if(isset($_POST['refresh'])){ //update de hoeveelheden als iemand de bestelling wil aanpassen
    foreach ($cart as $productid => $aantal){
        if(isset($_POST[$productid])) {
            if ($_POST[$productid] != $aantal and $_POST[$productid] != "" and $_POST[$productid]<=vooraadOphalen($databaseConnection,$productid)) {
                if ($_POST[$productid] > 0 ){
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




if(isset($_POST["deleteKorting"])) {
    $kortingen = getKorting();
    if(array_key_exists($_POST["deleteKorting"],$kortingen)) {
        unset($kortingen[$_POST["deleteKorting"]]);
        kortingenOpslaan($kortingen);
        aantalKortingVerwijderen();
        unset($_POST["korting"]);
    }
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

if(!empty($cart)) {
    print(" 
   <div id='WinkelmandKnopOnder' class='KnopBovenOnder'>
            <h3 class='VerderWinkelen'><a href='browse.php' class='HrefDecoration'>verder winkelen</a></h3>  
        </div>
");
}


print("</div>");

if(!empty($cart)) { //checkt of er items in de cart zitten, zo ja, dan print hij een tabel, anders print hij een melding
    print("    
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

            $prijs = number_format($row['SellPrice'] * $aantal, 2,".","");
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
    print("</form>");
    //Berekeningen voor de kortingen
    //afhandelend formulier kortingen

    $kortingAantal=aantalKorting();
    if (isset($_POST['korting'])) {
        if(!array_key_exists($_POST["korting"],getKorting())) {
            if ($kortingAantal <= 3) {
                $korting = ophalenKorting($databaseConnection, $customerID, $_POST["korting"]);
                if ($korting != null) {
                    aantalKortingBijwerken();
                    kortingenToevoegen($_POST["korting"], $korting["Amount"], $korting["Percentage"], $korting["Verzend"]);
                    unset($_POST["korting"]);
                }
            }
        }
    }
    $kortingAantal=aantalKorting();
    if(!empty(getKorting())) {

            $kortingSom = 0;
            $verzendKortingSom = 0;
            foreach (getKorting() as $code => $hoeveelheid) {
                $korting = ophalenKorting($databaseConnection, $customerID, $code);
                if ($korting != null) {
                    $verzendKorting = $korting['Verzend'];
                    $korting = number_format(abs(berekenKorting($korting["Amount"], $korting["Percentage"], $som)), 2,".","");
                    $kortingSom += $korting;
                    $verzendKortingSom += $verzendKorting;

                }
            }
            if ($korting == 0) {
                $verzendKorting = 0;
                $korting = 0;
            }



        foreach (getKorting() as $code => $hoeveelheid) {
            print("<form name='korting' method='post' id='korting'><tr>");
            print("<td xmlns=\"http://www.w3.org/1999/html\"> <div class='Cart-ProductInfo'>
                            <div>
                                  <h6>$code</h6> ");
            print("<td >   <div class='CartAantalInfo'>
                                <div class='CartVerwijderKnop'>
                                    <button id='delete' name='deleteKorting' type='submit' value=$code >verwijderen</button>
                                </div>
                            </div>
                    </td>");
            print("<td> 
                        <div class='ProductSubtotaal'>");
            if ($hoeveelheid["Amount"] > 0) {
                print("- €" . $hoeveelheid['Amount']);
            } elseif ($hoeveelheid["Percentage"] < 1) {
                print("- " . 100 - $hoeveelheid['Percentage'] * 100 . "%");
            } elseif ($hoeveelheid["Verzend"]) {
                print("- " . $hoeveelheid['Verzend'] . "% Verzendkosten");
            }
            print("
                   </td>
                   </tr>
                   </form>");
        }
    }else{
        $verzendKortingSom = 0;
        $kortingSom = 0;
    }



    print("
            <form name='kortingInvoeren' method='post' id='kortingInvoeren'>
            <tr>
                <td>
                    <div class='Cart-ProductInfo'>");
                    if(isset($_POST['korting'])) { //HTML voor de korting invoeren form, moet nog kijken naar
                        if ($kortingAantal < 3) {
                            if (controleerKortingKlant($databaseConnection, $customerID, $_POST['korting']) != null) { //CUSTOMER ID MOET NOG MET INLOG GEMAAKT WORDEN

                                print("   
                                        <tr> 
                                            <td>Korting is toegevoegd!</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td><form method='post'> <input name='korting' type='text' placeholder='Kortingscode'>
                                            <input type='hidden' name='kortingAantal' value='$kortingAantal'></form></td>
                                            <td></td>
                                        </tr>
                  
                                ");
                            } elseif(isset($_POST["deleteKorting"])){
                                print("<tr>
                                            <td><form method='post'> <input name='korting' type='text' placeholder='Kortingscode'>
                                            <input type='hidden' name='kortingAantal' value='$kortingAantal'></form></td>
                                            <td></td>
                                        </tr>");
                                }else{
                                print("
                                <tr> 
                                    <td>Error: de code is niet gevonden</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><form method='post'> <input name='korting' type='text' placeholder='Kortingscode'>
                                    <input type='hidden' name='kortingAantal' value='$kortingAantal'></form></td>
                                    <td></td>
                                </tr>");
                            }
                        }else{
                            print("<td>U hebt het maximaal aantal kortingscodes gebruikt</td><td></td>");
                        }
                    }
                    else {
                            print("
                                    <tr>
                                        <td><form method='post'> <input name='korting' type='text' placeholder='Kortingscode'>
                                        <input type='hidden' name='kortingAantal' value='$kortingAantal'></form></td>
                                        <td></td>
                                    </tr>
        
                                ");
                        }




                    print("
                    </div>

                </td>

            </tr>



");
    print("</table>");
    print("</form>");
}


if(!empty($cart)) {

    //tabel regels voor de totalen
    print(" 
    <div class='TotaalPrijs'>
        <table>");
    //convert alle bedragen, zodat het netter staat
    $kortingSom = number_format($kortingSom,2,".","");
    $som = number_format($som,2,".","");
    $verzend = number_format(berekenVerzend($som,$verzendKortingSom),2,".","");
    $totaal = number_format($som + $verzend - $kortingSom,2,".","");

    if($totaal<0){
        $totaal = 0;
    }
    print("     <tr>
                <td>Subtotaal</td>
                <td>€$som</td>
            </tr>
        
            <tr>
                <td>Verzendkosten</td>
                <td>€$verzend</td>
            </tr>");
    if($kortingSom > 0){ //displayed geen korting als die er niet is
        print("<tr>
                <td>Korting:</td>
                <td>€$kortingSom</td>
            </tr>");
    }


    print("
            <tr>
                <td>Totaal</td>
                <td>€$totaal</td>
            </tr>
        </table>
    </div>
    
");
}

if(empty($cart)){

    print("
                <h4 class='LeegWinkelwagen'>Je winkelwagen is leeg.</h4>
            
    ");
}//voor als er niks in de cart zit



if(!empty($cart)) {
    print(" 
   <div id='WinkelmandKnopOnder' class='KnopBovenOnder'>
            <h3 class='VerderWinkelen'><a href='browse.php' class='HrefDecoration'>verder winkelen</a></h3> 
           <form method='post' id='FormOrder' action='order.php'>
            <input type='hidden' name='Subtotaal' value='$som'>
            <input type='hidden' name='Verzendkosten' value='$verzend'>
            <input type='hidden' name='Korting' value='$kortingSom'>
            <input type='hidden' name='Totaal' value='$totaal'>
            <input type='submit' class='HrefDecoration' name='DoorgaanBetalen' value='Doorgaan naar betalen'></form>
           
        </div>
");
}


?>


        </div>
    </body>
</html>