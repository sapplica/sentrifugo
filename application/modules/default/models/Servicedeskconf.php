<?php
/********************************************************************************* 
 *  This file is part of Sentrifugo.
 *  Copyright (C) 2015 Sapplica
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

class Default_Model_Servicedeskconf extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_sd_configurations';
    protected $_primary = 'id';
	
	public function getServiceDeskRequestData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "c.isactive=1 AND b.isactive=1";
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		

	  $serviceConfData = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('c'=>'main_sd_configurations'),array('c.*','service_desk_flag'=>'if (c.service_desk_flag=1,"Business Unit wise","Department Wise")','attachment'=>'if (c.attachment=1,"Yes","No")','request_for'=>'if(c.request_for=1,"Service","Asset")','category_name'=>'if(c.request_for=1,s.service_desk_name,ac.name)'))
                           ->joinLeft(array('s'=>'main_sd_depts'), 'c.service_desk_id = s.id', array('s.service_desk_name'))
                           ->joinLeft(array('ac'=>'assets_categories'), 'c.service_desk_id = ac.id and ac.is_active=1 and ac.parent=0', array('ac.name'))
                           ->joinLeft(array('b'=>'main_businessunits'), 'c.businessunit_id = b.id', array('deptname'=>'ifnull(d.deptname,"No department")'))
                           ->joinLeft(array('d'=>'main_departments'), 'c.department_id = d.id', array('b.unitname'))
                           ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		return $serviceConfData;       		
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
					 if($key == 'unitname')
					 	$searchQuery .= " b.".$key." like '%".$val."%' AND ";
					 else if($key == 'deptname')
						$searchQuery .= " d.".$key." like '%".$val."%' AND ";	
					 else if($key == 'category_name')
					 	$searchQuery .= " s.service_desk_name like '%".$val."%' OR ac.name like '%".$val."%' AND ";	
					 else	
					 	$searchQuery .= " c.".$key." like '%".$val."%' AND ";
                           $searchArray[$key] = $val;
				}
				$searchQuery = rtrim($searchQuery," AND");					
			}
			
		$objName = 'servicedeskconf';
		
		$tableFields = array('action'=>'Action','unitname'=>'Business Unit','deptname' => 'Department',
                    'service_desk_flag'=>'Applicability','request_for'=>'Request For','category_name' => 'Category','attachment' => 'Attachment',
                    'description' => 'Description');
		
		$tablecontent = $this->getServiceDeskRequestData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
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
                        'search_filters' => array(
                            'service_desk_flag' => array(
                                'type' => 'select',
                                'filter_data' => array('' => 'All',1 => 'Business Unit wise', 0 => 'Department wise'),
                            ),
                            'attachment' => array(
                                'type' => 'select',
                                'filter_data' => array('' => 'All',1 => 'Yes', 0 => 'No'),
                            ),
                        	'request_for'=> array(
                        			'type' => 'select',
                        			'filter_data' => array('' => 'All',1 => 'Service', 2 => 'Asset'),
                        	),
                        ),
                        
		);
		return $dataTmp;
	}
	
	public function getServiceDeskConfbyID($id)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('sc'=>'main_sd_configurations'),array('sc.*'))
					    ->where('sc.isactive = 1 AND sc.id='.$id.' ');
		return $this->fetchAll($select)->toArray();
	
	}
/**
 * 
 * Enter description here ...
 * @param unknown_type $data
 * @param unknown_type $where
 */	
	public function SaveorUpdateServiceConfData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_sd_configurations');
			return $id;
		}
		
	
	}
	
	public function getActiveServiceDepartments($bunitid,$deptid='',$reqfor)
	{
		$where = 'sc.isactive=1';
		if($bunitid != '' && $bunitid !='null' )
			$where .= ' AND sc.businessunit_id = '.$bunitid.'';
		if($deptid !='' && $deptid !='null')
			$where .= ' AND sc.department_id = '.$deptid.'';
		
		
		$db = Zend_Db_Table::getDefaultAdapter();
		 $qry = "select sc.service_desk_id from main_sd_configurations sc where ".$where." ";
		$res = $db->query($qry)->fetchAll();
		$serviceId = '';
		if(!empty($res))
		{
			foreach ($res as $ids)
			{
				$serviceId.=$ids['service_desk_id'].',';
			}
			$serviceId = rtrim($serviceId,',');
		}
		$resWhere = 'sd.isactive = 1';
		if($serviceId !='')
		$resWhere.= ' AND sd.id NOT IN ('.$serviceId.')';
		$resWhere.='AND msc.request_for = 1';
		$resultqry = "select sd.id,sd.service_desk_name from main_sd_depts sd INNER JOIN main_sd_configurations msc where ".$resWhere." ";
		$resultqry = "select distinct sd.id,sd.service_desk_name from main_sd_depts sd inner join main_sd_reqtypes sdr on sd.id = sdr.service_desk_id
INNER JOIN main_sd_configurations msc where sdr.isactive = 1 and sd.isactive =1 and msc.request_for=1";
		
		$result = $db->query($resultqry)->fetchAll();
		return $result;
	}
