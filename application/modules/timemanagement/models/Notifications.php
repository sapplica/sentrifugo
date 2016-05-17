<?php

/**
 * Timemanagement_Model_Notifications
 *
 * @author l.sravani
 * @version
 */


class Timemanagement_Model_Notifications extends Zend_Db_Table_Abstract
{
	/**
	 * The default table name
	 */
	public function getnotifications($loginId,$weekend)
	{
        $db = Zend_Db_Table::getDefaultAdapter();
		  /*$query = "select ts.ts_year,ts.cal_week,
				concat(if(sun_status not in('submitted','approved'),concat(sun_date,'#',sun_status),''),'$',
				if(mon_status not in('submitted','approved'),concat(mon_date,'#',mon_status),''),'$',
				if(tue_status not in('submitted','approved'),concat(tue_date,'#',tue_status),''),'$',
				if(wed_status not in('submitted','approved'),concat(wed_date,'#',wed_status),''),'$',
				if(thu_status not in('submitted','approved'),concat(thu_date,'#',thu_status),''),'$',
				if(fri_status not in('submitted','approved'),concat(fri_date,'#',fri_status),''),'$',
				if(sat_status not in('submitted','approved'),concat(sat_date,'#',sat_status),'')) ts_week_dates
				from tm_ts_status ts 
				inner join main_employees_summary es on es.user_id = ts.emp_id
				where ts.emp_id=".$loginId." and (ts.ts_year >= year(es.date_of_joining) and cal_week >= week(es.date_of_joining))
				and (ts.ts_year <= year(now()) and cal_week <= week('".$weekend."'))
				and week_status != 'approved'
				group by ts.ts_year,ts.cal_week";*/
			 $query = "select ts_year,
				concat(if(sun_status not in('submitted','approved'),concat(sun_date,'#',if(sun_status!='',sun_status,'no_entry')),''),'$', 
				if(mon_status not in('submitted','approved'),concat(mon_date,'#',if(mon_status!='',mon_status,'no_entry')),''),'$', 
				if(tue_status not in('submitted','approved'),concat(tue_date,'#',if(tue_status!='',tue_status,'no_entry')),''),'$',
				if(wed_status not in('submitted','approved'),concat(wed_date,'#',if(wed_status!='',wed_status,'no_entry')),''),'$', 
				if(thu_status not in('submitted','approved'),concat(thu_date,'#',if(thu_status!='',thu_status,'no_entry')),''),'$', 
				if(fri_status not in('submitted','approved'),concat(fri_date,'#',if(fri_status!='',fri_status,'no_entry')),''),'$',
				if(sat_status not in('submitted','approved'),concat(sat_date,'#',if(sat_status!='',sat_status,'no_entry')),'')) ts_week_dates  
				from (
				select ts.ts_year,ts.ts_month,ts.cal_week, sun_date,TRIM( BOTH ',' FROM GROUP_CONCAT(DISTINCT if(sun_status = 'no_entry','',sun_status))) sun_status,
				mon_date,TRIM( BOTH ',' FROM GROUP_CONCAT(DISTINCT if(mon_status = 'no_entry','',mon_status))) mon_status,
				tue_date,TRIM( BOTH ',' FROM GROUP_CONCAT(DISTINCT if(tue_status = 'no_entry','',tue_status))) tue_status,
				wed_date,TRIM( BOTH ',' FROM GROUP_CONCAT(DISTINCT if(wed_status = 'no_entry','',wed_status))) wed_status,
				thu_date,TRIM( BOTH ',' FROM GROUP_CONCAT(DISTINCT if(thu_status = 'no_entry','',thu_status))) thu_status,
				fri_date,TRIM( BOTH ',' FROM GROUP_CONCAT(DISTINCT if(fri_status = 'no_entry','',fri_status))) fri_status,
				sat_date,TRIM( BOTH ',' FROM GROUP_CONCAT(DISTINCT if(sat_status = 'no_entry','',sat_status))) sat_status
				from tm_ts_status ts
				inner join main_employees_summary es on es.user_id = ts.emp_id
				where ts.emp_id=".$loginId." AND ts.ts_year >= year(es.date_of_joining)
				and cal_week <= week('".$weekend."') and week_status != 'approved'
				group by ts.ts_year,ts.cal_week
				)k";
				
		$result = $db->query($query)->fetchAll();
		return $result;
	}
	public function getSubmittedNotifications($loginId,$weekend)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		  /*$query = "select ts.ts_year,ts.cal_week,
				concat(if(sun_status in('submitted','approved'),concat(sun_date,'#',sun_status),''),'$',
				if(mon_status in('submitted','approved'),concat(mon_date,'#',mon_status),''),'$',
				if(tue_status in('submitted','approved'),concat(tue_date,'#',tue_status),''),'$',
				if(wed_status in('submitted','approved'),concat(wed_date,'#',wed_status),''),'$',
				if(thu_status in('submitted','approved'),concat(thu_date,'#',thu_status),''),'$',
				if(fri_status in('submitted','approved'),concat(fri_date,'#',fri_status),''),'$',
				if(sat_status in('submitted','approved'),concat(sat_date,'#',sat_status),'')) ts_week_dates
				from tm_ts_status ts 
				inner join main_employees_summary es on es.user_id = ts.emp_id
				where ts.emp_id=".$loginId." and (ts.ts_year >= year(es.date_of_joining) and cal_week >= week(es.date_of_joining))
				and (ts.ts_year <= year(now()) and cal_week <= week('".$weekend."'))
				and week_status != 'approved'
				group by ts.ts_year,ts.cal_week";*/
				
