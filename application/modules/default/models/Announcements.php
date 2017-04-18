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

class Default_Model_Announcements extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_announcements';
    protected $_primary = 'id';
	
	public function getAnnouncementsData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginuserGroup = $auth->getStorage()->read()->group_id;
			$loginuserRole = $auth->getStorage()->read()->emprole;
			$businessunit_id = $auth->getStorage()->read()->businessunit_id;
			$department_id = $auth->getStorage()->read()->department_id;
		}
		
		$where = "a.isactive = 1";
		if($loginuserGroup == HR_GROUP || $loginuserGroup == MANAGEMENT_GROUP || $loginuserRole == 1)
		{
			$where .= ' AND a.status in (1,2) ';
		}
		else
		{
			$where .= ' AND a.status = 2 ';
			if($businessunit_id)
			$where .= ' AND FIND_IN_SET('.$businessunit_id.',a.businessunit_id)';
			if($department_id)
			$where .= ' AND FIND_IN_SET('.$department_id.',a.department_id)';
		}
		
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		// Show announcements with multiple departments
		$announcementData = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('a'=>'main_announcements'),array('a.*','status' =>new Zend_Db_Expr("case  when a.status = 1 then 'Save as draft' when a.status = 2 then 'Posted' end"),))
                           ->joinInner(array('b'=>'main_businessunits'), "FIND_IN_SET(b.id, a.businessunit_id) and b.isactive = 1",array('unitname'=>'GROUP_CONCAT(DISTINCT b.unitname)'))
                           ->joinInner(array('d'=>'main_departments'), "FIND_IN_SET(d.id, a.department_id) and d.isactive = 1",array('deptname'=>'GROUP_CONCAT(DISTINCT d.deptname)'))
                           ->where($where)
                           ->group("a.id")
    					   ->order("$by $sort")
    					   ->limitPage($pageNo, $perPage);  					   
    		return $announcementData;
	}
	
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$a='',$b='',$c='',$d='')
	{		
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
		
        $searchQuery = '';
        $searchArray = array();
        $data = array();
		
		if($searchData != '' && $searchData!='undefined')
			{
				$searchValues = json_decode($searchData);
				foreach($searchValues as $key => $val)
				{
					if($key=='description')
					{
						$searchQuery .= " ".'a.description'." like '%".$val."%' AND ";
					}
					else
					{
						$searchQuery .= " ".$key." like '%".$val."%' AND ";
					}
                           $searchArray[$key] = $val;
				}
				$searchQuery = rtrim($searchQuery," AND");					
			}
			
		$objName = 'announcements';
		
		if($loginuserGroup != EMPLOYEE_GROUP && $loginuserGroup != SYSTEMADMIN_GROUP)
			/*$tableFields = array('action'=>'Action','title' => 'Title','description' => 'Description', 'unitname' => 'Business Unit', 'status' => 'Status');*/
			// Show announcements with multiple departments
			$tableFields = array('action'=>'Action','title' => 'Title','description' => 'Description', 'unitname' => 'Business Units', 'deptname' => 'Departments', 'status' => 'Status');
		else {
			//$tableFields = array('action'=>'Action','title' => 'Title', 'unitname' => 'Business Unit' ,'description' => 'Description');
			
			// Show announcements with multiple departments
			$tableFields = array('action'=>'Action','title' => 'Title', 'unitname' => 'Business Units', 'deptname' => 'Departments', 'description' => 'Description');
		}
		
		$tablecontent = $this->getAnnouncementsData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
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
			'search_filters' => 
						array(
                            'status' => array(
                                'type' => 'select',
                                'filter_data' => array(''=>'All',1=>'Save as draft',2=>'Posted'),
                           	),
                        ),
		);
		return $dataTmp;
	}
	
	public function getAnnouncementsDatabyID($id)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('a'=>'main_announcements'),array('a.*'))
					    ->where('a.isactive = 1 AND a.id='.$id.' ');
		return $this->fetchAll($select)->toArray();
	}
	
	public function getAllAnnouncementsData()
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('a'=>'main_announcements'),array('a.*'))
					    ->where('a.isactive = 1');
		return $this->fetchAll($select)->toArray();
	}
	
	public function getBusinessUnitNames($busi_ids)
	{
	  $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('b'=>'main_businessunits'),array('b.id','b.unitname'))
					    ->where('b.id in ('.$busi_ids.') AND b.isactive = 1');
	
		return $this->fetchAll($select)->toArray();	
	}
	
	public function getDepartmentNames($dept_ids)
	{
	  $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('d'=>'main_departments'),array('d.id','d.deptname'))
					    ->where('d.id in ('.$dept_ids.') AND d.isactive = 1');
	
		return $this->fetchAll($select)->toArray();	
	}
	
	public function SaveorUpdateAnnouncementsData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_announcements');
			return $id;
		}
	}
	
	public function getAllByBusiAndDeptId()
	{
		$where = '';
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginuserGroup = $auth->getStorage()->read()->group_id;
			$loginuserRole = $auth->getStorage()->read()->emprole;
			$businessunit_id = $auth->getStorage()->read()->businessunit_id;
			$department_id = $auth->getStorage()->read()->department_id;
		}
		
		$where = "a.isactive = 1";
		if($loginuserGroup == HR_GROUP || $loginuserGroup == MANAGEMENT_GROUP || $loginuserRole == 1)
		{
			$where .= ' AND a.status in (2) ';
		}
		else
		{
			$where .= ' AND a.status = 2 ';
		
			if($businessunit_id)
			$where .= ' AND FIND_IN_SET('.$businessunit_id.',a.businessunit_id)';
			if($department_id)
			$where .= ' AND FIND_IN_SET('.$department_id.',a.department_id)';
		}
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		 $announcementData = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('a'=>'main_announcements'),array('a.*','status' =>new Zend_Db_Expr("case  when a.status = 1 then 'Save as draft' when a.status = 2 then 'Posted' end"),))
                            ->where($where)
    					   ->order("a.modifieddate DESC")
    					   ->limitPage(0,5);
    		return $this->fetchAll($announcementData)->toArray();
		/*if($busiId)
			$where .= ' AND FIND_IN_SET('.$busiId.',a.businessunit_id)';
		if($deptId)
			$where .= ' AND FIND_IN_SET('.$deptId.',a.department_id)';
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('a'=>'main_announcements'),array('a.*'))
					    ->where("a.isactive = 1 AND a.status = 2 $where")
					    ->order("a.modifieddate DESC");
					    
		return $this->fetchAll($select)->toArray();*/
	}
}