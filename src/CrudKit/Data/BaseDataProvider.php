<?php

namespace CrudKit\Data;

abstract class BaseDataProvider {
    public abstract function getSummaryData ($params = array());
    public abstract function getSummarySchema ();
    public abstract function getRowCount ();
    public abstract function getEditForm ();
}