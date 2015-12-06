<?php
// Require CrudKit application
require "crudkit/crudkit.php";
use CrudKit\CrudKitApp;
use CrudKit\Pages\SQLiteTablePage;
use CrudKit\Pages\BasicLoginPage;

// Create a new CrudKitApp object
$app = new CrudKitApp ();
$app->setAppName ("Admin Panel");

$login = new BasicLoginPage ();
if ($login->userTriedLogin ()) {
    $username = $login->getUserName ();
    $password = $login->getPassword ();

    // TODO: you should use a better way to validate the password. Even if
    if ($username === 'admin' && $password === 'demo') {
        $login->success ();
    }
    else if ($username === 'user' && $password === 'demo') {
        $app->setReadOnly (true);
        $login->success ();
    }
    else {
        $login->fail ("Please check your password");
    }
}
$app->useLogin ($login);

$page = new SQLiteTablePage ("sqlite2", "fixtures/chinook.sqlite");
$page->setName("Customer Management")
    ->setTableName("Customer")
    ->setRowsPerPage (20)
    ->setPrimaryColumn("CustomerId")
    ->addColumn("FirstName", "First Name", array(
        'required' => true
    ))
    ->addColumn("LastName", "Last Name")
    ->addColumn("City", "City", array(
        'required' => true
    ))
    ->addColumn("Country", "Country")
    ->addColumn("Email", "E-mail")
    ->setSummaryColumns(array("FirstName", "Country"));
$app->addPage($page);

$invoice = new SQLiteTablePage ("sqlite1", "fixtures/chinook.sqlite");
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

// Render the app. This will display the HTML
$app->render ();
