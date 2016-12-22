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

class Default_CitiesController extends Zend_Controller_Action
{

    private $options;
    public function preDispatch()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('getcitiescand', 'json')->initContext();		
    }
	
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
		
    }

    public function indexAction()
    {
		$citiesmodel = new Default_Model_Cities();	
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
			$sort = 'DESC';$by = 'c.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else 
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'c.modifieddate';
			if($dashboardcall == 'Yes')
				$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
			else 
				$perPage = $this->_getParam('per_page',PERPAGE);
			$pageNo = $this->_getParam('page', 1);
			/** search from grid - START **/
			$searchData = $this->_getParam('searchData');	
			$searchData = rtrim($searchData,',');
			/** search from grid - END **/
		}
		$dataTmp = $citiesmodel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);		 		
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
		$objName = 'cities';
		$countriesModel = new Default_Model_Countries();
		$citiesform = new Default_Form_cities();
		$statesmodel = new Default_Model_States();
		$citiesform->removeElement("submit");
		$citiesmodel = new Default_Model_Cities();
		
		$elements = $citiesform->getElements();
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
		    if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					$data = $citiesmodel->getCitiesDataByID($id);
					if(!empty($data))
					{
						$ent=ENT_COMPAT; 
						$charset='ISO-8859-1';
						$city_name = htmlentities($data[0]['city'], $ent, $charset);
						$countrieslistArr = $countriesModel->getActiveCountryName($data[0]['countryid']);
						$citiesform->countryid->addMultiOption($countrieslistArr[0]['country_id_org'],utf8_encode($countrieslistArr[0]['country']));
						$data[0]['countryid']=$countrieslistArr[0]['country'];
						$statenameArr = $statesmodel->getStateName($data[0]['state']);
						$citiesform->state->addMultiOption($statenameArr[0]['id'].'!@#'.$statenameArr[0]['statename'],utf8_encode($statenameArr[0]['statename']));
						$data[0]['state']=$statenameArr[0]['statename'];
						$citiesform->city->addMultiOption($data[0]['city_org_id'].'-'.$city_name,utf8_encode($city_name));
						$citiesform->populate($data[0]);
						$citiesform->setDefault('state',$statenameArr[0]['id'].'!@#'.$statenameArr[0]['statename']);
						$this->view->controllername = $objName;
						$this->view->id = $id;
						$this->view->form = $citiesform;
						$this->view->cityValue = $data[0]['city_org_id'].'-'.$city_name;
						$this->view->data = $data[0];
						$this->view->cityname = $city_name;
						$this->view->ermsg = '';
					}
					else
					{
						$this->view->ermsg = 'norecord';
					}
				}
                else
				{
					$this->view->ermsg = 'norecord';
				}				
			}
			else
			{
				$this->view->ermsg = 'norecord';
			}
		}
		catch(Exception $e)
		{
			   $this->view->ermsg = 'nodata';
		}
	}
	
	public function editAction()
	{	
	    $auth = Zend_Auth::getInstance();
		$popConfigPermission = array();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;
					$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
		$id = $this->getRequest()->getParam('id');		
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		if(sapp_Global::_checkprivileges(COUNTRIES,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
				array_push($popConfigPermission,'country');
		}
		if(sapp_Global::_checkprivileges(STATES,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
				array_push($popConfigPermission,'state');
		}
		$citiesform = new Default_Form_cities();
		$citiesmodel = new Default_Model_Cities();
		$countriesModel = new Default_Model_Countries();
		$statesmodel = new Default_Model_States();
		$msgarray = array();
		
		$countrieslistArr = $countriesModel->getActiveCountriesList();
			if(sizeof($countrieslistArr)>0)
			{
			    $citiesform->countryid->addMultiOption('','Select Country');
				
				foreach ($countrieslistArr as $countrieslistres){
					$citiesform->countryid->addMultiOption($countrieslistres['country_id_org'],$countrieslistres['country']);
				}
			}else
			{
			    $msgarray['countryid'] = 'Countries are not configured yet.';
			}
		$allStatesData = $statesmodel->fetchAll('isactive=1','state')->toArray();
		if(empty($allStatesData))
		{
			$msgarray['state'] = 'States are not configured yet.';
			$flag = 'false';
		}		
		try
		{
		    if($id)
			{
			    $data = $citiesmodel->getCitiesDataByID($id);
				if(!empty($data))
				{
				$statesmodel = new Default_Model_States();
				$statesmodeldata = $statesmodel->getStatesList($data[0]['countryid']);
				$statenameArr = $statesmodel->getStateName($data[0]['state']);
				foreach ($statesmodeldata as $state) {
				   $citiesform->state->addMultiOption($state['id'].'!@#'.$state['state_name'],utf8_encode($state['state_name']));
				}
				$citiesmodeldata = $citiesmodel->getCitiesList($data[0]['state']);
				foreach ($citiesmodeldata as $city) {
				   $citiesform->city->addMultiOption($city['id'].'!@#'.$city['city_name'],utf8_encode($city['city_name']));
				}
				$citiesform->populate($data[0]);
				$citiesform->setDefault('state',$statenameArr[0]['id'].'!@#'.$statenameArr[0]['statename']);
				$this->view->cityValue = $data[0]['city_org_id'].'!@#'.$data[0]['city'];
				$this->view->data = $data;
				$this->view->id = $id;
				$this->view->ermsg = '';
				}
				else
				{
				    $this->view->ermsg = 'norecord';
				}
			}
            else
			{
				$this->view->ermsg = '';
			}			
		
		}
		catch(Exception $e)
		{
			   $this->view->ermsg = 'nodata';
		}
		
		$this->view->form = $citiesform;
		$this->view->msgarray = $msgarray;
                
		if($this->getRequest()->getPost()){
		        $id = $this->_request->getParam('id'); 
				$errorflag = "true";
				$msgarray = array();
				$newcityArr = array();
				$dbstate = '';
				$citystring = '';
				$dbcountryid ='';
				$countryid = $this->_request->getParam('countryid');
				$stateidstr = $this->_request->getParam('state');
			    $stateid = intval($stateidstr);
				$cityArr = $this->_request->getParam('city');
				if(!empty($cityArr))
				{
					
					$othercityname = trim($this->_request->getParam('othercityname'));
					if(in_array('other',$cityArr))
					{
					 if($othercityname == '')
					   {
						 $msgarray['othercityname'] = "Please enter city name.";
						 $msgarray['dupcityname'] = '';
						 $msgarray['countid'] = $countryid;
						 $msgarray['stateidval'] = $stateid;
						 $errorflag = "false"; 
					   }
					  else
					   {
					    $isDuplicateCityNameArr = $citiesmodel->getDuplicateCityName($othercityname,$stateid); 
						   if($isDuplicateCityNameArr[0]['count'] > 0)
							{
							   $errorflag = "false"; 
								$msgarray['othercityname'] = "City already exists.";
								$msgarray['dupcityname'] = $othercityname;
								$msgarray['countid'] = $countryid;
								$msgarray['stateidval'] = $stateid;
							}
							else
							{ 
                                $citystring = implode(",",$cityArr); 							
                                if(sizeof($cityArr)>1)
								{
								$newcityArr = $cityArr;
								array_pop($newcityArr);
									$citystring = implode(",",$newcityArr);
									$citystringcomma = trim(str_replace("!@#", ",", $citystring),',');
									$citystringArr = explode(",",$citystringcomma);
									foreach($citystringArr as $key =>$val)
									{
										if (is_numeric($val))
										  $cityid[] = $val;
										else
										  $cityname[] = $val;  						
									
									}
								}							
								$dbstate = $othercityname;
								$errorflag = "true"; 
							}
					   }
					}
					else
					{
					    $citystring = implode(",",$cityArr);
						$citystringcomma = trim(str_replace("!@#", ",", $citystring),',');
						$citystringArr = explode(",",$citystringcomma);
						foreach($citystringArr as $key =>$val)
						{
							if (is_numeric($val))
							  $cityid[] = $val;
							else
							  $cityname[] = $val;  						
						}
						$errorflag = "true"; 
					}
				}
				else
				{
				  $msgarray['countid'] = $countryid;
				  $msgarray['stateidval'] = $stateid;
				  $msgarray['stateiddropdown'] = $stateidstr;
				  $errorflag = "false";
				}
		    if($citiesform->isValid($this->_request->getPost()) && $errorflag == "true" && $citystring!=''){
				$actionflag = '';
				$tableid  = ''; 
				  if(in_array('other',$cityArr))
					{
					  if($othercityname !='')
					  {
					    $NewCityId = $citiesmodel->SaveMainCityData($stateid,$othercityname);
						$NewCityInsertedId = $citiesmodel->SaveorUpdateCitiesData($countryid,$stateid,$othercityname,$NewCityId,$loginUserId);
							if(sizeof($cityArr)>1)
							{
								if(!empty($cityid))
								{
									for($j=0;$j<sizeof($cityid);$j++)
									   {
										 $Id = $citiesmodel->SaveorUpdateCitiesData($countryid,$stateid,$cityname[$j],$cityid[$j],$loginUserId);
									   }
								}
							}
						$actionflag = 1;
						$tableid = $NewCityInsertedId;
						$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"City  added successfully.")); 
					   }	
					}
				   else
                    {
					   for($j=0;$j<sizeof($cityid);$j++)
					   {
					     $Id = $citiesmodel->SaveorUpdateCitiesData($countryid,$stateid,$cityname[$j],$cityid[$j],$loginUserId);
					   }
					   if($id)
					   {
					 	 $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"City  updated successfully."));
						 $actionflag = 2;
					     $tableid = $id;
					   }
					   else
					   {	
						   if(sizeof($cityid)>1)
							$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Cities  added successfully."));
						   else
							$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"City  added successfully.")); 					   
						 $actionflag = 1;
					     $tableid = $Id;	
						}	
                    }					
				    $menuID = CITIES;
					$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
    			    $this->_redirect('cities');		
			}else
			{
     			$messages = $citiesform->getMessages();
				foreach ($messages as $key => $val)
					{
						foreach($val as $key2 => $val2)
						 {
							  $msgarray[$key] = $val2;
							  break;
						 }
					}
				if(isset($countryid) && $countryid != 0 && $countryid != '')
				{
					$statesmodel = new Default_Model_States();
					$statesmodeldata = $statesmodel->getBasicStatesList($countryid);
					
					foreach($statesmodeldata as $res)
                    {					
					 $citiesform->state->addMultiOption($res['state_id_org'].'!@#'.utf8_encode($res['state']),utf8_encode($res['state']));
					} 
					if(isset($stateidstr) && $stateidstr != 0 && $stateidstr != '')
						$citiesform->setDefault('state',$stateidstr);			
				}
				
				if(isset($stateidstr) && $stateidstr != 0 && $stateidstr != '')
				{
					$citiesmodel = new Default_Model_Cities();
					$citiesmodeldata = $citiesmodel->getCitiesList($stateid);
					foreach($citiesmodeldata as $res)
                    {					
					 $citiesform->city->addMultiOption($res['id'].'!@#'.utf8_encode($res['city_name']),utf8_encode($res['city_name']));
					} 
					 $citiesform->city->addMultiOption('other','Other');
				}
				
				$this->view->msgarray = $msgarray;
			}
		}
		$this->view->popConfigPermission = $popConfigPermission;
	}
	
	public function deleteAction()
	{
	     $auth = Zend_Auth::getInstance();
     		if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
				}
		 $id = $this->_request->getParam('objid');
		 $deleteflag= $this->_request->getParam('deleteflag');
		 $messages['message'] = '';
		 $actionflag = 3;
		    if($id)
			{
			 $citiesmodel = new Default_Model_Cities();
			  $citydata = $citiesmodel->getCitiesDataByID($id);
			  $data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
			  $where = array('id=?'=>$id);
			  $Id = $citiesmodel->deleteCityData($data, $where);
			    if($Id == 'update')
				{
				   $menuID = CITIES;
				   $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id); 
				   $configmail = sapp_Global::send_configuration_mail('City',$citydata[0]['city']); 				   
				   $messages['message'] = 'City deleted successfully.';
				   $messages['msgtype'] = 'success';
				}   
				else
				{
                   $messages['message'] = 'City cannot be deleted.';	
                   $messages['msgtype'] = 'error';  
                }				   
			}
			else
			{ 
			 $messages['message'] = 'City cannot be deleted.';
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
	
        public function getcitiescandAction()
        {
            $state_id = $this->_getParam('state_id',null);
            $city_model = new Default_Model_Cities();
            $city_options = sapp_Global::selectOptionBuilder('', 'Select City');
            if($state_id != '')
            {
                $city_data = $city_model->getBasicCitiesList($state_id);
                foreach($city_data as $data)
                {
                    $city_options .= sapp_Global::selectOptionBuilder($data['city_org_id'], $data['city']);
                }
            }
            $this->_helper->_json(array('options'=>$city_options));
        }
	
	public function addpopupAction()
	{
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->getRequest()->getParam('id');
		$selectedcountryid = $this->_request->getParam('selectcountryid');
		$selectedstateid = $this->_request->getParam('selectstateid');
		$msgarray = array();$setDefaultString = '';$citystring = '';$controllername = 'cities';
		$citiesform = new Default_Form_cities();
		$citiesmodel = new Default_Model_Cities();
		$countriesModel = new Default_Model_Countries();
		$citiesform->setAction(BASE_URL.'cities/addpopup/selectcountryid/'.$selectedcountryid.'/selectstateid/'.$selectedstateid);			
		$countrieslistArr = $countriesModel->getActiveCountriesList();
		if(sizeof($countrieslistArr)>0)
		{
			$citiesform->countryid->addMultiOption('','Select Country');
			
			foreach ($countrieslistArr as $countrieslistres)
			{
				if($selectedcountryid != '')
				{
					if($countrieslistres['country_id_org'] == $selectedcountryid)
					{
						$citiesform->countryid->addMultiOption($countrieslistres['country_id_org'],$countrieslistres['country']);
						$citiesform->setDefault('countryid',$selectedcountryid);
					}
				}
				else
				{
					$citiesform->countryid->addMultiOption($countrieslistres['country_id_org'],$countrieslistres['country']);
				}
			}
		}else
		{
			$msgarray['countryid'] = 'Countries are not configured yet.';
		}
		
		if(isset($selectedcountryid) && $selectedcountryid != '')
		{
			$statesmodel = new Default_Model_States();
			$statesmodeldata = $statesmodel->getBasicStatesList($selectedcountryid);
			$citiesform->state->addMultiOption('','Select State');
			$setDefaultString = '';
			
			foreach($statesmodeldata as $res)
			{
				$citiesform->state->addMultiOption($res['state_id_org'].'!@#'.utf8_encode($res['state']),utf8_encode($res['state']));						
					if($selectedstateid != '' && $res['state_id_org'] == $selectedstateid){
						$setDefaultString = $res['state_id_org'].'!@#'.utf8_encode($res['state']);
					}
			}
			$citiesform->setDefault('state',$setDefaultString);
        }else{
			$citiesform->state->addMultiOption('','Select State');
		}
		
		if($selectedstateid != '' && $selectedcountryid != '')
		{
			$citiesmodel = new Default_Model_Cities();
			$citiesmodeldata = $citiesmodel->getCitiesList($selectedstateid,'addcity');
			
			$citiesform->city->addMultiOption('','Select City');
			foreach($citiesmodeldata as $res)
			{					
				$citiesform->city->addMultiOption($res['id'].'!@#'.utf8_encode($res['city_name']),utf8_encode($res['city_name']));
			} 
			$citiesform->city->addMultiOption('other','Other');
			if($citystring == 'other')
				$citiesform->setDefault('city','other'); 		
		}
		
		if($this->getRequest()->getPost())
		{
			$id = $this->_request->getParam('id'); 
			$errorflag = "true";
			$msgarray = array();
			$newcityArr = array();
			$dbstate = '';							
			$dbcountryid ='';
			$countryid = $this->_request->getParam('countryid');
			$stateidstr = $this->_request->getParam('state');
			$stateid = intval($stateidstr);
			$cityArr = $this->_request->getParam('city');
			if(!empty($cityArr))
			{
				$othercityname = $this->_request->getParam('othercityname');
				if(in_array('other',$cityArr))
				{
				 if($othercityname == '')
				   {
					 $msgarray['othercityname'] = "Please enter city name.";
					 $msgarray['dupcityname'] = '';
					 $msgarray['countid'] = $countryid;
					 $msgarray['stateidval'] = $stateid;
					 $errorflag = "false"; 
				   }
				  else
				   {
					$isDuplicateCityNameArr = $citiesmodel->getDuplicateCityName($othercityname,$stateid); 
					  if($isDuplicateCityNameArr[0]['count'] > 0)
						{
						   $errorflag = "false"; 
							$msgarray['othercityname'] = "City already exists.";
							$msgarray['dupcityname'] = $othercityname;
							$msgarray['countid'] = $countryid;
							$msgarray['stateidval'] = $stateid;
						}
						else
						{
                            $citystring = implode(",",$cityArr);
                            if(sizeof($cityArr)>1)
							{
							$newcityArr = $cityArr;
							array_pop($newcityArr);
								$citystring = implode(",",$newcityArr);
								$citystringcomma = trim(str_replace("!@#", ",", $citystring),',');
								$citystringArr = explode(",",$citystringcomma);
								foreach($citystringArr as $key =>$val)
								{
									if (is_numeric($val))
									  $cityid[] = $val;
									else
									  $cityname[] = $val;  						
								
								}
							}								
							$dbstate = $othercityname;
							$errorflag = "true"; 
						}
				   }
				}
				else
				{
				    $citystring = implode(",",$cityArr);
					$citystringcomma = trim(str_replace("!@#", ",", $citystring),',');
					$citystringArr = explode(",",$citystringcomma);
					foreach($citystringArr as $key =>$val)
					{
						if (is_numeric($val))
						  $cityid[] = $val;
						else
						  $cityname[] = $val;  						
					}
					$errorflag = "true"; 
				}
			}
			else
			{
			  $msgarray['countid'] = $countryid;
			  $msgarray['stateidval'] = $stateid;
			  $msgarray['stateiddropdown'] = $stateidstr;
			  $errorflag = "false";
			}		
												
		    if($citiesform->isValid($this->_request->getPost()) && $errorflag == "true" && $citystring!='')
			{
				$actionflag = '';
				$tableid  = ''; 
				if(in_array('other',$cityArr))
				{
					if($othercityname !='')
					{
					    $NewCityId = $citiesmodel->SaveMainCityData($stateid,$othercityname);
						$NewCityInsertedId = $citiesmodel->SaveorUpdateCitiesData($countryid,$stateid,$othercityname,$NewCityId,$loginUserId);
						if(sizeof($cityArr)>1)
							{
							    if(!empty($cityid))
								{
									for($j=0;$j<sizeof($cityid);$j++)
									   {
										 $Id = $citiesmodel->SaveorUpdateCitiesData($countryid,$stateid,$cityname[$j],$cityid[$j],$loginUserId);
									   }
								}   
							}
						$actionflag = 1;
						$tableid = $NewCityInsertedId;						
					}	
				}
				else
                {					   
					for($j=0;$j<sizeof($cityid);$j++)
					{
					    $Id = $citiesmodel->SaveorUpdateCitiesData($countryid,$stateid,$cityname[$j],$cityid[$j],$loginUserId);
					}
                    $actionflag = 1;
					$tableid = $Id;	   				
                }					
				$menuID = CITIES;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
				if(isset($selectedstateid) && isset($selectedcountryid))
				{
					$cityData = $citiesmodel->fetchAll('isactive = 1 and state = '.$selectedstateid.' and countryid = '.$selectedcountryid,'city')->toArray();
				}
				else $cityData = array();				
				
				$opt ='';   
				foreach($cityData as $record)
				{
					$opt .= sapp_Global::selectOptionBuilder($record['city_org_id'], $record['city']);
				}
				
				$this->view->cityData = $opt;
				$this->view->eventact = 'added';
				$close = 'close';
				$this->view->popup=$close;
			}
			else
			{
     			$messages = $citiesform->getMessages();				
				foreach ($messages as $key => $val)
				{
					foreach($val as $key2 => $val2)
					{
						$msgarray[$key] = $val2;
						break;
						
					}
				}
				if(isset($countryid) && $countryid != 0 && $countryid != '')
				{
					$statesmodel = new Default_Model_States();
					$statesmodeldata = $statesmodel->getBasicStatesList($countryid);
					
					foreach($statesmodeldata as $res)
                    {					
						$citiesform->state->addMultiOption($res['state_id_org'].'!@#'.utf8_encode($res['state']),utf8_encode($res['state']));
					} 
					if(isset($stateidstr) && $stateidstr != 0 && $stateidstr != '')
						$citiesform->setDefault('state',$stateidstr);			
				}
				
				if(isset($stateidstr) && $stateidstr != 0 && $stateidstr != '')
				{
					$citiesmodel = new Default_Model_Cities();
					$citiesmodeldata = $citiesmodel->getCitiesList($stateid);
					
					foreach($citiesmodeldata as $res)
                    {					
						$citiesform->city->addMultiOption($res['id'].'!@#'.utf8_encode($res['city_name']),utf8_encode($res['city_name']));
					} 
					$citiesform->city->addMultiOption('other','Other');
                    if($citystring == 'other')
					    $citiesform->setDefault('city','other'); 					 
				}
			}
		}
		$this->view->controllername = $controllername;
		$this->view->ermsg = '';
		$this->view->form = $citiesform;
		$this->view->msgarray = $msgarray;
	}

	public function addnewcityAction()
	{
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->getRequest()->getParam('id');
		$selectedcountryid = $this->_request->getParam('selectcountryid');
		$selectedstateid = $this->_request->getParam('selectstateid');
		
		$msgarray = array();$setDefaultString = '';$citystring = '';$controllername = 'cities';
		
		$citiesform = new Default_Form_cities();
		$citiesmodel = new Default_Model_Cities();
		$countriesModel = new Default_Model_Countries();
		$statesmodel = new Default_Model_States();
		
		/* Changing the form */		
		$citiesform->setAction(BASE_URL.'cities/addnewcity/selectcountryid/'.$selectedcountryid.'/selectstateid/'.$selectedstateid);
		$citiesform->removeElement('city');
		$citiesform->addElement('text', 'city',array(
					'label'      => 'City',
					'maxlength'		=> '20',
					'required'   => true,
					'validators' => array(
						array('validator' => 'NotEmpty','options'=> array('messages' => 'Please enter city name.')
         ))));
		
		/* END */
		$countrieslistArr = $countriesModel->getTotalCountriesList('');
		if(sizeof($countrieslistArr)>0)
		{
			$citiesform->countryid->addMultiOption('','Select Country');
			
			foreach ($countrieslistArr as $countrieslistres)
			{
				if($selectedcountryid != '')
				{
					if($countrieslistres['id'] == $selectedcountryid)
					{
						$citiesform->countryid->addMultiOption($countrieslistres['id'],utf8_encode($countrieslistres['country_name']) );
						$citiesform->setDefault('countryid',$selectedcountryid);
					}
				}
				else
				{
					$citiesform->countryid->addMultiOption($countrieslistres['id'],utf8_encode($countrieslistres['country_name']) );
				}
				$citiesform->countryid->setAttrib('onchange', 'displayParticularState_normal(this,"","state","city")');
			}
		}else
		{
			$msgarray['countryid'] = 'Countries are not configured yet.';
		}
		$countryid = $this->_request->getParam('countryid');
		
		if((isset($selectedcountryid) && $selectedcountryid != '') || (isset($countryid) && $countryid != '' ))
		{		
			if($countryid)
			$statesmodeldata = $statesmodel->getStatesList($countryid,'');
			else
			$statesmodeldata = $statesmodel->getStatesList($selectedcountryid,'');
			$citiesform->state->addMultiOption('','Select State');
			
			foreach($statesmodeldata as $res)
			{
				$citiesform->state->addMultiOption($res['id'],utf8_encode($res['state_name']));
				if($selectedstateid != '')
				{					
					if($res['id'] == $selectedstateid)
						$setDefaultString = $res['id'];
					
				}else if($countryid!=''){
					if($res['id'] == $countryid)
						$setDefaultString = $res['id'];			
				}
			}
			$citiesform->setDefault('state',$setDefaultString);
        }else{
			$citiesform->state->addMultiOption('','Select State');
		}
		if($this->getRequest()->getPost())
		{
			$id = $this->_request->getParam('id'); 
			$errorflag = "true";
			$msgarray = array();
			$dbstate = '';							
			$dbcountryid ='';			
			$stateidstr = $this->_request->getParam('state');
			$stateid = intval($stateidstr);
			$city = $this->_request->getParam('city');
			if(isset($stateid))
			{
				$isDuplicateCityNameArr = $citiesmodel->getDuplicateCityName($city,$stateid); 
				if($isDuplicateCityNameArr[0]['count'] > 0)
				{
					$errorflag = "false"; 
					$msgarray['city'] = "City already exists.";
				}
			}else{
				$errorflag = "false"; 
				$msgarray['state'] = "Please select state.";
			}
		    if($citiesform->isValid($this->_request->getPost()) && $errorflag == "true")
			{
				$city = $this->_request->getParam('city');
				$actionflag = '';
				$tableid  = ''; 
				$NewCityId = $citiesmodel->SaveMainCityData($stateid,$city);
				$actionflag = 1;
				$tableid = $NewCityId;	
				$menuID = CITIES;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
				if(isset($selectedstateid) && isset($selectedcountryid))
				{
					$cityData = $citiesmodel->getCitiesList($selectedstateid,'city');
				}
				else $cityData = array();				
				
				$opt ='';   
				foreach($cityData as $record)
				{
					$opt .= sapp_Global::selectOptionBuilder($record['id'], $record['city_name']);
				}
				$this->view->cityData = $opt;
				$this->view->eventact = 'added';
				$close = 'close';
				$this->view->popup=$close;
			}
			else
			{
     			$messages = $citiesform->getMessages();				
				foreach ($messages as $key => $val)
				{
					foreach($val as $key2 => $val2)
					{
						$msgarray[$key] = $val2;
						break;
						
					}
				}
				if(isset($countryid) && $countryid != 0 && $countryid != '')
				{
					$statesmodel = new Default_Model_States();
					$statesmodeldata = $statesmodel->getBasicStatesList($countryid);
					
					foreach($statesmodeldata as $res)
                    {					
						$citiesform->state->addMultiOption($res['state_id_org'].'!@#'.utf8_encode($res['state']),utf8_encode($res['state']));
					} 
					if(isset($stateidstr) && $stateidstr != 0 && $stateidstr != '')
						$citiesform->setDefault('state',$stateidstr);			
				}			
			}
		}
		$this->view->controllername = $controllername;
		$this->view->ermsg = '';
		$this->view->form = $citiesform;
		$this->view->msgarray = $msgarray;
	}
}

