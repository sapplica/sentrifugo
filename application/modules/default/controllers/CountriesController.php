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

class Default_CountriesController extends Zend_Controller_Action
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
		$countriesmodel = new Default_Model_Countries();	
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
			$sort = 'DESC';$by = 'modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else 
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'modifieddate';
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

		$dataTmp = $countriesmodel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);		 		
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
		$objName = 'countries';
		$countriesform = new Default_Form_countries();
		$countriesform->removeElement("submit");
		$elements = $countriesform->getElements();
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)
			{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
				$element->setAttrib("disabled", "disabled");
					}
        	}
        }
		$countriesmodel = new Default_Model_Countries();
		try
		{
		    if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					$data = $countriesmodel->getCountriesDataByID($id);
					if(!empty($data))
					{
						$countriesform->populate($data[0]);
						$countriesform->setDefault('country',$data[0]['country_id_org']);
						$this->view->controllername = $objName;
						$this->view->id = $id;
						$this->view->data = $data[0];
						$this->view->form = $countriesform;
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
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		$countriesform = new Default_Form_countries();
		$countriesmodel = new Default_Model_Countries();
		try
		{
			if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					$data = $countriesmodel->getCountriesDataByID($id);
					if(!empty($data))
					{
					   $countriesform->country->addMultiOption($data[0]['country_id_org'],$data[0]['country']);
						$countriesform->populate($data[0]);
						$countriesform->submit->setLabel('Update');
						$countriesform->setDefault('country',$data[0]['country_id_org']);
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
		$this->view->form = $countriesform;
		if($this->getRequest()->getPost()){
		        $id = $this->_request->getParam('id'); 
				$errorflag = "true";
				$msgarray = array();
				$dbcountry = '';
				$dbcountryid ='';
			    $country = $this->_request->getParam('country');
				$othercountry = $this->_request->getParam('othercountry');
				$countrycode = $this->_request->getParam('countrycode');
				$citizenship = $this->_request->getParam('citizenship');
				if($country == 'other')
				{
				   if($othercountry == '')
				   {
				     $msgarray['othercountry'] = "Please enter other country";
				     $errorflag = "false"; 
				   }
				   else
				   {
						$isduplicatecountrynameArr = $countriesmodel->getDuplicateCountryName($othercountry); 
						if($isduplicatecountrynameArr[0]['count'] > 0)
						{
						  $errorflag = "false";
						  $msgarray['othercountry'] = "Country already exists.";
						  $msgarray['othercountryname'] = $othercountry;
						}
						else if ( !preg_match('/^[^ ][a-z0-9 ]*$/i', $othercountry) )
						{
						  $errorflag = "false";
						  $msgarray['othercountry'] = "Please enter valid country name.";
						  $msgarray['othercountryname'] = $othercountry;
						}
						else
						{ 					
							$dbcountry = $othercountry;
							$errorflag = "true";
						}	
				   }
				}
				else
				{
				    $countrynamearr = $countriesmodel->getCountryCode($country);
				    if(!empty($countrynamearr))
					{
						$dbcountry = $countrynamearr[0]['country_name'];
						$dbcountryid = $countrynamearr[0]['id'];
						$errorflag = "true";
					}
					else
					{
						$msgarray['country'] = $dbcountry." already added";
						 $errorflag = "false";
					
					}
				}
			
		    if($countriesform->isValid($this->_request->getPost()) && $errorflag == "true"){
			    
				$date = new Zend_Date();
				$actionflag = '';
				$tableid  = ''; 
				  if($country == 'other')
					{
					  if($othercountry !='' && $countrycode!='')
					    $countryID = $countriesmodel->SaveMainCountryData($othercountry,$countrycode);
					}
				   $data = array('country'=>trim($dbcountry),
				           'countrycode'=>trim($countrycode),
						  'citizenship'=>NULL,
						  'modifiedby'=>$loginUserId,
						  'modifieddate'=>gmdate("Y-m-d H:i:s"),
						  'country_id_org'=>($dbcountryid !='')?$dbcountryid:$countryID,
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
					$Id = $countriesmodel->SaveorUpdateCountryData($data, $where);
					
						if($Id == 'update')
						{
						   $tableid = $id;
						   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Country updated successfully."));
						}   
						else
						{
						   $tableid = $Id; 	
							$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Country  added successfully."));					   
						}   
					$menuID = COUNTRIES;
					$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
    			    $this->_redirect('countries');		
			}else
			{
     			$messages = $countriesform->getMessages();
				foreach ($messages as $key => $val)
					{
						foreach($val as $key2 => $val2)
						 {
							  $msgarray[$key] = $val2;
							  break;
							
						 }
					}
				$this->view->msgarray = $msgarray;
			
			}
		}
	}
	
	public function saveupdateAction()
	{
	     $auth = Zend_Auth::getInstance();
     		if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
				}
	    $id = $this->_request->getParam('id');
		$gendercode = $this->_request->getParam('gendercode');
		$gendername = $this->_request->getParam('gendername');
		$description = $this->_request->getParam('description');
		$genderform = new Default_Form_gender();
		$gendermodel = new Default_Model_Gender();
		$messages = $genderform->getMessages();
		$actionflag = '';
		$tableid  = '';
		
		if($this->getRequest()->getPost()){
		    if($genderform->isValid($this->_request->getPost())){
				   $data = array('gendercode'=>trim($gendercode),
				           'gendername'=>trim($gendername),
						  'description'=>trim($description),
						  'modifiedby'=>$loginUserId,
						  'modifieddate'=>Zend_Registry::get('currentdate')
						);
					if($id!=''){
						$where = array('id=?'=>$id);  
						$messages['message']='Gender updated successfully';
						$actionflag = 2;
						
					}
					else
					{
					    $data['createdby'] = $loginUserId;
						$data['createddate'] = Zend_Registry::get('currentdate');
						$data['isactive'] = 1;
						$where = '';
						$messages['message']='Gender  added successfully';
						$actionflag = 1;
					}
					$Id = $gendermodel->SaveorUpdateGenderData($data, $where);
					if($Id == 'update')
					   $tableid = $id;
					else
                       $tableid = $Id; 					
					$menuID = GENDER;
					$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
    			    $messages['result']='saved';
					$this->_helper->json($messages);
			
			}else
			{
			    $messages = $genderform->getMessages();
				$messages['result']='error';
    			$this->_helper->json($messages);
			
			}
		}
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
		 $messages['msgtype'] = '';
		 $actionflag = 3;
		 $countryOrgId = '';
		    if($id)
			{
			 $countriesmodel = new Default_Model_Countries();
			 $statesmodel = new Default_Model_States();
			 $citiesmodel = new Default_Model_Cities();
			  $countrydata = $countriesmodel->getCountriesDataByID($id);
			  if(!empty($countrydata))			  
			    $countryOrgId = $countrydata[0]['country_id_org'];
			  $data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
			  $where = array('id=?'=>$id);
			  $Id = $countriesmodel->SaveorUpdateCountryData($data, $where);
			    if($countryOrgId !='')
			    {
				    $statesandcitydata = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
					$statesandcitywhere = array('countryid=?'=>$countryOrgId);
					$StateId = $statesmodel->deleteStateData($statesandcitydata, $statesandcitywhere);
					$CityId = $citiesmodel->deleteCityData($statesandcitydata, $statesandcitywhere);
				}
			    if($Id == 'update')
				{
				   $menuID = COUNTRIES;
				   $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
                   $configmail = sapp_Global::send_configuration_mail('Country',$countrydata[0]['country']); 				   
				   $messages['message'] = 'Country deleted successfully.';
				   $messages['msgtype'] = 'success';
				}   
				else
				{
                   $messages['message'] = 'Country cannot be deleted.';
				   $messages['msgtype'] = 'error';
                } 				   
			}
			else
			{ 
			 $messages['message'] = 'Country cannot be deleted.';
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
	
	public function getcountrycodeAction()
	{
	   $coutryid = $this->_request->getParam('coutryid');
	   $messages['message'] = '';
	   $countriesmodel = new Default_Model_Countries();
	   $countrycode = $countriesmodel->getCountryCode($coutryid);
	

	   if(!empty($countrycode))
	   {
	     $this->_helper->json($countrycode);
	   }
	}	
	
	public function addpopupAction()
	{
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		$countriesform = new Default_Form_countries();
		$countriesmodel = new Default_Model_Countries();
		$countriesform->setAction(BASE_URL.'countries/addpopup');		
		$controllername = 'countries';
		if($this->getRequest()->getPost())
		{
			$id = $this->_request->getParam('id'); 
			$errorflag = "true";
			$msgarray = array();
			$dbcountry = '';
			$dbcountryid ='';
			$country = $this->_request->getParam('country');
			$othercountry = $this->_request->getParam('othercountry');
			$countrycode = $this->_request->getParam('countrycode');
			$citizenship = $this->_request->getParam('citizenship');
			if($country == 'other')
			{
			   if($othercountry == '')
			   {
				 $msgarray['othercountry'] = "Please enter other country";
				 $errorflag = "false"; 
			   }
			   else
			   {
					$isduplicatecountrynameArr = $countriesmodel->getDuplicateCountryName($othercountry); 
					if($isduplicatecountrynameArr[0]['count'] > 0)
					{
					  $errorflag = "false";
					  $msgarray['othercountry'] = "Country already exists";
					  $msgarray['othercountryname'] = $othercountry;
					}
					else if ( !preg_match('/^[^ ][a-z0-9 ]*$/i', $othercountry) )
					{
					  $errorflag = "false";
					  $msgarray['othercountry'] = "Please enter valid country name.";
					  $msgarray['othercountryname'] = $othercountry;
					}
					else
					{ 					
						$dbcountry = $othercountry;
						$errorflag = "true";
					}	
			   }
			}
			else
			{
				$countrynamearr = $countriesmodel->getCountryCode($country);
				if(!empty($countrynamearr))
				{
					$dbcountry = $countrynamearr[0]['country_name'];
					$dbcountryid = $countrynamearr[0]['id'];
					$errorflag = "true";					
				}
				else
				{
					$msgarray['country'] = $dbcountry." already added";
					 $errorflag = "false";
				
				}
			}
			
		    if($countriesform->isValid($this->_request->getPost()) && $errorflag == "true")
			{
				$date = new Zend_Date();
				$actionflag = '';
				$tableid  = ''; 
				if($country == 'other')
				{
					if($othercountry !='' && $countrycode!='')
					    $countryID = $countriesmodel->SaveMainCountryData($othercountry,$countrycode);
				}
				$data = array('country'=>trim($dbcountry),
								'countrycode'=>trim($countrycode),
								'citizenship'=>NULL,
								'modifiedby'=>$loginUserId,								
								'modifieddate'=>gmdate("Y-m-d H:i:s"),
								'country_id_org'=>($dbcountryid !='')?$dbcountryid:$countryID,
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
				$Id = $countriesmodel->SaveorUpdateCountryData($data, $where);
				$tableid = $Id; 	
				$menuID = COUNTRIES;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
				$countryData = $countriesmodel->fetchAll('isactive = 1','country')->toArray();
				$opt ='';   
				foreach($countryData as $record)
				{
					$opt .= sapp_Global::selectOptionBuilder($record['country_id_org'], $record['country']);
				}
				$this->view->countryData = $opt;
				$this->view->eventact = 'added';
				$close = 'close';
				$this->view->popup=$close;
			}
			else
			{
     			$messages = $countriesform->getMessages();				
				foreach ($messages as $key => $val)
				{
					foreach($val as $key2 => $val2)
					{
						$msgarray[$key] = $val2;
						break;						
					}
				}
				$this->view->msgarray = $msgarray;			
			}
		}
		$this->view->ermsg = '';
		$this->view->form = $countriesform;
		$this->view->controllername = $controllername;
	}
	
	public function addnewcountryAction()
	{
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		$controllername = 'countries';
		
		$countriesform = new Default_Form_countries();
		$countriesmodel = new Default_Model_Countries();
		$countriesform->setAction(BASE_URL.'countries/addnewcountry');		
		
		/* Changing the form */
		$countriesform->removeElement('country');
		$countriesform->removeElement('countrycode');
		$countriesform->addElement('text', 'country',array(
					'label'      => 'Country',
					'required'   => true,
					'validators' => array(
						array('validator' => 'NotEmpty','options'=> array('messages' => 'Please enter country name.')
         ))));
		$countriesform->country->addValidator(new Zend_Validate_Db_NoRecordExists(
	                                            array(  'table'=>'tbl_countries',
	                                                     'field'=>'country_name'
						) ) );
		$countriesform->country->getValidator('Db_NoRecordExists')->setMessage('Country name already exists.');				
		$countriesform->addElement('text', 'countrycode',array(
					'label'      => 'Country code',
					'required'   => true,
					'validators' => array(
						array('validator' => 'NotEmpty', 'options'=> array('messages' => 'Please enter country code.')))));
		$countriesform->countrycode->addValidator(new Zend_Validate_Db_NoRecordExists(
	                                            array(  'table'=>'tbl_countries',
	                                                     'field'=>'country_code'
						) ) );
		$countriesform->countrycode->getValidator('Db_NoRecordExists')->setMessage('Country code already exists.');
		
		/* END */
		
		if($this->getRequest()->getPost())
		{
			$id = $this->_request->getParam('id'); 
			$errorflag = "true";
			$msgarray = array();
			$dbcountry = '';
			$dbcountryid ='';
			$country = $this->_request->getParam('country');			
			$countrycode = $this->_request->getParam('countrycode');
			$citizenship = $this->_request->getParam('citizenship');
			$dbcountry = $country;
			
		    if($countriesform->isValid($this->_request->getPost()) && $errorflag == "true")
			{
				$date = new Zend_Date();
				$actionflag = 1;
				$tableid  = ''; 
				$countryID = $countriesmodel->SaveMainCountryData($country,$countrycode);
				$tableid = $countryID; 	
                $menuID = COUNTRIES;				
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
				$countryData = $countriesmodel->getTotalCountriesList('isactive=1','country_name');
				$opt ='';   
				foreach($countryData as $record)
				{
					$opt .= sapp_Global::selectOptionBuilder($record['id'], $record['country_name']);
				}
				
				$this->view->countryData = $opt;
				$this->view->eventact = 'added';
				$close = 'close';
				$this->view->popup=$close;
				
			}
			else
			{
     			$messages = $countriesform->getMessages();				
				foreach ($messages as $key => $val)
				{
					foreach($val as $key2 => $val2)
					{
						$msgarray[$key] = $val2;
						break;						
					}
				}
				$this->view->msgarray = $msgarray;			
			}
		}
		$this->view->ermsg = '';
		$this->view->form = $countriesform;
		$this->view->controllername = $controllername;
	}
}

