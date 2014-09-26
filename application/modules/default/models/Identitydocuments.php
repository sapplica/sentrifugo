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

class Default_Model_Identitydocuments extends Zend_Db_Table_Abstract
{	
    protected $_name = 'main_identitydocuments';
    protected $_primary = 'id';
	
	public function getIdentitydocumnetsrecord()
	{	$identityCodesArr="";
		$db = Zend_Db_Table::getDefaultAdapter();		
	    $select = $this->select()
                            ->from(array('i'=>'main_identitydocuments'),array('i.*'))
                            ->where('i.isactive=1');							
		$identityDocumnetsArr = $this->fetchAll($select)->toArray(); 
		
		return  $identityDocumnetsArr; 
	
	}
	
	public function getIdentitydocumnetsrecordwithID($id)
	{	$identityCodesArr="";
		$db = Zend_Db_Table::getDefaultAdapter();		
	    $select = $this->select()
                            ->from(array('i'=>'main_identitydocuments'),array('i.*'))
                            ->where('i.isactive=1 AND i.id='.$id.'');							
		$identityDocumnetsArr = $this->fetchAll($select)->toArray(); 
		
		return  $identityDocumnetsArr; 
	
	}
      
	public function SaveorUpdateIdentitydocumentsData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_identitycodes');
			return $id;
		}		
	}
	
	public function getallcodes($code)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select * from main_identitycodes";		
		$result = $db->query($query)->fetch();
		if($code == 'bgcheck')
	    return $result['backgroundagency_code'];
		else
		return $result;
	}
}