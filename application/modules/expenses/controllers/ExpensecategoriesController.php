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

class Expenses_ExpensecategoriesController extends Zend_Controller_Action
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
	
		$categoriesmodel = new Expenses_Model_Categories();
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
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'c.modifieddate';
			if($dashboardcall == 'Yes')
			$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
			else
			$perPage = $this->_getParam('per_page',PERPAGE);
			$pageNo = $this->_getParam('page', 1);
			$searchData = $this->_getParam('searchData');
			$searchData = rtrim($searchData,',');
		}
		$dataTmp = $categoriesmodel->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall);
		
		
	
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
		
		
	
	}
	
	
	
	
	
	
	
		/**
	 * This Action is used to Create/Update the category details based on the  id.
	 *
	 */
	public function editAction(){
		
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			
			$loginUserId = $auth->getStorage()->read()->id;
		
		}
		$objName = 'expensecategories';
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();

		$categoryForm = new Expenses_Form_Categories();
		$categoriesmodel = new Expenses_Model_Categories();
		try{
			if($id)
			{	//Edit
				if(is_numeric($id) && $id>0)
				{
			
					$data = $categoriesmodel->getsingleCategorystatusData($id);


					if(!empty($data))
					{
                             $categoryForm = new Expenses_Form_Categories();
							 $categoryForm->submit->setLabel('Update');

								$categoryForm->populate($data);
								$categoryForm->setDefault('expense_category_name',$data['expense_category_name']);
								$this->view->form = $categoryForm;
								
						
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
				$this->view->form = $categoryForm;
				$this->view->inpage = 'Add';
				
			}
		}
		catch(Exception $e)
		{
			$this->view->ermsg = 'nodata';
		}
		if($this->getRequest()->getPost()){
			
			if($categoryForm->isValid($this->_request->getPost())){
				
				
				
				$id = $this->_request->getParam('id');

				$expense_category_name = $this->_request->getParam('expense_category_name');
				$createdby = $loginUserId;
				

				$date = gmdate("Y-m-d H:i:s");

				$data = array(
							'expense_category_name'  => $expense_category_name,
							'createdby'  => $createdby,
							'created_date'  => $date,
				);
				if($id!=''){
					$data['modifiedby'] = $loginUserId;
					$data['modifieddate'] = $date;
					$where = array('id=?'=>$id);
				}
				else
				{
					$data['expense_category_name']  = $expense_category_name;
					$data['createdby'] = $loginUserId;
					$data['created_date'] = $date;
					$data['modifieddate'] = $date;
					$data['isactive'] = 1;
					$where = '';
				}
				$Id = $categoriesmodel->saveOrUpdateCategoryData($data, $where);
				if($Id == 'update')
				{
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Expense Category Name updated successfully."));
				}
				else
				{
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Expense Category Name added successfully."));
				}
					
				$this->_redirect('expenses/expensecategories');
				
			}else
			{
				$messages = $categoryForm->getMessages();
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
	 * This action is used to delete the category details based on the  id.
	 *
	 */
	public function deleteAction(){

		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->_request->getParam('objid');
		$messages['message'] = ''; $messages['msgtype'] = '';
		$messages['flagtype'] = ''; 
		$actionflag = 3;
		if($id)
		{

			$categoriesmodel = new Expenses_Model_Categories();

			
			
			$isexist = $categoriesmodel->isCategoryExistForexpense($id);
			if($isexist==0)
			{
				$data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
				$data['modifiedby'] = $loginUserId;
				$where = array('id=?'=>$id);
				$id = $categoriesmodel->saveOrUpdateCategoryData($data, $where);
				if($id == 'update')
				{
					$messages['message'] = 'Category deleted successfully.';
					$messages['msgtype'] = 'success';
				}
				else
				{
					$messages['message'] = 'Category cannot be deleted.';
					$messages['msgtype'] = 'error';
				}
			}else
			{
			
				$messages['message'] = 'Category is in use. You cannot deleted the category.';
				$messages['msgtype'] = 'error';
			}
			
			
		}
		else
		{
			$messages['message'] = 'category cannot be deleted.';$messages['msgtype'] = 'error';
		}
		$this->_helper->json($messages);

	}
}
	