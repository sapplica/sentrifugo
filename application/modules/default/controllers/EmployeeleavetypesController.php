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

class Default_EmployeeleavetypesController extends Zend_Controller_Action
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
		$employeeleavetypesmodel = new Default_Model_Employeeleavetypes();	
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
										
			$sort = 'DESC';$by = 'e.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';	
			$searchArray = array();
		}
		else 
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'e.modifieddate';
			if($dashboardcall == 'Yes')
				$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
			else 
				$perPage = $this->_getParam('per_page',PERPAGE);
			
			$pageNo = $this->_getParam('page', 1);
			$searchData = $this->_getParam('searchData');	
			$searchData = rtrim($searchData,',');
		}	
		
		$dataTmp = $employeeleavetypesmodel->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall);
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
		$objName = 'employeeleavetypes';
		$employeeleavetypesform = new Default_Form_employeeleavetypes();
		$employeeleavetypesform->removeElement("submit");
		$elements = $employeeleavetypesform->getElements();
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)
			{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
				$element->setAttrib("disabled", "disabled");
					}
        	}
        }
		$employeeleavetypesmodel = new Default_Model_Employeeleavetypes();	
		try
		{
			if(is_numeric($id) && $id>0)
			{
				$data = $employeeleavetypesmodel->getsingleEmployeeLeavetypeData($id);
				if(!empty($data) && $data != 'norows')
				{
					//echo $data[0]['leavepreallocated'];exit;
					
					if($data[0]['leavepreallocated']==2)
					{
						$data[0]['leavepreallocated']="no";
					}
					else 
					{
						$data[0]['leavepreallocated']="yes";
					}
					
				if($data[0]['leavepredeductable']==2)
					{
						$data[0]['leavepredeductable']="no";
					}
					else 
					{
						$data[0]['leavepredeductable']="yes";
					}
					$employeeleavetypesform->populate($data[0]);
					$this->view->form = $employeeleavetypesform;
					$this->view->controllername = $objName;
					$this->view->id = $id;
					$this->view->data = $data[0];
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
		
		$objName = 'employeeleavetypes';
		$employeeleavetypesform = new Default_Form_employeeleavetypes();
		$employeeleavetypesmodel = new Default_Model_Employeeleavetypes();
		try
		{
			if($id)
			{
				$data = $employeeleavetypesmodel->getsingleEmployeeLeavetypeData($id);
				if(!empty($data) && $data != 'norows')
				{
					$employeeleavetypesform->populate($data[0]);
					$employeeleavetypesform->submit->setLabel('Update');
					$this->view->form = $employeeleavetypesform;
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
				$this->view->form = $employeeleavetypesform;
			}
			
		}
		catch(Exception $e)
		{
			 $this->view->ermsg = 'nodata';
		}
		if($this->getRequest()->getPost()){
		    if($employeeleavetypesform->isValid($this->_request->getPost())){
			    $id = $this->_request->getParam('id'); 
			    $leavetype = $this->_request->getParam('leavetype');
				$numberofdays = $this->_request->getParam('numberofdays');
				$leavecode = $this->_request->getParam('leavecode');
				$leavepreallocated = $this->_request->getParam('leavepreallocated');
				$leavepredeductable = $this->_request->getParam('leavepredeductable');
				$description = $this->_request->getParam('description');
				$date = new Zend_Date();
				$actionflag = '';
				$tableid  = ''; 
				   $data = array( 'leavetype'=>trim($leavetype),
				                  'numberofdays'=>$numberofdays, 
				                  'leavecode'=>trim($leavecode),
								  'leavepreallocated'=>trim($leavepreallocated),
                                  'leavepredeductable'=>trim($leavepredeductable),								  
				      			 'description'=>trim($description),
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
					$Id = $employeeleavetypesmodel->SaveorUpdateEmployeeLeaveTypeData($data, $where);
					if($Id == 'update')
					{
					   $tableid = $id;
					   $this->_helper->getHelper("FlashMessenger")->addMessage("Leave type updated successfully.");
					}   
					else
					{
                       $tableid = $Id; 	
                        $this->_helper->getHelper("FlashMessenger")->addMessage("Leave type added successfully.");					   
					}   
					$menuID = EMPLOYEELEAVETYPES;
					$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
    			    $this->_redirect('employeeleavetypes');		
			}else
			{
     			$messages = $employeeleavetypesform->getMessages();
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
	
	public function deleteAction()
	{
	     $auth = Zend_Auth::getInstance();
     		if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
				}
		$id = $this->_request->getParam('objid');
		 $deleteflag=$this->_request->getParam('deleteflag');
		 $messages['message'] = ''; $messages['msgtype'] = '';$messages['flagtype'] = '';
		 $actionflag = 3;
		    if($id)
			{
			$employeeleavetypesmodel = new Default_Model_Employeeleavetypes();
			  $data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
			  $where = array('id=?'=>$id);
                          $leave_data = $employeeleavetypesmodel->getsingleEmployeeLeavetypeData($id);
						  
			  $Id = $employeeleavetypesmodel->SaveorUpdateEmployeeLeaveTypeData($data, $where);
			    if($Id == 'update')
				{
                   sapp_Global::send_configuration_mail("Leave Type", $leave_data[0]['leavetype']);
				   $menuID = EMPLOYEELEAVETYPES;
				   $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id); 
				   $messages['message'] = 'Leave type deleted successfully.';
				    $messages['msgtype'] = 'success';	
				}   
				else
                {
					$messages['message'] = 'Leave type cannot be deleted.';	
					$messages['msgtype'] = 'error';
				}
			}
			else
			{ 
			 $messages['message'] = 'Leave type cannot be deleted.';
			  $messages['msgtype'] = 'error';
			}
			
			// delete success message after delete in view
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

