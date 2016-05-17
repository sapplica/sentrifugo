<?php

/**
 * MyTimesheet
 *
 * @author l.sudhakar
 * @version
 */

require_once 'Zend/Db/Table/Abstract.php';

class Timemanagement_Model_MyTimesheet extends Zend_Db_Table_Abstract
{
	/**
	 * The default table name
	 */
	protected $_name = 'tm_emp_timesheets';
	
	public function SaveOrUpdateTimesheet($arrayData)
	{
		$db = Zend_Db_Table::getDefaultAdapter();	
		$arrayDataObj = new ArrayObject($arrayData);
		$arrayDataObj['modified_by'] = $arrayData['emp_id'];
		$arrayDataObj['modified'] = Zend_Registry::get('currentdate'); 
		unset($arrayDataObj['created_by']);
		unset($arrayDataObj['created']);		
		$arrayDataForUpdate = $arrayDataObj->getArrayCopy();
			
		$query = 'INSERT INTO '. $this->_name.'('.implode(',',array_keys($arrayData)).') VALUES ('.
			implode(',',array_fill(1, count($arrayData), '?')).') ON DUPLICATE KEY UPDATE '.implode(' = ?,',
			array_keys($arrayDataForUpdate)).' = ?';
		//	echo $query; exit;      
        $result = $db->query($query,array_merge(array_values($arrayData),array_values($arrayDataForUpdate)));
        
        return $result;
        
//		if($where != ''){
//			$this->update($data, $where);
//			return 'update';
//		} else {
//			$this->insert($data);
//			$id=$this->getAdapter()->lastInsertId($this->_name);
//			return $id;
//		}
	}
	public function updateTimesheetRecord($data, $where) {
		
		$this->update($data, $where);
			return 'update';
	}
	public function updateStatusRecord($data, $where) {
		
		$db = Zend_Db_Table::getDefaultAdapter();	
		$query = 'UPDATE  tm_ts_status SET '.implode(' = ?,',array_keys($data)).' = ?'.
				 ' where '.$where;
		//echo $query; exit;      
        $result = $db->query($query,array_values($data));
		
	}
	public function getProjNullRecordCountInTimeSheet($empId,$year,$month,$week){
		
		 $select = $this->select()
				->setIntegrityCheck(false)
				->from(array('t' => 'tm_emp_timesheets'),
				array('count(*) rec_count'))			
				->where(" project_id is null and t.emp_id =".$empId." and t.ts_year = '".$year."' and t.ts_month='".$month."' and t.ts_week = '".$week."'");
			//echo $select;exit;					
     	 $result = $this->fetchAll($select)->toArray();		  	
		 
       	 return $result[0]['rec_count'];
	}
	public function getProjNullRecordCountInStatus($empId,$year,$month,$week){
		
		 $select = $this->select()
				->setIntegrityCheck(false)
				->from(array('s' => 'tm_ts_status'),
				array('count(*) rec_count'))			
				->where(" project_id is null and s.emp_id =".$empId." and s.ts_year = '".$year."' and s.ts_month='".$month."' and s.ts_week = '".$week."'");
			//echo $select;exit;	
		 $result = $this->fetchAll($select)->toArray();		  	
		 
       return $result[0]['rec_count'];
	}
	public function deleteTimesheetTask($empId,$arrayData,$projTaskId,$year,$month,$week){
		
		$db = Zend_Db_Table::getDefaultAdapter();	
		$query = 'UPDATE '. $this->_name.' SET '.implode(' = ?,',array_keys($arrayData)).' = ?'.
			' where project_task_id = '. $projTaskId.' and ts_year = '.$year.' and  ts_month = '.$month .' and ts_week = '.$week.' and emp_id ='. $empId;
		//echo $query; exit;      
        $result = $db->query($query,array_values($arrayData));
        
        return $result;
	}
	public function deleteWeekProjectStatus($empId,$arrayData,$projId,$year,$month,$week){
		
		$db = Zend_Db_Table::getDefaultAdapter();	
		$query = 'UPDATE tm_ts_status  SET '.implode(' = ?,',array_keys($arrayData)).' = ?'.
			' where project_id = '. $projId.' and ts_year = '.$year.' and  ts_month = '.$month .' and ts_week = '.$week.' and emp_id ='. $empId;
		//echo $query; exit;      
        $result = $db->query($query,array_values($arrayData));
        
        return $result;
	}
	public function SaveOrUpdateTimesheetStatus($arrayData)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$arrayDataObj = new ArrayObject($arrayData);
		$arrayDataObj['modified_by'] = $arrayData['emp_id'];
		$arrayDataObj['modified'] = Zend_Registry::get('currentdate'); 
		unset($arrayDataObj['created_by']);
		unset($arrayDataObj['created']);
		
