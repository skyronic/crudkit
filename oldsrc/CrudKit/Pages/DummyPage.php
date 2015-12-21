<?php

namespace CrudKit\Pages;

use CrudKit\Util\TwigUtil;


class DummyPage extends BasePage {

    protected $content = "";
    function render()
    {
        $twig = new TwigUtil();
        return $twig->renderTemplateToString("pages/dummy.twig", array(
            'name' => $this->name,
            'content' => $this->content
        ));
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }
}