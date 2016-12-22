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

class Default_OrganisationinfoController extends Zend_Controller_Action
{

	private $options;
	public function preDispatch()
	{			
	}

	public function init()
	{
		$orgInfoModel = new Default_Model_Organisationinfo();
		$this->_options= $this->getInvokeArg('bootstrap')->getOptions();
	}
	public function indexAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
		$orgInfoModel = new Default_Model_Organisationinfo();
		$getorgData = $orgInfoModel->getorgrecords();
		$addpermission = sapp_Global::_checkprivileges(ORGANISATIONINFO,$loginuserGroup,$loginuserRole,'add');
        $viewpermission = sapp_Global::_checkprivileges(ORGANISATIONINFO,$loginuserGroup,$loginuserRole,'view');
		$editpermission = sapp_Global::_checkprivileges(ORGANISATIONINFO,$loginuserGroup,$loginuserRole,'edit');
		$this->view->addpermission = $addpermission;
		$this->view->editpermission = $editpermission;
                
		if(!empty($getorgData))
		{
                    
			$id = $getorgData[0]['id'];
                        $data = $getorgData[0];
                        $head_data = $orgInfoModel->getorghead_details();
                        $data['orghead'] = isset($head_data['head_name'])?$head_data['head_name']:"";
                        $data['jobtitle'] = isset($head_data['jobtitle_name'])?$head_data['jobtitle_name']:"";
			$countryId = $data['country'];$stateId = $data['state'];$cityId = $data['city'];
			$countriesmodel = new Default_Model_Countries();
			$statesmodel = new Default_Model_States();
			$citiesmodel = new Default_Model_Cities();
			if($countryId !='')
			{
				try{
					$countryData = $countriesmodel->getsingleCountriesData($countryId,'main');
					$data['country'] = $countryData['country'];
				}catch(Exception $e) {
					$data['country'] = 'No country';
				}
			}

			if($stateId !='')
			{
				try{
					$stateData = $statesmodel->getsingleStatesData($stateId,'main');
					$data['state'] = $stateData['state'];
				}catch(Exception $e) {
					$data['state'] = 'No state';
				}
			}

			if($cityId !='')
			{
				try{
					$citiesData = $citiesmodel->getsingleCitiesData($cityId,'main');
					$data['city'] = $citiesData['city'];
				}catch(Exception $e) {
					$data['city'] = 'No city';
				}
			}
			$data['org_startdate'] = sapp_Global::change_date($data['org_startdate'],'view');
			
			if($data['totalemployees'] == 1)
			$data['totalemployees'] = '20-50';
			else if($data['totalemployees'] == 2)
			$data['totalemployees'] = '51-100';
			else if($data['totalemployees'] == 3)
			$data['totalemployees'] = '101-500';
			else if($data['totalemployees'] == 4)
			$data['totalemployees'] = '501 -1000';
			else
			$data['totalemployees'] = '> 1000';
				
			
			$organizationImg = new Zend_Session_Namespace('organizationinfo');
			if(!empty($data['org_image']))
			{
				$organizationImg->orgimg = $data['org_image'];
			}
			
		    $data['address1']=htmlentities($data['address1'],ENT_QUOTES, "UTF-8");
		    $data['address2']=htmlentities($data['address2'],ENT_QUOTES, "UTF-8");
		    $data['address3']=htmlentities($data['address3'],ENT_QUOTES, "UTF-8");
			
		    $this->view->dataArray = $data;
		    $this->view->messages = $this->_helper->flashMessenger->getMessages();
			$this->view->role = $loginuserRole;
			$this->view->ermsg = '';
                        
                        if($loginuserGroup == MANAGEMENT_GROUP || $loginuserGroup == HR_GROUP)
                        {
                            if($viewpermission != 'Yes')                     
                            {
                                $this->view->ermsg = 'noview';
                            }
                        }
		}else{
			$this->view->ermsg = 'nodata';
		}
                
	}
