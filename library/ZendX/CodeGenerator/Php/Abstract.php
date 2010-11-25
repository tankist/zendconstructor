<?php
abstract class ZendX_CodeGenerator_Php_Abstract extends Zend_CodeGenerator_Php_Abstract {
	
	public static function getStringFromArray($array) {
		$arraysChunks = array();
		foreach ($array as $key => $value) {
			if (strpos($value, '$') == false) {
				if (is_string($value)) {
					$value = self::getStringFromString($value);
				}
				elseif (is_array($value)) {
					$value = self::getStringFromArray($value);
				}
				elseif (is_bool($value)) {
					$value = self::getStringFromBoolean($value);
				}
				else {
					$value = self::getStringFromString((string)$value);
				}
			}
			if (!is_integer($key)) {
				$key = self::getStringFromString($key);
			}
			$arraysChunks[] = "$key => $value";
		}
		$array = join(', ', $arraysChunks);
		return "array($array)";
	}
	
	public static function getStringFromString($value) {
		return "'$value'";
	}
	
	public static function getStringFromBoolean($value) {
		return ((boolean)$value)?"true":"false";
	}
	
}
?>
