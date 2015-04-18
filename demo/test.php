<?php

use CrudKit\CrudKitApp;
use CrudKit\Pages\BasePage;

require "../vendor/autoload.php";

$crud = new CrudKitApp();
$crud->setStaticRoot("/src/static");

$page = new \CrudKit\Pages\DummyPage('dummy1');
$page->setName("A dummy");
$page->setContent("Dummy Content");

$crud->addPage($page);
$crud->render();
