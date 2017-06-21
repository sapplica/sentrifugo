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

class Default_MydetailsController extends Zend_Controller_Action
{

    private $options;
	private $mydetailsobjPrivileges='';
	public function preDispatch()
	{
		
	}
	
    public function init()
    {
		$employeeModel = new Default_Model_Employee();	
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginUserRole = $auth->getStorage()->read()->emprole;
					$loginUserGroup = $auth->getStorage()->read()->group_id;
		}
		$this->mydetailsobjPrivileges = sapp_Global::_checkprivileges(MYDETAILS,$loginUserGroup,$loginUserRole,'edit');
	}
	public function indexAction()
	{	
		$editPrivilege="";
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $loginUserId;	$data = array();$tabName="employee";
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		
		$employeeform = new Default_Form_employee();
		try
		{
			if($id!='' && $id > 0)
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
				$prefix_model = new Default_Model_Prefix();
				$data = $employeeModal->getsingleEmployeeData($id);
				if($data == 'norows')
				{
					$this->view->rowexist = "norows";
					$this->view->empdata = "";
				}
				else if(!empty($data))
				{
                                    
					$this->view->rowexist = "rows";
					$this->view->empdata = $data;
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
					$employeeform->removeElement("submit");
					$data = $data[0]; 
					if($loginUserId == SUPERADMIN)
					{
						//If login user is superAdmin..... role is 'Super Admin'.
						 $employeeform->emprole->addMultiOption('superAdmin','Super Admin');
					}
					else
					{
						$roles_arr = $role_model->getRolesDataByID($data['emprole']); 
						if(sizeof($roles_arr) > 0)
						{ 			                    
							$employeeform->emprole->addMultiOption($roles_arr[0]['id'].'_'.$roles_arr[0]['group_id'],utf8_encode($roles_arr[0]['rolename']));
							$data['emprole']=$roles_arr[0]['rolename'];
							
						}
						else
						{
							$data['emprole']="";
						}
					}
					$prefix_data = array();	
					if($data['prefix_id']!='' && $data['prefix_id']!='null')
						$prefix_data = $prefix_model->getsinglePrefixData($data['prefix_id']);
					if(!empty($prefix_data) && $prefix_data !='norows')
					{
						$prefix_data = $prefix_data[0];
						$employeeform->prefix_id->addMultiOption($prefix_data['id'],$prefix_data['prefix']);
					}
					$referedby_options = $user_model->getRefferedByForUsers();
				
				/* Code for reporting manager dropdown */
				
				$reportingManagerData = $usersModel->getUserDetailsByID($data['reporting_manager']);
				if(!empty($reportingManagerData))
				{
				   $employeeform->reporting_manager->addMultiOption($reportingManagerData[0]['id'],$reportingManagerData[0]['userfullname']);
				}
				
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
				
					$employeeform->populate($data);
					$employeeform->setDefault('user_id',$data['user_id']);
					$employeeform->setDefault('emp_status_id',$data['emp_status_id']);
					$employeeform->setDefault('businessunit_id',$data['businessunit_id']);
					$employeeform->setDefault('jobtitle_id',$data['jobtitle_id']);
					$employeeform->setDefault('department_id',$data['department_id']);
					$employeeform->setDefault('position_id',$data['position_id']);
					if($data['date_of_joining'] !='' && $data['date_of_joining'] !='0000-00-00')
					{
						$date_of_joining = sapp_Global::change_date($data['date_of_joining'],'view');
						$employeeform->date_of_joining->setValue($date_of_joining);
					}
			
					if($data['date_of_leaving'] !='' && $data['date_of_leaving'] !='0000-00-00')
					{
						$date_of_leaving = sapp_Global::change_date($data['date_of_leaving'],'view');
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
					
				    if(isset($data['prefix_id']) && $data['prefix_id'] !='')
					{
					
						$singlePrefixArr = $prefix_model->getsinglePrefixData($data['prefix_id']);
						
						if($singlePrefixArr !='norows')
						{
						 $employeeform->prefix_id->addMultiOption($singlePrefixArr[0]['id'],$singlePrefixArr[0]['prefix']);
					
				        	$data['prefix_id']=$singlePrefixArr[0]['prefix'];
		
						}
						else
						{
							$data['prefix_id']="";
						}
					
					}
					

				   if(!empty($data['businessunit_id']))
				   {
					
						$buname = $busineesUnitModel->getSingleUnitData($data['businessunit_id']);
						
						if(!empty($buname)){
							$data['businessunit_id'] = $buname['unitname'];
						}
						else
						{
							$data['businessunit_id'] = "";
						}
					}
					if(!empty($data['department_id'])) 
					{
						$depname = $deptModel->getSingleDepartmentData($data['department_id']);
						
						if(!empty($depname)){
							$data['department_id'] = $depname['deptname'];
							
						}
						else
						{
							$data['department_id'] ="";
						}
					}
					if(!empty($data['jobtitle_id'])) 
					{
				    	$jobname = $jobtitlesModel->getsingleJobTitleData($data['jobtitle_id']);
				
						if(!empty($jobname) && $jobname!='norows'){
							
							$data['jobtitle_id'] = $jobname[0]['jobtitlename'];
							
						}
						else{
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
						else{
							$data['reporting_manager'] = "";
						}
					}
					
					
					$employeeform->setAttrib('action',BASE_URL.'mydetails/edit/');
					$this->view->id = $id;
					$this->view->form = $employeeform;
					$this->view->employeedata = (!empty($data))?$data:"";
					$this->view->messages = $this->_helper->flashMessenger->getMessages();
					$this->view->empdata = $data;
					$this->view->editPrivilege = $this->mydetailsobjPrivileges;
					$this->view->data = $data;	
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
			$result = $this->save($employeeform,$tabName);	
			$this->view->msgarray = $result; 
		}
	}
	public function personaldetailsviewAction()
	{
	    if(defined('EMPTABCONFIGS'))
		{
		$empOrganizationTabs = explode(",",EMPTABCONFIGS);
			if(in_array('emppersonaldetails',$empOrganizationTabs)){
				$auth = Zend_Auth::getInstance();
				$loginUserId ='';$loginUserGroup ='';$loginUserRole ='';
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
				$objName = 'mydetails';$editPrivilege="";
				$identityDocumentArr = array();
				$documentsArr = array();
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
						{  
							$this->view->rowexist = "rows";
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
								$identitydocumentsModel = new Default_Model_Identitydocuments();	
								$identityDocumentArr = $identitydocumentsModel->getIdentitydocumnetsrecord();
								$data = $empperdetailsModal->getsingleEmpPerDetailsData($id);
							
							  if(!empty($identityDocumentArr))
								$this->view->identitydocument = $identityDocumentArr;
									
							   
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
								        $data[0]['genderid'] ="";
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
								        $data[0]['maritalstatusid'] ="";
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
								         $data[0]['nationalityid'] ="";
						                } 
									}
									
									if(isset($data[0]['ethniccodeid']) && $data[0]['ethniccodeid'] !='')
									{
										$singleethniccodeArr = $ethniccodemodel->getsingleEthnicCodeData($data[0]['ethniccodeid']);
										  if($singleethniccodeArr !='norows')
										  {
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
										  if($singleracecodeArr !='norows')
										  {
											$emppersonaldetailsform->racecodeid->addMultiOption($singleracecodeArr[0]['id'],$singleracecodeArr[0]['racename']);
											$data[0]['racecodeid']=$singleracecodeArr[0]['racename'];
										  }
										   else
					                      {
								           $data[0]['racecodeid'] ="";
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
								           $data[0]['languageid'] ="";
						                  } 
									}
								
										$emppersonaldetailsform->populate($data[0]);
										
										$dob = sapp_Global::change_date($data[0]["dob"], 'view');
										$emppersonaldetailsform->dob->setValue($dob);
										
										if($data[0]['identity_documents'] !='')
										{
											$documentsArr = get_object_vars(json_decode($data[0]['identity_documents']));
											$documentsArr = sapp_Global::object_to_array($documentsArr);
											
										}
										
								}
							
									$this->view->controllername = $objName;
									$this->view->data = $data;
									$this->view->documentsArr = $documentsArr;
									$this->view->id = $id;
									$this->view->form = $emppersonaldetailsform;
									$this->view->employeedata = $empdata[0];
									$this->view->actionname = 'personal';	//Edit action name
									$this->view->editPrivilege = $this->mydetailsobjPrivileges;
							}
							$this->view->empdata = $empdata;				
						}
					}
				}
				catch(Exception $e)
				{
					$this->view->rowexist = "norows";
				}
			}else{
		 	  $this->_redirect('error');
		    }
        }
        else{
		 	$this->_redirect('error');
		}   		
	}
	//Employee Personal Details edit....
	public function personalAction()
	{   
	    if(defined('EMPTABCONFIGS'))
		{
		    $empOrganizationTabs = explode(",",EMPTABCONFIGS);
			if(in_array('emppersonaldetails',$empOrganizationTabs)){
	
				$employeeData =array();$emptyFlag=0;
				$identityDocumentArr = array();
				$documentsArr = array();
				$auth = Zend_Auth::getInstance();
				if($auth->hasIdentity()){
							$loginUserId = $auth->getStorage()->read()->id;
							$loginUserGroup = $auth->getStorage()->read()->group_id;
							$loginUserRole = $auth->getStorage()->read()->emprole;
				}
				$id = $loginUserId;	$tabName="personal";
				$employeeModal = new Default_Model_Employee();
				$empdata = $employeeModal->getsingleEmployeeData($id);
				
				if($empdata == 'norows')
				{
				  $this->view->rowexist = "norows";
				  $this->view->empdata = "";
				}
				else if(!empty($empdata))
				{
					$this->view->rowexist = "rows";
					$usersModel = new Default_Model_Users();
					$emppersonaldetailsform = new Default_Form_emppersonaldetails();
					if($id)
					{
						$empperdetailsModal = new Default_Model_Emppersonaldetails();
						$usersModel = new Default_Model_Users();
						$emppersonaldetailsform = new Default_Form_emppersonaldetails();
						$identitydocumentsModel = new Default_Model_Identitydocuments();	
						$identityDocumentArr = $identitydocumentsModel->getIdentitydocumnetsrecord();
						
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
						}
						else
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
						}
						else
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
						}
						else
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
						else
						{
							$this->view->identitydocument = "";
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
						$this->view->form = $emppersonaldetailsform;
						$this->view->data = $data;
						$this->view->documentsArr = $documentsArr;
						$this->view->id = $id;
						$this->view->msgarray = $msgarray;
						$this->view->employeedata = $empdata[0];
						$this->view->emptyFlag=$emptyFlag;
						$this->view->messages = $this->_helper->flashMessenger->getMessages();
					
						$emppersonaldetailsform->user_id->setValue($id);
						$emppersonaldetailsform->setAttrib('action',BASE_URL.'mydetails/personal');
						
						$this->view->messages = $this->_helper->flashMessenger->getMessages();		
					}
					if($this->getRequest()->getPost())
					{
						$result = $this->savepersonaldetails($emppersonaldetailsform,$loginUserId,$identityDocumentArr);	
						$this->view->msgarray = $result; 
					}
					$this->view->empdata = $empdata; 
				}
			}	
			else{
		 	    $this->_redirect('error');
		    }
        }
		else{
		 	$this->_redirect('error');
		 }			
	}
	
	public function savepersonaldetails($emppersonaldetailsform,$userid,$identityDocumentArr)
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
			$this->_redirect('mydetails/personal');
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
	public function communicationdetailsviewAction()
	{
	    if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);
				if(in_array('empcommunicationdetails',$empOrganizationTabs)){
				$auth = Zend_Auth::getInstance();
				$empdata =array();
				$empDeptdata=array();
				$empDept = '';
				$editPrivilege="";
				$departmentAddress = array();
				if($auth->hasIdentity()){
							$loginUserId = $auth->getStorage()->read()->id;
				} 	
				$id = $this->getRequest()->getParam('userid');
				if($id == '')		$id = $loginUserId;
				$callval = $this->getRequest()->getParam('call');
				if($callval == 'ajaxcall')
					$this->_helper->layout->disableLayout();
					
				$objName = 'mydetails';
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
									$departmentAddress = $usersModel->getOrganizationAddress();	
							
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
									if($data[0]['perm_country'] != '')	
										$empcommdetailsform->setDefault('perm_country',$data[0]['perm_country']);
									if($data[0]['perm_state'] != ''){
										$permstateNameArr = $statesmodel->getStateName($data[0]['perm_state']);
										$empcommdetailsform->setDefault('perm_state',$permstateNameArr[0]['id'].'!@#'.$permstateNameArr[0]['statename']);
									}
									if($data[0]['perm_city'] != ''){
										$permcityNameArr = $citiesmodel->getCityName($data[0]['perm_city']);
										$empcommdetailsform->setDefault('perm_city',$permcityNameArr[0]['id'].'!@#'.$permcityNameArr[0]['cityname']);
									}
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
					         }
							if(!empty($data[0]['perm_state']))
							 {
						        $statename = $statesmodel->getStateName($data[0]['perm_state']);
						          if(!empty($statename))
						          {
							      $data[0]['perm_state'] = $statename[0]['statename'];
						          }
					         }
                            if(!empty($data[0]['perm_city'])) 
                            {
						        $cityname = $citiesmodel->getCityName($data[0]['perm_city']);
						         if(!empty($cityname))
						         {
							     $data[0]['perm_city'] = $cityname[0]['cityname'];
						         }
					        }
							if(!empty($data[0]['current_country']))
							 {
						       $countryname = $countriesModel->getCountryCode($data[0]['current_country']);
						          if(!empty($countryname))
						          {
							      $data[0]['current_country'] = $countryname[0]['country_name'];
						          }
					         }
							if(!empty($data[0]['current_state']))
							 {
						        $statename = $statesmodel->getStateName($data[0]['current_state']);
						          if(!empty($statename))
						          {
							      $data[0]['current_state'] = $statename[0]['statename'];
						          }
					         }
                            if(!empty($data[0]['current_city'])) 
                            {
						        $cityname = $citiesmodel->getCityName($data[0]['current_city']);
						         if(!empty($cityname))
						         {
							     $data[0]['current_city'] = $cityname[0]['cityname'];
						         }
					        }
							$this->view->controllername = $objName;
							$this->view->actionname = 'communication';	//Edit action name
							$this->view->data = $data;
							$this->view->dataArray = (!empty($departmentAddress))?$departmentAddress:array();	
							$this->view->id = $id;
							$this->view->employeedata = $empdata[0];
							$this->view->form = $empcommdetailsform;
							
						}
						$this->view->empdata = $empdata;
						$this->view->editPrivilege = $this->mydetailsobjPrivileges;
					}
				}
				}
				catch(Exception $e)
				{	
					 $this->view->rowexist = "norows";
				}
			}
			else{
				$this->_redirect('error');
			}
		 
		}
		else{
		 	$this->_redirect('error');
		}	
	}
	//Employee Communication Details edit....
	public function communicationAction()
	{	
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);
				if(in_array('empcommunicationdetails',$empOrganizationTabs)){
			$empdata=array();
			$departmentAddress = array();
			$tabName="communication";
			$emptyFlag=0;
			$auth = Zend_Auth::getInstance();
			if($auth->hasIdentity())
			{
				$loginUserId = $auth->getStorage()->read()->id;
				$id=$loginUserId;
				$employeeModal = new Default_Model_Employee();
				$empdata = $employeeModal->getsingleEmployeeData($id);
				if($empdata == 'norows')
				{
					$this->view->rowexist = "norows";
					$this->view->empdata = "";
				}
				else if(!empty($empdata))
				{
					$this->view->rowexist = "rows";
					$empcommdetailsform = new Default_Form_empcommunicationdetails();
					$empcommdetailsModal = new Default_Model_Empcommunicationdetails();
					$usersModel = new Default_Model_Users();
					$countriesModel = new Default_Model_Countries();
					$statesmodel = new Default_Model_States();
					$citiesmodel = new Default_Model_Cities();
					$countrieslistArr = $countriesModel->getTotalCountriesList();
					$msgarray = array();
					//Department address
					$deptId = $empdata[0]['department_id'];
					if($deptId !='' && $deptId !='')
						$departmentAddress = $usersModel->getDepartmentAddress($deptId);
					else
						$departmentAddress = $usersModel->getOrganizationAddress();		
					if(!empty($countrieslistArr))
					{
						$empcommdetailsform->perm_country->addMultiOption('','Select Country');
						$empcommdetailsform->current_country->addMultiOption('','Select Country');
						foreach ($countrieslistArr as $countrieslistres)
						{
							$empcommdetailsform->perm_country->addMultiOption($countrieslistres['id'],utf8_encode($countrieslistres['country_name']) );
							$empcommdetailsform->current_country->addMultiOption($countrieslistres['id'],utf8_encode($countrieslistres['country_name']) );
						}
					}
					else
					{
						$msgarray['perm_country'] = 'Countries are not configured yet.';
						$msgarray['current_country'] = 'Countries are not configured yet.';
						$emptyFlag++;
					}
					//login Employee communication details.....
					$data = $empcommdetailsModal->getsingleEmpCommDetailsData($loginUserId);
					if(!empty($data))
					{
						if($data[0]['perm_country']!=''){  
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
						if($data[0]['perm_state']!=''){
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
						if($data[0]['perm_country']!='')	
							$empcommdetailsform->setDefault('perm_country',$data[0]['perm_country']);
						if($data[0]['perm_state']!=''){
							$permstateNameArr = $statesmodel->getStateName($data[0]['perm_state']);
							$empcommdetailsform->setDefault('perm_state',$permstateNameArr[0]['id'].'!@#'.$permstateNameArr[0]['statename']);							
						}
						if($data[0]['perm_city']!=''){
							$permcityNameArr = $citiesmodel->getCityName($data[0]['perm_city']);
							$empcommdetailsform->setDefault('perm_city',$permcityNameArr[0]['id'].'!@#'.$permcityNameArr[0]['cityname']);							
						}
						
						if($data[0]['current_country'] != '')   
						   $empcommdetailsform->setDefault('current_country',$data[0]['current_country']);
						if($data[0]['current_state'] !='')
						   $empcommdetailsform->setDefault('current_state',$currstateNameArr[0]['id'].'!@#'.$currstateNameArr[0]['statename']);
						if($data[0]['current_city'] != '')   
						   $empcommdetailsform->setDefault('current_city',$currcityNameArr[0]['id'].'!@#'.$currcityNameArr[0]['cityname']);

						$empcommdetailsform->setAttrib('action',BASE_URL.'mydetails/communication');
						$empcommdetailsform->user_id->setValue($loginUserId);
										
						$this->view->data = $data;
						
						
						$this->view->emptyFlag = $emptyFlag;
						$this->view->msgarray = $msgarray;
						$this->view->messages = $this->_helper->flashMessenger->getMessages();	
					}
					$this->view->dataArray = $departmentAddress;
					if($this->getRequest()->getPost())
					{
						$result = $this->save($empcommdetailsform,$tabName);	
						$this->view->msgarray = $result; 
					}
					$this->view->id = $loginUserId;
					$this->view->empdata = $empdata;  
					$this->view->form = $empcommdetailsform;				
				}
				
			}
		}
			else{
				$this->_redirect('error');
			 }
		}
		else{
		 	$this->_redirect('error');
		 } 
		
	}
	
	//Employee Skills edit....(GRID)
	public function skillsAction()
	{
	    if(defined('EMPTABCONFIGS'))
		{
		    $empOrganizationTabs = explode(",",EMPTABCONFIGS);
			if(in_array('emp_skills',$empOrganizationTabs))
			{
				$employeeData =array();$empdata=array();$emptyFlag=0;
				$auth = Zend_Auth::getInstance();
				if($auth->hasIdentity()){
							$loginUserId = $auth->getStorage()->read()->id;
				}
				$id = $loginUserId;	$tabName="skills";$conText='mydetails';
				$call = $this->_getParam('call');
				if($call == 'ajaxcall')
				{	
					$this->_helper->layout->disableLayout();
					$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
				}
				if($id == '')	$id = $userID;
				$Uid = ($id)?$id:$userID;
				$employeeModal = new Default_Model_Employee();
				$empdata = $employeeModal->getsingleEmployeeData($Uid);
				if($empdata == 'norows')
				{
				  $this->view->rowexist = "norows";
					$this->view->empdata="";
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
						$dashboardcall = $this->_getParam('dashboardcall',null);
						$data = array();	$searchQuery = '';$searchArray = array();$tablecontent = '';$levelsArr=array();
					
						if($refresh == 'refresh')
						{
							if($dashboardcall == 'Yes')
									$perPage = DASHBOARD_PERPAGE;
							else	
									$perPage = PERPAGE;
							$searchQuery = '';$searchArray = array();
							$sort = 'DESC';$by = 'e.modifieddate';$perPage = 10;$pageNo = 1;$searchData = '';
						}
						else 
						{
							$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
							$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'e.modifieddate';
							if($dashboardcall == 'Yes')
									$perPage = DASHBOARD_PERPAGE;
							else	
									$perPage = PERPAGE;
									
							$pageNo = $this->_getParam('page', 1);
							$searchData = $this->_getParam('searchData');	
							$searchData = rtrim($searchData,',');			
						}
						$dataTmp = $empskillsModel->getGrid($sort, $by, $perPage, $pageNo,$searchData,$call,$dashboardcall,$Uid,$conText);
						array_push($data,$dataTmp);
						$this->view->dataArray = $data;
						$this->view->call = $call ;
						$this->view->id = $id ;
						$this->view->messages = $this->_helper->flashMessenger->getMessages();
						$this->view->empdata = $empdata;  	
					}
					$this->view->empdata = $empdata; 
				}
			}
			else{
		 	   $this->_redirect('error');
		    }
        }
		else{
		 	$this->_redirect('error');
		 } 			
	}
	//Employee Education details...(GRID)
	public function educationAction()
	{
	    if(defined('EMPTABCONFIGS'))
		{
		    $empOrganizationTabs = explode(",",EMPTABCONFIGS);
			if(in_array('education_details',$empOrganizationTabs))
			{
				$tabName = "education";$employeeData =array();
				$auth = Zend_Auth::getInstance();
				if($auth->hasIdentity()){
							$loginUserId = $auth->getStorage()->read()->id;
				}
				$userid = $loginUserId;
				$call = $this->_getParam('call');
				if($call == 'ajaxcall')
				{	
					$this->_helper->layout->disableLayout();
					$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
				}
				if($userid == '')	$userid = $userID;
				$Uid = ($userid)?$userid:$userID;
				$employeeModal = new Default_Model_Employee();
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
						$educationdetailsModel = new Default_Model_Educationdetails();	
								
						$view = Zend_Layout::getMvcInstance()->getView();		
						$objname = $this->_getParam('objname');
						$refresh = $this->_getParam('refresh');
						$data = array();$searchQuery = '';	$searchArray = array();	$tablecontent = '';
						
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
							'menuName'=>'Education Details','formgrid' => 'true','unitId'=>$Uid,
							'call'=>$call,'context'=>'mydetails',
							'search_filters' => array('from_date' =>array('type'=>'datepicker'),
															'to_date' =>array('type'=>'datepicker'),
																	'educationlevel' => array(
																		'type' => 'select',
																		'filter_data' => array('' => 'All')+$level_opt,
																	),
														)	
						);			
						array_push($data,$dataTmp);
						$this->view->id=$userid;
						$this->view->controllername = $objName;
						$this->view->dataArray = $data;
						
						$this->view->call = $call ;
						$this->view->messages = $this->_helper->flashMessenger->getMessages();
					}
					$this->view->empdata = $empdata;  
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
	//Employee Experience details...(GRID)
	public function experienceAction()
	{
	    if(defined('EMPTABCONFIGS'))
		{
		    $empOrganizationTabs = explode(",",EMPTABCONFIGS);
			if(in_array('experience_details',$empOrganizationTabs))
			{
				$tabName = "experience";
				$employeeData =array();
				$auth = Zend_Auth::getInstance();
				if($auth->hasIdentity()){
							$loginUserId = $auth->getStorage()->read()->id;
				}
				$userid = $loginUserId;
				$call = $this->_getParam('call');
				if($call == 'ajaxcall')
				{	
					$this->_helper->layout->disableLayout();
					$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
				}
				if($userid == '')	$userid = $userID;
				$Uid = ($userid)?$userid:$userID;
				
				$employeeModal = new Default_Model_Employee();
				$empdata = $employeeModal->getsingleEmployeeData($Uid);
				
				if($empdata == 'norows')
				  $this->view->rowexist = "norows";
				else
				{
					$this->view->rowexist = "rows";
					if(!empty($empdata))
					{
						//Check for this user id record exists or not....
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
								echo $searchQuery = rtrim($searchQuery," AND");					
							}
							/** search from grid - END **/
						}
								
						$objName = 'experiencedetails';
						
						$tableFields = array('action'=>'Action','comp_name'=>'Company Name','comp_website'=>'Company Website','designation'=>'Designation','from_date'=>'From','to_date'=>'To');
						
						$tablecontent = $experiencedetailsModel->getexperiencedetailsData($sort, $by, $pageNo, $perPage,$searchQuery,$Uid);    
						
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
							'unitId'=>$Uid,
							'call'=>$call,'context'=>'mydetails',
							'search_filters' => array(
											'from_date' =>array('type'=>'datepicker'),
											'to_date' =>array('type'=>'datepicker')											
											)							);			
						
						array_push($data,$dataTmp);
						
						$this->view->id=$userid;	//User_id sending to view for tabs navigation....
						$this->view->controllername = $objName;
						$this->view->dataArray = $data;
						$this->view->call = $call ;
						$this->view->messages = $this->_helper->flashMessenger->getMessages();
					}
					$this->view->empdata = $empdata;
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
	public function leavesAction()
	{
	    if(defined('EMPTABCONFIGS'))
		{
		    $empOrganizationTabs = explode(",",EMPTABCONFIGS);
			if(in_array('emp_leaves',$empOrganizationTabs))
			{
				$auth = Zend_Auth::getInstance();
				if($auth->hasIdentity()){
							$loginUserId = $auth->getStorage()->read()->id;
							$loginUserRole = $auth->getStorage()->read()->emprole;
							$loginuserGroup = $auth->getStorage()->read()->group_id;
				}
				$id = $loginUserId;
				$call = $this->_getParam('call');
				if($call == 'ajaxcall')
				{
					$this->_helper->layout->disableLayout();
					$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
				}	
				$Uid = ($id)?$id:$userID;
				$employeeleavesModel = new Default_Model_Employeeleaves();
				$leavemanagementModel = new Default_Model_Leavemanagement();	 		
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
						if(!empty($empdata))
						{
							if($Uid)
							{
							   $empleavesform = new Default_Form_empleaves();
							   $employeeleavesModal = new Default_Model_Employeeleaves();
							   $currentdata = '';
							   $leavetransferArr = $leavemanagementModel->getWeekendDetails($empdata[0]['department_id']);
							   $prevyeardata = $employeeleavesModal->getPreviousYearEmployeeleaveData($Uid);
							   $currentyeardata = $employeeleavesModal->getsingleEmployeeleaveData($Uid);
							   if(empty($currentyeardata))
								{
									$currentdata = "empty";
									$currentyearleavecount ='';
								} 
								else
								{
									$currentdata = "notempty";
									$currentyearleavecount = $currentyeardata[0]['emp_leave_limit'];
								}					 
							
							$this->view->currentdata = $currentdata;					
							$leavetransfercount = '';   $previousyear = '';   $isleavetrasnferset = '';
							$data = $employeeleavesModal->getsingleEmployeeleaveData($id);
							$used_leaves = 0;   $date=date('Y');
						   if(!empty($data))
							{
								 $empleavesform->populate($data[0]);
								 $used_leaves=$data[0]['used_leaves'];
							}
							$empleavesform->alloted_year->setValue($date);
							if(!empty($leavetransferArr) && $leavetransferArr[0]['is_leavetransfer'] == 1 && !empty($prevyeardata) && is_numeric($prevyeardata[0]['remainingleaves']) && (int)$prevyeardata[0]['remainingleaves'] > 0 && $prevyeardata[0]['alloted_year'] !='' && empty($currentyeardata))
							{
								 $leavetransfercount = $prevyeardata[0]['remainingleaves'];
								 $previousyear = $prevyeardata[0]['alloted_year'];
								 $isleavetrasnferset = 1;
								 $empleavesform->submitbutton->setAttrib('onClick','return showleavealert('.$leavetransfercount.','.$previousyear.')');
								 $empleavesform->setAttrib('action',BASE_URL.'mydetails/leaves/');
							 
							}
							else
							{
							 $empleavesform->setAttrib('action',BASE_URL.'mydetails/leaves/');
							} 
							$this->view->form = $empleavesform;
							$this->view->data = $data;
							$this->view->id = $Uid;
							$this->view->leavetransfercount = $leavetransfercount;
							
						}
						//Post values....
						if($this->getRequest()->getPost())
						{	
							$result = $this->empaddorremoveleaves($empleavesform,$Uid,$used_leaves,$leavetransfercount,$isleavetrasnferset,$currentyearleavecount);	
							$this->view->msgarray = $result; 
						}  		
						$objname = $this->_getParam('objname');
						$refresh = $this->_getParam('refresh');
						$data = array();$searchQuery = '';	$searchArray = array();	$tablecontent = '';
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
									$searchQuery .= " ".$key." like '%".$val."%' AND ";
									$searchArray[$key] = $val;
								}
								$searchQuery = rtrim($searchQuery," AND");					
							}
							/** search from grid - END **/
						}
								
						$objName = 'empleaves';

						$tableFields = array('action'=>'Action','emp_leave_limit'=>'Allotted Leave Limit','used_leaves'=>'Used Leaves','remainingleaves'=>'Leave Balance','alloted_year'=>'Alloted Year');
						

						$tablecontent = $employeeleavesModel->getEmpLeavesData($sort, $by, $pageNo, $perPage,$searchQuery,$Uid);     
						$dataTmp = array(
							'userid'=>$Uid, 
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
							'menuName'=>'Employee Leaves',
							'formgrid'=>'true','unitId'=>$Uid,
							'call'=>$call,'context'=>'mydetails'
						);			
						array_push($data,$dataTmp);
						$permission = sapp_Global::_checkprivileges(EMPLOYEE,$loginuserGroup,$loginUserRole,'edit');
						$this->view->dataArray = $data;
						$this->view->call = $call ;
						$this->view->id = $Uid;
						$this->view->messages = $this->_helper->flashMessenger->getMessages();
						$this->view->employeedata = $empdata[0];
						$this->view->usergroup = $loginuserGroup;
						$this->view->permission = $permission;
					}
					$this->view->empdata = $empdata; 	
				}
				}
				catch(Exception $e)
				{
					$this->view->rowexist = "norows";
				}
				$this->view->usergroup = $loginuserGroup;
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
	
	//Employee holidays....(GRID)
	public function holidaysAction()
	{
		if(defined('EMPTABCONFIGS'))
		{
		    $empOrganizationTabs = explode(",",EMPTABCONFIGS);
			if(in_array('emp_holidays',$empOrganizationTabs))
			{
				$tabName = "holidays";
				$objName = 'empholidays';
				$employeeData =array();
				$empdata =array();
				$auth = Zend_Auth::getInstance();
				if($auth->hasIdentity()){
							$loginUserId = $auth->getStorage()->read()->id;
				}
				$id = $loginUserId;
				$call = $this->_getParam('call');
				if($call == 'ajaxcall')
				{	
					$this->_helper->layout->disableLayout();
					$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
				}
				if($id == '')	$id = $userID;
				$Uid = ($id)?$id:$userID;  
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
						if(!empty($empdata))
						{
							$holidaydatesmodel = new Default_Model_Holidaydates();		
							$usersModel = new Default_Model_Users();
							$call = $this->_getParam('call');
							$objname = $this->_getParam('objname');
							$refresh = $this->_getParam('refresh');
							$data = array();	$searchQuery = '';	$searchArray = array();		$tablecontent = '';
							
							if($refresh == 'refresh')
							{
								$sort = 'DESC';$by = 'h.modifieddate';$perPage = 10;$pageNo = 1;$searchData = '';
							}
							else 
							{
								$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
								$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'h.modifieddate';
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
										if($key == 'holidaydate')
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
							$employeesModel = new Default_Model_Employees();		
							$empholidaydata = $employeesModel->getHolidayGroupForEmployee($id);
							
							if(isset($empholidaydata[0]) && $empholidaydata[0]['holiday_group'] !='')
							{
								$empGroupId = $empholidaydata[0]['holiday_group'];
								$tableFields = array('action'=>'Action','holidayname' => 'Holiday','holidaydate' => 'Date','description' => 'Description');
								
								$tablecontent = $holidaydatesmodel->getHolidayDatesData($sort, $by, $pageNo, $perPage,$searchQuery,$empGroupId);     
								
								$dataTmp = array(
											'userid'=>$Uid, 
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
											'menuName'=>'Employee Holidays',
											'formgrid'=>'true','unitId'=>$Uid,
											'call'=>$call,'context'=>'mydetails',
											'search_filters' => array(
												'holidaydate' =>array('type'=>'datepicker')					
												)									
										);			
									array_push($data,$dataTmp);
									$this->view->dataArray = $data;
									$this->view->call = $call ;
							}
							if($Uid)	   
						   
							if(!empty($empdata))
									$this->view->empdata = $empdata[0];
								else
									$this->view->empdata = $empdata;		
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
	//Emp salary account  details view....
	public function salarydetailsviewAction()
	{
        if(defined('EMPTABCONFIGS'))
		{
		    $empOrganizationTabs = explode(",",EMPTABCONFIGS);
			if(in_array('emp_salary',$empOrganizationTabs))
			{
				$auth = Zend_Auth::getInstance();
				if($auth->hasIdentity()){
							$loginUserId = $auth->getStorage()->read()->id;
				} 	
				$id = $loginUserId;
				$callval = $this->getRequest()->getParam('call');
				if($callval == 'ajaxcall')
					$this->_helper->layout->disableLayout();
				
				$objName = 'mydetails';
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
								$payfrequencyModal = new Default_Model_Payfrequency();
								$data = $empsalarydetailsModal->getsingleEmpSalaryDetailsData($id);
							
							  if(!empty($data))
								{	

									if(isset($data[0]['currencyid']) && $data[0]['currencyid'] !='')
									{						
										$currencyArr = $currencymodel->getCurrencyDataByID($data[0]['currencyid']);
										if(sizeof($currencyArr)>0)
										{
											$empsalarydetailsform->currencyid->addMultiOption($currencyArr[0]['id'],$currencyArr[0]['currencyname'].' '.$currencyArr[0]['currencycode']);
											$data[0]['currencyid']= $currencyArr[0]['currencyname'];
										}
										 else
					                    {
								         $data[0]['currencyid'] ="";
						                } 
										
									}
									
									if(isset($data[0]['accountclasstypeid']) && $data[0]['accountclasstypeid'] !='')
									{
										$accountclasstypeArr = $accountclasstypemodel->getsingleAccountClassTypeData($data[0]['accountclasstypeid']);
																		
										if(sizeof($accountclasstypeArr)>0 && $accountclasstypeArr!='norows')
										{
												$empsalarydetailsform->accountclasstypeid->addMultiOption($accountclasstypeArr[0]['id'],$accountclasstypeArr[0]['accountclasstype']);
											    $data[0]['accountclasstypeid']=$accountclasstypeArr[0]['accountclasstype'];
										}
									    else
					                    {
								         $data[0]['accountclasstypeid'] ="";
						                }
									}
									
									if(isset($data[0]['bankaccountid']) && $data[0]['bankaccountid'] !='')
									{
										$bankaccounttypeArr = $bankaccounttypemodel->getsingleBankAccountData($data[0]['bankaccountid']);
										if($bankaccounttypeArr !='norows')
										{
											$empsalarydetailsform->bankaccountid->addMultiOption($bankaccounttypeArr[0]['id'],$bankaccounttypeArr[0]['bankaccounttype']);
											  $data[0]['bankaccountid']=$bankaccounttypeArr[0]['bankaccounttype'];
										}
									    else
					                    {
								         $data[0]['bankaccountid'] ="";
						                }
									}
									
									if(isset($data[0]['salarytype']) && $data[0]['salarytype'] !='')
			 						{
					 					$payfreqData = $payfrequencyModal->getActivePayFreqData($data[0]['salarytype']);
										if(sizeof($payfreqData) > 0)
										{
											foreach ($payfreqData as $payfreqres){
												$empsalarydetailsform->salarytype->addMultiOption($payfreqres['id'],$payfreqres['freqtype']);
												 $data[0]['salarytype']=$payfreqres['freqtype'];
											}
											
										}else{
										 $data[0]['salarytype']="";
										}
			 						}
									
									$empsalarydetailsform->populate($data[0]);

									if($data[0]['accountholding'] !='')
									{
										$accountholding = sapp_Global::change_date($data[0]["accountholding"], 'view');
										$empsalarydetailsform->accountholding->setValue($accountholding);
									}
									
								    if(!empty($data[0]['salary'])){
									 if($data[0]['salary'] !='')
									{
									  $data[0]['salary']=sapp_Global:: _decrypt( $data[0]['salary']);
									}
									else
									{
										$data[0]['salary']="";
									}
						        }
									
													
								}
							   
								$this->view->controllername = $objName;
								$this->view->actionname = 'salarydetails';	//Edit action name....
								$this->view->data = $data;
								$this->view->id = $id;
								$this->view->form = $empsalarydetailsform;
								$this->view->employeedata = $empdata[0];
							}
							$this->view->empdata = $empdata;  	
							$this->view->editPrivilege = $this->mydetailsobjPrivileges;  	
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
	//Emp salary account details edit ....
	/*public function salarydetailsAction()
	{	
	    if(defined('EMPTABCONFIGS'))
		{
		    $empOrganizationTabs = explode(",",EMPTABCONFIGS);
			if(in_array('emp_salary',$empOrganizationTabs))
			{
				$auth = Zend_Auth::getInstance();
				$emptyFlag=0;
				$tabName='salarydetails';
				if($auth->hasIdentity()){
							$loginUserId = $auth->getStorage()->read()->id;
				}
				  $id = $loginUserId;
				$callval = $this->getRequest()->getParam('call');
				if($callval == 'ajaxcall')
					$this->_helper->layout->disableLayout();
				
				$empsalarydetailsform = new Default_Form_empsalarydetails();
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
								$payfrequencyModal = new Default_Model_Payfrequency();
								$msgarray = array();
								
								$basecurrencymodeldata = $currencymodel->getCurrencyList();
								if(sizeof($basecurrencymodeldata) > 0)
								{ 			
									   $empsalarydetailsform->currencyid->addMultiOption('','Select Salary Currency');
									foreach ($basecurrencymodeldata as $basecurrencyres){
										$empsalarydetailsform->currencyid->addMultiOption($basecurrencyres['id'],utf8_encode($basecurrencyres['currency']));
									}
								}else
								{
									$msgarray['currencyid'] = 'Salary currencies are not configured yet.';
									$emptyFlag++;
								}
								
								$payfreqData = $payfrequencyModal->getActivePayFreqData();
				 				$empsalarydetailsform->salarytype->addMultiOption('','Select Pay Frequency');
								if(sizeof($payfreqData) > 0)
								{
									foreach ($payfreqData as $payfreqres){
										$empsalarydetailsform->salarytype->addMultiOption($payfreqres['id'],$payfreqres['freqtype']);
									}
						
								}else
								{
									$msgarray['salarytype'] = 'Pay frequency is not configured yet.';
									$emptyFlag++;
						
								}
								
								$bankaccounttypeArr = $bankaccounttypemodel->getBankAccountList();
								if(!empty($bankaccounttypeArr))
								{
									$empsalarydetailsform->bankaccountid->addMultiOption('','Select Bank Account Type');
									foreach ($bankaccounttypeArr as $bankaccounttyperes){
										$empsalarydetailsform->bankaccountid->addMultiOption($bankaccounttyperes['id'],$bankaccounttyperes['bankaccounttype']);
										
									}
								}else
								{
									$msgarray['bankaccountid'] = 'Bank account types are not configured yet.';
									$emptyFlag++;
								}
								
								$accountclasstypeArr = $accountclasstypemodel->getAccountClassTypeList();
								if(!empty($accountclasstypeArr))
								{
									$empsalarydetailsform->accountclasstypeid->addMultiOption('','Select Account Type');
									foreach ($accountclasstypeArr as $accountclasstyperes){
										$empsalarydetailsform->accountclasstypeid->addMultiOption($accountclasstyperes['id'],$accountclasstyperes['accountclasstype']);
										
									}
								}else
								{
									$msgarray['accountclasstypeid'] = 'Account class types are not configured yet.';
									$emptyFlag++;
								}
								
													
								
								$data = $empsalarydetailsModal->getsingleEmpSalaryDetailsData($id);
								if(!empty($data))
								{    
									$empsalarydetailsform->populate($data[0]);	
									if($data[0]['accountholding'] !='')
									{
										$accountholding = sapp_Global::change_date($data[0]["accountholding"], 'view');
										$empsalarydetailsform->accountholding->setValue($accountholding);
									}
									if($data[0]['accountclasstypeid'] !='')
									  $empsalarydetailsform->setDefault('accountclasstypeid',$data[0]['accountclasstypeid']);
									
									$empsalarydetailsform->setDefault('currencyid',$data[0]['currencyid']);
									$empsalarydetailsform->setDefault('bankaccountid',$data[0]['bankaccountid']);
									
									$this->view->data = $data[0];
								}
								$empsalarydetailsform->user_id->setValue($id);
								$empsalarydetailsform->setAttrib('action',BASE_URL.'mydetails/salarydetails');
								
								$this->view->form = $empsalarydetailsform;
								
								$this->view->id = $id;
								$this->view->msgarray = $msgarray;
								$this->view->employeedata = $empdata[0];
								$this->view->emptyFlag=$emptyFlag;
								$this->view->messages = $this->_helper->flashMessenger->getMessages();
							}
							 $this->view->empdata = $empdata; 	
						}
						
					}
				}
				catch(Exception $e)
				{
					   $this->view->rowexist = "norows";
				}
				if($this->getRequest()->getPost())
				{
					$result = $this->save($empsalarydetailsform,$tabName);	
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
	}*/
	//Employee training and certification details....(GRID)
	public function certificationAction()
	{
	    if(defined('EMPTABCONFIGS'))
		{
		    $empOrganizationTabs = explode(",",EMPTABCONFIGS);
			if(in_array('trainingandcertification_details',$empOrganizationTabs))
			{
				$tabName = "certification";
				$employeeData =array();
				$auth = Zend_Auth::getInstance();
				if($auth->hasIdentity()){
							$loginUserId = $auth->getStorage()->read()->id;
				}
				$id = $loginUserId;	
				
				$call = $this->_getParam('call');
				if($call == 'ajaxcall')
				{	
					$this->_helper->layout->disableLayout();
					$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
				}
				if($id == '')	$id = $userID;
				$Uid = ($id)?$id:$userID;
				
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
						$TandCdetailsModel = new Default_Model_Trainingandcertificationdetails();	
						$call = $this->_getParam('call');
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
								'menuName'=>'Employee Certification  Details',
								'formgrid' => 'true','unitId'=>$Uid,
								'call'=>$call,'context'=>'mydetails'
								);			
							
							array_push($data,$dataTmp);
								
							$this->view->id=$Uid;	//User_id sending to view for tabs navigation....
							$this->view->controllername = $objName;
							$this->view->dataArray = $data;
							$this->view->call = $call ;
							$this->view->messages = $this->_helper->flashMessenger->getMessages();
					}
					$this->view->empdata = $empdata; 
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
	//Employee Credit card details view...
	public function creditcarddetailsviewAction()
	{	
	    if(defined('EMPTABCONFIGS'))
		{
		    $empOrganizationTabs = explode(",",EMPTABCONFIGS);
			if(in_array('creditcarddetails',$empOrganizationTabs))
			{
				$tabName = "creditcard";
				$employeeData =array();
				$objName = 'mydetails';
				$editPrivilege='';
				$auth = Zend_Auth::getInstance();
				   if($auth->hasIdentity())
				   {
						$loginUserId = $auth->getStorage()->read()->id;
					}
				$id = $loginUserId;
				$callval = $this->getRequest()->getParam('call');
				if($callval == 'ajaxcall')
					$this->_helper->layout->disableLayout();
				
				$creditcardDetailsform = new Default_Form_Creditcarddetails();
				$creditcardDetailsModel = new Default_Model_Creditcarddetails();
				
				$creditcardDetailsform->removeElement("submit");
				$elements = $creditcardDetailsform->getElements();
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
					$data = $creditcardDetailsModel->getcreditcarddetailsRecord($id);
					$employeeModal = new Default_Model_Employee();
					try
					{
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
								if(!empty($data))
								{
									$creditcardDetailsform->setDefault("id",$data[0]['id']);
									$creditcardDetailsform->setDefault('user_id',$data[0]['user_id']);
									$creditcardDetailsform->setDefault("card_type",$data[0]["card_type"]);
									$creditcardDetailsform->setDefault("card_number",$data[0]["card_number"]);
									$creditcardDetailsform->setDefault("nameoncard",$data[0]["nameoncard"]);
									
									$expiry_date = sapp_Global::change_date($data[0]["card_expiration"], 'view');
									$creditcardDetailsform->setDefault('card_expiration', $expiry_date);
									$creditcardDetailsform->setDefault("card_issuedby",$data[0]["card_issued_comp"]);
									$creditcardDetailsform->setDefault("card_code",$data[0]["card_code"]);
						
								}
								$this->view->controllername = $objName;
								$this->view->actionname = 'creditcard';	//Edit action name
								$this->view->id = $id;
								if(!empty($empdata))
									$this->view->employeedata = $empdata[0];
								else
									$this->view->employeedata = $empdata;
								$this->view->form = $creditcardDetailsform;
								$this->view->data =$data;
							}
							$this->view->empdata =$empdata;
							$this->view->editPrivilege = $this->mydetailsobjPrivileges;
						}
					}
					catch(Exception $e)
					{
						$this->view->rowexist = "norows";
					}
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
	//Employee Credit card details...
	public function creditcardAction()
	{
	    if(defined('EMPTABCONFIGS'))
		{
		    $empOrganizationTabs = explode(",",EMPTABCONFIGS);
			if(in_array('creditcarddetails',$empOrganizationTabs))
			{
				$tabName = "creditcard";
				$employeeData =array();
				
			   $auth = Zend_Auth::getInstance();
			   if($auth->hasIdentity())
			   {
					$loginUserId = $auth->getStorage()->read()->id;
				}
				$id = $loginUserId;
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
						$creditcardDetailsform = new Default_Form_Creditcarddetails();
						$creditcardDetailsModel = new Default_Model_Creditcarddetails();
						if($id)
						{	
							$data = $creditcardDetailsModel->getcreditcarddetailsRecord($id);
							
							if(!empty($data))
							{
								$creditcardDetailsform->setDefault("id",$data[0]["id"]);
								$creditcardDetailsform->setDefault("user_id",$data[0]["user_id"]);
								$creditcardDetailsform->setDefault("card_type",$data[0]["card_type"]);
								$creditcardDetailsform->setDefault("card_number",$data[0]["card_number"]);
								$creditcardDetailsform->setDefault("nameoncard",$data[0]["nameoncard"]);
								
								$expiry_date = sapp_Global::change_date($data[0]["card_expiration"],'view');
								$creditcardDetailsform->setDefault('card_expiration', $expiry_date);
								$creditcardDetailsform->setDefault("card_issuedby",$data[0]["card_issued_comp"]);
								$creditcardDetailsform->setDefault("card_code",$data[0]["card_code"]);
							}
							$creditcardDetailsform->setAttrib('action',BASE_URL.'mydetails/creditcard/');
							$this->view->id=$id;
							$this->view->form = $creditcardDetailsform;
							$this->view->data=$data;
						}
						if($this->getRequest()->getPost())
						{
							$result = $this->save($creditcardDetailsform,$tabName);	
							$this->view->msgarray = $result; 
						}
					}
					$this->view->empdata = $empdata; 
					$this->view->messages = $this->_helper->flashMessenger->getMessages();	
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

	public function visadetailsviewAction()
	{
	 if(defined('EMPTABCONFIGS'))
		{
		    $empOrganizationTabs = explode(",",EMPTABCONFIGS);
			if(in_array('visadetails',$empOrganizationTabs))
			{
				$employeeData =array();$empdata=array();$emptyFlag=0;
				$auth = Zend_Auth::getInstance();
				if($auth->hasIdentity()){
							$loginUserId = $auth->getStorage()->read()->id;
				}
				$id = $loginUserId;	$tabName="visadetails";$conText='mydetails';
				$call = $this->_getParam('call');
				if($call == 'ajaxcall')
				{	
					$this->_helper->layout->disableLayout();
					$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
				}
				if($id == '')	$id = $userID;
				$Uid = ($id)?$id:$userID;
				$employeeModal = new Default_Model_Employee();
				$empdata = $employeeModal->getsingleEmployeeData($Uid);
				if($empdata == 'norows')
				{
				  $this->view->rowexist = "norows";
					$this->view->empdata="";
				}
				else
				{
					$this->view->rowexist = "rows";
					if(!empty($empdata))
					{	
						$visaandimmigrationdetailsModel = new Default_Model_Visaandimmigrationdetails();		
						$view = Zend_Layout::getMvcInstance()->getView();		
						$objname = $this->_getParam('objname');
						$refresh = $this->_getParam('refresh');
						$dashboardcall = $this->_getParam('dashboardcall',null);
						$data = array();	$searchQuery = '';$searchArray = array();$tablecontent = '';$levelsArr=array();
					
						if($refresh == 'refresh')
						{
							if($dashboardcall == 'Yes')
									$perPage = DASHBOARD_PERPAGE;
							else	
									$perPage = PERPAGE;
							$searchQuery = '';$searchArray = array();
							$sort = 'DESC';$by = 'e.modifieddate';$perPage = 10;$pageNo = 1;$searchData = '';
						}
						else 
						{
							$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
							$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'e.modifieddate';
							if($dashboardcall == 'Yes')
									$perPage = DASHBOARD_PERPAGE;
							else	
									$perPage = PERPAGE;
									
							$pageNo = $this->_getParam('page', 1);
							$searchData = $this->_getParam('searchData');	
							$searchData = rtrim($searchData,',');			
						}
						$dataTmp = $visaandimmigrationdetailsModel->getGrid($sort, $by, $perPage, $pageNo,$searchData,$call,$dashboardcall,$Uid,$conText);			
						array_push($data,$dataTmp);
						$this->view->dataArray = $data;
						$this->view->call = $call ;
						$this->view->id = $id ;
						$this->view->messages = $this->_helper->flashMessenger->getMessages();
						$this->view->empdata = $empdata;  	
					}
					$this->view->empdata = $empdata; 
				}
			}
			else{
		 	   $this->_redirect('error');
		    }
        }
		else{
		 	$this->_redirect('error');
		 } 	
	}
	//Employee visa details...
	public function visaAction()
	{
	    if(defined('EMPTABCONFIGS'))
		{
		    $empOrganizationTabs = explode(",",EMPTABCONFIGS);
			if(in_array('visadetails',$empOrganizationTabs))
			{
				$tabName = "visa";
				$employeeData =array();
				$auth = Zend_Auth::getInstance();
			   if($auth->hasIdentity())
			   {
					$loginUserId = $auth->getStorage()->read()->id;
				}
				$id = $loginUserId;
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
						$visaandimmigrationDetailsform = new Default_Form_Visaandimmigrationdetails();
						$visaandimmigrationdetailsModel = new Default_Model_Visaandimmigrationdetails();
						if($id)
						{	
							$data = $visaandimmigrationdetailsModel->getvisadetailsRecord($id);
							
							if(!empty($data))
							{
								$visaandimmigrationDetailsform->setDefault("id",$data[0]["id"]);
								$visaandimmigrationDetailsform->setDefault("user_id",$data[0]["user_id"]);
									
								$visaandimmigrationDetailsform->setDefault("passport_number",$data[0]["passport_number"]);
								
								$pp_issue_date = sapp_Global::change_date($data[0]["passport_issue_date"], 'view');
								$visaandimmigrationDetailsform->setDefault('passport_issue_date', $pp_issue_date);
								
								$pp_expiry_date = sapp_Global::change_date($data[0]["passport_expiry_date"], 'view');
								$visaandimmigrationDetailsform->setDefault("passport_expiry_date",$pp_expiry_date);
								
								$visaandimmigrationDetailsform->setDefault("visa_number",$data[0]["visa_number"]);
								$visaandimmigrationDetailsform->setDefault("visa_type",$data[0]["visa_type"]);
								
								$v_issue_date = sapp_Global::change_date($data[0]["visa_issue_date"], 'view');
								$visaandimmigrationDetailsform->setDefault('visa_issue_date', $v_issue_date);
								
								
								$v_expiry_date = sapp_Global::change_date($data[0]["visa_expiry_date"], 'view');
								$visaandimmigrationDetailsform->setDefault("visa_expiry_date",$v_expiry_date);
								
								$visaandimmigrationDetailsform->setDefault("inine_status",$data[0]["inine_status"]);
								
								$inine_review = sapp_Global::change_date($data[0]["inine_review_date"], 'view');
								$visaandimmigrationDetailsform->setDefault("inine_review_date",$inine_review);
								
								$visaandimmigrationDetailsform->setDefault("issuing_authority",$data[0]["issuing_authority"]);
								$visaandimmigrationDetailsform->setDefault("ininetyfour_status",$data[0]["ininetyfour_status"]);
								
								$ininetyfour_expiry = sapp_Global::change_date($data[0]["ininetyfour_expiry_date"], 'view');
								$visaandimmigrationDetailsform->setDefault("ininetyfour_expiry_date",$ininetyfour_expiry);
							}
							$this->view->id=$id;
							$visaandimmigrationDetailsform->setAttrib('action',BASE_URL.'mydetails/visa');
							$this->view->form = $visaandimmigrationDetailsform;
							$this->view->data=$data;
						}
						if($this->getRequest()->getPost())
						{
							$result = $this->save($visaandimmigrationDetailsform,$tabName);	
							$this->view->msgarray = $result; 
						}
					}
					$this->view->empdata = $empdata;
					$this->view->messages = $this->_helper->flashMessenger->getMessages();	
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
	//Employee medical claims...
	
	public function medicalclaimsAction()
    {  	
	    if(defined('EMPTABCONFIGS'))
		{
		    $empOrganizationTabs = explode(",",EMPTABCONFIGS);
			if(in_array('medical_claims',$empOrganizationTabs))
			{
				$userID="";
				$conText = "";
				$tabName = "medicalclaims";
				$auth = Zend_Auth::getInstance();
				if($auth->hasIdentity()){
							$loginUserId = $auth->getStorage()->read()->id;
							$loginUserRole = $auth->getStorage()->read()->emprole;
							$loginUserGroup = $auth->getStorage()->read()->group_id;
				} 
				$userid = $loginUserId;
				
				$call = $this->_getParam('call');
				if($call == 'ajaxcall')
				{
					$this->_helper->layout->disableLayout();
					$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
					$conText = ($this->_getParam('context') !='')? $this->_getParam('context'):$this->getRequest()->getParam('context');
				}
				if($userid == '') $userid = $userID;
				$Uid = ($userid)?$userid:$userID;//die;
				
				$empMedicalclaimsform = new Default_Form_Medicalclaims();
				$empMedicalclaimsModel = new Default_Model_Medicalclaims();
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
						if(!empty($empdata))
						{
								
								$view = Zend_Layout::getMvcInstance()->getView();		
								$objname = $this->_getParam('objname');
								$refresh = $this->_getParam('refresh');
								$data = array();	$employeeData=array();
								$searchQuery = '';		$searchArray = array();	$tablecontent = '';
								if($refresh == 'refresh')
								{
									$sort = 'DESC';$by = 'm.modifieddate';$perPage = 10;$pageNo = 1;$searchData = '';
								}
								else 
								{
									$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
									$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'m.modifieddate';
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
										
								$objName = 'medicalclaims';

								$tableFields = array('action'=>'Action','injury_type'=>'Medical Claim Type','leaveappliedbyemployee_days'=>'Approved Leaves','leavebyemployeer_days'=>'Employee Applied Leaves','expected_date_join'=> 'Date of Joining');

								$tablecontent = $empMedicalclaimsModel->getempmedicalclaimdetails($sort, $by, $pageNo, $perPage,$searchQuery,$Uid);     
								
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
									'menuName'=>'Medical Claims',
									'formgrid' => 'true',
									'unitId'=>$Uid,
									'call'=>$call,
									'context'=>'mydetails',
									'search_filters' => array(
									'injury_type' => array('type'=>'select',
														'filter_data'=>array(''=>'All',1 => 'Paternity',2 => 'Maternity',
															3=>'Disability',4=>'Injury')
														),
									'expected_date_join'=>array('type'=>'datepicker'))
									
								);			
								array_push($data,$dataTmp);
								$this->view->id=$userid;
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
	//Employee disability details view....
	public function disabilitydetailsviewAction()
	{
	    if(defined('EMPTABCONFIGS'))
		{
		    $empOrganizationTabs = explode(",",EMPTABCONFIGS);
			if(in_array('disabilitydetails',$empOrganizationTabs))
			{
				$tabName = "disability";
				$employeeData =array();
				$objName="mydetails";
				$editPrivilege="";
				$auth = Zend_Auth::getInstance();
			   if($auth->hasIdentity())
			   {
					$loginUserId = $auth->getStorage()->read()->id;
				}
				$id = $loginUserId;
				$empDisabilitydetailsform = new Default_Form_Disabilitydetails();
				$empDisabilitydetailsModel = new Default_Model_Disabilitydetails();
				$employeeModal = new Default_Model_Employee();
				
				$empDisabilitydetailsform->removeElement("submit");
				$elements = $empDisabilitydetailsform->getElements();
				if(count($elements)>0)
				{
					foreach($elements as $key=>$element)
					{
						if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
						$element->setAttrib("disabled", "disabled");
							}
					}
				}
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
						
						if($id)
						{	
							$data = $empDisabilitydetailsModel->getempDisabilitydetails($id);
							if(!empty($data))
							{
								$empDisabilitydetailsform->setDefault("id",$data[0]["id"]);
								$empDisabilitydetailsform->setDefault("disability_name",$data[0]["disability_name"]);
								$empDisabilitydetailsform->setDefault("disability_type",$data[0]["disability_type"]);
								$empDisabilitydetailsform->setDefault("disability_description",$data[0]["disability_description"]);
								$empDisabilitydetailsform->setDefault("other_disability_type",$data[0]["other_disability_type"]);
								
							}
							$empDisabilitydetailsform->setAttrib('action',BASE_URL.'mydetails/disability');
						}
							$this->view->form = $empDisabilitydetailsform;
							$this->view->id = $id;
							$this->view->data=$data;
					}
					$this->view->empdata = $empdata;
					$this->view->controllername=$objName;
					$this->view->actionname='disability';
					$this->view->editPrivilege = $this->mydetailsobjPrivileges;
					$this->view->messages = $this->_helper->flashMessenger->getMessages();	
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
	//Employee disability details....
	public function disabilityAction()
	{
	    if(defined('EMPTABCONFIGS'))
		{
		    $empOrganizationTabs = explode(",",EMPTABCONFIGS);
			if(in_array('disabilitydetails',$empOrganizationTabs))
			{
				$tabName = "disability";
				$objName="mydetails";		
				$employeeData =array();
				$auth = Zend_Auth::getInstance();
			   if($auth->hasIdentity())
			   {
					$loginUserId = $auth->getStorage()->read()->id;
				}
				$id = $loginUserId;
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
						$empDisabilitydetailsform = new Default_Form_Disabilitydetails();
						$empDisabilitydetailsModel = new Default_Model_Disabilitydetails();
						if($id)
						{	
							$data = $empDisabilitydetailsModel->getempDisabilitydetails($id);
							if(!empty($data))
							{
								$empDisabilitydetailsform->setDefault("id",$data[0]["id"]);
								$empDisabilitydetailsform->setDefault("disability_name",$data[0]["disability_name"]);
								$empDisabilitydetailsform->setDefault("disability_type",$data[0]["disability_type"]);
								$empDisabilitydetailsform->setDefault("disability_description",$data[0]["disability_description"]);
								$empDisabilitydetailsform->setDefault("other_disability_type",$data[0]["other_disability_type"]);
								
							}
							$empDisabilitydetailsform->setAttrib('action',BASE_URL.'mydetails/disability');
						}
							$this->view->form = $empDisabilitydetailsform;
							$this->view->id = $id;
							$this->view->data=$data;
						if($this->getRequest()->getPost())
						{
							$result = $this->save($empDisabilitydetailsform,$tabName);	
							$this->view->msgarray = $result; 
						}
					}
					$this->view->empdata = $empdata;
					$this->view->messages = $this->_helper->flashMessenger->getMessages();	
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
	//Employee dependency details...(GRID)
	public function dependencyAction()
	{
		if(defined('EMPTABCONFIGS'))
		{
		    $empOrganizationTabs = explode(",",EMPTABCONFIGS);
			if(in_array('dependency_details',$empOrganizationTabs))
			{
				$tabName = "dependency";
				$employeeData =array();
				$auth = Zend_Auth::getInstance();
				if($auth->hasIdentity())
				{
					$loginUserId = $auth->getStorage()->read()->id;
				}
				$userid = $loginUserId;
				$call = $this->_getParam('call');
				if($call == 'ajaxcall')
				{	
					$this->_helper->layout->disableLayout();
					$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
				}
				if($userid == '')	$userid = $userID;
				$Uid = ($userid)?$userid:$userID;//die;
				
				//Check for this user id record exists or not....
				$employeeModal = new Default_Model_Employee();
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
						$dependencydetailsModel = new Default_Model_Dependencydetails();	
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
									if($key == 'dependent_dob')
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
								
						$objName = 'dependencydetails';
						
					
						
						$tableFields = array('action'=>'Action','dependent_name'=>'Dependent Name','dependent_relation'=>'Dependent Relation','dependent_dob'=>'Dependent DOB');
						

						$tablecontent = $dependencydetailsModel->getdependencydetailsData($sort, $by, $pageNo, $perPage,$searchQuery,$Uid);     
						
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
							'menuName'=>'Dependency Details',
							'formgrid' => 'true','unitId'=>$Uid,
							'call'=>$call,'context'=>'mydetails',
							'search_filters' => array(
											'dependent_dob' =>array('type'=>'datepicker')					
											)	);			
						
						array_push($data,$dataTmp);
						
						$this->view->id=$Uid;	//User_id sending to view for tabs navigation....
						$this->view->controllername = $objName;
						$this->view->dataArray = $data;
						$this->view->call = $call ;
						$this->view->messages = $this->_helper->flashMessenger->getMessages();
					}
					$this->view->empdata = $empdata;
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
	//Employee work eligibility details view ....
	public function workeligibilitydetailsviewAction()
	{
	    if(defined('EMPTABCONFIGS'))
		{
		    $empOrganizationTabs = explode(",",EMPTABCONFIGS);
			if(in_array('workeligibilitydetails',$empOrganizationTabs))
			{ 
				$tabName = "workeligibility";
				$employeeData =array();
				$emptyFlag=0;
				$objName="mydetails";
				$editPrivilege="";
				$issuingauthority = '';
				$auth = Zend_Auth::getInstance();
			   if($auth->hasIdentity())
			   {
					$loginUserId = $auth->getStorage()->read()->id;
				}
				$userid =$loginUserId;
				$workeligibilityform = new Default_Form_Workeligibilitydetails();
				$workeligibilityModel = new Default_Model_Workeligibilitydetails();	
				$employeeModal = new Default_Model_Employee();
				
				$workeligibilityform->removeElement("submit");
				$elements = $workeligibilityform->getElements();
				if(count($elements)>0)
				{
					foreach($elements as $key=>$element)
					{
						if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
						$element->setAttrib("disabled", "disabled");
							}
					}
				}
				$empdata = $employeeModal->getsingleEmployeeData($userid);
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
						
						$workeligibilityDoctypesModal = new Default_Model_Workeligibilitydoctypes();
						$workeligibilityDoctypesData = $workeligibilityDoctypesModal->fetchAll('isactive=1','documenttype');
						$workeligibilityDoctypesDataArr =$workeligibilityDoctypesData->toArray();
						if(!empty($workeligibilityDoctypesDataArr))
						{
							foreach ($workeligibilityDoctypesDataArr as $data)
							{
								$workeligibilityform->documenttype_id->addMultiOption($data['id'],$data['documenttype']);
							}
						}
						else
						{	
							$msgarray['documenttype_id'] = 'Work eligibility document types are not configured yet.';
							$emptyFlag++;
						}
						if($userid)
						{		
							$countriesModel = new Default_Model_Countries();
							$statesmodel = new Default_Model_States();
							$citiesmodel = new Default_Model_Cities();
											
							$countrieslistArr = $countriesModel->getTotalCountriesList();
					
							if(sizeof($countrieslistArr)>0)
							{
								$workeligibilityform->issuingauth_country->addMultiOption('','Select Country');
									
								foreach ($countrieslistArr as $countrieslistres)
								{
									$workeligibilityform->issuingauth_country->addMultiOption($countrieslistres['id'],utf8_encode($countrieslistres['country_name']) );
									
								}
							}
							else
							{
								$msgarray['issuingauth_country'] = 'Countries are not configured yet.';
								$emptyFlag++;
							}	
							$data = $workeligibilityModel->getWorkEligibilityRecord($userid);
							
							if(!empty($data) && isset($data))
							{
								$countryId = $data[0]['issuingauth_country'];
								$stateId = $data[0]['issuingauth_state'];
								$cityId = $data[0]['issuingauth_city'];
								$documenttype_id = $data[0]['documenttype_id'];
								//Populating states & cities Drop down......... from tbl_states & tbl_cities
								if($countryId !='')
								{
									$statelistArr = $statesmodel->getStatesList($countryId);
									if(sizeof($statelistArr)>0)
									{
										$workeligibilityform->issuingauth_state->addMultiOption('','Select State');
										foreach($statelistArr as $statelistres)	
										 {
											$workeligibilityform->issuingauth_state->addMultiOption($statelistres['id'].'!@#'.$statelistres['state_name'],$statelistres['state_name']); 
										 }   						 
									}
								}
								
								if($stateId !='')
								{	
									$citylistArr = $citiesmodel->getCitiesList($stateId);
									if(sizeof($citylistArr)>0)
									{
										$workeligibilityform->issuingauth_city->addMultiOption('','Select City');
										foreach($citylistArr as $cityPermlistres)	
										 {
											$workeligibilityform->issuingauth_city->addMultiOption($cityPermlistres['id'].'!@#'.$cityPermlistres['city_name'],$cityPermlistres['city_name']); 
										 }   						 
									}
									
									$stateNameArr = $statesmodel->getStateName($stateId);
								}
									
								if($cityId !='')
									$cityNameArr = $citiesmodel->getCityName($cityId);
								
								if($documenttype_id !='')
									$issuingauthorityArr = $workeligibilityDoctypesModal->getIssuingAuthority($documenttype_id);
								if(!empty($issuingauthorityArr))
								{
								   $issuingauthority = $issuingauthorityArr[0]['issuingauthority'];
								   $workeligibilityform->issuingauthflag->setValue($issuingauthority);
								}		
								
								$workeligibilityform->setDefault("id",$data[0]["id"]);
								$workeligibilityform->setDefault("user_id",$data[0]["user_id"]);
								$workeligibilityform->setDefault('issuingauth_country',$data[0]['issuingauth_country']);
								if(!empty($stateNameArr))
								 $workeligibilityform->setDefault('issuingauth_state',$stateNameArr[0]['id'].'!@#'.$stateNameArr[0]['statename']);
								if(!empty($cityNameArr)) 
								  $workeligibilityform->setDefault('issuingauth_city',$cityNameArr[0]['id'].'!@#'.$cityNameArr[0]['cityname']);
								$workeligibilityform->setDefault("documenttype_id",$data[0]["documenttype_id"]);
								$workeligibilityform->setDefault("issuingauth_name",$data[0]["issuingauth_name"]);
								$workeligibilityform->setDefault("issuingauth_postalcode",$data[0]["issuingauth_postalcode"]);
																
								
								$issue_date = sapp_Global::change_date($data[0]["doc_issue_date"],'view');
								$workeligibilityform->setDefault('doc_issue_date', $issue_date);
								
								
								$expiry_date = sapp_Global::change_date($data[0]["doc_expiry_date"],'view');
								$workeligibilityform->setDefault('doc_expiry_date', $expiry_date);
								
								$workeligibilityform->setAttrib('action',BASE_URL.'mydetails/workeligibility');
								if(!empty($data[0]['issuingauth_country']))
								{
								  $countryname = $countriesModel->getCountryCode($data[0]['issuingauth_country']);
								   if(!empty($countryname))
								   {
									$data[0]['issuingauth_country'] = $countryname[0]['country_name'];
									}
									else
									{
										$data[0]['issuingauth_country'] ="";
									}
								}
								if(!empty($data[0]['issuingauth_state']))
								{
								  $statename = $statesmodel->getStateName($data[0]['issuingauth_state']);
								  if(!empty($statename)){
									   $data[0]['issuingauth_state'] = $statename[0]['statename'];
									}
									else
									{
										$data[0]['issuingauth_state'] = "";
									}
								}
								if(!empty($data[0]['documenttype_id']))
								{
									$docname = $workeligibilityDoctypesModal->getsingleWorkEligibilityDocTypeData($data[0]['documenttype_id']);	
									if(!empty($docname)){
										$data[0]['documenttype_id'] = $docname[0]['documenttype'];
									}
									else
									{
										$data[0]['documenttype_id'] = "";
									}
								}
				        
								if(!empty($data[0]['issuingauth_city']))
								{
									$cityname = $citiesmodel->getCityName($data[0]['issuingauth_city']);
									if(!empty($cityname)){
										$data[0]['issuingauth_city'] = $cityname[0]['cityname'];
									}
									else
									{
										$data[0]['issuingauth_city'] = "";
									}
								}
								$this->view->data=$data[0];
							}
							
							$this->view->id=$userid;
							
							$this->view->form = $workeligibilityform;
							$this->view->issuingauthority= $issuingauthority;
						}
					}
				
					$this->view->empdata = $empdata; 
					$this->view->emptyFlag= $emptyFlag;
					$this->view->controllername=$objName;
					$this->view->actionname='workeligibility';
					$this->view->editPrivilege = $this->mydetailsobjPrivileges;
					$this->view->messages = $this->_helper->flashMessenger->getMessages();	
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
	//Employee work eligibility details....
	public function workeligibilityAction()
	{
	    if(defined('EMPTABCONFIGS'))
		{
		    $empOrganizationTabs = explode(",",EMPTABCONFIGS);
			if(in_array('workeligibilitydetails',$empOrganizationTabs))
			{
				$tabName = "workeligibility";
				$employeeData =array();
				$msgarray = array();
				$emptyFlag=0;
				$issuingauthority = '';
				$auth = Zend_Auth::getInstance();
			   if($auth->hasIdentity())
			   {
					$loginUserId = $auth->getStorage()->read()->id;
				}
				$userid =$loginUserId;
				$employeeModal = new Default_Model_Employee();
				$empdata = $employeeModal->getsingleEmployeeData($userid);
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
						$workeligibilityform = new Default_Form_Workeligibilitydetails();
						$workeligibilityModel = new Default_Model_Workeligibilitydetails();	
						$workeligibilityDoctypesModal = new Default_Model_Workeligibilitydoctypes();
						//fetchAll($where = null, $order = null, $count = null, $offset = null) function syntax...
						$workeligibilityDoctypesData = $workeligibilityDoctypesModal->fetchAll('isactive=1','documenttype');
						$workeligibilityDoctypesDataArr =$workeligibilityDoctypesData->toArray();
						if(!empty($workeligibilityDoctypesDataArr))
						{
							foreach ($workeligibilityDoctypesDataArr as $data)
							{
								$workeligibilityform->documenttype_id->addMultiOption($data['id'],$data['documenttype']);
							}
						}
						else
						{	
							$msgarray['documenttype_id'] = 'Work eligibility document types are not configured yet.';
							$emptyFlag++;
						}
						if($userid)
						{		
							$countriesModel = new Default_Model_Countries();
							$statesmodel = new Default_Model_States();
							$citiesmodel = new Default_Model_Cities();
											
							$countrieslistArr = $countriesModel->getTotalCountriesList();
					
							if(sizeof($countrieslistArr)>0)
							{
								$workeligibilityform->issuingauth_country->addMultiOption('','Select Country');
									
								foreach ($countrieslistArr as $countrieslistres)
								{
									$workeligibilityform->issuingauth_country->addMultiOption($countrieslistres['id'],utf8_encode($countrieslistres['country_name']) );
								}
							}
							else
							{
								$msgarray['issuingauth_country'] = 'Countries are not configured yet.';
								$emptyFlag++;
							}	
							$data = $workeligibilityModel->getWorkEligibilityRecord($userid);
							
							if(!empty($data) && isset($data))
							{
								$countryId = $data[0]['issuingauth_country'];
								$stateId = $data[0]['issuingauth_state'];
								$cityId = $data[0]['issuingauth_city'];
								$documenttype_id = $data[0]['documenttype_id'];
								//Populating states & cities Drop down......... from tbl_states & tbl_cities
								if($countryId !='')
								{
									$statelistArr = $statesmodel->getStatesList($countryId);
									if(sizeof($statelistArr)>0)
									{
										$workeligibilityform->issuingauth_state->addMultiOption('','Select State');
										foreach($statelistArr as $statelistres)	
										 {
											$workeligibilityform->issuingauth_state->addMultiOption($statelistres['id'].'!@#'.$statelistres['state_name'],$statelistres['state_name']); 
										 }   						 
									}
								}
								
                                if($stateId !='')
								{		
									$citylistArr = $citiesmodel->getCitiesList($stateId);
									if(sizeof($citylistArr)>0)
									{
										$workeligibilityform->issuingauth_city->addMultiOption('','Select City');
										foreach($citylistArr as $cityPermlistres)	
										 {
											$workeligibilityform->issuingauth_city->addMultiOption($cityPermlistres['id'].'!@#'.$cityPermlistres['city_name'],$cityPermlistres['city_name']); 
										 }   						 
									}
									
									$stateNameArr = $statesmodel->getStateName($stateId);
								}
									
								if($cityId !='')
									$cityNameArr = $citiesmodel->getCityName($cityId);
								
								if($documenttype_id !='')
								   $issuingauthorityArr = $workeligibilityDoctypesModal->getIssuingAuthority($documenttype_id);
								if(!empty($issuingauthorityArr))
								{
								   $issuingauthority = $issuingauthorityArr[0]['issuingauthority'];
								   $workeligibilityform->issuingauthflag->setValue($issuingauthority);
								}   
								
								$workeligibilityform->setDefault("id",$data[0]["id"]);
								$workeligibilityform->setDefault("user_id",$data[0]["user_id"]);
								$workeligibilityform->setDefault('issuingauth_country',$data[0]['issuingauth_country']);
								if(!empty($stateNameArr))
								 $workeligibilityform->setDefault('issuingauth_state',$stateNameArr[0]['id'].'!@#'.$stateNameArr[0]['statename']);
								if(!empty($cityNameArr)) 
								 $workeligibilityform->setDefault('issuingauth_city',$cityNameArr[0]['id'].'!@#'.$cityNameArr[0]['cityname']);
								$workeligibilityform->setDefault("documenttype_id",$data[0]["documenttype_id"]);
								$workeligibilityform->setDefault("issuingauth_name",$data[0]["issuingauth_name"]);
								$workeligibilityform->setDefault("issuingauth_postalcode",$data[0]["issuingauth_postalcode"]);
																
								
								$issue_date = sapp_Global::change_date($data[0]["doc_issue_date"],'view');
								$workeligibilityform->setDefault('doc_issue_date', $issue_date);
								
								
								$expiry_date = sapp_Global::change_date($data[0]["doc_expiry_date"],'view');
								$workeligibilityform->setDefault('doc_expiry_date', $expiry_date);
							}
							$workeligibilityform->setAttrib('action',BASE_URL.'mydetails/workeligibility');
							
							$this->view->id=$userid;
							$this->view->data=$data;
							$this->view->form = $workeligibilityform;
							$this->view->issuingauthority= $issuingauthority;
							$this->view->msgarray = $msgarray;
							
						}
						if($this->getRequest()->getPost())
						{
						    $documenttype_id = $this->_request->getParam('documenttype_id');
							if($documenttype_id !='')
							 $issuingauthorityArr = $workeligibilityDoctypesModal->getIssuingAuthority($documenttype_id);
							if(!empty($issuingauthorityArr))
							{
							  $issuingauthority = $issuingauthorityArr[0]['issuingauthority'];
								if($issuingauthority == 1){
									$workeligibilityform->issuingauth_country->setRequired(true)->addErrorMessage('Please select country.');
									$workeligibilityform->issuingauth_country->addValidator('NotEmpty', false, array('messages' => 'Please select country.'));
								 }
								else if($issuingauthority == 2)
								{
								$workeligibilityform->issuingauth_country->setRequired(true)->addErrorMessage('Please select country.');
								$workeligibilityform->issuingauth_country->addValidator('NotEmpty', false, array('messages' => 'Please select country.'));
								
								$workeligibilityform->issuingauth_state->setRequired(true)->addErrorMessage('Please select state.');
								$workeligibilityform->issuingauth_state->addValidator('NotEmpty', false, array('messages' => 'Please select state.'));
								}else if($issuingauthority == 3)
								{
								$workeligibilityform->issuingauth_country->setRequired(true)->addErrorMessage('Please select country.');
								$workeligibilityform->issuingauth_country->addValidator('NotEmpty', false, array('messages' => 'Please select country.'));
								
								$workeligibilityform->issuingauth_state->setRequired(true)->addErrorMessage('Please select state.');
								$workeligibilityform->issuingauth_state->addValidator('NotEmpty', false, array('messages' => 'Please select state.'));
									
									$workeligibilityform->issuingauth_city->setRequired(true)->addErrorMessage('Please select city.');
									$workeligibilityform->issuingauth_city->addValidator('NotEmpty', false, array('messages' => 'Please select city.')); 
								}
							}
							$result = $this->save($workeligibilityform,$tabName);
							$result['issuingauthorityflag'] = $_POST['issuingauthflag'];
							$this->view->msgarray = $result; 
						}
					}
					$this->view->empdata = $empdata; 
					$this->view->emptyFlag= $emptyFlag;
					$this->view->messages = $this->_helper->flashMessenger->getMessages();	
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
		 	$id = $loginUserId;	
			$tabName="skills";
			$conText='mydetails';
		 	
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
	
	public function jobhistoryAction()
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
				$id = $loginUserId;
				$conText ='mydetails';
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
	//This function is to save the form details to database tables.
	public function save($employeeDetailsform,$tabName)
	{	
		$data=array();
		$employeeData =array();
		$actionflag = '';	
		$tableid  = ''; 
		$date = new Zend_Date();
		$msgStr="";
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		} 
		$id =$loginUserId;
		if($employeeDetailsform->isValid($this->_request->getPost()))
		{
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
			 //Taking id(PK) from Form....
			$id = $this->getRequest()->getParam('id');
			$user_id = $loginUserId;
			
			if($id!='')
			{
				$where = array('id=?'=>$id);  
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
		    switch($tabName)
			{	
				case 'employee':	//Employee Details....
						
				break;
				case 'personal':	//Employee personal details...
						$empperdetailsModal = new Default_Model_Emppersonaldetails();
						$id = $this->_request->getParam('id'); 
						$user_id = $loginUserId;
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
						
						$passport = $this->_request->getParam('passport');
						$pancard_number = $this->_request->getParam('pancard_number');
						$SSN_number = $this->_request->getParam('SSN_number');
						$adhar_number = $this->_request->getParam('adhar_number');
						$drivinglicence_number = $this->_request->getParam('drivinglicence_number');
						$otheridentity = $this->_request->getParam('otheridentity');
				
						$bloodgroup = $this->_request->getParam('bloodgroup');
						
						  $data = array('user_id'=>$user_id,
				                 'genderid'=>$genderid,
								 'maritalstatusid'=>$maritalstatusid,
                                 'nationalityid'=>$nationalityid,
                                 'ethniccodeid'=>$ethniccodeid,
                                 'racecodeid'=>$racecodeid,
                                 'languageid'=>$languageid,    								 
				      			 'dob'=>$dob,
								 //'celebrated_dob'=>($celebrated_dob!=''?$celebrated_dob:NULL),
								 'passport'=>($passport!=''?$passport:NULL),
								 'pancard_number'=>($pancard_number!=''?$pancard_number:NULL),
								 'adhar_number'=>($adhar_number!=''?$adhar_number:NULL),
                                 'SSN_number'=>($SSN_number!=''?$SSN_number:NULL), 
                                 'drivinglicence_number'=>($drivinglicence_number!=''?$drivinglicence_number:NULL), 								 
								 'otheridentity'=>($otheridentity!=''?$otheridentity:NULL),
				      			 'bloodgroup'=>($bloodgroup!=''?$bloodgroup:NULL),
								 'modifiedby'=>$loginUserId,
								 'modifieddate'=>gmdate("Y-m-d H:i:s")
						);
						$Id = $empperdetailsModal->SaveorUpdateEmpPersonalData($data, $where);
				
				break;
				case 'communication':
						$empcommdetailsModal = new Default_Model_Empcommunicationdetails();
						
						$personalemail = $this->_request->getParam('personalemail');
						$perm_streetaddress = $this->_request->getParam('perm_streetaddress');
						$perm_country = $this->_request->getParam('perm_country');
						
						$perm_stateArr = explode("!@#",$this->_request->getParam('perm_state'));
						$perm_state = $perm_stateArr[0]; 
						
						$perm_cityArr = explode("!@#",$this->_request->getParam('perm_city'));
						$perm_city = $perm_cityArr[0];
						
						$perm_pincode = $this->_request->getParam('perm_pincode');
						$address_flag = $this->_request->getParam('address_flag');
						$current_streetaddress = $this->_request->getParam('current_streetaddress');
						$current_country = $this->_request->getParam('current_country');
						
						$current_stateArr = explode("!@#",$this->_request->getParam('current_state'));
						$current_state = $current_stateArr[0];
						
						$current_cityArr = explode("!@#",$this->_request->getParam('current_city'));
						$current_city = $current_cityArr[0]; 
						
						$current_pincode = $this->_request->getParam('current_pincode');
						$emergency_number = $this->_request->getParam('emergency_number');
						$emergency_name = $this->_request->getParam('emergency_name');
						$emergency_email = $this->_request->getParam('emergency_email');
						
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
						$Id = $empcommdetailsModal->SaveorUpdateEmpcommData($data, $where);
				break;
				
				case 'skills':	//Employee Skills....
						$empskillsModal = new Default_Model_Empskills();
						$skillname = $this->_request->getParam('skillname');
						$yearsofexp = $this->_request->getParam('yearsofexp');
						$competencylevelid = $this->_request->getParam('competencylevelid');
						$year_skill_last_used = $this->_request->getParam('year_skill_last_used',null);
						$year_skill_last_used =sapp_Global::change_date($year_skill_last_used,'database');	
						
											
						$data = array('user_id'=>$user_id,
									 'skillname'=>$skillname,
									 'yearsofexp'=>$yearsofexp,
									 'competencylevelid'=>$competencylevelid, 								 
									 'year_skill_last_used'=>($year_skill_last_used!=''?$year_skill_last_used:NUll),
									 'modifiedby'=>$loginUserId,
									 //'modifieddate'=>$date->get('yyyy-MM-dd HH:mm:ss')
									 'modifieddate'=>gmdate("Y-m-d H:i:s")
								);
						$Id = $empskillsModal->SaveorUpdateEmpSkills($data, $where);
				break;
				
				case 'education':	//Employee education details
						$educationDetailsModel = new Default_Model_Educationdetails();
					
						$institution_name = $this->_request->getParam('institution_name');
						$course = $this->_request->getParam('course');
						$from_date = $this->_request->getParam('from_date');
						$to_date = $this->_request->getParam('to_date');
						$percentage = $this->_request->getParam('percentage');
						
						 //Date Formats....
						$fromDate =sapp_Global::change_date($from_date,'database');	
						$toDate = sapp_Global::change_date($to_date,'database');	
						
						$data = array('user_id'=>$user_id,
				                 'institution_name'=>$institution_name,
								 'course'=>$course,
                                 'from_date'=>$fromDate, 								 
				      			 'to_date'=>$toDate,
								 'percentage'=>$percentage,
								 'modifiedby'=>$loginUserId,
								 //'modifieddate'=>$date->get('yyyy-MM-dd HH:mm:ss')
								 'modifieddate'=>gmdate("Y-m-d H:i:s")
						);
					$Id = $educationDetailsModel->SaveorUpdateEducationDetails($data, $where);
				break;
				
				case 'experience':	//Employee experience details...
						$experienceDetailsModel = new Default_Model_Experiencedetails();
	  
						$comp_name = $this->_request->getParam('comp_name');
						$comp_website = $this->_request->getParam('comp_website');
						$designation = $this->_request->getParam('designation');
						
						$reference_name = $this->_request->getParam('reference_name');
						$reference_contact = $this->_request->getParam('reference_contact');
						$reference_email = $this->_request->getParam('reference_email');
						
						$fromdate = $this->_request->getParam('from_date',null);
						$fromDateStr = sapp_Global::change_date($fromdate,'database');
						
						$todate = $this->_request->getParam('to_date',null);
						$toDateStr = sapp_Global::change_date($todate,'database');
					
						$reason_for_leaving = $this->_request->getParam('reason_for_leaving');
																			
						$data = array(  'comp_name'=>$comp_name,
										'comp_website'=>$comp_website,
										'designation'=>$designation,
										'from_date'=>$fromDateStr,
										'to_date'=>$toDateStr,
										'reference_name'=>$reference_name,
										'reference_contact'=>$reference_contact,
										'reference_email'=>$reference_email,
										'reason_for_leaving'=>$reason_for_leaving,
										'user_id'=>$user_id,
										'modifiedby'=>$loginUserId,
										'modifieddate'=>gmdate("Y-m-d H:i:s")
									);
										
						
						$Id = $experienceDetailsModel->SaveorUpdateEmployeeexperienceData($data,$where);
				break;
				
				case 'leaves':	//Employee Leaves...
						$employeeleavesModel = new Default_Model_Employeeleaves();
						
						$emp_leave_limit = $this->_request->getParam('leave_limit');
										
						$data = array('user_id'=>$user_id,
									'emp_leave_limit'=>$emp_leave_limit,
									'used_leaves'=>$used_leaves,
									'alloted_year'=>$date->get('yyyy'),
									'modifiedby'=>$loginUserId,
									'modifieddate'=>$date->get('yyyy-MM-dd HH:mm:ss')
								);
						$Id = $employeeleavesModel->SaveorUpdateEmpLeaves($data, $where);
				break;
				
				case 'certification':	//Employee training and certification details....
								$TandCdetailsModel = new Default_Model_Trainingandcertificationdetails();	

								$course_name = $this->_request->getParam('course_name');
								$description = $this->_request->getParam('description');
								$course_level = $this->_request->getParam('course_level');
								
								$course_offered_by = $this->_request->getParam('course_offered_by');
								$certification_name = $this->_request->getParam('certification_name');
												
								$issueddate = $this->_request->getParam('issued_date',null);
								
								$issuedDateStr = sapp_Global::change_date($issueddate,'database');
																						
								$data = array(  'course_name'=>$course_name,
												'description'=>$description,
												'course_level'=>$course_level,
												'course_offered_by'=>$course_offered_by,
												'certification_name'=>$certification_name,
												'issued_date'=>$issuedDateStr,
												'user_id'=>$user_id,
												'modifiedby'=>$loginUserId,
												'modifieddate'=>gmdate("Y-m-d H:i:s")
											);
								 $Id = $TandCdetailsModel->SaveorUpdateEmployeeTandCData($data,$where);
				break;
				
				case 'medicalclaims':	//Employee Medical claims....
							//echo "in medical claims case";die;
							$empMedicalclaimsModel = new Default_Model_Medicalclaims();
						   	//Post values .. 
							$injuryIndicator = $this->_request->getParam('injuryindicator');
							$type = $this->_request->getParam('type');
							$injured_date =$this->_request->getParam('injured_date',null);
							$description =$this->_request->getParam('description');
							$injury_name=$this->_request->getParam('injury_name');
							$injury_severity=$this->_request->getParam('injury_severity');
							$disability_type=$this->_request->getParam('disability_type');
							$other_disability_type=$this->_request->getParam('other_disability_type');
							$insurer_name=$this->_request->getParam('insurer_name');
							$expected_date_join=$this->_request->getParam('expected_date_join',null);
							$leavebyemp_from_date=$this->_request->getParam('leavebyemp_from_date',null);
							$leavebyemp_to_date=$this->_request->getParam('leavebyemp_to_date',null);
							$leavebyemp_days=$this->_request->getParam('leavebyemp_days');
							$empleave_from_date=$this->_request->getParam('empleave_from_date',null);
							$empleave_to_date=$this->_request->getParam('empleave_to_date',null);
							$empleave_days=$this->_request->getParam('empleave_days');
							$hospital_name=$this->_request->getParam('hospital_name');
							$hospital_addr=$this->_request->getParam('hospital_addr');
							$room_num=$this->_request->getParam('room_num');
							$gp_name=$this->_request->getParam('gp_name');
							$treatment_details=$this->_request->getParam('treatment_details');
							$total_cost=$this->_request->getParam('total_cost');
							$amount_claimed=$this->_request->getParam('amount_claimed');
							$amount_approved=$this->_request->getParam('amount_approved');
							//Date Formats....
							$empleaveToDate="";$leavebyEmpFromDate="";$leavebyEmpToDate='';$empleaveFromDate='';
							$injuredDate =sapp_Global::change_date($injured_date, 'database');		
							$expectedDateOfJoin = sapp_Global::change_date($expected_date_join, 'database');		
							
							if($leavebyemp_from_date != "")
							{
								$leavebyEmpFromDate = sapp_Global::change_date($leavebyemp_from_date, 'database');		
							}
							if($leavebyemp_to_date != "")
							{
								$leavebyEmpToDate = sapp_Global::change_date($leavebyemp_to_date, 'database');		
							}
							if($empleave_from_date != "")
							{
								$empleaveFromDate = sapp_Global::change_date($empleave_from_date, 'database');		
							}
							if($empleave_to_date != "")
							{
								$empleaveToDate = sapp_Global::change_date($empleave_to_date, 'database');		
							}
							$data = array(  'amount_approved'=>$amount_approved,
											'amount_claimed_for'=>$amount_claimed,
											'treatment_details'=>$treatment_details,
											'total_cost'=>$total_cost,
											'concerned_physician_name'=>$gp_name,
											'room_number'=>$room_num,
											'hospital_address'=>$hospital_addr,
											'hospital_name'=>$hospital_name,
											'leaveappliedbyemployee_days'=>$empleave_days,
											'leaveappliedbyemployee_to_date'=>$empleaveToDate,
											'leaveappliedbyemployee_from_date'=>$empleaveFromDate,
											'leavebyemployeer_days'=>$leavebyemp_days,
											'leavebyemployeer_to_date'=>$leavebyEmpToDate,
											'leavebyemployeer_from_date'=>$leavebyEmpFromDate,
											'expected_date_join'=>$expectedDateOfJoin,
											'medical_insurer_name'=>$insurer_name,
											'other_disability_type'=>$other_disability_type,
											'disability_type'=>$disability_type,
											'injury_severity'=>$injury_severity,
											'injury_name'=>$injury_name,
											'injury_description'=>$description,
											'injured_date'=>$injuredDate,
											'injury_type'=>$type,
											'injury_indicator'=>$injuryIndicator,
											'user_id'=>$user_id,
											'modifiedby'=>$loginUserId,
											'modifieddate'=>gmdate("Y-m-d H:i:s")
										);
							
							$Id = $empMedicalclaimsModel->SaveorUpdateEmpmedicalclaimsDetails($data, $where);
				break;
				
				case 'disability':	//Employee disability details....
						$empDisabilitydetailsModel = new Default_Model_Disabilitydetails();
						//Post values .. 
						$disability_type = $this->_request->getParam('disability_type');
						$disabiity_name =$this->_request->getParam('disability_name');
						$description =$this->_request->getParam('disability_description');
						$other_disability_type=$this->_request->getParam('other_disability_type');
						
						$data = array(  'other_disability_type'=>$other_disability_type,
										'disability_type'=>$disability_type,
										'disability_name'=>$disabiity_name,
										'disability_description'=>$description,
										'user_id'=>$loginUserId,
										'modifiedby'=>$loginUserId,
										'modifieddate'=>gmdate("Y-m-d H:i:s")
									);
						
				       $Id = $empDisabilitydetailsModel->SaveorUpdateEmpdisabilityDetails($data, $where);
				break;
				
				case 'dependency':	//Employee dependency details....
						$dependencyDetailsModel = new Default_Model_Dependencydetails();	
						$dependent_name = $this->_request->getParam('dependent_name');
						$dependent_relation = $this->_request->getParam('dependent_relation');
						$dependent_custody = $this->_request->getParam('dependent_custody');
						$dependentdob = $this->_request->getParam('dependent_dob',null);
						$dependentDOB = sapp_Global::change_date($dependentdob, 'database');
						$dependent_age = $this->_request->getParam('dependent_age');
																				
						$data = array(  'dependent_name'=>$dependent_name,
										'dependent_relation'=>$dependent_relation,
										'dependent_custody'=>$dependent_custody,
										'dependent_dob'=>$dependentDOB,
										'dependent_age'=>$dependent_age,
										'user_id'=>$user_id,
										'modifiedby'=>$loginUserId,
										'modifieddate'=>gmdate("Y-m-d H:i:s")
									);
					$Id = $dependencyDetailsModel->SaveorUpdateEmployeedependencyData($data,$where);
				break;
				
				case 'visa':	//Employee visa and immigration details.....
							$visaandimmigrationdetailsModel = new Default_Model_Visaandimmigrationdetails();
              				
							$passport_num = $this->_request->getParam('passport_number');
							$passport_issue_date = $this->_request->getParam('passport_issue_date',null);
							$passport_expiry_date = $this->_request->getParam('passport_expiry_date',null);
							$visa_number = $this->_request->getParam('visa_number');
							$visa_type = $this->_request->getParam('visa_type');
							$visa_issue_date = $this->_request->getParam('visa_issue_date',null);
							$visa_expiry_date = $this->_request->getParam('visa_expiry_date',null);
							$inine_status = $this->_request->getParam('inine_status');
							$inine_review_date = $this->_request->getParam('inine_review_date',null);
							$issuing_authority = $this->_request->getParam('issuing_authority');
							$ininetyfour_status = $this->_request->getParam('ininetyfour_status');
							$ininetyfour_expiry_date = $this->_request->getParam('ininetyfour_expiry_date',null);
							
							// Date Formats....
							$passport_issue = sapp_Global::change_date($passport_issue_date, 'database');
							
							$passport_expiry = sapp_Global::change_date($passport_expiry_date, 'database');
							
							$visa_issue = sapp_Global::change_date($visa_issue_date, 'database');
							
							$visa_expiry = sapp_Global::change_date($visa_expiry_date, 'database');
							
							$inine_review = sapp_Global::change_date($inine_review_date, 'database');
							
							$ininetyfour_expiry = sapp_Global::change_date($ininetyfour_expiry_date, 'database');
							
							$data = array(  'passport_number'=>$passport_num,
											'passport_issue_date'=>$passport_issue,
											'passport_expiry_date'=>$passport_expiry,
											'visa_number'=>$visa_number,
											'visa_type'=>$visa_type,
											'visa_issue_date'=>$visa_issue,
											'visa_expiry_date'=>$visa_expiry,
											'inine_status'=>$inine_status,
											'inine_review_date'=>$inine_review,
											'issuing_authority'=>$issuing_authority,
											'ininetyfour_status'=>$ininetyfour_status,
											'ininetyfour_expiry_date'=>$ininetyfour_expiry,
											'user_id'=>$user_id,
											'modifiedby'=>$loginUserId,
											'modifieddate'=>gmdate("Y-m-d H:i:s")
										);
					       $Id = $visaandimmigrationdetailsModel->SaveorUpdatevisaandimmigrationDetails($data,$where);
				break;
				
				case 'creditcard':	//Employee credit card details....
						$creditcardDetailsModel = new Default_Model_Creditcarddetails();	
					 	$card_type = $this->_request->getParam('card_type');
						$card_number = $this->_request->getParam('card_number');
						$card_name = $this->_request->getParam('nameoncard');
						$card_expiry_1 = $this->_request->getParam('card_expiration',null);
						
						$card_expiry = sapp_Global::change_date($card_expiry_1, 'database');
						$card_issuedBy = $this->_request->getParam('card_issuedby');
						$card_code = $this->_request->getParam('card_code');
																		 
						$data = array(  'card_type'=>$card_type,
										'card_number'=>$card_number,
										'nameoncard'=>$card_name,
										'card_expiration'=>$card_expiry,
										'card_issued_comp'=>$card_issuedBy,
										'card_code'=>$card_code,
										'user_id'=>$user_id,
										'modifiedby'=>$loginUserId,
										'modifieddate'=>gmdate("Y-m-d H:i:s")
									);
				       $Id = $creditcardDetailsModel->SaveorUpdateCreditcardDetails($data, $where);
				break;
				
				case 'workeligibility':	//Employee work eligibility details....
						$workeligibilityModel = new Default_Model_Workeligibilitydetails();	
               			$documenttype_id = $this->_request->getParam('documenttype_id');
						$doc_issue_date = $this->_request->getParam('doc_issue_date');
						$doc_expiry_date = $this->_request->getParam('doc_expiry_date');
						$issuingauth_name = $this->_request->getParam('issuingauth_name');
						$issuingauth_country = $this->_request->getParam('issuingauth_country');
						$issuingauth_state = $this->_request->getParam('issuingauth_state');
						$issuingauth_city = $this->_request->getParam('issuingauth_city');
						$issuingauth_postalcode = $this->_request->getParam('issuingauth_postalcode');
						
						$docexpiryDate = sapp_Global::change_date($doc_expiry_date, 'database');
						
						$docissueDate = sapp_Global::change_date($doc_issue_date, 'database');
						
						$stateArr = explode("!@#",$issuingauth_state);
						$stateStr = $stateArr[0];
						$cityArr = explode("!@#",$issuingauth_city);
						$cityStr = $cityArr[0];
						
						$data = array(  'documenttype_id'=>$documenttype_id,
										'doc_issue_date'=>$docissueDate,
										'doc_expiry_date'=>$docexpiryDate,
										'issuingauth_name'=>$issuingauth_name,
										'issuingauth_country'=>$issuingauth_country,
										'issuingauth_state'=>$stateStr,
										'issuingauth_city'=>$cityStr,
										'issuingauth_postalcode'=>$issuingauth_postalcode,
										'user_id'=>$user_id,
										'modifiedby'=>$loginUserId,
										'modifieddate'=>gmdate("Y-m-d H:i:s")
									);
						$Id = $workeligibilityModel->SaveorUpdateWorkEligibilityDetails($data, $where);
				break;
				
				case 'additionaldetails':	//Employee additional details ....
						$empadditionaldetailsModal = new Default_Model_Empadditionaldetails();
						$id = $this->_request->getParam('id'); 
						$military_status = $this->_request->getParam('military_status');
						$countries_served = $this->_request->getParam('countries_served');
						$branch_service = trim($this->_request->getParam('branch_service'));
						$rank_achieved = trim($this->_request->getParam('rank_achieved'));
						$special_training = trim($this->_request->getParam('special_training'));
						$awards = trim($this->_request->getParam('awards'));
				
						$from_date = $this->_request->getParam('from_date'); 				
						$from_date = sapp_Global::change_date($from_date, 'database');
				
						$to_date = $this->_request->getParam('to_date'); 				
						$to_date = sapp_Global::change_date($to_date, 'database');
				
						$discharge_status = $this->_request->getParam('discharge_status');
						$service_number = trim($this->_request->getParam('service_number'));
						$rank = trim($this->_request->getParam('rank'));
						$verification_report = trim($this->_request->getParam('verification_report'));
						$military_servicetype = $this->_request->getParam('military_servicetype');
						$veteran_status = $this->_request->getParam('veteran_status');
				
						$data = array('user_id'=>$loginUserId,
				                 'military_status'=>$military_status,
								 'countries_served'=>$countries_served,
                                 'branch_service'=>($branch_service!=''?$branch_service:NULL),
                                 'rank_achieved'=>($rank_achieved!=''?$rank_achieved:NULL),
                                 'special_training'=>($special_training!=''?$special_training:NULL),
                                 'awards'=>($awards!=''?$awards:NULL),    								 
				      			 'from_date'=>($from_date!=''?$from_date:NULL),
								 'to_date'=>($to_date!=''?$to_date:NULL),
								 'discharge_status'=>$discharge_status,
								 'service_number'=>($service_number!=''?$service_number:NULL),
                                 'rank'=>($rank!=''?$rank:NULL), 
                                 'verification_report'=>($verification_report!=''?$verification_report:NULL), 								 
				      			 'military_servicetype'=> $military_servicetype,
								 'veteran_status'=> $veteran_status,
								 'modifiedby'=>$loginUserId,
								 'modifieddate'=>gmdate("Y-m-d H:i:s")
						);
				    
					$Id = $empadditionaldetailsModal->SaveorUpdateEmpAdditionalData($data, $where);
				break;
				
				case 'salarydetails':	//Employee salary account details.....
							$empsalarydetailsModal = new Default_Model_Empsalarydetails();
							$id = $this->_request->getParam('id'); 
							$user_id = $loginUserId;
							$currencyid = $this->_request->getParam('currencyid');
							$salarytype = $this->_request->getParam('salarytype');
							$salary = $this->_request->getParam('salary');
							$bankname = trim($this->_request->getParam('bankname'));
							$accountholder_name = trim($this->_request->getParam('accountholder_name'));
							$accountclasstypeid = $this->_request->getParam('accountclasstypeid');
							$bankaccountid = $this->_request->getParam('bankaccountid');
							$accountnumber = trim($this->_request->getParam('accountnumber'));
							
							$accountholding = $this->_request->getParam('accountholding'); 				
							$accountholding = sapp_Global::change_date($accountholding, 'database');
							$data = array('user_id'=>$user_id,
				                 'currencyid'=>$currencyid,
								 'salarytype'=>$salarytype,
								 'salary'=>$salary,
                                 'bankname'=>($bankname!=''?$bankname:NULL),
                                 'accountholder_name'=>($accountholder_name!=''?$accountholder_name:NULL),
                                 'accountclasstypeid'=>($accountclasstypeid!=''?$accountclasstypeid:NULL),
                                 'bankaccountid'=>($bankaccountid!=''?$bankaccountid:NULL),    								 
				      			 'accountnumber'=>($accountnumber!=''?$accountnumber:NULL),
								 'accountholding'=>($accountholding!=''?$accountholding:NULL),
								 'modifiedby'=>$loginUserId,
								 'modifieddate'=>gmdate("Y-m-d H:i:s"),
								 'isactive'=>1
						);
				    $Id = $empsalarydetailsModal->SaveorUpdateEmpSalaryData($data, $where);
				break;
			}	//switch case end...
			if($Id == 'update')
			{	
			   $tableid = $id;
			     if($tabName == "skills" || $tabName == "leaves" || $tabName == "holidays" || $tabName == "medicalclaims")
					$msgStr = "Employee ".$tabName." updated successfully.";
				else if($tabName == "employee")
						$msgStr = "Employee details updated successfully.";
				else if($tabName == "salarydetails")
						$msgStr = "Employee salary details updated successfully.";
				else if($tabName == "creditcard")
						$msgStr = "Employee corporate card details updated successfully.";						
				else
					$msgStr = "Employee ".$tabName." details updated successfully.";
				
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>$msgStr));
			}   
			else
			{
				$tableid = $Id; 	
				if($tabName == "skills" || $tabName == "leaves" || $tabName == "holidays" || $tabName == "medicalclaims")
					$msgStr = "Employee ".$tabName." added successfully.";
				else if($tabName == "employee")
						$msgStr = "Employee details added successfully.";
				else if($tabName == "salarydetails")
						$msgStr = "Employee salary details added successfully.";
				else if($tabName == "creditcard")
						$msgStr = "Employee corporate card details added successfully.";							
				else
					$msgStr = "Employee ".$tabName." details added successfully.";
			
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>$msgStr));
			}   
			$menuID = EMPLOYEE;
			$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$user_id);
         }else
         {
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("error"=>FIELDMSG));
         }	
			if($tabName == "employee")	
				$this->_redirect('mydetails/edit');	
			else
				$this->_redirect('mydetails/'.$tabName);	
		}
		else
			{
     			$messages = $employeeDetailsform->getMessages();
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
	public function deleteAction()
	{
		
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
				$loginUserId = $auth->getStorage()->read()->id;
			}
		 $id = $this->_request->getParam('objid');
		
		 $messages['message'] = '';$messages['msgtype'] = '';
		 $actionflag = 3;
		    if($id)
			{
	    	   $educationDetailsModel = new Default_Model_Educationdetails();
			  $data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
			  $where = array('id=?'=>$id);
			  $Id = $educationDetailsModel->SaveorUpdateEducationDetails($data, $where);
			    if($Id == 'update')
				{
				   $menuID = EMPLOYEE;
				   $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id); 
				   $messages['message'] = 'Employee Education details deleted successfully';
				    $messages['msgtype'] = 'success';
				}   
				else
				{
                   $messages['message'] = 'Employee Education details  cannot be deleted';	
					$messages['msgtype'] = 'error';				   
				}
			}
			else
			{ 
				 $messages['message'] = 'Employee Education details cannot be deleted';
				 $messages['msgtype'] = 'error';	
			}
			$this->_helper->json($messages);
	}
	
   /*	Medical claims validations */
	public function medicalclaimsvalidations()
	{	$msgarray =array();$errFlag=0;	$fieldValues=array();$totalArr = array();
		if($this->getRequest()->getPost())
		{	
			//Default yes...
			
			$type = $this->_request->getParam('type');
			$fieldValues['type']=($type != '')?$type:4;	//By default value is injury......
			
			$injured_date =$this->_request->getParam('injured_date',null);
			$fieldValues['injured_date']=($injured_date != '')?date(DATEFORMAT_PHP, strtotime($injured_date)):'';
			
			$description =$this->_request->getParam('description');
			$fieldValues['description']=($description != '')?$description:'';
			
			$injury_name=$this->_request->getParam('injury_name');
			$fieldValues['injury_name']=($injury_name != '')?$injury_name:'';
			
			$injury_severity=$this->_request->getParam('injury_severity');
			$fieldValues['injury_severity']=($injury_severity != '')?$injury_severity:'';
			
			$disability_type=$this->_request->getParam('disability_type');
			$fieldValues['disability_type']=($disability_type != '')?$disability_type:'';
			
			$other_disability_type=$this->_request->getParam('other_disability_type');
			$fieldValues['other_disability_type']=($other_disability_type != '')?$other_disability_type:'';
			
			$insurer_name=$this->_request->getParam('insurer_name');
			$fieldValues['insurer_name']=($insurer_name != '')?$insurer_name:'';
			
			$expected_date_join=$this->_request->getParam('expected_date_join',null);
			$fieldValues['expected_date_join']=($expected_date_join != '')?date(DATEFORMAT_PHP, strtotime($expected_date_join)):'';
			
			$leavebyemp_from_date=$this->_request->getParam('leavebyemp_from_date',null);
			$fieldValues['leavebyemp_from_date']=($leavebyemp_from_date != '')?date(DATEFORMAT_PHP, strtotime($leavebyemp_from_date)):'';
			
			$leavebyemp_to_date=$this->_request->getParam('leavebyemp_to_date',null);
			$fieldValues['leavebyemp_to_date']=($leavebyemp_to_date != '')?date(DATEFORMAT_PHP, strtotime($leavebyemp_to_date)):'';
			
			$leavebyemp_days=$this->_request->getParam('leavebyemp_days');
			$fieldValues['leavebyemp_days']=($leavebyemp_days != '')?$leavebyemp_days:'';
			
			$empleave_from_date=$this->_request->getParam('empleave_from_date',null);
			$fieldValues['empleave_from_date']=($empleave_from_date != '')?date(DATEFORMAT_PHP, strtotime($empleave_from_date)):'';
			
			$empleave_to_date=$this->_request->getParam('empleave_to_date',null);
			$fieldValues['empleave_to_date']=($empleave_to_date != '')?date(DATEFORMAT_PHP, strtotime($empleave_to_date)):'';
			
			$empleave_days=$this->_request->getParam('empleave_days');
			$fieldValues['empleave_days']=($empleave_days != '')?$empleave_days:'';
			
			$hospital_name=$this->_request->getParam('hospital_name');
			$fieldValues['hospital_name']=($hospital_name != '')?$hospital_name:'';
			
			$hospital_addr=$this->_request->getParam('hospital_addr');
			$fieldValues['hospital_addr']=($hospital_addr != '')?$hospital_addr:'';
			
			$room_num=$this->_request->getParam('room_num');
			$fieldValues['room_num']=($room_num != '')?$room_num:'';
			
			$gp_name=$this->_request->getParam('gp_name');
			$fieldValues['gp_name']=($gp_name != '')?$gp_name:'';
			
			$treatment_details=$this->_request->getParam('treatment_details');
			$fieldValues['treatment_details']=($treatment_details != '')?$treatment_details:'';
			
			$total_cost=$this->_request->getParam('total_cost');
			$fieldValues['total_cost']=($total_cost != '')?$total_cost:'';
			
			$amount_claimed=$this->_request->getParam('amount_claimed');
			$fieldValues['amount_claimed']=($amount_claimed != '')?$amount_claimed:'';
			
			$amount_approved=$this->_request->getParam('amount_approved');
			$fieldValues['amount_approved']=($amount_approved != '')?$amount_approved:'';
			
			
			if($type != "")
			{	
				$msgarray['type'] = $type;	$errFlag=0;
				switch($type)
				{
					case 1:	//Paternity
							if($injured_date == "")
							{
								//Error
								$msgarray['injured_date'] = 'Please select a date.';
								$errFlag++;
							}
							if($insurer_name == "")
							{
								$msgarray['insurer_name'] = "Please enter the insurer name.";
								$errFlag++;
							}
							if($expected_date_join == "")
							{
								$msgarray['expected_date_join'] = 'Please select a date.';
								$errFlag++;
							}
							if($empleave_from_date =="")
							{
								$msgarray['empleave_from_date'] = 'Please select a date.';
								$errFlag++;
							}
							if($empleave_to_date == "")
							{
								$msgarray['empleave_to_date'] = 'Please select a date.';
								$errFlag++;
							}
							if($leavebyemp_from_date == "")
							{
								$msgarray['leavebyemp_from_date'] = 'Please select a date.';
								$errFlag++;
							}
							if($leavebyemp_to_date == "")
							{
								$msgarray['leavebyemp_to_date'] = 'Please select a date.';
							}
							
							if($hospital_name == "")
							{
								$msgarray['hospital_name'] = 'Please enter the hospital name.';
								$errFlag++;
							}
							if($hospital_addr=="")
							{
								$msgarray['hospital_addr'] = 'Please enter the hospital address.';
								$errFlag++;
							}
							if($room_num=="")
							{
								$msgarray['room_num'] = 'Please enter the room/ward number.';
								$errFlag++;
							}
							if($gp_name=="")
							{
								$msgarray['gp_name'] = 'Please enter the concerned physician name.';
								$errFlag++;
							}
							if($treatment_details == "")
							{
								$msgarray['treatment_details'] = 'Please enter the treatment details.';
								$errFlag++;
							}
							if($total_cost == "")
							{
								$msgarray['total_cost'] = 'Please enter the total cost.';		
								$errFlag++;
							}
							if($amount_claimed== "")
							{
								$msgarray['amount_claimed'] = 'Please enter the amount claimed for.';
								$errFlag++;								
							}
							if($amount_approved== "")
							{
								$msgarray['amount_approved'] = 'Please enter the amount approved.';
								$errFlag++;
							}
							if($amount_claimed !="" && $total_cost != "")
							{
								if($amount_claimed > $total_cost)
								{
									$msgarray['amount_claimed'] = 'Amount claimed should not be greater than total cost.';$errFlag++;
								}
							}
							if($amount_claimed !="" && $amount_approved != "")
							{
								if($amount_approved > $amount_claimed)
								{
									$msgarray['amount_approved'] = 'Amount approved should not be greater than amount claimed.';$errFlag++;
								}
							}
							
					break;	
					case 2:	//Maternity
							if($injured_date == "")
							{
								//Error
								$msgarray['injured_date'] = 'Please select a date.';$errFlag++;
							}
							if($insurer_name == "")
							{
								$msgarray['insurer_name'] = "Please enter the insurer name.";$errFlag++;
							}
							if($expected_date_join == "")
							{
								$msgarray['expected_date_join'] = 'Please select a date.';$errFlag++;
							}
							if($empleave_from_date =="")
							{
								$msgarray['empleave_from_date'] = 'Please select a date.';$errFlag++;
							}
							if($empleave_to_date == "")
							{
								$msgarray['empleave_to_date'] = 'Please select a date.';$errFlag++;
							}
							if($leavebyemp_from_date == "")
							{
								$msgarray['leavebyemp_from_date'] = 'Please select a date.';$errFlag++;
							}
							if($leavebyemp_to_date == "")
							{
								$msgarray['leavebyemp_to_date'] = 'Please select a date.';$errFlag++;
							}
							
							if($hospital_name == "")
							{
								$msgarray['hospital_name'] = 'Please enter the hospital name.';$errFlag++;
							}
							if($hospital_addr=="")
							{
								$msgarray['hospital_addr'] = 'Please enter the hospital address.';$errFlag++;
							}
							if($room_num=="")
							{
								$msgarray['room_num'] = 'Please enter the room/ward number.';$errFlag++;
							}
							if($gp_name=="")
							{
								$msgarray['gp_name'] = 'Please enter the concerned physician name.';$errFlag++;
							}
							if($treatment_details == "")
							{
								$msgarray['treatment_details'] = 'Please enter the treatment details.';$errFlag++;
							}
							if($total_cost == "")
							{
								$msgarray['total_cost'] = 'Please enter the total cost.';		$errFlag++;
							}
							if($amount_claimed== "")
							{
								$msgarray['amount_claimed'] = 'Please enter the amount claimed for.';	$errFlag++;
							}
							if($amount_approved== "")
							{
								$msgarray['amount_approved'] = 'Please enter the amount approved.';$errFlag++;
							}
							if($amount_claimed !="" && $total_cost != "")
							{
								if($amount_claimed > $total_cost)
								{
									$msgarray['amount_claimed'] = 'Amount claimed should not be greater than total cost.';$errFlag++;
								}
							}
							if($amount_claimed !="" && $amount_approved != "")
							{
								if($amount_approved > $amount_claimed)
								{
									$msgarray['amount_approved'] = 'Amount approved should not be greater than amount claimed.';$errFlag++;
								}
							}
							
					break;	
					case 3:	//Disability
							if($injured_date == "")
							{
								//Error
								$msgarray['injured_date'] = 'Please select a date.';$errFlag++;
							}
							if($injury_name == "")
							{
								//Error
								$msgarray['injury_name'] = 'Please enter the disability.';$errFlag++;
							}
							if($insurer_name == "")
							{
								$msgarray['insurer_name'] = "Please enter the insurer name.";$errFlag++;
							}
							if($expected_date_join == "")
							{
								$msgarray['expected_date_join'] = 'Please select a date.';$errFlag++;
							}
							if($leavebyemp_from_date == "")
							{
								$msgarray['leavebyemp_from_date'] = 'Please select a date.';$errFlag++;
							}
							if($leavebyemp_to_date == "")
							{
								$msgarray['leavebyemp_to_date'] = 'Please select a date.';$errFlag++;
							}
							if($disability_type == "")
							{
								$msgarray['disability_type'] = 'Please select a disability type.';$errFlag++;
							}
							if($disability_type!= "" && $disability_type == "other impairments" && $other_disability_type == "")
							{
								$msgarray['other_disability_type'] = 'Please enter any other disability type.';$errFlag++;
							}
							if($amount_claimed== "")
							{
								$msgarray['amount_claimed'] = 'Please enter the amount claimed for.';	$errFlag++;	
							}
							if($amount_approved== "")
							{
								$msgarray['amount_approved'] = 'Please enter the amount approved.';$errFlag++;
							}
							if($amount_claimed !="" && $amount_approved != "")
							{
								if($amount_approved > $amount_claimed)
								{
									$msgarray['amount_approved'] = 'Amount approved should not be greater than amount claimed.';$errFlag++;
								}
							}
							
					break;	
					case 4:		//Injury
							if($injured_date == "")
							{
								//Error
								$msgarray['injured_date'] = 'Please select a date.';$errFlag++;
							}
							if($injury_name == "")
							{
								//Error
								$msgarray['injury_name'] = 'Please enter the injury.';$errFlag++;
							}
							if($injury_severity == "")
							{
								//Error
								$msgarray['injury_severity'] = 'Please select an injury severity.';$errFlag++;
							}
							if($insurer_name == "")
							{
								$msgarray['insurer_name'] = "Please enter the medical insurer name.";$errFlag++;
							}
							if($expected_date_join == "")
							{
								$msgarray['expected_date_join'] = 'Please select a date.';$errFlag++;
							}
							if($empleave_from_date =="")
							{
								$msgarray['empleave_from_date'] = 'Please select a date.';$errFlag++;
							}
							if($empleave_to_date == "")
							{
								$msgarray['empleave_to_date'] = 'Please select a date.';$errFlag++;
							}
							if($leavebyemp_from_date == "")
							{
								$msgarray['leavebyemp_from_date'] = 'Please select a date.';$errFlag++;
							}
							if($leavebyemp_to_date == "")
							{
								$msgarray['leavebyemp_to_date'] = 'Please select a date.';$errFlag++;
							}
							
							if($hospital_name == "")
							{
								$msgarray['hospital_name'] = 'Please enter the hospital name.';$errFlag++;
							}
							if($hospital_addr=="")
							{
								$msgarray['hospital_addr'] = 'Please enter the hospital address.';$errFlag++;
							}
							if($room_num=="")
							{
								$msgarray['room_num'] = 'Please enter the room/ward number.';$errFlag++;
							}
							if($gp_name=="")
							{
								$msgarray['gp_name'] = 'Please enter the concerned physician name.';$errFlag++;
							}
							if($treatment_details == "")
							{
								$msgarray['treatment_details'] = 'Please enter the treatment details.';$errFlag++;
							}
							if($total_cost == "")
							{
								$msgarray['total_cost'] = 'Please enter the total cost.';	$errFlag++;	
							}
							if($amount_claimed== "")
							{
								$msgarray['amount_claimed'] = 'Please enter the amount claimed for.';	$errFlag++;	
							}
							if($amount_approved== "")
							{
								$msgarray['amount_approved'] = 'Please enter the amount approved.';$errFlag++;
							}
							if($amount_claimed !="" && $total_cost != "")
							{
								if($amount_claimed > $total_cost)
								{
									$msgarray['amount_claimed'] = 'Amount claimed should not be greater than total cost.';$errFlag++;
								}
							}
							if($amount_claimed !="" && $amount_approved != "")
							{
								if($amount_approved > $amount_claimed)
								{
									$msgarray['amount_approved'] = 'Amount approved should not be greater than amount claimed.';$errFlag++;
								}
							}
					break;
				}
			}
			if($errFlag == 0)	$msgarray ='';
			$totalArr['msgarray'] = $msgarray;
			$totalArr['fieldValues'] = $fieldValues;
			return $totalArr;
		}	
	}
	public function empaddorremoveleaves($empleavesform,$userid,$used_leaves,$leavetransfercount,$isleavetrasnferset,$currentyearleavecount)
	{
		
	  $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		} 
		if($empleavesform->isValid($this->_request->getPost()))
		{
			$employeeleavesModel = new Default_Model_Employeeleaves();
			$id = $this->_request->getParam('id'); 	//Id hidden field in form....
			$user_id = $userid;
			$emp_leave_limit = $this->_request->getParam('leave_limit');
			if($leavetransfercount !='' && $currentyearleavecount =='')
			 $emp_leave_limit = ($emp_leave_limit + $leavetransfercount);
			else
			 $emp_leave_limit = ($emp_leave_limit + $currentyearleavecount);
			 
			$isleavetrasnfer = 0; 
			if($isleavetrasnferset == 1)	   $isleavetrasnfer = 1;				
				
			$date = new Zend_Date();
			$actionflag = '';	$tableid  = ''; 
			
			$Id = $employeeleavesModel->SaveorUpdateEmployeeLeaves($user_id, $emp_leave_limit,$isleavetrasnfer,$loginUserId);
			 if($id)
			   {
				 $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Employee Leave details updated successfully."));
				 $actionflag = 2;
				 $tableid = $id;
			   }
			   else
			   {
				  $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Employee Leave details added successfully.")); 					   
				  $actionflag = 1;
				  $tableid = $Id;	
			   }
				$menuID = EMPLOYEE;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
				$this->_redirect('mydetails/leaves/');
    			   
			}
			else
			{
     			$messages = $empleavesform->getMessages();
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
	
	public function updateempdetails($employeeform)
	{	
		$emproleStr='';$roleArr=array();
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		} 
		$usersModel = new Default_Model_Usermanagement();
		$employeeModal = new Default_Model_Employee();            
		$businessunit_id = $this->_request->getParam('businessunit_id',null);
		$department_id = $this->_request->getParam('department_id',null);
		$reporting_manager = $this->_request->getParam('reporting_manager',null);
		$jobtitle_id = $this->_request->getParam('jobtitle_id',null);
		$position_id = $this->_request->getParam('position_id',null);
		$user_id = $this->_getParam('user_id',null);
		$prefix_id = $this->_getParam('prefix_id',null);
		$extension_number = $this->_getParam('extension_number',null); 			
		if($employeeform->isValid($this->_request->getPost()))
		{                				
			$id = $this->_request->getParam('id'); 
			$emp_status_id = $this->_request->getParam('emp_status_id',null);
			$date_of_joining = $this->_request->getParam('date_of_joining',null);                                 
			$date_of_joining = sapp_Global::change_date($date_of_joining,'database');
			$date_of_leaving = $this->_request->getParam('date_of_leaving',null);                 
			$date_of_leaving = sapp_Global::change_date($date_of_leaving,'database');                
			$years_exp = $this->_request->getParam('years_exp');
			//FOR USER table
			$employeeId = $this->_getParam('employeeId',null);
			$modeofentry = $this->_getParam('modeofentry',null);
			$hid_modeofentry = $this->_getParam('hid_modeofentry',null);
			$other_modeofentry = $this->_getParam('other_modeofentry',null);
			$userfullname = $this->_getParam('userfullname',null);
			$candidatereferredby = $this->_getParam('candidatereferredby',null);
			$rccandidatename = $this->_getParam('rccandidatename',null);
			$emprole = $this->_getParam('emprole',null);	//roleid_group_id
			if($emprole != "")
			{
				$roleArr = explode('_',$emprole);
				if(!empty($roleArr))
				{	
					$emproleStr = $roleArr[0];
				}
			}
			$emailaddress = $this->_getParam('emailaddress',null);
			$tmp_name = $this->_request->getParam('tmp_emp_name',null);
			$act_inact = $this->_request->getParam("act_inact",null);
            //end of user table
            
			$date = new Zend_Date();
			$empstatusarray = array(8,9,10);
			$actionflag = '';	$tableid  = ''; 
		
			if($modeofentry == 'Direct' || $hid_modeofentry == 'Direct')
			{
				$candidate_key = 'userfullname';
				$candidate_value = $userfullname;
				$emp_name = $userfullname;
				$candidate_flag = 'no';
			}
			else 
			{
				$candidate_key = 'rccandidatename';
				$candidate_value = $rccandidatename;
				$emp_name = $tmp_name;                    
				$candidate_flag = 'yes';
				
			}
            $trDb = Zend_Db_Table::getDefaultAdapter();		
			// starting transaction
			$trDb->beginTransaction();
            try
            {
				$emppassword = sapp_Global::generatePassword();
				$user_data = array(
							    'emprole' =>$emproleStr,
								$candidate_key => $candidate_value,
								'emailaddress' => $emailaddress,                                                                
								'modifiedby'=> $loginUserId,
								'modifieddate'=> Zend_Registry::get('currentdate'),                                                                      
								'emppassword' => md5($emppassword),
								'employeeId' => $employeeId,
								'modeofentry' => ($id =='')?$modeofentry:"",                                                              
								'selecteddate' => $date_of_joining,
								'candidatereferredby' => $candidatereferredby,
								'userstatus' => 'old',
								'other_modeofentry' => $other_modeofentry,
							);
				if($id!='')
				{
					$where = array('user_id=?'=>$user_id);  
					$actionflag = 2;
					$user_where = "id = ".$user_id;
					unset($user_data['candidatereferredby']);
					unset($user_data['userstatus']);
					unset($user_data['emppassword']);
					unset($user_data['employeeId']);
					unset($user_data['modeofentry']);
					unset($user_data['other_modeofentry']);
				}
				else
				{
					$data['createdby'] = $loginUserId;
					$data['createddate'] = gmdate("Y-m-d H:i:s");
					$data['isactive'] = 1;

					$user_data['createdby'] = $loginUserId;
					$user_data['createddate'] = gmdate("Y-m-d H:i:s");
					$user_data['isactive'] = 1;
					if($modeofentry != 'Direct')
					{
						$user_data['userfullname'] = $emp_name;
					}
					$where = '';
					$actionflag = 1;
					$user_where = '';
					
				}
                    
                $user_status = $usersModel->SaveorUpdateUserData($user_data, $user_where);
                if($id == '')	 $user_id = $user_status;
                
				$data = array(  'user_id'=>$user_id,
								'reporting_manager'=>$reporting_manager,
								'emp_status_id'=>$emp_status_id,
								'businessunit_id'=>$businessunit_id,
								'department_id'=>$department_id,
								'jobtitle_id'=>$jobtitle_id, 
								'position_id'=>$position_id, 
								'prefix_id'=>$prefix_id,
								'extension_number'=>$extension_number,  									
								'date_of_joining'=>$date_of_joining,
								'date_of_leaving'=>($date_of_leaving!=''?$date_of_leaving:NULL),
								'years_exp'=>($years_exp=='')?null:$years_exp,
								'modifiedby'=>$loginUserId,
								'modifieddate'=>gmdate("Y-m-d H:i:s")
                            );
                
				$Id = $employeeModal->SaveorUpdateEmployeeData($data, $where);
                $statuswhere = array('id=?'=>$user_id);
			   
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
				}
                else
                {
                    $statusdata = array('isactive'=> 1); 
                    $empstatusId = $usersModel->SaveorUpdateUserData($statusdata, $statuswhere);
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
					$this->view->emp_name = $emp_name;
					$this->view->password = $emppassword;
					$this->view->emp_id = $employeeId;
					$this->view->base_url=$base_url;
					$text = $view->render('mailtemplates/newpassword.phtml');
					$options['subject'] = APPLICATION_NAME.' login credentials';
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
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);	
				$menuID_user = MANAGEEXTERNALUSERS;
                 			
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$user_id);
				if($act_inact == 1)
				{
					if($user_data['isactive'] == 1)
					{
						$act_str = array("Activated" => Zend_Registry::get('currentdate'));
					}
					else 
					{
						$act_str = array("Inactivated"=> Zend_Registry::get('currentdate'));
					}
				  
					$result = sapp_Global::logManager($menuID_user,4,$loginUserId,$user_id,'',$act_str);
				}
				$trDb->commit();
				$this->_redirect('employee/edit/id/'.$user_id);	
            }
			catch (Exception $e) 
			{
				$trDb->rollBack();
				$msgarray['employeeId'] = "Something went wrong,please try again later.";
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
			$usersModel = new Default_Model_Users();
			if(isset($businessunit_id) && $businessunit_id != 0 && $businessunit_id != '')
			{
				$departmentsmodel = new Default_Model_Departments();
				$departmentlistArr = $departmentsmodel->getDepartmentList($businessunit_id);
				$employeeform->department_id->clearMultiOptions();
				$employeeform->reporting_manager->clearMultiOptions();
				$employeeform->department_id->addMultiOption('','Select Department');
				foreach($departmentlistArr as $departmentlistresult)
				{
					$employeeform->department_id->addMultiOption($departmentlistresult['id'],utf8_encode($departmentlistresult['deptname']));
				}  
				if(isset($department_id) && $department_id != 0 && $department_id != '')
					$employeeform->setDefault('department_id',$department_id);			
			}
				
			if(isset($department_id) && $department_id != 0 && $department_id != '')
			{
				$reportingManagerArr = $usersModel->getReportingManagerList($department_id,$user_id);
				$employeeform->reporting_manager->addMultiOption('','Select Reporting Manager');
				foreach($reportingManagerArr as $reportingManagerresult)
				{
					$employeeform->reporting_manager->addMultiOption($reportingManagerresult['id'],utf8_encode($reportingManagerresult['userfullname']));
				}  
				if(isset($reporting_manager) && $reporting_manager != 0 && $reporting_manager != '')
					$employeeform->setDefault('reporting_manager',$reporting_manager);			
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

	public function documentsAction()
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
		 		}

			 	$id = $loginUserId;//$this->getRequest()->getParam('userid');
			 	
			 	try
			 	{
			 		if($id && is_numeric($id) && $id>0 && $id==$loginUserId)
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
	}
	public function assetdetailsviewAction()
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
				}
	
				$id = $loginUserId;//$this->getRequest()->getParam('userid');
					
				try
				{
					if($id && is_numeric($id) && $id>0 && $id==$loginUserId)
					{
						$employeeModal = new Default_Model_Employee();
						$empdata = $employeeModal->getActiveEmployeeData($id);
						if(!empty($empdata))
						{
							$assetcategoriesModel = new Assets_Model_AssetCategories();
							$userassetdata = $assetcategoriesModel->getUserAssetData($id);
							if(!empty($userassetdata) && $userassetdata != "norows")
							{
							$this->view->userassetdata = $userassetdata;
							}
							else
							{
								$this->view->ermsg = 'norecord';
							}
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
	}
}