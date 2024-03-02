<?php
    //Vérifier que la session de l'utilisateur est en cours
    if (isset($_SESSION['utilisateur']) && isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) < $_SESSION['DUREE_SESSION']) {
        //Si c'est le cas, mettre à jour le temps de la dernière activité
        $_SESSION['LAST_ACTIVITY'] = time();
    } else {
        //Sinon, unset la session et renvoyer l'utilisateur à la page de connexion avec un message
        session_unset();
        $_SESSION['SESSION_EXPIRED'] = "Votre session a expiré!";
        
        header("Location: /Connexion/page_connexion.php");
        exit(); 
    }