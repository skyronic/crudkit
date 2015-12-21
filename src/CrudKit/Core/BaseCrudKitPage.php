<?php

namespace CrudKit\Core;


class BaseCrudKitPage implements ICrudKitPage
{

    /**
     * Get the name of the page to display
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function setName ($name) {
        $this->name = $name;
    }

    /**
     * Get a unique identifier for this page
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Register the page with the app
     *
     * @param ICrudKitApp $app
     */
    public function init(ICrudKitApp $app)
    {
        $this->app = $app;
        $this->provider = $app->getProvider();
    }

    /**
     * Handle an action and return a response
     *
     * @param $action
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function handle($action, $params)
    {
        if(method_exists($this, "__handle_".$action)) {
            $result = call_user_func(array($this, "__handle_". $action), $params);
        }
        else {
            throw new \Exception ("Unknown action $action");
        }

        return $result;
    }

    public function __construct ($id) {
        $this->id = $id;
    }

    /**
     * Name of the page
     * @var string
     */
    protected $name = 'Untitled Page';

    /**
     * ID of the page
     * @var
     */
    protected $id;

    /**
     * The app
     * @var ICrudKitApp
     */
    protected $app;

    /**
     * The provider
     *
     * @var ICrudKitProvider
     */
    protected $provider;
}