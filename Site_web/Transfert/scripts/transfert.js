document.querySelector('.btn-virer').addEventListener('click', function() {
        //Vérifier que le solde est suffisant pour le transfert
        let montant = document.querySelector('montant').innerText;
        let solde = document.querySelector('solde').innerText;

        if(solde-montant < 0) 
            //Le solde n'est pas suffisant, mettre erreur
            document.getElementById('erreur-div').innerText = "Le solde du compte sélectionné n'est pas suffisant!";
        
        else {
            //Créer la requête
            let request = new XMLHttpRequest();

            //Configurer la requête pour effectuer le virement
            request.open('PUT', '/Transfert/API/gestionTransfert.php/compte', true);
            request.setRequestHeader('Content-Type', 'application/json');

            //Stocke les donnees a envoyer en format JSON
            const requestJSON = JSON.stringify({"montant": montant,
                                                "idCompteBancaireProvenant": compte1,
                                                "idCompteBancaireRecevant": compte2});
        
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
            request.send(requestJSON);
        }
})