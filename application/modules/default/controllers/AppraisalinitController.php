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

class Default_AppraisalinitController extends Zend_Controller_Action
{
    private $options;
    public function preDispatch()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('savegroupedemployees', 'json')->initContext();
        $ajaxContext->addActionContext('getperiod', 'json')->initContext();
    }
	
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
    }

    public function confmanagersAction()
    {
        $init_param = $this->_getParam('i',null);
        $init_id = sapp_Global::_decrypt($init_param);
        $this->render('configuremanagers');
    }
    public function getperiodAction()
    {
        $from_year = $this->_getParam('from_year',null);
        $to_year = $this->_getParam('to_year',null);
        $bunit = $this->_getParam('bunit',null);
        $mode = $this->_getParam('mode',null);
        $app_init_model = new Default_Model_Appraisalinit();
        $period = $app_init_model->getperiod($bunit,$from_year,$to_year,$mode);
        
        $this->_helper->json(array('status' =>'success','val' => $period));
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
		$this->render('commongrid/index', null, true);		
    }
	
    public function addAction()
    {
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $businessunit_id = $auth->getStorage()->read()->businessunit_id;
            $department_id = $auth->getStorage()->read()->department_id;            
        }
        $callval = $this->getRequest()->getParam('call');
        if($callval == 'ajaxcall')
            $this->_helper->layout->disableLayout();

        $errorMsg = '';
        $empSummaryModel = new Default_Model_Employee();
        $empData = $empSummaryModel->getEmp_from_summary($loginUserId);
		
        $appImpleData = sapp_PerformanceHelper::check_per_implmentation($businessunit_id, $department_id);
        //echo "<pre>";print_r($appImpleData);echo "</pre>";
        $appInitModel = new Default_Model_Appraisalinit();
		
        $appraisalInitForm = new Default_Form_Appraisalinit();
        if(count($appImpleData) > 0)
        {
            $this->view->imple_data = $appImpleData;
            $checkActiveApp = $appInitModel->checkAppraisalExists($businessunit_id, $department_id);
            if(count($checkActiveApp) == 0)
            {
                $appraisalInitForm->businessunit_name->setValue($empData['businessunit_name']);
                if($appImpleData['performance_app_flag'] == 0)
                    $appraisalInitForm->department_name->setValue($empData['department_name']);
                else 
                {
                    $appraisalInitForm->removeElement("department_name");
                }
                $appraisalInitForm->businessunit_id->setValue($empData['businessunit_id']);
                $appraisalInitForm->department_id->setValue($empData['department_id']);
                $appraisalInitForm->appraisal_mode->setValue($appImpleData['appraisal_mode']);
				
                $employmentstatusModel = new Default_Model_Employmentstatus();
                $employmentStatusData = $employmentstatusModel->getempstatusActivelist();
                if(!empty($employmentStatusData))
                {
                    foreach ($employmentStatusData as $employmentStatusres)
                    {
                        $appraisalInitForm->eligibility->addMultiOption($employmentStatusres['workcodename'],$employmentStatusres['statusname']);
                    }
                }
                else 
                {
                    $msgarray['eligibility'] = 'Employment status is not configured yet.';
                }
            } 
            else 
            {
                $errorMsg = 'Appraisal process is already initialized.';
            }
        } 
        else 
        {
            $errorMsg = 'Appraisal process is not yet configured.';
        }
		
        $msgarray = array();
        $appraisalInitForm->setAttrib('action',DOMAIN.'appraisalinit/add');
        $this->view->form = $appraisalInitForm; 
        $this->view->msgarray = $msgarray; 
        $this->view->ermsg = $errorMsg;
		
        if($this->getRequest()->getPost())
        {
            $result = $this->save($appraisalInitForm);	
            $this->view->msgarray = $result; 
        }
        $this->render('form');	
    }//end of add action

    public function save($appraisalInitForm)
    {
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
        } 
        $appraisalInitModel = new Default_Model_Appraisalinit();
        $msgarray = array();
						
        $enable_step = $this->_request->getParam('enable_step');
						
        if($appraisalInitForm->isValid($this->_request->getPost()) && count($msgarray) == 0)
        {
            try
            {
                $id = $this->_request->getParam('id');
                $businessunit_id = $this->_request->getParam('businessunit_id');	
                $pa_configured_id = $this->_request->getParam('configuration_id');	
                $department_id = $this->_request->getParam('department_id');
                $appraisal_mode = $this->_request->getParam('appraisal_mode');
                $appraisal_period = $this->_request->getParam('appraisal_period');
                $from_year = $this->_request->getParam('from_year');
                $to_year = $this->_request->getParam('to_year');
                $eligibility = $this->_request->getParam('eligibility');
                $status = $this->_request->getParam('status');
				
                if(count($eligibility)>0)
                    $eligibility = implode(',', $eligibility);
                else
                    $eligibility = '';
				
				$menumodel = new Default_Model_Menu();
				$actionflag = '';
				$tableid  = ''; 
			   	$data = array('businessunit_id'=>$businessunit_id,
                                            'pa_configured_id' => $pa_configured_id,
                                            'department_id'=>$department_id, 
                                            'enable_step'=>$enable_step,
                                            'appraisal_mode'=>$appraisal_mode,
                                            'appraisal_period'=>$appraisal_period,
                                            'from_year' => $from_year,
                                            'to_year' => $to_year,
                                            'eligibility'=>$eligibility,							   								   	
                                            'status'=>$status,
                                            'modifiedby'=>$loginUserId,
                                            'modifiedby_role'=>$loginuserRole,
                                            'modifiedby_group'=>$loginuserGroup,
                                            'modifieddate'=>gmdate("Y-m-d H:i:s")
					);									
					
				if($id!=''){
					$where = array('id=?'=>$id);  
					$actionflag = 2;
				}
				else
				{					
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
				   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Initialization updated successfully."));
				}   
				else
				{
				   $tableid = $Id; 	
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Initialization added successfully."));					   
				}   
				$menuidArr = $menumodel->getMenuObjID('/appraisalinit');
				$menuID = $menuidArr[0]['id'];
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
                                
				$this->_redirect('appraisalinit/confmanagers/i/'.sapp_Global::_encrypt($tableid));	
			}
        	catch(Exception $e)
          	{	
            	$msgarray['businessunit_name'] = "Something went wrong, please try again.";
             	return $msgarray;
          	}
		} else {
			$messages = $appraisalInitForm->getMessages();
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
	
    public function viewAction()
	{
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		$objName = 'appraisalinit';
		$appraisalinitform = new Default_Form_Appraisalinit();
		$appraisalinitform->removeElement("submit");
		$elements = $appraisalinitform->getElements();
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)
			{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
					$element->setAttrib("disabled", "disabled");
				}
        	}
        }
		try
		{
		    if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					$appraisalinitmodel = new Default_Model_Appraisalinit();
					$data = $appraisalinitmodel->getConfigData($id);
					if(!empty($data))
					{
						$empSummaryModel = new Default_Model_Employee();
						$empData = $empSummaryModel->getEmp_from_summary($loginUserId);
						$appraisalinitform->businessunit_name->setValue($empData['businessunit_name']);
						$appraisalinitform->department_name->setValue($empData['department_name']);
						
						$employmentstatusModel = new Default_Model_Employmentstatus();
						$employmentStatusData = $employmentstatusModel->getempstatusActivelist();
						if(!empty($employmentStatusData))
						{
							foreach ($employmentStatusData as $employmentStatusres){
								$appraisalinitform->eligibility->addMultiOption($employmentStatusres['workcodename'],$employmentStatusres['statusname']);
							}
						}
						
						$data = $data[0];
						$appraisalinitform->populate($data); 
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
		$this->view->eligibility_value = $data['eligibility']; 
		$this->view->controllername = $objName;
		$this->view->id = $id;
		$this->view->form = $appraisalinitform;
		$this->render('form');	
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
		
		$appraisalinitform = new Default_Form_Appraisalinit();
		$appraisalinitform->submit->setLabel('Update');
		try
        {		
			if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					$appraisalinitmodel = new Default_Model_Appraisalinit();
					$data = $appraisalinitmodel->getConfigData($id);
					if(!empty($data))
					{
						$empSummaryModel = new Default_Model_Employee();
						$empData = $empSummaryModel->getEmp_from_summary($loginUserId);
						$appraisalinitform->businessunit_name->setValue($empData['businessunit_name']);
						$appraisalinitform->department_name->setValue($empData['department_name']);
						
						$employmentstatusModel = new Default_Model_Employmentstatus();
						$employmentStatusData = $employmentstatusModel->getempstatusActivelist();
						if(!empty($employmentStatusData))
						{
							foreach ($employmentStatusData as $employmentStatusres){
								$appraisalinitform->eligibility->addMultiOption($employmentStatusres['workcodename'],$employmentStatusres['statusname']);
							}
						}
						
						$data = $data[0];
						$appraisalinitform->populate($data);
						$appraisalinitform->setAttrib('action',DOMAIN.'appraisalinit/edit/id/'.$id);
                        $this->view->data = $data;
					} else {
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
		$this->view->eligibility_value = $data['eligibility'];	
		$this->view->form = $appraisalinitform;
		if($this->getRequest()->getPost()){
      		$result = $this->save($appraisalinitform);	
		    $this->view->msgarray = $result; 
		}
		$this->render('form');	
	}
	
	public function assigngroupsAction()
	{
		$appraisalinitmodel = new Default_Model_Appraisalgroupemployees();
		$appraisalGroupsModel = new Default_Model_Appraisalgroups();
		$id = $this->getRequest()->getParam('id');
		$id=1;
		$employeeIds = '';
		$groupIds = '';
		$options = '';
		try
		{
		    if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					$data = $appraisalinitmodel->getConfigData($id);
					if(!empty($data))
					{
						$data = $data[0];
						$param = 'main_pa_groups_employees_temp'; 
						$mappedEmployeeIds =  $appraisalinitmodel->getMappedEmployeeList($id,$param);
						//echo '<pre>';print_r($mappedEmployeeIds);exit;
						if(!empty($mappedEmployeeIds))
						{
							foreach($mappedEmployeeIds as $list)
								{
									$employeeIds .=$list['employee_ids'].',';
									$groupIds .=$list['group_id'].','; 
								}
								$employeeIds = rtrim($employeeIds,',');
								$groupIds = rtrim($groupIds,',');
								if($groupIds !='')
									$appraisalgroupName = $appraisalGroupsModel->getAppraisalGroupsName($groupIds); 		
						}
						$employeeList = $appraisalinitmodel->getEmployeeList($data,$employeeIds,1);
						//$mappedemployeeList = $appraisalinitmodel->getEmployeeListWithGroup($data,$employeeIds,$groupIds);
						//echo '<pre>';print_r($appraisalgroupName);exit;
						$appraisalGroups = $appraisalGroupsModel->getAppraisalGroupsData();
						$options = "<option value='' title=''>Select Group</opton>";
						if(!empty($appraisalGroups))
						{
							foreach($appraisalGroups as $groups)
							{
								$options.="<option value =".$groups['id'].">".$groups['group_name']."</option>";
							}
						}
						
						$this->view->employeeList = $employeeList;
						$this->view->appraisalgroupName = $appraisalgroupName;
						$this->view->options = $options;
						$this->view->appraisalid = $id;
						if($this->getRequest()->getPost()){
							 $result = $this->savegroupedemployees();	
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
	
	public function getgroupedemployeesAction()
	{		
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('getgroupedemployees', 'html')->initContext();
		$employeeIds = '';
		$groupedemployeeIds = '';
		$groupedemployeeList = array();
		
		$groupid = $this->_request->getParam('groupid');
		$appraisalid = $this->_request->getParam('appraisalid');
		
		$appraisalinitmodel = new Default_Model_Appraisalgroupemployees();
		$appraisalGroupsModel = new Default_Model_Appraisalgroups();
		
		if($groupid && $appraisalid)
		{
			$appraisaldata = $appraisalinitmodel->getConfigData($appraisalid);
			$appraisaldata = $appraisaldata[0];
			$tablename = 'main_pa_groups_employees_temp';
			/** Start
			 * Fetching the employee ids who are being assigned for initialization.
			 * Subsequently fetching details of available employee who have not been assigned
			 * to any appraisal for display.
			 */
		
				$initializedEmployeeIds =  $appraisalinitmodel->getGrouppedEmployeeList($appraisalid,'',$tablename);
				 
					if(!empty($initializedEmployeeIds))
							{
								foreach($initializedEmployeeIds as $list)
									{
										$employeeIds .=$list['employee_ids'].','; 
									}
									$employeeIds = rtrim($employeeIds,',');
							}
							
		   		$employeeList = $appraisalinitmodel->getEmployeeList($appraisaldata,$employeeIds,1);
		   		//echo '<pre>';print_r($employeeList);exit;
		   
			/**
			 * End
			 */ 
		   
		 /** Start
		  * Fetching the employee ids of employees who have been assigned to any group.
		  * Subsequently fetching the details to display.
		  */  
		  	$groupedEmployeeIds =  $appraisalinitmodel->getGrouppedEmployeeList($appraisalid,$groupid,$tablename);
		  	//echo '<pre>';print_r($groupedEmployeeIds);exit;
			if(!empty($groupedEmployeeIds))
							{
								foreach($groupedEmployeeIds as $list)
									{
										$groupedemployeeIds .=$list['employee_ids'].','; 
									}
									$groupedemployeeIds = rtrim($groupedemployeeIds,',');
									
									$groupedemployeeList = $appraisalinitmodel->getEmployeeList($appraisaldata,$groupedemployeeIds,2);
							}
		   
		   
		   /**
		    * End
		    */
		    $this->view->employeeList = $employeeList;
			$this->view->groupedemployeeList = $groupedemployeeList;
		}	

	}
	
	public function getempdetailsAction()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('getempdetails', 'html')->initContext();
		$appraisalinitmodel = new Default_Model_Appraisalgroupemployees();
		$groupid = $this->_request->getParam('groupid');
		$appraisalid = $this->_request->getParam('appraisalid');
		$tablename = 'main_pa_groups_employees_temp';
		if($groupid && $appraisalid)
		{
			$groupedemployeeList = $appraisalinitmodel->getGrouppedEmployeeList($appraisalid,$groupid,$tablename);
			echo '<pre>';print_r($groupedemployeeList);exit;
		}	
	}
	
	public function savegroupedemployees()
	{
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;
					$loginuserGroup = $auth->getStorage()->read()->group_id;
		} 
		$appraisalinitmodel = new Default_Model_Appraisalgroupemployeestemp();
		$menumodel = new Default_Model_Menu();
		$groupid = $this->_request->getParam('group_id');
		$appraisalid = $this->_request->getParam('appraisalid');
		$empids = $this->_request->getParam('empids');
		$id = '';
		$actionflag = '';
		$tableid  = '';
		$msgarray = array();
		if(!isset($groupid) && $groupid =='')
			$groupid = 0;

		try
		{
			$ifrecordexists = $appraisalinitmodel->checkAppraisalRecordexists($groupid,$appraisalid);
			
			if(!empty($ifrecordexists))
				$id = $ifrecordexists[0]['id'];
		
			
			   $insertdata = array('pa_initialization_id'=>$appraisalid, 
							    'group_id'=>$groupid,
							    'employee_ids'=>$empids,
							    'isactive'=>1,
			   					'createdby'=>$loginUserId,
			   					'createdby_role'=>$loginuserRole,
							    'createdby_group'=>$loginuserGroup,
			   					'modifiedby'=>$loginUserId,
			   					'modifiedby_role'=>$loginuserRole,
							    'modifiedby_group'=>$loginuserGroup,
			   					'createddate'=>gmdate("Y-m-d H:i:s"),
							    'modifieddate'=>gmdate("Y-m-d H:i:s")
					);
					
				$updatedata = array('employee_ids'=>$empids,
								'modifiedby'=>$loginUserId,
			   					'modifiedby_role'=>$loginuserRole,
							    'modifiedby_group'=>$loginuserGroup,
								'modifieddate'=>gmdate("Y-m-d H:i:s")
				);	
				if($id!=''){
					$where = array('id=?'=>$id);  
					$actionflag = 2;
					$tableid = $id;
					$appraisalinitmodel->SaveorUpdateAppraisalGroupsEmployeesTempData($updatedata, $where);
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Employees updated successfully."));
				}
				else
				{
					$where = '';
					$actionflag = 1;
					$Id = $appraisalinitmodel->SaveorUpdateAppraisalGroupsEmployeesTempData($insertdata, $where);
					$tableid = $Id;
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Employees added successfully."));
				}
				 
				$menuidArr = $menumodel->getMenuObjID('/appraisalinit');
				$menuID = $menuidArr[0]['id'];
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
				$this->_redirect('appraisalinit/assigngroups/id/'.$appraisalid);
		}
		catch(Exception $e)
          {
            	$msgarray = $this->_helper->getHelper("FlashMessenger")->addMessage(array("error"=>$e->getMessage()));
          }		
				return $msgarray;
	}
	

	
}

