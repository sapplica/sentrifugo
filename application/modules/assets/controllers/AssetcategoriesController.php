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
/**
 * @Name   Assetcategories Controller
 *
 * @description
 *
 * This Assetcategory controller contain actions related to Assetcategory.
 *
 * 1. Display all Assetcategory details.
 * 2. Save or Update Assetcategory details.
 * 3. Delete Assetcategory.
 * 4. View Assetcategory details.
 *
 * @author sagarsoft
 * @version 1.0
 */
class Assets_AssetcategoriesController extends Zend_Controller_Action
{
	private $options;

	/**
	 * The default action - show the home page
	 */
	
	public function preDispatch()
	{
		
		
		/*$userModel = new Timemanagement_Model_Users();
		$checkTmEnable = $userModel->checkTmEnable();

		if(!$checkTmEnable){
			$this->_redirect('error');
		}*/
		
		//check Time management module enable
		if(!sapp_Helper::checkTmEnable())
			$this->_redirect('error');

	}

	public function init()
	{
		$this->_options= $this->getInvokeArg('bootstrap')->getOptions();

	}

	/**
	 * This method will display all the Assetcategory details in grid format.
	 */
	public function indexAction()
	{
		//echo "sdfsdfsdf";
		
		$assetcategoriesModel = new Assets_Model_AssetCategories();
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
			$sort = 'DESC';$by = 'modified';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'modified';
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
	
		
		$dataTmp = $assetcategoriesModel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);
			
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
	}

	/**
	 * This Action is used to Create/Update the Assetcategory details based on the Assetcategory id.
	 *
	 */
	public function editAction(){
		
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$objName = 'assetscategories';
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();

		$assetscategoriesForm = new Assets_Form_AssetCategories();
		$assetcategoriesModel = new Assets_Model_AssetCategories();
					
		$sub_cat_data = array();
		
		try{
			
			if($id)
			{	//Edit Record...
				
				if(is_numeric($id) && $id>0)
				{
					$data = $assetcategoriesModel->getAssetCategoriesDetailsById($id);
					if(count($data) > 0){
						$sub_cat_data = $assetcategoriesModel->getAssetSubCategoriesDetailsById($id);
					}

					if(!empty($data) && $data != "norows")
					{
						$assetscategoriesForm->populate($data[0]);
						$assetscategoriesForm->submit->setLabel('Update');
						$this->view->form = $assetscategoriesForm;
						$this->view->controllername = $objName;
						$this->view->id = $id;
						$this->view->ermsg = '';
						$this->view->inpage = 'Edit';
						$this->view->sub_cat_data = $sub_cat_data;
						
					}
					else
					{
						$this->view->ermsg = 'norecord';
					}
				}
				else
				{
					$this->view->ermsg = 'nodata';
				}
			}
			else
			{	//Add Record...
				$this->view->ermsg = '';
				$this->view->form = $assetscategoriesForm;
				$this->view->inpage = 'Add';
				$this->view->sub_cat_data = $sub_cat_data;
			}
		}
		catch(Exception $e)
		{
			$this->view->ermsg = 'nodata';
		}
		if($this->getRequest()->getPost()){
		
    	if($assetscategoriesForm->isValid($this->_request->getPost())){
				
				$id = $this->_request->getParam('id');
				$name = $this->_request->getParam('name');
				$subcatgory = $this->_request->getParam('subcat');
				$date = gmdate("Y-m-d H:i:s");

				$data = array(
							'name'  => $name ,
							'parent'  => '',
							);
				if($id!=''){
					$data['modified_by'] = $loginUserId;
					$data['modified'] = $date;
					$where = array('id=?'=>$id);
				}
				else
				{
					$data['created_by'] = $loginUserId;
					$data['created'] = $date;
					$data['modified'] = $date;
					$data['is_active'] = 1;
					$where = '';
				}
				
			$Id = $assetcategoriesModel->saveOrUpdateAssetCategoriesData($data, $where);
				
				if($Id=='update' )
				{
									
					$subCate_array = $this->_request->getParam('subcatid');
					$subcatgory = $this->_request->getParam('subcat');
					$id = $this->_request->getParam('id');
					$subdata = $assetcategoriesModel->getAssetSubCategoriesDetailsById($id);
					$sub_cat_data_array=array();
					$table_sub_cat = array();
				
					if((count($subdata)>count($subCate_array))){
						foreach($subdata as $sub_ids)
						{
							$table_sub_cat[] = $sub_ids['id'];
						}
					
						foreach($table_sub_cat as $subcatid){
							if(!in_array($subcatid, $subCate_array)){
								$deletesubcat = array('is_active'=>0,'modified'=>gmdate("Y-m-d H:i:s"),'modified_by'=>$loginUserId);
								$deletewhere_cond = array('id=?'=>$subcatid);
								$sub_cat_del = $assetcategoriesModel->saveOrUpdateAssetCategoriesData($deletesubcat, $deletewhere_cond);
					
							}
						}
					
					}
					if(count($subcatgory)>0)
					{
						
						foreach($subcatgory as $key =>$value)
						{
						if(trim($value) != '')
						{
							if(!empty($subCate_array[$key]))
							{
								$sub_cat_data_array = array(
										'name'=>$value,
										'modified_by'=>$loginUserId,
										'modified'=>$date
								);
								$sub_where = array('id=?'=>$subCate_array[$key]);
							
								$sub_cat_edit = $assetcategoriesModel->saveOrUpdateAssetCategoriesData($sub_cat_data_array, $sub_where);
							}else
							{
								$sub_cat_data_array = array(
											
										'name'  => $value ,
										'parent'  => $id,
										'created_by'=>$loginUserId,
										'created'=>$date,
										'is_active'=>1,
											
								);
								$sub_where_add = '';
								$sub_cat_edit = $assetcategoriesModel->saveOrUpdateAssetCategoriesData($sub_cat_data_array, $sub_where_add);
							}
						}
							
						}
					}
			}
				else 
				{
					$subdata=array();
					if(count($subcatgory)>0  ) {
						foreach($subcatgory as $parentdata)
						{
					if(	$parentdata!=''){
						$subdata = array(
								'name'  => $parentdata ,
								'parent'  => $Id,
								'created_by'=>$loginUserId,
								'created'=>$date,
								'is_active'=>1,
						);
						$where = '';
						$subId = $assetcategoriesModel->saveOrUpdateAssetCategoriesData($subdata,$where);
					}
						}
					}
					
				}
				
				if($Id == 'update')
				{
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Asset Category updated successfully."));
				}
				else
				{
			     $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Asset Category added successfully."));
				}
					
				$this->_redirect('assets/assetcategories');
			}else
			{
				
				$subCate_array = $this->_request->getParam('subcatid');
				$subcatgory = $this->_request->getParam('subcat');
				
				$final_sub_array = array();
				
				if(!empty($id) && $id>0)
				{
					if(count($subcatgory)>0)
					{
						
						foreach($subcatgory as $key =>$value)
						{
							if(trim($value) != '')
							{
								if(empty($subCate_array[$key]))
								{
									$final_sub_array[] = $value;
								}
							}

						}
					}
				}else
				{
					$final_sub_array = $subcatgory;
				}
				$this->view->final_sub_array = $final_sub_array;
				$messages = $assetscategoriesForm->getMessages();
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
	//========================================================

	/**
	 * This Action is used to view the Asset Category details based on the Assetcategory id.
	 *
	 */
	public function viewAction(){

		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();
		$objName = 'assetcategories';

			$assetscategoriesModel = new Assets_Model_AssetCategories();
		
		try
		{
			if(is_numeric($id) && $id>0)
			{
				$data = $assetscategoriesModel->getAssetCategoriesDetailsById($id);
				
				if(count($data) > 0){
					$sub_cat_data = $assetscategoriesModel->getAssetSubCategoriesDetailsById($id);
				}
				if(!empty($data) && $data != "norows")
				{
					
					
					$this->view->id = $id;
					$this->view->controllername=$objName;
					$this->view->ermsg = '';
					$this->view->inpage = 'Edit';
					$this->view->sub_cat_data = $sub_cat_data;
					$this->view->data = $data;
				}
				else
				{
					$this->view->ermsg = 'norecord';
				}
				}
				else
				{
					$this->view->ermsg = 'nodata';
				}
				
		}
		catch(Exception $e)
		{
			$this->view->ermsg = 'nodata';
		}
	}

	/**
	 * This action is used to delete the asset category details based on the Assetcategory id.
	 *
	 */
	public function deleteAction(){
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->_request->getParam('objid');
		$deleteflag= $this->_request->getParam('deleteflag');
		$messages['message'] = ''; $messages['msgtype'] = '';
		$messages['flagtype'] = '';
		$actionflag = 3;
		if($id)
		{
			$assetscategoriesModel = new Assets_Model_AssetCategories();
			$isexistforsetting = $assetscategoriesModel->isCategoryExistForassetSettings($id);
			$isexist = $assetscategoriesModel->isCategoryExistForasset($id);
		    	if($isexist==0 && $isexistforsetting ==0)
		    	{
		    		
					$data = array('is_active'=>0,'modified'=>gmdate("Y-m-d H:i:s"));
					$data['modified_by'] = $loginUserId;
					$where = array('id=?'=>$id);
					$id = $assetscategoriesModel->saveOrUpdateAssetCategoriesData($data, $where);
					if($id == 'update')
					{
						$messages['message'] = 'Assset category deleted successfully.';
						$messages['msgtype'] = 'success';
					}
					else
					{
						$messages['message'] = 'Assset category cannot be deleted.';
						$messages['msgtype'] = 'error';
					}
		    	}
		    	else 
		    	{
		    	
		    		$messages['message'] = 'Category is in use. You cannot deleted the category.';
		    		$messages['msgtype'] = 'error';
		    	}
		    
		}
		else
		{
			$messages['message'] = 'Assset category cannot be deleted.';$messages['msgtype'] = 'error';
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

	public function addpopupAction()
	{
		$msgarray = array();
		$emptyFlag = '';
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->getRequest()->getParam('id');

		$controllername = 'assetcategories';
		$assetcategoriesForm = new Assets_Form_AssetCategories();
		
		$assetcategoriesModel = new Assets_Model_AssetCategories();
		$assetcategoriesForm->setAction(BASE_URL.'assets/assetcategories/addpopup');
		$sub_cat_data = array();
		if($id)
		{
			if(is_numeric($id) && $id>0)
			{
				$data = $assetcategoriesModel->getAssetCategoriesDetailsById($id);
				if(count($data) > 0){
					$sub_cat_data = $assetcategoriesModel->getAssetSubCategoriesDetailsById($id);
				}
		
				if(!empty($data) && $data != "norows")
				{
					$assetcategoriesForm->populate($data[0]);
					//$assetcategoriesForm->submit->setLabel('Update');
					$this->view->form = $assetcategoriesForm;
					$this->view->controllername = $controllername;
					$this->view->id = $id;
					$this->view->ermsg = '';
					$this->view->inpage = 'Edit';
					$this->view->sub_cat_data = $sub_cat_data;
		
				}
				else
				{
					$this->view->ermsg = 'norecord';
				}
			}
			else
			{
				$this->view->ermsg = 'nodata';
			}
		}else
			{	
				$this->view->ermsg = '';
				$this->view->form = $assetcategoriesForm;
			
				$this->view->inpage = 'Add';
				$this->view->sub_cat_data = $sub_cat_data;
			}
		if($this->getRequest()->getPost()){
			if($assetcategoriesForm->isValid($this->_request->getPost())){
				
				$id = $this->_request->getParam('id');
				$name = $this->_request->getParam('name');
				$subcatgory = $this->_request->getParam('subcat');
				$date = gmdate("Y-m-d H:i:s");
				
				
				$data = array(
							'name'  => $name ,
							'parent'  => '',
							
				);
				
				if($id!=''){
					$data['modified_by'] = $loginUserId;
					$data['modified'] = $date;
					$where = array('id=?'=>$id);
				}
				else
				{
					$data['created_by'] = $loginUserId;
					$data['created'] = $date;
					//$data['modified'] = $date;
					$data['is_active'] = 1;
					$where = '';
				}


				$Id = $assetcategoriesModel->saveOrUpdateAssetCategoriesData($data, $where);
				
				if($Id=='update' )
				{
				
					$subCate_array = $this->_request->getParam('subcatid');
				
					$subcatgory = $this->_request->getParam('subcat');
					$id = $this->_request->getParam('id');
					$subdata = $assetcategoriesModel->getAssetSubCategoriesDetailsById($id);
					$sub_cat_data_array=array();
					$table_sub_cat = array();
					if((count($subdata)>count($subCate_array))){
						foreach($subdata as $sub_ids)
						{
							$table_sub_cat[] = $sub_ids['id'];
						}
							
						foreach($table_sub_cat as $subcatid){
							if(!in_array($subcatid, $subCate_array)){
									
								$deletesubcat = array('is_active'=>0,'modified'=>gmdate("Y-m-d H:i:s"),'modified_by'=>$loginUserId);
								$deletewhere_cond = array('id=?'=>$subcatid);
								$sub_cat_del = $assetcategoriesModel->saveOrUpdateAssetCategoriesData($deletesubcat, $deletewhere_cond);
				
							}
						}
				
					}
						
						
					if(count($subcatgory)>0)
					{
						foreach($subcatgory as $key =>$value)
						{
						if(trim($value) != '')
							{
							if(!empty($subCate_array[$key]))
							{
								$sub_cat_data_array = array('name'=>$value,
										'modified_by'=>$loginUserId,
										'modified'=>$date
								);
								$sub_where = array('id=?'=>$subCate_array[$key]);
								$sub_cat_edit = $assetcategoriesModel->saveOrUpdateAssetCategoriesData($sub_cat_data_array, $sub_where);
							}else
							{
								
								$sub_cat_data_array = array(
										'name'  => $value ,
										'parent'  => $id,
										'created_by'=>$loginUserId,
										'created'=>$date,
										'is_active'=>1,
											
								);
								
								$sub_where_add = '';
								$sub_cat_edit = $assetcategoriesModel->saveOrUpdateAssetCategoriesData($sub_cat_data_array, $sub_where_add);
							}
								
							}	
						}
					}
				}
				else
				{
					$subdata=array();
					if(count($subcatgory)>0){
						foreach($subcatgory as $parentdata){
							if(	$parentdata!=''){
							$subdata = array(
									'name'  => $parentdata ,
									'parent'  => $Id,
									'created_by'=>$loginUserId,
									'created'=>$date,
									'is_active'=>1,
							);
							$where = '';
							$subId = $assetcategoriesModel->saveOrUpdateAssetCategoriesData($subdata,$where);
							}		
						}
					}
						
				}
				
			   $assetscategoriesData = $assetcategoriesModel->fetchAll('is_active = 1 and parent=0','name')->toArray();
			   
				$opt ='';
				foreach($assetscategoriesData as $record){
					$opt .= sapp_Global::selectOptionBuilder($record['id'], $record['name']);
				}
				$this->view->assetscategoriesData = $opt;
					
				$this->view->eventact = 'added';
				$close = 'close';
				$this->view->popup=$close;
			}else
			{
				$subCate_array = $this->_request->getParam('subcatid');
				$subcatgory = $this->_request->getParam('subcat');
				
				$final_sub_array = array();
				if(!empty($id) && $id>0)
				{
					if(count($subcatgory)>0)
					{
				
						foreach($subcatgory as $key =>$value)
						{
							//echo "<pre>";print_r($subCate_array[$value]);die;
							if(trim($value) != '')
							{
								if(empty($subCate_array[$key]))
								{
									$final_sub_array[] = $value;
								}
							}
				
						}
					}
				}else
				{
					$final_sub_array = $subcatgory;
				}
				$this->view->final_sub_array = $final_sub_array;
				$messages = $assetcategoriesForm->getMessages();
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
		$this->view->form = $assetcategoriesForm;
		$this->view->ermsg = '';

	}
	
	public function addsubcatpopupAction()
	{
		$msgarray = array();
		$emptyFlag = '';
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$controllername = 'assetcategories';
		$id = $this->getRequest()->getParam('id');
		$assetcategoriesForm = new Assets_Form_AssetCategories();
		$assetcategoriesModel = new Assets_Model_AssetCategories();
		$assetcategoriesForm->setAction(BASE_URL.'assets/assetcategories/addsubcatpopup');
		$sub_cat_data=array();
	    if($id)
			{	
				if(is_numeric($id) && $id>0)
				{
					$data = $assetcategoriesModel->getAssetCategoriesDetailsById($id);
					if(count($data) > 0){
						$sub_cat_data = $assetcategoriesModel->getAssetSubCategoriesDetailsById($id);
					}

					if(!empty($data) && $data != "norows")
					{
						$assetcategoriesForm->populate($data[0]);
						//$assetcategoriesForm->submit->setLabel('Update');
						$this->view->form = $assetcategoriesForm;
						$this->view->controllername = $controllername;
						$this->view->id = $id;
						$this->view->ermsg = '';
						$this->view->inpage = 'Edit';
						$this->view->sub_cat_data = $sub_cat_data;
						
						$assetcategoriesForm->name->setAttrib('readonly', 'true');
						
					}
					else
					{
						$this->view->ermsg = 'norecord';
					}
				}
				else
				{
					$this->view->ermsg = 'nodata';
				}
			}else
			{	
				$this->view->ermsg = '';
				$this->view->form = $assetcategoriesForm;
				$this->view->inpage = 'Add';
				$this->view->sub_cat_data = $sub_cat_data;
			}
		if($this->getRequest()->getPost()){
			if($assetcategoriesForm->isValid($this->_request->getPost())){
	
				$id = $this->_request->getParam('id');
				$name = $this->_request->getParam('name');
				$subcatgory = $this->_request->getParam('subcat');
				$date = gmdate("Y-m-d H:i:s");
	
	
				$data = array(
						'name'  => $name ,
						'parent'  => '',
							
				);
	
				if($id!=''){
					$data['modified_by'] = $loginUserId;
					$data['modified'] = $date;
					$where = array('id=?'=>$id);
					
				}
				else
				{
					$data['created_by'] = $loginUserId;
					$data['created'] = $date;
					//$data['modified'] = $date;
					$data['is_active'] = 1;
					$where = '' ;
					
				}
	
				$Id = $assetcategoriesModel->saveOrUpdateAssetCategoriesData($data, $where);
				
				
			if($Id=='update' )
				{
										
					$subCate_array = $this->_request->getParam('subcatid');
						
					$subcatgory = $this->_request->getParam('subcat');
					$id = $this->_request->getParam('id');
					$subdata = $assetcategoriesModel->getAssetSubCategoriesDetailsById($id);
					$sub_cat_data_array=array();
					$table_sub_cat = array();
					 if((count($subdata)>count($subCate_array))){
					 	foreach($subdata as $sub_ids)
					 	{
					 		$table_sub_cat[] = $sub_ids['id'];
					 	}
					 	
					 	foreach($table_sub_cat as $subcatid){
					 		if(!in_array($subcatid, $subCate_array)){
					 			
					 			$deletesubcat = array('is_active'=>0,'modified'=>gmdate("Y-m-d H:i:s"),'modified_by'=>$loginUserId);
					 			$deletewhere_cond = array('id=?'=>$subcatid);
					 			$sub_cat_del = $assetcategoriesModel->saveOrUpdateAssetCategoriesData($deletesubcat, $deletewhere_cond);
					 
					 		}
					 	}
					 
					 }
					
					
					if(count($subcatgory)>0)
					{
						foreach($subcatgory as $key =>$value)
						{
						if(trim($value) != '')
						{
							  if(!empty($subCate_array[$key]))
							  {
							  	$sub_cat_data_array = array('name'=>$value,
							  			'modified_by'=>$loginUserId,
							  			'modified'=>$date
							  	);
							  	$sub_where = array('id=?'=>$subCate_array[$key]);
							  	$sub_cat_edit = $assetcategoriesModel->saveOrUpdateAssetCategoriesData($sub_cat_data_array, $sub_where);
							  }else 
							  {
							  	
							  	$sub_cat_data_array = array(
										'name'  => $value ,
										'parent'  => $id,
										'created_by'=>$loginUserId,
										'created'=>$date,
										'is_active'=>1,
						
								);
							  	$sub_where_add = '';
							  	$sub_cat_edit = $assetcategoriesModel->saveOrUpdateAssetCategoriesData($sub_cat_data_array, $sub_where_add);
							  }
						}	
							
						}
					}
			}
				else 
				{
					
					$subdata=array();
					
					if(count($subcatgory)>0){
						
						foreach($subcatgory as $parentdata){
							if(	$parentdata!=''){
							$subdata = array(
									'name'  => $parentdata ,
									'parent'  => $Id,
									'created_by'=>$loginUserId,
									'created'=>$date,
									'is_active'=>1,
							);
							$where = '';
							$subId = $assetcategoriesModel->saveOrUpdateAssetCategoriesData($subdata,$where);
							}
						}
					}
					
								
				}
			if($id!=''){
				$assetscategoriesData = $assetcategoriesModel->getAssetSubCategoriesDetailsById($id);
				$opt ='';
				foreach($assetscategoriesData as $record){
					$opt .= sapp_Global::selectOptionBuilder($record['id'], $record['name']);
				}
				
				$this->view->assetscategoriesData = $opt;
				$this->view->id=$id;
				$this->view->eventact = 'sub category added';
				}else{
					
					$assetscategoriesData = $assetcategoriesModel->fetchAll('is_active = 1 and parent=0','name')->toArray();
					$opt ='';
					foreach($assetscategoriesData as $record){
						$opt .= sapp_Global::selectOptionBuilder($record['id'], $record['name']);
						$this->view->eventact = 'category added';
						$this->view->assetscategoriesData = $opt;
						$this->view->Id=$Id;
				}
				
				
					
				}
				
				$close = 'close';
				$this->view->popup=$close;
				
			}else
			{
				$subCate_array = $this->_request->getParam('subcatid');
				$subcatgory = $this->_request->getParam('subcat');
				
				$final_sub_array = array();
				
				if(!empty($id) && $id>0)
				{
					if(count($subcatgory)>0)
					{
				
						foreach($subcatgory as $key =>$value)
						{
							//echo "<pre>";print_r($subCate_array[$value]);die;
							if(trim($value) != '')
							{
								if(empty($subCate_array[$key]))
								{
									$final_sub_array[] = $value;
								}
							}
				
						}
					}
				}else
				{
					$final_sub_array = $subcatgory;
				}
				$this->view->final_sub_array = $final_sub_array;
				$messages = $assetcategoriesForm->getMessages();
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
		$this->view->form = $assetcategoriesForm;
		$this->view->ermsg = '';
	
	}

	 public function assetuserlogAction(){
	 	Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$objName = 'Asset'; 
		$id = $this->getRequest()->getParam('id');
		
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();
		
		$assetcategoriesModel = new Assets_Model_AssetCategories();
		
		try
		{
			if($id){
				if(is_numeric($id) && $id>0)
				{
					$assetuserlogdata = $assetcategoriesModel->getUserAssetData($id);
				$date = gmdate("Y-m-d H:i:s");
				
				if(!empty($assetuserlogdata) && $assetuserlogdata != "norows")
				{
					$this->view->assetuserlogdata = $assetuserlogdata;
					$this->view->ermsg = '';
					$this->view->objName=$objName;
					
				}
				else
				{
					$this->view->ermsg = 'norecord';
				}
			}
			else
			{
				$this->view->ermsg = 'nodata';
			}
		}
		}
		catch(Exception $e)
		{
			$this->view->ermsg = 'nodata';
		}
		}
		
		
		
		
		
	} 
	
	

