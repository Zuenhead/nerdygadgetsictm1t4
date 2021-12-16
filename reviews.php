<?php
include __DIR__ . "/header.php";
?>

<html>
<body>
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
<?php
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
    insertReview($databaseConnection, 7, 138, $rating, $titel, $beschrijving);
}

$reviewsArray = ophalenReviews($databaseConnection, 138);
foreach ($reviewsArray as $reviews) {
    print "<br>";
    foreach ($reviews as $review) {
        print "$review<br>";
    }
}
?>
</body>
</html>
