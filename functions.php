<?php

//browse functions


function getVoorraadTekst($actueleVoorraad) {
    if ($actueleVoorraad > 1000) {
        return "Ruime voorraad beschikbaar.";
    } elseif ($actueleVoorraad <= 0) {
        return "Niet op voorraad";
    } else {
        return "Voorraad: $actueleVoorraad";
    }
}

function berekenVerkoopPrijs($adviesPrijs, $btw) {
    return $btw * $adviesPrijs / 100 + $adviesPrijs;
}

//cart functions
    function getCart(){
if(isset($_SESSION['cart'])){               //controleren of winkelmandje (=cart) al bestaat
    $cart = $_SESSION['cart']; //zo ja:  ophalen
} else{
    $cart = array();                        //zo nee: dan een nieuwe (nog lege) array
}
return $cart;                               // resulterend winkelmandje terug naar aanroeper functie
}

function saveCart($cart){
    $_SESSION["cart"] = $cart;                  // werk de "gedeelde" $_SESSION["cart"] bij met de meegestuurde gegevens
}

function addProductToCart($StockItem, $amount){
    $cart = getCart();                          // eerst de huidige cart ophalen
    $cart[$StockItem] = $amount;
    saveCart($cart);                            // werk de "gedeelde" $_SESSION["cart"] bij met de bijgewerkte cart
}

//database initialization

function maakVerbinding(){
    $host ='localhost';
    $user = 'root';
    $pass = '';
    $port = 3306;
    $database='nerdygadgets';
    $connecties = mysqli_connect($host,$user,$pass,$database,$port);
}


function verbindingOpruimen($connectie){
    mysqli_close($connectie);
}


// database functions
function gegevensOphalen($productID, $databaseConnection){

    $Query = "SELECT S.StockItemID, StockItemName, (RecommendedRetailPrice*(1+(TaxRate/100))) AS SellPrice, QuantityOnHand, SearchDetails, ImagePath, S.TaxRate FROM stockitems S JOIN stockitemholdings SH on S.StockItemID = SH.StockItemId JOIN stockitemimages SI on S.StockItemID = SI.StockItemID WHERE S.StockItemID = ?";
    $statement = mysqli_prepare($databaseConnection,$Query);
    mysqli_stmt_bind_param($statement,"i",$productID);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $result = mysqli_fetch_array($result, MYSQLI_ASSOC);
    return $result;

}

function vooraadOphalen($databaseConnection,$productID){
    $Query = "SELECT QuantityOnHand FROM stockitemholdings WHERE StockItemID = ?";
    $statement = mysqli_prepare($databaseConnection,$Query);
    mysqli_stmt_bind_param($statement,"i",$productID);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $result = mysqli_fetch_array($result, MYSQLI_ASSOC);
    return $result["QuantityOnHand"];
}

function createOrder($databaseConnection, $customerID, $salespersonPersonID, $pickedByPersonID, $contactPersonID, $backorderOrderID, $orderDate, $expectedDeliveryDate, $customerPurchaseOrderNumber, $isUndersupplyBackordered, $comments, $deliveryInstructions, $internalComments, $lastEditedBy, $lastEditedWhen) {
    $Query = "INSERT INTO orders(CustomerID, SalespersonPersonID, PickedByPersonID, ContactPersonID, BackorderOrderID, OrderDate, ExpectedDeliveryDate, CustomerPurchaseOrderNumber, IsUndersupplyBackordered, Comments, DeliveryInstructions, InternalComments, LastEditedBy, LastEditedWhen)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "iiiiissiisssis", $customerID, $salespersonPersonID, $pickedByPersonID, $contactPersonID, $backorderOrderID, $orderDate, $expectedDeliveryDate, $customerPurchaseOrderNumber, $isUndersupplyBackordered, $comments, $deliveryInstructions, $internalComments, $lastEditedBy, $lastEditedWhen);
    mysqli_stmt_execute($Statement);
}

