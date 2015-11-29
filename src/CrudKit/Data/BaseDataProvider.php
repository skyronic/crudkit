<?php

namespace CrudKit\Data;

use CrudKit\Pages\BasePage;
use CrudKit\Util\FormHelper;
use CrudKit\Util\RouteGenerator;
use CrudKit\Util\UrlHelper;

abstract class BaseDataProvider {
    // Data modelling
    public abstract function getData ($params = array());
    public abstract function getSchema ();
    public abstract function getRowCount ($params = array());

    // Ordering
    public abstract function getSummaryColumns ();
    public abstract function getEditFormOrder ();

    // Individual values
    public abstract function getRow ($id = null);
    public abstract function setRow ($id = null, $values = array());

    // Editing Options
    public abstract function getEditFormConfig ();

    public abstract function deleteMultipleItems ($ids);

    public function getEditForm () {
        $form = new FormHelper(array(), $this->getEditFormConfig());

        return $form;
    }

    public function init () {
        foreach($this->initQueue as $item) {
            $item->init();
        }
    }

    protected $initQueue = array();

    /**
     * @var BasePage
     */
    protected $page = null;

    public function setPage ($page) {
        $this->page = $page;
    }
    public function getRelationshipValues($id, $foreign_key) {
        return array(
            'type' => 'json',
            'data' => array(
                'values' => array()
            )
        );
    }

    public abstract function createItem($values);

    public abstract function deleteItem($rowId);

    //validation
    public abstract function validateRequiredRow ($values = array());
    public abstract function validateRow ($values = array());
}