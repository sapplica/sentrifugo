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

class Default_Model_Empadditionaldetails extends Zend_Db_Table_Abstract
{	
    protected $_name = 'main_empadditionaldetails';
    protected $_primary = 'id';
	
	public function getEmpAdditionalsData($sort, $by, $pageNo, $perPage,$searchQuery,$id)
	{
		$where = " e.user_id = ".$id." AND e.isactive = 1 ";

		if($searchQuery)
		$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();

		$empadditionaldetailsData = $this->select()
		->setIntegrityCheck(false)
		->from(array('e' => 'main_empadditionaldetails'),array('e.*',
		                                               'military_status'=>'if(e.military_status = 1,"Yes","No")',
		                                               'from_date'=>'DATE_FORMAT(e.from_date,"'.DATEFORMAT_MYSQL.'")',
													   'to_date'=>'DATE_FORMAT(e.to_date,"'.DATEFORMAT_MYSQL.'")'))
		->joinLeft(array('v'=>'main_veteranstatus'),'e.veteran_status=v.id AND v.isactive = 1',array('veteran_status'=>'v.veteranstatus'))
		->joinLeft(array('m'=>'main_militaryservice'),'e.military_servicetype=m.id AND m.isactive = 1',array('military_servicetype'=>'m.militaryservicetype'))
		->where($where)
		->order("$by $sort")
		->limitPage($pageNo, $perPage);
		
		return $empadditionaldetailsData;
	}
	
	public function getsingleEmpAdditionalDetailsData($id)
	{
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('ea'=>'main_empadditionaldetails'),array('ea.*'))
						->where('ea.user_id='.$id.' AND ea.isactive = 1');
					
		return $this->fetchAll($select)->toArray();
	}
	
	public function getEmpAdditionalDetailsData($id)
	{
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('es'=>'main_empadditionaldetails'),array('es.*'))
		->where('es.id='.$id.' AND es.isactive = 1');
			
		return $this->fetchAll($select)->toArray();
	}
	
	public function SaveorUpdateEmpAdditionalData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_empadditionaldetails');
			return $id;
		}
		
	}
	
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$exParam1='',$exParam2='',$exParam3='',$exParam4='')
	{
		$searchQuery = '';
		$tablecontent = '';
		$searchArray = array();
		$data = array(); 
		$dataTmp = array();
		/** search from grid - START **/
		if($searchData != '' && $searchData!='undefined')
		{
			$searchValues = json_decode($searchData);
			foreach($searchValues as $key => $val)
			{
				if($key == 'from_date')
				{
					$searchQuery .= " ".$key." like '%".  sapp_Global::change_date($val,'database')."%' AND ";
				}
				else if($key == 'to_date')
				{
					$searchQuery .= " ".$key." like '%".  sapp_Global::change_date($val,'database')."%' AND ";
				}
				else
				$searchQuery .= " ".$key." like '%".$val."%' AND ";

				$searchArray[$key] = $val;
			}
			$searchQuery = rtrim($searchQuery," AND");
		}
		
		$objName = 'empadditionaldetails';

		$tableFields = array('action'=>'Action','military_status'=>'Served in Military','branch_service'=>'Branch of Service','from_date'=>'From','to_date'=>'To','veteran_status'=>'Veteran Status','military_servicetype'=>'Service Type');
			
		$tablecontent = $this->getEmpAdditionalsData($sort,$by,$pageNo,$perPage,$searchQuery,$exParam1);

		$bool_arr = array('' => 'All',1 => 'Yes',2 => 'No');
		
		$empVeteranStatusArr = $this->empveteranstatus($exParam1);
		$veteranstatusArr = array();
		if(!empty($empVeteranStatusArr)){
			for($i=0;$i<sizeof($empVeteranStatusArr);$i++)
			{
				$veteranstatusArr[$empVeteranStatusArr[$i]['id']] = $empVeteranStatusArr[$i]['veteranstatus'];
			}
		}
		
		$empMiltitaryStatusArr = $this->empmilitarystatus($exParam1);
		$militarystatusArr = array();
		if(!empty($empMiltitaryStatusArr)){
			for($i=0;$i<sizeof($empMiltitaryStatusArr);$i++)
			{
				$militarystatusArr[$empMiltitaryStatusArr[$i]['id']] = $empMiltitaryStatusArr[$i]['militaryservicetype'];
			}
		}
		
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
						'menuName'=>'Additional Details',
						'formgrid'=>'true',
						'unitId'=>$exParam1,
						'dashboardcall'=>$dashboardcall,
						'call'=>$call,
						'context'=>$exParam2,
						'search_filters' => array(
						'military_status' => array(
                                'type' => 'select',
                                'filter_data' => $bool_arr,
                            ),
						'military_servicetype' => array('type'=>'select',
													'filter_data'=>array(''=>'All')+$militarystatusArr),
						'veteran_status' => array('type'=>'select',
													'filter_data'=>array(''=>'All')+$veteranstatusArr),							
						'from_date'=>array('type'=>'datepicker','yearrange'=>'yearrange'),
						'to_date'=>array('type'=>'datepicker','yearrange'=>'yearrange')
		)
		);
		return $dataTmp;
	}
	
	public function empveteranstatus($userId)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$Data = $db->query("select v.id,v.veteranstatus
								from main_veteranstatus as v
								where v.isactive=1 ");	
		$data = $Data->fetchAll();
		
		return $data;
	}
	
	public function empmilitarystatus($userId)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$Data = $db->query("select m.id,m.militaryservicetype
								from main_militaryservice as m
								where m.isactive=1 ");	
		$data = $Data->fetchAll();
		
		return $data;
	}
	
}