<?php

namespace CrudKit\Data;

use CrudKit\Pages\BasePage;
use CrudKit\Util\FormHelper;
use CrudKit\Util\RouteGenerator;
use CrudKit\Util\UrlHelper;

abstract class BaseDataProvider implements DataProvider
{
    protected $initQueue = array();

    /**
     * @var BasePage
     */
    protected $page = null;

    public function setPage ($page) {
        $this->page = $page;
    }
    public function init ()
    {
        foreach($this->initQueue as $item) {
            $item->init();
        }
    }

    public function getEditForm ()
    {
        return new FormHelper([], $this->getEditFormConfig());
    }

}