function ophalenOrderID($databaseConnection) {
    $Query = "SELECT MAX(OrderID) AS NieuwsteOrderID FROM orders;";
    $Result = mysqli_query($databaseConnection, $Query);
    $Result = mysqli_fetch_array($Result, MYSQLI_ASSOC);
    return $Result;
}

function createOrderLine($databaseConnection, $stockItemID, $description, $packageTypeID, $quantity, $unitPrice, $taxRate, $pickedQuantity, $lastEditedBy, $lastEditedWhen) {
    $orderID = ophalenOrderID($databaseConnection);
    $nieuwsteOrderID = $orderID['NieuwsteOrderID'];
    $Query = "INSERT INTO orderlines (OrderID, StockItemID, Description, PackageTypeID, Quantity, UnitPrice, TaxRate, PickedQuantity, LastEditedBy, LastEditedWhen)
              VALUES ($nieuwsteOrderID, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "isiiddiis", $stockItemID, $description, $packageTypeID, $quantity, $unitPrice, $taxRate, $pickedQuantity, $lastEditedBy, $lastEditedWhen);
    mysqli_stmt_execute($Statement);
}

function ophalenProduct($databaseConnection, $productID) {
    $Query = "SELECT StockItemID, StockItemName, UnitPrice, TaxRate FROM stockitems AS SI WHERE StockItemID = ?";
    $statement = mysqli_prepare($databaseConnection,$Query);
    mysqli_stmt_bind_param($statement,"i",$productID);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $result = mysqli_fetch_array($result, MYSQLI_ASSOC);
    return $result;
}


function gegevensUpdaten ($databaseConnection, $aantal, $productID){
    $Query = "UPDATE stockitemholdings SET QuantityOnHand = QuantityOnHand - ? WHERE StockItemID = ?";
    $statement = mysqli_prepare($databaseConnection,$Query);
    mysqli_stmt_bind_param($statement,"ii",$aantal,$productID);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    return $result;
}


//functies voor recommended in view.php
function suggestieWoorden($databaseConnection, $productID){
    $som = 0;
    $Query = "select stockitemname from stockitems where stockitemid= ?";
    $statement = mysqli_prepare($databaseConnection,$Query);
    mysqli_stmt_bind_param($statement, "i",$productID);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $result = mysqli_fetch_all($result,MYSQLI_ASSOC);

    $woorden = explode(" ",$result[0]['stockitemname']);
    $som += strlen($woorden[0]);
    $som += strlen($woorden[1]);
    $result= substr($result[0]["stockitemname"],0,$som+1);
    return $result;
}

function alternatiefOphalen($databaseConnection, $productID){ //poging tot relevante alternatieven
    $naam = suggestieWoorden($databaseConnection,$productID);
    $Query = "select s.stockitemid, stockitemname, (RecommendedRetailPrice*(1+(TaxRate/100))) AS SellPrice, imagepath from stockitems s join stockitemimages si on s.stockitemid=si.stockitemid where s.stockitemid <> ? and s.stockitemname like  CONCAT('%',?,'%') order by rand(),s.stockitemid limit 3";
    $statement = mysqli_prepare($databaseConnection,$Query);
    mysqli_stmt_bind_param($statement, "is",$productID,$naam);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $result = mysqli_fetch_all($result,MYSQLI_ASSOC);
    return $result;
}

//functies kortingscodes

function controleerKortingKlant($databaseConnection,$customerID,$kortingscode){
    $Query = "select CustomerID from discountCodes where DiscountCode= ? and CustomerID = ? and Expired = 0";
    $statement = mysqli_prepare($databaseConnection,$Query);
    mysqli_stmt_bind_param($statement, "si",$kortingscode, $customerID);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $result = mysqli_fetch_all($result,MYSQLI_ASSOC);

    if(empty($result)){
        $Query = "select CustomerID from discountCodes where DiscountCode= ? and Expired = 0 and CustomerID IS NULL ";
        $statement = mysqli_prepare($databaseConnection,$Query);
        mysqli_stmt_bind_param($statement, "s",$kortingscode);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $result = mysqli_fetch_all($result,MYSQLI_ASSOC);

        if(empty($result)){
            return FALSE;
        }else{
            return TRUE;
        }
    }else{
        return TRUE;
    }
}

