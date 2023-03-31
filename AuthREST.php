<?php

    require_once 'jwt_utils.php';

    /// Identification du type de méthode HTTP envoyée par le client
    $http_method = $_SERVER['REQUEST_METHOD'];

    if($http_method == "POST"){
        $data = (array) json_decode(file_get_contents('php://input'), TRUE);

        if (isValidUser($data['username'], $data['password'])){
            $username = $data['username'];
            $headers = array('alg'=>'HS256', 'typ'=>'JWT');
            $payload = array('username'=>$username, 'exp'=>(time()+60));
            $jwt = generate_jwt($headers, $payload);
            deliver_response(201, "[LOCAL SERVEUR REST] POST REQUEST : Token generate ok", $jwt);
        }else{
            deliver_response(401, "[LOCAL SERVEUR REST] POST REQUEST : utilisateur ou mot de passe incorrect", NULL);
        }
    }else{
        deliver_response(405, "[LOCAL SERVEUR REST] REQUEST : Methode de requete non autorisee. ", NULL);
    }

    
///vérification de la validité des identifiants 
function isValidUser($username,$password){
    include 'config.php';
    $req = $linkpdo -> prepare('SELECT mdp FROM utilisateur WHERE pseudo = ?');
            if($req == false){
            die('Erreur linkpdo');
            }

            $req -> execute(array($username));
            if($req == false){
            die('Erreur execute');
            }

            $data = $req -> fetch();

            if($data !=false){
                if(password_verify($password,$data[0])){
                    return True;
                    exit();
                }else{
                    return False;
                }
            }else{
                return False;
            }
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