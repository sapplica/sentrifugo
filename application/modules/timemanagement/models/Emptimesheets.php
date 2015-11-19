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
 *
 * @model Employee Timesheets Model
 * @author sagarsoft
 *
 */
class Timemanagement_Model_Emptimesheets extends Zend_Db_Table_Abstract
{
	protected $_name = 'tm_emp_timesheets';
	protected $_primary = 'id';
	protected $_ts_status = 'tm_ts_status';
	public function getMinYear()
	{
		$select=$this->select()
		->setIntegrityCheck(false)
		->from(array('e'=>'main_employees_summary'),array('minyear'=>'min(year(e.date_of_joining))'))
		->where("e.isactive=1 and e.date_of_joining!='0000-00-00 00:00:00'");
		$data=$this->fetchRow($select)->toArray();
		$minyear=$data['minyear'];
		return $minyear;
	}
	
	public function monthly_master($selmn)
	{
		if($selmn!='')
		{
			$week=array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
			$date=date($selmn.'-01');
			$dt_arr=preg_split('/-/',$date);
			$lsday = cal_days_in_month(CAL_GREGORIAN, $dt_arr[1], $dt_arr[0]);
			$wday = date('l',mktime(0,0,0,$dt_arr[1],$dt_arr[2],$dt_arr[0]));
			$wval = array_search($wday, $week);
			$master=array();
			$j=1;
			for($i=0;$i<42;$i++)
			{
				$master[$i]=0;
				if($i>=$wval && $i<($lsday+$wval))
				{
					$master[$i]=$j;
					$j++;
				}
			}
			return $master;
		}
	}

