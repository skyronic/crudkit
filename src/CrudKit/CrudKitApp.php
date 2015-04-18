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


    /**
     * Render your CrudKit app and return it as a string
     */
    public function renderToString () {
        if(!isset($this->staticRoot)) {
            throw new \Exception("Please set static root using `setStaticRoot`");
        }

        $pageMap = [];
        /** @var BasePage $pageItem */
        foreach($this->pages as $pageItem) {
            $pageMap []= array(
                'id' => $pageItem->getId(),
                'name' => $pageItem->getName()
            );
        }

        $twig = new TwigUtil();
        return $twig->renderTemplateToString("layout.twig", array(
            'staticRoot' => $this->staticRoot,
            'pageMap' => $pageMap
        ));
    }

    /**
     * Render your CrudKit app and output to HTML
     */
    public function render () {
        echo $this->renderToString();
    }
}