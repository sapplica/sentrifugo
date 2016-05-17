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
class Timemanagement_Model_Projecttasks extends Zend_Db_Table_Abstract
{
	/**
	 * The default table name
	 */
	protected $_name = 'tm_project_tasks';
	protected $_primary = 'id';

	public function getProjectTasksData($projectId){
		$select = $this->select()
		->distinct()
		->setIntegrityCheck(false)
		->from(array('pt'=>$this->_name),array('pt.*','taskInUse'=> new Zend_Db_Expr("(SELECT count(id) FROM tm_emp_timesheets et WHERE et.project_task_id  = pt.id AND et.is_active = 1)")))
		->joinLeft(array('t'=>'tm_tasks'),'t.id=pt.task_id',array('taskname'=>'t.task','isDefault'=>'t.is_default'))
		->joinLeft(array('et'=>'tm_emp_timesheets'),'et.project_task_id = pt.id AND et.is_active = 1',array())
		->where('pt.is_active = 1 AND pt.project_id = '.$projectId.'')
		->order("pt.modified DESC");
		//echo $select; //exit;
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
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$a='',$b='',$c='',$d='')
	{
		$searchQuery = '';
		$searchArray = array();
		$data = array();
		$having = '';
		if($searchData != '' && $searchData!='undefined')
		{
			$searchValues = json_decode($searchData);
			foreach($searchValues as $key => $val)
			{
				if($key == 'actual_hrs'){
					$having = " ".$key." like '%".$val."%' ";
				}else{
					$searchQuery .= " ".$key." like '%".$val."%' AND ";
				}
				$searchArray[$key] = $val;
			}
			$searchQuery = rtrim($searchQuery," AND");
		}
			
		$objName = 'projecttasks';

		$projectModel = new Timemanagement_Model_Projects();
		$projectData = $projectModel->getSingleProjectData($a);
		$currencyCode = (isset($projectData[0]['currencycode']))?$projectData[0]['currencycode']:'';
					
		$tableFields = array('task' => 'Name','estimated_hrs' => 'Estimated Hours','billable_rate' => 'Billable Rate ('.$currencyCode.')','actual_hrs' => 'Actual Hours','viewresources'=>'');

		$tablecontent = $this->getProjectTaskData($sort, $by, $pageNo, $perPage,$searchQuery,$a,$having);

		$dataTmp = array(
			'sort' => $sort,
			'by' => $by,
			'pageNo' => $pageNo,
			'perPage' => $perPage,				
			'tablecontent' => $tablecontent,
			'objectname' => $objName,
		    'menuName' => 'Tasks',
			'extra' => array(),
			'tableheader' => $tableFields,
			'jsGridFnName' => 'getAjaxgridData',
			'jsFillFnName' => '',
			'searchArray' => $searchArray,
			'call'=>$call,
			'dashboardcall'=>$dashboardcall
		);
		return $dataTmp;
	}
	public function getProjectTaskData($sort, $by, $pageNo, $perPage,$searchQuery,$a,$having)
	{
		$where = "tpt.is_active = 1 and tpt.project_id=$a";

		if($searchQuery)
		$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();

		$projectTaskData = $this->select()
		->setIntegrityCheck(false)
		->from(array('tpt'=>$this->_name),array('tpt.*','viewresources'=>"concat('viewResources(',tpt.project_id,',',tpt.task_id,')')",
		'actual_hrs'=>new Zend_Db_Expr("IF(et.week_duration is null,'',time_format(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.week_duration as time)))),'%H:%i' ))")
		))
		->joinLeft(array('tt'=>'tm_tasks'),'tpt.task_id=tt.id',array('task'=>'tt.task'))
		->joinLeft(array('et'=>'tm_emp_timesheets'),'et.project_task_id = tpt.id AND et.is_active = 1',array())
		->where($where)
		->order("$by $sort")
		->limitPage($pageNo, $perPage)
		->group(array('tpt.id')); //echo $projectTaskData;exit;

		if($having != ''){
			$projectTaskData->having("$having");
		}

		return $projectTaskData;
	}

