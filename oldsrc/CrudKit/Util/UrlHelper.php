<?php

namespace CrudKit\Util;

use League\Url\Url;
use League\Url\UrlImmutable;
use League\Url\Components\Query;


class UrlHelper {
    /**
     * @var URLImmutable
     */
    protected static $url = null;

    public function __construct () {
        if(self::$url === null) {
            self::$url = UrlImmutable::createFromServer($_SERVER);
        }
    }
    public function addGetParams ($params = array()) {
        $currentParams = self::$url->getQuery();
        $currentParams->modify($params);

        return self::$url->setQuery($currentParams);
    }

    public function resetGetParams ($params = array()) {
        return self::$url->setQuery($params);
    }

    public function has ($key) {
        return $this->get ($key, '__ck_undefined') !== "__ck_undefined";
    }

    public function get ($key, $default = null) {
        $postdata = file_get_contents("php://input", 'rb');
        $json_post = array();

        try {
            $json_post = json_decode($postdata, true);
        }
        catch (Exception $e) {
            // Don't do anything this is what's expected if json serialization fails
        }

        if(isset($_GET[$key])) {
            return $_GET[$key];
        }
        else if(isset($_POST[$key])) {
            return $_POST[$key];
        }
        else if(isset($json_post[$key]))  {
            return $json_post[$key];
        }
        else {
            return $default;
        }
    }
}