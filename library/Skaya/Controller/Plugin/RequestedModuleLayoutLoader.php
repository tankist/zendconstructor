<?php
class Skaya_Controller_Plugin_RequestedModuleLayoutLoader extends Zend_Controller_Plugin_Abstract {
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		$config     = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOptions();
		$moduleName = $request->getModuleName();

		if (isset($config[$moduleName]['resources']['layout'])) {
			Zend_Layout::startMvc($config[$moduleName]['resources']['layout']);
		}
	}
}
?>