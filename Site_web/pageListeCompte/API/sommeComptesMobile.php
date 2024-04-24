<?php

    if(isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST"){

        //Update Solde total du compte apres une transaction
        if (preg_match('/\/pageListeCompte\/API\/sommeComptesMobile\.php\/updateSolde$/', $_SERVER['REQUEST_URI'], $matches))
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
                $soldeTotal = trim(implode($donneesJSON['solde']));
                $idUser = trim(implode($donneesJSON['id']));
                if(isset($soldeTotal))
                {
                    $requete = $conn->prepare("UPDATE SommeTotale SET solde = $soldeTotal WHERE compteId = $idUser AND dateSolde = CURRENT_DATE");
                    $requete->execute();
                }
            }
            else
            {
                echo json_encode(["erreur"=>"Erreur lors de la requete", "code"=>"400"]);
            }
        }
        else
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

                $idUser = trim(implode($donneesJSON['idUser']));

                $requete = $conn->prepare("SELECT * FROM SommeTotale WHERE compteId = $idUser AND dateSolde > CURRENT_DATE-7 ORDER BY dateSolde");
                $requete->execute();
                $sommes = $requete->fetchAll(PDO::FETCH_ASSOC);

                // Échapper les caractères spéciaux dans le contenu des comptes
                foreach ($sommes as $somme) {
                    $somme['compteId'] = htmlspecialchars($somme['compteId'], ENT_QUOTES, 'UTF-8');
                    $somme['solde'] = htmlspecialchars($somme['solde'], ENT_QUOTES, 'UTF-8');
                    $somme['dateSolde'] = htmlspecialchars($somme['dateSolde'], ENT_QUOTES, 'UTF-8');
                }

                
                //Encoder les informations des comptes en json
                echo json_encode(["comptes" => $sommes, "code"=>"200"]);
            }
            else
            {
                echo json_encode(['erreur'=>"Erreur lors de l'envoi de donnees", 'code'=>"400"]);
            }
        }


    }

    //Pour WEB
    else if(isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "GET"){

        //Gérer la connexion à la base de données
        try {
            require "../../connexion.php";
        } catch(Exception $e) {
            die("Connexion échouée!: " .$e->getMessage());
        }

        session_start();
        $idUtilisateur = $_SESSION['utilisateur'];

        $requete = $conn->prepare("SELECT dateSolde, solde FROM SommeTotale WHERE compteId = $idUtilisateur AND dateSolde > CURRENT_DATE-7 ORDER BY dateSolde");
        $requete->execute();
        $sommes = $requete->fetchAll(PDO::FETCH_ASSOC);

        // Échapper les caractères spéciaux dans le contenu des comptes
        foreach ($sommes as $somme) {
            $somme['solde'] = htmlspecialchars($somme['solde'], ENT_QUOTES, 'UTF-8');
            $somme['dateSolde'] = htmlspecialchars($somme['dateSolde'], ENT_QUOTES, 'UTF-8');
        }

        
        //Encoder les informations des comptes en json
        echo json_encode(["sommes" => $sommes]);

    }

?>
