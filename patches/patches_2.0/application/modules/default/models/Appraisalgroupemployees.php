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

class Default_Model_Appraisalgroupemployees extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_pa_groups_employees';
    protected $_primary = 'id';
	
		
	public function getConfigData($id)
	{
		 $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('pi'=>'main_pa_initialization'),array('pi.*'))
					    ->where('pi.isactive = 1 AND pi.id='.$id.' ');
		return $this->fetchAll($select)->toArray();
		
	}
	
	public function getMappedEmployeeList($id,$tablename)
	{
		$options = array();
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select employee_ids,group_id from $tablename 
                     where isactive = 1 and pa_initialization_id = ".$id." ";
            $result = $db->query($query);
            $options = $result->fetchAll();
        return $options;
		
	}
	
	public function getGrouppedEmployeeList($initid,$groupid='',$tablename)
	{
		$options = array();
			$where = 'isactive = 1 and pa_initialization_id = '.$initid.'';
		if($groupid)
			$where.= ' AND group_id = '.$groupid.' ';
			
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select employee_ids from $tablename where $where ";
            $result = $db->query($query);
            $options = $result->fetchAll();
        return $options;
		
	}
	
	public function getEmployeeList($data,$employeeIds='',$flag)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$where = 'e.isactive=1';
		if(!empty($data))
		{
			if($data['businessunit_id'] !='' && $data['businessunit_id'] !='NULL')
			{
				$where.= ' AND e.businessunit_id = '.$data['businessunit_id'].'';
			}
			if($data['department_id'] !='' && $data['department_id'] !='NULL')
			{
				$where.= ' AND e.department_id = '.$data['department_id'].'';
			}	
			if($data['eligibility'] !='' && $data['eligibility'] !='NULL')
			{
				$where.= ' AND e.emp_status_id IN('.$data['eligibility'].')';
			}
		}
		if($employeeIds !='')
		{
			if($flag == 1)
				$where.=' AND e.user_id NOT IN('.$employeeIds.')';
			else if($flag == 2)
				$where.=' AND e.user_id IN('.$employeeIds.')';	
		}
		
		 $query = "select e.user_id,e.userfullname,e.businessunit_name,e.department_name,e.businessunit_id,e.department_id,e.profileimg,g.id as groupid,r.rolename from main_employees_summary e
				   inner join main_roles r on r.id=e.emprole and r.isactive=1
				   inner join main_groups g on r.group_id = g.id and g.id IN(".MANAGEMENT_GROUP.",".MANAGER_GROUP.",".HR_GROUP.",".EMPLOYEE_GROUP.",".SYSTEMADMIN_GROUP.") 
				   where $where ";
            $result = $db->query($query);
            $options = $result->fetchAll();
            
            return $options;
	}
	
	public function getEmployeeListWithGroup($data,$employeeIds='',$groupIds='')
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$where = 'e.isactive=1';
		if(!empty($data))
		{
			if($data['businessunit_id'] !='' && $data['businessunit_id'] !='NULL')
			{
				$where.= ' AND e.businessunit_id = '.$data['businessunit_id'].'';
			}
			if($data['department_id'] !='' && $data['department_id'] !='NULL')
			{
				$where.= ' AND e.department_id = '.$data['department_id'].'';
			}	
			if($data['eligibility'] !='' && $data['eligibility'] !='NULL')
			{
				$where.= ' AND e.emp_status_id IN('.$data['eligibility'].')';
			}
		}
		if($employeeIds !='')
		{
				$where.=' AND e.user_id IN('.$employeeIds.')';	
		}
		
		
		 $query = "select e.user_id,e.userfullname,e.profileimg from main_employees_summary e
				   inner join main_roles r on r.id=e.emprole and r.isactive=1
				   inner join main_groups g on r.group_id = g.id and g.id IN(".MANAGEMENT_GROUP.",".MANAGER_GROUP.",".HR_GROUP.",".EMPLOYEE_GROUP.",".SYSTEMADMIN_GROUP.") 
				   where $where ";
            $result = $db->query($query);
            $options = $result->fetchAll();
            
            return $options;
	}
	
	public function getEmployeeNameWithProfile($employeeIds)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$options = array();
		
		if($employeeIds !='')
		{
		 	$query = "select e.user_id,e.userfullname,e.businessunit_name,e.department_name,e.profileimg,g.id as groupid,r.rolename from main_employees_summary e
				   inner join main_roles r on r.id=e.emprole and r.isactive=1
				   inner join main_groups g on r.group_id = g.id and g.id IN(".MANAGEMENT_GROUP.",".MANAGER_GROUP.",".HR_GROUP.",".EMPLOYEE_GROUP.",".SYSTEMADMIN_GROUP.") 
				   where e.user_id IN($employeeIds) AND e.isactive=1 ";
            $result = $db->query($query);
            $options = $result->fetchAll();
		}   
            
            return $options;
	}
	
	
	public function SaveorUpdateAppraisalGroupsEmployeesData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_pa_groups_employees');
			return $id;
		}
		
	
	} 
		
}