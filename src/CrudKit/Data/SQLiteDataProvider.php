<?php

namespace CrudKit\Data;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use PDO;

class SQLiteDataProvider extends BaseSQLDataProvider{

    /**
     * @param $id String A unique string id (exposed to client)
     * @param $expr String The Expression/Column Name
     * @param $name String Name of the column (Used on forms)
     * @param array $options array of options
     */
    public function addColumn ($id, $expr, $name, $options = array()) {
        $this->columns []= array(
            'id' => $id,
            'expr' => $expr,
            'name' => $name,
            'options' => $options
        );
    }

    public function setTable ($table) {
        $this->tableName = $table;
    }

    protected $url;

    public function __construct($url) {
        $this->url = $url;
    }

    protected function transformColumns () {

    }

    protected $columns = array();
    protected $tableName = null;

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
        return array('CustomerId', 'FirstName', 'LastName');
    }

    public function getRow($id = null)
    {
        $builder = $this->conn->createQueryBuilder();
        $exec = $builder->select(array("CustomerId", "FirstName", "LastName"))
            ->from("Customer")
            ->where("CustomerId = ".$builder->createNamedParameter($id))
            ->execute();

        return $exec->fetch(PDO::FETCH_ASSOC);
    }

    public function setRow($id = null, $values = array())
    {
        $builder = $this->conn->createQueryBuilder();
        $exec = $builder->update("Customer")
            ->set('FirstName', $builder->createNamedParameter($values['FirstName']))
            ->set('LastName', $builder->createNamedParameter($values['LastName']))
            ->where("CustomerId = ".$builder->createNamedParameter($id))
            ->execute();

//        $exec->execute();

        return true;

    }

    public function getEditFormConfig()
    {
        return array(
            'CustomerId' => array(
                'label' => "Customer Id",
                'type' => 'text',
                'validation' => 'required'
            ),
            'FirstName' => array (
                'label' => "First Name",
                'type' => 'text',
                'validation' => 'required'
            ),
            'LastName' => array (
                'label' => "Last Name",
                'type' => 'text',
                'validation' => 'required'
            ),
        );
    }

    /**
     * @var Connection
     */
    protected $conn = null;

    public function init () {
        parent::init();
        $params = array(
            'driver' => 'pdo_sqlite',
            'path' => $this->url
        );
        $this->conn = DriverManager::getConnection($params);
    }
}