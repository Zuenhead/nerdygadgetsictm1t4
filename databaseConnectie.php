<?php
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