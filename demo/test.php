<?php

use CrudKit\CrudKit;
use CrudKit\Pages\BasePage;

require "../vendor/autoload.php";

$crud = new CrudKit();
$crud->setStaticRoot("/src/static/");

$crud->render();
