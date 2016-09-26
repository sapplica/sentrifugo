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
		 	if(sapp_Global::_checkprivileges(PAYFREQUENCY,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
		 		array_push($popConfigPermission,'payfrequency');
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
		 					$empsalarydetailsModal = new Default_Model_Empsalarydetails();
		 					$usersModel = new Default_Model_Users();

		 					$currencymodel = new Default_Model_Currency();
		 					$accountclasstypemodel = new Default_Model_Accountclasstype();
		 					$bankaccounttypemodel = new Default_Model_Bankaccounttype();
		 					$payfrequencyModal = new Default_Model_Payfrequency();
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
                                                        $empsalarydetailsform->bankaccountid->addMultiOption('','Select Account Type');
		 					if(!empty($bankaccounttypeArr))
		 					{
		 						
		 						foreach ($bankaccounttypeArr as $bankaccounttyperes){
		 							$empsalarydetailsform->bankaccountid->addMultiOption($bankaccounttyperes['id'],$bankaccounttyperes['bankaccounttype']);

		 						}
		 					}else
		 					{
		 						$msgarray['bankaccountid'] = 'Account types are not configured yet.';
		 						$emptyFlag++;
		 					}

		 					$accountclasstypeArr = $accountclasstypemodel->getAccountClassTypeList();
                                                        $empsalarydetailsform->accountclasstypeid->addMultiOption('','Select Account Class Type');
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
		 						 $data[0]['salary']=sapp_Global:: _decrypt( $data[0]['salary']);
                                                          //  if($data[0]['salary'] != '')
                                                          //  $data[0]['salary'] = number_format($data[0]['salary'], 2, '.', '');
                                                          //  $data[0]['salary'];
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
		 					$empsalarydetailsform->setAttrib('action',BASE_URL.'empsalarydetails/edit/userid/'.$id);

		 					$this->view->form = $empsalarydetailsform;
		 						
		 					$this->view->data = isset($data[0])?$data[0]:array();
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
										$data[0]['currencyid']="";
									}
		 						}

		 						if(isset($data[0]['accountclasstypeid']) && $data[0]['accountclasstypeid'] !='')
		 						{
		 							$accountclasstypeArr = $accountclasstypemodel->getsingleAccountClassTypeData($data[0]['accountclasstypeid']);
		 							if(sizeof($accountclasstypeArr)>0 && $accountclasstypeArr !='norows')
		 							{
		 								$empsalarydetailsform->accountclasstypeid->addMultiOption($accountclasstypeArr[0]['id'],$accountclasstypeArr[0]['accountclasstype']);
		 							    $data[0]['accountclasstypeid']=$accountclasstypeArr[0]['accountclasstype'];
		 							}
									else
									{
										 $data[0]['accountclasstypeid']="";
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
										 $data[0]['bankaccountid']="";
									}
		 						}
		 						
		 						if(isset($data[0]['salarytype']) && $data[0]['salarytype'] !='')
		 						{
				 					$payfreqData = $payfrequencyModal->getActivePayFreqData($data[0]['salarytype']);
									if(sizeof($payfreqData) > 0)
									{
										foreach ($payfreqData as $payfreqres){
											$empsalarydetailsform->salarytype->addMultiOption($payfreqres['id'],$payfreqres['freqtype']);
										}
									}
		 						}

		 						$empsalarydetailsform->populate($data[0]);

		 						if($data[0]['accountholding'] !='')
		 						{
		 							$accountholding = sapp_Global::change_date($data[0]["accountholding"], 'view');
		 							$empsalarydetailsform->accountholding->setValue($accountholding);
		 						}
			 					 if(!empty($data[0]['salarytype']))
								 {
							           $salarytype = $payfrequencyModal->getsinglePayfrequencyData($data[0]['salarytype']);
							            if(!empty($salarytype) && $salarytype !='norows')
							            {
								          $data[0]['salarytype'] = $salarytype[0]['freqtype'];
							            }
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
		 					$this->view->data = $data;
		 					$this->view->id = $id;
		 					$this->view->form = $empsalarydetailsform;
		 					$this->view->employeedata = $employeeData[0];

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
				$actionflag = '';
				$tableid  = '';
	            $salary=sapp_Global::_encrypt($salary);
				$data = array('user_id'=>$user_id,
					                 'currencyid'=>$currencyid,
									 'salarytype'=>$salarytype,
									 'salary'=>($salary!=''?$salary:NULL), 	
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
				$menuID = EMPLOYEE;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$user_id);
           }else
           {
           		$this->_helper->getHelper("FlashMessenger")->addMessage(array("error"=>FIELDMSG));
           }
           $this->_redirect('empsalarydetails/edit/userid/'.$userid);
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
