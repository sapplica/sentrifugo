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

require_once 'Zend/Db/Table/Abstract.php';

class Timemanagement_Model_States extends Zend_Db_Table_Abstract
{
	protected $_name = 'tbl_states';
	protected $_primary = 'id';

	/**
	 * 
	 * This action is used to get states based on country id.
	 * @param number $country_id
	 */
	public function getStatesByCountryId($country_id){
		$sql = "SELECT * FROM ".$this->_name." WHERE country_id = :param1 AND isactive = :param2";
		$state_data  = $this->_db->fetchAll($sql,array("param1"=>$country_id,"param2"=>1));
		return $state_data;
	}

}