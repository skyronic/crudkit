<?php

// Note: this prevents usage in MVC frameworks, which will be supported soon
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__."/vendor/autoload.php";