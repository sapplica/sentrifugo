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

class Default_WorkeligibilitydoctypesController extends Zend_Controller_Action
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
		$workeligibilitydoctypesmodel = new Default_Model_Workeligibilitydoctypes();
		$call = $this->_getParam('call');
		if($call == 'ajaxcall')
		$this->_helper->layout->disableLayout();

		$view = Zend_Layout::getMvcInstance()->getView();
		$objname = $this->_getParam('objname');
		$refresh = $this->_getParam('refresh');
		$dashboardcall = $this->_getParam('dashboardcall',null);
		$data = array();$searchQuery = '';	$searchArray = array();	$tablecontent='';

		if($refresh == 'refresh')
		{
			if($dashboardcall == 'Yes')
			$perPage = DASHBOARD_PERPAGE;
			else
			$perPage = PERPAGE;

			$sort = 'DESC';$by = 'modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';
			$searchArray = array();
		}
		else
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'modifieddate';
			if($dashboardcall == 'Yes')
			$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
			else
			$perPage = $this->_getParam('per_page',PERPAGE);

			$pageNo = $this->_getParam('page', 1);
			$searchData = $this->_getParam('searchData');
			$searchData = rtrim($searchData,',');
		}

		$dataTmp = $workeligibilitydoctypesmodel->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall);
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
		$objName = 'workeligibilitydoctypes';
		$workeligibilitydoctypesform = new Default_Form_workeligibilitydoctypes();
		$workeligibilitydoctypesform->removeElement("submit");
		$elements = $workeligibilitydoctypesform->getElements();
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)
			{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
					$element->setAttrib("disabled", "disabled");
				}
			}
		}
		$workeligibilitydoctypesmodel = new Default_Model_Workeligibilitydoctypes();
	
		try
		{
			if($id)
			{
				$data = $workeligibilitydoctypesmodel->getsingleWorkEligibilityDocTypeData($id);
	
				if(!empty($data) && $data != 'norows')
				{
					
					if ($data[0]['issuingauthority']==1)
					 {
                      $data[0]['issuingauthority']="Country";
                     } 
                    elseif ($data[0]['issuingauthority']==2) 
                     {
                     $data[0]['issuingauthority']="State";
                     } 
                    else 
                     {
                      $data[0]['issuingauthority']="City";
                     } 
					
					$workeligibilitydoctypesform->populate($data[0]);
					$this->view->form = $workeligibilitydoctypesform;
					$this->view->controllername = $objName;
					$this->view->id = $id;
					$this->view->data = $data[0];
					$this->view->ermsg = '';
				}
				else
				{
					$this->view->ermsg = 'norecord';
				}
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
		}
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();

		$objName = 'workeligibilitydoctypes';
		$workeligibilitydoctypesform = new Default_Form_workeligibilitydoctypes();
		$workeligibilitydoctypesmodel = new Default_Model_Workeligibilitydoctypes();
	
		try
		{
			if($id)
			{
				$data = $workeligibilitydoctypesmodel->getsingleWorkEligibilityDocTypeData($id);
	
				if(!empty($data) && $data != 'norows')
				{
					$workeligibilitydoctypesform->populate($data[0]);
					$workeligibilitydoctypesform->submit->setLabel('Update');
					$this->view->form = $workeligibilitydoctypesform;
					$this->view->controllername = $objName;
					$this->view->id = $id;
					$this->view->ermsg = '';
				}
				else
				{
					$this->view->ermsg = 'norecord';
				}
			}
			else
			{
				$this->view->form = $workeligibilitydoctypesform;
			}
		}
		catch(Exception $e)
		{
			$this->view->ermsg = 'nodata';
		}
		if($this->getRequest()->getPost()){
			if($workeligibilitydoctypesform->isValid($this->_request->getPost())){
				$id = $this->_request->getParam('id');
				$documenttype = $this->_request->getParam('documenttype');
				$issuingauthority = $this->_request->getParam('issuingauthority');
				$description = $this->_request->getParam('description');
				$date = new Zend_Date();
				$actionflag = '';
				$tableid  = '';
				$data = array( 'documenttype'=>trim($documenttype),
				                'issuingauthority'=>$issuingauthority,
				      			 'description'=>trim($description),
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

				$Id = $workeligibilitydoctypesmodel->SaveorUpdateWorkEligibilityDocumentData($data, $where);
				if($Id == 'update')
				{
					$tableid = $id;
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Work eligibility document type updated successfully."));
				}
				else
				{
					$tableid = $Id;
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Work eligibility document type added successfully."));
				}
				$menuID = WORKELIGIBILITYDOCTYPES;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
				$this->_redirect('workeligibilitydoctypes');
			}else
			{
				$messages = $workeligibilitydoctypesform->getMessages();
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


	public function addpopupAction()
	{
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->getRequest()->getParam('id');

		$msgarray = array();
		$controllername = 'workeligibilitydoctypes';
		$workeligibilitydoctypesform = new Default_Form_workeligibilitydoctypes();
		$workeligibilitydoctypesmodel = new Default_Model_Workeligibilitydoctypes();
		$workeligibilitydoctypesform->setAction(BASE_URL.'workeligibilitydoctypes/addpopup');
		if($this->getRequest()->getPost()){
			if($workeligibilitydoctypesform->isValid($this->_request->getPost())){
				$id = $this->_request->getParam('id');
			    $documenttype = $this->_request->getParam('documenttype');
				$issuingauthority = $this->_request->getParam('issuingauthority');
				$description = $this->_request->getParam('description');
				$date = new Zend_Date();
				$actionflag = '';
				$tableid  = '';
				$data = array( 'documenttype'=>trim($documenttype),
				                'issuingauthority'=>$issuingauthority,
				      			 'description'=>trim($description),
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
				$Id = $workeligibilitydoctypesmodel->SaveorUpdateWorkEligibilityDocumentData($data, $where);
				$tableid = $Id;
				$menuID = WORKELIGIBILITYDOCTYPES;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);

				$workeligibilitydoctypesData = $workeligibilitydoctypesmodel->fetchAll('isactive = 1','documenttype')->toArray();
				$opt ='';
				foreach($workeligibilitydoctypesData as $record){
					$opt .= sapp_Global::selectOptionBuilder($record['id'], $record['documenttype']);
				}
				$this->view->workeligibilitydoctypesData = $opt;
					
				$this->view->eventact = 'added';
				$close = 'close';
				$this->view->popup=$close;
			}else
			{
				$messages = $workeligibilitydoctypesform->getMessages();
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
		$this->view->form = $workeligibilitydoctypesform;
		$this->view->ermsg = '';

	}

	public function deleteAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->_request->getParam('objid');
		$deleteflag=$this->_request->getParam('deleteflag');
		$messages['message'] = ''; $messages['msgtype'] = '';$messages['flagtype'] = '';
		$actionflag = 3;
		if($id)
		{
			$workeligibilitydoctypesmodel = new Default_Model_Workeligibilitydoctypes();
			$data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
			$where = array('id=?'=>$id);
			$doc_data = $workeligibilitydoctypesmodel->getsingleWorkEligibilityDocTypeData($id);
			$Id = $workeligibilitydoctypesmodel->SaveorUpdateWorkEligibilityDocumentData($data, $where);
			if($Id == 'update')
			{
				sapp_Global::send_configuration_mail("Work Eligibility Document Type", $doc_data[0]['documenttype']);
				$menuID = WORKELIGIBILITYDOCTYPES;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
				$messages['message'] = 'Work eligibility document type deleted successfully.';
				$messages['msgtype'] = 'success';
			}
			else
			{
				$messages['message'] = 'Work eligibility document type cannot be deleted.';
				$messages['msgtype'] = 'error';
			}
		}
		else
		{
			$messages['message'] = 'Work eligibility document type cannot be deleted.';
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

