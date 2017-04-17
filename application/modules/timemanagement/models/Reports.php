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
 * @model Reports Model
 * @author sagarsoft
 *
 */
class Timemanagement_Model_Reports extends Zend_Db_Table_Abstract
{
	/**
	 * The default table name
	 */
	protected $_name = 'tm_projects';
	/**
	 * This method is used to fetch the project details based on the user Role.
	 * 
	 * Added by Manju for reports.
	 */
	public function getProjectsListByRole(){
		$storage = new Zend_Auth_Storage_Session();
		$sessionData = $storage->read();
		$result = array();
		$tm_role = Zend_Registry::get('tm_role');
		if($tm_role == "Admin") {
			$select = $this->select()
						   ->setIntegrityCheck(false)
						   ->from(array('p'=>$this->_name),array('p.id','project_name'))
						   ->where('p.is_active = 1 ')
						   ->order('p.project_name asc');
			$result = $this->fetchAll($select)->toArray();
		}else{
			$select = $this->select()
							->setIntegrityCheck(false)
							->from(array('p' => $this->_name),array('id'=>'p.id','project_name' => 'p.project_name',))
							->joinLeft(array('tpe'=>'tm_project_employees'), 'tpe.project_id=p.id AND tpe.is_active=1',array())
							->where('p.is_active=1 AND tpe.emp_id ='.$sessionData['id'])
							->order("p.project_name asc")
							->group('p.id');
			$result = $this->fetchAll($select)->toArray();
		}
		return $result;
		
	}
	
	public function getEmpList()
	{
		$select = $this->select()
					->setIntegrityCheck(false)
					->from(array('e'=>'main_employees_summary'), array('id'=>'e.user_id','text'=>'e.userfullname','pic'=>'e.profileimg'))
					->where("e.isactive = 1 ")
					->order("e.userfullname ASC")
					->distinct('e.id');

		return $this->fetchAll($select)->toArray();
	}
        
	public function getEmployeeReportsbyProjectId($sort, $by, $perPage, $pageNo, $searchData, $call, $dashboardcall, 
			$start_date, $end_date, $projid,$org_start_date,$org_end_date,$param=''){
		
		$searchQuery = '';
		$searchArray = array();
		$data = array();

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
			
		$objName = 'reports';

		//email,phone_no,poc,address,country_id,state_id,created_by
		$tableFields = array(
					//'action'=>'Action',
					'userfullname' => 'Employee',
					//'project_type' => 'Project Type',
					'duration' => 'Hours',
		);

		$tablecontent = $this->getEmployeeReportsData($sort, $by, $pageNo, $perPage, $searchQuery, $start_date, $end_date, $projid, $param);
		
		$dataTmp = array(
				'sort' => $sort,
				'by' => $by,
				'pageNo' => $pageNo,
				'perPage' => $perPage,				
				'tablecontent' => $tablecontent,
				'objectname' => $objName,
				'extra' => array(),
				'tableheader' => $tableFields,
				'jsGridFnName' => 'getAjaxgridData',
				'jsFillFnName' => '',
				'searchArray' => $searchArray,
				'call'=>$call,
				'dashboardcall'=>$dashboardcall,
				'menuName' => 'Employee Reports',
				'otheraction' => 'employeereports',
				'projectId' => $projid,
				'start_date' => $org_start_date,
				'end_date' => $org_end_date,
			);
			return $dataTmp;
 	 }
	

