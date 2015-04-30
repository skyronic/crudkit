<?php

namespace CrudKit\Data;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Column;
use PDO;
use utilphp\util;

class SQLiteDataProvider extends BaseSQLDataProvider{

    public function setPrimaryColumn ($expr) {
        $this->addColumn($expr, "Primary", array('primary' => true), 'primary');
    }
    /**
     * @param $id String A unique string id (exposed to client)
     * @param $expr String The Expression/Column Name
     * @param $name String Name of the column (Used on forms)
     * @param array $options array of options
     */
    public function addColumn ($expr, $name, $options = array(), $category = null) {
        $category = !is_null($category) ? $category : "value";
        $item = array(
            'category' => $category,
            'expr' => $expr,
            'name' => $name,
            'options' => $options
        );

        if(isset($options['primary']) && $options['primary']) {

            $item['primary'] = true;
            $item['category'] = 'primary';
            $this->primary_col = $expr;
        }
        else
        {
            // not a primary key
            $this->col_name_list []= $expr;
        }
        $this->cols_list []= $item;
        $this->columns[$expr] = $item;
    }

    public function setSummaryColumns ($cols) {
        $this->summary_cols = $cols;
    }

    public function setTable ($table) {
        $this->tableName = $table;
    }

    protected $url;

    public function __construct($url) {
        $this->url = $url;
    }

    protected function transformColumns () {
        $sm = $this->conn->getSchemaManager();
        $columns = $sm->listTableColumns($this->tableName);

        $type_lookup = array();

        /**
         * @var $col Column
         */
        foreach($columns as $col) {
            $type_lookup[$col->getName()] = array(
                'type' => $col->getType()->getName(),
                'doctrine_type' => $col->getType(),
                'not_null' => $col->getNotnull()
            );
        }

        foreach($this->columns as $name => $opts) {
            $this->columns[$name] = array_merge($opts, $type_lookup[$name]);
        }
    }

    protected $cols_list = array();
    protected $col_name_list = array();
    protected $columns = array();
    protected $summary_cols = array();
    protected $primary_col = null;

    protected $tableName = null;

    protected function listOfValueColumns () {
        $valColumns = array();

        foreach($this->columns as $key => $val){
            if($val['category'] === 'value') {
                $valColumns []= $key;
            }
        }

        return $valColumns;
    }

    public function getData($params = array())
    {
        $skip = isset($params['skip']) ? $params['skip'] : 0;
        $take = isset($params['take']) ? $params['take'] : 10;

        $builder = $this->conn->createQueryBuilder();
        $exec = $builder->select($this->listOfValueColumns())
            ->from($this->tableName)
            ->setFirstResult($skip)
            ->setMaxResults($take)
            ->execute();

        return $exec->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getSchema()
    {
        $schema = array();

        foreach($this->columns as $expr => $opts) {
            $type = isset($opts['primary']) && $opts['primary'] ? 'primary' : $opts['type'];

            $schema[$expr] = array(
                'type' => $type
            );
        }

        return $schema;
    }

    public function getRowCount()
    {
        // TODO: do a count
        return 100;
    }

    public function getSummaryColumns()
    {
        $summary_schema = array();

        $index = 0;
        foreach($this->summary_cols as $col) {
            $colOpts = $this->columns[$col];

            $schemaItem = array (
                'key' => $col,
                'name' => $colOpts['name'],
                'renderType' => $index === 0 ? 'primaryLink' : $colOpts['type']
            );

            // TODO: find a better way to mark primary link
            if($index === 0) {
                $schemaItem['primaryColumn'] = $this->primary_col;
            }

            $summary_schema []= $schemaItem;

            $index++;
        }

        return $summary_schema;
    }

    public function getEditFormOrder()
    {
        return $this->listOfValueColumns();
    }

    public function getRow($id = null)
    {
        $pk = $this->primary_col;
        $builder = $this->conn->createQueryBuilder();
        $exec = $builder->select($this->listOfValueColumns())
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
        foreach($this->getEditFormConfig() as $formKey => $formValue) {
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
        $formSchema = array();
        foreach($this->columns as $colName => $colOpts) {
            if($colOpts['category'] === "value") {
                $formSchema [$colName] = array(
                    'label' => $colOpts['name'],
                    'type' => $colOpts['type'],
                    'validation' => "TODO"
                );
            }
        }

        return $formSchema;
    }

    protected function getRelationshipValues ($colObject) {

    }

    public function manyToOne ($foreignKey, $externalTable, $primary, $nameColumn, $label) {
        $this->addColumn($foreignKey, $label, array(
            'foreign' => true,
            'foreign_type' => 'manyToOne',
            'foreign_table' => $externalTable,
            'foreign_primary' => $primary,
            'foreign_name_col' => $nameColumn
        ), 'foreign');
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
        $this->transformColumns();
    }
}