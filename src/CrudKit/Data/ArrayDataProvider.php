<?php
namespace CrudKit\Data;

use CrudKit\Form\DateTimeFormItem;
use CrudKit\Form\NumberFormItem;
use CrudKit\Form\TextFormItem;
use CrudKit\Util\FormHelper;

class ArrayDataProvider extends BaseDataProvider
{
    /**
     * @var array[]
     */
    protected $schema = [];

    /**
     * @var string
     */
    protected $summaryColumns = [];

    /**
     * @var array[]
     */
    protected $data = [];

    public function __construct(array $schema, array $summaryCols, array $data = [])
    {
        $this->schema = $schema;
        $this->summaryColumns = $summaryCols;
        foreach($data as $key => $values) {
            $this->setRow($key, $values);
        }
    }

    public function getSchema()
    {
        return $this->schema;
    }

    public function getSummaryColumns()
    {
        $summaryCols = [];
        foreach($this->summaryColumns as $column) {
            $columnData = $this->schema[$column];
            $summaryCols[] = [
                'key'           => $column,
                'label'         => $columnData['label'],
                'renderType'    => empty($columnData['type']) ? 'string' : $columnData['type']
            ];
        }
        return $summaryCols;
    }

    public function getRowCount(array $params = [])
    {
        $data = $this->getData($params);
        return count($data);
    }

    public function getEditForm()
    {
        $form = new FormHelper();
        foreach($this->schema as $columnId => $columnOptions) {
            $this->addFormItemFromSchema($form, $columnId, $columnOptions);
        }
        return $form;
    }

    private function addFormItemFromSchema(FormHelper $form, $columnId, array $columnOptions)
    {
        $type = empty($columnOptions['type']) ? 'string' : $columnOptions['type'];
        $config = [
            'label' => $columnOptions['label']
        ];
        switch($type) {
            case 'text':
            case 'string':
                $item = new TextFormItem($form, $columnId, $config);
                break;
            case 'number':
                $item = new NumberFormItem($form, $columnId, $config);
                break;
            case 'datetime':
                $item = new DateTimeFormItem($form, $columnId, $config);
                break;
            default:
                throw new \Exception("The Column Type [$type] is not valid.");
        }
        $form->addItem($item);
    }

    public function getEditFormOrder()
    {
        return array_keys($this->schema);
    }

    public function getData(array $params = [])
    {
        if(isset($params['filters_json'])) {
            throw new \InvalidArgumentException('JSON Filters are currently not supported');
        }
        $skip = isset($params['skip']) ? $params['skip'] : 0;
        $take = isset($params['take']) ? $params['take'] : 10;

        $data = array_values($this->data);
        return array_slice($data, $skip, $take);
    }

    public function getRow($id = null)
    {
        $id = (int) $id;
        return isset($this->data[$id]) ? $this->data[$id] : [];
    }

    public function setRow($id = null, array $values = [])
    {
        $errors = $this->validateRow($values);
        if(!empty($errors)) {
            return false;
        }
        $this->data[ (int) $id ] = $values;
        return true;
    }

    /**
     * @param array $values
     * @return int
     */
    public function createItem(array $values)
    {
        foreach($values as $formKey => $formValue) {
            if(!$this->isFieldInSchema($formKey)) {
                throw new \InvalidArgumentException ("The Column [$formKey] is not defined.");
            }
        }
        $this->data[] = $values;
        end($this->data);
        return key($this->data);
    }

    /**
     * @param mixed $rowId
     * @return bool
     */
    public function deleteItem($rowId)
    {
        $rowId = (int) $rowId;
        if(isset($this->data[$rowId])) {
            unset($this->data[$rowId]);
            return true;
        }
        return false;
    }

    /**
     * @param array $ids
     * @return bool
     */
    public function deleteMultipleItems(array $ids)
    {
        $deleted = 0;
        foreach($ids as $id) {
            $deleted += (int) $this->deleteItem($id);
        }
        return $deleted;
    }

    /**
     * @param $id
     * @param $foreign_key
     * @return array
     */
    public function getRelationshipValues($id, $foreign_key)
    {
        return [
            'type' => 'json',
            'data' => [
                'values' => []
            ]
        ];
    }

    /**
     * Returns true if a field exists as part of this source's schema
     *
     * @param string $formKey
     * @return bool
     */
    protected function isFieldInSchema($formKey)
    {
        return array_key_exists($formKey, $this->schema);
    }

    /**
     * Returns a callable validator for this field if it exists, and null otherwise
     *
     * @param string $formKey
     * @return callable|null
     */
    protected function getValidatorForField($formKey)
    {
        $validator = null;
        if( isset($this->schema[$formKey]['options']['validator']) ) {
            $validator = $this->schema[$formKey]['options']['validator'];
            if(!is_callable($validator)) {
                $validator = null;
            }
        }
        return $validator;
    }

    /**
     * Returns an array of require field names
     *
     * @return string[]
     */
    protected function getRequiredFields()
    {
        $required = [];
        foreach($this->schema as $field => $fieldSchema) {
            if(isset($fieldSchema['options']['required']) && $fieldSchema['options']['required']) {
                $required[] = $field;
            }
        }
        return $required;
    }
}
