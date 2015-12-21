<?php

namespace CrudKit\Core;

interface ICrudKitApp
{
    /**
     * Get the provider for this app
     * @return mixed
     */
    public function getProvider ();

    /**
     * Return a response or render directly to the output buffer.
     *
     * @return mixed
     */
    public function render ();

    /**
     * Add a page to the app.
     *
     * @param ICrudKitPage $page
     * @return mixed
     */
    public function addPage (ICrudKitPage $page);
}