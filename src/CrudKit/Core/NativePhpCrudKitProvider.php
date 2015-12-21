<?php

namespace CrudKit\Core;


use Exception;
use League\Url\UrlImmutable;

/**
 * Class NativePhpCrudKitProvider:
 *
 * A simple provider for CrudKit which works using as simple native php features
 * as possible without coupling with any framework or library
 *
 * @package CrudKit\Core
 */
class NativePhpCrudKitProvider implements ICrudKitProvider
{

    /**
     * Get a value from the session
     *
     * @param $key
     * @param string $default
     * @return mixed
     */
    public function sessionGet($key, $default = '')
    {
        if (isset ($_SESSION[$key]))
            return $_SESSION[$key];
        else
            return $default;
    }

    /**
     * Set a value inside the session
     *
     * @param $key
     * @param $value
     * @return mixed
     */
    public function sessionSet($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Check if the session has a given key
     *
     * @param $key
     * @return boolean
     */
    public function sessionHas($key)
    {
        return isset ($_SESSION[$key]);
    }

    /**
     * Get a list of all request parameters
     *
     * @return mixed
     */
    public function getRequestParams()
    {
        if(!is_null($this->params)) {
            return $this->params;
        }

        $result = [];
        $postdata = file_get_contents("php://input", 'rb');

        try {
            $json_post = json_decode($postdata, true);
            if (is_array($json_post)) {
                $result = array_merge ($result, $json_post);
            }
        }
        catch (Exception $e) {
            // Don't do anything this is what's expected if json serialization fails
        }

        $result = array_merge($result, $_GET);
        $result = array_merge($result, $_POST);

        $this->params = $result;

        return $result;
    }

    /**
     * Respond with this text
     *
     * @param $text
     * @return mixed
     */
    public function responseText($text)
    {
        echo $text;
        exit ();
    }

    /**
     * Respond with a json representation of this object
     *
     * @param $object
     * @return mixed
     */
    public function responseJson($object)
    {
        header ("Content-Type: text/json");
        echo json_encode($object);
        exit ();
    }

    /**
     * Respond with performing a redirect
     *
     * @param $url
     * @return mixed
     */
    public function responseRedirect($url)
    {
        header ("Location: ".$url);
        exit ();
    }

    /**
     * Respond with showing details of an exception
     * @param $e Exception
     * @return mixed
     */
    public function responseException($e)
    {
        http_response_code(500);
        echo "<h3>Exception:</h3>";
        echo "<pre>";
        echo $e->getMessage()."\n";
        echo $e->getTraceAsString()."\n";
        echo "</pre>";
        exit ();
    }

    /**
     * Register itself with the app
     *
     * @param ICrudKitApp $app
     * @return mixed
     */
    public function init(ICrudKitApp $app)
    {
        $this->app = $app;
    }

    /**
     * @var ICrudKitApp
     */
    protected $app;

    /**
     * Make a route to set the particular page and action
     *
     * @param $page
     * @param $action
     * @param array $params
     * @return mixed
     */
    public function makeRoute($page, $action, $params = array())
    {
        $params['action'] = $action;
        $params['page'] = $page;
        // TODO: remove dependency on UrlImmutable
        $url = UrlImmutable::createFromServer($_SERVER);
        return ''.$url->setQuery($params);
    }

    /**
     * Get the information on which page to render depending on the URL
     *
     * @return mixed
     */
    public function getPageInfo()
    {
        $params = $this->getRequestParams();
        return [
            'page' => isset($params['page']) ? $params['page'] : null,
            'action' => isset($params['action']) ? $params['action'] : null,
        ];
    }

    protected $params = null;
}