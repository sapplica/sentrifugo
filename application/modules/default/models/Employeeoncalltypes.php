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

class Default_Model_Employeeoncalltypes extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_employeeoncalltypes';
    protected $_primary = 'id';

	public function getEmployeeOncallData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "e.isactive = 1";

		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();

		$employeeOncallData = $this->select()
    					   ->setIntegrityCheck(false)
                           ->from(array('e'=>'main_employeeoncalltypes'),array('e.id','e.numberofdays','e.isactive','e.oncalltype','e.oncallcode','oncallpreallocated'=>'if(e.oncallpreallocated=1,"Yes","No")','oncallpredeductable'=>'if(e.oncallpredeductable=1,"Yes","No")','e.description'))
						   ->where($where)
    					   ->order("$by $sort")
    					   ->limitPage($pageNo, $perPage);

		return $employeeOncallData;
	}
	public function getsingleEmployeeOncalltypeData($id)
	{

		$db = Zend_Db_Table::getDefaultAdapter();
		$oncallData = $db->query("SELECT * FROM main_employeeoncalltypes WHERE id = ".$id." AND isactive=1");
		$res = $oncallData->fetchAll();
		if (isset($res) && !empty($res))
		{
			return $res;
		}
		else
			return 'norows';
	}

	public function getOncalltypeDataByID($id)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('e'=>'main_employeeoncalltypes'),array('e.*'))
					    ->where('e.isactive = 1 AND e.id='.$id.' ');
		return $this->fetchAll($select)->toArray();

	}

	public function SaveorUpdateEmployeeOncallTypeData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_employeeoncalltypes');
			return $id;
		}

	}

	public function getactiveoncalltype()
	{
	 	$select = $this->select()
    					   ->setIntegrityCheck(false)
                           ->from(array('e'=>'main_employeeoncalltypes'),array('e.id','e.oncalltype','e.numberofdays','e.oncallpredeductable'))
						   ->where('e.isactive = 1');
		return $this->fetchAll($select)->toArray();

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
		$objName = 'employeeoncalltypes';

		$tableFields = array('action'=>'Action','oncalltype' => 'On Call Type','numberofdays' => 'Number Of Days','oncallcode'=>'On Call Code','oncallpreallocated'=>'Is Pre Allocated','oncallpredeductable'=>'Is Deductible','description' => 'Description');

		$bool_arr = array('' => 'All',1 => 'Yes',2 => 'No');
		$tablecontent = $this->getEmployeeOncallData($sort, $by, $pageNo, $perPage,$searchQuery);

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
			'dashboardcall'=>$dashboardcall,
			'searchArray' => $searchArray,
                        'call'=>$call,
                        'search_filters' => array(
                           'oncallpreallocated' => array(
                               'type' => 'select',
                               'filter_data' => $bool_arr,
                           ),
                            'oncallpredeductable' => array(
                               'type' => 'select',
                               'filter_data' => $bool_arr,
                           ),
                        ),
			);

		return $dataTmp;
	}

	public function checkDuplicateOncallType($oncallTypeName)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$qry = "select count(*) as count from main_employeeoncalltypes el where el.oncalltype='".$oncallTypeName."' AND el.isactive=1 ";
		$res = $db->query($qry)->fetchAll();
		return $res;
	}
}
