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

class Default_Model_Logmanager extends Zend_Db_Table_Abstract{
	protected $_name = 'main_logmanager';

	/**
	 * This function insert or updates a record in logmanager.
	 * @parameters
	 * @menuId        = Id of menu
	 * @actionflag    = action value to insert
	 * @jsonlogarr    = jsonlogarr value in log_details field
	 * @userid        = userid in last_modifieddate field
	 * @keyflag       = leyflag value in is_active
	 *
	 * @return  ResultSet;
	 */
	public function addOrUpdateLogManager($menuId,$actionflag,$jsonlogarr,$userid,$keyflag)
	{
		$date= gmdate("Y-m-d H:i:s");
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$rows = $db->query("INSERT INTO `main_logmanager` (menuId,user_action,log_details,last_modifiedby,last_modifieddate,key_flag,is_active) VALUES (".$menuId.",".$actionflag.",'".$jsonlogarr."',".$userid.",'".$date."','".$keyflag."',1) ON DUPLICATE KEY UPDATE log_details=concat(log_details,',','".$jsonlogarr."'),last_modifiedby=".$userid.",last_modifieddate='".$date."',key_flag='".$keyflag."' ");

		$id=$this->getAdapter()->lastInsertId('main_logmanager');
		return $id;

	}

	/**
	 * This function gives data for log details.
	 * @parameters
	 * @id      = to fetch specific record
	 *
	 * @return  ResultSet;
	 */
	public function getLogManagerDataByID($id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('cc'=>'main_logmanager'),array('cc.*'))
		->where('cc.id = '.$id.' and cc.is_active = 1 and cc.user_action != 4');
			
		return $this->fetchAll($select)->toArray();
	}

	/**
	 * This function gives data for log report.
	 * @parameters
	 * @sort          = ascending or descending
	 * @by            = name of field which to be sort
	 * @pageNo        = page number
	 * @perPage       = no.of records per page
	 * @searchQuery   = search string
	 *
	 * @return  ResultSet;
	 */
	public function getLogManagerDataReport($by="last_modifieddate",$order="desc",$pageNo=1, $perPage=20,$searchQuery=1,$selectfield=array('*'))
	{
		$where = "menuId != 0 AND is_active = 1 AND user_action != 4";
		$str = '';

		if($searchQuery)
		$where .= " AND ".$searchQuery;
		
	   if($by == 'user_action' && $order == 'asc')
		{
			$str = "case user_action
				when 1 then 1
				when 5 then 2
				when 3 then 3
				when 2 then 4 
				else 99 end";
		}
		else if($by == 'user_action' && ($order == 'desc' || $order == 'Desc')) {
			$str = "case user_action
				when 2 then 1
				when 3 then 2
				when 5 then 3
				when 1 then 4 
				else 99 end";
		}
		

		if($by == ''){

			$select = $this->select()
			->setIntegrityCheck(false)
			->from(array($this->_name),$selectfield)
			->joinInner(array('m'=>'main_menu'), "m.id = menuId and m.isactive = 1",array())
			->where($where)
			->order($order)
			->limitPage($pageNo, $perPage);
		}else if($by == 'user_action'){
			$select = $this->select()
			->setIntegrityCheck(false)
			->from(array($this->_name),$selectfield)
			->joinInner(array('m'=>'main_menu'), "m.id = menuId and m.isactive = 1",array())
			->where($where)
			->order(new Zend_Db_Expr($str))
			->limitPage($pageNo, $perPage);			
		}
		else if($pageNo == ''){
			$select = $this->select()
			->setIntegrityCheck(false)
			->from(array($this->_name),$selectfield)
			->joinInner(array('m'=>'main_menu'), "m.id = menuId and m.isactive = 1",array())
			->where($where)
			->order("$by $order");
			
		}else{
			$select = $this->select()
			->setIntegrityCheck(false)
			->from(array($this->_name),$selectfield)
			->joinInner(array('m'=>'main_menu'), "m.id = menuId and m.isactive = 1",array())
			->where($where)
			->order("$by $order")
			->limitPage($pageNo, $perPage);
		}

		
		$activitylogData = $this->fetchAll($select)->toArray();
		
		return $activitylogData;
	}

	/**
	 * This function gives sorted userid's in string based on userfullname order.
	 * @parameters
	 * @order          = ascending or descending
	 *
	 * @return  String;
	 */
	public function getUsernameOrderForLog($order)
	{
		$users = "";
		$userorderArray = array();
		$qry = "select id from main_users where isactive = 1 order by userfullname {$order}";
		$db = Zend_Db_Table::getDefaultAdapter();
		$sqlRes = $db->query($qry);
		$users = $sqlRes->fetchAll();

		foreach($users as $key => $userid){
			$userorderArray[] = $userid['id'];
		}

		$userids = implode(',',$userorderArray);

		return $userids;
	}

	/**
	 * This function gives sorted userid's in string based on employeeId order.
	 * @parameters
	 * @order          = ascending or descending
	 *
	 * @return  String;
	 */
	public function getEmpidOrderForLog($order)
	{
		$users = "";
		$userorderArray = array();
		$qry = "select id from main_users where isactive = 1 order by employeeId {$order}";
		$db = Zend_Db_Table::getDefaultAdapter();
		$sqlRes = $db->query($qry);
		$users = $sqlRes->fetchAll();

		foreach($users as $key => $userid){
			$userorderArray[] = $userid['id'];
		}

		$userids = implode(',',$userorderArray);

		return $userids;
	}

	/**
	 * This function gives sorted menuid's in string based on menuName order.
	 * @parameters
	 * @order          = ascending or descending
	 *
	 * @return  String;
	 */
	public function getMenuOrderForLog($order)
	{
		$users = "";
		$menuorderArray = array();
		$qry = "select id from main_menu where isactive = 1 order by menuName {$order}";
		$db = Zend_Db_Table::getDefaultAdapter();
		$sqlRes = $db->query($qry);
		$menus = $sqlRes->fetchAll();

		foreach($menus as $key => $menuid){
			$menuorderArray[] = $menuid['id'];
		}

		$menuids = implode(',',$menuorderArray);

		return $menuids;
	}

	/**
	 * This function gives employee name for autocomplete element.
	 * @parameters
	 * @order          = search string
	 *
	 * @return  Resultset;
	 */
	public function getAutoReportEmpnameWithId($search_str)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select u.id,u.profileimg,u.userfullname emp_name,
                  case
                   when u.userfullname like '".$search_str."%' then 4
                   when u.userfullname like '__".$search_str."%' then 2 
                   when u.userfullname like '_".$search_str."%' then 3 
                   when u.userfullname like '%".$search_str."%' then 1 
                  else 0 end emp 
                  from main_users u where (u.userfullname like '%".$search_str."%') 
                  order by emp desc
                  limit 0,10";
		$result = $db->query($query);
		$emp_arr = array();
		$emp_arr = $result->fetchAll();
		return $emp_arr;
	}
	
 
    /**
	 * This function gives data from logmanager table .
	 *
	 * @return  ResultSet;
	 */
	public function getLogManagerData()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('cc'=>'main_logmanager'),array('cc.*'));		
			
		return $this->fetchAll($select)->toArray();
	}
	
	public function UpdateLogManagerWhileCron($id,$jsonlogarr)
	{
	    $date= gmdate("Y-m-d H:i:s");
           
	    $data = array('log_details'=>$jsonlogarr,
					  'last_modifieddate'=>$date						 
					 );
		$where = array('id=?'=>$id);   
	    if($where != ''){
			$this->update($data, $where);
			return true;
		} 
	}
	
	

	

	
}
?>