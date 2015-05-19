<?php

namespace CrudKit\Data;

use CrudKit\Util\LoggingHelper;

class DummyDataProvider extends BaseDataProvider {

    public function getData($params = array())
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

    public function getRowCount()
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

    public function setRow($id = null, $values = array())
    {
        $log = new LoggingHelper();
        $log->vardump($values);
    }
}