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

class Default_Model_Disabilitydetails extends Zend_Db_Table_Abstract
{	
    protected $_name = 'main_empdisabilitydetails';
    protected $_primary = 'id';
	
       
    public function getempDisabilitydetails($id=0)
	{  
		$disabilityDetailsArr="";$where = "";
		$db = Zend_Db_Table::getDefaultAdapter();		
		if($id != 0)
		{
			$where = "user_id =".$id;
			$disabilitydetails = $this->select()
									->from(array('d'=>'main_empdisabilitydetails'))
									->where($where);
		
			
			$disabilityDetailsArr = $this->fetchAll($disabilitydetails)->toArray(); 
        }
		return $disabilityDetailsArr;       		
	}
    
    public function SaveorUpdateEmpdisabilityDetails($data, $where)
    {
	    if($where != '')
        {
            $this->update($data, $where);
			return 'update';
        }
        else
        {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_empdisabilitydetails');
			return $id;
		}
		
	
	}
	
	
}