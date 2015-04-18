<?php

namespace CrudKit\Util;

use Twig_Autoloader;
use Twig_Environment;
use Twig_Loader_Filesystem;


class TwigUtil {
    protected $basePath = null;
    public function __construct () {
        // TODO: make a more elegant way of getting the template directory
        $this->basePath =  dirname(dirname(dirname(__FILE__)))."/templates/";
    }

    public function renderTemplateToString ($name, $dictionary) {
        Twig_Autoloader::register();
        $loader = new Twig_Loader_Filesystem($this->basePath);
        $twig = new Twig_Environment($loader, array(
        ));
        return $twig->render($name, $dictionary);
    }
}