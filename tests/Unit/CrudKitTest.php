<?php
namespace CrudKitTests\Unit;

use CrudKitTests\SqlDataProviderFactory;
use Doctrine\DBAL\Connection;

abstract class CrudKitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Connection
     */
    private $connection;

    public function setUp()
    {
        parent::setUp();
        $this->connection = SqlDataProviderFactory::connection();
        $this->connection->beginTransaction();
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->connection->rollBack();
    }
}