document.querySelector('.messagerie').addEventListener('click', function() {
    //Si le bouton de notification est cliqué
    if(!isClicked) {
        //--------------------------------------REQUÊTE GET LES NOTIFIATIONS--------------------------------------
        let requeteGetNotifications = new XMLHttpRequest();

        //Configurer la requête, pour aller chercher les notifications ET les informations des transactions
        requeteGetNotifications.open('GET', '/Liste_compte/API/afficherNotifications.php', true);

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
                    let notificationHtml = '<div class="notif-box" id="' + notificationEtTransaction[i].idTransaction + '"><div class="notif-box-header">'
                    notificationHtml += '<h4>' + notificationEtTransaction[i].titre + '</h4>';
                    notificationHtml += '<button class="btn-supprimer"><i class="bx bx-trash"></i></button></div>'
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

                        recevoirVirement(idTransaction, 'accepter')
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
});



//--------------------------FONCTION REQUÊTE PUT pour accepter/refuser un transfert----------------------------
function recevoirVirement(idTransaction, decision) {
    //On peut commencer notre requête
    requeteVirement = new XMLHttpRequest();
    requeteVirement.open('PUT', '/Transfert/API/gestionTransfert.php/utilisateurReception', true);

    //Get la réponse
    console.log("idTransaction: " + idTransaction)
    let inputReponse = document.querySelector('#reponse-' + idTransaction).value;
    console.log(inputReponse)
    
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
                //Enlever le formulaire et les boutons
                document.getElementById(idTransaction).children[1].children[1].remove();
                document.getElementById(idTransaction).children[1].children[1].remove();

                //Changer le texte pour dire que le virement a été accepté/refusé
                document.getElementById(idTransaction).children[1].children[0].innerText = responseData.msgSucces;
            }

            else {
                responseData.erreur.forEach(function(message) {
                    msg.innerText = message;
                    msg.style.color = "red";
                    document.getElementById('msg-erreur-' + idTransaction).appendChild(msg);
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

//À FAIRE
//  -Delete TOUTES les notifs, SAUF celles qui sont un virement
//  - Une notif contient un virement SI ELLE CONTIENT UN FORM (!delete if parent contains form)