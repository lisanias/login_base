<?php

// composer update --ignore-platform-reqs

ob_start();
session_start();

require __dir__ . "/vendor/autoload.php";

use CoffeeCode\Router\Router;

$router = new Router(site());
$router->namespace(namespace: "Source\Controllers");

/**
 * WEB
 */
$router->group(null);
$router->get(route: "/", handler: "Web:login", name:"web.login");
$router->get(route: "/cadastrar", handler: "Web:register", name:"web.register");
$router->get(route: "/recuperar", handler: "Web:forget", name:"web.forget");
$router->get(route: "/senha/{email}/{forget}", handler: "Web:reset", name:"web.reset");

/**
 * AUTH
 */
$router->group(null);
$router->post(route: "/login", handler: "Auth:login", name:"auth.login");
$router->post(route: "/register", handler: "Auth:register", name:"auth.register");
$router->post(route: "/forget", handler: "Auth:forget", name:"auth.forget");
$router->post(route: "/reset", handler: "Auth:reset", name:"auth.reset");

/**
 * AUTH SOCIAL
 */
$router->group(null);
$router->get(route:"/facebook", handler:"Auth:facebook", name:"auth.facebook");
$router->get(route:"/google", handler:"Auth:google", name:"auth.google");

/**
 * PROFILE
 */
$router->group(group:"/me");
$router->get(route:"/", handler:"App:home", name:"app.home");
$router->get(route:"/sair", handler:"App:logoff", name:"app.logoff");

/**
 * ERRORS
 */
$router->group(group: "ops");
$router->get( route: "/{errcode}", handler:"Web:error", name:"web.error");

/**
 * ROUTE PROCESS
 */
$router->dispatch();

/**
 * ERRORS PROCESS
 */
if ($router->error()) {
    $router->redirect( "web.error", ["errcode" => $router->error()]);
}

ob_end_flush();