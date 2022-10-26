<?php

namespace Source\Controllers;

use CoffeeCode\Optimizer\Optimizer;
use CoffeeCode\Router\Router;
use League\Plates\Engine;

abstract class Controller
{
    /** @var Engine */
    protected $views;

    /** @var Router */
    protected $router;

    /** @var Optimizer */
    protected $seo;

    public function __construct($router)
    {
        $this->router = $router;
        //$this->view = new Engine(dirname(__DIR__, 2) . "/views", "php");
        $this->view = Engine::create(dirname(__DIR__, 2) . "/views", "php");
        $this->view->addData(["router" => $this->router]);

        $this->seo = new Optimizer();
        $this->seo->openGraph(site("name"), site("locale"), "article")
                ->publisher(social("facebook_page"), social("facebook_author"))
                ->twitterCard(SOCIAL["twitter_creator"], SOCIAL["twitter_site"], site("domain"))
                ->facebook(social("facebook_appId"));
    }

    public function ajaxResponse($param, $values): string
    {
        return json_encode([$param=>$values]);
    }
}