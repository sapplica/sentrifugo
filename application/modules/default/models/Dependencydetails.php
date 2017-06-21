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

class Default_Model_Dependencydetails extends Zend_Db_Table_Abstract
{
	protected $_name = 'main_empdependencydetails';
    protected $_primary = 'id';
	
	public function getdependencydetailsData($sort, $by, $pageNo, $perPage,$searchQuery,$userid)
	{	
		$where = "isactive = 1  AND user_id = ".$userid ;
		
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$dependencyData = $this->select()
						->from(array('d'=>'main_empdependencydetails'),array('id'=>'id','dependent_name'=>'dependent_name','dependent_relation'=>'dependent_relation','dependent_dob'=>'DATE_FORMAT(dependent_dob,"'.DATEFORMAT_MYSQL.'")'))
					   ->where($where)
					   ->order("$by $sort") 
					   ->limitPage($pageNo, $perPage);
		
		return $dependencyData;         		
	}
	public function getdependencydetailsRecord($id=0)
	{  
		$empdependencyDetailsArr="";$where = "";
		$db = Zend_Db_Table::getDefaultAdapter();		
		if($id != 0)
		{
			$where = "id =".$id;
			$empdependencyDetails = $this->select()
									->from(array('d'=>'main_empdependencydetails'))
									->where($where);
		
			
			$empdependencyDetailsArr = $this->fetchAll($empdependencyDetails)->toArray(); 
        }
		return $empdependencyDetailsArr;       		
	}
    
	public function SaveorUpdateEmployeedependencyData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_empdependencydetails');
			return $id;
		}
		
	}
	public function getdependencyrelations($userId)
	{
		$empdependencyrelationArr="";
		$db = Zend_Db_Table::getDefaultAdapter();		
		if($userId != 0)
		{
			$where = "user_id =".$userId;
			$empdependencyDetails = $this->select()
									->from(array('d'=>'main_empdependencydetails'),array('dependent_relation'=>'d.dependent_relation'))
									->where($where)
									->order("d.dependent_relation");
		
			
			$empdependencyrelationArr = $this->fetchAll($empdependencyDetails)->toArray(); 
        }
		
		return $empdependencyrelationArr;  
	}
	
	
}
?>