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
 * Login Admin Auth
 * 
 * @author K.Manjunath
 */
class Login_Adminauth {
	
	private function __construct() {
		// private - should not be used
	}
	
	public static function _getAdapter($adapter,$options) {
		
		if (empty($adapter) || empty($options) || !is_array($options)) {
            return false;
        }
        if (!in_array($adapter,array('ldap','db'))) {
        	return false;
        }
		if (!array_key_exists('username',$options) ||  !array_key_exists('user_password',$options)) {
			return false;
		} 
		
		$username= $options['username'];
		$password= $options['user_password'];
        switch ($adapter) {
        	case 'ldap' :
        		
        		$auth= new Zend_Auth_Adapter_Ldap($options['ldap'],$username,$password);
        		break;
        	case 'db' :
        		
				
				$password=md5($password);
        		$auth= new Zend_Auth_Adapter_DbTable($options['db'],
        									    	 'tbl_users',
        											 'email',
        											 'user_password');
        		
        		$auth->setIdentity($username)->setCredential($password);
        		break;
        }
        return $auth;
	}
}