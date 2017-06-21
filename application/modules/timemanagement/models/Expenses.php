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
class Timemanagement_Model_Expenses extends Zend_Db_Table_Abstract
{
	protected $_name = 'tm_emp_expenses';
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
	public function getExpensesData($sort, $by, $pageNo, $perPage,$searchQuery, $start_date, $end_date,$employee_id)
	{
		$andwhere = '(1=1)';
		if($start_date != "")
		{
			if($end_date == "")
			{
				$end_date = date('%m-%d-%Y %H:%i:%s');
			}
			$andwhere .= " AND ex.created BETWEEN STR_TO_DATE('".$start_date."','%d-%m-%Y %H:%i:%s') AND STR_TO_DATE('".$end_date."','%d-%m-%Y %H:%i:%s')";
		}	
			
		$andwhere .= " AND ex.is_active = 1 AND ex.emp_id = ".$employee_id;

		if($searchQuery)
		$andwhere .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();

		$expensesData = $this->select()
		->setIntegrityCheck(false)
		->from(array('ex'=>'tm_emp_expenses'),array('ex.id','expense_date'=>'date_format(ex.expense_date,"'.DATEFORMAT_MYSQL.'")','expense_amount'=> new Zend_Db_Expr("CONCAT(mc.currencycode, ' ', ex.expense_amount)"),'is_billable'=>'if (ex.is_billable=1,"Yes","No")','ex.expense_status','ex.receipt_file','ex.note'))
		->joinInner(array('ec'=>'tm_expense_categories'),'ec.id = ex.expense_cat_id',array('ec.expense_category'))
		->joinInner(array('c'=>'tm_clients'),'c.id = ex.client_id',array('c.client_name'))
		->joinInner(array('p'=>'tm_projects'),'p.id = ex.project_id',array('p.project_name'))
		->joinInner(array('mc'=>'main_currency'),'mc.id = p.currency_id',array('mc.currencycode'))
		->where($andwhere)
		->order("$by $sort")
		->limitPage($pageNo, $perPage);
		//echo $expensesData->__toString();die;
/*SELECT ex.expense_date,c.client_name,p.project_name,ec.expense_category,
mc.currencycode,ex.expense_amount,ex.is_billable,ex.expense_status,ex.receipt_file
FROM tm_emp_expenses ex
INNER JOIN tm_expense_categories ec ON ec.id = ex.expense_cat_id
INNER JOIN tm_clients c ON c.id = ex.client_id
INNER JOIN tm_projects p ON p.id = ex.project_id
INNER JOIN main_currency mc ON mc.id = p.currency_id
ORDER BY ex.created DESC;*/
		return $expensesData;
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
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall, $start_date, $end_date, $org_start_date,$org_end_date,$employee_id)
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
			
		$objName = 'expenses';

		$tableFields = array(
								'action'=>'Action'
								,'expense_date' => 'Date'
								,'client_name' => 'Client'
								,'project_name' => 'Project'
								,'expense_category' => 'Category'
								//,'currencycode' => 'Current'
								,'expense_amount' => 'Amount'
								,'is_billable' => 'Billable'
								,'expense_status' => 'Status'
								//,'receipt_file' => 'File'
								//,'note' => 'Notes'
								,'att' => 'Attachment'
					
		);

