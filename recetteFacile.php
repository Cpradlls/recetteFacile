<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recette facile</title>
  <meta name="description" content="Site internet qui répétorie des recettes faciles à réaliser">
</head>
<body><pre><?php

  // séparer ses identifiants et les protéger, une bonne habitude à prendre
  include "recetteFacile-conf.php";

  try {

    // instancie un objet $connexion à partir de la classe PDO
    $connexion = new PDO(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_LOGIN, DB_PASS, DB_OPTIONS);

    // Requête de sélection 01
    $requete = "SELECT * FROM `recettes`";
    $prepare = $connexion->prepare($requete);
    $prepare->execute();
    $resultat = $prepare->fetchAll();
    print_r([$requete, $resultat]); // debug & vérification

    // Requête de sélection 02
    $requete = "SELECT *
                FROM `recettes`
                WHERE `recette_id` = :recette_id"; 
    $prepare = $connexion->prepare($requete);
    $prepare->execute(array(":recette_id" => 19)); 
    $resultat = $prepare->fetchAll();
    print_r([$requete, $resultat]); // debug & vérification
    

    // Requête d'insertion
    $requete = "INSERT INTO `recettes` (`recette_titre`, `recette_contenu`, `recette_datetime`) 
                VALUES (:recette_titre, :recette_contenu, :recette_datetime);";
    $prepare = $connexion->prepare($requete);
    $prepare->execute(array(
      ":recette_titre" => "ma pâte à pizza maison",
      ":recette_contenu" => "Ingrédient :\n- 500g de farine\n- 60cl de lait\n- 1 cube de levure fraiche...",
      ":recette_datetime" => "2021-01-12"
    ));
    $resultat = $prepare->rowCount(); // rowCount() nécessite PDO::MYSQL_ATTR_FOUND_ROWS => true
    $lastInsertedRecetteId = $connexion->lastInsertId(); // on récupère l'id automatiquement créé par SQL
    print_r([$requete, $resultat, $lastInsertedRecetteId]); // debug & vérification
    

    // Requête de modification
    $icone = "😺";
    $requete = "UPDATE `recettes`
                SET `recette_titre` = :recette_titre
                WHERE `recette_id` = :recette_id;";
    $prepare = $connexion->prepare($requete);
    $prepare->execute(array(
      ":recette_id"   => $lastInsertedRecetteId,
      ":recette_titre" => $icone . "ma pâte à pizza maison"
    ));
    $resultat = $prepare->rowCount();
    print_r([$requete, $resultat]); // debug & vérification
    

    // Requête de suppression
    $requete = "DELETE FROM `recettes`
                WHERE ((`recette_id` = :recette_id));";
    $prepare = $connexion->prepare($requete);
    $prepare->execute(array($lastInsertedRecetteId)); // on lui passe l'id tout juste créé
    $resultat = $prepare->rowCount();
    print_r([$requete, $resultat, $lastInsertedRecetteId]); // debug & vérification


    //Requête insertion "Levain" dans la table hashtags
    $requete = "INSERT INTO `hashtags` (`hashtag_nom`) 
                VALUES (:hashtag_nom);";
    $prepare = $connexion->prepare($requete);
    $prepare->execute(array(
      ":hashtag_nom" => "Levain"
    ));
    $resultat = $prepare->rowCount(); // rowCount() nécessite PDO::MYSQL_ATTR_FOUND_ROWS => true
    $lastInsertedHashtagId = $connexion->lastInsertId(); // on récupère l'id automatiquement créé par SQL
    print_r([$requete, $resultat, $lastInsertedHashtagId]); // debug & vérification
    

    //Requête qui lie le hashtag "levain" à la recette du "pain au levain"
    $requete = "INSERT INTO `assoc_hashtags_recettes` (`assoc_hr_hashtag_id`, `assoc_hr_recette_id`) 
                VALUES (:assoc_hr_hashtag_id, :assoc_hr_recette_id);";
    $prepare = $connexion->prepare($requete);
    $prepare->execute(array(
      ":assoc_hr_hashtag_id" => 4,
      ":assoc_hr_recette_id" => 1
    ));
    $resultat = $prepare->rowCount(); // rowCount() nécessite PDO::MYSQL_ATTR_FOUND_ROWS => true
    $lastInsertedHashtagId = $connexion->lastInsertId(); // on récupère l'id automatiquement créé par SQL
    print_r([$requete, $resultat, $lastInsertedHashtagId]); // debug & vérification
    

    // //Pour aller plus loin : requête de sélection pour requêter des données dont le hashtag est "nourriture" et afficher le titre de chaque recette concernée.
    $requete = "SELECT 
    hashtag_nom,
    recette_titre
    FROM assoc_hashtags_recettes
    JOIN hashtags ON hashtag_id = assoc_hr_hashtag_id
    JOIN recettes ON recette_id = assoc_hr_recette_id
    WHERE hashtag_id = 1;";
    $prepare = $connexion->prepare($requete);
    $prepare->execute();
    $resultat = $prepare->fetchAll();
    print_r([$requete, $resultat]);
    

  } catch (PDOException $e) {

    // en cas d'erreur, on récup et on affiche, grâce à notre try/catch
    exit("❌🙀💀 OOPS :\n" . $e->getMessage());

  }

?></pre></body>
