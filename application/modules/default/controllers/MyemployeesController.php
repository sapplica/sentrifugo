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

class Default_MyemployeesController extends Zend_Controller_Action
{
	
    private $options;
	public function preDispatch()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('getempreportdata', 'html')->initContext();	
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
		$role_model = new Default_Model_Roles();		

		$data = array();
		$limit=LIMIT;
		$offset=0;
		$search_val = $this->_request->getParam('search_val',null);
		$search_str = $this->_request->getParam('search_str',null);
		$role_id = $this->_request->getParam('role_id',null);
		
		// get active roles
		$roles_arr = $role_model->getRoles();
		$this->view->roles_arr  = $roles_arr ;
		/* 
		$dataTmp = $myemployeesModel->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$loginUserId,$loginUserId); */			
		$dataTmp = $employeeModel->getEmployees($loginUserId,$loginUserId,$limit,$offset,$search_val,$search_str,$role_id ); 
		
		$totalemployees= $employeeModel->getEmployees($loginUserId,$loginUserId,'','',$search_val,$search_str,$role_id ); 
		$totalcount=count($totalemployees);
		$empcount=count($dataTmp);
		
				
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->empcount = $empcount;
		$this->view->totalcount = $totalcount;
		$this->view->limit = $limit;
		$this->view->offset = $offset + $limit;
		$this->view->remainingcount = $totalcount -  $empcount;
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
			$data = $employeeModal->getsingleEmployeeData($id);
			$empdata = $employeeModal->getActiveEmployeeData($id);
			if($data == 'norows')
			{
				$this->view->rowexist = "norows";
			}
			else if(!empty($data))
			{
				$this->view->rowexist = "rows";
                                
				$data = $data[0]; 
				$employeeData = $employeeModal->getsingleEmployeeData($data['user_id']);
				$roles_arr = $role_model->getRolesDataByID($data['emprole']); 
				if(sizeof($roles_arr) > 0)
				{ 			                    
					$employeeform->emprole->addMultiOption($roles_arr[0]['id'].'_'.$roles_arr[0]['group_id'],utf8_encode($roles_arr[0]['rolename']));
					$data['emprole']=$roles_arr[0]['rolename'];
					//for reporting managers
					$reportingManagerData = $usersModel->getReportingManagerList($data['department_id'],$data['id'],$roles_arr[0]['group_id']);	
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
					
					
				}
				else
				{
				   $data['emprole']="";
				}
				
                $referedby_options = $user_model->getRefferedByForUsers();
				
				$employmentStatusData = $employmentstatusModel->getempstatuslist();
				if(sizeof($employmentStatusData) > 0)
				{ 			
						$employeeform->emp_status_id->addMultiOption('','Select a Employment Status');
					foreach ($employmentStatusData as $employmentStatusres){
						$employeeform->emp_status_id->addMultiOption($employmentStatusres['workcodename'],$employmentStatusres['statusname']);
					}
				}
		
				$businessunitData = $busineesUnitModel->getDeparmentList(); 
				if(sizeof($businessunitData) > 0)
				{ 			
					$employeeform->businessunit_id->addMultiOption('0','No Business Unit');
					foreach ($businessunitData as $businessunitres){
						$employeeform->businessunit_id->addMultiOption($businessunitres['id'],$businessunitres['unitname']);
					}
				}
				
				$departmentsData = $deptModel->getDepartmentList($data['businessunit_id']);
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
				       if(!empty($cand_data ))
						{
							$data['requisition_code'] = $cand_data['requisition_code'];
						}
						else{
							$data['requisition_code'] = "";
						}
				}
			    if(isset($data['prefix_id']) && $data['prefix_id'] !='')
				{
					
					$singlePrefixArr = $prefixModel->getsinglePrefixData($data['prefix_id']);
					
					if($singlePrefixArr !='norows')
					{
						$employeeform->prefix_id->addMultiOption($singlePrefixArr[0]['id'],$singlePrefixArr[0]['prefix']);	
						$data['prefix_id']=$singlePrefixArr[0]['prefix'];
	
					}
					else
					{
						$data['prefix_id']= "";
					}
					
				}
				
				if(!empty($data['businessunit_id'])) {
					
						$buname = $busineesUnitModel->getSingleUnitData($data['businessunit_id']);
						
						if(!empty($buname)){
							$data['businessunit_id'] = $buname['unitname'];
							//echo $data['businessunit_id'];exit;
						}
						else
						{
							$data['businessunit_id'] = "" ;
						}
					}
					if(!empty($data['department_id'])) {
						$depname = $deptModel->getSingleDepartmentData($data['department_id']);
						
						if(!empty($depname)){
							$data['department_id'] = $depname['deptname'];
							
						}
						else
						{
							$data['department_id'] = "";
						}
					}
					if(!empty($data['jobtitle_id'])) 
					{
					  $jobname = $jobtitlesModel->getsingleJobTitleData($data['jobtitle_id']);
				
						if(!empty($jobname) && $jobname!='norows'){
							
							$data['jobtitle_id'] = $jobname[0]['jobtitlename'];
							
						}
						 else
						{
						  $data['jobtitle_id'] = "";
						}
						
					}
				    else
					{
							$data['jobtitle_id'] = "";
					}
					
					if(!empty($data['position_id']))
					 {
						
						$nameofposition = $positionsmodel->getsinglePositionData($data['position_id']);
						
						if(!empty($nameofposition) && $nameofposition!='norows'){
							
							$data['position_id'] = $nameofposition[0]['positionname'];
							
						}
						else
					    {
							$data['position_id'] ="";
					    }
						
					}
				     else
					{
							$data['position_id'] ="";
					}
					
					if(!empty($data['emp_status_id'])) {
						$employmentStatusValue = $employmentstatusModel->getParticularStatusName($data['emp_status_id']);
						
						if(!empty($employmentStatusValue)){
							
							$data['emp_status_id'] = $employmentStatusValue[0]['employemnt_status'];
						
						}
						else
						{
							$data['emp_status_id'] = "";
						}
					}
					
					
					if(!empty($data['reporting_manager'])) {
					 	$reportingManagerName = $usersModel->getUserDetailsByID($data['reporting_manager']);
						
						if(!empty($reportingManagerName)){
							
							$data['reporting_manager'] = $reportingManagerName[0]['userfullname'];
						
						}
						else
						{
							$data['reporting_manager'] = "";
						}
					}
				
				$employeeform->setAttrib('action',BASE_URL.'employee/edit/id/'.$id);
				$this->view->id = $id;
				$this->view->form = $employeeform;
				$this->view->employeedata = (!empty($employeeData))?$employeeData[0]:"";
				$this->view->messages = $this->_helper->flashMessenger->getMessages();
				$this->view->data = $data;
				$this->view->empdata = $empdata;	
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
						$usersModel = new Default_Model_Users();
                        $empdata = $employeeModal->getActiveEmployeeData($id);
						$employeeData = $usersModel->getUserDetailsByIDandFlag($id);
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
								
								//To get all ids of employees in my team
									$myEmployees_model = new Default_Model_Myemployees();
									$getMyTeamIds = $myEmployees_model->getTeamIds($loginUserId);
									$teamIdArr = array();
									if(!empty($getMyTeamIds))
									{
										foreach($getMyTeamIds as $teamId)
										{
											array_push($teamIdArr,$teamId['user_id']);
										}
									}
									
