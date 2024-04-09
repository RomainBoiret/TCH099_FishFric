document.addEventListener("DOMContentLoaded", function() {

    //--------------------------------------REQUÊTE GET AFFICHER LES COMPTES--------------------------------------
    
    //Créer la requête
    let requeteGetComptes = new XMLHttpRequest();

    //Configurer la requête, pour aller chercher les comptes
    requeteGetComptes.open('GET', '/TCH099_FishFric/Site_web/Liste_compte/API/afficherComptes.php', true);

    requeteGetComptes.onload = function() {
        //Vérifier si la requête a marché
        if (requeteGetComptes.readyState === 4 && requeteGetComptes.status === 200) {

            //Décoder la réponse (qui est au format JSON)
            let responseData = JSON.parse(requeteGetComptes.responseText);

            //Afficher la liste des comptes de l'utilisateur en HTML
            let comptes = '';
            responseData.comptes.forEach(function(compte) {
                //Mettre tout le code HTML de la structure d'un compte dans une string
                //style="background-image: url(\'Images/poisson-globe.png\'); background-repeat: no-repeat;"

                let compteHtml = '<div class="compte-box" id="compte-box"'
                
                //Ajouter une image selon le type de compte
                if (compte.typeCompte == "Compte chèque")
                {
                    compteHtml += 'style="background-image: url(\'Images/poisson-globe.png\'); background-repeat: no-repeat;"';
                }
                else if (compte.typeCompte == "Compte épargne")
                {
                    compteHtml += 'style="background-image: url(\'Images/poisson-koi.png\'); background-repeat: no-repeat;"';
                }
                else if (compte.typeCompte == "Carte requin")
                {
                    compteHtml += 'style="background-image: url(\'Images/requin.png\'); background-repeat: no-repeat;"';
                }
                else
                {
                    compteHtml += 'style="background-image: url(\'Images/algue.png\'); background-repeat: no-repeat;"';
                } 
                
                compteHtml += '><div class="box-header">';
                compteHtml += '<h2>' + compte.typeCompte + '</h2>';
                compteHtml += '<div class="montant-compte">';
                compteHtml += '<div class="montant">' + compte.solde + '</div>';
                compteHtml += '<img src="Images/fishcoin.png"></div></div>';
                compteHtml += '<p>Numéro de compte: ' + compte.id + '</p>';
                compteHtml += '<div class="btn-menu"><i class="bx bxs-right-arrow-circle">';
                compteHtml += '</i><a href="/TCH099_FishFric/Site_web/consulterCompte/consulterCompte.php?id=' + compte.id + '">Détails du compte</a></div></div>';                                                              

                comptes += compteHtml;

            });

            //Afficher les comptes dans le div
            document.getElementById('compte-content').innerHTML = comptes;
            let toastBox = document.getElementById('toastBox')

            //--------------------------------------AFFICHER COMPTES virement entre comptes--------------------------------------
            document.getElementById("btnPopupComptes").addEventListener('click', function() {
                comptes = '<tr><th>De</th><th>Vers</th><th>Compte et descriptif</th><th>Solde ($)</th></tr>';
                
                responseData.comptes.forEach(function(compte) {
                    //Afficher chaque compte dans le tableau, ajouter le HTML dynamiquement
                    comptes += '<tr><td><input type="radio" name="option-1" id="' + compte.id + '"></td>';
                    comptes += '<td><input type="radio" name="option-2" id="' + compte.id + '"></td>';
                    comptes += '<td><span>' + compte.typeCompte + ' </span>';
                    comptes += '<span>ID: ' + compte.id + ' </span>';
                    comptes += '</td><td><span class="solde-transfert-' + compte.id + '">' + compte.solde + '</span></td></tr>';                  
                });

                //Ajouter le HTML dans la table
                document.getElementById('tableVirementComptes').innerHTML = comptes;

                //--------------------------------------REQUÊTE PUT VIREMENT--------------------------------------
                document.getElementById('btnVirerCompte').addEventListener('click', function() {
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

                    //On peut commencer notre requête
                    requeteVirement = new XMLHttpRequest();
                    requeteVirement.open('PUT', '/TCH099_FishFric/Site_web/Transfert/API/gestionTransfert.php/compte', true);
                    
                    //Stocke les donnees a envoyer en format JSON
                    requeteVirement.setRequestHeader('Content-Type', 'application/json');
                    const donneesJsonVirement = JSON.stringify({"idCompteBancaireProvenant": idCompteBancaireProvenant,
                                                                "idCompteBancaireRecevant": idCompteBancaireRecevant,
                                                                "montant": montant});

                    //Messages d'erreurs ou de succès du virement
                    requeteVirement.onload = function() {
                        //Vérifier si la requête a marché
                        if (requeteVirement.readyState === 4 && requeteVirement.status === 200) {
                            //Décoder la réponse (qui est au format JSON)
                            let responseData = JSON.parse(requeteVirement.responseText);

                            //Afficher les messages d'erreur ou de succès
                            // document.getElementById('msg-erreur-virement-compte').innerText = "";
                            // let msg = document.createElement('span');

                            if ("msgSucces" in responseData) {
                                //Actualiser le solde du compte provenant
                                let spanSoldeProvenant = document.querySelector('.solde-transfert-' + idCompteBancaireProvenant);
                                let nouveauSoldeProvenant = (spanSoldeProvenant.textContent - parseFloat(montant)).toFixed(2);
                                spanSoldeProvenant.textContent = nouveauSoldeProvenant;

                                //Actualiser le solde du compte provenant
                                let spanSoldeRecevant = document.querySelector('.solde-transfert-' + idCompteBancaireRecevant);
                                let nouveauSoldeRecevant = (parseFloat(spanSoldeRecevant.textContent) + parseFloat(montant)).toFixed(2);
                                spanSoldeRecevant.textContent = nouveauSoldeRecevant;

                                //Mettre le message de succès en vert
                                let toast = document.createElement('div');
                                toast.classList.add('toast');
                                toast.classList.add('success');
                                toast.innerHTML = '<i class="bx bxs-check-circle"></i>' + responseData.msgSucces;
                                toastBox.appendChild(toast);

                                //Fermer la fenêtre
                                setTimeout(() => {
                                    togglePopupentreCompte()
                                }, 1500);

                                //Désactiver le bouton pour ne pas refaire un virement
                                document.getElementById('btnVirerCompte').setAttribute('disabled', 'true');

                                
                            }

                            else {
                                responseData.erreur.forEach(function(message) {
                                    //Afficher chaque message d'erreur
                                    let toast = document.createElement('div');
                                    toast.classList.add('toast');
                                    toast.classList.add('error');
                                    toast.innerHTML = '<i class="bx bxs-error-circle"></i>' + message;
                                    toastBox.appendChild(toast);
                
                                    setTimeout(() => {
                                        toast.remove();
                                    }, 3000);
                                })
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
            document.getElementById("btnPopupPersonnes").addEventListener('click', function() {
                comptes = '<tr><th>De</th><th>Compte et descriptif</th><th>Solde ($)</th></tr>';
                
                responseData.comptes.forEach(function(compte) {
                    //Afficher chaque compte dans le tableau, ajouter le HTML dynamiquement
                    comptes += '<tr><td><input type="radio" name="choix" id="' + compte.id + '"></td>';
                    comptes += '<td><span>' + compte.typeCompte + ' </span>';
                    comptes += '<span>ID: ' + compte.id + ' </span>';
                    comptes += '</td><td><span class="solde-virement-' + compte.id + '">' + compte.solde + '</span></td></tr>';                 
                });

                //Ajouter le HTML dans la table
                document.getElementById('tableVirementPersonnes').innerHTML = comptes;

                //--------------------------------------REQUÊTE PUT VIREMENT--------------------------------------
                document.getElementById('btnVirerPersonne').addEventListener('click', function() {
                    //Chercher les données à envoyer à la requête
                    let montant = document.getElementById("montant-virement-personne").value;
                    let idCompteBancaireProvenant;

                    //Chercher le compte que l'utilisateur a sélectionné 
                    ["choix"].forEach(option => {
                        const selectedOption = document.querySelector(`input[name=${option}]:checked`);

                        if (selectedOption) {
                            idCompteBancaireProvenant = selectedOption.id;
                        }
                    });

                    //Chercher les données du virement
                    let courrielDest = document.getElementById('courrielDest').value;
                    let question = document.getElementById('question').value;
                    let reponse = document.getElementById('reponse').value;
                    let confReponse = document.getElementById('confReponse').value;

                    //On peut commencer notre requête
                    requeteVirement = new XMLHttpRequest();
                    requeteVirement.open('PUT', '/TCH099_FishFric/Site_web/Transfert/API/gestionTransfert.php/utilisateurEnvoi', true);
                    
                    //Stocke les donnees a envoyer en format JSON
                    requeteVirement.setRequestHeader('Content-Type', 'application/json');
                    const donneesJsonVirement = JSON.stringify({"idCompteBancaireProvenant": idCompteBancaireProvenant,
                                                                "montant": montant,
                                                                "courrielDest": courrielDest,
                                                                "question": question,
                                                                "reponse": reponse,
                                                                "confReponse": confReponse});

                    //Messages d'erreurs ou de succès du virement
                    requeteVirement.onload = function() {
                        //Vérifier si la requête a marché
                        if (requeteVirement.readyState === 4 && requeteVirement.status === 200) {
                            //Décoder la réponse (qui est au format JSON)
                            let responseData = JSON.parse(requeteVirement.responseText);

                            //Afficher les messages d'erreur ou de succès
                            if ("msgSucces" in responseData) {
                                //Actualiser le solde du compte provenant
                                let spanSoldeProvenant = document.querySelector('.solde-virement-' + idCompteBancaireProvenant);
                                let nouveauSoldeProvenant = (spanSoldeProvenant.textContent - parseFloat(montant)).toFixed(2);
                                spanSoldeProvenant.textContent = nouveauSoldeProvenant;

                                //Afficher le message de succès
                                let toast = document.createElement('div');
                                toast.classList.add('toast');
                                toast.classList.add('success');
                                toast.innerHTML = '<i class="bx bxs-check-circle"></i>' + responseData.msgSucces;
                                toastBox.appendChild(toast);

                                //Fermer la fenêtre
                                setTimeout(() => {
                                    togglePopupentrePersonne()
                                }, 1500);

                                //Désactiver le bouton pour ne pas refaire un virement
                                document.getElementById('btnVirerPersonne').setAttribute('disabled', 'true');
                            }

                            else {
                                responseData.erreur.forEach(function(message) {
                                    //Afficher chaque message d'erreur
                                    let toast = document.createElement('div');
                                    toast.classList.add('toast');
                                    toast.classList.add('error');
                                    toast.innerHTML = '<i class="bx bxs-error-circle"></i>' + message;
                                    toastBox.appendChild(toast);
                
                                    setTimeout(() => {
                                        toast.remove();
                                    }, 3000);
                                })
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
            });


            //--------------------------------------AFFICHER COMPTES PAYER FACTURE--------------------------------------
            document.getElementById("btnPopupFacture").addEventListener('click', function() {
                comptes = '<tr><th>De</th><th>Compte et descriptif</th><th>Solde ($)</th></tr>';
                
                responseData.comptes.forEach(function(compte) {
                    //Afficher chaque compte dans le tableau, ajouter le HTML dynamiquement
                    comptes += '<tr><td><input type="radio" name="choix" id="' + compte.id + '"></td>';
                    comptes += '<td><span>' + compte.typeCompte + ' </span>';
                    comptes += '<span>ID: ' + compte.id + ' </span>';
                    comptes += '</td><td><span class="solde-facture-' + compte.id + '">' + compte.solde + '</span></td></tr>';               
                });

                //Ajouter le HTML dans la table
                document.getElementById('tableFacture').innerHTML = comptes;

                //--------------------------------------REQUÊTE PUT PAYER FACTURE--------------------------------------
                document.getElementById('btnPayerFacture').addEventListener('click', function() {
                    //Chercher les données à envoyer à la requête
                    let montant = document.getElementById("montant-payer-facture").value;
                    let idCompteBancaireProvenant;

                    //Chercher le compte que l'utilisateur a sélectionné 
                    ["choix"].forEach(option => {
                        const selectedOption = document.querySelector(`input[name=${option}]:checked`);

                        if (selectedOption) {
                            idCompteBancaireProvenant = selectedOption.id;
                        }
                    });

                    //Chercher les données du virement
                    let nomEtablissement = document.getElementById('nomEtablissement').value;
                    let raison = document.getElementById('facture_raison').value;

                    //On peut commencer notre requête
                    requeteFacture = new XMLHttpRequest();
                    requeteFacture.open('PUT', '/TCH099_FishFric/Site_web/Transfert/API/gestionTransfert.php/facture', true);
                    
                    //Stocke les donnees a envoyer en format JSON
                    requeteFacture.setRequestHeader('Content-Type', 'application/json');
                    const donneesJsonFacture = JSON.stringify({"idCompteBancaireProvenant": idCompteBancaireProvenant,
                                                                "montant": montant,
                                                                "nomEtablissement": nomEtablissement,
                                                                "raison": raison});

                    //Messages d'erreurs ou de succès du virement
                    requeteFacture.onload = function() {
                        //Vérifier si la requête a marché
                        if (requeteFacture.readyState === 4 && requeteFacture.status === 200) {
                            //Décoder la réponse (qui est au format JSON)
                            let responseData = JSON.parse(requeteFacture.responseText);

                            //Afficher un message de succès si la reqûete renvoie "msgSucces"
                            if ("msgSucces" in responseData) {
                                //Actualiser le solde
                                let spanSolde = document.querySelector('.solde-facture-' + idCompteBancaireProvenant);
                                let nouveauSolde = (spanSolde.textContent - parseFloat(montant)).toFixed(2);
                                spanSolde.textContent = nouveauSolde;

                                //Mettre le message de succès
                                let toast = document.createElement('div');
                                toast.classList.add('toast');
                                toast.classList.add('success');
                                toast.innerHTML = '<i class="bx bxs-check-circle"></i>' + responseData.msgSucces;
                                toastBox.appendChild(toast);

                                //Fermer la fenêtre
                                setTimeout(() => {
                                    togglePopupFacture()
                                }, 1500);

                                //Désactiver le bouton pour ne pas refaire un virement
                                document.getElementById('btnPayerFacture').setAttribute('disabled', 'true');
                            }

                            //Sinon, afficher chaque erreur
                            else {
                                responseData.erreur.forEach(function(message) {
                                    //Afficher chaque message d'erreur
                                    let toast = document.createElement('div');
                                    toast.classList.add('toast');
                                    toast.classList.add('error');
                                    toast.innerHTML = '<i class="bx bxs-error-circle"></i>' + message;
                                    toastBox.appendChild(toast);
                
                                //Fermer la fenêtre
                                setTimeout(() => {
                                    togglePopupNouveauCompte()
                                }, 1500);
                                })
                            }
                        } 
        
                        else {
                            //Afficher l'erreur s'il y a lieu
                            console.error('Request failed with status code:', requeteFacture.status);
                        }
                    }

                    //Message d'erreur de la requête
                    requeteFacture.onerror = function() {
                        console.error('La requête n\'a pas fonctionné!');
                    };

                    //Envoyer la requête
                    requeteFacture.send(donneesJsonFacture);
                })
            });
        } 

        else {
            //Afficher l'erreur de la requête GET s'il y a lieu
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

//--------------------------------------AFFICHER/CACHER la Messagerie--------------------------------------
const divContainer = document.querySelector('#elementToWorkOn');
let isClicked = true;

let showOrHide = function() {
    if(isClicked) {
        divContainer.style.display = 'block';
        isClicked = false;
        document.querySelector('section').addEventListener('click', fermerMessagerie);

    } else {
        divContainer.style.display = 'none';
        isClicked = true;

        //Recharger la page
        setTimeout(function() {
            location.reload();
        }, 100);
    }
}

//Fonction pour fermer la messagerie lorsqu'on appuie sur la page et que la messagerie est ouverte
function fermerMessagerie() {
    //Si le div de la messagerie est apparant, on le fait disparaître
    if (divContainer.style.display == 'block') {
        divContainer.style.display = 'none';
        isClicked = true;

        //Recharger la page
        setTimeout(function() {
            location.reload();
        }, 100);
    }
}

//--------------------------------------AFFICHER la popup "transfert entre comptes"--------------------------------------
function togglePopupentreCompte() {
    let popup = document.getElementById("popup-1");
    popup.classList.toggle("active");
    //document.getElementById('msg-erreur-virement-compte').innerText = ""; //Vider les messages d'erreurs

    //Rafraîchir la page au bout de 100ms lorsqu'on ferme la popup, pour actualiser les valeurs de comptes
    if (!document.getElementById("popup-1").classList.contains("active")) {
        setTimeout(function() {
            location.reload();
        }, 100);
    }
}

//--------------------------------------AFFICHER la popup "transfert entre personnes"--------------------------------------
function togglePopupentrePersonne() {
    let popup = document.getElementById("popup-2");
    popup.classList.toggle("active");
    //document.getElementById('msg-erreur-virement-personne').innerText = ""; //Vider les messages d'erreurs

    //Rafraîchir la page au bout de 100ms lorsqu'on ferme la popup, pour actualiser les valeurs de comptes
    if (!document.getElementById("popup-2").classList.contains("active")) {
        setTimeout(function() {
            location.reload();
        }, 100);
    }
}

//--------------------------------------AFFICHER la popup "payer facture"--------------------------------------
function togglePopupFacture() {
    let popup = document.getElementById("popup-3");
    popup.classList.toggle("active");
    //document.getElementById('msg-erreur-payer-facture').innerText = ""; //Vider les messages d'erreurs

    //Rafraîchir la page au bout de 100ms lorsqu'on ferme la popup, pour actualiser les valeurs de comptes
    if (!document.getElementById("popup-3").classList.contains("active")) {
        setTimeout(function() {
            location.reload();
        }, 100);
    }
}

//--------------------------------------AFFICHER la popup "Ajouter un compte"--------------------------------------
function togglePopupNouveauCompte()  {
    let popup = document.getElementById("popup-4");
    popup.classList.toggle("active");
    //document.getElementById('msg-erreur-payer-facture').innerText = ""; //Vider les messages d'erreurs

    //Rafraîchir la page au bout de 100ms lorsqu'on ferme la popup, pour actualiser les valeurs de comptes
    if (!document.getElementById("popup-4").classList.contains("active")) {
        setTimeout(function() {
            location.reload();
        }, 100);
    }
}
