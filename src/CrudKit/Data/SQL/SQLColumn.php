<?php

namespace CrudKit\Data\SQL;


abstract class SQLColumn {
    public $id = null;
    public $category = null;
    public $options = null;

    const CATEGORY_VALUE = "value";
    const CATEGORY_PRIMARY = "primary";
    const CATEGORY_FOREIGN = "foreign";

    public function __construct ($id, $category, $options) {
        $this->id = $id;
        $this->category = $category;
        $this->options = $options;
    }

    public function doctrineColumnLookup ($col_lookup) {
        // Usually a child will override this
    }

    public function init () {
        // A child will override this
    }

    public function getExpr () {
        throw new \Exception("This column doesn't support expressions. this shouldn't be caled");
        /** @noinspection PhpUnreachableStatementInspection */
        return "";
    }
}