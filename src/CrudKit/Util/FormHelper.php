<?php
/**
 * Created by PhpStorm.
 * User: anirudh
 * Date: 23/04/15
 * Time: 11:56 PM
 */

namespace CrudKit\Util;


class FormHelper {
    protected $config = array();
    protected $items = array();
    public function __construct ($config, $items) {
        $this->config = $config;
        $this->items = $items;
    }

    public function render () {
        $twig = new TwigUtil();
        return $twig->renderTemplateToString("util/form.twig", array(
            'formItems' => $this->items,
            'config' => $this->config
        ));
    }
}