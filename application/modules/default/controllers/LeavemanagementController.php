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

class Default_LeavemanagementController extends Zend_Controller_Action
{

    private $options;
    public function preDispatch()
    {
		 
		
    }
	
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('gethremployees', 'json')->initContext();
		
    }

    public function indexAction()
    {
		$leavemanagementmodel = new Default_Model_Leavemanagement();
        
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
			$sort = 'DESC';$by = 'l.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else 
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'l.modifieddate';
			if($dashboardcall == 'Yes')
				$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
			else 
			    $perPage = $this->_getParam('per_page',PERPAGE);
			$pageNo = $this->_getParam('page', 1);
			// search from grid - START 
			$searchData = $this->_getParam('searchData');	
			$searchData = rtrim($searchData,',');
			// search from grid - END 
		}
		$dataTmp = $leavemanagementmodel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);     			
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
    }
	
	 public function addAction()
	{
	   $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$department_id = $auth->getStorage()->read()->department_id;
		}
		
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
			
		$leavemanagementform = new Default_Form_leavemanagement();
		$leavemanagementmodel = new Default_Model_Leavemanagement();
		$requi_model = new Default_Model_Requisition();  
		$monthslistmodel = new Default_Model_Monthslist();
		$weekdaysmodel = new Default_Model_Weekdays();
		$busineesUnitModel = new Default_Model_Businessunits();
		$departmentsmodel = new Default_Model_Departments();
		$msgarray = array();
		$bu_msg = ''; 
		$months_msg = ''; 
		$days_msg = '';
		
		    $businessunitData = $busineesUnitModel->getDeparmentList(); //getDeparmentList --- gets the business units			
		   if(sizeof($businessunitData) > 0)
            { 			
			        $leavemanagementform->businessunit->addMultiOption('0','No Business Unit');
				foreach ($businessunitData as $businessunitres){
				    $leavemanagementform->businessunit->addMultiOption($businessunitres['id'],$businessunitres['unitname']);
				}
				$department_ids = $leavemanagementmodel->getActiveDepartmentIds();
				$deptstr ='';
				if(!empty($department_ids))
				{
					foreach($department_ids as $depid)
					{
						$deptstr.= $depid['deptid'].",";
					}
					$deptstr = rtrim($deptstr,',');
				}else
                {
				    $msgarray['department_id'] = 'Departments are not added yet.';
                } 				
				
				if(isset($_POST['businessunit']) && $_POST['businessunit']!='')
				{
				    $departments_list = $departmentsmodel->getUniqueDepartmentList($deptstr,$_POST['businessunit']);	
					foreach ($departments_list as $departmentsres){
						$leavemanagementform->department_id->addMultiOption($departmentsres['id'],utf8_encode($departmentsres['deptname']));
					}
				}else{
				     $departments_list = $departmentsmodel->getUniqueDepartmentList($deptstr,'0');
					foreach ($departments_list as $departmentsres){
						$leavemanagementform->department_id->addMultiOption($departmentsres['id'],utf8_encode($departmentsres['deptname']));
					}
				}

			}
			else
			{
				$msgarray['businessunit'] = 'Business units are not added yet.';
                                $msgarray['department_id'] = 'Departments are not added yet.';
				$bu_msg = 'no bu';
			} 
				
			/*  if(isset($_POST['hrmanager']) && $_POST['hrmanager']!='')
			{ */
				if(isset($_POST['businessunit']) && $_POST['businessunit']!='')
				{
					$hremployees=$leavemanagementmodel->getBusinessunitHrEmployees($_POST['businessunit']);	
					foreach ($hremployees as $employees)
					{
						$leavemanagementform->hrmanager->addMultiOption($employees['user_id'], $employees['userfullname']);
					}
					$leavemanagementform->setDefault('hrmanager',$_POST['hrmanager']);	
				}
			//} 
		
			$monthslistdata = $monthslistmodel->getMonthlistData();
				if(sizeof($monthslistdata) > 0)
				{
					foreach ($monthslistdata as $monthslistres){
						$leavemanagementform->cal_startmonth->addMultiOption($monthslistres['month_id'],utf8_encode($monthslistres['month_name']));
					}
				}else
				{
                    $msgarray['cal_startmonth'] = 'Months list is not configured yet.';					
					$months_msg = 'no months';
				}
			
            $weekdaysdata = $weekdaysmodel->getWeeklistData();
			
				if(sizeof($weekdaysdata) > 0)
				{
					foreach ($weekdaysdata as $weekdaysres){
						$leavemanagementform->weekend_startday->addMultiOption($weekdaysres['day_id'],utf8_encode($weekdaysres['day_name']));
						$leavemanagementform->weekend_endday->addMultiOption($weekdaysres['day_id'],utf8_encode($weekdaysres['day_name']));
				    }
				}else
				{
					$msgarray['weekend_startday'] = 'Weekdays are not configured yet.';
					$msgarray['weekend_endday'] = 'Weekdays are not configured yet.';
					$days_msg = 'no weeks';
				}			
		$leavemanagementform->setAttrib('action',BASE_URL.'leavemanagement/add');
        $this->view->form = $leavemanagementform; 	
		$this->view->bu_msg = $bu_msg; 	
		$this->view->months_msg = $months_msg; 	
		$this->view->days_msg = $days_msg; 
        $this->view->msgarray = $msgarray; 		
        if($this->getRequest()->getPost()){
		     $result = $this->save($leavemanagementform);	
		     $this->view->msgarray = $result; 
        }  		
		
	}

    public function viewAction()
	{	
	
	    $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;
					$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		$objName = 'leavemanagement';
		$leavemanagementform = new Default_Form_leavemanagement();
		$leavemanagementform->removeElement("submit");
		$elements = $leavemanagementform->getElements();
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)
			{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
				$element->setAttrib("disabled", "disabled");
					}
        	}
        }
		$permission = sapp_Global::_checkprivileges(LEAVEMANAGEMENT,$loginuserGroup,$loginuserRole,'edit');
					
		$this->view->editpermission = $permission;
			try
			{
				if($id)
				{
				    if(is_numeric($id) && $id>0)
				    {
						$leavemanagementmodel = new Default_Model_Leavemanagement();
						$data = $leavemanagementmodel->getActiveleavemanagementId($id);
						if(!empty($data))
							{
								$data = $data[0]; 
								$monthslistmodel = new Default_Model_Monthslist();
								$weekdaysmodel = new Default_Model_Weekdays();
								$departmentsmodel = new Default_Model_Departments();
								$busineesUnitModel = new Default_Model_Businessunits();
								$businessunitData = $busineesUnitModel->getParicularBusinessUnit($data['businessunit_id']);
								$particulardeptidArr = $departmentsmodel->getParicularDepartmentId($data['department_id']);	
								$monthslistdata = $monthslistmodel->getMonthlistData();
								$weekdaysdata = $weekdaysmodel->getWeeklistData();
								if(!empty($businessunitData) && !empty($particulardeptidArr) && !empty($monthslistdata) && !empty($weekdaysdata))
								{	
									if(!empty($businessunitData)){
									  $leavemanagementform->businessunit->addMultiOption($businessunitData[0]['id'],utf8_encode($businessunitData[0]['unitname']));
									 $data['businessunit_id']= $businessunitData[0]['unitname'];
									} 
									
									
									if(!empty($particulardeptidArr))
									{
										$leavemanagementform->department_id->addMultiOption($particulardeptidArr[0]['id'],utf8_encode($particulardeptidArr[0]['deptname']));		   
									    $data['department_id']=$particulardeptidArr[0]['deptname'];
									} 
								   
									
									if(sizeof($monthslistdata) > 0)
									{
										foreach ($monthslistdata as $monthslistres){
											$leavemanagementform->cal_startmonth->addMultiOption($monthslistres['month_id'],utf8_encode($monthslistres['month_name']));
											
										}
									}
									
									
									if(sizeof($weekdaysdata) > 0)
									{
										foreach ($weekdaysdata as $weekdaysres){
											$leavemanagementform->weekend_startday->addMultiOption($weekdaysres['day_id'],utf8_encode($weekdaysres['day_name']));
										     $leavemanagementform->weekend_endday->addMultiOption($weekdaysres['day_id'],utf8_encode($weekdaysres['day_name']));
										}
									}	
								
									$leavemanagementform->populate($data);
									$leavemanagementform->setDefault('cal_startmonth',$data['cal_startmonth']);
									$leavemanagementform->setDefault('weekend_startday',$data['weekend_startday']);
									$leavemanagementform->setDefault('weekend_endday',$data['weekend_endday']);
									$leavemanagementform->setDefault('businessunit',$data['businessunit_id']);
									$leavemanagementform->setDefault('department_id',$data['department_id']);								
									$this->view->rowexist = "";
								}
								else
								{
								   $this->view->rowexist = "rows";
								}
							
							}	
							else
							{
							   $this->view->rowexist = "norows";
							}
					}else
					{
					   $this->view->rowexist = "norows";
					}		
				}else{
				    $this->view->rowexist = "norows";
				}
			}
			catch(Exception $e)
			{
				  $this->view->rowexist = "norows";
			} 	
		
	      if(isset($data['weekend_startday'])) {
	               $startdayname= $weekdaysmodel->getParticularWeekDayName($data['weekend_startday']);
						if(!empty($startdayname)){
							$data['weekend_startday'] = $startdayname[0]['week_name'];
						}
					}
					if(isset($data['weekend_endday'])) {
						$enddayname= $weekdaysmodel->getParticularWeekDayName($data['weekend_endday']);
						if(!empty($enddayname)){
							$data['weekend_endday'] = $enddayname[0]['week_name'];
						}
					}
	                if(!empty($data['cal_startmonth'])) {
						$monthname= $monthslistmodel->getsingleMonthListData($data['cal_startmonth']);
						if(!empty($monthname)){
							$data['cal_startmonth'] = $monthname['description'];
						}
					}
					
					
	                if($data['is_halfday']==2)
					{
						$data['is_halfday']="no";
					}
					else 
					{
						$data['is_halfday']="yes";
					}
	                if($data['is_leavetransfer']==2)
					{
						$data['is_leavetransfer']="no";
					}
					else 
					{
						$data['is_leavetransfer']="yes";
					}
	                if($data['is_skipholidays']==2)
					{
						$data['is_skipholidays']="no";
					}
					else 
					{
						$data['is_skipholidays']="yes";
					}
					if(!empty($data['hr_id']) && isset($data['hr_id'])) {
						$employeeModel = new Default_Model_Employee();
						$empdata= $employeeModel->getIndividualEmpDetails($data['hr_id']);
						if(!empty($empdata)){
							$data['hr_id'] = $empdata['userfullname'];
						}
					}
					
			$this->view->controllername = $objName;
			$this->view->id = $id;
			$this->view->data = $data;
			$this->view->form = $leavemanagementform;
	}
	
	
	public function editAction()
	{	
	    $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		
		$leavemanagementform = new Default_Form_leavemanagement();
		$leavemanagementform->submit->setLabel('Update'); 
		$msgarray = array();
			try
			{
				if($id)
				{
				    if(is_numeric($id) && $id>0)
				    {
						$leavemanagementmodel = new Default_Model_Leavemanagement();
						$data = $leavemanagementmodel->getActiveleavemanagementId($id);
						if(!empty($data))
						   {
								$data = $data[0];
								$monthslistmodel = new Default_Model_Monthslist();
								$weekdaysmodel = new Default_Model_Weekdays();
								$busineesUnitModel = new Default_Model_Businessunits();
								$departmentsmodel = new Default_Model_Departments();
								$businessunitData = $busineesUnitModel->getParicularBusinessUnit($data['businessunit_id']);
								$particulardeptidArr = $departmentsmodel->getParicularDepartmentId($data['department_id']);	
								$monthslistdata = $monthslistmodel->getMonthlistData();
								$weekdaysdata = $weekdaysmodel->getWeeklistData();
								
								if(!empty($businessunitData) && !empty($particulardeptidArr))
									{					
										if(!empty($businessunitData))
										{
										 $leavemanagementform->businessunit->addMultiOption($businessunitData[0]['id'],$businessunitData[0]['unitname']);
										}  	
										
										
										if(!empty($particulardeptidArr))
										{
										  $leavemanagementform->department_id->addMultiOption($particulardeptidArr[0]['id'],utf8_encode($particulardeptidArr[0]['deptname']));
										} 						
										 
										
										if(sizeof($monthslistdata) > 0)
										{
											foreach ($monthslistdata as $monthslistres){
												$leavemanagementform->cal_startmonth->addMultiOption($monthslistres['month_id'],utf8_encode($monthslistres['month_name']));
											}
										}
										
										
										if(sizeof($weekdaysdata) > 0)
										{
											foreach ($weekdaysdata as $weekdaysres){
												$leavemanagementform->weekend_startday->addMultiOption($weekdaysres['day_id'],utf8_encode($weekdaysres['day_name']));
												$leavemanagementform->weekend_endday->addMultiOption($weekdaysres['day_id'],utf8_encode($weekdaysres['day_name']));
											}
										}
										$leavemanagementform->populate($data);
										$leavemanagementform->setDefault('cal_startmonth',$data['cal_startmonth']);
										$leavemanagementform->setDefault('weekend_startday',$data['weekend_startday']);
										$leavemanagementform->setDefault('weekend_endday',$data['weekend_endday']);
										$leavemanagementform->setDefault('businessunit',$data['businessunit_id']);
										$leavemanagementform->setDefault('department_id',$data['department_id']);
										
										$leavemanagementform->setAttrib('action',BASE_URL.'leavemanagement/edit/id/'.$id);
										$this->view->form = $leavemanagementform;
										$this->view->rowexist = "";
									}
									else
									{
									   $this->view->rowexist = "rows";
									}
								if($data['businessunit_id'] != '')
								{
									//hrmanagers dropdown
									$hremployees=$leavemanagementmodel->getBusinessunitHrEmployees($data['businessunit_id']);	

									if(!empty($hremployees))
									{
										foreach ($hremployees as $employees)
										{
											$leavemanagementform->hrmanager->addMultiOption($employees['user_id'], $employees['userfullname']);
										}	
										$leavemanagementform->setDefault('hrmanager',$data['hr_id']);
									}
									else
									{

										$msgarray['hrmanager']  = 'Hr manager not added yet for the selected department.';
									} 
								}
						        	
							}
							else
							{
							   $this->view->rowexist = "norows";
							} 
					}else
					{
					   $this->view->rowexist = "norows";
					}	
				}
				else
				{
				   $this->view->rowexist = "norows";
				}
			}
			catch(Exception $e)
			{
				  $this->view->rowexist = "norows";
			}  		
		$this->view->msgarray = $msgarray; 		
			if($this->getRequest()->getPost()){
				$result = $this->save($leavemanagementform);	
				$this->view->msgarray = $result; 
			}
	}
	
	public function save($leavemanagementform)
	{
	  $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		}
		$businessunit_id = $this->_request->getParam('businessunit');
		$department_id = $this->_request->getParam('department_id');
		if(isset($businessunit_id) && $businessunit_id != 0 && $businessunit_id != '')
		{
		    $departmentsmodel = new Default_Model_Departments();
			$departmentlistArr = $departmentsmodel->getDepartmentList($businessunit_id);
			$leavemanagementform->department_id->addMultiOption('','Select Department');
			foreach($departmentlistArr as $departmentlistresult)
			{
			  $leavemanagementform->department_id->addMultiOption($departmentlistresult['id'],utf8_encode($departmentlistresult['deptname']));
			}  
			if(isset($department_id) && $department_id != 0 && $department_id != '')
				$leavemanagementform->setDefault('department_id',$department_id);			
		}
		
			if($leavemanagementform->isValid($this->_request->getPost())){
			    $id = $this->_request->getParam('id');
			    $cal_startmonth = $this->_request->getParam('cal_startmonth');
				$weekend_startday = $this->_request->getParam('weekend_startday');
				$weekend_endday = $this->_request->getParam('weekend_endday');
				
				$hours_day = $this->_request->getParam('hours_day');
				$is_halfday = $this->_request->getParam('is_halfday');
				$is_leavetransfer = $this->_request->getParam('is_leavetransfer');
				$is_skipholidays = $this->_request->getParam('is_skipholidays');
				$description = $this->_request->getParam('description');
				$hr_id= $this->_request->getParam('hrmanager');
				$leavemanagementmodel = new Default_Model_Leavemanagement();
				$date = new Zend_Date();
				$actionflag = '';
				$tableid  = ''; 
				   $data = array( 'cal_startmonth'=>$cal_startmonth,
				                 'weekend_startday'=>$weekend_startday,
								 'weekend_endday'=>$weekend_endday,
								 'businessunit_id'=>$businessunit_id,
								 'department_id'=>$department_id,
				                 'hours_day'=>$hours_day,
								 'is_halfday'=>$is_halfday,
								 'is_leavetransfer'=>$is_leavetransfer,
								 'is_skipholidays'=>$is_skipholidays,
								 'hr_id'=>$hr_id,
				      			 'description'=>$description,
								 'modifiedby'=>$loginUserId,
								 'modifieddate'=>gmdate("Y-m-d H:i:s")
						);
					if($id!=''){
						$where = array('id=?'=>$id);  
						$actionflag = 2;
					}
					else
					{
					    $data['createdby'] = $loginUserId;
						$data['createddate'] = gmdate("Y-m-d H:i:s");
						$data['isactive'] = 1;
						$where = '';
						$actionflag = 1;
					}
					$Id = $leavemanagementmodel->SaveorUpdateLeaveManagementData($data, $where);
					if($Id == 'update')
					{
					   $tableid = $id;
					   $this->_helper->getHelper("FlashMessenger")->addMessage("Leave management updated successfully.");
					}   
					else
					{
                       $tableid = $Id; 	
                        $this->_helper->getHelper("FlashMessenger")->addMessage("Leave management added successfully.");					   
					}   
					$menuID = LEAVEMANAGEMENT;
					$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
    			    $this->_redirect('leavemanagement');		
			}else
			{
			     $messages = $leavemanagementform->getMessages();
				foreach ($messages as $key => $val)
					{
						foreach($val as $key2 => $val2)
						 {
							$msgarray[$key] = $val2;
							break;
						 }
					}
				return $msgarray;	
			
			}
	
	}
	
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
		    if($id)
			{
			$leavemanagementmodel = new Default_Model_Leavemanagement();
			  $data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
			  $where = array('id=?'=>$id);
			  $Id = $leavemanagementmodel->SaveorUpdateLeaveManagementData($data, $where);
			    if($Id == 'update')
				{
				   $menuID = LEAVEMANAGEMENT;
				   $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id); 
				   $messages['message'] = 'Leave management deleted successfully.';
				   $messages['msgtype'] = 'success';
				}   
				else
				{
                   $messages['message'] = 'Leave management cannot be deleted.';
				   $messages['msgtype'] = 'error';
                } 				   
			}
			else
			{ 
			 $messages['message'] = 'Leave management cannot be deleted.';
			 $messages['msgtype'] = 'succerroress';
			}
			// delete success message after delete in view
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
	
	
	public function getuniqueDepartment($businessunit_id)
	{
	 
		   $leavemanagementmodel = new Default_Model_Leavemanagement();
		   $departmentidsArr = $leavemanagementmodel->getActiveDepartmentIds();
		   $departmentsmodel = new Default_Model_Departments();
			$depatrmentidstr = '';
			$newarr = array();
			if(!empty($departmentidsArr))
				{
					$where = '';
					for($i=0;$i<sizeof($departmentidsArr);$i++)
					{
						$newarr1[] = array_push($newarr, $departmentidsArr[$i]['deptid']);
						
					}
					$depatrmentidstr = implode(",",$newarr);
					foreach($newarr as $deparr)
					{
					$where.= " id!= $deparr AND ";
					}
					$where = trim($where," AND");
					$querystring = "Select d.id,d.deptname from main_departments as d where d.unitid=$businessunit_id and d.isactive=1 and $where  ";
												  
					$uniquedepartmentids = $departmentsmodel->getUniqueDepartments($querystring);
					return $uniquedepartmentids;
				}
			else
			    {
					$departmentlistArr = $departmentsmodel->getDepartmentList($businessunit_id);
					return $departmentlistArr; 
                }				
	}
}

