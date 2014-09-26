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

class Default_Model_Appraisalemployeeratings extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_pa_employee_ratings';
    protected $_primary = 'id';
	
	public function getAppraisalEmpStatusData($sort, $by, $pageNo, $perPage, $searchQuery, $a, $b)
	{
		$where = "aer.isactive = 1 AND ai.businessunit_id = ".$a." AND ai.department_id = ".$b." AND ai.status = 1 ";
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$servicedeskDepartmentData = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('aer'=>'main_pa_employee_ratings'),array('es.userfullname','es.reporting_manager_name','es.department_name','aer.appraisal_status'))
                           ->joinInner(array('es'=>'main_employees_summary'), 'es.user_id = aer.employee_id')
                           ->joinInner(array('ai'=>'main_pa_initialization'), 'ai.id = aer.pa_initialization_id')
                           ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		return $servicedeskDepartmentData;       		
	}
	
	public function getAppraisalEmployeeStatusGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$a='',$b='',$c='',$d='')
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
			
		$objName = 'appraisalstatusemployee';
		
		$tableFields = array('userfullname' => 'Employee Name','reporting_manager_name' => 'Reporting Manager','department_name' => 'Department','appraisal_status' => 'Status');
		
		$tablecontent = $this->getAppraisalEmpStatusData($sort, $by, $pageNo, $perPage, $searchQuery, $a, $b);     
		
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
	
	public function SaveorUpdateAppraisalSkillsData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_pa_employee_ratings');
			return $id;
		}	
	}    
}