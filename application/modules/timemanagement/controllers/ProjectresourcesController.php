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
class Timemanagement_ProjectresourcesController extends Zend_Controller_Action
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
		$ajaxContext->addActionContext('viewemptasks', 'html')->initContext();
		$ajaxContext->addActionContext('addresourcesproject', 'html')->initContext();
		$ajaxContext->addActionContext('addresources', 'html')->initContext();
		$ajaxContext->addActionContext('deleteprojectresource', 'json')->initContext();
		$ajaxContext->addActionContext('taskassign', 'json')->initContext();
		$ajaxContext->addActionContext('assigntasktoresources', 'html')->initContext();
		$ajaxContext->addActionContext('resourcetaskdelete', 'json')->initContext();
		$ajaxContext->addActionContext('resourcetaskassigndelete', 'json')->initContext();

	}
	public function indexAction()
	{
		$auth = Zend_Auth::getInstance();
		$loginUserId=0;
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$projectResourcesModel = new Timemanagement_Model_Projectresources();
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

		$role = Zend_Registry::get( 'tm_role' );
		$dataTmp = $projectResourcesModel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall,$projectId,$loginUserId,$role);
		$dataTmp['projectId'] = $projectId;

		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
	}

	/*
	 * resources functionality
	 */
	public function resourcesAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
		$projectResourcesData = $projectData = array();
		$projectId = $this->getRequest()->getParam('projectid',null);
		$projectResourcesModel = new Timemanagement_Model_Projectresources();
		$resMessages = array();

		if($this->getRequest()->getPost()){
			//echo '<pre>'; print_r($this->getRequest()->getPost());exit;
			$resourceprojectIds = $this->getRequest()->getParam('hid_resourceprojid');
			$projectId = $this->getRequest()->getParam('project_id');
			$empIds = $this->getRequest()->getParam('hid_resourceid');
			$projectResourceData = array();
			if(count($resourceprojectIds) > 0){
				foreach($resourceprojectIds as $key=>$resourceProjectId){
					if($this->getRequest()->getParam('txt_cost_rate'.$resourceProjectId) != ''){
						$cost_rate =  $this->getRequest()->getParam('txt_cost_rate'.$resourceProjectId);
					}else{
						$cost_rate =  "";
					}
					if($this->getRequest()->getParam('txt_billable_rate'.$resourceProjectId) != ''){
						$billable_rate =  $this->getRequest()->getParam('txt_billable_rate'.$resourceProjectId);
					}else{
						$billable_rate =  "";
					}

					$projectResourceData = array( 'project_id'=>trim($projectId),
				               'emp_id'=>trim($empIds[$key]), 
							   'cost_rate'=> $cost_rate,
							   'billable_rate'=>$billable_rate, 
			     	           'is_active' => 1,
				   			   'modified_by'=>$loginUserId,
							   'modified'=>gmdate("Y-m-d H:i:s")	
					);
					$where = array('id=?'=>$resourceProjectId);
					$projectResourcesModel->SaveorUpdateProjectResourceData($projectResourceData,$where);
				}
			}
			$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Resource(s) updated successfully."));
			$this->_redirect('timemanagement/projectresources/resources/projectid/'.$projectId);
		}

		$projectResourcesModel = new Timemanagement_Model_Projectresources();
		$checkResourceExistsforProject = $projectResourcesModel->checkProjectResource($projectId,$loginUserId);
		$projectResourcesData = 'norows';
		if($loginUserId == 1 || $checkResourceExistsforProject > 0){
			try{
				if(is_numeric($projectId) && $projectId>0){
					$projectModel = new Timemanagement_Model_Projects();
					$projectData = $projectModel->getSingleProjectData($projectId);
					$projectResourcesData = $projectResourcesModel->getProjectResourcesData($projectId); //echo '<pre>';print_r($projectResourcesData); exit;

					if($projectResourcesData == 'norows')
					{
						//$projectData='norows';
						$this->view->rowexist = "norows";
					}
					else if(!empty($projectResourcesData))
					{
						$this->view->rowexist = "rows";

					}
				}
				else
				{
					$projectData='norows';
					$this->view->rowexist = "norows";
				}
			}catch(Exception $e)
			{
				$this->view->ermsg = 'nodata';
			}
		}else{
			$projectData='norows';
			$this->view->rowexist = "norows";
		}
		$this->view->projectData = $projectData;
		$this->view->projectResourcesData = $projectResourcesData;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
	}
	public function viewAction()
	{

	}

	public function addresourcesprojectAction()
	{
		$type=$this->_getParam('type');
		$projectId = $this->_getParam('projectId');
		$projectResourcesModel = new Timemanagement_Model_Projectresources();
		//$userModel = new Timemanagement_Model_Users();
		//print_r($userModel->getEmployees('manager'));exit;
		$otherResources = 'norows';
		if($type == 'manager'){
			$added_mngr_emp_str = $this->_getParam('addedempstr');
			//echo "<pre>";print_r($added_emp_str);exit;
			$added_mgr_arr = array();
			$added_mgr_data = '';
			if(isset($added_mngr_emp_str) && $added_mngr_emp_str != '')
			{
				$added_mgr_data = trim($added_mngr_emp_str,",");
				$added_mgr_arr = explode(",",$added_mgr_data);
			}
			$addedEmployees=$projectResourcesModel->getEmployeesAdded($projectId,$added_mgr_data);
			$notAddedManagers=$projectResourcesModel->getProjectNotAddedResource($projectId,$added_mgr_data,'manager');
		}
		if($type == 'emp'){
			$added_emp_str = $this->_getParam('addedempstr');
			$added_mgr_str = $this->_getParam('mngrstr');
				
			//echo "<pre>";print_r($added_emp_str);exit;
			$added_mgr_arr = array();
			$added_mgr_data = $added_emp_data='';
			if(isset($added_emp_str) && $added_emp_str != '')
			{
				$added_emp_data = trim($added_emp_str,",");
				$added_emp_arr = explode(",",$added_emp_data);
			}
			if(isset($added_mgr_str) && $added_mgr_str != '')
			{
				$added_mgr_data = trim($added_mgr_str,",");
			}
				
			$addedEmployees=$projectResourcesModel->getEmployeesAdded($projectId,$added_emp_data);
			$notAddedManagers=$projectResourcesModel->getProjectNotAddedResource($projectId,$added_emp_data,'emp',$added_mgr_data);
			$otherResources=$projectResourcesModel->getOtherEmployees($projectId,$added_emp_data,'emp',$added_mgr_data);
		}
		$this->view->type=$type;
		$this->view->projectId=$projectId;
		$this->view->addedEmployees = $addedEmployees;
		$this->view->notAddedManagers=$notAddedManagers;
		$this->view->otherResources = $otherResources;
	}

	public function addresourcesAction(){
		$auth = Zend_Auth::getInstance();  //echo '<pre>';print_r($auth->getStorage()->read()); exit;
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;

			$projectResourcesModel = new Timemanagement_Model_Projectresources();
			if($this->getRequest()->getPost()){
				$type = $this->_getParam('type');
				$projectId = $this->_getParam('projectId');
				$projRes = $this->_getParam('projRes');
				$projRes_arr = array();

				if(isset($projRes) && $projRes != '')
				{
					$projRes = trim($projRes,",");
					$projRes_arr = explode(",",$projRes);
				}

				foreach($projRes_arr as $newres){
					$projectResourceData = array( 'project_id'=>trim($projectId),
			               'emp_id'=>trim($newres), 
						   'created_by' => $loginUserId,
						   'created' =>gmdate("Y-m-d H:i:s"),
						   'is_active' => 1,
			   			   'modified_by'=>$loginUserId,
						   'modified'=>gmdate("Y-m-d H:i:s")	
					);
					$result = $projectResourcesModel->SaveorUpdateProjectResourceData($projectResourceData,'');
				}

				//$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"$type added successfully."));


				$projectResourcesData = $projectData = array();
				try{
					if(is_numeric($projectId) && $projectId>0){
						$projectModel = new Timemanagement_Model_Projects();
						$projectData = $projectModel->getSingleProjectData($projectId);
						$projectResourcesData = $projectResourcesModel->getProjectResourcesData($projectId); //echo '<pre>';print_r($projectResourcesData); exit;

						if($projectResourcesData == 'norows')
						{
							$this->view->rowexist = "norows";
						}
						else if(!empty($projectResourcesData))
						{
							$this->view->rowexist = "rows";

						}
					}
				}catch(Exception $e)
				{
					$this->view->ermsg = 'nodata';
				}
				$this->view->projectData = $projectData;
				$this->view->projectResourcesData = $projectResourcesData;
			}
		}
	}
	//view employee resources in pop up
	public function viewemptasksAction()
	{
		$projectId=$this->_getParam('projectId');
		$projectResourceId=$this->_getParam('projectResourceId');
		$dtask_model=new Timemanagement_Model_Projectresources();
		$empDetails = $dtask_model->getEmpDetails($projectResourceId);
		$dataTmp=$dtask_model->getEmpTasks($projectId,$projectResourceId);

		$this->view->projectId=$projectId;
		$this->view->projectResourceId=$projectResourceId;
		$this->view->dtask_arr=$dataTmp;
		$this->view->emp_arr=$empDetails;
	}

	//delete resource from project
	public function deleteprojectresourceAction(){
		$auth = Zend_Auth::getInstance();  //echo '<pre>';print_r($auth->getStorage()->read()); exit;
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$projectId=$this->_getParam('projectId');
			$projectresourceId=$this->_getParam('resourceProjectId');
			$empId=$this->_getParam('empId');
			$projResCount = '';

			$projectResourcesModel = new Timemanagement_Model_Projectresources();
			if($projectresourceId)
			{
				$checkProjectResourceDependency = $projectResourcesModel->checkProjectResourceDependency($projectId,$empId);
				if($checkProjectResourceDependency == 0){
					$data = array('is_active'=>0,'modified'=>gmdate("Y-m-d H:i:s"),'modified_by'=>$loginUserId);
					$where = array('id=?'=>$projectresourceId);

					$Id = $projectResourcesModel->SaveorUpdateProjectResourceData($data, $where);
					if($Id == 'update')
					{
						$task_resource_model=new Timemanagement_Model_Projecttaskresources();
						$projTaskEmpData = array('is_active'=>0,'modified'=>gmdate("Y-m-d H:i:s"),'modified_by'=>$loginUserId);
						$projTaskEmpWhere = array('project_id=?'=>$projectId,'emp_id=?'=>$empId);
						$update_data = array('is_active'=>0,'modified'=>gmdate("Y-m-d H:i:s"),'modified_by'=>$loginUserId);
						$where_cond = array('emp_id=?'=>$empId,'project_id=?'=>$projectId);
							
						$Id = $task_resource_model->SaveorUpdateProjectTaskResourceData($update_data, $where_cond);


						//sapp_Global::send_configuration_mail("Default Task", $taskData[0]['task']);
						$messages['message'] = 'Resource deleted successfully.';
						$messages['msgtype'] = 'success';
					}
					else
					{
						$messages['message'] = 'Resource cannot be deleted.';
						$messages['msgtype'] = 'error';
					}
				}else{
					$messages['message'] = 'Resource started working in project.';
					$messages['msgtype'] = 'error';
				}
			}
			$this->_helper->json(array('message'=>$messages['message'],'status'=>$messages['msgtype']));
		}
	}

	//function to show all tasks, assigned tasks, unassigned tasks
	public function assigntasktoresourcesAction()
	{
		$projectId=$this->_getParam('projectId');
		$employeeId=$this->_getParam('employeeId');
		$type=$this->_getParam('type');
		$this->view->projectId=$projectId;
		$this->view->employeeId=$employeeId;
		$this->view->type=$type;
		$dresource_model=new Timemanagement_Model_Projectresources();
		$getAllTasks = $dresource_model->getAllTasks($projectId);//get all tasks for perticuler project
		$empDetails = $dresource_model->getEmpDetails($employeeId);
		$assignedTasks = $dresource_model->getassignedTasks($projectId,$employeeId);
		$unAssignedTasks = array();
		$Alltasks_array = array();
		$assignesTaskIds = array();
		$is_project_in_timesheet = array();
		$project_task_primary_id = array();
		$tasks_array = array();
		foreach($getAllTasks as $taks)
		{
			$tasks_array[]=$taks['task_id'];
		}
		foreach($assignedTasks as $task_id)
		{
			$assignesTaskIds[] = $task_id['task_id'];
			$is_project_in_timesheet[$task_id['task_id']]=$task_id['projectTaskCount'];
			$project_task_primary_id[$task_id['task_id']]=$task_id['id'];
		}
		foreach($tasks_array as $tId)
		{
			if(!in_array($tId,$assignesTaskIds))
			{
				$unAssignedTasks[] = $tId;
			}
		}
		$unAssignedTasksArr = array();
		if(count($unAssignedTasks)>0)
		{
			foreach($unAssignedTasks as $taskId)
			{
				$unAssignedTasksArr[] = $dresource_model->getAllTasks($projectId,$taskId);
			}
		}
		//get unassigned tasks
		$this->view->assignedTasks=$assignedTasks;
		$this->view->empDetails=$empDetails;
		$this->view->getAllTasks=$getAllTasks;
		$this->view->unAssignedTaskIds=$unAssignedTasks;
		$this->view->unAssignedTasks=$unAssignedTasksArr;
		$this->view->is_project_in_timesheet=$is_project_in_timesheet;

	}
	public function taskassignAction()
	{
		$task_resource_model=new Timemanagement_Model_Projecttaskresources();
		$projectId = $this->_getParam('projectId');
		$taskIds=$this->_getParam('taskids');
		$checkedTaskIds = json_decode($taskIds);
		$employeeId=$this->_getParam('employeeId');
		$projectTaskids=$this->_getParam('projecttaskids');
		$ProjectTaskId = json_decode($projectTaskids);
		$success_count=0;
		if(count($checkedTaskIds)>0)
		{
			foreach($checkedTaskIds as $key=>$task_id)
			{
				$isTaskAssigned = $task_resource_model->isTaskAssigned($projectId,$task_id,$employeeId);
				//insert tasks in employee_tasks table
				if($isTaskAssigned==0)
				{
					$is_inserted = $task_resource_model->assignTasks($task_id,$projectId,$employeeId,$ProjectTaskId[$key]);
					if($is_inserted>0)
					{
						$success_count++;
					}
				}
			}
			$this->_helper->json(array('message'=>'success','status'=>'Tasks assigned to resource successfully.'));
		}
	}
	public function resourcetaskdeleteAction()
	{
		$task_resource_model=new Timemanagement_Model_Projecttaskresources();
		$projectId=$this->_getParam('projectId');
		$employeeId=$this->_getParam('employeeId');
		$taskids=$this->_getParam('taskids');
		$selectedTaskIds = json_decode($taskids);
		$projectTaskids=$this->_getParam('projecttaskids');
		$ProjectTaskId = json_decode($projectTaskids);//primary key in tm_project_task_employees table for update
		$success_count=0;
		if(count($selectedTaskIds)>0)
		{
			foreach($selectedTaskIds as $key=>$task_id)
			{
				//insert tasks in employee_tasks table
				$is_updated = $task_resource_model->assignTasks($task_id,$projectId,$employeeId,$ProjectTaskId[$key],$for_update=1);
				if($is_updated=='update')
				{
					$success_count++;
				}
			}
			$this->_helper->json(array('message'=>'success','status'=>'Tasks deleted successfully.'));
		}
	}
	public function resourcetaskassigndeleteAction()
	{
		$dresource_model=new Timemanagement_Model_Projectresources();
		$task_resource_model=new Timemanagement_Model_Projecttaskresources();
		$projectId=$this->_getParam('projectId');
		$employeeId=$this->_getParam('employeeId');
		$checkedTasks=$this->_getParam('chekedtaskids');
		$uncheckedTasks=$this->_getParam('uncheckedtaskids');
		$checkedTaskIds = json_decode($checkedTasks);
		$uncheckedTasksIds = json_decode($uncheckedTasks);
		$allTaskIdsArray = array();
		$project_task_id = array();
		$inserted_count=0;
		$updated_count=0;
		$getAllTasks = $dresource_model->getAllTasks($projectId);//get all tasks for perticuler project
		foreach($getAllTasks as $task_id)
		{
			$allTaskIdsArray[] = $task_id['task_id'];
			$project_task_id[$task_id['task_id']]=$task_id['id'];
		}
		$assignedTasks = $dresource_model->getassignedTasks($projectId,$employeeId);
		$assignedTaskIds = array();
		$resourceProjectTaskPrimaryId = array();

		if(count($assignedTasks)>0)
		{
			foreach($assignedTasks as $id)
			{
				$assignedTaskIds[] = $id['task_id'];
				$resourceProjectTaskPrimaryId[$id['task_id']]=$id['id'];

			}
		}
		$unAssignedTasks = array();
		foreach($allTaskIdsArray as $tId)
		{
			if(!in_array($tId,$assignedTaskIds))
			{
				$unAssignedTasks[] = $tId;
			}
		}
		//user selected to delete tasks from resources
		//compare previoues assigned tasks and selected to delete tasks
		if(count($uncheckedTasksIds)>0)//delete resource task
		{
			foreach($uncheckedTasksIds as $ckedId)
			{
				if(in_array($ckedId,$assignedTaskIds))
				{
					$is_updated = $task_resource_model->assignTasks($ckedId,$projectId,$employeeId,$resourceProjectTaskPrimaryId[$ckedId],$for_update=1);
					if($is_updated=='update')
					{
						$updated_count++;
					}
				}
			}
		}
		if(count($checkedTaskIds)>0)//insert resource task
		{
			foreach($checkedTaskIds as $taskId)
			{
				if(in_array($taskId,$unAssignedTasks))
				{
					//insert tasks in employee_tasks table
					$isTaskAssigned = $task_resource_model->isTaskAssigned($projectId,$taskId,$employeeId);
					if($isTaskAssigned==0)
					{
						$is_inserted = $task_resource_model->assignTasks($taskId,$projectId,$employeeId,$project_task_id[$taskId]);
						if($is_inserted>0)
						{
							$inserted_count++;
						}
					}
				}
			}
		}
		//$this->_helper->json(array('message'=>'success','status'=>$updated_count.' Tasks deleted successfully,'.$inserted_count.' Tasks assigned successfully.'));
		$this->_helper->json(array('message'=>'success','status'=>'Tasks list updated successfully.'));

	}

}
