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
class Exit_Model_Exitproc extends Zend_Db_Table_Abstract
{
	protected $_name = 'main_exit_process';
	private $db;
	private $searchArray = array();

	public function init()
	{
		$this->db = Zend_Db_Table::getDefaultAdapter();
		$auth = Zend_Auth::getInstance();
		
		if($auth->hasIdentity())
		{
			$this->loggedInUser = $auth->getStorage()->read()->id;
			$this->loggedInUserRole = $auth->getStorage()->read()->emprole;
			$this->loggedInUserGroup = $auth->getStorage()->read()->group_id;
		}
	}
	
	public function getExitProcDetails($userId='',$process_id)
	{
		$cond='';
		$join = '';
		if($process_id!='')
		{
			$cond = ' AND ep.id = '.$process_id;
			$join = ' AND econfig.id = ep.exit_settings_id'; 
		}
		if(!empty($userId))
		{
			$res = $this->select()
				->setIntegrityCheck(false)
				->from(array('ep' => $this->_name),'ep.*')
				->joinInner(array('u' => 'main_employees_summary'),'u.user_id = ep.employee_id',array('u.businessunit_id','u.department_id'))
				->joinInner(array('et' => 'main_exit_types'),'et.id = ep.exit_type_id',array('et.exit_type'))
				->joinInner(array('econfig' => 'main_exit_settings'),'econfig.businessunit_id = u.businessunit_id AND econfig.department_id = u.department_id'.$join,array('econfig.id as econfigId','econfig.businessunit_id','econfig.department_id','econfig.l2_manager','econfig.hr_manager','econfig.sys_admin','econfig.general_admin','econfig.finance_manager'))
				->where('ep.employee_id ='.$userId.' AND u.isactive = 1'.$cond);
				//->order('ep.id DESC')
				//->limit('0,1');
			return $this->fetchAll($res)->toArray();
		}
	}

	public function getExitTypes()
	{
		$res = $this->select()
				->setIntegrityCheck(false)
				->from(array('et' => 'main_exit_types'),'et.*')
				->where('et.isactive = 1');

			return $this->fetchAll($res)->toArray();
	}

	public function getEmployeeDetails($userId = '',$con='')
	{
		if($userId == SUPERADMIN)
		{
			$res = $this->select()
				->setIntegrityCheck(false)
				->from(array('emp' => 'main_users'),'emp.*')
				->where('emp.id ='.$userId.' AND emp.isactive = 1');				

			return $this->fetchAll($res)->toArray();
		}
		else if(!empty($userId) && $con == 'one')
		{
			$res = $this->select()
				->setIntegrityCheck(false)
				->from(array('emp' => 'main_employees_summary'),'emp.*')
				->where('emp.user_id ='.$userId.' AND emp.isactive = 1');				

			return $this->fetchAll($res)->toArray();
		}
		else if(!empty($userId) && $con == 'multiple')
		{
			$where = ' AND emp.user_id IN ('.$userId.')';
			$res = $this->select()
				->setIntegrityCheck(false)
				->from(array('emp' => 'main_employees_summary'),'emp.*')
				->where('emp.isactive = 1 '.$where);				

			return $this->fetchAll($res)->toArray();
		}
		
	}

	public function save($data, $where = '')
	{
		if(empty($where))
		{
			$this->insert($data);
			$id = $this->getAdapter()->lastInsertId($this->_name);
			return $id;
		}
	}

	public function getSettings($businessunit_id,$department_id)
	{	
		$res = $this->select()
				->setIntegrityCheck(false)
				->from(array('epConfig' => 'main_exit_settings'),'epConfig.*')
				->where('epConfig.isactive = 1 AND epConfig.businessunit_id = '.$businessunit_id.' AND epConfig.department_id = '.$department_id);

		return $this->fetchAll($res)->toArray();
	}	

	public function exitProcHistory($historyData)
	{
		if(!empty($historyData))
		{
				$this->db->insert('main_exit_history',$historyData);
				$id = $this->getAdapter()->lastInsertId('main_exit_history');
				return $id;
		}
	}

	public function getExitProcHistory($exitProcId)
	{
		if($exitProcId)
		{
			$qry = $this->select()
					->setIntegrityCheck(false)
					->from(array('eph' => 'main_exit_history'),'eph.*')
					->join(array('e' => 'main_users'),'e.id = eph.createdby',array('e.userfullname','e.profileimg'))
					->where('eph.exit_request_id = '.$exitProcId);

			return $this->fetchAll($qry)->toArray();			
		}
	}
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$a='',$b='',$c='',$d='')
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
						elseif($key=='exit_type')	
							$searchQuery .= " econfig.".$key." like '%".$val."%' AND ";
						elseif($key=='overall_status')
							$searchQuery .= " e.".$key." like '%".$val."%' AND ";
						else
							$searchQuery .= " emp.".$key." like '%".$val."%' AND ";
					}
					$this->searchArray[$key] = $val;
				}
				$searchQuery = rtrim($searchQuery," AND");					
			}
		$objName = 'exitproc';

		$tableFields = array('action'=>'Action','businessunit_name'=>'Business Unit','department_name'=>'Department','exit_type'=>'Exit Type','overall_status'=>'Overall Status','initiateddate' => 'Initiated Date');

		$tablecontent = $this->getExitProcs('grid',$sort, $by, $pageNo, $perPage,$searchQuery,$a);     
		
		

		
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
	public function getExitProcs($con,$sort='', $by='', $pageNo='', $perPage='',$searchQuery='',$conText = '')
	{	
		try
		{
			$configWhere = "";

			if($this->loggedInUser != SUPERADMIN)
			{
				$configWhere = " AND econfig.isactive = 1 ";
			}
			$columns = 'e.*';
			$where = " e.employee_id = ".$this->loggedInUser;
			
			if($searchQuery)
				$where .= " AND ".$searchQuery;
		
			$res = $this->select()
				->setIntegrityCheck(false)
				->from(array('e' => $this->_name),array('e.id','date(e.createddate) as initiateddate',$columns))
				->joinInner(array('emp'=> 'main_employees_summary'),'e.employee_id = emp.user_id',array('emp.userfullname','emp.businessunit_name','emp.department_name','emp.employeeId','emp.reporting_manager'));
			if(!empty($configWhere))
				$res = $res->joinInner(array('econfig' => 'main_exit_types'),'econfig.id = e.exit_type_id',array('econfig.createddate as created_date','econfig.exit_type as exit_type'));

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
	//function to get user_email to send mails
	public function getUserEmail($userId)
	{
		$qry = $this->select()
					->setIntegrityCheck(false)
					->from(array('mu' => 'main_users'),'mu.*')
					->where('mu.id = '.$userId);

			return $this->fetchAll($qry)->toArray();
	}
	//function to get settings data
	public function getSettingsData($settingId)
	{
		$qry = $this->select()
					->setIntegrityCheck(false)
					->from(array('mes' => 'main_exit_settings'),'mes.*')
					->where('mes.id = '.$settingId);

			return $this->fetchAll($qry)->toArray();
	}
	//function to get employee raised requests
	public function getEmpRaisedRequest($emp_id)
	{
		$qry = $this->select()
					->setIntegrityCheck(false)
					->from(array('mep' => 'main_exit_process'),'mep.*')
					->where('mep.employee_id = '.$emp_id.' and mep.overall_status="Pending"');

		return $this->fetchAll($qry)->toArray();
	}

}


?>