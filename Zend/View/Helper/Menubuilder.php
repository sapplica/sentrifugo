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

/**
 * Menubuilder View Helper
 *
 * A View Helper that creates the menu
 *
 *
 */

class Zend_View_Helper_Menubuilder extends Zend_View_Helper_Abstract {

	public $privelegesMenuIdsStr = '';
	public $empRole = '';
	public $class_name = '';
	public $actionData = '';
	public $strMenuName = '';
	public $src = '';
	public $menu_name = '';
	public $tmpMenuDataArr = array();
	public $view = null;

	public $extra = array();

	private $output; // Container to hold the Grid

	public function setView(Zend_View_Interface $view)   
        {
            $this->view = $view;
            return $this;
	}

	/**
	**	1. gets menudata from database
	**	2. prepares the first and second level menuids array
	**	3. builds the menu tree html
	**	4. con = settings for settings menu
	**/
 	public function menubuilder($con="",$userRole = 2,$flag = "",$usergroup)
	{        			
		$uploadPath = BASE_URL.'public/media/images/menuIcons/';
		
		$menuheightclass= '';
		$groupbasedclass = '';
		$ulclass = '';
		
		if($usergroup =='' || $usergroup == MANAGEMENT_GROUP)
		{
			$menuheightclass = '';
			$groupbasedclass = '';
			
		}else
		{
			$menuheightclass = 'menuheight';
			$groupbasedclass = '-common';
			$ulclass = 'new-roles'; 
		}	
			
		$menu_model = new Default_Model_Menu();
		$marr = $menu_model->getgroup_formenu($usergroup, $userRole, '');
		$active_menus = $menu_model->getisactivemenus();
		$act_menus = array();
		if(!empty($active_menus))
		{
			foreach($active_menus as $act)
			{
				$act_menus[$act['id']] = $act;
			}
		}
		$menuarr = $marr['tmpArr'];
       /*** removing icons in the menu 07-08-2015 - START - ***
	   
	   $parent_menu_class = array(SITECONFIGURATION => 'site-configuration',EMPLOYEECONFIGURATION => "employee-configuration",
                                            SITEPREFERENCE => "site-preferences",ORGANIZATION => "organization-menu",
                                            USERMANAGEMENT => "user-management",HUMANRESOURCE => "hr-module",RESOURCEREQUISITION => "req-module",
                                            EMPLOYEESELFSERVICE => "ess-module",BGCHECKS => "bg-module",
                                            REPORTS => "report-module",MANAGEMODULE => "manage-module",SERVICEDESK => 'service-desk',
                                            PERFORMANCEAPPRAISAL => "perf-app",FEED_FORWARD => "feed-forward",DASHBOARD_MENU => "dashboard",ANNOUNCEMENTS => ""    
                    );
		$parent_menu_selected_class = array(
                                                    SITECONFIGURATION => 'sitec-selected',EMPLOYEECONFIGURATION => "employeec-selected",
                                                    SITEPREFERENCE => "sitep-selected",ORGANIZATION => "organization-selected",
                                                    USERMANAGEMENT => "user-selected",HUMANRESOURCE => "hr-selected",RESOURCEREQUISITION => "req-selected",
                                                    EMPLOYEESELFSERVICE => "ess-selected",BGCHECKS => "bg-selected",
                                                    REPORTS => "report-selected",MANAGEMODULE => "manage-selected",
                                                    SERVICEDESK => 'service-selected',PERFORMANCEAPPRAISAL => "perf-app-selected",FEED_FORWARD => 'feed-forward-selected',DASHBOARD_MENU => "dashboard-selected",ANNOUNCEMENTS => ""     
			);
			
		$tour_menu_class = array(SITECONFIGURATION => 'tour_siteconfiguration',
                                         EMPLOYEECONFIGURATION => "tour_employeeconfigurations",
                                         SITEPREFERENCE => "tour_sitepreferences",ORGANIZATION => "tour_organization",
                                         USERMANAGEMENT => "tour_usermanagement",HUMANRESOURCE => "tour_humanresource",
                                         RESOURCEREQUISITION => "tour_requisition",EMPLOYEESELFSERVICE => "tour_employeeselfservice",
                                         BGCHECKS => "tour_backgroundchecks",REPORTS => "tour_reports",
                                        MANAGEMODULE =>"tour_managemodules",SERVICEDESK => 'tour_service',PERFORMANCEAPPRAISAL => "tour_performanceappraisal",
                                        FEED_FORWARD => 'tour_feedforward',DASHBOARD_MENU => "tour_dashboard" ,ANNOUNCEMENTS => "tour_dashboard" 
                    );

		 ******* removing icons in the menu 07-08-2015 - END - ***/
		
		$parent_menu_class = array(SITECONFIGURATION => '',EMPLOYEECONFIGURATION => "",
                                            SITEPREFERENCE => "",ORGANIZATION => "",
                                            USERMANAGEMENT => "",HUMANRESOURCE => "",RESOURCEREQUISITION => "",
                                            EMPLOYEESELFSERVICE => "",BGCHECKS => "",
                                            REPORTS => "",MANAGEMODULE => "",SERVICEDESK => '',
                                            PERFORMANCEAPPRAISAL => "",FEED_FORWARD => "",DASHBOARD_MENU => "",ANNOUNCEMENTS => "",TIMEMANAGEMENT => "",
                                            EXPENSES=>"", ASSETS =>"", DISCIPLINARY=>""
                    );
		$parent_menu_selected_class = array(
                                                    SITECONFIGURATION => '',EMPLOYEECONFIGURATION => "",
                                                    SITEPREFERENCE => "",ORGANIZATION => "",
                                                    USERMANAGEMENT => "",HUMANRESOURCE => "",RESOURCEREQUISITION => "",
                                                    EMPLOYEESELFSERVICE => "",BGCHECKS => "",
                                                    REPORTS => "",MANAGEMODULE => "",
                                                    SERVICEDESK => '',PERFORMANCEAPPRAISAL => "",FEED_FORWARD => '',DASHBOARD_MENU => "",ANNOUNCEMENTS => "",TIMEMANAGEMENT => "",EXPENSES => "", 
                                                    ASSETS =>"", DISCIPLINARY=>""    
			);
			
		$tour_menu_class = array(SITECONFIGURATION => 'tour_siteconfiguration',
                                         EMPLOYEECONFIGURATION => "tour_employeeconfigurations",
                                         SITEPREFERENCE => "tour_sitepreferences",ORGANIZATION => "tour_organization",
                                         USERMANAGEMENT => "tour_usermanagement",HUMANRESOURCE => "tour_humanresource",
                                         RESOURCEREQUISITION => "tour_requisition",EMPLOYEESELFSERVICE => "tour_employeeselfservice",
                                         BGCHECKS => "tour_backgroundchecks",REPORTS => "tour_reports",
                                        MANAGEMODULE =>"tour_managemodules",SERVICEDESK => 'tour_service',PERFORMANCEAPPRAISAL => "tour_performanceappraisal",
                                        FEED_FORWARD => 'tour_feedforward',DASHBOARD_MENU => "tour_dashboard" ,ANNOUNCEMENTS => "tour_dashboard",
                                        TIMEMANAGEMENT => "tour_timemanagement" ,EXPENSES=>"tour_expenses", 
                                        ASSETS=>"tour_assets", DISCIPLINARY=>"tour_disciplinary"
                    );

		$childs_menu = "";
		$parent_menu = "<div class='home_menu'><div role='main' class='menu-head main".$menuheightclass."' id='menu-shadow' style='display:none;'>
				<ul id='main_ul' class='menu flex'>";
		$clas_drag = ($con == 'settings')? ' draggable-reports-li ': ''; // Add draggable class for dashbord 
		
		/*** commented to remove menu icon 07-08-2015 - START - ***
		$parent_menu .= "<li id='main_parent_".DASHBOARD_MENU."' super-parent = 'main_parent_".DASHBOARD_MENU."' class = '".$clas_drag."clickable_menu ".$parent_menu_selected_class[DASHBOARD_MENU]."-main ".$tour_menu_class[DASHBOARD_MENU]."' menu-url ='".BASE_URL."welcome' selected-class = '".$parent_menu_selected_class[DASHBOARD_MENU]."' > <a id='".DASHBOARD_MENU."' ><span class='scroll-menu dashboard dashboard-selected-common super_selected'></span><b>Dashboard</b></a></li>";
		*** commented to remove menu icon 07-08-2015 - END - ***/
		$parent_menu .= "<li id='main_parent_".DASHBOARD_MENU."' super-parent = 'main_parent_".DASHBOARD_MENU."' class = '".$clas_drag."clickable_menu ".$parent_menu_selected_class[DASHBOARD_MENU]."-main ".$tour_menu_class[DASHBOARD_MENU]."' menu-url ='".BASE_URL."welcome' selected-class = '".$parent_menu_selected_class[DASHBOARD_MENU]."' > <a id='".DASHBOARD_MENU."' ><b>Dashboard</b></a></li>";
		
		foreach($menuarr as $menuid => $data)
		{			
			
                    $for_childs_str = "";
                    $parent_url = rtrim(BASE_URL,"/").$data['url'];

					if(isset($data['childs']) && count($data['childs']) > 0)
                    {			   
                        $for_childs_str = "div_mchilds_".$menuid;
                        $parent_url = "";
                        $childs_menu .= "<div class='side-menu ".$for_childs_str."' style='display:none;'>
									<ul>";
                        foreach($data['childs'] as $ch_menu_id => $ch_menu_data)
                        {
                        	if(isset($ch_menu_data['childs']) && count($ch_menu_data['childs']) > 0)
                            {
                                $childs_menu .= "<li class='acc_li'><span class='acc_li_toggle' id='acc_li_toggle_".$ch_menu_data['id']."' onclick='togglesubmenus(".$ch_menu_data['id'].");'><b>".$ch_menu_data['menuName']."</b></span><ul>";
								
                                foreach($ch_menu_data['childs'] as $ch2_menu_id => $ch2_menu_data)
                                {
									if($con == 'settings')
                                    {
										if($ch2_menu_id == MANAGE_POLICY_DOCS)
										{
											/** policy document categories is not built as menu items 
											** for settings page
											**/
											
										}
										else
										{
											$childs_menu .= "<li class = 'clickable_menu' super-parent = 'main_parent_".$menuid."' menu-url = '".rtrim(BASE_URL,"/").$ch2_menu_data['url']."' parent-div = '".$for_childs_str."'><a id=".$ch2_menu_data['id']."		href='javascript:void(0);'>".$ch2_menu_data['menuName']."</a></li>";
										}
                                    }
									else if($ch2_menu_id == MANAGE_POLICY_DOCS)
									{
										/** to build policy document categories as menu items **/
										$childs_menu .= sapp_Helper::viewPolicyDocuments('menu');
									}
                                    else
                                    {
                                        $childs_menu .= "<li class = 'clickable_menu' primary_parent = '".$ch2_menu_data['parent']."' super-parent = 'main_parent_".$menuid."' menu-url = '".rtrim(BASE_URL,"/").$ch2_menu_data['url']."' parent-div = '".$for_childs_str."'><a id=".$ch2_menu_data['id']." href='".rtrim(BASE_URL,"/").$ch2_menu_data['url']."/'>".$ch2_menu_data['menuName']."</a></li>";
                                        
                                    }
                                }
                                $childs_menu .= "</ul></li>";
                            }
                            else 
                            {
								if($con == 'settings')
                                {
									if($menuid == SERVICEDESK  && $ch_menu_id == SD_TRANS)
                                    {     
                                        $storage = new Zend_Auth_Storage_Session();
                                        $sess_data = $storage->read();
                                        $childs_menu .= sapp_Helper::service_header($sess_data, 'menusettings');
                                    } 
									else if($menuid == MANAGE_POLICY_DOCS)
									{
										/** policy document categories is not built as menu items 
										** for settings page
										**/
									}
                                    else
                                    {
                                        $childs_menu .= "<li class = 'clickable_menu single-menu' super-parent = 'main_parent_".$menuid."' menu-url = '".rtrim(BASE_URL,"/").$ch_menu_data['url']."' parent-div = '".$for_childs_str."'><a id=".$ch_menu_data['id']." href='javascript:void(0);'>".$ch_menu_data['menuName']."</a></li>";
                                    }
                                }
                                else
                                {
                                    if($menuid == SERVICEDESK  && $ch_menu_id == SD_TRANS)
                                    {     
                                        $storage = new Zend_Auth_Storage_Session();
                                        $sess_data = $storage->read();
                                        $childs_menu .= sapp_Helper::service_header($sess_data, 'menu');
                                    } 
									else if($menuid == MANAGE_POLICY_DOCS)
									{
										/** to build policy document categories as menu items **/
										$childs_menu .= sapp_Helper::viewPolicyDocuments('menu');
									}
                                    else
                                    {
                                    	$condition = ($ch_menu_data['id'] == 168)? "<span class='beta_menu'></span>" : "";
                                        $childs_menu .= "<li class = 'clickable_menu single-menu' super-parent = 'main_parent_".$menuid."' menu-url = '".rtrim(BASE_URL,"/").$ch_menu_data['url']."' parent-div = '".$for_childs_str."'><a id=".$ch_menu_data['id']." href='".rtrim(BASE_URL,"/").$ch_menu_data['url']."/'>".$ch_menu_data['menuName'].$condition."</a></li>";
                                    }
									
                                }
                            }
                        }                                
                        $childs_menu .= "</ul></div>";
                    }
                        
                    $super_str = "";$dummy_parent_div = "";
                    if($menuid == SITEPREFERENCE)
                    {
                        $super_str = " super-parent = 'main_parent_".$menuid."' ";
                        $dummy_parent_div = "parent-div = 'dummy'";
                    }
                        
                    if($con == 'settings')
                    {
                        $parent_menu .= "<li id='main_parent_".$menuid."' ".$dummy_parent_div." ".$super_str." super-parent = 'main_parent_".$menuid."' class = 'draggable-reports-li clickable_menu ".$parent_menu_selected_class[$menuid].($groupbasedclass!=''?"-main-common ":"-main ").$tour_menu_class[$menuid]."' menu-url ='".$parent_url."' for-childs = '".$for_childs_str."' selected-class = '".(($groupbasedclass!='')?$parent_menu_selected_class[$menuid]."-common":$parent_menu_selected_class[$menuid])."'><a id='".$menuid."'><span class='scroll-menu ".(($groupbasedclass!='')?$parent_menu_class[$menuid]."-common":$parent_menu_class[$menuid])."'></span><b>".$data['menuName']."</b></a></li>";
                    }
                    else 
                    {
						// && $menuid != EXPENSES
						if($menuid != TIMEMANAGEMENT)
						{
							$groupclass= "";
							$parent_menu .= "<li id='main_parent_".$menuid."' ".$dummy_parent_div." ".$super_str." super-parent = 'main_parent_".$menuid."' class = 'clickable_menu ".$parent_menu_selected_class[$menuid].($groupbasedclass!=''?"-main-common ":"-main ").$tour_menu_class[$menuid]."' menu-url ='".$parent_url."' for-childs = '".$for_childs_str."' selected-class = '".(($groupbasedclass!='')?$parent_menu_selected_class[$menuid]."-common":$parent_menu_selected_class[$menuid])."'><a id='".$menuid."'><span class='scroll-menu ".(($groupbasedclass!='')?$parent_menu_class[$menuid]."-common":$parent_menu_class[$menuid])."'></span><b>".$data['menuName']."</b></a></li>";
						}
						
                    }
                   			                            			
		}//end of for loop
		
		//for time management
		if(isset($act_menus[TIMEMANAGEMENT]) && $act_menus[TIMEMANAGEMENT]['isactive'] == 1 && $usergroup!=USERS_GROUP)
		{
			$parent_menu .= "<li id='main_parent_".TIMEMANAGEMENT."' super-parent = 'main_parent_".TIMEMANAGEMENT."' class = '".$clas_drag."clickable_menu ".$parent_menu_selected_class[TIMEMANAGEMENT]."-main ".$tour_menu_class[TIMEMANAGEMENT]."' menu-url ='".BASE_URL."timemanagement' selected-class = '".$parent_menu_selected_class[TIMEMANAGEMENT]."' > <a id='".TIMEMANAGEMENT."' ><b>".$act_menus[TIMEMANAGEMENT]['menuName']."</b></a></li>";
		}
		
		
		/*if(isset($act_menus[EXPENSES]) && $act_menus[EXPENSES]['isactive'] == 1 && $usergroup!=USERS_GROUP)
		{
			$parent_menu .= "<li id='main_parent_".EXPENSES."' super-parent = 'main_parent_".EXPENSES."' class = '".$clas_drag."clickable_menu ".$parent_menu_selected_class[EXPENSES]."-main ".$tour_menu_class[EXPENSES]."' menu-url ='".BASE_URL."expenses' selected-class = '".$parent_menu_selected_class[EXPENSES]."' > <a id='".EXPENSES."' ><b>".$act_menus[EXPENSES]['menuName']."</b></a></li>";
		}*/
		
		
	/*	if(isset($act_menus[ASSETS]) && $act_menus[ASSETS]['isactive'] == 1 && $usergroup!=USERS_GROUP)
		{
			$parent_menu .= "<li id='main_parent_".ASSETS."' super-parent = 'main_parent_".ASSETS."' class = '".$clas_drag."clickable_menu ".$parent_menu_selected_class[ASSETS]."-main ".$tour_menu_class[ASSETS]."' menu-url ='".BASE_URL."assets/assets' selected-class = '".$parent_menu_selected_class[ASSETS]."' > <a id='".ASSETS."' ><b>".$act_menus[ASSETS]['menuName']."</b></a></li>";
		}*/
		
		
		if(($userRole == SUPERADMIN || $usergroup == MANAGEMENT_GROUP) && $con != 'settings')
		{		
             /*** removing icon for Logs menu item 17-08-2015 - START ---  ****
			 $parent_menu .= "<li selected-class='log-selected' id='main_parent_logs'  class='clickable_menu log-selected-main tour_logs' menu-url='' for-childs = 'div_mchilds_logs'><span class='scroll-menu log-module'></span><b>Logs</b></li>";
            **** END ****/
			 $parent_menu .= "<li selected-class='log-selected' id='main_parent_logs'  class='clickable_menu log-selected-main tour_logs' menu-url='' for-childs = 'div_mchilds_logs'><b>Logs</b></li>";

			/**
			 * 
			 * Logs links are static so <a> tags are required for these static links. 
			 * Remaingin links are dynamic (Exists in database)
			 * @var String - HTML markup
			 */
            $childs_menu .= "<div class='side-menu div_mchilds_logs' style='display:none;'>
								<ul>
							   
								<li super-parent = 'main_parent_logs' parent-div='div_mchilds_logs' class = 'clickable_menu single-menu' menu-url = '".BASE_URL."logmanager'><a href='".BASE_URL."logmanager'>Activity Log</a></li>
								<li super-parent = 'main_parent_logs' parent-div='div_mchilds_logs' class = 'clickable_menu single-menu' menu-url = '".BASE_URL."userloginlog'><a href='".BASE_URL."userloginlog'>User Log</a></li>
								
								</ul>
							  </div>";
		}
		$parent_menu .= " </ul>
			 </div></div>
		";				             
		
			return array('parent_menu' => $parent_menu,'childs_menu' => $childs_menu);
            
	}
	/**
	** prepares the menu data for the first, second and third level menu items
	** based on the child items array count, the arrow class and menu name length are determined
	**/
	public  function prepareMenuData($keysArrSize,$menuId,$level)
	{		
		$uploadPath = DOMAIN.'public/media/images/menuIcons/';	
		$menu_id = $this->tmpMenuDataArr[$menuId]['id'];
		$menu_name = $this->tmpMenuDataArr[$menuId]['menuName'];
		$ParentId = $this->tmpMenuDataArr[$menuId]['parent'];
		$menu_link = BASE_URL.$this->tmpMenuDataArr[$menuId]['url'];
		$menu_icon = $this->tmpMenuDataArr[$menuId]['iconPath'];
		
		if($menu_icon != '')
			$src = $uploadPath.$menu_icon;
		else
			$src = DOMAIN.'public/media/images/home_icon.png';

		$class_name = '';
		if($keysArrSize > 0)
			$class_name = 'menutag parentmenu';
		else
			$class_name = 'childelement menutag';				
	
		/* To remove href and put onclick javascript void for parent menus */
		$actionData = '';
		if($menu_link == BASE_URL.'#') 
			$actionData = "onclick = 'javascript:void(0);'";
		else
			$actionData = "href='".$menu_link."'";	

		if($level == 1)
		{
			if($keysArrSize == 0) //Lines 137 to 142 added - Display of menu name with 30 characters, if the menu doesn't have children
			{
				if(strlen($menu_name) > 31)
				$strMenuName =  substr($menu_name,0,31)."...";
				else $strMenuName =  $menu_name;
			}
			else if(strlen($menu_name) > 21)
				$strMenuName =  substr($menu_name,0,21)."...";
			else
				$strMenuName =  $menu_name;
			
		}
		else if($keysArrSize == 0)
		{
			if(strlen($menu_name) > 33)
				$strMenuName =  substr($menu_name,0,33)."...";
			else
				$strMenuName =  $menu_name;
			
		}
		else
		{
			if(strlen($menu_name) > 23)
				$strMenuName =  substr($menu_name,0,23)."...";				
			else
				$strMenuName =  $menu_name;
			
		}				
		$this->class_name = $class_name;
		$this->actionData = $actionData;
		$this->strMenuName = $strMenuName;
		$this->src = $src;
		$this->menu_name = $menu_name;
	}
	public  function getMenuIdsByRole($userRole)
	{            
		$privelegesMenuIds = array();
		$selectQuery = "select p.object from main_privileges p where p.isactive =1 and p.role = ".$userRole;
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = $db->query($selectQuery);
		$result = $sql->fetchAll();
		$tmpArr = array();
		foreach ($result as $sub) {
		  $tmpArr[] = implode(',', $sub);
		}
		$resultstring = implode(',', $tmpArr);
		if($resultstring)
		{
		    try
                    {
                        $qry = "select ob.id, ob.nav_ids from main_menu ob 
                                        where ob.id IN (".$resultstring.") or ob.nav_ids IN (".$resultstring.") and isactive = 1";		
                        $db = Zend_Db_Table::getDefaultAdapter();
                        $sqlRes = $db->query($qry);
                        $navRes = $sqlRes->fetchAll();
                        if(!empty($navRes))
                        {
                            for($n = 0; $n < sizeof($navRes); $n++)
                            {
                                $navIdsArr[] = trim($navRes[$n]['nav_ids'],',');
                            }
                        }
                    }
                    catch(Exception $e)
                    {
                        echo "Error Encountered - ".$e->getMessage();
                    }
                    if(!empty($navIdsArr))
                    {
                        $tmpStr = implode(",",$navIdsArr);
                        $tmpArr = array_unique (explode(",",$tmpStr));
                        $this->privelegesMenuIdsStr = implode(",",$tmpArr);
                    }
		}		
		else
		{
			$this->privelegesMenuIdsStr ="";
		}
	}
	public  function getShortcutIcons($userId)
	{
		$settingsModel = new Default_Model_Settings();
		
		$iconidcount = $settingsModel->getActiveiconCount($userId);		
		$shortcutsStr = '<ul>';
		if($iconidcount[0]['count'] > 0)
		{
			$menuIdsArr = $settingsModel->getMenuIds($userId,2);
			
			if(!empty($menuIdsArr) === true)
			{
				$menuIdsStr = $menuIdsArr[0]['menuid'];				
				$menuDetailsArr = $settingsModel->getMenuName($menuIdsStr);				
				$size = (sizeof($menuDetailsArr) > 16) ? 16: sizeof($menuDetailsArr);
				for($s = 0; $s < $size; $s++)
				{
					$menuName = $menuDetailsArr[$s]['menuName'];
					$tmpMenuUrl = $menuDetailsArr[$s]['url'];				

					if($tmpMenuUrl && (strpos($tmpMenuUrl,'http://') === false || strpos($tmpMenuUrl,'http://') === false  || strpos($tmpMenuUrl,'http://') === false))
					{
						$menuUrl = BASE_URL.substr($tmpMenuUrl,1,strlen($tmpMenuUrl));
					}
					else if(strpos($tmpMenuUrl,'http://') === true || strpos($tmpMenuUrl,'http://') === true  || strpos($tmpMenuUrl,'http://') === true)
						$menuUrl = $tmpMenuUrl;
					else
						$menuUrl = 'javascript:void(0);';
						
                    if($menuDetailsArr[$s]['iconPath'] !='') 
					  $menuIcon = 'images/menuIcons/shortcuts/'.$menuDetailsArr[$s]['iconPath'];
                    else
                      $menuIcon = 'images/sampleimg.png';	

                    
					$shortcutsStr.='<li><a href="'.$menuUrl.'"><img src="'.MEDIA_PATH.''.$menuIcon.'"  height="33" width="33" border="0" /></a></li>';
				}										

				echo $shortcutsStr.='</ul>';

			}
			else
			{
				echo 'You have not configured your shortcuts. <a href="'.BASE_URL.'viewsettings/2">Click here</a> to configure.';
			}
		}
		else
		{
			echo 'You have not configured your shortcuts. <a href="'.BASE_URL.'viewsettings/2">Click here</a> to configure.';
		}
		
	}
	public  function getBreadCrumbsData($baseUrlString = '')
	{		
		$pageUrl = explode("/",$_SERVER['REQUEST_URI']); 
		$pageName = $pageUrl[2];			
		
		if($pageName == '')
		{
			$breadCrumbsData .= '';
		}
		else if($pageName == 'dashboard')
		{
			$breadCrumbsData = '<div class="breadcrumbs">';	
			
			if($pageUrl[3] == 'viewsettings')
			$breadCrumbsData .= '<a href="'.$baseUrlString.'">Home</a> <span class="arrows">&rsaquo;</span> Settings';
			else if($pageUrl[3] == 'viewprofile')
			$breadCrumbsData .= '<a href="'.$baseUrlString.'">Home</a> <span class="arrows">&rsaquo;</span> Profile';
			else if($pageUrl[3] == 'changepassword')
			$breadCrumbsData .= '<a href="'.$baseUrlString.'">Home</a> <span class="arrows">&rsaquo;</span> Change Password';			
			
			$breadCrumbsData .='</div>';
		}
		else
		{	
			$breadCrumbsData = '<div class="breadcrumbs">';		
			$url = "/".$pageName;
			$breadCrumIds = $this->getBreadCrumDetails($url);
			if(!empty($breadCrumIds))
			{
				$breadcrumstring = trim($breadCrumIds[0]['nav_ids'], ',');
				$breadcrumArr = explode(",",$breadcrumstring);
				for($i=0;$i<sizeof($breadcrumArr);$i++)
				{
				   $breadCrumNames[] = $this->getBreadCrumNames($breadcrumArr[$i]);		  
				}
				$breadCrumbsData .= '<span class="firstbreadcrumb" onclick="window.location=\''.$baseUrlString.'\'">Home</span> <span class="arrows">&rsaquo;</span> ';			
						
				for($b = 0; $b < sizeof($breadCrumNames);$b++)
				{	
					if($b == 0)
					{
						$breadCrumbsData .= '<span>'.$breadCrumNames[$b][0]['menuName'].'</span> <span class="arrows">&rsaquo;</span> ';
					}
					else if($b == (sizeof($breadCrumNames) - 1))
					{
						if($pageUrl[3] == '')					
						$breadCrumbsData .= '<span>'.$breadCrumNames[$b][0]['menuName'].'</span>';
						else
						$breadCrumbsData .= '<a href="'.$baseUrlString.$breadCrumNames[$b][0]['url'].'" >'.$breadCrumNames[$b][0]['menuName'].'</a>';
					}
					else
					{
						$breadCrumbsData .= '<span>'.$breadCrumNames[$b][0]['menuName'].'</span> <span class="arrows">&rsaquo;</span> ';
					}			
				}	
				if($pageUrl[3] == 'add')	
				$breadCrumbsData .= '<span class="arrows">&rsaquo;</span> <span>Add</span>';
				else if($pageUrl[3] == 'edit')
				$breadCrumbsData .= '<span class="arrows">&rsaquo;</span> <span>Edit</span>';
				else if($pageUrl[3] == 'view')
				$breadCrumbsData .= '<span class="arrows">&rsaquo;</span> <span>View</span>';
				
				
				$breadCrumbsData .='</div>';
			}
			else
			{
				$breadCrumbsData = '';
			}
		}
		
		echo $breadCrumbsData;
	}
	
