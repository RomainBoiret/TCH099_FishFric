document.getElementById("btnConnexion").addEventListener('click', function() {
    //Créer requête POST nouveau compte

    let xhrCreerCompte = new XMLHttpRequest();
    xhrCreerCompte.open('POST', '/TCH099_FishFric/Site_web/pageConnexion/API/apiConnexion.php', true);

    //Chercher les données
    let courriel = document.getElementById('courriel').value;
    let password = document.getElementById('password').value;
    let checked = document.getElementById('remember_account').checked;

    //Mettre les données sous format JSON
    xhrCreerCompte.setRequestHeader('Content-Type', 'application/json');
    const creerCompteJSON = JSON.stringify({"courriel": courriel,
                                            "password": password,
                                            "checked": checked,
                                            "mobile": null});

    //Chercher la réponse (messages succès/erreur)
    xhrCreerCompte.onload = function() {
        //Vérifier si la requête a marché
        if (xhrCreerCompte.readyState === 4 && xhrCreerCompte.status === 200) {
            //Décoder la réponse (qui est au format JSON)
            let responseData = JSON.parse(xhrCreerCompte.responseText);

            //Supprimer TOUS les messages avant d'en rajouter, dans le cas où on soumet plusieurs requêtes
            // document.getElementById('erreur-message').innerHTML = "";

            //Afficher le message de succès dans le DIV respectif
            if ("erreurs" in responseData) {
                responseData.erreurs.forEach(function(erreur) {                    
                    let toast = document.createElement('div');
                    toast.classList.add('toast');
                    toast.innerHTML = '<i class="bx bxs-error-circle"></i>' + erreur;
                    toastBox.appendChild(toast);

                    setTimeout(() => {
                        toast.remove();
                    }, 4500);
                })
            }

            else {
                //Rediriger utilisateur vers le site
                window.location = "../pageListeCompte/pageListeCompte.php";
            }
        } 

        else {
            //Afficher l'erreur s'il y a lieu
            console.error('Request failed with status code:', xhrCreerCompte.status);
        }
    }

    //Message d'erreur de la requête
    xhrCreerCompte.onerror = function() {
        console.error('La requête n\'a pas fonctionné!');
    };

    //Envoyer la requête
    xhrCreerCompte.send(creerCompteJSON);

    //Notif Toast
    let toastbox = document.getElementById('toastBox');
})
