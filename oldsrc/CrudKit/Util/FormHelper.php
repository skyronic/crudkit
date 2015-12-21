<?php

namespace CrudKit\Util;


use CrudKit\Form\ManyToOneItem;
use CrudKit\Form\TextFormItem;

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

    public function setNewItem () {
        $this->params['newItem'] = true;
        $this->jsParams['newItem'] = true;
    }

    public function setDeleteUrl ($url) {
        $this->params['canDelete'] = true;
        $this->params['deleteUrl'] = $url;
    }

    public function addRelationship ($fKey, $type) {
        $this->jsParams['hasRelationships'] = true;
        if(!isset($this->jsParams['relationships'])) {
            $this->jsParams['relationships'] = array();
        }
        $this->jsParams['relationships'] []= array(
            'type' => $type,
            'key' => $fKey
        );
    }

    protected $ngModel = "formItems";

    protected $relationships = array();

    protected $formItems = array();

    public function render ($order) {
        $twig = new TwigUtil();

        $this->params['formItems'] = $this->formItems;
        $this->params['id'] = $this->id;

        ValueBag::set($this->id, $this->jsParams);

        return $twig->renderTemplateToString("util/form.twig", $this->params);
    }

    public function renderInline () {
        $twig = new TwigUtil();

        $this->params['formItems'] = $this->formItems;
//        $this->params['id'] = $this->id;

//        ValueBag::set($this->id, $this->jsParams);

        return $twig->renderTemplateToString("util/form_inline.twig", $this->params);
    }

    /**
     * @return string
     */
    public function getNgModel()
    {
        return $this->ngModel;
    }

    /**
     * @param string $ngModel
     */
    public function setNgModel($ngModel)
    {
        $this->ngModel = $ngModel;
    }
}