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
    <title>Liste Compte</title>
    <link rel="stylesheet" href="/Liste_compte/styles/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    
<!-- header design -->
<header>
    <a href="#" class="logo">
        <img src="/Images/logo-website.jpg">
        <div>Fish<span>&</span>Fric</div>
    </a>

    <button class="btn-deconnexion" onclick="window.location.href='../deconnexion.php'"><i class='bx bx-log-out-circle'></i><a>Se déconnecter</a></button>
</header>
<main>

<!-- compte design -->
<section>
    <div class="main-container">
        <div class="main-compte">
            <div class="header-compte">
                <i class='bx bx-chevron-right'></i>
                <h4>Menu des Comptes</h4>
            </div>

            <div id="compte-content">
                <div class="compte-box">
                    <div class="box-header">
                        <h2>Compte chèque</h2>

                        <div class="montant-compte">
                            <div class="montant">4,567.89</div>
                        </div>
                    </div>

                    <p>Numéro de compte: 11-105-4528</p>

                    <div class="btn-menu">
                        <i class='bx bxs-right-arrow-circle'></i><a href="#">Détails du compte</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="main-side-bar">
            <div class="header-nav">
                <div class="title-header-nav">
                    <i class='bx bx-chevron-right'></i>
                    <h4>Effectuer une opération</h4>
                </div>
                
                <nav class="navigation">
                    <div onclick="togglePopup()" class="btn"><i class='bx bx-transfer-alt'></i>Virer entre comptes</div>
                    <button class="btn"><i class='bx bx-group'></i>Virer entre personnes</button>
                    <button class="btn"><i class='bx bx-money-withdraw'></i>Payer une facture</button>
                </nav>
            </div>

            <div class="footer-nav">
                <div class="title-footer-nav">
                    <i class='bx bx-chevron-right'></i>
                    <h4>Centre d'assistance</h4>
                </div>

                <nav class="navigation-footer">
                    <button class="btn"><i class='bx bx-chat'></i>Messages</button>
                    <button class="btn"><i class='bx bx-add-to-queue' ></i>Ajouter un compte</button>
                    <button class="btn"><i class='bx bx-cog' ></i>Préférences de compte</button>
                </nav>

                <div class="assistance-part">
                    <div class="assistance-nav">
                        <h5>Assistance Nautico</h5>

                        <p>Avez-vous des questions ?</p>

                        <button class="btn"><a href="#">Nous contacter</a></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="popup" id="popup-1">
        <div class="overlay"></div>

        <div class="content">
            <div class="close-btn" onclick="togglePopupentreCompte()"><i class='bx bx-x'></i></div>
            <h1>Virement entre comptes</h1>
            
            <div class="main-content-part">
                <div class="virement-entre-compte">
                    <div class="virement-option">
                        <p>De:</p>

                        <div class="dropdown">
                            <div class="select">
                                <span class="selected">Compte chèque</span>
                                <div class="caret"></div>
                            </div>
                            <ul class="menu">
                                <li class="active">Compte chèque</li>
                                <li>Compte épargne</li>
                                <li>Carte de Crédit</li>
                                <li>Compte d'investissement</li>
                            </ul>
                        </div>

                        <div class="solde">1,524.47 <i class='bx bx-dollar'></i></div>
                    </div>

                    <div class="virement-option">
                        <p>Vers:</p>

                        <div class="dropdown">
                            <div class="select">
                                <span class="selected">Compte épargne</span>
                                <div class="caret"></div>
                            </div>
                            <ul class="menu">
                                <li class="active">Compte épargne</li>
                                <li>Compte chèque</li>
                                <li>Carte de Crédit</li>
                                <li>Compte d'investissement</li>
                            </ul>
                        </div>

                        <div class="solde">2,854.12 <i class='bx bx-dollar'></i></div>
                    </div>

                    <div class="transfert-montant">
                        <div class="input-box">
                            <p>Montant:</p>

                            <div class="input-label">
                                <input type="text">
                            </div>

                            <i class='bx bx-dollar'></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="btn-box">
                <button class="btn-virer">Virer</button>
            </div>
        </div>
    </div>

    <div class="popup" id="popup-2">
        <div class="overlay"></div>

        <div class="content">
            <div class="close-btn" onclick="togglePopupentrePersonne()"><i class='bx bx-x'></i></div>
            <h1>Virement entre comptes</h1>
        </div>
    </div>
</section>
</main>

<!-- footer design -->
<footer>

</footer>
</body>
<script src="/Liste_compte/scripts/getComptes.js"></script>
</html>