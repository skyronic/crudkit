<?php

namespace CrudKit\Form;

use CrudKit\Data\BaseDataProvider;

class OneToManyItem extends BaseFormItem {

    public function render()
    {
        $ngModel = $this->form->getNgModel();
        $label = $this->config['label'];

        /**
         * @var $provider BaseDataProvider
         */
        $provider = $this->config['fk_provider'];
        $providerForm = $provider->getEditForm();
        $providerForm->setNgModel("inlineItem");
        $inlineContent = $providerForm->renderInline();

        return  <<<RENDER
        <div>
        <h4>${label}</h4>
        <div ng-repeat="inlineItem in $ngModel.{$this->key}">
        $inlineContent
        </div>
        </div>
RENDER;
    }

    public function renderInline()
    {
        return "";
    }
}