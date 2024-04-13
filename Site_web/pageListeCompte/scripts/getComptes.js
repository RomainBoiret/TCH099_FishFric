document.addEventListener("DOMContentLoaded", async function() {
    let toastBox = document.getElementById('toastBox')

    //--------------------------------------REQUÊTE GET AFFICHER LES COMPTES--------------------------------------
    let responseData = await getComptes();
    responseData = JSON.parse(responseData);
    
    //--------------------------------------AFFICHER COMPTES virement entre comptes--------------------------------------
    document.getElementById("btnPopupComptes").addEventListener('click', async function() {
        //Get l'info des comptes
        responseData = await getComptes();
        responseData = JSON.parse(responseData);

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
    });

    //--------------------------------------REQUÊTE PUT VIREMENT--------------------------------------
    document.getElementById('btnVirerCompte').addEventListener('click', function() {
        clic = true;

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
        requeteVirement.onload = async function() {
            //Vérifier si la requête a marché
            if (requeteVirement.readyState === 4 && requeteVirement.status === 200) {
                //Décoder la réponse (qui est au format JSON)
                let responseData = JSON.parse(requeteVirement.responseText);

                if ("msgSucces" in responseData) {
                    let responseData2 = await getComptes();
                    responseData2 = JSON.parse(responseData2);

                    comptes = '<tr><th>De</th><th>Vers</th><th>Compte et descriptif</th><th>Solde ($)</th></tr>';
    
                    responseData2.comptes.forEach(function(compte) {
                        //Afficher chaque compte dans le tableau, ajouter le HTML dynamiquement
                        comptes += '<tr><td><input type="radio" name="option-1" id="' + compte.id + '"></td>';
                        comptes += '<td><input type="radio" name="option-2" id="' + compte.id + '"></td>';
                        comptes += '<td><span>' + compte.typeCompte + ' </span>';
                        comptes += '<span>ID: ' + compte.id + ' </span>';
                        comptes += '</td><td><span class="solde-transfert-' + compte.id + '">' + compte.solde + '</span></td></tr>';                  
                    });
            
                    //Ajouter le HTML dans la table
                    document.getElementById('tableVirementComptes').innerHTML = comptes;

                    //Mettre le message de succès en vert
                    let toast = document.createElement('div');
                    toast.classList.add('toast');
                    toast.classList.add('success');
                    toast.innerHTML = '<i class="bx bxs-check-circle"></i>' + responseData.msgSucces;
                    toastBox.appendChild(toast);

                    //Désactiver le bouton pour ne pas refaire un virement
                    document.getElementById('btnVirerCompte').setAttribute('disabled', 'true');

                    //Fermer la fenêtre
                    setTimeout(() => {
                        togglePopupentreCompte();
                        toast.remove();

                        //Réactiver le bouton
                        document.getElementById('btnVirerCompte').removeAttribute('disabled');
                    }, 1500);
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
                        }, 4500);
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
    });


    //--------------------------------------AFFICHER COMPTES virement entre personnes--------------------------------------
    document.getElementById("btnPopupPersonnes").addEventListener('click', async function() {
        //Get l'info des comptes
        responseData = await getComptes();
        responseData = JSON.parse(responseData);
        
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
    });

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
        requeteVirement.onload = async function() {
            //Vérifier si la requête a marché
            if (requeteVirement.readyState === 4 && requeteVirement.status === 200) {
                //Décoder la réponse (qui est au format JSON)
                let responseData = JSON.parse(requeteVirement.responseText);

                //Afficher les messages d'erreur ou de succès
                if ("msgSucces" in responseData) {
                    // //Actualiser le solde du compte provenant
                    let responseData2 = await getComptes();
                    responseData2 = JSON.parse(responseData2);

                    comptes = '<tr><th>De</th><th>Compte et descriptif</th><th>Solde ($)</th></tr>';
    
                    responseData2.comptes.forEach(function(compte) {
                        //Afficher chaque compte dans le tableau, ajouter le HTML dynamiquement
                        comptes += '<tr><td><input type="radio" name="choix" id="' + compte.id + '"></td>';
                        comptes += '<td><span>' + compte.typeCompte + ' </span>';
                        comptes += '<span>ID: ' + compte.id + ' </span>';
                        comptes += '</td><td><span class="solde-virement-' + compte.id + '">' + compte.solde + '</span></td></tr>';                 
                    });
            
                    //Ajouter le HTML dans la table
                    document.getElementById('tableVirementPersonnes').innerHTML = comptes;

                    //Afficher le message de succès
                    let toast = document.createElement('div');
                    toast.classList.add('toast');
                    toast.classList.add('success');
                    toast.innerHTML = '<i class="bx bxs-check-circle"></i>' + responseData.msgSucces;
                    toastBox.appendChild(toast);

                    //Désactiver le bouton pour ne pas refaire un virement
                    document.getElementById('btnVirerPersonne').setAttribute('disabled', 'true');

                    //Fermer la fenêtre
                    setTimeout(() => {
                        togglePopupentrePersonne();
                        toast.remove();

                        //Réactiver le bouton
                        document.getElementById('btnVirerPersonne').removeAttribute('disabled');
                    }, 1500);
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
                        }, 4500);
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
    });


    //--------------------------------------AFFICHER COMPTES PAYER FACTURE--------------------------------------
    document.getElementById("btnPopupFacture").addEventListener('click', async function() {
        //Get l'info des comptes
        responseData = await getComptes();
        responseData = JSON.parse(responseData);

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
    });

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
        requeteFacture.onload = async function() {
            //Vérifier si la requête a marché
            if (requeteFacture.readyState === 4 && requeteFacture.status === 200) {
                //Décoder la réponse (qui est au format JSON)
                let responseData = JSON.parse(requeteFacture.responseText);

                //Afficher un message de succès si la reqûete renvoie "msgSucces"
                if ("msgSucces" in responseData) {
                    //Actualiser les soldes des comptes
                    let responseData2 = await getComptes();
                    responseData2 = JSON.parse(responseData2);

                    comptes = '<tr><th>De</th><th>Compte et descriptif</th><th>Solde ($)</th></tr>';
    
                    responseData2.comptes.forEach(function(compte) {
                        //Afficher chaque compte dans le tableau, ajouter le HTML dynamiquement
                        comptes += '<tr><td><input type="radio" name="choix" id="' + compte.id + '"></td>';
                        comptes += '<td><span>' + compte.typeCompte + ' </span>';
                        comptes += '<span>ID: ' + compte.id + ' </span>';
                        comptes += '</td><td><span class="solde-facture-' + compte.id + '">' + compte.solde + '</span></td></tr>';               
                    });
            
                    //Ajouter le HTML dans la table
                    document.getElementById('tableFacture').innerHTML = comptes;



                    //Mettre le message de succès
                    let toast = document.createElement('div');
                    toast.classList.add('toast');
                    toast.classList.add('success');
                    toast.innerHTML = '<i class="bx bxs-check-circle"></i>' + responseData.msgSucces;
                    toastBox.appendChild(toast);

                    //Désactiver le bouton pour ne pas refaire un virement
                    document.getElementById('btnPayerFacture').setAttribute('disabled', 'true');

                    //Fermer la fenêtre après 1.5s
                    setTimeout(() => {
                        togglePopupFacture();
                        toast.remove();

                        //Réactiver le bouton
                        document.getElementById('btnPayerFacture').removeAttribute('disabled');
                    }, 1500);
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
                            toast.remove();
                        }, 4500);
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
    });
        

    //--------------------------------AFFICHER COMPTES SUPPRIMER COMPTE BANCAIRE--------------------------------
    document.getElementById("btnPopupPreferences").addEventListener('click', async function() {
        //Get l'info des comptes
        responseData = await getComptes();
        responseData = JSON.parse(responseData);

        comptes = '<tr><th>Choix</th><th>Compte</th></tr>';
        
        responseData.comptes.forEach(function(compte) {
            //Afficher chaque compte dans le tableau, ajouter le HTML dynamiquement
            comptes += '<tr><td><input type="radio" name="choix" id="' + compte.id + '"></td>';
            comptes += '<td><span>' + compte.typeCompte + ' </span></td></tr>';            
        });

        //Ajouter le HTML dans la table
        document.getElementById('tableSupprimerCompte').innerHTML = comptes;

        document.getElementById('btnSupprimerCompteBancaire').addEventListener('click', function() {
            //Chercher le compte que l'utilisateur a sélectionné 
            let idCompteBancaire;

            ["choix"].forEach(option => {
                const selectedOption = document.querySelector(`input[name=${option}]:checked`);

                if (selectedOption) {
                    idCompteBancaire = selectedOption.id;
                }
            });

            //On peut commencer notre requête
            requeteSupprimerCptBancaire = new XMLHttpRequest();
            requeteSupprimerCptBancaire.open('PUT', '/TCH099_FishFric/Site_web/pageListeCompte/API/preferences.php/compteBancaire', true);
            
            //Stocke les donnees a envoyer en format JSON
            requeteSupprimerCptBancaire.setRequestHeader('Content-Type', 'application/json');
            const donneesJsonSupprimerCptBancaire = JSON.stringify({"idCompteBancaire": idCompteBancaire});

            console.log("ID:" + idCompteBancaire)

            //Messages d'erreurs ou de succès du virement
            requeteSupprimerCptBancaire.onload = function() {
                //Vérifier si la requête a marché
                if (requeteSupprimerCptBancaire.readyState === 4 && requeteSupprimerCptBancaire.status === 200) {
                    //Décoder la réponse (qui est au format JSON)
                    let responseData = JSON.parse(requeteSupprimerCptBancaire.responseText);

                    //Afficher un message de succès si la reqûete renvoie "msgSucces"
                    if ("msgSucces" in responseData) {
                        //Mettre le message de succès
                        let toast = document.createElement('div');
                        toast.classList.add('toast');
                        toast.classList.add('success');
                        toast.innerHTML = '<i class="bx bxs-check-circle"></i>' + responseData.msgSucces;
                        toastBox.appendChild(toast);

                        //Fermer la fenêtre
                        setTimeout(() => {
                            toast.remove();
                            togglePopupPreferences();
                        }, 1500);
                    }

                    else if ("erreurs" in responseData) {
                        responseData.erreurs.forEach(function(message) {
                            //Afficher chaque message d'erreur
                            let toast = document.createElement('div');
                            toast.classList.add('toast');
                            toast.classList.add('error');
                            toast.innerHTML = '<i class="bx bxs-error-circle"></i>' + message;
                            toastBox.appendChild(toast);
        
                            //Fermer la fenêtre
                            setTimeout(() => {
                                toast.remove();
                            }, 4500);
                        })
                    }
                }
            }

            //Message d'erreur de la requête
            requeteSupprimerCptBancaire.onerror = function() {
                console.error('La requête n\'a pas fonctionné!');
            };

            //Envoyer la requête
            requeteSupprimerCptBancaire.send(donneesJsonSupprimerCptBancaire);

        });

    });

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
}

