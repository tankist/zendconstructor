<?php

require_once("AbstractController.php");

class Form_IndexController extends Form_AbstractController {
	
	public $ajaxable = array(
		'save' => true
	);
	
	const DEFAULT_CLASS_NAME = 'Form';

	public function indexAction()
	{
		$userFormElements = $userFormDecorators = $userFormElementDecorators = array();
		$this->view->formElements = array_merge(ZendX_CodeGenerator_Php_FormElement::getPredefinedFormElements(), $userFormElements);
		$this->view->selectedFormElement = ZendX_CodeGenerator_Php_FormElement::FORM_ELEMENT_TEXT;
		$this->view->formDecorators = array_merge(ZendX_CodeGenerator_Php_FormDecorator::getPredefinedFormDecorators(), $userFormDecorators);
		$this->view->formElementDecorators = array_merge(ZendX_CodeGenerator_Php_FormDecorator::getPredefinedFormElementDecorators(), $userFormElementDecorators);
	}

	public function saveAction()
	{
		$request = $this->getRequest();
		if ($request->isPost()) {
			$data = $request->getPost();
			
			$className = $this->_getParam('className', self::DEFAULT_CLASS_NAME);
			if (empty($className)) {
				$className = self::DEFAULT_CLASS_NAME;
			}
			
			$elementsArray = $data['item'];
			
			foreach ($elementsArray as &$element) {
				settype($element['elementOptions']['required'], 'boolean');
				if (!$element['elementOptions']['required']) {
					unset($element['elementOptions']['required']);
				}
			}
			
			$formDecorators = (array)$data['formDecorators'];
			$formElementDecorators = (array)$data['formElementDecorators'];
			
			foreach ($formDecorators as &$decorator) {
				if (is_array($decorator) && is_string($decorator['decoratorOptions'])) {
					$decorator['decoratorOptions'] = Zend_Json::decode(stripslashes($decorator['decoratorOptions']));
				}
			}
			
			foreach ($formElementDecorators as &$decorator) {
				if (is_array($decorator) && is_string($decorator['decoratorOptions'])) {
					$decorator['decoratorOptions'] = Zend_Json::decode(stripslashes($decorator['decoratorOptions']));
				}
			}
			
			$generator = new ZendX_CodeGenerator_Php_Form(array(
				'name' => $className,
				'elements' => $elementsArray,
				'formDecorators' => $formDecorators,
				'formElementDecorators' => $formElementDecorators
			));
			
			$generator->setPrepareDecoratorsFunctionName($data['prepareFunctionName']);
			
			$this->view->code = $generator->generate();
		}
		
	}


}
