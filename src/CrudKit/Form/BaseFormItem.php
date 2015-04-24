<?php

namespace CrudKit\Form;

abstract class BaseFormItem {
    protected $config = array();
    protected $key = "";
    protected $id = "";
    protected $formId = "";
    public function __construct ($formId, $key, $config) {
        $this->config = $config;
        $this->key = $key;
        $this->formId = $formId;
        $this->id = "control-{$this->formId}-{$this->key}";
    }

    public abstract function render ();
}