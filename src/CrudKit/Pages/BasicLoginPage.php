<?php
namespace CrudKit\Pages;

use CrudKit\Util\RouteGenerator;
use CrudKit\Util\TwigUtil;
use CrudKit\Util\UrlHelper;
use CrudKit\Util\ValueBag;

class BasicLoginPage extends BasePage {
	protected $url;
	public function __construct () {
		$this->setId ("__ck_basic_login");
		$this->url = new UrlHelper ();
		$this->route = new RouteGenerator();
	}

	public function hasInput () {

	}

	public function getUserName () {
		return $this->url->get ('username');
	}

	public function getPassword () {
		return $this->url->get ('username');
	}

	// TODO: Refactor this into a session helper
	public function check () {
		return isset($_SESSION['__ck_logged_in']) && $_SESSION['__ck_logged_in'] === true;
	}

	public function doLogin ($username = null) {
		$_SESSION['__ck_logged_in'] = true;
		$_SESSION['__ck_username'] = $username;
	}

    function render()
    {
        return array (
        	'type' => 'template',
        	'template' => 'pages/login.twig',
        	'data' => [
	        	'staticRoot' => $this->app->getStaticRoot(),
	        	'title' => $this->app->getAppName (),
	        	'endpoint' => $this->route->root (),
	        	'valueBag' => json_encode(ValueBag::getValues()),
	        	'bodyclass' => 'login-page'
        	]
    	);
    }
}