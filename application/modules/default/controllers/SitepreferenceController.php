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

class Default_SitepreferenceController extends Zend_Controller_Action
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
        $sitepreferencemodel = new Default_Model_Sitepreference();
        $activerecordArr = $sitepreferencemodel->getActiveRecord();
      
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $sess_values = $auth->getStorage()->read();
        }
        if(!empty($activerecordArr))
           $this->view->dataArray = $activerecordArr;
        
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        $this->view->sess_values = $sess_values;
    }
	
    public function addAction()
    {
	   $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
		$popConfigPermission = array();
		 if(sapp_Global::_checkprivileges(CURRENCY,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
			array_push($popConfigPermission,'currency');
		}  
		if(sapp_Global::_checkprivileges(TIMEZONE,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
			array_push($popConfigPermission,'timezone');
		} 
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
			
		$systempreferenceform = new Default_Form_systempreference();
		$dateformatidmodel = new Default_Model_Dateformat();
		$timeformatidmodel = new Default_Model_Timeformat();
		$currencyidmodel = new Default_Model_Currency();
		$systempreferencemodel = new Default_Model_Sitepreference();
                $activerecordArr = $systempreferencemodel->getActiveRecord();
		$msgarray = array();
		$timezonemodel = new Default_Model_Timezone();
				
                $allTimezoneData = $timezonemodel->fetchAll('isactive=1','timezone')->toArray();
		 $date_formats_arr = $dateformatidmodel->getAllDateFormats();
            $time_formats_arr = $timeformatidmodel->fetchAll()->toArray();
        
            if(sizeof($date_formats_arr) > 0)
            {  		
		        $this->view->date_formats_arr = $date_formats_arr;	
		    }else
			{
				$msgarray['dateformatid'] = 'Date Formats are not configured yet';
				
			}
        
            if(sizeof($time_formats_arr) > 0)
            {		
		       
		$this->view->time_formats_arr = $time_formats_arr;
            }else
            {
				$msgarray['timeformatid'] = 'Time Formats are not configured yet';
				
            }
			
		$currencyidmodeldata = $currencyidmodel->getCurrencyList();
                $systempreferenceform->currencyid->addMultiOption('','Select Currency'); 			
		    if(sizeof($currencyidmodeldata) > 0)
            {
                
				foreach ($currencyidmodeldata as $currencyidres){
					$systempreferenceform->currencyid->addMultiOption($currencyidres['id'],utf8_encode($currencyidres['currency']));
				}
			}else{
				$msgarray['currencyid'] = 'Currency is not configured yet';
            }
 			
		$passworddataArr = $systempreferencemodel->getPasswordData();
			   $systempreferenceform->passwordid->addMultiOption('','Select Password Preference');	 
			   foreach($passworddataArr as $passwordres)
			   {
				 $systempreferenceform->passwordid->addMultiOption($passwordres['id'],utf8_encode($passwordres['passwordtype']));
			   }
	    if(sizeof($allTimezoneData) > 0)
            {                
                foreach ($allTimezoneData as $timezoneidres)
                {
                    $systempreferenceform->timezoneid->addMultiOption($timezoneidres['id'],utf8_encode($timezoneidres['timezone'].' ['.$timezoneidres['timezone_abbr'].']'));
                } 
            }
            else
            {		
                $msgarray['timezoneid'] = 'Time Zone is not configured yet.';
            }		
		$systempreferenceform->setAttrib('action',BASE_URL.'sitepreference/add');
        $this->view->form = $systempreferenceform; 	
		$this->view->passworddata = $passworddataArr; 
        $this->view->msgarray = $msgarray;
        $this->view->popConfigPermission = $popConfigPermission;
        if(count($activerecordArr)>0)
        {
            $this->view->nodata = "nodata";
        }
        if($this->getRequest()->getPost()){
		     $result = $this->save($systempreferenceform);	
		     $this->view->msgarray = $result; 
        }  		
		
	}

    public function viewAction()
	{	
		
		$id = (int)$this->getRequest()->getParam('id');
                $id = abs($id);
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		$objName = 'sitepreference';
		$systempreferenceform = new Default_Form_systempreference();
		$dateformatidmodel = new Default_Model_Dateformat();
		$timeformatidmodel = new Default_Model_Timeformat();
		$currencyidmodel = new Default_Model_Currency();
		$systempreferencemodel = new Default_Model_Sitepreference();
		$systempreferenceform->removeElement("submit");
		$data = $systempreferencemodel->getsingleSystemPreferanceData($id);
				
            $dateformatidmodeldata = $dateformatidmodel->getsingleDateformatData($data['dateformatid']); 
            if(sizeof($dateformatidmodeldata) > 0)
            {  			
					 $systempreferenceform->dateformatid->addMultiOption($dateformatidmodeldata['id'],utf8_encode($dateformatidmodeldata['dateformat']));
		    }
            $timeformatidmodeldata = $timeformatidmodel->getsingleTimeformatData($data['timeformatid']);	
            if(sizeof($timeformatidmodeldata) > 0)
            {		
		       	$systempreferenceform->timeformatid->addMultiOption($timeformatidmodeldata['id'],utf8_encode($timeformatidmodeldata['timeformat']));
            }
			
		    $currencyidmodeldata = $currencyidmodel->getsingleCurrencyData($data['currencyid']);
		    if(sizeof($currencyidmodeldata) > 0)
            {
            	$systempreferenceform->currencyid->addMultiOption($currencyidmodeldata['id'],utf8_encode($currencyidmodeldata['currencyname'])." ".$currencyidmodeldata['currencycode']);
			}
 			
		     $passworddataArr = $systempreferencemodel->getSinglePasswordData($data['passwordid']);
			if(sizeof($passworddataArr) > 0)
            { 
			   	$systempreferenceform->passwordid->addMultiOption($passworddataArr[0]['id'],utf8_encode($passworddataArr[0]['passwordtype']));
			}  
			
			$elements = $systempreferenceform->getElements();
			if(count($elements)>0)
			{
				foreach($elements as $key=>$element)
				{
					if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
					$element->setAttrib("disabled", "disabled");
						}
				}
			}
		
		$systempreferenceform->populate($data);
		$systempreferenceform->setDefault('dateformatid',$data['dateformatid']);
		$systempreferenceform->setDefault('timeformatid',$data['timeformatid']);
		$systempreferenceform->setDefault('currencyid',$data['currencyid']);
		$systempreferenceform->setDefault('passwordid',$data['passwordid']);
		$this->view->controllername = $objName;
		$this->view->id = $id;
		$this->view->data = $data;
		
		$this->view->form = $systempreferenceform;
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
        $msgarray = array();
        $id = $this->getRequest()->getParam('id');      
        $callval = $this->getRequest()->getParam('call');
        if($callval == 'ajaxcall')
            $this->_helper->layout->disableLayout();
		
        $systempreferenceform = new Default_Form_systempreference();	
        $dateformatidmodel = new Default_Model_Dateformat();
        $timeformatidmodel = new Default_Model_Timeformat();	
        $currencyidmodel = new Default_Model_Currency();
        $systempreferencemodel = new Default_Model_Sitepreference(); 
        $date_formats_arr = array();
        $time_formats_arr = array();
        $passworddataArr = array();
        $timezonemodel = new Default_Model_Timezone();
				
        $allTimezoneData = $timezonemodel->fetchAll('isactive=1','timezone')->toArray();
        if(sapp_Global::_checkprivileges(CURRENCY,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
			array_push($popConfigPermission,'currency');
		}  
		if(sapp_Global::_checkprivileges(TIMEZONE,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
			array_push($popConfigPermission,'timezone');
		} 
        if($id && $id>0 && is_numeric($id))
        {
            try
            {
                $data = $systempreferencemodel->getsingleSystemPreferanceData($id);			
                $date_formats_arr = $dateformatidmodel->getAllDateFormats();
                $time_formats_arr = $timeformatidmodel->fetchAll()->toArray();           		
                $currencyidmodeldata = $currencyidmodel->getCurrencyList();
                $systempreferenceform->currencyid->addMultiOption('','Select Currency');
                if(sizeof($currencyidmodeldata) > 0)
                {            
                    foreach ($currencyidmodeldata as $currencyidres)
                    {
                        $systempreferenceform->currencyid->addMultiOption($currencyidres['id'],utf8_encode($currencyidres['currencytext']));
                    }
                }
                $systempreferenceform->passwordid->addMultiOption('','Select Password Preference');	 
                $passworddataArr = $systempreferencemodel->getPasswordData();	
                foreach($passworddataArr as $passwordres)
                {
                    $systempreferenceform->passwordid->addMultiOption($passwordres['id'],utf8_encode($passwordres['passwordtype']));
                }
                if(sizeof($allTimezoneData) > 0)
                {                
                    foreach ($allTimezoneData as $timezoneidres)
                    {
                        $systempreferenceform->timezoneid->addMultiOption($timezoneidres['id'],utf8_encode($timezoneidres['timezone'].' ['.$timezoneidres['timezone_abbr'].']'));
                    } 
                }
                else
                {		
                    $msgarray['timezoneid'] = 'Time Zone is not configured yet.';
                }
                $systempreferenceform->populate($data);	
                $systempreferenceform->setDefault('dateformatid',$data['dateformatid']);
                $systempreferenceform->setDefault('timeformatid',$data['timeformatid']);	
                $systempreferenceform->setDefault('currencyid',$data['currencyid']);
                $systempreferenceform->setDefault('passwordid',$data['passwordid']);
                $systempreferenceform->setAttrib('action',BASE_URL.'sitepreference/edit/id/'.$id);
                $this->view->msgarray = $msgarray;
                
            }
            catch(Exception $e)
            {
                $this->view->nodata = "nodata";
            }
        }
        else
        {
            $this->view->nodata = "nodata";
        }
        $this->view->form = $systempreferenceform;
        $this->view->date_formats_arr = $date_formats_arr;
        $this->view->time_formats_arr = $time_formats_arr;
        $this->view->passworddata = $passworddataArr;
        if($this->getRequest()->getPost())
        {
            $result = $this->save($systempreferenceform);	
            $this->view->msgarray = $result; 
        }
		$this->view->popConfigPermission = $popConfigPermission;
    }
	
    public function formatOffset($offset) {
            $hours = $offset / 3600;
            $remainder = $offset % 3600;
            $sign = $hours > 0 ? '+' : '-';
            $hour = (int) abs($hours);
            $minutes = (int) abs($remainder / 60);

            if ($hour == 0 AND $minutes == 0) {
                $sign = ' ';
            }
            return $sign . str_pad($hour, 2, '0', STR_PAD_LEFT) .':'. str_pad($minutes,2, '0');

    } 
    public function save($systempreferenceform)
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
        } 
        if($systempreferenceform->isValid($this->_request->getPost()))
        {
            $trDb = Zend_Db_Table::getDefaultAdapter();		
            // starting transaction
            $trDb->beginTransaction();
            try
            {
                $systempreferencemodel = new Default_Model_Sitepreference(); 
                $id = (int)$this->_request->getParam('id'); 
                $id = abs($id);
                $dateformatid = $this->_request->getParam('dateformatid');
                $timeformatid = $this->_request->getParam('timeformatid');
                $timezoneid = $this->_request->getParam('timezoneid');
                $currencyid = $this->_request->getParam('currencyid');
                $passwordid = $this->_request->getParam('passwordid');
                $description = $this->_request->getParam('description');
                $date = new Zend_Date();
                $actionflag = '';
                $tableid  = ''; 
                $data = array( 
                                'dateformatid'=>$dateformatid,
                                'timeformatid'=>$timeformatid,
                                'timezoneid'=>$timezoneid,
                                'currencyid'=>$currencyid,
                                'passwordid'=>$passwordid, 								 
                                'description'=>$description,
                                'modifiedby'=>$loginUserId,
                                'modifieddate'=>$date->get('yyyy-MM-dd HH:mm:ss')
                            );
                if($id!='')
                {
                    
                    $data['createdby'] = $loginUserId;
                    $data['createddate'] = $date->get('yyyy-MM-dd HH:mm:ss');
                    $data['isactive'] = 1;
                    $where ="id=$id";
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
                $update_arr = array(
                                    'isactive' => 0,
                                    'modifieddate'=>$date->get('yyyy-MM-dd HH:mm:ss'),
                                    'modifiedby'=>$loginUserId,                                                
                                );
                 
                $systempreferencemodel->SaveorUpdateSystemPreferanceData($update_arr, array('isactive'=>1)); 
                
                $Id = $systempreferencemodel->SaveorUpdateSystemPreferanceData($data, $where);
                
                if($id!='')
                {
                   $tableid = $id;
                   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Site preferences updated successfully."));
                }   
                else
                {
                    $tableid = $Id; 	
                    $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Site preferences added successfully."));					   
                }   
				$menuID = SITEPREFERENCE;
                
                $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
                sapp_Global::generateSiteConstants();
                $trDb->commit();
            }
            catch (Exception $e) 
            {
                $trDb->rollBack();
                $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Something went wrong,please try again later."));					   
            }
            $this->_redirect('sitepreference');		
        }
        else
        {
            $messages = $systempreferenceform->getMessages();
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
	
	public function deleteAction()
	{
	     $auth = Zend_Auth::getInstance();
     		if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
				}
		 $id = $this->_request->getParam('objid');
		 $messages['message'] = '';
		 $messages['msgtype'] = '';
		 $actionflag = 3;
		    if($id)
			{
			 $systempreferencemodel = new Default_Model_Sitepreference();
			  $data = array('isactive'=>0);
			  $where = array('id=?'=>$id);
			  $Id = $systempreferencemodel->SaveorUpdateSystemPreferanceData($data, $where);
			    if($Id == 'update')
				{
				   $menuID = SITEPREFERENCE;
				   $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id); 
				   $messages['message'] = 'Site preferences deleted successfully.';
				   $messages['msgtype'] = 'success';
				}   
				else
                   $messages['message'] = 'Site preferences cannot be deleted.';	
                   $messages['msgtype'] = 'error'; 				   
			}
			else
			{ 
			 $messages['message'] = 'Site preferences cannot be deleted.';
			 $messages['msgtype'] = 'error';
			}
			$this->_helper->json($messages);
		
	}
	
	

}

