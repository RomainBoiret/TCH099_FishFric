document.addEventListener("DOMContentLoaded", function() {
    //--------------------------------------REQUÊTE GET AFFICHER LE COMPTE ET LES TRANSACTIONS--------------------------------------
    
    //Rechercher l'ID du compte dans l'URL
    const urlParams = new URLSearchParams(window.location.search);
    const compteId = urlParams.get('id');

    //Créer la requête
    let requeteGetCompte = new XMLHttpRequest();

    //Configurer la requête pour chercher les infos du compte en question
    requeteGetCompte.open('GET', '/TCH099_FishFric/Site_web/pageConsulterCompte/API/getCompte.php?compteId=' + compteId, true);

    requeteGetCompte.onload = function() {
        //Vérifier si la requête a marché
        if (requeteGetCompte.readyState === 4 && requeteGetCompte.status === 200) {

            //------------------------AFFICHER LE COMPTE--------------------------

            //Décoder la réponse (qui est au format JSON)
            let responseData = JSON.parse(requeteGetCompte.responseText);
            let compte = responseData.compte;
            let transactions = responseData.transactions;

            //Afficher le compte dynamiquement
            let compteHtml = '<div class="detail-compte-header">';
            compteHtml += '<h2>' + compte.typeCompte + '</h2>';
            compteHtml += '<div class="montant-compte"><div class="montant">' + compte.solde;
            compteHtml += '</div></div></div>';
            compteHtml += '<p>Numéro de compte: ' + compte.id + '</p>';
            compteHtml += '<div class="detail-compte-footer"><p>Date d\'ouverture: ' + compte.ouverture + '</p>';
            compteHtml += '<p>Taux d\'intérêt quotidien: ' + compte.interet + '%</p></div>';                                                         

            //Afficher les comptes dans le div
            document.querySelector('.detail-compte').innerHTML = compteHtml;
            let transactionHtml = '';

            //------------------------------------AFFICHER LES 4 PREMIÈRES TRANSACTIONS-----------------------------------------

            //De base, on affiche seulement 4 transactions
            let nbTransactions = 4;

            //Afficher les transactions de ce compte, à partir de la plus récente (length-1)
            for (let i = transactions.length - 1; i >= transactions.length - nbTransactions; i--) {
                //Si on a pas un indice négatif, on continue à afficher les transactions
                if (i >= 0) {
                    //Afficher le type de la transaction
                    transactionHtml += '<div class="transfert-box"><div class="transfert-detail">';
                    transactionHtml += '<div class="detail-titre"><span>' + transactions[i].typeTransaction;

                    //S'il y a un nom d'établissement ou de contact pour le transfert
                    if (transactions[i].nomEtablissement) {
                        transactionHtml += ' / ';

                        //Si le compte présent est le nom d'établissement:
                        if (transactions[i].idCompteBancaireRecevant == compteId)
                            transactionHtml += transactions[i].courrielProvenant.toLowerCase();
                        else
                            transactionHtml += transactions[i].nomEtablissement.toLowerCase();
                    }

                    //Si c'est des intérets, on met rien
                    else if (transactions[i].typeTransaction == 'Intérêts' || transactions[i].typeTransaction == 'Dépôt mobile');

                    //Sinon, il s'agit d'un transfert entre comptes. Afficher le compte recevant
                    else {
                        transactionHtml += ' / ';
                        
                        if (transactions[i].idCompteBancaireProvenant == compteId)
                            transactionHtml += 'compte #' + transactions[i].idCompteBancaireRecevant
                        else 
                            transactionHtml += 'compte #' + transactions[i].idCompteBancaireProvenant
                    }

    
                    //Afficher la date de la transaction
                    transactionHtml += '</span></div>';
                    transactionHtml += '<div class="detail-date">' + transactions[i].dateTransaction + '</div></div>';
                    transactionHtml += '<div class="transfert-montant-';
    
                    //Déterminer si c'est un envoi ou une réception de fonds, pour mettre le style et signe respectif
                    if (compteId == transactions[i].idCompteBancaireProvenant)
                        transactionHtml += 'negatif"> - ';
                    else 
                        transactionHtml += 'positif"> + ';
                    
                    //Afficher le montant de la transaction
                    transactionHtml += transactions[i].montant + '<i class="bx bx-dollar"></i></div></div>';
                }
            }

            //Afficher les transactions dans la div respective
            document.querySelector('.transfert-content').innerHTML = transactionHtml;

            //-----------------------------------AFFICHER TRANSACTIONS PLUS ANCIENNES-----------------------------------
            //
            //Lorsque l'utilisateur clique sur le bouton "+", on affiche 5 transactions plus anciennes dans la div
            document.querySelector('.btn-voir-plus').addEventListener('click', function() {
                //incrémenter le nombre de transactions que l'on désire voir
                nbTransactions += 5;

                transactionHtml = '';
                for (let i = transactions.length - 1; i >= transactions.length - nbTransactions; i--) {
                    //Si on a pas un indice négatif, on continue à afficher les transactions
                    if (i >= 0) {
                        transactionHtml += '<div class="transfert-box"><div class="transfert-detail">';
                        transactionHtml += '<div class="detail-titre"><span>' + transactions[i].typeTransaction + ' / ';
        
                        //S'il y a un nom d'établissement ou de contact pour le transfert
                        if (transactions[i].nomEtablissement) {
                            //Si le compte présent est le nom d'établissement:
                            if (transactions[i].idCompteBancaireRecevant == compteId)
                                transactionHtml += transactions[i].courrielProvenant.toLowerCase();
                            else
                                transactionHtml += transactions[i].nomEtablissement.toLowerCase();
                        }

                        //Si c'est des intérets, on met rien
                        else if (transactions[i].typeTransaction == 'Intérêts' || transactions[i].typeTransaction == 'Dépôt mobile');

                        //Sinon, il s'agit d'un transfert entre comptes. Afficher le compte recevant
                        else {
                            if (transactions[i].idCompteBancaireProvenant == compteId)
                                transactionHtml += 'compte #' + transactions[i].idCompteBancaireRecevant
                            else 
                                transactionHtml += 'compte #' + transactions[i].idCompteBancaireProvenant
                        }
        
                        transactionHtml += '</span></div>';
                        transactionHtml += '<div class="detail-date">' + transactions[i].dateTransaction + '</div></div>';
                        transactionHtml += '<div class="transfert-montant-'
        
                        //Déterminer si c'est un envoi ou une réception de fonds, pour mettre le style et signe respectif
                        if (compteId == transactions[i].idCompteBancaireProvenant)
                            transactionHtml += 'negatif"> - '
                        else 
                            transactionHtml += 'positif"> + '
        
                        transactionHtml += transactions[i].montant + '<i class="bx bx-dollar"></i></div></div>';
                    }
                }

                //Afficher les transactions dans la div respective
                document.querySelector('.transfert-content').innerHTML = transactionHtml;
            })

        }
        
        else {
            //Afficher l'erreur de la requête GET s'il y a lieu
            console.error('Request failed with status code:', requeteGetCompte.status);
        }
    };

    //Message d'erreur de la requête
    requeteGetCompte.onerror = function() {
        console.error('La requête n\'a pas fonctionné!');
    };

    //Envoyer la requête
    requeteGetCompte.send();
});