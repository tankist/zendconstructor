<?php
class Form_AbstractController extends Zend_Controller_Action {
	
	public function init() {
		
		if ($this->_hasParam('locale')) {
			$locale = $this->_getParam('locale');
			$localeInstance = $this->getInvokeArg('bootstrap')->locale;
			if ($localeInstance instanceOf Zend_Locale && Zend_Locale::isLocale($locale)) {
				$localeInstance->setLocale($locale);
				/**
				* @var Zend_Translate
				*/
				$translateInstance = $this->getInvokeArg('bootstrap')->translate;
				if ($translateInstance instanceOf Zend_Translate) {
//					$translateInstance->getAdapter()->setLocale($locale);
				}
			}
		}
			
		$this->_helper->AjaxContext->initContext('json');
	}
	
}
?>
