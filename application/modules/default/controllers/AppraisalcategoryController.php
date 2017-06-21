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

class Default_AppraisalcategoryController extends Zend_Controller_Action
{

    private $options;
	public function preDispatch()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('getappraisalcategory', 'json')->initContext();
	}
	
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
    }

    public function indexAction()
    {
		$appraisalCategoryModel = new Default_Model_Appraisalcategory();	
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
			$sort = 'DESC';$by = 'ac.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else 
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'ac.modifieddate';
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
				
		$dataTmp = $appraisalCategoryModel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);		 		
					
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
		}
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		$appraisalCategoryForm = new Default_Form_Appraisalcategory();
		$msgarray = array();
		$appraisalCategoryForm->setAttrib('action',BASE_URL.'appraisalcategory/add');
		$this->view->form = $appraisalCategoryForm; 
		$this->view->msgarray = $msgarray; 
		$this->view->ermsg = '';
			if($this->getRequest()->getPost()){
				 $result = $this->save($appraisalCategoryForm);	
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
		$objName = 'appraisalcategory';
		
		try
		{
		    if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					$appraisalCategoryModel = new Default_Model_Appraisalcategory();	
					$data = $appraisalCategoryModel->getAppraisalCategoryDatabyID($id);
					$previ_data = sapp_Global::_checkprivileges(APPRAISALCATEGORIES,$login_group_id,$login_role_id,'edit');

                   
					if(!empty($data))
					{
						$data = $data[0]; 
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
				$this->view->controllername ='appraisalcategory';			
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
		}
	 	
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		
		$appraisalCategoryForm = new Default_Form_Appraisalcategory();
		$appraisalCategoryModel = new Default_Model_Appraisalcategory();
		$appraisalCategoryForm->submit->setLabel('Update');
		try
        {		
			if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					$data = $appraisalCategoryModel->getAppraisalCategoryDatabyID($id);
					if(!empty($data))
					{
					  $data = $data[0];
					  
					  // When category is already used category details can't be edited
					  if ($data['isused'] == 1) {
					  	$this->view->ermsg = 'norecord';
					  } else {
						$appraisalCategoryForm->populate($data);
						$appraisalCategoryForm->setAttrib('action',BASE_URL.'appraisalcategory/edit/id/'.$id);
                        $this->view->data = $data;
					  }
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
				$this->view->ermsg = '';
			}
		}	
		catch(Exception $e)
		{
			   $this->view->ermsg = 'nodata';
		}	
		$this->view->form = $appraisalCategoryForm;
		if($this->getRequest()->getPost()){
      		$result = $this->save($appraisalCategoryForm);	
		    $this->view->msgarray = $result; 
		}
		$this->render('form');	
	}
	
	public function save($appraisalCategoryForm)
	{
	  $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		} 
	    $appraisalCategoryModel = new Default_Model_Appraisalcategory();
		$msgarray = array();
	    
		  if($appraisalCategoryForm->isValid($this->_request->getPost())){
            try{
            $id = $this->_request->getParam('id');
            $appraisal_category_name = $this->_request->getParam('category_name');	
			$description = $this->_request->getParam('description');
			$actionflag = '';
			$tableid  = ''; 
			   $data = array('category_name'=>$appraisal_category_name, 
							 'description'=>($description!=''?trim($description):NULL),
							  'modifiedby'=>$loginUserId,
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
					$data['isactive'] = 1;
					$where = '';
					$actionflag = 1;
				}
				$Id = $appraisalCategoryModel->SaveorUpdateAppraisalCategoryData($data, $where);
				if($Id == 'update')
				{
				   $tableid = $id;
				   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Parameter updated successfully."));
				}   
				else
				{
				   $tableid = $Id; 	
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Parameter added successfully."));					   
				}   
				$menuID = APPRAISALCATEGORIES;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
				$this->_redirect('appraisalcategory');	
                  }
        catch(Exception $e)
          {
             $msgarray['category_name'] = "Something went wrong, please try again.";
             return $msgarray;
          }
		}else
		{
			$messages = $appraisalCategoryForm->getMessages();
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
	
	public function deleteAction()
	{
	     $auth = Zend_Auth::getInstance();
     		if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
				}
		 $id = $this->_request->getParam('objid');
		$deleteflag= $this->_request->getParam('deleteflag');
		 $messages['message'] = '';
		 $messages['msgtype'] = '';
		 $count = 0;
		 $actionflag = 3;
		    if($id)
			{
			  $appraisalCategoryModel = new Default_Model_Appraisalcategory();			  
			  $appCategorydata = $appraisalCategoryModel->getAppraisalCategoryDatabyID($id);
			
			  
			  if($appCategorydata[0]['isused'] == 0)
			  {
				$appQuestionModel = new Default_Model_Appraisalquestions();
				$appQuesData = $appQuestionModel->getAppraisalQuestionsByCategotyID($id);
				$d=array();
				foreach($appQuesData as $key => $value)
				{
					 
					$d[] = $value['id'];
				}
				$d=implode(',',$d);
				
				if(sizeof($appQuesData) == 0)
				{				
				  $data = array('isactive'=>0,'modifiedby'=>$loginUserId,'modifieddate'=>gmdate("Y-m-d H:i:s"));
				  $where = array('id=?'=>$id);
				  $Id = $appraisalCategoryModel->SaveorUpdateAppraisalCategoryData($data, $where);
				   
				    if($Id == 'update')
					{
						$menuID = APPRAISALCATEGORIES;
						$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
						/***
						** commented on 29-04-2015 by sapplica
						** need to integrate mail template
							$configmail = sapp_Global::send_configuration_mail('Category',$appCategorydata[0]['category_name']);	
						***/					   
						$messages['message'] = 'Parameter deleted successfully.';
						$messages['msgtype'] = 'success';
					}   
					else
					{
	                   $messages['message'] = 'Parameter cannot be deleted.';
	                   $messages['msgtype'] = 'error';
	                }
				} else {
					$messages['message'] = 'Parameter cannot be deleted as there are active questions assigned to it.';
				 	$messages['msgtype'] = 'error';
			  	}
			  	if(sizeof($appQuesData)> 0)
			  	{ 
			  		
			  	 $data = array('isactive'=>0,'modifiedby'=>$loginUserId,'modifieddate'=>gmdate("Y-m-d H:i:s"));
				  $where = array('id=?'=>$id);
				  $Id = $appraisalCategoryModel->SaveorUpdateAppraisalCategoryData($data, $where);
			  			
			  	  $appQuestionModel->UpdateAppraisalQuestionData($d);
			  		if($Id == 'update')
			  		{
			  			$menuID = APPRAISALCATEGORIES;
			  			$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
			  			/***
			  			 ** commented on 29-04-2015 by sapplica
			  			 ** need to integrate mail template
			  			 $configmail = sapp_Global::send_configuration_mail('Category',$appCategorydata[0]['category_name']);
			  			***/
			  			$messages['message'] = 'Parameter deleted successfully.';
			  			$messages['msgtype'] = 'success';
			  		}
			  		else
			  		{
			  			$messages['message'] = 'Parameter cannot be deleted.';
			  			$messages['msgtype'] = 'error';
			  		}
			  	}
			  } 
			  else {
				$messages['message'] = 'Parameter cannot be deleted as it is being used in appraisal process.';
			 	$messages['msgtype'] = 'error';
			  }
			}
			else
			{ 
			 $messages['message'] = 'Parameter cannot be deleted.';
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
	
	public function addpopupAction()
	{
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->getRequest()->getParam('id');

		$msgarray = array();
		$controllername = 'appraisalcategory';
		$appraisalCategoryForm = new Default_Form_Appraisalcategory();
		$appraisalCategoryModel = new Default_Model_Appraisalcategory();
		$appraisalCategoryForm->setAction(BASE_URL.'appraisalcategory/addpopup');
		if($this->getRequest()->getPost()){
			if($appraisalCategoryForm->isValid($this->_request->getPost())){
			$id = $this->_request->getParam('id');
            $category_name = $this->_request->getParam('category_name');	
			$description = $this->_request->getParam('description');
			$actionflag = '';
			$tableid  = ''; 
			   $data = array('category_name'=>$category_name, 
							 'description'=>$description,
							  'modifiedby'=>$loginUserId,
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
					$data['isactive'] = 1;
					$where = '';
					$actionflag = 1;
				}
				$Id = $appraisalCategoryModel->SaveorUpdateAppraisalCategoryData($data, $where);
				$tableid = $Id;
				$menuID = APPRAISALCATEGORIES;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);

				$appraisalcategoryData = $appraisalCategoryModel->getAppraisalCategorysData();
				$opt ='';
				foreach($appraisalcategoryData as $record){
					$opt .= sapp_Global::selectOptionBuilder($record['id'], utf8_encode($record['category_name']));
				}
				$this->view->departmentData = $opt;
					
				$this->view->eventact = 'added';
				$close = 'close';
				$this->view->popup=$close;
			}else
			{
				$messages = $appraisalCategoryForm->getMessages();
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
		$this->view->form = $appraisalCategoryForm;
		$this->view->ermsg = '';
		$this->render('form');	
	}
	
	public function getappraisalcategoryAction()
	{
		$this->_helper->layout->disableLayout();
		$appraisalCategoryModel = new Default_Model_Appraisalcategory();
		$appraisalinitmodel = new Default_Model_Appraisalinit();
		$result['result'] = 'success';
		$result['data'] = "<option value=''>Select Parameter</option>";
		$categoryids='';
		$appraisalid = $this->_request->getParam('appraisalid');
		$data = $appraisalinitmodel->getConfigData($appraisalid);
		if(!empty($data))
		{
			$categoryids = $data[0]['category_id'];
		}
		if($categoryids)
		{
			$appraisalCategoriesData = $appraisalCategoryModel->getCategoryNameByIds($categoryids);
			if(!empty($appraisalCategoriesData))
			{
				foreach($appraisalCategoriesData as $data)
				{
					$result['data'].="<option value='".$data['id']."'>".utf8_encode($data['category_name'])."</option>"; 
				}
			}
			else
			{
				$result['result'] = 'error';
				$result['data']="Category names are not configured yet.";
			}
		}else
		{
			$result['result'] = 'error';
			$result['data']="Category names are not configured yet.";
		}
		$this->_helper->json($result);
	}
	
}

