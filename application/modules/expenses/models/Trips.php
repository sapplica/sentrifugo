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
class Expenses_Model_Trips extends Zend_Db_Table_Abstract
{
	
	protected $_name = 'expense_trips';
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
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$a='',$b='',$c='',$d='')
	{
		$searchQuery = '';
		$searchArray = array();
		$data = array();

		  if($searchData != '' && $searchData!='undefined')
        {
            $searchValues = json_decode($searchData);
            if(count($searchValues) >0)
            {
                foreach($searchValues as $key => $val)
                {    
                    if($key == 'from_date' ||  $key == 'to_date')                    
                        $searchQuery .= " date(".$key.") = '".  sapp_Global::change_date($val,'database')."' AND ";					
                    else
                        $searchQuery .= " ".$key." like '%".$val."%' AND ";
                    $searchArray[$key] = $val;
                }
                $searchQuery = rtrim($searchQuery," AND");
            }
        }
		$objName = 'trips';
		
		$tableFields = array(
					'action'=>'Action',
					'trip_name' => 'Trip',
					'from_date' => 'From date',
					'to_date' => 'To date',
					'description' => 'Description',
		);

		$tablecontent = $this->getTripsData($sort, $by, $pageNo, $perPage,$searchQuery);
	
		$dataTmp = array(
			'sort' => $sort,
			'by' => $by,
			'pageNo' => $pageNo,
			'perPage' => $perPage,				
			'tablecontent' => $tablecontent,
			'objectname' => $objName,
			'extra' => array(),
			'tableheader' => $tableFields,
			'jsGridFnName' => 'getAjaxgridData',
			'jsFillFnName' => '',
			'searchArray' => $searchArray,
			'call'=>$call,
			'dashboardcall'=>$dashboardcall,
			'menuName' => 'Trips',
				 'search_filters' => array(
                            
                            'from_date'=>array('type'=>'datepicker'),
							  'to_date'=>array('type'=>'datepicker'),
                            
                        ),
			);
			
			return $dataTmp;
	}

	/**
	 * This will fetch all the active client details.
	 *
	 * @param string $sort
	 * @param string $by
	 * @param number $pageNo
	 * @param number $perPage
	 * @param string $searchQuery
	 *
	 * @return array $tripsData
	 */
	public function getTripsData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
           $loginUserId = $auth->getStorage()->read()->id;
        }
		$where = "t.isactive = 1 and t.createdby = ".$loginUserId;

		if($searchQuery)
		$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();

		/* $tripsData = $this->select()
		->setIntegrityCheck(false)
		->where($where)
		->order("$by $sort")
		->limitPage($pageNo, $perPage);
		//echo "<pre>";print_r($tripsData);exit;
		return $tripsData; */
		
		
		$tripsData = $this->select()
		->setIntegrityCheck(false)
		 ->from(array('t'=>'expense_trips'),array('t.*','from_date'=>'DATE_FORMAT(from_date,"'.DATEFORMAT_MYSQL.'")','to_date'=>'DATE_FORMAT(to_date,"'.DATEFORMAT_MYSQL.'")'))
		->where($where)
		->order("$by $sort")
		->limitPage($pageNo, $perPage);
		return $tripsData;
	}

	/**
	 * This method will save or update the client details based on the client id.
	 *
	 * @param array $data
	 * @param string $where
	 */
	public function saveOrUpdateTripsData($data, $where){
		
		
		if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId($this->_name);
			return $id;
		}
	}
	
	/**
	 * This method is used to fetch client details based on id.
	 * 
	 * @param number $id
	 */
	public function getTripDetailsById($id)
	{
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('c'=>$this->_name),array('c.*'))						
						->where('c.isactive = 1 AND c.id='.$id.' ');
						
		return $this->fetchAll($select)->toArray();
		 
		 
	}
	public function getSingleTripDetailsById($id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$where = "et.isactive = 1";
		
		$select =
		$this->select()
		->setIntegrityCheck(false)
		->from(array('et' => 'expense_trips'))
	    ->joinLeft(array('ex'=>'expenses'), "ex.trip_id = et.id and ex.isactive = 1",array('expense_date'=>'ex.expense_date','expense_conversion_rate'=>'ex.expense_conversion_rate','expense_id'=>'ex.id','is_reimbursable'=>'ex.is_reimbursable','expense_amount'=>'ex.expense_amount','application_amount'=>'ex.application_amount','status'=>'ex.status as status_expense','receipt_count'=>'(select count(*) from expense_receipts er where er.isactive=1 and er.expense_id = ex.id )'))
		->joinInner(array('mc'=>'main_currency'), "mc.id = ex.expense_currency_id " ,array('currencycode'=>'mc.currencycode'))
		->joinInner(array('ec'=>'expense_categories'), "ec.id = ex.category_id",array('expense_category_name'=>'ec.expense_category_name'))
		->joinLeft(array('tp'=>'tm_projects'), "tp.id = ex.project_id",array('project_name'=>'tp.project_name'))
		->where('ex.isactive = 1 AND et.id='.$id.' ') 
		->order("ex.id DESC"); 
		return $this->fetchAll($select)->toArray();
	}

	public function getsingleTripData($id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$where = "et.isactive = 1";
		
		echo $select =
		$this->select()
		->setIntegrityCheck(false)
		->from(array('et' => 'expense_trips'))
		->where('et.isactive = 1 AND et.id='.$id.' ') ;
		return $this->fetchAll($select)->toArray();
	}

	
	/**
	 * This method returns all active clients to show in projects screen 
	 *
	 * @return array 
	 */
	public function getActiveClientsData()
	{
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('c'=>$this->_name),array('c.id','c.client_name'))
		->where('c.is_active = 1 ')
		->order('c.client_name');
		return $this->fetchAll($select)->toArray();
	}
	
	/**
	 * This method is used to check weather the client is associated in any project or not.
	 * 
	 * @param unknown_type $clientId
	 */
	public function checkExpensesAndTrips($tripId){
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select count(*) as count from expenses where trip_id = ".$tripId." AND isactive = 1";
		$result = $db->query($query)->fetch();
		return $result['count'];
		
	} 
	
	
	/*Get trips list*/
	
    public function getTripsList()
	{
		
		$auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
           $loginUserId = $auth->getStorage()->read()->id;
        }
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('p'=>$this->_name),array('p.id','trip_name'))
		->where('p.isactive = 1 and p.status!="A" and p.status!="S" and p.createdby='.$loginUserId)
		->order('p.trip_name');
		return $this->fetchAll($select)->toArray();
	}


	public function getTrips($limit,$offset,$trip_id=0) 
	{
		$auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
           $loginUserId = $auth->getStorage()->read()->id;
        }
		$db = Zend_Db_Table::getDefaultAdapter();
		$trip_cond = '';
		if($trip_id>0)
			$trip_cond = ' and id!='.$trip_id;
		$where = 'tr.isactive=1 and tr.status!="A"  and tr.status!="S" '.$trip_cond.' AND tr.createdby = '.$loginUserId;
		
		 $tripsData = $this->select()
		->setIntegrityCheck(false)
		->from(array('tr' => 'expense_trips'),array('tr.id','tr.trip_name','tr.status'))
		->where($where)
		->limit($limit,$offset)
		;
		return $this->fetchAll($tripsData)->toArray();
	}
	public function getTripsCount()
	{
		$auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
           $loginUserId = $auth->getStorage()->read()->id;
        }
		$db = Zend_Db_Table::getDefaultAdapter();
		$count_query = "select count(id) cnt from expense_trips e where e.isactive = 1 and e.status!='A'  and e.status!='S' and e.createdby = ".$loginUserId;
		$count_result = $db->query($count_query);
		$count_row = $count_result->fetch();
		return $count_row['cnt'];  
	}
	
	public function getReportingManagerAction($loginUserId)
	{
	
		$db = Zend_Db_Table::getDefaultAdapter();
		
		//$loginUserId=3;
		$select =  $this->select()
						->setIntegrityCheck(false)
						->from(array('c'=>'main_employees_summary'),array('c.reporting_manager_name'))						
						->where('c.user_id='.$loginUserId);
		return $this->fetchAll($select)->toArray();
		
		
	}
	public function getTripExpenses($trip_id)
	{
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('e'=>'expenses'),array('e.status'))
		->where('e.isactive = 1 and e.trip_id = '.$trip_id);
		return $this->fetchAll($select)->toArray();
	}
	
	public function getApplicationCurrency()
	{
	    $select =  $this->select()
                            ->setIntegrityCheck(false)	
                            ->from(array('s'=>'main_sitepreference'),array('s.*'))
                            ->joinLeft(array('c'=>'main_currency'), 'c.id=s.currencyid AND c.isactive=1',array('currencycode'=>'c.currencycode'))						   
                            ->where('s.isactive = 1');
    	  
		return $this->fetchAll($select)->toArray();   
	
	}
}

