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

class Default_Model_Groups extends Zend_Db_Table_Abstract
{
	protected $_name = 'main_groups';
	protected $_primary = 'id';

	/**
	 * This function is used to save/update data in database.
	 * @parameters
	 * @param data  =  array of form data.
	 * @param where =  where condition in case of update.
	 *
	 * @return  Primary id when new record inserted,'update' string when a record updated.
	 */
	public function SaveorUpdateGroupsData($data, $where)
	{
		if($where != '')
		{
			$this->update($data, $where);
			return 'update';
		}
		else
		{
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId($this->_name);
			return $id;
		}
	}

	/**
	 * This function is used to get all groups for any dropdown list.
	 *
	 * @return Array of group names and ids.
	 */
	public function getGroupsListForRoles()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select g.id,g.group_name,(select count(*) from main_roles where isactive =1 and group_id = g.id) cnt
                   from main_groups g where g.isactive = 1";
		$result = $db->query($query);
		$group_arr = array();
		while($row = $result->fetch())
		{
			$group_arr[$row['id']]['group_name'] = $row['group_name'];
			$group_arr[$row['id']]['cnt'] = $row['cnt'];
		}

		return $group_arr;
	}

	/**
	 * This function is used to get all groups for any dropdown list.
	 *
	 * @return Array of group names and ids.
	 */
	public function getGroupsListForUserLoginLog()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select g.id,g.group_name,(select count(*) from main_userloginlog where group_id = g.id) cnt
                   from main_groups g where g.isactive = 1";
		$result = $db->query($query);
		$group_arr = array();
		while($row = $result->fetch())
		{
			$group_arr[$row['id']]['group_name'] = $row['group_name'];
			$group_arr[$row['id']]['cnt'] = $row['cnt'];
		}

		return $group_arr;
	}
	/**
	 * This function will fetch all data of a particular group id.
	 * @param Integer $group_id = id of group.
	 * @return Array of group data.
	 */
	public function getGroupDataById($group_id)
	{
            if($group_id != '')
            {
		$data = $this->fetchRow("id = ".$group_id)->toArray();
		return $data;
            }
            else 
                return array();
	}

	/**
	 * This function is used to get all group for any dropdown list.
	 *
	 * @return Array array of group names and ids.
	 */
	public function getGroupList()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select id,group_name from main_groups where isactive = 1 order by group_name";
		$result = $db->query($query);
		$group_arr = array();
		while($row = $result->fetch())
		{
			$group_arr[$row['id']] = $row['group_name'];
		}

		return $group_arr;
	}

	/**
	 * This function is used to get all groupnames by passing id's.
	 *
	 * @return Array array of group names and ids.
	 */
	public function getGroupNamesByIds($groupIdArray)
	{
		$resultstring = implode(',', $groupIdArray);
		$groupArray = array();
		if($resultstring)
		{
			try
			{
				$qry = "select ob.id, ob.group_name from main_groups ob
                                        where ob.id IN (".$resultstring.") and ob.isactive = 1";		
				$db = Zend_Db_Table::getDefaultAdapter();
				$sqlRes = $db->query($qry);
				$groupRes = $sqlRes->fetchAll();

				if(!empty($groupRes))
				{
					foreach($groupRes as $group)
					{
						$groupArray[$group['id']]= $group['group_name'];
					}
				}
			}
			catch(Exception $e)
			{
				echo "Error Encountered - ".$e->getMessage();
			}
		}
		return $groupArray;
	}

}//end of class