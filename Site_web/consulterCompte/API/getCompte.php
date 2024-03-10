<?php 
    //Récupérer le point de terminaison
    if(isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['compteId'])) {
        //Gérer la connexion à la base de données
        try {
            require "../../connexion.php";
        } catch(Exception $e) {
            die("Connexion échouée!: " .$e->getMessage());
        }

        //Chercher le compte en question avec l'ID de la route
        $compteId = $_GET['compteId'];

        //Requête SQL pour chercher le compte que l'on désire consulter
        $requete = $conn->prepare("SELECT id, solde, typeCompte, interet, ouverture 
        FROM CompteBancaire WHERE id='$compteId';");
        $requete->execute();
        $compte = $requete->fetch();

        //Chercher les transactions de ce compte
        $requete = $conn->prepare("SELECT montant, typeTransaction, dateTransaction, nomEtablissement, idCompteBancaireProvenant, idCompteBancaireRecevant FROM TransactionBancaire
        WHERE idCompteBancaireProvenant='$compteId' OR idCompteBancaireRecevant='$compteId';");
        $requete->execute();
        $transactions = $requete->fetchAll(PDO::FETCH_ASSOC);

        // Échapper les caractères spéciaux 
        $compte['id'] = htmlspecialchars($compte['id'], ENT_QUOTES, 'UTF-8');
        $compte['solde'] = htmlspecialchars($compte['solde'], ENT_QUOTES, 'UTF-8');
        $compte['typeCompte'] = htmlspecialchars($compte['typeCompte'], ENT_QUOTES, 'UTF-8');
        $compte['interet'] = htmlspecialchars($compte['interet'], ENT_QUOTES, 'UTF-8');
        $compte['ouverture'] = htmlspecialchars($compte['ouverture'], ENT_QUOTES, 'UTF-8');

        foreach ($transactions as $transaction) {
            $transaction['montant'] = htmlspecialchars($transaction['montant'], ENT_QUOTES, 'UTF-8');
            $transaction['typeTransaction'] = htmlspecialchars($transaction['typeTransaction'], ENT_QUOTES, 'UTF-8');
            $transaction['dateTransaction'] = htmlspecialchars($transaction['dateTransaction'], ENT_QUOTES, 'UTF-8');
            $transaction['nomEtablissement'] = htmlspecialchars($transaction['nomEtablissement'], ENT_QUOTES, 'UTF-8');
            $transaction['idCompteBancaireProvenant'] = htmlspecialchars($transaction['idCompteBancaireProvenant'], ENT_QUOTES, 'UTF-8');
            $transaction['idCompteBancaireRecevant'] = htmlspecialchars($transaction['idCompteBancaireRecevant'], ENT_QUOTES, 'UTF-8');
        }

        $json = [
            'compte' => $compte,
            'transactions' => $transactions 
        ];
    

        //Encoder les informations des comptes en json
        echo json_encode($json);
    }
?>