<?php

namespace Source\Controllers;

use Source\Models\User;

class Web extends Controller
{
    public function __construct($router)
    {
        parent::__construct($router);

        if (!empty($_SESSION["user"])) {
            $this->router->redirect("app.home");
        }
    }

    public function login(): void
    {
        $head = $this->seo->optimize(
            "Entre em sua conta no " . site("name"),
            site("desc"),
            $this->router->route("web.login"),
            routeImage("login")
        )->render();

        $form_user = new \stdClass();
        $form_user->email = !empty($_SESSION["facebook_auth"]) ? unserialize($_SESSION["facebook_auth"])->getEmail(): 
            (!empty($_SESSION["google_auth"]) ? unserialize($_SESSION["google_auth"])->getEmail() : null);

        echo $this->view->render("theme/login", [
            "head" => $head,
            "user" => $form_user
        ]);
    }

    public function register(): void
    {
        $head = $this->seo->optimize(
            "Crie sua conta no " . site("name"),
            site("desc"),
            $this->router->route("web.register"),
            routeImage("Register")
        )->render();

        $form_user = new \stdClass();
        $form_user->first_name = null;
        $form_user->last_name = null;
        $form_user->email = null;


        $social_user = (
            !empty($_SESSION["facebook_auth"]) ? unserialize($_SESSION["facebook_auth"]): 
            (!empty($_SESSION["google_auth"]) ? unserialize($_SESSION["google_auth"]) : null)
        );
        if ($social_user) {
            $form_user->first_name = $social_user->getFirstName();
            $form_user->last_name = $social_user->getLastName();
            $form_user->email = $social_user->getEmail();
        }


        echo $this->view->render("theme/register", [
            "head" => $head,
            "user" => $form_user
        ]);
    }

    public function forget(): void
    {
        $head = $this->seo->optimize(
            "Recupere sua senha | " . site("name"),
            site("desc"),
            $this->router->route("web.forget"),
            routeImage("Forget")
        )->render();

        echo $this->view->render("theme/forget", [
            "head" => $head
        ]);
    }

    public function reset($data): void
    {
        if (empty($_SESSION["forget"])) {
            flash("info", "Informe seu E-mail para recuperar a senha");
            $this->router->redirect("web.forget");
        }

        $email = filter_var($data["email"], FILTER_VALIDATE_EMAIL);
        $forget = filter_var($data["forget"], FILTER_DEFAULT);

        $errForget = "Não foi possivel recuperar, tente novamente";

        if ( !$email || !$forget ) {
            flash("error", $errForget);
            $this->router->redirect("web.forget");
        }

        $user = (new User())->find("email = :e AND forget = :f", "e={$email}&f={$forget}")->fetch();
        if (!$user) {
            flash("error", $errForget);
            $this->router->redirect("web.forget");
        }

        $head = $this->seo->optimize(
            "Crie sua nova senha | " . site("name"),
            site("desc"),
            $this->router->route("web.reset"),
            routeImage("Reset")
        )->render();

        echo $this->view->render("theme/reset", [
            "head" => $head
        ]);
    }

    public function error($data): void
    {
        $error = filter_var($data["errcode"], FILTER_VALIDATE_INT);

        $head = $this->seo->optimize(
            "Oooppss {$error} | " . site("name"),
            site("desc"),
            $this->router->route("web.error", ["errcode" => $error]),
            routeImage("Error")
        )->render();

        echo $this->view->render("theme/error", [
            "head" => $head,
            "error" => $error
        ]);
    }
}