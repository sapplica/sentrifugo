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

class Default_AppraisalskillsController extends Zend_Controller_Action
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
		$appraisalSkillsModel = new Default_Model_Appraisalskills();	
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
			$sort = 'DESC';$by = 'as.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else 
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'as.modifieddate';
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
				
		$dataTmp = $appraisalSkillsModel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);		 		
					
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
		$appraisalSkillsForm = new Default_Form_Appraisalskills();
		$msgarray = array();
		$appraisalSkillsForm->setAttrib('action',DOMAIN.'appraisalskills/add');
		$this->view->form = $appraisalSkillsForm; 
		$this->view->msgarray = $msgarray; 
		$this->view->ermsg = '';
			if($this->getRequest()->getPost()){
				 $result = $this->save($appraisalSkillsForm);	
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
		$objName = 'appraisalskills';
		$appraisalSkillsForm = new Default_Form_Appraisalskills();
		$appraisalSkillsForm->removeElement("submit");
		$elements = $appraisalSkillsForm->getElements();
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
					$appraisalSkillsModel = new Default_Model_Appraisalskills();	
					$data = $appraisalSkillsModel->getAppraisalSkillsDatabyID($id);
					if(!empty($data))
					{
						$data = $data[0]; 
						$appraisalSkillsForm->populate($data);
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
		$this->view->form = $appraisalSkillsForm;
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
		
		$appraisalSkillsForm = new Default_Form_Appraisalskills();
		$appraisalSkillsModel = new Default_Model_Appraisalskills();
		$appraisalSkillsForm->submit->setLabel('Update');
		try
        {		
			if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					$data = $appraisalSkillsModel->getAppraisalSkillsDatabyID($id);
					if(!empty($data))
					{
						  $data = $data[0];
						$appraisalSkillsForm->populate($data);
						$appraisalSkillsForm->setAttrib('action',DOMAIN.'appraisalskills/edit/id/'.$id);
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
		$this->view->form = $appraisalSkillsForm;
		if($this->getRequest()->getPost()){
      		$result = $this->save($appraisalSkillsForm);	
		    $this->view->msgarray = $result; 
		}
		$this->render('form');	
	}
	
	public function save($appraisalSkillsForm)
	{
	  $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;
					$loginuserGroup = $auth->getStorage()->read()->group_id;
		} 
	    $appraisalSkillsModel = new Default_Model_Appraisalskills();
		$msgarray = array();
	    
		  if($appraisalSkillsForm->isValid($this->_request->getPost())){
            try{
            $id = $this->_request->getParam('id');
            $appraisal_skill_name = $this->_request->getParam('skill_name');	
			$description = $this->_request->getParam('description');
			$menumodel = new Default_Model_Menu();
			$actionflag = '';
			$tableid  = ''; 
			   $data = array('skill_name'=>$appraisal_skill_name, 
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
				$Id = $appraisalSkillsModel->SaveorUpdateAppraisalSkillsData($data, $where);
				if($Id == 'update')
				{
				   $tableid = $id;
				   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Skill updated successfully."));
				}   
				else
				{
				   $tableid = $Id; 	
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Skill added successfully."));					   
				}   
				$menuidArr = $menumodel->getMenuObjID('/appraisalskills');
				$menuID = $menuidArr[0]['id'];
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
				$this->_redirect('appraisalskills');	
                  }
        catch(Exception $e)
          {
             $msgarray['skill_name'] = "Something went wrong, please try again.";
             return $msgarray;
          }
		}else
		{
			$messages = $appraisalSkillsForm->getMessages();
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
			  $appraisalSkillsModel = new Default_Model_Appraisalskills();
			  $menumodel = new Default_Model_Menu();			  
			  $appSkillsdata = $appraisalSkillsModel->getAppraisalSkillsDatabyID($id);
			  
			  if($appSkillsdata[0]['isused'] == 0)	
			  {
				  $data = array('isactive'=>0, 'modifiedby'=>$loginUserId, 'modifiedby_role'=>$loginuserRole,
								   'modifiedby_group'=>$loginuserGroup, 'modifieddate'=>gmdate("Y-m-d H:i:s"));
				  $where = array('id=?'=>$id);
				  $Id = $appraisalSkillsModel->SaveorUpdateAppraisalSkillsData($data, $where);
				    if($Id == 'update')
					{
					   $menuidArr = $menumodel->getMenuObjID('/appraisalskills');
					   $menuID = $menuidArr[0]['id'];
					   $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
	                   $configmail = sapp_Global::send_configuration_mail('Skill',$appSkillsdata[0]['skill_name']);				   
					   $messages['message'] = 'Skill deleted successfully.';
					   $messages['msgtype'] = 'success';
					}   
					else
					{
	                   $messages['message'] = 'Skill cannot be deleted.';
	                   $messages['msgtype'] = 'error';
	                }
			  }else{
				  	   $messages['message'] = 'Skill cannot be deleted as its using in appraisal process.';
	                   $messages['msgtype'] = 'error';
				  }
			}
			else
			{ 
			 $messages['message'] = 'Skill cannot be deleted.';
			 $messages['msgtype'] = 'error';
			}
			$this->_helper->json($messages);		
	}
	
}

