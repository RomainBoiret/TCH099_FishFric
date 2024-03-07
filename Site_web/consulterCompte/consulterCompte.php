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

            <div class="transaction-mois">
                <div class="mois">Mars 2024</div>
                <div class="depenses-mois">+ 123.4<i class='bx bx-dollar'></i></div>
            </div>

            <div class="transfert-content">
                <div class="transfert-box">
                    <div class="transfert-detail">
                        <div class="detail-titre"><span>Transfert</span> / <span>Maman</span></div>
                        <div class="detail-date">24 fév 2024</div>
                    </div>

                    <div class="transfert-montant-positif">+ 50.24<i class='bx bx-dollar'></i></div>
                </div>

                <div class="transfert-box">
                    <div class="transfert-detail">
                        <div class="detail-titre"><span>Paiement de facture</span> / <span>ÉTS</span></div>
                        <div class="detail-date">3 fév 2024</div>
                    </div>

                    <div class="transfert-montant-negatif">-1934.26<i class='bx bx-dollar'></i></div>
                </div>

                <div class="transfert-box">
                    <div class="transfert-detail">
                        <div class="detail-titre"><span>Dépot de chèque</span> / <span>Retour d'impot</span></div>
                        <div class="detail-date">25 déc 2023</div>
                    </div>

                    <div class="transfert-montant-positif">+ 100.00<i class='bx bx-dollar'></i></div>
                </div>

                <div class="transfert-box">
                    <div class="transfert-detail">
                        <div class="detail-titre"><span>Transfert envoyé</span> / <span>Maman</span></div>
                        <div class="detail-date">4 nov 2023</div>
                    </div>

                    <div class="transfert-montant-negatif">- 200.00<i class='bx bx-dollar'></i></div>
                </div>
            </div>

            <div class="footer-historique">
                <div class="btn-voir-plus"><i class='bx bx-plus'></i></div>
                <p>Voir transactions plus anciennes</p>
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