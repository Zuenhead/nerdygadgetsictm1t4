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




        <table id="alternatieven">
            <?php  $alternatieven = array(alternatiefOphalen($databaseConnection,$ItemID));
            print("<tr>");
            for($i=0; $i<3; $i++) {
                if (isset($alternatieven[0][$i]['stockitemid'])){
                    $afbeelding = $alternatieven[0][$i]["imagepath"];
                    print("<th> <a href='view.php?id=" . $alternatieven[0][$i]['stockitemid'] ."'> <img src='Public/StockItemIMG/" . $afbeelding . "' width='50%' alt=". $alternatieven[0][$i]['stockitemid'] . "><br>" .
                        $alternatieven[0][$i]['stockitemname'] . "<br> € " . number_format($alternatieven[0][$i]['SellPrice'], 2,".",",") . "</th>");


                }elseif($i == 0){
                    print("Sorry, er zijn geen gerelateerde producten");
                }
            }

            print("</tr> </table>");
            ?>



        </table>
        <?php
    } else {
        ?><h2 id="ProductNotFound">Het opgevraagde product is niet gevonden.</h2><?php
    }
    ?>




</div>

<?php
if (isset($_SESSION['UserLogin'])){
    print '

<form method="post">
    <h4>Rating:</h4>
    <input type="radio" name="review_rating" id="1_ster" value="1_ster" required>
    <label for="1_ster">★</label>
    <input type="radio" name="review_rating" id="2_ster" value="2_ster" required>
    <label for="2_ster">★★</label>
    <input type="radio" name="review_rating" id="3_ster" value="3_ster" required>
    <label for="3_ster">★★★</label>
    <input type="radio" name="review_rating" id="4_ster" value="4_ster" required>
    <label for="4_ster">★★★★</label>
    <input type="radio" name="review_rating" id="5_ster" value="5_ster" required>
    <label for="5_ster">★★★★★</label>
    <h4>Titel:</h4>
    <input type="text" name="review_titel" id="review_titel" class="form-submit">
    <h4>Beschrijving:</h4>
    <input type="text" name="review_beschrijving" id="review_beschrijving" class="form-submit">
    <input type="submit" name="review_submit" id="review_submit" class="form-submit" value="Verzenden">
</form>
'; }

if (isset($_POST['review_submit'])) {
    $titel = $_POST['review_titel'];
    $beschrijving = $_POST['review_beschrijving'];
    if ($_POST['review_rating'] == "1_ster") {
        $rating = 1;
    } elseif ($_POST['review_rating'] == "2_ster") {
        $rating = 2;
    } elseif ($_POST['review_rating'] == "3_ster") {
        $rating = 3;
    } elseif ($_POST['review_rating'] == "4_ster") {
        $rating = 4;
    } elseif ($_POST['review_rating'] == "5_ster") {
        $rating = 5;
    } else {
        echo "Ongeldige sterbeoordeling.";
        $rating = NULL;
    }
        insertReview($databaseConnection, $_SESSION['UserLogin'], $ItemID, $rating, $titel, $beschrijving);
}

$reviewsArray = ophalenReviews($databaseConnection, $ItemID);
foreach ($reviewsArray as $reviews) {
    print "<br>";
    foreach ($reviews as $review) {
        print "$review<br>";
    }
}
?>