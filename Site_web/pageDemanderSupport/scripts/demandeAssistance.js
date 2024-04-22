document.getElementById("soumettre").addEventListener('click', function() {
    //Créer requête POST 

    let xhrDemanderAssistance = new XMLHttpRequest();
    xhrDemanderAssistance.open('POST', '/TCH099_FishFric/Site_web/pageDemanderSupport/API/demandeAssistance.php', true);

    //Chercher les données
    let messageRecu = document.getElementById('messageRecu').value;

    //Mettre les données sous format JSON
    xhrDemanderAssistance.setRequestHeader('Content-Type', 'application/json');
    const creerCompteJSON = JSON.stringify({"messageRecu": messageRecu});

    console.log(creerCompteJSON)


    //Chercher la réponse (messages succès/erreur)
    xhrDemanderAssistance.onload = function() {
        //Vérifier si la requête a marché
        if (xhrDemanderAssistance.readyState === 4 && xhrDemanderAssistance.status === 200) {
            //Décoder la réponse (qui est au format JSON)
            let responseData = JSON.parse(xhrDemanderAssistance.responseText);
            console.log(responseData);

            //Afficher le message de succès dans le DIV respectif
            if ("msgSucces" in responseData) {
                //Afficher message succès
                Swal.fire({
                    title: "Message envoyé !",
                    text: "Votre demande de support a été soumise avec succès !",
                    icon: "success",
                    customClass: {
                        container: 'my-swal-container'
                    }
                });

                document.getElementById('titreRecu').value = "";
                document.getElementById('messageRecu').value = "";
            }

            else {
                //Afficher erreurs MDP
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
                    })
                }
            }
        } 

        else {
            //Afficher l'erreur s'il y a lieu
            console.error('Request failed with status code:', xhrDemanderAssistance.status);
        }
    }

    //Message d'erreur de la requête
    xhrDemanderAssistance.onerror = function() {
        console.error('La requête n\'a pas fonctionné!');
    };

    //Envoyer la requête
    xhrDemanderAssistance.send(creerCompteJSON);
});

// Effacer message
document.getElementById("effacer").addEventListener('click', function() {

    document.getElementById('titreRecu').value = "";
    document.getElementById('messageRecu').value = "";
});