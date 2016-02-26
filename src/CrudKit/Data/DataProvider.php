<?php
namespace CrudKit\Data;

use CrudKit\Pages\Page;
use CrudKit\Util\FormHelper;

//TODO: Write Proper API Documentation
interface DataProvider
{

    public function init();

    /**
     * @return array
     */
    public function getSchema();

    /**
     * @param array $params
     * @return int
     */
    public function getRowCount(array $params = []);

    /**
     * @return array
     */
    public function getSummaryColumns();

    /**
     * @param array $params
     * @return array
     */
    public function getData(array $params = []);

    /**
     * @param mixed $id
     * @return array
     */
    public function getRow($id = null);

    /**
     * @param mixed $id
     * @param array $values
     * @return true
     */
    public function setRow($id = null, array $values = []);

    /**
     * @param array $values
     * @return int
     */
    public function createItem(array $values);

    /**
     * @param mixed $rowId
     * @return bool
     */
    public function deleteItem($rowId);

    /**
     * @param array $ids
     * @return bool
     */
    public function deleteMultipleItems(array $ids);

    /**
     * @return array
     */
    public function getEditFormConfig();

    /**
     * @return array
     */
    public function getEditFormOrder();

    /**
     * @return FormHelper
     */
    public function getEditForm();

    /**
     * @param Page $page
     */
    public function setPage(Page $page);

    /**
     * @param $id
     * @param $foreign_key
     * @return array
     */
    public function getRelationshipValues($id, $foreign_key);

    /**
     * @param array $values
     * @return array
     */
    public function validateRequiredRow(array $values = []);

    /**
     * @param array $values
     * @return array
     */
    public function validateRow(array $values = []);
}