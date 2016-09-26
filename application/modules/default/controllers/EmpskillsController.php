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

class Default_EmpskillsController extends Zend_Controller_Action
{

	private $options;
	public function preDispatch()
	{

	}

	public function init()
	{
		$employeeModel = new Default_Model_Employee();
		$this->_options= $this->getInvokeArg('bootstrap')->getOptions();
	}
	public function indexAction()
	{
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);

		 if(in_array('emp_skills',$empOrganizationTabs)){
		 	$userID="";$levelsArr=array();$empcompetencyLevelsArr =array();
		 	$auth = Zend_Auth::getInstance();
		 	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginUserRole = $auth->getStorage()->read()->emprole;
					$loginUserGroup = $auth->getStorage()->read()->group_id;
		 	}
		 	$id = $this->getRequest()->getParam('userid');
		 	$conText ='';
		 	$call = $this->_getParam('call');
		 	if($call == 'ajaxcall')
		 	{
		 		$this->_helper->layout->disableLayout();
		 		$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
		 		$conText = ($this->_getParam('context') !='')? $this->_getParam('context'):$this->getRequest()->getParam('context');
		 	}
		 	if($id == '')		$id = $userID;
		 	$Uid = ($id)?$id:$userID;
		 	 
		 	$employeeModal = new Default_Model_Employee();
		 	try
		 	{
			    if($Uid && is_numeric($Uid) && $Uid>0)
				{
					$usersModel = new Default_Model_Users();
					$empdata = $employeeModal->getActiveEmployeeData($Uid);
					$employeeData = $usersModel->getUserDetailsByIDandFlag($Uid);
					if($empdata == 'norows')
					{
						$this->view->rowexist = "norows";
						$this->view->empdata = "";
					}
					else
					{
						$this->view->rowexist = "rows";
						if(!empty($empdata))
						{
							$empskillsModel = new Default_Model_Empskills();
							$view = Zend_Layout::getMvcInstance()->getView();
							$objname = $this->_getParam('objname');
							$refresh = $this->_getParam('refresh');
							$dashboardcall = $this->_getParam('dashboardcall',null);
							$data = array();	$searchQuery = '';	$searchArray = array();		$tablecontent = '';
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
								$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
								$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'e.modifieddate';
								if($dashboardcall == 'Yes')
								$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
								else
								$perPage = $this->_getParam('per_page',PERPAGE);

								$pageNo = $this->_getParam('page', 1);
								$searchData = $this->_getParam('searchData');
								$searchData = rtrim($searchData,',');
							}
							$dataTmp = $empskillsModel->getGrid($sort, $by, $perPage, $pageNo,$searchData,$call,$dashboardcall,$Uid,$conText);
							array_push($data,$dataTmp);
							$this->view->dataArray = $data;
							$this->view->call = $call ;
							$this->view->employeedata = $employeeData[0];
							$this->view->id = $id;
							$this->view->messages = $this->_helper->flashMessenger->getMessages();
						}
						$this->view->empdata = $empdata;
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
		 }else{
		 	$this->_redirect('error');
		 }
		}else{
			$this->_redirect('error');
		}
	}
	public function editAction()
	{
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);

		 if(in_array('emp_skills',$empOrganizationTabs)){
		 	$userID="";$levelsArr=array();$empcompetencyLevelsArr =array();
		 	$auth = Zend_Auth::getInstance();
		 	if($auth->hasIdentity()){
		 		$loginUserId = $auth->getStorage()->read()->id;
		 		$loginUserRole = $auth->getStorage()->read()->emprole;
		 		$loginUserGroup = $auth->getStorage()->read()->group_id;
		 	}
		 	$id = $this->getRequest()->getParam('userid');
		 	$conText ='';
		 	$call = $this->_getParam('call');
		 	if($call == 'ajaxcall')
		 	{
		 		$this->_helper->layout->disableLayout();
		 		$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
		 		$conText = ($this->_getParam('context') !='')? $this->_getParam('context'):$this->getRequest()->getParam('context');
		 	}
		 	if($id == '')		$id = $userID;
		 	$Uid = ($id)?$id:$userID;
		 	 
		 	$employeeModal = new Default_Model_Employee();
			$usersModel = new Default_Model_Users();
		 	try
		 	{
			    if($Uid && is_numeric($Uid) && $Uid>0 && $Uid!=$loginUserId)
				{
					$empdata = $employeeModal->getActiveEmployeeData($Uid);
					$employeeData = $usersModel->getUserDetailsByIDandFlag($Uid);
					if($empdata == 'norows')
					{
						$this->view->rowexist = "norows";
						$this->view->empdata = "";
					}
					else
					{
						$this->view->rowexist = "rows";
						if(!empty($empdata))
						{
							$empskillsModel = new Default_Model_Empskills();
							$view = Zend_Layout::getMvcInstance()->getView();
							$objname = $this->_getParam('objname');
							$refresh = $this->_getParam('refresh');
							$dashboardcall = $this->_getParam('dashboardcall',null);
							$data = array();	$searchQuery = '';	$searchArray = array();		$tablecontent = '';
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
								$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
								$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'e.modifieddate';
								if($dashboardcall == 'Yes')
								$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
								else
								$perPage = $this->_getParam('per_page',PERPAGE);

								$pageNo = $this->_getParam('page', 1);
								$searchData = $this->_getParam('searchData');
								$searchData = rtrim($searchData,',');
							}
							$dataTmp = $empskillsModel->getGrid($sort, $by, $perPage, $pageNo,$searchData,$call,$dashboardcall,$Uid,$conText);

							array_push($data,$dataTmp);
							$this->view->dataArray = $data;
							$this->view->call = $call ;
							$this->view->employeedata = $employeeData[0];
							$this->view->id = $id;
							$this->view->messages = $this->_helper->flashMessenger->getMessages();
						}
						$this->view->empdata = $empdata;
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
		 }else{
		 	$this->_redirect('error');
		 }
		}else{
			$this->_redirect('error');
		}
	}

	public function viewAction()
	{
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);

			if(in_array('emp_skills',$empOrganizationTabs)){
				$auth = Zend_Auth::getInstance();
				if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
				}
				$id = $this->getRequest()->getParam('userid');
				$conText='';$levelsArr=array();$empcompetencyLevelsArr =array();
				$call = $this->_getParam('call');
				if($call == 'ajaxcall')
				{
					$this->_helper->layout->disableLayout();
					$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
					$conText = ($this->_getParam('context') !='')? $this->_getParam('context'):$this->getRequest()->getParam('context');
				}
				if($id == '')		$id = $userID;
				$Uid = ($id)?$id:$userID;

				$employeeModal = new Default_Model_Employee();
				$usersModel = new Default_Model_Users();
				try
				{
					if($Uid && is_numeric($Uid) && $Uid>0 && $Uid!=$loginUserId)
				    {
						$empdata = $employeeModal->getActiveEmployeeData($Uid);
						$employeeData = $usersModel->getUserDetailsByIDandFlag($Uid);
						if($empdata == 'norows')
						{
							$this->view->rowexist = "norows";
							$this->view->empdata = "";
						}
						else
						{
							$this->view->rowexist = "rows";
							if(!empty($empdata))
							{
								$empskillsModel = new Default_Model_Empskills();

								$view = Zend_Layout::getMvcInstance()->getView();
								$objname = $this->_getParam('objname');
								$refresh = $this->_getParam('refresh');
								$dashboardcall = $this->_getParam('dashboardcall');
								$data = array();	$searchQuery = '';	$searchArray = array();		$tablecontent = '';
								if($refresh == 'refresh')
								{
									if($dashboardcall == 'Yes')
									$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
									else
									$perPage = $this->_getParam('per_page',PERPAGE);

									$sort = 'DESC';$by = 'e.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';	$searchArray = array();
								}
								else
								{
									$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
									$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'e.modifieddate';
									if($dashboardcall == 'Yes')
									$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
									else
									$perPage = $this->_getParam('per_page',PERPAGE);

									$pageNo = $this->_getParam('page', 1);
									$searchData = $this->_getParam('searchData');
									$searchData = rtrim($searchData,',');
								}
								$dataTmp = $empskillsModel->getGrid($sort, $by, $perPage, $pageNo,$searchData,$call,$dashboardcall,$Uid,$conText);

								array_push($data,$dataTmp);
								$this->view->dataArray = $data;
								$this->view->call = $call;
								$this->view->employeedata = $employeeData[0];
								$this->view->id = $id;
								$this->view->messages = $this->_helper->flashMessenger->getMessages();
							}
							$this->view->empdata = $empdata;
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
		 }else{
		 	$this->_redirect('error');
		 }
		}else{
			$this->_redirect('error');
		}
	}

	public function addpopupAction()
	{
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
                    $loginUserId = $auth->getStorage()->read()->id;
                    $loginuserRole = $auth->getStorage()->read()->emprole;
                    $loginuserGroup = $auth->getStorage()->read()->group_id;
		}
                $addcomp = sapp_Global::_checkprivileges(COMPETENCYLEVEL,$loginuserGroup,$loginuserRole,'add');
		$id = $this->getRequest()->getParam('unitId');
		if($id == '')
		$id = $loginUserId;
		// For open the form in popup...
		$emptyFlag=0;
		$empskillsform = new Default_Form_empskills();
		$competencylevelModel = new Default_Model_Competencylevel();
		$competencylevelArr = $competencylevelModel->getCompetencylevelList();
		$msgarray = array();
                $empskillsform->competencylevelid->addMultiOption('','Select Competency Level');
		if(!empty($competencylevelArr))
		{
			
			foreach ($competencylevelArr as $competencylevelres){
				$empskillsform->competencylevelid->addMultiOption($competencylevelres['id'],$competencylevelres['competencylevel']);
					
			}
		}else
		{
			$msgarray['competencylevelid'] = 'Competency levels are not configured yet.';
			$emptyFlag++;
		}
		$empskillsform->setAttrib('action',BASE_URL.'empskills/addpopup/unitId/'.$id);
		$this->view->form = $empskillsform;
		$this->view->msgarray = $msgarray;
		$this->view->emptyFlag = $emptyFlag;
		$this->view->controllername = 'empskills';
                $this->view->addcomp = $addcomp;
		if($this->getRequest()->getPost())
		{	
			$result = $this->save($empskillsform,$id);
			$this->view->msgarray = $result;
		}

	}

	public function viewpopupAction()
	{
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$id = $this->getRequest()->getParam('id');
		if($id == '')
		$id = $loginUserId;
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();
		$objName = 'empskills';
		$empskillsform = new Default_Form_empskills();
		$empskillsModel = new Default_Model_Empskills();
		$competencylevelModel = new Default_Model_Competencylevel();
		$empskillsform->removeElement("submit");
		$elements = $empskillsform->getElements();
			
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)
			{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
					$element->setAttrib("disabled", "disabled");
				}
			}
		}
		$data = $empskillsModel->getsingleEmpSkillsData($id);
		if(!empty($data))
		{
			$singlecompetencylevelArr = $competencylevelModel->getsingleCompetencyLevelData($data[0]['competencylevelid']);
			if($singlecompetencylevelArr !='norows')
			{
			 $empskillsform->competencylevelid->addMultiOption($singlecompetencylevelArr[0]['id'],$singlecompetencylevelArr[0]['competencylevel']);
	         $data[0]['competencylevelid']=$singlecompetencylevelArr[0]['competencylevel'];
			}
			else
			{
				$data[0]['competencylevelid']="";
			}
			$empskillsform->populate($data[0]);
			$empskillsform->setDefault('competencylevelid',$data[0]['competencylevelid']);
			$year_skill_last_used = sapp_Global::change_date($data[0]['year_skill_last_used'], 'view');
			$empskillsform->year_skill_last_used->setValue($year_skill_last_used);

		}
		$this->view->controllername = $objName;
		$this->view->id = $id;
		$this->view->data = $data[0];
		$this->view->form = $empskillsform;
	}

	public function editpopupAction()
	{
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->getRequest()->getParam('id');
		$userid = $this->getRequest()->getParam('unitId');
		if($id == '')
		$id = $loginUserId;
		// For open the form in popup...

		$empskillsform = new Default_Form_empskills();
		$empskillsModel = new Default_Model_Empskills();
		$competencylevelModel = new Default_Model_Competencylevel();
		$competencylevelArr = $competencylevelModel->getCompetencylevelList();

		if(sizeof($competencylevelArr)>0)
		{
			$empskillsform->competencylevelid->addMultiOption('','Select Competency Level');
			foreach ($competencylevelArr as $competencylevelres){
				$empskillsform->competencylevelid->addMultiOption($competencylevelres['id'],$competencylevelres['competencylevel']);
					
			}
		}
		if($id)
		{
			$data = $empskillsModel->getsingleEmpSkillsData($id);
			if(!empty($data))
			{
				$empskillsform->populate($data[0]);
				$empskillsform->setDefault('competencylevelid',$data[0]['competencylevelid']);
				$year_skill_last_used = sapp_Global::change_date($data[0]['year_skill_last_used'], 'view');
				$empskillsform->year_skill_last_used->setValue($year_skill_last_used);
			}
		}
		$empskillsform->setAttrib('action',BASE_URL.'empskills/editpopup/unitId/'.$userid);
		$this->view->form = $empskillsform;
		$this->view->controllername = 'empskills';

		if($this->getRequest()->getPost())
		{	
			$result = $this->save($empskillsform,$userid);
			$this->view->msgarray = $result;
		}

	}


	public function save($empskillsform,$userid)
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		if($empskillsform->isValid($this->_request->getPost())){
			$empskillsModal = new Default_Model_Empskills();
			$id = $this->_request->getParam('id');
			$user_id = $userid;
			$skillname = $this->_request->getParam('skillname');
			$yearsofexp = $this->_request->getParam('yearsofexp');
			$competencylevelid = $this->_request->getParam('competencylevelid');
			$year_skill_last_used = $this->_request->getParam('year_skill_last_used',null);
			$year_skill_last_used = sapp_Global::change_date($year_skill_last_used, 'database');
			$date = new Zend_Date();
			$actionflag = '';
			$tableid  = '';

			$data = array('user_id'=>$user_id,
				                 'skillname'=>$skillname,
								 'yearsofexp'=>$yearsofexp,
                                 'competencylevelid'=>$competencylevelid, 								 
				      			 'year_skill_last_used'=>($year_skill_last_used!=''?$year_skill_last_used:NUll),
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
			$Id = $empskillsModal->SaveorUpdateEmpSkills($data, $where);
			if($Id == 'update')
			{
				$tableid = $id;
				$this->view->successmessage = 'Employee skills updated successfully.';
			}
			else
			{
				$tableid = $Id;
				$this->view->successmessage = 'Employee skills added successfully.';
			}
			$menuID = EMPLOYEE;
			$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$user_id);
			$this->view->controllername = 'empskills';
			Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		}else
		{
			$messages = $empskillsform->getMessages();
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
		$messages['message'] = '';$messages['msgtype'] = '';$messages['flagtype'] = '';
		$actionflag = 3;
		if($id)
		{
			$empskillsModal = new Default_Model_Empskills();
			$data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
			$where = array('id=?'=>$id);
			$Id = $empskillsModal->SaveorUpdateEmpSkills($data, $where);
			if($Id == 'update')
			{
				$menuID = EMPLOYEE;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
				$messages['message'] = 'Employee skills deleted successfully.';
				$messages['msgtype'] = 'success';
			}
			else
			{
				$messages['message'] = 'Employee skills cannot be deleted.';
				$messages['msgtype'] = 'error';
			}
		}
		else
		{
			$messages['message'] = 'Employee skills cannot be deleted.';
			$messages['msgtype'] = 'error';
		}
		$this->_helper->json($messages);

	}



}