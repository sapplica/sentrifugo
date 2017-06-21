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


class Default_HrwizardController extends Zend_Controller_Action
{

	private $options;
	public function preDispatch()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('updatewizardcompletion', 'json')->initContext();
	}

	public function init()
	{
		$this->_options= $this->getInvokeArg('bootstrap')->getOptions();
	}
	
	public function indexAction() {
		$hrWizardModel = new Default_Model_Hrwizard();
		$hrWizardData = $hrWizardModel->getHrwizardData();
		if($hrWizardData['leavetypes'] == 1)
			$this->_redirect('hrwizard/configureleavetypes');
		else if($hrWizardData['holidays'] == 1)
			$this->_redirect('hrwizard/configureholidays');
		else if($hrWizardData['perf_appraisal'] == 1)
			$this->_redirect('hrwizard/configureperformanceappraisal');
		else
			$this->_redirect('hrwizard/configureleavetypes');		
		
	}
	
	public function configureleavetypesAction() {
		
		$hrWizardModel = new Default_Model_Hrwizard();
        $hrWizardData = $hrWizardModel->getHrwizardData();
		if($this->getRequest()->getPost()){
      		$result = $this->saveLeaveTypes($hrWizardData);	
		    $this->view->msgarray = $result; 
		}
		$this->view->ermsg = '';	
        $this->view->hrWizardData = $hrWizardData;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();               
	}
	
	public function saveLeaveTypes($hrWizardData) {
    $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
				 $loginUserId = $auth->getStorage()->read()->id;
				 $loginuserRole = $auth->getStorage()->read()->emprole;
				 $loginuserGroup = $auth->getStorage()->read()->group_id;
		} 
	    $employeeLeaveTypesModel = new Default_Model_Employeeleavetypes();	
		$msgarray = array();
		$errorflag = 'true';
		$service_desk_id = $this->_request->getParam('category_id');
		$leaveTypeArr = $this->_request->getParam('leavetype');
		$numberOfDaysArr = $this->_request->getParam('numberofdays');
		
		if(!empty($leaveTypeArr))
		{
			$leaveArr = array_count_values($leaveTypeArr);
			for($i=0;$i<sizeof($leaveTypeArr);$i++)
			{
				if($leaveTypeArr[$i] == '') {
					$msgarray['leave_type'][$i] = 'Please enter request type.';
					$errorflag = 'false';
				} else if(!preg_match('/^[a-zA-Z0-9.\- ]+$/', $leaveTypeArr[$i])) {
					$msgarray['leave_type'][$i] = 'Please enter valid request type.';
					$errorflag = 'false';
				}
				else if($i>0 && $leaveArr[$leaveTypeArr[$i]] > 1)
				{
					$msgarray['leave_type'][$i] = 'Please enter different leave type.';
					$errorflag = 'false';
				}
				else 
				{
					$duplicateLeaveType = $employeeLeaveTypesModel->checkDuplicateLeaveType($leaveTypeArr[$i]);
					if(!empty($duplicateLeaveType))
					{
						if($duplicateLeaveType[0]['count'] > 0)
						{
							$msgarray['leave_type'][$i] = 'Leave type already exists.';
							$errorflag = 'false';
						}
					}
				}
				
				if($numberOfDaysArr[$i] == '') {
					$msgarray['leave_type'][$i] = 'Please enter number of days.';
					$errorflag = 'false';
				} else if(!preg_match('/^[0-9]+$/', $numberOfDaysArr[$i])) {
					$msgarray['leave_type'][$i] = 'Please enter only number.';
					$errorflag = 'false';
				}
			}
			$msgarray['leavetypesize'] = sizeof($leaveTypeArr);
		}	
		
		  if($errorflag == 'true'){
            try{
			$actionflag = 1;
			$tableid  = ''; 
			$where = '';
			for($i=0;$i<sizeof($leaveTypeArr);$i++)
			{
			   	if(strlen(trim($leaveTypeArr[$i])) > 3)
			   		$leaveCode = strtoupper(substr(trim($leaveTypeArr[$i]), 0, 4));
		    	else		 		
		   			$leaveCode = strtoupper(trim($leaveTypeArr[$i]));		
			   $data = array('leavetype'=>$leaveTypeArr[$i],
			                 'numberofdays'=>$numberOfDaysArr[$i], 
							 'leavecode'=> $leaveCode,
			   				  'leavepreallocated'=>1,
			   				  'leavepredeductable'=>1,
			   				  'createdby'=>$loginUserId,		 	
							  'createddate'=>gmdate("Y-m-d H:i:s"),	
							  'modifiedby'=>$loginUserId,
							  'modifieddate'=>gmdate("Y-m-d H:i:s"),
			   				  'isactive' =>1 	
				);
				
				$Id = $employeeLeaveTypesModel->SaveorUpdateEmployeeLeaveTypeData($data, $where);
				$tableid = $Id; 	
				$menuID = EMPLOYEELEAVETYPES;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
			}
			
			 $hrWizardModel = new Default_Model_Hrwizard();
			
					$hrwizardarray = array('leavetypes' => 2,
                  						 'modifiedby'=>$loginUserId,
                  						'modifieddate'=>gmdate("Y-m-d H:i:s")
                  					);
					 if($hrWizardData['holidays'] == 2 && $hrWizardData['perf_appraisal'] == 2)
					 {
					 	$hrwizardarray['iscomplete'] = 2;
					 }                  					
               		$hrWizardModel->SaveorUpdateHrWizardData($hrwizardarray,'');
				
				$this->_helper->getHelper("FlashMessenger")->addMessage("Leave Types added successfully.");
				$this->_redirect('hrwizard/configureleavetypes');	
          }
          catch(Exception $e)
          {
             $msgarray['category_id'] = "Something went wrong, please try again.";
             return $msgarray;
          }
		}else
		{
			return $msgarray;	
		}
    }
    
	public function configureholidaysAction()
    {
    	$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
        }
        $hrWizardModel = new Default_Model_Hrwizard();
        $holidaygroupsmodel = new Default_Model_Holidaygroups();
        $hrWizardData = $hrWizardModel->getHrwizardData();
        $msgarray = array();
        
        $holidayGroupdata = $holidaygroupsmodel->getAllGroupData();
        if(empty($holidayGroupdata)) {
        	$msgarray['groupname'] = 'Please configure holiday group.';
        }
        if($this->getRequest()->getPost()){
      		$result = $this->saveholidays($hrWizardData);	
		    $this->view->msgarray = $result; 
		}
        $this->view->holidayGroupdata = $holidayGroupdata;
        $this->view->ermsg = '';	
        $this->view->hrWizardData = $hrWizardData;
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
    }
	
	public function saveholidays($hrWizardData) {
    $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
				 $loginUserId = $auth->getStorage()->read()->id;
				 $loginuserRole = $auth->getStorage()->read()->emprole;
				 $loginuserGroup = $auth->getStorage()->read()->group_id;
		} 
	    $holidaydatesmodel = new Default_Model_Holidaydates();	
		$msgarray = array();
		$errorflag = 'true';
		$groupnameId = $this->_request->getParam('groupname');
		$holidayname_arr = $this->_request->getParam('holidayname');
		$holidaydate_arr = $this->_request->getParam('holidaydate');
		$holidayyears=array();
		if(!empty($holidaydate_arr))
		{
			for($i=0;$i<sizeof($holidaydate_arr);$i++)
			{		
				$holidaydate = sapp_Global::change_date($holidaydate_arr[$i], 'database');
				$holidayyears[] = date('Y',strtotime($holidaydate));
			}
		}
		
		if(!empty($holidayname_arr))
		{
			$holidayArr = array_count_values($holidayname_arr);
			$yearcountArr = array_count_values($holidayyears);
			for($i=0;$i<sizeof($holidayname_arr);$i++)
			{
				
				if($holidayname_arr[$i] == '') {
					$msgarray['holiday_name'][$i] = 'Please enter holiday.';
					$errorflag = 'false';
				} else if(!preg_match('/^[a-zA-Z0-9.\- ?]+$/', $holidayname_arr[$i])) {
					$msgarray['holiday_name'][$i] = 'Please enter valid holiday.';
					$errorflag = 'false';
				}
				else if($i>0 && $holidayArr[$holidayname_arr[$i]] > 1)
				{
					if($yearcountArr[$holidayyears[$i]] > 1)
					{
						 $msgarray['holiday_name'][$i] = 'Please enter different holiday.';
						 $errorflag = 'false';
					}
				}
				else 
				{
					if($groupnameId) {
						if(	$holidaydate_arr[$i] != '')
						{				
							$holidaydate = sapp_Global::change_date($holidaydate_arr[$i], 'database');
							$holidayyear = date('Y',strtotime($holidaydate));
						}
						$isduplicateholiday = $holidaydatesmodel->checkholidayname($holidayname_arr[$i],$groupnameId,'',$holidayyear);
						if(!empty($isduplicateholiday))
						{
							if($isduplicateholiday[0]['count'] > 0)
							{
								$msgarray['holiday_name'][$i] = 'Holiday already exist..';
								$msgarray['holiday_group'] = $groupnameId;
								$errorflag = 'false';
							}
						}
					}
				}
				
				if($holidaydate_arr[$i] == '') {
					$msgarray['date_error'][$i] = 'Please enter date.';
					$errorflag = 'false';
				} 
				
				if($groupnameId == '') {
						$msgarray['groupname'] = 'Please select holiday group.';
						$errorflag = 'false';
				}
			}
			$msgarray['holidayerrorsize'] = sizeof($holidayname_arr);
		}	
		
		  if($errorflag == 'true'){
            try{
			$actionflag = 1;
			$tableid  = ''; 
			$where = '';
			$date = new Zend_Date();
			for($i=0;$i<sizeof($holidayname_arr);$i++)
			{
			   $data = array('groupid'=>$groupnameId,
			   				 'holidayname'=>trim($holidayname_arr[$i]),
			                 'holidaydate'=>sapp_Global::change_date($holidaydate_arr[$i], 'database'), 
							 'holidayyear'=>date('Y',strtotime(sapp_Global::change_date($holidaydate_arr[$i], 'database'))),
			   				  'createdby'=>$loginUserId,
							  'createddate'=>gmdate("Y-m-d H:i:s"),	
							  'modifiedby'=>$loginUserId,
							  'modifieddate'=>gmdate("Y-m-d H:i:s"),
			   				  'isactive' =>1 	
				);
				
				$Id = $holidaydatesmodel->SaveorUpdateHolidayDates($data, $where);	
				$tableid = $Id; 	
				$menuID = HOLIDAYDATES;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
			}
			
			 $hrWizardModel = new Default_Model_Hrwizard();
			 $hrWizardData = $hrWizardModel->getHrwizardData();
			
			$hrwizardarray = array('holidays' => 2,
                  				   'modifiedby'=>$loginUserId,
                  				    'modifieddate'=>gmdate("Y-m-d H:i:s")
                  				   );
			 if($hrWizardData['leavetypes'] == 2 && $hrWizardData['perf_appraisal'] == 2)
			 {
			 	$hrwizardarray['iscomplete'] = 2;
			 }                  					
               		$hrWizardModel->SaveorUpdateHrWizardData($hrwizardarray,'');
				
				$this->_helper->getHelper("FlashMessenger")->addMessage("Holidays added successfully.");
				$this->_redirect('hrwizard/configureholidays');	
          }
          catch(Exception $e)
          {
             $msgarray['category_id'] = "Something went wrong, please try again.";
             return $msgarray;
          }
		}else
		{
			return $msgarray;	
		}
    }
    
	public function saveholidaygroupAction()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('saveholidaygroup', 'json')->initContext();
		$this->_helper->layout->disableLayout();
	  	$auth = Zend_Auth::getInstance();
		$date = new Zend_Date();
     	if($auth->hasIdentity()){
				 $loginUserId = $auth->getStorage()->read()->id;
				 $loginuserRole = $auth->getStorage()->read()->emprole;
				 $loginuserGroup = $auth->getStorage()->read()->group_id;
		} 
		$result['result'] = '';
		$result['id'] = '';
		$result['groupname'] = '';
		$result['description'] = '';
		$holidaygroupsmodel = new Default_Model_Holidaygroups();
            try{
            $holidaygroup = trim($this->_request->getParam('holidaygroup'));
            $description = $this->_request->getParam('description');
            $isGroupExist = $holidaygroupsmodel->checkDuplicateGroupName($holidaygroup);
            if(!empty($isGroupExist))
			{
				if($isGroupExist[0]['count'] > 0)
				{
					$result['msg'] = 'Group name already exists.';
		            $result['id'] = '';
					$result['groupname'] = '';
					$result['address'] = '';
				}
				else 
				{
				$actionflag = '';
				$tableid  = ''; 
			   	$data = array(	 'groupname'=>$holidaygroup, 
								 'description'=>($description!=''?trim($description):NULL),
			   					 'createdby'=>$loginUserId,
							     'createddate'=>$date->get('yyyy-MM-dd HH:mm:ss'),
								 'modifiedby'=>$loginUserId,
								 'modifieddate'=>gmdate("Y-m-d H:i:s")
						);
				
					$where = '';
					$actionflag = 1;
					$Id = $holidaygroupsmodel->SaveorUpdateGroupData($data, $where);
					if($Id)
					{
					
						$menuID = HOLIDAYGROUPS;
						$logresult = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$Id);
						
						$result['msg'] = 'success';
						$result['id'] = $Id;
						$result['groupname'] = $holidaygroup;
						$result['description'] = $description;
					}else
					{
						$result['msg'] = 'error';
			            $result['id'] = '';
						$result['groupname'] = '';
						$result['description'] = '';
					}
				}
            }	
           }
        catch(Exception $e)
          {
             $result['msg'] = $e->getMessage();
             $result['id'] = '';
			 $result['groupname'] = '';
			 $result['description'] = '';
          }
          
          $this->_helper->json($result);
	
	}
	
	public function configureperformanceappraisalAction()
    {
    	$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
        }
        $hrWizardModel = new Default_Model_Hrwizard();
       	$appraisalCategoryModel = new Default_Model_Appraisalcategory();
        $hrWizardData = $hrWizardModel->getHrwizardData();
        $msgarray = array();
        
        $appraisalCategoriesData = $appraisalCategoryModel->getAppraisalCategorysData();
        if(empty($appraisalCategoriesData)) {
        	$msgarray['category_name_error'] = 'Please configure appraisal parameters.';
        }
        if($this->getRequest()->getPost()){
      		$result = $this->savequestions($hrWizardData);	
		    $this->view->msgarray = $result; 
		}
        $this->view->appraisalCategoriesData = $appraisalCategoriesData;
        $this->view->ermsg = '';	
        $this->view->hrWizardData = $hrWizardData;
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
    }
    
	public function savequestions($hrWizardData) {
    $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
				 $loginUserId = $auth->getStorage()->read()->id;
				 $loginuserRole = $auth->getStorage()->read()->emprole;
				 $loginuserGroup = $auth->getStorage()->read()->group_id;
		} 
	    $appraisalquestionsmodel = new Default_Model_Appraisalquestions();	
		$msgarray = array();
		$errorflag = 'true';
		$categoryNameId = $this->_request->getParam('category_name_id');
		$questionarr = $this->_request->getParam('question');
		$descriptionarr = $this->_request->getParam('description');
		if(!empty($questionarr))
		{
			for($i=0;$i<sizeof($questionarr);$i++)
			{
				if($questionarr[$i] == '') {
					$msgarray['question_name'][$i] = 'Please enter question.';
					$errorflag = 'false';
				} else if(!preg_match('/^[a-zA-Z0-9.\- ?\',\/#@$&*()!]+$/', $questionarr[$i])) {
					$msgarray['question_name'][$i] = 'Please enter valid holiday.';
					$errorflag = 'false';
				}
				
				if($categoryNameId == '') {
						$msgarray['category_name_error'] = 'Please select category name.';
						$errorflag = 'false';
				}
			}
			$msgarray['categoryerrorsize'] = sizeof($questionarr);
		}	
		
		  if($errorflag == 'true'){
            try{
			$actionflag = 1;
			$tableid  = ''; 
			$where = '';
			$date = new Zend_Date();
			for($i=0;$i<sizeof($questionarr);$i++)
			{
			   $data = array('pa_category_id'=>$categoryNameId,
			                 'question'=>trim($questionarr[$i]), 
							 'description'=>($descriptionarr[$i]!=''?trim($descriptionarr[$i]):NULL),
							  'modifiedby'=>$loginUserId,
							  'modifieddate'=>gmdate("Y-m-d H:i:s"),
			   				  'modifiedby_role'=>$loginuserRole,
							  'modifiedby_group'=>$loginuserGroup,
			  				  'createdby_role'=> $loginuserRole,
							  'createdby_group'=> $loginuserGroup,
			   				  'createdby' => $loginUserId,
							  'module_flag'=>1, 
							  'createddate'=> gmdate("Y-m-d H:i:s"),
							  'isactive'=> 1
					);
				
				$Id = $appraisalquestionsmodel->SaveorUpdateAppraisalQuestionData($data, $where);
				$tableid = $Id; 	
				$menuID = APPRAISALQUESTIONS;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
			}
			
			 $hrWizardModel = new Default_Model_Hrwizard();
			
			$hrwizardarray = array('perf_appraisal' => 2,
                  				   'modifiedby'=>$loginUserId,
                  				    'modifieddate'=>gmdate("Y-m-d H:i:s")
                  				   );
			 if($hrWizardData['leavetypes'] == 2 && $hrWizardData['holidays'] == 2)
			 {
			 	$hrwizardarray['iscomplete'] = 2;
			 }                  					
               		$hrWizardModel->SaveorUpdateHrWizardData($hrwizardarray,'');
				
				$this->_helper->getHelper("FlashMessenger")->addMessage("Questions added successfully.");
				$this->_redirect('hrwizard/configureperformanceappraisal');	
          }
          catch(Exception $e)
          {
             $msgarray['category_name_error'] = "Something went wrong, please try again.";
             return $msgarray;
          }
		}else
		{
			return $msgarray;	
		}
    }
    
