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
class Expenses_MyemployeeexpensesController extends Zend_Controller_Action
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
		$expensesModel = new Expenses_Model_Myemployeeexpenses();		
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
			$sort = 'DESC';$by = 'ex.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
			//echo "here";exit;
		}
		else
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'ex.modifieddate';
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

