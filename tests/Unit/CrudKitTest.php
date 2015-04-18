<?php
namespace CrudKitTests\Unit;

use CrudKit\CrudKitApp;
use CrudKit\Pages\BasePage;

class CrudKitTest extends \PHPUnit_Framework_TestCase
{
    public function testHelloWorld()
    {
    	$crud = new CrudKitApp();
    	$crud->addPage(new BasePage());

    	$this->assertEquals($crud->say(), "Hello World");
    }
}
