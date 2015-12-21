<?php
namespace CrudKit;

use CrudKit\Core\BaseCrudKitApp;
use CrudKit\Core\ICrudKitProvider;
use CrudKit\Core\NativePhpCrudKitProvider;

class CrudKitApp extends BaseCrudKitApp
{
    public function __construct () {
        $this->provider = new NativePhpCrudKitProvider();
        $this->provider->init ($this);
    }

    /**
     * Get the provider for this app
     * @return mixed
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @var ICrudKitProvider
     */
    protected $provider;
}