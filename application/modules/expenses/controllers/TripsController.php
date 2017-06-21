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
/**
 * @Name   Trips Controller
 *
 * @description
 *
 * This Trip controller contain actions related to Trip.
 *
 * 1. Display all Trip details.
 * 2. Save or Update Trip details.
 * 3. Delete Trip.
 * 4. View Trip details.
 *
 * @author sagarsoft
 * @version 1.0
 */
class Expenses_TripsController extends Zend_Controller_Action
{
	private $options;

	/**
	 * The default action - show the home page
	 */
	public function preDispatch()
	{
		
		/*$userModel = new Timemanagement_Model_Users();
		$checkTmEnable = $userModel->checkTmEnable();

		if(!$checkTmEnable){
			$this->_redirect('error');
		}*/
		
		//check Time management module enable
		if(!sapp_Helper::checkTmEnable())
			$this->_redirect('error');

	}

	public function init()
	{
		$this->_options= $this->getInvokeArg('bootstrap')->getOptions();

	}

	/**
	 * This method will display all the Trip details in grid format.
	 */
	public function indexAction()
	{
		
		$tripsModel = new Expenses_Model_Trips();		
		$call = $this->_getParam('call');
		if($call == 'ajaxcall')
		$this->_helper->layout->disableLayout();

		$view = Zend_Layout::getMvcInstance()->getView();
		$objname = $this->_getParam('objname');
		$refresh = $this->_getParam('refresh');
		$dashboardcall = $this->_getParam('dashboardcall');
		
		
		$data = array();
		$searchQuery = '';
		$searchArray = array();
		$tablecontent='';

		if($refresh == 'refresh')
		{
			if($dashboardcall == 'Yes')
			$perPage = DASHBOARD_PERPAGE;
			else
			$perPage = PERPAGE;
			$sort = 'DESC';$by = 'modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
			//echo "here";exit;
		}
		else
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'modifieddate';
			if($dashboardcall == 'Yes')
			$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
			else
			$perPage = $this->_getParam('per_page',PERPAGE);

			$pageNo = $this->_getParam('page', 1);
			/** search from grid - START **/
			$searchData = $this->_getParam('searchData');
			$searchData = rtrim($searchData,',');
			/** search from grid - END **/
		}
		//echo "here";exit;
		$dataTmp = $tripsModel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);
		
		
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
		
		//echo $call;exit;
		
	}

	/**
	 * This Action is used to Create/Update the trip details based on the trip id.
	 *
	 */
	public function editAction(){

		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$objName = 'trips';
		$id = $this->getRequest()->getParam('id');
		$isfrompopup = $this->getRequest()->getParam('isfrompopup');
		$isfrompopup = !empty($isfrompopup)?$this->getRequest()->getParam('isfrompopup'):'';
		$isfromview = $this->getRequest()->getParam('isfromview');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();
		$this->view->isfrompopup=$isfrompopup;
		$tripsForm = new Expenses_Form_Trips();
		$tripsModel = new Expenses_Model_Trips();
		try{
			if($id)
			{	//Edit Record...
				if(is_numeric($id) && $id>0)
				{
					$data = $tripsModel->getTripDetailsById($id);
					if(!empty($data) && $data != "norows")
					{
						$tripsForm->populate($data[0]);
						$tripsForm->submit->setLabel('Update');
						$this->view->form = $tripsForm;
						$this->view->controllername = $objName;
						$this->view->id = $id;
						$this->view->ermsg = '';
						$this->view->inpage = 'Edit';
						
						$siteconfigmodel = new Default_Model_Sitepreference();
					$configData = $siteconfigmodel->getActiveRecord();
					$currency = !empty($configData[0]['currency'])?$configData[0]['currency']:'';
					$this->view->currency=$currency;
					$tripsModel = new Expenses_Model_Trips();
					$reportingdata = $tripsModel->getReportingManagerAction($loginUserId );
					$reporting_manager = !empty($reportingdata[0]['reporting_manager_name'])?$reportingdata[0]['reporting_manager_name']:'';
					$this->view->report=$reporting_manager;
					
					$total_amount = 0;
								
				
				


					$this->view->data = $data;
					$this->view->id = $id;
					$this->view->isfromview = $isfromview;
					$this->view->total_amount = $total_amount;
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
			else
			{	//Add Record...
				$this->view->ermsg = '';
				$this->view->form = $tripsForm;
				$this->view->inpage = 'Add';
			}
		}
		catch(Exception $e)
		{
			$this->view->ermsg = 'nodata';
		}
		if($this->getRequest()->getPost()){
			if($tripsForm->isValid($this->_request->getPost())){
				$country_id	= NULL;
				$state_id	= NULL;
				$id = $this->_request->getParam('id');
				$description = $this->_request->getParam('description');
				$trip_name = $this->_request->getParam('trip_name');				
				$from_date	= $this->_request->getParam('from_date');
				$from_date = sapp_Global::change_date($from_date,'database');
				
				$to_date = $this->_request->getParam('to_date');
				$to_date = sapp_Global::change_date($to_date,'database');
				
				$date = gmdate("Y-m-d H:i:s");

				$data = array(
							'description'  => $description,
							'trip_name'  => $trip_name,							
							'from_date'  =>	$from_date,
							'to_date'  => $to_date,							
				);
				if($id!=''){
					$data['modifiedby'] = $loginUserId;
					$data['modifieddate'] = $date;
					$where = array('id=?'=>$id);
				}
				else
				{
					$data['createdby'] = $loginUserId;
					$data['createddate'] = $date;
					$data['modifieddate'] = $date;
					$data['isactive'] = 1;
					$where = '';
				}
				//echo "<pre>";print_r($data);exit;
				$Id = $tripsModel->saveOrUpdateTripsData($data, $where);
				if($Id == 'update')
				{
					//Record history
						$tripHistoryModel = new Expenses_Model_Triphistory();
						$date = gmdate("Y-m-d H:i:s");
						//$format = 'Associated with Trip %s';
						//$history = sprintf($format, '"Trip to Boston"');
						$history = 'Modified';
						$history_data = array(											
											'trip_id' => $id,
											'history' => $history,
											'createdby' => $loginUserId,
											'createddate'=> $date
										);
						$history_where = '';
						$historyId = $tripHistoryModel->saveOrUpdateTripHistory($history_data,$history_where); 
					//End
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Trip updated successfully."));
				}
				else
				{
					//Record history
						$tripHistoryModel = new Expenses_Model_Triphistory();
						$date = gmdate("Y-m-d H:i:s");
						//$format = 'Associated with Trip %s';
						//$history = sprintf($format, '"Trip to Boston"');
						$history = 'Created';
						$history_data = array(											
											'trip_id' => $Id,
											'history' => $history,
											'createdby' => $loginUserId,
											'createddate'=> $date
										);
						$history_where = '';
						$historyId = $tripHistoryModel->saveOrUpdateTripHistory($history_data,$history_where); 
					//End
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Trip added successfully."));
				}
					
				$this->_redirect('expenses/trips');
			}else
			{
				$messages = $tripsForm->getMessages();
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

	}

	/**
	 * This Action is used to view the Trip details based on the trip id.
	 *
	 */
	public function viewAction(){
	$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->_request->getParam('objid');

		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();
		$objName = 'trips';

		$tripsModel = new Expenses_Model_Trips();

		try
		{
			if(is_numeric($id) && $id>0)
			{
				
			
				$data = $tripsModel->getSingleTripDetailsById($id);

				$trip_single_data = $tripsModel->getTripDetailsById($id);
				
			
				if(!empty($data) && $data != "norows")
				{
					if($data[0]['status']=='S')
					{
						$data[0]['status']="Submitted";
					}
					elseif($data[0]['status']=='A')
					{
						$data[0]['status']="Approved";
					}	
					elseif($data[0]['status']=='R')
					{
						$data[0]['status']="Rejected";
					}	
					else
					{
						$data[0]['status']="Notsubmitted";
					}	
				//	NS-Notsubmitted,S-submitted,R-Rejected,A-Approved
					
						 
					$tripsmodel = new Expenses_Model_Trips();
					$configData = $tripsmodel->getApplicationCurrency();
					$currency = !empty($configData[0]['currencycode'])?$configData[0]['currencycode']:'';
					$this->view->currency=$currency;
					
					
					$tripsModel = new Expenses_Model_Trips();
					$reportingdata = $tripsModel->getReportingManagerAction($loginUserId );
					$reporting_manager = !empty($reportingdata[0]['reporting_manager_name'])?$reportingdata[0]['reporting_manager_name']:0;
					$this->view->report=$reporting_manager;
			
					$total_amount = 0;
					foreach($data as $trip_data)
					{
					
						$total_amount = $total_amount+$trip_data['application_amount'];

					}			
					
					
					$emp_summary_model = new Default_Model_Employee();
					$emp_det = $emp_summary_model->getEmp_from_summary($trip_single_data[0]['createdby']); //submit can be done by only owner of Expense
					$manager_id = $emp_det['reporting_manager'];
				
				

					$historyModel = new Expenses_Model_Triphistory();
					$historydata = $historyModel->getTripHistory($id);
					
					$this->view->data = $data;
					$this->view->id = $id;
					$this->view->total_amount = $total_amount;
					$this->view->historydata = $historydata;
					$this->view->loginUserId = $loginUserId;
					$this->view->manager_id = $manager_id;
					$this->view->ermsg = '';
					$this->view->controllername = $objName;
				}
				else
				{
					$data = $tripsModel->getsingleTripData($id);
					if($data[0]['status']=='S')
					{
						$data[0]['status']="Submitted";
					}
					elseif($data[0]['status']=='A')
					{
						$data[0]['status']="Approved";
					}	
					elseif($data[0]['status']=='R')
					{
						$data[0]['status']="Rejected";
					}	
					else
					{
						$data[0]['status']="Notsubmitted";
					}
					$this->view->id = $id;
					$this->view->loginUserId = $loginUserId;
					$this->view->data = $data;
					$this->view->ermsg = 'norecord';
					$this->view->controllername = $objName;
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

	/**
	 * This action is used to delete the client details based on the client id.
	 *
	 */
	public function deleteAction(){
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->_request->getParam('objid');
		$deleteflag= $this->_request->getParam('deleteflag');
		$messages['message'] = ''; $messages['msgtype'] = '';
		$messages['flagtype'] = '';
		$actionflag = 3;
		if($id)
		{
			$tripsModel = new Expenses_Model_Trips();
			
			$checkExpenses = $tripsModel->checkExpensesAndTrips($id);
			//echo "here";exit;
			if($checkExpenses == 0){
				$data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
				$data['modifiedby'] = $loginUserId;
				$where = array('id=?'=>$id);
				$id = $tripsModel->saveOrUpdateTripsData($data, $where);
				if($id == 'update')
				{
					$messages['message'] = 'Trip deleted successfully.';
					$messages['msgtype'] = 'success';
				}
				else
				{
					$messages['message'] = 'Trip cannot be deleted.';
					$messages['msgtype'] = 'error';
				}
			}else{
				$messages['message'] = 'Trip is in use. You cannot delete the trip';
				$messages['msgtype'] = 'error';
			}
		}
		else
		{
			$messages['message'] = 'Trip cannot be deleted.';$messages['msgtype'] = 'error';
		}
		if($deleteflag==1)
		{
			if(	$messages['msgtype'] == 'error')
			{
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("error"=>$messages['message'],"msgtype"=>$messages['msgtype'] ,'deleteflag'=>$deleteflag));
			}
			if(	$messages['msgtype'] == 'success')
			{
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>$messages['message'],"msgtype"=>$messages['msgtype'],'deleteflag'=>$deleteflag));
			}
			
		}
		$this->_helper->json($messages);

	}

	public function addpopupAction()
	{
		$msgarray = array();
		$emptyFlag = '';
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->getRequest()->getParam('id');

		$controllername = 'clients';
		$clientsForm = new Timemanagement_Form_Clients();
		$clientsModel = new Timemanagement_Model_Clients();
		$clientsForm->setAction(BASE_URL.'timemanagement/clients/addpopup');

		if($this->getRequest()->getPost()){
			if($clientsForm->isValid($this->_request->getPost())){
				$country_id	= NULL;
				$state_id	= NULL;
				$id = $this->_request->getParam('id');
				$address = $this->_request->getParam('address');
				$client_name = $this->_request->getParam('client_name');
				if($this->_request->getParam('country_id')){
					$country_id	= $this->_request->getParam('country_id');
				}

				$email	= $this->_request->getParam('email');
				$phone_no = $this->_request->getParam('phone_no');
				$poc = $this->_request->getParam('poc');
				$fax = $this->_request->getParam('fax');
				if($this->_request->getParam('state_id')){
					$state_id	= $this->_request->getParam('state_id');
				}
				$date = gmdate("Y-m-d H:i:s");

				$data = array(
							'address'  => $address,
							'client_name'  => $client_name,
							'country_id'  => $country_id,
							'email'  =>	$email,
							'phone_no'  => $phone_no,
							'poc'  => $poc,
							'fax'  => $fax,
							'state_id'  => $state_id,
				);

				$data['created_by'] = $loginUserId;
				$data['created'] = gmdate("Y-m-d H:i:s");
				$data['modified'] = gmdate("Y-m-d H:i:s");
				$data['is_active'] = 1;
				$where = '';


				$Id = $clientsModel->saveOrUpdateClientsData($data, $where);

				$clientsData = $clientsModel->fetchAll('is_active = 1','client_name')->toArray();

				$opt ='';
				foreach($clientsData as $record){
					$opt .= sapp_Global::selectOptionBuilder($record['id'], $record['client_name']);
				}
				$this->view->clientsData = $opt;
					
				$this->view->eventact = 'added';
				$close = 'close';
				$this->view->popup=$close;
			}else
			{
				$messages = $clientsForm->getMessages();
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
		$this->view->form = $clientsForm;
		$this->view->ermsg = '';

	}
	public function tripstatusAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$date = gmdate("Y-m-d H:i:s");
		$tripsModel = new Expenses_Model_Trips();	
		$status = $this->_request->getParam('status');
		$trip_id = $this->_request->getParam('trip_id');
		$where = array('id=?'=>$trip_id);
		$data = array('status'=>$status,'modifiedby'=>$loginUserId,'modifieddate'=>$date);
		
		$Id = $tripsModel->saveOrUpdateTripsData($data, $where);
		
		//update trip expenses status as trip status
		$expense_status = 'submitted';
		if($status=='S')
			$expense_status = 'submitted';
		else if($status=='R')
			$expense_status = 'rejected';
		else if($status=='A')
		$expense_status = 'approved';
		$expenseModel = new Expenses_Model_Expenses();	
		$expensedata = array('status'=>$expense_status,'modifiedby'=>$loginUserId,'modifieddate'=>$date);
		if($expense_status=='submitted')
			$expensewhere = array('trip_id=?'=>$trip_id,'isactive=?'=>1,'status!=?'=>'approved');
		else
			$expensewhere = array('trip_id=?'=>$trip_id,'isactive=?'=>1,'status!=?'=>'approved','manager_id=?'=>$loginUserId);
		$expenseId = $expenseModel->saveOrUpdateExpensesData($expensedata, $expensewhere);
		//echo $expenseId;die();
		
		
		
		//email notifications start
				$link = BASE_URL.'expenses/trips/view/id/'.$trip_id;
				$trip_details = $tripsModel->getTripDetailsById($trip_id);
		
				$emp_summary_model = new Default_Model_Employee();
				//send email to reporting manager while submitting expense
				$emp_details = $emp_summary_model->getEmp_from_summary($trip_details[0]['createdby']);
				$to_name = $emp_details['userfullname'];
				$emil_id = $emp_details['emailaddress'];
				$emp_to_details = $emp_summary_model->getEmp_from_summary($emp_details['reporting_manager']);
				$from_name = $emp_to_details['userfullname'];
				
				if($expense_status == 'submitted')
				{
					$emp_det = $emp_summary_model->getEmp_from_summary($loginUserId); 
					$managerDetails = $emp_summary_model->getEmp_from_summary($emp_det['reporting_manager']); 
					$emil_id = $managerDetails['emailaddress'];
					$to_name = $managerDetails['userfullname'];
					$from_name = $emp_det['userfullname'];
				}
				
				
				$base_url = 'http://'.$this->getRequest()->getHttpHost() . $this->getRequest()->getBaseUrl();
				$view = $this->getHelper('ViewRenderer')->view;
				$this->view->from_name = $from_name;
				$this->view->to_name = $to_name;
				$this->view->expense_name = $trip_details[0]['trip_name'];
				$this->view->base_url=$base_url;
				$this->view->status=$expense_status;
				$this->view->page='Trip';
				$this->view->url_link=$link;
				$text = $view->render('expensemailtemplates/expensestatus.phtml');
				$options['subject'] = APPLICATION_NAME.': Notification';
				$options['header'] = 'Greetings from Sentrifugo';
				$options['toEmail'] = $emil_id;
				$options['toName'] = $to_name;
				$options['message'] = $text;
				$result = sapp_Global::_sendEmail($options);
		
		//email notification end
		
		
		
		
		//Record history
			$tripHistoryModel = new Expenses_Model_Triphistory();
			$date = gmdate("Y-m-d H:i:s");
			//$format = 'Associated with Trip %s';
			//$history = sprintf($format, '"Trip to Boston"');
			$history = $expense_status;
			$history_data = array(											
								'trip_id' => $trip_id,
								'history' => $history,
								'createdby' => $loginUserId,
								'createddate'=> $date
							);
			$history_where = '';
			$historyId = $tripHistoryModel->saveOrUpdateTripHistory($history_data,$history_where); 
		//End
		
		sapp_ExpensesHelper::tripstatus($trip_id);
		$this->_helper->json(array('message'=>'success','status'=> 'Trip '.$status.' successfully.'));
	}

	public function deleteexpenseAction()
	{
	//	echo "das";die();
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->_request->getParam('objid');
		$expenseModel = new Expenses_Model_Expenses();
		$trip_id=$this->_getParam('trip_id');
		$expense_id=$this->_getParam('expense_id');
		//echo $expense_id;die();
		

		$data = array('trip_id'=>'','modifieddate'=>gmdate("Y-m-d H:i:s"));
		$data['modifiedby'] = $loginUserId;
		$where = array('id=?'=>$expense_id);
		$Id = $expenseModel->saveOrUpdateExpensesData($data, $where);
		$this->_helper->json(array('message'=>'success','status'=> 'Expense Deleted Successfully.'));	
		
		/* $receiptsModel = new Expenses_Model_Receipts();
		$dataTmp = $receiptsModel->getReceipts();	
		$this->view->dataArray = $dataTmp; */
	}

	
	//for downloading pdf
    public function downloadtrippdfAction()
    {		
        $this->_helper->layout->disableLayout();
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
           $loginUserId = $auth->getStorage()->read()->id;
        }
       	$id = $this->getRequest()->getParam('id');		
		$objName = 'trips';

		$tripsModel = new Expenses_Model_Trips();
        if(!is_numeric($id))
        {
            return false;
        }
        else
        {
           // $data = $tripsModel->getTripDetailsById($id);
			$data = $tripsModel->getSingleTripDetailsById($id);
				
			if(!empty($data) && $data != "norows")
			{
				
					if($data[0]['status']=='S')
					{
						$data[0]['status']="Submitted";
					}
					elseif($data[0]['status']=='A')
					{
						$data[0]['status']="Approved";
					}	
					elseif($data[0]['status']=='R')
					{
						$data[0]['status']="Rejected";
					}	
					else
					{
						$data[0]['status']="Notsubmitted";
					}	
		
				
				
				$tripsmodel = new Expenses_Model_Trips();
					$configData = $tripsmodel->getApplicationCurrency();
					$currency = !empty($configData[0]['currencycode'])?$configData[0]['currencycode']:'';
				
				
				$reportingdata = $tripsModel->getReportingManagerAction($loginUserId );
				$reporting_manager = !empty($reportingdata[0]['reporting_manager_name'])?$reportingdata[0]['reporting_manager_name']:0;
				
		
				$total_amount = 0;
				foreach($data as $trip_data)
				{
				
					$total_amount = $total_amount+$trip_data['application_amount'];

				}			
			
				$appText = utf8_encode(substr($data[0]['from_date'],0,1)).$data[0]['to_date'];
				//render view page as text
				$view = $this->getHelper('ViewRenderer')->view;
				$this->view->currency=$currency;
				$this->view->report=$reporting_manager;
				$this->view->data = $data;
				$this->view->id = $id;
				$this->view->total_amount = $total_amount;
				$this->view->ermsg = '';				
				$text = $view->render('trips/downloadtrippdf.phtml');
				//generating file name
				/* $file_name_params_array = array($data[0]['trip_name'],$data[0]['from_date'],$data[0]['to_date'],$appText);
				$file_name = $this->_helper->PdfHelper->generateFileName($file_name_params_array); */
				$file_name = $data[0]['trip_name'];
				//mpdf
				require_once 'MPDF57/mpdf.php';
				$mpdf=new mPDF('', 'A4', 14, '', 10, 10, 12, 12, 6, 6);
				$mpdf->SetDisplayMode('fullpage');
				
				$mpdf->list_indent_first_level = 0;
				$mpdf->SetDisplayMode('fullpage');
				$mpdf->pagenumPrefix = 'Generated using Sentrifugo'.str_repeat(" ",72);
				$mpdf->pagenumSuffix = '';
				$mpdf->nbpgPrefix = ' out of ';
				$mpdf->nbpgSuffix = '';
				$mpdf->SetFooter('{PAGENO}{nbpg}');
				$mpdf->AddPage();
				$mpdf->WriteHTML($text);
				$mpdf->Output((!empty($file_name)?$file_name:'-trip').'.pdf','D');
				exit;				
			}
			
        }
    }

}

