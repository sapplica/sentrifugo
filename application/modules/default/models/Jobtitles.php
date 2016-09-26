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

class Default_Model_Jobtitles extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_jobtitles';
    protected $_primary = 'id';
	
	public function getJobtitlesData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "j.isactive = 1 AND p.isactive = 1 ";
		
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$JobtitlesData = $this->select()
    					   ->setIntegrityCheck(false)	 
						   ->from(array('j' => 'main_jobtitles'),array('j.*'))
						   ->joinInner(array('p'=>'main_payfrequency'), 'j.jobpayfrequency=p.id', array('freqtype' => 'p.freqtype'))
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $JobtitlesData;       		
	}
	public function getsingleJobTitleData($id)
	{
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$jobTitleData = $db->query("SELECT * FROM main_jobtitles WHERE isactive=1 and id = ".$id);
		$res = $jobTitleData->fetchAll();
		if (isset($res) && !empty($res)) 
		{	
			return $res;
		}
		else
			return 'norows';
	}
	
	public function SaveorUpdateJobTitleData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_jobtitles');
			return $id;
		}
		
	
	}
	
	public function getJobTitleList()
	{
		
		$where = "j.isactive = 1 AND p.isactive = 1 ";
	
	$JobtitlesData = $this->select()
				   ->setIntegrityCheck(false)	 
				   ->from(array('j' => 'main_jobtitles'),array('j.*'))
				   ->joinInner(array('p'=>'main_payfrequency'), 'j.jobpayfrequency=p.id', array('freqtype' => 'p.freqtype'))
				   ->where($where)
				   ->order('j.jobtitlename');
	
	return $this->fetchAll($JobtitlesData)->toArray();
	
	}
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$exParam1='',$exParam2='',$exParam3='',$exParam4='')
	{		
        $searchQuery = '';$tablecontent = '';  $searchArray = array();$data = array(); $dataTmp = array();
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
		$objName = 'jobtitles';
		
		$tableFields = array('action'=>'Action','jobtitlecode' => 'Job Title Code','jobtitlename' =>'Job Title','jobdescription' => 'Job Description','minexperiencerequired' => 'Min Experience Required','jobpaygradecode' => 'Job Pay Grade Code','freqtype' => 'Job Pay Frequency','comments' => 'Comments');
		
		$tablecontent = $this->getJobtitlesData($sort, $by,$pageNo,$perPage,$searchQuery);     
		
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
			'call'=>$call,
			'dashboardcall'=>$dashboardcall
		);		
			
		return $dataTmp;
	}
        
}