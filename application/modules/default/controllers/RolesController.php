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

class Default_RolesController extends Zend_Controller_Action
{
    private $_options;
    public function preDispatch()
    {
        $auth = Zend_Auth::getInstance();
        $session = sapp_Global::_readSession();
        if(!isset($session))
        {
            if($this->getRequest()->isXmlHttpRequest())
            {
                echo Zend_Json::encode( array('login' => 'failed') );
                die();	
            }
            else
            {
                $this->_redirect('');
            }
        }
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('getgroupmenu', 'html')->initContext();
    }
	
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();		
    }

    public function indexAction()
    {
        $role_model = new Default_Model_Roles();
        
        $call = $this->_getParam('call');
        if($call == 'ajaxcall')
            $this->_helper->layout->disableLayout();
		        		
        $objname = $this->_getParam('objname');
        $refresh = $this->_getParam('refresh');
        $dashboardcall = $this->_getParam('dashboardcall');
        $data = array();
        $searchQuery = '';
        $searchArray = array();
       
        if($refresh == 'refresh')
        {
            if($dashboardcall == 'Yes')
                $perPage = DASHBOARD_PERPAGE;
            else	
                $perPage = PERPAGE;
            $sort = 'DESC';$by = 'r.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';
            $searchArray = array();
        }
        else 
        {
            $sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
            $by = ($this->_getParam('by')!='')? $this->_getParam('by'):'r.modifieddate';
            if($dashboardcall == 'Yes')
                $perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
            else 
                $perPage = $this->_getParam('per_page',PERPAGE);
            $pageNo = $this->_getParam('page', 1);           
            $searchData = $this->_getParam('searchData');	                      
        }

        $dataTmp = $role_model->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall,'','','','');
        array_push($data,$dataTmp);
        $this->view->dataArray = $data;
        $this->view->call = $call ;
    }

    public function viewAction()
    {	        
        $groups_model = new Default_Model_Groups();  
        $group_arr = $groups_model->getGroupsListForRoles(); 
        $id = (int)$this->getRequest()->getParam('id');
        $id = abs($id);
        $callval = $this->getRequest()->getParam('call');
        if($callval == 'ajaxcall')
                $this->_helper->layout->disableLayout();
        $objName = 'roles';
        $form = new Default_Form_Roles();
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
        $roles_model = new Default_Model_Roles();
        try
        {
            if($id && is_numeric($id) && $id>1 )
            {
                $data = $roles_model->getRoleDataById($id);
                if(count($data)>0)
                {
                    foreach($data as $key=>$val)
                    {
                        $data[$key] = htmlentities(addslashes($val), ENT_QUOTES, "UTF-8");
                    }
                    $form->populate($data);
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
        $this->view->controllername = $objName;
        $this->view->id = $id;
        $this->view->form = $form;
        
        $this->view->group_arr = $group_arr; 
    }
    /**
     * This action is used for adding/updating data.
     * @parameters
     * @param id  =  id of role (optional)
     * 
     * @return Zend_Form.
     */
    public function editAction()
    {        
        $groups_model = new Default_Model_Groups();        
        $role_model = new Default_Model_Roles();
        $group_arr = $groups_model->getGroupsListForRoles();                                                         

        $id = $this->getRequest()->getParam('id',null);
        $display_grp = "1";
        $total_role_cnt = 0;
        
        $callval = $this->getRequest()->getParam('call');
        if($callval == 'ajaxcall')
            $this->_helper->layout->disableLayout();

        $form = new Default_Form_Roles();            
        $data = array();
        if($id && is_numeric($id) && $id>1)
        {
            try
            {
                $id = (int) $id;
                $id = abs($id);
                $form->submit->setLabel('Update');                			                	
                $data = $role_model->getRoleDataById($id);              
                if(count($data)>0)
                {
                    foreach($data as $key=>$val)
                    {
                        $data[$key] = htmlentities(addslashes($val), ENT_QUOTES, "UTF-8");
                    }	
                    $form->populate($data);
                }
            }
            catch(Exception $e)
            {
                $this->view->nodata = "nodata";
            }
        }
        else if($id == '')
        {
            $total_role_cnt = $role_model->getTotalRolecnt();
            foreach($group_arr as $gid => $gdata)
            {
                if($gdata['cnt'] <5)
                {
                    $display_grp = $gid;
                    break;
                }
            }
            
        }
        else 
        {
            $this->view->nodata = "nodata";
        }
        $this->view->form = $form;                                        
        $this->view->group_arr = $group_arr; 
        $this->view->data = $data;
        $this->view->display_grp = $display_grp;
        $this->view->total_role_cnt = $total_role_cnt;
    }
    /**
     * This function is used to add/update data in database.
     * @parameters   
     * @param   Array   all form data.
     * @return  success/error messages in json format.
     */
    public function saveupdateAction()
    {
         $auth = Zend_Auth::getInstance();
         if($auth->hasIdentity())
         {
             $loginUserId = $auth->getStorage()->read()->id;
             $sess_values = $auth->getStorage()->read();
         }
         
         $role_form = new Default_Form_Roles();
         $roles_model = new Default_Model_Roles();
         $previlege_model = new Default_Model_Privileges();
         $menumodel = new Default_Model_Menu();
         $messages = $role_form->getMessages();
         $actionflag = '';
         $tableid  = '';
		
        if($this->getRequest()->getPost())
        {
            if($role_form->isValid($this->_request->getPost()))
            {
                $trDb = Zend_Db_Table::getDefaultAdapter();		
		// starting transaction
		$trDb->beginTransaction();
                try
                {
                $id = $this->_request->getParam('id');
                $role_name = trim($this->_request->getParam('rolename'));
                $role_type = trim($this->_request->getParam('roletype'));
                $role_description = trim($this->_request->getParam('roledescription'));
                $levelid = $this->_request->getParam('levelid');
                $group_id = $this->_request->getParam('hidgroup_value');
                $chk_menu = $this->_request->getParam("chk_menu");
                
                foreach($chk_menu as $key => $value)
                    $chk_menu_val = $value;
                
                $data = array(
                            'rolename' => $role_name,
                            'roletype' => $role_type,
                            'roledescription' => $role_description,
                            'levelid' => $levelid,  
                            'group_id' => $group_id,    
                            'modifiedby'=>$loginUserId,
                            'modifieddate'=> gmdate("Y-m-d H:i:s"),                                                        
                        );
                if($id!='')
                {
                    $where = array('id=?'=>$id);  
                    $messages['message']='Role updated successfully.';
                    $actionflag = 2;
                }
                else
                {
                    $data['createdby'] = $loginUserId;
                    $data['createddate'] =  gmdate("Y-m-d H:i:s");
                    $data['isactive'] = 1;
                    $where = '';
                    $messages['message']='Role added successfully.';
                    $actionflag = 1;
                }
					
                $Id = $roles_model->SaveorUpdateRolesData($data, $where);
                if($Id == 'update')
                {    
                    $tableid = $id;
                    $menu_data_post = $this->_request->getParam('menu_data_post');
                    if(count($menu_data_post) > 0)
                    {
                        $add_arr = array_diff($chk_menu_val,$menu_data_post);
                        $del_arr = array_diff($menu_data_post,$chk_menu_val);
                        $update_arr =  array_diff($chk_menu_val,$add_arr);
                        foreach($del_arr as $key => $value)
                        {
                            $delete_prev_data = array(
                                                'modifiedby' => $loginUserId,
                                                'modifieddate' => gmdate("Y-m-d H:i:s"),
                                                'isactive' => 0,
                            );
                            $where_delete = "id = ".$key;
                            $previlege_model ->SaveorUpdatePrivilegesData($delete_prev_data, $where_delete); 
                        }
                        
                        foreach($update_arr as $key => $value)
                        {
                            $prev_update_data = array(                                            
                                            'modifiedby' => $loginUserId,   
                                            'group_id' => $group_id,
                                            'modifieddate' => gmdate("Y-m-d H:i:s"),                                                                                        
                                            'addpermission' => $this->_request->getParam('rd_addpermission'.$value,'No'),
                                            'editpermission' => $this->_request->getParam('rd_editpermission'.$value,'No'),
                                            'deletepermission' => $this->_request->getParam('rd_deletepermission'.$value,'No'),
                                            'viewpermission' => $this->_request->getParam('rd_viewpermission'.$value,'No'),
                                            'uploadattachments' => $this->_request->getParam('rd_uploadattachment'.$value,'No'),
                                            'viewattachments' => $this->_request->getParam('rd_viewattachment'.$value,'No'),                                            
                            );
                            $update_where = "isactive = 1 and object = ".$value." and role = ".$tableid;
                            $previlege_model ->SaveorUpdatePrivilegesData($prev_update_data, $update_where);
                        }
                        
                        foreach($add_arr as $key => $value)
                        {
                            $prev_data = array(
                                            'createdby' => $loginUserId,
                                            'modifiedby' => $loginUserId,
                                            'createddate' => gmdate("Y-m-d H:i:s"),
                                            'modifieddate' => gmdate("Y-m-d H:i:s"),
                                            'isactive' => 1,
                                            'object' => $value,
                                            'group_id' => $group_id,
                                            'addpermission' => $this->_request->getParam('rd_addpermission'.$value,'No'),
                                            'editpermission' => $this->_request->getParam('rd_editpermission'.$value,'No'),
                                            'deletepermission' => $this->_request->getParam('rd_deletepermission'.$value,'No'),
                                            'viewpermission' => $this->_request->getParam('rd_viewpermission'.$value,'No'),
                                            'uploadattachments' => $this->_request->getParam('rd_uploadattachment'.$value,'No'),
                                            'viewattachments' => $this->_request->getParam('rd_viewattachment'.$value,'No'),
                                            'role' => $tableid,
                            );
                            $previlege_model ->SaveorUpdatePrivilegesData($prev_data, '');
                        }
                        sapp_Global::generateAccessControl();
                        
                    }
                    //start of mailing 
                    $this->mailing_function($sess_values,$role_name,'update',$roles_model);
                    //end of mailing
                }
                else //for adding
                {
                    $tableid = $Id; 
                    if(count($chk_menu_val) > 0)
                    {
                        foreach($chk_menu_val as $menu_id)
                        {
                            $prev_data = array(
                                            'createdby' => $loginUserId,
                                            'modifiedby' => $loginUserId,
                                            'createddate' => gmdate("Y-m-d H:i:s"),
                                            'modifieddate' => gmdate("Y-m-d H:i:s"),
                                            'isactive' => 1,
                                            'object' => $menu_id,
                                            'group_id' => $group_id,
                                            'addpermission' => $this->_request->getParam('rd_addpermission'.$menu_id,'No'),
                                            'editpermission' => $this->_request->getParam('rd_editpermission'.$menu_id,'No'),
                                            'deletepermission' => $this->_request->getParam('rd_deletepermission'.$menu_id,'No'),
                                            'viewpermission' => $this->_request->getParam('rd_viewpermission'.$menu_id,'No'),
                                            'uploadattachments' => $this->_request->getParam('rd_uploadattachment'.$menu_id,'No'),
                                            'viewattachments' => $this->_request->getParam('rd_viewattachment'.$menu_id,'No'),
                                            'role' => $tableid,
                            );
                            $previlege_model ->SaveorUpdatePrivilegesData($prev_data, ''); 
                        }
                        sapp_Global::generateAccessControl();
                    }
                    
                    //start of mailing 
                    $this->mailing_function($sess_values,$role_name,'add',$roles_model);
                    //end of mailing
                }//end of else of adding
                $objidArr = $menumodel->getMenuObjID('/roles');
                $objID = $objidArr[0]['id'];
                $result = sapp_Global::logManager($objID,$actionflag,$loginUserId,$tableid);
                $messages['result']='saved';
                $messages['nomessage']='no';
                if($actionflag == 1)
                    $messages['message']='Role added successfully.';
                else
                    $messages['message']='Role updated successfully.';
                $_SESSION['role_msg'] = $messages['message'];
                $_SESSION['role_cls'] = 'success';
                $trDb->commit();
                $this->_helper->json($messages);
                }
                catch (Exception $e) 
		{
                    $trDb->rollBack();
						
                    $messages['result']='exception';
                    $_SESSION['role_cls'] = 'error';
                    $messages['message']='Something went wrong, please try again later.';
                    $_SESSION['role_msg'] = $messages['message'];
                    $this->_helper->json($messages);			
		}  
            }//end of valid if
            else
            {
                $messages = $role_form->getMessages();
                $messages['result']='error';
                $this->_helper->json($messages);	    	
            }
        }           
    }

    /**
     * This action is used to delete roles and their child data.
     * @parameters
     * @param $objid    =   id of role.
     * 
     * @return   {String}   success/failure message 
     */
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
        $actionflag = 3;
        if($id)
        {
            $roles_model = new Default_Model_Roles();
            $user_model = new Default_Model_Usermanagement();
            $user_cnt = $user_model->getUserCntByRole($id);
            if($user_cnt == 0)
            {
                $previleges_model = new Default_Model_Privileges();
                $menumodel = new Default_Model_Menu();
                $data = array(
                            'isactive' => 0,
                            'modifiedby' => $loginUserId,
                            'modifieddate' => gmdate("Y-m-d H:i:s"),
                    );
                $where = array('id=?'=>$id);
                $Id = $roles_model->SaveorUpdateRolesData($data, $where);
                if($Id == 'update')
                {
                    sapp_Global::generateAccessControl();
                    $prev_data = array(
                            'isactive' => 0,
                            'modifiedby' => $loginUserId,
                            'modifieddate' => gmdate("Y-m-d H:i:s"),
                    );
                    $where_prev = "role = ".$id;
                    $previleges_model->SaveorUpdatePrivilegesData($prev_data, $where_prev);
                    $objidArr = $menumodel->getMenuObjID('/roles');
                    $objID = $objidArr[0]['id'];                    
                    $result = sapp_Global::logManager($objID,$actionflag,$loginUserId,$id);  
                    $messages['message'] = 'Role deleted successfully';
                    $messages['msgtype'] = 'success';
                }   
                else
                {
                   $messages['message'] = 'Role cannot be deleted as Employees with the role exist.';
                   $messages['msgtype'] = 'error';
                }
            }
            else 
            {
                $messages['message'] = 'Role cannot be deleted as Employees with the role exist.';
                $messages['msgtype'] = 'error';
            }
        }
        else
        { 
            $messages['message'] = 'Role cannot be deleted as Employees with the role exist.';
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
    }// end of delete action
    
    public function mailing_function($sess_values,$role_name,$save_type,$roles_model)
    {
        $group_ids = MANAGEMENT_GROUP.",".SYSTEMADMIN_GROUP;
        $object_id = ROLES;
                            
        $emp_arr = $roles_model->getEmpForRoleMail($group_ids,$object_id);
        unset($emp_arr[$sess_values->id]);
        if($save_type == 'add')        
        {
            $hr_arr = $roles_model->getEmpForRoleMail(HR_GROUP,EMPLOYEE);
            unset($hr_arr[$sess_values->id]);
            $emp_arr = $emp_arr + $hr_arr;
        }
        
        if($save_type == 'add')
        	$headerText = 'created';
        else
        	$headerText = 'updated';	
        foreach($emp_arr as $empdata)
        {
            $base_url = 'http://'.$this->getRequest()->getHttpHost() . $this->getRequest()->getBaseUrl();
        
            $view = $this->getHelper('ViewRenderer')->view;
            $this->view->emp_name = $empdata['userfullname'];                           
            $this->view->base_url=$base_url;
            $this->view->type = $save_type;
            $this->view->created_by = $sess_values->userfullname;
            $this->view->role_name = $role_name;
                        
            $text = $view->render('mailtemplates/role.phtml');
            $options['subject'] = APPLICATION_NAME.': Role is '.$headerText.'';
            $options['header'] = 'Role is '.$headerText.'';
            $options['toEmail'] = $empdata['emailaddress'];  
            $options['toName'] = $empdata['userfullname'];
            $options['message'] = $text;

            $options['cron'] = 'yes';
            sapp_Global::_sendEmail($options);
        }                
    }
    public function getgroupmenuAction()
    {
        $role_id = $this->_getParam('role_id',null);
        $group_id = $this->_getParam('group_id',null);
        $id = $this->_getParam('pri_val',null);
        $disabled = $this->_getParam('disabled','');
        $menu_model = new Default_Model_Menu();
        
        $menu_data = $menu_model->getgroupmenu($group_id,$role_id,$id);
		$active_menus = $menu_model->getisactivemenus();
		$act_menus = array();
		if(!empty($active_menus))
		{
			foreach($active_menus as $act)
			{
				$act_menus[$act['id']] = $act;
			}
		}
		$this->view->act_menus = $act_menus;
        $this->view->menu_arr = $menu_data['tmpArr'];
        $this->view->menu_data_post = $menu_data['menu_data_post'];
        $this->view->menu_data = $menu_data['menu_data'];
        $this->view->permission_data = $menu_data['permission_data'];
        $this->view->group_level = $menu_data['group_data']['level'];
        $this->view->disabled = $disabled;
        $this->view->group_id = $group_id;
        
    }//end of getgroupmenu action function.
}//end of class

