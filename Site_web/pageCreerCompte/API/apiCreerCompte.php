<?php
    if(isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] === 'POST') {
        //Connection a la base de donnee
        try{
            require("../../connexion.php");
        }
        catch(Exception $e)
        {
            die("Connection echouee : " . $e->getMessage());
        }

        //Inclure fichier qui contient la fonction d'encryption
        include "../../Encryption/encryption.php";

        //Get les données du POST
        $donneesJSON = json_decode(file_get_contents("php://input"), true);

        //Gestion d'erreurs
        $erreurs = array();
        $erreurMdp = array();

        if(empty($donneesJSON['mobile']))
        {
            //Get toutes les données JSON
            $nom = trim($donneesJSON['nom']);
            $prenom = trim($donneesJSON['prenom']);
            $courriel = trim($donneesJSON['courriel']);
            $password = trim($donneesJSON['password']);
            $confirmation_mdp = trim($donneesJSON['conf_password']);
            $mobile = false;
        }
        else
        {
            //Get donnees JSON mobile
            $nom = trim(implode($donneesJSON['nom']));
            $prenom = trim(implode($donneesJSON['prenom']));
            $courriel = trim(implode($donneesJSON['courriel']));
            $password = trim(implode($donneesJSON['password']));
            $confirmation_mdp = trim(implode($donneesJSON['conf_password']));
            $mobile = true;
        }


        //Vérifier le prénom
        if(empty($prenom) || is_numeric($prenom))
            $erreurs[] = "Le prénom saisi est invalide";
        else
            $prenom = htmlspecialchars($prenom);

        //Vérifier le nom
        if(empty($nom) || is_numeric($nom))
            $erreurs[] = "Le nom saisi est invalide";
        else
            $nom = htmlspecialchars($nom);

        //Vérifier si le mot de passe est dans les critères
        if(strlen($password) < 8 || !preg_match("/[a-z]/", $password) 
        || !preg_match("/[A-Z]/", $password) || !preg_match("/[0-9]/", $password) 
        || !preg_match("/[@.#$%^&*!]/", $password)) 

                $erreurMdp[] = "Le mot de passe est invalide! Il doit contenir:<br>" . 
                "- Au moins 8 caractères<br>" . 
                "- Au moins 1 lettre majuscule et minuscule<br>" . 
                "- Au moins 1 caractère spécial<br>";
        else
            $password = htmlspecialchars($password);
        
        //Vérifier si les 2 mots de passe sont identiques
        if($password != $confirmation_mdp) 
            $erreurs[] = "Les mots de passe ne sont pas identiques!";

        //Verifie si courriel suit le bon format
        if(!filter_var($courriel, FILTER_VALIDATE_EMAIL))
            $erreurs[] = "Le courriel saisi n'est pas valide!";
        else {
            $courriel = htmlspecialchars($courriel);

            //Verifier si courriel existe deja 
            $requete = $conn->prepare("SELECT * FROM Compte WHERE courriel = '$courriel'");
            $requete->execute();

            if($requete->rowCount() != 0)
                $erreurs[] = "Le courriel est déjà utilisé";
        }

        
        //Si tout est valide, ajouter utilisateur a la base de données
        if(count($erreurs) == 0 && count($erreurMdp) == 0)
        {
            //Encrypter le mot de passe
            $mdp_encrypte = AES256CBC_encrypter($password, CLE_ENCRYPTION);

            //Effectuer la requête pour créer le compte utilisateur
            $requete = $conn->prepare("INSERT INTO Compte (courriel, prenom, nom, motDePasse) 
            VALUES ('$courriel', '$prenom', '$nom', '$mdp_encrypte')");
            $requete->execute();

            //---------------------CRÉER COMPTE CHÈQUE---------------------
            $interet = 1.00;
            
            $requete = $conn->prepare("INSERT INTO CompteBancaire (compteId, solde, typeCompte, interet, ouverture, suspendu) VALUES
            ((SELECT id FROM Compte WHERE courriel LIKE '$courriel'), 0, 'Compte chèque', '$interet', NOW(), 0);");
            $requete->execute();

            //Chercher l'ID du compte et créer le nom de l'événement
            $requete = $conn->prepare("SELECT id FROM CompteBancaire WHERE typeCompte='Compte chèque' 
            AND compteId LIKE (SELECT id FROM Compte WHERE courriel LIKE '$courriel')");
            $requete->execute();
            $idCompteCheque = $requete->fetchColumn();
            $eventName = "interet" . $idCompteCheque;

            //Écrire le sql de la requête
            //--À chaque jour, on met le montant gangé en intérêt dans les transactions
            //--et on actualise le solde
            $requete = "CREATE EVENT `projet_integrateur`.`$eventName`
            ON SCHEDULE EVERY 1 MINUTE STARTS NOW() DO 
                INSERT INTO TransactionBancaire (idCompteBancaireRecevant, dateTransaction, montant, typeTransaction) 
                SELECT id, NOW(), solde*(1 + $interet/100) - solde, 'Intérêts' 
                FROM CompteBancaire 
                WHERE CompteBancaire.id = $idCompteCheque;
            
                UPDATE CompteBancaire 
                SET solde = solde*(1 + $interet/100)
                WHERE id = $idCompteCheque;";

            $conn->query($requete);



            //Créer événement qui prend le solde total tous les jours
            //D'abord chercher l'ID de l'utilisateur
            $requete = $conn->prepare("SELECT id FROM Compte WHERE courriel LIKE '$courriel';");
            $requete->execute();
            $idUtilisateur = $requete->fetchColumn();

            //Set le nom de l'événement
            $eventNameSolde = "solde" . $idUtilisateur;
            
            //Faire l'événement
            $requete = "CREATE EVENT `projet_integrateur`.`$eventNameSolde`
            ON SCHEDULE EVERY 1 DAY STARTS NOW() DO 
            INSERT INTO SommeTotale (compteId, solde, dateSolde) 
            VALUES ($idUtilisateur, (SELECT SUM(solde) AS total_solde FROM CompteBancaire WHERE compteId = $idUtilisateur), NOW());";

            $conn->prepare($requete)->execute();

            if($mobile)
            {
                echo json_encode(['reponse'=>"Bienvenue chez Fish&Fric :) ", 'code'=>'201']);
            }
            else
            {
            //Mettre le message de succès 
            echo json_encode(['msgSucces' => "L'utilisateur a été créé avec succès! Bienvenue chez Fish&Fric."]); 
            }
        }

        //Sinon, on affiche les erreurs
        else
        {
            if($mobile)
            {
                //HTTP CODE 401 Donnee eronnees
                $str = implode(',', $erreurs);

                echo json_encode(['reponse'=>"$str", 'code'=>'401']);
            }
            else
            {
                echo json_encode(['erreurs' => $erreurs, 'erreurMdp' => $erreurMdp]); 
            }
        }
    } 
?>