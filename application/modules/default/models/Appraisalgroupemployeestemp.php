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

class Default_Model_Appraisalgroupemployeestemp extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_pa_groups_employees_temp';
    protected $_primary = 'id';
	
	public function checkAppraisalRecordexists($groupid,$appraisalid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$options = array();
		
		if($groupid !='null' && $appraisalid !='null')
		{
		 	$query = "select ge.id from main_pa_groups_employees_temp ge where ge.pa_initialization_id='.$appraisalid.' AND ge.group_id ='.$groupid.' AND ge.isactive=1 ";
            $result = $db->query($query);
            $options = $result->fetchAll();
		}    
        return $options;
	}
	
		
	public function SaveorUpdateAppraisalGroupsEmployeesTempData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_pa_groups_employees_temp');
			return $id;
		}
		
	
	} 
		
}