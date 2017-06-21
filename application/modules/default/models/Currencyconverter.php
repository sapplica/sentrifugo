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

class Default_Model_Currencyconverter extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_currencyconverter';
    protected $_primary = 'id';
	
	public function getCurrencyConverterData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "isactive = 1";
		
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$currencyConverterData = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('cc'=>'main_currencyconverter'),array('cc.*','start_date'=>'DATE_FORMAT(start_date,"'.DATEFORMAT_MYSQL.'")','end_date'=>'DATE_FORMAT(end_date,"'.DATEFORMAT_MYSQL.'")'))
						  
                           ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		 		
		return $currencyConverterData;       		
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
                                    if($key == 'start_date' || $key == 'end_date')
                                        $searchQuery .= " ".$key." like '%".  sapp_Global::change_date($val,'database')."%' AND ";
                                    else
					$searchQuery .= " ".$key." like '%".$val."%' AND ";
                                    $searchArray[$key] = $val;
				}
				$searchQuery = rtrim($searchQuery," AND");					
			}
			
		$objName = 'currencyconverter';
		
		$tableFields = array('action'=>'Action','basecurrtext' => 'Base Currency',
                    'targetcurrtext' => 'Target Currency','exchangerate' => 'Exchange Rate',
                    'start_date' => 'Active Start Date','end_date' => 'Active End Date',
                    'description' => 'Description');
		
		$tablecontent = $this->getCurrencyConverterData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
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
			'add' =>'add',
			'call'=>$call,
			'dashboardcall'=>$dashboardcall,
                        'search_filters' => array(
                            'start_date' => array('type'=>'datepicker'),
                            'end_date' => array('type'=>'datepicker'),
                        ),
		);
		return $dataTmp;
	}
	public function getsingleCurrencyConverterData($id)
	{
		$row = $this->fetchRow("id = '".$id."'");
		if (!$row) {
			throw new Exception("Could not find row $id");
		}
		return $row->toArray();
	}
	
	public function getCurrencyConverterDatabyID($id)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('cc'=>'main_currencyconverter'),array('cc.*'))
					    ->where('cc.isactive = 1 AND cc.id='.$id.' ');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function SaveorUpdateCurrencyConverterData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_currencyconverter');
			return $id;
		}
		
	
	}
    /**
     * This function helps to get currency names of base and target currencies.
     * @param integer $base_id    = id of base currency
     * @param integer $target_id  = id of target currency
     * @return array Array of ids and names of currencies.
     */    
    public function getCurrencyNames($base_id,$target_id)
    {
        $names = array();
        if($base_id != '' && $target_id != '')
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select id,currencyname from main_currency where id in (".$base_id.",".$target_id.")";
            $result = $db->query($query);
            
            while($row = $result->fetch())
            {
                $names[$row['id']] = $row['currencyname'];
            }
            
        }
        return $names;
    }
}