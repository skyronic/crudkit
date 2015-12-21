<?php

namespace CrudKit\Data\SQL;


use CrudKit\Form\ManyToOneItem;
use CrudKit\Form\OneToManyItem;
use CrudKit\Util\FormHelper;

class ForeignColumn extends SQLColumn {

    /**
     * @param $form FormHelper
     * @return mixed
     */
    public function updateForm($form)
    {
        if($this->options['fk_type'] === "manyToOne") {
            $item = new ManyToOneItem($form, $this->id, array(
                'label' => $this->options['label']
            ));
            $form->addItem($item);
            $form->addRelationship($this->id, $this->options['fk_type']);
        }
        else if($this->options['fk_type'] === "oneToMany") {
            $item = new OneToManyItem($form, $this->id, array(
                'label' => $this->options['label'],
                'fk_provider' => $this->options['fk_provider']
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