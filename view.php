<!-- dit bestand bevat alle code voor de pagina die één product laat zien -->
<?php
include __DIR__ . "/header.php";


$StockItem = getStockItem($_GET['id'], $databaseConnection);
$StockItemImage = getStockItemImage($_GET['id'], $databaseConnection);
if(isset($StockItem)) {
    $ItemID = $StockItem['StockItemID'];
    $Stock = filter_var($StockItem["QuantityOnHand"], FILTER_SANITIZE_NUMBER_INT); //$StockItem geeft voor de voorraad "voorraad: x" dit haalt het nummer eruit
}

$tempratuur = tempratuurophalen($databaseConnection);
print_r($tempratuur);

if (empty($ItemID)){
    header("location: index.php");
}

?>

<div id="CenteredContent">
    <?php
    if ($StockItem != null) {
        ?>
        <?php
        if (isset($StockItem['Video'])) {
            ?>
            <div id="VideoFrame">
                <?php print $StockItem['Video']; ?>
            </div>
        <?php }
        ?>


        <div id="ArticleHeader">
            <?php
            if (isset($StockItemImage)) {
                // één plaatje laten zien
                if (count($StockItemImage) == 1) {
                    ?>
                    <div id="ImageFrame"
                         style="background-image: url('Public/StockItemIMG/<?php print $StockItemImage[0]['ImagePath']; ?>'); background-size: cover; background-repeat: no-repeat; background-position: center;"></div>
                    <?php
                } else if (count($StockItemImage) >= 2) { ?>
                    <!-- meerdere plaatjes laten zien -->
                    <div id="ImageFrame">
                        <div id="ImageCarousel" class="carousel slide" data-interval="false">
                            <!-- Indicators -->
                            <ul class="carousel-indicators">
                                <?php for ($i = 0; $i < count($StockItemImage); $i++) {
                                    ?>
                                    <li data-target="#ImageCarousel"
                                        data-slide-to="<?php print $i ?>" <?php print (($i == 0) ? 'class="active"' : ''); ?>></li>
                                    <?php
                                } ?>
                            </ul>

                            <!-- slideshow -->
                            <div class="carousel-inner">
                                <?php for ($i = 0; $i < count($StockItemImage); $i++) {
                                    ?>
                                    <div class="carousel-item <?php print ($i == 0) ? 'active' : ''; ?>">
                                        <img src="Public/StockItemIMG/<?php print $StockItemImage[$i]['ImagePath'] ?>">
                                    </div>
                                <?php } ?>
                            </div>

                            <!-- knoppen 'vorige' en 'volgende' -->
                            <a class="carousel-control-prev" href="#ImageCarousel" data-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </a>
                            <a class="carousel-control-next" href="#ImageCarousel" data-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </a>
                        </div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div id="ImageFrame"
                     style="background-image: url('Public/StockGroupIMG/<?php print $StockItem['BackupImagePath']; ?>'); background-size: cover;"></div>
                <?php
            }
            ?>


            <h1 class="StockItemID">Artikelnummer: <?php print($StockItem["StockItemID"]); ?></h1>
            <h2 class="StockItemNameViewSize StockItemName">
                <?php print $StockItem['StockItemName']; ?>
            </h2>

            <div class="QuantityText"><?php if ($Stock > 1000) { //Print verschilende waardes bij verschilende stock hoeveelheden.
                    print "Ruime voorraad beschikbaar";
                } elseif ($Stock <= 0){
                    print "Niet op voorraad";
                } else {
                    print  $StockItem['QuantityOnHand'];
                } ?></div>
            <div id="StockItemHeaderLeft">
                <div class="CenterPriceLeft">
                    <div class="CenterPriceLeftChild">
                        <p class="StockItemPriceText"><b><?php print sprintf("€ %.2f", $StockItem['SellPrice']); ?></b></p>
                        <h6> Inclusief BTW </h6>
                    </div>
                </div>
            </div>
            <div id="ToevoegenProduct">
                <form class = "cart" method="post">
                    <input type="number" name="stockItemID" value="<?php print($ItemID) ?>" hidden>
                    <input type="number" id="QuantityDisplay" name='amount' placeholder='1' value="1" min='1' max="<?php print($Stock)?>">
                    <input type="submit" id="ImageButton" name="submit" value="Add to cart">
                </form>
                <?php
                if (isset($_POST["submit"])) {              // zelfafhandelend formulier
                    $amount = (int)$_POST["amount"] ;
                    if($_POST['amount'] > 0) {
                        addProductToCart($ItemID, $amount);
                        print("<div> Product added to <a href='cart.php'> cart!</a></div>"); //confirmatie winkelmand
                    }
                }else{
                    $amount = 0;
                }
                ?>
            </div>
        </div>


        <div id="StockItemDescription">
            <h3>Artikel beschrijving</h3>
            <p><?php print $StockItem['SearchDetails']; ?></p>

        </div>



        <div id="StockItemSpecifications">

            <h3>Artikel specificaties</h3>
            <?php
            $CustomFields = json_decode($StockItem['CustomFields'], true);
            if (is_array($CustomFields)) { ?>
                <table>
                <thead>
                <th>Naam</th>
                <th>Data</th>
                </thead>
                <?php
                foreach ($CustomFields as $SpecName => $SpecText) { ?>
                    <tr>
                        <td>
                            <?php print $SpecName; ?>
                        </td>
                        <td>
                            <?php
                            if (is_array($SpecText)) {
                                foreach ($SpecText as $SubText) {
                                    print $SubText . " ";
                                }
                            } else {
                                print $SpecText;
                            }
                            ?>
                        </td>
                    </tr>
                <?php } ?>
                </table><?php
            } else { ?>

                <p><?php print $StockItem['CustomFields']; ?>.</p>
                <?php
            }
            ?>


        </div>

            <?php  $alternatieven = array(alternatiefOphalen($databaseConnection,$ItemID));
            if (!empty($alternatieven[0])) {
                print ("<table id='alternatieven'>");
                print (" <tr> <th class='AlterHeader'>Alternatieven</th> </tr> ");
                print("<tr>");
            }
            for($i=0; $i<3; $i++) {
                if (isset($alternatieven[0][$i]['stockitemid'])){
                    $afbeelding = $alternatieven[0][$i]["imagepath"];
                    print("<th> <a class='HrefDecoration' href='view.php?id=" . $alternatieven[0][$i]['stockitemid'] ."'> <img src='Public/StockItemIMG/" . $afbeelding . "'  alt=". $alternatieven[0][$i]['stockitemid'] . "><br>" .
                      "<div class='AlterText'> " . $alternatieven[0][$i]['stockitemname'] . "</div>" . "<div class='AlterPrijs'>" . "<br> € " . number_format($alternatieven[0][$i]['SellPrice'], 2,".",",")  . "</div>" . "</th>");

                }
            }

        if (!empty($alternatieven[0])){
            print("</tr> </table>");
        }

            ?>

        <?php
    } else {
        ?><h2 id="ProductNotFound">Het opgevraagde product is niet gevonden.</h2><?php
    }
    ?>




</div>

<?php
if (isset($_SESSION['UserLogin'])){
    print "
<div class='ContainerRating'>
    <form method='post' action='reviewcheck.php'>
    <input type='hidden' value='$ItemID' name='ItemID'>
        <h3>Recensie plaatsen</h3>
        <h5>Hoe bevalt het product?</h5>
        <div class='SterRating'>
            <input type='radio' name='review_rating' id='5_ster' value='5_ster' required>
            <label for='5_ster'></label>
            <input type='radio' name='review_rating' id='4_ster' value='4_ster' required>
            <label for='4_ster'></label>
            <input type='radio' name='review_rating' id='3_ster' value='3_ster' required>
            <label for='3_ster'></label>
            <input type='radio' name='review_rating' id='2_ster' value='2_ster' required>
            <label for='2_ster'></label>
            <input type='radio' name='review_rating' id='1_ster' value='1_ster' required>
            <label for='1_ster'></label>
        </div>
        <hr>
        <h5>Titel</h5>
        <input type='text' name='review_titel' id='review_titel' class='RatingTitel' placeholder='Een korte titel voor de recensie.' required>
        <hr>
        <h5>Recensie</h5>
        <textarea rows='20' cols='5' name='review_beschrijving' id='review_beschrijving' class='RatingRecensie' placeholder='Wat vond u wel of niet goed aan het product?' required></textarea>
        <input type='submit' name='review_submit' id='review_submit' value='Recensie plaatsen'>
    </form>
</div>
"; }

$reviewsArray = ophalenReviews($databaseConnection, $ItemID);

if (!empty($reviewsArray)){
    print ("<div class='ContainerReviews'>");
    print ("<h1>Klantenrecensies</h1>");
}

foreach ($reviewsArray as $reviews){
    $reviewnaam = $reviews['UserName'];
    $reviewrating = AfbeeldingSter($reviews['Rating']);
    $reviewtitle = $reviews['Title'];
    $reviewcomment = $reviews['Comment'];
    print("<div class='IndividueleReview'>");
    print("<div class='ReviewRating'>$reviewrating</div>");
    print("<div class='ReviewTitle'>$reviewtitle</div>");
    print("<div class='ReviewNaam'>$reviewnaam</div>");
    print("<div class='ReviewComment'>$reviewcomment</div>");
    print ("<hr>");
    print("</div>");
}
print ("</div>");

?>