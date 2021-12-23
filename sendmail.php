<?php

include "functions.php";
//verwijzing naar PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

//Probeert mail te verzenden
try {
    //Server instellingen
    $mail->SMTPSecure = 'tls';
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth = true;                                   //Enable SMTP authentication
    $mail->Username = 'lopendeijsbeer@gmail.com';                     //SMTP username
    $mail->Password = 'Jn5gSfVA^At!N./r';                               //SMTP password
    $mail->Port = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => false
        )
    );

    //ontvangers en verzender
    $mail->setFrom('lopendeijsbeer@gmail.com', 'Mailer');
    $mail->addAddress('ruitenbeeksven@gmail.com', 'User');     //Add a recipient

    //inhoud
    $mail->Subject = 'Testmail';
    $mail->Body    = 'Dit is een testmail.<br>https://youtu.be/dQw4w9WgXcQ';
    $mail->AltBody = 'Dit is een testmail.';

    //verzenden
    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>