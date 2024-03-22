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

        $donneesJSON = json_decode(file_get_contents("php://input"), true);

        if(empty($donneesJSON))
        {
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
        }
        else
        {
            $id = trim(implode($donneesJSON['utilisateur']));

            //Requete SQL pour chercher les comptes
            $requete = $conn->prepare("SELECT id, solde, typeCompte 
            FROM CompteBancaire WHERE compteId = $idUtilisateur;");
            $requete->execute();
            $comptes = $requete->fetchAll(PDO::FETCH_ASSOC);

            //Echapper les characteres speciaux
            foreach ($comptes as $compte) {
                $compte['id'] = htmlspecialchars($compte['id'], ENT_QUOTES, 'UTF-8');
                $compte['solde'] = htmlspecialchars($compte['solde'], ENT_QUOTES, 'UTF-8');
                $compte['typeCompte'] = htmlspecialchars($compte['typeCompte'], ENT_QUOTES, 'UTF-8');
            }
        }

    }
    //Requete get comptes pour mobile
    else if(isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST")
    {
        //Gérer la connexion à la base de données
        try {
            require "../../connexion.php";
        } catch(Exception $e) {
            die("Connexion échouée!: " .$e->getMessage());
        }

        $donneesJSON = json_decode(file_get_contents("php://input"), true);

        if(!empty($donneesJSON))
        {
            //Chercher les comptes de l'utilisateur avec la variable de session
            $idUtilisateur = $trim(implode($donneesJSON['utilisateur']));

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

        }
        else
        {
            echo json_encode(['erreur'=>"Mauvaise requete", 'code'=>405]);
        }
    }
?>