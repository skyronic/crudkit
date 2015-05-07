<?php

namespace CrudKit\Util;


use CrudKit\Form\ManyToOneItem;
use CrudKit\Form\TextFormItem;
use utilphp\util;

class FormHelper {
    protected $id = "default_form";

    protected $jsParams = array();

    // Extra params to be passed to the form
    protected $params = array();
    public function __construct () {

    }

    public function addItem ($item) {
        $this->formItems []= $item;
    }

    public function setPageId ($page) {
        $this->params['pageId'] = $page;
        $this->jsParams['pageId'] = $page;
    }

    public function setItemId ($itemId) {
        $this->params['itemId'] = $itemId;
        $this->jsParams['itemId'] = $itemId;
    }

    public function addRelationship ($fKey, $type) {
        $this->jsParams['hasRelationships'] = true;
        if(!isset($this->jsParams['relationships'])) {
            $this->jsParams['relationships'] = array();
        }
        $this->jsParams['relationships'] []= array(
            'type' => 'manyToOne',
            'key' => $fKey
        );
    }

    protected $relationships = array();

    protected $formItems = array();

    public function render ($order) {
        $twig = new TwigUtil();
        $items = array();

        $this->params['formItems'] = $items;
        $this->params['id'] = $this->id;

        ValueBag::set($this->id, $this->jsParams);

        return $twig->renderTemplateToString("util/form.twig", $this->params);
    }

    public function validate ($values) {
        // TODO: Fix me
        return true;
    }

    public function setValues($values)
    {
        foreach($values as $key => $val) {
//            $this->items[$key]['value'] = $val;
        }
    }
}