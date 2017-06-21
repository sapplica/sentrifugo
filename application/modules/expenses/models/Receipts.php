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
 * @model Trips Model
 * @author sagarsoft
 *
 */
class Expenses_Model_Receipts extends Zend_Db_Table_Abstract
{
	//echo "tripsmodel";exit;
	protected $_name = 'expense_receipts';
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
	public function getReceipts($isUnreported='',$limit=0,$offset=0,$search_str='',$start_date='',$end_date='')
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$where = "er.isactive = 1 and er.createdby = ".$loginUserId;
		
		
		if($isUnreported!='' && $isUnreported!='all')
			$where = "er.isactive = 1 AND er.trip_id IS NULL AND (er.expense_id IS NULL OR er.expense_id =0) and er.createdby = ".$loginUserId;
		
		
		if($search_str!='')
		{
			//$where .= " AND (er.receipt_name LIKE ".$db->quote(trim('%'.trim($search_str).'%'))."  OR ex.expense_name LIKE ".$db->quote(trim('%'.trim($search_str).'%'))."  OR extr.trip_name LIKE ".$db->quote(trim('%'.trim($search_str).'%'))." OR c.currencycode LIKE ".$db->quote(trim('%'.trim($search_str).'%'))." OR exca.expense_category_name LIKE ".$db->quote(trim('%'.trim($search_str).'%')).") ";
			
			$where .= " AND (er.receipt_name LIKE ".$db->quote(trim('%'.trim($search_str).'%'))."  OR ex.expense_name LIKE ".$db->quote(trim('%'.trim($search_str).'%'))." OR extr.trip_name LIKE ".$db->quote(trim('%'.trim($search_str).'%')) ." OR exca.expense_category_name LIKE ".$db->quote(trim('%'.trim($search_str).'%')) ." OR c.currencycode LIKE ".$db->quote(trim('%'.trim($search_str).'%')). ") ";
		}
		if($start_date=='' && $end_date=='')
		{
			$start_date = date('01-m-Y');
			$end_date = date('d-m-Y');
		}
		if($start_date!='' && $end_date!='')
		{
			$start_date = date('Y-m-d',strtotime($start_date));
			$end_date = date('Y-m-d',strtotime($end_date));
			$where .= " AND (date(er.createddate) >= '".$start_date."' AND date(er.createddate) <= '".$end_date."') ";
		}
		

		$receiptsData = $this->select()
		->setIntegrityCheck(false)
		->from(array('er' => 'expense_receipts'))
		->joinLeft(array('ex'=>'expenses'), "ex.id = er.expense_id and ex.isactive=1",array('expense_name'=>'ex.expense_name','expense_amount'=>'ex.expense_amount','status'=>'ex.status','exp_trip_id'=>'ex.trip_id'))
		->joinLeft(array('c'=>'main_currency'), "c.id = ex.expense_currency_id and ex.isactive=1",array('currencycode'=>'c.currencycode'))
		->joinLeft(array('extr'=>'expense_trips'), "extr.id = ex.trip_id and extr.isactive=1",array('trip_name'=>'extr.trip_name'))
		->joinLeft(array('exca'=>'expense_categories'), "exca.id = ex.category_id and ex.isactive=1",array('expense_category_name'=>'exca.expense_category_name'))
		->where($where)
		->order("id DESC")
		->limit($limit,$offset)
		;
		
