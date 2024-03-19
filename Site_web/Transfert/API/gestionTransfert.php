<?php
    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == 'PUT') {
        //Gérer la connexion à la base de données
        try {
            require("../../connexion.php");
        } catch(Exception $e) {
            die("Connexion échouée!: " .$e->getMessage());
        }

        //Chercher ID de l'utilisateur 
        session_start();
        $compteIdProvenant = $_SESSION['utilisateur'];

        // Obtenir les données POST et les décoder
        $donnees = json_decode(file_get_contents("php://input"), true);

        $erreurs = [];

        //----------------------------VÉRIFICATIONS DES DONNÉES DU POST s'appliquant à TOUS les types de transfert SAUF réception de virement-------------------------
        //
        //On ne vérifie PAS le compte provenant et le montant si c'est pour une réception de virement
        if (!preg_match('/\/Transfert\/API\/gestionTransfert\.php\/utilisateurReception$/', $_SERVER['REQUEST_URI'], $matches))  {
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
        }
        

        //-----------------------------------------TRANSFERT ENTRE UTILISATEURS, ENVOI-----------------------------------------
        //
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

            //---------Vérifier que le destinataire n'est pas la personne envoyant le virement
            //Chercher courriel de l'utilisateur envoyant le virement
            $sql = "SELECT courriel FROM Compte WHERE id='$compteIdProvenant';";
            $resultat = $conn->query($sql);
            $courrielCompteProvenant = $resultat->fetchColumn();

            //Si le courriel destinataire est le même, on met une erreur
            if(isset($donnees['courrielDest']) && !is_numeric($donnees['courrielDest'])) {
                if (strtolower($courrielDest) == strtolower($courrielCompteProvenant))
                    $erreurs[] = "Vous ne pouvez pas envoyer un virement à vous-même!";
            }
         
            //---------S'il n'y a pas d'erreurs, on effectue le virement
            if(empty($erreurs)) {
                //Actualiser le montant du compte bancaire provenant
                $sql = "UPDATE CompteBancaire SET solde = solde - $montant WHERE id = '$idCompteBancaireProvenant';";
                $conn->query($sql);

                //Ajouter la transaction en attente
                $sql = "INSERT INTO TransactionBancaire (idCompteBancaireProvenant, dateTransaction, montant, 
                typeTransaction, enAttente, question, reponse, nomEtablissement) VALUES ('$idCompteBancaireProvenant', 
                NOW(), '$montant', 'Virement', 1, '$question', '$reponse', '$courrielDest');";
                $conn->query($sql);

                //----------AJOUTER transaction------------

                //Chercher ID de la transaction
                $sql = "SELECT id FROM TransactionBancaire ORDER BY id DESC LIMIT 1;";
                $resultat = $conn->query($sql);
                $idTransaction = $resultat->fetchColumn();

                //Chercher ID de l'utilisateur destinataire
                $sql = "SELECT id FROM Compte WHERE courriel='$courrielDest';";
                $resultat = $conn->query($sql);
                $compteIdDestinataire = $resultat->fetchColumn();

                //Ajout notification d'envoi de virement à la personne envoyant le virement
                $contenuNotif = 'Vous avez envoyé un virement de ' . $montant . '$ à ' . $courrielDest;
                $sql = "INSERT INTO NotificationClient(compteId, titre, contenu, lu, dateRecu, idTransaction)
                VALUES ('$compteIdProvenant', 'Virement envoyé', '$contenuNotif', 0, NOW(), $idTransaction);";
                $conn->query($sql);

                //Ajouter notification de réception de virement au destinataire
                $contenuNotif = 'Vous avez reçu un virement de ' . $montant . '$ de la part de ' . $courrielCompteProvenant;
                $sql = "INSERT INTO NotificationClient(compteId, titre, contenu, lu, dateRecu, idTransaction)
                VALUES ($compteIdDestinataire, 'Virement reçu', '$contenuNotif', 0, NOW(), $idTransaction);";
                $conn->query($sql);

                //Message de succès
                echo json_encode(['msgSucces' => "Le virement a été effectué avec succès!"]);
            } 
            
            //Sinon, le virement n'a pas marché. On renvoie les messages d'erreur
            else {
                echo json_encode(['erreur' => $erreurs]);
            }
        }


        //-----------------------------------------TRANSFERT ENTRE UTILISATEURS, RECEPTION-----------------------------------------
        //
        else if (preg_match('/\/Transfert\/API\/gestionTransfert\.php\/utilisateurReception$/', $_SERVER['REQUEST_URI'], $matches)) {
            //Vérifier qu'il y a une acceptation ou bien un refus du transfert
            if(isset($donnees['decision'])) {
                $decision = $donnees['decision'];
                $decision = trim($decision);

                //Si la décision est d'accepter, on vérifie la réponse
                if ($decision == 'accepter') {
                    if(isset($donnees['inputReponse'])) {
                        $inputReponse = $donnees['inputReponse'];
                        $inputReponse = trim($inputReponse);
    
                        //Vérifier que la réponse est bonne ou pas
                        $idTransaction = $donnees['idTransaction'];
                        $sql = "SELECT reponse FROM TransactionBancaire WHERE id = $idTransaction";
                        $resultat = $conn->query($sql);
                        $reponse = $resultat->fetchColumn();
    
                        //Si les 2 ne matchent pas, on envoie une erreur
                        if ($reponse != $inputReponse) {
                            $erreurs[] = "Réponse incorrecte! Veuillez réessayer";
                        }

                    } 
                    
                    //Sinon, ça veut dire qu'aucune réponse n'a été inscrite
                    else {
                        $erreurs[] = "Réponse non-reçue";
                    }
                } 

            } else
                $erreurs[] ="Décision non-reçue ou non valide";


            //S'il n'y a pas d'erreurs, on effectue le virement
            if(empty($erreurs)) {

                //Chercher le courriel de la personne qui a envoyé le virement
                $sql = "SELECT courriel FROM compte c INNER JOIN CompteBancaire cb ON cb.compteId=c.id 
                INNER JOIN TransactionBancaire tb ON tb.idCompteBancaireProvenant = cb.id WHERE tb.id = '$idTransaction'";
                $resultat = $conn->query($sql);
                $courrielCompteProvenant = $resultat->fetchColumn();

                //Chercher courriel destinataire
                $sql = "SELECT nomEtablissement FROM TransactionBancaire WHERE id='$idTransaction'";
                $resultat = $conn->query($sql);
                $courrielDest = $resultat->fetchColumn();

                //Chercher CompteId de l'envoyeur
                $sql = "SELECT id FROM Compte WHERE courriel LIKE '$courrielCompteProvenant'";
                $resultat = $conn->query($sql);
                $idCompteProvenant = $resultat->fetchColumn();

                //-------------------------ACCEPTER LE VIREMENT---------------------------
                if ($decision == 'accepter') {

                    //CHERCHER ID du compte chèque recevant
                    $sql = "SELECT cb.id FROM CompteBancaire cb INNER JOIN Compte c ON c.id = cb.compteId WHERE c.id='$compteIdProvenant' AND typeCompte LIKE 'Compte chèque'";
                    $resultat = $conn->query($sql);
                    $idCompteBancaireRecevant = $resultat->fetchColumn();

                    //Chercher le montant du virement
                    $sql = "SELECT montant FROM TransactionBancaire WHERE id='$idTransaction'";
                    $resultat = $conn->query($sql);
                    $montant = $resultat->fetchColumn();

                    //Actualiser le montant du compte bancaire recevant
                    $sql = "UPDATE CompteBancaire SET solde = solde + $montant WHERE id = '$idCompteBancaireRecevant';";
                    $conn->query($sql);

                    //Actualiser la transaction, elle n'est plus en attente
                    $sql = "UPDATE TransactionBancaire SET enAttente = 0, idCompteBancaireRecevant = '$idCompteBancaireRecevant'  WHERE id = '$idTransaction';";
                    $conn->query($sql);

                    //Modification de la notification pour montrer qu'on a accepté le virement
                    $msgSucces1 = "Le virement de " . $montant . "$ de la part de " . $courrielCompteProvenant 
                    . " a été déposé dans votre compte chèque!";

                    $sql = "UPDATE NotificationClient SET titre='Virement accepté', contenu = '$msgSucces1'
                    WHERE idTransaction='$idTransaction' AND CompteId='$compteIdProvenant'";
                    $conn->query($sql);

                    //Modification de la notification pour montrer À L'ENVOYEUR qu'on a accepté le virement
                    $msgSucces2 = $courrielDest . " a accepté votre virement.";
                    $sql = "UPDATE NotificationClient SET titre='Virement accepté', contenu = '$msgSucces2' 
                    WHERE idTransaction='$idTransaction' AND CompteId='$idCompteProvenant'";
                    $conn->query($sql);
                }


                //---------------------------------------REJETER LE VIREMENT---------------------------------------
                else {
                    // //CHERCHER ID du compte chèque de l'envoyeur
                    // $sql = "SELECT id FROM CompteBancaire WHERE id='$idCompteProvenant' AND typeCompte LIKE 'Compte chèque'";
                    // $resultat = $conn->query($sql);
                    // $idCompteBancaireProvenant = $resultat->fetchColumn();





                    // //Chercher le montant du virement
                    // $sql = "SELECT montant FROM TransactionBancaire WHERE id='$idTransaction'";
                    // $resultat = $conn->query($sql);
                    // $montant = $resultat->fetchColumn();

                    // //Actualiser le montant du compte bancaire recevant
                    // $sql = "UPDATE CompteBancaire SET solde = solde + $montant WHERE id = '$idCompteBancaireRecevant';";
                    // $conn->query($sql);
 





                }

                //Rechercher l'ID de la notification
                $sql = "SELECT id FROM NotificationClient WHERE idTransaction='$idTransaction' AND CompteId='$compteIdProvenant'";
                $resultat = $conn->query($sql);
                $id_notif = $resultat->fetchColumn();

                //Message de succès + ID de notification
                echo json_encode(['msgSucces' => $msgSucces1, 'id_notif' => $id_notif]);
            } 
            
            //Sinon, on renvoie les messages d'erreur
            else {
                echo json_encode(['erreur' => $erreurs]);
            }
        }


        //-----------------------------------------TRANSFERT ENTRE comptes-----------------------------------------
        //
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



                //À FAIRE
                //AJOUTER NOTIFICATIONS




                //Message de succès
                echo json_encode(['msgSucces' => "Le transfert a été effectué avec succès!"]);
            } 
            
            //Sinon, on renvoie les messages d'erreur
            else {
                echo json_encode(['erreur' => $erreurs]);
            }
        }

        //PAIEMENT DE FACTURE-----------------------------------------
        else if (preg_match('/\/Transfert\/API\/gestionTransfert\.php\/facture$/', $_SERVER['REQUEST_URI'], $matches)) { 
            //Vérifier que le nom d'établissement est présent
            if(isset($donnees['nomEtablissement']) && !is_numeric($donnees['nomEtablissement'])) {
                $nomEtablissement = $donnees['nomEtablissement'];
                $nomEtablissement = trim($nomEtablissement);
            } else
                $erreurs[] ="Nom d'établissement non-reçu ou non valide";

            //Vérifier que la raison de la facture est présente
            if(isset($donnees['raison']) && !is_numeric($donnees['raison'])) {
                $raison = $donnees['raison'];
                $raison = trim($raison);
            } else
                $erreurs[] ="Raison de la facture non-reçu ou non valide";
         
            //S'il n'y a pas d'erreurs, on effectue le paiement de la facture
            if(empty($erreurs)) {
                //Actualiser le montant du compte bancaire provenant
                $sql = "UPDATE CompteBancaire SET solde = solde - $montant WHERE id = '$idCompteBancaireProvenant';";
                $conn->query($sql);

                //Ajouter la transaction
                $sql = "INSERT INTO TransactionBancaire (idCompteBancaireProvenant, dateTransaction, montant, 
                typeTransaction, nomEtablissement) VALUES ('$idCompteBancaireProvenant', 
                NOW(), '$montant', 'Paiement de facture', '$nomEtablissement');";
                $conn->query($sql);



                //À FAIRE
                //AJOUTER NOTIFICATIONS




                //Message de succès
                echo json_encode(['msgSucces' => "Le paiement de la facture a été effectué avec succès!"]);
            } 
            
            //Sinon, le paiement n'a pas marché. On renvoie les messages d'erreur
            else {
                echo json_encode(['erreur' => $erreurs]);
            }
        }

        //ERREUR DE ROUTE-----------------------------------------
        else { 
            echo json_encode(['erreur' => 'Mauvaise route.',
                              'code' => 404]);
        }
    }





    //---------------------------REQUÊTE GET INFOS TRANSFERT-----------------------------
    else if(isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "GET") {
        //Gérer la connexion à la base de données
        try {
            require "../../connexion.php";
        } catch(Exception $e) {
            die("Connexion échouée!: " .$e->getMessage());
        }

        session_start();

        //Chercher la transaction en question et l'ID de l'utilisateur
        $idTransaction = $_GET['idTransaction'];
        $idUtilisateur = $_SESSION['utilisateur'];

        //Requête SQL pour chercher les transaction
        $requete = $conn->prepare("SELECT dateTransaction, montant, typeTransaction, enAttente, question, reponse
        FROM TransactionBancaire WHERE id='$idTransaction';");
        $requete->execute();
        $transaction = $requete->fetch();

        // Échapper les caractères spéciaux dans le contenu de la transaction
        $transaction['dateTransaction'] = htmlspecialchars($transaction['dateTransaction'], ENT_QUOTES, 'UTF-8');
        $transaction['montant'] = htmlspecialchars($transaction['montant'], ENT_QUOTES, 'UTF-8');
        $transaction['typeTransaction'] = htmlspecialchars($transaction['typeTransaction'], ENT_QUOTES, 'UTF-8');
        $transaction['enAttente'] = htmlspecialchars($transaction['enAttente'], ENT_QUOTES, 'UTF-8');
        $transaction['question'] = htmlspecialchars($transaction['question'], ENT_QUOTES, 'UTF-8');
        $transaction['reponse'] = htmlspecialchars($transaction['reponse'], ENT_QUOTES, 'UTF-8');

        //Encoder les informations des transaction en json
        echo json_encode(["transaction" => $transaction]);
    }

    else {
        // Code HTTP 405 - Method Not Allowed
        echo json_encode(['erreur' => 'Méthode non autorisée.',
                        'code' => 405]);
    }
    