function ophalenKorting($databaseConnection,$customerID,$kortingscode){
    if(controleerKortingKlant($databaseConnection,$customerID,$kortingscode)){
        $Query = "select DiscountAmount,DiscountPercentage,DiscountShipping from discountCodes where DiscountCode= ?";
        $statement = mysqli_prepare($databaseConnection,$Query);
        mysqli_stmt_bind_param($statement, "s",$kortingscode);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $result = mysqli_fetch_all($result,MYSQLI_ASSOC);
        $result = array(
            "Amount" => $result[0]["DiscountAmount"],
            "Percentage" => 1-$result[0]["DiscountPercentage"]/100,
            "Verzend" => $result[0]["DiscountShipping"]
        );
        return $result;
    }else{
        return null;
    }
}

function berekenKorting($amount,$percentage,$subtotaal){
    $korting = $subtotaal - ($amount + $percentage*$subtotaal);
    return $korting;
}

function berekenVerzend($subtotaal,$verzendkorting){
    $verzendkorting = 1-$verzendkorting/100;
    if($verzendkorting<0){
        $verzendkorting = 0;
    }
    if($subtotaal > 30){
        $verzend = 0;
    }elseif($verzendkorting >= 0){
        $verzend = $subtotaal*0.25*$verzendkorting;
    }else{
        $verzend = $subtotaal*0.25;
    }
    return$verzend;
}

//getcart functies, maar gemaakt om kortingen op te slaan
function getKorting(){
    if(isset($_SESSION['kortingen'])){               //controleren of winkelmandje (=cart) al bestaat
        $kortingen = $_SESSION['kortingen']; //zo ja:  ophalen
    } else{
        $kortingen = array();                        //zo nee: dan een nieuwe (nog lege) array
    }
    return $kortingen;                               // resulterend winkelmandje terug naar aanroeper functie
}

function kortingenOpslaan($kortingen){
    $_SESSION["kortingen"] = $kortingen;                  // werk de "gedeelde" $_SESSION["kortingen"] bij met de meegestuurde gegevens
}

function kortingenToevoegen($kortingscode, $amount, $percentage,$verzend)
{
    $kortingen = getKorting();                          // eerst de huidige cart ophalen
    if (!array_key_exists($kortingscode,$kortingen)) {
        $kortingen[$kortingscode] = array(
            "Amount" => $amount,
            "Percentage" => $percentage,
            "Verzend" => $verzend);
        kortingenOpslaan($kortingen);              // werk de "gedeelde" $_SESSION["kortingen"] bij met de bijgewerkte cart
    }
}
//functies hoeveelheid kortingscodes

function aantalKorting(){
    if(isset($_SESSION["kortingAantal"])){
        $kortingAantal = $_SESSION["kortingAantal"];
    }else{
        $_SESSION["kortingAantal"] = 0;
        $kortingAantal = 0;
    }
    return $kortingAantal;
}

function aantalKortingBijwerken(){
        if(isset($_SESSION["kortingAantal"])){
            $_SESSION["kortingAantal"] = $_POST["kortingAantal"]+ 1;
        }else{
            $_SESSION["kortingAantal"] = 0;
        }
        unset($_POST["kortingAantal"]);
}

function aantalKortingVerwijderen(){
    if($_SESSION["kortingAantal"]>0){
        $_SESSION["kortingAantal"] -= 1;
    }else{
        $_SESSION["kortingAantal"] = 0;
    }
}

