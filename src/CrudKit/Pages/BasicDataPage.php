<?php

namespace CrudKit\Pages;

use CrudKit\Data\BaseDataProvider;
use CrudKit\Util\FormHelper;
use CrudKit\Util\RouteGenerator;
use CrudKit\Util\TwigUtil;
use CrudKit\Util\ValueBag;
use CrudKit\Util\UrlHelper;

class BasicDataPage extends BasePage{

    function render()
    {
        $twig = new TwigUtil();
        return $twig->renderTemplateToString("pages/basicdata.twig", array(
            'route' => new RouteGenerator(),
            'page' => $this,
            'name' => $this->name,
        ));
    }

    /**
     * Get the column specification and send to the client
     * @return array
     */
    public function handle_get_colSpec () {
        return array(
            'type' => 'json',
            'data' => array (
                'count' => $this->dataProvider->getRowCount(),
                'schema' => $this->dataProvider->getSchema(),
                'columns' => $this->dataProvider->getSummaryColumns()
            )
        );
    }

    /**
     * Get Data
     * @return array
     */
    public function handle_get_data() {
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
                'rows' => $this->dataProvider->getData($params)
            )
        );
    }

    public function handle_edit_item () {
        $twig = new TwigUtil();

        $url = new UrlHelper();
        $rowId = $url->get("item_id", null);
        $form = $this->dataProvider->getEditForm();
        $form->setPageId($this->getId());
        $form->setItemId($rowId);

        $route = new RouteGenerator();
        $deleteUrl = ($route->itemFunc($this->getId(), $rowId, "delete_item"));

        $formContent = $form->render($this->dataProvider->getEditFormOrder());
        $templateData = array(
            'page' => $this,
            'name' => $this->name,
            'editForm' => $formContent,
            'rowId' => $rowId,
            'canDelete' => true,
            'deleteUrl' => $deleteUrl
        );

        return $twig->renderTemplateToString("pages/basicdata/edit_item.twig", $templateData);
    }

    public function handle_delete_item () {
        $url = new UrlHelper();
        $rowId = $url->get("item_id", null);
        $route = new RouteGenerator();

        $status = $this->dataProvider->deleteItem ($rowId);

        // Redirect back to the pageme
        return array(
            'type' => 'redirect',
            'url' => $route->openPage($this->getId())
        );
    }

    public function handle_new_item () {
        $twig = new TwigUtil();

        $form = $this->dataProvider->getEditForm();

        $form->setPageId($this->getId());
        $form->setNewItem();

        $formContent = $form->render($this->dataProvider->getEditFormOrder());
        $templateData = array(
            'page' => $this,
            'name' => $this->name,
            'editForm' => $formContent
        );

        return $twig->renderTemplateToString("pages/basicdata/edit_item.twig", $templateData);
    }

    public function handle_get_form_values () {
        $url = new UrlHelper ();
        return array(
            'type' => 'json',
            'data' => array (
                'schema' => $this->dataProvider->getSchema(),
                'values' => $this->dataProvider->getRow($url->get("item_id", null))
            )
        );
    }

    public function handle_get_foreign () {
        $url = new UrlHelper();
        $foreign_key = $url->get("foreign_key", null);
        $item_id = $url->get("item_id", null);

        return array(
            'type' => 'json',
            'data' => array (
                'values' => $this->dataProvider->getRelationshipValues($item_id, $foreign_key)
            )
        );
    }

    public function handle_create_item () {
        $url = new UrlHelper();

        // let's not worry about validation right now.
        $values = json_decode($url->get("values_json", "{}"), true);

        $new_pk = $this->dataProvider->createItem($values);
        return array(
            'type' => 'json',
            'data' => array(
                'success' => true,
                'newItemId' => $new_pk
            )
        );
    }

    public function handle_set_form_values () {
        $form = new FormHelper(array(), $this->dataProvider->getEditFormConfig());
        $url = new UrlHelper();

        // let's not worry about validation right now.
        $values = json_decode($url->get("values_json", "{}"), true);

        if($form->validate($values)) {
            $this->dataProvider->setRow($url->get("item_id", null), $values);
            return array(
                'type' => 'json',
                'data' => array(
                    'success' => true
                )
            );
        }
        else {
            // TODO show errors on validation fail
            throw new \Exception("Cannot validate values");
        }
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
        $this->dataProvider->setPage($this);
    }

    public function init () {
        parent::init();
        $this->dataProvider->init();
    }

}