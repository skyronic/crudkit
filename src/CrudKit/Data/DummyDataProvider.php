<?php

namespace CrudKit\Data;

use CrudKit\Util\LoggingHelper;

class DummyDataProvider extends BaseDataProvider
{

    public function getData(array $params = array())
    {
        $skip = isset($params['skip']) ? $params['skip'] : 0;
        $take = isset($params['take']) ? $params['take'] : 10;

        $data = array();
        for($i = 0; $i < $take; $i ++ ) {
            $data []= array(
                'id' => $skip + $i,
                'foo' => "A - " . ($skip + $i),
                'bar' => "B - " . ($skip + $i),
            );
        }

        return $data;
    }

    public function getSchema()
    {
        return array(
            'id' => array (
                'type' => "primary"
            ),
            'foo' => array(
                'type' => 'text'
            ),
            'bar' => array (
                'type' => 'text'
            )
        );
    }

    public function getSummaryColumns()
    {
        return array(
            array(
                'key' => 'foo',
                'name' => "Foo",
                'renderType' => 'primaryLink',
                'primaryColumn' => 'id'
            ),
            array(
                'key' => 'bar',
                'name' => "Bar",
                'renderType' => 'text'
            )
        );
    }

    public function getRowCount(array $params = [])
    {
        return 100;
    }

    public function getEditFormConfig()
    {
        return array(
            'foo' => array(
                'label' => "Foo",
                'type' => 'text',
                'validation' => 'required'
            ),
            'bar' => array (
                'label' => "Bar",
                'type' => 'text',
                'validation' => 'required'
            )
        );
    }

    public function getEditFormOrder () {
        return array('foo', 'bar');
    }

    public function getRow($id = null)
    {
        $id = intval($id);
        return array(
            'id' => $id,
            'foo' => "A - $id",
            'bar' => "B - $id"
        );
    }

    public function setRow($id = null, array $values = [])
    {
        $log = new LoggingHelper();
        $log->vardump($values);
    }

    /**
     * @param array $values
     * @return int
     */
    public function createItem(array $values)
    {
        throw new \Exception('This Data Provider does not support creating additional data.');
    }

    /**
     * @param mixed $rowId
     * @return bool
     */
    public function deleteItem($rowId)
    {
        return true;
    }

    /**
     * @param array $ids
     * @return bool
     */
    public function deleteMultipleItems($ids)
    {
        return true;
    }

    /**
     * @param $id
     * @param $foreign_key
     * @return array
     */
    public function getRelationshipValues($id, $foreign_key)
    {
        return [
            'type' => 'json',
            'data' => [
                'values' => []
            ]
        ];
    }

    /**
     * @param array $values
     * @return array
     */
    public function validateRequiredRow(array $values = [])
    {
        return [];
    }

    /**
     * @param array $values
     * @return array
     */
    public function validateRow(array $values = [])
    {
        return [];
    }
}