		return $this->fetchAll($receiptsData)->toArray();
	}
	public function getReceiptsCount($isUnreported='',$search_str='',$start_date='',$end_date='')
	{
		/*$db = Zend_Db_Table::getDefaultAdapter();
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$where = "";
		if($isUnreported!='')
		{
			$where = " and e.trip_id IS NULL AND (e.expense_id IS NULL OR e.expense_id=0)";
		}
		if($search_str!='')
		{
			$where .= " AND (e.receipt_name LIKE ".$db->quote(trim('%'.trim($search_str).'%'))."  OR ex.expense_name LIKE ".$db->quote(trim('%'.trim($search_str).'%')).") ";
			//$where .= " AND (er.receipt_name LIKE ".$db->quote(trim('%'.trim($search_str).'%'))."  OR ex.expense_name LIKE ".$db->quote(trim('%'.trim($search_str).'%'))."  OR extr.trip_name LIKE ".$db->quote(trim('%'.trim($search_str).'%'))." OR c.currencycode LIKE ".$db->quote(trim('%'.trim($search_str).'%'))." OR exca.expense_category_name LIKE ".$db->quote(trim('%'.trim($search_str).'%')).") ";
		}
		if($start_date=='' && $end_date=='')
		{
			$start_date = date('01-m-Y');
			$end_date = date('d-m-Y');
		}
		if($start_date!='' && $end_date!='')
		{
			$start_date = date('Y-m-d',strtotime($start_date));
			$end_date = date('Y-m-d',strtotime($end_date));
			$where .= " AND (date(e.createddate) >= '".$start_date."' AND date(e.createddate) <= '".$end_date."') ";
		}
		$count_query = "select count(e.id) cnt from expense_receipts e 
		left join expenses ex on ex.id = e.expense_id and ex.isactive=1
		where e.isactive = 1 and e.createdby = ".$loginUserId.''.$where;
		
		$count_result = $db->query($count_query);
		$count_row = $count_result->fetch();
		return $count_row['cnt'];  */
		
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$where = "er.isactive = 1 and er.createdby = ".$loginUserId;
		
		
		if($isUnreported!='' && $isUnreported!='all')
			$where = "er.isactive = 1 AND er.trip_id IS NULL AND (er.expense_id IS NULL OR er.expense_id =0) and er.createdby = ".$loginUserId;
		
		
		if($search_str!='')
		{
			//$where .= " AND (er.receipt_name LIKE ".$db->quote(trim('%'.trim($search_str).'%'))."  OR ex.expense_name LIKE ".$db->quote(trim('%'.trim($search_str).'%'))."  OR extr.trip_name LIKE ".$db->quote(trim('%'.trim($search_str).'%'))." OR c.currencycode LIKE ".$db->quote(trim('%'.trim($search_str).'%'))." OR exca.expense_category_name LIKE ".$db->quote(trim('%'.trim($search_str).'%')).") ";
			
			$where .= " AND (er.receipt_name LIKE ".$db->quote(trim('%'.trim($search_str).'%'))."  OR ex.expense_name LIKE ".$db->quote(trim('%'.trim($search_str).'%'))." OR extr.trip_name LIKE ".$db->quote(trim('%'.trim($search_str).'%')) ." OR exca.expense_category_name LIKE ".$db->quote(trim('%'.trim($search_str).'%')) ." OR c.currencycode LIKE ".$db->quote(trim('%'.trim($search_str).'%')). ") ";
		}
		if($start_date=='' && $end_date=='')
		{
			$start_date = date('01-m-Y');
			$end_date = date('d-m-Y');
		}
		if($start_date!='' && $end_date!='')
		{
			$start_date = date('Y-m-d',strtotime($start_date));
			$end_date = date('Y-m-d',strtotime($end_date));
			$where .= " AND (date(er.createddate) >= '".$start_date."' AND date(er.createddate) <= '".$end_date."') ";
		}
		

		$receiptsData = $this->select()
		->setIntegrityCheck(false)
		->from(array('er' => 'expense_receipts'))
		->joinLeft(array('ex'=>'expenses'), "ex.id = er.expense_id and ex.isactive=1",array('expense_name'=>'ex.expense_name','expense_amount'=>'ex.expense_amount','status'=>'ex.status','exp_trip_id'=>'ex.trip_id'))
		->joinLeft(array('c'=>'main_currency'), "c.id = ex.expense_currency_id and ex.isactive=1",array('currencycode'=>'c.currencycode'))
		->joinLeft(array('extr'=>'expense_trips'), "extr.id = ex.trip_id and extr.isactive=1",array('trip_name'=>'extr.trip_name'))
		->joinLeft(array('exca'=>'expense_categories'), "exca.id = ex.category_id and ex.isactive=1",array('expense_category_name'=>'exca.expense_category_name'))
		->where($where);
		$receiptCount = $this->fetchAll($receiptsData)->toArray();
		return count($receiptCount);
	}
	public function deleteReceipt($data, $where)
	{
		if($where != ''){
			$this->update($data, $where);
		}
	}
	public function saveReceipts($data,$where)
	{
		if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('expense_receipts');
			return $id;
		}
	}
	public function getunreportedReceipts($receipt_ids='')
	{
		$receipts_where = '';
		if($receipt_ids!='')
		{
			$receipts_where = ' and id NOT IN('.$receipt_ids.')';
		}
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$where = "isactive = 1 AND trip_id IS NULL AND (expense_id IS NULL OR expense_id=0) and createdby = ".$loginUserId.''.$receipts_where;
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$receiptsData = $this->select()
		->setIntegrityCheck(false)
		->where($where)
		->order("id DESC");
		
		return $this->fetchAll($receiptsData)->toArray();
	}
	public function updateReceiptsData($data, $where){
		
		$this->update($data, $where);
		return 'update';
	}
	public function getReceiptData($receipt_ids)
	{
		$where = "isactive = 1 AND trip_id IS NULL AND (expense_id IS NULL OR expense_id=0) AND id IN (".$receipt_ids.") ";
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$receiptsData = $this->select()
		->setIntegrityCheck(false)
		->where($where)
		->order("id DESC");
		
		
		return $this->fetchAll($receiptsData)->toArray();
	}
	public function getExpenseReceipts($expenseId)
	{
		$where = "isactive = 1 AND expense_id=".$expenseId;
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$receiptsData = $this->select()
		->setIntegrityCheck(false)
		->where($where)
		->order("id DESC");
		
		return $this->fetchAll($receiptsData)->toArray();
	}
	
}