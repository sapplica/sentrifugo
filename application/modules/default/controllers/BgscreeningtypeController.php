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

class Default_BgscreeningtypeController extends Zend_Controller_Action
{

    private $options;
	public function preDispatch()
	{
			
	}
	
    public function init()
    {
    }
	
	public function indexAction()
	{
		$bgscreeningtypemodel = new Default_Model_Bgscreeningtype();		
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
			$sort = 'DESC';$by = 'modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else 
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'modifieddate';
			if($dashboardcall == 'Yes')
				$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
			else $perPage = $this->_getParam('per_page',PERPAGE);
			$pageNo = $this->_getParam('page', 1);			
			$searchData = $this->_getParam('searchData');				
		}
		
		$dataTmp = $bgscreeningtypemodel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();		
	}
	
	public function viewAction()
	{
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
		$permission = 'No';
		$id = intVal($this->getRequest()->getParam('id'));
		if(is_int($id) && $id != 0)
		{
			$callval = $this->getRequest()->getParam('call');
			if($callval == 'ajaxcall')
				$this->_helper->layout->disableLayout();
			$permission = sapp_Global::_checkprivileges(BGSCREENINGTYPE,$loginuserGroup,$loginuserRole,'edit');	
			$objName = 'bgscreeningtype';
			$bgscreeningtypeform = new Default_Form_bgscreeningtype();
			$bgscreeningtypemodel = new Default_Model_Bgscreeningtype();
			$data = $bgscreeningtypemodel->getSingleScreeningtypeData($id);	
			if(!empty($data))
			{
				$bgscreeningtypeform->populate($data);	
				$elements = $bgscreeningtypeform->getElements();
				if(count($elements)>0)
				{
					foreach($elements as $key=>$element)
					{
						if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
						$element->setAttrib("disabled", "disabled");
							}
					}
				}
				$this->view->controllername = $objName;
				$this->view->id = $id;
				$this->view->data = $data;
				$this->view->form = $bgscreeningtypeform;
				$this->view->permission = $permission;		
				$this->view->ermsg = '';
			}else{
				$this->view->ermsg = 'nodata';
			}			
		}else{
			$this->view->ermsg = 'nodata';
		}
	}
	
	public function editAction()
	{
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = intval($this->getRequest()->getParam('id'));
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		
		$bgscreeningtypeform = new Default_Form_bgscreeningtype();
	    $bgscreeningtypemodel = new Default_Model_Bgscreeningtype();		
		$bgscreeningtypeform->submit->setLabel('Update');
		if($id)
		{
		 	$data = $bgscreeningtypemodel->getSingleScreeningtypeData($id);
			if(!empty($data))
			{
				$bgscreeningtypeform->setAttrib('action',BASE_URL.'bgscreeningtype/edit/id/'.$id);
				$bgscreeningtypeform->populate($data);			
				$this->view->ermsg = '';
			}else{
				$this->view->ermsg = 'nodata';
			}
		}		
		$this->view->form = $bgscreeningtypeform;
		if($this->getRequest()->getPost())
		{
      		$result = $this->save($bgscreeningtypeform);	
            $this->view->msgarray = $result; 
			$this->view->messages = $result;
		}
	}
	
	public function addAction()
	{
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
			
		$bgscreeningtypeform = new Default_Form_bgscreeningtype();
		$bgscreeningtypeform->setAttrib('action',BASE_URL.'bgscreeningtype/add');
        $this->view->form = $bgscreeningtypeform; 
		if($this->getRequest()->getPost())
		{
		    $result = $this->save($bgscreeningtypeform);	
            $this->view->msgarray = $result; 
			$this->view->messages = $result;	
        }
		  		
	}
	
	public function save($bgscreeningtypeform)
	{
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		} 
		$id = $this->_request->getParam('id'); 
		$type = $this->_request->getParam('type'); 
	    $bgscreeningtypemodel = new Default_Model_Bgscreeningtype();		
		$typeExistance = $bgscreeningtypemodel->checktypeduplicates($type,$id);
		$flag = 'true';
		if($typeExistance)
		{
			$msgarray['type'] = 'Given screening type already exists.';
			$flag = 'false';
		}
		if($bgscreeningtypeform->isValid($this->_request->getPost()) && $flag == 'true')
		{
			$id = $this->_request->getParam('id'); 
			$type = $this->_request->getParam('type'); 
			$description = $this->_request->getParam('description'); 			
			
			if(!$typeExistance)
			{				
				$date = new Zend_Date();
				$actionflag = '';
				$tableid  = '';

				$data = array(  
								'type'				=>		$type,
								'description'		=>		$description,
								'modifiedby'		=>		$loginUserId,
								'modifieddate'		=>		gmdate("Y-m-d H:i:s")
							);
				if($id!='')
				{
					$where = array('id=?'=>$id);  
					$actionflag = 2;
				}else
				{
					$data['createdby'] = $loginUserId;
					$data['createddate'] = gmdate("Y-m-d H:i:s");
					$data['isactive'] = 1;
					$where = '';
					$actionflag = 1;
				}
				$Id = $bgscreeningtypemodel->SaveorUpdateScreeningtype($data, $where);
				if($Id == 'update')
				{
				   $tableid = $id;
				   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Screening type updated successfully."));
				}   
				else
				{
				   $tableid = $Id; 	
				   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Screening type added successfully."));					   
				}   
				$menuID = BGSCREENINGTYPE;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
				$this->_redirect('bgscreeningtype');
			}
			else
			{
				$msgarray['message'] = 'Given Screening type already exists.';
				$msgarray['msgtype'] = 'error';
				return $msgarray;
			}
		}
		else
		{
			$messages = $bgscreeningtypeform->getMessages();
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
		 $messages['message'] = '';$messages['msgtype'] = '';
		 $actionflag = 3;
		if($id)
		{
			$bgscreeningtypemodel = new Default_Model_Bgscreeningtype();
			$checkagencyexistance = $bgscreeningtypemodel->checkagencyfortype($id);
			if($checkagencyexistance == 0)
			{
				$data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
				$where = array('id=?'=>$id);
				$Id = $bgscreeningtypemodel->SaveorUpdateScreeningtype($data, $where);
				if($Id == 'update')
				{
					$menuID = BGSCREENINGTYPE;
					$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id); 
					$messages['message'] = 'Screening type deleted successfully.';
					$messages['msgtype'] = 'success';
				}   
				else
				{
					$messages['message'] = 'Screening type cannot be deleted.';				
					$messages['msgtype'] = 'error';
				}
			}else{
				$messages['message'] = 'Since agencies are associated with this screening type, you cannot delete it.';		
				$messages['msgtype'] = 'error';
			}
		}
		else
		{ 
		    $messages['message'] = 'Screening type cannot be deleted.';
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
		$controllername = 'bgscreeningtype';
		$msgarray = array();	
		$bgscreeningtypeform = new Default_Form_bgscreeningtype();
		$bgscreeningtypeform->setAttrib('action',BASE_URL.'bgscreeningtype/addpopup');
        $this->view->form = $bgscreeningtypeform; 
		if($this->getRequest()->getPost())
		{
			$id = $this->_request->getParam('id'); 
			$type = $this->_request->getParam('type'); 			
		    $bgscreeningtypemodel = new Default_Model_Bgscreeningtype();		
			$typeExistance = $bgscreeningtypemodel->checktypeduplicates($type,$id);
			$flag = 'true';
			if($typeExistance)
			{
				$msgarray['type'] = 'Screening type already exists.';
				$flag = 'false';
			}
			if($bgscreeningtypeform->isValid($this->_request->getPost()) && $flag == 'true')
			{
				$id = $this->_request->getParam('id'); 
				$type = $this->_request->getParam('type'); 
				$description = $this->_request->getParam('description'); 			
			
				if(!$typeExistance)
				{				
					$date = new Zend_Date();
					$actionflag = '';
					$tableid  = '';

					$data = array(  
									'type'				=>		$type,
									'description'		=>		$description,
									'modifiedby'		=>		$loginUserId,
									'modifieddate'		=>		gmdate("Y-m-d H:i:s")
								);
					if($id!='')
					{
						$where = array('id=?'=>$id);  
						$actionflag = 2;
					}else
					{
						$data['createdby'] = $loginUserId;
						$data['createddate'] = gmdate("Y-m-d H:i:s");
						$data['isactive'] = 1;
						$where = '';
						$actionflag = 1;
					}
					$Id = $bgscreeningtypemodel->SaveorUpdateScreeningtype($data, $where);
					$tableid = $Id;
					$menuID = BGSCREENINGTYPE;
					$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
					
					$screeningData = $bgscreeningtypemodel->fetchAll('isactive = 1','type')->toArray();
					$opt ='';   
					foreach($screeningData as $record)
					{
						$opt .= sapp_Global::selectOptionBuilder($record['id'], $record['type']);
					}
					$this->view->screeningData = $opt;					
					
					$this->view->eventact = 'added';
					$close = 'close';
					$this->view->popup=$close;
				}
				else
				{								   
					$msgarray['message'] = 'Screening type already exists.';
					$msgarray['msgtype'] = 'error';					
				}
			}
			else
			{
				$messages = $bgscreeningtypeform->getMessages();
				foreach ($messages as $key => $val)
				{
					foreach($val as $key2 => $val2)
					{
						$msgarray[$key] = $val2;
						break;
					}
				}
			}
        }
		$this->view->controllername = $controllername;
		$this->view->msgarray = $msgarray; 
		$this->view->messages = $msgarray;
	}
}