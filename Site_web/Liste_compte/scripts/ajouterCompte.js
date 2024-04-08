document.getElementById('btnAjouterCompte').addEventListener('click', function() {
    //--------------------------------------REQUÊTE AJOUTER NOUVEAU COMPTE--------------------------------------

    //Créer la requête
    let requeteAjouterCompte = new XMLHttpRequest();
    requeteAjouterCompte.open('POST', '/TCH099_FishFric/Site_web/Liste_compte/API/ajouterCompte.php', true);

    //Chercher le compte à créer
    let typeCompte = '';
    let btnsChoix = document.querySelectorAll('#choix');


    btnsChoix.forEach(function(button) {
        if(button.checked) {
            //Le type du compte est le placeholder du radiobutton sélectionné
            typeCompte = button.placeholder;
        }
    })

    //Mettre les données JSON à envoyer
    requeteAjouterCompte.setRequestHeader('Content-Type', 'application/json');
    const donneesJSON = JSON.stringify({"typeCompte": typeCompte});

    //Récupérer les messages de succès/erreur de l'API
    requeteAjouterCompte.onload = function() {
        //Vérifier si la requête a marché
        if (requeteAjouterCompte.readyState === 4 && requeteAjouterCompte.status === 200) {

            //Décoder la réponse (qui est au format JSON)
            let responseData = JSON.parse(requeteAjouterCompte.responseText);

            if (responseData.succes) {
                //Changer le texte pour dire que le compte a bien été crée
                let toast = document.createElement('div');
                toast.classList.add('toast');
                toast.classList.add('success');
                toast.innerHTML = '<i class="bx bxs-check-circle"></i>' + responseData.succes;
                toastBox.appendChild(toast);

                setTimeout(() => {
                    toast.remove();
                }, 3000);

            }

            else {
                //Changer le texte pour dire que le compte a bien été crée
                let toast = document.createElement('div');
                toast.classList.add('toast');
                toast.classList.add('error');
                toast.innerHTML = '<i class="bx bxs-error-circle"></i>' + responseData.erreurs;
                toastBox.appendChild(toast);

                setTimeout(() => {
                    toast.remove();
                }, 3000);
            }
        }
    }

    //Message d'erreur de la requête
    requeteAjouterCompte.onerror = function() {
        console.error('La requête n\'a pas fonctionné!');
    };

    //Envoyer la requête
    requeteAjouterCompte.send(donneesJSON);
})