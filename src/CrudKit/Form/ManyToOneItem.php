<?php

namespace CrudKit\Form;


class ManyToOneItem extends HorizontalItem {
    public function renderInternal()
    {
        return <<<COMP
        <select ng-model="formItems.{$this->key}" ng-options="item.id as item.label for item in selectValues.{$this->key}" >
        </select>
COMP;
    }
}