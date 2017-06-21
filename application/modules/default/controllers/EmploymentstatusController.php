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

class Default_EmploymentstatusController extends Zend_Controller_Action
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
		$employmentstatusmodel = new Default_Model_Employmentstatus();
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

			$sort = 'DESC';$by = 'e.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';
			$searchArray = array();
		}
		else
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'e.modifieddate';
			if($dashboardcall == 'Yes')
			$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
			else
			$perPage = $this->_getParam('per_page',PERPAGE);

			$pageNo = $this->_getParam('page', 1);
			$searchData = $this->_getParam('searchData');
			$searchData = rtrim($searchData,',');
		}
		$dataTmp = $employmentstatusmodel->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall);
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
		$objName = 'employmentstatus';
		$employmentstatusform = new Default_Form_employmentstatus();
		$employmentstatusform->removeElement("submit");
		$elements = $employmentstatusform->getElements();
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)
			{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
					$element->setAttrib("disabled", "disabled");
				}
			}
		}
		$employmentstatusmodel = new Default_Model_Employmentstatus();
		try
		{
			if(is_numeric($id) && $id>0)
			{
				$data = $employmentstatusmodel->getsingleEmploymentstatusData($id);
				if(!empty($data))
				{
					$particularstatusnameArr = $employmentstatusmodel->getParticularStatusName($data['workcodename']);
					if(!empty($particularstatusnameArr)){
					$employmentstatusform->workcodename->addMultiOption($particularstatusnameArr[0]['id'],utf8_encode($particularstatusnameArr[0]['employemnt_status']));
					 //overwriting the value of employment status
					$data['workcodename']=$particularstatusnameArr[0]['employemnt_status'];
					}

					$employmentstatusform->populate($data);
					$employmentstatusform->setDefault('workcodename',$data['workcodename']);
					$this->view->ermsg = '';
				}
				else
				{
					$this->view->ermsg = 'norecord';
				}
				$employmentstatusform->populate($data);
				$this->view->controllername = $objName;
				$this->view->id = $id;
				$this->view->data = $data;
				$this->view->form = $employmentstatusform;
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

		$employmentstatusform = new Default_Form_employmentstatus();
		$employmentstatusmodel = new Default_Model_Employmentstatus();
			
		try	{
			if($id)
			{	//Edit
				if(is_numeric($id) && $id>0)
				{
					$data = $employmentstatusmodel->getsingleEmploymentstatusData($id);
					if(!empty($data))
					{
						$particularstatusnameArr = $employmentstatusmodel->getParticularStatusName($data['workcodename']);
						$employmentstatusform->submit->setLabel('Update');
						if(!empty($particularstatusnameArr))
						$employmentstatusform->workcodename->addMultiOption($particularstatusnameArr[0]['id'],utf8_encode($particularstatusnameArr[0]['employemnt_status']));
						$employmentstatusform->populate($data);
						$employmentstatusform->setDefault('workcodename',$data['workcodename']);
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
				$activeEmploymentStatusArr =  $employmentstatusmodel->getEmploymentStatuslist();
				$newarr = array();  $empstatusstr = '';
				if(!empty($activeEmploymentStatusArr))
				{
					for($i=0;$i<sizeof($activeEmploymentStatusArr);$i++)
					{
						$newarr1[] = $activeEmploymentStatusArr[$i]['workcodename'];
					}
					$empstatusstr = implode(",",$newarr1);

				}
				if($empstatusstr !='')
				$statusArr = $employmentstatusmodel->getStatuslist($empstatusstr);
				else
				$statusArr = $employmentstatusmodel->getCompleteStatuslist();
				if(!empty($statusArr))
				{
					$employmentstatusform->workcodename->addMultiOption('','Select Work Code');

					for($i=0;$i<sizeof($statusArr);$i++)
					{
						$employmentstatusform->workcodename->addMultiOption($statusArr[$i]['id'],utf8_encode($statusArr[$i]['employemnt_status']));
					}
				}
				$this->view->statusArr = $statusArr;
				$this->view->ermsg = '';
			}
			$this->view->form = $employmentstatusform;
		}
		catch(Exception $e)
		{
			$this->view->ermsg = 'nodata';
		}
		if($this->getRequest()->getPost())
		{
			if($employmentstatusform->isValid($this->_request->getPost())){
				$id = $this->_request->getParam('id');
				$workcode = $this->_request->getParam('workcode');
				$workcodename = $this->_request->getParam('workcodename');
				$default_leaves = $this->_request->getParam('default_leaves');
				$description = $this->_request->getParam('description');
				$date = new Zend_Date();
				$actionflag = '';
				$tableid  = '';
				$data = array('workcode'=>trim($workcode),
				           'workcodename'=>trim($workcodename),
						  'description'=>trim($description),
						  'modifiedby'=>$loginUserId,
							'modifieddate'		=>		gmdate("Y-m-d H:i:s")	
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
				$Id = $employmentstatusmodel->SaveorUpdateEmploymentStatusData($data, $where);
				if($Id == 'update')
				{
					$tableid = $id;
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Employment status updated successfully."));
				}
				else
				{
					$tableid = $Id;
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Employment status  added successfully."));
				}
				$menuID = EMPLOYMENTSTATUS;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
				$this->_redirect('employmentstatus');
			}else
			{
				$messages = $employmentstatusform->getMessages();
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
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->_request->getParam('objid');
		$deleteflag=$this->_request->getParam('deleteflag');
		$messages['message'] = '';$messages['msgtype'] = '';
		$messages['flagtype'] = '';
		$actionflag = 3;
		if($id)
		{
			$employmentstatusmodel = new Default_Model_Employmentstatus();
			$data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
			$where = array('id=?'=>$id);
			$status_data = $employmentstatusmodel->getsingleEmploymentstatusData($id);

			$Id = $employmentstatusmodel->SaveorUpdateEmploymentStatusData($data, $where);
			if($Id == 'update')
			{
				$particularstatusnameArr = $employmentstatusmodel->getParticularStatusName($status_data['workcodename']);
				sapp_Global::send_configuration_mail("Employment Status", utf8_encode($particularstatusnameArr[0]['employemnt_status']));
				$menuID = EMPLOYMENTSTATUS;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
				$messages['message'] = 'Employment status deleted successfully.';$messages['msgtype'] = 'success';
			}
			else
			{
				$messages['message'] = 'Employment status cannot be deleted.';
				$messages['msgtype'] = 'error';
			}
		}
		else
		{
			$messages['message'] = 'Employment status cannot be deleted.';$messages['msgtype'] = 'error';
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
			//$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Employment status deleted successfully.",'deleteflag'=>$deleteflag)); 
		}
		$this->_helper->json($messages);
		//$this->_helper->json($messages);
	}

	public function addpopupAction()
	{
            $screenFlag = "";
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->getRequest()->getParam('id');
		if($this->getRequest()->getParam('screenflag')){
		  $screenFlag = $this->getRequest()->getParam('screenflag');
		}
		
        $boxid = $this->_getParam('boxid',null);
        $fromcontroller = $this->_getParam('fromcontroller',null);
		$msgarray = array();
		$controllername = 'employmentstatus';
		$employmentstatusform = new Default_Form_employmentstatus();
		$employmentstatusmodel = new Default_Model_Employmentstatus();
		$employmentstatusform->setAction(BASE_URL.'employmentstatus/addpopup');

		$activeEmploymentStatusArr =  $employmentstatusmodel->getEmploymentStatuslist();
		$newarr = array();  $empstatusstr = '';
		if(!empty($activeEmploymentStatusArr))
		{
			for($i=0;$i<sizeof($activeEmploymentStatusArr);$i++)
			{
				$newarr1[] = $activeEmploymentStatusArr[$i]['workcodename'];
			}
			if($screenFlag == 'add'){
			  array_push($newarr1,8,9,10);
			}
			$empstatusstr = implode(",",$newarr1);

		}
		if($empstatusstr !='')
		$statusArr = $employmentstatusmodel->getStatuslist($empstatusstr);
		else
		$statusArr = $employmentstatusmodel->getCompleteStatuslist();
		if(!empty($statusArr))
		{
			$employmentstatusform->workcodename->addMultiOption('','Select Work Code');

			for($i=0;$i<sizeof($statusArr);$i++)
			{
                            if($fromcontroller == 'requisition')
                            {
                                if(!in_array($statusArr[$i]['id'], array(5,7,8,9,10)))
                                    $employmentstatusform->workcodename->addMultiOption($statusArr[$i]['id'],utf8_encode($statusArr[$i]['employemnt_status']));
                            }
                            else 
                            {
                                $employmentstatusform->workcodename->addMultiOption($statusArr[$i]['id'],utf8_encode($statusArr[$i]['employemnt_status']));
                            }
			}
		}
		$this->view->statusArr = $statusArr;
		$this->view->ermsg = '';


		if($this->getRequest()->getPost()){
			if($employmentstatusform->isValid($this->_request->getPost())){
				$id = $this->_request->getParam('id');
				$workcode = $this->_request->getParam('workcode');
				$workcodename = $this->_request->getParam('workcodename');
				$default_leaves = $this->_request->getParam('default_leaves');
				$description = $this->_request->getParam('description');
				$screenFlag = $this->getRequest()->getParam('screenflag');
				
				$date = new Zend_Date();
				$actionflag = '';
				$tableid  = '';
				$data = array('workcode'=>trim($workcode),
				           'workcodename'=>trim($workcodename),
						  'description'=>trim($description),
						  'modifiedby'=>$loginUserId,
							'modifieddate'		=>		gmdate("Y-m-d H:i:s")	
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

				$Id = $employmentstatusmodel->SaveorUpdateEmploymentStatusData($data, $where);
				$tableid = $Id;
				$menuID = EMPLOYMENTSTATUS;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);

				
					
				$this->view->eventact = 'added';
				$close = 'close';
				$this->view->popup=$close;
                                if(isset($_POST['fromcontroller']) && $_POST['fromcontroller'] != '')
                                    $fromcontroller = $_POST['fromcontroller'];
                                if(isset($_POST['boxid']) && $_POST['boxid'] != '')
                                    $boxid = $_POST['boxid'];
			}else
			{
				$messages = $employmentstatusform->getMessages();
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
                
                
                $opt ='';
                if($fromcontroller == '')
                {
					if($screenFlag == 'edit'){
					   $empstatusData = $employmentstatusmodel->getempstatuslist();
					}
					if($screenFlag == 'add'){
					   $empstatusData = $employmentstatusmodel->getempstatusActivelist(); 
					}
                    if(!empty($empstatusData))
                    {
                    foreach($empstatusData as $record){
                          $opt .= sapp_Global::selectOptionBuilder($record['workcodename'], $record['statusname']);
                    }
                    }
                  
                }
                else 
                {
                    if($fromcontroller == 'requisition')
                    {
                        $requi_model = new Default_Model_Requisition();
                        $empstatusData = $requi_model->getStatusOptionsForRequi();                        
                        foreach($empstatusData as $stat_id => $stat_name){
                                $opt .= sapp_Global::selectOptionBuilder($stat_id, $stat_name);
                        }
                    }
                }
                $this->view->empstatusData = $opt;
                $this->view->screenFlag = $screenFlag;
				$this->view->controllername = $controllername;
				$this->view->form = $employmentstatusform;
				$this->view->ermsg = '';
                $this->view->boxid = $boxid;
                $this->view->fromcontroller = $fromcontroller;

	}



}

