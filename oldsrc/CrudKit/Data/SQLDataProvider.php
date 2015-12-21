<?php

namespace CrudKit\Data;


use CrudKit\Data\SQL\ForeignColumn;
use CrudKit\Data\SQL\PrimaryColumn;
use CrudKit\Data\SQL\ExternalColumn;
use CrudKit\Data\SQL\SQLColumn;
use CrudKit\Data\SQL\ValueColumn;
use CrudKit\Util\FormHelper;
use CrudKit\Util\LoggingHelper;
use CrudKit\Util\RouteGenerator;
use CrudKit\Util\UrlHelper;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\Column;
use PDO;

class SQLDataProvider extends BaseSQLDataProvider{
    public function setConn ($conn) {
        $this->conn = $conn;
    }

    public function addColumn ($id, $expr, $label, $options = array()) {
        $options['label'] = $label;
        $options['expr'] = $expr;

        $this->internalAddColumn(SQLColumn::CATEGORY_VALUE, $id, $options);
    }

    public function hasMany ($id, $label, $page, $options) {
        $this->internalAddColumn(SQLColumn::CATEGORY_EXTERNAL, $id, array(
            'page_id' => $page->getId (),
            'label' => $label,
            'type' => ExternalColumn::HAS_MANY,
            'foreign_key' => $options['foreign_key'],
            'local_key' => $options['local_key']
        ));
    }

    public function setPrimaryColumn ($id, $expr) {
        $this->internalAddColumn(SQLColumn::CATEGORY_PRIMARY, $id, array(
            'name' => "Primary",
            'expr' => $expr
        ));
        $this->primary_col = $id;
    }

    /**
     * @return SQLColumn
     */
    protected function getPrimaryColumn()
    {
        return $this->columns[$this->primary_col];
    }

    public function manyToOne ($id, $foreignKey, $extTable, $primary, $nameColumn, $label) {
        $this->internalAddColumn(SQLColumn::CATEGORY_FOREIGN, $id, array(
            'fk_type' => 'manyToOne',
            'fk_table' => $extTable,
            'fk_primary' => $primary,
            'fk_name_col' => $nameColumn,
            'expr' => $foreignKey,
            'label' => $label
        ));
    }

