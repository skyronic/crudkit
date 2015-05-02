<?php

namespace CrudKit\Util;

class ValueBag {
	protected static $values = array();

	public static function getValues () {
		return self::$values;
	}

	public static function set($key, $value) {
		self::$values[$key] = $value;
	}
}