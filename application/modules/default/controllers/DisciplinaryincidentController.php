<?php
/********************************************************************************* 
 *  This file is part of Sentrifugo.
 *  Copyright (C) 2015 Sapplica
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

class Default_DisciplinaryincidentController extends Zend_Controller_Action
{
    private $options;
    public function preDispatch()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('get_employees', 'html')->initContext();
    }
    
    public function indexAction() {
        $disciplinaryIncidentModel = new Default_Model_Disciplinaryincident();
        $call = $this->_getParam('call');
        if ($call == 'ajaxcall')
            $this->_helper->layout->disableLayout();

        $refresh = $this->_getParam('refresh');
        $dashboardcall = $this->_getParam('dashboardcall');

        $data = array();
        $searchQuery = '';
        $searchArray = array();
        $tablecontent = '';

        if ($refresh == 'refresh') {
            if ($dashboardcall == 'Yes')
                $perPage = DASHBOARD_PERPAGE;
            else
                $perPage = PERPAGE;
            $sort = 'DESC';
            $by = 'i.modifieddate';
            $pageNo = 1;
            $searchData = '';
            $searchQuery = '';
            $searchArray = '';
        }
        else {
            $sort = ($this->_getParam('sort') != '') ? $this->_getParam('sort') : 'DESC';
            $by = ($this->_getParam('by') != '') ? $this->_getParam('by') : 'i.modifieddate';
            if ($dashboardcall == 'Yes')
                $perPage = $this->_getParam('per_page', DASHBOARD_PERPAGE);
            else
                $perPage = $this->_getParam('per_page', PERPAGE);
            $pageNo = $this->_getParam('page', 1);
            /** search from grid - START * */
            $searchData = $this->_getParam('searchData');
            $searchData = rtrim($searchData, ',');
            /** search from grid - END * */
        }
        $flag='managementincident';
        $dataTmp = $disciplinaryIncidentModel->getGrid($sort, $by, $perPage, $pageNo, $searchData, $call, $dashboardcall,$flag);

        array_push($data, $dataTmp);
        $this->view->dataArray = $data;
        $this->view->call = $call;
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        $this->render('disciplinarymyincidents/index', null, true);
        
    }
    
	public function viewAction() {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $loginUserId = $auth->getStorage()->read()->id;
        }
		$objName = 'disciplinaryincident';
        $id = $this->getRequest()->getParam('id');
        try {
            $disciplinaryIncidentModel = new Default_Model_Disciplinaryincident();
            if ($id != '') {
                if (is_numeric($id) && $id > 0) {
                    $data = $disciplinaryIncidentModel->getIncidentData($id);
                    
                    if(!empty($data) && $data[0]['incident_raised_by']==$loginUserId) {
                    	$this->view->data = $data[0];
                    	$this->view->controllername = 'disciplinaryincident';
                    	$this->view->loginUserId = $loginUserId;
                    	if(!defined('sentrifugo_gilbert')) {
	                    	$disciplineHistoryModel = new Default_Model_Disciplineincidenthistory();
	                    	$incident_history = $disciplineHistoryModel->getDisciplineIncidentHistory($id);
	                    	$this->view->incident_history = $incident_history;
                    	}
                    }	
                    else
                    	$this->view->rowexist = "norows";	
                } else {
                    $this->view->rowexist = "norows";
                }
            } else {
                $this->view->rowexist = "norows";
            }
        } catch (Exception $e) {
            $this->view->rowexist = "norows";
        }
		$this->view->controllername = $objName;
        $this->render('disciplinarymyincidents/view', null, true);
    }
    
