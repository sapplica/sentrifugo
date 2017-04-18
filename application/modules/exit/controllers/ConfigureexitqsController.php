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

class Exit_ConfigureexitqsController extends Zend_Controller_Action
{

	private $options;

	public function preDispatch()
	{
	}
	public function init()
	{
		$this->_options = $this->getInvokeArg('bootstrap')->getOptions();
		
	}
	public function indexAction()
	{
		$exitquestionsModel = new Exit_Model_Exitquestions();	
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
			$sort = 'DESC';$by = 'eq.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else 
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'eq.modifieddate';
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
				
		$dataTmp = $exitquestionsModel->getGrid($sort, $by, $perPage, $pageNo, $searchData, $call, $dashboardcall);		 		
					
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
		
	}
	
	
	/**
	**	this action is used to display add form
	**  if post values are available will create new exit type
	**	@parameters 
	**		string exit type (mandatory)
	**
	**	@return parameters
	**		@object success/failure messages		
	**/
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
			
			$exitquestionsform = new Exit_Form_Exitquestions();
			$exittypesModel = new Exit_Model_Exittypes();
			$msgarray = array();
			$popConfigPermission = array();
			
			if(sapp_Global::_checkprivileges(EXIT_QUESTIONS,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
				array_push($popConfigPermission,'exitquestions');
			}

			if(sapp_Global::_checkprivileges(EXIT_PROC_TYPES,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
					array_push($popConfigPermission,'exittypes');
			}
			
			$exittypesData = $exittypesModel->getExittypesData();
			
				if(sizeof($exittypesData ) > 0)
				{ 			
					foreach ($exittypesData  as $ac){
						$exitquestionsform->exit_type_id->addMultiOption($ac['id'],utf8_encode($ac['exit_type']));
					}
				}
				else
				{
					$msgarray['exit_type_id'] = 'Exit types are not configured yet.';
					$this->view->configuremsg = 'notconfigurable';
				}
			$this->view->popConfigPermission = $popConfigPermission;
			$exitquestionsform->setAttrib('action',BASE_URL.'exit/configureexitqs/add');
			$this->view->form = $exitquestionsform; 
			$this->view->msgarray = $msgarray; 
			$this->view->ermsg = '';
				if($this->getRequest()->getPost()){
					 $result = $this->add($exitquestionsform);	
					 $this->view->msgarray = $result; 
				}  		
		
	}
	
	
	// edit exit questions data,if it in use it can not be edited.
	
	public function editAction()
	{	

	    $auth = Zend_Auth::getInstance();
            if($auth->hasIdentity())
            {
                $loginUserId = $auth->getStorage()->read()->id;
                $loginuserRole = $auth->getStorage()->read()->emprole;
                $loginuserGroup = $auth->getStorage()->read()->group_id;
            }
	 	
            $id = $this->getRequest()->getParam('id');
            $callval = $this->getRequest()->getParam('call');
            if($callval == 'ajaxcall')
                $this->_helper->layout->disableLayout();
		
		$exitquestionsform = new Exit_Form_Exitquestions();
		$exittypesModel = new Exit_Model_Exittypes();
		$exitquestionsModel = new Exit_Model_Exitquestions();
		$msgarray = array();
		$popConfigPermission = array();
		
		if(sapp_Global::_checkprivileges(EXIT_QUESTIONS,$loginuserGroup,$loginuserRole,'edit') == 'Yes'){
	 		array_push($popConfigPermission,'exitquestions');
	 	}
		$exitquestionsform->submit->setLabel('Update');
		
		try
        {		
			if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					$data = $exitquestionsModel->getExitQuestionbyID($id);
					if(!empty($data))
					{
						$data = $data[0];
						  
						// When question is already question details can't be edited
						if ($data['isused'] == 1) {
							$this->view->ermsg = 'norecord';
						} else {
							$exittypesData = $exittypesModel->getExittypesData();
					 		if(sizeof($exittypesData) > 0)
				            { 			
								foreach ($exittypesData as $ac){
									$exitquestionsform->exit_type_id->addMultiOption($ac['id'],utf8_encode($ac['exit_type']));
								}
							}  
							$exitquestionsform->populate($data);
							$exitquestionsform->setDefault('exit_type_id',$data['exit_type_id']);
							$exitquestionsform->setAttrib('action',BASE_URL.'exit/configureexitqs/edit/id/'.$id);
	                        $this->view->data = $data;							
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
		$this->view->form = $exitquestionsform;
		if($this->getRequest()->getPost()){
      		$result = $this->save($exitquestionsform);	
		    $this->view->msgarray = $result; 
		}
		$this->render('form');	
	}
	
	// save exit questions
	
	public function save($exitquestionsform)
	{
	  $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
				 $loginUserId = $auth->getStorage()->read()->id;
				 $loginuserRole = $auth->getStorage()->read()->emprole;
				 $loginuserGroup = $auth->getStorage()->read()->group_id;
		} 
		$exitquestionsModel = new Exit_Model_Exitquestions();
		$msgarray = array();
		  if($exitquestionsform->isValid($this->_request->getPost())){
       try{
            $id = $this->_request->getParam('id');
            $exit_type_id = $this->_request->getParam('exit_type_id');
            $question = trim($this->_request->getParam('question'));	
			$description = trim($this->_request->getParam('description'));
			$actionflag = '';
			$tableid  = ''; 
			   $data = array('exit_type_id'=>$exit_type_id,
			                 'question'=>$question, 
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
				$Id = $exitquestionsModel->SaveorUpdateExitQuestionData($data, $where);
				if($Id == 'update')
				{
				   $tableid = $id;
				   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Question updated successfully."));
				}   
				else
				{
				   $tableid = $Id; 	
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Questions added successfully."));					   
				}   
				$menuID = EXIT_QUESTIONS;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
				$this->_redirect('exit/configureexitqs');	
        }
		catch(Exception $e)
        {
             $msgarray['exit_type_id'] = "Something went wrong, please try again later.";
             return $msgarray;
        }
		}else
		{
			$messages = $exitquestionsform->getMessages();
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
	
	public function add($exitquestionsform)
	{
	  $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
				 $loginUserId = $auth->getStorage()->read()->id;
				  $loginuserRole = $auth->getStorage()->read()->emprole;
				 $loginuserGroup = $auth->getStorage()->read()->group_id;
		} 
	    $exitquestionsmodel = new Exit_Model_Exitquestions();	
		$msgarray = array();
		$errorflag = 'true';
		$exit_type_id = $this->_request->getParam('exit_type_id');
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
				} 
			}
			$msgarray['requestsize'] = sizeof($question_arr);
		}	
		
		  if($exitquestionsform->isValid($this->_request->getPost()) && $errorflag == 'true'){
            try{
            $id = $this->_request->getParam('id');
            $exit_type_id = $this->_request->getParam('exit_type_id');
			$actionflag = 1;
			$tableid  = ''; 
			$where = '';
			for($i=0;$i<sizeof($question_arr);$i++)
			{
			   $data = array('exit_type_id'=>$exit_type_id,
			                 'question'=>trim($question_arr[$i]), 
							 'description'=>($description_arr[$i]!=''?trim($description_arr[$i]):NULL),
							  'createdby' => $loginUserId,
							  'modifiedby'=>$loginUserId,
							  'modifieddate'=>gmdate("Y-m-d H:i:s"),
							  'createddate'=> gmdate("Y-m-d H:i:s"),
							  'isactive'=> 1
					);
				// save exit type questions 
				$Id = $exitquestionsmodel->SaveorUpdateExitQuestionData($data, $where);
				$tableid = $Id; 	
				$menuID = EXIT_QUESTIONS;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
			}
				
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Questions added successfully."));
				$this->_redirect('exit/configureexitqs');	
                  }
        catch(Exception $e)
          {
             $msgarray['exit_type_id'] = "Something went wrong, please try again.";
             return $msgarray;
          }
		}else
		{
			$messages = $exitquestionsform->getMessages();
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
	
	//view for exit questions
	
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
		$objName = 'configureexitqs';
		$exittypesModel = new Exit_Model_Exittypes();
		$exitquestionsmodel = new Exit_Model_Exitquestions();
		try
		{
		    if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					// get questions data based on id
					$data = $exitquestionsmodel->getExitQuestionbyID($id);
					$previ_data = sapp_Global::_checkprivileges(EXIT_QUESTIONS,$login_group_id,$login_role_id,'view');
					
					if(!empty($data))
					{
						$data = $data[0]; 
						$exittypeData = $exittypesModel->getExittypeById($data['exit_type_id']);
						if(sizeof($exittypeData) > 0)
						{
							$this->view->ermsg = ''; 
	                    	$this->view->data = $data;
							$this->view->previ_data = $previ_data;
	                   	 	$this->view->id = $id;
	                    	$this->view->controllername = $objName;
	                    	$exittype_name = $exittypeData[0]['exit_type'];	
	                    	$this->view->exittype_name = $exittype_name;	
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
            else
			{
			   $this->view->ermsg = 'norecord';
			} 	
			$this->view->controllername =	$objName ;					
		}
		catch(Exception $e)
		{
			   $this->view->ermsg = 'nodata';
		}
							
	}
	
	// delete questions if it is not used in exit process
	public function deleteAction()
	{
	     $auth = Zend_Auth::getInstance();
     		if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;
				 	$loginuserGroup = $auth->getStorage()->read()->group_id;
				}
		 $id = $this->_request->getParam('objid');
		 // differentiate grid delete and view page delete based on delete flag
		 $deleteflag= $this->_request->getParam('deleteflag');
		 $messages['message'] = '';
		 $messages['msgtype'] = '';
		 $actionflag = 3;
		    if($id)
			{
				$exitquestionsmodel = new Exit_Model_Exitquestions();
				$questionsdata = $exitquestionsmodel->getExitQuestionbyID($id);
				//if question is in use in exit process it can not be deleted
				if($questionsdata[0]['isused'] == 0)	
				{
					  $data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"),'modifiedby'=>$loginUserId);
					  $where = array('id=?'=>$id);
					  $Id = $exitquestionsmodel->SaveorUpdateExitQuestionData($data, $where);
					    if($Id == 'update')
						{
							$menuID = EXIT_QUESTIONS;
							$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
												   
							$messages['message'] = 'Question deleted successfully.';
							$messages['msgtype'] = 'success';
						}   
						else
						{
		                   $messages['message'] = 'Something went wrong, please try again later.';
		                   $messages['msgtype'] = 'error';
		                }
				  }else
				  {
				  	   $messages['message'] = 'Question cannot be deleted as it is being used in exit process.';
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
	//function for add questions in popup
	public function addpopupAction()
	{
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		
		$isfrompopup = $this->getRequest()->getParam('isfrompopup');
		$exit_type_id = $this->_request->getParam('exit_type_id');
		$exitquestionsform = new Exit_Form_Exitquestions();
		$exitquestionsform->setAction(BASE_URL.'exit/configureexitqs/addpopup');
		$controllername = 'configureexitqs';
		$this->view->controllername =$controllername ;
		$this->view->isfrompopup=$isfrompopup;
		$this->view->form = $exitquestionsform;
		$this->view->exit_type_id = $exit_type_id;
		
		if($this->getRequest()->getPost()){
			$exitquestionsmodel = new Exit_Model_Exitquestions();	
			$msgarray = array();
			$errorflag = 'true';
			$exit_type_id = $this->_request->getParam('exit_type_id');
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
					} 
				}
				$msgarray['requestsize'] = sizeof($question_arr);
			}	
		
		  if($exitquestionsform->isValid($this->_request->getPost()) && $errorflag == 'true'){
            try{
				$id = $this->_request->getParam('id');
				$exit_type_id = $this->_request->getParam('exit_type_id');
				$actionflag = 1;
				$tableid  = ''; 
				$where = '';
				for($i=0;$i<sizeof($question_arr);$i++)
				{
				   $data = array('exit_type_id'=>$exit_type_id,
								 'question'=>trim($question_arr[$i]), 
								 'description'=>($description_arr[$i]!=''?trim($description_arr[$i]):NULL),
								  'createdby' => $loginUserId,
								  'modifiedby'=>$loginUserId,
								  'modifieddate'=>gmdate("Y-m-d H:i:s"),
								  'createddate'=> gmdate("Y-m-d H:i:s"),
								  'isactive'=> 1
						);
					// save exit type questions 
					$Id = $exitquestionsmodel->SaveorUpdateExitQuestionData($data, $where);
					$tableid = $Id; 	
					$menuID = EXIT_QUESTIONS;
					$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
				}
					$questionsArr =$exitquestionsmodel->getExitQuestionsByexitId($exit_type_id);
					
					$this->view->eventact = 'added';
					$close = 'close';
					$this->view->popup=$close; 
				
          }
			catch(Exception $e)
          {
             $msgarray['question'] = "Something went wrong, please try again.";
             return $msgarray;
          }
		  
		}
			else
			{
				$messages = $exitquestionsform->getMessages();
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
	
	
}
?>