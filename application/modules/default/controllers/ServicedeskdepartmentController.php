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

class Default_ServicedeskdepartmentController extends Zend_Controller_Action
{

    private $options;
    public function preDispatch()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('getrequests', 'json')->initContext();		
    }
	
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
    }

    public function indexAction()
    {
		$servicedeskdepartmentmodel = new Default_Model_Servicedeskdepartment();	
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
			$sort = 'DESC';$by = 'sd.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else 
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'sd.modifieddate';
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
				
		$dataTmp = $servicedeskdepartmentmodel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);		 		
					
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
		//$this->renderScript('commongrid/index.phtml');
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
		$servicedeskdepartmentform = new Default_Form_servicedeskdepartment();
		$msgarray = array();
		$servicedeskdepartmentform->setAttrib('action',BASE_URL.'servicedeskdepartment/add');
		$this->view->form = $servicedeskdepartmentform; 
		$this->view->msgarray = $msgarray; 
		$this->view->ermsg = '';
			if($this->getRequest()->getPost()){
				 $result = $this->save($servicedeskdepartmentform);	
				 $this->view->msgarray = $result; 
			}  		
		$this->render('form');	
	}

    public function viewAction()
	{	
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		$objName = 'servicedeskdepartment';
		$servicedeskdepartmentform = new Default_Form_servicedeskdepartment();
		$servicedeskdepartmentform->removeElement("submit");
		$elements = $servicedeskdepartmentform->getElements();
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
					$servicedeskdepartmentmodel = new Default_Model_Servicedeskdepartment();	
					$data = $servicedeskdepartmentmodel->getServiceDeskDepartmentDatabyID($id);
					if(!empty($data))
					{
						$data = $data[0]; 
						$servicedeskdepartmentform->populate($data);
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
		$this->view->form = $servicedeskdepartmentform;
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
		
		$servicedeskdepartmentform = new Default_Form_servicedeskdepartment();
		$servicedeskdepartmentmodel = new Default_Model_Servicedeskdepartment();
		$servicedeskdepartmentform->submit->setLabel('Update');
		try
        {		
			if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					$data = $servicedeskdepartmentmodel->getServiceDeskDepartmentDatabyID($id);
					if(!empty($data))
					{
						  $data = $data[0];
						$servicedeskdepartmentform->populate($data);
						$servicedeskdepartmentform->setAttrib('action',BASE_URL.'servicedeskdepartment/edit/id/'.$id);
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
		$this->view->form = $servicedeskdepartmentform;
		if($this->getRequest()->getPost()){
      		$result = $this->save($servicedeskdepartmentform);	
		    $this->view->msgarray = $result; 
		}
		$this->render('form');	
	}
	
	public function save($servicedeskdepartmentform)
	{
	  $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		} 
	    $servicedeskdepartmentmodel = new Default_Model_Servicedeskdepartment();
		$msgarray = array();
	    
		  if($servicedeskdepartmentform->isValid($this->_request->getPost())){
            try{
            $id = $this->_request->getParam('id');
            $service_desk_name = $this->_request->getParam('service_desk_name');	
			$description = $this->_request->getParam('description');
			$actionflag = '';
			$tableid  = ''; 
			   $data = array('service_desk_name'=>$service_desk_name, 
							 'description'=>($description!=''?trim($description):NULL),
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
				$Id = $servicedeskdepartmentmodel->SaveorUpdateServiceDeskDepartmentData($data, $where);
				if($Id == 'update')
				{
				   $tableid = $id;
				   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Category updated successfully."));
				}   
				else
				{
				   $tableid = $Id; 	
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Category added successfully."));					   
				}   
				$menuID = SERVICEDESKDEPARTMENT;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
				$this->_redirect('servicedeskdepartment');	
                  }
        catch(Exception $e)
          {
             $msgarray['service_desk_name'] = "Something went wrong, please try again.";
             return $msgarray;
          }
		}else
		{
			$messages = $servicedeskdepartmentform->getMessages();
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
            if($auth->hasIdentity())
            {
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
                $servicedeskdepartmentmodel = new Default_Model_Servicedeskdepartment();
                $servicedeskrequestmodel = new Default_Model_Servicedeskrequest();
                $servicedeskconfmodel = new Default_Model_Servicedeskconf();
                $pendingRequestdata = $servicedeskconfmodel->getServiceReqDeptCount($id,1);
                if(!empty($pendingRequestdata))
                    $count = $pendingRequestdata[0]['count'];
                $service_req_cnt = $servicedeskrequestmodel->getReqCnt($id);
                if($service_req_cnt > 0)
                    $count = $count + $service_req_cnt;
                if($count < 1)
                {	
                    $servicedeskdepartmentdata = $servicedeskdepartmentmodel->getServiceDeskDepartmentDatabyID($id);
                    $data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
                    $where = array('id=?'=>$id);
                    $reqwhere = array('service_desk_id=?'=>$id);
                    $Id = $servicedeskdepartmentmodel->SaveorUpdateServiceDeskDepartmentData($data, $where);
                    $RId = $servicedeskrequestmodel->SaveorUpdateServiceDeskRequestData($data, $reqwhere);
                    if($Id == 'update')
                    {
						$menuID = SERVICEDESKDEPARTMENT;
                        $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
                        $configmail = sapp_Global::send_configuration_mail('Category',$servicedeskdepartmentdata[0]['service_desk_name']);				   
                        $messages['message'] = 'Category deleted successfully.';
                        $messages['msgtype'] = 'success';
                    }   
                    else
                    {
                       $messages['message'] = 'Category cannot be deleted.';
                        $messages['msgtype'] = 'error';
                    }
                }
                else
                {
                    $messages['message'] = 'Category cannot be deleted.';
                    $messages['msgtype'] = 'error';
                } 				   
            }
            else
            { 
                $messages['message'] = 'Category cannot be deleted.';
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

		$msgarray = array();
		$controllername = 'servicedeskdepartment';
		$servicedeskdepartmentform = new Default_Form_servicedeskdepartment();
		$servicedeskdepartmentmodel = new Default_Model_Servicedeskdepartment();
		$servicedeskdepartmentform->setAction(BASE_URL.'servicedeskdepartment/addpopup');
		if($this->getRequest()->getPost()){
			if($servicedeskdepartmentform->isValid($this->_request->getPost())){
			$id = $this->_request->getParam('id');
            $service_desk_name = $this->_request->getParam('service_desk_name');	
			$description = $this->_request->getParam('description');
			$actionflag = '';
			$tableid  = ''; 
			   $data = array('service_desk_name'=>$service_desk_name, 
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
				$Id = $servicedeskdepartmentmodel->SaveorUpdateServiceDeskDepartmentData($data, $where);
				$tableid = $Id;
				$menuID = SERVICEDESKDEPARTMENT;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);

				$servicedeskdepartmentData = $servicedeskdepartmentmodel->getSDDepartmentData();
				$opt ='';
				foreach($servicedeskdepartmentData as $record){
					$opt .= sapp_Global::selectOptionBuilder($record['id'], utf8_encode($record['service_desk_name']));
				}
				$this->view->departmentData = $opt;
					
				$this->view->eventact = 'added';
				$close = 'close';
				$this->view->popup=$close;
			}else
			{
				$messages = $servicedeskdepartmentform->getMessages();
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
		$this->view->form = $servicedeskdepartmentform;
		$this->view->ermsg = '';
		$this->render('form');	
	}
	
	
	public function getrequestsAction()
    {
        $service_desk_id = $this->_getParam('service_desk_id',null);
        $service_desk_conf_id = $this->_getParam('service_desk_conf_id',null);
        $request_for_flag = $this->_getParam('request_for_flag',null);
        $data = array();
        $options = sapp_Global::selectOptionBuilder('', 'Select request', '');
        if($service_desk_id != '')
        {
        	if($request_for_flag!='') {
        		$request_for_param = explode('_',$service_desk_id);
        		if($request_for_param[1]==1) {
	        		$sd_dept_model = new Default_Model_Servicedeskdepartment();
		            $data = $sd_dept_model->getRequestsById($request_for_param[0]);
		            if(count($data) > 0)
		            {
		                foreach($data as $opt)
		                {                    
		                    $options .= sapp_Global::selectOptionBuilder($opt['id'], utf8_encode($opt['service_request_name']), '');
		                }
		            }
        			
        		}else{
        			$assetsModel = new Assets_Model_Assets();
        			$data = $assetsModel->getAllAssetForCategory($request_for_param[0]);
        			if(count($data) > 0)
        		    {
		                foreach($data as $opt)
		                {                    
		                    $options .= sapp_Global::selectOptionBuilder($opt['id'], utf8_encode($opt['name']), '');
		                }
		            }
        		}
        	}
        	else
        	{	
	            $sd_dept_model = new Default_Model_Servicedeskdepartment();
	            $data = $sd_dept_model->getRequestsById($service_desk_id);
	            if(count($data) > 0)
	            {
	                foreach($data as $opt)
	                {                    
	                    $options .= sapp_Global::selectOptionBuilder($opt['id'], utf8_encode($opt['service_request_name']), '');
	                }
	            }
        	}            
        }
        $attachment = false;
        if($service_desk_conf_id != '')
        {
        	
        	$sd_conf_model = new Default_Model_Servicedeskconf();
        	$sd_conf_data = $sd_conf_model->getServiceDeskConfbyID($service_desk_conf_id);
        	if(count($sd_conf_data)>0){
        		if($sd_conf_data[0]['attachment'] == 1)
        			$attachment = true;	
        	}
        }
        $this->_helper->json(array('options' => $options,'datacount'=>count($data),'attachment'=>$attachment));
    }// end of getrequests action
	
}

