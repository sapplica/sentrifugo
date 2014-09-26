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

class Default_Model_Logmanagercron extends Zend_Db_Table_Abstract{
	protected $_name = 'main_logmanagercron';
	
 public function InsertLogManagerCron($menuId,$actionflag,$jsonlogarr,$userid,$keyflag,$date)
	{
	    $date= gmdate("Y-m-d H:i:s");
		$db = Zend_Db_Table::getDefaultAdapter();
		
				
		$data =  array('menuId' => $menuId,
		'user_action' => $actionflag, 
		'log_details' => $jsonlogarr,
		'last_modifiedby' => $userid,
		'last_modifieddate' => $date,
		'key_flag' => $keyflag,
        'is_active' => 1
		);
		
		$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_logmanagercron');
			return $id;

	}

}