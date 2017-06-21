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
/**
 * @Name   Employee Timesheets Controller
 *
 * @description
 *
 * This controller contain actions related to Employee Timesheets.
 *
 *
 * @author sagarsoft
 * @version 1.0
 *
 */
class Timemanagement_EmptimesheetsController extends Zend_Controller_Action
{
	private $options;

	/**
	 * The default action - show the home page
	 */
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
		$ajaxContext->addActionContext('displayweeks', 'html')->initContext();
		$ajaxContext->addActionContext('empdisplayweeks', 'html')->initContext();
		$ajaxContext->addActionContext('accordion', 'html')->initContext();
		$ajaxContext->addActionContext('emptimesheetmonthly', 'html')->initContext();
		$ajaxContext->addActionContext('emptimesheetweekly', 'html')->initContext();
		$ajaxContext->addActionContext('enabletimesheet', 'json')->initContext();
		$ajaxContext->addActionContext('approvetimesheet', 'json')->initContext();
		$ajaxContext->addActionContext('rejecttimesheet', 'json')->initContext();
		$ajaxContext->addActionContext('approvedaytimesheet', 'json')->initContext();
		$ajaxContext->addActionContext('rejectdaytimesheet', 'json')->initContext();
		$ajaxContext->addActionContext('getweekstartenddates', 'json')->initContext();
	}

	public function init()
	{
		$this->_options= $this->getInvokeArg('bootstrap')->getOptions();

	}

	/**
	 * This method will display all the client details in grid format.
	 */
	public function indexAction()
	{
		$storage = new Zend_Auth_Storage_Session();
		$data = $storage->read();
		$empTimesheets_model=new Timemanagement_Model_Emptimesheets();
		$min_year=$empTimesheets_model->getMinYear();
		$date1 = new DateTime(date('Y-m-01'));
		$startday=$date1->format('Y-m-d');
		$endday=date('Y-m')."-".cal_days_in_month(CAL_GREGORIAN, $date1->format('m'), $date1->format('Y')); //ending date of month
		$this->view->tm_role = Zend_Registry::get('tm_role');
		$this->view->data=$data;
		$this->view->startday_m=$startday;
		$this->view->endday_m=$endday;
		$this->view->min_year=$min_year;

	}

	/**
	 * This action will display weeks for the month in employee screen.
	 */

	public function empdisplayweeksAction(){

		$selmn=$this->_getParam('selmn');
		$hidweek=$this->_getParam('hidweek',null);
		$manager_id=$this->_getParam('manager_id');
		$selDay = "";
		$user_id = "";
		if($this->_getParam('user_id')){
			$user_id = $this->_getParam('user_id');
		}
		if($this->_getParam('day')){
			$selDay = $this->_getParam('day');
		}
		$empTimesheets_model=new Timemanagement_Model_Emptimesheets();
		$master=$empTimesheets_model->monthly_master($selmn);
		$k=0;
		$weeks=array();
		$pre_week='';
		for($i=1;$i<=6;$i++)
		{
			for($j=1;$j<=7;$j++)
			{
				if($master[$k]!=0)
				{
					$weeks[$i][]=$selmn."-".str_pad($master[$k], 2, '0', STR_PAD_LEFT);
					if(date('d')==$master[$k])
					$pre_week=$i;
				}
				$k++;
			}
		}
		if($selmn==date('Y-m') && $hidweek=='')
		$hidweek=$pre_week;
		if($hidweek=='' && $selmn!=date('Y-m'))
		$hidweek=1;

		$this->view->weeks=$weeks;
		$this->view->hidweek=$hidweek;
		$this->view->manager_id=$manager_id;
		$this->view->selmn=$selmn;
		$this->view->pre_week=$pre_week;
		$this->view->selDay=$selDay;
		$this->view->user_id=$user_id;
	}

	/**
	 * This action will display weeks for the month.
	 */
	public function displayweeksAction()
	{
		$selmn=$this->_getParam('selmn');
		$hidweek=$this->_getParam('hidweek',null);
		$manager_id=$this->_getParam('manager_id');
		$search=$this->_getParam('search');
		$clicked_status=$this->_getParam('clicked_status');
		$empTimesheets_model=new Timemanagement_Model_Emptimesheets();
		$master=$empTimesheets_model->monthly_master($selmn);
		$k=0;
		$weeks=array();
		$pre_week='';
		for($i=1;$i<=6;$i++)
		{
			for($j=1;$j<=7;$j++)
			{
				if($master[$k]!=0)
				{
					$weeks[$i][]=$selmn."-".str_pad($master[$k], 2, '0', STR_PAD_LEFT);
					if(date('d')==$master[$k])
					$pre_week=$i;
				}
				$k++;
			}
		}
		if($selmn==date('Y-m') && $hidweek=='')
		$hidweek=$pre_week;
		if($hidweek=='' && $selmn!=date('Y-m'))
		$hidweek=1;
		$this->view->weeks=$weeks;
		$this->view->hidweek=$hidweek;
		$this->view->manager_id=$manager_id;
		$this->view->search=$search;
		$this->view->clicked_status=$clicked_status;
		$this->view->selmn=$selmn;
		$this->view->pre_week=$pre_week;

	}

	public function getmonthlyspanAction()
	{
		$selmn=$this->_getParam('selmn');
		$date1 = new DateTime(date($selmn.'-01'));
		$startday=$date1->format('Y-m-d');//starting date of month
		$endday=$selmn."-".cal_days_in_month(CAL_GREGORIAN, $date1->format('m'), $date1->format('Y')); //ending date of month
		
		$displayYearMonth = $date1->format('F').' '.$date1->format('Y');
		
		$this->_helper->json(array('startday'=>$startday,'endday'=>$endday,'displayYearMonth'=>$displayYearMonth));

	}

	/**
	 * This action will display all employees based on the tabs selected in employee timesheet screen.
	 */
	public function accordionAction()
	{
		//print_r($this->_getAllParams());exit();
		$emp_list_flag=$this->_getParam('emp_list_flag','');
		$hidweek=$this->_getParam('hidweek');
		$type=$this->_getParam('type');
		$active=$this->_getParam('active');
		$clicked_status=$this->_getParam('clicked_status');
		$manager_id = $this->_getParam('manager_id');
		$startday = $this->_getParam('startday');
		$nstartday = new DateTime($startday);
		$endday = $this->_getParam('endday');
		$search = $this->_getParam('search');
		$year = $nstartday->format('Y');
		$month = $nstartday->format('m');
		$page = $this->_getParam('page');


		if($this->_getParam('page')){
			$page = $this->_getParam('page');
			if($page == "NaN"){
				$page = 1;
			}
		}else{
			$page = 1;
		}
		$empTimesheets_model=new Timemanagement_Model_Emptimesheets();
		$empList = array();
		$displayYearMonth = '';

		if($emp_list_flag == "project"){
			$empList = $empTimesheets_model->getEmployeesAsscociatedWithProject($manager_id,$year,$month,$search,$clicked_status,$emp_list_flag,$hidweek,$page);
		}else{
			$empList = $empTimesheets_model->getEmployeesForMonthly($manager_id,$year,$month,$search,$clicked_status,$emp_list_flag,$hidweek,$page);
		}

		$this->view->emplist= $empList;
		$this->view->hidweek=$hidweek;
		$this->view->type=$type;
		$this->view->active=$active;
		$this->view->clicked_status=$clicked_status;
		$this->view->manager_id=$manager_id;
		$this->view->startday=$startday;
		$this->view->endday=$endday;
		$this->view->search=$search;
		$this->view->page = $page;
		$this->view->emp_list_flag = $emp_list_flag;
		$this->view->selmn=$nstartday->format('Y-m');
	}

	/**
	 * Action to load employee timesheet for month or week based on flag.
	 */
	public function employeetimesheetAction(){
		$storage = new Zend_Auth_Storage_Session();
		$data = $storage->read();

		$selYrMon = $this->_getParam('selYrMon');
		$user_id = $this->_getParam('user_id');
		$manager_id = $this->_getParam('manager_id');
		$type = $this->_getParam('type');
		$hidweek = $this->_getParam('hidweek');
		$emplistflag = $this->_getParam('emplistflag');
		$project_ids = $this->_getParam('project_ids');


		$empTimesheets_model=new Timemanagement_Model_Emptimesheets();
		$min_year=$empTimesheets_model->getMinYear();
		$date1 = new DateTime(date('Y-m-01'));
		$startday=$date1->format('Y-m-d');
		$endday=date('Y-m')."-".cal_days_in_month(CAL_GREGORIAN, $date1->format('m'), $date1->format('Y')); //ending date of month

		$this->view->tm_role = Zend_Registry::get('tm_role');
		$this->view->data=$data;
		$this->view->startday_m=$startday;
		$this->view->endday_m=$endday;
		$this->view->min_year=$min_year;
		$this->view->type = $type;
		$this->view->selYrMon = $selYrMon;
		$this->view->user_id = $user_id;
		$this->view->manager_id = $manager_id;
		$this->view->hidweek = $hidweek;
		$this->view->emplistflag = $emplistflag;
		$this->view->project_ids = $project_ids;

	}
	/**
	 * This action will display employee timesheet in month format.
	 */
	public function emptimesheetmonthlyAction(){
		$storage = new Zend_Auth_Storage_Session();
		$data = $storage->read();
		$usersModel = new Timemanagement_Model_Users();
		$project_ids = "";

		$selYrMon = $this->_getParam('selYrMon');
		$user_id = $this->_getParam('user_id');
		$manager_id = $this->_getParam('manager_id');
		$emplistflag = $this->_getParam('emplistflag');

		if($this->_getParam('project_ids')){
			$project_ids = $this->_getParam('project_ids');
		}


		$empDoj = $usersModel->getEmployeeDoj($user_id);
		$this->view->empDoj=$empDoj['date_of_joining'];
		$dateEmpDoj = date('Y-m',strtotime($empDoj['date_of_joining']));
			
		$selYrMon = ($selYrMon != '')?$selYrMon:$now->format('Y-m');
		$yrMon = explode('-', $selYrMon);

		$empTSModel = new Timemanagement_Model_Emptimesheets();
		//$empMYTSModel = new Timemanagement_Model_MyTimesheet();

		$empMonthTSData = $empTSModel->getMonthTimesheetData($user_id, $yrMon[0],$yrMon[1],$project_ids,$emplistflag);
			
		$empHolidaysWeekendsData = $usersModel->getEmployeeHolidaysNWeekends($user_id, $yrMon[0],$yrMon[1]);
		$empData = $empTSModel->getEmployeeTimsheetDetails($yrMon[0],$yrMon[1],"",$user_id,$project_ids,$emplistflag);

		$total = "";
		$totalHrs = "00:00";
		foreach($empMonthTSData as $dataa) {
			$duration = explode(':',$dataa['week_duration']);
			$total += $duration[0] * 60;
			$total += $duration[1];
		}
		if($total != "") {
			$hrs = floor($total / 60);
			$mins = $total % 60;
			$totalHrs = $hrs.':'.$mins;
		}

		$firstday = $yrMon[0]."-".$yrMon[1].'-01';
		$noOfDaysMonth = date("t", mktime(0, 0, 0, $yrMon[1], 1, $yrMon[0]));
		$lastday =   $yrMon[0]."-".$yrMon[1]."-".$noOfDaysMonth;

		$empLeavesData = $usersModel->getEmpLeaves($user_id,$firstday,$lastday,'all');
		
		
		$empTimesheets_model=new Timemanagement_Model_Emptimesheets();
		$min_year=$empTimesheets_model->getMinYear();

		$this->view->empMonthTSData = $empMonthTSData;
		$this->view->empHolidaysWeekends = $empHolidaysWeekendsData[0];
		$this->view->selYrMon = $selYrMon;
		$this->view->user_id = $user_id;
		$this->view->manager_id = $manager_id;
		$this->view->type = "month";
		$this->view->empTotalHrs = $totalHrs;
		$this->view->empData = $empData;
		$this->view->leavesData = $empLeavesData;
		$this->view->emplistflag = $emplistflag;
		$this->view->project_ids = $project_ids;
		$this->view->min_year=$min_year;
		$this->view->data=$data;
	}

	/**
	 * This action will display employee timesheet in the week format.
	 */
	public function emptimesheetweeklyAction(){

		$storage = new Zend_Auth_Storage_Session();
		$data = $storage->read();
		$now = new DateTime();
		$selDay = "";
		$selYrMon = $this->_getParam('selYrMon');
		$user_id = $this->_getParam('user_id');
		$manager_id = $this->_getParam('manager_id');
		$week = $this->_getParam('hideweek');
		$emplistflag = $this->_getParam('emplistflag');
		$project_ids = "";

		if($this->_getParam('day')){
			$selDay = $this->_getParam('day');
		}
			
		if($this->_getParam('project_ids')){
			$project_ids = $this->_getParam('project_ids');
		}


		$usersModel = new Timemanagement_Model_Users();
		$empDoj = $usersModel->getEmployeeDoj($user_id);
		$dateEmpDoj = date('Y-m',strtotime($empDoj['date_of_joining']));

		$approvedAlert = $usersModel->getEmpApprovalStatusDteails($user_id);
			
		$selYrMon = ($selYrMon != '')?$selYrMon:$now->format('Y-m');
		$yrMon = explode('-', $selYrMon);
		$year = $yrMon[0];
		$month = $yrMon[1];
			
		$lastday = date("t", mktime(0, 0, 0, $month, 1, $year));
		$firstCalWeek = strftime('%U',strtotime($year.'-'.$month.'-01'));
		$lastCalWeek = strftime('%U',strtotime($selYrMon.'-'.$lastday));

		$calenderWeek = array();
		$calenderWeeksArray = range($firstCalWeek, $lastCalWeek);
		$calWeek = $calenderWeeksArray[$week-1];

		if($calWeek == null || $calWeek == '') {
			$calWeek = strftime('%U',strtotime($selYrMon.'-01'));
		}

		$myTsModel = new Timemanagement_Model_MyTimesheet();
		$empTSModel = new Timemanagement_Model_Emptimesheets();

		$myTsWeekData = $empTSModel->getWeeklyTimesheetData($user_id,$yrMon[0],$yrMon[1],$week,$project_ids);
		$empHolidaysWeekendsData = $usersModel->getEmployeeHolidaysNWeekends($user_id, $yrMon[0],$yrMon[1],$calWeek);

		$startDate = date("Y-m-d", strtotime("{$yrMon[0]}-W{$calWeek}-7"));
		//$startDate =  date("Y-m-d",strtotime('last sunday', strtotime($yrMon[0].'W'.str_pad($calWeek+1, 2, 0, STR_PAD_LEFT))));
		$endDate = date("Y-m-d",strtotime('next saturday',strtotime($startDate)));
			
		$empLeavesData = $usersModel->getEmpLeaves($user_id,$startDate,$endDate,'all');

		$weekNotes = $myTsModel->getWeekNotes($user_id,$yrMon[0],$yrMon[1],$week);
		$weekDaysStatus =  $myTsModel->getWeekDaysStatus($user_id,$yrMon[0],$yrMon[1],$week,$emplistflag,$project_ids);
			
		$empData = $empTSModel->getEmployeeTimsheetDetails($yrMon[0],$yrMon[1],$week,$user_id,$project_ids,$emplistflag);

		$this->view->empDoj=$empDoj['date_of_joining'];
		$this->view->selYrMon = $selYrMon;
		$this->view->user_id = $user_id;
		$this->view->manager_id = $manager_id;
		$this->view->hideweek = $week;
		$this->view->selWeek = $week;
		$this->view->type = "week";
		$this->view->myTsWeekData = $myTsWeekData;
		$this->view->weekNotesData = $weekNotes;
		$this->view->empHolidaysWeekends = $empHolidaysWeekendsData[0];
		$this->view->leavesData = $empLeavesData;
		$this->view->approvedAlert =  $approvedAlert;
		$this->view->weekDaysStatus = $weekDaysStatus;
		$this->view->empData = $empData;
		$this->view->emplistflag = $emplistflag;
		if($selDay != '') {
			$selDay=  date("D",strtotime($selYrMon.'-'.$selDay));
		}
		$this->view->selDay = $selDay;
		$this->view->project_ids = $project_ids;

	}

	/**
	 * Action to enable employee timesheet for month or week based on calender week.
	 */
	public function enabletimesheetAction (){
		$selYrMon = $this->_getParam('selmn');
		$emp_id = $this->_getParam('emp_id');
		$type = $this->_getParam('type');
		$week = $this->_getParam('hideweek');
		$emplistflag = $this->_getParam('emplistflag');
		$yrMon = explode('-', $selYrMon);
		$year = $yrMon[0];
		$month = $yrMon[1];

		$result = false;
		$empTSModel = new Timemanagement_Model_Emptimesheets();

		$lastday = date("t", mktime(0, 0, 0, $month, 1, $year));
		$firstCalWeek = strftime('%U',strtotime($year.'-'.$month.'-01'));
		$lastCalWeek = strftime('%U',strtotime($selYrMon.'-'.$lastday));
		$calenderWeek = array();
		$calenderWeeksArray = range($firstCalWeek, $lastCalWeek);
		if($type == 'month'){
			$calenderWeek = $calenderWeeksArray;
		}
		if($type == 'week'){
			$calenderWeek[0] = $calenderWeeksArray[$week-1];
		}
		$result = $empTSModel-> updateEmployeeTimesheet($emp_id,$year,$month,$lastday,$calenderWeek,"enable","",$emplistflag);
		$this->_helper->json(array('saved'=>$result));
	}

	/**
	 * Action to Approve employee timesheet for month or week based on calender week.
	 */
	public function approvetimesheetAction (){
		$selYrMon = $this->_getParam('selmn');
		$emp_id = $this->_getParam('emp_id');
		$type = $this->_getParam('type');
		$week = $this->_getParam('hideweek');
		$emplistflag = $this->_getParam('emplistflag');
		$yrMon = explode('-', $selYrMon);
		$year = $yrMon[0];
		$month = $yrMon[1];

		$result = false;
		$empTSModel = new Timemanagement_Model_Emptimesheets();

		$lastday = date("t", mktime(0, 0, 0, $month, 1, $year));
		$firstCalWeek = strftime('%U',strtotime($year.'-'.$month.'-01'));
		$lastCalWeek = strftime('%U',strtotime($selYrMon.'-'.$lastday));

		$calenderWeek = array();
		$calenderWeeksArray = range($firstCalWeek, $lastCalWeek);
		if($type == 'month'){
			$calenderWeek = $calenderWeeksArray;
		}
		if($type == 'week'){
			$calenderWeek[0] = $calenderWeeksArray[$week-1];
		}
		$result = $empTSModel-> updateEmployeeTimesheet($emp_id,$year,$month,$lastday,$calenderWeek,"approve","",$emplistflag);
		$this->_helper->json(array('saved'=>$result));
	}

	/**
	 * Action to reject employee timesheet for month or week based on calender week.
	 */
	public function rejecttimesheetAction (){
		$selYrMon = $this->_getParam('selmn');
		$emp_id = $this->_getParam('emp_id');
		$type = $this->_getParam('type');
		$week = $this->_getParam('hideweek');
		$rejnote = $this->_getParam('rejnote');
		$emplistflag = $this->_getParam('emplistflag');

		$yrMon = explode('-', $selYrMon);
		$year = $yrMon[0];
		$month = $yrMon[1];

		$result = false;
		$empTSModel = new Timemanagement_Model_Emptimesheets();

		$lastday = date("t", mktime(0, 0, 0, $month, 1, $year));
		$firstCalWeek = strftime('%U',strtotime($year.'-'.$month.'-01'));
		$lastCalWeek = strftime('%U',strtotime($selYrMon.'-'.$lastday));

		$calenderWeek = array();
		$calenderWeeksArray = range($firstCalWeek, $lastCalWeek);
		if($type == 'month'){
			$calenderWeek = $calenderWeeksArray;
		}
		if($type == 'week'){
			$calenderWeek[0] = $calenderWeeksArray[$week-1];
		}
		$result = $empTSModel-> updateEmployeeTimesheet($emp_id,$year,$month,$lastday,$calenderWeek,"reject",$rejnote,$emplistflag);
		$this->_helper->json(array('saved'=>$result));
	}

	/**
	 * Action to approve employee timesheet for a particular day.
	 */
	public function approvedaytimesheetAction(){
		$selYrMon = $this->_getParam('selmn');
		$emp_id = $this->_getParam('emp_id');
		$day = $this->_getParam('day');
		$emplistflag = $this->_getParam('emplistflag');

		$approvedDate = $selYrMon."-".$day;
		$yrMon = explode('-', $selYrMon);
		$year = $yrMon[0];
		$month = $yrMon[1];
		$empTSModel = new Timemanagement_Model_Emptimesheets();

		$approvedDateTimestamp = strtotime(DATE($approvedDate));
		$approvedDate_day = strtolower(DATE('D', $approvedDateTimestamp));
		$approvedDate = DATE('Y-m-d', $approvedDateTimestamp);

		$calweek=strftime('%U',strtotime($approvedDate));

		$result = $empTSModel->updateEmployeeDayTimesheet($emp_id,$calweek,$year,$month,$approvedDate_day,$approvedDate, "approve", "",$emplistflag);

		$this->_helper->json(array('saved'=>$result));
	}

	/**
	 * Action to reject employee timesheet for a particular day.
	 */
	public function rejectdaytimesheetAction(){
		$selYrMon = $this->_getParam('selmn');
		$emp_id = $this->_getParam('emp_id');
		$day = $this->_getParam('day');
		$rejnote = $this->_getParam('rejnote');
		$emplistflag = $this->_getParam('emplistflag');

		$approvedDate = $selYrMon."-".$day;
		$yrMon = explode('-', $selYrMon);
		$year = $yrMon[0];
		$month = $yrMon[1];
		$empTSModel = new Timemanagement_Model_Emptimesheets();

		$approvedDateTimestamp = strtotime(DATE($approvedDate));
		$approvedDate_day = strtolower(DATE('D', $approvedDateTimestamp));
		$approvedDate = DATE('Y-m-d', $approvedDateTimestamp);

		$calweek=strftime('%U',strtotime($approvedDate));
		$result = $empTSModel->updateEmployeeDayTimesheet($emp_id,$calweek,$year,$month,$approvedDate_day,$approvedDate, "reject",$rejnote,$emplistflag);

		$this->_helper->json(array('saved'=>$result));

	}
	//function to get week start end dates
	public function getweekstartenddatesAction()
	{
		$selYrMon = $this->_getParam('selmn');
		$week = $this->_getParam('hidweek');
		$currentMonth = date($selYrMon);
		//$datesArray =  iterator_to_array(new DatePeriod(new DateTime("first sunday of $currentMonth"),
		//DateInterval::createFromDateString('next sunday'),new DateTime("last day of $currentMonth")));
		
		
		$selectedYrMon = explode('-', $currentMonth);
      	//$selMonName = date('F', mktime(0, 0, 0, $selectedYrMon[1], 10)); 
      	$firstday = date("w", mktime(0, 0, 0, $selectedYrMon[1], 1, $selectedYrMon[0])); 
        $lastday = date("t", mktime(0, 0, 0, $selectedYrMon[1], 1, $selectedYrMon[0])); 
		$noOfweeks = 1 + ceil(($lastday-7+$firstday)/7);
		 
		
		$selWeek = $week;
		//$nextMonth = $selectedYrMon[1]+1;
		if($selectedYrMon[1] < 12) 
			$nextMonth = $selectedYrMon[1]+1;
		else {
			// $nextMonth = $selectedYrMon[1];
			$nextMonth = 1;
			$selectedYrMon[0] = $selectedYrMon[0]+1;
		}
			
		$datesArray =  iterator_to_array(new DatePeriod(new DateTime("first sunday of $currentMonth"),
    	 	DateInterval::createFromDateString('next sunday'),new DateTime("first day of $selectedYrMon[0]-$nextMonth")));
		

		$firstDay = DateTime::createFromFormat('Y-m-d', "$currentMonth".'-1');
		$firstDayName =  $firstDay->format('D');
		$wCounter = 1;
		if($firstDayName != 'Sun')  $wCounter = 2;
			
		if($week == 1) {
			$startDate = $currentMonth."-1";
			//$weekStartDay = date('F d, Y', strtotime('last sunday', strtotime($startDate)));
			$startDateName = date('D', strtotime($startDate));
			if($startDateName != "Sun") {
				$weekStartDay = date('F d, Y', strtotime('last sunday', strtotime($startDate)));
			} else {
				$weekStartDay = date('F d, Y', strtotime($startDate));
			}
		} else  {
			$startDate = $datesArray[($week-$wCounter)]->format("Y-m-d");
			$weekStartDay = date('F d, Y', strtotime($startDate));
		}
		$weekendDay = date('F d, Y', strtotime('next saturday', strtotime($weekStartDay)));
		$display_date = $weekStartDay.' - '.$weekendDay;
		$this->_helper->json(array('saved'=>$display_date));
	}
}