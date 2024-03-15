document.querySelector('.messagerie').addEventListener('click', function() {
    //Si le bouton de notification est cliqué
    if(!isClicked) {
        //--------------------------------------REQUÊTE GET LES NOTIFIATIONS--------------------------------------
        let requeteGetNotifications = new XMLHttpRequest();

        //Configurer la requête, pour aller chercher les notifications
        requeteGetNotifications.open('GET', '/Liste_compte/API/afficherNotifications.php', true);

        requeteGetNotifications.onload = function() {
            //Vérifier si la requête a marché
            if (requeteGetNotifications.readyState === 4 && requeteGetNotifications.status === 200) {

                //Décoder la réponse (qui est au format JSON)
                let responseData = JSON.parse(requeteGetNotifications.responseText);
        
                //Afficher la liste des notifications de l'utilisateur en HTML
                let notifications = '';
                responseData.notifications.forEach(function(notification) {
                    //Mettre tout le code HTML de la structure d'une notification dans une string
                    let notificationHtml = '<div class="notif-box" id="virement-transfert"><div class="notif-box-header">'
                    notificationHtml += '<h4>' + notification.titre + '</h4>';
                    notificationHtml += '<button class="btn-supprimer"><i class="bx bx-trash"></i></button></div>'
                    notificationHtml += '<div class="notif-box-body"><p>' + notification.contenu + '</p></div>';
                    notificationHtml += '<div class="notif-box-footer"><p>' + notification.dateRecu + '</p></div></div>';

                    notifications += notificationHtml;
                });

                //Afficher les notifications dans le div
                document.querySelector('.notif-container').innerHTML = notifications;
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