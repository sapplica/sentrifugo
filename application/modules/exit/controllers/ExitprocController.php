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
** controller used to raise exit proc
**/
class Exit_ExitprocController extends Zend_Controller_Action
{
	private $exitProcModel;
	private $options;
	private $loggedInUser = '';
	private $loggedInUserGroup = '';
	private $loggedInUserRole = '';
	
	public function preDispatch()
	{	
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('savequestions', 'json')->initContext();
	
	}
	public function init()
	{
		$this->_options = $this->getInvokeArg('bootstrap')->getOptions();
		/** Instantiate exit proc settings model **/
		$this->exitProcModel = new Exit_Model_Exitproc();

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
			$this->businessunit_id = $auth->getStorage()->read()->businessunit_id;
			$this->department_id = $auth->getStorage()->read()->department_id;
			$this->userfullname = $auth->getStorage()->read()->userfullname;
			$this->is_org_head = $auth->getStorage()->read()->is_orghead;
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
		$dataTmp = $this->exitProcModel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall,$context);
		array_push($data,$dataTmp);
		
		/**
		** send data objects to view
		**/
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		
		/**
		** send flash messages to view
		**/
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
	}

	public function save($exitProc)
	{
		$exitType = $this->_request->getParam('exit_type');	
		$comments = $this->_request->getParam('comments');	
		$epSettingsObj = $this->exitProcModel->getSettings($this->businessunit_id,$this->department_id);
		$msgarray = array();
		$auth = Zend_Auth::getInstance();
		//get login user reporting manager
		$reporting_manager = ($auth->getStorage()->read()->reporting_manager)?$auth->getStorage()->read()->reporting_manager:'';
		
		$managersArray = array();
		$ep_config_id ='';
		$noticeperiod_days='';
		if(!empty($epSettingsObj))
		{
			$ep_config_id = $epSettingsObj[0]['id'];
			$managersArray = array($reporting_manager,$epSettingsObj[0]['l2_manager'],$epSettingsObj[0]['hr_manager'],$epSettingsObj[0]['sys_admin'],$epSettingsObj[0]['general_admin'],$epSettingsObj[0]['finance_manager']);
			$noticeperiod_days = $epSettingsObj[0]['notice_period'];// notice period days
		}

		if($exitProc->isvalid($this->_request->getPost()) && !empty($ep_config_id))
		{
			//calculate relieving date based on notice period days
			$d = new DateTime();
			$d->modify("+$noticeperiod_days days"); // add notice period days to current date
			$expecting_releaving_date = $d->format("Y-m-d");
			
			$year = date('Y', strtotime($expecting_releaving_date)); //get releaving date month and year
			$month = date('m', strtotime($expecting_releaving_date));//get releaving date month and year
			
			$usersModel = new Timemanagement_Model_Users();
			$empHolidaysWeekends = $usersModel->getEmployeeHolidaysNWeekends($this->loggedInUser,$year,$month); // get emp holidays and weekends
			$holidays = $empHolidaysWeekends[0]['holiday_dates'];
			$holidayDates = explode(',',$holidays); // emp holidays array in a releaving month
			$start_Date = date("Y-m-01", strtotime($expecting_releaving_date));
			$end_Date = date("Y-m-t", strtotime($expecting_releaving_date));
			$empWeekends = $usersModel->getWeekend($start_Date,$end_Date,$this->department_id); //get emp weekends in a releaving month
			$non_working_days = array_merge($holidayDates,$empWeekends); // total non working days in a releaving month
			
			$releaving_date = $expecting_releaving_date;
			if(!empty($non_working_days))
			{
				for($i=1;$i<=7;$i++){
					if(in_array($expecting_releaving_date,$non_working_days))// check if releaving day exist in non working day
					{
						//remove one day if exist
						$releaving_date = date('Y-m-d',(strtotime ( "-1 day" , strtotime ( $expecting_releaving_date) ) ));
						$expecting_releaving_date = $releaving_date;
					}
				}
			}

			
				$data = array(
					"employee_id" =>  $this->loggedInUser ,
					"exit_settings_id" => $ep_config_id,
					"exit_type_id" => $exitType,
					"employee_comments" => $comments ,
					"l1_status" => 'Pending',
					"l2_status" => 'Pending',
					"hr_manager_status" => 'Pending',
					"sys_admin_status" => 'Pending',
					"gen_admin_status" =>'Pending',
					"fin_admin_status" => 'Pending',
					"createdby" => $this->loggedInUser ,
					"createddate" => gmdate("Y-m-d H:i:s") ,
					"relieving_date" => $expecting_releaving_date
				);
			
			$res = $this->exitProcModel->save($data);
			if($res)
			{
				/** insert into log manager table **/
				sapp_Global::logManager(INITIATE_EXIT_PROC,1,$this->loggedInUser,$res);
				
				/** insert into exit proc history - START **/
				$history_data = array(
					"exit_request_id" =>  $res ,
					"description" => "<name> has initiated exit procedure on <createddate>",	
					"createdby" => $this->loggedInUser ,
					"createddate" => gmdate("Y-m-d H:i:s")
				);
				
				$this->exitProcModel->exitProcHistory($history_data);
				/** insert into exit proc history - END **/
				
				/** insert into exit main_exit_types - START **/
				$this->exitTypesModel = new Exit_Model_Exittypes();
				$update_data = array("isused"=>1,
									"modifieddate"=>gmdate("Y-m-d H:i:s"),
									"modifiedby"=>$this->loggedInUser 
									);
				$update_where = array('id=?' => $exitType);					
				$this->exitTypesModel->SaveorUpdateExitTypesData($update_data,$update_where);
				
				/** insert into exit main_exit_types - END **/
				
				
				
				/** notification mail - START **/
				$managers_email_array = array();
				$email_array = array();
				if(!empty($managersArray)){
					foreach($managersArray as $userId){
						if($userId!=''){
							$email_array = $this->exitProcModel->getUserEmail($userId);
							$userfullname = ($email_array[0]['userfullname'])?$email_array[0]['userfullname']:'';
							$emailaddress = ($email_array[0]['emailaddress'])?$email_array[0]['emailaddress']:'';
							$managers_email_array[$userfullname] = $emailaddress;
						}
					}
				}
				if(!empty($managers_email_array)){
					foreach($managers_email_array as $user_name => $manager_email)
					{
						
						
						//$base_url = 'http://'.$this->getRequest()->getHttpHost() . $this->getRequest()->getBaseUrl();
						
						$view = $this->getHelper('ViewRenderer')->view;
						$mail_content="Exit procedure has been initialized by ".$this->userfullname;
						$this->view->mail_content = $mail_content;
						$text = $view->render('exitmailtemplates/exitmailtemplate.phtml');
						$options['subject'] = APPLICATION_NAME.': Exit Procedure Notification';
						$options['header'] = 'Exit Procedure';
						$options['toEmail'] = $manager_email;
						$options['toName'] = $user_name;
						$options['message'] = $text;
						$options['cron'] = 'yes';
						$result = sapp_Global::_sendEmail($options);
						
						
					}
				}
				
				/** notification mail - END **/
				
				$this->_helper->getHelper('FlashMessenger')->addMessage(array('success' => 'Employee exit procedure has been initiated successfully.'));
				$this->_redirect('exit/exitproc/view/id/'.$res);
			}
			else
			{
				$this->_helper->getHelper('FlashMessenger')->addMessage(array('error' => 'Failed to initiate employee exit procedure. Please try again.'));
				$this->_redirect('exit/exitproc');
			}
			
			
		}
		else
		{
			//added condition for management group
			if($this->loggedInUserGroup==MANAGEMENT_GROUP && empty($ep_config_id))
				$msgarray['exit_type'] = 'You are not associated with any department so you can not initialize exit request.';
			else if(empty($ep_config_id))
				$msgarray['exit_type'] = 'Exit settings are not configured please contact to your HR.';
				
			$validationMsgs = $exitProc->getMessages();
			foreach($validationMsgs as $key => $val)
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
	// function to show assigned exit questions
	public function questionsAction()
	{
		$this->exitQnModel = new Exit_Model_Questionsresponse();
		$id = $this->getRequest()->getParam('id');
		$employee_questions_str_array = $this->exitQnModel->getEmployeeQuestionsData($this->loggedInUser,$id);
		$employee_questions_array = array();
		$emp_response_array = array();
		$emp_comments = '';
		if(!empty($employee_questions_str_array)){
			$qustions_str = $employee_questions_str_array[0]['hr_qs'];
			$emp_comments = ($employee_questions_str_array[0]['employee_response']!='')?$employee_questions_str_array[0]['employee_response']:'';
			$employee_questions_array = $this->exitQnModel->getEmployeeQuestions($qustions_str);
		}
		if($emp_comments!='')
			$emp_response_array = json_decode($emp_comments,true);
		
		$this->view->employee_questions_array = $employee_questions_array;
		$this->view->exitprocessId = $id;
		$this->view->emp_response_array = $emp_response_array;
		$this->view->employee_id = $this->loggedInUser;
		
	}
	public function viewAction()
	{
		//parameter to differntiate tabs
		$extra_param ='employeedetails';
		$this->view->flag = $extra_param ;
		$id = (int)$this->_request->getParam('id');
		/**
		** display logged in user details - business unit, department, job title, position, L1 manager, L2 manager
		**/
		$empDetailsObj = $this->exitProcModel->getEmployeeDetails($this->loggedInUser,'one');
		$l2managerDetails = '';
		if(isset($empDetailsObj[0]))
		{
			$this->view->empDetailsObj = $empDetailsObj[0];
			if(!empty($empDetailsObj[0]['reporting_manager']))
			{
				$l2managerDetails = $this->exitProcModel->getEmployeeDetails($empDetailsObj[0]['reporting_manager'],'one');
			}
		}
		else
		{
			$this->view->empDetailsObj = array();
		}

		$this->view->l2managerDetails = $l2managerDetails;
		$this->view->loggedInUser = $this->loggedInUser;
		$this->view->loggedInUserGroup = $this->loggedInUserGroup;
		
		
		/*array to display after exit process approved */
		$tabsarray = array('employeedetails','questions');
		$this->view->tabsarray = $tabsarray;
		$this->view->objName = 'exitproc';
		/**
		** check if exit proc configurations exist for the logged-in user's department and/or business unit
		**/
		if($this->loggedInUser != SUPERADMIN)
		{
			$epSettingsObj = $this->exitProcModel->getSettings($this->businessunit_id,$this->department_id);
			if(empty($epSettingsObj))
			{
				$this->view->ermsg = 'noConfig';
				return;
			}
			else
				$this->view->epSettingsObj = $epSettingsObj;
		}

		/**
		** 1. check if logged in user has initiated any exit procs
		** 2. if yes, display details and status
		** 3. if no, display form to initiate exit proc
		**/
		$res = $this->exitProcModel->getExitProcDetails($this->loggedInUser,$id);
;
		if(!empty($res))
		{
			$this->view->context = 'status';			
			$exitProc = new Exit_Form_Exitproc();
			$this->view->form = $exitProc;
			/**
			** getting hr manager, sys admin, general admin, finance manager details - START
			**/
			$empIdsStr = '';
			if(!empty($res[0]['hr_manager']))
				$empIdsStr .= $res[0]['hr_manager'];
			if(!empty($res[0]['l2_manager']))
				$empIdsStr .= ",".$res[0]['l2_manager'];
			if(!empty($res[0]['sys_admin']))
				$empIdsStr .= ",".$res[0]['sys_admin'];
			if(!empty($res[0]['general_admin']))
				$empIdsStr .= ",".$res[0]['general_admin'];
			if(!empty($res[0]['finance_manager']))
				$empIdsStr .= ",".$res[0]['finance_manager'];
			
			$empIdsStr = trim($empIdsStr,",");
			
			$managersDataObj_tmp = $this->exitProcModel->getEmployeeDetails($empIdsStr,'multiple');
			$managersDataObj = array();

			if(!empty($managersDataObj_tmp))
			{
				foreach ($managersDataObj_tmp as &$row) {
					$managersDataObj[$row['user_id']] = &$row;
				}				
			}
			/**
			** getting hr manager, sys admin, general admin, finance manager details - END
			**/
			
			/** retrieve exit proc history **/
			
			$exitProcId = $res[0]['id'];
			$ep_userId = $res[0]['employee_id'];
			$historyObj = $this->exitProcModel->getExitProcHistory($exitProcId);
			$this->view->historyObj = $historyObj;

			$this->view->managersDataObj = $managersDataObj;		
			$this->view->exitProcDetails = $res[0];			
		}
	
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
	}
	//function to initiate exit process
	public function addAction()
	{
		$this->view->context = 'raise';
		$exitProc = new Exit_Form_Exitproc();
		$this->view->form = $exitProc;
		$exitProc->setAttrib('action',BASE_URL.'exit/exitproc/add');
		
		if($this->is_org_head==1){
			$this->view->ermsg = 'noConfig';
			return;
		}
		
		if($this->loggedInUser != SUPERADMIN && $this->is_org_head!=1)
		{
			$epSettingsObj = $this->exitProcModel->getSettings($this->businessunit_id,$this->department_id);
			if(empty($epSettingsObj))
			{
				$this->view->ermsg = 'noConfig';
				return;
			}
			
		}
		//get is employee valid to initialize new request
		$emp_exit_details = $this->exitProcModel->getEmpRaisedRequest($this->loggedInUser);
		$this->view->not_valid_for_add = count($emp_exit_details);
			
		/**
		** display exit types
		**/
		$exitTypes =  $this->exitProcModel->getExitTypes();
		$exitProc->exit_type->addMultiOption('','Select Exit Type');
		if(!empty($exitTypes))
		{
			foreach($exitTypes as $exitType)
			{
				$exitProc->exit_type->addMultiOption($exitType['id'],utf8_encode($exitType['exit_type']));
			}
		}	

		if($this->loggedInUser != SUPERADMIN && $this->getRequest()->getPost())
		{
			$this->save($exitProc);
		}	
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
	}
	//function to save employee comments
	public function savequestionsAction()
	{
		$this->exitQnModel = new Exit_Model_Questionsresponse();
		$exitprocessId = $this->getRequest()->getParam('exitprocessId');
		$employee_id = $this->getRequest()->getParam('employee_id');
		$employee_comments = $this->getRequest()->getParam('emp_comment');
		$encoded_emp_comments = json_encode($employee_comments,true);	
		$questions_array = array('employee_response'=>$encoded_emp_comments,
								'modifiedby'=>$this->loggedInUser,
								'modifieddate'=>gmdate("Y-m-d H:i:s"));
		$questions_where = array('exit_initiation_id=?' => $exitprocessId,'user_id=?'=>$employee_id);
		$result = $this->exitQnModel->SaveorUpdateQuestionsData($questions_array,$questions_where);	
		if($result=='update')
		{
			$msg = 'saved';
			$this->_helper->getHelper('FlashMessenger')->addMessage(array('success' => 'Exit procedure answers saved successfully.'));
			/** notification mail - START **/
			$this->allExitProcsModel = new Exit_Model_Allexitprocs();
					$processData = $this->allExitProcsModel->getExitProcessData($exitprocessId);//function to get exit request data
					$settingsID = $processData[0]['exit_settings_id'];
					
					//get hr based on settings id
					$settingsData = $this->exitProcModel->getSettingsData($settingsID);
					$user_name = '';$email_address = '';
					if(!empty($settingsData))
					{
						$userDetails = $this->exitProcModel->getUserEmail($settingsData[0]['hr_manager']);
						$user_name = $userDetails[0]['userfullname'];
						$email_address = $userDetails[0]['emailaddress'];
					}
					
					$view = $this->getHelper('ViewRenderer')->view;
					$mail_content="Exit procedure questions has been answered by ".$this->userfullname;
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
		else{
			$msg = 'err';
			$this->_helper->getHelper('FlashMessenger')->addMessage(array('error' => 'Exit procedure answers failed to add'));
		}
			
		
		$this->_helper->json(array('msg'=>$msg));
	}

}
?>