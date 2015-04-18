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

$page2 = new \CrudKit\Pages\DummyPage('dummy2');
$page2->setName("A dummy 2");
$page2->setContent("Dummy Content 2");
$crud->addPage($page2);

$crud->render();
