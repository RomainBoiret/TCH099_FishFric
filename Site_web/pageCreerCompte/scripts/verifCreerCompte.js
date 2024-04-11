document.getElementById("btnCreerCompte").addEventListener('click', function() {
    //Créer requête POST nouveau compte

    let xhrCreerCompte = new XMLHttpRequest();
    xhrCreerCompte.open('POST', '/TCH099_FishFric/Site_web/pageCreerCompte/API/apiCreerCompte.php', true);

    //Chercher les données
    let nom = document.getElementById('nom').value;
    let prenom = document.getElementById('prenom').value;
    let courriel = document.getElementById('courriel').value;
    let password = document.getElementById('password').value;
    let conf_password = document.getElementById('conf_password').value;

    //Mettre les données sous format JSON
    xhrCreerCompte.setRequestHeader('Content-Type', 'application/json');
    const creerCompteJSON = JSON.stringify({"nom": nom,
                                            "prenom": prenom,
                                            "courriel": courriel,
                                            "password": password,
                                            "conf_password": conf_password});

    console.log(creerCompteJSON)


    //Chercher la réponse (messages succès/erreur)
    xhrCreerCompte.onload = function() {
        //Vérifier si la requête a marché
        if (xhrCreerCompte.readyState === 4 && xhrCreerCompte.status === 200) {
            //Décoder la réponse (qui est au format JSON)
            let responseData = JSON.parse(xhrCreerCompte.responseText);
            console.log(responseData);

            //Afficher le message de succès dans le DIV respectif
            if ("msgSucces" in responseData) {
                //Désactiver le bouton pour ne pas créer de nouveau compte
                document.getElementById('btnCreerCompte').setAttribute('disabled', 'true');
                                 
                let toast = document.createElement('div');
                toast.classList.add('toast');
                toast.classList.add('success');
                toast.innerHTML = '<i class="bx bxs-check-circle"></i>' + responseData.msgSucces;
                toastBox.appendChild(toast);

                setTimeout(() => {
                    toast.remove();
                }, 4500);
            }

            else {
                //Afficher grande erreur de MDP 
                if (responseData.erreurMdp != '') {
                    let toast = document.createElement('div');
                    toast.classList.add('toast');
                    toast.style.height = '100px';

                    toast.classList.add('error');
                    toast.innerHTML = '<i class="bx bxs-error-circle"></i>' + responseData.erreurMdp;
                    toastBox.appendChild(toast);
    
                    setTimeout(() => {
                        toast.remove();
                    }, 4500);
                }

                if (responseData.erreurs) {
                    responseData.erreurs.forEach(function(erreur) {

                        let toast = document.createElement('div');
                        toast.classList.add('toast');
                        toast.classList.add('error');
                        toast.innerHTML = '<i class="bx bxs-error-circle"></i>' + erreur;
                        toastBox.appendChild(toast);
    
                        setTimeout(() => {
                            toast.remove();
                        }, 4500);
                    });
                }
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
});