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
class Exit_Model_Allexitprocs extends Zend_Db_Table_Abstract
{
	protected $_name = 'main_exit_process';
	private $db;
	private $searchArray = array();
	private $loggedInUser = '';
	private $loggedInUserGroup = '';
	private $loggedInUserRole = '';
	
	public function init()
	{
		$this->db = Zend_Db_Table::getDefaultAdapter();

		/**
		** Initiating zend auth object
		** for getting logged in user id
		**/
		$auth = Zend_Auth::getInstance();
		
		if($auth->hasIdentity())
		{
			$this->loggedInUser = $auth->getStorage()->read()->id;
			$this->loggedInUserRole = $auth->getStorage()->read()->emprole;
			$this->loggedInUserGroup = $auth->getStorage()->read()->group_id;
			$this->department_id = $auth->getStorage()->read()->department_id;
			$this->is_org_head = $auth->getStorage()->read()->is_orghead;
		}
	}
	
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$a='',$b='',$c='',$d='',$status = '')
	{
		$searchQuery = '';
       // $searchArray = array();
        $data = array();

		if($searchData != '' && $searchData!='undefined')
			{
				$searchValues = json_decode($searchData);
				foreach($searchValues as $key => $val)
				{
					if(empty($a))
					{
						if($key=='initiateddate')
							$searchQuery .= " date(e.".'createddate'.") = '".  sapp_Global::change_date($val,'database')."' AND ";
						else if($key=='overall_status')
							$searchQuery .= " e.".$key." like '%".$val."%' AND ";
						else if($key=='exit_type')
							$searchQuery .= " et.".$key." like '%".$val."%' AND ";
						else if($key=='individual_status'){
							$searchQuery .="case 
										when emp.reporting_manager =".$this->loggedInUser." then e.l1_status LIKE '%".$val."%'
										when econfig.l2_manager =".$this->loggedInUser." then e.l2_status LIKE '%".$val."%'
										when econfig.hr_manager =".$this->loggedInUser." then e.hr_manager_status LIKE '%".$val."%'
										when econfig.sys_admin =".$this->loggedInUser." then e.sys_admin_status LIKE '%".$val."%'
										when econfig.finance_manager =".$this->loggedInUser." then e.fin_admin_status LIKE '%".$val."%'
										when econfig.general_admin =".$this->loggedInUser." then e.gen_admin_status LIKE '%".$val."%' end ";
						
						}
						else	
							$searchQuery .= " emp.".$key." like '%".$val."%' AND ";
					}
					$this->searchArray[$key] = $val;
				}
				$searchQuery = rtrim($searchQuery," AND");					
			}
		$objName = 'allexitproc';

		if($this->loggedInUserRole != SUPERADMINROLE && $this->is_org_head!=1){
			$tableFields = array('action'=>'Action','userfullname'=>'Employee','employeeId'=>'Employee ID','businessunit_name'=>'Business Unit','department_name'=>'Department','exit_type'=>'Exit Type','overall_status'=>'Overall Status','individual_status'=>'Individual Status','initiateddate' => 'Initiated Date');
		}else{
			$tableFields = array('action'=>'Action','userfullname'=>'Employee','employeeId'=>'Employee ID','businessunit_name'=>'Business Unit','department_name'=>'Department','exit_type'=>'Exit Type','overall_status'=>'Overall Status','initiateddate' => 'Initiated Date');
		}

		$tablecontent = $this->getAllExitProcs('grid',$sort, $by, $pageNo, $perPage,$searchQuery,$a,$status);     
		
		
		/**
		** check if logged in user acts as a l2 manager for any of the raised exit procedures
		**/
		$l2mData = $this->getExitProcsByL2Manager('grid',$sort, $by, $pageNo, $perPage,$searchQuery,$a);

		
		$dataTmp = array(
			'sort' => $sort,
			'by' => $by,
			'pageNo' => $pageNo,
			'perPage' => $perPage,				
			'tablecontent' => $tablecontent,
			'l2mData' => $l2mData,
			'objectname' => $objName,
			'extra' => array(),
			'tableheader' => $tableFields,
			'jsGridFnName' => 'getAjaxgridData',
			'jsFillFnName' => '',
			'searchArray' => $this->searchArray,
			'add' =>'add',
			'call'=>$call,
			'dashboardcall'=>$dashboardcall,
			'search_filters' => array(
					'initiateddate' =>array('type'=>'datepicker')					
				)
		);
		return $dataTmp;
	}
	public function getAllExitProcs($con,$sort='', $by='', $pageNo='', $perPage='',$searchQuery='',$conText = '',$status = '')
	{	
		try
		{
			$configWhere = "";
			$select = "";
			// to show all records to manager, super admin	
			//if($this->loggedInUser != SUPERADMIN)
			if($this->loggedInUserRole != SUPERADMINROLE && $this->is_org_head!=1)
			{
				$configWhere = " AND econfig.isactive = 1 AND ( emp.reporting_manager = ".$this->loggedInUser." OR econfig.l2_manager = ".$this->loggedInUser." OR econfig.hr_manager = ".$this->loggedInUser." OR econfig.sys_admin = ".$this->loggedInUser." OR econfig.general_admin = ".$this->loggedInUser." OR econfig.finance_manager = ".$this->loggedInUser.")";
		
			}
		
			$columns = 'e.*';				
			$where = " e.employee_id != ".$this->loggedInUser;
			
			if($searchQuery)
				$where .= " AND ".$searchQuery;
			
			
			/**based on status value displaying grid
			$status=1 //all records
			$status=2 // approved records
			$status=3 // rejected records
			$status=4 // pending records
			**/
			
			if($status==1)//all records
			{
				$where .= " AND e.overall_status in (1,2,3) ";
			}
			else if($status==2)// approved records
			{
				$where .= " AND e.overall_status in (2) ";
			}
			else if($status==3)// rejected records
			{
				$where .= " AND e.overall_status in (3) ";
			}
			else
			{
				$where .= " AND e.overall_status in (1) ";
			}
			if($this->loggedInUserRole != SUPERADMINROLE && $this->is_org_head!=1){
				$res = $this->select()
				->setIntegrityCheck(false)

				->from(array('e' => $this->_name),array('e.id',$columns,'date(e.createddate) as initiateddate','individual_status'=>new Zend_Db_Expr('case when emp.reporting_manager ='.$this->loggedInUser.' then e.l1_status
							when econfig.l2_manager ='.$this->loggedInUser.' then e.l2_status
							when econfig.hr_manager='.$this->loggedInUser.' then e.hr_manager_status
							when econfig.sys_admin='.$this->loggedInUser.' then e.sys_admin_status
							when econfig.finance_manager='.$this->loggedInUser.' then e.fin_admin_status
							when econfig.general_admin='.$this->loggedInUser.' then e.gen_admin_status else "--" end')))
				->joinInner(array('emp'=> 'main_employees_summary'),'e.employee_id = emp.user_id',array('emp.userfullname','emp.businessunit_name','emp.department_name','emp.employeeId','emp.reporting_manager'))
				->joinInner(array('et'=> 'main_exit_types'),'et.id = e.exit_type_id',array('et.exit_type'));
			}else{
				$res = $this->select()
				->setIntegrityCheck(false)
				->from(array('e' => $this->_name),array('e.id','date(e.createddate) as initiateddate',$columns))
				->joinInner(array('emp'=> 'main_employees_summary'),'e.employee_id = emp.user_id',array('emp.userfullname','emp.businessunit_name','emp.department_name','emp.employeeId','emp.reporting_manager'))
				->joinInner(array('et'=> 'main_exit_types'),'et.id = e.exit_type_id',array('et.exit_type'));
			}
			
			if(!empty($configWhere))
				$res = $res->joinInner(array('econfig' => 'main_exit_settings'),'econfig.id = e.exit_settings_id',array('econfig.createddate as created_date','econfig.l2_manager','econfig.hr_manager','econfig.sys_admin','econfig.general_admin','econfig.finance_manager'));
			
			//echo $res;exit;

				$res = $res->where($where.$configWhere);
				$res = $res->order("$by $sort");
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
			print_r($e);
		}
	}
	
	

	public function getExitProcsByL2Manager($con,$sort='', $by='', $pageNo='', $perPage='',$searchQuery='',$conText = '')
	{
		try
		{
			$columns = 'e.*';
			$where = "e.isactive = 1";
			
			if($searchQuery)
				$where .= " AND ".$searchQuery;
		
			$res = "";
		
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

	public function getExitProcDetails($exitProcId)
	{
		if($exitProcId)
		{
			$res = $this->select()
				->setIntegrityCheck(false)
				->from(array('ep' => $this->_name),'ep.*')
				->joinInner(array('u' => 'main_employees_summary'),'u.user_id = ep.employee_id',array('u.userfullname','u.businessunit_id','u.businessunit_name','u.department_id','u.department_name','u.employeeId','u.date_of_joining','u.jobtitle_name','u.position_name','u.reporting_manager','u.reporting_manager_name'))
				->joinInner(array('econfig' => 'main_exit_settings'),'econfig.businessunit_id = u.businessunit_id AND econfig.department_id = u.department_id',array('econfig.id as econfigId','econfig.businessunit_id','econfig.department_id','econfig.l2_manager','econfig.hr_manager','econfig.sys_admin','econfig.general_admin','econfig.finance_manager'))
				->joinInner(array('etypes' => 'main_exit_types'),'etypes.id = ep.exit_type_id',array('etypes.exit_type'))
				->where('ep.id = '.$exitProcId);

			return $this->fetchAll($res)->toArray();
		
		}
	}

	public function updateExitProc($data,$where,$id)
	{
		if(!empty($data) && !empty($where))
		{
			$this->update($data,$where);
			return 'update';
		}
		else
			return 'failed';
	}

	//get the details based on exit_setting_id
	public function getExitsettingsProcDetails($id,$con='')
	{
		
		if($con=='')
		{
			$where='ep.exit_settings_id='.$id;
		}
		else
		{
			$where='ep.exit_settings_id='.$id.' AND ep.overall_status ="pending"';
		}
		 $select = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('ep'=>$this->_name),array('ep.*'))
						   ->where($where);	 				   				
		return $this->fetchAll($select)->toArray(); 
	}

	//function to get exit process initialization data
	public function getExitProcessData($id)
	{
		$res = $this->select()
				->setIntegrityCheck(false)
				->from(array('ep' => $this->_name),'ep.*')
				->where('ep.id = '.$id);
		return $this->fetchAll($res)->toArray();
	}
}
?>