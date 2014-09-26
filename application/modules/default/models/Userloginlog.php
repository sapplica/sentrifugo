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

class Default_Model_Userloginlog extends Zend_Db_Table_Abstract
{
	protected $_name = 'main_userloginlog';
	protected $_primary = 'id';


	/**
	 * This function gives all content for grid view.
	 * @parameters
	 * @param $sort          = ascending or descending
	 * @param $by            = name of field which to be sort
	 * @param $pageNo        = page number
	 * @param $perPage       = no.of records per page
	 * @param $searchData    = search string
	 * @param $call          = type of call like ajax.
	 * @return  Array;
	 */
	public function getUserLoginLogGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$p1,$p2,$p3,$p4,$p5)
	{
		$group_model = new Default_Model_Groups();
		$role_model = new Default_Model_Roles();
		$user_model = new Default_Model_Users();
		$searchQuery = '';
		$searchArray = array();
		$data = array();

		if($searchData != '' && $searchData!='undefined')
		{
			$searchValues = json_decode($searchData);
			if(count($searchValues) >0)
			{
				foreach($searchValues as $key => $val)
				{
					
					if($key == 'userfullname' || $key == 'employeeId' || $key == 'emailaddress' || $key == 'empipaddress')
					{
						$searchQuery .= " r.".$key." like '%".$val."%' AND ";
					}else if($key == 'logindatetime')
					{
						$searchQuery .= " ".$key." like '%".  sapp_Global::change_date(urldecode($val),'database')."%' AND ";
					}
				    else if($key == 'rolename')
					{
						$searchQuery .= " ro.".$key." like '%".$val."%' AND ";
					}
					else $searchQuery .= " ".$key." like '%".$val."%' AND ";
					$searchArray[$key] = $val;
				}
				$searchQuery = rtrim($searchQuery," AND");
			}
		}
		$objName = 'userloginlog';

		$tableFields = array('action'=>'Action',
                             'userfullname' => 'User',
		                     'profileimg'=>'Profile',
							 'employeeId'=>'Employee ID',
							 'group_name' => 'Group',
                             'rolename' => 'Role',
                             'emailaddress'=>'Email',		
                             'logindatetime' => 'Login Time',
		                     'empipaddress' => 'Ip Address',
		);

		$tablecontent = $this->getUserLoginLogData($sort, $by, $pageNo, $perPage,$searchQuery);

		$group_data = $group_model->getGroupsListForUserLoginLog();
		$group_arr = array();

		foreach($group_data as $gkey => $gdata)
		{
			$group_arr[$gdata['group_name']] = $gdata['group_name'];
		}
		$role_data = $role_model->getRoleListForUserLoginLog();
		$role_arr = array();
		foreach($role_data as $gkey => $gdata)
		{
			$role_arr[$gdata['rolename']] = $gdata['rolename'];
		}


		$dataTmp = array(
                'sort' => $sort,
                'by' => $by,
                'pageNo' => $pageNo,
                'perPage' => $perPage,				
                'tablecontent' => $tablecontent,
                'objectname' => $objName,
		        'menuName'=>'User log',
                'extra' => array(),
                'tableheader' => $tableFields,
                'jsGridFnName' => 'getAjaxgridData',
                'jsFillFnName' => '',
                'searchArray' => $searchArray,
                'call'=>$call,
                'search_filters' => array(
                    'group_name' =>array(
                        'type'=>'select',
                        'filter_data' => array(''=>'All')+$group_arr,
		),
					'rolename' =>array(
			                        'type'=>'select',
			                        'filter_data' => array(''=>'All')+$role_arr,

		),
		             'logindatetime' =>array('type'=>'datepicker')	
			
		),
		);
		
		return $dataTmp;
	}

	/**
	 * This function returns data to getUserLoginLogGrid function.
	 * @parameters
	 * @sort          = ascending or descending
	 * @by            = name of field which to be sort
	 * @pageNo        = page number
	 * @perPage       = no.of records per page
	 * @searchQuery   = search string
	 *
	 * @return  ResultSet;
	 */
	public function getUserLoginLogData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "1";

		if($searchQuery)
		$where .= " AND ".$searchQuery;

		$userLoginLogData = $this->select()
		->setIntegrityCheck(false)
		->from(array('r'=>$this->_name),array('r.*',))
		->joinLeft(array('g'=>'main_groups'), "g.id = r.group_id and g.isactive = 1",array('group_name'=>'g.group_name'))
		->joinInner(array('ro'=>'main_roles'), "ro.id = r.emprole and ro.isactive = 1",array('rolename'=>'ro.rolename'))
		->joinInner(array('u'=>'main_users'), "u.id = r.userid and u.isactive = 1",array('profileimg'=>'u.profileimg'))
		->where($where)
		->order("$by $sort")
		->limitPage($pageNo, $perPage);
		
		return $userLoginLogData;
	}

	/**
	 * This function gives data for report.
	 * @parameters
	 * @sort          = ascending or descending
	 * @by            = name of field which to be sort
	 * @pageNo        = page number
	 * @perPage       = no.of records per page
	 * @searchQuery   = search string
	 *
	 * @return  ResultSet;
	 */
	public function getUserWiseLogData($by="logindatetime",$order="desc",$pageNo=1, $perPage=20,$searchQuery=1,$selectfield=array('*'))
	{
		$where = "1";
		$by = trim($by);

		if($searchQuery)
		$where .= " AND ".$searchQuery;

		if($by == ''){

			$select = $this->select()
			->setIntegrityCheck(false)
			->from(array($this->_name),$selectfield)
			->where($where)
			->order($order)
			->limitPage($pageNo, $perPage);
		}else{
			$select = $this->select()
			->setIntegrityCheck(false)
			->from(array($this->_name),$selectfield)
			->where($where)
			->order("$by $order")
			->limitPage($pageNo, $perPage);
		}

		
		$userLogRecords = $this->fetchAll($select)->toArray();
		

		return $userLogRecords;
	}

	/**
	 * This function gives count of userloginlog table.
	 * @parameters
	 * @searchQuery   = search string
	 *
	 * @return  ResultValue;
	 */
	public function getUserLogCount($searchQuery = 1)
	{
		$where = "1";

		if($searchQuery)
		$where .= " AND ".$searchQuery;

		$qry = "select count(*) as count from main_userloginlog where {$where}";
		$db = Zend_Db_Table::getDefaultAdapter();
		$sqlRes = $db->query($qry);
		$count = $sqlRes->fetchAll();
			
		return $count[0]['count'];
	}

	/**
	 * This function gives roles data.
	 * @parameters
	 * @order          = ascending or descending
	 *
	 * @return  Array;
	 */
	public function getEmpRoleOrder($order)
	{
		$emprole = "";
		$qry = "select id from main_roles order by rolename {$order}";
		$db = Zend_Db_Table::getDefaultAdapter();
		$sqlRes = $db->query($qry);
		$emprole = $sqlRes->fetchAll();

		$roleorderidArray = array();
		foreach($emprole as $key => $roleid){
			$roleorderidArray[] = $roleid['id'];

		}

		return $roleorderidArray;
	}

	/**
	 * This function gives group data.
	 * @parameters
	 * @order          = ascending or descending
	 *
	 * @return  Array;
	 */
	public function getEmpgroupOrder($order)
	{
		$emprole = "";
		$qry = "select id from main_groups order by group_name {$order}";
		$db = Zend_Db_Table::getDefaultAdapter();
		$sqlRes = $db->query($qry);
		$empgroup = $sqlRes->fetchAll();

		$grouporderidArray = array();
		foreach($empgroup as $key => $groupid){
			$grouporderidArray[] = $groupid['id'];

		}

		return $grouporderidArray;
	}

	/**
	 * This function gives data for employeename autocomplete.
	 * @parameters
	 * @search_str   = search string
	 *
	 * @return  ResultSet;
	 */
	public function getAutoReportEmpname($search_str)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select u.profileimg,u.userfullname emp_name,
                  case
                   when u.userfullname like '".$search_str."%' then 4
                   when u.userfullname like '__".$search_str."%' then 2 
                   when u.userfullname like '_".$search_str."%' then 3 
                   when u.userfullname like '%".$search_str."%' then 1 
                  else 0 end emp 
                  from main_users u where (u.userfullname like '%".$search_str."%') 
                  order by emp desc
                  limit 0,10";
		$result = $db->query($query);
		$emp_arr = array();
		$emp_arr = $result->fetchAll();
		return $emp_arr;
	}

	/**
	 * This function gives data for employeeId autocomplete.
	 * @parameters
	 * @search_str   = search string
	 *
	 * @return  ResultSet;
	 */
	public function getAutoReportEmpID($search_str)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select u.employeeId emp_id,
                  case
                   when u.employeeId like '".$search_str."%' then 4
                   when u.employeeId like '__".$search_str."%' then 2 
                   when u.employeeId like '_".$search_str."%' then 3 
                   when u.employeeId like '%".$search_str."%' then 1 
                  else 0 end emp 
                  from main_users u where (u.employeeId like '%".$search_str."%') 
                  order by emp desc
                  limit 0,10";
		$result = $db->query($query);
		$emp_arr = array();
		$emp_arr = $result->fetchAll();
		return $emp_arr;
	}

	/**
	 * This function gives data for employeeIPAddress autocomplete.
	 * @parameters
	 * @search_str   = search string
	 *
	 * @return  ResultSet;
	 */
	public function getAutoReportEmpIpaddress($search_str)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select u.empipaddress emp_id,
                  case
                   when u.empipaddress like '".$search_str."%' then 4
                   when u.empipaddress like '__".$search_str."%' then 2 
                   when u.empipaddress like '_".$search_str."%' then 3 
                   when u.empipaddress like '%".$search_str."%' then 1 
                  else 0 end emp 
                  from main_users u where (u.empipaddress like '%".$search_str."%') 
                  order by emp desc
                  limit 0,10";
		$result = $db->query($query);
		$emp_arr = array();
		$emp_arr = $result->fetchAll();
		return $emp_arr;
	}

	/**
	 * This function gives data for employeeEmail autocomplete.
	 * @parameters
	 * @search_str   = search string
	 *
	 * @return  ResultSet;
	 */
	public function getAutoReportEmpEmail($search_str)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select distinct u.emailaddress emp_email,
                  case
                   when u.emailaddress like '".$search_str."%' then 4
                   when u.emailaddress like '__".$search_str."%' then 2 
                   when u.emailaddress like '_".$search_str."%' then 3 
                   when u.emailaddress like '%".$search_str."%' then 1 
                  else 0 end emp 
                  from main_users u where (u.emailaddress like '%".$search_str."%') 
                  order by emp desc
                  limit 0,10";
		$result = $db->query($query);
		$emp_arr = array();
		$emp_arr = $result->fetchAll();
		return $emp_arr;
	}
}