	public  function getBreadCrumDetails($url)
	{	
		$selectQuery = "select mm.nav_ids FROM main_menu mm where mm.url = '".$url."'";		
		try
		{
			$db = Zend_Db_Table::getDefaultAdapter();
			$sql = $db->query($selectQuery);
			$result = $sql->fetchAll();
		}
		catch(Exception $e){
			echo $e->getMessage();
		}		
	}
	
	public  function getBreadCrumNames($menuid)
	{	  
		$selectQuery = "select mm.menuName,mm.url FROM main_menu mm WHERE mm.id = ".$menuid;		
		try{
			$db = Zend_Db_Table::getDefaultAdapter();
			$sql=$db->query($selectQuery);
			return $result = $sql->fetchAll();
		}
		catch(Exception $e){
			echo $e->getMessage();
		}	
	}
	
	public  function getRecentlyViewedItems()
	{
		//The Logic used behind this functionality is we are using the object of zend session to store the action 
		$recentlyViewed = new Zend_Session_Namespace('recentlyViewed'); // Creating a new session with namespace
		if(!empty($recentlyViewed->recentlyViewedObject))
		{	
			echo '<div class="recentviewd"><label id="recentviewtext">Recently viewed</label><ul>';
			$rvSize = 0;
			if(sizeof($recentlyViewed->recentlyViewedObject) > 3)
				$rvSize = 3;
			else
				$rvSize = sizeof($recentlyViewed->recentlyViewedObject);
			
			$menuName = '';												
			for($i=0;$i<$rvSize;$i++)
			{
				$pagesplit = explode("!@#",$recentlyViewed->recentlyViewedObject[$i]);
				$pagesplitName = $pagesplit[0]; 
				$pagesplitLink = $pagesplit[1];

				/* Instead of url - display menu name for each list item*/
				if($pagesplitName != 'dashboard' && $pagesplitName != 'welcome' && $pagesplitName != 'viewsettings')
				{
					$selectQuery1 = "select m.menuName from main_menu m where m.url = '/".$pagesplitName."'";
					$db = Zend_Db_Table::getDefaultAdapter();
					$sql=$db->query($selectQuery1);
					$resultarray = $sql->fetchAll();
					
					if(!empty($resultarray))
						$menuName = ucfirst($resultarray[0]['menuName']);
					else $menuName = ucfirst($pagesplitName);
				}
				else $menuName = ucfirst($pagesplitName);
				/* Display of add, edit or view in each list item */
				$urldata = explode("/",$pagesplitLink);
				if(isset($urldata[3]))
				{
					if($urldata[3] == 'add')
						$menuName .= '-Add';
					else if($urldata[3] == 'edit')
						$menuName .= '-Edit';
					else if($urldata[3] == 'view')
						$menuName .= '-View';
					else if($urldata[3] == 'viewsettings')
						$menuName = 'Settings';
					else if($urldata[3] == 'viewprofile')
						$menuName = 'Profile';
					else if($urldata[3] == 'changepassword')
						$menuName = 'Change password';
				}							 
				echo '<li><span id="redirectlink" onclick ="redirecttolink(\''.$pagesplitLink.'\',"");">'.$menuName.'</span><a href="javascript:void(0);" onClick="closetab(this,\''.$pagesplitName.'\',\''.$pagesplitLink.'\')"></a></li>';											
			}
		} 
		$tmpPageLink = explode("/",$_SERVER['REQUEST_URI']); 
		$pageName = $tmpPageLink[2];
		$pageLink = $_SERVER['REQUEST_URI'];
		$a= 'index';
		
		if(isset($recentlyViewed->recentlyViewedObject))
		{
			
			if(sizeof($recentlyViewed->recentlyViewedObject) > 3 && $pageLink != BASE_URL && !in_array($pageName."!@#".$pageLink, $recentlyViewed->recentlyViewedObject))
			{
				array_shift($recentlyViewed->recentlyViewedObject);											
			}
			if($pageName != 'public')
			{
				if(!in_array($pageName."!@#".$pageLink, $recentlyViewed->recentlyViewedObject))
				{
					
					if($pageLink != BASE_URL)
					{
						array_push($recentlyViewed->recentlyViewedObject,$pageName."!@#".$pageLink);
					}
				}  											
			}
		}
		else
		{
			$recentlyViewed->recentlyViewedObject = array();
			
			if($pageLink != BASE_URL && !in_array($pageName."!@#".$pageLink, $recentlyViewed->recentlyViewedObject)) 
				array_push($recentlyViewed->recentlyViewedObject,$pageName."!@#".$pageLink);
			
		}
		

		echo '</ul></div>';
	}
	

}
?>