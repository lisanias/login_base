<?php

/**
 * SITE CONFIG
 */

use Illuminate\Support\Facades\DB;

define("SITE", [
    "name" => "Auth em MVC com PHP",
    "desc" => "Sistema de login com o google e facebook",
    "domain" => "login.com",
    "locale" => "pt_BR",
    "root" => "https://aprender.test/PHP/codigoaberto/t1"
]);

/**
 * SITE MINIFY
 */
if ($_SERVER["SERVER_NAME"] == "aprender.test") {
    require __DIR__ . "/Minify.php";
}

/**
 * DATABASE CONECT
 */
const DATA_LAYER_CONFIG = [
    "driver" => "mysql",
    "host" => "localhost",
    "port" => "3306",
    "dbname" => "auth",
    "username" => "root",
    "passwd" => "",
    "options" => [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_CASE => PDO::CASE_NATURAL
    ]
];

/**
 * SOCIAL CONFIG
 */
define("SOCIAL" , [
    "facebook_page" => "lisaniasloback",
    "facebook_author" => "lisanias",
    "facebook_appId" => "",
    "twitter_creator" => "@lisaniasloback",
    "twitter_site" => "@lisaniasloback",
    "email" => "pastorlisanias@gmail.com"
]);

/**
 * MAIL CONNECT
 */
 //Server settings

define("MAIL2", [
    "host" => "mail.lisaniasloback.com",
    "port" => "465",
    "user" => "contato@lisaniasloback.com",
    "passwd" => "lucas#3$1",
    "from_name" => "Lisanias Loback",
    "from_email" => "contato@lisaniasloback.com" 
]);
/**
 * MAILLAZI.COM
 * user:lisanias@hotmail.com
 * Dominio: webig.pro.br
 * server: smtp.mailazy.com
 * Port: 587
 * usename: cae1ua5b6okrmgtgubi0oMkqfYcEMs
 * key: wCwNKWYAwsRHrVvdqgddjsBzooQ.XGGSKXKpCUvpbJz67txmmc
 * 
 * SendGrid API Key
 * testeCA
 * Key: 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
 * 
 * SENDGRIG.COM
 * Conta:contato@lisaniasloback.com
 * Senha: lidia@lary#duda#
 * lisaniasloback.com
 * key: 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
 * server: 	smtp.sendgrid.net
 * ports: 587 (25, 587 for unencrypted/TLS connections; 465	for SSL connections)
 * Username: apikey * 
 */
define("MAIL", [
    "host" => "smtp.sendgrid.net",
    "port" => "587",
    "user" => "apikey",
    "passwd" => "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",
    "from_name" => "Lisanias Teste",
    "from_email" => "contato@lisaniasloback.com" 
]);

define("MAIL_mailazy", [
    "host" => "smtp.mailazy.com",
    "port" => "587",
    "user" => "cae1ua5b6okrmgtgubi0oMkqfYcEMs",
    "passwd" => "wCwNKWYAwsRHrVvdqgddjsBzooQ.XGGSKXKpCUvpbJz67txmmc",
    "from_name" => "Lisanias Teste",
    "from_email" => "lisanias@hotmail.com" 
]);


/**
 * SOCIAL LOGIN: FACEBOOK
 */
define("FACEBOOK_LOGIN", [
    'clientId'          => '1189602211631277',
    'clientSecret'      => '14246e422f45192a3bb7d7589916a5d7',
    'redirectUri'       => 'https://aprender.test/PHP/codigoaberto/t1/facebook',
    'graphApiVersion'   => 'v15.0',
]);

/**
 *  SOCIAL LOGIN: GOOGLE
 */
define("GOOGLE_LOGIN", [
    'clientId'     => '1081599119050-vl24vs88732ur73ri0orfmbh1nca0bj4.apps.googleusercontent.com',
    'clientSecret' => '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',
    'redirectUri'  => 'https://webig.pro.br/login/google',
]);