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
class Expenses_Model_Expensehistory extends Zend_Db_Table_Abstract
{
	//echo "expensesmodel";exit;
	protected $_name = 'expense_history';
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
	 * This method will save or update the expense history details based on the expense id.
	 *
	 * @param array $data
	 * @param string $where
	 */
	public function saveOrUpdateExpenseHistory($data, $where){
		
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
	
	/*public function getExpenseHostory($expense_id=0,$limit,$offset)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$where = ' ex.isactive=1';
		if($expense_id>0)
		{
			$where .= ' and ex.expense_id!='.$expense_id;
		}
			
		$expenseData = $this->select()
		->setIntegrityCheck(false)
		->from(array('ex' => 'expense_history'))	  
		->where($where)
		->limit($limit,$offset)
		;
		
		return $this->fetchAll($expenseData)->toArray();
	}*/
	public function getExpenseHistoryCount($expense_id=0)
	{
		$where = '';
		if($expense_id>0)
		{
			$where = ' and e.expense_id!='.$expense_id;
		}
		$db = Zend_Db_Table::getDefaultAdapter();
		$count_query = "select count(id) cnt from expense_history e where e.isactive = 1".$where;
		$count_result = $db->query($count_query);
		$count_row = $count_result->fetch();
		return $count_row['cnt'];  
	}
	public function getExpenseHostory($expense_id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$expenseData = $this->select()
		->setIntegrityCheck(false)
		->from(array('ex' => 'expense_history'))
		->joinInner(array('mu'=>'main_users'), "mu.id = ex.createdby",array('userfullname'=>'mu.userfullname'))		
		->where('ex.isactive=1 and ex.expense_id='.$expense_id);
		return $this->fetchAll($expenseData)->toArray();
	}
	
		
}