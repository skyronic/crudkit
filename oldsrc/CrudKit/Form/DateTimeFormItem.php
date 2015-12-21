<?php

namespace CrudKit\Form;

class DateTimeFormItem extends HorizontalItem{

    public function renderInternal()
    {
    	$directives = $this->getAngularDirectives ();

        return <<<COMP
        <div class="input-group">
        <datepicker show-weeks="true" class="well well-sm" $directives/>
        </div>
COMP;
    }
}