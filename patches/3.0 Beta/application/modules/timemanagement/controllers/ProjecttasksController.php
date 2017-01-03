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
class Timemanagement_ProjecttasksController extends Zend_Controller_Action
{
	private $options;
	/**
	 * Init.
	 *
	 * @see Zend_Controller_Action::init()
	 */
	public function init()
	{
		$this->_options = $this->getInvokeArg('bootstrap')->getOptions();
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

		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('viewtasksresources', 'html')->initContext();
		$ajaxContext->addActionContext('deletetask', 'json')->initContext();
		$ajaxContext->addActionContext('assignresourcestotask', 'html')->initContext();
		$ajaxContext->addActionContext('saveresources', 'json')->initContext();
		$ajaxContext->addActionContext('edittaskname', 'json')->initContext();

	}


	/*
	 * index functionality
	 */
	public function indexAction()
	{
		$projectTaskModel = new Timemanagement_Model_Projecttasks();
		$call = $this->_getParam('call');
		if($call == 'ajaxcall')
		$this->_helper->layout->disableLayout();
		$view = Zend_Layout::getMvcInstance()->getView();
		$objname = $this->_getParam('objname');
		$refresh = $this->_getParam('refresh');
		$dashboardcall = $this->_getParam('dashboardcall');
		$projectId = $this->_getParam('projectId');

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

		$dataTmp = $projectTaskModel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall,$projectId);
		$dataTmp['projectId'] = $projectId;

		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();

	}
	/* function to show resources in pop up*/
	public function viewtasksresourcesAction()
	{
		$projectId=$this->_getParam('projectId');
		$projectTaskId=$this->_getParam('projectTaskId');
		$dtask_model=new Timemanagement_Model_Projecttasks();
		$dataTmp=$dtask_model->getTasksResources($projectId,$projectTaskId);
		$tasks_data=$dtask_model->getTasks($projectId,$projectTaskId);
		$this->view->projectId=$projectId;
		$this->view->projectTaskId=$projectTaskId;
		$this->view->dtask_arr=$dataTmp;
		$this->view->task_arr=$tasks_data;
	}

	/* function to delete a task from project*/
	public function deletetaskAction(){

		$auth = Zend_Auth::getInstance();  //echo '<pre>';print_r($auth->getStorage()->read()); exit;
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}

		$projectId=$this->_getParam('projectId');
		$projectTaskId=$this->_getParam('projectTaskId');
		$taskId=$this->_getParam('taskId');
		$projectTaskModel = new Timemanagement_Model_Projecttasks();
		$TaskModel = new Timemanagement_Model_Tasks();
		$projectTasResourceModel = new Timemanagement_Model_Projecttaskresources();
		if($projectTaskId)
		{
			$checkProjectTaskDependency = $projectTaskModel->checkProjectTaskDependency($projectId,$projectTaskId);
			
			if($checkProjectTaskDependency == 0){
				$data = array('is_active'=>0,'modified'=>gmdate("Y-m-d H:i:s"),'modified_by'=>$loginUserId);
				$where = array('id=?'=>$projectTaskId);
					
				$Id = $projectTaskModel->SaveorUpdateProjectTaskData($data, $where);
				if($Id == 'update')
				{
					$update_data = array('is_active'=>0,'modified'=>gmdate("Y-m-d H:i:s"),'modified_by'=>$loginUserId);
					$where_cond = array('project_task_id=?'=>$projectTaskId,'project_id=?'=>$projectId);
					$update_is_active = $projectTasResourceModel->SaveorUpdateProjectTaskResourceData($update_data,$where_cond);
					
					$taskAssignedCount = $projectTasResourceModel->getAssignedTaskCount($taskId,$projectId);
					if($taskAssignedCount==0)
					{
						$task_where = array('id=?'=>trim($taskId));
						$task_data = array( 'is_active'=>0,
				                   'modified_by'=>$loginUserId,
							       'modified'=>gmdate("Y-m-d H:i:s")	
						);
						$updateisactiveintm_task = $TaskModel->SaveorUpdateTaskData($task_data,$task_where);
					}
					
					//sapp_Global::send_configuration_mail("Default Task", $taskData[0]['task']);
					$messages['message'] = 'Task deleted successfully.';
					$messages['msgtype'] = 'success';
				}
				else
				{
					$messages['message'] = 'Task cannot be deleted.';
					$messages['msgtype'] = 'error';
				}
			}else{
				$messages['message'] = 'Project Task in use.';
				$messages['msgtype'] = 'error';
			}
		}
		$this->_helper->json(array('message'=>$messages['message'],'status'=>$messages['msgtype']));
	}


	public function assignresourcestotaskAction(){
		$projectId = $this->_getParam('projectId');
		$taskId = $this->_getParam('taskId');
		$projectTaskId = $this->_getParam('projectTaskId');

		$projectTaskModel = new Timemanagement_Model_Projecttasks();
		if($projectTaskId){
			$projectEmployees = $projectTaskModel->getProjectEmployees($projectId);
			$projectTaskEmployees = $projectTaskModel->getProjectTaskEmployees($projectId,$taskId,$projectTaskId);
			$taskDetails = $projectTaskModel->getProjectTaskDetails($projectTaskId);
			$assignedEmployees = array();
			$notAssignedEmployees = array();

			$existedEmpArray = $this->array_column($projectTaskEmployees, 'emp_id');

			if(count($projectEmployees) > 0){
				foreach($projectEmployees as $projectEmp){
					if(in_array($projectEmp['user_id'], $existedEmpArray)){
						$key = array_search($projectEmp['user_id'], $existedEmpArray);
						$projectEmp['tsEmptaskCnt'] = $projectTaskEmployees[$key]['tsEmptaskCnt'];
						$assignedEmployees[] = $projectEmp;
					}else{
						$notAssignedEmployees[] = $projectEmp;
					}
				}
			}

			$this->view->projectTaskId = $projectTaskId;
			$this->view->assignedEmployees = $assignedEmployees;
			$this->view->notAssignedEmployees = $notAssignedEmployees;
			$this->view->existedEmpIdArray =$existedEmpArray;

			//$this->view->projectEmployees = $projectEmployees;
			$this->view->taskDetails = $taskDetails;
			$this->view->projectTaskEmployees = $projectTaskEmployees;
		}
	}

	//function to create single key values from a multidimensional array
	function array_column(array $input, $columnKey, $indexKey = null) {
		$array = array();
		foreach ($input as $value) {
			if ( ! isset($value[$columnKey])) {
				trigger_error("Key \"$columnKey\" does not exist in array");
				return false;
			}
			if (is_null($indexKey)) {
				$array[] = $value[$columnKey];
			}
			else {
				if ( ! isset($value[$indexKey])) {
					trigger_error("Key \"$indexKey\" does not exist in array");
					return false;
				}
				if ( ! is_scalar($value[$indexKey])) {
					trigger_error("Key \"$indexKey\" does not contain scalar value");
					return false;
				}
				$array[$value[$indexKey]] = $value[$columnKey];
			}
		}
		return $array;
	}

	public function saveresourcesAction(){
		$auth = Zend_Auth::getInstance();  //echo '<pre>';print_r($auth->getStorage()->read()); exit;
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;

			$projectTaskResourceModel = new Timemanagement_Model_Projecttaskresources();
			if($this->getRequest()->getPost()){
				$projectId = $this->_getParam('projectId');
				$oldRes = $this->_getParam('oldRes');
				$newRes = $this->_getParam('newRes');
				$taskId = $this->_getParam('taskId');
				$projectTaskId = $this->_getParam('projectTaskId');
					
				$oldRes_arr = $newRes_arr = array();
				//echo $oldRes.'</br>';
				if(isset($oldRes) && $oldRes != '')
				{
					$projRes = trim($oldRes,",");
					$oldRes_arr = explode(",",$oldRes);
				}
				//echo $newRes.'</br>';
				if(isset($newRes) && $newRes != '')
				{
					$projRes = trim($newRes,",");
					$newRes_arr = explode(",",$newRes);
				}

				if(!empty($newRes_arr)){
				 foreach($newRes_arr as $newRess){
				 	if(in_array($newRess, $oldRes_arr)){
				 		$key = array_search($newRess, $oldRes_arr); //echo $key;
				 		unset($oldRes_arr[$key]); // check already exists or not
				 		continue;
				 	}else{
				 		$checkprojTaskResRecExists = $projectTaskResourceModel->isTaskAssigned(trim($projectId),trim($taskId),trim($newRess));

				 		if(empty($checkprojTaskResRecExists)){
				 			$projectTaskResourceData = array( 'project_id'=>trim($projectId),
										             'task_id'=>trim($taskId), 
				 	                                 'project_task_id'=>trim($projectTaskId), 
				 	                                 'emp_id'=>trim($newRess),
													 'created_by' => $loginUserId,
													 'created' =>gmdate("Y-m-d H:i:s"),
													 'is_active' => 1,
										   			 'modified_by'=>$loginUserId,
													 'modified'=>gmdate("Y-m-d H:i:s")	
				 			);
				 			 
				 			$result = $projectTaskResourceModel->SaveorUpdateProjectTaskResourceData($projectTaskResourceData,'');
				 		}
				 		//echo $result;
				 	}
				 }
				}

				if(!empty($oldRes_arr)){ // now $oldRes_arr contains removed res id's
					foreach($oldRes_arr as $removedRes){
						$projectTaskResourceData_remove = array( 'is_active' => 0,
										   			       'modified_by'=>$loginUserId,
													       'modified'=>gmdate("Y-m-d H:i:s")	
						);
						$where = array('project_id=?'=>trim($projectId),'project_task_id=?'=>trim($projectTaskId),'emp_id=?'=>trim($removedRes));
						$result = $projectTaskResourceModel->SaveorUpdateProjectTaskResourceData($projectTaskResourceData_remove,$where);
					}
				}
				$this->_helper->json(array('message'=>'success','status'=> 'Resource assigned to tasks successfully.'));
			}
		}
	}

	public function edittasknameAction(){
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;

			if($this->getRequest()->getPost()){
				$projectTaskModel = new Timemanagement_Model_Projecttasks();
				$projectId = $this->_getParam('projectId');
				$taskId = $this->_getParam('taskId');
				$taskName = $this->_getParam('taskName');

				$checkTaskNameExistAlready = $projectTaskModel->getProjTaskNameExists($projectId,$taskId,$taskName);
				if($checkTaskNameExistAlready[0]['taskNameExistsCount'] > 0){
					$this->_helper->json(array('status'=>'error','message'=> 'Task name already exists in default task or in project task.'));
				}else{
					$data = array( 'task'=>trim($taskName),
				                   'modified_by'=>$loginUserId,
							       'modified'=>gmdate("Y-m-d H:i:s")	
					);
					if($taskId!=''){
						$taskModel = new Timemanagement_Model_Tasks();
						$where = array('id=?'=>trim($taskId));
						$Id = $taskModel->SaveorUpdateTaskData($data, $where);
						if($Id == 'update')
						{
							$this->_helper->json(array('status'=>'success','message'=> 'Task name updated successfully.'));
						}
					}
				}
			}
		}
	}

}
