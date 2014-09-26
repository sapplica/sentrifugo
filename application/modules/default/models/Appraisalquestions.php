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

class Default_Model_Appraisalquestions extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_pa_questions';
    protected $_primary = 'id';
	
	public function getAppraisalQuestionData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "aq.isactive = 1 and ac.isactive=1";
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$appQuestionsData = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('aq'=>'main_pa_questions'),array('aq.*'))
                           ->joinInner(array('ac'=>'main_pa_category'), 'aq.pa_category_id = ac.id', array('category_name' => 'ac.category_name'))
                           ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		return $appQuestionsData;       		
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
							$searchQuery .= " aq.".$key." like '%".$val."%' AND ";
                           $searchArray[$key] = $val;
				}
				$searchQuery = rtrim($searchQuery," AND");					
			}
			
		$objName = 'appraisalquestions';
		
		$tableFields = array('action'=>'Action','category_name'=>'Category','question' => 'Question','description' => 'Description');
		
		$tablecontent = $this->getAppraisalQuestionData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
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
	
	public function getAppraisalQuestionbyID($id)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('aq'=>'main_pa_questions'),array('aq.*'))
					    ->where('aq.isactive = 1 AND aq.id='.$id.' ');
		return $this->fetchAll($select)->toArray();	
	}
	
	public function getAppraisalQuestionsByCategotyID($categotyId)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('aq'=>'main_pa_questions'),array('aq.*'))
					    ->where('aq.isactive = 1 AND aq.pa_category_id='.$categotyId.' ');
		return $this->fetchAll($select)->toArray();	
	}
	
	public function SaveorUpdateAppraisalQuestionData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_pa_questions');
			return $id;
		}	
	}
	
	public function checkDuplicateQuestionName($categoryId,$question)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$qry = "select count(*) as count from main_pa_questions aq where aq.pa_category_id =".$categoryId." AND aq.question='".$question."' AND aq.isactive=1 ";
		$res = $db->query($qry)->fetchAll();
		return $res;
	}
}