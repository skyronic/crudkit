<?php

namespace CrudKit\Pages;

use CrudKit\Core\BaseCrudKitPage;

class SqlitePage extends BaseCrudKitPage
{
    public function __handle_index () {
        return [
            'type' => 'template',
            'template' => 'pages/data/view',
            'data' => [
                'page_id' => $this->getId (),
                'page_name' => $this->getName ()
            ]
        ];
    }

    public function __construct($id, $path)
    {
        parent::__construct($id);
    }
}