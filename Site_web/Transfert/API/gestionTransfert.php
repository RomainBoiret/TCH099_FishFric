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

        //VÉRIFICATIONS DES DONNÉES DU POST-----------------------------------------
        //Vérif. ID compte bancaire source du transfert
        if(isset($donnees["idCompteBancaireProvenant"]) 
        && is_numeric(trim($donnees["idCompteBancaireRecevant"]))) {
            $idCompteBancaireProvenant = $donnees["idCompteBancaireProvenant"];
            $idCompteBancaireProvenant = intval(trim($idCompteBancaireProvenant));
        }
            
        else
            echo json_encode(['erreur' => "ID PROVENANT NON REÇU OU NON VALIDE", 'code' => 400]);

        //Vérif. ID compte bancaire destinataire du transfert
        if(isset($donnees["idCompteBancaireRecevant"]) 
        && is_numeric(trim($donnees["idCompteBancaireRecevant"]))) {
            $idCompteBancaireRecevant = $donnees["idCompteBancaireRecevant"];
            $idCompteBancaireRecevant = intval(trim($idCompteBancaireRecevant));
        }

        else
            echo json_encode(['erreur' => "ID RECEVANT NON REÇU OU NON VALIDE", 'code' => 400]);

        //Vérifier qu'il y a un montant
        if(isset($donnees["montant"]) && is_numeric($donnees["montant"])) {
            $montant = $donnees["montant"];
            $montant = intval(trim($montant));
        }
    
        else
            echo json_encode(['erreur' => "Montant non valide", 'code' => 400]);
        

        //TRANSFERT ENTRE UTILISATEURS, ENVOI-----------------------------------------
        if (preg_match('/\/Transfert\/API\/gestionTransfert\.php\/utilisateurEnvoi$/', $_SERVER['REQUEST_URI'], $matches)) {
            //Vérifier qu'il y a une question de sécurité
            if(isset($donnees['question']))
                $question = $donnees['question'];
            else
                echo json_encode(['erreur' => "QUESTION NON REÇUE OU NON VALIDE", 'code' => 400]);

            //Vérifier qu'il y a une réponse
            if(isset($donnees['reponse']))
                $reponse = $donnees['reponse'];
            else
                echo json_encode(['erreur' => "RÉPONSE NON REÇUE OU NON VALIDE", 'code' => 400]);

            //Vérifier qu'il y a un nom de contact
            if(isset($donnees['nomContact']))
                $nomContact = $donnees['nomContact'];
            else
                echo json_encode(['erreur' => "NOM DE CONTACT NON REÇU OU NON VALIDE", 'code' => 400]);


            //Prepare toutes les données pour éviter les injections SQL
            $question = (trim($question));
            $reponse = (trim($reponse));
            $nomContact = (trim($nomContact));
         
            //Actualiser le montant du compte bancaire provenant
            $sql = "UPDATE CompteBancaire SET solde = solde - $montant WHERE id = '$idCompteBancaireProvenant';";
            $conn->query($sql);

            //Ajouter la transaction en attente
            $sql = "INSERT INTO TransactionBancaire (idCompteBancaireProvenant, idCompteBancaireRecevant, 
            dateTransaction, montant, typeTransaction, enAttente, question, reponse, nomContact) VALUES ('$idCompteBancaireProvenant, $idCompteBancaireRecevant, 
            NOW(), '$montant', 'Virement entre utilisateurs', 1, '$question', '$reponse', '$nomContact');";
            $conn->query($sql);
        }


        //TRANSFERT ENTRE UTILISATEURS, RECEPTION-----------------------------------------
        if (preg_match('/\/Transfert\/API\/gestionTransfert\.php\/utilisateurEnvoi$/', $_SERVER['REQUEST_URI'], $matches)) {
            //Vérifier qu'il y a une question de sécurité
            if(isset($donnees['question']))
                $question = $donnees['question'];
            else
                echo json_encode(['erreur' => "QUESTION NON REÇUE OU NON VALIDE", 'code' => 400]);

            //Vérifier qu'il y a une réponse
            if(isset($donnees['reponse']))
                $reponse = $donnees['reponse'];
            else
                echo json_encode(['erreur' => "RÉPONSE NON REÇUE OU NON VALIDE", 'code' => 400]);

            //Vérifier qu'il y a une acceptation ou bien un refus du transfert
            if(isset($donnees['accept']))
                $nomContact = $donnees['nomContact'];
            else
                echo json_encode(['erreur' => "NOM DE CONTACT NON REÇU OU NON VALIDE", 'code' => 400]);


            //Prepare toutes les données pour éviter les injections SQL
            $question = (trim($question));
            $reponse = (trim($reponse));
            $nomContact = (trim($nomContact));
         
            //Actualiser le montant du compte bancaire provenant
            $sql = "UPDATE CompteBancaire SET solde = solde - $montant WHERE id = '$idCompteBancaireProvenant';";
            $conn->query($sql);

            //Ajouter la transaction en attente
            $sql = "INSERT INTO TransactionBancaire (idCompteBancaireProvenant, idCompteBancaireRecevant, 
            dateTransaction, montant, typeTransaction, enAttente, question, reponse, nomContact) VALUES ('$idCompteBancaireProvenant, $idCompteBancaireRecevant, 
            NOW(), '$montant', 'Virement entre utilisateurs', 1, '$question', '$reponse', '$nomContact');";
            $conn->query($sql);
        }


        //TRANSFERT ENTRE comptes-----------------------------------------
        else if (preg_match('/\/Transfert\/API\/gestionTransfert\.php\/compte$/', $_SERVER['REQUEST_URI'], $matches)) {
            if($idCompteBancaireProvenant != $idCompteBancaireRecevant) {
                //Actualiser le montant du compte bancaire provenant
                $sql = "UPDATE CompteBancaire SET solde = solde - $montant WHERE id = '$idCompteBancaireProvenant';";
                $conn->query($sql);

                //Actualiser le montant du compte bancaire recevant
                $sql = "UPDATE CompteBancaire SET solde = solde + $montant WHERE id = '$idCompteBancaireRecevant';";
                $conn->query($sql);

                //Ajouter la transaction
                $sql = "INSERT INTO TransactionBancaire (idCompteBancaireProvenant, idCompteBancaireRecevant, 
                dateTransaction, montant, typeTransaction) VALUES ($idCompteBancaireProvenant, $idCompteBancaireRecevant, 
                NOW(), '$montant', 'Virement entre comptes');";
                $conn->query($sql);
            } else {
                //Le compte de banque provenant ne peut pas être le même que le compte en banque recevant
                echo json_encode(['erreur' => 'Le compte de banque provenant ne peut pas être le même que 
                    le compte en banque recevant', 'code' => 400]);
            }
        }

        else { 
            echo json_encode(['erreur' => 'Mauvaise route.',
                              'code' => 404]);
        }
    }

    else {
        // Code HTTP 405 - Method Not Allowed
        echo json_encode(['erreur' => 'Méthode non autorisée.',
                        'code' => 405]);
    }
    