<?php
// Require CrudKit application
require "crudkit/crudkit.php";
use CrudKit\CrudKitApp;
use CrudKit\Pages\SQLiteTablePage;

// Create a new CrudKitApp object
$app = new CrudKitApp ();

$page = new SQLiteTablePage ("sqlite1", "fixtures/chinook.sqlite");
$page->setName("Customer Management")
    ->setTableName("Customer")
    ->setPrimaryColumnWithId("c1", "CustomerId")
    ->addColumnWithId("c2", "FirstName", "First Name")
    ->addColumnWithId("c3", "LastName", "Last Name")
    ->addColumnWithId("c4", "City", "City")
    ->addColumnWithId("c5", "Country", "Country")
    ->addColumnWithId("c6", "Email", "E-mail")
    ->setSummaryColumns(array("c1", "c5"));
$app->addPage($page);

// Render the app. This will display the HTML
$app->render ();

?>