	public function getEmployeesAsscociatedWithProject($manager_id = "",$year,$month,$search="",$clicked_status,$emp_list_flag="",$week="",$current_page){
		
		$search=urldecode($search);
		$stat_arr=array('submitted'=>'For Approval','saved'=>'Saved');
		$fin_arr=array();
		$i=0;
		$where = " WHERE (ms.isactive = 1 AND pe.is_active=1 ) ";
		$per_page = 8;
		$current_index = ($current_page - 1) * $per_page;
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$duration = "IF(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.week_duration as time))))is null,'00:00',
						time_format(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.week_duration as time)))),'%H:%i'))";

		$time_status = "IF(FIND_IN_SET('submitted',GROUP_CONCAT(DISTINCT week_status))!=0,'For Approval',
						IF(FIND_IN_SET('saved',GROUP_CONCAT(DISTINCT week_status))!=0,'Saved','No Entry'))";
		if($emp_list_flag=="project")
		{
			$time_status = "IF(FIND_IN_SET('submitted',GROUP_CONCAT(sun_project_status,',',mon_project_status,',',tue_project_status,',',wed_project_status,',',thu_project_status,',',fri_project_status,',',sat_project_status))!=0,'For Approval',
			IF(FIND_IN_SET('saved',GROUP_CONCAT(sun_project_status,',',mon_project_status,',',tue_project_status,',',wed_project_status,',',thu_project_status,',',fri_project_status,',',sat_project_status))!=0,'Saved','No Entry'
			))";
		}		

		$selectEmpTimesheetsQuery = "SELECT GROUP_CONCAT( distinct pe.project_id) proj_ids, ms.user_id AS empid, ms.userfullname AS empname,
									".$duration." AS duration, 
									".$time_status." AS time_status,et.ts_week,et.ts_year,et.ts_month
									 FROM tm_project_employees AS pe 
									 INNER JOIN tm_projects p ON p.id = pe.project_id AND project_status  != 'draft'

									 INNER JOIN  main_employees_summary AS ms ON ms.user_id = pe.emp_id";
		
		if($week != ""){
			$selectEmpTimesheetsQuery.=" LEFT JOIN tm_emp_timesheets AS et ON et.emp_id = pe.emp_id and et.project_id = pe.project_id and et.project_id IS NOT NULL and et.ts_year =".$year." and et.ts_month = ".$month." and et.ts_week =".$week;
		}else{
			$selectEmpTimesheetsQuery.=" LEFT JOIN tm_emp_timesheets AS et ON et.emp_id = pe.emp_id and et.project_id = pe.project_id and et.project_id IS NOT NULL and et.ts_year =".$year." and et.ts_month = ".$month;
		}
		
		$selectEmpTimesheetsQuery.=" LEFT JOIN ".$this->_ts_status." AS ts ON ts.emp_id = et.emp_id and ts.project_id = et.project_id and et.ts_year = ts.ts_year
									and et.ts_month = ts.ts_month and ts.ts_week = et.ts_week";
		
		if(trim($search) != ""){
			$where .= " AND (ms.userfullname LIKE ".$db->quote(trim('%'.trim($search).'%'))." ) ";
		}
		
		$where.= " and ms.user_id  != ".$manager_id;
		
		$where.= " and  pe.project_id in (select project_id from  tm_project_employees where emp_id = ".$manager_id." and is_active = 1)";
		
		$selectEmpTimesheetsQuery.=$where;
		$selectEmpTimesheetsQuery.=" GROUP BY ms.user_id";

		if($clicked_status != "all"){
			$selectEmpTimesheetsQuery.=" HAVING time_status=\"".$stat_arr[$clicked_status]."\" ";
		}
		$selectEmpTimesheetsQuery .= " LIMIT ".$current_index.",".$per_page;

		$res = $db->query($selectEmpTimesheetsQuery);
		while ($row=$res->fetch())
		{

			$fin_arr[$i]['proj_ids']=$row['proj_ids'];
			$fin_arr[$i]['emp_id']=$row['empid'];
			$fin_arr[$i]['emp_name']=$row['empname'];
			$fin_arr[$i]['duration']=$row['duration'];
			$fin_arr[$i]['time_status']=$row['time_status'];
			$fin_arr[$i]['ts_week']=$row['ts_week'];
			$fin_arr[$i]['ts_year']=$row['ts_year'];
			$fin_arr[$i]['ts_month']=$row['ts_month'];
			$i++;
		}

		return $fin_arr;
		
	}
	
	public function getEmployeesForMonthly($manager_id = "",$year,$month,$search="",$clicked_status,$emp_list_flag="",$week="",$current_page)
	{
		$search=urldecode($search);
		$stat_arr=array('submitted'=>'For Approval','saved'=>'Saved');
		$fin_arr=array();
		$i=0;
		$where = " WHERE (ms.isactive = 1) ";
		$per_page = 8;
		$current_index = ($current_page - 1) * $per_page;
		$db = Zend_Db_Table::getDefaultAdapter();

		$duration = "IF(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.week_duration as time))))is null,'00:00',
						time_format(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.week_duration as time)))),'%H:%i'))";
		
		$time_status = "IF(FIND_IN_SET('submitted',GROUP_CONCAT(DISTINCT week_status))!=0,'For Approval',
						IF(FIND_IN_SET('saved',GROUP_CONCAT(DISTINCT week_status))!=0,'Saved','No Entry'))";

		$selectEmpTimesheetsQuery = "SELECT ms.user_id AS empid, ms.userfullname AS empname,
									".$duration." AS duration, 
									".$time_status." AS time_status,et.ts_week,et.ts_year,et.ts_month
									 FROM main_employees_summary AS ms 
									 LEFT JOIN tm_project_employees AS pe ON ms.user_id = pe.emp_id AND pe.is_active=1";

		if($week != ""){
			$selectEmpTimesheetsQuery.=" LEFT JOIN tm_emp_timesheets AS et ON et.emp_id = ms.user_id and (et.project_id = pe.project_id or et.project_id IS NULL) and et.ts_year =".$year." and et.ts_month = ".$month." and et.ts_week =".$week;
		}else{
			$selectEmpTimesheetsQuery.=" LEFT JOIN tm_emp_timesheets AS et ON et.emp_id = ms.user_id and (et.project_id = pe.project_id or et.project_id IS NULL) and et.ts_year =".$year." and et.ts_month = ".$month;
		}

		$selectEmpTimesheetsQuery.=" LEFT JOIN ".$this->_ts_status." AS ts ON ts.emp_id = et.emp_id and (ts.project_id = et.project_id OR  ts.project_id IS NULL) and et.ts_year = ts.ts_year
									and et.ts_month = ts.ts_month and ts.ts_week = et.ts_week and ts.is_active = 1";
		
		if(trim($search) != ""){
			$where .= " AND (ms.userfullname LIKE ".$db->quote(trim('%'.trim($search).'%'))." ) ";
		}

		if($emp_list_flag != "admin"){
			if($emp_list_flag != "all"){
				$where .= " AND (ms.reporting_manager = ".$manager_id.")";
			}else{
				$where .= " AND (ms.reporting_manager = ".$manager_id; 
				$where .= " OR ms.reporting_manager in (select user_id from main_employees_summary where reporting_manager = ".$manager_id.")";
				$where .= " OR ms.reporting_manager in (select user_id from main_employees_summary where reporting_manager in (select user_id from main_employees_summary where reporting_manager = ".$manager_id.")))";
			}
		}
		

		$selectEmpTimesheetsQuery.=$where;
		$selectEmpTimesheetsQuery.=" GROUP BY ms.user_id";

		if($clicked_status != "all"){
			$selectEmpTimesheetsQuery.=" HAVING time_status=\"".$stat_arr[$clicked_status]."\" ";
		}
		$selectEmpTimesheetsQuery .= " LIMIT ".$current_index.",".$per_page;

		$res = $db->query($selectEmpTimesheetsQuery);
		while ($row=$res->fetch())
		{

			$fin_arr[$i]['emp_id']=$row['empid'];
			$fin_arr[$i]['emp_name']=$row['empname'];
			$fin_arr[$i]['duration']=$row['duration'];
			$fin_arr[$i]['time_status']=$row['time_status'];
			$fin_arr[$i]['ts_week']=$row['ts_week'];
			$fin_arr[$i]['ts_year']=$row['ts_year'];
			$fin_arr[$i]['ts_month']=$row['ts_month'];
			$fin_arr[$i]['proj_ids']="";
			$i++;
		}

		return $fin_arr;

	}

	function getEmployeeTimsheetDetails($year,$month,$week="",$user_id,$project_ids="",$emplistflag=""){
		

		$stat_arr=array('submitted'=>'For Approval','saved'=>'Saved');
		$fin_arr=array();
		$i=0;
		$where = "";
		$db = Zend_Db_Table::getDefaultAdapter();

		$duration = "IF(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.week_duration as time))))is null,'00:00',
						time_format(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.week_duration as time)))),'%H:%i'))";

		$time_status = "IF(FIND_IN_SET('submitted',GROUP_CONCAT(DISTINCT week_status))!=0,'For Approval',
						IF(FIND_IN_SET('saved',GROUP_CONCAT(DISTINCT week_status))!=0,'Saved','No Entry'))";
		if($emplistflag=="project")
		{
			$time_status = "
IF(FIND_IN_SET('submitted',GROUP_CONCAT(sun_project_status,',',mon_project_status,',',tue_project_status,',',wed_project_status,',',thu_project_status,',',fri_project_status,',',sat_project_status))!=0,'For Approval',
IF(FIND_IN_SET('saved',GROUP_CONCAT(sun_project_status,',',mon_project_status,',',tue_project_status,',',wed_project_status,',',thu_project_status,',',fri_project_status,',',sat_project_status))!=0,'Saved','No Entry'))";
		}				

		$selectEmpTimesheetsQuery = "SELECT ms.user_id AS empid,ms.reporting_manager_name, ms.userfullname AS empname,
									".$duration." AS duration, 
									".$time_status." AS time_status,et.ts_week,et.ts_year,et.ts_month
									 FROM main_employees_summary AS ms 
									 LEFT JOIN tm_project_employees AS pe ON ms.user_id = pe.emp_id and pe.is_active = 1";

		if($week != ""){
			$selectEmpTimesheetsQuery.=" LEFT JOIN tm_emp_timesheets AS et ON et.emp_id = pe.emp_id and  (et.project_id = pe.project_id or et.project_id is null) and et.ts_year =".$year." and et.ts_month = ".$month." and et.ts_week =".$week;
		}else{
			$selectEmpTimesheetsQuery.=" LEFT JOIN tm_emp_timesheets AS et ON et.emp_id = pe.emp_id and  (et.project_id = pe.project_id or et.project_id is null) and et.ts_year =".$year." and et.ts_month = ".$month;
		}

		$selectEmpTimesheetsQuery.=" LEFT JOIN ".$this->_ts_status." AS ts ON ts.emp_id = et.emp_id and (ts.project_id = et.project_id or ts.project_id is null) and et.ts_year = ts.ts_year
									and et.ts_month = ts.ts_month and ts.ts_week = et.ts_week";
			
		$where.= " WHERE (ms.isactive = 1) AND (ms.user_id = ".$user_id.")";
		
		if($project_ids != "")
			$where.=" AND pe.project_id IN (".$project_ids.")";
		
		$selectEmpTimesheetsQuery.=$where;
		$selectEmpTimesheetsQuery.=" GROUP BY ms.user_id";

		$res = $db->query($selectEmpTimesheetsQuery);
		while ($row=$res->fetch())
		{

			$fin_arr[$i]['emp_id']=$row['empid'];
			$fin_arr[$i]['emp_name']=$row['empname'];
			$fin_arr[$i]['reporting_manager']=$row['reporting_manager_name'];
			$fin_arr[$i]['duration']=$row['duration'];
			$fin_arr[$i]['time_status']=$row['time_status'];
			$fin_arr[$i]['ts_week']=$row['ts_week'];
			$fin_arr[$i]['ts_year']=$row['ts_year'];
			$fin_arr[$i]['ts_month']=$row['ts_month'];
			$i++;
		}

		return $fin_arr;

	}

	public function getEachDayTsDateCron($empid,$cal_week,$startDateYear,$month="")
	{
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('t' => $this->_ts_status),
						array('ts_week_dates' => "concat(concat(sun_date,'#',sun_status,'#','sun'),'$',
												concat(mon_date,'#',mon_status,'#','mon'),'$',
												concat(tue_date,'#',tue_status,'#','tue'),'$',
												concat(wed_date,'#',wed_status,'#','wed'),'$',
												concat(thu_date,'#',thu_status,'#','thu'),'$',
												concat(fri_date,'#',fri_status,'#','fri'),'$',
												concat(sat_date,'#',sat_status,'#','sat'))"))
						->where("t.emp_id=".$empid." AND t.is_active=1 AND t.cal_week = '".$cal_week."' AND t.ts_month='".$month."' AND t.ts_year = '".$startDateYear."' ")
						->group('t.emp_id');

		return $this->fetchAll($select)->toArray();
	}

	public function getEachDayTsDateCronWithProject($empid,$cal_week,$startDateYear,$month=""){
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('t' => $this->_ts_status),
							  array('project_id'=>'project_id',
						  			'ts_week_dates' => "concat(concat(sun_date,'#',sun_project_status,'#','sun'),'$',
													concat(mon_date,'#',mon_project_status,'#','mon'),'$',
													concat(tue_date,'#',tue_project_status,'#','tue'),'$',
													concat(wed_date,'#',wed_project_status,'#','wed'),'$',
													concat(thu_date,'#',thu_project_status,'#','thu'),'$',
													concat(fri_date,'#',fri_project_status,'#','fri'),'$',
													concat(sat_date,'#',sat_project_status,'#','sat'))"))
						->where("t.emp_id=".$empid." AND t.project_id IS NOT NULL AND t.is_active=1 AND t.cal_week = '".$cal_week."' AND t.ts_month='".$month."' AND t.ts_year = '".$startDateYear."' ");

		return $this->fetchAll($select)->toArray();
		
	}

	
	public function getEmp_from_summary($userid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$qry = "select e.* from main_employees_summary e where e.user_id = ".$userid." ";
		$res = $db->query($qry)->fetch();
		return $res;
	}
	
	public function getEmpProjects($userid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$qry = "select GROUP_CONCAT(project_id) as projects from tm_project_employees where is_active = 1 AND  emp_id = ".$userid." ";
		$res = $db->query($qry)->fetch();

		return $res['projects'];
	}
	
	public function getEmpProjectByDate($userid,$calweek,$month,$year,$day,$date)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$qry = "select  GROUP_CONCAT(project_id)   as projects from ".$this->_ts_status." where project_id IS NOT NULL AND is_active = 1 AND  emp_id = ".$userid." 
				AND ts_year=".$year." AND ts_month=".$month." AND cal_week = ".$calweek." AND ".$day." = '".$date."'";
		
		$res = $db->query($qry)->fetch();
		return $res['projects'];
	}
	

	
	public function getMonthTimesheetData($empId,$year,$month,$project_ids="",$emplistflag='') {
		$status = '_status';
		if($emplistflag == "project"){
			$status = '_project_status';
		}
		 $select = $this->select()
				  ->setIntegrityCheck(false)
				  ->from(array('et'=>$this->_name),array(
				  	'ts.ts_week,et.sun_date,et.mon_date,et.tue_date,et.wed_date,et.thu_date,et.fri_date,et.sat_date,
				  	if(et.sun_duration is null,"00:00",time_format(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.sun_duration as time)))),"%H:%i"))sun_duration,ifnull(ts.sun'.$status.',"no_entry")sun_status,
					if(et.mon_duration is null,"00:00",time_format(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.mon_duration as time)))),"%H:%i"))mon_duration,ifnull(ts.mon'.$status.',"no_entry")mon_status,
					if(et.tue_duration is null,"00:00",time_format(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.tue_duration as time)))),"%H:%i"))tue_duration,ifnull(ts.tue'.$status.',"no_entry")tue_status,
					if(et.wed_duration is null,"00:00",time_format(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.wed_duration as time)))),"%H:%i"))wed_duration,ifnull(ts.wed'.$status.',"no_entry")wed_status,
					if(et.thu_duration is null,"00:00",time_format(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.thu_duration as time)))),"%H:%i"))thu_duration,ifnull(ts.thu'.$status.',"no_entry")thu_status,
					if(et.fri_duration is null,"00:00",time_format(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.fri_duration as time)))),"%H:%i"))fri_duration,ifnull(ts.fri'.$status.',"no_entry")fri_status,
					if(et.sat_duration is null,"00:00",time_format(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.sat_duration as time)))),"%H:%i"))sat_duration,ifnull(ts.sat'.$status.',"no_entry")sat_status,
				  	if(et.week_duration is null,"00:00",time_format(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.week_duration as time)))),"%H:%i"))week_duration'
					));
			  if($project_ids != ""){
			  	$select->joinInner(array('ts'=> $this->_ts_status),'ts.emp_id = et.emp_id and et.ts_year= ts.ts_year and et.ts_month= ts.ts_month and et.ts_week = ts.ts_week and et.project_id = ts.project_id and et.project_id IN ('.$project_ids.')',array());
			  }else{
			 	 	$select->joinInner(array('ts'=> $this->_ts_status),'ts.emp_id = et.emp_id and et.ts_year= ts.ts_year and et.ts_month= ts.ts_month and et.ts_week = ts.ts_week and (et.project_id = ts.project_id OR ts.project_id IS NULL)',array());
			  }
			  $select->joinInner(array('en'=> 'tm_emp_ts_notes'),'en.emp_id = et.emp_id and et.ts_year= en.ts_year and et.ts_month= en.ts_month and et.ts_week = en.ts_week ',array());
	  	  	  $select->where("et.is_active=1 and ts.is_active=1 and et.ts_year = $year and  et.ts_month = $month and et.emp_id = $empId");
			  $select->group('ts.ts_week');

		return $this->fetchAll($select)->toArray();
	}
	
	public function getWeeklyTimesheetData($empId,$year,$month,$week,$project_ids="") { 
		$select = $this->select()
				  ->setIntegrityCheck(false)
				  ->from(array('pte'=>'tm_project_task_employees'),array(
				  	'pte.project_id','p.project_name','pte.project_task_id','t.task','sun_duration'=>'ifnull(et.sun_duration,0)','mon_duration'=>'ifnull(et.mon_duration,0)',
				  	'tue_duration'=>'ifnull(et.tue_duration,0)','wed_duration'=>'ifnull(et.wed_duration,0)','thu_duration'=>'ifnull(et.thu_duration,0)','fri_duration'=>'ifnull(et.fri_duration,0)','sat_duration'=>'ifnull(et.sat_duration,0)','week_duration'=>'ifnull(et.week_duration,0)'))
				  ->joinInner(array('pt'=>'tm_project_tasks'),'pt.id = pte.project_task_id',array())
				  ->joinInner(array('p'=>'tm_projects'),'p.id = pt.project_id and p.id = pte.project_id',array())
				  ->joinInner(array('t'=>'tm_tasks'),'t.id = pt.task_id',array())
		          ->joinInner(array('et'=>'tm_emp_timesheets'),'et.project_task_id = pte.project_task_id and et.ts_year='.$year.' and et.ts_month='.$month.' and et.ts_week = '.$week.' and et.emp_id= '.$empId,array());
		  $select->where("et.is_active=1 and pte.is_active=1 and pte.emp_id =".$empId); 
		  if($project_ids != ""){
		  	$select->where("p.id IN (".$project_ids.")");
		  }
		  $select->order('p.project_name');

		return $this->fetchAll($select)->toArray();
	}

	public function saveProcessNotes($emp_id,$date,$user_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$insertProcessUpdates = array(
				'emp_id' => $emp_id,
				'ts_dates' => $date,
				'action_type' => 'approved',
				'alert' => 1,
				'action_by' => $user_id,
				'created' => gmdate("Y-m-d H:i:s")
		);
		$db->insert('tm_process_updates',$insertProcessUpdates);
		return $db->lastInsertId();
	}
}