public function savecategoryAction()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('savecategory', 'json')->initContext();
		$this->_helper->layout->disableLayout();
	  	$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
				 $loginUserId = $auth->getStorage()->read()->id;
				 $loginuserRole = $auth->getStorage()->read()->emprole;
				 $loginuserGroup = $auth->getStorage()->read()->group_id;
		} 
		$result['result'] = '';
		$result['id'] = '';
		$result['category_name'] = '';
		$result['description'] = '';
		$appraisalCategoryModel = new Default_Model_Appraisalcategory();
            try{
            $categoryName = trim($this->_request->getParam('category_name'));
            $description = $this->_request->getParam('description');
            $isParameterExist = $appraisalCategoryModel->checkDuplicateParameterName($categoryName);
            if(!empty($isParameterExist))
            {
            	if($isParameterExist[0]['count'] > 0)
            	{
            		$result['msg'] = 'Parameter name already exists.';
            		$result['id'] = '';
            		$result['category_name'] = '';
            		$result['description'] = '';
            	}
                else 
                {
				$actionflag = '';
				$tableid  = ''; 
			   	$data = array(	 'category_name'=>$categoryName, 
								 'description'=>($description!=''?trim($description):NULL),
			   					 'createdby'=>$loginUserId,
							     'createddate'=>gmdate("Y-m-d H:i:s"),
								 'modifiedby'=>$loginUserId,
								 'modifieddate'=>gmdate("Y-m-d H:i:s")
						);
				
					$where = '';
					$actionflag = 1;
					$Id = $appraisalCategoryModel->SaveorUpdateAppraisalCategoryData($data, $where);
					if($Id)
					{
					
						$menuID = APPRAISALCATEGORIES;
						$logresult = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$Id);
						
						$result['msg'] = 'success';
						$result['id'] = $Id;
						$result['category_name'] = $categoryName;
						$result['description'] = $description;
					}else
					{
						$result['msg'] = 'error';
			            $result['id'] = '';
						$result['groupname'] = '';
						$result['description'] = '';
					}
                }
            }
           }
        catch(Exception $e)
          {
             $result['msg'] = $e->getMessage();
             $result['id'] = '';
			 $result['groupname'] = '';
			 $result['description'] = '';
          }
          
          $this->_helper->json($result);
	
	}
	
    
    public function updatewizardcompletionAction()
    {
    	$this->_helper->layout->disableLayout();
    	$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$result['result'] = '';
    	$hrWizardModel = new Default_Model_Hrwizard();
    	$db = Zend_Db_Table::getDefaultAdapter();	
		$db->beginTransaction();
		try
		{
	    	$Completion_data = array( 'iscomplete'=>0,
	                 				'modifiedby'=>$loginUserId,
									'modifieddate'=>gmdate("Y-m-d H:i:s"),
						);
			$CompleteId = $hrWizardModel->SaveorUpdateHrWizardData($Completion_data, '');
			$db->commit();
			$result['result'] = 'success';
		}
		catch(Exception $e)
		{	
			$db->rollBack();
			$result['result'] = 'fail';
		}
		$this->_helper->json($result);			
    }
    
	
	
	
}

