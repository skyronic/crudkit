<?php
namespace CrudKit\Form;


abstract class HorizontalItem extends BaseFormItem{
    public function render () {
        $label = $this->config['label'];
        $content = $this->renderInternal();
        return  <<<RENDER
        <div class="form-group">
                <label for="{$this->id}" class="col-sm-2 control-label">$label</label>
                <div class="col-sm-10">
                $content
                </div>
            </div>
RENDER;

    }

    public abstract function renderInternal ();
}