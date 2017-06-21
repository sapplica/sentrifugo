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
class Expenses_AdvancesController extends Zend_Controller_Action
{
	 private $options;
	
	/**
	 * The default action - show the home page
	 */
	public function preDispatch()
	{
	
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('viewmoreadvances', 'html')->initContext();
		$ajaxContext->addActionContext('clearadvancesdata', 'html')->initContext();
		
	}

	public function init()
	{
		$this->_options= $this->getInvokeArg('bootstrap')->getOptions();

	}
	/**
	 * 
	 	 * This action is used for add advances .

	 */ 
	public function indexAction()
	{ 
		
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
		 	$loginUserId = $auth->getStorage()->read()->id;

		}
		
		$objName = 'advances';$emptyFlag=0;
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();

		$advancesForm = new Expenses_Form_Advance();
		$advancesModel = new Expenses_Model_Advances();

		$this->view->loginUserId=$loginUserId;
		$msgarray = array();
	    $usersData = $advancesModel->getUserList($loginUserId );
		if(sizeof($usersData) > 0)
		{
			foreach ($usersData as $user){
				$advancesForm->to_id->addMultiOption($user['cnt'],utf8_encode($user['userfullname']));
			}

		}else
		{
			$msgarray['to_id'] = 'employee are not configured yet.';
			$emptyFlag++;
		}
		
		
		
		$currencyModel = new Default_Model_Currency();
		$currencyData = $currencyModel->getCurrencyList();
		if(sizeof($currencyData) > 0)
		{
			foreach ($currencyData as $currency){
				$advancesForm->currency_id->addMultiOption($currency['id'],utf8_encode($currency['currency']));
			}

		}else
		{
			$msgarray['currency_id'] = 'Currency are not configured yet.';
			$emptyFlag++;
		}
		
		$projectModel = new Timemanagement_Model_Projects();
		$base_projectData = $projectModel->getProjectList();
		$paymentmodemodel = new Expenses_Model_Paymentmode();
		$paymentmodeData = $paymentmodemodel->getPaymentList();
		if(sizeof($paymentmodeData) > 0)
		{
			foreach ($paymentmodeData as $payment_mode){
				    $advancesForm->payment_mode_id->addMultiOption($payment_mode['id'],$payment_mode['payment_method_name']);
			}

		}
		$this->view->msgarray = $msgarray;
		$this->view->emptyFlag = $emptyFlag;
	
