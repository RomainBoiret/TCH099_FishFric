<?php
session_start();
//Vérifier que la session de l'utilisateur est en cours
if (!isset($_SESSION["compteSupprime"]) && isset($_SESSION['utilisateur']) && isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) < $_SESSION['DUREE_SESSION']) {
    //Si c'est le cas, le rediriger directement sur sa liste de comptes, et mettre à jour le moment de dernière activité
    $_SESSION['LAST_ACTIVITY'] = time();
    header("Location: ../pageListeCompte/pageListeCompte.php");
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
    <link rel="icon" href="/TCH099_FishFric/Site_web/favicon.ico">
</head>
<body>
<!-- header design -->
<header>
    <a href="../index.html" class="logo">
        <img src="../imagesCommunes/logo-website.jpg">
        <div>Fish<span>&</span>Fric</div>
    </a>

    <div class="bx bx-menu" id="menu-icon"></div>

    <nav class="navigation">
        <a href="../index.html">Accueil</a>
        <a href="../pageFaq/pageFaq.html">Voir la FAQ</a>
        <a href="#">Notre équipe</a>

        <span class="active-nav"></span>
    </nav>
</header>

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
<script src="../pageAccueil/scripts/script.js"></script>
<script src="./scripts/verifConnexion.js"></script>
</html>

<?php
//Si l'utilisateur vient de fermer son compte
if(isset( $_SESSION["compteSupprime"])) {
    echo "<script>";
    echo "let toast = document.createElement('div');
    toast.classList.add('toast');
    toast.classList.add('success');
    toast.innerHTML = '<i class=\"bx bxs-check-circle\"></i>' + 'Votre compte Fish&Fric a bien été supprimé.';
    toastBox.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 3000);";
    echo '</script>';    

    //Vider la session
    $_SESSION = [];
}

//Si la session de la personne a expiré, l'afficher
else if (isset($_SESSION["SESSION_EXPIRED"])) {
    echo "<script>";
    echo "let toast = document.createElement('div');
    toast.classList.add('toast');
    toast.classList.add('error');
    toast.innerHTML = '<i class=\"bx bxs-error-circle\"></i>' + 'Votre session a expiré!';
    toastBox.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 3000);";
    echo '</script>';    

    //Vider la session
    $_SESSION = [];
}
?>