<?php
// Require CrudKit application
require "crudkit/crudkit.php";
use CrudKit\CrudKitApp;
use CrudKit\Pages\MySQLTablePage;

// Create a new CrudKitApp object
$app = new CrudKitApp ();

$invoice = new MySQLTablePage("mysql2", "user2","hunter2", "Chinook", array('charset' => "UTF8"));
$invoice->setName("Invoice")
    ->setPrimaryColumnWithId("a0", "InvoiceId")
    ->setTableName("Invoice")
    ->addColumnWithId("a1", "BillingCity", "City")
    ->addColumnWithId("a2", "BillingCountry", "Country")
    ->addColumnWithId("a3", "Total", "Total")
    ->addColumnWithId("a4", "InvoiceDate", "Date", array(
    ))
    ->setSummaryColumns(["a1", "a2", "a3", "a4"]);
$app->addPage($invoice);

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