		try
		{

			if($id)
			{	
			
				if(is_numeric($id) && $id>0)
				{
					$projectModel = new Timemanagement_Model_Projects();
						$projectData = array();
						foreach ($projectData as $project){
							$advancesForm->project_id->addMultiOption($project_id['id'],utf8_encode($project_id['project_name']));
						}

					
					
					$auth = Zend_Auth::getInstance(); 
					if($auth->hasIdentity()){
						$loginUserId = $auth->getStorage()->read()->id;
					
					}
						$advancesForm = new Expenses_Form_Advance();

						if(!empty($data) && $data != "norows")
						{
							
							$advancesForm->populate($data[0]);
							$advancesForm->submit->setLabel('Update');
							
							
					
						$this->view->form = $advancesForm;
						$this->view->controllername = $objName;
						$this->view->id = $id;
						$this->view->ermsg = '';
						$this->view->inpage = 'Edit';
								
						
					}
					else
					{
						$this->view->ermsg = 'norecord';
					}
				}
				else
				{
					$this->view->ermsg = 'nodata';
				}
			}
			else
			{
			
				//Add Record...
				$this->view->ermsg = '';
				$this->view->form = $advancesForm;
				$this->view->inpage = 'Add';
			}
		}
		catch(Exception $ex)
		{
			$this->view->ermsg = 'nodata';
		}
		if($this->getRequest()->getPost()){
			if(isset($_POST['to_id']) && $_POST['to_id']!='')
			{
				$projectsmodel = new Timemanagement_Model_Projects();
				$projects = $projectsmodel->getEmployeeProjects(intval($_POST['to_id']));
				$project_array = array();
				if(count($projects) > 0)
				{
					foreach($projects as $project)
					{
						$project_array[$project['id']] = $project['project_name'];
					}
				}
				$advancesForm->project_id->addMultiOptions(array(''=>'Select Project')+$project_array);
			}
			if($advancesForm->isValid($this->_request->getPost())){
			
				$to_id	= NULL;
				$project_id	= NULL;
				$id = $this->_request->getParam('id');
				$to_id = $this->_request->getParam('to_id');
				$project_id =$this->_request->getParam('project_id');
				$currency = $this->_request->getParam('currency_id');
				$amount = $this->_request->getParam('amount');
				$payment_mode = $this->_request->getParam('payment_mode_id');
				$paymentref = $this->_request->getParam('payment_ref_number');
				$description = $this->_request->getParam('description');

				$date = new Zend_Date();
				$data = array('to_id'=>$to_id,
							  'project_id'=>$project_id,
				              'currency_id'=>$currency,
							  'amount'=>$amount,
							  'payment_mode_id'=>$payment_mode,
							  'payment_ref_number'=>$paymentref,
							  
							  'description'=>$description,
				              'createdby'=>$loginUserId,
							  'from_id'=>$loginUserId,
							 'createddate' => gmdate("Y-m-d H:i:s"),
							// 'modifiedby'=> $loginUserId,
							 //'modifieddate' =>gmdate("Y-m-d H:i:s"),
							 'isactive'=>1
							
				);

                    
					$where = '';
					

				
				$advancesModel = new Expenses_Model_Advances();
				$insertedId = $advancesModel->SaveAdvanceData($data, $where);
		
				
				//insert advance details
				
				//check if employee has already advance
				$advancesummary = new Expenses_Model_Advancesummary();
				$isRecordExist = $advancesummary->getAdvanceDetailsById($to_id);
				
				$summerydata = array();
				if(count($isRecordExist)>0)
				{
					$totalsum = $isRecordExist[0]['total']+$amount;
					$balence = $isRecordExist[0]['balance']+$amount;
					$summerydata = array('total'=>$totalsum,
										'balance'=>$balence,
								 'modifiedby'=> $loginUserId,
								 'modifieddate' =>gmdate("Y-m-d H:i:s")
							   );
					$summeryWhere = array('employee_id=?'=>$to_id); 		   
				}else
				{
					$totalsum  = $amount;
					$summerydata = array('total'=>$totalsum,
										'balance'=>$totalsum,
								 'createdby'=> $loginUserId,
								 'employee_id'=>$to_id,
								 'createddate' =>gmdate("Y-m-d H:i:s")
							   );
					$summeryWhere  = "";
				}	
				$insertSummey = $advancesummary->SaveAdvanceData($summerydata, $summeryWhere);
		
		
		
				if($insertedId == 'update')    
				{
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"advance  updated successfully."));
				}
				else
				{

					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Advance added successfully."));
				}

