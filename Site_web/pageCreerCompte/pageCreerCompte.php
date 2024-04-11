<!DOCTYPE html>
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
        <div class="create-account-container">
            <div class="create-account-header">
                <h1>Créer un compte Fish&Fric</h1>
            </div>

            <div class="create-account-embed">
                <h2>Compte chèques</h2>

                <p>Notre compte chèque est parfait pour faire vos opérations de tous les jours.
                    Vous pourrez ajouter d'autres types de compte ultérieurement.</p>
            </div>

            <div class="account-formulaire">
                <form class="formulaire">
                    <div class="input-content">
                        <div class="input-box">
                            <div class="input-field">
                                <i class='bx bxs-user'></i>
                                <input type="text" id="nom" placeholder="..." required>
                                <label for="nom">Nom de famille</label>
                            </div>
    
                            <div class="input-field">
                                <i class='bx bx-user' ></i>
                                <input type="text" id="prenom" placeholder="..." required>
                                <label for="prenom">Prénom</label>
                            </div>
    
                            <div class="input-field">
                                <i class='bx bxs-phone'></i>
                                <input type="text" id="telephone" placeholder="..." required>
                                <label for="telephone">Numéro de téléphone</label>
                            </div>
                        </div>
                
                        <div class="input-box">
                            <div class="input-field">
                                <i class='bx bxs-envelope'></i>
                                <input type="text" id="courriel" placeholder="..." required>
                                <label for="courriel">Adresse courriel</label>
                            </div>
    
                            <div class="input-field">
                                <i class='bx bxs-lock'></i>
                                <input type="password" id="password" placeholder="..." required>
                                <label for="password">Mot de passe</label>
                            </div>
    
                            <div class="input-field">
                                <i class='bx bxs-lock'></i>
                                <input type="password" id="conf_password" placeholder="..." required>
                                <label for="conf_password">Confirmer mot de passe</label>
                            </div>
                        </div>
                    </div>

                    <div id="toastBox"></div>
                </form>

                <div class="btn-box">
                    <button class="btn" id="btnCreerCompte">Créer</button>
                </div>
            </div>
        </div>

        <div class="illustration-container">
            <h1>Vous êtes déjà membre ?</h1>

            <button class="btn-connecter"  onclick="window.location.href='../pageConnexion/pageConnexion.php'"><a>Se connecter</a></button>
        </div>
    </div>
</section>
</main>
</body>
<script src="../pageAccueil/scripts/script.js"></script>
<script src="./scripts/verifCreerCompte.js"></script>
</html>