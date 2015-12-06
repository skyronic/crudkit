<?php

namespace CrudKit;

use CrudKit\Controllers\MainController;
use CrudKit\Pages\BasePage;
use CrudKit\Util\TwigUtil;
use Twig_Autoloader;
use Twig_Environment;
use Twig_Loader_Filesystem;


class CrudKitApp {
    protected $staticRoot = "crudkit/src/static/";
    /**
     * @var array[BasePage]
     */
    protected $pages = array();

    /**
     * @var array
     */
    protected $pageById = array();

    protected $title = "CrudKit";

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
        $this->pageById[$page->getId()] = $page;
    }

    public function setAppName ($title) {
        $this->title = $title;
    }

    public function getAppName () {
        return $this->title;
    }

    public function getPages () {
        return $this->pages;
    }

    public function getStaticRoot () {
        return $this->staticRoot;
    }

    public function getDefaultPage () {
        if(isset($this->pages[0]))
            return $this->pages [0];
        else
            return null;
    }

    protected $readOnly = false;
    public function setReadOnly ($readOnlyFlag) {
        $this->readOnly = $readOnlyFlag;
    }

    public function isReadOnly () {
        return $this->readOnly;
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
     * @param $id
     * @return BasePage
     */
    public function getPageById ($id) {
        if(isset($this->pageById[$id])) {
            return $this->pageById[$id];
        } else {
            return null;
        }
    }

    /**
     * Render your CrudKit app and output to HTML
     */
    public function render () {
        $content = $this->renderToString();

        // Headers are also calculated in render to string
        if($this->isJsonResponse()) {
            header("Content-type: application/json;");
        }


        echo $content;
    }

    protected $jsonResponse = false;

    /**
     * @return boolean
     */
    public function isJsonResponse()
    {
        return $this->jsonResponse;
    }

    /**
     * @param boolean $jsonResponse
     */
    public function setJsonResponse($jsonResponse)
    {
        $this->jsonResponse = $jsonResponse;
    }

}