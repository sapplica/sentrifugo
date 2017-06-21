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

class Default_Model_Empholidays extends Zend_Db_Table_Abstract
{
	protected $_name = 'main_employeeleaves';
    protected $_primary = 'id';
	
	public function getEmpLeavesData($sort, $by, $pageNo, $perPage,$searchQuery,$id)
	{
		$where = " e.user_id = ".$id." AND e.isactive = 1 ";
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$empskillsData = $this->select()
    					   ->setIntegrityCheck(false)	 
						   ->from(array('e' => 'main_employeeleaves'),array('id'=>'e.id','leavelimit'=>'e.emp_leave_limit','used_leaves'=>'e.used_leaves','e.alloted_year'))
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
	
		return $empskillsData;       		
	}
	
	public function getsingleEmployeeleaveData($id)
	{
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('el'=>'main_employeeleaves'),array('el.*'))
						->where('el.user_id='.$id.' AND el.isactive = 1 AND el.alloted_year = year(now())');
		
		return $this->fetchAll($select)->toArray();
	}
	
	public function getsingleEmpleavesrow($id)
	{
		$row = $this->fetchRow("id = '".$id."'");
		if (!$row) {
			throw new Exception("Could not find row $id");
		}
		return $row->toArray();
	}
	
	public function SaveorUpdateEmpLeaves($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_employeeleaves');
			return $id;
		}
		
	}
	
	
}
?>