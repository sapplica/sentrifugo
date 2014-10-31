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

class Default_Model_Trainingandcertificationdetails extends Zend_Db_Table_Abstract
{
	protected $_name = 'main_empcertificationdetails';
    protected $_primary = 'id';
	
	public function getTandCdetailsData($sort, $by, $pageNo, $perPage,$searchQuery,$userid)
	{	
		$where = "isactive = 1  AND user_id = ".$userid ;
		
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$expData = $this->select()
    					   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $expData;         		
	}
	public function getTandCdetailsRecord($id=0)
	{  
		$empdependencyDetailsArr="";$where = "";
		$db = Zend_Db_Table::getDefaultAdapter();		
		if($id != 0)
		{
			$where = "id =".$id;
			$empdependencyDetails = $this->select()
									->from(array('c'=>'main_empcertificationdetails'))
									->where($where);
		
			$empdependencyDetailsArr = $this->fetchAll($empdependencyDetails)->toArray(); 
        }
		return $empdependencyDetailsArr;       		
	}
    
	public function SaveorUpdateEmployeeTandCData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_empcertificationdetails');
			return $id;
		}
		
	}
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$exParam1='',$exParam2='',$exParam3='',$exParam4='')
	{		
        $searchQuery = '';$tablecontent = '';
        $searchArray = array();$data = array();$id='';
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
		$objName = 'trainingandcertificationdetails';
				
		$tableFields = array('action'=>'Action','course_name'=>'Course Name','course_level'=>'Course Level','course_offered_by'=>'Course Offered By','certification_name'=>'Certification Name');
				
						
		$tablecontent = $this->getTandCdetailsData($sort, $by,$pageNo,$perPage,$searchQuery,$exParam1);     
		
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
						'dashboardcall'=>$dashboardcall,
						'add'=>'add',
						'menuName'=>'Training & Certification',
						'formgrid' => 'true',
						'unitId'=>$exParam1,
						'call'=>$call,
						'context'=>$exParam2,
								
						);	
		return $dataTmp;
	}
	
	
}
?>