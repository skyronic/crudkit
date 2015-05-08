<?php
namespace CrudKit\Form;


abstract class HorizontalItem extends BaseFormItem{
    public function render () {
        $label = $this->config['label'];
        $content = $this->renderInternal();
        return  <<<RENDER
        <div class="form-group">
                <label for="{$this->id}">$label</label>
                <div>
                $content
                </div>
            </div>
RENDER;

    }

    public function renderInline () {
        return $this->render();
    }

    public abstract function renderInternal ();
}