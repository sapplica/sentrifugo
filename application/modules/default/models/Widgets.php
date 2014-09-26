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

class Default_Model_Widgets extends Zend_Db_Table_Abstract
{
	public function getWidgets($userid,$role_id)
	{	
		$menuDetails=array();$objectCsv="";
		$db = Zend_Db_Table::getDefaultAdapter();
		$menu = $db->query("SELECT menuid from main_settings WHERE isactive = 1 AND flag = 1 AND userid = ".$userid);
		$menuids = $menu->fetch();
			
		if(!empty($menuids) && $menuids['menuid']!= '')
		{	
			//Checking whether the logged in role has privileges for configured menus in widgets...
			$menuPrivilegesquery = "select object from main_privileges where viewpermission = 'Yes' and isactive = 1  and object in(".$menuids['menuid'].") and role =".$role_id;
			$result = $db->query($menuPrivilegesquery);
			$privilegedMenusArr = $result->fetchAll();	
		}
		
		if(!empty($privilegedMenusArr))
		{	
			//Build csv for objects....
			for($i=0;$i<sizeof($privilegedMenusArr);$i++)
			$objectCsv .= $privilegedMenusArr[$i]['object'].",";
			$objectCsv = trim($objectCsv,",");
			
			$widgets =  $db->query("SELECT * FROM main_menu WHERE  FIND_IN_SET(id,'".$objectCsv."');");
			$menuDetails = $widgets->fetchAll();
		}					
		
		return $menuDetails;
	}
	
	public function getWidgetData($sort, $by, $pageNo, $perPage,$searchQuery,$tableName)
	{	
		$where = "isactive = 1 ";
		
		if($searchQuery)
			$where = $searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();	 
		if(!empty($by) && !empty($sort))	{
		
			$widgetData = $db->select()
							   ->from($tableName)
							   ->where($where)
							   ->order("$by $sort") 
							   ->limitPage($pageNo, $perPage);
			}
			else
			{	
				$widgetData = $db->select()
							   ->from($tableName)
							   ->where($where)
							  ->limitPage($pageNo, $perPage);
			}
			
		return $widgetData;   	
	}
	
	public function getTableFields($tableName)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$columns = $db->query("SELECT menufields FROM main_menu WHERE url = '".$tableName."'");
		$columnData = $columns->fetch();                
		return $columnData;
	}
}
?>