<?php

namespace CrudKit\Pages;


use CrudKit\Data\SQLDataProvider;

abstract class BaseSQLDataPage extends BasicDataPage {
    /**
     * @var SQLDataProvider
     */
    protected $sqlProvider = null;

    protected function preInit ($id, $conn) {
        $this->setId($id);
        $this->sqlProvider = new SQLDataProvider();
        $this->sqlProvider->setConn($conn);
    }


    /**
     * Set the name of the table to work with.
     *
     * @param $tableName
     * @return $this
     */
	public function setTableName ($tableName) {
        $this->sqlProvider->setTable($tableName);
        return $this;
	}

    /**
     * Add a column to edit
     *
     * @param string $column_name
     * @param string $label
     * @param array $options
     * @return $this
     */
    public function addColumn ($column_name, $label, $options = array()) {
        $this->sqlProvider->addColumn($column_name, $column_name, $label, $options);
        return $this;
    }

    public function hasMany ($id, $name, $columns) {
        $this->sqlProvider->hasMany($id, $name, $columns);
        return $this;
    }

    /**
     * Add a column to edit with a unique ID
     *
     * @param string $id
     * @param string $column_name
     * @param string $label
     * @param array $options
     * @return $this
     */
    public function addColumnWithId ($id, $column_name, $label, $options = array()) {
        $this->sqlProvider->addColumn($id, $column_name, $label, $options);
        return $this;
    }

    /**
     * Set the columns to display in the summary table
     *
     * @param $summaryColumns
     * @return $this
     */
    public function setSummaryColumns ($summaryColumns) {
        $this->sqlProvider->setSummaryColumns($summaryColumns);
        return $this;
    }

    /**
     * Set the primary column
     * @param $primaryColumn
     * @return $this
     */
    public function setPrimaryColumn ($primaryColumn) {
        $this->sqlProvider->setPrimaryColumn($primaryColumn, $primaryColumn);
        return $this;
    }

    /**
     * @param $id
     * @param $primaryColumn
     * @return $this
     */
    public function setPrimaryColumnWithId ($id, $primaryColumn) {
        $this->sqlProvider->setPrimaryColumn($id, $primaryColumn);
        return $this;
    }

	public function init ($app = null) {
        $this->setDataProvider($this->sqlProvider);
		parent::init ($app);
	}
}