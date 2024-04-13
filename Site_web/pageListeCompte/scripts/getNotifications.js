document.querySelector('.messagerie').addEventListener('click', function() {
    getNotifs();
});


//--------------------------FONCTION getNotifs----------------------------
// - Fait la requête pour chercher les notifs et les affiche dans la messagerie
function getNotifs() {
    //--------------------------------------REQUÊTE GET LES NOTIFIATIONS--------------------------------------
    let requeteGetNotifications = new XMLHttpRequest();

    //Configurer la requête, pour aller chercher les notifications ET les informations des transactions
    requeteGetNotifications.open('GET', '/TCH099_FishFric/Site_web/pageListeCompte/API/afficherNotifications.php', true);

    requeteGetNotifications.onload = function() {
        //Vérifier si la requête a marché
        if (requeteGetNotifications.readyState === 4 && requeteGetNotifications.status === 200) {

            //Décoder la réponse (qui est au format JSON)
            //Le JSON contient les infos de chaque notification et de la transaction représentant le virement
            let responseData = JSON.parse(requeteGetNotifications.responseText);
            let notificationEtTransaction = responseData.notificationsEtTransactions;
    
            //Afficher la liste des notifications de l'utilisateur en HTML et réinitialiser les notifs
            document.querySelector('.notif-container').innerHTML = '';

            //Boucler toutes les lignes de notifications/transaction
            for (let i = notificationEtTransaction.length - 1; i >= 0; i--) {
                //Mettre tout le code HTML de la structure d'une notification dans une string
                let notificationHtml = '<div class="notif-box" id="' + notificationEtTransaction[i].id_notif + '"><div class="notif-box-header">'
                notificationHtml += '<h4>' + notificationEtTransaction[i].titre + '</h4>';
                notificationHtml += '<button class="btn-supprimer" onclick="supprimerNotif(' + notificationEtTransaction[i].id_notif + ')"><i class="bx bx-trash"></i></button></div>'
                notificationHtml += '<div class="notif-box-body"><p>' + notificationEtTransaction[i].contenu + '</p>';

                //------------------------------AFFICHAGE RÉCEPTION DE VIREMENT DYNAMIQUEMENT------------------------------------
                if (notificationEtTransaction[i].titre == 'Virement reçu') {
                    //Ajouter le div de la réception du virement dans la notification
                    notificationHtml += '<form class="formulaire"><div class="label-field"><label for="quest_securite">';
                    notificationHtml += notificationEtTransaction[i].question + '</label></div>';
                    notificationHtml += '<div class="input-box"><label for="reponse">Réponse:</label>';
                    notificationHtml += '<input type="text" id="reponse-' + notificationEtTransaction[i].idTransaction +'"></div></form>';
                    notificationHtml += '<div class="btn-box"><button class="accepter" id="' + notificationEtTransaction[i].idTransaction +'">Accepter</button>';
                    notificationHtml += '<button class="rejeter" id="' + notificationEtTransaction[i].idTransaction +'">Rejeter</button></div>';
                    notificationHtml += '<div id="msg-erreur-' + notificationEtTransaction[i].idTransaction + '"></div>';
                    notificationHtml += '</div><div class="notif-box-footer"><p>' + notificationEtTransaction[i].dateRecu + '</p></div></div>';

                    //Ajouter le HTML au div
                    document.querySelector('.notif-container').innerHTML += notificationHtml;
                }

                //Sinon, juste mettre la date comme footer
                else {
                    //Mettre le footer avec la date de réception de la notification et ajouter le HTML au div
                    notificationHtml += '</div><div class="notif-box-footer"><p>' + notificationEtTransaction[i].dateRecu + '</p></div></div>';
                    document.querySelector('.notif-container').innerHTML += notificationHtml;
                }
            }

            //Ajouter écouteur d'événement aux boutons accepter/rejeter le virement
            document.querySelectorAll('.accepter').forEach(function(boutonAccepter) {
                boutonAccepter.addEventListener('click', function() {
                    //Chercher l'ID de transaction (classe du bouton)
                    let idTransaction = boutonAccepter.id;

                    recevoirVirement(idTransaction, 'accepter')
                });
            });
            

            document.querySelectorAll('.rejeter').forEach(function(boutonRejeter) {
                boutonRejeter.addEventListener('click', function() {
                    //Chercher l'ID de transaction (classe du bouton)
                    let idTransaction = boutonRejeter.id;

                    recevoirVirement(idTransaction, 'rejeter')
                });
            });
        }
    }

    //Message d'erreur de la requête
    requeteGetNotifications.onerror = function() {
        console.error('La requête n\'a pas fonctionné!');
    };

    //Envoyer la requête
    requeteGetNotifications.send();
}