//--------------------------------------AFFICHER la popup "transfert entre personnes"--------------------------------------
function togglePopupentrePersonne() {
    let popup = document.getElementById("popup-2");
    popup.classList.toggle("active");
}

//--------------------------------------AFFICHER la popup "payer facture"--------------------------------------
function togglePopupFacture() {
    let popup = document.getElementById("popup-3");
    popup.classList.toggle("active");
}

//--------------------------------------AFFICHER la popup "Ajouter un compte"--------------------------------------
function togglePopupNouveauCompte()  {
    let popup = document.getElementById("popup-4");
    popup.classList.toggle("active");
}

//--------------------------------------AFFICHER la popup "Préférences de compte"--------------------------------------
function togglePopupPreferences()  {
    let popup = document.getElementById("popup-5");
    popup.classList.toggle("active");

    //Rafraîchir la page au bout de 100ms lorsqu'on ferme la popup
    if (!document.getElementById("popup-5").classList.contains("active")) {
        setTimeout(function() {
            location.reload();
        }, 100);
    }
}

//--------------------------Fonction pour fermer les divs de détails dans la popup préférences-------------------------
const details = document.querySelectorAll("details");

//Ajouter l'écouteur d'événement pour chaque div de détail
details.forEach((detail) => {
  detail.addEventListener("toggle", () => {
    if (detail.open) setTargetDetail(detail);
  });
});

