<?php

namespace CrudKit;

use CrudKit\Pages\BasePage;
use CrudKit\Util\TwigUtil;
use Twig_Autoloader;
use Twig_Environment;
use Twig_Loader_Filesystem;


class CrudKitApp {
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

        $twig = new TwigUtil();
        return $twig->renderTemplateToString("layout.twig", array(
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