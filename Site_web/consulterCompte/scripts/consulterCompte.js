document.addEventListener("DOMContentLoaded", function() {

    //--------------------------------------REQUÊTE GET AFFICHER LES COMPTES--------------------------------------
    
    //Rechercher l'ID du compte dans l'URL
    const urlParams = new URLSearchParams(window.location.search);
    const compteId = urlParams.get('id');
    console.log(compteId)

    //Créer la requête
    let requeteGetComptes = new XMLHttpRequest();

    //Configurer la requête pour chercher les infos du compte en question
    requeteGetComptes.open('GET', '/consulterCompte/API/getCompte.php?compteId=' + compteId, true);

    requeteGetComptes.onload = function() {
        //Vérifier si la requête a marché
        if (requeteGetComptes.readyState === 4 && requeteGetComptes.status === 200) {

            //Décoder la réponse (qui est au format JSON)
            let responseData = JSON.parse(requeteGetComptes.responseText);
            let compte = responseData.compte;
            let transactions = responseData.transactions;

            //Afficher le compte dynamiquement
            let compteHtml = '<div class="detail-compte-header">';
            compteHtml += '<h2>' + compte.typeCompte + '</h2>';
            compteHtml += '<div class="montant-compte"><div class="montant">' + compte.solde;
            compteHtml += '</div></div></div>';
            compteHtml += '<p>Numéro de compte: ' + compte.id + '</p>';
            compteHtml += '<div class="detail-compte-footer"><p>Date d\'ouverture: ' + compte.ouverture + '</p>';
            compteHtml += '<p>Taux d\intérêt: ' + compte.interet + '</p></div>';                                                         


            //Afficher les comptes dans le div
            document.querySelector('.detail-compte').innerHTML = compteHtml;


            //Afficher les transactions de ce compte

            //..À FAIRE...
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
