<?php 
    session_start();

    //Récupérer le point de terminaison
    if(isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "GET") {
        //Gérer la connexion à la base de données
        try {
            require "../../connexion.php";
        } catch(Exception $e) {
            die("Connexion échouée!: " .$e->getMessage());
        }

        //FAIRE ROUTE GET AFFICHER INFOS DES COMPTES -----------------------------

        //pregmatch...

        //Chercher les comptes de l'utilisateur avec la variable de session
        $idUtilisateur = $_SESSION['utilisateur'];

        //Requête SQL pour chercher les comptes
        $requete = $conn->prepare("SELECT id, solde, typeCompte 
        FROM CompteBancaire WHERE compteId = $idUtilisateur;");
        $requete->execute();
        $comptes = $requete->fetchAll(PDO::FETCH_ASSOC);

        // Échapper les caractères spéciaux dans le contenu des comptes
        foreach ($comptes as $compte) {
            $compte['id'] = htmlspecialchars($compte['id'], ENT_QUOTES, 'UTF-8');
            $compte['solde'] = htmlspecialchars($compte['solde'], ENT_QUOTES, 'UTF-8');
            $compte['typeCompte'] = htmlspecialchars($compte['typeCompte'], ENT_QUOTES, 'UTF-8');
        }

        //Encoder les informations des comptes en json
        echo json_encode(["comptes" => $comptes]);


        //FAIRE ROUTE GET AFFICHER COMPTES POUR TRANSFERT ENTRE COMPTES -----------------------------
        

    }
?>