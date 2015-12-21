<?php
namespace CrudKit\Util;


use Doctrine\DBAL\Query\QueryBuilder;

class LoggingHelper {
    public static function log($string) {
        // Push to flashbag
        FlashBag::add("log", $string);
    }

    public static function logObject ($object) {
        FlashBag::add("log", json_encode($object), "json");
    }

    /**
     * @param QueryBuilder $builder
     */
    public static function logBuilder ($builder) {
        $sql = $builder->getSQL();
        $params = $builder->getParameters();

        self::log("Running Query: ".$sql. " with params:");
        self::logObject($params);
    }
}