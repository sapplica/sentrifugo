<?php

class Exit_Bootstrap extends Zend_Application_Module_Bootstrap
{
	protected function _initAppAutoload() {

		$auth= Zend_Auth::getInstance();
		$storage = $auth->getStorage()->read();
	}
}

