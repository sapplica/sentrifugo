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

class Default_Model_Timeformat extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_timeformat';
    protected $_primary = 'id';
	
	public function getTimeFormatData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "isactive = 1";
		
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$timeFormatData = $this->select()
    					   ->setIntegrityCheck(false)	    					
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $timeFormatData;       		
	}
	public function getsingleTimeformatData($id)
	{
		$row = $this->fetchRow("id = '".$id."'");
		if (!$row) {
			throw new Exception("Could not find row $id");
		}
		return $row->toArray();
	}
	
	public function SaveorUpdateTimeFormatData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_timeformat');
			return $id;
		}
	}
	
	public function getTimeFormatDataByID($id)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('t'=>'main_timeformat'),array('t.*'))
					    ->where('t.isactive = 1 AND t.id='.$id.' ');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function getTimeFormatList()
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('t'=>'main_timeformat'),array('t.id','t.timeformat'))
					    ->where('t.isactive = 1');
		return $this->fetchAll($select)->toArray();
	
	}
}