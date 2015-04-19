<?php

namespace CrudKit\Controllers;


class MainController extends BaseController {
    public function handle_view_page () {
        // Handle the view page action
        $pageId = $this->url->get('page');
        $page = $this->app->getPageById($pageId);

        return array(
            'type' => 'template',
            'template' => "main_page.twig",
            'data' => array (
                'page_content' => $page->render()
            )
        );
    }

    public function handle_page_function () {
        $pageId = $this->url->get('page');
        $page = $this->app->getPageById($pageId);
        $func = $this->url->get("func");
        if(method_exists($page, "handle_".$func))
        {
            return call_user_func(array($page, "handle_".$func));
        }
        else {
            throw new \Exception("Unknown method");
        }
    }
}