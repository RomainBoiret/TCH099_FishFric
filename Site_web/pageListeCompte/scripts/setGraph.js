anychart.onDocumentReady(function() {
    //Requête pour get les données de l'API
    requeteGetGraph = new XMLHttpRequest();
    requeteGetGraph.open('GET', '/TCH099_FishFric/Site_web/pageListeCompte/API/sommeComptesMobile.php', true);

    let tableauData = [];
    //Messages d'erreurs ou de succès du virement
    requeteGetGraph.onload = function() {
        //Vérifier si la requête a marché
        if (requeteGetGraph.readyState === 4 && requeteGetGraph.status === 200) {
            //Décoder la réponse (qui est au format JSON)
            let responseData = JSON.parse(requeteGetGraph.responseText);

            let jsonSoldes = responseData.sommes;
            

            //Itérer les sommes pour les stocker dans des tableaux
            jsonSoldes.forEach(element => {
                let tableauJour = Array(element["dateSolde"], element["solde"]);
                console.log(tableauJour)

                tableauData.push(tableauJour);
            });


            //Créer le dataset
            let dataSet = anychart.data.set(tableauData);

            //Faire la ligne
            let firstSeriesData = dataSet.mapAs({x: 0, value: 1});
            let chart = anychart.line();

            //Mettre le titre de la ligne
            let firstSeries = chart.line(firstSeriesData);
            firstSeries.name("Solde total");

            //Nom de l'axe des Y
            chart.yAxis().title('Solde');

            //Mettre background
            chart.container("graph-container");
            chart.background({fill: "#f0f3fa 3.5"});

            //Set le style du titre
            chart.title("Votre solde total");
            let title = chart.title();
            title.fontSize('25');
            title.fontFamily('Poppins');
            title.fontWeight('700');

            title.useHtml(true);
            title.text(
                "<p style=\"color:#2b2d42;\">Votre solde total</p>"
            )

            //Set le style du label de l'axe Y
            let labelY = chart.yAxis().title();
            labelY.fontSize('15');
            labelY.fontFamily('Poppins');
            labelY.fontWeight('700');

            labelY.useHtml(true);
            labelY.text(
                "<p style=\"color:#2b2d42;\">Solde</p>"
            )

            //Dessiner le tableau
            chart.draw();
        }
    }

    requeteGetGraph.onerror = function() {
        console.error('La requête n\'a pas fonctionné!');
    };

    //Envoyer la requête
    requeteGetGraph.send();
});
