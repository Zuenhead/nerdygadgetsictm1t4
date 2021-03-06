<?php

include "database.php";
include "functions.php";
$databaseConnection = connectToDatabase();


if(isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

        if(empty($email)){
            header("location: login.php?error=Email is verplicht");
            exit();
        } elseif (empty($password)){
            header("location: login.php?error=Wachtwoord is verplicht");
            exit();
        } else {
           $sql = "SELECT * FROM useraccounts WHERE EmailAddress=? LIMIT 1";
           $stmt = mysqli_stmt_init($databaseConnection);
           if(!mysqli_stmt_prepare($stmt, $sql)){
               header("location: login.php?error=SQL error");
               exit();
           } else{
               mysqli_stmt_bind_param($stmt, "s", $email);
               mysqli_stmt_execute($stmt);
               $result = mysqli_stmt_get_result($stmt);
               if($row = mysqli_fetch_assoc($result)){
                   $PasswordCheck = password_verify($password, $row['HashedPassword']);
                   if($PasswordCheck == false){
                       header("location: login.php?error=Verkeerd wachtwoord of E-mail");
                       exit();
                   } elseif ($PasswordCheck == true){
                       session_start();
                       $_SESSION['UserLogin'] = $row['PersonID'];
                       $_SESSION['UserName'] = $row['UserName'];
                       header("location: logout.php");
                   }
               } else{
                   header("location: login.php?error=Verkeerd wachtwoord of E-mail");
                   exit();
               }
           }
        }
} else{
    header("location: index.php");
}

?>

