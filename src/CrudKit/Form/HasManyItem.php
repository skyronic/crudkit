<?php

namespace CrudKit\Form;

use CrudKit\Data\BaseDataProvider;

class HasManyItem extends BaseFormItem {

    public function render()
    {
        $ngModel = $this->form->getNgModel();
        $label = $this->config['label'];

        return  <<<RENDER
        <div>
        <h4>${label}</h4>
        <div>
            <p>Has Many will come here</p>
        </div>
        </div>
RENDER;
    }

    public function renderInline()
    {
        return "";
    }
}