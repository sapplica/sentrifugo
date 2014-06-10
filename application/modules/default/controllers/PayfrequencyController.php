<?php
/********************************************************************************* 
 *  This file is part of Sentrifugo.
 *  Copyright (C) 2014 Sapplica
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

class Default_PayfrequencyController extends Zend_Controller_Action
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
		$payfrequencymodel = new Default_Model_Payfrequency();	
        $call = $this->_getParam('call');
		if($call == 'ajaxcall')
				$this->_helper->layout->disableLayout();
		
		$view = Zend_Layout::getMvcInstance()->getView();		
		$objname = $this->_getParam('objname');
		$refresh = $this->_getParam('refresh');
		$dashboardcall = $this->_getParam('dashboardcall',null);
		$data = array();$searchQuery = '';	$searchArray = array();	$tablecontent='';
		
		if($refresh == 'refresh')
		{
			if($dashboardcall == 'Yes')
				$perPage = DASHBOARD_PERPAGE;
			else	
				$perPage = PERPAGE;
										
			$sort = 'DESC';$by = 'modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';	
			$searchArray = array();
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
			$searchData = $this->_getParam('searchData');	
			$searchData = rtrim($searchData,',');
		}
		$dataTmp = $payfrequencymodel->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall);	
		
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
    }
    
   public function addpopupAction()
	{
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->getRequest()->getParam('id');

		$msgarray = array();
		$controllername = 'payfrequency';
		$payfrequencyform = new Default_Form_payfrequency();
		$payfrequencymodel = new Default_Model_Payfrequency();
		$payfrequencyform->setAction(DOMAIN.'payfrequency/addpopup');
		if($this->getRequest()->getPost()){
			if($payfrequencyform->isValid($this->_request->getPost())){
				$id = $this->_request->getParam('id'); 
			    $freqtype = $this->_request->getParam('freqtype');
				$freqdescription = $this->_request->getParam('freqdescription');
				$date = new Zend_Date();
				$menumodel = new Default_Model_Menu();
				$actionflag = '';
				$tableid  = ''; 
				   $data = array('freqtype'=>trim($freqtype),
				           'freqdescription'=>trim($freqdescription),
							'modifiedby'=>$loginUserId,
						 // 'modifieddate'=>$date->get('yyyy-MM-dd HH:mm:ss')
						   'modifieddate'=>gmdate("Y-m-d H:i:s")
						);
					if($id!=''){
						$where = array('id=?'=>$id);  
						$actionflag = 2;
					}
					else
					{
					    $data['createdby'] = $loginUserId;
						//$data['createddate'] = $date->get('yyyy-MM-dd HH:mm:ss');
						$data['createddate'] = gmdate("Y-m-d H:i:s");
						$data['isactive'] = 1;
						$where = '';
						$actionflag = 1;
					}
					//echo "<pre>";print_r($data);exit;
				$Id = $payfrequencymodel->SaveorUpdatePayFrequencyData($data, $where);
				$tableid = $Id;
					
				$menuidArr = $menumodel->getMenuObjID('/payfrequency');
				$menuID = $menuidArr[0]['id'];
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);

				$frequencyData = $payfrequencymodel->fetchAll('isactive = 1','freqtype')->toArray();
				$opt ='';
				foreach($frequencyData as $record){
					$opt .= sapp_Global::selectOptionBuilder($record['id'], $record['freqtype']);
				}
				$this->view->frequencyData = $opt;
					
				$this->view->eventact = 'added';
				$close = 'close';
				$this->view->popup=$close;
			}else
			{
				$messages = $payfrequencyform->getMessages();
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
		$this->view->form = $payfrequencyform;
		$this->view->ermsg = '';

	}
	

    public function viewAction()
	{	
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		$objName = 'payfrequency';
		$payfrequencyform = new Default_Form_payfrequency();
		$payfrequencyform->removeElement("submit");
		$elements = $payfrequencyform->getElements();
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)
			{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
				$element->setAttrib("disabled", "disabled");
					}
        	}
        }
		$payfrequencymodel = new Default_Model_Payfrequency();	
		/*$data = $payfrequencymodel->getsinglePayfrequencyData($id);
		$payfrequencyform->populate($data);
		$this->view->controllername = $objName;
		$this->view->id = $id;
		$this->view->form = $payfrequencyform;*/
		try
		{
			if(is_numeric($id) && $id>0)
			{
				$data = $payfrequencymodel->getsinglePayfrequencyData($id);
				//echo "<pre>";print_r($data);die;
				if(!empty($data) && $data != 'norows')
				{
					$payfrequencyform->populate($data[0]);
					$this->view->form = $payfrequencyform;
					$this->view->controllername = $objName;
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
				$this->view->ermsg = 'nodata';
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
		$objName = 'payfrequency';
		$payfrequencyform = new Default_Form_payfrequency();
		$payfrequencymodel = new Default_Model_Payfrequency();
		/*if($id)
		{
			$data = $payfrequencymodel->getsinglePayfrequencyData($id);
			$
		}
		$this->view->form = $payfrequencyform;*/
		try
		{
			if($id)
			{
				if(is_numeric($id) && $id>0)
				{
					$data = $payfrequencymodel->getsinglePayfrequencyData($id);
					//echo "<pre>";print_r($data);die;
					if(!empty($data) && $data != 'norows')
					{
						$payfrequencyform->populate($data[0]);
						$payfrequencyform->submit->setLabel('Update'); 
						$this->view->form = $payfrequencyform;
						$this->view->controllername = $objName;
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
					$this->view->ermsg = 'nodata';
				}
			}
			else
			{
				$this->view->ermsg = '';
				$this->view->form = $payfrequencyform;
			}
		}
		catch(Exception $e)
		{
			 $this->view->ermsg = 'nodata';
		}
		if($this->getRequest()->getPost()){
		    if($payfrequencyform->isValid($this->_request->getPost())){
			    $id = $this->_request->getParam('id'); 
			    $freqtype = $this->_request->getParam('freqtype');
				$freqdescription = $this->_request->getParam('freqdescription');
				$date = new Zend_Date();
				$menumodel = new Default_Model_Menu();
				$actionflag = '';
				$tableid  = ''; 
				   $data = array('freqtype'=>trim($freqtype),
				           'freqdescription'=>trim($freqdescription),
							'modifiedby'=>$loginUserId,
						 // 'modifieddate'=>$date->get('yyyy-MM-dd HH:mm:ss')
						   'modifieddate'=>gmdate("Y-m-d H:i:s")
						);
					if($id!=''){
						$where = array('id=?'=>$id);  
						$actionflag = 2;
					}
					else
					{
					    $data['createdby'] = $loginUserId;
						//$data['createddate'] = $date->get('yyyy-MM-dd HH:mm:ss');
						$data['createddate'] = gmdate("Y-m-d H:i:s");
						$data['isactive'] = 1;
						$where = '';
						$actionflag = 1;
					}
					//echo "<pre>";print_r($data);exit;
					$Id = $payfrequencymodel->SaveorUpdatePayFrequencyData($data, $where);
					if($Id == 'update')
					{
					   $tableid = $id;
					   //$this->_helper->getHelper("FlashMessenger")->addMessage("Pay Frequency updated successfully.");
					   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Pay frequency updated successfully."));
					}   
					else
					{
                       $tableid = $Id; 	
                        //$this->_helper->getHelper("FlashMessenger")->addMessage("Pay Frequency added successfully.");					   
						$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Pay frequency added successfully."));
					}   
					$menuidArr = $menumodel->getMenuObjID('/payfrequency');
					$menuID = $menuidArr[0]['id'];
					//echo "<pre>";print_r($menuidArr);exit;
					$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
					//echo $result;exit;
    			    $this->_redirect('payfrequency');		
			}else
			{
     			$messages = $payfrequencyform->getMessages();
				foreach ($messages as $key => $val)
					{
						foreach($val as $key2 => $val2)
						 {
							//echo $key." >> ".$val2;
							$msgarray[$key] = $val2;
                                                        break;
						 }
					}
				$this->view->msgarray = $msgarray;
			
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
		 $messages['message'] = ''; $messages['msgtype'] = '';$messages['flagtype'] = '';
		 $actionflag = 3;
		    if($id)
			{
			 $payfrequencymodel = new Default_Model_Payfrequency();
			  $menumodel = new Default_Model_Menu();
			  $data = array('isactive'=>0, 'modifieddate'=>gmdate("Y-m-d H:i:s"));
			  $where = array('id=?'=>$id);
                          $pay_data = $payfrequencymodel->getsinglePayfrequencyData($id);
                          //print_r($pay_data);exit;
			  $Id = $payfrequencymodel->SaveorUpdatePayFrequencyData($data, $where);
			    if($Id == 'update')
                            {
                                sapp_Global::send_configuration_mail("Pay Frequency", $pay_data[0]['freqtype']);
				   $menuidArr = $menumodel->getMenuObjID('/payfrequency');
				   $menuID = $menuidArr[0]['id'];
					//echo "<pre>";print_r($objid);exit;
				   $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id); 
				   $messages['message'] = 'Pay frequency deleted successfully.';
				    $messages['msgtype'] = 'success';//$messages['flagtype'] = 'process';
				}   
				else{
                   $messages['message'] = 'Pay frequency Status cannot be deleted.';	
					 $messages['msgtype'] = 'error';//$messages['flagtype'] = 'process';
					}
			}
			else
			{ 
			 $messages['message'] = 'Pay frequency cannot be deleted.';
			  $messages['msgtype'] = 'error';//$messages['flagtype'] = 'process';
			}
			$this->_helper->json($messages);
		
	}
	
	

}

