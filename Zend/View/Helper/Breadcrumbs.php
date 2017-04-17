<?php
/********************************************************************************* 
 *  This file is part of Sentrifugo.
 *  Copyright (C) 2015 Sapplica
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

class Zend_View_Helper_Breadcrumbs extends Zend_View_Helper_Abstract 
{
	
    public  function breadcrumbs($baseUrlString = '')
    {
    	
        $request = Zend_Controller_Front::getInstance();
		
        $params = $request->getRequest()->getParams();
        $controllerName = $request->getRequest()->getControllerName();
        $action_Name = $request->getRequest()->getActionName();
		
		$auth = Zend_Auth::getInstance();
		$loginuserRole = '';
		$loginuserGroup = '';
		if($auth->hasIdentity())
		{
			$loginuserRole = $auth->getStorage()->read()->emprole;	
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
        
        $tName =''; $vName = '';$tUrl = '';$serviceUrl = '';
                
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
        } 
        else
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
      /*  $reportsArr = array('leavesreport'=>'Leaves','holidaygroupreports'=>'Holidays','activeuser'=>'Active Users',
                            'employeereport'=>'Employees','rolesgroup'=>'Roles','emprolesgroup'=>'Employee Roles',
                            'userlogreport'=>'User Logs','activitylogreport'=>'Activity Logs','requisitionstatusreport'=>'Requisition','performancereport' => 'Year Wise'
                    );*/

        $pageName = $controllerName;
        $actionName = $action_Name; 
        $breadCrumbsData = '';
								   
        $mydetails_arr = array(	'jobhistory'=>'Employee Job History','certification'=>'Training & Certification Details',
				'experience'=>'Experience Details','education'=>'Education Details',
				'medicalclaims'=>'Medical Claims','leaves'=>'Employee Leaves',
				'skills'=>'Employee Skills','communication'=>'Contact Details',
				'communicationdetailsview'=>'Contact Details',	'disability'=>'Disability Details',
				'disabilitydetailsview'=>'Disability Details',	'workeligibility'=>'Work Eligibility Details',
				'workeligibilitydetailsview'=>'Work Eligibility Details','visa'=>'Visa and Immigration Details',
				'visadetailsview'=>'Visa and Immigration Details','additionaldetails'=>'Additional Details',
				'additionaldetailsview'=>'Additional Details','salarydetails'=>'Salary Details',
				'salarydetailsview'=>'Salary Details','personal'=>'Personal Details',
				'personaldetailsview'=>'Personal Details','creditcard'=>'Corporate Card Details',
				'creditcarddetailsview'=>'Corporate Card Details','dependency'=>'Dependency Details','edit'=>'Edit',
                        );	
				
        $myemployees_arr = array(
				'additionaldetailsview'=>'Additional Details','jobhistoryview'=>'Job History',
				'perview'=>'Personal Details','expview'=>'Experience Details','eduview'=>'Education Details',
				'skillsview'=>'Employee Skills','comview'=>'Contact Details','trainingview'=>'Training & Certification Details',
				'view'=>'View',	'employeereport' => 'My Team Report'		
                            );
									   
        $myemployeesedit_arr = array(
				'additionaldetailsedit'=>'Additional Details','jobhistoryedit'=>'Job History','peredit'=>'Personal3 Details',
				'expedit'=>'Experience Details','eduedit'=>'Education Details',	'skillsedit'=>'Employee Skills',
				'comedit'=>'Contact Details','trainingedit'=>'Training & Certification Details','edit'=>'Edit',			
                            );	
        
        $dashboard_actions = array(
            'viewsettings' => $this->dashboard_actions_html($baseUrlString, 'Settings'),
            'viewprofile' => $this->dashboard_actions_html($baseUrlString, 'Profile'),
            'changepassword' => $this->dashboard_actions_html($baseUrlString, 'Change Password'),
            'emailsettings' => $this->dashboard_actions_html($baseUrlString, 'Email Settings'),
            'upgradeapplication' => $this->dashboard_actions_html($baseUrlString, 'Upgrade Application'),
        );
        
         $emptabarr = array('dependencydetails','creditcarddetails','visaandimmigrationdetails','workeligibilitydetails','disabilitydetails','empcommunicationdetails','empskills','empleaves','empholidays','medicalclaims','educationdetails','experiencedetails','trainingandcertificationdetails','emppersonaldetails','empperformanceappraisal','emppayslips','empbenefits','emprenumerationdetails','emprequisitiondetails','empadditionaldetails','empsecuritycredentials','empsalarydetails','empjobhistory','employeedocs','empremunerationdetails');

        if($pageName == '' || $pageName == 'welcome')
        {
        	 $breadCrumbsData .= '';
        }
        else if($pageName == 'dashboard')
        {
            $breadCrumbsData = '<div class="breadcrumbs">';	
	    $breadCrumbsData .= $dashboard_actions[$actionName];            
            $breadCrumbsData .='</div>';
        }
        else if($pageName == 'configuresite')
        {        
        	    
            $breadCrumbsData .= $this->menu_home_html($baseUrlString, 'Configure Site');
        }
        else if($pageName == 'managemenus')
        {            
            $breadCrumbsData .= $this->menu_home_html($baseUrlString, 'Modules');
        }
        else if($pageName == 'logmanager')
        {			
            $breadCrumbsData .= $this->menu_home_html($baseUrlString, 'Activity Log');
        }
        else if($pageName == 'userloginlog')
        {			
            $breadCrumbsData .= $this->menu_home_html($baseUrlString, 'User Log');
        }
        else if($pageName == 'servicerequests')
        {			
            $breadCrumbsData = '<div class="breadcrumbs">';	
            $breadCrumbsData .= '<a href="'.$baseUrlString.'">Home</a> <span class="arrows">&rsaquo;</span> Service Request<span class="arrows">&rsaquo;</span>'.$serviceUrl.'';
            $breadCrumbsData .='</div>';
        }
        else if($pageName == 'reports')
        {
            $breadCrumbsData = '<div class="breadcrumbs">';	
            $breadCrumbsData .= '<a href="'.$baseUrlString.'">Home</a> <span class="arrows">&rsaquo;</span>';
					
            if(isset($actionName) && $actionName !='')
            {
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
					$breadCrumbsData .= '<span class="arrows">&rsaquo;</span>Employees<span class="arrows">&rsaquo;</span><span>Employees Report</span>';
				else if($actionName == 'rolesgroup')
					$breadCrumbsData .= '<span class="arrows">&rsaquo;</span>User Management<span class="arrows">&rsaquo;</span><span>Groups & Roles Report</span>';
				else if($actionName == 'emprolesgroup')
					$breadCrumbsData .= '<span class="arrows">&rsaquo;</span>User Management<span class="arrows">&rsaquo;</span><span>Groups, Roles & Employees Report</span>';
				else if($actionName == 'activeuser')
					$breadCrumbsData .= '<span class="arrows">&rsaquo;</span>User Management<span class="arrows">&rsaquo;</span><span>Users & Employees Report</span>';				
				else if($actionName == 'requisitionstatusreport')
					$breadCrumbsData .= '<span class="arrows">&rsaquo;</span>Recruitments<span class="arrows">&rsaquo;</span><span>Recruitments Report</span>';
				else if($actionName == 'candidatesreport')
					$breadCrumbsData .= '<span class="arrows">&rsaquo;</span>Recruitments<span class="arrows">&rsaquo;</span><span>Candidate Details Report</span>';
				else if($actionName == 'interviewrounds')
					$breadCrumbsData .= '<span class="arrows">&rsaquo;</span>Recruitments<span class="arrows">&rsaquo;</span><span>Scheduled Interviews Report</span>';
				else if($actionName == 'empscreening')
					$breadCrumbsData .= '<span class="arrows">&rsaquo;</span>Background Check<span class="arrows">&rsaquo;</span><span>Employee / Candidate Screening Report</span>';
				else if($actionName == 'agencylistreport')
					$breadCrumbsData .= '<span class="arrows">&rsaquo;</span>Background Check<span class="arrows">&rsaquo;</span><span>Background Check Agencies Report</span>';		
				else if($actionName == 'performancereport')
					$breadCrumbsData .= '<span class="arrows">&rsaquo;</span>Performance Appraisal<span class="arrows">&rsaquo;</span><span>Employee Appraisals Report</span>';	
				else if($actionName == 'previousappraisals')
					$breadCrumbsData .= '<span class="arrows">&rsaquo;</span>Performance Appraisal<span class="arrows">&rsaquo;</span><span>Employee Appraisals</span>';
				else if($actionName == 'servicedeskreport')
					$breadCrumbsData .= '<span class="arrows">&rsaquo;</span>Service Request<span class="arrows">&rsaquo;</span><span>Service Request Report</span>';
			}
			else
				$breadCrumbsData .= '<span>Analytics</span>';
			$breadCrumbsData .='</div>';
		}
   		else if($pageName == 'employee' && $actionName == 'changeorghead')
        {		
        	 $breadCrumbsData = '<div class="breadcrumbs">';	
            $breadCrumbsData .= '<a href="'.$baseUrlString.'">Home</a> <span class="arrows">&rsaquo;</span> Human Resource<span class="arrows">&rsaquo;</span> <a href="'.$baseUrlString.'/employee">Employees</a><span class="arrows">&rsaquo;</span>Manage Organization Head';
            $breadCrumbsData .='</div>';
        }
    	else if($pageName == 'appraisalstatus')
        {			
            $breadCrumbsData = '<div class="breadcrumbs">';	
            if($actionName=='manager')
            	$breadCrumbsData .= '<a href="'.$baseUrlString.'">Home</a> <span class="arrows">&rsaquo;</span> Performance Appraisal<span class="arrows">&rsaquo;</span><a href="'.$baseUrlString.'/appraisalstatus/manager">Manager Status</a>';
            else	
            	$breadCrumbsData .= '<a href="'.$baseUrlString.'">Home</a> <span class="arrows">&rsaquo;</span> Performance Appraisal<span class="arrows">&rsaquo;</span><a href="'.$baseUrlString.'/appraisalstatus/employee">Employee Status</a>'; 
            $breadCrumbsData .='</div>';
        }
    	else if(in_array($pageName,$emptabarr))
        {   
        	$breadCrumbsData = '<div class="breadcrumbs">';	
            $breadCrumbsData .= '<a href="'.$baseUrlString.'">Home</a> <span class="arrows">&rsaquo;</span> Human Resource<span class="arrows">&rsaquo;</span> <a href="'.$baseUrlString.'/employee">Employees</a><span class="arrows"> &rsaquo;</span>'; 
             if($pageName == 'employeedocs')
             $breadCrumbsData .='<span>Documents</span>';
             else 
             $breadCrumbsData .='<span>'.ucfirst($actionName).'</span>';
            $breadCrumbsData .='</div>';
        }
		else
		{
			$breadCrumbsData = '<div class="breadcrumbs">';		
			$url = "/".$pageName;
			if($pageName=='expensecategories' || $pageName=='expenses' || $pageName=='paymentmode' || $pageName=='receipts' || $pageName=='trips' || $pageName=='advances' || $pageName=='employeeadvances' || $pageName=='myemployeeexpenses')
			{
				
				$url = "/expenses/".$pageName;
				if($pageName=='advances')
					$url = "/expenses/".$pageName."/myadvances";
			}
			if($pageName=='assetcategories' || $pageName=='assets')
			{
				$url = "/assets/".$pageName;
			}
			if($pageName=='allexitproc' || $pageName=='exitproc' || $pageName=='exitprocsettings' || $pageName=='exittypes' || $pageName=='configureexitqs')
			{
				$url = "/exit/".$pageName;
			}
			$breadCrumIds = $this->getBreadCrumDetails($url);
			
			$breadCrumNames = array();	
			if(!empty($breadCrumIds))
			{
				
				$menu_model = new Default_Model_Menu();
                                
				
				$breadCrumbsData .= '<span class="firstbreadcrumb" onclick="window.location=\''.$baseUrlString.'\'">Home</span> <span class="arrows">&rsaquo;</span> ';			
                                $breadCrumIds['nav_ids_arr'] = array_merge($breadCrumIds['nav_ids_arr']);
                
				for($b = 0; $b < sizeof($breadCrumIds['nav_ids_arr']);$b++)
				{	
					
					$loop_menu_name = $breadCrumIds['menu_arr'][$breadCrumIds['nav_ids_arr'][$b]]['menuName'];
					$loop_menu_url = $breadCrumIds['menu_arr'][$breadCrumIds['nav_ids_arr'][$b]]['url'];
					$loop_menu_name = $menu_model->getMenuText($loop_menu_name);

		            if($b == 0)
					{
					   if($loop_menu_url == '/sitepreference'){
							$breadCrumbsData .= '<span>'.$loop_menu_name.'</span>';
					   
					        
					   }else if(!empty($loop_menu_name)){
							$breadCrumbsData .= '<span>'.$loop_menu_name.'</span> <span class="arrows">&rsaquo;</span> ';
					   }

					}
					else if($b == (sizeof($breadCrumIds['nav_ids_arr']) - 1))
					{
						if($actionName == ''){					
							$breadCrumbsData .= '<span>'.$loop_menu_name.'</span>';
						}else if(!empty($loop_menu_name)){
							$breadCrumbsData .= '<a href="'.$baseUrlString.$loop_menu_url.'" >'.$loop_menu_name.'</a>';
						}
					}
					else if(!empty($loop_menu_name))
					{
						$breadCrumbsData .= '<span>'.$loop_menu_name.'</span> <span class="arrows">&rsaquo;</span> ';
					}			
				}	
				
				if((($actionName == 'add' || ($actionName == 'edit' && $id_name == '')) || ($actionName !='' && $actionName !='view')) && $id_name != 'pd')
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
                                                      
							if($idval != 0 || $pageUrl[$idvalindex] != '')
							$breadCrumbsData .= ' <span class="arrows">&rsaquo;</span> <span >Edit</span>';
							else
							
							$breadCrumbsData .= ' <span class="arrows">&rsaquo;</span> <span>Add</span>';
						}
						else
						{
						  if($pageName == 'mydetails')
							{
							    if(isset($actionName) && $actionName !='')
								{
									if(array_key_exists($actionName, $mydetails_arr) !== false)
                                        $breadCrumbsData .= ' <span class="arrows">&rsaquo;</span> <span>'.$mydetails_arr[$actionName].'</span>';	
								}else
                                {
								    $breadCrumbsData .= ' <span class="arrows">&rsaquo;</span> <span>Edit</span>';
                                }  								
							}
							else if($pageName == 'myemployees')
							{
							    if(isset($actionName) && $actionName !='')
								{
									if(array_key_exists($actionName, $myemployees_arr) !== false)
                                        $breadCrumbsData .= ' <span class="arrows">&rsaquo;</span> <span>'.$myemployees_arr[$actionName].'</span>';	
                                   	else if(array_key_exists($actionName, $myemployeesedit_arr) !== false)
                                        $breadCrumbsData .= ' <span class="arrows">&rsaquo;</span> <span>'.$myemployeesedit_arr[$actionName].'</span>';
                                   	else if($actionName == 'add')
                                   		$breadCrumbsData .= ' <span class="arrows">&rsaquo;</span> <span>Add</span>';
								}else
                                {
								    $breadCrumbsData .= ' <span class="arrows">&rsaquo;</span> <span>View</span>';
                                } 
							}
							
							else
							{				
								if($actionName == 'multipleresume')
								$breadCrumbsData .= ' <span class="arrows">&rsaquo;</span> <span>Add multiple CVs</span>';
								if($actionName == 'edit' && ($pageName == 'heirarchy' || $pageName == 'appraisalself'))
								$breadCrumbsData .= ' <span class="arrows">&rsaquo;</span> <span>Edit</span>';
								else if($actionName == 'edit' && ($pageName == 'empconfiguration'))  
								$breadCrumbsData .= ' <span class="arrows">&rsaquo;</span> <span>Edit</span>';
								else if($actionName == 'edit' || $actionName == 'add')  
								$breadCrumbsData .= ' <span class="arrows">&rsaquo;</span> <span>Add</span>';
								else
								$breadCrumbsData .= '';
							}	
						}
					}
					else
					{      
						$breadCrumbsData .= ' <span class="arrows">&rsaquo;</span> <span>Add</span>';
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
						$breadCrumbsData .= ' <span class="arrows">&rsaquo;</span> <span>Edit</span>';
						else
						$breadCrumbsData .= ' <span class="arrows">&rsaquo;</span> <span>Add</span>';
					}
					else
						$breadCrumbsData .= ' <span class="arrows">&rsaquo;</span> <span>Add</span>';
				}
				else if($actionName == 'view')
				{
					
					$breadCrumbsData .= ' <span class="arrows">&rsaquo;</span> <span>View</span>';
				}	
				
				$breadCrumbsData .='</div>';
			}
			else
			{
				$breadCrumbsData = '';
			}
		}
		$expense_data='';
		if($pageName=='expensecategories' || $pageName=='expenses' || $pageName=='paymentmode' || $pageName=='receipts' || $pageName=='trips' || $pageName=='advances' || $pageName=='employeeadvances' || $pageName=='myemployeeexpenses')
		{
			if($loginuserRole==SUPERADMINROLE) {
				$expense_data = '<div class="dt_btn" style="margin: 0;position: absolute;right: 10px;top: 10px;">
				<a class="dropdown-button" href="#"data-activates="addbtna"><i class="fa fa-plus"></i> Add</a>
				<ul id="addbtna" class="dropdown-content" style="list-style: none; margin: 0; padding: 0;">
				
					<li><a href="'.BASE_URL.'expenses/expensecategories/edit";>Category</a></li>
					<li><a href="'.BASE_URL.'expenses/paymentmode/edit";>Payment Mode</a></li>
				</ul>
				</div>';
			}else if($loginuserGroup==HR_GROUP){	
				$expense_data = '<div class="dt_btn" style="margin: 0;position: absolute;right: 10px;top: 10px;">
				<a class="dropdown-button" href="#"data-activates="addbtna"><i class="fa fa-plus"></i> Add</a>
				<ul id="addbtna" class="dropdown-content" style="list-style: none; margin: 0; padding: 0;">
					<li><a href="'.BASE_URL.'expenses/expenses/edit";>Expense</a></li>
					<li><a href="'.BASE_URL.'expenses/expenses/bulkexpenses";">Bulk Expenses</a></li>
					<li><a href="'.BASE_URL.'expenses/expensecategories/edit";>Category</a></li>
					<li><a href="'.BASE_URL.'expenses/paymentmode/edit";>Payment Mode</a></li>
					<li><a href="'.BASE_URL.'expenses/receipts/";>Receipts</a></li>
					<li><a href="'.BASE_URL.'expenses/trips/edit";>Trip</a></li>
					<li><a href="'.BASE_URL.'expenses/employeeadvances/edit";>Advance</a></li>
					
					
				</ul>
				</div>';
			}else
			{
				$expense_data = '<div class="dt_btn" style="margin: 0;position: absolute;right: 10px;top: 10px;">
				<a class="dropdown-button" href="#"data-activates="addbtna"><i class="fa fa-plus"></i> Add</a>
				<ul id="addbtna" class="dropdown-content" style="list-style: none; margin: 0; padding: 0;">
					<li><a href="'.BASE_URL.'expenses/expenses/edit";>Expense</a></li>
					<li><a href="'.BASE_URL.'expenses/expenses/bulkexpenses";">Bulk Expenses</a></li>
					<li><a href="'.BASE_URL.'expenses/receipts/";>Receipts</a></li>
					<li><a href="'.BASE_URL.'expenses/trips/edit";>Trip</a></li>
					<li><a href="'.BASE_URL.'expenses/employeeadvances/edit";>Advance</a></li>
				</ul>
				</div>';
			}
		}
		echo $breadCrumbsData.$expense_data;
	}
        
    public function dashboard_actions_html($base_url,$menu_name)
    {
        return '<a href="'.$base_url.'">Home</a> <span class="arrows">&rsaquo;</span> '.$menu_name;
    }
    public function menu_home_html($base_url,$menu_name)
    {
    	if($menu_name=='Activity Log')
    	{
    		 return  '<div class="breadcrumbs"><a href="'.$base_url.'">Home</a> <span class="arrows">&rsaquo;</span>Logs<span class="arrows">&rsaquo;</span>'.$menu_name.'</div>';
    	}
       else if($menu_name=='User Log')
    	{
    		 return  '<div class="breadcrumbs"><a href="'.$base_url.'">Home</a> <span class="arrows">&rsaquo;</span>Logs<span class="arrows">&rsaquo;</span>'.$menu_name.'</div>';
    	}
        return  '<div class="breadcrumbs"><a href="'.$base_url.'">Home</a> <span class="arrows">&rsaquo;</span> '.$menu_name.' </div>';
    }
    public  function getBreadCrumDetails($url)
    {	
        $output = array();
        $selectQuery = "select mm.nav_ids FROM main_menu mm where mm.url = '".$url."'";	
        try
        {
            $menu_arr = $nav_ids_arr = array();
            $db = Zend_Db_Table::getDefaultAdapter();
            $sql = $db->query($selectQuery);
            $result = $sql->fetchAll();
            if(!empty($result))
            {  
                $nav_ids = $result[0]['nav_ids'];
                $nav_ids_arr = array_filter(explode(',', $nav_ids));
                $query = "SELECT id,menuName,url,nav_ids FROM main_menu WHERE  FIND_IN_SET(id,'".$result[0]['nav_ids']."') ;";
                $breadcrumbsData =  $db->query($query);
                $breadcrumbnames = $breadcrumbsData->fetchAll();					
                if(count($breadcrumbnames) > 0)
                {
                    foreach($breadcrumbnames as $bdata)
                    {
                        $menu_arr[$bdata['id']]['menuName'] = $bdata['menuName'];
                        $menu_arr[$bdata['id']]['url'] = $bdata['url'];
                    }
                }
                $output = array('nav_ids_arr' => $nav_ids_arr,'menu_arr' => $menu_arr);
            }                
        }
        catch(Exception $e)
        {	
        }			
        return $output;
    }
	
}
?>