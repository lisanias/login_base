<?php

require __dir__ . "/vendor/autoload.php";

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '1189602211631277',
      cookie     : true,
      xfbml      : true,
      version    : '15.0'
    });
      
    FB.AppEvents.logPageView();   
      
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "https://connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>
</body>
</html>

// use CoffeeCode\DataLayer\Connect;

// $conn = Connect::getInstance();
// $error = Connect::getError();

// if ($error) {
//     echo $error->getMessage();
//     die();
// }

// $query = $conn->query("select * from users");
// var_dump($query->fetchAll());

/* 
use Source\Models\User;

$user = new User();
$list = $user->find()->fetch(true);

foreach ($list as $userIten) {
    var_dump($userIten->data()->first_name);
} 
*/

/* use Source\Support\Email;

$email = new Email();

$email->add(
    "Teste de Email",
    "<h1>Teste</h1>Será que chegou!",
    "Loback Teste",
    "lisanias@hotmail.com"
)->send();

if (!$email->error()){
    var_dump(true);
} else {
    echo $email->error()->getMessage();
} */