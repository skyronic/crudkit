<?php

namespace CrudKit\Controllers;

use CrudKit\Util\FlashBag;
use CrudKit\Util\ValueBag;

class MainController extends BaseController {
    public function handle_default () {
        $firstPage = $this->app->getDefaultPage ();
        if($firstPage !== null) {
            return array(
                'type' => 'redirect',
                'url' => $this->routeGen->openPage ($firstPage->getId())
            );
        }
        else {
            return "";
        }
    }
    public function handle_view_page () {
        // Handle the view page action
        $pageId = $this->url->get('page');
        $this->page = $this->app->getPageById($pageId);
        $this->page->init($this->app);
        ValueBag::set("pageId", $this->page->getId());

        return $this->page->render();
    }

    public function handle_page_function () {
        $pageId = $this->url->get('page');
        $this->page = $this->app->getPageById($pageId);
        $this->page->init($this->app);
        $func = $this->url->get("func");
        ValueBag::set("pageId", $this->page->getId());

        if(method_exists($this->page, "handle_".$func))
        {
            return call_user_func(array($this->page, "handle_".$func));
        }
        else {
            throw new \Exception("Unknown method");
        }
    }
}