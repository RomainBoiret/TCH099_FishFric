<?php 

if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "POST") {
    //Gérer la connexion à la base de données
    try {
        require "../../connexion.php";
    } catch(Exception $e) {
        die("Connexion échouée!: " .$e->getMessage());
    }

    //Chercher l'ID de l'utilisateur conencté
    session_start();
    $idUtilisateur = $_SESSION["utilisateur"];

    //Gestion d'erreurs
    $erreurs = array();

    //Get les données du POST
    $donneesJSON = json_decode(file_get_contents("php://input"), true);

    //Vérifier qu'un choix de compte a été fait
    $typeCompte = trim($donneesJSON['typeCompte']);

    if(empty($typeCompte)) {
        //Si pas de choix n'a été fait, on retourne une erreur
        $erreurs[] = "Veuillez choisir un compte à créer";
        echo json_encode(['erreurs' => $erreurs]);
        exit();
    }
    
    //Sinon, on peut créer le compte
    else {
        //Vérifier que le client n'a pas déjà un compte de ce type
        $requete = $conn->prepare("SELECT * FROM CompteBancaire WHERE typeCompte = '$typeCompte' AND compteId LIKE '$idUtilisateur'");
        $requete->execute();

        if($requete->rowCount() != 0) {
            $erreurs[] = "Vous possédez déjà ce type de compte";
            echo json_encode(['erreurs' => $erreurs]);
        }

        //Sinon, on peut créer le compte
        else {
            //Mettre l'intérêt selon le compte
            if ($typeCompte == 'Compte épargne') {
                $interet = 3.00;
                $soldeDeBase = 15.00;
            }

            //Les cartes de crédit ont 0 solde et un intérêt de -9.99
            else if ($typeCompte == 'Carte requin') {
                $interet = 9.99;
                $soldeDeBase = 0.00;
            }

            else {
                $interet = 0;
                $soldeDeBase = 0.00;
            }
            
            $requete = $conn->prepare("INSERT INTO CompteBancaire (compteId, solde, typeCompte, interet, ouverture, suspendu) 
            VALUES ('$idUtilisateur', $soldeDeBase, '$typeCompte', '$interet', NOW(), 0);");
            $requete->execute();

            //Chercher l'ID du compte et créer le nom de l'événement

            // $requete = $conn->prepare("SELECT id FROM CompteBancaire WHERE typeCompte='$typeCompte' 
            // AND compteId LIKE '$idUtilisateur'");
            // $requete->execute();
            // $idCompte = $requete->fetchColumn();
            // $eventName = "interet" . $idCompte;

            // //Écrire le sql de la requête
            // //--À chaque jour, on met le montant gangé en intérêt dans les transactions
            // //--et on actualise le solde

            // $requete = "CREATE EVENT `projet_integrateur`.`$eventName`
            // ON SCHEDULE EVERY 1 DAY STARTS NOW() DO 
            //     INSERT INTO TransactionBancaire (idCompteBancaireRecevant, dateTransaction, montant, typeTransaction) 
            //     SELECT id, NOW(), solde*(1 + $interet/100) - solde, 'Intérêts' 
            //     FROM CompteBancaire 
            //     WHERE CompteBancaire.id = $idCompte;
            
            //     UPDATE CompteBancaire 
            //     SET solde = solde*(1 + $interet/100)
            //     WHERE id = $idCompte;";

            // $conn->query($requete);


            //Renvoyer message de succès
            echo json_encode(['succes' => "Votre nouveau compte a été crée!"]);
        }
    }
}
?>