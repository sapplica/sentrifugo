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

class Default_CurrencyconverterController extends Zend_Controller_Action
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
		$currencyconvertermodel = new Default_Model_Currencyconverter();	
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
			$sort = 'DESC';$by = 'cc.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else 
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'cc.modifieddate';
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
				
		$dataTmp = $currencyconvertermodel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);		 		
					
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
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
	 	
	 	$this->view->popConfigPermission = $popConfigPermission;
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		$currencymodel = new Default_Model_Currency();	
		$currencyconverterform = new Default_Form_currencyconverter();
		$msgarray = array();
		$basecurrencymodeldata = $currencymodel->getCurrencyList();
                $currencyconverterform->basecurrency->addMultiOption('','Select Base Currency');
           if(sizeof($basecurrencymodeldata) > 0)
            { 			
				foreach ($basecurrencymodeldata as $basecurrencyres){
					$currencyconverterform->basecurrency->addMultiOption($basecurrencyres['id'],utf8_encode($basecurrencyres['currency']));
				}
			}
			else
			{
				$msgarray['basecurrency'] = 'Currencies are not configured yet.';
				$msgarray['targetcurrency'] = 'Currencies are not configured yet.';
				$this->view->configuremsg = 'notconfigurable';
			}
		
			
			$currencyconverterform->setAttrib('action',BASE_URL.'currencyconverter/add');
			$this->view->form = $currencyconverterform; 
			$this->view->msgarray = $msgarray; 
			if($this->getRequest()->getPost()){
				 $result = $this->save($currencyconverterform);	
				 $this->view->msgarray = $result; 
			}  		
			
	}

    public function viewAction()
	{	
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		$objName = 'currencyconverter';
		$currencyconverterform = new Default_Form_currencyconverter();
		$currencyconverterform->removeElement("submit");
		$elements = $currencyconverterform->getElements();
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
					$currencyconvertermodel = new Default_Model_Currencyconverter(); 	
					$data = $currencyconvertermodel->getCurrencyConverterDatabyID($id);
					if(!empty($data))
					{
						$data = $data[0]; 
						$currencystring = $data['basecurrency'].','.$data['targetcurrency'];
						$currencymodel = new Default_Model_Currency();
						$currencydata = $currencymodel->getCurrencyName($currencystring);
							foreach($currencydata as $curr)
							{
							  if($data['basecurrency'] == $curr['id'])
							  {
								$currencyconverterform->basecurrency->addMultiOption($curr['id'],utf8_encode($curr['targetcurr']));
								$data['basecurrency']=$curr['targetcurr'];
							  }
							  if($data['targetcurrency'] == $curr['id'])
							  {
								$currencyconverterform->targetcurrency->addMultiOption($curr['id'],utf8_encode($curr['targetcurr']));
								$data['targetcurrency']=$curr['targetcurr'];
							  }
							}
						$currencyconverterform->populate($data);
						$startdate = sapp_Global::change_date($data["start_date"], 'view');
						$enddate =sapp_Global::change_date($data["end_date"], 'view');
						$currencyconverterform->start_date->setValue($startdate);
						$currencyconverterform->end_date->setValue($enddate);
						$currencyconverterform->setDefault('basecurrency',$data['basecurrency']);
						$currencyconverterform->setDefault('targetcurrency',$data['targetcurrency']);
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
            else
			{
			   $this->view->ermsg = 'norecord';
			} 			
		}
		catch(Exception $e)
		{
			   $this->view->ermsg = 'nodata';
		}
		$this->view->controllername = $objName;
		$this->view->id = $id;
		$this->view->data = $data;
		$this->view->form = $currencyconverterform;
	}
	
	
	public function editAction()
	{	
	    $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;
					$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
		
	   $popConfigPermission = array();
	   $basecurrexists = '';
	   $targetcurrexists = '';
	 	if(sapp_Global::_checkprivileges(CURRENCY,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
	 		array_push($popConfigPermission,'currency');
	 	}
	 	
	 	$this->view->popConfigPermission = $popConfigPermission;
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		
		$currencyconverterform = new Default_Form_currencyconverter();
		$currencyconvertermodel = new Default_Model_Currencyconverter();
		$currencymodel = new Default_Model_Currency();
		$currencyconverterform->submit->setLabel('Update');
		try
        {		
			if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					$data = $currencyconvertermodel->getCurrencyConverterDatabyID($id);
                                        $currencyconverterform->basecurrency->addMultiOption('','Select Base Currency');
					if(!empty($data))
					{
						  $data = $data[0];
						  if($data['basecurrency'] && $data['targetcurrency'])
						  {
							$basecurrencydata = $currencymodel->getCurrencyList();
							$targetcurrencydata = $currencymodel->getCurrencyList();
							foreach ($basecurrencydata as $res){
								$currencyconverterform->basecurrency->addMultiOption($res['id'],utf8_encode($res['currency']));
								if($res['id'] == $data['basecurrency'])
								   $basecurrexists = 'true';
							}
							foreach ($targetcurrencydata as $res){
							    if($data['basecurrency'] != $res['id']){
									$currencyconverterform->targetcurrency->addMultiOption($res['id'],utf8_encode($res['currency']));
									if($res['id'] == $data['targetcurrency'])
								   		$targetcurrexists = 'true';
							    }
							}
						  }
						$currencyconverterform->populate($data);
						$startdate = sapp_Global::change_date($data["start_date"], 'view');
						$enddate =sapp_Global::change_date($data["end_date"], 'view');
						$currencyconverterform->start_date->setValue($startdate);
						$currencyconverterform->end_date->setValue($enddate);
						$currencyconverterform->setDefault('basecurrency',$data['basecurrency']);
						$currencyconverterform->setDefault('targetcurrency',$data['targetcurrency']);
						$currencyconverterform->setAttrib('action',BASE_URL.'currencyconverter/edit/id/'.$id);
                        $this->view->data = $data;
                        $this->view->basecurrexists = $basecurrexists;
                        $this->view->targetcurrexists = $targetcurrexists;
                                                
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
			else
			{
				$this->view->ermsg = '';
			}
		}	
		catch(Exception $e)
		{
			   $this->view->ermsg = 'nodata';
		}	
		$this->view->form = $currencyconverterform;
		if($this->getRequest()->getPost()){
      		$result = $this->save($currencyconverterform);	
		    $this->view->msgarray = $result; 
		}
	}
	
	public function save($currencyconverterform)
	{
	  $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		} 
	    $currencyconvertermodel = new Default_Model_Currencyconverter();
	    $errorflag = 'true';
		$msgarray = array();
	    $id = $this->_request->getParam('id');
		$basecurrparam = $this->_request->getParam('basecurrency');
                if($basecurrparam !='')  
                {		
                    $basecurrency = $this->_request->getParam('basecurrency');
		}
		$targetcurrparam = $this->_request->getParam('targetcurrency');
		if($targetcurrparam !='')
		{
			$targetcurrency = $this->_request->getParam('targetcurrency');
			
		}	
		$exchangerate = $this->_request->getParam('exchangerate');
		$start_date = $this->_request->getParam('start_date'); 
		$start_date =sapp_Global::change_date($start_date,'database');
		$end_date = $this->_request->getParam('end_date');
		$end_date =sapp_Global::change_date($end_date,'database');
		if($basecurrparam !='' && $targetcurrparam !='' && $basecurrency == $targetcurrency)
		{
		  $errorflag = 'false';
		  $msgarray['targetcurrency'] = 'Base currency and target currency cannot be same.';
		
		}
		  if($currencyconverterform->isValid($this->_request->getPost()) && $errorflag == 'true'){
                      try{
			$description = $this->_request->getParam('description');
			$date = new Zend_Date();
			$actionflag = '';
			$tableid  = ''; 
                        $cur_names = $currencyconvertermodel->getCurrencyNames($basecurrency, $targetcurrency);
                        $basecurrtext = isset($cur_names[$basecurrency]) ? $cur_names[$basecurrency]:"";
                        $targetcurrtext = isset($cur_names[$targetcurrency]) ? $cur_names[$targetcurrency]:"";
			   $data = array( 'basecurrency'=>$basecurrency,
							  'targetcurrency'=>$targetcurrency,
							  'basecurrtext'=>$basecurrtext,
							  'targetcurrtext'=>$targetcurrtext,
							 'exchangerate'=>trim($exchangerate),
							 'start_date'=>$start_date,
							 'end_date'=>$end_date,
							 'description'=>$description,
							  'modifiedby'=>$loginUserId,
							  'modifieddate'=>gmdate("Y-m-d H:i:s")
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
				$Id = $currencyconvertermodel->SaveorUpdateCurrencyConverterData($data, $where);
				if($Id == 'update')
				{
				   $tableid = $id;
				   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Currency converter updated successfully."));
				}   
				else
				{
				   $tableid = $Id; 	
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Currency converter added successfully."));					   
				}   
				$menuID = CURRENCYCONVERTER;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
				$this->_redirect('currencyconverter');	
                  }
                  catch(Exception $e)
                  {
                      $msgarray['basecurrency'] = "Something went wrong, please try again.";
                      return $msgarray;
                  }
		}else
		{
			$messages = $currencyconverterform->getMessages();
			foreach ($messages as $key => $val)
				{
					foreach($val as $key2 => $val2)
					 {
						$msgarray[$key] = $val2;
						break;
					 }
				}
			if(isset($basecurrparam) && $basecurrparam != 0 && $basecurrparam != '')
				{
					$currencymodel = new Default_Model_Currency();
					$currdatadata = $currencymodel->getTargetCurrencyList($basecurrency);
					if(sizeof($currdatadata > 0))
					{
						foreach($currdatadata as $res)
						{					
						 $currencyconverterform->targetcurrency->addMultiOption($res['id'],utf8_encode($res['targetcurr']));
						}
						if(isset($targetcurrparam) && $targetcurrparam != 0 && $targetcurrparam != '')
						  $currencyconverterform->setDefault('targetcurrency',$targetcurrparam);
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
		 $deleteflag= $this->_request->getParam('deleteflag');
		 $messages['message'] = '';
		 $messages['msgtype'] = '';
		 $actionflag = 3;
		    if($id)
			{
			$currencyconvertermodel = new Default_Model_Currencyconverter();
			  $currencyconverterdata = $currencyconvertermodel->getCurrencyConverterDatabyID($id);
			  $data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
			  $where = array('id=?'=>$id);
			  $Id = $currencyconvertermodel->SaveorUpdateCurrencyConverterData($data, $where);
			    if($Id == 'update')
				{
				   $menuID =CURRENCYCONVERTER;
				   $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
                   $configmail = sapp_Global::send_configuration_mail('Base Currency',$currencyconverterdata[0]['basecurrtext']);				   
				   $messages['message'] = 'Currency converter deleted successfully.';
				   $messages['msgtype'] = 'success';
				}   
				else
				{
                   $messages['message'] = 'Currency converter cannot be deleted.';
                   $messages['msgtype'] = 'error';
                }				   
			}
			else
			{ 
			 $messages['message'] = 'Currency converter cannot be deleted.';
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
}

