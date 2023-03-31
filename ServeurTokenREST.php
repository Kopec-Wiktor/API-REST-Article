<?php
    require_once 'config.php';
    require_once 'jwt_utils.php';
    
    /// Paramétrage de l'entête HTTP (pour la réponse au Client)
    header("Content-Type:application/json");
    /// Identification du type de méthode HTTP envoyée par le client
    $http_method = $_SERVER['REQUEST_METHOD'];
    switch ($http_method){
    /// Cas de la méthode POST
    case "POST" :

        function VerifyIdentifiant($username, $password){//verifi si l'identifiant et le mdp est correct retourne 1 si oui ou 0 sinon
            $req = $linkpdo -> prepare('SELECT mdp FROM utilisateur WHERE nom = ?');
            if($req == false){
            die('Erreur linkpdo');
            }

            $req -> execute(array($username));
            if($req == false){
            die('Erreur execute');
            }

            $data = $req -> fetch();

            if($data !=false){
                if(password_verify($_POST['mdp'],$data[0])){
                    return True;
                    //créer jeton
                    header('Location: PageAccueil.php');
                    exit();
                }else{
                    return False;
                    //message erreur
                }
                }else{
                    return False;
                    //message erreur
            }

        }

    $username = $_POST['username'];
    $password = $_POST['password'];

    $postedData = 
    (file_get_contents('php://input'));


    $query = "INSERT INTO chuckn_facts(phrase,vote,date_ajout,date_modif,faute,signalement)VALUES(?,?,?,?,?,?)";

    $maData = (array)$postedData;
    $phrase = $maData['phrase'];
    $vote = $maData['vote'];
    $date_ajout = $maData['date_ajout'];
    $date_modif = $maData['date_modif'];
    $faute = $maData['faute'];
    $signalement = $maData['signalement'];

    $insert = $database->prepare($query);
    $insert->execute(array($phrase,$vote,$date_ajout,$date_modif,$faute,$signalement));

    deliver_response(201, "[LOCAL SERVEUR REST] POST REQUEST : Insert Data OK", NULL);
    break;
    }
    /// Envoi de la réponse au Client
    function deliver_response($status, $status_message, $data){
    /// Paramétrage de l'entête HTTP, suite
    header("HTTP/1.1 $status $status_message");
    /// Paramétrage de la réponse retournée
    $response['status'] = $status;
    $response['status_message'] = $status_message;
    $response['data'] = $data;
    /// Mapping de la réponse au format JSON
    $json_response = json_encode($response);
    echo $json_response;
}
?>