public function editAction()
    {		
    	
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
        }
        $popConfigPermission = array();
        $new_stateId ='';
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
        $new_stateId = '';
        $new_cityId = '';
        $id = $this->getRequest()->getParam('id');
        $form = new Default_Form_Organisationinfo();
        $orgInfoModel = new Default_Model_Organisationinfo();
        $countriesModel = new Default_Model_Countries();
        $statesmodel = new Default_Model_States();
        $citiesmodel = new Default_Model_Cities();
        $wizard_model = new Default_Model_Wizard();        

        $allCountriesData = $countriesModel->fetchAll('isactive=1','country')->toArray();
        $allStatesData = $statesmodel->fetchAll('isactive=1','state')->toArray();
        $allCitiesData = $citiesmodel->fetchAll('isactive=1','city')->toArray();
        $flag = 'true';
        	
		
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
        if($id)
        {
			$form->submit->setLabel('Update');
            try 
            {
                $data = $orgInfoModel->getOrganisationData($id);
                $form->setAttrib('action',BASE_URL.'organisationinfo/edit/id/'.$id);
                $data['org_startdate'] = sapp_Global::change_date($data['org_startdate'],'view');
                $form->populate($data);
                $countryId = $data['country'];
                $stateId = $data['state'];
                $cityId = $data['city'];
                $actionpage = 'edit';                
                if(count($_POST) > 0)
                {
                    $countryId = isset($_POST['country'])?$_POST['country']:"";
                    $stateId = isset($_POST['state'])?$_POST['state']:"";
                    $cityId = isset($_POST['city'])?$_POST['city']:"";                                    
                }
                if($countryId != '')
                {
                    $statesData = $statesmodel->getBasicStatesList((int)$countryId);
                    foreach($statesData as $res)
                    {
                        if($stateId == $res['state_id_org'])
                            $new_stateId = $res['state_id_org'].'!@#'.utf8_encode($res['state']);
                        $form->state->addMultiOption($res['state_id_org'].'!@#'.utf8_encode($res['state']),utf8_encode($res['state']));
                    }
                    if(count($_POST) == 0)
                        $stateId = $new_stateId;
                }
                if($stateId != '')
                {
                    $citiesData = $citiesmodel->getBasicCitiesList((int)$stateId);

                    foreach($citiesData as $res)
                    {
                        if($cityId == $res['city_org_id'])
                            $new_cityId = $res['city_org_id'].'!@#'.utf8_encode($res['city']);
                        $form->city->addMultiOption($res['city_org_id'].'!@#'.utf8_encode($res['city']),utf8_encode($res['city']));
                    }
                    if(count($_POST) == 0)
                        $cityId = $new_cityId;
                }
                $form->setDefault('country',$countryId);
                $form->setDefault('state',$stateId);
                $form->setDefault('city',$cityId);
                $this->view->domainValue = $data['domain'];
                $this->view->org_image = $data['org_image'];
                $this->view->ermsg = '';
                $this->view->datarr = $data;
            }
            catch(Exception $e)
            {
                $this->view->ermsg = 'nodata';
            }
        }
        else
        {
        	$wizardData = $wizard_model->getWizardData();
        	sapp_Global::buildlocations($form,$wizardData);
        }
        $this->view->form = $form;
        if(!empty($allCountriesData) && !empty($allStatesData) && !empty($allCitiesData))
        {
            $this->view->configuremsg = '';
        }else{
            $this->view->configuremsg = 'notconfigurable';
        }
        $this->view->msgarray = $msgarray;
        if($this->getRequest()->getPost())
        {
        	$result = $this->saveorginfo($form,$loginUserId);
			$this->view->msgarray = $result;
			if(isset($this->msgarray['domain'])) 
            $this->view->msMsg = 'multiselecterror';
        }
      
        
        $this->view->popConfigPermission = $popConfigPermission;
        
    }
    
    public function saveorginfo($form,$loginUserId)
    {
    
    
    		$orgInfoModel = new Default_Model_Organisationinfo();
    		$wizard_model = new Default_Model_Wizard();
    		$id = $this->getRequest()->getParam('id');
            $imagerror = $this->_request->getParam('imgerr');
            $imagepath = $this->_request->getParam('org_image_value');
            $imgerrmsg = $this->_request->getParam('imgerrmsg');
            $pphnumber = $this->_request->getParam('phonenumber');
            $sphnumber = $this->_request->getParam('secondaryphone');
            $org_startdate = sapp_Global::change_date($this->_request->getParam('org_startdate'),'database');

            $flag = 'true';
            if(isset($imagepath) && $imagepath != '')
            {       
            	
                $imageArr = explode('.',$imagepath);
                if(sizeof($imageArr) > 1)
                {
                    $imagename = $imageArr[0]; $imageext = $imageArr[1];
                    $extArr = array('gif', 'jpg', 'jpeg', 'png');
                    if(!in_array($imageext, $extArr))
                    {
                        $msgarray['org_image_value'] = 'Please upload an appropriate image file.';
                        $flag = 'false';
                    }
                }
                else
                {
                    $msgarray['org_image_value'] = 'Please upload an appropriate image file.';
                    $flag = 'false';
                }
            }
            if($imagerror == 'error')
            {
                if($imgerrmsg != '' && $imgerrmsg != 'undefined')
                    $msgarray['org_image_value'] = $imgerrmsg;
                else
                    $msgarray['org_image_value'] = 'Please upload an appropriate image file.';
                $flag = 'false';
            }
            if($pphnumber == $sphnumber && $sphnumber != '' && $pphnumber != '')
            {
                $msgarray['secondaryphone'] = 'Please enter different phone number.';
                $flag = 'false';
            }
            if($form->isValid($this->_request->getPost()) && $flag != 'false')                    
            { 
				$domain = $this->_request->getParam('domain'); 
				
				if(!empty($domain))
				{
					
				  $domain = implode(',',$domain);
				}
				
				$date = new Zend_Date();
				$data = array(
							'organisationname'=> trim($this->_request->getParam('organisationname')),
							'domain' =>trim($domain),
							'website' => trim($this->_request->getParam('website')),
							'org_image'=> $imagepath,
							'orgdescription'=>trim($this->_request->getParam('orgdescription')),
							'totalemployees'=>trim($this->_request->getParam('totalemployees')),
							'org_startdate' => ($org_startdate!=''?$org_startdate:NULL), 
							'phonenumber'=>trim($this->_request->getParam('phonenumber')),
							'secondaryphone' =>trim($this->_request->getParam('secondaryphone')),
							'faxnumber' => trim($this->_request->getParam('faxnumber')),
							'country' => trim((int)$this->_request->getParam('country')),
							'state' => trim(intval($this->_request->getParam('state'))),
							'city' => trim(intval($this->_request->getParam('city'))),
							'address1' => trim($this->_request->getParam('address1')),
							'address2' => trim($this->_request->getParam('address2')),
							'address3' => trim($this->_request->getParam('address3')),
							'description' => trim($this->_request->getParam('description')),
							//'orghead' => trim($this->_request->getParam('orghead')),
							'designation' => trim($this->_request->getParam('jobtitle_id',null)),
							'modifiedby' =>	$loginUserId,
							'modifieddate' => gmdate("Y-m-d H:i:s")
						);
				
				$db = Zend_Db_Table::getDefaultAdapter();	
				$db->beginTransaction();
				try
				{
		  
					$path = IMAGE_UPLOAD_PATH;
					$imagepath = $this->_request->getParam('org_image_value');
					$filecopy = 'success';
					if($imagepath !='')
					{
						$filecopy = 'error';
						if(file_exists(USER_PREVIEW_UPLOAD_PATH.'//'.$imagepath))
						{
							try
							{
								if(copy(USER_PREVIEW_UPLOAD_PATH.'//'.$imagepath, $path.'//'.$imagepath))
									$filecopy = 'success';
								unlink(USER_PREVIEW_UPLOAD_PATH.'//'.$imagepath);

							}
							catch(Exception $e)
							{
								echo $msgarray['org_image_value'] = $e->getMessage();exit;
							}
						}
					}
					$where = array('id=?'=>$id);
					if($imagepath == '')
						unset($data['org_image']);
					else if($filecopy == 'error')
						unset($data['org_image']);
					if($id!='')
					{
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
					$Id = $orgInfoModel->SaveorUpdateData($data, $where);
					
					$location_data = array('country' => trim((int)$this->_request->getParam('country')),
											'state' => trim(intval($this->_request->getParam('state'))),
											'city' => trim(intval($this->_request->getParam('city'))),
                 							'modifiedby'=>$loginUserId,
								  	       'modifieddate'=>gmdate("Y-m-d H:i:s"),
					);
					
					$LocationId = $wizard_model->SaveorUpdateWizardData($location_data, '');
									
						if($filecopy == 'success' && $Id!='update')
							$this->_helper->getHelper("FlashMessenger")->addMessage("Organization information saved successfully.");
						else if($filecopy == 'success' && $Id=='update')
								$this->_helper->getHelper("FlashMessenger")->addMessage("Organization information updated successfully.");
						else
							$this->_helper->getHelper("FlashMessenger")->addMessage("Organization information saved successfully but failed to upload the logo.");

					$menuID = ORGANISATIONINFO;
					try 
					{
						if($Id != '' && $Id != 'update')
						$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$Id);
						else 
						$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
					}
					catch(Exception $e) 
					{ 
						echo $e->getMessage();
					}
					
					$db->commit();
					$this->_redirect('organisationinfo');
                }
				catch(Exception $e)
				{	
					$db->rollBack();
					return 'failed';
				}
            }
            else
            {
                $messages = $form->getMessages();
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
    
    public function buildlocations($form)
    {
    	$countriesModel = new Default_Model_Countries();
        $statesmodel = new Default_Model_States();
        $citiesmodel = new Default_Model_Cities();
    	$countryId = '';
    	$stateId = '';
    	$cityId = '';
    		if(count($_POST) > 0)
                {
                    $countryId = isset($_POST['country'])?$_POST['country']:"";
                    $stateId = isset($_POST['state'])?$_POST['state']:"";
                    $cityId = isset($_POST['city'])?$_POST['city']:"";                                    
                }
                if($countryId != '')
                {
                    $statesData = $statesmodel->getBasicStatesList((int)$countryId);
                    foreach($statesData as $res)
                    {
                        if($stateId == $res['state_id_org'])
                            $new_stateId = $res['state_id_org'].'!@#'.utf8_encode($res['state']);
                        $form->state->addMultiOption($res['state_id_org'].'!@#'.utf8_encode($res['state']),utf8_encode($res['state']));
                    }
                    if(count($_POST) == 0)
                        $stateId = $new_stateId;
                }
                if($stateId != '')
                {
                    $citiesData = $citiesmodel->getBasicCitiesList((int)$stateId);

                    foreach($citiesData as $res)
                    {
                        if($cityId == $res['city_org_id'])
                            $new_cityId = $res['city_org_id'].'!@#'.utf8_encode($res['city']);
                        $form->city->addMultiOption($res['city_org_id'].'!@#'.utf8_encode($res['city']),utf8_encode($res['city']));
                    }
                    if(count($_POST) == 0)
                        $cityId = $new_cityId;
                }       
    }
    
    public function edit_oldAction()
    {		
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
        }
        $popConfigPermission = array();
        $empid = '';
        $orgheadsData = array();
        $oldOrgHead = '';
        $new_stateId ='';
        $actionpage = '';
        if(sapp_Global::_checkprivileges(PREFIX,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
			array_push($popConfigPermission,'prefix');
        }
        if(sapp_Global::_checkprivileges(IDENTITYCODES,$loginuserGroup,$loginuserRole,'edit') == 'Yes'){
            array_push($popConfigPermission,'identitycodes');
        }
        if(sapp_Global::_checkprivileges(JOBTITLES,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
                array_push($popConfigPermission,'jobtitles');
        }
        if(sapp_Global::_checkprivileges(POSITIONS,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
                array_push($popConfigPermission,'position');
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
		if(sapp_Global::_checkprivileges(EMPLOYEE,$loginuserGroup,$loginuserRole,'edit') == 'Yes'){
            array_push($popConfigPermission,'employee');
        }
        $msgarray = array();$new_stateId = '';$new_cityId = '';
        $id = $this->getRequest()->getParam('id');
        $form = new Default_Form_Organisationinfo();
        $user_model = new Default_Model_Usermanagement();
        $orgInfoModel = new Default_Model_Organisationinfo();
        $countriesModel = new Default_Model_Countries();
        $statesmodel = new Default_Model_States();
        $citiesmodel = new Default_Model_Cities();        
        $role_model = new Default_Model_Roles();
        $prefixModel = new Default_Model_Prefix();
        $identity_code_model = new Default_Model_Identitycodes();
        $jobtitlesModel = new Default_Model_Jobtitles();
        $employeeModal = new Default_Model_Employee();
        $positionsmodel = new Default_Model_Positions();

        $identity_codes = $identity_code_model->getIdentitycodesRecord();
        $role_data = $role_model->getRolesList_orginfo();
        $allCountriesData = $countriesModel->fetchAll('isactive=1','country')->toArray();
        $allStatesData = $statesmodel->fetchAll('isactive=1','state')->toArray();
        $allCitiesData = $citiesmodel->fetchAll('isactive=1','city')->toArray();
        
        $flag = 'true';
        $emp_identity_code = isset($identity_codes[0])?$identity_codes[0]['employee_code']:"";
        if($emp_identity_code!='')
        {
            $emp_id = $emp_identity_code.str_pad($user_model->getMaxEmpId($emp_identity_code), 4, '0', STR_PAD_LEFT);
        }	
        else 
        {
            $emp_id = '';
            $msgarray['employeeId'] = 'Identity codes are not configured yet.';
            $flag = 'false';
        }	
		
        $form->employeeId->setValue($emp_id);
        $form->emprole->addMultiOptions(array('' => 'Select Role')+$role_data);
        if(empty($allCountriesData))
        {
                $msgarray['country'] = 'Countries are not configured yet.';
                $flag = 'false';
        }
        if(empty($role_data))
        {
                $msgarray['emprole'] = 'Roles are not added yet.';
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
        $prefixData = $prefixModel->getPrefixList(); 
			
        $form->prefix_id->addMultiOption('','Select Prefix');
        if(!empty($prefixData))
        { 			
            foreach ($prefixData as $prefixres)
            {
                $form->prefix_id->addMultiOption($prefixres['id'],$prefixres['prefix']);
            }
        }
    	else
        {
            $msgarray['prefix_id'] = 'Prefixes are not configured yet.';
        }
        
        $jobtitleData = $jobtitlesModel->getJobTitleList(); 	

        if(!empty($jobtitleData))
        { 						        
            foreach ($jobtitleData as $jobtitleres)
            {
                $form->jobtitle_id->addMultiOption($jobtitleres['id'],$jobtitleres['jobtitlename']);
            }
        }
    	else
        {			    
            $msgarray['jobtitle_id'] = 'Job titles are not configured yet.';
            $msgarray['position_id'] = 'Positions are not configured yet.';
        }
		
		$orgheadsData = $employeeModal->getEmployeesForOrgHead();		
		 $currentOrgHead = $employeeModal->getCurrentOrgHead();
         if(!empty($currentOrgHead))
			$oldOrgHead = $currentOrgHead[0]['user_id'];
        if($id)
        {
			$form->submit->setLabel('Update');
            try 
            {
                $data = $orgInfoModel->getOrganisationData($id);
                $form->setAttrib('action',BASE_URL.'organisationinfo/edit/id/'.$id);
                $data['org_startdate'] = sapp_Global::change_date($data['org_startdate'],'view');
                $form->populate($data);
                $countryId = $data['country'];
                $stateId = $data['state'];
                $cityId = $data['city'];
                $actionpage = 'edit';                
                if(count($_POST) > 0)
                {
                    $countryId = isset($_POST['country'])?$_POST['country']:"";
                    $stateId = isset($_POST['state'])?$_POST['state']:"";
                    $cityId = isset($_POST['city'])?$_POST['city']:"";                                    
                }
                if($countryId != '')
                {
                    $statesData = $statesmodel->getBasicStatesList((int)$countryId);
                    foreach($statesData as $res)
                    {
                        if($stateId == $res['state_id_org'])
                            $new_stateId = $res['state_id_org'].'!@#'.utf8_encode($res['state']);
                        $form->state->addMultiOption($res['state_id_org'].'!@#'.utf8_encode($res['state']),utf8_encode($res['state']));
                    }
                    if(count($_POST) == 0)
                        $stateId = $new_stateId;
                }
                if($stateId != '')
                {
                    $citiesData = $citiesmodel->getBasicCitiesList((int)$stateId);

                    foreach($citiesData as $res)
                    {
                        if($cityId == $res['city_org_id'])
                            $new_cityId = $res['city_org_id'].'!@#'.utf8_encode($res['city']);
                        $form->city->addMultiOption($res['city_org_id'].'!@#'.utf8_encode($res['city']),utf8_encode($res['city']));
                    }
                    if(count($_POST) == 0)
                        $cityId = $new_cityId;
                }
                $emp_data = $employeeModal->fetchRow("is_orghead = 1");
				if(!empty($emp_data))
				{
					$user_data = $user_model->fetchRow("id = ".$emp_data->user_id);
					if(!empty($user_data))
					{
						$form->setDefault('firstname_orghead',$user_data->firstname);
						$form->setDefault('lastname_orghead',$user_data->lastname);
						$form->setDefault('employeeId',$user_data->employeeId);
						$form->setDefault('emprole',$user_data->emprole);
						$form->setDefault('emailaddress',$user_data->emailaddress);
						$form->setDefault('jobtitle_id',$user_data->jobtitle_id);
						$form->setDefault('prefix_id',$emp_data->prefix_id);
						$form->setDefault('date_of_joining',  sapp_Global::change_date($emp_data->date_of_joining,'view'));
						$jobtitle_id = $emp_data->jobtitle_id;
						if(isset($_POST['jobtitle_id']))
							$jobtitle_id =  $_POST['jobtitle_id'];
						
						$form->position_id->addMultiOption('','Select a Position');
						if($jobtitle_id != '')
						{
							$positionlistArr = $positionsmodel->getPositionList($jobtitle_id);
							if(sizeof($positionlistArr) > 0)
							{                        
								foreach ($positionlistArr as $positionlistres)
								{
									$form->position_id->addMultiOption($positionlistres['id'],$positionlistres['positionname']);
								}
							}
						}
						$form->setDefault('position_id',$emp_data->position_id);
						$form->setDefault('orghead',$user_data->id);
					}
					$empid = $emp_data->user_id;
				}else{
					$form->setDefault('orghead','');
				}
				if(empty($orgheadsData))
				{
					$msgarray['orghead'] = 'Management employees are not added yet.';
				}
                $form->setDefault('country',$countryId);
                $form->setDefault('state',$stateId);
                $form->setDefault('city',$cityId);
                $this->view->domainValue = $data['domain'];
                $this->view->org_image = $data['org_image'];
                $this->view->ermsg = '';
                $this->view->datarr = $data;
                $this->view->user_id = $empid;
				$this->view->orgheadsData = $orgheadsData;
            }
            catch(Exception $e)
            {
                $this->view->ermsg = 'nodata';
            }
        }
        else
        {
        	$actionpage = 'add'; 
            $activeOrgs = $orgInfoModel->getorgrecords();
            if(empty($activeOrgs))
            {
                $form->setAttrib('action',BASE_URL.'organisationinfo/edit');
                $country = $this->_request->getParam('country');
                $state = intVal($this->_request->getParam('state'));
                $city = intVal($this->_request->getParam('city'));
                if(isset($country) && $country != 0 && $country != '')
                {
                    $statesData = $statesmodel->getBasicStatesList($country);
                    foreach($statesData as $res)
                        $form->state->addMultiOption($res['state_id_org'].'!@#'.utf8_encode($res['state']),utf8_encode($res['state']));
                    if(isset($state) && $state != 0 && $state != '')
                        $form->setDefault('state',$state);
                }
                if(isset($state) && $state != 0 && $state != '')
                {
                    $citiesData = $citiesmodel->getBasicCitiesList($state);
                    foreach($citiesData as $res)
                        $form->city->addMultiOption($res['city_org_id'].'!@#'.utf8_encode($res['city']),utf8_encode($res['city']));
                    if(isset($city) && $city != 0 && $city != '')
                        $form->setDefault('city',$city);
                }
                $this->view->ermsg = '';
            }
            else
            {
                $this->view->ermsg = 'cannotadd';
            }
            $form->position_id->addMultiOption('','Select a Position');
            if(isset($_POST['jobtitle_id']) && $_POST['jobtitle_id'] != '')
            {
                $jobtitle_id =  $_POST['jobtitle_id'];
                $positionlistArr = $positionsmodel->getPositionList($jobtitle_id);
                
                if(sizeof($positionlistArr) > 0)
                {                        
                    foreach ($positionlistArr as $positionlistres)
                    {
                        $form->position_id->addMultiOption($positionlistres['id'],$positionlistres['positionname']);
                    }
                }
                
            }
			 $emp_data = $employeeModal->fetchRow("is_orghead = 1");
			if(!empty($emp_data))
			{
				$user_data = $user_model->fetchRow("id = ".$emp_data->user_id);
				if(!empty($user_data))
				{
					$form->setDefault('employeeId',$user_data->employeeId);
					$form->setDefault('emprole',$user_data->emprole);
					$form->setDefault('emailaddress',$user_data->emailaddress);
					$form->setDefault('jobtitle_id',$user_data->jobtitle_id);
					$form->setDefault('prefix_id',$emp_data->prefix_id);
					$form->setDefault('date_of_joining',  sapp_Global::change_date($emp_data->date_of_joining,'view'));
					$jobtitle_id = $emp_data->jobtitle_id;
					if(isset($_POST['jobtitle_id']))
						$jobtitle_id =  $_POST['jobtitle_id'];
					
					$form->position_id->addMultiOption('','Select a Position');
					if($jobtitle_id != '')
					{
						$positionlistArr = $positionsmodel->getPositionList($jobtitle_id);
						if(sizeof($positionlistArr) > 0)
						{                        
							foreach ($positionlistArr as $positionlistres)
							{
								$form->position_id->addMultiOption($positionlistres['id'],$positionlistres['positionname']);
							}
						}
					}
					$form->setDefault('position_id',$emp_data->position_id);
					$form->setDefault('orghead',$user_data->id);
				}
				$empid = $emp_data->user_id;
			}else{
				$form->setDefault('orghead','');				
			}
			
			if(empty($orgheadsData))
			{
				$msgarray['orghead'] = 'Management employees are not added yet.';
			}
			$this->view->user_id = $empid;
			$this->view->orgheadsData = $orgheadsData;
        }
        $this->view->form = $form;
        $this->view->currentOrgHead = $currentOrgHead;
		$this->view->actionpage = $actionpage;
        if(!empty($allCountriesData) && !empty($allStatesData) && !empty($allCitiesData))
        {
            $this->view->configuremsg = '';
        }else{
            $this->view->configuremsg = 'notconfigurable';
        }
		if(isset($_POST['prevorgheadrm']))
		$prevorgheadrm = $this->_request->getParam('prevorgheadrm');
		else
		$prevorgheadrm = '';
		$this->view->prevorgheadrmval = $prevorgheadrm;
		
		if(isset($_POST['rmflag']))
		$rmflag = $this->_request->getParam('rmflag');
		else
		$rmflag = '0';
		$this->view->rmflag = $rmflag;
        if($this->getRequest()->getPost())
        {
            $imagerror = $this->_request->getParam('imgerr');
            $imagepath = $this->_request->getParam('org_image_value');
            $imgerrmsg = $this->_request->getParam('imgerrmsg');
            $pphnumber = $this->_request->getParam('phonenumber');
            $sphnumber = $this->_request->getParam('secondaryphone');
            $org_startdate = sapp_Global::change_date($this->_request->getParam('org_startdate'),'database');

            $flag = 'true';
            if(isset($imagepath) && $imagepath != '')
            {                
                $imageArr = explode('.',$imagepath);
                if(sizeof($imageArr) > 1)
                {
                    $imagename = $imageArr[0]; $imageext = $imageArr[1];
                    $extArr = array('gif', 'jpg', 'jpeg', 'png');
                    if(!in_array($imageext, $extArr))
                    {
                        $msgarray['org_image_value'] = 'Please upload an appropriate image file.';
                        $flag = 'false';
                    }
                }
                else
                {
                    $msgarray['org_image_value'] = 'Please upload an appropriate image file.';
                    $flag = 'false';
                }
            }
            if($imagerror == 'error')
            {
                if($imgerrmsg != '' && $imgerrmsg != 'undefined')
                    $msgarray['org_image_value'] = $imgerrmsg;
                else
                    $msgarray['org_image_value'] = 'Please upload an appropriate image file.';
                $flag = 'false';
            }
            if($pphnumber == $sphnumber && $sphnumber != '' && $pphnumber != '')
            {
                $msgarray['secondaryphone'] = 'Please enter different phone number.';
                $flag = 'false';
            }
            if($form->isValid($this->_request->getPost()) && $flag != 'false')                    
            { 
				$domain = $this->_request->getParam('domain'); $domain = implode(',',$domain);
				$prevorgheadrm = $this->_request->getParam('prevorgheadrm');				
				$actionflag = '';
				$date = new Zend_Date();
				$newOrgHead = $this->_request->getParam('orghead');
				if($newOrgHead !='')
					$neworgHeadData = $employeeModal->getsingleEmployeeData($newOrgHead);
				if(!empty($neworgHeadData))
				{	
					$headfullname = $neworgHeadData[0]['userfullname'];
				}else{
					$headfullname = '';
				}
				$data = array(
							'organisationname'=> trim($this->_request->getParam('organisationname')),
							'domain' =>trim($domain),
							'website' => trim($this->_request->getParam('website')),
							'org_image'=> $imagepath,
							'orgdescription'=>trim($this->_request->getParam('orgdescription')),
							'totalemployees'=>trim($this->_request->getParam('totalemployees')),
							'org_startdate' => ($org_startdate!=''?$org_startdate:NULL), 
							'phonenumber'=>trim($this->_request->getParam('phonenumber')),
							'secondaryphone' =>trim($this->_request->getParam('secondaryphone')),
							'faxnumber' => trim($this->_request->getParam('faxnumber')),
							'country' => trim((int)$this->_request->getParam('country')),
							'state' => trim(intval($this->_request->getParam('state'))),
							'city' => trim(intval($this->_request->getParam('city'))),
							'address1' => trim($this->_request->getParam('address1')),
							'address2' => trim($this->_request->getParam('address2')),
							'address3' => trim($this->_request->getParam('address3')),
							'description' => trim($this->_request->getParam('description')),
							'orghead' => trim($this->_request->getParam('orghead')),
							'designation' => trim($this->_request->getParam('jobtitle_id',null)),
							'modifiedby' =>	$loginUserId,
							'modifieddate' => gmdate("Y-m-d H:i:s")
						);
				
				$db = Zend_Db_Table::getDefaultAdapter();	
				$db->beginTransaction();
				try
				{
		  
					if($oldOrgHead != $newOrgHead && $oldOrgHead != '' && $newOrgHead != '' && $prevorgheadrm)
					{	
						$orgInfoModel->changeOrgHead($oldOrgHead, $newOrgHead,$prevorgheadrm);
					}
					$path = IMAGE_UPLOAD_PATH;
					$imagepath = $this->_request->getParam('org_image_value');
					$filecopy = 'success';
					if($imagepath !='')
					{
						$filecopy = 'error';
						if(file_exists(USER_PREVIEW_UPLOAD_PATH.'//'.$imagepath))
						{
							try
							{
								if(copy(USER_PREVIEW_UPLOAD_PATH.'//'.$imagepath, $path.'//'.$imagepath))
									$filecopy = 'success';
								unlink(USER_PREVIEW_UPLOAD_PATH.'//'.$imagepath);

							}
							catch(Exception $e)
							{
								echo $msgarray['org_image_value'] = $e->getMessage();exit;
							}
						}
					}
					$where = array('id=?'=>$id);
					if($imagepath == '')
						unset($data['org_image']);
					else if($filecopy == 'error')
						unset($data['org_image']);
					if($id!='')
					{
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
					$Id = $orgInfoModel->SaveorUpdateData($data, $where);
					
					$jobtitle_id = $this->_request->getParam('jobtitle_id',null);
					$position_id = $this->_request->getParam('position_id',null);                        
					$date_of_joining = sapp_Global::change_date($this->_request->getParam('date_of_joining',null),'database');
					$employeeId = $this->_getParam('employeeId',null);
					$emprole = $this->_getParam('emprole',null);
					$emailaddress = $this->_getParam('emailaddress',null);
					$emppassword = sapp_Global::generatePassword();
					$first_name = trim($this->_request->getParam('firstname_orghead',null));
					$last_name = trim($this->_request->getParam('lastname_orghead',null));
					$userfullname = $first_name.' '.$last_name;
					//$userfullname = $headfullname;
					$prefix_id = $this->_getParam('prefix_id',null);
					$user_id = $this->_getParam('user_id',null);
					
					$user_data = array(
								'emprole' => $emprole,
								'firstname' => $first_name,
								'lastname' => $last_name,                                
								'userfullname' => $userfullname,
								'emailaddress' => $emailaddress,
								'jobtitle_id'=> $jobtitle_id,
								'modifiedby'=> $loginUserId,
								'modifieddate'=> gmdate("Y-m-d H:i:s"),                                                                      
								'emppassword' => md5($emppassword),
								'employeeId' => $employeeId,
								'selecteddate' => $date_of_joining,                                    
								'userstatus' => 'old',       
								'modeofentry' => 'Direct'
							);
					$emp_data = array(  
									'user_id'=>$newOrgHead,                                        
									'jobtitle_id'=>$jobtitle_id, 
									'position_id'=>$position_id, 
									'prefix_id'=>$prefix_id,    
									'reporting_manager' => 0,
									'date_of_joining'=>$date_of_joining,                                    
									'modifiedby'=>$loginUserId,				
									'modifieddate'=>gmdate("Y-m-d H:i:s")
									);
									
					if($Id == 'update')
					{
						$tableid = $id;
						unset($user_data['emppassword']);unset($user_data['modeofentry']);unset($user_data['userstatus']);
						if($newOrgHead != '')
						{
							$user_st = $user_model->SaveorUpdateUserData($user_data, " id = ".$newOrgHead);
							$employeeModal->SaveorUpdateEmployeeData($emp_data, " user_id = ".$newOrgHead);
						}
						else
						{
							$user_data['userstatus'] = 'old';
							$user_data['emppassword'] = md5($emppassword);
							$user_data['createdby'] = $loginUserId;
							$user_data['createddate'] = gmdate("Y-m-d H:i:s");
							$user_data['isactive'] = 1;
							if($emp_identity_code!='')
								$emp_id = $emp_identity_code.str_pad($user_model->getMaxEmpId($emp_identity_code), 4, '0', STR_PAD_LEFT);
							else
							$emp_id = '';

							$user_data['employeeId'] = $emp_id;
							
							$user_id = $user_model->SaveorUpdateUserData($user_data, '');
							
							$emp_data['user_id'] = $newOrgHead;
							$emp_data['createdby'] = $loginUserId;
							$emp_data['createddate'] = gmdate("Y-m-d H:i:s");;
							$emp_data['isactive'] = 1;
							$emp_data['is_orghead'] = 1;
							$employeeModal->SaveorUpdateEmployeeData($emp_data, '');
						}
						
						
						if($filecopy == 'success')
							$this->_helper->getHelper("FlashMessenger")->addMessage("Organization information updated successfully.");
						else
							$this->_helper->getHelper("FlashMessenger")->addMessage("Organization information updated successfully but failed to upload the logo.");
					}
					else
					{
						//start of saving into employee table                                                                                              
						$user_data['createdby'] = $loginUserId;
						$user_data['createddate'] = gmdate("Y-m-d H:i:s");
						$user_data['isactive'] = 1;
						if($emp_identity_code!='')
							$emp_id = $emp_identity_code.str_pad($user_model->getMaxEmpId($emp_identity_code), 4, '0', STR_PAD_LEFT);
						else
						$emp_id = '';

						$user_data['employeeId'] = $emp_id;
						$user_id = $user_model->SaveorUpdateUserData($user_data, '');
						
						
						$emp_data['user_id'] = $user_id;
						$emp_data['createdby'] = $loginUserId;
						$emp_data['createddate'] = gmdate("Y-m-d H:i:s");;
						$emp_data['isactive'] = 1;
						$emp_data['is_orghead'] = 1;
						$employeeModal->SaveorUpdateEmployeeData($emp_data, '');
						$tableid = $Id;
						if($filecopy == 'success')
							$this->_helper->getHelper("FlashMessenger")->addMessage("Organization information saved successfully.");
						else
							$this->_helper->getHelper("FlashMessenger")->addMessage("Organization information saved successfully but failed to upload the logo.");
					}

					$menuID = ORGANISATIONINFO;
					try 
					{
						if($Id != '' && $Id != 'update')
						$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$Id);
						else $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
					}
					catch(Exception $e) { echo $e->getMessage();}
					
					/* Send Mail to the user */
					if($Id != 'update')
					{
						$base_url = 'http://'.$this->getRequest()->getHttpHost() . $this->getRequest()->getBaseUrl();
						$view = $this->getHelper('ViewRenderer')->view;
						$this->view->emp_name = $userfullname;
						$this->view->password = $emppassword;
						$this->view->emp_id = $employeeId;
						$this->view->base_url=$base_url;
						$text = $view->render('mailtemplates/newpassword.phtml');
						$options['subject'] = APPLICATION_NAME.': login credentials';
						$options['header'] = 'Greetings from Sentrifugo';
						$options['toEmail'] = $emailaddress;
						$options['toName'] = $this->view->emp_name;
						$options['message'] = $text;
						$result = sapp_Global::_sendEmail($options);
					}
					/* END */
					$db->commit();
					$this->_redirect('organisationinfo');
                }
				catch(Exception $e)
				{	
					$db->rollBack();
					return 'failed';
				}
            }
            else
            {
                $messages = $form->getMessages();
                foreach ($messages as $key => $val)
                {
                    foreach($val as $key2 => $val2)
                    {
                        $msgarray[$key] = $val2;
                        break;
                    }
                    if(empty($allCountriesData)){
                            $msgarray['country'] = 'Countries are not configured yet.';
                    }
                    if(empty($allStatesData)){
                            $msgarray['state'] = 'States are not configured yet.';
                    }
                    if(empty($allCitiesData)){
                            $msgarray['city'] = 'Cities are not configured yet.';
                    }
                }
                if(isset($this->msgarray['domain'])) $this->view->msMsg = 'multiselecterror';
            }			
        }
      
        $this->view->msgarray = $msgarray;
        $this->view->popConfigPermission = $popConfigPermission;
        
    }

	public function saveupdateAction()
	{
		$orgInfoModel = new Default_Model_Organisationinfo();
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->_request->getParam('id');
		$domain = $this->_request->getParam('domain'); $domain = implode(',',$domain);
		$form = new Default_Form_Organisationinfo();
		$orgInfoModel = new Default_Model_Organisationinfo();
		$messages = $form->getMessages();
		if($this->getRequest()->getPost())
		{
			if($form->isValid($this->_request->getPost()))
			{

				$org_startdate =  sapp_Global::change_date($this->_request->getParam('org_startdate'), 'database');
				$data = array(
							'organisationname'		=>	trim($this->_request->getParam('organisationname')),
							'domain'				=>	trim($domain),
							'website'				=>	trim($this->_request->getParam('website')),
							'totalemployees'		=>	trim($this->_request->getParam('totalemployees')),
							'org_startdate'         =>  $org_startdate, 
							'phonenumber'			=>	trim($this->_request->getParam('phonenumber')),
							'secondaryphone'		=>	trim($this->_request->getParam('secondaryphone')),
							'faxnumber'				=>	trim($this->_request->getParam('faxnumber')),
							'address1'				=>	trim($this->_request->getParam('address1')),
							'address2'				=>	trim($this->_request->getParam('address2')),
							'address3'				=>	trim($this->_request->getParam('address3')),
							'managementdetails'		=>	trim($this->_request->getParam('managementdetails')),
							'modifiedby'			=>	$loginUserId,
							'modifieddate'=>gmdate("Y-m-d H:i:s")
				);
				$where = array('id=?'=>$id);
				$Id = $orgInfoModel->SaveorUpdateData($data, $where);
				$actionflag = 2;
				$menuID=ORGANISATIONINFO;		
		    	$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
				$this->_redirect('organisationinfo');
				$this->_helper->getHelper("FlashMessenger")->addMessage("Organization information updated successfully.");
			}
			else
			{
				$messages = $nationalitycontextcodeform->getMessages();
				foreach ($messages as $key => $val)
				{
					foreach($val as $key2 => $val2)
					{
						$msgarray[$key] = $val2;
					}
				}
				$this->view->msgarray = $msgarray;

			}
		}

	}

	public function uploadpreviewAction(){
		$result = $this->imageupload();

		$this->_helper->json($result);

	}
	public function imageupload()
	{
		$savefolder = USER_PREVIEW_UPLOAD_PATH;		// folder for upload



		$max_size = 1024;			// maxim size for image file, in KiloBytes

		// Allowed image types
		//$allowtype = array('gif', 'jpg', 'jpeg', 'png');
		$allowtype = array('gif', 'jpg', 'jpeg', 'png','GIF','JPG','JPEG','PNG');

		/** Uploading the image **/

		$rezultat = '';
		$result_status = '';
		$result_msg = '';
		// if is received a valid file
		if (isset ($_FILES['profile_photo'])) {
			$type = explode(".", strtolower($_FILES['profile_photo']['name']));
			//echo in_array($type, $allowtype);exit;
			if (in_array($type[1], $allowtype)) {
				// check its size
				if ($_FILES['profile_photo']['size'] != 0 && $_FILES['profile_photo']['size']<=$max_size*1024) {
					// if no errors

					if ($_FILES['profile_photo']['error'] == 0) {
						//$newname = 'preview_'.date("His").'.'.$type[1];
						$newname = 'organisation_image_'.time().'.'.$type[1];
						$thefile = $savefolder . "/" . $_FILES['profile_photo']['name'];
						$newfilename = $savefolder . "/" . $newname;
						$filename = $newname;

						if (!move_uploaded_file ($_FILES['profile_photo']['tmp_name'], $newfilename)) {
							$rezultat = '';
							$result_status = 'error';
							$result_msg = 'The file cannot be uploaded, try again.';
						}
						else {
							$rezultat = $filename;
							$setWidth = 265;
							$setHeight = 40;
							list($imgwidth, $imgheight) = getimagesize($newfilename);
							if($imgwidth < 265){
							   $setWidth = $imgwidth;
							}  
						    if($imgheight < 40){
							   $setHeight = $imgheight;
							}
							
                            if($imgwidth > 265 || $imgheight > 40){
							   sapp_Global::smartresizeimage($newfilename,$setWidth, $setHeight,false,'file',false,false);
                            }
							$result_status = 'success';
							$result_msg = '';
						}
					}
				}
				else
				{
					$rezultat = '';
					$result_status = 'error';
					$result_msg = 'Image size must not exceed '. $max_size. ' KB.';
				}
			}
			else
			{
				$rezultat = '';
				$result_status = 'error';
				$result_msg = 'Please upload only .gif, .jpg, .jpeg, .png images.';

			}
		}
		else
		{
			$rezultat = '';
			$result_status = 'error';
			$result_msg = 'Please upload only .gif, .jpg, .jpeg, .png images.';

		}

		$result = array(
				'result'=>$result_status,
				'img'=>$rezultat,
				'msg'=>$result_msg
		);
		return $result;
	}
	
	public function validateorgstartdateAction()
	{
	   $ajaxContext = $this->_helper->getHelper('AjaxContext');
	   $ajaxContext->addActionContext('validateorgstartdate', 'json')->initContext();
	   
	   $this->_helper->layout->disableLayout();	
	   $orgInfoModel = new Default_Model_Organisationinfo();
	   $result = ''; 
	   $startdate =  sapp_Global::change_date($this->_request->getParam('startdate'), 'database');
	   $con = $this->_request->getParam('con');
	   $bunitid = $this->_request->getParam('bunitid');
	   if($con == 'organisationinfo')
	   {
	       $isvalidorgstartdate = $orgInfoModel->validateOrgStartDate($startdate,$con);
	   }
	   else if($con == 'businessunit')
	   {
	      if($bunitid !='')
		    $unitid = $bunitid;
		  else
            $unitid = '';	
      		$isvalidorgstartdate = $orgInfoModel->validateOrgStartDate($startdate,$con,$unitid);
	   }
	   else if($con == 'departments' || $con == 'deptunit')
	   {
	      if($bunitid != 0)
		    $unitid = $bunitid;
		  else
           	$unitid = '';
			
      		$isvalidorgstartdate = $orgInfoModel->validateOrgStartDate($startdate,$con,$unitid);
	   }
	  
	   if(!empty($isvalidorgstartdate))
	     $result = 'error';
	   else
         $result = 'success';  	   
	   $this->_helper->_json(array('result'=>$result));
	   
	}
	
	
	public function getcompleteorgdataAction()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('getcompleteorgdata', 'json')->initContext();
	   
		$this->_helper->layout->disableLayout();	
		$userid = $this->_request->getParam('userid');
		$employeeModal = new Default_Model_Employee();		
		$positionsmodel = new Default_Model_Positions();
		$orgheadsData = $employeeModal->getEmployeesForOrgHead($userid);
		$result['result'] = array();$options_data = '';
		
		$emp_data = $employeeModal->getsingleEmployeeData($userid);
		if(!empty($emp_data))
		{
			$result['result'] = $emp_data[0];			
			$result['result']['date_of_joining']	= 	sapp_Global::change_date($emp_data[0]['date_of_joining'],'');								
			$positionlistArr = $positionsmodel->getPositionList($emp_data[0]['jobtitle_id']);			
			foreach($positionlistArr as $opt)
			{
				$options_data .= sapp_Global::selectOptionBuilder($opt['id'],ucwords(utf8_encode($opt['positionname'])));
			}
			$result['positionsdata'] = $options_data;
		}
		
		$this->_helper->_json($result);
	}
	
	public function addorgheadAction()
	{
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
		}
		$org_id = $this->_request->getParam('orgid',null);
		$msgarray = array();$prevorgheadId = '';$posted_prevorghead_rm = '';
		try
		{
			$user_model = new Default_Model_Usermanagement();
			$orgInfoModel = new Default_Model_Organisationinfo();
			$countriesModel = new Default_Model_Countries();
			$statesmodel = new Default_Model_States();
			$citiesmodel = new Default_Model_Cities();        
			$role_model = new Default_Model_Roles();
			$prefixModel = new Default_Model_Prefix();
			$identity_code_model = new Default_Model_Identitycodes();
			$jobtitlesModel = new Default_Model_Jobtitles();
			$employeeModal = new Default_Model_Employee();
			$positionsmodel = new Default_Model_Positions();
			$form = new Default_Form_Organisationhead();
			
			$form->setAttrib('action',BASE_URL.'organisationinfo/addorghead/orgid/'.$org_id);
			
			$identity_codes = $identity_code_model->getIdentitycodesRecord();
			$role_data = $role_model->getRolesList_orginfo();			
			
			$flag = 'true';
			$emp_identity_code = isset($identity_codes[0])?$identity_codes[0]['employee_code']:"";
			if($emp_identity_code!='')
			{
				$emp_id = $emp_identity_code.str_pad($user_model->getMaxEmpId($emp_identity_code), 4, '0', STR_PAD_LEFT);
			}	
			else 
			{
				$emp_id = '';
				$msgarray['employeeId'] = 'Identity codes are not configured yet.';
				$flag = 'false';
			}			
			$form->employeeId->setValue($emp_id);
			
			$form->emprole->addMultiOptions(array('' => 'Select Role')+$role_data);
			if(empty($role_data))
			{
					$msgarray['emprole'] = 'Roles are not added yet.';
					$flag = 'false';
			}
			 $prefixData = $prefixModel->getPrefixList(); 
			
			$form->prefix_id->addMultiOption('','Select Prefix');
			if(!empty($prefixData))
			{ 			
				foreach ($prefixData as $prefixres)
				{
					$form->prefix_id->addMultiOption($prefixres['id'],$prefixres['prefix']);
				}
			}
			else
			{
				$msgarray['prefix_id'] = 'Prefixes are not configured yet.';
			}
			
			$jobtitleData = $jobtitlesModel->getJobTitleList(); 	
			if(!empty($jobtitleData))
			{ 						        
				foreach ($jobtitleData as $jobtitleres)
				{
					$form->jobtitle_id->addMultiOption($jobtitleres['id'],$jobtitleres['jobtitlename']);
				}
			}
			else
			{			    
				$msgarray['jobtitle_id'] = 'Job titles are not configured yet.';
				$msgarray['position_id'] = 'Positions are not configured yet.';
			}
			
			$form->position_id->addMultiOption('','Select a Position');
            if(isset($_POST['jobtitle_id']) && $_POST['jobtitle_id'] != '')
            {
                $jobtitle_id =  $_POST['jobtitle_id'];
                $positionlistArr = $positionsmodel->getPositionList($jobtitle_id);
                
                if(sizeof($positionlistArr) > 0)
                {                        
                    foreach ($positionlistArr as $positionlistres)
                    {
                        $form->position_id->addMultiOption($positionlistres['id'],$positionlistres['positionname']);
                    }
                }
                
            }
			
			if(isset($_POST['prevorghead_rm']) && $_POST['prevorghead_rm'] != '')
            {
                $posted_prevorghead_rm =  $_POST['prevorghead_rm'];               
            }
			
			$orgheadsData = $employeeModal->getEmployeesForOrgHead();
			
			$emp_data = $employeeModal->fetchRow("is_orghead = 1");
			if(!empty($emp_data))
			{
				$user_data = $user_model->fetchRow("id = ".$emp_data->user_id);
				if(!empty($user_data))
				{
					$prevorgheadId = $user_data->id;
				}else{
					$form->removeElement('prevorghead_rm');
				}
			}else{
				$form->removeElement('prevorghead_rm');
			}
			if($this->getRequest()->getPost())
			{
				if($form->isValid($this->_request->getPost()) && $flag != 'false')                    
				{ 
					$jobtitle_id = $this->_request->getParam('jobtitle_id',null);
					$position_id = $this->_request->getParam('position_id',null);                        
					$date_of_joining = sapp_Global::change_date($this->_request->getParam('date_of_joining_head',null),'database');
					$employeeId = $this->_request->getParam('employeeId',null);
					$emprole = $this->_request->getParam('emprole',null);
					$emailaddress = $this->_request->getParam('emailaddress',null);
					$emppassword = sapp_Global::generatePassword();
					$first_name = trim($this->_request->getParam('firstname_orghead',null));
					$last_name = trim($this->_request->getParam('lastname_orghead',null));
					//$userfullname = trim($this->_request->getParam('orghead',null));
					$userfullname = $first_name.' '.$last_name;
					$prefix_id = $this->_request->getParam('prefix_id',null);
					$user_id = $this->_request->getParam('user_id',null);
					$prevorghead_rm = $this->_request->getParam('prevorghead_rm',null);
					$prevheadid = $this->_request->getParam('prevheadid',null);
				
					$user_data = array(
						'emprole' => $emprole,
					    'firstname' => $first_name,
						'lastname' => $last_name,					                                     
						'userfullname' => $userfullname,
						'emailaddress' => $emailaddress,
						'jobtitle_id'=> $jobtitle_id,						                                                                 
						'emppassword' => md5($emppassword),
						'employeeId' => $employeeId,						                                                 
						'selecteddate' => $date_of_joining,                                    
						'userstatus' => 'old',       
						'modeofentry' => 'Direct',
						'createdby'	=> $loginUserId,
						'createddate' => gmdate("Y-m-d H:i:s"),
						'modifiedby'=> $loginUserId,
						'modifieddate'=> gmdate("Y-m-d H:i:s"),     
						'isactive' => 1,
					);
					$emp_data = array(  						                                
						'jobtitle_id'=>$jobtitle_id, 
						'position_id'=>$position_id, 
						'prefix_id'=>$prefix_id,    
						'reporting_manager' => 0,
						'date_of_joining'=>$date_of_joining,  
						'createdby'	=> $loginUserId,
						'createddate' => gmdate("Y-m-d H:i:s"),						
						'modifiedby'=>$loginUserId,				
						'modifieddate'=>gmdate("Y-m-d H:i:s"),
						'isactive' => 1,
						'is_orghead' => 1,
					);
					
					$org_data = array(
						'modifiedby'=>$loginUserId,				
						'modifieddate'=>gmdate("Y-m-d H:i:s"),
					);
					
					if($emp_identity_code!='')
						$emp_id = $emp_identity_code.str_pad($user_model->getMaxEmpId($emp_identity_code), 4, '0', STR_PAD_LEFT);
					else
					$emp_id = '';
					$user_data['employeeId'] = $emp_id;		
		
					
					$db = Zend_Db_Table::getDefaultAdapter();	
					$db->beginTransaction();
					try
					{
					
						$user_id = $user_model->SaveorUpdateUserData($user_data, '');
						
						$emp_data['user_id'] = $user_id;					
						$employeeModal->SaveorUpdateEmployeeData($emp_data, '');						
						
						if(isset($prevheadid) && $prevheadid != '')
						{
							$org_data['orghead'] = $user_id;		
							$orgwhere = array('id=?'=>$org_id);
							$orgInfoModel->SaveorUpdateData($org_data, $orgwhere);
							
							$orgInfoModel->changeOrgHead($prevheadid,$user_id,$prevorghead_rm);
							
							$this->sendmailstoemployees($prevheadid,$user_id);	
						}
						$tableid = $user_id;
						$actionflag = 1;
						$menuID = EMPLOYEE;
						try 
						{
							$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
						}
						catch(Exception $e) { echo $e->getMessage();}
						$close = 'close';
						$this->view->popup=$close;
						$this->view->eventact = 'added';
						$db->commit();
					}
					catch(Exception $e)
					{
						$db->rollBack();
					}
				}
				else
				{
					$messages = $form->getMessages();   
					foreach ($messages as $key => $val)
					{
						foreach($val as $key2 => $val2)
						{
							$msgarray[$key] = $val2;
							break;
						}                   
					}                
				}
			}	
			$this->view->prevorgheadId = $prevorgheadId;
			$this->view->form = $form;			
			$this->view->msgarray = $msgarray;
			$this->view->orgheadsData = $orgheadsData;
			$this->view->posted_prevorghead_rm = $posted_prevorghead_rm;
		}
		catch(Exception $e)
		{
			echo $e->getMessage(); die;
		}
	}
	
	public function sendmailstoemployees($oldRM,$newRM)
	{		
		$baseUrl = BASE_URL;
		$employeeModal = new Default_Model_Employee();
		$employessunderEmpId = $employeeModal->getEmployeesUnderRM($oldRM);		
		/* Send Mails to the employees whose reporting manager is changed */
		$oldRMData = $employeeModal->getsingleEmployeeData($oldRM);
		$newRMData = $employeeModal->getsingleEmployeeData($newRM);
		if(!empty($newRMData))
		{
			foreach($employessunderEmpId as $employee)
			{
				$options['subject'] = APPLICATION_NAME.' : Change of reporting manager';	
				$options['header'] = 'Change of reporting manager';
				$options['toEmail'] = $employee['emailaddress']; 	
				$options['toName'] = $employee['userfullname'];
				$options['message'] = '<div>Hello '.ucfirst($employee['userfullname']).',
											<div>'.ucfirst($newRMData[0]['userfullname']).' is your new reporting manager.</div>
											<div style="padding:20px 0 10px 0;">Please <a href="'.$baseUrl.'/index/popup" target="_blank" style="color:#b3512f;">click here</a> to login </div>
										</div>';
				$result = sapp_Global::_sendEmail($options);
				
			}
		}
	}
	
}