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

class Default_AnnouncementsController extends Zend_Controller_Action
{

    private $options;
	public function preDispatch()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('uploadsave', 'json')->initContext();		
        $ajaxContext->addActionContext('uploaddelete', 'html')->initContext();
        $ajaxContext->addActionContext('getdepts', 'json')->initContext();
	}
	
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
    }

    public function indexAction()
    {
		$announcementsModel = new Default_Model_Announcements();	
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
			$sort = 'DESC';$by = 'a.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else 
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'a.modifieddate';
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
				
		$dataTmp = $announcementsModel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);		 		
					
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
			$loginuserbusinessunit_id = $auth->getStorage()->read()->businessunit_id;
		}
		$msgarray = array();
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		$announcementsForm = new Default_Form_Announcements();
		$bu_model = new Default_Model_Businessunits();
		
		$bu_arr = $bu_model->getBU_report();
		if(!empty($bu_arr))
        {
        	foreach ($bu_arr as $bu)
            {
            	/* if($loginuserGroup == HR_GROUP && $bu['id'] == $loginuserbusinessunit_id)
            		$announcementsForm->businessunit_id->addMultiOption($bu['id'],utf8_encode($bu['bu_name']));
            	if($loginuserGroup != HR_GROUP) */
            		$announcementsForm->businessunit_id->addMultiOption($bu['id'],utf8_encode($bu['bu_name']));
			}
        }
        else
        {
        	$msgarray['businessunit_id'] = 'Business Units are not added yet.';
        }
        // hr can add announcements for any businessunit and department
        /* if($loginuserGroup == HR_GROUP){
        	$announcementsForm->businessunit_id->setValue($loginuserbusinessunit_id);
        	
        	if($loginuserbusinessunit_id)
            {
                $dept_model = new Default_Model_Departments();
                $dept_data = $dept_model->getDepartmentWithCodeList_bu($loginuserbusinessunit_id);
                if(!empty($dept_data))
                {
                	$de_val = array();
                    foreach($dept_data as $dept)
                    {
                    	$announcementsForm->department_id->addMultiOption($dept['id'],$dept['unitcode']." ".$dept['deptname']);
                    	$de_val[] = $dept['id'];
                    }
                    $announcementsForm->department_id->setValue($de_val);
                }
            }
        } */
		
		$announcementsForm->setAttrib('action',BASE_URL.'announcements/add');
		$this->view->form = $announcementsForm; 
		$this->view->msgarray = $msgarray; 
		$this->view->ermsg = '';
		if($this->getRequest()->getPost()){
			 $result = $this->save($announcementsForm);
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
		$objName = 'announcements';
		$d_a=array();
		try
		{
		    if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					$announcementsModel = new Default_Model_Announcements();	
					$data = $announcementsModel->getAnnouncementsDatabyID($id);
					$previ_data = sapp_Global::_checkprivileges(ANNOUNCEMENTS,$login_group_id,$login_role_id,'edit');
                   	
					$busi_names = '';
					$dept_names = '';
					
					if($data[0]['businessunit_id'] != ''){
						$busiData = $announcementsModel->getBusinessUnitNames($data[0]['businessunit_id']);
						foreach ($busiData as $bd)
							$b_a[] = $bd['unitname']; 
						$busi_names = implode(', ', $b_a);
					}
					if($data[0]['department_id']){
						$deptData = $announcementsModel->getDepartmentNames($data[0]['department_id']);
						foreach ($deptData as $dd)
							$d_a[] = $dd['deptname'];
						$dept_names = implode(', ', $d_a);
					}
					
					if(!empty($data))
					{
						
						$data[0]['modifieddate'] =  sapp_Global::change_date($data[0]['modifieddate'],'view');
						$data = $data[0];
						$data['busi_names'] = $busi_names;
						$data['dept_names'] = $dept_names; 
						$data['description'] =html_entity_decode( $data['description']);
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
					$loginuserbusinessunit_id = $auth->getStorage()->read()->businessunit_id;
		}
	 	
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		
		$announcementsForm = new Default_Form_Announcements();
		$announcementsModel = new Default_Model_Announcements();
		try
        {		
			if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					$data = $announcementsModel->getAnnouncementsDatabyID($id);
					
					if((isset($data[0]['status']) && $data[0]['status']==1) || $loginuserRole == SUPERADMINROLE || $loginuserGroup == HR_GROUP || $loginuserGroup == MANAGEMENT_GROUP) //
					{
						$bu_model = new Default_Model_Businessunits();
						$bu_arr = $bu_model->getBU_report();
						if(!empty($bu_arr))
				        {
				        	foreach ($bu_arr as $bu)
				            {
				            	/* if($loginuserGroup == HR_GROUP && $bu['id'] == $loginuserbusinessunit_id)
				            		$announcementsForm->businessunit_id->addMultiOption($bu['id'],utf8_encode($bu['bu_name']));
				            	if($loginuserGroup != HR_GROUP) */
				            		$announcementsForm->businessunit_id->addMultiOption($bu['id'],utf8_encode($bu['bu_name']));
							}
				        }
						
						$dept_model = new Default_Model_Departments();
		                $dept_data = $dept_model->getDepartmentWithCodeList_bu($data[0]['businessunit_id']);
		                if(!empty($dept_data))
		                {
		                    foreach($dept_data as $dept)
		                    {
		                    	$announcementsForm->department_id->addMultiOption($dept['id'],$dept['unitcode']." ".$dept['deptname']);
		                    }
		                }
				        
						if(!empty($data))
						{
							$data = $data[0];
							$announcementsForm->populate($data);
							$announcementsForm->post_description->setValue($data['description']);
							$announcementsForm->businessunit_id->setValue(explode(',', $data['businessunit_id']));
							if($data['department_id'])
								$announcementsForm->department_id->setValue(explode(',', $data['department_id']));
							$announcementsForm->setAttrib('action',BASE_URL.'announcements/edit/id/'.$id);
							
							if($data['attachments']){
								$new = array();
								$ori = array();
								
								$attachments = json_decode($data['attachments'],true);
								foreach ($attachments as $k => $v) {
									$new[] = $v["new_name"];
									$ori[] = $v["original_name"];
								}
								$msg['file_original_names'] = implode(',', $ori);
								$msg['file_new_names'] = implode(',', $new);
								$this->view->msgarray = $msg;								
							}							
							
	                        $this->view->data = $data;
						}else
						{
							$this->view->ermsg = 'norecord';
						}
					}else
					{
						$this->_redirect('announcements');
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
		$this->view->form = $announcementsForm;
		if($this->getRequest()->getPost()){
      		$result = $this->save($announcementsForm);	
		    $this->view->msgarray = $result; 
		}
		$this->render('form');	
	}
	
	public function save($announcementsForm)
	{
	  	$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
			$loginuserbusinessunit_id = $auth->getStorage()->read()->businessunit_id;
		} 
	    $announcementsModel = new Default_Model_Announcements();
		$msgarray = array();
	    
		if($announcementsForm->isValid($this->_request->getPost()))
		{
            try
            {
	            $id = $this->_request->getParam('id');
				$actionflag = '';
				$tableid  = '';

				$status_value = 1;         
	          	$businessunit_id = $this->_getParam('businessunit_id',null);
                $department_id = $this->_getParam('department_id',null);
                $title = $this->_getParam('title',null);
                $description = $this->_getParam('post_description',null);
                $attachment = $this->_getParam('attachment',null);
                $status = $this->_getParam('status',null);
                    
                $file_original_names = $this->_getParam('file_original_names',null);
                $file_new_names = $this->_getParam('file_new_names',null);

                $org_names = explode(',', $file_original_names);
                $new_names = explode(',', $file_new_names);
                $attachment_array = array();

				for ($i=0; $i < count($org_names); $i++)
                {
                	if($new_names[$i] != '')
                    	$attachment_array[] = array("original_name" => $org_names[$i], "new_name" => $new_names[$i]);
              	}
                  	
                if($status == 'post')
                	$status_value = 2;
				
			   	$data = array(
                                    'businessunit_id' => count($businessunit_id) > 0?implode(',', $businessunit_id):null,
                                    'department_id' => count($department_id) > 0?implode(',', $department_id):null,
                                    'title' => $title,
                                    'description' => strip_tags(trim($description)),
                                    'attachments' => count($attachment_array) > 0?json_encode($attachment_array):null,
                                    'status' => $status_value,
                                    'isactive' => 1,
                                    'modifiedby' => $loginUserId,
			   						'modifiedby_role'=>$loginuserRole,
									'modifiedby_group'=>$loginuserGroup,
                                    'modifieddate'=>gmdate("Y-m-d H:i:s")
                        );
              /* 	if($loginuserGroup == HR_GROUP)
              		$data['businessunit_id'] = $loginuserbusinessunit_id; */
					
				if($id!=''){
					$where = array('id=?'=>$id);  
					$actionflag = 2;
				}
				else
				{
					$data['createdby'] = $loginUserId;
					$data['createddate'] = gmdate("Y-m-d H:i:s");
					$data['createdby_role'] = $loginuserRole;
					$data['createdby_group'] = $loginuserGroup;
					$data['isactive'] = 1;
					$where = '';
					$actionflag = 1;
				}
				$Id = $announcementsModel->SaveorUpdateAnnouncementsData($data, $where);
				if($status == 'post'){
	            	if(count($new_names) > 0)
	                {
						foreach ($new_names as $n)
	                    {
	                    	if($n != '')
	                        {
	                        	if(file_exists(CA_TEMP_UPLOAD_PATH.$n))
	                            {
	                            	copy(CA_TEMP_UPLOAD_PATH.$n, CA_UPLOAD_PATH.$n);
	                                unlink(CA_TEMP_UPLOAD_PATH.$n);
	                           	}
							}
						}
	              	}
				}
				if($Id == 'update')
				{
				   $tableid = $id;
				   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Announcement updated successfully."));
				}   
				else
				{
				   	$tableid = $Id; 	
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Announcement added successfully."));					   
				}   
				$menuID = ANNOUNCEMENTS;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
				$this->_redirect('announcements');	
			}
	        catch(Exception $e)
	      	{
	        	$msgarray['businessunit_id'] = "Something went wrong, please try again.";
	            return $msgarray;
	        }
		} else {
			$messages = $announcementsForm->getMessages();
			foreach ($messages as $key => $val)
			{
				foreach($val as $key2 => $val2)
				 {
					$msgarray[$key] = $val2;
					break;
				 }
			}
			
			$file_original_names = $this->_getParam('file_original_names',null);
			$file_new_names = $this->_getParam('file_new_names',null);
			
			$file_original_names = $this->getRequest()->getParam('file_original_names');
			$final_original_name_array = explode(',',$file_original_names);
			$final_original_name_array = array_filter($final_original_name_array);
			
			if(count($final_original_name_array)>0)
			{
				$file_original_names=implode(',',$final_original_name_array);
				
			}
			else
			{
				$file_original_names='';
			}

            $msgarray['file_original_names'] = $file_original_names;
            $msgarray['file_new_names'] = $file_new_names;
            
			$bu_id = $this->_getParam('businessunit_id',null);
            $options = "";
            if(!empty($bu_id))
            {
				//if superadmin
				if(strtoupper(gettype($bu_id)) == 'ARRAY')
				{
					$bu_id = implode(',', $bu_id);	
				}

                $dept_model = new Default_Model_Departments();
                $dept_data = $dept_model->getDepartmentWithCodeList_bu($bu_id);
                if(!empty($dept_data))
                {
                    foreach($dept_data as $dept)
                    {
                    	$announcementsForm->department_id->addMultiOption($dept['id'],$dept['unitcode']." ".$dept['deptname']);
                    }
                }
            }
			return $msgarray;	
		}	
	}
	
	public function getdeptsAction()
        {
            $bu_id = $this->_getParam('bu_id',null);
            $options = "";
            
            if(!empty($bu_id))
            {
                $bu_id = implode(',', $bu_id);
                $dept_model = new Default_Model_Departments();
                $dept_data = $dept_model->getDepartmentWithCodeList_bu($bu_id);
                if(!empty($dept_data))
                {
                    foreach($dept_data as $dept)
                    {
                        $options .= "<option value='".$dept['id']."' selected='selected'>".$dept['unitcode']." ".$dept['deptname']."</option>";
                    }
                }
            }
            $this->_helper->json(array('options' => $options));
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
			$announcementsModel = new Default_Model_Announcements();
			$data = array('isactive'=>0,'modifiedby'=>$loginUserId,'modifiedby_role'=>$loginuserRole,
									'modifiedby_group'=>$loginuserGroup,'modifieddate'=>gmdate("Y-m-d H:i:s"));
			$where = array('id=?'=>$id);
			$Id = $announcementsModel->SaveorUpdateAnnouncementsData($data, $where);
			if($Id == 'update')
			{
				$menuID = ANNOUNCEMENTS;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
				$messages['message'] = 'Announcement deleted successfully.';
				$messages['msgtype'] = 'success';
			}   
			else
			{
	        	$messages['message'] = 'Announcement cannot be deleted.';
	            $messages['msgtype'] = 'error';
	       	}
		}
		else
		{ 
			$messages['message'] = 'Announcement cannot be deleted.';
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

	public function uploadsaveAction() 
    {
    	$user_id = sapp_Global::_readSession('id');
        $filedata = array();
        if(isset($_FILES["myfile"]))
        {
            $fileName = $_FILES["myfile"]["name"];
            $fileName = preg_replace('/[^a-zA-Z0-9.\']/', '_', $fileName);			  	
            $newName  = time().'_'.$user_id.'_'.str_replace(' ', '_', $fileName);

            move_uploaded_file($_FILES["myfile"]["tmp_name"],CA_TEMP_UPLOAD_PATH.$newName);

            $filedata['original_name'] = $fileName;
            $filedata['new_name'] = $newName;
            $this->_helper->json(array('filedata' => $filedata));
        }
    }

    public function uploaddeleteAction()
    {	
    	$attData =array();
        if(isset($_POST["op"]) && $_POST["op"] == "delete" && isset($_POST['doc_new_name']))
        {
        	$filePath = CA_UPLOAD_PATH.$_POST['doc_new_name']; 
        	
        	if(isset($_POST["a_id"]) && $_POST["a_id"] != '' && isset($_POST["status"])){
        		
        		// Update attachments field in database by removing deleted attachment
        		$announcementsModel = new Default_Model_Announcements();
				$annData = $announcementsModel->getAnnouncementsDatabyID($_POST["a_id"]);
				if($annData[0]['attachments']){
					$attData = json_decode($annData[0]['attachments'],true);

					foreach ($attData as $key => $attachment) {
						if ($attachment['new_name'] == $_POST['doc_new_name']) {
							unset($attData[$key]);
							break;
						}
					}
					
					$data = array('attachments'=>(count($attData)>0)?json_encode($attData):null);
					$where = array('id=?'=>$_POST["a_id"]);
					$announcementsModel->SaveorUpdateAnnouncementsData($data, $where);
				}
				
				// Get file path of attachment to be removed.
				if ($_POST["status"] == 1) {
					// Path of attachment files when Announcement saved as draft
					$filePath = CA_TEMP_UPLOAD_PATH.$_POST['doc_new_name'];
				}
		    	                                	
	        	// Remove attachment files from upload folder.
	            if (file_exists($filePath)) {
	                unlink($filePath);
	            }
				
	            // Update photo gallery with removed attachment
	            if($_POST["status"]==2){ $path = CA_FILES_PATH; } else { $path = CA_FILES_TEMP_PATH; }
	        	
	            $this->view->path = $path;
				$this->view->attachments = $attData; 
				
        	} else {
		    	                                	
	        	// Remove attachment files from upload folder.
	            if (file_exists($filePath)) {
	                unlink($filePath);
	            }
		    	$this->_helper->json(array());
        	}
        }
    }
}

