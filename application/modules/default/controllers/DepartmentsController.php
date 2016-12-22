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

class Default_DepartmentsController extends Zend_Controller_Action
{

    private $options;
    public function preDispatch()
    {		 		
    }
	
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();		
		 $this->departmentsmodel = new Default_Model_Departments();
    }

    public function indexAction()
    {
		$orgInfoModel = new Default_Model_Organisationinfo();
		$getorgData = $orgInfoModel->getorgrecords();
		if(!empty($getorgData))
		{
		        $orgdata = '';
				$deptModel = new Default_Model_Departments();	
				$call = $this->_getParam('call');
				$popup = $this->getRequest()->getParam('popup');
				if($call == 'ajaxcall' || $popup == 1)
						$this->_helper->layout->disableLayout();
				$dashboardcall = $this->_getParam('dashboardcall');
				$view = Zend_Layout::getMvcInstance()->getView();		
				$objname = $this->_getParam('objname');
				$refresh = $this->_getParam('refresh');
				$unitId = intval($this->_getParam('unitId'));if(!isset($unitId))$unitId = '';
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
					$sort = 'DESC';$by = 'd.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
				}
				else 
				{
					$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
					$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'d.modifieddate';
					if($dashboardcall == 'Yes')
						$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
					else $perPage = $this->_getParam('per_page',PERPAGE);
					$pageNo = $this->_getParam('page', 1);
					$searchData = $this->_getParam('searchData');	
					$searchData = rtrim($searchData,',');
					$searchQuery = '';
					$searchArray = array();
					$tablecontent='';			
				}
				$dataTmp = $deptModel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall,$unitId);				
					
				array_push($data,$dataTmp);
				$this->view->dataArray = $data;
				$this->view->call = $call ;
				$this->view->messages = $this->_helper->flashMessenger->getMessages();
		}
        else
		{
			$orgdata = 'noorgdata';
			$this->view->orgdata = $orgdata ;			
		} 		
	}
	
	public function viewAction()
	{
		
        $orgInfoModel = new Default_Model_Organisationinfo();
        $employeeModal = new Default_Model_Employee();
		$getorgData = $orgInfoModel->getorgrecords();
		if(!empty($getorgData))
		{
		        $orgdata = '';
				$auth = Zend_Auth::getInstance();
				if($auth->hasIdentity()){
							$loginUserId = $auth->getStorage()->read()->id;
							$loginuserRole = $auth->getStorage()->read()->emprole;
							$loginuserGroup = $auth->getStorage()->read()->group_id;
				}	
				$permission = 'No';
				$id = $this->getRequest()->getParam('id');
				if(is_numeric($id) && $id > 0)
				{
					
					$callval = $this->getRequest()->getParam('call');
					if($callval == 'ajaxcall')
						$this->_helper->layout->disableLayout();
					$popup = $this->getRequest()->getParam('popup');
					$bunitid = $this->getRequest()->getParam('unitId');
					$permission = sapp_Global::_checkprivileges(DEPARTMENTS,$loginuserGroup,$loginuserRole,'edit');	
					$form = new Default_Form_departments(); 		
					if($popup && $bunitid)
					{
						Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
						$this->view->popup=$popup;
						$this->view->unitid = $bunitid;
						$this->view->id = $id;
					}
					
					$objName = 'departments';		
					$form->removeElement("submit");
					$elements = $form->getElements();
					if(count($elements)>0)
					{
						foreach($elements as $key=>$element)
						{
							if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
							$element->setAttrib("disabled", "disabled");
								}
						}
					}
					$deptModel = new Default_Model_Departments();	
					$data = $deptModel->getSingleDepartmentData($id);
					if(!empty($data))
					{
						$countryId = $data['country'];$stateId = $data['state'];$cityId = $data['city'];
						
						if($countryId && $stateId)
						{
							$busineesUnitModel = new Default_Model_Businessunits();
							$timezoneModel = new Default_Model_Timezone();
							$countrymodel = new Default_Model_Countries();
							$statesmodel = new Default_Model_States();
							$citiesmodel = new Default_Model_Cities();
							$statesData = $statesmodel->getBasicStatesList($countryId);
							$citiesData = $citiesmodel->getBasicCitiesList($stateId);
							foreach($statesData as $res) 
							$form->state->addMultiOption($res['state_id_org'],utf8_encode($res['state']));
							foreach($citiesData as $res) 
							$form->city->addMultiOption($res['city_org_id'],utf8_encode($res['city']));
							
							$form->setDefault('country',$countryId);
							$form->setDefault('state',$stateId);
							$form->setDefault('city',$cityId);		
						}
						
					if(!empty($data['unitid'])) {
						$buname = $busineesUnitModel->getSingleUnitData($data['unitid']);
						
						if(!empty($buname)){
							$data['unitid'] = $buname['unitname'];
						}
					}
					else{
						$data['unitid'] = "";
					}
				    if(!empty($data['timezone'])) {
						$timezoneval = $timezoneModel->getsingleTimezoneData($data['timezone']);
						if(!empty($timezoneval)){
							$data['timezone'] = $timezoneval['timezone'].' ['. $timezoneval['timezone_abbr'].']';
						}
					}
					
					if(!empty($data['country'])) {
						$countryname = $countrymodel->getActiveCountryName($data['country']);
						if(!empty($countryname)){
							$data['country'] = $countryname[0]['country'];
						}
					}
					if(!empty($data['state'])) {
						$statename = $statesmodel->getStateNameData($data['state']);
						if(!empty($statename)){
							$data['state'] = $statename[0]['state'];
						}
					}	
					if(!empty($data['city'])) {
						$cityname = $citiesmodel->getCitiesNameData($data['city']);
						if(!empty($cityname)){
							$data['city'] = $cityname[0]['city'];
						}
					}		
					
						if($data["startdate"] != '')
						{
							$st_date = sapp_Global::change_date($data["startdate"], 'view');
							$form->setDefault('start_date', $st_date);
						}
						if(!empty($data['depthead'])){
							$empdata = $employeeModal->getsingleEmployeeData($data['depthead']);
						if(!empty($empdata) && $empdata != 'norows')	
						$form->depthead->addMultiOption($empdata[0]['user_id'],utf8_encode($empdata[0]['userfullname']));
						$data['depthead']=$empdata[0]['userfullname'];
						}
						else{
							$data['depthead']="";
						}
						$form->populate($data);
						$this->view->controllername = $objName;
						$this->view->id = $id;
						$this->view->data = $data;
						$this->view->form = $form;
						$this->view->role = $loginuserRole;
						$this->view->editpermission = $permission;
						$this->view->ermsg = '';
					}else{
						$this->view->ermsg = 'nodata';
					}
				}else {
					$this->view->ermsg = 'nodata';
				}
		}
        else
		{
			$orgdata = 'noorgdata';
			$this->view->orgdata = $orgdata ;			
		} 		
	}
	
	public function viewpopupAction()
	{
		$id = intVal($this->getRequest()->getParam('id'));
		if(is_int($id) && $id != 0)
		{
			
			
			$callval = $this->getRequest()->getParam('call');
			$popup = $this->getRequest()->getParam('popup');
			$unitId = $this->getRequest()->getParam('unitId');
			Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
			
			$objName = 'departments';
			$form = new Default_Form_departments(); 		
			$form->removeElement("submit");
			$elements = $form->getElements();
				
			if(count($elements)>0)
			{
				foreach($elements as $key=>$element)
				{
					if($key == "Edit")
					$key->setAttribute("onclick", "displaydeptform('".BASE_URL."'departments/editpopup/id/'.$id.'/unitId/'.$bunitid','')");
					if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
					$element->setAttrib("disabled", "disabled");
						}
				}
			}
			$deptModel = new Default_Model_Departments();	
			$data = $deptModel->getSingleDepartmentData($id);
			if(!empty($data))
			{
				$form->populate($data);
				$this->view->controllername = $objName;
				$this->view->id = $id;
				$this->view->form = $form;
				$this->view->ermsg = '';
			}else{
				$this->view->ermsg = 'nodata';
			}
		}else{
			$this->view->ermsg = 'nodata';
		}
	}
	public function editAction()
	{	
		$orgInfoModel = new Default_Model_Organisationinfo();
		$getorgData = $orgInfoModel->getorgrecords();
		$sitepreferencemodel = new Default_Model_Sitepreference();
		$activerecordArr = $sitepreferencemodel->getActiveRecord();
		$timezoneid=!empty($activerecordArr[0]['timezoneid'])?$activerecordArr[0]['timezoneid']:'';
		$popConfigPermission = array();
		$organisationHead = array();
		if(!empty($getorgData))
		{
		    $orgdata = '';
				$auth = Zend_Auth::getInstance();
				if($auth->hasIdentity()){
							$loginUserId = $auth->getStorage()->read()->id;
							$loginUserRole = $auth->getStorage()->read()->emprole;
							$loginUserGroup = $auth->getStorage()->read()->group_id;
				}
				if(sapp_Global::_checkprivileges(TIMEZONE,$loginUserGroup,$loginUserRole,'add') == 'Yes'){
					array_push($popConfigPermission,'timezone');
				}  
				if(sapp_Global::_checkprivileges(COUNTRIES,$loginUserGroup,$loginUserRole,'add') == 'Yes'){
						array_push($popConfigPermission,'country');
				}
				if(sapp_Global::_checkprivileges(STATES,$loginUserGroup,$loginUserRole,'add') == 'Yes'){
						array_push($popConfigPermission,'state');
				}
				if(sapp_Global::_checkprivileges(CITIES,$loginUserGroup,$loginUserRole,'add') == 'Yes'){
						array_push($popConfigPermission,'city');
				}
				if(sapp_Global::_checkprivileges(EMPLOYEE,$loginUserGroup,$loginUserRole,'add') == 'Yes'){
						array_push($popConfigPermission,'employee');
				}	
				$msgarray = array();$flag = 'true';
				$id = $this->getRequest()->getParam('id');
				$callval = $this->getRequest()->getParam('call');
				$deptModel = new Default_Model_Departments();		
				$deptform = new Default_Form_departments(); 
				$statesmodel = new Default_Model_States();
				$citiesmodel = new Default_Model_Cities();
				$countriesModel = new Default_Model_Countries();
				$statesmodel = new Default_Model_States();
				$citiesmodel = new Default_Model_Cities();
				$timezonemodel = new Default_Model_Timezone();
				$businessunitsmodel = new Default_Model_Businessunits();
				$orgInfoModel = new Default_Model_Organisationinfo();
				$employeeModal = new Default_Model_Employee();
				$allTimezoneData = $timezonemodel->fetchAll('isactive=1','timezone')->toArray();			
				$allCountriesData = $countriesModel->fetchAll('isactive=1','country')->toArray();
				$allStatesData = $statesmodel->fetchAll('isactive=1','state')->toArray();
				$allCitiesData = $citiesmodel->fetchAll('isactive=1','city')->toArray();	
				$allBusinessunitsData = $businessunitsmodel->fetchAll('isactive=1','unitname')->toArray();	
				$deptData = array();
				$deptform->setAttrib('action',BASE_URL.'departments/edit');	
				$country = $getorgData[0]['country'];
                                if(isset($_POST['country']))
                                {
                                    $country = $_POST['country'];
                                }
				$state = $getorgData[0]['state'];
                                if(isset($_POST['state']))
                                {
                                    $state = $_POST['state'];
                                }
				$city = $getorgData[0]['city'];
                                if(isset($_POST['city']))
                                {
                                    $city = $_POST['city'];
                                }
                 $deptform->setDefault('timezone',$timezoneid);
				$address = $getorgData[0]['address1'];
				
				$organisationHead = $employeeModal->getCurrentOrgHead();
				
				if(isset($country) && $country != 0 && $country != '')
				{
				    $deptform->setDefault('country',$country);
					$statesData = $statesmodel->getBasicStatesList($country);
					foreach($statesData as $res) 
					$deptform->state->addMultiOption($res['state_id_org'],utf8_encode($res['state']));
					if(isset($state) && $state != 0 && $state != '')
						$deptform->setDefault('state',$state);
				}
				if(isset($state) && $state != 0 && $state != ''){
					$citiesData = $citiesmodel->getBasicCitiesList($state);
					foreach($citiesData as $res) 
					$deptform->city->addMultiOption($res['city_org_id'],utf8_encode($res['city']));		
					if(isset($city) && $city != 0 && $city != '')
					$deptform->setDefault('city',$city);
				}
				if(isset($address) && $address !='')
				    $deptform->address1->setValue($address);
				
				if(is_numeric($id) && $id > 0)
				{
					$data = $deptModel->getSingleDepartmentData($id);
					if(!empty($data))
					{
						$deptform->setAttrib('action',BASE_URL.'departments/edit/id/'.$id);
						$managementUsersData = $deptModel->getDepartmenttHead($data['depthead']);
				    	foreach ($managementUsersData as $mgmtdata){
							$deptform->depthead->addMultiOption($mgmtdata['user_id'],$mgmtdata['userfullname']);
				    	}	
						$deptform->populate($data);
						$deptform->setDefault('depthead',$data['depthead']);
						$deptform->submit->setLabel('Update');
                                                $deptform->state->clearMultiOptions();
                                                $deptform->city->clearMultiOptions();
                                                $deptform->state->addMultiOption('',utf8_encode("Select State"));
                                                $deptform->city->addMultiOption('',utf8_encode("Select City"));
						$countryId = $data['country'];
                                                if(isset($_POST['country']))
                                                {
                                                    $countryId = $_POST['country'];
                                                }
						$stateId = $data['state'];
                                                if(isset($_POST['state']))
                                                {
                                                    $stateId = $_POST['state'];
                                                }
						$cityId = $data['city'];
                                                if(isset($_POST['city']))
                                                {
                                                    $cityId = $_POST['city'];
                                                }
						if($countryId != '')
						{
                                                    $statesData = $statesmodel->getBasicStatesList($countryId);
                                                    
                                                    foreach($statesData as $res) 
                                                    $deptform->state->addMultiOption($res['state_id_org'],utf8_encode($res['state']));
                                                    
                                                    $deptform->setDefault('country',$countryId);
                                                    $deptform->setDefault('state',$stateId);
                                                }
                                                if($stateId != '')
						{
                                                    $citiesData = $citiesmodel->getBasicCitiesList($stateId);
                                                    foreach($citiesData as $res) 
                                                        $deptform->city->addMultiOption($res['city_org_id'],utf8_encode($res['city']));
                                                    $deptform->setDefault('city',$cityId);	
                                                }
						if($data["startdate"] != '')
						{
							$st_date = sapp_Global::change_date($data["startdate"], 'view');
							$deptform->setDefault('start_date', $st_date);
						}
						$this->view->ermsg = '';
						$this->view->datarr = $data;
					}else{
						$this->view->ermsg = 'nodata';
					}
				}else if($id != ''){
					$this->view->ermsg = 'nodata';
				}else
				{
	    		    $managementUsersData = $deptModel->getDepartmenttHead('');
			    	foreach ($managementUsersData as $mgmtdata){
						$deptform->depthead->addMultiOption($mgmtdata['user_id'],$mgmtdata['userfullname']);
			    	}
				}
				$this->view->deptData = sizeof($deptData);
				$this->view->form = $deptform;
					
				if(!empty($allBusinessunitsData) && !empty($allCountriesData) && !empty($allStatesData) && !empty($allCitiesData) && !empty($allTimezoneData))
				{
					$this->view->configuremsg = '';
				}else{
					$this->view->configuremsg = 'notconfigurable';
				}		
				if(empty($allCountriesData))
				{
					$msgarray['country'] = 'Countries are not configured yet.';
					$flag = 'false';
				}
				if(empty($allStatesData))
				{
					$msgarray['state'] = 'States are not configured yet.';
					$flag = 'false';
				}
				if(empty($allCitiesData))
				{
					$msgarray['city'] = 'Cities are not configured yet.';
					$flag = 'false';
				}
				if(empty($allBusinessunitsData))
				{
					$msgarray['unitid'] = 'Business units are not added yet.';
					$flag = 'false';
				}
				if(empty($allTimezoneData))
				{
					$msgarray['timezone'] = 'Time zones are not configured yet.';
					$flag = 'false';
				}
				$this->view->msgarray = $msgarray;
				if($this->getRequest()->getPost())
				{
					 $deptname = trim($this->_request->getParam('deptname'));
					 $unitid = $this->_request->getParam('unitid');
					 if($deptname != '' && $unitid != '')
					 {
					 	if(!preg_match('/^(?![0-9]{4})[a-zA-Z0-9.\- ?]+$/', $deptname))
					 	{
					 		$msgarray['deptname'] = "Please enter valid department name.";
							$flag = 'false';
					 	}else
					 	{
						   $checkExists = $deptModel->checkExistance($deptname,$unitid,$id);
							 if($checkExists != 0)
							 {
								$msgarray['deptname'] = "Department name already exists.";
								$flag = 'false';
							 }	
					 	} 
					 }else $flag = 'false';
					 
					$start_date = $this->_request->getParam('start_date',null);				
					$start_date =sapp_Global::change_date($start_date,'database');
					if($deptform->isValid($this->_request->getPost()) && $flag == 'true')
					{
						$deptname = $this->_request->getParam('deptname');
						$deptcode = $this->_request->getParam('deptcode');
						$description = $this->_request->getParam('description');
						
						$country = $this->_request->getParam('country');
						$state = intval($this->_request->getParam('state'));
						$city = $this->_request->getParam('city');
						$address1 = $this->_request->getParam('address1');
						$address2 = $this->_request->getParam('address2');
						$address3 = $this->_request->getParam('address3');
						$unitid = $this->_request->getParam('unitid');
						$timezone = $this->_request->getParam('timezone');
						$depthead = $this->_request->getParam('depthead');				
						$deptcodeExistance = $deptModel->checkCodeDuplicates($deptcode,$id);
						if(!$deptcodeExistance)
						{
							$date = new Zend_Date();
							$actionflag = '';
							$tableid  = ''; 
							   $data = array(
										'deptname'=>trim($deptname),
										'deptcode'=>trim($deptcode),
										'description'=>trim($description),
										'startdate'=>($start_date!=''?$start_date:NULL),
										'country'=>trim($country),
										'state'=>trim($state),
										'city'=>trim($city),
										'address1'=>trim($address1),
										'address2'=>trim($address2),
										'address3'=>trim($address3),
										'timezone'=>trim($timezone),
										'unitid'=>$unitid,
										'depthead'=>trim($depthead),
										'modifiedby'=>$loginUserId,
										'modifieddate'=>$date->get('yyyy-MM-dd HH:mm:ss')
									);
								if($id!=''){
									$where = array('id=?'=>$id);  
									$actionflag = 2;
								}
								else
								{
									$data['createdby'] = $loginUserId;
									$data['createddate'] = $date->get('yyyy-MM-dd HH:mm:ss');
									$data['isactive'] = 1;
									$where = '';
									$actionflag = 1;
								}
								$Id = $deptModel->SaveorUpdateDepartmentsUnits($data, $where);
								
								/* Updating business unit and department for org head*/
								$emp_data = array(  
								'businessunit_id'=>$unitid,  
								'modifiedby'=>$loginUserId,				
								'modifieddate'=>gmdate("Y-m-d H:i:s")
								);
								$emp_where = array('user_id=?'=>$depthead);
								
								if($Id == 'update')
								{
								   $tableid = $id;
								   $emp_data['department_id'] = $id;
								   $this->_helper->getHelper("FlashMessenger")->addMessage("Department updated successfully.");
								}   
								else
								{
								   $tableid = $Id; 	
								   $emp_data['department_id'] = $Id;
									$this->_helper->getHelper("FlashMessenger")->addMessage("Department added successfully.");					   
								}  

								$employeeModal->SaveorUpdateEmployeeData($emp_data, $emp_where);
								$menuID = DEPARTMENTS;
								$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
                                                                $this->_redirect('departments');
						}
						else
						{
							$msgarray['message'] = 'A Department, with the given code, already exists.';
							$msgarray['msgtype'] = 'error';
							$this->view->messages = $msgarray;
						}
					}
					else
					{
						$messages = $deptform->getMessages();
						foreach ($messages as $key => $val)
							{
								foreach($val as $key2 => $val2)
								 {
									$msgarray[$key] = $val2;
									break;
								 }
								if(empty($allCountriesData))
								{
									$msgarray['country'] = 'Countries are not configured yet.';
								}if(empty($allStatesData)){
									$msgarray['state'] = 'States are not configured yet.';
								}if(empty($allCitiesData)){
									$msgarray['city'] = 'Cities are not configured yet.';
								}if(empty($allBusinessunitsData)){
									$msgarray['unitid'] = 'Business units are not added yet.';							
								}if(empty($allTimezoneData)){
									$msgarray['timezone'] = 'Time zones are not configured yet.';
								}
							}
						$this->view->msgarray = $msgarray;
					
					}
				}
		}
        else
		{
			$orgdata = 'noorgdata';
			$this->view->orgdata = $orgdata ;			
		} 				
		$this->view->popConfigPermission = $popConfigPermission;
		$this->view->organisationHead = $organisationHead;
	}
	
	public function editpopupAction()
	{
		
	    Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
	    $orgInfoModel = new Default_Model_Organisationinfo();
		$getorgData = $orgInfoModel->getorgrecords();
		$deptModel = new Default_Model_Departments();
		if(!empty($getorgData))
		{
		        $orgdata = '';
				
				$auth = Zend_Auth::getInstance();
				if($auth->hasIdentity()){
							$loginUserId = $auth->getStorage()->read()->id;
				}
				$msgarray = array(); $flag = 'true';
				$bunitid = $this->getRequest()->getParam('unitId');
				$id = intVal($this->getRequest()->getParam('id'));
				$countriesModel = new Default_Model_Countries();
				$statesmodel = new Default_Model_States();
				$citiesmodel = new Default_Model_Cities();
				$timezonemodel = new Default_Model_Timezone();
				$businessunitsmodel = new Default_Model_Businessunits();
				$allTimezoneData = $timezonemodel->fetchAll('isactive=1','timezone')->toArray();			
				$allCountriesData = $countriesModel->fetchAll('isactive=1','country')->toArray();
				$allStatesData = $statesmodel->fetchAll('isactive=1','state')->toArray();
				$allCitiesData = $citiesmodel->fetchAll('isactive=1','city')->toArray();	
				$allBusinessunitsData = $businessunitsmodel->fetchAll('isactive=1','unitname')->toArray();	
				$deptModel = new Default_Model_Departments();
				$deptform = new Default_Form_departments(); 
				$deptform->setAction(BASE_URL.'departments/editpopup/id/'.$id.'/unitId/'.$bunitid);
				$country = $getorgData[0]['country'];
                                if(isset($_POST['country']))
                                {
                                    $country = $_POST['country'];
                                }
				$state = $getorgData[0]['state'];
                                if(isset($_POST['state']))
                                {
                                    $state = $_POST['state'];
                                }
				$city = $getorgData[0]['city'];
                                if(isset($_POST['city']))
                                {
                                    $city = $_POST['city'];
                                }
				$address = $getorgData[0]['address1'];
				//department head data
					$managementUsersData = $deptModel->getDepartmenttHead('');
			    	foreach ($managementUsersData as $mgmtdata){
						$deptform->depthead->addMultiOption($mgmtdata['user_id'],$mgmtdata['userfullname']);
			    	}
				if(isset($country) && $country != 0 && $country != '')
				{
				    $deptform->setDefault('country',$country);
					$statesData = $statesmodel->getBasicStatesList($country);
					foreach($statesData as $res) 
					$deptform->state->addMultiOption($res['state_id_org'],utf8_encode($res['state']));
					if(isset($state) && $state != 0 && $state != '')
						$deptform->setDefault('state',$state);			
				}
				if(isset($state) && $state != 0 && $state != ''){
					$citiesData = $citiesmodel->getBasicCitiesList($state);
					foreach($citiesData as $res) 
					$deptform->city->addMultiOption($res['city_org_id'],utf8_encode($res['city']));		
					if(isset($city) && $city != 0 && $city != '')
					$deptform->setDefault('city',$city);			
				}
				if(isset($address) && $address !='')
				    $deptform->address1->setValue($address);
				
				$close = '';
				$controllername = 'departments';
				$deptData = array();
				if($id)
				{
					$data = $deptModel->getSingleDepartmentData($id);
					if(!empty($data))
					{
						$deptform->populate($data);
						$deptform->submit->setLabel('Update'); 
										$st_date = sapp_Global::change_date($data['startdate'], 'view');
						$deptform->setDefault('start_date', $st_date);
						$deptform->setDefault('start_date', $st_date);

                                                $deptform->state->clearMultiOptions();
                                                $deptform->city->clearMultiOptions();							
						$countryId = $data['country'];
                                                if(isset($_POST['country']))
                                                {
                                                    $countryId = $_POST['country'];
                                                }
						$stateId = $data['state'];
                                                if(isset($_POST['state']))
                                                {
                                                    $stateId = $_POST['state'];
                                                }
						$cityId = $data['city'];
                                                if(isset($_POST['city']))
                                                {
                                                    $cityId = $_POST['city'];
                                                }
						if($countryId != '')
						{
                                                    $statesmodel = new Default_Model_States();                                                    
                                                    $statesData = $statesmodel->getBasicStatesList($countryId);                                                    
                                                    foreach($statesData as $res) 
                                                        $deptform->state->addMultiOption($res['state_id_org'],utf8_encode($res['state']));                                                    
						}
                                                if($stateId != '')
						{
                                                    $citiesmodel = new Default_Model_Cities();
                                                    $citiesData = $citiesmodel->getBasicCitiesList($stateId);
                                                    foreach($citiesData as $res) 
                                                        $deptform->city->addMultiOption($res['city_org_id'],utf8_encode($res['city']));														
                                                }
                                                $deptform->setDefault('country',$countryId);
                                                $deptform->setDefault('state',$stateId);
                                                $deptform->setDefault('city',$cityId);	
                                                $this->view->ermsg = '';
                                                $this->view->datarr = $data;
					}else{
						$this->view->ermsg = 'nodata';
					}
				}
				$bname = $deptModel->getbusinessunitname($bunitid);
				$this->view->bunitname = $bname;
				$this->view->deptData = sizeof($deptData);
				$this->view->form = $deptform;
				$this->view->unitid = $bunitid;
				$this->view->controllername = $controllername;
					
				if(!empty($allBusinessunitsData) && !empty($allCountriesData) && !empty($allStatesData) && !empty($allCitiesData) && !empty($allTimezoneData))
				{
					$this->view->configuremsg = '';
				}else{
					$this->view->configuremsg = 'notconfigurable';
				}		
				if(empty($allCountriesData))
				{
					$msgarray['country'] = 'Countries are not configured yet.';
					$flag = 'false';
				}
				if(empty($allStatesData))
				{
					$msgarray['state'] = 'States are not configured yet.';
					$flag = 'false';
				}
				if(empty($allCitiesData))
				{
					$msgarray['city'] = 'Cities are not configured yet.';
					$flag = 'false';
				}		
				if(empty($allTimezoneData))
				{
					$msgarray['timezone'] = 'Time zones are not configured yet.';
					$flag = 'false';
				}
				$this->view->msgarray = $msgarray;
				if($this->getRequest()->getPost())
				{
					if($deptform->isValid($this->_request->getPost()) && $flag == 'true')
					{
						$deptname = $this->_request->getParam('deptname');
						$deptcode = $this->_request->getParam('deptcode');
						$description = $this->_request->getParam('description');
						$start_date = $this->_request->getParam('start_date',null);
						$start_date = sapp_Global::change_date($start_date, 'database');
						$country = $this->_request->getParam('country');
						$state = intval($this->_request->getParam('state'));
						$city = intval($this->_request->getParam('city'));
						$address1 = $this->_request->getParam('address1');
						$address2 = $this->_request->getParam('address2');
						$address3 = $this->_request->getParam('address3');
						$unitid = $this->_request->getParam('unitid');
						$timezone = $this->_request->getParam('timezone');
						$depthead = $this->_request->getParam('depthead');		
						if(!isset($unitid) || $unitid == '') $unitid = $bunitid;
						$deptcodeExistance = $deptModel->checkCodeDuplicates($deptcode,$id);
						if(!$deptcodeExistance)
						{
							$date = new Zend_Date();
							$actionflag = '';
							$tableid  = ''; 
							   $data = array(
										'deptname'		=>	trim($deptname),
										'deptcode'		=>	trim($deptcode),
										'description'	=>	trim($description),
										'startdate'     => ($start_date!=''?$start_date:NULL),
										'country'		=>	trim($country),
										'state'			=>	trim($state),
										'city'			=>	trim($city),
										'address1'		=>	trim($address1),
										'address2'		=>	trim($address2),
										'address3'		=>	trim($address3),
										'timezone'		=>	trim($timezone),
										'unitid'		=>	$unitid,
										'depthead'		=>	trim($depthead),
										'modifiedby'	=>	$loginUserId,
										'modifieddate'	=>	$date->get('yyyy-MM-dd HH:mm:ss')
									);
								if($id!=''){
									$where = array('id=?'=>$id);  
									$actionflag = 2;
								}
								else
								{
									$data['createdby'] = $loginUserId;
									$data['createddate'] = $date->get('yyyy-MM-dd HH:mm:ss');
									$data['isactive'] = 1;
									$where = '';
									$actionflag = 1;
								}
								$Id = $deptModel->SaveorUpdateDepartmentsUnits($data, $where);
								if($Id == 'update')
								{
								   $this->view->eventact = 'updated';
								   $tableid = $id;				  
								}   
								else
								{
									$this->view->eventact = 'added';
								   $tableid = $Id; 	                       
								}   
								$menuID = DEPARTMENTS;
								$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
								Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
								$close = 'close';
								$this->view->popup=$close;
							    $this->view->controllername = $controllername;
						}				
						else
						{
							$msgarray['message'] = 'A Department, with the given code, already exists.';
							$msgarray['msgtype'] = 'error';
							$this->view->messages = $msgarray;
						}
								
					}
					else
					{
						$messages = $deptform->getMessages();
						foreach ($messages as $key => $val)
							{
								foreach($val as $key2 => $val2)
								 {
									$msgarray[$key] = $val2;
									break;
								 }
								 if(empty($allCountriesData))
								{
									$msgarray['country'] = 'Countries are not configured yet.';
								}if(empty($allStatesData)){
									$msgarray['state'] = 'States are not configured yet.';
								}if(empty($allCitiesData)){
									$msgarray['city'] = 'Cities are not configured yet.';
								}if(empty($allTimezoneData)){
									$msgarray['timezone'] = 'Time zones are not configured yet.';
								}
							}
						$this->view->msgarray = $msgarray;
					
					}
				}
		}
        else
		{
			$orgdata = 'noorgdata';
			$this->view->orgdata = $orgdata ;			
		} 		
	}
	
	public function getdepartmentsAction()
	{
	    $ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('getdepartments', 'html')->initContext();
		$businessunit_id = $this->_request->getParam('buss_id');
		$positionsform = new Default_Form_positions();
		$deptModel = new Default_Model_Departments();
		$deptmodeldata = $deptModel->getDepartmentList($businessunit_id);
		$this->view->positionsform=$positionsform;
		$this->view->deptmodeldata=$deptmodeldata;
	
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
		 $actionflag = 3;
		    if($id)
			{
	    	  $departmentsmodel = new Default_Model_Departments();
			  $checkemployees = $departmentsmodel->checkemployeestodepartment($id);
			  if($checkemployees == 0)
			  {				
				  $data = array('isactive'=>0);
				  $where = array('id=?'=>$id);
				  $Id = $departmentsmodel->SaveorUpdateDepartmentsUnits($data, $where);
					if($Id == 'update')
					{
					   $menuID = DEPARTMENTS;
					   $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id); 
					   $messages['message'] = 'Department deleted successfully.';
					   $messages['msgtype'] = 'success';
					}else{
					   $messages['message'] = 'Department cannot be deleted.';			
					   $messages['msgtype'] = 'error';
					}
				}else{
						$messages['message'] = 'Please re-assign the employees to another department';
						$messages['msgtype'] = 'error';
				}
			}
			else
			{ 
			 $messages['message'] = 'Department cannot be deleted.';
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
	
	public function getempnamesAction()
	{
		$this->_helper->layout->disableLayout();	
		$deptid = $this->_request->getparam('deptid');
		$pageno = intval($this->_request->getParam('pageno',1));
		$perpage = intval($this->_request->getParam('perpage',PERPAGE));
		if($perpage == 0) $perpage = PERPAGE;		
		$emps = $this->departmentsmodel->getEmpForDepartment($deptid,$pageno,$perpage);
		$empCount = $this->departmentsmodel->getEmpCountForDepartment($deptid);
		if($empCount > 0)
		{
			$lastpage =  ceil($empCount/$perpage);
		}else{
			$lastpage = '';
			$empCount = '';
		}
		$this->view->deptid = $deptid;
		$this->view->empArr = $emps;
		$this->view->pageno = $pageno;
		$this->view->perpage = $perpage;
		$this->view->lastpage = $lastpage;
	}
}
?>