<?php


/*----------------------------------------------------- GESTION GET NOTIFICATIONS ----------------------------------------------------*/
    if(isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST"){

        try{
            require "../../connexion.php";
        }catch(Exception $e)
        {
            die("Connexion échouée!: " .$e->getMessage());
        }

        $donneesJSON = json_decode(file_get_contents("php://input"), true);

        if(isset($donneesJSON['idUtilisateur']))
        {
            $idUtilisateur = trim($donneesJSON['idUtilisateur']);
        }

        //Requête SQL pour chercher les notifications
        $requete = $conn->prepare("SELECT nc.id AS id_notif, idTransaction, CompteId, titre, contenu, lu, dateRecu, idCompteBancaireProvenant,
        dateTransaction, montant, typeTransaction, enAttente, question, reponse
        FROM NotificationClient nc JOIN TransactionBancaire tb ON tb.id = nc.idTransaction WHERE compteId = $idUtilisateur;");
        $requete->execute();
        $notificationsEtTransactions = $requete->fetchAll(PDO::FETCH_ASSOC);

        // Échapper les caractères spéciaux dans le contenu des notifications
        foreach ($notificationsEtTransactions as $notification) {
            $notification['id_notif'] = htmlspecialchars($notification['id_notif'], ENT_QUOTES, 'UTF-8');
            $notification['CompteId'] = htmlspecialchars($notification['CompteId'], ENT_QUOTES, 'UTF-8');
            $notification['titre'] = htmlspecialchars($notification['titre'], ENT_QUOTES, 'UTF-8');
            $notification['contenu'] = htmlspecialchars($notification['contenu'], ENT_QUOTES, 'UTF-8');
            $notification['lu'] = htmlspecialchars($notification['lu'], ENT_QUOTES, 'UTF-8');
            $notification['dateRecu'] = htmlspecialchars($notification['dateRecu'], ENT_QUOTES, 'UTF-8');
            $notification['idTransaction'] = htmlspecialchars($notification['idTransaction'], ENT_QUOTES, 'UTF-8');

            $notification['idCompteBancaireProvenant'] = htmlspecialchars($notification['idCompteBancaireProvenant'], ENT_QUOTES, 'UTF-8');
            $notification['dateTransaction'] = htmlspecialchars($notification['dateTransaction'], ENT_QUOTES, 'UTF-8');
            $notification['montant'] = htmlspecialchars($notification['montant'], ENT_QUOTES, 'UTF-8');
            $notification['typeTransaction'] = htmlspecialchars($notification['typeTransaction'], ENT_QUOTES, 'UTF-8');
            $notification['enAttente'] = htmlspecialchars($notification['enAttente'], ENT_QUOTES, 'UTF-8');
            $notification['question'] = htmlspecialchars($notification['question'], ENT_QUOTES, 'UTF-8');
            $notification['reponse'] = htmlspecialchars($notification['reponse'], ENT_QUOTES, 'UTF-8');
        }
        //Encoder les informations des notifications en json
        echo json_encode(["notificationsEtTransactions" => $notificationsEtTransactions]);
    }
    /*----------------------------------------------------- GESTION DELETE NOTIFICATIONS ----------------------------------------------------*/
    else if(isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "DELETE")
    {
        try{
            require "../../connexion.php";
        }catch(Exception $e)
        {
            die("Connexion échouée!: " .$e->getMessage());
        }

        $donneesJSON = json_decode(file_get_contents("php://input"), true);

        if(isset($donneesJSON['idUtilisateur']))
        {
            $idUtilisateur = trim(implode($donneesJSON['idUtilisateur']));
        }

        if(isset($donneesJSON['idNotif']))
        {
            $idNotif = trim(implode($donneesJSON['idNotif']));
        }

        //Requete pour effacer toutes les notifcations
        if (preg_match('/\/Liste_compte\/API\/afficherNotifications\.php\/deleteAll$/')) {
            //Chercher les notifications à supprimer (pour les renvoyer en données JSON et les enlever dynamiquement)
            $sql = $conn->prepare("SELECT nc.id FROM NotificationClient nc INNER JOIN TransactionBancaire tb ON tb.id=nc.idTransaction 
            WHERE tb.enAttente=0 AND nc.CompteId = '$idUtilisateur';");
            $sql->execute();
            $notifications = $sql->fetchAll(PDO::FETCH_ASSOC);

            //Faire SQL pour supprimer TOUTES les notifications de l'utilisateur (sauf ceux en attente)
            $requete = $conn->prepare("DELETE nc FROM NotificationClient nc
            INNER JOIN TransactionBancaire tb ON tb.id = nc.idTransaction
            WHERE tb.enAttente = 0
            AND nc.CompteId ='$idUtilisateur';");
            $requete->execute();
        }
        //Sinon delete un seul element
        else
        {
            $sql = $conn->prepare("SELECT nc.id FROM NotificationClient nc INNER JOIN TransactionBancaire tb ON tb.id=nc.idTransaction 
            WHERE tb.enAttente=0 AND nc.id = '$idNotif';");
            $sql->execute();
            $notifications = $sql->fetchAll(PDO::FETCH_ASSOC);

            //Faire SQL pour supprimer la notification
            $requete = $conn->prepare("DELETE nc FROM NotificationClient nc
            INNER JOIN TransactionBancaire tb ON tb.id = nc.idTransaction
            WHERE tb.enAttente = 0
            AND nc.id = $idNotif;");
            $requete->execute();
        }
        echo json_encode(["idNotifsEffacees" => $notifications]);
    }
?>