		$weekDailyStatus = self::getWeekDaysDailyStatus($arrayData['emp_id'],$arrayData['ts_year'],$arrayData['ts_month'],$arrayData['ts_week']);
		if(sizeof($weekDailyStatus) > 0) {
			if(in_array($weekDailyStatus[0]['sun_status'], array('approved','submitted'))) {
				unset($arrayDataObj['sun_project_status']);
				unset($arrayDataObj['sun_status']);
			}
			if(in_array($weekDailyStatus[0]['mon_status'], array('approved','submitted'))) {
				unset($arrayDataObj['mon_project_status']);
				unset($arrayDataObj['mon_status']);
			}
			if(in_array($weekDailyStatus[0]['tue_status'], array('approved','submitted'))) {
				unset($arrayDataObj['tue_project_status']);
				unset($arrayDataObj['tue_status']);
			}
			if(in_array($weekDailyStatus[0]['wed_status'], array('approved','submitted'))) {
				unset($arrayDataObj['wed_project_status']);
				unset($arrayDataObj['wed_status']);
			}
			if(in_array($weekDailyStatus[0]['thu_status'], array('approved','submitted'))) {
				unset($arrayDataObj['thu_project_status']);
				unset($arrayDataObj['thu_status']);
			}
			if(in_array($weekDailyStatus[0]['fri_status'], array('approved','submitted'))) {
				unset($arrayDataObj['fri_project_status']);
				unset($arrayDataObj['fri_status']);
			}
			if(in_array($weekDailyStatus[0]['sat_status'], array('approved','submitted'))) {
				unset($arrayDataObj['sat_project_status']);
				unset($arrayDataObj['sat_status']);
			}
		}
		
		$weekProjStatus = self::getWeekDaysProjStatusForProject($arrayData['emp_id'],$arrayData['ts_year'],$arrayData['ts_month'],$arrayData['ts_week'],$arrayData['project_id']);
		foreach($weekProjStatus as $projStatus) {
			$sunProjFlag = in_array($projStatus['sun_project_status'], array('submitted','approved'));
      		$monProjFlag = in_array($projStatus['mon_project_status'], array('submitted','approved'));
      		$tueProjFlag = in_array($projStatus['tue_project_status'], array('submitted','approved'));
      		$wedProjFlag = in_array($projStatus['wed_project_status'], array('submitted','approved'));
      		$thuProjFlag = in_array($projStatus['thu_project_status'], array('submitted','approved'));
      		$friProjFlag = in_array($projStatus['fri_project_status'], array('submitted','approved'));
      		$satProjFlag = in_array($projStatus['sat_project_status'], array('submitted','approved'));
      				
      		if($sunProjFlag) {
      			if(array_key_exists("sun_project_status",$arrayDataObj)) unset($arrayDataObj['sun_project_status']);
      		}
      		if($monProjFlag){
      			if(array_key_exists("mon_project_status",$arrayDataObj)) unset($arrayDataObj['mon_project_status']);
      		}
      		if($tueProjFlag){
      			if(array_key_exists("tue_project_status",$arrayDataObj)) unset($arrayDataObj['tue_project_status']);
      		}
      		if($wedProjFlag) { 
      			if(array_key_exists("wed_project_status",$arrayDataObj)) unset($arrayDataObj['wed_project_status']);
      		}
      		if($thuProjFlag) { 
      			if(array_key_exists("thu_project_status",$arrayDataObj)) unset($arrayDataObj['thu_project_status']);
      		}
      		if($friProjFlag){ 
      			if(array_key_exists("fri_project_status",$arrayDataObj)) unset($arrayDataObj['fri_project_status']);
      		}
      		if($satProjFlag){
      			if(array_key_exists("sat_project_status",$arrayDataObj)) unset($arrayDataObj['sat_project_status']);
      		}
		}		
		
		$arrayDataForUpdate = $arrayDataObj->getArrayCopy();
			
		$query = 'INSERT INTO tm_ts_status ('.implode(',',array_keys($arrayData)).') VALUES ('.
			implode(',',array_fill(1, count($arrayData), '?')).') ON DUPLICATE KEY UPDATE '.implode(' = ?,',
			array_keys($arrayDataForUpdate)).' = ?';
		//echo $query; //exit;

       // return $db->query($query,array_merge(array_values($arrayData),array_values($arrayDataForUpdate)))->fetch();
        $result =  $db->query($query,array_merge(array_values($arrayData),array_values($arrayDataForUpdate)));
        
