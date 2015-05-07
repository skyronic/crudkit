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
    public function __construct($path) {
        $this->path = $path;
    }

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
        $this->primary_col = $expr;
    }

    /**
     * @return string
     */
    protected function getPrimaryColumn()
    {
        return $this->primary_col;
    }

    public function manyToOne ($foreignKey, $extTable, $primary, $nameColumn, $label) {
        $this->internalAddColumn(SQLColumn::CATEGORY_FOREIGN, $foreignKey, array(
            'fr_type' => 'manyToOne',
            'fr_table' => $extTable,
            'fk_primary' => $primary,
            'fk_name_col' => $nameColumn,
            'expr' => $foreignKey,
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

        $this->processColumns();
        $this->postProcessColumns();
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

    protected function postProcessColumns () {
        // Set the first summary column as a primary column
        /**
         * @var SQLColumn $sumColObj
         */
        $sumColObj = $this->columns[$this->summary_cols[0]];
        $sumColObj->setOptions(array(
            'primaryColumn' => $this->primary_col
        ));
        // TODO: Improve primary handling
    }

    /**
     * Super cool and useful function to query columns and get a reduced
     * subset
     *
     * @param $queryType
     * @param $queryValues
     * @param $valueType
     * @param bool $keyValue
     * @return array
     * @throws \Exception
     */
    protected function queryColumns ($queryType, $queryValues, $valueType, $keyValue = false, $ignoreNull = false) {
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
                else if ($queryType === "all") {
                    $target_columns []= $key;
                }
            }
        }

        $results = array();

        foreach($target_columns as $colKey) {
            /**
             * @var $column SQLColumn
             */
            $column = $this->columns[$colKey];
            $resultItem = null;
            switch($valueType) {
                case "id":
                    $resultItem = $colKey;
                    break;
                case "expr":
                    $resultItem = $column->getExpr();
                    break;
                case "object":
                    $resultItem = $column;
                    break;
                case "schema":
                    $resultItem = $column->getSchema();
                    break;
                case "summary":
                    $resultItem = $column->getSummaryConfig();
                    break;
                default:
                    throw new \Exception("Unknown value type $valueType");
            }

            if(is_null($resultItem) && $ignoreNull) {
                continue;
            }

            if($keyValue) {
                $results[$colKey] = $resultItem;
            }
            else {
                $results []= $resultItem;
            }
        }

        return $results;
    }

    protected function internalAddColumn ($category, $id, $options = array()) {
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
        $skip = isset($params['skip']) ? $params['skip'] : 0;
        $take = isset($params['take']) ? $params['take'] : 10;

        $builder = $this->conn->createQueryBuilder();
        $exec = $builder->select($this->queryColumns('all', array(), 'expr', false, true))
            ->from($this->tableName)
            ->setFirstResult($skip)
            ->setMaxResults($take)
            ->execute();

        return $exec->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getSchema()
    {
        return $this->queryColumns("category", array(SQLColumn::CATEGORY_VALUE, SQLColumn::CATEGORY_PRIMARY), "schema", true);
    }

    public function getRowCount()
    {
        return 100;
    }

    public function oneToMany($dataProvider, $externalKey, $localKey, $name)
    {

    }

    public function getSummaryColumns()
    {
        return $this->queryColumns("col_list", $this->summary_cols, "summary");
    }

    public function getEditFormOrder()
    {
        return $this->queryColumns("category",
            array(SQLColumn::CATEGORY_VALUE, SQLColumn::CATEGORY_PRIMARY),
            "id");
    }

    public function getRow($id = null)
    {
        $pk = $this->getPrimaryColumn ();
        $builder = $this->conn->createQueryBuilder();
        $exec = $builder->select($this->queryColumns('all', array(), 'expr'))
            ->from($this->tableName)
            ->where("$pk = ".$builder->createNamedParameter($id))
            ->execute();

        return $exec->fetch(PDO::FETCH_ASSOC);
    }

    public function setRow($id = null, $values = array())
    {
        $builder = $this->conn->createQueryBuilder();
        $pk = $this->primary_col;
        $builder->update($this->tableName);
        foreach($values as $formKey => $formValue) {
            if(!isset($this->columns[$formKey])) {
                throw new \Exception ("Unknown column");
            }
            $builder->set($formKey, $builder->createNamedParameter($values[$formKey]));
        }
        $builder->where("$pk = ".$builder->createNamedParameter($id))
            ->execute();
        return true;
    }

    public function getEditFormConfig()
    {
        return array();
    }

    public function getEditForm ($id = null) {
        $form = new FormHelper();
        $form->setPageId($this->page->getId());
        $form->setItemId($id);

        $formColumns = $this->queryColumns("category", array(SQLColumn::CATEGORY_VALUE), 'object');

        /**
         * @var $col SQLColumn
         */
        foreach($formColumns as $col) {
            $col->updateForm($form);
        }

        return $form;


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

    protected $primary_col = null;

}