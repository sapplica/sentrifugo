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

class Default_EmppersonaldetailsController extends Zend_Controller_Action
{

	private $options;
	public function preDispatch()
	{

	}

	public function init()
	{
		$employeeModel = new Default_Model_Employee();
		$this->_options= $this->getInvokeArg('bootstrap')->getOptions();
	}

	public function indexAction()
	{

	}

	public function viewAction()
	{
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);
		 if(in_array('emppersonaldetails',$empOrganizationTabs)){

		 	$loginUserId ='';$loginUserGroup ='';$loginUserRole ='';
		 	$auth = Zend_Auth::getInstance();
		 	if($auth->hasIdentity()){
		 		$loginUserId = $auth->getStorage()->read()->id;
		 	}
		 	$id = $this->getRequest()->getParam('userid');
		 	if($id == '')		$id = $loginUserId;

		 	$callval = $this->getRequest()->getParam('call');
		 	if($callval == 'ajaxcall')
		 	$this->_helper->layout->disableLayout();

		 	$objName = 'emppersonaldetails';
		 	$emppersonaldetailsform = new Default_Form_emppersonaldetails();
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
		 		if($id && is_numeric($id) && $id>0 && $id!=$loginUserId)
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
		 					//$this->view->employeedata = $employeeData[0];
		 					$this->view->employeedata = $empdata[0];

		 				}
		 				$this->view->empdata = $empdata;
		 			}
		 		}else
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

	public function editAction()
	{
		$genderaddpermission = '';
		$msaddpermission = '';
		$ethnicaddpermission = '';
		$racecodepermission = '';
		$languagepermission = '';
		$nationalityaddpermission = '';	
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
				
				$genderaddpermission = sapp_Global::_checkprivileges(GENDER,$loginUserGroup,$loginUserRole,'add');
				$msaddpermission = sapp_Global::_checkprivileges(MARITALSTATUS,$loginUserGroup,$loginUserRole,'add');
				$ethnicaddpermission = sapp_Global::_checkprivileges(ETHNICCODE,$loginUserGroup,$loginUserRole,'add');
				$racecodepermission = sapp_Global::_checkprivileges(RACECODE,$loginUserGroup,$loginUserRole,'add');
				$languagepermission = sapp_Global::_checkprivileges(LANGUAGE,$loginUserGroup,$loginUserRole,'add');
				$nationalityaddpermission = sapp_Global::_checkprivileges(NATIONALITY,$loginUserGroup,$loginUserRole,'add');
				$id = $this->getRequest()->getParam('userid');
				if($id == '')		$id = $loginUserId;
				$callval = $this->getRequest()->getParam('call');
				if($callval == 'ajaxcall')
				$this->_helper->layout->disableLayout();

				try
				{
					if($id && is_numeric($id) && $id>0 && $id!=$loginUserId)
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
							//$empdata = $employeeModal->getActiveEmployeeData($id);
							if(!empty($empdata))
							{
								$empperdetailsModal = new Default_Model_Emppersonaldetails();
								$usersModel = new Default_Model_Users();
								$emppersonaldetailsform = new Default_Form_emppersonaldetails();
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
								//$employeeData = $usersModel->getUserDetailsByIDandFlag($id);
								//echo "<pre>";print_r($employeeData);exit;
								$genderModel = new Default_Model_Gender();
								$maritalstatusmodel = new Default_Model_Maritalstatus();
								$nationalitymodel = new Default_Model_Nationality();
								$ethniccodemodel = new Default_Model_Ethniccode();
								$racecodemodel = new Default_Model_Racecode();
								$languagemodel = new Default_Model_Language();
								$msgarray = array();

								$genderlistArr = $genderModel->getGenderList();
								//$genderlistArr="";
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
								//echo"<pre>";print_r($identityDocumentArr);exit;
								if(!empty($identityDocumentArr))
								{
									/*if($identityDocumentArr[0]['passport'] == 1)
									 {
									 $emppersonaldetailsform->passport->setRequired(true);
									 $emppersonaldetailsform->passport->addValidator('NotEmpty', false, array('messages' => 'Please enter passport number.'));
									 }
									 if($identityDocumentArr[0]['ssn'] == 1)
									 {
									 $emppersonaldetailsform->SSN_number->setRequired(true);
									 $emppersonaldetailsform->SSN_number->addValidator('NotEmpty', false, array('messages' => 'Please enter SSN.'));
									 }
									 if($identityDocumentArr[0]['aadhaar'] == 1)
									 {
									 $emppersonaldetailsform->adhar_number->setRequired(true);
									 $emppersonaldetailsform->adhar_number->addValidator('NotEmpty', false, array('messages' => 'Please enter aadhar number.'));
									 }
									 if($identityDocumentArr[0]['pancard'] == 1)
									 {
									 $emppersonaldetailsform->pancard_number->setRequired(true);
									 $emppersonaldetailsform->pancard_number->addValidator('NotEmpty', false, array('messages' => 'Please enter pancard number.'));
									 }
									 if($identityDocumentArr[0]['drivinglicense'] == 1)
									 {
									 $emppersonaldetailsform->drivinglicence_number->setRequired(true);
									 $emppersonaldetailsform->drivinglicence_number->addValidator('NotEmpty', false, array('messages' => 'Please enter driving license number.'));
									 }
									 if($identityDocumentArr[0]['others'] !='')
									 {
									 $emppersonaldetailsform->otheridentity->setRequired(true);
									 $emppersonaldetailsform->otheridentity->addValidator('NotEmpty', false, array('messages' => 'Please enter '.$identityDocumentArr[0]['others'].'.'));
									 }*/
									$this->view->identitydocument = $identityDocumentArr;
								}


								$data = $empperdetailsModal->getsingleEmpPerDetailsData($id);
								//echo "<pre>";print_r($data);exit;
								if(!empty($data))
								{
									$emppersonaldetailsform->populate($data[0]);

									$dob = sapp_Global::change_date($data[0]["dob"], 'view');
									$emppersonaldetailsform->dob->setValue($dob);
									if($data[0]['celebrated_dob'] !='')
									{

										$celebrated_dob = sapp_Global::change_date($data[0]["celebrated_dob"], 'view');
										$emppersonaldetailsform->celebrated_dob->setValue($celebrated_dob);
									}
									$emppersonaldetailsform->setDefault('genderid',$data[0]['genderid']);
									$emppersonaldetailsform->setDefault('maritalstatusid',$data[0]['maritalstatusid']);
									$emppersonaldetailsform->setDefault('nationalityid',$data[0]['nationalityid']);
									$emppersonaldetailsform->setDefault('ethniccodeid',$data[0]['ethniccodeid']);
									$emppersonaldetailsform->setDefault('racecodeid',$data[0]['racecodeid']);
									$emppersonaldetailsform->setDefault('languageid',$data[0]['languageid']);
								}
								$emppersonaldetailsform->user_id->setValue($id);
								$emppersonaldetailsform->setAttrib('action',DOMAIN.'emppersonaldetails/edit/userid/'.$id);

								$this->view->form = $emppersonaldetailsform;
								$this->view->data = $data;
								$this->view->id = $id;
								$this->view->msgarray = $msgarray;
								$this->view->employeedata = $empdata[0];
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
					$result = $this->save($emppersonaldetailsform,$id);
					$this->view->msgarray = $result;
				}

			}
			else
			{
				$this->_redirect('error');
			}
		}else{
			$this->_redirect('error');
		}
		$this->view->genderaddpermission = $genderaddpermission;
		$this->view->msaddpermission = $msaddpermission;
		$this->view->ethnicaddpermission = $ethnicaddpermission;
		$this->view->racecodepermission = $racecodepermission;
		$this->view->languagepermission = $languagepermission;
		$this->view->nationalityaddpermission = $nationalityaddpermission;
	}

	public function save($emppersonaldetailsform,$userid)
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		//echo"<pre>";print_r($this->_request->getPost());exit;
		if($emppersonaldetailsform->isValid($this->_request->getPost())){
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
			$celebrated_dob = $this->_request->getParam('celebrated_dob');
			$celebrated_dob = sapp_Global::change_date($celebrated_dob, 'database');

			$passport = $this->_request->getParam('passport');
			$pancard_number = $this->_request->getParam('pancard_number');
			$SSN_number = $this->_request->getParam('SSN_number');
			$adhar_number = $this->_request->getParam('adhar_number');
			$drivinglicence_number = $this->_request->getParam('drivinglicence_number');
			$otheridentity = $this->_request->getParam('otheridentity');

			$bloodgroup = $this->_request->getParam('bloodgroup');

			$date = new Zend_Date();
			$menumodel = new Default_Model_Menu();
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
								 'celebrated_dob'=>($celebrated_dob!=''?$celebrated_dob:NULL),
								 'passport'=>($passport!=''?$passport:NULL),
								 'pancard_number'=>($pancard_number!=''?$pancard_number:NULL),
								 'adhar_number'=>($adhar_number!=''?$adhar_number:NULL),
                                 'SSN_number'=>($SSN_number!=''?$SSN_number:NULL), 
                                 'drivinglicence_number'=>($drivinglicence_number!=''?$drivinglicence_number:NULL), 								 
								 'otheridentity'=>($otheridentity!=''?$otheridentity:NULL),
				      			 'bloodgroup'=>($bloodgroup!=''?$bloodgroup:NULL),
								 'modifiedby'=>$loginUserId,
			                     'modifieddate'=>gmdate("Y-m-d H:i:s")

			//'modifieddate'=>$date->get('yyyy-MM-dd HH:mm:ss')
			);
			if($id!=''){
				$where = array('user_id=?'=>$user_id);
				$actionflag = 2;
			}
			else
			{
				$data['createdby'] = $loginUserId;
				//$data['createddate'] = $date->get('yyyy-MM-dd HH:mm:ss');
				$data['createddate'] = gmdate("Y-m-d H:i:s");
				$data['isactive'] = 1;
				$where = '';
				$actionflag = 1;
			}
			//print_r($where);
			//echo "<pre>";print_r($data);exit;
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
			$menuidArr = $menumodel->getMenuObjID('/employee');
			$menuID = $menuidArr[0]['id'];
			//echo "<pre>";print_r($menuidArr);exit;
			$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$user_id);
			//echo $result;exit;
			$this->_redirect('emppersonaldetails/edit/userid/'.$user_id);
		}else
		{
			$messages = $emppersonaldetailsform->getMessages();
			//echo"<pre>";print_r($messages);exit;
			foreach ($messages as $key => $val)
			{
				foreach($val as $key2 => $val2)
				{
					$msgarray[$key] = $val2;
					break;
				}
			}
			//echo"<pre>";print_r($messages);exit;
			return $msgarray;
		}

	}



}