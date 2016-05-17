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
 * @model Projecttasks Model
 * @author sagarsoft
 *
 */
class Timemanagement_Model_Projectresources extends Zend_Db_Table_Abstract
{
	/**
	 * The default table name
	 */
	protected $_name = 'tm_project_employees';
	protected $_primary = 'id';

	public function getProjectResourcesData($projectId){
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('pr'=>$this->_name),array('pr.*'))
		->joinLeft(array('e'=>'main_employees_summary'),'e.user_id = pr.emp_id',array('e.user_id','e.emprole','tm_role'=>new Zend_Db_Expr("(CASE WHEN g.group_name = 'Management' || g.group_name = 'Manager'  THEN  'Manager'
 ELSE CASE WHEN pr.emp_id IN (SELECT reporting_manager FROM main_employees_summary) THEN 'Manager'
      ELSE 'Employee' END END)"),'empname'=>'e.userfullname','e.emailaddress','e.employeeId','profileimg'=>'e.profileimg','prj_createdby'=>new Zend_Db_Expr("(SELECT created_by FROM tm_projects WHERE id = '$projectId')")))
		->joinInner(array('r'=>'main_roles'),"r.id = e.emprole",array())
		->joinInner(array('g'=>'main_groups'),"g.id = r.group_id",array())
		->where('pr.is_active = 1 AND e.isactive = 1 AND pr.project_id = '.$projectId.'')
		->order("pr.modified DESC");;
		//echo $select;exit;
		$res = $this->fetchAll($select)->toArray();
		if (isset($res) && !empty($res))
		{
			return $res;
		}
		else
		return 'norows';
	}

	/**
	 * This will fetch all the default task details based on the search paramerters passed with pagination.
	 *
	 * @param string $sort
	 * @param string $by
	 * @param number $perPage
	 * @param number $pageNo
	 * @param JSON $searchData
	 * @param string $call
	 * @param string $dashboardcall
	 * @param string $a
	 * @param string $b
	 * @param string $c
	 * @param string $d
	 *
	 * @return array
	 */
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$a='',$loginUserId='',$tm_role='',$d='')
	{
		$searchQuery = $having = '';
		$searchArray = array();
		$data = array();

		if($searchData != '' && $searchData!='undefined')
		{
			$searchValues = json_decode($searchData);
			foreach($searchValues as $key => $val)
			{
				if($key == 'tm_role'){
					$having = " tm_role ='".$val."' ";
				}
				else if($key == 'empname'){
					$searchQuery .= " e.userfullname like'%".$val."%' AND ";
				}
				else{
					$searchQuery .= " ".$key." like '%".$val."%' AND ";
				}
				$searchArray[$key] = $val;
			}
			$searchQuery = rtrim($searchQuery," AND");
		}
			
		$objName = 'projectresources';

		$projectModel = new Timemanagement_Model_Projects();
		$projectData = $projectModel->getSingleProjectData($a);
		$currencyCode = (isset($projectData[0]['currencycode']))?$projectData[0]['currencycode']:'';

		$tableFields = array('empname' => 'Name','tm_role' => 'Role','billable_rate' => 'Billable Rate ('.$currencyCode.')','cost_rate'=>'Cost Rate ('.$currencyCode.')','viewtasks' => '');

		$tablecontent = $this->getProjectResourcesGridData($sort, $by, $pageNo, $perPage,$searchQuery,$a,$loginUserId,$having);

		$dataTmp = array(
			'sort' => $sort,
			'by' => $by,
			'pageNo' => $pageNo,
			'perPage' => $perPage,				
			'tablecontent' => $tablecontent,
			'objectname' => $objName,
		    'menuName' => 'Resources',
			'extra' => array(),
			'tableheader' => $tableFields,
			'jsGridFnName' => 'getAjaxgridData',
			'jsFillFnName' => '',
			'searchArray' => $searchArray,
			'call'=>$call,
			'dashboardcall'=>$dashboardcall,
			 'search_filters' => array(
	                    'tm_role' => array(
	                        'type' => 'select',
	                        'filter_data' => array(''=>'All','Manager' => 'Manager','Employee' => 'Employee'),
		),
		)
		);

		/* if($tm_role == 'Lead'){
		 $dataTmp['otheraction'] = 'leadresourcegrid';
		 }*/
		return $dataTmp;
	}
	public function getProjectResourcesGridData($sort, $by, $pageNo, $perPage,$searchQuery,$project_id,$loginUserId,$having = '')
	{
		$where='1=1';
		if($searchQuery)
		$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();

		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('pr'=>$this->_name),array('pr.*','viewtasks'=>"concat('viewTasks(',pr.project_id,',',pr.emp_id,')')"))
		->joinLeft(array('e'=>'main_employees_summary'),'e.user_id = pr.emp_id',array('e.user_id','e.emprole','tm_role'=>new Zend_Db_Expr("(CASE WHEN g.group_name = 'Management' || g.group_name = 'Manager'  THEN  'Manager'
 ELSE CASE WHEN pr.emp_id IN (SELECT reporting_manager FROM main_employees_summary) THEN 'Manager'
      ELSE 'Employee' END END)"),'empname'=>'e.userfullname','e.emailaddress','e.employeeId','profileimg'=>'e.profileimg'))
		->joinInner(array('r'=>'main_roles'),"r.id = e.emprole",array())
		->joinInner(array('g'=>'main_groups'),"g.id = r.group_id",array())
		->where('pr.is_active = 1 AND pr.project_id = '.$project_id.'')
		->where($where)
		->group("pr.emp_id");
		if($having != ''){
			$select->having("$having")
			->order("$by $sort");
			//->limitPage($pageNo, $perPage);
		}else{
			$select->order("$by $sort")
			->limitPage($pageNo, $perPage);
		}//echo $select;exit;
		return $select;
	}

	public function getProjectNotAddedResource($projectId,$added_empIdStrind,$empType,$addedMngrStr=''){
		$orWhere = '';
		if($empType == 'manager'){
			$empType='Manager';
			if($added_empIdStrind != '')
			{
				$orWhere = " AND u.user_id NOT IN ($added_empIdStrind)";
			}
			else
			{
				$orWhere = " AND u.user_id NOT IN ('')";
			}
		}

		if($empType == 'emp'){
			$empType='Employee';
			if($addedMngrStr != ''){
				if($added_empIdStrind != '')
				{
					$orWhere = " AND u.user_id NOT IN ($added_empIdStrind)";
				}
				else
				{
					$orWhere = " AND u.user_id NOT IN ('')";
				}
					
				$orWhere.= " AND u.reporting_manager IN ($addedMngrStr)";
			}else{
				$orWhere = " AND 1!=1";
			}
		}
			
		$select = $this->select()
		->distinct('e.id')
		->setIntegrityCheck(false)
		->from(array('u'=>'main_employees_summary'),
		array('u.user_id','u.emprole','tm_role'=>new Zend_Db_Expr("(CASE WHEN g.group_name = 'Management' || g.group_name = 'Manager'  THEN  'Manager'
 ELSE CASE WHEN u.user_id IN (SELECT reporting_manager FROM main_employees_summary) THEN 'Manager'
      ELSE 'Employee' END END)"),'empname'=>'u.userfullname','u.emailaddress','empid'=>'u.user_id','u.profileimg','u.employeeId','jobtitle'=>'j.jobtitlename'))
		->joinInner(array('r'=>'main_roles'),"r.id = u.emprole",array())
		->joinInner(array('g'=>'main_groups'),"g.id = r.group_id",array())
		->joinLeft(array('j'=>'main_jobtitles'),"u.jobtitle_id = j.id",array())
		->where("1=1 AND u.date_of_joining <= CURDATE() AND u.isactive = 1 ".$orWhere."")
		->group("u.user_id")
		->having("tm_role = '".$empType."'");
		//echo $select;exit;
		$res = $this->fetchAll($select)->toArray();
		if (isset($res) && !empty($res))
		{
			return $res;
		}
		else
		return 'norows';
	}

	public function getOtherEmployees($projectId,$added_empIdStrind,$empType,$addedMngrStr=''){

		if($empType == 'emp'){
			$empType='Employee';
			if($added_empIdStrind != '')
			{
				$orWhere = " AND u.user_id NOT IN ($added_empIdStrind)";
			}
			else
			{
				$orWhere = " AND u.user_id NOT IN ('')";
			}
			if($addedMngrStr != ''){
				$orWhere.= " AND u.reporting_manager NOT IN ($addedMngrStr)";
			}
		}
			
		$select = $this->select()
		->distinct('e.id')
		->setIntegrityCheck(false)
		->from(array('u'=>'main_employees_summary'),
		array('u.user_id','u.emprole','tm_role'=>new Zend_Db_Expr("(CASE WHEN g.group_name = 'Management' || g.group_name = 'Manager'  THEN  'Manager'
 ELSE CASE WHEN u.user_id IN (SELECT reporting_manager FROM main_employees_summary) THEN 'Manager'
      ELSE 'Employee' END END)"),'empname'=>'u.userfullname','u.emailaddress','empid'=>'u.user_id','u.profileimg','u.employeeId','jobtitle'=>'j.jobtitlename'))
		->joinInner(array('r'=>'main_roles'),"r.id = u.emprole",array())
		->joinInner(array('g'=>'main_groups'),"g.id = r.group_id",array())
		->joinLeft(array('j'=>'main_jobtitles'),"u.jobtitle_id = j.id",array())
		->where("1=1 AND u.isactive =1 AND u.date_of_joining <= CURDATE() ".$orWhere."")
		->group("u.user_id")
		->having("tm_role = '".$empType."'");;
		//echo $select;exit;
		$res = $this->fetchAll($select)->toArray();
		if (isset($res) && !empty($res))
		{
			return $res;
		}
		else
		return 'norows';
	}

	public function getEmployeesAdded($projectId,$empids){

		$ids = '1!=1';
		if($empids!=''){
			$ids = ' e.user_id IN('.$empids.') AND e.isactive = 1';
		}
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('e'=> 'main_employees_summary'),array('member_name'=>'e.userfullname','pempid'=>'e.user_id','e.profileimg','tm_role'=>new Zend_Db_Expr("(CASE WHEN g.group_name = 'Management' || g.group_name = 'Manager'  THEN  'Manager'
 ELSE CASE WHEN e.user_id IN (SELECT reporting_manager FROM main_employees_summary) THEN 'Manager'
      ELSE 'Employee' END END)"),'e.employeeId','jobtitle'=>'j.jobtitlename'))
		->joinInner(array('r'=>'main_roles'),"r.id = e.emprole",array())
		->joinInner(array('g'=>'main_groups'),"g.id = r.group_id",array())
		->joinLeft(array('j'=>'main_jobtitles'),"e.jobtitle_id = j.id",array())
		->where($ids);
			
		return $this->fetchAll($select)->toArray();
	}

	public function SaveorUpdateProjectResourceData($data, $where)
	{
		if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId($this->_name);
			return $id;
		}
	}

	//function to get resource tasks to show in popup
	public function getEmpTasks($project_id,$resource_id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('pr'=>'tm_project_task_employees'),array('pr.*'))
		->joinInner(array('tt'=>'tm_tasks'),"tt.id = pr.task_id",array('tt.task'))
		->joinInner(array('ept'=>'tm_project_tasks'),"ept.task_id = pr.task_id AND ept.project_id = pr.project_id",array('ept.estimated_hrs','actual_hrs'=>new Zend_Db_Expr("IF(et.week_duration is null,'',time_format(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.week_duration as time)))),'%H:%i' ))")))
		->joinLeft(array('et'=>'tm_emp_timesheets'),'et.project_task_id = ept.id and pr.emp_id = et.emp_id AND et.is_active = 1',array())
		->where('pr.emp_id='.$resource_id.' AND pr.project_id = '.$project_id.' AND pr.is_active = 1 AND ept.is_active=1')
		->group(array('ept.id'));
		//echo $select;
		return $this->fetchAll($select)->toArray();
	}
	//FUNCTION TO GET EMPLOYEE DETAILS
	public function getEmpDetails($resource_id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('e'=>'main_employees_summary'),array('e.user_id','e.emprole','empname'=>'e.userfullname','e.emailaddress','e.employeeId','profileimg'=>'e.profileimg'))
		->joinLeft(array('p'=>'main_jobtitles'),"p.id = e.jobtitle_id",array('p.jobtitlename'))
		->where('e.user_id='.$resource_id.'');
		return $this->fetchAll($select)->toArray();
	}

	public function checkProjectResourceDependency($projectId,$empId){
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select count(*) as count from tm_emp_timesheets where project_id = ".$projectId." AND emp_id = ".$empId." AND is_active = 1";
		$result = $db->query($query)->fetch();
		return $result['count'];
	}

	//function to get all tasks for the perticuler project
	public function getAllTasks($projectId,$task_ids='')
	{
		$cond = '';
		if($task_ids!='')
		{
			$cond = ' AND pt.task_id ='.$task_ids.'';
		}
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('pt'=>'tm_project_tasks'),array('pt.task_id','pt.estimated_hrs','pt.id'))
		->joinInner(array('t'=>'tm_tasks'),"t.id = pt.task_id ",array('t.task'))
		->where('pt.is_active = 1 AND pt.project_id = '.$projectId.$cond.'');
		return $this->fetchAll($select)->toArray();
	}
	//function to get assigned tasks
	public function getassignedTasks($project_id,$employee_id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('pt'=>'tm_project_tasks'),array('pt.task_id','pt.estimated_hrs'))
		->joinInner(array('pte'=>'tm_project_task_employees'),"pt.task_id = pte.task_id and pte.project_id = ".$project_id." ",array('pte.id','pte.project_task_id','projectTaskCount'=>'(SELECT count(*) FROM tm_emp_timesheets et WHERE et.project_task_id = pte.project_task_id AND et.is_active = 1 AND et.emp_id ='. $employee_id.')'))
		->joinInner(array('t'=>'tm_tasks'),"t.id = pt.task_id ",array('t.task'))
		->where('pt.is_active = 1 AND pt.project_id = '.$project_id.' and pte.is_active = 1 and pte.emp_id = '.$employee_id);
		return $this->fetchAll($select)->toArray();
	}

	public function checkProjectResource($projectId,$empId){
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select count(*) as count from tm_project_employees where project_id = ".$projectId." AND emp_id = ".$empId." AND is_active = 1";
		$result = $db->query($query)->fetch();
		return $result['count'];
	}

}