<?php
class Skaya_Controller_Action_Helper_XmlContextSwitch extends Zend_Controller_Action_Helper_ContextSwitch {
	
	protected $_autoXmlSerialization = true;
	
	protected $_xmlRootName = 'rest-response';
	
	protected $_replaceNumericKeys = array();
	
	public function __construct($options = null) {
		parent::__construct($options);
		
		$xmlCallbacks = array(
			'init' => 'initXmlContext',
			'post' => 'postXmlContext'
		);
		
		if ($this->hasContext('xml')) {
			foreach ($this->getCallbacks('xml') as $trigger => $callback) {
				$xmlCallbacks[$trigger] = $callback;
			}
		}
		
		$this->setCallbacks('xml', $xmlCallbacks);
	}
	
	public function setReplaceNumericKeys($replaceNumericKeys = array()) {
		$this->_replaceNumericKeys = $replaceNumericKeys;
		return $this;
	}
	
	public function getReplaceNumericKeys() {
		return $this->_replaceNumericKeys;
	}
	
	public function setXmlRootName($xmlRootname) {
		$this->_xmlRootName = $xmlRootname;
		return $this;
	}
	
	public function getXmlRootName() {
		return $this->_xmlRootName;
	}
	
	public function setAutoXmlSerialization($autoSerialization) {
		$this->_autoXmlSerialization = (boolean)$autoSerialization;
		return $this;
	}
	
	public function getAutoXmlSerialization() {
		return $this->_autoXmlSerialization;
	}
	
	public function initXmlContext() {
		if (!$this->getAutoXmlSerialization()) {
			return;
		}

		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		$view = $viewRenderer->view;
		if ($view instanceof Zend_View_Interface) {
			$viewRenderer->setNoRender(true);
		}
	}
	
	public function postXmlContext() {
		if (!$this->getAutoXmlSerialization()) {
			return;
		}

		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		$view = $viewRenderer->view;
		if ($view instanceof Zend_View_Interface) {
			if(method_exists($view, 'getVars')) {
				$this->getResponse()->setBody($this->_renderXml($view->getVars()));
			} else {
				require_once 'Zend/Controller/Action/Exception.php';
				throw new Zend_Controller_Action_Exception('View does not implement the getVars() method needed to encode the view into XML');
			}
		}
	}
	
	protected function _renderXml($data) {
		$writer = new XMLWriter();
		$writer->openMemory();
		$writer->setIndent(false);
		$writer->startDocument('1.0', 'UTF-8');
		$rootName = $this->getXmlRootName();
		$firstKey = key($data);
		if (is_array($data) && count($data) == 1 && !is_numeric($firstKey)) {
			$rootName = $firstKey;
			$data = $data[$firstKey];
		}
		$writer->startElement($rootName);
		$this->_fromArray($writer, $data, $rootName, $this->getReplaceNumericKeys());
		$writer->endElement();
		$writer->endDocument();
		return $writer->outputMemory();
	}
	
	protected function _fromArray(XMLWriter $writer, $data, $elementName, $replaceNumericKeys = array()) {
		if (is_array($data)) {
			foreach ($data as $key => $value) {
				if (is_numeric($key)) {
					$key = (isset($replaceNumericKeys[$elementName]) && 
							!is_numeric($replaceNumericKeys[$elementName]))?$replaceNumericKeys[$elementName]:'key_' . $key;
				}
				$writer->startElement($key);
				if (is_array($value)) {
					$this->_fromArray($writer, $value, $key, $replaceNumericKeys);
				}
				elseif (is_scalar($value)) {
					$writer->text($value);
				}
				$writer->endElement();
			}
		}
	}
	
}
?>
