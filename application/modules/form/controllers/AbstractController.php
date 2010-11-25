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
		
		$this->view->headTitle()->set('ZendConstructor: Forms');
		
		$this->view->headScript()
			->appendFile('/js/jquery/jquery-1.4.2.js')
			->appendFile('/js/jquery/jquery-ui.js')
//			->appendFile('http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js')
//			->appendFile('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/jquery-ui.min.js')
			->appendFile('/js/highlight/highlight.js')
			->appendFile('/js/highlight/php.js')
			->appendFile('/js/jquery/jquery.form.js')
			->appendFile('/js/uniform/jquery.uniform.min.js')
			->appendFile('/js/jquery/tmpl.js')
			->appendFile('/js/form.js')
			->appendFile('/js/html5.js', 'text/javascript', array('conditional' => 'lt IE 9'));
		
		$this->view->headLink()
			->appendStylesheet('/css/reset.css')
			->appendStylesheet('/css/jquery/jquery.ui.all.css')
			->appendStylesheet('/css/uniform/css/uniform.default.css')
			->appendStylesheet('/css/highlight/vs.css')
			->appendStylesheet('/css/style.css')
			->appendStylesheet('/css/style.css')
			->appendStylesheet('/css/ie.css', 'all', 'lt IE 9');
			
		$this->_helper->AjaxContext->initContext('json');
	}
	
}
?>
