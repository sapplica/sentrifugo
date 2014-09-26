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
 * Breadcrumbs View Helper
 *
 * A View Helper that creates the menu
 *
 *
 */

class Zend_View_Helper_Breadcrumbs extends Zend_View_Helper_Abstract {
	
	
	public  function breadcrumbs($baseUrlString = '')
	{	
	    $request = Zend_Controller_Front::getInstance();
        $params = $request->getRequest()->getParams();
		$controllerName = $request->getRequest()->getControllerName();
        $action_Name = $request->getRequest()->getActionName();
        $tName ='';
        $vName = '';
        $tUrl = '';
        $serviceUrl = '';
        
        
        $burl = $controllerName."/".$action_Name;
        
        /**
         * 
         * For service request modifying the breadcrum based on t and v params
         * @var t and @var v
         */
        $param_t = isset($params['t'])?sapp_Global::_decrypt($params['t']):"";
        $param_v = isset($params['v'])?sapp_Global::_decrypt($params['v']):"";
        $service_menu = sapp_Helper::sd_menu_names();
        $service_action_arr = sapp_Helper::sd_action_names();
        if($param_t != '' && isset($service_menu[$param_t]))
        {
           $tName = $service_menu[$param_t].' Summary';
           $tUrl = $baseUrlString.'/'.$controllerName.'/index/t/'.sapp_Global::_encrypt($param_t);
        }       
           
        if($param_v != '' && isset($service_action_arr[$param_v]))
           $vName = $service_action_arr[$param_v]; 
        else
        {
        	 
           $vName = ($action_Name!='index'?$action_Name:'');
        }      
        
        if($vName !='')
         {
         	if($tName !='')
         		$serviceUrl = '<a href='.$tUrl.'>'.$tName.'</a><span class="arrows">&rsaquo;</span>';
         		
         	$serviceUrl.= '<span>'.ucfirst($vName).'</span>';
         } else
         {
            $serviceUrl = '<span>'.$tName.'</span>';
         } 

        /**
         * End modifying breadcrum for servicerequest.
         */ 
        
        unset($params['module'], $params['controller'], $params['action']);
		if(isset($params['error_handler']))
		 unset($params['error_handler']);
        
        $id_name = '';
        if(is_array($params) && !empty($params))
        {            
            foreach($params as $key => $value)
            {
            	if(!is_array($value)){
	                $burl .= "/".$key."/".$value;
            	}
            }
            $id_name = "yes";
        }	
		$pageUrl = explode("/",$_SERVER['REQUEST_URI']);
		 
        $serverUrl = $_SERVER['HTTP_HOST'];
		$reportsArr = array('leavesreport'=>'Leaves',
		                    'holidaygroupreports'=>'Holidays',
                            'activeuser'=>'Active Users',
							'employeereport'=>'Employees',
							'rolesgroup'=>'Roles',
							'emprolesgroup'=>'Employee Roles',
							'userlogreport'=>'User Logs',
                            'activitylogreport'=>'Activity Logs',
							'requisitionstatusreport'=>'Requisition'
		                    );

		$pageName = $controllerName;
		$actionName = $action_Name;
		$breadCrumbsData = '';
									   
				$mydetails_arr = array(
				'jobhistory'=>'Employee Job History',
				'certification'=>'Training & Certification Details',
				'experience'=>'Experience Details',
				'education'=>'Education Details',
				'medicalclaims'=>'Medical Claims',
				'leaves'=>'Employee Leaves',
				'skills'=>'Employee Skills',
				'communication'=>'Communication Details',
				'communicationdetailsview'=>'Communication Details',
				'disability'=>'Disability Details',
				'disabilitydetailsview'=>'Disability Details',
				'workeligibility'=>'Work Eligibility Details',
				'workeligibilitydetailsview'=>'Work Eligibility Details',
				'visa'=>'Visa and Immigration Details',
				'visadetailsview'=>'Visa and Immigration Details',
				'additionaldetails'=>'Additional Details',
				'additionaldetailsview'=>'Additional Details',
				'salarydetails'=>'Salary Account Details',
				'salarydetailsview'=>'Salary Account Details',
				'personal'=>'Personal Details',
				'personaldetailsview'=>'Personal Details',
				'creditcard'=>'Corporate Card Details',
				'creditcarddetailsview'=>'Corporate Card Details',
				'dependency'=>'Dependency Details',
				'edit'=>'Edit',
				);	
				

				$myemployees_arr = array(
				'additionaldetailsview'=>'Additional Details',
				'jobhistoryview'=>'Job History',
				'perview'=>'Personal Details',
				'expview'=>'Experience Details',
				'eduview'=>'Education Details',
				'skillsview'=>'Employee Skills',
				'comview'=>'Communication Details',
				'trainingview'=>'Training & Certification Details',
				'view'=>'View',
			
				);
									   
		
		

			
		if($pageName == '' || $pageName == 'welcome')
		{
			$breadCrumbsData .= '';
		}
		else if($pageName == 'dashboard')
		{
			$breadCrumbsData = '<div class="breadcrumbs">';	
			
			if($actionName == 'viewsettings')
			$breadCrumbsData .= '<a href="'.$baseUrlString.'">Home</a> <span class="arrows">&rsaquo;</span> Settings';
			else if($actionName == 'viewprofile')
			$breadCrumbsData .= '<a href="'.$baseUrlString.'">Home</a> <span class="arrows">&rsaquo;</span> Profile';
			else if($actionName == 'changepassword')
			$breadCrumbsData .= '<a href="'.$baseUrlString.'">Home</a> <span class="arrows">&rsaquo;</span> Change Password';
            else if($actionName == 'emailsettings')
			$breadCrumbsData .= '<a href="'.$baseUrlString.'">Home</a> <span class="arrows">&rsaquo;</span> Email Settings';
			else if($actionName == 'upgradeapplication')
			$breadCrumbsData .= '<a href="'.$baseUrlString.'">Home</a> <span class="arrows">&rsaquo;</span> Upgrade Application  ';
			
			$breadCrumbsData .='</div>';
		}
		else if($pageName == 'configuresite')
		{
			$breadCrumbsData = '<div class="breadcrumbs">';	
			$breadCrumbsData .= '<a href="'.$baseUrlString.'">Home</a> <span class="arrows">&rsaquo;</span> Configure Site';
			$breadCrumbsData .='</div>';
		}
		else if($pageName == 'managemenus')
		{
			$breadCrumbsData = '<div class="breadcrumbs">';	
			$breadCrumbsData .= '<a href="'.$baseUrlString.'">Home</a> <span class="arrows">&rsaquo;</span> Manage Modules';
			$breadCrumbsData .='</div>';
		}
	   else if($pageName == 'logmanager')
		{
			$breadCrumbsData = '<div class="breadcrumbs">';	
			$breadCrumbsData .= '<a href="'.$baseUrlString.'">Home</a> <span class="arrows">&rsaquo;</span> Activity Log';
			$breadCrumbsData .='</div>';
		}
	   else if($pageName == 'userloginlog')
		{
			$breadCrumbsData = '<div class="breadcrumbs">';	
			$breadCrumbsData .= '<a href="'.$baseUrlString.'">Home</a> <span class="arrows">&rsaquo;</span> User Log';
			$breadCrumbsData .='</div>';
		}
	 	else if($pageName == 'servicerequests')
		{
			
			$breadCrumbsData = '<div class="breadcrumbs">';	
			$breadCrumbsData .= '<a href="'.$baseUrlString.'">Home</a> <span class="arrows">&rsaquo;</span> Service Request Management<span class="arrows">&rsaquo;</span>'.$serviceUrl.'';
			$breadCrumbsData .='</div>';
		}
		else if($pageName == 'reports')
		{
			$breadCrumbsData = '<div class="breadcrumbs">';	
			$breadCrumbsData .= '<a href="'.$baseUrlString.'">Home</a> <span class="arrows">&rsaquo;</span>';
					
			if(isset($actionName) && $actionName !=''){
				$breadCrumbsData .= '<span><a href="'.$baseUrlString.'/reports">Analytics</a></span>';
				
				if($actionName == 'userlogreport')
					$breadCrumbsData .= '<span class="arrows">&rsaquo;</span>Audit Logs<span class="arrows">&rsaquo;</span><span>User log Report</span>';
				else if($actionName == 'activitylogreport')
					$breadCrumbsData .= '<span class="arrows">&rsaquo;</span>Audit Logs<span class="arrows">&rsaquo;</span><span>Activity log Report</span>';
				else if($actionName == 'businessunits')
					$breadCrumbsData .= '<span class="arrows">&rsaquo;</span>Organization<span class="arrows">&rsaquo;</span><span>Business Units Report</span>';
				else if($actionName == 'departments')
					$breadCrumbsData .= '<span class="arrows">&rsaquo;</span>Organization<span class="arrows">&rsaquo;</span><span>Departments Report</span>';
				else if($actionName == 'leavesreport')
					$breadCrumbsData .= '<span class="arrows">&rsaquo;</span>Leave Management<span class="arrows">&rsaquo;</span><span>Employee Leaves Summary Report</span>';
				else if($actionName == 'leavemanagementreport')
					$breadCrumbsData .= '<span class="arrows">&rsaquo;</span>Leave Management<span class="arrows">&rsaquo;</span><span>Leave Management Summary Report</span>';	
				else if($actionName == 'holidaygroupreports')
					$breadCrumbsData .= '<span class="arrows">&rsaquo;</span>Holiday Management<span class="arrows">&rsaquo;</span><span>Holiday Groups & Holidays Report</span>';
				else if($actionName == 'employeereport')
					$breadCrumbsData .= '<span class="arrows">&rsaquo;</span><span>Employees Report</span> ';
				else if($actionName == 'rolesgroup')
					$breadCrumbsData .= '<span class="arrows">&rsaquo;</span>User Management<span class="arrows">&rsaquo;</span><span>Groups & Roles Report</span>';
				else if($actionName == 'emprolesgroup')
					$breadCrumbsData .= '<span class="arrows">&rsaquo;</span>User Management<span class="arrows">&rsaquo;</span><span>Groups, Roles & Employees Report</span>';
				else if($actionName == 'activeuser')
					$breadCrumbsData .= '<span class="arrows">&rsaquo;</span>User Management<span class="arrows">&rsaquo;</span><span>Users & Employees Report</span>';				
				else if($actionName == 'requisitionstatusreport')
					$breadCrumbsData .= '<span class="arrows">&rsaquo;</span>Resource Requisition<span class="arrows">&rsaquo;</span><span>Requisitions Report</span>';
				else if($actionName == 'candidatesreport')
					$breadCrumbsData .= '<span class="arrows">&rsaquo;</span>Resource Requisition<span class="arrows">&rsaquo;</span><span>Candidate Details Report</span>';
				else if($actionName == 'interviewrounds')
					$breadCrumbsData .= '<span class="arrows">&rsaquo;</span>Resource Requisition<span class="arrows">&rsaquo;</span><span>Scheduled Interviews Report</span>';
				else if($actionName == 'empscreening')
					$breadCrumbsData .= '<span class="arrows">&rsaquo;</span>Background Checks<span class="arrows">&rsaquo;</span><span>Employee / Candidate Screening Report</span>';
				else if($actionName == 'agencylistreport')
					$breadCrumbsData .= '<span class="arrows">&rsaquo;</span>Background Checks<span class="arrows">&rsaquo;</span><span>Background Checks Agencies Report</span>';				
			}
			else
				$breadCrumbsData .= '<span>Analytics</span>';
			$breadCrumbsData .='</div>';
		}
		else
		{	
			$breadCrumbsData = '<div class="breadcrumbs">';		
			$url = "/".$pageName;
			$breadCrumIds = $this->getBreadCrumDetails($url);
			$breadCrumNames = array();	
			
			if(!empty($breadCrumIds))
			{
				$menu_model = new Default_Model_Menu();
				
				$breadcrumstring = trim($breadCrumIds[0]['nav_ids'], ',');
				$breadcrumArr = explode(",",$breadcrumstring);
				
				$breadCrumNames = $breadCrumIds;
					
				$breadCrumbsData .= '<span class="firstbreadcrumb" onclick="window.location=\''.$baseUrlString.'\'">Home</span> <span class="arrows">&rsaquo;</span> ';			
				for($b = 0; $b < sizeof($breadCrumNames);$b++)
				{	

					$breadCrumNames[$b]['menuName'] = $menu_model->getMenuText($breadCrumNames[$b]['menuName']);

		            if($b == 0)
					{
					   if($breadCrumNames[$b]['url'] == '/sitepreference'){
							$breadCrumbsData .= '<span>'.$breadCrumNames[$b]['menuName'].'</span>';
					   
					        
					   }else{
							$breadCrumbsData .= '<span>'.$breadCrumNames[$b]['menuName'].'</span> <span class="arrows">&rsaquo;</span> ';
					   }

					}
					else if($b == (sizeof($breadCrumNames) - 1))
					{
						if($actionName == ''){					
							$breadCrumbsData .= '<span>'.$breadCrumNames[$b]['menuName'].'</span>';
						}else{
							$breadCrumbsData .= '<a href="'.$baseUrlString.$breadCrumNames[$b]['url'].'" >'.$breadCrumNames[$b]['menuName'].'</a>';
						}
					}
					else
					{
						$breadCrumbsData .= '<span>'.$breadCrumNames[$b]['menuName'].'</span> <span class="arrows">&rsaquo;</span> ';
					}			
				}	
				if( ($actionName == 'add' || ($actionName == 'edit' && $id_name == '')) || ($actionName !='' && $actionName !='view'))
				{
		   			if($actionName == 'edit' || $actionName !='')
					{
						$idvalindex = '';
						if(in_array('id',$pageUrl))
						{
							$idindex = array_search('id', $pageUrl);
							$idvalindex = $idindex + 1;
						}
						else if(in_array('userid',$pageUrl))
						{
							$idindex = array_search('userid', $pageUrl);
							$idvalindex = $idindex + 1;
						}					
						if((in_array('id',$pageUrl) || in_array('userid',$pageUrl)) && $pageName != 'myemployees')
						{		
							$idval = intval($pageUrl[$idvalindex]);
							if($idval != 0)
							$breadCrumbsData .= '<span class="arrows">&rsaquo;</span> <span>Edit</span>';
							else
							$breadCrumbsData .= '<span class="arrows">&rsaquo;</span> <span>Add</span>';
						}
						else
						{
						  if($pageName == 'mydetails')
							{
							
							    if(isset($actionName) && $actionName !='')
								{
									if(array_key_exists($actionName, $mydetails_arr) !== false)
                                        $breadCrumbsData .= '<span class="arrows">&rsaquo;</span> <span>'.$mydetails_arr[$actionName].'</span>';	
								}else
                                {
								    $breadCrumbsData .= '<span class="arrows">&rsaquo;</span> <span>Edit</span>';
                                }  								
							}
							else if($pageName == 'myemployees')
							{
							    if(isset($actionName) && $actionName !='')
								{
									if(array_key_exists($actionName, $myemployees_arr) !== false)
                                        $breadCrumbsData .= '<span class="arrows">&rsaquo;</span> <span>'.$myemployees_arr[$actionName].'</span>';	
								}else
                                {
								    $breadCrumbsData .= '<span class="arrows">&rsaquo;</span> <span>View</span>';
                                } 
							}
							
							else
							{		
								if($actionName == 'multipleresume')
								$breadCrumbsData .= '<span class="arrows">&rsaquo;</span> <span>Add multiple CVs</span>';
								if($actionName == 'edit' && $pageName == 'heirarchy')
								$breadCrumbsData .= '<span class="arrows">&rsaquo;</span> <span>Edit</span>';
								else if($actionName == 'edit' || $actionName == 'add')  
								$breadCrumbsData .= '<span class="arrows">&rsaquo;</span> <span>Add</span>';
								else
								$breadCrumbsData .= '';
							}	
						}
					}
					else
					{
							   $breadCrumbsData .= '<span class="arrows">&rsaquo;</span> <span>Add</span>';
					}	
				}
				else if($actionName == 'edit')
				{
					$idvalindex = '';
					if(in_array('id',$pageUrl))
					{
						$idindex = array_search('id', $pageUrl);
						$idvalindex = $idindex + 1;
					}
					else if(in_array('userid',$pageUrl))
					{
						$idindex = array_search('userid', $pageUrl);
						$idvalindex = $idindex + 1;
					}					
					if(in_array('id',$pageUrl) || in_array('userid',$pageUrl))
					{						
						$idval = intval($pageUrl[$idvalindex]);
						if($idval != '')
						$breadCrumbsData .= '<span class="arrows">&rsaquo;</span> <span>Edit</span>';
						else
						$breadCrumbsData .= '<span class="arrows">&rsaquo;</span> <span>Add</span>';
					}
					else
						$breadCrumbsData .= '<span class="arrows">&rsaquo;</span> <span>Add</span>';
				}
				else if($actionName == 'view')
				{
					$breadCrumbsData .= '<span class="arrows">&rsaquo;</span> <span>View</span>';
				}	
				
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
			if(!empty($result))
			{
				$breadcrumbsData =  $db->query("SELECT menuName,url,nav_ids FROM main_menu WHERE  FIND_IN_SET(id,'".$result[0]['nav_ids']."') order by nav_ids;");
				$breadcrumbnames = $breadcrumbsData->fetchAll();					
			}else $breadcrumbnames = array();
		}
		catch(Exception $e){
			echo $e->getMessage();
		}	
		
		return $breadcrumbnames;
	}
	
}
?>