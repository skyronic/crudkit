<?php
/**
 * Created by PhpStorm.
 * User: anirudh
 * Date: 23/04/15
 * Time: 11:56 PM
 */

namespace CrudKit\Util;


use CrudKit\Form\TextFormItem;

class FormHelper {
    protected $config = array();
    protected $items = array();
    public function __construct ($config, $items) {
        $this->config = $config;
        $this->items = $items;
    }

    public function render ($order) {
        $twig = new TwigUtil();
        $items = array();

        foreach($order as $formKey) {
            $items []= $this->createFormItem($formKey, $this->items[$formKey]);
        }
        return $twig->renderTemplateToString("util/form.twig", array(
            'formItems' => $items,
            'config' => $this->config
        ));
    }

    protected function createFormItem ($key, $config) {
        switch($config['type']) {
            case "text":
                return new TextFormItem("foo", $key, $config);
            default:
                throw new \Exception("Can't find form item type");
        }
    }

    public function setValues($values)
    {
        foreach($values as $key => $val) {
            $this->items[$key]['value'] = $val;
        }
    }
}