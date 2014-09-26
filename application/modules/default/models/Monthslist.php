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

class Default_Model_Monthslist extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_monthslist';
    protected $_primary = 'id';
	
	public function getMonthslistData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "m.isactive = 1";
		
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$weekDaysData = $this->select()
    					   ->setIntegrityCheck(false)
                           ->from(array('m'=>'main_monthslist'),array( 'm.*'))
                            ->joinLeft(array('mo'=>'tbl_months'), 'mo.monthid=m.month_id',array('month_name'=>'mo.month_name'))							   
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $weekDaysData;       		
	}
	
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$a='',$b='',$c='',$d='')
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
		$objName = 'monthslist';
		
		$tableFields = array('action'=>'Action','month_name' => 'Month Name','monthcode' => 'Month Code');
		$tablecontent = $this->getMonthslistData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
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
			'dashboardcall'=>$dashboardcall
		);	
		return $dataTmp;
	}
	
	
	public function getsingleMonthListData($id)
	{
		$row = $this->fetchRow("id = '".$id."'");
		if (!$row) {
			throw new Exception("Could not find row $id");
		}
		return $row->toArray();
	}
	
	public function getMonthNameDataByID($id)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('m'=>'main_monthslist'),array('m.*'))
					    ->where('m.isactive = 1 AND m.id='.$id.' ');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function SaveorUpdateMonthListData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_monthslist');
			return $id;
		}
		
	
	}
	
	public function getMonthsList()
	{
	  $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('m'=>'main_monthslist'),array('m.id','m.month_id','m.monthcode'))
					    ->where('m.isactive = 1');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function getMonthlistData()
	{
	  $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('m'=>'main_monthslist'),array('m.id','m.month_id','m.monthcode'))
						->joinLeft(array('mo'=>'tbl_months'), 'mo.monthid=m.month_id',array('mo.month_name'))							   
					    ->where('m.isactive = 1');
		
		return $this->fetchAll($select)->toArray();
	
	}
	
	/* Queries from tbl_months
	    one query to get monthids which are not added to main_monthlist
		second query is to get total monthids from tbl_months
		third query is to get particular month id from tbl_months
		
		START
	*/
	
	public function getMonthNamelist($monthidstr='')
	{
	 if($monthidstr !='')
	  $params = explode(",",$monthidstr);
	  
	  $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('m'=>'tbl_months'),array('m.*'))
					    ->where('m.isactive = 1 AND monthid NOT IN(?)', $params)
		
                  ;
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function getCompleteMonthNamelist()
	{
	  
	  $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('m'=>'tbl_months'),array('m.*'))
					    ->where('m.isactive = 1 ')
		
                  ;
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function getParticularMonthName($monthid)
	{
	  
	  $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('m'=>'tbl_months'),array('m.*'))
					    ->where('m.isactive = 1 AND m.monthid ='.$monthid.' ');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function getcombinedmonthname($id)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('m'=>'main_monthslist'),array('m.*'))
					    ->where('m.isactive = 1 AND m.id='.$id.' ');
		$monthdata =  $this->fetchAll($select)->toArray();
		
		if(!empty($monthdata))
		{
		 $selectmonthname = $this->select()
						->setIntegrityCheck(false)
						->from(array('m'=>'tbl_months'),array('m.*'))
					    ->where('m.isactive = 1 AND m.monthid ='.$monthdata[0]['month_id'].' ');
		 $monthname = $this->fetchAll($selectmonthname)->toArray();
		 return $monthname[0]['month_name'];
		}
		
		
	
	}
	
	
	/* END*/
	
	
}