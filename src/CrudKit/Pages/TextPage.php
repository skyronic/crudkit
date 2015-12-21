<?php

namespace CrudKit\Pages;

use CrudKit\Core\BaseCrudKitPage;

class TextPage extends BaseCrudKitPage
{
    public function __handle_index () {
        return [
            'type' => 'content',
            'content' => $this->text
        ];
    }

    public function setText ($text) {
        $this->text = $text;
    }

    protected $text;

}