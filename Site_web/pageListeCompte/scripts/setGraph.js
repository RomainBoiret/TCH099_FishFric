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

            console.log(tableauData)

            // create a data set
            var dataSet = anychart.data.set(tableauData);

            // map the data for all series
            var firstSeriesData = dataSet.mapAs({x: 0, value: 1});
            var chart = anychart.line();

            var firstSeries = chart.line(firstSeriesData);

            chart.xAxis().title('Date');
            chart.yAxis().title('Solde');

            chart.container("graph-container");
            chart.background({fill: "#2F2F2F 3.5"});

            chart.title("Votre solde total");

            let title = chart.title();
            title.fontSize('25');
            title.fontFamily('Poppins');
            title.fontWeight('700');

            let labelX = chart.xAxis().title();
            labelX.fontSize('15');
            labelX.fontFamily('Poppins');
            labelX.fontWeight('700');

            let labelY = chart.yAxis().title();
            labelY.fontSize('15');
            labelY.fontFamily('Poppins');
            labelY.fontWeight('700');

            chart.draw();
        }
    }

    requeteGetGraph.onerror = function() {
        console.error('La requête n\'a pas fonctionné!');
    };

    //Envoyer la requête
    requeteGetGraph.send();




















    // var data = [
    // ["2003", 1],
    // ["2004", 4],
    // ["2005", 6],
    // ["2006", 9],
    // ["2007", 12],
    // ["2008", 1],
    // ["9", 1],
    // ["10", 4],
    // ["11", 6],
    // ["12", 9],
    // ["13", 12],
    // ["14", 1],
    // ["15", 1],
    // ["16", 4],
    // ["17", 6],
    // ["18", 9],
    // ["19", 12],
    // ["20", 1],
    // ["21", 1],
    // ["22", 1],
    // ["23", 4],
    // ["24", 6],
    // ["25", 9],
    // ["26", 12],
    // ["27", 1],

    // ];

    // // create a data set
    // var dataSet = anychart.data.set(tableauData);

    // // map the data for all series
    // var firstSeriesData = dataSet.mapAs({x: 0, value: 1});
    // var chart = anychart.line();

    // var firstSeries = chart.line(firstSeriesData);

    // chart.xAxis().title('Date');
    // chart.yAxis().title('Solde');

    // chart.container("graph-container");
    // chart.background({fill: "#2F2F2F 3.5"});

    // chart.title("Votre solde total");

    // let title = chart.title();
    // title.fontSize('25');
    // title.fontFamily('Poppins');
    // title.fontWeight('700');

    // let labelX = chart.xAxis().title();
    // labelX.fontSize('15');
    // labelX.fontFamily('Poppins');
    // labelX.fontWeight('700');

    // let labelY = chart.yAxis().title();
    // labelY.fontSize('15');
    // labelY.fontFamily('Poppins');
    // labelY.fontWeight('700');

    // chart.draw();
});