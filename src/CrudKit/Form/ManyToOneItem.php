<?php

namespace CrudKit\Form;


class ManyToOneItem extends HorizontalItem {
    public function renderInternal()
    {
        return <<<COMP
        <select ng-model="formItems.{$this->key}" >
            <option ng-repeat="item in selectValues.{$this->key}" value="{{ item.id }}">{{ item.label }}</option>
        </select>
COMP;
    }
}