//Fermer tous les détails sauf celui qu'on a cliqué pour ouvrir
function setTargetDetail(targetDetail) {
  details.forEach((detail) => {
    if (detail !== targetDetail) {
      detail.open = false;
    }
  });
}


// easter egg
let bonjour = document.getElementById('txtBonjour');

bonjour.addEventListener('click', function () {

    let myAudio = document.createElement("audio");
    myAudio.src = "../bandesSons/welcome-aboard.mp3";
    myAudio.play();
});


//---------------------------------------FONCTION GET COMPTES-------------------------------------//
function getComptes() {
    return new Promise(function (resolve, reject) {
        //Créer la requête
        let requeteGetComptes = new XMLHttpRequest();

        //Configurer la requête, pour aller chercher les comptes
        requeteGetComptes.open('GET', '/TCH099_FishFric/Site_web/pageListeCompte/API/afficherComptes.php', true);

        requeteGetComptes.onload = function() {
            //Vérifier si la requête a marché
            if (requeteGetComptes.readyState === 4 && requeteGetComptes.status === 200) {

                //Décoder la réponse (qui est au format JSON)
                let responseData = JSON.parse(requeteGetComptes.responseText);

                //--------------------------------------AFFICHER LA LISTE DES COMPTES--------------------------------------
                let comptes = '';
                responseData.comptes.forEach(function(compte) {
                    //Mettre tout le code HTML de la structure d'un compte dans une string
                    //style="background-image: url(\'images/poisson-globe.png\'); background-repeat: no-repeat;"

                    let compteHtml = '<div class="compte-box" id="compte-box"'
                    
                    //Ajouter une image selon le type de compte
                    if (compte.typeCompte == "Compte chèque")
                    {
                        compteHtml += 'style="background-image: url(\'images/sticker-poisson.png\'); background-repeat: no-repeat;"';
                    }
                    else if (compte.typeCompte == "Compte épargne")
                    {
                        compteHtml += 'style="background-image: url(\'images/sticker-dauphin.png\'); background-repeat: no-repeat;"';
                    }
                    else if (compte.typeCompte == "Carte requin")
                    {
                        compteHtml += 'style="background-image: url(\'images/sticker-raie-manta.png\'); background-repeat: no-repeat;"';
                    }
                    else
                    {
                        compteHtml += 'style="background-image: url(\'images/sticker-recif-de-corail.png\'); background-repeat: no-repeat;"';
                    } 
                    
                    compteHtml += '><div class="box-header">';
                    compteHtml += '<h2>' + compte.typeCompte + '</h2>';
                    compteHtml += '<div class="montant-compte">';
                    compteHtml += '<div class="montant">' + compte.solde + '</div>';
                    compteHtml += '<img src="images/fishcoin.png"></div></div>';
                    compteHtml += '<p>Numéro de compte: ' + compte.id + '</p>';
                    compteHtml += '<div class="btn-menu"><i class="bx bxs-right-arrow-circle">';
                    compteHtml += '</i><a href="/TCH099_FishFric/Site_web/pageConsulterCompte/pageConsulterCompte.php?id=' + compte.id + '">Détails du compte</a></div></div>';                                                              

                    comptes += compteHtml;
                });

                //Afficher les comptes dans le div
                document.getElementById('compte-content').innerHTML = comptes;

                resolve(requeteGetComptes.responseText);
            } 

            else {
                //Afficher l'erreur de la requête GET s'il y a lieu
                console.error('Request failed with status code:', requeteGetComptes.status);

                reject(status);
            }
        };

        //Message d'erreur de la requête
        requeteGetComptes.onerror = function() {
            console.error('La requête n\'a pas fonctionné!');
        };

        //Envoyer la requête
        requeteGetComptes.send();
    });
}