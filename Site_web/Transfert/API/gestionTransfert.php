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

        //VÉRIFICATIONS DES DONNÉES DU POST-----------------------------------------
        //Vérif. ID compte bancaire source du transfert
        if(isset($donnees["idCompteBancaireProvenant"]) 
        && is_numeric(trim($donnees["idCompteBancaireProvenant"]))) {
            //Mettre la valeur dans une variable
            $idCompteBancaireProvenant = $donnees["idCompteBancaireProvenant"];
            $idCompteBancaireProvenant = intval(trim($idCompteBancaireProvenant));

            //Vérifier qu'il y a un montant
            if(isset($donnees["montant"]) && is_numeric($donnees["montant"])) {
                $montant = $donnees["montant"];
                $montant = floatval(trim($montant));

                //VÉRIF SOLDE - Requête pour checker si solde <= 0
                //Vérifier si le numéro est déjà dans la base de données
                $requete = $conn->prepare("SELECT solde FROM CompteBancaire WHERE id = '$idCompteBancaireProvenant'");
                $requete->execute();
                $result = $requete->fetch(PDO::FETCH_ASSOC);
        
                //Si le solde est pas suffisant ou bien nul, on met une erreur
                if($result['solde'] < $montant)
                    $erreurs[] = "Le montant est supérieur au solde"; 

                if($montant == 0)
                    $erreurs[] = "Le montant du virement ne peut pas être nul"; 
            }

            else
                $erreurs[] = "Montant non reçu ou non valide";
        }    

        else
            $erreurs[] = "ID provenant non reçu ou non valide";
        

        //TRANSFERT ENTRE UTILISATEURS, ENVOI-----------------------------------------
        if (preg_match('/\/Transfert\/API\/gestionTransfert\.php\/utilisateurEnvoi$/', $_SERVER['REQUEST_URI'], $matches)) {
            //Vérifier qu'il y a un courriel de contact
            if(isset($donnees['courrielDest']) && !is_numeric($donnees['courrielDest'])) {
                $courrielDest = $donnees['courrielDest'];
                $courrielDest = trim($courrielDest);

                //Vérifier que le courriel est un utilisateur de la banque
                $requete = $conn->prepare("SELECT * FROM Compte WHERE courriel = '$courrielDest'");
                $requete->execute();
                if(!$requete->fetch(PDO::FETCH_ASSOC)) {
                    $erreurs[] = "Le courriel saisi n'est pas un utilisateur de la banque";
                }

            } else
                $erreurs[] = "Courriel de contact non reçu ou non valide";
            
            //Vérifier qu'il y a une question de sécurité
            if(isset($donnees['question']) && !is_numeric($donnees['question'])) {
                $question = $donnees['question'];
                $question = (trim($question));
            } else
                $erreurs[] = "Question non reçue ou non valide";

            //Vérifier qu'il y a une réponse
            if(isset($donnees['reponse']) && !is_numeric($donnees['reponse'])) {
                $reponse = $donnees['reponse'];
                $reponse = (trim($reponse));
            } else
                $erreurs[] = "Réponse non reçue ou non valide";

            //Vérifier qu'il y a une confirmation de la réponse
            if(isset($donnees['confReponse']) && !is_numeric($donnees['confReponse'])) {
                $confReponse = $donnees['confReponse'];
                $confReponse = (trim($confReponse));
            } else
                $erreurs[] = "Confirmation de réponse non reçue ou non valide";

            //Vérifier que la réponse et la confirmation sont identiques
            if(isset($donnees['reponse']) && !is_numeric($donnees['reponse'])
            && isset($donnees['confReponse']) && !is_numeric($donnees['confReponse'])) {
                if (!($reponse == $confReponse))
                    $erreurs[] = "La réponse doit être identique à la confirmation de réponse";  
            }
         
            if(empty($erreurs)) {
                //Actualiser le montant du compte bancaire provenant
                $sql = "UPDATE CompteBancaire SET solde = solde - $montant WHERE id = '$idCompteBancaireProvenant';";
                $conn->query($sql);

                //Ajouter la transaction en attente
                $sql = "INSERT INTO TransactionBancaire (idCompteBancaireProvenant, dateTransaction, montant, 
                typeTransaction, enAttente, question, reponse, nomEtablissement) VALUES ('$idCompteBancaireProvenant', 
                NOW(), '$montant', 'Virement', 1, '$question', '$reponse', '$courrielDest');";
                $conn->query($sql);

                //Message de succès
                echo json_encode(['msgSucces' => "Le virement a été effectué avec succès!"]);
            } 
            
            //Sinon, le virement n'a pas marché. On renvoie les messages d'erreur
            else {
                //Le compte de banque provenant ne peut pas être le même que le compte en banque recevant
                echo json_encode(['erreur' => $erreurs]);
            }
        }


        //TRANSFERT ENTRE UTILISATEURS, RECEPTION-----------------------------------------
        else if (preg_match('/\/Transfert\/API\/gestionTransfert\.php\/utilisateurReception$/', $_SERVER['REQUEST_URI'], $matches)) {
            //Vérifier qu'il y a une acceptation ou bien un refus du transfert
            if(isset($donnees['validation'])) {
                $validation = $donnees['validation'];
                $validation = trim($validation);
            } else
                $erreurs[] ="Choix du recevant non-reçu ou non valide";

            if(isset($donnees['idTransaction'])) {
                $idTransaction = $donnees['idTransaction'];
                $idTransaction = trim($idTransaction);
            } else
                $erreurs[] ="Id de transaction non-reçu ou non valide";

            //Prepare toutes les données pour éviter les injections SQL
            $validation = (trim($validation));
         
            //S'il n'y a pas d'erreurs, on effectue le virement
            if(empty($erreurs)) {
                //Actualiser le montant du compte bancaire recevant
                $sql = "UPDATE CompteBancaire SET solde = solde + $montant WHERE id = '$idCompteBancaireRecevant';";
                $conn->query($sql);

                //Actualiser la transaction, elle n'est plus en attente
                $sql = "UPDATE TransactionBancaire SET enAttente = 0 WHERE id = '$idTransaction';";
                $conn->query($sql);

                //Message de succès
                echo json_encode(['msgSucces' => "Le virement a été effectué avec succès!"]);
            } 
            
            //Sinon, on renvoie les messages d'erreur
            else {
                //Le compte de banque provenant ne peut pas être le même que le compte en banque recevant
                echo json_encode(['erreur' => $erreurs]);
            }
        }


        //TRANSFERT ENTRE comptes-----------------------------------------
        else if (preg_match('/\/Transfert\/API\/gestionTransfert\.php\/compte$/', $_SERVER['REQUEST_URI'], $matches)) {
            //Vérif. ID compte bancaire destinataire du transfert
            if(isset($donnees["idCompteBancaireRecevant"]) 
            && is_numeric(trim($donnees["idCompteBancaireRecevant"]))) {
                $idCompteBancaireRecevant = $donnees["idCompteBancaireRecevant"];
                $idCompteBancaireRecevant = intval(trim($idCompteBancaireRecevant));
            } else 
                $erreurs[] = "ID recevant non reçu ou non valide";

            //Vérifier que les 2 comptes ne soient pas les mêmes
            if(isset($donnees["idCompteBancaireProvenant"]) 
            && is_numeric(trim($donnees["idCompteBancaireProvenant"]))
            && isset($donnees["idCompteBancaireRecevant"]) 
            && is_numeric(trim($donnees["idCompteBancaireRecevant"]))) {
                if ($idCompteBancaireProvenant == $idCompteBancaireRecevant)
                    $erreurs[] = "Les 2 comptes ne peuvent pas être le même";
            }
            
            //S'il n'y a pas d'erreurs, on effectue le virement
            if(empty($erreurs)) {
                //Actualiser le montant du compte bancaire provenant
                $sql = "UPDATE CompteBancaire SET solde = solde - $montant WHERE id = '$idCompteBancaireProvenant';";
                $conn->query($sql);

                //Actualiser le montant du compte bancaire recevant
                $sql = "UPDATE CompteBancaire SET solde = solde + $montant WHERE id = '$idCompteBancaireRecevant';";
                $conn->query($sql);

                //Ajouter la transaction
                $sql = "INSERT INTO TransactionBancaire (idCompteBancaireProvenant, idCompteBancaireRecevant, 
                dateTransaction, montant, typeTransaction) VALUES ($idCompteBancaireProvenant, $idCompteBancaireRecevant, 
                NOW(), '$montant', 'Transfert');";
                $conn->query($sql);

                //Message de succès
                echo json_encode(['msgSucces' => "Le transfert a été effectué avec succès!"]);
            } 
            
            //Sinon, on renvoie les messages d'erreur
            else {
                //Le compte de banque provenant ne peut pas être le même que le compte en banque recevant
                echo json_encode(['erreur' => $erreurs]);
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
    