<?php
namespace CrudKitTests\Unit;

use CrudKit\CrudKit;
use CrudKit\Pages\BasePage;

class CrudKitTest extends \PHPUnit_Framework_TestCase
{
    public function testHelloWorld()
    {
    	$crud = new CrudKit();
    	$crud->addPage(new BasePage());

    	$this->assertEquals($crud->say(), "Hello World");
    }
}