        return $result;

	}
	public function SaveOrUpdateTimesheetNotes($arrayData)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$arrayDataObj = new ArrayObject($arrayData);
		$arrayDataObj['modified_by'] = $arrayData['emp_id'];
		$arrayDataObj['modified'] = Zend_Registry::get('currentdate');
		unset($arrayDataObj['created_by']); 
		unset($arrayDataObj['created']);
		
		$arrayDataForUpdate = $arrayDataObj->getArrayCopy();
				
		$query = 'INSERT INTO tm_emp_ts_notes ('.implode(',',array_keys($arrayData)).') VALUES ('.
			implode(',',array_fill(1, count($arrayData), '?')).') ON DUPLICATE KEY UPDATE '.implode(' = ?,',
			array_keys($arrayDataForUpdate)).' = ?';
	////	echo $query; exit;      
        $result = $db->query($query,array_merge(array_values($arrayData),array_values($arrayDataForUpdate)));
        return $result;
        
	}
	public function getWeekProjectHrs($empId,$projId,$year,$month,$week){
		
		$db = Zend_Db_Table::getDefaultAdapter();	
	
		$select = $this->select()
				  ->setIntegrityCheck(false)
				  ->from(array('et'=>$this->_name),array(
				  	'if(et.sun_duration is null,"00:00",time_format(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.sun_duration as time)))),"%H:%i"))sun_duration,
					if(et.mon_duration is null,"00:00",time_format(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.mon_duration as time)))),"%H:%i"))mon_duration,
					if(et.tue_duration is null,"00:00",time_format(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.tue_duration as time)))),"%H:%i"))tue_duration,
					if(et.wed_duration is null,"00:00",time_format(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.wed_duration as time)))),"%H:%i"))wed_duration,
					if(et.thu_duration is null,"00:00",time_format(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.thu_duration as time)))),"%H:%i"))thu_duration,
					if(et.fri_duration is null,"00:00",time_format(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.fri_duration as time)))),"%H:%i"))fri_duration,
					if(et.sat_duration is null,"00:00",time_format(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.sat_duration as time)))),"%H:%i"))sat_duration,
				  	if(et.week_duration is null,"00:00",time_format(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.week_duration as time)))),"%H:%i"))week_duration'
					))				
				  ->where("et.is_active=1 and et.project_id = $projId and et.ts_year = $year and  et.ts_month = $month and et.ts_week = $week and et.emp_id = $empId")
				  ->group('et.project_id');
				  	         
		return $this->fetchAll($select)->toArray();
		
	}
	public function empployeeDeletedTasks($empId,$year,$mon,$week,$calWeek,$projTasks) {
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$taskIds = "".implode(',',array_values($projTasks)); 
		//echo " tasks --> ".implode(',',array_values($projTasks)); exit;
		$query = 'SELECT project_task_id FROM tm_emp_timesheets WHERE is_active = 1 and emp_id ='.$empId.' AND ts_year='.$year.' AND ts_month="'.$mon.
				 '" AND ts_week ='.$week.' AND cal_week ='.$calWeek.' AND project_task_id NOT IN ('.$taskIds.')';
		//echo " SQL  ".$query; exit; 
		$result =$db->query($query)->fetch();
		return $result;
	}
//	public function getEmployeeWeekTasks($empId,$year,$mon,$week) {
//		
//		$db = Zend_Db_Table::getDefaultAdapter();
//		$taskIds = "".implode(',',array_values($projTasks)); 
//		//echo " tasks --> ".implode(',',array_values($projTasks)); exit;
//		$query = 'SELECT project_task_id FROM tm_emp_timesheets WHERE is_active = 1 and emp_id ='.$empId.' AND ts_year='.$year.' AND ts_month="'.$mon.
//				 '" AND ts_week ='.$week.')';
//		//echo " SQL  ".$query; exit; 
//		$result =$db->query($query)->fetch();
//		return $result;
//	}
	public function empployeeDeletedProjects($empId,$year,$mon,$week,$calWeek,$projectIds) {
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$projIds = "".implode(',',array_values($projectIds)); 
		//echo " tasks --> ".implode(',',array_values($projTasks)); exit;
		$query = 'SELECT project_id FROM tm_ts_status WHERE is_active = 1 and emp_id ='.$empId.' AND ts_year='.$year.' AND ts_month="'.$mon.
				 '" AND ts_week ='.$week.' AND cal_week ='.$calWeek.' AND project_id NOT IN ('.$projIds.')';
		//echo " SQL  ".$query; exit; 
		$result =$db->query($query)->fetch();
		return $result;
	}
	public function getWeekDaysDailyStatus($empId,$year,$mon,$week) {
		
		 $select = $this->select()
				->setIntegrityCheck(false)
				->from(array('s' => 'tm_ts_status'),array('s.sun_status','s.mon_status','s.tue_status','s.wed_status','s.thu_status',
				's.fri_status','s.sat_status'))
				->where("s.emp_id =".$empId." and s.ts_year = '".$year."' and s.ts_month = '".$mon."' and s.ts_week = '".$week."' ")
				->group('s.emp_id');
			//echo $select;exit;					
       return $this->fetchAll($select)->toArray();
		
	}
	public function getWeekDaysProjStatus($empId,$year,$mon,$week) {
		
		 $select = $this->select()
				->setIntegrityCheck(false)
				->from(array('s' => 'tm_ts_status'),array('s.project_id','s.sun_project_status','s.mon_project_status','s.tue_project_status','s.wed_project_status','s.thu_project_status',
				's.fri_project_status','s.sat_project_status'))
				->where("s.emp_id =".$empId." and s.ts_year = '".$year."' and s.ts_month = '".$mon."' and s.ts_week = '".$week."' and s.is_active = 1 ");
			
			//echo $select;exit;					
       return $this->fetchAll($select)->toArray();
		
	}
	public function getWeekDaysProjStatusForProject($empId,$year,$mon,$week,$projId) {
		
		 $select = $this->select()
				->setIntegrityCheck(false)
				->from(array('s' => 'tm_ts_status'),array('s.project_id','s.sun_project_status','s.mon_project_status','s.tue_project_status','s.wed_project_status','s.thu_project_status',
				's.fri_project_status','s.sat_project_status'))
				->where("s.project_id =".$projId." and s.emp_id =".$empId." and s.ts_year = '".$year."' and s.ts_month = '".$mon."' and s.ts_week = '".$week."' ");
			
			//echo $select;exit;					
       return $this->fetchAll($select)->toArray();
		
	}
	
	public function getMonthTimesheetData($empId,$year,$month) { //,$project_ids="" 
		//echo $project_ids;
		$select = $this->select()
				  ->setIntegrityCheck(false)
				  ->from(array('et'=>$this->_name),array(
				  	'ts.ts_week,et.sun_date,et.mon_date,et.tue_date,et.wed_date,et.thu_date,et.fri_date,et.sat_date,
				  	if(et.sun_duration is null,"00:00",time_format(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.sun_duration as time)))),"%H:%i"))sun_duration,ifnull(ts.sun_status,"no_entry")sun_status,
					if(et.mon_duration is null,"00:00",time_format(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.mon_duration as time)))),"%H:%i"))mon_duration,ifnull(ts.mon_status,"no_entry")mon_status,
					if(et.tue_duration is null,"00:00",time_format(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.tue_duration as time)))),"%H:%i"))tue_duration,ifnull(ts.tue_status,"no_entry")tue_status,
					if(et.wed_duration is null,"00:00",time_format(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.wed_duration as time)))),"%H:%i"))wed_duration,ifnull(ts.wed_status,"no_entry")wed_status,
					if(et.thu_duration is null,"00:00",time_format(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.thu_duration as time)))),"%H:%i"))thu_duration,ifnull(ts.thu_status,"no_entry")thu_status,
					if(et.fri_duration is null,"00:00",time_format(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.fri_duration as time)))),"%H:%i"))fri_duration,ifnull(ts.fri_status,"no_entry")fri_status,
					if(et.sat_duration is null,"00:00",time_format(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.sat_duration as time)))),"%H:%i"))sat_duration,ifnull(ts.sat_status,"no_entry")sat_status,
				  	if(et.week_duration is null,"00:00",time_format(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.week_duration as time)))),"%H:%i"))week_duration,
				  	GROUP_CONCAT(distinct ifnull(tn.sun_reject_note,""), if(ts.sun_reject_note != "", CONCAT(p.project_name,":",ifnull(ts.sun_reject_note,"")),"")) sun_reject_note,
				  	GROUP_CONCAT(distinct ifnull(tn.mon_reject_note,""), if(ts.mon_reject_note != "", CONCAT(p.project_name,":",ifnull(ts.mon_reject_note,"")),"")) mon_reject_note,
				  	GROUP_CONCAT(distinct ifnull(tn.tue_reject_note,""), if(ts.tue_reject_note != "", CONCAT(p.project_name,":",ifnull(ts.tue_reject_note,"")),"")) tue_reject_note,
				  	GROUP_CONCAT(distinct ifnull(tn.wed_reject_note,""), if(ts.wed_reject_note != "", CONCAT(p.project_name,":",ifnull(ts.wed_reject_note,"")),"")) wed_reject_note,
				  	GROUP_CONCAT(distinct ifnull(tn.thu_reject_note,""), if(ts.thu_reject_note != "", CONCAT(p.project_name,":",ifnull(ts.thu_reject_note,"")),"")) thu_reject_note,
				  	GROUP_CONCAT(distinct ifnull(tn.fri_reject_note,""), if(ts.fri_reject_note != "", CONCAT(p.project_name,":",ifnull(ts.fri_reject_note,"")),"")) fri_reject_note,
				  	GROUP_CONCAT(distinct ifnull(tn.sat_reject_note,""), if(ts.sat_reject_note != "", CONCAT(p.project_name,":",ifnull(ts.sat_reject_note,"")),"")) sat_reject_note'
					));
			//	  ->joinInner(array('ts'=>'tm_ts_status'),'ts.emp_id = et.emp_id and ts.project_id = et.project_id',array())
