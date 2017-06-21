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

class Default_AppraisalselfController extends Zend_Controller_Action
{

    private $options;
    public $app_history_disc_array = array(1=>APP_TXT_EMP_SUBMIT, 2=>APP_TXT_L1_SUBMIT, 3=>APP_TXT_L2_SUBMIT, 4=>APP_TXT_L3_SUBMIT,
										5=>APP_TXT_L4_SUBMIT, 6=>APP_TXT_L5_SUBMIT, 7=>APP_TXT_COMPLETED);
										
	public $app_status_array = array(1=>APP_PENDING_EMP, 2=>APP_PENDING_L1, 3=>APP_PENDING_L2, 4=>APP_PENDING_L3,
										5=>APP_PENDING_L4, 6=>APP_PENDING_L5, 7=>APP_COMPLETED);
	public function preDispatch()
	{		 
		
	}
	
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('save', 'json')->initContext();
    }

    public function indexAction()
    {
    	$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
		try
		{
	    	$appEmpRatingsModel = new Default_Model_Appraisalemployeeratings();
	    	$app_rating_model = new Default_Model_Appraisalratings();
			$appEmpRatingsData = $appEmpRatingsModel->getSelfAppraisalDataByEmpID($loginUserId);	//configuration_id
			if(sizeof($appEmpRatingsData)>0 && $appEmpRatingsData[0]['employee_id'] == $loginUserId && $appEmpRatingsData[0]['status'] == 1){
				if($appEmpRatingsData[0]['appraisal_status']=='Pending employee ratings')
				{
					$this->_redirect('appraisalself/edit');
				}else
				{
					// Check rating exist for appraisal. 
					$checkRatingsExists = $app_rating_model->getAppraisalRatingsbyInitId($appEmpRatingsData[0]['pa_initialization_id']);
					
					// get all Categories Data based on category ids
					$categories_data = $appEmpRatingsModel->getAppCateDataByIDs($appEmpRatingsData[0]['category_id']);
					
					// get question previleges data of employee for that initialization
					$appEmpQuesPrivData = $appEmpRatingsModel->getAppEmpQuesPrivData($appEmpRatingsData[0]['pa_initialization_id'], $appEmpRatingsData[0]['employee_id']); 
					
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
					
					// Employee and Manager response
					$emp_response = array();
					$mgr_response = array();
					if($appEmpRatingsData[0]['employee_response'])
						$emp_response = json_decode($appEmpRatingsData[0]['employee_response'],true);
						
					if($appEmpRatingsData[0]['manager_response'])
						$mgr_response = json_decode($appEmpRatingsData[0]['manager_response'],true);
					                                
					// get rating details using configuration id
					$ratingsData = $appEmpRatingsModel->getAppRatingsDataByConfgId($appEmpRatingsData[0]['pa_configured_id'],$appEmpRatingsData[0]['pa_initialization_id']);
					$ratingType = "";
					if(!empty($ratingsData))
						$ratingType = $ratingsData[0]['rating_type'];
					
					$ratingText = array();
					$ratingTextDisplay = array();
					$ratingValues = array();
					foreach ($ratingsData as $rd){
						$ratingText[] = $rd['rating_text'];
						$ratingTextDisplay[$rd['id']] = $rd['rating_text'];
						$ratingValues[$rd['id']] = $rd['rating_value']; 
					}
	
					// building managers names from Emp-Ques-Privileges data
					$managerIDs = array();
					$managerNames = array();
					$line_unique_arr = array_filter(array_unique(array(1 => $appEmpQuesPrivData[0]['line_manager_1'],2 => $appEmpQuesPrivData[0]['line_manager_2'],3 => $appEmpQuesPrivData[0]['line_manager_3'],4 => $appEmpQuesPrivData[0]['line_manager_4'],5 => $appEmpQuesPrivData[0]['line_manager_5'])),'strlen');
					if(!empty($line_unique_arr)){
						for ($i=1;$i<=count($line_unique_arr);$i++){
							$managerIDs[] = $line_unique_arr[$i];
						}
						
						$usersData = $appEmpRatingsModel->getUserNamesByIDs(implode($managerIDs,','));
						
						foreach ($managerIDs as $mi){
							foreach ($usersData as $ud){
								if($ud['id']==$mi)
								//	$managerNames[] = $ud['userfullname'];	
									$managerNames[$ud['id']] = $ud['userfullname'];		
							}
						}
					}
					
					// Skill response data
					$skill_response = array();
					if($appEmpRatingsData[0]['skill_response']){
						$skill_response = json_decode($appEmpRatingsData[0]['skill_response'],true);
						$skill_ids = array_keys($skill_response);
						$skData = $appEmpRatingsModel->getSkillNamesByIDs(implode($skill_ids,','));
						
						$skillData = array();
						foreach ($skData as $s)
							$skillData[$s['id']] = $s['skill_name'];
						
						$this->view->skillData = $skillData;
					}
					
					// get Appraisal history details
					$appHistoryData = $appEmpRatingsModel->getAppHistoryData($appEmpRatingsData[0]['employee_id'],$appEmpRatingsData[0]['pa_initialization_id']);
					
					$this->view->skill_response = $skill_response;
					$this->view->appHistoryData = $appHistoryData;
					$this->view->managerNames = $managerNames;
					$this->view->appEmpRatingsData = $appEmpRatingsData;					
					$this->view->categories_data = $categories_data;
					$this->view->questions_data = $questions_data;
					$this->view->ratingType = $ratingType;
					$this->view->ratingTextDisplay = $ratingTextDisplay;
					$this->view->ratingText = json_encode($ratingText);
					$this->view->ratingValues = $ratingValues;
					$this->view->emp_response = $emp_response;
					$this->view->mgr_response = $mgr_response;
					$this->view->check_ratings_exists = $checkRatingsExists;
					$this->view->login_user_id = $loginUserId;
				}
			}
		    else{
				$this->view->rowexist = "norows";
           	}
		}
		catch(Exception $e)
		{
			echo $e->getMessage();
			$this->view->rowexist = "norows";
		}				    	
    }
    
	public function editAction()
    {
    	$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		
		try
		{
	    	$appEmpRatingsModel = new Default_Model_Appraisalemployeeratings();
	    	$app_rating_model = new Default_Model_Appraisalratings();
			$appEmpRatingsData = $appEmpRatingsModel->getSelfAppraisalDataByEmpID($loginUserId);
			
			if(sizeof($appEmpRatingsData)>0 && $appEmpRatingsData[0]['employee_id'] == $loginUserId && $appEmpRatingsData[0]['status'] == 1 && $appEmpRatingsData[0]['appraisal_status'] == APP_PENDING_EMP){
				// Check rating exist for appraisal. 
				$checkRatingsExists = $app_rating_model->getAppraisalRatingsbyInitId($appEmpRatingsData[0]['pa_initialization_id']);
				// get all Categories Data based on category ids
				$categories_data = $appEmpRatingsModel->getAppCateDataByIDs($appEmpRatingsData[0]['category_id']);
				
				// get question previleges data of employee for that initialization
				$appEmpQuesPrivData = $appEmpRatingsModel->getAppEmpQuesPrivData($appEmpRatingsData[0]['pa_initialization_id'], $appEmpRatingsData[0]['employee_id']); 
				
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
				
				// merging HR and Manager questions privileges
				$hr_ques_previs = array();
				$mgr_ques_previs = array();
                                $ratingType = array();
				if($appEmpQuesPrivData[0]['hr_group_qs_privileges'])
					$hr_ques_previs = json_decode($appEmpQuesPrivData[0]['hr_group_qs_privileges'],true);
					
				if($appEmpQuesPrivData[0]['manager_qs_privileges'])
					$mgr_ques_previs = json_decode($appEmpQuesPrivData[0]['manager_qs_privileges'],true);
				
				$question_previs = $hr_ques_previs + $mgr_ques_previs;
				
				// Employee and Manager response
				$emp_response = array();
				$mgr_response = array();
				if($appEmpRatingsData[0]['employee_response'])
					$emp_response = json_decode($appEmpRatingsData[0]['employee_response'],true);
					
				if($appEmpRatingsData[0]['manager_response'])
					$mgr_response = json_decode($appEmpRatingsData[0]['manager_response'],true);
				
				// get rating details using configuration id
				$ratingsData = $appEmpRatingsModel->getAppRatingsDataByConfgId($appEmpRatingsData[0]['pa_configured_id'],$appEmpRatingsData[0]['pa_initialization_id']);
                                if(count($ratingsData) > 0)
                                    $ratingType = $ratingsData[0]['rating_type'];
				
				$ratingText = array();
				$ratingValues = array();
				foreach ($ratingsData as $rd){
					$ratingText[] = $rd['rating_text'];
					$ratingValues[$rd['id']] = $rd['rating_value']; 
				}

				// building managers names from Emp-Ques-Privileges data
				$managerIDs = array();
				$managerNames = array();
				$line_unique_arr = array_filter(array_unique(array(1 => $appEmpQuesPrivData[0]['line_manager_1'],2 => $appEmpQuesPrivData[0]['line_manager_2'],3 => $appEmpQuesPrivData[0]['line_manager_3'],4 => $appEmpQuesPrivData[0]['line_manager_4'],5 => $appEmpQuesPrivData[0]['line_manager_5'])),'strlen');
				if(!empty($line_unique_arr)){
						for ($i=1;$i<=count($line_unique_arr);$i++){
							$managerIDs[] = $line_unique_arr[$i];
					}
					$usersData = $appEmpRatingsModel->getUserNamesByIDs(implode($managerIDs,','));
					
					foreach ($managerIDs as $mi){
						foreach ($usersData as $ud){
							if($ud['id']==$mi)
								$managerNames[] = $ud['userfullname'];							
						}
					}
				}
				
				$this->view->managerNames = $managerNames;				
				$this->view->mgrLevels = $appEmpQuesPrivData[0]['manager_levels'];
				$this->view->appEmpRatingsData = $appEmpRatingsData;					
				$this->view->categories_data = $categories_data;
				$this->view->questions_data = $questions_data;
				$this->view->question_previs = $question_previs;					
				$this->view->ratingType = $ratingType;
				$this->view->ratingText = json_encode($ratingText);
				$this->view->ratingValues = $ratingValues;
				$this->view->emp_response = $emp_response;
				$this->view->mgr_response = $mgr_response;
				$this->view->check_ratings_exists = $checkRatingsExists;
				$this->view->login_user_id = $loginUserId;
			}
		    else{
				$this->view->rowexist = "norows";
           	}
		}
		catch(Exception $e)
		{
			$this->view->rowexist = "norows";
		}		
    }
    
	public function saveAction()
    {
    	$auth = Zend_Auth::getInstance();
    	$loginuserFullName = '';
     	if($auth->hasIdentity()){
     		$loginUserId = $auth->getStorage()->read()->id;
			$loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
			$loginuserProfileImg = $auth->getStorage()->read()->profileimg;
			$loginuserEmail = $auth->getStorage()->read()->emailaddress;
			$loginuserFullName = $auth->getStorage()->read()->userfullname;
			$loginUserEmpId = $auth->getStorage()->read()->employeeId;
		}
		try
    	{
    		$appEmpRatingsModel = new Default_Model_Appraisalemployeeratings();
    		$app_init_model = new Default_Model_Appraisalinit();
    		$id = $this->_request->getParam('id');
    		$employee_id = $this->_request->getParam('employee_id');
    		$initialization_id = $this->_request->getParam('initialization_id');
    		$config_id = $this->_request->getParam('config_id');
    		$flag = $this->_request->getParam('flag');
    		$app_status = $this->_request->getParam('app_status');
    		$mgr_levels = $this->_request->getParam('mgr_levels');
    		
            $ratingsData = $appEmpRatingsModel->getAppRatingsDataByConfgId($config_id,$initialization_id);//configuration id
    		$ratingValues = array();
			foreach ($ratingsData as $rd)
				$ratingValues[$rd['id']] = $rd['rating_value']; 
            
            $appData = array(	
							'modifiedby'=>$loginUserId,
							'modifiedby_role'=>$loginuserRole,
							'modifiedby_group'=>$loginuserGroup,
							'modifieddate'=>gmdate("Y-m-d H:i:s"),
					);
			
            	$emp_rating_arr = $this->_request->getParam('emp_rating');
            	$emp_comment_arr = $this->_request->getParam('emp_comment');
            	$emp_response = array();
	            if(sizeof($emp_rating_arr)>0 || sizeof($emp_comment_arr)>0){
	            	foreach($emp_rating_arr as $qid=>$val){
	            		if(isset($emp_rating_arr[$qid]))
	            			$rating_id = array_search($emp_rating_arr[$qid], $ratingValues);
	            		else
	            			$rating_id = '';
	            		$emp_response[$qid] = array('comment'=>$emp_comment_arr[$qid],'rating_id'=>$rating_id);
	            	}
	            }
	            
	            $appData['employee_response'] = json_encode($emp_response,true);
            
			$curent_level = array_search($app_status, $this->app_status_array);
			if($flag == 'submit')
			{				
            	$appData['appraisal_status'] = $curent_level+1;
            	$history_desc = $this->app_history_disc_array[$curent_level];
	            
	            $appHistoryData = array(
									'employee_id'=>$employee_id,
									'pa_initialization_id'=>$initialization_id,
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

				/* Update employee response column */				
				$appraisaldata = $app_init_model->getConfigData($initialization_id);				
				if($appraisaldata[0]['employee_response']==1)
				{				
					$initdata = array('employee_response'=>2, 
									  'modifiedby'=>$loginUserId,
									   'modifiedby_role'=>$loginuserRole,
									   'modifiedby_group'=>$loginuserGroup,
									  'modifieddate'=>gmdate("Y-m-d H:i:s")
					);
					$initwhere = array('id=?'=>$initialization_id);		
					$app_init_model->SaveorUpdateAppraisalInitData($initdata, $initwhere);
				}		
				
				/*
				 *   Logs Storing
				 */
					$actionflag = '';
					$tableid  = '';
					$actionflag = 1;
					$menuID = APPRAISALSELF;
					sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
				/*
				 *  Logs storing ends
				 */
				
				/** Start
					 * Sending Mails to employees
					 */
				
					//to get initialization details using appraisal Id for Business Unit,Department,To Year
					$appraisalratingsmodel = new Default_Model_Appraisalratings();
					$appraisal_details = $appraisalratingsmodel->getappdata($initialization_id);
					if(!empty($appraisal_details))
					{
						$bunit = $appraisal_details['unitname'];
						$dept = $appraisal_details['deptname'];
						$to_year = $appraisal_details['to_year'];
					}
					
					
					$dept_str = ($dept == '') ? " ":"and <b>$dept</b> department"; 
					$emp_id_str = ($loginuserRole == SUPERADMINROLE) ? " ":"($loginUserEmpId)";
					
						
						$app_manager_model = new Default_Model_Appraisalmanager();
					   $getLineManager = $app_manager_model->getLineMgr($initialization_id,$loginUserId);
					   if(!empty($getLineManager))
					   {
					   
					   	$line_mgr = $getLineManager['line_manager_1'];
					   
						 $employeeDetailsArr = $app_manager_model->getUserDetailsByEmpID($line_mgr);
						 $employeeDetailsArr = $employeeDetailsArr[0];
						// Sending mail to Manager
						if(!empty($employeeDetailsArr))
						{
								$options['subject'] = APPLICATION_NAME.': Self Appraisal Submitted';
                                $options['header'] = "Performance Appraisal : $to_year";
                                $options['toEmail'] = $employeeDetailsArr['emailaddress'];  
                                $options['toName'] = $employeeDetailsArr['userfullname'];
                                $options['message'] = "<div style='padding: 0; text-align: left; font-size:14px; font-family:Arial, Helvetica, sans-serif;'>				
														<span style='color:#3b3b3b;'>Dear ".$employeeDetailsArr['userfullname'].",</span><br />
														<div style='padding:20px 0 0 0;color:#3b3b3b;'> ".$loginuserFullName.$emp_id_str." has submitted appraisal form.</div>
														<div style='padding:20px 0 10px 0;'>Please <a href=".BASE_URL." target='_blank' style='color:#b3512f;'>click here</a> to login to <b>".APPLICATION_NAME."</b>  and check the appraisal ratings and comments.</div>
														</div> ";
                               $mail_id =  sapp_Global::_sendEmail($options); 
                     	}
					   }
					   	
					   	//Sending mail to Employee
								$options['subject'] = APPLICATION_NAME.': Performance Appraisal Submitted to Line1 manager';
                                $options['header'] = "Performance Appraisal : $to_year";
                                $options['toEmail'] = $loginuserEmail;  
                                $options['toName'] = $loginuserFullName;
                                $options['message'] = "<div style='padding: 0; text-align: left; font-size:14px; font-family:Arial, Helvetica, sans-serif;'>				
														<span style='color:#3b3b3b;'>Dear $loginuserFullName,</span><br />
														<div style='padding:20px 0 0 0;color:#3b3b3b;'>Your appraisal form is submitted successfully to your Line1 Manager,".$employeeDetailsArr['userfullname']."(".$employeeDetailsArr['employeeId'].") </div>
														<div style='padding:20px 0 10px 0;'>Please <a href=".BASE_URL." target='_blank' style='color:#b3512f;'>click here</a> to login  to your <b>".APPLICATION_NAME."</b> account.</div>
														</div> ";
                              $mail_id =  sapp_Global::_sendEmail($options);
                               

				
			}
					/**
					 * End
					 */	
			
			$appWhere = array('id=?'=>$id);
			$result1 = $appEmpRatingsModel->SaveorUpdateAppraisalSkillsData($appData, $appWhere);
			
			if($result1)
			{
				
				$msg = 'saved';
			}
			else {
				$msg = 'err';
			}
    	}
    	catch(Exception $e)
        {
        	//echo $e->getMessage();
        	//echo $e->getTrace();
        	//echo $e->getTraceAsString();
        	$msg = "Something went wrong, please try again.";
        }
        $this->_helper->json(array('msg'=>$msg));
    }
    
/*    
    public function saveAction()
    {
    	$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
			$loginuserFullName = $auth->getStorage()->read()->userfullname;
			$loginuserProfileImg = $auth->getStorage()->read()->profileimg;
		}
    	try
    	{
    		$appEmpRatingsModel = new Default_Model_Appraisalemployeeratings();
    		
			$id = $this->_request->getParam('id');
    		$employee_id = $this->_request->getParam('employee_id');
    		$initialization_id = $this->_request->getParam('initialization_id');
    		$config_id = $this->_request->getParam('config_id');
    		$flag = $this->_request->getParam('flag');
    		$app_status = $this->_request->getParam('app_status');
    		$mgr_levels = $this->_request->getParam('mgr_levels');
    		
            $ratingsData = $appEmpRatingsModel->getAppRatingsDataByConfgId($config_id);
    		$ratingValues = array();
			foreach ($ratingsData as $rd)
				$ratingValues[$rd['id']] = $rd['rating_value']; 
            
            $appData = array(	
							'modifiedby'=>$loginUserId,
							'modifiedby_role'=>$loginuserRole,
							'modifiedby_group'=>$loginuserGroup,
							'modifieddate'=>gmdate("Y-m-d H:i:s"),
					);
			
            if($app_status == APP_PENDING_EMP)
            {
            	$emp_rating_arr = $this->_request->getParam('emp_rating');
            	$emp_comment_arr = $this->_request->getParam('emp_comment');
            	$emp_response = array();
	            if(sizeof($emp_rating_arr)>0 || sizeof($emp_comment_arr)>0){
	            	foreach($emp_rating_arr as $qid=>$val){
	            		if(isset($emp_rating_arr[$qid]))
	            			$rating_id = array_search($emp_rating_arr[$qid], $ratingValues);
	            		else
	            			$rating_id = '';
	            		$emp_response[$qid] = array('comment'=>$emp_comment_arr[$qid],'rating_id'=>$rating_id);
	            	}
	            }
	            
	            $appData['employee_response'] = json_encode($emp_response,true);
            }
            
            if($app_status == APP_PENDING_L1)
            {
            	$mgr_rating_arr = $this->_request->getParam('mgr_rating');
            	$mgr_comment_arr = $this->_request->getParam('mgr_comment');
            	$line_rating_1 = $this->_request->getParam('line_rating_1');
            	$line_comment_1 = trim($this->_request->getParam('line_comment_1'));
            	
	            $mgr_response = array();
	    		if(sizeof($mgr_rating_arr)>0 || sizeof($mgr_comment_arr)>0){
	            	foreach($mgr_rating_arr as $qid=>$val){
	            		if(isset($mgr_rating_arr[$qid]))
	            			$rating_id = array_search($mgr_rating_arr[$qid], $ratingValues);
	            		else
	            			$rating_id = '';
	            		$mgr_response[$qid] = array('comment'=>$mgr_comment_arr[$qid],'rating_id'=>$rating_id);
	            	}
	            }
	            
	            $appData['manager_response'] = json_encode($mgr_response,true);
	            $appData['line_comment_1'] = ($line_comment_1!=''?trim($line_comment_1):NULL);
	            $appData['line_rating_1'] = ($line_rating_1!=''?trim($line_rating_1):NULL);
            }
            
            if($app_status == APP_PENDING_L2)
            {
            	$line_rating_2 = $this->_request->getParam('line_rating_2');
            	$line_comment_2 = trim($this->_request->getParam('line_comment_2'));
            	
            	$appData['line_comment_2'] = ($line_comment_2!=''?trim($line_comment_2):NULL);
	            $appData['line_rating_2'] = ($line_rating_2!=''?trim($line_rating_2):NULL);
            }
            
    		if($app_status == APP_PENDING_L3)
            {
            	$line_rating_3 = $this->_request->getParam('line_rating_3');
            	$line_comment_3 = trim($this->_request->getParam('line_comment_3'));
            	
            	$appData['line_comment_3'] = ($line_comment_3!=''?trim($line_comment_3):NULL);
	            $appData['line_rating_3'] = ($line_rating_3!=''?trim($line_rating_3):NULL);
            }
            
    		if($app_status == APP_PENDING_L4)
            {
            	$line_rating_4 = $this->_request->getParam('line_rating_4');
            	$line_comment_4 = trim($this->_request->getParam('line_comment_4'));
            	
            	$appData['line_comment_4'] = ($line_comment_4!=''?trim($line_comment_4):NULL);
	            $appData['line_rating_4'] = ($line_rating_4!=''?trim($line_rating_4):NULL);
            }
            
    		if($app_status == APP_PENDING_L5)
            {
            	$line_rating_5 = $this->_request->getParam('line_rating_5');
            	$line_comment_5 = trim($this->_request->getParam('line_comment_5'));
            	
            	$appData['line_comment_5'] = ($line_comment_5!=''?trim($line_comment_5):NULL);
	            $appData['line_rating_5'] = ($line_rating_5!=''?trim($line_rating_5):NULL);
            }
            
			$curent_level = array_search($app_status, $this->app_status_array);
			
			if($flag == 'submit'){
	            if($curent_level<$mgr_levels+1){
	            	$appData['appraisal_status'] = $curent_level+1;
	            	$history_desc = $this->app_history_disc_array[$curent_level];
	            }else{
	            	$appData['appraisal_status'] = 7;	            	
	            	$history_desc = $this->app_history_disc_array[7];
	            }
	            
	            $appHistoryData = array(
									'employee_id'=>$employee_id,
									'pa_initialization_id'=>$initialization_id,
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
			}
			
			$appWhere = array('id=?'=>$id);
			$result1 = $appEmpRatingsModel->SaveorUpdateAppraisalSkillsData($appData, $appWhere);
			
			if($result1)
				$msg = 'saved';
			else
				$msg = 'err';
    	}
    	catch(Exception $e)
        {
        	$msg = "Something went wrong, please try again.";
        }
        $this->_helper->json(array('msg'=>$msg));
    }
*/
}