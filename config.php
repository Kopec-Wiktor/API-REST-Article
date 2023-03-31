<?php
    ///Connexion au serveur MySQL
    $server = 'localhost';
    $db = 'apiarticle';
    $login = 'root';
    try {
        $linkpdo = new PDO("mysql:host=$server;dbname=$db", $login);
    }
    catch (Exception $e) {
        die('Erreur : ' . $e -> getMessage());
    }
?>    