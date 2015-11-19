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
/**
 *
 * @model Tasks Model
 * @author sagarsoft
 *
 */
class Timemanagement_Model_Tasks extends Zend_Db_Table_Abstract
{
	protected $_name = 'tm_tasks';
	protected $_primary = 'id';

	/**
	 * This will fetch all the active task details.
	 *
	 * @param string $sort
	 * @param string $by
	 * @param number $pageNo
	 * @param number $perPage
	 * @param string $searchQuery
	 *
	 * @return array $defaultTaskData
	 */
	public function getDefaultTaskData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "is_active = 1 and is_default = 1";

		if($searchQuery)
		$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();

		$defaultTaskData = $this->select()
		->setIntegrityCheck(false)
		->where($where)
		->order("$by $sort")
		->limitPage($pageNo, $perPage);

		return $defaultTaskData;
	}

	/**
	 * This will fetch all the default task details based on the search paramerters passed with pagination.
	 *
	 * @param string $sort
	 * @param string $by
	 * @param number $perPage
	 * @param number $pageNo
	 * @param JSON $searchData
	 * @param string $call
	 * @param string $dashboardcall
	 * @param string $a
	 * @param string $b
	 * @param string $c
	 * @param string $d
	 *
	 * @return array
	 */
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
			
		$objName = 'defaulttasks';

		$tableFields = array('action'=>'Action','task' => 'Default task');

		$tablecontent = $this->getDefaultTaskData($sort, $by, $pageNo, $perPage,$searchQuery);

		$dataTmp = array(
			'sort' => $sort,
			'by' => $by,
			'pageNo' => $pageNo,
			'perPage' => $perPage,				
			'tablecontent' => $tablecontent,
			'objectname' => $objName,
		    'menuName' => 'Default Tasks',
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

	public function SaveorUpdateTaskData($data, $where)
	{
		if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId($this->_name);
			return $id;
		}
	}

	public function getTaskData($id)
	{
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('t'=>$this->_name),array('t.*'))
		->where('t.is_active = 1 AND t.id='.$id.' ');
		return $this->fetchAll($select)->toArray();
	}

	public function checkProjectTasks($id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select count(*) as count from tm_project_tasks where task_id = ".$id." AND is_active = 1";
		$result = $db->query($query)->fetch();
		return $result['count'];
	}

	public function getcheckDupTask($taskname){
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('t'=>$this->_name),array('t.*'))
						->where('t.is_active = 1 AND t.task="'.$taskname.'" ');
						
		return $this->fetchAll($select)->toArray();
	}
	
	public function getTaskById($id)
    {
        $data=$this->fetchRow("id=".$id)->toArray();
        return $data['task'];
    }	


}
