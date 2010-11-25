<?php
class Skaya_Controller_Plugin_RouteLocale extends Zend_Controller_Plugin_Abstract {
	
	public function routeStartup(Zend_Controller_Request_Http $request) {
		$uri = $request->getRequestUri();
		$matchesCount = preg_match('$/(\w+)$i', $uri, $matches);
		if ($matchesCount > 0) {
			$locale = $matches[1];
			if (Zend_Locale::isLocale($locale)) {
				$request->setParam('locale', $locale);
				$uri = str_replace('/' . $locale, '', $uri);
				$request->setRequestUri($uri);
			}
		}
	}
	
}
?>
