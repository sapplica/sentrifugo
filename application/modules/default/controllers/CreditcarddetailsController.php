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

class Default_CreditcarddetailsController extends Zend_Controller_Action
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
	}

	public function addAction()
	{
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);

		 if(in_array('creditcarddetails',$empOrganizationTabs)){
		 	$msgarray = array();
		 	$auth = Zend_Auth::getInstance();
		 	if($auth->hasIdentity())
		 	{
		 		$loginUserId = $auth->getStorage()->read()->id;
		 	}
		 	$creditcardDetailsform = new Default_Form_Creditcarddetails();
		 	$this->view->form = $creditcardDetailsform;
		 	$this->view->msgarray = $msgarray;
		 	$creditcardDetailsform->setAttrib('action',BASE_URL.'creditcarddetails/edit/add');
		 	if($this->getRequest()->getPost())
		 	{
		 		$result = $this->save($creditcardDetailsform);
		 		$this->view->form = $creditcardDetailsform;
		 		$this->view->msgarray = $result;
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
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);

		 if(in_array('creditcarddetails',$empOrganizationTabs)){
		 	$employeeData=array();$empdata=array();
		 	$auth = Zend_Auth::getInstance();
		 	if($auth->hasIdentity())
		 	{
		 		$loginUserId = $auth->getStorage()->read()->id;
		 		$loginUserGroup = $auth->getStorage()->read()->group_id;
				$loginUserRole = $auth->getStorage()->read()->emprole;
		 	}
		 	$id = $this->getRequest()->getParam('userid');
		 	$auth = Zend_Auth::getInstance();
		 	$creditcardDetailsform = new Default_Form_Creditcarddetails();
		 	$creditcardDetailsModel = new Default_Model_Creditcarddetails();
		 		//TO get the Employee  profile information....
		 		$usersModel = new Default_Model_Users();
		 		$employeeModal = new Default_Model_Employee();
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
				
			 	if($loginUserRole == SUPERADMINROLE || $loginUserGroup == MANAGEMENT_GROUP || $loginUserGroup == HR_GROUP || ($loginUserGroup == MANAGER_GROUP && in_array($id,$teamIdArr)))
			 	{
			 		
			 	
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
						{	$this->view->rowexist = "rows";
						if(!empty($empdata))
						{
							$data = $creditcardDetailsModel->getcreditcarddetailsRecord($id);
							if(!empty($data))
							{
								$creditcardDetailsform->setDefault("id",$data[0]["id"]);
								$creditcardDetailsform->setDefault("user_id",$data[0]["user_id"]);
								$creditcardDetailsform->setDefault("card_type",$data[0]["card_type"]);
								$creditcardDetailsform->setDefault("card_number",$data[0]["card_number"]);
								$creditcardDetailsform->setDefault("nameoncard",$data[0]["nameoncard"]);
								$expiry_date = sapp_Global::change_date($data[0]["card_expiration"], 'view');
								$creditcardDetailsform->setDefault('card_expiration', $expiry_date);
								$creditcardDetailsform->setDefault("card_issuedby",$data[0]["card_issued_comp"]);
								$creditcardDetailsform->setDefault("card_code",$data[0]["card_code"]);
							}
							$creditcardDetailsform->setAttrib('action',BASE_URL.'creditcarddetails/edit/userid/'.$id);
							$this->view->id=$id;
							$this->view->form = $creditcardDetailsform;
							$this->view->employeedata = $employeeData[0];
							$this->view->messages = $this->_helper->flashMessenger->getMessages();

						}
						$this->view->empdata =$empdata;
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
		 			$result = $this->save($creditcardDetailsform);
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

	public function save($creditcardDetailsform)
	{
		$result ="";
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$date = new Zend_Date();
		if($creditcardDetailsform->isValid($this->_request->getPost()))
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
				$creditcardDetailsModel = new Default_Model_Creditcarddetails();
				$id = $this->_request->getParam('id');
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
				$Id = $creditcardDetailsModel->SaveorUpdateCreditcardDetails($data, $where);
				if($Id == 'update')
				{
					$tableid = $id;
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Employee corporate card details updated successfully."));
				}
				else
				{
					$tableid = $Id;
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Employee corporate card details added successfully."));
				}
				$menuID = EMPLOYEE;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$user_id);
				
           	} else {
           		$this->_helper->getHelper("FlashMessenger")->addMessage(array("error"=>FIELDMSG));
			}
			$this->_redirect('creditcarddetails/edit/userid/'.$user_id);
		}
		else
		{
			$messages = $creditcardDetailsform->getMessages();

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
	public function viewAction()
	{
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);

		 if(in_array('creditcarddetails',$empOrganizationTabs)){
		    $auth = Zend_Auth::getInstance();
		 	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		 	}
		 	$id = $this->getRequest()->getParam('userid');
		 	$callval = $this->getRequest()->getParam('call');
		 	if($callval == 'ajaxcall')
		 	$this->_helper->layout->disableLayout();
		 	$objName = 'creditcarddetails';
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
		
		 		$data = $creditcardDetailsModel->getcreditcarddetailsRecord($id);
		 		$employeeModal = new Default_Model_Employee();
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
								$this->view->id = $id;
								$this->view->employeedata = $employeeData[0];
								$this->view->form = $creditcardDetailsform;
								$this->view->data =$data;
							}
							$this->view->empdata =$empdata;
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
