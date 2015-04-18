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
        return $this->urlHelper->resetGetParams(array('page' => $pageId));
    }

}