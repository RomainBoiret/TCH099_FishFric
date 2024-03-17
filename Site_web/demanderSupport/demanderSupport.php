<?php
session_start();

//Vérifier si l'utilisateur a une session active
include "../verifSession.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assistance Nautico</title>
    <link rel="stylesheet" href="/demanderSupport/styles/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<!-- header design -->
<header>
    <a href="/Liste_compte/listeCompte.php"  class="logo">
        <img src="/Images/logo-website.jpg">
        <div>Fish<span>&</span>Fric</div>
    </a>

    <button class="btn-deconnexion" onclick="window.location.href='../deconnexion.php'"><i class='bx bx-log-out-circle'></i><a>Se déconnecter</a></button>
</header>

<!-- assistance design -->
<section>
    <div class="main-container">
        <div class="recherche-container">
            <form action="" class="search-bar">
                <input type="text" placeholder="effectuer une recherche" name="q">
                <button type="submit"><i class='bx bx-search'></i></button>
            </form>
        </div>

        <div class="assistance-container">
            <div class="assistance-left-part">
                <div class="assistance-illustration">
                    <h1>Assistance Nautico</h1>
                </div>

                <div class="assistance-title">
                    <h2>Besoin d'aide ?</h2>
                    <p>Vous rencontrez un problème ou vous avez des intérogations ?</p>
                    <p>Posez vos questions à notre ChatBot ou contactez un technicien !</p>
                </div>

                <div class="btn-box">
                    <button class="btn-faq">FAQ</button>
                    <button onclick="togglePopupContacterSupport()" class="btn-contacter">Contacter</button>
                </div>
            </div>

            <div class="assistance-right-part">
                <div class="template-message">
                    
                </div>

                <div class="template-message-footer">
                    <form action="" class="message-bar">
                        <input type="text" placeholder="Poser une question..." name="p">
                        <button onclick="" type="submit"><i class='bx bx-paper-plane'></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ------------------------------------POPUP Contacter technicien support------------------------------------ -->
    <div class="popup" id="popup">
        <div class="overlay"></div>

        <div class="content">
            <div class="close-btn" onclick="togglePopupContacterSupport()"><i class='bx bx-x'></i></div>
            <h1>Contacter un support</h1>

            <div class="main-content-part">
                <form action="#">
                    <div class="input-box">
                        <div class="input-field">
                            <input type="text" placeholder="Sujet du problème" required>
                            <span class="focus"></span>
                        </div>
                    </div>
            
                    <div class="textarea-field">
                        <textarea name="" id="" cols="30" rows="10" placeholder="Décrivez votre problème" required></textarea>
                        <span class="focus"></span>
                    </div>
                </form>
            </div>
            
            <div class="btn-box">
                <button class="btn-envoyer" id="btnContacterSupport">Envoyer</button>
            </div>
        </div>
    </div>
</section>
<main>
    <script src="/demanderSupport/scripts/script.js"></script>
</body>
</html>