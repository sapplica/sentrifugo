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

class Default_EmailcontactsController extends Zend_Controller_Action
{
    private $options;
    
    public function preDispatch()
    {	
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('getmailcnt', 'json')->initContext();
        $ajaxContext->addActionContext('getgroupoptions', 'json')->initContext();
    }

    public function init()
     {
         $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
     }

   	public function indexAction()
     {  	
			$emailContactsModel = new Default_Model_Emailcontacts();	
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
					$sort = 'DESC';$by = 'e.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
			}
			else 
			{
					$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
					$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'e.modifieddate';
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
			
			$dataTmp = $emailContactsModel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);		
		
			array_push($data,$dataTmp);
			$this->view->dataArray = $data;
			$this->view->call = $call ;
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
        $emailgroup_model = new Default_Model_Emailgroups();
        $emailcontacts_model = new Default_Model_Emailcontacts();
        $business_unit_model = new Default_Model_Businessunits();
        $bunits_options = $business_unit_model->getBusinessUnitsList();
        $emailContactsform = new Default_Form_emailcontacts();
        $emailContactsform->setAttrib('action',BASE_URL.'emailcontacts/add');
        $group_options = array();
        if(isset($_POST['business_unit_id']) && $_POST['business_unit_id'] != '')
        {
            $bunit_val = $_POST['business_unit_id'];
        }
        else 
        {
            $bunit_val = 0;
        }
        $group_data = $emailcontacts_model->getgroupoptions($bunit_val);
        if(count($group_data) > 0)
        {
            foreach($group_data as $group)
            {
				if($group['group_code'] == 'BG_CHECKS_HR' || $group['group_code']  == 'BG_CHECKS_MNGMNT')
				{
					if(sapp_Global::_isactivemodule(BGCHECKS))
					 $group_options[$group['id']] = $group['group_name'];
				}
				else if($group['group_code'] == 'REQ_HR' || $group['group_code']  == 'REQ_MGMT')
				{
					if(sapp_Global::_isactivemodule(RESOURCEREQUISITION))
					 $group_options[$group['id']] = $group['group_name'];
				}
				else
				$group_options[$group['id']] = $group['group_name'];
            }
        }
        else 
        {
            $msgarray['group_id'] = "No more groups are available for this business unit.";
        }
        $emailContactsform->group_id->addMultiOptions(array(''=>'Select Group')+$group_options);
        $emailContactsform->business_unit_id->addMultiOptions(array('0'=>'No Business Unit')+$bunits_options);
        $this->view->form = $emailContactsform; 	
        $this->view->msgarray = $msgarray;
        $this->view->bunits_options = $bunits_options;
        if($this->getRequest()->getPost())
        {
            $result = $this->save($emailContactsform);	
            $this->view->form = $emailContactsform;			 
            $this->view->msgarray = $result; 
        }  		
    }

    public function editAction()
    {	
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
        }
        $id = $this->getRequest()->getParam('id');
        $id = abs($id);
	$emailgroup_model = new Default_Model_Emailgroups();
        $business_unit_model = new Default_Model_Businessunits();
        $bunits_options = $business_unit_model->getBusinessUnitsList();
        $emailContactsform = new Default_Form_emailcontacts();
        $emailContactsModel = new Default_Model_Emailcontacts();
        $group_options = $emailgroup_model->getEgroupsOptions();
        
        $emailContactsform->group_id->addMultiOptions(array(''=>'Select Group')+$group_options);
        $emailContactsform->business_unit_id->addMultiOptions(array(''=>'Select Business Unit')+$bunits_options);
        try
        {
            if($id && $id>0 && is_numeric($id))
            {
                $data = $emailContactsModel->getgroupEmailRecord($id);
                if(!empty($data))
                {
					if((sapp_Global::_isactivemodule(RESOURCEREQUISITION) && ($data[0]['group_code'] == 'REQ_HR' || $data[0]['group_code'] == 'REQ_MGMT')) || (sapp_Global::_isactivemodule(BGCHECKS) && ($data[0]['group_code'] == 'BG_CHECKS_HR' || $data[0]['group_code'] == 'BG_CHECKS_MNGMNT')) || $data[0]['group_code'] == 'LV_HR')
                    {
						$group_name = $group_options[$data[0]["group_id"]];
						$bunit_name = $bunits_options[$data[0]["business_unit_id"]];
						$emailContactsform->setDefault("id",$id);
						$emailContactsform->setDefault("group_id",$data[0]["group_id"]);                                        
						$emailContactsform->setDefault("groupEmail",$data[0]["groupEmail"]);
						$emailContactsform->setDefault("business_unit_id",$data[0]["business_unit_id"]);
						$this->view->id = $id;
						$this->view->nodata = '';
						$this->view->group_name = $group_name;
						$this->view->bunit_name = $bunit_name;
						$emailContactsform->submit->setLabel('Update'); 
					}
					else
					{
						$this->view->nodata = 'norecord';
					}
                }
                else
                {
                    $this->view->nodata = 'norecord';
                }
                $emailContactsform->setAttrib('action',BASE_URL.'emailcontacts/edit/id/'.$id);
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
        
        $this->view->form = $emailContactsform;
        if($this->getRequest()->getPost())
        {
            $result = $this->save($emailContactsform);	
            $this->view->msgarray = $result; 
        }
    }
    public function getgroupoptionsAction()
    {
        $bunit = $this->_getParam('bunit', null);
        $options = sapp_Global::selectOptionBuilder('', 'Select Group');
        if($bunit != '')
        {
            $emailcontacts_model = new Default_Model_Emailcontacts();
            $group_data = $emailcontacts_model->getgroupoptions($bunit);
            if(count($group_data) > 0)
            {
                foreach($group_data as $group)
                {
                    $options .= sapp_Global::selectOptionBuilder($group['id'], $group['group_name']);
                }
            }
        }
        $this->_helper->json(array('options' => $options));
    }
    public function save($emailContactsform)
    {   
         $result ="";
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
        } 
        $date = new Zend_Date();
        if($emailContactsform->isValid($this->_request->getPost()))
        {
            $trDb = Zend_Db_Table::getDefaultAdapter();		
            // starting transaction
            $trDb->beginTransaction();
            try
            {
                $emailContactsModel = new Default_Model_Emailcontacts();	
                $id = $this->_request->getParam('id'); 

                $group_id = $this->_request->getParam('group_id');
                $grpEmail = $this->_request->getParam('groupEmail');
                $business_unit_id = $this->_request->getParam('business_unit_id');
                $hid_groupemail = $this->_request->getParam('hid_groupemail',null);
                $data = array(  'group_id'=>$group_id,
                                'groupEmail'=>$grpEmail,
                                'business_unit_id' => $business_unit_id,
                                'modifiedby'=>$loginUserId,
                                'modifieddate'=>gmdate("Y-m-d H:i:s"),
                                'createdby'=>$loginUserId,
                                'createddate'=>gmdate("Y-m-d H:i:s"),
                            );
                $updata = array(
                            'isactive' => 0,
                            'modifiedby'=>$loginUserId,
							'modifieddate'=>gmdate("Y-m-d H:i:s")
                );
                
                if($id!='')
                {
                    
                    $where = '';  
                    $actionflag = 2;
                    if($hid_groupemail == $grpEmail)
                    {
                        $where = " id = ".$id;
                        $data = array(
                                'modifiedby'=>$loginUserId,
                                'modifieddate'=>gmdate("Y-m-d H:i:s"),
                        );
                    }
                    else 
                    {
                        $upwhere = "isactive = 1 and business_unit_id =".$business_unit_id."  and group_id = ".$group_id;                
                        $emailContactsModel->SaveorUpdateEmailcontactsData($updata, $upwhere);
                    }
                }
                else
                {                    
                    $where = '';
                    $actionflag = 1;
                }
                $Id = $emailContactsModel->SaveorUpdateEmailcontactsData($data,$where);
                if($id)
                {
                    $tableid = $id;
                    $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Email contacts updated successfully."));
                }   
                else
                {
                    $tableid = $Id; 	
                    $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Email contacts added successfully."));					   
                }   
			    $menuID = EMAILCONTACTS;
                $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
                sapp_Global::generateEmailConstants();
                $trDb->commit();
                $this->_redirect('emailcontacts');	
            }
            catch (Exception $e) 
            {
                $trDb->rollBack();
                $msgarray['group_id'] = "Something went wrong, please try again later.";
                return $msgarray;
            }
        }
        else
        {   
            $messages = $emailContactsform->getMessages();

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
    public function viewAction()
    {
     $id = abs(intval($this->getRequest()->getParam('id')));
        $callval = $this->getRequest()->getParam('call');
        if($callval == 'ajaxcall')
            $this->_helper->layout->disableLayout();

        $emailContactsform = new Default_Form_emailcontacts();
        $emailContactsModel = new Default_Model_Emailcontacts();                
                        
        if($id && $id>0)
        {
        	
            try
            {
                $data = $emailContactsModel->getdataforview($id);            
				if(count($data)>0)
                {
                	
					if((sapp_Global::_isactivemodule(RESOURCEREQUISITION) && ($data['group_code'] == 'REQ_HR' || $data['group_code'] == 'REQ_MGMT')) || (sapp_Global::_isactivemodule(BGCHECKS) && ($data['group_code'] == 'BG_CHECKS_HR' || $data['group_code'] == 'BG_CHECKS_MNGMNT' || $data['group_code'] == 'LV_HR' )))
                    {
                    	$emailContactsform->setDefault("id",$id);                    
						$emailContactsform->setDefault("groupEmail",$data["groupEmail"]);                    
						$emailContactsform->group_id->addMultiOptions(array(''=>$data['group_name']));
						$emailContactsform->business_unit_id->addMultiOptions(array(''=>$data['unitname']));
						$emailContactsform->group_id->setAttrib('disabled','disabled');
						$emailContactsform->groupEmail->setAttrib('disabled','disabled');
						$emailContactsform->business_unit_id->setAttrib('disabled','disabled');
						$this->view->emailContactsData = $data;
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
        $this->view->form = $emailContactsform;		
        $this->view->id = $id;
        $this->view->controllername = "emailcontacts";
    }
    /**
     * This action is used to delete email contact.
     * @parameters
     * @param objid    =   id of email contact.
     * 
     * @return  {String} =   success/failure message 
     */
    public function deleteAction()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
        }
        $id = $this->_request->getParam('objid');
        $deleteflag= $this->_request->getParam('deleteflag');
        $messages['message'] = '';
        $actionflag = 3;
        if($id)
        {
            $econtact_model = new Default_Model_Emailcontacts();
			$emailgroupdata = $econtact_model->getgroupEmailRecord($id);
            $data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
            $where = array('id=?'=>$id);
            $Id = $econtact_model->SaveorUpdateEmailcontactsData($data, $where);
            if($Id == 'update')
            {
                sapp_Global::generateEmailConstants();
			    $menuID = EMAILCONTACTS;
                sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id); 
                $configmail = sapp_Global::send_configuration_mail('Email Group',$emailgroupdata[0]['groupEmail']);				
                $messages['message'] = 'Email contact deleted successfully.';
                $messages['msgtype'] = 'success';
            }   
            else
            {
                $messages['message'] = 'Email contact cannot be deleted.';				
                $messages['msgtype'] = 'error';
            }
        }
        else
        { 
            $messages['message'] = 'Email contact cannot be deleted.';
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
    public function getmailcntAction()
    {
        $bunit_id = $this->_getParam('bunit_id',null);
        $group_id = $this->_getParam('group_id',null);
        
        $email_model = new Default_Model_Emailcontacts();
        $result  = 'no';
        $cnt = $email_model->getemailcnt($bunit_id, $group_id);
        if($cnt > 0)
            $result = "yes";
        $this->_helper->json(array('result'=>$result));
        
    }    
}

