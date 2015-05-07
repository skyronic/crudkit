<?php

namespace CrudKit\Form;

class OneToManyItem extends BaseFormItem {

    public function render()
    {
        $label = $this->config['label'];
        return  <<<RENDER
        <div>
        <h4>${label}</h4>
        </div>
RENDER;
    }
}