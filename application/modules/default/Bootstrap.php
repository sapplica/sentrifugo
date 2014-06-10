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
		//$autoloader->registerNamespace ( 'HTMLPurifier_' );
		$autoloader->registerNamespace ( 'Jqgrid_' );
		$autoloader->registerNamespace ( 'sapp_' );
		
		$this->options = $this->getOptions();
                //Zend_Registry::set('config.recaptcha', $this->options['recaptcha']);
		
		//Zend_Registry::set('config.services', $this->options['services']);		
    
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
                $serverUrl = (!empty($_SERVER['HTTP_HOST']))?$_SERVER['HTTP_HOST']:'';
                $serverArr = array('localhost','www.sentrifugo.com');
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
		
		
		/**
		 * facebook details
		 */
		Zend_Registry::set('fbappid','416698625055165');
		Zend_Registry::set('fbsecretkey','b00cf03b67e262904ca16054322fa889');
		
		/**
		 * twitter details
		 */
		Zend_Registry::set('consumerkey','2vnWV0Z2yoZcoVM9JkghQ');
		Zend_Registry::set('Consumersecret','3ZVxrXGck6ERaP5l3Fg3zgpEUJRV2pWb3ks1FoWGDxk');
		
		
		/**
		 * Email ids used for different scenarios
		 */
		
		Zend_Registry::set('notifications','notifications@stubstats.com');
		Zend_Registry::set('feedbacks','feedback@stubstats.com');
		Zend_Registry::set('donotreply','do-not-reply@stubstats.com');
		Zend_Registry::set('support','support@stubstats.com');
		
		Zend_Registry::set('logo_url','/public/images/landing_header.jpg');
		$view = new Zend_View ();
		$view->setEscape('stripslashes');
		$view->setBasePath ( $templatePath );		
		$view->setScriptPath ( APPLICATION_PATH );
		$view->addHelperPath ( 'ZendX/JQuery/View/Helper', 'ZendX_JQuery_View_Helper' );
		//$view->addHelperPath ( 'Helpers', 'Zend_View_Helper_Grid' );
		//$view->addHelperPath ( 'Helpers', 'Zend_View_Helper_AjaxJson' );	
		
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
		//$view->addHelperPath ('Helpers','Zend_View_Helper_Grid' );
		
	}

	
	protected function _initDbProfiler()
	{
	  //if ('production' !== $this->getEnvironment()) {
	        $this->bootstrap('db');
	        $profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
	        $profiler->setEnabled(true);
	        $this->getPluginResource('db')->getDbAdapter()->setProfiler($profiler);
	//  }
	} 
	public function _initFilter()
	{
		/*HTMLPurifier_Bootstrap::registerAutoload();
		$config = HTMLPurifier_Config::createDefault();
	    $config->set('Attr.EnableID',true);
	    $config->set('HTML.Strict',true);
	    Zend_Registry::set('purifier',new HTMLPurifier($config));*/
	}
	
	public function _initRoutes()
    {
 
        /*$frontController = Zend_Controller_Front::getInstance();
        $router = $frontController->getRouter();
 
        $route = new Zend_Controller_Router_Route(
            'tm/:name/:userid',
            array('module' => 'admin',
            	  'controller' => 'members',
                  'action' => 'timeline')
        );
 
        $router->addRoute('default-override', $route);*/
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
	
		$router->addRoute('login', $route); 
		$router->addRoute('welcome', $welcomeroute);
		//$router->addRoute('viewprofile', $viewprofileroute); 
		$router->addRoute('viewsettings', $viewsettingsroute);
		$router->addRoute('empleavesummary', $empleavesummaryroute);
		$router->addRoute('approvedrequisitions', $approvedrequisitionroute);
		$router->addRoute('shortlistedcandidates', $shortlistedroute);
		$router->addRoute('empscreening', $empscreeningroute);
                //$router->addRoute('error', $error_route);
		//$router->addRoute('changepassword', $changepasswordroute);
 
    }  
}

