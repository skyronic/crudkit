<?php

namespace CrudKit\Pages;

use CrudKit\Data\BaseDataProvider;
use CrudKit\Data\DataProvider;
use CrudKit\Util\FormHelper;
use CrudKit\Util\LoggingHelper;
use CrudKit\Util\RouteGenerator;
use CrudKit\Util\TwigUtil;
use CrudKit\Util\ValueBag;
use CrudKit\Util\UrlHelper;
use CrudKit\Util\FlashBag;

use \Exception;

class BasicDataPage extends BasePage{

    function render()
    {
        $twig = new TwigUtil();
        $writableFlag = !$this->app->isReadOnly ();
        ValueBag::set ("writable", $writableFlag);
        ValueBag::set ("rowsPerPage", $this->rowsPerPage);
        return $twig->renderTemplateToString("pages/basicdata.twig", array(
            'route' => new RouteGenerator(),
            'page' => $this,
            'writable' => $writableFlag,
            'name' => $this->name
        ));
    }

    /**
     * Get the column specification and send to the client
     * @return array
     */
    public function handle_get_colSpec () {
        $url = new UrlHelper ();
        $filters = $url->get("filters_json", "[]");

        $params = array(
            'filters_json' => $filters
        );

        return array(
            'type' => 'json',
            'data' => array (
                'count' => $this->dataProvider->getRowCount($params),
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
        $filters = $url->get("filters_json", "[]");

        $params = array(
            'skip' => ($pageNumber - 1) * $perPage,
            'take' => $perPage,
            'filters_json' => $filters
        );
        return array(
            'type' => 'json',
            'data' => array (
                'rows' => $this->dataProvider->getData($params)
            )
        );
    }

    public function handle_delete_items () {
        if ($this->app->isReadOnly ()) {
            throw new Exception ("Read Only");
        }
        $url = new UrlHelper ();
        $delete_ids = json_decode($url->get('delete_ids', "[]"), true);
        $this->dataProvider->deleteMultipleItems($delete_ids);

        return array (
            'type' => 'json',
            'data' => array (
                'success' => true
            )
        );
    }

    public function handle_view_item () {
        $twig = new TwigUtil();

        $url = new UrlHelper();
        $rowId = $url->get("item_id", null);

        $route = new RouteGenerator();
        $deleteUrl = ($route->itemFunc($this->getId(), $rowId, "delete_item"));
        $editUrl = ($route->itemFunc($this->getId(), $rowId, "edit_item"));
        $writable = !$this->app->isReadOnly ();

        $summaryKey = $this->dataProvider->getSummaryColumns()[0]['key'];
        $rowData = $this->dataProvider->getRow ($rowId);
        $rowName = $rowData[$summaryKey];

        $templateData = array(
            'page' => $this,
            'name' => $this->name,
            'rowId' => $rowId,
            'writable' => $writable,
            'deleteUrl' => $deleteUrl,
            'editUrl' => $editUrl,
            'schema' => $this->dataProvider->getSchema (),
            'rowName' => $rowName,
            'row' => $rowData,
        );

        return $twig->renderTemplateToString("pages/basicdata/view_item.twig", $templateData);
    }

    public function handle_edit_item () {
        if ($this->app->isReadOnly ()) {
            throw new Exception ("Read Only");
        }
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
        if ($this->app->isReadOnly ()) {
            throw new Exception ("Read Only");
        }
        $url = new UrlHelper();
        $rowId = $url->get("item_id", null);
        $route = new RouteGenerator();
        $summaryKey = $this->dataProvider->getSummaryColumns()[0]['key'];
        $rowData = $this->dataProvider->getRow ($rowId);
        $rowName = $rowData[$summaryKey];

        $status = $this->dataProvider->deleteItem ($rowId);
        FlashBag::add("alert", "Item $rowName has been deleted", "success");

        // Redirect back to the pageme
        return array(
            'type' => 'redirect',
            'url' => $route->openPage($this->getId())
        );
    }

    public function handle_new_item () {
        if ($this->app->isReadOnly ()) {
            throw new Exception ("Read Only");
        }
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
        $item_id = $url->get("item_id", null);
        if($item_id === "_ck_new"){
            return array(
                'type' => 'json',
                'data' => array (
                    'schema' => $this->dataProvider->getSchema(),
                    'values' => array()
                    )
                );
        }
        return array(
            'type' => 'json',
            'data' => array (
                'schema' => $this->dataProvider->getSchema(),
                'values' => $this->dataProvider->getRow($item_id)
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
        if ($this->app->isReadOnly ()) {
            throw new Exception ("Read Only");
        }
        $url = new UrlHelper();

        $values = json_decode($url->get("values_json", "{}"), true);

        //We have to check that all required fields are in request AND all provided data meet requirements
        $failedValues = array_merge(
            $this->dataProvider->validateRow($values), 
            $this->dataProvider->validateRequiredRow($values));
        if(empty($failedValues)){
            $new_pk = $this->dataProvider->createItem($values);
            FlashBag::add("alert", "Item $new_pk has been created", "success");
            return array(
                'type' => 'json',
                'data' => array(
                   'success' => true,
                  'newItemId' => $new_pk
                )
            );
        }
        else {
            FlashBag::add("alert", "Could not set certain fields", "error");

            return array(
                'type' => 'json',
                'data' => array(
                    'success' => true,
                    'dataValid' => false,
                    'failedValues' => $failedValues
                )
            );
            //throw new \Exception("Cannot validate values");
        }
       
    }

    public function handle_set_form_values () {
        if ($this->app->isReadOnly ()) {
            throw new Exception ("Read Only");
        }
        $form = new FormHelper(array(), $this->dataProvider->getEditFormConfig());
        $url = new UrlHelper();

        $values = json_decode($url->get("values_json", "{}"), true);
        if(empty($values)) {
            return array(
                'type' => 'json',
                'data' => array(
                    'success' => true
                )
            );
        }

        //validate
        $failedValues = $this->dataProvider->validateRow($values);
        if(empty($failedValues)) {
            $url = new UrlHelper();
            $rowId = $url->get("item_id", null);
            $summaryKey = $this->dataProvider->getSummaryColumns()[0]['key'];
            $rowData = $this->dataProvider->getRow ($rowId);
            $rowName = $rowData[$summaryKey];

            $this->dataProvider->setRow($url->get("item_id", null), $values);
            FlashBag::add("alert", "Item $rowName has been updated", "success");
            return array(
                'type' => 'json',
                'data' => array(
                    'success' => true,
                    'dataValid' => true
                )
            );
        }
        else {
            FlashBag::add("alert", "Could not update certain fields", "error");

            return array(
                'type' => 'json',
                'data' => array(
                    'success' => true,
                    'dataValid' => false,
                    'failedValues' => $failedValues
                )
            );
            //throw new \Exception("Cannot validate values");
        }
    }

    // TODO: start organizing parameters 
    protected $rowsPerPage = 10;
    public function setRowsPerPage ($rows = 10) {
        $this->rowsPerPage = $rows;
        return $this;
    }

    /**
     * @var DataProvider
     */
    protected $dataProvider = null;

    /**
     * @return DataProvider
     */
    public function getDataProvider()
    {
        return $this->dataProvider;
    }

    /**
     * @param DataProvider $dataProvider
     */
    public function setDataProvider($dataProvider)
    {
        $this->dataProvider = $dataProvider;
        $this->dataProvider->setPage($this);
    }

    public function init ($app = null) {
        parent::init($app);
        $this->dataProvider->init();
    }
}

