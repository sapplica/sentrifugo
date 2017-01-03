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

class Default_AssignmententryreasoncodeController extends Zend_Controller_Action
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
		$assignmententryreasoncodemodel = new Default_Model_Assignmententryreasoncode();	
        $call = $this->_getParam('call');
		if($call == 'ajaxcall')
				$this->_helper->layout->disableLayout();
		
		$view = Zend_Layout::getMvcInstance()->getView();		
		$objname = $this->_getParam('objname');
		$refresh = $this->_getParam('refresh');
		$data = array();
		$searchQuery = '';
		$searchArray = array();
		$tablecontent='';
		
		if($refresh == 'refresh')
		{
			$sort = 'DESC';$by = 'createddate';$perPage = 10;$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else 
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'createddate';
			$perPage = $this->_getParam('per_page',10);
			$pageNo = $this->_getParam('page', 1);
			/** search from grid - START **/
			$searchData = $this->_getParam('searchData');	
			if($searchData != '' && $searchData!='undefined')
			{
				$searchValues = json_decode($searchData);
				foreach($searchValues as $key => $val)
				{
					$searchQuery .= " ".$key." like '%".$val."%' AND ";
					$searchArray[$key] = $val;
				}
				$searchQuery = rtrim($searchQuery," AND");					
			}
			/** search from grid - END **/
		}
				
		$objName = 'assignmententryreasoncode';
		
		$tableFields = array('action'=>'Action','assignmententryreasoncode' => 'Assignment Entry Reason Code','description' => 'Description');
		
			
		$tablecontent = $assignmententryreasoncodemodel->getAssignmentEntryReasonData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
		$dataTmp = array(
			'sort' => $sort,
			'by' => $by,
			'pageNo' => $pageNo,
			'perPage' => $perPage,				
			'tablecontent' => $tablecontent,
			'objectname' => $objName,
			'extra' => array(),
			'tableheader' => $tableFields,
			'jsGridFnName' => 'getAjaxgridData',
			'jsFillFnName' => '',
			'searchArray' => $searchArray
		);			
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
    }

    public function viewAction()
	{	
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		$objName = 'assignmententryreasoncode';
		$assignmententryreasoncodeform = new Default_Form_assignmententryreasoncode();
		$assignmententryreasoncodeform->removeElement("submit");
		$elements = $assignmententryreasoncodeform->getElements();
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)
			{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
				$element->setAttrib("disabled", "disabled");
					}
        	}
        }
		$assignmententryreasoncodemodel = new Default_Model_Assignmententryreasoncode();	
		$data = $assignmententryreasoncodemodel->getsingleAssignmentEntryReasonData($id);
		$assignmententryreasoncodeform->populate($data);
		$this->view->controllername = $objName;
		$this->view->id = $id;
		$this->view->form = $assignmententryreasoncodeform;
	}
	
	
	public function editAction()
	{	
	    $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		
		$assignmententryreasoncodeform = new Default_Form_assignmententryreasoncode();
		$assignmententryreasoncodemodel = new Default_Model_Assignmententryreasoncode();
		if($id)
		{
			$data = $assignmententryreasoncodemodel->getsingleAssignmentEntryReasonData($id);
			$assignmententryreasoncodeform->populate($data);
		}
		$this->view->form = $assignmententryreasoncodeform;
		if($this->getRequest()->getPost()){
		    if($assignmententryreasoncodeform->isValid($this->_request->getPost())){
			    $id = $this->_request->getParam('id'); 
			    $assignmententryreasoncode = $this->_request->getParam('assignmententryreasoncode');
				$description = $this->_request->getParam('description');
				$date = new Zend_Date();
				$actionflag = '';
				$tableid  = ''; 
				   $data = array( 'assignmententryreasoncode'=>trim($assignmententryreasoncode),
				      			 'description'=>trim($description),
								  'modifiedby'=>$loginUserId,
								  'modifieddate'=>$date->get('yyyy-MM-dd HH:mm:ss')
						);
					if($id!=''){
						$where = array('id=?'=>$id);  
						$actionflag = 2;
					}
					else
					{
					    $data['createdby'] = $loginUserId;
						$data['createddate'] = $date->get('yyyy-MM-dd HH:mm:ss');
						$data['isactive'] = 1;
						$where = '';
						$actionflag = 1;
					}
					$Id = $assignmententryreasoncodemodel->SaveorUpdateAssignmentEntryData($data, $where);
					if($Id == 'update')
					{
					   $tableid = $id;
					   $this->_helper->getHelper("FlashMessenger")->addMessage("Assignment Entry Reason Code updated successfully.");
					}   
					else
					{
                       $tableid = $Id; 	
                        $this->_helper->getHelper("FlashMessenger")->addMessage("Assignment Entry Reason Code added successfully.");					   
					}   
					$menuID = ASSIGNMENTENTRYREASONCODE;
					$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
    			    $this->_redirect('assignmententryreasoncode');		
			}else
			{
     			$messages = $assignmententryreasoncodeform->getMessages();
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
	
	public function deleteAction()
	{
	     $auth = Zend_Auth::getInstance();
     		if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
				}
		 $id = $this->_request->getParam('objid');
		 $messages['message'] = '';
		 $actionflag = 3;
		    if($id)
			{
			$assignmententryreasoncodemodel = new Default_Model_Assignmententryreasoncode();
			  $data = array('isactive'=>0);
			  $where = array('id=?'=>$id);
			  $Id = $assignmententryreasoncodemodel->SaveorUpdateAssignmentEntryData($data, $where);
			    if($Id == 'update')
				{
				   $menuID = ASSIGNMENTENTRYREASONCODE;
				   $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id); 
				   $messages['message'] = 'Assignment entry reason code deleted successfully.';
				}   
				else
                   $messages['message'] = 'Assignment entry reason code cannot be deleted.';				
			}
			else
			{ 
			 $messages['message'] = 'Assignment entry reason code cannot be deleted.';
			}
			$this->_helper->json($messages);
		
	}
}

