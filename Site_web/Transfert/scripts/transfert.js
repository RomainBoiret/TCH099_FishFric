document.querySelector('.btn-virer').addEventListener('click', function() {
        //chercher l'ID du compte source



        //Chercher le solde du compte source du transfert
        let requeteSolde = new XMLHttpRequest();
        requeteSolde.open('GET', '/Liste_compte/API/afficherComptes.php', true);

        requeteSolde.onload = function() {
            //Vérifier si la requête a marché
            if (requeteSolde.readyState === 4 && requeteSolde.status === 200) {

                //Décoder la réponse (qui est au format JSON)
                let responseData = JSON.parse(requeteSolde.responseText);

                responseData.comptes.forEach(function(compte) {
                    if (compte.id) {} //Trouver le compte ID qui correspond au compte source
                });
            } 
            
            else {
                //Afficher l'erreur s'il y a lieu
                console.error('Request failed with status code:', request.status);
            }
        };

        //Message d'erreur de la requête
        requeteSolde.onerror = function() {
            console.error('Request failed to reach the server');
        };

        //Envoyer la requête
        requeteSolde.send();

        //Vérifier s'il y a assez de solde pour effectuer le virement
        const montant = document.getElementById('montant-virement').innerText;

        //Créer la requête
        let request = new XMLHttpRequest();

        //Configurer la requête pour effectuer le virement
        request.open('POST', '/Transfert/API/gestionTransfert.php/compte', true);
        request.setRequestHeader('Content-Type', 'application/json');

        //Stocke les donnees a envoyer en format JSON
        const requestJSON = JSON.stringify({"montant": titre,
                                            "contenu": contenu});
    
        request.onload = function() {
            //Vérifier si la requête a marché
            if (request.readyState === 4 && request.status === 200) {

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
})