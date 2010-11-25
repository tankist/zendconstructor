<?php
class Form_Bootstrap extends Zend_Application_Module_Bootstrap {
	
	protected function _initErrorHandler() {
		$this->bootstrap('frontcontroller');
		$front = Zend_Controller_Front::getInstance();
		$errorOptions = array(
			'module' => 'admin',
			'controller' => 'error',
			'action' => 'error'
		);
		if (!$errorPlugin = $front->getPlugin('Zend_Controller_Plugin_ErrorHandler')) {
			$errorPlugin = new Zend_Controller_Plugin_ErrorHandler($errorOptions);
			$front->registerPlugin($errorPlugin);
		}
		else {
			$errorPlugin->setErrorHandler($errorOptions);
		}
	}
	
}
?>
