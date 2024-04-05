<?php
session_start();

//Vérifier que la session de l'utilisateur est en cours
if (isset($_SESSION['utilisateur']) && isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) < $_SESSION['DUREE_SESSION']) {
    //Si c'est le cas, le rediriger directement sur sa liste de comptes, et mettre à jour le moment de dernière activité
    $_SESSION['LAST_ACTIVITY'] = time();
    header("Location: ../Liste_compte/listeCompte.php");
    exit(); 
}

//Sinon, afficher le formulaire de connexion
?>

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
                                <input type="text" id="courriel" placeholder="..." required>
                                <label for="courriel">Adresse courriel</label>
                            </div>
    
                            <div class="input-field">
                                <i class='bx bxs-lock'></i>
                                <input type="password" id="password" placeholder="..." required>
                                <label for="password">Mot de passe</label>
                            </div>

                            <div class="remember-box">
                                <label for="remember_account"><input type="checkbox" name="checkbox" id="remember_account" class="remember">
                                    Garder la session active pendant 8 heures</label>
                            </div>

                            <div id="erreur-message"></div>
                        </div>
                    </form>

                    <div class="btn-box" >
                        <button id="btnConnexion" class="btn">Connexion</button>
                    </div>

                    <div id="toastBox"></div>
                </div>
            </div>
        </div>
    </section>
</main>
</body>
<script src="./scripts/verifConnexion.js"></script>
</html>

<?php
//Si la session de la personne a expiré, l'afficher
if (isset($_SESSION["SESSION_EXPIRED"])) {
    echo "<script>";
    echo "document.getElementById('erreur-message').innerHTML = '<p style=\"color:red;\">Votre session a expiré!</p>';";
    echo '</script>';    

    $_SESSION = [];
}
?>