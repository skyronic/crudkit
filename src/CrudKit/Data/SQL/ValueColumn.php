<?php

namespace CrudKit\Data\SQL;


use CrudKit\Form\BaseFormItem;
use CrudKit\Form\TextFormItem;
use CrudKit\Util\FormHelper;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\Type;

class ValueColumn extends SQLColumn {

    /**
     * @param $form FormHelper
     * @return mixed
     */
    public function updateForm($form)
    {
        /** @var BaseFormItem $item */
        $item = null;
        switch($this->typeName) {
            case "string":
                $item = new TextFormItem("foo", $this->id, array(
                    'value' => ""
                ));
                break;
        }
        $form->addItem($item);
    }

    public function getSchema()
    {
        return array(
            'type' => $this->typeName
        );
    }

    public function getExpr()
    {
        return $this->options['expr'];
    }

    public function getSummaryConfig()
    {
        $summaryConf = array(
            'key' => $this->id,
            'name' => $this->options['label'],
            'renderType' => $this->typeName
        );

        if(isset($this->options['primaryColumn'])) {
            $summaryConf['primaryColumn'] = $this->options['primaryColumn'];
            $summaryConf['renderType'] = "primaryLink";
        }

        return $summaryConf;
    }


    public function doctrineColumnLookup($col_lookup)
    {
        parent::doctrineColumnLookup($col_lookup);

    }
}