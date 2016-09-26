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

class Default_ServicerequestsController extends Zend_Controller_Action
{
    private $_options;
    public function preDispatch()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('getrequests', 'json')->initContext();
        $ajaxContext->addActionContext('uploadsave', 'json')->initContext();
        $ajaxContext->addActionContext('checkrequeststatus', 'json')->initContext();
        $ajaxContext->addActionContext('getuserassets', 'html')->initContext();
    }

    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
    }

    public function indexAction()
    {
    	$ser_req_model = new Default_Model_Servicerequests();
        $call = $this->_getParam('call');
        if($call == 'ajaxcall')
        $this->_helper->layout->disableLayout();
        $refresh = $this->_getParam('refresh');
        $grid_type = $this->_getParam('t')?$this->_getParam('t'):$this->_getParam('t', sapp_Global::_encrypt('1'));
        $status_value = $this->_getParam('v',null);
        $dashboardcall = $this->_getParam('dashboardcall');
        $data = array();
        $searchQuery = '';
        $searchArray = array();
        if($refresh == 'refresh')
        { 
        	if($dashboardcall == 'Yes')
            $perPage = DASHBOARD_PERPAGE;
            else
                $perPage = PERPAGE;
            $sort = 'DESC';$by = 'modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
        }
        else
        {
            $sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
            $by = ($this->_getParam('by')!='')? $this->_getParam('by'):'modifieddate';
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
     
        $dataTmp = $ser_req_model->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall,$grid_type,$status_value,'','');

        array_push($data,$dataTmp);
        $this->view->dataArray = $data;
        $this->view->call = $call ;
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
    }//end of index action

    public function addAction()
    {
    	
        $request_form = new Default_Form_Servicerequest();
        $msgarray = array();
        $sd_req_model = new Default_Model_Servicerequests();
        $configModel = new Default_Model_Servicedeskconf();
        $auth = Zend_Auth::getInstance();
        $grid_type = $this->_getParam('t',null);
        $status_value = $this->_getParam('v',null);
        
        $this->view->x_grid_type = $grid_type;
        $this->view->x_status_value = $status_value;
        
        if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;            
            $login_bu = $auth->getStorage()->read()->businessunit_id;
            $login_dept = $auth->getStorage()->read()->department_id;
            $reporting_manager = $auth->getStorage()->read()->reporting_manager;  
            $org_head_flag = $auth->getStorage()->read()->is_orghead;
        }
        
        if($org_head_flag == 1)
        {
            $this->view->ermsg = 'norecord';
            //$this->render('form');
        }
        if($grid_type != '')
        {   
       
            if($login_bu == 0)
                $service_desk_flag = 0;
            else 
            {
                $bu_model = new Default_Model_Businessunits();
                $bu_data = $bu_model->getSingleUnitData($login_bu);
                $service_desk_flag = $bu_data['service_desk_flag'];            
            }
            
            $service_types_data = $sd_req_model->getServiceTypes($login_bu,$login_dept,$service_desk_flag);
            $grid_type = sapp_Global::_decrypt($grid_type);
            $grid_type_arr = $sd_req_model->getGridtypearr();
         
        if(empty($service_types_data))
		{
			
			$msgarray['service_desk_conf_id'] = 'Categories are not configured yet.';
			
		}
        $user_allocated_cat=$sd_req_model->getuserallocatedAssetData($loginUserId); 
        if(empty($user_allocated_cat))
		{
			
			$msgarray['asset_id'] = 'Asset Category not configured.';
			
		}
           
            if($login_bu == 0 && $login_dept == 0)
            {
                $msgarray['service_desk_conf_id'] = "You are not assigned to any department, please contact HR.";
            }
            
            $this->view->msgarray = $msgarray; 
            $this->view->form = $request_form;
            $this->view->service_types_data = $service_types_data;
            $this->view->action_name = $this->getRequest()->getActionName();
            $this->view->grid_type = $grid_type_arr[$grid_type];
            $this->view->grid_type_arr = $sd_req_model->getGridtypearr_rev();
            $this->view->status_value = $status_value;
            
            if($this->getRequest()->getPost())
            {
            	 
            	$request_for = $this->_getParam('request_for',null);
                $asset_id = $this->_getParam('asset_id',null);
                $service_desk_id = $this->_getParam('service_desk_id',null);
            	$service_desk_conf_id = $this->_getParam('service_desk_conf_id',null);
            	$service_request_id = $this->_getParam('service_request_id',null);
                
                 
                if(!empty($asset_id) && $request_for=='2'){ 
                	
                	list($service_desk_id, $service_request_id) = explode("_", $asset_id);
                	$_POST['service_desk_id'] = $service_desk_id;
            		$_POST['service_request_id'] = $service_request_id;
            		$conf_id = $configModel->getConfigIdForAssetCategory($service_request_id,$login_bu);
            		if(!empty($conf_id)) {
	            		$service_desk_conf_id = $conf_id[0]['id'];
	            		$_POST['service_desk_conf_id'] = $service_desk_conf_id;
            		}
                   
            	}
            	
            	if($request_form->isValid($this->_request->getPost()))
                {      
                   $priority = $this->_getParam('priority',null);
                    $description = $this->_getParam('description',null);
                    $attachment = $this->_getParam('attachment',null);
               
                    $check_raiser = $sd_req_model->check_raiser($service_desk_conf_id,$loginUserId);

                    if($check_raiser == 'yes')
                    {
                        $file_original_names = $this->_getParam('file_original_names',null);
                        $file_new_names = $this->_getParam('file_new_names',null);

                        $org_names = explode(',', $file_original_names);
                        $new_names = explode(',', $file_new_names);
                        $attachment_array = array();

                        for ($i=0; $i < count($org_names); $i++)
                        {
                            if($new_names[$i] != '')
                                $attachment_array[$org_names[$i]] = $new_names[$i];
                        }

                        $data = array(
                                    'service_desk_id' => $service_desk_id,
                                    'service_desk_conf_id' => $service_desk_conf_id,
                        	        'request_for' =>$request_for,
                        	      	'service_request_id' => $service_request_id,
                                    'priority' => $priority,
                                    'description' => $description,
                                    'attachment' => count($attachment_array) > 0?json_encode($attachment_array):null,
                                    'status' => 'Open',
                                    'raised_by' => $loginUserId,
                                    'isactive' => 1,
                                    'createdby' => $loginUserId,
                                    'modifiedby' => $loginUserId,
                                    'createddate'=>gmdate("Y-m-d H:i:s"),
                                    'modifieddate'=>gmdate("Y-m-d H:i:s")
                        );
                   
                        $trDb = Zend_Db_Table::getDefaultAdapter();		
                        // starting transaction
                        $trDb->beginTransaction();
                        try
                        {                   
                                $id = $sd_req_model->SaveorUpdateRequestData($data, '');
                            $data = array(
                                'modifiedby' => $loginUserId,
                                'modifieddate'=> gmdate("Y-m-d H:i:s"),
                                'ticket_number' => "SD".str_pad($id,4,'0',STR_PAD_LEFT),
                            );
                            
                            $sd_req_model->SaveorUpdateRequestData($data, 'id = '.$id);

                            if(count($new_names) > 0)
                            {
                                foreach ($new_names as $n)
                                {
                                    if($n != '')
                                    {
                                        if(file_exists(SD_TEMP_UPLOAD_PATH.$n))
                                        {
                                            copy(SD_TEMP_UPLOAD_PATH.$n, SD_UPLOAD_PATH.$n);
                                            unlink(SD_TEMP_UPLOAD_PATH.$n);
                                        }
                                    }
                                }
                            }                                       
                            $result = sapp_Global::logManager(SERVICEDESK,1,$loginUserId,$id);  
                            $this->send_req_mails($id);
                            $trDb->commit();
                            $this->_helper->getHelper("FlashMessenger")->addMessage(array(array("success"=>"Request raised successfully.")));
                            $this->_redirect('servicerequests/index/t/'.sapp_Global::_encrypt('1'));                    
                        } 
                        catch (Exception $ex) 
                        {
                        	
                            $trDb->rollBack();
                            $msgarray['service_desk_conf_id'] = "Something went wrong, please try again.";
                            $this->view->msgarray = $msgarray;
                        }
                    }
                    else 
                    {
    	
                    	
                    	$service_request_model = new Default_Model_Servicerequests();
                    	if($request_for==1){

                        $msgarray['service_desk_conf_id'] = "You cannot raise the request as you are the request receiver.Please contact your HR to add one more executor.";

                    	}
                    	else
                    	{
                    	 $msgarray['asset_id'] = "You cannot raise the request as you are the request receiver.Please contact your HR to add one more executor.";
                    	}

                    	
                    	if(isset($request_for) && $request_for!=''){
                    		if($request_for==2) {
                    			
                    			$user_allocate_cat=$service_request_model->getuserallocatedAssetData($loginUserId);
                    			foreach($user_allocate_cat as $userdata)
                    			{
                    	
                    				$request_form->asset_id->addMultiOption($userdata['id'].'_'.$userdata['category'],utf8_encode($userdata['name']));
                    			}
                    		}
                    	}

                        $file_original_names = $this->_getParam('file_original_names',null);
                    	$file_new_names = $this->_getParam('file_new_names',null);
                        $show_attachment = $this->_getParam('show_attachment',null);
                        
                        $this->view->msgarray = $msgarray;
                        $this->view->file_original_names = $file_original_names;
                    	$this->view->file_new_names = $file_new_names;
                    	$this->view->show_attachment = $show_attachment;
                    }
                }
                else
                { 
                	
                	$service_request_model = new Default_Model_Servicerequests();
                	$user_allocated_cat=array();
                	if(isset($request_for) && $request_for!=''){
 						if($request_for==2) {
		                	$user_allocate_cat=$service_request_model->getuserallocatedAssetData($loginUserId);
		                     foreach($user_allocate_cat as $userdata)
							{
								
									$request_form->asset_id->addMultiOption($userdata['id'].'_'.$userdata['category'],utf8_encode($userdata['name']));
							}
						}
                	}
                
                    $file_original_names = $this->_getParam('file_original_names',null);
                    $file_new_names = $this->_getParam('file_new_names',null);
                                    $show_attachment = $this->_getParam('show_attachment',null);

                    $messages = $request_form->getMessages();
                    foreach ($messages as $key => $val)
                    {
                        foreach($val as $key2 => $val2)
                        {
                            $msgarray[$key] = $val2;
                            break;
                        }
                    }

                    $this->view->msgarray = $msgarray;
                    $this->view->file_original_names = $file_original_names;
                    $this->view->file_new_names = $file_new_names;
                    $this->view->show_attachment = $show_attachment;
                }   
                if(count($msgarray) > 0)
                {
                    $sd_dept_model = new Default_Model_Servicedeskdepartment();
                    $data = $sd_dept_model->getRequestsById($this->_getParam('service_desk_id'));
                  $ser_req_options = array('' => 'Select request');
                 if(count($data) > 0)
                    {
                        foreach($data as $opt)
                        {
                            $ser_req_options[$opt['id']] = $opt['service_request_name'];                        
                        }
                    }
                    $request_form->service_request_id->addMultiOptions($ser_req_options);
                }
            }            
        }
        else
        {
            $this->view->ermsg = 'norecord';
        }
        $this->render('form');
    }// end of add action
    
    /**
     * This action is used to send mails to all members involved in a service desk request.
     * @param integer $id = id of the service request.
     */
    public function send_req_mails($id)//modified
    { 
        $sd_req_model = new Default_Model_Servicerequests();
        $summary_data = $sd_req_model->getDataSummary($id);
        $cc_req_ids = $sd_req_model->getCC_mails($summary_data['service_desk_conf_id']);
        $app_ids_arr = array();
        $auth = Zend_Auth::getInstance();
                    
        if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;            
        }
                    
        if($summary_data['status'] == 'Open')
            $total_ids = array($cc_req_ids['request_recievers'],$cc_req_ids['cc_mail_recievers'] ,$summary_data['createdby'],$summary_data['reporting_manager_id']);
        else if($summary_data['status'] == 'Closed' || $summary_data['status'] == 'Rejected')
            $total_ids = array($summary_data['executor_id'],$cc_req_ids['cc_mail_recievers'],$summary_data['createdby'],
                               $summary_data['reporting_manager_id'],$summary_data['approver_1'],$summary_data['approver_2'],
                               $summary_data['approver_3']);
        else if($summary_data['status'] == 'To manager approve')
        {            
            $total_ids = array($summary_data['modifiedby'],$cc_req_ids['cc_mail_recievers'],$summary_data['reporting_manager_id']);
        }
        else if($summary_data['status'] == 'Manager approved'  || $summary_data['status'] == 'Manager rejected')
        {
            $total_ids = array($cc_req_ids['request_recievers'],$cc_req_ids['cc_mail_recievers'],$summary_data['reporting_manager_id']);
        }
        else if($summary_data['status'] == 'To management approve')
        {
            $app_id = "";
            $approver_name = "";
            for($i=1;$i<=3;$i++)
            {
                if($summary_data['approver_status_'.$i] == '')
                {
                    $app_id = $summary_data['approver_'.$i];
                    $approver_name = $summary_data['approver_'.$i.'_name'];
                    break;
                }
            }
            $total_ids = array($summary_data['modifiedby'],$cc_req_ids['cc_mail_recievers'],$app_id);
        }
        else if($summary_data['status'] == 'Management approved' || $summary_data['status'] == 'Management rejected')
        {
            
            $approver_name = "";
            for($i=1;$i<=3;$i++)
            {
                if($summary_data['approver_'.$i] != '')
                {
                    $app_ids_arr[] = $summary_data['approver_'.$i];
                    if($summary_data['approver_'.$i] == $summary_data['modifiedby'])
                        $approver_name = $summary_data['approver_'.$i.'_name'];
                }
            }
            $app_ids = implode(',', $app_ids_arr);
            $total_ids = array($cc_req_ids['cc_mail_recievers'],$app_ids,$cc_req_ids['request_recievers']);
        }
        
        $total_ids = array_filter($total_ids);
        $total_ids = implode(',', $total_ids);
        $email_ids = $sd_req_model->getEmailIds($total_ids);
        $req_receivers = explode(',',$cc_req_ids['request_recievers']);
        $cc = array();
        if($cc_req_ids['cc_mail_recievers'] != '')
        {
            $cc = explode(',',$cc_req_ids['cc_mail_recievers']);
        }
                
        //start of mailing        
        if($summary_data['status'] == 'Open')
        {
            $message = "A new request has been raised by you.";
            $this->mail_helper($message, $summary_data, isset($email_ids[$summary_data['createdby']])?$email_ids[$summary_data['createdby']]:"");
            if(count($req_receivers) > 0)
            {
                $message = "A new request has been raised for your perusal.";
                foreach($req_receivers as $rec)
                {                    
                    $this->mail_helper($message, $summary_data, isset($email_ids[$rec])?$email_ids[$rec]:"");
                }
            }
            $message = "A new request has been raised by ".$summary_data['raised_by_name'];
            $this->mail_helper($message, $summary_data, isset($email_ids[$summary_data['reporting_manager_id']])?$email_ids[$summary_data['reporting_manager_id']]:"");
            
            if(count($cc) > 0)
            {
                foreach($cc as $rec)
                {
                    $this->mail_helper($message, $summary_data, isset($email_ids[$rec])?$email_ids[$rec]:"");
                }
            }
        }
        else if($summary_data['status'] == 'Closed' || $summary_data['status'] == 'Rejected')
        {
            $message = "An action has been performed on your request. Please find the details below.";
            $this->mail_helper($message, $summary_data, isset($email_ids[$summary_data['createdby']])?$email_ids[$summary_data['createdby']]:"");
            if(count($req_receivers) > 0)
            {
                
                foreach($req_receivers as $rec)
                {                    
                    $message = "An action has been performed by ".(($rec == $summary_data['executor_id'])?"you":$summary_data['executor_name'])." on the request. Please find the details below.";
                    $this->mail_helper($message, $summary_data, isset($email_ids[$rec])?$email_ids[$rec]:"");
                }
            }
            $message = "The below request has been ".(strtolower($summary_data['status']))." by ".$summary_data['executor_name'];
            $this->mail_helper($message, $summary_data, isset($email_ids[$summary_data['reporting_manager_id']])?$email_ids[$summary_data['reporting_manager_id']]:"");
            
            if(count($cc) > 0)
            {
                foreach($cc as $rec)
                {
                    $this->mail_helper($message, $summary_data, isset($email_ids[$rec])?$email_ids[$rec]:"");
                }
            }
            for($i = 1; $i<= 3; $i++)
            {
                if($summary_data['approver_'.$i] != '')
                {
                    $this->mail_helper($message, $summary_data, isset($email_ids[$summary_data['approver_'.$i]])?$email_ids[$summary_data['approver_'.$i]]:"");
                }
            }
        }
        else if($summary_data['status'] == 'Management approved' || $summary_data['status'] == 'Management rejected')
        {
            $approver_status = $summary_data['status'] == 'Management approved'?"approved":"rejected";
            $message = "A ".$summary_data['service_desk_name']." request has been ".$approver_status." by you.";
            $this->mail_helper($message, $summary_data, isset($email_ids[$summary_data['modifiedby']])?$email_ids[$summary_data['modifiedby']]:"");

            if(count($req_receivers) > 0)
            {
                $message = "A ".$summary_data['service_desk_name']." request has been ".$approver_status." by ".$approver_name.".";
                foreach($req_receivers as $rec)
                {                    
                    $this->mail_helper($message, $summary_data, isset($email_ids[$rec])?$email_ids[$rec]:"");
                }
            }
            if(count($cc) > 0)
            {
                $message = "A ".$summary_data['service_desk_name']." request has been ".$approver_status." by ".$approver_name.".";
                foreach($cc as $rec)
                {
                    $this->mail_helper($message, $summary_data, isset($email_ids[$rec])?$email_ids[$rec]:"");
                }
            }
            if(count($app_ids_arr) > 0)
            {
                $message = "A ".$summary_data['service_desk_name']." request has been ".$approver_status." by ".$approver_name.".";
                foreach ($app_ids_arr as $ap_id)
                {
                    if($ap_id != $summary_data['modifiedby'])
                        $this->mail_helper($message, $summary_data, isset($email_ids[$ap_id])?$email_ids[$ap_id]:"");
                }
            }
        }
        else if($summary_data['status'] == 'Manager approved' || $summary_data['status'] == 'Manager rejected')
        {
            $manager_status = $summary_data['reporting_manager_status'] == 'Approve'?"approved":"rejected";
            $message = "A ".$summary_data['service_desk_name']." request has been ".$manager_status." by you.";
            $this->mail_helper($message, $summary_data, isset($email_ids[$summary_data['reporting_manager_id']])?$email_ids[$summary_data['reporting_manager_id']]:"");

            if(count($req_receivers) > 0)
            {
                $message = "A ".$summary_data['service_desk_name']." request has been ".$manager_status." by ".$summary_data['reporting_manager_name'].".";
                foreach($req_receivers as $rec)
                {                    
                    $this->mail_helper($message, $summary_data, isset($email_ids[$rec])?$email_ids[$rec]:"");
                }
            }
            if(count($cc) > 0)
            {
                $message = "A ".$summary_data['service_desk_name']." request has been ".$manager_status." by ".$summary_data['reporting_manager_name'].".";
                foreach($cc as $rec)
                {
                    $this->mail_helper($message, $summary_data, isset($email_ids[$rec])?$email_ids[$rec]:"");
                }
            } 
        }
        else if($summary_data['status'] == 'To manager approve')
        {            
            $message = "A ".$summary_data['service_desk_name']." request has been sent by you for ".$summary_data['reporting_manager_name']."’s approval.";
            $this->mail_helper($message, $summary_data, isset($email_ids[$summary_data['modifiedby']])?$email_ids[$summary_data['modifiedby']]:"");
            $message = "A new request has been sent to you for approval.";
            $this->mail_helper($message, $summary_data, isset($email_ids[$summary_data['reporting_manager_id']])?$email_ids[$summary_data['reporting_manager_id']]:"");
                
            $message = ucfirst($auth->getStorage()->read()->userfullname)." sent the below request for ".$summary_data['reporting_manager_name']."’s approval.";
            if(count($cc) > 0)
            {
                foreach($cc as $rec)
                {
                    $this->mail_helper($message, $summary_data, isset($email_ids[$rec])?$email_ids[$rec]:"");
                }
            }            
        }
        else if($summary_data['status'] == 'To management approve')
        {
            $message = "A ".$summary_data['service_desk_name']." request has been sent by you for ".$approver_name."’s approval.";
            $this->mail_helper($message, $summary_data, isset($email_ids[$summary_data['modifiedby']])?$email_ids[$summary_data['modifiedby']]:"");
            $message = "A new request has been sent to you for approval.";
            $this->mail_helper($message, $summary_data, isset($email_ids[$app_id])?$email_ids[$app_id]:"");
                
            $message = ucfirst($auth->getStorage()->read()->userfullname)." sent the below request for ".$approver_name."’s approval.";
            if(count($cc) > 0)
            {
                foreach($cc as $rec)
                {
                    $this->mail_helper($message, $summary_data, isset($email_ids[$rec])?$email_ids[$rec]:"");
                }
            }
        }
        //end of mailing
    }
    public function mail_helper($message,$summary_data,$to_email)
    {
        $status = $summary_data['status'];
        $disp_arr = array('Closed' => 'Closed','Rejected' => 'Rejected');
        $display_status = (!array_key_exists($status,$disp_arr))?"Open":$disp_arr[$status];
        $view = $this->getHelper('ViewRenderer')->view;
        $base_url = 'http://'.$this->getRequest()->getHttpHost() . $this->getRequest()->getBaseUrl();
        $this->view->base_url=$base_url;
        $this->view->message = $message;                
        $this->view->data = $summary_data;
        $text = $view->render('mailtemplates/sd_request.phtml');
        
        $options['subject'] = "New online request: Status - ".$display_status;
        $options['header'] = 'New online request';
        $options['toEmail'] = $to_email;
        $options['toName'] = '';
        $options['message'] = $text;

        $options['cron'] = 'yes';
        if($options['toEmail'] != '')
        {                    
            sapp_Global::_sendEmail($options);
        }
    }
    public function send_req_mails1($id)//original
    {        
        $sd_req_model = new Default_Model_Servicerequests();
        $summary_data = $sd_req_model->getDataSummary($id);
        
        $cc_req_ids = $sd_req_model->getCC_mails($summary_data['service_desk_conf_id']);
        
        if($summary_data['status'] == 'Open')
            $total_ids = array($cc_req_ids['request_recievers'],$cc_req_ids['cc_mail_recievers'] ,$summary_data['createdby']);
        else if($summary_data['status'] == 'Closed')
            $total_ids = array($summary_data['executor_id'],$cc_req_ids['cc_mail_recievers'],$summary_data['createdby']);
        else if($summary_data['status'] == 'To manager approve')
        {
            $total_ids = array($summary_data['executor_id'],$cc_req_ids['cc_mail_recievers'],$summary_data['reporting_manager_id']);
        }
        else if(in_array($summary_data['status'] , array( 'To management approve','Management approved','Management rejected') ) )
        {
            $total_ids = array($summary_data['executor_id'],$cc_req_ids['cc_mail_recievers'],$summary_data['approver_1'],$summary_data['approver_2'],$summary_data['approver_3']);
        }
        else if($summary_data['status'] == 'Manager approved' || $summary_data['status'] == 'Manager rejected')
        {
            $total_ids = array($summary_data['executor_id'],$cc_req_ids['cc_mail_recievers'],$summary_data['reporting_manager_id']);
        }
        
        $total_ids = array_filter($total_ids);
        $total_ids = implode(',', $total_ids);
        $email_ids = $sd_req_model->getEmailIds($total_ids);
        $req_receivers = explode(',',$cc_req_ids['request_recievers']);
        $cc = array();
        if($cc_req_ids['cc_mail_recievers'] != '')
        {
            $cc = explode(',',$cc_req_ids['cc_mail_recievers']);
        }
        $req_receivers[] = $summary_data['createdby'];
        
        $status = $summary_data['status'];
        $disp_arr = array('Closed' => 'Closed','Rejected' => 'Closed');
        $display_status = (!in_array($status,$disp_arr))?"Open":"Closed";
        if(count($req_receivers) > 0)
        {
            foreach($req_receivers as $rec)
            {
                $base_url = 'http://'.$this->getRequest()->getHttpHost() . $this->getRequest()->getBaseUrl();
                $view = $this->getHelper('ViewRenderer')->view;
                
                $this->view->base_url=$base_url;
                $this->view->type = "receivers";                
                $this->view->data = $summary_data;
                $text = $view->render('mailtemplates/sdrequest.phtml');
                $options['subject'] = "New Online request: Status - ".$display_status;

                $options['header'] = 'Online request';
                $options['toEmail'] = isset($email_ids[$rec])?$email_ids[$rec]:"";
                $options['toName'] = '';
                $options['message'] = $text;
                
                $options['cron'] = 'yes';
                if($options['toEmail'] != '')
                {                    
                    sapp_Global::_sendEmail($options);
                }
                
            }
        }
        if(count($cc) > 0)
        {
            foreach($cc as $rec)
            {
                $base_url = 'http://'.$this->getRequest()->getHttpHost() . $this->getRequest()->getBaseUrl();
                $view = $this->getHelper('ViewRenderer')->view;
                
                $this->view->base_url=$base_url;
                $this->view->type = "receivers";                
                $this->view->data = $summary_data;
                $text = $view->render('mailtemplates/sdrequest.phtml');
                $options['subject'] = "New Online request: Status - Open";

                $options['header'] = 'Online request';
                $options['toEmail'] = $email_ids[$rec];
                $options['toName'] = '';
                $options['message'] = $text;
                
                $options['cron'] = 'yes';
                if($options['toEmail'] != '')
                {                    
                    sapp_Global::_sendEmail($options);
                }
            }
        }                                
    }
        
    public function uploadsaveAction() 
    {		
        $user_id = sapp_Global::_readSession('id');
        $filedata = array();
        if(isset($_FILES["myfile"]))
        {
            $fileName = $_FILES["myfile"]["name"];			  	
            $newName  = time().'_'.$user_id.'_'.str_replace(' ', '_', $_FILES["myfile"]["name"]);

            move_uploaded_file($_FILES["myfile"]["tmp_name"],SD_TEMP_UPLOAD_PATH.$newName);

            $filedata['original_name'] = $fileName;
            $filedata['new_name'] = $newName;
            $this->_helper->json(array('filedata' => $filedata));
        }
    }

    public function uploaddeleteAction()
    {		
        if(isset($_POST["op"]) && $_POST["op"] == "delete" && isset($_POST['doc_new_name']))
        {
            $filePath = SD_TEMP_UPLOAD_PATH.$_POST['doc_new_name'];
            if (file_exists($filePath)) 
            {
                unlink($filePath);
                $this->_helper->json(array());
            }
        }
    }
    
    public function viewAction()
    {
        $request_form = new Default_Form_Servicerequest();
        $id = $this->_getParam('id',null);
        $grid_type = $this->_getParam('t',null);
        $status_value = $this->_getParam('v',null);
        $req_msg = "";
        $app_names=array();
        try
        {
            if($id != '' && $grid_type != '')
            {
                $grid_type = sapp_Global::_decrypt($grid_type);
                $sd_req_model = new Default_Model_Servicerequests();
                $grid_type_arr = $sd_req_model->getGridtypearr();
                
                if(is_numeric($id) && $id>0 && is_numeric($grid_type) && $grid_type > 0 && array_key_exists($grid_type,$grid_type_arr))
                {                                        
                    $auth = Zend_Auth::getInstance();
                    
                    if($auth->hasIdentity())
                    {
                        $loginUserId = $auth->getStorage()->read()->id;
                        $loginuserRole = $auth->getStorage()->read()->emprole;
                        $loginuserGroup = $auth->getStorage()->read()->group_id;            
                        $login_bu = $auth->getStorage()->read()->businessunit_id;
                        $login_dept = $auth->getStorage()->read()->department_id;
                    }	
                    $data = $sd_req_model->getRequestById($id);
                    if(!empty($data))
                    {  
                        $approver_level = "view";
                        if(($grid_type_arr[$grid_type] == 'rept_app' || $grid_type_arr[$grid_type] == 'approver') && $data['status'] == 'To management approve')
                        {
                            $approver_level = $sd_req_model->getApproverLevel($loginUserId,$id);
                        }
                        $request_history = $sd_req_model->getRequestHistory($id);
                        $emp_model = new Default_Model_Employee();
                        $assetCategoryModel = new Assets_Model_AssetCategories();
                        $service_conf_model = new Default_Model_Servicedeskconf();
                        $raised_by_details = $emp_model->getEmp_from_summary($data['raised_by']);
                        $app_data = $sd_req_model->getApprovers($data['service_desk_conf_id'], "config");                        
                        $app_details = $emp_model->getEmployeeDetails(implode(',', $app_data));
                        
                        foreach($app_data as $key => $value)
                        {
                            $app_names[$key] = $app_details[$value];
                        }
                                                
                        $conf_data = $service_conf_model->getServiceDeskConfbyID($data['service_desk_conf_id']);                        
                        $exec_data = $emp_model->getEmployeeDetails($conf_data[0]['request_recievers']);
                        if($data['priority'] == 1) {
                    	$data['priority']='Low';
                    }else if($data['priority'] == 2) {
                    	$data['priority']='Medium';
                    }else {
                    $data['priority']='High';
                    }	
                    
                    if(isset($data['request_for'])){
                    	if($data['request_for']=='1'){
                    		$data['request_for']="Service";
                    	}else{
                    		$data['request_for']="Asset";
                    	}
                    }
                  
                        $raised_date = sapp_Global::change_date($data['createddate'],'view');
                        $this->view->raised_date = $raised_date;
                         $this->view->id = $id;
                        $this->view->data = $data; 
                        $this->view->grid_type = $grid_type_arr[$grid_type];
                        $this->view->grid_type_arr = $sd_req_model->getGridtypearr_rev();
                        $this->view->status_value = $status_value;
                        $this->view->approver_level = $approver_level;
                        $this->view->loginUserId = $loginUserId;
                        $this->view->request_history = $request_history;
                        $this->view->raised_by_details = $raised_by_details;
                        $this->view->app_names = $app_names;
                        $this->view->exec_data = $exec_data;
                        
                        if($this->getRequest()->getPost())
                        {  
                            $trDb = Zend_Db_Table::getDefaultAdapter();		
                            // starting transaction
                            $trDb->beginTransaction();
                            try
                            {                                
                                $req_id = $this->_getParam('hid_reqid',null);
                                $hid_status = $this->_getParam('hid_status',null);

                                if($hid_status != '' && $hid_status == 'Cancelled')
                                {
                                    $save_data = array(
                                        'status' => $hid_status,                                    
                                        'modifiedby' => $loginUserId,
                                        'modifieddate'=> gmdate("Y-m-d H:i:s"),                                    
                                    );
                                    $sd_req_model->SaveorUpdateRequestData($save_data, 'id = '.$id);
                                    //start of saving history
                                    $reqh_model = new Default_Model_Requesthistory();
                                    $reqh_data = array(
                                        'request_id' => $id,
                                        'description' => ucfirst($data['service_desk_name'])." Request has been cancelled by ",
                                        'emp_id' => $loginUserId,
                                        'emp_name' => ucfirst($data['raised_by_name']),
                                        'emp_profileimg' => $auth->getStorage()->read()->profileimg,
                                        'createdby' => $loginUserId,
                                        'modifiedby' => $loginUserId,
                                        'isactive' => 1,
                                        'createddate' =>gmdate("Y-m-d H:i:s"),
                                        'modifieddate'=> gmdate("Y-m-d H:i:s"),
                                    );
                                    $reqh_model->SaveorUpdateRhistory($reqh_data, '');
                                    //end of saving history
                                    
                                    $req_msg['success'] = "Request cancelled successfully.";
                                }
                                else if($hid_status != '' && $hid_status == 'To management approve')
                                {
                                    $hid_app_pos = $this->_getParam('hid_app_pos', null);
                                    $hid_max_app = $this->_getParam('hid_max_app', null);
                                    if($hid_app_pos == '' && $hid_max_app == '')
                                    {
                                        $request_comments = trim($this->_getParam('request_comments',null));
                                        if($request_comments != '')
                                        {
                                            $save_data = array(
                                                'status' => "To management approve",
                                                'to_mgmt_comments' => trim($request_comments),
                                                'modifiedby' => $loginUserId,
                                                'modifieddate'=> gmdate("Y-m-d H:i:s"),                                    
                                            );
                                            $save_data = $save_data + $app_data;
                                            $sd_result = "";
                                            if($data['status'] == 'Open')
                                                $sd_result = $sd_req_model->SaveorUpdateRequestData($save_data, 'status = "Open" and id = '.$id);
                                            
                                            //start of saving history
                                            $reqh_model = new Default_Model_Requesthistory();
                                            $reqh_data = array(
                                                'request_id' => $id,
                                                'description' => ucfirst($data['service_desk_name'])." Request has been sent for management approval by ",
                                                'emp_id' => $loginUserId,
                                                'emp_name' => ucfirst($auth->getStorage()->read()->userfullname),
                                                'emp_profileimg' => $auth->getStorage()->read()->profileimg,
                                                'createdby' => $loginUserId,
                                                'modifiedby' => $loginUserId,
                                                'comments' => trim($request_comments),
                                                'isactive' => 1,
                                                'createddate' =>gmdate("Y-m-d H:i:s"),
                                                'modifieddate'=> gmdate("Y-m-d H:i:s"),
                                            );
                                            if($sd_result == 'update')
                                            {
                                                $reqh_model->SaveorUpdateRhistory($reqh_data, '');
                                                //end of saving history
                                                $this->send_req_mails($id);
                                                $req_msg['success'] = "Request sent for management approval successfully.";
                                            }
                                            else 
                                                $req_msg['error'] = "Some thing went wrong, please try again.";
                                            
                                        }
                                    }
                                    else // approver approving/rejecting the request
                                    {
                                        $request_comments = trim($this->_getParam('request_comments',null));
                                        $request_action = $this->_getParam('request_action',null);
                                        if($request_comments != '' && $request_action != '')
                                        {                                           
                                            $save_data = array(                                    
                                                'approver_status_'.$approver_level['app_pos'] => $request_action,
                                                'modifiedby' => $loginUserId,
                                                'approver_'.$approver_level['app_pos'].'_comments' => trim($request_comments),
                                                'modifieddate'=>gmdate("Y-m-d H:i:s"),                                    
                                            );
                                            if($request_action == 'Reject')
                                            {
                                                $save_data['status'] = 'Management rejected';
                                            }
                                            if($approver_level['app_pos'] == $approver_level['max_app'])
                                            {
                                                $save_data['status'] = $request_action == 'Approve'?"Management approved":"Management rejected";
                                            }
                                
                                            $sd_req_model->SaveorUpdateRequestData($save_data, 'id = '.$id);
                                            //start of saving history
                                            $reqh_model = new Default_Model_Requesthistory();
                                            $reqh_data = array(
                                                'request_id' => $id,
                                                'description' => ucfirst($data['service_desk_name'])." Request has been ".($request_action == 'Approve'?"approved":"rejected")." by ",
                                                'emp_id' => $loginUserId,
                                                'emp_name' => ucfirst($auth->getStorage()->read()->userfullname),
                                                'emp_profileimg' => $auth->getStorage()->read()->profileimg,
                                                'createdby' => $loginUserId,
                                                'modifiedby' => $loginUserId,
                                                'comments' => trim($request_comments),
                                                'isactive' => 1,
                                                'createddate' =>gmdate("Y-m-d H:i:s"),
                                                'modifieddate'=> gmdate("Y-m-d H:i:s"),
                                            );
                                            $reqh_model->SaveorUpdateRhistory($reqh_data, '');
                                            //end of saving history
                                            $this->send_req_mails($id);
                                            $req_msg['success'] = "Request ".($request_action == 'Approve'?"approved":"rejected")." successfully.";
                                        }
                                    }
                                }
                                else if($hid_status != '' && $hid_status == 'To manager approve')
                                {
                                    if($loginUserId == $data['reporting_manager_id'])//for reporting manager
                                    {                                        
                                        $request_action = $this->_getParam('request_action',null);
                                        $request_comments = trim($this->_getParam('request_comments',null));

                                        if($request_action != '' && $request_comments != '')
                                        {                                            
                                            $save_data = array(
                                                'status' => $request_action == 'Approve'?"Manager approved":"Manager rejected",
                                                'reporting_manager_status' => $request_action,
                                                'reporting_manager_comments' => trim($request_comments),
                                                'modifiedby' => $loginUserId,
                                                'modifieddate'=> gmdate("Y-m-d H:i:s"),                                    
                                            );
                                            $sd_req_model->SaveorUpdateRequestData($save_data, 'id = '.$id);
                                            //start of saving history
                                            $reqh_model = new Default_Model_Requesthistory();
                                            $reqh_data = array(
                                                'request_id' => $id,
                                                'description' => ucfirst($data['service_desk_name'])." Request has been ".($request_action == 'Approve'?"approved":"rejected")." by reporting manager ",
                                                'emp_id' => $loginUserId,
                                                'emp_name' => ucfirst($auth->getStorage()->read()->userfullname),
                                                'emp_profileimg' => $auth->getStorage()->read()->profileimg,
                                                'createdby' => $loginUserId,
                                                'modifiedby' => $loginUserId,
                                                'isactive' => 1,
                                                'comments' => trim($request_comments),
                                                'createddate' =>gmdate("Y-m-d H:i:s"),
                                                'modifieddate'=> gmdate("Y-m-d H:i:s"),
                                            );
                                            $reqh_model->SaveorUpdateRhistory($reqh_data, '');
                                            //end of saving history
                                            $this->send_req_mails($id);
                                            $req_msg['success'] = "Request ".($request_action == 'Approve'?"approved":"rejected")." successfully.";
                                        }
                                    }
                                    else //for executor
                                    {
                                        $request_comments = trim($this->_getParam('request_comments',null));
                                        if($request_comments != '')
                                        {                                                                                        
                                            $save_data = array(
                                                'status' => "To manager approve",
                                                'to_manager_comments' => trim($request_comments),
                                                'reporting_manager_id' => $raised_by_details['reporting_manager'],
                                                'modifiedby' => $loginUserId,
                                                'modifieddate'=> gmdate("Y-m-d H:i:s"),                                    
                                            );
                                            $sd_result = "";
                                            if($data['status'] == 'Open')
                                                $sd_result = $sd_req_model->SaveorUpdateRequestData($save_data, 'status = "Open" and id = '.$id);
                                            //start of saving history
                                            $reqh_model = new Default_Model_Requesthistory();
                                            $reqh_data = array(
                                                'request_id' => $id,
                                                'description' => ucfirst($data['service_desk_name'])." Request has been sent for manager approval by ",
                                                'emp_id' => $loginUserId,
                                                'emp_name' => ucfirst($auth->getStorage()->read()->userfullname),
                                                'emp_profileimg' => $auth->getStorage()->read()->profileimg,
                                                'createdby' => $loginUserId,
                                                'modifiedby' => $loginUserId,
                                                'comments' => trim($request_comments),
                                                'isactive' => 1,
                                                'createddate' =>gmdate("Y-m-d H:i:s"),
                                                'modifieddate'=> gmdate("Y-m-d H:i:s"),
                                            );
                                            
                                            if($sd_result == 'update')
                                            {
                                                $reqh_model->SaveorUpdateRhistory($reqh_data, '');
                                                //end of saving history
                                                $this->send_req_mails($id);
                                                $req_msg['success'] = "Request sent for manager approval successfully.";
                                            }                                            
                                            else 
                                                $req_msg['error'] = "Some thing went wrong, please try again.";
                                        }
                                    }
                                }
                                else if($hid_status != '' && $hid_status == 'Closed')
                                {
                                    $request_action = $this->_getParam('request_action',null);
                                    $request_comments = trim($this->_getParam('request_comments',null));
                                    
                                    if($request_action != '' && $request_comments != '')
                                    {
                                        $save_data = array(
                                            'status' => $request_action == 'Approve'?"Closed":"Rejected",
                                            'executor_comments' => trim($request_comments),
                                            'executor_id' => $loginUserId,
                                            'modifiedby' => $loginUserId,
                                            'modifieddate'=> gmdate("Y-m-d H:i:s"),                                    
                                        );
                                        $sd_result = "";
                                        if($data['status'] != 'Closed' && $data['status'] != 'Rejected')
                                            $sd_result = $sd_req_model->SaveorUpdateRequestData($save_data, 'id = '.$id);
                                        //start of saving history
                                        $reqh_model = new Default_Model_Requesthistory();
                                        $reqh_data = array(
                                            'request_id' => $id,
                                            'description' => ucfirst($data['service_desk_name'])." Request has been ".($request_action == 'Approve'?"closed":"rejected")." by ",
                                            'emp_id' => $loginUserId,
                                            'emp_name' => ucfirst($auth->getStorage()->read()->userfullname),
                                            'emp_profileimg' => $auth->getStorage()->read()->profileimg,
                                            'createdby' => $loginUserId,
                                            'modifiedby' => $loginUserId,
                                            'comments' => trim($request_comments),
                                            'isactive' => 1,
                                            'createddate' =>gmdate("Y-m-d H:i:s"),
                                            'modifieddate'=> gmdate("Y-m-d H:i:s"),
                                        );
                                        if($sd_result == 'update')
                                        {
                                            $reqh_model->SaveorUpdateRhistory($reqh_data, '');
                                            //end of saving history
                                            $this->send_req_mails($id);
                                            $req_msg['success'] = "Request ".($request_action == 'Approve'?"closed":"rejected")." successfully.";
                                        }
                                        else 
                                            $req_msg['error'] = "Some thing went wrong, please try again.";
                                    }
                                }
                                                                        
                                $result = sapp_Global::logManager(SERVICEDESK,2,$loginUserId,$id);                    
                                $trDb->commit();
                                $this->_helper->getHelper("FlashMessenger")->addMessage(array($req_msg));
                                $this->_redirect('servicerequests/index/t/'.sapp_Global::_encrypt($grid_type).($status_value !=''?"/v/".$status_value:""));
                            } 
                            catch (Exception $ex) 
                            {
                                $trDb->rollBack();
                                $msgarray['executor_comments'] = "Something went wrong, please try again.";
                            }
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
                $this->view->ermsg = 'norecord';
            } 			
        }
        catch(Exception $e)
        {
            $this->view->ermsg = 'nodata';            
        }
        
        $this->view->action_name = $this->getRequest()->getActionName();        
    }//end of view function.
    public function getrequestsAction()
    {
        $service_desk_id = $this->_getParam('service_desk_id',null);
        $service_desk_conf_id = $this->_getParam('service_desk_conf_id',null);
        $data = array();
        $options = sapp_Global::selectOptionBuilder('', 'Select request', '');
        if($service_desk_id != '')
        {
            $sd_dept_model = new Default_Model_Servicedeskdepartment();
            $data = $sd_dept_model->getRequestsById($service_desk_id);
            if(count($data) > 0)
            {
                foreach($data as $opt)
                {                    
                    $options .= sapp_Global::selectOptionBuilder($opt['id'], utf8_encode($opt['service_request_name']), '');
                }
            }            
        }
        $attachment = false;
        if($service_desk_conf_id != '')
        {
        	
        	$sd_conf_model = new Default_Model_Servicedeskconf();
        	$sd_conf_data = $sd_conf_model->getServiceDeskConfbyID($service_desk_conf_id);
        	if(count($sd_conf_data)>0){
        		if($sd_conf_data[0]['attachment'] == 1)
        			$attachment = true;	
        	}
        }
        $this->_helper->json(array('options' => $options,'datacount'=>count($data),'attachment'=>$attachment));
    }// end of getrequests action
    
    public function changestatusAction()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
        }
        $id = $this->_request->getParam('objid');
        $status = $this->_getParam('status',null);
        $grid_type = $this->_getParam('grid_type',null);
        $messages['message'] = '';
        $messages['msgtype'] = '';
        
        $actionflag = 3;
        if($id != '')
        {
            $sd_req_model = new Default_Model_Servicerequests(); 
            $grid_type_arr = $sd_req_model->getGridtypearr_rev();
            $display_status = array('Cancelled' => 'cancelled','To manager approve' => ' sent for manager approve ',
                'To management approve' => ' sent for approve','Closed' => 'closed');
            
            $data = array('status'=>$status,'modifieddate'=>gmdate("Y-m-d H:i:s"),'modifiedby' => $loginUserId);
            if($status != 'Cancelled')
                $data['executor_id'] = $loginUserId;
            if($status == 'To manager approve')
            {
                $reporting_manager = $sd_req_model->getReptId($id);
                if($reporting_manager != '')
                    $data['reporting_manager_id'] = $reporting_manager;
                if($reporting_manager != "" && ($reporting_manager == $loginUserId))
                {
                    $messages['message'] = 'Service desk request cannot be sent for manager approve.';
                    $messages['msgtype'] = 'error';
                    $messages['flagtype'] = 'dont_redirect';
                    $messages['grid_type'] = sapp_Global::_encrypt($grid_type_arr[$grid_type]);
                    $this->_helper->json($messages);
                }
            }
            else if($status == 'To management approve')
            {
                $approver_arr = $sd_req_model->getApprovers($id,'request');
                $data = $data + $approver_arr; 
                
            }
            
            $where = array('id=?'=>$id);
            $trDb = Zend_Db_Table::getDefaultAdapter();		
		// starting transaction
            $trDb->beginTransaction();
            try
            {
                $Id = $sd_req_model->SaveorUpdateRequestData($data, $where);
                if($Id == 'update')
                {                
                    $result = sapp_Global::logManager(SERVICEDESKREQUEST,$actionflag,$loginUserId,$id);
                    if($status != 'Cancelled')
                        $this->send_req_mails($id);
                    $messages['message'] = 'Service desk request '.$display_status[$status].' successfully.';
                    $messages['msgtype'] = 'success';
                    $messages['flagtype'] = 'sd_request';
                    $messages['grid_type'] = sapp_Global::_encrypt($grid_type_arr[$grid_type]);
                }   
                else
                {
                    $messages['message'] = 'Service desk request cannot be '.$display_status[$status].'.';
                    $messages['msgtype'] = 'error';
                    $messages['flagtype'] = 'sd_request';
                    $messages['grid_type'] = sapp_Global::_encrypt($grid_type_arr[$grid_type]);
                }
                $trDb->commit();
            }
            catch (Exception $e)
            {
                $trDb->rollBack();
                $messages['message'] = 'Service desk request cannot be '.$display_status[$status].'.';
                $messages['msgtype'] = 'error';
                $messages['flagtype'] = 'sd_request';
                $messages['grid_type'] = sapp_Global::_encrypt($grid_type_arr[$grid_type]);
            }
        }
        else
        { 
            $messages['message'] = 'Service desk request cannot be '.$display_status[$status].'.';
            $messages['msgtype'] = 'error';
            $messages['flagtype'] = 'sd_request';
            $messages['grid_type'] = sapp_Global::_encrypt($grid_type_arr[$grid_type]);
        }
        $this->_helper->json($messages);
    }//end of delete action
	
	//function to check the status of a service request
	public function checkrequeststatusAction()
	{
		$request_id = $this->_request->getParam('request_id');
		$request_status = '';
		if(!empty($request_id) && is_numeric($request_id))
		{
			$service_request_model = new Default_Model_Servicerequests(); 
			$request_data = $service_request_model->getRequestById($request_id);
			$request_status = $request_data['status'];
		}
		if($request_status != 'Open')
		{
		    $this->_helper->getHelper("FlashMessenger")->addMessage(array(array("error"=>"You cannot cancel the request as the current status is ".$request_status.".")));
		}
		$this->_helper->json($request_status);
	}
	public function getuserassetsAction(){
	
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$userid=$loginUserId;
		$request_form = new Default_Form_Servicerequest();
		$asset_cat_ids=array();
		
		if($userid){
		$service_request_model = new Default_Model_Servicerequests();
		$user_allocated_cat=$service_request_model->getuserallocatedAssetData($userid);
	 
		$this->view->user_allocated_cat=$user_allocated_cat;
		
		$this->view->request_form=$request_form;
		}
		
	}
	
	 
}//end of class

