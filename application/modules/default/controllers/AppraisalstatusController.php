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

class Default_AppraisalstatusController extends Zend_Controller_Action
{

    private $options;
	public function preDispatch()
	{		 
		
	}
	
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
    }

    public function indexAction()
    {
    	
    }
    
	public function managerAction()
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
        
    	$errorMsg = '';
    	$msgarray = array();
    	$bunitdataArr = array();
    	$flag = '';
        $appInitModel = new Default_Model_Appraisalinit();
        if($loginuserRole != SUPERADMINROLE && $loginuserGroup != MANAGEMENT_GROUP && $loginuserGroup != HR_GROUP)
        {
            $appImpleData = sapp_PerformanceHelper::check_per_implmentation($businessunit_id, $department_id);
           // if(count($appImpleData) > 0)
            //{                
                $this->view->imple_data = $appImpleData;
                $checkActiveApp = $appInitModel->checkAppraisalExists($businessunit_id, $department_id); //,$appImpleData['performance_app_flag']
                if(count($checkActiveApp) > 0)
                {
                	$checkActiveApp = $checkActiveApp[0];
					if($checkActiveApp['enable_step'] == 1 && $checkActiveApp['status'] == 1)
					{
						$this->view->checkActiveApp = $checkActiveApp;
						$flag = 1;
					}
					else
					{
						if($checkActiveApp['enable_step'] == 2)
							$errorMsg = 'Appraisal process is enabled to employees';
						if($checkActiveApp['status'] == 2)	
							$errorMsg = 'Appraisal process is closed';
					}
                } 
                else 
                {
                    $errorMsg = 'No active appraisal process exists';
                }
          //  } 
           // else 
           // {
           //     $errorMsg = 'Appraisal process is not configured yet.';
           // }
		
        }else
        {
        	$bunitModel = new Default_Model_Businessunits();
        	$buids = '';
        	$bunitdataArr = array();
        	$activeAppraisalManagerArr = $appInitModel->getAppraisalForMgrEmp(1);
        	if(!empty($activeAppraisalManagerArr))
        	{
        		foreach($activeAppraisalManagerArr as $mgrArr)
        		{
        			$buids.=$mgrArr['businessunit_id'].',';
        		}
        		$buids = rtrim($buids,',');
        		if($buids!='')
        		{
	        		$bustr = implode(',',array_unique(explode(',', $buids)));	
	        		$bunitdataArr = $bunitModel->getBusinessUnits($bustr);
        		}	
        	}
			$this->view->bunitdataarr = $bunitdataArr;
			$flag = 2;   
        }
        
        $this->view->msgarray = $msgarray; 
        $this->view->ermsg = $errorMsg;
        $this->view->flag = $flag;
		$this->view->loginuserGroup = $loginuserGroup;         
    }
    
	public function managerstatusAction()
    {
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('managerstatus', 'html')->initContext();
    	$flag = $this->_request->getParam('flag');
    	$appraisalid = $this->_request->getParam('appraisalid');
    	$completedmgrids = $this->_request->getParam('completedmgrids');
    	if($completedmgrids)
    	$completedmgrids = array_unique(explode(',', $completedmgrids));
    	$department_id = $this->_request->getParam('deptid');
    	$businessunit_id = $this->_request->getParam('bunitid');
    	$performanceappflag = $this->_request->getParam('perf_app_flag');
    	$app_status = $this->_request->getParam('app_status');	
        $appqsprivilegesModel = new Default_Model_Appraisalqsmain();
        $appempModel = new Default_Model_Appraisalgroupemployees();
        $appInitModel = new Default_Model_Appraisalinit();
        $managerIds = '';
        $msgarray = array();
        $employeeArr = array();
        $budeptArr = array();
        $errorMsg = '';
        
        if($flag == 1)
        {
        	if($appraisalid)
        	{
        		$getManagerIdsArr = $appqsprivilegesModel->getManagerIDs($appraisalid);
        		if(!empty($getManagerIdsArr))
        		{
        			foreach($getManagerIdsArr as $ids)
        		 		$managerIds.= $ids['mgr'].',';
        		 	$managerIds = rtrim($managerIds,',');		
        		}
        		else
        		{
        			$errorMsg = 'Line Manager(s) not configured for the appraisal.';
        		} 
        		if($managerIds !='')
        		 $employeeArr = $appempModel->getEmployeeList(array(), $managerIds,2);
        		 
        		 //$budeptArr = $this->getbudeptname($appraisalid);
        		 $budeptArr =  sapp_Global::getbudeptname($appraisalid);
        	}
        }else
        {
        		$checkActiveApp = $appInitModel->checkAppraisalExists($businessunit_id, $department_id);//,$performanceappflag
        		if(count($checkActiveApp) > 0)
                {
                	$checkActiveApp = $checkActiveApp[0];
					if($checkActiveApp['enable_step'] == 1 && $checkActiveApp['status'] == 1)
					{
						$getManagerIdsArr = $appqsprivilegesModel->getManagerIDs($checkActiveApp['id']);
						if(!empty($getManagerIdsArr))
		        		{
		        			foreach($getManagerIdsArr as $ids)
		        		 		$managerIds.= $ids['mgr'].',';
		        		 	$managerIds = rtrim($managerIds,',');		
		        		} 
		        		if($managerIds !='')
		        		 $employeeArr = $appempModel->getEmployeeList(array(), $managerIds,2);
		        		 
		        		 $completedmgrids = array_unique(explode(',', $checkActiveApp['manager_ids']));
					}
					else
					{
						if($checkActiveApp['enable_step'] == 2)
							$errorMsg = 'Appraisal process is enabled to employees.';
						if($checkActiveApp['status'] == 2)	
							$errorMsg = 'Appraisal process is closed.';
					}
					
					//$budeptArr = $this->getbudeptname($checkActiveApp['id']);
					$budeptArr =  sapp_Global::getbudeptname($checkActiveApp['id']);
                }else 
                {
                	$errorMsg = 'No Active Appraisal process.';
                }
        }
        
        $this->view->flag = $flag;
        $this->view->msgarray = $msgarray; 
        $this->view->ermsg = $errorMsg;
        $this->view->employeeArr = $employeeArr;
        $this->view->completedmgrids = $completedmgrids;
        $this->view->budeptArr = $budeptArr;
        $this->view->app_status = $app_status;
    }
    
    public function checkappraisalimplementationAction()
    {
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('savegroupedemployeesajax','json')->initContext();
		$this->_helper->layout->disableLayout();
		$result = array();
		$model = new Default_Model_Appraisalinit();
		$departmentsmodel = new Default_Model_Departments();
		$businessunit_id = $this->_request->getParam('buid');
		$enable_step = $this->_request->getParam("enable_step");
		$checkActiveApp = $model->checkAppraisalExists($businessunit_id);
		if(!empty($checkActiveApp))
		{
			$result['flag'] = 'buwise';
			$result['msg'] = 'false';
			$deptstr = '';
			
			foreach($checkActiveApp as $actApp)
			{
				if(!empty($actApp['department_id']))
				{
					$deptstr.= trim($actApp['department_id']).",";
				}
			}
			$deptstr = rtrim($deptstr,',');
			if(!empty($deptstr))
			{
				$departmentlistArr = $model->getdeparmentsadmin($businessunit_id,$enable_step);
				$options_data = "";
				$options_data .= sapp_Global::selectOptionBuilder('', 'Select Department');
				if(!empty($departmentlistArr))
				{
					foreach($departmentlistArr as $dept)
					{
						$options_data .= sapp_Global::selectOptionBuilder($dept['id'],utf8_encode($dept['deptname']));
					}
					$result['result'] = $options_data;
					$result['msg'] = 'true';
				}
				$result['flag'] = 'deptwise';
			}
		}
		else
		{
			$result['flag'] = 'notinitialized';
			$result['msg'] ='false';
			$result['result'] = '';
		}
		$this->_helper->_json($result);
   }
    
	public function employeeAction()
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
        
    	$errorMsg = '';
    	$msgarray = array();
    	$bunitdataArr = array();
    	$flag = '';
        $appInitModel = new Default_Model_Appraisalinit();
        $app_status_array = array(1=>APP_PENDING_EMP, 2=>APP_PENDING_L1, 3=>APP_PENDING_L2, 4=>APP_PENDING_L3,
										5=>APP_PENDING_L4, 6=>APP_PENDING_L5, 7=>APP_COMPLETED);
        if($loginuserRole != SUPERADMINROLE && $loginuserGroup != MANAGEMENT_GROUP && $loginuserGroup != HR_GROUP)
        {
            $appImpleData = sapp_PerformanceHelper::check_per_implmentation($businessunit_id, $department_id);
           // if(count($appImpleData) > 0)
           // {                
                $this->view->imple_data = $appImpleData;
                $checkActiveApp = $appInitModel->checkAppraisalExists($businessunit_id, $department_id); //,$appImpleData['performance_app_flag']
                if(count($checkActiveApp) > 0)
                {
                	$checkActiveApp = $checkActiveApp[0];
					if($checkActiveApp['enable_step'] == 2 && $checkActiveApp['status'] == 1)
					{
						$this->view->checkActiveApp = $checkActiveApp;
						$flag = 1;
					}
					else
					{
						if($checkActiveApp['enable_step'] == 1)
							$errorMsg = 'Appraisal process is enabled to managers.';
						if($checkActiveApp['status'] == 2)	
							$errorMsg = 'Appraisal process is closed.';
					}
                } 
                else 
                {
                    $errorMsg = 'Active Appraisal process is not there.';
                }
          //  } 
         //   else 
         //   {
         //       $errorMsg = 'Appraisal process is not yet configured.';
         //   }
        }
		else
        {
        	$bunitModel = new Default_Model_Businessunits();
			$buids = '';
        	$bunitdataArr = array();
        	$activeAppraisalManagerArr = $appInitModel->getAppraisalForMgrEmp(2);
        	if(!empty($activeAppraisalManagerArr))
        	{
        		foreach($activeAppraisalManagerArr as $mgrArr)
        		{
        			$buids.=$mgrArr['businessunit_id'].',';
        		}
        		$buids = rtrim($buids,',');
        		if($buids!='')
        		{
	        		$bustr = implode(',',array_unique(explode(',', $buids)));	
	        		$bunitdataArr = $bunitModel->getBusinessUnits($bustr);
        		}	
        	}
										
			$this->view->bunitdataarr = $bunitdataArr;
			$flag = 2;   
        }
        
        $this->view->app_status_array = $app_status_array;
        $this->view->msgarray = $msgarray; 
        $this->view->ermsg = $errorMsg;
        $this->view->flag = $flag;
    }
    
	public function employeestatusAction()
    {
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('employeestatus', 'html')->initContext();
    	$flag = $this->_request->getParam('flag');
    	$appraisalid = $this->_request->getParam('appraisalid');
    	$department_id = $this->_request->getParam('deptid');
    	$businessunit_id = $this->_request->getParam('bunitid');
    	$performanceappflag = $this->_request->getParam('perf_app_flag');	
    	$app_status = $this->_request->getParam('app_status');
        $appqsprivilegesModel = new Default_Model_Appraisalqsmain();
        $appempModel = new Default_Model_Appraisalgroupemployees();
        $appInitModel = new Default_Model_Appraisalinit();
        $empratingsModel = new Default_Model_Appraisalemployeeratings();
        $employeeIds = '';
        $msgarray = array();
        $employeeArr = array();
        $getEmployeeRatingsArr = array();
        $errorMsg = '';
        $budeptArr = array();
        
        if($flag == 1)
        {
        	if($appraisalid)
        	{
        		$getEmployeeRatingsArr = $empratingsModel->getEmployeeIds($appraisalid,'',$app_status);
        		if(!empty($getEmployeeRatingsArr))
        		{
        			foreach($getEmployeeRatingsArr as $ids)
        		 		$employeeIds.= $ids['employee_id'].',';
        		 	$employeeIds = rtrim($employeeIds,',');		
        		} 
        		if($employeeIds !='')
				$employeedetailsArr = $appempModel->getEmployeeList(array(), $employeeIds,2);
				if(!empty($employeedetailsArr))
				{
					foreach($employeedetailsArr as $key => $val)
					{
						$employeeArr[$val['user_id']] = $val;
					}
				}
				//$budeptArr = $this->getbudeptname($appraisalid);
				$budeptArr =  sapp_Global::getbudeptname($appraisalid);
        	}
        }else
        {
        		$checkActiveApp = $appInitModel->checkAppraisalExists($businessunit_id, $department_id);//,$performanceappflag
        		if(count($checkActiveApp) > 0)
                {
                	$checkActiveApp = $checkActiveApp[0];
					if($checkActiveApp['enable_step'] == 2 && $checkActiveApp['status'] == 1)
					{
						$appraisalid = $checkActiveApp['id'];
						$getEmployeeRatingsArr = $empratingsModel->getEmployeeIds($checkActiveApp['id'],'',$app_status);
						if(!empty($getEmployeeRatingsArr))
			        		{
			        			foreach($getEmployeeRatingsArr as $ids)
			        		 		$employeeIds.= $ids['employee_id'].',';
			        		 	$employeeIds = rtrim($employeeIds,',');		
			        		} 
			        		if($employeeIds !='')
			        		 $employeedetailsArr = $appempModel->getEmployeeList(array(), $employeeIds,2);
			        		 if(!empty($employeedetailsArr))
			        		 {
			        		 	foreach($employeedetailsArr as $key => $val)
			        		 	{
			        		 		$employeeArr[$val['user_id']] = $val;
			        		 	}
			        		 }
					}
					else
					{
						if($checkActiveApp['enable_step'] == 1)
							$errorMsg = 'Appraisal process is enabled to managers.';
						if($checkActiveApp['status'] == 2)	
							$errorMsg = 'Appraisal process is closed.';
					}
					
					//$budeptArr = $this->getbudeptname($checkActiveApp['id']);
					$budeptArr =  sapp_Global::getbudeptname($checkActiveApp['id']);
                }else 
                {
                	$errorMsg = 'Appraisal process is not yet configured.';
                }
        }
        
        $this->view->flag = $flag;
        $this->view->msgarray = $msgarray; 
        $this->view->ermsg = $errorMsg;
        $this->view->employeeArr = $employeeArr;
        $this->view->employeeratingsArr = $getEmployeeRatingsArr;
        $this->view->appraisalid = $appraisalid;
        $this->view->budeptArr = $budeptArr;
    }

	public function employeeAction_old()
    {
    	$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$businessUnitId = $auth->getStorage()->read()->businessunit_id;
			$departmentId = $auth->getStorage()->read()->department_id;
		}
    	
		$appraisalSkillsModel = new Default_Model_Appraisalemployeeratings();	
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
			$sort = 'DESC';$by = 'aer.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else 
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'aer.modifieddate';
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
				
		$dataTmp = $appraisalSkillsModel->getAppraisalEmployeeStatusGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall,$businessUnitId,$departmentId);		 		
					
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
		$this->render('commongrid/index', null, true);		
    }
    
    public function getbudeptname($appraisalid)
    {
    	$appInitModel = new Default_Model_Appraisalinit();
    	$businessunitmodel = new Default_Model_Businessunits();
    	$deptmodel = new Default_Model_Departments();
    	$buname = '';
    	$deptname ='';
    	$perf_impl_flag = '';
    	$appraisaldataArr = array();
    	if($appraisalid)
    	{
    		$appraisaldataArr = $appInitModel->getAppDataById($appraisalid);
    		if(!empty($appraisaldataArr))
    		{
    			if($appraisaldataArr['businessunit_id']!='')
    			{
					$buDataArr = $businessunitmodel->getSingleUnitData($appraisaldataArr['businessunit_id']);
					// $perfimplementation = $appInitModel->check_performance_implmentation($appraisaldataArr['businessunit_id']);
					if(!empty($buDataArr))
					{
						$buname = $buDataArr['unitname'];
					}
					if(!empty($appraisaldataArr['performance_app_flag']))
					{
						$perf_impl_flag = $appraisaldataArr['performance_app_flag'];
					}
    			}
    			if($perf_impl_flag == 0)
    			{	
					if($appraisaldataArr['department_id']!='')
						$deptArr = $deptmodel->getSingleDepartmentData($appraisaldataArr['department_id']);

					if(!empty($deptArr))
					{
						$deptname = $deptArr['deptname'];
					}	
    			}		
    		}
    	}
    	
    	return array('buname' => $buname,'deptname'=>$deptname,'perf_app_flag'=>$perf_impl_flag,'appdata'=>$appraisaldataArr);
    
    }
    
	public function addlinemanagerAction()
    {
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('addlinemanager', 'html')->initContext();
        $init_id = $this->_getParam('init_id',null);
        $context = $this->_getParam('context');
        $employeeid = $this->_getParam('employeeid');
        //$line1_id = $this->_getParam('line1_id',null); 
        //$levels = $this->_getParam('levels',null); 
        $app_levels = 1;
        $app_init_model = new Default_Model_Appraisalinit();
        $app_qsmodel = new Default_Model_Appraisalqsmain();
        $appEmpRatingsModel = new Default_Model_Appraisalemployeeratings();
        $levels = 1;
        $init_data = $app_init_model->getConfigData($init_id);
        if(count($init_data) > 0)
            $init_data = $init_data[0];
        $employees_cnt = 1;
        
        $emp_data_arr = $app_qsmodel->getAllManagerIds($init_id,$employeeid);
        if(!empty($emp_data_arr))
        {
        		if($emp_data_arr[0]['line_manager_1']!='' && $emp_data_arr[0]['line_manager_1']!='null')
        		{
        			$levels=1;
        			$line1_id = $emp_data_arr[0]['line_manager_1'];
        		}	
        		if($emp_data_arr[0]['line_manager_2']!='' && $emp_data_arr[0]['line_manager_2']!='null')
        			$levels=$levels+1;
        		if($emp_data_arr[0]['line_manager_3']!='' && $emp_data_arr[0]['line_manager_3']!='null')
        			$levels=$levels+1;
        		if($emp_data_arr[0]['line_manager_4']!='' && $emp_data_arr[0]['line_manager_4']!='null')
        			$levels=$levels+1;
        		if($emp_data_arr[0]['line_manager_5']!='' && $emp_data_arr[0]['line_manager_5']!='null')
        			$levels=$levels+1;				
        	
        }
        
        
        $appEmpRatingsData = $appEmpRatingsModel->getSelectedAppraisalData_notused($init_id,$employeeid);
        if(!empty($appEmpRatingsData))
        {
        	if($appEmpRatingsData['appstatus']!=1)
        	{
        		$app_levels = ($appEmpRatingsData['appstatus']-1);
        	}
        }
        
        $this->view->init_data = $init_data;
        $this->view->init_id = $init_id;
        $this->view->context = $context;
        $this->view->line1_id = $line1_id;
        $this->view->levels = $levels;
        $this->view->employees_cnt = $employees_cnt;
        $this->view->app_levels = $app_levels;
        $this->view->employeeid = $employeeid;
        
    }
    
