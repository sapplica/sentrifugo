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

class Default_AppraisalinitController extends Zend_Controller_Action
{
    private $_options;
    public function preDispatch()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('savegroupedemployees', 'json')->initContext();
        $ajaxContext->addActionContext('changesettings', 'json')->initContext();
        $ajaxContext->addActionContext('getperiod', 'json')->initContext();
        $ajaxContext->addActionContext('deletelinemanager', 'json')->initContext();
        $ajaxContext->addActionContext('deletegroupedemployees', 'json')->initContext();
        $ajaxContext->addActionContext('initializegroup', 'json')->initContext();
        $ajaxContext->addActionContext('completeappraisal', 'json')->initContext();
        $ajaxContext->addActionContext('displaymanagers', 'html')->initContext();
        $ajaxContext->addActionContext('displaysettings', 'html')->initContext();
        $ajaxContext->addActionContext('displayreportmanagers', 'html')->initContext();
        $ajaxContext->addActionContext('displayemployees', 'html')->initContext();
        $ajaxContext->addActionContext('displaycontentacc', 'html')->initContext();
        $ajaxContext->addActionContext('displaycontentreportacc', 'html')->initContext();
        $ajaxContext->addActionContext('constructacc', 'html')->initContext();
        $ajaxContext->addActionContext('constructreportacc', 'html')->initContext();
        $ajaxContext->addActionContext('displayline', 'html')->initContext();
        $ajaxContext->addActionContext('displayreport', 'html')->initContext();
        $ajaxContext->addActionContext('addlinemanager', 'html')->initContext();
        $ajaxContext->addActionContext('discardsteptwo', 'json')->initContext();
        $ajaxContext->addActionContext('getdepartmentsadmin', 'json')->initContext();
        $ajaxContext->addActionContext('checkappadmin', 'json')->initContext();
        $ajaxContext->addActionContext('getemployeeslinemanagers', 'html')->initContext();
        $ajaxContext->addActionContext('getconfiglinemanagersAction', 'json')->initContext();
        $ajaxContext->addActionContext('validateconfigAction', 'json')->initContext();
    }
	
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
    }

    public function checkappadminAction()
    {
        $businessunit_id = $this->_getParam('businessunit_id',null);
        $department_id = $this->_getParam('department_id',null);                     
        $result = array('status' => "success",'frequency' => "",'ratings'=>"",'implid'=>"");
        $ratings = '';
        if($businessunit_id != '')
        {
			$app_init_model = new Default_Model_Appraisalinit();
			$app_cnt = $app_init_model->checkappadmin($businessunit_id,$department_id);
            if($app_cnt > 0)
            {
                $result = array('status' => 'error');
            }
            else
            {
                // $app_data = $app_init_model->check_per_implmentation($businessunit_id, $department_id);                
                $app_data = $app_init_model->checkAppraisalExists($businessunit_id, $department_id);                
                $result['frequency'] = $app_data['appraisal_mode'];
				$app_ratings_type = isset($app_data[0]['appraisal_ratings'])?$app_data[0]['appraisal_ratings']:'';
				$app_id = isset($app_data[0]['id'])?$app_data[0]['id']:0;
                if($app_ratings_type == 1)
                	$ratings = '1-5';
                else
                	$ratings = '1-10';	
                $result['ratings'] = $ratings;
                $result['implid'] = $app_id;
            }

        }
        $this->_helper->json($result);
    }
    public function getdepartmentsadminAction()
    {
        $businessunit_id = $this->_getParam('businessunit_id',null);
        $options = "<option value=''>Select Department</option>";
        if($businessunit_id != '')
        {
            $app_init_model = new Default_Model_Appraisalinit();
            $dept_data = $app_init_model->getdeparmentsadmin($businessunit_id);
            
            if(count($dept_data) > 0)
            {
                foreach($dept_data as $dept)
                {
                    $options .= "<option value='".$dept['id']."'>".$dept['deptname']."</option>";
                }
            }
        }
        $this->_helper->json(array('options' => $options));
    }
    public function discardsteptwoAction()
    {
    	
        $init_id = $this->_getParam('init_id',null);
        $management_appraisal = $this->_getParam('management_appraisal',null);
        
        $status = "failure";
        $message = "Something went wrong, please try again.";
        $result = array('status' => $status,'message' => $message);
        
        if(!empty($init_id) && $management_appraisal != '')
        {
            $auth = Zend_Auth::getInstance();
            if($auth->hasIdentity())
            {
                $loginUserId = $auth->getStorage()->read()->id;
                $loginuserRole = $auth->getStorage()->read()->emprole;
                $loginuserGroup = $auth->getStorage()->read()->group_id;
            }
            $app_init_model = new Default_Model_Appraisalinit();
            $result = $app_init_model->discardsteptwo($init_id,$management_appraisal,$loginUserId,$loginuserRole,$loginuserGroup);
        }
        
        $this->_helper->json($result);
    }
    public function displaylineAction()
    {
        $init_id = $this->_getParam('init_id',null);
        $call = $this->_getParam('call',null);
        
        $app_init_model = new Default_Model_Appraisalinit();
        $init_data = $app_init_model->getConfigData($init_id);
        
        if(count($init_data) > 0)
            $init_data = $init_data[0];
        
        $this->view->init_data = $init_data;
        $this->view->init_id = $init_id;
        $this->view->call = $call;
        
        $this->render('displayline');
    }
    public function addlinemanagerAction()
    {
        $init_id = $this->_getParam('init_id',null);
        $context = $this->_getParam('context','add');
        $line1_id = $this->_getParam('line1_id',null); 
        $levels = $this->_getParam('levels',null); 
        
        $app_init_model = new Default_Model_Appraisalinit();
        $init_data = $app_init_model->getConfigData($init_id);
                
        if(count($init_data) > 0)
            $init_data = $init_data[0];
        
        $employees_cnt = count($app_init_model->getEmpInit($init_id,$init_data,array()));   
        if($context == 'edit')
            $employees_cnt = 1;
        
        $this->view->init_data = $init_data;
        $this->view->init_id = $init_id;
        $this->view->context = $context;
        $this->view->line1_id = $line1_id;
        $this->view->levels = $levels;
        $this->view->employees_cnt = $employees_cnt;
        
        $this->render('addlinemanager');
    }
    public function displayreportAction()
    {
        $init_id = $this->_getParam('init_id',null);
        
        $app_init_model = new Default_Model_Appraisalinit();
        $init_data = $app_init_model->getConfigData($init_id);
        
        if(count($init_data) > 0)
            $init_data = $init_data[0];
        
        $this->view->init_data = $init_data;
        $this->view->init_id = $init_id;
        
        $this->render('displayreport');
    }
    public function deletelinemanagerAction()
    {
    
        $init_id = $this->_getParam('init_id',null);
        $manager_id = $this->_getParam('manager_id',null);
        
        $status = "failure";
        $message = "Something went wrong, please try again.";
        $result = array('status' => $status,'message' => $message);
        if(!empty($init_id) && !empty($manager_id))
        {
            $auth = Zend_Auth::getInstance();
            if($auth->hasIdentity())
            {
                $loginUserId = $auth->getStorage()->read()->id;
                $loginuserRole = $auth->getStorage()->read()->emprole;
                $loginuserGroup = $auth->getStorage()->read()->group_id;
              
               
            }
            $app_init_model = new Default_Model_Appraisalinit();
            $result = $app_init_model->deletelinemanager($init_id,$manager_id,$loginUserId,$loginuserRole,$loginuserGroup);
           $result = array('status' => 'success','msg' => 'Line managers discarded successfully');       
        }
        $this->_helper->json($result);
    }
    public function deletereportmanagerAction()
    {
        $init_id = $this->_getParam('init_id',null);
        $manager_id = $this->_getParam('manager_id',null);
		$status = "failure";
        $message = "Something went wrong, please try again.";
        $result = array('status' => $status,'message' => $message);
		$app_init_model = new Default_Model_Appraisalinit();
        if(!empty($init_id) && !empty($manager_id))
        {
            $auth = Zend_Auth::getInstance();
            if($auth->hasIdentity())
            {
                $loginUserId = $auth->getStorage()->read()->id;
                $loginuserRole = $auth->getStorage()->read()->emprole;
                $loginuserGroup = $auth->getStorage()->read()->group_id;
            }
            $result = $app_init_model->deletereportmanager($init_id,$manager_id,$loginUserId,$loginuserRole,$loginuserGroup);
        }
        $result['active_records_temp'] = $app_init_model->getActiveRecordsCountTemp($init_id);
		$result['active_records'] = $app_init_model->getActiveRecordsCount($init_id);
        $this->_helper->json($result);
    }
    public function constructreportaccAction()
    {
        $init_id = $this->_getParam('init_id',null);
        
        $app_init_model = new Default_Model_Appraisalinit();
		$init_data = $app_init_model->getConfigData($init_id);
		if(isset($init_data[0]['initialize_status']) && $init_data[0]['initialize_status'] == 1)
		{
			$exist_data = $app_init_model->getexist_line($init_id);
		}
		else
		{
			$exist_data = $app_init_model->getManagers_report($init_id);
		}
        
		$active_records_temp = $app_init_model->getActiveRecordsCountTemp($init_id);
		$active_records = $app_init_model->getActiveRecordsCount($init_id);
        
        if(count($init_data) > 0)
            $init_data = $init_data[0];
        
        $this->view->exist_data = $exist_data;
        $this->view->init_data = $init_data;
        $this->view->init_id = $init_id;
        $this->view->active_records_temp = $active_records_temp;
        $this->view->active_records = $active_records;
        
        $this->render('constructreportacc');
    }
    public function constructaccAction()
    {
        $init_id = $this->_getParam('init_id',null);
        $call = $this->_getParam('call',null);
        
        $app_init_model = new Default_Model_Appraisalinit();
        $exist_data = $app_init_model->getexist_line($init_id);
        $init_data = $app_init_model->getConfigData($init_id);
        
        if(count($init_data) > 0)
            $init_data = $init_data[0];
        
        $this->view->exist_data = $exist_data;
        $this->view->init_data = $init_data;
        $this->view->init_id = $init_id;
        $this->view->call = $call;
        
        $this->render('constructacc');
    }
    public function displayemployeesAction()
    {
    	
        $exist_employees = array();
        
        $init_id = $this->_getParam('init_id',null);
        $context = $this->_getParam('context',null);
        $line1_id = $this->_getParam('line1_id',null);
        $selected_managers_arr = $this->_getParam('sel_line',array());
        $app_init_model = new Default_Model_Appraisalinit();
        $init_data = $app_init_model->getConfigData($init_id);
        
        if(count($init_data) > 0)
            $init_data = $init_data[0];
        
        $employees = $app_init_model->getEmpInit($init_id,$init_data,$selected_managers_arr);
        if($context == 'edit')
        {
            $exist_employees = $app_init_model->getInitExistEmp($init_id,$line1_id);
          
        }
        $this->view->init_id = $init_id;
        $this->view->employees = $employees;
        $this->view->context = $context;
        $this->view->exist_employees = $exist_employees;
        $this->view->line1_id = $line1_id;
        
        $this->render('displayemployees');
    }
    public function displaycontentreportaccAction()
    {
        $init_manager_data = array();
        $userid = '';
        $init_id = $this->_getParam('init_id',null);
        $manager_id = $this->_getParam('manager_id',null);
        $type = $this->_getParam('type',null);
        $context = $this->_getParam('context',null);
        $manager_levels = $this->_getParam('manager_levels',null);
        $businessunit_id = $this->_getParam('businessunit_id',null);
        $department_id = $this->_getParam('department_id',null);
		$app_init_model = new Default_Model_Appraisalinit();
        $init_data = $app_init_model->getConfigData($init_id);
		if(isset($init_data[0]['initialize_status']) && $init_data[0]['initialize_status'] == 1)
		{
			$content = $app_init_model->getdisplayacontentacc_rep($init_id,$manager_id);
		}
		else
		{
			$content = $app_init_model->getdisplayacontentreportacc($init_id,$manager_id);
		}
        if(!empty($content))
        {
	        foreach($content as $val)
	        {
	        	$userid.=$val['user_id'].',';
	        }
	        $userid = rtrim($userid,',');
        }
        

        if($context == 'view')
        $init_manager_data = $app_init_model->getdisplayacontentacc($init_id,$manager_id);
        
        $view = $this->view;
        $view->init_id = $init_id;
        $view->init_data = $init_data;
        $view->manager_id = $manager_id;        
        $view->content = $content;
        $view->context = $context;
        $view->init_manager_data = $init_manager_data;
        $view->manager_levels = $manager_levels;
        $view->userid = $userid;
        $view->businessunit_id = $businessunit_id;
        $view->department_id = $department_id;
        
        $this->render('displaycontentreportacc');
    }
    public function displaycontentaccAction()
    {
        $init_id = $this->_getParam('init_id',null);
        $manager_id = $this->_getParam('manager_id',null);
        $tot_levels = $this->_getParam('tot_levels',null);
        $init_status = $this->_getParam('init_status',null);
        $status = $this->_getParam('status',null);
        
        $app_init_model = new Default_Model_Appraisalinit();
        $content = $app_init_model->getdisplayacontentacc($init_id,$manager_id);
        
        $view = $this->view;
        $view->init_id = $init_id;
        $view->manager_id = $manager_id;
        $view->tot_levels = $tot_levels;
        $view->content = $content;
        $view->init_status = $init_status;
        $view->status = $status;
        
        $this->render('displaycontentacc');
    }
    public function viewconfmanagersAction()
    {
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
        }
        $app_init_model = new Default_Model_Appraisalinit();  
        $app_rating_model = new Default_Model_Appraisalratings();      
        
        $init_param = $this->_getParam('i',null);
        $init_id = sapp_Global::_decrypt($init_param);
        
        $init_data = $app_init_model->getConfigData($init_id);
        if(count($init_data) > 0)
            $init_data = $init_data[0];
            
        $ratingsflag = 'false';
        $checkRatingsExists = $app_rating_model->getAppraisalRatingsbyInitId($init_id); 
        if(!empty($checkRatingsExists))
        	$ratingsflag = 'true';    
                        
        $this->view->init_id = $init_id;        
        $this->view->init_data = $init_data;
        $this->view->ratingsflag=$ratingsflag;

        $this->render('viewconfiguremanagers');
    }
    public function confmanagersAction()
    {
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
        }
        
        $app_init_model = new Default_Model_Appraisalinit();
        $ques_temp_model = new Default_Model_Appraisalqstemp();
        $ques_org_model = new Default_Model_Appraisalqsmain();
        $app_rating_model = new Default_Model_Appraisalratings();
        
        $init_param = $this->_getParam('i',null);
        $init_id = sapp_Global::_decrypt($init_param);
        $init_data = $app_init_model->getConfigData($init_id);
        if(count($init_data) > 0)
            $init_data = $init_data[0];
                        
        $this->view->init_id = $init_id;        
        $this->view->init_data = $init_data;
        $this->view->msg_arr = array();
        $ratingsflag = 'false';
        $checkRatingsExists = $app_rating_model->getAppraisalRatingsbyInitId($init_id); 
        if(!empty($checkRatingsExists))
        	$ratingsflag = 'true';
        if($this->getRequest()->getPost())
        {
            $red_result = "";
            $type_arr = array('line' => 1,'report' => 2);
            $post_values = $this->getRequest()->getPost();
            $trDb = Zend_Db_Table::getDefaultAdapter();		
            // starting transaction
            $trDb->beginTransaction();
            try
            {
				//for total page post(save all managers data at a time)
				if(isset($_POST['sbtnsaveorg']))
				{
					$manager_ids = $this->_getParam('manager_ids',null);
					$this->_redirect('appraisalinit/savemngrorghierarchy/i/'.$init_param.'/mid/'.$manager_ids);
				}
				else if(isset($post_values['existetd_mem_str']) && $post_values['existetd_mem_str'] !=='' && isset($post_values['sel_line']) && !empty($post_values['sel_line']))//for single manager save click
                {
                    if($post_values['choose_option']  == 'line')
                    {
                        if($post_values['context'] == 'add')
                        {                            
                            $init_sdata = array(                                
                                'manager_level_type' => $type_arr[$post_values['choose_option']],
                            );
                            
                            $app_result = $app_init_model->SaveorUpdateAppraisalInitData($init_sdata, " id = ".$init_id);
                            $qresult = '';
                            if($app_result == 'update')
                            {
                                $tot_ids = explode(',', $post_values['existetd_mem_str']);
                                $line_str_arr = array();
                                for($i =1 ;$i<= $post_values['sel_levels'];$i ++)
                                {
                                    $line_str_arr['line_manager_'.$i] = $post_values['sel_line'][$i];
                                }
                                
                                if(count($tot_ids) > 0)
                                {
                                    foreach ($tot_ids as $emp_id)
                                    {
                                        $qdata = array(
                                            'pa_initialization_id' => $init_id,
                                            'manager_levels' => $post_values['sel_levels'],
                                            'employee_id' => $emp_id,
                                            'module_flag' => 1,
                                            'createdby' => $loginUserId,
                                            'createdby_role' => $loginuserRole,
                                            'createdby_group' => $loginuserGroup,
                                            'modifiedby' => $loginUserId,
                                            'modifiedby_role' => $loginuserRole,
                                            'modifiedby_group' => $loginuserGroup,
                                            'createddate' => gmdate("Y-m-d H:i:s"),
                                            'modifieddate' => gmdate("Y-m-d H:i:s"),
                                            'isactive' => 1,
                                        );
                                        $qdata = array_merge($qdata,$line_str_arr);  
                                        if($init_data['initialize_status'] == 1)
                                            $qresult = $ques_org_model->SaveorUpdatePrivilegeData($qdata, '');
                                        else 
                                            $qresult = $ques_temp_model->SaveorUpdateData($qdata, '');
                                    }
                                }
                            }
                            if($qresult !== '' && $app_result !== '')
                                $red_result = "saved";
                            $message = "Appraisal process updated successfully";
                        }
                        else//for edit
                        {                            
                            $orig_emp_arr = explode(',', $post_values['org_mem_list']);
                            $selected_emp_arr = explode(',', $post_values['existetd_mem_str']);
                            $deleted_emp_arr = array_diff($orig_emp_arr,$selected_emp_arr);
                            $new_emp_arr = array_diff($selected_emp_arr,$orig_emp_arr);
                            $existing_emp_arr = array_intersect($orig_emp_arr, $selected_emp_arr);
                            
                            $line_str_arr = array();
                            /*for($i =1 ;$i<= $post_values['sel_levels'];$i ++)
                            {
                                $line_str_arr['line_manager_'.$i] = $post_values['sel_line'][$i];
                            }*/
							for($i =1 ;$i<= 5;$i ++)
                            {
                                $line_str_arr['line_manager_'.$i] = isset($post_values['sel_line'][$i])?$post_values['sel_line'][$i]:"(NULL)"; 
                            }
                            if(count($existing_emp_arr) > 0)
                            {
                                foreach($existing_emp_arr as $emp_id)
                                {
                                    $qdata = array(
                                        'modifiedby' => $loginUserId,
                                        'modifiedby_role' => $loginuserRole,
                                        'modifiedby_group' => $loginuserGroup,
                                        'modifieddate' => gmdate("Y-m-d H:i:s"),
										'manager_levels' => $post_values['sel_levels']
                                    );
                                    $qdata = array_merge($qdata,$line_str_arr);
                                    $qwhere = " employee_id = '".$emp_id."' and pa_initialization_id='".$init_id."' ";
                                    if($init_data['initialize_status'] == 1)
                                        $qresult = $ques_org_model->SaveorUpdatePrivilegeData($qdata, $qwhere);
                                    else 
                                        $qresult = $ques_temp_model->SaveorUpdateData($qdata, $qwhere);
                                }
                            }
                            if(count($new_emp_arr) > 0)
                            {
                                foreach($new_emp_arr as $emp_id)
                                {
                                    $qdata = array(
                                        'pa_initialization_id' => $init_id,
                                        'employee_id' => $emp_id,
                                        'module_flag' => 1,
                                        'createdby' => $loginUserId,
                                        'createdby_role' => $loginuserRole,
                                        'createdby_group' => $loginuserGroup,
                                        'modifiedby' => $loginUserId,
                                        'modifiedby_role' => $loginuserRole,
                                        'modifiedby_group' => $loginuserGroup,
                                        'createddate' => gmdate("Y-m-d H:i:s"),
                                        'modifieddate' => gmdate("Y-m-d H:i:s"),
                                        'isactive' => 1,
                                    );
                                    $qdata = array_merge($qdata,$line_str_arr);
                                    if($init_data['initialize_status'] == 1)
                                        $qresult = $ques_org_model->SaveorUpdatePrivilegeData($qdata, '');
                                    else 
                                        $qresult = $ques_temp_model->SaveorUpdateData($qdata, '');
                                }
                            }

                            if(count($deleted_emp_arr) > 0)
                            {
                                foreach ($deleted_emp_arr as $emp_id)
                                {
                                    $qdata = array(
                                        'isactive' => 0,
                                        'modifiedby' => $loginUserId,
                                        'modifiedby_role' => $loginuserRole,
                                        'modifiedby_group' => $loginuserGroup,
                                        'modifieddate' => gmdate("Y-m-d H:i:s"),
                                    );
                                    $qwhere = " employee_id = '".$emp_id."' and pa_initialization_id='".$init_id."' and line_manager_1 = '".$post_values['sel_line'][1]."'";
                                    if($init_data['initialize_status'] == 1)
                                        $qresult = $ques_org_model->SaveorUpdatePrivilegeData($qdata, $qwhere);
                                    else 
                                        $qresult = $ques_temp_model->SaveorUpdateData($qdata, $qwhere);
                                }
                            }

                            $red_result = "saved";
                            $message = "Appraisal process updated successfully";
                        }//end of line managers edit
                    }
                    else//for reporting
                    {
                        $choose_option = $post_values['choose_option'];
                        $context = $post_values['hid_context'];
                        if($context == 'add')
                        {
                            $init_sdata = array(                            
                                'manager_level_type' => $type_arr[$choose_option],
                            );
                            $app_result = $app_init_model->SaveorUpdateAppraisalInitData($init_sdata, " id = ".$init_id);
                            $qresult = '';
                            
                            if($app_result === 'update')
                            {
                                $tot_ids = $post_values['existetd_mem_str'];
                                $line_str_arr = array();
                                for($i =1 ;$i<= count($post_values['sel_line']);$i ++)
                                {
                                    $line_str_arr['line_manager_'.$i] = $post_values['sel_line'][$i];
                                }
                                if(count($tot_ids) > 0)
                                {
                                    //echo "<pre>";print_r($post_values);echo "</pre>";exit;
                                    foreach ($tot_ids as $emp_id)
                                    {
                                        $qdata = array(
                                            'pa_initialization_id' => $init_id,
                                            'manager_levels' => $post_values['sel_levels'],
                                            'employee_id' => $emp_id,
                                            'module_flag' => 1,
                                            'createdby' => $loginUserId,
                                            'createdby_role' => $loginuserRole,
                                            'createdby_group' => $loginuserGroup,
                                            'modifiedby' => $loginUserId,
                                            'modifiedby_role' => $loginuserRole,
                                            'modifiedby_group' => $loginuserGroup,
                                            'createddate' => gmdate("Y-m-d H:i:s"),
                                            'modifieddate' => gmdate("Y-m-d H:i:s"),
                                            'isactive' => 1,
                                        );
                                        $qdata = array_merge($qdata,$line_str_arr);
                                        if($init_data['initialize_status'] == 1)
                                            $qresult = $ques_org_model->SaveorUpdatePrivilegeData($qdata, '');
                                        else 
                                            $qresult = $ques_temp_model->SaveorUpdateData($qdata, '');
                                    }
                                }
                            }
                            if($qresult !== '' && $app_result !== '')
                                $red_result = "saved";
                            $message = "Appraisal process updated successfully";
                        }
                        else // for reporting edit
                        {                            
                            $line_str_arr = array(
                                'manager_levels' => $post_values['sel_levels'],
                                'modifiedby' => $loginUserId,
                                'modifiedby_role' => $loginuserRole,
                                'modifiedby_group' => $loginuserGroup,
                                'modifieddate' => gmdate("Y-m-d H:i:s"),
                            );
                            for($i =1 ;$i<= count($post_values['sel_line']);$i ++)
                            {
                                $line_str_arr['line_manager_'.$i] = $post_values['sel_line'][$i];
                            }
                            for($i = (count($post_values['sel_line'])+1) ;$i<=5 ;$i ++)
                            {
                                $line_str_arr['line_manager_'.$i] = null;
                            }
                            if($init_data['initialize_status'] == 1)
                                $qresult = $ques_org_model->SaveorUpdatePrivilegeData($line_str_arr, 'pa_initialization_id = '.$init_id.' and line_manager_1 = '.$post_values['hid_manager_id'].' and isactive = 1');
                            else
                            { 
                                $qresult = $ques_temp_model->SaveorUpdateData($line_str_arr, 'pa_initialization_id = '.$init_id.' and line_manager_1 = '.$post_values['hid_manager_id'].' and isactive = 1');
                                
                                /**
                                 * Start 
									If new employee is added after appraisal initialization
                                 */
                                $employeeIdsArr = $ques_temp_model->getTempEmployeeIds($init_id,$post_values['hid_manager_id']);
                                $newEmpArr = array();
                                if(!empty($employeeIdsArr))
                                {
                                	$newEmpArr = array_diff($post_values['existetd_mem_str'],explode(',',$employeeIdsArr[0]['empids']));
                                }
                                if(!empty($newEmpArr))
                                {
	                                $line_str_arr = array();
	                                for($i =1 ;$i<= count($post_values['sel_line']);$i ++)
	                                {
	                                    $line_str_arr['line_manager_'.$i] = $post_values['sel_line'][$i];
	                                }
                                	
                                	foreach($newEmpArr as $empval)
                                	{
                                		$nqdata = array(
                                            'pa_initialization_id' => $init_id,
                                            'manager_levels' => $post_values['sel_levels'],
                                            'employee_id' => $empval,
                                			'line_manager_1' => $post_values['hid_manager_id'],
                                            'module_flag' => 1,
                                            'createdby' => $loginUserId,
                                            'createdby_role' => $loginuserRole,
                                            'createdby_group' => $loginuserGroup,
                                            'modifiedby' => $loginUserId,
                                            'modifiedby_role' => $loginuserRole,
                                            'modifiedby_group' => $loginuserGroup,
                                            'createddate' => gmdate("Y-m-d H:i:s"),
                                            'modifieddate' => gmdate("Y-m-d H:i:s"),
                                            'isactive' => 1,
                                        );
                                         $nqdata = array_merge($nqdata,$line_str_arr);
                                         $qresult = $ques_temp_model->SaveorUpdateData($nqdata, '');
                                	}
                                	
                                }
                                
                                /**
                                 * End
                                 */
                            }    
                            $red_result = "saved";  
                            $message = "Appraisal process updated successfully";
                        }                    
                    }
                }
                $trDb->commit();
            }
            catch(Exception $e)
            {
                $trDb->rollBack();                
            }
            if($red_result !== '' )
            {                
                $this->_helper->FlashMessenger()->setNamespace('conf_success')->addMessage($message); 
                $this->_redirect('appraisalinit/confmanagers/i/'.sapp_Global::_encrypt($init_id));
            }
        }
        $this->view->ratingsflag=$ratingsflag;
        $this->render('configuremanagers');
    }
    public function displaymanagersAction()
    {
        $line1_data = array();
        $line_managers = array();
        $type = $this->_getParam('type',null);
        $levels = $this->_getParam('levels',null);
        $init_id = $this->_getParam('init_id',null);
        $line1_id = $this->_getParam('line1_id',null);
        $context = $this->_getParam('context','add');
        
        $app_init_model = new Default_Model_Appraisalinit();
        $init_data = $app_init_model->getConfigData($init_id);
        if(count($init_data) > 0)
		{
            $init_data = $init_data[0];
		}
		$businessunit_id = !empty($init_data['businessunit_id'])?$init_data['businessunit_id']:0;
		$department_id = !empty($init_data['department_id'])?$init_data['department_id']:0;
        if($context == 'add')
		{
			$managers = $app_init_model->getRepManagers($type,$init_id,$init_data);
		}
        else if($context == 'edit')
        {
            $emp_model = new Default_Model_Employee();
            $line1_data = $emp_model->getEmp_from_summary($line1_id);
            $line_managers = $app_init_model->getLineManagers($init_id,$line1_id);
            $line_managers = array_filter($line_managers);
			$employeeids = '';
			$managers = $app_init_model->getRepManagers_report($line1_id,$init_id,$employeeids,$businessunit_id,$department_id);
        }
        $this->view->department_id = $businessunit_id;
        $this->view->businessunit_id = $department_id;
        $this->view->levels = $levels;
        $this->view->managers = $managers;
        $this->view->init_id = $init_id;
        $this->view->context = $context;
        $this->view->line1_data = $line1_data;
        $this->view->line_managers = $line_managers;
        $this->view->line1_id = $line1_id;
        
        $this->render('displaymanagers');
    }
    public function displayreportmanagersAction()
    {
        $line1_data = array();
        $line_managers = array();
        $type = $this->_getParam('type',null);
        $levels = $this->_getParam('levels',null);
        $init_id = $this->_getParam('init_id',null);
        $line1_id = $this->_getParam('line1_id',null);
        $call_type = $this->_getParam('call_type',null);
        $context = $this->_getParam('context','add');
        $employeeids = $this->_getParam('employeeids');
		$businessunit_id = $this->_getParam('businessunit_id',null);
		$department_id = $this->_getParam('department_id',null);       
		
        $app_init_model = new Default_Model_Appraisalinit();
        $managers = $app_init_model->getRepManagers_report($line1_id,$init_id,$employeeids,$businessunit_id,$department_id);if($context == 'edit')
        {
            $emp_model = new Default_Model_Employee();
            $line1_data = $emp_model->getEmp_from_summary($line1_id);
            $line_managers = $app_init_model->getLineManagers($init_id,$line1_id);            
            $line_managers = array_filter($line_managers);            
        }
        $this->view->levels = $levels;
        $this->view->managers = $managers;
        $this->view->init_id = $init_id;
        $this->view->context = $context;
        $this->view->line1_data = $line1_data;
        $this->view->line_managers = $line_managers;
        $this->view->line1_id = $line1_id;
        $this->view->call_type = $call_type;
        
        $this->render('displayreportmanagers');
    }
    public function period_helper($mode,$period)
    {
        if($mode == 'Quarterly')
            $disp_val = "Q".$period;
        else if($mode == 'Half-yearly')
            $disp_val = "H".$period;
        else 
            $disp_val = "Yearly";
        return $disp_val;
    }
	public function getperiodAction($from_year=0,$to_year=0,$bunit=0,$dept_id=0,$dept_flag=0,$mode=0,$call_flag=0,$is_edit=0,$appraisal_id=0,$existing_period='')
    {
		// if ajax call(to year on change)
		if($call_flag == 0)
		{
			$from_year = $this->_getParam('from_year',null);
			$to_year = $this->_getParam('to_year',null);
			$bunit = $this->_getParam('bunit',null);
			$dept_id = $this->_getParam('dept_id',null);
			$dept_flag = $this->_getParam('flag',null);
			$mode = $this->_getParam('mode',null);
			$is_edit = $this->_getParam('is_edit',null);
			$appraisal_id = $this->_getParam('appraisal_id',null);
			$existing_period = $this->_getParam('existing_period',null);
		}
		$dept = (isset($dept_flag) && $dept_flag==0)?$dept_id:0;  
		$app_init_model = new Default_Model_Appraisalinit();
		//code for edit appraisal 
		if($is_edit == 1)
		{
			//get the data for the appraisal based on id to compare from year, to year and period
			$appraisal_data = $app_init_model->getConfigData($appraisal_id);
			$appr_from_year = isset($appraisal_data[0]['from_year'])?$appraisal_data[0]['from_year']:0;
			$appr_to_year = isset($appraisal_data[0]['to_year'])?$appraisal_data[0]['to_year']:0;
			$appr_mode = isset($appraisal_data[0]['appraisal_mode'])?$appraisal_data[0]['appraisal_mode']:'';
			$appr_period = isset($appraisal_data[0]['appraisal_period'])?$appraisal_data[0]['appraisal_period']:0;
			$actual_appr_period = $this->period_helper($appr_mode, $appr_period);
			if(($from_year == $appr_from_year) && ($to_year == $appr_to_year) && ($existing_period == $actual_appr_period))
			{
				if($call_flag == 0)
				{
					$this->_helper->json(array('status' =>'success','val' => $appr_period,'disp_val' => $actual_appr_period));	
					return;
				}
				else
				{
					return 'success';
				}
			}
		}
		$exist_appraisal = $app_init_model->checkappadmin($bunit,$dept_id);
		$period = 0;
		if(!$exist_appraisal)
		{
			//code for edit appraisal 
			$year_diff = 0;
			$exist_flag = 0;
			//calculate the difference between toyear and from year
			if(is_numeric($from_year) && is_numeric($to_year))
			{
				$year_diff = $to_year - $from_year;
			}
			$period = $app_init_model->getperiod($bunit,$from_year,$to_year,$mode,$dept);		
			//if year difference is 0
			if($year_diff == 0)
			{
				//check with from year and to year
				if($app_init_model->isAppraisalExist($bunit,$dept_id,$from_year,$to_year,' AND ')) $exist_flag++;
				//check with from year or to year
				if($app_init_model->isAppraisalExist($bunit,$dept_id,$from_year,$to_year,' OR ')) $exist_flag++;
			}
			else if($year_diff == 1)//if year difference is 1
			{
				//check with from year and to year
				if($app_init_model->isAppraisalExist($bunit,$dept_id,$from_year,$to_year,' AND ')) $exist_flag++;			
				//check with from year or (to year - 1)
				$to_year = $to_year-1;
				if($app_init_model->isAppraisalExist($bunit,$dept_id,$from_year,$to_year,' AND ')) $exist_flag++;
			}
			$status = 'success';
			if(($exist_flag == 0 && $period == 1) || ($exist_flag > 0 && ($period > 1 && $period < 5) && strcmp($mode,'Yearly') != 0))
			{
				// die('ef '.$exist_flag.' md '.$mode.' pd '.$period);
				if((strcmp($mode,'Quarterly') == 0 && $period > 4) || (strcmp($mode,'Half-yearly') == 0 && $period > 2) || (strcmp($mode,'Yearly') == 0 && $period > 1) && $period != 0)
				{
					$status = 'fail';
				}
				else
				{
					$status = 'success';
				}
			}
			else
			{
				$status = 'fail';
			}
		}
		else
		{
			$status = 'fail';
		}
		if($call_flag==0)
		{
			$disp_val = '';
			if(!empty($period))
			{
				$disp_val = $this->period_helper($mode, $period);
			}
			$this->_helper->json(array('status' =>$status,'val' => $period,'disp_val' => $disp_val));
		}
		else
		{
			return $status;
		}
    }

    public function indexAction()
    {
        $appraisalInitModel = new Default_Model_Appraisalinit();	
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
                $sort = 'DESC';$by = 'ai.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
        }
        else 
        {
                $sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
                $by = ($this->_getParam('by')!='')? $this->_getParam('by'):'ai.modifieddate';
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

        $dataTmp = $appraisalInitModel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);		 		

        array_push($data,$dataTmp);
        $this->view->dataArray = $data;
        $this->view->call = $call ;
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        $this->render('commongrid/performanceindex', null, true);		
    }
	
    public function addAction()
    {
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $businessunit_id = $auth->getStorage()->read()->businessunit_id;
            $department_id = $auth->getStorage()->read()->department_id; 
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
        }
        
        $this->view->loginuserRole = $loginuserRole;
        $this->view->loginuserGroup = $loginuserGroup;
        $ratings = '';
        $eligibilityvalue = '';
        $callval = $this->getRequest()->getParam('call');
        if($callval == 'ajaxcall')
		{
            $this->_helper->layout->disableLayout();
		}
        $errorMsg = '';
        $appraisalInitForm = new Default_Form_Appraisalinit();
        $appInitModel = new Default_Model_Appraisalinit();
        if($loginuserRole != SUPERADMINROLE && $loginuserGroup != MANAGEMENT_GROUP && $loginuserGroup != HR_GROUP)
        {
            $empSummaryModel = new Default_Model_Employee();
            $empData = $empSummaryModel->getEmp_from_summary($loginUserId);
			$this->view->businessunit_id = $businessunit_id;
			$checkActiveApp = $appInitModel->checkAppraisalExists($businessunit_id,$department_id);
			if(count($checkActiveApp) == 0)
			{
				$appraisalInitForm->businessunit_name->setValue($empData['businessunit_name']);
				$appraisalInitForm->department_name->setValue($empData['department_name']);
				$appraisalInitForm->department_id->setValue($empData['department_id']);
				$appraisalInitForm->businessunit_id->setValue($empData['businessunit_id']);
			} 
			else 
			{
				$errorMsg = 'Appraisal process is already initialized.';
			}
        }
        else//for super admin
        {
            $businessunits = $appInitModel->getbusinnessunits_admin('');
            $this->view->businessunits = $businessunits;
            $this->view->businessunit_id = $businessunit_id;
        }
        $employmentstatusModel = new Default_Model_Employmentstatus();
        $employmentStatusData = $employmentstatusModel->getempstatusActivelist();
        if(!empty($employmentStatusData))
        {
            foreach ($employmentStatusData as $employmentStatusres)
            {
                $appraisalInitForm->eligibility->addMultiOption($employmentStatusres['workcodename'],$employmentStatusres['statusname']);
                $appraisalInitForm->eligibility_hidden->addMultiOption($employmentStatusres['workcodename'],$employmentStatusres['statusname']);
                $eligibilityvalue.= $employmentStatusres['workcodename'].',';
            }
            $eligibilityvalue = rtrim($eligibilityvalue,',');
        }
        else 
        {
            $msgarray['eligibility'] = 'Employment status is not configured yet.';
        }
        
        if($eligibilityvalue!='')
        {
        	$appraisalInitForm->eligibility_value->setValue($eligibilityvalue);
        }
		$appraisalInitForm->eligibilityflag->setValue('1');
		$appraisalInitForm->eligibility_hidden->setAttrib("disabled", "disabled");
        $category_model = new Default_Model_Appraisalcategory();
        $category_data = $category_model->getAppraisalCategorysData();
        foreach($category_data as $cdata)
        {
            $appraisalInitForm->category_id->addMultiOption($cdata['id'],$cdata['category_name']);
        }
        $msgarray = array();
        $appraisalInitForm->setAttrib('action',BASE_URL.'appraisalinit/add');
        $this->view->form = $appraisalInitForm; 
        $this->view->msgarray = $msgarray; 
        $this->view->ermsg = $errorMsg;
        $this->view->ratingsflag='true';
        $this->view->employmentStatusData = $employmentStatusData;
        $this->view->eligibilityvalue = $eligibilityvalue;
		
        if($this->getRequest()->getPost())
        {
            $result = $this->save($appraisalInitForm);	
            $this->view->msgarray = $result; 
        }
        
        $this->render('form');	
    }//end of add action

    public function save($appraisalInitForm,$is_edit=0)
    {
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
        } 
        $appraisalInitModel = new Default_Model_Appraisalinit();
        $dept_model = new Default_Model_Departments();
        $msgarray = array();
        $errorflag = 'true';
        $eligibilityflag = $this->_request->getParam('eligibilityflag');
        $eligibility = $this->_request->getParam('eligibility');
        $eligibility_hidden = $this->_request->getParam('eligibility_hidden');
        $eligibility_value = $this->_request->getParam('eligibility_value');
        if($eligibilityflag == 1)
        {
        	if(empty($eligibility))
        	{
        		$msgarray['eligibility'] = "Please select eligibility";
        		$errorflag = 'false';
        	}
        }
        $enable_step = $this->_request->getParam('enable_step');
        $businessunit_id = $this->_request->getParam('businessunit_id');
        $department_id = $this->_request->getParam('department_id',null);
		$performance_app_flag = $this->_request->getParam('performance_app_flag');
		$id = $this->_request->getParam('id');	
        if($appraisalInitForm->isValid($this->_request->getPost()) && $errorflag == 'true')
        {
            try
            {
                $post_values = $this->_request->getPost();
                $pa_configured_id = $this->_request->getParam('configuration_id');	
                $appraisal_mode = $this->_request->getParam('appraisal_mode');
                $appraisal_period = $this->_request->getParam('appraisal_period');
                $from_year = $this->_request->getParam('from_year');
                $to_year = $this->_request->getParam('to_year');
                $category_id = $this->_request->getParam('category_id',null);
                $status = $this->_request->getParam('status');
                $disable_arr = $this->_request->getParam('disable_arr',array());
                $management_appraisal = $this->_request->getParam('management_appraisal',null);
                $appraisal_ratings = $this->_request->getParam('appraisal_ratings',null);
                $managers_due_date = $this->_request->getParam('managers_due_date');
                $employee_due_date = $this->_request->getParam('employee_due_date');
                $hid_performance_app_flag = $this->_request->getParam('hid_performance_app_flag');
                $hid_appraisal_period = $this->_request->getParam('hid_appraisal_period',null);
				//checking whether the appraisal exists for the given peroid
				if(is_numeric($hid_appraisal_period) && !empty($hid_appraisal_period) && $this->getperiodAction($from_year,$to_year,$businessunit_id,$department_id,$hid_performance_app_flag,$appraisal_mode,1,$is_edit,$id,$appraisal_period) == 'success' && $this->validateconfigAction($from_year,$to_year,$businessunit_id,$department_id,$hid_performance_app_flag,$appraisal_mode,1,$is_edit,$id,$appraisal_period) == 'success')
				{
					if(count($eligibility)>0)
						$eligibility = implode(',', $eligibility);
					else
						$eligibility = null;
						
					if(count($eligibility_hidden)>0)
						$eligibility_hidden = implode(',', $eligibility_hidden);
					else
						$eligibility_hidden = null;    
					
					if(count($category_id)>0)
						$category_id = implode(',', $category_id);
					else
						$category_id = null;
					$actionflag = '';
					$tableid  = ''; 
					$data = array(
						'businessunit_id'=>$businessunit_id,
						'department_id'=>($hid_performance_app_flag == 0)?$department_id:null, 
						'enable_step'=>$enable_step,
						'appraisal_mode'=> $appraisal_mode,
						'appraisal_period'=> $hid_appraisal_period,
						'from_year' => $from_year,
						'to_year' => $to_year,
						'managers_due_date' => sapp_Global::change_date($managers_due_date, 'database'),
						'employees_due_date' => sapp_Global::change_date($employee_due_date, 'database'),
						'eligibility'=>($eligibilityflag==1?$eligibility:$eligibility_value),
						'category_id' => $category_id,
						'status'=>$status,
						'performance_app_flag'=>!empty($performance_app_flag)?$performance_app_flag:$hid_performance_app_flag,						
						'modifiedby'=>$loginUserId,
						'modifiedby_role'=>$loginuserRole,
						'modifiedby_group'=>$loginuserGroup,
						'modifieddate'=>gmdate("Y-m-d H:i:s"),
						'appraisal_ratings' => $appraisal_ratings,
					);		
					
					if($id!='')
					{
						$where = array('id=?'=>$id);  
						$actionflag = 2;
					}
					else
					{	
						// $app_data = $appraisalInitModel->check_per_implmentation($businessunit_id, $department_id);
						// $data['pa_configured_id'] = !empty($app_data)?$app_data['id']:'0';	
						$data['pa_configured_id'] = 0;
						$data['createdby_role'] = $loginuserRole;
						$data['createdby_group'] = $loginuserGroup;					
						$data['createdby'] = $loginUserId;
						$data['createddate'] = gmdate("Y-m-d H:i:s");
						$data['isactive'] = 1;
						$where = '';
						$actionflag = 1;
					}
					$Id = $appraisalInitModel->SaveorUpdateAppraisalInitData($data, $where);
					if($Id == 'update')
					{
						$tableid = $id;                    
						$this->_helper->FlashMessenger()->setNamespace('appinit_success')->addMessage('Appraisal process updated successfully'); 
					}   
					else
					{
						$this->_helper->FlashMessenger()->setNamespace('appinit_success')->addMessage('Appraisal process added successfully'); 
						$tableid = $Id; 	                    
					}   
					$menuID =INITIALIZE_APPRAISAL;
					$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
					$this->_redirect('appraisalinit/confmanagers/i/'.sapp_Global::_encrypt($tableid));			
				}
				else
				{
					if($loginuserRole == SUPERADMINROLE || $loginuserGroup == MANAGEMENT_GROUP || $loginuserGroup == HR_GROUP)
					{
						if($performance_app_flag == 0)
						{
							if(isset($businessunit_id) && $businessunit_id != '')
							{
								$appraisalInitForm->setDefault('businessunit_id',$businessunit_id);
								$app_init_model = new Default_Model_Appraisalinit();
								$appraisalInitForm->department_id->clearMultiOptions();
								$appraisalInitForm->department_id->addMultiOption('','Select Department');
								if($id=='')
								{
									$dept_data = $app_init_model->getdeparmentsadmin($businessunit_id);
									if(count($dept_data) > 0)
									{
										foreach($dept_data as $dept)
										{
											$appraisalInitForm->department_id->addMultiOption($dept['id'],utf8_encode($dept['deptname']));
										}
									}
								}
							}  
							if(isset($department_id) && $department_id != 0 && $department_id != '')
							{
								if($id!='')
								{
									$dept_data = $dept_model->getParicularDepartmentId($department_id);
									if(count($dept_data) > 0)
									 {
										$dept_data = $dept_data[0];
										$appraisalInitForm->department_id->addMultiOption($dept_data['id'],$dept_data['deptname']);
										$appraisalInitForm->department_id->setValue($department_id);
									 }
								}
								$appraisalInitForm->setDefault('department_id',$department_id);
								$app_cnt = $app_init_model->checkappadmin($businessunit_id,$department_id);
								if($app_cnt > 0)
								{
									$msgarray['department_id'] = 'Appraisal already exists for this department.';
								}
							}
						}
					}
					$msgarray['to_year'] = "Please select a valid year range.";
					return $msgarray;					
				}
            }
            catch(Exception $e)
            {	
            	$msgarray['businessunit_name'] = "Something went wrong, please try again.";
             	return $msgarray;
            }
        } 
        else 
        {
            $messages = $appraisalInitForm->getMessages();
            foreach ($messages as $key => $val)
            {
                foreach($val as $key2 => $val2)
                {
                    $msgarray[$key] = $val2;
                    break;
                }
            }

            if($loginuserRole == SUPERADMINROLE || $loginuserGroup == MANAGEMENT_GROUP || $loginuserGroup == HR_GROUP)
            {
				if($performance_app_flag == 0)
				{
					if(isset($businessunit_id) && $businessunit_id != '')
					{
						$appraisalInitForm->setDefault('businessunit_id',$businessunit_id);
						$app_init_model = new Default_Model_Appraisalinit();
						$appraisalInitForm->department_id->clearMultiOptions();
						$appraisalInitForm->department_id->addMultiOption('','Select Department');
						if($id=='')
						{
							$dept_data = $app_init_model->getdeparmentsadmin($businessunit_id);
							if(count($dept_data) > 0)
							{
								foreach($dept_data as $dept)
								{
									$appraisalInitForm->department_id->addMultiOption($dept['id'],utf8_encode($dept['deptname']));
								}
							}
						}
					}  
					if(isset($department_id) && $department_id != 0 && $department_id != '')
					{
						if($id!='')
						{
							$dept_data = $dept_model->getParicularDepartmentId($department_id);
							if(count($dept_data) > 0)
							 {
								$dept_data = $dept_data[0];
								$appraisalInitForm->department_id->addMultiOption($dept_data['id'],$dept_data['deptname']);
								$appraisalInitForm->department_id->setValue($department_id);
							 }
						}
						$appraisalInitForm->setDefault('department_id',$department_id);
						$app_cnt = $app_init_model->checkappadmin($businessunit_id,$department_id);
						if($app_cnt > 0)
						{
							$msgarray['department_id'] = 'Appraisal already exists for this department.';
						}
					}	
				}
			}
        	return $msgarray;
        }	
    }
	
    public function deleteAction()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
        }
        $id = $this->_request->getParam('objid');
        $messages['message'] = '';
        $messages['msgtype'] = '';
        $count = 0;
        $actionflag = 3;
        
        if($id)
        {
            $model = new Default_Model_Appraisalinit();
            $init_cnt = $model->check_delete($id);
            if($init_cnt == 0)
            {
                $data = array( 
                    'isactive' => 0,
                    'modifieddate' => gmdate("Y-m-d H:i:s"),                    
                    'modifiedby' => $loginUserId,
                );
                $where = array('id=?'=>$id);
                $Id = $model->SaveorUpdateAppraisalInitData($data, $where);
                if($Id == 'update')
                {                                        
                    $result = sapp_Global::logManager(INITIALIZE_APPRAISAL,$actionflag,$loginUserId,$id);		    
                    $messages['message'] = 'Appraisal process deleted successfully';
                    $messages['msgtype'] = 'success';
                }   
                else
                {
                    $messages['message'] = 'Appraisal process cannot be deleted';
                    $messages['msgtype'] = 'error';
                }
            }
            else
            {
                $messages['message'] = 'Appraisal process cannot be deleted ';
                $messages['msgtype'] = 'error';
            }
        }
        else
        { 
            $messages['message'] = 'Appraisal process cannot be deleted';
            $messages['msgtype'] = 'error';
        }
        $this->_helper->json($messages);
    }
    
    public function viewAction()
    {
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
        }
		
        $id = $this->getRequest()->getParam('id');
        $callval = $this->getRequest()->getParam('call');
        if($callval == 'ajaxcall')
            $this->_helper->layout->disableLayout();
        $objName = 'appraisalinit';
        $ratingsflag = 'false';
        
        try
        {
            if($id)
            {
                if(is_numeric($id) && $id>0)
                {
                    $appraisalinitmodel = new Default_Model_Appraisalinit();
                    $app_rating_model = new Default_Model_Appraisalratings();
			        $checkRatingsExists = $app_rating_model->getAppraisalRatingsbyInitId($id); 
			        if(!empty($checkRatingsExists))
			        	$ratingsflag = 'true';
                    $data = $appraisalinitmodel->getappdata_forview($id);
                    if(!empty($data))
                    {                                               
                        $previ_data = sapp_Global::_checkprivileges(INITIALIZE_APPRAISAL,$loginuserGroup,$loginuserRole,'edit');
	                    $data['process_status'] = '';
	                    	if($data['initialize_status'] == 1)
	                    	{
	                    		if($data['enable_step'] == 1)
	                    			$data['process_status'] = 'Enabled to Managers';
	                    		if($data['enable_step'] == 2)
	                    			$data['process_status'] = 'Enabled to Employees';	
	                    	}
	                    	else if($data['initialize_status'] == 2)
	                    	{
	                    		$data['process_status'] = 'Initialize Later';
	                    	}else
				         	{
				         	   $data['process_status'] = 'In progress';	
				         	}
				         if($data['status'] == 1) {
                    	   $data['status']="Open";
                          }else if($data['status'] == 2) {
                    	   $data['status']="Closed";
                          }else {
                          $data['status']="Force Closed";
                          }	
                        if($data['appraisal_ratings'] == 1) {
                    	   $data['appraisal_ratings']="1-5";
                          }else {
                          $data['appraisal_ratings']="1-10";
                          }	
                         if($data['appraisal_mode'] == 'Quarterly'){
						 $data['appraisal_period']="Q".$data['appraisal_period'];
                          }
					      else if($data['appraisal_mode'] == 'Half-yearly'){
						  $data['appraisal_period']= "H".$data['appraisal_period'];
					      }
					      else {
						  $data['appraisal_period']="Yearly";
					      }
                        $data['managers_due_date'] =  sapp_Global::change_date($data['managers_due_date'],'view');
                        $data['employees_due_date'] = sapp_Global::change_date($data['employees_due_date'],'view');
	                    $this->view->previ_data = $previ_data;
                        $this->view->init_id = $id;
                        $this->view->data = $data;
                        $this->view->ratingsflag=$ratingsflag;
                    }
                    else
                    {
                        $this->view->ermsg = 'norecord';
                    }
                } 
                else
                {
                    $this->view->ermsg = 'norecord';
                }				
            }
            else
            {
                $this->view->ermsg = 'norecord';
            } 			
        }
        catch(Exception $e)
        {
            $this->view->ermsg = 'nodata';
        }		
        $this->view->controllername = $objName;
        $this->view->id = $id;		
        $this->render('view');	
    } 
	
    public function editAction()
    {	
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $businessunit_id = $auth->getStorage()->read()->businessunit_id;
            $department_id = $auth->getStorage()->read()->department_id; 
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
        }
	 
        $this->view->loginuserRole = $loginuserRole;
        $this->view->loginuserGroup = $loginuserGroup;
        $id = $this->getRequest()->getParam('id');
        $callval = $this->getRequest()->getParam('call');
        if($callval == 'ajaxcall')
            $this->_helper->layout->disableLayout();
		
        $appraisalinitform = new Default_Form_Appraisalinit();
        $app_rating_model = new Default_Model_Appraisalratings();
        $appraisalinitform->submit->setLabel('Update');
        $disable_arr = array();
        $ratings = '';
        $eligibilityvalue = '';
        $ratingsflag = 'false';
        $data = array();
        try
        {		
            if($id != '')
            {
                if(is_numeric($id) && $id>0)
                {
                    $appraisalinitmodel = new Default_Model_Appraisalinit();
			        $checkRatingsExists = $app_rating_model->getAppraisalRatingsbyInitId($id); 
			        if(!empty($checkRatingsExists))
			        	$ratingsflag = 'true';
					$this->view->ratingsflag=$ratingsflag;								        	
                    $data = $appraisalinitmodel->getConfigData($id);
                    if(!empty($data))
                    {
                        $data = $data[0];
						$performance_app_flag = isset($data['performance_app_flag'])?$data['performance_app_flag']:'';
						$this->view->performance_app_flag = $performance_app_flag;
						$app_rating_type = isset($data['appraisal_ratings'])?$data['appraisal_ratings']:0;
                        $this->view->init_id = $id;
                        if($data['initialize_status'] == 1)
                        {
                        	$this->appraisalinitialized($data);
                        }
                        else
                        {
	                        if($loginuserRole != SUPERADMINROLE && $loginuserGroup != MANAGEMENT_GROUP && $loginuserGroup != HR_GROUP)
	                        {
	                            // $appImpleData = sapp_PerformanceHelper::check_per_implmentation($businessunit_id, $department_id);
	                            
	                            $empSummaryModel = new Default_Model_Employee();
	                            $empData = $empSummaryModel->getEmp_from_summary($loginUserId);
	                            $appraisalinitform->businessunit_name->setValue($empData['businessunit_name']);
	
	                            if($performance_app_flag == 0)
								{
	                                $appraisalinitform->department_name->setValue($empData['department_name']);
								}
	                            else 
	                            {
	                                $appraisalinitform->removeElement("department_name");
	                            }
	                        }
	                        else 
	                        {
	                            // $appImpleData = sapp_PerformanceHelper::check_per_implmentation($data['businessunit_id'], $data['department_id']);
	                            
	                            $businessunits = $appraisalinitmodel->getbusinnessunits_admin($data['businessunit_id']);
	                            $this->view->businessunits = $businessunits;
	                            $dept_model = new Default_Model_Departments();
	                            if($performance_app_flag == 0)
	                            {
	                                $dept_data = $dept_model->getParicularDepartmentId($data['department_id']);
	                                if(count($dept_data) > 0)
	                                {
	                                    $dept_data = $dept_data[0];
	                                    $appraisalinitform->department_id->addMultiOption($dept_data['id'],$dept_data['deptname']);
	                                }                                
	                            }
	                        }
	                        
	                        // $this->view->imple_data = $appImpleData;
	                        $employmentstatusModel = new Default_Model_Employmentstatus();
	                        $employmentStatusData = $employmentstatusModel->getempstatusActivelist();
							
	                        if(!empty($employmentStatusData))
	                        {
	                            foreach ($employmentStatusData as $employmentStatusres)
	                            {
	                                $appraisalinitform->eligibility->addMultiOption($employmentStatusres['workcodename'],$employmentStatusres['statusname']);
	                                $appraisalinitform->eligibility_hidden->addMultiOption($employmentStatusres['workcodename'],$employmentStatusres['statusname']);
                					$eligibilityvalue.= $employmentStatusres['workcodename'].',';
	                            }
	                            $eligibilityvalue = rtrim($eligibilityvalue,',');
	                        }
						
	                        $category_model = new Default_Model_Appraisalcategory();
	                        $category_data = $category_model->getAppraisalCategorysData();
	                        foreach($category_data as $cdata)
	                        {
	                            $appraisalinitform->category_id->addMultiOption($cdata['id'],$cdata['category_name']);
	                        }
	                        
	                        // Set default values for 'To Year' field
	                        $following_year = $data["from_year"] + 1;
	                        $appraisalinitform->to_year->addMultiOptions(array($data["from_year"] => $data["from_year"], $following_year => $following_year));
	                        
	                        $appraisalinitform->populate($data);
	                        // if($appImpleData['appraisal_ratings'] == 1)
	                        // if($app_rating_type == 1)
		                		// $ratings = '1-5';
		                	// else
		                		// $ratings = '1-10'; 
		                		
		                    $appraisalinitform->appraisal_ratings->setValue($app_rating_type);
                        	if($eligibilityvalue!='')
					        {
					        	$appraisalinitform->eligibility_value->setValue($eligibilityvalue);
					        }
	                        $appraisalinitform->setAttrib('action',BASE_URL.'appraisalinit/edit/id/'.$id);
	                        $appraisalinitform->eligibilityflag->setValue('1');
							$appraisalinitform->eligibility_hidden->setAttrib("disabled", "disabled");
	                        
	                        /*$cnt = $appraisalinitmodel->check_delete($id);
	                        if($cnt > 0)
	                        {
	                            $disable_arr[] = "management_appraisal"; 
	                        }*/
	                        
	                        if($data['enable_step'] == 2)
	                        {
	                        	$appraisalinitform->enable_step->removeMultiOption(1);
	                        }
	                        $this->view->period_disp = $this->period_helper($data['appraisal_mode'], $data['appraisal_period']);
	                        $this->view->data = $data;
	                        $this->view->category_id_value = $data['category_id'];
	                        $this->view->businessunit_id = $businessunit_id;
	                        $this->view->employmentStatusData = $employmentStatusData;
        					$this->view->eligibilityvalue = $eligibilityvalue;
                        }
                    } 
                    else 
                    {
                        $this->view->ermsg = 'norecord';
                    }
                }
                else
                {
                    $this->view->ermsg = 'norecord';
                }				
            }
            else
            {
                $this->view->ermsg = '';
            }
        }
        catch(Exception $e)
        {
            $this->view->ermsg = 'nodata';
        }
        $this->view->eligibility_value = isset($data['eligibility'])? $data['eligibility']:'';	
        $this->view->form = $appraisalinitform;
        if($this->getRequest()->getPost())
        {
            $result = $this->save($appraisalinitform,$is_edit=1);	
            $this->view->msgarray = $result; 
        }
        //$this->view->disable_arr = $disable_arr;
        if(!empty($data) && $data['initialize_status'] == 1)
        	$this->render('edit');
        else	
        	$this->render('form');	
    }
    
	public function appraisalinitialized($data)
	{
		$employmentstatusModel = new Default_Model_Employmentstatus();
		$category_model = new Default_Model_Appraisalcategory();
		$eligibility = '';
		$category = '';
		$budeptArr = sapp_Global::getbudeptname($data['id']);
		$empstatusArr = $employmentstatusModel->getEmploymentStatusName($data['eligibility']);
		if(!empty($empstatusArr))
		{
			foreach($empstatusArr as $status)
			{
				$eligibility.= $status['statusname'].',';
			}
			$eligibility = rtrim($eligibility,',');
		}
		$categoryArr = $category_model->getCategoryNameByIds($data['category_id']);
		if(!empty($categoryArr))
		{
			foreach($categoryArr as $catid)
			{
				$category.= $catid['category_name'].',';
			}
			$category = rtrim($category,',');
		}
		$data['process_status'] = '';
		if($data['initialize_status'] == 1)
		{
			if($data['enable_step'] == 1)
			   $data['process_status'] = 'Enabled to Managers';
			if($data['enable_step'] == 2)
			   $data['process_status'] = 'Enabled to Employees';	
		}
		else if($data['initialize_status'] == 2)
		{
		   $data['process_status'] = 'Initialize Later';
		}
		else
		{
		   $data['process_status'] = 'In progress';	
		}
		$this->view->ermsg = '';
		$this->view->eligibility = $eligibility;
		$this->view->category = $category;
		$this->view->budeptArr = $budeptArr;
		$this->view->data = $data;	
	}    
	
	public function viewassigngroupsAction()
	{
		$appraisalinitmodel = new Default_Model_Appraisalinit();
		$appraisalGroupsModel = new Default_Model_Appraisalgroups();
		$appraisalQsModel = new Default_Model_Appraisalquestions();
		$app_rating_model = new Default_Model_Appraisalratings();
		$init_param = $this->_getParam('i',null);
        $init_id = sapp_Global::_decrypt($init_param);
		$id = $init_id;
		$employeeIds = '';
		$groupIds = '';
		$options = '';
		$appraisalgroupName = '';
		$data['empcount'] = '';
		$groupflag = 'notassigned';
		$msgarray = array();
		$check_array = array();
		$questionsArr = array();
		$ratingsflag = 'false';
		try
		{
		    if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
			        $checkRatingsExists = $app_rating_model->getAppraisalRatingsbyInitId($id); 
			        if(!empty($checkRatingsExists))
			        	$ratingsflag = 'true';
					$data = $appraisalinitmodel->getConfigData($id);
					//echo '<pre>';print_r($data);exit;
					if(!empty($data))
					{
						$data = $data[0];
						if($data['initialize_status'] == 1)
							$tablename = 'main_pa_questions_privileges';
						else
						    $tablename = 'main_pa_questions_privileges_temp';
						if($data['group_settings'] != 0)
						{
							if($data['group_settings'] == 1)
							{
								$questionPrivileges = $appraisalQsModel->gethrquestionprivileges($id,$tablename,'');
								if($data['category_id'] !='' && $data['category_id'] !='null')
									$questionsArr = $appraisalQsModel->getQuestionsByCategory($data['category_id'],$questionPrivileges['hr_qs']);
								 	
								 	if(!empty($questionPrivileges))
								 	{
								 		if(isset($questionPrivileges['hr_qs']) && isset($questionPrivileges['hr_group_qs_privileges']))
								 		{
								 			if($questionPrivileges['hr_qs'] !='' && $questionPrivileges['hr_group_qs_privileges'] !='')
								 			{
										 		$hr_qs_Arr = explode(',',$questionPrivileges['hr_qs']);
										 		$hr_qs_privileges = json_decode($questionPrivileges['hr_group_qs_privileges'],true);
											 	foreach($hr_qs_privileges as $key => $val)
											 	{
											 		//$val = explode(',',substr($val, 1, -1));
											 		$check_array[$key] = $val;
											 	}
								 			}
								 		}
								 	}
								 	
								$this->view->questionsArr = $questionsArr;		
								$this->view->checkArr = $check_array;
								
							}else
							{
								
								$groupEmployeeCountArr = $appraisalQsModel->getGroupEmployeeCount($id,$tablename);
								$EmpCountArr = $appraisalQsModel->getGroupCountDetails($id,$tablename);
								if(!empty($EmpCountArr))
									$data['empcount'] = $EmpCountArr[0]['empcount'];
								$this->view->groupEmployeeCountArr = $groupEmployeeCountArr;
								
							}			
							$this->view->msgarray = $msgarray;
						}
						$this->view->appraisalid = $id;
						$this->view->encryptapprslid = $init_param;
						$this->view->initializationdata = $data;
						$this->view->ratingsflag=$ratingsflag;
						if($this->getRequest()->getPost()){
							 $result = $this->savequestionPrivilegs($data);	
							 $this->view->msgarray = $result; 
						} 
						$this->view->messages = $this->_helper->flashMessenger->getMessages();
					}else
					{
					   $this->view->ermsg = 'norecord';
					}
                } 
                else
				{
				   $this->view->ermsg = 'norecord';
				}				
			}
            else
			{
			   $this->view->ermsg = 'norecord';
			} 			
		}
		catch(Exception $e)
		{
			$this->view->ermsg = 'nodata';
		}		
	}
	
	public function assigngroupsAction()
	{
		$appraisalinitmodel = new Default_Model_Appraisalinit();
		$appraisalGroupsModel = new Default_Model_Appraisalgroups();
		$appraisalQsModel = new Default_Model_Appraisalquestions();
		$app_rating_model = new Default_Model_Appraisalratings();
		$init_param = $this->_getParam('i',null);
                $init_id = sapp_Global::_decrypt($init_param);
		$id = $init_id;
		$employeeIds = '';
		$groupIds = '';
		$options = '';
		$appraisalgroupName = '';
		$data['empcount'] = '';
		$groupflag = 'notassigned';
		$msgarray = array();
		$check_array = array();
		$questionsArr = array();
		$ratingsflag = 'false';
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;
					$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
		try
		{
		    if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
			        $checkRatingsExists = $app_rating_model->getAppraisalRatingsbyInitId($id); 
			        if(!empty($checkRatingsExists))
        				$ratingsflag = 'true';
					$data = $appraisalinitmodel->getConfigData($id);
					//echo '<pre>';print_r($data);exit;
					if(!empty($data) && $data[0]['status'] == 1)
					{
						$data = $data[0];
						if($data['initialize_status'] == 1)
								$tablename = 'main_pa_questions_privileges';
						else
						    $tablename = 'main_pa_questions_privileges_temp';
						if($data['group_settings'] != 0)
						{
							if($data['group_settings'] == 1)
							{
								
								
								$questionPrivileges = $appraisalQsModel->gethrquestionprivileges($id,$tablename,'');
								
									if($data['category_id'] !='' && $data['category_id'] !='null')
									{
										/*if($data['initialize_status'] == 1)
											$questionsArr = $appraisalQsModel->getQuestionsByCategory($data['category_id'],$questionPrivileges['hr_qs']);
										else*/
											$questionsArr = $appraisalQsModel->getQuestionsByCategory($data['category_id'],'');
									}		
										
								 	//echo '<pre>';print_r($questionPrivileges);exit;
								 	if(!empty($questionPrivileges))
								 	{
								 		if(isset($questionPrivileges['hr_qs']) && isset($questionPrivileges['hr_group_qs_privileges']))
								 		{
								 			if($questionPrivileges['hr_qs'] !='' && $questionPrivileges['hr_group_qs_privileges'] !='')
								 			{
										 		$hr_qs_Arr = explode(',',$questionPrivileges['hr_qs']);
										 		$hr_qs_privileges = json_decode($questionPrivileges['hr_group_qs_privileges'],true);
											 	foreach($hr_qs_privileges as $key => $val)
											 	{
											 		//$val = explode(',',substr($val, 1, -1));
											 		$check_array[$key] = $val;
											 	}
								 			}
								 		}
								 	}
								$this->view->questionsArr = $questionsArr;		
								$this->view->checkArr = $check_array;
								
							}else
							{
								
								$groupEmployeeCountArr = $appraisalQsModel->getGroupEmployeeCount($id,$tablename);
								$EmpCountArr = $appraisalQsModel->getGroupCountDetails($id,$tablename);
								if(!empty($EmpCountArr))
									$data['empcount'] = $EmpCountArr[0]['empcount'];
								//echo '<pre>';print_r($groupEmployeeCountArr);exit;	
								$this->view->groupEmployeeCountArr = $groupEmployeeCountArr;
								
							}			
							$this->view->msgarray = $msgarray;
						}
						if(sapp_Global::_checkprivileges(APPRAISALQUESTIONS,$loginuserGroup,$loginuserRole,'edit') == 'Yes'){
				            $data['poppermission'] = 'yes';
				        }
						$this->view->appraisalid = $id;
						$this->view->encryptapprslid = $init_param;
						$this->view->initializationdata = $data;
						$this->view->ratingsflag=$ratingsflag;
						
						if($this->getRequest()->getPost()){                                                    
							 $result = $this->savequestionPrivilegs($data);	
							 $this->view->msgarray = $result; 
						}
						$this->view->messages = $this->_helper->flashMessenger->getMessages();
					}else
					{
					   $this->view->ermsg = 'norecord';
					}
                } 
                else
				{
				   $this->view->ermsg = 'norecord';
				}				
			}
            else
			{
			   $this->view->ermsg = 'norecord';
			} 			
		}
		catch(Exception $e)
		{
			$this->view->ermsg = 'nodata';
		}		
	}
	
	public function displaygroupedemployeesAction()
	{		
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('displaygroupedemployees', 'html')->initContext();
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;
					$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
		$groupedEmployeeDetails = array();
		$ungroupedEmployeeDetails = array();
		$check_array = array();
		$questionsArr = array();
		
		$groupid = $this->_request->getParam('groupid');
		$appraisalid = $this->_request->getParam('appraisalid');
		$groupname = $this->_request->getParam('groupname');
		$empcount = $this->_request->getParam('empcount');
		
		$appraisalinitmodel = new Default_Model_Appraisalinit();
		$appraisalQsModel = new Default_Model_Appraisalquestions();
		
		if($appraisalid)
		{
			$appraisaldata = $appraisalinitmodel->getConfigData($appraisalid);
			$appraisaldata = $appraisaldata[0];
			if($appraisaldata['initialize_status'] == 1)
				$tablename = 'main_pa_questions_privileges';
			else
			    $tablename = 'main_pa_questions_privileges_temp';
			/** Start Fetching grouped employee details by joining with employee summary table.*/
			    if($groupid!='')
					$groupedEmployeeDetails =  $appraisalQsModel->getEmpGroupDetails($groupid,$appraisalid,$tablename);
			/** End*/ 
		 	/** Start Fetching ungrouped employee details */  
		  		$ungroupedEmployeeDetails =  $appraisalQsModel->getEmpGroupDetails('',$appraisalid,$tablename);
		    /** End */
		  		
		  	if($appraisaldata['category_id'] !='' && $appraisaldata['category_id'] !='null')
				$questionsArr = $appraisalQsModel->getQuestionsByCategory($appraisaldata['category_id'],'');

				if($groupid !='')
		 			$questionPrivileges = $appraisalQsModel->gethrquestionprivileges($appraisalid,$tablename,$groupid);
			 	if(!empty($questionPrivileges))
			 	{
			 		if(isset($questionPrivileges['hr_qs']) && isset($questionPrivileges['hr_group_qs_privileges']))
			 		{
			 			if($questionPrivileges['hr_qs'] !='' && $questionPrivileges['hr_group_qs_privileges'] !='')
						{
					 		$hr_qs_Arr = explode(',',$questionPrivileges['hr_qs']);
					 		$hr_qs_privileges = json_decode($questionPrivileges['hr_group_qs_privileges'],true);
						 	foreach($hr_qs_privileges as $key => $val)
						 	{
						 		//$val = explode(',',substr($val, 1, -1));
						 		$check_array[$key] = $val;
						 	}
						}
			 		}
			 	}
				if(sapp_Global::_checkprivileges(APPRAISALQUESTIONS,$loginuserGroup,$loginuserRole,'edit') == 'Yes'){
				            $appraisaldata['poppermission'] = 'yes';
				    }
										
		    $this->view->groupid = $groupid;
		    $this->view->groupname = $groupname;
		    $this->view->empcount = $empcount;
		    $this->view->appraisalid = $appraisalid;
			$this->view->groupedEmployeeDetails = $groupedEmployeeDetails;
			$this->view->ungroupedEmployeeDetails = $ungroupedEmployeeDetails;
			$this->view->questionsArr = $questionsArr;		
			$this->view->checkArr = $check_array;
			$this->view->initializationdata = $appraisaldata;
		}	

	}
	
	public function showgroupedemployeesAction()
	{		
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('showgroupedemployees', 'html')->initContext();
		$groupedEmployeeDetails = array();
		$ungroupedEmployeeDetails = array();
		$check_array = array();
		$questionsArr = array();
		
		$groupid = $this->_request->getParam('groupid');
		$appraisalid = $this->_request->getParam('appraisalid');
		$groupname = $this->_request->getParam('groupname');
		$empcount = $this->_request->getParam('empcount');
		
		$appraisalinitmodel = new Default_Model_Appraisalinit();
		$appraisalQsModel = new Default_Model_Appraisalquestions();
		
		if($appraisalid)
		{
			$appraisaldata = $appraisalinitmodel->getConfigData($appraisalid);
			$appraisaldata = $appraisaldata[0];
			if($appraisaldata['initialize_status'] == 1)
				$tablename = 'main_pa_questions_privileges';
			else
			    $tablename = 'main_pa_questions_privileges_temp';
			/** Start Fetching grouped employee details by joining with employee summary table.*/
			    if($groupid!='')
					$groupedEmployeeDetails =  $appraisalQsModel->getEmpGroupDetails($groupid,$appraisalid,$tablename);
			/** End*/ 
		 	/** Start Fetching ungrouped employee details */  
		  		$ungroupedEmployeeDetails =  $appraisalQsModel->getEmpGroupDetails('',$appraisalid,$tablename);
		    /** End */
		  		
		  	if($appraisaldata['category_id'] !='' && $appraisaldata['category_id'] !='null')
				$questionsArr = $appraisalQsModel->getQuestionsByCategory($appraisaldata['category_id'],'');

				if($groupid !='')
		 			$questionPrivileges = $appraisalQsModel->gethrquestionprivileges($appraisalid,$tablename,$groupid);
			 	if(!empty($questionPrivileges))
			 	{
			 		if(isset($questionPrivileges['hr_qs']) && isset($questionPrivileges['hr_group_qs_privileges']))
			 		{
			 			if($questionPrivileges['hr_qs'] !='' && $questionPrivileges['hr_group_qs_privileges'] !='')
						{
					 		$hr_qs_Arr = explode(',',$questionPrivileges['hr_qs']);
					 		$hr_qs_privileges = json_decode($questionPrivileges['hr_group_qs_privileges'],true);
						 	foreach($hr_qs_privileges as $key => $val)
						 	{
						 		//$val = explode(',',substr($val, 1, -1));
						 		$check_array[$key] = $val;
						 	}
						}
			 		}
			 	}
										
		    $this->view->groupid = $groupid;
		    $this->view->groupname = $groupname;
		    $this->view->empcount = $empcount;
		    $this->view->appraisalid = $appraisalid;
			$this->view->groupedEmployeeDetails = $groupedEmployeeDetails;
			$this->view->ungroupedEmployeeDetails = $ungroupedEmployeeDetails;
			$this->view->questionsArr = $questionsArr;		
			$this->view->checkArr = $check_array;
			$this->view->initializationdata = $appraisaldata;
		}	

	}	
	
	public function viewgroupedemployeesAction()
	{		
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('viewgroupedemployees', 'html')->initContext();
		$groupedEmployeeDetails = array();
		$ungroupedEmployeeDetails = array();
		$check_array = array();
		$questionsArr = array();
		$qsids = '';
		
		$groupid = $this->_request->getParam('groupid');
		$appraisalid = $this->_request->getParam('appraisalid');
		$groupname = $this->_request->getParam('groupname');
		$empcount = $this->_request->getParam('empcount');
		
		$appraisalinitmodel = new Default_Model_Appraisalinit();
		$appraisalQsModel = new Default_Model_Appraisalquestions();
		
		if($appraisalid)
		{
			$appraisaldata = $appraisalinitmodel->getConfigData($appraisalid);
			$appraisaldata = $appraisaldata[0];
			if($appraisaldata['initialize_status'] == 1)
				$tablename = 'main_pa_questions_privileges';
			else
			    $tablename = 'main_pa_questions_privileges_temp';
			/** Start Fetching grouped employee details by joining with employee summary table.*/
			    if($groupid!='')
					$groupedEmployeeDetails =  $appraisalQsModel->getEmpGroupDetails($groupid,$appraisalid,$tablename);
			/** End*/ 
			  	if($groupid !='')
			  	{
			 			$questionPrivileges = $appraisalQsModel->gethrquestionprivileges($appraisalid,$tablename,$groupid);
			 			
			  	}		
			 	if(!empty($questionPrivileges))
			 	{
			 		if(isset($questionPrivileges['hr_qs']) && isset($questionPrivileges['hr_group_qs_privileges']))
			 		{
			 			if($questionPrivileges['hr_qs'] !='' && $questionPrivileges['hr_group_qs_privileges'] !='')
			 			{
					 		$hr_qs_Arr = explode(',',$questionPrivileges['hr_qs']);
					 		$hr_qs_privileges = json_decode($questionPrivileges['hr_group_qs_privileges'],true);
						 	foreach($hr_qs_privileges as $key => $val)
						 	{
						 		//$val = explode(',',substr($val, 1, -1));
						 		$check_array[$key] = $val;
						 	}
			 			}
			 		}
			 		$qsids = $questionPrivileges['hr_qs'];
			 	}
			 	
			 	if($appraisaldata['category_id'] !='' && $appraisaldata['category_id'] !='null')
					$questionsArr = $appraisalQsModel->getQuestionsByCategory($appraisaldata['category_id'],$qsids);
										
		    $this->view->groupid = $groupid;
		    $this->view->groupname = $groupname;
		    $this->view->empcount = $empcount;
		    $this->view->appraisalid = $appraisalid;
			$this->view->groupedEmployeeDetails = $groupedEmployeeDetails;
			//$this->view->ungroupedEmployeeDetails = $ungroupedEmployeeDetails;
			$this->view->questionsArr = $questionsArr;		
			$this->view->checkArr = $check_array;
			$this->view->initializationdata = $appraisaldata;
		}	

	}
	
	public function savegroupedemployeesajaxAction()
	{
		
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('savegroupedemployeesajax', 'json')->initContext();
		$this->_helper->layout->disableLayout();
		
		$appraisalinitmodel = new Default_Model_Appraisalinit();
		$appraisalQsModel = new Default_Model_Appraisalquestions();
		$appraisalGroupModel = new Default_Model_Appraisalgroups();
		$appraisalPrivTempModel = new Default_Model_Appraisalqstemp();
		$appraisalPrivMainModel = new Default_Model_Appraisalqsmain();
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;
					$loginuserGroup = $auth->getStorage()->read()->group_id;
					$loginuserArr = array('loginuserid' => $loginUserId,
										  'loginuserrole'=>$loginuserRole,
										   'loginusergroup'=>$loginuserGroup,	
										);
		} 
		$finalarray = array();
		$msgarray = array();
		$deleted_emp_arr = array();
		$mgrIndex = 'MC';
		$mgrratIndex = 'MR';
		$empIndex = 'EC';
		$empratIndex = 'ER'; 
		$questions = '';
		$qsprivileges = '';
		$errorflag = "true";
		$result['result'] = '';
		$result['msg'] = '';
                $result['flag'] = "appraisal";
		$duplicateGroupName = 0;
		$questionArr = $this->_request->getParam('check');
		$managercmntsArr = $this->_request->getParam('mgrcmnt');
		$managerratingsArr = $this->_request->getParam('mgrrating');
		$empratingsArr = $this->_request->getParam('empratings');
		$empcmntsArr = $this->_request->getParam('empcmnt');
		$appraisalid = $this->_request->getParam('appraisalid');
		$groupid = $this->_request->getParam('groupid');
		$group_name = $this->_request->getParam('group_name');
		$empids = $this->_request->getParam('existetd_mem_str');
		$originalempids = $this->_request->getParam('original_mem_str');	
		$group_settings = $this->_request->getParam('group_settings');
		if($originalempids !='')
			$orig_emp_arr = explode(',',$originalempids);
		else
		   	$orig_emp_arr = array();
		   	
		$selected_emp_arr = explode(',',$empids);
		$appraisaldata = $appraisalinitmodel->getConfigData($appraisalid);
		$initializestep = 0;
		$initializestep = isset($appraisaldata[0]['initialize_status'])?$appraisaldata[0]['initialize_status']:0;
		if($initializestep == 1)
			$tablename = 'main_pa_questions_privileges';
		else
		    $tablename = 'main_pa_questions_privileges_temp';
		
		 
		$trDb = Zend_Db_Table::getDefaultAdapter();		
        $trDb->beginTransaction();
		try
		{
			if($group_name !='' && !empty($questionArr) && $empids !='')
			{
				if($groupid)
					$CheckDuplicateName = $appraisalGroupModel->getDuplicateGroupName($appraisalid, $group_name,$groupid);
				else 
					$CheckDuplicateName = $appraisalGroupModel->getDuplicateGroupName($appraisalid, $group_name,'');
				if(!empty($CheckDuplicateName))	
					$duplicateGroupName = $CheckDuplicateName[0]['grpcnt'];
					if($duplicateGroupName > 0)
					{
						$result['result'] = 'error';
						$result['msg'] = 'Group name already exists.';
						
					}else
					{
						if(!empty($questionArr))
						{
							for($i=0;$i<sizeof($questionArr);$i++)
							{
								if(isset($managercmntsArr[$questionArr[$i]]))
									$managercomments = 1;
								else
									$managercomments = 0;
									
								if(isset($managerratingsArr[$questionArr[$i]]))
									$managerratings = 1;
								else
									$managerratings = 0;	
								
								if(isset($empratingsArr[$questionArr[$i]]))
									$empratings = 1;
								else
									$empratings = 0;
								
								if(isset($empcmntsArr[$questionArr[$i]]))
									$empcomments = 1;
								else
									$empcomments = 0;
								
								/*$finalstring = '{'."$mgrratIndex:1,$mgrIndex:$managercomments,$empratIndex:$empratings,$empIndex:$empcomments".'}';
								$finalarray[$questionArr[$i]] = $finalstring;
								$finalstring = '';*/
								
								$commntsarry = array($mgrratIndex=>$managerratings,$mgrIndex=>$managercomments,$empratIndex=>$empratings,$empIndex=>$empcomments);	
								$finalarray[$questionArr[$i]] = $commntsarry;
								
							}
						
						$questions = implode(',', $questionArr);
						$qsprivileges = json_encode($finalarray,true);
						
						$groupdata = array('pa_initialization_id'=>$appraisalid,
									'group_name'=>trim($group_name), 
								  'modifiedby'=>$loginUserId,
								   'modifiedby_role'=>$loginuserRole,
								   'modifiedby_group'=>$loginuserGroup,
								  'modifieddate'=>gmdate("Y-m-d H:i:s")
						);
						if($groupid!=''){
							$where = array('id=?'=>$groupid);  
							$actionflag = 2;
						}else
						{
							$groupdata['createdby_role'] = $loginuserRole;
							$groupdata['createdby_group'] = $loginuserGroup;
							$groupdata['createdby'] = $loginUserId;
							$groupdata['createddate'] = gmdate("Y-m-d H:i:s");
							$groupdata['isactive'] = 1;
							$where = '';
							$actionflag = 1;
						}
						
						$Id = $appraisalGroupModel->SaveorUpdateAppraisalGroupsData($groupdata, $where);
						if($Id != 'update')
							$groupid = $Id;
						
						/* Removing privileges for previously assigned employees*/	
						if(!empty($orig_emp_arr))
							$deleted_emp_arr = array_diff($orig_emp_arr,$selected_emp_arr);
						if(!empty($deleted_emp_arr))
						{
							foreach($deleted_emp_arr as $val)
							{
								
								$removeprivileges = $appraisalQsModel->removegroupqsprivileges($tablename,$appraisalid,$val,$loginuserArr);
							}
						}						
						/* END */
						
						$privilegesdata = array('group_id'=>$groupid, 
								  'hr_qs'=>$questions,
								  'hr_group_qs_privileges'=>$qsprivileges,		
								  'modifiedby'=>$loginUserId,
								   'modifiedby_role'=>$loginuserRole,
								   'modifiedby_group'=>$loginuserGroup,
								  'modifieddate'=>gmdate("Y-m-d H:i:s")
						);
						$privielgeswhere = " pa_initialization_id = '".$appraisalid."' and employee_id IN($empids) and module_flag=1 and isactive= 1 ";
						
						if($initializestep == 1)
							$updateprivileges = $appraisalPrivMainModel->SaveorUpdatePrivilegeData($privilegesdata, $privielgeswhere);
						else
							$updateprivileges = $appraisalPrivTempModel->SaveorUpdateData($privilegesdata, $privielgeswhere);	
						
						//$updateprivileges = $appraisalQsModel->updategroupqsprivileges($tablename,$groupid,$questions, $qsprivileges,$appraisalid,$empids,$loginuserArr);
						
						
						/* Update Group setings in initialization table */
						if($group_settings == 0)
							$updatestatus = $appraisalinitmodel->updategroupsettings(2,$appraisalid);
						
						$trDb->commit();
						$result['result'] = 'success';
						$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Employee added to group succesfully."));
						}	
					}		
			}else
			{
				$result['result'] = 'error';
				$result['msg'] = 'Something went wrong.';
			}
			
		}catch(Exception $e)
          {
          		$trDb->rollBack();
          		$result['result'] = 'error';
          		$result['msg'] = $e->getMessage();
          }
		
		$this->_helper->json($result);
	}
	
	
	public function changesettingsAction()
	{
		
		$this->_helper->layout->disableLayout();
		$result['result'] = 'success';
		$result['msg'] = '';
		$appraisalinitmodel = new Default_Model_Appraisalinit();
		
		$appraisalid = $this->_request->getParam('appraisalid');
		$settingflag = $this->_request->getParam('settingflag');
		$trDb = Zend_Db_Table::getDefaultAdapter();	
		
        $trDb->beginTransaction();
		try
		{
		  if($appraisalid)
			$updatestatus = $appraisalinitmodel->updategroupsettings($settingflag,$appraisalid);
			$trDb->commit();
			$result['result'] = 'success';
			$result['msg'] = '';
			$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Settings were discarded succesfully."));
		}
		catch(Exception $e)
          {
          		$trDb->rollBack();
            	$result['result'] = 'error';
            	$result['msg'] = $e->getMessage();
          }	
		
		$this->_helper->json($result);
	}
	
	public function displaysettingsAction()
	{		
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;
					$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
		$msgarray = array();
		$check_array = array();
		$questionsArr = array();
		
		$appraisalid = $this->_request->getParam('appraisalid');
		$settingflag = $this->_request->getParam('settingflag');
		
		$appraisalinitmodel = new Default_Model_Appraisalinit();
		$appraisalQsModel = new Default_Model_Appraisalquestions();
		$data = $appraisalinitmodel->getConfigData($appraisalid);
		$data = $data[0];
		if($data['initialize_status'] == 1)
				$tablename = '';
		else
		    $tablename = 'main_pa_questions_privileges_temp';
		    
			if($settingflag == 1)
			{
				if($data['category_id'] !='' && $data['category_id'] !='null')
					$questionsArr = $appraisalQsModel->getQuestionsByCategory($data['category_id'],'');
					$questionPrivileges = $appraisalQsModel->gethrquestionprivileges($appraisalid,$tablename,'');
					//echo '<pre>';print_r($questionsArr);exit;
				 	if(!empty($questionPrivileges))
				 	{
				 		if(isset($questionPrivileges['hr_qs']) && isset($questionPrivileges['hr_group_qs_privileges']))
				 		{
				 			if($questionPrivileges['hr_qs'] !='' && $questionPrivileges['hr_group_qs_privileges'] !='')
				 			{
						 		$hr_qs_Arr = explode(',',$questionPrivileges['hr_qs']);
						 		$hr_qs_privileges = json_decode($questionPrivileges['hr_group_qs_privileges'],true);
							 	foreach($hr_qs_privileges as $key => $val)
							 	{
							 		//$val = explode(',',substr($val, 1, -1));
							 		$check_array[$key] = $val;
							 	}
				 			}
				 		}
				 	}
				$this->view->questionsArr = $questionsArr;		
				$this->view->checkArr = $check_array;
			} else
			{
				
				$groupEmployeeCountArr = $appraisalQsModel->getGroupEmployeeCount($appraisalid,$tablename);
				$EmpCountArr = $appraisalQsModel->getGroupCountDetails($appraisalid,$tablename);
				if(!empty($EmpCountArr))
					$data['empcount'] = $EmpCountArr[0]['empcount'];
				$this->view->groupEmployeeCountArr = $groupEmployeeCountArr;
			}
			if(sapp_Global::_checkprivileges(APPRAISALQUESTIONS,$loginuserGroup,$loginuserRole,'edit') == 'Yes'){
				            $data['poppermission'] = 'yes';
			}	
			$this->view->appraisalid = $appraisalid;
			$this->view->initializationdata = $data;
			$this->view->settingflag = $settingflag;
			

	}
	
	public function deletegroupedemployeesAction()
	{
		
		$this->_helper->layout->disableLayout();
		$result['result'] = '';
		$result['msg'] = '';
		$result['empcount'] = '';
		$appraisalinitmodel = new Default_Model_Appraisalinit();
		$appraisalQsModel = new Default_Model_Appraisalquestions();
		$appraisalgroupModel = new Default_Model_Appraisalgroups();
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;
					$loginuserGroup = $auth->getStorage()->read()->group_id;
					$loginuserArr = array('loginuserid' => $loginUserId,
										  'loginuserrole'=>$loginuserRole,
										   'loginusergroup'=>$loginuserGroup,	
										);
		} 
		
		$groupid = $this->_request->getParam('groupid');
		$appraisalid = $this->_request->getParam('appraisalid');
		$trDb = Zend_Db_Table::getDefaultAdapter();		
        $trDb->beginTransaction();
		try
		{
			if($groupid && $appraisalid)
			{
				$appraisaldata = $appraisalinitmodel->getConfigData($appraisalid);
				if($appraisaldata[0]['initialize_status'] == 1)
					$tablename = '';
				else
				    $tablename = 'main_pa_questions_privileges_temp';
				
				$updatestatus = $appraisalQsModel->deletegroupqsprivileges($tablename, $appraisalid, $groupid,$loginuserArr);
				$groupdata = array('isactive'=>0, 
								  'modifiedby'=>$loginUserId,
								   'modifiedby_role'=>$loginuserRole,
								   'modifiedby_group'=>$loginuserGroup,
								  'modifieddate'=>gmdate("Y-m-d H:i:s")
				);
						
				$groupwhere = array('id=?'=>$groupid);  
				$appraisalgroupModel->SaveorUpdateAppraisalGroupsData($groupdata, $groupwhere);
				$EmpCountArr = $appraisalQsModel->getGroupCountDetails($appraisalid,$tablename);
				if(!empty($EmpCountArr))
					$result['empcount'] = $EmpCountArr[0]['empcount'];
				$trDb->commit();	
				$result['result'] = 'success';
				$result['msg'] = 'Group deleted succesfully.';	
			}
		}
		catch(Exception $e)
          {
          		$trDb->rollBack();
            	$result['result'] = 'error';
            	$result['msg'] = $e->getMessage();
          }	
		
		$this->_helper->json($result);
	}
	
	public function savequestionPrivilegs($appraisaldata)
	{
		$initializestep = $this->_request->getParam('initializestep');
                
		if($initializestep == 3)
			$result = $this->updateprivafeterinitforallemployees($initializestep,$appraisaldata);
		else
			$result = $this->updateprivilegesforallemployees($initializestep,$appraisaldata);	
			return $result;
		
	}
	
	public function updateprivilegesforallemployees($initializestep,$appraisaldata)
	{
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;
					$loginuserGroup = $auth->getStorage()->read()->group_id;
					$loginuserArr = array('loginuserid' => $loginUserId,
										  'loginuserrole'=>$loginuserRole,
										   'loginusergroup'=>$loginuserGroup,	
										);
					$loginUserEmpId = $auth->getStorage()->read()->employeeId;
					$loginUserfullname = $auth->getStorage()->read()->userfullname;						
		} 
		$appraisalQsModel = new Default_Model_Appraisalquestions();
		$app_init_model = new Default_Model_Appraisalinit();
		$appraisalPrivMainModel = new Default_Model_Appraisalqsmain();
		$appraisalPrivTempModel = new Default_Model_Appraisalqstemp();
		$appraisalempratingsmodel = new Default_Model_Appraisalemployeeratings();
		$usersmodel = new Default_Model_Users();
		$departmentsmodel = new Default_Model_Departments();
		$announcementsModel = new Default_Model_Announcements();
		$deptids = '';
		$title = 'Performance Appraisal';
		$description = 'Performance appraisal initialized';
		//echo '<pre>';print_r($this->getRequest()->getPost());exit;
		$questionArr = $this->_request->getParam('check');
		$managercmntsArr = $this->_request->getParam('mgrcmnt');
		$managerratingsArr = $this->_request->getParam('mgrrating');
		$empratingsArr = $this->_request->getParam('empratings');
		$empcmntsArr = $this->_request->getParam('empcmnt');
		$appraisalid = $this->_request->getParam('appraisalid');
		$initializeflag = $this->_request->getParam('initializeflag');
		$initializestep = $this->_request->getParam('initializestep');
		$group_settings = $this->_request->getParam('group_settings');
		$finalarray = array();
		$msgarray = array();
		$mgrIndex = 'MC';
		$mgrratIndex = 'MR';
		$empIndex = 'EC';
		$empratIndex = 'ER'; 
		$questions = '';
		$qsprivileges = '';
		
		$trDb = Zend_Db_Table::getDefaultAdapter();		
        $trDb->beginTransaction();
		try
		{
			if(!empty($questionArr))
			{
				for($i=0;$i<sizeof($questionArr);$i++)
				{
					if(isset($managercmntsArr[$questionArr[$i]]))
						$managercomments = 1;
					else
						$managercomments = 0;
						
					if(isset($managerratingsArr[$questionArr[$i]]))
						$managerratings = 1;
					else
						$managerratings = 0;	
					
					if(isset($empratingsArr[$questionArr[$i]]))
						$empratings = 1;
					else
						$empratings = 0;
					
					if(isset($empcmntsArr[$questionArr[$i]]))
						$empcomments = 1;
					else
						$empcomments = 0;
					
					/*$finalstring = '{'."$mgrratIndex:1,$mgrIndex:$managercomments,$empratIndex:$empratings,$empIndex:$empcomments".'}';
					$finalarray[$questionArr[$i]] = $finalstring;
					$finalstring = '';*/
					$commntsarry = array($mgrratIndex=>$managerratings,$mgrIndex=>$managercomments,$empratIndex=>$empratings,$empIndex=>$empcomments);	
					$finalarray[$questionArr[$i]] = $commntsarry;
					
				}
		
			
			$questions = implode(',', $questionArr);
			$qsprivileges = json_encode($finalarray,true);
			$tablename = 'main_pa_questions_privileges_temp';

			/* Updating Group settings if it is 0 */
			if($group_settings == 0)
				$updatestatus = $app_init_model->updategroupsettings(1,$appraisalid);
			/* End */	
				
			/* Updating privileges tables with questions */	
				$privilegesdata = array( 'hr_qs'=>$questions,
										 'hr_group_qs_privileges'=>$qsprivileges,		
										 'modifiedby'=>$loginUserId,
										 'modifiedby_role'=>$loginuserRole,
										 'modifiedby_group'=>$loginuserGroup,
										 'modifieddate'=>gmdate("Y-m-d H:i:s")
						);
				$privielgeswhere = " pa_initialization_id = '".$appraisalid."' and module_flag=1 and isactive= 1 ";
				$updateprivileges = $appraisalPrivTempModel->SaveorUpdateData($privilegesdata, $privielgeswhere);
				//$updateprivileges = $appraisalQsModel->updatequestionprivileges($tablename,$questions, $qsprivileges,$appraisalid,$loginuserArr);
				
			/* End */	
				$initdata = array('initialize_status'=>2, 
								  'modifiedby'=>$loginUserId,
								   'modifiedby_role'=>$loginuserRole,
								   'modifiedby_group'=>$loginuserGroup,
								  'modifieddate'=>gmdate("Y-m-d H:i:s")
				);
				$initwhere = array('id=?'=>$appraisalid);  
				
				/**  Initialization 
				 * If initialize
				 * Inserting to main table from temp table
				 * Updating temp table to isactive 0
				 * Updating initialization status in initialization table
				 * Sending mails to managers
				 * Else
				 * Updating initialization status in initialization table
				 */
				if($initializestep == 1)
				{
					$initdata['initialize_status']=1;
					
					$insertQstable = $appraisalQsModel->insertQsData($appraisalid,$loginuserArr);
					$updateTmptable = $appraisalPrivTempModel->updateQsTempData($appraisalid,$loginuserArr);
					$app_init_model->SaveorUpdateAppraisalInitData($initdata, $initwhere);
					
					
					if($appraisaldata['enable_step'] == 2)
					{
						/**
						 * Start 
						 * Inserting or Updating employee ratings table when enabled to employees
						 * If record exists then updating else inserting
						 */
						 $employeeidArr = $appraisalPrivMainModel->getemployeeIDs($appraisalid);
							if(!empty($employeeidArr))
							{
								foreach($employeeidArr as $emp)
								{
									$emprating_Arr = array('pa_initialization_id'=>$appraisalid,
															   'employee_id'=>$emp['employee_id'],
															   'line_manager_1'=>($emp['line_manager_1']!=''?$emp['line_manager_1']:NULL),
															   'line_manager_2'=>($emp['line_manager_2']!=''?$emp['line_manager_2']:NULL),
															   'line_manager_3'=>($emp['line_manager_3']!=''?$emp['line_manager_3']:NULL),
															   'line_manager_4'=>($emp['line_manager_4']!=''?$emp['line_manager_4']:NULL),
															   'line_manager_5'=>($emp['line_manager_5']!=''?$emp['line_manager_5']:NULL),	
																'modifiedby'=>$loginUserId,
											                    'modifiedby_role'=>$loginuserRole,
											                    'modifiedby_group'=>$loginuserGroup,
											                    'modifieddate'=>gmdate("Y-m-d H:i:s"),		
															  );
									$employeeexistArr = $appraisalempratingsmodel->checkEmployeeExists($appraisalid, $emp['employee_id']);
									if($employeeexistArr[0]['empcount']>0)
									{
										$qwhere = " employee_id = '".$emp['employee_id']."' and pa_initialization_id='".$appraisalid."' and isactive=1";
									}else
									{
										$emprating_Arr['createdby'] =$loginUserId;
										$emprating_Arr['createdby_role'] =$loginuserRole;
										$emprating_Arr['createdby_group'] =$loginuserGroup;
										$emprating_Arr['createddate'] =gmdate("Y-m-d H:i:s");
										$qwhere = '';
									}
									$appraisalempratingsmodel->SaveorUpdateAppraisalSkillsData($emprating_Arr, $qwhere);
									 /**
										 * End updating emp ratings table
										 */
									
									
									
								}
							}
							
							
					}//enable_step=2 scenario
					
					//Logs storing					
						$actionflag = 1;
						$tableid  = ''; 
						$menuID =INITIALIZE_APPRAISAL;
						sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
					/**
					 * Sending mails to HR,Super Admin,Management,(managers OR employees) based on enable step.
					 */
						$appraisalratingsmodel = new Default_Model_Appraisalratings();
						$app_manager_model = new Default_Model_Appraisalmanager();
							//to get initialization details using appraisal Id for Business Unit,Department,To Year
					
							$appraisal_details = $appraisalratingsmodel->getappdata($appraisalid);
							if(!empty($appraisal_details))
							{
								
								$businessUnit = $appraisal_details['businessunit_id'];
								$department   = $appraisal_details['deptid'];
								$bunit = $appraisal_details['unitname'];
								$dept = $appraisal_details['deptname'];
								$to_year = $appraisal_details['to_year'];
								$appraisalconfigmodel = new Default_Model_Appraisalconfig();
						        if($department != '')
								$employeeDetailsArr = $appraisalconfigmodel->getUserDetailsByID($businessUnit,$department);
								else
								$employeeDetailsArr = $appraisalconfigmodel->getUserDetailsByID($businessUnit,'');
							
								$dept_str = ($dept == '') ? " ":"and department <b>$dept</b> "; 
								$emp_id_str = ($loginuserRole == SUPERADMINROLE) ? " ":"($loginUserEmpId)";
							 
							   //Preparing Employee array for Bcc
								$empArr = array();
								if(!empty($employeeDetailsArr))
								{
									$empArrList = '';
									foreach($employeeDetailsArr as $emp)
									{
										array_push($empArr,$emp['emailaddress']);
									}
									
								}
					
					       		$mail_str = ($appraisaldata['enable_step'] == 1)? 'to managers':'to employees';
					       	
					       		 $empmgrArr = array();
							       if($appraisaldata['enable_step'] == 1)  //Preparing Managers array for Bcc
							       {
							         $getLine1ManagerId = $appraisalPrivMainModel->getLine1ManagerIdMain($appraisalid);
							          foreach($getLine1ManagerId as $mgr)
										{
										  array_push($empmgrArr,$mgr['emailaddress']);
										}
										
							       }
								   else  //Preparing EmployeeArry for Bcc
							       {
							       	 $empIdArr = array();
							       	 $empIdList = '';
							         $employeeidArr = $appraisalPrivMainModel->getemployeeIDs($appraisalid); //fetching Employee Ids
							         if(!empty($employeeidArr))
									  {
							           foreach($employeeidArr as $emp)
										{
										  array_push($empIdArr,$emp['employee_id']);
										  
										}
										$empIdList = implode(',',$empIdArr); //Preparing Employee Id List
										$empDetailsArr = $app_manager_model->getUserDetailsByEmpID($empIdList); //Fetching employee details
									   if(!empty($empDetailsArr))
										{
											foreach($empDetailsArr as $emp)
											{  
												array_push($empmgrArr,$emp['emailaddress']); //preparing Bcc array
												
											}
										}
										
									 }
										
							       }				
						
					   			$totalArr = array_merge($empArr,$empmgrArr);
						 		//Sending mail to Super admin					
								$options['subject'] = APPLICATION_NAME.': Performance Appraisal Initialization';
                                $options['header'] = 'Performance Appraisal Initialization : '.$to_year;
                                $options['toEmail'] = SUPERADMIN_EMAIL;  
                                $options['toName'] = 'Super Admin';
                                $options['bcc'] 	= $totalArr; 
                                $options['message'] =  "<div style='padding: 0; text-align: left; font-size:14px; font-family:Arial, Helvetica, sans-serif;'>				
														<span style='color:#3b3b3b;'>Hi,</span><br />
														<div style='padding:20px 0 0 0;color:#3b3b3b;'>Performance appraisal has been initialized $mail_str for the year <b>$to_year</b> for business unit <b>$bunit</b>  $dept_str by ".$loginUserfullname.$emp_id_str." </div>
														<div style='padding:20px 0 10px 0;'>Please <a href=".BASE_URL." target='_blank' style='color:#b3512f;'>click here</a> to login  to your <b>".APPLICATION_NAME."</b> account to check the details.</div>
														</div> " ;
                                $mail_id =  sapp_Global::_sendEmail($options); 
                      }
					
					sapp_PerformanceHelper::update_QsParmas_Allemps($questions,$appraisaldata['category_id']);
				
			}
				else
				{
					$app_init_model->SaveorUpdateAppraisalInitData($initdata, $initwhere);
				}
			
			
				$trDb->commit();
				if($initializestep == 1)
				{
					$msgarray = $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>'Appraisal initialized successfully'));
					$this->_redirect('appraisalinit');
				}	
				else
				{
					$msgarray = $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>'Appraisal configurations are saved to be initialized later.'));
					$this->_redirect('appraisalinit/assigngroups/i/'.sapp_Global::_encrypt($appraisalid));
				}		
			}
			
		}catch(Exception $e)
          {
          		$trDb->rollBack();
            	$msgarray = $this->_helper->getHelper("FlashMessenger")->addMessage(array("error"=>$e->getMessage()));
          }
          return $msgarray;		
		
	}
	
	public function updateprivafeterinitforallemployees($initializestep,$appraisaldata)
	{
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;
					$loginuserGroup = $auth->getStorage()->read()->group_id;
					$loginuserArr = array('loginuserid' => $loginUserId,
										  'loginuserrole'=>$loginuserRole,
										   'loginusergroup'=>$loginuserGroup,	
										);
					$loginUserEmpId = $auth->getStorage()->read()->employeeId;	
					$loginUserfullname = $auth->getStorage()->read()->userfullname;				
		} 
		$appraisalQsModel = new Default_Model_Appraisalquestions();
		$appraisalQsTmpModel = new Default_Model_Appraisalqstemp();
		$app_init_model = new Default_Model_Appraisalinit();
		$appraisalPrivMainModel = new Default_Model_Appraisalqsmain();
		$appraisalempratingsmodel = new Default_Model_Appraisalemployeeratings();
		$usersmodel = new Default_Model_Users();
		$departmentsmodel = new Default_Model_Departments();
		$announcementsModel = new Default_Model_Announcements();
		$deptids = '';
		$title = 'Performance Appraisal';
		$description = 'Performance appraisal initialized';
		//echo '<pre>';print_r($this->getRequest()->getPost());exit;
		$questionArr = $this->_request->getParam('check');
		$managercmntsArr = $this->_request->getParam('mgrcmnt');
		$managerratingsArr = $this->_request->getParam('mgrrating');
		$empratingsArr = $this->_request->getParam('empratings');
		$empcmntsArr = $this->_request->getParam('empcmnt');
		$appraisalid = $this->_request->getParam('appraisalid');
		$initializeflag = $this->_request->getParam('initializeflag');
		//$initializestep = $this->_request->getParam('initializestep');
		$group_settings = $this->_request->getParam('group_settings');
		$finalarray = array();
		$msgarray = array();
		$mgrIndex = 'MC';
		$mgrratIndex = 'MR';
		$empIndex = 'EC';
		$empratIndex = 'ER'; 
		$questions = '';
		$qsprivileges = '';
		
		$trDb = Zend_Db_Table::getDefaultAdapter();		
        $trDb->beginTransaction();
		try
		{
			if(!empty($questionArr))
			{
				for($i=0;$i<sizeof($questionArr);$i++)
				{
					if(isset($managercmntsArr[$questionArr[$i]]))
						$managercomments = 1;
					else
						$managercomments = 0;
						
					if(isset($managerratingsArr[$questionArr[$i]]))
						$managerratings = 1;
					else
						$managerratings = 0;	
					
					if(isset($empratingsArr[$questionArr[$i]]))
						$empratings = 1;
					else
						$empratings = 0;
					
					if(isset($empcmntsArr[$questionArr[$i]]))
						$empcomments = 1;
					else
						$empcomments = 0;
					
					/*$finalstring = '{'."$mgrratIndex:1,$mgrIndex:$managercomments,$empratIndex:$empratings,$empIndex:$empcomments".'}';
					$finalarray[$questionArr[$i]] = $finalstring;
					$finalstring = '';*/
					$commntsarry = array($mgrratIndex=>$managerratings,$mgrIndex=>$managercomments,$empratIndex=>$empratings,$empIndex=>$empcomments);	
					$finalarray[$questionArr[$i]] = $commntsarry;
					
				}
		
			
			$questions = implode(',', $questionArr);
			$qsprivileges = json_encode($finalarray,true);
			$tablename = 'main_pa_questions_privileges_temp';
				
			/* Updating privileges tables with questions */	
				$privilegesdata = array( 'hr_qs'=>$questions,
										 'hr_group_qs_privileges'=>$qsprivileges,		
										 'modifiedby'=>$loginUserId,
										 'modifiedby_role'=>$loginuserRole,
										 'modifiedby_group'=>$loginuserGroup,
										 'modifieddate'=>gmdate("Y-m-d H:i:s")
						);
				$privielgeswhere = " pa_initialization_id = '".$appraisalid."' and module_flag=1 and isactive= 1 ";
				$updateprivileges = $appraisalPrivMainModel->SaveorUpdatePrivilegeData($privilegesdata, $privielgeswhere);
				
					if($appraisaldata['enable_step'] == 2)
					{	
						/**
						 * Start 
						 * Inserting or Updating employee ratings table when enabled to employees
						 * If record exists then updating else inserting
						 */
						 $employeeidArr = $appraisalPrivMainModel->getemployeeIDs($appraisalid);
							if(!empty($employeeidArr))
							{
								foreach($employeeidArr as $emp)
								{
									$emprating_Arr = array('pa_initialization_id'=>$appraisalid,
															   'employee_id'=>$emp['employee_id'],
															   'line_manager_1'=>($emp['line_manager_1']!=''?$emp['line_manager_1']:NULL),
															   'line_manager_2'=>($emp['line_manager_2']!=''?$emp['line_manager_2']:NULL),
															   'line_manager_3'=>($emp['line_manager_3']!=''?$emp['line_manager_3']:NULL),
															   'line_manager_4'=>($emp['line_manager_4']!=''?$emp['line_manager_4']:NULL),
															   'line_manager_5'=>($emp['line_manager_5']!=''?$emp['line_manager_5']:NULL),	
																'modifiedby'=>$loginUserId,
											                    'modifiedby_role'=>$loginuserRole,
											                    'modifiedby_group'=>$loginuserGroup,
											                    'modifieddate'=>gmdate("Y-m-d H:i:s"),		
															  );
									$employeeexistArr = $appraisalempratingsmodel->checkEmployeeExists($appraisalid, $emp['employee_id']);
									if($employeeexistArr[0]['empcount']>0)
									{
										$qwhere = " employee_id = '".$emp['employee_id']."' and pa_initialization_id='".$appraisalid."' and isactive=1";
									}else
									{
										$emprating_Arr['createdby'] =$loginUserId;
										$emprating_Arr['createdby_role'] =$loginuserRole;
										$emprating_Arr['createdby_group'] =$loginuserGroup;
										$emprating_Arr['createddate'] =gmdate("Y-m-d H:i:s");
										$qwhere = '';
									}
									$appraisalempratingsmodel->SaveorUpdateAppraisalSkillsData($emprating_Arr, $qwhere);
									/**
									 * 
									 * End Sending Mails and updating emp ratings table
									 */
									 
								}
							}
					}
			/* End */	
					
					//Logs storing					
						$actionflag = 1;
						$tableid  = ''; 
						$menuID =INITIALIZE_APPRAISAL;
						sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
					
					/**
					 * Sending mails to HR,Super Admin,Management,(managers OR employees) based on enable step.
					 */
						$appraisalratingsmodel = new Default_Model_Appraisalratings();
						$app_manager_model = new Default_Model_Appraisalmanager();
							//to get initialization details using appraisal Id for Business Unit,Department,To Year
					
							$appraisal_details = $appraisalratingsmodel->getappdata($appraisalid);
							if(!empty($appraisal_details))
							{
								
								$businessUnit = $appraisal_details['businessunit_id'];
								$department   = $appraisal_details['deptid'];
								$bunit = $appraisal_details['unitname'];
								$dept = $appraisal_details['deptname'];
								$to_year = $appraisal_details['to_year'];
								$appraisalconfigmodel = new Default_Model_Appraisalconfig();
						        if($department != '')
								$employeeDetailsArr = $appraisalconfigmodel->getUserDetailsByID($businessUnit,$department);
								else
								$employeeDetailsArr = $appraisalconfigmodel->getUserDetailsByID($businessUnit,'');
							
								$dept_str = ($dept == '') ? " ":"and department <b>$dept</b> "; 
								$emp_id_str = ($loginuserRole == SUPERADMINROLE) ? " ":"($loginUserEmpId)";
							 
							//Preparing Employee array for Bcc
								$empArr = array();
								if(!empty($employeeDetailsArr))
								{
									$empArrList = '';
									foreach($employeeDetailsArr as $emp)
									{
										array_push($empArr,$emp['emailaddress']);
									}
									
								}
					
					     	   $mail_str = ($appraisaldata['enable_step'] == 1)? 'to managers':'to employees';
					      
					           $empmgrArr = array();
							       if($appraisaldata['enable_step'] == 1)  //Preparing Managers array for Bcc
							       {
							         $getLine1ManagerId = $appraisalPrivMainModel->getLine1ManagerIdMain($appraisalid);
							          foreach($getLine1ManagerId as $mgr)
										{
										  array_push($empmgrArr,$mgr['emailaddress']);
										}
										
							       }
								   else  //Preparing EmployeeArry for Bcc
							       {
							       	 $empIdArr = array();
							       	 $empIdList = '';
							         $employeeidArr = $appraisalPrivMainModel->getemployeeIDs($appraisalid); //fetching Employee Ids
							         if(!empty($employeeidArr))
									  {
							           foreach($employeeidArr as $emp)
										{
										  array_push($empIdArr,$emp['employee_id']);
										  
										}
										$empIdList = implode(',',$empIdArr); //Preparing Employee Id List
										$empDetailsArr = $app_manager_model->getUserDetailsByEmpID($empIdList); //Fetching employee details
									   if(!empty($empDetailsArr))
										{
											foreach($empDetailsArr as $emp)
											{  
												array_push($empmgrArr,$emp['emailaddress']); //preparing Bcc array
												
											}
										}
										
									 }
										
							       }				
						
					   			$totalArr = array_merge($empArr,$empmgrArr);
						
					   
						 //Sending mail to Super admin					
								$options['subject'] = APPLICATION_NAME.': Performance Appraisal Initialization';
                                $options['header'] = 'Performance Appraisal Initialization : '.$to_year;
                                $options['toEmail'] = SUPERADMIN_EMAIL;  
                                $options['toName'] = 'Super Admin';
                                $options['bcc'] 	= $totalArr; 
                                $options['message'] =  "<div style='padding: 0; text-align: left; font-size:14px; font-family:Arial, Helvetica, sans-serif;'>				
														<span style='color:#3b3b3b;'>Hi,</span><br />
														<div style='padding:20px 0 0 0;color:#3b3b3b;'>Performance appraisal has been initialized $mail_str for the year <b>$to_year</b> for business unit <b>$bunit</b>  $dept_str by ".$loginUserfullname.$emp_id_str." </div>
														<div style='padding:20px 0 10px 0;'>Please <a href=".BASE_URL." target='_blank' style='color:#b3512f;'>click here</a> to login  to your <b>".APPLICATION_NAME."</b> account to check the details.</div>
														</div> " ;
                                $mail_id =  sapp_Global::_sendEmail($options); 
							
				
				} 
					sapp_PerformanceHelper::update_QsParmas_Allemps($questions,$appraisaldata['category_id']);
					
				
					$trDb->commit();
					$msgarray = $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>'Appraisal initialized successfully'));
					$this->_redirect('appraisalinit');
			}
			
		}catch(Exception $e)
          {
          		$trDb->rollBack();
            	$msgarray = $this->_helper->getHelper("FlashMessenger")->addMessage(array("error"=>$e->getMessage()));
          }
          return $msgarray;		
		
	}
	
	public function initializegroupAction()
	{
		
		$this->_helper->layout->disableLayout();
		$result['result'] = '';
		$result['msg'] = '';
		$appraisalinitmodel = new Default_Model_Appraisalinit();
		$appraisalQsModel = new Default_Model_Appraisalquestions();
		
		$initflag = $this->_request->getParam('initflag');
		$appraisalid = $this->_request->getParam('appraisalid');
		
		if($initflag == 1)
			$tablename = 'main_pa_questions_privileges';
		else
			$tablename = 'main_pa_questions_privileges_temp';	
		
		$groupEmpCountArr = $appraisalQsModel->getGroupCountDetails($appraisalid,$tablename);
		if(!empty($groupEmpCountArr))
			$groupempcount = $groupEmpCountArr[0]['empcount'];
		else 
			$groupempcount = '';

			if($groupempcount == 0)
			{
				$initresult = $this->initialize($appraisalid,$initflag);
				if($initresult == 'success')
				{
					$result['result'] = 'success';
					$result['msg'] = '';
				}else
				{
					$result['result'] = 'error';
					$result['msg'] = 'Something went wrong.';
				}
			}else
			{
				$result['result'] = 'error';
				$result['msg'] = 'All the employees are not grouped yet. Please group all the employees to initialize.';
			}
		
		
		$this->_helper->json($result);
	}
	
	public function initialize($appraisalid,$initflag)
	{
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;
					$loginuserGroup = $auth->getStorage()->read()->group_id;
					$loginuserArr = array('loginuserid' => $loginUserId,
										  'loginuserrole'=>$loginuserRole,
										   'loginusergroup'=>$loginuserGroup,	
										);
					$loginUserEmpId = $auth->getStorage()->read()->employeeId;
					$loginUserfullname = $auth->getStorage()->read()->userfullname;					
		}
		$appraisalQsModel = new Default_Model_Appraisalquestions();
		$appraisalQsTmpModel = new Default_Model_Appraisalqstemp();
		$app_init_model = new Default_Model_Appraisalinit();
		$appraisalPrivTempModel = new Default_Model_Appraisalqstemp();
		$appraisalPrivMainModel = new Default_Model_Appraisalqsmain();
		$appraisalempratingsmodel = new Default_Model_Appraisalemployeeratings();
		$app_manager_model = new Default_Model_Appraisalmanager();
		$usersmodel = new Default_Model_Users();
		$departmentsmodel = new Default_Model_Departments();
		$announcementsModel = new Default_Model_Announcements();
		$deptids = '';
		$questions = '';
		$title = 'Performance Appraisal';
		$description = 'Performance appraisal initialized';
		$trDb = Zend_Db_Table::getDefaultAdapter();		
        $trDb->beginTransaction();
        try 
        {
        	$data = $app_init_model->getConfigData($appraisalid);
        	$appraisaldata = $data[0];
        	$qsdataArr = $appraisalPrivTempModel->getAppraisalQuestions($appraisalid);
        	if(!empty($qsdataArr))
        	{
        		foreach($qsdataArr as $qs)
        		{
        			$questions.=$qs['hr_qs'].',';
        		}
        		$questions=rtrim($questions,',');
        	}
        	$questions = implode(',', array_keys(array_flip(explode(',', $questions))));
        	
        		$initdata = array('initialize_status'=>2, 
								  'modifiedby'=>$loginUserId,
								   'modifiedby_role'=>$loginuserRole,
								   'modifiedby_group'=>$loginuserGroup,
								  'modifieddate'=>gmdate("Y-m-d H:i:s")
				);
				$initwhere = array('id=?'=>$appraisalid);  
				
			if($initflag == 1)
			{
					$initdata['initialize_status']=1;
					$insertQstable = $appraisalQsModel->insertQsData($appraisalid,$loginuserArr);
					$updateTmptable = $appraisalPrivTempModel->updateQsTempData($appraisalid,$loginuserArr);
					$con = $app_init_model->SaveorUpdateAppraisalInitData($initdata, $initwhere);
					
					if($appraisaldata['enable_step'] == 2)
					{
						/**
						 * Start 
						 * Inserting or Updating employee ratings table when enabled to employees
						 * If record exists then updating else inserting
						 */
						 $employeeidArr = $appraisalPrivMainModel->getemployeeIDs($appraisalid);
							if(!empty($employeeidArr))
							{
								foreach($employeeidArr as $emp)
								{
									$emprating_Arr = array('pa_initialization_id'=>$appraisalid,
															   'employee_id'=>$emp['employee_id'],
															   'line_manager_1'=>($emp['line_manager_1']!=''?$emp['line_manager_1']:NULL),
															   'line_manager_2'=>($emp['line_manager_2']!=''?$emp['line_manager_2']:NULL),
															   'line_manager_3'=>($emp['line_manager_3']!=''?$emp['line_manager_3']:NULL),
															   'line_manager_4'=>($emp['line_manager_4']!=''?$emp['line_manager_4']:NULL),
															   'line_manager_5'=>($emp['line_manager_5']!=''?$emp['line_manager_5']:NULL),	
																'modifiedby'=>$loginUserId,
											                    'modifiedby_role'=>$loginuserRole,
											                    'modifiedby_group'=>$loginuserGroup,
											                    'modifieddate'=>gmdate("Y-m-d H:i:s"),		
															  );
									$employeeexistArr = $appraisalempratingsmodel->checkEmployeeExists($appraisalid, $emp['employee_id']);
									if($employeeexistArr[0]['empcount']>0)
									{
										$qwhere = " employee_id = '".$emp['employee_id']."' and pa_initialization_id='".$appraisalid."' and isactive=1";
									}else
									{
										$emprating_Arr['createdby'] =$loginUserId;
										$emprating_Arr['createdby_role'] =$loginuserRole;
										$emprating_Arr['createdby_group'] =$loginuserGroup;
										$emprating_Arr['createddate'] =gmdate("Y-m-d H:i:s");
										$qwhere = '';
									}
									$appraisalempratingsmodel->SaveorUpdateAppraisalSkillsData($emprating_Arr, $qwhere);
									/**
									 * End
									 */
							   }
						}
					}
					
					/*
				 	 *   Logs Storing
				 	 */
						if($con == 'update')
						$actionflag = 2;
						else 
						$actionflag = 1;
						$tableid  = ''; 
						$menuID =INITIALIZE_APPRAISAL;
						$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
						
					/*
				 	 *  Logs storing ends
				 	 */
					
						
					/**
					 * Sending mails to HR,Super Admin,Management,(managers OR employees) based on enable step.
					 */
						$appraisalratingsmodel = new Default_Model_Appraisalratings();
							//to get initialization details using appraisal Id for Business Unit,Department,To Year
					
							$appraisal_details = $appraisalratingsmodel->getappdata($appraisalid);
							if(!empty($appraisal_details))
							{
								
								$businessUnit = $appraisal_details['businessunit_id'];
								$department   = $appraisal_details['deptid'];
								$bunit = $appraisal_details['unitname'];
								$dept = $appraisal_details['deptname'];
								$to_year = $appraisal_details['to_year'];
								$appraisalconfigmodel = new Default_Model_Appraisalconfig();
						        if($department != '')
								$employeeDetailsArr = $appraisalconfigmodel->getUserDetailsByID($businessUnit,$department);
								else
								$employeeDetailsArr = $appraisalconfigmodel->getUserDetailsByID($businessUnit,'');
							
								$dept_str = ($dept == '') ? " ":"and department <b>$dept</b> "; 
								$emp_id_str = ($loginuserRole == SUPERADMINROLE) ? " ":"($loginUserEmpId)";
							 
							//Preparing Employee array for Bcc
								$empArr = array();
								if(!empty($employeeDetailsArr))
								{
									$empArrList = '';
									foreach($employeeDetailsArr as $emp)
									{
										array_push($empArr,$emp['emailaddress']);
									}
									
								}
					
					       $mail_str = ($appraisaldata['enable_step'] == 1)? 'to managers':'to employees';
						
					   		$empmgrArr = array();
							       if($appraisaldata['enable_step'] == 1)  //Preparing Managers array for Bcc
							       {
							         $getLine1ManagerId = $appraisalPrivMainModel->getLine1ManagerIdMain($appraisalid);
							          foreach($getLine1ManagerId as $mgr)
										{
										  array_push($empmgrArr,$mgr['emailaddress']);
										}
										
							       }
								   else  //Preparing EmployeeArry for Bcc
							       {
							       	 $empIdArr = array();
							       	 $empIdList = '';
							         $employeeidArr = $appraisalPrivMainModel->getemployeeIDs($appraisalid); //fetching Employee Ids
							         if(!empty($employeeidArr))
									  {
							           foreach($employeeidArr as $emp)
										{
										  array_push($empIdArr,$emp['employee_id']);
										  
										}
										$empIdList = implode(',',$empIdArr); //Preparing Employee Id List
										$empDetailsArr = $app_manager_model->getUserDetailsByEmpID($empIdList); //Fetching employee details
									   if(!empty($empDetailsArr))
										{
											foreach($empDetailsArr as $emp)
											{  
												array_push($empmgrArr,$emp['emailaddress']); //preparing Bcc array
												
											}
										}
										
									 }
										
							       }				
						
					   			$totalArr = array_merge($empArr,$empmgrArr);
						    //Sending mail to Super admin					
								$options['subject'] = APPLICATION_NAME.': Performance Appraisal Initialization';
                                $options['header'] = 'Performance Appraisal Initialization : '.$to_year;
                                $options['toEmail'] = SUPERADMIN_EMAIL;  
                                $options['toName'] = 'Super Admin';
                                $options['bcc'] 	= $totalArr; 
                                $options['message'] =  "<div style='padding: 0; text-align: left; font-size:14px; font-family:Arial, Helvetica, sans-serif;'>				
														<span style='color:#3b3b3b;'>Hi,</span><br />
														<div style='padding:20px 0 0 0;color:#3b3b3b;'>Performance appraisal has been initialized $mail_str for the year <b>$to_year</b> for business unit <b>$bunit</b>  $dept_str by ".$loginUserfullname.$emp_id_str." </div>
														<div style='padding:20px 0 10px 0;'>Please <a href=".BASE_URL." target='_blank' style='color:#b3512f;'>click here</a> to login  to your <b>".APPLICATION_NAME."</b> account to check the details.</div>
														</div> " ;
                                $mail_id =  sapp_Global::_sendEmail($options); 
                                
              
			}
					if($appraisaldata['enable_step'] == 2)
					{
						$appImpleData = sapp_PerformanceHelper::check_per_implmentation($appraisaldata['businessunit_id'], $appraisaldata['department_id']);
						
					}  

					sapp_PerformanceHelper::update_QsParmas_Allemps($questions,$appraisaldata['category_id']);
			}
	    	else 
			{
				$app_init_model->SaveorUpdateAppraisalInitData($initdata, $initwhere);
			}
			$trDb->commit();
			if($initflag == 1)
			$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>'Appraisal initialized successfully'));
			else
			$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>'Appraisal configurations are saved to be initialized later'));
			return 'success';
         }
        
		catch(Exception $e)
          {
          		$trDb->rollBack();
          		return 'error';
          }
        	
	}
	
	public function completeappraisalAction()
	{
		
		$this->_helper->layout->disableLayout();
		$result['result'] = '';
		$result['msg'] = '';
		$appraisalinitmodel = new Default_Model_Appraisalinit();
		$appraisalQsModel = new Default_Model_Appraisalquestions();
		
		$enablestepflag = $this->_request->getParam('enablestepflag');
		$appraisalid = $this->_request->getParam('appraisalid');
		$status = $this->_request->getParam('status');
		$enablestep = $this->_request->getParam('enablestep');
		$managers_due_date = $this->_request->getParam('managers_due_date');
		$employee_due_date = $this->_request->getParam('employee_due_date');
		$buid = $this->_request->getParam('buid');
		$perfflag = $this->_request->getParam('perfflag');
		$deptid = $this->_request->getParam('deptid');
		
		if($enablestepflag == 1 && $status == 1 && $enablestep ==2 && $employee_due_date!='' && $managers_due_date!='')
		{
			
			$initialize_Arr = array('managers_due_date'=>$managers_due_date,
									'employees_due_date'=>$employee_due_date,
									'enable_step'=>$enablestep,
									'buid'=>$buid,
									'perfflag'=>$perfflag,
									'deptid'=>$deptid,
									);
									
			$empratingsArr = $this->initializeemployeeratings($appraisalid,$initialize_Arr);
			if(!empty($empratingsArr))
			{
				$result['result'] = $empratingsArr['result'];
				$result['msg'] = $empratingsArr['msg'];
			}
			
		}else 
		{
			$closedappArr = $this->closeappraisal($appraisalid,$status,$enablestepflag,$buid,$perfflag,$deptid);
			if(!empty($closedappArr))
			{
				$result['result'] = $closedappArr['result'];
				$result['msg'] = $closedappArr['msg'];
			}
			
		}
		
		$this->_helper->json($result);
	}
	
	
	public function initializeemployeeratings($appraisalid,$initialize_Arr)
	{
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;
					$loginuserGroup = $auth->getStorage()->read()->group_id;
					$loginuserArr = array('loginuserid' => $loginUserId,
										  'loginuserrole'=>$loginuserRole,
										   'loginusergroup'=>$loginuserGroup,	
										);
					$loginUserEmpId = $auth->getStorage()->read()->employeeId;
					$loginUserfullname = $auth->getStorage()->read()->userfullname;					
		}
		$appraisalinitmodel = new Default_Model_Appraisalinit();
		$appraisalqsmodel = new Default_Model_Appraisalqsmain();
		$appraisalempratingsmodel = new Default_Model_Appraisalemployeeratings();
		//$appraisalPrivMainModel = new Default_Model_Appraisalqsmain();
		 $app_manager_model = new Default_Model_Appraisalmanager();
		$usersmodel = new Default_Model_Users();
		$departmentsmodel = new Default_Model_Departments();
		$announcementsModel = new Default_Model_Announcements();
		$deptids = '';
		$title = 'Performance Appraisal';
		$description = 'Performance appraisal initialized';
		$result = array();
		$id = '';
		if($appraisalid)
		{
			$trDb = Zend_Db_Table::getDefaultAdapter();		
	        $trDb->beginTransaction();
			try 
			{
				/** Start
				 * Updating initialization table
				 */
				
				//$data = $appraisalinitmodel->getConfigData($appraisalid);
        		//$appraisaldata = $data[0];
				if(!empty($initialize_Arr))
				{
					$init_Arr = array(
					                    'enable_step'=>$initialize_Arr['enable_step'],
					                    'managers_due_date' => sapp_Global::change_date($initialize_Arr['managers_due_date'], 'database'),
										'employees_due_date' => sapp_Global::change_date($initialize_Arr['employees_due_date'], 'database'),
					                    'modifiedby'=>$loginUserId,
					                    'modifiedby_role'=>$loginuserRole,
					                    'modifiedby_group'=>$loginuserGroup,
					                    'modifieddate'=>gmdate("Y-m-d H:i:s"),
									);
					$where = array('id=?'=>$appraisalid);				
					$Id = $appraisalinitmodel->SaveorUpdateAppraisalInitData($init_Arr, $where);				
				}
				
				 if($initialize_Arr['enable_step'] == 2)
					{
						
						/**
						 * Start 
						 * Inserting or Updating employee ratings table when enabled to employees
						 * If record exists then updating else inserting
						 */
						 $employeeidArr = $appraisalqsmodel->getemployeeIDs($appraisalid);
							if(!empty($employeeidArr))
							{
								foreach($employeeidArr as $emp)
								{
									$emprating_Arr = array('pa_initialization_id'=>$appraisalid,
															   'employee_id'=>$emp['employee_id'],
															   'line_manager_1'=>($emp['line_manager_1']!=''?$emp['line_manager_1']:NULL),
															   'line_manager_2'=>($emp['line_manager_2']!=''?$emp['line_manager_2']:NULL),
															   'line_manager_3'=>($emp['line_manager_3']!=''?$emp['line_manager_3']:NULL),
															   'line_manager_4'=>($emp['line_manager_4']!=''?$emp['line_manager_4']:NULL),
															   'line_manager_5'=>($emp['line_manager_5']!=''?$emp['line_manager_5']:NULL),	
																'modifiedby'=>$loginUserId,
											                    'modifiedby_role'=>$loginuserRole,
											                    'modifiedby_group'=>$loginuserGroup,
											                    'modifieddate'=>gmdate("Y-m-d H:i:s"),		
															  );
									$employeeexistArr = $appraisalempratingsmodel->checkEmployeeExists($appraisalid, $emp['employee_id']);
									if($employeeexistArr[0]['empcount']>0)
									{
										$qwhere = " employee_id = '".$emp['employee_id']."' and pa_initialization_id='".$appraisalid."' and isactive=1";
									}else
									{
										$emprating_Arr['createdby'] =$loginUserId;
										$emprating_Arr['createdby_role'] =$loginuserRole;
										$emprating_Arr['createdby_group'] =$loginuserGroup;
										$emprating_Arr['createddate'] =gmdate("Y-m-d H:i:s");
										$qwhere = '';
									}
									$appraisalempratingsmodel->SaveorUpdateAppraisalSkillsData($emprating_Arr, $qwhere);
									/**
									 * End
									 */
									
									
									
								}
							}
							
							
					}//enable_step=2 scenario
				/**
					End
				 */
				
				
					//Logs storing					
						$actionflag = 1;
						$tableid  = ''; 
						$menuID =INITIALIZE_APPRAISAL;
						sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
				
						
						
						
							/**
							 * Sending mails to managers OR employees based on enable step.
							 */
								
							 $app_manager_model = new Default_Model_Appraisalmanager();
							$appraisalratingsmodel = new Default_Model_Appraisalratings();
							//to get initialization details using appraisal Id for Business Unit,Department,To Year
					
							$appraisal_details = $appraisalratingsmodel->getappdata($appraisalid);
							if(!empty($appraisal_details))
							{
								
								$businessUnit = $appraisal_details['businessunit_id'];
								$department   = $appraisal_details['deptid'];
								$bunit = $appraisal_details['unitname'];
								$dept = $appraisal_details['deptname'];
								$to_year = $appraisal_details['to_year'];
								$appraisalconfigmodel = new Default_Model_Appraisalconfig();
						        if($department != '')
								$employeeDetailsArr = $appraisalconfigmodel->getUserDetailsByID($businessUnit,$department);
								else
								$employeeDetailsArr = $appraisalconfigmodel->getUserDetailsByID($businessUnit,'');
							
								$dept_str = ($dept == '') ? " ":"and department <b>$dept</b> "; 
								$emp_id_str = ($loginuserRole == SUPERADMINROLE) ? " ":"($loginUserEmpId)";
							 
							//Preparing Employee array for Bcc
								$empArr = array();
								if(!empty($employeeDetailsArr))
								{
									$empArrList = '';
									foreach($employeeDetailsArr as $emp)
									{
										array_push($empArr,$emp['emailaddress']);
									}
									
								}
					
					   		 $mail_str = ($initialize_Arr['enable_step'] == 1)? 'to managers':'to employees';
					   		 
					   		  $empmgrArr = array();
							       if($initialize_Arr['enable_step'] == 1)  //Preparing Managers array for Bcc
							       {
							         $getLine1ManagerId = $appraisalqsmodel->getLine1ManagerIdMain($appraisalid);
							          foreach($getLine1ManagerId as $mgr)
										{
										  array_push($empmgrArr,$mgr['emailaddress']);
										}
										
							       }
								   else  //Preparing EmployeeArry for Bcc
							       {
							       	 $empIdArr = array();
							       	 $empIdList = '';
							         $employeeidArr = $appraisalqsmodel->getemployeeIDs($appraisalid); //fetching Employee Ids
							         if(!empty($employeeidArr))
									  {
							           foreach($employeeidArr as $emp)
										{
										  array_push($empIdArr,$emp['employee_id']);
										  array_push($empIdArr,$emp['line_manager_1']);
										}
										$empIdList = implode(',',$empIdArr); //Preparing Employee Id List
										$empDetailsArr = $app_manager_model->getUserDetailsByEmpID($empIdList); //Fetching employee details
									   if(!empty($empDetailsArr))
										{
											foreach($empDetailsArr as $emp)
											{  
												array_push($empmgrArr,$emp['emailaddress']); //preparing Bcc array
												
											}
										}
										
									 }
										
							       }				
						
					   			$totalArr = array_merge($empArr,$empmgrArr);
					   			
						 //Sending mail to Super admin,HR,Management					
								$options['subject'] = APPLICATION_NAME.': Performance Appraisal Initialization';
                                $options['header'] = 'Performance Appraisal Initialization : '.$to_year;
                                $options['toEmail'] = SUPERADMIN_EMAIL;  
                                $options['toName'] = 'Super Admin';
                                $options['bcc'] 	= $totalArr; 
                                $options['message'] =  "<div style='padding: 0; text-align: left; font-size:14px; font-family:Arial, Helvetica, sans-serif;'>				
														<span style='color:#3b3b3b;'>Hi,</span><br />
														<div style='padding:20px 0 0 0;color:#3b3b3b;'>Performance appraisal has been initialized $mail_str for the year <b>$to_year</b> for business unit <b>$bunit</b>  $dept_str by ".$loginUserfullname.$emp_id_str." </div>
														<div style='padding:20px 0 10px 0;'>Please <a href=".BASE_URL." target='_blank' style='color:#b3512f;'>click here</a> to login  to your <b>".APPLICATION_NAME."</b> account to check the details.</div>
														</div> " ;
                                
                                $mail_id =  sapp_Global::_sendEmail($options); 
                    
				}
				
				$trDb->commit();
				
				$result['msg'] = 'Appraisal process updated successfully';
	          	$result['result'] = 'success';
	          	$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Appraisal process updated successfully"));
			
			}
			catch(Exception $e)
	          {
	          		$trDb->rollBack();
	          		$result['msg'] = $e->getMessage();
	          		$result['result'] = 'error';
	          }
			
		}
		
		return $result;
	}
	
	public function closeappraisal($appraisalid,$status,$enablestepflag,$buid,$perfflag,$deptid)
	{
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;
					$loginuserGroup = $auth->getStorage()->read()->group_id;
					$loginuserArr = array('loginuserid' => $loginUserId,
										  'loginuserrole'=>$loginuserRole,
										   'loginusergroup'=>$loginuserGroup,	
										);
		}
		$appraisalinitmodel = new Default_Model_Appraisalinit();
		$appraisalqsmodel = new Default_Model_Appraisalqsmain();
		$appraisalempratingsmodel = new Default_Model_Appraisalemployeeratings();
		$usersmodel = new Default_Model_Users();
		$departmentsmodel = new Default_Model_Departments();
		$announcementsModel = new Default_Model_Announcements();
		$deptids = '';
		$title = 'Performance Appraisal';
		$description = 'Performance appraisal initialized';
		$result = array();
		$employeeidArr = array();
		if($status == 2)
			$statustext = 'Closed';
		else
			$statustext = 'Forcefully Closed';	
		
		if($appraisalid)
		{
			$trDb = Zend_Db_Table::getDefaultAdapter();		
	        $trDb->beginTransaction();
			try 
			{
				/** Start
				 * Updating initialization table
				 */
					
						$init_Arr = array(
						                    'status'=>$status,
						                    'modifiedby'=>$loginUserId,
						                    'modifiedby_role'=>$loginuserRole,
						                    'modifiedby_group'=>$loginuserGroup,
						                    'modifieddate'=>gmdate("Y-m-d H:i:s"),
										);
						$where = array('id=?'=>$appraisalid);				
						$Id = $appraisalinitmodel->SaveorUpdateAppraisalInitData($init_Arr, $where);				
					
					/**
						End
					 */
					
					/** Start
						 * Sending Mails to employees
				    */
					
					if($enablestepflag == 1)
						$employeeidArr = $appraisalqsmodel->getemployeeIDs($appraisalid);
					else
						$employeeidArr = $appraisalempratingsmodel->getEmployeeIds($appraisalid);	
						
						 if(!empty($employeeidArr))	
						 {
						 	foreach($employeeidArr as $emp)
						 	{
						 			$employeeDetailsArr = $usersmodel->getUserDetailsByID($emp['employee_id'],'');
									if(!empty($employeeDetailsArr))
									{
											$options['subject'] = APPLICATION_NAME.': Performance Appraisal '.$statustext;
			                                $options['header'] = 'Performance Appraisal '.$statustext;
			                                $options['toEmail'] = $employeeDetailsArr[0]['emailaddress'];  
			                                $options['toName'] = $employeeDetailsArr[0]['userfullname'];
			                                $options['message'] = 'Dear '.$employeeDetailsArr[0]['userfullname'].', performance appraisal '.$statustext;
			                                $options['cron'] = 'yes';
				                           // sapp_Global::_sendEmail($options);
									}
						 	}
						 }
						 
						 /**
						 * End
						 */	
						 
						 /** 
						 	* Mail to performance Appraisal Group
						 */
							if (defined('PER_APPRAISAL_'.$buid) && $buid !='')
						    {
						    		$options['subject'] = APPLICATION_NAME.': Performance Appraisal '.$statustext;
	                                $options['header'] = 'Performance Appraisal';
	                                $options['toEmail'] = constant('PER_APPRAISAL_'.$buid);  
	                                $options['toName'] = 'Performance Appraisal';
	                                $options['message'] = 'Performance appraisal initiated '.$statustext;
	                                $options['cron'] = 'yes';
		                          //  sapp_Global::_sendEmail($options);
						    	
						    }
						    
						/**
						 * End
						 */    
							
						
				 $trDb->commit();
				 $result['msg'] = 'Performance Appraisal '.$statustext;
	          	 $result['result'] = 'success';
	          	 $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Performance Appraisal ".$statustext));
		      }
			  catch(Exception $e)
	          {
	          		$trDb->rollBack();
	          		$result['msg'] = $e->getMessage();
	          		$result['result'] = 'error';
	          } 	
		}
		
		return $result;
	}
	
	public function checkemployeeresponseAction()
	{
		$this->_helper->layout->disableLayout();
		$appraisalempratingsmodel = new Default_Model_Appraisalemployeeratings();
		$result['result'] = 'success';
		$appraisalid = $this->_request->getParam('appraisalid');
		if($appraisalid)
		{
			$empresponseArr = $appraisalempratingsmodel->checkEmployeeResponse($appraisalid);
			if(!empty($empresponseArr))
			{
				if($empresponseArr[0]['empresponse'] > 0)
				{
					$result['result'] = 'error';
				}
			}
		}
		$this->_helper->json($result);
	}
	//function to get employees line managers
	public function getemployeeslinemanagersAction()
	{
		$initialization_id = $this->_getParam('initialization_id',null);
		$employee_id = $this->_getParam('employee_id',null);
		$employees_managers = array();
        if(is_numeric($initialization_id) && is_numeric($employee_id))
        {
            $app_init_model = new Default_Model_Appraisalinit();
            $employees_managers = $app_init_model->getEmployeeLineManagers($employee_id,$initialization_id);
        }
        $this->view->employees_managers = $employees_managers;		
        $this->render('displayemployeeslinemanagers');		
	}
	
	//function to save configure managers by organization hierarchy, all managers at a time
	public function savemngrorghierarchyAction()
    {
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
        }		
        $app_init_model = new Default_Model_Appraisalinit();
        $ques_temp_model = new Default_Model_Appraisalqstemp();
        $ques_org_model = new Default_Model_Appraisalqsmain();
        $app_rating_model = new Default_Model_Appraisalratings();
        // $init_id = $this->_getParam('init_id',null);
        $init_param = $this->_getParam('i',null);
        $init_id = sapp_Global::_decrypt($init_param);
		$init_data = $app_init_model->getConfigData($init_id);
        if(count($init_data) > 0)
		{
            $init_data = $init_data[0];	
		}
		$this->view->init_id = $init_id;        
        $this->view->init_data = $init_data;
        $this->view->msg_arr = array();
        $ratingsflag = 'false';
        $checkRatingsExists = $app_rating_model->getAppraisalRatingsbyInitId($init_id); 
        if(!empty($checkRatingsExists))
		{
        	$ratingsflag = 'true';
		}
		$red_result = "";
		$type_arr = array('line' => 1,'report' => 2);
		$trDb = Zend_Db_Table::getDefaultAdapter();	
		//get manager ids as comma seperated string
		$manager_ids = $this->_getParam('mid',null);
		//break the manager ids into array
		$mngr_ids_array = explode(',',$manager_ids);
		$get_managers_data = array();
		$get_managers_data = $app_init_model->getManagers_report($init_id);
		$final_managers_data = array();
		//get the managers data and build an array with manager id as key
		if(isset($get_managers_data) && count($get_managers_data) > 0)
		{
			foreach($get_managers_data as $data)
			{
				$mngr_id = isset($data['user_id'])?$data['user_id']:0;
				$final_managers_data[$mngr_id] = $data;
			}
		}
		if(isset($mngr_ids_array) && count($mngr_ids_array) > 0)
		{
			$choose_option = 'report';
			foreach($mngr_ids_array as $mngr)
			{
				$tot_ids = array();
				$context = 'add';
				$content = array();
				$content = $app_init_model->getdisplayacontentreportacc($init_id,$mngr);
				if($context == 'add')
				{
					$init_sdata = array(                            
						'manager_level_type' => $type_arr[$choose_option],
					);
					$app_result = array();
					$app_result = $app_init_model->SaveorUpdateAppraisalInitData($init_sdata, " id = ".$init_id);
					$qresult = '';
					if($app_result === 'update')
					{
						if(count($content) > 0)
						{
							foreach($content as $cont)
							{
								if(is_numeric($cont['user_id']))
								{
									array_push($tot_ids,$cont['user_id']);
								}
							}
						}
						$line_str_arr = array();
						$line_str_arr['line_manager_1'] = $mngr;
						if(count($tot_ids) > 0)
						{
							foreach ($tot_ids as $emp_id)
							{
								$qdata = array(
									'pa_initialization_id' => $init_id,
									'manager_levels' => 1,
									'employee_id' => $emp_id,
									'module_flag' => 1,
									'createdby' => $loginUserId,
									'createdby_role' => $loginuserRole,
									'createdby_group' => $loginuserGroup,
									'modifiedby' => $loginUserId,
									'modifiedby_role' => $loginuserRole,
									'modifiedby_group' => $loginuserGroup,
									'createddate' => gmdate("Y-m-d H:i:s"),
									'modifieddate' => gmdate("Y-m-d H:i:s"),
									'isactive' => 1,
								);
								$qdata = array_merge($qdata,$line_str_arr);
								if($init_data['initialize_status'] == 1)
								{
									$qresult = $ques_org_model->SaveorUpdatePrivilegeData($qdata, '');
								}
								else 
								{
									$qresult = $ques_temp_model->SaveorUpdateData($qdata, '');
								}
							}
						}
					}
					if($qresult !== '' && $app_result !== '')
					{
						$red_result = 'saved';
					}
					$message = "Appraisal process updated successfully";					
				}
			}
            if($red_result === 'saved')
            {      
            	$message = "Appraisal process updated successfully";          
                $this->_helper->FlashMessenger()->setNamespace('conf_success')->addMessage($message); 
                $this->_redirect('appraisalinit/confmanagers/i/'.sapp_Global::_encrypt($init_id));
            }
		}
        $this->view->ratingsflag=$ratingsflag;
        $this->render('configuremanagers');
    }
	//function to fill the dropdown for configure line manager 
	public function getconfiglinemanagersAction()
	{
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
        }
		$line1_id = $this->_getParam('line1_id',null);
		$init_id = $this->_getParam('init_id',null);
		$employeeids = $this->_getParam('employee_ids',null);
		$businessunit_id = $this->_getParam('businessunit_id',null);
		$department_id	= $this->_getParam('department_id',null);
		$selected_val	= $this->_getParam('selected_val',null);
        $app_init_model = new Default_Model_Appraisalinit();
		$managers_data = $app_init_model->getRepManagers_report($line1_id,$init_id,$employeeids,$businessunit_id,$department_id);
		$managers_html = '<option value="">Select L1 Manager</option>';
		if(isset($managers_data))
		{
			foreach($managers_data as $data)
			{
				$managers_html .= "<option value='".$data['user_id']."' ".(!empty($selected_val)?(($selected_val==$data['user_id'])?'selected=\"selected\"':''):'')." >".$data['userfullname']."</option>";
			}
		}
		$this->_helper->json($managers_html);
	}
	//function to check the appraisal on year change in add initialize appraisal
	/*public function check_for_appraisal()
	{
		$businessunit_id = $this->_getParam('businessunit_id',null);
		$department_id	= $this->_getParam('department_id',null);	
		$from_year	= $this->_getParam('from_year',null);	
		$to_year	= $this->_getParam('to_year',null);	
		$app_init_model = new Default_Model_Appraisalinit();
		$app_init_model->check_for_appraisal($businessunit_id,$department_id,$from_year,$to_year);
	}*/
	public function validateconfigAction($from_year=0,$to_year=0,$bunit=0,$dept_id=0,$dept_flag=0,$mode=0,$call_flag=0,$is_edit=0,$appraisal_id=0,$existing_period='')
    {
		//if ajax call(to year on change)
		if($call_flag == 0)
		{
			$from_year = $this->_getParam('from_year',null);
			$to_year = $this->_getParam('to_year',null);
			$bunit = $this->_getParam('bunit',null);
			$dept_id = $this->_getParam('dept_id',null);
			$dept_flag = $this->_getParam('flag',null);
			$mode = $this->_getParam('mode',null);
			$is_edit = $this->_getParam('is_edit',null);
			$appraisal_id = $this->_getParam('appraisal_id',null);
			$existing_period = $this->_getParam('existing_period',null);
		}
		$dept = (isset($dept_flag) && $dept_flag==0)?$dept_id:0;		
		$app_init_model = new Default_Model_Appraisalinit();	
		$exist_appraisal = $app_init_model->checkappadmin($bunit,$dept_id);
		$json_status = 'fail';
		$json_msg_str = '';
		$json_app_mode = '';
		$json_app_period = 0;
		$json_app_disp_period = '';
		$json_is_exist = 0;
		$json_rating_type = '';
		$existing_app_flag = $app_init_model->getexistingperformanceappflag($bunit,$from_year,$to_year);
		$app_flag_diff = 0;
		if(isset($existing_app_flag) && ($existing_app_flag != $dept_flag))	
		{
			$app_flag_diff++;
		}
		if($is_edit == 1)
		{
			//get the data for the appraisal based on id to compare from year, to year and period
			$appraisal_data = $app_init_model->getConfigData($appraisal_id);
			$appr_from_year = isset($appraisal_data[0]['from_year'])?$appraisal_data[0]['from_year']:0;
			$appr_to_year = isset($appraisal_data[0]['to_year'])?$appraisal_data[0]['to_year']:0;
			$appr_mode = isset($appraisal_data[0]['appraisal_mode'])?$appraisal_data[0]['appraisal_mode']:'';
			$appr_period = isset($appraisal_data[0]['appraisal_period'])?$appraisal_data[0]['appraisal_period']:0;
			$appraisal_ratings = isset($appraisal_data[0]['appraisal_ratings'])?$appraisal_data[0]['appraisal_ratings']:0;
			$actual_appr_period = $this->period_helper($appr_mode, $appr_period);
			if(($from_year == $appr_from_year) && ($to_year == $appr_to_year) && ($existing_period == $actual_appr_period))
			{
				if($call_flag == 0)
				{
					$this->_helper->json(array('status' =>'success','msg_str'=>$json_msg_str,'app_mode'=>$appr_mode,'app_period'=>$appr_period,'app_disp_period'=>$actual_appr_period,'is_exist'=>1,'rating_type'=>$appraisal_ratings));
					return;
				}
				else
				{
					return 'success';
				}
			}			
		}
		if((!$exist_appraisal || $is_edit == 1) && $app_flag_diff == 0)
		{
			//get the existing appraisal data using business unit
			$appraisal_data = array();
			$json_status = 'success';
			// $json_mode = '';
			$appraisal_data = $app_init_model->getAppraisalPeriodOnBuDept($bunit,$dept,$from_year,$to_year,'AND');
			$year_diff_exist = 0;
			if(empty($appraisal_data))
			{
				$appraisal_data_diff = array();
				//calculate the difference between toyear and from year
				// if(is_numeric($from_year) && is_numeric($to_year))
				// {
					$year_diff = $to_year - $from_year;
				// }		
				//if year difference is 0
				if($year_diff == 0)
				{
					//check with from year or to year
					$appraisal_data_diff = $app_init_model->getAppraisalPeriodOnBuDept($bunit,$dept,$from_year,$to_year,'OR');
					if(!empty($appraisal_data_diff))
					{
						$year_diff_exist++;
					}
				}
				else if($year_diff == 1)//if year difference is 1
				{
					//check with from year or (to year - 1)
					$to_in_year = $to_year-1;
					$appraisal_data_diff = $app_init_model->getAppraisalPeriodOnBuDept($bunit,$dept,$from_year,$to_in_year,'AND');
					if(!empty($appraisal_data_diff))
					{
						$year_diff_exist++;
					}
				}
				// if($year_diff_exist == 0 && $dept_flag == 0)
				// {
					// $appraisal_data = $app_init_model->getAppraisalPeriodOnBuDept($bunit,$dept,$from_year,$to_year,'AND',1);
				// }
			}
			$exist_flag = 0;
			$exist_str = '';
			if(!$year_diff_exist)
			{
				if(!empty($appraisal_data))
				{
					foreach($appraisal_data as $app_data)
					{
						$appraisal_mode = !empty($app_data['appraisal_mode'])?$app_data['appraisal_mode']:'';
						$appraisal_period = !empty($app_data['appraisal_period'])?$app_data['appraisal_period']:0;
						// $appraisal_id = !empty($app_data['id'])?$app_data['id']:0;
						switch($appraisal_mode) {
							case 'Quarterly':
								if($appraisal_period <= 4)
								{
									$exist_flag++;
									// $exist_str .= $appraisal_id.',';
								}
								break;
							case 'Half-yearly':
								if($appraisal_period <= 2)
								{
									$exist_flag++;
									// $exist_str .= $appraisal_id.',';
								}
								break;
							case 'Yearly':
								if($appraisal_period <= 1)
								{
									$exist_flag++;
									// $exist_str .= $appraisal_id.',';
								}
								break;
						}
					}
					if($exist_flag > 0)
					{
						// $json_status = 'fail';
						$exist_dept_flag = isset($appraisal_data[0]['performance_app_flag'])?$appraisal_data[0]['performance_app_flag']:'';
						//if existing 
						// $json_app_mode = '';
						// $json_app_period = 0;
						if($dept_flag == $exist_dept_flag)
						{
							$appraisal_mode = isset($appraisal_data[0]['appraisal_mode'])?$appraisal_data[0]['appraisal_mode']:'';
							$appraisal_period = isset($appraisal_data[0]['appraisal_period'])?$appraisal_data[0]['appraisal_period']:0;
							$appraisal_rating_type = isset($appraisal_data[0]['appraisal_ratings'])?$appraisal_data[0]['appraisal_ratings']:'';
							$json_status = 'success';
							switch($appraisal_mode) {
								case 'Quarterly':
									if($appraisal_period <= 4)
									{
										$appraisal_period++;
										if($appraisal_period > 4)
										{
											$json_status = 'fail';
										}
									}
									break;
								case 'Half-yearly':
									if($appraisal_period <= 2)
									{
										$appraisal_period++;
										if($appraisal_period > 2)
										{
											$json_status = 'fail';
										}									
									}
									break;
								case 'Yearly':
									if($appraisal_period <= 1)
									{
										$appraisal_period++;
										if($appraisal_period > 1)
										{
											$json_status = 'fail';
										}
									}
									break;
							}
							if($json_status == 'fail')
							{
								$json_msg_str = 'Appraisal is completed for the selected years.';
							}
							else
							{
								$json_app_period = $appraisal_period;
								$json_app_mode = $appraisal_mode;
								$json_app_disp_period = $this->period_helper($json_app_mode,$json_app_period);
								$json_rating_type = $appraisal_rating_type;
							}
							$json_is_exist = 1;
						}
						else
						{
							$json_status = 'fail';
							$json_msg_str = 'Appraisal is already configured '.(($exist_dept_flag==1)?'business unit':'department').' wise.';
							$json_app_mode = '';
							$json_app_period = 1;
						}
					}
					else
					{
						$json_status = 'success';
						$json_msg_str = '';
						$json_app_mode = '';
						$json_app_period = 1;
					}
				}
				else
				{
					$json_status = 'success';
					$json_msg_str = '';
					$json_app_mode = '';
					$json_app_period = 1;
				}
			}
			else
			{
				$json_status = 'fail';
				$json_msg_str = 'Please select valid year range.';
				$json_app_mode = '';
				$json_app_period = 0;
			}			
		}
		else
		{
			$json_msg_str = 'Appraisal already exist.';
		}
		// echo 'status '.$json_status.' msg_str '.$json_msg_str.' app_mode '.$json_app_mode.' app_period '.$json_app_period.' is_exist '.$json_is_exist;
		// die();
		if($call_flag == 0)
		{
			$this->_helper->json(array('status' =>$json_status,'msg_str'=>$json_msg_str,'app_mode'=>$json_app_mode,'app_period'=>$json_app_period,'app_disp_period'=>$json_app_disp_period,'is_exist'=>$json_is_exist,'rating_type'=>$json_rating_type));
		}
		else
		{
			return $json_status;
		}
	}	
}

