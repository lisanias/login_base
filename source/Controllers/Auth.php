<?php

namespace Source\Controllers;

use League\OAuth2\Client\Provider\Facebook;
use League\OAuth2\Client\Provider\FacebookUser;
use League\OAuth2\Client\Provider\Google;
use League\OAuth2\Client\Provider\GoogleUser;
use Source\Models\User;
use Source\Support\Email;

class Auth extends Controller
{
    public function __construct($router)
    {
        parent::__construct($router);
    }

    public function login($data): void
    {
        $email = filter_var($data["email"], FILTER_VALIDATE_EMAIL);
        $paswd = filter_var($data["passwd"], FILTER_DEFAULT);

        if(!$email || !$paswd) {
            echo $this->ajaxResponse("message", [
                "type" => "alert",
                "message" => "Informe seu email e senha para logar"
            ]);
            return;
        }

        $user = (new User())->find(terms:"email = :e", params:"e={$email}")->fetch();
        if (!$user || !password_verify($paswd, $user->passwd)){
            echo $this->ajaxResponse("message", [
                "type" => "error",
                "message" => "Email ou senha não conferem"
            ]);
            return;
        }

        /** SOCIAL VALIDATE **/
        $this->socialValidate($user);


        $_SESSION["user"] = $user->id;
        echo $this->ajaxResponse("redirect", ["url" => $this->router->route("app.home")]);
    }

