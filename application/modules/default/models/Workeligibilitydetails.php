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

class Default_Model_Workeligibilitydetails extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_empworkeligibility';
    protected $_primary = 'id';
	
	
	public function getWorkEligibilityRecord($id=0)
	{
		$WorkEligibilityDetailsArr="";$where = "";
		$db = Zend_Db_Table::getDefaultAdapter();		
		if($id != 0)
		{
			$where = "user_id =".$id;
			$WorkEligibilityData = $this->select()
									->from(array('w'=>'main_empworkeligibility'))
									->where($where);
		
			
			$WorkEligibilityDetailsArr = $this->fetchAll($WorkEligibilityData)->toArray(); 
        }
		return $WorkEligibilityDetailsArr; 
	}
	
	public function SaveorUpdateWorkEligibilityDetails($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_empworkeligibility');
			return $id;
		}
		
	
	}
}?>