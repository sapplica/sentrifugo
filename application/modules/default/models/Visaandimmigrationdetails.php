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

class Default_Model_Visaandimmigrationdetails extends Zend_Db_Table_Abstract
{	
    protected $_name = 'main_empvisadetails';
    protected $_primary = 'id';
	
       
    public function getvisadetails($sort, $by, $pageNo, $perPage,$searchQuery)
	{
        $where = "v.isactive = 1";
		if($searchQuery)
			$where .= " AND ".$searchQuery;		
		$creditcarddata = $this->select()
						->from(array('v'=>'main_empvisadetails'))
						 ->where($where)
						  ->order("$by $sort") 
						  ->limitPage($pageNo, $perPage);
		return $creditcarddata;  
	}
	public function getvisadetailsRecord($id=0)
	{  
		$creditcardDetailsArr="";$where = "";
		$db = Zend_Db_Table::getDefaultAdapter();		
		if($id != 0)
		{
			$where = "user_id =".$id." AND isactive = 1";
			$creditcardDetailsData = $this->select()
									->from(array('v'=>'main_empvisadetails'))
									->where($where);
			$creditcardDetailsArr = $this->fetchAll($creditcardDetailsData)->toArray(); 
        }
		return $creditcardDetailsArr;       		
	}
    
    public function SaveorUpdatevisaandimmigrationDetails($data, $where)
    {
	    if($where != '')
        {
            $this->update($data, $where);
			return 'update';
        }
        else
        {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_empvisadetails');
			return $id;
		}
		
	
	}
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$exParam1='',$exParam2='',$exParam3='',$exParam4='')
	{
		$searchQuery = '';$tablecontent = '';
		$searchArray = array();$data = array(); $dataTmp = array();
		/** search from grid - START **/
		if($searchData != '' && $searchData!='undefined')
		{
			$searchValues = json_decode($searchData);
			foreach($searchValues as $key => $val)
			{
				if($key == 'visa_expiry_date')
				{
					$searchQuery .= " ".$key." like '%".  sapp_Global::change_date($val,'database')."%' AND ";
				}
				else
					$searchQuery .= " ".$key." like '%".$val."%' AND ";
	
				$searchArray[$key] = $val;
			}
			$searchQuery = rtrim($searchQuery," AND");
		}
		/** search from grid - END **/
			$objName = 'visaandimmigrationdetails';
	
	    	$tableFields = array('action'=>'Action','passport_number'=>'Passport Number','passport_expiry_date'=>'Passport Expiry Date','visa_number'=>'Visa Number','visa_expiry_date'=>'Visa Expiry Date','inine_status'=>'Inine Status','ininetyfour_status'=>'Ininetyfour Status');
						
			$tablecontent = $this->getEmpVisaDetails($sort,$by,$pageNo,$perPage,$searchQuery,$exParam1);
			$dataTmp = array('userid'=>$exParam1,
					'sort' => $sort,
					'by' => $by,
					'pageNo' => $pageNo,
					'perPage' => $perPage,
					'tablecontent' => $tablecontent,
					'objectname' => $objName,
					'extra' => array(),
					'tableheader' => $tableFields,
					'jsGridFnName' => 'getEmployeeAjaxgridData',
					'jsFillFnName' => '',
					'searchArray' => $searchArray,
					'add'=>'add',
					'menuName'=>'Visa and Immigration',
					'formgrid'=>'true',
					'unitId'=>$exParam1,
					'dashboardcall'=>$dashboardcall,
					'call'=>$call,
					'context'=>$exParam2,
					'search_filters' => array(
					 	'passport_expiry_date'=>array('type'=>'datepicker'),
					  	'visa_expiry_date'=>array('type'=>'datepicker')	
					)
		);
	     return $dataTmp;
	}
	public function getEmpVisaDetails($sort, $by, $pageNo, $perPage,$searchQuery,$id)
	{
		$where = " e.user_id = ".$id." AND e.isactive = 1 ";
	
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$empvisaData = $this->select()
		->setIntegrityCheck(false)
		->from(array('e' => 'main_empvisadetails'),array('id'=>'e.id','passport_number'=>'e.passport_number','passport_expiry_date'=>'DATE_FORMAT(e.passport_expiry_date,"'.DATEFORMAT_MYSQL.'")','visa_number'=>'e.visa_number','visa_expiry_date'=>'DATE_FORMAT(e.visa_expiry_date,"'.DATEFORMAT_MYSQL.'")','inine_status'=>'e.inine_status','ininetyfour_status'=>'e.ininetyfour_status'))
		->where($where)
		->order("$by $sort")
		->limitPage($pageNo, $perPage);
		return $empvisaData;
	}
	public function getsinglevisadetailsRecord($id)
	{
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('es'=>'main_empvisadetails'),array('es.*'))
		->where('es.id='.$id.' AND es.isactive = 1');
			
		return $this->fetchAll($select)->toArray();
	}
	
}