    public function init () {
        parent::init();

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
                case SQLColumn::CATEGORY_VALUE:
                    $target = new ValueColumn($id, $category, $opts);
                    break;
                // case "foreign":
                //     $target = new ForeignColumn($id, $category, $opts);
                //     break;
                case SQLColumn::CATEGORY_PRIMARY:
                    $target = new PrimaryColumn($id, $category, $opts);
                    break;
                case SQLColumn::CATEGORY_EXTERNAL:
                    $target = new ExternalColumn ($id, $category, $opts);
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
            'primaryColumn' => $this->getPrimaryColumn()->id
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
                case "exprAs":
                    $resultItem = $column->getExprAs();
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

    protected function prepareObjectForClient ($object) {
        $result = array();
        foreach($object as $key => $value) {
            /**
             * @var $col SQLColumn
             */
            $col = $this->columns[$key];
            $result[$key] = $col->prepareForClient($value);
        }

        return $result;
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
        $builder->select($this->queryColumns('all', array(), 'exprAs', false, true))
            ->from($this->tableName)
            ->setFirstResult($skip)
            ->setMaxResults($take);

        if(isset($params['filters_json'])) {
            $filters = json_decode($params['filters_json'], true);
            if(count($filters) > 0) {
                $this->addConditionsToBuilder($builder, $filters);
            }
        }
        LoggingHelper::logBuilder($builder);
        $exec = $builder->execute();

        return $exec->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param $builder QueryBuilder
     * @param $filters
     */
    protected function addConditionsToBuilder ($builder, $filters) {
        foreach($filters as $filterItem) {
            $id = $filterItem['id'];
            if($id === "_ck_all_summary") {
                $target_cols = $this->summary_cols;

                $exprList = array();
                foreach($target_cols as $colKey) {
                    /**
                     * @var $col SQLColumn
                     */
                    $col = $this->columns[$colKey];
                    $val = $col->cleanValue($filterItem['value']);
                    $exprString = $col->addFilterToBuilder ($builder, $builder->expr(), $filterItem['type'], $val);
                    $exprList []= $exprString;
                    $composite = call_user_func_array(array($builder->expr(), "orX"), $exprList);
                    $builder->andWhere($composite);
                }
            }
            if(isset($this->columns[$id])) {
                /**
                 * @var $col SQLColumn
                 */
                $col = $this->columns[$id];
                $val = $col->cleanValue($filterItem['value']);
                $exprString = $col->addFilterToBuilder ($builder, $builder->expr(), $filterItem['type'], $val);
                $builder->andWhere($exprString);
            }
        }
    }

    public function getSchema()
    {
        return $this->queryColumns("category", array(SQLColumn::CATEGORY_VALUE, SQLColumn::CATEGORY_PRIMARY), "schema", true);
    }

    public function getRowCount($params = array())
    {
        $builder = $this->conn->createQueryBuilder();
        $builder->select(array("COUNT(".$this->getPrimaryColumn()->getExpr().") AS row_count"))
            ->from($this->tableName);

        if(isset($params['filters_json'])) {
            $filters = json_decode($params['filters_json'], true);
            if(count($filters) > 0) {
                $this->addConditionsToBuilder($builder, $filters);
            }
        }

        LoggingHelper::logBuilder($builder);
        $exec = $builder->execute();

        $countResult = $exec->fetchAll(\PDO::FETCH_ASSOC);
        return $countResult[0]['row_count'];
    }

    public function oneToMany($id, $dataProvider, $externalKey, $localKey, $name)
    {
        // Make sure data provider gets inited properly
        $this->initQueue []= $dataProvider;

        $this->internalAddColumn(SQLColumn::CATEGORY_FOREIGN, $id, array(
            'fk_type' => 'oneToMany',
            'fk_provider' => $dataProvider,
            'fk_extKey' => $externalKey,
            'fk_localKey' => $localKey,
            'label' => $name
        ));
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
        $pk = $this->getPrimaryColumn ()->getExpr();
        $builder = $this->conn->createQueryBuilder();
        $exec = $builder->select($this->queryColumns('all', array(), 'exprAs', false, true))
            ->from($this->tableName)
            ->where("$pk = ".$builder->createNamedParameter($id))
            ->execute();

        LoggingHelper::logBuilder($builder);
        $values = $exec->fetch(PDO::FETCH_ASSOC);
        return $this->prepareObjectForClient($values);
    }

    public function setRow($id = null, $values = array())
    {
        $builder = $this->conn->createQueryBuilder();
        $pk = $this->getPrimaryColumn()->getExpr();
        $builder->update($this->tableName);
        foreach($values as $formKey => $formValue) {
            /** @var SQLColumn $col */
            $col = null;
            if(!isset($this->columns[$formKey])) {
                throw new \Exception ("Unknown column");
            }
            $col = $this->columns[$formKey];
            $val = $col->cleanValue ($values[$formKey]);
            $builder->set($col->getExpr(), $builder->createNamedParameter($val));
        }

        LoggingHelper::logBuilder($builder);
        $builder->where("$pk = ".$builder->createNamedParameter($id))
            ->execute();
        return true;
    }

    public function deleteItem($rowId)
    {
        $builder = $this->conn->createQueryBuilder();
        $pk = $this->getPrimaryColumn()->getExpr();
        $status = $builder->delete($this->tableName)
            ->where("$pk = ".$builder->createNamedParameter($rowId))
            ->execute();

        return $status;
    }

    public function createItem($values)
    {
        $builder = $this->conn->createQueryBuilder();
        $builder->insert($this->tableName);
        foreach($values as $formKey => $formValue) {
            /** @var SQLColumn $col */
            $col = null;
            if(!isset($this->columns[$formKey])) {
                throw new \Exception ("Unknown column");
            }
            $col = $this->columns[$formKey];
            $builder->setValue($col->getExpr(), $builder->createNamedParameter($values[$formKey]));
        }

        LoggingHelper::logBuilder($builder);
        $builder->execute();
        return $this->conn->lastInsertId();

    }

    public function deleteMultipleItems($ids)
    {
        $builder = $this->conn->createQueryBuilder();
        $pk = $this->getPrimaryColumn()->getExpr();
        $expr = $builder->expr();

        $builder->delete($this->tableName)
            ->where($expr->in($pk, $ids));
        LoggingHelper::logBuilder($builder);
        $status = $builder->execute();

        return $status;
    }

    public function validateRequiredRow($values = array()) {
        $failed = array();
        foreach($this->columns as $columnKey => $col) {
            if(isset($col->options["required"]) && $col->options["required"]) {
                if(!isset($values[$columnKey]) || empty($values[$columnKey])) {
                    $failed[$columnKey] = "missing";
                }
            }
        }
        return $failed;
    }

    public function validateRow($values = array()) {
        $failed = array();
        foreach($values as $formKey => $formValue) {
            $col = null;
            if(!isset($this->columns[$formKey])) {
                throw new \Exception ("Unknown column");
            }
            $col = $this->columns[$formKey];
            //check if validator available
            if(isset($col->options["validator"]) && is_callable($col->options["validator"])){
                if(!$col->options["validator"]($col->cleanValue($formValue))){
                    $failed[$formKey] = $formValue;
                }
            }
            if(isset($col->options["required"]) && $col->options["required"]) {
                if(empty($values[$formKey])) {
                    $failed[$formKey] = "missing";
                }
            }

        }
        return $failed;
    }

    public function getEditFormConfig()
    {
        return array();
    }

    public function getEditForm ($id = null) {
        $form = new FormHelper();

        $formColumns = $this->queryColumns("category", array(SQLColumn::CATEGORY_VALUE, SQLColumn::CATEGORY_EXTERNAL), 'object');

        /**
         * @var $col SQLColumn
         */
        foreach($formColumns as $col) {
            $col->updateForm($form);
        }

        return $form;
    }

    public function getRelationshipValues ($id, $foreign) {

        $builder = $this->conn->createQueryBuilder();
        $forColumn = $this->columns[$foreign];
        $forOpts = $forColumn->options;
        if($forOpts['fk_type'] === "manyToOne") {
            $statement = $builder->select(array($forOpts['fk_name_col']." AS label", $forOpts['fk_primary']." AS id"))
                ->from($forOpts['fk_table'])
                ->setMaxResults(100)
                ->execute();

            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        else if($forOpts['fk_type'] === "oneToMany") {
            /**
             * @var $extProvider SQLiteDataProvider
             */
            $extProvider = $forOpts['fk_provider'];
            return $extProvider->getForeignValues($forOpts['fk_extKey'], $id);
        }
        else {
            throw new \Exception("Unknown relationship value");
        }
        return array();
    }

    public function getForeignValues ($localKey, $value) {
        $builder = $this->conn->createQueryBuilder();
        $exec = $builder->select($this->queryColumns('all', array(), 'exprAs', false, true))
            ->from($this->tableName)
            ->where("$localKey = ".$builder->createNamedParameter($value))
            ->setMaxResults(10) // TODO fix this
            ->execute();

        return $exec->fetchAll(\PDO::FETCH_ASSOC);
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