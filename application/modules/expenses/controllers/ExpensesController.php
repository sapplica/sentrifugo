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
 * @Name   Expenses Controller
 *
 * @description
 *
 * This Expense controller contain actions related to Expense.
 *
 * 1. Display all Expense details.
 * 2. Save or Update Expense details.
 * 3. Delete Expense.
 * 4. View Expense details.
 *
 * @author sagarsoft
 * @version 1.0
 */
class Expenses_ExpensesController extends Zend_Controller_Action
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
		/* if(!sapp_Helper::checkTmEnable())
			$this->_redirect('error'); */
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('addreceiptimage', 'html')->initContext();
		$ajaxContext->addActionContext('listreportingmangers', 'html')->initContext();
		$ajaxContext->addActionContext('viewmoremanagers', 'html')->initContext();

		$ajaxContext->addActionContext('getcurrencyname', 'json')->initContext();

		$ajaxContext->addActionContext('uploadedfiles', 'html')->initContext();


	}

	public function init()
	{
		$this->_options= $this->getInvokeArg('bootstrap')->getOptions();

	}

	/**
	 * This method will display all the Expense details in grid format.
	 */
	public function indexAction()
	{		
		$expensesModel = new Expenses_Model_Expenses();		
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
		$dataTmp = $expensesModel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);
		
		
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
		
		//echo $call;exit;
		
	}

	/**
	 * This Action is used to Create/Update the expense details based on the expense id.
	 *
	 */
	public function editAction(){

		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$objName = 'expenses';
		$msgarray = '';
		$id = $this->getRequest()->getParam('id');
		$trip_id_from_view = $this->getRequest()->getParam('trip_id_from_view');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();

		$expensesForm = new Expenses_Form_Expenses();
		$expensesModel = new Expenses_Model_Expenses();
		
		
		//Start Categories list
		$expensesCategoriesModel = new Expenses_Model_Categories();
		$expenseCategoryData = $expensesCategoriesModel->getExpensesCategoriesList();
		if(sizeof($expenseCategoryData) > 0)
		{
			foreach ($expenseCategoryData as $category){
				$expensesForm->category_id->addMultiOption($category['id'],utf8_encode($category['expense_category_name']));
			}

		}else
		{
			$msgarray['category_id'] = 'Categories are not configured yet.';
			//$emptyFlag++;
		}
		//End
		
		
		$popConfigPermission = array();
		array_push($popConfigPermission,'client');
		array_push($popConfigPermission,'currency');
		$this->view->popConfigPermission = $popConfigPermission;
		
		
		$tripsmodel = new Expenses_Model_Trips();
		$configData = $tripsmodel->getApplicationCurrency();
	
		//Start Currency list
		$expensesmodel = new Expenses_Model_Expenses();
		$currencyData = $expensesmodel->getCurrencyList();
		//echo "<pre/>"; print_r($currencyData ); 
		if(sizeof($currencyData) > 0)
		{
			foreach ($currencyData as $currency){
					$expensesForm->expense_currency_id->addMultiOption($currency['id'],utf8_encode($currency['currencycode']));
			}
			if(empty($configData))
			{
				$msgarray['expense_currency_id'] = 'Default currency is not configured yet.';
			}
		}else
		{
			$msgarray['expense_currency_id'] = 'Currency are not configured yet.';
			//$emptyFlag++;
		}
		//End
		
		//Start Payment methods
		$paymentMethodsModel = new Expenses_Model_Paymentmode();
		$paymentMethodsData = $paymentMethodsModel->getPaymentList();
		if(sizeof($paymentMethodsData) > 0)
		{
			foreach ($paymentMethodsData as $payment){
				$expensesForm->expense_payment_id->addMultiOption($payment['id'],utf8_encode($payment['payment_method_name']));
			}

		}else
		{
			$msgarray['expense_payment_id'] = 'Payment methods are not configured yet.';
			//$emptyFlag++;
		}
		//End
		
		//Start Trips data
		$tripsModel = new Expenses_Model_Trips();
		$tripsData = $tripsModel->getTripsList();
		if(sizeof($tripsData) > 0)
		{
			foreach ($tripsData as $trip){
				$expensesForm->trip_id->addMultiOption($trip['id'],utf8_encode($trip['trip_name']));
			}

		}
		//End
		
		//Start Get advance of an employ
		$advancesummaryModel = new Expenses_Model_Advancesummary();
		$advanceData = $advancesummaryModel->getAdvanceDetailsById($loginUserId);
		
		//echo "<pre>";print_r($advanceData);exit;

		if(sizeof($advanceData) > 0)
		{
			$expensesForm->is_from_advance->addMultiOption('','Select');
			foreach ($advanceData as $advance){
				$expensesForm->is_from_advance->addMultiOption($advance['id'],$advance['balance']);
			}

		}else
		{
			
			$expensesForm->is_from_advance->addMultiOption('','No Advance');
			//$msgarray['is_from_advance'] = 'Advance not configured yet.';
		}		
		//End
		
		//Projects data 
		$projectModel = new Timemanagement_Model_Projects();
		$base_projectData = $projectModel->getEmpProjects($loginUserId);
	
		if(sizeof($base_projectData) > 0)
		{
			foreach ($base_projectData as $base_project){
				$expensesForm->project_id->addMultiOption($base_project['id'],$base_project['project_name']);
			}

		}		
		//End
		
		
		//if receipt selected from computer validation
		$file_orginal_name_array = array();
		$file_original_names = $this->getRequest()->getParam('file_original_names');
		
		if($file_original_names!='')
		{
			$file_orginal_name_array = explode(',',$file_original_names);
		}
		
		$this->view->file_orginal_name_array = $file_orginal_name_array;
		
		
		//if receipt selected from already existing reciipts validation 
		$post_receipt_ids = $this->getRequest()->getParam('post_receipt_ids');
		
		$post_receipt_array = array();
		$existing_recipt_array = array();
		if($post_receipt_ids!='')
		{
			$post_receipt_array = explode(',',$post_receipt_ids);
		}
		if(count($post_receipt_array)>0)
		{
			
			foreach($post_receipt_array as $rID)
			{
				$receiptsModel = new Expenses_Model_Receipts();
				$data_rec = $receiptsModel->getReceiptData($rID);
				foreach($data_rec as $receiptId)
				{
					$existing_recipt_array[$rID] = $receiptId;
				}
			}
			
		}
		$this->view->existing_recipt_array = $existing_recipt_array;
		
		$currency="";
		if(!empty($configData) && (isset($configData[0]['currencycode'])))
		{
			$currency = $configData[0]['currencycode'];
		}
		$expensesForm->expense_currency_id->setValue(!empty($configData[0]['currencyid'])?$configData[0]['currencyid']:'');
	
		$this->view->currency=$currency;
		$this->view->currencyid=(!empty($configData[0]['currencyid']))?$configData[0]['currencyid']:"";
		
		
		$receiptIdsArray = array();
		$expense_exist_data = array();
		try{
			if($id)
			{	
					//Edit Record...
				if(is_numeric($id) && $id>0)
				{
					$expense_exist_data = $expensesModel->getExpenseDetailsById($id);
					
					if(!empty($expense_exist_data) && $expense_exist_data != "norows")
					{
						$expensesForm->populate($expense_exist_data[0]);
						$expensesForm->submit->setLabel('Update');
						$this->view->form = $expensesForm;
						$this->view->controllername = $objName;
						$this->view->id = $id;
						$this->view->ermsg = '';
						$this->view->inpage = 'Edit';
						//echo "<pre>";print_r($msgarray);exit;
						$this->view->msgarray = $msgarray;
						$this->view->trip_id_from_view = $trip_id_from_view;
						
						$receiptsModel = new Expenses_Model_Receipts();
						$getReceiptData = $receiptsModel->getExpenseReceipts($id);
						$this->view->getReceiptData = $getReceiptData;
						$this->view->data = $expense_exist_data;
						if(count($getReceiptData)>0)
						{
							foreach($getReceiptData as $receiptsData)
							{
								$receiptIdsArray[] = $receiptsData['id'];
							}
							
						}
						
						/*$currencyModel = new Default_Model_Currency();
						$currencyData = $currencyModel->getCurrencyDataByID($expense_exist_data[0]['expense_currency_id']);
						$currencyname="";
						if(!empty($currencyData))
						{
							$currencyname="1 ".$currencyData[0]['currencyname']."=";
						}
						
						
						$this->view->currencyname = $currencyname;*/
						
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
				$this->view->form = $expensesForm;
				$this->view->inpage = 'Add';
			}
		}
		catch(Exception $e)
		{
			$this->view->ermsg = 'nodata';
		}
		if($this->getRequest()->getPost()){
			if(isset($_POST['cal_amount']) && $_POST['cal_amount']!='')
			{
				$this->view->cal_amount = $_POST['cal_amount'];
			}
		
			if($expensesForm->isValid($this->_request->getPost())){
				
				$country_id	= NULL;
				$state_id	= NULL;
				$id = $this->_request->getParam('id');
				$expense_amount = $this->_request->getParam('expense_amount');
				$expense_name = $this->_request->getParam('expense_name');

				$category_id = $this->_request->getParam('category_id');
				$project_id = $this->_request->getParam('project_id');
				$expense_currency_id = $this->_request->getParam('expense_currency_id');
				$is_reimbursable = $this->_request->getParam('is_reimbursable');
				
				$expense_payment_id = $this->_request->getParam('expense_payment_id');
				$trip_id = $this->_request->getParam('trip_id');
				$expense_payment_ref_no = $this->_request->getParam('expense_payment_ref_no');
				$description = $this->_request->getParam('description');
				$cal_amount = $this->_request->getParam('cal_amount');
				$app_amount="";
				
				if(!empty($id))
				{
				$expense_exist_data = $expensesModel->getExpenseDetailsById($id);
				$app_amount=$expense_exist_data[0]['application_amount'];
				}
			
				$application_amount=null;
				if($cal_amount!=0)
				{
				
				$application_amount=$cal_amount*$expense_amount;
				}
				
				if( $app_amount!="" && $cal_amount==0  && ($expense_currency_id==$expense_exist_data[0]['expense_currency_id']))
				{
					$application_amount=$app_amount;
				}
				
				$curId = !empty($configData[0]['currencyid'])?$configData[0]['currencyid']:'';
				if($expense_currency_id==$curId)
				{
					$application_amount=$expense_amount;
				}
				if($application_amount=='')
				{
					$msgarray['expense_amount'] = 'Convert Currency.';
					$this->view->msgarray = $msgarray;
				}
				
				$siteconfigmodel = new Default_Model_Sitepreference();
				$configData = $siteconfigmodel->getActiveRecord();
			$application_currency='';
				if(!empty($configData) && (isset($configData[0]['currencyid'])))
				{
			       $application_currency = $configData[0]['currencyid'];
				}
				
				
				
				$is_from_advance = $this->_request->getParam('is_from_advance');
				if(trim($is_from_advance))
					$is_from_advance = 1;
				else
					$is_from_advance = 0;
				
				$expense_date = $this->_request->getParam('expense_date');
				$expense_date = sapp_Global::change_date($expense_date,'database');
				
				
				$emp_summary_model = new Default_Model_Employee();
				$emp_det = $emp_summary_model->getEmp_from_summary($loginUserId); //submit can be done by only owner of Expense
				$manager_id = $emp_det['reporting_manager'];
				
				$date = gmdate("Y-m-d H:i:s");

				$data = array(
							'expense_amount'  => $expense_amount,
							'expense_name'  => $expense_name,							
							'expense_date'  =>	$expense_date,
							'category_id'  => $category_id,							
							'project_id'  => $project_id,
							'expense_currency_id'  => $expense_currency_id,
							'expense_conversion_rate'  => $cal_amount,
						    'application_amount'=>$application_amount,
						    'application_currency_id'  => $application_currency,
							'is_reimbursable' => $is_reimbursable,
							'expense_payment_id'  => $expense_payment_id,
							'trip_id'  => $trip_id,
							'expense_payment_ref_no' => $expense_payment_ref_no,
							'is_from_advance' => $is_from_advance,
							'description' => $description,
							'manager_id' => $manager_id,
				);
				
				// if project is selected update client id in expenses
				if($project_id!='' && $project_id>0)
				{
					$projectsDetails = $expensesModel->getProjectClient($project_id);
					$data['client_id'] = $projectsDetails[0]['client_id'];
				}
				
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
				
				//Update advance summary based on expense amount if it is from advance.
				if($is_from_advance == 1)
				{
					$advanceData = $advancesummaryModel->getAdvanceDetailsById($loginUserId);
					if($application_amount > $advanceData[0]['balance'])
					{
						//$msgarray['expense_amount'] = 'Expense amount is more than selected advance.';
						//$this->view->msgarray = $msgarray;
						$change_utilized_amt = $advanceData[0]['utilized'] + $advanceData[0]['balance'];							
						$change_balance_amt = 0;
						$adva_summary_array = array(
								'utilized'  => $change_utilized_amt,
								'balance'  => $change_balance_amt,							
								'modifiedby'  => $loginUserId,
								'modifieddate' => $date,									
							);	
					}
					else{
						if(count($expense_exist_data)>0){
							$adva_summary_array = array(
									'utilized'  => $advanceData[0]['utilized'],
									'balance'  => $advanceData[0]['balance'],							
									'modifiedby'  => $loginUserId,
									'modifieddate' => $date,									
								);
							//if expense old amount is greater than now given amount 
							if($expense_exist_data[0]['application_amount'] > $application_amount){
								$change_utilized_amt = $advanceData[0]['utilized'] - ($expense_exist_data[0]['application_amount'] - $application_amount);
								
								$change_balance_amt = $advanceData[0]['balance'] + ($expense_exist_data[0]['application_amount'] - $application_amount);
								
								$adva_summary_array = array(
									'utilized'  => $change_utilized_amt,
									'balance'  => $change_balance_amt,							
									'modifiedby'  => $loginUserId,
									'modifieddate' => $date,									
								);
							}
							else if($expense_exist_data[0]['application_amount'] < $application_amount){//if expense old amount is less than now given amount 
							
								$change_utilized_amt = $advanceData[0]['utilized'] + ($application_amount - $expense_exist_data[0]['application_amount']);
								
								$change_balance_amt = $advanceData[0]['balance'] - ($application_amount - $expense_exist_data[0]['application_amount']);
								
								$adva_summary_array = array(
									'utilized'  => $change_utilized_amt,
									'balance'  => $change_balance_amt,							
									'modifiedby'  => $loginUserId,
									'modifieddate' => $date,									
								);
							}
							
						}
						else{
							$change_utilized_amt = $advanceData[0]['utilized'] + $application_amount;							
							$change_balance_amt = $advanceData[0]['balance'] - $application_amount;
							$adva_summary_array = array(
								'utilized'  => $change_utilized_amt,
								'balance'  => $change_balance_amt,							
								'modifiedby'  => $loginUserId,
								'modifieddate' => $date,									
							);							
						}
						
						
					}
					$adva_summary_where = array('id=?'=>$advanceData[0]['id']);
					$adv_summary_id = $advancesummaryModel->SaveAdvanceData($adva_summary_array, $adva_summary_where);
				} 				
				//End
				if($msgarray == ''){
					
					$result_Id = $expensesModel->saveOrUpdateExpensesData($data, $where);				
				
					if($result_Id == 'update')
					{
						$expenseId = $id;
					}else
					{
						$expenseId = $result_Id;
					}		

					
					//update advance amount in expense table
					if($is_from_advance == 1)
					{
						$advance_amount = $application_amount;
						if($application_amount > $advanceData[0]['balance'])
						{
							$advance_amount = $advanceData[0]['balance'];
						}
						$advance_amount_array = array('advance_amount'=>$advance_amount);
						$adva_amount_where = array('id=?'=>$expenseId);
						$expensesModel->saveOrUpdateExpensesData($advance_amount_array, $adva_amount_where);
					}
					
					
					//add expense receipts START
				
					$receiptsModel = new Expenses_Model_Receipts();

					//if receipt is already exist
					
					$post_receipt_ids = $this->getRequest()->getParam('post_receipt_ids');
					$receiptIdArray=array();
					if($post_receipt_ids!='')
					{
						$receiptIdArray = explode(',',$post_receipt_ids);
						foreach($receiptIdArray as $receipt_id)
						{
							$date = gmdate("Y-m-d H:i:s");
							$receipt_data['expense_id'] = $expenseId;
							$receipt_data['modifieddate'] = $date;
							$receipt_data['modifiedby'] = $loginUserId;
							$receipt_where = array('id=?'=>$receipt_id);
							
							$receiptsModel->updateReceiptsData($receipt_data,$receipt_where);
						}
						
					}
					$deleteReceptsArray = array_diff($receiptIdsArray,$receiptIdArray);
					if(count($deleteReceptsArray)>0)
					{
						foreach($deleteReceptsArray as $rID)
						{
							$date = gmdate("Y-m-d H:i:s");
							$receipt_delete_data['expense_id'] = $expenseId;
							$receipt_delete_data['modifieddate'] = $date;
							$receipt_delete_data['modifiedby'] = $loginUserId;
							$receipt_delete_data['isactive'] = 0;
							$receipt_where_delete = array('id=?'=>$rID);
							$receiptsModel->updateReceiptsData($receipt_delete_data,$receipt_where_delete);
						}
						
					}
					$file_original_names = $this->getRequest()->getParam('file_original_names');
					$file_new_names = $this->getRequest()->getParam('file_new_names');
					
					
					$org_names = explode(',', $file_original_names);
					$new_names = explode(',', $file_new_names);
					
					
					if(count($new_names) > 0)
					{
						foreach ($new_names as $key => $n)
						{
							if($n != '')
							{
								
								$receiptsModel = new Expenses_Model_Receipts();
								$file_type_array = explode('.',$n);
								$file_type = $file_type_array[1];
								
								$file_data = array(
										'receipt_name' => $org_names[$key],
										'receipt_filename' => $n,
										'expense_id' => $expenseId,
										'receipt_file_type' => $file_type,
										'createdby' => $loginUserId,
										'createddate'=>gmdate("Y-m-d H:i:s")
								);
								
								$receiptId = $receiptsModel->saveReceipts($file_data, '');
								
								
								if(file_exists(EXPENSES_RECEIPTS_TEMP_UPLOAD_PATH.$n))
								{
									copy(EXPENSES_RECEIPTS_TEMP_UPLOAD_PATH.$n, EXPENSES_RECEIPTS_PATH.$n);
									unlink(EXPENSES_RECEIPTS_TEMP_UPLOAD_PATH.$n);
								}
							}
						}
					}
				
				//add expense receipts END
					
					
					
					if($result_Id == 'update')
					{
						$history = 'Modified';
						$msg = 'updated';
					}
					else
					{
						$history = 'Created';
						$msg = 'added';
					}
					//Record history
						$expenseHistoryModel = new Expenses_Model_Expensehistory();
						$date = gmdate("Y-m-d H:i:s");
						//$format = 'Associated with Trip %s';
						//$history = sprintf($format, '"Trip to Boston"');
						
						$history_data = array(											
											'trip_id' => $trip_id,
											'expense_id' => $expenseId,
											'history' => $history,
											'createdby' => $loginUserId,
											'createddate'=> $date
										);
						$history_where = '';
						$historyId = $expenseHistoryModel->saveOrUpdateExpenseHistory($history_data,$history_where); 
					//End	
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Expense ".$msg." successfully."));	
					$this->_redirect('expenses/expenses');
				}
				else{
					$this->view->msgarray = $msgarray;
				}
				
			}else
			{
				
				
				$messages = $expensesForm->getMessages();
				foreach ($messages as $key => $val)
				{
					foreach($val as $key2 => $val2)
					{
						$msgarray[$key] = $val2;
						break;
					}
				}
			$file_original_names = $this->_getParam('file_original_names',null);
			$file_new_names = $this->_getParam('file_new_names',null);
			$post_receipt_ids = $this->_getParam('post_receipt_ids',null);
			
			
			
			$this->view->file_original_names = $file_original_names;
			$this->view->file_new_names = $file_new_names;
			$this->view->post_receipt_ids = $post_receipt_ids;
			$msgarray['file_original_names'] =  $file_original_names;
			$msgarray['file_new_names'] =  $file_new_names;
			$msgarray['post_receipt_ids'] =  $post_receipt_ids;
				//echo "<pre>";print_r($msgarray);exit;
				$this->view->msgarray = $msgarray;
					
			}
		}

	}
	
	
	
	/**
	 * This Action is used to Make a copy/clone the expense details based on the expense id.
	 *
	 */
	public function cloneAction(){
		//echo "here";exit;
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$objName = 'expenses';
		$msgarray = '';
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();
	
		$trip_id_from_view = $this->getRequest()->getParam('trip_id_from_view');

		$expensesForm = new Expenses_Form_Expenses();
		$expensesModel = new Expenses_Model_Expenses();
		
		//Start Categories list
		$expensesCategoriesModel = new Expenses_Model_Categories();
		$expenseCategoryData = $expensesCategoriesModel->getExpensesCategoriesList();
		if(sizeof($expenseCategoryData) > 0)
		{
			foreach ($expenseCategoryData as $category){
				$expensesForm->category_id->addMultiOption($category['id'],utf8_encode($category['expense_category_name']));
			}

		}else
		{
			$msgarray['category_id'] = 'Categories are not configured yet.';
			//$emptyFlag++;
		}
		//End
		
		
		$popConfigPermission = array();
		array_push($popConfigPermission,'client');
		array_push($popConfigPermission,'currency');
		$this->view->popConfigPermission = $popConfigPermission;
		
		
		
		//Start Currency list
		$expensesmodel = new Expenses_Model_Expenses();
		$currencyData = $expensesmodel->getCurrencyList();
		//echo "<pre/>"; print_r($currencyData ); 
		if(sizeof($currencyData) > 0)
		{
			foreach ($currencyData as $currency){
				$expensesForm->expense_currency_id->addMultiOption($currency['id'],utf8_encode($currency['currencycode']));
			}

		}else
		{
			$msgarray['expense_currency_id'] = 'Currency are not configured yet.';
			//$emptyFlag++;
		}
		//End
	
		
		//Start Payment methods
		$paymentMethodsModel = new Expenses_Model_Paymentmode();
		$paymentMethodsData = $paymentMethodsModel->getPaymentList();
		if(sizeof($paymentMethodsData) > 0)
		{
			foreach ($paymentMethodsData as $payment){
				$expensesForm->expense_payment_id->addMultiOption($payment['id'],utf8_encode($payment['payment_method_name']));
			}

		}else
		{
			$msgarray['expense_payment_id'] = 'Payment methods are not configured yet.';
			//$emptyFlag++;
		}
		//End
		
		//Start Trips data
		$tripsModel = new Expenses_Model_Trips();
		$tripsData = $tripsModel->getTripsList();
		if(sizeof($tripsData) > 0)
		{
			foreach ($tripsData as $trip){
				$expensesForm->trip_id->addMultiOption($trip['id'],utf8_encode($trip['trip_name']));
			}

		}
		//End
		
		//Start Get advance of an employ
		$advancesummaryModel = new Expenses_Model_Advancesummary();
		$advanceData = $advancesummaryModel->getAdvanceDetailsById($loginUserId);
		
		//echo "<pre>";print_r($advanceData);exit;

		if(sizeof($advanceData) > 0)
		{
			$expensesForm->is_from_advance->addMultiOption('','Select');
			foreach ($advanceData as $advance){
				$expensesForm->is_from_advance->addMultiOption($advance['id'],$advance['balance']);
			}

		}else
		{
			
			$expensesForm->is_from_advance->addMultiOption('','No Advance');
			//$msgarray['is_from_advance'] = 'Advance not configured yet.';
		}		
		//End
		
		//Projects data 
		$projectModel = new Timemanagement_Model_Projects();
		$base_projectData = $projectModel->getEmpProjects($loginUserId);

		if(sizeof($base_projectData) > 0)
		{
			foreach ($base_projectData as $base_project){
				$expensesForm->project_id->addMultiOption($base_project['id'],$base_project['project_name']);
			}

		}	
		
		
		//if receipt selected from computer validation
		$file_orginal_name_array = array();
		$file_original_names = $this->getRequest()->getParam('file_original_names');
		
		if($file_original_names!='')
		{
			$file_orginal_name_array = explode(',',$file_original_names);
		}
		
		$this->view->file_orginal_name_array = $file_orginal_name_array;
		
		
		//if receipt selected from already existing reciipts validation 
		$post_receipt_ids = $this->getRequest()->getParam('post_receipt_ids');
		
		$post_receipt_array = array();
		$existing_recipt_array = array();
		if($post_receipt_ids!='')
		{
			$post_receipt_array = explode(',',$post_receipt_ids);
		}
		if(count($post_receipt_array)>0)
		{
			
			foreach($post_receipt_array as $rID)
			{
				$receiptsModel = new Expenses_Model_Receipts();
				$data_rec = $receiptsModel->getReceiptData($rID);
				foreach($data_rec as $receiptId)
				{
					$existing_recipt_array[$rID] = $receiptId;
				}
			}
			
		}
		$this->view->existing_recipt_array = $existing_recipt_array;

         $tripsmodel = new Expenses_Model_Trips();
		$configData = $tripsmodel->getApplicationCurrency();
		$currency="";
		if(!empty($configData) && (isset($configData[0]['currencycode'])))
		{
			$currency = $configData[0]['currencycode'];
		}
		$expensesForm->expense_currency_id->setValue(!empty($configData[0]['currencyid'])?$configData[0]['currencyid']:'');
	
		$this->view->currency=$currency;
		$this->view->currencyid=(!empty($configData[0]['currencyid']))?$configData[0]['currencyid']:"";		
		//End
		$receiptIdsArray = array();
		try{
			if($id)
			{	//Edit Record...
				if(is_numeric($id) && $id>0)
				{
					$data = $expensesModel->getExpenseDetailsById($id);
					if(!empty($data) && $data != "norows")
					{
						$expensesForm->populate($data[0]);
						$expensesForm->submit->setLabel('Save');
						$this->view->form = $expensesForm;
						$this->view->controllername = $objName;
						//$this->view->id = $id;
						$this->view->id = '';
						$this->view->ermsg = '';
						$this->view->inpage = 'Edit';
						//echo "<pre>";print_r($msgarray);exit;
						$this->view->msgarray = $msgarray;
						
						
						$this->view->trip_id_from_view = $trip_id_from_view;
						
						$receiptsModel = new Expenses_Model_Receipts();
						//$getReceiptData = $receiptsModel->getExpenseReceipts($id);
						$getReceiptData = array();
						$this->view->getReceiptData = $getReceiptData;
						$this->view->data = $data;
						if(count($getReceiptData)>0)
						{
							foreach($getReceiptData as $receiptsData)
							{
								$receiptIdsArray[] = $receiptsData['id'];
							}
							
						}
						
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
				$this->view->form = $expensesForm;
				$this->view->inpage = 'Add';
			}
		}
		catch(Exception $e)
		{
			$this->view->ermsg = 'nodata';
		}
		if($this->getRequest()->getPost()){
			if(isset($_POST['cal_amount']) && $_POST['cal_amount']!='')
			{
				$this->view->cal_amount = $_POST['cal_amount'];
			}
			
			if($expensesForm->isValid($this->_request->getPost())){
				
				$country_id	= NULL;
				$state_id	= NULL;
				//$id = $this->_request->getParam('id');
				$id = '';
				$expense_amount = $this->_request->getParam('expense_amount');
				$expense_name = $this->_request->getParam('expense_name');

				$category_id = $this->_request->getParam('category_id');
				$project_id = $this->_request->getParam('project_id');
				$expense_currency_id = $this->_request->getParam('expense_currency_id');
				$is_reimbursable = $this->_request->getParam('is_reimbursable');
				
				$expense_payment_id = $this->_request->getParam('expense_payment_id');
				$trip_id = $this->_request->getParam('trip_id');
				$expense_payment_ref_no = $this->_request->getParam('expense_payment_ref_no');
				$description = $this->_request->getParam('description');
				
				$is_from_advance = $this->_request->getParam('is_from_advance');
				if(trim($is_from_advance))
					$is_from_advance = 1;
				else
					$is_from_advance = 0;
				
				$expense_date	= $this->_request->getParam('expense_date');
				$expense_date = sapp_Global::change_date($expense_date,'database');
				
				
				
				
				
				
				$date = gmdate("Y-m-d H:i:s");

				$data = array(
							'expense_amount'  => $expense_amount,
							'expense_name'  => $expense_name,							
							'expense_date'  =>	$expense_date,
							'category_id'  => $category_id,							
							'project_id'  => $project_id,
							'expense_currency_id'  => $expense_currency_id,
							'is_reimbursable' => $is_reimbursable,
							'expense_payment_id'  => $expense_payment_id,
							'trip_id'  => $trip_id,
							'expense_payment_ref_no' => $expense_payment_ref_no,
							'is_from_advance' => $is_from_advance,
							'description' => $description,
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
				$Id = $expensesModel->saveOrUpdateExpensesData($data, $where);
				if($Id=='update')
				{
					$expenseId = $id;
				}else
				{
					$expenseId = $Id;
				}
				
				
				
			//add expense receipts START
			
				$receiptsModel = new Expenses_Model_Receipts();

				//if receipt is already exist
				
				$post_receipt_ids = $this->getRequest()->getParam('post_receipt_ids');
				$receiptIdArray=array();
				if($post_receipt_ids!='')
				{
					$receiptIdArray = explode(',',$post_receipt_ids);
					foreach($receiptIdArray as $receipt_id)
					{
						$date = gmdate("Y-m-d H:i:s");
						$receipt_data['expense_id'] = $expenseId;
						$receipt_data['modifieddate'] = $date;
						$receipt_data['modifiedby'] = $loginUserId;
						$receipt_where = array('id=?'=>$receipt_id);
						
						$receiptsModel->updateReceiptsData($receipt_data,$receipt_where);
					}
					
				}
				$deleteReceptsArray = array_diff($receiptIdsArray,$receiptIdArray);
				if(count($deleteReceptsArray)>0)
				{
					foreach($deleteReceptsArray as $rID)
					{
						$date = gmdate("Y-m-d H:i:s");
						$receipt_delete_data['expense_id'] = $expenseId;
						$receipt_delete_data['modifieddate'] = $date;
						$receipt_delete_data['modifiedby'] = $loginUserId;
						$receipt_delete_data['isactive'] = 0;
						$receipt_where_delete = array('id=?'=>$rID);
						$receiptsModel->updateReceiptsData($receipt_delete_data,$receipt_where_delete);
					}
					
				}
				$file_original_names = $this->getRequest()->getParam('file_original_names');
				$file_new_names = $this->getRequest()->getParam('file_new_names');
				
				
				$org_names = explode(',', $file_original_names);
				$new_names = explode(',', $file_new_names);
				
				
				if(count($new_names) > 0)
				{
					foreach ($new_names as $key => $n)
					{
						if($n != '')
						{
							
							$receiptsModel = new Expenses_Model_Receipts();
							$file_type_array = explode('.',$n);
							$file_type = $file_type_array[1];
							
							$file_data = array(
									'receipt_name' => $org_names[$key],
									'receipt_filename' => $n,
									'expense_id' => $expenseId,
									'receipt_file_type' => $file_type,
									'createdby' => $loginUserId,
									'createddate'=>gmdate("Y-m-d H:i:s")
							);
							
							$receiptId = $receiptsModel->saveReceipts($file_data, '');
							
							
							if(file_exists(EXPENSES_RECEIPTS_TEMP_UPLOAD_PATH.$n))
							{
								copy(EXPENSES_RECEIPTS_TEMP_UPLOAD_PATH.$n, EXPENSES_RECEIPTS_PATH.$n);
								unlink(EXPENSES_RECEIPTS_TEMP_UPLOAD_PATH.$n);
							}
						}
					}
				}
			
			//add expense receipts END
				
				
				
				if($Id == 'update')
				{
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Expense updated successfully."));
				}
				else
				{
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Expense added successfully."));
				}
					
				$this->_redirect('expenses/expenses');
			}else
			{
				$messages = $expensesForm->getMessages();
				foreach ($messages as $key => $val)
				{
					foreach($val as $key2 => $val2)
					{
						$msgarray[$key] = $val2;
						break;
					}
				}
				$file_original_names = $this->_getParam('file_original_names',null);
			$file_new_names = $this->_getParam('file_new_names',null);
			$post_receipt_ids = $this->_getParam('post_receipt_ids',null);
			
			
			
			$this->view->file_original_names = $file_original_names;
			$this->view->file_new_names = $file_new_names;
			$this->view->post_receipt_ids = $post_receipt_ids;
			$msgarray['file_original_names'] =  $file_original_names;
			$msgarray['file_new_names'] =  $file_new_names;
			$msgarray['post_receipt_ids'] =  $post_receipt_ids;
				//echo "<pre>";print_r($msgarray);exit;
				$this->view->msgarray = $msgarray;
					
			}
		}

	}

	/**
	 * This Action is used to view the Expense details based on the expense id.
	 *
	 */
	public function viewAction(){
		
		
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
            $loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserId = $auth->getStorage()->read()->id;
		}
		//echo $loginuserId;exit;
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();
		$objName = 'expenses';

		$expensesModel = new Expenses_Model_Expenses();
		$expenseshistoryModel = new Expenses_Model_Expensehistory();

		try
		{
			if(is_numeric($id) && $id>0)
			{
				$data = $expensesModel->getExpenseDetailsById($id);
				
				
				//get expense history
				$expense_hostory = $expenseshistoryModel->getExpenseHostory($id);
				
				//echo "<pre/>"; print_r($data);
				if(!empty($data) && $data != "norows")
				{
					if($data[0]['is_reimbursable']==1)
					{
						$data[0]['is_reimbursable']="Yes";
					}
					else
					{
						$data[0]['is_reimbursable']="No";
					}					
					
					
					if($data[0]['is_from_advance']==0)
					{
						$data[0]['advance_amount']="No Advance";
				
					}
					else
					{
						$data[0]['advance_amount']=$data[0]['advance_amount'] ;
						$tripsmodel = new Expenses_Model_Trips();
						$configData = $tripsmodel->getApplicationCurrency();
						$currency = $configData[0]['currencycode'];
						$this->view->currency=$currency;;
					}					
					
									
					
					$this->view->data = $data;
					$this->view->id = $id;
					$this->view->loginuserRole = $loginuserRole;
					$this->view->loginuserId = $loginuserId;
					$this->view->expense_hostory = $expense_hostory;
					$this->view->controllername = $objName;
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
					
			$expensesModel = new Expenses_Model_Expenses();
			
			$checkExpenses = $expensesModel->checkExpensesAndTrips($id);
			//echo "here";exit;
			if($checkExpenses == 0){
				$data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
				$data['modifiedby'] = $loginUserId;
				$where = array('id=?'=>$id);
				$idstatus = $expensesModel->saveOrUpdateExpensesData($data, $where);
				if($idstatus == 'update')
				{
					//delete expense related receipts
					$receipts_model = new Expenses_Model_Receipts();
					$receiptData = $receipts_model->getExpenseReceipts($id);
					
					//delete already expense receipts
					$updatereceits_data = array('isactive'=>0,
												'modifiedby'=>$loginUserId,
												'modifieddate'=>gmdate("Y-m-d H:i:s"));
					$updatereceipts_where = array('expense_id=?'=>$id)	;							
					
					$receipts_model->saveReceipts($updatereceits_data, $updatereceipts_where);
					
					if(count($receiptData)>0)
					{
						foreach($receiptData as $receipt_data)
						{
							//create receipts again
							$receits_data = array(
									'receipt_name' => $receipt_data['receipt_name'],
									'receipt_filename' => $receipt_data['receipt_filename'],
									'receipt_file_type' => $receipt_data['receipt_file_type'],
									'createdby' => $loginUserId,
									'createddate'=>gmdate("Y-m-d H:i:s")
							);
							$receipts_where='';
							$receiptId = $receipts_model->saveReceipts($receits_data, $receipts_where);
						}
					}
					
					
					$messages['message'] = 'Expense deleted successfully.';
					$messages['msgtype'] = 'success';
				}
				else
				{
					$messages['message'] = 'Expense cannot be deleted.';
					$messages['msgtype'] = 'error';
				}
			}else{
				$messages['message'] = 'Expense is added in trip or submitted or approved or utilized advance.';
				$messages['msgtype'] = 'error';
			}
		}
		else
		{
			$messages['message'] = 'Expense cannot be deleted.';$messages['msgtype'] = 'error';
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
		$objName = 'expenses';
		$msgarray = array();
		$emptyFlag = '';
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		//$id = $this->getRequest()->getParam('expense_Id');
		$id = $this->_request->getParam('expenseId');
		$expensesModel = new Expenses_Model_Expenses();
		$controllername = 'expenses';
		$expenseForm = new Expenses_Form_Expenses();
		$clientsModel = new Timemanagement_Model_Clients();
		$expenseForm->setAction(BASE_URL.'expenses/expenses/addpopup');
		//Start Categories list
		$expensesCategoriesModel = new Expenses_Model_Categories();
		$expenseCategoryData = $expensesCategoriesModel->getExpensesCategoriesList();	
		if(sizeof($expenseCategoryData) > 0)
		{
			
			foreach ($expenseCategoryData as $category){
				$expenseForm->category_id->addMultiOption($category['id'],utf8_encode($category['expense_category_name']));
				
			}

		}else
		{
			$msgarray['category_id'] = 'Categories are not configured yet.';
			//$emptyFlag++;
		}
		//End
		
		
		$popConfigPermission = array();
		array_push($popConfigPermission,'client');
		array_push($popConfigPermission,'currency');
		$this->view->popConfigPermission = $popConfigPermission;
	
		//Start Currency list
		$expensesmodel = new Expenses_Model_Expenses();
		$currencyData = $expensesmodel->getCurrencyList();
		if(sizeof($currencyData) > 0)
		{
			foreach ($currencyData as $currency){
				$expenseForm->expense_currency_id->addMultiOption($currency['id'],utf8_encode($currency['currencycode']));
			}

		}else
		{
			$msgarray['expense_currency_id'] = 'Currency are not configured yet.';
			//$emptyFlag++;
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
		
		$expenseForm->expense_currency_id->setValue($configData[0]['currencyid']);
	
		$this->view->currency=$currency;
		$this->view->currencyid=$configData[0]['currencyid'];
		//End
		
		/* //Start Currency list
		$expensesmodel = new Expenses_Model_Expenses();
		$currencyData = $expensesmodel->getCurrencyList();
		//echo "<pre/>"; print_r($currencyData ); 
		if(sizeof($currencyData) > 0)
		{
			foreach ($currencyData as $currency){
				$expensesForm->expense_currency_id->addMultiOption($currency['id'],utf8_encode($currency['currencycode']));
			}

		}else
		{
			$msgarray['expense_currency_id'] = 'Currency are not configured yet.';
			//$emptyFlag++;
		}
		//End */
		
		

		//Start Payment methods
		$paymentMethodsModel = new Expenses_Model_Paymentmode();
		$paymentMethodsData = $paymentMethodsModel->getPaymentList();
		if(sizeof($paymentMethodsData) > 0)
		{
			foreach ($paymentMethodsData as $payment){
				$expenseForm->expense_payment_id->addMultiOption($payment['id'],utf8_encode($payment['payment_method_name']));
			}

		}else
		{
			$msgarray['expense_payment_id'] = 'Payment methods are not configured yet.';
			//$emptyFlag++;
		}
		//End
		
		//Start Trips data
		$tripsModel = new Expenses_Model_Trips();
		$tripsData = $tripsModel->getTripsList();
		if(sizeof($tripsData) > 0)
		{
			foreach ($tripsData as $trip){
				$expenseForm->trip_id->addMultiOption($trip['id'],utf8_encode($trip['trip_name']));
			}

		}
		// else
		// {
			// $msgarray['trip_id'] = 'Trips are not configured yet.';
		// }
		//End
		
		//Start Get advance of an employ
		$advancesummaryModel = new Expenses_Model_Advancesummary();
		$advanceData = $advancesummaryModel->getAdvanceDetailsById($loginUserId);
		
		//echo "<pre>";print_r($advanceData);exit;

		if(sizeof($advanceData) > 0)
		{
			$expenseForm->is_from_advance->addMultiOption('','Select');
			foreach ($advanceData as $advance){
				$expenseForm->is_from_advance->addMultiOption($advance['id'],$advance['balance']);
			}

		}else
		{
			
			$expenseForm->is_from_advance->addMultiOption('','No Advance');
			//$msgarray['is_from_advance'] = 'Advance not configured yet.';
		}		
		//End
		
		//Projects data 
		$projectModel = new Timemanagement_Model_Projects();
		$base_projectData = $projectModel->getEmpProjects($loginUserId);

		if(sizeof($base_projectData) > 0)
		{
			foreach ($base_projectData as $base_project){
				$expenseForm->project_id->addMultiOption($base_project['id'],$base_project['project_name']);
			}

		}		
		//End
		$receiptId = $this->_request->getParam('receiptId');
		$expense_Id = $this->_request->getParam('expenseId');
		
		
				$receiptIdsArray = array();
		try{
			if($id)
			{	//Edit Record...
				if(is_numeric($id) && $id>0)
				{
					$data = $expensesModel->getExpenseDetailsById($id);
					if(!empty($data) && $data != "norows")
					{
						
						$expenseForm->populate($data[0]);
						$expenseForm->submit->setLabel('Update');
						$this->view->form = $expenseForm;
						$this->view->controllername = $objName;
						$this->view->id = $id;
						$this->view->ermsg = '';
						$this->view->inpage = 'Edit';
						$this->view->data = $data;
						//echo "<pre>";print_r($msgarray);exit;
						$this->view->msgarray = $msgarray;
						
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
				$this->view->form = $expenseForm;
				$this->view->inpage = 'Add';
			}
		}
		catch(Exception $e)
		{
			$this->view->ermsg = 'nodata';
		}
		
		if($this->getRequest()->getPost()){
			
			if($expenseForm->isValid($this->_request->getPost())){
				
				$country_id	= NULL;
				$state_id	= NULL;
				$id = $this->_request->getParam('expense_Id');
				$expense_amount = $this->_request->getParam('expense_amount');
				$expense_name = $this->_request->getParam('expense_name');

				$category_id = $this->_request->getParam('category_id');
				$project_id = $this->_request->getParam('project_id');
				$expense_currency_id = $this->_request->getParam('expense_currency_id');
				$is_reimbursable = $this->_request->getParam('is_reimbursable');
				
				$expense_payment_id = $this->_request->getParam('expense_payment_id');
				$trip_id = $this->_request->getParam('trip_id');
				$expense_payment_ref_no = $this->_request->getParam('expense_payment_ref_no');
				$description = $this->_request->getParam('description');
				
				$cal_amount = $this->_request->getParam('cal_amount');
				$app_amount="";
				$expense_exist_data = array();
				if(!empty($id))
				{
				$expense_exist_data = $expensesModel->getExpenseDetailsById($id);
				$app_amount=$expense_exist_data[0]['application_amount'];
				}
			
				$application_amount=null;
				if($cal_amount!=0)
				{
				
				$application_amount=$cal_amount*$expense_amount;
				}
				$exp_cur_id = !empty($expense_exist_data[0]['expense_currency_id'])?$expense_exist_data[0]['expense_currency_id']:'';
				if( $app_amount!="" && $cal_amount==0  && ($expense_currency_id==$exp_cur_id))
				{
					$application_amount=$app_amount;
				}
				
			
				if($expense_currency_id==$configData[0]['currencyid'])
				{
					$application_amount=$expense_amount;
				}
				if($application_amount=='')
				{
					$msgarray['expense_amount'] = 'Convert Currency.';
					$this->view->msgarray = $msgarray;
				}
				
				$siteconfigmodel = new Default_Model_Sitepreference();
				$configData = $siteconfigmodel->getActiveRecord();
			
				if(!empty($configData) && (isset($configData[0]['currencyid'])))
				{
			       $application_currency = $configData[0]['currencyid'];
				}
				
				$is_from_advance = $this->_request->getParam('is_from_advance');
				if(trim($is_from_advance))
					$is_from_advance = 1;
				else
					$is_from_advance = 0;
				
				$expense_date	= $this->_request->getParam('expense_date');
				$expense_date = sapp_Global::change_date($expense_date,'database');
				
				
				$expense_date = $this->_request->getParam('expense_date');
				$expense_date = sapp_Global::change_date($expense_date,'database');
				
				
				$emp_summary_model = new Default_Model_Employee();
				$emp_det = $emp_summary_model->getEmp_from_summary($loginUserId); //submit can be done by only owner of Expense
				$manager_id = $emp_det['reporting_manager'];
				
				
				$date = gmdate("Y-m-d H:i:s");

				$data = array(
							'expense_amount'  => $expense_amount,
							'expense_name'  => $expense_name,							
							'expense_date'  =>	$expense_date,
							'category_id'  => $category_id,							
							'project_id'  => $project_id,
							'expense_currency_id'  => $expense_currency_id,
							'expense_conversion_rate'  => $cal_amount,
						    'application_amount'=>$application_amount,
						    'application_currency_id'  => $application_currency,
							'is_reimbursable' => $is_reimbursable,
							'expense_payment_id'  => $expense_payment_id,
							'trip_id'  => $trip_id,
							'expense_payment_ref_no' => $expense_payment_ref_no,
							'is_from_advance' => $is_from_advance,
							'description' => $description,
							'manager_id' => $manager_id,
				);
				// if project is selected update client id in expenses
				if($project_id!='' && $project_id>0)
				{
					$projectsDetails = $expensesModel->getProjectClient($project_id);
					$data['client_id'] = $projectsDetails[0]['client_id'];
				}
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
				//Update advance summary based on expense amount if it is from advance.
				if($is_from_advance == 1)
				{
					$advanceData = $advancesummaryModel->getAdvanceDetailsById($loginUserId);
					if($application_amount > $advanceData[0]['balance'])
					{
						//$msgarray['expense_amount'] = 'Expense amount is more than selected advance.';
						//$this->view->msgarray = $msgarray;
						$change_utilized_amt = $advanceData[0]['utilized'] + $advanceData[0]['balance'];							
						$change_balance_amt = 0;
						$adva_summary_array = array(
								'utilized'  => $change_utilized_amt,
								'balance'  => $change_balance_amt,							
								'modifiedby'  => $loginUserId,
								'modifieddate' => $date,									
							);	
					}
					else{
						if(count($expense_exist_data)>0){
							$adva_summary_array = array(
									'utilized'  => $advanceData[0]['utilized'],
									'balance'  => $advanceData[0]['balance'],							
									'modifiedby'  => $loginUserId,
									'modifieddate' => $date,									
								);
							//if expense old amount is greater than now given amount 
							if($expense_exist_data[0]['application_amount'] > $application_amount){
								$change_utilized_amt = $advanceData[0]['utilized'] - ($expense_exist_data[0]['application_amount'] - $application_amount);
								
								$change_balance_amt = $advanceData[0]['balance'] + ($expense_exist_data[0]['application_amount'] - $application_amount);
								
								$adva_summary_array = array(
									'utilized'  => $change_utilized_amt,
									'balance'  => $change_balance_amt,							
									'modifiedby'  => $loginUserId,
									'modifieddate' => $date,									
								);
							}
							else if($expense_exist_data[0]['application_amount'] < $application_amount){//if expense old amount is less than now given amount 
							
								$change_utilized_amt = $advanceData[0]['utilized'] + ($application_amount - $expense_exist_data[0]['application_amount']);
								
								$change_balance_amt = $advanceData[0]['balance'] - ($application_amount - $expense_exist_data[0]['application_amount']);
								
								$adva_summary_array = array(
									'utilized'  => $change_utilized_amt,
									'balance'  => $change_balance_amt,							
									'modifiedby'  => $loginUserId,
									'modifieddate' => $date,									
								);
							}
							
						}
						else{
							$change_utilized_amt = $advanceData[0]['utilized'] + $application_amount;							
							$change_balance_amt = $advanceData[0]['balance'] - $application_amount;
							$adva_summary_array = array(
								'utilized'  => $change_utilized_amt,
								'balance'  => $change_balance_amt,							
								'modifiedby'  => $loginUserId,
								'modifieddate' => $date,									
							);							
						}
						
						
					}
					$adva_summary_where = array('id=?'=>$advanceData[0]['id']);
					$adv_summary_id = $advancesummaryModel->SaveAdvanceData($adva_summary_array, $adva_summary_where);
				} 				
				//End
				$Id = $expensesModel->saveOrUpdateExpensesData($data, $where);
				if($Id == 'update')
				{
					$expenseId = $id;
				}else
				{
					$expenseId = $Id;
				}
				//update advance amount in expense table
					if($is_from_advance == 1)
					{
						$advance_amount = $application_amount;
						if($application_amount > $advanceData[0]['balance'])
						{
							$advance_amount = $advanceData[0]['balance'];
						}
						$advance_amount_array = array('advance_amount'=>$advance_amount);
						$adva_amount_where = array('id=?'=>$expenseId);
						$expensesModel->saveOrUpdateExpensesData($advance_amount_array, $adva_amount_where);
					}
					if($Id == 'update')
					{
						$history = 'Modified';
						$msg = 'updated';
					}
					else
					{
						$history = 'Created';
						$msg = 'added';
					}
				//Record history
						$expenseHistoryModel = new Expenses_Model_Expensehistory();
						$date = gmdate("Y-m-d H:i:s");
						//$format = 'Associated with Trip %s';
						//$history = sprintf($format, '"Trip to Boston"');
						
						$history_data = array(											
											'trip_id' => $trip_id,
											'expense_id' => $expenseId,
											'history' => $history,
											'createdby' => $loginUserId,
											'createddate'=> $date
										);
						$history_where = '';
						$historyId = $expenseHistoryModel->saveOrUpdateExpenseHistory($history_data,$history_where); 
					//End	
				
				
				$receiptId = $this->_request->getParam('receiptId');
				
				if($receiptId!='')
				{
					
					$receiptsModel = new Expenses_Model_Receipts();
					//if receipt is already exist
					
					$date = gmdate("Y-m-d H:i:s");
					$receipt_data['expense_id'] = $expenseId;
					$receipt_data['modifieddate'] = $date;
					$receipt_data['modifiedby'] = $loginUserId;
					$receipt_where = array('id=?'=>$receiptId);
					
					$receiptsModel->updateReceiptsData($receipt_data,$receipt_where);
				}
				
				
				$close = 'close';
				$this->view->popup=$close;
			
				
				if($Id == 'update')
				{
					$this->view->eventact = 'updated';
				}
				else
				{
					$this->view->eventact = 'added';
				}
					
			}else
			{
				$messages = $expenseForm->getMessages();
				foreach ($messages as $key => $val)
				{
					foreach($val as $key2 => $val2)
					{
						$msgarray[$key] = $val2;
						break;
					}
				}
				
				//echo "<pre>";print_r($msgarray);exit;
				$this->view->msgarray = $msgarray;
					
			}
		}
		$this->view->controllername = $controllername;
		$this->view->receiptId = $receiptId;
		$this->view->expense_Id = $expense_Id;
		$this->view->form = $expenseForm;
		$this->view->ermsg = '';

	}
	
	public function uploadsaveAction()
	{
		$user_id = sapp_Global::_readSession('id');
        $filedata = array();
        
    	// Validate file with size greater than default(Upload Max Filesize)limit
        if ($_FILES["myfile"]["size"] == 0 || $_FILES["myfile"]["size"] > (2*1024*1024)) {
            $this->_helper->json(array('error' => 'filesize'));
        } else if(isset($_FILES["myfile"])) {
            $fileName = $_FILES["myfile"]["name"];
            $fileName = preg_replace('/[^a-zA-Z0-9.\']/', '_', $fileName);			  	
            $newName  = time().'_'.$user_id.'_'.str_replace(' ', '_', $fileName);

            $filedata['original_name'] = $fileName;
            $filedata['new_name'] = $newName;
            
        	if (isset($_POST["doc_id"]) && $_POST["doc_id"] != '') {
        		move_uploaded_file($_FILES["myfile"]["tmp_name"],EXPENSES_RECEIPTS_PATH.$newName);
        	} else {
        		move_uploaded_file($_FILES["myfile"]["tmp_name"],EXPENSES_RECEIPTS_TEMP_UPLOAD_PATH.$newName);
        	}
            
            $this->_helper->json(array('filedata' => $filedata));
        }
		
		
		
	}
	public function uploaddeleteAction()
    {	
    	if(isset($_POST["op"]) && $_POST["op"] == "delete" && isset($_POST['doc_new_name']))
        {
        	$filePath = "";
        		$filePath = EXPENSES_RECEIPTS_TEMP_UPLOAD_PATH.$_POST['doc_new_name'];
            
	        	// Remove attachment files from upload folder.
	            if (file_exists($filePath)) {
	                unlink($filePath);
	            }
	            
	            $this->_helper->json(array());
        }
    }
	public function displayreceiptsAction()
	{
		$param=$this->_getParam('param')?$this->_getParam('param'):'';
		// $receipt_ids_str = '"'. implode('","', explode(',', $receipt_ids)) .'"';
		
		$limit = $this->_getParam('limit')?$this->_getParam('limit'):8;
		$offset = $this->_getParam('offset')?$this->_getParam('offset'):0;
		
		$receiptsModel = new Expenses_Model_Receipts();
		$dataReceipts = $receiptsModel->getReceipts($param,$limit,$offset);
		$getAllReceiptsCount = $receiptsModel->getReceiptsCount($isUnreported='');
		$this->view->dataArray = $dataReceipts;
		$this->view->limit = $limit;
		$this->view->offset = $limit+$offset;
		
			
		$this->view->getAllReceiptsCount = $getAllReceiptsCount;
		
		
		$getAllUnreportedReceiptsCount = $receiptsModel->getReceiptsCount($isUnreported='yes');
		$this->view->getAllUnreportedReceiptsCount = $getAllUnreportedReceiptsCount;
		//get unreported receipts
		if($param=='unreported')
			$this->view->getAllReceiptsCount = $getAllUnreportedReceiptsCount;		
			
	}
	public function addtrippopupAction()
	{
		$msgarray = array();
		$emptyFlag = '';
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		 $id = $this->getRequest()->getParam('id');
		 
		 $isfrompopup = $this->getRequest()->getParam('isfrompopup');
		$isfrompopup = !empty($isfrompopup)?$this->getRequest()->getParam('isfrompopup'):'';

		$controllername = 'expenses';
		$tripsForm = new Expenses_Form_Trips();
		$tripsModel = new Expenses_Model_Trips();
		$tripsForm->setAction(BASE_URL.'expenses/expenses/addtrippopup');
		
		$this->view->id=$id;
		$this->view->isfrompopup=$isfrompopup;
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
			
				$data['createdby'] = $loginUserId;
				$data['createddate'] = $date;
				$data['modifieddate'] = $date;
				$data['isactive'] = 1;
				$where = '';
				$Id = $tripsModel->saveOrUpdateTripsData($data, $where);
				
				
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
				
				
				$tripsData = $tripsModel->fetchAll('isactive = 1 and createdby='.$loginUserId,'trip_name')->toArray();

				$opt ='';
				foreach($tripsData as $record){
					$opt .= sapp_Global::selectOptionBuilder($record['id'], $record['trip_name']);
				}
				$this->view->tripsData = $opt;
				
				$this->view->eventact = 'added';
				$close = 'close';
				$this->view->popup=$close;
				
					
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

		
		$this->view->controllername = $controllername;
		$this->view->form = $tripsForm;
		$this->view->ermsg = '';
	}

	
	public function submitexpenseAction()
	{
		echo 'success';	
		exit;		
	}

	public function addreceiptimageAction()
	{
		$receipt_ids = $this->_request->getParam('receipt_ids');
		$isFromBulk = ($this->_request->getParam('isFromBulk'))?$this->_request->getParam('isFromBulk'):'';
		$key_val = ($this->_request->getParam('key_val'))?$this->_request->getParam('key_val'):'';
		$receiptsModel = new Expenses_Model_Receipts();
		$getReceiptData = array();
		if($receipt_ids!='')
			$getReceiptData = $receiptsModel->getReceiptData($receipt_ids);
		$this->view->getReceiptData = $getReceiptData;
		$this->view->isFromBulk = $isFromBulk;
		$this->view->key_val = $key_val;
	}
	public function expensestatusAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$date = gmdate("Y-m-d H:i:s");
		$expensesModel = new Expenses_Model_Expenses();	
		$status = $this->_request->getParam('status');
		$expense_id = $this->_request->getParam('expense_id');
		$trip_id = $this->_request->getParam('trip_id');
		$where = array('id=?'=>$expense_id);		
		$data = array('status'=>$status,'modifiedby'=>$loginUserId,'modifieddate'=>$date);
		
		$emp_summary_model = new Default_Model_Employee();
		$expense_details = $expensesModel->getExpenseDetailsById($expense_id);
		$exp_emp_details = $emp_summary_model->getEmp_from_summary($expense_details[0]['createdby']);
		
		$emil_id = $exp_emp_details['emailaddress'];
		$to_name = $exp_emp_details['userfullname'];
		$managerDetails = $emp_summary_model->getEmp_from_summary($expense_details[0]['manager_id']); 
		$from_name = $managerDetails['userfullname'];
		$link = BASE_URL.'expenses/expenses/view/id/'.$expense_id;
			
		if($status == 'submitted'){
			$emp_det = $emp_summary_model->getEmp_from_summary($loginUserId); //submit can be done by only owner of Expense
			$data['manager_id'] = $emp_det['reporting_manager'];
			
			$managerDetails = $emp_summary_model->getEmp_from_summary($emp_det['reporting_manager']); 
			$emil_id = $managerDetails['emailaddress'];
			$to_name = $managerDetails['userfullname'];
			$from_name = $emp_det['userfullname'];
			$link = BASE_URL.'expenses/myemployeeexpenses/view/id/'.$expense_id;
		}
		
		
		$Id = $expensesModel->saveOrUpdateExpensesData($data, $where);
		
		
				//start of mailing
				 
				//send email to reporting manager while submitting expense
			
				$base_url = 'http://'.$this->getRequest()->getHttpHost() . $this->getRequest()->getBaseUrl();
				$view = $this->getHelper('ViewRenderer')->view;
				$this->view->from_name = $from_name;
				$this->view->to_name = $to_name;
				$this->view->expense_name = $expense_details[0]['expense_name'];
				$this->view->base_url=$base_url;
				$this->view->status=$status;
				$this->view->page='Expense';
				$this->view->url_link=$link;
				$text = $view->render('expensemailtemplates/expensestatus.phtml');
				$options['subject'] = APPLICATION_NAME.': Notification';
				$options['header'] = 'Greetings from Sentrifugo';
				$options['toEmail'] = $emil_id;
				$options['toName'] = $to_name;
				$options['message'] = $text;
				$result = sapp_Global::_sendEmail($options);
				
		//end of mailing
		
		//Record history
			$expenseHistoryModel = new Expenses_Model_Expensehistory();
			$date = gmdate("Y-m-d H:i:s");
			$format = 'Associated with Trip %s';
			//$history = sprintf($format, '"Trip to Boston"');
			$history = $status;
			$history_data = array(											
								'expense_id' => $expense_id,
								'history' => $history,
								'createdby' => $loginUserId,
								'createddate'=> $date
							);
			$history_where = '';
			$historyId = $expenseHistoryModel->saveOrUpdateExpenseHistory($history_data,$history_where); 
		//End	
		
		sapp_ExpensesHelper::tripstatus($trip_id);
		$this->_helper->json(array('message'=>'success','status'=> 'Expense '.$status.' successfully.'));
	}
	public function listreportingmangersAction()
	{
		$limit = 4;
		$offset = 0;
		$expense_id = $this->_getParam('expense_id')?$this->_getParam('expense_id'):'';		
		$managerId = $this->_getParam('managerId')?$this->_getParam('managerId'):0;				
		$expenseCreatedBy = $this->_getParam('expenseCreatedBy')?$this->_getParam('expenseCreatedBy'):0;				
		$empModel = new Default_Model_Employees();
		$managerslist = $empModel->getReportingManagers($limit,$offset,$managerId,$expenseCreatedBy);	
		//echo "<pre>";print_r($managerslist);exit;
		
		$this->view->managerslist = $managerslist;
		$managersCount = $empModel->getReportingManagersCount($managerId,$expenseCreatedBy);
		//echo "<pre>";print_r($managersCount);exit;
		$this->view->totalManagersCount = $managersCount;		
		$this->view->managerId = $managerId;
		$this->view->expenseCreatedBy = $expenseCreatedBy;
		$this->view->expense_id = $expense_id;
		$this->view->limit = $limit;
		$this->view->offset = $offset+$limit;
	}
	
	public function viewmoremanagersAction()
	{
		$limit = $this->_getParam('limit')?$this->_getParam('limit'):8;
		$offset = $this->_getParam('offset')?$this->_getParam('offset'):0;
		$expense_id = $this->_getParam('expense_id')?$this->_getParam('expense_id'):0;
		$managerId = $this->_getParam('managerId')?$this->_getParam('managerId'):0;
		$expenseCreatedBy = $this->_getParam('expenseCreatedBy')?$this->_getParam('expenseCreatedBy'):0;
		
		$empModel = new Default_Model_Employees();
		$managerslist = $empModel->getReportingManagers($limit,$offset,$managerId,$expenseCreatedBy);	
		$managersCount = $empModel->getReportingManagersCount($managerId,$expenseCreatedBy);
		$this->view->managerslist = $managerslist;
		$this->view->totalManagersCount = $managersCount;		
		$this->view->managerId = $managerId;
		$this->view->expense_id = $expense_id;
		$this->view->limit = $limit;
		$this->view->offset = $offset+$limit;
	}
	
	public function forwardexpensetoAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$managerId = $this->_getParam('managerId')?$this->_getParam('managerId'):0;
		$expense_id = $this->_getParam('expense_id')?$this->_getParam('expense_id'):0;
		
		$date = gmdate("Y-m-d H:i:s");
		$data['manager_id'] = $managerId;
		$data['modifieddate'] = $date;
		$data['modifiedby'] = $loginUserId;
		$where = array('id=?'=>$expense_id);
		$ExpensesModel = new Expenses_Model_Expenses();
		
		$updated = $ExpensesModel->saveOrUpdateExpensesData($data,$where);		
		
		if($updated){	
			
			//$exp_det = $ExpensesModel->getExpenseDetailsById($expense_id);
			$date = gmdate("Y-m-d H:i:s");
			$forward_data['expense_id'] = $expense_id;
			//$forward_data['trip_id'] = $exp_det[0]['trip_id'];
			$forward_data['from_id'] = $loginUserId;
			$forward_data['to_id'] = $managerId;
			$forward_data['createddate'] = $date;
			$forward_data['createdby'] = $loginUserId;
			$ForwardExpensesModel = new Expenses_Model_Forwardexpenses();
			$forward_where = '';
			$ForwardExpensesModel->saveOrUpdateForwardData($forward_data,$forward_where);
			
			
			//start of mailing
				$emp_summary_model = new Default_Model_Employee();
				//send email to reporting manager while submitting expense
				$emp_details = $emp_summary_model->getEmp_from_summary($loginUserId);
				$from_name = $emp_details['userfullname'];
				
				$emp_to_details = $emp_summary_model->getEmp_from_summary($managerId);
				$to_name = $emp_to_details['userfullname'];
				$emil_id = $emp_to_details['emailaddress'];
				
				$expense_details = $ExpensesModel->getExpenseDetailsById($expense_id);
				
				$link = BASE_URL.'expenses/myemployeeexpenses/view/id/'.$expense_id;
				
				$base_url = 'http://'.$this->getRequest()->getHttpHost() . $this->getRequest()->getBaseUrl();
				$view = $this->getHelper('ViewRenderer')->view;
				$this->view->from_name = $from_name;
				$this->view->to_name = $to_name;
				$this->view->expense_name = $expense_details[0]['expense_name'];
				$this->view->base_url=$base_url;
				$this->view->status='forwarded';
				$this->view->page='Expense';
				$this->view->url_link=$link;
				$text = $view->render('expensemailtemplates/expensestatus.phtml');
				$options['subject'] = APPLICATION_NAME.': Notification';
				$options['header'] = 'Greetings from Sentrifugo';
				$options['toEmail'] = $emil_id;
				$options['toName'] = $to_name;
				$options['message'] = $text;
				$result = sapp_Global::_sendEmail($options);
				
			//end of mailing
			
			
		}
		
		
		$this->_helper->json(array('message'=>'success','status'=> 'Expense forwarded successfully.'));
	}
	
	//for downloading pdf
    public function downloadexpensepdfAction()
    {		
        $auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
            $loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserId = $auth->getStorage()->read()->id;
		}
		//echo $loginuserId;exit;
		$id = $this->getRequest()->getParam('id');		
		$objName = 'expenses';
		$expensesModel = new Expenses_Model_Expenses();		
        if(!is_numeric($id))
        {
            return false;
        }
        else
        {
            $data = $expensesModel->getExpenseDetailsById($id);
				
			if(!empty($data) && $data != "norows")
			{
				if($data[0]['is_reimbursable']==1)
				{
					$data[0]['is_reimbursable']="Yes";
				}
				else
				{
					$data[0]['is_reimbursable']="No";
				}
				if($data[0]['is_from_advance']==0)
				{
					$data[0]['advance_amount']="No Advance";
			
				}
				else
				{
					$data[0]['advance_amount']=$data[0]['advance_amount'] ;
					$tripsmodel = new Expenses_Model_Trips();
					$configData = $tripsmodel->getApplicationCurrency();
					$currency = $configData[0]['currencycode'];
					$this->view->currency=$currency;;
				}	
				$appText = utf8_encode(substr($data[0]['expense_name'],0,1)).$data[0]['expense_name'];
				//render view page as text
				$view = $this->getHelper('ViewRenderer')->view;
				$this->view->data = $data;
				$this->view->id = $id;
				$this->view->loginuserRole = $loginuserRole;
				$this->view->loginuserId = $loginuserId;
				$this->view->ermsg = '';				
				$text = $view->render('expenses/downloadexpensepdf.phtml');
				//generating file name
				/* $file_name_params_array = array($data[0]['trip_name'],$data[0]['from_date'],$data[0]['to_date'],$appText);
				$file_name = $this->_helper->PdfHelper->generateFileName($file_name_params_array); */
				$file_name = $data[0]['expense_name'];
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
				$mpdf->Output((!empty($file_name)?$file_name:'Expense').'.pdf','D');
				exit;				
			}
			
        }
    }
	//function for bulk expense
	public function bulkexpensesAction()
	{
		$auth = Zend_Auth::getInstance();
			if($auth->hasIdentity()){
				$loginUserId = $auth->getStorage()->read()->id;
			}
		//echo "fgdfg";die();
		$expensesCategoriesModel = new Expenses_Model_Categories();
		$expenseCategoryData = $expensesCategoriesModel->getExpensesCategoriesList();
		$this->view->expenseCategoryData = $expenseCategoryData;
		
		
		
	
		$expenseModel = new Expenses_Model_Expenses();
		$currencyData = $expenseModel->getCurrencyList();
		$this->view->currencyData = $currencyData;

	//setting application currency
		$tripsmodel = new Expenses_Model_Trips();
		$configData = $tripsmodel->getApplicationCurrency();
		$currency="";
		if(!empty($configData) && (isset($configData[0]['currencycode'])))
		{
		$currency = $configData[0]['currencycode'];
		}
			
		$this->view->currency=$currency;
		$this->view->currencyid=(!empty($configData[0]['currencyid']))?$configData[0]['currencyid']:"";
		//end		
		
		//Projects data 
		$projectModel = new Timemanagement_Model_Projects();
		$base_projectData = $projectModel->getEmpProjects($loginUserId);
		$this->view->base_projectData = $base_projectData;
		
		
		//Start Payment methods
		$paymentMethodsModel = new Expenses_Model_Paymentmode();
		$paymentMethodsData = $paymentMethodsModel->getPaymentList();
		$this->view->paymentmode = $paymentMethodsData;
		
		//End
		
		if($this->getRequest()->getPost()){
			
			
			$siteconfigmodel = new Default_Model_Sitepreference();
			$configData = $siteconfigmodel->getActiveRecord();
		
			if(!empty($configData) && (isset($configData[0]['currencyid'])))
			{
			   $application_currency = $configData[0]['currencyid'];
			}
			
			$total_count = $this->_request->getParam('count');
			for($i=1;$i<=$total_count;$i++)
			{
				$expense_name = ($this->_request->getParam('expense_name_'.$i))?$this->_request->getParam('expense_name_'.$i):'';
				
				$payment_mode = $this->_request->getParam('paymentmode_'.$i);
				$payment_refe = $this->_request->getParam('payment_ref_'.$i);
				
				$exp_date = $this->_request->getParam('expense_date_'.$i);
				$exp_date = sapp_Global::change_date($exp_date,'database');
				
				$exp_category = $this->_request->getParam('category_'.$i);
				$exp_project = $this->_request->getParam('project_'.$i);
				$exp_currency = $this->_request->getParam('currency_'.$i);
				$exp_amount = $this->_request->getParam('amount_'.$i);
				$exp_description = $this->_request->getParam('description_'.$i);
				
				$cal_amount = $this->_request->getParam('cal_amount_'.$i);
				if($exp_currency==$application_currency)
				{
					$application_amount = $exp_amount;
				}else
				{
					$application_amount =$cal_amount*$exp_amount;
				}
				
				$emp_summary_model = new Default_Model_Employee();
				$emp_det = $emp_summary_model->getEmp_from_summary($loginUserId); //submit can be done by only owner of Expense
				$manager_id = $emp_det['reporting_manager'];
				
				$date = gmdate("Y-m-d H:i:s");
				$data = array('expense_name'=>$expense_name,
							'category_id'=>$exp_category,
							'project_id'=>$exp_project,
							'expense_date'=>$exp_date,
							'expense_currency_id'=>$exp_currency,
							'expense_amount'=>$exp_amount,
							'application_currency_id'=>$application_currency,
							'application_amount'=>$application_amount,
							'expense_conversion_rate'=>$cal_amount,
							'description'=>$exp_description,
							'createdby'=>$loginUserId,
							'modifieddate'=>$date,
							'createddate'=>$date,
							'isactive'=>1,
							'expense_payment_id'=>$payment_mode,
							'expense_payment_ref_no'=>$payment_refe,
							'manager_id'=>$manager_id
				);
				$where = '';
				$expensesModel = new Expenses_Model_Expenses();		
				$expense_Id = $expensesModel->saveOrUpdateExpensesData($data, $where);
				
				
				
				//Record history
						$expenseHistoryModel = new Expenses_Model_Expensehistory();
						$date = gmdate("Y-m-d H:i:s");
						//$format = 'Associated with Trip %s';
						//$history = sprintf($format, '"Trip to Boston"');
						$history='Created';
						$history_data = array(
											'expense_id' => $expense_Id,
											'history' => $history,
											'createdby' => $loginUserId,
											'createddate'=> $date
										);
						$history_where = '';
						$historyId = $expenseHistoryModel->saveOrUpdateExpenseHistory($history_data,$history_where); 
					//End	
				
				
				
				//add expense receipts START
				
					$receiptsModel = new Expenses_Model_Receipts();

					//if receipt is already exist
					
					$post_receipt_ids = $this->getRequest()->getParam('receipts_ids_'.$i);
					$receiptIdArray=array();
					if($post_receipt_ids!='')
					{
						$receiptIdArray = explode(',',$post_receipt_ids);
						foreach($receiptIdArray as $receipt_id)
						{
							$date = gmdate("Y-m-d H:i:s");
							$receipt_data['expense_id'] = $expense_Id;
							$receipt_data['modifieddate'] = $date;
							$receipt_data['modifiedby'] = $loginUserId;
							$receipt_where = array('id=?'=>$receipt_id);
							
							$receiptsModel->updateReceiptsData($receipt_data,$receipt_where);
						}
						
					}
					
					$file_original_names = $this->getRequest()->getParam('file_original_names_'.$i);
					$file_new_names = $this->getRequest()->getParam('file_new_names_'.$i);
					
					
					$org_names = explode(',', $file_original_names);
					$new_names = explode(',', $file_new_names);
					
					
					if(count($new_names) > 0)
					{
						foreach ($new_names as $key => $n)
						{
							if($n != '')
							{
								
								$receiptsModel = new Expenses_Model_Receipts();
								$file_type_array = explode('.',$n);
								$file_type = $file_type_array[1];
								
								$file_data = array(
										'receipt_name' => $org_names[$key],
										'receipt_filename' => $n,
										'expense_id' => $expense_Id,
										'receipt_file_type' => $file_type,
										'createdby' => $loginUserId,
										'createddate'=>gmdate("Y-m-d H:i:s")
								);
								
								$receiptId = $receiptsModel->saveReceipts($file_data, '');
								
								
								if(file_exists(EXPENSES_RECEIPTS_TEMP_UPLOAD_PATH.$n))
								{
									copy(EXPENSES_RECEIPTS_TEMP_UPLOAD_PATH.$n, EXPENSES_RECEIPTS_PATH.$n);
									unlink(EXPENSES_RECEIPTS_TEMP_UPLOAD_PATH.$n);
								}
							}
						}
					}
				
				//add expense receipts END
				
				
			}
			$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Expense added successfully."));
						
			$this->_redirect('expenses/expenses');
		}
		
		
	}

	
	/*public function getcurrencynameAction()
	{
		$currncy_id= $this->_getParam('currencyId');
		$currencyModel = new Default_Model_Currency();
		$currencyData = $currencyModel->getCurrencyDataByID($currncy_id);
		$currencyname="";
		if(!empty($currencyData))
		{
			$currencyname="1 ".$currencyData[0]['currencyname']."=";
		}
		
		$this->_helper->json(array('currency_name'=>$currencyname));
	}*/

	public function getcategoriesAction()
	{
		$expensesCategoriesModel = new Expenses_Model_Categories();
		$expenseCategoryData = $expensesCategoriesModel->getExpensesCategoriesList();
		
		$opt='<option value=\'\'>Select category</option>';
		if(count($expenseCategoryData)>0)
		{
			foreach($expenseCategoryData as $category)
			{
				$opt.="<option value='".$category['id']."'>".$category['expense_category_name']."</option>";
			}
		}
		
		
		$this->_helper->json(array('options'=>utf8_encode($opt)));
	}
	public function getprojectsAction()
	{
		$opt='<option value=\'\'>Mode</option>';
		
		$paymentMethodsModel = new Expenses_Model_Paymentmode();
		$paymentMethodsData = $paymentMethodsModel->getPaymentList();
		
		if(count($paymentMethodsData)>0)
		{
			foreach($paymentMethodsData as $mode)
			{
				$opt.="<option value='".$mode['id']."'>".$mode['payment_method_name']."</option>";
			}
		}
		
		
		$this->_helper->json(array('options'=>utf8_encode($opt)));
	}
	public function getcurrencyAction()
	{
		$expenseModel = new Expenses_Model_Expenses();
		$currencyData = $expenseModel->getCurrencyList();
		
		 //start for setting application currency
        $tripsmodel = new Expenses_Model_Trips();
        $configData = $tripsmodel->getApplicationCurrency();
        $currencyid=!empty($configData[0]['currencyid'])?$configData[0]['currencyid']:'';
        //End
		
		$opt='';
		if(count($currencyData)>0)
		{
			foreach($currencyData as $curency)
			{
				$slected='';
				if($currencyid==$curency['id'])
					$slected = "selected='selected'";
				$opt.="<option ".$slected." value='".$curency['id']."'>".$curency['currencycode']."</option>";
			}
		}
		
		$this->_helper->json(array('options'=>utf8_encode($opt)));
	}
	public function uploadedfilesAction()
	{
		$files = $this->_getParam('files');
		$org_files = $this->_getParam('org_files');
		$key_val = $this->_getParam('key_val');
		$this->view->files = $files;
		$this->view->org_files = $org_files;
		$this->view->key_val = $key_val;
	}
}

