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

class Default_WorkeligibilitydetailsController extends Zend_Controller_Action
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
		//echo "In index Action";die;

	}

	public function addAction()
	{
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);

		 if(in_array('workeligibilitydetails',$empOrganizationTabs)){
		 	$msgarray = array();
		 	$auth = Zend_Auth::getInstance();
		 	if($auth->hasIdentity())
		 	{
		 		$loginUserId = $auth->getStorage()->read()->id;
		 	}
		 	$workeligibilityform = new Default_Form_Workeligibilitydetails();

		 	$this->view->form = $workeligibilityform;
		 	$this->view->msgarray = $msgarray;
		 	$workeligibilityform->setAttrib('action',BASE_URL.'workeligibilitydetails/add');
		 	if($this->getRequest()->getPost())
		 	{
		 		$result = $this->save($workeligibilityform);
		 		$this->view->form = $workeligibilityform;
		 		$this->view->msgarray = $result;
		 	}
		 }else{
		 	$this->_redirect('error');
		 }
		}else{
			$this->_redirect('error');
		}

	}
	/*

	*/
	public function editAction()
	{
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);

		 if(in_array('workeligibilitydetails',$empOrganizationTabs)){
		 	$auth = Zend_Auth::getInstance();
		 	if($auth->hasIdentity())
		 	{
		 		$loginUserId = $auth->getStorage()->read()->id;
		 		$loginUserRole = $auth->getStorage()->read()->emprole;
		 		$loginUserGroup = $auth->getStorage()->read()->group_id;
		 	}

		 	$this->view->WorkeligibilitydoctypesPermission = sapp_Global::_checkprivileges(WORKELIGIBILITYDOCTYPES,$loginUserGroup,$loginUserRole,'add');
		 	
		 	$popConfigPermission = array();
		    if(sapp_Global::_checkprivileges(COUNTRIES,$loginUserGroup,$loginUserRole,'add') == 'Yes'){
					array_push($popConfigPermission,'country');
			}
			if(sapp_Global::_checkprivileges(STATES,$loginUserGroup,$loginUserRole,'add') == 'Yes'){
					array_push($popConfigPermission,'state');
			}
			if(sapp_Global::_checkprivileges(CITIES,$loginUserGroup,$loginUserRole,'add') == 'Yes'){
					array_push($popConfigPermission,'city');
			} 
		 	$this->view->popConfigPermission = $popConfigPermission;
		 	
		 	$userid = $this->getRequest()->getParam('userid');
		 	$employeeModal = new Default_Model_Employee();
		 	$workeligibilityform = new Default_Form_Workeligibilitydetails();
		 	$workeligibilityModel = new Default_Model_Workeligibilitydetails();
		 	$msgarray = array();
		 	$emptyFlag=0;
			$issuingauthority = '';
			//To check previliges for edit
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
			 	if($loginUserRole == SUPERADMINROLE || $loginUserGroup == MANAGEMENT_GROUP || $loginUserGroup == HR_GROUP || ($loginUserGroup == MANAGER_GROUP && in_array($userid,$teamIdArr)))
			 	{
			 		
			 	try
			 	{
		 		if($userid && is_numeric($userid) && $userid>0 && $userid!=$loginUserId)
		 		{
		 			$usersModel = new Default_Model_Users();
					$empdata = $employeeModal->getActiveEmployeeData($userid);
					$employeeData = $usersModel->getUserDetailsByIDandFlag($userid);
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
		 					$countriesModel = new Default_Model_Countries();
		 					$statesmodel = new Default_Model_States();
		 					$citiesmodel = new Default_Model_Cities();
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

		 					$countrieslistArr = $countriesModel->getTotalCountriesList();
		 					if(!empty($countrieslistArr))
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
		 					}
		 					$data = $workeligibilityModel->getWorkEligibilityRecord($userid);
		 					//echo "<pre>Work eligibility  Data ";print_r($data);echo "</pre>"; die;
		 					if(!empty($data) && isset($data))
		 					{
		 						$countryId = $data[0]['issuingauth_country'];
		 						$stateId = $data[0]['issuingauth_state'];
		 						$cityId = $data[0]['issuingauth_city'];
								$documenttype_id = $data[0]['documenttype_id'];
								if($countryId !='')
								{
									$statelistArr = $statesmodel->getStatesList($countryId);
									if(!empty($statelistArr))
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
									if(!empty($citylistArr))
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

		 						$issue_date = date(DATEFORMAT_PHP, strtotime($data[0]["doc_issue_date"]));
		 						$workeligibilityform->setDefault('doc_issue_date', $issue_date);

		 						$expiry_date = date(DATEFORMAT_PHP, strtotime($data[0]["doc_expiry_date"]));
		 						$workeligibilityform->setDefault('doc_expiry_date', $expiry_date);
		 					}
		 					$workeligibilityform->setAttrib('action',BASE_URL.'workeligibilitydetails/edit/userid/'.$userid);
		 					$workeligibilityform->setDefault("user_id",$userid);
		 					$this->view->id = $userid;
		 				}
		 				$this->view->employeedata = $employeeData[0];
		 				$this->view->form = $workeligibilityform;
		 				$this->view->empdata = $empdata;
		 				$this->view->msgarray = $msgarray;
		 				$this->view->emptyFlag= $emptyFlag;
						$this->view->issuingauthority= $issuingauthority;
		 				$this->view->messages = $this->_helper->flashMessenger->getMessages();
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
			    $result = $this->save($workeligibilityform);
				$result['issuingauthorityflag'] = $_POST['issuingauthflag'];
		 		$this->view->msgarray = $result;
		 	}
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

	public function save($workeligibilityform)
	{
		$result ="";
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$date = new Zend_Date();
		$workeligibilitydoctypesmodel = new Default_Model_Workeligibilitydoctypes();
		$documenttype_id = $this->_request->getParam('documenttype_id');
		if($documenttype_id !='')
		 $issuingauthorityArr = $workeligibilitydoctypesmodel->getIssuingAuthority($documenttype_id);
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
		if($workeligibilityform->isValid($this->_request->getPost()))
		{
			$post_values = $this->_request->getPost();
           	if(isset($post_values['id']))
            	unset($post_values['id']);
            if(isset($post_values['user_id']))
                unset($post_values['user_id']);
            if(isset($post_values['submit']))	
                unset($post_values['submit']);
           	$new_post_values = array_filter($post_values);
           	$user_id = $this->_request->getParam('userid');
           	if(!empty($new_post_values))
           	{
				$workeligibilityModel = new Default_Model_Workeligibilitydetails();
				$id = $this->_request->getParam('id');
				$doc_issue_date = $this->_request->getParam('doc_issue_date',null);
				$doc_expiry_date = $this->_request->getParam('doc_expiry_date',null);
				$issuingauth_name = $this->_request->getParam('issuingauth_name');
				$issuingauth_country = $this->_request->getParam('issuingauth_country');
				$issuingauth_state = $this->_request->getParam('issuingauth_state');
				$issuingauth_city = $this->_request->getParam('issuingauth_city');
				$issuingauth_postalcode = $this->_request->getParam('issuingauth_postalcode');
	
				$stateArr = explode("!@#",$issuingauth_state);
				$stateStr = $stateArr[0];
				$cityArr = explode("!@#",$issuingauth_city);
				$cityStr = $cityArr[0];
	
				$docissueDate = sapp_Global::change_date($doc_issue_date, 'database');
				$docexpiryDate = sapp_Global::change_date($doc_expiry_date, 'database');
	
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
									'modifieddate'=>gmdate("Y-m-d H:i:s"));
				if($id!='')
				{
					$where = array('user_id=?'=>$user_id);
					$actionflag = 2;
				}
				else
				{
					$data['createdby'] = $loginUserId;
					$data['createddate'] = gmdate("Y-m-d H:i:s");
					$where = '';
					$actionflag = 1;
				}
				$Id = $workeligibilityModel->SaveorUpdateWorkEligibilityDetails($data, $where);
				if($Id == 'update')
				{
					$tableid = $id;
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Employee work eligibility details updated successfully."));
				}
				else
				{
					$tableid = $Id;
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Employee work eligibility details added successfully."));
				}
				$menuID = EMPLOYEE;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$user_id);
           	} else {
           		$this->_helper->getHelper("FlashMessenger")->addMessage(array("error"=>FIELDMSG));
			}
			$this->_redirect('workeligibilitydetails/edit/userid/'.$user_id);
		}
		else
		{
			$messages = $workeligibilityform->getMessages();

			foreach ($messages as $key => $val)
			{
				foreach($val as $key2 => $val2)
				{
					$msgarray[$key] = $val2;
					break;
				}
			}
			if($this->_request->getPost())
			{
				$issuingauth_country = $this->_request->getParam('issuingauth_country');
				$issuingauth_state = $this->_request->getParam('issuingauth_state');
				$stateArr = explode("!@#",$issuingauth_state);
				$stateStr = $stateArr[0];
				$issuingauth_city = $this->_request->getParam('issuingauth_city');
				$cityArr = explode("!@#",$issuingauth_city);
				$cityStr = $cityArr[0];
			}
			else	{
				$issuingauth_country = '';
				$issuingauth_state = '';
				$issuingauth_city = '';
			}
			if(isset($issuingauth_country) && $issuingauth_country != 0 && $issuingauth_country != '')
			{
				$statesmodel = new Default_Model_States();
				$statesmodeldata = $statesmodel->getStatesList($issuingauth_country);
					
				$workeligibilityform->issuingauth_state->addMultiOption('','Select State');
				foreach($statesmodeldata as $res)
				$workeligibilityform->issuingauth_state->addMultiOption($res['id'].'!@#'.utf8_encode($res['state_name']),utf8_encode($res['state_name']));
					
				if(isset($issuingauth_state) && $issuingauth_state != 0 && $issuingauth_state != '')
				$workeligibilityform->setDefault('issuingauth_state',$issuingauth_state);
			}

			if(isset($issuingauth_state) && $issuingauth_state != 0 && $issuingauth_state != '')
			{
				$citiesmodel = new Default_Model_Cities();
				$citiesmodeldata = $citiesmodel->getCitiesList($stateStr);
					
				$workeligibilityform->issuingauth_city->addMultiOption('','Select City');
				foreach($citiesmodeldata as $res)
				$workeligibilityform->issuingauth_city->addMultiOption($res['id'].'!@#'.utf8_encode($res['city_name']),utf8_encode($res['city_name']));
					
				if(isset($issuingauth_city) && $issuingauth_city != 0 && $issuingauth_city != '')
				$workeligibilityform->setDefault('issuingauth_city',$issuingauth_city);
			}
			return $msgarray;
		}

	}
	public function viewAction()
	{
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);

		 if(in_array('workeligibilitydetails',$empOrganizationTabs)){
		 	$auth = Zend_Auth::getInstance();
		 	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		 	}
		 	$employeeData=array();
		 	$id = $this->getRequest()->getParam('userid');
		 	$callval = $this->getRequest()->getParam('call');
		 	if($callval == 'ajaxcall')
		 	$this->_helper->layout->disableLayout();

		 	$objName = 'workeligibilitydetails';
			$issuingauthority = '';
		 	$employeeModal = new Default_Model_Employee();
		 	$workeligibilityform = new Default_Form_Workeligibilitydetails();
		 	$workeligibilityform->removeElement("submit");
		 	$elements = $workeligibilityform->getElements();
		 	if(count($elements)>0)
		 	{
		 		foreach($elements as $key=>$element)
		 		{
		 			if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments"))
		 			{
		 				$element->setAttrib("disabled", "disabled");
		 			}
		 		}
		 	}

		 	try
		 	{
		 		if($id && is_numeric($id) && $id>0 && $id!=$loginUserId)
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
		 			{
		 				$this->view->rowexist = "rows";
		 				if(!empty($empdata))
		 				{
		 					$workeligibilityModel = new Default_Model_Workeligibilitydetails();
		 					if($id)
		 					{
		 						$usersModel = new Default_Model_Users();
								$workeligibilityDoctypesModal = new Default_Model_Workeligibilitydoctypes();
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
		 						}
		 						$data = $workeligibilityModel->getWorkEligibilityRecord($id);

		 						if(!empty($data) && isset($data))
		 						{
		 							$countryId = $data[0]['issuingauth_country'];
		 							$stateId = $data[0]['issuingauth_state'];
		 							$cityId = $data[0]['issuingauth_city'];
									$documenttype_id = $data[0]['documenttype_id'];
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
									   $workeligibilityform->documenttype_id->addMultiOption($issuingauthorityArr[0]['id'],$issuingauthorityArr[0]['documenttype']);
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

		 							$issue_date = date(DATEFORMAT_PHP, strtotime($data[0]["doc_issue_date"]));
		 							$workeligibilityform->setDefault('doc_issue_date', $issue_date);

		 							$expiry_date = date(DATEFORMAT_PHP, strtotime($data[0]["doc_expiry_date"]));
		 							$workeligibilityform->setDefault('doc_expiry_date', $expiry_date);
									
									if(!empty($data[0]['issuingauth_country'])){
									$countryname = $countriesModel->getCountryCode($data[0]['issuingauth_country']);
									if(!empty($countryname)){
										$data[0]['issuingauth_country'] = $countryname[0]['country_name'];
										}
										else{
											$data[0]['issuingauth_country'] = "";
										}
									}
									if(!empty($data[0]['issuingauth_state'])){
									$statename = $statesmodel->getStateName($data[0]['issuingauth_state']);
									if(!empty($statename)){
										$data[0]['issuingauth_state'] = $statename[0]['statename'];
										}
										else{
											$data[0]['issuingauth_state'] = "";
										}
									}
									if(!empty($data[0]['documenttype_id'])){
									$docname = $workeligibilityDoctypesModal->getsingleWorkEligibilityDocTypeData($data[0]['documenttype_id']);
									if(!empty($docname)){
										$data[0]['documenttype_id'] = $docname[0]['documenttype'];
										}
										else{
											$data[0]['documenttype_id'] = "";
										}
									}
				        
									if(!empty($data[0]['issuingauth_city'])){
									$cityname = $citiesmodel->getCityName($data[0]['issuingauth_city']);
									if(!empty($cityname)){
										$data[0]['issuingauth_city'] = $cityname[0]['cityname'];
										}
										else{
											$data[0]['issuingauth_city'] = "";
										}
									}	
										
										$this->view->data =$data[0];
		 						}
		 						
		 						$this->view->controllername = $objName;
		 						$this->view->id = $id;
		 						$this->view->employeedata = $employeeData[0];
		 						$this->view->form = $workeligibilityform;
								$this->view->issuingauthority= $issuingauthority;
		 					}
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


}
?>
