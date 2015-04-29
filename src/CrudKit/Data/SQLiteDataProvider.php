<?php

namespace CrudKit\Data;


use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

class SQLiteDataProvider extends BaseSQLDataProvider{

    public function getData($params = array())
    {
        $skip = isset($params['skip']) ? $params['skip'] : 0;
        $take = isset($params['take']) ? $params['take'] : 10;

        $builder = $this->conn->createQueryBuilder();
        $exec = $builder->select(array("CustomerId", "FirstName", "LastName"))
            ->from("Customer")
            ->setFirstResult($skip)
            ->setMaxResults($take)
            ->execute();

        return $exec->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getSchema()
    {
        return array(
            'CustomerId' => array (
                'type' => "primary"
            ),
            'FirstName' => array(
                'type' => 'text'
            ),
            'LastName' => array (
                'type' => 'text'
            )
        );
    }

    public function getRowCount()
    {
        return 100;
    }

    public function getSummaryColumns()
    {
        return array(
            array(
                'key' => 'FirstName',
                'name' => "First Name",
                'renderType' => 'primaryLink',
                'primaryColumn' => 'CustomerId'
            ),
            array(
                'key' => 'LastName',
                'name' => "Last Name",
                'renderType' => 'text'
            )
        );
    }

    public function getEditFormOrder()
    {
        // TODO: Implement getEditFormOrder() method.
    }

    public function getRow($id = null)
    {
        // TODO: Implement getRow() method.
    }

    public function setRow($id = null, $values = array())
    {
        // TODO: Implement setRow() method.
    }

    public function getEditFormConfig()
    {
        // TODO: Implement getEditFormConfig() method.
    }

    /**
     * @var Connection
     */
    protected $conn = null;

    public function init ($url) {
        $params = array(
            'driver' => 'pdo_sqlite',
            'path' => $url
        );
        $this->conn = DriverManager::getConnection($params);
    }
}