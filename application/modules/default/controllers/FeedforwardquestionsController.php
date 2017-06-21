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

class Default_FeedforwardquestionsController extends Zend_Controller_Action
{

    private $options;
	public function preDispatch()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('savepopup', 'json')->initContext();
	}
	
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
    }

    public function indexAction()
    {    
		$feedforwardquestionsmodel = new Default_Model_Feedforwardquestions();	
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
			$sort = 'DESC';$by = 'aq.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else 
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'aq.modifieddate';
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
				
		$dataTmp = $feedforwardquestionsmodel->getGrid($sort, $by, $perPage, $pageNo, $searchData, $call, $dashboardcall);		 		
					
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
		$feedforwardquestionsform = new Default_Form_Feedforwardquestions();
		$msgarray = array();
		$popConfigPermission = array();
		
	 	if(sapp_Global::_checkprivileges(FEEDFORWARDQUESTIONS,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
	 		array_push($popConfigPermission,'feedforwardquestions');
	 	}
	 	
	 	$this->view->popConfigPermission = $popConfigPermission;
		$feedforwardquestionsform->setAttrib('action',BASE_URL.'feedforwardquestions/add');
		$this->view->form = $feedforwardquestionsform; 
		$this->view->msgarray = $msgarray; 
		$this->view->ermsg = '';
		
		if($this->getRequest()->getPost()){
			 $result = $this->add($feedforwardquestionsform);	
			 $this->view->msgarray = $result; 
		}  		
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
		$objName = 'feedforwardquestions';
		$feedforwardquestionsmodel = new Default_Model_Feedforwardquestions();
		try
		{
		    if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					$data = $feedforwardquestionsmodel->getFeedforwardQuestionbyID($id);
					$previ_data = sapp_Global::_checkprivileges(FEEDFORWARDQUESTIONS,$login_group_id,$login_role_id,'edit');
					
					if(!empty($data))
					{
						$data = $data[0]; 
							$this->view->ermsg = ''; 
	                    	$this->view->data = $data;
							$this->view->previ_data = $previ_data;
	                   	 	$this->view->id = $id;
	                    	$this->view->controllername = $objName;
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
		
		$feedforwardquestionsform = new Default_Form_Feedforwardquestions();
		$feedforwardquestionsmodel = new Default_Model_Feedforwardquestions();	
		$msgarray = array();
		$popConfigPermission = array();
		
		if(sapp_Global::_checkprivileges(FEEDFORWARDQUESTIONS,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
	 		array_push($popConfigPermission,'feedforwardquestions');
	 	}
		$feedforwardquestionsform->submit->setLabel('Update');
		
		try
        {		
			if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					$data = $feedforwardquestionsmodel->getFeedforwardQuestionbyID($id);
					
					if(!empty($data))
					{
						$data = $data[0];
						if($data['isused'] != 1)
						{
							$feedforwardquestionsform->populate($data);
							$feedforwardquestionsform->setAttrib('action',BASE_URL.'feedforwardquestions/edit/id/'.$id);
							$this->view->data = $data;
						}
						else
						{
							$this->view->ermsg = 'noedit';
						}
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
		$this->view->form = $feedforwardquestionsform;
		if($this->getRequest()->getPost()){
      		$result = $this->save($feedforwardquestionsform);	
		    $this->view->msgarray = $result; 
		}
		$this->render('form');	
	}
	
	public function add($feedforwardquestionsform)
	{
	  $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
				 $loginUserId = $auth->getStorage()->read()->id;
				  $loginuserRole = $auth->getStorage()->read()->emprole;
				 $loginuserGroup = $auth->getStorage()->read()->group_id;
		} 
	    $feedforwardquestionsmodel = new Default_Model_Feedforwardquestions();	
		$msgarray = array();
		$errorflag = 'true';
		$question_arr = $this->_request->getParam('question');
		$quesArr = array_count_values($question_arr);
		$description_arr = $this->_request->getParam('description');
		if(!empty($question_arr))
		{
			for($i=0;$i<sizeof($question_arr);$i++)
			{
				$quesArr = array_count_values($question_arr);
				if($question_arr[$i] == '') {
					$msgarray['ques_name'][$i] = 'Please enter question.';
					$errorflag = 'false';
				} else if(!preg_match('/^[a-zA-Z0-9.\- ?\',\/#@$&*()!]+$/', $question_arr[$i])) {
					$msgarray['ques_name'][$i] = 'Please enter valid question.';
					$errorflag = 'false';
				} else if($quesArr[$question_arr[$i]] > 1) {
					$msgarray['ques_name'][$i] = 'Question already exists for the department.';
					$errorflag = 'false';
				} 
			}
			$msgarray['requestsize'] = sizeof($question_arr);
		}	
		
		  if($feedforwardquestionsform->isValid($this->_request->getPost()) && $errorflag == 'true'){
            try{
            $id = $this->_request->getParam('id');
			$actionflag = 1;
			$tableid  = ''; 
			$where = '';
			for($i=0;$i<sizeof($question_arr);$i++)
			{
			   $data = array(
			                 'question'=>trim($question_arr[$i]), 
							 'description'=>($description_arr[$i]!=''?trim($description_arr[$i]):NULL),
							  'modifiedby'=>$loginUserId,
			    			  'module_flag'=>2, // 2 = Feed Forward , 1= Performance appraisal
							  'modifieddate'=>gmdate("Y-m-d H:i:s"),
			   				  'modifiedby_role'=>$loginuserRole,
							  'modifiedby_group'=>$loginuserGroup,
			  				  'createdby_role'=> $loginuserRole,
							  'createdby_group'=> $loginuserGroup,
			   				  'createdby' => $loginUserId,
							  'createddate'=> gmdate("Y-m-d H:i:s"),
							  'isactive'=> 1
					);
				
				$Id = $feedforwardquestionsmodel->SaveorUpdateFeedforwardQuestionData($data, $where);
				$tableid = $Id; 	
				$menuID = FEEDFORWARDQUESTIONS;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
			}
				
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Question added successfully."));
				$this->_redirect('feedforwardquestions');	
                  }
        catch(Exception $e)
          {
             $msgarray['pa_category_id'] = "Something went wrong, please try again.";
             return $msgarray;
          }
		}else
		{
			$messages = $feedforwardquestionsform->getMessages();
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
	
	
	public function save($feedforwardquestionsform)
	{
	  $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
				 $loginUserId = $auth->getStorage()->read()->id;
				 $loginuserRole = $auth->getStorage()->read()->emprole;
				 $loginuserGroup = $auth->getStorage()->read()->group_id;
		} 
	    $feedforwardquestionsmodel = new Default_Model_Feedforwardquestions();	
		$msgarray = array();
		  if($feedforwardquestionsform->isValid($this->_request->getPost())){
            try{
            $id = $this->_request->getParam('id');
            $question = trim($this->_request->getParam('question'));	
			$description = trim($this->_request->getParam('description'));
			$actionflag = '';
			$tableid  = ''; 
			   $data = array(
			                 'question'=>$question, 
							 'description'=>($description!=''?$description:NULL),			   
							 'module_flag'=>2, // 2 = Feed Forward , 1= Performance appraisal
							 'modifiedby_role'=>$loginuserRole,
							 'modifiedby_group'=>$loginuserGroup,			   
							 'modifiedby'=>$loginUserId,
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
				$Id = $feedforwardquestionsmodel->SaveorUpdateFeedforwardQuestionData($data, $where);
				if($Id == 'update')
				{
				   $tableid = $id;
				   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Question updated successfully."));
				}   
				else
				{
				   $tableid = $Id; 	
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Question added successfully."));					   
				}   
			    $menuID = FEEDFORWARDQUESTIONS;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
				$this->_redirect('feedforwardquestions');	
                  }
        catch(Exception $e)
          {
          	//echo $getMessage();
          	//echo $getTraceString();
             //$msgarray['pa_category_id'] = "Something went wrong, please try again.";
             return $msgarray;
          }
		}else
		{
			$messages = $feedforwardquestionsform->getMessages();
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
		 $deleteflag= $this->_request->getParam('deleteflag');
		 $messages['message'] = '';
		 $messages['msgtype'] = '';
		 $count = 0;
		 $actionflag = 3;
		    if($id)
			{
				$feedforwardquestionsmodel = new Default_Model_Feedforwardquestions();
				$feedforwardquestionsdata = $feedforwardquestionsmodel->getFeedforwardQuestionbyID($id);
				$quesUsed = $feedforwardquestionsmodel->checkQuestionUsed($id);
				
				if($quesUsed[0]['count'] == 0)	
				{
					  $data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"),'modifiedby_role'=>$loginuserRole,
									 'modifiedby_group'=>$loginuserGroup,'modifiedby'=>$loginUserId);
					  $where = array('id=?'=>$id);
					  $Id = $feedforwardquestionsmodel->SaveorUpdateFeedforwardQuestionData($data, $where);
					    if($Id == 'update')
						{
							$menuID = FEEDFORWARDQUESTIONS;
							$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
							/***
							** commented on 29-04-2015 by sapplica
							** need to integrate mail template								
								$configmail = sapp_Global::send_configuration_mail('Question',$feedforwardquestionsdata[0]['question']);
							***/								
							$messages['message'] = 'Question deleted successfully.';
							$messages['msgtype'] = 'success';
						}   
						else
						{
		                   $messages['message'] = 'Question cannot be deleted.';
		                   $messages['msgtype'] = 'error';
		                }
				  }else
				  {
				  	   $messages['message'] = 'Question cannot be deleted as it is being used in feedforward process.';
	                   $messages['msgtype'] = 'error';
				  } 				   
			}
			else
			{ 
			 $messages['message'] = 'Question cannot be deleted.';
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
	
	public function savepopupAction()
	{
	  $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
				 $loginUserId = $auth->getStorage()->read()->id;
				 $loginuserRole = $auth->getStorage()->read()->emprole;
				 $loginuserGroup = $auth->getStorage()->read()->group_id;
		} 
	    $feedforwardquestionsmodel = new Default_Model_Feedforwardquestions();	
		$msgarray = array();
		$result['msg'] = '';
		$result['id'] = '';
		$result['question'] = '';
		$result['description'] = '';
            try
            {
            $question = trim($this->_request->getParam('question'));	
			$description = trim($this->_request->getParam('description'));
			$actionflag = '';
			   $data = array(
			                 'question'=>$question, 
							 'description'=>($description!=''?$description:NULL),			   
							 'module_flag'=>2, // 2 = Feed Forward , 1= Performance appraisal
							 'modifiedby_role'=>$loginuserRole,
							 'modifiedby_group'=>$loginuserGroup,			   
							 'modifiedby'=>$loginUserId,
							 'modifieddate'=>gmdate("Y-m-d H:i:s")
					);
					$data['createdby_role'] = $loginuserRole;
					$data['createdby_group'] = $loginuserGroup;
					$data['createdby'] = $loginUserId;
					$data['createddate'] = gmdate("Y-m-d H:i:s");
					$data['isactive'] = 1;
					$where = '';
					$actionflag = 1;
				
				$Id = $feedforwardquestionsmodel->SaveorUpdateFeedforwardQuestionData($data, $where);
				$menuID = FEEDFORWARDQUESTIONS;
				sapp_Global::logManager($menuID,$actionflag,$loginUserId,$Id);
				
				$result['msg'] = 'success';
				$result['id'] = $Id;
				$result['question'] = $question;
				$result['description'] = $description;
      	}
        catch(Exception $e)
          {
             $result['msg'] = $e->getMessage();
             $result['id'] = '';
			 $result['question'] = '';
			 $result['description'] = '';
          }
  		$this->_helper->json($result);
	}
}