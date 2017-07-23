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

namespace App;

use \PDO;

final class Database extends PDO {
  public function __construct(array $settings) {
    $host = $settings['host'];
    $port = $settings['port'];
    $base = $settings['base'];

    $password = $settings['password'];
    $user = $settings['user'];
    unset($settings['user']);
    unset($settings['password']);
    $dns = "mysql:host=$host:port=$port;dbname=$base;charset=utf8";
    parent::__construct($dns, $user, $password, $settings['debug']);
  }
}
