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

class Expenses_Model_Advances extends Zend_Db_Table_Abstract
{
	/**
	 * The advance table name
	 */
    protected $_name = 'expense_advance';
    protected $_primary = 'id';
	
	
	public function SaveAdvanceData($data, $where)
	{
		
		if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			
			$this->insert($data);
			 return 1;
		}
	}
	
	public function getMyAdvanceData($emp_id)
	{
		//$emp_id = 9;
		$select =  $this->select()
						->setIntegrityCheck(false)
						->from(array('c'=>'expense_advacne_summary'),array('c.id','c.total','c.utilized','c.returned','c.balance','c.employee_id'))						
						->where('c.isactive = 1 AND c.employee_id='.$emp_id);
		return $this->fetchAll($select)->toArray();
		
		

	}
	public function getMyAdvances($limit,$offset,$emp_id,$search_str='')
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$where = "adv.isactive = 1";
		if($search_str!='')
		{
			
			  $where .= " AND (project_name LIKE ".$db->quote(trim('%'.trim($search_str).'%'))." OR currencyname LIKE ".$db->quote(trim('%'.trim($search_str).'%'))." OR currencycode LIKE ".$db->quote(trim('%'.trim($search_str).'%'))." OR amount LIKE" .$db->quote(trim('%'.trim($search_str).'%'))." OR DATE_FORMAT(adv.createddate,'%d-%m-%Y')  LIKE ".$db->quote(trim('%'.trim($search_str).'%'))." OR userfullname LIKE ".$db->quote(trim('%'.trim($search_str).'%'))." ) "; 
		
		}
		
		
		//$emp_id=9;
	  $myadvance = $this->select()
		->setIntegrityCheck(false)
		->from(array('adv' => 'expense_advance'),array('amount'=>'adv.amount','createddate'=>'DATE_FORMAT(adv.createddate,"'.DATEFORMAT_MYSQL.'")'))
	    ->joinLeft(array('tp'=>'tm_projects'), "tp.id = adv.project_id and
	    adv.project_id IS NOT NULL")
		->joinInner(array('c'=>'main_currency'), "c.id = adv.currency_id",array('currencycode'=>'c.currencycode'))
		->joinInner(array('mc'=>'main_users'), "mc.id = adv.createdby",array('userfullname'=>'mc.userfullname'))
		->where($where.' and adv.isactive = 1 and adv.type = "advance" and adv.to_id = '.$emp_id)
		->order("adv.id DESC")
		->limit($limit,$offset); 
		return $this->fetchAll($myadvance)->toArray();
		
	}
	
	
	public function getAdvancesCount($emp_id,$search_str='')
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$where = "adv.isactive = 1";
		
		if($search_str!='')
		{
		 $where .= " AND (project_name LIKE ".$db->quote(trim('%'.trim($search_str).'%'))." OR currencyname LIKE ".$db->quote(trim('%'.trim($search_str).'%'))." OR currencycode LIKE ".$db->quote(trim('%'.trim($search_str).'%'))." OR amount LIKE" .$db->quote(trim('%'.trim($search_str).'%'))." OR DATE_FORMAT(adv.createddate,'%d-%m-%Y')  LIKE ".$db->quote(trim('%'.trim($search_str).'%'))." OR userfullname LIKE ".$db->quote(trim('%'.trim($search_str).'%'))." ) "; 
		}
		
		
		//$emp_id=9;
		 $myadvance =$this->select()
		->setIntegrityCheck(false)
		->from(array('adv' => 'expense_advance'),array('createddate'=>'DATE_FORMAT(adv.createddate,"'.DATEFORMAT_MYSQL.'")'))
	    ->joinLeft(array('tp'=>'tm_projects'), "tp.id = adv.project_id and
	    adv.project_id IS NOT NULL")
		->joinInner(array('c'=>'main_currency'), "c.id = adv.currency_id",array('currencycode'=>'c.currencycode'))
		->joinInner(array('mc'=>'main_users'), "mc.id = adv.createdby",array('userfullname'=>'mc.userfullname'))
		->where($where.' and adv.isactive = 1 and adv.type = "advance" and  adv.to_id = '.$emp_id);
		$advance_count = $this->fetchAll($myadvance)->toArray();
		return count($advance_count);
		
	}
	// function to get employee to add advances
	public function getUserList($loginUserId='')
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
		 	$login_UserId = $auth->getStorage()->read()->id;
			$loginUserGroup = $auth->getStorage()->read()->group_id;
			$loginUserRole = $auth->getStorage()->read()->emprole;
		}
		if($loginUserId=='')
		{
			$loginUserId = $login_UserId;
		}	
		//$loginUserId=3; 
		if($loginUserGroup == HR_GROUP)
			$where = "  e.isactive = 1 AND e.user_id != ".$loginUserId." ";
		else	
			$where = "  e.isactive = 1 AND e.reporting_manager = ".$loginUserId." ";
       
        $employeesData = $this->select()
                                ->setIntegrityCheck(false)	                                
                                ->from(array('e' => 'main_employees_summary'),
                                        array('*','id'=>'e.user_id','cnt'=>'(select count(*) from main_logmanager where is_active =1 and last_modifiedby = e.user_id)' ))                               
                                ->where($where);
		$username_arr = array();
		$employeesDataArray = $this->fetchAll($employeesData)->toArray();
		
        return $employeesDataArray;  

		
	}
	public function getAdvanceUserList($emp_id=0)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
		 	$login_UserId = $auth->getStorage()->read()->id;
		}
		if($emp_id==0)
			$emp_id = $login_UserId;
		
		$select =  $this->select()
						->setIntegrityCheck(false)
						->from(array('ea'=>'expense_advance'))	
						->joinInner(array('mu'=>'main_users'), "mu.id = ea.from_id",array('userfullname'=>'mu.userfullname'))
						->where('ea.isactive = 1 AND mu.isactive = 1 AND ea.type = "advance" AND ea.to_id='.$emp_id)
						->group('ea.from_id');
		return $this->fetchAll($select)->toArray();
	}
	
}

	
	
	
	
