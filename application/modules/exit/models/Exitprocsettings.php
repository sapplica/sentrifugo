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
class Exit_Model_Exitprocsettings extends Zend_Db_Table_Abstract
{
	protected $_name = 'main_exit_settings';
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
			
		$objName = 'exitprocsettings';
		
		$tableFields = array('action'=>'Action','businessunit_id'=>'Business Unit','department_id'=>'Department','l2_manager'=>'L2 Manager','hr_manager'=>'HR Manager','sys_admin'=>'System Admin','general_admin'=>'General Admin','finance_manager'=>'Finance Manager');

		$tablecontent = $this->getExitprocSettings('grid',$sort, $by, $pageNo, $perPage,$searchQuery,$a,$searchArray);     

		/** retrieve names array **/
		$tmpRes = $tablecontent;
		$empDetailsObj = $buDetailsObj = $deptDetailsObj = '';
		$tmpResObj = $this->fetchAll($tmpRes)->toArray();
		if(count($tmpResObj) > 0)
		{
			$lineManager1 = $lineManager2 = $employeesStr = $businessUnitsStr = $departmentsStr = '';
			
			for($e = 0; $e < sizeof($tmpResObj); $e++)
			{
				$employeesStr .= $tmpResObj[$e]['hr_manager'].","; 
				if(!empty($tmpResObj[$e]['l2_manager']))
					$employeesStr .= $tmpResObj[$e]['l2_manager'].",";
				if(!empty($tmpResObj[$e]['sys_admin']))
					$employeesStr .= $tmpResObj[$e]['sys_admin'].",";
				if(!empty($tmpResObj[$e]['general_admin']))
					$employeesStr .= $tmpResObj[$e]['general_admin'].","; 
				if(!empty($tmpResObj[$e]['finance_manager']))
					$employeesStr .= $tmpResObj[$e]['finance_manager'].",";

				$businessUnitsStr .= $tmpResObj[$e]['businessunit_id'].","; 
				$departmentsStr .= $tmpResObj[$e]['department_id'].",";
			}
			
			if(empty($sort)) 
				$sort = 'ASC';

			if(!empty($employeesStr))
			{
				$employeesStr = rtrim($employeesStr,",");

				$empDetailsQuery = $this->select()
								->setIntegrityCheck(false)
								->from(array('emp' => 'main_employees_summary'),array('emp.user_id','emp.userfullname','emp.profileimg', 'emp.jobtitle_name'))
								->where('emp.user_id IN ('.$employeesStr.') AND emp.isactive = 1 ')
								->order("emp.userfullname");

				$empDetailsObj_tmp = $this->fetchAll($empDetailsQuery)->toArray();

				foreach ($empDetailsObj_tmp as &$row) {
					$empDetailsObj[$row['user_id']] = &$row;
				}

			}
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
			'empDetailsObj' => $empDetailsObj,
			'buDetailsObj' => $buDetailsObj,
			'deptDetailsObj' => $deptDetailsObj,
		);
		return $dataTmp;
	}

	public function getExitprocSettings($con,$sort='', $by='', $pageNo='', $perPage='',$searchQuery='',$conText = '',$searchArray=array())
	{
	
		try
		{
			$columns = 'e.*';
			$where = "e.isactive = 1";
			
			if($conText)
			{
				$searchQuery1 = $searchQuery2 = $searchQuery3 = $searchQuery4 = $searchQuery5 = $searchQuery6 = $searchQuery7 ='';
				
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
						else if($key == 'l2_manager')
						{
							$searchQuery7 = " l2.user_id = e.l2_manager AND l2.userfullname  like '%".$val."%' AND l2.isactive = 1 ";	
						}
						else if($key == 'hr_manager')
						{
							$searchQuery3 = " hr.user_id = e.hr_manager AND hr.userfullname  like '%".$val."%' AND hr.isactive = 1 ";
						}							
						else if($key == 'sys_admin')
						{
							$searchQuery4 = " sy.user_id = e.sys_admin AND sy.userfullname  like '%".$val."%' AND sy.isactive = 1 ";	
						}
						else if($key == 'general_admin')
						{
							$searchQuery5 = " ga.user_id = e.general_admin AND ga.userfullname  like '%".$val."%' AND ga.isactive = 1 ";
						}
								
						else if($key == 'finance_manager')
						{
							$searchQuery6 = " fm.user_id = e.finance_manager AND fm.userfullname  like '%".$val."%' AND fm.isactive = 1 ";	
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
				if(!empty($searchQuery3))
				{
					$res = $res->joinInner(array('hr' => 'main_employees_summary'),$searchQuery3,array());
				}
				if(!empty($searchQuery7))
				{
					$res = $res->joinInner(array('l2' => 'main_employees_summary'),$searchQuery7,array());
				}
				if(!empty($searchQuery4))
				{
					$res = $res->joinInner(array('sy' => 'main_employees_summary'),$searchQuery4,array());
				}
				if(!empty($searchQuery5))
				{
					$res = $res->joinInner(array('ga' => 'main_employees_summary'),$searchQuery5,array());
				}
				if(!empty($searchQuery6))
				{
					$res = $res->joinInner(array('fm' => 'main_employees_summary'),$searchQuery6,array());
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
	
	public function getExitProcSettingsById($id)
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
	public function getExitProcSettingsDetails($id)
	{
	
		if($id)
		{
			$res = $this->select()
				->setIntegrityCheck(false)
				->from(array('e' => $this->_name),array('e.*'))
				->joinLeft(array('b' => 'main_businessunits'),'b.id=e.businessunit_id',array('b.unitname as unitname'))
				->joinLeft(array('d' => 'main_departments'),'d.id=e.department_id',array('d.deptname as deptname'))
				->joinLeft(array('esl' => 'main_employees_summary'),'esl.user_id=e.l2_manager',array('esl.userfullname as l2manager_userfullname'))
				->joinLeft(array('esh' => 'main_employees_summary'),'esh.user_id=e.hr_manager',array('esh.userfullname as hrmanager_userfullname'))
				->joinLeft(array('esa' => 'main_employees_summary'),'esa.user_id=e.sys_admin',array('esa.userfullname as sysadmin_userfullname'))
				->joinLeft(array('esg' => 'main_employees_summary'),'esg.user_id=e.general_admin',array('esg.userfullname as generaladmin_userfullname'))
				->joinLeft(array('esf' => 'main_employees_summary'),'esf.user_id=e.finance_manager',array('esf.userfullname as finacemanager_userfullname'))
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

	public function getEmployeesDataByRole($empGroup,$bunitId, $deptId,$con='')
	{
		$where = 'e.isactive = 1';
		
		/* if($deptId)
			$where .= ' AND e.department_id = '.$deptId; */

		if($empGroup && !empty($con)){
			$grpStr = implode(", ",$empGroup);
			$where .= ' AND r.group_id IN ('.$grpStr.')';
		}
		else if($empGroup && empty($con))
			$where .= ' AND r.group_id = '.$empGroup;

		$res = $this->select()
			->setIntegrityCheck(false)
			->from(array('e' => 'main_employees_summary'),array('e.user_id','e.userfullname','e.profileimg', 'e.jobtitle_name'))
			->joinInner(array('r' => 'main_roles'),'r.id = e.empRole',array())
			->where($where.' AND e.businessunit_id = '.$bunitId);
		
		return $this->fetchAll($res)->toArray();
	}

	public function getEmployeesDataById($ids,$con='')
	{
		$where = 'e.isactive = 1';
		
		if(!empty($con))
		{
			$idsStr = implode(", ",$ids);
			$where .= ' AND e.user_id IN ('.$idsStr.')';
		}
		else
		{
			$where .= ' AND e.user_id = '.$ids;
		}

		$res = $this->select()
			->setIntegrityCheck(false)
			->from(array('e' => 'main_employees_summary'),array('e.user_id','e.userfullname','e.profileimg', 'e.jobtitle_name'))
			->where($where);
		
		return $this->fetchAll($res)->toArray();
	}
	
	public function saveExitProcSettings($data,$where = '', $con = '')
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
                           ->from(array('l'=>'main_exit_settings'),array('deptid'=>'l.department_id'))
						   ->where('l.isactive = 1');  		   					   				
		return $this->fetchAll($select)->toArray(); 
	}
	
}
?>