//			  if($project_ids != ""){
//			  	$select->joinInner(array('ts'=>'tm_ts_status'),'ts.emp_id = et.emp_id and et.ts_year= ts.ts_year and et.ts_month= ts.ts_month and et.ts_week = ts.ts_week and et.project_id = ts.project_id and et.project_id IN ('.$project_ids.')',array());
//			  }else{
			  	$select->joinInner(array('ts'=>'tm_ts_status'),'ts.emp_id = et.emp_id and et.ts_year= ts.ts_year and et.ts_month= ts.ts_month and et.ts_week = ts.ts_week and (et.project_id = ts.project_id OR ts.project_id IS NULL)',array());
			  	$select->joinInner(array('tn'=>'tm_emp_ts_notes'),'tn.emp_id = ts.emp_id and tn.ts_year= ts.ts_year and tn.ts_month= ts.ts_month and tn.ts_week = ts.ts_week',array());
			  	$select->joinLeft(array('p'=>'tm_projects'),'p.id = ts.project_id and p.id = et.project_id',array());
			  	
			  	
			//  }
	  	  	  $select->where("et.is_active=1 and ts.is_active=1 and et.ts_year = $year and  et.ts_month = $month and et.emp_id = $empId");
			  $select->group('ts.ts_week');
			//echo $select; exit;	  	         
		return $this->fetchAll($select)->toArray();
	}
	//public function getWeeklyTimesheetData($empId,$year,$calWeek,$week) {
	public function getWeeklyTimesheetData($empId,$year,$month,$week,$flag='') { //,$project_ids="" 
		//$calWeek = 26;
		$where = '';
		if($flag != 'view'){
			$where .= " and  p.project_status NOT IN ('draft')";
		}
		$select = $this->select()
				  ->setIntegrityCheck(false)
				  ->from(array('pte'=>'tm_project_task_employees'),array(
				  	'pte.project_id','p.project_name','p.project_status','pte.project_task_id','t.task','sun_duration'=>'ifnull(et.sun_duration,0)','mon_duration'=>'ifnull(et.mon_duration,0)',
				  	'tue_duration'=>'ifnull(et.tue_duration,0)','wed_duration'=>'ifnull(et.wed_duration,0)','thu_duration'=>'ifnull(et.thu_duration,0)','fri_duration'=>'ifnull(et.fri_duration,0)','sat_duration'=>'ifnull(et.sat_duration,0)','week_duration'=>'ifnull(et.week_duration,0)'))
				  ->joinInner(array('pt'=>'tm_project_tasks'),'pt.id = pte.project_task_id',array())
				  ->joinInner(array('p'=>'tm_projects'),'p.id = pt.project_id and p.id = pte.project_id',array())
				  ->joinInner(array('t'=>'tm_tasks'),'t.id = pt.task_id',array());
				 // ->joinLeft(array('et'=>'tm_emp_timesheets'),'et.project_task_id = pte.project_task_id and ts_year='.$year.' and et.ts_month='.$month.' and et.ts_week = '.$week.' and et.emp_id= '.$empId,array());
				 
				if($flag == 'view')  
		 		 	$select->joinInner(array('et'=>'tm_emp_timesheets'),'et.project_task_id = pte.project_task_id and et.ts_year='.$year.' and et.ts_month='.$month.' and et.ts_week = '.$week.' and et.emp_id= '.$empId,array());
		 		else  
		 			$select->joinLeft(array('et'=>'tm_emp_timesheets'),'et.project_task_id = pte.project_task_id and ts_year='.$year.' and et.ts_month='.$month.' and et.ts_week = '.$week.' and et.emp_id= '.$empId,array());	
		  $select->where("pte.emp_id =".$empId." and pte.is_active = 1 and p.is_active = 1 $where ");
//		  if($project_ids != ""){
//		  	$select->where("p.id IN (".$project_ids.")");
//		  }
		  $select->order('p.project_name');
		//echo $select;exit;		  	         
		return $this->fetchAll($select)->toArray();
	}
	public function getWeekNotes($empId,$year,$month,$week){
		
		 $select = $this->select()
				->setIntegrityCheck(false)
				->from(array('n' => 'tm_emp_ts_notes'),array('n.sun_note','n.mon_note','n.tue_note','n.wed_note','n.thu_note',
				'n.fri_note','n.sat_note','n.week_note'))
			//	->where("n.emp_id =".$empId." and n.ts_year = '".$year."' and n.cal_week = '".$calWeek."' ");
				->where("n.emp_id =".$empId." and n.ts_year = '".$year."' and n.ts_month='".$month."' and n.ts_week = '".$week."'");
			//echo $select;exit;					
       return $this->fetchAll($select)->toArray();
	}
