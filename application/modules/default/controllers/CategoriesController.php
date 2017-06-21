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

class Default_CategoriesController extends Zend_Controller_Action
{
	private $categoriesModel;
	private $options;
	private $loggedInUser = '';
	private $loggedInUserGroup = '';
	private $loggedInUserRole = '';
	
	public function init()
	{
		$this->_options = $this->getInvokeArg('bootstrap')->getOptions();
		
		/** Instantiate category model **/
		$this->categoriesModel = new Default_Model_Categories();

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
		/**
		**	check for ajax call
		**/
		$call = $this->_getParam('call');
		if($call == 'ajaxcall')
				$this->_helper->layout->disableLayout();	
		
		/**
		** capture request parameters 
		**/
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

			$sort = 'DESC';$by = 'c.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else 
		{
			/** build sort parameter **/
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';

			/** build order by parameter **/
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'c.modifieddate';
			
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
		**  get data object to build categories grid
		**/
		$dataTmp = $this->categoriesModel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);
		array_push($data,$dataTmp);

		/** 
		** check if user has privileges to add categories
		**/
		$documentsModel = new Default_Model_Documents();
		$userPrivilege = $documentsModel->getPrivileges($this->loggedInUserGroup, $this->loggedInUserRole,'addpermission');
		$this->view->userPrivilege = $userPrivilege;

		/**
		** send data objects to view
		**/
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		
		/**
		** send flash messages to view
		**/
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
	
	}

