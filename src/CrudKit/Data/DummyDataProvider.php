<?php

namespace CrudKit\Data;

class DummyDataProvider extends BaseDataProvider {

    public function getSummaryData($params = array())
    {
        $skip = isset($params['skip']) ? $params['skip'] : 0;
        $take = isset($params['take']) ? $params['take'] : 10;

        $data = array();
        for($i = 0; $i < $take; $i ++ ) {
            $data []= array(
                $skip + $i,
                "A - " . ($skip + $i),
                "B - " . ($skip + $i),
            );
        }

        return $data;
    }

    public function getSummarySchema()
    {
        return array(
            array(
                'type' => 'id'
            ),
            array(
                'name' => "Foo",
                'type' => 'link',
            ),
            array(
                'name' => "Bar",
                'type' => 'text'
            )
        );
    }

    public function getRowCount()
    {
        return 100;
    }

    public function getEditForm()
    {
        return array(
            array(
                'type' => 'text',
                'name' => "Foo"
            ),
            array(
                'type' => 'text',
                'name' => "Bar"
            ),
        );
    }
}