								//if($loginUserGroup == MANAGER_GROUP || $loginUserRole == SUPERADMINROLE)
								//{
									//if($loginUserRole == SUPERADMINROLE || $loginUserGroup == MANAGEMENT_GROUP || $loginUserGroup == HR_GROUP || ($loginUserGroup == MANAGER_GROUP && in_array($id,$teamIdArr)))
									if(in_array($id,$teamIdArr))
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
											$data[0]['genderid']=$genderlistArr[0]['gendername'];
										}
										else
										{
											$data[0]['genderid']="";
										}
									}
									
									if(isset($data[0]['maritalstatusid']) && $data[0]['maritalstatusid'] !='')
									{
										$maritalstatuslistArr = $maritalstatusmodel->getsingleMaritalstatusData($data[0]['maritalstatusid']);
										if($maritalstatuslistArr !='norows')
										{
												$emppersonaldetailsform->maritalstatusid->addMultiOption($maritalstatuslistArr[0]['id'],$maritalstatuslistArr[0]['maritalstatusname']);
										        $data[0]['maritalstatusid']=$maritalstatuslistArr[0]['maritalstatusname'];
										}
										else
										{
											$data[0]['maritalstatusid']= "";
										}
									}
									
									if(isset($data[0]['nationalityid']) && $data[0]['nationalityid'] !='')
									{
										$nationalitylistArr = $nationalitymodel->getNationalityDataByID($data[0]['nationalityid']);
										if(sizeof($nationalitylistArr)>0)
										{
											$emppersonaldetailsform->nationalityid->addMultiOption($nationalitylistArr[0]['id'],$nationalitylistArr[0]['nationalitycode']);
										    $data[0]['nationalityid']=$nationalitylistArr[0]['nationalitycode'];
										}
										else
										{
											$data[0]['nationalityid']= "";
										}
									}
									
									if(isset($data[0]['ethniccodeid']) && $data[0]['ethniccodeid'] !='')
									{
										$singleethniccodeArr = $ethniccodemodel->getsingleEthnicCodeData($data[0]['ethniccodeid']);
										  if($singleethniccodeArr !='norows'){
											$emppersonaldetailsform->ethniccodeid->addMultiOption($singleethniccodeArr[0]['id'],$singleethniccodeArr[0]['ethnicname']);
									        $data[0]['ethniccodeid']=$singleethniccodeArr[0]['ethnicname'];
										  }
										  else
										  {
											   $data[0]['ethniccodeid'] ="";
										  }
									}
									
									if(isset($data[0]['racecodeid']) && $data[0]['racecodeid'] !='')
									{
										$singleracecodeArr = $racecodemodel->getsingleRaceCodeData($data[0]['racecodeid']);
										  if($singleracecodeArr !='norows'){
											$emppersonaldetailsform->racecodeid->addMultiOption($singleracecodeArr[0]['id'],$singleracecodeArr[0]['racename']);
									        $data[0]['racecodeid']=$singleracecodeArr[0]['racename'];
										  }
										  else
										  {
											  $data[0]['racecodeid']= "";
										  }
								    }
									
									if(isset($data[0]['languageid']) && $data[0]['languageid'] !='')
									{
										$singlelanguageArr = $languagemodel->getLanguageDataByID($data[0]['languageid']);
										  if(!empty($singlelanguageArr))
										  {
											$emppersonaldetailsform->languageid->addMultiOption($singlelanguageArr[0]['id'],$singlelanguageArr[0]['languagename']);
									        $data[0]['languageid']=$singlelanguageArr[0]['languagename'];
										  }
										  else
										  {
											   $data[0]['languageid']="";
										  }
								    }
								
										$emppersonaldetailsform->populate($data[0]);
										if($data[0]['dob'] !='')
										{
											$dob = sapp_Global::change_date($data[0]["dob"], 'view');
											$emppersonaldetailsform->dob->setValue($dob);
										}
										/*
										if($data[0]['celebrated_dob'] !='')
										{
										
											$celebrated_dob = sapp_Global::change_date($data[0]["celebrated_dob"], 'view');
											$emppersonaldetailsform->celebrated_dob->setValue($celebrated_dob);
										}
										*/
										$documentsArr = array();
										if($data[0]['identity_documents'] !='')
										{
											$documentsArr = get_object_vars(json_decode($data[0]['identity_documents']));
											$documentsArr = sapp_Global::object_to_array($documentsArr);
										    $this->view->documentsArr = $documentsArr;
										}
										$this->view->data = $data;
								}
								$this->view->controllername = $objName;
								$this->view->id = $id;
								$this->view->form = $emppersonaldetailsform;
								$this->view->employeedata = $employeeData[0];
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
						$usersModel = new Default_Model_Users();
                        $empdata = $employeeModal->getActiveEmployeeData($id);
						$employeeData = $usersModel->getUserDetailsByIDandFlag($id);
						
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
								$empDept = $empdata[0]['department_id'];
							
								$empcommdetailsModal = new Default_Model_Empcommunicationdetails();
								$usersModel = new Default_Model_Users();
								$countriesModel = new Default_Model_Countries();
								$statesmodel = new Default_Model_States();
								$citiesmodel = new Default_Model_Cities();
								$orgInfoModel = new Default_Model_Organisationinfo();
								$msgarray = array();
								
								$deptModel = new Default_Model_Departments();
								if($empDept !='' && $empDept !='NULL')
									$departmentAddress = $usersModel->getDepartmentAddress($empDept);
								else
									$departmentAddress = $usersModel->getOrganizationAddress($empDept);	
							
								$data = $empcommdetailsModal->getsingleEmpCommDetailsData($id);
								if(!empty($data))
								{
									if($data[0]['perm_country'] != ''){
										$countrieslistArr = $countriesModel->getCountryCode($data[0]['perm_country']);
										if(sizeof($countrieslistArr)>0)
										{
											$empcommdetailsform->perm_country->addMultiOption('','Select Country');
											foreach ($countrieslistArr as $countrieslistres)
											{
												$empcommdetailsform->perm_country->addMultiOption($countrieslistres['id'],$countrieslistres['country_name']);
											}
										}
									}
								  
									if($data[0]['perm_country'] != ''){
										$statePermlistArr = $statesmodel->getStatesList($data[0]['perm_country']);
										if(sizeof($statePermlistArr)>0)
										{
											$empcommdetailsform->perm_state->addMultiOption('','Select State');
											foreach($statePermlistArr as $statelistres)	
											 {
												$empcommdetailsform->perm_state->addMultiOption($statelistres['id'].'!@#'.$statelistres['state_name'],$statelistres['state_name']); 
											 }   						 
										}
									}
									if($data[0]['perm_state'] != ''){
										$cityPermlistArr = $citiesmodel->getCitiesList($data[0]['perm_state']);
										if(sizeof($cityPermlistArr)>0)
										{
											$empcommdetailsform->perm_city->addMultiOption('','Select City');
											foreach($cityPermlistArr as $cityPermlistres)	
											{
												$empcommdetailsform->perm_city->addMultiOption($cityPermlistres['id'].'!@#'.$cityPermlistres['city_name'],$cityPermlistres['city_name']); 
											}   						 
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
									$empcommdetailsform->populate($data[0]);
									if($data[0]['perm_state'] != ''){	
										$permstateNameArr = $statesmodel->getStateName($data[0]['perm_state']);
										$empcommdetailsform->setDefault('perm_state',$permstateNameArr[0]['id'].'!@#'.$permstateNameArr[0]['statename']);
									}
									if($data[0]['perm_city'] != ''){
										$permcityNameArr = $citiesmodel->getCityName($data[0]['perm_city']);
										$empcommdetailsform->setDefault('perm_city',$permcityNameArr[0]['id'].'!@#'.$permcityNameArr[0]['cityname']);										
									}
									if($data[0]['perm_country'] != '')
										$empcommdetailsform->setDefault('perm_country',$data[0]['perm_country']);
									if($data[0]['current_country'] != '')   
									   $empcommdetailsform->setDefault('current_country',$data[0]['current_country']);
									if($data[0]['current_state'] !='')
									   $empcommdetailsform->setDefault('current_state',$currstateNameArr[0]['id'].'!@#'.$currstateNameArr[0]['statename']);
									if($data[0]['current_city'] != '')   
									   $empcommdetailsform->setDefault('current_city',$currcityNameArr[0]['id'].'!@#'.$currcityNameArr[0]['cityname']);
									
							    }
									if(!empty($data[0]['perm_country']))
									 {
									   $countryname = $countriesModel->getCountryCode($data[0]['perm_country']);
										  if(!empty($countryname))
										  {
										  $data[0]['perm_country'] = $countryname[0]['country_name'];
										  }
										  else{
											  $data[0]['perm_country'] = "";
										  }
									 }
									if(!empty($data[0]['perm_state']))
									 {
										$statename = $statesmodel->getStateName($data[0]['perm_state']);
										  if(!empty($statename))
										  {
										    $data[0]['perm_state'] = $statename[0]['statename'];
										  }
										  else
										  {
											   $data[0]['perm_state'] = "";
										  }
									 }
									if(!empty($data[0]['perm_city'])) 
									{
										$cityname = $citiesmodel->getCityName($data[0]['perm_city']);
										 if(!empty($cityname))
										 {
									    	 $data[0]['perm_city'] = $cityname[0]['cityname'];
										 }
										 else
										 {
											  $data[0]['perm_city'] = "";
										 }
									}
									if(!empty($data[0]['current_country']))
									 {
									   $countryname = $countriesModel->getCountryCode($data[0]['current_country']);
										  if(!empty($countryname))
										  {
										   $data[0]['current_country'] = $countryname[0]['country_name'];
										  }
										  else
										  {
											  $data[0]['current_country'] = "";
										  }
									 }
									if(!empty($data[0]['current_state']))
									 {
										$statename = $statesmodel->getStateName($data[0]['current_state']);
										  if(!empty($statename))
										  {
										   $data[0]['current_state'] = $statename[0]['statename'];
										  }
										  else
										  {
											  $data[0]['current_state'] = ""; 
										  }
									 }
									if(!empty($data[0]['current_city'])) 
									{
										$cityname = $citiesmodel->getCityName($data[0]['current_city']);
										 if(!empty($cityname))
										 {
										  $data[0]['current_city'] = $cityname[0]['cityname'];
										 }
										 else
										 {
											 $data[0]['current_city'] = "";
										 }
									}
							$this->view->controllername = $objName;
							$this->view->data = $data;
							$this->view->dataArray = $departmentAddress;
							$this->view->id = $id;
							$this->view->employeedata = $employeeData[0];
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
				$Uid = ($id)?$id:$userID;
				$employeeModal = new Default_Model_Employee();
				try
				{
					$usersModel = new Default_Model_Users();
                    $empdata = $employeeModal->getActiveEmployeeData($Uid);
					$employeeData = $usersModel->getUserDetailsByIDandFlag($Uid);
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
							$empskillsModel = new Default_Model_Empskills();	
							$view = Zend_Layout::getMvcInstance()->getView();		
							$objname = $this->_getParam('objname');
							$refresh = $this->_getParam('refresh');
							$data = array();$searchQuery = '';	$searchArray = array();	$tablecontent = '';$levelsArr=array();$empcompetencyLevelsArr =array();
							if($refresh == 'refresh')
							{
								$sort = 'DESC';$by = 'e.modifieddate';$perPage = PERPAGE;$pageNo = 1;$searchData = '';
							}
							else 
							{
								$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
								$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'e.modifieddate';
								$perPage = $this->_getParam('per_page',PERPAGE);
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
									$searchQuery = rtrim($searchQuery," AND");						
								}
								/** search from grid - END **/
							}
									
							$objName = 'empskills';
							$tableFields = array('action'=>'Action','skillname'=>'Skill','yearsofexp'=>'Years of Experience','competencylevelid'=>'Competency Level','year_skill_last_used'=>'Skill Last Used Year');
							
							$tablecontent = $empskillsModel->getEmpSkillsData($sort, $by, $pageNo, $perPage,$searchQuery,$Uid);     
								$empcompetencyLevelsArr = $empskillsModel->empcompetencylevels($Uid);
								for($i=0;$i<sizeof($empcompetencyLevelsArr);$i++)
								{
									$levelsArr[$empcompetencyLevelsArr[$i]['id']] = $empcompetencyLevelsArr[$i]['competencylevel'];
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
											'menuName'=>TAB_EMP_SKILLS,
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
							$this->view->employeedata = $employeeData[0];
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
				$Uid = ($userid)?$userid:$userID;
				
				$employeeModal = new Default_Model_Employee();
				$usersModel = new Default_Model_Users();
                $empdata = $employeeModal->getActiveEmployeeData($Uid);
				$employeeData = $usersModel->getUserDetailsByIDandFlag($Uid);
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
						if(!empty($empdata))
						{
							$experiencedetailsModel = new Default_Model_Experiencedetails();	
							$view = Zend_Layout::getMvcInstance()->getView();		
						
							$objname = $this->_getParam('objname');
							$refresh = $this->_getParam('refresh');
						
							$data = array();	$searchQuery = '';	$searchArray = array();		$tablecontent = '';
						
							if($refresh == 'refresh')
							{
								$sort = 'DESC';$by = 'modifieddate';$perPage = PERPAGE;$pageNo = 1;$searchData = '';
							}
							else 
							{
								$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
								$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'modifieddate';
								$perPage = $this->_getParam('per_page',PERPAGE);
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
							'menuName'=>TAB_EMP_EXPERIENCE,
							'formgrid' => 'true',
							'unitId'=>$Uid,
							'call'=>$call,'context'=>'myteam',
							'search_filters' => array(
											'from_date' =>array('type'=>'datepicker'),
											'to_date' =>array('type'=>'datepicker')											
											)
							);			
						
						array_push($data,$dataTmp);
						
						$this->view->id=$Uid;	//User_id sending to view for tabs navigation....
						$this->view->controllername = $objName;
						$this->view->dataArray = $data;
						$this->view->call = $call ;
						$this->view->employeedata = $employeeData[0];
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
				$Uid = ($userid)?$userid:$userID;
				$employeeModal = new Default_Model_Employee();
				try
				{
					$usersModel = new Default_Model_Users();
                    $empdata = $employeeModal->getActiveEmployeeData($Uid);
					$employeeData = $usersModel->getUserDetailsByIDandFlag($Uid);
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
							$educationdetailsModel = new Default_Model_Educationdetails();	
										
							$view = Zend_Layout::getMvcInstance()->getView();		
							$objname = $this->_getParam('objname');
							$refresh = $this->_getParam('refresh');
							$data = array();	
							$searchQuery = '';
							$searchArray = array();
							$tablecontent = '';
							if($refresh == 'refresh')
							{
								$sort = 'DESC';$by = 'e.modifieddate';$perPage = PERPAGE;$pageNo = 1;$searchData = '';
							}
							else 
							{
								$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
								$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'e.modifieddate';
								$perPage = $this->_getParam('per_page',PERPAGE);
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
								'menuName'=>TAB_EMP_EDUCATION,
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
							$this->view->id=$Uid;
							$this->view->controllername = $objName;
							$this->view->dataArray = $data;
							$this->view->employeedata = $employeeData[0];
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
					$usersModel = new Default_Model_Users();
                    $empdata = $employeeModal->getActiveEmployeeData($Uid);
					$employeeData = $usersModel->getUserDetailsByIDandFlag($Uid);
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
							
							$data = array();$searchQuery = '';$searchArray = array();$tablecontent = '';
						
							if($refresh == 'refresh')
							{
								$sort = 'DESC';$by = 'modifieddate';$perPage = PERPAGE;$pageNo = 1;$searchData = '';
							}
							else 
							{
								$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
								$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'modifieddate';
								$perPage = $this->_getParam('per_page',PERPAGE);
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
							'menuName'=>TAB_EMP_TRAINING_CERTIFY,
							'formgrid' => 'true','unitId'=>$Uid,
							'call'=>$call,'context'=>'myteam');			
						
						array_push($data,$dataTmp);
						
						$this->view->id=$Uid;	//User_id sending to view for tabs navigation....
						$this->view->controllername = $objName;
						$this->view->dataArray = $data;
						$this->view->employeedata = $employeeData[0];
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
		 	$conText ='myteam';
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
					$usersModel = new Default_Model_Users();
                    $empdata = $employeeModal->getActiveEmployeeData($Uid);
					$employeeData = $usersModel->getUserDetailsByIDandFlag($Uid);
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
							array_push($data,$dataTmp);
							$this->view->dataArray = $data;
							$this->view->call = $call ;
							$this->view->employeedata = $employeeData[0];
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
					$usersModel = new Default_Model_Users();
                    $empdata = $employeeModal->getActiveEmployeeData($Uid);
					$employeeData = $usersModel->getUserDetailsByIDandFlag($Uid);
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
							$this->view->employeedata = $employeeData[0];
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
	
	public function addAction()
	{
		$emptyFlag=0;
		$auth = Zend_Auth::getInstance();

		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
			$loginuserUnitID = $auth->getStorage()->read()->businessunit_id;
			$loginuserDeptID = $auth->getStorage()->read()->department_id;
		}
		
		$employeeform = new Default_Form_Myteamemployee();
		
		$usersModel = new Default_Model_Users();
		$employmentstatusModel = new Default_Model_Employmentstatus();
		$busineesUnitModel = new Default_Model_Businessunits();
		$user_model = new Default_Model_Usermanagement();
		$candidate_model = new Default_Model_Candidatedetails();
		$role_model = new Default_Model_Roles();
	
		$jobtitlesModel = new Default_Model_Jobtitles();
		$prefixModel = new Default_Model_Prefix();
		$msgarray = array();
		$identity_code_model = new Default_Model_Identitycodes();
		$identity_codes = $identity_code_model->getIdentitycodesRecord();
		

		$emp_identity_code = isset($identity_codes[0])?$identity_codes[0]['employee_code']:"";
		
		if($emp_identity_code!='')
		{
			// $emp_id = $emp_identity_code.str_pad($user_model->getMaxEmpId($emp_identity_code), 4, '0', STR_PAD_LEFT);
			//sending identity code instead of employee id
			$emp_id = $emp_identity_code;
			
		}
		else
		{
			$emp_id = '';
			$msgarray['employeeId'] = 'Identity codes are not configured yet.';
		}

		$employeeform->employeeId->setValue($emp_id);
		$employeeform->modeofentry->setValue('Direct');

		$roles_arr = $role_model->getRolesListByGroupID(EMPLOYEE_GROUP);

		if(sizeof($roles_arr) > 0)
		{
			$employeeform->emprole->addMultiOptions(array(''=>'Select Role')+$roles_arr);
		}
		else
		{
		    $employeeform->emprole->addMultiOptions(array(''=>'Select Role'));
			$msgarray['emprole'] = 'Roles are not configured yet.';
		}
			
		$employmentStatusData = $employmentstatusModel->getempstatusActivelist();
		$employeeform->emp_status_id->addMultiOption('','Select Employment Status');
		if(!empty($employmentStatusData))
		{
			foreach ($employmentStatusData as $employmentStatusres){
				$employeeform->emp_status_id->addMultiOption($employmentStatusres['workcodename'],$employmentStatusres['statusname']);
			}
		}
		else
		{
			$msgarray['emp_status_id'] = 'Employment status is not configured yet.';
			$emptyFlag++;
		}

		$businessunitData = $busineesUnitModel->getDeparmentList();
		if(!empty($businessunitData))
		{
			foreach ($businessunitData as $businessunitres){
				if($businessunitres['id'] == $loginuserUnitID)
					$employeeform->businessunit_id->addMultiOption($businessunitres['id'],$businessunitres['unitname']);
			}

			$departmentsmodel = new Default_Model_Departments();
			$loginUserdepartmentData = $departmentsmodel->getSingleDepartmentData($loginuserDeptID);
			$totalDeptList = $departmentsmodel->getTotalDepartmentList();
			$employeeform->department_id->clearMultiOptions();
			if(count($loginUserdepartmentData) > 0)
			{
				$employeeform->department_id->addMultiOption($loginUserdepartmentData['id'],utf8_encode($loginUserdepartmentData['deptname']));
			}
			if(empty($totalDeptList))
			{
				$msgarray['department_id'] = 'Departments are not added yet.';
			}
		}
		else
		{
			$msgarray['businessunit_id'] = 'Business units are not added yet.';
			$emptyFlag++;
		}

		$jobtitleData = $jobtitlesModel->getJobTitleList();
		$employeeform->jobtitle_id->addMultiOption('','Select Job Title');
		if(!empty($jobtitleData))
		{
			foreach ($jobtitleData as $jobtitleres)
			{
				$employeeform->jobtitle_id->addMultiOption($jobtitleres['id'],$jobtitleres['jobtitlename']);
			}
		}
		else
		{
			$msgarray['jobtitle_id'] = 'Job titles are not configured yet.';
			$msgarray['position_id'] = 'Positions are not configured yet.';
			$emptyFlag++;
		}
			
		$prefixData = $prefixModel->getPrefixList();
		$employeeform->prefix_id->addMultiOption('','Select Prefix');
		if(!empty($prefixData))
		{
			foreach ($prefixData as $prefixres){
				$employeeform->prefix_id->addMultiOption($prefixres['id'],$prefixres['prefix']);
			}
		}
		else
		{
			$msgarray['prefix_id'] = 'Prefixes are not configured yet.';
			$emptyFlag++;
		}

		$userData = $usersModel->getUserDetails($loginUserId);
		if(count($userData)>0)
			$employeeform->reporting_manager->addMultiOption($userData[0]['id'],$userData[0]['userfullname']);
			
		
		$employeeform->setAttrib('action',BASE_URL.'myemployees/add');
		$this->view->form = $employeeform;
		$this->view->msgarray = $msgarray;
		$this->view->emptyFlag = $emptyFlag++;
		if($this->getRequest()->getPost())
		{
			$result = $this->save($employeeform);
			$this->view->msgarray = $result;
			$this->view->messages = $result;
		}
	}
	
	public function save($employeeform)
	{
		$emproleStr='';
		$roleArr=array();
		$empgroupStr='';
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$usersModel = new Default_Model_Usermanagement();
		$employeeModal = new Default_Model_Employee();
		$requimodel = new Default_Model_Requisition();
		$candidate_model = new Default_Model_Candidatedetails();
		$orgInfoModel = new Default_Model_Organisationinfo();
		
		$unitid = '';
		$deptid = '';
		$errorflag = 'true';
		$msgarray = array();
		
		$id = $this->_request->getParam('id');
		$businessunit_id = $this->_request->getParam('businessunit_id',null);
		$department_id = $this->_request->getParam('department_id',null);
		$reporting_manager = $this->_request->getParam('reporting_manager',null);
		$jobtitle_id = $this->_request->getParam('jobtitle_id',null);
		$position_id = $this->_request->getParam('position_id',null);
		$user_id = $this->_getParam('user_id',null);
		$prefix_id = $this->_getParam('prefix_id',null);
		$extension_number = $this->_getParam('extension_number',null);
		$office_number = $this->_request->getParam('office_number',null);
		$office_faxnumber = $this->_request->getParam('office_faxnumber',null);
		$date_of_joining = $this->_request->getParam('date_of_joining',null);
		$date_of_joining = sapp_Global::change_date($date_of_joining,'database');
		
		$emp_id = '';
		$employeeNumId = trim($this->_getParam('employeeNumId',null));
		//employee id
		$identity_code_model = new Default_Model_Identitycodes();
		$identity_codes = $identity_code_model->getIdentitycodesRecord();
		$emp_identity_code = isset($identity_codes[0])?$identity_codes[0]['employee_code']:"";
		if($emp_identity_code!='')
		{
			// $emp_id = $emp_identity_code.str_pad($usersModel->getMaxEmpId($emp_identity_code), 4, '0', STR_PAD_LEFT);
			$emp_id = $emp_identity_code.$employeeNumId;
		}
		else
		{
			$emp_id = '';	
		}
		//duplicate emp id check
		$where_condition = "";
		if(!empty($id))
		{
			$where_condition = " and id != $id";
		}
		$isemployeeidexist = $employeeModal->checkemployeeidexist($emp_id,$where_condition);
		if($isemployeeidexist)
		{
			$msgarray['employeeNumId'] = "Employee ID already exists. Please try again.";
			$errorflag = 'false';
		}

		$isvalidorgstartdate = $orgInfoModel->validateEmployeeJoiningDate($date_of_joining,$unitid,$deptid);
		if(!empty($isvalidorgstartdate))
		{
			 $msgarray['date_of_joining'] = 'Employee joining date should be greater than organization start date.';
			 $errorflag = 'false';
		} 
		
		if($employeeform->isValid($this->_request->getPost()) && $errorflag == 'true')
		{
			$id = $this->_request->getParam('id');
			$emp_status_id = $this->_request->getParam('emp_status_id',null);
			
			$date_of_leaving = $this->_request->getParam('date_of_leaving',null);
			$date_of_leaving = sapp_Global::change_date($date_of_leaving,'database');
			$years_exp = $this->_request->getParam('years_exp');
			//FOR USER table
			$employeeId = $this->_getParam('employeeId',null);
			$modeofentry = $this->_getParam('modeofentry',null);
			
			$firstname = trim($this->_getParam('firstname',null));
			$lastname = trim($this->_getParam('lastname',null));
			$userfullname = $firstname.' '.$lastname;
			$emprole = $this->_getParam('emprole',null);	//roleid_group_id
			if($emprole != "")
			{
				$roleArr = explode('_',$emprole);
				if(!empty($roleArr))
				{
					$emproleStr = $roleArr[0];
					$empgroupStr = $roleArr[0];
				}
			}
			$emailaddress = $this->_getParam('emailaddress',null);
			//end of user table
			$date = new Zend_Date();
			$empstatusarray = array(8,9,10);
			$actionflag = '';
			$tableid  = '';

			$trDb = Zend_Db_Table::getDefaultAdapter();
			// starting transaction
			$trDb->beginTransaction();
			try
			{
				$emppassword = sapp_Global::generatePassword();
				$user_data = array(
                                    'emprole' =>$emproleStr,
                                    'firstname' => ($firstname!='')?$firstname:NULL,
                                    'lastname' => ($lastname!='')?$lastname:NULL,
                                    'userfullname' => $userfullname,
                                    'emailaddress' => $emailaddress,
                                    'jobtitle_id'=> $jobtitle_id,
                                    'modifiedby'=> $loginUserId,
                                    'modifieddate'=> gmdate("Y-m-d H:i:s"),                                                                      
                                    'emppassword' => md5($emppassword),
                                    // 'employeeId' => $employeeId,
                                    'modeofentry' => ($id =='')?$modeofentry:"",                                                              
                                    'selecteddate' => $date_of_joining,
                                    'candidatereferredby' => 0,
                                    'userstatus' => 'old',
				);
				$user_data['employeeId'] = $emp_id;
				if($id!='')
				{
					$where = array('user_id=?'=>$user_id);
					$actionflag = 2;
					$user_where = "id = ".$user_id;
					unset($user_data['candidatereferredby']);
					unset($user_data['userstatus']);
					unset($user_data['emppassword']);
					//unset($user_data['employeeId']);
					unset($user_data['modeofentry']);
				}
				else
				{
					$user_data['createdby'] = $loginUserId;
					$user_data['createddate'] = gmdate("Y-m-d H:i:s");
					$user_data['isactive'] = 1;
					$where = '';
					$actionflag = 1;
					$user_where = '';

					$identity_code_model = new Default_Model_Identitycodes();
					$identity_codes = $identity_code_model->getIdentitycodesRecord();
					$emp_identity_code = isset($identity_codes[0])?$identity_codes[0]['employee_code']:"";
					/*if($emp_identity_code!='')
					$emp_id = $emp_identity_code.str_pad($usersModel->getMaxEmpId($emp_identity_code), 4, '0', STR_PAD_LEFT);
					else
					$emp_id = '';*/

					$user_data['employeeId'] = $emp_id;
				}
				$user_status = $usersModel->SaveorUpdateUserData($user_data, $user_where);
                                
				if($id == '')
				$user_id = $user_status;
				$data = array(  'user_id'=>$user_id,
                                    'reporting_manager'=>$reporting_manager,
                                    'emp_status_id'=>$emp_status_id,
                                    'businessunit_id'=>$businessunit_id,
                                    'department_id'=>$department_id,
                                    'jobtitle_id'=>$jobtitle_id, 
                                    'position_id'=>$position_id, 
                                    'prefix_id'=>$prefix_id,
                                    'extension_number'=>($extension_number!=''?$extension_number:NULL),
                                    'office_number'=>($office_number!=''?$office_number:NULL),
                                    'office_faxnumber'=>($office_faxnumber!=''?$office_faxnumber:NULL),
                                    'date_of_joining'=>$date_of_joining,
                                    'date_of_leaving'=>($date_of_leaving!=''?$date_of_leaving:NULL),
                                    'years_exp'=>($years_exp=='')?null:$years_exp,
                                    'modifiedby'=>$loginUserId,				
                                    'modifieddate'=>gmdate("Y-m-d H:i:s")
				);
				if($id == '')
				{
					$data['createdby'] = $loginUserId;
					$data['createddate'] = gmdate("Y-m-d H:i:s");;
					$data['isactive'] = 1;
				}
				$Id = $employeeModal->SaveorUpdateEmployeeData($data, $where);

				$statuswhere = array('id=?'=>$user_id);
                                if($id != '')
                                {
                                    if (in_array($emp_status_id, $empstatusarray))
                                    {
                                        $isactivestatus = '';
                                        if($emp_status_id == 8)
                                        $isactivestatus = 2;
                                        else if($emp_status_id == 9)
                                        $isactivestatus = 3;
                                        else if($emp_status_id == 10)
                                        $isactivestatus = 4;
                                        $statusdata = array('isactive'=>$isactivestatus);
                                        $empstatusId = $usersModel->SaveorUpdateUserData($statusdata, $statuswhere);
                                        $employeeModal->SaveorUpdateEmployeeData($statusdata, "user_id = ".$user_id);
                                    }
                                    else
                                    {
                                        $edata = $usersModel->getUserDataById($id);
                                        $statusdata = array('isactive'=> 1);
                                        if($edata['isactive'] != 0)
                                        {
                                            if($edata['emptemplock'] == 1)
                                                $statusdata = array('isactive'=> 0);
                                            $empstatusId = $usersModel->SaveorUpdateUserData($statusdata, $statuswhere);
                                            $employeeModal->SaveorUpdateEmployeeData($statusdata, "user_id = ".$user_id);
                                        }
                                    }
                                }
				if($Id == 'update')
				{
					$tableid = $id;
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Employee details updated successfully."));
				}
				else
				{
					//start of mailing
					$base_url = 'http://'.$this->getRequest()->getHttpHost() . $this->getRequest()->getBaseUrl();
					$view = $this->getHelper('ViewRenderer')->view;
					$this->view->emp_name = $userfullname;
					$this->view->password = $emppassword;
					$this->view->emp_id = $emp_id;
					$this->view->base_url=$base_url;
					$text = $view->render('mailtemplates/newpassword.phtml');
					$options['subject'] = APPLICATION_NAME.': Login Credentials';
					$options['header'] = 'Greetings from Sentrifugo';
					$options['toEmail'] = $emailaddress;
					$options['toName'] = $this->view->emp_name;
					$options['message'] = $text;
					$result = sapp_Global::_sendEmail($options);
					//end of mailing
					$tableid = $Id;
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Employee details added successfully."));
				}
				$menuID = EMPLOYEE;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$user_id);

				$trDb->commit();
				
				// Send email to employee when his details are edited by other user.
											$options['subject'] = APPLICATION_NAME.': Employee details updated';
                                            $options['header'] = 'Employee details updated';
                                            $options['toEmail'] = $emailaddress;  
                                            $options['toName'] = $userfullname;
                                            $options['message'] = 'Dear '.$userfullname.', your employee details are updated.';
                                            $options['cron'] = 'yes';
                                            if(!empty($id)){
	                                            sapp_Global::_sendEmail($options);
                                            }
				$this->_redirect('myemployees/edit/id/'.$user_id);
			}
			catch (Exception $e)
			{
				$trDb->rollBack();
				$msgarray['employeeId'] = "Something went wrong, please try again later.";
				return $msgarray;
			}
		}
		else
		{
			$messages = $employeeform->getMessages();
			foreach ($messages as $key => $val)
			{
				foreach($val as $key2 => $val2)
				{
					$msgarray[$key] = $val2;
					break;
				}
			}

			if(isset($jobtitle_id) && $jobtitle_id != 0 && $jobtitle_id != '')
			{
				$positionsmodel = new Default_Model_Positions();
				$positionlistArr = $positionsmodel->getPositionList($jobtitle_id);
				$employeeform->position_id->clearMultiOptions();
				$employeeform->position_id->addMultiOption('','Select Position');
				foreach($positionlistArr as $positionlistRes)
				{
					$employeeform->position_id->addMultiOption($positionlistRes['id'],utf8_encode($positionlistRes['positionname']));
				}
				if(isset($position_id) && $position_id != 0 && $position_id != '')
				$employeeform->setDefault('position_id',$position_id);
			}
			return $msgarray;
		}
	}
    
	public function editAction()
	{
		$auth = Zend_Auth::getInstance();
		$role_datap=array();$empGroup="";
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
			$loginuserUnitID = $auth->getStorage()->read()->businessunit_id;
			$loginuserDeptID = $auth->getStorage()->read()->department_id;
		}

		$id = (int)$this->getRequest()->getParam('id');
		$id = abs($id);
		if($id == '')
		$id = $loginUserId;
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();

		$employeeform = new Default_Form_Myteamemployee();

		try
		{
			if($id!='' && is_numeric($id) && $id>0 && $id!=$loginUserId)
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
				$my_emp_data = array();
				$empDeptId="";
				$empRoleId="";
				$my_emp_data = $employeeModal->getsingleEmployeeData($id);
                $empdata = $employeeModal->getActiveEmployeeData($id);
				
				if($my_emp_data == 'norows')
				{                                    
					$this->view->rowexist = "norows";
				}
				else if(!empty($my_emp_data))
				{
					$this->view->rowexist = "rows";
					$employeeform->submit->setLabel('Update');
					$my_emp_data = $my_emp_data[0];
						
					$roles_arr = $role_model->getRolesListByGroupID(EMPLOYEE_GROUP);
					if(sizeof($roles_arr) > 0)
					{
						$employeeform->emprole->addMultiOptions(array(''=>'Select Role')+$roles_arr);
					}

					$employmentStatusData = $employmentstatusModel->getempstatuslist();
					if(sizeof($employmentStatusData) > 0)
					{
						$employeeform->emp_status_id->addMultiOption('','Select Employment Status');
						foreach ($employmentStatusData as $employmentStatusres){
							$employeeform->emp_status_id->addMultiOption($employmentStatusres['workcodename'],$employmentStatusres['statusname']);
						}
					}
						
					$businessunitData = $busineesUnitModel->getDeparmentList();
				   	if(sizeof($businessunitData) > 0)
					{
						foreach ($businessunitData as $businessunitres){
							if($businessunitres['id'] == $my_emp_data['businessunit_id'])
								$employeeform->businessunit_id->addMultiOption($businessunitres['id'],$businessunitres['unitname']);
						}
					}
						
					$departmentsData = $deptModel->getDepartmentList($my_emp_data['businessunit_id']);
					if(sizeof($departmentsData) > 0)
					{
						foreach ($departmentsData as $departmentsres){
							if($departmentsres['id'] == $my_emp_data['department_id'])
								$employeeform->department_id->addMultiOption($departmentsres['id'],$departmentsres['deptname']);
						}
					}
						
						
					$jobtitleData = $jobtitlesModel->getJobTitleList();
					if(sizeof($jobtitleData) > 0)
					{
						$employeeform->jobtitle_id->addMultiOption('','Select Job Title');
						foreach ($jobtitleData as $jobtitleres){
							$employeeform->jobtitle_id->addMultiOption($jobtitleres['id'],$jobtitleres['jobtitlename']);
						}
					}

					$positionlistArr = $positionsmodel->getPositionList($my_emp_data['jobtitle_id']);
					if(sizeof($positionlistArr) > 0)
					{
						$employeeform->position_id->addMultiOption('','Select Position');
						foreach ($positionlistArr as $positionlistres)
						{
							$employeeform->position_id->addMultiOption($positionlistres['id'],$positionlistres['positionname']);
						}
					}

					$prefixData = $prefixModel->getPrefixList();
					if(!empty($prefixData))
					{
						foreach ($prefixData as $prefixres){
							$employeeform->prefix_id->addMultiOption($prefixres['id'],$prefixres['prefix']);
						}
					}
					
					$userData = $usersModel->getUserDetails($loginUserId);
					if(count($userData)>0)
						$employeeform->reporting_manager->addMultiOption($userData[0]['id'],$userData[0]['userfullname']);
					
					$employeeform->populate($my_emp_data);
					//splitting employee id
					$employeeId = !empty($my_emp_data['employeeId'])?$my_emp_data['employeeId']:'';
					$identity_code_model = new Default_Model_Identitycodes();
					$identity_codes = $identity_code_model->getIdentitycodesRecord();
					//getting the identity code
					$emp_identity_code = isset($identity_codes[0])?$identity_codes[0]['employee_code']:"";
					//getting the numeric id
					$employeeNumId = str_replace($emp_identity_code, '', $employeeId);

					$employeeform->setDefault('employeeId',$emp_identity_code);
					$employeeform->setDefault('employeeNumId',$employeeNumId);
					$employeeform->setDefault('final_emp_id',$employeeId);					
					$employeeform->setDefault('user_id',$my_emp_data['user_id']);
					$employeeform->setDefault('emp_status_id',$my_emp_data['emp_status_id']);
					$employeeform->setDefault('businessunit_id',$my_emp_data['businessunit_id']);
					$employeeform->setDefault('jobtitle_id',$my_emp_data['jobtitle_id']);
					$employeeform->setDefault('department_id',$my_emp_data['department_id']);
					$employeeform->setDefault('position_id',$my_emp_data['position_id']);
					$employeeform->setDefault('prefix_id',$my_emp_data['prefix_id']);
					$date_of_joining = sapp_Global::change_date($my_emp_data['date_of_joining'],'view');
					$employeeform->date_of_joining->setValue($date_of_joining);
						
					if($my_emp_data['date_of_leaving'] !='' && $my_emp_data['date_of_leaving'] !='0000-00-00')
					{
						$date_of_leaving = sapp_Global::change_date($my_emp_data['date_of_leaving'],'view');
						$employeeform->date_of_leaving->setValue($date_of_leaving);
					}
					$role_data = $role_model->getRoleDataById($my_emp_data['emprole']);
					$employeeform->emprole->setValue($my_emp_data['emprole']."_".$role_data['group_id']);
						
					$employeeform->setAttrib('action',BASE_URL.'myemployees/edit/id/'.$id);
						
					$this->view->id = $id;
					$this->view->form = $employeeform;
					$this->view->my_emp_data = $my_emp_data;
					$this->view->empdata = $empdata;
					$this->view->messages = $this->_helper->flashMessenger->getMessages();
					}
			}
			else
			{
				$this->view->rowexist = "norows";
			}

			if($this->getRequest()->getPost()){
				$result = $this->save($employeeform);
				$this->view->msgarray = $result;
				$employeeform->modeofentry->setValue($this->getRequest()->getParam('hid_modeofentry'));
			}
		}
		catch(Exception $e)
		{                                        
			$this->view->rowexist = "norows";
		}
	}

	public function skillseditAction()
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
				$Uid = ($id)?$id:$userID;
				$employeeModal = new Default_Model_Employee();
				try
				{
					$usersModel = new Default_Model_Users();
                    $empdata = $employeeModal->getActiveEmployeeData($Uid);
					$employeeData = $usersModel->getUserDetailsByIDandFlag($Uid);
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
							$empskillsModel = new Default_Model_Empskills();	
							$view = Zend_Layout::getMvcInstance()->getView();		
							$objname = $this->_getParam('objname');
							$refresh = $this->_getParam('refresh');
							$data = array();$searchQuery = '';	$searchArray = array();	$tablecontent = '';$levelsArr=array();$empcompetencyLevelsArr =array();
							if($refresh == 'refresh')
							{
								$sort = 'DESC';$by = 'e.modifieddate';$perPage = PERPAGE;$pageNo = 1;$searchData = '';
							}
							else 
							{
								$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
								$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'e.modifieddate';
								$perPage = $this->_getParam('per_page',PERPAGE);
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
									$searchQuery = rtrim($searchQuery," AND");						
								}
								/** search from grid - END **/
							}
									
							$objName = 'empskills';
							$tableFields = array('action'=>'Action','skillname'=>'Skill','yearsofexp'=>'Years of Experience','competencylevelid'=>'Competency Level','year_skill_last_used'=>'Skill Last Used Year');
							
							$tablecontent = $empskillsModel->getEmpSkillsData($sort, $by, $pageNo, $perPage,$searchQuery,$Uid);     
								$empcompetencyLevelsArr = $empskillsModel->empcompetencylevels($Uid);
								for($i=0;$i<sizeof($empcompetencyLevelsArr);$i++)
								{
									$levelsArr[$empcompetencyLevelsArr[$i]['id']] = $empcompetencyLevelsArr[$i]['competencylevel'];
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
											'menuName'=>TAB_EMP_SKILLS,
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
							$this->view->employeedata = $employeeData[0];
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
	
	public function jobhistoryeditAction()
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
					$usersModel = new Default_Model_Users();
                    $empdata = $employeeModal->getActiveEmployeeData($Uid);
					$employeeData = $usersModel->getUserDetailsByIDandFlag($Uid);
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
							$this->view->employeedata = $employeeData[0];
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
	
	public function expeditAction()
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
				$Uid = ($userid)?$userid:$userID;
				
				$employeeModal = new Default_Model_Employee();
				$usersModel = new Default_Model_Users();
                $empdata = $employeeModal->getActiveEmployeeData($Uid);
				$employeeData = $usersModel->getUserDetailsByIDandFlag($Uid);
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
						if(!empty($empdata))
						{
							$experiencedetailsModel = new Default_Model_Experiencedetails();	
							$view = Zend_Layout::getMvcInstance()->getView();		
						
							$objname = $this->_getParam('objname');
							$refresh = $this->_getParam('refresh');
						
							$data = array();	$searchQuery = '';	$searchArray = array();		$tablecontent = '';
						
							if($refresh == 'refresh')
							{
								$sort = 'DESC';$by = 'modifieddate';$perPage = PERPAGE;$pageNo = 1;$searchData = '';
							}
							else 
							{
								$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
								$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'modifieddate';
								$perPage = $this->_getParam('per_page',PERPAGE);
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
							'menuName'=>TAB_EMP_EXPERIENCE,
							'formgrid' => 'true',
							'unitId'=>$Uid,
							'call'=>$call,'context'=>'myteam',
							'search_filters' => array(
											'from_date' =>array('type'=>'datepicker'),
											'to_date' =>array('type'=>'datepicker')											
											)
							);			
						
						array_push($data,$dataTmp);
						
						$this->view->id=$Uid;	//User_id sending to view for tabs navigation....
						$this->view->controllername = $objName;
						$this->view->dataArray = $data;
						$this->view->call = $call ;
						$this->view->employeedata = $employeeData[0];
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
	
	public function edueditAction()
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
				$Uid = ($userid)?$userid:$userID;
				$employeeModal = new Default_Model_Employee();
				try
				{
					$usersModel = new Default_Model_Users();
                    $empdata = $employeeModal->getActiveEmployeeData($Uid);
					$employeeData = $usersModel->getUserDetailsByIDandFlag($Uid);
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
							$educationdetailsModel = new Default_Model_Educationdetails();	
										
							$view = Zend_Layout::getMvcInstance()->getView();		
							$objname = $this->_getParam('objname');
							$refresh = $this->_getParam('refresh');
							$data = array();	
							$searchQuery = '';
							$searchArray = array();
							$tablecontent = '';
							if($refresh == 'refresh')
							{
								$sort = 'DESC';$by = 'e.modifieddate';$perPage = PERPAGE;$pageNo = 1;$searchData = '';
							}
							else 
							{
								$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
								$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'e.modifieddate';
								$perPage = $this->_getParam('per_page',PERPAGE);
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
								'menuName'=>TAB_EMP_EDUCATION,
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
							$this->view->id=$Uid;
							$this->view->controllername = $objName;
							$this->view->dataArray = $data;
							$this->view->employeedata = $employeeData[0];
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
	
	public function trainingeditAction()
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
					$usersModel = new Default_Model_Users();
                    $empdata = $employeeModal->getActiveEmployeeData($Uid);
					$employeeData = $usersModel->getUserDetailsByIDandFlag($Uid);
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
							
							$data = array();$searchQuery = '';$searchArray = array();$tablecontent = '';
						
							if($refresh == 'refresh')
							{
								$sort = 'DESC';$by = 'modifieddate';$perPage = PERPAGE;$pageNo = 1;$searchData = '';
							}
							else 
							{
								$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
								$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'modifieddate';
								$perPage = $this->_getParam('per_page',PERPAGE);
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
							'menuName'=>TAB_EMP_TRAINING_CERTIFY,
							'formgrid' => 'true','unitId'=>$Uid,
							'call'=>$call,'context'=>'myteam');			
						
						array_push($data,$dataTmp);
						
						$this->view->id=$Uid;	//User_id sending to view for tabs navigation....
						$this->view->controllername = $objName;
						$this->view->dataArray = $data;
						$this->view->employeedata = $employeeData[0];
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
    
	public function additionaldetailseditAction()
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
		 	$conText ='myteam';
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
					$usersModel = new Default_Model_Users();
                    $empdata = $employeeModal->getActiveEmployeeData($Uid);
					$employeeData = $usersModel->getUserDetailsByIDandFlag($Uid);
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
							array_push($data,$dataTmp);
							$this->view->dataArray = $data;
							$this->view->call = $call ;
							$this->view->employeedata = $employeeData[0];
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
	
	public function pereditAction()
	{
		$identityDocumentArr = array();
		$documentsArr = array();
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);

			if(in_array('emppersonaldetails',$empOrganizationTabs)){

				$loginUserId ='';$loginUserGroup ='';$loginUserRole ='';
				$auth = Zend_Auth::getInstance();$emptyFlag=0;
				if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginUserGroup = $auth->getStorage()->read()->group_id;
					$loginUserRole = $auth->getStorage()->read()->emprole;
				}
				
				$id = $this->getRequest()->getParam('userid');
				if($id == '')		$id = $loginUserId;
				$callval = $this->getRequest()->getParam('call');
				if($callval == 'ajaxcall')
				$this->_helper->layout->disableLayout();
				//To check previlige for edit
				$myEmployees_model = new Default_Model_Myemployees();
			 	$getMyTeamIds = $myEmployees_model->getTeamIds($loginUserId);
			 	$teamIdArr = array();
			 	if(!empty($getMyTeamIds))
			 	{
				 	foreach($getMyTeamIds as $teamId)
				 	{
				 		array_push($teamIdArr,$teamId['user_id']);
				 	}
			 	}
			  	if($loginUserRole == SUPERADMINROLE || $loginUserGroup == MANAGEMENT_GROUP || $loginUserGroup == HR_GROUP || ($loginUserGroup == MANAGER_GROUP && in_array($id,$teamIdArr)))
			 	{
				try
				{
					if($id && is_numeric($id) && $id>0 && $id!=$loginUserId)
					{
						$employeeModal = new Default_Model_Employee();
						$usersModel = new Default_Model_Users();
                        $empdata = $employeeModal->getActiveEmployeeData($id);
						$employeeData = $usersModel->getUserDetailsByIDandFlag($id);
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
								$empperdetailsModal = new Default_Model_Emppersonaldetails();
								$usersModel = new Default_Model_Users();
								$emppersonaldetailsform = new Default_Form_emppersonaldetails();
								if($loginUserGroup == MANAGER_GROUP || $loginUserRole == SUPERADMINROLE)
								{
									$identitydocumentsModel = new Default_Model_Identitydocuments();
									$identityDocumentArr = $identitydocumentsModel->getIdentitydocumnetsrecord();
								}
								$genderModel = new Default_Model_Gender();
								$maritalstatusmodel = new Default_Model_Maritalstatus();
								$nationalitymodel = new Default_Model_Nationality();
								$ethniccodemodel = new Default_Model_Ethniccode();
								$racecodemodel = new Default_Model_Racecode();
								$languagemodel = new Default_Model_Language();
								$msgarray = array();

								$genderlistArr = $genderModel->getGenderList();
								if(!empty($genderlistArr))
								{
									foreach ($genderlistArr as $genderlistres){
										$emppersonaldetailsform->genderid->addMultiOption($genderlistres['id'],$genderlistres['gendername']);

									}
								}
								else
								{
									$msgarray['genderid'] = 'Gender is not configured yet.';
									$emptyFlag++;
								}

								$maritalstatuslistArr = $maritalstatusmodel->getMaritalStatusList();
								if(!empty($maritalstatuslistArr))
								{
									foreach ($maritalstatuslistArr as $maritalstatuslistres){
										$emppersonaldetailsform->maritalstatusid->addMultiOption($maritalstatuslistres['id'],$maritalstatuslistres['maritalstatusname']);

									}
								}else
								{
									$msgarray['maritalstatusid'] = 'Marital status is not configured yet.';
									$emptyFlag++;
								}

								$nationalitylistArr = $nationalitymodel->getNationalityList();
								if(!empty($nationalitylistArr))
								{
									foreach ($nationalitylistArr as $nationalitylistres){
										$emppersonaldetailsform->nationalityid->addMultiOption($nationalitylistres['id'],$nationalitylistres['nationalitycode']);

									}
								}else
								{
									$msgarray['nationalityid'] = 'Nationality is not configured yet.';
									$emptyFlag++;
								}

								$ethniccodeArr = $ethniccodemodel->gettotalEthnicCodeData();
								if(!empty($ethniccodeArr))
								{
									foreach ($ethniccodeArr as $ethniccoderes){
										$emppersonaldetailsform->ethniccodeid->addMultiOption($ethniccoderes['id'],$ethniccoderes['ethnicname']);

									}
								}else
								{
									$msgarray['ethniccodeid'] = 'Ethnic codes are not configured yet.';
									$emptyFlag++;
								}

								$racecodeArr = $racecodemodel->gettotalRaceCodeData();
								if(!empty($racecodeArr))
								{
									foreach ($racecodeArr as $racecoderes){
										$emppersonaldetailsform->racecodeid->addMultiOption($racecoderes['id'],$racecoderes['racename']);

									}
								}else
								{
									$msgarray['racecodeid'] = 'Race codes are not configured yet.';
									$emptyFlag++;
								}

								$languageArr = $languagemodel->gettotalLanguageData();
								if(!empty($languageArr))
								{
									foreach ($languageArr as $languageres){
										$emppersonaldetailsform->languageid->addMultiOption($languageres['id'],$languageres['languagename']);

									}
								}else
								{
									$msgarray['languageid'] = 'Languages are not configured yet.';
									$emptyFlag++;
								}
								if(!empty($identityDocumentArr))
								{
									$this->view->identitydocument = $identityDocumentArr;
								}


								$data = $empperdetailsModal->getsingleEmpPerDetailsData($id);
								if(!empty($data))
								{
									$emppersonaldetailsform->populate($data[0]);

									$dob = sapp_Global::change_date($data[0]["dob"], 'view');
									$emppersonaldetailsform->dob->setValue($dob);
									/*
									if($data[0]['celebrated_dob'] !='')
									{

										$celebrated_dob = sapp_Global::change_date($data[0]["celebrated_dob"], 'view');
										$emppersonaldetailsform->celebrated_dob->setValue($celebrated_dob);
									}
									*/
									if($data[0]['identity_documents'] !='')
									{
										$documentsArr = get_object_vars(json_decode($data[0]['identity_documents']));
										$documentsArr = sapp_Global::object_to_array($documentsArr);
										
									}
									$emppersonaldetailsform->setDefault('genderid',$data[0]['genderid']);
									$emppersonaldetailsform->setDefault('maritalstatusid',$data[0]['maritalstatusid']);
									$emppersonaldetailsform->setDefault('nationalityid',$data[0]['nationalityid']);
									$emppersonaldetailsform->setDefault('ethniccodeid',$data[0]['ethniccodeid']);
									$emppersonaldetailsform->setDefault('racecodeid',$data[0]['racecodeid']);
									$emppersonaldetailsform->setDefault('languageid',$data[0]['languageid']);
								}
								$emppersonaldetailsform->user_id->setValue($id);
								$emppersonaldetailsform->setAttrib('action',BASE_URL.'myemployees/peredit/userid/'.$id);
								
								
								$this->view->form = $emppersonaldetailsform;
								$this->view->data = $data;
								$this->view->documentsArr = $documentsArr;
								$this->view->id = $id;
								$this->view->msgarray = $msgarray;
								$this->view->employeedata = $employeeData[0];
								$this->view->emptyFlag=$emptyFlag;
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
				if($this->getRequest()->getPost())
				{
					$result = $this->persave($emppersonaldetailsform,$id,$identityDocumentArr);
					$this->view->msgarray = $result;
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
		}else{
			$this->_redirect('error');
		}
	}

	public function persave($emppersonaldetailsform,$userid,$identityDocumentArr)
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		
				$documentnameArr = array();
				$expiry_dateArr = array();
				$mandatorydocArr = array();
				
				$documentnameArr = $this->_request->getParam('document_name');
				$expiry_dateArr = $this->_request->getParam('expiry_date');
				$mandatorydocStr = $this->_request->getParam('mandatorydoc');
				
				$errorflag = 'true';
				if($mandatorydocStr !='')
				{
					$mandatorydocArr = explode(',',$mandatorydocStr);
				}
				if(!empty($documentnameArr) && !empty($mandatorydocArr))
				{
					for($i=0;$i<sizeof($documentnameArr);$i++)
					{
						if($mandatorydocArr[$i] == 1)
						{
							if($documentnameArr[$i] == '')
							{
								$msgarray[$i]['document_name'] = 'Please enter document name.';
								$errorflag = 'false';
							}
							else if(!preg_match('/^[a-zA-Z0-9.\- ?]+$/', $documentnameArr[$i]))
							{
								$msgarray[$i]['document_name'] = 'Please enter valid request type.';
								$errorflag = 'false';
							}	
						}else if($mandatorydocArr[$i] == 0)
						{
							if($documentnameArr[$i] !='')
							{
								if(!preg_match('/^[a-zA-Z0-9.\- ?]+$/', $documentnameArr[$i]))
								{
									$msgarray[$i]['document_name'] = 'Please enter valid request type.';
									$errorflag = 'false';
								}
							}
						}
						
					}
				}
				
				if(!empty($expiry_dateArr))
				{
					for($j=0;$j<sizeof($expiry_dateArr);$j++)
					{
							if($expiry_dateArr[$j] == '')
							{
								$msgarray[$j]['expiry_date'] = 'Please enter expiry date.';
								$errorflag = 'false';
							}	
					}
				}
				
				if($emppersonaldetailsform->isValid($this->_request->getPost()) && $errorflag == 'true'){
					$post_values = $this->_request->getPost();
		           	 if(isset($post_values['id']))
		                unset($post_values['id']);
		             if(isset($post_values['user_id']))
		                unset($post_values['user_id']);
		             if(isset($post_values['submit']))	
		                unset($post_values['submit']);
		        $new_post_values = array_filter($post_values);
		        if(!empty($new_post_values))
		        {
					$identitydocArr = array();
					$identitydoc = '';
					$expirydate = '';
					if(!empty($identityDocumentArr))
					{
						for($k=0;$k<sizeof($identityDocumentArr);$k++)
						{
							$identitydoc = isset($documentnameArr[$k])?$documentnameArr[$k]:'';
							if(isset($expiry_dateArr[$k]) && $expiry_dateArr[$k] !='empty')
								$expirydate = sapp_Global::change_date($expiry_dateArr[$k],'database');
							else
								$expirydate = '';	
							$identitydocArr[$identityDocumentArr[$k]['id']] = $identitydoc.':'.$expirydate;
						}
					}
					
					$identitydocjson = json_encode($identitydocArr);
					
					$empperdetailsModal = new Default_Model_Emppersonaldetails();
					$id = $this->_request->getParam('id');
					$user_id = $userid;
					$genderid = $this->_request->getParam('genderid');
					$maritalstatusid = $this->_request->getParam('maritalstatusid');
					$nationalityid = $this->_request->getParam('nationalityid');
					$ethniccodeid = $this->_request->getParam('ethniccodeid');
					$racecodeid = $this->_request->getParam('racecodeid');
					$languageid = $this->_request->getParam('languageid');
		
					$dob = $this->_request->getParam('dob');
					$dob = sapp_Global::change_date($dob, 'database');
					//$celebrated_dob = $this->_request->getParam('celebrated_dob');
					//$celebrated_dob = sapp_Global::change_date($celebrated_dob, 'database');
		
					$bloodgroup = $this->_request->getParam('bloodgroup');
		
					$date = new Zend_Date();
					$actionflag = '';
					$tableid  = '';
		
					$data = array('user_id'=>$user_id,
						                 'genderid'=>$genderid,
										 'maritalstatusid'=>$maritalstatusid,
		                                 'nationalityid'=>$nationalityid,
		                                 'ethniccodeid'=>$ethniccodeid,
		                                 'racecodeid'=>$racecodeid,
		                                 'languageid'=>$languageid,    								 
						      			 'dob'=>$dob,
										 //'celebrated_dob'=>($celebrated_dob!=''?$celebrated_dob:NULL),
						      			 'bloodgroup'=>($bloodgroup!=''?$bloodgroup:NULL),
										 'identity_documents'=>(!empty($identitydocArr)?$identitydocjson:NULL),	
										 'modifiedby'=>$loginUserId,
					                     'modifieddate'=>gmdate("Y-m-d H:i:s")
					);
					if($id!=''){
						$where = array('user_id=?'=>$user_id);
						$actionflag = 2;
					}
					else
					{
						$data['createdby'] = $loginUserId;
						$data['createddate'] = gmdate("Y-m-d H:i:s");
						$data['isactive'] = 1;
						$where = '';
						$actionflag = 1;
					}
					$Id = $empperdetailsModal->SaveorUpdateEmpPersonalData($data, $where);
					if($Id == 'update')
					{
						$tableid = $id;
						$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Employee personal details updated successfully."));
					}
					else
					{
						$tableid = $Id;
						$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Employee personal details added successfully."));
					}
					$menuID = EMPLOYEE;
					$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$user_id);
			}else
			{
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("error"=>FIELDMSG));
			}			
			$this->_redirect('myemployees/peredit/userid/'.$userid);
		}else
		{
			$messages = $emppersonaldetailsform->getMessages();
			foreach ($messages as $key => $val)
			{
				foreach($val as $key2 => $val2)
				{
					$msgarray[$key] = $val2;
					break;
				}
			}
			return $msgarray;
		}
	}
	
	public function comeditAction()
    {
        if(defined('EMPTABCONFIGS'))
        {
            $empOrganizationTabs = explode(",",EMPTABCONFIGS);
            if(in_array('empcommunicationdetails',$empOrganizationTabs))
            {
					
                $empDeptdata=array();$employeeData =array();
                $auth = Zend_Auth::getInstance();
                if($auth->hasIdentity()){
                     $loginUserId = $auth->getStorage()->read()->id;
					 $loginuserRole = $auth->getStorage()->read()->emprole;
					 $loginuserGroup = $auth->getStorage()->read()->group_id;
                }
                
                $id = $this->getRequest()->getParam('userid');
                if($id == '')		$id = $loginUserId;
                $callval = $this->getRequest()->getParam('call');
                if($callval == 'ajaxcall')
                $this->_helper->layout->disableLayout();
                //To check previlige for edit
				$myEmployees_model = new Default_Model_Myemployees();
			 	$getMyTeamIds = $myEmployees_model->getTeamIds($loginUserId);
			 	$teamIdArr = array();
			 	if(!empty($getMyTeamIds))
			 	{
				 	foreach($getMyTeamIds as $teamId)
				 	{
				 		array_push($teamIdArr,$teamId['user_id']);
				 	}
			 	}
				if($loginuserRole == SUPERADMINROLE || $loginuserGroup == MANAGEMENT_GROUP || $loginuserGroup == HR_GROUP || ($loginuserGroup == MANAGER_GROUP && in_array($id,$teamIdArr)))
			 	{
                try
                {
                    if($id && is_numeric($id) && $id>0 && $id!=$loginUserId)
                    {
                        $employeeModal = new Default_Model_Employee();
                        $usersModel = new Default_Model_Users();
                        $empdata = $employeeModal->getActiveEmployeeData($id);
						$employeeData = $usersModel->getUserDetailsByIDandFlag($id);
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
                                $empDept = $empdata[0]['department_id'];
                                $empcommdetailsform = new Default_Form_empcommunicationdetails();
								
                                $empcommdetailsModal = new Default_Model_Empcommunicationdetails();
                                $usersModel = new Default_Model_Users();
                                $countriesModel = new Default_Model_Countries();
                                $statesmodel = new Default_Model_States();
                                $citiesmodel = new Default_Model_Cities();
                                $orgInfoModel = new Default_Model_Organisationinfo();
                                $msgarray = array();
								$orgid = 1;
								$countryId = '';
								$stateId = '';
								$cityId = '';
								
                                $deptModel = new Default_Model_Departments();
								if($empDept !='' && $empDept !='NULL')
								{
									$empDeptdata = $deptModel->getEmpdepartmentdetails($empDept);
									if(!empty($empDeptdata))
									{
										$countryId = $empDeptdata[0]['country'];
										$stateId = $empDeptdata[0]['state'];
										$cityId = $empDeptdata[0]['city'];
									}
								}	
								else
								{
									$empDeptdata = $orgInfoModel->getOrganisationDetails($orgid);
									if(!empty($empDeptdata))
									{
										$countryId = $empDeptdata[0]['country'];
										$stateId = $empDeptdata[0]['state'];
										$cityId = $empDeptdata[0]['city'];
									}
								}	
									if($countryId !='')
										$countryData = $countriesModel->getActiveCountryName($countryId);
                                    if(!empty($countryData))
                                        $empDeptdata[0]['country'] = $countryData[0]['country'];
                                    else
                                        $empDeptdata[0]['country'] ='';
									
									if($stateId !='')
										$stateData = $statesmodel->getStateNameData($stateId);
                                    if(!empty($stateData))
                                        $empDeptdata[0]['state'] = $stateData[0]['state'];
                                    else
                                        $empDeptdata[0]['state'] ='';
									
									if($cityId !='')
										$citiesData = $citiesmodel->getCitiesNameData($cityId);
                                    if(!empty($citiesData))
                                        $empDeptdata[0]['city'] = $citiesData[0]['city'];
                                    else
                                        $empDeptdata[0]['city'] ='';
                                $countrieslistArr = $countriesModel->getTotalCountriesList();
                                if(sizeof($countrieslistArr)>0)
                                {
                                    $empcommdetailsform->perm_country->addMultiOption('','Select Country');
                                    $empcommdetailsform->current_country->addMultiOption('','Select Country');
                                    foreach ($countrieslistArr as $countrieslistres)
                                    {
                                        $empcommdetailsform->perm_country->addMultiOption($countrieslistres['id'],  utf8_encode($countrieslistres['country_name']));
                                        $empcommdetailsform->current_country->addMultiOption($countrieslistres['id'],utf8_encode($countrieslistres['country_name']));
                                    }
                                }
                                else
                                {
                                    $msgarray['perm_country'] = 'Countries are not configured yet.';
                                    $msgarray['current_country'] = 'Countries are not configured yet.';
                                }
                                $data = $empcommdetailsModal->getsingleEmpCommDetailsData($id);
                                if(!empty($data))
                                {
                                    $perm_country = $data[0]['perm_country'];
                                    if(isset($_POST['perm_country']))
                                    {
                                        $perm_country = $_POST['perm_country'];
                                    }
                                    $perm_state = $data[0]['perm_state'];
                                    if(isset($_POST['perm_state']))
                                    {
                                        $perm_state = $_POST['perm_state'];
                                    }
                                    $perm_city = $data[0]['perm_city'];
                                    if(isset($_POST['perm_city']))
                                    {
                                        $perm_city = $_POST['perm_city'];
                                    }
                                    if($perm_country != '')
                                    {
                                        $statePermlistArr = $statesmodel->getStatesList($perm_country);
                                        if(sizeof($statePermlistArr)>0)
                                        {
                                            $empcommdetailsform->perm_state->addMultiOption('','Select State');
                                            foreach($statePermlistArr as $statelistres)
                                            {
                                                $empcommdetailsform->perm_state->addMultiOption($statelistres['id'],$statelistres['state_name']);
                                            }
                                        }
                                    }
                                    if($perm_state != '')
                                    {
                                        $cityPermlistArr = $citiesmodel->getCitiesList($perm_state);
                                        if(sizeof($cityPermlistArr)>0)
                                        {
                                            $empcommdetailsform->perm_city->addMultiOption('','Select City');
                                            foreach($cityPermlistArr as $cityPermlistres)
                                            {
                                                $empcommdetailsform->perm_city->addMultiOption($cityPermlistres['id'],$cityPermlistres['city_name']);
                                            }
                                        }
                                    }
                                    $current_country = $data[0]['current_country'];
                                    if(isset($_POST['current_country']))
                                    {
                                        $current_country = $_POST['current_country'];
                                    }
                                    $current_state = $data[0]['current_state'];
                                    if(isset($_POST['current_state']))
                                    {
                                        $current_state = $_POST['current_state'];
                                    }
                                    $current_city = $data[0]['current_city'];
                                    if(isset($_POST['current_city']))
                                    {
                                        $current_city = $_POST['current_city'];
                                    }
                                    if($current_country != '')
                                    {
                                        $statecurrlistArr = $statesmodel->getStatesList($current_country);
                                        if(sizeof($statecurrlistArr)>0)
                                        {
                                            $empcommdetailsform->current_state->addMultiOption('','Select State');
                                            foreach($statecurrlistArr as $statecurrlistres)
                                            {
                                                $empcommdetailsform->current_state->addMultiOption($statecurrlistres['id'],$statecurrlistres['state_name']);
                                            }
                                        }
                                    }
                                    if($current_state != '')
                                    {
                                        $cityCurrlistArr = $citiesmodel->getCitiesList($current_state);

                                        if(sizeof($cityCurrlistArr)>0)
                                        {
                                            $empcommdetailsform->current_city->addMultiOption('','Select City');
                                            foreach($cityCurrlistArr as $cityCurrlistres)
                                            {
                                                $empcommdetailsform->current_city->addMultiOption($cityCurrlistres['id'],$cityCurrlistres['city_name']);
                                            }
                                        }
                                    }
                                    $empcommdetailsform->populate($data[0]);
                                    $empcommdetailsform->setDefault('perm_country',$perm_country);
                                    $empcommdetailsform->setDefault('perm_state',$perm_state);
                                    $empcommdetailsform->setDefault('perm_city',$perm_city);
                                    if($data[0]['current_country'] != '')
                                        $empcommdetailsform->setDefault('current_country',$current_country);
                                    if($data[0]['current_state'] !='')
                                        $empcommdetailsform->setDefault('current_state',$current_state);
                                    if($data[0]['current_city'] != '')
                                        $empcommdetailsform->setDefault('current_city',$current_city);
                                }
                                $empcommdetailsform->setAttrib('action',BASE_URL.'myemployees/comedit/userid/'.$id);
                                $empcommdetailsform->user_id->setValue($id);
                                $this->view->employeedata = $employeeData[0];
                                if(!empty($empDeptdata))
                                    $this->view->dataArray = $empDeptdata[0];
                                else
                                    $this->view->dataArray = $empDeptdata;
                                $this->view->form = $empcommdetailsform;
                                $this->view->data = $data;
                                $this->view->id = $id;
                                $this->view->msgarray = $msgarray;
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
                if($this->getRequest()->getPost())
                {
                    $result = $this->comsave($empcommdetailsform,$id);
                    $this->view->msgarray = $result;
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
        else
        {
            $this->_redirect('error');
        }
    }
    
	public function comsave($empcommdetailsform,$userid)
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$perm_country = $this->_request->getParam('perm_country');
		$perm_stateparam = $this->_request->getParam('perm_state');
		$perm_stateArr = explode("!@#",$this->_request->getParam('perm_state'));
		$perm_state = $perm_stateArr[0];
		$perm_cityparam = $this->_request->getParam('perm_city');
		$perm_cityArr = explode("!@#",$this->_request->getParam('perm_city'));
		$perm_city = $perm_cityArr[0];
		$address_flag = $this->_request->getParam('address_flag');
		$current_country = $this->_request->getParam('current_country');
		$current_stateparam = $this->_request->getParam('current_state');
		$current_stateArr = explode("!@#",$this->_request->getParam('current_state'));
		$current_state = $current_stateArr[0];
		$current_cityparam = $this->_request->getParam('current_city');
		$current_cityArr = explode("!@#",$this->_request->getParam('current_city'));
		$current_city = $current_cityArr[0];

		if($empcommdetailsform->isValid($this->_request->getPost())){
			 $post_values = $this->_request->getPost();
           	 if(isset($post_values['id']))
                unset($post_values['id']);
             if(isset($post_values['user_id']))
                unset($post_values['user_id']);
             if(isset($post_values['submit']))	
                unset($post_values['submit']);
           $new_post_values = array_filter($post_values);
           if(!empty($new_post_values))
           {
			$empcommdetailsModal = new Default_Model_Empcommunicationdetails();
			$id = $this->_request->getParam('id');
			$user_id = $userid;
			$personalemail = $this->_request->getParam('personalemail');
			$perm_streetaddress = $this->_request->getParam('perm_streetaddress');

			$perm_pincode = $this->_request->getParam('perm_pincode');

			$current_streetaddress = $this->_request->getParam('current_streetaddress');

			$current_pincode = $this->_request->getParam('current_pincode');
			$emergency_number = $this->_request->getParam('emergency_number');
			$emergency_name = $this->_request->getParam('emergency_name');
			$emergency_email = $this->_request->getParam('emergency_email');
			$date = new Zend_Date();
			$actionflag = '';
			$tableid  = '';
			$data = array('user_id'=>$user_id,
				                 'personalemail'=>$personalemail,
								 'perm_streetaddress'=>$perm_streetaddress, 								 
				      			 'perm_country'=>($perm_country!=''?$perm_country:NULL),
								 'perm_state'=>($perm_state!=''?$perm_state:NULL),
								 'perm_city'=>($perm_city!=''?$perm_city:NULL),
								 'perm_pincode'=>$perm_pincode,
                                 'current_streetaddress'=>($current_streetaddress!=''?$current_streetaddress:NULL), 
                                 'current_country'=>($current_country!=''?$current_country:NULL), 								 
				      			 'current_state'=>($current_state!=''?$current_state:NULL),
								 'current_city'=>($current_city!=''?$current_city:NULL), 								 
				      			 'current_pincode'=>($current_pincode!=''?$current_pincode:NULL),
								 'emergency_number'=>($emergency_number!=''?$emergency_number:NULL),
								 'emergency_name'=>($emergency_name!=''?$emergency_name:NULL),
								 'emergency_email'=>($emergency_email!=''?$emergency_email:NULL),
								 'modifiedby'=>$loginUserId,
			                     'modifieddate'=>gmdate("Y-m-d H:i:s")			
			);
			if($id!=''){
				$where = array('user_id=?'=>$user_id);
				$actionflag = 2;
			}
			else
			{
				$data['createdby'] = $loginUserId;
				$data['createddate'] = gmdate("Y-m-d H:i:s");
				$data['isactive'] = 1;
				$where = '';
				$actionflag = 1;
			}
			$Id = $empcommdetailsModal->SaveorUpdateEmpcommData($data, $where);
			if($Id == 'update')
			{
				$tableid = $id;
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Employee contact details updated successfully."));
			}
			else
			{
				$tableid = $Id;
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Employee contact details added successfully."));
			}
			$menuID = EMPLOYEE;
			$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$user_id);
           }
			else
           {
           		$this->_helper->getHelper("FlashMessenger")->addMessage(array("error"=>FIELDMSG));
           }	
			$this->_redirect('myemployees/comedit/userid/'.$userid);
		}else
		{
			$messages = $empcommdetailsform->getMessages();
			foreach ($messages as $key => $val)
			{
				foreach($val as $key2 => $val2)
				{
					$msgarray[$key] = $val2;
					break;
				}
			}
			if(isset($perm_country) && $perm_country != 0 && $perm_country != '')
			{
				$statesmodel = new Default_Model_States();
				$statesmodeldata = $statesmodel->getStatesList($perm_country);
				$empcommdetailsform->perm_state->clearMultiOptions();
				$empcommdetailsform->perm_city->clearMultiOptions();
				$empcommdetailsform->perm_state->addMultiOption('','Select State');
				foreach($statesmodeldata as $res)
				$empcommdetailsform->perm_state->addMultiOption($res['id'].'!@#'.utf8_encode($res['state_name']),utf8_encode($res['state_name']));
				if(isset($perm_stateparam) && $perm_stateparam != 0 && $perm_stateparam != '')
				$empcommdetailsform->setDefault('perm_state',$perm_stateparam);
			}
			if(isset($perm_stateparam) && $perm_stateparam != 0 && $perm_stateparam != '')
			{
				$citiesmodel = new Default_Model_Cities();
				$citiesmodeldata = $citiesmodel->getCitiesList($perm_state);
					
				$empcommdetailsform->perm_city->addMultiOption('','Select City');
				foreach($citiesmodeldata as $res)
				$empcommdetailsform->perm_city->addMultiOption($res['id'].'!@#'.utf8_encode($res['city_name']),utf8_encode($res['city_name']));
				if(isset($perm_cityparam) && $perm_cityparam != 0 && $perm_cityparam != '')
				$empcommdetailsform->setDefault('perm_city',$perm_cityparam);
			}
			if(isset($current_country) && $current_country != 0 && $current_country != '')
			{
				$statesmodel = new Default_Model_States();
				$statesmodeldata = $statesmodel->getStatesList($current_country);
					
				$empcommdetailsform->current_state->addMultiOption('','Select State');
				foreach($statesmodeldata as $res)
				$empcommdetailsform->current_state->addMultiOption($res['id'].'!@#'.utf8_encode($res['state_name']),utf8_encode($res['state_name']));
				if(isset($current_stateparam) && $current_stateparam != 0 && $current_stateparam != '')
				$empcommdetailsform->setDefault('current_state',$current_stateparam);
			}
			if(isset($current_stateparam) && $current_stateparam != 0 && $current_stateparam != '')
			{
				$citiesmodel = new Default_Model_Cities();
				$citiesmodeldata = $citiesmodel->getCitiesList($current_state);
					
				$empcommdetailsform->current_city->addMultiOption('','Select City');
				foreach($citiesmodeldata as $res)
				$empcommdetailsform->current_city->addMultiOption($res['id'].'!@#'.utf8_encode($res['city_name']),utf8_encode($res['city_name']));
				if(isset($current_cityparam) && $current_cityparam != 0 && $current_cityparam != '')
				$empcommdetailsform->setDefault('current_city',$current_cityparam);
			}
			return $msgarray;
		}
	}
	
	public function docviewAction()
	{
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);

		 	if(in_array('employeedocs',$empOrganizationTabs))
		 	{
			 	$auth = Zend_Auth::getInstance();
			 	if($auth->hasIdentity())
			 	{
			 		$loginUserId = $auth->getStorage()->read()->id;
			 		$loginUserGroup = $auth->getStorage()->read()->group_id;
					$loginUserRole = $auth->getStorage()->read()->emprole;
		 		}

			 	$id = $this->getRequest()->getParam('userid');
			 	
			 	$myEmployees_model = new Default_Model_Myemployees();
			 	$getMyTeamIds = $myEmployees_model->getTeamIds($loginUserId);
			 	$teamIdArr = array();
			 	if(!empty($getMyTeamIds))
			 	{
				 	foreach($getMyTeamIds as $teamId)
				 	{
				 		array_push($teamIdArr,$teamId['user_id']);
				 	}
			 	}
				if($loginUserRole == SUPERADMINROLE || $loginUserGroup == MANAGEMENT_GROUP || $loginUserGroup == HR_GROUP || ($loginUserGroup == MANAGER_GROUP && in_array($id,$teamIdArr)))
			 	{
			 	try
			 	{
			 		if($id && is_numeric($id) && $id>0 && $id!=$loginUserId)
					{
						$employeeModal = new Default_Model_Employee();
						$empdata = $employeeModal->getActiveEmployeeData($id);
						if(!empty($empdata))
						{
							$empDocuModel = new Default_Model_Employeedocs();
							$empDocuments = $empDocuModel->getEmpDocumentsByFieldOrAll('user_id',$id);

							$this->view->empDocuments = $empDocuments;
						}
						
						$usersModel = new Default_Model_Users();
						$employeeData = $usersModel->getUserDetailsByIDandFlag($id);
						if(!empty($employeeData))
							$this->view->employeedata = $employeeData[0];
							$this->view->id = $id;
							$this->view->empdata = $empdata;
					} else {
				   		$this->view->rowexist = "norows";
					}
			 	}
			 	catch(Exception $e) {
		 			$this->view->rowexist = "norows";
		 		}
		 		
		 		// Show message to user when document was deleted by other user.
		 		$this->view->messages = $this->_helper->flashMessenger->getMessages();
		 	}
			else{
		 		$this->_redirect('error');
		 	}
			}else{
		 		$this->_redirect('error');
		 	}
		}else{
			$this->_redirect('error');
		}
	}
	
	public function doceditAction()
	{
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);

		 	if(in_array('employeedocs',$empOrganizationTabs))
		 	{
			 	$auth = Zend_Auth::getInstance();
			 	if($auth->hasIdentity())
			 	{
			 		$loginUserId = $auth->getStorage()->read()->id;
			 		$loginUserGroup = $auth->getStorage()->read()->group_id;
					$loginUserRole = $auth->getStorage()->read()->emprole;
		 		}

			 	$id = $this->getRequest()->getParam('userid');
			 	
		 		$myEmployees_model = new Default_Model_Myemployees();
			 	$getMyTeamIds = $myEmployees_model->getTeamIds($loginUserId);
			 	$teamIdArr = array();
			 	if(!empty($getMyTeamIds))
			 	{
				 	foreach($getMyTeamIds as $teamId)
				 	{
				 		array_push($teamIdArr,$teamId['user_id']);
				 	}
			 	}
				if($loginUserRole == SUPERADMINROLE || $loginUserGroup == MANAGEMENT_GROUP || $loginUserGroup == HR_GROUP || ($loginUserGroup == MANAGER_GROUP && in_array($id,$teamIdArr)))
			 	{
			 	try
			 	{
			 		if($id && is_numeric($id) && $id>0 && $id!=$loginUserId)
					{
						$employeeModal = new Default_Model_Employee();
						$empdata = $employeeModal->getActiveEmployeeData($id);
						if(!empty($empdata))
						{
							$empDocuModel = new Default_Model_Employeedocs();
							$empDocuments = $empDocuModel->getEmpDocumentsByFieldOrAll('user_id',$id);

							$this->view->empDocuments = $empDocuments;
						}
						
						$usersModel = new Default_Model_Users();
						$employeeData = $usersModel->getUserDetailsByIDandFlag($id);
						if(!empty($employeeData))
							$this->view->employeedata = $employeeData[0];
						$this->view->id = $id;
						$this->view->empdata = $empdata;
					} else {
				   		$this->view->rowexist = "norows";
					}
			 	}
			 	catch(Exception $e) {
		 			$this->view->rowexist = "norows";
		 		}
		 		
		 		// Show message to user when document was deleted by other user.
		 		$this->view->messages = $this->_helper->flashMessenger->getMessages();
		 	}else{
		 		$this->_redirect('error');
		 	}
		 	}else{
		 		$this->_redirect('error');
		 	}
		}else{
			$this->_redirect('error');
		}
	}
	
	
	/**
	 * 
	 * Show analytics of employees reporting to manager 
	 */
	public function employeereportAction()
	{
			$auth = Zend_Auth::getInstance();
            if($auth->hasIdentity())
            {
                $loginUserId = $auth->getStorage()->read()->id;
                $loginuserRole = $auth->getStorage()->read()->emprole;
                $loginuserGroup = $auth->getStorage()->read()->group_id;
            }
            if($loginuserGroup==MANAGER_GROUP || $loginuserGroup==MANAGEMENT_GROUP || $loginuserRole==SUPERADMINROLE)
            {
	            $norec_arr = array();
	            $form = new Default_Form_Employeereport();
	            $requi_model = new Default_Model_Requisition();
	            $employmentstatusModel = new Default_Model_Employmentstatus();
	            $role_model = new Default_Model_Roles();
	            $departmentsmodel = new Default_Model_Departments();
	            $bu_model = new Default_Model_Businessunits();
	
	            $roles_arr = $role_model->getRolesList_EMP();
	            $job_data = $requi_model->getJobTitleList();
	            $employmentStatusData = $employmentstatusModel->getempstatuslist();
	            if(count($job_data)==0)
	            {
	                $norec_arr['jobtitle_id'] = "Job titles are not configured yet.";
	                $norec_arr['position_id'] = "Positions are not configured yet.";
	            }
	            if(count($employmentStatusData)==0)
	            {
	                $norec_arr['emp_status_id'] = "Employment status is not configured yet.";
	            }
	            $form->jobtitle_id->addMultiOptions(array(''=>'Select Job Title')+$job_data);
	            if(count($employmentStatusData) > 0)
	            {
	                    $form->emp_status_id->addMultiOption('','Select Employment Status');
	                    foreach ($employmentStatusData as $employmentStatusres)
	                    {
	                            $form->emp_status_id->addMultiOption($employmentStatusres['workcodename'],$employmentStatusres['statusname']);
	                    }
	            }
	            if(sizeof($roles_arr) > 0)
	            {
	                    $form->emprole->addMultiOptions(array(''=>'Select Role')+$roles_arr);
	            }
	            else
	            {
	                    $norec_arr['emprole'] = 'Roles are not added yet.';
	            }
	                        
	            $bu_arr = $bu_model->getBU_report();
	            if(!empty($bu_arr))
	            {
	                foreach ($bu_arr as $bu)
	                {
	                    $form->businessunit_id->addMultiOption($bu['id'],utf8_encode($bu['bu_name']));
	                }
	            }
	            else
	            {
	                $norec_arr['businessunit_id'] = 'Business Units are not added yet.';
	            }
	            
	            // Show count of employees reporting to manager
				
				// Get employees data reporting to manager
				$myEmployees_model = new Default_Model_Myemployees();
				
	            $employee_model = new Default_Model_Employee();
	            
	            //$this->_helper->layout->setLayout("analyticslayout");
	            
	            $this->view->count_emp_reporting = $employee_model->getCountEmpReporting($myEmployees_model->getLoginUserId());
	            
	            $this->view->form = $form;
	            $this->view->messages = $norec_arr;
	            $this->view->ermsg = '';
            }
            else
            {
            	$this->render('error/error.phtml');
            }
	}	

	/**
	 * 
	 * Get employee data to show analytics of employees reporting to manager
	 */
	public function getempreportdataAction()
	{
		try {
			$param_arr = $this->_getAllParams();
			$cols_param_arr = $this->_getParam('cols_arr',array());
			if(isset($param_arr['cols_arr']))
	                    unset($param_arr['cols_arr']);
			$page_no = isset($param_arr['page_no'])?$param_arr['page_no']:1;
			$per_page = isset($param_arr['per_page'])?$param_arr['per_page']:PERPAGE;
			$sort_name = $param_arr['sort_name'];
			$sort_type = $param_arr['sort_type'];
			if(isset($param_arr['page_no']))unset($param_arr['page_no']);
			if(isset($param_arr['sort_name']))unset($param_arr['sort_name']);
			if(isset($param_arr['sort_type']))unset($param_arr['sort_type']);
			if(isset($param_arr['per_page']))unset($param_arr['per_page']);
			
			// Get employees data reporting to manager
			$myEmployees_model = new Default_Model_Myemployees();
			$param_arr['reporting_manager'] = $myEmployees_model->getLoginUserId();
			 
			unset($param_arr['module']);unset($param_arr['controller']);unset($param_arr['action']);unset($param_arr['format']);
	
			$employee_model = new Default_Model_Employee();
	
			$emp_data_org = $employee_model->getdata_emp_report($param_arr,$per_page,$page_no,$sort_name,$sort_type);
			$page_cnt = $emp_data_org['page_cnt'];
			$emp_arr = $emp_data_org['rows'];
			$columns_array = $this->empreport_heplper1('all');
			$mandatory_array = $this->empreport_heplper1('mandatory');
			if(count($cols_param_arr)  == 0)
			$cols_param_arr = $mandatory_array;
			$mandatory_array = array_keys($mandatory_array);
			$this->view->emp_arr = $emp_arr;
			$this->view->page_cnt = $page_cnt;
			$this->view->per_page = $per_page;
			$this->view->page_no = $page_no;
			$this->view->cols_param_arr = $cols_param_arr;
			$this->view->sort_name = $sort_name;
			$this->view->sort_type = $sort_type;
			$this->view->columns_array = $columns_array;
		} catch(Exception $e) {
			exit($e->getMessage());
		}
	}
		
	/**
	 * 
	 * Employee report helper
	 * @param String $type
	 */
	public function empreport_heplper1($type)
	{
		$columns_array = array(
                        'employeeId' => 'Employee ID',
                        'userfullname' => 'Employee',
                        'emailaddress' => 'Email',
                        'contactnumber' => 'Mobile',
                        'emprole_name' => 'Role',
                        'date_of_joining' => 'Date of Joining',
                        'modeofentry' => 'Mode of Employment',
                        'jobtitle_name' => 'Job Title',
                        'position_name' => 'Position',
                        'businessunit_name' => 'Business Unit',
                        'department_name' => 'Department',
                        'emp_status_name' => 'Employment Status',
                        'date_of_leaving' => 'Date of Leaving',
                        'years_exp' => 'Years of Experience',
                        'holiday_group_name' => 'Holiday Group',
                        'office_number' => 'Work Phone',
                        'extension_number' => 'Extension Number',
                        'backgroundchk_status' => 'Background Check Status',
                        'other_modeofentry' => 'Mode of Entry(Other)',
						'freqtype' => 'Pay Frequency',//04-02-2015
                        'salary' => 'Salary',//04-02-2015
                        'referer_name' => 'Referred By',
						'isactive'=>'User Status'
		);
		$mandatory_array = array(
                        'employeeId' => 'Employee ID',
                        'userfullname' => 'Employee',
                        'emailaddress' => 'Email',
                        'contactnumber' => 'Mobile',
                        'emprole_name' => 'Role',
                        'jobtitle_name' => 'Job Title',
                        'position_name' => 'Position',
                        'businessunit_name' => 'Business Unit',
                        'department_name' => 'Department',
                        'emp_status_name' => 'Employment Status',
                        'date_of_joining' => 'Date of Joining', 
						'isactive'=>'User Status'                                   
		);
		if($type == 'all')
		return $columns_array;
		else
		return $mandatory_array;
	}
	
	/**
	 * 
	 * Show auto suggestions to filters in report
	 */		
	public function empautoAction()
	{
		$params = $this->_getAllParams();
		$output = array(array('id' => '','value' => 'No records','label' => 'No records'));
		$emp_model = new Default_Model_Employee();
		if($params['term'] != '')
		{
			
			// Get reporting manager ID
			$myEmployees_model = new Default_Model_Myemployees();
			$params['reporting_manager'] = $myEmployees_model->getLoginUserId();
			
			// Get employees data by search criteria and reporting manager
			$emp_arr = $emp_model->getAutoReportEmployee($params);
			if(count($emp_arr)>0)
			{
				$output = array();
				foreach($emp_arr as $emp)
				{
					$output[] = array('id' => $emp['user_id'],'value' => $emp[$params['field']],'label' => $emp[$params['field']],'profile_img' => $emp['profileimg']);
				}
			}
		}
		$this->_helper->json($output);
	}

	/**
	 * Export analytics of employees reporting to manager to PDF
	 */
	public function emprptpdfAction()
	{
		$this->_helper->layout->disableLayout();
		$param_arr = $this->_getAllParams();

		$cols_param_arr = $this->_getParam('cols_arr',array());
		if(isset($param_arr['cols_arr']))	unset($param_arr['cols_arr']);
		$page_no = isset($param_arr['page_no'])?$param_arr['page_no']:1;
		$per_page = isset($param_arr['per_page'])?$param_arr['per_page']:PERPAGE;
		$sort_name = $param_arr['sort_name'];
		$sort_type = $param_arr['sort_type'];
		if(isset($param_arr['page_no']))unset($param_arr['page_no']);
		if(isset($param_arr['sort_name']))unset($param_arr['sort_name']);
		if(isset($param_arr['sort_type']))unset($param_arr['sort_type']);
		if(isset($param_arr['per_page']))unset($param_arr['per_page']);
		unset($param_arr['module']);unset($param_arr['controller']);unset($param_arr['action']);
			
		// Get employees data reporting to manager
		$myEmployees_model = new Default_Model_Myemployees();
		$param_arr['reporting_manager'] = $myEmployees_model->getLoginUserId();
		
		if(count($cols_param_arr) == 0)
		$cols_param_arr = $this->empreport_heplper1('mandatory');
		$employee_model = new Default_Model_Employee();
		$emp_data_org = $employee_model->getdata_emp_report($param_arr,$per_page,$page_no,$sort_name,$sort_type);
		$emp_arr = $emp_data_org['rows'];
		for($i=0;$i<=sizeof($emp_arr);$i++)
		{
			if(isset($emp_arr[$i]['prefix_name']) && $emp_arr[$i]['prefix_name']!='')
			{
				$emp_arr[$i]['userfullname'] = $emp_arr[$i]['prefix_name'].'. '.$emp_arr[$i]['userfullname'];
			}
		}
		$field_names = array();
		$field_widths = array();
		$data['field_name_align'] = array();

		foreach($cols_param_arr as $column_key => $column_name)
		{
			$field_names[] = array(
                                        'field_name'=>$column_key,
                                        'field_label'=>$column_name
			);
			$field_widths[] = 25;
			$data['field_name_align'][] = 'C';
		}
		if(count($cols_param_arr) != 7)
		{
			$totalPresentFieldWidth = array_sum($field_widths);
			foreach($field_widths as $key => $width)
			{
				$field_widths[$key] = ($width*180)/$totalPresentFieldWidth;
			}
		}
		
        // Show count of employees reporting to manager
		
		// Get employees data reporting to manager
		$myEmployees_model = new Default_Model_Myemployees();
		
        $employee_model = new Default_Model_Employee();
            
        $count_emp_reporting = $employee_model->getCountEmpReporting($myEmployees_model->getLoginUserId());
            		
		$data = array('grid_no'=>1, 'project_name'=>'', 'object_name'=>'Employee Report', 'grid_count'=>1,'file_name'=>'EmployeeRpt.pdf', 'count_emp_reporting' => $count_emp_reporting);

		$pdf = $this->_helper->PdfHelper->generateReport($field_names, $emp_arr, $field_widths, $data);

		
		$this->_helper->json(array('file_name'=>$data['file_name']));
	}

	/**
	 * Export analytics of employees reporting to manager to excel.
	 */
	public function exportemployeereportAction()
	{
		$this->_helper->layout->disableLayout();
		$param_arr = $this->_getAllParams();

		$cols_param_arr = $this->_getParam('cols_arr',array());
		if(isset($param_arr['cols_arr']))	unset($param_arr['cols_arr']);
		$page_no = isset($param_arr['page_no'])?$param_arr['page_no']:1;
		$per_page = isset($param_arr['per_page'])?$param_arr['per_page']:PERPAGE;
		$sort_name = $param_arr['sort_name'];
		$sort_type = $param_arr['sort_type'];
		if(isset($param_arr['page_no']))unset($param_arr['page_no']);
		if(isset($param_arr['sort_name']))unset($param_arr['sort_name']);
		if(isset($param_arr['sort_type']))unset($param_arr['sort_type']);
		if(isset($param_arr['per_page']))unset($param_arr['per_page']);
		unset($param_arr['module']);unset($param_arr['controller']);unset($param_arr['action']);
			
		// Get employees data reporting to manager
		$myEmployees_model = new Default_Model_Myemployees();
		$param_arr['reporting_manager'] = $myEmployees_model->getLoginUserId();
		
		if(count($cols_param_arr) == 0)
		$cols_param_arr = $this->empreport_heplper1('mandatory');
		$employee_model = new Default_Model_Employee();
		$emp_data_org = $employee_model->getdata_emp_report($param_arr,$per_page,$page_no,$sort_name,$sort_type);
		$emp_arr = $emp_data_org['rows'];
		require_once 'Classes/PHPExcel.php';
		require_once 'Classes/PHPExcel/IOFactory.php';
		$objPHPExcel = new PHPExcel();

		$letters = range('A','Z');
		$count =0;
		$filename = "EmployeeReport.xlsx";
		$cell_name="";
		
        // Show count of employees reporting to manager
		
		// Get employees data reporting to manager
		$myEmployees_model = new Default_Model_Myemployees();
		
        $employee_model = new Default_Model_Employee();
            
        $count_emp_reporting = $employee_model->getCountEmpReporting($myEmployees_model->getLoginUserId());
		$objPHPExcel->getActiveSheet()->SetCellValue($letters[$count]."1", "My Team Count : ".$count_emp_reporting);
		
		// Make first row Headings bold and highlighted in Excel.
		foreach ($cols_param_arr as $names)
		{
			$i = 2;
			$cell_name = $letters[$count].$i;
			$names = html_entity_decode($names,ENT_QUOTES,'UTF-8');

			$objPHPExcel->getActiveSheet()->SetCellValue($cell_name, $names);
			// Make bold cells
			$objPHPExcel->getActiveSheet()->getStyle($cell_name)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle($cell_name)->applyFromArray( array(
									        'fill' => array(
									            'type' => PHPExcel_Style_Fill::FILL_SOLID,
									            'color' => array('rgb' => '82CAFF')
			)
			)
			);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letters[$count])->setAutoSize(true);
			$i++;
			$count++;
		}

		// Display field/column values in Excel.
		$i = 3;
		foreach($emp_arr as $emp_data)
		{
			$count1 =0;
			foreach ($cols_param_arr as $column_key => $column_name)
			{
				// display field/column values
				$cell_name = $letters[$count1].$i;


				if($column_key == 'userfullname')
				{
					$value = isset($emp_data['prefix_name'])?$emp_data['prefix_name'].". ".$emp_data['userfullname']:$emp_data['userfullname'];
				}
				elseif($column_key == 'date_of_joining')
				{
					$value = isset($emp_data['date_of_joining'])?  sapp_Global::change_date($emp_data['date_of_joining'],"view"):"";
				}
				else
				{
					$value = isset($emp_data[$column_key])?$emp_data[$column_key]:"";
				}
				$value = html_entity_decode($value,ENT_QUOTES,'UTF-8');
				$objPHPExcel->getActiveSheet()->SetCellValue($cell_name, $value);
				$count1++;
			}
			$i++;
		}
			
		sapp_Global::clean_output_buffer();
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header('Cache-Control: max-age=0');
		sapp_Global::clean_output_buffer();
			
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');

		exit;
	}

	public function downloadreportAction(){
		$file_name = $this->_getParam('file_name', NULL);
		if(!empty($file_name)){
			$file = BASE_PATH.'/downloads/reports/'.$this->_getParam('file_name');
			$status = sapp_Global::downloadReport($file);
		}

	}
}