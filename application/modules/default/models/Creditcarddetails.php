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

class Default_Model_Creditcarddetails extends Zend_Db_Table_Abstract
{	
    protected $_name = 'main_empcreditcarddetails';
    protected $_primary = 'id';
	
       
    public function getcreditcarddetails($sort, $by, $pageNo, $perPage,$searchQuery)
	{
        $where = "c.isactive = 1";
		if($searchQuery)
			$where .= " AND ".$searchQuery;		
		$creditcarddata = $this->select()
						->from(array('c'=>'main_empcreditcarddetails'),array('id'=>'id','user_id'=>'user_id','card_type'=>'card_type','card_number'=>'card_number','card_expiration'=>'DATE_FORMAT(card_expiration,"'.DATEFORMAT_MYSQL.'")','card_issued_comp'=>'card_issued_comp','nameoncard'=>'nameoncard','card_code'=>'card_code'))
						 ->where($where)
						  ->order("$by $sort") 
						  ->limitPage($pageNo, $perPage);
		
		return $creditcarddata;       		
	}
	
	public function getcreditcarddetailsRecord($id=0)
	{  
		$creditcardDetailsArr="";$where = "";
		$db = Zend_Db_Table::getDefaultAdapter();		
		if($id != 0)
		{
			$where = "user_id =".$id;
			$creditcardDetailsData = $this->select()
									->from(array('c'=>'main_empcreditcarddetails'),array('id'=>'id','user_id'=>'user_id','card_type'=>'card_type','card_number'=>'card_number','card_expiration'=>'DATE_FORMAT(card_expiration,"'.DATEFORMAT_MYSQL.'")','card_issued_comp'=>'card_issued_comp','nameoncard'=>'nameoncard','card_code'=>'card_code'))
									->where($where);
		
			
			$creditcardDetailsArr = $this->fetchAll($creditcardDetailsData)->toArray(); 
        }
		return $creditcardDetailsArr;       		
	}
    
    public function SaveorUpdateCreditcardDetails($data, $where)
    {
	    if($where != '')
        {
            $this->update($data, $where);
			return 'update';
        }
        else
        {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_empcreditcarddetails');
			return $id;
		}
		
	
	}
}