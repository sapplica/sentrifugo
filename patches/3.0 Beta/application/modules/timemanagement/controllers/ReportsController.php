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

class Timemanagement_ReportsController extends Zend_Controller_Action
{
	private $options;
	public function preDispatch()
	{
		/*$userModel = new Timemanagement_Model_Users();
		$checkTmEnable = $userModel->checkTmEnable();

		if(!$checkTmEnable){
			$this->_redirect('error');
		}*/
		
		//check Time management module enable
		// if(!sapp_Helper::checkTmEnable())
			// $this->_redirect('error');
		
		$auth = Zend_Auth::getInstance();
		$loginuserGroup=0;
		$loginuserRole=0;
		 	if($auth->hasIdentity()){
		 		$loginuserGroup = $auth->getStorage()->read()->group_id;
		 		$loginuserRole = $auth->getStorage()->read()->emprole;
		 	}
		if(!($loginuserGroup==3 || ($loginuserGroup=='' && $loginuserRole==1) || $loginuserGroup==1))	
			$this->_redirect('error');
		
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('employeereports', 'html')->initContext();
		$ajaxContext->addActionContext('projectsreports', 'html')->initContext();
		$ajaxContext->addActionContext('getempduration', 'html')->initContext();
		$ajaxContext->addActionContext('getprojecttaskduration', 'html')->initContext();
	}

	public function init()
	{
		$this->_options= $this->getInvokeArg('bootstrap')->getOptions();


	}
	/**
	 * default action
	 */
	public function tmreportAction()
	{
		$reportsmodel = new Timemanagement_Model_Reports();
		$this->view->reports_model = $reportsmodel;

		$year_first_day = '01-01-'.date('Y');
		$today = date('d-m-Y');
			
		$start_date = ($this->_getParam('start_date')!='')? $this->_getParam('start_date'):$year_first_day;
		$end_date = ($this->_getParam('end_date')!='')? $this->_getParam('end_date'):$today;

		// if($start_date != '')
		// $start_date = $start_date.' 00:00:00';
		// if($end_date != '')
		// $end_date = $end_date.' 23:59:59';
			
		$this->view->start_date = ($this->_getParam('start_date')!='')? $this->_getParam('start_date'):$year_first_day;
		$this->view->end_date = ($this->_getParam('end_date')!='')? $this->_getParam('end_date'):$today;
		$this->view->selected_period_hidden = ($this->_getParam('selected_period_hidden')!='')? $this->_getParam('selected_period_hidden'):'';
			
			
	}

	public function employeereportsAction(){

		$reportsmodel = new Timemanagement_Model_Reports();
		$projid = ($this->_request->getParam('projectId') != "undefined" && $this->_request->getParam('projectId') != "all")?$this->_request->getParam('projectId'):"";

		$year_first_day = '01-01-'.date('Y');
		$today = date('m-d-Y');

		$start_date = ($this->_getParam('start_date')!='' && $this->_getParam('start_date')!='undefined')? $this->_getParam('start_date'):$year_first_day;
		$end_date = ($this->_getParam('end_date')!='' && $this->_getParam('end_date')!='undefined')? $this->_getParam('end_date'):date('Y-m-d');
		
		$start_date = sapp_Global::change_date($start_date,'database');
		$end_date = sapp_Global::change_date($end_date,'database');

		$org_start_date = $start_date;
		$org_end_date = $end_date;

		// if($start_date != '')
		// $start_date = $start_date;
		// if($end_date != '')
		// $end_date = $end_date;

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
			$sort = 'DESC'; $by = 'et.modified'; $pageNo = 1; $searchData = ''; $searchQuery = '';$searchArray='';
		}
		else
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'et.modified';
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
		$param = $this->_getParam('selected_period_hidden');
		$projectsdata = $reportsmodel->getEmployeeReportsbyProjectId($sort, $by, $perPage, $pageNo, $searchData,
		$call, $dashboardcall, $start_date, $end_date, $projid,$org_start_date,$org_end_date,$param);

