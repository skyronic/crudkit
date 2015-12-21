<?php

namespace CrudKit\Core;

interface ICrudKitPage
{
    /**
     * Get the name of the page to display
     *
     * @return string
     */
    public function getName ();

    /**
     * Get a unique identifier for this page
     *
     * @return string
     */
    public function getId ();

    /**
     * Register the page with the app
     *
     * @param ICrudKitApp $app
     */
    public function init (ICrudKitApp $app);

    /**
     * Handle an action and return a response
     *
     * @param $action
     * @param $params
     * @return mixed
     */
    public function handle ($action, $params);
}