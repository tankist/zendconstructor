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
			$className = $request->getParam('className', self::DEFAULT_CLASS_NAME);
			if (empty($className)) {
				$className = self::DEFAULT_CLASS_NAME;
			}
			
			$elementsArray = $request->getParam('elements', array());
			
			foreach ($elementsArray as &$element) {
				settype($element['elementOptions']['required'], 'boolean');
				if (!$element['elementOptions']['required']) {
					unset($element['elementOptions']['required']);
				}
			}
			
			$formDecorators = $request->getParam('decorators', array());
			$formElementDecorators = $request->getParam('elementDecorators', array());
			
			$generator = new ZendX_CodeGenerator_Php_Form(array(
				'name' => $className,
				'elements' => $elementsArray,
				'formDecorators' => $formDecorators,
				'formElementDecorators' => $formElementDecorators
			));
			
			$generator->setPrepareDecoratorsFunctionName($request->getParam('prepareFunctionName'));
			
			$this->view->code = $generator->generate();
		}
		
	}


}
