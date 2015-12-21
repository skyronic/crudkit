<?php

namespace CrudKit\Data\SQL;


use CrudKit\Util\FormHelper;

class PrimaryColumn extends SQLColumn {

    /**
     * @param $form FormHelper
     * @return mixed
     */
    public function updateForm($form)
    {
        // Nothing here, primary columns don't get form item
    }

    public function getSchema()
    {
        return array(
            'type' => $this->typeName,
            'label' => "Primary",
            'primaryFlag' => true,
            'key' => $this->id
        );
    }

    public function getExpr()
    {
        return $this->options['expr'];
    }

    public function getSummaryConfig()
    {
        return array(
            'key' => $this->id,
            'name' => "Primary",
            'renderType' => $this->typeName
        );
    }
}