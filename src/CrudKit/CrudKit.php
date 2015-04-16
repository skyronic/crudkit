<?php

namespace CrudKit;

use CrudKit\Pages\BasePage;

class CrudKit {
    /**
     * @var BasePage
     */
    protected $page;
    public function addPage(BasePage $page) {
        $this->page = $page;
    }

    public function say () {
        $result = "Hello ".$this->page->saySomething ();

        return $result;
    }
}