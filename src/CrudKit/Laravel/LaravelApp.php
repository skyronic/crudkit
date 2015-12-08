<?php

namespace CrudKit\Laravel;

use CrudKit\CrudKitApp;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;

class LaravelApp extends CrudKitApp {
	public function makeResponse () {
        $content = $this->renderToString();

        if ($this->redirect !== null) {
            $url = ''.$this->redirect;
        	return new RedirectResponse ($url);
        }
        $response = new Response ();
        // Headers are also calculated in render to string
        if($this->isJsonResponse()) {
            $response->header("Content-type", "application/json;");
        }
        $response->setContent ($content);

        return $response;
	}

	public function __construct () {
		// configure to laravel default
		$this->setStaticRoot ("/vendor/crudkit/");
	}
}