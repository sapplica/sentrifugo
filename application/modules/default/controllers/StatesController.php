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

class Default_StatesController extends Zend_Controller_Action
{

    private $options;
    public function preDispatch()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('getstatescand', 'json')->initContext();

    }
	
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();		
    }

    public function indexAction()
    {
	
		$statesmodel = new Default_Model_States();	
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
			$sort = 'DESC';$by = 's.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else 
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'s.modifieddate';
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
				
		$dataTmp = $statesmodel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);		 		
				
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
		$objName = 'states';
		$statesform = new Default_Form_states();
		
		$statesform->removeElement("submit");
		$elements = $statesform->getElements();
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)
			{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
				$element->setAttrib("disabled", "disabled");
					}
        	}
        }
		
		$countriesModel = new Default_Model_Countries();
		$statesmodel = new Default_Model_States();
		try
        { 		
			if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					$data = $statesmodel->getStatesDataByID($id);
					if(!empty($data))
					{
					  $countrieslistArr = $countriesModel->getActiveCountryName($data[0]['countryid']);
					  $statesform->countryid->addMultiOption($countrieslistArr[0]['country_id_org'],utf8_encode($countrieslistArr[0]['country']));
					  $data[0]['countryid']=$countrieslistArr[0]['country'];
					  $statesform->state->addMultiOption($data[0]['state_id_org'].'-'.$data[0]['state'],utf8_encode($data[0]['state']));
						$statesform->populate($data[0]);
						$this->view->form = $statesform;
						$this->view->stateValue = $data[0]['state_id_org'].'-'.$data[0]['state'];
						$this->view->data = $data;
						$this->view->ermsg = '';
						$this->view->controllername = $objName;
						$this->view->id = $id;
						
					}
					else
					{
					 $this->view->ermsg = 'norecord';
					}
				}else
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
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;
					$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		$popConfigPermission = array();
		if(sapp_Global::_checkprivileges(COUNTRIES,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
						array_push($popConfigPermission,'country');
		}
		$statesform = new Default_Form_states();
		$statesmodel = new Default_Model_States();
		$countriesModel = new Default_Model_Countries();
		$msgarray = array();
		
		    $countrieslistArr = $countriesModel->getActiveCountriesList();
			if(sizeof($countrieslistArr)>0)
			{
			    $statesform->countryid->addMultiOption('','Select Country');
				
				foreach ($countrieslistArr as $countrieslistres){
					$statesform->countryid->addMultiOption($countrieslistres['country_id_org'],$countrieslistres['country']);
				}
			}else
			{
			    $msgarray['countryid'] = 'Countries are not configured yet.';
			}
		try
        { 		
			if($id)
			{
				$data = $statesmodel->getStatesDataByID($id);
				if(!empty($data))
				{
					$statesmodeldata = $statesmodel->getStatesList($data[0]['countryid']);
					foreach ($statesmodeldata as $state) {
					   $statesform->state->addMultiOption($state['id'].'!@#'.$state['state_name'],utf8_encode($state['state_name']));
					}
					$statesform->populate($data[0]);
					$statesform->submit->setLabel('Update');
					$this->view->stateValue = $data[0]['state_id_org'].'!@#'.$data[0]['state'];
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
		
		$this->view->form = $statesform;
		$this->view->msgarray = $msgarray;
		if($this->getRequest()->getPost()){
		        $id = $this->_request->getParam('id'); 
				$errorflag = "true";
				$msgarray = array();
				$newstateArr = array();
				$dbstate = '';
				$dbcountryid ='';
				$statestring = '';
			    $countryid = $this->_request->getParam('countryid');
				$stateArr = $this->_request->getParam('state');
				if(!empty($stateArr))
				{
					
					$otherstatename = trim($this->_request->getParam('otherstatename'));
					if(in_array('other',$stateArr))
					{
					 if($otherstatename == '')
					   {
						 $msgarray['otherstatename'] = "Please enter state name.";
						 $msgarray['dupstatename'] = '';
						 $msgarray['countid'] = $countryid;
						 $errorflag = "false"; 
					   }
					  else
					   {
						$isDuplicateStateNameArr = $statesmodel->getDuplicateStateName($otherstatename,$countryid);
							if($isDuplicateStateNameArr[0]['count'] > 0)
							{
							   $errorflag = "false"; 
								$msgarray['otherstatename'] = "State already exists";
								$msgarray['dupstatename'] = $otherstatename;
								$msgarray['countid'] = $countryid;
							}
							else
							{ 	
							    $statestring = implode(",",$stateArr);
                                if(sizeof($stateArr) > 1)
								{
								    $newstateArr = $stateArr;
								    array_pop($newstateArr);
									$statestring = implode(",",$newstateArr);
									$statestringcomma = trim(str_replace("!@#", ",", $statestring),',');
									$statestringArr = explode(",",$statestringcomma);
									foreach($statestringArr as $key =>$val)
									{
										if (is_numeric($val))
										  $stateid[] = $val;
										else
										  $statename[] = $val;  						
									
									}
								}	
								$dbstate = $otherstatename;
								$errorflag = "true"; 
							}	
					   }
					}
					else
					{
					    $statestring = implode(",",$stateArr);
						$statestringcomma = trim(str_replace("!@#", ",", $statestring),',');
						$statestringArr = explode(",",$statestringcomma);
						foreach($statestringArr as $key =>$val)
						{
							if (is_numeric($val))
							  $stateid[] = $val;
							else
							  $statename[] = $val;  						
						
						}
						$errorflag = "true"; 
					}
				}else
				{
				  $msgarray['countid'] = $countryid;
				  $errorflag = "false";
				}
		if($statesform->isValid($this->_request->getPost()) && $errorflag == "true" && $statestring!=''){
				$actionflag = '';
				$tableid  = ''; 
				   if(in_array('other',$stateArr))
					{
					  if($otherstatename !='')
					  {
					    $NewStateId = $statesmodel->SaveMainStateData($countryid,$otherstatename);
						$NewStateInsertedId = $statesmodel->SaveorUpdateStatesData($countryid,$otherstatename,$NewStateId,$loginUserId);
							if(sizeof($stateArr) > 1)
							{
								if(!empty($stateid))
								{	
								   for($j=0;$j<sizeof($stateid);$j++)
									  {
										 $Id = $statesmodel->SaveorUpdateStatesData($countryid,$statename[$j],$stateid[$j],$loginUserId);
									  }
								}
							}
						$actionflag = 1;
						$tableid = $NewStateInsertedId;
						$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"State  added successfully.")); 
					   }	
					}
				  else
                    {
					 for($j=0;$j<sizeof($stateid);$j++)
					  {
					     $Id = $statesmodel->SaveorUpdateStatesData($countryid,$statename[$j],$stateid[$j],$loginUserId);
					  }
                       
					   if($id)
					   {
					 	 $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"State  updated successfully."));
						 $actionflag = 2;
					     $tableid = $id;
					   }
					   else
					   {
						   if(sizeof($stateid)>1)
							$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"States  added successfully."));
						   else
							$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"State  added successfully.")); 					   
							
						 $actionflag = 1;
					     $tableid = $Id;	
						}	
                    }					
					$menuID = STATES;
					$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
    			    $this->_redirect('states');		
			}else
			{
     			$messages = $statesform->getMessages();
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
					$statesmodeldata = $statesmodel->getStatesList($countryid);
					
					
						foreach($statesmodeldata as $res)
						{					
						 $statesform->state->addMultiOption($res['id'].'!@#'.utf8_encode($res['state_name']),utf8_encode($res['state_name']));
						} 
					    $statesform->state->addMultiOption('other','Other');
					
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
		 $messages['msgtype'] = 'error';
		 $actionflag = 3;
		 $stateOrgId = '';
		    if($id)
			{
			 $statesmodel = new Default_Model_States();
			 $citiesmodel = new Default_Model_Cities();
			  $statedata = $statesmodel->getStatesDataByID($id);
			  if(!empty($statedata))
			    $stateOrgId = $statedata[0]['state_id_org'];
			  $data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
			  $where = array('id=?'=>$id);
			  $Id = $statesmodel->deleteStateData($data, $where);
			  
			  if($stateOrgId !='')
			  {
			        $citydata = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
					$citywhere = array('state=?'=>$stateOrgId);
					$CityId = $citiesmodel->deleteCityData($citydata, $citywhere); 
			  }
			    if($Id == 'update')
				{
				   $menuID = STATES;
				   $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id); 
				   $configmail = sapp_Global::send_configuration_mail('State',$statedata[0]['state']); 				   
				   $messages['message'] = 'State deleted successfully';
				   $messages['msgtype'] = 'success';
				}   
				else
				{
                   $messages['message'] = 'State cannot be deleted';
				   $messages['msgtype'] = 'error';
                }				   
			}
			else
			{ 
			 $messages['message'] = 'State cannot be deleted';
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
	
	public function getstatesAction()
	{
	
	    $ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('getstates', 'html')->initContext();
		
		
		$country_id = $this->_request->getParam('country_id');
		$con = $this->_request->getParam('con');
		$statesform = new Default_Form_states();
		
		$statesmodel = new Default_Model_States();
		if($con == 'state')
		$statesmodeldata = $statesmodel->getBasicStatesList($country_id);
		else
		$statesmodeldata = $statesmodel->getStatesList($country_id);
		$this->view->statesform=$statesform;
		$this->view->con = $con;
		$this->view->statesmodeldata=$statesmodeldata;
	
	}
    /**
     * This action is used to get state options on country dropdown.
     * @paramaters
     * @param country_id = id of country.
     * 
     * @return String State options.
     */
    public function getstatescandAction()
    {
        $state_model = new Default_Model_States();
        $country_id = $this->_getParam('country_id',null);
        $state_opt = sapp_Global::selectOptionBuilder('', 'Select State');
        if($country_id != '')
        {
            $state_data = $state_model->getStatesList($country_id);
            foreach($state_data as $state)
            {
                $state_opt .= sapp_Global::selectOptionBuilder($state['id'], $state['state_name']);
            }
        }
        $this->_helper->_json(array('options'=>$state_opt));
        
    }
	
	public function addpopupAction()
	{
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		}
		
		$id = $this->getRequest()->getParam('id');		
		$selectedcountryid = $this->_request->getParam('selectcountryid',null);
		$countryid = $this->_request->getParam('countryid');
		
		$msgarray = array();	$controllername = 'states';		$statestring = '';
		
		$statesform = new Default_Form_states();
		$statesmodel = new Default_Model_States();
		$countriesModel = new Default_Model_Countries();
		
		
		$statesform->setAction(BASE_URL.'states/addpopup/selectcountryid/'.$selectedcountryid);		
		
		
		
		$countrieslistArr = $countriesModel->getActiveCountriesList();
		if(sizeof($countrieslistArr)>0)
		{
			$statesform->countryid->addMultiOption('','Select Country');
			
			foreach ($countrieslistArr as $countrieslistres)
			{				
				if(isset($selectedcountryid))
				{
					if($countrieslistres['country_id_org'] == $selectedcountryid)
					{
						$statesform->countryid->addMultiOption($countrieslistres['country_id_org'],$countrieslistres['country']);
						$statesform->setDefault('countryid',$selectedcountryid);
					}
				}else{
					$statesform->countryid->addMultiOption($countrieslistres['country_id_org'],$countrieslistres['country']);
				}				
			}
		}
		else
		{
			$msgarray['countryid'] = 'Countries are not configured yet.';
		}	
		
		if(isset($selectedcountryid) && $selectedcountryid != 0 && $selectedcountryid != '')
		{			
			$statesmodeldata = $statesmodel->getStatesList($selectedcountryid,'addstate');
			$statesform->state->addMultiOption('','Select State');
			foreach($statesmodeldata as $res)
			{					
			 $statesform->state->addMultiOption($res['id'].'!@#'.utf8_encode($res['state_name']),utf8_encode($res['state_name']));
			} 
			$statesform->state->addMultiOption('other','Other');
			
			if($statestring == 'other')
				$statesform->setDefault('state','other');					
		}	
		
		if($this->getRequest()->getPost())
		{	
			$id = $this->_request->getParam('id'); 
			$errorflag = "true";
			$msgarray = array();
			$newstateArr = array();
			$dbstate = '';
			$dbcountryid ='';
			$statestring = '';			
			$stateArr = $this->_request->getParam('state');
			if(!empty($stateArr))
			{
				
				$otherstatename = $this->_request->getParam('otherstatename');
				if(in_array('other',$stateArr))
				{
					if($otherstatename == '')
					{
						$msgarray['otherstatename'] = "Please enter state name.";
						$msgarray['dupstatename'] = '';
						$msgarray['countid'] = $countryid;
						$errorflag = "false"; 
					}
					else
					{
						$isDuplicateStateNameArr = $statesmodel->getDuplicateStateName($otherstatename,$countryid);
						if($isDuplicateStateNameArr[0]['count'] > 0)
						{
						   $errorflag = "false"; 
							$msgarray['otherstatename'] = "State already exists";
							$msgarray['dupstatename'] = $otherstatename;
							$msgarray['countid'] = $countryid;
						}
						else
						{ 
						    $statestring = implode(",",$stateArr);
                            if(sizeof($stateArr) > 1)
							{
								$newstateArr = $stateArr;
								array_pop($newstateArr);
								$statestring = implode(",",$newstateArr);
								$statestringcomma = trim(str_replace("!@#", ",", $statestring),',');
								$statestringArr = explode(",",$statestringcomma);
								foreach($statestringArr as $key =>$val)
								{
									if (is_numeric($val))
									  $stateid[] = $val;
									else
									  $statename[] = $val;  						
								
								}
							}	
							$dbstate = $otherstatename;
							$errorflag = "true"; 
						}	
					}
				}
				else
				{
				    $statestring = implode(",",$stateArr);
					$statestringcomma = trim(str_replace("!@#", ",", $statestring),',');
					$statestringArr = explode(",",$statestringcomma);
					foreach($statestringArr as $key =>$val)
					{
						if (is_numeric($val))
						  $stateid[] = $val;
						else
						  $statename[] = $val;  						
					
					}
					$errorflag = "true"; 
				}
			}else
			{
			  $msgarray['countid'] = $countryid;
			  $errorflag = "false";
			}
			
			if($statesform->isValid($this->_request->getPost()) && $errorflag == "true" && $statestring!='')
			{
				$actionflag = '';
				$tableid  = ''; 
				if(in_array('other',$stateArr))
				{
					if($otherstatename !='')
					{
					    $NewStateId = $statesmodel->SaveMainStateData($countryid,$otherstatename);
						$NewStateInsertedId = $statesmodel->SaveorUpdateStatesData($countryid,$otherstatename,$NewStateId,$loginUserId);
							if(sizeof($stateArr) > 1)
							{
							    if(!empty($stateid))
								{
							     for($j=0;$j<sizeof($stateid);$j++)
								  {
									 $Id = $statesmodel->SaveorUpdateStatesData($countryid,$statename[$j],$stateid[$j],$loginUserId);
								  }
								} 
							}
						$actionflag = 1;
						$tableid = $NewStateInsertedId;
					}
				}
				else
				{
					for($j=0;$j<sizeof($stateid);$j++)
					{
						$Id = $statesmodel->SaveorUpdateStatesData($countryid,$statename[$j],$stateid[$j],$loginUserId);
					}
				    $tableid = $Id;	
					$actionflag = 1;				
				}			
				$menuID = STATES;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
			   
				if(isset($selectedcountryid))
				{
					$stateData = $statesmodel->fetchAll('isactive = 1 and countryid = '.$selectedcountryid,'state')->toArray();
				}
				else $stateData = array();				
				
				$opt ='';   
				foreach($stateData as $record)
				{
					$opt .= sapp_Global::selectOptionBuilder($record['state_id_org'], $record['state']);
				}
				
				$this->view->stateData = $opt;
				$this->view->eventact = 'added';
				$close = 'close';
				$this->view->popup=$close;
			}
			else
			{
     			$messages = $statesform->getMessages();				
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
					$statesmodeldata = $statesmodel->getStatesList($countryid);
					$statesform->state->addMultiOption('','Select State');
					foreach($statesmodeldata as $res)
					{					
					 $statesform->state->addMultiOption($res['id'].'!@#'.utf8_encode($res['state_name']),utf8_encode($res['state_name']));
					} 
					$statesform->state->addMultiOption('other','Other');
					
					if($statestring == 'other')
						$statesform->setDefault('state','other');					
				}	
			}
		}
		$this->view->ermsg = '';
		$this->view->form = $statesform;
		$this->view->msgarray = $msgarray;
		$this->view->controllername = $controllername;
	}
	
	public function addnewstateAction()
	{
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		}
		$msgarray = array();	$controllername = 'states';		$statestring = '';
		$id = $this->getRequest()->getParam('id');		
		$selectedcountryid = $this->_request->getParam('selectcountryid',null);
		
		
		$statesform = new Default_Form_states();
		$statesmodel = new Default_Model_States();
		$countriesModel = new Default_Model_Countries();
		
		/* Changing the form */		
		$statesform->setAction(BASE_URL.'states/addnewstate/selectcountryid/'.$selectedcountryid);		
		$statesform->removeElement('state');
		$statesform->addElement('text', 'state',array(
					'label'      => 'State',
					'maxlength'	 => '20',
					'required'   => true,
					'validators' => array(
						array('validator' => 'NotEmpty','options'=> array('messages' => 'Please enter state name.')
         ))));
		
		/* END */
		
		$countrieslistArr = $countriesModel->getTotalCountriesList('');
		if(sizeof($countrieslistArr)>0)
		{
			$statesform->countryid->addMultiOption('','Select Country');
			
			foreach ($countrieslistArr as $countrieslistres)
			{				
				if(isset($selectedcountryid))
				{
					if($countrieslistres['id'] == $selectedcountryid)
					{
						$statesform->countryid->addMultiOption($countrieslistres['id'],utf8_encode($countrieslistres['country_name']) );
						$statesform->setDefault('countryid',$selectedcountryid);
					}
				}else{
					$statesform->countryid->addMultiOption($countrieslistres['id'],utf8_encode($countrieslistres['country_name']) );
				}				
			}
		}
		else
		{
			$msgarray['countryid'] = 'Countries are not configured yet.';
		}	
		
		if($this->getRequest()->getPost())
		{	
			$errorflag = "true";
			$msgarray = array();
			
			$id = $this->_request->getParam('id'); 
			
			$dbstate = '';
			$dbcountryid ='';			
			$state = trim($this->_request->getParam('state'));		
			$countryid = $this->_request->getParam('countryid');
			if(isset($countryid) && $countryid != '')
			{
				$isDuplicateStateNameArr = $statesmodel->getDuplicateStateName($state,$countryid);
				if($isDuplicateStateNameArr[0]['count'] > 0)
				{
					$errorflag = "false"; 
					$msgarray['state'] = "State already exists.";				
				}
			}else{
				$errorflag = "false"; 
				$msgarray['countryid'] = "Please select country.";	
			}
			if($statesform->isValid($this->_request->getPost()) && $errorflag == "true")
			{   
				$actionflag = '';
				$tableid  = ''; 
				
				$NewStateId = $statesmodel->SaveMainStateData($countryid,$state);
				$tableid = $NewStateId;	
				$actionflag = 1;				
				$menuID = STATES;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
			   
				if(isset($selectedcountryid))
				{
					$stateData = $statesmodel->getStatesList($selectedcountryid,'');
				}
				else $stateData = array();				
				
				$opt ='';   
				foreach($stateData as $record)
				{
					$opt .= sapp_Global::selectOptionBuilder($record['id'], $record['state_name']);
				}
				
				$this->view->stateData = $opt;
				$this->view->eventact = 'added';
				$close = 'close';
				$this->view->popup=$close;
			}
			else
			{
     			$messages = $statesform->getMessages();				
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
		$this->view->ermsg = '';
		$this->view->form = $statesform;
		$this->view->msgarray = $msgarray;
		$this->view->controllername = $controllername;
	}
}

