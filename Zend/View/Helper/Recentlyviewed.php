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
* Recentlyviewed View Helper
*
* A View Helper that creates the menu
*
*
*/

class Zend_View_Helper_Recentlyviewed extends Zend_View_Helper_Abstract
{

    public function recentlyviewed()
    {
        $request = Zend_Controller_Front::getInstance();
        $params = $request->getRequest()->getParams();
        $moduleName = $request->getRequest()->getModuleName();
        $controllerName = $request->getRequest()->getControllerName();
        $actionName = $request->getRequest()->getActionName();
        $mparams['module'] = $params['module'];
        $mparams['controller'] = $params['controller'];
        $mparams['action'] = $params['action'];
        $actionurl = '';
        $id_name = 'yes';
        
        $burl = $controllerName."/".$actionName;
        
        if($actionName !='')
        {
        	$actionurl = strstr($_SERVER['REQUEST_URI'], $actionName."/");
        	$actionurl = str_replace($actionName, '', $actionurl);
        }
        else if($controllerName !='')
        {
        	$actionurl = strstr($_SERVER['REQUEST_URI'], $controllerName."/");
        	$actionurl = str_replace($actionName, '', $actionurl);
        }
        else
        {
        	$actionurl = strstr($_SERVER['REQUEST_URI'], $moduleName."/");
        	$actionurl = str_replace($actionName, '', $actionurl);
        }

        $burl = $burl.$actionurl;
               
        $tmpPageLink = explode("/",$_SERVER['REQUEST_URI']);
        $pageName = $controllerName;
        $pageLink = $burl;
                                
        $reportsArr = array('leavesreport'=>'-'.TAB_EMP_LEAVES,
                            'leavemanagementreport'=>'-Leave Management',
                            'holidaygroupreports'=>'-'.TAB_EMP_HOLIDAYS,
                            'activeuser'=>'-Active Users',
                            'employeereport'=>'-Employees',
                            'rolesgroup'=>'-Roles',
                            'emprolesgroup'=>'-Employee Roles',
                            'userlogreport'=>'-User Logs',
                            'activitylogreport'=>'-Activity Logs',
                            'requisitionstatusreport'=>'-Requisition',
                            'candidatesreport'=>'-Candidates',
                            'interviewrounds'=>'-Interview Rounds',
                            'agencylistreport'=>'-Agency List',
                            'empscreening'=>'-Employee Screening',
                            'businessunits'=>'-Business Units',
                            'departments'=>'-Departments',
        					'servicedeskreport'=>'-Requests',
        					'performancereport'=>'-Appraisals'
                );
        $emptabarr = array(
                        'dependencydetails'=> TAB_EMP_DEPENDENCY,
                        'creditcarddetails'=> TAB_EMP_CORPORATE_CARD,
                        'visaandimmigrationdetails'=> TAB_EMP_VISA_EMIGRATION,
                        'workeligibilitydetails'=> TAB_EMP_WORK_ELIGIBILITY,
                        'disabilitydetails'=> TAB_EMP_DISABILITY,
                        'empcommunicationdetails'=> TAB_EMP_CONTACT,
                        'empskills'=> TAB_EMP_SKILLS,
                        'empleaves'=> TAB_EMP_LEAVES,
                        'empholidays'=> TAB_EMP_HOLIDAYS,
                        'medicalclaims'=> TAB_EMP_MEDICAL_CLAIMS,
                        'educationdetails'=> TAB_EMP_EDUCATION,
                        'experiencedetails'=> TAB_EMP_EXPERIENCE,
                        'trainingandcertificationdetails'=> TAB_EMP_TRAINING_CERTIFY,
                        'emppersonaldetails'=> TAB_EMP_PERSONAL,
                        'empperformanceappraisal'=> TAB_EMP_PERFORMANCE_APPRAISAL,
                        'emppayslips'=> TAB_EMP_PAY_SLIPS,
                        'empbenefits'=> TAB_EMP_BENEFITS,
                        'emprenumerationdetails'=> TAB_EMP_REMUNERATION,
                        'empadditionaldetails'=> TAB_EMP_ADDITIONAL,
                        'empsecuritycredentials'=> TAB_EMP_SECURITY_CREDENTIALS,
                        'empsalarydetails'=> TAB_EMP_SALARY,
                        'empjobhistory'=> TAB_EMP_JOB_HISTORY,
                        'mydetails' => "",
                        "myemployees" => "My Team",
                        "userloginlog" => "User Log",
                        "logmanager" => "Activity Log",
                        "empconfiguration" => "Employee Tabs",                                                                
            );	
           							
        $myemployees_arr = array(
                    'view'=> '-View',
                    'trainingview'=> '-'.TAB_EMP_TRAINING_CERTIFY,
                    'comview'=> '-'.TAB_EMP_CONTACT,
                    'skillsview'=> '-'.TAB_EMP_SKILLS,
                    'eduview'=> '-'.TAB_EMP_EDUCATION,
                    'expview'=> '-'.TAB_EMP_EXPERIENCE,
                    'perview'=> '-'.TAB_EMP_PERSONAL,
                    'additionaldetailsview'=> '-'.TAB_EMP_ADDITIONAL,
                    'jobhistoryview'=> '-'.TAB_EMP_JOB_HISTORY,                                                                
                );
                
                $myemployeesedit_arr = array(
                    'edit'=> '-Edit',
                    'trainingedit'=> '-'.TAB_EMP_TRAINING_CERTIFY,
                    'comedit'=> '-'.TAB_EMP_CONTACT,
                    'skillsedit'=> '-'.TAB_EMP_SKILLS,
                    'eduedit'=> '-'.TAB_EMP_EDUCATION,
                    'expedit'=> '-'.TAB_EMP_EXPERIENCE,
                    'peredit'=> '-'.TAB_EMP_PERSONAL,
                    'additionaldetailsedit'=> '-'.TAB_EMP_ADDITIONAL,
                    'jobhistoryedit'=> '-'.TAB_EMP_JOB_HISTORY,                                                                
                );
        
               	$myDetailsEmployeesarr = array('mydetails','myemployees');																																																																																																																																																																	
                $mydetails_arr = array(
                    'communicationdetailsview'=> TAB_EMP_CONTACT.'-View',
                    'communication'=> TAB_EMP_CONTACT.'-Edit',
                    'disabilitydetailsview'=> TAB_EMP_DISABILITY.'-View',
                    'disability'=> TAB_EMP_DISABILITY.'-Edit',
                    'workeligibilitydetailsview'=> TAB_EMP_WORK_ELIGIBILITY.'-View',                    
                    'workeligibility'=> TAB_EMP_WORK_ELIGIBILITY.'-Edit',                    
                    'visadetailsview'=> TAB_EMP_VISA_EMIGRATION.'-View',                    
                    'visa'=> TAB_EMP_VISA_EMIGRATION.'-Edit',                    
                    'creditcarddetailsview'=> TAB_EMP_CORPORATE_CARD.'-View',
                    'creditcard' => TAB_EMP_CORPORATE_CARD."-Edit",
                    "additionaldetails" => TAB_EMP_ADDITIONAL."-Edit",
                    "additionaldetailsview" => TAB_EMP_ADDITIONAL."-View",
                    "salarydetails" => TAB_EMP_SALARY."-Edit",
                    "salarydetailsview" => TAB_EMP_SALARY."-View",
                    "personaldetailsview" => TAB_EMP_PERSONAL."-View",
                    "personal" => TAB_EMP_PERSONAL."-Edit",
                    "jobhistory" => TAB_EMP_JOB_HISTORY,
                    "certification" => TAB_EMP_TRAINING_CERTIFY,
                    "experience" => TAB_EMP_EXPERIENCE,
                    "education" => TAB_EMP_EDUCATION,
                    "medicalclaims" => TAB_EMP_MEDICAL_CLAIMS,
                    "leaves" => TAB_EMP_LEAVES,
                    "skills" => TAB_EMP_SKILLS,
                    "dependency" => TAB_EMP_DEPENDENCY,
                    "index" => TAB_EMP_OFFICIAL."-View",
                    "edit" => TAB_EMP_OFFICIAL."-Edit",                                                    
                );
		    	
		//The Logic used behind this functionality is we are using the object of zend session to store the action
		$recentlyViewed = new Zend_Session_Namespace('recentlyViewed'); // Creating a new session with namespace
		if(!empty($recentlyViewed->recentlyViewedObject))
		{
                    echo '<div class="recentviewd"><label id="recentviewtext">Recently viewed</label><ul>';
                    $rvSize = 0;
                    if(sizeof($recentlyViewed->recentlyViewedObject) > 3)
                    {
                        $rvSize = 3;
                        $recentlyViewed->recentlyViewedObject = array_slice($recentlyViewed->recentlyViewedObject,1);
                    }
                    else
                    {
                        $rvSize = sizeof($recentlyViewed->recentlyViewedObject);
                    }
			
                    $menuName = '';	$pagesplitName='';
                    for($i=0;$i<$rvSize;$i++)
                    {
                        $pagesplit = $recentlyViewed->recentlyViewedObject[$i];
                        
                        $pagesplitName = isset($pagesplit['controller_name'])?$pagesplit['controller_name']:"";
                        $pagesplitLink = isset($pagesplit['url'])?$pagesplit['url']:"";
                        $pagesplit_action = isset($pagesplit['action_name'])?$pagesplit['action_name']:"";
                        $pagesplit_idname = isset($pagesplit['id_name'])?$pagesplit['id_name']:"";
                        $pagesplit_module = isset($pagesplit['module_name'])?$pagesplit['module_name']:"";
						
                        if($pagesplit_module !='timemanagement') 
                        {
	                        // Instead of url - display menu name for each list item
	                        if($pagesplitName != 'dashboard' && $pagesplitName != 'welcome' && $pagesplitName != 'viewsettings')
	                        {					
	
	                            if(array_key_exists($pagesplitName,$emptabarr) !== false)
	                            {
									$menuName = $emptabarr[$pagesplitName];
	                            }
								else if($pagesplitName == 'policydocuments') //for policy documents
								{
									$pagesplit_id = isset($pagesplit['id'])?$pagesplit['id']:"";
									$pagesplit_cat = isset($pagesplit['cat'])?$pagesplit['cat']:"";
									
									if($pagesplit_action == 'index')
									{
										$pagesplitLink = 'policydocuments/id/'.$pagesplit_id;
										
										$documentsModel = new Default_Model_Documents();
										$tmpCatObj = $documentsModel->getCategoryById($pagesplit_id);
										if(!empty($tmpCatObj))
										{
											$menuName = $tmpCatObj['category'];
										}
									}
									else if($pagesplit_action == 'edit' || $pagesplit_action == 'view')
									{
										$documentsModel = new Default_Model_Documents();
										$tmpCatObj = $documentsModel->getCategoryByDocId($pagesplit_id);
										if(!empty($tmpCatObj))
										{
											$menuName = $tmpCatObj['category'];
										}										
									}
									else if($pagesplit_action == 'add' && empty($pagesplit_id))
									{
										$menuName = 'Policy Documents';
									}
									else if($pagesplit_action == 'addmultiple')
									{
										$documentsModel = new Default_Model_Documents();
										$tmpCatObj = $documentsModel->getCategoryById($pagesplit_id);
										if(!empty($tmpCatObj))
										{
											$menuName = $tmpCatObj['category'];
										}
									}
									//$menuName = sapp_Helper::policyDocsRviewed($tmpPageLink);
									if(empty($menuName))
									{
										$selectQuery1 = "select m.menuName from main_menu m where m.url = '/".$pagesplitName."'";
										$db = Zend_Db_Table::getDefaultAdapter();
										$sql=$db->query($selectQuery1);
										$resultarray = $sql->fetchAll();
				
										if(!empty($resultarray)){
											$menuName = ucfirst($resultarray[0]['menuName']);									
										}else{
											$menuName = ucfirst($pagesplitName);									
										}	
									}
								}
	                            else
	                            {
	                                $selectQuery1 = "select m.menuName from main_menu m where m.url = '/".$pagesplitName."'";
	                                $db = Zend_Db_Table::getDefaultAdapter();
	                                $sql=$db->query($selectQuery1);
	                                $resultarray = $sql->fetchAll();
	        
	                                if(!empty($resultarray)){
	                                    $menuName = ucfirst($resultarray[0]['menuName']);									
	                                }else{
										$menuName = ucfirst($pagesplitName);
										if($menuName == 'Appraisalstatus')
											$menuName = '';
									}	
									
	                            }
	                        }
	                        else
	                        {
	                            if($pagesplitName == 'viewsettings')
	                            {
	                                $flagnumber = substr($pagesplitLink, -1);
	                                if($flagnumber !='')
	                                {
	                                    if($flagnumber == 1)
	                                        $menuName = "Settings-Widgets";
	                                    else if($flagnumber == 2)
	                                        $menuName = "Settings-Shortcuts";
	                                }
	                                else
	                                    $menuName = "Settings";
	                            }
	                            else
	                                $menuName = ucfirst($pagesplitName);
	                        }
                        }
				// Display of add, edit or view in each list item                                                                                                       				
				// Checking condition for my employee and my details static controllers
                        if($pagesplitName !='' && in_array($pagesplitName,$myDetailsEmployeesarr))
                        {
                            if($pagesplit_action != '')
                            {
                                        
                                if($pagesplitName == 'myemployees')
                                {
                                    if(array_key_exists($pagesplit_action, $myemployees_arr) !== false)
                                        $menuName .= $myemployees_arr[$pagesplit_action];	
                                  	else if(array_key_exists($pagesplit_action, $myemployeesedit_arr) !== false)
                                        $menuName .= $myemployeesedit_arr[$pagesplit_action];
                                  	else if($actionName == 'add')
                                  		$menuName .= '-Add';                                        														
                                }
                                else
                                {
                                    if(array_key_exists($pagesplit_action, $mydetails_arr) !== false)
                                        $menuName .= $mydetails_arr[$pagesplit_action];
                                }
                            }
                            else if($pagesplit_action == '')
                            {
                                if($pagesplitName == 'mydetails')
                                    $menuName .= TAB_EMP_OFFICIAL."-View";
                            }					
                            else
                            {
                                $menuName .= '';
                            }
                        }
				// For Reports Module checking with global array and printing with key value of that array
                        else if($pagesplitName != '' && $pagesplitName == 'reports')
                        {
                            if($pagesplit_action != '')
                            {
                                if(array_key_exists($pagesplit_action,$reportsArr) !== false)
                                    $menuName .=$reportsArr[$pagesplit_action]; 
                                else if($pagesplit_module == 'timemanagement')
                                	$menuName ='Analytics-Time Management';    
                            }		    		  
                        }
                    	else if($pagesplitName != '' && $pagesplitName == 'servicerequests')
                        {
                            if($pagesplit_action != '')
                            {
                            	$param_t = isset($pagesplit['t'])?sapp_Global::_decrypt($pagesplit['t']):"";
                                $param_v = isset($pagesplit['v'])?sapp_Global::_decrypt($pagesplit['v']):"";
                                $service_menu = sapp_Helper::sd_menu_names();
                                $service_action_arr = sapp_Helper::sd_action_names();
                                if($param_t != '' && isset($service_menu[$param_t]))
                                    $menuName .= " - ".$service_menu[$param_t];    
                                if($param_v != '' && isset($service_action_arr[$param_v]))
                                    $menuName .= " - ".$service_action_arr[$param_v];    
                            }		    		  
                        }
                    	else if($pagesplitName != '' && $pagesplitName == 'appraisalinit')
                        {
                            if($pagesplit_action != '')
                            {
                            	if($pagesplit_action == 'edit')
                                    $menuName .= '-Edit';
                                if($pagesplit_action == 'view')
                                    $menuName .= '-View';
                                if($pagesplit_action == 'add')
                                    $menuName .= '-Add';        
                            	if($pagesplit_action == 'assigngroups')
                                    $menuName .= '-Edit Questions';
                                if($pagesplit_action == 'confmanagers')
                                    $menuName .= '-Edit Managers';
                                if($pagesplit_action == 'viewassigngroups')
                                    $menuName .= '-View Questions';        
                                if($pagesplit_action == 'viewconfmanagers')
                                    $menuName .= '-View Managers';        
                            }		    		  
                        }
                    	else if($pagesplitName != '' && $pagesplitName == 'appraisalstatus')
                        {
                            if($pagesplit_action != '')
                            {
                            	if($pagesplit_action == 'employee')
                                    $menuName .= 'Employee Status';
                                if($pagesplit_action == 'manager')
                                    $menuName .= 'Manager Status';    
                            }		    		  
                        }
                        else
                        {				    
                            if($pagesplit_action != '' && $pagesplit_action != 'employeetimesheet' && $pagesplitName !='reports')
                            { 
	                            if($pagesplit_module == 'timemanagement')
	                            {
	                            	if($pagesplitName != '') {
	                            		if($pagesplitName == 'defaulttasks')
	                            			$menuName ='Default Tasks';
	                            		else if($pagesplitName == 'emptimesheets')
	                            			$menuName ='Employee Timesheets';
	                            		else if($pagesplitName == 'employeeprojects')
	                            			$menuName ='Projects';
	                            		else 
	                            			$menuName = ucfirst($pagesplitName);	
	                            	}
	                            	if($pagesplit_action!='' && $pagesplit_action!='index')	
	                            			$menuName .='-'.ucfirst($pagesplit_action);
	                            }
	                            else 
	                            {
		                                if($pagesplit_action == 'add')
		                                    $menuName .= '-Add';
										else if($pagesplit_action == 'addmultiple')
											$menuName .= '-Add Multiple';
		                                else if($pagesplit_action == 'edit' && $pagesplit_idname == 'yes')
		                                    $menuName .= '-Edit';
		                                else if($pagesplit_action == 'edit')
		                                    $menuName .= '-Add';
		                                else if($pagesplit_action == 'view')
		                                    $menuName .= '-View';
		                                else if($pagesplit_action == 'viewsettings')
		                                    $menuName = 'Settings';
		                                else if($pagesplit_action == 'viewprofile')
		                                    $menuName = 'Profile';
		                                else if($pagesplit_action == 'changepassword')
		                                    $menuName = 'Change password';
		                                else if($pagesplit_action == 'emailsettings')
		                                    $menuName = 'Email Settings';
		                                else if($pagesplit_action == 'upgradeapplication')
		                                    $menuName = 'Upgrade Application';   
	                            }     
                            }
					
                        }
                        if($menuName)
                        {
                        	 if($pagesplit_module == 'timemanagement'){
                        	 	if($pagesplit_action != 'employeetimesheet'){
							 	   echo '<li><span id="redirectlink"  title = "'.$menuName.'" onclick ="redirecttolink(\''.$pagesplitLink.'\',\''.$pagesplit_module.'\');">'.$menuName.'</span><a href="javascript:void(0);" onClick="closetab(this,\''.$pagesplitName.'\',\''.$pagesplitLink.'\')"></a></li>';
                        	 	}
                        	 }else if($pagesplit_module == 'expenses' || $pagesplit_module == 'assets' || $pagesplit_module == 'exit')
							 {
								
							 	   echo '<li><span id="redirectlink"  title = "'.$menuName.'" onclick ="redirecttolink(\''.$pagesplitLink.'\',\''.$pagesplit_module.'\');">'.$menuName.'</span><a href="javascript:void(0);" onClick="closetab(this,\''.$pagesplitName.'\',\''.$pagesplitLink.'\')"></a></li>';

							 }
							else 
							 	echo '<li><span id="redirectlink"  title = "'.$menuName.'" onclick ="redirecttolink(\''.$pagesplitLink.'\',\'\');">'.$menuName.'</span><a href="javascript:void(0);" onClick="closetab(this,\''.$pagesplitName.'\')"></a></li>';	
                        }
                    }
			
		}//end of display


        if(isset($recentlyViewed->recentlyViewedObject))
        {                
            if(sizeof($recentlyViewed->recentlyViewedObject) > 3 && $pageLink != BASE_URL && !in_array($pageName."!@#".$pageLink, $recentlyViewed->recentlyViewedObject))
            {
                array_shift($recentlyViewed->recentlyViewedObject);
            }
            if($pageName != 'public' && $pageName != 'welcome' && $controllerName !='error' )
            {
                if(!in_array('PIE.htc', $tmpPageLink))
                {                        
                    if($pageLink != BASE_URL && $controllerName !='index' && $actionName != 'welcome')
                    {                        
                        if($this->recentlyviewed_helper($pageLink, $recentlyViewed->recentlyViewedObject) === true)
                        {
                            if($controllerName == 'servicerequests')
                                array_push($recentlyViewed->recentlyViewedObject,array('url' => $burl,'controller_name' => $controllerName,'action_name' => $actionName,'id_name' => $id_name,'t'=> isset($params['t'])?$params['t']:"",'v'=> isset($params['v'])?$params['v']:""));
                            else if($controllerName == 'policydocuments')
								array_push($recentlyViewed->recentlyViewedObject,array('url' => $burl,'controller_name' => $controllerName,'action_name' => $actionName,'id_name' => $id_name,'id'=> isset($params['cat'])?$params['cat']:"",'id'=> isset($params['id'])?$params['id']:""));
							else 
                                array_push($recentlyViewed->recentlyViewedObject,array('url' => $burl,'controller_name' => $controllerName,'action_name' => $actionName,'id_name' => $id_name,'module_name'=>$moduleName));
                        }
                    }
                }
            }
        }
        else
        {
            $recentlyViewed->recentlyViewedObject = array();                                                
            if($pageLink != BASE_URL && $controllerName !='index' && $actionName != 'welcome'  && $controllerName !='error' && !in_array('PIE.htc', $tmpPageLink))    
            {
                if($this->recentlyviewed_helper($pageLink, $recentlyViewed->recentlyViewedObject) === true)
                {
                    if($controllerName == 'servicerequests')
                        array_push($recentlyViewed->recentlyViewedObject,array('url' => $burl,'controller_name' => $controllerName,'action_name' => $actionName,'id_name' => $id_name,'t'=> isset($params['t'])?$params['t']:"",'v'=> isset($params['v'])?$params['v']:""));
                    else if($controllerName == 'policydocuments')
						array_push($recentlyViewed->recentlyViewedObject,array('url' => $burl,'controller_name' => $controllerName,'action_name' => $actionName,'id_name' => $id_name,'id'=> isset($params['cat'])?$params['cat']:"",'id'=> isset($params['id'])?$params['id']:"",'module_name'=>$moduleName));
					else 
                        array_push($recentlyViewed->recentlyViewedObject,array('url' => $burl,'controller_name' => $controllerName,'action_name' => $actionName,'id_name' => $id_name,'module_name'=>$moduleName));
                }
            }
        }
        echo '</ul></div>';
    }//end of recently view function
    
    public function recentlyviewed_helper($url,$recently_arr)
    {
        if(!empty($recently_arr) && $url != '')
        {
            $k = 0;
            foreach($recently_arr as $rarr)
            {
                if($rarr['url'] == $url)
                    $k++;
            }
            if($k > 0)
                return false;
        }
        return true;
    }
}//end of class