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
** controller used to configure work schedule
**/
class Default_WorkscheduleController extends Zend_Controller_Action
{
	private $workScheduleModel;
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
		
		/** Instantiate work schedule model **/
		$this->workScheduleModel = new Default_Model_Workschedule();
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
		**  get data object to build work shcedule grid
		**/
		$dataTmp = $this->workScheduleModel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall,$context);
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
		$objName = 'workschedule';

		if($id)
		{
			if(is_numeric($id) && $id>0)
			{
				// get details based on id
				$res = $this->workScheduleModel->getWorkScheduleDetails($id);
				$previ_data = sapp_Global::_checkprivileges(WORK_SCHEDULE,$login_group_id,$login_role_id,'view');
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
		if(sapp_Global::_checkprivileges(WORK_SCHEDULE,$this->loggedInUserGroup,$this->loggedInUserRole,'add') != 'Yes'){
				$this->view->ermsg = 'noprivileges';
				return;
		} 

		/**
		** Initiating work schedule form
		** and assigning action
		**/
		$workScheduleForm = new Default_Form_Workschedule();
		$workScheduleForm->setAttrib('action',BASE_URL.'default/workschedule/add');
		$this->view->form = $workScheduleForm;
	
		/**
		** populate business units on page load
		**/
		$workScheduleForm->businessunit_id->addMultiOption('','Select Business Unit');
		$businessUnitsObj = $this->workScheduleModel->getBusinessUnits();
		if(!empty($businessUnitsObj))
		{
			foreach($businessUnitsObj as $businessUnit)
			{
				$workScheduleForm->businessunit_id->addMultiOption($businessUnit['id'],utf8_encode($businessUnit['unitname']));
			}
		}	

		if($this->getRequest()->getPost())
		{
			$this->save($workScheduleForm,'add');
		}
	}
	
	public function editAction()
	{
		/**
		** check for logged in user role for add privileges
		**/
		if(sapp_Global::_checkprivileges(WORK_SCHEDULE,$this->loggedInUserGroup,$this->loggedInUserRole,'edit') != 'Yes'){
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
			$res = $this->workScheduleModel->getWorkScheduleDetails($id);
			
			if(empty($res))
			{
				$this->view->ermsg = 'nodata';
				return;
			}
			if(!empty($res))
			{
				$businessunit_id = !empty($res[0]['businessunit_id'])? $res[0]['businessunit_id']:'';
				$department_id =  !empty($res[0]['department_id'])? $res[0]['department_id']:'';
				$startdate =  !empty($res[0]['startdate'])? $res[0]['startdate']:'';
				$enddate = !empty($res[0]['enddate'])? $res[0]['enddate']:'';
				$sun_duration = !empty($res[0]['sun_duration'])? $res[0]['sun_duration']:'';
				$mon_duration = !empty($res[0]['mon_duration'])? $res[0]['mon_duration']:'';
				$tue_duration = !empty($res[0]['tue_duration'])? $res[0]['tue_duration']:'';
				$wed_duration = !empty($res[0]['wed_duration'])? $res[0]['wed_duration']:'';
				$thu_duration = !empty($res[0]['thu_duration'])? $res[0]['thu_duration']:'';
				$fri_duration = !empty($res[0]['fri_duration'])? $res[0]['fri_duration']:'';
				$sat_duration = !empty($res[0]['sat_duration'])? $res[0]['sat_duration']:'';
			}
			
			/**
			** Initiating work schedule form
			** and assigning action
			**/
			$workScheduleForm = new Default_Form_Workschedule();
			$workScheduleForm->setAttrib('action',BASE_URL.'default/workschedule/edit/id/'.$id);
			$this->view->form = $workScheduleForm;
			$workScheduleForm->submit->setLabel('Update');
			if(!empty($res))
			{
				$workScheduleForm->populate($res[0]);
			}
			/**
			** populate business units on page load
			**/
			$workScheduleForm->businessunit_id->addMultiOption('','Select Business Unit');
			$businessUnitsObj = $this->workScheduleModel->getBusinessUnits();
			if(!empty($businessUnitsObj))
			{
				foreach($businessUnitsObj as $businessUnit)
				{
					$workScheduleForm->businessunit_id->addMultiOption($businessUnit['id'],utf8_encode($businessUnit['unitname']));
				}
				$workScheduleForm->setDefault('businessunit_id',$businessunit_id);
			}	

			/**
			** populate departments on page load
			**/
			$workScheduleForm->department_id->addMultiOption('','Select Department');
			$departmentsObj = $this->workScheduleModel->getDepartments($businessunit_id,'','all');
			if(!empty($departmentsObj))
			{
				foreach($departmentsObj as $departments)
				{
					$workScheduleForm->department_id->addMultiOption($departments['id'],utf8_encode($departments['deptname']));
				}
				$workScheduleForm->setDefault('department_id',$department_id);
			}	

			if($this->getRequest()->getPost())
			{
				$this->save($workScheduleForm,'edit',$id);
			}
		}		
		else
		{
			$this->view->ermsg = 'invalidURL';
		}
	}

	public function save($workScheduleForm,$con,$id = '')
	{
		$businessunit_id = $this->_request->getParam('businessunit_id');
		$department_id = $this->_request->getParam('department_id');
		
		if($workScheduleForm->isvalid($this->_request->getPost()))
		{
			
			/** capture form values **/
			$startdate =  $this->_request->getParam('startdate');
			$enddate =  $this->_request->getParam('enddate');
			$sun_duration =  $this->_request->getParam('sun_duration');
			$mon_duration =  $this->_request->getParam('mon_duration');
			$tue_duration =  $this->_request->getParam('tue_duration');
			$wed_duration =  $this->_request->getParam('wed_duration');
			$thu_duration =  $this->_request->getParam('thu_duration');
			$fri_duration =  $this->_request->getParam('fri_duration');
			$sat_duration =  $this->_request->getParam('sat_duration');
			
			/** prepare data to insert into database **/
			if($con == 'add')
			{
				$data = array(
							'businessunit_id' => $businessunit_id,
							'department_id' => $department_id,
							'startdate' => $startdate,
							'enddate' => $enddate,
							'sun_duration' => $sun_duration,
							'mon_duration' => $mon_duration,
							'tue_duration' => $tue_duration,
							'wed_duration' => $wed_duration,
							'thu_duration' => $thu_duration,
							'fri_duration' => $fri_duration,
							'sat_duration' => $sat_duration,
							'createdby'=> $this->loggedInUser,
							'modifiedby'=> $this->loggedInUser,
							'createddate'=> gmdate("Y-m-d H:i:s"),
							'modifieddate'=> gmdate("Y-m-d H:i:s"),
							'isactive' => 1
				);
				
				$res = $this->workScheduleModel->saveWorkSchedule($data,'', $con);
			}
			
			else if($con == 'edit')
			{
				$data = array(
							'businessunit_id' => $businessunit_id,
							'department_id' => $department_id,
							'startdate' => $startdate,
							'enddate' => $enddate,
							'sun_duration' => $sun_duration,
							'mon_duration' => $mon_duration,
							'tue_duration' => $tue_duration,
							'wed_duration' => $wed_duration,
							'thu_duration' => $thu_duration,
							'fri_duration' => $fri_duration,
							'sat_duration' => $sat_duration,
							'modifiedby'=> $this->loggedInUser,
							'modifieddate'=> gmdate("Y-m-d H:i:s")						
				);
				
				$where = array( 'id =?' => $id);
				$res = $this->workScheduleModel->saveWorkSchedule($data, $where, $con);
			}
			if($res)
			{
				if($con == 'add')
				{
					/** insert into log manager table **/
					sapp_Global::logManager(WORK_SCHEDULE,1,$this->loggedInUser,$res);
			
					$msg = 'Work schedule added successfully.';
				}
				else
				{
					/** insert into log manager table **/
					sapp_Global::logManager(WORK_SCHEDULE,2,$this->loggedInUser,$id);
					
					$msg = 'Work schedule updated successfully.';
				}
				$this->_helper->getHelper('FlashMessenger')->addMessage(array('success' => $msg));
			}
			else
			{	
				if($con == 'add') $msg = 'Failed to add work schedule. Please try again.';
				else $msg = 'Failed to update work schedule. Please try again.';
				$this->_helper->getHelper('FlashMessenger')->addMessage(array('error' => $msg));
			}	

			$this->_redirect('default/workschedule');
		}	
		else
		{
			$validationMsgs = $workScheduleForm->getMessages();
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
			$departmentsObj = $this->workScheduleModel->getDepartments($businessunit_id);
			if(count($departmentsObj) > 0)
			{
				foreach($departmentsObj as $dept)
				{
					$workScheduleForm->department_id->addMultiOption($dept['id'],utf8_encode($dept['deptname']));
				}
				$workScheduleForm->setDefault('department_id',$department_id);
			}
		}
	}
	
	public function deleteAction()
	{
		$messages = array('message' => '','msgtype' => '');

		/**
		** check for logged in user role for delete privileges
		**/
		if(sapp_Global::_checkprivileges(WORK_SCHEDULE,$this->loggedInUserGroup,$this->loggedInUserRole,'delete') != 'Yes'){
				$messages['message'] = 'You are not authorized to delete work schedule.';
				$messages['msgtype'] = 'error';
				$this->_helper->json($messages);
				return;
		} 

		
		/**
		** capture work schedule id 
		**/
		$id = $this->_request->getParam('objid');
		$id = (int)$id;
	
		$deleteflag=$this->_request->getParam('deleteflag');
		if(!empty($id))
		{
			$data = array(
					'isactive'=> 0,
					'modifiedby'=> $this->loggedInUser,
					'modifieddate'=> gmdate("Y-m-d H:i:s")
			);
			$where = array('id=?' => $id);
			$res = $this->workScheduleModel->saveWorkSchedule($data,$where,'edit');

			if($res)
			{
				/** insert into log manager table **/
				sapp_Global::logManager(WORK_SCHEDULE,3,$this->loggedInUser,$id);

				$messages['message'] = 'Work schedule deleted successfully.';
				$messages['msgtype'] = 'success';
			}
			else
			{
				$messages['message'] = 'Work schedule is not deleted.';
				$messages['msgtype'] = 'error';
			}
		}
		else
		{
			$messages['message'] = 'Work schedule is not deleted.';
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

		$departmentsObj = $this->workScheduleModel->getDepartments($bunit_id);
	
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
		
		$dept_id='';

		$this->_helper->json(array(
									'options' => $options,
								));
	}
	
}
?>