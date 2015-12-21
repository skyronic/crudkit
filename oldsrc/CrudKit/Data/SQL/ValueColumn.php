<?php

namespace CrudKit\Data\SQL;


use CrudKit\Form\BaseFormItem;
use CrudKit\Form\TextFormItem;
use CrudKit\Form\DateTimeFormItem;
use CrudKit\Form\NumberFormItem;
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
                $item = new TextFormItem($form, $this->id, array(
                    'label' => $this->options['label']
                ));
                break;
            case "datetime":
                $item = new DateTimeFormItem($form, $this->id, array(
                    'label' => $this->options['label']
                ));
                break;
            case "number":
                $item = new NumberFormItem($form, $this->id, array(
                    'label' => $this->options['label']
                ));
                break;
        }
        $form->addItem($item);
    }

    public function getSchema()
    {
        return array(
            'type' => $this->typeName,
            'label' => $this->options['label']
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