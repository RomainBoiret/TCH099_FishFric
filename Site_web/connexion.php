<?php
    //CONNEXION BD LOCALE

    // $conn = new PDO("mysql:host=localhost;dbname=local", "admin", "admin");

    // if ($conn == null) {
    //     die("Connexion échouée avec PDO : " . $conn->connect_error);
    // } 

    //CONNEXION BD Google cloud

        
    try{
        $conn = new PDO("mysql:host=35.234.241.60;dbname=projet_integrateur","felix","admin", array(
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
            PDO::MYSQL_ATTR_SSL_KEY =>'/home/felix_caron04/client-key.pem',
            PDO::MYSQL_ATTR_SSL_CERT=>'/home/felix_caron04/client-cert.pem',
            PDO::MYSQL_ATTR_SSL_CA => '/home/felix_caron04/server-ca.pem'
        ));

        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }catch(PDOException $e)
    {
        die("Connexion echouee avec PDO : " . $conn->connect_error);
    }
?>