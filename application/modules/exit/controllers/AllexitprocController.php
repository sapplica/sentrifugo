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
** controller used to list down all raised exit procedures based on logged in user id
** super admin can view all exit procs and edit any segment in the proc
**/
class Exit_AllexitprocController extends Zend_Controller_Action
{
	private $options;
	private $loggedInUser = '';
	private $loggedInUserGroup = '';
	private $loggedInUserRole = '';

	public function preDispatch()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('updateexitprocess', 'json')->initContext();
	}
	public function init()
	{
		$this->_options = $this->getInvokeArg('bootstrap')->getOptions();
		
		/** Instantiate exit proc settings model **/
		$this->allExitProcsModel = new Exit_Model_Allexitprocs();
		$this->exitProcModel = new Exit_Model_Exitproc();
		$this->exitquestionsmodel = new Exit_Model_Exitquestions();
		$this->exitquesresponse =	new Exit_Model_Questionsresponse();
		/**
		** Initiating zend auth object
		** for getting logged in user id
		**/
		$auth = Zend_Auth::getInstance();
		
		if($auth->hasIdentity())
		{
			$this->loggedInUser = $auth->getStorage()->read()->id;
			$this->loggedInUserRole = $auth->getStorage()->read()->emprole;
			$this->loggedInUserGroup = $auth->getStorage()->read()->group_id;
			$this->userfullname = $auth->getStorage()->read()->userfullname;
			$this->reporting_manager = $auth->getStorage()->read()->reporting_manager;
		}
	}
	public function indexAction()
	{
		/**
		**	check for ajax call
		**/
		$call = $this->_getParam('call');
		if($call == 'ajaxcall')
				$this->_helper->layout->disableLayout();	
		
		/**
		** capture request parameters 
		**/
		$objname = $this->_getParam('objname');
		$refresh = $this->_getParam('refresh');
		$dashboardcall = $this->_getParam('dashboardcall');
		$context = $this->_getParam('context');
		$data = array();
		$searchQuery = '';
		$searchArray = array();
		$tablecontent='';
		
		// based on parameter diffrentiate grid display
		$con =$this->_request->getParam('con');
		if($con=='')
		{
			$con =	sapp_Global::_encrypt(1);
		}
		$status =  sapp_Global::_decrypt($con);
		
		/** check for refresh button event 
		**  If yes, build default parameters
		**	else, build parameters accordingly
		**/
		if($refresh == 'refresh')
		{
			if($dashboardcall == 'Yes')
				$perPage = DASHBOARD_PERPAGE;
			else	
				$perPage = PERPAGE;

			$sort = 'DESC';$by = 'e.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else 
		{
			/** build sort parameter **/
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';

			/** build order by parameter **/
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'e.modifieddate';
			
			/** get records per page parameter **/
			if($dashboardcall == 'Yes')
				$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
			else 
				$perPage = $this->_getParam('per_page',PERPAGE);

			/** set page number parameter **/
			$pageNo = $this->_getParam('page', 1);
			
			/** build search parameters **/
			$searchData = $this->_getParam('searchData');	
			$searchData = rtrim($searchData,',');					
		}

		/**
		**	based on search, sort, pagination, order by parameters 
		**  get data object to build exit proc settings grid
		**/
		$dataTmp = $this->allExitProcsModel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall,$context,'','','',$status);
		array_push($data,$dataTmp);

		//count of all exit records
		$allcount=$this->allExitProcsModel->getAllExitProcs('count',$sort, $by, '', '','','',1);
		$allexitcount= count($allcount);
		
		//count of approved exit records
		$approvedcount=$this->allExitProcsModel->getAllExitProcs('count',$sort,$by, '', '','','',2);
		$approvedexitcount= count($approvedcount);
		
		//count of rejected exit records
		$rejectedcount=$this->allExitProcsModel->getAllExitProcs('count',$sort,$by, '', '','','',3);
		$rejectedexitcount= count($rejectedcount);
		
		//count of pending exit records
		$pendingcount=$this->allExitProcsModel->getAllExitProcs('count',$sort,$by, '', '','','',4);
		$pendingexitcount= count($pendingcount);

		/**
		** send data objects to view
		**/
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->allcount = $allexitcount;	
		$this->view->approvedcount = $approvedexitcount;
		$this->view->rejectedexitcount = $rejectedexitcount;	
		$this->view->pendingexitcount = $pendingexitcount;
		$this->view->status = $con;			
	
		
		/**
		** send flash messages to view
		**/
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
	}

	public function editAction()
	{
		/**
		** check for logged in user role for add privileges
		**/
		if(sapp_Global::_checkprivileges(ALL_EXIT_PROCS,$this->loggedInUserGroup,$this->loggedInUserRole,'edit') != 'Yes'){
				$this->view->ermsg = 'noprivileges';
				exit;
		} 
		
		/**
		** capture record id to edit
		**/
		$id = (int)$this->_request->getParam('id');
		$this->view->id = $id;
		
		$exitProcDetails = $this->allExitProcsModel->getExitProcDetails($id);


		/**
		** getting hr manager, sys admin, general admin, finance manager details - START
		**/
		
		if(!empty($exitProcDetails))
		{
			$exitProcDetails = $exitProcDetails[0];
			$other_manager='';
			/* update form post values START*/
			$status = "";
			$comments = "";
			$selected_status_value="";
			$selected_comment_value="";
			$is_valid_user = 0;
			if($this->loggedInUser == $exitProcDetails['reporting_manager']){
				$status = "l1m";
				$comments = "l1mcomments";
				$selected_status_value="l1_status";
				$selected_comment_value="l1_comments";
				$is_valid_user=1;
				
				
				//check if l1 manager is matching with any setting confugured managers
				//get employee reporting_manager
				$emp_reporting_manager = $exitProcDetails['reporting_manager'];
				
				$configured_manager_arr = array('L2 manager'=>$exitProcDetails['l2_manager'],'Hr manager'=>$exitProcDetails['hr_manager'],'System admin'=>$exitProcDetails['sys_admin'],'General admin'=>$exitProcDetails['general_admin'],'Finance manager'=>$exitProcDetails['finance_manager']);
				
				
				if(in_array($emp_reporting_manager, $configured_manager_arr))
				{
					$other_manager = array_search($emp_reporting_manager, $configured_manager_arr);
				}
				
			}else if(!empty($exitProcDetails['l2_manager']) && ($this->loggedInUser == $exitProcDetails['l2_manager'] )){
				$status = "l2m";
				$comments = "l2mcomments";
				$selected_status_value="l2_status";
				$selected_comment_value="l2_comments";
				$is_valid_user=1;
				
			}else if($this->loggedInUser == $exitProcDetails['hr_manager']){
				$status = "hrm";
				$comments = "hrmcomments";
				$selected_status_value="hr_manager_status";
				$selected_comment_value="hr_manager_comments";
				$is_valid_user=1;
				
			}else if(!empty($exitProcDetails['sys_admin']) && ($this->loggedInUser == $exitProcDetails['sys_admin'])){
				$status = "sysadm";
				$comments = "sysadmcomments";
				$selected_status_value="sys_admin_status";
				$selected_comment_value="sys_admin_comments";
				$is_valid_user=1;
				
			}else if(!empty($exitProcDetails['general_admin']) && ($this->loggedInUser == (int)$exitProcDetails['general_admin'] ))
			{
				$status = "gam";
				$comments = "gamcomments";
				$selected_status_value="gen_admin_status";
				$selected_comment_value="gen_admin_comments";
				$is_valid_user=1;
			}else if(!empty($exitProcDetails['finance_manager']) && ($this->loggedInUser == (int)$exitProcDetails['finance_manager'])){
				$status = "fm";
				$comments = "fmcomments";
				$selected_status_value="fin_admin_status";
				$selected_comment_value="fin_admin_comments";
				$is_valid_user=1;
			}
			
			$this->view->status = $status;
			$this->view->comments = $comments;
			$this->view->selected_status_value = $selected_status_value;
			$this->view->selected_comment_value = $selected_comment_value;
			$this->view->is_valid_user = $is_valid_user;
			$this->view->other_manager = $other_manager;
			/* update form post values END*/

			$empIdsStr = '';
			if(!empty($exitProcDetails['hr_manager']))
				$empIdsStr .= $exitProcDetails['hr_manager'];
			if(!empty($exitProcDetails['sys_admin']))
				$empIdsStr .= ",".$exitProcDetails['sys_admin'];
			if(!empty($exitProcDetails['general_admin']))
				$empIdsStr .= ",".$exitProcDetails['general_admin'];
			if(!empty($exitProcDetails['finance_manager']))
				$empIdsStr .= ",".$exitProcDetails['finance_manager'];
			if(!empty($exitProcDetails['l2_manager']))//l2 manager condition added
				$empIdsStr .= ",".$exitProcDetails['l2_manager'];
			
			$empIdsStr = trim($empIdsStr,",");
			
			$empIdsArr = explode(",",$empIdsStr);

			array_push($empIdsArr, $exitProcDetails['reporting_manager']);
			if(!empty($exitProcDetails['reporting_manager']))
			{
				$l2managerDetails = $this->exitProcModel->getEmployeeDetails($exitProcDetails['reporting_manager'],'one');
				$this->view->l2managerDetails = $l2managerDetails;
				array_push($empIdsArr,$l2managerDetails[0]['reporting_manager']);
			}

			if(in_array($this->loggedInUser,$empIdsArr) || ($this->loggedInUserGroup == MANAGEMENT_GROUP || $this->loggedInUserGroup == HR_GROUP) || $this->loggedInUserRole == SUPERADMINROLE)
			{
				$managersDataObj_tmp = $this->exitProcModel->getEmployeeDetails($empIdsStr,'multiple');
				$managersDataObj = array();

				if(!empty($managersDataObj_tmp))
				{
					foreach ($managersDataObj_tmp as &$row) {
						$managersDataObj[$row['user_id']] = &$row;
					}				
				}
				$this->view->managersDataObj = $managersDataObj;
			
			}
			else
			{
				$this->view->ermsg = 'noprivileges';
			}
		}
		/**
		** getting hr manager, sys admin, general admin, finance manager details - END
		**/
		$this->view->exitProcDetails = $exitProcDetails;
		$this->view->loggedInUser = $this->loggedInUser;
		
		if($this->getRequest()->getPost())
		{
			$this->save($managersDataObj,$exitProcDetails,$id);
		}
		$this->view->loginuserRole = $this->loggedInUserRole;
		$this->view->loginuserGroup = $this->loggedInUserGroup;
		/** retrieve exit proc history **/
		$historyObj = $this->exitProcModel->getExitProcHistory($id);
		$this->view->historyObj = $historyObj;
	}
	
	public function save($managersDataObj,$exitProcDetails,$id)
	{

		if(!empty($this->loggedInUser))
		{
			$data = $msgarray = array(); 
			$where = array('id=?' => $id);
			
			//array to set post values
			$post_array = array();
			
			/**
			** capture form data, check validations and return error / success messages - START
			** if logged in user is superadmin, entire form is editabale, so all the form values are captured
			** else the segment which is editable to logged in user is only captured
			**/
			$status = '';
			
			//check if l1 manager is matching with any other configured managers, if same update both statuses
			$configured_manager_arr = array('l2_manager'=>$exitProcDetails['l2_manager'],'hr_manager'=>$exitProcDetails['hr_manager'],'sys_admin'=>$exitProcDetails['sys_admin'],'general_admin'=>$exitProcDetails['general_admin'],'finance_manager'=>$exitProcDetails['finance_manager']);
				
			$other_manager='';	
			if(in_array($exitProcDetails['reporting_manager'], $configured_manager_arr))
			{
				$other_manager = array_search($exitProcDetails['reporting_manager'], $configured_manager_arr);
			}

			/** for l1 manager **/
			if($this->loggedInUser == $exitProcDetails['reporting_manager'])
			{
				if(!empty($_POST['l1m']) && !empty($_POST['l1mcomments'])){
					$data['l1_status'] = $_POST['l1m'];
					$data['l1_comments'] = $_POST['l1mcomments'];
							
					$status = $_POST['l1m'];
				}else{
					if(empty($_POST['l1m']))
						$msgarray['l1m'] = 'Please select status.';
					else
						$post_array['l1m']=$_POST['l1m'];
					if(empty($_POST['l1mcomments']))
						$msgarray['l1mcomments'] = 'Please enter comments.';	
					else
						$post_array['l1mcomments']=$_POST['l1mcomments'];
				}
				
			}

			/** for l2 manager **/	
			if($this->loggedInUser == $exitProcDetails['l2_manager'])
			{
				if($other_manager!='' && $configured_manager_arr[$other_manager]==$exitProcDetails['l2_manager'])
				{
					$_POST['l2m'] =  $_POST['l1m'];
					$_POST['l2mcomments'] =  $_POST['l1mcomments'];
				}
				if(!empty($_POST['l2m']) && !empty($_POST['l2mcomments']))
				{
					$data['l2_status'] = $_POST['l2m'];
					$data['l2_comments'] = $_POST['l2mcomments'];
	
					$status = $_POST['l2m'];
				}
				else {
					if(empty($_POST['l2m']))
						$msgarray['l2m'] = 'Please select status.';
					else
						$post_array['l2m'] = $_POST['l2m'];
					if(empty($_POST['l2mcomments']))
						$msgarray['l2mcomments'] = 'Please enter comments.';	
					else
						$post_array['l2mcomments'] = $_POST['l2mcomments'];
				}
			}
			/** for hr manager **/			
			if($this->loggedInUser == $exitProcDetails['hr_manager'])
			{
				if($other_manager!='' && $configured_manager_arr[$other_manager]==$exitProcDetails['hr_manager'])
				{
					$_POST['hrm'] =  $_POST['l1m'];
					$_POST['hrmcomments'] =  $_POST['l1mcomments'];
				}
				if(!empty($_POST['hrm']) && !empty($_POST['hrmcomments'])){
					
					$data['hr_manager_status'] = $_POST['hrm'];
					$data['hr_manager_comments'] = $_POST['hrmcomments'];
					
					$status = $_POST['hrm'];
				}
				else {
					if(empty($_POST['hrm']))
						$msgarray['hrm'] = 'Please select status.';
					else
						$post_array['hrm'] = $_POST['hrm'];
						
					if(empty($_POST['hrmcomments']))
						$msgarray['hrmcomments'] = 'Please enter comments.';	
					else
						$post_array['hrmcomments'] = $_POST['hrmcomments'];
				}
			}
			

			/** for system admin **/
			if($this->loggedInUser == $exitProcDetails['sys_admin'])
			{
				if($other_manager!='' && $configured_manager_arr[$other_manager]==$exitProcDetails['sys_admin'])
				{
					$_POST['sysadm'] =  $_POST['l1m'];
					$_POST['sysadmcomments'] =  $_POST['l1mcomments'];
				}
				if(!empty($_POST['sysadm']) && !empty($_POST['sysadmcomments'])){
					$data['sys_admin_status'] = $_POST['sysadm'];
					$data['sys_admin_comments'] = $_POST['sysadmcomments'];
					
					$status = $_POST['sysadm'];
				}else {
					if(empty($_POST['sysadm']))
						$msgarray['sysadm'] = 'Please select status.';
					else
						$post_array['sysadm'] = $_POST['sysadm'];	
					if(empty($_POST['sysadmcomments']))
						$msgarray['sysadmcomments'] = 'Please enter comments.';	
					else
						$post_array['sysadmcomments'] = $_POST['sysadmcomments'];	
				}
				
			}
			

			/** for general admin **/
			if($this->loggedInUser == $exitProcDetails['general_admin'])
			{
				if($other_manager!='' && $configured_manager_arr[$other_manager]==$exitProcDetails['general_admin'])
				{
					$_POST['gam'] =  $_POST['l1m'];
					$_POST['gamcomments'] =  $_POST['l1mcomments'];
				}
				if(!empty($_POST['gam']) && !empty($_POST['gamcomments'])){
					$data['gen_admin_status'] = $_POST['gam'];
					$data['gen_admin_comments'] = $_POST['gamcomments'];
					
					$status = $_POST['gam'];
				}else{
					if(empty($_POST['gam']))
						$msgarray['gam'] = 'Please select status.';
					else
						$post_array['gam'] = $_POST['gam'];
					if(empty($_POST['gamcomments']))
						$msgarray['gamcomments'] = 'Please enter comments.';
					else
						$post_array['gamcomments'] = $_POST['gamcomments'];
				}
				
			}

			/** for finance manager **/	
			if($this->loggedInUser == $exitProcDetails['finance_manager'])
			{
				if($other_manager!='' && $configured_manager_arr[$other_manager]==$exitProcDetails['finance_manager'])
				{
					$_POST['fm'] =  $_POST['l1m'];
					$_POST['fmcomments'] =  $_POST['l1mcomments'];
				}
				if(!empty($_POST['fm']) && !empty($_POST['fmcomments'])){
					
					$data['fin_admin_status'] = $_POST['fm'];
					$data['fin_admin_comments'] = $_POST['fmcomments'];
					$status = $_POST['fm'];
				}else{
					if(empty($_POST['fm']))
						$msgarray['fm'] = 'Please select status.';
					else
						$post_array['fm'] = $_POST['fm'];
					if(empty($_POST['fmcomments']))
						$msgarray['fmcomments'] = 'Please enter comments.';
					else
						$post_array['fmcomments'] = $_POST['fmcomments'];
				}
				
			}
			//$this->view->msgarray = $msgarray;
			/**
			** capture form data, check validations and return error / success messages - END
			**/
			
			if($this->loggedInUserGroup == MANAGEMENT_GROUP || $this->loggedInUserGroup == HR_GROUP || $this->loggedInUserRole == SUPERADMINROLE){
				$relieving_date = sapp_Global::change_date($_POST['relieving_date'],'database');
				$data['relieving_date'] = $relieving_date;			
					
					
			}
			
			/**
			** updating exit proc data
			**/
			if(!empty($data) && empty($msgarray))
			{
				$data['modifiedby'] = $this->loggedInUser;
				$data['modifieddate'] = gmdate("Y-m-d H:i:s");
	

				$res = $this->allExitProcsModel->updateExitProc($data,$where,$id);

				if($res == 'update')
				{
					$this->_helper->getHelper('FlashMessenger')->addMessage(array('success' => 'Exit procedure has been updated successfully.'));
					
					/** insert into log manager table **/
					$result = sapp_Global::logManager(ALL_EXIT_PROCS,2,$this->loggedInUser,$id);

					/** insert into exit proc history - START **/
					$history_data = array(
						"exit_request_id" =>  $id ,
						//"user_id" => $this->loggedInUser ,
						"description" => "<name> has updated exit procedure on <createddate>",	
						"createdby" => $this->loggedInUser ,
						"createddate" => gmdate("Y-m-d H:i:s")
					);
					
					$this->exitProcModel->exitProcHistory($history_data);
					/** insert into exit proc history - END **/
					
					
					/** notification mail - START **/
					$processData = $this->allExitProcsModel->getExitProcessData($id);//function to get exit request data
					$user_name = '';$email_address = '';
					if(!empty($processData))
					{
						$userDetails = $this->exitProcModel->getUserEmail($processData[0]['employee_id']);
						$user_name = $userDetails[0]['userfullname'];
						$email_address = $userDetails[0]['emailaddress'];
					}
					
					$view = $this->getHelper('ViewRenderer')->view;
					$mail_content="Exit procedure has been ".$status." by ".$this->userfullname;
					$this->view->mail_content = $mail_content;
					$text = $view->render('exitmailtemplates/exitmailtemplate.phtml');
					$options['subject'] = APPLICATION_NAME.': Exit Procedure Notification';
					$options['header'] = 'Exit Procedure';
					$options['toEmail'] = $email_address;
					$options['toName'] = $user_name;
					$options['message'] = $text;
					$options['cron'] = 'yes';
					$result = sapp_Global::_sendEmail($options);
					
					
					
					//send email to hr
					
					//get hr based on settings id
					$settingsData = $this->exitProcModel->getSettingsData($processData[0]['exit_settings_id']);
					$user_name = '';$email_address = '';
					if(!empty($settingsData))
					{
						$userDetails = $this->exitProcModel->getUserEmail($settingsData[0]['hr_manager']);
						$user_name = $userDetails[0]['userfullname'];
						$email_address = $userDetails[0]['emailaddress'];
					}
					
					$view = $this->getHelper('ViewRenderer')->view;
					$mail_content="Exit procedure has been ".$status." by ".$this->userfullname;
					$this->view->mail_content = $mail_content;
					$text = $view->render('exitmailtemplates/exitmailtemplate.phtml');
					$options['subject'] = APPLICATION_NAME.': Exit Procedure Notification';
					$options['header'] = 'Exit Procedure';
					$options['toEmail'] = $email_address;
					$options['toName'] = $user_name;
					$options['message'] = $text;
					$options['cron'] = 'yes';
					$result = sapp_Global::_sendEmail($options);
					
					/** notification mail - END **/
					
				}
				else
				{
					$this->_helper->getHelper('FlashMessenger')->addMessage(array('error' => 'Failed to update exit procedure. Please try again.'));
				}
				
				$this->_redirect('exit/allexitproc');
			}else{
				$this->view->msgarray = $msgarray;
				$this->view->post_array = $post_array;
			}
		}
	}
	public function viewAction()
	{
		/**
		** check for logged in user role for add privileges
		**/
		if(sapp_Global::_checkprivileges(ALL_EXIT_PROCS,$this->loggedInUserGroup,$this->loggedInUserRole,'view') != 'Yes'){
				$this->view->ermsg = 'noprivileges';
				return;
		} 
		$this->view->loggedInUserRole=$this->loggedInUserRole;
		/**
		** capture record id to edit
		**/
		$id = (int)$this->_request->getParam('id');
		$this->view->id = $id;
		$this->view->controllername = "allexitproc";

		$exitProcDetails = $this->allExitProcsModel->getExitProcDetails($id);
		
		/**
		** getting hr manager, sys admin, general admin, finance manager details - START
		**/
		if(!empty($exitProcDetails))
		{

			$exitProcDetails = $exitProcDetails[0];
		
			$empIdsStr = '';
			if(!empty($exitProcDetails['hr_manager']))
				$empIdsStr .= $exitProcDetails['hr_manager'];
			if(!empty($exitProcDetails['sys_admin']))
				$empIdsStr .= ",".$exitProcDetails['sys_admin'];
			if(!empty($exitProcDetails['general_admin']))
				$empIdsStr .= ",".$exitProcDetails['general_admin'];
			if(!empty($exitProcDetails['finance_manager']))
				$empIdsStr .= ",".$exitProcDetails['finance_manager'];
			if(!empty($exitProcDetails['l2_manager']))
				$empIdsStr .= ",".$exitProcDetails['l2_manager'];
			
			$empIdsStr = trim($empIdsStr,",");
			

			$empIdsArr = explode(",",$empIdsStr);

			array_push($empIdsArr, $exitProcDetails['reporting_manager']);
			if(!empty($exitProcDetails['reporting_manager']))
			{
				$l2managerDetails = $this->exitProcModel->getEmployeeDetails($exitProcDetails['reporting_manager'],'one');
				$this->view->l2managerDetails = $l2managerDetails;
				array_push($empIdsArr,$l2managerDetails[0]['reporting_manager']);
			}

			if(in_array($this->loggedInUser,$empIdsArr) || ($this->loggedInUserGroup == MANAGEMENT_GROUP || $this->loggedInUserGroup == HR_GROUP) || $this->loggedInUserRole == SUPERADMINROLE)
			{
				$managersDataObj_tmp = $this->exitProcModel->getEmployeeDetails($empIdsStr,'multiple');
				$managersDataObj = array();

				if(!empty($managersDataObj_tmp))
				{
					foreach ($managersDataObj_tmp as &$row) {
						$managersDataObj[$row['user_id']] = &$row;
					}				
				}
				$this->view->managersDataObj = $managersDataObj;
			
			}
			else
			{
				$this->view->ermsg = 'noprivileges';
			}
		}
		$historyObj = $this->exitProcModel->getExitProcHistory($id);
		$this->view->historyObj = $historyObj;
		/**
		** getting hr manager, sys admin, general admin, finance manager details - END
		**/
		$this->view->exitProcDetails = $exitProcDetails;
	}
	//function to display popup
	public function editpopupAction()
	{
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->getRequest()->getParam('id');
		$exitProcDetails=array();
		$exitProcDetails = $this->allExitProcsModel->getExitProcDetails($id);
		if(!empty($exitProcDetails))
		{
			$exitProcDetails = $exitProcDetails[0];
		}
		$this->view->exitProcDetails = $exitProcDetails;
		$this->view->id = $id;
		$this->view->controllername='allexitproc';
	}
	//function to update overall status
	public function updateexitprocessAction()
	{
		$result_array['result'] = '';
		$result_array['msg'] = '';
		$data = array();
		$msgarray = array();
		$this->_helper->layout->disableLayout();
		$id = $this->_request->getParam('id');
		$status = $this->_request->getParam('status');
		$comments = $this->_request->getParam('comments');
		$relieving_date = $this->_request->getParam('relieving_date');
		if(empty($status))
			$msgarray['hrm'] = 'Please select status.';
		if(empty($comments))
			$msgarray['hrmcomments'] = 'Please enter comments.';	
		if(!empty($status) && !empty($comments)){
			$data = array(
					'overall_status' =>   $status,
					'overall_comments' => $comments			
			);
		}
		$relieving_date = sapp_Global::change_date($relieving_date,'database');
		$data['relieving_date'] = $relieving_date;		
		
		if(!empty($data) && empty($msgarray)){
			$data['modifiedby'] = $this->loggedInUser;
			$data['modifieddate'] = gmdate("Y-m-d H:i:s");
			$where = array('id=?' => $id);
			$res = $this->allExitProcsModel->updateExitProc($data,$where,$id);
			if($res == 'update')
			{
				$result_array['result'] = 'success';
				$result_array['msg'] = 'Exit procedure has been updated successfully.';
				/** insert into log manager table **/
				$result = sapp_Global::logManager(ALL_EXIT_PROCS,2,$this->loggedInUser,$id);

				/** insert into exit proc history - START **/
				$history_data = array(
					"exit_request_id" =>  $id ,
					//"user_id" => $this->loggedInUser ,
					"description" => "<name> has updated exit procedure on <createddate>",	
					"createdby" => $this->loggedInUser ,
					"createddate" => gmdate("Y-m-d H:i:s")
				);
				
				
				/** notification mail - START **/
					$processData = $this->allExitProcsModel->getExitProcessData($id);//function to get exit request data
					$user_name = '';$email_address = '';
					if(!empty($processData))
					{
						$userDetails = $this->exitProcModel->getUserEmail($processData[0]['employee_id']);
						$user_name = $userDetails[0]['userfullname'];
						$email_address = $userDetails[0]['emailaddress'];
					}
					
					
					$view = $this->getHelper('ViewRenderer')->view;
					$mail_content="Exit procedure has been ".$status." by ".$this->userfullname;
					$this->view->mail_content = $mail_content;
					$text = $view->render('exitmailtemplates/exitmailtemplate.phtml');
					$options['subject'] = APPLICATION_NAME.': Exit Procedure Notification';
					$options['header'] = 'Exit Procedure';
					$options['toEmail'] = $email_address;
					$options['toName'] = $user_name;
					$options['message'] = $text;
					$options['cron'] = 'yes';
					$result = sapp_Global::_sendEmail($options);
					
					
					
					//get hr based on settings id
					$settingsData = $this->exitProcModel->getSettingsData($processData[0]['exit_settings_id']);
					$user_name = '';$email_address = '';
					if(!empty($settingsData))
					{
						$userDetails = $this->exitProcModel->getUserEmail($settingsData[0]['hr_manager']);
						$user_name = $userDetails[0]['userfullname'];
						$email_address = $userDetails[0]['emailaddress'];
					}
					
					$view = $this->getHelper('ViewRenderer')->view;
					$mail_content="Exit procedure has been ".$status." by ".$this->userfullname;
					$this->view->mail_content = $mail_content;
					$text = $view->render('exitmailtemplates/exitmailtemplate.phtml');
					$options['subject'] = APPLICATION_NAME.': Exit Procedure Notification';
					$options['header'] = 'Exit Procedure';
					$options['toEmail'] = $email_address;
					$options['toName'] = $user_name;
					$options['message'] = $text;
					$options['cron'] = 'yes';
					$result = sapp_Global::_sendEmail($options);
					
					/** notification mail - END **/
				
				$this->exitProcModel->exitProcHistory($history_data);
				/** insert into exit proc history - END **/
			}
			else
			{
				$result_array['result'] = 'failure';
				$result_array['msg'] = 'Failed to update exit procedure. Please try again.';
			}
		}
		$this->_helper->json($result_array);
	}
	//function to assign questions to employees after exit process has approved
	public function assignquestionsAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
		$popConfigPermission = '';
			
		if(sapp_Global::_checkprivileges(EXIT_QUESTIONS,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
			$popConfigPermission = 'yes';
		}
	
		$id = $this->_request->getParam('id');
		// based on process id get data
		$exitProcDetails = $this->allExitProcsModel->getExitProcDetails($id);
		$questionsArr=array();
		if(!empty($exitProcDetails))
		{
			$exitProcDetails = $exitProcDetails[0];
			$this->view->exit_type_id = $exitProcDetails['exit_type_id'];
			// get questions based on exit type id
			$questionsArr =$this->exitquestionsmodel->getExitQuestionsByexitId($exitProcDetails['exit_type_id']);
			
		}
		$question_response=array();
		$assigned_ques = array();
		$emp_response_array =array();
		$assignedques_array=array();
		if(!empty($exitProcDetails))
		{
			//based on user_id and process id get the data
			$question_response = $this->exitquesresponse->getResponseData($exitProcDetails['employee_id'],$id);
		}
		if(!empty($question_response ))
		{
			$assigned_ques=explode(',',$question_response[0]['hr_qs']);
			$emp_response_array = json_decode($question_response[0]['employee_response'],true);
			$this->view->question_employee_response = $question_response[0]['employee_response'];
			$assignedques_array=$this->exitquesresponse->getEmployeeQuestions($question_response[0]['hr_qs']);
		}
	
		$this->view->emp_response_array = $emp_response_array;
		$this->view->questionsArr = $questionsArr;
		$this->view->checkArr = $assigned_ques;
		$this->view->assignedques_array = $assignedques_array;
		$this->view->popConfigPermission = $popConfigPermission;
		if($this->getRequest()->getPost())
		{

				$selectedquestions = $this->getRequest()->getParam('check');
				$qs_Arr = implode(',', $selectedquestions);
				foreach($selectedquestions  as $key=>$val)
				{

					$update_data = array("isused"=>1,
										"modifieddate"=>gmdate("Y-m-d H:i:s"),
										"modifiedby"=>$loginUserId 
										);
					$update_where = array('id=?' => $val);	
					$this->exitquestionsmodel->SaveorUpdateExitQuestionData($update_data,$update_where);
				} 
				// check record exits or not in main_exit_question_response table if exist update the record 
				
				if(count($question_response) == 0)
				{

					$data = array('user_id'=>$exitProcDetails['employee_id'],
								 'exit_initiation_id'=>$id, 
								 'hr_qs'=>$qs_Arr ,	
								'createdby'=>$loginUserId,	
								'createddate'=>gmdate("Y-m-d H:i:s")						
						);
					$where = '';  
				}
				else
				{
					// if uncheck the question update isused=0 in exit_questions table
					foreach($assigned_ques  as $key=>$val)
					{
						 if (!in_array($val,$selectedquestions ))
						 {							
							 $update_data = array("isused"=>0,
										"modifieddate"=>gmdate("Y-m-d H:i:s"),
										"modifiedby"=>$loginUserId 
										);
							$update_where = array('id=?' => $val);								 
							$this->exitquestionsmodel->SaveorUpdateExitQuestionData($update_data,$update_where);
						 }
						 
						 
					}
					
					$data = array(
								 'hr_qs'=>$qs_Arr ,	
								'modifiedby'=>$loginUserId,	
								'modifieddate'=>gmdate("Y-m-d H:i:s")						
						);
					 $where = " user_id = '".$exitProcDetails['employee_id']."' and exit_initiation_id='".$id."' and isactive= 1 ";
					
				}
				 
				
				$result=$this->exitquesresponse->SaveorUpdateQuestionsData($data,$where);
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Questions have been configured successfully."));	
				
				$this->_redirect('exit/allexitproc');	
				
		}
	}
	
}
?>