	public function SaveorUpdateProjectTaskData($data, $where)
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
	//get Tasks
	public function getTasks($projectId,$projectTaskId)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('tpte'=>'tm_project_tasks'),array('tpte.is_billable','tpte.estimated_hrs'))
		->joinInner(array('tt'=>'tm_tasks'),"tt.id = tpte.task_id",array('tt.task'))
		->where('tpte.is_active=1 and tpte.project_id='.$projectId.' and tpte.task_id='.$projectTaskId.'');
		return $this->fetchAll($select)->toArray();
	}
	//function to get tasks resources
	public function getTasksResources($projectId,$projectTaskId)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('tpte'=>'tm_project_task_employees'),array('tpte.*'))
		->joinInner(array('e'=>'main_employees_summary'),'e.user_id=tpte.emp_id',array('e.user_id','e.emprole','empname'=>'e.userfullname','e.emailaddress','e.employeeId','profileimg'=>'e.profileimg','tm_role'=>new Zend_Db_Expr("(CASE WHEN g.group_name = 'Management' || g.group_name = 'Manager'  THEN  'Manager'
 ELSE CASE WHEN e.user_id IN (SELECT reporting_manager FROM main_employees_summary) THEN 'Manager'
      ELSE 'Employee' END END)")))
		->joinLeft(array('p'=>'main_jobtitles'),"p.id = e.jobtitle_id",array('p.jobtitlename'))
		->joinInner(array('r'=>'main_roles'),"r.id = e.emprole",array())
		->joinInner(array('g'=>'main_groups'),"g.id = r.group_id",array())
		->where( 'tpte.project_id='.$projectId.' and tpte.task_id='.$projectTaskId.' and tpte.is_active = 1');
		return $this->fetchAll($select)->toArray();
	}

	//function to check task dependency before deletion
	public function checkProjectTaskDependency($projectId,$projectTaskId){

		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select count(*) as count from tm_emp_timesheets where project_task_id = ".$projectTaskId." AND is_active = 1";
		$result = $db->query($query)->fetch();
		return $result['count'];
	}

	//function to get employees array in a project
	public function getProjectEmployees($projectId){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('tpe'=>'tm_project_employees'),array('e.user_id','e.profileimg','e.employeeId','userfullname'=>'e.userfullname','j.jobtitlename','tm_role'=>new Zend_Db_Expr("(CASE WHEN g.group_name = 'Management' || g.group_name = 'Manager'  THEN  'Manager'
 ELSE CASE WHEN e.user_id IN (SELECT reporting_manager FROM main_employees_summary) THEN 'Manager'
      ELSE 'Employee' END END)")))
		->joinInner(array('e'=>'main_employees_summary'),'e.user_id = tpe.emp_id',array())
		->joinLeft(array('j'=>'main_jobtitles'),"e.jobtitle_id = j.id",array(''))
		->joinInner(array('r'=>'main_roles'),"r.id = e.emprole",array())
		->joinInner(array('g'=>'main_groups'),"g.id = r.group_id",array())
		->where( 'tpe.is_active = 1 AND tpe.project_id = '.$projectId.'');
		return $this->fetchAll($select)->toArray();
	}

	//function to get project task resources
	public function getProjectTaskEmployees($projectId,$taskId,$projectTaskId){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('pte'=>'tm_project_task_employees'),array('pte.emp_id','pte.task_id','tsEmptaskCnt' => new Zend_Db_Expr("(SELECT count(id) FROM tm_emp_timesheets et WHERE et.project_task_id  = pte.project_task_id AND et.emp_id = pte.emp_id AND et.is_active = 1)"),))
		->where( 'pte.is_active = 1 AND pte.project_task_id = '.$projectTaskId.'');
		return $this->fetchAll($select)->toArray();

	}

	//function to get Task details
	public function getProjectTaskDetails($projectTaskId){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('pt'=>$this->_name),array('pt.*'))
		->joinLeft(array('t'=>'tm_tasks'),'t.id=pt.task_id',array('taskname'=>'t.task'))
		->where('pt.is_active = 1 AND pt.id = '.$projectTaskId.'');
		return $this->fetchAll($select)->toArray();

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
	public function getEmpTaskGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$a='',$loginUserId='',$tm_role='',$d='')
	{
		$searchQuery = '';
		$searchArray = array();
		$data = array();
		$having = '';

		if($searchData != '' && $searchData!='undefined')
		{
			$searchValues = json_decode($searchData);
			foreach($searchValues as $key => $val)
			{
				if($key == 'task'){
					$searchQuery .= " tt.".$key." like '%".$val."%' AND ";
				}else if($key == 'estimated_hrs'){
					$searchQuery .= " tpt.".$key." like '%".$val."%' AND ";
				}else if($key == 'actual_hrs'){
					$having = " ".$key." like '%".$val."%' ";
				}else{
					$searchQuery .= " ".$key." like '%".$val."%' AND ";
				}
				$searchArray[$key] = $val;
			}
			$searchQuery = rtrim($searchQuery," AND");
		}
			
		$objName = 'employeeprojects';
		if($tm_role == 'Lead'){
		 $objName = 'leadprojects';
		}

		$tableFields = array('task' => 'Name','estimated_hrs' => 'Estimated Hours','actual_hrs' => 'Actual Hours');

		$tablecontent = $this->getProjectEmpTaskData($sort, $by, $pageNo, $perPage,$searchQuery,$a,$loginUserId,$having);

		$dataTmp = array(
			'sort' => $sort,
			'by' => $by,
			'pageNo' => $pageNo,
			'perPage' => $perPage,				
			'tablecontent' => $tablecontent,
			'objectname' => $objName,
		    'menuName' => 'Tasks',
			'extra' => array(),
			'tableheader' => $tableFields,
			'jsGridFnName' => 'getAjaxgridData',
			'jsFillFnName' => '',
			'searchArray' => $searchArray,
			'call'=>$call,
			'dashboardcall'=>$dashboardcall,
		    'otheraction' => 'emptasksgrid'
		    );
		    return $dataTmp;
	}

	public function getProjectEmpTaskData($sort, $by, $pageNo, $perPage,$searchQuery,$projectId = '',$loginUserid = '',$having = ''){
		$where = " tpe.project_id=$projectId AND tpe.emp_id = $loginUserid AND tpe.is_active = 1";

		if($searchQuery)
		$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$empTaskData = $this->select()
		->setIntegrityCheck(false)
		->from(array('tpe'=>'tm_project_task_employees'),array('tt.task','tpt.estimated_hrs','actual_hrs'=>new Zend_Db_Expr("IF(et.week_duration is null,'',time_format(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.week_duration as time)))),'%H:%i' ))")))
		->joinINNER(array('tt'=>'tm_tasks'),'tpe.task_id=tt.id',array())
		->joinINNER(array('tpt'=>'tm_project_tasks'),'tpe.project_task_id = tpt.id',array())
		->joinLeft(array('et'=>'tm_emp_timesheets'),'et.project_task_id = tpt.id AND et.is_active = 1',array())
		->where($where)
		->order("$by $sort")
		->limitPage($pageNo, $perPage)
		->group(array('tpt.id'));

		if($having != ''){
			$empTaskData->having("$having");
		}
		//echo $empTaskData;exit;
		return $empTaskData;

	}

	public function getProjTaskNameExists($projectId,$taskId,$taskName){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('t'=>'tm_tasks'),array('taskNameExistsCount' => new Zend_Db_Expr("count(t.id)"),))
		->joinLeft(array('pt'=>'tm_project_tasks'),'t.id = pt.task_id AND pt.is_active = 1',array())
		->where( "t.task = '".$taskName."' AND t.id != '".$taskId."'  AND ((pt.project_id = '".$projectId."') OR (t.is_default = 1))");
		return $this->fetchAll($select)->toArray();
	}

	public function getMostTasks()
	{
		$db=  Zend_Db_Table::getDefaultAdapter();
		$qry="select task_id,count(*) cnt from tm_project_tasks where is_active=1 and task_id
                  not in (select id from tm_tasks where is_active=1 and is_default=1) group by task_id 
                  order by cnt desc limit 500";
		$res=$db->query($qry);
		$most_arr=array();
		$task_model=new Timemanagement_Model_Tasks();
		while($row=$res->fetch())
		{
			$most_arr[$row['task_id']]=$task_model->getTaskById($row['task_id']);
		} 
		return $most_arr;
	}
}