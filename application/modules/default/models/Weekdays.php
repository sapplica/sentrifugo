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

class Default_Model_Weekdays extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_weekdays';
    protected $_primary = 'id';
	
	public function getWeekDaysData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "w.isactive = 1 AND wk.isactive=1 ";
		
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$weekDaysData = $this->select()
    					   ->setIntegrityCheck(false)
						    ->from(array('w'=>'main_weekdays'),array( 'w.*'))
                            ->joinLeft(array('wk'=>'tbl_weeks'), 'wk.week_id=w.day_name',array('week_name'=>'wk.week_name'))							   
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
		$objName = 'weekdays';
		
		$tableFields = array('action'=>'Action','week_name' => 'Day','dayshortcode' => 'Short Code','daylongcode' => 'Long Code','description' => 'Description');
		$tablecontent = $this->getWeekDaysData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
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
	
	public function getsingleWeekdayData($id)
	{
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$weekDaysData = $db->query("SELECT * FROM main_weekdays WHERE id = ".$id." AND isactive=1");
		$res = $weekDaysData->fetchAll();
		if (isset($res) && !empty($res)) 
		{	
			return $res;
		}
		else
			return 'norows';
	}
	
	public function getWeekdayDataByID($id)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('w'=>'main_weekdays'),array('w.*'))
					    ->where('w.isactive = 1 AND w.id='.$id.' ');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function SaveorUpdateWeekdaysdataData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_weekdays');
			return $id;
		}
			
	}
	
	public function getWeeklist()
	{
	  $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('w'=>'main_weekdays'),array('w.id','w.day_name','w.daylongcode'))
					    ->where('w.isactive = 1')
						
                                                ;
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function getWeeklistData()
	{
	  $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('w'=>'main_weekdays'),array('w.id','day_id'=>'w.day_name','w.daylongcode'))
						->joinLeft(array('wk'=>'tbl_weeks'), 'wk.week_id=w.day_name',array('day_name'=>'wk.week_name'))							   
					    ->where('w.isactive = 1')
                        ->order('wk.id')
                  ;
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function getWeekEndDetails($weekendnumberstr)
	{
	    $db = Zend_Db_Table::getDefaultAdapter();
        
        $query = "select w.id,w.day_name from main_weekdays w where w.isactive = 1 and w.id IN(".$weekendnumberstr.")";
        $result = $db->query($query)->fetchAll();
	    return $result;
	
	}
	
	/* Queries from tbl_weeks
	    one query to get weekids which are not added to main_weekdays
		second query is to get total weekids from tbl_weeks
		third query is to get particular week id from tbl_weeks
		
		START
	*/
	
	public function getWeekdayslist($weekidstr='')
	{
	 if($weekidstr !='')
	  $params = explode(",",$weekidstr);
	  
	  $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('w'=>'tbl_weeks'),array('w.*'))
					    ->where('w.isactive = 1 AND week_id NOT IN(?)', $params)
		
                  ;
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function getCompleteWeekdayslist()
	{
	  
	  $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('w'=>'tbl_weeks'),array('w.*'))
					    ->where('w.isactive = 1 ')
		
                  ;
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function getParticularWeekDayName($weekid)
	{
	  
	  $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('w'=>'tbl_weeks'),array('w.*'))
					    ->where('w.isactive = 1 AND w.week_id ='.$weekid.' ');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function getcombinedweekname($id)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('w'=>'main_weekdays'),array('w.*'))
					    ->where('w.isactive = 1 AND w.id='.$id.' ');
		$weekdata = $this->fetchAll($select)->toArray();
		
		if(!empty($weekdata))
		{
		 $selectweekname = $this->select()
						->setIntegrityCheck(false)
						->from(array('w'=>'tbl_weeks'),array('w.*'))
					    ->where('w.isactive = 1 AND w.week_id ='.$weekdata[0]['day_name'].' ');
		
		 $weeknamearr = $this->fetchAll($selectweekname)->toArray();
		 return $weeknamearr;
		}
		
		
	
	}
	
	/* END  */
}