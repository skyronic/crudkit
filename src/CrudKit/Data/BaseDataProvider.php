<?php

namespace CrudKit\Data;

use CrudKit\Pages\Page;
use CrudKit\Util\FormHelper;
use CrudKit\Util\RouteGenerator;
use CrudKit\Util\UrlHelper;

abstract class BaseDataProvider implements DataProvider
{
    protected $initQueue = array();

    /**
     * @var Page
     */
    protected $page = null;

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

    public function setPage(Page $page)
    {
        $this->page = $page;
    }
}