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

function allowed_collectes(ServerRequestInterface $request, ResponseInterface $response, $next) {
  if (is_allowed_collecte()) {
    return $next($request, $response);
  } else {
    return $resp->withJson(['error' => 'Action interdite pour cet utilisateur.'], 403);
  }
}

function allowed_sorties(ServerRequestInterface $request, ResponseInterface $response, $next) {
  if (is_allowed_collecte()) {
    return $next($request, $response);
  } else {
    return $resp->withJson(['error' => 'Action interdite pour cet utilisateur.'], 403);
  }
}

// middleware applicatif verifie la session en cours.
function legit_session(ServerRequestInterface $request, ResponseInterface $response, $next) {
  if (is_valid_session()) {
    return $next($request, $response);
  } elseif ($request->getUri()->getPath() === '/login') {
    session_destroy();
    return $next($request, $response);
  } else {
    session_destroy();
    return $resp->withJson(['error' => 'Action interdite pour cet utilisateur.'], 403);
  }
}
