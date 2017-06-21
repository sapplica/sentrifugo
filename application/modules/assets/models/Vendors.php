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

class Assets_Model_Vendors extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_vendors';
    protected $_primary = 'id';
	
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
				$searchAlias = ' ';
				if($key == 'city')
					$searchAlias = " c.";
				if($key == 'state')
					$searchAlias = " s.";
				if($key == 'country')
					$searchAlias = " cn.";
				if($key == 'timezone')
					$searchAlias = " tz.";
				if($key == 'startdate')
				{
					$searchQuery .= " ".$key." like '%".  sapp_Global::change_date(urldecode($val),'database')."%' AND ";
				}
				else
				$searchQuery .= $searchAlias.$key." like '%".urldecode($val)."%' AND ";
				$searchArray[$key] = urldecode($val);
			}
			$searchQuery = rtrim($searchQuery," AND");					
		}
		$objName = 'vendors';
		$tableFields = array('action'=>'Action','name' =>'Name','contact_person' => 'Contact Person','country_name' => 'Country','state_name' => 'State','city_name' => 'City','primary_phone'=>'Primary Phone');
		
		
		
		$tablecontent = $this->getVendorsData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
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
			'search_filters' => array(
					'startdate' =>array('type'=>'datepicker')					
				)
				);			
		return $dataTmp;
	}
	public function getVendorsData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		
		$where = " b.isactive = 1 AND b.id <> 0 ";
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$vendorsdata =  $this->select()
    					   ->setIntegrityCheck(false)	 
						   ->from(array('b' => 'main_vendors'),array('id'=>'b.id','isactive'=>'b.isactive','name'=>'b.name','contact_person'=>'b.contact_person','primary_phone'=>'b.primary_phone'))
						   
						  ->joinLeft(array('c'=>'tbl_countries'), 'c.id=b.country',array('country_name'=>'c.country_name'))
						   ->joinLeft(array('s'=>'tbl_states'), 's.id=b.state',array('state_name'=>'s.state_name'))
						   ->joinLeft(array('u'=>'tbl_cities'), 'u.id=b.city',array('city_name'=>'u.city_name'))
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $vendorsdata;       		
	}
	public function getsingleVendorsData($id)
	{
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select * from main_vendors where id = ".$id." AND isactive = 1";
		$result = $db->query($query)->fetch();
		return $result;
		
	}
        
	
	public function SaveorUpdateVendors($data, $where)
	{
		
		if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_vendors');
			return $id;
		} 
	}
	public function getVendorsList()
	{
		 $select = $this->select()
		->setIntegrityCheck(false)
		->from(array('c'=>'main_vendors'),array('c.*'))
		->where('c.isactive = 1');
		return $this->fetchAll($select)->toArray();
	}
	
	
	}
?>