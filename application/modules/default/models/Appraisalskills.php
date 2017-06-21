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

class Default_Model_Appraisalskills extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_pa_skills';
    protected $_primary = 'id';
	
	public function getAppraisalSkillData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "as.isactive = 1";
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
        {		
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
     	}
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		// if($loginuserRole != 1)
        // {
            // if($loginuserGroup != MANAGER_GROUP)
                // $where .= " AND (as.createdby_group in ( ".$loginuserGroup.") or as.createdby_group is null ) ";
            // else 
                // $where .= " AND (as.createdby_group in ( ".$loginuserGroup.") ) ";
        // }	
		$db = Zend_Db_Table::getDefaultAdapter();		
		$servicedeskDepartmentData = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('as'=>'main_pa_skills'),array('as.*'))
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
			
		$objName = 'appraisalskills';
		
		$tableFields = array('action'=>'Action','skill_name' => 'Skill','description' => 'Description');
		
		$tablecontent = $this->getAppraisalSkillData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
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
	
	public function getAppraisalSkillsDatabyID($id)
	{
     	$where = 'as.isactive = 1 AND as.id='.$id.' ';
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('as'=>'main_pa_skills'),array('as.*'))
					    ->where($where);
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function getAppraisalSkillsData()
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('as'=>'main_pa_skills'),array('as.*'))
					    ->where('as.isactive = 1');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function getselectedAppraisalSkillsData($skillsid)
	{
		$where = '';
		if($skillsid!='')
		{
			$where=" AND s.id NOT IN ($skillsid) ";
		}
	    $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select s.id,s.skill_name from main_pa_skills s where s.isactive=1 $where ";
        $result = $db->query($query)->fetchAll();
        return $result;
	
	}
	
	public function SaveorUpdateAppraisalSkillsData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_pa_skills');
			return $id;
		}
		
	
	}

	public function getDuplicateSkillsName($skillname)
	{
	    	$select = $this->select()
                            ->setIntegrityCheck(false)
                            ->from(array('sk'=>'main_pa_skills'),array('grpcnt'=>'count(*)'))
                            ->where('sk.isactive=1 AND sk.skill_name = "'.$skillname.'" ');
            return $this->fetchAll($select)->toArray();	
	}
}