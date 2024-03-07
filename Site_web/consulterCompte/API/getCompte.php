<?php 
    //Récupérer le point de terminaison
    if(isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['compteId'])) {
        //Gérer la connexion à la base de données
        try {
            require "../../connexion.php";
        } catch(Exception $e) {
            die("Connexion échouée!: " .$e->getMessage());
        }

        //Chercher le compte en question avec l'ID de la route
        $compteId = $_GET['compteId'];

        //Requête SQL pour chercher les comptes
        $requete = $conn->prepare("SELECT id, solde, typeCompte, interet, ouverture 
        FROM CompteBancaire WHERE id='$compteId';");
        $requete->execute();
        $compte = $requete->fetch(PDO::FETCH_ASSOC);

        //Chercher les transactions de ce compte

        // À FAIRE...


        // Échapper les caractères spéciaux dans le contenu du compte
        $compte['id'] = htmlspecialchars($compte['id'], ENT_QUOTES, 'UTF-8');
        $compte['solde'] = htmlspecialchars($compte['solde'], ENT_QUOTES, 'UTF-8');
        $compte['typeCompte'] = htmlspecialchars($compte['typeCompte'], ENT_QUOTES, 'UTF-8');
        $compte['interet'] = htmlspecialchars($compte['interet'], ENT_QUOTES, 'UTF-8');
        $compte['ouverture'] = htmlspecialchars($compte['ouverture'], ENT_QUOTES, 'UTF-8');

        //Encoder les informations des comptes en json
        echo json_encode(["compte" => $compte]);
    }
?>