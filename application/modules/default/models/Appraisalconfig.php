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

class Default_Model_Appraisalconfig extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_pa_implementation';
    protected $_primary = 'id';
	
	public function getAppraisalconfigData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = " c.isactive=1 AND b.isactive=1";
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$appraisalConfigData = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('c'=>'main_pa_implementation'),array('c.*','performance_app_flag'=>'if (c.performance_app_flag=1,"Business Unit wise","Department Wise")','approval_selection'=>'if (c.approval_selection=1,"HR","Manager")','appraisal_ratings'=>'if (c.appraisal_ratings=1,"1-5","1-10")'))
                           ->joinLeft(array('b'=>'main_businessunits'), 'c.businessunit_id = b.id', array('deptname'=>'ifnull(d.deptname,"No department")'))
                           ->joinLeft(array('d'=>'main_departments'), 'c.department_id = d.id', array('b.unitname'))
                           ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		return $appraisalConfigData;
		      		
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
					else	
						$searchQuery .= " c.".$key." like '%".$val."%' AND ";
                           $searchArray[$key] = $val;
				}
				$searchQuery = rtrim($searchQuery," AND");					
			}
			
		$objName = 'appraisalconfig';
		
		$tableFields = array('action'=>'Action','unitname'=>'Business Unit','deptname' => 'Department',
                    'performance_app_flag'=>'Implementation','appraisal_mode'=>'Appraisal Mode','approval_selection'=>'Approver','appraisal_ratings'=>'Ratings');
		
		$tablecontent = $this->getAppraisalconfigData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
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
                            'performance_app_flag' => array(
                                'type' => 'select',
                                'filter_data' => array('' => 'All',1 => 'Business Unit wise', 0 => 'Department wise'),
                            ),
                            'appraisal_mode' => array(
                                'type' => 'select',
                                'filter_data' => array('' => 'All','Quarterly'=>'Quarterly', 'Half yearly'=>'Half yearly','Yearly'=>'Yearly'),
                            ),
                            'approval_selection' => array(
                                'type' => 'select',
                                'filter_data' => array('' => 'All',1 => 'HR', 2 => 'Manager')
                            ),
                            'appraisal_ratings' => array(
                                'type' => 'select',
                                'filter_data' => array('' => 'All',1 => '1-5', 2 => '1-10')
                            ),
                            
                        ),
                        
		);
		return $dataTmp;
	}
	
	public function getAppraisalConfigbyID($id)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('im'=>'main_pa_implementation'),array('im.*'))
					    ->where('im.isactive = 1 AND im.id='.$id.' ');
		return $this->fetchAll($select)->toArray();
	
	}
/**
 * 
 * Enter description here ...
 * @param unknown_type $data
 * @param unknown_type $where
 */	
	public function SaveorUpdateAppraisalConfigData($data, $where)
	{ //echo "coming here";
	  //echo "<pre>"; print_r($data); echo "</pre>"; die;
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_pa_implementation'); 
			return $id;
		}
		
	
	}
	
	public function getActiveServiceDepartments($bunitid,$deptid='')
	{
		$where = 'sc.isactive=1';
		if($bunitid != '' && $bunitid !='null')
			$where .= ' AND sc.businessunit_id = '.$bunitid.'';
		if($deptid !='' && $deptid !='null')
			$where .= ' AND sc.department_id = '.$deptid.'';
		
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$qry = "select sc.service_desk_id from main_pa_implementation sc where ".$where." ";
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
		$resultqry = "select sd.id,sd.service_desk_name from main_sd_depts sd where ".$resWhere." ";
		$result = $db->query($resultqry)->fetchAll();
		return $result;
	}
	
	public function checkuniqueAppraisalConfigData($bunitid,$paflag,$deptid='')
	{
		$where = 'im.isactive=1 AND im.businessunit_id ='.$bunitid.' AND im.performance_app_flag ='.$paflag.'';
		if($deptid !='' && $deptid !='null')
			$where .= ' AND im.department_id = '.$deptid.'';
			
	   	$db = Zend_Db_Table::getDefaultAdapter();
		$qry = "select count(*) as count from main_pa_implementation im where ".$where." ";
		$res = $db->query($qry)->fetchAll();	
		return $res;	
		
	}
	
	/**
	 * 
	 * Here we are checking if any pending request is there for a business unit
	 * @param integer $bunitid = Id of business unit.
	 */
	public function getPendingAppraisalConfigData($bunitid)
	{
		$where = 'im.isactive=1 AND im.businessunit_id ='.$bunitid.' and pi.isactive=1 ';
			
	   	$db = Zend_Db_Table::getDefaultAdapter();
		$qry = "select count(*) as count from main_pa_implementation im
				left join main_pa_initialization pi on pi.businessunit_id = im.id 
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
	
	public function getDepartments($id,$dept_list)
	{
		$str = '';
		if($id != '')
            {
           if($dept_list!='')
           $str = "AND d.id not in($dept_list)";
            	
	  $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('d'=>'main_departments'),array('d.id','d.deptname'))
					    ->where('d.unitid = '.$id.' AND d.isactive = 1 '.$str)
						->order('d.deptname');
	//echo $select;exit;					
		return $this->fetchAll($select)->toArray();
            }
            else 
                return array();
	}
	
	public function getExistDepartments($id)
	{
		if($id != '')
           {
	  $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('d'=>'main_pa_implementation'),array('d.businessunit_id','d.department_id'))
					    ->where('d.businessunit_id = '.$id.' AND d.isactive = 1');
						
	//echo $select;exit;					
		return $this->fetchAll($select)->toArray();
            }
            else 
                return array();
	}
	
	
	
}