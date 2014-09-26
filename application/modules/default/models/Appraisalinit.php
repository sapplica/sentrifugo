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

class Default_Model_Appraisalinit extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_pa_initialization';
    protected $_primary = 'id';
	
	public function getAppraisalInitData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "ai.isactive = 1";
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$appInitData = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('ai'=>'main_pa_initialization'),array('ai.*'))
                           
                           ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
    					   
    		
		return $appInitData;       		
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
							$searchQuery .= " ai.".$key." like '%".$val."%' AND ";
                           $searchArray[$key] = $val;
				}
				$searchQuery = rtrim($searchQuery," AND");					
			}
			
		$objName = 'appraisalinit';
		
		$tableFields = array('action'=>'Action','appraisal_mode'=>'Appraisal Period','appraisal_from_date' => 'From','appraisal_to_date' => 'To','status' => 'Status');
		
		$tablecontent = $this->getAppraisalInitData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
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
		);
		return $dataTmp;
	}
	
	public function getConfigData($id)
	{
		 $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('pi'=>'main_pa_initialization'),array('pi.*'))
					    ->where('pi.isactive = 1 AND pi.id='.$id.' ');
		return $this->fetchAll($select)->toArray();
		
	}
	
	public function getAppImplementationData($businessUnitId,$departmentId)
	{
		 $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('pim'=>'main_pa_implementation'),array('pim.*'))
					    ->where('pim.isactive = 1 AND pim.businessunit_id = '.$businessUnitId.' AND pim.department_id = '.$departmentId);
		return $this->fetchAll($select)->toArray();		
	}
	
	public function checkAppraisalExists($businessUnitId,$departmentId)
	{
		 $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('pi'=>'main_pa_initialization'),array('pi.*'))
					    ->where('pi.isactive = 1 AND pi.status in (0,1) AND pi.businessunit_id = '.$businessUnitId.' AND pi.department_id = '.$departmentId);
		return $this->fetchAll($select)->toArray();
	}
	
	public function SaveorUpdateAppraisalInitData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_pa_initialization');
			return $id;
		}
		
	}
		
		

}