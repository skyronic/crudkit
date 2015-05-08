<?php

namespace CrudKit\Form;

class TextFormItem extends HorizontalItem{

    public function renderInternal()
    {
        $ngModel = $this->form->getNgModel();
        $value = isset($this->config['value']) ? $this->config['value'] : "";
        return <<<COMP
        <input type="text" class="form-control" id="{$this->id}" placeholder="" ng-model="$ngModel.{$this->key}" />
COMP;
    }
}