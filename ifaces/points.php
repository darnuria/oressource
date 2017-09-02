<?php
/*
  Oressource
  Copyright (C) 2014-2017  Martin Vert and Oressource devellopers

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU Affero General Public License as
  published by the Free Software Foundation, either version 3 of the
  License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU Affero General Public License for more details.

  You should have received a copy of the GNU Affero General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

session_start();

require_once '../core/session.php';
require_once '../core/requetes.php';
require_once '../core/composants.php';

function metadata_point(int $id = 0, string $color = '', string $name = '', bool $visible = true, string $commentaire = '') {
  return [
    'id' => $id,
    'visible' => [['name' => 'visible', 'text' => ['on' => 'oui', 'off' => 'non']], $visible],
    'color' => [['text' => 'Couleur :', 'name' => 'couleur'], $color],
    'name' => [['text' => 'Nom:', 'name' => 'nom'], $name],
    'commentaire' => [['text' => 'Commentaire :', 'name' => 'commentaire'], $commentaire]
  ];
}

function descript_point(string $text, string $href, array $metadata = [], Callable $f): array {
  return array_merge($metadata, [
    'href' => $href,
    'text' => $text,
    'childrens' => $f,
  ]);
}

function pointsConfig5(array $props) {
  ?>
  <div class='panel'>
    <div class="panel-heading">
      <h3 class='panel-title'><?= $props['text'] ?></h1>
    </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-md-2">
          <?= textInput($props['name'][0], $props['name'][1]) ?>
        </div>
        <?= $props['childrens']($props) ?>
        <div class="col-md-2">
          <?= textInput($props['commentaire'][0], $props['commentaire'][1]) ?>
        </div>
        <div class="col-md-1">
          <?= checkboxOnOff($props['visible'][0], $props['visible'][1]) ?>
        </div>
        <div class="col-md-1">
          <?= colorInput($props['color'][0], $props['color'][1]) ?>
        </div>
        <div class="col-md-1">
          <br>
          <?= buttonSubmitConfig($props) ?>
        </div>
      </div>
    </div>
  </div>
  <?php
}

if (is_valid_session() && is_allowed_config()) {
  require_once 'tete.php';
  require_once '../moteur/dbconfig.php';
  $points = points_collectes($bdd);

  $id = $_GET['id'] ?? 0;
  $couleur = $_GET['couleur'] ?? '';
  $nom = $_GET['nom'] ?? '';
  $commentaire = $_GET['commentaire'] ?? '';


  $adresse = $_GET['adresse'] ?? '';
  $masse = $_GET['pesee_max'] ?? '';

  $meta = metadata_point($id, $couleur, $nom, true, $commentaire);
  $data = descript_point("Creation d'un point de collecte", '../moteur/modification_types_collecte_post.php', $meta, function (array $props) {
    ?>
    <div class="col-md-2">
      <?= textInput($props['adress'][0], $props['adress'][1]) ?>
    </div>
    <div class="col-md-2">
      <?= textInput($props['mass'][0], $props['mass'][1]) ?>
    </div>
    <?php
  });

  $data = array_merge($data, [
    'adress' => [['text' => 'Adresse:', 'name' => 'adresse'], $adresse],
    'mass' => [['text' => "Masse maxi. pesée kg :", 'name' => 'pesee_max'], $masse]
  ]);

  function navData($text, string $type): array {
    $nav = [
      'text' => $text,
      'links' => [
        ['href' => 'points.php?tab=collectes', 'text' => 'Collectes'],
        ['href' => 'points.php?tab=ventes', 'text' => 'Ventes'],
        ['href' => 'points.php?tab=collectes', 'text' => 'Sorties']
      ]
    ];
    if ($type === 'collecte') {
      $nav['links'][0]['state'] = 'active';
    } else if ($type === 'ventes') {
      $nav['links'][$active][1] = 'active';
    } else if ($type === 'sorties') {
      $nav['links'][$active][2] = 'active';
    } else {
      $nav['links'][0]['state'] = 'active';
    }
    return $nav;
  }

  $nav = navData('Gestion des points de collectes', 'collecte');
  ?>

  <div class="container">
      <?= configNav($nav); ?>
    <form action="../moteur/edition_points_collecte_post.php" method="post">
  <?= pointsConfig5($data) ?>
    </form>

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
  <?php foreach ($points as $p) { ?>
          <tr>
            <td><?= $p['id']; ?></td>
            <td><?= $p['timestamp']; ?></td>
            <td><?= $p['nom']; ?></td>
            <td><?= $p['adresse']; ?></td>
            <td><?= colorSpan($p) ?></td>
            <td><?= $p['commentaire']; ?></td>
            <td><?= $p['pesee_max']; ?></td>
            <td><?= bool_to_oui_non($p['visible']) ?></td>
            <td>
              <form action="modification_points_collecte.php" method="post">
                <input type="hidden" name="id" id="id" value="<?= $p['id']; ?>">
                <button class="btn btn-warning btn-sm">modifier</button>
              </form>
            </td>
          </tr>
  <?php } ?>
      </tbody>
    </table>
  </div><!-- /.container -->

  <?php
  require_once 'pied.php';
} else {
  header('Location: ../moteur/destroy.php');
}
?>
