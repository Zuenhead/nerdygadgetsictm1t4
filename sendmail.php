<?php
include "functions.php";
//verwijzing naar PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

//Roept PHPMailer class aan
$mail = new PHPMailer(true);
sendMail($mail);
?>