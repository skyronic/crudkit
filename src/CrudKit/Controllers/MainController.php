<?php

namespace CrudKit\Controllers;


class MainController extends BaseController {
    public function handle_default () {
        return "TODO";
    }
    public function handle_view_page () {
        // Handle the view page action
        $pageId = $this->url->get('page');
        $this->page = $this->app->getPageById($pageId);

        return $this->page->render();
    }

    public function handle_page_function () {
        $pageId = $this->url->get('page');
        $this->page = $this->app->getPageById($pageId);
        $func = $this->url->get("func");

        if(method_exists($this->page, "handle_".$func))
        {
            return call_user_func(array($this->page, "handle_".$func));
        }
        else {
            throw new \Exception("Unknown method");
        }
    }
}