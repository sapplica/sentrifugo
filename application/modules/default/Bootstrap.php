<?php
/********************************************************************************* 
 *  This file is part of Sentrifugo.
 *  Copyright (C) 2014 Sapplica
 *   
 *  Sentrifugo is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Sentrifugo is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with Sentrifugo.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  Sentrifugo Support <support@sentrifugo.com>
 ********************************************************************************/

class Default_Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

	protected function _initAppAutoload() {
		
		$autoloader = Zend_Loader_Autoloader::getInstance ();
		$autoloader->registerNamespace ( 'Ingot_' );
		$autoloader->registerNamespace ( 'ZendX_' );		
		$autoloader->registerNamespace ( 'Jqgrid_' );
		$autoloader->registerNamespace ( 'sapp_' );
		
		$this->options = $this->getOptions();                			
    
		return $autoloader;
	}

    protected function _initView()
    {
    	
		$theme = 'default';
		$templatePath  = APPLICATION_PATH . '/../public/themes/' . $theme . '/templates';
		Zend_Registry::set('user_date_format', 'm-d-Y');
		Zend_Registry::set('calendar_date_format', 'mm-dd-yy');
		Zend_Registry::set('db_date_format', 'Y-m-d');
		Zend_Registry::set('perpage', 10);
		Zend_Registry::set('menu', 'home');
		Zend_Registry::set('eventid', '');                
                
                $dir_name = $_SERVER['DOCUMENT_ROOT'].rtrim(str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']),'/');
                Zend_Registry::set('acess_file_path',$dir_name.SEPARATOR."application".SEPARATOR."modules".SEPARATOR."default".SEPARATOR."plugins".SEPARATOR."AccessControl.php");
                Zend_Registry::set('siteconstant_file_path',$dir_name.SEPARATOR."public".SEPARATOR."site_constants.php");
                Zend_Registry::set('emailconstant_file_path',$dir_name.SEPARATOR."public".SEPARATOR."email_constants.php");
                Zend_Registry::set('emptab_file_path',$dir_name.SEPARATOR."public".SEPARATOR."emptabconfigure.php");
                Zend_Registry::set('emailconfig_file_path',$dir_name.SEPARATOR."public".SEPARATOR."mail_settings_constants.php");
                Zend_Registry::set('application_file_path',$dir_name.SEPARATOR."public".SEPARATOR."application_constants.php");
                
		$date=new Zend_Date();
		Zend_Registry::set('currentdate', ($date->get('yyyy-MM-dd HH:mm:ss')));
		
		Zend_Registry::set('currenttime', ($date->get('HH:mm:ss')));								
		
		Zend_Registry::set('logo_url','/public/images/landing_header.jpg');
		$view = new Zend_View ();
		$view->setEscape('stripslashes');
		$view->setBasePath ( $templatePath );		
		$view->setScriptPath ( APPLICATION_PATH );
		$view->addHelperPath ( 'ZendX/JQuery/View/Helper', 'ZendX_JQuery_View_Helper' );				
		
		$viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer ();
		$viewRenderer->setView ( $view );

		Zend_Controller_Action_HelperBroker::addHelper ( $viewRenderer );
        return $this;
    }
    
	public function _initViewHelpers()
	{
		$this->bootstrap('layout');
		$layout = $this->getResource('layout');
		$view = $layout->getView();
		$view->doctype('HTML5');
		
		
	}

	
	protected function _initDbProfiler()
	{
	  
	        $this->bootstrap('db');
	        $profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
	        $profiler->setEnabled(true);
	        $this->getPluginResource('db')->getDbAdapter()->setProfiler($profiler);
	
	} 
	public function _initFilter()
	{		
	}
	
	public function _initRoutes()
       {
       
    	$router = Zend_Controller_Front::getInstance()->getRouter();

	
		$route = new Zend_Controller_Router_Route('login', array(
		'module' => 'default',
		'controller' => 'index',
		'action' => 'login'
		));
		
		$welcomeroute = new Zend_Controller_Router_Route('welcome', array(
		'module' => 'default',
		'controller' => 'index',
		'action' => 'welcome'
		));
		
		$viewprofileroute = new Zend_Controller_Router_Route('viewprofile', array(
		'module' => 'default',
		'controller' => 'dashboard',
		'action' => 'viewprofile'
		));
		
		$viewsettingsroute = new Zend_Controller_Router_Route('viewsettings/:tab', array(
			 'module' => 'default',
			 'controller' => 'dashboard',
			 'action' => 'viewsettings',
			));
			
		$changepasswordroute = new Zend_Controller_Router_Route('changepassword', array(
		'module' => 'default',
		'controller' => 'dashboard',
		'action' => 'changepassword'
		));	
		
        $empleavesummaryroute = new Zend_Controller_Router_Route('empleavesummary/:status', array(
			 'module' => 'default',
			 'controller' => 'empleavesummary',
			 'action' => 'index',
			));  

		$approvedrequisitionroute = new Zend_Controller_Router_Route('approvedrequisitions/:status', array(
			 'module' => 'default',
			 'controller' => 'approvedrequisitions',
			 'action' => 'index',
			));  
		
		$shortlistedroute = new Zend_Controller_Router_Route('shortlistedcandidates/:status', array(
			 'module' => 'default',
			 'controller' => 'shortlistedcandidates',
			 'action' => 'index',
			));  
	
		$empscreeningroute = new Zend_Controller_Router_Route('empscreening/con/:status', array(
			 'module' => 'default',
			 'controller' => 'empscreening',
			 'action' => 'index',
			));
                
         $error_route = new Zend_Controller_Router_Route('error', array(
			 'module' => 'default',
			 'controller' => 'error',
			 'action' => 'error',
			));
	
		/** route for policy documents **/
		$polidydocs_route = new Zend_Controller_Router_Route('policydocuments/id/:id/*',array(
				'module' => 'default',
				'controller' => 'policydocuments',
				'action' => 'index',
			));

		/** route for adding multiple policy documents **/
		$multiplepolidydocs_route = new Zend_Controller_Router_Route('policydocuments/addmultiple/:id',array(
				'module' => 'default',
				'controller' => 'policydocuments',
				'action' => 'addmultiple',
			));
			
		$myleavesroute = new Zend_Controller_Router_Route('pendingleaves/:flag', array(
			 'module' => 'default',
			 'controller' => 'pendingleaves',
			 'action' => 'index',
		));	
		$router->addRoute('login', $route); 
		$router->addRoute('welcome', $welcomeroute);		
		$router->addRoute('viewsettings', $viewsettingsroute);
		$router->addRoute('empleavesummary', $empleavesummaryroute);
		$router->addRoute('approvedrequisitions', $approvedrequisitionroute);
		$router->addRoute('shortlistedcandidates', $shortlistedroute);
		$router->addRoute('empscreening', $empscreeningroute);                		
		$router->addRoute('policydocuments',$polidydocs_route);
		$router->addRoute('multiplepolicydocuments',$multiplepolidydocs_route);
		$router->addRoute('myleaves',$myleavesroute);
    }  
}

