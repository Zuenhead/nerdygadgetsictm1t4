<?php
include __DIR__ . "/header.php";

?>
<!DOCTYPE html>
<html lang="nl">
    <head>
        <meta charset="UTF-8">
        <title>Inloggen</title>
    </head>
    <body>
    <?php if(isset($_SESSION['UserLogin'])){
        header("location: logout.php");
    }
    ?>
        <form action="authentication.php" method="POST">
            <div class="LoginContainer">
                    <h3>Inloggen</h3>
                    <?php if(isset($_GET['error'])) { ?>
                        <div class="AlertVeldInloggen">
                    <?php print(htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'));?>
                        </div>
                    <?php } ?>
                        <div class="LoginInput">
                            <label for="LoginEmail">E-mailadres</label>
                            <input type="email" maxlength="100" id="LoginEmail" name="email" placeholder="E-mailadres" required>
                            <label for="LoginPassword">Wachtwoord</label>
                            <div class="WachtwoordContainer">
                            <input type="password" maxlength="100" id="LoginPassword" name="password" placeholder="Wachtwoord" required>
                            <input type="checkbox" id="ToggleWachtwoord" onclick="myFunction()">
                            <label for="ToggleWachtwoord"></label>
                            <script>
                                function myFunction() {
                                    var x = document.getElementById("LoginPassword");
                                    if (x.type === "password") {
                                        x.type = "text";
                                    } else {
                                        x.type = "password";
                                    }
                                }
                            </script>
                            </div>
                            <button type="submit" id="LoginFormSubmit"><span>Inloggen</span></button>
                        </div>
                <div class="GeenAccount">
                    <label for="AcntAanmakenBtn">Nog geen account?</label>
                    <a href="index.php" class="AcntAanmakenBtn" id="AcntAanmakenBtn">Accout aanmaken</a>
                </div>
            </div>
        </form>
    </body>
</html>

