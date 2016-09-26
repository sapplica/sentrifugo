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

class Default_Model_Employees extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_employees';
    protected $_primary = 'id';
	
	public function getRepMangerID($empid)
	{
		$select = $this->select()
								->setIntegrityCheck(false)
								->from(array('e'=>'main_employees'), array('repmangerid'=>'e.reporting_manager'))
								->where('e.user_id="'.$empid.'" AND e.isactive = 1 ');
			 
		return $this->fetchAll($select)->toArray();

	}
	
	public function getLoggedInEmployeeDetails($userid)
	{
	    $result =  $this->select()
    				->setIntegrityCheck(false) 	
    				->from(array('e'=>'main_employees'),array('e.*'))
 	  				->where("e.isactive = 1 AND e.user_id =".$userid." ");
		
    	return $this->fetchAll($result)->toArray();
	}
    
	public function SaveorUpdateEmployees($data, $where)
	{
		
		if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_employees');
			return $id;
		}
	}
	
	public function getHolidayGroupForEmployee($userid)
	{
	    $result =  $this->select()
    				->setIntegrityCheck(false) 	
    				->from(array('e'=>'main_employees'),array('e.holiday_group'))
 	  				->where("e.isactive = 1 AND e.user_id =".$userid." ");
		
    	return $this->fetchAll($result)->toArray();
	
	}
	
	public function CheckIfReportingManager($loginUserId)
	{
		$select = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('main_employees_summary'),array('count'=>'count(*)'))
                           ->where("isactive = 1 AND reporting_manager = $loginUserId");
		                           
		return $this->fetchAll($select)->toArray();       		
	}
	
	public function getReportingManagers($limit,$offset,$manager_id=0,$expense_created_by=0) 
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$manager_cond = '';
		$createdby_con = '';
		if($manager_id>0)
			$manager_cond = ' AND reporting_manager!='.$manager_id;
		if($expense_created_by>0)
			$createdby_con = ' AND reporting_manager!='.$expense_created_by;
		$where = ' reporting_manager != 0 AND isactive = 1 '.$manager_cond.''.$createdby_con;
		
		$managersData = $this->select()
		->setIntegrityCheck(false)
		->from(array('emp' => 'main_employees_summary'),array('emp.*'))
		->where($where)
		->group("reporting_manager")
		->limit($limit,$offset);
		return $this->fetchAll($managersData)->toArray();
	}
	
	public function getReportingManagersCount($manager_id=0,$expense_created_by=0)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$manager_cond = '';
		$createdby_con='';
		if($manager_id>0)
			$manager_cond = ' AND reporting_manager!='.$manager_id;
		if($expense_created_by>0)
			$createdby_con = ' AND reporting_manager!='.$expense_created_by;
		
		
		$managersData = $this->select()
		->setIntegrityCheck(false)
		->from(array('emp' => 'main_employees_summary'),array('emp.*'))
		->where('isactive = 1 AND reporting_manager != 0 '.$manager_cond.''.$createdby_con)
		->group("reporting_manager");
		
		$count_array = $this->fetchAll($managersData)->toArray();
		return count($count_array);
		
		//$count_query = "select * from main_employees_summary WHERE isactive = 1 AND reporting_manager != 0  $manager_cond $createdby_con GROUP BY reporting_manager";
		//$count_result = $db->query($count_query);
		//$count_row = $this->fetchAll($count_result)->toArray();
		
		//return $count_row['cnt'];  
	}
}