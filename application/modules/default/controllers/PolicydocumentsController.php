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
class Default_PolicydocumentsController extends Zend_Controller_Action
{
	private $documentsModel;
	private $options;
	private $loggedInUser = '';
	private $loggedInUserGroup = '';
	private $loggedInUserRole = '';

	public function preDispatch()
	{
		/**
		** for ajax calls
		**/
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('uploaddoc','json')->initContext();
		$ajaxContext->addActionContext('uploadmultipledocs','json')->initContext();
		$ajaxContext->addActionContext('deletedocument','json')->initContext();
	}
	public function init()
	{
		$this->_options = $this->getInvokeArg('bootstrap')->getOptions();
		
		/** Instantiate documents model **/
		$this->documentsModel = new Default_Model_Documents();

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
	/**
	** function to view documents based on category id
	** if category id is not sent, returns all documents object
	**	@parameters 
	**		string category id
	**
	**	@return parameteres
	**		documents object
	**/
	public function indexAction()
	{
		try
		{
			/** capture category id **/
			$id = $this->getRequest()->getParam('id');
			$id = (int)$id;

			if(!empty($id) && $id != 0)
			{
				$categoryObj = $this->documentsModel->getCategoryById($id);
				if(!empty($categoryObj))
				{
					/** check for ajax call **/
					$call = $this->_getParam('call');
					if($call == 'ajaxcall')
							$this->_helper->layout->disableLayout();	
					
					/** capture request parameters **/
					$objname = $this->_getParam('objname');
					$refresh = $this->_getParam('refresh');
					$dashboardcall = $this->_getParam('dashboardcall');
					$data = array();
					$searchQuery = '';
					$searchArray = array();
					$tablecontent='';	
					
					/** check for refresh button event 
					**  If yes, build default parameters
					**	else, build parameters accordingly
					**/
					if($refresh == 'refresh')
					{
						if($dashboardcall == 'Yes')
							$perPage = DASHBOARD_PERPAGE;
						else	
							$perPage = PERPAGE;

						$sort = 'DESC';$by = 'd.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
					}
					else 
					{
						/** build sort parameter **/
						$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';

						/** build order by parameter **/
						$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'d.modifieddate';
						
						/** get records per page parameter **/
						if($dashboardcall == 'Yes')
							$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
						else 
							$perPage = $this->_getParam('per_page',PERPAGE);

						/** set page number parameter **/
						$pageNo = $this->_getParam('page', 1);
						
						/** build search parameters **/
						$searchData = $this->_getParam('searchData');	
						$searchData = rtrim($searchData,',');					
					}
					
					/**
					**	based on search, sort, pagination, order by parameters 
					**  get data object to build documents grid
					**/
					$dataTmp = $this->documentsModel->getDocumentsGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall,$id);
					array_push($data,$dataTmp);
					
					/** pass category id to grid - add link **/
					$data[0]['category_id'] = $id;

					/** pass category name to grid **/
					$data[0]['categoryName'] = $categoryObj['category'];
				
					/**
					** to display "Add Multiple Documents" link, check add privileges for the logged in user 
					**/
					$popConfigPermission = array();
					if(sapp_Global::_checkprivileges(MANAGE_POLICY_DOCS,$this->loggedInUserGroup,$this->loggedInUserRole,'add') == 'Yes'){
								array_push($popConfigPermission,'addmultiple');
					}  
					$this->view->popConfigPermission = $popConfigPermission;


					/** send data objects to view **/
					$this->view->dataArray = $data;
					$this->view->call = $call ;
					$this->view->nodata = '';
					
					/** send flash messages to view **/
					$this->view->messages = $this->_helper->flashMessenger->getMessages();
				}
				else
				{
					$this->view->ermsg = 'nocategory';
				}
			}
			else
			{
				$this->view->ermsg = 'nodata';
			}
		}
		catch(Exception $e)
		{
			
			$this->view->nodata = 'exception';
		}
	}
	/**
	**	function to display add form
	**  populate categories 
	**  if post values are available redirects to save function
	**/
	public function addAction()
	{
		/**
		** capturing referral URL, to redirect after adding policy document
		**/		
		$redirectUrl = '';
		if(isset($_SERVER['HTTP_REFERER']))
		{
			$httpReferrer = $_SERVER['HTTP_REFERER'];
			$redirectUrl = str_replace(BASE_URL,'',$httpReferrer);
			if(strpos($redirectUrl,'add') === false)
				$this->view->redirectUrl = $redirectUrl;
		}
		
		/** capture category id from url **/
		$category_id = (int)$this->_request->getParam('cat');
		
		/**
		** Initiating category form
		** and assigning action
		**/
		$documentsAddForm = new Default_Form_Policydocuments();
		if($category_id)
			$documentsAddForm->setAttrib('action',BASE_URL.'policydocuments/add/cat/'.$category_id);
		else
			$documentsAddForm->setAttrib('action',BASE_URL.'policydocuments/add');
		
		$this->view->form = $documentsAddForm;
		$this->view->userid = $this->loggedInUser;

		$documentsAddForm->category_id->addMultiOption('','Select Category');
			
		/**
		** Get all categories
		** populate categories select control
		**/
		$categoriesModel = new Default_Model_Categories();
		$categoriesObj = $categoriesModel->getCategories('add');
		if(!empty($categoriesObj))
		{
			foreach($categoriesObj as $categories)
			{
				$documentsAddForm->category_id->addMultiOption($categories['id'],utf8_encode($categories['category']));
			}
		}

		if(!empty($category_id))
		{
			/** check for valid category id
			** else redirect to category not found page
			**/
			$categoryObj = $categoriesModel->getCategoryById($category_id);
			if(!empty($categoryObj))
			{
				$this->view->category_id = $category_id;
				$documentsAddForm->setDefault('category_id',$category_id);
			}
			
		}
		
		/**
		** to display "Add category" link, check add privileges for the logged in user 
		**/
		$popConfigPermission = array();
		if(sapp_Global::_checkprivileges(POLICY_DOCS_CATEGORIES,$this->loggedInUserGroup,$this->loggedInUserRole,'add') == 'Yes'){
					array_push($popConfigPermission,'category');
		}  
		$this->view->popConfigPermission = $popConfigPermission;

		/**
		** if post variables are available
		** redirect to save function
		**/
		if($this->getRequest()->getPost())
		{
			$this->save($documentsAddForm);
		}
	}
	/**
	**	function to save document details
	**  if post values are available will create new policy document
	**	@parameters 
	**		string document  (mandatory)
	**		int	category id		(mandatory)
	**		text	description (optional)
	**		string version (optional)
	**		text	document	(optional)
	**
	**	@return parameteres
	**		@object success/failure messages		
	**/
	public function save($documentsAddForm)
	{
		if($documentsAddForm->isValid($this->_request->getPost()))
		{
			/** capture form values **/
			$document_name = $this->_request->getParam('document_name');
			$category_id = $this->_request->getParam('category_id');
			$document_version = $this->_request->getParam('document_version');
			$description = $this->_request->getParam('description');
			$attachment = $this->_getParam('attachment',null);
			$redirectUrl = $this->_request->getParam('redirectUrl');
			
			$org_names = $this->_getParam('file_original_names',null);
			$new_names = $this->_getParam('file_new_names',null);
			$documents_array = array();
			if($new_names != '')
					$documents_array[] = array("original_name" => $org_names, "new_name" => $new_names);
		  
			/** prepare data array to insert into database **/
			$data = array(
						'document_name' => $document_name,
						'category_id' => $category_id,
						'document_version' => $document_version,
						'description' => $description,
						'file_name' => count($documents_array) > 0?json_encode($documents_array):null,
						'isactive' =>   1,
						'modifiedby' =>  $this->loggedInUser,
						'createdby' =>  $this->loggedInUser,
						'modifieddate' => gmdate("Y-m-d H:i:s"),
						'createddate' =>  gmdate("Y-m-d H:i:s"),
			);

			/** save document details **/
			$id = $this->documentsModel->savePolicyDocument($data);
					

			if($id)
			{
				/** insert into log manager table **/
				sapp_Global::logManager(MANAGE_POLICY_DOCS,1,$this->loggedInUser,$id);
		
				/** if document details are saved successfully
				** move the uploaded document from policy_doc_temp to policydocs folder
				**/
				if(!empty($new_names) && file_exists(POLICY_DOC_TEMP_UPLOAD_PATH.$new_names))
				{
					copy(POLICY_DOC_TEMP_UPLOAD_PATH.$new_names, POLICY_DOC_UPLOAD_PATH.$new_names);
					unlink(POLICY_DOC_TEMP_UPLOAD_PATH.$new_names);
				}
				
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("success" => "Policy document added successfully"));
			}
			else
			{
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("failure" => "Failed to add policy document. Please try again."));
			}

