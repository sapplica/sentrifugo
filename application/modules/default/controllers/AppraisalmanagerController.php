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
 * ****************************************************************************** 
 */

/**
 * For manager's login to assign manager questions to employees.
 *
 * @author ramakrishna
 */
class Default_AppraisalmanagerController extends Zend_Controller_Action
{
    private $_options;
    
    public function preDispatch()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('createnewgroup', 'html')->initContext();
        $ajaxContext->addActionContext('showgroups', 'html')->initContext();
        $ajaxContext->addActionContext('showviewgroups', 'html')->initContext();
        $ajaxContext->addActionContext('viewgroup', 'html')->initContext();
        $ajaxContext->addActionContext('savemanagergroup', 'json')->initContext();
        $ajaxContext->addActionContext('submitmanager', 'json')->initContext();
    }
    
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
    }
    
    public function submitmanagerAction()
    {
    	
       $auth = Zend_Auth::getInstance();
     	 if($auth->hasIdentity())
         {
         	 $loginUserId = $auth->getStorage()->read()->id;
             $loginuserRole = $auth->getStorage()->read()->emprole;
             $loginuserGroup = $auth->getStorage()->read()->group_id;
             $loginuserFullname = $auth->getStorage()->read()->userfullname;
             $loginuserEmail = $auth->getStorage()->read()->emailaddress;
             $loginUserEmpId = $auth->getStorage()->read()->employeeId;          
         }
        $post_values = $this->getRequest()->getPost();
        $result = array('status' => 'fail','msg' => 'Something went wrong, please try again.');
        $questions = '';
        if(count($post_values) > 0)
        {
            $appraisal_id = $post_values['appraisal_id'];
            $manager_id = $post_values['manager_id'];
            if($appraisal_id != '' && $manager_id != '' )
            {
                $appraisal_id = sapp_Global::_decrypt($appraisal_id);
                $manager_id = sapp_Global::_decrypt($manager_id);
                
		
                $trDb = Zend_Db_Table::getDefaultAdapter();		
                $trDb->beginTransaction();
                try
                {
                	 
                    $app_init_model = new Default_Model_Appraisalinit();
                    $appraisalPrivMainModel = new Default_Model_Appraisalqsmain();
                    $qsdataArr = $appraisalPrivMainModel->getAppraisalQuestionsMain($appraisal_id);
					
					if(!empty($qsdataArr))
		        	{
		        		foreach($qsdataArr as $qs)
		        		{
							if(!empty($qs['manager_qs']))
			        			$questions.=$qs['manager_qs'].',';
		        		}
		        		$questions = rtrim($questions,',');
		        	}
					
					$questions = implode(',', array_keys(array_flip(explode(',', $questions))));
					$submit_manager = $app_init_model->submitmanager($appraisal_id,$manager_id);
					
                    sapp_PerformanceHelper::update_QsParmas_Allemps($questions,'');

                /*
				 *   Logs Storing
				 */
					$actionflag = '';
					$tableid  = '';
					$menuID = APPRAISAL_MANAGER;
					$actionflag = 1;
					sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
					
				/*
				 *  Logs storing ends
				 */
					
					//to get initialization details using appraisal Id for Business Unit,Department,To Year
					$appraisalratingsmodel = new Default_Model_Appraisalratings();
					$appraisal_details = $appraisalratingsmodel->getappdata($appraisal_id);
					if(!empty($appraisal_details))
					{
						$bunit = $appraisal_details['unitname'];
						$dept = $appraisal_details['deptname'];
						$to_year = $appraisal_details['to_year'];
					}
				
					/** Start
					 * Sending Mails to employees
					 */
							   $appraisalconfigmodel = new Default_Model_Appraisalconfig();
							   $app_manager_model = new Default_Model_Appraisalmanager();
							   $getBunit_dept = $app_manager_model->getBunitDept($appraisal_id);
							   if(!empty($getBunit_dept))
							   {
							   	$unitID = $getBunit_dept['businessunit_id'];
							   	$deptID = $getBunit_dept['department_id'];
							   }
								 $employeeDetailsArr = $appraisalconfigmodel->getUserDetailsByID($unitID,$deptID);
						
						 		$dept_str = ($dept == '') ? " ":"and <b>$dept</b> department"; 
								$emp_id_str = ($loginuserRole == SUPERADMINROLE) ? " ":"($loginUserEmpId)";
						
                				$empArr = array();
								if(!empty($employeeDetailsArr))
								{
									$empArrList = '';
									foreach($employeeDetailsArr as $emp)
									{
										array_push($empArr,$emp['emailaddress']);
									}
									
								}
						// Sending mail to HR
								$options['subject'] 	= APPLICATION_NAME.': Manager Appraisal Submitted.';
                                $options['header'] 		= 'Performance Appraisal : Manager Appraisal '.$to_year;
                                $options['toEmail'] 	= $loginuserEmail;   
                                $options['bcc'] 		= $empArr; 
                                $options['toEmail'] 	= $loginuserEmail; 
                                $options['toName'] 		= $loginuserFullname; 
                                $options['message'] 	= "<div style='padding: 0; text-align: left; font-size:14px; font-family:Arial, Helvetica, sans-serif;'>				
														<span style='color:#3b3b3b;'>Hi,</span><br />
														<div style='padding:20px 0 0 0;color:#3b3b3b;'> ".$loginuserFullname.$emp_id_str." has submitted the Manager appraisal form for the year <b>$to_year</b> for <b>$bunit</b> business unit $dept_str </div>
														<div style='padding:20px 0 10px 0;'>Please <a href=".BASE_URL." target='_blank' style='color:#b3512f;'>click here</a> to login  to your <b>".APPLICATION_NAME."</b> account and check the details.</div>
														</div> ";
                                $mail_id 				= sapp_Global::_sendEmail($options); 
							
						
						
					/**
					 * End
					 */	
						
					$trDb->commit();
                    $result['status'] = 'success';
                    $result['msg'] = "Submitted successfully";  
                    $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Initialization Submitted successfully. "));
                    
                }
                catch (Exception $e) 
                {
                    $trDb->rollBack();
                    $result['status'] = 'error';
                    $result['msg'] = $e->getMessage();
                }
            }
        }
        $this->_helper->json($result);
    }
    /**
     * This action is to delete a group which created by manager.
     * @return json Array of status and message.
     */
    public function deletemanagergroupAction()
    {
        $post_values = $this->getRequest()->getPost();
         $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
        }    
        $result = array('status' => 'fail','msg' => 'Something went wrong, please try again.','empcount'=>'','appraisalid'=>'','managerid'=>'');
        if(count($post_values) > 0)
        {
            $appraisal_id = $post_values['appraisal_id'];
            $manager_id = $post_values['manager_id'];
            $group_id = $post_values['group_id'];
            
            if($appraisal_id != '' && $manager_id != '' && $group_id != '')
            {
                $trDb = Zend_Db_Table::getDefaultAdapter();		
                $trDb->beginTransaction();
                try
                {
                    $app_manager_model = new Default_Model_Appraisalmanager();
                    $delete_manager_group = $app_manager_model->deletemanagergroup($appraisal_id,$manager_id,$group_id);
                    $EmpCountArr = $app_manager_model->getManagerGroupCount($appraisal_id,$loginUserId);
					if(!empty($EmpCountArr))
						$result['empcount'] = $EmpCountArr[0]['empcount'];
                    $trDb->commit();
                    $result['status'] = 'success';
                    $result['msg'] = "Group deleted successfully";
                    $result['appraisalid'] = sapp_Global::_encrypt($appraisal_id);
                    $result['managerid'] = sapp_Global::_encrypt($manager_id);  
                }
                catch (Exception $e) 
                {
                    $trDb->rollBack();
                    $result['status'] = 'error';
                    $result['msg'] = $e->getMessage();
                }
            }
        }
        $this->_helper->json($result);
    }
    /**
     * This action is used to save/edit group details created by manager.
     * @return json Array of status and message.
     */
    public function savemanagergroupAction()
    {
        $post_values = $this->getRequest()->getPost();
        //echo "<pre>";print_r($post_values);echo "</pre>";exit;
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
            $loginuserArr = array(
                'loginuserid' => $loginUserId,
                'loginuserrole'=>$loginuserRole,
                'loginusergroup'=>$loginuserGroup,	
            );            
        }
        $finalarray = $deleted_emp_arr = $result = array();
        $mgrIndex = 'MC';
        $mgrratIndex = 'MR';
        $empIndex = 'EC';
        $empratIndex = 'ER';
        $questionArr = $post_values['check'];
        $managercmntsArr = isset($post_values['mgrcmnt'])?$post_values['mgrcmnt']:array();
        $managerratingsArr = isset($post_values['mgrrating'])?$post_values['mgrrating']:array();
        $empratingsArr = isset($post_values['empratings'])?$post_values['empratings']:array();
        $empcmntsArr = isset($post_values['empcmnt'])?$post_values['empcmnt']:array();
        $empids = $post_values['existetd_mem_str'];
        $groupid = $post_values['groupid'];
        $group_name = $post_values['group_name'];
        $appraisal_id = $post_values['appraisal_id'];
        $manager_id = $post_values['manager_id'];
        $original_mem_str = $post_values['original_mem_str'];
        $action_flag = $post_values['action_flag'];
        
        $selected_emp_arr = explode(',',$empids);
        
        $result['flag'] = 'appraisalmanager';
        $trDb = Zend_Db_Table::getDefaultAdapter();		
        $trDb->beginTransaction();
        try
        {  
            if($group_name !='' && !empty($questionArr) && $empids !='')
            {
                $appraisalGroupModel = new Default_Model_Appraisalgroups();
                $appraisalPrivMainModel = new Default_Model_Appraisalqsmain();
                
                $CheckDuplicateName = $appraisalGroupModel->getDuplicateGroupName($appraisal_id, $group_name,($groupid != '')?$groupid:'');
                if(!empty($CheckDuplicateName))	
                    $duplicateGroupName = $CheckDuplicateName[0]['grpcnt'];
                if($duplicateGroupName > 0)
                {
                    $result['result'] = 'error';
                    $result['msg'] = 'Group name already exists.';						
                }
                else
                {    
                    if(!empty($original_mem_str))
                    {
                        $privilegesdata = array(
                            'manager_group_id' => null, 
                            'manager_qs' => null,
                            'manager_qs_privileges' => null,		
                            'modifiedby' => $loginUserId,
                            'modifiedby_role' => $loginuserRole,
                            'modifiedby_group' => $loginuserGroup,
                            'modifieddate' => gmdate("Y-m-d H:i:s")
                        );
                        $privielgeswhere = " pa_initialization_id = '".$appraisal_id."' and line_manager_1 = '".$manager_id."' and employee_id IN ($original_mem_str) and module_flag=1 and isactive= 1 ";
                                  
                        $updateprivileges = $appraisalPrivMainModel->SaveorUpdatePrivilegeData($privilegesdata, $privielgeswhere);
                    }
                    if(!empty($questionArr))
                    {
                        for($i=0;$i<sizeof($questionArr);$i++)
                        {                                
                            $managercomments = isset($managercmntsArr[$questionArr[$i]])?1:0;                                                               
                            $managerratings = isset($managerratingsArr[$questionArr[$i]])?1:0;                                                               
                            $empratings = isset($empratingsArr[$questionArr[$i]])?1:0;                                                               
                            $empcomments = isset($empcmntsArr[$questionArr[$i]])?1:0;
                                                           
                            $commntsarry = array($mgrratIndex=>$managerratings,$mgrIndex=>$managercomments,$empratIndex=>$empratings,$empIndex=>$empcomments);	
                            $finalarray[$questionArr[$i]] = $commntsarry;

                        }
                        $questions = implode(',', $questionArr);
                        $qsprivileges = json_encode($finalarray,true);
                        
                        $groupdata = array(
                            'pa_initialization_id'=>$appraisal_id,
                            'group_name'=>trim($group_name), 
                            'modifiedby'=>$loginUserId,
                            'modifiedby_role'=>$loginuserRole,
                            'modifiedby_group'=>$loginuserGroup,
                            'modifieddate'=>gmdate("Y-m-d H:i:s")
                        );
                        if($groupid!='')
                        {
                            $where = array('id=?'=>$groupid);  
                            $actionflag = 2;
                        }
                        else
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
                             
                        $privilegesdata = array(
                            'manager_group_id'=>$groupid, 
                            'manager_qs'=>$questions,
                            'manager_qs_privileges'=>$qsprivileges,		
                            'modifiedby'=>$loginUserId,
                            'modifiedby_role'=>$loginuserRole,
                            'modifiedby_group'=>$loginuserGroup,
                            'modifieddate'=>gmdate("Y-m-d H:i:s")
                        );
                        $privielgeswhere = " pa_initialization_id = '".$appraisal_id."' and line_manager_1 = '".$manager_id."' and employee_id IN ($empids) and module_flag=1 and isactive= 1 ";
                                  
                        $updateprivileges = $appraisalPrivMainModel->SaveorUpdatePrivilegeData($privilegesdata, $privielgeswhere);
                        //echo '<pre>';print_r($finalarray);echo "</pre>";
                        $trDb->commit();
                        $result['result'] = 'success';
                        $result['msg'] = "Group ".($action_flag == 'edit'?"updated":"created")." successfully";  
                    }
                    else 
                    {
                        $result['result'] = 'error';
                        $result['msg'] = 'No questions to add';
                    }
                }
                
            }
            else 
            {
                $result['result'] = 'error';
                $result['msg'] = 'Something went wrong.';
            }
            
        } 
        catch (Exception $e) 
        {
            $trDb->rollBack();
            $result['result'] = 'error';
            $result['msg'] = $e->getMessage();
        }
        $this->_helper->json($result);
    }
    /**
     * This action is to display grid view of manager related appraisal.
     */
    public function indexAction()
    {
        $appraisalInitModel = new Default_Model_Appraisalmanager();	
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
        $this->render('commongrid/index', null, true);
    }
    
    /**
     * This action is used to view the group by manager.It will act on ajax call.
     */
    public function viewgroupAction()
    {
        $post_values = $this->getRequest()->getPost();
        if(count($post_values) > 0)
        {
            $appraisal_id = $this->_getParam('appraisal_id',null);
            $manager_id = $this->_getParam('manager_id',null);
            $group_id = $this->_getParam('group_id',null);
            $group_name = $this->_getParam('group_name',null);
            
            try
            {
                if($appraisal_id != '' && $manager_id != '' && $group_id != '')
                {
                    $appraisal_id = sapp_Global::_decrypt($appraisal_id);
                    $manager_id = sapp_Global::_decrypt($manager_id);
                    $group_id = sapp_Global::_decrypt($group_id);
                    
                    $app_manager_model = new Default_Model_Appraisalmanager();
                    $appraisal_init_model = new Default_Model_Appraisalinit();
                    $appraisal_qs_model = new Default_Model_Appraisalquestions();

                    $manager_emp = $app_manager_model->getmanager_emp($appraisal_id, $manager_id,$group_id);
                    $appraisaldata = $appraisal_init_model->getConfigData($appraisal_id);
                    $appraisaldata = (isset($appraisaldata[0]) && !empty($appraisaldata[0]))?$appraisaldata[0]:array();
                    if(!empty($appraisaldata) && !empty($manager_emp))
					{
						$ques_ids = $manager_emp[0]['manager_qs'];
						$questionsArr = $appraisal_qs_model->getQuestionsByCategory($appraisaldata['category_id'],$ques_ids);
						
						$manager_qs_privileges = json_decode($manager_emp[0]['manager_qs_privileges'],true);
						foreach($manager_qs_privileges as $key => $val)
						{                            
							$check_array[$key] = $val;
						}
						
						$view = $this->view;
						$view->appraisal_id = $appraisal_id;
						$view->manager_id = $manager_id;
						$view->manager_emp = $manager_emp;
						$view->questionsArr = $questionsArr;
						$view->appraisaldata = $appraisaldata;
						$this->view->checkArr = $check_array;
						$view->group_name = sapp_Global::_decrypt($group_name);
					}
					else
					{
						$this->view->ermsg = 'nodata';
					}					
                }
                else
                {
                    $this->view->ermsg = 'nodata';
                }
            } 
            catch (Exception $e) 
            {
                $this->view->ermsg = 'nodata';
            }
        }
    }

    /**
     * This action is used to create new group by manager.It will serve as ajax call.
     */
    public function createnewgroupAction()
    {
        $appraisal_id = $this->_getParam('appraisal_id',null);
        $manager_id = $this->_getParam('manager_id',null);
        $flag = $this->_getParam('flag',null);
        $group_id = $this->_getParam('group_id',null);
        
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {            
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
        }
        
        try
        {
            if($appraisal_id != '' && $manager_id != '')
            {
                $appraisal_id = sapp_Global::_decrypt($appraisal_id);
                $manager_id = sapp_Global::_decrypt($manager_id);
                if($flag == 'edit')
                    $group_id = sapp_Global::_decrypt($group_id);
                
                $app_manager_model = new Default_Model_Appraisalmanager();
                $appraisal_init_model = new Default_Model_Appraisalinit();
                $appraisal_qs_model = new Default_Model_Appraisalquestions();
                $check_array = array();
                $tablename = 'main_pa_questions_privileges';
                $manager_emp = $app_manager_model->getmanager_emp($appraisal_id, $manager_id,'');
                                
                if(empty($manager_emp) && $flag == 'add')
                    $this->view->ermsg = 'No employees to add.';
                
                $appraisaldata = $appraisal_init_model->getConfigData($appraisal_id);
                $appraisaldata = $appraisaldata[0];
                
                $questionPrivileges = $appraisal_qs_model->gethrquestionprivileges($appraisal_id,$tablename,'');  
                                                    
                $questionsArr = $appraisal_qs_model->getQuestionsByCategory($appraisaldata['category_id'],'');
                
            	if(!empty($questionPrivileges))
			 	{
			 		if(isset($questionPrivileges['manager_qs']) && isset($questionPrivileges['manager_qs_privileges']))
			 		{
			 			if($questionPrivileges['manager_qs'] !='' && $questionPrivileges['manager_qs_privileges'] !='')
			 			{
					 		$hr_qs_Arr = explode(',',$questionPrivileges['manager_qs']);
					 		$hr_qs_privileges = json_decode($questionPrivileges['manager_qs_privileges'],true);
						 	foreach($hr_qs_privileges as $key => $val)
						 	{
						 		//$val = explode(',',substr($val, 1, -1));
						 		$check_array[$key] = $val;
						 	}
			 			}
			 		}
			 	}
			 	
                if(sapp_Global::_checkprivileges(APPRAISALQUESTIONS,$loginuserGroup,$loginuserRole,'edit') == 'Yes')
                {
                    $appraisaldata['poppermission'] = 'yes';
                }
                $appraisaldata['poppermission'] = 'yes';
                $manager_emp_selected = array();
                $group_name = "";
                if($flag == 'edit')
                {
                    $app_group_model = new Default_Model_Appraisalgroups();
                    $group_details = $app_group_model->getAppraisalGroupsDatabyID($group_id);
                    if(!empty($group_details))
					{
                        $group_details = $group_details[0];
						$group_name = $group_details['group_name'];
						$manager_emp_selected = $app_manager_model->getmanager_emp($appraisal_id, $manager_id,$group_id);
						$manager_qs_privileges = json_decode($manager_emp_selected[0]['manager_qs_privileges'],true);
						foreach($manager_qs_privileges as $key => $val)
						{                            
							$check_array[$key] = $val;
						}
					}
					else
					{
						$this->view->ermsg = 'No data found.';
					}					
                }
                
                $view = $this->view;
                $view->appraisal_id = $appraisal_id;
                $view->manager_id = $manager_id;
                $view->manager_emp = $manager_emp;
                $view->questionsArr = $questionsArr;
                $view->checkArr = $check_array;
                $view->appraisaldata = $appraisaldata;
                $view->checkArr = $check_array;
                $view->flag = $flag;
                $view->group_name = $group_name;
                $view->group_id = $group_id;
                $view->selected_emp = $manager_emp_selected;
            }
            else
            {
                $this->view->ermsg = 'No data found.';
            }
        } 
        catch (Exception $ex) 
        {
            $this->view->ermsg = 'No data found.';
        }
    }
    
    /**
     * This action is used to display all groups created by manager.It will work on ajax call.
     */
    public function showgroupsAction()
    {
		try{
		$post_values = $this->getRequest()->getPost();
       
        if(count($post_values) > 0)
        {
            $auth = Zend_Auth::getInstance();
            if($auth->hasIdentity())
            {
                $loginUserId = $auth->getStorage()->read()->id;                
            }
            $empgroupcount = '';
            $appraisal_id = $post_values['appraisal_id'];
            $app_manager_model = new Default_Model_Appraisalmanager();
            $appraisal_init_model = new Default_Model_Appraisalinit();
                                                            
            $appraisal_data = $appraisal_init_model->getappdata_forview($appraisal_id);
            $manager_groups = $app_manager_model->getManagergroups($appraisal_id,$loginUserId);
            
            $EmpCountArr = $app_manager_model->getManagerGroupCount($appraisal_id,$loginUserId);
			if(!empty($EmpCountArr))
				$empgroupcount = $EmpCountArr[0]['empcount'];
                    
            $submit_val = "yes";
            if(!in_array($loginUserId,array_unique(explode(',', $appraisal_data['manager_ids']))))    
                $submit_val = "no";
        
            $this->view->appraisal_id = $appraisal_id;
            $this->view->manager_id = $loginUserId;
            $this->view->manager_groups = $manager_groups;
            $this->view->submit_val = $submit_val;
            $this->view->empgroupcount = $empgroupcount;
        }
		}
		catch(Exception $e)
		{
			print_r($e); die;

		}
    }
    
	/**
     * This action is used to display all groups created by manager.It will work on ajax call.
     */
    public function showviewgroupsAction()
    {
        $post_values = $this->getRequest()->getPost();
        if(count($post_values) > 0)
        {
            $auth = Zend_Auth::getInstance();
            if($auth->hasIdentity())
            {
                $loginUserId = $auth->getStorage()->read()->id;                
            }
            $empgroupcount = '';
            $appraisal_id = $post_values['appraisal_id'];
            $app_manager_model = new Default_Model_Appraisalmanager();
            $appraisal_init_model = new Default_Model_Appraisalinit();
                                                            
            $appraisal_data = $appraisal_init_model->getappdata_forview($appraisal_id);
            $manager_groups = $app_manager_model->getManagergroups($appraisal_id,$loginUserId);
            
                    
            $submit_val = "yes";
            if(!in_array($loginUserId,array_unique(explode(',', $appraisal_data['manager_ids']))))    
                $submit_val = "no";
        
            $this->view->appraisal_id = $appraisal_id;
            $this->view->manager_id = $loginUserId;
            $this->view->manager_groups = $manager_groups;
            $this->view->submit_val = $submit_val;
        }
    }
    
 /**
     * This action is used to view the current available appraisal,where manager will manager his/her
     * employees as groups.
     */
    public function viewAction()
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
        $id = $this->getRequest()->getParam('id');
        try
        {
            if($id != '')
            {
			    $app_manager_model = new Default_Model_Appraisalmanager();
				$line_managers = array();
				$line_managers = $app_manager_model->getLineManagers($id,$loginUserId);
				$appraisal_init_model = new Default_Model_Appraisalinit();
				$appraisal_data = $appraisal_init_model->getappdata_forview($id);
				
                if(is_numeric($id) && $id>0 && $line_managers && !empty($appraisal_data))
                {
					$appraisal_data['process_status'] = '';
                    if(!empty($appraisal_data))
                    {
                    	if($appraisal_data['initialize_status'] == 1)
                    	{
                    		if($appraisal_data['enable_step'] == 1)
                    			$appraisal_data['process_status'] = 'Enabled to Managers';
                    		if($appraisal_data['enable_step'] == 2)
                    			$appraisal_data['process_status'] = 'Enabled to Employees';	
                    	}
                    	else if($appraisal_data['initialize_status'] == 2)
                    	{
                    		$appraisal_data['process_status'] = 'Initialize Later';
                    	}else
			         	{
			         	   $data['process_status'] = 'In progress';	
			         	}
                    }
                if($appraisal_data['status'] == 1) {
                    	   $appraisal_data['status']="Open";
                          }else if($appraisal_data['status'] == 2) {
                    	   $appraisal_data['status']="Closed";
                          }else {
                          $appraisal_data['status']="Force Closed";
                          }	
                        if($appraisal_data['appraisal_ratings'] == 1) {
                    	   $appraisal_data['appraisal_ratings']="1-5";
                          }else {
                          $appraisal_data['appraisal_ratings']="1-10";
                          }	
                         if($appraisal_data['appraisal_mode'] == 'Quarterly'){
						 $appraisal_data['appraisal_period']="Q".$appraisal_data['appraisal_period'];
                          }
					      else if($appraisal_data['appraisal_mode'] == 'Half-yearly'){
						  $appraisal_data['appraisal_period']= "H".$appraisal_data['appraisal_period'];
					      }
					      else {
						  $appraisal_data['appraisal_period']="Yearly";
					      }
					      
					      if($appraisal_data['enable_step'] == 1) {
                    	   $appraisal_data['enable_step']="Managers";
                          }else {
                          $appraisal_data['enable_step']="Employees";
                          }
					      $appraisal_data['managers_due_date'] =  sapp_Global::change_date($appraisal_data['managers_due_date'],'view');
                        $appraisal_data['employees_due_date'] = sapp_Global::change_date($appraisal_data['employees_due_date'],'view');
                    $manager_groups = $app_manager_model->getManagergroups($id,$loginUserId);
                    $view = $this->getHelper('ViewRenderer')->view;
                    $view->previ_data = 'no';                    
                    $view->data = $appraisal_data;
                    $text = $view->render('appraisalinit/view.phtml');
                    $this->view->appraisal_id = $id;
                    $this->view->manager_id = $loginUserId;
                    $this->view->manager_groups = $manager_groups;
                    $this->view->appraisal_data = $appraisal_data;
                    $this->view->app_text = $text;
                    $this->view->controllername = 'appraisalmanager';
                    $this->view->id = $id;
                }
                else 
                {
                    $this->view->ermsg = 'nodata';
                }
            }
            else
            {
                $this->view->ermsg = 'nodata';
            }
        } 
        catch (Exception $ex) 
        {
            $this->view->ermsg = 'nodata';
        }
    }
    
    
    /**
     * This action is used to edit the current available appraisal,where manager will manager his/her
     * employees as groups.
     */
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
	 	
        $id = $this->getRequest()->getParam('id');
        try
        {
            if($id != '')
            {
                //$id = sapp_Global::_decrypt($id);
                if(is_numeric($id) && $id>0)
                {
                    $app_manager_model = new Default_Model_Appraisalmanager();
                    $appraisal_init_model = new Default_Model_Appraisalinit();
                    $appraisalQsModel = new Default_Model_Appraisalquestions();
                    $tablename = 'main_pa_questions_privileges';
                                                            
                    $appraisal_data = $appraisal_init_model->getappdata_forview($id);
                    if($appraisal_data['status'] == 1 && $appraisal_data['enable_step'] == 1)
                    {
	                	$appraisal_data['process_status'] = '';
	                    if(!empty($appraisal_data))
	                    {
	                    	if($appraisal_data['initialize_status'] == 1)
	                    	{
	                    		if($appraisal_data['enable_step'] == 1)
	                    			$appraisal_data['process_status'] = 'Enabled to Managers';
	                    		if($appraisal_data['enable_step'] == 2)
	                    			$appraisal_data['process_status'] = 'Enabled to Employees';	
	                    	}
	                    	else if($appraisal_data['initialize_status'] == 2)
	                    	{
	                    		$appraisal_data['process_status'] = 'Initialize Later';
	                    	}else
					        {
					         	   $appraisal_data['process_status'] = 'In progress';	
					        }
	                    }
	                    $EmpCountArr = $app_manager_model->getManagerGroupCount($id,$loginUserId);
						if(!empty($EmpCountArr))
							$appraisal_data['empcount'] = $EmpCountArr[0]['empcount'];
	                    $manager_groups = $app_manager_model->getManagergroups($id,$loginUserId);
	                    
	                    $questionsArr = $appraisalQsModel->getQuestionsByCategory($appraisal_data['category_id'],'');
	                    
	                    $view = $this->getHelper('ViewRenderer')->view;
	                    $view->previ_data = 'no';                    
	                    $view->data = $appraisal_data;
	                    $text = $view->render('appraisalinit/view.phtml');
	                    $this->view->appraisal_id = $id;
	                    $this->view->manager_id = $loginUserId;
	                    $this->view->manager_groups = $manager_groups;
	                    $this->view->appraisal_data = $appraisal_data;
	                    $this->view->app_text = $text; 
	                    $this->view->questionsArr = $questionsArr;
                    }
                    else
                    {
                    	$this->view->ermsg = 'nodata';
                    } 

                }
                else 
                {
                    $this->view->ermsg = 'nodata';
                }
            }
            else
            {
                $this->view->ermsg = 'nodata';
            }
        } 
        catch (Exception $ex) 
        {
            $this->view->ermsg = 'nodata';
        }
    }
}//end of class