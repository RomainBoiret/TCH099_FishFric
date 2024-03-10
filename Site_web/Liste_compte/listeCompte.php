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

<!-- ------------------------------------MENU COMPTES + MENU DROIT DE LA PAGE------------------------------------ -->
<section>
    <div class="main-container">
        <div class="main-compte">
            <div class="header-compte">
                <i class='bx bx-chevron-right'></i>
                <h4>Menu des Comptes</h4>
            </div>

            <div id="compte-content">
                <div class="compte-box">
                    
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
                    <div onclick="togglePopupentreCompte()" class="btn" id="btnPopupComptes"><i class='bx bx-transfer-alt'></i>Virer entre comptes</div>
                    <div onclick="togglePopupentrePersonne()" class="btn" id="btnPopupPersonnes"><i class='bx bx-group'></i>Virer entre personnes</div>
                    <div onclick="togglePopupFacture()" class="btn" id="btnPopupFacture"><i class='bx bx-money-withdraw'></i>Payer une facture</div>
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

    <!-- ------------------------------------POPUP TRANSFERT ENTRE COMPTES------------------------------------ -->
    <div class="popup" id="popup-1">
        <div class="overlay"></div>

        <div class="content">
            <div class="close-btn" onclick="togglePopupentreCompte()"><i class='bx bx-x'></i></div>
            <h1>Virement entre comptes</h1>
            
            <div class="main-content-part">
                <div class="virement-entre-compte">
                    <table>
                        <tbody id="tableVirementComptes">
                            <tr>
                                <th>De</th>
                                <th>Vers</th>
                                <th>Compte et descriptif</th>
                                <th>Solde ($)</th>
                            </tr>
                        </tbody>    
                    </table>
                </div>
            </div>

            <div class="transfert-montant">
                <div class="input-box">
                    <p>Montant:</p>

                    <div class="input-label">
                        <input type="text" id="montant-virement-comptes">
                        <i class='bx bx-dollar'></i>
                    </div>
                </div>
            </div>

            <div id="msg-erreur-virement-compte"></div>

            <div class="btn-box">
                <button class="btn-virer" id="btnVirerCompte">Virer</button>
            </div>
        </div>
    </div>

    <!-- ------------------------------------POPUP VIREMENT ENTRE PERSONNES------------------------------------ -->
    <div class="popup" id="popup-2">
        <div class="overlay"></div>

        <div class="content-2">
            <div class="close-btn" onclick="togglePopupentrePersonne()"><i class='bx bx-x'></i></div>
            <h1>Virement entre personnes</h1>

            <div class="main-content-part">
                <div class="virement-entre-personne">
                    <table>
                        <tbody id="tableVirementPersonnes">
                            <tr>
                                <th>De</th>
                                <th>Compte et descriptif</th>
                                <th>Solde ($)</th>
                            </tr>
                        </tbody>
                    </table>
                    <div class="virement-formulaire">
                        <form action="" method="post" class="formulaire">
                            <div class="input-box">
                                <div class="input-field">
                                    <input type="text" name="courriel_dest" placeholder="..." id="courrielDest" required>
                                    <label for="courriel_dest">Courriel du destinataire</label>
                                </div>

                                <div class="input-field">
                                    <input type="text" name="quest_rep" placeholder="..." id="reponse" required>
                                    <label for="quest_rep">Réponse</label>
                                </div>
                            </div>

                            <div class="input-box">
                                <div class="input-field">
                                    <input type="text" name="quest_secu" placeholder="..." id="question" required>
                                    <label for="quest_secu">Question de sécurité</label>
                                </div>

                                <div class="input-field">
                                    <input type="text" name="conf_quest_rep" placeholder="..." id="confReponse" required>
                                    <label for="conf_quest_rep">Confirmer la réponse</label>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="transfert-montant">
                        <div class="input-box">
                            <p>Montant:</p>

                            <div class="input-label">
                                <input type="text" id="montant-virement-personne">
                                <i class='bx bx-dollar'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="msg-erreur-virement-personne"></div>
            
            <div class="btn-box">
                <button class="btn-virer" id="btnVirerPersonne">Virer</button>
            </div>
        </div>
    </div>

    <!-- ------------------------------------POPUP PAYER FACTURE------------------------------------ -->
    <div class="popup" id="popup-3">
        <div class="overlay"></div>

        <div class="content-2">
            <div class="close-btn" onclick="togglePopupFacture()"><i class='bx bx-x'></i></div>
            <h1>Payer une facture</h1>

            <div class="main-content-part">
                <div class="payer-facture">
                    <table>
                        <tbody id="tableFacture">
                            <tr>
                                <th>De</th>
                                <th>Compte et descriptif</th>
                                <th>Solde ($)</th>
                            </tr>
                        </tbody>
                    </table>
                    <div class="virement-formulaire">
                        <form action="" method="post" class="formulaire">
                            <div class="input-box">
                                <div class="input-field">
                                    <input type="text" name="nomEtablissement" placeholder="..." id="nomEtablissement" required>
                                    <label for="etablissement">Établissement</label>
                                </div>
                            </div>

                            <div class="input-box">
                                <div class="input-field">
                                    <input type="text" name="facture_raison" placeholder="..." id="facture_raison" required>
                                    <label for="facture_raison">Raison</label>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="transfert-montant">
                        <div class="input-box">
                            <p>Montant:</p>

                            <div class="input-label">
                                <input type="text" id="montant-payer-facture">
                                <i class='bx bx-dollar'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="msg-erreur-payer-facture"></div>
            
            <div class="btn-box">
                <button class="btn-virer" id="btnPayerFacture">Payer</button>
            </div>
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