//--------------------------FONCTION REQUÊTE PUT pour accepter/refuser un transfert----------------------------
//
function recevoirVirement(idTransaction, decision) {
    //On peut commencer notre requête
    requeteVirement = new XMLHttpRequest();
    requeteVirement.open('PUT', '/TCH099_FishFric/Site_web/Transfert/API/gestionTransfert.php/utilisateurReception', true);

    //Get la réponse
    let inputReponse = document.querySelector('#reponse-' + idTransaction).value;
    
    //Stocke les donnees a envoyer en format JSON
    requeteVirement.setRequestHeader('Content-Type', 'application/json');
    const donneesJsonVirement = JSON.stringify({"decision": decision,
                                                "idTransaction": idTransaction,
                                                "inputReponse": inputReponse});

    //Messages d'erreurs ou de succès du virement
    requeteVirement.onload = function() {
        //Vérifier si la requête a marché
        if (requeteVirement.readyState === 4 && requeteVirement.status === 200) {
            //Décoder la réponse (qui est au format JSON)
            let responseData = JSON.parse(requeteVirement.responseText);

            //Afficher les messages d'erreur ou de succès
            document.getElementById('msg-erreur-' + idTransaction).innerText = "";
            let msg = document.createElement('span');

            if ("msgSucces" in responseData) {
                //Recharger les notifications pour afficher notif de succès et afficher le nouveau solde du compte
                getNotifs();
                getComptes();

                let toast = document.createElement('div');
                toast.classList.add('toast');
                toast.classList.add('success');
                toast.innerHTML = '<i class="bx bxs-check-circle"></i>' + responseData.msgSucces;
                toastBox.appendChild(toast);

                //Fermer la fenêtre
                setTimeout(() => {
                    toast.remove()
                }, 4500);
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
}


//--------------------------REQUÊTE DELETE TOUTES LES NOTIFS----------------------------
//
document.querySelector('.clear-all').addEventListener('click', supprimerNotifs);

function supprimerNotifs() {
    //Requête DELETE
    deleteNotifs = new XMLHttpRequest();
    deleteNotifs.open('DELETE', '/TCH099_FishFric/Site_web/pageListeCompte/API/afficherNotifications.php', true);
    
    deleteNotifs.onload = function() {
        //Vérifier si la requête a marché
        if (deleteNotifs.readyState === 4 && deleteNotifs.status === 200) {
            //Décoder la réponse (qui est au format JSON)
            let responseData = JSON.parse(deleteNotifs.responseText);

            responseData.idNotifsEffacees.forEach(function(notification) {
                //Chercher toutes les notifs
                let notifs = document.querySelectorAll('.notif-box');

                //Itérer à travers les notifs, et les effacer si elles ont notre ID
                notifs.forEach(function(notif) {    
                    if(notif.id == notification.id)
                        notif.remove();
                })
            });

            getNotifs();
        }
    }

    //Message d'erreur de la requête
    deleteNotifs.onerror = function() {
        console.error('La requête n\'a pas fonctionné!');
    };

    //Envoyer la requête
    deleteNotifs.send();
}


//--------------------------REQUÊTE DELETE UNE NOTIF----------------------------
//
function supprimerNotif(idNotif) {
    console.log("Supprimer notif seule: " + idNotif)
    //Requête DELETE
    deleteNotif = new XMLHttpRequest();
    deleteNotif.open('DELETE', '/TCH099_FishFric/Site_web/pageListeCompte/API/afficherNotifications.php?idNotif=' + idNotif, true);

    deleteNotif.onload = function() {
        //Vérifier si la requête a marché
        if (deleteNotif.readyState === 4 && deleteNotif.status === 200) {
            //Décoder la réponse (qui est au format JSON)
            let responseData = JSON.parse(deleteNotif.responseText);

            responseData.idNotifsEffacees.forEach(function(notification) {
                //Chercher toutes les notifs
                let notifs = document.querySelectorAll('.notif-box');

                //Itérer à travers les notifs, et les effacer si elles ont notre ID
                notifs.forEach(function(notif) {    
                    if(notif.id == notification.id)
                        notif.remove();
                })
            });

            getNotifs();
        }
    }

    //Message d'erreur de la requête
    deleteNotif.onerror = function() {
        console.error('La requête n\'a pas fonctionné!');
    };

    //Envoyer la requête
    deleteNotif.send();
}