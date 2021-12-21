<?php
include __DIR__ . "/header.php";

?>

<!DOCTYPE html>
<html lang="nl">
    <head>
        <meta charset="UTF-8">
        <title>Uitloggen</title>
    </head>
    <body>
    <?php
    if(!isset($_SESSION['UserLogin'])){
        header("location: login.php");
        exit();
    }

    if (isset($_POST['logout'])){
        session_unset();
        session_destroy();
        header('Location: login.php');
    }
    ?>
        <div class="LogoutContainer">
        <h2>U bent nu ingelogd als: <span><?php if(isset($_SESSION['UserName'])){print $_SESSION['UserName'];}  ?> </span></h2>
            <form action="#" method="POST">
                <div class="UitlogKnop">
                    <input type="hidden" name="logout">
                    <label for="UitlogKnop">Klik hier als u wilt uitloggen</label>
                    <input type="submit" value="Uitloggen" id="UitlogKnop">
                </div>
            </form>
        </div>

    </body>
</html>
