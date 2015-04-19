<?php

namespace CrudKit\Controllers;

use CrudKit\CrudKitApp;
use CrudKit\Pages\BasePage;
use CrudKit\Util\TwigUtil;
use CrudKit\Util\UrlHelper;

class BaseController {

    /**
     * @var UrlHelper
     */
    protected $url = null;

    /**
     * @var CrudKitApp
     */
    protected $app = null;

    /**
     * @param $app CrudKitApp
     */
    public function __construct ($app) {
        $this->app = $app;
    }

    public function handle () {
        if($this->url === null) {
            $this->url = new UrlHelper();
        }

        $action = $this->url->get('action');
        if($action !== null && method_exists($this, "handle_".$action)) {
            $result = call_user_func(array($this, "handle_". $action));
            if($result['type'] === "template") {
                return $this->renderTemplate($result['template'], $result['data']);
            }
            else if($result['type'] === "json") {
                $this->app->setJsonResponse(true);
                return json_encode($result['data']);
            }
            else {
                throw new \Exception("Unknown result type");
            }
        }
        else  {
            return $this->default_page ();
        }
    }

    public function renderTemplate ($templateName, $data = array()) {
        $pageMap = [];
        /** @var BasePage $pageItem */
        foreach($this->app->getPages() as $pageItem) {
            $pageMap []= array(
                'id' => $pageItem->getId(),
                'name' => $pageItem->getName()
            );
        }

        $twig = new TwigUtil();
        $template_data = array_merge(array(
            'staticRoot' => $this->app->getStaticRoot(),
            'pageMap' => $pageMap
        ), $data);

        return $twig->renderTemplateToString($templateName, $template_data);
    }

    public function default_page () {
        return $this->renderTemplate("layout.twig", array());
    }
}