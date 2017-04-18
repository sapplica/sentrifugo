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

class Exit_ExittypesController extends Zend_Controller_Action
{
	private $exittypesModel;
	private $options;
	private $loggedInUser = '';
	private $loggedInUserGroup = '';
	private $loggedInUserRole = '';
	
	public function preDispatch()
	{
	}
	public function init()
	{
		$this->_options = $this->getInvokeArg('bootstrap')->getOptions();
		
		/** Instantiate exit type model **/
		$this->exittypesModel = new Exit_Model_Exittypes();

		/**
		** Initiating zend auth object
		** for getting logged in user id
		**/
		$auth = Zend_Auth::getInstance();
		
		if($auth->hasIdentity())
		{
			$this->loggedInUser = $auth->getStorage()->read()->id;
			$this->loggedInUserRole = $auth->getStorage()->read()->emprole;
			$this->loggedInUserGroup = $auth->getStorage()->read()->group_id;
		}
	}
	public function indexAction()
	{

		$exittypeModel = new Exit_Model_Exittypes();
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
			$sort = 'DESC';$by = 'et.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else 
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'et.modifieddate';
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
				
		$dataTmp = $exittypeModel->getGrid($sort, $by, $perPage, $pageNo, $searchData, $call, $dashboardcall);		 		
					
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
		
		$msgarray = array();
		$addExittypesForm = new Exit_Form_Exittypes();
		$addExittypesForm->setAttrib('action',BASE_URL.'exit/exittypes/add');
		$this->view->form = $addExittypesForm; 
		$this->view->msgarray = $msgarray; 
		$this->view->ermsg = '';
		$this->view->popuppage = 'no';
		if($this->getRequest()->getPost())
		{
			 if($addExittypesForm->isValid($this->_request->getPost()))
			{ 
				
				/** capture form values **/
				$exitType = $this->_request->getParam('exit_type');
				$description = $this->_request->getParam('description');

				/** prepare data to insert into database **/
				$data = array(
						'exit_type' => $exitType,
						'description' => $description,
						'isused'=> 0, //default value
						'isactive'=> 1, //default value
						'modifiedby'=> $this->loggedInUser,
						'createdby' => $this->loggedInUser,
						'modifieddate'=> gmdate("Y-m-d H:i:s"),
						'createddate'=> gmdate("Y-m-d H:i:s")
				);
				$where = '';
				$res = $this->exittypesModel->SaveorUpdateExitTypesData($data,$where );

				if($res)
				{
					/** insert into log manager table **/
					sapp_Global::logManager(EXIT_PROC_TYPES,1,$this->loggedInUser,$res);
					
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success" => "Exit type added successfully."));
				}
				else
				{
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("error" => "Failed to add new exit type. Please try again."));
				}

				$this->_redirect('exit/exittypes');
			}
			 else
			{
				
				$validationMsgs = $addExittypesForm->getMessages();
				foreach($validationMsgs as $key => $val)
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

	/**
	**	this action is used to display edit form
	**  @input exit type id
	**  if post values are available will edit exit type
	**	@parameters 
	**		string exit type (mandatory)
	**
	**	@return parameteres
	**		@object success/failure messages		
	**/
	public function editAction()
	{
	
		/**
		** check for logged in user role for edit privileges
		**/
		if(sapp_Global::_checkprivileges(EXIT_PROC_TYPES,$this->loggedInUserGroup,$this->loggedInUserRole,'edit') != 'Yes'){
				$this->view->ermsg = 'noprivileges';
				return;
		} 

		/**
		** capture exit type id 
		**/
		$id = (int) $this->_request->getParam('id');
		
		if(is_numeric($id) && $id > 0)
		{
			/**
			** Instantiate exit type form
			**/
			$editExittypeForm = new Exit_Form_Exittypes();
			$this->view->form = $editExittypeForm;

			/**
			** get exit type data based on id
			** populate data to form controls
			**/
			$editExittypeForm->submit->setLabel('Update');
			$res = $this->exittypesModel->getExittypeById($id);
			if(!empty($res))
			{
				
					$data = $res[0];
					  
				  // When exit type is already used category details can't be edited
				  if ($data['isused'] == 1) {
					$this->view->ermsg = 'norecord';
				  } else {
					$editExittypeForm->populate($data);
					$editExittypeForm->setAttrib('action',BASE_URL.'exit/exittypes/edit/id/'.$id);
					$this->view->data = $data;
				  }
				  
				if($this->getRequest()->getPost())
				{
					if($editExittypeForm->isValid($this->_request->getPost()))
					{
						/** capture form values **/
						$exit_type = $this->_request->getParam('exit_type');
						$description = $this->_request->getParam('description');

						/** prepare data object and where clause **/
						$data = array(
								'exit_type' => $exit_type,
								'description' =>$description,
								'isactive'=> 1, //default value
								'modifiedby'=> $this->loggedInUser,
								'modifieddate'=> gmdate("Y-m-d H:i:s"),
								
						);
						$where = array('id=?' => $id);

						/** update exit type details **/
						$res = $this->exittypesModel->SaveorUpdateExitTypesData($data,$where);
						
						if($res)
						{
							/** insert into log manager table **/
							sapp_Global::logManager(EXIT_PROC_TYPES,2,$this->loggedInUser,$id);

							$this->_helper->getHelper("FlashMessenger")->addMessage(array("success" => "Exit type updated successfully."));
						}
						else
						{
							$this->_helper->getHelper("FlashMessenger")->addMessage(array("error" => "Failed to update exit type. Please try again."));
						}

						$this->_redirect('exit/exittypes');
					}
					else
					{
						$validationMsgs = $editExittypeForm->getMessages();
						foreach($validationMsgs as $key => $val)
						{
							foreach($val as $key2 => $val2)
							{
								$msgarray[$key] = $val2;
								break;
							}
						}
						$this->view->msgarray = $msgarray;
					}
					$this->view->ermsg = '';
				}
			}
			else
			{
				$this->view->ermsg = 'nodata';
			}
		}	
		else
		{
			$this->view->ermsg = 'invalidUrl';
		}
	}

	/**
	**	this action is used to display view form
	**  @input exit type id
	**/
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
		$objName = 'exittypes';
		
		try
		{
		    if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					$exittypesModel = new Exit_Model_Exittypes();
					$exitquestionsmodel = new Exit_Model_Exitquestions();
					
					$data = $exittypesModel->getExittypeById($id);
					$previ_data = sapp_Global::_checkprivileges(EXIT_PROC_TYPES,$login_group_id,$login_role_id,'view');

                   
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
				$this->view->controllername = $objName;			
		}
		catch(Exception $e)
		{
			   $this->view->ermsg = 'nodata';
		}	
	}

	/**
	**	this action is used to delete
	**  @input exit type id
	**/
	
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
				$exittypesModel = new Exit_Model_Exittypes();
				$exitquestionsmodel = new Exit_Model_Exitquestions();
				$exittypedata = $exittypesModel->getExittypeById($id);
			
			  if($exittypedata[0]['isused'] == 0)
			  {
				
				$QuesData = $exitquestionsmodel->getExitQuestionsByexitId($id);
				$d=array();
				foreach($QuesData as $key => $value)
				{
 
					$d[] = $value['id'];
				}
				$d=implode(',',$d);
				
				if(sizeof($QuesData) == 0)
				{				
				  $data = array('isactive'=>0,'modifiedby'=>$loginUserId,'modifieddate'=>gmdate("Y-m-d H:i:s"));
				  $where = array('id=?'=>$id);
				  $Id = $exittypesModel->SaveorUpdateExitTypesData($data, $where);
				   
				    if($Id == 'update')
					{
						$menuID = EXIT_PROC_TYPES;
						$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
						/***
						** commented on 29-04-2015 by sapplica
						** need to integrate mail template
							$configmail = sapp_Global::send_configuration_mail('Category',$appCategorydata[0]['category_name']);	
						***/					   
						$messages['message'] = 'Exit Type deleted successfully.';
						$messages['msgtype'] = 'success';
					}   
					else
					{
	                   $messages['message'] = 'Exit Type cannot be deleted.';
	                   $messages['msgtype'] = 'error';
	                }
				} 
				/* else {
					$messages['message'] = 'Exit Type cannot be deleted as there are active questions assigned to it.';
				 	$messages['msgtype'] = 'error';
			  	} */
			  	if(sizeof($QuesData)> 0)
			  	{ 
			  		
			  	 $data = array('isactive'=>0,'modifiedby'=>$loginUserId,'modifieddate'=>gmdate("Y-m-d H:i:s"));
				  $where = array('id=?'=>$id);
				  $Id = $exittypesModel->SaveorUpdateExitTypesData($data, $where);
			  			
			  	  $exitquestionsmodel->UpdateExitQuestionData($d);
			  		if($Id == 'update')
			  		{
			  			$menuID = EXIT_PROC_TYPES;
			  			$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
			  			/***
			  			 ** commented on 29-04-2015 by sapplica
			  			 ** need to integrate mail template
			  			 $configmail = sapp_Global::send_configuration_mail('Category',$appCategorydata[0]['category_name']);
			  			***/
			  			$messages['message'] = 'Exit Type deleted successfully.';
			  			$messages['msgtype'] = 'success';
			  		}
			  		else
			  		{
			  			$messages['message'] = 'Exit Type cannot be deleted.';
			  			$messages['msgtype'] = 'error';
			  		}
			  	}
			  } 
			  else {
				$messages['message'] = 'Exit Type cannot be deleted as it is being used in exit process.';
			 	$messages['msgtype'] = 'error';
			  }
			}
			else
			{ 
			 $messages['message'] = 'Exit Type cannot be deleted.';
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
	
   //add exit types in popup in exit questions add page
	public function addpopupAction()
	{
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->getRequest()->getParam('id');

		$msgarray = array();
		$controllername = 'exittypes';
		$exittypeForm = new Exit_Form_Exittypes();
		$exittypesModel = new Exit_Model_Exittypes();
		$exittypeForm->setAction(BASE_URL.'exit/exittypes/addpopup');
		 if($this->getRequest()->getPost()){
			if($exittypeForm->isValid($this->_request->getPost())){
			$id = $this->_request->getParam('id');
            $exittype_name = $this->_request->getParam('exit_type');	
			$description = $this->_request->getParam('description');
			$actionflag = '';
			$tableid  = ''; 
			   $data = array('exit_type'=>$exittype_name, 
							 'description'=>$description,
							 'modifiedby'=>$loginUserId,
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
				$Id = $exittypesModel->SaveorUpdateExitTypesData($data, $where);
				$tableid = $Id;
				$menuID = EXIT_PROC_TYPES;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);

				$exittypesData = $exittypesModel->getExittypesData();
				$opt ='';
				foreach($exittypesData as $record){
					$opt .= sapp_Global::selectOptionBuilder($record['id'], utf8_encode($record['exit_type']));
				}
				$this->view->exittypeData = $opt;
					
				$this->view->eventact = 'added';
				$close = 'close';
				$this->view->popup=$close;
			}else
			{
				$messages = $exittypeForm->getMessages();
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
		$this->view->form = $exittypeForm;
		$this->view->ermsg = '';
		$this->view->popuppage = 'yes';
		$this->render('add');	
		
	}
	
}
?>