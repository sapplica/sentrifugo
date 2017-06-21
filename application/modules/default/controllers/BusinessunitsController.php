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

class Default_BusinessunitsController extends Zend_Controller_Action
{

    private $options;
    public function preDispatch()
    {		 		
    }
	/**
	 * Init
	 *
	 * @see Zend_Controller_Action::init()
	 */
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();	
		$this->businessunitsmodel = new Default_Model_Businessunits();
    }
	
    public function indexAction()
    {
	    $orgInfoModel = new Default_Model_Organisationinfo();
		$getorgData = $orgInfoModel->getorgrecords();
		    if(!empty($getorgData))
			{
			    $orgdata = ''; 
				$businessunitModel = new Default_Model_Businessunits();	
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
					$sort = 'DESC';$by = 'b.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
				}
				else 
				{
					$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
					$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'b.modifieddate';
					if($dashboardcall == 'Yes')
						$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
					else $perPage = $this->_getParam('per_page',PERPAGE);
					$pageNo = $this->_getParam('page', 1);
					$searchData = $this->_getParam('searchData');	
					$searchData = rtrim($searchData,',');					
				}
						
				$dataTmp = $businessunitModel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);
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
	
	public function departmentGrid($unitid)
	{	
		$deptModel = new Default_Model_Departments();	
		$sort = 'DESC';$by = 'd.modifieddate';$pageNo = 1;$searchData = '';$searchArray=array();
		$dashboardcall = $this->_getParam('dashboardcall');
		if($dashboardcall == 'Yes')
				$perPage = DASHBOARD_PERPAGE;
			else	
				$perPage = PERPAGE;
		$objName = 'departments';
		$tableFields = array('action'=>'Action','deptname' => 'Name','deptcode' =>'Code','startdate'=>'Started On','depthead'=>'Department Head','timezone'=>'Time Zone','unitname'=>'Business Unit');
		$tablecontent = $deptModel->getDepartmentsData($sort, $by, $pageNo, $perPage,'',$unitid);     
		$data = array();
		$dataTmp = array(
			'sort' => $sort,
			'by' => $by,
			'pageNo' => $pageNo,
			'perPage' => $perPage,				
			'tablecontent' => $tablecontent,
			'objectname' => $objName,
			'extra' => array(),
			'tableheader' => $tableFields,
			'jsGridFnName' => 'getAjaxgridData',
			'jsFillFnName' => '',
			'searchArray' => $searchArray,
			'formgrid' => 'true',
			'call'=>'',
			'dashboardcall'=>$dashboardcall,
			'search_filters' => array(
					'startdate' =>array('type'=>'datepicker')					
				)
			
		);		
		array_push($data,$dataTmp);
		return $data;
	}
	
	public function editAction()
	{
	    $orgInfoModel = new Default_Model_Organisationinfo();
		$getorgData = $orgInfoModel->getorgrecords();
		$sitepreferencemodel = new Default_Model_Sitepreference();
		$activerecordArr = $sitepreferencemodel->getActiveRecord();
		$timezoneid=!empty($activerecordArr[0]['timezoneid'])?$activerecordArr[0]['timezoneid']:'';
		$popConfigPermission = array();
		if(!empty($getorgData))
		{
		        $orgdata = '';
				$auth = Zend_Auth::getInstance();
				if($auth->hasIdentity()){
							$loginUserId = $auth->getStorage()->read()->id;
							$loginuserRole = $auth->getStorage()->read()->emprole;
							$loginuserGroup = $auth->getStorage()->read()->group_id;
				}				
				if(sapp_Global::_checkprivileges(TIMEZONE,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
					array_push($popConfigPermission,'timezone');
				}  
				if(sapp_Global::_checkprivileges(COUNTRIES,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
					array_push($popConfigPermission,'country');
				}
				if(sapp_Global::_checkprivileges(STATES,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
						array_push($popConfigPermission,'state');
				}
				if(sapp_Global::_checkprivileges(CITIES,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
						array_push($popConfigPermission,'city');
				}
				$msgarray = array(); $flag = 'true';
				$id = $this->getRequest()->getParam('id');
				$callval = $this->getRequest()->getParam('call');
				if($callval == 'ajaxcall')
					$this->_helper->layout->disableLayout();
				
				$countriesModel = new Default_Model_Countries();
				$statesmodel = new Default_Model_States();
				$citiesmodel = new Default_Model_Cities();
				$timezonemodel = new Default_Model_Timezone();
				
				$allTimezoneData = $timezonemodel->fetchAll('isactive=1','timezone')->toArray();	
				$allCountriesData = $countriesModel->fetchAll('isactive=1','country')->toArray();
				$allStatesData = $statesmodel->fetchAll('isactive=1','state')->toArray();
				$allCitiesData = $citiesmodel->fetchAll('isactive=1','city')->toArray();	
				
				$businessunitsform = new Default_Form_businessunits();
				$businessunitsmodel = new Default_Model_Businessunits();
				$orgInfoModel = new Default_Model_Organisationinfo();
				
				$deptModel = new Default_Model_Departments();
				$deptform = new Default_Form_departments(); 		
				$deptData = array();$msgarray = array();
			
				$businessunitsform->setAttrib('action',BASE_URL.'businessunits/edit');
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
                              
                $businessunitsform->setDefault('timezone',$timezoneid);
				$address = $getorgData[0]['address1'];
				if(isset($country) && $country != 0 && $country != '')
				{
					$businessunitsform->setDefault('country',$country);
					$statesData = $statesmodel->getBasicStatesList($country);
					foreach($statesData as $res) 
					$businessunitsform->state->addMultiOption($res['state_id_org'],utf8_encode($res['state']));
				    if(isset($state) && $state != 0 && $state != '')
						$businessunitsform->setDefault('state',$state);			
				}
				if(isset($state) && $state != 0 && $state != ''){
					$citiesData = $citiesmodel->getBasicCitiesList($state);
					foreach($citiesData as $res) 
					$businessunitsform->city->addMultiOption($res['city_org_id'],utf8_encode($res['city']));		
					if(isset($city) && $city != 0 && $city != '')
					$businessunitsform->setDefault('city',$city);			
				}
				if(isset($address) && $address !='')
				    $businessunitsform->address1->setValue($address);

				if(is_numeric($id) && $id > 0)
				{			
					$data = $businessunitsmodel->getSingleUnitData($id);
					if(!empty($data))
					{
						$businessunitsform->setAttrib('action',BASE_URL.'businessunits/edit/id/'.$id);
						$businessunitsform->populate($data);
						$businessunitsform->submit->setLabel('Update'); 
						$st_date = sapp_Global::change_date($data["startdate"], 'view');
						$businessunitsform->setDefault('start_date', $st_date);
						$businessunitsform->state->clearMultiOptions();
                                                $businessunitsform->city->clearMultiOptions();
                                                $businessunitsform->state->addMultiOption('',utf8_encode("Select State"));
                                                $businessunitsform->city->addMultiOption('',utf8_encode("Select City"));
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
						if($countryId !='')
						{
                                                    $statesData = $statesmodel->getBasicStatesList($countryId);
							
                                                    foreach($statesData as $res) 
							$businessunitsform->state->addMultiOption($res['state_id_org'],utf8_encode($res['state']));
                                                        
                                                    $businessunitsform->setDefault('country',$countryId);															
                                                    $businessunitsform->setDefault('state',$stateId);								
						}
                         if($stateId != '')
						{
							$citiesData = $citiesmodel->getBasicCitiesList($stateId);
							foreach($citiesData as $res) 
							$businessunitsform->city->addMultiOption($res['city_org_id'],utf8_encode($res['city']));
                           $businessunitsform->setDefault('city',$cityId);	
                        }
						
						$deptData = $deptModel->getAllDeptsForUnit($id);
						$this->view->ermsg = '';
						$this->view->datarr = $data;
					}else{
						$this->view->ermsg = 'nodata';
					}
				}else if($id != ''){
					$this->view->ermsg = 'nodata';
				}
				$deptaddpermission = sapp_Global::_checkprivileges(DEPARTMENTS,$loginuserGroup,$loginuserRole,'add');
				$this->view->deptaddpermission = $deptaddpermission;
				$this->view->dataArray = $this->departmentGrid($id);
				$this->view->deptData = sizeof($deptData);
				$this->view->form = $businessunitsform;
				$this->view->unitid = $id; 
			
					
				if(!empty($allCountriesData) && !empty($allStatesData) && !empty($allCitiesData) && !empty($allTimezoneData))
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
				$start_date = $this->_request->getParam('start_date');				
				$start_date =sapp_Global::change_date($start_date,'database');
				$this->view->msgarray = $msgarray;
				if($this->getRequest()->getPost())
				{
					if($businessunitsform->isValid($this->_request->getPost()) && $flag == 'true')
					{
						$unitname = $this->_request->getParam('unitname');
						$unitcode = $this->_request->getParam('unitcode');
						$description = $this->_request->getParam('description');
						
						$country = $this->_request->getParam('country');
						$state = intval($this->_request->getParam('state'));
						$city = $this->_request->getParam('city');
						$address1 = $this->_request->getParam('address1');
						$address2 = $this->_request->getParam('address2');
						$address3 = $this->_request->getParam('address3');
						$timezone = $this->_request->getParam('timezone');
						$unithead = $this->_request->getParam('unithead');				
						
						$unitcodeExistance = $businessunitsmodel->checkUnitCodeDuplicates($unitcode,$id);
						if(!$unitcodeExistance)
						{
							$date = new Zend_Date();
							$actionflag = '';
							$tableid  = ''; 
							   $data = array(
										'unitname'=>trim($unitname),
										'unitcode'=>trim($unitcode),
										'description'=>trim($description),
										'startdate'=>($start_date!=''?$start_date:NULL),
										'country'=>trim($country),
										'state'=>trim($state),
										'city'=>trim($city),
										'address1'=>trim($address1),
										'address2'=>trim($address2),
										'address3'=>trim($address3),
										'timezone'=>trim($timezone),
										'unithead'=>trim($unithead),
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
								$Id = $businessunitsmodel->SaveorUpdateBusinessUnits($data, $where);
								if($Id == 'update')
								{
								   $tableid = $id;
								   $this->_helper->getHelper("FlashMessenger")->addMessage("Business unit updated successfully.");
								}   
								else
								{
								   $tableid = $Id; 	
									$this->_helper->getHelper("FlashMessenger")->addMessage("Business unit added successfully.");					   
								}   
								$menuID = BUSINESSUNITS;
								$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
								$this->_redirect('businessunits');	
						}
						else
						{
							$msgarray['message'] = 'A Business Unit, with the given code, already exists.';
							$msgarray['msgtype'] = 'error';
							$this->view->messages = $msgarray;
						}
					}else
					{
						$messages = $businessunitsform->getMessages();
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
		$this->view->popConfigPermission = $popConfigPermission;
	}
	
	public function viewAction()
	{
	    $orgInfoModel = new Default_Model_Organisationinfo();
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
					$objName = 'businessunits';
					$businessunitsform = new Default_Form_businessunits();
					$deptModel = new Default_Model_Departments();
					$businessunitsform->removeElement("submit");
					$elements = $businessunitsform->getElements();
					if(count($elements)>0)
					{
						foreach($elements as $key=>$element)
						{
							if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
							$element->setAttrib("disabled", "disabled");
								}
						}
					}
					$businessunitsmodel = new Default_Model_Businessunits();			
					$data = $businessunitsmodel->getSingleUnitData($id);
					if(!empty($data))
					{
						$businessunitsform->populate($data);
						$countryId = $data['country'];$stateId = $data['state'];$cityId = $data['city'];
						if($countryId && $stateId)
						{
							$timezoneModel = new Default_Model_Timezone();
							$countrymodel = new Default_Model_Countries();
							$statesmodel = new Default_Model_States();
							$citiesmodel = new Default_Model_Cities();
							$statesData = $statesmodel->getBasicStatesList($countryId);
							$citiesData = $citiesmodel->getBasicCitiesList($stateId);
							foreach($statesData as $res) 
							$businessunitsform->state->addMultiOption($res['state_id_org'],utf8_encode($res['state']));
							foreach($citiesData as $res) 
							$businessunitsform->city->addMultiOption($res['city_org_id'],utf8_encode($res['city']));
							$businessunitsform->setDefault('country',$countryId);
							$businessunitsform->setDefault('state',$stateId);
							$businessunitsform->setDefault('city',$cityId);		
						}
						$st_date = sapp_Global::change_date($data["startdate"], 'view');
						$businessunitsform->setDefault('start_date', $st_date);
						$permission = sapp_Global::_checkprivileges(BUSINESSUNITS,$loginuserGroup,$loginuserRole,'edit');	
						$deptData = $deptModel->getAllDeptsForUnit($id);
						$this->view->deptData = sizeof($deptData);
						$this->view->dataArray = $this->departmentGrid($id);
						$this->view->ermsg = '';
					}else{
						$this->view->ermsg = 'nodata';
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
					$this->view->editpermission = $permission;
					$this->view->controllername = $objName;
					$this->view->id = $id;
					$this->view->data = $data;
					$this->view->form = $businessunitsform;
					$this->view->role = $loginuserRole;
				}else{
					$this->view->ermsg = 'nodata';
				}
		}
        else
        {
		   $orgdata = 'noorgdata';
		   $this->view->orgdata = $orgdata ;
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
		 $actionflag = 3;
		    if($id)
			{
	    	  $businessunitsmodel = new Default_Model_Businessunits();
			  $checkdeptexistance = $businessunitsmodel->checkdeptstobusinessunits($id);
			  if($checkdeptexistance == 0)
			  {
				  $data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
				  $where = array('id=?'=>$id);
				  $Id = $businessunitsmodel->SaveorUpdateBusinessUnits($data, $where);
					if($Id == 'update')
					{
					 $menuID = BUSINESSUNITS;
					   $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id); 
					   $messages['message'] = 'Business unit deleted successfully.';
					   $messages['msgtype'] = 'success';
					}else{
						$messages['message'] = 'Business unit cannot be deleted.';
						$messages['msgtype'] = 'error';
					}
				}else{
					$messages['message'] = 'Since departments are associated with this business unit, you cannot delete the business unit.';		
					$messages['msgtype'] = 'error';
				}
			}
			else
			{ 
			 $messages['message'] = 'Business unit cannot be deleted.';
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
	
	public function getdeptnamesAction()
	{
		$this->_helper->layout->disableLayout();	
		$unitid = $this->_request->getparam('bunitid');
		$pageno = intval($this->_request->getParam('pageno',1));
		$perpage = intval($this->_request->getParam('perpage',PERPAGE));
		if($perpage == 0) $perpage = PERPAGE;
		$depts = $this->businessunitsmodel->getDeptForBusinessUnit($unitid,$pageno,$perpage);
		$deptCount = $this->businessunitsmodel->getDeptCountForBusinessUnit($unitid);
		if($deptCount > 0)
		{
			$lastpage =  ceil($deptCount/$perpage);
		}
		else
		{
			$lastpage = '';
			$deptCount = '';
		}
		$this->view->bunitid = $unitid;
		$this->view->deptArr = $depts;
		$this->view->pageno = $pageno;
		$this->view->perpage = $perpage;
		$this->view->lastpage = $lastpage;
	}
}