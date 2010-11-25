<?php
class Skaya_Filter_UcWords extends Zend_Filter_StringToUpper {
	
	public function filter($value) {
		if ($this->_encoding !== null) {
			return mb_convert_case($value, MB_CASE_TITLE, $this->_encoding);
		}
		return ucwords($value);
	}
	
}
?>
