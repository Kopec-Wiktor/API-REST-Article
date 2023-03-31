<?php
    require_once 'config.php';
    require_once 'jwt_utils.php';
    
    /// Paramétrage de l'entête HTTP (pour la réponse au Client)
    header("Content-Type:application/json");
    /// Identification du type de méthode HTTP envoyée par le client
    $http_method = $_SERVER['REQUEST_METHOD'];
    switch ($http_method){
    /// Cas de la méthode GET
    case "GET" :

        function decodeTokenUsername($bearer_token){ // decode le jeton est retourne le nom utilisateur
            $bearer_token = base64_decode($bearer_token);
            $Token = explode(":", $bearer_token);
            $Pseudo = explode('"', $Token[3]);
            $username = $Pseudo[1];
            //echo $username;exit;
            //$username = $bearer_token['username'];//trouve un moyen de recup le username du decodage 
            return $username;
        }

        function recupRole($username){//return le role de l'utilisateur
            include 'config.php';
            $req = $linkpdo->prepare('SELECT role1 FROM utilisateur WHERE pseudo = ?');
            if ($req == false) {
                die('Erreur linkpdo');
            }

            $req->execute(array($username));
            if ($req == false) {
                die('Erreur execute');
            }

            $data_Role = $req->fetch();
            $role = $data_Role[0];
            return $role;
        }

        function GetArticleNonAuthentif($id){
            include 'config.php';
            if($id == NULL){
                $query = "SELECT pseudo as auteur, date_publication, contenu FROM article";
                $select = $linkpdo->prepare($query);
                $select->execute(array());
                $data = $select -> fetchAll();
                if($data != false){
                    deliver_response(201, "[LOCAL SERVEUR REST] GET REQUEST : Read Data OK", $data);
                }else{
                    deliver_response(404, "[LOCAL SERVEUR REST] GET REQUEST : Read Data KO, ressource non trouvée", $data);
                }
                
            }else{
                $req = $linkpdo -> prepare('SELECT pseudo as auteur, date_publication, contenu FROM article WHERE id_article = ?');
                if($req == false){
                die('Erreur linkpdo');
                }
    
                $req -> execute(array($id));
                if($req == false){
                die('Erreur execute');
                }

                $data = $req -> fetchAll();
                if($data != false){
                    deliver_response(201, "[LOCAL SERVEUR REST] GET REQUEST : Read Data OK", $data);
                }else{
                    deliver_response(404, "[LOCAL SERVEUR REST] GET REQUEST : Read Data KO, ressource non trouvée", $data);
                }
            }
        }

        function GetArticleModerateur($id){
            include 'config.php';
            if($id == NULL){
                $query = "SELECT a.pseudo as auteur, a.date_publication, a.contenu, 
                GROUP_CONCAT(CASE WHEN l.liker THEN u.pseudo END) AS liste_likes, 
                COUNT(CASE WHEN l.liker THEN 1 END) AS nombre_likes,
                GROUP_CONCAT(CASE WHEN l.disliker THEN u.pseudo END) AS liste_dislikes, 
                COUNT(CASE WHEN l.disliker THEN 1 END) AS nombre_dislikes
                FROM article a
                LEFT JOIN Liker l ON a.id_article = l.id_article
                LEFT JOIN utilisateur u ON u.pseudo = l.pseudo
                GROUP BY a.pseudo, a.date_publication, a.contenu
                ";
                $select = $linkpdo->prepare($query);
                $select->execute(array());
                $data = $select -> fetchAll();
                if($data != false){
                    deliver_response(201, "[LOCAL SERVEUR REST] GET REQUEST : Read Data OK", $data);
                }else{
                    deliver_response(404, "[LOCAL SERVEUR REST] GET REQUEST : Read Data KO, ressource non trouvée", NULL);
                }
                
            }else{
                $req = $linkpdo -> prepare('SELECT a.pseudo as auteur, a.date_publication, a.contenu, 
                GROUP_CONCAT(CASE WHEN l.liker THEN u.pseudo END) AS liste_likes, 
                COUNT(CASE WHEN l.liker THEN 1 END) AS nombre_likes,
                GROUP_CONCAT(CASE WHEN l.disliker THEN u.pseudo END) AS liste_dislikes, 
                COUNT(CASE WHEN l.disliker THEN 1 END) AS nombre_dislikes
                FROM article a
                LEFT JOIN Liker l ON a.id_article = l.id_article
                LEFT JOIN utilisateur u ON u.pseudo = l.pseudo
                WHERE a.id_article = ?
                GROUP BY a.pseudo, a.date_publication, a.contenu
                ');
                if($req == false){
                die('Erreur linkpdo');
                }
    
                $req -> execute(array($id));
                if($req == false){
                die('Erreur execute');
                }

                $data = $req -> fetchAll();
                if($data != false){
                    deliver_response(201, "[LOCAL SERVEUR REST] GET REQUEST : Read Data OK", $data);
                }else{
                    deliver_response(404, "[LOCAL SERVEUR REST] GET REQUEST : Read Data KO, ressource non trouvée", NULL);
                }
            }
        }

        function GetArticlePublisher($id, $username){
            include 'config.php';
            if($id == NULL){
                $query = "SELECT a.pseudo as auteur, a.date_publication, a.contenu, 
                COUNT(CASE WHEN l.liker THEN 1 END) AS nombre_likes,
                COUNT(CASE WHEN l.disliker THEN 1 END) AS nombre_dislikes
                FROM article a
                LEFT JOIN Liker l ON a.id_article = l.id_article
                LEFT JOIN utilisateur u ON u.pseudo = l.pseudo
                GROUP BY a.pseudo, a.date_publication, a.contenu
                ";
                $select = $linkpdo->prepare($query);
                $select->execute(array());
                $data = $select -> fetchAll();
                if($data != false){
                    deliver_response(201, "[LOCAL SERVEUR REST] GET REQUEST : Read Data OK", $data);
                }else{
                    deliver_response(404, "[LOCAL SERVEUR REST] GET REQUEST : Read Data KO, ressource non trouvée", NULL);
                }
                
            }else{
                if($id == "MyArticle"){
                    $req = $linkpdo -> prepare('SELECT a.pseudo as auteur, a.date_publication, a.contenu, 
                    COUNT(CASE WHEN l.liker THEN 1 END) AS nombre_likes,
                    COUNT(CASE WHEN l.disliker THEN 1 END) AS nombre_dislikes
                    FROM article a
                    LEFT JOIN Liker l ON a.id_article = l.id_article
                    LEFT JOIN utilisateur u ON u.pseudo = l.pseudo
                    WHERE a.pseudo = ?
                    GROUP BY a.pseudo, a.date_publication, a.contenu');
                    if($req == false){
                    die('Erreur linkpdo');
                    }
        
                    $req -> execute(array($username));
                    if($req == false){
                    die('Erreur execute');
                    }

                    $data = $req -> fetchAll();
                    if($data != false){
                        deliver_response(201, "[LOCAL SERVEUR REST] GET REQUEST : Read Data OK", $data);
                    }else{
                        deliver_response(404, "[LOCAL SERVEUR REST] GET REQUEST : Read Data KO, ressource non trouvée", $data);
                    }
                }else{
                    $query = "SELECT a.pseudo as auteur, a.date_publication, a.contenu, 
                    COUNT(CASE WHEN l.liker THEN 1 END) AS nombre_likes,
                    COUNT(CASE WHEN l.disliker THEN 1 END) AS nombre_dislikes
                    FROM article a
                    LEFT JOIN Liker l ON a.id_article = l.id_article
                    LEFT JOIN utilisateur u ON u.pseudo = l.pseudo
                    WHERE a.id_article = ?
                    GROUP BY a.pseudo, a.date_publication, a.contenu
                    ";
                    $select = $linkpdo->prepare($query);
                    $select->execute(array($id));
                    $data = $select -> fetchAll();
                    if($data != false){
                        deliver_response(201, "[LOCAL SERVEUR REST] GET REQUEST : Read Data OK", $data);
                    }else{
                        deliver_response(404, "[LOCAL SERVEUR REST] GET REQUEST : Read Data KO, ressource non trouvée", NULL);
                    }
                }
            }
        }

        if (!empty($_GET['id'])){
            $id = htmlspecialchars($_GET['id']);
        }else{
            $id = NULL;
        }

        $bearer_token = '';
        ///Recherche du token dans la requête
        $bearer_token = get_bearer_token();

        ///Si l'utilisateur n'est pas authentifier
        if($bearer_token == NULL){
            //affiche les article avec les informations pour un utilisateur non authentifié
            $DataArticle = GetArticleNonAuthentif($id);

        }else{
            ///Si le token est valide, traitement de la requête 
            if(is_jwt_valid($bearer_token)){
                $username = decodeTokenUsername($bearer_token);
                $role = recupRole($username);
                switch($role){
                    case "publisher" :
                        GetArticlePublisher($id, $username);
                    break;
                    case "moderator" :
                        GetArticleModerateur($id);
                    break;
                }
            }else{ //sinon message jeton invalid
                deliver_response(401, "[LOCAL SERVEUR REST] POST REQUEST : Le jeton fournit est invalid", NULL);
            }
        }

    break;
    /// Cas de la méthode POST
    case "POST" :
        function InsertArticle($username){
            include 'config.php';
            $postedData = json_decode(file_get_contents('php://input'));
            $query = "INSERT INTO article(date_publication,contenu,pseudo)VALUES(?,?,?)";
            $maData = (array)$postedData;
            $date_publication = $maData['date_publication'];
            $contenu = $maData['contenu'];
            $pseudo = $username;
            $insert = $linkpdo->prepare($query);
            $insert->execute(array($date_publication,$contenu,$pseudo));
        }

        //duplicate
        function decodeTokenUsername($bearer_token){ // decode le jeton est retourne le nom utilisateur
            $bearer_token = base64_decode($bearer_token);
            $Token = explode(":", $bearer_token);
            $Pseudo = explode('"', $Token[3]);
            $username = $Pseudo[1];
            //echo $username;exit;
            //$username = $bearer_token['username'];//trouve un moyen de recup le username du decodage 
            return $username;
        }
        function recupRole($username){//return le role de l'utilisateur
            include 'config.php';
            $req = $linkpdo->prepare('SELECT role1 FROM utilisateur WHERE pseudo = ?');
            if ($req == false) {
                die('Erreur linkpdo');
            }

            $req->execute(array($username));
            if ($req == false) {
                die('Erreur execute');
            }

            $data_Role = $req->fetch();
            $role = $data_Role[0];
            return $role;
        }
        
        function InsertLike($username, $idArticle){
            include 'config.php';
            $query = "INSERT INTO Liker(pseudo, id_article, liker, disliker)VALUES(?,?,?,NULL)";
            $like = True;
            $pseudo = $username;
            $insert = $linkpdo->prepare($query);
            $insert->execute(array($pseudo, $idArticle, $like));
            deliver_response(200, "[LOCAL SERVEUR REST] POST REQUEST : POST Data Like OK", NULL);
        }

        function InsertDislike($username, $idArticle){
            include 'config.php';
            $query = "INSERT INTO Liker(pseudo, id_article, liker, disliker)VALUES(?,?,NULL,?)";
            $like = True;
            $pseudo = $username;
            $insert = $linkpdo->prepare($query);
            $insert->execute(array($pseudo,$idArticle, $like));
            deliver_response(200, "[LOCAL SERVEUR REST] POST REQUEST : POST Data Dislike OK", NULL);
        }


        $bearer_token = '';
        ///Recherche du token dans la requête
        $bearer_token = get_bearer_token();

        if($bearer_token == NULL){//si utiliateur non authentifier
            deliver_response(403, "[LOCAL SERVEUR REST] POST REQUEST : Accès refusé, un jeton est requis pour cette action", NULL);
        }else{
            ///Si le token est valide, traitement de la requête 
            if(is_jwt_valid($bearer_token)){
                $username = decodeTokenUsername($bearer_token);
                $role = recupRole($username);
                if($role == "publisher"){
                    if (!empty($_GET['id'])){
                        $id = htmlspecialchars($_GET['id']);
                        $Evaluation = explode("/", $id);
                        $Liker = $Evaluation[0];
                        $idArticle = $Evaluation[1];
                        switch($Liker){
                            case "Like" :
                                //verifier si l'article a été créer par lui meme si oui delivery ok sinon mess erreur
                                InsertLike($username, $idArticle);
                            break;
                            case "Dislike" :
                                //supprimer sans verifier puis retourner message ok
                                InsertDislike($username, $idArticle);
                            break;
                        }
                    }else{
                        InsertArticle($username);
                        deliver_response(200, "[LOCAL SERVEUR REST] POST REQUEST : POST Data OK", NULL);
                    }
                }else{
                    deliver_response(401, "[LOCAL SERVEUR REST] POST REQUEST : Seul le publisher peut créer un article", NULL);
                }
            }else{
                deliver_response(401, "[LOCAL SERVEUR REST] POST REQUEST : Le jeton fournit est invalid", NULL);
            }
        }
        
    break;
    /// Cas de la méthode PUT
    case "PUT" :

        function UpdateArticle($username,$id){
            include 'config.php';
            $postedData = json_decode(file_get_contents('php://input'));
            $query = "UPDATE article SET date_publication=?,contenu=?,pseudo=? WHERE id_article=?";
            $maData = (array)$postedData;
            $date_publication = $maData['date_publication'];
            $contenu = $maData['contenu'];
            $pseudo = $username;
            $insert = $linkpdo->prepare($query);
            $insert->execute(array($date_publication,$contenu,$pseudo,$id));
        }
        //duplicate
        function decodeTokenUsername($bearer_token){ // decode le jeton est retourne le nom utilisateur
            $bearer_token = base64_decode($bearer_token);
            $Token = explode(":", $bearer_token);
            $Pseudo = explode('"', $Token[3]);
            $username = $Pseudo[1];
            //echo $username;exit;
            //$username = $bearer_token['username'];//trouve un moyen de recup le username du decodage 
            return $username;
        }
        function recupRole($username){//return le role de l'utilisateur
            include 'config.php';
            $req = $linkpdo->prepare('SELECT role1 FROM utilisateur WHERE pseudo = ?');
            if ($req == false) {
                die('Erreur linkpdo');
            }

            $req->execute(array($username));
            if ($req == false) {
                die('Erreur execute');
            }

            $data_Role = $req->fetch();
            $role = $data_Role[0];
            return $role;
        }
        function verifAuteur($id, $username){//verifie si l'utilisateur est l'auteur de l'article avec l'$id return oui ou non
            include 'config.php';
            $req2 = $linkpdo->prepare('SELECT pseudo FROM article WHERE id_article = ?');
            if ($req2 == false) {
                die('Erreur linkpdo');
            }

            $req2->execute(array($id));
            if ($req2 == false) {
                die('Erreur execute');
            }

            $data_pseudo = $req2->fetch();
            $pseudo = $data_pseudo[0];

            if($pseudo == $username){
                return True;
            }else{
                return False;
            }
        }

        if(!empty($_GET['id'])){
            $id = htmlspecialchars($_GET['id']);
            $bearer_token = '';
            ///Recherche du token dans la requête
            $bearer_token = get_bearer_token();
            if($bearer_token == NULL){
                deliver_response(403, "[LOCAL SERVEUR REST] POST REQUEST : Accès refusé, un jeton est requis pour cette action", NULL);
            }else{
                ///Si le token est valide, traitement de la requête 
                if(is_jwt_valid($bearer_token)){
                    $username = decodeTokenUsername($bearer_token);
                    $role = recupRole($username);
                    if($role == "publisher" && verifAuteur($id, $username)){
                        UpdateArticle($username, $id);
                        deliver_response(200, "[LOCAL SERVEUR REST] PUT REQUEST : PUT Data OK", NULL);
                    }else{
                        deliver_response(401, "[LOCAL SERVEUR REST] PUT REQUEST : Pour modifier il faut être publisher est l'auteur de l'article", NULL);
                    }
                }else{
                    deliver_response(401, "[LOCAL SERVEUR REST] PUT REQUEST : Le jeton fournit est invalid", NULL);
                }
            }       
        }

    break;
    
    case "DELETE":

        function decodeTokenUsername($bearer_token){ // decode le jeton est retourne le nom utilisateur
            $bearer_token = base64_decode($bearer_token);
            $Token = explode(":", $bearer_token);
            $Pseudo = explode('"', $Token[3]);
            $username = $Pseudo[1];
            //echo $username;exit();
            //$username = $bearer_token['username'];//trouve un moyen de recup le username du decodage 
            return $username;
        }

        function recupRole($username){//return le role de l'utilisateur
            include 'config.php';
            $req = $linkpdo->prepare('SELECT role1 FROM utilisateur WHERE pseudo = ?');
            if ($req == false) {
                die('Erreur linkpdo');
            }

            $req->execute(array($username));
            if ($req == false) {
                die('Erreur execute');
            }

            $data_Role = $req->fetch();
            $role = $data_Role[0];
            return $role;
        }

        function supprimerArticle($id){//supprime l'article éyant l'identifiant $id
            include 'config.php';
            $query = "DELETE FROM article WHERE id_article=$id";
            $delete = $linkpdo->prepare($query);
            $delete ->execute(array());
        }

        function verifAuteur($id, $username){//verifie si l'utilisateur est l'auteur de l'article avec l'$id return oui ou non
            include 'config.php';
            $req2 = $linkpdo->prepare('SELECT pseudo FROM article WHERE id_article = ?');
            if ($req2 == false) {
                die('Erreur linkpdo');
            }

            $req2->execute(array($id));
            if ($req2 == false) {
                die('Erreur execute');
            }

            $data_pseudo = $req2->fetch();
            $pseudo = $data_pseudo[0];

            if($pseudo == $username){
                return True;
            }else{
                return False;
            }
        }

        if(!empty($_GET['id'])){
            $id = htmlspecialchars($_GET['id']);
            $bearer_token = '';
            ///Recherche du token dans la requête
            $bearer_token = get_bearer_token();

            ///Si l'utilisateur n'est pas authentifier
            if($bearer_token == NULL){
                deliver_response(403, "[LOCAL SERVEUR REST] POST REQUEST : Accès refusé, un jeton est requis pour cette action", NULL);
            }else{
                ///Si le token est valide, traitement de la requête
                if(is_jwt_valid($bearer_token)){
                    $username = decodeTokenUsername($bearer_token);
                    $role = recupRole($username);
                    switch($role){
                        case "publisher" :
                            //verifier si l'article a été créer par lui meme si oui delivery ok sinon mess erreur
                            if(verifAuteur($id, $username)){
                                supprimerArticle($id);
                                deliver_response(200, "[LOCAL SERVEUR REST] DELETE REQUEST : DELETE Data OK", NULL);
                            }else{
                                deliver_response(401, "[LOCAL SERVEUR REST] DELETE REQUEST : Vous n'etes pas moderateur vous pouvez supprimer uniquement vos article", NULL);
                            }
                        break;
                        case "moderator" :
                            //supprimer sans verifier puis retourner message ok
                            supprimerArticle($id);
                            deliver_response(200, "[LOCAL SERVEUR REST] DELETE REQUEST : DELETE Data OK", NULL);
                        break;
                    }    
                }else{ //sinon message jeton invalid
                    deliver_response(401, "[LOCAL SERVEUR REST] DELETE REQUEST : Le jeton fournit est invalid", NULL);
                }
            }
        
        }else{
            deliver_response(400, "[LOCAL SERVEUR REST] DELETE REQUEST : Un id est nécessaire pour supprimer un article", NULL);
        }

    default:
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