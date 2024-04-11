<?php
//Chercher l'ID de l'utilisateur conencté
session_start();
$idUtilisateur = $_SESSION["utilisateur"];

if(isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "PUT"){

    try{
        require "../../connexion.php";
    }catch(Exception $e)
    {
        die("Connexion échouée!: " .$e->getMessage());
    }

    include "../../encryption/encryption.php";

    //Chercher les données
    $donneesJSON = json_decode(file_get_contents("php://input"), true);

    //Gestionnaire d'erreurs
    $erreurs = array();

    //-----------------------MODIFIER COURRIEL-----------------------
    if (preg_match('/\/pageListeCompte\/API\/preferences\.php\/courriel$/', $_SERVER['REQUEST_URI'], $matches)) {
        if (isset($donneesJSON['nouveauCourriel']) && !empty($donneesJSON['nouveauCourriel'])) {
            $nouveauCourriel = $donneesJSON['nouveauCourriel'];

            //Verifie si courriel suit le bon format
            if(!filter_var($nouveauCourriel, FILTER_VALIDATE_EMAIL))
                $erreurs[] = "Le courriel saisi n'est pas valide!";
            else {
                $nouveauCourriel = htmlspecialchars($nouveauCourriel);

                //Verifier si courriel existe deja 
                $requete = $conn->prepare("SELECT * FROM Compte WHERE courriel = '$nouveauCourriel'");
                $requete->execute();

                if($requete->rowCount() != 0)
                    $erreurs[] = "Le courriel est déjà utilisé";
            }

        } else {
            $erreurs[] = "Veuillez saisir une adresse courriel.";
        }

        //S'il n'y a pas d'erreurs, on peut effectuer le changement de courriel
        if(empty($erreurs)) {
            //Altérer le courriel de l'utilisateur
            $requete = $conn->prepare("UPDATE Compte SET courriel = '$nouveauCourriel' WHERE id = $idUtilisateur;");
            $requete->execute();

            echo json_encode(['msgSucces' => "Le courriel a bien été modifié."]);
        } 
        
        //Sinon, on renvoie les erreurs
        else {
            echo json_encode(['erreurs' => $erreurs]);
        }
    }

    //-----------------------MODIFIER MDP-----------------------
    else if (preg_match('/\/pageListeCompte\/API\/preferences\.php\/mdp$/', $_SERVER['REQUEST_URI'], $matches)) {
        if (isset($donneesJSON['nouveauMdp']) && !empty($donneesJSON['nouveauMdp'])) {
            $nouveauMdp = htmlspecialchars($donneesJSON['nouveauMdp']);

            //Vérifier que le mot de passe n'est pas le même
            $requete = "SELECT motDePasse FROM Compte WHERE id = $idUtilisateur;";
            $resultat = $conn->query($requete);
            $mdp = $resultat->fetchColumn();

            if(AES256CBC_decrypter($mdp, CLE_ENCRYPTION) == $nouveauMdp) {
                $erreurs[] = "Le mot de passe doit être différent de votre mot de passe actuel.";
            }

        } else {
            $erreurs[] = "Veuillez saisir un nouveau mot de passe.";
        }

        //S'il n'y a pas d'erreurs, on peut effectuer le changement de courriel
        if(empty($erreurs)) {
            //Altérer le courriel de l'utilisateur
            $mdp_encrypte = AES256CBC_encrypter($nouveauMdp, CLE_ENCRYPTION);


            $requete = $conn->prepare("UPDATE Compte SET motDePasse = '$mdp_encrypte' WHERE id = $idUtilisateur;");
            $requete->execute();

            echo json_encode(['msgSucces' => "Le mot de passe a bien été modifié."]);
        } 
        
        //Sinon, on renvoie les erreurs
        else {
            echo json_encode(['erreurs' => $erreurs]);
        }
    }
    

    //-----------------------SUPPRIMER COMPTE BANCAIRE-----------------------
    else if (preg_match('/\/pageListeCompte\/API\/preferences\.php\/compteBancaire$/', $_SERVER['REQUEST_URI'], $matches)) {
        //Vérifier qu'un compte a été sélectionné
        if (isset($donneesJSON['idCompteBancaire']) && !empty($donneesJSON['idCompteBancaire'])) {
            $idCompteBancaire = $donneesJSON['idCompteBancaire'];

            //Vérifier que le compte a un solde de 0
            $sql = "SELECT solde FROM CompteBancaire WHERE id=$idCompteBancaire;";
            $resultat = $conn->query($sql);
            $solde = $resultat->fetchColumn();

            if($solde != 0) {
                $erreurs[] = "Le solde du compte doit être à 0 pour pouvoir l'effacer.";
            }

            //Si le compte est un compte chèque, on ne peut pas le fermer
            $sql = "SELECT typeCompte FROM CompteBancaire WHERE id=$idCompteBancaire;";
            $resultat = $conn->query($sql);
            $typeCompte = $resultat->fetchColumn();

            if($typeCompte == "Compte chèque") {
                $erreurs[] = "Vous ne pouvez pas supprimer votre compte chèque, sauf lorsque vous fermez votre compte Fish&Fric.";
            }

        } else {
            $erreurs[] = "Veuillez sélectionner un compte.";
        }
        
        //S'il n'y a pas d'erreurs, on supprime le compte
        if (empty($erreurs)) {
            //D'abord supprimer toutes les notifications 
            $sql = "SELECT id FROM TransactionBancaire WHERE idCompteBancaireProvenant=$idCompteBancaire OR idCompteBancaireRecevant=$idCompteBancaire;";
            $resultat = $conn->query($sql);
            $transactionsArray = $resultat->fetchAll(PDO::FETCH_ASSOC);

            // Création d'un tableau contenant uniquement les identifiants de transaction
            $transactionIds = array_map(function($transaction) {
                return $transaction['id'];
            }, $transactionsArray);

            // Si des transactions sont trouvées, supprime les notifications correspondantes
            if (!empty($transactionIds)) {
                $idsPrepared = implode(',', array_fill(0, count($transactionIds), '?'));
                $sql = "DELETE FROM NotificationClient WHERE idTransaction IN ($idsPrepared)";
                $stmt = $conn->prepare($sql);
                $stmt->execute($transactionIds);

                //Effacer les transactions
                $sql = "DELETE FROM TransactionBancaire WHERE idCompteBancaireProvenant = $idCompteBancaire 
                OR idCompteBancaireRecevant = $idCompteBancaire;";
                $conn->query($sql);
            }

            //Effacer le compte
            $sql = "DELETE FROM CompteBancaire WHERE id=$idCompteBancaire";
            $conn->query($sql);




            echo json_encode(['msgSucces' => "Le compte a bien été supprimé."]);
        } else {
            echo json_encode(['erreurs' => $erreurs]);
        }
    }

    else { 
        echo json_encode(['erreur' => 'Mauvaise route.',
                          'code' => 404]);
    }
}


 //-----------------------SUPPRIMER COMPTE-----------------------
