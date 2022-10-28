<?php

require __DIR__ . "/vendor/autoload.php";

use Source\Support\Email;

$email = new Email();

die("terminou");

$email->add(
    subject: "Email enviado por webig.pro.br",
    body: "<h1>Email de Teste</h1>Vamos ver se chegou e se deu certo",
    recipient_name: "Lisanias Loback",
    recipient_email: "pastorlisanias@gmail.com"
)->send();

if (!$email->error()) {
    var_dump(true);
} else {
    echo $email->error()->getMessage();
}
