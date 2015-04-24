<?php

namespace CrudKit\Pages;

use CrudKit\Data\BaseDataProvider;
use CrudKit\Util\FormHelper;
use CrudKit\Util\TwigUtil;
use CrudKit\Util\UrlHelper;

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
        $url = new UrlHelper ();
        $pageNumber = $url->get('pageNumber', 1);
        $perPage = $url->get('perPage', 10);

        $params = array(
            'skip' => ($pageNumber - 1) * $perPage,
            'perPage' => ($pageNumber) * $perPage
        );
        return array(
            'type' => 'json',
            'data' => array (
                'count' => $this->dataProvider->getRowCount(),
                'schema' => $this->dataProvider->getSummarySchema(),
                'data' => $this->dataProvider->getSummaryData($params)
            )
        );
    }

    public function handle_edit_item () {
        $twig = new TwigUtil();
        $form = new FormHelper(array(), $this->dataProvider->getEditForm());
        $url = new UrlHelper();

        $form->setValues($this->dataProvider->getItemForId($url->get("item_id", null)));;

        $formContent = $form->render();
        $templateData = array(
            'page' => $this,
            'name' => $this->name,
            'editForm' => $formContent
        );


        return array(
            'type' => 'transclude',
            'content' => $twig->renderTemplateToString("pages/basicdata/edit_item.twig", $templateData),
            'page' => $this
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