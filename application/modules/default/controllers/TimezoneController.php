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

class Default_TimezoneController extends Zend_Controller_Action
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
		$timezone = new Default_Model_Timezone();	
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
				
		$dataTmp = $timezone->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);     			
					
					
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
		$objName = 'timezone';
		$timezoneform = new Default_Form_timezone();
		$timezoneform->removeElement("submit");
		$elements = $timezoneform->getElements();
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)
			{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
				$element->setAttrib("disabled", "disabled");
					}
        	}
        }
		$timezonemodel = new Default_Model_Timezone();
        try
        { 		
			if($id)
			{
				 if(is_numeric($id) && $id>0)
				 {  			
					 $data = $timezonemodel->getTimeZoneDataByID($id);
					  if(!empty($data))
					   {
						 $timezoneform->populate($data[0]);
						$timezoneVal = $data[0]['timezone'].' ['.$data[0]['timezone_abbr'].']';
					   if(!empty($timezoneVal)){
						$data['timezoneVal'] = $timezoneVal;
					      }
						 $this->view->timezoneVal = $timezoneVal;
						 $this->view->controllername = $objName;
						 $this->view->id = $id;
						 $this->view->data = $data;
						 $this->view->form = $timezoneform;
						 $this->view->ermsg = '';
					   }
					   else
						{
						$this->view->ermsg = 'no record';
						} 
				}else
				{
				$this->view->ermsg = 'no record';
				}	
			}else
			{
			$this->view->ermsg = 'no record';
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
		
		$timezoneform = new Default_Form_timezone();
		$timezonemodel = new Default_Model_Timezone();
        try
        { 		
			if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					$data = $timezonemodel->getTimeZoneDataByID($id);
					if(!empty($data))
					{
					  $timezoneform->populate($data[0]);
					  $timezoneform->removeElement('timezone');
					  $timezoneVal = $data[0]['timezone'].' ['.$data[0]['timezone_abbr'].']';
					  $timezoneform->submit->setLabel('Update');
					  $this->view->timezoneVal = $timezoneVal;
					  $this->view->data = $data;
					  $this->view->id = $id;
					  $this->view->ermsg = '';
					}
					else
					{
					$this->view->ermsg = 'nodata';
					} 
                }
                else
				{
				$this->view->ermsg = 'nodata';
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
		$this->view->form = $timezoneform;
		if($this->getRequest()->getPost()){
		    if($timezoneform->isValid($this->_request->getPost()))
			{
			    $id = $this->_request->getParam('id');
				$timezones = $this->_request->getParam('timezone');
				$description = $this->_request->getParam('description');
				$date = new Zend_Date();
				$actionflag = '';
				$tableid  = '';
				if(is_array($timezones))
				$timezonesStr = implode(",",$timezones);
				else
				$timezonesStr ='';
				
			   $data = array(
					  'description'=>trim($description),
					  'modifiedby'=>$loginUserId,
					  'modifieddate'=>gmdate("Y-m-d H:i:s")
					);
				$where = array('id=?'=>$id);
				if($id!=''){
					$Id = $timezonemodel->SaveorUpdateTimeZoneData($data, $where);
					$actionflag = 2;
				}
				else
				{	
					$count = count($timezones);
					$Id = $timezonemodel->savetimezonedetails($timezonesStr,$description,$loginUserId);
					$where = '';
					$actionflag = 1;					
				}					
				if($Id == 'update')
				{
				   $tableid = $id;
				   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Time zone updated successfully."));
				}   
				else
				{
				   $tableid = $Id;
				   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Time zone added successfully."));
				}   
				$menuID = TIMEZONE;
				if($id!='')
				{
					$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
				}else{
					for($i=0; $i<$count; $i++)
					{
						$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
						$tableid = $tableid + 1;
					}
				}
				$this->_redirect('timezone');
			
			}else
			{
			    $messages = $timezoneform->getMessages();
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
		$dateformat = $this->_request->getParam('timezone');
		$description = $this->_request->getParam('description');
		$timezoneform = new Default_Form_timezone();
		$timezonemodel = new Default_Model_Timezone();
		$messages = $timezoneform->getMessages();
		$actionflag = '';
		$tableid  = '';
		
		if($this->getRequest()->getPost()){
		    if($timezoneform->isValid($this->_request->getPost())){
				   $data = array('timezone'=>trim($dateformat),
						  'description'=>trim($description),
						  'modifiedby'=>$loginUserId,
						  'modifieddate'=>Zend_Registry::get('currentdate')
						);
					if($id!=''){
						$where = array('id=?'=>$id);  
						$messages['message']='Time zone updated successfully.';
						$actionflag = 2;
						
					}
					else
					{
					    $data['createdby'] = $loginUserId;
						$data['createddate'] = Zend_Registry::get('currentdate');
						$data['isactive'] = 1;
						$where = '';
						$messages['message']='Time zone added successfully.';
						$actionflag = 1;
					}
					$Id = $timezonemodel->SaveorUpdateTimeZoneData($data, $where);
					if($Id == 'update')
					   $tableid = $id;
					else
                       $tableid = $Id; 					
					$menuID = TIMEZONE;
					$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
    			    $messages['result']='saved';
					$this->_helper->json($messages);
			
			}else
			{
			    $messages = $timezoneform->getMessages();
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
		 $messages['flagtype'] = '';
		 $timezonename = '';
		 $actionflag = 3;
		    if($id)
			{
			 $timezonemodel = new Default_Model_Timezone();	 
			 $timezonedata = $timezonemodel->getTimeZoneDataByID($id);
			 if(!empty($timezonedata))
			  $timezonename = $timezonedata[0]['timezone'];
			 
			  $data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
			  $where = array('id=?'=>$id);
			  $Id = $timezonemodel->SaveorUpdateTimeZoneData($data, $where);
			    if($Id == 'update')
				{
				   $menuID = TIMEZONE;
				   $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id); 
                   $configmail = sapp_Global::send_configuration_mail('Time Zone',$timezonename);				   
				   $messages['message'] = 'Time zone deleted successfully.';
				   $messages['msgtype'] = 'success';
				}   
				else
				{
                   $messages['message'] = 'Time zone cannot be deleted.';
                   $messages['msgtype'] = 'error';			   
				}   
			}
			else
			{ 
			 $messages['message'] = 'Time zone cannot be deleted.';
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
	
	public function addpopupAction()
	{
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
				$loginUserId = $auth->getStorage()->read()->id;
			}		
		$id = $this->getRequest()->getParam('id');
		$controllername = 'timezone';
		$timezoneform = new Default_Form_timezone();
		$timezonemodel = new Default_Model_Timezone();
		$timezoneform->setAction(BASE_URL.'timezone/addpopup');		
		
		if($this->getRequest()->getPost()){
		    if($timezoneform->isValid($this->_request->getPost()))
			{
			    $id = $this->_request->getParam('id');
				$timezones = $this->_request->getParam('timezone');
				$description = $this->_request->getParam('description');
				$date = new Zend_Date();
				$actionflag = '';
				$tableid  = '';
				if(is_array($timezones))
				$timezonesStr = implode(",",$timezones);
				else
				$timezonesStr ='';
				
			   
				$count = count($timezones);
				$Id = $timezonemodel->savetimezonedetails($timezonesStr,$description,$loginUserId);
				$where = '';
				$actionflag = 1;					
				$tableid = $Id;
				$menuID = TIMEZONE;
				
				for($i=0; $i<$count; $i++)
				{
					$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
					$tableid = $tableid + 1;
				}
				
				
				$timezonesData = $timezonemodel->fetchAll('isactive = 1','timezone')->toArray();
				$opt ='';   
				foreach($timezonesData as $record)
				{
					$opt .= sapp_Global::selectOptionBuilder($record['id'], $record['timezone'].' ['.$record['timezone_abbr'].']');
				}
					
				$this->view->timezonesData = $opt;
				$this->view->eventact = 'added';
				$close = 'close';
				$this->view->popup=$close;
			}else
			{
			    $messages = $timezoneform->getMessages();
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
		$this->view->controllername = $controllername;
		$this->view->form = $timezoneform;
		$this->view->ermsg = '';
	}

}

