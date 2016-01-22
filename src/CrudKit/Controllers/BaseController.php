<?php

namespace CrudKit\Controllers;

use CrudKit\CrudKitApp;
use CrudKit\Pages\Page;
use CrudKit\Util\FlashBag;
use CrudKit\Util\TwigUtil;
use CrudKit\Util\ValueBag;
use CrudKit\Util\RouteGenerator;
use CrudKit\Util\UrlHelper;
use Exception;

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
     * @var TwigUtil
     */
    protected $twig = null;

    /**
     * @var Page
     */
    protected $page = null;

    /**
     * Route generator
     * @var RouteGenerator
     */
    protected $routeGen = null;

    /**
     * @param $app CrudKitApp
     */
    public function __construct ($app) {
        $this->app = $app;
        $this->url = new UrlHelper();
        $this->routeGen = new RouteGenerator();
        $this->twig = new TwigUtil();
    }

    public function handle () {
        $action = $this->url->get("action", "default");

        $whoops = new \Whoops\Run();
        if($this->url->get("ajax", false)) {
            $whoops->pushHandler (new \Whoops\Handler\JsonResponseHandler());
        }
        else {
            $whoops->pushHandler (new \Whoops\Handler\PrettyPageHandler());
        }
        $whoops->register();
        $result = null;
        if(method_exists($this, "handle_".$action)) {
            $result = call_user_func(array($this, "handle_". $action));
        }
        else {
            throw new Exception ("Unknown action");
        }
        $output = "";

        if(is_string($result)) {
            $newResult = array(
                'type' => 'transclude',
                'content' => $result
            );

            $result = $newResult;
        }

        switch($result['type']) {
            case "template":
                $output = $this->twig->renderTemplateToString($result['template'], $result['data']);
                break;
            case "json":
                $this->app->setJsonResponse(true);
                $data = $result['data'];
                $data['flashbag'] = FlashBag::getFlashes();
                $output = json_encode($data);
                break;
            case "redirect":
                $this->app->_requestRedirect ($result['url']);
                return;
                break;
            case "transclude":
                $pageMap = [];
                /** @var Page $pageItem */
                foreach($this->app->getPages() as $pageItem) {
                    $pageMap []= array(
                        'id' => $pageItem->getId(),
                        'name' => $pageItem->getName()
                    );
                }
                ValueBag::set("flashbag", FlashBag::getFlashes());
                $data = array(
                    'valueBag' => json_encode(ValueBag::getValues()),
                    'staticRoot' => $this->app->getStaticRoot(),
                    'pageMap' => $pageMap,
                    'defaultUrl' => $this->routeGen->defaultRoute(),
                    'title' => $this->app->getAppName (),
                    'userParams' => $this->app->getUserParams (),
                    'pageTitle' => ''
                );
                if($this->page !== null) {
                    $data['page'] = $this->page;
                    $data['currentId'] = $this->page->getId();
                    $data['pageTitle'] = $this->page->getName();
                }
                else {
                    $data['currentId'] = -1;
                }
                $data['page_content'] = $result['content'];
                $data['dev'] = false; // change to true to load unminified js

                $output = $this->twig->renderTemplateToString("main_page.twig", $data);
                break;
            default:
                throw new Exception ("Unknown result type");
        }

        return $output;

    }
}