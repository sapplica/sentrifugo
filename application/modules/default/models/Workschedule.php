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
class Default_Model_Workschedule extends Zend_Db_Table_Abstract
{
	protected $_name = 'main_work_schedule';
	private $db;


	public function init()
	{
		$this->db = Zend_Db_Table::getDefaultAdapter();
	}
	
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$a='',$b='',$c='',$d='')
	{
		$searchQuery = '';
		$searchArray = array();
		$data = array();
		if($searchData != '' && $searchData!='undefined')
			{
				$searchValues = json_decode($searchData);
				foreach($searchValues as $key => $val)
				{
					$searchArray[$key] = $val;
				}
								
			}
			
		$objName = 'workschedule';
		
		$tableFields = array('action'=>'Action','businessunit_id'=>'Business Unit','department_id'=>'Department','startdate'=>'Start Date','enddate'=>'End Date','sun_duration'=>'Sunday Hours','mon_duration'=>'Monday Hours','tue_duration'=>'Tuesday Hours','wed_duration'=>'Wednesday Hours','thu_duration'=>'Thrusday Hours','fri_duration'=>'Friday Hours','sat_duration'=>'Saturday Hours');

		$tablecontent = $this->getWorkSchedule('grid',$sort, $by, $pageNo, $perPage,$searchQuery,$a,$searchArray);     

		/** retrieve names array **/
		$tmpRes = $tablecontent;
		$buDetailsObj = $deptDetailsObj = [];
		$tmpResObj = $this->fetchAll($tmpRes)->toArray();
		if(count($tmpResObj) > 0)
		{
			$startdateStr = $enddateStr = $sun_durationStr = $mon_durationStr = $tue_durationStr = $wed_durationStr = $thu_durationStr = $fri_durationStr = $sat_durationStr = $businessUnitsStr = $departmentsStr = '';
			
			for($e = 0; $e < sizeof($tmpResObj); $e++)
			{
				$startdateStr .= $tmpResObj[$e]['startdate'].","; 
				$enddateStr .= $tmpResObj[$e]['enddate'].","; 
				$sun_durationStr .= $tmpResObj[$e]['sun_duration'].","; 
				$mon_durationStr .= $tmpResObj[$e]['mon_duration'].","; 
				$tue_durationStr .= $tmpResObj[$e]['tue_duration'].","; 
				$wed_durationStr .= $tmpResObj[$e]['wed_duration'].","; 
				$thu_durationStr .= $tmpResObj[$e]['thu_duration'].","; 
				$fri_durationStr .= $tmpResObj[$e]['fri_duration'].","; 
				$sat_durationStr .= $tmpResObj[$e]['sat_duration'].","; 
				$businessUnitsStr .= $tmpResObj[$e]['businessunit_id'].","; 
				$departmentsStr .= $tmpResObj[$e]['department_id'].",";
			}
			
			if(empty($sort)) 
				$sort = 'ASC';

			if(!empty($businessUnitsStr))
			{
				$businessUnitsStr = rtrim($businessUnitsStr,",");

				$buDetailsQuery = $this->select()
								->setIntegrityCheck(false)
								->from(array('bu' => 'main_businessunits'),array('bu.id','bu.unitname'))
								->where('bu.id IN ('.$businessUnitsStr.') AND bu.isactive = 1 ')
								->order("bu.unitname");

				$buDetailsObj_tmp = $this->fetchAll($buDetailsQuery)->toArray();
				foreach ($buDetailsObj_tmp as &$row) {
					$buDetailsObj[$row['id']] = &$row;
				}
			}
			if(!empty($departmentsStr))
			{
				$departmentsStr = rtrim($departmentsStr,",");

				$deptDetailsQuery = $this->select()
								->setIntegrityCheck(false)
								->from(array('d' => 'main_departments'),array('d.id','d.deptname'))
								->where('d.id IN ('.$departmentsStr.') AND d.isactive = 1 ')
								->order("d.deptname");

				$deptDetailsObj_tmp = $this->fetchAll($deptDetailsQuery)->toArray();
				foreach ($deptDetailsObj_tmp as &$row) {
					$deptDetailsObj[$row['id']] = &$row;
				}
			}			
		}
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
			'add' =>'add',
			'call'=>$call,
			'dashboardcall'=>$dashboardcall,
			'buDetailsObj' => $buDetailsObj,
			'deptDetailsObj' => $deptDetailsObj,
		);
		return $dataTmp;
	}

	public function getWorkSchedule($con,$sort='', $by='', $pageNo='', $perPage='',$searchQuery='',$conText = '',$searchArray=array())
	{
	
		try
		{
			$columns = 'e.*';
			$where = "e.isactive = 1";
			
			if($conText)
			{
				$searchQuery1 = $searchQuery2 = '';
				
				if(!empty($searchArray))
				{
					foreach($searchArray as $key => $val)
					{
						
						if($key == 'businessunit_id'){
							$searchQuery1 = " b.id = e.businessunit_id AND b.unitname  like '%".$val."%' AND b.isactive = 1 ";
						}
						else if($key == 'department_id'){
							$searchQuery2 = " d.id = e.department_id AND d.deptname  like '%".$val."%' AND d.isactive = 1 ";
						}
					}
				}
				else
				{

				}
			
			 $res = $this->select()
					 ->setIntegrityCheck(false)
					 ->from(array('e' => $this->_name),array($columns));
					
				if(!empty($searchQuery1))
				{
				
					$res = $res->joinInner(array('b' => 'main_businessunits'),$searchQuery1,array());
				}
				if(!empty($searchQuery2))
				{
					$res = $res->joinInner(array('d' => 'main_departments'),$searchQuery2,array());
				}
				
				 $res = $res->where($where)
					->order("$by $sort")
					->limitPage($pageNo, $perPage);
			}	
			else
			{
			
			 $res = $this->select()
					->setIntegrityCheck(false)
					->from(array('e' => $this->_name),array('e.id',$columns))
					->where($where)
					->order("$by $sort")
					->limitPage($pageNo, $perPage);
			}
		
			if($con == 'grid' && !empty($pageNo) && !empty($perPage))
			{
				$this->select()->limitPage($pageNo, $perPage);
			}

			if($con == 'grid')
			{
				return $res;
			}
			else
			{
				return $this->fetchAll($res)->toArray();
			}
		}
		catch(Exception $e)
		{
			//print_r($e);
		}
	}
	
	public function getWorkScheduleById($id)
	{
		if($id)
		{
			$res = $this->select()
				->setIntegrityCheck(false)
				->from(array('e' => $this->_name),array('e.*'))
				->where('e.id=?',$id);

			return $this->fetchAll($res)->toArray();
		}
	}
	//function for get the data based on setting id 
	public function getWorkScheduleDetails($id)
	{
	
		if($id)
		{
			$res = $this->select()
				->setIntegrityCheck(false)
				->from(array('e' => $this->_name),array('e.*'))
				->joinLeft(array('b' => 'main_businessunits'),'b.id=e.businessunit_id',array('b.unitname as unitname'))
				->joinLeft(array('d' => 'main_departments'),'d.id=e.department_id',array('d.deptname as deptname'))
				->where('e.id=?',$id);

			return $this->fetchAll($res)->toArray();
		}
	}

	public function getBusinessUnits($bunitId = '')
	{
		$where = 'b.isactive = 1';
		if(!empty($bunitId))
		{
			$where .= ' AND b.id = '.$bunitId;
		}
		$res = $this->select()
				->distinct()
				->setIntegrityCheck(false)
				->from(array('b' => 'main_businessunits'),array('b.id','b.unitname'))
				->where($where);

		return $this->fetchAll($res)->toArray();
		
	}

	public function getDepartments($bunitId, $deptId = '', $con='')
	{
		if($con == 'all')
		{
			$where = (!empty($deptId)) ? ' AND d.id = '.$deptId :"";
			$res = $this->select()
				->setIntegrityCheck(false)
				->from(array('d' => 'main_departments'),array('d.*'))
				->where('d.isactive = 1 and unitid = '.$bunitId.$where);
			return $this->fetchAll($res)->toArray();
		}
		else
		{
			$db = Zend_Db_Table::getDefaultAdapter();
			$qry = 'select  distinct d.id,d.deptname from main_departments d where d.isactive = 1 and d.unitid = '.$bunitId;
			$result = $db->query($qry)->fetchAll();
			return $result;
		}
	}
	
	public function saveWorkSchedule($data,$where = '', $con = '')
	{
		if($con == 'add' && !empty($data))
		{
			$this->insert($data);
			$id = $this->getAdapter()->lastInsertId($this->_name);
			return $id;
		}
		else if($con == 'edit' && !empty($where))
		{
			$this->update($data,$where);
			return 'update';
		}
	}
	public function getActiveDepartmentIds()
	{
	  $select = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('l'=>'main_work_schedule'),array('deptid'=>'l.department_id'))
						   ->where('l.isactive = 1');  		   					   				
		return $this->fetchAll($select)->toArray(); 
	}
	
}
?>