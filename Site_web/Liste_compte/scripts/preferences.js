//-----------Modification de courriel---------
document.getElementById('btnNouveauCourriel').addEventListener('click', function() {
    //Chercher le courriel inscrit par l'utilisateur
    let nouveauCourriel = document.getElementById('inputNouveauCourriel').value;

    requeteNouvelEmail = new XMLHttpRequest();
    requeteNouvelEmail.open('PUT', '/TCH099_FishFric/Site_web/Liste_compte/API/preferences.php/courriel', true);
    
    //Stocke les donnees a envoyer en format JSON
    requeteNouvelEmail.setRequestHeader('Content-Type', 'application/json');
    const donneesJson = JSON.stringify({"nouveauCourriel": nouveauCourriel});

    console.log("ID:" + nouveauCourriel)

    //Messages d'erreurs ou de succès du virement
    requeteNouvelEmail.onload = function() {
        //Vérifier si la requête a marché
        if (requeteNouvelEmail.readyState === 4 && requeteNouvelEmail.status === 200) {
            //Décoder la réponse (qui est au format JSON)
            let responseData = JSON.parse(requeteNouvelEmail.responseText);

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
    requeteNouvelEmail.onerror = function() {
        console.error('La requête n\'a pas fonctionné!');
    };

    //Envoyer la requête
    requeteNouvelEmail.send(donneesJson);
})