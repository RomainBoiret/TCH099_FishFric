document.addEventListener("DOMContentLoaded", function() {

    //--------------------------------------REQUÊTE GET AFFICHER LES COMPTES--------------------------------------
    
    //Créer la requête
    let requeteGetComptes = new XMLHttpRequest();

    //Configurer la requête, pour aller chercher les comptes
    requeteGetComptes.open('GET', '/Liste_compte/API/afficherComptes.php', true);

    requeteGetComptes.onload = function() {
        //Vérifier si la requête a marché
        if (requeteGetComptes.readyState === 4 && requeteGetComptes.status === 200) {

            //Décoder la réponse (qui est au format JSON)
            let responseData = JSON.parse(requeteGetComptes.responseText);

            //Afficher la liste des comptes de l'utilisateur en HTML
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

            //--------------------------------------AFFICHER COMPTES virement entre comptes--------------------------------------
            document.getElementById("btnPopupComptes").addEventListener('click', function() {
                comptes = '<tr><th>De</th><th>Vers</th><th>Compte et descriptif</th><th>Solde ($)</th></tr>';
                
                responseData.comptes.forEach(function(compte) {
                    //Afficher chaque compte dans le tableau, ajouter le HTML dynamiquement
                    comptes += '<tr><td><input type="radio" name="option-1" id="' + compte.id + '"></td>';
                    comptes += '<td><input type="radio" name="option-2" id="' + compte.id + '"></td>';
                    comptes += '<td><span>' + compte.typeCompte + ' </span>';
                    comptes += '<span>ID: ' + compte.id + ' </span>';
                    comptes += '</td><td><span>' + compte.solde + '</span></td></tr>';               
                });

                //Ajouter le HTML dans la table
                document.getElementById('tableVirementComptes').innerHTML = comptes;

                //--------------------------------------REQUÊTE PUT VIREMENT--------------------------------------
                document.getElementById('btnVirer').addEventListener('click', function() {
                    //Chercher les données à envoyer à la requête
                    let montant = document.getElementById("montant-virement-comptes").value;
                    console.log(montant)
                    let idCompteBancaireProvenant;
                    let idCompteBancaireRecevant;

                    ["option-1", "option-2"].forEach(option => {
                        const selectedOption = document.querySelector(`input[name=${option}]:checked`);
                        if (selectedOption && option == "option-1") {
                            idCompteBancaireProvenant = selectedOption.id;
                        }

                        if (selectedOption && option == "option-2") {
                            idCompteBancaireRecevant = selectedOption.id;
                        }
                    });

                    //S'il n'y a pas 2 options sélectionnées, on arrête et on met un message d'erreur
                    // if (!idCompteBancaireProvenant || !idCompteBancaireRecevant) {
                    //     document.getElementById('msg-erreur-virement-compte').innerText = "Choisissez 1 compte source et un compte de destination";
                    //     return;
                    // }

                    //S'

                    //Sinon tout va bien et on peut commencer notre requête
                    requeteVirement = new XMLHttpRequest();
                    requeteVirement.open('PUT', '/Transfert/API/gestionTransfert.php/compte', true);
                    
                    //Stocke les donnees a envoyer en format JSON
                    requeteVirement.setRequestHeader('Content-Type', 'application/json');
                    const donneesJsonVirement = JSON.stringify({"idCompteBancaireProvenant": idCompteBancaireProvenant,
                                                                "idCompteBancaireRecevant": idCompteBancaireRecevant,
                                                                "montant": montant});

                    console.log("Json: " + donneesJsonVirement)

                    requeteVirement.onload = function() {
                        //Vérifier si la requête a marché
                        if (requeteVirement.readyState === 4 && requeteVirement.status === 200) {
                            //Décoder la réponse (qui est au format JSON)
                            let responseData = JSON.parse(requeteVirement.responseText);

                            //S'il y a une réponse, c'est soit un message d'erreur ou de succès
                            if (responseData) {
                                document.getElementById('msg-erreur-virement-compte').innerText = responseData.erreur;
                            }

                        } 
        
                        else {
                            //Afficher l'erreur s'il y a lieu
                            console.error('Request failed with status code:', requeteVirement.status);
                        }
                    }

                    //Message d'erreur de la requête
                    requeteVirement.onerror = function() {
                        console.error('La requête n\'a pas fonctionné!');
                    };

                    //Envoyer la requête
                    requeteVirement.send(donneesJsonVirement);
                })

            })

            //--------------------------------------AFFICHER COMPTES virement entre personnes--------------------------------------
            //...



        } 
        
        else {
            //Afficher l'erreur s'il y a lieu
            console.error('Request failed with status code:', requeteGetComptes.status);
        }
    };

    //Message d'erreur de la requête
    requeteGetComptes.onerror = function() {
        console.error('La requête n\'a pas fonctionné!');
    };

    //Envoyer la requête
    requeteGetComptes.send();
});

//--------------------------------------AFFICHER la popup "transfert entre comptes"--------------------------------------
function togglePopupentreCompte() {
    document.getElementById("popup-1").classList.toggle("active");
    document.getElementById('msg-erreur-virement-compte').innerText = "";
}

//--------------------------------------AFFICHER la popup "transfert entre personnes"--------------------------------------
function togglePopupentrePersonne() {
    document.getElementById("popup-2").classList.toggle("active");
}

//Fonction enlever la sélection quand le popup se ferme
function removeSelect() {
    divSelect = document.getElementsByClassName("select-clicked");

    for (let i = 0; i < divSelect.length; i++)
        divSelect[i].classList.remove("select-clicked");
}