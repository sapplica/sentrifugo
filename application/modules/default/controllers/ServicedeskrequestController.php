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

class Default_ServicedeskrequestController extends Zend_Controller_Action
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
		$servicedeskrequestmodel = new Default_Model_Servicedeskrequest();	
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
			$sort = 'DESC';$by = 'sdr.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else 
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'sdr.modifieddate';
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
				
		$dataTmp = $servicedeskrequestmodel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);		 		
					
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
		$this->render('commongrid/index', null, true);
		
    }
	
	 public function addAction()
	{
	   $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;
					$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		$servicedeskrequestsform = new Default_Form_servicedeskrequest();
		$servicedeskdepartmentmodel = new Default_Model_Servicedeskdepartment();
		$msgarray = array();
		$popConfigPermission = array();
		
	 	if(sapp_Global::_checkprivileges(SERVICEDESKDEPARTMENT,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
	 		array_push($popConfigPermission,'servicedeskdepartment');
	 	}
	 	
	 	$servicedeskdepartmentData = $servicedeskdepartmentmodel->getSDDepartmentData();
	 		if(sizeof($servicedeskdepartmentData) > 0)
            { 			
				foreach ($servicedeskdepartmentData as $servicedeskdepartmentres){
					$servicedeskrequestsform->service_desk_id->addMultiOption($servicedeskdepartmentres['id'],utf8_encode($servicedeskdepartmentres['service_desk_name']));
				}
			}
			else
			{
				$msgarray['service_desk_id'] = 'Category names are not configured yet.';
				$this->view->configuremsg = 'notconfigurable';
			}
	 	$this->view->popConfigPermission = $popConfigPermission;
		$servicedeskrequestsform->setAttrib('action',BASE_URL.'servicedeskrequest/add');
		$this->view->form = $servicedeskrequestsform; 
		$this->view->msgarray = $msgarray; 
		$this->view->ermsg = '';
			if($this->getRequest()->getPost()){
				 $result = $this->add($servicedeskrequestsform);	
				 $this->view->msgarray = $result; 
			}  		
	}

    public function viewAction()
	{	
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		$objName = 'servicedeskrequest';
		$servicedeskrequestsform = new Default_Form_servicedeskrequest();
		$servicedeskdepartmentmodel = new Default_Model_Servicedeskdepartment();
		$servicedeskrequestmodel = new Default_Model_Servicedeskrequest();
		$servicedeskrequestsform->removeElement("submitbutton");
		$elements = $servicedeskrequestsform->getElements();
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
					$data = $servicedeskrequestmodel->getServiceDeskRequestbyID($id);
					if(!empty($data))
					{
						$data = $data[0]; 
						$servicedeskdepartmentData = $servicedeskdepartmentmodel->getServiceDeskDepartmentDatabyID($data['service_desk_id']);
						if(sizeof($servicedeskdepartmentData) > 0)
						{
						  $servicedeskrequestsform->service_desk_id->addMultiOption($servicedeskdepartmentData[0]['id'],utf8_encode($servicedeskdepartmentData[0]['service_desk_name']));
						  $data['service_desk_id']=$servicedeskdepartmentData[0]['service_desk_name'];
						}
						else
						{
							$data['service_desk_id']="";
						}
						$servicedeskrequestsform->populate($data);
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
		$this->view->flag = 'view';
		$this->view->form = $servicedeskrequestsform;
		$this->render('form');	
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
		
		$servicedeskrequestsform = new Default_Form_servicedeskrequest();
		$servicedeskdepartmentmodel = new Default_Model_Servicedeskdepartment();
		$servicedeskrequestmodel = new Default_Model_Servicedeskrequest();	
		$msgarray = array();
		$popConfigPermission = array();
		
	 	if(sapp_Global::_checkprivileges(SERVICEDESKDEPARTMENT,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
	 		array_push($popConfigPermission,'servicedeskdepartment');
	 	}
		$this->view->popConfigPermission = $popConfigPermission;
		try
        {		
			if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					$data = $servicedeskrequestmodel->getServiceDeskRequestbyID($id);
					if(!empty($data))
					{
						  $data = $data[0];
						$servicedeskdepartmentData = $servicedeskdepartmentmodel->getSDDepartmentData();
				 		if(sizeof($servicedeskdepartmentData) > 0)
			            { 			
							foreach ($servicedeskdepartmentData as $servicedeskdepartmentres){
								$servicedeskrequestsform->service_desk_id->addMultiOption($servicedeskdepartmentres['id'],utf8_encode($servicedeskdepartmentres['service_desk_name']));
							}
						}  
						$servicedeskrequestsform->populate($data);
						$servicedeskrequestsform->setDefault('service_desk_id',$data['service_desk_id']);
						$servicedeskrequestsform->setAttrib('action',BASE_URL.'servicedeskrequest/edit/id/'.$id);
                        $this->view->data = $data;
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
				$this->view->ermsg = 'nodata';
			}
		}	
		catch(Exception $e)
		{
			   $this->view->ermsg = 'nodata';
		}	
		$this->view->form = $servicedeskrequestsform;
		if($this->getRequest()->getPost()){
      		$result = $this->save($servicedeskrequestsform);	
		    $this->view->msgarray = $result; 
		}
		$this->render('form');	
	}
	
	public function add($servicedeskrequestsform)
	{
	  $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
				 $loginUserId = $auth->getStorage()->read()->id;
		} 
	    $servicedeskrequestmodel = new Default_Model_Servicedeskrequest();	
		$msgarray = array();
		$errorflag = 'true';
		$service_desk_id = $this->_request->getParam('service_desk_id');
		$service_request_name_arr = $this->_request->getParam('service_request_name');
		$servArr = array_count_values($service_request_name_arr);
		$description_arr = $this->_request->getParam('description');
		if(!empty($service_request_name_arr))
		{
			$servArr = array_count_values($service_request_name_arr);
			for($i=0;$i<sizeof($service_request_name_arr);$i++)
			{
					if($service_request_name_arr[$i] == '')
					{
						$msgarray['request_name'][$i] = 'Please enter request type.';
						$errorflag = 'false';
					}else if(!preg_match('/^[a-zA-Z0-9.\- ?]+$/', $service_request_name_arr[$i]))
					{
						$msgarray['request_name'][$i] = 'Please enter valid request type.';
						$errorflag = 'false';
					}else if($i>0 && $servArr[$service_request_name_arr[$i]] > 1)
					{
							$msgarray['request_name'][$i] = 'Please enter different request type.';
							$errorflag = 'false';
					}
					else 
					{
						if($service_desk_id!='')
						{
							$duplicaterequestname = $servicedeskrequestmodel->checkduplicaterequestname($service_desk_id,$service_request_name_arr[$i]);
							if(!empty($duplicaterequestname))
							{
								if($duplicaterequestname[0]['count'] > 0)
								{
									$msgarray['request_name'][$i] = 'Request type already exists for the category.';
									$errorflag = 'false';
								}
							}
						}	
					}
			}
			$msgarray['requestsize'] = sizeof($service_request_name_arr);
		}	
		  if($servicedeskrequestsform->isValid($this->_request->getPost()) && $errorflag == 'true'){
            try{
            $id = $this->_request->getParam('id');
            $service_desk_id = $this->_request->getParam('service_desk_id');
			$actionflag = 1;
			$tableid  = ''; 
			$where = '';
			for($i=0;$i<sizeof($service_request_name_arr);$i++)
			{
			   $data = array('service_desk_id'=>$service_desk_id,
			                 'service_request_name'=>trim($service_request_name_arr[$i]), 
							 'description'=>($description_arr[$i]!=''?trim($description_arr[$i]):NULL),
							  'modifiedby'=>$loginUserId,
							  'modifieddate'=>gmdate("Y-m-d H:i:s"),
			   				  'createdby' => $loginUserId,
							  'createddate'=> gmdate("Y-m-d H:i:s"),
							  'isactive'=> 1
					);
				
				$Id = $servicedeskrequestmodel->SaveorUpdateServiceDeskRequestData($data, $where);
				$tableid = $Id; 	
				$menuID = SERVICEDESKREQUEST;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
			}
				
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Request types added successfully."));
				$this->_redirect('servicedeskrequest');	
                  }
        catch(Exception $e)
          {
             $msgarray['service_desk_id'] = "Something went wrong, please try again.";
             return $msgarray;
          }
		}else
		{
			$messages = $servicedeskrequestsform->getMessages();
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
	
	
	public function save($servicedeskrequestsform)
	{
	  $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
				 $loginUserId = $auth->getStorage()->read()->id;
		} 
	    $servicedeskrequestmodel = new Default_Model_Servicedeskrequest();	
		$msgarray = array();
		  if($servicedeskrequestsform->isValid($this->_request->getPost())){
            try{
            $id = $this->_request->getParam('id');
            $service_desk_id = $this->_request->getParam('service_desk_id');
            $service_request_name = trim($this->_request->getParam('service_request_name'));	
			$description = trim($this->_request->getParam('description'));
			$actionflag = '';
			$tableid  = ''; 
			   $data = array('service_desk_id'=>$service_desk_id,
			                 'service_request_name'=>$service_request_name, 
							 'description'=>($description!=''?$description:NULL),
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
				$Id = $servicedeskrequestmodel->SaveorUpdateServiceDeskRequestData($data, $where);
				if($Id == 'update')
				{
				   $tableid = $id;
				   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Request type updated successfully."));
				}   
				else
				{
				   $tableid = $Id; 	
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Request type added successfully."));					   
				}   
				$menuID = SERVICEDESKREQUEST;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
				$this->_redirect('servicedeskrequest');	
                  }
        catch(Exception $e)
          {
             $msgarray['service_desk_id'] = "Something went wrong, please try again.";
             return $msgarray;
          }
		}else
		{
			$messages = $servicedeskrequestsform->getMessages();
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
		 $deleteflag=$this->_request->getParam('deleteflag');
		 $messages['message'] = '';
		 $messages['msgtype'] = '';
		 $count = 0;
		 $actionflag = 3;
		    if($id)
			{
			$servicedeskrequestmodel = new Default_Model_Servicedeskrequest();
			$servicedeskconfmodel = new Default_Model_Servicedeskconf();
			  $pendingRequestdata = $servicedeskconfmodel->getServiceReqDeptCount($id,2);
			  if(!empty($pendingRequestdata))
			  	$count = $pendingRequestdata[0]['count'];
			  if($count < 1)	
			  {
			  $servicedeskrequestdata = $servicedeskrequestmodel->getServiceDeskRequestbyID($id);
			  $data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
			  $where = array('id=?'=>$id);
			  $Id = $servicedeskrequestmodel->SaveorUpdateServiceDeskRequestData($data, $where);
			    if($Id == 'update')
				{
				   $menuID = SERVICEDESKREQUEST;
				   $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
                   $configmail = sapp_Global::send_configuration_mail('Request type',$servicedeskrequestdata[0]['service_request_name']);				   
				   $messages['message'] = 'Request type deleted successfully.';
				   $messages['msgtype'] = 'success';
				}   
				else
				{
                   $messages['message'] = 'Request type cannot be deleted.';
                   $messages['msgtype'] = 'error';
                }
			  }else
			  {
			  	   $messages['message'] = 'Request type cannot be deleted as requests are in pending state.';
                   $messages['msgtype'] = 'error';
			  } 				   
			}
			else
			{ 
			 $messages['message'] = 'Request type cannot be deleted.';
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

