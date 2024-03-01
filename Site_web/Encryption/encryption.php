<?php

    // Clé secrète pour l'encryption
    define("CLE_ENCRYPTION", "cle");

    function AES256CBC_encrypter($valeur, $cle) {
        // Vecteur d'initialisation
        $vect = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        
        // Encrypter la valeur
        $valeurEncryptee = openssl_encrypt($valeur, 'aes-256-cbc', $cle, 0, $vect);
        
        // Retourner la valeur encryptée encodée en base64 (c.-à-d. en format texte)
        return base64_encode($valeurEncryptee . '::' . $vect);
    }

    function AES256CBC_decrypter($valeur, $cle) {
        
        // Décoder la valeur
        list($valeurEncryptee, $vect) = array_pad(explode('::', base64_decode($valeur), 2), 2, null);
        
        // Décrypter la valeur
        return openssl_decrypt($valeurEncryptee, 'aes-256-cbc', $cle, 0, $vect);

    }
?>