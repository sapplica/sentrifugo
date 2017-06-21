<?php

/**
 * Time sheet status
 *
 * @author sagarsoft
 * @version
 */

require_once 'Zend/Db/Table/Abstract.php';

class Timemanagement_Model_Timesheetstatus extends Zend_Db_Table_Abstract
{
	/**
	 * The default table name
	 */
	protected $_name = 'tm_ts_status';

	public function getEachDayTsDateCron($empid,$cal_weekArray,$yearArray)
	{
		$where = '';
		if(!empty($cal_weekArray)){
			foreach($cal_weekArray as $cal_week){
				foreach($yearArray[$cal_week] as $year){
					$where.= "(t.cal_week = '".$cal_week."' AND t.ts_year = '".$year."') OR ";
				}
			}
		}
		if($where != ''){
			$where = rtrim($where," OR ");
		}
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('t' => 'tm_ts_status'), array('ts_week_dates'=>"group_concat(concat(sun_date,'#',IFNULL(sun_status,'NULL')),'$',concat(mon_date,'#',IFNULL(mon_status,'NULL')),'$',concat(tue_date,'#',IFNULL(tue_status,'NULL')),'$',concat(wed_date,'#',IFNULL(wed_status,'NULL')),'$',concat(thu_date,'#',IFNULL(thu_status,'NULL')),'$',concat(fri_date,'#',IFNULL(fri_status,'NULL')),'$',concat(sat_date,'#',IFNULL(sat_status,'NULL')))"))
		->where("t.emp_id=".$empid." AND t.is_active=1 AND (".$where.")")
		->group('t.ts_month');

		//echo $select;exit;
		return $this->fetchAll($select)->toArray();
	}

	public function getTsRecordExists($empid,$ts_year,$ts_month,$ts_week,$cal_week)
	{
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('t' => 'tm_ts_status'), array('ts_week_dates'=>"concat(concat(sun_date,'#',IFNULL(sun_status,'NULL')),'$',concat(mon_date,'#',IFNULL(mon_status,'NULL')),'$',concat(tue_date,'#',IFNULL(tue_status,'NULL')),'$',concat(wed_date,'#',IFNULL(wed_status,'NULL')),'$',concat(thu_date,'#',IFNULL(thu_status,'NULL')),'$',concat(fri_date,'#',IFNULL(fri_status,'NULL')),'$',concat(sat_date,'#',IFNULL(sat_status,'NULL')))"))
		->where("t.emp_id=".$empid." AND t.is_active=1 AND t.ts_year=".$ts_year." AND t.ts_month=".$ts_month." AND t.ts_week=".$ts_week." AND t.cal_week=".$cal_week." ")
		->group('t.emp_id');

		//echo $select;exit;
		return $this->fetchAll($select)->toArray();
	}

	public function SaveTsData($data)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->insert('tm_ts_status', $data);
		return $id = $db->lastInsertId();
	}

	public function SaveEmpTsNotesData($data)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->insert('tm_emp_ts_notes', $data);
		return $id = $db->lastInsertId();
	}

	public function SaveEmpTsData($data)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->insert('tm_emp_timesheets', $data);
		return $id = $db->lastInsertId();
	}

	public function updateTsData($data,$where)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$id = $db->update('tm_ts_status', $data ,$where);
		return $id;
	}

}
