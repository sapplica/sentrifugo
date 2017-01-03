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

class Default_Model_Workeligibilitydoctypes extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_workeligibilitydoctypes';
    protected $_primary = 'id';
	
	public function getWorkEligibilityDocTypesData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "isactive = 1";
		
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$workEligibilityDocTypesData = $this->select()
    					   ->setIntegrityCheck(false)
                            ->from(array('w'=>'main_workeligibilitydoctypes'),
						          array( 'w.*',
										 'issuingauthority' => new Zend_Db_Expr("CASE w.issuingauthority WHEN 1 THEN 'Country' WHEN 2 THEN 'State' ELSE 'City' END"),										 
								       ))						   
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $workEligibilityDocTypesData;       		
	}
	public function getsingleWorkEligibilityDocTypeData($id)
	{
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$workeligibilityDoctypesData = $db->query("SELECT * FROM main_workeligibilitydoctypes WHERE id = ".$id." AND isactive=1");
		$res = $workeligibilityDoctypesData->fetchAll();
		if (isset($res) && !empty($res)) 
		{	
			return $res;
		}
		else
			return 'norows';
		
	}
	
	public function SaveorUpdateWorkEligibilityDocumentData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_workeligibilitydoctypes');
			return $id;
		}
	}
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$exParam1='',$exParam2='',$exParam3='',$exParam4='')
	{		
        $searchQuery = '';$tablecontent = '';  $searchArray = array();$data = array();$id='';
        $dataTmp = array();
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

		/** search from grid - END **/
		$objName = 'workeligibilitydoctypes';
		
		$tableFields = array('action'=>'Action','documenttype' => 'Document Type','issuingauthority' => 'Issuing Authority','description' => 'Description');
		
			
		$tablecontent = $this->getWorkEligibilityDocTypesData($sort, $by, $pageNo, $perPage,$searchQuery);    
		
		    
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
			'call'=>$call,'dashboardcall'=>$dashboardcall
		);		
			
		return $dataTmp;
	}
	
	public function getIssuingAuthority($doctypeid)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('w'=>'main_workeligibilitydoctypes'),array('w.id','w.issuingauthority','w.documenttype'))
					    ->where('w.isactive = 1 AND w.id='.$doctypeid.' ');
		return $this->fetchAll($select)->toArray();
	}
}