    public function register($data): void
    {
        $data = filter_var_array($data, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (in_array("", $data)) {
            echo $this->ajaxResponse("message", [
                "type" => "error",
                "message" => "Preencha todos os campos para cadastrar-se."
            ]);
            return;
        }

        $user = new User();
        $user->first_name = $data["first_name"];
        $user->last_name = $data["last_name"];
        $user->email = $data["email"];
        $user->passwd = $data["passwd"];

        /** SOCIAL VALIDATE **/
        $this->socialValidate($user);

        if (!$user->save()) {
            echo $this->ajaxResponse("message", [
                "type" => "error",
                "message" => $user->fail()->getMessage()
            ]);
            return;
        };
        
        $_SESSION["user"] = $user->id;
        echo $this->ajaxResponse("redirect", [
            "url" => $this->router->route("app.home")
        ]);

        //echo json_encode($data);
    }

    public function forget($data): void
    {
        // verifica se tem um email digitado e se o email é valido/
        $email = filter_var($data["email"], FILTER_VALIDATE_EMAIL);
        if (!$email) {
            echo $this->ajaxResponse("message", [
                "type" => "alert",
                "message" => "Informe seu email para recuperar a senha"
            ]);
            return;            
        }

        // Verifica se tem o email na base de dados
        $user = (new User())->find("email = :e", "e={$email}")->fetch();
        if (!$user) {
            echo $this->ajaxResponse("message", [
                "type" => "error",
                "message" => "Email informado não é cadastrado"
            ]);
            return;
        }

        // procedimento para nova senha
        $user->forget = (md5(uniqid(rand(),true)));
        $user->save();

        $_SESSION["forget"] = $user->id;

        $email = new Email();
        $email->add(
            subject: "Recupere sua senha | ". site("name"),
            body: $this->view->render("emails/recover", [
                "user" => $user,
                "link" => $this->router->route("web.reset", [
                    "email" => $user->email,
                    "forget" => $user->forget
                ])
            ]),
            recipient_name: "{$user->first_name} {$user->last_name}",
            recipient_email: $user->email
        );

        if (!$email->send()) {
            flash("error", "erro no email: <br>{$email->error()->getMessage()}");

            echo $this->ajaxResponse("redirect", [
                "url" => $this->router->route("web.forget")
            ]);
        }  

        flash("success", "Enviamos um link de recuperação para seu e-mail");

        echo $this->ajaxResponse("redirect", [
            "url" => $this->router->route("web.forget")
        ]);

    }

    public function reset($data): void
    {
        if ( (empty($_SESSION["forget"])) || !$user = (new User())->findById($_SESSION["forget"]) ) {
            flash("error", "Não foi possível recuperar, tente novamente");
            echo $this->ajaxResponse("redirect", [
                "url" => $this->router->route("web.forgot")
            ]);
            return;
        }

        // VERIFICA SE TEM UMA PASSWORD DIGITADA EM AMBOS OS CAMPOS
        if ( empty($data["password"]) || empty($data["password_re"]) ) {
            echo $this->ajaxResponse("message", [
                "type" => "alert",
                "message" => "Informe e repita sua nova senha"
            ]);
            return;
        }

        // VERIFICA SE AS PASSWORDS SÃO IGUAIS
        if ( $data["password"] != $data["password_re"] ) {
            echo $this->ajaxResponse("message", [
                "type" => "alert",
                "message" => "A senha digitada não é igual nos dois campos"
            ]);
            return;
        }

        // SALVAR A SENHA E FINALIZAR PROCESSO DE RECUPERAÇÃO
        $user->passwd = $data["password"];
        $user->forget = null;

        if (!$user->save()) {
            echo $this->ajaxResponse("message", [
                "type" => "error",
                "message" => $user->fail()->getMessage()
            ]);
            return;
        }
        
        unset($_SESSION["forget"]);

        flash("success", "Sua senha foi atualizada com sucesso");
        echo $this->ajaxResponse("redirect", [
            "url" => $this->router->route("web.login")
        ]);
    }

    public function facebook(): void
    {
        $facebook = new Facebook(FACEBOOK_LOGIN);
        $error = filter_input(INPUT_GET, "error", FILTER_SANITIZE_STRIPPED);
        $code = filter_input(INPUT_GET, "code", FILTER_SANITIZE_STRIPPED);

        if (!$error && !$code) {
            $auth_url = $facebook->getAuthorizationUrl(["scope" => "email"]);
            
            header( "Location: {$auth_url}");
            return;
        }

        if ($error) {
            flash("error", "Não foi possivel logar com o Facebook (1)");
            $this->router->redirect("web.login");
        }

        if ($code && empty($_SESSION["facebook_auth"])) {
            try {
                $token = $facebook->getAccessToken("authorization_code",["code" => $code]);
                $_SESSION["facebook_auth"] = serialize($facebook->getResourceOwner($token));
            } catch ( \Exception $exception) {
                flash("error", "Não foi possivel logar com o Facebook (2)");
                $this->router->redirect("web.login");
            }
        } 

        /** @var $facebook_user FacebookUser */
        $facebook_user = unserialize($_SESSION["facebook_auth"]);
        $user_by_id = (new User())->find("facebook_id = :id", "id={$facebook_user->getID()}")->fetch();

        // LOGIN BY FACEBOOK ID
        if ($user_by_id) {
            unset($_SESSION["facebook_auth"]);

            $_SESSION["user"] = $user_by_id->id;
            $this->router->redirect("web.login");
        }

         // LOGIN BY EMAIL
        $user_by_email = (new User())->find("email = :e", "e={$facebook_user->getEmail()}")->fetch();
        if ($user_by_email) {
            flash("info", "Faça {$facebook_user->getFirstName()}, faça login para conectar seu Facebook");
            $this->router->redirect("web.login");
        }

        // REGISTRAR O USUÁRIO DO FACEBOOK SE AINDA NÃO ESTIVER CADASTRADO LOCALMENTE
        $link = $this->router->route("web.login");
        flash(
            "info",
            "Olá {$facebook_user->getFirstName()} <b>se já tem uma conta clique em <a title='Fazer Login' href='{$link}'>FAZER LOGIN</a></b>, ou complete seu cadastro"
        );
        $this->router->redirect("web.register");
    }

    public function google(): void
    {
        $google = new Google(GOOGLE_LOGIN);
        $error = filter_input(INPUT_GET, "error", FILTER_SANITIZE_STRIPPED);
        $code = filter_input(INPUT_GET, "code", FILTER_SANITIZE_STRIPPED);

        if (!$error && !$code) {
            $auth_url = $google->getAuthorizationUrl();
            
            header( "Location: {$auth_url}");
            return;
        }

        if ($error) {
            flash("error", "Não foi possivel logar com o Google");
            $this->router->redirect("web.login");
        }

        if ($code && empty($_SESSION["google_auth"])) {
            try {
                $token = $google->getAccessToken("authorization_code",["code" => $code]);
                $_SESSION["google_auth"] = serialize($google->getResourceOwner($token));
            } catch ( \Exception $exception) {
                flash("error", "Não foi possivel logar com o Google");
                $this->router->redirect("web.login");
            }
        } 

        /** @var $google_user GooglekUser */
        $google_user = unserialize($_SESSION["google_auth"]);
        $user_by_id = (new User())->find("google_id = :id", "id={$google_user->getID()}")->fetch();


        // LOGIN BY GOOGLE ID
        if ($user_by_id) {
            unset($_SESSION["google_auth"]);

            $_SESSION["user"] = $user_by_id->id;
            $this->router->redirect("web.login");
        }

         // LOGIN BY EMAIL
        $user_by_email = (new User())->find("email = :e", "e={$google_user->getEmail()}")->fetch();
        if ($user_by_email) {
            flash("info", "Faça {$google_user->getFirstName()}, faça login para conectar com sua conta Google");
            $this->router->redirect("app.home");
        }

        // REGISTRAR O USUÁRIO DO FACEBOOK SE AINDA NÃO ESTIVER CADASTRADO LOCALMENTE
        $link = $this->router->route("web.login");
        flash(
            "info",
            "Olá {$google_user->getFirstName()} <b>se já tem uma conta clique em <a title='Fazer Login' href='{$link}'>FAZER LOGIN</a></b>, ou complete seu cadastro"
        );
        $this->router->redirect("web.register");

    }

    public function socialValidate(User $user): void
    {
        /**
         * FACEBOOK
         */
        if (!empty($_SESSION["facebook_auth"])) {
            /** @var $facebook_user FacebookUser */
            $facebook_user = unserialize($_SESSION["facebook_auth"]);

            $user->facebook_id = $facebook_user->getID();
            $user->photo = $facebook_user->getPictureUrl();
            $user->save();

            unset($_SESSION["facebook_auth"]);
        }

        /**
         * GOOGLE
         */
        if (!empty($_SESSION["google_auth"])) {
            /** @var $google_user GoogleUser */
            $google_user = unserialize($_SESSION["google_auth"]);

            $user->google_id= $google_user->getID();
            $user->photo = $google_user->getAvatar();
            $user->save();

            unset($_SESSION["google_auth"]);
        }
    }
}