<?php
    //CONNEXION BD LOCALE

    $conn = new PDO("mysql:host=localhost;dbname=local", "admin", "admin");

    if ($conn == null) {
        die("Connexion échouée avec PDO : " . $conn->connect_error);
    } 

    //CONNEXION BD Google cloud

    // $conn = new PDO("mysql:host=35.234.241.60;dbname=projet_integrateur", "felix", "admin");

    // if ($conn == null) {
    //     die("Connexion échouée avec PDO : " . $conn->connect_error);
    // } 
?>