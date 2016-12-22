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

class Default_AgencylistController extends Zend_Controller_Action
{

	private $options;
	public function preDispatch()
	{

	}

	public function init()
	{
		$this->_options= $this->getInvokeArg('bootstrap')->getOptions();
	}
	
	/**
	* @name indexAction
	*
	* This method is used to display the agencies in a grid
	*
	*  @author Asma
	*  @version 1.0
	*/
	public function indexAction()
	{
		$agencylistmodel = new Default_Model_Agencylist();
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
		}
		$dataTmp = $agencylistmodel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);
			
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
	}
	
	/**
	* @name indexAction
	*
	* This method is used to add an agency. 
	* The details of that agency are stored in main_users and main_agencydetails table
	*  @author Asma
	*  @version 1.0
	*/
	public function addAction()
	{
		$popConfigPermission = array();
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
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
		if(sapp_Global::_checkprivileges(BGSCREENINGTYPE,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
				array_push($popConfigPermission,'screening');
		}
		
		$msgarray = array();
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();
		$checktypeModal = new Default_Model_Bgscreeningtype();
		$agencylistmodel = new Default_Model_Agencylist();
		$typesData = $checktypeModal->fetchAll('isactive=1','type')->toArray();
		if(!empty($typesData))
		{
			$this->view->configure = '';
		}else{
			$msgarray['bg_checktype'] = 'Screening types are not configured yet.';
			$this->view->configure = 'notconfigured';
		}
		$rolesData = $agencylistmodel->getagencyrole();
		if(!empty($rolesData))
		{
			$this->view->rolesconfigure = '';
		}else{
			$msgarray['emprole'] = 'Roles are not configured yet.';
			$this->view->rolesconfigure = 'notconfigured';
		}

		$agencylistform = new Default_Form_agencylist();
		$agencylistform->setAttrib('action',BASE_URL.'agencylist/add');
		$agencylistform->contact_type_1->setValue('1');
		$this->view->form = $agencylistform;
		$thirdpocid = '';$secondpocid = '';
		$this->view->msgarray = $msgarray;
		if($this->getRequest()->getPost())
		{
			$result = $this->save($agencylistform);
			$thirdpocid = $this->_request->getParam('thirdpocid');
			$secondpocid = $this->_request->getParam('secondpocid');
			$this->view->msgarray = $result;
			$this->view->messages = $result;
		}
		$this->view->thirdpocid = $thirdpocid;
		$this->view->secondpocid = $secondpocid;
		$this->view->popConfigPermission = $popConfigPermission;
	}
	/**
	* @name indexAction
	*
	* This function is used to populate the 3 point of contact details in the respective divs.  
	*  @author Asma
	*  @version 1.0
	*/
	public function getrecordData($id,$agencylistform,$agencylistmodel)
	{
		$returnData = array();
		$checktypeArr  = Array();$returnData['secondpocid'] = '';$returnData['thirdpocid']= '';$returnData['recordData'] = '';
		if($id)
		{
			$agencyData = $agencylistmodel->getSingleagencyPOCData($id);
				
			if(!empty($agencyData))
			{
				$agencylistform->setAttrib('action',BASE_URL.'agencylist/edit/id/'.$id);
				$agencylistform->populate($agencyData[0]);
				$checktypeArr = explode(',',$agencyData[0]['bg_checktype']);
				$agencylistform->bg_checktype->setValue($checktypeArr);
				$agencylistform->id->setValue($agencyData[0]['agencyid']);
				for($i=0; $i<sizeof($agencyData);$i++)
				{
					if($i == 0)
					{
						$agencylistform->pocid_1->setValue($agencyData[$i]['pocid']);
						$agencylistform->firstname_1->setValue($agencyData[$i]['first_name']);
						$agencylistform->lastname_1->setValue($agencyData[$i]['last_name']);
						$agencylistform->mobile_1->setValue($agencyData[$i]['contact_no']);
						$agencylistform->email_1->setValue($agencyData[$i]['email']);
						$agencylistform->location_1->setValue($agencyData[$i]['location']);
						$countryId = $agencyData[$i]['country'];
						if(isset($_POST['country_1']))
						{
							$countryId = $_POST['country_1'];
						}
						$stateId = $agencyData[$i]['state'];
						if(isset($_POST['state_1']))
						{
							$stateId = $_POST['state_1'];
						}
						$cityId = $agencyData[$i]['city'];
						if(isset($_POST['city_1']))
						{
							$cityId = $_POST['city_1'];
						}
						if($countryId != '')
						{
							$statesmodel = new Default_Model_States();
							$statesData = $statesmodel->getStatesList($countryId);
							foreach($statesData as $res)
							$agencylistform->state_1->addMultiOption($res['id'],utf8_encode($res['state_name']));
						}
						if($stateId != '')
						{
							$citiesmodel = new Default_Model_Cities();
							$citiesData = $citiesmodel->getCitiesList($stateId);
							foreach($citiesData as $res)
							$agencylistform->city_1->addMultiOption($res['id'],utf8_encode($res['city_name']));
						}
						$agencylistform->country_1->setValue($countryId);
						$agencylistform->state_1->setValue($stateId);
						$agencylistform->city_1->setValue($cityId);
						$agencylistform->contact_type_1->setValue($agencyData[$i]['contact_type']);
						$this->view->pocId_one = $agencyData[$i]['pocid'];
					}
					if($i == 1)
					{
						$agencylistform->pocid_2->setValue($agencyData[$i]['pocid']);
						$agencylistform->firstname_2->setValue($agencyData[$i]['first_name']);
						$agencylistform->lastname_2->setValue($agencyData[$i]['last_name']);
						$agencylistform->mobile_2->setValue($agencyData[$i]['contact_no']);
						$agencylistform->email_2->setValue($agencyData[$i]['email']);
						$agencylistform->location_2->setValue($agencyData[$i]['location']);
						$countryId = $agencyData[$i]['country'];
						if(isset($_POST['country_2']))
						{
							$countryId = $_POST['country_2'];
						}
						$stateId = $agencyData[$i]['state'];
						if(isset($_POST['state_2']))
						{
							$stateId = $_POST['state_2'];
						}
						$cityId = $agencyData[$i]['city'];
						if(isset($_POST['city_2']))
						{
							$cityId = $_POST['city_2'];
						}
						if($countryId != '')
						{
							$statesmodel = new Default_Model_States();
							$statesData = $statesmodel->getStatesList($countryId);
							foreach($statesData as $res)
							$agencylistform->state_2->addMultiOption($res['id'],utf8_encode($res['state_name']));
						}
						if($stateId != '')
						{
							$citiesmodel = new Default_Model_Cities();
							$citiesData = $citiesmodel->getCitiesList($stateId);
							foreach($citiesData as $res)
							$agencylistform->city_2->addMultiOption($res['id'],utf8_encode($res['city_name']));
						}
						$agencylistform->country_2->setValue($countryId);
						$agencylistform->state_2->setValue($stateId);
						$agencylistform->city_2->setValue($cityId);
						$agencylistform->contact_type_2->setValue($agencyData[$i]['contact_type']);
						$returnData['secondpocid'] = 'shown';
						$this->view->pocId_two = $agencyData[$i]['pocid'];
					}
					if($i == 2)
					{
						$agencylistform->pocid_3->setValue($agencyData[$i]['pocid']);
						$agencylistform->firstname_3->setValue($agencyData[$i]['first_name']);
						$agencylistform->lastname_3->setValue($agencyData[$i]['last_name']);
						$agencylistform->mobile_3->setValue($agencyData[$i]['contact_no']);
						$agencylistform->email_3->setValue($agencyData[$i]['email']);
						$agencylistform->location_3->setValue($agencyData[$i]['location']);
						$countryId = $agencyData[$i]['country'];
						if(isset($_POST['country_3']))
						{
							$countryId = $_POST['country_3'];
						}
						$stateId = $agencyData[$i]['state'];
						if(isset($_POST['state_3']))
						{
							$stateId = $_POST['state_3'];
						}
						$cityId = $agencyData[$i]['city'];
						if(isset($_POST['city_3']))
						{
							$cityId = $_POST['city_3'];
						}
						if($countryId != '')
						{
							$statesmodel = new Default_Model_States();
							$statesData = $statesmodel->getStatesList($countryId);
							foreach($statesData as $res)
							$agencylistform->state_3->addMultiOption($res['id'],utf8_encode($res['state_name']));
						}
						if($stateId != '')
						{
							$citiesmodel = new Default_Model_Cities();
							$citiesData = $citiesmodel->getCitiesList($stateId);
							foreach($citiesData as $res)
							$agencylistform->city_3->addMultiOption($res['id'],utf8_encode($res['city_name']));
						}
						$agencylistform->country_3->setValue($countryId);
						$agencylistform->state_3->setValue($stateId);
						$agencylistform->city_3->setValue($cityId);
						$agencylistform->contact_type_3->setValue($agencyData[$i]['contact_type']);
						$returnData['thirdpocid'] = 'shown';
						$this->view->pocId_three = $agencyData[$i]['pocid'];
					}
				}
			}
			else
			{

				$returnData['recordData'] = 'no data';
			}
		}
		return $returnData;
	}
	/**
	* @name indexAction
	*
	* This function is used to view the details of a particular agency.
	*  @author Asma
	*  @version 1.0
	*/
	public function viewAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
		$permission = 'No';
		$objName = 'agencylist';
		$id = intVal($this->getRequest()->getParam('id'));
		if(is_int($id) && $id != 0)
		{
			$callval = $this->getRequest()->getParam('call');
			if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
			$permission = sapp_Global::_checkprivileges(AGENCYLIST,$loginuserGroup,$loginuserRole,'edit');
			$agencylistform = new Default_Form_agencylist();
			$agencylistmodel = new Default_Model_Agencylist();
			$bgchecktype = new Default_Model_Bgscreeningtype();
			$countrymodel = new Default_Model_Countries();
			$statesmodel = new Default_Model_States();
			$citiesmodel = new Default_Model_Cities();
			$firstpocid = '';
			$secondpocid = ''; $thirdpocid = '';$recordData = '';
			$endData = $this->getrecordData($id,$agencylistform,$agencylistmodel);
			if($endData['secondpocid']) $secondpocid = $endData['secondpocid'];
			if($endData['thirdpocid']) $thirdpocid = $endData['thirdpocid'];
			$recordData = $endData['recordData'];
			$elements = $agencylistform->getElements();
			if(count($elements)>0)
			{
				foreach($elements as $key=>$element)
				{
					if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
						$element->setAttrib("disabled", "disabled");
					}
				}
			}
		
			$data = $agencylistmodel->getSingleAgencyData($id);
			$pocdata = $agencylistmodel->getSingleagencyPOCData($id);
            $bgscreeningtype='';
            $bgScreeningType=$bgchecktype->getSingleScreeningtypeNamesData($data['bg_checktype']);
			if(count($bgScreeningType)>0)
			{
				
				foreach($bgScreeningType as $checkname)
				{
				$bgscreeningtype.=$checkname['type'].',';
				}
				
			}
			
			$data['bg_checktype']= rtrim($bgscreeningtype, ',');
		    if(!empty($pocdata[0]['country'])) {
						$countryname = $countrymodel->getCountryCode($pocdata[0]['country']);
						if(!empty($countryname)){
							$pocdata[0]['country'] = $countryname[0]['country_name'];
						}
					}
					
		   if(!empty($pocdata[1]['country'])) {
						$countryname = $countrymodel->getCountryCode($pocdata[1]['country']);
						if(!empty($countryname)){
							$pocdata[1]['country'] = $countryname[0]['country_name'];
						}
					}
		   if(!empty($pocdata[2]['country'])) {
						$countryname = $countrymodel->getCountryCode($pocdata[2]['country']);
						if(!empty($countryname)){
							$pocdata[2]['country'] = $countryname[0]['country_name'];
						}
					}
		   if(!empty($pocdata[0]['state'])) {
						$statename = $statesmodel->getStateName($pocdata[0]['state']);
						if(!empty($statename)){
							$pocdata[0]['state'] = $statename[0]['statename'];
						}
					}
		   if(!empty($pocdata[1]['state'])) {
						$statename = $statesmodel->getStateName($pocdata[1]['state']);
						if(!empty($statename)){
							$pocdata[1]['state'] = $statename[0]['statename'];
						}
					}
		   if(!empty($pocdata[2]['state'])) {
						$statename = $statesmodel->getStateName($pocdata[2]['state']);
						if(!empty($statename)){
							$pocdata[2]['state'] = $statename[0]['statename'];
						}
					}
		   if(!empty($pocdata[0]['city'])) {
						$cityname = $citiesmodel->getCityName($pocdata[0]['city']);
						if(!empty($cityname)){
							$pocdata[0]['city'] = $cityname[0]['cityname'];
						}
					}
		   if(!empty($pocdata[1]['city'])) {
						$cityname = $citiesmodel->getCityName($pocdata[1]['city']);
						if(!empty($cityname)){
							$pocdata[1]['city'] = $cityname[0]['cityname'];
						}
					}
		   if(!empty($pocdata[2]['city'])) {
						$cityname = $citiesmodel->getCityName($pocdata[2]['city']);
						if(!empty($cityname)){
							$pocdata[2]['city'] = $cityname[0]['cityname'];
						}
					}
					//echo"<pre>";print_r($pocdata[1]);exit;
					if(!empty($pocdata[0]['contact_type'])) {		
		    if($pocdata[0]['contact_type'] == 1)
					{
					 
					 $pocdata[0]['contact_type']='Primary';   
					}
					else 
					{
					 $pocdata[0]['contact_type']='Secondary';
					}
					}
					 if(!empty($pocdata[1]['contact_type'])) {
			if($pocdata[1]['contact_type'] == 1)
					{
					 
					 $pocdata[1]['contact_type']='Primary';   
					}
					else 
					{
					 $pocdata[1]['contact_type']='Secondary';
					}	
					 }
					  if(!empty($pocdata[2]['contact_type'])) {	
			if($pocdata[2]['contact_type'] == 1)
					{
					 
					 $pocdata[2]['contact_type']='Primary';   
					}
					else 
					{
					 $pocdata[2]['contact_type']='Secondary';
					}
					  }				
            $this->view->record = $recordData;
			$this->view->permission = $permission;
			$this->view->form = $agencylistform;
			$this->view->data= $data;
			$this->view->controllername=$objName;
			$this->view->id = $id;
			$this->view->pocdata= $pocdata;
			$this->view->firstpocid = $firstpocid;
			$this->view->secondpocid = $secondpocid;
            $this->view->thirdpocid = $thirdpocid;
			$this->view->ermsg = '';
		}else{
			$this->view->ermsg = 'nodata';
		}
	}
	/**
	* @name indexAction
	*
	* This function is used to edit the details of a particular agency.
	*  @author Asma
	*  @version 1.0
	*/
	public function editAction()
	{
		$popConfigPermission = array();
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
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
		if(sapp_Global::_checkprivileges(BGSCREENINGTYPE,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
				array_push($popConfigPermission,'screening');
		}
		$checktypeModal = new Default_Model_Bgscreeningtype();
		$msgarray = array();
		$id = intval($this->getRequest()->getParam('id'));
		if(is_int($id) && $id != 0)
		{
			$agencylistform = new Default_Form_agencylist();
			$agencylistmodel = new Default_Model_Agencylist();
			$secondpocid = ''; $thirdpocid = '';$checktypeArr  = Array();$recordData = '';
			$userdata = $agencylistmodel->getAgencyEmail($id);
			
			$endData = $this->getrecordData($id,$agencylistform,$agencylistmodel);
			$recordData = $endData['recordData'];
			$agencylistform->setDefault('emprole',$userdata['emprole']);
			$agencylistform->submit->setLabel('Update');
			$this->view->record = $recordData;
			$this->view->form = $agencylistform;
			$this->view->ermsg = '';
		}else{
			$this->view->ermsg = 'nodata';
		}
		$rolesData = $agencylistmodel->getagencyrole();
		$typesData = $checktypeModal->fetchAll('isactive=1','type')->toArray();
		if(!empty($typesData))
		{
			$this->view->configure = '';
		}else{
			$msgarray['bg_checktype'] = 'Screening types are not configured yet.';
			$this->view->configure = 'notconfigured';
		}
		if(!empty($rolesData))
		{
			$this->view->rolesconfigure = '';
		}else{
			$msgarray['emprole'] = 'Roles are not configured yet.';
			$this->view->rolesconfigure = 'notconfigured';
		}

			
		$this->view->msgarray = $msgarray;
		if($this->getRequest()->getPost())
		{
			$result = $this->save($agencylistform);
			$this->view->msgarray = $result;
			$this->view->messages = $result;
		}
		$this->view->popConfigPermission = $popConfigPermission;
	}

	/**
	* @name save
	*
	* This function is used to save the data that comes from add and edit actions.
	*  @author Asma
	*  @version 1.0
	*/
	public function save($agencylistform)
	{
		$baseUrl = BASE_URL;
		$baseUrl = rtrim($baseUrl,'/');	
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->_request->getParam('id');
		$agencyEmail = ''; $agencyContactNum = '';$agencyuserid = '';
		$agencylistmodel = new Default_Model_Agencylist();
		$pocModel = new Default_Model_Bgpocdetails();
		$usersModel = new Default_Model_Users();
		$statesmodel = new Default_Model_States();
		$citiesmodel = new Default_Model_Cities();
		$pocData_1 = array();$pocData_2 = array();$pocData_3 = array();
		$contact_type_1 = $this->_request->getParam('contact_type_1');
		$contact_type_2 = $this->_request->getParam('contact_type_2');
		$contact_type_3 = $this->_request->getParam('contact_type_3');
		$checktypeModal = new Default_Model_Bgscreeningtype();
		$errorflag = "true";
		$primaryphone = $this->_request->getParam('primaryphone');
		$secondaryphone = $this->_request->getParam('secondaryphone');
		if($primaryphone == $secondaryphone && $secondaryphone != '' && $primaryphone != '')
		{
			$msgarray['secondaryphone'] = 'Primary and secondary phone numbers must not be same.';
			$errorflag = 'false';
		}
		if($agencylistform->isValid($this->_request->getPost()))
		{
			if(($contact_type_1 == 1 || $contact_type_2 == 1 || $contact_type_3 == 1))
			{
				if(($contact_type_1 == 1 && $contact_type_2 == 1) || ($contact_type_1 == 1 && $contact_type_3 == 1) || ($contact_type_2 == 1 && $contact_type_3 == 1) || ($contact_type_1 == 1 && $contact_type_2 == 1 && $contact_type_3 == 1))
				{
					$msgarray['contacttype'] = "Please set one contact type as primary.";
					$errorflag = "false";
				}
			}
			else
			{
				$msgarray['contacttype'] = "Please set one contact type as primary.";
				$errorflag = "false";
			}
		}
		/* Check for duplicate entry of mobile and email*/
		$pocid_1 = $this->_request->getParam('pocid_1');
		$mobile_1 = $this->_request->getParam('mobile_1');
		$email_1 = $this->_request->getParam('email_1');
		if($mobile_1 != '' && $email_1 != '')
		{
			$exists_M1 = $pocModel->checkMobileDuplicates($pocid_1,$mobile_1);
			if($exists_M1){
				$msgarray['mobile_1'] = "Mobile number already exists.";
				$errorflag = "false";
			}
			$exists_E1 = $pocModel->checkEmailDuplicates($pocid_1,$email_1);
			if($exists_E1){
				$msgarray['email_1'] = "Email already exists.";
				$errorflag = "false";
			}
		}
		$pocid_2 = $this->_request->getParam('pocid_2');
		$mobile_2= $this->_request->getParam('mobile_2');
		$email_2 = $this->_request->getParam('email_2');
		if($mobile_2 != '' && $email_2 != '')
		{
			$exists_M2 = $pocModel->checkMobileDuplicates($pocid_2,$mobile_2);
			if($exists_M2){
				$msgarray['mobile_2'] = "Mobile number already exists.";
				$errorflag = "false";
			}
			$exists_E2 = $pocModel->checkEmailDuplicates($pocid_2,$email_2);
			if($exists_E2){
				$msgarray['email_2'] = "Email already exists.";
				$errorflag = "false";
			}
		}
		$pocid_3 = $this->_request->getParam('pocid_3');
		$mobile_3 = $this->_request->getParam('mobile_3');
		$email_3 = $this->_request->getParam('email_3');
		if($mobile_3 != '' && $email_3 != '')
		{
			$exists_M3 = $pocModel->checkMobileDuplicates($pocid_3,$mobile_3);
			if($exists_M3){
				$msgarray['mobile_3'] = "Mobile number already exists.";
				$errorflag = "false";
			}
			$exists_E3 = $pocModel->checkEmailDuplicates($pocid_3,$email_3);
			if($exists_E3){
				$msgarray['email_3'] = "Email already exists.";
				$errorflag = "false";
			}
		}
		
		$contact_type_1 = $this->_request->getParam('contact_type_1');
		$contact_type_2 = $this->_request->getParam('contact_type_2');
		$contact_type_3 = $this->_request->getParam('contact_type_3');
		if($contact_type_1 == 1) 
		{ 
			$agid = $id;
			$agencyEmail = $email_1; $agencyContactNum = $mobile_1; 
			$pocuseremail = $pocModel->checkEmailInUsers($email_1,$agid);
			if(!empty($pocuseremail))
			{
				$msgarray['email_1'] = "Email already exists.";
				$errorflag = "false";
			}
		}
		else if($contact_type_2 == 1) 
		{ 
			$agencyEmail = $email_2; $agencyContactNum = $mobile_2;
			$pocuseremail = $pocModel->checkEmailInUsers($email_2,$agid);
			if(!empty($pocuseremail))
			{	
				$msgarray['email_2'] = "Email already exists.";
				$errorflag = "false";
			}
		}
		else if($contact_type_3 == 1) 
		{
			$agencyEmail = $email_3; $agencyContactNum = $mobile_3;
			$pocuseremail = $pocModel->checkEmailInUsers($email_3,$agid);
			if(!empty($pocuseremail))
			{
				$msgarray['email_3'] = "Email already exists.";
				$errorflag = "false";
			}
		}
		if($mobile_1 != '' && ($mobile_2 != '' || $mobile_3 != ''))
		{
			if($mobile_2 != '' && $mobile_1 == $mobile_2){
				$msgarray['mobile_2'] = "Contact 1 and contact 2 mobile numbers cannot be same.";
				$errorflag = "false";
			}
			if($mobile_3 != '' && $mobile_1 == $mobile_3){
				$msgarray['mobile_3'] = "Contact 1 and contact 3 mobile numbers cannot be same.";
				$errorflag = "false";
			}
			if($mobile_3 != '' && $mobile_2 != '' && $mobile_2 == $mobile_3){
				$msgarray['mobile_3'] = "Contact 2 and contact 3 mobile numbers cannot be same.";
				$errorflag = "false";
			}
		}
		if($email_1 != '' && ($email_2 != '' || $email_3 != ''))
		{
			if($email_2 != '' && $email_1 == $email_2){
				$msgarray['email_2'] = "Contact 1 and contact 2 emails cannot be same.";
				$errorflag = "false";
			}
			if($email_3 != '' && $email_1 == $email_3){
				$msgarray['email_3'] = "Contact 1 and contact 3 emails cannot be same.";
				$errorflag = "false";
			}
			if($email_3 != '' && $email_2 != '' && $email_2 == $email_3){
				$msgarray['email_3'] = "Contact 2 and contact 3 emails cannot be same.";
				$errorflag = "false";
			}
		}
		/* Duplicate check END */
		$country_1 = $this->_request->getParam('country_1');
		$state_1 = intVal($this->_request->getParam('state_1'));
		$city_1 = intVal($this->_request->getParam('city_1'));
		if(isset($country_1) && $country_1 != 0 && $country_1 != '')
		{
			$statesData = $statesmodel->getStatesList($country_1);
			foreach($statesData as $res)
			{
				$agencylistform->state_1->addMultiOption($res['id'],utf8_encode($res['state_name']));
			}
			if(isset($state_1) && $state_1 != 0 && $state_1 != '')
			$agencylistform->setDefault('state_1',$state_1);
		}
		if(isset($state_1) && $state_1 != 0 && $state_1 != ''){
			$citiesData = $citiesmodel->getCitiesList($state_1);
			foreach($citiesData as $res)
			{
				$agencylistform->city_1->addMultiOption($res['id'],utf8_encode($res['city_name']));
			}
			if(isset($city_1) && $city_1 != 0 && $city_1 != '')
			$agencylistform->setDefault('city_1',$city_1);
		}

		$country_2 = $this->_request->getParam('country_2');
		$state_2 = intVal($this->_request->getParam('state_2'));
		$city_2 = intVal($this->_request->getParam('city_2'));
		if(isset($country_2) && $country_2 != 0 && $country_2 != '')
		{
			$statesData = $statesmodel->getStatesList($country_2);
			foreach($statesData as $res)
			{
				$agencylistform->state_2->addMultiOption($res['id'],utf8_encode($res['state_name']));
			}
			if(isset($state_2) && $state_2 != 0 && $state_2 != '')
			$agencylistform->setDefault('state_2',$state_2);
		}
		if(isset($state_2) && $state_2 != 0 && $state_2 != ''){
			$citiesData = $citiesmodel->getCitiesList($state_2);
			foreach($citiesData as $res)
			{
				$agencylistform->city_2->addMultiOption($res['id'],utf8_encode($res['city_name']));
			}
			if(isset($city_2) && $city_2 != 0 && $city_2 != '')
			$agencylistform->setDefault('city_2',$city_2);
		}

		$country_3 = $this->_request->getParam('country_3');
		$state_3 = intVal($this->_request->getParam('state_3'));
		$city_3 = intVal($this->_request->getParam('city_3'));
		if(isset($country_3) && $country_3 != 0 && $country_3 != '')
		{
			$statesData = $statesmodel->getStatesList($country_3);
			foreach($statesData as $res)
			{
				$agencylistform->state_3->addMultiOption($res['id'],utf8_encode($res['state_name']));
			}
			if(isset($state_3) && $state_3 != 0 && $state_3 != '')
			$agencylistform->setDefault('state_3',$state_3);
		}
		if(isset($state_3) && $state_3 != 0 && $state_3 != ''){
			$citiesData = $citiesmodel->getCitiesList($state_3);
			foreach($citiesData as $res)
			{
				$agencylistform->city_3->addMultiOption($res['id'],utf8_encode($res['city_name']));
			}
			if(isset($city_3) && $city_3 != 0 && $city_3 != '')
			$agencylistform->setDefault('city_3',$city_3);
		}
		$typesData = $checktypeModal->fetchAll('isactive=1','type')->toArray();
		if(!empty($typesData))
		{
			$this->view->configure = '';
		}else{
			$msgarray['bg_checktype'] = 'Screening types are not configured yet.';
			$errorflag = 'false';
			$this->view->configure = 'notconfigured';
		}
		$rolesData = $agencylistmodel->getagencyrole();
		if(!empty($rolesData))
		{
			$this->view->rolesconfigure = '';
		}else{
			$msgarray['emprole'] = 'Roles are not configured yet.';
			$errorflag = 'false';
			$this->view->rolesconfigure = 'notconfigured';
		}
		/* Email address validation */
		$email_1 = $this->_request->getParam('email_1');
		$isvalidemail = filter_var($email_1, FILTER_VALIDATE_EMAIL );
		if($email_1 == $isvalidemail){
		}else{
			$msgarray['email_1'] = "Please enter valid email.";
			$errorflag = "false";
		}
		$email_2 = $this->_request->getParam('email_2');
		if($email_2 != '')
		{
			$isvalidemail = filter_var($email_2, FILTER_VALIDATE_EMAIL );
			if($email_2 == $isvalidemail){
			}else{
				$msgarray['email_2'] = "Please enter valid email.";
				$errorflag = "false";
			}
		}

		$email_3 = $this->_request->getParam('email_3');
		if($email_3 != '')
		{
			$isvalidemail = filter_var($email_3, FILTER_VALIDATE_EMAIL );
			if($email_3 == $isvalidemail){
			}else{
				$msgarray['email_3'] = "Please enter valid email.";
				$errorflag = "false";
			}
		}
		/* Email address validation END	 */
		/* Website validation */
		$id = $this->_request->getParam('id');
		$website_url = $this->_request->getParam('website_url');
		$websiteExistance = $agencylistmodel->checkSiteDuplicates($website_url,$id);
		if(!empty($websiteExistance))
		{
			$eid = isset($websiteExistance['employeeId'])?$websiteExistance['employeeId']:'An agency';
			if($websiteExistance['isactive'] == '1')
			{
				$msgarray['website_url'] = $eid." with the given website URL already exists.";
			}
			else
			{
				$msgarray['website_url'] = $eid." with the given website URL already exists but it might be inactive. Please activate it from manage external users.";
			}
			$errorflag = "false";
		}
		/* Website validation END */
		if($agencylistform->isValid($this->_request->getPost())  && $errorflag != 'false')
		{
			$agencyname = $this->_request->getParam('agencyname');
			$address = $this->_request->getParam('address');
			$primaryphone = $this->_request->getParam('primaryphone');
			$secondaryphone = $this->_request->getParam('secondaryphone');
			$bg_checktype = $this->_request->getParam('bg_checktype'); $bg_checktype = implode(',',$bg_checktype);
			$website_url = $this->_request->getParam('website_url');
			$pocid_1 = $this->_request->getParam('pocid_1');
			$firstname_1 = $this->_request->getParam('firstname_1');
			$lastname_1 = $this->_request->getParam('lastname_1');
			$mobile_1 = $this->_request->getParam('mobile_1');
			$email_1 = $this->_request->getParam('email_1');
			$location_1 = $this->_request->getParam('location_1');
			$country_1 = $this->_request->getParam('country_1');
			$state_1 = $this->_request->getParam('state_1');
			$city_1 = $this->_request->getParam('city_1');
			$contact_type_1 = $this->_request->getParam('contact_type_1');
			$pocid_2 = $this->_request->getParam('pocid_2');
			$firstname_2 = $this->_request->getParam('firstname_2');
			$lastname_2 = $this->_request->getParam('lastname_2');
			$mobile_2= $this->_request->getParam('mobile_2');
			$email_2 = $this->_request->getParam('email_2');
			$location_2 = $this->_request->getParam('location_2');
			$country_2 = $this->_request->getParam('country_2');
			$state_2 = $this->_request->getParam('state_2');
			$city_2 = $this->_request->getParam('city_2');
			$contact_type_2 = $this->_request->getParam('contact_type_2');
			$pocid_3 = $this->_request->getParam('pocid_3');
			$firstname_3 = $this->_request->getParam('firstname_3');
			$lastname_3 = $this->_request->getParam('lastname_3');
			$mobile_3 = $this->_request->getParam('mobile_3');
			$email_3 = $this->_request->getParam('email_3');
			$location_3 = $this->_request->getParam('location_3');
			$country_3 = $this->_request->getParam('country_3');
			$state_3 = $this->_request->getParam('state_3');
			$city_3 = $this->_request->getParam('city_3');
			$contact_type_3 = $this->_request->getParam('contact_type_3');
			$emprole = $this->_request->getParam('emprole');
			if(empty($websiteExistance))
			{
				$date = new Zend_Date();
				if($contact_type_1 == 1) { $agencyEmail = $email_1; $agencyContactNum = $mobile_1; $pocfn = $firstname_1; $pocln = $lastname_1;}
				else if($contact_type_2 == 1) { $agencyEmail = $email_2; $agencyContactNum = $mobile_2;$pocfn = $firstname_2; $pocln = $lastname_2;}
				else if($contact_type_3 == 1) { $agencyEmail = $email_3; $agencyContactNum = $mobile_3;$pocfn = $firstname_3; $pocln = $lastname_3;}
				$primary_poc_name = ucfirst($pocfn);
				/* Create user for the agency */
				if($id=='')
				{
					$pswd = uniqid();
					$userdata = array(
							'emprole' 			=> 		$emprole,
							'userstatus'		=>		'old',
							'firstname'         =>	    $firstname_1,
							'lastname'         =>     	$lastname_1,
							'userfullname' 		=> 		$firstname_1.' '.$lastname_1,
							'emailaddress'		=> 		$agencyEmail,
							'contactnumber'		=> 		$agencyContactNum,
							'emppassword' 		=> 		md5($pswd),
							'isactive'			=>   	1,
							'createdby'			=> 		$loginUserId,						
							'createddate' 		=> 		gmdate("Y-m-d H:i:s"),
							'modifiedby'		=>		$loginUserId,
							'modifieddate'		=>		gmdate("Y-m-d H:i:s")
					);
					$userWhere = '';
					$usersModel = new Default_Model_Users();
					
					$usersTableId = $usersModel->addOrUpdateUserModel($userdata,$userWhere);
					$agencyuserid = $usersTableId;
					$idcodeModel = new Default_Model_Identitycodes();
					$idcode = $idcodeModel->getallcodes('bgcheck');
					$userdata = array(
						'employeeId'		=>      $idcode.$usersTableId,
						'modifiedby'		=>		$loginUserId,
						'modifieddate'		=>		gmdate("Y-m-d H:i:s")
					);
				
					$userWhere = array('id=?'=>$usersTableId);
					$usersTableId = $usersModel->addOrUpdateUserModel($userdata,$userWhere);
					$options['subject'] = APPLICATION_NAME.' :: Agency is created';
					$options['header'] = 'Greetings from '.APPLICATION_NAME;
					$options['toEmail'] = $agencyEmail;
					$options['toName'] = $pocfn.' '.$pocln;
					$options['message'] = 	'<div>Dear '.$pocfn.',</div>
											<div>'.ucfirst($agencyname).' agency has been created. The credentials to login to your '.APPLICATION_NAME.' account are:
												<div>Login ID : '.$idcode.$agencyuserid.' </div>
												<div>Password : '.$pswd.'</div>
												<div></div>											
												<div style="padding:20px 0 10px 0;">Please <a href="'.$baseUrl.'/index/popup" target="_blank" style="color:#b3512f;">click here</a> to login and check the agency details.</div>
											</div>';
					$result = sapp_Global::_sendEmail($options);
					$emailids = $agencylistmodel->getAllHRManagementEMails();
					foreach($emailids as $email)
					{
						$options['subject'] = APPLICATION_NAME.' :: Agency is created';
						$options['header'] = 'Agency created';
						$options['toEmail'] = $email['groupEmail'];
						$options['toName'] = $email['group_name'];
						if($email['group_id'] == 4) {
							$salutation = 'Dear HR,';
							$options['toName'] = 'HR';
						}
						else {
							$salutation = 'Dear Management,';
							$options['toName'] = 'Management';
						}
						$options['message'] =	'<div>
													<div>'.$salutation.' </div>
													<div></div>	
													'.ucfirst($agencyname).' agency has been created with '.$pocfn.' '.$pocln.' as primary point of contact.
													<div></div>											
													<div style="padding:20px 0 10px 0;">Please <a href="'.$baseUrl.'/index/popup" target="_blank" style="color:#b3512f;">click here</a> to login and check the agency details.</div>
												</div>';	
						$options['cron'] = 'yes';
						$result = sapp_Global::_sendEmail($options);
					}
				}else{
					$agencyInfo = $agencylistmodel->getSingleagencyPOCData($id);
					$userid = $agencyInfo[0]['user_id'];
					$agencyoldname = $agencyInfo[0]['agencyname'];
					$userdata = array(
								'emprole' 			=> 		$emprole,
								'userfullname' 		=> 		$agencyname,
								'emailaddress'		=> 		$agencyEmail,
								'contactnumber'		=> 		$agencyContactNum,
								'modifiedby'		=>		$loginUserId,
								'modifieddate'		=>		gmdate("Y-m-d H:i:s")
					);
					$userWhere = array('id=?'=>$userid);
					$usersModel->addOrUpdateUserModel($userdata,$userWhere);
					if($agencyoldname != $agencyname)
					$agencynamemsg = '<div>Hello '.ucfirst($primary_poc_name).',</div><div>Your agency name is changed to '.ucfirst($agencyname).'.</div>';
					else $agencynamemsg = '<div>Hello '.ucfirst($primary_poc_name).',</div><div>Your agency information has been modified. Please find the details below:</div>';
						
					$options['subject'] = APPLICATION_NAME.' :: Agency details are updated';
					$options['header'] = 'Agency information';
					$options['toEmail'] = $agencyEmail;
					$options['toName'] = $pocfn.' '.$pocln;
					$options['message'] = '<div>'.$agencynamemsg.'
											<div><table border="1" style="border-collapse:collapse;"><tr><td>Primary email</td><td>'.$agencyEmail.'</td></tr><tr><td>Primary Contact Number</td><td>'.$agencyContactNum.'</td></tr></table></div>
											<div style="padding:20px 0 10px 0;">Please <a href="'.$baseUrl.'/index/popup" target="_blank" style="color:#b3512f;">click here</a> to login and check the agency details.
											</div>
											</div>';	
					$options['cron'] = 'yes';
					$result = sapp_Global::_sendEmail($options);
				}
				$actionflag = '';
				$tableid  = '';

				$data = array(
								'user_id'			=>		$agencyuserid,
								'agencyname'		=>		$agencyname,
								'address'			=>		$address,
								'primaryphone' 		=> 		$primaryphone,
								'secondaryphone' 	=> 		$secondaryphone,
								'bg_checktype' 		=> 		$bg_checktype,
								'website_url' 		=> 		$website_url,
								'modifiedby'		=>		$loginUserId,
								'modifieddate'		=>		gmdate("Y-m-d H:i:s")
				);

				if($id!='')
				{
					unset($data['user_id']);
					$where = array('id=?'=>$id);
					$actionflag = 2;
				}else
				{
					$data['createdby'] = $loginUserId;
					$data['createddate'] = gmdate("Y-m-d H:i:s");
					$data['isactive'] = 1;
					$where = '';
					$actionflag = 1;
				}
				$agencyId = $agencylistmodel->SaveorUpdateAgency($data, $where);

				if($agencyId == 'update')
				$tableid = $id;
				else
				$tableid = $agencyId;
				$menuID = AGENCYLIST;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);

				if($firstname_1 != '' && $mobile_1 != '')
				{
					$pocData_1 = array(
								'bg_agencyid'		=> 		$tableid,
								'first_name' 		=>		$firstname_1, 
								'last_name'			=>		$lastname_1, 
								'contact_no'    	=>		$mobile_1, 
								'email'				=>		$email_1, 
								'location'			=>		$location_1, 
								'country'			=>		$country_1, 
								'state'				=>		$state_1, 
								'city'				=>		$city_1, 
								'contact_type'		=>		$contact_type_1,
								'modifiedby'		=>		$loginUserId,
								'modifieddate'		=>		gmdate("Y-m-d H:i:s")
					);
					if($pocid_1!='')
					{
						$pocWhere_1 = array('id=?'=>$pocid_1);
						$actionflag = 2;
					}else
					{
						$pocData_1['createdby'] = $loginUserId;
						$pocData_1['createddate'] = gmdate("Y-m-d H:i:s");
						$pocData_1['isactive'] = 1;
						$pocWhere_1 = '';
						$actionflag = 1;
					}
					
					$savedpocId_1 = $pocModel->SaveorUpdatePOCDetails($pocData_1,$pocWhere_1);
					if($savedpocId_1 == 'update')
					$newpocid_1 = $pocid_1;
					else
					$newpocid_1 = $savedpocId_1;
				}
				if($firstname_2 != '' && $mobile_2 != '')
				{
					$pocData_2 = array(
								'bg_agencyid'		=> 		$tableid,
								'first_name' 		=>		$firstname_2, 
								'last_name'			=>		$lastname_2, 
								'contact_no'    	=>		$mobile_2, 
								'email'				=>		$email_2,
								'location'			=>		$location_2, 
								'country'			=>		$country_2, 
								'state'				=>		$state_2, 
								'city'				=>		$city_2, 
								'contact_type'		=>		$contact_type_2,
								'modifiedby'		=>		$loginUserId,
								'modifieddate'		=>		gmdate("Y-m-d H:i:s")
					);
					if($pocid_2!='')
					{
						$pocWhere_2 = array('id=?'=>$pocid_2);
						$actionflag = 2;
					}else
					{
						$pocData_2['createdby'] = $loginUserId;
						$pocData_2['createddate'] = gmdate("Y-m-d H:i:s");
						$pocData_2['isactive'] = 1;
						$pocWhere_2 = '';
						$actionflag = 1;
					}
					$savedpocId_2 = $pocModel->SaveorUpdatePOCDetails($pocData_2,$pocWhere_2);
					if($savedpocId_2 == 'update')
					$newpocid_2 = $pocid_2;
					else
					$newpocid_2 = $savedpocId_2;
				}
				if($firstname_3 != '' && $mobile_3 != '')
				{
					$pocData_3 = array(
								'bg_agencyid'		=> 		$tableid,
								'first_name' 		=>		$firstname_3, 
								'last_name'			=>		$lastname_3, 
								'contact_no'    	=>		$mobile_3, 
								'email'				=>		$email_3,
								'location'			=>		$location_3, 
								'country'			=>		$country_3, 
								'state'				=>		$state_3, 
								'city'				=>		$city_3, 
								'contact_type'		=>		$contact_type_3,
								'modifiedby'		=>		$loginUserId,
								'modifieddate'		=>		gmdate("Y-m-d H:i:s")
					);
					if($pocid_3!='')
					{
						$pocWhere_3 = array('id=?'=>$pocid_3);
						$actionflag = 2;
					}else
					{
						$pocData_3['createdby'] = $loginUserId;
						$pocData_3['createddate'] = gmdate("Y-m-d H:i:s");
						$pocData_3['isactive'] = 1;
						$pocWhere_3 = '';
						$actionflag = 1;
					}
					$savedpocId_3 = $pocModel->SaveorUpdatePOCDetails($pocData_3,$pocWhere_3);
					if($savedpocId_3 == 'update')
					$newpocid_3 = $pocid_3;
					else
					$newpocid_3 = $savedpocId_3;
				}

				if($agencyId == 'update')
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Agency data updated successfully."));
				else
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Agency data added successfully."));
				$this->_redirect('agencylist');
			}
			else
			{
				$msgarray['message'] = 'An Agency, with the given website address, already exists.';
				$msgarray['msgtype'] = 'error';
				return $msgarray;
			}
		}
		else
		{
			$messages = $agencylistform->getMessages();
			if(isset($msgarray['mobile_1']) && !isset($messages['mobile_1']))
			$messages['mobile_1'] = $msgarray['mobile_1'];
			if(isset($msgarray['email_1']) && !isset($messages['email_1']))
			$messages['email_1'] = $msgarray['email_1'];
			if(isset($msgarray['mobile_2']) && !isset($messages['mobile_2']))
			$messages['mobile_2'] = $msgarray['mobile_2'];
			if(isset($msgarray['email_2']) && !isset($messages['email_2']))
			$messages['email_2'] = $msgarray['email_2'];
			if(isset($msgarray['mobile_3']) && !isset($messages['mobile_3']))
			$messages['mobile_3'] = $msgarray['mobile_3'];
			if(isset($msgarray['email_3']) && !isset($messages['email_3']))
			$messages['email_3'] = $msgarray['email_3'];
			$i=0;
			$msgarray['error1dv'] = '';$msgarray['error2dv'] = '';$msgarray['error3dv'] = '';
				
			foreach ($messages as $key => $val)
			{
					
				if(strpos($key,'_1') !== false) {
					$msgarray['error1dv'] = 'first';
				}

				if(strpos($key,'_2') !== false) {
					$msgarray['error2dv'] = 'second';
				}

				if(strpos($key,'_3') !== false) {
					$msgarray['error3dv'] = 'third';
				}
				if(!isset($msgarray[$key]))
				{
					foreach($val as $key2 => $val2)
					{
						$msgarray[$key] = $val2;
						break;
					}
				}
				$i = $i +1;
				if(empty($typesData))
				{
					$msgarray['bg_checktype'] = 'Screening types are not configured yet.';
				}
				if(empty($rolesData))
				{
					$msgarray['emprole'] = 'Roles are not configured yet.';
				}
			}
			return $msgarray;
		}
	}
	/**
	* @name deleteAction
	*
	* This action is used to delete the agency and its point of contacts.
	*  @author Asma
	*  @version 1.0
	*/
	public function deleteAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$login_user = $auth->getStorage()->read()->userfullname;
		}
		$id = $this->_request->getParam('objid');
		$deleteflag= $this->_request->getParam('deleteflag');
		$messages['message'] = '';$messages['msgtype'] = '';
		$actionflag = 3;
		if($id)
		{
			$agencylistmodel = new Default_Model_Agencylist();
			$childIds = $agencylistmodel->deleteAgencyData($id,$loginUserId);
			$deleteBGchecks = $agencylistmodel->deleteBGcheckdetails($id,$loginUserId);
			if($childIds)
			{
				$menuID = AGENCYLIST;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
				$messages['message'] = 'Agency is deleted successfully.';
				$messages['msgtype'] = 'success';
				$agencydata = $agencylistmodel->getAgencyEmail($id);
				$agencyname = $agencydata['userfullname'];
				$agencyemail = $agencydata['emailaddress'];

				/* Mail to agency */
				$options['subject'] = APPLICATION_NAME.' :: Agency is deleted';
				$options['header'] = 'Agency deleted';
				$options['toEmail'] = $agencyemail;
				$options['toName'] = $agencyname;
				$options['message'] = '<div>
											<div> Dear '.$agencyname.',</div>
											We regret to inform you that your agency has been deleted.
											<div></div>												
										</div>';
				$options['cron'] = 'yes';
				$result = sapp_Global::_sendEmail($options);
				/* Mails to All HR and Management Emails */
				$emailids = $agencylistmodel->getAllHRManagementEMails();
				foreach($emailids as $email)
				{
					$options['subject'] = APPLICATION_NAME.' :: Agency is Deleted';
					$options['header'] = 'Agency deleted';
					$options['toEmail'] = $email['groupEmail'];
					$options['toName'] = $email['group_name'];
					if($email['group_id'] == 4) {
						$salutation = 'Dear HR,';
					}else{
						$salutation = 'Dear Management,';
					}
					$custom_base_url = 'http://'.$this->getRequest()->getHttpHost() . $this->getRequest()->getBaseUrl();
					$options['message'] = '<div>
												<div>'.$salutation.' </div>
												<div></div>	
												'.$agencyname.' agency has been deleted by '.$login_user.'.
												<div></div>
												<div style="padding:20px 0 10px 0;">Please <a href="'.$custom_base_url.'/index/popup" target="_blank" style="color:#b3512f;">click here</a> to login and check the agency details.</div>											
											</div>';	
					$options['cron'] = 'yes';
					$result = sapp_Global::_sendEmail($options);
				}
			}
			else
			{
				$messages['message'] = 'Agency cannot be deleted.';
				$messages['msgtype'] = 'error';
			}
		}
		else
		{
			$messages['message'] = 'Agency cannot be deleted.';
			$messages['msgtype'] = 'error';
		}
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
	/**
	* @name pocsgrid
	*
	* This function is used to display poc's of the agency in grid.
	*  @author Asma
	*  @version 1.0
	*/
	public function POCsGrid($agencyid)
	{
		$pocsModel = new Default_Model_Bgpocdetails();
		$sort = 'DESC';$by = 'createddate';$perPage = 10;$pageNo = 1;$searchData = '';$searchArray=array();
		$objName = 'agencylist';
		$tableFields = array('action'=>'Action','first_name' => 'First Name','last_name' =>'Last Name','contact_no' => 'Contact Number','email'=>'Email','location'=>'Location','city'=>'City','state'=>'State','country'=>'Country');
		$tablecontent = $pocsModel->getPOCsData($sort, $by, $pageNo, $perPage,'',$agencyid);
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
			'formgrid' => 'true'
			);
			array_push($data,$dataTmp);
			return $data;
	}
	/**
	* @name deletepocAction
	*
	* This action is used to delete a point of contact of an agency.
	*  @author Asma
	*  @version 1.0
	*/
	public function deletepocAction()
	{
		try{
		$pocsmodel = new Default_Model_Bgpocdetails();
		$agencylistmodel = new Default_Model_Agencylist();
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$poc_name = '';	
		$agencyid = $this->_request->getParam('agencyid');
		$pocid = $this->_request->getParam('pocid');
		$pocdata = $pocsmodel->getSingleagencyPOCData($pocid);
		if(!empty($pocdata[0]))
		$poc_name = $pocdata[0]['first_name'].' '.$pocdata[0]['last_name'];
		$messages['message'] = '';$messages['msgtype'] = '';
		$actionflag = 3;
		if(isset($pocdata[0]['contact_type']) && $pocdata[0]['contact_type'] == '2')
		{
			if($pocid && $agencyid)
			{
				$data = array(
						'isactive'=>0,
						'modifiedby' =>	$loginUserId,
						'modifieddate' => gmdate("Y-m-d H:i:s")
				);
				$where = array('id=?'=>$pocid,'bg_agencyid=?'=>$agencyid);
				$result = $pocsmodel->SaveorUpdatePOCDetails($data,$where);
				$result_2 = $pocsmodel->deleteBGcheckdetails($agencyid,$pocid,$loginUserId);

				// Mail to Secondary contact
				$options['subject'] = APPLICATION_NAME.' :: POC is deleted';
				$options['header'] = 'POC deleted';
				$options['toEmail'] = trim($pocdata[0]['email']);
				$options['toName'] = $pocdata[0]['userfullname'];
				$salutation = 'Dear '.$pocdata[0]['first_name'].',';
				$custom_base_url = 'http://'.$this->getRequest()->getHttpHost() . $this->getRequest()->getBaseUrl();
				$options['message'] = '<div>
										<div>'.$salutation.' </div>
										<div></div>	
										<div>You are deleted as a secondary point of contact from '.$pocdata[0]['agencyname'].' agency.</div>
										<div>
											<table border="1" style="border-collapse:collapse;">
												<tr><td>Name : </td><td>'.$poc_name.'</td></tr>
												<tr><td>Contact Number : </td><td>'.$pocdata[0]['contact_no'].'</td></tr>
												<tr><td>Email : </td><td>'.$pocdata[0]['email'].'</td></tr>
											</table>
										</div>	
										<div></div>
										</div>';	
				$options['cron'] = 'Yes';
				if($options['toEmail'] != '')
					$result_email = sapp_Global::_sendEmail($options);
				
				// Mail to Primary contact 
				$primary_poc = $pocsmodel->getPrimaryPOCData($agencyid);
				$primary_poc_name = $primary_poc[0]['first_name'].' '.$primary_poc[0]['last_name'];
				$options['subject'] = APPLICATION_NAME.' :: POC is deleted';
				$options['header'] = 'POC deleted';
				$options['toEmail'] = trim($primary_poc[0]['email']);
				$options['toName'] = $primary_poc_name;
				$salutation = 'Dear '.$primary_poc[0]['first_name'].',';
				$options['message'] = '<div>
										<div>'.$salutation.' </div>
										<div></div>	
										<div>Secondary point of contact has been deleted from '.$pocdata[0]['agencyname'].' agency. Please find the details below:</div>
										<div>
											<table border="1" style="border-collapse:collapse;">
												<tr><td>Name : </td><td>'.$poc_name.'</td></tr>
												<tr><td>Contact Number : </td><td>'.$pocdata[0]['contact_no'].'</td></tr>
												<tr><td>Email : </td><td>'.$pocdata[0]['email'].'</td></tr>
											</table>
										</div>	
										<div></div>
										<div style="padding:20px 0 10px 0;">Please <a href="'.$custom_base_url.'/index/popup" target="_blank" style="color:#b3512f;">click here</a> to login and check the agency details.</div>												
										</div>';
				$options['cron'] = 'Yes';
				if($options['toEmail'] != '')
					$result_email = sapp_Global::_sendEmail($options);
								
				
				// Mail to HR and Management
				$emailids = $agencylistmodel->getAllHRManagementEMails();
				foreach($emailids as $email){
					
					$options['subject'] = APPLICATION_NAME.' :: POC is deleted';
					$options['header'] = 'POC deleted';
					$options['toEmail'] = trim($email['groupEmail']);
					$options['toName'] = $email['group_name'];
					if($email['group_id'] == 4){
						$salutation = 'Dear HR,';
					}else{
						$salutation = 'Dear Management,';
					}
					$options['message'] = 	'<div>
												<div>'.$salutation.'</div>
												<div></div>	
												<div>Secondary point of contact has been deleted from '.$pocdata[0]['agencyname'].' agency. Please find the details below:</div>
												<div>
													<table cellpadding="0" cellspacing="0" border="1">
														<tr><td>Name :</td><td>'.$poc_name.'</td></tr>
														<tr><td>Contact Number : </td><td>'.$pocdata[0]['contact_no'].'</td></tr>
														<tr><td>Email : </td><td>'.$pocdata[0]['email'].'</td></tr>
													</table>
												</div>	
												<div></div>
												<div style="padding:20px 0 10px 0;">Please <a href="'.$custom_base_url.'/index/popup" target="_blank" style="color:#b3512f;">click here</a> to login and check the agency details.</div>											
											</div>';	
					$options['cron'] = 'Yes';
                    if($options['toEmail'] != '')
						$result_email = sapp_Global::_sendEmail($options);
					
				}
				

				if($result)
				{
					$menuID = AGENCYLIST;
					$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$agencyid,$pocid);
					$messages['message'] = 'Point Of Contact details deleted successfully.';
					$messages['msgtype'] = 'success';
				}
				else
				{
					$messages['message'] = 'POC cannot be deleted.';
					$messages['msgtype'] = 'error';
				}
			}
			else
			{
				$messages['message'] = 'POC cannot be deleted.';
				$messages['msgtype'] = 'error';
			}
		} else {
			$messages['message'] = 'Primary point of contact cannot be deleted.';
			$messages['msgtype'] = 'error';
		}
		$this->_helper->json($messages);
		}catch(Exception $e){
			exit($e->getMessage());
		}

	}
	
	
}