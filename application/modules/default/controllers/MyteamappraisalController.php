<?php

/* ********************************************************************************* 
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
 * ****************************************************************************** */

/**
 * MyteamappraisalController is used to give manager response to employees appraisal
 *
 * @author K.Rama Krishna
 */
class Default_MyteamappraisalController extends Zend_Controller_Action
{
    private $_options;
    
    public $app_history_disc_array = array(1=>APP_TXT_EMP_SUBMIT, 2=>APP_TXT_L1_SUBMIT, 3=>APP_TXT_L2_SUBMIT, 4=>APP_TXT_L3_SUBMIT,
										5=>APP_TXT_L4_SUBMIT, 6=>APP_TXT_L5_SUBMIT, 7=>APP_TXT_COMPLETED);
										
	public $app_status_array = array(1=>APP_PENDING_EMP, 2=>APP_PENDING_L1, 3=>APP_PENDING_L2, 4=>APP_PENDING_L3,
										5=>APP_PENDING_L4, 6=>APP_PENDING_L5, 7=>APP_COMPLETED);
    
    public function preDispatch()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('getempcontent', 'html')->initContext();
        $ajaxContext->addActionContext('getsearchedempcontent', 'html')->initContext();
        $ajaxContext->addActionContext('getsearchedstatus', 'html')->initContext();
        $ajaxContext->addActionContext('savemngresponse', 'json')->initContext();
        $ajaxContext->addActionContext('savelineresponse', 'json')->initContext();
    }
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
        $auth = Zend_Auth::getInstance();
        
        if($auth->hasIdentity())
        {
        	$this->loggedInUser = $auth->getStorage()->read()->id;
        	$this->loggedInUserRole = $auth->getStorage()->read()->emprole;
        	$this->loggedInUserGroup = $auth->getStorage()->read()->group_id;
        }
    }
    
    public function savelineresponseAction()
    {
    	
        $post_values = $this->getRequest()->getPost();
        $result = array('status' => 'fail','msg' => 'Something went wrong, please try again.');
        if(count($post_values) > 0)
        {
            // if($post_values['consol_rating'] != '' && $post_values['consol_comments'] != '')
            // {
                $auth = Zend_Auth::getInstance();
                if($auth->hasIdentity())
                {
                    $loginUserId = $auth->getStorage()->read()->id;
                    $loginuserRole = $auth->getStorage()->read()->emprole;
                    $loginuserGroup = $auth->getStorage()->read()->group_id;
                    $loginuserFullName = $auth->getStorage()->read()->userfullname;
					$loginuserProfileImg = $auth->getStorage()->read()->profileimg;
					$loginuserEmail = $auth->getStorage()->read()->emailaddress;
					$loginuserEmpId = $auth->getStorage()->read()->employeeId;
					
                    $loginuserArr = array(
                        'loginuserid' => $loginUserId,
                        'loginuserrole'=>$loginuserRole,
                        'loginusergroup'=>$loginuserGroup,	
                    );            
                }

                $model = new Default_Model_Appraisalemployeeratings();
                $app_manager_model = new Default_Model_Appraisalmanager();
              	$manager_rating = $post_values['consol_rating'];
                $consol_comments = $post_values['consol_comments'];
                $employee_id = $post_values['hid_emp_id'];
                $appraisal_id = $post_values['hid_init_id'];
                $flag = $post_values['hid_btn_flag'];
                $hid_total_lines = $post_values['hid_total_lines'];
               	$hid_line_status = $post_values['hid_line_status'];
               	$line_mgr1 = '';
				$line_mgr2 = '';
               	for($i=1;$i<$hid_line_status;$i++)
               	{
               	  $line_mgr1 .= 'line_rating_'.$i.'+';
               	  $line_mgr2 .= 'line_rating_'.$i.',';
               	} //building variables for query 
				$line_mgr1 = rtrim($line_mgr1, "+"); 
				//to get total ratings line managers given earlier. 
              	$get_total_rating = $model->get_total_rating($appraisal_id,$employee_id,$line_mgr1,$line_mgr2);
                $total_ratings = $get_total_rating['total_ratings'];
                //Not updating consolidated value for for draft status 
                $new_rating = ($flag == 'draft')? $total_ratings/($hid_line_status-1) : ($total_ratings+$manager_rating)/$hid_line_status;  
               // $rating_arr = $model->getRatingsByInitId($appraisal_id);
                $save_data = array(                                        
                    'line_comment_'.$hid_line_status => $consol_comments,
                    //'line_rating_'.$hid_line_status => $rating_arr[$consol_rating],
                	'line_rating_'.$hid_line_status => $manager_rating,
                    'consolidated_rating' => $new_rating,
                    'modifiedby' => $loginUserId,
                    'modifiedby_role' => $loginuserRole,
                    'modifiedby_group' => $loginuserGroup,
                    'modifieddate' => gmdate("Y-m-d H:i:s"),
                );
                if($flag != 'draft')
                {
                	/* Using this db call because to get latest value of number of line 
                	 * managersif line managers levels edited then   '$hid_total_lines' is failing.
                	 * 
                	 */
                	$employee_id_str = $employee_id; //
                	$total_lineMgr_count = $model->get_total_lineMgr_count($appraisal_id,$employee_id);
                	if($total_lineMgr_count == $hid_line_status)
                	{
                        $save_data['appraisal_status'] = "Completed";
                        $mail_sub = "Completed";
                        
                	}
                    else 
                    { 
                        $save_data['appraisal_status'] = "Pending L".($hid_line_status+1)." ratings";
                        $mail_sub = "Submitted to Line ".($hid_line_status+1)." Manager.";
                        $next_mgr_num = $hid_line_status+1;
                        //$line_mgr_str = "line_".($hid_line_status+1)."_mgr";
                       // $line_mgr = $$line_mgr_str;
                        $next_line_mgr = $app_manager_model->getNextLineMgr($appraisal_id,$employee_id,$next_mgr_num);
                        if(!empty($next_line_mgr))
                        {
                        	$next_line_mgr = $next_line_mgr['line_manager_'.$next_mgr_num] ;
                        }
                        $employee_id_str =  $employee_id.",".$next_line_mgr;
                    }
                         //$history_desc : changing the description text according to the draft flag and submit flag(Ref Array:app_history_disc_array)
                 		$history_desc = $this->app_history_disc_array[$hid_line_status+1];
                        $appHistoryData = array(
									'employee_id'=>$employee_id,
									'pa_initialization_id'=>$appraisal_id,
									'description'=>$history_desc,
									'desc_emp_id'=>$loginUserId,
									'desc_emp_name'=>$loginuserFullName,
									'desc_emp_profileimg'=>$loginuserProfileImg,
									'createdby'=>$loginUserId,
									'createddate'=>gmdate("Y-m-d H:i:s"),
	            					'modifiedby'=>$loginUserId,
									'modifieddate'=>gmdate("Y-m-d H:i:s"),
								);
						$appHistoryModel = new Default_Model_Appraisalhistory();
						//$result2 = $appHistoryModel->SaveorUpdateAppraisalHistoryData($appHistoryData);
                }

                $save_where = " pa_initialization_id ='".$appraisal_id."' and employee_id = '".$employee_id."' and appraisal_status = 'Pending L".($hid_line_status)." ratings' ";

                $save_result = $model->SaveorUpdateAppraisalSkillsData($save_data, $save_where);
               
                
                 
	             	

					
       						 // Sending mail to Employee
								$employeeDetailsArr = $app_manager_model->getUserDetailsByEmpID($employee_id_str);
					  			$appraisalratingsmodel = new Default_Model_Appraisalratings();
					  			$appraisal_details = $appraisalratingsmodel->getappdata($appraisal_id);
								if(!empty($appraisal_details))
								{
									$to_year = $appraisal_details['to_year'];
								}
					  			
					  			//Preparing Employee array for Bcc
								$empArr = array();
								if(!empty($employeeDetailsArr))
								{
									$empArrList = '';
									$empUserIdArr = array();
									$toEmailId = '';
									$toEmailName = '';
									foreach($employeeDetailsArr as $emp)
									{  
										
										array_push($empArr,$emp['emailaddress']); //preparing Bcc array
										array_push($empUserIdArr,$emp['user_id']);
										if($emp['user_id'] == $employee_id)    // checking employeeId to prepare toemailAddress
										{
											$toEmailId = $emp['emailaddress'];
											$toEmailName = $emp['userfullname'];
											$toEmpId = $emp['employeeId'];
											array_pop($empArr);
										}
									 }
									 //pushing loginUserEmail to Bcc array 
									  array_push($empArr,$loginuserEmail);
									//echo "<pre>";print_r($empArr); die();
									
								$options['subject'] = APPLICATION_NAME.': Appraisal '.$mail_sub;
                                $options['header'] = 'Performance Appraisal : '.$to_year;
                                $options['toEmail'] = $toEmailId;
                                $options['bcc'] 	= $empArr;   
                                $options['toName'] = $toEmailName;
                                $options['message'] = "<div style='padding: 0; text-align: left; font-size:14px; font-family:Arial, Helvetica, sans-serif;'>				
														<span style='color:#3b3b3b;'>Hi,</span><br />
														<div style='padding:20px 0 0 0;color:#3b3b3b;'>$loginuserFullName($loginuserEmpId) has Updated $toEmailName($toEmpId) appraisal form. </div>
														<div style='padding:20px 0 10px 0;'>Please <a href=".BASE_URL." target='_blank' style='color:#b3512f;'>click here</a> to login  to your <b>".APPLICATION_NAME."</b> account and check the details.</div>
														</div> ";
                                $mail_id =  sapp_Global::_sendEmail($options); 
							
						}
                
                if($save_result)                        
                    $result = array('status' => 'success','msg' => 'Employee appraisal process '.($flag == 'draft'?"drafted":"submitted").' successfully');
            // }
        }
        $this->_helper->json($result);
    }
    public function savemngresponseAction()
    {
        $post_values = $this->getRequest()->getPost();
        $result = array('status' => 'fail','msg' => 'Something went wrong, please try again.');
        if(count($post_values) > 0)
        {
            if(isset($post_values['hid_employee_id']) && $post_values['hid_employee_id'] != ''  && isset($post_values['hid_appraisal_id']) && $post_values['hid_appraisal_id'] != '' )
            {
                //if($post_values['consol_rating'] != '' && $post_values['consol_comments'] != '')
                //{
                    $auth = Zend_Auth::getInstance();
                    if($auth->hasIdentity())
                    {
                        $loginUserId = $auth->getStorage()->read()->id;
                        $loginuserRole = $auth->getStorage()->read()->emprole;
                        $loginuserGroup = $auth->getStorage()->read()->group_id;
                        $loginuserFullName = $auth->getStorage()->read()->userfullname;
                        $loginuserEmail = $auth->getStorage()->read()->emailaddress;
						$loginuserProfileImg = $auth->getStorage()->read()->profileimg;
						$loginuserEmpId = $auth->getStorage()->read()->employeeId;
                        $loginuserArr = array(
                            'loginuserid' => $loginUserId,
                            'loginuserrole'=>$loginuserRole,
                            'loginusergroup'=>$loginuserGroup,	
                        );            
                    } 
                                    
					$model = new Default_Model_Appraisalemployeeratings();
					$appEmpRatingsModel = new Default_Model_Appraisalemployeeratings();
					$consol_rating = (isset($post_values['consol_rating']) && trim($post_values['consol_rating']) != 0)?$post_values['consol_rating']:NULL;
					$consol_comments = isset($post_values['consol_comments'])?$post_values['consol_comments']:'';
					$employee_id = $post_values['hid_employee_id'];
					$appraisal_id = $post_values['hid_appraisal_id'];
					$question_rating = isset($post_values['question_rating'])?$post_values['question_rating']:'';
					$question_comments = isset($post_values['question_comments'])?$post_values['question_comments']:'';
					$skill_ids = isset($post_values['emp_skills'])?$post_values['emp_skills']:'';
					$skill_rating = isset($post_values['skill_rating'])?$post_values['skill_rating']:'';
					$flag = $post_values['hid_btn_flag'];
					$ratings_ids_arr = $model->getRatingsByInitId($appraisal_id);
					$mng_response_arr = array();
					$skill_response_arr = array();
					$response_json = '';
					$skill_json = '';
                    if(!empty($ratings_ids_arr))
                    {
	                    if(!empty($question_comments))
	                    {
		                    foreach($question_comments as $qid => $qc)
		                    {
		                    	// if($qc!='')
		                          $mng_response_arr[$qid]['comment'] = (isset($qc) && trim($qc) != '')?$qc:'';
		                        // if($qid!='' && $question_rating[$qid]!='') 
		                          $mng_response_arr[$qid]['rating'] = isset($ratings_ids_arr[$question_rating[$qid]])?$ratings_ids_arr[$question_rating[$qid]]:'';
		                    }
	                    }
	                    if(!empty($skill_ids))
	                    {
		                    foreach($skill_ids as $key => $sk_id)
		                    {
		                        $skill_response_arr[$sk_id] = isset($ratings_ids_arr[$skill_rating[$key]])?$ratings_ids_arr[$skill_rating[$key]]:0;
		                    }
	                    }
                    }
                    if(!empty($mng_response_arr)) {
                    	$response_json = json_encode($mng_response_arr);
                    }
                    if(!empty($skill_response_arr))	{
                    	$skill_json = json_encode($skill_response_arr);
                    }
                    $save_data = array(
                        'manager_response' => $response_json!=''?$response_json:NULL,
                        'skill_response' => $skill_json!=''?$skill_json:NULL,
                        'line_comment_1' => $consol_comments,
                        'line_rating_1' => $consol_rating,
                        'consolidated_rating' => $consol_rating,
                        'modifiedby' => $loginUserId,
                        'modifiedby_role' => $loginuserRole,
                        'modifiedby_group' => $loginuserGroup,
                        'modifieddate' => gmdate("Y-m-d H:i:s"),
                    );
                    if($flag != 'draft')
                    {	
                    	$line_2_mgr = '';
                    	$appEmpQuesPrivData = $appEmpRatingsModel->getAppEmpQuesPrivData($appraisal_id, $employee_id);
                    	$line_2_mgr = $appEmpQuesPrivData[0]['line_manager_2'];
                    	if(!isset($line_2_mgr) && $line_2_mgr=='')
                    	{
                    		$save_data['appraisal_status'] = "Completed";
                    		$mail_sub = "Completed"; // using for mail sending scenario.
                    	}
                    	else 
                    	{	
                        	$save_data['appraisal_status'] = "Pending L2 ratings";
                        	$mail_sub = "Submitted to Line 2 Manager.";
                    	}
                        	
                        $history_desc = $this->app_history_disc_array[2];
		            	$appHistoryData = array(
										'employee_id'=>$employee_id,
										'pa_initialization_id'=>$appraisal_id,
										'description'=>$history_desc,
										'desc_emp_id'=>$loginUserId,
										'desc_emp_name'=>$loginuserFullName,
										'desc_emp_profileimg'=>$loginuserProfileImg,
										'createdby'=>$loginUserId,
										'createddate'=>gmdate("Y-m-d H:i:s"),
		            					'modifiedby'=>$loginUserId,
										'modifieddate'=>gmdate("Y-m-d H:i:s"),
									);
									
						$appHistoryModel = new Default_Model_Appraisalhistory();
						$result2 = $appHistoryModel->SaveorUpdateAppraisalHistoryData($appHistoryData);	

						
							//sending mails and Logsmanager action 
							$tableid  = '';
			           		$actionflag = 1;
							  $menuID = APPRAISALSELF;
				
							$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
							
							
						/** Start
						 * Sending Mails to employees
						 */
						
							// Sending mail to Employee
								$employee_id_str = $employee_id;
								$app_manager_model = new Default_Model_Appraisalmanager();
								if($line_2_mgr != '' && is_numeric($line_2_mgr))
								{
									$employee_id_str =  $employee_id.','.$line_2_mgr;
								}
					  			$employeeDetailsArr = $app_manager_model->getUserDetailsByEmpID($employee_id_str);
					  			
                   				$appraisalratingsmodel = new Default_Model_Appraisalratings();
					  			$appraisal_details = $appraisalratingsmodel->getappdata($appraisal_id);
								if(!empty($appraisal_details))
								{
									$to_year = $appraisal_details['to_year'];
								}
					  			//echo "<pre>";print_r($employeeDetailsArr);
							//Preparing Employee array for Bcc
								$empArr = array();
								if(!empty($employeeDetailsArr))
								{
									$empArrList = '';
									$empUserIdArr = array();
									$toEmailId = '';
									$toEmailName = '';
									foreach($employeeDetailsArr as $emp)
									{  
										
										array_push($empArr,$emp['emailaddress']); //preparing Bcc array
										array_push($empUserIdArr,$emp['user_id']);
										if($emp['user_id'] == $employee_id)    // checking employeeId to prepare toemailAddress
										{
											$toEmailId = $emp['emailaddress'];
											$toEmailName = $emp['userfullname'];
											$toEmpId = $emp['employeeId'];
											array_pop($empArr);
										}
									 }
									 //pushing loginUserEmail to Bcc array 
									  array_push($empArr,$loginuserEmail);
									//echo "<pre>";print_r($empArr); die();
									
								$options['subject'] = APPLICATION_NAME.': Appraisal '.$mail_sub;
                                $options['header'] = 'Performance Appraisal : '.$to_year;
                                $options['toEmail'] = $toEmailId;
                                $options['bcc'] 	= $empArr;   
                                $options['toName'] = $toEmailName;
                                $options['message'] = "<div style='padding: 0; text-align: left; font-size:14px; font-family:Arial, Helvetica, sans-serif;'>				
														<span style='color:#3b3b3b;'>Hi,</span><br />
														<div style='padding:20px 0 0 0;color:#3b3b3b;'>$loginuserFullName($loginuserEmpId) has Updated $toEmailName($toEmpId) appraisal form. </div>
														<div style='padding:20px 0 10px 0;'>Please <a href=".BASE_URL." target='_blank' style='color:#b3512f;'>click here</a> to login  to your <b>".APPLICATION_NAME."</b> account and check the details.</div>
														</div> ";
                                $mail_id =  sapp_Global::_sendEmail($options); 
							
						}
						
						
								
                    }    
                    
                    $save_where = " pa_initialization_id ='".$appraisal_id."' and employee_id = '".$employee_id."' and appraisal_status = 'Pending L1 ratings' ";
                    $save_result = $model->SaveorUpdateAppraisalSkillsData($save_data, $save_where);
                   	
                   
					  
                    if($save_result)                        
                        $result = array('status' => 'success','msg' => 'Employee appraisal process '.($flag == 'draft'?"drafted":"submitted").' successfully');
                //}
            }
        }
        
        $this->_helper->json($result);
    }
    public function getempcontentAction()
    {
        $post_values = $this->getRequest()->getPost();
        if(count($post_values) > 0)
        {
            $appraisal_id = sapp_Global::_decrypt($post_values['appraisal_id']);
            $manager_id = sapp_Global::_decrypt($post_values['manager_id']);
            $user_id = sapp_Global::_decrypt($post_values['user_id']);
            $flag = sapp_Global::_decrypt($post_values['flag']);
            $app_config_id = sapp_Global::_decrypt($post_values['app_config_id']);
            $app_ratings = sapp_Global::_decrypt($post_values['app_rating']);
            $emp_status = sapp_Global::_decrypt($post_values['emp_status']);
            $history_flag = isset($post_values['history_flag'])?sapp_Global::_decrypt($post_values['history_flag']):'';
            $key = $post_values['key'];
            $appEmpRatingsModel = new Default_Model_Appraisalemployeeratings();
            $data = array();
            if($appraisal_id != '' && $manager_id != '' && $user_id != '' && $flag != '')
            {
                $model = new Default_Model_Appraisalmanager();                
                $data = $model->getempcontent($appraisal_id,$manager_id,$user_id,$flag,$app_config_id);                                                    
            }
            $appEmpQuesPrivData = $appEmpRatingsModel->getAppEmpQuesPrivData($appraisal_id, $user_id);
            $hr_ques_previs = array();
			$mgr_ques_previs = array();
            $ratingType = array();
				if($appEmpQuesPrivData[0]['hr_group_qs_privileges'])
					$hr_ques_previs = json_decode($appEmpQuesPrivData[0]['hr_group_qs_privileges'],true);
					
				if($appEmpQuesPrivData[0]['manager_qs_privileges'])
					$mgr_ques_previs = json_decode($appEmpQuesPrivData[0]['manager_qs_privileges'],true);
				
		    $question_previs = $hr_ques_previs + $mgr_ques_previs;
		    
		    // Get 'My Team Appraisal - Employee' skills
		    $emp_skills = $appEmpRatingsModel->getAppEmpSkills($appraisal_id, $user_id);
		    
            $this->view->data = $data;
            $this->view->key = $key;
            $this->view->flag =$flag;
            $this->view->user_id = $user_id;
            $this->view->emp_skills = $emp_skills;
            $this->view->appraisal_id = $appraisal_id;
            $this->view->emp_status = $emp_status;
            $this->view->app_ratings = $app_ratings;
            $this->view->question_previs = $question_previs;
            $this->view->manager_id = $manager_id;
            $this->view->app_config_id = $app_config_id;
            $this->view->history_flag = $history_flag;
        }
    }
    public function indexAction()
    {
        $errorMsg = "";
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $businessunit_id = $auth->getStorage()->read()->businessunit_id;
            $department_id = $auth->getStorage()->read()->department_id; 
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
        }
        $view = $this->view;                        
        $model = new Default_Model_Appraisalmanager();
        $app_rating_model = new Default_Model_Appraisalratings();
        $ratingflag = 'false';
        $linemangerids = '';
        $managerprofileimgArr = array();
        $businessUnits = array();
		$business_unit_model = new Default_Model_Businessunits();
		$businessUnits = $business_unit_model->getBusinessUnitsList();
		
        $emp_data = $model->getEmpdata_managerapp($loginUserId);
        if(!empty($emp_data))
        {
        	$checkRatingsExists = $app_rating_model->getAppraisalRatingsbyInitId($emp_data[0]['init_id']);
        	if(!empty($checkRatingsExists))
        	{
        		$ratingflag = 'true';
        	}
        }
        if(!empty($emp_data))
        {
        	foreach($emp_data as $key => $empval)
        	{
        		for($i=1;$i<=5;$i++)
        		{
        			if(isset($empval['line_rating_'.$i]))
        			{
        				$linemangerids.=$empval['line_manager_'.$i].',';
        			}
        		}
        		if($linemangerids)
        		{
        			    $linemangerids = rtrim($linemangerids,',');  
        				$managerprofileimgArr = $app_rating_model->getManagerProfileImg($linemangerids);
        		}
        		$emp_data[$key]= $emp_data[$key]+$managerprofileimgArr;
        		$linemangerids = '';
        		$managerprofileimgArr = array();
        		
        	}
        }
        $view->emp_data = $emp_data;
        $view->manager_id = $loginUserId;                            
        $view->error_msg = $errorMsg;
        $view->ratingflag = $ratingflag;
        $view->business_units = $businessUnits;
        $view->loginuserRole = $loginuserRole;
        $view->loginuserGroup = $loginuserGroup;
    }
    
	public function getsearchedempcontentAction()
    {
        $errorMsg = "";
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $businessunit_id = $auth->getStorage()->read()->businessunit_id;
            $department_id = $auth->getStorage()->read()->department_id; 
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
        }
        $searchval = '';
        $searchstring = mysql_real_escape_string($this->_request->getParam('searchstring'));
       // $searchstring = $this->_request->getParam('searchstring');
        $view = $this->view;                        
        $model = new Default_Model_Appraisalmanager();
        $app_rating_model = new Default_Model_Appraisalratings();
        $ratingflag = 'false';
        $linemangerids = '';
        $managerprofileimgArr = array();
		
        if($searchstring!='')
        {
        	$searchval = " and es.userfullname like '%$searchstring%'";
        }
        //$emp_data = $model->getSearchEmpdata_managerapp($loginUserId,$searchval);
        $emp_data = $model->getEmpdata_managerapp($loginUserId,$searchval);
    	if(!empty($emp_data))
        {
        	foreach($emp_data as $key => $empval)
        	{
        		for($i=1;$i<=5;$i++)
        		{
        			if(isset($empval['line_rating_'.$i]))
        			{
        				$linemangerids.=$empval['line_manager_'.$i].',';
        			}
        		}
        		if($linemangerids)
        		{
					$linemangerids = rtrim($linemangerids,',');  
					$managerprofileimgArr = $app_rating_model->getManagerProfileImg($linemangerids);
        		}
        		$emp_data[$key]= $emp_data[$key]+$managerprofileimgArr;
        		$linemangerids = '';
        		$managerprofileimgArr = array();
        		
        	}
        }
        
        $view->emp_data = $emp_data;
        $view->manager_id = $loginUserId;                            
        $view->error_msg = $errorMsg;
    }
    
	public function getsearchedstatusAction()
    {
        $errorMsg = "";
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            
            $businessunit_id = $auth->getStorage()->read()->businessunit_id;
            $department_id = $auth->getStorage()->read()->department_id; 
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
        }
        
        $appraisalstatus = $this->_request->getParam('statusval');
        $businessunit = $this->_request->getParam('business_unit');
        $view = $this->view;                        
        $model = new Default_Model_Appraisalmanager();
		$appwhere = '';
        if($appraisalstatus)
        	  $appwhere = ' and er.appraisal_status='.$appraisalstatus.' ';	
        
		if($businessunit)
        	  $appwhere = ' and es.businessunit_id='.$businessunit.' ';	
			
        $emp_data = $model->getEmpdata_managerapp($loginUserId,$appwhere);
       
        $view->emp_data = $emp_data;
        $view->manager_id = $loginUserId;                            
        $view->error_msg = $errorMsg;
    }
    //code for pdf
   //for downloading pdf
    public function downloadpdfAction()
    {
        $this->_helper->layout->disableLayout();
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $businessunit_id = $auth->getStorage()->read()->businessunit_id;
            $department_id = $auth->getStorage()->read()->department_id; 
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
        }
        $appraisalManagerModelObj = new Default_Model_Appraisalmanager();
        // $userId = !empty($this->_request->getParam('user_id'))?trim($this->_request->getParam('user_id')):null;
        $appraisal_id = sapp_Global::_decrypt($this->_request->getParam('appraisal_id'));
        $manager_id = sapp_Global::_decrypt($this->_request->getParam('manager_id'));
        $user_id = sapp_Global::_decrypt($this->_request->getParam('user_id'));
        $flag = sapp_Global::_decrypt($this->_request->getParam('flag'));
        $app_config_id = sapp_Global::_decrypt($this->_request->getParam('app_config_id'));
        $app_ratings = sapp_Global::_decrypt($this->_request->getParam('app_rating'));
        $emp_status = sapp_Global::_decrypt($this->_request->getParam('emp_status'));
        $history_flag = sapp_Global::_decrypt($this->_request->getParam('history_flag'));
        $key = $this->_request->getParam('key');
        $appEmpRatingsModel = new Default_Model_Appraisalemployeeratings();
        if(!is_numeric($user_id))
        {
            return false;
        }
        else
        {
            //get the employee details with manager overall rating
            $searchval = " AND es.user_id=$user_id ";
            if($flag != 'manager')
            {
                // If PDF is being downloaded from history page
                if(!empty($history_flag) && $history_flag==1) {
                    $empAppraisalData = $appEmpRatingsModel->getEmpAppraisalDoc($user_id,$appraisal_id);
                    if(!empty($empAppraisalData)) {
                        $empAppraisalData = $appEmpRatingsModel->getSelfAppraisalHistoryDataByAppID($empAppraisalData[0]['id']);
                    }
                }else{
                    $empAppraisalData = $appEmpRatingsModel->getSelfAppraisalDataByEmpID($loginUserId);
                }    
            }
            else 
            {
            	//myteam appraisal
                // If PDF is being downloaded from history page
                if(!empty($history_flag) && $history_flag==1) {
                    $searchval = " and qp.employee_id =$user_id ";
                    $empAppraisalData = $appraisalManagerModelObj->getEmpdata_managerapphistory($manager_id,$searchval,$appraisal_id); 
                }else{
                    $empAppraisalData = $appraisalManagerModelObj->getEmpdata_managerapp($manager_id,$searchval);
                }
            }
            $data = array();
            if($appraisal_id != '' && $user_id != '' && $flag != '')
            {
                $model = new Default_Model_Appraisalmanager();
                $data = $appraisalManagerModelObj->getempcontent($appraisal_id,$manager_id,$user_id,$flag,$app_config_id);

                if(empty($data['employee_response']))
                {
                    $data['category_arr'] = $this->appraisalQuestionsForDownload($appraisal_id,$user_id);

                }
              // in analytics to print appraisal pdf document
                if($flag == 'analytics')
                {
                    $empAppraisalModel = new Default_Model_Appraisalemployeeratings();
                    $empAppraisalData = $empAppraisalModel->getAnalyticsEmpAppraisalPdfData($user_id,$appraisal_id);
                    $data = $appraisalManagerModelObj->getempcontent($appraisal_id,$empAppraisalData[0]['line_manager_1'],$user_id,$flag,$empAppraisalData[0]['id']);
                }
            }
            $appEmpQuesPrivData = $appEmpRatingsModel->getAppEmpQuesPrivData($appraisal_id, $user_id);
            $hr_ques_previs = array();
            $mgr_ques_previs = array();
            $ratingType = array();
           if($appEmpQuesPrivData[0]['hr_group_qs_privileges'])
                $hr_ques_previs = json_decode($appEmpQuesPrivData[0]['hr_group_qs_privileges'],true);
                
            if($appEmpQuesPrivData[0]['manager_qs_privileges'])
                $mgr_ques_previs = json_decode($appEmpQuesPrivData[0]['manager_qs_privileges'],true);
                
            $question_previs = $hr_ques_previs + $mgr_ques_previs;
            
            // Get 'My Team Appraisal - Employee' skills
            $emp_skills = $appEmpRatingsModel->getAppEmpSkills($appraisal_id, $user_id);
            //app period (Q1,H2 etc)
            $appText = utf8_encode(substr($empAppraisalData[0]['appraisal_mode'],0,1)).$empAppraisalData[0]['appraisal_period'];
            //render view page as text
            $view = $this->getHelper('ViewRenderer')->view;
            $this->view->data = $data;
            $this->view->key = $key;
            $this->view->flag =$flag;
            $this->view->user_id = $user_id;
            $this->view->emp_skills = $emp_skills;
            $this->view->appraisal_id = $appraisal_id;
            $this->view->emp_status = $emp_status;
            $this->view->app_ratings = $app_ratings;
            $this->view->question_previs = $question_previs;                
            $this->view->empAppraisalData = $empAppraisalData; 
            $this->view->loginUserId = $loginUserId; 
            $this->view->appText = $appText; 
            $text = $view->render('myteamappraisal/appraisalpdf.phtml');
            //generating file name
            $file_name_params_array = array($empAppraisalData[0]['userfullname'],$empAppraisalData[0]['from_year'],$empAppraisalData[0]['to_year'],$appText);
            $file_name = $this->_helper->PdfHelper->generateFileName($file_name_params_array);
            //mpdf
            require_once 'MPDF57/mpdf.php';
            $mpdf=new mPDF('', 'A4', 14, '', 10, 10, 12, 12, 6, 6);
            $mpdf->SetDisplayMode('fullpage');
            
            $mpdf->list_indent_first_level = 0;
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->pagenumPrefix = 'Generated using Sentrifugo'.str_repeat(" ",72);
            $mpdf->pagenumSuffix = '';
            $mpdf->nbpgPrefix = ' of ';
            $mpdf->nbpgSuffix = '';
            $mpdf->SetFooter('{PAGENO}{nbpg}');
            $mpdf->AddPage();
            $mpdf->WriteHTML($text);
            $mpdf->Output((!empty($file_name)?$file_name:'appraisal').'.pdf','D');
            exit;
        }
    }
    //for employee self appraisal
    public function appraisalQuestionsForDownload($appraisal_id,$employee_id)
    {
        $appModel = new Default_Model_Appraisalinit();
        $appraisalData = $appModel->getAppDataById($appraisal_id);
        $appEmpRatingsModel = new Default_Model_Appraisalemployeeratings();
        // get all Categories Data based on category ids
         $categories_data = null;
        if(!empty($appraisalData['category_id']))
        {
            $categories_data = $appEmpRatingsModel->getAppCateDataByIDs($appraisalData['category_id']);    
        }
        // get question previleges data of employee for that initialization
        $appEmpQuesPrivData = $appEmpRatingsModel->getAppEmpQuesPrivData($appraisal_id, $employee_id); 
        
        // merging HR and Manager questions
        $ques_csv = '';
        if($appEmpQuesPrivData[0]['hr_qs']){
            $ques_csv .= $appEmpQuesPrivData[0]['hr_qs'];
        }
        if($appEmpQuesPrivData[0]['manager_qs']){
            if($ques_csv){ $ques_csv .= ','; }
            $ques_csv .= $appEmpQuesPrivData[0]['manager_qs'];
        }
        
        // get all questions data based on above question ids
        $questions_data = $appEmpRatingsModel->getAppQuesDataByIDs($ques_csv);
        $final_array = array();
        $id_array = array();
        $name_array = array();
        foreach ($categories_data as $key => $value) 
        {
            foreach ($questions_data as $k => $v) 
            {
                if($value['id'] == $v['pa_category_id'])
                {
                    $final_array[$value['category_name']][] = $v;
                }
            }   
        }
        return $final_array;
    }    
    
public function downloadUploadedFileAction()
    {
    	
      $cand_details_model = new Default_Model_Candidatedetails();
    	$result = $cand_details_model->getcandidateData($this->_getParam('id'));
    	if(!empty($result['cand_resume'])){
    		$status = array();
			$file = BASE_PATH.'/uploads/resumes/'.$result['cand_resume'];
			$status = sapp_Global::downloadFile($file);
			if(!empty($status['message'])){				
                            $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>$status['message']));
			}
    	}
  		$this->_redirect('candidatedetails/index');
    }
    
    
}
