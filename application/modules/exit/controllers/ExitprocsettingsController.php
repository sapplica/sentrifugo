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
/**
** controller used to configure exit proc settings
**/
class Exit_ExitprocsettingsController extends Zend_Controller_Action
{
	private $epSettingsModel;
	private $options;
	private $loggedInUser = '';
	private $loggedInUserGroup = '';
	private $loggedInUserRole = '';
	
	public function preDispatch()
	{
		/**
		** for ajax calls
		**/
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('getdepartments','json')->initContext();
	}
	
	public function init()
	{
		$this->_options = $this->getInvokeArg('bootstrap')->getOptions();
		
		/** Instantiate exit proc settings model **/
		$this->epSettingsModel = new Exit_Model_Exitprocsettings();
		$this->allExitProcsModel = new Exit_Model_Allexitprocs();
		/**
		** Initiating zend auth object
		** for getting logged in user id
		**/
		$auth = Zend_Auth::getInstance();
		
		if($auth->hasIdentity())
		{
			$this->loggedInUser = $auth->getStorage()->read()->id;
			$this->loggedInUserRole = $auth->getStorage()->read()->emprole;
			$this->loggedInUserGroup = $auth->getStorage()->read()->group_id;
		}
	}

	public function indexAction()
	{
		/**
		**	check for ajax call
		**/
		$call = $this->_getParam('call');
		if($call == 'ajaxcall')
				$this->_helper->layout->disableLayout();	
		
		/**
		** capture request parameters 
		**/
		$objname = $this->_getParam('objname');
		$refresh = $this->_getParam('refresh');
		$dashboardcall = $this->_getParam('dashboardcall');
		$context = $this->_getParam('context');
		$data = array();
		$searchQuery = '';
		$searchArray = array();
		$tablecontent='';	
		
		/** check for refresh button event 
		**  If yes, build default parameters
		**	else, build parameters accordingly
		**/
		if($refresh == 'refresh')
		{
			if($dashboardcall == 'Yes')
				$perPage = DASHBOARD_PERPAGE;
			else	
				$perPage = PERPAGE;

			$sort = 'DESC';$by = 'e.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else 
		{
			/** build sort parameter **/
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';

			/** build order by parameter **/
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'e.modifieddate';
			
			/** get records per page parameter **/
			if($dashboardcall == 'Yes')
				$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
			else 
				$perPage = $this->_getParam('per_page',PERPAGE);

			/** set page number parameter **/
			$pageNo = $this->_getParam('page', 1);
			
			/** build search parameters **/
			$searchData = $this->_getParam('searchData');	
			$searchData = rtrim($searchData,',');					
		}

		/**
		**	based on search, sort, pagination, order by parameters 
		**  get data object to build exit proc settings grid
		**/
		$dataTmp = $this->epSettingsModel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall,$context);
		array_push($data,$dataTmp);

		/**
		** send data objects to view
		**/
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		
		/**
		** send flash messages to view
		**/
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
	}
	
	public function viewAction()
	{
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $login_group_id = $auth->getStorage()->read()->group_id;
            $login_role_id = $auth->getStorage()->read()->emprole;
        }
		
		
		/**
		** capture record id to view
		**/
		$id = (int)$this->_request->getParam('id');
		$objName = 'exitprocsettings';

