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

                <button class="btn-connecter"><a href="#">Créer un compte</a></button>
            </div>

            <div class="connexion-container">
                <div class="connexion-header">
                    <h1>Page de connexion</h1>

                    <p>Heureux de vous revoir chère Fisheur ! 🐳</p>
                </div>

                <div class="connexion-formulaire">
                    <form action="" method="post" class="formulaire">
                        <div class="input-box">
                            <div class="input-field">
                                <i class='bx bxs-user'></i>
                                <input type="text" name="courriel" placeholder="..." required>
                                <label for="courriel">Adresse courriel</label>
                            </div>
    
                            <div class="input-field">
                                <i class='bx bxs-lock'></i>
                                <input type="password" name="mot_de_passe" placeholder="..." required>
                                <label for="mot_de_passe">Mot de passe</label>
                            </div>

                            <div class="remember-box">
                                <label for="remember_account"><input type="checkbox" name="remember_account" id="remember_account" class="remember">
                                    Rester connecté pendant 30 jours</label>
                            </div>

                            <div id="erreur-message"></div>
                        </div>

                        <div class="btn-box">
                            <button type="submit" class="btn">Connexion</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>
<script>
    //Chercher les erreurs depuis la variable de session
    let errorMessage = "<?php echo isset($_SESSION['erreur']) ? $_SESSION['erreur'] : ''; ?>";

    //S'il y a des erreurs, les afficher dans le div
    if (errorMessage) {
        let errorDiv = document.getElementById("erreur-message");
        errorDiv.innerHTML = "<p style='color:red'>" + errorMessage + "</p>";
    }

    //Vider la variable de session d'erreurs
    <?php unset($_SESSION['erreur']); ?>

</script>
</body>
</html>