<?php

namespace CrudKit\Data\SQL;


use Carbon\Carbon;
use CrudKit\Util\FormHelper;
use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\Type;

abstract class SQLColumn {
    public $id = null;
    public $category = null;
    public $options = array();

    /**
     * @var Type
     */
    protected $type = null;

    /**
     * @var string
     */
    protected $typeName = null;

    const CATEGORY_VALUE = "value";
    const CATEGORY_PRIMARY = "primary";
    const CATEGORY_FOREIGN = "foreign";
    const CATEGORY_EXTERNAL = "external";

    const TYPE_STRING = "string";
    const TYPE_NUMBER = "number";
    const TYPE_DATETIME = "datetime";
    const TYPE_DATE = "date";

    public function __construct ($id, $category, $options) {
        $this->id = $id;
        $this->category = $category;
        $this->options = $options;
    }

    public function doctrineColumnLookup ($col_lookup) {
        if(isset($this->options['expr']) && isset($col_lookup[$this->options['expr']]))
        {
            /**
             * @var $col Column
             */
            $col = $col_lookup[$this->options['expr']];
            $this->type = $col->getType();
            $this->typeName = self::simplifyTypeName($this->type->getName());
        }
    }

    public function cleanValue ($value) {
        switch($this->typeName) {
            case "number":
                return floatval($value);
                break;
            case "string":
                return "".$value;
                break;
            case "datetime":
                $timezone = isset($this->options['timezone']) ? $this->options['timezone'] : "UTC";
                // Assuming that the value that client has given is in UTC
                $timeObject = Carbon::createFromTimestamp(intval($value), "UTC");

                // Now convert this into the target timezone
                $timeObject->setTimezone($timezone);
                return $timeObject;
                break;
            case "date":
                $date = date_create($value);
                $date = date_format($date,'m-d-Y');
                return $date;
                break;
            default:
                throw new \Exception("Unknown type {$this->typeName}");
        }
    }

    public function prepareForClient ($value) {
        switch($this->typeName) {
            case "number":
                return "".floatval($value);
                break;
            case "string":
                return "".$value;
                break;
            case "datetime":
                $timezone = isset($this->options['timezone']) ? $this->options['timezone'] : "UTC";
                $timeObject = Carbon::parse($value, $timezone);
                // Convert that into UTC
                $timeObject->setTimezone("UTC");
                return $timeObject->getTimestamp();
                break;
            case "date":
                $date = date_create($value);
                $date = date_format($date,'m-d-Y');
                return $date;
                break;
            default:
                throw new \Exception("Unknown type {$this->typeName}");
        }
    }

    /**
     * @param $builder QueryBuilder
     * @param $expr ExpressionBuilder
     * @param $type string
     * @param $value
     * @param bool $orFlag
     * @return string
     * @throws \Exception
     */
    public function addFilterToBuilder($builder, $expr, $type, $value, $orFlag = false)
    {
        switch ($type){
            case "contains":
            case "like":
                return $expr->like($this->getExpr(), $builder->createNamedParameter("%".$value."%"));
            case "sw": // starts with
                return $expr->like($this->getExpr(), $builder->createNamedParameter($value."%"));
            case "ew": // starts with
                return $expr->like($this->getExpr(), $builder->createNamedParameter("%".$value));
            case "eq":
                return $expr->eq($this->getExpr(), $builder->createNamedParameter($value));
            case "gt":
                return $expr->gt ($this->getExpr(), $builder->createNamedParameter($value));
            case "gte":
                return $expr->gte ($this->getExpr(), $builder->createNamedParameter($value));
            case "lt":
                return $expr->gt ($this->getExpr(), $builder->createNamedParameter($value));
            case "lte":
                return $expr->gte ($this->getExpr(), $builder->createNamedParameter($value));
            default:
                throw new \Exception("Unkown filter type $type");
        }
    }

    public static function simplifyTypeName ($typeName) {
        switch($typeName) {
            case "integer":
            case "float":
            case "smallint":
            case "bigint":
            case "decimal":
            case "numeric":
            case "smallint":
            case "bigint":
                return self::TYPE_NUMBER;
            case "string":
            case "text":
                return self::TYPE_STRING;
            case "datetime":
                return self::TYPE_DATETIME;
            case "date":
                return self::TYPE_DATE;
            default:
                throw new \Exception("Unknown type $typeName");
        }
    } 

    public function setOptions($values) {
        $this->options = array_merge($this->options, $values);
    }

    public function getExprAs() {
        if(is_null($this->getExpr()))
        {
            // Some columns don't get expressions. In those case return null
            return null;
        }

        return $this->getExpr()." AS ".$this->id;
    }

    public function init () {
        // A child will override this
    }

    /**
     * @param $form FormHelper
     * @return mixed
     */
    public abstract function updateForm ($form);
    public abstract function getSchema ();
    public abstract function getExpr ();
    public abstract function getSummaryConfig ();

}