public function displaymanagersAction()
    {
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('displaymanagers', 'html')->initContext();
        $line1_data = array();
        $line_managers = array();
        $type = $this->_getParam('type',null);
        $levels = $this->_getParam('levels',null);
        $init_id = $this->_getParam('init_id',null);
        $line1_id = $this->_getParam('line1_id',null);
        $context = $this->_getParam('context','add');
        $employeeid = $this->_getParam('employeeid');
        $app_levels = 1;
        $employeeIds = '';
        
        $app_init_model = new Default_Model_Appraisalinit();
        $appEmpRatingsModel = new Default_Model_Appraisalemployeeratings();
        $init_data = $app_init_model->getConfigData($init_id);
    	$getEmployeeRatingsArr = $appEmpRatingsModel->getEmployeeIds($init_id,'','');
        if(!empty($getEmployeeRatingsArr))
        	{
        		foreach($getEmployeeRatingsArr as $ids)
        		 	$employeeIds.= $ids['employee_id'].',';
        		 	$employeeIds = rtrim($employeeIds,',');		
        	}
        
        if(count($init_data) > 0)
            $init_data = $init_data[0];
        
        $managers = $app_init_model->getRepManagers_new($type,$init_id,$init_data,$employeeIds);
        
        if($context == 'edit')
        {
            $emp_model = new Default_Model_Employee();
            $line1_data = $emp_model->getEmp_from_summary($line1_id);
            $line_managers = $app_init_model->getLineManagers_new($init_id,$employeeid);
            $line_managers = array_filter($line_managers);            
        }
        
    	$appEmpRatingsData = $appEmpRatingsModel->getSelectedAppraisalData_notused($init_id,$employeeid);
        if(!empty($appEmpRatingsData))
        {
        	if($appEmpRatingsData['appstatus']!=1)
        	{
        		$app_levels = ($appEmpRatingsData['appstatus']-1);
        	}
        }
        $this->view->levels = $levels;
        $this->view->managers = $managers;
        $this->view->init_id = $init_id;
        $this->view->context = $context;
        $this->view->line1_data = $line1_data;
        $this->view->line_managers = $line_managers;
        $this->view->line1_id = $line1_id;
        $this->view->app_levels = $app_levels;
        
        $this->render('displaymanagers');
    }
    
	public function updatelinemanagerAction()
	{
		$this->_helper->layout->disableLayout();
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('updatelinemanager', 'json')->initContext();
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
            $loginUserEmpId = $auth->getStorage()->read()->employeeId;
            $loginUserEmail = $auth->getStorage()->read()->emailaddress;
            $loginUsername = $auth->getStorage()->read()->userfullname;
            $loginUserprofileimg = $auth->getStorage()->read()->profileimg;
        }
		$appraisalPrivMainModel = new Default_Model_Appraisalqsmain();
		$appraisalempratingsmodel = new Default_Model_Appraisalemployeeratings();
		$result['result'] = 'success';
		$result['msg'] = '';
		$send_mails = false;
		$appraisalid = $this->_request->getParam('appraisalid');
		$employeeid = $this->_request->getParam('employeeid');
		$line_1_mgr = $this->_request->getParam('line_1_mgr');
		$line_2_mgr = $this->_request->getParam('line_2_mgr');
		$line_3_mgr = $this->_request->getParam('line_3_mgr');
		$line_4_mgr = $this->_request->getParam('line_4_mgr');
		$line_5_mgr = $this->_request->getParam('line_5_mgr');
		$levels = $this->_request->getParam('levels');
		//checking equality of manager levels for sending mails
		if($appraisalid && $employeeid)
		{
			  $appraisal_level_arr = $appraisalPrivMainModel->getAllManagerIds($appraisalid,$employeeid);
			  $appraisal_level_arr = $appraisal_level_arr[0];
			  $levels_pre = $appraisal_level_arr['manager_levels'];
			  if($levels_pre == $levels)
		      {
		      	$preLine_1_mgr = $appraisal_level_arr['line_manager_1'];
		      	$preLine_2_mgr = $appraisal_level_arr['line_manager_2'];
		      	$preLine_3_mgr = $appraisal_level_arr['line_manager_3'];
		      	$preLine_4_mgr = $appraisal_level_arr['line_manager_4'];
		      	$preLine_5_mgr = $appraisal_level_arr['line_manager_5'];
		      	
		      	for($i=1;$i<=$levels_pre;$i++)
		      	{
		      		$preLine_mgr = "preLine_".$i."_mgr";
		      		$line_mgr    = "line_".$i."_mgr";
		      		if($$line_mgr != $$preLine_mgr)
		      		{  
		      			$send_mails = true;
		      			
		      		}	      		 
		      	}
		      }
		      else 
		      {
				$send_mails = true;
		      }
		}
		//end checking send mails to employees 
		if($appraisalid && $employeeid)
		{
			$trDb = Zend_Db_Table::getDefaultAdapter();		
	        $trDb->beginTransaction();
	        try 
	        {
				$data = array( 'line_manager_1' =>  $line_1_mgr!=''?$line_1_mgr:NULL,
								'line_manager_2' => $line_2_mgr!=''?$line_2_mgr:NULL,
								'line_manager_3' => $line_3_mgr!=''?$line_3_mgr:NULL,
								'line_manager_4' => $line_4_mgr!=''?$line_4_mgr:NULL,
								'line_manager_5' => $line_5_mgr!=''?$line_5_mgr:NULL,
								'manager_levels'=> is_numeric($levels)?$levels:1,
	                            'modifiedby' => $loginUserId,
	                            'modifiedby_role' => $loginuserRole,
	                            'modifiedby_group' => $loginuserGroup,
	                            'modifieddate' => gmdate("Y-m-d H:i:s"),
	                                    );
	           $privilegeswhere = " employee_id = '".$employeeid."' and pa_initialization_id='".$appraisalid."' and module_flag=1 and isactive=1 ";
	           $empratingswhere = " employee_id = '".$employeeid."' and pa_initialization_id='".$appraisalid."' and isactive=1 ";
	           $appraisalPrivMainModel->SaveorUpdatePrivilegeData($data, $privilegeswhere);
			   //remove the manager_levels as this column is not there in main_pa_employee_ratings table
	           unset($data['manager_levels']);
			   $appraisalempratingsmodel->SaveorUpdateAppraisalSkillsData($data, $empratingswhere);
			   
			   
					if($send_mails == true)
					{		   
			  		/** Start
					 * Sending Mails to employees
					 */
						$emp_id_str = ($loginuserRole == SUPERADMINROLE) ? " ":"($loginUserEmpId)";
						
						//Preparing string with line manager ids
						$mgrStr = '';
						for($i=1;$i<=$levels;$i++)
						{
							$mgr_str = 'line_'.$i.'_mgr';//$line_1_mgr
							if(is_numeric($$mgr_str))
								$mgrStr .= $$mgr_str.',';
						}
						$mgrStr = rtrim($mgrStr, ",");
						$appraisalratingsmodel = new Default_Model_Appraisalratings();
						$appraisal_details = $appraisalratingsmodel->getappdata($appraisalid);
						if(!empty($appraisal_details))
						{
							$to_year = $appraisal_details['to_year'];
						}
						$employeeDetailsArr = $appraisalPrivMainModel ->getManagerDetailsByIds($employeeid,$mgrStr); 
						$mgr_array = array();
						$mgr_array = explode(",",$mgrStr);
						
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
										if($emp['user_id'] == $employeeid)    // checking employeeId to prepare toemailAddress
										{
											$toEmailId = $emp['emailaddress'];
											$toEmailName = $emp['userfullname'];
											$toEmpId = $emp['employeeId'];
											array_pop($empArr);
											
										}
										
										
									}
									$index = array_search($employeeid,$empUserIdArr);
									unset($employeeDetailsArr[$index]);
									$mail_str = '';
									for($j=0;$j<sizeof($mgr_array);$j++)
									{
										foreach($employeeDetailsArr as $employee)
										{
											if($mgr_array[$j] == $employee['user_id'])
											{ 
												$profile_pic = $employee['profileimg'];
												$cnt = $j+1;
												if($profile_pic != '')
												$src = DOMAIN."public/uploads/profile/".$profile_pic;
												else
												$src = MEDIA_PATH."images/default-profile-pic.jpg";
												 $mail_str .= "<div style='padding:20px 0 0 0;color:#3b3b3b;'>Line $cnt Manager : ".$employee['userfullname']." <img src=".$src." onError=this.src=".MEDIA_PATH."images/default-profile-pic.jpg width='30px' height='30px' /></div>";
											
								
											}
										} 
										
									}
								}
								//pushing loginUserEmail to Bcc array 
								array_push($empArr,$loginUserEmail);
								
								$options['subject'] = APPLICATION_NAME.': Change in Line Managers' ;
                                $options['header'] 	= 'Performance Appraisal : '.$to_year;
                                $options['bcc'] 	= $empArr; 
                                $options['toEmail'] = $toEmailId; 
                                $options['toName'] = $toEmailName; 
                                $options['message'] = "<div style='padding: 0; text-align: left; font-size:14px; font-family:Arial, Helvetica, sans-serif;'>				
														<span style='color:#3b3b3b;'>Hi,</span><br />
														<div style='padding:20px 0 0 0;color:#3b3b3b;'>Line Managers for ".$toEmailName."(".$toEmpId.") have been modified by ".$loginUsername. $emp_id_str."</div>
														$mail_str
														<div style='padding:20px 0 10px 0;'>Please <a href=".BASE_URL." target='_blank' style='color:#b3512f;'>click here</a> to login  to your <b>".APPLICATION_NAME."</b> account and check the details.</div>
														</div> ";
                                $mail_id 			=  sapp_Global::_sendEmail($options);
							 
					/**
					 * End mails sending
					 */	
				}
			   
	           $trDb->commit();
	        }
			catch(Exception $e)
          	{
          		$trDb->rollBack();
          		$result['result'] = 'error';
          		$result['msg'] = $e->getMessage();
          	}   
		}
		$this->_helper->json($result);
	}
}