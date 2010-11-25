<?php
class Skaya_Controller_Plugin_Auth extends Zend_Controller_Plugin_Abstract {
	/**
	* Auth provider
	* 
	* @var Zend_Auth
	*/
	protected $_auth = array();
	
	/**
	* Acl provider
	* 
	* @var Zend_Acl
	*/
	protected $_acl = array();
	
	protected $_noauth = array();

	protected $_noacl = array();

	public function __construct(Zend_Auth $auth, Zend_Acl $acl, $module = 'default') {
		$this
			->setAuth($auth, $module)
			->setAcl($acl, $module);
			
		$defaultModuleName = Zend_Controller_Front::getInstance()->getDefaultModule();
		if ($module != $defaultModuleName) {
			$this->setNoAuthRules(array(
				'module' => 'default',
				'controller' => 'login',
				'action' => 'index'
			), $defaultModuleName);
			
			$this->setNoAclRules(array(
				'module' => 'default',
				'controller' => 'error',
				'action' => 'privileges'
			), $defaultModuleName);
		}
	}
	
	public function setAuth(Zend_Auth $auth, $module = 'default') {
		$this->_auth[$module] = $auth;
		return $this;
	}
	
	public function getAuth($module = 'default') {
		return $this->_auth[$module];
	}
	
	public function setAcl(Zend_Acl $acl, $module = 'default') {
		$this->_acl[$module] = $acl;
		return $this;
	}
	
	public function getAcl($module = 'default') {
		return $this->_acl[$module];
	}
	
	public function setNoAclRules(array $noAcl, $module = 'default') {
		$this->_noacl[$module] = $noAcl;
		return $this;
	}
	
	public function getNoAclRules($module = 'default') {
		return $this->_noacl[$module];
	}
	
	public function setNoAuthRules(array $noAuth, $module = 'default') {
		$this->_noauth[$module] = $noAuth;
		return $this;
	}
	
	public function getNoAuthRules($module = 'default') {
		return $this->_noauth[$module];
	}
	
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		$controller = $request->getControllerName();
		$action = $request->getActionName();
		$module = (is_string($request->getModuleName()))?strtolower($request->getModuleName()):'default';
		
		$auth = $this->getAuth($module);
		$acl = $this->getAcl($module);
		
		if (!$auth || !$acl) {
			return false;
		}
		
		$role = ($auth->hasIdentity())?$auth->getIdentity()->getRole():Model_Store::STORE_ROLE_GUEST;
		$resource = $controller;
	
		if (!$acl->has($resource)) {
			$resource = null;
		}

		if (!$acl->isAllowed($role, $resource, $action)) {
			$redirectType = (!$auth->hasIdentity())?'noauth':'noacl';
			$this->_setRequestParameters($redirectType);
			$request->setDispatched(false);
		}
	}
	
	protected function _setRequestParameters($noType) {
		$moduleName = strtolower($this->getRequest()->getModuleName());
		$noType = '_'.strtolower($noType);
		if (property_exists($this, $noType)) {
			$noRules = $this->$noType;
			if (!array_key_exists($moduleName, $noRules)) {
				$moduleName = Zend_Controller_Front::getInstance()->getDefaultModule();
				if (!array_key_exists($moduleName, $noRules)) {
					throw new Zend_Controller_Exception('Unknown redirect params for the current module');
				}
			}
			$noRules = $noRules[$moduleName];
			foreach ($noRules as $actionName => $rule) {
				$actionName = "set" . ucwords($actionName) . "Name";
				if (method_exists($this->getRequest(), $actionName)) {
					call_user_func(array($this->getRequest(), $actionName), $rule);
				}
			}
		}
		else {
			throw new Zend_Controller_Exception('Wrong redirect type provided. Only NoAcl and NoAuth are supported');
		}
		return $this;
	}
}
?>
