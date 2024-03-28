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

        //Vérifier qu'il y a un montant
        if (isset($donnees['montant']) &&is_numeric($donnees['montant'])) {
            $montant = intval(htmlspecialchars(trim($donnees['montant'])));
        } else {
            $erreurs[] = "Veuillez inscrire un montant";
        }

        //Vérifier qu'il y a l'ID de l'utilisateur
        if (isset($donnees['utilisateur'])) {
            $idUtilisateur = trim(implode($donneesJSON['utilisateur']));
        }

        if(empty($erreurs)) {
            //Vérifier que le courriel est un utilisateur de la banque
            $requete = $conn->prepare("SELECT * FROM Compte WHERE courriel = '$courrielDest'");
            $requete->execute();
        }

    }