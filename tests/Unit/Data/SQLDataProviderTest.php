<?php
namespace CrudKitTests\Unit\Data;

use CrudKitTests\Unit\CrudKitTest;
use CrudKitTests\SqlDataProviderFactory;

class SQLDataProviderTest extends CrudKitTest
{
    /**
     * @var \Doctrine\DBAL\Connection
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
