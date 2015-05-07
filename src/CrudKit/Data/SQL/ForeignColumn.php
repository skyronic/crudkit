<?php

namespace CrudKit\Data\SQL;


use CrudKit\Form\ManyToOneItem;
use CrudKit\Util\FormHelper;

class ForeignColumn extends SQLColumn {

    /**
     * @param $form FormHelper
     * @return mixed
     */
    public function updateForm($form)
    {
        if($this->options['fk_type'] === "manyToOne") {
            $item = new ManyToOneItem('foo', $this->id, array(
                'label' => $this->options['label']
            ));
            $form->addItem($item);
            $form->addRelationship($this->id, $this->options['fk_type']);
        }
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