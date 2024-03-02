<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentification</title>
    <link rel="stylesheet" href="./styles/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
<main>
    <section>
        <div class="main-container">
            <div class="illustration-container">
                <div class="illustration-header">
                    <h1>Vous êtes nouveau ?</h1>

                    <p>Rejoignez notre communauté de clients satisfaits, 
                        et plongez dans l'aventure bancaire unique de Fish&Fric
                        dès aujourd'hui.</p>
                </div>

                <button class="btn-connecter" onclick="window.location.href='../Creer_un_compte/creerCompte.php'"><a>Créer un compte</a></button>
            </div>

            <div class="connexion-container">
                <div class="connexion-header">
                    <h1>Page de connexion</h1>

                    <p>Heureux de vous revoir chère Fisheur ! 🐳</p>
                </div>

                <div class="connexion-formulaire">
                    <form action="page_connexion.php" method="post" class="formulaire">
                        <div class="input-box">
                            <div class="input-field">
                                <i class='bx bxs-user'></i>
                                <input type="text" name="courriel" placeholder="..." required>
                                <label for="courriel">Adresse courriel</label>
                            </div>
    
                            <div class="input-field">
                                <i class='bx bxs-lock'></i>
                                <input type="password" name="password" placeholder="..." required>
                                <label for="password">Mot de passe</label>
                            </div>

                            <div class="remember-box">
                                <label for="remember_account"><input type="checkbox" name="remember_account" id="remember_account" class="remember">
                                    Rester connecté pendant 30 jours</label>
                            </div>

                            <div id="erreur-message"></div>
                        </div>

                        <div class="btn-box">
                            <button type="submit" class="btn" >Connexion</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>
</body>
</html>

<?php

if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST')
{
    //Connection a la base de donnee
    try {
        require("../connexion.php");
    } catch(Exception $e) {
        die("Connection echouee : " . $e->getMessage());
    }

    //Inclure fichier qui contient la fonction de décryption
    include "../Encryption/encryption.php";

    $courriel = $_POST['courriel'];
    $password = $_POST['password'];

    //Vérifier si le numéro de compte existe dans la BD
    $requete = "SELECT * FROM Compte WHERE courriel = '$courriel'";
    $resultat = $conn->query($requete);

    //Si aucun utilisateur avec le courriel fourni existe 
    if ($resultat->rowCount() == 0) {
        //On affiche l'erreur que l'utilisateur est inexistant
        echo "<script>";
        echo "let erreurDiv = document.getElementById('erreur-message');";
        echo 'erreurDiv.innerHTML = "<p>L\'utilisateur saisi n\'existe pas!</p>";';
        echo '</script>';  
    }

    else {
        //Aller chercher le mot de passe dans la base de données correspondant au courriel
        $requete = "SELECT motDePasse FROM Compte WHERE courriel = '$courriel'";
        $resultat = $conn->query($requete);
        $resultat = $resultat->fetchColumn();

        //Verfie si le mot de passe saisi correspond au mot de passe hashed de la BD
        if(AES256CBC_decrypter($resultat, CLE_ENCRYPTION) == $password)
        {
            //Si le mot de passe est bon, on envoie l'utilisateur vers la page de ses comptes et commence sa session
            //D'abord chercher l'ID de l'utilisateur
            $requete = "SELECT id FROM Compte WHERE courriel LIKE '$courriel'";
            $resultat = $conn->query($requete);
            $id = $resultat->fetchColumn();

            session_start();
            $_SESSION["utilisateur"] = $id;
            header("Location: ../Liste_compte/listeCompte.html");
            exit(); 
        } 
        else {
            //On Affiche l'erreur de mot de passe
            echo "<script>";
            echo "let erreurDiv = document.getElementById('erreur-message');";
            echo 'erreurDiv.innerHTML = "<p style=\'red\'>Le mot de passe est erroné</p>";';
            echo '</script>';    
        }
    }
}
?>