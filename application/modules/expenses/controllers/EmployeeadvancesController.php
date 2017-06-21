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

class Expenses_EmployeeadvancesController extends Zend_Controller_Action
{
	private $options;

	/**
	 * The default action - show the home page
	 */
	public function preDispatch()
	{

		
		
	}

	public function init()
	{
		$this->_options= $this->getInvokeArg('bootstrap')->getOptions();

	}
	/**
	 * default action
	 */
	public function indexAction()
	{
	
		$employeeadvancesmodel = new Expenses_Model_Employeeadvances();
		$call = $this->_getParam('call');
		if($call == 'ajaxcall')
		$this->_helper->layout->disableLayout();

		$view = Zend_Layout::getMvcInstance()->getView();
		$objname = $this->_getParam('objname');
		$refresh = $this->_getParam('refresh');
		$dashboardcall = $this->_getParam('dashboardcall',null);
		$data = array();		$searchQuery = '';		$searchArray = array();		$tablecontent='';

		if($refresh == 'refresh')
		{
			if($dashboardcall == 'Yes')
			$perPage = DASHBOARD_PERPAGE;
			else
			$perPage = PERPAGE;

			$sort = 'DESC';$by = 'c.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';
			$searchArray = array();
		}
		else
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			if($this->_getParam('by')=='userfullname')
				$by='u.userfullname';
			else
				$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'c.modifieddate';
			if($dashboardcall == 'Yes')
			$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
			else
			$perPage = $this->_getParam('per_page',PERPAGE);
			$pageNo = $this->_getParam('page', 1);
			$searchData = $this->_getParam('searchData');
			$searchData = rtrim($searchData,',');
			//echo "<pre/>"; print_r($searchData); 
		}
		$dataTmp = $employeeadvancesmodel->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall);
		
		
	
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
		
		
	
	}
	
		/**
	 * This Action is used to Create/Update the employee advances details based on the  id.
	 *
	 */
	public function editAction()
	{ 
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
		 	$loginUserId = $auth->getStorage()->read()->id;
		}
		
		$objName = 'employeeadvances';$emptyFlag=0;
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();

		$advancesForm = new Expenses_Form_Advance();
		$employeeadvancesModel = new Expenses_Model_Employeeadvances();


		$usersModel = new Expenses_Model_Advances();
		$msgarray = array();
	    $usersData = $usersModel->getUserList($loginUserId );

		if(sizeof($usersData) > 0)
		{
			foreach ($usersData as $user){
				$advancesForm->to_id->addMultiOption($user['id'],utf8_encode($user['userfullname']));
				
			}

		}else
		{
			$msgarray['from_id'] = 'employee are not configured yet.';
			$emptyFlag++;
		}
		
		
		$tripsmodel = new Expenses_Model_Trips();
		$configData = $tripsmodel->getApplicationCurrency();
		
		$expensesmodel = new Expenses_Model_Expenses();
		$currencyData = $expensesmodel->getCurrencyList();
		//echo "<pre/>"; print_r($currencyData ); 
		$msgarray['currency_id']='';
		if(sizeof($currencyData) > 0)
		{
			
			foreach ($currencyData as $currency){
				$advancesForm->currency_id->addMultiOption($currency['id'],utf8_encode($currency['currencycode']));
			}
			if(empty($configData)){	
				$msgarray['currency_id'] = 'Default currency is not selected yet.';
			}
			

		}else
		{
			$msgarray['currency_id'] = 'Currency are not configured yet.';
			//$emptyFlag++;
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
			
		
		
		//echo "<pre/>"; print_R($configData ); 
		$currency="";
		if(!empty($configData) && (isset($configData[0]['currencycode'])))
		{
		$currency = $configData[0]['currencycode'];
		}
		$advancesForm->currency_id->setValue(!empty($configData[0]['currencyid'])?$configData[0]['currencyid']:'');
	
		$this->view->currency=$currency;
		$this->view->currencyid=!empty($configData[0]['currencyid'])?$configData[0]['currencyid']:'';
		
		 
		
		$this->view->msgarray = $msgarray;
		$this->view->emptyFlag = $emptyFlag;
	
		try
		{

			if($id)
			{	
			
				if(is_numeric($id) && $id>0)
				{
					
					$advancesForm = new Expenses_Form_Advance();
					$auth = Zend_Auth::getInstance(); 
					if($auth->hasIdentity()){
						$loginUserId = $auth->getStorage()->read()->id;
					
					}
						$employeeadvancesModel = new Expenses_Model_Employeeadvances();
						$dataarray = $employeeadvancesModel->getsingleEmployeeadvancesData($id);
					
						$data = $dataarray[0];

					if(!empty($data) && $data != "norows")
					{
						
						
						
						
				$projectModel = new Timemanagement_Model_Projects();
				$projectData = $projectModel->getEmployeeProjects(intval($data['to_id']));
				$project_array = array();
				if(count($projectData) > 0)
				{
					foreach($projectData as $project)
					{
						$advancesForm->project_id->addMultiOption($project['id'],utf8_encode($project['project_name']));
						
					}
				}	
					
							$advancesForm->populate($data);
							$advancesForm->submit->setLabel('Update');
								$this->view->data = $data;	
								//echo "<pre/>"; print_r($data); exit;

		$paymentmodemodel = new Expenses_Model_Paymentmode();
		$paymentmodeData = $paymentmodemodel->getPaymentList();
		if(sizeof($paymentmodeData) > 0)
		{
			foreach ($paymentmodeData as $payment_mode){
				    $advancesForm->payment_mode_id->addMultiOption($payment_mode['id'],$payment_mode['payment_method_name']);
					
			}

		}						
					
		$expensesmodel = new Expenses_Model_Expenses();
		$currencyData = $expensesmodel->getCurrencyList();
		if(sizeof($currencyData) > 0)
		{
			foreach ($currencyData as $currency){
				$advancesForm->currency_id->addMultiOption($currency['id'],$currency['currencycode']);
			}

		}
			
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
					
					foreach($projects as $dstate)
					{
						$project_array[$dstate['id']] = $dstate['project_name'];
					}
				}
				$advancesForm->project_id->addMultiOptions(array(''=>'Select State')+$project_array);
			}
			if(isset($_POST['cal_amount']) && $_POST['cal_amount']!='')
			{
				$this->view->cal_amount = $_POST['cal_amount'];
			}
			if($advancesForm->isValid($this->_request->getPost())){
				if($msgarray['currency_id']=='')
				{
				
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
				$cal_amount = $this->_request->getParam('cal_amount');
				$app_amount="";

				$siteconfigmodel = new Default_Model_Sitepreference();
				$configData = $siteconfigmodel->getActiveRecord();
				$application_currency='';
				if(!empty($configData) && (isset($configData[0]['currencyid'])))
				{
					$application_currency = $configData[0]['currencyid'];
				}
				
				if(!empty($id))
				{
					
					$employeeadvancesModel = new Expenses_Model_Employeeadvances();
					$employeeadvance_exist_data = $employeeadvancesModel->getsingleEmployeeadvancesData($id);
					$app_amount=$employeeadvance_exist_data[0]['application_amount'];
					
				}
					
				$application_amount=null;
				if($cal_amount!=0)
				{
				
					$application_amount=$cal_amount*$amount;
				}
				
				if( $app_amount!="" && $cal_amount==0  && ($currency==$employeeadvance_exist_data[0]['currency_id']))
				{
					$application_amount=$app_amount;
				}
				
				$curId = !empty($configData[0]['currencyid'])?$configData[0]['currencyid']:'';
				if($currency==$curId)
				{
					$application_amount=$amount;
				}
				
				
				
				//echo $application_amount;exit;
				$date = new Zend_Date();
				$data = array('to_id'=>$to_id,
							  'project_id'=>$project_id,
				              'currency_id'=>$currency,
							  'amount'=>$amount,
							  'payment_mode_id'=>$payment_mode,
							  'payment_ref_number'=>$paymentref,
						      'application_currency_id'  => $application_currency,
						      'application_amount'=>$application_amount,
						      'advance_conversion_rate'=>$cal_amount,
							  'description'=>$description,
				              'createdby'=>$loginUserId,
							  'from_id'=>$loginUserId,
							  'createddate' => gmdate("Y-m-d H:i:s"),
							  'isactive'=>1
							
				);

                   
						if($id!=''){
							
									$data['modifiedby'] = $loginUserId;
									$data['modifieddate'] = gmdate("Y-m-d H:i:s");
									$where = array('id=?'=>$id);  
									
								}
								else
								{
									$data['createdby'] = $loginUserId;
									$data['createddate'] = gmdate("Y-m-d H:i:s");
									$data['modifieddate'] = gmdate("Y-m-d H:i:s");
									$data['isactive'] = 1;
									$where = '';
								
								}
					
					
				

				
				$employeeadvancesModel = new Expenses_Model_Employeeadvances();
				$insertedId = $employeeadvancesModel->saveOrUpdateAdvanceData($data, $where);
				
				
				
				//insert advance details

				
					//check if employee has already advance
					$advancesummary = new Expenses_Model_Advancesummary();
					$isRecordExist = $advancesummary->getAdvanceDetailsById($to_id);
					
					$summerydata = array();
					if(count($isRecordExist)>0)
					{
						$totalsum = $isRecordExist[0]['total'];
						$balence = $isRecordExist[0]['balance'];
						if($app_amount>$application_amount)
						{
							$totalsum = $isRecordExist[0]['total'] - ($app_amount - $application_amount);
							$balence = $isRecordExist[0]['balance']-($app_amount - $application_amount);
						}else if($app_amount<$application_amount)
						{
							$totalsum = $isRecordExist[0]['total'] + ($application_amount - $app_amount);
							$balence = $isRecordExist[0]['balance']+($application_amount - $app_amount);
						}
						
						$summerydata = array('total'=>$totalsum,
						'balance'=>$balence,
									 'modifiedby'=> $loginUserId,
									 'modifieddate' =>gmdate("Y-m-d H:i:s")
								   );
						$summeryWhere = array('employee_id=?'=>$to_id); 
							
					}else
					{
						$totalsum  = $application_amount;
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

				$this->_redirect('expenses/employeeadvances');
				}else{
					$msgarray['currency_id'] = 'Default currency is not selected yet.';
					$this->view->msgarray = $msgarray;
				}
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
	/**
	 * This action is used to delete the employee advances details based on the  id.
	 *
	 */
		public function deleteAction(){

		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->_request->getParam('id');
		$emp_id = $this->_request->getParam('emp_id');
		$messages['message'] = ''; $messages['msgtype'] = '';
		$messages['flagtype'] = '';
		$actionflag = 3;
		if($id)
		{
			
			$employeeadvancesModel = new Expenses_Model_Employeeadvances();

			//check employee has amount of select amount
			$getEmployeeBalance = $employeeadvancesModel->getEmpAdvanceSummary($emp_id);
			$total = !empty($getEmployeeBalance[0]['balance'])?$getEmployeeBalance[0]['balance']:0;
			
			$employeeAdvanceData = $employeeadvancesModel->getsingleEmployeeadvancesData($id,$type='advance');
			
			$employeebalance = !empty($employeeAdvanceData[0]['application_amount'])?$employeeAdvanceData[0]['application_amount']:0;
			
			if($total >= $employeebalance) //if employee has suffiecient amount to delete advance
			{
				$data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
				$data['modifiedby'] = $loginUserId;
				$where = array('id=?'=>$id);
				$id = $employeeadvancesModel->saveOrUpdateAdvanceData($data, $where);
				if($id == 'update')
				{
					//remove deleted advance amount from employee total advance amount
					$advancesummary = new Expenses_Model_Advancesummary();
					$emp_advance_summery = $advancesummary->getAdvanceDetailsById($emp_id);
					$totalsum = $emp_advance_summery[0]['total']-$employeebalance;
					$balence = $emp_advance_summery[0]['balance']-$employeebalance;
					$summerydata = array('total'=>$totalsum,
									'balance'=>$balence,
									 'modifiedby'=> $loginUserId,
									 'modifieddate' =>gmdate("Y-m-d H:i:s")
								   );
					$summeryWhere = array('employee_id=?'=>$emp_id); 
					$insertSummey = $advancesummary->SaveAdvanceData($summerydata, $summeryWhere);
					
					$messages['message'] = 'advance deleted successfully.';
					$messages['msgtype'] = 'success';
				}
				else
				{
					$messages['message'] = 'advance cannot be deleted.';
					$messages['msgtype'] = 'error';
				}
			}else
			{
				$messages['message'] = 'advance cannot be deleted already used.';
				$messages['msgtype'] = 'error';
			}
				
			
			 
			
		}
		else
		{
			$messages['message'] = 'advance cannot be deleted.';
			$messages['msgtype'] = 'error';
		}
		$this->_helper->json($messages);

	}
	
	
		public function viewAction(){

		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();
		$objName = 'employeeadvances';
		
		$employeeadvancesModel = new Expenses_Model_Employeeadvances();

		try
		{
			if(is_numeric($id) && $id>0)
			{
				
				$data = $employeeadvancesModel->getIndividualEmployeeadvancesData($id);
				$exp_advance_summary=$employeeadvancesModel->getEmpAdvanceSummary($id);
				$exp_advance_return=$employeeadvancesModel->getEmpAdvanceReturn($id);
			
				if(!empty($exp_advance_summary))
				{
					$this->view->returned=$exp_advance_summary[0]['returned'];
					$this->view->utilized=$exp_advance_summary[0]['utilized'];
					$this->view->balance=$exp_advance_summary[0]['balance'];
				}
		

				if(!empty($data) && $data != "norows")
				{
								
					$auth = Zend_Auth::getInstance();
					if($auth->hasIdentity()){
						$loginUserId = $auth->getStorage()->read()->id;
					}
					
					$objName = 'employeeadvances';$emptyFlag=0;
					
					$this->view->loginUserId=$loginUserId; 
					
					$tripsmodel = new Expenses_Model_Trips();
					$configData = $tripsmodel->getApplicationCurrency();
					$currency = !empty($configData[0]['currencycode'])?$configData[0]['currencycode']:'';
					$this->view->currency=$currency;
					
					
					$total_amount = 0;
					foreach($data as $employeadv_data)
					{
					
						$total_amount = $total_amount+$employeadv_data['application_amount'];

					}			
				
					$this->view->data = $data;
					$this->view->id = $id;
					$this->view->total_amount = $total_amount;
					$this->view->exp_advance_return =	$exp_advance_return;
					$this->view->ermsg = '';
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
		catch(Exception $e)
		{
			$this->view->ermsg = 'nodata';
		}
	}
	
	
	
}	