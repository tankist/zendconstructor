<?php
class Skaya_Controller_Plugin_HeadScriptLoader extends Zend_Controller_Plugin_Abstract {
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		$config     = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOptions();
		$moduleName = $request->getModuleName();
		$view = Zend_Layout::getMvcInstance()->getView();
		
		$applicationConfig = (isset($config['plugin']['headscript']))?$config['plugin']['headscript']:array();
		$moduleConfig = (isset($config[$moduleName]['plugin']['headscript']))?$config[$moduleName]['plugin']['headscript']:array();
		
		$headScriptConfig = array_merge_recursive($applicationConfig, $moduleConfig);

		if (!empty($headScriptConfig['scripts']) && $view instanceOf Zend_View_Abstract) {
			$helper = $view->getHelper('headScript');
			foreach ($headScriptConfig['scripts'] as $filePath) {
				$helper->appendFile($filePath);
			}
		}
	}
}
?>