<?php
namespace CrudKit\Pages;

//TODO: Add code documentation
interface Page
{
    /**
     * @return mixed
     */
    public function getId();


    public function init($app = null);

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function render();
}