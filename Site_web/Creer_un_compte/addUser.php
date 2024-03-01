<?php

    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == 'POST') {
        //Connection a la base de donnee
        try{
            require("../connexion.php");
        }
        catch(Exception $e)
        {
            die("Connection echouee : " . $e->getMessage());
        }

        //Gestion d'erreurs
        $erreurs = array();

        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $telephone = trim($_POST['telephone']);
        $courriel = trim($_POST['courriel']);
        $password = trim($_POST['password']);
        $confirmation_mdp = trim($_POST['conf_password']);

        if(empty($prenom) || is_numeric($prenom))
            $erreurs[] = "Le prenom saisi est invalide <br>";

        if(empty($nom) || is_numeric($nom))
            $erreurs[] = "Le nom saisi est invalide <br>";

        if(empty($telephone) || !is_numeric($telephone) || strlen($telephone) != 10) 
            $erreurs[] = "Le numéro de téléphone saisi est invalide <br>";
        else {
            //Vérifier si le numéro est déjà dans la base de données
            $requete = $conn->prepare("SELECT * FROM Compte WHERE telephone = $telephone");
            $requete->execute();
    
            if($requete->rowCount() != 0)
                $erreurs[] = "Le numéro de téléphone est déjà utilisé"; 
        }

        //Vérifier si le mot de passe est dans les critères
        if(strlen($password) < 8 || !preg_match("/[a-z]/", $password) 
        || !preg_match("/[A-Z]/", $password) || !preg_match("/[0-9]/", $password) 
        || !preg_match("/[@.#$%^&*!]/", $password)) 

                $erreurs[] = "Le mot de passe est invalide! Il doit contenir:<br>" . 
                "- Au moins 8 caractères<br>" . 
                "- Au moins 1 lettre majuscule<br>" . 
                "- Au moins 1 lettre minuscule<br>" . 
                "- Au moins 1 caractère spécial<br>";
        
        //Vérifier si les 2 mots de passe sont identiques
        if($password != $confirmation_mdp) 
            $erreurs[] = "Les mots de passe ne sont pas identiques! <br>";

        //Verifie si courriel suit le bon format
        if(!filter_var($_POST['courriel'], FILTER_VALIDATE_EMAIL))
            $erreurs[] = "Le courriel saisi n'est pas valide <br>";
        else {
            //Verifier si courriel existe deja 
            $requete = $conn->prepare("SELECT * FROM Compte WHERE courriel = '$courriel'");
            $requete->execute();

            if($requete->rowCount() != 0)
                $erreurs[] = "Le courriel est déjà utilisé";
        }

        //Si tout est valide, ajouter utilisateur a la base de données
        if(count($erreurs) == 0)
        {
            //Hash le mot de passe
            $password = password_hash($password, PASSWORD_DEFAULT);

            //Effectuer la requête
            $requete = $conn->prepare("INSERT INTO Compte (courriel, prenom, nom, motDePasse, telephone) VALUES ('$courriel', '$prenom', '$nom', '$password', $telephone)");
            $requete->execute();

            //Afficher page de connexion et message de succès
            $messageSucces = "L'utilisateur a été créé avec succès!";
            setcookie('success_message', $messageSucces, time() + 10, '/');
            header("Location: ../Connexion/page_connexion.php");
        }

        //Sinon, on affiche les erreurs
        else
        {
            include "./creerCompte.html";
            echo '<script>';
            echo 'let erreurDiv = document.getElementById("erreurs-messages");';
            echo 'if(erreurDiv){';
                for($i = 0; $i < count($erreurs); $i++)
                {
                    echo 'erreurDiv.innerHTML += "<p style=\'color:red\'>'. $erreurs[$i] . '</p>";';
                }
                echo '}';
                echo '</script>';
        }
    }

?>