				 $query = "select ts_year,
				concat(if(sun_status in('submitted','approved'),concat(sun_date,'#',if(sun_status!='',sun_status,'no_entry')),''),'$', 
				if(mon_status in('submitted','approved'),concat(mon_date,'#',if(mon_status!='',mon_status,'no_entry')),''),'$', 
				if(tue_status in('submitted','approved'),concat(tue_date,'#',if(tue_status!='',tue_status,'no_entry')),''),'$',
				if(wed_status in('submitted','approved'),concat(wed_date,'#',if(wed_status!='',wed_status,'no_entry')),''),'$', 
				if(thu_status in('submitted','approved'),concat(thu_date,'#',if(thu_status!='',thu_status,'no_entry')),''),'$', 
				if(fri_status in('submitted','approved'),concat(fri_date,'#',if(fri_status!='',fri_status,'no_entry')),''),'$',
				if(sat_status in('submitted','approved'),concat(sat_date,'#',if(sat_status!='',sat_status,'no_entry')),'')) ts_week_dates  
				from (
				select ts.ts_year,ts.ts_month,ts.cal_week, sun_date,TRIM( BOTH ',' FROM GROUP_CONCAT(DISTINCT if(sun_status = 'no_entry','',sun_status))) sun_status,
				mon_date,TRIM( BOTH ',' FROM GROUP_CONCAT(DISTINCT if(mon_status = 'no_entry','',mon_status))) mon_status,
				tue_date,TRIM( BOTH ',' FROM GROUP_CONCAT(DISTINCT if(tue_status = 'no_entry','',tue_status))) tue_status,
				wed_date,TRIM( BOTH ',' FROM GROUP_CONCAT(DISTINCT if(wed_status = 'no_entry','',wed_status))) wed_status,
				thu_date,TRIM( BOTH ',' FROM GROUP_CONCAT(DISTINCT if(thu_status = 'no_entry','',thu_status))) thu_status,
				fri_date,TRIM( BOTH ',' FROM GROUP_CONCAT(DISTINCT if(fri_status = 'no_entry','',fri_status))) fri_status,
				sat_date,TRIM( BOTH ',' FROM GROUP_CONCAT(DISTINCT if(sat_status = 'no_entry','',sat_status))) sat_status
				from tm_ts_status ts
				inner join main_employees_summary es on es.user_id = ts.emp_id
				where ts.emp_id=".$loginId." AND ts.ts_year >= year(es.date_of_joining)
				and cal_week <= week('".$weekend."')
				group by ts.ts_year,ts.cal_week
				)k";
		$result = $db->query($query)->fetchAll();
		return $result;
	}
	//get time sheet status for employee in between days
	public function getTimesheetStatus($emp_id,$weekend)
	{
		$mnth = date('m');
		//and (ts.ts_year = year(now()) and ts.ts_month = month(now()) and cal_week <= week('".$weekend."'))
		$db = Zend_Db_Table::getDefaultAdapter();
		 $query = "select ts.ts_year,ts.cal_week,
				concat(if(sun_status not in('no_entry','saved'),if(month(sun_date)='".$mnth."',concat(sun_date,'#',sun_status),''),''),'$',
				if(mon_status not in('no_entry','saved'),if(month(mon_date)='".$mnth."',concat(mon_date,'#',mon_status),''),''),'$', 
				if(tue_status not in('no_entry','saved'),if(month(tue_date)='".$mnth."',concat(tue_date,'#',tue_status),''),''),'$', 
				if(wed_status not in('no_entry','saved'),if(month(wed_date)='".$mnth."',concat(wed_date,'#',wed_status),''),''),'$',
				if(thu_status not in('no_entry','saved'),if(month(thu_date)='".$mnth."',concat(thu_date,'#',thu_status),''),''),'$', 
				if(fri_status not in('no_entry','saved'),if(month(fri_date)='".$mnth."',concat(fri_date,'#',fri_status),''),''),'$',
				if(sat_status not in('no_entry','saved'),if(month(sat_date)='".$mnth."',concat(sat_date,'#',sat_status),''),'')) ts_week_dates
				from tm_ts_status ts 
				inner join main_employees_summary es on es.user_id = ts.emp_id
				where ts.emp_id=".$emp_id." and (ts.ts_year >= year(es.date_of_joining))
				and (ts.ts_year = year(now()) and ts.ts_month = month(now()) and cal_week = week('".$weekend."'))
				group by ts.ts_year,ts.ts_month,ts.cal_week";
		$result = $db->query($query)->fetchAll();
		return $result;
	}
	//get saved time sheets
	public function getSavedTimesheets($emp_id,$weekend)
	{
		$mnth = date('m');
		$db = Zend_Db_Table::getDefaultAdapter();
		 $query = "select ts.ts_year,ts.cal_week,
				concat(if(sun_status in('no_entry','saved'),if(month(sun_date)='".$mnth."',concat(sun_date,'#',sun_status),''),''),'$',
				if(mon_status in('no_entry','saved'),if(month(mon_date)='".$mnth."',concat(mon_date,'#',mon_status),''),''),'$', 
				if(tue_status in('no_entry','saved'),if(month(tue_date)='".$mnth."',concat(tue_date,'#',tue_status),''),''),'$', 
				if(wed_status in('no_entry','saved'),if(month(wed_date)='".$mnth."',concat(wed_date,'#',wed_status),''),''),'$',
				if(thu_status in('no_entry','saved'),if(month(thu_date)='".$mnth."',concat(thu_date,'#',thu_status),''),''),'$', 
				if(fri_status in('no_entry','saved'),if(month(fri_date)='".$mnth."',concat(fri_date,'#',fri_status),''),''),'$',
				if(sat_status in('no_entry','saved'),if(month(sat_date)='".$mnth."',concat(sat_date,'#',sat_status),''),'')) ts_week_dates
				from tm_ts_status ts 
				inner join main_employees_summary es on es.user_id = ts.emp_id
				where ts.emp_id=".$emp_id." and (ts.ts_year >= year(es.date_of_joining))
				and (ts.ts_year = year(now()) and ts.ts_month = month(now()) and cal_week = week('".$weekend."'))
				group by ts.ts_year,ts.ts_month,ts.cal_week";
		$result = $db->query($query)->fetchAll();
		return $result;
	}

	//function to get employees under reporting manager
	public function getManagerEmployees($managerId)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select es.user_id,es.userfullname from main_employees_summary es where es.isactive=1 and es.reporting_manager = ".$managerId;
		$result = $db->query($query)->fetchAll();
		return $result;

	}
	//function to get previous days time sheet status less than current week
	public function getpreviousTimesheetStatus($loginId,$weekend,$mnth,$year)
	{
        $db = Zend_Db_Table::getDefaultAdapter();
		  $query = "select ts.ts_year,ts.cal_week,
			concat(if(sun_status not in('rejected','blocked'), if(month(sun_date)='".$mnth."',concat(sun_date,'#',sun_status),''),''),'$',
			if(mon_status not in('rejected','blocked'),if(month(mon_date)='".$mnth."',concat(mon_date,'#',mon_status),''),''),'$', 
			if(tue_status not in('rejected','blocked'),if(month(tue_date)='".$mnth."',concat(tue_date,'#',tue_status),''),''),'$', 
			if(wed_status not in('rejected','blocked'),if(month(wed_date)='".$mnth."',concat(wed_date,'#',wed_status),''),''),'$', 
			if(thu_status not in('rejected','blocked'),if(month(thu_date)='".$mnth."',concat(thu_date,'#',thu_status),''),''),'$', 
			if(fri_status not in('rejected','blocked'),if(month(fri_date)='".$mnth."',concat(fri_date,'#',fri_status),''),''),'$', 
			if(sat_status not in('rejected','blocked'),if(month(sat_date)='".$mnth."',concat(sat_date,'#',sat_status),''),'')) ts_week_dates 
			from tm_ts_status ts 
			inner join main_employees_summary es on es.user_id = ts.emp_id
			where ts.emp_id=".$loginId." and (ts.ts_year >= year(es.date_of_joining))
				and (ts.ts_year <= '".$year."' and ts.ts_month = '".$mnth."' and cal_week = week('".$weekend."'))
				group by ts.ts_year,ts.cal_week";
		$result = $db->query($query)->fetchAll();
		return $result;
	}
	//get time sheet status for employee in between days
	public function getpreviousSavedTimesheets($emp_id,$weekend,$mnth,$year)
	{
		//and (ts.ts_year = year(now()) and ts.ts_month = month(now()) and cal_week <= week('".$weekend."'))
		$db = Zend_Db_Table::getDefaultAdapter();
		 $query = "select ts.ts_year,ts.cal_week,
				concat(if(sun_status in('rejected','blocked'), if(month(sun_date)='".$mnth."',concat(sun_date,'#',sun_status),''),''),'$',
			if(mon_status in('rejected','blocked'),if(month(mon_date)='".$mnth."',concat(mon_date,'#',mon_status),''),''),'$', 
			if(tue_status in('rejected','blocked'),if(month(tue_date)='".$mnth."',concat(tue_date,'#',tue_status),''),''),'$', 
			if(wed_status in('rejected','blocked'),if(month(wed_date)='".$mnth."',concat(wed_date,'#',wed_status),''),''),'$', 
			if(thu_status in('rejected','blocked'),if(month(thu_date)='".$mnth."',concat(thu_date,'#',thu_status),''),''),'$', 
			if(fri_status in('rejected','blocked'),if(month(fri_date)='".$mnth."',concat(fri_date,'#',fri_status),''),''),'$', 
			if(sat_status in('rejected','blocked'),if(month(sat_date)='".$mnth."',concat(sat_date,'#',sat_status),''),'')) ts_week_dates 
				from tm_ts_status ts 
				inner join main_employees_summary es on es.user_id = ts.emp_id
				where ts.emp_id=".$emp_id." and (ts.ts_year >= year(es.date_of_joining))
				and (ts.ts_year = '".$year."' and ts.ts_month = '".$mnth."' and cal_week = week('".$weekend."'))
				group by ts.ts_year,ts.ts_month,ts.cal_week";
		$result = $db->query($query)->fetchAll();
		//echo $query.'<br/>';
		return $result;
	}
	//get number of weeks in current given month
	function weeks_in_month($year, $month, $start_day_of_week)
	{
		// Total number of days in the given month.
		$num_of_days = date("t", mktime(0,0,0,$month,1,$year));
	 
		// Count the number of times it hits $start_day_of_week.
		$num_of_weeks = 0;
		for($i=1; $i<=$num_of_days; $i++)
		{
		  $day_of_week = date('w', mktime(0,0,0,$month,$i,$year));
		  if($day_of_week==$start_day_of_week)
			$num_of_weeks++;
		}
	 
		return $num_of_weeks;
	}

}
