<?php

namespace CrudKit\Pages;

use CrudKit\Data\BaseDataProvider;
use CrudKit\Util\TwigUtil;

class BasicDataPage extends BasePage{

    function render()
    {
        $twig = new TwigUtil();
        return $twig->renderTemplateToString("pages/basicdata.twig", array(
            'page' => $this,
            'name' => $this->name,
        ));
    }

    public function handle_get_summary_data () {
        return array(
            'type' => 'json',
            'data' => array (
                'count' => $this->dataProvider->getRowCount(),
                'schema' => $this->dataProvider->getSummarySchema(),
                'data' => $this->dataProvider->getSummaryData()
            )
        );
    }

    /**
     * @var BaseDataProvider
     */
    protected $dataProvider = null;

    /**
     * @return BaseDataProvider
     */
    public function getDataProvider()
    {
        return $this->dataProvider;
    }

    /**
     * @param BaseDataProvider $dataProvider
     */
    public function setDataProvider($dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

}