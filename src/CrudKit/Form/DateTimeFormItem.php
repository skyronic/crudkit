<?php

namespace CrudKit\Form;

class DateTimeFormItem extends HorizontalItem{

    public function renderInternal()
    {
        $ngModel = $this->form->getNgModel();
        $value = isset($this->config['value']) ? $this->config['value'] : "";

        return <<<COMP
        <input type="text" class="form-control" id="{$this->id}" placeholder="" ng-model="$ngModel.{$this->key}" />
        <input type="date" class="form-control" datepicker-popup ng-model="$ngModel.{$this->key}" is-open="openStatus.{$this->key}" />
              <span class="input-group-btn">
                <button type="button" class="btn btn-default" ng-click="openStatus.{$this->key} = true"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>
COMP;
    }
}