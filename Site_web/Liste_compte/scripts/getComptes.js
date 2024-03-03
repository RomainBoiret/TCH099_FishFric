document.addEventListener("DOMContentLoaded", function() {

    //--------------------------------------REQUÊTE GET AFFICHER LES COMPTES--------------------------------------
    
    //Créer la requête
    let request = new XMLHttpRequest();

    //Configurer la requête, pour aller chercher les comptes
    request.open('GET', '/Liste_compte/API/afficherComptes.php', true);

    request.onload = function() {
        //Vérifier si la requête a marché
        if (request.readyState === 4 && request.status === 200) {

            //Décoder la réponse (qui est au format JSON)
            let responseData = JSON.parse(request.responseText);

            //Afficher les comptes en HTML
            let comptes = '';
            responseData.comptes.forEach(function(compte) {
                //Mettre tout le code HTML de la structure d'un compte dans une string
                let compteHtml = '<div class="compte-box"><div class="box-header">';
                compteHtml += '<h2>' + compte.typeCompte + '</h2>';
                compteHtml += '<div class="montant-compte">';
                compteHtml += '<div class="montant">' + compte.solde + '</div></div></div>';
                compteHtml += '<p>Numéro de compte: ' + compte.id + '</p>';
                compteHtml += '<div class="btn-menu"><i class="bx bxs-right-arrow-circle">';
                compteHtml += '</i><a href="#">Détails du compte</a></div></div>';                                                              

                comptes += compteHtml;
            });

            //Afficher les comptes dans le div
            document.getElementById('compte-content').innerHTML = comptes;

            //Afficher les comptes de l'utilisateur 
            let menuInitialized = false

            document.getElementById("popupBtn").addEventListener('click', function() {
                comptes = '';
                
                responseData.comptes.forEach(function(compte) {
                    //Afficher les infos de chaque compte dans le menu
                    if(compte.typeCompte == 'Compte chèque') {
                        //Afficher le compte chèque comme sélectionné par défaut
                        compteHtml = '<li class="active" id="' + compte.id + '">' + compte.typeCompte + '</li>';
                        selectedHtml = '<span class="selected" id="' + compte.id + '">Compte chèque</span><div class="caret"></div>';
                    }

                    else
                        compteHtml = '<li id="' + compte.id + '">' + compte.typeCompte + '</li>';
                                                            
                    comptes += compteHtml;
                });

                //Afficher les comptes dans le div de chaque menu dropdown
                document.getElementById('menu-1').innerHTML = comptes;
                document.getElementById('menu-2').innerHTML = comptes;

                //Mettre compte chèque comme sélectionné par défaut
                console.log(selectedHtml)
                document.getElementById('select-1').innerHTML = selectedHtml;
                document.getElementById('select-2').innerHTML = selectedHtml;

                //Ajouter effets de sélection du menu la 1ère fois que la popup est ouverte
                if(!menuInitialized) {
                    console.log("true")
                    gestionDropDownMenu();
                    menuInitialized = true;
                }
            })
        } 
        
        else {
            //Afficher l'erreur s'il y a lieu
            console.error('Request failed with status code:', request.status);
        }
    };

    //Message d'erreur de la requête
    request.onerror = function() {
        console.error('Request failed to reach the server');
    };

    //Envoyer la requête
    request.send();
});

//--------------------------------------AFFICHER la popup "transfert entre personne"--------------------------------------
function togglePopupentreCompte() {
    document.getElementById("popup-1").classList.toggle("active");
}

//Fonction des animations des menus du transfert
function gestionDropDownMenu() {
    const dropdowns = document.querySelectorAll('.dropdown');

    dropdowns.forEach(dropdown => {
    
        const select = dropdown.querySelector('.select');
        const caret = dropdown.querySelector('.caret');
        const menu = dropdown.querySelector('.menu');
        const options = dropdown.querySelectorAll('.menu li');
        const selected = dropdown.querySelector('.selected');
    
        select.addEventListener('click', () => {
            select.classList.toggle('select-clicked');
            caret.classList.toggle('caret-rotate');
            menu.classList.toggle('menu-open');
        });
    
        options.forEach(option => {
    
            option.addEventListener('click', () => {
    
                selected.innerText = option.innerText;
    
                select.classList.remove('select-clicked');
    
                caret.classList.remove('caret-rotate');
    
                menu.classList.remove('menu-open');
                
                options.forEach(option => {
    
                    option.classList.remove('active');
                });
    
                option.classList.add('active');
            });
        });
    });
}

//Fonction enlever la sélection quand le popup se ferme
function removeSelect() {
    divSelect = document.getElementsByClassName("select-clicked");

    for (let i = 0; i < divSelect.length; i++)
        divSelect[i].classList.remove("select-clicked");
}