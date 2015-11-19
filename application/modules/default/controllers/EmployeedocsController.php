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

class Default_EmployeedocsController extends Zend_Controller_Action
{

	private $options;
	public function preDispatch()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('edit', 'html')->initContext();
		$ajaxContext->addActionContext('uploaddelete', 'html')->initContext();		
	}

	public function init()
	{
		$employeeModel = new Default_Model_Employee();
		$this->_options= $this->getInvokeArg('bootstrap')->getOptions();
	}
	public function indexAction()
	{
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);

		 	if(in_array('employeedocs',$empOrganizationTabs))
		 	{
			 	$auth = Zend_Auth::getInstance();
			 	if($auth->hasIdentity())
			 	{
			 		$loginUserId = $auth->getStorage()->read()->id;
					$loginuserGroup = $auth->getStorage()->read()->group_id;
			 		$loginUserRole = $auth->getStorage()->read()->emprole;
		 		}

			 	$id = $this->getRequest()->getParam('userid');
			 	
			 	try
			 	{
			 		if($id && is_numeric($id) && $id>0 && $id!=$loginUserId && ($loginuserGroup == HR_GROUP ||$loginuserGroup == MANAGEMENT_GROUP || $loginUserRole == SUPERADMIN))
					{
						$employeeModal = new Default_Model_Employee();
						$empdata = $employeeModal->getActiveEmployeeData($id);
						if(!empty($empdata))
						{
							$empDocuModel = new Default_Model_Employeedocs();
							$empDocuments = $empDocuModel->getEmpDocumentsByFieldOrAll('user_id',$id);

							$this->view->empDocuments = $empDocuments;
						}
						
						$usersModel = new Default_Model_Users();
						$employeeData = $usersModel->getUserDetailsByIDandFlag($id);
						if(!empty($employeeData))
							$this->view->employeedata = $employeeData[0];
						$this->view->id = $id;
						$this->view->empdata = $empdata;
					} else {
				   		$this->view->rowexist = "norows";
					}
			 	}
			 	catch(Exception $e) {
		 			$this->view->rowexist = "norows";
		 		}
		 		// Show message to user when document was deleted by other user.
		 		$this->view->messages = $this->_helper->flashMessenger->getMessages();
			}else{
		 		$this->_redirect('error');
		 	}
		}else{
			$this->_redirect('error');
		}
	}
	
	public function viewAction()
	{
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);

		 	if(in_array('employeedocs',$empOrganizationTabs))
		 	{
			 	$auth = Zend_Auth::getInstance();
			 	if($auth->hasIdentity())
			 	{
			 		$loginUserId = $auth->getStorage()->read()->id;
		 		}

			 	$id = $this->getRequest()->getParam('userid');
			 	
			 	try
			 	{
			 		if($id && is_numeric($id) && $id>0 && $id!=$loginUserId)
					{
						$employeeModal = new Default_Model_Employee();
						$empdata = $employeeModal->getActiveEmployeeData($id);
						if(!empty($empdata))
						{
							$empDocuModel = new Default_Model_Employeedocs();
							$empDocuments = $empDocuModel->getEmpDocumentsByFieldOrAll('user_id',$id);

							$this->view->empDocuments = $empDocuments;
						}
						
						$usersModel = new Default_Model_Users();
						$employeeData = $usersModel->getUserDetailsByIDandFlag($id);
						if(!empty($employeeData))
							$this->view->employeedata = $employeeData[0];
						$this->view->id = $id;
						$this->view->empdata = $empdata;
					} else {
				   		$this->view->rowexist = "norows";
					}
			 	}
			 	catch(Exception $e) {
		 			$this->view->rowexist = "norows";
		 		}
			}else{
		 		$this->_redirect('error');
		 	}
		}else{
			$this->_redirect('error');
		}
	}
	
	public function saveAction()
	{
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$empDocuModel = new Default_Model_Employeedocs();
		
		$id = $this->getRequest()->getParam('id');
		$userid = $this->getRequest()->getParam('userid');
		$doc_name = $this->getRequest()->getParam('doc_name');
		$file_original_names = $this->getRequest()->getParam('file_original_names');
		$file_new_names = $this->getRequest()->getParam('file_new_names');
		
		$empDocumentsCount = $empDocuModel->getEmpDocumentsByFieldOrAll('user_id',$userid);
		if(count($empDocumentsCount) < EMP_MAX_DOCS)
		{
			$org_names = explode(',', $file_original_names);
			$new_names = explode(',', $file_new_names);
	        $attachment_array = array();
	
			for ($i=0; $i < count($org_names); $i++)
	        {
	        	if($new_names[$i] != '')
	            	$attachment_array[] = array("original_name" => $org_names[$i], "new_name" => $new_names[$i]);
			}
			
			$empDocuments = $empDocuModel->checkDocNameByUserIdAndDocId($userid, $doc_name);
			
			$count_emp_docs = count($empDocuments);
			$exception = 'no';
				try{
					if(count($new_names) > 0 && $count_emp_docs==0)
	                {
						foreach ($new_names as $n)
	                    {
	                    	if($n != '')
	                        {
	                        	if(file_exists(EMP_DOC_TEMP_UPLOAD_PATH.$n))
	                            {
	                            	copy(EMP_DOC_TEMP_UPLOAD_PATH.$n, EMP_DOC_UPLOAD_PATH.$n);
	                                unlink(EMP_DOC_TEMP_UPLOAD_PATH.$n);
	                           	}
							}
						}
	              	}
				}catch (Exception $e){
					$exception = 'yes';
				}
			
			if($exception == 'no' && $count_emp_docs==0)
			{
				$data = array(
								'user_id' => $userid,
								'name' => $doc_name,
		                        'attachments' => count($attachment_array) > 0?json_encode($attachment_array):null,
		                        'modifiedby' => $loginUserId,
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
					$where = '';
					$actionflag = 1;
				}
				$recordId = $empDocuModel->SaveorUpdateEmpDocuments($data, $where);
				$menuID = EMPLOYEE;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$userid);
				
				$this->_helper->json(array('result' => 'success'));
			} else {
				if($count_emp_docs>0)
					$this->_helper->json(array('result' => 'exists'));
				else
					$this->_helper->json(array('result' => 'error'));
			}
		}else
			$this->_helper->json(array('result' => 'max'));
	}
	
	public function deleteAction()
	{
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->_request->getParam('doc_id');
		$messages['message'] = '';
		$messages['msgtype'] = '';
		$count = 0;
		$actionflag = 3;
		if($id)
		{
			$empDocuModel = new Default_Model_Employeedocs();
			$data = array('isactive'=>0,	
							'modifiedby'=>$loginUserId,
							'modifieddate'=>gmdate("Y-m-d H:i:s")
					);
			$where = array('id=?'=>$id);
			$record_id = $empDocuModel->SaveorUpdateEmpDocuments($data, $where);
			if($record_id == 'update')
			{
				$menuID = EMPLOYEE;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
				$messages['message'] = 'Document deleted successfully.';
				$messages['msgtype'] = 'success';
			}   
			else
			{
	        	$messages['message'] = 'Document cannot be deleted.';
	            $messages['msgtype'] = 'error';
	       	}
		}
		else
		{ 
			$messages['message'] = 'Document cannot be deleted.';
			$messages['msgtype'] = 'error';
		}
		$this->_helper->json($messages);		
	}
	
	public function editAction(){
		$id = $this->_request->getParam('doc_id');
		$empDocuModel = new Default_Model_Employeedocs();
		$empDocuments = $empDocuModel->getEmpDocumentsByFieldOrAll('id',$id);
		if (!empty($empDocuments[0])) {
			$this->view->empDocuments = $empDocuments[0];
		} else {
			exit("No file");	
		}
		
	}
	
	public function updateAction(){
		
		$empDocuModel = new Default_Model_Employeedocs();
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->_request->getParam('doc_id');
		$name = $this->_request->getParam('doc_name');
		$userid = $this->_request->getParam('userid');
		
		// Get attachments
		$file_original_names = $this->getRequest()->getParam('file_original_names');
		$file_new_names = $this->getRequest()->getParam('file_new_names');
		$org_names = explode(',', $file_original_names);
		$new_names = explode(',', $file_new_names);
        $attachment_array = array();

		for ($i=0; $i < count($org_names); $i++)
        {
        	if($new_names[$i] != '')
            	$attachment_array[] = array("original_name" => $org_names[$i], "new_name" => $new_names[$i]);
		}
					
		$data = array(
						'name' => $name,
						'attachments' => count($attachment_array) > 0?json_encode($attachment_array):null,
                        'modifiedby' => $loginUserId,
                        'modifieddate'=>gmdate("Y-m-d H:i:s")
				);

		// Validate duplicate document name
		$empDocuments = $empDocuModel->checkDocNameByUserIdAndDocId($userid, $name, $id);

		$count_emp_docs = count($empDocuments);
		if($count_emp_docs==0) {
		
			$where = array('id=?'=>$id);  
			$actionflag = 2;
			
			$recordId = $empDocuModel->SaveorUpdateEmpDocuments($data, $where);
			$menuID = EMPLOYEE;
			$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$userid);
			
			$this->_helper->json(array('result' => 'success'));
		} else {
			if($count_emp_docs>0)
						$this->_helper->json(array('result' => 'exists'));
					else
						$this->_helper->json(array('result' => 'error'));
			
		}
	}
	
	public function uploadsaveAction() 
    {		
        $user_id = sapp_Global::_readSession('id');
        $filedata = array();
        
    	// Validate file with size greater than default(Upload Max Filesize)limit
        if ($_FILES["myfile"]["size"] == 0 || $_FILES["myfile"]["size"] > (2*1024*1024)) {
            $this->_helper->json(array('error' => 'filesize'));
        } else if(isset($_FILES["myfile"])) {
            $fileName = $_FILES["myfile"]["name"];
            $fileName = preg_replace('/[^a-zA-Z0-9.\']/', '_', $fileName);			  	
            $newName  = time().'_'.$user_id.'_'.str_replace(' ', '_', $fileName);

            $filedata['original_name'] = $fileName;
            $filedata['new_name'] = $newName;
            
        	if (isset($_POST["doc_id"]) && $_POST["doc_id"] != '') {
        		move_uploaded_file($_FILES["myfile"]["tmp_name"],EMP_DOC_UPLOAD_PATH.$newName);
        	} else {
        		move_uploaded_file($_FILES["myfile"]["tmp_name"],EMP_DOC_TEMP_UPLOAD_PATH.$newName);
        	}
            
            $this->_helper->json(array('filedata' => $filedata));
        }        
    }

    public function uploaddeleteAction()
    {	
    	 if(isset($_POST["op"]) && $_POST["op"] == "delete" && isset($_POST['doc_new_name']))
        {
        	$filePath = "";
        	if(isset($_POST["doc_id"]) && $_POST["doc_id"] != ''){
        		
        		// Update attachments field in database by removing deleted attachment
        		$empDocuModel = new Default_Model_Employeedocs();
				$empDocuments = $empDocuModel->getEmpDocumentsByFieldOrAll('id',$_POST["doc_id"]);
				if($empDocuments[0]['attachments']){
					$attData = json_decode($empDocuments[0]['attachments'],true);
					
					foreach ($attData as $key => $attachment) {
						if ($attachment['new_name'] == $_POST['doc_new_name']) {
							unset($attData[$key]);
							break;
						}
					}
					
					$data = array('attachments'=>(count($attData)>0)?json_encode($attData):null);
					$where = array('id=?'=>$_POST["doc_id"]);
					$empDocuModel->SaveorUpdateEmpDocuments($data, $where);
				}
				
				$filePath = EMP_DOC_UPLOAD_PATH.$_POST['doc_new_name'];	
				
				// Remove attachment files from upload folder.
	            if (file_exists($filePath)) {
	            	 unlink($filePath);
	            }
				
	            // Update photo gallery with removed attachment.
	            $this->view->path = CA_FILES_PATH;
				$this->view->attachments = $attData;
				$this->view->doc_id = $_POST["doc_id"];				
							
        	}else{
        		$filePath = EMP_DOC_TEMP_UPLOAD_PATH.$_POST['doc_new_name'];
            
	        	// Remove attachment files from upload folder.
	            if (file_exists($filePath)) {
	                unlink($filePath);
	            }
	            
	            $this->_helper->json(array());
        	}
        }
    }
    
	function downloadfilesAction()
	{
		if($_POST){
			
			$doc_id = $_POST['doc_id'];
			$file_name = isset($_POST['file_name'])? $_POST['file_name'] : '';
			$empDocuModel = new Default_Model_Employeedocs();
			$empDocuments = $empDocuModel->getEmpDocumentsByFieldOrAll('id',$doc_id);
			if (!empty($empDocuments)) {
				if($empDocuments[0]['attachments']){
					$attachments = json_decode($empDocuments[0]['attachments'],true);
					//Downloading single file
					if($file_name)
					{	$new = array();$ori = array();

							foreach ($attachments as $k => $v){
							 if($file_name == $v["new_name"])
							 {
							   $new[] = $v["new_name"];
							   $ori[] = $v["original_name"];
							 }
						}
					}
					else 
					{
					  if(count($attachments)>0){
				    	$new = array();$ori = array();
				        foreach ($attachments as $k => $v){
							$new[] = $v["new_name"];
							$ori[] = $v["original_name"];
						}
				     }
					}
				}
				
				$file_names = $new;
				$originalNames = $ori;
				$originalNamesString = implode(",", $originalNames);
				if(isset($file_name) && empty($_POST['user_id']))
				{
					$fileNameArr = explode('.',$file_name);
					$download_file_name = $empDocuments[0]['name'].'_'.$ori[0]; //to appand original name for single file download
				 	$archive_file_name = preg_replace('/[^a-zA-Z0-9\']/', '_', $download_file_name);
				}
				else {
				$archive_file_name = preg_replace('/[^a-zA-Z0-9\']/', '_', $empDocuments[0]['name']);
				}
				$file_path = EMP_DOC_UPLOAD_PATH;
	
				$temp = md5(DATE_CONSTANT.uniqid()).'.zip';
	
				$zip = new ZipArchive();
				//create the file and throw the error if unsuccessful
				if ($zip->open($file_path.$archive_file_name.$temp, ZIPARCHIVE::CREATE )!==TRUE) {
					exit("cannot open <$archive_file_name>\n");
				}
				//add each files of $file_name array to archive
				for($i=0; $i<sizeof($file_names); $i++)
				{
					$name = '';
					$count = substr_count($originalNamesString, $originalNames[$i]);
					if($count > 1)
					$name = $i.$originalNames[$i];
					else
					$name = $originalNames[$i];
					$zip->addFile($file_path.$file_names[$i],$name);
				}
				$zip->close();
				//then send the headers to foce download the zip file
				header("Content-type: application/zip");
				header("Content-Disposition: attachment; filename=".$archive_file_name.'.zip');
				header("Pragma: no-cache");
				header("Expires: 0");
				readfile($file_path.$archive_file_name.$temp);
				unlink($file_path.$archive_file_name.$temp);
				exit;
				
			} else {
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("error"=>'This document was deleted by other user just now.'));
				$auth = Zend_Auth::getInstance();
				$loginUserId = $auth->getStorage()->read()->id;
				if (!empty($_POST['user_id'])) {
					if (!empty($_POST['context']) && $_POST['context'] == 'My Employees') {
						$this->_redirect('myemployees/docedit/userid/'.$_POST['user_id']);
					} else {
						$this->_redirect('employeedocs/index/userid/'.$_POST['user_id']);				
					}
				} else {
					$this->_redirect('mydetails/documents');
				}
			}
		}
		else{
			$this->_redirect('error');
		}
	}    
}