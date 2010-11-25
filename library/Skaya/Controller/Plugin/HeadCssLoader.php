<?php
class Skaya_Controller_Plugin_HeadCssLoader extends Zend_Controller_Plugin_Abstract {
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		$config     = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOptions();
		$moduleName = $request->getModuleName();
		$view = Zend_Layout::getMvcInstance()->getView();
		
		$applicationConfig = (isset($config['plugin']['headcss']))?$config['plugin']['headcss']:array();
		$moduleConfig = (isset($config[$moduleName]['plugin']['headcss']))?$config[$moduleName]['plugin']['headcss']:array();
		
		$headCssConfig = array_merge_recursive($applicationConfig, $moduleConfig);

		if (!empty($headCssConfig['stylesheets']) && $view instanceOf Zend_View_Abstract) {
			$helper = $view->getHelper('headLink');
			foreach ($headCssConfig['stylesheets'] as $filePath) {
				$helper->appendStylesheet($filePath);
			}
		}
	}
}
?>