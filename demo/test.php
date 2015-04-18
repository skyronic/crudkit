<?php

use CrudKit\CrudKitApp;
use CrudKit\Pages\BasePage;

require "../vendor/autoload.php";

$crud = new CrudKitApp();
$crud->setStaticRoot("/src/static");

$crud->render();
