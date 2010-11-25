<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {
	
	protected function _initModule() {
		$loader = new Zend_Application_Module_Autoloader(array(
			'namespace' => 'Skaya_',
			'basePath' => APPLICATION_PATH,
		));
		return $loader;
	}
	
	protected function _initAutoloadNamespace() {
		$this->getApplication()->getAutoloader()->registerNamespace(array('Skaya_'));
	}
}