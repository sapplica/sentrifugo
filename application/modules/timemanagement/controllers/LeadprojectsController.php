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
class Timemanagement_LeadprojectsController extends Zend_Controller_Action
{
	private $options;
	/**
	 * Init.
	 *
	 * @see Zend_Controller_Action::init()
	 */
	public function init()
	{
		$this->_options = $this->getInvokeArg('bootstrap')->getOptions();
	}
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
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
	}
	//function to display employee projects
	public function indexAction()
	{
		$auth = Zend_Auth::getInstance();
		$loginUserId=0;
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$projectModel = new Timemanagement_Model_Projects();
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
			$sort = 'DESC';$by = 'p.modified';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'p.modified';
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

		$role = Zend_Registry::get( 'tm_role' );
		$dataTmp = $projectModel->getEmpGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall,$loginUserId,$role);
			
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
	}
	public function viewAction()
	{
		$auth = Zend_Auth::getInstance();
		$loginUserId=0;
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}

		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();
		$objName = 'employeeprojects';

		$projectModel = new Timemanagement_Model_Projects();
		$projectTaskModel = new Timemanagement_Model_Projecttasks();
		$projectResourcesModel = new Timemanagement_Model_Projectresources();
		try
		{
			if(is_numeric($id) && $id>0)
			{
				$data = $projectModel->getSingleProjectData($id);
				if(!empty($data) && $data != "norows")
				{
					$data_arr = array();
					$call = $this->_getParam('call');
					if($call == 'ajaxcall')
					$this->_helper->layout->disableLayout();
					$view = Zend_Layout::getMvcInstance()->getView();
					$objname = $this->_getParam('objname');
					$refresh = $this->_getParam('refresh');
					$dashboardcall = $this->_getParam('dashboardcall');

					//$data = array();
					$searchQuery = '';
					$searchArray = array();
					$tablecontent='';

					if($refresh == 'refresh')
					{
						$sort = 'DESC';$by = 'modified';$perPage = DASHBOARD_PERPAGE;$pageNo = 1;$searchData = '';
					}
					else
					{
						$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
						$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'tpe.modified';
						$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
						$pageNo = $this->_getParam('page', 1);
						$searchData = $this->_getParam('searchData');
					}

					$role = Zend_Registry::get( 'tm_role' );
					$dataTmp = $projectTaskModel->getEmpTaskGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall,$id,$loginUserId,$role);
					$dataTmp['emptyRoles'] = '';
					$dataTmp['objectname'] = 'leadprojects';
					$dataTmp['dataemptyFlag'] = '';
					$dataTmp['menuName'] = 'Tasks';
					$dataTmp['dashboardcall'] = 'Yes';
					$dataTmp['projectId'] = $id;
					array_push($data_arr,$dataTmp);
						
					$by = 'modified';
					$projectResourcesModel = new Timemanagement_Model_Projectresources();
					$dataResourceTmp = $projectResourcesModel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall,$id,$loginUserId,$role);
					$dataResourceTmp['emptyRoles'] = '';
					$dataResourceTmp['objectname'] = 'projectresources';
					$dataResourceTmp['dataemptyFlag'] = '';
					$dataResourceTmp['menuName'] = 'Resources';
					$dataResourceTmp['dashboardcall'] = 'Yes';
					$dataResourceTmp['projectId'] = $id;
					array_push($data_arr,$dataResourceTmp);
						
					$this->view->data_arr = $data_arr;
					$this->view->controllername = $objName;
					$this->view->data = $data;
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
				$this->view->ermsg = 'nodata';
			}
		}
		catch(Exception $e)
		{
			$this->view->ermsg = 'nodata';
		}
	}

	public function emptasksgridAction(){
		$auth = Zend_Auth::getInstance();
		$loginUserId=0;
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$projectTaskModel = new Timemanagement_Model_Projecttasks();
		$call = $this->_getParam('call');
		if($call == 'ajaxcall')
		$this->_helper->layout->disableLayout();
		$view = Zend_Layout::getMvcInstance()->getView();
		$objname = $this->_getParam('objname');
		$refresh = $this->_getParam('refresh');
		$dashboardcall = $this->_getParam('dashboardcall');
		$projectId = $this->_getParam('projectId');

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
			$sort = 'DESC';$by = 'tpe.modified';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'tpe.modified';
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

		$role = Zend_Registry::get( 'tm_role' );
		$dataTmp = $projectTaskModel->getEmpTaskGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall,$projectId,$loginUserId,$role);
		$dataTmp['emptyRoles'] = '';
		$dataTmp['objectname'] = 'leadprojects';
		$dataTmp['dataemptyFlag'] = '';
		$dataTmp['menuName'] = 'Tasks';
		$dataTmp['dashboardcall'] = 'Yes';
		$dataTmp['projectId'] = $projectId;
			
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
	}
}