public function getnotconfiguredActiveServiceDepartments($bunitid,$deptid='')
	{
		
		$where = 'sc.isactive=1 and sc.request_for=1';		
		$service_desk_flag = ' AND service_desk_flag = 1';		
		if($bunitid != '' && $bunitid !='null')
			$where .= '  AND sc.businessunit_id = '.$bunitid.'';
		if($deptid !='' && $deptid !='null')
		{
			$service_desk_flag = ' AND service_desk_flag = 0';
			$where .= '  AND sc.department_id = '.$deptid.'';			
		}	
			
		$where .= $service_desk_flag;
		
		$db = Zend_Db_Table::getDefaultAdapter();
	  $qry = "select sc.service_desk_id from main_sd_configurations sc where ".$where." ";
	 
	
		$res = $db->query($qry)->fetchAll();
		$serviceId = '';
		if(!empty($res))
		{
			
			foreach ($res as $ids)
			{
				$serviceId.=$ids['service_desk_id'].',';
			}
			$serviceId = rtrim($serviceId,',');
			
		}
		$resWhere = 'sd.isactive = 1';
		
		if($serviceId !='')
		
		$resWhere.= ' AND sd.id NOT IN ('.$serviceId.')';
		
		if(isset($bunitid) || $bunitid=='0') {
		 $resultqry = "select sd.id,sd.service_desk_name from main_sd_depts sd where ".$resWhere." ";
		}
		if(!empty($resultqry)) {
		$result = $db->query($resultqry)->fetchAll();
		}
		return $result;
	}
	
	public function checkuniqueServiceConfData($bunitid,$sdflag,$sdid,$deptid='',$request_for='')
	{
		
		$where = 'sc.isactive=1 AND sc.businessunit_id ='.$bunitid.' AND sc.service_desk_flag ='.$sdflag.' AND sc.service_desk_id IN ('.$sdid.')';
		if($deptid !='' && $deptid !='null')
			$where .= ' AND sc.department_id = '.$deptid.'';
		if($request_for !='' && $request_for !='null')
			$where .= ' AND sc.request_for = '.$request_for.'';	
			
	   	$db = Zend_Db_Table::getDefaultAdapter();
	    $qry = "select count(*) as count from main_sd_configurations sc where ".$where." ";
		$res = $db->query($qry)->fetchAll();	
		return $res;	
		
	}

	/**
	 * 
	 * Here we are checking if any pending request is there for a business unit
	 * @param integer $bunitid = Id of business unit.
	 */
	public function getPendingServiceReqData($bunitid)
	{
		
		$where = 'sc.isactive=1 AND sc.businessunit_id ='.$bunitid.' and sr.isactive=1 ';
			
	   	$db = Zend_Db_Table::getDefaultAdapter();
		$qry = "select count(*) as count from main_sd_configurations sc
				left join main_sd_requests sr on sr.service_desk_conf_id = sc.id 
		        where ".$where." ";
		$res = $db->query($qry)->fetchAll();	
		return $res;	
		
	}
	
	
/**
	 * 
	 * Here we are checking if any request is there for a service department.
	 * @param integer $id = Id of service department or service request.
	 */
	public function getServiceReqDeptCount($id,$flag='')
	{
		$where = 'sr.isactive=1'; 
		if($flag !='')
		{
			if($flag==1)
				$where.= ' AND sr.service_desk_id ='.$id.' ';
			if($flag==2)	
				$where.= ' AND sr.service_request_id ='.$id.' ';
		}	
			
	   	$db = Zend_Db_Table::getDefaultAdapter();
		$qry = "select count(*) as count from main_sd_requests sr where ".$where." ";
		$res = $db->query($qry)->fetchAll();	
		return $res;	
		
	}
	
	public function getActiveCategoriesData()
	{
		$userdata=	$this->select()
		->setIntegrityCheck(false)
		->from(array('c'=>'assets_categories'),array('c.*'))
		->order('c.id')
		->where('c.is_active =1 and c.parent=0 ');
		return $this->fetchAll($userdata)->toArray();
	
	}
	
	public function getCategoryBYId($id)
	{
		$assetcategoriesData = $this->select()
		->setIntegrityCheck(false)
		->from(array('c1'=>'assets_categories'),array('c1.*'))
		->where('c1.is_active = 1 AND c1.id='.$id.' ');
		return $this->fetchAll($assetcategoriesData)->toArray();
	}
public function getConfigIdForAssetCategory($service_request_id,$login_bu='')
	{
		$where = "c.isactive = 1 AND c.request_for= 2";
		if($login_bu!='') {
			$where.= " and c.businessunit_id=$login_bu";
		}
		
		$configid = $this->select()
					->setIntegrityCheck(false)
					->from(array('c'=>$this->_name),array('c.id'))
					->where(' '.$where.' AND c.service_desk_id IN ('.$service_request_id.') ');
		return $this->fetchAll($configid)->toArray();
	}
}