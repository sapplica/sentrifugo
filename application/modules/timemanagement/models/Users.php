<?php

/**
 * Timemanagement_Model_Users
 *
 * @author l.sudhakar
 * @version
 */

require_once 'Zend/Db/Table/Abstract.php';

class Timemanagement_Model_Users extends Zend_Db_Table_Abstract
{
	/**
	 * The default table name
	 */
	protected $_name = 'main_users';


	public function getUserTimemanagementRole($userId){

		if($userId != 1){
			$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('u'=>$this->_name), array('tm_role'=>new Zend_Db_Expr("(CASE WHEN g.group_name = 'Management' || g.group_name = 'Manager'  THEN  'Manager'
 ELSE CASE WHEN u.id IN (SELECT reporting_manager FROM main_employees_summary) THEN 'Manager'
      ELSE 'Employee' END END)")))
			->joinInner(array('r'=>'main_roles'),"r.id = u.emprole",array())
			->joinInner(array('g'=>'main_groups'),"g.id = r.group_id",array())
			->where(" u.id = ".$userId." ");

			$tmRole = $this->fetchAll($select)->toArray();
		}else{
			$tmRole[0]['tm_role'] = 'Admin';
		}
		if(isset($tmRole))
		{
			return	$tmRole[0]['tm_role'];
		}else{
			return array();
		}
		
	}
	public function getEmployees($empType) {

		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('u'=>'main_employees_summary'),
		array('u.user_id','p.role','u.emprole','tm_role'=>'m.menuName','u.userfullname','u.emailaddress','u.employeeId'))
		->joinInner(array('p'=>'main_privileges'),"p.role = u.emprole",array())
		->joinInner(array('m'=>'main_menu'),"m.id = p.object",array())
		->where(" m.menuName = '".$empType."' and m.parent=(select id from main_menu where menuName ='Time Management' and isactive = 1)");
		
		$employees = $this->fetchAll($select)->toArray();

		return	$employees;

	}
	public function getEmployeeDoj($empid){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = "select date_of_joining,holiday_group,department_id from  main_employees_summary where user_id = ".$empid;

		$data=$db->query($select)->fetch();
		return $data;
	}
	public function getEmployeeHolidaysNWeekends($empId,$year,$month,$calWeek="") {
		$where = "";
		if($calWeek != "") {
			$where .= " and WEEK(holidaydate,2) = '".$calWeek."'";
		} else
		$where .= " and MONTH(holidaydate) = '".$month."'";
			
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('s'=>'main_employees_summary'),
		array('date_of_joining','m.weekend_startday','m.weekend_endday','holiday_names'=>'GROUP_CONCAT(h.holidayname)' ,
					 'holiday_dates'=>'GROUP_CONCAT(h.holidaydate)'))				
		->joinInner(array('m'=>'main_leavemanagement'),"m.department_id = s.department_id",array())
		->joinLeft(array('h'=>'main_holidaydates'),"h.groupid = s.holiday_group and h.isactive=1 and YEAR(holidaydate) = '".$year."' ".$where." ",array())
		->where(" s.user_id = '".$empId."'");
		//echo $select; 
		$result = $this->fetchAll($select)->toArray();

