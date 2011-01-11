<?php
class ZendX_CodeGenerator_Php_FormDecorator extends ZendX_CodeGenerator_Php_Abstract {
	
	const FORM_ELEMENT_DECORATOR_CALLBACK = 'Callback';
	const FORM_ELEMENT_DECORATOR_CAPTCHA = 'Captcha';
	const FORM_ELEMENT_DECORATOR_DESCRIPTION = 'Description';
	const FORM_ELEMENT_DECORATOR_DTDDWRAPPER = 'DtDdWrapper';
	const FORM_ELEMENT_DECORATOR_ERRORS = 'Errors';
	const FORM_ELEMENT_DECORATOR_FIELDSET = 'Fieldset';
	const FORM_ELEMENT_DECORATOR_FILE = 'File';
	const FORM_ELEMENT_DECORATOR_HTMLTAG = 'HtmlTag';
	const FORM_ELEMENT_DECORATOR_IMAGE = 'Image';
	const FORM_ELEMENT_DECORATOR_LABEL = 'Label';
	const FORM_ELEMENT_DECORATOR_TOOLTIP = 'Tooltip';
	const FORM_ELEMENT_DECORATOR_VIEWHELPER = 'ViewHelper';
	const FORM_ELEMENT_DECORATOR_VIEWSCRIPT = 'ViewScript';
	const FORM_ELEMENT_DECORATOR_FORMERRORS = 'FormErrors';
	const FORM_ELEMENT_DECORATOR_FORM = 'Form';
	const FORM_ELEMENT_DECORATOR_FORMELEMENTS = 'FormElements';
	
	protected $_decoratorOptions = array();
	
	protected $_decorator = '';
	
	protected $_name = '';
	
	protected static $_predefinedFormDecorators = array(
		self::FORM_ELEMENT_DECORATOR_CALLBACK => 'Callback',
		self::FORM_ELEMENT_DECORATOR_DESCRIPTION => 'Element description',
		self::FORM_ELEMENT_DECORATOR_FORMERRORS => 'Form errors',
		self::FORM_ELEMENT_DECORATOR_FIELDSET => 'Fieldset',
		self::FORM_ELEMENT_DECORATOR_FORM => 'Form renderer',
		self::FORM_ELEMENT_DECORATOR_FORMELEMENTS => 'Form elements renderer',
		self::FORM_ELEMENT_DECORATOR_HTMLTAG => 'HTML tag wrapper',
		self::FORM_ELEMENT_DECORATOR_VIEWSCRIPT => 'Viewscript renderer'
	);
	
	protected static $_predefinedFormElementDecorators = array(
		self::FORM_ELEMENT_DECORATOR_CALLBACK => 'Callback',
		self::FORM_ELEMENT_DECORATOR_CAPTCHA => 'Captcha',
		self::FORM_ELEMENT_DECORATOR_DESCRIPTION => 'Element description',
		self::FORM_ELEMENT_DECORATOR_DTDDWRAPPER => 'DT/DD wrapper for form elements',
		self::FORM_ELEMENT_DECORATOR_ERRORS => 'Form element errors',
		self::FORM_ELEMENT_DECORATOR_FIELDSET => 'Fieldset',
		self::FORM_ELEMENT_DECORATOR_FILE => 'File element renderer',
		self::FORM_ELEMENT_DECORATOR_HTMLTAG => 'HTML tag wrapper',
		self::FORM_ELEMENT_DECORATOR_IMAGE => 'Image element',
		self::FORM_ELEMENT_DECORATOR_LABEL => 'Element label',
		self::FORM_ELEMENT_DECORATOR_TOOLTIP => 'Tooltip for the element',
		self::FORM_ELEMENT_DECORATOR_VIEWHELPER => 'Viewhelper renderer',
		self::FORM_ELEMENT_DECORATOR_VIEWSCRIPT => 'Viewscript renderer'
	);
	
	public function setDecoratorOptions($options = array()) {
		$this->_decoratorOptions = (array)$options;
		return $this;
	}
	
	public function getDecoratorOptions() {
		return $this->_decoratorOptions;
	}
	
	public function setDecorator($decorator = array()) {
		$this->_decorator = $decorator;
		return $this;
	}
	
	public function getDecorator() {
		return $this->_decorator;
	}
	
	public function setName($name = array()) {
		$this->_name = $name;
		return $this;
	}
	
	public function getName() {
		return $this->_name;
	}
	
	public function generate() {
		$decoratorDefinition = $optionsDefinition = '';
		$type = $this->getDecorator();
		if (empty($type)) {
			throw new Zend_CodeGenerator_Exception('Decorator type must be defined');
		}
		if ($options = $this->getDecoratorOptions()) {
			$optionsDefinition = ZendX_CodeGenerator_Php_Abstract::getStringFromArray($options);
		}
		$predefinedDecorators = array_merge(self::getPredefinedFormDecorators(), self::getPredefinedFormElementDecorators());
		if (array_key_exists($type, $predefinedDecorators)) {
			$decoratorDefinition = "'$type'";
		}
		else {
			$decoratorDefinition = "new $type($optionsDefinition)";
			$optionsDefinition = '';
		}
		if ($name = $this->getName()) {
			$decoratorDefinition = "array('$name' => $decoratorDefinition)";
		}
		if (!empty($optionsDefinition)) {
			$decoratorDefinition = "array($decoratorDefinition, $optionsDefinition)";
		}
		return $decoratorDefinition;
	}
	
	public static function getPredefinedFormElementDecorators() {
		return self::$_predefinedFormElementDecorators;
	}
	
	public static function setPredefinedFormElementDecorators($formElementDecorators) {
		self::$_predefinedFormElementDecorators = $formElementDecorators;
	}
	
	public static function getPredefinedFormDecorators() {
		return self::$_predefinedFormDecorators;
	}
	
	public static function setPredefinedFormDecorators($formDecorators) {
		self::$_predefinedFormDecorators = $formDecorators;
	}
	
}
?>