public function editAction()
	{
	    $popConfigPermission = array();
		$auth = Zend_Auth::getInstance();
		$report_opt = array();$role_datap=array();$empGroup="";
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
			$loggedinuserName = $auth->getStorage()->read()->userfullname;
		}
		if(sapp_Global::_checkprivileges(VIOLATION_TYPE,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
				 array_push($popConfigPermission,'violationname');
				}  
		$this->view->popConfigPermission = $popConfigPermission;
		
		$id = (int)$this->getRequest()->getParam('id');
		$id = abs($id);
		$diciplinaryIncidentform = new Default_Form_Disciplinaryincident();
		$disciplinaryIncidentModel = new Default_Model_Disciplinaryincident();
		$violation_object = new Default_Model_Disciplinaryviolation;
		$deptModel = new Default_Model_Departments();
		$errormsg = 'norecord';
		try
		{
			if($id!='' && is_numeric($id) && $id>0)
			{
				$data = $disciplinaryIncidentModel->getIncidentData($id);
				if(!empty($data) && $data[0]['incident_raised_by']==$loginUserId) {
					$data = $data[0];
					$departmentsData = $deptModel->getDepartmentList($data['employee_bu_id']);
					if(sizeof($departmentsData) > 0)
					{
						$diciplinaryIncidentform->employee_dept_id->addMultiOption('','Select Department');
						foreach ($departmentsData as $departmentsres){
							$diciplinaryIncidentform->employee_dept_id->addMultiOption($departmentsres['id'],$departmentsres['deptname']);
						}
					}
				
			        $violation_types = $violation_object->getallDisciplinaryViolationTypesData();
			        $diciplinaryIncidentform->violation_id->addMultiOption('','Select Type of Violation');
			        if(!empty($violation_types))
			        {
			            foreach ($violation_types as $violation_type){
			                $diciplinaryIncidentform->violation_id->addMultiOption($violation_type['id'],$violation_type['violationname']);
			            }
			        }
					
					$diciplinaryIncidentform->populate($data);
					$diciplinaryIncidentform->incident_raised_by->setValue($loggedinuserName);
					$diciplinaryIncidentform->setDefault('employee_bu_id',$data['employee_bu_id']);
					if(isset($data['employee_dept_id'])) {
						$diciplinaryIncidentform->setDefault('employee_dept_id',$data['employee_dept_id']);
					}
					$diciplinaryIncidentform->date_of_occurrence->setValue(sapp_Global::change_date($data['date_of_occurrence'],'view'));
					if(!defined('sentrifugo_gilbert')) {
						if(isset($data['violation_expiry'])) {
							$diciplinaryIncidentform->violation_expiry->setValue(sapp_Global::change_date($data['violation_expiry'],'view'));
						}
					}
					if(isset($data['corrective_action'])) {
						$diciplinaryIncidentform->setDefault('corrective_action',$data['corrective_action']);
					}
					$diciplinaryIncidentform->setDefault('incident_status',$data['incident_status']);
					$diciplinaryIncidentform->setDefault('violation_id',$data['violation_id']);
                    $diciplinaryIncidentform->employee_appeal->setValue($data['employee_appeal']=='Yes'?1:2);
                    $diciplinaryIncidentform->cao_verdict->setValue($data['cao_verdict']=='Guilty'?1:2);
					$diciplinaryIncidentform->setAttrib('action',BASE_URL.'disciplinaryincident/edit/id/'.$id);
					if(!defined('sentrifugo_gilbert')) {
                    	$disciplineHistoryModel = new Default_Model_Disciplineincidenthistory();
                    	$incident_history = $disciplineHistoryModel->getDisciplineIncidentHistory($id);
					  	$this->view->incident_history = $incident_history;
                    }
					$this->view->id = $id;
					$this->view->form = $diciplinaryIncidentform;
					$this->view->messages = $this->_helper->flashMessenger->getMessages();
					$this->view->data = $data;
					$errormsg = '';
				}
				if($this->getRequest()->getPost()){
					$result = $this->save($diciplinaryIncidentform);
					$diciplinaryIncidentform->incident_raised_by->setValue($loggedinuserName);
					$this->view->msgarray = $result;
				}
			}
			

		}
		catch(Exception $e)
		{   
			$this->view->ermsg = $errormsg;
		}
		$this->view->ermsg = $errormsg;
		$this->render('disciplinaryincident/add', null, true);
	}
    
    //function to add disciplinary incident
    public function addAction()
    {
        $auth = Zend_Auth::getInstance();
        $logged_in_user_id = 0;
        $logged_in_user_name = '';
        $popConfigPermission = array();
        //getting logged in user details
        if($auth->hasIdentity())
        {
            $logged_in_user_id = $auth->getStorage()->read()->id;
            $logged_in_user_name = $auth->getStorage()->read()->userfullname;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
			$loginuserRole = $auth->getStorage()->read()->emprole;
        }
        // die($logged_in_user_name);
       if(sapp_Global::_checkprivileges(VIOLATION_TYPE,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
				 array_push($popConfigPermission,'violationname');
				}  
		$this->view->popConfigPermission = $popConfigPermission;
		
		
        $form = new Default_Form_Disciplinaryincident();
        $form->incident_raised_by->setValue($logged_in_user_name);

        $violation_object = new Default_Model_Disciplinaryviolation;
        $violation_types = $violation_object->getallDisciplinaryViolationTypesData();
        $form->violation_id->addMultiOption('','Select Type of Violation');
        if(!empty($violation_types))
        {
            foreach ($violation_types as $violation_type){
                $form->violation_id->addMultiOption($violation_type['id'],$violation_type['violationname']);
            }
        }
        else
        {
            $msgarray['violation_id'] = 'Violation Types are not configured yet.';
            // $emptyFlag++;
        }

        $this->view->form = $form;
        $this->view->logged_in_user_id = $logged_in_user_id;
        $this->view->logged_in_user_name = $logged_in_user_name;

        if($this->getRequest()->getPost())
        {
            $result = $this->save($form);
            $form->incident_raised_by->setValue($logged_in_user_name);
            $this->view->msgarray = $result; 
        }
    }
    public function save($form)
    {
        $disciplinary_object = new Default_Model_Disciplinaryincident();
        try
        {
            $corrective_action = $this->_request->getParam('corrective_action');
            $corrective_action_text = $this->_request->getParam('corrective_action_text');
            $employee_bu_id = $this->_request->getParam('employee_bu_id');
            $employee_dept_id = $this->_request->getParam('employee_dept_id');
            $error_flag = 0;
            if(!empty($corrective_action) && $corrective_action == 'Other' )
            {   
                if(empty($corrective_action_text))
                {
                    $msgarray['corrective_action_text'] = "Please enter corrective Action.";
                    $error_flag++;
                }
                elseif(!preg_match('/^[a-zA-Z0-9.\- ?\',\/#@$&*()!]+$/',$corrective_action_text))
                {
                    $msgarray['corrective_action_text'] = "Please enter a valid Corrective Action.";
                    $error_flag++;
                }
                
            }
            if($form->isValid($this->_request->getPost()) && $error_flag == 0)
            {
                $auth = Zend_Auth::getInstance();
                $logged_in_user_id = 0;
                //getting logged in user details
                if($auth->hasIdentity())
                {
                    $logged_in_user_id = $auth->getStorage()->read()->id;
                }

                $incident_raised_by = $logged_in_user_id;
                $id = $this->_request->getParam('id');
                $employee_id = $this->_request->getParam('employee_id');
                $employee_rep_mang_id = $this->_request->getParam('employee_rep_mang_id');
                $date_of_occurrence = $this->_request->getParam('date_of_occurrence');
                $violation_id = $this->_request->getParam('violation_id');
                $violation_expiry = $this->_request->getParam('violation_expiry');
                $employee_job_title_id = $this->_request->getParam('employee_job_title_id');
                $employer_statement = $this->_request->getParam('employer_statement');
                $employee_appeal = $this->_request->getParam('employee_appeal');
                $employee_statement = $this->_request->getParam('employee_statement');
                $cao_verdict = $this->_request->getParam('cao_verdict');

                $data = array(
                    'incident_raised_by' => $incident_raised_by,
                    'employee_bu_id' => $employee_bu_id,
                    'employee_dept_id' => $employee_dept_id,
                    'employee_id' => $employee_id,
                    'employee_rep_mang_id' => $employee_rep_mang_id,
                    'date_of_occurrence' => date('Y-m-d',strtotime($date_of_occurrence)),
                    'violation_id' => $violation_id,
                    //'violation_expiry' => date('Y-m-d',strtotime($violation_expiry)),
                    'employee_job_title_id' => $employee_job_title_id,
                    'employer_statement' => $employer_statement,
                    'employee_appeal' => $employee_appeal,
                    'employee_statement' => $employee_statement,
                    'cao_verdict' => $cao_verdict,
                    'corrective_action' => $corrective_action,
                    'corrective_action_text' => ($corrective_action == 'Other')?$corrective_action_text:$corrective_action,
                    'modifieddate' => gmdate("Y-m-d H:i:s"),
                    'modifiedby' => $logged_in_user_id,
                );
                
                if(!defined('sentrifugo_gilbert')) {
                	$data['violation_expiry'] = date('Y-m-d',strtotime($violation_expiry));
                }

                if($id!=''){
                	if(!defined('sentrifugo_gilbert'))
                		$data['incident_status'] = 'Closed';
                    $where = array('id=?'=>$id);  
                    $actionflag = 2;
                }
                else
                {
                    $data['createdby'] = $logged_in_user_id;
                    $data['createddate'] = gmdate("Y-m-d H:i:s");
                    $where = '';
                    $actionflag = 1;
                }

                $Id = $disciplinary_object->SaveorUpdateIncidentData($data, $where);
                if($Id == 'update')
                {
                   $tableid = $id;
                   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Incident updated successfully."));
                }   
                else
                {
                   $tableid = $Id;  
                    $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Incident raised successfully."));                     
                }  

                $menuID = RAISE_INCIDENT;
                $result = sapp_Global::logManager($menuID,$actionflag,$logged_in_user_id,$tableid);
                
                $incidentData = $disciplinary_object->getIncidentData($tableid);
                $this->sendEmails($incidentData);
                if(!defined('sentrifugo_gilbert')) {
                	$disciplineHistoryModel = new Default_Model_Disciplineincidenthistory();
                	if($id=='') {
                		$desc_msg = "Disciplinary incident has been raised by ";
                	}else{
                		$desc_msg = "Disciplinary incident verdict has been provided by ";
                	}
					$incident_data = array(
					 'createdby' =>$logged_in_user_id,
					 'createddate' =>gmdate("Y-m-d H:i:s"),
					 'incident_id'=>$tableid,
					 'action_emp_id'=>$logged_in_user_id,
					 'description' => $desc_msg
					  );
					$disciplineHistoryModel->saveOrUpdateDisciplineIncidentHistory($incident_data,'');
                }
                $this->_redirect('disciplinaryincident');                  
            }
            else
            {
                $messages = $form->getMessages();
                foreach ($messages as $key => $val)
                {
                    foreach($val as $key2 => $val2)
                     {
                        $msgarray[$key] = $val2;
                        break;
                     }
                }
                if(isset($employee_bu_id)  && $employee_bu_id != '')
                {
                    $departmentsmodel = new Default_Model_Departments();
                    $departmentlistArr = $departmentsmodel->getDepartmentList($employee_bu_id);
                    $form->employee_dept_id->clearMultiOptions();
                    $form->employee_dept_id->addMultiOption('','Select Department');
                    foreach($departmentlistArr as $departmentlistresult)
                    {
                        $form->employee_dept_id->addMultiOption($departmentlistresult['id'],utf8_encode($departmentlistresult['deptname']));
                    }
                     
                    if(isset($employee_dept_id) && $employee_dept_id != 0 && $employee_dept_id != '')
                    $form->setDefault('employee_dept_id',$employee_dept_id);
                }
                return $msgarray;   
            }
        }
        catch(Exception $e)
        {
             $msgarray['incident_raised_by'] = "Something went wrong, please try again.";
            return $msgarray;
        }
        

    }
    //function to get employee list based on business unit and department
    public function getemployeesAction()
    {
        $this->_helper->layout->disableLayout();
        $businessunit_id = $this->_getParam('businessunit_id');
        $department_id = $this->_getParam('department_id');
        $disciplinary_object = new Default_Model_Disciplinaryincident();
        $employee_data = $disciplinary_object->getemployees($businessunit_id,$department_id);
        if(empty($employee_data))
        {
            $flag = 'false';
        }
        else
        {
            $flag = 'true';
        }
        $this->view->employee_data=$employee_data;
        $this->view->flag=$flag;        
    }

    
    // function to delete incident if initiated
	public function deleteAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->_request->getParam('objid');
		$deleteflag=$this->_request->getParam('deleteflag');
		$messages['message'] = '';
		$messages['msgtype'] = '';
		$actionflag = 3;
		$flag='true';
		if($id)
		{
			$disciplinary_object = new Default_Model_Disciplinaryincident();
			$incidentData = $disciplinary_object->getIncidentData($id);
			if(!defined('sentrifugo_gilbert')) {
				if($incidentData[0]['incident_status']!='Initiated') {
					$flag=='false';
				}
			}
			if($flag=='true') {
				$data = array('isactive'=>0,
								'modifieddate' => gmdate("Y-m-d H:i:s"),
	                    		'modifiedby' => $loginUserId
								);
				
				$where = array('id=?'=>$id);	
				$Id = $disciplinary_object->SaveorUpdateIncidentData($data, $where);
					
				if($Id == 'update')
				{
					$menuID = RAISE_INCIDENT;
					$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
					$messages['message'] = 'Incident deleted successfully.';
					$messages['msgtype'] = 'success';
					$this->sendEmails($incidentData);
				}
				else
				{
					$messages['message'] = 'Incident cannot be deleted.';
					$messages['msgtype'] = 'error';
				}
			}
			else
			{
				$messages['message'] = 'Incident cannot be deleted.';
				$messages['msgtype'] = 'error';
			}
		}
		else
		{
			$messages['message'] = 'Incident cannot be deleted.';
			$messages['msgtype'] = 'error';
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
	
	public function sendEmails($incidentData) {
				// Mail Code to be sent to HR,Management,Employee,Manager
                $mail_arr[0]['name'] = 'HR';
				$mail_arr[0]['email'] = defined('LV_HR_'.$incidentData[0]['employee_bu_id'])?constant('LV_HR_'.$incidentData[0]['employee_bu_id']):"";
				$mail_arr[0]['type'] = 'HR';
				$mail_arr[1]['name'] = $incidentData[0]['incidentraisedby'];
				$mail_arr[1]['email'] = $incidentData[0]['managementemail'];
				$mail_arr[1]['type'] = 'Management';
				$mail_arr[2]['name'] = $incidentData[0]['employeename'];
				$mail_arr[2]['email'] = $incidentData[0]['employeeemail'];
				$mail_arr[2]['type'] = 'Employee';
				$mail_arr[3]['name'] = $incidentData[0]['reportingmanagername'];
				$mail_arr[3]['email'] = $incidentData[0]['reportingmanageremail'];
				$mail_arr[3]['type'] = 'Manager';
				for($ii =0;$ii<count($mail_arr);$ii++)
				{
					$base_url = 'http://'.$this->getRequest()->getHttpHost() . $this->getRequest()->getBaseUrl();
					$view = $this->getHelper('ViewRenderer')->view;
					$this->view->emp_name = $mail_arr[$ii]['name'];
					$this->view->base_url=$base_url;
					$this->view->type = $mail_arr[$ii]['type'];
					$this->view->raised_name = $incidentData[0]['incidentraisedby'];
					$this->view->raised_against = $incidentData[0]['employeename'];
					$this->view->raised_against_employee_id = $incidentData[0]['employeeId'];
					$this->view->raised_by = 'Manager';
					$text = $view->render('mailtemplates/disciplineincident.phtml');
					$options['subject'] = APPLICATION_NAME.': New Disciplinary Incident Raised';
					$options['header'] = 'Disciplinary Incident';
					$options['toEmail'] = $mail_arr[$ii]['email'];
					$options['toName'] = $mail_arr[$ii]['name'];
					$options['message'] = $text;

					$options['cron'] = 'yes';
					if($options['toEmail'] != '')
					sapp_Global::_sendEmail($options);
				}
	}
}


