<?php
//martin vert
// Connexion à la base de données
		try
{
		include('dbconfig.php');
}
		catch(Exception $e)
{
        die('Erreur : '.$e->getMessage());
}
 
// Insertion du post à l'aide d'une requête préparée
// mot de passe crypté md5 

// Insertion du post à l'aide d'une requête préparée
	$req = $bdd->prepare('INSERT INTO description_structure(id, nom, adresse, description, siret, telephone, mail, id_localite, texte_adhesion) VALUES (:id, :nom, :adresse, :description, :siret, :telephone, :mail, :id_localite, :texte_adhesion) ON DUPLICATE KEY UPDATE  nom=:nom, mail=:mail, id_localite=:id_localite, adresse=:adresse, description=:description, siret=:siret, telephone=:telephone, texte_adhesion=:texte_adhesion;');
	$req->execute(array('id' => 1,'nom' => $_POST['nom'], 'description' => $_POST['description'], 'siret' => $_POST['siret'], 'mail' => $_POST['mail'], 'adresse' => $_POST['adresse'],'telephone' => $_POST['telephone'],'id_localite' => $_POST['localite'],'texte_adhesion' => $_POST['texte_adhesion']));
    $req->closeCursor();
// Redirection du visiteur vers la page de gestion des points de collecte
header('Location:../ifaces/edition_description.php');
?>
