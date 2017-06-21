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

class Assets_VendorsController extends Zend_Controller_Action
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
			$vendorsModel = new Assets_Model_Vendors();
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
				
				$dataTmp = $vendorsModel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);
				
				array_push($data,$dataTmp);
				$this->view->dataArray = $data;
				$this->view->call = $call ;
				$this->view->messages = $this->_helper->flashMessenger->getMessages();
			
	}
	
	public function viewAction()
	{
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();
		$objName = 'vendors';
		$vendorsform = new Assets_Form_Vendors();
		$vendorsform->removeElement("submit");
		$elements = $vendorsform->getElements();
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)
			{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
					$element->setAttrib("disabled", "disabled");
				}
			}
		}
		$vendorsmodel= new Assets_Model_Vendors();
		$countriesModel = new Default_Model_Countries();
	    $statesmodel = new Default_Model_States();
	    $citiesmodel = new Default_Model_Cities();			
		$allCountriesData = $countriesModel->fetchAll('isactive=1','country')->toArray();
		$allStatesData = $statesmodel->fetchAll('isactive=1','state')->toArray();
		$allCitiesData = $citiesmodel->fetchAll('isactive=1','city')->toArray();
	    $vendorsform->setAttrib('action',BASE_URL.'vendors/edit');
	    $countrieslistArr = $countriesModel->getTotalCountriesList();
		if(sizeof($countrieslistArr)>0){
			$vendorsform->country->addMultiOption('','Select Country');
			foreach($countrieslistArr as $countrieslistres)
			{
				$vendorsform->country->addMultiOption($countrieslistres['id'],utf8_encode($countrieslistres['country_name']) );
			}
		}else{
			$msgarray['country'] = 'Countries are not configured yet.';
		}
	
		if(isset($_POST['country']) && $_POST['country']!='')
        {
            $statesmodel = new Default_Model_States();
            $statesmodeldata = $statesmodel->getStatesList(intval($_POST['country']));
            $st_opt = array();
            if(count($statesmodeldata) > 0)
            {
                foreach($statesmodeldata as $dstate)
                {
                    $st_opt[$dstate['id'].'!@#'.$dstate['state_name']] = $dstate['state_name'];
                }
            }
            $vendorsform->state->addMultiOptions(array(''=>'Select State')+$st_opt);
        }
        if(isset($_POST['state']) && $_POST['state']!='')
        {
            $citiesmodel = new Default_Model_Cities();
            $citiesmodeldata = $citiesmodel->getCitiesList(intval($_POST['state']));
            $ct_opt = array();
            if(count($citiesmodeldata) > 0)
            {
                foreach($citiesmodeldata as $dcity)
                {
                    $ct_opt[$dcity['id'].'!@#'.$dcity['city_name']] = $dcity['city_name'];
                }
            }
            $vendorsform->city->addMultiOptions(array(''=>'Select City')+$ct_opt);
        }
		
		try
		{
			if(is_numeric($id) && $id>0)
			{
				$data = $vendorsmodel->getsingleVendorsData($id);
				if(!empty($data))
				{
					$vendorsform->populate($data);
					$vendorsform->setDefault('name',$data['name']);
					$vendorsform->setDefault('contact_person',$data['contact_person']);
					$vendorsform->setDefault('address',$data['address']);
					$vendorsform->setDefault('pincode',$data['pincode']);
					$vendorsform->setDefault('primary_phone',$data['primary_phone']);
					$vendorsform->setDefault('secondary_phone',$data['secondary_phone']);
					$this->view->ermsg = '';
			        $vendorsform->state->clearMultiOptions();
                    $vendorsform->city->clearMultiOptions();
                    $vendorsform->state->addMultiOption('',utf8_encode("Select State"));
                    $vendorsform->city->addMultiOption('',utf8_encode("Select City"));
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
							   $statesData = $statesmodel->getStatesList($countryId);
								 foreach($statesData as $res) 
									$vendorsform->state->addMultiOption($res['id'],utf8_encode($res['state_name']));
							   $vendorsform->setDefault('country',$countryId);
						}
						if($stateId != '')
						{
								$citiesmodel = new Default_Model_Cities();
								$citiesData = $citiesmodel->getCitiesList($stateId);
								  foreach($citiesData as $res) 
									 $vendorsform->city->addMultiOption($res['id'],utf8_encode($res['city_name']));
								$vendorsform->setDefault('state',$stateId);
						}
				}
			    
				else
				{
					$this->view->ermsg = 'norecord';
				}
				$vendorsform->populate($data);
				$this->view->controllername = $objName;
				$this->view->id = $id;
				$this->view->form = $vendorsform;
			}
			else
			{
				$this->view->ermsg = 'nodata';
			}
		}
		catch(Exception $e)
		{
			$this->view->ermsg = 'nodata';
		}
	}
	public function addpopupAction(){
		
	$msgarray = array();
	$emptyFlag = '';
	Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
	$auth = Zend_Auth::getInstance();
	if($auth->hasIdentity()){
		$loginUserId = $auth->getStorage()->read()->id;
		$loginuserRole = $auth->getStorage()->read()->emprole;
		$loginuserGroup = $auth->getStorage()->read()->group_id;
	}
	$id = $this->getRequest()->getParam('id');
	
	/* if(sapp_Global::_checkprivileges(COUNTRIES,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
		array_push($popConfigPermission,'country');
	}
	if(sapp_Global::_checkprivileges(STATES,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
		array_push($popConfigPermission,'state');
	}
	if(sapp_Global::_checkprivileges(CITIES,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
		array_push($popConfigPermission,'city');
	} */
		
	$controllername = 'vendors';
	$vendorsform = new Assets_Form_Vendors();
	$vendorsmodel= new Assets_Model_Vendors();
	

	$countriesModel = new Default_Model_Countries();
	$statesmodel = new Default_Model_States();
	$citiesmodel = new Default_Model_Cities();
		
	$allCountriesData = $countriesModel->fetchAll('isactive=1','country')->toArray();
	$allStatesData = $statesmodel->fetchAll('isactive=1','state')->toArray();
	$allCitiesData = $citiesmodel->fetchAll('isactive=1','city')->toArray();
	
	$countrieslistArr = $countriesModel->getTotalCountriesList();
	
	
	if(sizeof($countrieslistArr)>0){
		$vendorsform->country->addMultiOption('','Select Country');
		foreach($countrieslistArr as $countrieslistres)
		{
			$vendorsform->country->addMultiOption($countrieslistres['id'],utf8_encode($countrieslistres['country_name']) );
		}
	}else{
		$msgarray['country'] = 'Countries are not configured yet.';
	}
	if(isset($_POST['country']) && $_POST['country']!='')
	{
		$statesmodel = new Default_Model_States();
		$statesmodeldata = $statesmodel->getStatesList(intval($_POST['country']));
		$st_opt = array();
		if(count($statesmodeldata) > 0)
		{
			foreach($statesmodeldata as $dstate)
			{
				$st_opt[$dstate['id'].'!@#'.$dstate['state_name']] = $dstate['state_name'];
			}
		}
		$vendorsform->state->addMultiOptions(array(''=>'Select State')+$st_opt);
	}
	if(isset($_POST['state']) && $_POST['state']!='')
	{
		$citiesmodel = new Default_Model_Cities();
		$citiesmodeldata = $citiesmodel->getCitiesList(intval($_POST['state']));
		$ct_opt = array();
		if(count($citiesmodeldata) > 0)
		{
			foreach($citiesmodeldata as $dcity)
			{
				$ct_opt[$dcity['id'].'!@#'.$dcity['city_name']] = $dcity['city_name'];
			}
		}
		$vendorsform->city->addMultiOptions(array(''=>'Select City')+$ct_opt);
	}
	$vendorsform->setAction(BASE_URL.'assets/vendors/addpopup');
	try
	{
		if(is_numeric($id) && $id>0)
		{
			$data = $vendorsmodel->getsingleVendorsData($id);
	
			if(!empty($data))
			{
				$vendorsform->populate($data);
				$vendorsform->setDefault('name',$data['name']);
				$vendorsform->setDefault('contact_person',$data['contact_person']);
				$vendorsform->setDefault('address',$data['address']);
				$vendorsform->setDefault('primary_phone',$data['primary_phone']);
				$vendorsform->setDefault('secondary_phone',$data['secondary_phone']);
				$vendorsform->submit->setLabel('Update');
				$this->view->ermsg = '';
					
				$vendorsform->state->clearMultiOptions();
				$vendorsform->city->clearMultiOptions();
				$vendorsform->state->addMultiOption('',utf8_encode("Select State"));
				$vendorsform->city->addMultiOption('',utf8_encode("Select City"));
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
					$statesData = $statesmodel->getStatesList($countryId);
					foreach($statesData as $res)
						$vendorsform->state->addMultiOption($res['id'],utf8_encode($res['state_name']));
						$vendorsform->setDefault('country',$countryId);
				}
				if($stateId != '')
				{
					$citiesmodel = new Default_Model_Cities();
					$citiesData = $citiesmodel->getCitiesList($stateId);
					foreach($citiesData as $res)
						$vendorsform->city->addMultiOption($res['id'],utf8_encode($res['city_name']));
						$vendorsform->setDefault('state',$stateId);
				}
				$countrieslistArr = $countriesModel->getTotalCountriesList();
				if(sizeof($countrieslistArr)>0)
				{
					$vendorsform->country->addMultiOption('','Select Country');
					foreach($countrieslistArr as $countrieslistres)
					{
						$vendorsform->country->addMultiOption($countrieslistres['id'],utf8_encode($countrieslistres['country_name']) );
					}
				}
				else
				{
					$msgarray['country'] = 'Countries are not configured yet.';
				}
					
			}
			else
			{
				$this->view->ermsg = 'norecord';
			}
	
		}
			
		$this->view->msgarray = $msgarray;
		$this->view->controllername = $controllername;
		$this->view->id = $id;
		$this->view->form = $vendorsform;
		//$this->view->popConfigPermission = $popConfigPermission;
		$this->view->form = $vendorsform;
			
	}
	catch(Exception $e)
	{
		$this->view->ermsg = 'nodata';
	}
	if($this->getRequest()->getPost()){
		if($vendorsform->isValid($this->_request->getPost())){
			$country_id	= NULL;
			$state_id	= NULL;
			            $id = $this->_request->getParam('id');
						
						$name = $this->_request->getParam('name');
						$contact_person = $this->_request->getParam('contact_person');
						$address = $this->_request->getParam('address');
						
						$country = $this->_request->getParam('country');
						$state = intval($this->_request->getParam('state'));
						$city = $this->_request->getParam('city');
						
						$pincode = $this->_request->getParam('pincode');
						$primary_phone = $this->_request->getParam('primary_phone');
						$secondary_phone = $this->_request->getParam('secondary_phone');				
						
						
							$actionflag = '';
							$tableid  = ''; 
							   $data = array(
										'name'=>trim($name),
										'contact_person'=>trim($contact_person),
										'address'=>trim($address),
										
										'country'=>trim($country),
										'state'=>trim($state),
										'city'=>trim($city),
										'pincode'=>trim($pincode),
										'primary_phone'=>trim($primary_phone),
										'secondary_phone'=>trim($secondary_phone),
										'isactive'=>1,
										'createdby'=>$loginUserId,
										'modifieddate'=>gmdate("Y-m-d H:i:s")
									);
								
								
							
									$data['createdby'] = $loginUserId;
									$data['createddate'] = gmdate("Y-m-d H:i:s");
									$data['isactive'] = 1;
									$where = '';
									$actionflag = 1;
									$vendorsform->populate($data);
						            $vendorsform->setDefault('name',$data['name']);
								
						            //echo "<pre>";print_r($data);die;
								$Id = $vendorsmodel->SaveorUpdateVendors($data, $where);
								if($Id == 'update')
								{
									$this->_helper->getHelper("FlashMessenger")->addMessage("Vendors updated successfully.");
								}
								else
								{
									$this->_helper->getHelper("FlashMessenger")->addMessage("Vendors added successfully.");
								}
			$vendorData = $vendorsmodel->fetchAll('isactive = 1','name')->toArray();
	//echo "<pre>";print_r($vendorData);die;
			$opt ='';
			foreach($vendorData as $record){
				$opt .= sapp_Global::selectOptionBuilder($record['id'], $record['name']);
			}
			$this->view->vendorData = $opt;
				
			$this->view->eventact = 'added';
			$close = 'close';
			$this->view->popup=$close;
		}else
		{
			$messages = $vendorsform->getMessages();
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
								}
			}
			$this->view->msgarray = $msgarray;
		}
	}
	$this->view->controllername = $controllername;
	$this->view->form = $vendorsform;
	$this->view->ermsg = '';
	
	}
	
	public function deleteAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		
		}
		$id = $this->_request->getParam('objid');
		$messages['message'] = '';$messages['msgtype'] = '';
		$messages['flagtype'] = '';
		$actionflag = 3;
		if($id)
		{
		$vendorsmodel= new Assets_Model_Vendors();
			$data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
			$where = array('id=?'=>$id);
            $status_data = $vendorsmodel->getsingleVendorsData($id);
			$Id = $vendorsmodel->SaveorUpdateVendors($data, $where);
			$messages['message'] = 'vendors deleted successfully.';$messages['msgtype'] = 'success';
		}
		else
		{
			$messages['message'] = 'vendors cannot be deleted.';$messages['msgtype'] = 'error';
		}
		$this->_helper->json($messages);
	}
	public function editAction()
	{
				$popConfigPermission = array();
				$auth = Zend_Auth::getInstance();
				$popConfigPermission = array();
				$objName = 'vendors';
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
						$msgarray = array(); 
						
				
				$id = $this->getRequest()->getParam('id');
				$callval = $this->getRequest()->getParam('call');
				if($callval == 'ajaxcall')
					$this->_helper->layout->disableLayout();
				$vendorsform = new Assets_Form_Vendors();
				$vendorsmodel= new Assets_Model_Vendors();
				
				$countriesModel = new Default_Model_Countries();
				$statesmodel = new Default_Model_States();
				$citiesmodel = new Default_Model_Cities();
							
				$allCountriesData = $countriesModel->fetchAll('isactive=1','country')->toArray();
				$allStatesData = $statesmodel->fetchAll('isactive=1','state')->toArray();
				$allCitiesData = $citiesmodel->fetchAll('isactive=1','city')->toArray();
				
				$vendorsform->setAttrib('action',BASE_URL.'vendors/edit');
				$countrieslistArr = $countriesModel->getTotalCountriesList();
				
				
		if(sizeof($countrieslistArr)>0){
			$vendorsform->country->addMultiOption('','Select Country');
			foreach($countrieslistArr as $countrieslistres)
			{
				$vendorsform->country->addMultiOption($countrieslistres['id'],utf8_encode($countrieslistres['country_name']) );
			}
		}else{
			$msgarray['country'] = 'Countries are not configured yet.';
		}
		if(isset($_POST['country']) && $_POST['country']!='')
        {
            $statesmodel = new Default_Model_States();
            $statesmodeldata = $statesmodel->getStatesList(intval($_POST['country']));
            $st_opt = array();
            if(count($statesmodeldata) > 0)
            {
                foreach($statesmodeldata as $dstate)
                {
                    $st_opt[$dstate['id'].'!@#'.$dstate['state_name']] = $dstate['state_name'];
                }
            }
            $vendorsform->state->addMultiOptions(array(''=>'Select State')+$st_opt);
        }
        if(isset($_POST['state']) && $_POST['state']!='')
        {
            $citiesmodel = new Default_Model_Cities();
            $citiesmodeldata = $citiesmodel->getCitiesList(intval($_POST['state']));
            $ct_opt = array();
            if(count($citiesmodeldata) > 0)
            {
                foreach($citiesmodeldata as $dcity)
                {
                    $ct_opt[$dcity['id'].'!@#'.$dcity['city_name']] = $dcity['city_name'];
                }
            }
            $vendorsform->city->addMultiOptions(array(''=>'Select City')+$ct_opt);
        }
		try
		{
			if(is_numeric($id) && $id>0)
			{
				$data = $vendorsmodel->getsingleVendorsData($id);
				
				if(!empty($data))
				{
				    $vendorsform->populate($data);
					$vendorsform->setDefault('name',$data['name']);
					$vendorsform->setDefault('contact_person',$data['contact_person']);
					$vendorsform->setDefault('address',$data['address']);
					$vendorsform->setDefault('primary_phone',$data['primary_phone']);
					$vendorsform->setDefault('secondary_phone',$data['secondary_phone']);
					$vendorsform->submit->setLabel('Update');
					$this->view->ermsg = '';
					
					$vendorsform->state->clearMultiOptions();
					$vendorsform->city->clearMultiOptions();
					$vendorsform->state->addMultiOption('',utf8_encode("Select State"));
					$vendorsform->city->addMultiOption('',utf8_encode("Select City"));
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
                               $statesData = $statesmodel->getStatesList($countryId);
                                 foreach($statesData as $res) 
                                    $vendorsform->state->addMultiOption($res['id'],utf8_encode($res['state_name']));
                               $vendorsform->setDefault('country',$countryId);
						}
                        if($stateId != '')
						{
                                $citiesmodel = new Default_Model_Cities();
                                $citiesData = $citiesmodel->getCitiesList($stateId);
                                  foreach($citiesData as $res) 
 	                                 $vendorsform->city->addMultiOption($res['id'],utf8_encode($res['city_name']));
                                $vendorsform->setDefault('state',$stateId);
    	                }
						$countrieslistArr = $countriesModel->getTotalCountriesList();
						if(sizeof($countrieslistArr)>0)
						{
							$vendorsform->country->addMultiOption('','Select Country');
							foreach($countrieslistArr as $countrieslistres)
							{
								$vendorsform->country->addMultiOption($countrieslistres['id'],utf8_encode($countrieslistres['country_name']) );
							}
						}
						else
						{
							$msgarray['country'] = 'Countries are not configured yet.';
						}
					
				}
				else
				{
					$this->view->ermsg = 'norecord';
				}

			}
			
				$this->view->msgarray = $msgarray;
				$this->view->controllername = $objName;
				$this->view->id = $id;
				$this->view->form = $vendorsform;
				$this->view->popConfigPermission = $popConfigPermission;
				$this->view->form = $vendorsform;
			
		}
		catch(Exception $e)
		{
			$this->view->ermsg = 'nodata';
		}
	
				if($this->getRequest()->getPost())
				{
				
					if($vendorsform->isValid($this->_request->getPost()))
					{
						$id = $this->_request->getParam('id');
						
						$name = $this->_request->getParam('name');
						$contact_person = $this->_request->getParam('contact_person');
						$address = $this->_request->getParam('address');
						
						$country = $this->_request->getParam('country');
						$state = intval($this->_request->getParam('state'));
						$city = $this->_request->getParam('city');
						
						$pincode = $this->_request->getParam('pincode');
						$primary_phone = $this->_request->getParam('primary_phone');
						$secondary_phone = $this->_request->getParam('secondary_phone');				
						
						
							$actionflag = '';
							$tableid  = ''; 
							   $data = array(
										'name'=>trim($name),
										'contact_person'=>trim($contact_person),
										'address'=>trim($address),
										
										'country'=>trim($country),
										'state'=>trim($state),
										'city'=>trim($city),
										'pincode'=>trim($pincode),
										'primary_phone'=>trim($primary_phone),
										'secondary_phone'=>trim($secondary_phone),
										'isactive'=>1,
										'createdby'=>$loginUserId,
										'modifieddate'=>gmdate("Y-m-d H:i:s")
									);
								
								if($id!=''){
									$where = array('id=?'=>$id);  
									$actionflag = 2;
									$this->view->form = $vendorsform;

								}
								else
								{
									$data['createdby'] = $loginUserId;
									$data['createddate'] = gmdate("Y-m-d H:i:s");
									$data['isactive'] = 1;
									$where = '';
									$actionflag = 1;
									$vendorsform->populate($data);
						            $vendorsform->setDefault('name',$data['name']);
								
								}
								$Id = $vendorsmodel->SaveorUpdateVendors($data, $where);
								if($Id == 'update')
								{
								   $tableid = $id;
								   $this->_helper->getHelper("FlashMessenger")->addMessage("Vendors updated successfully.");
								}   
								else
								{
								   $tableid = $Id; 	
									$this->_helper->getHelper("FlashMessenger")->addMessage("Vendors added successfully.");					   
								}   
								$menuID = VENDORS;
								$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
								$this->_redirect('vendors');	
						
					}else
					{
						$messages = $vendorsform->getMessages();
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
								}
							}
					
						$this->view->msgarray = $msgarray;
					
					}
				}
		 
	    }
	}
?>