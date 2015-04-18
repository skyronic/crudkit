<?php

namespace CrudKit\Controllers;


class MainController extends BaseController {
    public function handle_view_page () {
        // Handle the view page action
        $pageId = $this->url->get('page');
        $page = $this->app->getPageById($pageId);

        return array(
            'template' => "main_page.twig",
            'data' => array (
                'page_content' => $page->render()
            )
        );
    }
}