<?php

require_once '../moteur/dbconfig.php';
/*
 * Here comes the text of your license
 * Each line should be prefixed with  *
 */
function unmatchedData($bdd, $a, $aId, $b, $bId) {
  $sql = "select a.*
  from $a as a
  left outer join $b as b
  on a.$aId = b.$bId
  where b.$bId is null;";
  $stmt = $bdd->prepare($sql);
  $stmt->execute();
  return $stmt;
}

function main($bdd) { {
    $tables = [
      "unmatchCollects" => unmatchedData($bdd, "pesees_collectes", "id_collecte", "collectes", "id"),
      "unmatchpesees" => unmatchedData($bdd, "collectes", "id", "pesees_collectes", "id_collecte"),
      //unmatchedData($bdd, "sorties", "id_sortie", "pesees_sorties", "id"),
      //unmatchedData($bdd, "sorties", "id", "pesees_sorties", "id_sortie"),
    ];
    var_dump($tables["unmatchCollects"]);
    var_dump($tables["unmatchCollects"]->fetchAll());
    
    exit;
    // Supprimes collectes sans pesees collectes.
    $sql = 'delete FROM collectes where id = :id';
    $stmt = $bdd->prepare($sql);
    foreach ($tables['unmatchpesees'] as $c) {
      $stmt->bindValue("id", $c['unmatchpesees']['a.id']);
      //$stmt->execute();
    }
    $stmt->closeCursor();
  }
}

main($bdd);