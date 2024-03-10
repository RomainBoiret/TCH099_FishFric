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
                compteHtml += '</i><a href="/consulterCompte/consulterCompte.php?id=' + compte.id + '">Détails du compte</a></div></div>';                                                              

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
                    requeteVirement.open('PUT', '/Transfert/API/gestionTransfert.php/compte', true);
                    
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
                            document.getElementById('msg-erreur-virement-compte').innerText = "";
                            let msg = document.createElement('span');

                            if ("msgSucces" in responseData) {
                                msg.innerText = responseData.msgSucces;
                                msg.style.color = "green";
                                document.getElementById('msg-erreur-virement-compte').appendChild(msg);
                            }

                            else {
                                responseData.erreur.forEach(function(message) {
                                    msg.innerText = message;
                                    msg.style.color = "red";
                                    document.getElementById('msg-erreur-virement-compte').appendChild(msg);
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
                    comptes += '</td><td><span>' + compte.solde + '</span></td></tr>';               
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
                    requeteVirement.open('PUT', '/Transfert/API/gestionTransfert.php/utilisateurEnvoi', true);
                    
                    //Stocke les donnees a envoyer en format JSON
                    requeteVirement.setRequestHeader('Content-Type', 'application/json');
                    const donneesJsonVirement = JSON.stringify({"idCompteBancaireProvenant": idCompteBancaireProvenant,
                                                                "montant": montant,
                                                                "courrielDest": courrielDest,
                                                                "question": question,
                                                                "reponse": reponse,
                                                                "confReponse": confReponse});

                    console.log("Json: " + donneesJsonVirement)

                    //Messages d'erreurs ou de succès du virement
                    requeteVirement.onload = function() {
                        //Vérifier si la requête a marché
                        if (requeteVirement.readyState === 4 && requeteVirement.status === 200) {
                            //Décoder la réponse (qui est au format JSON)
                            let responseData = JSON.parse(requeteVirement.responseText);

                            //Afficher les messages d'erreur ou de succès
                            document.getElementById('msg-erreur-virement-personne').innerText = "";
                            let msg = document.createElement('span');

                            if ("msgSucces" in responseData) {
                                msg.innerText = responseData.msgSucces;
                                msg.style.color = "green";
                                document.getElementById('msg-erreur-virement-personne').appendChild(msg);
                            }

                            else {
                                responseData.erreur.forEach(function(message) {
                                    msg.innerText = message;
                                    msg.style.color = "red";
                                    document.getElementById('msg-erreur-virement-personne').appendChild(msg);
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
                    comptes += '</td><td><span>' + compte.solde + '</span></td></tr>';               
                });

                //Ajouter le HTML dans la table
                document.getElementById('tableFacture').innerHTML = comptes;

                //--------------------------------------REQUÊTE PUT VIREMENT--------------------------------------
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
                    requeteFacture.open('PUT', '/Transfert/API/gestionTransfert.php/facture', true);
                    
                    //Stocke les donnees a envoyer en format JSON
                    requeteFacture.setRequestHeader('Content-Type', 'application/json');
                    const donneesJsonFacture = JSON.stringify({"idCompteBancaireProvenant": idCompteBancaireProvenant,
                                                                "montant": montant,
                                                                "nomEtablissement": nomEtablissement,
                                                                "raison": raison});

                    console.log("Json: " + donneesJsonFacture)

                    //Messages d'erreurs ou de succès du virement
                    requeteFacture.onload = function() {
                        //Vérifier si la requête a marché
                        if (requeteFacture.readyState === 4 && requeteFacture.status === 200) {
                            //Décoder la réponse (qui est au format JSON)
                            let responseData = JSON.parse(requeteFacture.responseText);

                            //Afficher les messages d'erreur ou de succès
                            document.getElementById('msg-erreur-payer-facture').innerText = "";
                            let msg = document.createElement('span');

                            if ("msgSucces" in responseData) {
                                msg.innerText = responseData.msgSucces;
                                msg.style.color = "green";
                                document.getElementById('msg-erreur-payer-facture').appendChild(msg);
                            }

                            else {
                                responseData.erreur.forEach(function(message) {
                                    msg.innerText = message;
                                    msg.style.color = "red";
                                    document.getElementById('msg-erreur-payer-facture').appendChild(msg);
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

//--------------------------------------AFFICHER la popup "transfert entre comptes"--------------------------------------
function togglePopupentreCompte() {
    document.getElementById("popup-1").classList.toggle("active");
    document.getElementById('msg-erreur-virement-compte').innerText = ""; //Vider les messages d'erreurs
}

//--------------------------------------AFFICHER la popup "transfert entre personnes"--------------------------------------
function togglePopupentrePersonne() {
    document.getElementById("popup-2").classList.toggle("active");
    document.getElementById('msg-erreur-virement-personne').innerText = ""; //Vider les messages d'erreurs
}

//--------------------------------------AFFICHER la popup "payer facture"--------------------------------------
function togglePopupFacture() {
    document.getElementById("popup-3").classList.toggle("active");
    document.getElementById('msg-erreur-payer-facture').innerText = ""; //Vider les messages d'erreurs
}
