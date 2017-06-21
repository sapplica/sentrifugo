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

class Timemanagement_ExpensesController extends Zend_Controller_Action
{


	/**
	 * Init
	 *
	 * @see Zend_Controller_Action::init()
	 */
	private $options;
	public function preDispatch()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('expensereports', 'html')->initContext();
		$ajaxContext->addActionContext('viewexpensereports', 'html')->initContext();
	}
	public function init()
	{
		$this->_options= $this->getInvokeArg('bootstrap')->getOptions();
	}


	public function indexAction()
	{
		//$expensesModel = new Timemanagement_Model_Expenses();
		//$this->view->expenses_model = $expensesModel;

		$year_first_day = '01-01-'.date('Y');
		$today = date('d-m-Y');
			
		$start_date = ($this->_getParam('start_date')!='')? $this->_getParam('start_date'):$year_first_day;
		$end_date = ($this->_getParam('end_date')!='')? $this->_getParam('end_date'):$today;

		if($start_date != '')
		$start_date = $start_date.' 00:00:00';
		if($end_date != '')
		$end_date = $end_date.' 23:59:59';
			
		$this->view->start_date = ($this->_getParam('start_date')!='')? $this->_getParam('start_date'):$year_first_day;
		$this->view->end_date = ($this->_getParam('end_date')!='')? $this->_getParam('end_date'):$today;
		$this->view->selected_period_hidden = ($this->_getParam('selected_period_hidden')!='')? $this->_getParam('selected_period_hidden'):'';
			
			
	}
	
	public function expensereportsAction()
	{
		$auth = Zend_Auth::getInstance();
		$loginUserId=0;
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}		
		$expensesModel = new Timemanagement_Model_Expenses();
		$projid = ($this->_request->getParam('projectId') != "undefined" && $this->_request->getParam('projectId') != "all")?$this->_request->getParam('projectId'):"";

		$year_first_day = '01-01-'.date('Y');
		$today = date('m-d-Y');

		$start_date = ($this->_getParam('start_date')!='' && $this->_getParam('start_date')!='undefined')? $this->_getParam('start_date'):$year_first_day;
		$end_date = ($this->_getParam('end_date')!='' && $this->_getParam('end_date')!='undefined')? $this->_getParam('end_date'):$today;

		$org_start_date = $start_date;
		$org_end_date = $end_date;

		if($start_date != '')
		$start_date = $start_date.' 00:00:00';
		if($end_date != '')
		$end_date = $end_date.' 23:59:59';

		$call = $this->_getParam('call');

		//if($call == 'ajaxcall')
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
			$sort = 'DESC';$by = 'ex.modified';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'ex.modified';
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

		$dataTmp = $expensesModel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall, $start_date, $end_date, $org_start_date,$org_end_date,$loginUserId);
			
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
				
		//**************************************
		/*$expensesModel = new Timemanagement_Model_Expenses();
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
			$sort = 'DESC';$by = 'ex.modified';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'ex.modified';
			if($dashboardcall == 'Yes')
			$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
			else
			$perPage = $this->_getParam('per_page',PERPAGE);
			$pageNo = $this->_getParam('page', 1);
			// search from grid - START 
			$searchData = $this->_getParam('searchData');
			$searchData = rtrim($searchData,',');
			// search from grid - END
		}

		$dataTmp = $expensesModel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);
			
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();*/
	}

	public function editAction()
	{
		$auth = Zend_Auth::getInstance();$emptyFlag=0;
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$objName = 'expenses';
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();

		$expensesForm = new Timemanagement_Form_Expenses();
		$expensesModel = new Timemanagement_Model_Expenses();
		
		//pre populate data
		$clientsModel = new Timemanagement_Model_Clients();
		$clientsData = $clientsModel->getActiveClientsData();
		$msgarray = array();
		if(sizeof($clientsData) > 0){
			foreach ($clientsData as $client){
				$expensesForm->client_id->addMultiOption($client['id'],$client['client_name']);
			}
		}else{
			$msgarray['client_id'] = 'Clients are not configured yet.';
			$emptyFlag++;
		}
		
		$expenseCategoriesModel = new Timemanagement_Model_Expensecategories();
		$expenseCategoriesData = $expenseCategoriesModel->getActiveExpenseCategoriesData();
		if(sizeof($expenseCategoriesData) > 0)
		{
			foreach ($expenseCategoriesData as $category){
				$expensesForm->expense_cat_id->addMultiOption($category['id'],$category['expense_category']);
			}

		}else{
			$msgarray['expense_cat_id'] = 'Expense Categories are not configured yet.';
			$emptyFlag++;
		}		
		//End pre populate
		try
		{
			if($id)
			{	//Edit Record...
				if(is_numeric($id) && $id>0)
				{
					$data = $expensesModel->getExpensesDataById($id);
					
					$projectsModel = new Timemanagement_Model_Projects();
					$projectsData = $projectsModel->getProjectListByClientID($data[0]['client_id']);
					if(sizeof($projectsData) > 0){
						foreach ($projectsData as $project){
								$expensesForm->project_id->addMultiOption($project['id'],$project['project_name']);
							}
					}else{
							$msgarray['project_id'] = 'Clients are not configured yet.';
							$emptyFlag++;
					}
															
					if(!empty($data) && $data != "norows")
					{
						$expensesForm->populate($data[0]);
						$expensesForm->submit->setLabel('Update');
						$this->view->form = $expensesForm;
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
			if($expensesForm->isValid($this->_request->getPost())){
				$id = $this->_request->getParam('id');
				$clientID = $this->_request->getParam('client_id');
				$expenseAmount = $this->_request->getParam('expense_amount');
				$expenseCategoryID = $this->_request->getParam('expense_cat_id');
				$expenseDate = sapp_Global::change_date($this->_request->getParam('expense_date'),'database');
				$isBillable = $this->_request->getParam('is_billable');
				$note = $this->_request->getParam('note');
				$projectID = $this->_request->getParam('project_id');
				$imagepath = $this->_request->getParam('expense_image_value');			
				$date = new Zend_Date();
				$data = array( 'client_id'=>$clientID,
							   'emp_id'=>$loginUserId,
							   'expense_amount'=>trim($expenseAmount),
							   'expense_cat_id'=>trim($expenseCategoryID),
							   'expense_date'=>trim($expenseDate),
							   'is_billable'=>trim($isBillable),
							   'note'=>trim($note),
							   'project_id'=>trim($projectID),
							   'receipt_file'=>trim($imagepath),
				               'expense_status'=>'saved',
							   'modified'=>gmdate("Y-m-d H:i:s")	
				);
				
				$path = EXPENSES_UPLOAD_PATH;
				
				$filecopy = 'success';
				if($imagepath !='')
				{
					$filecopy = 'error';
					if(file_exists(USER_PREVIEW_UPLOAD_PATH.'//'.$imagepath))
					{
						try
						{
							echo $srcPath.'<br/>';
							if(copy(USER_PREVIEW_UPLOAD_PATH.'//'.$imagepath, $path.'//'.$imagepath))
								$filecopy = 'success';
							unlink(USER_PREVIEW_UPLOAD_PATH.'//'.$imagepath);

						}
						catch(Exception $e)
						{
							echo $msgarray['expense_image_value'] = $e->getMessage();exit;
						}
					}
				}
				if($imagepath == '')
					unset($data['receipt_file']);
				else if($filecopy == 'error')
					unset($data['receipt_file']);				
				if($id!=''){
					$where = array('id=?'=>$id);
				}
				else
				{
					//$data['created_by'] = $loginUserId;
					$data['created'] = gmdate("Y-m-d H:i:s");
					$data['is_active'] = 1;
					$where = '';
				}
				$Id = $expensesModel->SaveorUpdateExpensesData($data, $where);
				if($Id == 'update')
				{
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Expenses updated successfully."));
				}
				else
				{
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Expenses added successfully."));
				}
					
				$this->_redirect('timemanagement/expenses');
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
				$this->view->msgarray = $msgarray;
					
			}
		}
	}

	public function viewAction()
	{
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();
		$objName = 'expensecategory';
		$expenseCategoryForm = new Timemanagement_Form_Expensecategory();
		$expenseCategoryForm->removeElement("submit");
		$elements = $expenseCategoryForm->getElements();
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)
			{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
					$element->setAttrib("disabled", "disabled");
				}
			}
		}
		$expenseCategoryModel = new Timemanagement_Model_Expensecategories();
		try
		{
			if(is_numeric($id) && $id>0)
			{
				$data = $expenseCategoryModel->getExpenseCategoryDataById($id);
				if(!empty($data) && $data != "norows")
				{
					$expenseCategoryForm->populate($data[0]);
					$this->view->form = $expenseCategoryForm;
					$this->view->controllername = $objName;
					$this->view->id = $id;
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
				$this->view->form = $expenseCategoryForm;
			}
		}
		catch(Exception $e)
		{
			$this->view->ermsg = 'nodata';
		}
	}

	public function deleteAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->_request->getParam('objid');
		$messages['message'] = ''; $messages['msgtype'] = '';
		$messages['flagtype'] = '';
		if($id)
		{
			$expenseCategoryModel = new Timemanagement_Model_Expensecategories();
			$checkExpenses = $expenseCategoryModel->checkExpenses($id);
			if($checkExpenses == 0){
				$data = array('is_active'=>0,'modified'=>gmdate("Y-m-d H:i:s"));
				$where = array('id=?'=>$id);
				$expenseCategoryData = $expenseCategoryModel->getExpenseCategoryDataById($id);
				$Id = $expenseCategoryModel->SaveorUpdateExpenseCategoryData($data, $where);
				if($Id == 'update')
				{
				    //sapp_Global::send_configuration_mail("Expense Category", $expenseCategoryData[0]['category']);
					$messages['message'] = 'Category deleted successfully.';
					$messages['msgtype'] = 'success';
				}
				else
				{
					$messages['message'] = 'Category cannot be deleted.';
					$messages['msgtype'] = 'error';
				}
			}else{
				$messages['message'] = 'Category in use.';
				$messages['msgtype'] = 'error';
			}
		}
		else
		{
			$messages['message'] = 'Category cannot be deleted.';$messages['msgtype'] = 'error';
		}
		$this->_helper->json($messages);
	}//end of delete
	
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

		$controllername = 'expensecategory';
		$expenseCategoryForm = new Timemanagement_Form_Expensecategory();
		$expenseCategoryModel = new Timemanagement_Model_Expensecategories();
		$clientsForm->setAction(BASE_URL.'timemanagement/expensecategory/addpopup');

		if($this->getRequest()->getPost()){
			if($expenseCategoryForm->isValid($this->_request->getPost())){
				$category = $this->_request->getParam('category');
				$date = gmdate("Y-m-d H:i:s");
 
			   $data = array(
							'category'  => $category,
			                'created_by' => $loginUserId,
			                'created' => gmdate("Y-m-d H:i:s"),
			                'is_active' => 1,
				);
				
				$where = '';
		
				$Id = $expenseCategoryModel->SaveorUpdateExpenseCategoryData($data, $where);
				
				$expenseCategoryData = $expenseCategoryModel->fetchAll('is_active = 1','category')->toArray();

				$opt ='';
				foreach($expenseCategoryData as $record){
					$opt .= sapp_Global::selectOptionBuilder($record['id'], $record['category']);
				}
				$this->view->expenseCategoryData = $opt;
					
				$this->view->eventact = 'added';
				$close = 'close';
				$this->view->popup=$close;
			}else
			{
				$messages = $expenseCategoryForm->getMessages();
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
		$this->view->form = $expenseCategoryForm;
		$this->view->ermsg = '';

	}
	
	public function getfilenameAction(){
		$id = $this->_getParam('id', NULL);
		$expensesModel = new Timemanagement_Model_Expenses();
		$data = $expensesModel->getExpensesDataById($id);
		$this->_helper->json(array('file_name'=>$data[0]['receipt_file']));
		//print_r($data[0]['receipt_file']);die;
	}
	
	public function downloadAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		//echo "aaaa" ;die;
    	$expenseFile = $this->_getParam('expense_file', NULL);
    	//$expenseFile = 'www.jpg';
    	//echo $expenseFile;die;
		if(!empty($expenseFile)){
			//$file = EXPENSES_FILES_PATH.$expenseFile;
			$file = BASE_PATH.'/uploads/expenses/'.$this->_getParam('expense_file');
			$status = sapp_Global::downloadFile($file);
			//print_r($status);
		}
	}
	
	public function uploadpreviewAction(){
		$result = $this->expenseReceiptUpload();

		$this->_helper->json($result);

	}
	public function expenseReceiptUpload(){
		$savefolder = USER_PREVIEW_UPLOAD_PATH;		// folder for upload

		$max_size = 1024;			// maxim size for image file, in KiloBytes

		// Allowed image types
		//$allowtype = array('gif', 'jpg', 'jpeg', 'png');
		$allowtype = array('gif', 'jpg', 'jpeg', 'png','GIF','JPG','JPEG','PNG','pdf','PDF','doc','docx','xps');

		/** Uploading the image **/

		$rezultat = '';
		$result_status = '';
		$result_msg = '';
		// if is received a valid file
		$file = $_FILES['expenses_upload'];
		if (isset ($file)) {
			$type = explode(".", strtolower($file['name']));
			//echo in_array($type, $allowtype);exit;
			if (in_array($type[1], $allowtype)) {
				// check its size

					if ($file['error'] == 0) {
						//$newname = 'preview_'.date("His").'.'.$type[1];
						$newname = 'expense_'.rand().time().'.'.$type[1];
						$thefile = $savefolder . "/" . $file['name'];
						$newfilename = $savefolder . "/" . $newname;
						$filename = $newname;

						if (!move_uploaded_file ($file['tmp_name'], $newfilename)) {
							$rezultat = '';
							$result_status = 'error';
							$result_msg = 'The file cannot be uploaded, try again.';
						}
						else {
							move_uploaded_file ($file['tmp_name'], $newfilename);
							$rezultat = $filename;
							$result_status = 'success';
							$result_msg = '';
						}
					}
			}
			else
			{
				$rezultat = '';
				$result_status = 'error';
				$result_msg = 'Please upload only .gif, .jpg, .jpeg, .png images.';

			}
		}
		else
		{
			$rezultat = '';
			$result_status = 'error';
			$result_msg = 'Please upload only .gif, .jpg, .jpeg, .png images.';

		}

		$result = array(
				'result'=>$result_status,
				'img'=>$rezultat,
				'msg'=>$result_msg
		);
		return $result;
	}

	public function getprojectbyclientidAction(){
		$id = $this->getRequest()->getParam('clientID');
		$projectsModel = new Timemanagement_Model_Projects();
		$projectsList = $projectsModel->getProjectListByClientID($id);
		$out = array_values($projectsList);
		$this->_helper->json(json_encode($out));		
	}
	
	public function submitexpenseAction(){
		$id = $this->getRequest()->getParam('id');
		$expensesModel = new Timemanagement_Model_Expenses();
		$data['expense_status'] = 'submitted';
		$where = array('id=?'=>$id); 
		$Id = $expensesModel->SaveorUpdateExpensesData($data, $where);
		$this->_helper->json(array("status"=>"success","msg"=>"Expense submitted successfully."));		
	}
	
	//Admin Approcal
	public function viewexpensesAction()
	{
		//$expensesModel = new Timemanagement_Model_Expenses();
		//$this->view->expenses_model = $expensesModel;

		$year_first_day = '01-01-'.date('Y');
		$today = date('d-m-Y');
			
		$start_date = ($this->_getParam('start_date')!='')? $this->_getParam('start_date'):$year_first_day;
		$end_date = ($this->_getParam('end_date')!='')? $this->_getParam('end_date'):$today;

		if($start_date != '')
		$start_date = $start_date.' 00:00:00';
		if($end_date != '')
		$end_date = $end_date.' 23:59:59';
			
		$this->view->start_date = ($this->_getParam('start_date')!='')? $this->_getParam('start_date'):$year_first_day;
		$this->view->end_date = ($this->_getParam('end_date')!='')? $this->_getParam('end_date'):$today;
		$this->view->selected_period_hidden = ($this->_getParam('selected_period_hidden')!='')? $this->_getParam('selected_period_hidden'):'';
		$this->view->tm_role = Zend_Registry::get('tm_role');	
			
	}
	
	public function viewexpensereportsAction()
	{
		$auth = Zend_Auth::getInstance();
		$loginUserId=0;
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}		
		$expensesModel = new Timemanagement_Model_Expenses();
		$projid = ($this->_request->getParam('projectId') != "undefined" && $this->_request->getParam('projectId') != "all")?$this->_request->getParam('projectId'):"";

		$year_first_day = '01-01-'.date('Y');
		$today = date('m-d-Y');

		$start_date = ($this->_getParam('start_date')!='' && $this->_getParam('start_date')!='undefined')? $this->_getParam('start_date'):$year_first_day;
		$end_date = ($this->_getParam('end_date')!='' && $this->_getParam('end_date')!='undefined')? $this->_getParam('end_date'):$today;

		$org_start_date = $start_date;
		$org_end_date = $end_date;

		if($start_date != '')
		$start_date = $start_date.' 00:00:00';
		if($end_date != '')
		$end_date = $end_date.' 23:59:59';

		$call = $this->_getParam('call');

		//if($call == 'ajaxcall')
		$this->_helper->layout->disableLayout();

		$view = Zend_Layout::getMvcInstance()->getView();
		$objname = $this->_getParam('objname');
		$refresh = $this->_getParam('refresh');
		$dashboardcall = $this->_getParam('dashboardcall');
		$type=$this->_getParam('type');
		
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
			$sort = 'DESC';$by = 'ex.modified';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'ex.modified';
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

		$dataTmp = $expensesModel->getViewExpensesGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall, $start_date, $end_date, $org_start_date,$org_end_date,$loginUserId,$type);
			
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
				
	}

	public function updateexpensestatusAction(){
		$id = $this->getRequest()->getParam('id');
		$status = $this->getRequest()->getParam('status');
		if($status == 1)
			$data['expense_status'] = 'approved';
		else{
			$data['expense_status'] = 'rejected';
			$data['reject_note'] = $this->getRequest()->getParam('notes');
		}	
		$expensesModel = new Timemanagement_Model_Expenses();
		
		$where = array('id=?'=>$id); 
		$Id = $expensesModel->SaveorUpdateExpensesData($data, $where);
		if($Id == 'update')
			$this->_helper->json(array("status"=>"success","msg"=>"Expense updated successfully."));
		else
			$this->_helper->json(array("status"=>"failed","msg"=>"Expense updatation failed."));
	}
}