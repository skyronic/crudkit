<?php

namespace CrudKit\Data\SQL;


use CrudKit\Util\FormHelper;

class ForeignColumn extends SQLColumn {

    /**
     * @param $form FormHelper
     * @return mixed
     */
    public function updateForm($form)
    {
        // TODO: Implement updateForm() method.
    }

    public function getSchema()
    {
        return array();
    }

    public function getExpr()
    {
        if(isset($this->options['expr'])) {
            return $this->options['expr'];
        }
        else {
            return null;
        }
    }

    public function getSummaryConfig()
    {
        return array();
    }
}