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

class Default_IdentitydocumentsController extends Zend_Controller_Action
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
		$identitydocumentsModel = new Default_Model_Identitydocuments();	
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
			$sort = 'DESC';$by = 'i.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else 
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'i.modifieddate';
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
				
		$dataTmp = $identitydocumentsModel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);		 		
					
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
		//$this->renderScript('commongrid/index.phtml');
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
		$identitydocumentsform = new Default_Form_identitydocuments();
		$msgarray = array();
		$identitydocumentsform->setAttrib('action',BASE_URL.'identitydocuments/add');
		$this->view->form = $identitydocumentsform; 
		$this->view->msgarray = $msgarray; 
		$this->view->ermsg = '';
			if($this->getRequest()->getPost()){
				 $result = $this->save($identitydocumentsform);	
				 $this->view->msgarray = $result; 
			}  		
		$this->render('form');	
	}

	public function viewAction()
	{	
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		$objName = 'identitydocuments';
		$identitydocumentsform = new Default_Form_identitydocuments();
		$identitydocumentsform->removeElement("submit");
		$elements = $identitydocumentsform->getElements();
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)
			{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
				$element->setAttrib("disabled", "disabled");
					}
        	}
        }
		try
		{
		    if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					$identitydocumentsModel = new Default_Model_Identitydocuments();	
					$data = $identitydocumentsModel->getIdentitydocumnetsrecordwithID($id);
					if(!empty($data))
					{
						$data = $data[0]; 
						$identitydocumentsform->populate($data);
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
		if($data['mandatory']=='1'){
			$data['mandatory']='yes';
		}else{
			$data['mandatory']='no';
		}
		
	if($data['expiry']=='1'){
			$data['expiry']='yes';
		}else{
			$data['expiry']='no';
		}
		$this->view->controllername = $objName;
		$this->view->id = $id;
		$this->view->data = $data;
		$this->view->flag = 'view';
		$this->view->form = $identitydocumentsform;
		$this->render('form');	
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
		
		$identitydocumentsform = new Default_Form_identitydocuments();
		$identitydocumentsModel = new Default_Model_Identitydocuments();
		$identitydocumentsform->submit->setLabel('Update');
		try
        {		
			if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					$data = $identitydocumentsModel->getIdentitydocumnetsrecordwithID($id);
					if(!empty($data))
					{
						  $data = $data[0];
						$identitydocumentsform->populate($data);
						$identitydocumentsform->setAttrib('action',BASE_URL.'identitydocuments/edit/id/'.$id);
                        $this->view->data = $data;
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
				$this->view->ermsg = 'nodata';
			}
		}	
		catch(Exception $e)
		{
			   $this->view->ermsg = 'nodata';
		}	
		$this->view->form = $identitydocumentsform;
		if($this->getRequest()->getPost()){
      		$result = $this->save($identitydocumentsform);	
		    $this->view->msgarray = $result; 
		}
		$this->render('form');	
	}
	
	public function save($identitydocumentsform)
	{
	  $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		} 
	    $identitydocumentsModel = new Default_Model_Identitydocuments();
		$msgarray = array();
	    
		  if($identitydocumentsform->isValid($this->_request->getPost())){
            try{
            $id = $this->_request->getParam('id');
            $document_name = trim($this->_request->getParam('document_name'));
            $mandatory = $this->_request->getParam('mandatory');
            $expiry = $this->_request->getParam('expiry');	
			$description = $this->_request->getParam('description');
			$actionflag = '';
			$tableid  = ''; 
			   $data = array('document_name'=>$document_name,
			   				 'mandatory'=>$mandatory,
			   				 'expiry'=>$expiry,	 	 
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
				$Id = $identitydocumentsModel->SaveorUpdateIdentitydocumentsData($data, $where);
				if($Id == 'update')
				{
				   $tableid = $id;
				   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Identity documents updated successfully."));
				}   
				else
				{
				   $tableid = $Id; 	
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Identity documents added successfully."));					   
				}   
				$menuID = IDENTITYDOCUMENTS;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
				$this->_redirect('identitydocuments');	
                  }
        catch(Exception $e)
          {
             $msgarray['service_desk_name'] = "Something went wrong, please try again.";
             return $msgarray;
          }
		}else
		{
			$messages = $identitydocumentsform->getMessages();
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
		  $deleteflag=$this->_request->getParam('deleteflag');
		  $messages['message'] = '';
		  $messages['msgtype'] = '';
		  $messages['flagtype'] = '';
		  $documentname = '';
		  $actionflag = 3;
		    if($id)
			{
			 $identitydocumentsModel = new Default_Model_Identitydocuments();
			  $identitydocumentdata = $identitydocumentsModel->getIdentitydocumnetsrecordwithID($id);
			  if(!empty($identitydocumentdata))
			   $documentname = $identitydocumentdata[0]['document_name'];
			   
			  $data = array('isactive'=>0,
			                'modifieddate'=>gmdate("Y-m-d H:i:s"));
			  $where = array('id=?'=>$id);
			  $Id = $identitydocumentsModel->SaveorUpdateIdentitydocumentsData($data, $where);
			    if($Id == 'update')
				{
				   $menuID = IDENTITYDOCUMENTS;
				   $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id); 
				   $configmail = sapp_Global::send_configuration_mail('Identity Document',$documentname);
				   $messages['message'] = 'Identity document deleted successfully.';
				    $messages['msgtype'] = 'success';
				}   
				else
				{
                   $messages['message'] = 'Identity document cannot be deleted.';
                   $messages['msgtype'] = 'error'; 
                }				   
			}
			else
			{ 
			 $messages['message'] = 'Identity document cannot be deleted.';
			  $messages['msgtype'] = 'error';
			}
			// delete success message after delete in view
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
	
}

