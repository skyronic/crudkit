<?php

namespace CrudKit\Core;

interface ICrudKitProvider
{
    /**
     * Get a value from the session
     *
     * @param $key
     * @param string $default
     * @return mixed
     */
    public function sessionGet ($key, $default = '');

    /**
     * Set a value inside the session
     *
     * @param $key
     * @param $value
     * @return mixed
     */
    public function sessionSet ($key, $value);

    /**
     * Check if the session has a given key
     *
     * @param $key
     * @return boolean
     */
    public function sessionHas ($key);

    /**
     * Get a list of all request parameters
     *
     * @return mixed
     */
    public function getRequestParams ();

    /**
     * Respond with this text
     *
     * @param $text
     * @return mixed
     */
    public function responseText ($text);

    /**
     * Respond with a json representation of this object
     *
     * @param $object
     * @return mixed
     */
    public function responseJson ($object);

    /**
     * Respond with performing a redirect
     *
     * @param $url
     * @return mixed
     */
    public function responseRedirect ($url);

    /**
     * Respond with showing details of an exception
     * @param $e
     * @return mixed
     */
    public function responseException ($e);

    /**
     * Make a route to set the particular page and action
     *
     * @param $page
     * @param $action
     * @param array $params
     * @return mixed
     */
    public function makeRoute ($page, $action, $params = array());

    /**
     * Get the information on which page to render depending on the URL
     *
     * @return mixed
     */
    public function getPageInfo ();

    /**
     * Register itself with the app
     *
     * @param ICrudKitApp $app
     * @return mixed
     */
    public function init (ICrudKitApp $app);
}