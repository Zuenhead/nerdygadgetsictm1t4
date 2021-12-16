<?php

function gegevensOphalen($productID){

    $statement = "SELECT S.StockItemID, UnitPrice, ImagePath FROM stockitems S JOIN stockitemimages SI on S.StockItemID = SI.StockItemID WHERE S.StockItemID = $productID";
    $uitvoering = mysqli_query(connectToDatabase(),$statement);
    $result = mysqli_fetch_array($uitvoering,MYSQLI_ASSOC);
    return $result;

}

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