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

class Default_Model_Empjobhistory extends Zend_Db_Table_Abstract
{
	protected $_name = 'main_empjobhistory';
    protected $_primary = 'id';
	
	public function getEmpJobHistoryData($sort, $by, $pageNo, $perPage,$searchQuery,$id)
	{
		$where = " e.user_id = ".$id." AND e.isactive = 1 ";
		
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$empjobhistoryData = $this->select()
    					   ->setIntegrityCheck(false)	 
						   ->from(array('e' => 'main_empjobhistory'),array('id'=>'e.id','active_company'=>'if(e.active_company = 1,"Yes","No")','start_date'=>'DATE_FORMAT(e.start_date,"'.DATEFORMAT_MYSQL.'")','end_date'=>'DATE_FORMAT(e.end_date,"'.DATEFORMAT_MYSQL.'")'))
						   ->joinLeft(array('p'=>'main_positions'),'e.positionheld=p.id AND p.isactive = 1',array('positionheld'=>'p.positionname'))
						   ->joinLeft(array('d'=>'main_departments'),'e.department=d.id AND d.isactive = 1',array('department'=>'d.deptname'))
						   ->joinLeft(array('j'=>'main_jobtitles'),'e.jobtitleid=j.id AND j.isactive = 1',array('jobtitleid'=>'j.jobtitlename'))
						   ->joinLeft(array('c'=>'tm_clients'),'c.id=e.client_id AND c.is_active = 1',array('client_id'=>'c.client_name'))
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		return $empjobhistoryData;       		
	}
	
	public function getsingleEmpJobHistoryData($id)
	{
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('ej'=>'main_empjobhistory'),array('ej.*'))
						->where('ej.id='.$id.' AND ej.isactive = 1');		
		return $this->fetchAll($select)->toArray();
	}
	
	public function SaveorUpdateEmpJobHistory($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_empjobhistory');
			return $id;
		}
		
	}
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$exParam1='',$exParam2='',$exParam3='',$exParam4='')
	{		
        $searchQuery = '';  $searchArray = array();       $data = array();		
		if($searchData != '' && $searchData!='undefined')
		{
			$searchValues = json_decode($searchData);
			foreach($searchValues as $key => $val)
			{
				if($key == 'department')
				{
					$searchQuery .= " d.deptname like '%".$val."%' AND ";
				}
				/*else if($key == 'jobtitleid')
				{
					$searchQuery .= " j.jobtitlename like '%".$val."%' AND ";
				}
				else if($key == 'positionheld')
				{
					$searchQuery .= " p.positionname like '%".$val."%' AND ";
				}*/
				else if($key == 'client_id')
				{
					$searchQuery .= " c.client_name like '%".$val."%' AND ";
				}
				else if($key == 'start_date')
				{
					$searchQuery .= " ".$key." like '%".  sapp_Global::change_date($val,'database')."%' AND ";
				}else if($key == 'year_skill_last_used')
				{
				}
				else
				$searchQuery .= " ".$key." like '%".$val."%' AND ";
				$searchArray[$key] = $val;
			}
			$searchQuery = rtrim($searchQuery," AND");					
		}
		$objName = 'empjobhistory';
		
		$tableFields = array('action'=>'Action','department'=>'Department','client_id'=>'Client','start_date'=>'From','end_date'=>'To');
		
		$tablecontent = $this->getEmpJobHistoryData($sort, $by, $pageNo, $perPage,$searchQuery,$exParam1);     
		
		$bool_arr = array('' => 'All',1 => 'Yes',2 => 'No');
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
						'menuName'=>'Job history',
						'formgrid'=>'true',
						'unitId'=>$exParam1,
						'call'=>$call,
						'context'=>$exParam2,
						'search_filters' => array(
						  'active_company' => array(
                                'type' => 'select',
                                'filter_data' => $bool_arr,
                            ), 
						  'start_date'=>array('type'=>'datepicker'),
						  'end_date'=>array('type'=>'datepicker')
						)
					);			
		return $dataTmp;
	}
	
	
}
?>