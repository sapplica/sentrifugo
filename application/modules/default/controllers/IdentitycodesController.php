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

class Default_IdentitycodesController extends Zend_Controller_Action
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
    	
        $IdentityCodesModel = new Default_Model_Identitycodes();	

        $identityCodesArr = $IdentityCodesModel->getIdentitycodesRecord();
        if(!empty($identityCodesArr))
         $this->view->dataArray = $identityCodesArr;
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
    }
	
    public function addAction()
    {
    	
        $msgarray = array();
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
        }
        $IdentityCodesform = new Default_Form_identitycodes();
		$IdentityCodesModel = new Default_Model_Identitycodes();	       
        $identityCodesArr = $IdentityCodesModel->getIdentitycodesRecord();	
        $IdentityCodesform->setAttrib('action',BASE_URL.'identitycodes/add');
		/* Removing the codes which are not included for first phase*/
		$IdentityCodesform->removeElement('vendor_code');
		$IdentityCodesform->removeElement('staffing_code');
		
		if(!sapp_Global::_isactivemodule(BGCHECKS))		
		$IdentityCodesform->removeElement('bg_code');
		if(!sapp_Global::_isactivemodule(RESOURCEREQUISITION))
		$IdentityCodesform->removeElement('requisition_code');		
		
		
        $this->view->form = $IdentityCodesform; 	
        $this->view->msgarray = $msgarray;
        if(count($identityCodesArr)>0)
        {
            $this->view->nodata = "nodata";
        }
        if($this->getRequest()->getPost())
        {
            $result = $this->save($IdentityCodesform);	
            $this->view->form = $IdentityCodesform;			 
            $this->view->msgarray = $result; 
        }  		
    }
    /**
     * This action is used to add/edit identity codes in popup
     */
    public function addpopupAction()
    {
        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
        }
		$prev_cntrl = $this->_getParam('prev_cntrl',null);
        $user_id = $this->_getParam('user_id',null);
        $IdentityCodesform = new Default_Form_identitycodes();
        $IdentityCodesform->setAction(BASE_URL.'identitycodes/addpopup/prev_cntrl/'.$prev_cntrl);
        $IdentityCodesModel = new Default_Model_Identitycodes();
        $identity_data = '';
        
		$IdentityCodesform->removeElement('vendor_code');
		$IdentityCodesform->removeElement('staffing_code');
		
        try
        {
            $identityCodesArr = $IdentityCodesModel->getIdentitycodesRecord();
            if(count($identityCodesArr) >0)
                $id = $identityCodesArr[0]['id'];
            else 
                $id = '';
            if($id != '')
            {
                $id = abs($id);	
                $data = $identityCodesArr;
                //print_r($data);exit;
                if(!empty($data))
                {
                    $IdentityCodesform->setDefault("employee_code",$data[0]["employee_code"]);
                    $IdentityCodesform->setDefault("bg_code",$data[0]["backgroundagency_code"]);
                    $IdentityCodesform->setDefault("users_code",$data[0]["users_code"]);
                    $IdentityCodesform->setDefault("requisition_code",$data[0]["requisition_code"]);
                    $IdentityCodesform->setDefault("id",$data[0]["id"]);
                    $IdentityCodesform->submit->setLabel('Update');
                    $this->view->id = $id;
                    $this->view->nodata = '';
                }
                else
                {
                    $this->view->nodata = 'norecord';
                }				
            }
            else
            {
                $IdentityCodesform->submit->setAttrib('value','Save');
            }
                        
        }
        catch(Exception $e)
        {
            $this->view->nodata = 'nodata';
        }		
		if(!sapp_Global::_isactivemodule(BGCHECKS))		
		$IdentityCodesform->removeElement('bg_code');
		if(!sapp_Global::_isactivemodule(RESOURCEREQUISITION))
		$IdentityCodesform->removeElement('requisition_code');
        $this->view->form = $IdentityCodesform;
		
        if($this->getRequest()->getPost())
        {
            $result = $this->save($IdentityCodesform,'from popup');	
            $this->view->msgarray = $result; 
            if(isset($result['msg']) && $result['msg'] != '')
            {
                if($prev_cntrl == 'usermanagement')
                {
                    $identityCodesArr = $IdentityCodesModel->getIdentitycodesRecord();
                    $identityCodesArr = $identityCodesArr[0];
                    $identity_data .= sapp_Global::selectOptionBuilder($identityCodesArr['users_code'], "Users (".$identityCodesArr['users_code'].")");

					/* Removing the codes which are not included for first phase*/
					//$identity_data = sapp_Global::selectOptionBuilder($identityCodesArr['staffing_code'], "Staffing (".$identityCodesArr['staffing_code'].")");
					//$identity_data .= sapp_Global::selectOptionBuilder($identityCodesArr['vendors_code'], "Vendors (".$identityCodesArr['vendors_code'].")");                    
                    
                }
                else if($prev_cntrl == 'organisationinfo' || $prev_cntrl == 'employee')
                {
                    /*$user_model = new Default_Model_Usermanagement();
                    if(isset($_POST['user_id']) && $_POST['user_id'] != '')
                        $user_id = $_POST['user_id'];
                    if($user_id != 'new')
                    {                        
                        $user_data = $user_model->getUserDataById($user_id);
                        $identity_data = $user_data['employeeId'];
                    }
                    else 
                    {*/
                        $identity_codes = $IdentityCodesModel->getIdentitycodesRecord();
                        $emp_identity_code = isset($identity_codes[0])?$identity_codes[0]['employee_code']:"";
                        if($emp_identity_code!='')
                        {
                            // $emp_id = $emp_identity_code.str_pad($user_model->getMaxEmpId($emp_identity_code), 4, '0', STR_PAD_LEFT);
                            $emp_id = $emp_identity_code;
                        }
                        $identity_data = $emp_id;
                    // }
                }
            }
        }
        $this->view->identity_data = $identity_data;
        $this->view->prev_cntrl = $prev_cntrl;
        $this->view->user_id = $user_id;
   }//end of addpopup action.
	
	public function editAction()
	{	
	    $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->getRequest()->getParam('id');
		
		$IdentityCodesform = new Default_Form_identitycodes();
		
		$IdentityCodesModel = new Default_Model_Identitycodes();	
		try
		{
			if($id && $id>0 && is_numeric($id))
			{$id = abs($id);	
				$data = $IdentityCodesModel->getIdentitycodesRecord_i($id);
				if(!empty($data))
				{
					$IdentityCodesform->setDefault("employee_code",$data[0]["employee_code"]);
					$IdentityCodesform->setDefault("bg_code",$data[0]["backgroundagency_code"]);
					$IdentityCodesform->setDefault("vendor_code",$data[0]["vendors_code"]);
					$IdentityCodesform->setDefault("staffing_code",$data[0]["staffing_code"]);
					$IdentityCodesform->setDefault("users_code",$data[0]["users_code"]);
					$IdentityCodesform->setDefault("requisition_code",$data[0]["requisition_code"]);
					$IdentityCodesform->submit->setLabel('Update');
					$this->view->id = $id;
					$this->view->nodata = '';
				}
				else
				{
					$this->view->nodata = 'norecord';
				}
				$IdentityCodesform->setAttrib('action',BASE_URL.'identitycodes/edit/id/'.$id);
			}
                        else
				{
					$this->view->nodata = 'norecord';
				}
		}
		catch(Exception $e)
		{
			 $this->view->nodata = 'nodata';
		}
		
		/* Removing the codes which we are not being using for the first phase*/
		$IdentityCodesform->removeElement('vendor_code');
		$IdentityCodesform->removeElement('staffing_code');
		
		if(!sapp_Global::_isactivemodule(BGCHECKS))		
		$IdentityCodesform->removeElement('bg_code');
		if(!sapp_Global::_isactivemodule(RESOURCEREQUISITION))
		$IdentityCodesform->removeElement('requisition_code');
		$this->view->form = $IdentityCodesform;
		
		if($this->getRequest()->getPost())
		{
      		$result = $this->save($IdentityCodesform);	
			$this->view->msgarray = $result; 
		}
	}
	
    public function save($IdentityCodesform,$redirect_flag = '')
    {
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
        } 
        $date = new Zend_Date();
        if($IdentityCodesform->isValid($this->_request->getPost()))
        {
            $IdentityCodesModel = new Default_Model_Identitycodes();	
            $id = $this->_request->getParam('id'); 

            $employeeCode = $this->_request->getParam('employee_code');
            $bgAgencyCode = $this->_request->getParam('bg_code');
            $vendorCode = $this->_request->getParam('vendor_code');
            $staffingCode = $this->_request->getParam('staffing_code');
            $users_code = $this->_request->getParam('users_code');
            $requisition_code = $this->_request->getParam('requisition_code');

            $data = array(  
                        'employee_code'=>$employeeCode,
                        'backgroundagency_code'=>$bgAgencyCode,
                        'vendor_code'=>$vendorCode,
                        'staffing_code'=>$staffingCode,
                        'users_code'=>$users_code,
                        'requisition_code'=>$requisition_code,
                        'modifiedby'=>$loginUserId,
                        'modifieddate'=>gmdate("Y-m-d H:i:s")
						
                    );
			/* Removing the codes which we are not being using for the first phase*/
			unset($data['vendor_code']);
			unset($data['staffing_code']);
			
			if(!sapp_Global::_isactivemodule(RESOURCEREQUISITION))
				unset($data['requisition_code']);
			if(!sapp_Global::_isactivemodule(BGCHECKS))
				unset($data['backgroundagency_code']);
			
			
            if($id!='')
            {
                $where = array('id=?'=>$id);  
                $actionflag = 2;
            }
            else
            {
                $data['createdby'] = $loginUserId;
                $data['createddate'] = gmdate("Y-m-d H:i:s");
                $where = '';
                $actionflag = 1;
            }
                $Id = $IdentityCodesModel->SaveorUpdateIdentitycodesData($data, $where);
            if($Id == 'update')
            {
               $tableid = $id;
               if($redirect_flag == '')
                    $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Identity codes updated successfully."));
               $smessage = "Identity codes updated successfully.";
            }   
            else
            {
                $tableid = $Id; 
                if($redirect_flag == '')
                    $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Identity codes added successfully."));					   
                $smessage = "Identity codes added successfully.";
            }   
			$menuID = IDENTITYCODES;
            $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
            if($redirect_flag == '')
                $this->_redirect('identitycodes');
            else 
                return array('msg' => $smessage);
        }
        else
        {
            $messages = $IdentityCodesform->getMessages();
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
	
}

