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

class Default_JobtitlesController extends Zend_Controller_Action
{

	private $options;
	public function preDispatch()
	{
			

	}
	
	/**
	 * Init
	 *
	 * @see Zend_Controller_Action::init()
	 */
	public function init()
	{
		$this->_options= $this->getInvokeArg('bootstrap')->getOptions();

	}

	/**
	 * @name indexAction
	 *
	 * This method is used to display the jobtitles info
	 *
	 *  @author Asma
	 *  @version 1.0
	 */
	public function indexAction()
	{
		$jobtitlesmodel = new Default_Model_Jobtitles();
		$call = $this->_getParam('call');
		if($call == 'ajaxcall')
		$this->_helper->layout->disableLayout();

		$view = Zend_Layout::getMvcInstance()->getView();
		$objname = $this->_getParam('objname');
		$refresh = $this->_getParam('refresh');
		$dashboardcall = $this->_getParam('dashboardcall',null);
		$data = array();		$searchQuery = '';		$searchArray = array();		$tablecontent='';

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
		$dataTmp = $jobtitlesmodel->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall);
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
	}

	/**
	 * @name viewAction
	 *
	 * This method is used to display particular job title info
	 *
	 *  @author Asma
	 *  @version 1.0
	 */
	public function viewAction()
	{
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();
		$objName = 'jobtitles';
		$jobtitlesform = new Default_Form_jobtitles();
		$jobtitlesform->removeElement("submit");
		$elements = $jobtitlesform->getElements();
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)
			{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
					$element->setAttrib("disabled", "disabled");
				}
			}
		}
		$jobtitlesmodel = new Default_Model_Jobtitles();
		$payfrequencyModal = new Default_Model_Payfrequency();
		try
		{
			if(is_numeric($id) && $id>0)
			{
				$data = $jobtitlesmodel->getsingleJobTitleData($id);
				$payfreqData = $payfrequencyModal->getActivePayFreqData($data[0]['jobpayfrequency']);
				if(sizeof($payfreqData) > 0)
				{
					foreach ($payfreqData as $payfreqres){
						$jobtitlesform->jobpayfrequency->addMultiOption($payfreqres['id'],$payfreqres['freqtype']);
					}
				}
				
					
				
				if(!empty($data) && $data != "norows")
				{
					
				if(!empty($data[0]['jobpayfrequency']))
				{ 
		          $jobPayFreq = $payfrequencyModal->getsinglePayfrequencyData($data[0]['jobpayfrequency']);
					
					if(!empty($jobPayFreq))
					{
						$data[0]['jobpayfrequency'] = $jobPayFreq[0]['freqtype'];
				     }
				 }
					$jobtitlesform->populate($data[0]);
					$this->view->controllername = $objName;
					$this->view->id = $id;
					$this->view->data = $data[0];
					$this->view->ermsg = '';
					$this->view->form = $jobtitlesform;
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
		catch(Exception $ex)
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
		$popConfigPermission = sapp_Global::_checkprivileges(PAYFREQUENCY,$loginuserGroup,$loginuserRole,'add');
	    $this->view->popConfigPermission = $popConfigPermission;
	    
		$objName = 'jobtitles';$emptyFlag=0;
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();

		$jobtitlesform = new Default_Form_jobtitles();
		$jobtitlesmodel = new Default_Model_Jobtitles();
		$payfrequencyModal = new Default_Model_Payfrequency();
		$payfreqData = $payfrequencyModal->getActivePayFreqData();
		$msgarray = array();
		if(sizeof($payfreqData) > 0)
		{
			foreach ($payfreqData as $payfreqres){
				$jobtitlesform->jobpayfrequency->addMultiOption($payfreqres['id'],$payfreqres['freqtype']);
			}

		}else
		{
			$msgarray['jobpayfrequency'] = 'Pay frequency is not configured yet.';
			$emptyFlag++;

		}
		$this->view->msgarray = $msgarray;
		$this->view->emptyFlag = $emptyFlag;
		try
		{
			if($id)
			{
				if(is_numeric($id) && $id>0)
				{
					$data = $jobtitlesmodel->getsingleJobTitleData($id);
					if(!empty($data) && $data != "norows")
					{
						$jobtitlesform->populate($data[0]);
						$jobtitlesform->submit->setLabel('Update');
						$this->view->form = $jobtitlesform;
						$this->view->ermsg = '';
						$this->view->controllername = $objName;
						$this->view->id = $id;
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
			else
			{
				$this->view->form = $jobtitlesform;
				$this->view->ermsg = '';
			}
		}
		catch(Exception $ex)
		{
			$this->view->ermsg = 'nodata';
		}
		if($this->getRequest()->getPost()){
			if($jobtitlesform->isValid($this->_request->getPost())){
				$id = $this->_request->getParam('id');
				$jobtitlecode = $this->_request->getParam('jobtitlecode');
				$jobtitlename = $this->_request->getParam('jobtitlename');
				$jobdescription = $this->_request->getParam('jobdescription');
				$minexperiencerequired = $this->_request->getParam('minexperiencerequired');
				$jobpaygradecode = $this->_request->getParam('jobpaygradecode');
				$jobpayfrequency = $this->_request->getParam('jobpayfrequency');
				$comments = $this->_request->getParam('comments');
				$date = new Zend_Date();
				$actionflag = '';
				$tableid  = '';
				$data = array('jobtitlecode'=>trim($jobtitlecode),
				           'jobtitlename'=>trim($jobtitlename),
						  'jobdescription'=>trim($jobdescription),
						  'minexperiencerequired'=>trim($minexperiencerequired),
						  'jobpaygradecode'=>trim($jobpaygradecode),
						  'jobpayfrequency'=>trim($jobpayfrequency),
						  'comments'=>trim($comments),
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
				$Id = $jobtitlesmodel->SaveorUpdateJobTitleData($data, $where);
				if($Id == 'update')
				{
					$tableid = $id;
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Job title updated successfully."));
				}
				else
				{
					$tableid = $Id;
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Job title added successfully."));
				}
				$menuID = JOBTITLES;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
				$this->_redirect('jobtitles');
			}else
			{
				$messages = $jobtitlesform->getMessages();
				foreach ($messages as $key => $val)
				{
					foreach($val as $key2 => $val2)
					{
						$msgarray[$key] = $val2;
						break;
					}
				}
				if(sizeof($payfreqData) < 1)
				$msgarray['jobpayfrequency'] = 'Pay frequency not configured yet.';
				$this->view->msgarray = $msgarray;
					
			}
		}
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

		$controllername = 'jobtitles';
		$jobtitlesform = new Default_Form_jobtitles();
		$jobtitlesmodel = new Default_Model_Jobtitles();
		$payfrequencyModal = new Default_Model_Payfrequency();
		$payfreqData = $payfrequencyModal->getActivePayFreqData();
		$jobtitlesform->setAction(BASE_URL.'jobtitles/addpopup');

		if(sizeof($payfreqData) > 0)
		{
			foreach ($payfreqData as $payfreqres){
				$jobtitlesform->jobpayfrequency->addMultiOption($payfreqres['id'],$payfreqres['freqtype']);
			}

		}else
		{
			$msgarray['jobpayfrequency'] = 'Pay frequency is not configured yet.';
			$emptyFlag++;

		}
		$this->view->msgarray = $msgarray;
		$this->view->emptyFlag = $emptyFlag;

		if($this->getRequest()->getPost()){
			if($jobtitlesform->isValid($this->_request->getPost())){
				$id = $this->_request->getParam('id');
				$jobtitlecode = $this->_request->getParam('jobtitlecode');
				$jobtitlename = $this->_request->getParam('jobtitlename');
				$jobdescription = $this->_request->getParam('jobdescription');
				$minexperiencerequired = $this->_request->getParam('minexperiencerequired');
				$jobpaygradecode = $this->_request->getParam('jobpaygradecode');
				$jobpayfrequency = $this->_request->getParam('jobpayfrequency');
				$comments = $this->_request->getParam('comments');
				$date = new Zend_Date();
				$actionflag = '';
				$tableid  = '';
				$data = array('jobtitlecode'=>trim($jobtitlecode),
				           'jobtitlename'=>trim($jobtitlename),
						  'jobdescription'=>trim($jobdescription),
						  'minexperiencerequired'=>trim($minexperiencerequired),
						  'jobpaygradecode'=>trim($jobpaygradecode),
						  'jobpayfrequency'=>trim($jobpayfrequency),
						  'comments'=>trim($comments),
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

				$Id = $jobtitlesmodel->SaveorUpdateJobTitleData($data, $where);
				$tableid = $Id;
				$menuID = JOBTITLES;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);

				$jobtitlesData = $jobtitlesmodel->fetchAll('isactive = 1','jobtitlename')->toArray();

				$opt ='';
				foreach($jobtitlesData as $record){
					$opt .= sapp_Global::selectOptionBuilder($record['id'], $record['jobtitlename']);
				}
				$this->view->jobtitlesData = $opt;
					
				$this->view->eventact = 'added';
				$close = 'close';
				$this->view->popup=$close;
			}else
			{
				$messages = $jobtitlesform->getMessages();
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
		$this->view->form = $jobtitlesform;
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
			$jobtitlesmodel = new Default_Model_Jobtitles();
			$positionsModel = new Default_Model_Positions();
			$data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
			$where = array('id=?'=>$id);
			$job_data = $jobtitlesmodel->getsingleJobTitleData($id);
			$Id = $jobtitlesmodel->SaveorUpdateJobTitleData($data, $where);
			if($Id == 'update')
			{
				$positionData = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
				$positionsWhere = array('jobtitleid=?'=>$id);
				$positionsModel->SaveorUpdatePositionData($positionData, $positionsWhere);
				sapp_Global::send_configuration_mail("Job Titles", $job_data[0]['jobtitlename']);
				$menuID = JOBTITLES;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
				$messages['message'] = 'Job title deleted successfully.';
				$messages['msgtype'] = 'success';
			}
			else
			{
				$messages['message'] = 'Job title cannot be deleted.';
				$messages['msgtype'] = 'error';
			}
		}
		else
		{
			$messages['message'] = 'Job title cannot be deleted.';
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
			
			//$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Job title deleted successfully.",'deleteflag'=>$deleteflag)); 
		}
		$this->_helper->json($messages);

	}



}

