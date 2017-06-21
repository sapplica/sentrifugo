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
		$stat_arr=array('enabled'=>'Enabled','blocked'=>'Blocked','rejected'=>'Rejected','submitted'=>'For Approval','approved'=>'Approved','saved'=>'Saved');
		$fin_arr=array();
		$i=0;
		$where = " WHERE (ms.isactive = 1 AND pe.is_active=1 ) ";
		$per_page = 8;
		$current_index = ($current_page - 1) * $per_page;
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$duration = "IF(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.week_duration as time))))is null,'00:00',
						time_format(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.week_duration as time)))),'%H:%i'))";

		$time_status = "IF(FIND_IN_SET('blocked',GROUP_CONCAT(DISTINCT week_status))!=0,'Blocked',
						IF(FIND_IN_SET('submitted',GROUP_CONCAT(DISTINCT week_status))!=0,'For Approval',						
						IF(FIND_IN_SET('rejected',GROUP_CONCAT(DISTINCT week_status))!=0,'Rejected',
						IF(FIND_IN_SET('approved',GROUP_CONCAT(DISTINCT week_status))!=0,'Approved',
						IF(FIND_IN_SET('saved',GROUP_CONCAT(DISTINCT week_status))!=0,'Saved',
						IF(FIND_IN_SET('enabled',GROUP_CONCAT(DISTINCT week_status))!=0,'Enabled','No Entry'))))))";
		if($emp_list_flag=="project")
		{
			$time_status = "IF(FIND_IN_SET('blocked',GROUP_CONCAT(sun_project_status,',',mon_project_status,',',tue_project_status,',',wed_project_status,',',thu_project_status,',',fri_project_status,',',sat_project_status))!=0,'Blocked',
IF(FIND_IN_SET('rejected',GROUP_CONCAT(sun_project_status,',',mon_project_status,',',tue_project_status,',',wed_project_status,',',thu_project_status,',',fri_project_status,',',sat_project_status))!=0,'Rejected',
IF(FIND_IN_SET('submitted',GROUP_CONCAT(sun_project_status,',',mon_project_status,',',tue_project_status,',',wed_project_status,',',thu_project_status,',',fri_project_status,',',sat_project_status))!=0,'For Approval',
IF(FIND_IN_SET('approved',GROUP_CONCAT(sun_project_status,',',mon_project_status,',',tue_project_status,',',wed_project_status,',',thu_project_status,',',fri_project_status,',',sat_project_status))!=0,'Approved',
IF(FIND_IN_SET('saved',GROUP_CONCAT(sun_project_status,',',mon_project_status,',',tue_project_status,',',wed_project_status,',',thu_project_status,',',fri_project_status,',',sat_project_status))!=0,'Saved',
IF(FIND_IN_SET('enabled',GROUP_CONCAT(sun_project_status,',',mon_project_status,',',tue_project_status,',',wed_project_status,',',thu_project_status,',',fri_project_status,',',sat_project_status))!=0,'Enabled','No Entry'))))))";
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
		//echo $selectEmpTimesheetsQuery;exit;
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
		$stat_arr=array('enabled'=>'Enabled','blocked'=>'Blocked','rejected'=>'Rejected','submitted'=>'For Approval','approved'=>'Approved','saved'=>'Saved');
		$fin_arr=array();
		$i=0;
		$where = " WHERE (ms.isactive = 1) ";
		$per_page = 8;
		$current_index = ($current_page - 1) * $per_page;
		$db = Zend_Db_Table::getDefaultAdapter();

		$duration = "IF(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.week_duration as time))))is null,'00:00',
						time_format(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.week_duration as time)))),'%H:%i'))";
		
		$time_status = "
						IF(FIND_IN_SET('blocked',GROUP_CONCAT(DISTINCT week_status))!=0,'Blocked',
						IF(FIND_IN_SET('submitted',GROUP_CONCAT(DISTINCT week_status))!=0,'For Approval',
						IF(FIND_IN_SET('rejected',GROUP_CONCAT(DISTINCT week_status))!=0,'Rejected',						
						IF(FIND_IN_SET('approved',GROUP_CONCAT(DISTINCT week_status))!=0,'Approved',
						IF(FIND_IN_SET('saved',GROUP_CONCAT(DISTINCT week_status))!=0,'Saved',
						IF(FIND_IN_SET('enabled',GROUP_CONCAT(DISTINCT week_status))!=0,'Enabled','No Entry'))))))";

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
		//echo $selectEmpTimesheetsQuery;//exit;
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
		

		$stat_arr=array('enabled'=>'Enabled','blocked'=>'Blocked','rejected'=>'Rejected','submitted'=>'For Approval','approved'=>'Approved','saved'=>'Saved');
		$fin_arr=array();
		$i=0;
		$where = "";
		$db = Zend_Db_Table::getDefaultAdapter();

		$duration = "IF(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.week_duration as time))))is null,'00:00',
						time_format(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.week_duration as time)))),'%H:%i'))";

		$time_status = "IF(FIND_IN_SET('blocked',GROUP_CONCAT(DISTINCT week_status))!=0,'Blocked',
						IF(FIND_IN_SET('submitted',GROUP_CONCAT(DISTINCT week_status))!=0,'For Approval',						
						IF(FIND_IN_SET('rejected',GROUP_CONCAT(DISTINCT week_status))!=0,'Rejected',
						IF(FIND_IN_SET('approved',GROUP_CONCAT(DISTINCT week_status))!=0,'Approved',
						IF(FIND_IN_SET('saved',GROUP_CONCAT(DISTINCT week_status))!=0,'Saved',
						IF(FIND_IN_SET('enabled',GROUP_CONCAT(DISTINCT week_status))!=0,'Enabled','No Entry'))))))";
		if($emplistflag=="project")
		{
			$time_status = "IF(FIND_IN_SET('blocked',GROUP_CONCAT(sun_project_status,',',mon_project_status,',',tue_project_status,',',wed_project_status,',',thu_project_status,',',fri_project_status,',',sat_project_status))!=0,'Blocked',
IF(FIND_IN_SET('rejected',GROUP_CONCAT(sun_project_status,',',mon_project_status,',',tue_project_status,',',wed_project_status,',',thu_project_status,',',fri_project_status,',',sat_project_status))!=0,'Rejected',
IF(FIND_IN_SET('submitted',GROUP_CONCAT(sun_project_status,',',mon_project_status,',',tue_project_status,',',wed_project_status,',',thu_project_status,',',fri_project_status,',',sat_project_status))!=0,'For Approval',
IF(FIND_IN_SET('approved',GROUP_CONCAT(sun_project_status,',',mon_project_status,',',tue_project_status,',',wed_project_status,',',thu_project_status,',',fri_project_status,',',sat_project_status))!=0,'Approved',
IF(FIND_IN_SET('saved',GROUP_CONCAT(sun_project_status,',',mon_project_status,',',tue_project_status,',',wed_project_status,',',thu_project_status,',',fri_project_status,',',sat_project_status))!=0,'Saved',
IF(FIND_IN_SET('enabled',GROUP_CONCAT(sun_project_status,',',mon_project_status,',',tue_project_status,',',wed_project_status,',',thu_project_status,',',fri_project_status,',',sat_project_status))!=0,'Enabled','No Entry'))))))";
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
		//echo $selectEmpTimesheetsQuery;//exit;
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
						//array('ts_week_dates' => "concat(concat(sun_date,'#',sun_status),'$',concat(mon_date,'#',mon_status),'$',concat(tue_date,'#',tue_status),'$',concat(wed_date,'#',wed_status),'$',concat(thu_date,'#',thu_status),'$',concat(fri_date,'#',fri_status),'$',concat(sat_date,'#',sat_status))"))
						array('ts_week_dates' => "concat(concat(sun_date,'#',sun_status,'#','sun'),'$',
												concat(mon_date,'#',mon_status,'#','mon'),'$',
												concat(tue_date,'#',tue_status,'#','tue'),'$',
												concat(wed_date,'#',wed_status,'#','wed'),'$',
												concat(thu_date,'#',thu_status,'#','thu'),'$',
												concat(fri_date,'#',fri_status,'#','fri'),'$',
												concat(sat_date,'#',sat_status,'#','sat'))"))
						->where("t.emp_id=".$empid." AND t.is_active=1 AND t.cal_week = '".$cal_week."' AND t.ts_month='".$month."' AND t.ts_year = '".$startDateYear."' ")
						->group('t.emp_id');
						//echo $select;exit();
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
		//echo $select;exit();
		return $this->fetchAll($select)->toArray();
		
	}
	
	function updateEmployeeTimesheet($emp_id="",$year="",$month="",$lastday="",$calenderWeekArray="",$flag="",$rejectNote="",$emplistflag){

		$db = Zend_Db_Table::getDefaultAdapter();
		$month_start_date = $year."-".$month."-"."01";
		$month_end_date = $year."-".$month."-".$lastday;
		try{
			$db->beginTransaction();
			$monthDatesArray = sapp_Global::createDateRangeArray($month_start_date,$month_end_date);
			for($i=0; $i<sizeof($calenderWeekArray); $i++){
				if($flag == "reject"){
					$this->rejectEmployeeTimesheetStatus($emp_id,$calenderWeekArray[$i],$year,$monthDatesArray,$rejectNote,$month,$emplistflag);	
				}else{
					$this->updateEmployeeTimesheetStatus($emp_id,$calenderWeekArray[$i],$year,$flag,$monthDatesArray,$month,$emplistflag);	
				}
				
			}
			// If all succeed, commit the transaction and all changes
			// are committed at once.
			$db->commit();
			return TRUE;
		}catch(Exception $e){
			// If any of the queries failed and threw an exception,
			// we want to roll back the whole transaction, reversing
			// changes made in the transaction, even those that succeeded.
			// Thus all changes are committed together, or none are.
			$db->rollBack();
			echo $e->getMessage();
			return FALSE;
		}
	}

	function rejectEmployeeTimesheetStatus($emp_id="", $calweek="", $year="", $datesArr="",$rejectNote="",$month="",$emplistflag){
		$resultData = array();
		$ts_filled_dates = array();
		$db = Zend_Db_Table::getDefaultAdapter();
		$isprojectManager = false;
		$conditionCheck = "submitted";
		$statusUpdate = "rejected";
		$storage = new Zend_Auth_Storage_Session();
		$data = $storage->read();
		
		/*$getEmpDetails = $this->getEmp_from_summary($emp_id);
		if($data->id !== $getEmpDetails['reporting_manager'] ){
			$isprojectManager = true;
		}*/
			
		if($emplistflag == "project"){
			$isprojectManager = true;
		}
		
		if($isprojectManager){
			// for manager
			$projects = $this->getEmpProjects($data->id);
			$projects = explode(',',$projects);
			if(sizeof($projects) > 0){
				$resultData = $this->getEachDayTsDateCronWithProject($emp_id,$calweek,$year,$month);
				if(sizeof($resultData) > 0){
					foreach ($resultData as $tmpresultData){
						$updateArray = array();
						$updateRejProjStatus = array();
						$updateStatus = array();
						$ts_week_dates = $tmpresultData['ts_week_dates'];
						$project_id = $tmpresultData['project_id'];
						$ts_weekArray = explode('$',$ts_week_dates);
						$curDate = gmdate("Y-m-d H:i:s");
						if(count($ts_weekArray) > 0){
							foreach($ts_weekArray as $ts_day){
								$ts_day_Array = explode('#',$ts_day);
								if(in_array($ts_day_Array[0],$datesArr)){
									if($ts_day_Array[1] == $conditionCheck){
										if(!empty($project_id) && $project_id != 'NULL' && in_array($project_id,$projects)){
											$updateArray[$ts_day_Array[2].'_project_status'] = $statusUpdate;
											$updateArray[$ts_day_Array[2].'_reject_note'] = $rejectNote;
											$updateStatus[$ts_day_Array[2].'_status'] = $statusUpdate;
											$updateArray[$ts_day_Array[2].'_status_date'] = $curDate;
										}
									}
								}
							}
						}
						if(!empty($project_id)){
							/*$where = array();
							$where['emp_id']=$emp_id;
							$where['ts_year']=$year;
							$where['cal_week']=$calweek;
							$where['project_id']=$project_id;
							$where['ts_month']=$month;*/
							$whereStmt = " project_id = ".(int)$project_id." AND emp_id = $emp_id AND ts_year = $year AND ts_month =".(int)$month." AND cal_week = $calweek";
							if(sizeof($updateArray) > 0){
								//print_r($updateArray);
								$result = $db->update($this->_ts_status, $updateArray ,$whereStmt);
								
								$updateStatusArray = array();
								$getDayStatusForEmp = $this->getEmpDayStatusByProjectStatusForWeek($emp_id,$month, $calweek, $year);
								foreach($getDayStatusForEmp as $key => $value){
									if(isset($updateStatus[$key])){
										$updateStatusArray[$key] = $value;	
									}
								}
								/*$statusWhere = array();
								$statusWhere['emp_id']=$emp_id;
								$statusWhere['ts_year']=$year;
								$statusWhere['cal_week']=$calweek;
								$statusWhere['ts_month']=$month;*/
								$statusWhere = " emp_id = $emp_id AND ts_year = $year AND ts_month =".(int)$month." AND cal_week = $calweek";
								//echo $statusWhere;
									
								$db->update($this->_ts_status, $updateStatusArray ,$statusWhere);
								
								$weekStatus = $this->getEmpWeekStatus($emp_id,$calweek,$year,$month);
								$week_status = $weekStatus['week_status'];
								$db->update($this->_ts_status, array('week_status' => $week_status) ,$statusWhere);
								
							}	
						}
					}
				}
			}
			
		}else{
			$updateArray = array();
			$updateRejProjStatus = array();
			$resultData = $this->getEachDayTsDateCron($emp_id,$calweek,$year,$month);
			if(!empty($resultData[0])){
				$ts_week_dates = $resultData[0]['ts_week_dates'];
				$ts_weekArray = explode('$',$ts_week_dates);
				if(count($ts_weekArray) > 0){
					foreach($ts_weekArray as $ts_day){
						$ts_day_Array = explode('#',$ts_day);
						if(in_array($ts_day_Array[0],$datesArr)){
							if($ts_day_Array[1] == $conditionCheck){
								$updateArray[$ts_day_Array[2].'_status'] = $statusUpdate;
								$updateArray[$ts_day_Array[2].'_project_status'] = $statusUpdate;
								$updateArray[$ts_day_Array[2].'_status_date'] = gmdate("Y-m-d H:i:s");
								$updateRejProjStatus[$ts_day_Array[2].'_reject_note'] = $rejectNote;
							}
						}
					}
					$whereStmt = "emp_id = $emp_id AND ts_year = $year AND ts_month =".(int)$month." AND cal_week = $calweek";
					if(sizeof($updateArray) > 0){
						$db->update($this->_ts_status, $updateArray ,$whereStmt);
						$weekStatus = $this->getEmpWeekStatus($emp_id,$calweek,$year,$month);
						$week_status = $weekStatus['week_status'];
						$db->update($this->_ts_status, array('week_status' => $week_status) ,$whereStmt);
						if(sizeof($updateRejProjStatus) > 0){
							$db->update('tm_emp_ts_notes', $updateRejProjStatus ,$whereStmt);
						}
					}
				}
			}
		}
		
	}
	
	function updateEmployeeTimesheetStatus($emp_id="", $calweek="", $year="", $flag="", $datesArr="",$month="",$emplistflag){

		$resultData = array();
		$ts_filled_dates = array();
		$conditionCheck = "";
		$statusUpdate = "";
		$db = Zend_Db_Table::getDefaultAdapter();
		$isprojectManager = false;
		if($flag=="enable"){
			$conditionCheck = "blocked";
			$statusUpdate = "enabled";
		}

		if($flag=="approve"){
			$conditionCheck = "submitted";
			$statusUpdate = "approved";
		}
		
		$storage = new Zend_Auth_Storage_Session();
		$data = $storage->read();
				
		if($emplistflag == "project"){
			$isprojectManager = true;
		}
			
		if($isprojectManager){
			// for manager
			$projects = $this->getEmpProjects($data->id);
			$projects = explode(',',$projects);
			if(sizeof($projects) > 0){
				$resultData = $this->getEachDayTsDateCronWithProject($emp_id,$calweek,$year,$month);
				if(sizeof($resultData) > 0){
					foreach ($resultData as $tmpresultData){
						$updateArray = array();
						$updateStatus = array();
						$approvedDates = "";
						$ts_week_dates = $tmpresultData['ts_week_dates'];
						$project_id = $tmpresultData['project_id'];
						$ts_weekArray = explode('$',$ts_week_dates);
						if(count($ts_weekArray) > 0){
							foreach($ts_weekArray as $ts_day){
								$ts_day_Array = explode('#',$ts_day);
								if(in_array($ts_day_Array[0],$datesArr)){
									if($ts_day_Array[1] == $conditionCheck){
										if($project_id != 'NULL' && in_array($project_id,$projects)){
											$updateStatus[$ts_day_Array[2].'_status'] = $statusUpdate;
											//$updateArray[$ts_day_Array[2].'_project_status'] = $statusUpdate;
											$updateArray[$ts_day_Array[2].'_status_date'] = gmdate("Y-m-d H:i:s");
											if($statusUpdate == "approved"){
												$updateArray[$ts_day_Array[2].'_project_status'] = $statusUpdate;
												$approvedDates.= $ts_day_Array[0].",";
											}
										}
									}
								}
							}
						}
						
						if(!empty($project_id) && in_array($project_id,$projects)){
							$whereStmt = " project_id = ".(int)$project_id." AND emp_id = $emp_id AND ts_year = $year AND ts_month =".(int)$month." AND cal_week = $calweek";
							if(sizeof($updateArray) > 0){
								$result = $db->update($this->_ts_status, $updateArray ,$whereStmt);
								if($result > 0){
									$updateStatusArray = array();
									$getDayStatusForEmp = $this->getEmpDayStatusByProjectStatusForWeek($emp_id,$month, $calweek, $year);
									foreach($getDayStatusForEmp as $key => $value){
										if(isset($updateStatus[$key])){
											$updateStatusArray[$key] = $value;	
										}
									}
									//print_r($updateStatusArray);
									$statusWhere = " emp_id = $emp_id AND ts_year = $year AND ts_month =".(int)$month." AND cal_week = $calweek";
									//echo $statusWhere;
									$db->update($this->_ts_status, $updateStatusArray ,$statusWhere);
																	
									$weekStatus = $this->getEmpWeekStatus($emp_id,$calweek,$year,$month);
									$week_status = $weekStatus['week_status'];
									$db->update($this->_ts_status, array('week_status' => $week_status) ,$statusWhere);
									
									if($statusUpdate == "approved" && $approvedDates != ""){
										$this->saveProcessNotes($emp_id,$approvedDates,$data->id);
										/*$insertProcessUpdates = array(
												'emp_id' => $emp_id,
												'ts_dates' => $approvedDates,
												'action_type' => 'approved',
												'alert' => 1,
												'action_by' => $data->id,
												'created' => gmdate("Y-m-d H:i:s")
										);
										//print_r($insertProcessUpdates);
										$db->insert('tm_process_updates',$insertProcessUpdates);*/
									}
								}
							}	
						}
					}
				}
			}
		}else{
			$updateArray = array();
			$approvedDates = "";
			$resultData = $this->getEachDayTsDateCron($emp_id,$calweek,$year,$month);
			if(!empty($resultData[0])){
				$ts_week_dates = $resultData[0]['ts_week_dates'];
				$ts_weekArray = explode('$',$ts_week_dates);
				if(count($ts_weekArray) > 0){
					foreach($ts_weekArray as $ts_day){
						$ts_day_Array = explode('#',$ts_day);
						if(in_array($ts_day_Array[0],$datesArr)){
							if($ts_day_Array[1] == $conditionCheck){
								$updateArray[$ts_day_Array[2].'_status'] = $statusUpdate;
								//$updateArray[$ts_day_Array[2].'_project_status'] = $statusUpdate;
								$updateArray[$ts_day_Array[2].'_status_date'] = gmdate("Y-m-d H:i:s");
								if($statusUpdate == "approved"){
									$updateArray[$ts_day_Array[2].'_project_status'] = $statusUpdate;
									$approvedDates.= $ts_day_Array[0].",";
								}
							}
						}
					}
					$whereStmt = "emp_id = $emp_id AND ts_year = $year AND ts_month =".(int)$month." AND cal_week = $calweek";
					if(sizeof($updateArray) > 0){
						$db->update($this->_ts_status, $updateArray ,$whereStmt);
						$weekStatus = $this->getEmpWeekStatus($emp_id,$calweek,$year,$month);
						$week_status = $weekStatus['week_status'];
						$db->update($this->_ts_status, array('week_status' => $week_status) ,$whereStmt);
						if($approvedDates != ""){
							$this->saveProcessNotes($emp_id,$approvedDates,$data->id);
							/*$insertProcessUpdates = array(
									'emp_id' => $emp_id,
									'ts_dates' => $approvedDates,
									'action_type' => 'approved',
									'alert' => 1,
									'action_by' => $data->id,
									'created' => gmdate("Y-m-d H:i:s")
							);
							$db->insert('tm_process_updates',$insertProcessUpdates);*/
						}
							
					}
				}
			}
		}
	
	}

	function getEmpDayStatusByProjectStatus($emp_id="",$month="", $calweek="", $year="",$project_status="",$day_status="",$day_date_column ="", $day_date=""){
		
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('t' => $this->_ts_status),
						array("$day_status" => "IF(FIND_IN_SET('blocked',GROUP_CONCAT(DISTINCT ".$project_status."))!=0,'blocked',
												IF(FIND_IN_SET('saved',GROUP_CONCAT(DISTINCT ".$project_status."))!=0,'saved',
											    IF(FIND_IN_SET('rejected',GROUP_CONCAT(DISTINCT ".$project_status."))!=0,'rejected',
												IF(FIND_IN_SET('submitted',GROUP_CONCAT(DISTINCT ".$project_status."))!=0,'submitted',
											    IF(FIND_IN_SET('approved',GROUP_CONCAT(DISTINCT ".$project_status."))!=0,'approved',											    
											    IF(FIND_IN_SET('enabled',GROUP_CONCAT(DISTINCT ".$project_status."))!=0,'enabled','No Entry'))))))
												"))
						->where("emp_id=".$emp_id." AND ts_month=".$month." AND project_id IS NOT NULL  
								AND is_active=1 AND cal_week = '".$calweek."' AND ts_year = '".$year."' AND ".$day_date_column." = '".$day_date."'");
		$result = $this->fetchAll($select)->toArray();
		return $result[0];
		
	}
	
	function getEmpDayStatusByProjectStatusForWeek($emp_id="",$month="", $calweek="", $year=""){
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('t' => $this->_ts_status),
							  array("mon_status" => " IF(FIND_IN_SET('blocked',GROUP_CONCAT(DISTINCT mon_project_status))!=0,'blocked',
							  						IF(FIND_IN_SET('saved',GROUP_CONCAT(DISTINCT mon_project_status))!=0,'saved',
												    IF(FIND_IN_SET('rejected',GROUP_CONCAT(DISTINCT mon_project_status))!=0,'rejected',
												    IF(FIND_IN_SET('submitted',GROUP_CONCAT(DISTINCT mon_project_status))!=0,'submitted',
												    IF(FIND_IN_SET('approved',GROUP_CONCAT(DISTINCT mon_project_status))!=0,'approved',												    
												    IF(FIND_IN_SET('enabled',GROUP_CONCAT(DISTINCT mon_project_status))!=0,'enabled','no_entry'))))))",
							  
							  		"tue_status" => "IF(FIND_IN_SET('blocked',GROUP_CONCAT(DISTINCT tue_project_status))!=0,'blocked',
							  						IF(FIND_IN_SET('saved',GROUP_CONCAT(DISTINCT tue_project_status))!=0,'saved',
												    IF(FIND_IN_SET('rejected',GROUP_CONCAT(DISTINCT tue_project_status))!=0,'rejected',
												    IF(FIND_IN_SET('submitted',GROUP_CONCAT(DISTINCT tue_project_status))!=0,'submitted',
												    IF(FIND_IN_SET('approved',GROUP_CONCAT(DISTINCT tue_project_status))!=0,'approved',												    
												    IF(FIND_IN_SET('enabled',GROUP_CONCAT(DISTINCT tue_project_status))!=0,'enabled','no_entry'))))))",
							  
									"wed_status" => "IF(FIND_IN_SET('blocked',GROUP_CONCAT(DISTINCT wed_project_status))!=0,'blocked',
													IF(FIND_IN_SET('saved',GROUP_CONCAT(DISTINCT wed_project_status))!=0,'saved',
												    IF(FIND_IN_SET('rejected',GROUP_CONCAT(DISTINCT wed_project_status))!=0,'rejected',
												    IF(FIND_IN_SET('submitted',GROUP_CONCAT(DISTINCT wed_project_status))!=0,'submitted',
												    IF(FIND_IN_SET('approved',GROUP_CONCAT(DISTINCT wed_project_status))!=0,'approved',												    
												    IF(FIND_IN_SET('enabled',GROUP_CONCAT(DISTINCT wed_project_status))!=0,'enabled','no_entry'))))))",
							  
									"thu_status" => "IF(FIND_IN_SET('blocked',GROUP_CONCAT(DISTINCT thu_project_status))!=0,'blocked',
													IF(FIND_IN_SET('saved',GROUP_CONCAT(DISTINCT thu_project_status))!=0,'saved',
												    IF(FIND_IN_SET('rejected',GROUP_CONCAT(DISTINCT thu_project_status))!=0,'rejected',
												    IF(FIND_IN_SET('submitted',GROUP_CONCAT(DISTINCT thu_project_status))!=0,'submitted',
												    IF(FIND_IN_SET('approved',GROUP_CONCAT(DISTINCT thu_project_status))!=0,'approved',												    
												    IF(FIND_IN_SET('enabled',GROUP_CONCAT(DISTINCT thu_project_status))!=0,'enabled','no_entry'))))))",
							  
									"fri_status" => "IF(FIND_IN_SET('blocked',GROUP_CONCAT(DISTINCT fri_project_status))!=0,'blocked',
													IF(FIND_IN_SET('saved',GROUP_CONCAT(DISTINCT fri_project_status))!=0,'saved',
												    IF(FIND_IN_SET('rejected',GROUP_CONCAT(DISTINCT fri_project_status))!=0,'rejected',
												    IF(FIND_IN_SET('submitted',GROUP_CONCAT(DISTINCT fri_project_status))!=0,'submitted',
												    IF(FIND_IN_SET('approved',GROUP_CONCAT(DISTINCT fri_project_status))!=0,'approved',												    
												    IF(FIND_IN_SET('enabled',GROUP_CONCAT(DISTINCT fri_project_status))!=0,'enabled','no_entry'))))))",
								    
									"sat_status" => "IF(FIND_IN_SET('blocked',GROUP_CONCAT(DISTINCT sat_project_status))!=0,'blocked',
													IF(FIND_IN_SET('saved',GROUP_CONCAT(DISTINCT sat_project_status))!=0,'saved',
												    IF(FIND_IN_SET('rejected',GROUP_CONCAT(DISTINCT sat_project_status))!=0,'rejected',
												    IF(FIND_IN_SET('submitted',GROUP_CONCAT(DISTINCT sat_project_status))!=0,'submitted',
												    IF(FIND_IN_SET('approved',GROUP_CONCAT(DISTINCT sat_project_status))!=0,'approved',												    
												    IF(FIND_IN_SET('enabled',GROUP_CONCAT(DISTINCT sat_project_status))!=0,'enabled','no_entry'))))))",
									
									"sun_status" => "IF(FIND_IN_SET('blocked',GROUP_CONCAT(DISTINCT sun_project_status))!=0,'blocked',
													IF(FIND_IN_SET('saved',GROUP_CONCAT(DISTINCT sun_project_status))!=0,'saved',
												    IF(FIND_IN_SET('rejected',GROUP_CONCAT(DISTINCT sun_project_status))!=0,'rejected',
												    IF(FIND_IN_SET('submitted',GROUP_CONCAT(DISTINCT sun_project_status))!=0,'submitted',
												    IF(FIND_IN_SET('approved',GROUP_CONCAT(DISTINCT sun_project_status))!=0,'approved',												    
												    IF(FIND_IN_SET('enabled',GROUP_CONCAT(DISTINCT sun_project_status))!=0,'enabled','no_entry'))))))"
							  )
						)
						->where("emp_id=".$emp_id." AND ts_month=".$month." AND project_id IS NOT NULL  
								AND is_active=1 AND cal_week = '".$calweek."' AND ts_year =".$year);
		$result = $this->fetchAll($select)->toArray();
		return $result[0];
	}
	
	
	function getEmpWeekStatus($emp_id="", $calweek="", $year="", $month=""){

		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('t' => $this->_ts_status),
						array('week_status' => "IF(FIND_IN_SET('blocked',CONCAT(sun_status,',',mon_status,',',tue_status,',',wed_status,',',thu_status,',',fri_status,',',sat_status))!=0,'blocked',
											   IF(FIND_IN_SET('submitted',CONCAT(sun_status,',',mon_status,',',tue_status,',',wed_status,',',thu_status,',',fri_status,',',sat_status))!=0,'submitted',
											   IF(FIND_IN_SET('rejected',CONCAT(sun_status,',',mon_status,',',tue_status,',',wed_status,',',thu_status,',',fri_status,',',sat_status))!=0,'rejected',
											   IF(FIND_IN_SET('saved',CONCAT(sun_status,',',mon_status,',',tue_status,',',wed_status,',',thu_status,',',fri_status,',',sat_status))!=0,'saved',											   
											   IF(FIND_IN_SET('approved',CONCAT(sun_status,',',mon_status,',',tue_status,',',wed_status,',',thu_status,',',fri_status,',',sat_status))!=0,'approved',											   
											   IF(FIND_IN_SET('enabled',CONCAT(sun_status,',',mon_status,',',tue_status,',',wed_status,',',thu_status,',',fri_status,',',sat_status))!=0,'enabled','no_entry'))))))
												"))
						->where("emp_id=".$emp_id." AND is_active=1 AND ts_month=".$month." AND cal_week = '".$calweek."' AND ts_year = '".$year."' ")
						->group('emp_id');
						//echo $select;
		$result = $this->fetchAll($select)->toArray();
		return $result[0];

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
		//print_r($res);
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
	
	public function updateEmployeeDayTimesheet($emp_id, $calweek, $year, $month, $day, $date, $flag, $rejectNote,$emplistflag){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$profiler = $db->getProfiler();
		$resultData = array();
		$ts_filled_dates = array();
		$conditionCheck = "";
		$statusUpdate = "";
		$isprojectManager = false;
		try{
			$db->beginTransaction();
		
			if($flag=="reject"){
				$conditionCheck = "submitted";
				$statusUpdate = "rejected";
			}
	
			if($flag=="approve"){
				$conditionCheck = "submitted";
				$statusUpdate = "approved";
			}
			
			//echo "Calender Week : $calweek";
			$storage = new Zend_Auth_Storage_Session();
			$data = $storage->read();
			
			if($emplistflag == "project"){
				$isprojectManager = true;
			}
			
			if($isprojectManager){
				// for manager
				$projects = $this->getEmpProjects($data->id);
				$projects = explode(',',$projects);
				if(sizeof($projects) > 0){
					$weekDay = $day.'_date';
					//for employee
					$project_ids = $this->getEmpProjectByDate($emp_id,$calweek,$month,$year,$weekDay,$date);
					$project_ids = explode(',',$project_ids);
					if(sizeof($project_ids)> 0){
						for($i=0; $i<sizeof($project_ids); $i++){
							if(in_array($project_ids[$i],$projects)){
								$updateArray = array();
								$updateRejProjStatus = array();
								
								$updateArray[$day.'_project_status'] = $statusUpdate;
								$updateArray[$day.'_status_date'] = gmdate("Y-m-d H:i:s");
								if($flag=="reject"){
									$updateArray[$day.'_reject_note'] = $rejectNote;
								}
								
								$whereStmt = " project_id = ".(int)$project_ids[$i]." AND emp_id = $emp_id AND ts_year = $year AND ts_month =".(int)$month." AND cal_week = $calweek AND ".$day."_date = '".$date."'";
								//print_r($updateArray);
								//echo $whereStmt;
								if(sizeof($updateArray) > 0){
									$result = $db->update($this->_ts_status, $updateArray ,$whereStmt);
									$day_status = $this->getEmpDayStatusByProjectStatus($emp_id,$month,$calweek,$year,$day.'_project_status',$day.'_status',$day.'_date', $date);
									$update_day_status_Array = array(
										$day.'_status'	=>$day_status[$day.'_status']
									);
									$where_day_status = " emp_id = $emp_id AND ts_year = $year AND ts_month =".(int)$month." AND cal_week = $calweek AND ".$day."_date = '".$date."'";
									//print_r($update_day_status_Array);
									//echo $where_day_status;
									$db->update($this->_ts_status, $update_day_status_Array ,$where_day_status);
									
									$where_week_status_Array = " emp_id = $emp_id AND ts_year = $year AND ts_month =".(int)$month." AND cal_week = $calweek";
									$weekStatus = $this->getEmpWeekStatus($emp_id,$calweek,$year,$month);
									$week_status = $weekStatus['week_status'];
									$db->update($this->_ts_status, array('week_status' => $week_status) ,$where_week_status_Array);
									
									if($flag=="approve"){
										
										$this->saveProcessNotes($emp_id,$date,$data->id);
										/*$insertProcessUpdates = array(
												'emp_id' => $emp_id,
												'ts_dates' => $date,
												'action_type' => 'approved',
												'alert' => 1,
												'action_by' => $data->id,
												'created' => gmdate("Y-m-d H:i:s")
										);
										$db->insert('tm_process_updates',$insertProcessUpdates);*/
									}
								}
					
							}
						}
					}
				}
			}else{
				$updateArray = array();
				$updateRejProjStatus = array();
				$updateArray[$day.'_status'] = $statusUpdate;
				$updateArray[$day.'_project_status'] = $statusUpdate;
				$updateArray[$day.'_status_date'] = gmdate("Y-m-d H:i:s");
				if($flag=="reject"){
					$updateRejProjStatus[$day.'_reject_note'] = $rejectNote;
				}
				$whereStmt = "emp_id = $emp_id AND ts_year = $year AND ts_month =".(int)$month." AND cal_week = $calweek AND ".$day."_date = '".$date."'";
				if(sizeof($updateArray) > 0){
					$db->update($this->_ts_status, $updateArray ,$whereStmt);
					$weekStatus = $this->getEmpWeekStatus($emp_id,$calweek,$year,$month);
					$week_status = $weekStatus['week_status'];
					$db->update($this->_ts_status, array('week_status' => $week_status) ,$whereStmt);
				}
				if($flag=="approve"){
					
					$this->saveProcessNotes($emp_id,$date,$data->id);
					/*$insertProcessUpdates = array(
							'emp_id' => $emp_id,
							'ts_dates' => $date,
							'action_type' => 'approved',
							'alert' => 1,
							'action_by' => $data->id,
							'created' => gmdate("Y-m-d H:i:s")
					);
					$db->insert('tm_process_updates',$insertProcessUpdates);*/
				}
				if(sizeof($updateRejProjStatus) > 0){
					$db->update('tm_emp_ts_notes', $updateRejProjStatus ,$whereStmt);
				}
			}
			
			// If all succeed, commit the transaction and all changes
			// are committed at once.
			$db->commit();
			return TRUE;
		}catch(Exception $e){
			// If any of the queries failed and threw an exception,
			// we want to roll back the whole transaction, reversing
			// changes made in the transaction, even those that succeeded.
			// Thus all changes are committed together, or none are.
			$db->rollBack();
			echo $e->getMessage();
			return FALSE;
		}
		
	}
	
	public function getMonthTimesheetData($empId,$year,$month,$project_ids="",$emplistflag='') {
		$status = '_status';
		if($emplistflag == "project"){
			$status = '_project_status';
		}
	
		$select_list = ",CONCAT(IFNULL(ts.sun_reject_note,''),IFNULL(en.sun_reject_note,'')) as sun_reject_note ,CONCAT(IFNULL(ts.mon_reject_note,''),IFNULL(en.mon_reject_note,'')) as mon_reject_note,CONCAT(IFNULL(ts.tue_reject_note,''),IFNULL(en.tue_reject_note,'')) as tue_reject_note ,CONCAT(IFNULL(ts.wed_reject_note,''),IFNULL(en.wed_reject_note,'')) as wed_reject_note,CONCAT(IFNULL(ts.thu_reject_note,''),IFNULL(en.thu_reject_note,'')) as thu_reject_note,CONCAT(IFNULL(ts.fri_reject_note,''),IFNULL(en.fri_reject_note,'')) as fri_reject_note,CONCAT(IFNULL(ts.sat_reject_note,''),IFNULL(en.sat_reject_note,'')) as sat_reject_note";
		
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
				  	if(et.week_duration is null,"00:00",time_format(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.week_duration as time)))),"%H:%i"))week_duration'.$select_list
					));
			  if($project_ids != ""){
			  	$select->joinInner(array('ts'=> $this->_ts_status),'ts.emp_id = et.emp_id and et.ts_year= ts.ts_year and et.ts_month= ts.ts_month and et.ts_week = ts.ts_week and et.project_id = ts.project_id and et.project_id IN ('.$project_ids.')',array());
			  }else{
			  //	$select->joinInner(array('ts'=> $this->_ts_status),'ts.emp_id = et.emp_id and et.ts_year= ts.ts_year and et.ts_month= ts.ts_month and et.ts_week = ts.ts_week and et.project_id = ts.project_id',array());
			 	 	$select->joinInner(array('ts'=> $this->_ts_status),'ts.emp_id = et.emp_id and et.ts_year= ts.ts_year and et.ts_month= ts.ts_month and et.ts_week = ts.ts_week and (et.project_id = ts.project_id OR ts.project_id IS NULL)',array());
			  }
			  $select->joinInner(array('en'=> 'tm_emp_ts_notes'),'en.emp_id = et.emp_id and et.ts_year= en.ts_year and et.ts_month= en.ts_month and et.ts_week = en.ts_week ',array());
	  	  	  $select->where("et.is_active=1 and ts.is_active=1 and et.ts_year = $year and  et.ts_month = $month and et.emp_id = $empId");
			  $select->group('ts.ts_week');
			// echo $select;//exit;
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
		//echo $select;exit;		  	         
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