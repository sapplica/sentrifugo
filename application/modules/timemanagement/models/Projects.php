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
 * @model Projects Model
 * @author sagarsoft
 *
 */
class Timemanagement_Model_Projects extends Zend_Db_Table_Abstract
{
	/**
	 * The default table name
	 */
	protected $_name = 'tm_projects';
	protected $_primary = 'id';

	/**
	 * T
	 *
	 * @param string $sort
	 * @param string $by
	 * @param number $pageNo
	 * @param number $perPage
	 * @param string $searchQuery
	 *
	 * @return array $projectsData
	 */
	public function getProjectsData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = " p.is_active = 1 ";
		if(Zend_Registry::get( 'tm_role' ) == 'Manager'){
			$auth = Zend_Auth::getInstance();
			if($auth->hasIdentity()){
				$loginUserId = $auth->getStorage()->read()->id;
			}
			$where .= " AND pe.emp_id = '".$loginUserId."' AND pe.is_active = 1";
		}

		if($searchQuery)
		$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();

		$projectsData = $this->select()->distinct()
		->setIntegrityCheck(false)
		->from(array('p' => $this->_name),array('id'=>'p.id','project_name'=>'p.project_name','project_status'=>'if(p.project_status = "initiated", "Initiated",if(p.project_status = "draft" , "Draft",if (p.project_status = "in-progress","In Progress",if(p.project_status = "hold","Hold",if(p.project_status = "completed","Completed","")))))','parent_project'=>'p2.project_name'))
		->joinLeft(array('p2' => $this->_name),'p.base_project = p2.id',array())
		->joinLeft(array('c'=>'tm_clients'),'p.client_id=c.id',array('client_name'=>'c.client_name'));
		if(Zend_Registry::get( 'tm_role' ) == 'Manager'){
			$projectsData->joinLeft(array('pe'=>'tm_project_employees'),'pe.project_id = p.id',array());
		}
		$projectsData->where($where)
		->order("$by $sort")
		->limitPage($pageNo, $perPage);
		return $projectsData;
	}

	/**
	 * This will fetch all the project details based on the search paramerters passed with pagination.
	 *
	 * @param string $sort
	 * @param string $by
	 * @param number $perPage
	 * @param number $pageNo
	 * @param JSON $searchData
	 * @param string $call
	 * @param string $dashboardcall
	 *
	 * @return array
	 */
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall)
	{
		$searchQuery = '';
		$searchArray = array();
		$data = array();

		if($searchData != '' && $searchData!='undefined')
		{
			$searchValues = json_decode($searchData);
			foreach($searchValues as $key => $val)
			{
				if($key == 'client') $key = 'client_id';
				if($key == 'parent_project'){
					$searchQuery .= " p.base_project = '".$val."' AND ";
				}else if($key == 'client_name'){
					$searchQuery .= " c.id = '".$val."' AND ";
				}else{
					$searchQuery .= " p.".$key." like '%".$val."%' AND ";
				}
				$searchArray[$key] = $val;
			}
			$searchQuery = rtrim($searchQuery," AND");
		}

		$objName = 'projects';

		$tableFields = array('action'=>'Action','project_name' => 'Project','project_status'=>'Status','parent_project'=>'Base Project','client_name' => 'Client');

		$tablecontent = $this->getProjectsData($sort, $by, $pageNo, $perPage,$searchQuery);

		$clientModel = new Timemanagement_Model_Clients();
		$clientData = $clientModel->getActiveClientsData();
		$clientArray = array(''=>'All');
		if(sizeof($clientData) > 0)
		{
			foreach ($clientData as $client){
				$clientArray[$client['id']] = $client['client_name'];
			}

		}

		$base_projectData = $this->getProjectList();
		$base_projectArray = array(''=>'All');
		if(sizeof($base_projectData) > 0)
		{
			foreach ($base_projectData as $base_project){
				$base_projectArray[$base_project['id']] = $base_project['project_name'];
			}
		}

		$dataTmp = array(
			'sort' => $sort,
			'by' => $by,
			'pageNo' => $pageNo,
			'perPage' => $perPage,				
			'tablecontent' => $tablecontent,
			'objectname' => $objName,
		    'menuName' => 'Projects',
			'extra' => array(),
			'tableheader' => $tableFields,
			'jsGridFnName' => 'getAjaxgridData',
			'jsFillFnName' => '',
			'searchArray' => $searchArray,
			'call'=>$call,
			'dashboardcall'=>$dashboardcall,
		    'search_filters' => array(
                    'client_name' => array(
                        'type' => 'select',
                        'filter_data' => $clientArray,
		),
    		'parent_project' => array(
			                        'type' => 'select',
			                        'filter_data' => $base_projectArray,
		),
			'project_status' => array(
			                        'type' => 'select',
			                        'filter_data' => array(''=>'All','initiated' => 'Initiated','draft' => 'Draft','in-progress' => 'In Progress','hold'=>'Hold','completed'=>'Completed'),
		),
				
		),
		);
		return $dataTmp;
	}

	public function getSingleProjectData($id){
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('p'=>$this->_name),array('p.*'))
		->joinLeft(array('c'=>'tm_clients'),'p.client_id=c.id',array('client_name'=>'c.client_name'))
		->where('p.is_active = 1 AND p.id='.$id.' ');
		$res = $this->fetchAll($select)->toArray();
		if (isset($res) && !empty($res))
		{
			return $res;
		}
		else
		return 'norows';
	}

	public function SaveorUpdateProjectsData($data, $where)
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


	//check wether project is assigned to employee time sheet or not
	public function chkProjAssigned($project_id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select count(*) as count from tm_emp_timesheets where project_id = ".$project_id." ";
		$result = $db->query($query)->fetch();
		return $result['count'];
	}

	/**
	 * This method returns all active clients to show in projects screen
	 *
	 * @return array
	 */
	public function getProjectList()
	{
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('p'=>$this->_name),array('p.id','project_name'))
		//->where('p.is_active = 1 ')
		->order('p.project_name');
		return $this->fetchAll($select)->toArray();
	}
	/**
	 * This will fetch all the project details based on the search paramerters passed with pagination.
	 *
	 * @param string $sort
	 * @param string $by
	 * @param number $perPage
	 * @param number $pageNo
	 * @param JSON $searchData
	 * @param string $call
	 * @param string $dashboardcall
	 *
	 * @return array
	 */
	public function getEmpGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$employee_id,$tm_role = '')
	{
		$searchQuery = '';
		$searchArray = array();
		$data = array();

		if($searchData != '' && $searchData!='undefined')
		{
			$searchValues = json_decode($searchData);
			foreach($searchValues as $key => $val)
			{
				if($key == 'client') $key = 'client_id';
				else{
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

		$tableFields = array('action'=>'Action','project_name' => 'Project','client_name' => 'Client');

		$tablecontent = $this->getEmpProjectsData($sort, $by, $pageNo, $perPage,$searchQuery,$employee_id);

		$clientModel = new Timemanagement_Model_Clients();
		$clientData = $clientModel->getActiveClientsData();
		$clientArray = array(''=>'All');
		if(sizeof($clientData) > 0)
		{
			foreach ($clientData as $client){
				$clientArray[$client['client_name']] = $client['client_name'];
			}

		}

		$base_projectData = $this->getProjectList();
		$base_projectArray = array(''=>'All');
		if(sizeof($base_projectData) > 0)
		{
			foreach ($base_projectData as $base_project){
				$base_projectArray[$base_project['id']] = $base_project['project_name'];
			}
		}

		$dataTmp = array(
			'sort' => $sort,
			'by' => $by,
			'pageNo' => $pageNo,
			'perPage' => $perPage,				
			'tablecontent' => $tablecontent,
			'objectname' => $objName,
		    'menuName' => 'Projects',
			'extra' => array(),
			'tableheader' => $tableFields,
			'jsGridFnName' => 'getAjaxgridData',
			'jsFillFnName' => '',
			'searchArray' => $searchArray,
			'call'=>$call,
			'dashboardcall'=>$dashboardcall,
		    'search_filters' => array(
                    'client_name' => array(
                        'type' => 'select',
                        'filter_data' => $clientArray,
		),
					'base_project' => array(
			                        'type' => 'select',
			                        'filter_data' => $base_projectArray,
		),
                    
					 'project_status' => array(
			                        'type' => 'select',
			                        'filter_data' => array(''=>'All','initiated' => 'Initiated','draft' => 'Draft','in-progress' => 'In Progress','hold'=>'Hold','completed'=>'Completed'),
		)
		 
		)
		);
		return $dataTmp;
	}
	public function getEmpProjectsData($sort, $by, $pageNo, $perPage,$searchQuery,$employee_id)
	{
		$where = " p.is_active = 1 ";
		if($searchQuery)
		$where .= " AND ".$searchQuery;
		if($employee_id>0)
		$where .= " AND p.project_status!='draft' and tpe.is_active = 1 AND tpe.emp_id = ".$employee_id;
		$db = Zend_Db_Table::getDefaultAdapter();
		$projectsData = $this->select()
		->setIntegrityCheck(false)
		->from(array('tpe' => 'tm_project_employees'),array('tpe.*'))
		->joinLeft(array('p' => $this->_name),'tpe.project_id=p.id',array('id'=>'p.id','project_name'=>'p.project_name','project_status'=>'p.project_status','base_project'=>'p.base_project'))
		->joinLeft(array('c'=>'tm_clients'),'p.client_id=c.id',array('client_name'=>'c.client_name'))
		->where($where)
		->order("$by $sort")
		->limitPage($pageNo, $perPage);
		return $projectsData;
	}

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
	//function to get last weekend week days
	public function getprevmonthweeks($selmn,$day)
	{
		$j=1;
		$master=array();
		$week=array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
		$date=date($selmn.'-01');
		$dt_arr=preg_split('/-/',$date);
		$lsday=cal_days_in_month(CAL_GREGORIAN, $dt_arr[1], $dt_arr[0]);
		$wday=date('l',mktime(0,0,0,$dt_arr[1],$dt_arr[2],$dt_arr[0]));
		$wval=  array_search($wday, $week);
		for($i=0;$i<42;$i++)
		{
			$master[$i]=0;
			if($i>=$wval && $i<($lsday+$wval))
			{
				$master[$i]=$j;
				$j++;
			}
		}
		$k=0;
		$weeks=array();       
		 for($i=1;$i<=6;$i++)
		 {            
			 for($j=1;$j<=7;$j++)
			 {
				 if($master[$k]!=0)
				 {
					 $weeks[$i][]=$selmn."-".str_pad($master[$k],2,'0',STR_PAD_LEFT);
					 if($master[$k]==$day)
						 $pre_week=$i;
				 }
				$k++;     
			 }           
		  }
		   
		  $weeknew=array();
		  for($i=1;$i<$pre_week;$i++)
		  {
			  $weeknew[$i]=$weeks[$i];
		  }
		 
		  if($pre_week!=1)
			return  $weeknew;             
		  else 
		  {  
			  $ndate=new DateTime($date);
			  $ndate->sub(new DateInterval('P1M'));
			  $new_selmn=$ndate->format('Y-m');
			  $lsday_new=cal_days_in_month(CAL_GREGORIAN, $ndate->format('m'), $ndate->format('Y'));
			  return $this->getprevmonthweeks($new_selmn, $lsday_new);
		  } 
	}


	/**
	 * This method returns all projects under client
	 *
	 * @return array
	 */
	public function getProjectListByClientID($clientID)
	{
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('p'=>$this->_name),array('p.id','project_name'))
		->where('p.is_active = 1 and p.client_id = '.$clientID)
		->order('p.project_name');
		return $this->fetchAll($select)->toArray();
	}	
	
}