//functies reviews
function insertReview ($databaseConnection, $personID, $stockItemID, $rating, $title, $comment) {
    $Query = "INSERT INTO reviews(PersonID, StockItemID,  Rating, Title, Comment)
                  VALUES (?, ?, ?, ?, ?)";
    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "iiiss", $personID, $stockItemID, $rating, $title, $comment);
    mysqli_stmt_execute($Statement);
}

function ophalenReviews($databaseConnection, $productID) {
    $Query = "SELECT UserName, Rating, Title, Comment
                  FROM Reviews R
                  LEFT JOIN Useraccounts U ON R.PersonID = U.PersonID
                  WHERE StockItemID = $productID
                  ORDER BY ReviewID DESC
                  LIMIT 10";
    $Result = mysqli_query($databaseConnection, $Query);
    $Result = mysqli_fetch_all($Result, MYSQLI_ASSOC);
    return $Result;
}

//functies accounts


function ophalenPersonID($databaseConnection) {
    $Query = "SELECT MAX(PersonID) AS NieuwstePersonID FROM useraccounts;";
    $Result = mysqli_query($databaseConnection, $Query);
    $Result = mysqli_fetch_array($Result, MYSQLI_ASSOC);
    return $Result;
}

function InsertAccount ($databaseConnection, $UserName, $EmailAddress, $HashedPassword, $PhoneNumber, $DeliveryCityID, $DeliveryPostalCode, $DeliveryAddress ){
    $PersonID = ophalenPersonID($databaseConnection);
    $nieuwstePersonID = $PersonID['NieuwstePersonID'] + 1;
    $sql = "INSERT INTO useraccounts(PersonID, UserName, EmailAddress, HashedPassword, PhoneNumber, DeliveryCityID, DeliveryPostalCode, DeliveryAddress)
            VALUES ($nieuwstePersonID, ?, ?, ?, ?, ?, ?, ?);";
    $Statement = mysqli_prepare($databaseConnection, $sql);
    mysqli_stmt_bind_param($Statement,"ssssiss", $UserName, $EmailAddress, $HashedPassword, $PhoneNumber, $DeliveryCityID, $DeliveryPostalCode, $DeliveryAddress);
    mysqli_stmt_execute($Statement);
}

function CheckCityName($databaseConnection, $CityName){
    $sql = "SELECT CityID FROM cities WHERE CityName LIKE ? LIMIT 1;";
    $Statement = mysqli_prepare($databaseConnection, $sql);
    mysqli_stmt_bind_param($Statement, "s", $CityName);
    mysqli_stmt_execute($Statement);
    mysqli_stmt_store_result($Statement);
    if($Statement->num_rows == 1) {
        mysqli_stmt_bind_result($Statement, $CityID);
        mysqli_stmt_fetch($Statement);
        mysqli_stmt_close($Statement);
        return $CityID;
    } else {
        return false;
    }
}

function ophalenNewOrderID($databaseConnection)
{
    $Query = "SELECT MAX(OrderID) AS NieuwsteOrderID FROM neworders;";
    $Result = mysqli_query($databaseConnection, $Query);
    $Result = mysqli_fetch_array($Result, MYSQLI_ASSOC);
    return $Result;
}

function CreateNewOrder($databaseConnection, $SubTotaal, $Verzendkosten, $Korting, $Totaal, $DeliveryAddress, $DeliveryCityID, $DeliveryPostalCode, $PhoneNumber, $UserName){
    $NewOrderID = ophalenNewOrderID($databaseConnection);
    $nieuwsteNewOrderID = $NewOrderID['NieuwsteOrderID'] + 1;
    if (isset($_SESSION['UserLogin'])){
        $NewPersonID = $_SESSION['UserLogin'];
    } else {
        $NewPersonID = 0;
    }
    $sql = "INSERT INTO neworders (OrderID, PersonID, OrderDate, SubTotaal, Verzendkosten, Korting, Totaal, DeliveryAddress, DeliveryCityID, DeliveryPostalCode, PhoneNumber, UserName)
    VALUES ($nieuwsteNewOrderID, $NewPersonID , current_date(), ?, ?, ?, ?, ?, ?, ?, ?, ? )";
    $Statement = mysqli_prepare($databaseConnection, $sql);
    mysqli_stmt_bind_param($Statement,"ddddsisss",$SubTotaal, $Verzendkosten, $Korting, $Totaal, $DeliveryAddress, $DeliveryCityID, $DeliveryPostalCode, $PhoneNumber, $UserName);
    mysqli_stmt_execute($Statement);
}

