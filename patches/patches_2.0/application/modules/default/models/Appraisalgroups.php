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

class Default_Model_Appraisalgroups extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_pa_groups';
    protected $_primary = 'id';
	
	public function getAppraisalGroupData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "ag.isactive = 1";
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$servicedeskDepartmentData = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('ag'=>'main_pa_groups'),array('ag.*'))
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
							$searchQuery .= " ".$key." like '%".$val."%' AND ";
                           $searchArray[$key] = $val;
				}
				$searchQuery = rtrim($searchQuery," AND");					
			}
			
		$objName = 'appraisalgroups';
		
		$tableFields = array('action'=>'Action','group_name' => 'Group','description' => 'Description');
		
		$tablecontent = $this->getAppraisalGroupData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
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
	
	public function getAppraisalGroupsDatabyID($id)
	{
	    $select = $this->select()
                            ->setIntegrityCheck(false)
                            ->from(array('ag'=>'main_pa_groups'),array('ag.*'))
                            ->where('ag.isactive = 1 AND ag.id='.$id.' ');
            return $this->fetchAll($select)->toArray();	
	}
	
	public function getAppraisalGroupsName($groupids)
	{
		$options = array();
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select id,group_name from main_pa_groups 
                      where isactive = 1 and id IN ($groupids) ";
            $result = $db->query($query);
            $options = $result->fetchAll();
        return $options;
		
	}
	
	public function getAppraisalGroupsData($groupids='')
	{
		$where = 'ag.isactive=1';
		if($groupids !='')
		{
			$where .= ' AND ag.id NOT IN('.$groupids.')';
		}
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('ag'=>'main_pa_groups'),array('ag.*'))
					    ->where($where);
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function getDuplicateGroupName($appraisalid,$groupname,$groupid)
	{
            $where = 'ag.isactive = 1 AND ag.pa_initialization_id='.$appraisalid.' ';
            if($groupid)
                $where.=' AND ag.group_name = "'.$groupname.'" AND id<>'.$groupid.' ';
            else
                $where.=' AND ag.group_name = "'.$groupname.'" ';
            $auth = Zend_Auth::getInstance();
            if($auth->hasIdentity())
            {
                $loginUserId = $auth->getStorage()->read()->id;
                $loginuserGroup = $auth->getStorage()->read()->group_id;
            }
            if($loginuserGroup == MANAGER_GROUP || $loginuserGroup == CUSTOM_GROUP )
                $where .= " and ag.createdby_group = '".$loginuserGroup."' and ag.createdby = '".$loginUserId."' ";
                
	    $select = $this->select()
                            ->setIntegrityCheck(false)
                            ->from(array('ag'=>'main_pa_groups'),array('grpcnt'=>'count(*)'))
                            ->where($where);
            return $this->fetchAll($select)->toArray();	
	}
	
	public function SaveorUpdateAppraisalGroupsData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_pa_groups');
			return $id;
		}
		
	
	}    
}