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

if (PHP_SAPI == 'cli-server') {
  // To help the built-in PHP dev server, check if the request was actually for
  // something which should probably be served as a static file
  $url = parse_url($_SERVER['REQUEST_URI']);
  $file = __DIR__ . $url['path'];
  if (is_file($file)) {
    return false;
  }
}

require_once __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$settings = [];
try {
  $settings = require_once __DIR__ . '/../src/settings.php';
} catch (Exception $e) {
  echo 'Erreur dans le ficher de configuration src/settings.php';
  echo "Details: {$e->getMessage()}";
  die();
}

$app = new \Slim\App($settings);

require_once __DIR__ . '../core/validation.php';
require_once __DIR__ . '../core/sessions.php';
require_once __DIR__ . '../core/requetes.php';

// Set up dependencies
require_once __DIR__ . '/../src/dependencies.php';

// Register middleware
require_once __DIR__ . '/../src/middleware.php';

// Register routes
require_once __DIR__ . '/../src/routes.php';

$app->add(legit_session);
$app->run();
