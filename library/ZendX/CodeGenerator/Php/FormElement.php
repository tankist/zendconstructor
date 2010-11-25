<?php
class ZendX_CodeGenerator_Php_FormElement extends ZendX_CodeGenerator_Php_Abstract {
	
	const FORM_ELEMENT_BUTTON = 'button';
	const FORM_ELEMENT_CAPTCHA = 'captcha';
	const FORM_ELEMENT_CHECKBOX = 'checkbox';
	const FORM_ELEMENT_FILE = 'file';
	const FORM_ELEMENT_HASH = 'hash';
	const FORM_ELEMENT_HIDDEN = 'hidden';
	const FORM_ELEMENT_IMAGE = 'image';
	const FORM_ELEMENT_MULTICHECKBOX = 'multicheckbox';
	const FORM_ELEMENT_MULTISELECT = 'multiselect';
	const FORM_ELEMENT_PASSWORD = 'password';
	const FORM_ELEMENT_RADIO = 'radio';
	const FORM_ELEMENT_RESET = 'reset';
	const FORM_ELEMENT_SELECT = 'select';
	const FORM_ELEMENT_SUBMIT = 'submit';
	const FORM_ELEMENT_TEXT = 'text';
	const FORM_ELEMENT_TEXTAREA = 'textarea';
	
	protected $_name = '';
	
	protected $_type = self::FORM_ELEMENT_TEXT;
	
	protected $_elementOptions = array();
	
	protected static $_predefinedFormElements = array(
		self::FORM_ELEMENT_BUTTON => 'Button',
		self::FORM_ELEMENT_CAPTCHA => 'Captcha',
		self::FORM_ELEMENT_CHECKBOX => 'Checkbox',
		self::FORM_ELEMENT_FILE => 'File Upload',
		self::FORM_ELEMENT_HASH => 'Hash',
		self::FORM_ELEMENT_HIDDEN => 'Hidden',
		self::FORM_ELEMENT_IMAGE => 'Image',
		self::FORM_ELEMENT_MULTICHECKBOX => 'Multiple Checkbox',
		self::FORM_ELEMENT_MULTISELECT => 'Multiple Select',
		self::FORM_ELEMENT_PASSWORD => 'Password',
		self::FORM_ELEMENT_RADIO => 'Radio button',
		self::FORM_ELEMENT_RESET => 'Reset button',
		self::FORM_ELEMENT_SELECT => 'Select',
		self::FORM_ELEMENT_SUBMIT => 'Submit button',
		self::FORM_ELEMENT_TEXT => 'Text',
		self::FORM_ELEMENT_TEXTAREA => 'Textarea'
	);
	
	public function setName($name) {
		$this->_name = $name;
		return $this;
	}
	
	public function getName() {
		return $this->_name;
	}
	
	public function setType($type) {
		$this->_type = $type;
		return $this;
	}
	
	public function getType() {
		return $this->_type;
	}
	
	public function setElementOptions($elementOptions) {
		$this->_elementOptions = $elementOptions;
		return $this;
	}
	
	public function getElementOptions() {
		return $this->_elementOptions;
	}
	
	public function generate() {
		$elementText = $optionsText = '';
		
		$name = $this->getName();
		if (empty($name)) {
			throw new Zend_CodeGenerator_Exception('Empty form element name is not allowed');
		}
		
		$className = $this->getType();
		$isPredefinedFormElement = array_key_exists($className, self::getPredefinedFormElements());
		
		$options = $this->getElementOptions();
		if (is_array($options) && count($options) > 0) {
			$optionsText = ", " . self::getStringFromArray($options);
		}
		
		if (!$isPredefinedFormElement) {
			$elementText = "'$name', new $className('$name'$optionsText)";
		}
		else {
			$elementText = "'$name', '$className'$optionsText";
		}
		
		return $elementText;
	}
	
	public static function getPredefinedFormElements() {
		return self::$_predefinedFormElements;
	}
	
	public static function setPredefinedFormElements($formElements) {
		self::$_predefinedFormElements = $formElements;
	}
	
}
?>
