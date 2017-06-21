<?php

/* * ******************************************************************************* 
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
 * ****************************************************************************** */

/**
 * Requisitionhistory will track every step of the request
 *
 * @author ramakrishna
 */
class Default_Model_Requisitionhistory extends Zend_Db_Table_Abstract 
{
    protected $_name = 'main_requisition_history';
    protected $_primary = 'id';
    
    /*
     * This function is used to save/update data in database.
     * @parameters
     * @data  =  array of form data.
     * @where =  where condition in case of update.
     *
     * returns  Primary id when new record inserted,'update' string when a record updated.
     */
    public function saveOrUpdateRequisitionHistory($data, $where)
    {
		if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId($this->_name);
			return $id;
		}
	}	//end of SaveorUpdateRequisitionhistory
    public function getRequisitionHistory($id)
    {
        $history = array();
  		$db = Zend_Db_Table::getDefaultAdapter();
		$where = " e.requisition_id = ".$id." AND e.isactive = 1 ";
		$by=" e.createddate desc ";
	    $history = $this->select()
					->setIntegrityCheck(false)
					->from(array('e' => 'main_requisition_history'),array('history'=>"concat(e.description,c.userfullname)",'hdate'=>"date(e.createddate)",'htime'=>"time(e.createddate)"))
					->joinLeft(array('c'=>'main_users'), 'c.id=e.createdby',array('emp_profile_img'=>'c.profileimg','emp_name'=>'c.userfullname'))	
					->where($where)
					->order($by);
		
    	return  $this->fetchAll($history)->toArray();
     }
	 public function getRequisitionHistoryforCandidate($id)
	 {
		$history = array();
		$where = " e.candidate_id = ".$id." AND e.isactive = 1 ";
		$by=" e.id desc ";
    	if($id != '')
    	{
    		$db = Zend_Db_Table::getDefaultAdapter();
    	    $history = $this->select()
					->setIntegrityCheck(false)
					->from(array('e' => 'main_requisition_history'),array('history'=>"concat(e.description,c.userfullname)",'hdate'=>"date(e.createddate)",'htime'=>"time(e.createddate)"))
					->joinLeft(array('c'=>'main_users'), 'c.id=e.createdby',array('emp_profile_img'=>'c.profileimg','emp_name'=>'c.userfullname'))	
					->where($where)
					->order($by);
    	}
    	return  $this->fetchAll($history)->toArray();
	 }
	
	 
}//end 
