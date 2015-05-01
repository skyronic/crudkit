<?php
/**
 * Created by PhpStorm.
 * User: anirudh
 * Date: 23/04/15
 * Time: 11:56 PM
 */

namespace CrudKit\Util;


use CrudKit\Form\ManyToOneItem;
use CrudKit\Form\TextFormItem;
use utilphp\util;

class FormHelper {
    protected $config = array();
    protected $items = array();

    // Extra params to be passed to the form
    protected $params = array();
    public function __construct ($config, $items) {
        $this->config = $config;
        $this->items = $items;
    }

    public function setGetValuesUrl ($url) {
        $this->params['fetchValues'] = true;
        $this->params ['getValuesUrl'] = $url;
    }

    public function setSetValuesUrl ($url) {
        $this->params['setValues'] = true;
        $this->params ['setValuesUrl'] = $url;
    }

    public function addRelationship ($fKey) {
        $this->params['hasRelationships'] = true;
        $this->relationships = $fKey;
    }

    protected $relationships = array();

    public function render ($order) {
        $twig = new TwigUtil();
        $items = array();

        foreach($order as $formKey) {
            $items []= $this->createFormItem($formKey, $this->items[$formKey]);
        }
        $this->params['formItems'] = $items;
        $this->params['config'] = $this->config;

        if(isset($this->params['hasRelationships'])) {
            $this->params['relationships'] = $this->relationships;
        }
        return $twig->renderTemplateToString("util/form.twig", $this->params);
    }

    public function validate ($values) {
        // TODO: Fix me
        return true;
    }

    protected function createFormItem ($key, $config) {
        $type = $config['type'];
        switch($type) {
            case "string":
                return new TextFormItem("foo", $key, $config);
            case "foreign_manyToOne":
                $this->addRelationship($key);
                return new ManyToOneItem("foo", $key, $config);
            default:
                throw new \Exception("Can't find form item type: $type");
        }
    }

    public function setValues($values)
    {
        foreach($values as $key => $val) {
            $this->items[$key]['value'] = $val;
        }
    }
}