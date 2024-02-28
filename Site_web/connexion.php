<?php
    //CONNEXION BD LOCALE
    $conn = new PDO("mysql:host=localhost;dbname=local", "admin", "admin");

    if ($conn == null) {
        die("Connexion échouée avec PDO : " . $conn->connect_error);
    } 
?>


<?php
//CONNEXION BD MICROSOFT AZURE
// try {
//     $conn = new PDO("sqlsrv:server = tcp:projetintegrateur.database.windows.net,1433; Database = Projet_Integrateur", "CloudSA596930b0", "{projet2024!}");
//     $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// }
// catch (PDOException $e) {
//     print("Error connecting to SQL Server.");
//     die(print_r($e));
// }
?>