		array_push($data,$projectsdata);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->start_date = ($this->_getParam('start_date')!='')? $this->_getParam('start_date'):$year_first_day;
		$this->view->end_date = ($this->_getParam('end_date')!='')? $this->_getParam('end_date'):$today;
		//$this->view->count=$paginator->getTotalItemCount();
		$this->view->selcetedproj =$projid;
		$this->view->selected_period_hidden = ($this->_getParam('selected_period_hidden')!='')? $this->_getParam('selected_period_hidden'):'';
			
	}

	public function projectsreportsAction(){

		$reportsmodel = new Timemanagement_Model_Reports();
		$empid = ($this->_request->getParam('emp_id') != "undefined" && $this->_request->getParam('emp_id') != "all") ?$this->_request->getParam('emp_id'):"";
		$year_first_day = '01-01-'.date('Y');
		$today = date('m-d-Y');

		$start_date = ($this->_getParam('start_date')!='' && $this->_getParam('start_date')!='undefined')? $this->_getParam('start_date'):$year_first_day;
		$end_date = ($this->_getParam('end_date')!='' && $this->_getParam('end_date')!='undefined')? $this->_getParam('end_date'):date('Y-m-d');
		
		$start_date = sapp_Global::change_date($start_date,'database');
		$end_date = sapp_Global::change_date($end_date,'database');
		
		$org_start_date = $start_date;
		$org_end_date = $end_date;

		// if($start_date != '')
		// $start_date = $start_date.' 00:00:00';
		// if($end_date != '')
		// $end_date = $end_date.' 23:59:59';

		$call = $this->_getParam('call');

		if($call == 'ajaxcall'){
			$this->_helper->layout->disableLayout();
		}
			
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
			$sort = 'DESC'; $by = 'et.modified'; $pageNo = 1; $searchData = ''; $searchQuery = '';$searchArray='';
		}
		else
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'et.modified';
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
		$param = $this->_getParam('selected_period_hidden');
		$employeedata = $reportsmodel->getProjectReportsbyEmployeeId($sort, $by, $perPage, $pageNo, $searchData,
		$call, $dashboardcall, $start_date, $end_date, $empid,$org_start_date,$org_end_date,$param);

		//print_r($employeedata);
		//exit();
		array_push($data,$employeedata);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->start_date = ($this->_getParam('start_date')!='')? $this->_getParam('start_date'):$year_first_day;
		$this->view->end_date = ($this->_getParam('end_date')!='')? $this->_getParam('end_date'):$today;
		//$this->view->count=$paginator->getTotalItemCount();
		$this->view->selcetedemp =$empid;
		$this->view->selected_period_hidden = ($this->_getParam('selected_period_hidden')!='')? $this->_getParam('selected_period_hidden'):'';

	}
	public function getempdurationAction()
	{
		$year_first_day = '01-01-'.date('Y');

		$start_date = ($this->_getParam('start_date')!='' && $this->_getParam('start_date')!='undefined')? $this->_getParam('start_date'):$year_first_day;
		$end_date = ($this->_getParam('end_date')!='' && $this->_getParam('end_date')!='undefined')? $this->_getParam('end_date'):date('Y-m-d');
		$projectId = $this->_getParam('projectId');
		$param = $this->_getParam('params');
		$params = $param;
		if($param=="Last7days")
		{
			$params = "Last 7 days";
		}else if($param=="Monthtodate")
		{
			$params = "Month to date";
		}else if($param=="Yeartodate")
		{
			$params = "Year to date";
		}else if($param=="PreviousMonth")
		{
			$params = "Previous Month";
		}
		$start_date = sapp_Global::change_date($start_date,'database');
		$end_date = sapp_Global::change_date($end_date,'database');
		// if($start_date != '')
		// $start_date = $start_date.' 00:00:00';
		// if($end_date != '')
		// $end_date = $end_date.' 23:59:59';
		$reportsmodel = new Timemanagement_Model_Reports();
		$emp_id = $this->_getParam('empId');
		$get_emp_proj_duration = $reportsmodel->getEmpProjDuration($emp_id,$start_date,$end_date,$projectId,$params);
		$this->view->get_emp_proj_duration = $get_emp_proj_duration;
	}
	public function getprojecttaskdurationAction()
	{
		$year_first_day = '01-01-'.date('Y');

		$start_date = ($this->_getParam('start_date')!='' && $this->_getParam('start_date')!='undefined')? $this->_getParam('start_date'):$year_first_day;
		$end_date = ($this->_getParam('end_date')!='' && $this->_getParam('end_date')!='undefined')? $this->_getParam('end_date'):date('Y-m-d');
		$projectId = $this->_getParam('projectId');
		
		$param = $this->_getParam('params');
		$params = $param;
		if($param=="Last7days")
		{
			$params = "Last 7 days";
		}else if($param=="Monthtodate")
		{
			$params = "Month to date";
		}else if($param=="Yeartodate")
		{
			$params = "Year to date";
		}else if($param=="PreviousMonth")
		{
			$params = "Previous Month";
		}
		$start_date = sapp_Global::change_date($start_date,'database');
		$end_date = sapp_Global::change_date($end_date,'database');

		// if($start_date != '')
		// $start_date = $start_date.' 00:00:00';
		// if($end_date != '')
		// $end_date = $end_date.' 23:59:59';
		$reportsmodel = new Timemanagement_Model_Reports();
		$emp_id = $this->_getParam('empId');
		$get_proj_task_duration = $reportsmodel->getProjTaskDuration($emp_id,$start_date,$end_date,$projectId,$params);
		$this->view->get_proj_task_duration = $get_proj_task_duration;
	}

}

