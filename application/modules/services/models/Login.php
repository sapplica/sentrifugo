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
class Services_Model_Login extends Zend_Db_Table_Abstract
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
	     $result = array();
	     $employeeid = $postarray['employeeid'];
		 $password= md5($postarray['password']);
	     $check = $this->isActiveUser($employeeid,$password);
              if(!empty($check) && $check[0]['count'] == 1)
			  {
			    $result =  $this->getUserObject($employeeid,$password);                            
				//$uploadpath = array('imagepath' => USER_UPLOAD_PATH.'/'.$result[0]['profileimg']);
				$uploadpath = array('imagepath' => sapp_Global::_getHostBaseURL()."public/uploads/profile/".$result[0]['profileimg']);
				$resultarray = array_merge($result[0], $uploadpath);
				$data = array('status'=>'1',
							  'message'=>'Success',
				              'result' => $resultarray);
			  }
			  else
			  {
			     $userStatusArr =  $this->getActiveStatus($employeeid,$password);
				 if(!empty($userStatusArr))
				 {
				    $userStatus = $userStatusArr[0]['status'];
					$islockaccount = $userStatusArr[0]['isaccountlock'];
					if($userStatus == 0)
						$data = array('status'=>'0','message'=>'Login failed. Employee has been inactivated from the organization.','result' => '');
					else if($userStatus == 2)
						$data = array('status'=>'0','message'=>'Login failed. Employee has resigned from the organization.','result' => '');	
					else if($userStatus == 3)
						$data = array('status'=>'0','message'=>'Login failed. Employee has left the organization.','result' => '');
					else if($userStatus == 4)
						$data = array('status'=>'0','message'=>'Login failed. Employee has been suspended from the organization.','result' => '');
					else if($userStatus == 5)
						$data = array('message'=>'Login failed. Employee deleted.','result' => '');
					else if($islockaccount == 1)
						$data = array('status'=>'0','message'=>'Login failed. Employee has been locked.','result' => '');
				 }else
				 {
				    $data = array('status'=>'0','message'=>'Invalid credentials given','result' => ''); 
				 }
			     
			  }
	     
	  }else if($postarray['employeeid'] == '')
	  {
	     $data = array('status'=>'0','message'=>'Employee Id cannot be empty.','result' => $result);
	  }else if($postarray['password'] == '')
	  {
	     $data = array('status'=>'0','message'=>'Password cannot be empty.','result' => $result);
	  }
	  //echo "<pre>";print_r($data);exit;
	  return $data;
    }
	
	public function changepassword($postarray)
    {
	  $result = array();
	  if(isset($postarray['userid']) && $postarray['userid'] != '' && isset($postarray['currentpassword']) && $postarray['currentpassword'] != '' && isset($postarray['newpassword']) && $postarray['newpassword'] != '' && isset($postarray['confirmpassword']) && $postarray['confirmpassword'] != '')
	  {
		 $userid = $postarray['userid'];
		 $currentpassword = $postarray['currentpassword'];
		 $newpassword = $postarray['newpassword'];
		 $confirmpassword = $postarray['confirmpassword'];
		 $pwd = md5($currentpassword);
		 $newpwd = md5($newpassword);
		 $confpwd = md5($confirmpassword);
	     $userpasswordArr = $this->getEmployeePassword($userid);
		 //echo "<pre>";print_r($userpasswordArr);exit;
              if(!empty($userpasswordArr))
			  {
			    $userpassword = $userpasswordArr[0]['emppassword'];
			    if($pwd == $userpassword)
				{
				    $newpasswordlength = strlen($newpassword);
					$confirmpasswordlength = strlen($confirmpassword);

					if($newpwd == $confpwd && $newpasswordlength >=6 && $newpasswordlength <=15 && $confirmpasswordlength >=6 && $confirmpasswordlength <=15)
					  {
						  if($pwd != $newpwd)
						  {
							$passwordcheckArr = $this->getSitePreferenceCheck($newpassword,$confirmpassword);
								if($passwordcheckArr['newpassword'] == 'true' && $passwordcheckArr['confirmpassword'] == 'true')
								{
								   $this->updateuserpassword($userid,$newpwd);
								   $data = array('status'=>'1','message'=>'Success','result' => '');
								}
								else
								{
									$data = array('status'=>'0','message'=>$passwordcheckArr['newpassword'],'result' => '');
								}
						  }
						  else
						  {
							$data = array('status'=>'0','message'=>'Please choose a different password.','result' => '');
						  }
					  }
					else if($newpwd != $confpwd)
					  {
						$data = array('status'=>'0','message'=>'New password and confirm password did not match.','result' => '');
					  } 
					else if($newpasswordlength < 6)
					  {
						$data = array('status'=>'0','message'=>'New password should be atleast 6 characters long.','result' => '');
					  }	
					else if($newpasswordlength > 15)
					  {
						$data = array('status'=>'0','message'=>'New password cannot be more than 15 characters long.','result' => '');
					  }
					
				}else
				{
				   $data = array('status'=>'0','message'=>'Wrong password. Please enter correct password.','result' => '');
				}
				
			  }
			  else
			  {
				    $data = array('status'=>'0','message'=>'User does not exist.','result' => ''); 
			  }
	     
	  }else if($postarray['userid'] == '')
	  {
	     $data = array('status'=>'0','message'=>'Employee Id cannot be empty.','result' => $result);
	  }else if($postarray['currentpassword'] == '')
	  {
	     $data = array('status'=>'0','message'=>'Current Password cannot be empty.','result' => $result);
	  }
	  else if($postarray['newpassword'] == '')
	  {
	     $data = array('status'=>'0','message'=>'New Password cannot be empty.','result' => $result);
	  }
	  else if($postarray['confirmpassword'] == '')
	  {
	     $data = array('status'=>'0','message'=>'Confirm Password cannot be empty.','result' => $result);
	  }
	  //echo "<pre>";print_r($data);exit;
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
			->from(array('u'=>'main_users'),array('u.id','u.emprole','u.userfullname','u.emailaddress','u.employeeId','u.profileimg'))
			->joinInner(array('r'=>'main_roles'), "u.emprole = r.id and r.isactive = 1",array('group_id'=>'r.group_id'))
			//->joinLeft(array('e'=>'main_employees'),"e.user_id = u.id",array('e.businessunit_id','e.department_id','e.jobtitle_id','e.position_id'))
			->where("u.employeeId = '".$employeeid."' and u.emppassword = '".$password."' and u.isactive = 1");

			$result = $this->fetchAll($query)->toArray();
			
		}
		return $result;

	}	
	
	public function getActiveStatus($employeeid,$password)
	{
	    $result= array();
		if ($employeeid !='' && $password !='')
		{
			$userData = $this->select()->setIntegrityCheck(false)
			->from(array('u' => 'main_users'),array('status' => 'u.isactive','isaccountlock' =>'u.emptemplock'))
			->where("u.employeeId = '".$employeeid."' and u.emppassword = '".$password."' ");
			 $result = $this->fetchAll($userData)->toArray();
		}
		
		return $result;
	}

    public function getEmployeePassword($userid)
	{
	    $result= array();
		if ($userid !='')
		{
			$userData = $this->select()->setIntegrityCheck(false)
			->from(array('u' => 'main_users'),array('emppassword' => 'u.emppassword'))
			->where("u.id = '".$userid."' AND u.isactive = 1 AND u.emptemplock = 0 ");
			 $result = $this->fetchAll($userData)->toArray();
		}
		
		return $result;
	}

    public function getSitePreferenceCheck($newpassword,$confirmpassword)
	{
			$sitepreferenceArr = $this->SitePreferanceData();
			$resultarray = array();
			//echo "<pre>";print_r($sitepreferenceArr);exit;
			if(!empty($sitepreferenceArr))
			{
			  $passwordid = $sitepreferenceArr[0]['passwordid'];
				if($passwordid == 1)
				{
				    if ( !preg_match('/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]+)$/', $newpassword) )
					{
					   $resultarray['newpassword'] = "Please enter only alphanumeric characters.";
					  
					}else
					{
					  $resultarray['newpassword'] = "true";
					}
					
					if ( !preg_match('/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]+)$/', $confirmpassword) )
					{
					   $resultarray['confirmpassword'] = "Please enter only alphanumeric characters.";
					  
					}else
					{
					  $resultarray['confirmpassword'] = "true";
					}
				}
				else if($passwordid == 2)
				{
				    if ( !preg_match('/^(?=.*[0-9])(?=.*[a-zA-Z])(?=.*[.\-#$@&\_*])([a-zA-Z0-9.\-#$@&\_*]+)$/', $newpassword) )
					{
					   $resultarray['newpassword'] = "Please enter only characters,numbers and special characters.";
					  
					}else
					{
					  $resultarray['newpassword'] = "true";
					}
					
					if ( !preg_match('/^(?=.*[0-9])(?=.*[a-zA-Z])(?=.*[.\-#$@&\_*])([a-zA-Z0-9.\-#$@&\_*]+)$/', $confirmpassword) )
					{
					   $resultarray['confirmpassword'] = "Please enter only characters,numbers and special characters.";
					  
					}else
					{
					  $resultarray['confirmpassword'] = "true";
					}
				}
				else if($passwordid == 3)
				{
				    if ( !preg_match('/^[0-9]+$/', $newpassword) )
					{
					   $resultarray['newpassword'] = "Please enter numbers only.";
					  
					}else
					{
					  $resultarray['newpassword'] = "true";
					}
					
					if ( !preg_match('/^[0-9]+$/', $confirmpassword) )
					{
					   $resultarray['confirmpassword'] = "Please enter numbers only.";
					  
					}else
					{
					  $resultarray['confirmpassword'] = "true";
					}
				}
				else if($passwordid == 4)
				{
				    if ( !preg_match('/^(?=.*[0-9])(?=.*[.\-#$@&\_*])([0-9.\-#$@&\_*]+)$/', $newpassword) )
					{
					   $resultarray['newpassword'] = "Please enter only numbers and special characters.";
					  
					}else
					{
					  $resultarray['newpassword'] = "true";
					}
					
					if ( !preg_match('/^(?=.*[0-9])(?=.*[.\-#$@&\_*])([0-9.\-#$@&\_*]+)$/', $confirmpassword) )
					{
					   $resultarray['confirmpassword'] = "Please enter only numbers and special characters.";
					  
					}else
					{
					  $resultarray['confirmpassword'] = "true";
					}
				}
			  
			}else
			{
				if ( !preg_match('/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]+)$/', $newpassword) )
				{
				   $resultarray['newpassword'] = "Please enter only alphanumeric characters.";
				  
				}else
				{
				  $resultarray['newpassword'] = "true";
				}
					
				if ( !preg_match('/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]+)$/', $confirmpassword) )
				{
				   $resultarray['confirmpassword'] = "Please enter only alphanumeric characters.";
				  
				}else
				{
				  $resultarray['confirmpassword'] = "true";
				}
			}
		return $resultarray;	
	}
	
	public function SitePreferanceData()
	{
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('s'=>'main_sitepreference'),array('s.*'))
					    ->where('s.isactive = 1');
		return $this->fetchAll($select)->toArray();
	}
	
	public function updateuserpassword($userid,$newpassword)
	{
	   $db = Zend_Db_Table::getDefaultAdapter();
		if($userid != '' && $newpassword != '')
		{
			$db->query("update main_users  set emppassword = '".$newpassword."' where id='".$userid."' ");
		}
		
	}
	

}