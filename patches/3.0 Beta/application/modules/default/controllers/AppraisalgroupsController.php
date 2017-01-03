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

class Default_AppraisalgroupsController extends Zend_Controller_Action
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
		$appraisalGroupsModel = new Default_Model_Appraisalgroups();	
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
			$sort = 'DESC';$by = 'ag.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else 
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'ag.modifieddate';
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
				
		$dataTmp = $appraisalGroupsModel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);		 		
					
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
		$appraisalGroupsForm = new Default_Form_Appraisalgroups();
		$msgarray = array();
		$appraisalGroupsForm->setAttrib('action',BASE_URL.'appraisalgroups/add');
		$this->view->form = $appraisalGroupsForm; 
		$this->view->msgarray = $msgarray; 
		$this->view->ermsg = '';
			if($this->getRequest()->getPost()){
				 $result = $this->save($appraisalGroupsForm);	
				 $this->view->msgarray = $result; 
			}  		
		$this->render('form');	
	}

    public function viewAction()
	{	
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $login_group_id = $auth->getStorage()->read()->group_id;
            $login_role_id = $auth->getStorage()->read()->emprole;
        }
        
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		$objName = 'appraisalgroups';
		
		try
		{
		    if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					$appraisalGroupsModel = new Default_Model_Appraisalgroups();	
					$data = $appraisalGroupsModel->getAppraisalGroupsDatabyID($id);
					$previ_data = sapp_Global::_checkprivileges(APPRAISALGROUPS,$login_group_id,$login_role_id,'edit');
					
					if(!empty($data))
					{
						$data = $data[0]; 
						$this->view->ermsg = ''; 
                    	$this->view->data = $data;
						$this->view->previ_data = $previ_data;
                   	 	$this->view->id = $id;
                    	$this->view->controllername = $objName;
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
		
		$appraisalGroupsForm = new Default_Form_Appraisalgroups();
		$appraisalGroupsModel = new Default_Model_Appraisalgroups();
		$appraisalGroupsForm->submit->setLabel('Update');
		try
        {		
			if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					$data = $appraisalGroupsModel->getAppraisalGroupsDatabyID($id);
					if(!empty($data))
					{
						  $data = $data[0];
						$appraisalGroupsForm->populate($data);
						$appraisalGroupsForm->setAttrib('action',BASE_URL.'appraisalgroups/edit/id/'.$id);
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
				$this->view->ermsg = '';
			}
		}	
		catch(Exception $e)
		{
			   $this->view->ermsg = 'nodata';
		}	
		$this->view->form = $appraisalGroupsForm;
		if($this->getRequest()->getPost()){
      		$result = $this->save($appraisalGroupsForm);	
		    $this->view->msgarray = $result; 
		}
		$this->render('form');	
	}
	
	public function save($appraisalGroupsForm)
	{
	  $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;
					$loginuserGroup = $auth->getStorage()->read()->group_id;
		} 
	    $appraisalGroupsModel = new Default_Model_Appraisalgroups();
		$msgarray = array();
	    
		  if($appraisalGroupsForm->isValid($this->_request->getPost())){
            try{
            $id = $this->_request->getParam('id');
            $appraisal_group_name = $this->_request->getParam('group_name');	
			$description = $this->_request->getParam('description');
			$menumodel = new Default_Model_Menu();
			$actionflag = '';
			$tableid  = ''; 
			   $data = array('group_name'=>$appraisal_group_name, 
							 'description'=>($description!=''?trim($description):NULL),
							  'modifiedby'=>$loginUserId,
							   'modifiedby_role'=>$loginuserRole,
							   'modifiedby_group'=>$loginuserGroup,
							  'modifieddate'=>gmdate("Y-m-d H:i:s")
					);
				if($id!=''){
					$where = array('id=?'=>$id);  
					$actionflag = 2;
				}
				else
				{
					$data['createdby_role'] = $loginuserRole;
					$data['createdby_group'] = $loginuserGroup;
					
					$data['createdby'] = $loginUserId;
					$data['createddate'] = gmdate("Y-m-d H:i:s");
					$data['isactive'] = 1;
					$where = '';
					$actionflag = 1;
				}
				$Id = $appraisalGroupsModel->SaveorUpdateAppraisalGroupsData($data, $where);
				if($Id == 'update')
				{
				   $tableid = $id;
				   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Group updated successfully."));
				}   
				else
				{
				   $tableid = $Id; 	
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Group added successfully."));					   
				}   
				$menuidArr = $menumodel->getMenuObjID('/appraisalgroups');
				$menuID = $menuidArr[0]['id'];
				
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
				$this->_redirect('appraisalgroups');	
                  }
        catch(Exception $e)
          {
             $msgarray['group_name'] = "Something went wrong, please try again.";
             return $msgarray;
          }
		}else
		{
			$messages = $appraisalGroupsForm->getMessages();
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
					$loginuserRole = $auth->getStorage()->read()->emprole;
					$loginuserGroup = $auth->getStorage()->read()->group_id;
				}
		 $id = $this->_request->getParam('objid');
		 $messages['message'] = '';
		 $messages['msgtype'] = '';
		 $count = 0;
		 $actionflag = 3;
		    if($id)
			{
			  $appraisalGroupsModel = new Default_Model_Appraisalgroups();
			  $menumodel = new Default_Model_Menu();			  
			  $appGroupsdata = $appraisalGroupsModel->getAppraisalGroupsDatabyID($id);
			  
			  if($appGroupsdata[0]['isused'] == 0)	
			  {
				  $data = array('isactive'=>0, 'modifiedby'=>$loginUserId, 'modifiedby_role'=>$loginuserRole,
								   'modifiedby_group'=>$loginuserGroup, 'modifieddate'=>gmdate("Y-m-d H:i:s"));
				  $where = array('id=?'=>$id);
				  $Id = $appraisalGroupsModel->SaveorUpdateAppraisalGroupsData($data, $where);
				    if($Id == 'update')
					{
						$menuidArr = $menumodel->getMenuObjID('/appraisalgroups');
						$menuID = $menuidArr[0]['id'];
						$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
						/***
						** commented on 29-04-2015 by sapplica
						** need to integrate mail template					   
							$configmail = sapp_Global::send_configuration_mail('Group',$appGroupsdata[0]['group_name']);	
						***/					   
						$messages['message'] = 'Group deleted successfully.';
						$messages['msgtype'] = 'success';
					}   
					else
					{
	                   $messages['message'] = 'Group cannot be deleted.';
	                   $messages['msgtype'] = 'error';
	                }
			  }else{
				  	   $messages['message'] = 'Group cannot be deleted as its using in appraisal process.';
	                   $messages['msgtype'] = 'error';
				  }
			}
			else
			{ 
			 $messages['message'] = 'Group cannot be deleted.';
			 $messages['msgtype'] = 'error';
			}
			$this->_helper->json($messages);		
	}
	
}