				$this->_redirect('expenses/advances');
			}else
			{
				
				$messages = $advancesForm->getMessages();
				foreach ($messages as $key => $val)
				{
					foreach($val as $key2 => $val2)
					{
						$msgarray[$key] = $val2;
						break;
					}
				}
				
				$this->view->msgarray = $msgarray;

		
			}
		} 
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
	}
	public function getprojectsAction()
	{
		$emp_id = $this->_getParam('emp_id');
		$projectsmodel = new Timemanagement_Model_Projects();
		
		$opt='<option value=\'\'>Select Project</option>';
		if($emp_id!='')
		{
			$projects = $projectsmodel->getEmployeeProjects($emp_id);
			foreach($projects as $pid)
			{
				$opt.="<option value='".$pid['id']."'>".$pid['project_name']."</option>";
			}
		}
		
		$this->_helper->json(array('options'=>utf8_encode($opt)));
	}
   
	public function myadvancesAction()
	{
		$auth = Zend_Auth::getInstance();
		
		if($auth->hasIdentity()){
		 	$loginUserId = $auth->getStorage()->read()->id;
			$loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		}	
		
		//login user add permission checking
		$addPermission = sapp_Global::_checkprivileges(MYADVANCES,$loginuserGroup,$loginuserRole,'add');
		$limit=4;$offset=0;
		$advancesModel = new Expenses_Model_Advances();
		$myAdvances = $advancesModel->getMyAdvances($limit,$offset,$loginUserId);
		$myutilizes = $advancesModel->getMyAdvanceData($loginUserId);
		
		/* To show more advances*/	

		$getadvancesCount = $advancesModel->getAdvancesCount($loginUserId);
		
		$this->view->limit = $limit;
		$this->view->offset = $limit+$offset;
		$this->view->getadvancesCount = $getadvancesCount;
		$this->view->myAdvances = $myAdvances;
		$this->view->myutilizes = $myutilizes;
		$this->view->addPermission = $addPermission;
		
		
		
	}
	
	public function viewmoreadvancesAction()
	{
        $auth = Zend_Auth::getInstance();
		$loginUserId = $auth->getStorage()->read()->id;
		$limit = $this->_getParam('limit')?$this->_getParam('limit'):4;
		$offset = $this->_getParam('offset')?$this->_getParam('offset'):0;
		
		$searchstr = $this->_getParam('searchstr')?$this->_getParam('searchstr'):'';
		
		$advancesModel = new Expenses_Model_Advances();
		$dataAdvances = $advancesModel->getMyAdvances($limit,$offset,$loginUserId,$searchstr);
		$getadvancesCount = $advancesModel->getAdvancesCount($loginUserId,$searchstr);
		$this->view->getadvancesCount = $getadvancesCount;
		$this->view->myAdvances = $dataAdvances;
		$this->view->limit = $limit;
		$this->view->offset = $limit+$offset;

		
	}
	
	public function clearadvancesdataAction()
	{
	$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			
		 	$loginUserId = $auth->getStorage()->read()->id;
			
		}
		$limit=4;$offset=0;
		$advancesModel = new Expenses_Model_Advances();
		$myAdvances = $advancesModel->getMyAdvances($limit,$offset,$loginUserId);
		
		
		

		$myutilizes = $advancesModel->getMyAdvanceData($loginUserId);
		
		
		/* To show more advances*/
		

		$getadvancesCount = $advancesModel->getAdvancesCount($loginUserId);
		
		$this->view->limit = $limit;
		$this->view->offset = $limit+$offset;
		$this->view->getadvancesCount = $getadvancesCount;
		$this->view->myAdvances = $myAdvances;
		$this->view->myutilizes = $myutilizes;	
	}
	public function addreturnpopupAction()
	{
		$msgarray = array();
		$emptyFlag = '';
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->getRequest()->getParam('id');
		$this->view->loginUserId=$loginUserId;
		$controllername = 'advances';
		$advanceForm = new Expenses_Form_Returnadvance();
		$tripsModel = new Expenses_Model_Trips();
		$advanceForm->setAction(BASE_URL.'expenses/advances/addreturnpopup');
		
		$advancesModel = new Expenses_Model_Advances();
		
	    $usersData = $advancesModel->getAdvanceUserList($loginUserId);
		if(sizeof($usersData) > 0)
		{
			foreach ($usersData as $user){
				$advanceForm->to_id->addMultiOption($user['from_id'],utf8_encode($user['userfullname']));
			}

		}else
		{
			$msgarray['to_id'] = 'employee are not configured yet.';
			$emptyFlag++;
		}
		
		
	
		//Start Currency list
		$expensesmodel = new Expenses_Model_Expenses();
		$currencyData = $expensesmodel->getCurrencyList();
		if(sizeof($currencyData) > 0)
		{
			foreach ($currencyData as $currency){
				$advanceForm->currency_id->addMultiOption($currency['id'],utf8_encode($currency['currencycode']));
			}

		}else
		{
			$msgarray['currency_id'] = 'Currency are not configured yet.';
			$emptyFlag++;
		}
		//End
		//start for setting application currency
		$tripsmodel = new Expenses_Model_Trips();
		$configData = $tripsmodel->getApplicationCurrency();
		$currency="";
		if(!empty($configData) && (isset($configData[0]['currencycode'])))
		{
		$currency = $configData[0]['currencycode'];
		}
		$advanceForm->currency_id->setValue($configData[0]['currencyid']);
	
		$this->view->currency=$currency;
		$this->view->currencyid=$configData[0]['currencyid'];
		
		//End
		$paymentmodemodel = new Expenses_Model_Paymentmode();
		$paymentmodeData = $paymentmodemodel->getPaymentList();
		if(sizeof($paymentmodeData) > 0)
		{
			foreach ($paymentmodeData as $payment_mode){
				    $advanceForm->payment_mode_id->addMultiOption($payment_mode['id'],$payment_mode['payment_method_name']);
			}

		}
		
		
		if($this->getRequest()->getPost()){
			
			if(isset($_POST['cal_amount']) && $_POST['cal_amount']!='')
			{
				$this->view->cal_amount = $_POST['cal_amount'];
			}
			if($advanceForm->isValid($this->_request->getPost())){
			
				$to_id	= NULL;
				$project_id	= NULL;
				$id = $this->_request->getParam('id');
				$to_id = $this->_request->getParam('to_id');
				$currency = $this->_request->getParam('currency_id');
				$amount = $this->_request->getParam('amount');
				$payment_mode = $this->_request->getParam('payment_mode_id');
				$paymentref = $this->_request->getParam('payment_ref_number');
				$description = $this->_request->getParam('description');
				$cal_amount = $this->_request->getParam('cal_amount');
				$app_amount="";

				$siteconfigmodel = new Default_Model_Sitepreference();
				$configData = $siteconfigmodel->getActiveRecord();
			
				if(!empty($configData) && (isset($configData[0]['currencyid'])))
				{
					$application_currency = $configData[0]['currencyid'];
				}
				
				if(!empty($id))
				{
					
					 $employeeadvancesModel = new Expenses_Model_Employeeadvances();
					$employeeadvance_exist_data = $employeeadvancesModel->getsingleEmployeeadvancesData($id,$type='return');
					$app_amount=$employeeadvance_exist_data[0]['application_amount']; 
				
				}
					
				$application_amount=null;
				if($cal_amount!=0)
				{
				
					$application_amount=$cal_amount*$amount;
				}
				
				if( $app_amount!="" && $cal_amount==0  && ($currency==$isRecordExist['currency_id']))
				{
					$application_amount=$app_amount;
				}
				
				
				if($currency==$configData[0]['currencyid'])
				{
					$application_amount=$amount;
				}
				
				
				

				$date = new Zend_Date();
				$data = array('to_id'=>$to_id,
				              'currency_id'=>$currency,
							  'amount'=>$amount,
							  'application_amount'=>$application_amount,
							  'payment_mode_id'=>$payment_mode,
							  'payment_ref_number'=>$paymentref,
							  'type'=>'return',
							  'description'=>$description,
				              'createdby'=>$loginUserId,
							  'from_id'=>$loginUserId,
							 'createddate' => gmdate("Y-m-d H:i:s"),
							 'isactive'=>1
							
				);

				$where = '';
				$insertedId = '';
				$advancesummaryModel = new Expenses_Model_Advancesummary();
				$advanceData = $advancesummaryModel->getAdvanceDetailsById($loginUserId);
				
				if($application_amount > $advanceData[0]['balance'])
				{
					$msgarray['amount'] = 'Balance amount is less than selected return amount.';
					$this->view->msgarray = $msgarray;
					$this->view->popup='';
				}else
				{
					$advancesModel = new Expenses_Model_Advances();
					$insertedId = $advancesModel->SaveAdvanceData($data, $where);
					
					
					$returned = $advanceData[0]['returned'];
					$bal = $advanceData[0]['balance'];
					$adva_summary_array = array('returned'=>($returned+$application_amount),'balance'=>($bal-$application_amount),'modifiedby'=>$loginUserId,'modifieddate'=>gmdate("Y-m-d H:i:s"));
					$adva_summary_where = array('employee_id=?'=>$loginUserId,'isactive=?'=>1);
					$adv_summary_id = $advancesummaryModel->SaveAdvanceData($adva_summary_array, $adva_summary_where);
					
					$this->view->eventact = 'returned';
					$close = 'close';
					$this->view->popup=$close;
				}

				
			}else
			{
				$messages = $advanceForm->getMessages();
				foreach ($messages as $key => $val)
				{
					foreach($val as $key2 => $val2)
					{
						$msgarray[$key] = $val2;
						break;
					}
				}
				$this->view->msgarray = $msgarray;
					
			}
		}

		
		$this->view->controllername = $controllername;
		$this->view->form = $advanceForm;
		$this->view->ermsg = '';
	}
}
	



