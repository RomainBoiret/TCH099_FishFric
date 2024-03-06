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
        
                if($result['solde'] < $montant)
                    $erreurs[] = "Le montant est supérieur au solde"; 

                if($montant == 0)
                    $erreurs[] = "Le montant du virement ne peut pas être nul"; 
            }
        
            else
                $erreurs[] = "Montant non reçu ou non valide";

            //Vérif. ID compte bancaire destinataire du transfert
            if(isset($donnees["idCompteBancaireRecevant"]) 
            && is_numeric(trim($donnees["idCompteBancaireRecevant"]))) {
                $idCompteBancaireRecevant = $donnees["idCompteBancaireRecevant"];
                $idCompteBancaireRecevant = intval(trim($idCompteBancaireRecevant));

                //Vérifier que l'ID des 2 comptes ne sont pas les mêmes
                if($idCompteBancaireProvenant == $idCompteBancaireRecevant) {
                    $erreurs[] = "Les comptes doivent être différents";
                }
            } else {
                $erreurs[] = "ID recevant non reçu ou non valide";
            }
        }       
            
        else
            $erreurs[] = "ID provenant non reçu ou non valide";
        

        //TRANSFERT ENTRE UTILISATEURS, ENVOI-----------------------------------------
        if (preg_match('/\/Transfert\/API\/gestionTransfert\.php\/utilisateurEnvoi$/', $_SERVER['REQUEST_URI'], $matches)) {
            //Vérifier qu'il y a une question de sécurité
            if(isset($donnees['question']))
                $question = $donnees['question'];
            else
                $erreurs[] = "Question non reçue ou non valide";

            //Vérifier qu'il y a une réponse
            if(isset($donnees['reponse']))
                $reponse = $donnees['reponse'];
            else
                $erreurs[] = "Réponse non reçue ou non valide";

            //Vérifier qu'il y a un nom de contact
            if(isset($donnees['nomContact']))
                $nomContact = $donnees['nomContact'];
            else
                $erreurs[] = "Nom de contact non reçu ou non valide";


            //Prepare toutes les données pour éviter les injections SQL
            $question = (trim($question));
            $reponse = (trim($reponse));
            $nomContact = (trim($nomContact));
         
            if(empty($erreurs)) {
                //Actualiser le montant du compte bancaire provenant
                $sql = "UPDATE CompteBancaire SET solde = solde - $montant WHERE id = '$idCompteBancaireProvenant';";
                $conn->query($sql);

                //Ajouter la transaction en attente
                $sql = "INSERT INTO TransactionBancaire (idCompteBancaireProvenant, idCompteBancaireRecevant, 
                dateTransaction, montant, typeTransaction, enAttente, question, reponse, nomContact) VALUES ('$idCompteBancaireProvenant, $idCompteBancaireRecevant, 
                NOW(), '$montant', 'Virement entre utilisateurs', 1, '$question', '$reponse', '$nomContact');";
                $conn->query($sql);
            }
        }


        //TRANSFERT ENTRE UTILISATEURS, RECEPTION-----------------------------------------
        if (preg_match('/\/Transfert\/API\/gestionTransfert\.php\/utilisateurReception$/', $_SERVER['REQUEST_URI'], $matches)) {
            //Vérifier qu'il y a une acceptation ou bien un refus du transfert
            if(isset($donnees['validation']))
                $validation = $donnees['validation'];
            else
                $erreurs[] ="Choix du recevant non-reçu ou non valide";

            if(isset($donnees['idTransaction']))
                $validation = $donnees['idTransaction'];
            else
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
            }
        }


        //TRANSFERT ENTRE comptes-----------------------------------------
        else if (preg_match('/\/Transfert\/API\/gestionTransfert\.php\/compte$/', $_SERVER['REQUEST_URI'], $matches)) {
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
                NOW(), '$montant', 'Virement entre comptes');";
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
    