<?php

    if(isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST"){

        //Gérer la connexion à la base de données
        try {
            require "../../connexion.php";
        } catch(Exception $e) {
            die("Connexion échouée!: " .$e->getMessage());
        }

        $donneesJSON = json_decode(file_get_contents("php://input"), true);

        if(!empty($donneesJSON))
        {

            $idUser = $donneesJSON['idUser'];

            $requete = $conn->prepare("SELECT * FROM SommeTotale WHERE compteId = $idUser");
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
            echo json_encode(['erreur'=>"Erreur de route", 'code'=>"502"]);
        }

    }

?>
