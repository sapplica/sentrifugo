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

class Default_Model_Roles extends Zend_Db_Table_Abstract
{
	protected $_name = 'main_roles';
	protected $_primary = 'id';

	/**
	 * This function gives data for grid view.
	 * @parameters
	 * @sort          = ascending or descending
	 * @by            = name of field which to be sort
	 * @pageNo        = page number
	 * @perPage       = no.of records per page
	 * @searchQuery   = search string
	 *
	 * @return  ResultSet;
	 */
	public function getRolesData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "r.isactive = 1";

		if($searchQuery)
		$where .= " AND ".$searchQuery;

		$roleData = $this->select()
		->setIntegrityCheck(false)
		->from(array('r'=>$this->_name),array('r.*',))
		->joinInner(array('g'=>'main_groups'), "g.id = r.group_id and g.isactive = 1",array('group_name'=>'g.group_name'))
		->where($where)
		->order("$by $sort")
		->limitPage($pageNo, $perPage);
		
		return $roleData;
	}
	/*
	 * This function will return all data of role by passing its primay key id.
	 * @parameters
	 * @id   =  id of role (primary key)
	 *
	 * returns Array of role's data.
	 */
	public function getRoleDataById($id)
	{
		$row = $this->fetchRow("isactive = 1 AND id = '".$id."'");
		if (!$row)
		{
			throw new Exception("Could not find row $id");
		}
		return $row->toArray();
	}

	/*
	 * This function is used to save/update data in database.
	 * @parameters
	 * @data  =  array of form data.
	 * @where =  where condition in case of update.
	 *
	 * returns  Primary id when new record inserted,'update' string when a record updated.
	 */
	public function SaveorUpdateRolesData($data, $where)
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
	 * This function is used to get role id and role type except admin role i,e id>1
	 *
	 * returns Array of role ids and role types except admin role.
	 */
	public function getRoleTypesForAccess()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select id,roletype,group_id from main_roles where id > 1 and isactive = 1";
		$result = $db->query($query);
		$role_arr = array();
		while($row = $result->fetch())
		{
			$role_arr[$row['id']]['roletype'] = $row['roletype'];
			$role_arr[$row['id']]['group_id'] = $row['group_id'];
		}

		return $role_arr;
	}
	/**
	 * This function is used to get all roles for any dropdown list.
	 *
	 * @return Array array of role names and ids.
	 */
	public function getRolesList()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select id,rolename from main_roles where isactive = 1 and group_id is not null order by rolename";
		$result = $db->query($query);
		$role_arr = array();
		while($row = $result->fetch())
		{
			$role_arr[$row['id']] = $row['rolename'];
		}

		return $role_arr;
	}
	/**
	 * This function is used to get all roles for dropdown list.
	 *
	 * @return Array array of role names and ids.
	 */
	public function getRolesListForUsers($id='',$type='')
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		if($id == '')
		{
			
			$query = "select r.id,r.rolename 
			          from main_privileges p 
					  inner join main_roles r on r.id = p.role and r.isactive = 1 
					  where p.group_id = ".USERS_GROUP." and p.isactive = 1  and p.role is not null 
					  and p.role not in (select distinct role from main_privileges where object = ".EMPSCREENING." and role is not null 
					  and isactive = 1 and group_id = ".USERS_GROUP.") group by r.id order by r.rolename";
		}
		else 
		{
			if (strpos($type,'Background Agency') !== false) 
			{
				
				$query = "select r.id,r.rolename from main_privileges p
							inner join main_roles r on r.id = p.role
							where object = ".EMPSCREENING." and role is not null and p.isactive = 1 and p.group_id = ".USERS_GROUP.";";
			}
			else
			{
				
				$query = "select r.id,r.rolename 
			          from main_privileges p 
					  inner join main_roles r on r.id = p.role and r.isactive = 1 
					  where p.group_id = ".USERS_GROUP." and p.isactive = 1  and p.role is not null 
					  and p.role not in (select distinct role from main_privileges where object = ".EMPSCREENING." and role is not null 
					  and isactive = 1 and group_id = ".USERS_GROUP.") group by r.id order by r.rolename";
			}
		}
		
		$result = $db->query($query);
		$role_arr = array();
		while($row = $result->fetch())
		{
			$role_arr[$row['id']] = $row['rolename'];
		}

		return $role_arr;
	}
	/**
	 * This function is used to get all roles dropdown list in usermanagement screen.
	 *
	 * @return Array  array of role names and ids.
	 */
	public function getRolesList_UM()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select r.id,r.rolename from main_roles r
                  where r.isactive = 1 and r.group_id is not null 
                  and r.group_id not in (".USERS_GROUP.") order by r.rolename asc";
		$result = $db->query($query);
		$role_arr = array();
		while($row = $result->fetch())
		{
			$role_arr[$row['id']] = $row['rolename'];
		}

		return $role_arr;
	}
        /**
	 * This function is used to get all management roles dropdown list in organisation screen.
	 *
	 * @return Array  array of role names and ids.
	 */
	public function getRolesList_orginfo()
	{
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select r.id,r.rolename from main_roles r
                      where r.isactive = 1 and r.group_id is not null 
                      and r.group_id in (".MANAGEMENT_GROUP.") order by r.rolename asc";
            $result = $db->query($query);
            $role_arr = array();
            while($row = $result->fetch())
            {
                $role_arr[$row['id']] = $row['rolename'];
            }

            return $role_arr;
	}
	/**
	 * This function is used to get all roles dropdown list in emp screen.
	 *
	 * @return Array  array of role names and ids,group ids.
	 */
