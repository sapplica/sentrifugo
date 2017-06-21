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

require_once 'Zend/Db/Table/Abstract.php';
class Default_Model_Currency extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_currency';
    protected $_primary = 'id';
	
	public function getCurrencyData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "isactive = 1";
		
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$CurrencyData = $this->select()
    					   ->setIntegrityCheck(false)	    					
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $CurrencyData;       		
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
			
		$objName = 'currency';
		
		$tableFields = array('action'=>'Action','currencyname' => 'Currency','currencycode' => 'Currency Code','description' => 'Description');
		
		$tablecontent = $this->getCurrencyData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
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
	
	
	public function getsingleCurrencyData($id)
	{
		$row = $this->fetchRow("id = '".$id."'");
		if (!$row) {
			throw new Exception("Could not find row $id");
		}
		return $row->toArray();
	}
	
	public function getCurrencyDataByID($id)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('c'=>'main_currency'),array('c.*'))
					    ->where('c.isactive = 1 AND c.id='.$id.' ');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function SaveorUpdateCurrencyData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_currency');
			return $id;
		}
		
	
	}
	
	public function getCurrencyList()
	{
	  $geographygroupData = $this->select()
                                    ->setIntegrityCheck(false)	
                                    
                                    ->from(array('c'=>'main_currency'),array('c.id','currency'=>'c.currencyname','currencytext'=>'concat(c.currencyname," (",c.currencycode,")")'))
                                     ->where('c.isactive = 1')
						   ->order('c.currencyname');
      return $this->fetchAll($geographygroupData)->toArray();
	}
	
	public function getTargetCurrencyList($basecurr_id)
	{
	  $db = Zend_Db_Table::getDefaultAdapter();
		
                
                $rows = $db->query("select c.id,c.currencyname as targetcurr 
                    from main_currency c WHERE c.id NOT IN (".$basecurr_id.") and c.isactive=1  
                        order by c.currencyname");
		
		return $rows->fetchAll();
	
	}
	
	public function getCurrencyName($correncystring)
	{
	 $db = Zend_Db_Table::getDefaultAdapter();
		
		$rows = $db->query("select c.id,concat(c.currencyname,' ',c.currencycode) as targetcurr from main_currency c WHERE c.id IN (".$correncystring.") and c.isactive=1");
		
		return $rows->fetchAll();
	
	}
}