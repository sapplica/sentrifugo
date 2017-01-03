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
/**
 *
 * @model Configuration Model
 * @author sagarsoft
 *
 */
class Timemanagement_Model_Configuration extends Zend_Db_Table_Abstract
{
    protected $_name = 'tm_configuration';
    protected $_primary = 'id';
	
	public function getActiveRecord()
	{
	   $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('c'=>$this->_name),array('c.*'))
					    ->where('c.is_active = 1');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function SaveorUpdateConfigurationData($data, $where)
	{	
	    if($where != ''){
		    $this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId($this->_name);
			return $id;
		}
	}	
	
}