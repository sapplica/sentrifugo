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
		$ajaxContext->addActionContext('billingreport', 'html')->initContext();
		$ajaxContext->addActionContext('getempduration', 'html')->initContext();
		$ajaxContext->addActionContext('getprojecttaskduration', 'html')->initContext();
		$ajaxContext->addActionContext('getpdftime', 'html')->initContext();
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

		$month_first_day = date('Y-m-01');
		$month_last_day = date('Y-m-t');
		$month_first_day = sapp_Global::change_date($month_first_day,'database');
		$month_last_day = sapp_Global::change_date($month_last_day,'database');
			
		$start_date = ($this->_getParam('start_date')!='')? $this->_getParam('start_date'):$month_first_day;
		$end_date = ($this->_getParam('end_date')!='')? $this->_getParam('end_date'):$month_last_day;

		$this->view->start_date = ($this->_getParam('start_date')!='')? $this->_getParam('start_date'):$month_first_day;
		$this->view->end_date = ($this->_getParam('end_date')!='')? $this->_getParam('end_date'):$month_last_day;
		$this->view->selected_period_hidden = ($this->_getParam('selected_period_hidden')!='')? $this->_getParam('selected_period_hidden'):'';
			
			
	}

	public function employeereportsAction(){

		$reportsmodel = new Timemanagement_Model_Reports();
		$projid = ($this->_request->getParam('projectId') != "undefined" && $this->_request->getParam('projectId') != "all")?$this->_request->getParam('projectId'):"";
		$month_first_day = date('Y-m-01');
		$month_last_day = date('Y-m-t');
		$month_first_day = sapp_Global::change_date($month_first_day,'database');
		$month_last_day = sapp_Global::change_date($month_last_day,'database');

		$start_date = ($this->_getParam('start_date')!='' && $this->_getParam('start_date')!='undefined')? $this->_getParam('start_date'):$month_first_day;
		$end_date = ($this->_getParam('end_date')!='' && $this->_getParam('end_date')!='undefined')? $this->_getParam('end_date'):date('Y-m-d');
		$start_date = sapp_Global::change_date($start_date,'database');
		$end_date = sapp_Global::change_date($end_date,'database');
		$org_start_date = $start_date;
		$org_end_date = $end_date;
		//pdf and excel flags 
		$is_pdf = ($this->_getParam('is_pdf')!='' && $this->_getParam('is_pdf')!='undefined')? $this->_getParam('is_pdf'):"";
		$is_excel = ($this->_getParam('is_excel')!='' && $this->_getParam('is_excel')!='undefined')? $this->_getParam('is_excel'):"";
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
			$sort = 'DESC'; $by = 'e.userfullname'; $pageNo = 1; $searchData = ''; $searchQuery = '';$searchArray='';
		}
		else
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'e.userfullname';
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
		//if pdf or excel
		if(!empty($is_pdf) || !empty($is_excel))
		{
			//sorting order, sorting column and pagination parameters
			$sort = ($this->_getParam('sort_order') !='')? $this->_getParam('sort_order'):'DESC';
			$by = ($this->_getParam('sort_by')!='')? $this->_getParam('sort_by'):'e.userfullname';
			$perPage = $this->_getParam('per_page',PERPAGE);
			$pageNo = $this->_getParam('page_no', 1);
			$searchData = $this->_getParam('search_data_pdf');
			$searchData = rtrim($searchData,',');
			$searchQuery = '';
			$searchArray = array();
			//build search parameters
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
			$this->_helper->layout->disableLayout();
			//getting the employee reports data
			$result = $reportsmodel->getEmployeeReportsData($sort, $by, $pageNo, $perPage, $searchQuery,$start_date, $end_date, $projid, $param,1);	
			//for pdf
			if(!empty($is_pdf))
			{
				$view = $this->getHelper('ViewRenderer')->view;
	            $this->view->reportsdata = $result;
	            $text = $view->render('reports/reportspdf.phtml');
	            require_once 'application/modules/default/library/MPDF57/mpdf.php';
	            $mpdf=new mPDF('', 'A4', 12, 'Arial', 10, 10, 12, 12, 6, 6);
	            $mpdf->SetDisplayMode('fullpage');
	            $mpdf->list_indent_first_level = 0;
	            $mpdf->SetDisplayMode('fullpage');
	            $mpdf->pagenumSuffix = '';
	            $mpdf->nbpgPrefix = ' of ';
	            $mpdf->nbpgSuffix = '';
	            $mpdf->AddPage();
	            $mpdf->WriteHTML($text);
	            $mpdf->Output('timereport'.'.pdf','D');
			}
			//for excel
			else if(!empty($is_excel))
			{
				$cols_param_arr = array('userfullname' => 'Employee','duration' => 'Hours');
				sapp_Global::export_to_excel($result,$cols_param_arr,"EmployeesReport.xlsx");
			}
            exit;
		}
		else //for report grid
		{
			$projectsdata = $reportsmodel->getEmployeeReportsbyProjectId($sort, $by, $perPage, $pageNo, $searchData,
			$call, $dashboardcall, $start_date, $end_date, $projid,$org_start_date,$org_end_date,$param);
			array_push($data,$projectsdata);
			$this->view->dataArray = $data;
			$this->view->call = $call ;
			$this->view->start_date = ($this->_getParam('start_date')!='')? $this->_getParam('start_date'):$month_first_day;
			$this->view->end_date = ($this->_getParam('end_date')!='')? $this->_getParam('end_date'):$today;
			$this->view->selcetedproj =$projid;
			$this->view->sort =$sort;
			$this->view->by =$by;
			$this->view->selected_period_hidden = ($this->_getParam('selected_period_hidden')!='')? $this->_getParam('selected_period_hidden'):'';
		}
			
	}
	
	public function billingreportAction(){

		$reportsmodel = new Timemanagement_Model_Reports();
    $this->view->reports_model = $reportsmodel;
		$projecttype = ($this->_request->getParam('projecttype') != "undefined" && $this->_request->getParam('projecttype') != "all")?$this->_request->getParam('projecttype'):"";
		$month_first_day = date('Y-m-01');
		$month_last_day = date('Y-m-t');
		$month_first_day = sapp_Global::change_date($month_first_day,'database');
		$month_last_day = sapp_Global::change_date($month_last_day,'database');

		$start_date = ($this->_getParam('start_date')!='' && $this->_getParam('start_date')!='undefined')? $this->_getParam('start_date'):$month_first_day;
		$end_date = ($this->_getParam('end_date')!='' && $this->_getParam('end_date')!='undefined')? $this->_getParam('end_date'):date('Y-m-d');
		$start_date = sapp_Global::change_date($start_date,'database');
		$end_date = sapp_Global::change_date($end_date,'database');
		$org_start_date = $start_date;
		$org_end_date = $end_date;
      
		//pdf and excel flags 
		$is_pdf = ($this->_getParam('is_pdf')!='' && $this->_getParam('is_pdf')!='undefined')? $this->_getParam('is_pdf'):"";
		$is_excel = ($this->_getParam('is_excel')!='' && $this->_getParam('is_excel')!='undefined')? $this->_getParam('is_excel'):"";
		$call = $this->_getParam('call');

    $selected_period_hidden = ($this->_getParam('selected_period_hidden')!='')? $this->_getParam('selected_period_hidden'):'';

    $this->view->start_date = $start_date;
		$this->view->end_date = $end_date;
		$this->view->selected_period_hidden = $selected_period_hidden;						
		$this->view->projecttype = $projecttype;
		$this->view->call = $call ;

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
			$sort = 'ASC'; $by = 'e.userfullname'; $pageNo = 1; $searchData = ''; $searchQuery = '';$searchArray='';
		}
		else
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'ASC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'e.userfullname';
			/** search from grid - START **/
			$searchData = $this->_getParam('searchData');
			$searchData = rtrim($searchData,',');
			/** search from grid - END **/
		}

		$param = $this->_getParam('selected_period_hidden');
		//if pdf or excel
		if(!empty($is_pdf) || !empty($is_excel))
		{
			//sorting order, sorting column and pagination parameters
			$sort = ($this->_getParam('sort_order') !='')? $this->_getParam('sort_order'):'ASC';
			$by = ($this->_getParam('sort_by')!='')? $this->_getParam('sort_by'):'e.userfullname';
			$searchData = $this->_getParam('search_data_pdf');
			$searchData = rtrim($searchData,',');
			$searchQuery = '';
			$searchArray = array();
			//build search parameters
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
			$this->_helper->layout->disableLayout();
		}

		$this->view->sort =$sort;
		$this->view->by =$by;
		
		//getting the employee reports data
		$result = array();
		
		$emp_billing_data = $reportsmodel->getBillingReportEmployeesData($sort, $by, $searchQuery,$start_date, $end_date);	
		
		$index = 1;
    $total_time = 0;
    $total_billing_hours = 0;
    $total_overtime = 0;
    $total_on_call_overtime = 0;
  	$total_leave_days = 0;
  	$partial_leave_days = 0;
  	$total_on_call_days = 0;
		$billable_total = 0;

		foreach($emp_billing_data as $temp_emp_billing_data){		
			$empid = $temp_emp_billing_data['user_id'];
    	$total_emp_time = 0;
    	$total_emp_billing_hours = 0;
    	$total_emp_overtime = 0;
    	$total_emp_on_call_overtime = 0;
    	$hours_status = '';
    	$total_emp_leave_days = 0;
    	$partial_emp_leave_days = 0;
    	$total_emp_on_call_days = 0;
			$billable_emp_total = 0;
			$billable_rate = 0;
			$project_name = '';
			$current_project_type_id = '';
			$current_project_type = '';
		
			$proj_billing_data = $reportsmodel->getBillingProjData($empid, $start_date, $end_date, $projecttype);
			$leave_billing_data = $reportsmodel->getBillingLeaveData($empid, $start_date, $end_date);
			$on_call_billing_data = $reportsmodel->getBillingOnCallData($empid, $start_date, $end_date);
			$work_schedule_hours = $reportsmodel->getWorkScheduleHours($empid);
		
			foreach($proj_billing_data as $temp_hours_task_week){	
    		$totalweektime = 0;
    		$totalweekbillinghours = 0;
          	
    		if ($temp_hours_task_week['sun_date'] >= $start_date && $temp_hours_task_week['sun_date'] <= $end_date) {
       		list($hours,$minutes) = explode(':', $temp_hours_task_week['sun_duration'], 2);
					
       		if ((is_numeric($hours) && $hours <> 0) || (is_numeric($minutes) && $minutes <> 0)) {			
    		    if ($temp_hours_task_week['billable_rate'] != '' && $temp_hours_task_week['billable_rate'] != 0) {
					    $billable_rate = $temp_hours_task_week['billable_rate'];
	  		    }
						
    		    if ($temp_hours_task_week['project_name'] != '') {
					    $project_name = $temp_hours_task_week['project_name'];
					    $current_project_type_id = $temp_hours_task_week['id'];
					    $current_project_type = $temp_hours_task_week['project_type'];
	  		    }
										            
						$day_time = 0;
          	
						if ($hours_status != 'rejected') {
            	$hours_status = $temp_hours_task_week['sun_status'];
          	}
       		
						if (is_numeric($hours) && $hours <> 0) {
							$day_time = $day_time + $hours;
						}
       		
       			if (is_numeric($minutes) && $minutes <> 0) {
    					$day_time = $day_time + ($minutes / 60);
						}
						
						$totalweektime = $totalweektime + $day_time;

            if ($temp_hours_task_week['task'] != 'Overtime' && $temp_hours_task_week['task'] != 'On call overtime') {
              $work_schedule_hours_day = 0;
   
              foreach($work_schedule_hours as $temp_work_schedule_hours){	   
			  		   	if ($temp_work_schedule_hours['startdate'] <= $temp_hours_task_week['sun_date'] &&
						   	    $temp_work_schedule_hours['enddate'] >= $temp_hours_task_week['sun_date'])
                    $work_schedule_hours_day = $temp_work_schedule_hours['sun_duration'];     
              }
						   
						  if ($hours != $work_schedule_hours_day || (is_numeric($minutes) && $minutes <> 0) || $temp_hours_task_week['hours_day'] == 0) {
						   	$totalweekbillinghours = $totalweekbillinghours + $day_time;
       			  } else {
						   	$totalweekbillinghours = $totalweekbillinghours + $temp_hours_task_week['hours_day'];
						  }
					  }
       		}
	  		}				
				            
    		if ($temp_hours_task_week['mon_date'] >= $start_date && $temp_hours_task_week['mon_date'] <= $end_date) {
       		list($hours,$minutes) = explode(':', $temp_hours_task_week['mon_duration'], 2);
					
       		if ((is_numeric($hours) && $hours <> 0) || (is_numeric($minutes) && $minutes <> 0)) {			
    		    if ($temp_hours_task_week['billable_rate'] != '' && $temp_hours_task_week['billable_rate'] != 0) {
					    $billable_rate = $temp_hours_task_week['billable_rate'];
	  		    }
						
    		    if ($temp_hours_task_week['project_name'] != '') {
					    $project_name = $temp_hours_task_week['project_name'];
					    $current_project_type_id = $temp_hours_task_week['id'];
					    $current_project_type = $temp_hours_task_week['project_type'];
	  		    }
										            
						$day_time = 0;
          	
						if ($hours_status != 'rejected') {
            	$hours_status = $temp_hours_task_week['mon_status'];
          	}
       		
						if (is_numeric($hours) && $hours <> 0) {
							$day_time = $day_time + $hours;
						}
       		
       			if (is_numeric($minutes) && $minutes <> 0) {
    					$day_time = $day_time + ($minutes / 60);
						}
						
						$totalweektime = $totalweektime + $day_time;

            if ($temp_hours_task_week['task'] != 'Overtime' && $temp_hours_task_week['task'] != 'On call overtime') {
              $work_schedule_hours_day = 0;
   
              foreach($work_schedule_hours as $temp_work_schedule_hours){	   
			  		   	if ($temp_work_schedule_hours['startdate'] <= $temp_hours_task_week['mon_date'] &&
						   	    $temp_work_schedule_hours['enddate'] >= $temp_hours_task_week['mon_date'])
                    $work_schedule_hours_day = $temp_work_schedule_hours['mon_duration'];     
              }
						   
						  if ($hours != $work_schedule_hours_day || (is_numeric($minutes) && $minutes <> 0) || $temp_hours_task_week['hours_day'] == 0) {
						   	$totalweekbillinghours = $totalweekbillinghours + $day_time;
       			  } else {
						   	$totalweekbillinghours = $totalweekbillinghours + $temp_hours_task_week['hours_day'];
						  }
					  }
       		}
	  		}				            
          	          	            						            	            						
    		if ($temp_hours_task_week['tue_date'] >= $start_date && $temp_hours_task_week['tue_date'] <= $end_date) {
       		list($hours,$minutes) = explode(':', $temp_hours_task_week['tue_duration'], 2);
					
       		if ((is_numeric($hours) && $hours <> 0) || (is_numeric($minutes) && $minutes <> 0)) {			
    		    if ($temp_hours_task_week['billable_rate'] != '' && $temp_hours_task_week['billable_rate'] != 0) {
					    $billable_rate = $temp_hours_task_week['billable_rate'];
	  		    }
						
    		    if ($temp_hours_task_week['project_name'] != '') {
					    $project_name = $temp_hours_task_week['project_name'];
					    $current_project_type_id = $temp_hours_task_week['id'];
					    $current_project_type = $temp_hours_task_week['project_type'];
	  		    }
										            
						$day_time = 0;
          	
						if ($hours_status != 'rejected') {
            	$hours_status = $temp_hours_task_week['tue_status'];
          	}
       		
						if (is_numeric($hours) && $hours <> 0) {
							$day_time = $day_time + $hours;
						}
       		
       			if (is_numeric($minutes) && $minutes <> 0) {
    					$day_time = $day_time + ($minutes / 60);
						}
						
						$totalweektime = $totalweektime + $day_time;

            if ($temp_hours_task_week['task'] != 'Overtime' && $temp_hours_task_week['task'] != 'On call overtime') {
              $work_schedule_hours_day = 0;
   
              foreach($work_schedule_hours as $temp_work_schedule_hours){	   
			  		   	if ($temp_work_schedule_hours['startdate'] <= $temp_hours_task_week['tue_date'] &&
						   	    $temp_work_schedule_hours['enddate'] >= $temp_hours_task_week['tue_date'])
                    $work_schedule_hours_day = $temp_work_schedule_hours['tue_duration'];     
              }
						   
						  if ($hours != $work_schedule_hours_day || (is_numeric($minutes) && $minutes <> 0) || $temp_hours_task_week['hours_day'] == 0) {
						   	$totalweekbillinghours = $totalweekbillinghours + $day_time;
       			  } else {
						   	$totalweekbillinghours = $totalweekbillinghours + $temp_hours_task_week['hours_day'];
						  }
					  }
       		}
	  		}				            
          	          	            						            	            						
    		if ($temp_hours_task_week['wed_date'] >= $start_date && $temp_hours_task_week['wed_date'] <= $end_date) {
       		list($hours,$minutes) = explode(':', $temp_hours_task_week['wed_duration'], 2);
					
       		if ((is_numeric($hours) && $hours <> 0) || (is_numeric($minutes) && $minutes <> 0)) {			
    		    if ($temp_hours_task_week['billable_rate'] != '' && $temp_hours_task_week['billable_rate'] != 0) {
					    $billable_rate = $temp_hours_task_week['billable_rate'];
	  		    }
						
    		    if ($temp_hours_task_week['project_name'] != '') {
					    $project_name = $temp_hours_task_week['project_name'];
					    $current_project_type_id = $temp_hours_task_week['id'];
					    $current_project_type = $temp_hours_task_week['project_type'];
	  		    }
										            
						$day_time = 0;
          	
						if ($hours_status != 'rejected') {
            	$hours_status = $temp_hours_task_week['wed_status'];
          	}
       		
						if (is_numeric($hours) && $hours <> 0) {
							$day_time = $day_time + $hours;
						}
       		
       			if (is_numeric($minutes) && $minutes <> 0) {
    					$day_time = $day_time + ($minutes / 60);
						}
						
						$totalweektime = $totalweektime + $day_time;

            if ($temp_hours_task_week['task'] != 'Overtime' && $temp_hours_task_week['task'] != 'On call overtime') {
              $work_schedule_hours_day = 0;
   
              foreach($work_schedule_hours as $temp_work_schedule_hours){	   
			  		   	if ($temp_work_schedule_hours['startdate'] <= $temp_hours_task_week['wed_date'] &&
						   	    $temp_work_schedule_hours['enddate'] >= $temp_hours_task_week['wed_date'])
                    $work_schedule_hours_day = $temp_work_schedule_hours['wed_duration'];     
              }
						   
						  if ($hours != $work_schedule_hours_day || (is_numeric($minutes) && $minutes <> 0) || $temp_hours_task_week['hours_day'] == 0) {
						   	$totalweekbillinghours = $totalweekbillinghours + $day_time;
       			  } else {
						   	$totalweekbillinghours = $totalweekbillinghours + $temp_hours_task_week['hours_day'];
						  }
					  }
       		}
	  		}				            
          	          	            						            	            						
    		if ($temp_hours_task_week['thu_date'] >= $start_date && $temp_hours_task_week['thu_date'] <= $end_date) {
       		list($hours,$minutes) = explode(':', $temp_hours_task_week['thu_duration'], 2);
					
       		if ((is_numeric($hours) && $hours <> 0) || (is_numeric($minutes) && $minutes <> 0)) {			
    		    if ($temp_hours_task_week['billable_rate'] != '' && $temp_hours_task_week['billable_rate'] != 0) {
					    $billable_rate = $temp_hours_task_week['billable_rate'];
	  		    }
						
    		    if ($temp_hours_task_week['project_name'] != '') {
					    $project_name = $temp_hours_task_week['project_name'];
					    $current_project_type_id = $temp_hours_task_week['id'];
					    $current_project_type = $temp_hours_task_week['project_type'];
	  		    }
										            
						$day_time = 0;
          	
						if ($hours_status != 'rejected') {
            	$hours_status = $temp_hours_task_week['thu_status'];
          	}
       		
						if (is_numeric($hours) && $hours <> 0) {
							$day_time = $day_time + $hours;
						}
       		
       			if (is_numeric($minutes) && $minutes <> 0) {
    					$day_time = $day_time + ($minutes / 60);
						}
						
						$totalweektime = $totalweektime + $day_time;

            if ($temp_hours_task_week['task'] != 'Overtime' && $temp_hours_task_week['task'] != 'On call overtime') {
              $work_schedule_hours_day = 0;
   
              foreach($work_schedule_hours as $temp_work_schedule_hours){	   
			  		   	if ($temp_work_schedule_hours['startdate'] <= $temp_hours_task_week['thu_date'] &&
						   	    $temp_work_schedule_hours['enddate'] >= $temp_hours_task_week['thu_date'])
                    $work_schedule_hours_day = $temp_work_schedule_hours['thu_duration'];     
              }
						   
						  if ($hours != $work_schedule_hours_day || (is_numeric($minutes) && $minutes <> 0) || $temp_hours_task_week['hours_day'] == 0) {
						   	$totalweekbillinghours = $totalweekbillinghours + $day_time;
       			  } else {
						   	$totalweekbillinghours = $totalweekbillinghours + $temp_hours_task_week['hours_day'];
						  }
					  }
       		}
	  		}				            
          	          	            						            	            						
    		if ($temp_hours_task_week['fri_date'] >= $start_date && $temp_hours_task_week['fri_date'] <= $end_date) {
       		list($hours,$minutes) = explode(':', $temp_hours_task_week['fri_duration'], 2);
					
       		if ((is_numeric($hours) && $hours <> 0) || (is_numeric($minutes) && $minutes <> 0)) {			
    		    if ($temp_hours_task_week['billable_rate'] != '' && $temp_hours_task_week['billable_rate'] != 0) {
					    $billable_rate = $temp_hours_task_week['billable_rate'];
	  		    }
						
    		    if ($temp_hours_task_week['project_name'] != '') {
					    $project_name = $temp_hours_task_week['project_name'];
					    $current_project_type_id = $temp_hours_task_week['id'];
					    $current_project_type = $temp_hours_task_week['project_type'];
	  		    }
										            
						$day_time = 0;
          	
						if ($hours_status != 'rejected') {
            	$hours_status = $temp_hours_task_week['fri_status'];
          	}
       		
						if (is_numeric($hours) && $hours <> 0) {
							$day_time = $day_time + $hours;
						}
       		
       			if (is_numeric($minutes) && $minutes <> 0) {
    					$day_time = $day_time + ($minutes / 60);
						}
						
						$totalweektime = $totalweektime + $day_time;

            if ($temp_hours_task_week['task'] != 'Overtime' && $temp_hours_task_week['task'] != 'On call overtime') {
              $work_schedule_hours_day = 0;
   
              foreach($work_schedule_hours as $temp_work_schedule_hours){	   
			  		   	if ($temp_work_schedule_hours['startdate'] <= $temp_hours_task_week['fri_date'] &&
						   	    $temp_work_schedule_hours['enddate'] >= $temp_hours_task_week['fri_date'])
                    $work_schedule_hours_day = $temp_work_schedule_hours['fri_duration'];     
              }
						   
						  if ($hours != $work_schedule_hours_day || (is_numeric($minutes) && $minutes <> 0) || $temp_hours_task_week['hours_day'] == 0) {
						   	$totalweekbillinghours = $totalweekbillinghours + $day_time;
       			  } else {
						   	$totalweekbillinghours = $totalweekbillinghours + $temp_hours_task_week['hours_day'];
						  }
					  }
       		}
	  		}				            
          	          	            						            	            						
    		if ($temp_hours_task_week['sat_date'] >= $start_date && $temp_hours_task_week['sat_date'] <= $end_date) {
       		list($hours,$minutes) = explode(':', $temp_hours_task_week['sat_duration'], 2);
					
       		if ((is_numeric($hours) && $hours <> 0) || (is_numeric($minutes) && $minutes <> 0)) {			
    		    if ($temp_hours_task_week['billable_rate'] != '' && $temp_hours_task_week['billable_rate'] != 0) {
					    $billable_rate = $temp_hours_task_week['billable_rate'];
	  		    }
						
    		    if ($temp_hours_task_week['project_name'] != '') {
					    $project_name = $temp_hours_task_week['project_name'];
					    $current_project_type_id = $temp_hours_task_week['id'];
					    $current_project_type = $temp_hours_task_week['project_type'];
	  		    }
										            
						$day_time = 0;
          	
						if ($hours_status != 'rejected') {
            	$hours_status = $temp_hours_task_week['sat_status'];
          	}
       		
						if (is_numeric($hours) && $hours <> 0) {
							$day_time = $day_time + $hours;
						}
       		
       			if (is_numeric($minutes) && $minutes <> 0) {
    					$day_time = $day_time + ($minutes / 60);
						}
						
						$totalweektime = $totalweektime + $day_time;

            if ($temp_hours_task_week['task'] != 'Overtime' && $temp_hours_task_week['task'] != 'On call overtime') {
              $work_schedule_hours_day = 0;
   
              foreach($work_schedule_hours as $temp_work_schedule_hours){	   
			  		   	if ($temp_work_schedule_hours['startdate'] <= $temp_hours_task_week['sat_date'] &&
						   	    $temp_work_schedule_hours['enddate'] >= $temp_hours_task_week['sat_date'])
                    $work_schedule_hours_day = $temp_work_schedule_hours['sat_duration'];     
              }
						   
						  if ($hours != $work_schedule_hours_day || (is_numeric($minutes) && $minutes <> 0) || $temp_hours_task_week['hours_day'] == 0) {
						   	$totalweekbillinghours = $totalweekbillinghours + $day_time;
       			  } else {
						   	$totalweekbillinghours = $totalweekbillinghours + $temp_hours_task_week['hours_day'];
						  }
					  }
       		}
	  		}				            
          	          	            						            	            						
    		if ($temp_hours_task_week['task'] == 'Overtime') {
       		$total_emp_overtime = $total_emp_overtime + $totalweektime;
    		}
    		else if ($temp_hours_task_week['task'] == 'On call overtime') {
       		$total_emp_on_call_overtime = $total_emp_on_call_overtime + $totalweektime;
    		}
    		else {
       		$total_emp_time = $total_emp_time + $totalweektime;
					$total_emp_billing_hours = $total_emp_billing_hours + $totalweekbillinghours;
    		}
				
				$billable_emp_total = $billable_emp_total + ($totalweekbillinghours * $billable_rate);
			}				
	
    	foreach ($leave_billing_data as $temp_leave) {
				if ($temp_leave['leaveday'] == '1') {
    			$total_emp_leave_days = $total_emp_leave_days + $temp_leave['appliedleavescount'];
    		} else {
    			$partial_emp_leave_days = $partial_emp_leave_days + $temp_leave['appliedleavescount'];
    		}	
  		}	
	
    	foreach ($on_call_billing_data as $temp_oncall) {
  			$total_emp_on_call_days = $total_emp_on_call_days + $temp_oncall['appliedoncallscount'];
  		}	

      if ($projecttype == "" || $projecttype ==	$current_project_type_id)
			{
			  $result[$index]['Full Name'] = iconv("UTF-8", "UTF-8//IGNORE", $temp_emp_billing_data['userfullname']); 
    	  $result[$index]['Enterprise ID'] = iconv("UTF-8", "UTF-8//IGNORE", $temp_emp_billing_data['office_faxnumber']); 
    	  $result[$index]['Business Unit'] = iconv("UTF-8", "UTF-8//IGNORE", $temp_emp_billing_data['businessunit_name']); 
			  $result[$index]['Project Name'] = iconv("UTF-8", "UTF-8//IGNORE", $project_name);
			  $result[$index]['Project Type'] = iconv("UTF-8", "UTF-8//IGNORE", $current_project_type);
        $result[$index]['Project Hours'] = round($total_emp_time, 2);
	  	  $result[$index]['Status'] = $hours_status;
	  	  $result[$index]['Overtime Hours'] = round($total_emp_overtime, 2);
	  	  $result[$index]['On Call Overtime Hours'] = round($total_emp_on_call_overtime, 2);
	  	  $result[$index]['Full Day On Call Total'] = round($total_emp_on_call_days, 2);
	  	  $result[$index]['Full Day Leaves Total'] = round($total_emp_leave_days, 2);
	  	  $result[$index]['Partial Day Leaves Total'] = round($partial_emp_leave_days, 2);
	  	  $result[$index]['Billable Hours'] = round($total_emp_billing_hours, 2);
	  	  $result[$index]['Billable Rate'] = $billable_rate;
				
				$billable_emp_total = round(($total_emp_billing_hours * $billable_rate), 2);
				
	  	  $result[$index]['Billable Total'] = round($billable_emp_total, 2);
        
			  $total_time = $total_time + $total_emp_time;
        $total_overtime = $total_overtime + $total_emp_overtime;
        $total_on_call_overtime = $total_on_call_overtime + $total_emp_on_call_overtime;
    	  $total_on_call_days = $total_on_call_days + $total_emp_on_call_days;
  	    $total_leave_days = $total_leave_days + $total_emp_leave_days;
    	  $partial_leave_days = $partial_leave_days + $partial_emp_leave_days;
        $total_billing_hours = $total_billing_hours + $total_emp_billing_hours;
			  $billable_total = $billable_total + $billable_emp_total;				
			}
			
			$index++;
		}
		
  	$result[$index]['Full Name'] = 'Total';
  	$result[$index]['Enterprise ID'] = '';
  	$result[$index]['Business Unit'] = '';
		$result[$index]['Project Name'] = '';
		$result[$index]['Project Type'] = '';
    $result[$index]['Project Hours'] = round($total_time, 2);
	  $result[$index]['Status'] = '';
  	$result[$index]['Overtime Hours'] = round($total_overtime, 2);
  	$result[$index]['On Call Overtime Hours'] = round($total_on_call_overtime, 2);
  	$result[$index]['Full Day On Call Total'] = round($total_on_call_days, 2);
  	$result[$index]['Full Day Leaves Total'] = round($total_leave_days, 2);
  	$result[$index]['Partial Day Leaves Total'] = round($partial_leave_days, 2);
  	$result[$index]['Billable Hours'] = round($total_billing_hours, 2);
	  $result[$index]['Billable Rate'] = '';
  	$result[$index]['Billable Total'] = number_format(round($billable_total, 2), 2, '.', '');
			
		//for pdf
		if(!empty($is_pdf))
		{
			$view = $this->getHelper('ViewRenderer')->view;
	    $this->view->reportsdata = $result;
	    $text = $view->render('reports/billingreportpdf.phtml');
      require_once 'application/modules/default/library/MPDF57/mpdf.php';
      $mpdf=new mPDF('', 'A4', 12, 'Arial', 10, 10, 12, 12, 6, 6);
      $mpdf->SetDisplayMode('fullpage');
      $mpdf->list_indent_first_level = 0;
      $mpdf->SetDisplayMode('fullpage');
      $mpdf->pagenumSuffix = '';
      $mpdf->nbpgPrefix = ' of ';
      $mpdf->nbpgSuffix = '';
      $mpdf->AddPage('L');
      $mpdf->WriteHTML($text);
      $mpdf->Output('Billing employee report - '.$start_date.' to '.$end_date.'.pdf','D');
		}
		//for excel
		else if(!empty($is_excel))
		{
			$cols_param_arr = array('Full Name' => 'Name',
															'Enterprise ID' => 'Enterprise ID',
															'Business Unit' => 'Business Unit',
														  'Project Name' => 'Project Name',
	                            'Project Type' => 'Project Type',
                              'Project Hours' => 'Project Hours',
	                            'Status' => 'Status',
	                            'Overtime Hours' => 'Overtime Hours',
	                            'On Call Overtime Hours' => 'On Call Overtime Hours',
	                            'Full Day On Call Total' => 'Full Day On Call Total',
	                            'Full Day Leaves Total' => 'Full Day Leaves Total',
	                            'Partial Day Leaves Total' => 'Partial Day Leaves Total',
		  												'Billable Hours' => 'Billable Hours',
	                            'Billable Rate' => 'Billable Rate',
	                            'Billable Total' => 'Billable Total');
			sapp_Global::export_to_excel($result,$cols_param_arr,"Billing employee report - ".$start_date." to ".$end_date.".xlsx");
		}
		else //for report grid
		{
			$this->view->dataArray = $result;
		}
			
	}

	public function projectsreportsAction(){

		$reportsmodel = new Timemanagement_Model_Reports();
		$empid = ($this->_request->getParam('emp_id') != "undefined" && $this->_request->getParam('emp_id') != "all") ?$this->_request->getParam('emp_id'):"";
		$month_first_day = date('Y-m-01');
		$month_last_day = date('Y-m-t');
		$month_first_day = sapp_Global::change_date($month_first_day,'database');
		$month_last_day = sapp_Global::change_date($month_last_day,'database');

		$start_date = ($this->_getParam('start_date')!='' && $this->_getParam('start_date')!='undefined')? $this->_getParam('start_date'):$month_first_day;
		$end_date = ($this->_getParam('end_date')!='' && $this->_getParam('end_date')!='undefined')? $this->_getParam('end_date'):date('Y-m-d');
		$start_date = sapp_Global::change_date($start_date,'database');
		$end_date = sapp_Global::change_date($end_date,'database');
		$org_start_date = $start_date;
		$org_end_date = $end_date;
		//pdf and excel flags 
		$is_pdf = ($this->_getParam('is_pdf')!='' && $this->_getParam('is_pdf')!='undefined')? $this->_getParam('is_pdf'):"";
		$is_excel = ($this->_getParam('is_excel')!='' && $this->_getParam('is_excel')!='undefined')? $this->_getParam('is_excel'):"";
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
			$sort = 'DESC'; $by = 'p.project_name'; $pageNo = 1; $searchData = ''; $searchQuery = '';$searchArray='';
		}
		else
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'p.project_name';
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
		//if pdf or excel
		if(!empty($is_pdf) || !empty($is_excel))
		{
			$this->_helper->layout->disableLayout();
			//sorting order, sorting column and pagination parameters
			$sort = ($this->_getParam('sort_order') !='')? $this->_getParam('sort_order'):'DESC';
			$by = ($this->_getParam('sort_by')!='')? $this->_getParam('sort_by'):'p.project_name';
			$perPage = $this->_getParam('per_page',PERPAGE);
			$pageNo = $this->_getParam('page_no', 1);
			$searchData = $this->_getParam('search_data_pdf');
			$searchData = rtrim($searchData,',');
			$searchQuery = '';
			$searchArray = array();
			//build search parameters
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
			//getting the employee reports data
			$result = $reportsmodel->getProjectReportsData($sort, $by, $pageNo, $perPage, $searchQuery, $start_date, $end_date, $empid, $param,1);
			//for pdf
			if(!empty($is_pdf))
			{
				$view = $this->getHelper('ViewRenderer')->view;
	            $this->view->reportsdata = $result;
	            $this->view->flag = 1;
	            $text = $view->render('reports/reportspdf.phtml');
	            require_once 'application/modules/default/library/MPDF57/mpdf.php';
	            $mpdf=new mPDF('', 'A4', 14, '', 10, 10, 12, 12, 6, 6);
	            $mpdf->SetDisplayMode('fullpage');
	            $mpdf->list_indent_first_level = 0;
	            $mpdf->SetDisplayMode('fullpage');
	            $mpdf->pagenumSuffix = '';
	            $mpdf->nbpgPrefix = ' of ';
	            $mpdf->nbpgSuffix = '';
	            $mpdf->AddPage();
	            $mpdf->WriteHTML($text);
	            $mpdf->Output('timereport'.'.pdf','D');
	        }
	        elseif (!empty($is_excel)) //for excel
	        {
				$cols_param_arr = array('project_name' => 'Project','project_type' => 'Project Type','duration'=>'Duration');
				sapp_Global::export_to_excel($result,$cols_param_arr,"ProjectsReport.xlsx");	        	
	        }
            exit;
		}
		else //for reports grid
		{		
			$employeedata = $reportsmodel->getProjectReportsbyEmployeeId($sort, $by, $perPage, $pageNo, $searchData,
			$call, $dashboardcall, $start_date, $end_date, $empid,$org_start_date,$org_end_date,$param);

			array_push($data,$employeedata);
			$this->view->dataArray = $data;
			$this->view->call = $call ;
			$this->view->start_date = ($this->_getParam('start_date')!='')? $this->_getParam('start_date'):$month_first_day;
			$this->view->end_date = ($this->_getParam('end_date')!='')? $this->_getParam('end_date'):$today;
			//$this->view->count=$paginator->getTotalItemCount();
			$this->view->selcetedemp =$empid;
			$this->view->selected_period_hidden = ($this->_getParam('selected_period_hidden')!='')? $this->_getParam('selected_period_hidden'):'';
		}

	}

	public function getempdurationAction()
	{
		$month_first_day = date('Y-m-01');
		$month_first_day = sapp_Global::change_date($month_first_day,'database');

		$start_date = ($this->_getParam('start_date')!='' && $this->_getParam('start_date')!='undefined')? $this->_getParam('start_date'):$month_first_day;
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
		$month_first_day = date('Y-m-01');
		$month_first_day = sapp_Global::change_date($month_first_day,'database');

		$start_date = ($this->_getParam('start_date')!='' && $this->_getParam('start_date')!='undefined')? $this->_getParam('start_date'):$month_first_day;
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