else if(isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "DELETE"){

    try{
        require "../../connexion.php";
    }catch(Exception $e)
    {
        die("Connexion échouée!: " .$e->getMessage());
    }

    //Vérifier que les comptes ont un solde de 0
    $sql = "SELECT solde FROM CompteBancaire WHERE compteId=$idUtilisateur;";
    $resultat = $conn->query($sql);
    $soldes = $resultat->fetchAll(PDO::FETCH_ASSOC);

    foreach($soldes as $solde) {
        if($solde['solde'] != 0.00) {
            $erreurs[] = "Le solde de tous vos comptes doivent être à 0.";
            break;
        }
    }



    if(empty($erreurs)) {
        //S'il n'y a pas d'erreurs, faire la requête qui supprime l'utilisateur.
        $sql = "DELETE FROM Compte WHERE id=$idUtilisateur";
        $conn->query($sql);

        //Mettre un message dans une variable de session et renvoyer l'utilisateur à la page de connexion
        $_SESSION["compteSupprime"] = "Votre compte Fish&Fric a bien été supprimé.";
        // header("Location: /TCH099_FishFric/Site_web/Connexion/page_connexion.php");
        // exit(); 

        echo json_encode(['msgSucces' => "Succès"]);
    } 
    
    else {
        echo json_encode(['erreurs' => $erreurs]);
    }
}


else {
    // Code HTTP 405 - Method Not Allowed
    echo json_encode(['erreur' => 'Méthode non autorisée.',
                    'code' => 405]);
}


?>