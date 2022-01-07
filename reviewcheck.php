<?php
session_start();
include "database.php";
include "functions.php";
$databaseConnection = connectToDatabase();

if (isset($_POST['review_submit'])) {
    $titel = $_POST['review_titel'];
    $beschrijving = $_POST['review_beschrijving'];
    $ItemID = $_POST['ItemID'];
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
        header("location: view.php?id=$ItemID error=Ongeleldige recensie");
        exit();
    }
    insertReview($databaseConnection, $_SESSION['UserLogin'], $ItemID, $rating, $titel, $beschrijving);
    header("location: view.php?id=$ItemID succes=Recensie geplaatst");
    exit();
}
