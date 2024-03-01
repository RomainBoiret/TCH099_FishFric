<?php
session_start();

//Mettre la variable de session à null
$_SESSION = [];
session_destroy();

//Envoyer l'utilisateur à la page de login
header('Location: ./index.html');

exit;
?>