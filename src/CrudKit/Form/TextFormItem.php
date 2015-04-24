<?php

namespace CrudKit\Form;

class TextFormItem extends HorizontalItem{

    public function renderInternal()
    {
        return <<<COMP
        <input type="text" class="form-control" id="{$this->id}" placeholder="" ng-model="formItems.{$this->key}" />
COMP;
    }
}