function OphalenNewOrderLineID($databaseConnection){
    $Query = "SELECT MAX(OrderLineID) AS NieuwsteOrderLineID FROM neworderlines;";
    $Result = mysqli_query($databaseConnection, $Query);
    $Result = mysqli_fetch_array($Result, MYSQLI_ASSOC);
    return $Result;
}

function CreateNewOrderLine($databaseConnection, $StockItemID, $Description, $Quantity, $UnitPrice, $TaxRate){
    $NewOrderLineID = OphalenNewOrderLineID($databaseConnection);
    $NieuwsteNewOrderLineID = $NewOrderLineID['NieuwsteOrderLineID'] + 1;
    $NewOrderID = ophalenNewOrderID($databaseConnection);
    $StringNewOrderID = $NewOrderID['NieuwsteOrderID'];
    $sql = "INSERT INTO neworderlines (OrderlineID, OrderID, StockItemID, Description, Quantity, UnitPrice, TaxRate)
    VALUES ($NieuwsteNewOrderLineID, $StringNewOrderID, ?, ?, ?, ?, ?);";
    $Statement = mysqli_prepare($databaseConnection, $sql);
    mysqli_stmt_bind_param($Statement, "isidd", $StockItemID, $Description, $Quantity, $UnitPrice, $TaxRate);
    mysqli_stmt_execute($Statement);
}

function AdresKlantOphalen($databaseConnection){
    $sql = "SELECT us.UserName, us.DeliveryAddress, ci.CityName, us.DeliveryPostalCode, us.PhoneNumber, us.DeliveryCityID FROM useraccounts us JOIN cities ci ON us.DeliveryCityID = ci.CityID WHERE us.PersonID = ? LIMIT 1";
    $Statement = mysqli_prepare($databaseConnection, $sql);
    mysqli_stmt_bind_param($Statement, "i", $_SESSION['UserLogin']);
    mysqli_stmt_execute($Statement);
    mysqli_stmt_store_result($Statement);
    if ($Statement->num_rows == 1) {
        mysqli_stmt_bind_result($Statement, $naam, $adres, $plaats, $postcode, $telefoonnummer, $cityid);
        mysqli_stmt_fetch($Statement);
        mysqli_stmt_close($Statement);
        return array($naam, $adres, $plaats, $postcode, $telefoonnummer, $cityid);
    } else{
        return false;
    }
}

function VeranderAdres($databaseConnection, $Adres, $CityID, $Postcode, $Telnummer, $UserName , $PersonID ){
    $sql ="UPDATE useraccounts SET DeliveryAddress = ?, DeliveryCityID = ?, DeliveryPostalCode = ?, PhoneNumber = ?, UserName =? WHERE PersonID = ?;";
    $Statement = mysqli_prepare($databaseConnection, $sql);
    mysqli_stmt_bind_param($Statement, "sisssi", $Adres, $CityID, $Postcode, $Telnummer, $UserName, $PersonID );
    mysqli_stmt_execute($Statement);

}

function tempratuurophalen ($databaseConnection){
    $Query = "SELECT temperature FROM coldroomtemperatures ORDER BY ColdRoomTemperatureID DESC;";
    $Result = mysqli_query($databaseConnection, $Query);
    $Result = mysqli_fetch_array($Result, MYSQLI_ASSOC);
    return $Result;
}

?>

