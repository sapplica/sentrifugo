<?php

class Expenses_Bootstrap extends Zend_Application_Module_Bootstrap
{
	protected function _initAppAutoload() {

		$auth= Zend_Auth::getInstance();
		$storage = $auth->getStorage()->read();
		/* if(isset($storage)) {
			$usersModel = new Timemanagement_Model_Users();
			$tmRole = $usersModel->getUserTimemanagementRole($storage->id);
			Zend_Registry::set('tm_role', $tmRole);
		} */
	}


	/** URL Masking */
	public function _initRoute()
	{
		$router = Zend_Controller_Front::getInstance()->getRouter();

		/* $route = new Zend_Controller_Router_Route('resources/:projectid', array(
		'module' => 'timemanagement',
		'controller' => 'projectresources',
		'action' => 'resources',
		));
		$router->addRoute('resources', $route);
		
		$route = new Zend_Controller_Router_Route('tasks/:projectid', array(
		'module' => 'timemanagement',
		'controller' => 'projects',
		'action' => 'tasks',
		));
		$router->addRoute('tasks', $route);
		
		$route = new Zend_Controller_Router_Route('monthview/:selYrMon/:flag', array(
		'module' => 'timemanagement',
		'controller' => 'index',
		'action' => 'index',
		));
		$router->addRoute('monthview', $route);
		
		$route = new Zend_Controller_Router_Route('weekview/:selYrMon/:week/:calWeek/:flag/:day', array(
		'module' => 'timemanagement',
		'controller' => 'index',
		'action' => 'week',
		'calWeek' => '',
		'flag' => '',
		'day' => '',		
		));
		$router->addRoute('weekview', $route);

		$route = new Zend_Controller_Router_Route('timeentry/:selYrMon/:week/:calWeek/:flag', array(
		'module' => 'timemanagement',
		'controller' => 'index',
		'action' => 'week',	
		'selYrMon' => '',
		'week' => '',
		'calWeek' => '',
		'flag' => 'time',
		));
		$router->addRoute('timeentry', $route);	
		$route = new Zend_Controller_Router_Route('timeentryday/:selYrMon/:flag/:day', array(
		'module' => 'timemanagement',
		'controller' => 'index',
		'action' => 'week',
		));
		$router->addRoute('timeentryday', $route);	 */
		
		
	}
}

