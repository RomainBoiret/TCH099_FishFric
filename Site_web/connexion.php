<?php

    $conn = new PDO("mysql:host=localhost;dbname=local", "admin", "admin");

    if ($conn == null) {
        die("Connexion échouée avec PDO : " . $conn->connect_error);
    } 
?>