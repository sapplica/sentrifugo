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

class Default_Model_Appraisalcategory extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_pa_category';
    protected $_primary = 'id';
	
	public function getAppraisalCategoryData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "ac.isactive = 1";
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$servicedeskDepartmentData = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('ac'=>'main_pa_category'),array('ac.*'))
                           ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		return $servicedeskDepartmentData;       		
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
							$searchQuery .= " ".$key." like '%".mysql_real_escape_string($val)."%' AND ";
                           $searchArray[$key] = $val;
				}
				$searchQuery = rtrim($searchQuery," AND");					
			}
			
		$objName = 'appraisalcategory';
		
		$tableFields = array('action'=>'Action','category_name' => 'Parameter','description' => 'Description');
		
		$tablecontent = $this->getAppraisalCategoryData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
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
	
	public function getAppraisalCategoryDatabyID($id)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('ac'=>'main_pa_category'),array('ac.*'))
					    ->where('ac.isactive = 1 AND ac.id='.$id.' ');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function getAppraisalCategorysData()
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('ac'=>'main_pa_category'),array('ac.*'))
					    ->where('ac.isactive = 1');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function SaveorUpdateAppraisalCategoryData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_pa_category');
			return $id;
		}
		
	
	}

	public function getAppraisalCategoryNamebyID($id)
	{   
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select id,category_name from main_pa_category where isactive=1 and id = ".$id;
		$res = $db->query($query)->fetchAll();
		return $res;
	}
	
	public function getCategoryNameByIds($categoryids)
		{
		   $categoryArr = array();		
		   if($categoryids !='')
		   	$categoryArr = explode(",",$categoryids);
		   
		   $select = $this->select()
							->setIntegrityCheck(false)
							->from(array('c'=>'main_pa_category'),array('c.*'))
							->where('c.isactive = 1 AND c.id IN(?)',$categoryArr);
			return $this->fetchAll($select)->toArray();
		
		}
		
		public function checkDuplicateParameterName($categoryName)
		{
			$db = Zend_Db_Table::getDefaultAdapter();
		    $qry = "select count(*) as count from main_pa_category h where h.category_name='".$categoryName."' AND h.isactive=1 ";
			$res = $db->query($qry)->fetchAll();
			return $res;
		}
}