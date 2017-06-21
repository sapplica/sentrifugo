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

class Default_HolidaygroupsController extends Zend_Controller_Action
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
		$holidaygroupsmodel = new Default_Model_Holidaygroups();
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

			$sort = 'DESC';$by = 'modifieddate';$pageNo = 1;$searchData = '';
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
			/** search from grid - START **/
			$searchData = $this->_getParam('searchData');
			$searchData = rtrim($searchData,',');
			/** search from grid - END **/
		}
			
		$dataTmp = $holidaygroupsmodel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);
		//print_r($dataTmp);exit;
			
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
	}

	public function holidaydateGrid($groupid)
	{
		$holidaydatesmodel = new Default_Model_Holidaydates();
		$sort = 'DESC';
		//$by = 'h.createddate';
		$by = 'h.holidaydate';
		$perPage = 10;
		$pageNo = 1;
		$searchData = '';
		$searchQuery = '';
		$searchArray=array();
		$objName = 'holidaydates';
		$tableFields = array('action'=>'Action','holidayname' => 'Holiday','holidaydate' => 'Date','description' => 'Description');
		$tablecontent = $holidaydatesmodel->getHolidayDatesData($sort, $by, $pageNo, $perPage,$searchQuery,$groupid);
		$data = array();
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
			'add' =>'add',
			'searchArray' => $searchArray,
			'formgrid' => 'true',
			'menuName'=>'Holiday Dates',
                        'search_filters' => array(
                            'holidaydate' => array('type' => 'datepicker'),
		),
		);
		array_push($data,$dataTmp);
		return $data;
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
			
		$holidaygroupsform = new Default_Form_holidaygroups();
		$holidaygroupsform->setAttrib('action',BASE_URL.'holidaygroups/add');
		$this->view->form = $holidaygroupsform;
		if($this->getRequest()->getPost()){
			$result = $this->save($holidaygroupsform);
			$this->view->msgarray = $result;
		}

	}

	public function viewAction()
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
		$objName = 'holidaygroups';
		$holidaygroupsform = new Default_Form_holidaygroups();
		$holidaygroupsform->removeElement("submit");
		$holidaygroupsmodel = new Default_Model_Holidaygroups();
		$holidaydatesmodel = new Default_Model_Holidaydates();

		$elements = $holidaygroupsform->getElements();
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
					$data = $holidaygroupsmodel->getParticularGroupData($id);
					if(!empty($data))
					{
						$groupdataArr = $holidaydatesmodel->getTotalGroupDataWithId($data[0]['id']);
						if($groupdataArr[0]['count'] > 0)
						{
							$this->view->groupdataCount = $groupdataArr[0]['count'];
							$this->view->dataArray = $this->holidaydateGrid($data[0]['id']);
							$this->view->controllergrid = 'holidaydates';
						}
						$holidaygroupsform->populate($data[0]);
						$permission = sapp_Global::_checkprivileges(HOLIDAYGROUPS,$loginuserGroup,$loginuserRole,'edit');

						$this->view->editpermission = $permission;
						$this->view->controllername = $objName;
						$this->view->id = $id;
						$this->view->data = $data[0];
						$this->view->form = $holidaygroupsform;
					}
					else
					{
						$this->view->rowexist = "norows";
					}
				}
				else
				{
					$this->view->rowexist = "norows";
				}
			}
			else
			{
				$this->view->rowexist = "norows";
			}
		}
		catch(Exception $e){
			$this->view->rowexist = "norows";
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

		$holidaygroupsform = new Default_Form_holidaygroups();
		$holidaygroupsmodel = new Default_Model_Holidaygroups();
		$holidaydatesmodel = new Default_Model_Holidaydates();
		try
		{
			if($id)
			{
				if(is_numeric($id) && $id>0)
				{
					$data = $holidaygroupsmodel->getParticularGroupData($id);
					if(!empty($data))
					{
						$holidaygroupsform->populate($data[0]);
						$holidaygroupsform->submit->setLabel('Update');
						$holidaygroupsform->setAttrib('action',BASE_URL.'holidaygroups/edit/id/'.$id);
						$groupdataArr = $holidaydatesmodel->getTotalGroupDataWithId($data[0]['id']);
						if($groupdataArr[0]['count'] > 0)
						{
							$this->view->groupdataCount = $groupdataArr[0]['count'];
							$this->view->dataArray = $this->holidaydateGrid($data[0]['id']);
							$this->view->controllergrid = 'holidaydates';
						}
						$this->view->form = $holidaygroupsform;
						$this->view->rowexist = "";
					}
					else
					{
						$this->view->rowexist = "rows";
					}
				}
				else
				{
					$this->view->rowexist = "norows";
				}
			}
			else
			{
				$this->view->rowexist = "norows";
			}
		}
		catch(Exception $e){
			$this->view->rowexist = "norows";
		}
		if($this->getRequest()->getPost()){
			$result = $this->save($holidaygroupsform);
			$this->view->msgarray = $result;
		}
	}

	public function save($holidaygroupsform)
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		if($holidaygroupsform->isValid($this->_request->getPost())){
			$id = $this->_request->getParam('id');
			$groupname = $this->_request->getParam('groupname');
			$description = $this->_request->getParam('description');
			$date = new Zend_Date();
			$holidaygroupsmodel = new Default_Model_Holidaygroups();
			$actionflag = '';
			$tableid  = '';
			$data = array( 'groupname'=>$groupname,
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
			$Id = $holidaygroupsmodel->SaveorUpdateGroupData($data, $where);
			if($Id == 'update')
			{
				$tableid = $id;
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Holiday group updated successfully."));
			}
			else
			{
				$tableid = $Id;
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Holiday group added successfully."));
			}
			$menuID = HOLIDAYGROUPS;
			$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
			$this->_redirect('holidaygroups');
		}else
		{
			$messages = $holidaygroupsform->getMessages();
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
		$actionflag = 3;
		if($id)
		{
			$holidaygroupsmodel = new Default_Model_Holidaygroups();
			$holidayedatesmodel = new Default_Model_Holidaydates();
			$data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
			$where = array('id=?'=>$id);
			$Id = $holidaygroupsmodel->SaveorUpdateGroupData($data, $where);
			/* deleteing dates of the group */
			$childdata = array('isactive'=>0);
			$childwhere = array('groupid=?'=>$id);
			$holidayedatesmodel->SaveorUpdateHolidayDates($childdata,$childwhere);
			/* END */
			if($Id == 'update')
			{
				$menuID = HOLIDAYGROUPS;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
				$messages['message'] = 'Holiday group deleted successfully.';
				$messages['msgtype'] = 'success';
			}
			else
			{
				$messages['message'] = 'Holiday group cannot be deleted.';
				$messages['msgtype'] = 'error';
			}
		}
		else
		{
			$messages['message'] = 'Holiday group cannot be deleted.';
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

	public function getempnamesAction()
	{
		$this->_helper->layout->disableLayout();
		$groupid = $this->_request->getparam('groupid');
		$pageno = intval($this->_request->getParam('pageno',1));
		$perpage = intval($this->_request->getParam('perpage',PERPAGE));
		if($perpage == 0)
		$perpage = PERPAGE;
		$holidaygroupsmodel = new Default_Model_Holidaygroups();
		$empdetailsArr = $holidaygroupsmodel->getEmpNamesForGroupId($groupid,$pageno,$perpage);
		$empdetailsCountArr = $holidaygroupsmodel->getEmpCountForGroups($groupid);
		$empdetailsCount = $empdetailsCountArr[0]['empcount'];
		if($empdetailsCount > 0)
		{
			$lastpage =  ceil($empdetailsCount/$perpage);
		}
		else
		{
			$lastpage = '';
			$empdetailsCount = '';
		}
		$this->view->groupid = $groupid;
		$this->view->empdetailsArr = $empdetailsArr;
		$this->view->pageno = $pageno;
		$this->view->perpage = $perpage;
		$this->view->lastpage = $lastpage;

	}

	public function getholidaynamesAction()
	{
		$this->_helper->layout->disableLayout();
		$groupid = $this->_request->getparam('groupid');
		$pageno = intval($this->_request->getParam('pageno',1));
		$perpage = intval($this->_request->getParam('perpage',PERPAGE));
		if($perpage == 0)
		$perpage = PERPAGE;
		$holidaygroupsmodel = new Default_Model_Holidaygroups();
		$holidaydetailsArr = $holidaygroupsmodel->getHolidayNamesForGroupId($groupid,$pageno,$perpage);
		$holidayCountArr = $holidaygroupsmodel->getHolidayCountForGroups($groupid);
		$holidayCount = $holidayCountArr[0]['count'];
		if($holidayCount > 0)
		{
			$lastpage =  ceil($holidayCount/$perpage);
		}
		else
		{
			$lastpage = '';
			$holidayCount = '';
		}
		$this->view->groupid = $groupid;
		$this->view->holidaydetailsArr = $holidaydetailsArr;
		$this->view->pageno = $pageno;
		$this->view->perpage = $perpage;
		$this->view->lastpage = $lastpage;

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
		$controllername = 'holidaygroups';
		$holidaygroupsform = new Default_Form_holidaygroups();
		$holidaygroupsform->setAction(BASE_URL.'holidaygroups/addpopup');
		if($this->getRequest()->getPost()){
			if($holidaygroupsform->isValid($this->_request->getPost())){
				$id = $this->_request->getParam('id');
				$groupname = $this->_request->getParam('groupname');
				$description = $this->_request->getParam('description');
				$date = new Zend_Date();
				$holidaygroupsmodel = new Default_Model_Holidaygroups();
				$actionflag = '';
				$tableid  = '';
				$data = array( 'groupname'=>$groupname,
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
				$Id = $holidaygroupsmodel->SaveorUpdateGroupData($data, $where);
				$tableid = $Id;
					

				$menuID = HOLIDAYGROUPS;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);

				$holidaygroupData = $holidaygroupsmodel->fetchAll('isactive = 1','groupname')->toArray();
				$opt ='';
				foreach($holidaygroupData as $record){
					$opt .= sapp_Global::selectOptionBuilder($record['id'], $record['groupname']);
				}
				$this->view->holidaygroupData = $opt;
					
				$this->view->eventact = 'added';
				$close = 'close';
				$this->view->popup=$close;
			}else
			{
				$messages = $holidaygroupsform->getMessages();
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
		$this->view->form = $holidaygroupsform;
		$this->view->ermsg = '';

	}

}

