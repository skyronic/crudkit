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