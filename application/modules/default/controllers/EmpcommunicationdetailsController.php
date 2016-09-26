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

class Default_EmpcommunicationdetailsController extends Zend_Controller_Action
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

	}

	public function viewAction()
	{
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);
			if(in_array('empcommunicationdetails',$empOrganizationTabs)){
				$auth = Zend_Auth::getInstance();$msgarray = array();$empDept = '';
				$employeeData=array();$empDeptdata =array();

				if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
				}
				$id = $this->getRequest()->getParam('userid');
				$callval = $this->getRequest()->getParam('call');
				if($callval == 'ajaxcall')
				$this->_helper->layout->disableLayout();

				$objName = 'empcommunicationdetails';
				$empcommdetailsform = new Default_Form_empcommunicationdetails();
				$empcommdetailsform->removeElement("submit");
				$elements = $empcommdetailsform->getElements();
				if(count($elements)>0)
				{
					foreach($elements as $key=>$element)
					{
						if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
							$element->setAttrib("disabled", "disabled");
						}
					}
				}
				try
				{
					if($id && is_numeric($id) && $id>0 && $id!=$loginUserId)
					{
						$employeeModal = new Default_Model_Employee();
						$usersModel = new Default_Model_Users();
                        $empdata = $employeeModal->getActiveEmployeeData($id);
						$employeeData = $usersModel->getUserDetailsByIDandFlag($id);
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
								$empDept = $empdata[0]['department_id'];
								$empcommdetailsModal = new Default_Model_Empcommunicationdetails();
								$usersModel = new Default_Model_Users();
								$countriesModel = new Default_Model_Countries();
								$statesmodel = new Default_Model_States();
								$citiesmodel = new Default_Model_Cities();
								$deptModel = new Default_Model_Departments();
								$orgInfoModel = new Default_Model_Organisationinfo();
								$orgid = 1;
								$countryId = '';
								$stateId = '';
								$cityId = '';

								if($empDept !='' && $empDept !='NULL')
								{
									$empDeptdata = $deptModel->getEmpdepartmentdetails($empDept);
									if(!empty($empDeptdata))
									{
										$countryId = $empDeptdata[0]['country'];
										$stateId = $empDeptdata[0]['state'];
										$cityId = $empDeptdata[0]['city'];
									}
								}	
								else
								{
									$empDeptdata = $orgInfoModel->getOrganisationDetails($orgid);
									if(!empty($empDeptdata))
									{
										$countryId = $empDeptdata[0]['country'];
										$stateId = $empDeptdata[0]['state'];
										$cityId = $empDeptdata[0]['city'];
									}
								}
									if($countryId !='')
										$countryData = $countriesModel->getActiveCountryName($countryId);
									if(!empty($countryData))
									$empDeptdata[0]['country'] = $countryData[0]['country'];
									else
									$empDeptdata[0]['country'] ='';

									if($stateId !='')
										$stateData = $statesmodel->getStateNameData($stateId);
									if(!empty($stateData))
									$empDeptdata[0]['state'] = $stateData[0]['state'];
									else
									$empDeptdata[0]['state'] ='';

									if($cityId !='')
										$citiesData = $citiesmodel->getCitiesNameData($cityId);
									if(!empty($citiesData))
									$empDeptdata[0]['city'] = $citiesData[0]['city'];
									else
									$empDeptdata[0]['city'] ='';

								$data = $empcommdetailsModal->getsingleEmpCommDetailsData($id);

								if(!empty($data))
								{
									if($data[0]['perm_country'] != ''){
										$countrieslistArr = $countriesModel->getCountryCode($data[0]['perm_country']);
										if(sizeof($countrieslistArr)>0)
										{
											$empcommdetailsform->perm_country->addMultiOption('','Select Country');
											foreach ($countrieslistArr as $countrieslistres)
											{
												$empcommdetailsform->perm_country->addMultiOption($countrieslistres['id'],$countrieslistres['country_name']);
											}
										}
									}

									if($data[0]['perm_country'] != ''){
										$statePermlistArr = $statesmodel->getStatesList($data[0]['perm_country']);
										if(sizeof($statePermlistArr)>0)
										{
											$empcommdetailsform->perm_state->addMultiOption('','Select State');
											foreach($statePermlistArr as $statelistres)
											{
												$empcommdetailsform->perm_state->addMultiOption($statelistres['id'].'!@#'.$statelistres['state_name'],$statelistres['state_name']);
											}
										}
									}

									if($data[0]['perm_state'] != ''){
										$cityPermlistArr = $citiesmodel->getCitiesList($data[0]['perm_state']);
										if(sizeof($cityPermlistArr)>0)
										{
											$empcommdetailsform->perm_city->addMultiOption('','Select City');
											foreach($cityPermlistArr as $cityPermlistres)
											{
												$empcommdetailsform->perm_city->addMultiOption($cityPermlistres['id'].'!@#'.$cityPermlistres['city_name'],$cityPermlistres['city_name']);
											}
										}
									}

									if($data[0]['current_country']!='')
									{
										$countrieslistArr = $countriesModel->getCountryCode($data[0]['current_country']);
										if(sizeof($countrieslistArr)>0)
										{
											$empcommdetailsform->current_country->addMultiOption('','Select Country');
											foreach ($countrieslistArr as $countrieslistres)
											{
												$empcommdetailsform->current_country->addMultiOption($countrieslistres['id'],$countrieslistres['country_name']);
											}
										}

									}


									if($data[0]['current_country']!='' && $data[0]['current_state'] !='')
									{
										$statecurrlistArr = $statesmodel->getStatesList($data[0]['current_country']);
										if(sizeof($statecurrlistArr)>0)
										{
											$empcommdetailsform->current_state->addMultiOption('','Select State');
											foreach($statecurrlistArr as $statecurrlistres)
											{
												$empcommdetailsform->current_state->addMultiOption($statecurrlistres['id'].'!@#'.$statecurrlistres['state_name'],$statecurrlistres['state_name']);
											}
										}
										$currstateNameArr = $statesmodel->getStateName($data[0]['current_state']);

									}
									if($data[0]['current_country']!='' && $data[0]['current_state'] !='' && $data[0]['current_city']!='')
									{
										$cityCurrlistArr = $citiesmodel->getCitiesList($data[0]['current_state']);

										if(sizeof($cityCurrlistArr)>0)
										{
											$empcommdetailsform->current_city->addMultiOption('','Select State');
											foreach($cityCurrlistArr as $cityCurrlistres)
											{
												$empcommdetailsform->current_city->addMultiOption($cityCurrlistres['id'].'!@#'.$cityCurrlistres['city_name'],$cityCurrlistres['city_name']);
											}
										}
										$currcityNameArr = $citiesmodel->getCityName($data[0]['current_city']);
									}
									
									$empcommdetailsform->populate($data[0]);
									if($data[0]['perm_country'] != '')
										$empcommdetailsform->setDefault('perm_country',$data[0]['perm_country']);
									if($data[0]['perm_state'] != ''){
										$permstateNameArr = $statesmodel->getStateName($data[0]['perm_state']);
										$empcommdetailsform->setDefault('perm_state',$permstateNameArr[0]['id'].'!@#'.$permstateNameArr[0]['statename']);	
									}
									if($data[0]['perm_city'] != ''){
										$permcityNameArr = $citiesmodel->getCityName($data[0]['perm_city']);
										$empcommdetailsform->setDefault('perm_city',$permcityNameArr[0]['id'].'!@#'.$permcityNameArr[0]['cityname']);
									}
									if($data[0]['current_country'] != '')
									$empcommdetailsform->setDefault('current_country',$data[0]['current_country']);
									if($data[0]['current_state'] !='')
									$empcommdetailsform->setDefault('current_state',$currstateNameArr[0]['id'].'!@#'.$currstateNameArr[0]['statename']);
									if($data[0]['current_city'] != '')
									$empcommdetailsform->setDefault('current_city',$currcityNameArr[0]['id'].'!@#'.$currcityNameArr[0]['cityname']);

								}
								if(!empty($data[0]['perm_country']))
								 {
								    $countryname = $countriesModel->getCountryCode($data[0]['perm_country']);
									  if(!empty($countryname))
									  {
										$data[0]['perm_country'] = $countryname[0]['country_name'];
									  }
									  else
									  {
										  $data[0]['perm_country'] = "";
									  }
								 }
								if(!empty($data[0]['perm_state']))
								 {
										$statename = $statesmodel->getStateName($data[0]['perm_state']);
									  if(!empty($statename))
									  {
										$data[0]['perm_state'] = $statename[0]['statename'];
									  }
									  else
									  {
										  $data[0]['perm_state'] = "";
									  }
								 }
								if(!empty($data[0]['perm_city'])) 
								{
									$cityname = $citiesmodel->getCityName($data[0]['perm_city']);
									 if(!empty($cityname))
									 {
										$data[0]['perm_city'] = $cityname[0]['cityname'];
									 }
									 else{
										 $data[0]['perm_city'] = "";
									 }
								}
								if(!empty($data[0]['current_country']))
								 {
								   $countryname = $countriesModel->getCountryCode($data[0]['current_country']);
									  if(!empty($countryname))
									  {
									  $data[0]['current_country'] = $countryname[0]['country_name'];
									  }
								 }
								if(!empty($data[0]['current_state']))
								{
									$statename = $statesmodel->getStateName($data[0]['current_state']);
									  if(!empty($statename))
									  {
										$data[0]['current_state'] = $statename[0]['statename'];
									  }
									  else{
										  $data[0]['current_state'] = "";
									  }
								}
								if(!empty($data[0]['current_city'])) 
								{
									$cityname = $citiesmodel->getCityName($data[0]['current_city']);
									 if(!empty($cityname))
									 {
										$data[0]['current_city'] = $cityname[0]['cityname'];
									 }
									 else
									 {
										 $data[0]['current_city'] = "";
									 }
								}
								$this->view->controllername = $objName;
								$this->view->data = $data;
								if(!empty($empDeptdata))
								$this->view->dataArray = $empDeptdata[0];
								else
								$this->view->dataArray = $empDeptdata;
								$this->view->id = $id;
								$this->view->employeedata = $employeeData[0];
								$this->view->form = $empcommdetailsform;
							}
							$this->view->empdata = $empdata;
						}
					}else
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
            $popConfigPermission = array();
            
            $empOrganizationTabs = explode(",",EMPTABCONFIGS);
            if(in_array('empcommunicationdetails',$empOrganizationTabs))
            {
					
                $empDeptdata=array();$employeeData =array();
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
				
                $id = $this->getRequest()->getParam('userid');
                if($id == '')		$id = $loginUserId;
                $callval = $this->getRequest()->getParam('call');
                if($callval == 'ajaxcall')
                $this->_helper->layout->disableLayout();
                //To check previliges for edit
		 		$myEmployees_model = new Default_Model_Myemployees();
			 	$getMyTeamIds = $myEmployees_model->getTeamIds($loginUserId);
			 	$teamIdArr = array();
			 	if(!empty($getMyTeamIds))
			 	{
				 	foreach($getMyTeamIds as $teamId)
				 	{
				 		array_push($teamIdArr,$teamId['user_id']);
				 	}
			 	}
				
			 	if($loginuserRole == SUPERADMINROLE || $loginuserGroup == MANAGEMENT_GROUP || $loginuserGroup == HR_GROUP || ($loginuserGroup == MANAGER_GROUP && in_array($id,$teamIdArr)))
			 	{
			 	
                
                try
                {
                    if($id && is_numeric($id) && $id>0 && $id!=$loginUserId)
                    {
                        $employeeModal = new Default_Model_Employee();
						$usersModel = new Default_Model_Users();
                        $empdata = $employeeModal->getActiveEmployeeData($id);
						$employeeData = $usersModel->getUserDetailsByIDandFlag($id);
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
                                $empDept = $empdata[0]['department_id'];
                                $empcommdetailsform = new Default_Form_empcommunicationdetails();
								
                                $empcommdetailsModal = new Default_Model_Empcommunicationdetails();
                                $usersModel = new Default_Model_Users();
                                $countriesModel = new Default_Model_Countries();
                                $statesmodel = new Default_Model_States();
                                $citiesmodel = new Default_Model_Cities();
                                $orgInfoModel = new Default_Model_Organisationinfo();
                                $msgarray = array();
								$orgid = 1;
								$countryId = '';
								$stateId = '';
								$cityId = '';
								
                                $deptModel = new Default_Model_Departments();
								if($empDept !='' && $empDept !='NULL')
								{
									$empDeptdata = $deptModel->getEmpdepartmentdetails($empDept);
									if(!empty($empDeptdata))
									{
										$countryId = $empDeptdata[0]['country'];
										$stateId = $empDeptdata[0]['state'];
										$cityId = $empDeptdata[0]['city'];
									}
								}	
								else
								{
									$empDeptdata = $orgInfoModel->getOrganisationDetails($orgid);
									if(!empty($empDeptdata))
									{
										$countryId = $empDeptdata[0]['country'];
										$stateId = $empDeptdata[0]['state'];
										$cityId = $empDeptdata[0]['city'];
									}
								}	
									if($countryId !='')
										$countryData = $countriesModel->getActiveCountryName($countryId);
                                    if(!empty($countryData))
                                        $empDeptdata[0]['country'] = $countryData[0]['country'];
                                    else
                                        $empDeptdata[0]['country'] ='';
									
									if($stateId !='')
										$stateData = $statesmodel->getStateNameData($stateId);
                                    if(!empty($stateData))
                                        $empDeptdata[0]['state'] = $stateData[0]['state'];
                                    else
                                        $empDeptdata[0]['state'] ='';
									
									if($cityId !='')
										$citiesData = $citiesmodel->getCitiesNameData($cityId);
                                    if(!empty($citiesData))
                                        $empDeptdata[0]['city'] = $citiesData[0]['city'];
                                    else
                                        $empDeptdata[0]['city'] ='';
                                $countrieslistArr = $countriesModel->getTotalCountriesList();
                                if(sizeof($countrieslistArr)>0)
                                {
                                    $empcommdetailsform->perm_country->addMultiOption('','Select Country');
                                    $empcommdetailsform->current_country->addMultiOption('','Select Country');
                                    foreach ($countrieslistArr as $countrieslistres)
                                    {
                                        $empcommdetailsform->perm_country->addMultiOption($countrieslistres['id'],  utf8_encode($countrieslistres['country_name']));
                                        $empcommdetailsform->current_country->addMultiOption($countrieslistres['id'],utf8_encode($countrieslistres['country_name']));
                                    }
                                }
                                else
                                {
                                    $msgarray['perm_country'] = 'Countries are not configured yet.';
                                    $msgarray['current_country'] = 'Countries are not configured yet.';
                                }
                                $data = $empcommdetailsModal->getsingleEmpCommDetailsData($id);
                                if(!empty($data))
                                {
                                    $perm_country = $data[0]['perm_country'];
                                    if(isset($_POST['perm_country']))
                                    {
                                        $perm_country = $_POST['perm_country'];
                                    }
                                    $perm_state = $data[0]['perm_state'];
                                    if(isset($_POST['perm_state']))
                                    {
                                        $perm_state = $_POST['perm_state'];
                                    }
                                    $perm_city = $data[0]['perm_city'];
                                    if(isset($_POST['perm_city']))
                                    {
                                        $perm_city = $_POST['perm_city'];
                                    }
                                    if($perm_country != '')
                                    {
                                        $statePermlistArr = $statesmodel->getStatesList($perm_country);
                                        if(sizeof($statePermlistArr)>0)
                                        {
                                            $empcommdetailsform->perm_state->addMultiOption('','Select State');
                                            foreach($statePermlistArr as $statelistres)
                                            {
                                                $empcommdetailsform->perm_state->addMultiOption($statelistres['id'],$statelistres['state_name']);
                                            }
                                        }
                                    }
                                    if($perm_state != '')
                                    {
                                        $cityPermlistArr = $citiesmodel->getCitiesList($perm_state);
                                        if(sizeof($cityPermlistArr)>0)
                                        {
                                            $empcommdetailsform->perm_city->addMultiOption('','Select City');
                                            foreach($cityPermlistArr as $cityPermlistres)
                                            {
                                                $empcommdetailsform->perm_city->addMultiOption($cityPermlistres['id'],$cityPermlistres['city_name']);
                                            }
                                        }
                                    }
                                    $current_country = $data[0]['current_country'];
                                    if(isset($_POST['current_country']))
                                    {
                                        $current_country = $_POST['current_country'];
                                    }
                                    $current_state = $data[0]['current_state'];
                                    if(isset($_POST['current_state']))
                                    {
                                        $current_state = $_POST['current_state'];
                                    }
                                    $current_city = $data[0]['current_city'];
                                    if(isset($_POST['current_city']))
                                    {
                                        $current_city = $_POST['current_city'];
                                    }
                                    if($current_country != '')
                                    {
                                        $statecurrlistArr = $statesmodel->getStatesList($current_country);
                                        if(sizeof($statecurrlistArr)>0)
                                        {
                                            $empcommdetailsform->current_state->addMultiOption('','Select State');
                                            foreach($statecurrlistArr as $statecurrlistres)
                                            {
                                                $empcommdetailsform->current_state->addMultiOption($statecurrlistres['id'],$statecurrlistres['state_name']);
                                            }
                                        }
                                    }
                                    if($current_state != '')
                                    {
                                        $cityCurrlistArr = $citiesmodel->getCitiesList($current_state);

                                        if(sizeof($cityCurrlistArr)>0)
                                        {
                                            $empcommdetailsform->current_city->addMultiOption('','Select City');
                                            foreach($cityCurrlistArr as $cityCurrlistres)
                                            {
                                                $empcommdetailsform->current_city->addMultiOption($cityCurrlistres['id'],$cityCurrlistres['city_name']);
                                            }
                                        }
                                    }
                                    $empcommdetailsform->populate($data[0]);
                                    $empcommdetailsform->setDefault('perm_country',$perm_country);
                                    $empcommdetailsform->setDefault('perm_state',$perm_state);
                                    $empcommdetailsform->setDefault('perm_city',$perm_city);
                                    if($data[0]['current_country'] != '')
                                        $empcommdetailsform->setDefault('current_country',$current_country);
                                    if($data[0]['current_state'] !='')
                                        $empcommdetailsform->setDefault('current_state',$current_state);
                                    if($data[0]['current_city'] != '')
                                        $empcommdetailsform->setDefault('current_city',$current_city);
                                }
                                $empcommdetailsform->setAttrib('action',BASE_URL.'empcommunicationdetails/edit/userid/'.$id);
                                $empcommdetailsform->user_id->setValue($id);
                                $this->view->employeedata = $employeeData[0];
                                if(!empty($empDeptdata))
                                    $this->view->dataArray = $empDeptdata[0];
                                else
                                    $this->view->dataArray = $empDeptdata;
                                $this->view->form = $empcommdetailsform;
                                $this->view->data = $data;
                                $this->view->id = $id;
                                $this->view->msgarray = $msgarray;
                                $this->view->popConfigPermission = $popConfigPermission;
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
                if($this->getRequest()->getPost())
                {
                    $result = $this->save($empcommdetailsform,$id);
                    $this->view->msgarray = $result;
                }
			 }
	            else
	            {
	                $this->_redirect('error');
	            }
            }
            else
            {
                $this->_redirect('error');
            }
        }
        else
        {
            $this->_redirect('error');
        }
    }

	public function save($empcommdetailsform,$userid)
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$perm_country = $this->_request->getParam('perm_country');
		$perm_stateparam = $this->_request->getParam('perm_state');
		$perm_stateArr = explode("!@#",$this->_request->getParam('perm_state'));
		$perm_state = $perm_stateArr[0];
		$perm_cityparam = $this->_request->getParam('perm_city');
		$perm_cityArr = explode("!@#",$this->_request->getParam('perm_city'));
		$perm_city = $perm_cityArr[0];
		$address_flag = $this->_request->getParam('address_flag');
		$current_country = $this->_request->getParam('current_country');
		$current_stateparam = $this->_request->getParam('current_state');
		$current_stateArr = explode("!@#",$this->_request->getParam('current_state'));
		$current_state = $current_stateArr[0];
		$current_cityparam = $this->_request->getParam('current_city');
		$current_cityArr = explode("!@#",$this->_request->getParam('current_city'));
		$current_city = $current_cityArr[0];


		if($empcommdetailsform->isValid($this->_request->getPost())){
			 $post_values = $this->_request->getPost();
           	 if(isset($post_values['id']))
                unset($post_values['id']);
             if(isset($post_values['user_id']))
                unset($post_values['user_id']);
             if(isset($post_values['submit']))	
                unset($post_values['submit']);
           $new_post_values = array_filter($post_values);
           if(!empty($new_post_values))
           {
			$empcommdetailsModal = new Default_Model_Empcommunicationdetails();
			$id = $this->_request->getParam('id');
			$user_id = $userid;
			$personalemail = $this->_request->getParam('personalemail');
			$perm_streetaddress = $this->_request->getParam('perm_streetaddress');

			$perm_pincode = $this->_request->getParam('perm_pincode');

			$current_streetaddress = $this->_request->getParam('current_streetaddress');

			$current_pincode = $this->_request->getParam('current_pincode');
			$emergency_number = $this->_request->getParam('emergency_number');
			$emergency_name = $this->_request->getParam('emergency_name');
			$emergency_email = $this->_request->getParam('emergency_email');
			$date = new Zend_Date();
			$actionflag = '';
			$tableid  = '';
			$data = array('user_id'=>$user_id,
				                 'personalemail'=>$personalemail,
								 'perm_streetaddress'=>$perm_streetaddress, 								 
				      			 'perm_country'=>($perm_country!=''?$perm_country:NULL),
								 'perm_state'=>($perm_state!=''?$perm_state:NULL),
								 'perm_city'=>($perm_city!=''?$perm_city:NULL),
								 'perm_pincode'=>$perm_pincode,
                                 'current_streetaddress'=>($current_streetaddress!=''?$current_streetaddress:NULL), 
                                 'current_country'=>($current_country!=''?$current_country:NULL), 								 
				      			 'current_state'=>($current_state!=''?$current_state:NULL),
								 'current_city'=>($current_city!=''?$current_city:NULL), 								 
				      			 'current_pincode'=>($current_pincode!=''?$current_pincode:NULL),
								 'emergency_number'=>($emergency_number!=''?$emergency_number:NULL),
								 'emergency_name'=>($emergency_name!=''?$emergency_name:NULL),
								 'emergency_email'=>($emergency_email!=''?$emergency_email:NULL),
								 'modifiedby'=>$loginUserId,
			                     'modifieddate'=>gmdate("Y-m-d H:i:s")			
			);
			if($id!=''){
				$where = array('user_id=?'=>$user_id);
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
			$Id = $empcommdetailsModal->SaveorUpdateEmpcommData($data, $where);
			if($Id == 'update')
			{
				$tableid = $id;
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Employee contact details updated successfully."));
			}
			else
			{
				$tableid = $Id;
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Employee contact details added successfully."));
			}
			$menuID = EMPLOYEE;
			$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$user_id);
           }
			else
           {
           		$this->_helper->getHelper("FlashMessenger")->addMessage(array("error"=>FIELDMSG));
           }	
			$this->_redirect('empcommunicationdetails/edit/userid/'.$userid);
		}else
		{
			$messages = $empcommdetailsform->getMessages();
			foreach ($messages as $key => $val)
			{
				foreach($val as $key2 => $val2)
				{
					$msgarray[$key] = $val2;
					break;
				}
			}
                        
			if(isset($perm_country) && $perm_country != 0 && $perm_country != '')
			{
				$statesmodel = new Default_Model_States();
				$statesmodeldata = $statesmodel->getStatesList($perm_country);
				$empcommdetailsform->perm_state->clearMultiOptions();
				$empcommdetailsform->perm_city->clearMultiOptions();
				$empcommdetailsform->perm_state->addMultiOption('','Select State');
				foreach($statesmodeldata as $res)
				$empcommdetailsform->perm_state->addMultiOption($res['id'].'!@#'.utf8_encode($res['state_name']),utf8_encode($res['state_name']));
				if(isset($perm_stateparam) && $perm_stateparam != 0 && $perm_stateparam != '')
				$empcommdetailsform->setDefault('perm_state',$perm_stateparam);
			}

			if(isset($perm_stateparam) && $perm_stateparam != 0 && $perm_stateparam != '')
			{
				$citiesmodel = new Default_Model_Cities();
				$citiesmodeldata = $citiesmodel->getCitiesList($perm_state);
					
				$empcommdetailsform->perm_city->addMultiOption('','Select City');
				foreach($citiesmodeldata as $res)
				$empcommdetailsform->perm_city->addMultiOption($res['id'].'!@#'.utf8_encode($res['city_name']),utf8_encode($res['city_name']));
				if(isset($perm_cityparam) && $perm_cityparam != 0 && $perm_cityparam != '')
				$empcommdetailsform->setDefault('perm_city',$perm_cityparam);
			}
			if(isset($current_country) && $current_country != 0 && $current_country != '')
			{
				$statesmodel = new Default_Model_States();
				$statesmodeldata = $statesmodel->getStatesList($current_country);
					
				$empcommdetailsform->current_state->addMultiOption('','Select State');
				foreach($statesmodeldata as $res)
				$empcommdetailsform->current_state->addMultiOption($res['id'].'!@#'.utf8_encode($res['state_name']),utf8_encode($res['state_name']));
				if(isset($current_stateparam) && $current_stateparam != 0 && $current_stateparam != '')
				$empcommdetailsform->setDefault('current_state',$current_stateparam);
			}

			if(isset($current_stateparam) && $current_stateparam != 0 && $current_stateparam != '')
			{
				$citiesmodel = new Default_Model_Cities();
				$citiesmodeldata = $citiesmodel->getCitiesList($current_state);
					
				$empcommdetailsform->current_city->addMultiOption('','Select City');
				foreach($citiesmodeldata as $res)
				$empcommdetailsform->current_city->addMultiOption($res['id'].'!@#'.utf8_encode($res['city_name']),utf8_encode($res['city_name']));
				if(isset($current_cityparam) && $current_cityparam != 0 && $current_cityparam != '')
				$empcommdetailsform->setDefault('current_city',$current_cityparam);
			}

			return $msgarray;
		}

	}



}