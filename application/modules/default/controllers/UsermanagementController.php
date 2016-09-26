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

class Default_UsermanagementController extends Zend_Controller_Action
{

    private $_options;
    public function preDispatch()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('getmailofuser', 'json')->initContext();
    }
	
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();		
    }
    public static function getTableName()
    {
        $model = new Default_Model_Usermanagement();
        return $model->getTableName();
    }
    public function indexAction()
    {
        $user_model = new Default_Model_Usermanagement();	
        $call = $this->_getParam('call');
        if($call == 'ajaxcall')
            $this->_helper->layout->disableLayout();
		
        $view = Zend_Layout::getMvcInstance()->getView();		
        $objname = $this->_getParam('objname');
        $refresh = $this->_getParam('refresh');
        $dashboardcall = $this->_getParam('dashboardcall');
        $data = array();

        if($refresh == 'refresh')
        {
            if($dashboardcall == 'Yes')
                $perPage = DASHBOARD_PERPAGE;
            else	
                $perPage = PERPAGE;
            $sort = 'DESC';$by = 'u.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';
            $searchArray = array();
        }
        else 
        {
            $sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
            $by = ($this->_getParam('by')!='')? $this->_getParam('by'):'u.modifieddate';
            if($dashboardcall == 'Yes')
                $perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
            else 
                $perPage = $this->_getParam('per_page',PERPAGE);
            $pageNo = $this->_getParam('page', 1);            
            $searchQuery = '';
            $searchArray = array();
            $searchData = $this->_getParam('searchData');	         
        }

        $dataTmp = $user_model->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,'','','','');			
        array_push($data,$dataTmp);
        $this->view->dataArray = $dataTmp;
        $this->view->call = $call ;
    }

    public function viewAction()
    {	       
        $user_model = new Default_Model_Usermanagement();
        $role_model = new Default_Model_Roles();
        $candidate_model = new Default_Model_Candidatedetails();
        $identity_code_model = new Default_Model_Identitycodes();
        $identity_codes = $identity_code_model->getIdentitycodesRecord();
        $identity_codes = $identity_codes[0];
        $id = (int)$this->getRequest()->getParam('id');
        $id = abs($id);
        $callval = $this->getRequest()->getParam('call');
        if($callval == 'ajaxcall')
            $this->_helper->layout->disableLayout();
        $objName = 'usermanagement';
        $form = new Default_Form_Usermanagement();        
        $form->removeElement("submit");
        
        $elements = $form->getElements();
        if(count($elements)>0)
        {
            foreach($elements as $key=>$element)
            {
                if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments"))
                {
                    $element->setAttrib("disabled", "disabled");
                }
            }
        }
     	if($id && $id>0 && is_numeric($id))
        {
            try
            {
                $data = $user_model->getUserDataById($id);
                if(count($data)>0)
                {
                    if($data['emprole'] != 1)
                    {
                        $role_data = $role_model->getRoleDataById($data['emprole']);
                        $roles_arr = $role_model->getRolesListForUsers($id);        
                        $form->emprole->addMultiOptions(array(''=>'Select Role')+$roles_arr);

                        $data['emplockeddate'] = sapp_Global::change_date($data['emplockeddate'], 'view');

                        $form->populate($data);
                        $identity_arr = array(
                                $identity_codes['backgroundagency_code'] => "Background Agency (".$identity_codes['backgroundagency_code'].")",
                                $identity_codes['users_code'] => "Users (".$identity_codes['users_code'].")",
                              
                            );
                        //$id_arr = preg_split('/-/', $data['employeeId']);
                        $id_arr = preg_split('/(?=\d)/', $data['employeeId'], 2);
                        
                    if(!empty($data['emprole'])) {
						$rolename = $role_model->getRoleDataById($data['emprole']);
						
						if(!empty($rolename)){
							$data['emprole'] = $rolename['rolename'];
							
						}
					}
					
                     
					
					
                        $form->employeeId->setValue($identity_arr[$id_arr[0]]);        
                        $this->view->controllername = $objName;
                        $this->view->id = $id;
                        $this->view->form = $form; 
                        $this->view->data = $data;
                    }
                    else 
                    {
                        $this->view->nodata = "nodata";
                    }
                }
                else 
                {
                    $this->view->nodata = "nodata";
                }
            }
            catch(Exception $e)
            {
                $this->view->nodata = "nodata";
            }
        }
        else 
        {
            $this->view->nodata = "nodata";
        }
    }
	
    /**
     * This action is used for adding/updating data.
     * @parameters
     * @param $id  =  id of users (optional)
     * 
     * @return Zend_Form.
     */
    public function editAction()
    {    
        $popConfigPermission = array();
        $user_model = new Default_Model_Usermanagement();
        $role_model = new Default_Model_Roles();        
        $identity_code_model = new Default_Model_Identitycodes();
        $identity_codes = $identity_code_model->getIdentitycodesRecord();
        $identity_codes = isset($identity_codes[0])?$identity_codes[0]:array();
        $id = $this->getRequest()->getParam('id',null);
        
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $login_group_id = $auth->getStorage()->read()->group_id;
            $login_role_id = $auth->getStorage()->read()->emprole;
        }
        
        if(sapp_Global::_checkprivileges(IDENTITYCODES,$login_group_id,$login_role_id,'edit') == 'Yes'){
			array_push($popConfigPermission,'identitycodes');
		}
        $this->view->popConfigPermission = $popConfigPermission;
        $callval = $this->getRequest()->getParam('call');
        if($callval == 'ajaxcall')
            $this->_helper->layout->disableLayout();

        $form = new Default_Form_Usermanagement();
        $err_messages = array();                        
        $roles_arr = $role_model->getRolesListForUsers('');        
      
        $form->emprole->addMultiOptions(array(''=>'Select Role')+$roles_arr);
        $emp_identity_code = isset($identity_codes['backgroundagency_code'])?$identity_codes['backgroundagency_code']:"";
        
        $identity_arr = array();
        if($emp_identity_code!='')
        {            
            $identity_arr = array(               
                $identity_codes['users_code'] => "Users(".$identity_codes['users_code'].")",
            );
        }
        else 
            $emp_id = '';
        
        $form->employeeId->addMultiOptions($identity_arr);
        if($id!=''  && $id>0)
        {
            try{
                $id = (int)$id;
                $id = abs($id);
            $form->submit->setLabel('Update');   
            
            $data = $user_model->getUserDataById($id); 
            if(count($data) >0)
            {
                if($data['jobtitle_id'] != ''){
                  $this->_redirect('/employee/edit/id/'.$id);
                }
                
                $role_data = $role_model->getRoleDataById($data['emprole']);   

                $data['emplockeddate'] = sapp_Global::change_date($data['emplockeddate'], 'view');
                $form->populate($data);
                $this->view->data = $data;
                //$id_arr = preg_split('/-/', $data['employeeId']);
                $id_arr = preg_split('/(?=\d)/', $data['employeeId'], 2);
                $identity_arr[$identity_codes['backgroundagency_code']] = "Background Agency (".$identity_codes['backgroundagency_code'].")";
                if(isset($identity_arr[$id_arr[0]]) && !empty($identity_arr[$id_arr[0]])){
                 $empIDSetVal = $identity_arr[$id_arr[0]];
                }else{
                 $empIDSetVal = '';
                }
                $form->employeeId->setValue($empIDSetVal); 
				
				if($role_data['group_id'] == USERS_GROUP)
                {
                    $form->emprole->clearMultiOptions();
                    $roles_arr = $role_model->getRolesListForUsers($id,$empIDSetVal);        
                    $form->emprole->addMultiOptions(array(''=>'Select Role')+$roles_arr);
                }
            }
            else 
            {
               $this->view->nodata = "nodata";
            }
            }
            catch(Exception $e)
            { 
                  $this->view->nodata = "nodata";
            }
            
        }
        else if($id == '')
        {
            
        }
        else 
        {
            echo $this->view->nodata = "nodata";
        }
        if($id == '')
        {
            if($emp_identity_code == '')
            {
                $err_messages['employeeId'] = "Identity codes are not configured yet.";
            }
            if(count($roles_arr) == 0)
            {
                
                $err_messages['emprole'] = "Roles are not added yet.";
            }            
        }
        $this->view->messages = $err_messages;
        $this->view->form = $form;                                                
    }
    /**
     * This function is used to add/update data in database.
     * @param  $user_form =    all form data.
     * 
     * @return        JSON  success/error messages in json format.
     */
    public function saveupdateAction()
    {
         
         $auth = Zend_Auth::getInstance();
         if($auth->hasIdentity())
         {
             $loginUserId = $auth->getStorage()->read()->id;
         }
         $agencylistmodel = new Default_Model_Agencylist();
         $user_form = new Default_Form_Usermanagement();
         $user_model = new Default_Model_Usermanagement();    
         $logmanagermodel = new Default_Model_Logmanager();    
         $menumodel = new Default_Model_Menu();        
         $messages = $user_form->getMessages();
         $actionflag = '';
         $tableid  = '';$agencyuser = 'no';
		
        if($this->getRequest()->getPost())
        {
            if($user_form->isValid($this->_request->getPost()))
            {
                $id = $this->_request->getParam('id');                
                $employeeId = $this->_request->getParam('employeeId',null);                                
                //$userfullname = $this->_request->getParam('userfullname',null);
                $firstname = $this->_request->getParam('firstname',null);
                $lastname = $this->_request->getParam('lastname',null);
                $userfullname = $firstname.' '.$lastname;
                $entrycomments = $this->_request->getParam("entrycomments",null);                
                $emailaddress = $this->_request->getParam("emailaddress",null);                
                $emprole = $this->_request->getParam("emprole",null);
                $emplockeddate = $this->_request->getParam("emplockeddate",null);
                $act_inact = $this->_request->getParam("act_inact",null);
                $empreasonlocked = $this->_request->getParam("empreasonlocked",null);                                                                                
                $emppassword = sapp_Global::generatePassword();
                
               
                $data = array(
                            'emprole' => $emprole,
                			'firstname' => $firstname,
                			'lastname' => $lastname,
                            'userfullname' => $userfullname,
                            'emailaddress' => $emailaddress,                            
                            'modifiedby'=>$loginUserId,
                            'modifieddate'=> gmdate("Y-m-d H:i:s"),
                            'emppassword' => md5($emppassword),
                            'entrycomments' => $entrycomments,                            
                            'userstatus' => 'old',
                        );
                        
                if($emplockeddate == '')
                {
                    unset($data['emplockeddate']);
                }
                if($id!='')
                {
                    if($act_inact != '')
                    {
                        
                        $data['isactive'] = $act_inact;
                        $data['emptemplock'] = ($act_inact==0?"1":"0");
						
						$agencyroles = $agencylistmodel->getagencyrole();
						$userData = $user_model->getUserDataById($id);			
                                                $agencyuser = '';
						$user_role = $userData['emprole'];
						foreach($agencyroles as $agrole)
						{
							if($agrole['id'] == $user_role)
							{
								$agencyuser = 'yes';
							}
						}
                                                
						if($agencyuser == 'yes')
						{
							$agencyData = $user_model->getAgencyData($id);
                                                        
							if($act_inact == '1')
							{								
								$user_model->activateAllagencydetails($agencyData['agencyid'],$loginUserId);
								if($agencyData['isactive'] != $act_inact)
								$this->sendEMails($agencyData,'activated');
							}else{								
								$user_model->deleteAllagencydetails($agencyData['agencyid'],$loginUserId);
								if($agencyData['isactive'] != $act_inact)
								$this->sendEMails($agencyData,'inactivated');
							}
						}
                    }
                    $where = array('id=?'=>$id);  
                    unset($data['emppassword']);
                    $messages['message'] = 'User updated successfully.';
                    $_SESSION['usermanagement_msg'] = $messages['message'];                    
                    $actionflag = 2;
                }
                else
                {
                    $data['createdby'] = $loginUserId;
                    $data['createddate'] = gmdate("Y-m-d H:i:s");
                    $data['isactive'] = 1;
                    $where = '';                                                          
                    $messages['message']='User added successfully.';
                    $_SESSION['usermanagement_msg'] = $messages['message'];
                    $actionflag = 1;
                }
		
              $Id = $user_model->SaveorUpdateUserData($data, $where);
                
                
                if($Id == 'update')
                {    
                    $tableid = $id;                    
                }
                else
                {
                    $employeeId = $employeeId.str_pad($Id, 4, '0', STR_PAD_LEFT);
                
                   
                    $user_model->SaveorUpdateUserData(array('employeeId'=>$employeeId), "id = ".$Id);
                    $tableid = $Id; 
                    $base_url = 'http://'.$this->getRequest()->getHttpHost() . $this->getRequest()->getBaseUrl();
                    $view = $this->getHelper('ViewRenderer')->view;
                    $this->view->emp_name = $userfullname;
                    $this->view->password = $emppassword;
                    $this->view->emp_id = $employeeId;
                    $this->view->base_url=$base_url;
                    $text = $view->render('mailtemplates/newpassword.phtml');
                   
                    $options['subject'] = APPLICATION_NAME.' login credentials';
                   
                    $options['header'] = 'Greetings from Sentrifugo';
                    $options['toEmail'] = $emailaddress;  
                    $options['toName'] = $this->view->emp_name;
                    $options['message'] = $text;
                            
                    try 
                    {
                    	
                        $result = sapp_Global::_sendEmail($options);  
                       
                    }
                    catch(Exception $e) 
                    { 
                        echo $e->getMessage();                    
                    }
                }
      
                $objidArr = $menumodel->getMenuObjID('/usermanagement');
          
                $objID = $objidArr[0]['id'];
                $result = sapp_Global::logManager($objID,$actionflag,$loginUserId,$tableid);
                
           
                if($act_inact != '')
                {
                    if($data['isactive'] == 1)
                    {
                        $logarr = array('userid' => $loginUserId,
											'recordid' =>$tableid,
											'date' => gmdate("Y-m-d H:i:s"),
                                             'isactive' => 1
						);
						$jsonlogarr = json_encode($logarr);
                    }
                    else 
                    {
                        $logarr = array('userid' => $loginUserId,
											'recordid' =>$tableid,
											'date' => gmdate("Y-m-d H:i:s"),
                                             'isactive' => 0
						);
						$jsonlogarr = json_encode($logarr);
                    }
                    $id = $logmanagermodel->addOrUpdateLogManager($objID,4,$jsonlogarr,$loginUserId,$tableid);
                }
                $messages['result']='saved';
                $this->_helper->json($messages);			
            }
            else
            {
                $messages = $user_form->getMessages();
                $messages['result']='error';
                $this->_helper->json($messages);	    	
            }
        }           
    }

    /**
     * This action is used to delete roles and their child data.
     * @parameters
     * @param objid    =   id of role.
     * 
     * @return String    success/failure message 
     */
    public function deleteAction()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
        }
        $baseUrl = $this->generateBaseurl();
        $id = $this->_request->getParam('objid');
        $messages['message'] = '';
        $actionflag = 3;$agencyuser = 'no';$Id = '';
        if($id)
        {
            $user_model = new Default_Model_Usermanagement();          
            $menumodel = new Default_Model_Menu();
			$agencylistmodel = new Default_Model_Agencylist();
            
			$agencyroles = $agencylistmodel->getagencyrole();
			$userData = $user_model->getUserDataById($id);			
			$user_role = $userData['emprole'];
			foreach($agencyroles as $agrole)
			{
				if($agrole['id'] == $user_role)
				{
					$agencyuser = 'yes';
				}
			}			
			$data = array(
						'isactive' => 0,
						'modifiedby' => $loginUserId,
						'modifieddate' => gmdate("Y-m-d H:i:s"),
				);
			$where = array('id=?'=>$id);
			$Id = $user_model->SaveorUpdateUserData($data, $where);
			
			if($agencyuser == 'yes')
			{
				$agencyData = $user_model->getAgencyData($id);
				$user_model->deleteAllagencydetails($agencyData['agencyid'],$loginUserId);
				$emailids = $agencylistmodel->getAllHRManagementEMails();				
				$this->sendEMails($agencyData,'inactivated');
			}
			
            if($Id == 'update')
            {                
                $objidArr = $menumodel->getMenuObjID('/roles');
                $objID = $objidArr[0]['id'];                    
                $result = sapp_Global::logManager($objID,$actionflag,$loginUserId,$id);  
                $messages['message'] = 'User deleted successfully';
                $messages['msgtype'] = 'success';
            }   
            else
            {
               $messages['message'] = 'User cannot be deleted';	
               $messages['msgtype'] = 'error';
            }
        }
        else
        { 
            $messages['message'] = 'User cannot be deleted';
            $messages['msgtype'] = 'error';
        }
        $this->_helper->json($messages);
    }
    public function getemailofuserAction()
    {
        $cand_id = $this->_getParam('cand_id',null);
        $cand_model = new Default_Model_Candidatedetails();
        $cand_data = $cand_model->getCandidateById($cand_id);
        
        $this->_helper->_json(array('email'=>$cand_data['emailid']));
    }
	
	
	public function generateBaseurl()
	{
		$baseUrl = DOMAIN;
		$baseUrl = rtrim($baseUrl,'/');	
		return $baseUrl;
	}
	
	public function sendEMails($agencyData,$flag)
	{
		$baseUrl =  $this->generateBaseurl();
		$agencylistmodel = new Default_Model_Agencylist();
		if($flag == 'activated')
		{
			$emailids = $agencylistmodel->getAllHRManagementEMails();			
			foreach($emailids as $email)
			{
				$options['subject'] = APPLICATION_NAME.' : Agency activated';
				$options['header'] = 'Agency activated';
				$options['toEmail'] = $email['groupEmail'];  
				if($email['group_id'] == 4) {
					$salutation = 'Dear HR,';
					$options['toName'] = 'HR';  
				}
				else{
					$salutation = 'Dear Management,';
					$options['toName'] = 'Management';  
				}
				$options['message'] = '<div>
										<div>'.$salutation.'</div>
										<div></div>	
										'.$agencyData['userfullname'].' agency has been activated. 
										<div></div>											
										<div style="padding:20px 0 10px 0;">Please <a href="'.$baseUrl.'/index/popup" target="_blank" style="color:#b3512f;">click here</a> to login  to your Sentrifugo account.</div>
										</div>';	
				$options['cron'] = 'yes';
				$result = sapp_Global::_sendEmail($options);
			}
			$options['subject'] = APPLICATION_NAME.' : Agency Activated';
			$options['header'] = 'Agency Activated';
			$options['toEmail'] = $agencyData['emailaddress'];  
			$options['toName'] = $agencyData['userfullname'];  
			$options['message'] = '<div>
									<div>Dear '.$agencyData['userfullname'].',</div>
									<div></div>	
									Your agency has been activated. For further details, please contact our HR directly.
									<div></div>	
									<div style="padding:20px 0 10px 0;">Please <a href="'.$baseUrl.'/index/popup" target="_blank" style="color:#b3512f;">click here</a> to login  to your Sentrifugo account.</div>																			
									</div>';	
			$options['cron'] = 'yes';
			$result = sapp_Global::_sendEmail($options);
		}
		else if($flag == 'inactivated')
		{
			$emailids = $agencylistmodel->getAllHRManagementEMails();			
			foreach($emailids as $email)
			{
				$options['subject'] = APPLICATION_NAME.' : Agency is deleted';
				$options['header'] = 'Agency deleted';
				$options['toEmail'] = $email['groupEmail'];  
				if($email['group_id'] == 4) {
					$salutation = 'Dear HR,';
					$options['toName'] = 'HR';  
				}
				else{
					$salutation = 'Dear Management,';
					$options['toName'] = 'Management';  
				}
				$options['message'] = '<div>
										<div>'.$salutation.' </div>
										<div></div>	
										'.$agencyData['userfullname'].' agency has been deleted. 
										<div></div>
										<div style="padding:20px 0 10px 0;">Please <a href="'.$baseUrl.'/index/popup" target="_blank" style="color:#b3512f;">click here</a> to login  to your Sentrifugo account.</div>											
										</div>';	
				$options['cron'] = 'yes';
				$result = sapp_Global::_sendEmail($options);
			}
			$options['subject'] = APPLICATION_NAME.' :: Agency is deleted';
			$options['header'] = 'Agency Deleted';
			$options['toEmail'] = $agencyData['emailaddress'];  
			$options['toName'] = $agencyData['userfullname'];  
			$options['message'] = '<div>
									<div>Dear '.$agencyData['userfullname'].',</div>
									<div></div>	
									We regret to inform you that your agency has been deleted.
									<div></div>											
									</div>';	
			$options['cron'] = 'yes';
			$result = sapp_Global::_sendEmail($options);
		}
	}
}

