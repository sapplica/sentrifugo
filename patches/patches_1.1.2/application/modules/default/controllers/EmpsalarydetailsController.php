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

class Default_EmpsalarydetailsController extends Zend_Controller_Action
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

	public function editAction()
	{
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);

		 if(in_array('emp_salary',$empOrganizationTabs)){
		 	$auth = Zend_Auth::getInstance();
		 	$emptyFlag=0;
		 	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;
					$loginuserGroup = $auth->getStorage()->read()->group_id;
		 	}
		 	$popConfigPermission = array();
		 	if(sapp_Global::_checkprivileges(CURRENCY,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
		 		array_push($popConfigPermission,'currency');
		 	}
		 	if(sapp_Global::_checkprivileges(ACCOUNTCLASSTYPE,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
		 		array_push($popConfigPermission,'accountclasstype');
		 	}
		 	if(sapp_Global::_checkprivileges(BANKACCOUNTTYPE,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
		 		array_push($popConfigPermission,'bankaccounttype');
		 	}
		 	
		 	$this->view->popConfigPermission = $popConfigPermission;

		 	$id = $this->getRequest()->getParam('userid');
		 	if($id == '')
		 	$id = $loginUserId;
		 	$callval = $this->getRequest()->getParam('call');
		 	if($callval == 'ajaxcall')
		 	$this->_helper->layout->disableLayout();

		 	$empsalarydetailsform = new Default_Form_empsalarydetails();
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
		 					$empsalarydetailsModal = new Default_Model_Empsalarydetails();
		 					$usersModel = new Default_Model_Users();

		 					$currencymodel = new Default_Model_Currency();
		 					$accountclasstypemodel = new Default_Model_Accountclasstype();
		 					$bankaccounttypemodel = new Default_Model_Bankaccounttype();
		 					$msgarray = array();

		 					$basecurrencymodeldata = $currencymodel->getCurrencyList();
                                                        $empsalarydetailsform->currencyid->addMultiOption('','Select Salary Currency');
		 					if(sizeof($basecurrencymodeldata) > 0)
		 					{
		 						
		 						foreach ($basecurrencymodeldata as $basecurrencyres){
		 							$empsalarydetailsform->currencyid->addMultiOption($basecurrencyres['id'],utf8_encode($basecurrencyres['currency']));
		 						}
		 					}else
		 					{
		 						$msgarray['currencyid'] = 'Currencies are not configured yet.';
		 						$emptyFlag++;
		 					}

		 					$bankaccounttypeArr = $bankaccounttypemodel->getBankAccountList();
                                                        $empsalarydetailsform->bankaccountid->addMultiOption('','Select Bank Account Type');
		 					if(!empty($bankaccounttypeArr))
		 					{
		 						
		 						foreach ($bankaccounttypeArr as $bankaccounttyperes){
		 							$empsalarydetailsform->bankaccountid->addMultiOption($bankaccounttyperes['id'],$bankaccounttyperes['bankaccounttype']);

		 						}
		 					}else
		 					{
		 						$msgarray['bankaccountid'] = 'Bank account types are not configured yet.';
		 						$emptyFlag++;
		 					}

		 					$accountclasstypeArr = $accountclasstypemodel->getAccountClassTypeList();
                                                        $empsalarydetailsform->accountclasstypeid->addMultiOption('','Select Account Type');
		 					if(!empty($accountclasstypeArr))
		 					{
		 						
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

		 					}
		 					$empsalarydetailsform->user_id->setValue($id);
		 					$empsalarydetailsform->setAttrib('action',DOMAIN.'empsalarydetails/edit/userid/'.$id);

		 					$this->view->form = $empsalarydetailsform;
		 						
		 					$this->view->data = isset($data[0])?$data[0]:array();
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
		 		$result = $this->save($empsalarydetailsform,$id);
		 		$this->view->msgarray = $result;
		 	}
		 }else{
		 	$this->_redirect('error');
		 }
		}else{
			$this->_redirect('error');
		}
	}

	public function viewAction()
	{
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);

		 if(in_array('emp_salary',$empOrganizationTabs)){
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
		 							if($bankaccounttypeArr !='norows')
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

	public function save($empsalarydetailsform,$userid)
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		if($empsalarydetailsform->isValid($this->_request->getPost())){
			$empsalarydetailsModal = new Default_Model_Empsalarydetails();
			$id = $this->_request->getParam('id');
			$user_id = $userid;
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

			$date = new Zend_Date();
			$menumodel = new Default_Model_Menu();
			$actionflag = '';
			$tableid  = '';

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
			$Id = $empsalarydetailsModal->SaveorUpdateEmpSalaryData($data, $where);
			if($Id == 'update')
			{
				$tableid = $id;
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Employee salary details updated successfully."));
					
			}
			else
			{
				$tableid = $Id;
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Employee salary details added successfully."));
			}
			$menuidArr = $menumodel->getMenuObjID('/employee');
			$menuID = $menuidArr[0]['id'];
			$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$user_id);
			$this->_redirect('empsalarydetails/edit/userid/'.$user_id);
		}else
		{
			$messages = $empsalarydetailsform->getMessages();
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



}
?>
