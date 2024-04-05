<!DOCTYPE html>
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
        <div class="create-account-container">
            <div class="create-account-header">
                <h1>Créer un compte Fish&Fric</h1>
            </div>

            <div class="create-account-embed">
                <h2>Compte courant (compte chèques)</h2>

                <p>Notre compte courant est parfait pour faire vos opérations de tous les jours.
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
                                <label for="conf_password">Confirmation du mot de passe</label>
                            </div>
                        </div>
                    </div>

                    <!-- <div id="messages">
                        <div id="erreur-mdp"></div>
                        <div id="erreurs-reste"></div>
                    </div>
                    
                    <div id="msg-succes"></div> -->

                    <div id="toastBox"></div>
                </form>

                <div class="btn-box">
                    <button class="btn" id="btnCreerCompte">Créer</button>
                </div>
            </div>
        </div>

        <div class="illustration-container">
            <h1>Vous êtes déjà membre ?</h1>

            <button class="btn-connecter"  onclick="window.location.href='../Connexion/page_connexion.php'"><a>Se connecter</a></button>
        </div>
    </div>
</section>
</main>
</body>
<script src="./scripts/verifCreerCompte.js"></script>
</html>