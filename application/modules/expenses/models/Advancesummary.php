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

class Expenses_Model_Advancesummary extends Zend_Db_Table_Abstract
{
	/**
	 * The advance table name
	 */
    protected $_name = 'expense_advacne_summary';
    protected $_primary = 'id';
	
	
	public function SaveAdvanceData($data, $where)
	{
			//$where = "isactive = 1";
			
		
		if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			
			//echo "here";exit;
			$this->insert($data);
			//$id=$this->getAdapter()->lastInsertId($this->_name);
			//echo $id;exit;
			 return 1;
		}
	}
	
	/**
	 * This method is used to fetch advance summary of employ  based on employ id.
	 * 
	 * @param number $id
	 */
	public function getAdvanceDetailsById($emp_id)
	{
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('c'=>$this->_name),array('c.*'))						
						->where('c.isactive = 1 AND c.employee_id='.$emp_id.' ');
						
		return $this->fetchAll($select)->toArray();
	}
	
}