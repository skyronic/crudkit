<?php

namespace CrudKit\Data;

abstract class BaseDataProvider {
    // Data modelling
    public abstract function getData ($params = array());
    public abstract function getSchema ();
    public abstract function getRowCount ();

    // Ordering
    public abstract function getSummaryColumns ();
    public abstract function getEditFormOrder ();

    // Individual values
    public abstract function getRow ($id = null);

    // Editing Options
    public abstract function getEditFormConfig ();
}