	/**
	 * This will fetch all the active client details.
	 *
	 * @param string $sort
	 * @param string $by
	 * @param number $pageNo
	 * @param number $perPage
	 * @param string $searchQuery
	 *
	 * @return array $EmployeeReportsData
	 */
	public function getEmployeeReportsData($sort, $by, $pageNo, $perPage, $searchQuery,$start_date, $end_date, $projid, $param="",$flag="")
	{
		$andwhere = ' AND (1=1)';
		if($start_date != "")
		{
			if($end_date == "")
			{
				//$end_date = date('%Y-%m-%d %H:%i:%s');
				$end_date = date('%Y-%m-%d');
			}
			$start_dates=strtotime($start_date);
			$sd_month=date("m",$start_dates);
			$sd_year=date("Y",$start_dates);
			
			$end_dates=strtotime($end_date);
			$ed_month=date("m",$end_dates);
			$ed_year=date("Y",$end_dates);
			
			$andwhere = " AND et.ts_year >= ".$sd_year." AND et.ts_year <=".$ed_year." AND et.ts_month >= ".$sd_month." AND et.ts_month <= ".$ed_month;
			$duration = "";
			$duration_sort = "";
			if($param=="" || $param=="undefined" || $param=="Last 7 days")
			{
				$duration = "CONCAT(FLOOR(SUM( TIME_TO_SEC( IF(sun_date BETWEEN '".$start_date."' AND '".$end_date."',sun_duration,'00:00')) +
				TIME_TO_SEC( IF(mon_date BETWEEN '".$start_date."' AND '".$end_date."',mon_duration,'00:00')) +
				TIME_TO_SEC( IF(tue_date BETWEEN '".$start_date."' AND '".$end_date."',tue_duration,'00:00')) +
				TIME_TO_SEC( IF(wed_date BETWEEN '".$start_date."' AND '".$end_date."',wed_duration,'00:00')) +
				TIME_TO_SEC( IF(thu_date BETWEEN '".$start_date."' AND '".$end_date."',thu_duration,'00:00')) +
				TIME_TO_SEC( IF(fri_date BETWEEN '".$start_date."' AND '".$end_date."',fri_duration,'00:00')) +
				TIME_TO_SEC( IF(sat_date BETWEEN '".$start_date."' AND '".$end_date."',sat_duration,'00:00')))/3600),':',
				LPAD(FLOOR(SUM( TIME_TO_SEC( IF(sun_date BETWEEN '".$start_date."' AND '".$end_date."',sun_duration,'00:00')) +
				TIME_TO_SEC( IF(mon_date BETWEEN '".$start_date."' AND '".$end_date."',mon_duration,'00:00')) +
				TIME_TO_SEC( IF(tue_date BETWEEN '".$start_date."' AND '".$end_date."',tue_duration,'00:00')) +
				TIME_TO_SEC( IF(wed_date BETWEEN '".$start_date."' AND '".$end_date."',wed_duration,'00:00')) +
				TIME_TO_SEC( IF(thu_date BETWEEN '".$start_date."' AND '".$end_date."',thu_duration,'00:00')) +
				TIME_TO_SEC( IF(fri_date BETWEEN '".$start_date."' AND '".$end_date."',fri_duration,'00:00')) +
				TIME_TO_SEC( IF(sat_date BETWEEN '".$start_date."' AND '".$end_date."',sat_duration,'00:00')))/60)%60,2,'0'))";
				
				$duration_sort = "TIME_TO_SEC( IF(mon_date BETWEEN '".$start_date."' AND '".$end_date."',mon_duration,'00:00')) +
				TIME_TO_SEC( IF(tue_date BETWEEN '".$start_date."' AND '".$end_date."',tue_duration,'00:00')) +
				TIME_TO_SEC( IF(wed_date BETWEEN '".$start_date."' AND '".$end_date."',wed_duration,'00:00')) +
				TIME_TO_SEC( IF(thu_date BETWEEN '".$start_date."' AND '".$end_date."',thu_duration,'00:00')) +
				TIME_TO_SEC( IF(fri_date BETWEEN '".$start_date."' AND '".$end_date."',fri_duration,'00:00')) +
				TIME_TO_SEC( IF(sat_date BETWEEN '".$start_date."' AND '".$end_date."',sat_duration,'00:00'))";
				
