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

class Timemanagement_ExpensecategoryController extends Zend_Controller_Action
{


	/**
	 * Init
	 *
	 * @see Zend_Controller_Action::init()
	 */
	private $options;
	public function init()
	{
		$this->_options= $this->getInvokeArg('bootstrap')->getOptions();
	}

	public function indexAction()
	{
		$expenseCategoryModel = new Timemanagement_Model_Expensecategories();
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
			$sort = 'DESC';$by = 'modified';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'modified';
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

		$dataTmp = $expenseCategoryModel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);
			
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
	}

	public function editAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$objName = 'expensecategory';
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();

		$expenseCategoryForm = new Timemanagement_Form_Expensecategory();
		$expenseCategoryModel = new Timemanagement_Model_Expensecategories();
		try
		{
			if($id)
			{	//Edit Record...
				if(is_numeric($id) && $id>0)
				{
					$data = $expenseCategoryModel->getExpenseCategoryDataById($id);
					if(!empty($data) && $data != "norows")
					{
						$expenseCategoryForm->populate($data[0]);
						$expenseCategoryForm->submit->setLabel('Update');
						$this->view->form = $expenseCategoryForm;
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
				$this->view->form = $expenseCategoryForm;
				$this->view->inpage = 'Add';
			}
		}
		catch(Exception $e)
		{
			$this->view->ermsg = 'nodata';
		}
		if($this->getRequest()->getPost()){
			if($expenseCategoryForm->isValid($this->_request->getPost())){
				$id = $this->_request->getParam('id');
				$category= $this->_request->getParam('expense_category');
				$unitPrice= $this->_request->getParam('unit_price');
				$date = new Zend_Date();

				$data = array( 'expense_category'=>trim($category),
							   'unit_price'=>trim($unitPrice),
				               'modified_by'=>$loginUserId,
							   'modified'=>gmdate("Y-m-d H:i:s")	
				);
				if($id!=''){
					$where = array('id=?'=>$id);
				}
				else
				{
					$data['created_by'] = $loginUserId;
					$data['created'] = gmdate("Y-m-d H:i:s");
					$data['is_active'] = 1;
					$where = '';
				}
				$Id = $expenseCategoryModel->SaveorUpdateExpenseCategoryData($data, $where);
				if($Id == 'update')
				{
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Expense Category updated successfully."));
				}
				else
				{
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Expense Category added successfully."));
				}
					
				$this->_redirect('timemanagement/expensecategory');
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
}