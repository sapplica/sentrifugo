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

class Default_Model_Privileges extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_privileges';
    protected $_primary = 'id';
   
    /*
     * This function is used for saving and updating privileges data.
     */
    public function SaveorUpdatePrivilegesData($data, $where)
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
    /*
     * This function is used to get all menu items assigned to particular role id
     * @parameters
     * @role_id   = id of role
     * 
     * returns Array of menus.
     */
    public function getMenuItemsByRoleId($role_id)
    {      
        $result = $this->fetchAll("role = ".$role_id." and isactive = 1")->toArray();       
        return $result;        
    }
	/*
		This function is to get privileges of a particular menu of particular group.
	*/
	public function getObjPrivileges($objId,$groupId = "",$role_id,$idCsv=0)
	{
		$privilege_arr=array();
		$db = Zend_Db_Table::getDefaultAdapter();
        if($objId !="" && $role_id != "" && $idCsv == 0)
		{
			$query = "select addpermission,editpermission,deletepermission,viewpermission,uploadattachments,viewattachments,isactive from main_privileges where isactive = 1  and object =".$objId." and role =".$role_id;
			$result = $db->query($query);
			$privilege_arr = $result->fetch();
			
			
		}
		else if($objId !="" && $role_id != "" && $idCsv == 1)
		{
			$query = "select object,addpermission,viewpermission,viewattachments,isactive from main_privileges where isactive = 1  and object in(".$objId.") and role =".$role_id;
			$result = $db->query($query);
			$privilege_arr = $result->fetchAll();
			
			
		}
        return $privilege_arr;
	}
}//end of class