			/**
			** if category id exists redirect to the respective category page
			** else if redirect url exists redirect to the URL
			** else to policy documents page
			**/
			if($category_id)
				$this->_redirect('policydocuments/id/'.$category_id);
			else if($redirectUrl)
				$this->_redirect($redirectUrl);
			else
				$this->_redirect('policydocuments');
		}
		else
		{
			$validationMsgs = $documentsAddForm->getMessages();
			foreach($validationMsgs as $key => $val)
			{
				foreach($val as $key2 => $val2)
				{
					$msgarray[$key] = $val2;
					break;
				}
			}

			$file_original_names = $this->_getParam('file_original_names',null);
			$file_new_names = $this->_getParam('file_new_names',null);
			
			if(empty($file_original_names))
			{
				$msgarray['doc_attachment'] = 'Please upload document';
			}
			
			$this->view->file_original_names = $file_original_names;
			$this->view->file_new_names = $file_new_names;
			$msgarray['file_original_names'] =  $file_original_names;
			$msgarray['file_new_names'] =  $file_new_names;
			$this->view->msgarray = $msgarray;
		}
	}

	/**
	**	this action is used to display edit form
	**  if post values are available will redirect to new policy document
	**/
	public function editAction()
	{
		/** capture document id to edit **/
		$docId = (int) $this->_request->getParam('id');
		
		/** check if document id is numeric 
		** if yes, then get document details and populate the edit form
		** if no, display no data message
		**/
		if(is_numeric($docId) && $docId > 0)
		{
			/**
			** capturing referral URL, to redirect after adding policy document
			**/		
			$redirectUrl = '';
			if(isset($_SERVER['HTTP_REFERER']))
			{
				$httpReferrer = $_SERVER['HTTP_REFERER'];
				$redirectUrl = str_replace(BASE_URL,'',$httpReferrer);
				if(strpos($redirectUrl,'edit') === false)
					$this->view->redirectUrl = $redirectUrl;
			}

			/** Initiate document form **/
			$documentsAddForm = new Default_Form_Policydocuments();
			$this->view->form = $documentsAddForm;
			$this->view->userid = $this->loggedInUser;
			
		
			$documentsAddForm->setAttrib('action',BASE_URL.'policydocuments/edit/id/'.$docId);	

			/** get document details based on document id **/
			$res = $this->documentsModel->getDocumentsById($docId);
			if(!empty($res))
			{
				/** populate edit form **/
				$documentsAddForm->populate($res);	
				if($res['file_name']){
					$new = array();
					$ori = array();

					$attachments = json_decode($res['file_name'],true);
					foreach ($attachments as $k => $v) {
						$new[] = $v["new_name"];
						$ori[] = $v["original_name"];
					}
					$msg['file_original_names'] = implode(',', $ori);
					$msg['file_new_names'] = implode(',', $new);
					$this->view->msgarray = $msg;								
					$this->view->file_name = $res['file_name'];
				}							
				
				/**
				** to display "Add category" link, check add privileges for the logged in user 
				**/
				$popConfigPermission = array();
				if(sapp_Global::_checkprivileges(POLICY_DOCS_CATEGORIES,$this->loggedInUserGroup,$this->loggedInUserRole,'add') == 'Yes'){
							array_push($popConfigPermission,'category');
				} 
				$this->view->popConfigPermission = $popConfigPermission;

				/**
				** Get all categories
				** populate categories select control
				**/
				$categoriesModel = new Default_Model_Categories();
				$categoriesObj = $categoriesModel->getCategories('add');
				if(!empty($categoriesObj))
				{
					$documentsAddForm->category_id->addMultiOption('','Select Category');
					foreach($categoriesObj as $categories)
					{
						$documentsAddForm->category_id->addMultiOption($categories['id'],utf8_encode($categories['category']));
					}
				}
				$documentsAddForm->setDefault('category_id',$res['category_id']);
				$this->view->category_id = $res['category_id'];
			
				/** change submit button label to Update **/
				$documentsAddForm->submit->setLabel('Update');
				$this->view->ermsg = '';

				/**
				** if post variables are available
				** redirect to update function
				** with form object and document id
				**/
				if($this->getRequest()->getPost())
				{
					$this->update($documentsAddForm,$docId);
				}
			}
			else
			{
				$this->view->ermsg = 'nodata';
			}
			$this->view->id = $docId;
		}
		else
		{
			$this->view->ermsg = 'invalidUrl';
		}
		
	}
	/**
	**	function to update document details
	**  if post values are available will create new policy document
	**	@parameters 
	**		string document  (mandatory)
	**		int	category id		(mandatory)
	**		text	description (optional)
	**		string version (optional)
	**		text	document	(optional)
	**
	**	@return parameteres
	**		@object success/failure messages		
	**/
	public function update($documentsAddForm,$id)
	{
		if($documentsAddForm->isValid($this->_request->getPost()))
		{
			/** capture form values **/
			$document_name = $this->_request->getParam('document_name');
			$category_id = $this->_request->getParam('category_id');
			$document_version = $this->_request->getParam('document_version');
			$description = $this->_request->getParam('description');
			$attachment = $this->_getParam('attachment',null);
			$redirectUrl = $this->_request->getParam('redirectUrl'); 
			
			$org_names = $this->_getParam('file_original_names',null);
			$new_names = $this->_getParam('file_new_names',null);
			$documents_array = array();
			if($new_names != '')
					$documents_array[] = array("original_name" => $org_names, "new_name" => $new_names);
		  
			/** prepare data array **/
			$data = array(
						'document_name' => $document_name,
						'category_id' => $category_id,
						'document_version' => $document_version,
						'description' => $description,
						'file_name' => count($documents_array) > 0?json_encode($documents_array):null,
						'modifiedby' =>  $this->loggedInUser,
						'modifieddate' => gmdate("Y-m-d H:i:s"),						
			);
			
			/** prepare where clause data **/
			$where = array('id=?'=>$id);  
			
			/** update document details **/
			$res = $this->documentsModel->savePolicyDocument($data,$where);
			
			if($res)
			{
				/** insert into log manager table **/
				sapp_Global::logManager(MANAGE_POLICY_DOCS,2,$this->loggedInUser,$id);
	
				/** if document details are saved successfully
				** move the uploaded document from policy_doc_temp to policydocs folder
				**/
				if(file_exists(POLICY_DOC_TEMP_UPLOAD_PATH.$new_names))
				{
					copy(POLICY_DOC_TEMP_UPLOAD_PATH.$new_names, POLICY_DOC_UPLOAD_PATH.$new_names);
					unlink(POLICY_DOC_TEMP_UPLOAD_PATH.$new_names);
				}

				$this->_helper->getHelper("FlashMessenger")->addMessage(array("success" => "Policy document updated successfully"));
			}
			else
			{
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("failure" => "Failed to update policy document. Please try again."));
			}

			//if($redirectUrl)
				//$this->_redirect($redirectUrl);
			//else
				$this->_redirect('policydocuments/id/'.$category_id);
		}
		else
		{
			$validationMsgs = $documentsAddForm->getMessages();
			foreach($validationMsgs as $key => $val)
			{
				foreach($val as $key2 => $val2)
				{
					$msgarray[$key] = $val2;
					break;
				}
			}
			$file_original_names = $this->_getParam('file_original_names',null);
			$file_new_names = $this->_getParam('file_new_names',null);

			if(empty($file_original_names))
			{
				$msgarray['doc_attachment'] = 'Please upload document';
			}
			
			$this->view->file_original_names = $file_original_names;
			$this->view->file_new_names = $file_new_names;
			$msgarray['file_original_names'] =  $file_original_names;
			$msgarray['file_new_names'] =  $file_new_names;
			
			$this->view->msgarray = $msgarray;
		}
	}
	/**
	** function to view document details
	** based on document id
	**/
	public function viewAction()
	{
		$docId = (int) $this->_request->getParam('id');
		$objName = 'policydocuments';
		if(is_numeric($docId) && $docId > 0)
		{
			/**
			** Initiating category form
			** and assigning action
			**/
			$documentsAddForm = new Default_Form_Policydocuments();
			$this->view->form = $documentsAddForm;

			/**
			** to display "edit" button, check add privileges for the logged in user 
			**/
			$popConfigPermission = array();
			if(sapp_Global::_checkprivileges(MANAGE_POLICY_DOCS,$this->loggedInUserGroup,$this->loggedInUserRole,'edit') == 'Yes'){
						array_push($popConfigPermission,'edit');
			}  
			$this->view->popConfigPermission = $popConfigPermission;

			/** get document details **/
			$res = $this->documentsModel->getDocumentsById($docId);
			if(!empty($res))
			{
				$documentsAddForm->populate($res);	
				$this->view->file_name = $res['file_name'];

				/**
				** Get category by id
				** populate category in select control
				**/
				$categoriesModel = new Default_Model_Categories();
				$categoriesObj = $categoriesModel->getCategoryById($res['category_id']);
				if(!empty($categoriesObj))
				{
					$documentsAddForm->category_id->addMultiOption($categoriesObj['id'],utf8_encode($categoriesObj['category']));
					$res['category_id']= $categoriesObj['category'];
				}
				else 
				{
					$res['category_id']="";
				}
				$documentsAddForm->setDefault('category_id',$res['category_id']);
				$this->view->category_id = $res['category_id'];

				/** remove submit button 
				** edit and cancel buttons is available in view page
				**/
				$documentsAddForm->removeElement('submit');

				/** disable form elements **/
				$elements = $documentsAddForm->getElements();
				if(count($elements)>0)
				{
					foreach($elements as $key=>$element)
					{
						if(($key!="Cancel")&&($key!="Edit") && ($key!="Delete")&&($key!="Attachments")){
							$element->setAttrib("disabled", "disabled");
						}
					}
				}
				$this->view->id = $docId;
				$this->view->controllername=$objName;
				$this->view->data = $res;
				$this->view->ermsg = '';
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
	** function to delete document 
	** call initiated from grid
	**/
	public function deleteAction()
	{
		try
		{
			/** capture document id **/
			$docId = (int) $this->_request->getParam('objid');
			$deleteflag= $this->_request->getParam('deleteflag');
			$res_cat = $this->documentsModel->getDocumentsById($docId);

			if(is_numeric($docId) && $docId > 0)
			{
				/** prepare data array  and where clause
				** to update document isactive status to 0
				**/
				$data = array(
						'isactive' => 0,
						'modifiedby' =>  $this->loggedInUser,
						'modifieddate' => gmdate("Y-m-d H:i:s")
				);
				$where = array('id=?'=>$docId);
				
				/** update document details **/
			  $res = $this->documentsModel->savePolicyDocument($data, $where);
				
                if($res == 'update')
				{
					/** insert into log manager table **/
					sapp_Global::logManager(MANAGE_POLICY_DOCS,3,$this->loggedInUser,$docId);
					
					$messages['message'] = 'Policy document deleted successfully.';
					$messages['msgtype'] = 'success';
				}
				else
				{
					$messages['message'] = 'Failed to delete policy document. Please try again.';
					$messages['msgtype'] = 'error';
				}
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
		}
		catch(Exception $e)
		{
				$messages['message'] = 'Failed to delete policy document. Please try again.';
				$messages['msgtype'] = 'error';
		}	
		/** to refresh the page after delete document transaction 
		** pass flagtype as process in the response to javascript function
		**/
		$messages['flagtype'] = 'process';
		$messages['id'] = $res_cat['category_id'];
		$this->_helper->json($messages);		
	}
	
	/**
	** function to upload selected document to  policy_doc_temp folder
	**/
	public function uploaddocAction()
	{
		//print_r($_FILES);
		$options_data = "";
		
		// Validate file with size greater than default(Upload Max Filesize)limit
		if (isset($_FILES["myfile"]) && ($_FILES["myfile"]["size"] == 0 || $_FILES["myfile"]["size"] > (2*1024*1024))) {
			$this->_helper->json(array('error' => 'filesize'));
		}
		else if(isset($_FILES["myfile"])) 
		{
			$fileName = $_FILES["myfile"]["name"];
            $fileName = preg_replace('/[^a-zA-Z0-9.\']/', '_', $fileName);			  	
            $newName  = time().'_'.$this->loggedInUser.'_'.str_replace(' ', '_', $fileName);

            move_uploaded_file($_FILES["myfile"]["tmp_name"],POLICY_DOC_TEMP_UPLOAD_PATH.$newName);

            $filedata['original_name'] = $fileName;
            $filedata['new_name'] = $newName;
            $this->_helper->json(array('filedata'=>$filedata));
		}
	}
	/**
	** function to delete document
	** call initaited from add / edit forms 
	** on click of cross button besided the file name
	**/
	public function deletedocumentAction()
	{
		if(isset($_POST["op"]) && isset($_POST['doc_new_name']))
        {
			if($_POST["op"] == "delete")
	        	$filePath = POLICY_DOC_UPLOAD_PATH.$_POST['doc_new_name']; 
			else if($_POST["op"] == "deletetemp")
				$filePath = POLICY_DOC_TEMP_UPLOAD_PATH.$_POST['doc_new_name']; 
        	
        	if(isset($_POST["a_id"]) && $_POST["a_id"] != '' && $_POST["op"] == "delete"){
        		// Update documents data in database by removing deleted document detailss
        		$docData = (int) $this->documentsModel->getDocumentsById($_POST["a_id"]);
				$fileData = '';
				if($docData['file_name']){
					$fileData = json_decode($docData['file_name'],true);

					foreach ($fileData as $key => $document) {
						if ($document['new_name'] == $_POST['doc_new_name']) {
							unset($fileData[$key]);
							break;
						}
					}
					
					$data = array('file_name'=>(count($fileData)>0)?json_encode($fileData):null);
					$where = array('id=?'=>$_POST["a_id"]);
					$this->documentsModel->savePolicyDocument($data, $where);
				}
				
				// Remove document files from upload folder.
	            if (file_exists($filePath)) {
	                unlink($filePath);
	            }
				
	            // Update photo gallery with removed document
	            $this->view->path = POLICY_DOC_UPLOAD_PATH;
				$this->view->attachments = $fileData; 
				
        	} else {
		    	                        
				
	        	// Remove document files from upload folder.
	            if (file_exists($filePath)) {
	                unlink($filePath);
	            }
		    	$this->_helper->json(array());
        	}
        }
	}
	
	/**
	** Function used to allow adding of multiple policy documents at a time
	**/
	public function addmultipleAction()
	{
		try
		{
			/**
			** to display "Add Multiple Documents" link, check add privileges for the logged in user 
			**/
			$popConfigPermission = array();
			if(sapp_Global::_checkprivileges(MANAGE_POLICY_DOCS,$this->loggedInUserGroup,$this->loggedInUserRole,'add') == 'Yes'){
							
				/**
				** capturing referral URL, to redirect after adding policy document
				**/		
				$redirectUrl = '';
				if(isset($_SERVER['HTTP_REFERER']))
				{
					$httpReferrer = $_SERVER['HTTP_REFERER'];
					$redirectUrl = str_replace(BASE_URL,'',$httpReferrer);
					if(strpos($redirectUrl,'addmultiple') === false)
						$this->view->redirectUrl = $redirectUrl;
				}
				
				/** capture category id **/
				$category_id = $this->getRequest()->getParam('id');
				$category_id = (int)$category_id;

				/**
				** if category id is available
				** load the form
				** else redirect to invalid url page
				**/
				if($category_id)
				{
					/**
					** Initiating category form
					** and assigning action
					**/
					$multipleDocsForm = new Default_Form_Policydocuments();
					$this->view->form = $multipleDocsForm;
					$multipleDocsForm->setAttrib('action',BASE_URL.'policydocuments/addmultiple/'.$category_id);	

					/**
					** Get category by id
					** populate category in select control
					**/
					$categoriesModel = new Default_Model_Categories();
					$categoriesObj = $categoriesModel->getCategoryById($category_id);
					if(!empty($categoriesObj))
					{
						$multipleDocsForm->category_id->addMultiOption($categoriesObj['id'],utf8_encode($categoriesObj['category']));
					}
					$multipleDocsForm->setDefault('category_id',$category_id);
					$this->view->category_id = $category_id;

					if($this->getRequest()->getPost())
					{
						$this->saveMultipleDoc($multipleDocsForm,$category_id,$redirectUrl);
					}
				}
				else
				{
					$this->view->ermsg = 'invalidUrl';
					return;
				}
			}
			else
			{
				$this->view->ermsg = 'noprivilege';
			}

		}
		catch(Exceptin $e)
		{
			//print_r($e);
		}
	}
	
	public function saveMultipleDoc($multipleDocsForm,$category_id,$redirectUrl)
	{
		try
		{
			
				/** capture form values **/
				$document_nameArr = $this->_request->getParam('document_name');
				$document_versionArr = $this->_request->getParam('document_version');
				$descriptionArr = $this->_request->getParam('description');
				$redirectUrl = $this->_request->getParam('redirectUrl');
				
				$org_namesArr = $this->_getParam('file_original_names',null);
				$new_namesArr = $this->_getParam('file_new_names',null);

				for($i = 0; $i < sizeof($new_namesArr); $i++)
				{
					$document_name = $document_nameArr[$i];
					$document_version = $document_versionArr[$i];
					$description = $descriptionArr[$i];
					$org_names = $org_namesArr[$i];
					$new_names = $new_namesArr[$i];

					$documents_array = array();
					if($new_names != '')
							$documents_array[] = array("original_name" => $org_names, "new_name" => $new_names);
				  
					/** prepare data array to insert into database **/
					$data = array(
								'document_name' => $document_name,
								'category_id' => $category_id,
								'document_version' => $document_version,
								'description' => $description,
								'file_name' => count($documents_array) > 0?json_encode($documents_array):null,
								'isactive' =>   1,
								'modifiedby' =>  $this->loggedInUser,
								'createdby' =>  $this->loggedInUser,
								'modifieddate' => gmdate("Y-m-d H:i:s"),
								'createddate' =>  gmdate("Y-m-d H:i:s"),
					);
					
					/** save document details **/
					$id = $this->documentsModel->savePolicyDocument($data);
							

					if($id)
					{

						/** insert into log manager table **/
						sapp_Global::logManager(MANAGE_POLICY_DOCS,1,$this->loggedInUser,$id);
						
						/** if document details are saved successfully
						** move the uploaded document from policy_doc_temp to policydocs folder
						**/
						if(!empty($new_names) && file_exists(POLICY_DOC_TEMP_UPLOAD_PATH.$new_names))
						{
							copy(POLICY_DOC_TEMP_UPLOAD_PATH.$new_names, POLICY_DOC_UPLOAD_PATH.$new_names);
							unlink(POLICY_DOC_TEMP_UPLOAD_PATH.$new_names);
						}
					}
				}
				
			
			/**
			** if category id exists redirect to the respective category page
			** else if redirect url exists redirect to the URL
			** else to policy documents page
			**/
			if($category_id)
				$this->_redirect('policydocuments/id/'.$category_id);
			else if($redirectUrl)
				$this->_redirect($redirectUrl);
			
		}
		catch(Exceptin $e)
		{
			//print_r($e);
		}
	}
	
	

	public function uploadmultipledocsAction()
	{
		$options_data = "";
		
		// Validate file with size greater than default(Upload Max Filesize)limit
		if ($_FILES["fileupload"]["size"] == 0 || $_FILES["fileupload"]["size"] > (2*1024*1024)) {
			$this->_helper->json(array('error' => 'filesize'));
		}
		else if(isset($_FILES["fileupload"])) 
		{
			$fileName = $_FILES["fileupload"]["name"];
            $fileName = preg_replace('/[^a-zA-Z0-9.\']/', '_', $fileName);			  	
            $newName  = time().'_'.$this->loggedInUser.'_'.str_replace(' ', '_', $fileName);

            move_uploaded_file($_FILES["fileupload"]["tmp_name"],POLICY_DOC_TEMP_UPLOAD_PATH.$newName);

            $filedata['original_name'] = $fileName;
            $filedata['new_name'] = $newName;
            $this->_helper->json(array('filedata'=>$filedata));
		}
	}
}
?>