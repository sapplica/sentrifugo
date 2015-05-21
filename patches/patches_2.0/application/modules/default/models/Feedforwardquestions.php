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

class Default_Model_Feedforwardquestions extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_pa_questions';
    protected $_primary = 'id';
	
	public function getFeedforwardQuestionData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
		
		$loginuserRole = $auth->getStorage()->read()->emprole;
		$loginuserGroup = $auth->getStorage()->read()->group_id;
     	}
		$where = "aq.isactive = 1 and aq.module_flag=2";
		if($searchQuery)
			$where .= " AND ".$searchQuery;
			if($loginuserRole != 1)
			$where .= " AND aq.createdby_group = ".$loginuserGroup;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$appQuestionsData = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('aq'=>'main_pa_questions'),array('aq.*'))
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
							$searchQuery .= " aq.".$key." like '%".mysql_real_escape_string($val)."%' AND ";
                           $searchArray[$key] = $val;
				}
				$searchQuery = rtrim($searchQuery," AND");					
			}
			
		$objName = 'feedforwardquestions';
		
		$tableFields = array('action'=>'Action','question' => 'Question','description' => 'Description');
		
		$tablecontent = $this->getFeedforwardQuestionData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
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
	
	public function getFeedforwardQuestionbyID($id)
	{
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
		
		$loginuserRole = $auth->getStorage()->read()->emprole;
		$loginuserGroup = $auth->getStorage()->read()->group_id;
     	}
		$where = 'aq.isactive = 1 AND aq.id='.$id.' ';
		
			if($loginuserRole != 1)
			$where .= " AND aq.createdby_group = ".$loginuserGroup;
			
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('aq'=>'main_pa_questions'),array('aq.*'))
					    ->where($where);
		return $this->fetchAll($select)->toArray();	
	}
	
	public function getFeedforwardQuestionsByCategotyID($categotyId)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('aq'=>'main_pa_questions'),array('aq.*'))
					    ->where('aq.isactive = 1 AND aq.pa_category_id='.$categotyId.' ');
		return $this->fetchAll($select)->toArray();	
	}
	
	public function SaveorUpdateFeedforwardQuestionData($data, $where)
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
	
	public function checkQuestionUsed($questionId)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$qry = "select count(*) as count from main_pa_ff_initialization where FIND_IN_SET(".$questionId.",questions) and isactive = 1";
		$res = $db->query($qry)->fetchAll();
		return $res;
	}
}