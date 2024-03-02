document.addEventListener("DOMContentLoaded", function() {

    //--------------------------------------AFFICHER LES COMPTES--------------------------------------
    
    //Créer la requête
    let request = new XMLHttpRequest();

    //Configurer la requête, pour aller chercher les comptes
    request.open('GET', '/Liste_compte/API/afficherComptes.php', true);

    request.onload = function() {
        //Vérifier si la requête a marché
        if (request.readyState === 4 && request.status === 200) {

            //Décoder la réponse (qui est au format JSON)
            let responseData = JSON.parse(request.responseText);
            console.log("Réponse: " + responseData);

            //Afficher les comptes en HTML
            let comptes = '';
            responseData.comptes.forEach(function(compte) {
                console.log("Réponse: " + compte);


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
        } 
        
        else {
            //Afficher l'erreur s'il y a lieu
            console.error('Request failed with status code:', request.status);
        }
    };

    // Define a function to handle network errors
    request.onerror = function() {
        console.error('Request failed to reach the server');
    };

    // Send the request
    request.send();
});