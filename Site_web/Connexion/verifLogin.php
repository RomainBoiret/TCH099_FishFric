<?php

if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST')
{
    //Connection a la base de donnee
    try {
        require("../connexion.php");
    } catch(Exception $e) {
        die("Connection echouee : " . $e->getMessage());
    }

    //Démarrer la session 
    session_start();
    
    //Gestion d'erreurs
    $erreurs = array();

    $id = $_POST['id'];
    $password = $_POST['password'];

    //Vérifier si le numéro de compte existe dans la BD
    $requete = "SELECT * FROM Compte WHERE id = '$id'";
    $resultat = $conn->query($requete);

    //Si aucun utilisateur avec le id fourni existe 
    if ($resultat->rowCount() == 0) {
        //On stocke le message d'erreur et on rafraîchit l'écran de connexion
        $_SESSION['erreur'] = "L'utilisateur saisi n'existe pas!";
    }

    else {
        //Aller chercher le mot de passe dans la base de données
        $requete = "SELECT motDePasse FROM Compte WHERE id = '$id'";
        $resultat = $conn->query($requete);
        $resultat = $resultat->fetchColumn();

        //Verfie si le mot de passe saisi correspond au mot de passe hashed de la BD
        if(password_verify($password, $resultat))
        {
            //Debut de la session de l'utilisateur
            $_SESSION["utilisateur"] = $id;
            header("Location: ../forum.php"); //------METTRE LE LIEN DE LA PAGE PRINCIPALE DU COMPTE
            exit(); 
        } 
        else {
            //On stocke le message d'erreur et on rafraîchit l'écran de connexion
            $_SESSION['erreur'] = "Le mot de passe est erroné!";
        }
    }

    //Rafficher le formulaire de connexion s'il y a eu une erreur
    include "./page_connexion.php";
}