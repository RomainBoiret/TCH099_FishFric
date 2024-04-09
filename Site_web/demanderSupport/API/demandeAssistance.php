<?php
if(isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] === 'POST') {
    //Connection a la base de donnee
    try{
        require("../../connexion.php");
    }
    catch(Exception $e)
    {
        die("Connection echouee : " . $e->getMessage());
    }

    //Get les données du POST
    $donneesJSON = json_decode(file_get_contents("php://input"), true);

    //Gestion d'erreurs
    $erreurs = array();

    if(empty($donneesJSON['mobile']))
    {
        //Get toutes les données JSON
        $messageRecu = trim($donneesJSON['messageRecu']);

        //Chercher l'ID de l'utilisateur
        session_start();
        $idUtilisateur = $_SESSION["utilisateur"];

        $mobile = false;
    }

    else
    {
        //Get donnees JSON mobile
        $messageRecu = trim(implode($donneesJSON['messageRecu']));
        $idUtilisateur = trim(implode($donneesJSON['idUtilisateur']));

        $mobile = true;
    }


    //Vérifier le prénom
    if(empty($messageRecu))
        $erreurs[] = "Veuillez saisir un message";
    else
        $messageRecu = htmlspecialchars($messageRecu);


    //Si tout est valide, ajouter utilisateur a la base de données
    if(count($erreurs) == 0)
    {
        //Effectuer la requête pour créer le compte utilisateur
        $requete = $conn->prepare("INSERT INTO DemandeAssistance (compteId, messageRecu, dateDemande) 
        VALUES ('$idUtilisateur', '$messageRecu', NOW())");
        $requete->execute();

        if($mobile)
        {
            echo json_encode(['reponse'=>"Votre demande d'assistance a bien été envoyée ", 'code'=>'201']);
        }
        else
        {
        //Mettre le message de succès 
        echo json_encode(['msgSucces' => "Votre demande d'assistance a bien été envoyée ."]); 
        }
    
    }

    //Sinon, on affiche les erreurs
    else
    {
        if($mobile)
        {
            //HTTP CODE 401 Donnee eronnees
            $str = implode(',', $erreurs);


            echo json_encode(['reponse'=>"$str", 'code'=>'401']);
        }
        else
        {
            echo json_encode(['erreurs' => $erreurs]); 
        }
    }
} 
?>