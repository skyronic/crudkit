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

    public function addColumn ($expr, $label, $options = array()) {
        $options['label'] = $label;
        $options['expr'] = $expr;

        $this->internalAddColumn(SQLColumn::CATEGORY_VALUE, $expr, $options);
    }

    public function setPrimaryColumn ($expr) {
        $this->internalAddColumn(SQLColumn::CATEGORY_PRIMARY, $expr, array(
            'name' => "Primary",
            'expr' => $expr
        ));
    }

    public function manyToOne ($foreignKey, $extTable, $primary, $nameColumn, $label) {
        $this->internalAddColumn(SQLColumn::CATEGORY_FOREIGN, $foreignKey, array(
            'fr_type' => 'manyToOne',
            'fr_table' => $extTable,
            'fk_primary' => $primary,
            'fk_name_col' => $nameColumn,
            'label' => $label
        ));
    }

    public function init () {
        parent::init();

        $params = array(
            'driver' => 'pdo_sqlite',
            'path' => $this->path
        );
        $this->conn = DriverManager::getConnection($params);
    }

    /**
     * @param array $summary_cols
     */
    public function setSummaryColumns($summary_cols)
    {
        $this->summary_cols = $summary_cols;
    }

    /**
     * Converts columns from raw objects to more powerful cool objects
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

            $target->doctrineColumnLookup($type_lookup);
            $target->init ();

            $this->columns[$id] = $target;
        }
    }

    /**
     * Super cool and useful function to query columns and get a reduced
     * subset
     *
     * @param $queryType
     * @param $queryValues
     * @param $valueType
     * @return array
     */
    protected function queryColumns ($queryType, $queryValues, $valueType) {
        $target_columns = array();

        if($queryType === "col_list") {
            // The caller has already specified a list of columns
            $target_columns = $queryValues;
        }
        else {
            /**
             * @var  $key
             * @var SQLColumn $col
             */
            foreach($this->columns as $key => $col) {
                if($queryType === "category") {
                    if(in_array($col->category, $queryValues)) {
                        $target_columns []= $key;
                    }
                }
            }
        }

        $results = array();

        foreach($target_columns as $colKey) {
            /**
             * @var $column SQLColumn
             */
            $column = $this->columns[$colKey];
            switch($valueType) {
                case "id":
                    $results []= $colKey;
                    break;
                case "expr":
                    $results []= $column->getExpr();
                    break;
                case "object":
                    $results []= $column;
                    break;
            }
        }

        return $results;
    }

    protected function internalAddColumn ($id, $category, $options = array()) {
        $this->colDefs []= array(
            'id' => $id,
            'category' => $category,
            'options' => $options
        );
    }

    /**
     * @param $table string
     */
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

    /**
     * Name of the table
     *
     * @var string
     */
    protected $tableName = null;

    /**
     * A list of summary columns
     *
     * @var array
     */
    protected $summary_cols = array();
}