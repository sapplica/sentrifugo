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

class Default_Model_Employeedocs extends Zend_Db_Table_Abstract
{
	protected $_name = 'main_employeedocuments';
    protected $_primary = 'id';
	
	public function getEmpDocumentsByFieldOrAll($field='',$value='')
	{
		$where = '';
		
		if($field && $value)
			$where = ' AND ed.'.$field.' = "'.$value.'"';
			
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('ed'=>'main_employeedocuments'),array('ed.*'))
						->where('ed.isactive = 1'.$where);
					
		return $this->fetchAll($select)->toArray();
	}
	
	public function SaveorUpdateEmpDocuments($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_employeedocuments');
			return $id;
		}
	}
	
	public function checkDocNameByUserIdAndDocId($userId, $docName, $docId='')
	{
		$where = '';
		if($docId)
			$where = ' AND id != '.$docId;
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('ed'=>'main_employeedocuments'),array('ed.*'))
						->where('ed.isactive = 1 AND ed.user_id = '.$userId.' AND ed.name = "'.$docName.'"'.$where);
					
		return $this->fetchAll($select)->toArray();
	}
}
?>