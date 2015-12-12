<?php

namespace CrudKit\Data\SQL;


use CrudKit\Form\HasManyItem;
use CrudKit\Util\FormHelper;

class ExternalColumn extends SQLColumn {

    // Foreign key on external table. Maps to this table
    const HAS_ONE = 'hasOne';

    // Foreign key on external table. Maps to this table.
    const HAS_MANY = 'hasMany';

    // Foreign key on this table. Maps to external table
    const BELONGS_TO = 'belongsTo';
    /**
     * @param $form FormHelper
     * @return mixed
     */
    public function updateForm($form)
    {
        if($this->options['type'] === self::HAS_MANY ) {
            $item = new HasManyItem ($form, $this->id, array(
                'label' => $this->options['label']
            ));
            $form->addItem($item);
        }
    }

    public function getSchema()
    {
        return array();
    }

    public function getExpr()
    {
        return null;
    }

    public function getSummaryConfig()
    {
        return array();
    }
}