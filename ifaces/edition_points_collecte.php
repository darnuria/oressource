<?php session_start();

require_once('../moteur/dbconfig.php');

//Vérification des autorisations de l'utilisateur et des variables de session requises pour l'affichage de cette page:
                    if (isset($_SESSION['id']) AND $_SESSION['systeme'] = "oressource" AND (strpos($_SESSION['niveau'], 'k') !== false))
                    {
                    include "tete.php";
                    ?>
<div class="container">
<h1>Gestion des points de collecte</h1> 
  <div class="panel-heading">Gérez ici les différents points de collecte.</div>
  <div class="panel-body">
    <div class="row">
     	<form action="../moteur/edition_points_collecte_post.php" method="post">
      <div class="col-md-2"><label for="nom">Nom:</label><br><br><input type="text" value ="<?php echo $_GET['nom']?>" name="nom" id="nom" class="form-control " required autofocus></div>
      <div class="col-md-3"><label for="adresse">Adresse:</label><br><br><input type="text" value ="<?php echo $_GET['adresse']?>" name="adresse" id="adresse" class="form-control " required ></div>
      <div class="col-md-2"><label for="commentaire">Commentaire:</label><br><br> <input type="text" value ="<?php echo $_GET['commentaire']?>" name="commentaire" id="commentaire" class="form-control " required ></div>
       <div class="col-md-2"><label for="pesee_max">Masse maxi. d'une pesée (Kg):</label> <input type="text" value ="<?php echo $_GET['pesee_max']?>" name="pesee_max" id="pesee_max" class="form-control " required ></div>
      <div class="col-md-1"><label for="couleur">Couleur:</label><br><br><input type="color"        value ="<?php if(isset($_GET['couleur']))echo "#".$_GET['couleur']?>" name="couleur" id="couleur" class="form-control " required ></div>
      <div class="col-md-1"><br><br><button name="creer" class="btn btn-default">Creer!</button></div>
      </form>
    </div>
  </div>
      <!-- Table -->
  <table class="table">
    <thead>
      <tr>
        <th>#</th>
        <th>Date de création</th>
        <th>Nom</th>
        <th>Adresse</th>
        <th>Couleur</th>
        <th>Commentaire</th>
        <th>Pesée maxi.</th>
        <th>Visible</th>
        <th>Modifier</th>
     </tr>
    </thead>
  <tbody>
                       <?php 
                        // On recupère tout le contenu de la table points-collecte
                        $reponse = $bdd->query('SELECT * FROM points_collecte');
                        // On affiche chaque entree une à une
                        while ($donnees = $reponse->fetch())
                        {
                        ?>
    <tr> 
      <td><?php echo $donnees['id']?></td>
      <td><?php echo $donnees['timestamp']?></td>
      <td><?php echo $donnees['nom']?></td>
      <td><?php echo $donnees['adresse']?></td>
      <td><span class="badge" style="background-color:<?php echo$donnees['couleur']?>"><?php echo$donnees['couleur']?></span></td> 
      <td><?php echo $donnees['commentaire']?></td>
      <td><?php echo $donnees['pesee_max']?></td>
      <td>
        <form action="../moteur/collectes_visibles_post.php" method="post">
          <input type="hidden" name ="id" id="id" value="<?php echo $donnees['id']?>">
          <input type="hidden"name ="visible" id ="visible" value="<?php if ($donnees['visible'] == "oui"){echo "non";} else {echo "oui";}?>">
                          <?php
                          if ($donnees['visible'] == "oui") // SI on a pas de message d'erreur
                          {?>
          <button  class="btn btn-info btn-sm" >
                            <?php
                          }

                          else // SINON 
                          {?>
          <button  class="btn btn-danger btn-sm " >
                           <?php
                          }
                           echo $donnees['visible']?> 
          </button>
        </form>
        </td>
        <td>
          <form action="modification_points_collecte.php" method="post">
            <input type="hidden" name ="id" id="id" value="<?php echo $donnees['id']?>">
            <input type="hidden" name ="nom" id="nom" value="<?php echo $donnees['nom']?>">
            <input type="hidden" name ="adresse" id="adresse" value="<?php echo $donnees['adresse']?>">
            <input type="hidden" name ="commentaire" id="commentaire" value="<?php echo $donnees['commentaire']?>">
            <input type="hidden" name ="pesee_max" id="pesee_max" value="<?php echo $donnees['pesee_max']?>">
            <input type="hidden" name ="couleur" id="couleur" value="<?php echo substr($_POST['couleur'],1)?>">
            <button  class="btn btn-warning btn-sm " >modifier</button>
          </form>
        </td>
    </tr>
                          <?php }
                          $reponse->closeCursor(); // Termine le traitement de la requête
                          ?>
  </tbody>
    <tfoot>
     <tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
      </tr>
    </tfoot>
  </table>
<br>
<div class="row">
  <div class="col-md-4"></div>
  <div class="col-md-4"><br> </div>
  <div class="col-md-4"></div>
</div>
</div>
</div>
</div><!-- /.container -->
   
<?php include "pied.php";

}
else
{header('Location: ../moteur/destroy.php');}
?>
