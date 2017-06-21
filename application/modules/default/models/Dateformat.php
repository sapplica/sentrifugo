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

class Default_Model_Dateformat extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_dateformat';
    protected $_primary = 'id';
	
	public function getDateFormatData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "isactive = 1";
		
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$dateFormatData = $this->select()
    					   ->setIntegrityCheck(false)	    					
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $dateFormatData;       		
	}
	public function getsingleDateformatData($id)
	{
		$row = $this->fetchRow("id = '".$id."'");
		
		if (!$row) {
		
			throw new Exception("Could not find row $id");
		}
		return $row->toArray();
	}
	
	public function SaveorUpdateDateFormatData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_dateformat');
			return $id;
		}
		
	
	}
	
	public function getDateFormatDataByID($id)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('d'=>'main_dateformat'),array('d.*'))
					    ->where('d.isactive = 1 AND d.id='.$id.' ');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function getDateFormatList()
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('d'=>'main_dateformat'),array('d.id','d.dateformat'))
					    ->where('d.isactive = 1');
		return $this->fetchAll($select)->toArray();
	
	}
        public function getAllDateFormats()
        {
            $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('d'=>'main_dateformat'),array('d.*'))
					    ->where('d.isactive = 1');
			return $this->fetchAll($select)->toArray();
        }
}