	/**
	**	this action is used to display add form
	**  if post values are available will create new category
	**	@parameters 
	**		string category name (mandatory)
	**		text	category description (optional)
	**
	**	@return parameteres
	**		@object success/failure messages		
	**/
	public function addAction()
	{
		/**
		** check for logged in user role for add privileges
		**/
		if(sapp_Global::_checkprivileges(POLICY_DOCS_CATEGORIES,$this->loggedInUserGroup,$this->loggedInUserRole,'add') != 'Yes'){
				$this->view->ermsg = 'noprivileges';
				return;
		} 

		/**
		** Initiating category form
		** and assigning action
		**/
		$addCategoryForm = new Default_Form_Categories();
		$addCategoryForm->setAttrib('action',BASE_URL.'categories/add');
		$this->view->form = $addCategoryForm;

		/**
		** To populate existing categories in auto-search
		**/
		$categoriesObj = $this->categoriesModel->getCategories('add');
		$categoriesStr = '';
		if(!empty($categoriesObj))
		{
			for($i = 0; $i < sizeof($categoriesObj);$i++)
			{
				$categoriesStr .= "'".$categoriesObj[$i]['category']."',";
			}
			$categoriesStr = trim($categoriesStr,",");
		}
		$this->view->categoriesStr = $categoriesStr;

		/**
		** Check if form post is initiated
		** if yes,	check if form is valid
		**			if yes, 
		**				1.	capture form values
		**				2.	prepare data object
		**				3.	insert into database
		**			if no,
		**				1.	build error message object		 
		**/
		if($this->getRequest()->getPost())
		{
			if($addCategoryForm->isValid($this->_request->getPost()))
			{
				/** capture form values **/
				$categoryName = $this->_request->getParam('category');
				$categoryDesc = $this->_request->getParam('description');

				/** prepare data to insert into database **/
				$data = array(
						'category' => $categoryName,
						'description' => $categoryDesc,
						'isused'=> 0, //default value
						'isactive'=> 1, //default value
						'modifiedby'=> $this->loggedInUser,
						'createdby' => $this->loggedInUser,
						'modifieddate'=> gmdate("Y-m-d H:i:s"),
						'createddate'=> gmdate("Y-m-d H:i:s")
				);
				$res = $this->categoriesModel->addCategory($data);

				if($res)
				{
					/** insert into log manager table **/
					sapp_Global::logManager(POLICY_DOCS_CATEGORIES,1,$this->loggedInUser,$res);
					
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success" => "Category added successfully"));
				}
				else
				{
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("failure" => "Failed to add new category. Please try again."));
				}

				$this->_redirect('categories');
			}
			else
			{
				$validationMsgs = $addCategoryForm->getMessages();
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
	**  @input category id
	**  if post values are available will edit category
	**	@parameters 
	**		string category name (mandatory)
	**		text	category description (optional)
	**
	**	@return parameteres
	**		@object success/failure messages		
	**/
	public function editAction()
	{
		/**
		** check for logged in user role for edit privileges
		**/
		if(sapp_Global::_checkprivileges(POLICY_DOCS_CATEGORIES,$this->loggedInUserGroup,$this->loggedInUserRole,'edit') != 'Yes'){
				$this->view->ermsg = 'noprivileges';
				return;
		} 

		/**
		** capture category id 
		**/
		$id = (int) $this->_request->getParam('id');
		
		if(is_numeric($id) && $id > 0)
		{
			/**
			** Instantiate category form
			**/
			$editCategoryForm = new Default_Form_Categories();
			$this->view->form = $editCategoryForm;

			/**
			** get category data based on id
			** populate data to form controls
			**/
			$res = $this->categoriesModel->getCategoryById($id);
			if(!empty($res))
			{
				$editCategoryForm->populate($res);			
				
				/** set form action parameter **/
				$editCategoryForm->setAttrib('action',BASE_URL.'categories/edit/id/'.$id);

				/** change submit button label to Update **/
				$editCategoryForm->submit->setLabel('Update');

				/**
				** Check if form post is initiated
				** if yes,	check if form is valid
				**			if yes, 
				**				1.	capture form values
				**				2.	prepare data object
				**				3.	update database
				**			if no,
				**				1.	build error message object		 
				**/
				if($this->getRequest()->getPost())
				{
					if($editCategoryForm->isValid($this->_request->getPost()))
					{
						/** capture form values **/
						$categoryName = $this->_request->getParam('category');
						$categoryDesc = $this->_request->getParam('description');

						/** prepare data object and where clause **/
						$data = array(
								'category' => $categoryName,
								'description' => $categoryDesc,
								'isactive'=> 1, //default value
								'modifiedby'=> $this->loggedInUser,
								'modifieddate'=> gmdate("Y-m-d H:i:s"),
								
						);
						$where = array('id=?' => $id);

						/** update category details **/
						$res = $this->categoriesModel->editCategory($data,$where);
						
						if($res)
						{
							/** insert into log manager table **/
							sapp_Global::logManager(POLICY_DOCS_CATEGORIES,2,$this->loggedInUser,$id);

							$this->_helper->getHelper("FlashMessenger")->addMessage(array("success" => "Category updated successfully"));
						}
						else
						{
							$this->_helper->getHelper("FlashMessenger")->addMessage(array("failure" => "Failed to update category. Please try again."));
						}

						$this->_redirect('categories');
					}
					else
					{
						$validationMsgs = $editCategoryForm->getMessages();
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
	**  @input category id
	**/
	public function viewAction()
	{
		/**
		** check for logged in user role for view privileges
		**/
		if(sapp_Global::_checkprivileges(POLICY_DOCS_CATEGORIES,$this->loggedInUserGroup,$this->loggedInUserRole,'view') != 'Yes'){
				$this->view->ermsg = 'noprivileges';
				return;
		} 

		/**
		** capture category id 
		**/
		$id = $this->_request->getParam('id');
		$id = (int)$id;
		$objName = 'categories';
		if(is_numeric($id) && $id > 0)
		{
			/**
			** Instantiate category form
			**/
			$viewCategoryForm = new Default_Form_Categories();
			$this->view->form = $viewCategoryForm;

			/**
			** get category data based on id
			** populate data to form controls
			**/
			$res = $this->categoriesModel->getCategoryById($id);
			if(!empty($res))
			{
				$viewCategoryForm->populate($res);	
				$viewCategoryForm->removeElement('submit');
				$elements = $viewCategoryForm->getElements();
				if(count($elements)>0)
				{
					foreach($elements as $key=>$element)
					{
						if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
							$element->setAttrib("disabled", "disabled");
						}
					}
				}
				$this->view->id = $id;
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
	**	this action is used to display view form
	**  @input category id
	**/
	public function deleteAction()
	{
		
		$messages = array('message' => '','msgtype' => '');

		/**
		** check for logged in user role for delete privileges
		**/
		if(sapp_Global::_checkprivileges(POLICY_DOCS_CATEGORIES,$this->loggedInUserGroup,$this->loggedInUserRole,'delete') != 'Yes'){
				$messages['message'] = 'You are not authorized to delete category.';
				$messages['msgtype'] = 'error';
				$this->_helper->json($messages);
				return;
		} 

		
		/**
		** capture category id 
		**/
		$id = $this->_request->getParam('objid');
		$deleteflag= $this->_request->getParam('deleteflag');
		$id = (int)$id;

		if(!empty($id))
		{
			/** prepare data object and where clause 
			** to update category details in database
			**/
			$data = array(
					'isactive'=> 0,
					'modifiedby'=> $this->loggedInUser,
					'modifieddate'=> gmdate("Y-m-d H:i:s")
			);
			$where = array('id=?' => $id);
			$res = $this->categoriesModel->editCategory($data,$where);

			if($res)
			{
				/** insert into log manager table **/
				sapp_Global::logManager(POLICY_DOCS_CATEGORIES,3,$this->loggedInUser,$id);

				$messages['message'] = 'Category deleted successfully';
				$messages['msgtype'] = 'success';
			}
			else
			{
				$messages['message'] = 'Category is not deleted';
				$messages['msgtype'] = 'error';
			}
		}
		else
		{
			$messages['message'] = 'Category is not deleted';
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
		$messages['flagtype'] = 'process';
		$this->_helper->json($messages);
	}	

	public function addnewcategoryAction()
	{
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		
		/**
		** Instantiate category form
		**/
		$addCategoryForm = new Default_Form_Categories();
		$this->view->form = $addCategoryForm;
		
		/** set form action parameter **/
		$addCategoryForm->setAttrib('action',BASE_URL.'categories/addnewcategory');

		/**
		** To populate existing categories in auto-search
		**/
		$categoriesObj = $this->categoriesModel->getCategories('add');
		$categoriesStr = '';
		if(!empty($categoriesObj))
		{
			for($i = 0; $i < sizeof($categoriesObj);$i++)
			{
				$categoriesStr .= "'".$categoriesObj[$i]['category']."',";
			}
			$categoriesStr = trim($categoriesStr,",");
		}
		$this->view->categoriesStr = $categoriesStr;
		
		/**
		** Check if form post is initiated
		** if yes,	check if form is valid
		**			if yes, 
		**				1.	capture form values
		**				2.	prepare data object
		**				3.	insert into database
		**			if no,
		**				1.	build error message object		 
		**/
		if($this->getRequest()->getPost())
		{
			if($addCategoryForm->isValid($this->_request->getPost()))
			{
				/** capture form values **/
				$categoryName = $this->_request->getParam('category');
				$categoryDesc = $this->_request->getParam('description');

				/** prepare data to insert into database **/
				$data = array(
						'category' => $categoryName,
						'description' => $categoryDesc,
						'isused'=> 0, //default value
						'isactive'=> 1, //default value
						'modifiedby'=> $this->loggedInUser,
						'createdby' => $this->loggedInUser,
						'modifieddate'=> gmdate("Y-m-d H:i:s"),
						'createddate'=> gmdate("Y-m-d H:i:s")
				);
				$res = $this->categoriesModel->addCategory($data);

				if($res)
				{
					/** insert into log manager table **/
					sapp_Global::logManager(POLICY_DOCS_CATEGORIES,1,$this->loggedInUser,$res);
					$this->view->newCat = $res;
				}

				$categoriesData = $this->categoriesModel->getCategories('add');
				$opt ='';   
				foreach($categoriesData as $category)
				{
					$opt .= sapp_Global::selectOptionBuilder($category['id'], $category['category']);
				}
					
				$this->view->categoriesData = $opt;
				$this->view->eventact = 'added';
				$this->view->popup = 'close';
			}
			else
			{
				$validationMsgs = $addCategoryForm->getMessages();
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
		$this->view->controllername = 'categories';
		$this->view->ermsg = '';

	}
}
?>