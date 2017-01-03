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
		$appraisalSkillsForm->setAttrib('action',BASE_URL.'appraisalskills/add');
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
		$objName = 'appraisalskills';
		
		try
		{
		    if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					$appraisalSkillsModel = new Default_Model_Appraisalskills();	
					$data = $appraisalSkillsModel->getAppraisalSkillsDatabyID($id);
					$previ_data = sapp_Global::_checkprivileges(APPRAISALSKILLS,$login_group_id,$login_role_id,'edit');
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
						$appraisalSkillsForm->setAttrib('action',BASE_URL.'appraisalskills/edit/id/'.$id);
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
				$menuID = APPRAISALSKILLS;
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
			  $appSkillsdata = $appraisalSkillsModel->getAppraisalSkillsDatabyID($id);
			  
			  if($appSkillsdata[0]['isused'] == 0)	
			  {
				  $data = array('isactive'=>0, 'modifiedby'=>$loginUserId, 'modifiedby_role'=>$loginuserRole,
								   'modifiedby_group'=>$loginuserGroup, 'modifieddate'=>gmdate("Y-m-d H:i:s"));
				  $where = array('id=?'=>$id);
				  $Id = $appraisalSkillsModel->SaveorUpdateAppraisalSkillsData($data, $where);
				    if($Id == 'update')
					{
						$menuID = APPRAISALSKILLS;
						$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
						/***
						** commented on 29-04-2015 by sapplica
						** need to integrate mail template						   
							$configmail = sapp_Global::send_configuration_mail('Skill',$appSkillsdata[0]['skill_name']);	
						***/						   
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
	
	public function getappraisalskillsAction()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('getappraisalskills', 'json')->initContext();
		$this->_helper->layout->disableLayout();
		$appraisalSkillsModel = new Default_Model_Appraisalskills();
		$skillsval = $this->_request->getParam('skillsval');
		$result['result'] = 'success';
		$result['data'] = "<option value=''>Select Skills</option>";
		$categoryids='';
		$appraisalid = $this->_request->getParam('appraisalid');
		$skillsdata = $appraisalSkillsModel->getselectedAppraisalSkillsData($skillsval);
		if(!empty($skillsdata))
		{
				foreach($skillsdata as $data)
				{
					$result['data'].="<option value=".$data['id'].">".utf8_encode($data['skill_name'])."</option>"; 
				}
		
		}else if(!empty($skillsval) && empty($skillsdata))
		{
			$result['result'] = 'error';
			$result['data']="No more skills to add.";
		}else
		{
			$result['result'] = 'error';
			$result['data']="Skills are not configured yet.";
		}
		$this->_helper->json($result);
	}
	
	public function saveskillspopupAction()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('saveskillspopup', 'json')->initContext();
		$this->_helper->layout->disableLayout();
	  	$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
				 $loginUserId = $auth->getStorage()->read()->id;
				 $loginuserRole = $auth->getStorage()->read()->emprole;
				 $loginuserGroup = $auth->getStorage()->read()->group_id;
		} 
	    $appraisalskillsmodel = new Default_Model_Appraisalskills();
		$result['result'] = '';
		$result['id'] = '';
		$result['skills'] = '';
		$duplicateSkillName = 0;
		
            try{
            $skillsval = rawurldecode($this->_request->getParam('skillsval'));
			$description = rawurldecode(trim($this->_request->getParam('description')));
			$actionflag = '';
			$tableid  = ''; 
			  if($skillsval)
			     $CheckDuplicateSkillName = $appraisalskillsmodel->getDuplicateSkillsName($skillsval);
              if(!empty($CheckDuplicateSkillName))	
				 $duplicateSkillName = $CheckDuplicateSkillName[0]['grpcnt'];
					
					if($duplicateSkillName > 0)
					{
						$result['msg'] = 'Skill name already exists.';
						
					}else
					{
				
					   		$data = array('skill_name'=>$skillsval,
									 'description'=>($description!=''?$description:NULL),			   
					   				 'createdby_role'=>$loginuserRole,
									 'createdby_group'=>$loginuserGroup,			   
									 'createdby'=>$loginUserId,
									 'modifiedby_role'=>$loginuserRole,
									 'modifiedby_group'=>$loginuserGroup,			   
									 'modifiedby'=>$loginUserId,
					   				 'isactive'=>1,
					   				 'createddate'=>gmdate("Y-m-d H:i:s"),
									 'modifieddate'=>gmdate("Y-m-d H:i:s")
							);
						
							$where = '';
							$actionflag = 1;
							if($skillsval!='')
							{
								$Id = $appraisalskillsmodel->SaveorUpdateAppraisalSkillsData($data, $where);
								$menuID = APPRAISALSKILLS;
								$logresult = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
								
								$result['msg'] = 'success';
								$result['id'] = $Id;
								$result['skills'] = $skillsval;
							}else
							{
								$result['msg'] = 'error';
					            $result['id'] = '';
								$result['skills'] = '';
							}
					}
                  }
        catch(Exception $e)
          {
             $result['msg'] = $e->getMessage();
             $result['id'] = '';
			 $result['skills'] = '';
          }
          
          $this->_helper->json($result);
		
	
	}
	
}

