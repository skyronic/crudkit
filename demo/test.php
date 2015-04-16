<?php

use CrudKit\CrudKit;
use CrudKit\Pages\BasePage;

require "../vendor/autoload.php";

$crud = new CrudKit();
$crud->addPage(new BasePage());

echo $crud->say();
