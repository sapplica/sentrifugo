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

class Timemanagement_NotificationsController extends Zend_Controller_Action
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
		if(!sapp_Helper::checkTmEnable())
			$this->_redirect('error');
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('getnotifications', 'html')->initContext();
		$ajaxContext->addActionContext('pendingsubmissionsweeklyview', 'html')->initContext();
		$ajaxContext->addActionContext('weeklymonthlyview', 'html')->initContext();
	}

	public function init()
	{
		$this->_options= $this->getInvokeArg('bootstrap')->getOptions();
	}
	public function indexAction()
	{
		$perPage = PERPAGE;
		$auth = Zend_Auth::getInstance();
		$loginUserId=0;
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$yet_to_submit_dates = array();
		$notificationModel = new Timemanagement_Model_Notifications();
		$usersModel = new Timemanagement_Model_Users();
		$employeeDOJ = $usersModel->getEmployeeDoj($loginUserId);
		$loginUserGroupId = $employeeDOJ['holiday_group'];
		$department_id = $employeeDOJ['department_id'];
		$weekend_date = date('Y-m-d', strtotime('last saturday'));
		$notificationArray = array();
		$submitted_notificationArray = array();
		$final_notification_Arry = array();
		$notificationArray = $notificationModel->getnotifications($loginUserId,$weekend_date);
		$submitted_notificationArray = $notificationModel->getSubmittedNotifications($loginUserId,$weekend_date);
		$employeeLeaves = array();
		$getWeekends = array();
		$employeeLeaves = $usersModel->getEmpLeaves($loginUserId,$employeeDOJ['date_of_joining'],$weekend_date);
		$getWeekends = $usersModel->getWeekend($employeeDOJ['date_of_joining'],$weekend_date,$department_id);
		$between_days = array();
		
		$doj_date = strtotime($employeeDOJ['date_of_joining']);
		$created_date = strtotime($employeeDOJ['createddate']);
		if($created_date < $doj_date)		
			$between_days = sapp_Global::createDateRangeArray($employeeDOJ['date_of_joining'],$weekend_date);
		else
			$between_days = sapp_Global::createDateRangeArray($employeeDOJ['createddate'],$weekend_date);
		
		//$between_days = sapp_Global::createDateRangeArray($employeeDOJ['date_of_joining'],$weekend_date);
		
		
		$holidayDatesArr =  array();
		$holidayDateslistArr=array();
		if( isset($loginUserId) && $loginUserId !=''){
			$holidaydatesmodel = new Default_Model_Holidaydates();
			if($loginUserGroupId>0)
			{
				$holidayDateslistArr = $holidaydatesmodel->getHolidayDatesListForGroup($loginUserGroupId);
			}
			if(!empty($holidayDateslistArr))
			{
				for($i=0;$i<sizeof($holidayDateslistArr);$i++)
				{
					$holidayDatesArr[$i] = $holidayDateslistArr[$i]['holidaydate'];
				}
			}
		}

		$emp_leave_days = array();
		foreach($employeeLeaves as $empleave)
		{
			$emplev_start_date = $empleave['from_date'];
			$emplev_endt_date = $empleave['to_date'];
			$emp_leave_days[] = sapp_Global::createDateRangeArray($emplev_start_date,$emplev_endt_date);
		}
		$employee_leave_days = array();
		foreach($emp_leave_days as $lev_days)
		{
			foreach($lev_days as $days)
			{
				$employee_leave_days[] = $days;
			}
		}
		//merge all dates holidays,weekends,leaves
		$hol_leav_weknd = array();
		$hol_leav_weknd = array_merge($holidayDatesArr,$employee_leave_days,$getWeekends);
		$working_days = array();
		$dates_with_status = array();
		$yet_to_submit_array = array();
		$rejected_array=array();
		$blocked_array=array();
		$enabled_array=array();
		$dates_without_status = array();
		//remove holidays,weekend,leaves from between days
		$working_days = array_diff($between_days, $hol_leav_weknd);
		$tsEnteredDatesArray = array();
		if(count($submitted_notificationArray)>0)
		{
			$submittes_ts_dates_with_status = '';
			foreach($submitted_notificationArray as $sub_array)
			{
				$submittes_ts_dates_with_status.=$sub_array['ts_week_dates'];
				$submittes_ts_dates_with_status.='$';
			}
			$ts_date_with_status_array = array_filter(explode('$',$submittes_ts_dates_with_status));
			$tsEnteredDates = implode('#',$ts_date_with_status_array);
			$tsEnteredDatesArray = explode('#',$tsEnteredDates);
		}

		if(count($notificationArray)>0 || count($submitted_notificationArray)>0)//if employee not saved timesheet
		{
			//get entered timesheet dates
			$timesheet_dates_with_status = '';
			foreach($notificationArray as $array)
			{
				$timesheet_dates_with_status.= $array['ts_week_dates'];
				$timesheet_dates_with_status.='$';
			}
			$timesheet_date_with_status_array = array_filter(explode('$',$timesheet_dates_with_status));
			//print_r($timesheet_date_with_status_array);
			$timesheetEnteredDates = implode('#',$timesheet_date_with_status_array);
			//echo $timesheetEnteredDates;
			$timesheetEnteredDates_Array = explode('#',$timesheetEnteredDates);
			$timesheetEnteredDatesArray = $timesheetEnteredDates_Array;
			if(count($tsEnteredDatesArray)>0)
			{
				$timesheetEnteredDatesArray = array_merge($timesheetEnteredDates_Array,$tsEnteredDatesArray);
			}
			$enteredDatesArray = array();
			$statusArray = array('saved','enabled','rejected','blocked','no_entry','submitted','approved');
			$enteredDateStatusArr = array();
			foreach($timesheetEnteredDatesArray as $date)
			{
				if(!empty($date))
				{
					if(!in_array($date,$statusArray))
					{
						$enteredDatesArray[] = $date;
					}
					else
					{
						$enteredDateStatusArr[] = $date;
					}
				}
			}
			$not_ts_weekend_ts = array();
			foreach($enteredDatesArray as $key=>$value)
			{
				if(isset($key)){
					if(in_array($value,$hol_leav_weknd) && $enteredDateStatusArr[$key]=='no_entry')
					{
						$not_ts_weekend_ts[] = $value;
					}
					$not_ts_weekend_ts[] = $value;
					if($enteredDateStatusArr[$key]=='saved' || ($enteredDateStatusArr[$key]=='no_entry' && !in_array($value,$hol_leav_weknd)))
					{
						$yet_to_submit_array[$value] = $enteredDateStatusArr[$key];
					}else if($enteredDateStatusArr[$key]=='rejected')
					{
						$rejected_array[$value] = $enteredDateStatusArr[$key];
					}else if($enteredDateStatusArr[$key]=='blocked')
					{
						$blocked_array[$value] = $enteredDateStatusArr[$key];
					}else if($enteredDateStatusArr[$key]=='enabled')
					{
						$enabled_array[$value] = $enteredDateStatusArr[$key];
					}
					$dates_with_status[$value] = $enteredDateStatusArr[$key];
				}
			}
			$remainingDays = array_diff($working_days,$enteredDatesArray);

			foreach($remainingDays as $dates)
			{
				$dates_without_status[$dates] = 'no_entry';
			}
			$display_dates = array_merge($dates_with_status,$dates_without_status);
		}
		else
		{
			$remainingDays = $working_days;
			foreach($remainingDays as $dates)
			{
				$dates_without_status[$dates] = 'no_entry';
			}
			$display_dates = $dates_without_status;
		}
		$display_dates_array = array();
		foreach($display_dates as $keydate => $statusarr)
		{
			if($keydate>=$employeeDOJ['date_of_joining'] && !($display_dates[$keydate]=='no_entry' && in_array($keydate,$hol_leav_weknd)))
			{
				if($display_dates[$keydate]!='submitted' && $display_dates[$keydate]!='approved') {
					$display_dates_array[$keydate] = $statusarr;
				}
			}
		}
		$this->view->allCount = count($display_dates_array);
		$this->view->enabledCount = count($enabled_array);
		$this->view->rejectedCount = count($rejected_array);
		$this->view->blockedCount = count($blocked_array);
		$yet_to_submit_arr = array();
		foreach($yet_to_submit_array as $key =>$valu)
		{
			if($key>=$employeeDOJ['date_of_joining'])
			{
				$yet_to_submit_arr[$key] = $valu;
			}
		}
		$this->view->pageNo = 1;
		$this->view->perPage = $perPage;
		$yet_to_submit_dates = array_merge($yet_to_submit_arr,$dates_without_status);
		$this->view->yet_to_submit_dates = $yet_to_submit_dates;
		$this->view->totalJsonCount = count($yet_to_submit_dates);
	}
	//function by sravani for displaying notifications
	public function getnotificationsAction()
	{
		$type = $this->_getParam('type');
		$perPage = PERPAGE;
		$auth = Zend_Auth::getInstance();
		$loginUserId=0;
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$yet_to_submit_dates = array();
		$notificationModel = new Timemanagement_Model_Notifications();
		$usersModel = new Timemanagement_Model_Users();
		$employeeDOJ = $usersModel->getEmployeeDoj($loginUserId);
		$loginUserGroupId = $employeeDOJ['holiday_group'];
		$department_id = $employeeDOJ['department_id'];
		$weekend_date = date('Y-m-d', strtotime('last saturday'));
		$notificationArray=array();
		$submitted_notificationArray = array();
		$notificationArray = $notificationModel->getnotifications($loginUserId,$weekend_date);
		$submitted_notificationArray = $notificationModel->getSubmittedNotifications($loginUserId,$weekend_date);
		$employeeLeaves = array();
		$getWeekends=array();
		$employeeLeaves = $usersModel->getEmpLeaves($loginUserId,$employeeDOJ['date_of_joining'],$weekend_date);
		$getWeekends = $usersModel->getWeekend($employeeDOJ['date_of_joining'],$weekend_date,$department_id);
		$between_days=array();
		
		$doj_date = strtotime($employeeDOJ['date_of_joining']);
		$created_date = strtotime($employeeDOJ['createddate']);
		if($created_date < $doj_date)		
			$between_days = sapp_Global::createDateRangeArray($employeeDOJ['date_of_joining'],$weekend_date);
		else
			$between_days = sapp_Global::createDateRangeArray($employeeDOJ['createddate'],$weekend_date);

		//$between_days = sapp_Global::createDateRangeArray($employeeDOJ['date_of_joining'],$weekend_date);
		$holidayDatesArr =  array();
		if( isset($loginUserId) && $loginUserId !=''){
			$holidaydatesmodel = new Default_Model_Holidaydates();
			if($loginUserGroupId>0)
			{
				$holidayDateslistArr = $holidaydatesmodel->getHolidayDatesListForGroup($loginUserGroupId);
			}
			if(!empty($holidayDateslistArr))
			{
				for($i=0;$i<sizeof($holidayDateslistArr);$i++)
				{
					$holidayDatesArr[$i] = $holidayDateslistArr[$i]['holidaydate'];
				}
			}
		}

		$emp_leave_days = array();
		foreach($employeeLeaves as $empleave)
		{
			$emplev_start_date = $empleave['from_date'];
			$emplev_endt_date = $empleave['to_date'];
			$emp_leave_days[] = sapp_Global::createDateRangeArray($emplev_start_date,$emplev_endt_date);
		}
		$employee_leave_days = array();
		foreach($emp_leave_days as $lev_days)
		{
			foreach($lev_days as $days)
			{
				$employee_leave_days[] = $days;
			}
		}
		//merge all dates holidays,weekends,leaves
		$hol_leav_weknd = array();
		$hol_leav_weknd = array_merge($holidayDatesArr,$employee_leave_days,$getWeekends);

		$working_days = array();
		$dates_with_status = array();
		$yet_to_submit_array = array();
		$enabled_array = array();
		$rejected_array = array();
		$blocked_array = array();
		$dates_without_status = array();
		//remove holidays,weekend,leaves from between days
		$working_days = array_diff($between_days, $hol_leav_weknd);

		$tsEnteredDatesArray = array();
		if(count($submitted_notificationArray)>0)
		{
			$submittes_ts_dates_with_status = '';
			foreach($submitted_notificationArray as $sub_array)
			{
				$submittes_ts_dates_with_status.=$sub_array['ts_week_dates'];
				$submittes_ts_dates_with_status.='$';
			}
			$ts_date_with_status_array = array_filter(explode('$',$submittes_ts_dates_with_status));
			$tsEnteredDates = implode('#',$ts_date_with_status_array);
			$tsEnteredDatesArray = explode('#',$tsEnteredDates);
		}

		if(count($notificationArray)>0 || count($submitted_notificationArray)>0) {
			//get entered timesheet dates
			$timesheet_dates_with_status = '';
			foreach($notificationArray as $array)
			{
				$timesheet_dates_with_status.= $array['ts_week_dates'];
				$timesheet_dates_with_status.='$';
			}
			$timesheet_date_with_status_array = array_filter(explode('$',$timesheet_dates_with_status));
			//print_r($timesheet_date_with_status_array);
			$timesheetEnteredDates = implode('#',$timesheet_date_with_status_array);
			//echo $timesheetEnteredDates;
			$timesheetEnteredDates_Array = explode('#',$timesheetEnteredDates);
			$timesheetEnteredDatesArray = $timesheetEnteredDates_Array;
			if(count($tsEnteredDatesArray)>0)
			{
				$timesheetEnteredDatesArray = array_merge($timesheetEnteredDates_Array,$tsEnteredDatesArray);
			}
			$enteredDatesArray = array();
			//$statusArray = array('saved','enabled','rejected','blocked','no_entry');
			$statusArray = array('saved','enabled','rejected','blocked','no_entry','submitted','approved');
			$enteredDateStatusArr = array();
			foreach($timesheetEnteredDatesArray as $date)
			{
				if(!empty($date))
				{
					if(!in_array($date,$statusArray))
					{
						$enteredDatesArray[] = $date;
					}
					else
					{
						$enteredDateStatusArr[] = $date;
					}
				}
			}
			foreach($enteredDatesArray as $key=>$value)
			{
				if(isset($key)){
					if($type == 'yet_to_submit')
					{
						if($enteredDateStatusArr[$key]=='saved' || ($enteredDateStatusArr[$key]=='no_entry' && !in_array($value,$hol_leav_weknd)))
						{
							$yet_to_submit_array[$value] = $enteredDateStatusArr[$key];
						}
					}else if($type == 'rejected')
					{
						if($enteredDateStatusArr[$key]=='rejected')
						{
							$rejected_array[$value] = $enteredDateStatusArr[$key];
						}
					}else if($type == 'blocked')
					{
						if($enteredDateStatusArr[$key]=='blocked')
						{
							$blocked_array[$value] = $enteredDateStatusArr[$key];
						}
					}else if($type == 'enabled')
					{
						if($enteredDateStatusArr[$key]=='enabled')
						{
							$enabled_array[$value] = $enteredDateStatusArr[$key];
						}
					}else if($type == 'all')
					{
						if(!($enteredDateStatusArr[$key]=='no_entry' && in_array($value,$hol_leav_weknd)))
						{
							if($enteredDateStatusArr[$key]!='submitted' && $enteredDateStatusArr[$key]!='approved') {
								$dates_with_status[$value] = $enteredDateStatusArr[$key];
							}
						}
					}
				}
			}
			$remainingDays = array_diff($working_days,$enteredDatesArray);

			foreach($remainingDays as $dates)
			{
				$dates_without_status[$dates] = 'no_entry';
			}
			$display_dates = array_merge($dates_with_status,$dates_without_status);
		}
		else
		{
			$remainingDays = $working_days;
			foreach($remainingDays as $dates)
			{
				$dates_without_status[$dates] = 'no_entry';
			}
			$display_dates = $dates_without_status;
		}

		$display_dates_array = array();
		foreach($display_dates as $keydate => $statusarr)
		{
			if($keydate>=$employeeDOJ['date_of_joining'] && !($display_dates[$keydate]=='no_entry' && in_array($keydate,$hol_leav_weknd)))
			{
				$display_dates_array[$keydate] = $statusarr;
			}
		}

		$this->view->pageNo = 1;
		$this->view->perPage = $perPage;
		$yet_to_submit_dates_array = array_merge($yet_to_submit_array,$dates_without_status);
		foreach($yet_to_submit_dates_array as $key =>$valu)
		{
			if($key>=$employeeDOJ['date_of_joining'])
			{
				$yet_to_submit_dates[$key] = $valu;
			}
		}
		if($type == 'all')
		{
			$this->view->dataArray = $display_dates_array;
			$this->view->totalJsonCount = count($display_dates_array);
		}else if($type == 'enabled')
		{
			$this->view->dataArray = $enabled_array;
			$this->view->totalJsonCount = count($enabled_array);
		}else if($type == 'blocked')
		{
			$this->view->dataArray = $blocked_array;
			$this->view->totalJsonCount = count($blocked_array);
		}else if($type == 'rejected')
		{
			$this->view->dataArray = $rejected_array;
			$this->view->totalJsonCount = count($rejected_array);
		}else if($type == 'yet_to_submit')
		{
			$this->view->dataArray = $yet_to_submit_dates;
			$this->view->totalJsonCount = count($yet_to_submit_dates);
		}
		$this->view->type=$type;
	}
	//function to get pending submissions
	public function pendingsubmissionsAction()
	{
		$auth = Zend_Auth::getInstance();
		$loginUserId=0;
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$weekend_date = date('Y-m-d', strtotime('last saturday'));
		$notificationModel = new Timemanagement_Model_Notifications();
		$getManagerEmployees = array();
		$getManagerEmployees = $notificationModel->getManagerEmployees($loginUserId);
		$concat_array = array();
		if(count($getManagerEmployees)>0)
		{
			$is_current_week = 1;
			$usersModel = new Timemanagement_Model_Users();
			$projmodel = new Timemanagement_Model_Projects();
			$prevweeks = $projmodel->getprevmonthweeks(date('Y-m') , date('d'));
			$end_date = max($prevweeks[count($prevweeks)]);//get previous week end date
			$start_date = min($prevweeks[count($prevweeks)]);//get previous week start date
			if(!($end_date>=date('Y-m-01') && $end_date<=date('Y-m-t')))//check previous week end date is in current month or not
			{	
				$is_current_week = 0;
				$sunday = strtotime("last sunday");
				$sunday = date('w', $sunday)==date('w') ? $sunday+7*86400 : $sunday;
				$satday = strtotime(date("Y-m-d",$sunday)." +6 days");
				$this_week_sd = date("Y-m-d",$sunday);
				$this_week_ed = date("Y-m-d",$satday);
				
				if($this_week_sd==date('Y-m-01'))//if month starts with sunday
				{
					$previous_week = strtotime("-1 week +1 day");

					$start_week = strtotime("last sunday",$previous_week);
					$end_week = strtotime("next saturday",$start_week);

					$start_week = date("Y-m-d",$start_week);
					$end_week = date("Y-m-d",$end_week);

					//	echo $start_week.' '.$end_week ;
					$this_week_sd = $start_week;
					$this_week_ed = $end_week;
				}	

				//To get day in the week
				$weekDatesArray = sapp_Global::createDateRangeArray($this_week_sd,$this_week_ed);
				//End
				
				foreach($weekDatesArray as $this_date)
				{
					if(!($this_date>=date('Y-m-01') && $this_date<=date('Y-m-t')))
					{
						$concat_array[] = $this_date;
					}
				}
			}
			if(count($concat_array)>0)
			{
				$prevweeks[count($prevweeks)+1] = $concat_array;
			}
			$prev_week_status = array();
			$emp_names = array();
			foreach($getManagerEmployees as $emp)
			{
				$emp_names[$emp['user_id']] = $emp['userfullname'];
				$empDoj = $usersModel->getEmployeeDoj($emp['user_id']);
				$prev_week_status[$emp['user_id']]=$projmodel->getPreviousDaysTSStatus($prevweeks,$emp['user_id'],$empDoj);
			}
			$total_weeks=0;
			$this->view->total_weeks = count($prevweeks);
		}
		$this->view->pending_submission = $prev_week_status;
		$this->view->prevweeks = $prevweeks;
		$this->view->emp_names = $emp_names;
		$this->view->is_current_week = $is_current_week;
	}
	//function to show pending submission weekly view
	public function pendingsubmissionsweeklyviewAction()
	{
		$post_array=$this->_getParam('display_array');
		$weekly_preview=$this->_getParam('weekly_preview');
		$emp_names=$this->_getParam('emp_names');
		$selType=$this->_getParam('type');
		$is_current_week=$this->_getParam('is_current_week');
		$display_date = json_decode($post_array,true);
		$week_dates = json_decode($weekly_preview,true);
		$emp_names = json_decode($emp_names,true);
		$this->view->pending_submission = $display_date;
		$this->view->prevweeks = $week_dates;
		$this->view->emp_names = $emp_names;
		$this->view->is_current_week = $is_current_week;
		$this->view->type = $selType;
		$currentMonth = date('Y-m');
		if($is_current_week==0)
		{
			$currentMonth = date('Y-m', strtotime(date('Y-m')." -1 month"));
		}
		$week = $selType;
		$datesArray =  iterator_to_array(new DatePeriod(new DateTime("first sunday of $currentMonth"),
    	DateInterval::createFromDateString('next sunday'),new DateTime("last day of $currentMonth")));
		
		$firstDay = DateTime::createFromFormat('Y-m-d', "$currentMonth".'-1');
		$firstDayName =  $firstDay->format('D');
		$wCounter = 1;
		if($firstDayName != 'Sun')  $wCounter = 2;
			
		if($week == 1) {
			$startDate = $currentMonth."-1";
			$weekStartDay = date('F d, Y', strtotime('last sunday', strtotime($startDate)));
			if($startDate==date('Y-m-1'))
			{
				$weekStartDay = date('F d, Y', strtotime($startDate));		
			}
		} else  {
			$startDate = $datesArray[($week-$wCounter)]->format("Y-m-d");
			$weekStartDay = date('F d, Y', strtotime($startDate));
		}
		$lastwkend_date_mnth = date('Y-m',strtotime('next saturday', strtotime($weekStartDay)));
		$lastweek_start_mnth = date('Y-m', strtotime('last sunday', strtotime($startDate)));
		$weekendDay = date('F d, Y', strtotime('next saturday', strtotime($weekStartDay)));
		if($lastweek_start_mnth != $lastwkend_date_mnth && $is_current_week==0)
		{
			$weekendDay = date("F d, Y", strtotime("last day of previous month"));
		}
		
		
		$hidstartweek_date=min($week_dates[$week]);
		$hidendweek_date=max($week_dates[$week]);
		$weekStartDay = date('F d, Y', strtotime($hidstartweek_date));
		$weekendDay = date("F d, Y", strtotime($hidendweek_date));
		
		$display_date = $weekStartDay.' - '.$weekendDay;
		$this->view->weekStartDay =$weekStartDay;
		$this->view->weekendDay =$weekendDay;
		$this->view->display_date =$display_date;
		
	}
	//function to get monthly,weely view
	public function weeklymonthlyviewAction()
	{
		$post_array=$this->_getParam('display_array');
		$weekly_preview=$this->_getParam('weekly_preview');
		$emp_names=$this->_getParam('emp_names');
		$input_type=$this->_getParam('input_type');
		$total_weeks=$this->_getParam('total_weeks');
		$div_name=$this->_getParam('div_name');
		$is_current_week=$this->_getParam('is_current_week');
		$display_date = json_decode($post_array,true);
		$week_dates = json_decode($weekly_preview,true);
		$emp_names = json_decode($emp_names,true);
		$this->view->pending_submission = $display_date;
		$this->view->prevweeks = $week_dates;
		$this->view->emp_names = $emp_names;
		$this->view->input_type = $input_type;
		$this->view->total_weeks = $total_weeks;
		$this->view->div_name = $div_name;
		$this->view->is_current_week = $is_current_week;
	}
}
?>
