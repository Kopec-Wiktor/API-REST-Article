<html lang="fr">
<meta charset = "utf-8">
  <head>
    <title>Inscritpion</title>
    <link rel="stylesheet" href="form.css" />
    <link rel="icon" type="icon/image" href="uploads/t21.jpg" />
  </head>
  <body>
 

    <div class="container">
      <form action="inscription.php" method="post" class="bloc">
        <h1>Inscription</h1>
        
        <input type="pseudo" name="pseudo" id="pseudo" placeholder="pseudo" required />

        <input type="password" name="mdp" id="mdp" placeholder="Mot de passe" required />
        <input type="submit" value = "S'inscrire" name="envoie" id="button"/>
        
      </form>
    </div>
  </body>
  <?php
	///Connexion au serveur MySQL
	include("config.php");
			
	if (!empty($_POST['mdp'])&& !empty($_POST['pseudo'])){
          
        //Déclaration des variables POST
        
        $mdp = $_POST['mdp'];
        $mdp1 = password_hash($mdp,CRYPT_BLOWFISH);
        $pseudo = $_POST['pseudo'];
        $publisher = "moderator";
        echo "$mdp";
        echo "$pseudo";

        //$pp = password_verify($m,$mdp);
        //echo "$pp";        

        ///Préparation de la requête
        $req2 = $linkpdo -> prepare ('INSERT INTO utilisateur(pseudo, mdp, role1) VALUES(:pseudo, :mdp, :role1)');
        if($req2 == false){
          die('Erreur prepare');
        }
        ///Exécution de la requête
        $req2->execute ( array('pseudo' => $pseudo,
        'mdp' => $mdp1,
        'role1' => $publisher));
        if($req2 == false){
          die('Erreur execute');
        }

        echo"inscription ok";
        

    }

    $ch1 = '{
      "alg": "HS256",
      "typ": "JWT"
  }{
      "username": "amk",
      "exp": 1679147696';
			$pieces = explode(":", $ch1);
      //echo $pieces[0]; // piece1
      //echo"<br>";
      //echo $pieces[1]; // piece2
      //echo"<br>";
      //echo $pieces[2]; // piece1
      //echo"<br>";
      echo $pieces[3]; // piece2
      $pseudo = explode('"', $pieces[3]);
      echo"<br>";
      
      //echo $pseudo[0]; // piece2
      echo"<br>";
      echo $pseudo[1]; // piece2
      echo"<br>";
      //echo $pseudo[2]; 
      
      $pseudoV = $pseudo[0];
      echo $pseudoV[0];
      echo $pseudoV;
      echo $pseudoV;
      echo $pseudoV;
      echo $pseudoV;

      echo $pseudoV;

		?>
</html>