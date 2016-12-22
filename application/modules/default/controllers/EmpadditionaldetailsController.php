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

class Default_EmpadditionaldetailsController extends Zend_Controller_Action
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

		 if(in_array('emp_additional',$empOrganizationTabs)){
		 	$userID="";
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
		 	if($id == '')		
			  $id = $userID;
			  
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
							$empadditionaldetailsModal = new Default_Model_Empadditionaldetails();
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
							$dataTmp = $empadditionaldetailsModal->getGrid($sort, $by, $perPage, $pageNo,$searchData,$call,$dashboardcall,$Uid,$conText);
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

		 if(in_array('emp_additional',$empOrganizationTabs)){
		 	$userID="";
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
		 	if($id == '')		
			  $id = $userID;
			  
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
							$empadditionaldetailsModal = new Default_Model_Empadditionaldetails();
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
							$dataTmp = $empadditionaldetailsModal->getGrid($sort, $by, $perPage, $pageNo,$searchData,$call,$dashboardcall,$Uid,$conText);
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

		 if(in_array('emp_additional',$empOrganizationTabs)){
		 	$userID="";
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
		 	if($id == '')		
			  $id = $userID;
			  
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
							$empadditionaldetailsModal = new Default_Model_Empadditionaldetails();
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
							$dataTmp = $empadditionaldetailsModal->getGrid($sort, $by, $perPage, $pageNo,$searchData,$call,$dashboardcall,$Uid,$conText);
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

	public function addpopupAction()
	{
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->getRequest()->getParam('unitId');
		if($id == '')
		$id = $loginUserId;
		// For open the form in popup...
		$emptyFlag=0;
	    $empadditionaldetailsform = new Default_Form_empadditionaldetails();
		$countriesModel = new Default_Model_Countries();
		$veteranstatusmodel = new Default_Model_Veteranstatus();
		$militaryservicemodel = new Default_Model_Militaryservice();
		
		$msgarray = array();

		$countrieslistArr = $countriesModel->getTotalCountriesList();
        $empadditionaldetailsform->countries_served->addMultiOption('','Select Country');
		if(!empty($countrieslistArr))
		{
			
			foreach ($countrieslistArr as $countrieslistres)
			{
				$empadditionaldetailsform->countries_served->addMultiOption($countrieslistres['id'],utf8_encode($countrieslistres['country_name']) );
			}
		}else
		{
			$msgarray['countries_served'] = 'Countries are not configured yet.';
			$emptyFlag++;
		}

		$militaryserviceArr = $militaryservicemodel->getTotalMilitaryServiceData();
        $empadditionaldetailsform->military_servicetype->addMultiOption('','Select Service Type');
		if(!empty($militaryserviceArr))
		{
			
			foreach ($militaryserviceArr as $militaryserviceres){
				$empadditionaldetailsform->military_servicetype->addMultiOption($militaryserviceres['id'],$militaryserviceres['militaryservicetype']);

			}
		}else
		{
			$msgarray['military_servicetype'] = 'Military service type not configured yet.';
			$emptyFlag++;
		}

		$veteranstatusArr = $veteranstatusmodel->getTotalVeteranStatusData();
        $empadditionaldetailsform->veteran_status->addMultiOption('','Select Veteran Status');
		if(!empty($veteranstatusArr))
		{
			foreach ($veteranstatusArr as $veteranstatusres){
				$empadditionaldetailsform->veteran_status->addMultiOption($veteranstatusres['id'],$veteranstatusres['veteranstatus']);

			}
		}else
		{
			$msgarray['veteran_status'] = 'Veteran status not configured yet.';
			$emptyFlag++;
		}
		
		$empadditionaldetailsform->setAttrib('action',BASE_URL.'empadditionaldetails/addpopup/unitId/'.$id);
		$this->view->form = $empadditionaldetailsform;
		$this->view->msgarray = $msgarray;
		$this->view->emptyFlag = $emptyFlag;
		$this->view->controllername = 'empadditionaldetails';

		if($this->getRequest()->getPost())
		{	
			$result = $this->save($empadditionaldetailsform,$id);
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
		$objName = 'empadditionaldetails';
		$empadditionaldetailsform = new Default_Form_empadditionaldetails();
		$empadditionaldetailsModal = new Default_Model_Empadditionaldetails();
		$countriesModel = new Default_Model_Countries();
		$veteranstatusmodel = new Default_Model_Veteranstatus();
		$militaryservicemodel = new Default_Model_Militaryservice();
		$empadditionaldetailsform->removeElement("submit");
		$elements = $empadditionaldetailsform->getElements();
			
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)
			{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
					$element->setAttrib("disabled", "disabled");
				}
			}
		}
		$data = $empadditionaldetailsModal->getEmpAdditionalDetailsData($id);
		if(!empty($data))
		{
			$singleCountryArr = $countriesModel->getCountryCode($data[0]['countries_served']);
			if(!empty($data[0]['military_servicetype']))
			{
				$singleMilitaryServiceArr = $militaryservicemodel->getMilitaryServiceDataByID($data[0]['military_servicetype']);
			}
			if(!empty($data[0]['veteran_status']))
			{
				$singleVeteranStatusArr = $veteranstatusmodel->getVeteranStatusDataByID($data[0]['veteran_status']);
			}
			if(!empty($singleCountryArr))
			 $empadditionaldetailsform->countries_served->addMultiOption($singleCountryArr[0]['id'],$singleCountryArr[0]['country_name']);
			if(!empty($singleMilitaryServiceArr))
			 $empadditionaldetailsform->military_servicetype->addMultiOption($singleMilitaryServiceArr[0]['id'],$singleMilitaryServiceArr[0]['militaryservicetype']);  
			if(!empty($singleVeteranStatusArr))
		     $empadditionaldetailsform->veteran_status->addMultiOption($singleVeteranStatusArr[0]['id'],$singleVeteranStatusArr[0]['veteranstatus']); 
			$empadditionaldetailsform->populate($data[0]);
			
			$empadditionaldetailsform->setDefault('countries_served',$data[0]['countries_served']);
			$empadditionaldetailsform->setDefault('military_servicetype',$data[0]['military_servicetype']);
			$empadditionaldetailsform->setDefault('veteran_status',$data[0]['veteran_status']);
				
			$from_date = sapp_Global::change_date($data[0]['from_date'], 'view');
			$to_date = sapp_Global::change_date($data[0]['to_date'], 'view');
			$empadditionaldetailsform->from_date->setValue($from_date);
			$empadditionaldetailsform->to_date->setValue($to_date);

		}
	                if($data[0]['military_status']==2)
					{
						$data[0]['military_status']="no";
					}
					else 
					{
						$data[0]['military_status']="yes";
					}
	                if(!empty($data[0]['countries_served'])) {
						$countryname = $countriesModel->getCountryCode($data[0]['countries_served']);
						if(!empty($countryname)){
							$data[0]['countries_served'] = $countryname[0]['country_name'];
						}
						else
						{
							$data[0]['countries_served'] = "";
						}
					}
	                if($data[0]['discharge_status']==2)
					{
						$data[0]['discharge_status']="Medical";
					}
					else if ($data[0]['discharge_status']==1)
					{
						$data[0]['discharge_status']="Honorable";
					}
					else 
					{ 
						$data[0]['discharge_status']="";
					}
					
	                if(!empty($data[0]['military_servicetype'])) {
						$milservicename = $militaryservicemodel->getsingleMilitaryServiceData($data[0]['military_servicetype']);
						if(!empty($milservicename)){
							$data[0]['military_servicetype'] = $milservicename['militaryservicetype'];
						}
						else
						{
							$data[0]['military_servicetype'] = "";
						}
					}
	                if(!empty($data[0]['veteran_status'])) {
						$veteranstatusname = $veteranstatusmodel->getsingleVeteranStatusData($data[0]['veteran_status']);
						if(!empty($veteranstatusname)){
							$data[0]['veteran_status'] = $veteranstatusname['veteranstatus'];
						}
						else
						{
							$data[0]['veteran_status'] = "";
						}
					}
		$this->view->controllername = $objName;
		$this->view->id = $id;
		$this->view->data= $data[0];
		
		$this->view->form = $empadditionaldetailsform;
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

		$empadditionaldetailsform = new Default_Form_empadditionaldetails();
		$empadditionaldetailsModal = new Default_Model_Empadditionaldetails();
		$countriesModel = new Default_Model_Countries();
		$veteranstatusmodel = new Default_Model_Veteranstatus();
		$militaryservicemodel = new Default_Model_Militaryservice();
		
		$countrieslistArr = $countriesModel->getTotalCountriesList();
                $empadditionaldetailsform->countries_served->addMultiOption('','Select Country');
		if(!empty($countrieslistArr))
		{
			
			foreach ($countrieslistArr as $countrieslistres)
			{
				$empadditionaldetailsform->countries_served->addMultiOption($countrieslistres['id'],utf8_encode($countrieslistres['country_name']) );
			}
		}

		$militaryserviceArr = $militaryservicemodel->getTotalMilitaryServiceData();
                $empadditionaldetailsform->military_servicetype->addMultiOption('','Select Service Type');
		if(!empty($militaryserviceArr))
		{
			
			foreach ($militaryserviceArr as $militaryserviceres){
				$empadditionaldetailsform->military_servicetype->addMultiOption($militaryserviceres['id'],$militaryserviceres['militaryservicetype']);

			}
		}

		$veteranstatusArr = $veteranstatusmodel->getTotalVeteranStatusData();
                $empadditionaldetailsform->veteran_status->addMultiOption('','Select Veteran Status');
		if(!empty($veteranstatusArr))
		{
			
			foreach ($veteranstatusArr as $veteranstatusres){
				$empadditionaldetailsform->veteran_status->addMultiOption($veteranstatusres['id'],$veteranstatusres['veteranstatus']);

			}
		}
		if($id)
		{
			$data = $empadditionaldetailsModal->getEmpAdditionalDetailsData($id);
			if(!empty($data))
			{
				$empadditionaldetailsform->populate($data[0]);
				$empadditionaldetailsform->setDefault('countries_served',$data[0]['countries_served']);
				$empadditionaldetailsform->setDefault('military_servicetype',$data[0]['military_servicetype']);
				$empadditionaldetailsform->setDefault('veteran_status',$data[0]['veteran_status']);
				$from_date = sapp_Global::change_date($data[0]['from_date'], 'view');
				$to_date = sapp_Global::change_date($data[0]['to_date'], 'view');
				$empadditionaldetailsform->from_date->setValue($from_date);
				$empadditionaldetailsform->to_date->setValue($to_date);
				$empadditionaldetailsform->submit->setLabel('Update');
				$this->view->data = $data;
			}
		}
		$empadditionaldetailsform->setAttrib('action',BASE_URL.'empadditionaldetails/editpopup/unitId/'.$userid);
		$this->view->form = $empadditionaldetailsform;
		$this->view->controllername = 'empadditionaldetails';
		if($this->getRequest()->getPost())
		{	
			$result = $this->save($empadditionaldetailsform,$userid);
			$this->view->msgarray = $result;
		}
	}

	public function save($empadditionaldetailsform,$userid)
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		if($empadditionaldetailsform->isValid($this->_request->getPost())){
			$empadditionaldetailsModal = new Default_Model_Empadditionaldetails();
			$id = $this->_request->getParam('id');
			$user_id = $userid;
			$military_status = $this->_request->getParam('military_status');
			$countries_served = $this->_request->getParam('countries_served');
			$branch_service = trim($this->_request->getParam('branch_service'));
			$rank_achieved = trim($this->_request->getParam('rank_achieved'));
			$special_training = trim($this->_request->getParam('special_training'));
			$awards = trim($this->_request->getParam('awards'));
			$from_date = $this->_request->getParam('from_date');
			$from_date = sapp_Global::change_date($from_date, 'database');
			$to_date = $this->_request->getParam('to_date');
			$to_date = sapp_Global::change_date($to_date, 'database');
			$discharge_status = $this->_request->getParam('discharge_status');
			$service_number = trim($this->_request->getParam('service_number'));
			$rank = trim($this->_request->getParam('rank'));
			$verification_report = trim($this->_request->getParam('verification_report'));
			$military_servicetype = $this->_request->getParam('military_servicetype');
			$veteran_status = $this->_request->getParam('veteran_status');
			$date = new Zend_Date();
			$actionflag = '';
			$tableid  = '';
			$data = array('user_id'=>$user_id,
				                 'military_status'=>$military_status,
								 'countries_served'=>$countries_served,
                                 'branch_service'=>($branch_service!=''?$branch_service:NULL),
                                 'rank_achieved'=>($rank_achieved!=''?$rank_achieved:NULL),
                                 'special_training'=>($special_training!=''?$special_training:NULL),
                                 'awards'=>($awards!=''?$awards:NULL),    								 
				      			 'from_date'=>($from_date!=''?$from_date:NULL),
								 'to_date'=>($to_date!=''?$to_date:NULL),
								 'discharge_status'=>$discharge_status,
								 'service_number'=>($service_number!=''?$service_number:NULL),
                                 'rank'=>($rank!=''?$rank:NULL), 
                                 'verification_report'=>($verification_report!=''?$verification_report:NULL), 								 
				      			 'military_servicetype'=> $military_servicetype,
								 'veteran_status'=> $veteran_status,
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
			$Id = $empadditionaldetailsModal->SaveorUpdateEmpAdditionalData($data, $where);
			if($Id == 'update')
			{
				$tableid = $id;
				$this->view->successmessage = 'Employee additional details updated successfully.';
					
			}
			else
			{
				$tableid = $Id;
				$this->view->successmessage = 'Employee additional details added successfully.';
			}
			$menuID = EMPLOYEE;
			$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$user_id);
			$this->view->controllername = 'empadditionaldetails';
			Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		}else
		{
			$messages = $empadditionaldetailsform->getMessages();
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
			$empadditionaldetailsModal = new Default_Model_Empadditionaldetails();
			$data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
			$where = array('id=?'=>$id);
			$Id = $empadditionaldetailsModal->SaveorUpdateEmpAdditionalData($data, $where);
			if($Id == 'update')
			{
				$menuID = EMPLOYEE;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
				$messages['message'] = 'Employee additional details deleted successfully.';
				$messages['msgtype'] = 'success';
			}
			else
			{
				$messages['message'] = 'Employee additional details cannot be deleted.';
				$messages['msgtype'] = 'error';
			}
		}
		else
		{
			$messages['message'] = 'Employee additional details cannot be deleted.';
			$messages['msgtype'] = 'error';
		}
		$this->_helper->json($messages);

	}
}