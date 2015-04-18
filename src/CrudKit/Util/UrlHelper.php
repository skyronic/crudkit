<?php

namespace CrudKit\Util;

use League\Url\Url;
use League\Url\UrlImmutable;


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
}