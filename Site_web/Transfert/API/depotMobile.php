<?php
    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == 'PUT') {
        //Gérer la connexion à la base de données
        try {
            require("../../connexion.php");
        } catch(Exception $e) {
            die("Connexion échouée!: " .$e->getMessage());
        }

        // Obtenir les données POST et les décoder
        $donnees = json_decode(file_get_contents("php://input"), true);

        if(!empty($donnees)) {
            $erreurs = [];

            //Vérifier qu'il y a un montant
            if (isset($donnees['montant'])) {
                $montant = trim(implode($donnees['montant']));
            } else {
                $erreurs[] = "Veuillez inscrire un montant";
            }
    
            //Vérifier qu'il y a l'ID de l'utilisateur
            if (isset($donnees['idUtilisateur'])) {
                $idUtilisateur = trim(implode($donnees['idUtilisateur']));
            } else {
                $erreurs[] = "ID utilisateur non reçu";
            }
    
            if(empty($erreurs)) {
                //Chercher le compte chèque de l'utilisateur
                $sql = "SELECT id FROM CompteBancaire WHERE compteId=$idUtilisateur AND typeCompte = 'Compte chèque';";
                $requete = $conn->query($sql);
                $idCompteCheque = $requete->fetchColumn();
    
                //Effectuer le dépot
                $requete = $conn->prepare("UPDATE CompteBancaire SET solde = solde + $montant WHERE id = $idCompteCheque;");
                $requete->execute();
    
                //Ajouter la transaction 
                $requete = $conn->prepare("INSERT INTO TransactionBancaire (idCompteBancaireRecevant, dateTransaction, montant, 
                typeTransaction, enAttente) VALUES ($idCompteCheque, NOW(), $montant, 'Dépôt mobile', 0);");
                $requete->execute();


                //Faire une nouvelle notification
                $sql = "SELECT id FROM TransactionBancaire ORDER BY id DESC LIMIT 1;";
                $resultat = $conn->query($sql);
                $idTransaction = $resultat->fetchColumn();

                $contenuNotif = 'Vous avez fait un dépôt mobile de ' . $montant . '$ dans votre compte chèque'; 
                $sql = "INSERT INTO NotificationClient(compteId, titre, contenu, dateRecu, idTransaction, lu)
                VALUES ($idUtilisateur, 'Dépôt mobile', '$contenuNotif', NOW(), $idTransaction, 0);";
                $conn->query($sql);
    
                //Message de succès
                echo json_encode(['reponse'=> "Succès! Le montant a été déposé dans votre compte chèque.", 'code'=>'201']);
            } 
            
            //Sinon, le virement n'a pas marché. On renvoie les messages d'erreur
            else {
                //HTTP CODE 401 Donnee eronnees
                $str = implode(',', $erreurs);
    
    
                echo json_encode(['reponse'=>"$str", 'code'=>'401']);
            }
        }

        else
        {
            echo json_encode(['reponse'=>"Mauvaise requete", 'code'=>'405']);
        }

    } else {
        echo json_encode(["reponse" => "Methode non autorisee"]);
    }