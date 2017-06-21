<?php
/********************************************************************************* 
 *  This file is part of Sentrifugo.
 *  Copyright (C) 2014 Sapplica
 *   
 *  Sentrifugo is free software: you canyou can redistribute it and/or modify
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

?>
<?php

class Assets_AssetsController extends Zend_Controller_Action
{
	//To Load Grid
	public function indexAction()
		{
			
			$assetsmodel = new Assets_Model_Assets();
			$call = $this->_getParam('call');
			if($call == 'ajaxcall')
			$this->_helper->layout->disableLayout();
			$view = Zend_Layout::getMvcInstance()->getView();
			$objname = $this->_getParam('objname');
			$refresh = $this->_getParam('refresh');
			
			$dashboardcall = $this->_getParam('dashboardcall',null);
				$data = array();		
				$searchQuery = '';		
				$searchArray = array();		
				$tablecontent='';		
			$organizationImg = new Zend_Session_Namespace('organizationinfo');
			if(!empty($data['image']))
				{
					$image->image = $data['image'];
				}
			if($refresh == 'refresh')
				{
					if($dashboardcall == 'Yes')
					$perPage = DASHBOARD_PERPAGE;
					else
					$perPage = PERPAGE;

					$sort = 'DESC';$by = 'modified';$pageNo = 1;$searchData = '';$searchQuery = '';
					$searchArray = array();
				
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
					$searchData = $this->_getParam('searchData');
					$searchData = rtrim($searchData,',');
				}
			$dataTmp = $assetsmodel->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall);
			array_push($data,$dataTmp);
			$this->view->dataArray = $data;
			$this->view->call = $call ;
			$this->view->messages = $this->_helper->flashMessenger->getMessages();
	
		}
		
	
	
	//Add or Edit The Record from the grid
	public function editAction()
		{
			try
			{
			$auth = Zend_Auth::getInstance();
			if($auth->hasIdentity())
				{
					$loginUserId = $auth->getStorage()->read()->id;
				}
			$objName = 'assets';
			$id = $this->getRequest()->getParam('id');
			$callval = $this->getRequest()->getParam('call');
			if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();


		$popConfigPermission = array();
	    $assetsForm = new Assets_Form_Assets();
		$assetsModel = new Assets_Model_Assets();
	
		$msgarray = array();
		array_push($popConfigPermission,'assetcategory');
		array_push($popConfigPermission,'assets');
		
		$this->view->popConfigPermission = $popConfigPermission; 
		
		$SubCategoriesData=array();
			
				
				if($id)
				{	
				//To Edit
				if(is_numeric($id) && $id>0)
						{
						$data_array = $assetsModel->getAssetsDetailsById($id);
				
					//Permenant Image Deletion on update
						 if($assetsForm->isValid($this->_request->getPost()))
						{
							
							$doc_new_name = $this->_request->getParam('delete_img_ids'); 
							$ImagEncrpName_array = explode(',',$doc_new_name);
							foreach($ImagEncrpName_array as $key => $image_new_name)
							{
								if($image_new_name!='')
								{	
									$filePath = "";
									$filePath = ASSETS_IMAGES_TEMP_PATH.$image_new_name;
									if (file_exists($filePath)) 
									{
										unlink($filePath);
									}
								}
							}
						} 
						
						if(!empty($data_array) && $data_array != "norows")
						{	
							$assetsModel = new Assets_Model_Assets();
							$assetsForm = new Assets_Form_Assets();
							if(($data_array[0]['is_working'])=='No'){
								$data_array[0]['allocated_to']= '';
							}
							
							$assetsForm->populate($data_array[0]);					
							$assetsForm->submit->setLabel('Update');
							$this->view->form = $assetsForm;
							$this->view->controllername = $objName;
							$this->view->image = $data_array[0]['image'];
							$this->view->ImagEncrpName = $data_array[0]['imagencrpname'];
							$this->view->id = $id;
							$this->view->ermsg = '';
							$this->view->inpage = 'Edit';
							$this->view->data_array = $data_array;

							$employeemodel = new Default_Model_Employee();
							if($data_array[0]['location']!='' && $data_array[0]['location'] !='null') {
							$employeeData = $employeemodel->getEmployeesForServiceDesk($data_array[0]['location']);
								if(!empty($employeeData)){
									foreach($employeeData as $empdata)
									{
										$assetsForm->allocated_to->addMultiOption($empdata['user_id'],utf8_encode($empdata['userfullname']));
									}
									if(!empty($employeeData)){
										$assetsForm->setDefault('allocated_to',$data_array[0]['allocated_to']);
									}
								}
							}

							$SubCategoriesModel = new Assets_Model_AssetCategories();
							$SubCategoriesData = $SubCategoriesModel->getAssetSubCategoriesDetailsById($data_array[0]['category']);
							foreach($SubCategoriesData as $SubCategories)
							{
								$assetsForm->sub_category->addMultiOption($SubCategories['id'],utf8_encode($SubCategories['name']));
							}
							
						
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
				{					
					//To Add
					$this->view->ermsg = '';
					$this->view->form = $assetsForm;
					$this->view->inpage = 'Add';
				  
					
				}
			
		if($this->getRequest()->getPost()){
			
			 /*   $image = $this->_request->getParam('file_original_names'); 
				$ImagEncrpName = $this->_request->getParam('file_new_names');
				'image' =>$image,
				'ImagEncrpName' => $ImagEncrpName
				$this->view->image = $image;
				$this->view->ImagEncrpName = $ImagEncrpName;
				 */
				
					
			 $file_orginal_name_array = array();
			$file_enc_name_array = array();
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
			$file_encrypt_names = $this->getRequest()->getParam('file_new_names');
			$file_encrypt_names_array = explode(',',$file_encrypt_names);
			$file_encrypt_names_array = array_filter($file_encrypt_names_array);
			if(count($file_encrypt_names_array)>0)
			{
				$file_encrypt_names=implode(',',$file_encrypt_names_array);
				
			}
			else
			{
				$file_encrypt_names='';
			}
			 
			 
			 
			/* if($file_original_names!='')
			{
			$file_orginal_name_array = explode(',',$file_original_names);
			}

			if($file_encrypt_names!='')
			{
			$file_enc_name_array = explode(',',$file_encrypt_names);
			} */
			$this->view->image = $file_original_names;
			$this->view->ImagEncrpName = $file_encrypt_names;
				
				
				
		
			if(isset($_POST['category']) && $_POST['category']!='')
			{
				$SubCategoriesModel = new Assets_Model_AssetCategories();
				$SubCategoriesData = $SubCategoriesModel->getAssetSubCategoriesDetailsById(intval($_POST['category']));
				$asset_sub__array = array();
				if(count($SubCategoriesData) > 0)
				{
					foreach($SubCategoriesData as $subdata)
					{
						$asset_sub__array[$subdata['id']] = $subdata['name'];
					}
				}
				$assetsForm->sub_category->addMultiOptions(array(''=>'Select Sub Category')+$asset_sub__array);
			}
			if(isset($_POST['location']) && $_POST['location']!='')
			{
				
				$employeemodel = new Default_Model_Employee();
				$employeeData = $employeemodel->getEmployeesForServiceDesk(intval($_POST['location']));
				$allocated_users_array = array();
				if(count($employeeData) > 0)
				{
					foreach($employeeData as $user_data)
					{
						$assetsForm->allocated_to->addMultiOption($user_data['user_id'],utf8_encode($user_data['userfullname']));
						
					}
				}
			}
				if($assetsForm->isValid($this->_request->getPost())){
					$category	= $this->_request->getParam('category');
					$sub_category	= $this->_request->getParam('sub_category');
					$id = $this->_request->getParam('id');
					$company_asset_code = $this->_request->getParam('company_asset_code');
					$name = $this->_request->getParam('name');
					$location = $this->_request->getParam('location');
					$allocated_to = $this->_request->getParam('allocated_to');
					
					/* $responsible_technician = $this->_request->getParam('responsible_technician');
					$vendor = $this->_request->getParam('vendor'); */
					$asset_classification = $this->_request->getParam('asset_classification');
					$purchase_date = $this->_request->getParam('purchase_date');
					$purchase_date = sapp_Global::change_date($purchase_date,'database');
					$invoice_number = $this->_request->getParam('invoice_number');
					$manufacturer = $this->_request->getParam('manufacturer');
					$key_number = $this->_request->getParam('key_number');
					$warenty_status =$this->_request->getParam('warenty_status');
					$warenty_end_date = $this->_request->getParam('warenty_end_date');
					$warenty_end_date = sapp_Global::change_date($warenty_end_date,'database');
					$is_working = $this->_request->getParam('is_working');
					$notes = $this->_request->getParam('notes');
					$image = $file_original_names;
					$ImagEncrpName = $file_encrypt_names;
					$qr_image = $this->_request->getParam('qr_image_names');
					
					if($this->_request->getParam('category_id'))
					{
						$category_id	= $this->_request->getParam('category_id');
					}
					if($this->_request->getParam('sub_category_id'))
					{
						$sub_category_id	= $this->_request->getParam('sub_category');
					}
				$date = gmdate("Y-m-d H:i:s");		
				$data = array( 
								'category' => $category,
								'sub_category'=> $sub_category,
								'company_asset_code'=>$company_asset_code,
								'name'=>$name,
								'location'=>$location,
								'allocated_to'=>$allocated_to,
								/* 'responsible_technician'=>$responsible_technician,
								'vendor'=>$vendor, */
								'asset_classification'=>$asset_classification,
						        'purchase_date'=>(trim($purchase_date)!=''?$purchase_date:NUll),
								'invoice_number'=>$invoice_number,
								'manufacturer'=>$manufacturer,
								'key_number'=>$key_number,
								'warenty_status' =>$warenty_status,
								'warenty_end_date'=>(trim($warenty_end_date)!=''?$warenty_end_date:NUll),
								'is_working'=>$is_working,
								'notes' => $notes,
								'image' =>$image,
								'ImagEncrpName' => $ImagEncrpName
						     
							);	
				//ToCheckThe EncrpName
			  

				if($id!='')
					{
						$data['modified_by'] = $loginUserId;
						$data['modified'] = $date;
						$where = array('id=?'=>$id);
					}
				else
					{
						
						$data['created_by'] = $loginUserId;
						$data['created'] = 	$date;
						$data['modified'] = $date;
						$data['isactive'] = 1;
						$where = '';
					}
					include 'phpqrcode/qrlib.php';
					$tempDir = ASSETS_QRCODE_PATH;	
					$t=time();
					$codeContents =  $t;
					$fileName = $t.'.png';
					$pngAbsoluteFilePath = $tempDir.$fileName;
					//$filename = $PNG_TEMP_DIR.'test'.md5($_REQUEST['data'].'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
					if (!file_exists($pngAbsoluteFilePath)) {
						QRcode::png($codeContents, $pngAbsoluteFilePath,4,4);
					
					} else {
						echo 'File already generated! We can use this cached file to speed up site on common codes!';
						echo '<hr />';
					}
						$data['qr_image'] = $fileName;
					$Id = $assetsModel->saveOrUpdateAssetsData($data, $where); 
	
				if($Id == 'update')
				{
					
				
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Asset updated successfully."));
					//insert asset history
					
					if($allocated_to!=''  && $allocated_to!=$data_array[0]['allocated_to'])
					{
						$usersModel = new Default_Model_Users();
						$userDetails = $usersModel->getUserDetailsByID($allocated_to);
						
						if($allocated_to!='' && $allocated_to!=0 && $data_array[0]['allocated_to']!='' && $data_array[0]['allocated_to']!=0)
							$history = 'Asset has been allocated to '.$userDetails[0]['userfullname'].' from '.$data_array[0]['AllocatedTo'];
						else if($allocated_to=='' || $allocated_to==0)
								$history = 'Asset has been removed from '.$data_array[0]['AllocatedTo'];
						else
								$history = 'Asset has been allocated to '.$userDetails[0]['userfullname'];
						
						$history_data= array( 
								'asset_id' => $id,
								'user_id'=>$allocated_to,
								'createdby'=> $loginUserId,
								'createddate' =>$date,
								'isactive'=> 1,
								'history'=>$history
							);
					
						$userlog = $assetsModel->InsertToHistoryTable($history_data);
					}
					if($category!=$data_array[0]['category'])
					{
						$categoryModel = new Assets_Model_AssetCategories();
						$new_categoryDetails = $categoryModel->getCategoryBYId($category);
						$old_categoryDetails = $categoryModel->getCategoryBYId($data_array[0]['category']);
						$history = 'Asset category has been changed to '.$new_categoryDetails[0]['name'].' from '.$old_categoryDetails[0]['name'];
						$history_data= array( 
								'asset_id' => $id,
								'createdby'=> $loginUserId,
								'createddate' =>$date,
								'isactive'=> 1,
								'history'=>$history
							);
					
						$userlog = $assetsModel->InsertToHistoryTable($history_data);
					}
					if(!empty($sub_category) && $sub_category!=$data_array[0]['sub_category'])
					{
						
						$categoryModel = new Assets_Model_AssetCategories();
						$new_categoryDetails = $categoryModel->getCategoryBYId($sub_category);
						if(!empty($data_array[0]['sub_category']))
						{
							$old_categoryDetails = $categoryModel->getCategoryBYId($data_array[0]['sub_category']);
						}
						
						if(!empty($old_categoryDetails))
						{
							$history = 'Asset subcategory has been changed to '.$new_categoryDetails[0]['name'].' from '.$old_categoryDetails[0]['name'];
						}
						else
						{
							$history = 'Asset subcategory has been changed to '.$new_categoryDetails[0]['name'].'';	
						}
						$history_data= array( 
								'asset_id' => $id,
								'createdby'=> $loginUserId,
								'createddate' =>$date,
								'isactive'=> 1,
								'history'=>$history
							);
					
						$userlog = $assetsModel->InsertToHistoryTable($history_data);
					}
					if($company_asset_code!= ''&& $data_array[0]['company_asset_code']!= ''&&$company_asset_code!= $data_array[0]['company_asset_code'])
					{
						$history = 'Company asset code changed to '.$company_asset_code.' from '.$data_array[0]['company_asset_code'];
						$history_data= array( 
								'asset_id' => $id,
								'createdby'=> $loginUserId,
								'createddate' =>$date,
								'isactive'=> 1,
								'history'=>$history
							);
						$userlog = $assetsModel->InsertToHistoryTable($history_data);
					}
					if($name!=$data_array[0]['name'])
					{
						$history = 'Asset name changed to '.$name.' from '.$data_array[0]['name'];
						$history_data= array( 
								'asset_id' => $id,
								'createdby'=> $loginUserId,
								'createddate' =>$date,
								'isactive'=> 1,
								'history'=>$history
							);
					
						$userlog = $assetsModel->InsertToHistoryTable($history_data);
					}
						if($asset_classification!=$data_array[0]['asset_classification'])
					{
						$history = 'Asset classification changed to '.$asset_classification.' from '.$data_array[0]['asset_classification'];
						$history_data= array( 
								'asset_id' => $id,
								'createdby'=> $loginUserId,
								'createddate' =>$date,
								'isactive'=> 1,
								'history'=>$history
							);
						$userlog = $assetsModel->InsertToHistoryTable($history_data);
					}
					
					
				}
				else
				{	
					$history = 'Asset Created';
					//Insert the allocated to in the Assets_History Table on new entry.
					$data= array( 
								'asset_id' => $Id,
								'user_id'=>$allocated_to,
								'createdby'=> $loginUserId,
								'modifiedby'=> $loginUserId,
								'createddate' =>$date,
								'modifieddate' => $date,
								'isactive'=> 1,
								'history'=>$history
							);
					
					$userlog = $assetsModel->InsertToHistoryTable($data);
					
					
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Asset added successfully."));
					
				}
					
				$this->_redirect('assets/assets');
			}else
			{
				$messages = $assetsForm->getMessages();
				foreach ($messages as $key => $val)
				{
					foreach($val as $key2 => $val2)
					{
						$msgarray[$key] = $val2;
						break;
					}
				}
				$file_original_names = $file_original_names;
				$file_encrypt_names = $file_encrypt_names;
				
				$this->view->image = $file_original_names;
				$this->view->ImagEncrpName = $file_encrypt_names;
				$this->view->msgarray = $msgarray;
				
					
			}
		}
}
			
		 catch(Exception $e)
		{
			$this->view->ermsg = 'nodata';
		} 
	}
	
	public function deleteAction()
	{ 
		
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
			
			$assetsModel = new Assets_Model_Assets();
			$isexist = $assetsModel->isAssetExistForSetting($id);
			if($isexist==0 )
			{
				$data = array('isactive'=>0,'modified'=>gmdate("Y-m-d H:i:s"));
				$data['modified_by'] = $loginUserId;
				$where = array('id=?'=>$id);
				$id = $assetsModel->saveOrUpdateAssetsData($data, $where);
				if($id == 'update')
				{
					$messages['message'] = 'Asset has deleted successfully.';
					$messages['msgtype'] = 'success';
					
				}
				else
				{
					$messages['message'] = 'Asset cannot be deleted.';
					$messages['msgtype'] = 'error';
				}
			}else
				{
						
					$messages['message'] = 'Asset cannot be deleted.';
					$messages['msgtype'] = 'error';
				}

			}
		else
		{
			$messages['message'] = 'Asset cannot be deleted.';$messages['msgtype'] = 'error';
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
			
		$assetsModel = new Assets_Model_Assets();
		$user_id = sapp_Global::_readSession('id');
		$image;
        $filedata = array();
    	// Validate file with size greater than default(Upload Max Filesize)limit
        if ($_FILES["myfile"]["size"] == 0 || $_FILES["myfile"]["size"] > (2*1024*1024)) 
			{
				$this->_helper->json(array('error' => 'filesize'));
			}
		else if(isset($_FILES["myfile"])) {
            $fileName = $_FILES["myfile"]["name"];
			$image = $fileName;
            $fileName = preg_replace('/[^a-zA-Z0-9.\']/', '_', $fileName);			  	
            $newName  = time().'_'.$user_id.'_'.str_replace(' ', '_', $fileName); 
            $filedata['original_name'] = $fileName;
            $filedata['new_name'] = $newName;
			$file_type_array = explode('.',$filedata['original_name']);
			$file_type = $file_type_array[1];
			
			
			move_uploaded_file($_FILES["myfile"]["tmp_name"],ASSETS_IMAGES_TEMP_PATH.$newName);
        	   
			$this->_helper->json(array('filedata' => $filedata));
			//echo $newName;die;
			
        } 
	}
	
    public function uploaddeleteAction()
    {	
	
    	 if(isset($_POST["op"]) && $_POST["op"] == "delete" && isset($_POST['doc_new_name']))
        {
        	$filePath = "";
        	if(isset($_POST["doc_id"]) && $_POST["doc_id"] != ''){
        		// Update attachments field in database by removing deleted attachment
        		$assetsModel = new Assets_Model_Assets();
				$assetImages = $assetsModel->getAssetsDetailsById('id',$_POST["doc_id"]);
				if($empDocuments[0]['attachments']){
					$attData = json_decode($empDocuments[0]['attachments'],true);
					//echo "It is here while deleting ??";exit;
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
				$filePath = ASSETS_IMAGES_TEMP_PATH.$_POST['doc_new_name'];	
				
				// Remove attachment files from upload folder.
	            if (file_exists($filePath)) {
	            	 unlink($filePath);
	            }
				
	            // Update photo gallery with removed attachment.
	            $this->view->path = CA_FILES_PATH;
				$this->view->attachments = $attData;
				$this->view->doc_id = $_POST["doc_id"];				
							
        	}else{
				
        		$filePath =ASSETS_IMAGES_TEMP_PATH.$_POST['doc_new_name'];
	            if (file_exists($filePath)) {
	                unlink($filePath);
	            }
	            
	            $this->_helper->json(array());
        	}
        }
    }
	
	public function viewAction()
	{
		$ImageName=array();
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$objName = 'assets';
				$id = $this->getRequest()->getParam('id');
				$callval = $this->getRequest()->getParam('call');
				$assetsForm = new Assets_Form_Assets();
				$assetsModel = new Assets_Model_Assets();
				$data = $assetsModel->getAssetsDetailsById($id);
				$assetHistory = $assetsModel->getAssetHistory($id);

				if($id)
					{
						$this->view->data= $data;
						 $ImageName =  $data[0]['image'];
						 $images_array = explode(',',$ImageName);
						 $ImageEncName =  $data[0]['imagencrpname'];
						 $images_array_encrypt= explode(',',$ImageEncName);
						 $this->view->images_array_encrypt=$images_array_encrypt;
						 $this->view->images_array=$images_array;
						 $this->view->controllername=$objName;
					     $this->view->assetHistory=$assetHistory;
					}
	}	
	
	public function getsubcategoriesAction()
	{
		$cnval = $this->_getParam('cnval');
		$opt= '<option value=\'\'>Select Sub Category</option>';
		if($cnval!='')
		{
			$SubCategoriesModel = new Assets_Model_AssetCategories();
			$SubCategoriesData = $SubCategoriesModel->getAssetSubCategoriesDetailsById($cnval);
			foreach($SubCategoriesData as $SubCategories)
			{
				$opt.="<option value='".$SubCategories['id']."'>".$SubCategories['name']."</option>";
			}
		}
			
		$this->_helper->json(array('options'=>utf8_encode($opt)));
		
	}
	public function deleteimageAction()
	{
		$filePath = "";
		$doc_new_name = $this->_getParam('doc_new_name');
	    $filePath = ASSETS_IMAGES_TEMP_PATH.$doc_new_name;	
			// Remove attachment files from upload folder.
	          if (file_exists($filePath)) 
					{
	            	 unlink($filePath);
					}
				 $this->_helper->json(array());
	} 

	public function downloadimageAction()
	{
		
		$this->view->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$image_names=$this->_getParam('image_names');
		$imgNames=explode(',',$image_names);
		$zip_file_name='myfile.zip';
		$file_path=DOMAIN.'public/uploads/assets_images_temp/';
		//print_r($imgNames);die;
		if(sizeof($imgNames) > 1){
			//zip
			$this->zipFilesDownload($imgNames);
		}else{
			
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.$imgNames[0].'"');
		//	echo $imgNames[0];die;
			readfile($file_path.$imgNames[0]);
		}
		//die; 
		
	}
	function zipFilesDownload($imgNames){
		$file_path = ASSETS_IMAGES_TEMP_PATH;
		$archive_file_name = time().rand(8,8).'.zip';
	
		$zip = new ZipArchive();
		if ($zip->open($file_path.$archive_file_name, ZIPARCHIVE::CREATE )!==TRUE) {
			exit("cannot open <$archive_file_name>\n");
	
		}
	
		foreach($imgNames as $files){
			$zip->addFile($file_path.$files,$files);
		}
		$zip->close();
	
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.$archive_file_name.'"');
		readfile($file_path.$archive_file_name);
	}
  public	function getemployeesdataAction(){
	    $ajaxContext = $this->_helper->getHelper('AjaxContext');
	    $ajaxContext->addActionContext('getemployeesdata', 'html')->initContext();
		$bunit_id=$this->_getParam('bunitid');
		$employeemodel = new Default_Model_Employee();
		$employeeData = $employeemodel->getEmployeesForServiceDesk($bunit_id);
		$this->view->employeeData=$employeeData;
		
	
	}
	/* public function addpopupAction(){
	 $msgarray = array();
	 $emptyFlag = '';
	 Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
	 $auth = Zend_Auth::getInstance();
	 if($auth->hasIdentity()){
	 $loginUserId = $auth->getStorage()->read()->id;
	 $loginuserRole = $auth->getStorage()->read()->emprole;
	 $loginuserGroup = $auth->getStorage()->read()->group_id;
	 }
	 $id = $this->getRequest()->getParam('id');
	
	
	
	 $controllername = 'assets';
	 $vendorsform = new Assets_Form_Vendors();
	 $vendorsmodel= new Assets_Model_Vendors();
	
	
	 $countriesModel = new Default_Model_Countries();
	 $statesmodel = new Default_Model_States();
	 $citiesmodel = new Default_Model_Cities();
	
	 $allCountriesData = $countriesModel->fetchAll('isactive=1','country')->toArray();
	 $allStatesData = $statesmodel->fetchAll('isactive=1','state')->toArray();
	 $allCitiesData = $citiesmodel->fetchAll('isactive=1','city')->toArray();
	
	 $countrieslistArr = $countriesModel->getTotalCountriesList();
	
	
	 if(sizeof($countrieslistArr)>0){
	 $vendorsform->country->addMultiOption('','Select Country');
	 foreach($countrieslistArr as $countrieslistres)
	 {
	 $vendorsform->country->addMultiOption($countrieslistres['id'],utf8_encode($countrieslistres['country_name']) );
	 }
	 }else{
	 $msgarray['country'] = 'Countries are not configured yet.';
	 }
	 if(isset($_POST['country']) && $_POST['country']!='')
	 {
	 $statesmodel = new Default_Model_States();
	 $statesmodeldata = $statesmodel->getStatesList(intval($_POST['country']));
	 $st_opt = array();
	 if(count($statesmodeldata) > 0)
	 {
	 foreach($statesmodeldata as $dstate)
	 {
	 $st_opt[$dstate['id'].'!@#'.$dstate['state_name']] = $dstate['state_name'];
	 }
	 }
	 $vendorsform->state->addMultiOptions(array(''=>'Select State')+$st_opt);
	 }
	 if(isset($_POST['state']) && $_POST['state']!='')
	 {
	 $citiesmodel = new Default_Model_Cities();
	 $citiesmodeldata = $citiesmodel->getCitiesList(intval($_POST['state']));
	 $ct_opt = array();
	 if(count($citiesmodeldata) > 0)
	 {
	 foreach($citiesmodeldata as $dcity)
	 {
	 $ct_opt[$dcity['id'].'!@#'.$dcity['city_name']] = $dcity['city_name'];
	 }
	 }
	 $vendorsform->city->addMultiOptions(array(''=>'Select City')+$ct_opt);
	 }
	 $vendorsform->setAction(BASE_URL.'assets/assets/addpopup');
	 try
	 {
	 if(is_numeric($id) && $id>0)
	 {
	 $data = $vendorsmodel->getsingleVendorsData($id);
	
	 if(!empty($data))
	 {
	 $vendorsform->populate($data);
	 $vendorsform->setDefault('name',$data['name']);
	 $vendorsform->setDefault('contact_person',$data['contact_person']);
	 $vendorsform->setDefault('address',$data['address']);
	 $vendorsform->setDefault('primary_phone',$data['primary_phone']);
	 $vendorsform->setDefault('secondary_phone',$data['secondary_phone']);
	 $vendorsform->submit->setLabel('Update');
	 $this->view->ermsg = '';
	 	
	 $vendorsform->state->clearMultiOptions();
	 $vendorsform->city->clearMultiOptions();
	 $vendorsform->state->addMultiOption('',utf8_encode("Select State"));
	 $vendorsform->city->addMultiOption('',utf8_encode("Select City"));
	 $countryId = $data['country'];
	 if(isset($_POST['country']))
	 {
	 $countryId = $_POST['country'];
	 }
	 $stateId = $data['state'];
	 if(isset($_POST['state']))
	 {
	 $stateId = $_POST['state'];
	 }
	 $cityId = $data['city'];
	 if(isset($_POST['city']))
	 {
	 $cityId = $_POST['city'];
	 }
	 if($countryId != '')
	 {
	 $statesmodel = new Default_Model_States();
	 $statesData = $statesmodel->getStatesList($countryId);
	 foreach($statesData as $res)
	 	$vendorsform->state->addMultiOption($res['id'],utf8_encode($res['state_name']));
	 	$vendorsform->setDefault('country',$countryId);
	 	}
	 	if($stateId != '')
	 	{
	 	$citiesmodel = new Default_Model_Cities();
	 	$citiesData = $citiesmodel->getCitiesList($stateId);
	 	foreach($citiesData as $res)
	 		$vendorsform->city->addMultiOption($res['id'],utf8_encode($res['city_name']));
	 		$vendorsform->setDefault('state',$stateId);
	 		}
	 		$countrieslistArr = $countriesModel->getTotalCountriesList();
	 		if(sizeof($countrieslistArr)>0)
	 		{
	 		$vendorsform->country->addMultiOption('','Select Country');
	 		foreach($countrieslistArr as $countrieslistres)
	 		{
	 		$vendorsform->country->addMultiOption($countrieslistres['id'],utf8_encode($countrieslistres['country_name']) );
	 		}
	 		}
	 		else
	 		{
	 		$msgarray['country'] = 'Countries are not configured yet.';
	 		}
	 			
	 		}
	 		else
	 		{
	 		$this->view->ermsg = 'norecord';
	 		}
	
	 		}
	 			
	 		$this->view->msgarray = $msgarray;
	 		$this->view->controllername = $controllername;
	 		$this->view->id = $id;
	 		$this->view->form = $vendorsform;
	 		//$this->view->popConfigPermission = $popConfigPermission;
	 		$this->view->form = $vendorsform;
	 			
	 		}
	 		catch(Exception $e)
	 		{
	 		$this->view->ermsg = 'nodata';
	 		}
	 		if($this->getRequest()->getPost()){
	 		if($vendorsform->isValid($this->_request->getPost())){
	 		$country_id	= NULL;
	 		$state_id	= NULL;
	 		$id = $this->_request->getParam('id');
	
	 		$name = $this->_request->getParam('name');
	 		$contact_person = $this->_request->getParam('contact_person');
	 		$address = $this->_request->getParam('address');
	
	 		$country = $this->_request->getParam('country');
	 		$state = intval($this->_request->getParam('state'));
	 		$city = $this->_request->getParam('city');
	
	 		$pincode = $this->_request->getParam('pincode');
	 		$primary_phone = $this->_request->getParam('primary_phone');
	 		$secondary_phone = $this->_request->getParam('secondary_phone');
	
	
	 		$actionflag = '';
	 		$tableid  = '';
	 		$data = array(
	 		'name'=>trim($name),
	 		'contact_person'=>trim($contact_person),
	 		'address'=>trim($address),
	
	 		'country'=>trim($country),
	 		'state'=>trim($state),
	 		'city'=>trim($city),
	 		'pincode'=>trim($pincode),
	 		'primary_phone'=>trim($primary_phone),
	 		'secondary_phone'=>trim($secondary_phone),
	 		'isactive'=>1,
	 		'createdby'=>$loginUserId,
	 		'modifieddate'=>gmdate("Y-m-d H:i:s")
	 		);
	
	
	 			
	 		$data['createdby'] = $loginUserId;
	 		$data['createddate'] = gmdate("Y-m-d H:i:s");
	 		$data['isactive'] = 1;
	 		$where = '';
	 		$actionflag = 1;
	 		$vendorsform->populate($data);
	 		$vendorsform->setDefault('name',$data['name']);
	
	 		//echo "<pre>";print_r($data);die;
	 		$Id = $vendorsmodel->SaveorUpdateVendors($data, $where);
	 			
	 		$vendorData = $vendorsmodel->fetchAll('isactive = 1','name')->toArray();
	 		//echo "<pre>";print_r($vendorData);die;
	 		$opt ='';
	 		foreach($vendorData as $record){
	 		$opt .= sapp_Global::selectOptionBuilder($record['id'], $record['name']);
	 		}
	 		$this->view->vendorData = $opt;
	
	 		$this->view->eventact = 'added';
	 		$close = 'close';
	 		$this->view->popup=$close;
	 		}else
	 		{
	 		$messages = $vendorsform->getMessages();
	 		foreach ($messages as $key => $val)
	 		{
	 		foreach($val as $key2 => $val2)
	 		{
	 		$msgarray[$key] = $val2;
	 		break;
	 		}
	 		if(empty($allCountriesData))
	 		{
	 		$msgarray['country'] = 'Countries are not configured yet.';
	 		}if(empty($allStatesData)){
	 		$msgarray['state'] = 'States are not configured yet.';
	 		}if(empty($allCitiesData)){
	 		$msgarray['city'] = 'Cities are not configured yet.';
	 		}
	 		}
	 		$this->view->msgarray = $msgarray;
	 		}
	 		}
	 		$this->view->controllername = $controllername;
	 		$this->view->form = $vendorsform;
	 		$this->view->ermsg = '';
	
	 		}
	 		*/
}
