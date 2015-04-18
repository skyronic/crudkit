<?php

namespace CrudKit;

use CrudKit\Pages\BasePage;
use Twig_Autoloader;
use Twig_Environment;
use Twig_Loader_Filesystem;

define("CK_BASE_PATH", dirname(dirname(__FILE__)));

class CrudKit {
    protected $staticRoot;

    /**
     * Set a static root which contains the "crudkit/" directory of css and JS
     * @param $staticRoot
     */
    public function setStaticRoot ($staticRoot) {
        // TODO: strip trailing slash
        $this->staticRoot = $staticRoot;
    }

    /**
     * Render your CrudKit app and return it as a string
     */
    public function renderToString () {
        if(!isset($this->staticRoot)) {
            throw new \Exception("Please set static root using `setStaticRoot`");
        }

        Twig_Autoloader::register();
        $loader = new Twig_Loader_Filesystem(CK_BASE_PATH."/templates/");
        $twig = new Twig_Environment($loader, array(
        ));
        return $twig->render("layout.twig", array(
            'staticRoot' => $this->staticRoot
        ));
    }

    /**
     * Render your CrudKit app and output to HTML
     */
    public function render () {
        echo $this->renderToString();
    }
}