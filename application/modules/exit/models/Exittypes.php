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
class Exit_Model_Exittypes extends Zend_Db_Table_Abstract
{
	protected $_name = 'main_exit_types';
	private $db;

	public function init()
	{
		$this->db = Zend_Db_Table::getDefaultAdapter();
	}
	
	/**
	** function to retrieve exit type
	** @con = 'add' gets active exit type 
	** @con = 'cnt' gets active exit type count
	** @con = 'grid' gets active exit type data
	**/
	public function getExittypes($con,$sort='', $by='', $pageNo='', $perPage='',$searchQuery='')
	{
		$where = "et.isactive = 1";
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$res = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('et'=>'main_exit_types'),array('et.*'))
                           ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		return $res ; 
		
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
							$searchQuery .= " ".$key." like '%".($val)."%' AND ";
                           $searchArray[$key] = $val;
				}
				$searchQuery = rtrim($searchQuery," AND");					
			}
			
	
			
		$objName = 'exittypes';
		
		$tableFields = array('action'=>'Action','exit_type'=>'Exit Type','description'=>'Description');
		
		$tablecontent = $this->getExittypes('grid',$sort, $by, $pageNo, $perPage,$searchQuery);     
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
 
	// get all exit type data 
	public function getExittypesData()
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('ac'=> $this->_name),array('ac.*'))
					    ->where('ac.isactive = 1');
		return $this->fetchAll($select)->toArray();
	
	}
	// get exit type data based on id
	public function getExittypeById($id)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('ac'=>'main_exit_types'),array('ac.*'))
					    ->where('ac.isactive = 1 AND ac.id='.$id.' ');
		return $this->fetchAll($select)->toArray();
	
	}
	//save or update exit types
	public function SaveorUpdateExitTypesData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_exit_types');
			return $id;
		}	
	}
}
?>