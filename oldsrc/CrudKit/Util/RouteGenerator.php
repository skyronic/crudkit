<?php

namespace CrudKit\Util;


class RouteGenerator {
    /**
     * @var UrlHelper
     */
    protected $urlHelper = null;
    public function __construct() {
        $this->urlHelper = new UrlHelper();
    }

    public function openPage($pageId) {
        return $this->urlHelper->resetGetParams(array('page' => $pageId, 'action' => 'view_page'));
    }

    public function pageFunc ($pageId, $func) {
        return $this->urlHelper->resetGetParams(array('page' => $pageId, 'action' => 'page_function', 'func' => $func));
    }

    public function itemFunc ($pageId, $rowId, $func) {
        return $this->urlHelper->resetGetParams(array('page' => $pageId, 'action' => 'page_function', 'func' => $func, 'item_id' => $rowId));
    }

    public function newItem ($pageId) {
        return $this->urlHelper->resetGetParams(array('page' => $pageId, 'action' => 'page_function', 'func' => "new_item"));
    }

    public function root () {
        return $this->urlHelper->resetGetParams();
    }

    public function defaultRoute () {
        return $this->urlHelper->resetGetParams(array());
    }

}