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

<?php
// // Connection parameters
// $host = 'tch054ora12c.logti.etsmtl.ca'; // Hostname
// $port = '1521'; // Port (usually 1521)
// $dbname = 'TCH054'; // Service name or SID
// $username = 'equipe104'; // Username
// $password = 'x1sB3Xmc'; // Password

// // Connection string
// $dsn = "oci:dbname=(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=$host)(PORT=$port))(CONNECT_DATA=(SID=$dbname)))";

// try {
//     // Connect to Oracle database
//     $pdo = new PDO($dsn, $username, $password);
    
//     // Set error mode to exception
//     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
//     // Your code here...
    
//     // Example: Query database
//     $stmt = $pdo->query('SELECT * FROM Client');
//     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
//         print_r($row);
//     }
    
// } catch (PDOException $e) {
//     // Handle connection errors
//     echo "Connection failed: " . $e->getMessage();
// }
?>
