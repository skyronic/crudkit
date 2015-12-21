<?php

namespace CrudKit\Form;

class TextFormItem extends HorizontalItem{

    public function renderInternal()
    {
    	$directives = $this->getAngularDirectives ();
        $value = isset($this->config['value']) ? $this->config['value'] : "";
        return <<<COMP
        <input type="text" class="form-control" id="{$this->id}" placeholder="" $directives />
COMP;
    }
}