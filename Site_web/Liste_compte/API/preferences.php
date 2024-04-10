<?php
if(isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "PUT"){

    try{
        require "../../connexion.php";
    }catch(Exception $e)
    {
        die("Connexion échouée!: " .$e->getMessage());
    }

    //Chercher l'ID de l'utilisateur conencté
    session_start();
    $idUtilisateur = $_SESSION["utilisateur"];

    //Chercher les données
    $donneesJSON = json_decode(file_get_contents("php://input"), true);

    //Gestionnaire d'erreurs
    $erreurs = array();

    //-----------------------MODIFIER COURRIEL-----------------------
    if (preg_match('/\/Liste_compte\/API\/preferences\.php\/courriel$/', $_SERVER['REQUEST_URI'], $matches)) {

    }

    //-----------------------MODIFIER MDP-----------------------
    else if (preg_match('/\/Liste_compte\/API\/preferences\.php\/mdp$/', $_SERVER['REQUEST_URI'], $matches)) {
        
    }

    //-----------------------SUPPRIMER COMPTE BANCAIRE-----------------------
    else if (preg_match('/\/Liste_compte\/API\/preferences\.php\/compteBancaire$/', $_SERVER['REQUEST_URI'], $matches)) {
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

            // //Effacer les notifications
            // $transactions = join("','",$transactionsArray);   
            // $sql = "DELETE FROM NotificationClient WHERE idTransaction IN ('$transactions');";
            // $conn->query($sql);

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

    //-----------------------SUPPRIMER COMPTE-----------------------
    else if (preg_match('/\/Liste_compte\/API\/preferences\.php\/compte$/', $_SERVER['REQUEST_URI'], $matches)) {
        
    }

    else { 
        echo json_encode(['erreur' => 'Mauvaise route.',
                          'code' => 404]);
    }
}


?>