		return $result;
	}



	//function to fetch all employees who reports
	public function getEmployeesReportingTo($reportingToId,$cron_run_day) {

		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('u'=>'main_employees_summary'),
		array('u.user_id','u.emprole','tm_role'=>new Zend_Db_Expr("(CASE WHEN g.group_name = 'Management' || g.group_name = 'Manager'  THEN  'Manager'
 ELSE CASE WHEN u.user_id IN (SELECT reporting_manager FROM main_employees_summary) THEN 'Manager'
      ELSE 'Employee' END END)"),'u.userfullname','u.emailaddress','u.employeeId','u.holiday_group','u.department_id','u.date_of_joining','emp_cur_day' => new Zend_Db_Expr("(SELECT LOWER(DAYNAME(CONVERT_TZ(UTC_TIMESTAMP(),'+00:00',tz.offet_value))))")))
		->joinInner(array('r'=>'main_roles'),"r.id = u.emprole",array())
		->joinInner(array('g'=>'main_groups'),"g.id = r.group_id",array())
		->joinInner(array('d'=>'main_departments'),"d.id = u.department_id",array())
		->joinInner(array('tz'=>'main_timezone'),"tz.id = d.timezone",array())
		->where("u.reporting_manager = '".$reportingToId."'");
	
		$employeesReported = $this->fetchAll($select)->toArray();

		return	$employeesReported;

	}


	/* To check whether in week/month all days have been submitted */
	public function checkweekdaysdatacron($hidstartweek_date,$hidendweek_date,$hidemp,$emp_dept_id,$employeeGroupId,$empJoiningDate)
	{
		$messages = array();
		if($hidemp != '' && $hidstartweek_date != '' && $hidendweek_date != '')
		{
			//To get days in the week/month
			$weekDatesArray = sapp_Global::createDateRangeArray($hidstartweek_date,$hidendweek_date); //echo 
			//End

			$submittedtsdates = $holDates = $weekendDates = $leaveDates = array();

			$startdateObj = DateTime::createFromFormat("Y-m-d", $hidstartweek_date);

			$loopDate = $hidstartweek_date;
			$cal_weekArray = array();
			$yearCalWeekArray = array();
			while (strtotime($loopDate) <= strtotime($hidendweek_date)) {

				$calWeekVal = strftime('%U',strtotime($loopDate));
				$dateYearVal = strftime('%Y',strtotime($loopDate));
				if(!in_array($calWeekVal,$cal_weekArray)){
					$cal_weekArray[] = $calWeekVal;
					$yearCalWeekArray[$calWeekVal][] = $dateYearVal;
				}

				if(!in_array($dateYearVal,$yearCalWeekArray[$calWeekVal])){
					$yearCalWeekArray[$calWeekVal][] = $dateYearVal;
				}
				$loopDate = date ("Y-m-d", strtotime("+1 day", strtotime($loopDate)));
			}
			

			$startDateYear = $startdateObj->format("Y");

			/*submitted and approved status dates in a date range*/
			//To get dates of timesheet filled by user of the given duration
			$tsStatus_model = new Timemanagement_Model_Timesheetstatus();
			$resultData = $tsStatus_model->getEachDayTsDateCron($hidemp,$cal_weekArray,$yearCalWeekArray); 
			$ts_filled_dates = array();
			if(!empty($resultData)){
				foreach($resultData as $resData){
					$ts_week_dates = $resData['ts_week_dates'];
					$ts_weekArray = explode('$',$ts_week_dates);
					if(count($ts_weekArray) > 0){
						foreach($ts_weekArray as $ts_day){
							$ts_day_Array = explode('#',$ts_day);
							if(in_array($ts_day_Array[0],$weekDatesArray)){
								if($ts_day_Array[1] == 'submitted' || $ts_day_Array[1] == 'approved'){
									
										$submittedtsdates[] = $ts_day_Array[0]; //$ts_day_Array[0] is ts status date
									
								}
							}
						}
					}
				}
				$submittedtsdates = array_unique($submittedtsdates);
			}
			/*End*/

			//To get Holidays for the given duration
			$holidayDatesArr =  array();
			if( isset($employeeGroupId) && $employeeGroupId !=''){
				$holidaydatesmodel = new Default_Model_Holidaydates();
				$holidayDateslistArr = $holidaydatesmodel->getHolidayDatesListForGroup($employeeGroupId);
				if(!empty($holidayDateslistArr))
				{
					for($i=0;$i<sizeof($holidayDateslistArr);$i++)
					{
						$holidayDatesArr[$i] = $holidayDateslistArr[$i]['holidaydate'];
					}
				}
			}
			//End
			
			//To get Leaves applied by user for the given duration
			$empLeaves = $this->getEmpLeaves($hidemp,$hidstartweek_date,$hidendweek_date);
			if(!empty($empLeaves)){
				$empleaveDatesArray = array();
				foreach($empLeaves as $empLeaveRow){
					if($empLeaveRow['leaveday'] == 1){
						$leaveDatesArray = sapp_Global::createDateRangeArray($empLeaveRow['from_date'],$empLeaveRow['to_date']);
					}
					$empleaveDatesArray = array_merge($empleaveDatesArray,$leaveDatesArray);
				}
			}
			//End

			//To get default not working days(saturday and sunday)
			if($emp_dept_id !='' && $emp_dept_id != NULL){
				$weekendDetailsArr = $this->getWeekend($hidstartweek_date, $hidendweek_date, $emp_dept_id);
			}
			
			//End


			if(isset($holidayDatesArr) && count($holidayDatesArr)> 0 )
			{
				foreach($holidayDatesArr as $holidayDate)
				{
					if(in_array($holidayDate,$weekDatesArray)){
						$holDates[] = $holidayDate;
					}
				}
			}
			if(isset($empleaveDatesArray) && count($empleaveDatesArray)> 0 )
			{
				foreach($empleaveDatesArray as $empleaveDate)
				{
					if(in_array($empleaveDate,$weekDatesArray)){
						$leaveDates[] = $empleaveDate;
					}
				}
			}
			if(isset($weekendDetailsArr) && count($weekendDetailsArr)> 0 )
			{
				foreach($weekendDetailsArr as $weekendDate)
				{
					if(in_array($weekendDate,$weekDatesArray)){
						$weekendDates[] = $weekendDate;
					}
				}
			}

			$totalDaysArray = array();
			$totalDaysArray = array_merge($submittedtsdates,$holDates,$leaveDates,$weekendDates);
			

			$emptyDataDatesArray = array();
			$emptyDataDatesArray = array_diff($weekDatesArray,$totalDaysArray);
			
			if(count($emptyDataDatesArray)>0)
			{
				$newemptyDataDatesArray=array();
				foreach($emptyDataDatesArray as $edate)
				{
					if($edate>=$empJoiningDate)
					$newemptyDataDatesArray[]=$edate;
				} 
				return $newemptyDataDatesArray;
			}
			else
			return array();
			
		}
	}

	public function getWeekend($startday,$endday,$dept_id)
	{
		$wkend_dates_arr = array();
		$leavemanagementmodel = new Default_Model_Leavemanagement();
		$weekendDetailsArr = $leavemanagementmodel->getWeekendNamesDetails($dept_id);

		if(!empty($weekendDetailsArr)){
			$weekend_arr=array($weekendDetailsArr[0]['daystartname'],$weekendDetailsArr[0]['dayendname']);
			$wkend_dates_arr=array();
			while($startday<=$endday)
			{
				$nday=new DateTime($startday);
				if(in_array($nday->format('l'),$weekend_arr))
				{
					$wkend_dates_arr[]=$startday;
				}

				$nday->add(new DateInterval('P1D'));
				$startday=$nday->format('Y-m-d');
			}
		}
		return $wkend_dates_arr;
	}

	public function getEmpLeaves($empid,$startday,$endday,$flag='')
	{
		$where = "";
		if($flag != 'all') $where .= "el.leaveday = 1 AND";
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('el' => 'main_leaverequest'),
		array('from_date'=>'date_format(el.from_date,"%Y-%m-%d")','to_date'=>'date_format(el.to_date,"%Y-%m-%d")','leavetypeid'=>'el.leavetypeid','leavestatus'=>'el.leavestatus','leaveday'=>'el.leaveday','leave_req_id'=>'el.id'))
	
		->where("$where  el.isactive=1 AND el.user_id=".$empid." AND el.leavestatus IN ('Approved','Pending for approval') AND  ((el.from_date >= '".$startday."' AND el.from_date <= '".$endday."') OR (el.to_date >= '".$startday."' AND el.to_date <= '".$endday."')) ");
	
		return $this->fetchAll($select)->toArray();
	}


	public function getManagers()
	{
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('u'=>'main_employees_summary'),
		array('u.user_id','u.emprole','tm_role'=>new Zend_Db_Expr("(CASE WHEN g.group_name = 'Management' || g.group_name = 'Manager'  THEN  'Manager'
 ELSE CASE WHEN u.user_id IN (SELECT reporting_manager FROM main_employees_summary) THEN 'Manager'
      ELSE 'Employee' END END)"),'u.userfullname','u.emailaddress','u.employeeId'))
		->joinInner(array('r'=>'main_roles'),"r.id = u.emprole",array())
		->joinInner(array('g'=>'main_groups'),"g.id = r.group_id",array())
		->group("u.user_id")
		->having("tm_role = 'Manager'");;

		return $this->fetchAll($select)->toArray();
	}

	
	public function checkTmEnable()
	{
		$select = "select * from main_menu where isactive=1 AND url='/timemanagement'";
		$db = Zend_Db_Table::getDefaultAdapter();
		$result = $db->query($select)->fetchAll();
		if(count($result) > 0){
			return true;
		}else{
			return false;
		}
	}

}
