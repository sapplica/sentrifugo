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
 * @author Enrico Zimuel (enrico@zimuel.it)
 */
class Services_Model_Services extends Zend_Db_Table_Abstract
{
	protected $_name = 'main_users';
	protected $_primary = 'employeeId';


	/**
	 * Check if a username is a Ldap user
	 *
	 * @param string $username
	 * @return boolean
	 */
	public function login($postarray)
    {
	  
	  if(isset($postarray['employeeid']) && $postarray['employeeid'] != '' && isset($postarray['password']) && $postarray['password'] != '')
	  {
	     $employeeid = $postarray['employeeid'];
		 $password= md5($postarray['password']);
	     $check = $this->isActiveUser($employeeid,$password);
              if(!empty($check) && $check[0]['count'] == 1)
			  {
			    $result =  $this->getUserObject($employeeid,$password);
				$data = array('message'=>'Success',
				              'result' => $result[0]);
			  }
			  else
			  {
			     $data = array('message'=>'Invalid credentials given' ); 
			  }
	     
	  }else if($postarray['employeeid'] == '')
	  {
	     $data = array('message'=>'Employee Id cannot be empty.');
	  }else if($postarray['password'] == '')
	  {
	     $data = array('message'=>'Password cannot be empty.');
	  }
	  echo "<pre>";print_r($data);exit;
	  return $data;
    }

    public function isActiveUser($employeeid,$password)
    {
	    $result = array();
	    if ($employeeid !='' && $password !='')
		{
			$db = Zend_Db_Table::getDefaultAdapter();
			$query = "SELECT count(*) AS `count` FROM (SELECT `a`.`id` AS `aid`, if(r.group_id in (1,5) and a.userstatus = 'new','old',a.userstatus) 
					AS `def_status` FROM `main_users` AS `a` 
					INNER JOIN `main_roles` AS `r` ON r.id=a.emprole 
					WHERE (a.isactive = 1 AND r.isactive = 1 AND a.emptemplock = 0 AND a.employeeId = '".$employeeid."' AND a.emppassword = '".$password."')) 
					AS `ac` WHERE (ac.def_status = 'old')";
					   
			$result = $db->query($query)->fetchAll();
		}
		return $result;
		//$this->fetchAll($result)->toArray();
	}

    public function getUserObject($employeeid,$password)
	{
	    $result= array();
		if ($employeeid !='' && $password !='')
		{
			$query = $this->select()
			->setIntegrityCheck(false)
			->from(array('u'=>'main_users'),array('u.*'))
			->joinInner(array('r'=>'main_roles'), "u.emprole = r.id and r.isactive = 1",array('group_id'=>'r.group_id'))
			//->joinLeft(array('e'=>'main_employees'),"e.user_id = u.id",array('e.businessunit_id','e.department_id','e.jobtitle_id','e.position_id'))
			->where("u.employeeId = '".$employeeid."' and u.emppassword = '".$password."' and u.isactive = 1");

			$result = $this->fetchAll($query)->toArray();
			
		}
		return $result;

	}	
	

}