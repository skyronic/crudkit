<?php
namespace CrudKit\Data;

use CrudKit\Pages\Page;
use CrudKit\Util\FormHelper;

abstract class BaseDataProvider implements DataProvider
{
    protected $initQueue = [];

    /**
     * @var Page
     */
    protected $page = null;

    public function init()
    {
        foreach ($this->initQueue as $item) {
            $item->init();
        }
    }

    public function getEditForm()
    {
        return new FormHelper([], $this->getEditFormConfig());
    }

    public function getEditFormConfig()
    {
        return [];
    }

    public function setPage(Page $page)
    {
        $this->page = $page;
    }

    public function validateRow(array $values = [])
    {
        $failed = [];
        foreach ($values as $formKey => $formValue) {
            if (!$this->isFieldInSchema($formKey)) {
                throw new \InvalidArgumentException ("The Column [$formKey] is not defined.");
            }
            $validator = $this->getValidatorForField($formKey);
            if ($validator && !$validator($formValue)) {
                $failed[$formKey] = $formValue;
            }
        }
        return $failed;
    }

    public function validateRequiredRow(array $values = [])
    {
        $failed = [];
        $requiredFields = $this->getRequiredFields();
        foreach ($values as $formKey => $formValue) {
            if (!$this->isFieldInSchema($formKey)) {
                throw new \InvalidArgumentException ("The Column [$formKey] is not defined.");
            }
        }
        foreach ($requiredFields as $requiredField) {
            if (empty($values[$requiredField])) {
                $failed[$requiredField] = 'missing';
            }
        }
        return $failed;
    }

    /**
     * Returns true if a field exists as part of this source's schema
     *
     * @param string $formKey
     * @return bool
     */
    protected abstract function isFieldInSchema($formKey);

    /**
     * Returns a callable validator for this field if it exists, and null otherwise
     *
     * @param string $formKey
     * @return callable|null
     */
    protected abstract function getValidatorForField($formKey);

    /**
     * Returns an array of require field names
     *
     * @return string[]
     */
    protected abstract function getRequiredFields();
}
