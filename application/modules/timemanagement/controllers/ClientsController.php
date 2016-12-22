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
 * @Name   Clients Controller
 *
 * @description
 *
 * This Client controller contain actions related to client.
 *
 * 1. Display all client details.
 * 2. Save or Update Client details.
 * 3. Delete Client.
 * 4. View Client details.
 *
 * @author sagarsoft
 * @version 1.0
 */
class Timemanagement_ClientsController extends Zend_Controller_Action
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
	 * This method will display all the client details in grid format.
	 */
	public function indexAction()
	{
		$clientsModel = new Timemanagement_Model_Clients();
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

		$dataTmp = $clientsModel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);
			
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
	}

	/**
	 * This Action is used to Create/Update the client details based on the client id.
	 *
	 */
	public function editAction(){
		
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$objName = 'clients';
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();

		$clientsForm = new Timemanagement_Form_Clients();
		$clientsModel = new Timemanagement_Model_Clients();
		//echo "routing test";
		try{
			if($id)
			{	//Edit Record...
				if(is_numeric($id) && $id>0)
				{
					$data = $clientsModel->getClientDetailsById($id);
					if(!empty($data) && $data != "norows")
					{
						$statesModel = new Timemanagement_Model_States();
						$statesData = $statesModel->getStatesByCountryId($data[0]['country_id']);
						foreach ($statesData as $state){
							$clientsForm->state_id->addMultiOption($state['id'],utf8_encode($state['state_name']));
						}

						$clientsForm->populate($data[0]);
						$clientsForm->submit->setLabel('Update');
						$this->view->form = $clientsForm;
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
				$this->view->form = $clientsForm;
				$this->view->inpage = 'Add';
			}
		}
		catch(Exception $e)
		{
			$this->view->ermsg = 'nodata';
		}
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
				if($id!=''){
					$data['modified_by'] = $loginUserId;
					$data['modified'] = $date;
					$where = array('id=?'=>$id);
				}
				else
				{
					$data['created_by'] = $loginUserId;
					$data['created'] = $date;
					$data['modified'] = $date;
					$data['is_active'] = 1;
					$where = '';
				}
				$Id = $clientsModel->saveOrUpdateClientsData($data, $where);
				if($Id == 'update')
				{
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Client updated successfully."));
				}
				else
				{
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Client added successfully."));
				}
					
				$this->_redirect('timemanagement/clients');
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

	}

	/**
	 * This Action is used to view the client details based on the client id.
	 *
	 */
	public function viewAction(){

		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();
		$objName = 'clients';

		$clientsModel = new Timemanagement_Model_Clients();

		try
		{
			if(is_numeric($id) && $id>0)
			{
				$data = $clientsModel->getClientDetailsById($id);
				if(!empty($data) && $data != "norows")
				{
					$this->view->data = $data;
					$this->view->controllername=$objName;
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
			$clientsModel = new Timemanagement_Model_Clients();
			$checkProjects = $clientsModel->checkProjectClients($id);
			if($checkProjects == 0){
				$data = array('is_active'=>0,'modified'=>gmdate("Y-m-d H:i:s"));
				$data['modified_by'] = $loginUserId;
				$where = array('id=?'=>$id);
				$id = $clientsModel->saveOrUpdateClientsData($data, $where);
				if($id == 'update')
				{
					$messages['message'] = 'Client deleted successfully.';
					$messages['msgtype'] = 'success';
				}
				else
				{
					$messages['message'] = 'Client cannot be deleted.';
					$messages['msgtype'] = 'error';
				}
			}else{
				$messages['message'] = 'Client is in use. You cannot delete the client';
				$messages['msgtype'] = 'error';
			}
		}
		else
		{
			$messages['message'] = 'Client cannot be deleted.';$messages['msgtype'] = 'error';
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

}

