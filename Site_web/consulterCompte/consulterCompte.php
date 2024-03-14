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
    <title>Consulter Compte</title>
    <link rel="stylesheet" href="/consulterCompte/styles/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <!-- header design -->
<header>
    <a href="/Liste_Compte/listeCompte.php" class="logo">
        <img src="/Images/logo-website.jpg">
        <div>Fish<span>&</span>Fric</div>
    </a>

    <button class="btn-deconnexion" onclick="window.location.href='../../deconnexion.php'"><i class='bx bx-log-out-circle'></i><a>Se déconnecter</a></button>
</header>
<main>

<!-- Consulter compte design -->
<section>
    <div class="consulter-compte">
        <div class="detail-compte">
            <div class="detail-compte-header">
                <h2>Compte chèque</h2>

                <div class="montant-compte">
                    <div class="montant">4,567.89</div>
                </div>
            </div>

            <p>Numéro de compte: 11-105-4528</p>

            <div class="detail-compte-footer">
                <p>Date d'ouverture: 02/12/2023</p>
            </div>
        </div>

        <div class="historique-compte">
            <div class="title-historique">
                <i class='bx bx-chevron-right'></i>
                <h4>Historique des transactions</h4>
            </div>

            <div class="transfert-content">
            </div>

            <div class="footer-historique">
                <div class="btn-voir-plus"><i class='bx bx-plus'></i></div>
                <p>Voir 5 transactions plus anciennes</p>
            </div>
        </div>
    </div>
</section>
</main>

<footer>

</footer>
<script src="/consulterCompte/scripts/consulterCompte.js"></script>
</body>
</html>