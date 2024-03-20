document.getElementById("btnCreerCompte").addEventListener('click', function() {
    //Créer requête POST nouveau compte

    let xhrCreerCompte = new XMLHttpRequest();
    xhrCreerCompte.open('POST', '/Creer_un_compte/API/apiCreerCompte.php', true);

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

            //Afficher les messages d'erreur ou de succès
            document.getElementById('messages').innerHTML = "";

            //Afficher le message de succès dans le DIV respectif
            if ("msgSucces" in responseData) {
                //Désactiver le bouton pour ne pas créer de nouveau compte
                document.getElementById('btnCreerCompte').setAttribute('disabled', 'true');

                //Ajouter un div pour y afficher le message de succès
                let msgDiv = document.createElement('div');
                msgDiv.id = 'msg-succes';
                document.getElementById('messages').appendChild(msgDiv);
                msgDiv.innerHTML = responseData.msgSucces;
            }

            else {
                //Afficher grande erreur de MDP 
                if (responseData.erreurMdp) {
                    //Ajouter un div pour y afficher l'erreur de mot de passe
                    let erreurMdpDiv = document.createElement('div');
                    erreurMdpDiv.id = 'erreur-mdp';
                    document.getElementById('messages').appendChild(erreurMdpDiv);
                    erreurMdpDiv.innerHTML = responseData.erreurMdp;
                }

                if (responseData.erreurs) {
                    //Afficher le reste des erreurs
                    let erreurDiv = document.createElement('div');
                    erreurDiv.id = 'erreurs-reste';
                    document.getElementById('messages').appendChild(erreurDiv);
                    erreurDiv.innerHTML = responseData.erreurs;
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
})