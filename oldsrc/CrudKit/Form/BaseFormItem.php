<?php

namespace CrudKit\Form;

use CrudKit\Util\FormHelper;

abstract class BaseFormItem {
    protected $config = array();
    protected $key = "";
    protected $id = "";

    protected $changeFunc = "registerChange";
    /**
     * @var FormHelper
     */
    protected $form = null;
    public function __construct ($form, $key, $config) {
        $this->config = $config;
        $this->key = $key;
        $this->form = $form;
        $this->id = "control-{$this->key}";
    }

    public abstract function render ();
    public abstract function renderInline ();
}