//-----------------------------Modification de courriel---------------------------
document.getElementById('btnNouveauCourriel').addEventListener('click', function() {
    //Chercher le courriel inscrit par l'utilisateur
    let nouveauCourriel = document.getElementById('inputNouveauCourriel').value;

    requeteNouvelEmail = new XMLHttpRequest();
    requeteNouvelEmail.open('PUT', '/TCH099_FishFric/Site_web/pageListeCompte/API/preferences.php/courriel', true);
    
    //Stocke les donnees a envoyer en format JSON
    requeteNouvelEmail.setRequestHeader('Content-Type', 'application/json');
    const donneesJson = JSON.stringify({"nouveauCourriel": nouveauCourriel});

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




//-----------------------------Modification de mot de passe---------------------------
document.getElementById('btnNouveauMdp').addEventListener('click', function() {
    //Chercher le courriel inscrit par l'utilisateur
    let nouveauMdp = document.getElementById('inputNouveauMdp').value;

    requeteNouveauMdp = new XMLHttpRequest();
    requeteNouveauMdp.open('PUT', '/TCH099_FishFric/Site_web/pageListeCompte/API/preferences.php/mdp', true);
    
    //Stocke les donnees a envoyer en format JSON
    requeteNouveauMdp.setRequestHeader('Content-Type', 'application/json');
    const donneesJson = JSON.stringify({"nouveauMdp": nouveauMdp});

    //Messages d'erreurs ou de succès du virement
    requeteNouveauMdp.onload = function() {
        //Vérifier si la requête a marché
        if (requeteNouveauMdp.readyState === 4 && requeteNouveauMdp.status === 200) {
            //Décoder la réponse (qui est au format JSON)
            let responseData = JSON.parse(requeteNouveauMdp.responseText);

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
    requeteNouveauMdp.onerror = function() {
        console.error('La requête n\'a pas fonctionné!');
    };

    //Envoyer la requête
    requeteNouveauMdp.send(donneesJson);
})




//-----------------------------Supprimer compte fish&fric---------------------------
document.getElementById('btnSupprimerCompte').addEventListener('click', function() {
    requeteDelete = new XMLHttpRequest();
    requeteDelete.open('DELETE', '/TCH099_FishFric/Site_web/pageListeCompte/API/preferences.php', true);

    //Messages d'erreurs ou de succès du virement
    requeteDelete.onload = function() {
        //Vérifier si la requête a marché
        if (requeteDelete.readyState === 4 && requeteDelete.status === 200) {
            //Décoder la réponse (qui est au format JSON)
            let responseData = JSON.parse(requeteDelete.responseText);

            //Afficher un message de succès si la reqûete renvoie "msgSucces"
            if ("msgSucces" in responseData) {
                //Rediriger la personne vers la page de connexion
                window.location.href = '/TCH099_FishFric/Site_web/pageConnexion/pageConnexion.php';
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
    requeteDelete.onerror = function() {
        console.error('La requête n\'a pas fonctionné!');
    };

    //Envoyer la requête
    requeteDelete.send();
})