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
 *
 * @model Expenses history Model
 * @author sagarsoft
 *
 */
class Expenses_Model_Triphistory extends Zend_Db_Table_Abstract
{
	//echo "expensesmodel";exit;
	protected $_name = 'expense_trip_history';
	protected $_primary = 'id';

	/**
	 * This will fetch all the client details based on the search paramerters passed with pagination.
	 *
	 * @param string $sort
	 * @param string $by
	 * @param number $perPage
	 * @param number $pageNo
	 * @param JSON $searchData
	 * @param string $call
	 * @param string $dashboardcall
	 * @param string $a
	 * @param string $b
	 * @param string $c
	 * @param string $d
	 *
	 * @return array
	 */
	

	/**
	 * This method will save or update the trip history details based on the trip id.
	 *
	 * @param array $data
	 * @param string $where
	 */
	public function saveOrUpdateTripHistory($data, $where){
		
		//echo "<pre>";print_r($data);exit;
		if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId($this->_name);
			return $id;
		}
	}	
	
	
	public function getTripHistory($trip_id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$tripData = $this->select()
		->setIntegrityCheck(false)
		->from(array('ex' => 'expense_trip_history'))
		->joinInner(array('mu'=>'main_users'), "mu.id = ex.createdby",array('userfullname'=>'mu.userfullname'))		
		->where('ex.isactive=1 and ex.trip_id='.$trip_id);
		return $this->fetchAll($tripData)->toArray();
	}
	
		
}