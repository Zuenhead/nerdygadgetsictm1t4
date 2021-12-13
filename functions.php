<?php
//browse functions


function getVoorraadTekst($actueleVoorraad) {
    if ($actueleVoorraad > 1000) {
        return "Ruime voorraad beschikbaar.";
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
/*
function alternatiefOphalen($databaseConnection, $categoryID){
    $Query = "SELECT TOP 4 StockItemID from stockitems where categoryid=?";
    $statement = mysqli_prepare($databaseConnection,)
}
*/


?>

