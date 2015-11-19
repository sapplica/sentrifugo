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
 * @model Expensecategories Model
 * @author sagarsoft
 *
 */
class Timemanagement_Model_Expensecategories extends Zend_Db_Table_Abstract
{
	protected $_name = 'tm_expense_categories';
	protected $_primary = 'id';

	/**
	 * This will fetch all the active expense categories details.
	 *
	 * @param string $sort
	 * @param string $by
	 * @param number $pageNo
	 * @param number $perPage
	 * @param string $searchQuery
	 *
	 * @return array $expenseCategoryData
	 */
	public function getExpenseCategoryData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "is_active = 1";

		if($searchQuery)
		$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();

		$expenseCategoryData = $this->select()
		->setIntegrityCheck(false)
		->where($where)
		->order("$by $sort")
		->limitPage($pageNo, $perPage);

		return $expenseCategoryData;
	}

	/**
	 * This will fetch all the Expense categories details based on the search paramerters passed with pagination.
	 *
	 * @param string $sort
	 * @param string $by
	 * @param number $perPage
	 * @param number $pageNo
	 * @param JSON $searchData
	 * @param string $call
	 * @param string $dashboardcall
	 *
	 * @return array
	 */
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall)
	{
		$searchQuery = '';
		$searchArray = array();
		$data = array();

		if($searchData != '' && $searchData!='undefined')
		{
			$searchValues = json_decode($searchData);
			foreach($searchValues as $key => $val)
			{
				$searchQuery .= " ".$key." like '%".$val."%' AND ";
				$searchArray[$key] = $val;
			}
			$searchQuery = rtrim($searchQuery," AND");
		}
			
		$objName = 'expensecategory';

		$tableFields = array('action'=>'Action','expense_category' => 'Category','unit_price' => 'Unit Price');

		$tablecontent = $this->getExpenseCategoryData($sort, $by, $pageNo, $perPage,$searchQuery);
		$dataTmp = array(
			'sort' => $sort,
			'by' => $by,
			'pageNo' => $pageNo,
			'perPage' => $perPage,				
			'tablecontent' => $tablecontent,
			'objectname' => $objName,
		    'menuName' => 'Expense Category',
			'extra' => array(),
			'tableheader' => $tableFields,
			'jsGridFnName' => 'getAjaxgridData',
			'jsFillFnName' => '',
			'searchArray' => $searchArray,
			'call'=>$call,
			'dashboardcall'=>$dashboardcall
		);
		return $dataTmp;
	}

	public function SaveorUpdateExpenseCategoryData($data, $where)
	{
		if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId($this->_name);
			return $id;
		}
	}

	public function getExpenseCategoryDataById($id)
	{
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('ec'=>$this->_name),array('ec.*'))
		->where('ec.is_active = 1 AND ec.id='.$id.' ');
		return $this->fetchAll($select)->toArray();
	}

	public function checkExpenses($id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select count(*) as count from tm_emp_expenses where expense_cat_id = ".$id." AND is_active = 1";
		$result = $db->query($query)->fetch();
		return $result['count'];
	}

	/**
	 * This method returns all active expense categories to show in expenses screen 
	 *
	 * @return array 
	 */
	public function getActiveExpenseCategoriesData()
	{
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('e'=>$this->_name),array('e.id','e.expense_category'))
		->where('e.is_active = 1 ')
		->order('e.expense_category');
		return $this->fetchAll($select)->toArray();
	}
	
}
