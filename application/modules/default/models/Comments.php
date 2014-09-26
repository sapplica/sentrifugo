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

class Default_Model_Comments extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_bgcheckcomments';
    protected $_primary = 'id';
	
	public function SaveorUpdateComments($data, $where)
	{
		
		if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_bgcheckcomments');
			return $id;
		}
	}
	
	public function getComments($id,$limitVar='')
	{	
		if($limitVar == '2')
		{
			$commentsData = $this->select()
    					   ->setIntegrityCheck(false)	 
						   ->from(array('b' => 'main_bgcheckcomments'),array('id'=>'b.id','detail_id'=>'bgdet_id','comment'=>'b.comment','from_id'=>'b.from_id','to_id'=>'b.to_id','createddate'=>'b.createddate'))
						   
						   
						   ->where(' b.isactive = 1 AND b.bgdet_id = '.$id)
						   ->order("b.createddate DESC") 
						   ->limitPage(0,2);
		}
		else if($limitVar == 'all')
		{
			$commentsData = $this->select()
    					   ->setIntegrityCheck(false)	 
						   ->from(array('b' => 'main_bgcheckcomments'),array('id'=>'b.id','detail_id'=>'bgdet_id','comment'=>'b.comment','from_id'=>'b.from_id','to_id'=>'b.to_id','createddate'=>'b.createddate'))
						   
						   
						   ->where(' b.isactive = 1 AND b.bgdet_id = '.$id)
						   ->order("b.createddate DESC");						   
		}
		else{
			$commentsData = $this->select()
    					   ->setIntegrityCheck(false)	 
						   ->from(array('b' => 'main_bgcheckcomments'),array('id'=>'b.id','detail_id'=>'bgdet_id','comment'=>'b.comment','from_id'=>'b.from_id','to_id'=>'b.to_id','createddate'=>'b.createddate'))
						   
						   
						   ->where(' b.isactive = 1 AND b.bgdet_id = '.$id)
						   ->order("b.createddate DESC") 
						   ->limitPage(0,100);
		}
		
		return $this->fetchAll($commentsData)->toArray();

	}
	public function getuserNames($userids)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$userData = $db->query("SELECT id,userfullname FROM main_users WHERE id IN (".$userids.")");									
		$result= $userData->fetchAll();
		return $result; 
	}
	
	public function getGMTData($query)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		return $db->query($query)->fetch();
	}
	
}