//	public function getWeekDaysStatus($empId,$year,$calWeek){
	public function getWeekDaysStatus($empId,$year,$month,$week,$emplistflag='',$project_ids=""){
		$status = '_status';
		$where = "";
		if($emplistflag == "project"){
			$status = '_project_status';
			if($project_ids!="")
			{
				$where = " AND s.project_id IN (".$project_ids.")";
			}
		}
		$sun_reject_note = 'CONCAT(IFNULL(s.sun_reject_note,""),IFNULL(en.sun_reject_note,""))';
		$mon_reject_note = 'CONCAT(IFNULL(s.mon_reject_note,""),IFNULL(en.mon_reject_note,""))';
		$tue_reject_note = 'CONCAT(IFNULL(s.tue_reject_note,""),IFNULL(en.tue_reject_note,""))';
		$wed_reject_note = 'CONCAT(IFNULL(s.wed_reject_note,""),IFNULL(en.wed_reject_note,""))';
		$thu_reject_note = 'CONCAT(IFNULL(s.thu_reject_note,""),IFNULL(en.thu_reject_note,""))';
		$fri_reject_note = 'CONCAT(IFNULL(s.fri_reject_note,""),IFNULL(en.fri_reject_note,""))';
		$sat_reject_note = 'CONCAT(IFNULL(s.sat_reject_note,""),IFNULL(en.sat_reject_note,""))';
		
		
		 $select = $this->select()
				->setIntegrityCheck(false)
				->from(array('s' => 'tm_ts_status'),array('sun_reject_note'=>$sun_reject_note,'mon_reject_note'=>$mon_reject_note,'tue_reject_note'=>$tue_reject_note,'wed_reject_note'=>$wed_reject_note,'thu_reject_note'=>$thu_reject_note,'fri_reject_note'=>$fri_reject_note,'sat_reject_note'=>$sat_reject_note,
				'sun_status'=>'case when s.sun'.$status.' ="submitted" then "For Approval" when s.sun'.$status.' ="saved" 
				then "Yet to submit" when s.sun'.$status.' ="no_entry" then "No Entry" 
				else CONCAT(UCASE(LEFT(s.sun'.$status.', 1)),SUBSTRING(s.sun'.$status.', 2)) end',
				
				'mon_status'=>'case when s.mon'.$status.' ="submitted" then "For Approval" when s.mon'.$status.' ="saved" 
				then "Yet to submit" when s.mon'.$status.' ="no_entry" then "No Entry" 
				else CONCAT(UCASE(LEFT(s.mon'.$status.', 1)),SUBSTRING(s.mon'.$status.', 2)) end',
				
				'tue_status'=>'case when s.tue'.$status.' ="submitted" then "For Approval" when s.tue'.$status.' ="saved" 
				then "Yet to submit" when s.tue'.$status.' ="no_entry" then "No Entry" 
				else CONCAT(UCASE(LEFT(s.tue'.$status.', 1)),SUBSTRING(s.tue'.$status.', 2)) end',
				
				'wed_status'=>'case when s.wed'.$status.' ="submitted" then "For Approval" when s.wed'.$status.' ="saved" 
				then "Yet to submit" when s.wed'.$status.' ="no_entry" then "No Entry" 
				else CONCAT(UCASE(LEFT(s.wed'.$status.', 1)),SUBSTRING(s.wed'.$status.', 2)) end',
				
				'thu_status'=>'case when s.thu'.$status.' ="submitted" then "For Approval" when s.thu'.$status.' ="saved" 
				then "Yet to submit" when s.thu'.$status.' ="no_entry" then "No Entry" 
				else CONCAT(UCASE(LEFT(s.thu'.$status.', 1)),SUBSTRING(s.thu'.$status.', 2)) end',
				
				'fri_status'=>'case when s.fri'.$status.' ="submitted" then "For Approval" when s.fri'.$status.' ="saved" 
				then "Yet to submit" when s.fri'.$status.' ="no_entry" then "No Entry" 
				else CONCAT(UCASE(LEFT(s.fri'.$status.', 1)),SUBSTRING(s.fri'.$status.', 2)) end',
				
				'sat_status'=>'case when s.sat'.$status.' ="submitted" then "For Approval" when s.sat'.$status.' ="saved" 
				then "Yet to submit" when s.sat'.$status.' ="no_entry" then "No Entry" 
				else CONCAT(UCASE(LEFT(s.sat'.$status.', 1)),SUBSTRING(s.sat'.$status.', 2)) end',
				'week_status'=>'case when s.week_status ="submitted" then "For Approval" when s.week_status ="saved" 
				then "Yet to submit" when s.week_status ="no_entry" then "No Entry" 
				else CONCAT(UCASE(LEFT(s.week_status, 1)),SUBSTRING(s.week_status, 2)) end'))
				
				->joinInner(array('en'=> 'tm_emp_ts_notes'),'en.emp_id = s.emp_id and s.ts_year= en.ts_year and s.ts_month= en.ts_month and s.ts_week = en.ts_week ',array())
				
		//		->where("s.emp_id =".$empId." and s.ts_year = '".$year."' and s.cal_week = '".$calWeek."' ");
				->where("s.emp_id =".$empId." and s.ts_year = '".$year."' and s.ts_month='".$month."' and s.ts_week = '".$week."'  ".$where)
				->group('s.emp_id');
				
		//echo $select;//exit;					
       return $this->fetchAll($select)->toArray();
	}
	public function updateDayStatus($empId,$year,$month,$week){
		 
		$select = $this->select()
				->setIntegrityCheck(false)
				->from(array('s' => 'tm_ts_status'),array(
				 'sun_status' => 'IF(FIND_IN_SET("blocked",GROUP_CONCAT(DISTINCT s.sun_project_status))!=0,"blocked",
				 IF(FIND_IN_SET("saved",GROUP_CONCAT(DISTINCT s.sun_project_status))!=0,"saved",
    			IF(FIND_IN_SET("rejected",GROUP_CONCAT(DISTINCT s.sun_project_status))!=0,"rejected",
    			IF(FIND_IN_SET("submitted",GROUP_CONCAT(DISTINCT s.sun_project_status))!=0,"submitted",
    			IF(FIND_IN_SET("approved",GROUP_CONCAT(DISTINCT s.sun_project_status))!=0,"approved",    			
    			IF(FIND_IN_SET("enabled",GROUP_CONCAT(DISTINCT s.sun_project_status))!=0,"enabled","no_entry"))))))',
				 'mon_status' => 'IF(FIND_IN_SET("blocked",GROUP_CONCAT(DISTINCT s.mon_project_status))!=0,"blocked",
				IF(FIND_IN_SET("saved",GROUP_CONCAT(DISTINCT s.mon_project_status))!=0,"saved",
			    IF(FIND_IN_SET("rejected",GROUP_CONCAT(DISTINCT s.mon_project_status))!=0,"rejected",
			    IF(FIND_IN_SET("submitted",GROUP_CONCAT(DISTINCT s.mon_project_status))!=0,"submitted",
			    IF(FIND_IN_SET("approved",GROUP_CONCAT(DISTINCT s.mon_project_status))!=0,"approved",			    
			    IF(FIND_IN_SET("enabled",GROUP_CONCAT(DISTINCT s.mon_project_status))!=0,"enabled","no_entry"))))))',
				 'tue_status' => 'IF(FIND_IN_SET("blocked",GROUP_CONCAT(DISTINCT s.tue_project_status))!=0,"blocked",
				IF(FIND_IN_SET("saved",GROUP_CONCAT(DISTINCT s.tue_project_status))!=0,"saved",
			    IF(FIND_IN_SET("rejected",GROUP_CONCAT(DISTINCT s.tue_project_status))!=0,"rejected",
			    IF(FIND_IN_SET("submitted",GROUP_CONCAT(DISTINCT s.tue_project_status))!=0,"submitted",
			    IF(FIND_IN_SET("approved",GROUP_CONCAT(DISTINCT s.tue_project_status))!=0,"approved",			    
			    IF(FIND_IN_SET("enabled",GROUP_CONCAT(DISTINCT s.tue_project_status))!=0,"enabled","no_entry"))))))',
				 'wed_status' => 'IF(FIND_IN_SET("blocked",GROUP_CONCAT(DISTINCT s.wed_project_status))!=0,"blocked",
				IF(FIND_IN_SET("saved",GROUP_CONCAT(DISTINCT s.wed_project_status))!=0,"saved",
			    IF(FIND_IN_SET("rejected",GROUP_CONCAT(DISTINCT s.wed_project_status))!=0,"rejected",
			    IF(FIND_IN_SET("submitted",GROUP_CONCAT(DISTINCT s.wed_project_status))!=0,"submitted",
			    IF(FIND_IN_SET("approved",GROUP_CONCAT(DISTINCT s.wed_project_status))!=0,"approved",			 
			    IF(FIND_IN_SET("enabled",GROUP_CONCAT(DISTINCT s.wed_project_status))!=0,"enabled","no_entry"))))))',
				 'thu_status' => 'IF(FIND_IN_SET("blocked",GROUP_CONCAT(DISTINCT s.thu_project_status))!=0,"blocked",
				IF(FIND_IN_SET("saved",GROUP_CONCAT(DISTINCT s.thu_project_status))!=0,"saved",
			    IF(FIND_IN_SET("rejected",GROUP_CONCAT(DISTINCT s.thu_project_status))!=0,"rejected",
			    IF(FIND_IN_SET("submitted",GROUP_CONCAT(DISTINCT s.thu_project_status))!=0,"submitted",
			    IF(FIND_IN_SET("approved",GROUP_CONCAT(DISTINCT s.thu_project_status))!=0,"approved",			    
			    IF(FIND_IN_SET("enabled",GROUP_CONCAT(DISTINCT s.thu_project_status))!=0,"enabled","no_entry"))))))',
				 'fri_status' => 'IF(FIND_IN_SET("blocked",GROUP_CONCAT(DISTINCT s.fri_project_status))!=0,"blocked",
				IF(FIND_IN_SET("saved",GROUP_CONCAT(DISTINCT s.fri_project_status))!=0,"saved",
			    IF(FIND_IN_SET("rejected",GROUP_CONCAT(DISTINCT s.fri_project_status))!=0,"rejected",
			    IF(FIND_IN_SET("submitted",GROUP_CONCAT(DISTINCT s.fri_project_status))!=0,"submitted",
			    IF(FIND_IN_SET("approved",GROUP_CONCAT(DISTINCT s.fri_project_status))!=0,"approved",			   
			    IF(FIND_IN_SET("enabled",GROUP_CONCAT(DISTINCT s.fri_project_status))!=0,"enabled","no_entry"))))))',
				 'sat_status' => 'IF(FIND_IN_SET("blocked",GROUP_CONCAT(DISTINCT s.sat_project_status))!=0,"blocked",
				IF(FIND_IN_SET("saved",GROUP_CONCAT(DISTINCT s.sat_project_status))!=0,"saved", 
			    IF(FIND_IN_SET("rejected",GROUP_CONCAT(DISTINCT s.sat_project_status))!=0,"rejected",
			    IF(FIND_IN_SET("submitted",GROUP_CONCAT(DISTINCT s.sat_project_status))!=0,"submitted",
			    IF(FIND_IN_SET("approved",GROUP_CONCAT(DISTINCT s.sat_project_status))!=0,"approved",			    
			    IF(FIND_IN_SET("enabled",GROUP_CONCAT(DISTINCT s.sat_project_status))!=0,"enabled","no_entry"))))))' 
				))
				->where(" s.project_id is not null and s.is_active = 1 and s.emp_id =".$empId." and s.ts_year = '".$year."' and s.ts_month = '".$month."' and s.ts_week = '".$week."' ")
				->group('s.emp_id');

		$result = $this->fetchAll($select)->toArray();
		$db = Zend_Db_Table::getDefaultAdapter();	
		//print_r($result);
		$where = " emp_id =".$empId." and ts_year = '".$year."' and ts_month = '".$month."' and ts_week = '".$week."' ";
		$db->update('tm_ts_status',$result[0], $where);		
	}
	public function updateWeekStatus($empId,$year,$month,$week){
		
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('t' => 'tm_ts_status'),
		array('week_status' => "IF(FIND_IN_SET('blocked',CONCAT(sun_status,',',mon_status,',',tue_status,',',wed_status,',',thu_status,',',fri_status,',',sat_status))!=0,'blocked',
					   		   IF(FIND_IN_SET('submitted',CONCAT(sun_status,',',mon_status,',',tue_status,',',wed_status,',',thu_status,',',fri_status,',',sat_status))!=0,'submitted',
							   IF(FIND_IN_SET('rejected',CONCAT(sun_status,',',mon_status,',',tue_status,',',wed_status,',',thu_status,',',fri_status,',',sat_status))!=0,'rejected',				
							   IF(FIND_IN_SET('approved',CONCAT(sun_status,',',mon_status,',',tue_status,',',wed_status,',',thu_status,',',fri_status,',',sat_status))!=0,'approved',
							   IF(FIND_IN_SET('saved',CONCAT(sun_status,',',mon_status,',',tue_status,',',wed_status,',',thu_status,',',fri_status,',',sat_status))!=0,'saved',
							   IF(FIND_IN_SET('enabled',CONCAT(sun_status,',',mon_status,',',tue_status,',',wed_status,',',thu_status,',',fri_status,',',sat_status))!=0,'enabled','no_entry'))))))
							"))
		->where("emp_id=".$empId." and is_active=1 and ts_week = '".$week."' and ts_year = '".$year."' and ts_month = '".$month."' ")
		->group('emp_id');
	//	echo $select;
		$result = $this->fetchAll($select)->toArray();
		$db = Zend_Db_Table::getDefaultAdapter();	
		//print_r($result);
		$where = " emp_id =".$empId." and ts_year = '".$year."' and ts_month = '".$month."' and ts_week = '".$week."' ";
		$db->update('tm_ts_status',$result[0], $where);		
	}
	public function updateSubmitStatus($empId,$year,$month,$week,$dataArray) {
		
		$db = Zend_Db_Table::getDefaultAdapter();	
		//print_r($result);
		$where = " emp_id =".$empId." and ts_year = '".$year."' and ts_month = '".$month."' and ts_week = '".$week."' ";
		$db->update('tm_ts_status',$dataArray, $where);		
	}
	public function updateProjectSubmitStatus($empId,$year,$month,$week,$dataArray,$projId) {
		
		$db = Zend_Db_Table::getDefaultAdapter();	
		//print_r($result);
		$where = " is_active = 1 and emp_id =".$empId." and ts_year = '".$year."' and ts_month = '".$month."' and ts_week = '".$week."' and project_id = ".$projId;
		$db->update('tm_ts_status',$dataArray, $where);		
	}
	public function updateTimesheetStatus($empId,$year,$month,$week,$dataArray) {
		$db = Zend_Db_Table::getDefaultAdapter();	
	
		$where = " emp_id =".$empId." and ts_year = '".$year."' and ts_month = '".$month."' and ts_week = '".$week."' ";
		$db->update('tm_ts_status',$dataArray, $where);		
	}
	public function updateTimesheetNotes($empId,$year,$month,$week,$dataArray) {
		$db = Zend_Db_Table::getDefaultAdapter();	
	
		$where = " emp_id =".$empId." and ts_year = '".$year."' and ts_month = '".$month."' and ts_week = '".$week."' ";
		$db->update('tm_emp_ts_notes',$dataArray, $where);		
	}
	public function updateTimesheet($empId,$year,$month,$week,$dataArray) {
		$db = Zend_Db_Table::getDefaultAdapter();	
	
		$where = " emp_id =".$empId." and ts_year = '".$year."' and ts_month = '".$month."' and ts_week = '".$week."' ";
		$db->update('tm_emp_timesheets',$dataArray, $where);		
	}
	public function updateWeekDuration($empId,$year,$month,$week) {
		$db = Zend_Db_Table::getDefaultAdapter();	
	
		$where = " emp_id =".$empId." and ts_year = '".$year."' and ts_month = '".$month."' and ts_week = '".$week."' ";
		$sql = "update tm_emp_timesheets set week_duration = concat(FLOOR(((SUBSTRING_INDEX(sun_duration,':',1)+
					SUBSTRING_INDEX(mon_duration,':',1)+SUBSTRING_INDEX(tue_duration,':',1)
					+SUBSTRING_INDEX(wed_duration,':',1)+SUBSTRING_INDEX(thu_duration,':',1)
					+SUBSTRING_INDEX(fri_duration,':',1)+SUBSTRING_INDEX(sat_duration,':',1))*60 +
			        SUBSTRING_INDEX(sun_duration,':',-1)+SUBSTRING_INDEX(mon_duration,':',-1)
			        +SUBSTRING_INDEX(tue_duration,':',-1)+SUBSTRING_INDEX(wed_duration,':',-1)
			 		+SUBSTRING_INDEX(thu_duration,':',-1)+SUBSTRING_INDEX(fri_duration,':',-1)+SUBSTRING_INDEX(sat_duration,':',-1))/60),':',
					((SUBSTRING_INDEX(sun_duration,':',1)+SUBSTRING_INDEX(mon_duration,':',1)+SUBSTRING_INDEX(tue_duration,':',1)
					+SUBSTRING_INDEX(wed_duration,':',1)+SUBSTRING_INDEX(thu_duration,':',1)+SUBSTRING_INDEX(fri_duration,':',1)+SUBSTRING_INDEX(sat_duration,':',1))*60 +
					SUBSTRING_INDEX(sun_duration,':',-1)+SUBSTRING_INDEX(mon_duration,':',-1)+SUBSTRING_INDEX(tue_duration,':',-1)
					+SUBSTRING_INDEX(wed_duration,':',-1)+SUBSTRING_INDEX(thu_duration,':',-1)+SUBSTRING_INDEX(fri_duration,':',-1)+SUBSTRING_INDEX(sat_duration,':',-1))%60)";
		$stmt = $db->query($sql, $where);			
		$stmt->execute(); 	
	}
	
	public function getCronDetailsForMonth($year,$month) {
		
		$select = $this->select()
				->setIntegrityCheck(false)
				->from(array('c' => 'tm_mailing_list'),array('ts_start_date','ts_end_date'))
				->where("mail_type = 'block' and YEAR(ts_end_date) = '".$year."' and MONTH(ts_end_date) = '".$month."'");
			
			//echo $select;exit;					
       return $this->fetchAll($select)->toArray();
	}
	
}
