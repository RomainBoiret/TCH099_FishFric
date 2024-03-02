<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentification</title>
    <link rel="stylesheet" href="./styles/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
<main>

<section>
    <div class="main-container">
        <div class="create-account-container">
            <div class="create-account-header">
                <h1>Créer un compte Fish&Fric</h1>
            </div>

            <div class="create-account-embed">
                <h2>Compte courant (compte chèques)</h2>

                <p>Notre compte courant est parfait pour faire vos opérations de tous les jours.
                    Vous pourrez choisir un autre type de compte ultérieurement</p>
            </div>

            <div class="account-formulaire">
                <form action="creerCompte.php" method="post" class="formulaire">
                    <div class="input-content">
                        <div class="input-box">
                            <div class="input-field">
                                <i class='bx bxs-user'></i>
                                <input type="text" name="nom" placeholder="..." required>
                                <label for="nom">Nom de famille</label>
                            </div>
    
                            <div class="input-field">
                                <i class='bx bx-user' ></i>
                                <input type="text" name="prenom" placeholder="..." required>
                                <label for="prenom">Prénom</label>
                            </div>
    
                            <div class="input-field">
                                <i class='bx bxs-phone'></i>
                                <input type="text" name="telephone" placeholder="..." required>
                                <label for="telephone">Numéro de téléphone</label>
                            </div>
                        </div>
                
                        <div class="input-box">
                            <div class="input-field">
                                <i class='bx bxs-envelope'></i>
                                <input type="text" name="courriel" placeholder="..." required>
                                <label for="courriel">Adresse courriel</label>
                            </div>
    
                            <div class="input-field">
                                <i class='bx bxs-lock'></i>
                                <input type="password" name="password" placeholder="..." required>
                                <label for="password">Mot de passe</label>
                            </div>
    
                            <div class="input-field">
                                <i class='bx bxs-lock'></i>
                                <input type="password" name="conf_password" placeholder="..." required>
                                <label for="conf_password">Confirmation du mot de passe</label>
                            </div>
                        </div>
                    </div>
                    
                    <div id="messages">
                        <div id="erreur-mdp"></div>
                        <div id="erreurs-reste"></div>
                        <div id="msg-succes"></div>
                    </div>

                    <div class="btn-box">
                        <button type="submit" class="btn">Créer</button>
                        <button type="reset" class="btn">Effacer</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="illustration-container">
            <h1>Vous êtes déjà membre ?</h1>

            <button class="btn-connecter"  onclick="window.location.href='../Connexion/page_connexion.php'"><a>Se connecter</a></button>
        </div>
    </div>
</section>
</main>
</body>
</html>

<?php
    if(isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == 'POST') {
        //Connection a la base de donnee
        try{
            require("../connexion.php");
        }
        catch(Exception $e)
        {
            die("Connection echouee : " . $e->getMessage());
        }

        //Inclure fichier qui contient la fonction d'encryption
        include "../Encryption/encryption.php";

        //Gestion d'erreurs
        $erreurs = array();
        $erreurMdp = array();

        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $telephone = trim($_POST['telephone']);
        $courriel = trim($_POST['courriel']);
        $password = trim($_POST['password']);
        $confirmation_mdp = trim($_POST['conf_password']);

        if(empty($prenom) || is_numeric($prenom))
            $erreurs[] = "Le prenom saisi est invalide";

        if(empty($nom) || is_numeric($nom))
            $erreurs[] = "Le nom saisi est invalide";

        if(empty($telephone) || !is_numeric($telephone) || strlen($telephone) != 10) 
            $erreurs[] = "Le numéro de téléphone saisi est invalide";
        else {
            //Vérifier si le numéro est déjà dans la base de données
            $requete = $conn->prepare("SELECT * FROM Compte WHERE telephone = $telephone");
            $requete->execute();
    
            if($requete->rowCount() != 0)
                $erreurs[] = "Le numéro de téléphone est déjà utilisé"; 
        }

        //Vérifier si le mot de passe est dans les critères
        if(strlen($password) < 8 || !preg_match("/[a-z]/", $password) 
        || !preg_match("/[A-Z]/", $password) || !preg_match("/[0-9]/", $password) 
        || !preg_match("/[@.#$%^&*!]/", $password)) 

                $erreurMdp[] = "Le mot de passe est invalide! Il doit contenir:<br>" . 
                "- Au moins 8 caractères<br>" . 
                "- Au moins 1 lettre majuscule et minuscule<br>" . 
                "- Au moins 1 caractère spécial<br>";
        
        //Vérifier si les 2 mots de passe sont identiques
        if($password != $confirmation_mdp) 
            $erreurs[] = "Les mots de passe ne sont pas identiques!";

        //Verifie si courriel suit le bon format
        if(!filter_var($_POST['courriel'], FILTER_VALIDATE_EMAIL))
            $erreurs[] = "Le courriel saisi n'est pas valide!";
        else {
            //Verifier si courriel existe deja 
            $requete = $conn->prepare("SELECT * FROM Compte WHERE courriel = '$courriel'");
            $requete->execute();

            if($requete->rowCount() != 0)
                $erreurs[] = "Le courriel est déjà utilisé";
        }

        //Si tout est valide, ajouter utilisateur a la base de données
        if(count($erreurs) == 0 && count($erreurMdp) == 0)
        {
            //Encrypter le mot de passe
            $mdp_encrypte = AES256CBC_encrypter($password, CLE_ENCRYPTION);

            //Effectuer la requête pour créer le compte utilisateur
            $requete = $conn->prepare("INSERT INTO Compte (courriel, prenom, nom, motDePasse, telephone) 
            VALUES ('$courriel', '$prenom', '$nom', '$mdp_encrypte', $telephone)");
            $requete->execute();

            //CRÉER COMPTE CHÈQUE-------
            $requete = $conn->prepare("INSERT INTO CompteBancaire (compteId, solde, typeCompte, interet, ouverture, suspendu) VALUES
            ((SELECT id FROM Compte WHERE courriel LIKE '$courriel'), 0, 'Compte chèque', 0, NOW(), 0);");
            $requete->execute();

            //Mettre le message de succès 
            echo "<script>";
            echo "let msgDiv = document.getElementById('msg-succes');";
            echo 'msgDiv.innerHTML = "<p>L\'utilisateur a été créé avec succès! Bienvenue chez Fish&Fric.</p>";';
            echo '</script>';            
        }

        //Sinon, on affiche les erreurs
        else
        {
            echo "<script>";
            echo "let erreurMdpDiv = document.getElementById('erreur-mdp');";
            echo "let erreursDiv = document.getElementById('erreurs-reste');";

            //Afficher les erreurs et l'erreur de mot de passe dans leur div respectif
            for ($i = 0; $i < count($erreurs); $i++) {
                echo 'erreursDiv.innerHTML += "<p>' . $erreurs[$i] . '</p><br>";';
            }

            //Afficher s'il y a une erreur de mot de passe
            if (count($erreurMdp))
                echo 'erreurMdpDiv.innerHTML += "<p>' . $erreurMdp[0] . '</p>";';

            echo '</script>';
        }
    } 
?>