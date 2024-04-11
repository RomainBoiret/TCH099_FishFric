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
    <title>Fish&Fric - FAQ</title>
    <link rel="stylesheet" href="./styles/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<!-- header design -->
<header>
    <a href="../index.html" class="logo">
        <img src="../imagesCommunes/logo-website.jpg">
        <div>Fish<span>&</span>Fric</div>
    </a>

    <div class="btn-box">
        <button class="btn-deconnexion" onclick="window.location.href='../deconnexion.php'"><i class='bx bx-log-out-circle'></i><a>Se déconnecter</a></button>
    </div>
</header>
<main>

<!-- assistance design -->
<section>
    <div class="main-container">
        <div class="assitance-container">
            <div class="text-container">
                <h1>Besoin d'aide ?</h1>

                <p>Vous rencontrez un problème ou vous avez des intérogations ?</p>
    
                <p>Contactez un tech-Fisher !</p>

                <p>Notre équipe d'assistance traitera votre demande dans les plus brefs délais et vous fournira une réponse ou une solution à votre problème. Nous sommes là pour vous aider à résoudre tout problème ou répondre à toute question que vous pourriez avoir concernant nos services.</p>
    
                <p>Consultez notre FAQ pour vérifier que nous n'ayons pas déjà répondu à votre question !</p>

                <div class="btn-box">
                    <a href="../pageFaq/pageFaq.html" class="btn">Consulter FAQ</a>
                </div>
            </div>
        </div>

        <div class="message-container">
            <form>
                <h2>Contacter un support</h2>
                <div class="input-field">
                    <input id="titreRecu" type="text" placeholder="Intitulé du prolème..." required>
                </div>
        
                <div class="textarea-field">
                    <textarea id="messageRecu" cols="30" rows="10" placeholder="Décrivez votre problème..." required></textarea>
                </div>
        
            </form>

            <div class="btn-box">
                <button type="submit" class="btn" id="soumettre">Soumettre</button>
                <button type="reset" class="btn" id="effacer">Effacer</button>
            </div>

        </div>

        <div id="toastBox"></div>
    </div>
</section>
</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="./scripts/demandeAssistance.js"></script>
</body>
</html>