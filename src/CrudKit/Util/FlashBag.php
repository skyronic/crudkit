<?php

namespace CrudKit\Util;

class FlashBag /* not to be mistaken with flashbang */ {
    public static function add ($category, $message, $extra = "") {
        if(!isset($_SESSION['_ck_flash'])) {
            $_SESSION['_ck_flash'] = array();
        }

        if(!isset($_SESSION['_ck_flash'][$category])) {
            $_SESSION['_ck_flash'][$category] = array();
        }

        $_SESSION['_ck_flash'][$category] []= array(
            'message'=>$message,
            'extra' => $extra
        );
    }

    public static function getFlashes () {
        if(!isset($_SESSION['_ck_flash'])) {
            return array();
        }
        $flashItems = $_SESSION['_ck_flash'];

        $_SESSION['_ck_flash'] = array();
        return $flashItems;
    }
}