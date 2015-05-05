<?php

namespace CrudKit\Data;


use CrudKit\Data\SQL\ForeignColumn;
use CrudKit\Data\SQL\PrimaryColumn;
use CrudKit\Data\SQL\SQLColumn;
use CrudKit\Data\SQL\ValueColumn;
use CrudKit\Util\FormHelper;
use CrudKit\Util\RouteGenerator;
use CrudKit\Util\UrlHelper;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Column;
use PDO;
use utilphp\util;

class SQLiteDataProvider extends BaseSQLDataProvider{
    public function init () {
        parent::init();

        $params = array(
            'driver' => 'pdo_sqlite',
            'path' => $this->path
        );
        $this->conn = DriverManager::getConnection($params);
    }

    /**
     *
     */
    protected function processColumns () {
        // Get a schema manager and get the list of columns
        $sm = $this->conn->getSchemaManager();
        $columns = $sm->listTableColumns($this->tableName);

        $type_lookup = array();

        /**
         * @var $col Column
         */
        foreach($columns as $col) {
            $type_lookup[$col->getName()] = $col;
        }

        foreach($this->colDefs as $item) {
            $id = $item['id'];
            $category = $item['category'];
            $opts = $item['options'];

            /**
             * @var $target SQLColumn
             */
            $target = null;

            switch($category) {
                case "value":
                    $target = new ValueColumn($id, $category, $opts);
                    break;
                case "foreign":
                    $target = new ForeignColumn($id, $category, $opts);
                    break;
                case "primary":
                    $target = new PrimaryColumn($id, $category, $opts);
                    break;
                default:
                    throw new \Exception("Unknown category for column $category");
            }


        }
    }

    protected function internalAddColumn ($id, $category, $options = array()) {
        $this->colDefs []= array(
            'id' => $id,
            'category' => $category,
            'options' => $options
        );
    }

    /**
     * Column definitions which are raw arrays and havne't been cast into
     * the appropriate SQLColumn
     *
     * @var array
     */
    protected $colDefs = array();

    /**
     * An array of SQL Columns
     *
     * @var array[SQLColumn]
     */
    protected $columns = array();


    /** @var Connection */
    protected $conn = null;

    /**
     * The path of the sqlite file to open.
     * @var string
     */
    protected $path = null;

    protected $tableName = null;

    public function setTable ($table) {
        $this->tableName = $table;
    }

    public function getData($params = array())
    {
        // TODO: Implement getData() method.
    }

    public function getSchema()
    {
        // TODO: Implement getSchema() method.
    }

    public function getRowCount()
    {
        // TODO: Implement getRowCount() method.
    }

    public function getSummaryColumns()
    {
        // TODO: Implement getSummaryColumns() method.
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
}