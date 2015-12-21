<?php

namespace CrudKit\Core;


use Exception;
use League\Plates\Engine;
use League\Plates\Template\Template;

abstract class BaseCrudKitApp implements ICrudKitApp
{
    /**
     * Return a response or render directly to the output buffer.
     *
     * @return mixed
     */
    public function render()
    {
        $provider = $this->getProvider();
        try {
            $pageInfo = $provider->getPageInfo();
            $params = $provider->getRequestParams();

            if (isset($pageInfo['page'])) {
                $action = isset($pageInfo['action']) ? $pageInfo['action'] : 'index';

                /**
                 * @var $targetPage ICrudKitPage
                 */
                $targetPage = $this->pagesById[$pageInfo['page']];

                $result = $targetPage->handle($action, $params);

                switch ($result['type']) {
                    case 'raw':
                        return $provider->responseText($result['content']);
                    case 'template':
                        $text = $this->renderTemplate($result['template'], $result['data']);
                        return $provider->responseText($text);
                    case 'content':
                        $text = $this->renderTemplate('layouts/page', [
                            'content' => $result ['content']
                        ]);
                        return $provider->responseText($text);
                    case 'data':
                        return $provider->responseJson($result['data']);
                    case 'redirect':
                        return $provider->responseRedirect($result['url']);
                    default:
                        throw new Exception("Unknown response type");
                }
            }
            else {
                return $provider->responseRedirect($this->defaultUrl ());
            }
        }
        catch (Exception $e) {
            return $provider->responseException($e);
        }
    }

    public function renderTemplate ($path, $params) {
        $params ['staticRoot'] = 'crudkit/src/static';
        $templates = new Engine(__DIR__."/../../templates/");
        $templates->addData ([
            'staticRoot' => 'crudkit/src/static',
            'defaultUrl' => $this->defaultUrl (),
            'title' => $this->title,
            'pages' => $this->pages
        ]);
        $provider = $this->getProvider ();
        $templates->registerFunction('getPageUrl', function ($page) use ($provider) {
            return $provider->makeRoute ($page->getId (), 'index');
        });
        $template = new Template($templates, $path);
        return $template->render ($params);
    }

    /**
     * Add a page to the app.
     *
     * @param ICrudKitPage $page
     * @return mixed
     */
    public function addPage(ICrudKitPage $page)
    {
        $this->pages []= $page;
        $this->pagesById [$page->getId()] = $page;
        $page->init ($this);
    }

    /** 
     * Set the title of the app
     * @param string $title The Title
     */
    public function setTitle ($title) {
        $this->title = $title;
    }

    protected function defaultUrl () {
        /**
         * @var $firstPage ICrudKitPage
         */
        $firstPage = $this->pages[0];
        $url = $this->getProvider()->makeRoute($firstPage->getId (), 'index');

        return $url;
    }

    /**
     * @var array
     */
    protected $pages = [];

    /**
     * @var array
     */
    protected $pagesById = [];

    /**
     * The title of the app 
     * @var string
     */
    protected $title = "CrudKit";
}