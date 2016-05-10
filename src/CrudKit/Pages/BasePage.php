<?php

namespace CrudKit\Pages;

abstract class BasePage {
    abstract function render ();

    protected $name = "";
    protected $id = null;

    /**
     * @param $name
     * @return $this
     */
    public function setName ($name) {
        $this->name = $name;
        return $this;
    }
    public function getName () {
        return $this->name;
    }

    public function init () {
        
    }

    /**
     * @return null
     */
    public function getId()
    {
        return $this->id;
    }

    protected function setId ($id) {
        $this->id = $id;
    }

}