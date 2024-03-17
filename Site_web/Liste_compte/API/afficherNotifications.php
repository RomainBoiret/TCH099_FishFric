<?php 
    session_start();

    //Récupérer le point de terminaison
    if(isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "GET") {
        //Gérer la connexion à la base de données
        try {
            require "../../connexion.php";
        } catch(Exception $e) {
            die("Connexion échouée!: " .$e->getMessage());
        }

        //Chercher les notifications de l'utilisateur avec la variable de session
        $idUtilisateur = $_SESSION['utilisateur'];

        //Requête SQL pour chercher les notifications
        $requete = $conn->prepare("SELECT *
        FROM NotificationClient nc JOIN TransactionBancaire tb ON tb.id = nc.idTransaction WHERE compteId = $idUtilisateur;");
        $requete->execute();
        $notificationsEtTransactions = $requete->fetchAll(PDO::FETCH_ASSOC);

        // Échapper les caractères spéciaux dans le contenu des notifications
        foreach ($notificationsEtTransactions as $notification) {
            $notification['id'] = htmlspecialchars($notification['id'], ENT_QUOTES, 'UTF-8');
            $notification['CompteId'] = htmlspecialchars($notification['CompteId'], ENT_QUOTES, 'UTF-8');
            $notification['titre'] = htmlspecialchars($notification['titre'], ENT_QUOTES, 'UTF-8');
            $notification['contenu'] = htmlspecialchars($notification['contenu'], ENT_QUOTES, 'UTF-8');
            $notification['lu'] = htmlspecialchars($notification['lu'], ENT_QUOTES, 'UTF-8');
            $notification['dateRecu'] = htmlspecialchars($notification['dateRecu'], ENT_QUOTES, 'UTF-8');

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
?>