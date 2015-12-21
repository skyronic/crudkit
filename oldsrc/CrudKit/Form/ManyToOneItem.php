<?php

namespace CrudKit\Form;


class ManyToOneItem extends HorizontalItem {
    public function renderInternal()
    {
        $ngModel = $this->form->getNgModel();
        $directives = $this->getAngularDirectives ();
        return <<<COMP
        <select class="form-control" $directives ng-options="item.id as item.label for item in selectValues.{$this->key}" >
        </select>
COMP;
    }

    public function renderInline () {
        // don't render a fkey item inline
        return "";
    }
}