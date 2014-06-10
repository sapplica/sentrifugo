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
			 //echo $select;exit; 
		return $this->fetchAll($select)->toArray();

	}
	
	public function getLoggedInEmployeeDetails($userid)
	{
	    $result =  $this->select()
    				->setIntegrityCheck(false) 	
    				->from(array('e'=>'main_employees'),array('e.*'))
 	  				->where("e.isactive = 1 AND e.user_id =".$userid." ");
		//echo $result;exit;			
    	return $this->fetchAll($result)->toArray();
	}
    
	public function SaveorUpdateEmployees($data, $where)
	{
		//echo "<pre>"; print_r($data); print_r($where);die;
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
		//echo $result;exit;			
    	return $this->fetchAll($result)->toArray();
	
	}
}