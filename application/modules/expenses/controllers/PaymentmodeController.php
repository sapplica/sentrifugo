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

class Expenses_PaymentmodeController extends Zend_Controller_Action
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

		$paymentmodemodel = new Expenses_Model_Paymentmode();
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

			$sort = 'DESC';$by = 'p.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';
			$searchArray = array();
		}
		else
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'p.modifieddate';
			if($dashboardcall == 'Yes')
			$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
			else
			$perPage = $this->_getParam('per_page',PERPAGE);
			$pageNo = $this->_getParam('page', 1);
			$searchData = $this->_getParam('searchData');
			$searchData = rtrim($searchData,',');
		}
		$dataTmp = $paymentmodemodel->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall);
		
		

		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
		
		
	}
	
	
	
	
	
	
		/**
	 * This Action is used to Create/Update the payment details based on the  id.
	 *
	 */
	public function editAction(){
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$objName = 'paymentmode';
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();

		$paymentForm = new Expenses_Form_Paymentmode();
		$paymentModel = new Expenses_Model_Paymentmode();
		try{
			if($id)
			{	//Edit
				if(is_numeric($id) && $id>0)
				{
					$data = $paymentModel->getsinglePaymentstatusData($id);
					
					if(!empty($data))
					{
                              $paymentForm = new Expenses_Form_Paymentmode();
								$paymentForm->submit->setLabel('Update');

								$paymentForm->populate($data);
								$paymentForm->setDefault('payment_method_name',$data['payment_method_name']);
								$this->view->form = $paymentForm;
								
						
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
				$this->view->form = $paymentForm;
				$this->view->inpage = 'Add';
			}
		}
		catch(Exception $e)
		{
			$this->view->ermsg = 'nodata';
		}
		if($this->getRequest()->getPost()){
			
			if($paymentForm->isValid($this->_request->getPost())){
				
				
				
				$id = $this->_request->getParam('id');

				$payment_method_name = $this->_request->getParam('payment_method_name');
				$createdby = $loginUserId;
				

				$date = gmdate("Y-m-d H:i:s");

				$data = array(
							'payment_method_name'  => $payment_method_name,
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
					$data['payment_method_name']  = $payment_method_name;
					$data['createdby'] = $loginUserId;
					$data['created_date'] = $date;
					$data['modifieddate'] = $date;
					$data['isactive'] = 1;

					$where = '';
				}
				$Id = $paymentModel->saveOrUpdatePaymentmodeData($data, $where);
				if($Id == 'update')
				{
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Payment mode updated successfully."));
				}
				else
				{
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Payment mode added successfully."));
				}
					
				$this->_redirect('expenses/paymentmode');
				
			}else
			{
				$messages = $paymentForm->getMessages();
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
	 * This action is used to delete the payment details based on the  id.
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
			$paymentModel = new Expenses_Model_Paymentmode();
			
			
			$isexist = $paymentModel->isPaymentExistForexpense($id);
			if($isexist==0)
			{
				$data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
				$data['modifiedby'] = $loginUserId;
				$where = array('id=?'=>$id);
				$id = $paymentModel->saveOrUpdatePaymentmodeData($data, $where);
				if($id == 'update')
				{
					$messages['message'] = 'Payment mode deleted successfully.';
					$messages['msgtype'] = 'success';
				}
				else
				{
					$messages['message'] = 'Payment mode cannot be deleted.';
					$messages['msgtype'] = 'error';
				}
			}else
			{
				
				$messages['message'] = 'Payment mode is in use. You cannot deleted the payment.';
				$messages['msgtype'] = 'error';
			}
			
			
		}
		else
		{
			$messages['message'] = 'payment mode cannot be deleted.';$messages['msgtype'] = 'error';
		}
		$this->_helper->json($messages);

	}
}