public function getRolesList_EMP($con='')
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		if(!empty($con) && $con == 'orghead')
		{
			$query = "select r.group_id,r.id,r.rolename from main_roles r
                  where r.isactive = 1 and r.group_id is not null 
                  and r.group_id in (".MANAGEMENT_GROUP.") order by r.rolename asc";
		}
		else
		{
			$query = "select r.group_id,r.id,r.rolename from main_roles r
                  where r.isactive = 1 and r.group_id is not null 
                  and r.group_id not in (".USERS_GROUP.") order by r.rolename asc";
		}
		$result = $db->query($query);
		$role_arr = array();
		while($row = $result->fetch())
		{
			$role_arr[$row['id'].'_'.$row['group_id']] = $row['rolename'];
		}

		return $role_arr;
	}
	
	public function getRolesListByGroupID($groupID)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select r.group_id,r.id,r.rolename from main_roles r
                  where r.isactive = 1 and r.group_id is not null 
                  and r.group_id = ".$groupID." order by r.rolename asc";
		$result = $db->query($query);
		$role_arr = array();
		while($row = $result->fetch())
		{
			$role_arr[$row['id'].'_'.$row['group_id']] = $row['rolename'];
		}

		return $role_arr;
	}
	
    /**
	 * This function is used to get all roles dropdown list in emp screen.
	 *
	 * @return Array  array of role names and ids,group ids.
	 */
	public function getRolesList_USERLOG()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select id,rolename from main_roles where isactive = 1 order by rolename";
		$result = $db->query($query);
		$role_arr = array();
		while($row = $result->fetch())
		{
			$role_arr[$row['id']] = $row['rolename'];
		}

		return $role_arr;
	}
	/*
	 * This function is used to get group for a particular  role.
	 *
	 * returns group id
	 */
	public function getRolegroup($roleId)
	{
		$role_arr=array();
		$db = Zend_Db_Table::getDefaultAdapter();
		if($roleId !=""){
			$query = "select id,group_id from main_roles where isactive = 1 and id=".$roleId;
			$result = $db->query($query);
			$role_arr = array();
			while($row = $result->fetch())
			{
				$role_arr[$row['id']] = $row['group_id'];
			}
		}
		return $role_arr;
	}

	public function getRolesDataByID($id)
	{
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('r'=>'main_roles'),array('r.*'))
		->where('r.isactive = 1 AND r.id='.$id.' ');
		return $this->fetchAll($select)->toArray();
		
	}
	/**
	 * This function gives all content for grid view.
	 * @parameters
	 * @param $sort          = ascending or descending
	 * @param $by            = name of field which to be sort
	 * @param $pageNo        = page number
	 * @param $perPage       = no.of records per page
	 * @param $searchData    = search string
	 * @param $call          = type of call like ajax.
	 * @return  Array;
	 */
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$p2,$p3,$p4,$p5)
	{
		$group_model = new Default_Model_Groups();
		$searchQuery = '';
		$searchArray = array();
		$data = array();

		if($searchData != '' && $searchData!='undefined')
		{
			$searchValues = json_decode($searchData);
			if(count($searchValues) >0)
			{
				foreach($searchValues as $key => $val)
				{
					$searchQuery .= " ".$key." like '%".$val."%' AND ";
					$searchArray[$key] = $val;
				}
				$searchQuery = rtrim($searchQuery," AND");
			}
		}
		$objName = 'roles';

		$tableFields = array('action'=>'Action',
                             'rolename' => 'Role Name',
                             'roletype' => 'Role Type',
                             'roledescription' => 'Role Description',
                             'group_name' => 'Group'
                             );

                             $tablecontent = $this->getRolesData($sort, $by, $pageNo, $perPage,$searchQuery);
                             $group_data = $group_model->getGroupsListForRoles();
                             $group_arr = array();

                             foreach($group_data as $gkey => $gdata)
                             {
                             	$group_arr[$gdata['group_name']] = $gdata['group_name'];
                             }
                             $dataTmp = array(
                'sort' => $sort,
                'by' => $by,
                'pageNo' => $pageNo,
                'perPage' => $perPage,				
                'tablecontent' => $tablecontent,
                'objectname' => $objName,
                'extra' => array(),
                'tableheader' => $tableFields,
                'jsGridFnName' => 'getAjaxgridData',
                'jsFillFnName' => '',
                'searchArray' => $searchArray,
                'call'=>$call,
                'dashboardcall' => $dashboardcall,
                'search_filters' => array(
                    'group_name' =>array(
                        'type'=>'select',
                        'filter_data' => array(''=>'All')+$group_arr,
                             )
                             ),
                             );
                             
                             return $dataTmp;
	}

	/**
	 * This function is used to get all groups for any dropdown list.
	 *
	 * @return Array of role names and ids.
	 */
	public function getRoleListForUserLoginLog()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select r.id,r.rolename,(select count(*) from main_userloginlog where emprole = r.id) cnt
               from main_roles r where r.isactive = 1";
		$result = $db->query($query);
		$role_arr = array();
		while($row = $result->fetch())
		{
			$role_arr[$row['id']]['rolename'] = $row['rolename'];
			$role_arr[$row['id']]['cnt'] = $row['cnt'];
		}

		return $role_arr;
	}
	public function getEmpForRoleMail($group_ids,$object_id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select u.id,u.userfullname,u.emailaddress,r.rolename
                  from main_users u inner join main_roles r on r.id = u.emprole 
                  and r.group_id in (".$group_ids.") and r.isactive = 1 
                  inner join main_privileges p on p.group_id = r.group_id and p.role = r.id 
                  and p.object = ".$object_id." and (p.editpermission = 'Yes' or p.addpermission = 'Yes') and p.isactive = 1 
                  where u.isactive = 1 group by u.id 
                  union 
                  select u.id,u.userfullname,u.emailaddress,r.rolename 
                  from main_users u inner join main_roles r on r.id = u.emprole and r.isactive = 1 
                  and u.emprole = ".SUPERADMINROLE." 
                  where u.isactive = 1 and u.id = ".SUPERADMIN." ";
		$result = $db->query($query);
		$emp_arr = array();
		while($row = $result->fetch())
		{
			$emp_arr[$row['id']]['userfullname'] = $row['userfullname'];
			$emp_arr[$row['id']]['emailaddress'] = $row['emailaddress'];
			$emp_arr[$row['id']]['rolename'] = $row['rolename'];
		}

		return $emp_arr;
	}

	public function getEmpRoleNamesByIds($empRoleIdArray)
	{
		$resultstring = implode(',', $empRoleIdArray);
		$empRoleArray = array();
		if($resultstring)
		{
			try
			{
				$qry = "select ob.id, ob.rolename from main_roles ob
                                        where ob.id IN (".$resultstring.") and ob.isactive = 1";		
				$db = Zend_Db_Table::getDefaultAdapter();
				$sqlRes = $db->query($qry);
				$empRoleRes = $sqlRes->fetchAll();

				if(!empty($empRoleRes))
				{
					foreach($empRoleRes as $empRole)
					{
						$empRoleArray[$empRole['id']]= $empRole['rolename'];
					}
				}
			}
			catch(Exception $e)
			{
				echo "Error Encountered - ".$e->getMessage();
			}
		}
		return $empRoleArray;
	}
    /**
     * This function is used in roles group report to get data.
     * @param String $sort_name = field name to be sort.
     * @param String $sort_type = type of sorting
     * @return Array  Array of groups and their role count.
     */
    public function getdata_for_rolesgroup_rpt($sort_name,$sort_type)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select group_id,group_name,count(*) cnt from main_groups g,main_roles r 
                  where r.group_id = g.id and r.isactive = 1 group by g.id order by ".$sort_name." ".$sort_type;
        $result = $db->query($query);
        $data = $result->fetchAll();
        
        return $data;
    }
    /**
     * This function is used in roles group report to get roles data.
     * @param Integer $group_id = id of group 
     * @param String $sort_name = field name to be sort.
     * @param String $sort_type = type of sorting     
     * @return Array  Array of groups and their role count.
     */
    public function getdata_for_rolesgroup_popup($group_id,$sort_name,$sort_type)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select r.rolename,r.roledescription from main_roles r 
                  where r.isactive = 1 and r.group_id = ".$group_id." order by ".$sort_name." ".$sort_type;
        $result = $db->query($query);
        $data = $result->fetchAll();
        
        return $data;
    }
    /**
     * This function is used to get data for groups,roles and employee report.
     * @param String $sort_name = field name to be sort.
     * @param String $sort_type = sorting type.
     * @return Array  Array of groups,roles and employee count.
     */
    public function getdata_emprolesgroup_rpt($sort_name,$sort_type)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select r.group_id,r.id role_id,g.group_name,r.rolename,count(u.id) user_cnt 
                  from main_roles r 
                  left join main_users u on u.emprole = r.id and u.isactive =1 
                  inner join main_groups g on g.id = r.group_id 
                  where r.isactive = 1 group by r.id order by ".$sort_name." ".$sort_type;
        $result = $db->query($query);
        $data = $result->fetchAll();
        
        return $data;
    }
	
	public function getRolesList_Dept($dept_id = '')
	{	
		$db = Zend_Db_Table::getDefaultAdapter();
		if($dept_id != '')
		$role_str = '';
		else
		$role_str = " and r.group_id in (".MANAGEMENT_GROUP.") ";
		$query = "select r.group_id,r.id,r.rolename from main_roles r
				  where r.isactive = 1 and r.group_id is not null $role_str
				  order by r.rolename asc";  //#
		$result = $db->query($query);
		$role_arr = array();
		while($row = $result->fetch())
		{
			$role_arr[$row['id'].'_'.$row['group_id']] = $row['rolename'];
		}

		return $role_arr;
	}
    public function getTotalRolecnt()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select count(id) cnt from main_roles where isactive = 1 and group_id is not null";
        $result = $db->query($query);
        $row = $result->fetch();
        return $row['cnt'];
    }
	public function getRoles($con='')
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		if(!empty($con) && $con == 'orghead')
		{
			$query = "select r.group_id,r.id,r.rolename from main_roles r
                  where r.isactive = 1 and r.group_id is not null 
                  and r.group_id in (".MANAGEMENT_GROUP.") order by r.rolename asc";
		}
		else
		{
			$query = "select r.group_id,r.id,r.rolename from main_roles r
                  where r.isactive = 1 and r.group_id is not null 
                  and r.group_id not in (".USERS_GROUP.") order by r.rolename asc";
		}
		$result = $db->query($query);
		
		return $result->fetchAll();
	}
	
}//end of class