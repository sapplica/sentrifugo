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

class Default_Model_Visaandimmigrationdetails extends Zend_Db_Table_Abstract
{	
    protected $_name = 'main_empvisadetails';
    protected $_primary = 'id';
	
       
    public function getvisadetails($sort, $by, $pageNo, $perPage,$searchQuery)
	{
        $where = "v.isactive = 1";
		if($searchQuery)
			$where .= " AND ".$searchQuery;		
		$creditcarddata = $this->select()
						->from(array('v'=>'main_empvisadetails'))
						 ->where($where)
						  ->order("$by $sort") 
						  ->limitPage($pageNo, $perPage);
		
		return $creditcarddata;       		
	}
	
	public function getvisadetailsRecord($id=0)
	{  
		$creditcardDetailsArr="";$where = "";
		$db = Zend_Db_Table::getDefaultAdapter();		
		if($id != 0)
		{
			$where = "user_id =".$id;
			$creditcardDetailsData = $this->select()
									->from(array('v'=>'main_empvisadetails'))
									->where($where);
		
			
			$creditcardDetailsArr = $this->fetchAll($creditcardDetailsData)->toArray(); 
        }
		return $creditcardDetailsArr;       		
	}
    
    public function SaveorUpdatevisaandimmigrationDetails($data, $where)
    {
	    if($where != '')
        {
            $this->update($data, $where);
			return 'update';
        }
        else
        {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_empvisadetails');
			return $id;
		}
		
	
	}
}