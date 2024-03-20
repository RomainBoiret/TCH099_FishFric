<?php
//TRAITEMENT REQUÊTE POST CONNEXION
if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST')
{
    //Connection a la base de donnee
    try {
        require("../../connexion.php");
    } catch(Exception $e) {
        die("Connection echouee : " . $e->getMessage());
    }

    session_start();

    //Inclure fichier qui contient la fonction de décryption
    include "../../Encryption/encryption.php";

    //Get les données JSON du POST
    $donneesJSON = json_decode(file_get_contents("php://input"), true);
    $courriel = trim($donneesJSON['courriel']);
    $password = trim($donneesJSON['password']);
    $checked = trim($donneesJSON['checked']);

    //Gestion d'erreurs
    $erreurs = array();

    //Vérifier le courriel
    if(empty($courriel))
        $erreurs[] = "Le courriel saisi est invalide";
    else
        $courriel = htmlspecialchars($courriel);

    //Vérifier le mot de passe
    if(empty($password))
        $erreurs[] = "Le mot de passe saisi est invalide";
    else
        $password = htmlspecialchars($password);

    //S'il n'y a pas d'erreurs, on peut faire la vérification
    if(empty($erreurs)) {
        //Vérifier si le numéro de compte existe dans la BD
        $requete = $conn->prepare("SELECT * FROM Compte WHERE courriel = '$courriel'");
        $requete->execute();
        $resultat = $requete->fetch(PDO::FETCH_ASSOC);

        //Si aucun utilisateur avec le courriel fourni existe 
        if (!$resultat) {
            //On affiche l'erreur que l'utilisateur est inexistant
            $erreurs[] = "L'utilisateur saisi n'existe pas!";
            echo json_encode(['erreurs' => $erreurs]); 
        }

        else {
            //Aller chercher le mot de passe dans la base de données correspondant au courriel
            $requete = "SELECT motDePasse FROM Compte WHERE courriel = '$courriel'";
            $resultat = $conn->query($requete);
            $resultat = $resultat->fetchColumn();

            //Verfie si le mot de passe saisi correspond au mot de passe hashed de la BD
            if(AES256CBC_decrypter($resultat, CLE_ENCRYPTION) == $password)
            {
                //Si le mot de passe est bon, on envoie l'utilisateur vers la page de ses comptes et commence sa session
                //D'abord chercher l'ID de l'utilisateur
                $requete = "SELECT id FROM Compte WHERE courriel LIKE '$courriel'";
                $resultat = $conn->query($requete);
                $id = $resultat->fetchColumn();

                //Vérifier si l'utilisateur veut rester connecté plus longtemps
                if($checked) {
                    //Laisser la session active pour 8 heures;
                    $_SESSION['DUREE_SESSION'] = 60*60*8; 
                } else {
                    //la durée d'une session est de 300 secondes
                    $_SESSION['DUREE_SESSION'] = 300; 
                }

                //Chercher le nom de l'utilisateur
                $requete = "SELECT prenom FROM Compte WHERE courriel LIKE '$courriel'";
                $resultat = $conn->query($requete);
                $nomUtilisateur = $resultat->fetchColumn();


                //Mettre des variables de session pour la session de l'utilisateur et son temps d'activité
                $_SESSION["utilisateur"] = $id;
                $_SESSION["nomUtilisateur"] = $nomUtilisateur;
                $_SESSION['LAST_ACTIVITY'] = time(); 

                echo json_encode(['succes' =>  $id]);
                // header("Location: ../../Liste_compte/listeCompte.php");
                // exit(); 
            } 

            else {
                $erreurs[] = "Le mot de passe est erroné!";
                echo json_encode(['erreurs' => $erreurs]);      
            }
        }
    } else {
        //On envoie les erreurs en JSON
        echo json_encode(['erreurs' => $erreurs]); 
    }
}
?>