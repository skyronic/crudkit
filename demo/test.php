<?php

use CrudKit\CrudKitApp;
use CrudKit\Data\DummyDataProvider;
use CrudKit\Data\SQLiteDataProvider;
use CrudKit\Pages\BasePage;
use CrudKit\Pages\BasicDataPage;

require "../vendor/autoload.php";

$crud = new CrudKitApp();
$crud->setStaticRoot("/src/static");


$page4 = new BasicDataPage('dummy4');
$page4->setName("SQLITE PAGE");
$sqliteProvider = new SQLiteDataProvider("fixtures/chinook.sqlite");
$sqliteProvider->setTable("Customer");
$sqliteProvider->setPrimaryColumn("a0", "CustomerId");
$sqliteProvider->addColumn("a1", "FirstName", "First Name");
$sqliteProvider->addColumn("a2", "LastName", "Last Name");
$sqliteProvider->addColumn("a3", "City", "City");
$sqliteProvider->addColumn("a4", "Country", "Country");
$sqliteProvider->addColumn("a5", "Email", "Email");
$sqliteProvider->setSummaryColumns(array("a1", "a2"));
$sqliteProvider->manyToOne("a6", "SupportRepId", "Employee", "EmployeeId", "FirstName", "Support Rep");
$page4->setDataProvider($sqliteProvider);
$crud->addPage($page4);

$page5 = new BasicDataPage('dummy5');
$page5->setName("Employees");

$empProvider = new SQLiteDataProvider("fixtures/chinook.sqlite");
$empProvider->setTable("Employee");
$empProvider->setPrimaryColumn("b0", "EmployeeId");
$empProvider->addColumn("b1", "FirstName", "First Name");
$empProvider->addColumn("b2", "LastName", "Last Name");
$empProvider->manyToOne("b3", "ReportsTo", "Employee", "EmployeeId", "FirstName", "Reports To");
$empProvider->oneToMany ("b4", $sqliteProvider, "SupportRepId", "b0", "Customers");
$empProvider->setSummaryColumns(array("b1", "b2"));

$page5->setDataProvider($empProvider);
$crud->addPage($page5);


$invProvider = new SQLiteDataProvider("fixtures/chinook.sqlite");
$invProvider->setTable("Invoice");
$invProvider->setPrimaryColumn("b0", "InvoiceId");
$invProvider->addColumn("b1", "BillingCountry", "Country123");
$invProvider->addColumn("b2", "Total", "Total123");
$invProvider->addColumn("b3", "InvoiceDate", "InvoiceDate123");
$invProvider->setSummaryColumns(array("b1", "b2", "b3"));

$page6 = new BasicDataPage('dummy6');
$page6->setName ("Custom Data Types");
$page6->setDataProvider($invProvider);
$crud->addPage($page6);




$crud->render();
