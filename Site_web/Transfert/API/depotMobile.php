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

        $erreurs = [];

        //Vérifier qu'il y a un montant
        if (isset($donnees['montant']) &&is_numeric($donnees['montant'])) {
            $montant = intval(htmlspecialchars(trim($donnees['montant'])));
        } else {
            $erreurs[] = "Veuillez inscrire un montant";
        }

        //Vérifier qu'il y a l'ID de l'utilisateur
        if (isset($donnees['idUtilisateur'])) {
            $idUtilisateur = trim(implode($donneesJSON['utilisateur']));
        } else {
            $erreurs[] = "ID utilisateur non reçu";
        }

        if(empty($erreurs)) {
            //Chercher le compte chèque de l'utilisateur
            $requete = $conn->prepare("SELECT id FROM CompteBancaire WHERE id = '$idUtilisateur' AND typeCompte = 'Compte chèque';");
            $requete->execute();
            $idCompteCheque = $requete->fetchColumn();

            //Effectuer le dépot
            $requete = $conn->prepare("UPDATE CompteBancaire SET solde = solde + $montant WHERE id = '$idCompteCheque';");
            $requete->execute();

            //Ajouter la transaction 
            $requete = $conn->prepare("INSERT INTO TransactionBancaire (idCompteBancaireRecevant, dateTransaction, montant, 
            typeTransaction) VALUES ('$idCompteCheque', NOW(), '$montant', 'Dépôt mobile');");
            $requete->execute();

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