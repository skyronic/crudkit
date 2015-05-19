<?php
// Require CrudKit application
require "crudkit/crudkit.php";
use CrudKit\CrudKitApp;
use CrudKit\Pages\MySQLTablePage;

// Create a new CrudKitApp object
$app = new CrudKitApp ();

$page = new MySQLTablePage("mysql1", "user2","hunter2", "Chinook", array('charset' => "UTF8"));
$page->setName("Customer Management")
    ->setTableName("Customer")
    ->setPrimaryColumn("CustomerId")
    ->addColumn("FirstName", "First Name")
    ->addColumn("LastName", "Last Name")
    ->addColumn("City", "City")
    ->addColumn("Country", "Country")
    ->addColumn("Email", "E-mail")
    ->setSummaryColumns(array("FirstName", "Country"));
$app->addPage($page);

// Render the app. This will display the HTML
$app->render ();

?>