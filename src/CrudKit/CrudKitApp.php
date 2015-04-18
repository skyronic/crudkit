<?php

namespace CrudKit;

use CrudKit\Controllers\MainController;
use CrudKit\Pages\BasePage;
use CrudKit\Util\TwigUtil;
use Twig_Autoloader;
use Twig_Environment;
use Twig_Loader_Filesystem;


class CrudKitApp {
    protected $staticRoot;
    /**
     * @var array[BasePage]
     */
    protected $pages = array();

    /**
     * Set a static root which contains the "crudkit/" directory of css and JS
     * @param $staticRoot
     */
    public function setStaticRoot ($staticRoot) {
        // TODO: strip trailing slash
        $this->staticRoot = $staticRoot;
    }

    /**
     * @param $page BasePage
     */
    public function addPage ($page) {
        $this->pages []= $page;
    }

    public function getPages () {
        return $this->pages;
    }

    public function getStaticRoot () {
        return $this->staticRoot;
    }


    /**
     * Render your CrudKit app and return it as a string
     */
    public function renderToString () {
        if(!isset($this->staticRoot)) {
            throw new \Exception("Please set static root using `setStaticRoot`");
        }

        $controller = new MainController($this);
        return $controller->handle();
    }

    /**
     * Render your CrudKit app and output to HTML
     */
    public function render () {
        echo $this->renderToString();
    }
}