		if($id)
		{
			if(is_numeric($id) && $id>0)
			{
				// get details based on id
				$res = $this->epSettingsModel->getExitProcSettingsDetails($id);
				$previ_data = sapp_Global::_checkprivileges(EXIT_PROC_SETTINGS,$login_group_id,$login_role_id,'view');
				if(!empty($res))
				{
					$this->view->data=$res[0];
					$this->view->id = $id;
					$this->view->previ_data = $previ_data;
					
				}
				else
				{
					$this->view->ermsg = 'norecord';
				}
				
			}
			else{
				$this->view->ermsg = 'norecord';
			}
			
		}
		else
		{
		   $this->view->ermsg = 'norecord';
		} 	
		$this->view->controllername =	$objName ;	
	}
	public function addAction()
	{
		/**
		** check for logged in user role for add privileges
		**/
		if(sapp_Global::_checkprivileges(EXIT_PROC_SETTINGS,$this->loggedInUserGroup,$this->loggedInUserRole,'add') != 'Yes'){
				$this->view->ermsg = 'noprivileges';
				return;
		} 

		/**
		** Initiating exit proc settings form
		** and assigning action
		**/
		$epSettingsForm = new Exit_Form_Exitprocsettings();
		$epSettingsForm->setAttrib('action',BASE_URL.'exit/exitprocsettings/add');
		$this->view->form = $epSettingsForm;
	
		/**
		** populate business units on page load
		**/
		$epSettingsForm->businessunit_id->addMultiOption('','Select Business Unit');
		$businessUnitsObj = $this->epSettingsModel->getBusinessUnits();
		if(!empty($businessUnitsObj))
		{
			foreach($businessUnitsObj as $businessUnit)
			{
				$epSettingsForm->businessunit_id->addMultiOption($businessUnit['id'],utf8_encode($businessUnit['unitname']));
			}
		}	

		if($this->getRequest()->getPost())
		{
			$this->save($epSettingsForm,'add');
		}
	}
	
	public function editAction()
	{
		/**
		** check for logged in user role for add privileges
		**/
		if(sapp_Global::_checkprivileges(EXIT_PROC_SETTINGS,$this->loggedInUserGroup,$this->loggedInUserRole,'edit') != 'Yes'){
				$this->view->ermsg = 'noprivileges';
				return;
		} 
		
		/**
		** capture record id to edit
		**/
		$id = (int)$this->_request->getParam('id');
		$this->view->id = $id;
		$msgarray = array(); 
		if($id)
		{
			$res = $this->epSettingsModel->getExitProcSettingsDetails($id);
			
			if(empty($res))
			{
				$this->view->ermsg = 'nodata';
				return;
			}
			if(!empty($res))
			{
				$businessunit_id = !empty($res[0]['businessunit_id'])? $res[0]['businessunit_id']:'';
				$department_id =  !empty($res[0]['department_id'])? $res[0]['department_id']:'';
				$l2_manager =  !empty($res[0]['l2_manager'])? $res[0]['l2_manager']:'';
				$hr_manager = !empty($res[0]['hr_manager'])? $res[0]['hr_manager']:'';
				$sys_admin = !empty($res[0]['sys_admin'])? $res[0]['sys_admin']:'';
				$general_admin =  !empty($res[0]['general_admin'])? $res[0]['general_admin']:'';
				$finance_manager = !empty($res[0]['finance_manager'])? $res[0]['finance_manager']:'';		
			}
			
			/**
			** Initiating exit proc settings form
			** and assigning action
			**/
			$epSettingsForm = new Exit_Form_Exitprocsettings();
			$epSettingsForm->setAttrib('action',BASE_URL.'exit/exitprocsettings/edit/id/'.$id);
			$this->view->form = $epSettingsForm;
			$epSettingsForm->submit->setLabel('Update');
			if(!empty($res))
			{
				$epSettingsForm->populate($res[0]);
			}
			/**
			** populate business units on page load
			**/
			$epSettingsForm->businessunit_id->addMultiOption('','Select Business Unit');
			$businessUnitsObj = $this->epSettingsModel->getBusinessUnits($businessunit_id);
			if(!empty($businessUnitsObj))
			{
				foreach($businessUnitsObj as $businessUnit)
				{
					$epSettingsForm->businessunit_id->addMultiOption($businessUnit['id'],utf8_encode($businessUnit['unitname']));
				}
				$epSettingsForm->setDefault('businessunit_id',$businessunit_id);
			}	

			/**
			** populate departments on page load
			**/
			$epSettingsForm->department_id->addMultiOption('','Select Department');
			$departmentsObj = $this->epSettingsModel->getDepartments($businessunit_id,$department_id,'all');
			if(!empty($departmentsObj))
			{
				foreach($departmentsObj as $departments)
				{
					$epSettingsForm->department_id->addMultiOption($departments['id'],utf8_encode($departments['deptname']));
				}
				$epSettingsForm->setDefault('department_id',$department_id);
			}	

			/**
			** populate hr manager on page load
			**/
			$hrOptions = $this->epSettingsModel->getEmployeesDataByRole(HR_GROUP,$businessunit_id,$department_id,"");
			if(count($hrOptions) > 0)
			{
				foreach($hrOptions as $hrm)
				{
					$epSettingsForm->hr_manager->addMultiOption($hrm['user_id'],utf8_encode($hrm['userfullname']));
				}
				$epSettingsForm->setDefault('hr_manager',$hr_manager);
			}
			
			/** fill l2 manager on page load **/
			$l2Options = $this->epSettingsModel->getEmployeesDataByRole(array(MANAGEMENT_GROUP,HR_GROUP,MANAGER_GROUP,SYSTEMADMIN_GROUP,EMPLOYEE_GROUP),$businessunit_id,$department_id,'multiple');
			if(count($l2Options) > 0)
			{
				foreach($l2Options as $l2m)
				{
					$epSettingsForm->l2_manager->addMultiOption($l2m['user_id'],utf8_encode($l2m['userfullname']));
				}
				$epSettingsForm->setDefault('l2_manager',$l2_manager);
			}

			/** fill system admin data **/
			$sysAdminOptions = $this->epSettingsModel->getEmployeesDataByRole(SYSTEMADMIN_GROUP,$businessunit_id,$department_id, "");
			if(count($sysAdminOptions) > 0)
			{
				foreach($sysAdminOptions as $sysAdmin)
				{
					$epSettingsForm->sys_admin->addMultiOption($sysAdmin['user_id'],utf8_encode($sysAdmin['userfullname']));
				}
				$epSettingsForm->setDefault('sys_admin',$sys_admin);
			}
			/* else
			{
				$msgarray['sys_admin'] = 'No employees';
			} */

			/** fill general admin data **/
			$generalAdminOptions = $this->epSettingsModel->getEmployeesDataByRole(array(MANAGER_GROUP,EMPLOYEE_GROUP),$businessunit_id, $department_id,'multiple');
			if(count($generalAdminOptions) > 0)
			{
				foreach($generalAdminOptions as $generalAdmin)
				{
					$epSettingsForm->general_admin->addMultiOption($generalAdmin['user_id'],utf8_encode($generalAdmin['userfullname']));
				}
				$epSettingsForm->setDefault('general_admin',$general_admin);
			}
			/* else
			{
				$msgarray['general_admin'] = 'No employees';
			}	 */		

			/** fill finance manager data **/
			$financeManagerOptions = $this->epSettingsModel->getEmployeesDataByRole(array(HR_GROUP,MANAGER_GROUP,EMPLOYEE_GROUP),$businessunit_id, $department_id,'multiple');
			if(count($financeManagerOptions) > 0)
			{
				foreach($financeManagerOptions as $financeManager)
				{
					$epSettingsForm->finance_manager->addMultiOption($financeManager['user_id'],utf8_encode($financeManager['userfullname']));
				}
				$epSettingsForm->setDefault('finance_manager',$finance_manager);
			}
			/* else
			{
				$msgarray['finance_manager'] = 'No employees';
			} */

		 /**
		 settings can not be edited after initializing the exit process.
		 if all records are approved or rejected then
		 we can edit **/
			$result=$this->allExitProcsModel->getExitsettingsProcDetails($id,'edit');
			
			if(count($result)>0)
			{
				$this->view->ermsg = 'noedit';
				$epSettingsForm->businessunit_id->setAttrib("disabled", "disabled");
				$epSettingsForm->department_id->setAttrib("disabled", "disabled");
				$epSettingsForm->hr_manager->setAttrib("disabled", "disabled");
				$epSettingsForm->l2_manager->setAttrib("disabled", "disabled");
				$epSettingsForm->sys_admin->setAttrib("disabled", "disabled");
				$epSettingsForm->general_admin->setAttrib("disabled", "disabled");
				$epSettingsForm->finance_manager->setAttrib("disabled", "disabled");
				$epSettingsForm->notice_period->setAttrib("readonly", "readonly");
				$epSettingsForm->removeElement("submit");
			}
			
			$this->view->msgarray = $msgarray;
			$this->view->result = $result;
			if($this->getRequest()->getPost())
			{
				$this->save($epSettingsForm,'edit',$id);
			}
		}		
		else
		{
			$this->view->ermsg = 'invalidURL';
		}
	}

	public function save($epSettingsForm,$con,$id = '')
	{
		$businessunit_id = $this->_request->getParam('businessunit_id');
		$department_id = $this->_request->getParam('department_id');
		
		//check duplicate employees
		$employee_array = array('l2_manager'=>$this->_request->getParam('l2_manager'),'hr_manager'=>$this->_request->getParam('hr_manager'),'sys_admin'=>$this->_request->getParam('sys_admin'),'general_admin'=>$this->_request->getParam('general_admin'),'finance_manager'=>$this->_request->getParam('finance_manager'));
		$duplicate_arr = $new_arr = array();
		foreach ($employee_array as $role => $value) {
			if($value!=''){
			  if (!isset($new_arr[$value])) {
				 $new_arr[$value] = $role;
			  } else {
				if (isset($duplicate_arr[$value])) {
				   $duplicate_arr[$value][] = $role;
				} else {
				   $duplicate_arr[$value] = array($role);
				   // Comment out the previous line, and uncomment the following line to
				   // include the initial key in the duplicate_arr.
				   // $duplicate_arr[$val] = array($new_arr[$val], $key);
				}
			  }
			} 
		}
		$duplicate_exist = 0;
		if(count($duplicate_arr)>0){
			$duplicate_exist = 1;
			foreach($duplicate_arr as $val_arr)
			{
				foreach($val_arr as $dup_val){
					$msgarray[$dup_val] = 'Employee has already been selected.';
				}
			}
			//$this->view->msgarray = $msgarray;
		}
		if($epSettingsForm->isvalid($this->_request->getPost()) && !$duplicate_exist)
		{
			
			/** capture form values **/
			$l2_manager =  $this->_request->getParam('l2_manager');
			$hr_manager = $this->_request->getParam('hr_manager');
			$sys_admin = $this->_request->getParam('sys_admin');
			$general_admin = $this->_request->getParam('general_admin');
			$finance_manager = $this->_request->getParam('finance_manager');
			$notice_period = $this->_request->getParam('notice_period');
			
			/** prepare data to insert into database **/
			if($con == 'add')
			{
				$data = array(
							'businessunit_id' => $businessunit_id,
							'department_id' => $department_id,
							//'config_arr' => "",
							'l2_manager' => $l2_manager,
							'hr_manager' => $hr_manager,
							'sys_admin' => $sys_admin,
							'general_admin' => $general_admin,
							'finance_manager' => $finance_manager,
							'notice_period' =>$notice_period,
							'modifiedby'=> $this->loggedInUser,
							'modifieddate'=> gmdate("Y-m-d H:i:s"),
							'createdby'=> $this->loggedInUser,
							'createddate'=> gmdate("Y-m-d H:i:s"),
							'isactive' => 1
				);
				
				$res = $this->epSettingsModel->saveExitProcSettings($data,'', $con);
			}
			
			else if($con == 'edit')
			{
				$data = array(
							//'config_arr' => "",
							'l2_manager' => $l2_manager,
							'hr_manager' => $hr_manager,
							'sys_admin' => $sys_admin,
							'general_admin' => $general_admin,
							'finance_manager' => $finance_manager,
							'notice_period' =>$notice_period,
							'modifiedby'=> $this->loggedInUser,
							'modifieddate'=> gmdate("Y-m-d H:i:s")						
				);
				
				$where = array( 'id =?' => $id);
				$res = $this->epSettingsModel->saveExitProcSettings($data, $where, $con);
			}
			if($res)
			{
				if($con == 'add')
				{
					/** insert into log manager table **/
					sapp_Global::logManager(EXIT_PROC_SETTINGS,1,$this->loggedInUser,$res);
			
					$msg = 'Exit procedure settings added successfully.';
				}
				else
				{
					/** insert into log manager table **/
					sapp_Global::logManager(EXIT_PROC_SETTINGS,2,$this->loggedInUser,$id);
					
					$msg = 'Exit procedure settings updated successfully.';
				}
				$this->_helper->getHelper('FlashMessenger')->addMessage(array('success' => $msg));
			}
			else
			{	
				if($con == 'add') $msg = 'Failed to add exit procedure settings. Please try again.';
				else $msg = 'Failed to update exit procedure settings. Please try again.';
				$this->_helper->getHelper('FlashMessenger')->addMessage(array('error' => $msg));
			}	

			$this->_redirect('exit/exitprocsettings');
		}	
		else
		{
			$validationMsgs = $epSettingsForm->getMessages();
			foreach($validationMsgs as $key => $val)
			{
				foreach($val as $key2 => $val2)
				{
					$msgarray[$key] = $val2;
					break;
				}
			}
			$this->view->msgarray = $msgarray;
		}
		if($businessunit_id && $con == 'add')
		{
			$hrOptions = $sysAdminOptions = $generalAdminOptions = $financeManagerOptions = '';

			/** fill departments data **/
			$departmentsObj = $this->epSettingsModel->getDepartments($businessunit_id);
			if(count($departmentsObj) > 0)
			{
				foreach($departmentsObj as $dept)
				{
					$epSettingsForm->department_id->addMultiOption($dept['id'],utf8_encode($dept['deptname']));
				}
				$epSettingsForm->setDefault('department_id',$department_id);
			}

			/** fill hr managers data **/
			$hrOptions = $this->epSettingsModel->getEmployeesDataByRole(HR_GROUP,$businessunit_id,$department_id,"");
			if(count($hrOptions) > 0)
			{
				foreach($hrOptions as $hrm)
				{
					$epSettingsForm->hr_manager->addMultiOption($hrm['user_id'],utf8_encode($hrm['userfullname']));
				}
			}

			/** fill l2 managers data **/
			$l2Options = $this->epSettingsModel->getEmployeesDataByRole(array(MANAGEMENT_GROUP,HR_GROUP,MANAGER_GROUP,SYSTEMADMIN_GROUP,EMPLOYEE_GROUP),$businessunit_id,$department_id,'multiple');
			if(count($l2Options) > 0)
			{
				foreach($l2Options as $l2m)
				{
					$epSettingsForm->l2_manager->addMultiOption($l2m['user_id'],utf8_encode($l2m['userfullname']));
				}
			}
			
			/** fill system admin data **/
			$sysAdminOptions = $this->epSettingsModel->getEmployeesDataByRole(SYSTEMADMIN_GROUP,$businessunit_id,$department_id, "");
			if(count($sysAdminOptions) > 0)
			{
				foreach($sysAdminOptions as $sysAdmin)
				{
					$epSettingsForm->sys_admin->addMultiOption($sysAdmin['user_id'],utf8_encode($sysAdmin['userfullname']));
				}
			}

			/** fill general admin data **/
			$generalAdminOptions = $this->epSettingsModel->getEmployeesDataByRole(array(MANAGER_GROUP,EMPLOYEE_GROUP),$businessunit_id, $department_id,'multiple');
			if(count($generalAdminOptions) > 0)
			{
				foreach($generalAdminOptions as $generalAdmin)
				{
					$epSettingsForm->general_admin->addMultiOption($generalAdmin['user_id'],utf8_encode($generalAdmin['userfullname']));
				}
			}			

			/** fill finance manager data **/
			$financeManagerOptions = $this->epSettingsModel->getEmployeesDataByRole(array(HR_GROUP,MANAGER_GROUP,EMPLOYEE_GROUP),$businessunit_id, $department_id,'multiple');
			if(count($financeManagerOptions) > 0)
			{
				foreach($financeManagerOptions as $financeManager)
				{
					$epSettingsForm->finance_manager->addMultiOption($financeManager['user_id'],utf8_encode($financeManager['userfullname']));
				}
			}			
		}
	}
	
	public function deleteAction()
	{
		$messages = array('message' => '','msgtype' => '');

		/**
		** check for logged in user role for delete privileges
		**/
		if(sapp_Global::_checkprivileges(EXIT_PROC_SETTINGS,$this->loggedInUserGroup,$this->loggedInUserRole,'delete') != 'Yes'){
				$messages['message'] = 'You are not authorized to delete exit procedure setting.';
				$messages['msgtype'] = 'error';
				$this->_helper->json($messages);
				return;
		} 

		
		/**
		** capture exit proc setting id 
		**/
		$id = $this->_request->getParam('objid');
		$id = (int)$id;
	
		$deleteflag=$this->_request->getParam('deleteflag');
		if(!empty($id))
		{
			/** prepare data object and where clause 
			** to update exit proc setting details in database
			**/
			//to check records are there not for that particular exit_setiins_id
			$result=$this->allExitProcsModel->getExitsettingsProcDetails($id);
			if(count($result)==0)
			{
				$data = array(
						'isactive'=> 0,
						'modifiedby'=> $this->loggedInUser,
						'modifieddate'=> gmdate("Y-m-d H:i:s")
				);
				$where = array('id=?' => $id);
				$res = $this->epSettingsModel->saveExitProcSettings($data,$where,'edit');

				if($res)
				{
					/** insert into log manager table **/
					sapp_Global::logManager(EXIT_PROC_SETTINGS,3,$this->loggedInUser,$id);

					$messages['message'] = 'Exit procedure setting deleted successfully.';
					$messages['msgtype'] = 'success';
				}
				else
				{
					$messages['message'] = 'Exit procedure setting is not deleted.';
					$messages['msgtype'] = 'error';
				}
			}
			else{
				$messages['message'] = 'Exit procedure settings cannot be deleted as exit process has been initialized.';
				$messages['msgtype'] = 'error';
			}
		}
		else
		{
			$messages['message'] = 'Exit procedure setting is not deleted.';
			$messages['msgtype'] = 'error';
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
	public function getdepartmentsAction()
	{
		$bunit_id = (int)$this->_request->getParam('bunitId');
		$l2Options = $hrOptions = $sysAdminOptions = $generalAdminOptions = $financeManagerOptions = '';
		$departmentsmodel = new Default_Model_Departments();
		$options = "<option value=''>Select Department</option>";
		// get configured department ids from settings table
		$departmentidsArr =  $this->epSettingsModel->getActiveDepartmentIds();
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
				$querystring = "Select d.id,d.deptname from main_departments as d where d.unitid=$bunit_id and d.isactive=1 and $where  ";
				$querystring .= "  order by d.deptname";
				$uniquedepartmentids = $departmentsmodel->getUniqueDepartments($querystring);
				/* if(empty($uniquedepartmentids))
				$flag = 'true';
					
				$this->view->uniquedepartmentids=$uniquedepartmentids; */
				
				
					if(count($uniquedepartmentids) > 0)
					{
						foreach($uniquedepartmentids as $dept)
						{
							$options .= "<option value='".$dept['id']."'>".$dept['deptname']."</option>";
						}
					}
					else
					{
						$options = "allconfigured";
					}
				
				
			}
			else{
				$departmentsObj = $this->epSettingsModel->getDepartments($bunit_id);
				
					if(count($departmentsObj) > 0)
					{
						foreach($departmentsObj as $dept)
						{
							$options .= "<option value='".$dept['id']."'>".$dept['deptname']."</option>";
						}
					}
					else
					{
						$options = "nodepartments";
					}
				
			}
		
		$dept_id='';
		$l2Options = $this->getEmployees('L2 Manager',array(MANAGEMENT_GROUP,HR_GROUP,MANAGER_GROUP,SYSTEMADMIN_GROUP,EMPLOYEE_GROUP),$bunit_id,$dept_id,'multiple');
		$hrOptions = $this->getEmployees('HR Manager',HR_GROUP,$bunit_id,$dept_id);
		$sysAdminOptions = $this->getEmployees('System Admin',SYSTEMADMIN_GROUP,$bunit_id,$dept_id);
		$generalAdminOptions = $this->getEmployees('General Admin',array(MANAGER_GROUP,EMPLOYEE_GROUP),$bunit_id,$dept_id,'multiple');
		$financeManagerOptions = $this->getEmployees('Finance Manager',array(HR_GROUP,MANAGER_GROUP,EMPLOYEE_GROUP),$bunit_id,$dept_id,'multiple');

		$this->_helper->json(array(
									'options' => $options,
									'l2Options' => $l2Options,
									'hrOptions' => $hrOptions,
									'sysAdminOptions' => $sysAdminOptions,
									'generalAdminOptions' => $generalAdminOptions,
									'financeManagerOptions' => $financeManagerOptions
								));
	}
	
	/**
	** function to retrieve employees in the dropdown
	** input parameter : 
	**	@@ param 1 - label to display in the drop down (Select ....)
	**	@@ param 2 - role groups to select employees, can be single or multiple (array)
	**	@@ param 3 - business unit id
	**  @@ param 4 - department id
	**  @@ param 5 - flag (one/multiple), based on role group parameter
	** output parameter :
	**	returns options html for dropdown
	**/
	public function getEmployees($labelStr,$empRole,$bunit_id,$dept_id='',$con='')
	{
		$employeeOptions = "<option value=''>Select ".$labelStr."</option>";
		
		$hrManagerObj = $this->epSettingsModel->getEmployeesDataByRole($empRole,$bunit_id, $dept_id,$con);
		if(count($hrManagerObj) > 0)
		{
			foreach($hrManagerObj as $hrm)
			{
				$employeeOptions .= "<option value='".$hrm['user_id']."'>".$hrm['userfullname']."</option>";
			}
		}
		else
		{
			$employeeOptions = "no".str_replace(" ","",$labelStr);
		}
		return $employeeOptions;
	}
}
?>