				$andwhere =" AND (sun_date BETWEEN '".$start_date."' AND '".$end_date."' OR mon_date BETWEEN '".$start_date."' AND '".$end_date."'
				OR tue_date BETWEEN '".$start_date."' AND '".$end_date."' OR wed_date BETWEEN '".$start_date."' AND '".$end_date."'
				OR thu_date BETWEEN '".$start_date."' AND '".$end_date."' OR fri_date BETWEEN '".$start_date."' AND '".$end_date."'
				OR sat_date BETWEEN '".$start_date."' AND '".$end_date."')";
			}
			else if($param=='Today')
			{
				$duration = "CONCAT(FLOOR(SUM( TIME_TO_SEC( IF(sun_date = '".$start_date."',sun_duration,'00:00')) +
				TIME_TO_SEC( IF(mon_date = '".$start_date."' ,mon_duration,'00:00')) +
				TIME_TO_SEC( IF(tue_date = '".$start_date."' ,tue_duration,'00:00')) +
				TIME_TO_SEC( IF(wed_date = '".$start_date."' ,wed_duration,'00:00')) +
				TIME_TO_SEC( IF(thu_date = '".$start_date."' ,thu_duration,'00:00')) +
				TIME_TO_SEC( IF(fri_date = '".$start_date."' ,fri_duration,'00:00')) +
				TIME_TO_SEC( IF(sat_date = '".$start_date."' ,sat_duration,'00:00')))/3600),':',
				LPAD(FLOOR(SUM( TIME_TO_SEC( IF(sun_date = '".$start_date."' ,sun_duration,'00:00')) +
				TIME_TO_SEC( IF(mon_date = '".$start_date."' ,mon_duration,'00:00')) +
				TIME_TO_SEC( IF(tue_date = '".$start_date."' ,tue_duration,'00:00')) +
				TIME_TO_SEC( IF(wed_date = '".$start_date."' ,wed_duration,'00:00')) +
				TIME_TO_SEC( IF(thu_date = '".$start_date."' ,thu_duration,'00:00')) +
				TIME_TO_SEC( IF(fri_date = '".$start_date."' ,fri_duration,'00:00')) +
				TIME_TO_SEC( IF(sat_date = '".$start_date."' ,sat_duration,'00:00')))/60)%60,2,'0'))";
				
				$duration_sort = "TIME_TO_SEC( IF(mon_date = '".$start_date."' ,mon_duration,'00:00')) +
				TIME_TO_SEC( IF(tue_date = '".$start_date."' ,tue_duration,'00:00')) +
				TIME_TO_SEC( IF(wed_date = '".$start_date."' ,wed_duration,'00:00')) +
				TIME_TO_SEC( IF(thu_date = '".$start_date."' ,thu_duration,'00:00')) +
				TIME_TO_SEC( IF(fri_date = '".$start_date."' ,fri_duration,'00:00')) +
				TIME_TO_SEC( IF(sat_date = '".$start_date."' ,sat_duration,'00:00'))";
				
				$andwhere = " AND (sun_date = '".$start_date."' OR mon_date = '".$start_date."' OR tue_date = '".$start_date."' OR wed_date = '".$start_date."' OR thu_date = '".$start_date."' OR fri_date = '".$start_date."' OR sat_date = '".$start_date."')";
			}
			else
			{
				$duration = "concat(floor(SUM( TIME_TO_SEC( et.week_duration ))/3600),':',lpad(floor(SUM( TIME_TO_SEC( et.week_duration ))/60)%60,2,'0'))";
				$andwhere = " AND et.ts_year >= ".$sd_year." AND et.ts_year <=".$ed_year." AND et.ts_month >= ".$sd_month." AND et.ts_month <= ".$ed_month;
				$duration_sort = "SUM(TIME_TO_SEC(et.week_duration))";
			}
			
			// if($param!="" && $param!="undefined" && $param!="Today" && $param!="Last 7 days")
			// {
				// $andwhere = " AND et.ts_year = ".$sd_year." AND et.ts_month >= ".$sd_month." AND et.ts_month <= ".$sd_month;
			// }
			//	$andwhere = " AND et.created BETWEEN STR_TO_DATE('".$start_date."','%Y-%m-%d %H:%i:%s') AND STR_TO_DATE('".$end_date."','%Y-%m-%d %H:%i:%s')";
		}
		
		if($searchQuery){
			$andwhere .= " AND ".$searchQuery;	
		}
		
		if($projid != ''){
			$andwhere .= " AND p.id = '".$projid."'";
		}
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $this->select()
			   		   ->setIntegrityCheck(false)
					   ->from(array('et' => 'tm_emp_timesheets'),array('e.userfullname','p.project_type','userId'=>'et.emp_id',
				                                'duration'=>$duration,'duration_sort'=>$duration_sort))  
					   ->joinInner(array('pt'=>'tm_project_tasks'), 'pt.id = et.project_task_id',array())
					   ->joinInner(array('p'=>'tm_projects'), 'p.id = pt.project_id',array())
					   ->joinInner(array('e'=>'main_employees_summary'), 'e.user_id = et.emp_id',array())
					   ->where('et.is_active=1 and pt.is_active =1 and p.is_active = 1 and e.isactive = 1'.$andwhere)
					   ->order("$by $sort")
					   ->group('et.emp_id')
					   ->limitPage($pageNo, $perPage);
					   //echo $select;
		if(!empty($flag))
		{
			return $this->fetchAll($select)->toArray(); 
		}
		return $select;
	}
	
	function getProjectReportsbyEmployeeId($sort, $by, $perPage, $pageNo, $searchData, $call, $dashboardcall,
			 $start_date, $end_date, $empid,$org_start_date,$org_end_date,$param=""){

		$searchQuery = '';
		$searchArray = array();
		$data = array();

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

		$objName = 'reports';

		//email,phone_no,poc,address,country_id,state_id,created_by
		$tableFields = array(
		//'action'=>'Action',
					'project_name' => 'Project',
					'project_type' => 'Project Type',
					'duration' => 'Hours',
		);

		$tablecontent = $this->getProjectReportsData($sort, $by, $pageNo, $perPage, $searchQuery, $start_date, $end_date, $empid, $param);

		$dataTmp = array(
				'sort' => $sort,
				'by' => $by,
				'pageNo' => $pageNo,
				'perPage' => $perPage,				
				'tablecontent' => $tablecontent,
				'objectname' => $objName,
				'extra' => array(),
				'tableheader' => $tableFields,
				'jsGridFnName' => 'getAjaxgridData',
				'jsFillFnName' => '',
				'searchArray' => $searchArray,
				'call'=>$call,
				'dashboardcall'=>$dashboardcall,
				'menuName' => 'Project Reports',
				'otheraction' => 'projectsreports',
				'emp_id' => $empid,
				'start_date' => $org_start_date,
				'end_date' => $org_end_date,
		);
		return $dataTmp;
		
	}
	
	function getProjectReportsData($sort, $by, $pageNo, $perPage, $searchQuery, $start_date, $end_date, $empid, $param="",$flag=""){
		
		$andwhere = " AND (1=1)";
		if($start_date != "")
		{
			if($end_date == "")
			{
				//$end_date = date('%Y-%m-%d %H:%i:%s');
				$end_date = date('%Y-%m-%d');
			}
			$start_dates=strtotime($start_date);
			$sd_month=date("m",$start_dates);
			$sd_year=date("Y",$start_dates);
			
			$end_dates=strtotime($end_date);
			$ed_month=date("m",$end_dates);
			$ed_year=date("Y",$end_dates);
			$andwhere = " AND et.ts_year >= ".$sd_year." AND et.ts_year <=".$ed_year." AND et.ts_month >= ".$sd_month." AND et.ts_month <= ".$ed_month;
			
			$duration="";
			$duration_sort ="";
			//$andwhere = " AND et.created BETWEEN STR_TO_DATE('".$start_date."','%Y-%m-%d %H:%i:%s') AND STR_TO_DATE('".$end_date."','%Y-%m-%d %H:%i:%s')";
			if($param=="" || $param=="undefined" || $param=="Last 7 days")
			{
				$duration = "CONCAT(FLOOR(SUM( TIME_TO_SEC( IF(sun_date BETWEEN '".$start_date."' AND '".$end_date."',sun_duration,'00:00')) +
				TIME_TO_SEC( IF(mon_date BETWEEN '".$start_date."' AND '".$end_date."',mon_duration,'00:00')) +
				TIME_TO_SEC( IF(tue_date BETWEEN '".$start_date."' AND '".$end_date."',tue_duration,'00:00')) +
				TIME_TO_SEC( IF(wed_date BETWEEN '".$start_date."' AND '".$end_date."',wed_duration,'00:00')) +
				TIME_TO_SEC( IF(thu_date BETWEEN '".$start_date."' AND '".$end_date."',thu_duration,'00:00')) +
				TIME_TO_SEC( IF(fri_date BETWEEN '".$start_date."' AND '".$end_date."',fri_duration,'00:00')) +
				TIME_TO_SEC( IF(sat_date BETWEEN '".$start_date."' AND '".$end_date."',sat_duration,'00:00')))/3600),':',
				LPAD(FLOOR(SUM( TIME_TO_SEC( IF(sun_date BETWEEN '".$start_date."' AND '".$end_date."',sun_duration,'00:00')) +
				TIME_TO_SEC( IF(mon_date BETWEEN '".$start_date."' AND '".$end_date."',mon_duration,'00:00')) +
				TIME_TO_SEC( IF(tue_date BETWEEN '".$start_date."' AND '".$end_date."',tue_duration,'00:00')) +
				TIME_TO_SEC( IF(wed_date BETWEEN '".$start_date."' AND '".$end_date."',wed_duration,'00:00')) +
				TIME_TO_SEC( IF(thu_date BETWEEN '".$start_date."' AND '".$end_date."',thu_duration,'00:00')) +
				TIME_TO_SEC( IF(fri_date BETWEEN '".$start_date."' AND '".$end_date."',fri_duration,'00:00')) +
				TIME_TO_SEC( IF(sat_date BETWEEN '".$start_date."' AND '".$end_date."',sat_duration,'00:00')))/60)%60,2,'0'))";
				
				$duration_sort = "TIME_TO_SEC( IF(mon_date BETWEEN '".$start_date."' AND '".$end_date."',mon_duration,'00:00')) +
				TIME_TO_SEC( IF(tue_date BETWEEN '".$start_date."' AND '".$end_date."',tue_duration,'00:00')) +
				TIME_TO_SEC( IF(wed_date BETWEEN '".$start_date."' AND '".$end_date."',wed_duration,'00:00')) +
				TIME_TO_SEC( IF(thu_date BETWEEN '".$start_date."' AND '".$end_date."',thu_duration,'00:00')) +
				TIME_TO_SEC( IF(fri_date BETWEEN '".$start_date."' AND '".$end_date."',fri_duration,'00:00')) +
				TIME_TO_SEC( IF(sat_date BETWEEN '".$start_date."' AND '".$end_date."',sat_duration,'00:00'))";
				
				$andwhere =" AND (sun_date BETWEEN '".$start_date."' AND '".$end_date."' OR mon_date BETWEEN '".$start_date."' AND '".$end_date."'
				OR tue_date BETWEEN '".$start_date."' AND '".$end_date."' OR wed_date BETWEEN '".$start_date."' AND '".$end_date."'
				OR thu_date BETWEEN '".$start_date."' AND '".$end_date."' OR fri_date BETWEEN '".$start_date."' AND '".$end_date."'
				OR sat_date BETWEEN '".$start_date."' AND '".$end_date."')";
			}
			else if($param=='Today')
			{
				$duration = "CONCAT(FLOOR(SUM( TIME_TO_SEC( IF(sun_date = '".$start_date."',sun_duration,'00:00')) +
				TIME_TO_SEC( IF(mon_date = '".$start_date."' ,mon_duration,'00:00')) +
				TIME_TO_SEC( IF(tue_date = '".$start_date."' ,tue_duration,'00:00')) +
				TIME_TO_SEC( IF(wed_date = '".$start_date."' ,wed_duration,'00:00')) +
				TIME_TO_SEC( IF(thu_date = '".$start_date."' ,thu_duration,'00:00')) +
				TIME_TO_SEC( IF(fri_date = '".$start_date."' ,fri_duration,'00:00')) +
				TIME_TO_SEC( IF(sat_date = '".$start_date."' ,sat_duration,'00:00')))/3600),':',
				LPAD(FLOOR(SUM( TIME_TO_SEC( IF(sun_date = '".$start_date."' ,sun_duration,'00:00')) +
				TIME_TO_SEC( IF(mon_date = '".$start_date."' ,mon_duration,'00:00')) +
				TIME_TO_SEC( IF(tue_date = '".$start_date."' ,tue_duration,'00:00')) +
				TIME_TO_SEC( IF(wed_date = '".$start_date."' ,wed_duration,'00:00')) +
				TIME_TO_SEC( IF(thu_date = '".$start_date."' ,thu_duration,'00:00')) +
				TIME_TO_SEC( IF(fri_date = '".$start_date."' ,fri_duration,'00:00')) +
				TIME_TO_SEC( IF(sat_date = '".$start_date."' ,sat_duration,'00:00')))/60)%60,2,'0'))";
				
				$duration_sort = "TIME_TO_SEC( IF(mon_date = '".$start_date."' ,mon_duration,'00:00')) +
				TIME_TO_SEC( IF(tue_date = '".$start_date."' ,tue_duration,'00:00')) +
				TIME_TO_SEC( IF(wed_date = '".$start_date."' ,wed_duration,'00:00')) +
				TIME_TO_SEC( IF(thu_date = '".$start_date."' ,thu_duration,'00:00')) +
				TIME_TO_SEC( IF(fri_date = '".$start_date."' ,fri_duration,'00:00')) +
				TIME_TO_SEC( IF(sat_date = '".$start_date."' ,sat_duration,'00:00'))";
				
				$andwhere = " AND (sun_date = '".$start_date."' OR mon_date = '".$start_date."' OR tue_date = '".$start_date."' OR wed_date = '".$start_date."' OR thu_date = '".$start_date."' OR fri_date = '".$start_date."' OR sat_date = '".$start_date."')";
			}
			else
			{
				$duration = "concat(floor(SUM( TIME_TO_SEC( et.week_duration ))/3600),':',lpad(floor(SUM( TIME_TO_SEC( et.week_duration ))/60)%60,2,'0'))";
				$andwhere = " AND et.ts_year >= ".$sd_year." AND et.ts_year <=".$ed_year." AND et.ts_month >= ".$sd_month." AND et.ts_month <= ".$ed_month;
				$duration_sort = "SUM(TIME_TO_SEC(et.week_duration))";
			}
		}
		
		if($searchQuery){
			$andwhere .= " AND ".$searchQuery;	
		}

		if($empid != "")
		{
			$andwhere .= ' AND et.emp_id = '.$empid;
		}
		//'duration'=>'concat(floor(SUM( TIME_TO_SEC( et.week_duration ))/3600),":",lpad(floor(SUM( TIME_TO_SEC( et.week_duration ))/60)%60,2,"0"))'
		//'SUM(TIME_TO_SEC(et.week_duration))'
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('et' => 'tm_emp_timesheets'),array('p.project_name','proj_category'=>'p.project_type','p.id','project_type'=>'IF(p.project_type="billable","Billable",IF(p.project_type="non_billable","Non billable","Revenue generation"))',
                                'duration'=>$duration,'duration_sort'=>$duration_sort))  
		->joinInner(array('pt'=>'tm_project_tasks'), 'pt.id = et.project_task_id',array())
		->joinInner(array('p'=>'tm_projects'), 'p.id = pt.project_id and p.id = et.project_id',array())
		->joinInner(array('e'=>'main_employees_summary'), 'e.user_id = et.emp_id',array())
		->joinLeft(array('pm'=>'tm_project_employees'), 'p.id = pm.project_id and pm.emp_id = et.emp_id ',array())
		//->joinLeft(array('pm'=>new Zend_Db_Expr('(SELECT project_id,GROUP_CONCAT(emp_id) as manager_ids FROM tm_project_employees 
		//WHERE is_active=1 and emp_type = \'manager\' GROUP BY project_id)')), 'pm.project_id = pt.project_id',array())
		->where('et.is_active=1 '.$andwhere)
		->order("$by $sort")
		->group('p.id')
		->limitPage($pageNo, $perPage);
		if(!empty($flag))
		{
			return $this->fetchAll($select)->toArray(); 
		}
		//echo $select;//exit;
		return $select;
	}
	public function getEmpProjDuration($empId,$start_date,$end_date,$project_id,$param)
	{
		$andwhere = '';
		if($start_date != "")
		{
			if($end_date == "")
			{
				//$end_date = date('%Y-%m-%d %H:%i:%s');
				$end_date = date('%Y-%m-%d');
			}
			$start_dates=strtotime($start_date);
			$sd_month=date("m",$start_dates);
			$sd_year=date("Y",$start_dates);
			
			$end_dates=strtotime($end_date);
			$ed_month=date("m",$end_dates);
			$ed_year=date("Y",$end_dates);
			
			$duration="";
			$andwhere = " AND et.ts_year >= ".$sd_year." AND et.ts_year <=".$ed_year." AND et.ts_month >= ".$sd_month." AND et.ts_month <= ".$ed_month;
			//$andwhere = " AND et.created BETWEEN STR_TO_DATE('".$start_date."','%Y-%m-%d %H:%i:%s') AND STR_TO_DATE('".$end_date."','%Y-%m-%d %H:%i:%s')";
			
			if($param=="" || $param=="undefined" || $param=="Last 7 days")
			{
				$duration = "CONCAT(FLOOR(SUM( TIME_TO_SEC( IF(sun_date BETWEEN '".$start_date."' AND '".$end_date."',sun_duration,'00:00')) +
				TIME_TO_SEC( IF(mon_date BETWEEN '".$start_date."' AND '".$end_date."',mon_duration,'00:00')) +
				TIME_TO_SEC( IF(tue_date BETWEEN '".$start_date."' AND '".$end_date."',tue_duration,'00:00')) +
				TIME_TO_SEC( IF(wed_date BETWEEN '".$start_date."' AND '".$end_date."',wed_duration,'00:00')) +
				TIME_TO_SEC( IF(thu_date BETWEEN '".$start_date."' AND '".$end_date."',thu_duration,'00:00')) +
				TIME_TO_SEC( IF(fri_date BETWEEN '".$start_date."' AND '".$end_date."',fri_duration,'00:00')) +
				TIME_TO_SEC( IF(sat_date BETWEEN '".$start_date."' AND '".$end_date."',sat_duration,'00:00')))/3600),':',
				LPAD(FLOOR(SUM( TIME_TO_SEC( IF(sun_date BETWEEN '".$start_date."' AND '".$end_date."',sun_duration,'00:00')) +
				TIME_TO_SEC( IF(mon_date BETWEEN '".$start_date."' AND '".$end_date."',mon_duration,'00:00')) +
				TIME_TO_SEC( IF(tue_date BETWEEN '".$start_date."' AND '".$end_date."',tue_duration,'00:00')) +
				TIME_TO_SEC( IF(wed_date BETWEEN '".$start_date."' AND '".$end_date."',wed_duration,'00:00')) +
				TIME_TO_SEC( IF(thu_date BETWEEN '".$start_date."' AND '".$end_date."',thu_duration,'00:00')) +
				TIME_TO_SEC( IF(fri_date BETWEEN '".$start_date."' AND '".$end_date."',fri_duration,'00:00')) +
				TIME_TO_SEC( IF(sat_date BETWEEN '".$start_date."' AND '".$end_date."',sat_duration,'00:00')))/60)%60,2,'0'))";
				
				
				$andwhere =" AND (sun_date BETWEEN '".$start_date."' AND '".$end_date."' OR mon_date BETWEEN '".$start_date."' AND '".$end_date."'
				OR tue_date BETWEEN '".$start_date."' AND '".$end_date."' OR wed_date BETWEEN '".$start_date."' AND '".$end_date."'
				OR thu_date BETWEEN '".$start_date."' AND '".$end_date."' OR fri_date BETWEEN '".$start_date."' AND '".$end_date."'
				OR sat_date BETWEEN '".$start_date."' AND '".$end_date."')";
			}
			else if($param=='Today')
			{
				$duration = "CONCAT(FLOOR(SUM( TIME_TO_SEC( IF(sun_date = '".$start_date."',sun_duration,'00:00')) +
				TIME_TO_SEC( IF(mon_date = '".$start_date."' ,mon_duration,'00:00')) +
				TIME_TO_SEC( IF(tue_date = '".$start_date."' ,tue_duration,'00:00')) +
				TIME_TO_SEC( IF(wed_date = '".$start_date."' ,wed_duration,'00:00')) +
				TIME_TO_SEC( IF(thu_date = '".$start_date."' ,thu_duration,'00:00')) +
				TIME_TO_SEC( IF(fri_date = '".$start_date."' ,fri_duration,'00:00')) +
				TIME_TO_SEC( IF(sat_date = '".$start_date."' ,sat_duration,'00:00')))/3600),':',
				LPAD(FLOOR(SUM( TIME_TO_SEC( IF(sun_date = '".$start_date."' ,sun_duration,'00:00')) +
				TIME_TO_SEC( IF(mon_date = '".$start_date."' ,mon_duration,'00:00')) +
				TIME_TO_SEC( IF(tue_date = '".$start_date."' ,tue_duration,'00:00')) +
				TIME_TO_SEC( IF(wed_date = '".$start_date."' ,wed_duration,'00:00')) +
				TIME_TO_SEC( IF(thu_date = '".$start_date."' ,thu_duration,'00:00')) +
				TIME_TO_SEC( IF(fri_date = '".$start_date."' ,fri_duration,'00:00')) +
				TIME_TO_SEC( IF(sat_date = '".$start_date."' ,sat_duration,'00:00')))/60)%60,2,'0'))";
				
				$andwhere = " AND (sun_date = '".$start_date."' OR mon_date = '".$start_date."' OR tue_date = '".$start_date."' OR wed_date = '".$start_date."' OR thu_date = '".$start_date."' OR fri_date = '".$start_date."' OR sat_date = '".$start_date."')";
			}
			else
			{
				$duration = "concat(floor(SUM( TIME_TO_SEC( et.week_duration ))/3600),':',lpad(floor(SUM( TIME_TO_SEC( et.week_duration ))/60)%60,2,'0'))";
				$andwhere = " AND et.ts_year >= ".$sd_year." AND et.ts_year <=".$ed_year." AND et.ts_month >= ".$sd_month." AND et.ts_month <= ".$ed_month;
			}
		}
		if($project_id != ''){
			$andwhere .= " AND p.id = '".$project_id."'";
		}
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $this->select()
			   		   ->setIntegrityCheck(false)
					   ->from(array('et' => 'tm_emp_timesheets'),array('p.project_name','et.project_id','userId'=>'et.emp_id','duration'=>$duration))  
					   ->joinInner(array('p'=>'tm_projects'), 'p.id = et.project_id',array())
					   ->where('et.is_active=1  and p.is_active = 1 '.$andwhere.' and et.emp_id = '.$empId)
					   ->group('et.project_id');
					  // echo $select;
		return $this->fetchAll($select)->toArray();
	}
	public function getProjTaskDuration($empId,$start_date,$end_date,$project_id,$param)
	{
		$andwhere = " AND (1=1)";
		if($start_date != "")
		{
			if($end_date == "")
			{
				//$end_date = date('%Y-%m-%d %H:%i:%s');
				$end_date = date('%Y-%m-%d');
			}
			$start_dates=strtotime($start_date);
			$sd_month=date("m",$start_dates);
			$sd_year=date("Y",$start_dates);
			
			$end_dates=strtotime($end_date);
			$ed_month=date("m",$end_dates);
			$ed_year=date("Y",$end_dates);
			
			
			$andwhere = " AND et.ts_year >= ".$sd_year." AND et.ts_year <=".$ed_year." AND et.ts_month >= ".$sd_month." AND et.ts_month <= ".$ed_month;
			//$andwhere = " AND et.created BETWEEN STR_TO_DATE('".$start_date."','%Y-%m-%d %H:%i:%s') AND STR_TO_DATE('".$end_date."','%Y-%m-%d %H:%i:%s')";
			$duration="";
			if($param=="" || $param=="undefined" || $param=="Last 7 days")
			{
				$duration = "CONCAT(FLOOR(SUM( TIME_TO_SEC( IF(sun_date BETWEEN '".$start_date."' AND '".$end_date."',sun_duration,'00:00')) +
				TIME_TO_SEC( IF(mon_date BETWEEN '".$start_date."' AND '".$end_date."',mon_duration,'00:00')) +
				TIME_TO_SEC( IF(tue_date BETWEEN '".$start_date."' AND '".$end_date."',tue_duration,'00:00')) +
				TIME_TO_SEC( IF(wed_date BETWEEN '".$start_date."' AND '".$end_date."',wed_duration,'00:00')) +
				TIME_TO_SEC( IF(thu_date BETWEEN '".$start_date."' AND '".$end_date."',thu_duration,'00:00')) +
				TIME_TO_SEC( IF(fri_date BETWEEN '".$start_date."' AND '".$end_date."',fri_duration,'00:00')) +
				TIME_TO_SEC( IF(sat_date BETWEEN '".$start_date."' AND '".$end_date."',sat_duration,'00:00')))/3600),':',
				LPAD(FLOOR(SUM( TIME_TO_SEC( IF(sun_date BETWEEN '".$start_date."' AND '".$end_date."',sun_duration,'00:00')) +
				TIME_TO_SEC( IF(mon_date BETWEEN '".$start_date."' AND '".$end_date."',mon_duration,'00:00')) +
				TIME_TO_SEC( IF(tue_date BETWEEN '".$start_date."' AND '".$end_date."',tue_duration,'00:00')) +
				TIME_TO_SEC( IF(wed_date BETWEEN '".$start_date."' AND '".$end_date."',wed_duration,'00:00')) +
				TIME_TO_SEC( IF(thu_date BETWEEN '".$start_date."' AND '".$end_date."',thu_duration,'00:00')) +
				TIME_TO_SEC( IF(fri_date BETWEEN '".$start_date."' AND '".$end_date."',fri_duration,'00:00')) +
				TIME_TO_SEC( IF(sat_date BETWEEN '".$start_date."' AND '".$end_date."',sat_duration,'00:00')))/60)%60,2,'0'))";
				
				
				$andwhere =" AND (sun_date BETWEEN '".$start_date."' AND '".$end_date."' OR mon_date BETWEEN '".$start_date."' AND '".$end_date."'
				OR tue_date BETWEEN '".$start_date."' AND '".$end_date."' OR wed_date BETWEEN '".$start_date."' AND '".$end_date."'
				OR thu_date BETWEEN '".$start_date."' AND '".$end_date."' OR fri_date BETWEEN '".$start_date."' AND '".$end_date."'
				OR sat_date BETWEEN '".$start_date."' AND '".$end_date."')";
			}
			else if($param=='Today')
			{
				$duration = "CONCAT(FLOOR(SUM( TIME_TO_SEC( IF(sun_date = '".$start_date."',sun_duration,'00:00')) +
				TIME_TO_SEC( IF(mon_date = '".$start_date."' ,mon_duration,'00:00')) +
				TIME_TO_SEC( IF(tue_date = '".$start_date."' ,tue_duration,'00:00')) +
				TIME_TO_SEC( IF(wed_date = '".$start_date."' ,wed_duration,'00:00')) +
				TIME_TO_SEC( IF(thu_date = '".$start_date."' ,thu_duration,'00:00')) +
				TIME_TO_SEC( IF(fri_date = '".$start_date."' ,fri_duration,'00:00')) +
				TIME_TO_SEC( IF(sat_date = '".$start_date."' ,sat_duration,'00:00')))/3600),':',
				LPAD(FLOOR(SUM( TIME_TO_SEC( IF(sun_date = '".$start_date."' ,sun_duration,'00:00')) +
				TIME_TO_SEC( IF(mon_date = '".$start_date."' ,mon_duration,'00:00')) +
				TIME_TO_SEC( IF(tue_date = '".$start_date."' ,tue_duration,'00:00')) +
				TIME_TO_SEC( IF(wed_date = '".$start_date."' ,wed_duration,'00:00')) +
				TIME_TO_SEC( IF(thu_date = '".$start_date."' ,thu_duration,'00:00')) +
				TIME_TO_SEC( IF(fri_date = '".$start_date."' ,fri_duration,'00:00')) +
				TIME_TO_SEC( IF(sat_date = '".$start_date."' ,sat_duration,'00:00')))/60)%60,2,'0'))";
				
				
				
				$andwhere = " AND (sun_date = '".$start_date."' OR mon_date = '".$start_date."' OR tue_date = '".$start_date."' OR wed_date = '".$start_date."' OR thu_date = '".$start_date."' OR fri_date = '".$start_date."' OR sat_date = '".$start_date."')";
			}
			else
			{
				$duration = "concat(floor(SUM( TIME_TO_SEC( et.week_duration ))/3600),':',lpad(floor(SUM( TIME_TO_SEC( et.week_duration ))/60)%60,2,'0'))";
				$andwhere = " AND et.ts_year >= ".$sd_year." AND et.ts_year <=".$ed_year." AND et.ts_month >= ".$sd_month." AND et.ts_month <= ".$ed_month;
			}
		}

		if($empId != "")
		{
			$andwhere .= ' AND et.emp_id = '.$empId;
		}
		
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('et' => 'tm_emp_timesheets'),array('p.project_name','t.task',
                                'duration'=>$duration))  
		->joinInner(array('pt'=>'tm_project_tasks'), 'pt.id = et.project_task_id',array())
		->joinInner(array('t'=>'tm_tasks'), 't.id = pt.task_id',array())
		->joinInner(array('p'=>'tm_projects'), 'p.id = pt.project_id and p.id = et.project_id',array())
		->joinInner(array('e'=>'main_employees_summary'), 'e.user_id = et.emp_id',array())
		->joinLeft(array('pm'=>'tm_project_employees'), 'p.id = pm.project_id and pm.emp_id = et.emp_id ',array())
		->where('et.is_active=1 and p.id='.$project_id.' '.$andwhere)
		->group('t.id');
		//echo $select;//exit;
		return $this->fetchAll($select)->toArray();
	}
}