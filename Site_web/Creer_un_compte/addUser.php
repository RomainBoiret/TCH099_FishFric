<?php

    //Connection a la base de donnee
    try{
        require("connexion.php");
    }
    catch(Exception $e)
    {
        die("Connection echouee : " . $e->getMessage());
    }
    echo '<h1>Connection reussie</h1>';
    //Gestion d'erreurs
    $erreurs = array();

    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $courriel = $_POST['courriel'];
    $password = $_POST['password'];
    $confirmation_mdp = $_POST['confirmation'];

    if(empty($_POST['prenom']) || is_numeric($_POST['prenom']))
    {
        $erreurs[] = "Le prenom saisi est invalide <br>";
    }

    if(empty($_POST['nom']) || is_numeric($_POST['nom']))
    {
        $erreurs[] = "Le nom saisi est invalide <br>";
    }

    //Verifie si courriel suit le bon format
    if(!filter_var($_POST['courriel'], FILTER_VALIDATE_EMAIL))
    {
        $erreurs[] = "Le courriel saisi n'est pas valide <br>";
    }

    //Vérifier si le mot de passe est dans les critères
    if(strlen($password) < 8 || !preg_match("/[a-z]/", $password) 
    || !preg_match("/[A-Z]/", $password) || !preg_match("/[0-9]/", $password) 
    || !preg_match("/[@.#$%^&*!]/", $password)) {

            $erreurs[] = "Le mot de passe est invalide <br>";
    }
    
    //Vérifier si les 2 mots de passe sont identiques
    if($password != $confirmation_mdp) {
        $erreurs[] = "Les mots de passe ne sont pas identiques! <br>";
    }


    //Verifier si courriel existe deja 
    $requete = "SELECT * FROM Compte WHERE courriel = '$courriel'";
    $resultat = $conn->query($requete);

    if($resultat->rowCount() != 0)
    {
        $erreurs[] = "Le courriel est deja utilise";
    }

    //Si tout est valide ajouter utilisateur a la base de donnee
    if(count($erreurs) == 0)
    {
        $nom = trim($nom);
        $prenom = trim($prenom);
        $courriel = trim($courriel);
        $password = trim($password);

        //Hash le mot de passe
        $password = password_hash($password, PASSWORD_DEFAULT);

        $requete = "INSERT INTO Compte (courriel, prenom, nom, motDePasse) VALUES ('$courriel', '$prenom', '$nom', '$password')";

        $requete = $conn->prepare($requete);
        $requete->execute();

    }
    else
    {
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

?>