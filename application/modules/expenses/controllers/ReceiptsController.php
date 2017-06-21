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

class Expenses_ReceiptsController extends Zend_Controller_Action
{
	public function preDispatch()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('deletereceipt', 'html')->initContext();
		$ajaxContext->addActionContext('downloadreceipt', 'html')->initContext();
		$ajaxContext->addActionContext('downloadexpensereceipt', 'html')->initContext();
		$ajaxContext->addActionContext('uploadsave', 'html')->initContext();
		$ajaxContext->addActionContext('displayreceipts', 'html')->initContext();
		$ajaxContext->addActionContext('viewmorereceipts', 'html')->initContext();
		$ajaxContext->addActionContext('addreceipttoexpense', 'json')->initContext();
		$ajaxContext->addActionContext('addexpensetotrip', 'json')->initContext();
		$ajaxContext->addActionContext('listexpenses', 'html')->initContext();
		$ajaxContext->addActionContext('listtrips', 'html')->initContext();
		$ajaxContext->addActionContext('viewmoreexpenses', 'html')->initContext();
		$ajaxContext->addActionContext('viewmoretrips', 'html')->initContext();
		$ajaxContext->addActionContext('cleardata', 'html')->initContext();
	}
	
	/**
	 * default action
	 */
	public function indexAction()
	{
		$receiptsModel = new Expenses_Model_Receipts();
		$limit=8;$offset=0;
		$dataTmp = $receiptsModel->getReceipts($isUnreported='',$limit,$offset);	
		$this->view->dataArray = $dataTmp;
		$unreportedReceipts = $receiptsModel->getReceipts($isUnreported='yes',$limit=8,$offset=0);
		
		$auth = Zend_Auth::getInstance();

		 	if($auth->hasIdentity()){
		 		$loginUserId = $auth->getStorage()->read()->id;
		 		$loginuserRole = $auth->getStorage()->read()->emprole;
		 		$loginuserGroup = $auth->getStorage()->read()->group_id;
		 	}
		
		//login user add permission checking
		$addPermission = sapp_Global::_checkprivileges(RECEIPTS,$loginuserGroup,$loginuserRole,'add');
		
		$getAllReceiptsCount = $receiptsModel->getReceiptsCount($isUnreported='');
		$getAllUnreportedReceiptsCount = $receiptsModel->getReceiptsCount($isUnreported='yes');
		$this->view->unreportedReceiptCount = count($unreportedReceipts);
		$this->view->addPermission = $addPermission;
		$this->view->limit = $limit;
		$this->view->offset = $limit+$offset;
		$this->view->getAllReceiptsCount = $getAllReceiptsCount;
		$this->view->getAllUnreportedReceiptsCount = $getAllUnreportedReceiptsCount;
		$month_first_day = sapp_Global::change_date(date('01-m-Y'),'view');
		$today = sapp_Global::change_date(date('d-m-Y'),'view');
		$this->view->start_date = $month_first_day;
		$this->view->end_date = $today;
		
	}
	public function downloadreceiptAction()
	{
		//New
	    $this->view->layout()->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(true);
	    $recipt_names=$this->_getParam('recipt_names');
	    $fileNames=explode(',',$recipt_names);
		
		
	    $zip_file_name='myfile.zip';
	    $file_path=DOMAIN.'public/uploads/expenses_receipts/';
	    //print_r($fileNames);die;
	    if(sizeof($fileNames) > 1){
	    	//zip
	    	$this->zipFilesDownload($fileNames);
	    }else{
	    	header('Content-Type: application/octet-stream');
	    	header('Content-Disposition: attachment; filename="'.$fileNames[0].'"');
	    	readfile($file_path.$fileNames[0]);
	    }
	    die;
	}
	public function downloadexpensereceiptAction()
	{
		$this->view->layout()->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(true);
	    $expense_id=$this->_getParam('expense_id');
		$receiptsModel = new Expenses_Model_Receipts();
		$receiptData = $receiptsModel->getExpenseReceipts($expense_id);
		$fileNames = array();
		if(count($receiptData)>0)
		{
			foreach($receiptData as $receipts)
			{
				$fileNames[] = $receipts['receipt_filename'];
			}
		}
		
	    //$fileNames=explode(',',$recipt_names);
	    $zip_file_name='myfile.zip';
	    $file_path=DOMAIN.'public/uploads/expenses_receipts/';
	    if(sizeof($fileNames) > 1){
	    	//zip
	    	$this->zipFilesDownload($fileNames);
	    }else{
	    	header('Content-Type: application/octet-stream');
	    	header('Content-Disposition: attachment; filename="'.$fileNames[0].'"');
	    	readfile($file_path.$fileNames[0]);
	    }
	    die;
	}

	function zipFilesDownload($file_names){
		
		$file_path = EXPENSES_RECEIPTS_PATH;
		$archive_file_name = time().rand(8,8).'.zip';
		
		$zip = new ZipArchive();
		if ($zip->open($file_path.$archive_file_name, ZIPARCHIVE::CREATE )!==TRUE) {
		  exit("cannot open <$archive_file_name>\n");

		}

		foreach($file_names as $files){
		  $zip->addFile($file_path.$files,$files);
		}
		$zip->close();

	    	header('Content-Type: application/octet-stream');
	    	header('Content-Disposition: attachment; filename="'.$archive_file_name.'"');
	    	readfile($file_path.$archive_file_name);	
	}
	public function deletereceiptAction()
	{
		$receiptsModel = new Expenses_Model_Receipts();
		$receipt_ids=$this->_getParam('receipt_ids');
		
		$receipt_ids_array = explode(',',$receipt_ids);
		
		if(count($receipt_ids_array)>0)
		{
			foreach($receipt_ids_array as $receipt_id)
			{
				$data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
				$where = array('id=?'=>$receipt_id);
				$Id = $receiptsModel->deleteReceipt($data, $where);
			}
		}
		$receiptsModel = new Expenses_Model_Receipts();
		$dataTmp = $receiptsModel->getReceipts();	
		$this->view->dataArray = $dataTmp;
	}
	public function uploadsaveAction()
	{
		$receiptsModel = new Expenses_Model_Receipts();
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
			
			$file_type_array = explode('.',$filedata['original_name']);
			$file_type = $file_type_array[1];
            
        	move_uploaded_file($_FILES["myfile"]["tmp_name"],EXPENSES_RECEIPTS_PATH.$newName);
			$data = array(
					'receipt_name' => $filedata['original_name'],
					'receipt_filename' => $filedata['new_name'],
					'receipt_file_type' => $file_type,
					'createdby' => $user_id,
					'createddate'=>gmdate("Y-m-d H:i:s")
			);
			$where='';
            $receiptId = $receiptsModel->saveReceipts($data, $where);
			$filedata['last_inserted_ids'] = $receiptId;
            $this->_helper->json(array('filedata' => $filedata));
        } 
	}
	public function displayreceiptsAction()
	{
		$param=$this->_getParam('param')?$this->_getParam('param'):'';
		// $receipt_ids_str = '"'. implode('","', explode(',', $receipt_ids)) .'"';
		
		
		$search_str = $this->_getParam('searchstr')?$this->_getParam('searchstr'):'';
		$start_date = $this->_getParam('start_date')?$this->_getParam('start_date'):'';
		$end_date = $this->_getParam('end_date')?$this->_getParam('end_date'):'';
		
		
		$limit = $this->_getParam('limit')?$this->_getParam('limit'):8;
		$offset = $this->_getParam('offset')?$this->_getParam('offset'):0;
		
		$receiptsModel = new Expenses_Model_Receipts();
		$dataReceipts = $receiptsModel->getReceipts($param,$limit,$offset,$search_str,$start_date,$end_date);
		$getAllReceiptsCount = $receiptsModel->getReceiptsCount($isUnreported='',$search_str,$start_date,$end_date);
		$this->view->dataArray = $dataReceipts;
		$this->view->limit = $limit;
		$this->view->offset = $limit+$offset;
		
			
		$this->view->getAllReceiptsCount = $getAllReceiptsCount;
		
		
		$getAllUnreportedReceiptsCount = $receiptsModel->getReceiptsCount($isUnreported='yes',$search_str,$start_date,$end_date);
		$this->view->getAllUnreportedReceiptsCount = $getAllUnreportedReceiptsCount;
		//get unreported receipts
		if($param=='unreported')
			$this->view->getAllReceiptsCount = $getAllUnreportedReceiptsCount;
			
		
			
	}
	public function viewmorereceiptsAction()
	{
		$limit = $this->_getParam('limit')?$this->_getParam('limit'):8;
		$offset = $this->_getParam('offset')?$this->_getParam('offset'):0;
		$param = $this->_getParam('param')?$this->_getParam('param'):'';
		$searchstr = $this->_getParam('searchstr')?$this->_getParam('searchstr'):'';
		$start_date = $this->_getParam('start_date')?$this->_getParam('start_date'):'';
		$end_date = $this->_getParam('end_date')?$this->_getParam('end_date'):'';
		$receiptsModel = new Expenses_Model_Receipts();
		$dataReceipts = $receiptsModel->getReceipts($param,$limit,$offset,$searchstr,$start_date,$end_date);
		$getAllReceiptsCount = $receiptsModel->getReceiptsCount($isUnreported='',$searchstr,$start_date,$end_date);
		$this->view->dataArray = $dataReceipts;
		$this->view->limit = $limit;
		$this->view->offset = $limit+$offset;
		
			
		$this->view->getAllReceiptsCount = $getAllReceiptsCount;
		
		
		$getAllUnreportedReceiptsCount = $receiptsModel->getReceiptsCount($isUnreported='yes',$searchstr,$start_date,$end_date);
		$this->view->getAllUnreportedReceiptsCount = $getAllUnreportedReceiptsCount;
		if($param=='unreported')
			$this->view->getAllReceiptsCount = $getAllUnreportedReceiptsCount;
		
	}
	public function listexpensesAction()
	{
		$limit = 4;
		$offset=0;
		$receipt_id = $this->_getParam('receipt_id')?$this->_getParam('receipt_id'):0;
		$expense_id = $this->_getParam('expense_id')?$this->_getParam('expense_id'):'';
		$expenseModel = new Expenses_Model_Expenses();
		$expenseslist = $expenseModel->getExpenses($expense_id,$limit,$offset);
		$this->view->expenseslist = $expenseslist;
		$expensesCount = $expenseModel->getExpensesCount($expense_id);
		$this->view->totalExpensescount = $expensesCount;
		$this->view->receipt_id = $receipt_id;
		$this->view->expense_id = $expense_id;
		$this->view->limit = $limit;
		$this->view->offset = $offset+$limit;
	}
	public function addreceipttoexpenseAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$receipt_id = $this->_getParam('receipt_id')?$this->_getParam('receipt_id'):0;
		$expense_id = $this->_getParam('expense_id')?$this->_getParam('expense_id'):0;
		
		$date = gmdate("Y-m-d H:i:s");
		$data['expense_id'] = $expense_id;
		$data['modifieddate'] = $date;
		$data['modifiedby'] = $loginUserId;
		$where = array('id=?'=>$receipt_id);
		$receiptsModel = new Expenses_Model_Receipts();
		$receiptsModel->updateReceiptsData($data,$where);
		$this->_helper->json(array('message'=>'success','status'=> 'Receipt Added To Expense.'));
	}
	public function viewmoreexpensesAction()
	{
		$limit = $this->_getParam('limit')?$this->_getParam('limit'):8;
		$offset = $this->_getParam('offset')?$this->_getParam('offset'):0;
		$expense_id = $this->_getParam('expense_id')?$this->_getParam('expense_id'):0;
		$receipt_id = $this->_getParam('receipt_id')?$this->_getParam('receipt_id'):0;
		
		$expenseModel = new Expenses_Model_Expenses();
		$expenseslist = $expenseModel->getExpenses($expense_id,$limit,$offset);
		$this->view->expenseslist = $expenseslist;
		$expensesCount = $expenseModel->getExpensesCount($expense_id);
		$this->view->totalExpensescount = $expensesCount;
		$this->view->receipt_id = $receipt_id;
		$this->view->expense_id = $expense_id;
		$this->view->limit = $limit;
		$this->view->offset = $offset+$limit;
	}
	public function cleardataAction()
	{
		$receiptsModel = new Expenses_Model_Receipts();
		$limit=8;$offset=0;
		$param = $this->_getParam('param')?$this->_getParam('param'):'';
		$dataTmp = $receiptsModel->getReceipts($param,$limit,$offset);	
		$this->view->dataArray = $dataTmp;
		$unreportedReceipts = $receiptsModel->getReceipts($isUnreported='yes',$limit,$offset);
		$getAllReceiptsCount = $receiptsModel->getReceiptsCount($isUnreported='');
		$getAllUnreportedReceiptsCount = $receiptsModel->getReceiptsCount($isUnreported='yes');
		$this->view->unreportedReceiptCount = count($unreportedReceipts);
		$this->view->limit = $limit;
		$this->view->offset = $limit+$offset;
		$this->view->getAllUnreportedReceiptsCount = $getAllUnreportedReceiptsCount;
		$this->view->getAllReceiptsCount = $getAllReceiptsCount;
		//if($param=='unreported')
			//$this->view->getAllReceiptsCount = $getAllUnreportedReceiptsCount;
	}
	public function showreceiptspopupAction()
	{
		$key = $this->_getParam('key')?$this->_getParam('key'):'';
		$receipts_ids = $this->_getParam('cls_receipts_id')?$this->_getParam('cls_receipts_id'):'';
		//echo 'ids.'.$receipts_ids;die();
		$msgarray = array();
		$emptyFlag = '';
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->getRequest()->getParam('id');

		$controllername = 'receipts';
		$receiptsModel = new Expenses_Model_Receipts();
		$receiptsData = $receiptsModel->getunreportedReceipts($receipts_ids);
		

		$this->view->receiptsData=$receiptsData;
		$this->view->controllername = $controllername;
		$this->view->ermsg = '';
		$this->view->keyval = $key;
	}
	public function listtripsAction()
	{
		$limit = 4;
		$offset=0;
		$expense_id = $this->_getParam('expense_id')?$this->_getParam('expense_id'):'';
		$tripId = $this->_getParam('tripId')?$this->_getParam('tripId'):'';
		$tripsModel = new Expenses_Model_Trips();
		$tripslist = $tripsModel->getTrips($limit,$offset,$tripId);
		
		$this->view->tripslist = $tripslist;
		$tripsCount = $tripsModel->getTripsCount();
		$this->view->totalTripcount = $tripsCount;
		$this->view->expense_id = $expense_id;
		$this->view->tripId = $tripId;
		$this->view->limit = $limit;
		$this->view->offset = $offset+$limit;
	}
	public function viewmoretripsAction()
	{
		$limit = $this->_getParam('limit')?$this->_getParam('limit'):8;
		$offset = $this->_getParam('offset')?$this->_getParam('offset'):0;
		$expense_id = $this->_getParam('expense_id')?$this->_getParam('expense_id'):0;
		
		$tripsModel = new Expenses_Model_Trips();
		$tripslist = $tripsModel->getTrips($limit,$offset);
		$this->view->tripslist = $tripslist;
		$tripsCount = $tripsModel->getTripsCount();
		$this->view->totalTripcount = $tripsCount;
		$this->view->expense_id = $expense_id;
		$this->view->limit = $limit;
		$this->view->offset = $offset+$limit;
	}
	public function addexpensetotripAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$trip_id = $this->_getParam('trip_id')?$this->_getParam('trip_id'):0;
		$expense_id = $this->_getParam('expense_id')?$this->_getParam('expense_id'):0;
		
		$date = gmdate("Y-m-d H:i:s");
		$data['trip_id'] = $trip_id;
		$data['modifieddate'] = $date;
		$data['modifiedby'] = $loginUserId;
		$where = array('id=?'=>$expense_id);
		$ExpensesModel = new Expenses_Model_Expenses();
		$ExpensesModel->saveOrUpdateExpensesData($data,$where);
		$this->_helper->json(array('message'=>'success','status'=> 'Expense Added To Trip.'));
	}
}

