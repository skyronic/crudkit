<?php
// Require CrudKit application
require "crudkit/crudkit.php";
use CrudKit\CrudKitApp;
use CrudKit\Pages\HtmlPage;

// Create a new CrudKitApp object
$app = new CrudKitApp ();

// Create a new Page, of type "HtmlPage" which allows you to display arbitrary HTML inside 
$page = new HtmlPage("mypage1"); // Every page needs to have a unique ID
$page->setName ("My First Page"); // setName is available for all pages.
$page->setInnerHtml ("This is page # <b>1</b>"); // You can set the HTML of this page, a feature supported by HtmlPage
$app->addPage ($page);

$page2 = new HtmlPage("mypage2");
$page2->setName ("My Second Page");
$page2->setInnerHtml ("This is page # <b>2</b>");
$app->addPage ($page2);

// Render the app. This will display the HTML
$app->render ();

?>