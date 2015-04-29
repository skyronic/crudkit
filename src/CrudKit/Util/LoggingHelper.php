<?php
namespace CrudKit\Util;


class LoggingHelper {
    public function log($string) {
        // TODO: make this nicer
        $out = fopen("php://stderr", "w");
        fputs($out, $string."\n");
        fclose($out);
    }

    public function vardump ($obj) {
        $this->log(print_r($obj, true));
    }
}