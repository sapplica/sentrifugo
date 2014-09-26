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


class Login_Acl extends Zend_Acl {
    /**
     * __construct
     *
     * @param Zend_Db_Adapter $db
     * @param integer $role
     */
    public function __construct($db,$role) {
        $this->loadRoles($db);
        $roles = new Login_Model_Roles($db);
        $inhRole= $role;
        while (!empty($inhRole)) {
            $this->loadResources($db,$inhRole);
            $this->loadPermissions($db,$inhRole);
            $inhRole= $roles->getParentRole($inhRole);
        }
    }
    /**
     * Load all the roles from the DB
     *
     * @param Zend_Db_Adapter $db
     * @return boolean
     */
    public function loadRoles($db) {
    	if (empty($db)) {
    		return false;
    	}
        $roles = new Login_Model_Roles($db);
        $allRoles = $roles->getRoles();
        foreach ($allRoles as $role) {
            if (!empty($role->id_parent)) {
                $this->addRole(new Zend_Acl_Role($role->id),$role->id_parent);
            } else {
                $this->addRole(new Zend_Acl_Role($role->id));
            }
        }
        return true;
    }
    /**
     * Load all the resources for the specified role
     *
     * @param Zend_Db_Adapter $db
     * @param integer $role
     * @return boolean
     */
    public function loadResources($db,$role) {
    	if (empty($db)) {
    		return false;
    	}
    	$resources= new Login_Model_Resources($db);
    	$allResources= $resources->getResources($role);
    	foreach ($allResources as $res) {
                if (!$this->has($res)) {
                    $this->addResource(new Zend_Acl_Resource($res['resource']));
                }
    	}
        return true;
    }
    /**
     * Load all the permission for the specified role
     *
     * @param Zend_Db_Adapter $db
     * @param integer $role
     * @return boolean
     */
    public function loadPermissions($db,$role) {
    	if (empty($db)) {
    		return false;
    	}
    	$permissions= new Login_Model_Permissions($db);
    	$allPermissions= $permissions->getPermissions($role);
    	foreach ($allPermissions as $res) {
    		if ($res['permission']=='allow') {
    			$this->allow($res['id_role'],$res['resource']);
    		} else {
    			$this->deny($res['id_role'],$res['resource']);
    		}	
    	}
        return true;
    }

}
