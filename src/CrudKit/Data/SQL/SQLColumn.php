<?php

namespace CrudKit\Data\SQL;


abstract class SQLColumn {
    public $id = null;
    public $category = null;
    public $options = null;

    public function __construct ($id, $category, $options) {

    }
}