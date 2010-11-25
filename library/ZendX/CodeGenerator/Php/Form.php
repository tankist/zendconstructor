<?php
class ZendX_CodeGenerator_Php_Form extends Zend_CodeGenerator_Php_Class {
	
	protected $_elements = array();
	
	protected $_formDecorators = array();
	
	protected $_formElementDecorators = array();
	
	protected $_extendedClass = 'Zend_Form';
	
	protected $_prepareDecoratorsFunctionName = '';
	
	public function setElements($elements) {
		$this->_elements = $elements;
		return $this;
	}
	
	public function getElements() {
		return $this->_elements;
	}
	
	public function addElement($element) {
		$this->_elements[] = $element;
		return $this;
	}
	
	public function setFormDecorators($decorators) {
		$this->_formDecorators = $decorators;
		return $this;
	}
	
	public function getFormDecorators() {
		return $this->_formDecorators;
	}
	
	public function addFormDecorator($decorator) {
		$this->_formDecorators[] = $decorator;
		return $this;
	}
	
	public function setFormElementDecorators($decorators) {
		$this->_formElementDecorators = $decorators;
		return $this;
	}
	
	public function getFormElementDecorators() {
		return $this->_formElementDecorators;
	}
	
	public function addFormElementDecorator($decorator) {
		$this->_formElementDecorators[] = $decorator;
		return $this;
	}
	
	public function setPrepareDecoratorsFunctionName($prepareDecoratorsFunctionName = '') {
		$this->_prepareDecoratorsFunctionName = $prepareDecoratorsFunctionName;
		return $this;
	}
	
	public function getPrepareDecoratorsFunctionName() {
		return $this->_prepareDecoratorsFunctionName;
	}
	
	public function generate() {
		$initMethod = new Zend_CodeGenerator_Php_Method(array(
			'name' => 'init',
			'visibility' => Zend_CodeGenerator_Php_Member_Abstract::VISIBILITY_PUBLIC
		));
		
		$elementsText = $decoratorsText = '';
		foreach ($this->getElements() as $key => $element) {
			if (!$element instanceOf ZendX_CodeGenerator_Php_FormElement) {
				$element = new ZendX_CodeGenerator_Php_FormElement($element);
			}
			$elementsText .= self::LINE_FEED . $this->getIndentation() . '->addElement(' . $element->generate() . ')';
		}
		if (!empty($elementsText)) {
			$elementsText = '$this' . $elementsText . ';';
		}
		
		$formDecorators = $this->getFormDecorators();
		if (!empty($formDecorators)) {
			$decoratorsText .= '$this->setDecorators(' . $this->_generateDecoratorsList($formDecorators) . ');';
		}
		
		$formDecorators = $this->getFormElementDecorators();
		if (!empty($formDecorators)) {
			if (!empty($decoratorsText)) {
				$decoratorsText .= self::LINE_FEED . self::LINE_FEED;
			}
			$decoratorsText .= '$this->setElementDecorators(' . $this->_generateDecoratorsList($formDecorators) . ');';
		}
		
		if ($prepareFunctionName = $this->getPrepareDecoratorsFunctionName()) {
			$prepareDecoratorsMethod = new Zend_CodeGenerator_Php_Method(array(
				'name' => $prepareFunctionName,
				'visibility' => Zend_CodeGenerator_Php_Member_Abstract::VISIBILITY_PUBLIC
			));
			$prepareDecoratorsMethod->setBody($decoratorsText . self::LINE_FEED . self::LINE_FEED . 'return $this;');
		}
		else {
			$elementsText .= self::LINE_FEED . self::LINE_FEED . $decoratorsText;
		}
		
		$initMethod->setBody($elementsText);
		
		$this->setMethod($initMethod);
		
		if (isset($prepareDecoratorsMethod) && $prepareDecoratorsMethod instanceof Zend_CodeGenerator_Php_Method) {
			$this->setMethod($prepareDecoratorsMethod);
		}
		
		return parent::generate();
	}
	
	protected function _generateDecoratorsList($formDecorators) {
		$decoratorsText = '';
		if (!empty($formDecorators)) {
			$decoratorsDefinition = array();
			foreach ($formDecorators as $decorator) {
				if (is_string($decorator)) {
					$decorator = array('decoratorType' => $decorator);
				}
				if (is_array($decorator) && isset($decorator['decoratorType'])) {
					$decoratorInstance = new ZendX_CodeGenerator_Php_FormDecorator($decorator);
					$decoratorsDefinition[] = self::LINE_FEED . $this->getIndentation() . $decoratorInstance->generate();
				}
			}
			$decoratorsText = 'array(' . join(',', $decoratorsDefinition). self::LINE_FEED . ')';
		}
		return $decoratorsText;
	}
	
}
?>
