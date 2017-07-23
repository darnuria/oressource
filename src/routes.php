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

/* POST /login
 * On reponds en JSON a la requete POST qui nous est envoyer.
 * Si le login est valide on renvoie un code 200 et un json qui a terme sera
 * classe qui represente un utilisateur.
 *
 * Sinon on renvoie une 401 Unauthorized et un petit document JSON qui explique l'erreur.
 *
 * On attends un JSON du schema suivant:
 * login.json
 * {
 *   'username': FILTER_VALIDATE_EMAIL, // A terme on pourrais etre moins restrictif.
 *   'password': octets bruts va etre hasher aucune validation/sanitizitation.
 * }
 * Reponse:
 * HTTPS Status code: 200 - OK
 * { 'status': 'Accepted' }
 * Ou en cas d'echec.
 * HTTP Status code: 401 - Unauthorized
 * { 'error': 'Mauvais identifiant ou mot de passe' }
 */
$app->post('/login', function(Request $request, Response $resp, $args) {
  $this->logger->info("[Routing][POST] /login");
  $this->logger->debug("[Validating][POST] /login");
  $json = validate_json_login($request->getParsedBody());
  try {
    $user = login_user($this->database, $json['username'], $json['password']);
    session_start();
    $structure = structure($this->database);
    set_session($user, $structure);
    $this->logger->info("[Login] Success.");
    // A terme on revera un document json decrivant l'utilisateur connecter.
    return $resp->WithJson(['status' => 'OK'], 200);
  } catch (Exception $e) {
    $this->logger->info("[Login] Failed.");
    return $resp->WithJson(['error' => $e->getMessage()], 401);
  }
})->setName('login');

$app->group('/sorties', function () use ($app) {
  $app->post('/collectes', function(Request $request, Response $resp, $args) {
    $this->logger->info("[Routing][POST] /collectes");
    $this->logger->debug("[Validating][POST] /collectes");
    try {
      $json = validate_json_collecte($request->getParsedBody());
      if (is_allowed_collecte_id($json['id_point'])) {
        $json['timestamp'] = user_datation($json['antidate']);
        try {
          $bdd = $this->database;
          $bdd->beginTransaction();
          $id_collecte = insert_collecte($bdd, $json);
          insert_items_collecte($bdd, $id_collecte, $json);
          $bdd->commit();
          return $resp->withJson(['id_collecte' => $id_collecte], 200);
        } catch (InvalidArgumentException $e) {
          $bdd->rollback();
          throw UnexpectedValueException($e->getMessage());
        }
      } else {
        return $resp->withJson(['error' => 'Action interdite pour cet utilisateur.'], 403);
      }
    } catch (UnexpectedValueException $e) {
      return $resp->withJson(['error' => $e->getMessage()], 400);
    }
  })->add(allowed_point_collecte);
})->add(allowed_collectes);


$app->group('/sorties', function () use ($app) {
  $app->post('', function(Request $request, Response $resp, $args) {
    $this->logger->info("[Routing][POST] /sorties");
    $this->logger->debug("[Validating][POST] /sorties");
    $bdd = $this->database;
    try {
      $json = validate_json_sorties($request->getParsedBody());
      if (!is_allowed_sortie_id($json['id_point'])) {
        return $resp->withJson(['error' => 'Action interdite.'], 403);
      }

      $json['timestamp'] = user_datation($json['antidate']);

      $bdd->beginTransaction();
      $id_sortie = insert_sortie($bdd, $json);
      $requete_OK = false;
      $classe = $json['classe'];

      if ($classe === 'sorties' || $classe === 'sortiesc' || $classe === 'sortiesr') {
        if (count($json['items']) > 0) {
          insert_items_sorties($bdd, $id_sortie, $json);
          $requete_OK = true;
        }
        if (count($json['evacs']) > 0) {
          insert_evac_sorties($bdd, $id_sortie, $json);
          $requete_OK = true;
        }
      } elseif ($classe === 'sortiesd') {
        if (count($json['evacs']) > 0) {
          insert_evac_sorties($bdd, $id_sortie, $json);
          $requete_OK = true;
        }
      } elseif ($classe === 'sortiesp') {
        if (count($json['evacs']) > 0) {
          $json['commentaire'] = '';
          insert_poubelle_sorties($bdd, $id_sortie, $json);
          $requete_OK = true;
        }
      } else {
        throw new UnexpectedValueException("Classe de sortie inconnue");
      }

      if ($requete_OK) {
        $bdd->commit();
        return $resp->withJson(['id_sortie' => $id_sortie], 200);
      } else {
        throw new UnexpectedValueException("Insertion sans objet ni evac abbandon.");
      }
    } catch (UnexpectedValueException $e) {
      $bdd->rollback();
      return $resp->withJson(['error' => $e->getMessage()], 400);
    }
  })->add(allowed_point_sortie);
})->add(allowed_sorties);
