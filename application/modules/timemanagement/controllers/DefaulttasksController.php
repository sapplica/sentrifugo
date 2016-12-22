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

class Timemanagement_DefaulttasksController extends Zend_Controller_Action
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

	public function indexAction()
	{
		$taskModel = new Timemanagement_Model_Tasks();
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

		$dataTmp = $taskModel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);
			
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
		$objName = 'defaulttasks';
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();

		$taskForm = new Timemanagement_Form_Task();
		$taskModel = new Timemanagement_Model_Tasks();
		try
		{
			if($id)
			{	//Edit Record...
				if(is_numeric($id) && $id>0)
				{
					$data = $taskModel->getTaskData($id);
					if(!empty($data) && $data != "norows")
					{
						$taskForm->populate($data[0]);
						$taskForm->submit->setLabel('Update');
						$this->view->form = $taskForm;
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
				$this->view->form = $taskForm;
				$this->view->inpage = 'Add';
			}
		}
		catch(Exception $e)
		{
			$this->view->ermsg = 'nodata';
		}
		if($this->getRequest()->getPost()){
			if($taskForm->isValid($this->_request->getPost())){
				$id = $this->_request->getParam('id');
				$task= $this->_request->getParam('task');
				$date = new Zend_Date();

				$data = array( 'task'=>trim($task),
				               'is_default'=>1, 
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
				$Id = $taskModel->SaveorUpdateTaskData($data, $where);
				if($Id == 'update')
				{
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Default task updated successfully."));
				}
				else
				{
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Default task added successfully."));
				}
					
				$this->_redirect('timemanagement/defaulttasks');
			}else
			{
				$messages = $taskForm->getMessages();
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
		$objName = 'defaulttasks';

		$taskModel = new Timemanagement_Model_Tasks();
		try
		{
			if(is_numeric($id) && $id>0)
			{
				$data = $taskModel->getTaskData($id);
				if(!empty($data) && $data != "norows")
				{
					$this->view->id = $id;
					$this->view->data = $data;
					$this->view->controllername=$objName;
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

	public function deleteAction()
	{
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
			$taskModel = new Timemanagement_Model_Tasks();
			$checkProjects = $taskModel->checkProjectTasks($id);
			if($checkProjects == 0){
				$data = array('is_active'=>0,'modified'=>gmdate("Y-m-d H:i:s"));
				$where = array('id=?'=>$id);
				$taskData = $taskModel->getTaskData($id);
				$Id = $taskModel->SaveorUpdateTaskData($data, $where);
				if($Id == 'update')
				{
					//sapp_Global::send_configuration_mail("Default Task", $taskData[0]['task']);
					$messages['message'] = 'Default task deleted successfully.';
					$messages['msgtype'] = 'success';
				}
				else
				{
					$messages['message'] = 'Default task cannot be deleted.';
					$messages['msgtype'] = 'error';
				}
			}else{
				$messages['message'] = 'Task in use. You cannot delete the default task';
				$messages['msgtype'] = 'error';
			}
		}
		else
		{
			$messages['message'] = 'Default task cannot be deleted.';$messages['msgtype'] = 'error';
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
	}//end of delete

	public function checkduptaskAction()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('checkDupTask', 'json')->initContext();

		$taskname=$this->_getParam('taskname');

		$taskModel = new Timemanagement_Model_Tasks();
		$taskexists = $taskModel->getcheckDupTask($taskname);

		if(count($taskexists) > 0){
			$this->_helper->json(array('result'=>'exists'));
		}else{
			$this->_helper->json(array('result'=>'notexists'));
		}

	}
}