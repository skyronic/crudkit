<?php

use CrudKit\CrudKitApp;
use CrudKit\Data\DummyDataProvider;
use CrudKit\Data\SQLiteDataProvider;
use CrudKit\Pages\BasePage;
use CrudKit\Pages\BasicDataPage;

require "../vendor/autoload.php";

$crud = new CrudKitApp();
$crud->setStaticRoot("/src/static");

$page = new \CrudKit\Pages\DummyPage('dummy1');
$page->setName("A dummy");
$page->setContent("Dummy Content");
$crud->addPage($page);

$page2 = new \CrudKit\Pages\DummyPage('dummy2');
$page2->setName("A dummy 2");
$page2->setContent("Dummy Content 2");
$crud->addPage($page2);

$page3 = new BasicDataPage('dummy3');
$page3->setName("Data Dummy");
$dummyProvider = new DummyDataProvider();
$page3->setDataProvider($dummyProvider);
$crud->addPage($page3);


$page4 = new BasicDataPage('dummy4');
$page4->setName("SQLITE PAGE");
$sqliteProvider = new SQLiteDataProvider("fixtures/chinook.sqlite");
$sqliteProvider->setTable("Customer");
$sqliteProvider->setPrimaryColumn("CustomerId");
$sqliteProvider->addColumn("FirstName", "First Name");
$sqliteProvider->addColumn("LastName", "Last Name");
$sqliteProvider->addColumn("City", "City");
$sqliteProvider->addColumn("Country", "Country");
$sqliteProvider->setSummaryColumns(array("FirstName", "City"));
$sqliteProvider->manyToOne("SupportRepId", "Employee", "EmployeeId", "FirstName", "Support Rep");

$page4->setDataProvider($sqliteProvider);
$crud->addPage($page4);

$crud->render();
