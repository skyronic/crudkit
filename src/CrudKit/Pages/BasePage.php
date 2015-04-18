<?php

namespace CrudKit\Pages;

abstract class BasePage {
    abstract function render ();

    protected $name = "";
    protected $id = null;

    public function setName ($name) {
        $this->name = $name;
    }
    public function getName () {
        return $this->name;
    }

    public function __construct ($id) {
        if(!isset($id)) {
            throw new \Exception("Need to set an id for the page");
        }
        $this->id = $id;
    }

    /**
     * @return null
     */
    public function getId()
    {
        return $this->id;
    }

}