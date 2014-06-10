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

class Default_Model_Structure extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_businessunits';
    protected $_primary = 'id';
	
	public function getOrgData()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$orgData = $db->query("select id,organisationname from main_organisationinfo where isactive = 1;");
		$result= $orgData->fetch();
		return $result;
	}
	
	public function getUnitData()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$unitData = $db->query("select id,unitname from main_businessunits where isactive = 1 order by unitname asc;");
		$result= $unitData->fetchAll();
		return $result;
	}
	
	public function getDeptData()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$deptData = $db->query("select id,deptname,unitid from main_departments where isactive = 1 order by deptname asc;");
		$result= $deptData->fetchAll();
		return $result;
	}
	
}