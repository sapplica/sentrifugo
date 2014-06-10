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

class Default_MyemployeesController extends Zend_Controller_Action
{

    private $options;
	public function preDispatch()
	{
		 
		
	}
	
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
		
    }

    public function indexAction()
    {
	    $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		} 
	    $employeeModel = new Default_Model_Employee();	
		$myemployeesModel = new Default_Model_Myemployees();	
		$call = $this->_getParam('call');
		if($call == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		
		$view = Zend_Layout::getMvcInstance()->getView();		
		$objname = $this->_getParam('objname');
		$refresh = $this->_getParam('refresh');
		$dashboardcall = $this->_getParam('dashboardcall',null);
		$data = array();$id='';		$searchQuery = '';		$searchArray = array();		$tablecontent = '';
		
		if($refresh == 'refresh')
		{
			if($dashboardcall == 'Yes')
				$perPage = DASHBOARD_PERPAGE;
			else	
				$perPage = PERPAGE;
							
			$sort = 'DESC';$by = 'e.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';	$searchArray = array();
			//$sort = 'DESC';$by = 'e.modifieddate';$perPage = 10;$pageNo = 1;$searchData = '';
		}
		else 
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'e.modifieddate';
			if($dashboardcall == 'Yes')
				$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
			else 
				$perPage = $this->_getParam('per_page',PERPAGE);
			
			$pageNo = $this->_getParam('page', 1);
			$searchData = $this->_getParam('searchData');	
			$searchData = rtrim($searchData,',');			
		}
		$dataTmp = $myemployeesModel->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$loginUserId,$loginUserId);			
		
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
    }
	
	public function viewAction()
	{	
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		$objName = 'employee';
		$employeeform = new Default_Form_employee();
		$employeeform->removeElement("submit");
		$elements = $employeeform->getElements();
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)
			{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
					$element->setAttrib("disabled", "disabled");
				}
        	}
        }
		if($id)	
		{
			$employeeModal = new Default_Model_Employee();
			$usersModel = new Default_Model_Users();
			$employmentstatusModel = new Default_Model_Employmentstatus();
			$busineesUnitModel = new Default_Model_Businessunits();
			$deptModel = new Default_Model_Departments();
			$role_model = new Default_Model_Roles();
			$user_model = new Default_Model_Usermanagement();
			$candidate_model = new Default_Model_Candidatedetails(); 
			$jobtitlesModel = new Default_Model_Jobtitles();
			$positionsmodel = new Default_Model_Positions();
			$prefixModel = new Default_Model_Prefix();
			//$data = $employeeModal->getsingleEmployeeData($id);
			$data = $employeeModal->getsingleEmployeeData($id);
			//echo "<pre>";print_r($data);exit;
			if($data == 'norows')
			{
				$this->view->rowexist = "norows";
			}
			else if(!empty($data))
			{
				$this->view->rowexist = "rows";
                                
				//$data = $employeeModal->getActiveEmployeeData($id);
				$data = $data[0]; 
				//$employeeData = $usersModel->getUserDetailsByIDandFlag($data['user_id']);
				$employeeData = $employeeModal->getsingleEmployeeData($data['user_id']);
				//echo "<pre>";print_r($employeeData);die;	
				/*if(sizeof($employeeData) > 0)
				{ 			
					$employeeform->user_id->addMultiOption($employeeData[0]['id'],$employeeData[0]['userfullname']);
				} */
				//$roles_arr = $role_model->getRolesList_UM();
				/*$roles_arr = $role_model->getRolesList_EMP();   
				echo "<pre>";print_r($roles_arr);exit;
				if(sizeof($roles_arr) > 0)
				{ 			                    
					$employeeform->emprole->addMultiOptions(array(''=>'Select Role')+$roles_arr);
				}*/
				$roles_arr = $role_model->getRolesDataByID($data['emprole']); 
				if(sizeof($roles_arr) > 0)
				{ 			                    
					$employeeform->emprole->addMultiOption($roles_arr[0]['id'].'_'.$roles_arr[0]['group_id'],utf8_encode($roles_arr[0]['rolename']));
					
				}
				
                $referedby_options = $user_model->getRefferedByForUsers();
				//$reportingManagerData = $usersModel->getReportingManagerList($data['department_id'],$data['id']);	
				//echo "<pre>";print_r($reportingManagerData);exit;
				/*if(!empty($reportingManagerData))
				{ 			
					$employeeform->reporting_manager->addMultiOption('','Select a Reporting Manager');
					foreach ($reportingManagerData as $reportingManagerres){
						$employeeform->reporting_manager->addMultiOption($reportingManagerres['id'],$reportingManagerres['userfullname']);
					}
				}*/
				/*else
				{
					$reportingManagerData = $usersModel->getUserDetailsByIDandFlag($data['reporting_manager']);
					echo "<pre>";print_r($reportingManagerData);exit;
					$employeeform->reporting_manager->addMultiOption($reportingManagerData[0]['id'],$reportingManagerData[0]['userfullname']);
				}*/
				$reportingManagerData = $usersModel->getReportingManagerList($data['department_id'],$data['id'],$roles_arr[0]['group_id']);	
				//echo "<pre>";print_r($reportingManagerData);exit;
				if(!empty($reportingManagerData))
				{ 			
					$employeeform->reporting_manager->addMultiOption('','Select a Reporting Manager');
					if($roles_arr[0]['group_id'] == MANAGEMENT_GROUP)
						$employeeform->reporting_manager->addMultiOption(SUPERADMIN,'Super Admin');
					
					foreach ($reportingManagerData as $reportingManagerres){
						$employeeform->reporting_manager->addMultiOption($reportingManagerres['id'],$reportingManagerres['name']);
					}
				}
				$employeeform->setDefault('reporting_manager',$data['reporting_manager']);
				
				$employmentStatusData = $employmentstatusModel->getempstatuslist();
				//echo "<pre>";print_r($employmentStatusData);exit;
				if(sizeof($employmentStatusData) > 0)
				{ 			
						$employeeform->emp_status_id->addMultiOption('','Select a Employment Status');
					foreach ($employmentStatusData as $employmentStatusres){
						$employeeform->emp_status_id->addMultiOption($employmentStatusres['workcodename'],$employmentStatusres['statusname']);
					}
				}
		
				$businessunitData = $busineesUnitModel->getDeparmentList(); 
				//echo "<pre>";print_r($businessunitData);exit;
				if(sizeof($businessunitData) > 0)
				{ 			
					$employeeform->businessunit_id->addMultiOption('0','No Business Unit');
					foreach ($businessunitData as $businessunitres){
						$employeeform->businessunit_id->addMultiOption($businessunitres['id'],$businessunitres['unitname']);
					}
				}
				
				$departmentsData = $deptModel->getDepartmentList($data['businessunit_id']);
				//echo "<pre>";print_r($departmentsData);exit;
				if(sizeof($departmentsData) > 0)
				{ 			
						$employeeform->department_id->addMultiOption('','Select a Department');
					foreach ($departmentsData as $departmentsres){
						$employeeform->department_id->addMultiOption($departmentsres['id'],$departmentsres['deptname']);
					}
				} 
				
				
				$jobtitleData = $jobtitlesModel->getJobTitleList(); 			
				if(sizeof($jobtitleData) > 0)
				{ 			
						$employeeform->jobtitle_id->addMultiOption('','Select a Job Title');
					foreach ($jobtitleData as $jobtitleres){
						$employeeform->jobtitle_id->addMultiOption($jobtitleres['id'],$jobtitleres['jobtitlename']);
					}
				}
			
				$positionlistArr = $positionsmodel->getPositionList($data['jobtitle_id']); 
				if(sizeof($positionlistArr) > 0)
				{ 			
						$employeeform->position_id->addMultiOption('','Select a Position');
					foreach ($positionlistArr as $positionlistres){
						$employeeform->position_id->addMultiOption($positionlistres['id'],$positionlistres['positionname']);
					}
				} 

                if(isset($data['prefix_id']) && $data['prefix_id'] !='')
				{
					$singlePrefixArr = $prefixModel->getsinglePrefixData($data['prefix_id']);
					if($singlePrefixArr !='norows')
						$employeeform->prefix_id->addMultiOption($singlePrefixArr[0]['id'],$singlePrefixArr[0]['prefix']);
				}				
				
				$employeeform->populate($data);
				$employeeform->setDefault('user_id',$data['user_id']);
				
				$employeeform->setDefault('emp_status_id',$data['emp_status_id']);
				$employeeform->setDefault('businessunit_id',$data['businessunit_id']);
				$employeeform->setDefault('jobtitle_id',$data['jobtitle_id']);
				$employeeform->setDefault('department_id',$data['department_id']);
				$employeeform->setDefault('position_id',$data['position_id']);
				$date_of_joining = sapp_Global::change_date($data['date_of_joining'],'view');
				$employeeform->date_of_joining->setValue($date_of_joining);
				
				if($data['date_of_leaving'] !='' && $data['date_of_leaving'] !='0000-00-00')
				{
						
					$date_of_leaving = sapp_Global::change_date($data['date_of_leaving'], 'view');
					$employeeform->date_of_leaving->setValue($date_of_leaving);
				}
				if($data['modeofentry'] != 'Direct')
				{                                                                        
					$employeeform->rccandidatename->setValue($data['userfullname']);
				}
				if(sizeof($referedby_options) > 0 && $data['candidatereferredby'] != '' && $data['candidatereferredby'] != 0)
				{ 			                    
					$employeeform->candidatereferredby->setValue($referedby_options[$data['candidatereferredby']]);
				}
				if($data['rccandidatename'] != '' && $data['rccandidatename']!=0)
				{
					$cand_data = $candidate_model->getCandidateById($data['rccandidatename']);
					$data['requisition_code'] = $cand_data['requisition_code'];
				}
				
				$employeeform->setAttrib('action',DOMAIN.'employee/edit/id/'.$id);
				$this->view->id = $id;
				$this->view->form = $employeeform;
				$this->view->employeedata = (!empty($employeeData))?$employeeData[0]:"";
				$this->view->messages = $this->_helper->flashMessenger->getMessages();
				$this->view->data = $data;	
                $this->view->controllername = $objName;
				$this->view->id = $id;
			}	
		}	
	}
        
	public function perviewAction()
	{
	    if(defined('EMPTABCONFIGS'))
		{
		    $empOrganizationTabs = explode(",",EMPTABCONFIGS);
			if(in_array('emppersonaldetails',$empOrganizationTabs))
			{
 		        $auth = Zend_Auth::getInstance();
				if($auth->hasIdentity()){
							$loginUserId = $auth->getStorage()->read()->id;
							$loginUserGroup = $auth->getStorage()->read()->group_id;
							$loginUserRole = $auth->getStorage()->read()->emprole;
				} 	
				$id = $this->getRequest()->getParam('userid');
				if($id == '')
				$id = $loginUserId;
				$callval = $this->getRequest()->getParam('call');
				if($callval == 'ajaxcall')
					$this->_helper->layout->disableLayout();
				$objName = 'emppersonaldetails';
				$emppersonaldetailsform = new Default_Form_emppersonaldetails();
				$employeeModal = new Default_Model_Employee();
				$emppersonaldetailsform->removeElement("submit");
				$elements = $emppersonaldetailsform->getElements();
				if(count($elements)>0)
				{
					foreach($elements as $key=>$element)
					{
						if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
						$element->setAttrib("disabled", "disabled");
							}
					}
				}
				try
				{
					if($id)
					{
						$empdata = $employeeModal->getsingleEmployeeData($id);
						if($empdata == 'norows')
						{
							$this->view->rowexist = "norows";
							$this->view->empdata = "";
						}
						else
						{  $this->view->rowexist = "rows";
						
							if(!empty($empdata))
							{
								$empperdetailsModal = new Default_Model_Emppersonaldetails();
								$usersModel = new Default_Model_Users();
								$genderModel = new Default_Model_Gender();
								$maritalstatusmodel = new Default_Model_Maritalstatus();
								$nationalitymodel = new Default_Model_Nationality();
								$ethniccodemodel = new Default_Model_Ethniccode();
								$racecodemodel = new Default_Model_Racecode();	
								$languagemodel = new Default_Model_Language();
								//$identitydocumentsModel = new Default_Model_Identitydocuments();	
								//$identityDocumentArr = $identitydocumentsModel->getIdentitydocumnetsrecord();
								/*
									Purpose:	Only management,HR,Superadmin can see the employee's identity documents.
									Modified Date:	11/13/2013
									Modified By:	Yamini
								*/
								if($loginUserGroup == MANAGEMENT_GROUP || $loginUserGroup == HR_GROUP || $loginUserRole == SUPERADMINROLE)
								{
									$identitydocumentsModel = new Default_Model_Identitydocuments();	
									$identityDocumentArr = $identitydocumentsModel->getIdentitydocumnetsrecord();
								}
								$data = $empperdetailsModal->getsingleEmpPerDetailsData($id);
								if(!empty($identityDocumentArr))
									$this->view->identitydocument = $identityDocumentArr;
								else
									$this->view->identitydocument = '';
												   
							  if(!empty($data))
								{	

									if(isset($data[0]['genderid']) && $data[0]['genderid'] !='')
									{						
										$genderlistArr = $genderModel->getGenderDataByID($data[0]['genderid']);
										if(sizeof($genderlistArr)>0)
										{
											$emppersonaldetailsform->genderid->addMultiOption($genderlistArr[0]['id'],$genderlistArr[0]['gendername']);
											
										}
									}
									
									if(isset($data[0]['maritalstatusid']) && $data[0]['maritalstatusid'] !='')
									{
										$maritalstatuslistArr = $maritalstatusmodel->getsingleMaritalstatusData($data[0]['maritalstatusid']);
										if($maritalstatuslistArr !='norows')
										{
												$emppersonaldetailsform->maritalstatusid->addMultiOption($maritalstatuslistArr[0]['id'],$maritalstatuslistArr[0]['maritalstatusname']);
										}
									}
									
									if(isset($data[0]['nationalityid']) && $data[0]['nationalityid'] !='')
									{
										$nationalitylistArr = $nationalitymodel->getNationalityDataByID($data[0]['nationalityid']);
										if(sizeof($nationalitylistArr)>0)
										{
											$emppersonaldetailsform->nationalityid->addMultiOption($nationalitylistArr[0]['id'],$nationalitylistArr[0]['nationalitycode']);
										}
									}
									
									if(isset($data[0]['ethniccodeid']) && $data[0]['ethniccodeid'] !='')
									{
										$singleethniccodeArr = $ethniccodemodel->getsingleEthnicCodeData($data[0]['ethniccodeid']);
										//echo "<pre>";print_r($singleethniccodeArr);exit;
										  if($singleethniccodeArr !='norows')
											$emppersonaldetailsform->ethniccodeid->addMultiOption($singleethniccodeArr[0]['id'],$singleethniccodeArr[0]['ethnicname']);
									}
									
									if(isset($data[0]['racecodeid']) && $data[0]['racecodeid'] !='')
									{
										$singleracecodeArr = $racecodemodel->getsingleRaceCodeData($data[0]['racecodeid']);
										  if($singleracecodeArr !='norows')
											$emppersonaldetailsform->racecodeid->addMultiOption($singleracecodeArr[0]['id'],$singleracecodeArr[0]['racename']);
									}
									
									if(isset($data[0]['languageid']) && $data[0]['languageid'] !='')
									{
										$singlelanguageArr = $languagemodel->getLanguageDataByID($data[0]['languageid']);
										  if(!empty($singlelanguageArr))
											$emppersonaldetailsform->languageid->addMultiOption($singlelanguageArr[0]['id'],$singlelanguageArr[0]['languagename']);
									}
								
										$emppersonaldetailsform->populate($data[0]);
										
										$dob = sapp_Global::change_date($data[0]["dob"], 'view');
										$emppersonaldetailsform->dob->setValue($dob);
										if($data[0]['celebrated_dob'] !='')
										{
										
											$celebrated_dob = sapp_Global::change_date($data[0]["celebrated_dob"], 'view');
											$emppersonaldetailsform->celebrated_dob->setValue($celebrated_dob);
										}
										
								}
								$this->view->controllername = $objName;
								$this->view->data = $data;
								$this->view->id = $id;
								$this->view->form = $emppersonaldetailsform;
								$this->view->employeedata = $empdata[0];
							}
							$this->view->empdata = $empdata;				
						}
					}
				}
				catch(Exception $e)
				{
					$this->view->rowexist = "norows";
				}
			}	
			else
			{
			 $this->_redirect('error');
		    }
        }
		else{
			$this->_redirect('error');
		}			
	}
	
	public function comviewAction()
	{
	    if(defined('EMPTABCONFIGS'))
		{
		 $empOrganizationTabs = explode(",",EMPTABCONFIGS);
			if(in_array('empcommunicationdetails',$empOrganizationTabs))
			{
				$auth = Zend_Auth::getInstance();
				$empdata =array();
				$empDeptdata=array();
				$empDept = '';
				if($auth->hasIdentity()){
							$loginUserId = $auth->getStorage()->read()->id;
				} 	
				$id = $this->getRequest()->getParam('userid');
				if($id == '')		$id = $loginUserId;
				$callval = $this->getRequest()->getParam('call');
				if($callval == 'ajaxcall')
					$this->_helper->layout->disableLayout();
					
				$objName = 'empcommunicationdetails';
				$empcommdetailsform = new Default_Form_empcommunicationdetails();
				$empcommdetailsform->removeElement("submit");
				$elements = $empcommdetailsform->getElements();
				if(count($elements)>0)
				{
					foreach($elements as $key=>$element)
					{
						if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
						$element->setAttrib("disabled", "disabled");
							}
					}
				}
				try
				{
					if($id)
					{
						$employeeModal = new Default_Model_Employee();
						$empdata = $employeeModal->getsingleEmployeeData($id);
						
						if($empdata == 'norows')
						{
							$this->view->rowexist = "norows";
							$this->view->empdata = "";
						}
						else
						{
							$this->view->rowexist = "rows";
							//	$empdata = $employeeModal->getActiveEmployeeData($id);
							//echo "<pre>";print_r($empdata);die;
							if(!empty($empdata))
							{ 
								$empDept = $empdata[0]['department_id'];
							
								/*//TO get the Employee  profile information....
								$usersModel = new Default_Model_Users();
								$employeeData = $usersModel->getUserDetailsByIDandFlag($id);
								//echo "Employee Data : <pre>";print_r($employeeData);die;
								*/
								$empcommdetailsModal = new Default_Model_Empcommunicationdetails();
								$usersModel = new Default_Model_Users();
								$countriesModel = new Default_Model_Countries();
								$statesmodel = new Default_Model_States();
								$citiesmodel = new Default_Model_Cities();
								$orgInfoModel = new Default_Model_Organisationinfo();
								$msgarray = array();
								
								$deptModel = new Default_Model_Departments();
								if($empDept !='')
								$departmentAddress = $usersModel->getDepartmentAddress($empDept);
							
								$data = $empcommdetailsModal->getsingleEmpCommDetailsData($id);
								if(!empty($data))
								{
									
									$countrieslistArr = $countriesModel->getCountryCode($data[0]['perm_country']);
									if(sizeof($countrieslistArr)>0)
									{
										$empcommdetailsform->perm_country->addMultiOption('','Select Country');
										foreach ($countrieslistArr as $countrieslistres)
										{
											$empcommdetailsform->perm_country->addMultiOption($countrieslistres['id'],$countrieslistres['country_name']);
										}
									}
								  
									$statePermlistArr = $statesmodel->getStatesList($data[0]['perm_country']);
									if(sizeof($statePermlistArr)>0)
									{
										$empcommdetailsform->perm_state->addMultiOption('','Select State');
										foreach($statePermlistArr as $statelistres)	
										 {
											$empcommdetailsform->perm_state->addMultiOption($statelistres['id'].'!@#'.$statelistres['state_name'],$statelistres['state_name']); 
										 }   						 
									}
									$cityPermlistArr = $citiesmodel->getCitiesList($data[0]['perm_state']);
									if(sizeof($cityPermlistArr)>0)
									{
										$empcommdetailsform->perm_city->addMultiOption('','Select City');
										foreach($cityPermlistArr as $cityPermlistres)	
										{
											$empcommdetailsform->perm_city->addMultiOption($cityPermlistres['id'].'!@#'.$cityPermlistres['city_name'],$cityPermlistres['city_name']); 
										}   						 
									}
									if($data[0]['current_country']!='' && $data[0]['current_state'] !='')
									{
										
										$countriesArr = $countriesModel->getCountryCode($data[0]['current_country']);
										if(sizeof($countriesArr)>0)
										{
											$empcommdetailsform->current_country->addMultiOption('','Select Country');
											foreach ($countriesArr as $countrieslistres)
											{
												$empcommdetailsform->current_country->addMultiOption($countrieslistres['id'],$countrieslistres['country_name']);
											}
										}
										
										$statecurrlistArr = $statesmodel->getStatesList($data[0]['current_country']); 
										if(sizeof($statecurrlistArr)>0)
										{
											$empcommdetailsform->current_state->addMultiOption('','Select State');
											foreach($statecurrlistArr as $statecurrlistres)	
											 {
												$empcommdetailsform->current_state->addMultiOption($statecurrlistres['id'].'!@#'.$statecurrlistres['state_name'],$statecurrlistres['state_name']); 
											 }   						 
										}
										$currstateNameArr = $statesmodel->getStateName($data[0]['current_state']);
										
									}
									if($data[0]['current_country']!='' && $data[0]['current_state'] !='' && $data[0]['current_city']!='')
									{
										$cityCurrlistArr = $citiesmodel->getCitiesList($data[0]['current_state']); 
										
										if(sizeof($cityCurrlistArr)>0)
										{
											$empcommdetailsform->current_city->addMultiOption('','Select State');
											foreach($cityCurrlistArr as $cityCurrlistres)	
											 {
												$empcommdetailsform->current_city->addMultiOption($cityCurrlistres['id'].'!@#'.$cityCurrlistres['city_name'],$cityCurrlistres['city_name']); 
											 }   						 
										}
										$currcityNameArr = $citiesmodel->getCityName($data[0]['current_city']);
									}	
									$permstateNameArr = $statesmodel->getStateName($data[0]['perm_state']);
									$permcityNameArr = $citiesmodel->getCityName($data[0]['perm_city']);
									$empcommdetailsform->populate($data[0]);	
									$empcommdetailsform->setDefault('perm_country',$data[0]['perm_country']);
									$empcommdetailsform->setDefault('perm_state',$permstateNameArr[0]['id'].'!@#'.$permstateNameArr[0]['statename']);
									$empcommdetailsform->setDefault('perm_city',$permcityNameArr[0]['id'].'!@#'.$permcityNameArr[0]['cityname']);
									if($data[0]['current_country'] != '')   
									   $empcommdetailsform->setDefault('current_country',$data[0]['current_country']);
									if($data[0]['current_state'] !='')
									   $empcommdetailsform->setDefault('current_state',$currstateNameArr[0]['id'].'!@#'.$currstateNameArr[0]['statename']);
									if($data[0]['current_city'] != '')   
									   $empcommdetailsform->setDefault('current_city',$currcityNameArr[0]['id'].'!@#'.$currcityNameArr[0]['cityname']);
									
							}
							$this->view->controllername = $objName;
							$this->view->data = $data;
							$this->view->dataArray = $departmentAddress;
							$this->view->id = $id;
							$this->view->employeedata = $empdata[0];
							$this->view->form = $empcommdetailsform;
						}
						$this->view->empdata = $empdata;
					}
				}
				}
				catch(Exception $e)
				{	
					 $this->view->rowexist = "norows";
				}
			}
			else
			{
			 $this->_redirect('error');
		    }
        }
		else
		{
			$this->_redirect('error');
		}			
	}
	
	public function skillsviewAction()
	{
	    if(defined('EMPTABCONFIGS'))
		{
		    $empOrganizationTabs = explode(",",EMPTABCONFIGS);
			if(in_array('emp_skills',$empOrganizationTabs))
			{
				$auth = Zend_Auth::getInstance();
				if($auth->hasIdentity()){
							$loginUserId = $auth->getStorage()->read()->id;
				} 
				$call = $this->_getParam('call');
				if($call == 'ajaxcall')
				{	
					$this->_helper->layout->disableLayout();
					$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
				}
				$id = $this->getRequest()->getParam('userid');
				if($id == '')		$id = $loginUserId;
				$Uid = ($id)?$id:$userID;//die;
				$employeeModal = new Default_Model_Employee();
				try
				{
					$empdata = $employeeModal->getsingleEmployeeData($Uid);
					//echo "<pre>";print_r($empdata);die;
					if($empdata == 'norows')
					{
					  $this->view->rowexist = "norows";
					   $this->view->empdata = "";
					}
					else
					{
						$this->view->rowexist = "rows";
						//$empdata = $employeeModal->getActiveEmployeeData($id);
						if(!empty($empdata))
						{ 
							$empskillsModel = new Default_Model_Empskills();	
							$view = Zend_Layout::getMvcInstance()->getView();		
							$objname = $this->_getParam('objname');
							$refresh = $this->_getParam('refresh');
							$data = array();$searchQuery = '';	$searchArray = array();	$tablecontent = '';$levelsArr=array();$empcompetencyLevelsArr =array();
							if($refresh == 'refresh')
							{
								$sort = 'DESC';$by = 'e.modifieddate';$perPage = 10;$pageNo = 1;$searchData = '';
							}
							else 
							{
								$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
								$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'e.modifieddate';
								$perPage = $this->_getParam('per_page',10);
								$pageNo = $this->_getParam('page', 1);
								$searchData = $this->_getParam('searchData');	
								$searchData = rtrim($searchData,',');			
								/** search from grid - START **/
								$searchData = $this->_getParam('searchData');	
								if($searchData != '' && $searchData!='undefined')
								{
									$searchValues = json_decode($searchData);
									foreach($searchValues as $key => $val)
									{
										if($key == 'year_skill_last_used')
										{
											$searchQuery .= " ".$key." like '%".  sapp_Global::change_date($val,'database')."%' AND ";
										}
										else 
											$searchQuery .= " ".$key." like '%".$val."%' AND ";
										$searchArray[$key] = $val;
									}
									//echo "levels Arr <pre>";print_r($levelsArr);die;
									$searchQuery = rtrim($searchQuery," AND");						
								}
								/** search from grid - END **/
							}
									
							$objName = 'empskills';
							$tableFields = array('action'=>'Action','skillname'=>'Skillname','yearsofexp'=>'Years of Experiance','competencylevelid'=>'Competencylevel','year_skill_last_used'=>'Skills last used');
							
							$tablecontent = $empskillsModel->getEmpSkillsData($sort, $by, $pageNo, $perPage,$searchQuery,$Uid);     
							/*	Purpose:TO get drop down for search filters.Getting all competency levels from main_competencylevel table.
								Modified Date	:	21/10/2013.
								Modified By:	Yamini.
								*/
								$empcompetencyLevelsArr = $empskillsModel->empcompetencylevels($Uid);
								for($i=0;$i<sizeof($empcompetencyLevelsArr);$i++)
								{
									$levelsArr[$empcompetencyLevelsArr[$i]['id']] = $empcompetencyLevelsArr[$i]['competencylevel'];
								}
								//echo "levels Arr <pre>";print_r($levelsArr);die;
							/*if($Uid != "")
							{
								//TO dispaly EMployee Profile information.....
								$usersModel = new Default_Model_Users();
								$employeeData = $usersModel->getUserDetailsByIDandFlag($Uid);
								echo "Employee Data : <pre>";print_r($employeeData);
								echo "EMp data : ";print_r($empdata);
								die;
							}*/
							
							$dataTmp = array('userid'=>$Uid,
											'sort' => $sort,
											'by' => $by,
											'pageNo' => $pageNo,
											'perPage' => $perPage,				
											'tablecontent' => $tablecontent,
											'objectname' => $objName,
											'extra' => array(),
											'tableheader' => $tableFields,
											'jsGridFnName' => 'getEmployeeAjaxgridData',
											'jsFillFnName' => '',
											'searchArray' => $searchArray,
											'add'=>'add',
											'menuName'=>'Employee skills',
											'formgrid'=>'true',
											'unitId'=>$Uid,
											'call'=>$call,'context'=>'myteam',
											'search_filters' => array(
												'competencylevelid' => array('type'=>'select',
															'filter_data'=>array(''=>'All')+$levelsArr),
															'year_skill_last_used'=>array('type'=>'datepicker')
																
								)
								);			
							array_push($data,$dataTmp);
							$this->view->dataArray = $data;
							$this->view->call = $call;
							$this->view->employeedata = $empdata[0];
							$this->view->id = $id;
							$this->view->messages = $this->_helper->flashMessenger->getMessages();
						}
						$this->view->empdata = $empdata;		
					}
				}
				catch(Exception $e)
				{
					$this->view->rowexist = "norows";
				}
			}
			else
			{
			  $this->_redirect('error');
		    }
        }
		else
		{
			$this->_redirect('error');
		}			
	}
	
	public function expviewAction()
	{
	    if(defined('EMPTABCONFIGS'))
		{
		    $empOrganizationTabs = explode(",",EMPTABCONFIGS);
			if(in_array('experience_details',$empOrganizationTabs))
			{
				$auth = Zend_Auth::getInstance();
				$employeeData=array();
				if($auth->hasIdentity()){
							$loginUserId = $auth->getStorage()->read()->id;
				} 
				$userid = $this->getRequest()->getParam('userid');
				$call = $this->_getParam('call');
				if($call == 'ajaxcall')
				{
					$this->_helper->layout->disableLayout();
					$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
				}
				if($userid == '')			$userid = $loginUserId;
				$Uid = ($userid)?$userid:$userID;//die;
				
				$employeeModal = new Default_Model_Employee();
				$empdata = $employeeModal->getsingleEmployeeData($Uid);
				try
				{
					if($empdata == 'norows')
					{
					  $this->view->rowexist = "norows";
					   $this->view->empdata = "";
					}
					else
					{
						$this->view->rowexist = "rows";
						//$empdata = $employeeModal->getActiveEmployeeData($userid);
						if(!empty($empdata))
						{
							$experiencedetailsModel = new Default_Model_Experiencedetails();	
							$view = Zend_Layout::getMvcInstance()->getView();		
						
							$objname = $this->_getParam('objname');
							$refresh = $this->_getParam('refresh');
						
							$data = array();	$searchQuery = '';	$searchArray = array();		$tablecontent = '';
						
							if($refresh == 'refresh')
							{
								$sort = 'DESC';$by = 'modifieddate';$perPage = 10;$pageNo = 1;$searchData = '';
							}
							else 
							{
								$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
								$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'modifieddate';
								$perPage = $this->_getParam('per_page',10);
								$pageNo = $this->_getParam('page', 1);
								$searchData = $this->_getParam('searchData');	
								$searchData = rtrim($searchData,',');			
								/** search from grid - START **/
								$searchData = $this->_getParam('searchData');	
								if($searchData != '' && $searchData!='undefined')
								{
									$searchValues = json_decode($searchData);
									foreach($searchValues as $key => $val)
									{
										if($key == 'from_date' || $key == 'to_date')
										{
											$searchQuery .= " ".$key." like '%".  sapp_Global::change_date($val,'database')."%' AND ";
										}
										else
											$searchQuery .= " ".$key." like '%".$val."%' AND ";
										$searchArray[$key] = $val;
									}
									$searchQuery = rtrim($searchQuery," AND");					
								}
								/** search from grid - END **/
							}
							$objName = 'experiencedetails';
						
						$tableFields = array('action'=>'Action','comp_name'=>'Company Name','comp_website'=>'Company Website','designation'=>'Designation','from_date'=>'From','to_date'=>'To');
						
						$tablecontent = $experiencedetailsModel->getexperiencedetailsData($sort, $by, $pageNo, $perPage,$searchQuery,$userid);     
						
						/*if($userid != "")
						{
							//TO dispaly EMployee Profile information.....
							$usersModel = new Default_Model_Users();
							$employeeData = $usersModel->getUserDetailsByIDandFlag($userid);
							//echo "Employee Data : <pre>";print_r($employeeData);die;
						}*/
						$dataTmp = array('userid'=>$Uid,
							'sort' => $sort,
							'by' => $by,
							'pageNo' => $pageNo,
							'perPage' => $perPage,				
							'tablecontent' => $tablecontent,
							'objectname' => $objName,
							'extra' => array(),
							'tableheader' => $tableFields,
							'jsGridFnName' => 'getEmployeeAjaxgridData',
							'jsFillFnName' => '',
							'searchArray' => $searchArray,
							'add'=>'add',
							'menuName'=>'Experience Details',
							'formgrid' => 'true',
							'call'=>$call,'context'=>'myteam',
							'search_filters' => array(
											'from_date' =>array('type'=>'datepicker'),
											'to_date' =>array('type'=>'datepicker')											
											)
							);			
						
						array_push($data,$dataTmp);
						//echo "Data : <pre>";print_r($data);die;
						
						$this->view->id=$Uid;	//User_id sending to view for tabs navigation....
						$this->view->controllername = $objName;
						$this->view->dataArray = $data;
						$this->view->call = $call ;
						$this->view->employeedata = $empdata[0];
						$this->view->messages = $this->_helper->flashMessenger->getMessages();
					}	
					$this->view->empdata = $empdata;
				}
				}
				catch(Exception $e)
				{
					 $this->view->rowexist = "norows";
				}
			}
			else
			{
			 $this->_redirect('error');
		    }
        }
		else
		{
			$this->_redirect('error');
		} 			
	}
	
	public function eduviewAction()
	{
	    if(defined('EMPTABCONFIGS'))
		{
		    $empOrganizationTabs = explode(",",EMPTABCONFIGS);
			if(in_array('education_details',$empOrganizationTabs))
			{
				$auth = Zend_Auth::getInstance();
				if($auth->hasIdentity()){
							$loginUserId = $auth->getStorage()->read()->id;
				} 
				$call = $this->_getParam('call');
				if($call == 'ajaxcall')
				{
					$this->_helper->layout->disableLayout();
					$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
				}
							
				$userid = $this->getRequest()->getParam('userid');
				if($userid == '')			$userid = $loginUserId;
				$Uid = ($userid)?$userid:$userID;//die;
				$employeeModal = new Default_Model_Employee();
				try
				{
					$empdata = $employeeModal->getsingleEmployeeData($Uid);
					if($empdata == 'norows')
					{
					  $this->view->rowexist = "norows";
					  $this->view->empdata = "";
					}
					else
					{  
						$this->view->rowexist = "rows";
						//$empdata = $employeeModal->getActiveEmployeeData($userid);
						if(!empty($empdata))
						{
							$educationdetailsModel = new Default_Model_Educationdetails();	
										
							$view = Zend_Layout::getMvcInstance()->getView();		
							$objname = $this->_getParam('objname');
							$refresh = $this->_getParam('refresh');
							$data = array();	$employeeData=array();
							$searchQuery = '';
							$searchArray = array();
							$tablecontent = '';
							if($refresh == 'refresh')
							{
								$sort = 'DESC';$by = 'e.modifieddate';$perPage = 10;$pageNo = 1;$searchData = '';
							}
							else 
							{
								$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
								$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'e.modifieddate';
								$perPage = $this->_getParam('per_page',10);
								$pageNo = $this->_getParam('page', 1);
								$searchData = $this->_getParam('searchData');	
								$searchData = rtrim($searchData,',');			
								/** search from grid - START **/
								$searchData = $this->_getParam('searchData');	
								if($searchData != '' && $searchData!='undefined')
								{
									$searchValues = json_decode($searchData);
										foreach($searchValues as $key => $val)
										{
											if($key == 'from_date' || $key == 'to_date')
											{
												$searchQuery .= " ".$key." like '%".  sapp_Global::change_date($val,'database')."%' AND ";
											}
											else 
											$searchQuery .= " ".$key." like '%".$val."%' AND ";
											$searchArray[$key] = $val;
										}
										$searchQuery = rtrim($searchQuery," AND");				
								}
								/** search from grid - END **/
							}
									
							$objName = 'educationdetails';
							/*$tableFields = array('action'=>'Action','educationlevelcode'=>'Education Level','institution_name'=>'Institution Name','course'=>'Course','from_date'=>'From',"to_date"=>"To","percentage"=>"Percentage");*/
							$tableFields = array('action'=>'Action','educationlevel'=>'Education Level',
										 'institution_name'=>'Institution Name','course'=>'Course',
										 'from_date'=>'From',"to_date"=>"To","percentage"=>"Percentage");
							

							$tablecontent = $educationdetailsModel->geteducationdetails($sort, $by, $pageNo, $perPage,$searchQuery,$Uid);     
							
							$educationlevelcodemodel = new Default_Model_Educationlevelcode();
							$educationlevelArr = $educationlevelcodemodel->getEducationlevelData();
							$level_opt = array();
							if(!empty($educationlevelArr))
							{
								foreach ($educationlevelArr as $educationlevelres)
								{                                                        
									$level_opt[$educationlevelres['id']] = $educationlevelres['educationlevelcode'];
								}
							}
							$dataTmp = array('userid'=>$Uid,
								'sort' => $sort,
								'by' => $by,
								'pageNo' => $pageNo,
								'perPage' => $perPage,				
								'tablecontent' => $tablecontent,
								'objectname' => $objName,
								'extra' => array(),
								'tableheader' => $tableFields,
								'jsGridFnName' => 'getEmployeeAjaxgridData',
								'jsFillFnName' => '',
								'searchArray' => $searchArray,
								'add'=>'add',
								'menuName'=>'Education Details',
								'formgrid' => 'true',
								'unitId'=>$Uid,
								'call'=>$call,'context'=>'myteam',
								'search_filters' => array('from_date' =>array('type'=>'datepicker'),
															'to_date' =>array('type'=>'datepicker'),
																	'educationlevel' => array(
																		'type' => 'select',
																		'filter_data' => array('' => 'All')+$level_opt,
																	),
														)	
							);			
							array_push($data,$dataTmp);
							//echo "Data : <pre>";print_r($data);die;
							$this->view->id=$Uid;
							$this->view->controllername = $objName;
							$this->view->dataArray = $data;
							$this->view->employeedata = $empdata[0];
							$this->view->call = $call ;
							$this->view->messages = $this->_helper->flashMessenger->getMessages();
						}
						$this->view->empdata = $empdata;
					}
				}
				catch(Exception $e)
				{
					 $this->view->rowexist = "norows";
				}
			}
			else
			{
			 $this->_redirect('error');
		    }
        }
		else
		{
			$this->_redirect('error');
		}			
	}
	
	public function trainingviewAction()
	{	
	    if(defined('EMPTABCONFIGS'))
		{
		    $empOrganizationTabs = explode(",",EMPTABCONFIGS);
			if(in_array('trainingandcertification_details',$empOrganizationTabs))
			{
				$auth = Zend_Auth::getInstance();
				if($auth->hasIdentity()){
							$loginUserId = $auth->getStorage()->read()->id;
				} 
				$userid = $this->getRequest()->getParam('userid');	//This is User_id taking from URL
				$call = $this->_getParam('call');
				if($call == 'ajaxcall')
				{
					$this->_helper->layout->disableLayout();
					$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
				}
				$Uid = ($userid)?$userid:$userID;//die;
				//Check for this user id record exists or not....
				$employeeModal = new Default_Model_Employee();
				try
				{
					$empdata = $employeeModal->getsingleEmployeeData($Uid);
					if($empdata == 'norows')
					{
					  $this->view->rowexist = "norows";
					  $this->view->empdata = "";
					}
					else
					 {
						$this->view->rowexist = "rows";
						//$empdata = $employeeModal->getActiveEmployeeData($userid);
						if(!empty($empdata))
						{
							$TandCdetailsModel = new Default_Model_Trainingandcertificationdetails();	
							$call = $this->_getParam('call');
							if($call == 'ajaxcall')
							{
								$this->_helper->layout->disableLayout();
								$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
							}
							$view = Zend_Layout::getMvcInstance()->getView();		
						
							$objname = $this->_getParam('objname');
							$refresh = $this->_getParam('refresh');
							
							$data = array();$searchQuery = '';$searchArray = array();$tablecontent = '';$employeeData=array();
						
							if($refresh == 'refresh')
							{
								$sort = 'DESC';$by = 'modifieddate';$perPage = 10;$pageNo = 1;$searchData = '';
							}
							else 
							{
								$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
								$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'modifieddate';
								$perPage = $this->_getParam('per_page',10);
								$pageNo = $this->_getParam('page', 1);
								$searchData = $this->_getParam('searchData');	
								$searchData = rtrim($searchData,',');			
								/** search from grid - START **/
								$searchData = $this->_getParam('searchData');	
								if($searchData != '' && $searchData!='undefined')
								{
									$searchValues = json_decode($searchData);
									foreach($searchValues as $key => $val)
									{
										$searchQuery .= " ".$key." like '%".$val."%' AND ";
										$searchArray[$key] = $val;
									}
									$searchQuery = rtrim($searchQuery," AND");					
								}
								/** search from grid - END **/
							}
								
						$objName = 'trainingandcertificationdetails';
						
						$tableFields = array('action'=>'Action','course_name'=>'Course Name','course_level'=>'Course Level','course_offered_by'=>'Course Offered By','certification_name'=>'Certification Name');
						
						$tablecontent = $TandCdetailsModel->getTandCdetailsData($sort,$by,$pageNo, $perPage,$searchQuery,$Uid); 
						/*if($Uid != "")
						{
							//TO dispaly EMployee Profile information.....
							$usersModel = new Default_Model_Users();
							$employeeData = $usersModel->getUserDetailsByIDandFlag($Uid);
							//echo "Employee Data : <pre>";print_r($employeeData);die;
						}    */
						
						$dataTmp = array('userid'=>$Uid,
							'sort' => $sort,
							'by' => $by,
							'pageNo' => $pageNo,
							'perPage' => $perPage,				
							'tablecontent' => $tablecontent,
							'objectname' => $objName,
							'extra' => array(),
							'tableheader' => $tableFields,
							'jsGridFnName' => 'getEmployeeAjaxgridData',
							'jsFillFnName' => '',
							'searchArray' => $searchArray,
							'add'=>'add',
							'menuName'=>'Employee Certification  Details',
							'formgrid' => 'true','unitId'=>$Uid,
							'call'=>$call,'context'=>'myteam');			
						
						array_push($data,$dataTmp);
						//echo "Data : <pre>";print_r($data);die;
						
						$this->view->id=$Uid;	//User_id sending to view for tabs navigation....
						$this->view->controllername = $objName;
						$this->view->dataArray = $data;
						$this->view->employeedata = $empdata[0];
						$this->view->call = $call ;
						$this->view->messages = $this->_helper->flashMessenger->getMessages();
					}
					$this->view->empdata = $empdata; 
					}
				}
				catch(Exception $e)
				{
					 $this->view->rowexist = "norows";
				}
			}
			else
			{
			 $this->_redirect('error');
		    }
        }
		else
		{
			$this->_redirect('error');
		} 			
    }
	
	public function additionaldetailsviewAction()
	{
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);

		 if(in_array('emp_additional',$empOrganizationTabs)){
		 	$userID="";
			$auth = Zend_Auth::getInstance();
		 	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginUserRole = $auth->getStorage()->read()->emprole;
					$loginUserGroup = $auth->getStorage()->read()->group_id;
		 	}
		 	$id = $this->getRequest()->getParam('userid');
		 	$conText ='';
		 	$call = $this->_getParam('call');
		 	if($call == 'ajaxcall')
		 	{
		 		$this->_helper->layout->disableLayout();
		 		$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
		 		$conText = ($this->_getParam('context') !='')? $this->_getParam('context'):$this->getRequest()->getParam('context');
		 	}
		 	if($id == '')		
			  $id = $userID;
			  
		 	$Uid = ($id)?$id:$userID;
		 	 
		 	$employeeModal = new Default_Model_Employee();
		 	try
		 	{
			    if($Uid && is_numeric($Uid) && $Uid>0)
				{
					$empdata = $employeeModal->getsingleEmployeeData($Uid);
					if($empdata == 'norows')
					{
						$this->view->rowexist = "norows";
						$this->view->empdata = "";
					}
					else
					{
						$this->view->rowexist = "rows";
						if(!empty($empdata))
						{
							$empadditionaldetailsModal = new Default_Model_Empadditionaldetails();
							$view = Zend_Layout::getMvcInstance()->getView();
							$objname = $this->_getParam('objname');
							$refresh = $this->_getParam('refresh');
							$dashboardcall = $this->_getParam('dashboardcall',null);
							$data = array();	$searchQuery = '';	$searchArray = array();		$tablecontent = '';
							if($refresh == 'refresh')
							{
								if($dashboardcall == 'Yes')
								$perPage = DASHBOARD_PERPAGE;
								else
								$perPage = PERPAGE;

								$sort = 'DESC';$by = 'e.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
							}
							else
							{
								$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
								$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'e.modifieddate';
								if($dashboardcall == 'Yes')
								$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
								else
								$perPage = $this->_getParam('per_page',PERPAGE);

								$pageNo = $this->_getParam('page', 1);
								$searchData = $this->_getParam('searchData');
								$searchData = rtrim($searchData,',');
							}
							$dataTmp = $empadditionaldetailsModal->getGrid($sort, $by, $perPage, $pageNo,$searchData,$call,$dashboardcall,$Uid,$conText);
							//$dataTmp['context'] = $conText;
							array_push($data,$dataTmp);
							$this->view->dataArray = $data;
							$this->view->call = $call ;
							$this->view->employeedata = $empdata[0];
							$this->view->id = $id;
							$this->view->messages = $this->_helper->flashMessenger->getMessages();
						}
						$this->view->empdata = $empdata;
					}
				}
                else
				{
				   $this->view->rowexist = "norows";
				}				
		 	}
		 	catch(Exception $e)
		 	{
		 		$this->view->rowexist = "norows";
		 	}
		 }else{
		 	$this->_redirect('error');
		 }
		}else{
			$this->_redirect('error');
		}
	}
	/*public function additionaldetailsviewAction()
	{
	    if(defined('EMPTABCONFIGS'))
		{
		    $empOrganizationTabs = explode(",",EMPTABCONFIGS);
			if(in_array('emp_additional',$empOrganizationTabs))
			{
				$auth = Zend_Auth::getInstance();
				if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
				} 	
				$id = $this->getRequest()->getParam('userid');
				if($id == '')		$id = $loginUserId;
				
				$callval = $this->getRequest()->getParam('call');
				if($callval == 'ajaxcall')
					$this->_helper->layout->disableLayout();
				
				$objName = 'empadditionaldetails';
				$empadditionaldetailsform = new Default_Form_empadditionaldetails();
				$empadditionaldetailsform->removeElement("submit");
				$elements = $empadditionaldetailsform->getElements();
				if(count($elements)>0)
				{
					foreach($elements as $key=>$element)
					{
						if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
						$element->setAttrib("disabled", "disabled");
							}
					}
				}
				 
				try
				{
					if($id)
					{
						$employeeModal = new Default_Model_Employee();
						$empdata = $employeeModal->getsingleEmployeeData($id);
						if($empdata == 'norows')
						{
						  $this->view->rowexist = "norows";
						   $this->view->empdata = "";
						}  
						else
						{
							$this->view->rowexist = "rows";
							if(!empty($empdata))
							{
								$empadditionaldetailsModal = new Default_Model_Empadditionaldetails();
								$usersModel = new Default_Model_Users();
								$countriesModel = new Default_Model_Countries();
								$veteranstatusmodel = new Default_Model_Veteranstatus();
								$militaryservicemodel = new Default_Model_Militaryservice();
								$data = $empadditionaldetailsModal->getsingleEmpAdditionalDetailsData($id);
								//echo "<pre>";print_r($data);exit;
								if(!empty($data))
								{	

									if(isset($data[0]['countries_served']) && $data[0]['countries_served'] !='')
									{						
										$countriesArr = $countriesModel->getCountryCode($data[0]['countries_served']);
										if(sizeof($countriesArr)>0)
										{
											$empadditionaldetailsform->countries_served->addMultiOption($countriesArr[0]['id'],$countriesArr[0]['country_name']);
											
										}
									}
									
									if(isset($data[0]['military_servicetype']) && $data[0]['military_servicetype'] !='')
									{
										$militaryserviceArr = $militaryservicemodel->getMilitaryServiceDataByID($data[0]['military_servicetype']);
										if(sizeof($militaryserviceArr)>0)
										{
												$empadditionaldetailsform->military_servicetype->addMultiOption($militaryserviceArr[0]['id'],$militaryserviceArr[0]['militaryservicetype']);
										}
									}
									
									if(isset($data[0]['veteran_status']) && $data[0]['veteran_status'] !='')
									{
										$veteranstatusArr = $veteranstatusmodel->getVeteranStatusDataByID($data[0]['veteran_status']);
										if(sizeof($veteranstatusArr)>0)
										{
											$empadditionaldetailsform->veteran_status->addMultiOption($veteranstatusArr[0]['id'],$veteranstatusArr[0]['veteranstatus']);
										}
									}
									
									$empadditionaldetailsform->populate($data[0]);

									if($data[0]['from_date'] !='')
									{
										$from_date = sapp_Global::change_date($data[0]["from_date"], 'view');
										$empadditionaldetailsform->from_date->setValue($from_date);
									}
									if($data[0]['to_date'] !='')
									{
									
										$to_date = sapp_Global::change_date($data[0]["to_date"], 'view');
										$empadditionaldetailsform->to_date->setValue($to_date);
									}
										
								}
								$this->view->controllername = $objName;
								$this->view->data = $data;
								$this->view->id = $id;
								$this->view->form = $empadditionaldetailsform;
								$this->view->employeedata = $empdata[0];
								
							}
							$this->view->empdata = $empdata;  	
						}
					}
				}
				catch(Exception $e)
				{
					$this->view->rowexist = "norows";
				}
            }
			else
			{
			 $this->_redirect('error');
		    }
        }
		else
		{
			$this->_redirect('error');
		}			
	}
	public function salarydetailsviewAction()
	{
		 $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		} 	
		$id = $this->getRequest()->getParam('userid');
		if($id == '')		$id = $loginUserId;
		
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		
		$objName = 'empsalarydetails';
		$empsalarydetailsform = new Default_Form_empsalarydetails();
		$empsalarydetailsform->removeElement("submit");
		$elements = $empsalarydetailsform->getElements();
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)
			{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
				$element->setAttrib("disabled", "disabled");
					}
			}
		}
		 
		try
		{
			if($id)
			{
			   	$employeeModal = new Default_Model_Employee();
				$empdata = $employeeModal->getsingleEmployeeData($id);
				if($empdata == 'norows')
				{
				  $this->view->rowexist = "norows";
				   $this->view->empdata = "";
				}  
				else
				{
					$this->view->rowexist = "rows";
					if(!empty($empdata))
					{
						$empsalarydetailsModal = new Default_Model_Empsalarydetails();
						$usersModel = new Default_Model_Users();
						$currencymodel = new Default_Model_Currency();
						$accountclasstypemodel = new Default_Model_Accountclasstype();
						$bankaccounttypemodel = new Default_Model_Bankaccounttype();
						$data = $empsalarydetailsModal->getsingleEmpSalaryDetailsData($id);
					
					  if(!empty($data))
						{	

                            if(isset($data[0]['currencyid']) && $data[0]['currencyid'] !='')
							{						
								$currencyArr = $currencymodel->getCurrencyDataByID($data[0]['currencyid']);
								if(sizeof($currencyArr)>0)
								{
									$empsalarydetailsform->currencyid->addMultiOption($currencyArr[0]['id'],$currencyArr[0]['currencyname'].' '.$currencyArr[0]['currencycode']);
									
								}
							}
							
							if(isset($data[0]['accountclasstypeid']) && $data[0]['accountclasstypeid'] !='')
							{
								$accountclasstypeArr = $accountclasstypemodel->getsingleAccountClassTypeData($data[0]['accountclasstypeid']);
								if(sizeof($accountclasstypeArr)>0)
								{
										$empsalarydetailsform->accountclasstypeid->addMultiOption($accountclasstypeArr[0]['id'],$accountclasstypeArr[0]['accountclasstype']);
								}
							}
							
							if(isset($data[0]['bankaccountid']) && $data[0]['bankaccountid'] !='')
							{
								$bankaccounttypeArr = $bankaccounttypemodel->getsingleBankAccountData($data[0]['bankaccountid']);
								if(sizeof($bankaccounttypeArr)>0)
								{
									$empsalarydetailsform->bankaccountid->addMultiOption($bankaccounttypeArr[0]['id'],$bankaccounttypeArr[0]['bankaccounttype']);
								}
							}
							
							$empsalarydetailsform->populate($data[0]);

							if($data[0]['accountholding'] !='')
							{
								$accountholding = sapp_Global::change_date($data[0]["accountholding"], 'view');
								$empsalarydetailsform->accountholding->setValue($accountholding);
							}
															
						}
						$this->view->controllername = $objName;
						$this->view->data = $data;
						$this->view->id = $id;
						$this->view->form = $empsalarydetailsform;
						$this->view->employeedata = $empdata[0];
						
					}
					$this->view->empdata = $empdata;  	
				}
            }
		}
		catch(Exception $e)
		{
			$this->view->rowexist = "norows";
		}
	}*/
	
	public function jobhistoryviewAction()
	{
	    if(defined('EMPTABCONFIGS'))
		{
		    $empOrganizationTabs = explode(",",EMPTABCONFIGS);
			if(in_array('emp_jobhistory',$empOrganizationTabs))
			{
				$userID="";
				$auth = Zend_Auth::getInstance();
				if($auth->hasIdentity()){
							$loginUserId = $auth->getStorage()->read()->id;
							$loginUserRole = $auth->getStorage()->read()->emprole;
							$loginUserGroup = $auth->getStorage()->read()->group_id;
				} 
				$id = $this->getRequest()->getParam('userid');
				//if($id == '')		$id = $loginUserId;
				$conText ='myteam';
				$call = $this->_getParam('call');
				if($call == 'ajaxcall')
				{	
					$this->_helper->layout->disableLayout();
					$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
					$conText = ($this->_getParam('context') !='')? $this->_getParam('context'):$this->getRequest()->getParam('context');
				}
				if($id == '')		$id = $userID;
				$Uid = ($id)?$id:$userID;
				
				$employeeModal = new Default_Model_Employee();
				try
				{
					$empdata = $employeeModal->getsingleEmployeeData($Uid);
					//echo "<pre>";print_r($empdata);die;
					if($empdata == 'norows')
					{
						$this->view->rowexist = "norows";
						$this->view->empdata = "";
					}
					else
					{
						$this->view->rowexist = "rows";
						//$empdata = $employeeModal->getActiveEmployeeData($id);
						if(!empty($empdata))
						{
							$empjobhistoryModel = new Default_Model_Empjobhistory();	
							$view = Zend_Layout::getMvcInstance()->getView();		
							$objname = $this->_getParam('objname');
							$refresh = $this->_getParam('refresh');
							$dashboardcall = $this->_getParam('dashboardcall',null);
							$data = array();	$searchQuery = '';	$searchArray = array();		$tablecontent = '';
							if($refresh == 'refresh')
							{
								if($dashboardcall == 'Yes')
									$perPage = DASHBOARD_PERPAGE;
								else	
									$perPage = PERPAGE;
												
								$sort = 'DESC';$by = 'e.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';	$searchArray = array();
							}
							else 
							{
								$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
								$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'e.modifieddate';
								if($dashboardcall == 'Yes')
									$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
								else 
									$perPage = $this->_getParam('per_page',PERPAGE);
								$pageNo = $this->_getParam('page', 1);
								$searchData = $this->_getParam('searchData');	
								$searchData = rtrim($searchData,',');
							}
							$dataTmp = $empjobhistoryModel->getGrid($sort, $by, $perPage, $pageNo,$searchData,$call,$dashboardcall,$Uid,$conText);	
											
							array_push($data,$dataTmp);
							$this->view->dataArray = $data;
							$this->view->call = $call ;
							$this->view->employeedata = $empdata[0];
							$this->view->id = $id ;
							$this->view->messages = $this->_helper->flashMessenger->getMessages();
						}
						$this->view->empdata = $empdata; 
					}
				}
				catch(Exception $e)
				{
					$this->view->rowexist = "norows";
				}
			}
			else
			{
			 $this->_redirect('error');
		    }
        }
		else
		{
			$this->_redirect('error');
		}			
	}
	
}