		$tablecontent = $this->getExpensesData($sort, $by, $pageNo, $perPage,$searchQuery, $start_date, $end_date,$employee_id);
		//echo"<pre>";print_r($tablecontent);die;
		$dataTmp = array(
			'sort' => $sort,
			'by' => $by,
			'pageNo' => $pageNo,
			'perPage' => $perPage,				
			'tablecontent' => $tablecontent,
			'objectname' => $objName,
		    'menuName' => 'Expenses',
			'otheraction' => 'expensereports',
			'extra' => array(),
			'tableheader' => $tableFields,
			'jsGridFnName' => 'getAjaxgridData',
			'jsFillFnName' => '',
			'searchArray' => $searchArray,
			'call'=>$call,
			'dashboardcall'=>$dashboardcall,
			'start_date' => $org_start_date,
			'end_date' => $org_end_date,
		);
		return $dataTmp;
	}

	public function SaveorUpdateExpensesData($data, $where)
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

	public function getExpensesDataById($id)
	{
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('ec'=>$this->_name),array('ec.*'))
		->where('ec.is_active = 1 AND ec.id='.$id.' ');
		return $this->fetchAll($select)->toArray();
	}

	public function checkExpenses($id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select count(*) as count from tm_user_expenses where category_id = ".$id." AND is_active = 1";
		$result = $db->query($query)->fetch();
		return $result['count'];
	}

	public function getViewExpensesGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall, $start_date, $end_date, $org_start_date,$org_end_date,$employee_id,$type)
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
			
		$objName = 'expenses';

		$tableFields = array(
								//'action'=>'Action',
								'expense_date' => 'Date'
								,'client_name' => 'Client'
								,'project_name' => 'Project'
								,'employee_name' => 'Employee'
								,'expense_category' => 'Category'
								//,'currencycode' => 'Current'
								,'expense_amount' => 'Amount'
								,'is_billable' => 'Billable'
								//,'expense_status' => 'Status'
								//,'receipt_file' => 'File'
								//,'note' => 'Notes'
								,'att' => 'Attachment'
								,'acc' => 'Action'
					
		);

		$tablecontent = $this->getViewExpensesData($sort, $by, $pageNo, $perPage,$searchQuery, $start_date, $end_date,$employee_id,$type);
		//echo"<pre>";print_r($tablecontent);die;
		$dataTmp = array(
			'sort' => $sort,
			'by' => $by,
			'pageNo' => $pageNo,
			'perPage' => $perPage,				
			'tablecontent' => $tablecontent,
			'objectname' => $objName,
		    'menuName' => 'view expenses',
			'otheraction' => 'viewexpensereports',
			'extra' => array(),
			'tableheader' => $tableFields,
			'jsGridFnName' => 'getAjaxgridData',
			'jsFillFnName' => '',
			'searchArray' => $searchArray,
			'call'=>$call,
			'dashboardcall'=>$dashboardcall,
			'start_date' => $org_start_date,
			'end_date' => $org_end_date,
		);
		return $dataTmp;
	}
	
	public function getViewExpensesData($sort, $by, $pageNo, $perPage,$searchQuery, $start_date, $end_date,$employee_id,$type)
		{
			$andwhere = '(1=1)';
			if($start_date != "")
			{
				if($end_date == "")
				{
					$end_date = date('%m-%d-%Y %H:%i:%s');
				}
				$andwhere .= " AND ex.created BETWEEN STR_TO_DATE('".$start_date."','%d-%m-%Y %H:%i:%s') AND STR_TO_DATE('".$end_date."','%d-%m-%Y %H:%i:%s')";
			}	
				
			$andwhere .= " AND ex.expense_status <> 'saved' AND ex.is_active = 1 AND mec.reporting_manager = ".$employee_id;
	
			if($type != 'all' && $type != '')
				$andwhere .= ' AND expense_status=\''.$type.'\'';
				
			if($searchQuery)
			$andwhere .= " AND ".$searchQuery;
			
			$db = Zend_Db_Table::getDefaultAdapter();
	
			$expensesData = $this->select()
			->setIntegrityCheck(false)
			->from(array('ex'=>'tm_emp_expenses'),array('ex.id','expense_date'=>'date_format(ex.expense_date,"'.DATEFORMAT_MYSQL.'")','expense_amount'=> new Zend_Db_Expr("CONCAT(mc.currencycode, ' ', ex.expense_amount)"),'is_billable'=>'if (ex.is_billable=1,"Yes","No")','ex.expense_status','ex.receipt_file','ex.note'))
			->joinInner(array('ec'=>'tm_expense_categories'),'ec.id = ex.expense_cat_id',array('ec.expense_category'))
			->joinInner(array('c'=>'tm_clients'),'c.id = ex.client_id',array('c.client_name'))
			->joinInner(array('p'=>'tm_projects'),'p.id = ex.project_id',array('p.project_name'))
			->joinInner(array('mc'=>'main_currency'),'mc.id = p.currency_id',array('mc.currencycode'))
			//->joinInner(array('mec'=>'main_employees_summary'),'mec.user_id = ex.emp_id',array())
			->joinInner(array('mec'=>'main_employees_summary'),'mec.user_id = ex.emp_id',array('employee_name'=> new Zend_Db_Expr("CONCAT(mec.firstname, ' ', mec.lastname)")))
			->where($andwhere)
			->order("$by $sort")
			->limitPage($pageNo, $perPage);
			//echo $expensesData;exit;
			return $expensesData;
			
			/** **/
			$search=urldecode($search);
			$stat_arr=array('enabled'=>'Enabled','blocked'=>'Blocked','rejected'=>'Rejected','submitted'=>'For Approval','approved'=>'Approved','saved'=>'Saved');
			$fin_arr=array();
			$i=0;
			$where = " WHERE (ms.isactive = 1 AND pe.is_active=1 ) ";
			$per_page = 8;
			$current_index = ($current_page - 1) * $per_page;
			$db = Zend_Db_Table::getDefaultAdapter();
			
			$duration = "IF(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.week_duration as time))))is null,'00:00',
							time_format(SEC_TO_TIME(sum(TIME_TO_SEC(cast(et.week_duration as time)))),'%H:%i'))";
	
			$time_status = "IF(FIND_IN_SET('submitted',GROUP_CONCAT(DISTINCT week_status))!=0,'For Approval',
							IF(FIND_IN_SET('blocked',GROUP_CONCAT(DISTINCT week_status))!=0,'Blocked',
							IF(FIND_IN_SET('rejected',GROUP_CONCAT(DISTINCT week_status))!=0,'Rejected',
							IF(FIND_IN_SET('approved',GROUP_CONCAT(DISTINCT week_status))!=0,'Approved',
							IF(FIND_IN_SET('saved',GROUP_CONCAT(DISTINCT week_status))!=0,'Saved',
							IF(FIND_IN_SET('enabled',GROUP_CONCAT(DISTINCT week_status))!=0,'Enabled','No Entry'))))))";
	
			$selectEmpTimesheetsQuery = "SELECT GROUP_CONCAT( distinct pe.project_id) proj_ids, ms.user_id AS empid, ms.userfullname AS empname,
										".$duration." AS duration, 
										".$time_status." AS time_status,et.ts_week,et.ts_year,et.ts_month
										 FROM tm_project_employees AS pe 
										 INNER JOIN tm_projects p ON p.id = pe.project_id AND project_status  != 'draft'
	
										 INNER JOIN  main_employees_summary AS ms ON ms.user_id = pe.emp_id";
			
			if($week != ""){
				$selectEmpTimesheetsQuery.=" LEFT JOIN tm_emp_timesheets AS et ON et.emp_id = pe.emp_id and et.project_id = pe.project_id and et.project_id IS NOT NULL and et.ts_year =".$year." and et.ts_month = ".$month." and et.ts_week =".$week;
			}else{
				$selectEmpTimesheetsQuery.=" LEFT JOIN tm_emp_timesheets AS et ON et.emp_id = pe.emp_id and et.project_id = pe.project_id and et.project_id IS NOT NULL and et.ts_year =".$year." and et.ts_month = ".$month;
			}
			
			$selectEmpTimesheetsQuery.=" LEFT JOIN ".$this->_ts_status." AS ts ON ts.emp_id = et.emp_id and ts.project_id = et.project_id and et.ts_year = ts.ts_year
										and et.ts_month = ts.ts_month and ts.ts_week = et.ts_week";
			
			if(trim($search) != ""){
				$where .= " AND (ms.userfullname LIKE ".$db->quote(trim('%'.trim($search).'%'))." ) ";
			}
			
			$where.= " and ms.user_id  != ".$manager_id;
			
			$where.= " and  pe.project_id in (select project_id from  tm_project_employees where emp_id = ".$manager_id.")";
			
			$selectEmpTimesheetsQuery.=$where;
			$selectEmpTimesheetsQuery.=" GROUP BY ms.user_id";
	
			if($clicked_status != "all"){
				$selectEmpTimesheetsQuery.=" HAVING time_status=\"".$stat_arr[$clicked_status]."\" ";
			}
			$selectEmpTimesheetsQuery .= " LIMIT ".$current_index.",".$per_page;
			//echo $selectEmpTimesheetsQuery;exit;
			$res = $db->query($selectEmpTimesheetsQuery);
			/** **/
	}	
}
