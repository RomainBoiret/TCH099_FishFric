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
        <div class="login-container">
            <div class="form-box">
                <form action="./verifLogin.php" method="post">
                    <h2 class="heading">Page de Connexion</h2>
                    <p>Bon retour dans l'aquarium cher Fisher ! 🐋</p>

                    <div class="input-box">
                        <i class='bx bxs-user'></i>
                        <input type="text" name="id" placeholder="..." required>
                        <label for="username">Nom d'utilisateur</label>
                    </div>
    
                    <div class="input-box">
                        <i class='bx bxs-lock' ></i>
                        <input type="password" name="password" placeholder="..." required>
                        <label for="password">Mot de passe</label>
                    </div>

                    <div class="remember-box">
                        <!-- <label for="remember"><input type="checkbox" name="remember" id="remember" class="remember">
                            Se souvenir de mon nom d'utilisateur</label> -->
                        <label for="remember_account"><input type="checkbox" name="remember_account" id="remember_account" class="remember">
                            Rester connecté pendant 30 jours</label>
                    </div>

                    <div class="btn-box">
                        <button type="submit" class="btn" id="login">Connexion</button>
                    </div>

                    <div class="footer-text">
                        <div id="erreur-message"></div>

                        <script>
                            //Chercher les erreurs depuis la variable de session
                            let errorMessage = "<?php echo isset($_SESSION['erreur']) ? $_SESSION['erreur'] : ''; ?>";
                            let addUserMessage = "<?php echo isset($_COOKIE['success_message']) ? $_COOKIE['success_message'] : ''; ?>";
                    
                            //S'il y a des erreurs, les afficher dans le div
                            let errorDiv = document.getElementById("erreur-message");

                            if (errorMessage) {
                                errorDiv.innerHTML = "<p style='color:red'>" + errorMessage + "</p>";
                            }
                            else if (addUserMessage) {
                                errorDiv.innerHTML += "<p style=\'color:green\'>L\'utilisateur a été crée avec succès!</p>";

                            }
                    
                            //Vider la variable de session d'erreurs
                            <?php unset($_SESSION['erreur']); session_destroy();?>

                        </script>


                        <p>Copyright &copy; 2024 | All Rights Reserved.</p>
                    </div>
                </form>
            </div>
            
            <div class="login-illustration">
                <div class="illustration-create-account">
                    <div class="logo">
                        <img src="../Images/logo-website.jpg">
                        <h3>Fish<span>&</span>Fric</h3>
                    </div>

                    <h2>Tu es nouveau ?</h2>

                    <p>Rejoins notre communauté de clients satisfaits et 
                        plonge dans l'aventure bancaire unique de Fish&Fric dès aujourd'hui !</p>

                    <div class="btn-box">
                        <button type="submit" class="btn" id="create_account" onclick="window.location.href='../Creer_un_compte/creerCompte.html'">Créer un Compte</button>
                    </div>
                </div>

                <div class="illustraction-container"></div>
            </div>
        </div>
    </section>
</main>
    <script src="./scripts/script.js"></script>
</body>
</html>