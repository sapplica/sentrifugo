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
 * Login_Model_Users
 *
 * @author
 */
class Default_Model_Menu extends Zend_Db_Table_Abstract
{
	protected $_name = 'main_menu';
	protected $_primary = 'id';

	public function getMenuObjID($url)
	{
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('m'=>'main_menu'), array('m.id'))
		->where('m.url="'.$url.'" ');

		return $this->fetchAll($select)->toArray();
	}
	/**
	 * This function gives all menus in an array.
	 *
	 * @param Integer $role_id    =  id of role.
	 * @param Integer $group_id   =  id of group.
	 * @return  Array.
	 */
	public function getMenuArray($role_id,$group_id)
	{		
            $role_str =" mp.role is null ";
            $group_str = " and mp.group_id = ".$group_id;
            if($role_id == SUPERADMINROLE)
            {
                $role_str = " mp.role = ".$role_id;
                $group_str = " and mp.group_id is null";
            }
            $db = Zend_Db_Table::getDefaultAdapter();

            $sql = "select m.*,mp.addpermission,mp.editpermission,mp.deletepermission,mp.viewpermission,
                    mp.uploadattachments,mp.viewattachments 
                    from main_menu m,main_privileges mp 
                    where m.isactive in (1,2) and m.id=mp.object 
                    and mp.isactive = 1 and ".$role_str."  ".$group_str." and m.parent is not null order by m.parent,m.menuOrder"; 
            $res = $db->query($sql);
            $res = $res->fetchAll();
            return $res;
	}
        /**
	 * This function gives all menus in an array.
	 *
	 * @param Integer $role_id    =  id of role.
	 * @param Integer $group_id   =  id of group.
	 * @return  Array.
	 */
	public function getMenuArray_formenu($role_id,$group_id)
	{		
            
            $role_str = " mp.role = ".$role_id;
            $group_str = " and mp.group_id = ".$group_id;
            if($role_id == SUPERADMINROLE)
            {                
                $group_str = " and mp.group_id is null";
            }
            $db = Zend_Db_Table::getDefaultAdapter();

            $sql = "select m.*,mp.addpermission,mp.editpermission,mp.deletepermission,mp.viewpermission,
                    mp.uploadattachments,mp.viewattachments 
                    from main_menu m,main_privileges mp 
                    where m.isactive in (1,2) and m.id=mp.object 
                    and mp.isactive = 1 and ".$role_str."  ".$group_str." and m.parent is not null order by m.parent,m.menuOrder"; 
            $res = $db->query($sql);
            $res = $res->fetchAll();
            return $res;
	}

	public function getTotalMenuArray()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$sql = "select * from main_menu mid order by mid.parent,mid.menuOrder";

		$res = $db->query($sql);
		$res = $res->fetchAll();
		return $res;
	}
	public function getMenusWithChilds($menu_id)
	{
		$menu_model = new Default_Model_Menu();
		$res = $menu_model->getTotalMenuArray();
		$tmpArr = array();
		$tmpMenuArr = array();
		$tmpMenuDataArr = array();
		for($i = 0; $i < sizeof($res); $i++)
		{
			$tmpMenuArr[$res[$i]['id']] = $res[$i]['parent'];
			$tmp = array();
			$tmp['id'] = $res[$i]['id'];
			$tmp["parent"] =  $res[$i]['parent'];
			$tmp["menuName"] =  $res[$i]['menuName'];
			
			$tmpMenuDataArr[$res[$i]['id']] = $tmp;
		}
		
		$emptyArr = array();
		foreach($tmpMenuArr as $key => $value)
		{
			if($value == 0 && !array_key_exists($value,$tmpArr))
			{
				$tmpArr[$key] = $emptyArr;
				$tmpArr[$key] = $tmpMenuDataArr[$key];
			}
			else if(array_key_exists($value,$tmpArr) && !array_key_exists($key,$tmpArr[$value]))
			$tmpArr[$value]['childs'][$key] = $tmpMenuDataArr[$key];
		}

		foreach($tmpArr as $key => $value_arr)
		{
			if(is_array($value_arr))
			{
				if(isset($value_arr['childs']) && is_array($value_arr['childs']))
				{
					foreach($value_arr['childs'] as $c_key => $ch_value)
					{
						foreach($tmpMenuArr as $tkey => $tvalue)
						{
							if($tvalue == $c_key  && !array_key_exists($tkey,$tmpArr))
							{
								$tmpArr[$key]['childs'][$c_key]['childs'][$tkey] = $tmpMenuDataArr[$tkey];
							}
						}
					}
				}
			}
		}
		$childs_array = array();
		if(array_key_exists($menu_id, $tmpArr))
		{
			$childs_array[] = $menu_id;
			if(isset($tmpArr[$menu_id]['childs']))
			{
				foreach($tmpArr[$menu_id]['childs'] as $chkey1 => $chvalue1)
				{
					$childs_array[] = $chkey1;
					if(isset($chvalue1['childs']))
					{
						foreach($chvalue1['childs'] as $chkey2 => $chvalue2)
						{
							$childs_array[] = $chkey2;
						}
					}
				}
			}
		}
		else
		{
			foreach($tmpArr as $menuid => $menu_data)
			{
				if(isset($tmpArr[$menuid]['childs']))
				{
					foreach($tmpArr[$menuid]['childs'] as $chkey1 => $chvalue1)
					{
						if($chkey1 == $menu_id)
						{
							$childs_array[] = $chkey1;
							if(isset($chvalue1['childs']))
							{
								foreach($chvalue1['childs'] as $chkey2 => $chvalue2)
								{
									$childs_array[] = $chkey2;
								}
							}
						}
					}
				}
			}
		}
		return $childs_array;
	}

	/**
	 * This function will give controllers related to particular role id.
	 * @parameters
	 * @param $role_id    =  id of role.
	 * @returns  Array of controllers.
	 */
	public function getControllersByRole($role_id,$group_id='')
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$query = "select m.menuName menuName,substring(m.url,2) url,SUBSTR(m.url,LOCATE('/',m.url)+1,(CHAR_LENGTH(m.url) - LOCATE('/',REVERSE(m.url)) - LOCATE('/',m.url))) nurl,
				  SUBSTR(m.url,CHAR_LENGTH(SUBSTRING_INDEX(m.url,'/',2))+2) vurl,
				  p.addpermission,p.editpermission,p.deletepermission,p.viewpermission,
                  p.uploadattachments,p.viewattachments,m.modulename from main_menu m inner join main_privileges p on p.object = m.id  
                  where m.url != '/#' and m.isactive = 1 and p.isactive = 1 and p.role = ".$role_id;
		$result = $db->query($query);
		$url_arr = array();

		while($row = $result->fetch())
		{
			$modulename = $row['modulename']==''?'default':$row['modulename'];
			if($modulename=='default') {
				$url = $row['nurl']==''?$row['url']:$row['nurl'];
			}else{
				$url = $row['vurl']==''?$row['url']:$row['vurl'];;
			}	
			$url_arr[$url."controller.php"]['url'] = $url;
			$url_arr[$url."controller.php"]['modulename'] = $modulename;
                        if($url != 'servicerequests')
                            $url_arr[$url."controller.php"]['actions'] = $this->getControllersByRole_helper($row);
                        else 
                        {
                            $service_actions = sapp_Global::generateAccessControl_helper(array('servicerequestscontroller.php'=>array('url'=>'servicerequests')), 'random');
                            $url_arr[$url."controller.php"]['actions'] = $service_actions['servicerequestscontroller.php'];
                        }
			
		}
		$index_arr = array();
		$index_arr['indexcontroller.php']['url'] = "index";
		$index_arr['indexcontroller.php']['modulename'] = "default";
		$index_actions = sapp_Global::generateAccessControl_helper(array('indexcontroller.php'=>array('url'=>'index')), 'random');
		$index_arr['indexcontroller.php']['actions'] = $index_actions['indexcontroller.php'];

		$index_arr['dashboardcontroller.php']['url'] = "dashboard";
		$index_arr['dashboardcontroller.php']['modulename'] = "default";
		$dash_actions = sapp_Global::generateAccessControl_helper(array('dashboardcontroller.php'=>array('url'=>'dashboard')), 'random');
		$index_arr['dashboardcontroller.php']['actions'] = $dash_actions['dashboardcontroller.php'];
                
                if($role_id == SUPERADMINROLE)
                {
                    $index_arr['managemenuscontroller.php']['url'] = "managemenus";
                    $index_arr['managemenuscontroller.php']['modulename'] = "default";
                    $index_actions = sapp_Global::generateAccessControl_helper(array('managemenuscontroller.php'=>array('url'=>'managemenus')), 'random');
                    $index_arr['managemenuscontroller.php']['actions'] = $index_actions['managemenuscontroller.php'];
                    
                    $index_arr['wizardcontroller.php']['url'] = "wizard";
                    $index_arr['wizardcontroller.php']['modulename'] = "default";
                    $index_actions = sapp_Global::generateAccessControl_helper(array('wizardcontroller.php'=>array('url'=>'wizard')), 'random');
                    $index_arr['wizardcontroller.php']['actions'] = $index_actions['wizardcontroller.php'];
                }
                
                if($group_id!='') {
                		if($group_id == HR_GROUP)
		                {
		                    $index_arr['hrwizardcontroller.php']['url'] = "hrwizard";
		                    $index_arr['hrwizardcontroller.php']['modulename'] = "default";
		                    $index_actions = sapp_Global::generateAccessControl_helper(array('hrwizardcontroller.php'=>array('url'=>'hrwizard')), 'random');
		                    $index_arr['hrwizardcontroller.php']['actions'] = $index_actions['hrwizardcontroller.php'];
		                }
                }
		return $url_arr+$index_arr;
	}

	public function getControllersByRole_helper($row)
	{
		
		$permi_arr = array();
		$i = 0;
		if($row['addpermission'] == 'Yes')
		$permi_arr[] = 'add';
		if($row['editpermission'] == 'Yes')
		$permi_arr[] = 'edit';
		if($row['deletepermission'] == 'Yes')
		$permi_arr[] = 'delete';
		if($row['viewpermission'] == 'Yes')
		$permi_arr[] = 'view';
		if($row['uploadattachments'] == 'Yes')
		$permi_arr[] = 'upload';
		if($row['viewattachments'] == 'Yes')
		$permi_arr[] = 'uploadview';
		$permi_arr[] = $row['menuName'];
		return $permi_arr;
	}

	public function UpdateMenuTable($resarr)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$query = "UPDATE main_menu SET isactive=0 WHERE id NOT IN(".$resarr.") ";
		$result = $db->query($query);

	}

	public function UpdateMenus($querystring)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$query =  "".$querystring." " ;
		$result = $db->query($query);

	}

	public function getExcludedMenuids()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$query = "select id,menuName,nav_ids from main_menu where (nav_ids like '%,2,%' or nav_ids like '%,70,%' or nav_ids like '%,131,%') ";
		$result = $db->query($query);
		return $result->fetchAll();

	}

	public function addOrUpdateMenuLogManager($menuId,$actionflag,$jsonlogarr,$userid,$menuNames)
	{
		$date= gmdate("Y-m-d H:i:s");
		$db = Zend_Db_Table::getDefaultAdapter();
		$rows = $db->query("INSERT INTO `main_logmanager` (menuId,user_action,log_details,last_modifiedby,last_modifieddate,key_flag,is_active) VALUES (".$menuId.",".$actionflag.",'".$jsonlogarr."',".$userid.",'".$date."','".$menuNames."',1) ON DUPLICATE KEY UPDATE log_details=concat(log_details,',','".$jsonlogarr."'),key_flag = '".$menuNames."',last_modifiedby=".$userid.",last_modifieddate='".$date."' ");
		
		$id=$this->getAdapter()->lastInsertId('main_logmanager');
		return $id;

	}

	public function getisactivemenus()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$arr_menu_ids = array(TIMEMANAGEMENT, PERFORMANCEAPPRAISAL, RESOURCEREQUISITION, BGCHECKS, STAFFING, COMPLIANCES, REPORTS, BENEFITS, SERVICEDESK,EXPENSES, ASSETS);
		$str_menu_ids = implode(",", $arr_menu_ids); 
		
		$sql = "select id,menuName,isactive from main_menu where id in ($str_menu_ids) and isactive = 1";
		$res = $db->query($sql);
		$res = $res->fetchAll();
		return $res;
	}

	/**
	 * This function is used to get all menu for any dropdown list.
	 *
	 * @return Array of menu names and ids.
	 */
	public function getMenusListForActivitylog()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select g.id,g.menuname,(select count(*) from main_logmanager where is_active =1 and menuId = g.id) cnt
                   from main_menu g where g.isactive = 1";
		$result = $db->query($query);
		$menu_arr = array();
		while($row = $result->fetch())
		{
			$menu_arr[$row['id']]['menuname'] = $row['menuname'];
			$menu_arr[$row['id']]['cnt'] = $row['cnt'];
		}

		return $menu_arr;
	}

	/**
	 * This function is used to menu Array.
	 *
	 * @return Array of menu Array.
	 */
	public function getMenusDataById($id)
	{
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('m'=>'main_menu'), array('m.url'))
		->where('m.id='.$id.'');

		return $this->fetchAll($select)->toArray();
	}

	/**
	 * This function is used menus which are clickable
	 *
	 * @return Array of menu Array.
	 */

	public function getMenuArrayActivityLogReport()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$noOperationMenus = APPROVEDLEAVES.','.CANCELLEAVES.','.EMPLOYEETABS.','.EMPLEAVESUMMARY.','.MYHOLIDAYCALENDAR.','.HEIRARCHY.','.STRUCTURE.','.MYEMPLOYEES.','.REJECTEDLEAVES.','.REJECTEDREQUISITIONS.','.REPORTS.','.VENDORSCREENING.','.MYDETAILS;
		$sql = "select * from main_menu mid where mid.url != '/#' and mid.id NOT IN(".$noOperationMenus.") AND mid.isactive = 1 order by mid.menuName";

		$res = $db->query($sql);
		$res = $res->fetchAll();
		return $res;
	}
    /**
     * This function is used as helper function in roles and privileges screen.On ajax call this function is used to 
     * get all menu items and their privileges based on group,role ids.
     * @param Integer $group_id   = id of group
     * @param Integer $role_id    = id of role(useful when calling from different controller)
     * @param Integer $id         = id of role(fetch privileges of a particular role)
     * @return Array Array of menu items,privileges etc.
     */    
    public function getgroupmenu($group_id,$role_id,$id)
    {
        $priveleges_model = new Default_Model_Privileges();
        $group_model = new Default_Model_Groups();
        $group_data = $group_model->getGroupDataById($group_id);
        
        
        $res = $this->getMenuArray($role_id,$group_id);
        $tmpArr = array();                        
        $tmpMenuArr = array();
        $tmpMenuDataArr = array();
        
        for($i = 0; $i < sizeof($res); $i++)
        {
            $tmpMenuArr[$res[$i]['id']] = $res[$i]['parent'];
            $tmp = array();
            $tmp['id'] = $res[$i]['id'];
            $tmp["parent"] =  $res[$i]['parent'];
            $tmp["menuName"] =  $res[$i]['menuName'];
            $tmp["iconPath"] =  $res[$i]['iconPath'];
            $tmp["url"] =  $res[$i]['url'];
            $tmp['default_permissions']["addpermission"] =  $res[$i]['addpermission'];
            $tmp['default_permissions']["editpermission"] =  $res[$i]['editpermission'];
            $tmp['default_permissions']["deletepermission"] =  $res[$i]['deletepermission'];
            $tmp['default_permissions']["viewpermission"] =  $res[$i]['viewpermission'];
            $tmp['default_permissions']["uploadattachments"] =  $res[$i]['uploadattachments'];
            $tmp['default_permissions']["viewattachments"] =  $res[$i]['viewattachments'];

            $tmpMenuDataArr[$res[$i]['id']] = $tmp;
        }

        

        /**
        ** prepare an array with parent and second level menus - $tmpArr
        **/
        $emptyArr = array();
        foreach($tmpMenuArr as $key => $value)
        {
            if($value == 0 && !array_key_exists($value,$tmpArr))
            {
                $tmpArr[$key] = $emptyArr;
                $tmpArr[$key] = $tmpMenuDataArr[$key];
            }
            else if(array_key_exists($value,$tmpArr) && !array_key_exists($key,$tmpArr[$value]))
                $tmpArr[$value]['childs'][$key] = $tmpMenuDataArr[$key];				
        }

        foreach($tmpArr as $key => $value_arr)
        {
            if(is_array($value_arr))
            {
                if(isset($value_arr['childs']) && is_array($value_arr['childs']))
                {
                   foreach($value_arr['childs'] as $c_key => $ch_value)
                   {                                                                                           
                        foreach($tmpMenuArr as $tkey => $tvalue)
                        {                               
                            if($tvalue == $c_key  && !array_key_exists($tkey,$tmpArr))
                            {                    
                                $tmpArr[$key]['childs'][$c_key]['childs'][$tkey] = $tmpMenuDataArr[$tkey];
                            }
                        }                                                            
                    }
                }
            }
        }//end of foreach.
        
        $menu_data = array();
        $menu_data_post = array();
        $permission_data = array();
        if($id != '')
        {                      
            $mdata = $priveleges_model->getMenuItemsByRoleId($id);                
            foreach($mdata as $val_arr)
            {
                $menu_data[] = $val_arr['object'];
                $menu_data_post[$val_arr['id']] = $val_arr['object'];
                if(isset($tmpMenuArr[$val_arr['object']]))
                {
                    if($tmpMenuArr[$val_arr['object']] != 0)                    
                        $menu_data[] = $tmpMenuArr[$val_arr['object']];
                }
                $permission_data[$val_arr['object']]['addpermission'] = $val_arr['addpermission'];
                $permission_data[$val_arr['object']]['editpermission'] = $val_arr['editpermission'];
                $permission_data[$val_arr['object']]['deletepermission'] = $val_arr['deletepermission'];
                $permission_data[$val_arr['object']]['viewpermission'] = $val_arr['viewpermission'];
                $permission_data[$val_arr['object']]['uploadattachments'] = $val_arr['uploadattachments'];
                $permission_data[$val_arr['object']]['viewattachments'] = $val_arr['viewattachments'];
            }
            $menu_data1 = array();
            foreach($menu_data as $menu_id)
            {
                if(array_key_exists($menu_id, $tmpMenuArr))
                    $menu_data1[] = $tmpMenuArr[$menu_id];
            }

            $menu_data1 = array_unique($menu_data1);                                
            $menu_data = array_unique($menu_data);                
            $menu_data = array_merge_recursive($menu_data,$menu_data1);
            
            
        }
        
        $return_arr = array('tmpArr' => $tmpArr,'menu_data_post' => $menu_data_post,
                             'menu_data' => $menu_data,'permission_data' => $permission_data,'group_data' => $group_data);
              
        return $return_arr;
        
    }
    
    /**
     * This function is used as helper function for building menu.
     * @param Integer $group_id   = id of group
     * @param Integer $role_id    = id of role(useful when calling from different controller)
     * @param Integer $id         = id of role(fetch privileges of a particular role)
     * @return Array Array of menu items,privileges etc.
     */    
    public function getgroup_formenu($group_id,$role_id,$id)
    {
        $priveleges_model = new Default_Model_Privileges();
        $group_model = new Default_Model_Groups();
        $group_data = $group_model->getGroupDataById($group_id);
        
        
        $res = $this->getMenuArray_formenu($role_id,$group_id);
        $tmpArr = array();                        
        $tmpMenuArr = array();
        $tmpMenuDataArr = array();
        
        for($i = 0; $i < sizeof($res); $i++)
        {
            $tmpMenuArr[$res[$i]['id']] = $res[$i]['parent'];
            $tmp = array();
            $tmp['id'] = $res[$i]['id'];
            $tmp["parent"] =  $res[$i]['parent'];
            
            $tmp["menuName"] =  $this->getMenuText($res[$i]['menuName'], $role_id);
            $tmp["iconPath"] =  $res[$i]['iconPath'];
            $tmp["url"] =  $res[$i]['url'];
            
            $tmp['default_permissions']["addpermission"] =  $res[$i]['addpermission'];
            $tmp['default_permissions']["editpermission"] =  $res[$i]['editpermission'];
            $tmp['default_permissions']["deletepermission"] =  $res[$i]['deletepermission'];
            $tmp['default_permissions']["viewpermission"] =  $res[$i]['viewpermission'];
            $tmp['default_permissions']["uploadattachments"] =  $res[$i]['uploadattachments'];
            $tmp['default_permissions']["viewattachments"] =  $res[$i]['viewattachments'];

            $tmpMenuDataArr[$res[$i]['id']] = $tmp;
        }

        

        /**
        ** prepare an array with parent and second level menus - $tmpArr
        **/
        $emptyArr = array();
        foreach($tmpMenuArr as $key => $value)
        {
            if($value == 0 && !array_key_exists($value,$tmpArr))
            {
                $tmpArr[$key] = $emptyArr;
                $tmpArr[$key] = $tmpMenuDataArr[$key];
            }
            else if(array_key_exists($value,$tmpArr) && !array_key_exists($key,$tmpArr[$value]))
                $tmpArr[$value]['childs'][$key] = $tmpMenuDataArr[$key];				
        }

        foreach($tmpArr as $key => $value_arr)
        {
            if(is_array($value_arr))
            {
                if(isset($value_arr['childs']) && is_array($value_arr['childs']))
                {
                   foreach($value_arr['childs'] as $c_key => $ch_value)
                   {                                                                                           
                        foreach($tmpMenuArr as $tkey => $tvalue)
                        {                               
                            if($tvalue == $c_key  && !array_key_exists($tkey,$tmpArr))
                            {                    
                                $tmpArr[$key]['childs'][$c_key]['childs'][$tkey] = $tmpMenuDataArr[$tkey];
                            }
                        }                                                            
                    }
                }
            }
        }//end of foreach.
        
        $menu_data = array();
        $menu_data_post = array();
        $permission_data = array();
        if($id != '')
        {                      
            $mdata = $priveleges_model->getMenuItemsByRoleId($id);                
            foreach($mdata as $val_arr)
            {
                $menu_data[] = $val_arr['object'];
                $menu_data_post[$val_arr['id']] = $val_arr['object'];
                if(isset($tmpMenuArr[$val_arr['object']]))
                {
                    if($tmpMenuArr[$val_arr['object']] != 0)                    
                        $menu_data[] = $tmpMenuArr[$val_arr['object']];
                }
                $permission_data[$val_arr['object']]['addpermission'] = $val_arr['addpermission'];
                $permission_data[$val_arr['object']]['editpermission'] = $val_arr['editpermission'];
                $permission_data[$val_arr['object']]['deletepermission'] = $val_arr['deletepermission'];
                $permission_data[$val_arr['object']]['viewpermission'] = $val_arr['viewpermission'];
                $permission_data[$val_arr['object']]['uploadattachments'] = $val_arr['uploadattachments'];
                $permission_data[$val_arr['object']]['viewattachments'] = $val_arr['viewattachments'];
            }
            $menu_data1 = array();
            foreach($menu_data as $menu_id)
            {
                if(array_key_exists($menu_id, $tmpMenuArr))
                    $menu_data1[] = $tmpMenuArr[$menu_id];
            }

            $menu_data1 = array_unique($menu_data1);                                
            $menu_data = array_unique($menu_data);                
            $menu_data = array_merge_recursive($menu_data,$menu_data1);
            
            
        }
        
        $return_arr = array('tmpArr' => $tmpArr,'menu_data_post' => $menu_data_post,
                             'menu_data' => $menu_data,'permission_data' => $permission_data,'group_data' => $group_data);
              
        return $return_arr;
        
    }
    /**
     * This function is used as helper function  for services to build menu.     
     * @param Integer $role_id    = id of role
     * @param Integer $group_id   = id of group      
     * @return Array Array of menu items,privileges etc.
     */    
    public function getgroupmenu_service($group_id,$role_id)
    {        
        $default_parent_menu = sapp_Global::mobile_parent_menus();
        $default_child_menu = sapp_Global::mobile_child_menus();
        
        $res = $this->getMenuArray($role_id,$group_id);
        $tmpArr = array();                        
        $tmpMenuArr = array();
        $tmpMenuDataArr = array();
        
        for($i = 0; $i < sizeof($res); $i++)
        {
            $tmpMenuArr[$res[$i]['id']] = $res[$i]['parent'];
            $tmp = array();
            $tmp['id'] = $res[$i]['id'];
            
            $tmp["menuName"] =  $res[$i]['menuName'];
            
            $tmpMenuDataArr[$res[$i]['id']] = $tmp;
        }        

        /**
        ** prepare an array with parent and second level menus - $tmpArr
        **/
        $emptyArr = array();
        foreach($tmpMenuArr as $key => $value)
        {
            if($value == 0 && !array_key_exists($value,$tmpArr) && in_array($key, $default_parent_menu))
            {
                $tmpArr[$key] = $emptyArr;
                $tmpArr[$key] = $tmpMenuDataArr[$key];
            }
            else if(array_key_exists($value,$tmpArr) && !array_key_exists($key,$tmpArr[$value]) )
                $tmpArr[$value]['childs'][$key] = $tmpMenuDataArr[$key];				
        }
        
        foreach($tmpArr as $key => $value_arr)
        {
            if(is_array($value_arr))
            {
                if(isset($value_arr['childs']) && is_array($value_arr['childs']))
                {
                   foreach($value_arr['childs'] as $c_key => $ch_value)
                   {                                                                                           
                        foreach($tmpMenuArr as $tkey => $tvalue)
                        {                               
                            if($tvalue == $c_key  && !array_key_exists($tkey,$tmpArr) && in_array($tkey, $default_child_menu))
                            {                                                    
                                $tmpArr[$key]['childs'][$tkey] = $tmpMenuDataArr[$tkey];
                            }
                        }                                                            
                    }
                }
            }
        }//end of foreach. 
        
        foreach($tmpArr as $key => $value_arr)
        {
            if(is_array($value_arr))
            {
                if(isset($value_arr['childs']) && is_array($value_arr['childs']))
                {
                    $new_childs = array();
                    foreach($value_arr['childs'] as $c_key => $ch_value)
                    {                                                                                           
                        if(in_array($c_key,$default_child_menu))
                            $new_childs[$c_key] = $ch_value;  
                    }
                    $tmpArr[$key]['childs'] = $new_childs;
                }
            }
        }//end of foreach.        
                      
        return $tmpArr;        
    }
	
	public function checkmenustatus($menuid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = "select * from main_menu mid where id=".$menuid;

		$res = $db->query($sql);
		$res = $res->fetch();
		return $res;
	}
	
    /**
	 * This function is used to get menunames in string by id's
	 *
	 * @return string of menu names.
	 */
	public function getMenusNamesByIds($menuIds)
	{
            $menu_string = "";
            if($menuIds != '')
            {
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select g.menuname from main_menu g where g.id IN (".$menuIds.") AND g.isactive = 1";
		
		$result = $db->query($query);
		$menu_string = '';
		while($row = $result->fetch())
		{
                    $menu_string .= $row['menuname'].',';			
		}
                $menu_string =  trim($menu_string, ",");
            }
            return $menu_string;
	}
	
	/**
	 * 
	 * This function is used to change menu text for 'Scheduled Interviews'
	 */
	public function getMenuText($menu_name, $group_id =''){
	            
            // Text 'Scheduled Interviews' has to be shown to Super Admin, Management, HR. Text 'Scheduled Interviews' has to be shown to remaining role groups.
            
            if($menu_name == 'Scheduled Interviews'){
            	if(empty($group_id)){
					$auth = Zend_Auth::getInstance();
		            $group_id = $auth->getStorage()->read()->group_id;            		
            	}
            	if(in_array($group_id, array(MANAGEMENT_GROUP,MANAGER_GROUP,EMPLOYEE_GROUP)) || empty($group_id)){
                    
            		return 'Scheduled Interviews';
            	}
            }
            return $menu_name;		
	}
	
}