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

class Default_Model_Departments extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_departments';
    protected $_primary = 'id';
	
	public function getDepartmentsData($sort, $by, $pageNo, $perPage,$searchQuery,$unitid)
	{
		$where = "d.isactive = 1 and b.isactive = 1 ";
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		if($unitid)
			$where .= ' AND d.unitid='.$unitid.' ';
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$departmentsdata = $this->select()
    					   ->setIntegrityCheck(false)	
						   ->from(array('d' => 'main_departments'),array('id'=>'d.id','isactive'=>'d.isactive','deptcode'=>'d.deptcode','deptname'=>'d.deptname','startdate'=>'DATE_FORMAT(d.startdate,"'.DATEFORMAT_MYSQL.'")'))
						   ->joinLeft(array('u'=>'main_users'),'d.depthead=u.id',array('depthead'=>'u.userfullname'))
						   ->joinLeft(array('c'=>'main_cities'),'d.city=c.city_org_id',array('address'=>'concat(d.address1,", ",c.city)'))
						   ->joinLeft(array('b'=>'main_businessunits'), 'd.unitid=b.id', array('unitname' => 'if(b.unitname="No Business Unit","",b.unitname)'))
						   ->joinLeft(array('tz'=>'main_timezone'), 'd.timezone=tz.id and tz.isactive=1', array('timezone' => 'concat(tz.timezone," [",tz.timezone_abbr,"]")'))
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $departmentsdata;       		
	}
	
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$unitId,$a='',$b='',$c='')
	{
		$searchQuery = '';
        $searchArray = array();
        $data = array();
		
		if($searchData != '' && $searchData!='undefined')
		{	
			$searchValues = json_decode($searchData);
			foreach($searchValues as $key => $val)
			{
				if($key == 'timezone')
				$searchQuery .= " tz.".$key." like '%".$val."%' AND ";
				else if($key == 'unitname')
				$searchQuery .= " b.".$key." like '%".$val."%' AND ";
				else if($key == 'depthead')
				$searchQuery .= " u.userfullname like '%".$val."%' AND ";
				else if($key == 'startdate')
				{
					$searchQuery .= " d.".$key." like '%".  sapp_Global::change_date($val,'database')."%' AND ";
				}
				else $searchQuery .= " d.".$key." like '%".$val."%' AND ";
				$searchArray[$key] = $val;
			}
			$searchQuery = rtrim($searchQuery," AND");		
		}
		$objName = 'departments';
		$tableFields = array('action'=>'Action','deptname' => 'Name','deptcode' =>'Code','startdate'=>'Started On','depthead'=>'Department Head','timezone'=>'Time Zone','unitname'=>'Business Unit');
		
		$tablecontent = $this->getDepartmentsData($sort, $by, $pageNo, $perPage,$searchQuery,$unitId);     
		if(isset($unitId) && $unitId != '') $formgrid = 'true'; else $formgrid = '';
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
			'formgrid' => $formgrid,
			'unitId'=>$unitId,
			'dashboardcall'=>$dashboardcall,
			'call'=>$call,
			'search_filters' => array(
					'startdate' =>array('type'=>'datepicker')					
				)
		);		
		return $dataTmp;
	}
	
	public function getSingleDepartmentData($id)
	{
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$deptData = $db->query("select * from main_departments where isactive = 1 AND id = ".$id);
	    $result= $deptData->fetch();
		return $result;
	}
	public function getdepts_interview_report()
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select d.id dept_id,concat(d.deptname,' - ',b.unitname) dept_name 
                      from main_departments d 
                      inner join main_businessunits b on b.id = d.unitid 
                      where d.isactive = 1 group by dept_id order by d.deptname";
            $result = $db->query($query);
            $rows = $result->fetchAll();
            return $rows;
        }
	public function getAllDeptsForUnit($unitid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$deptData = $db->query("select * from main_departments where isactive = 1 AND unitid = ".$unitid." order by deptname");
		$result= $deptData->fetchAll();
		return $result;
		  
	}
	public function SaveorUpdateDepartmentsUnits($data, $where)
	{
		if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_departments');
			return $id;
		}
	}
	
	public function getDepartmentList($bussinessunitid)
	{
            if($bussinessunitid != '')
            {
	  $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('d'=>'main_departments'),array('d.id','d.deptname'))
					    ->where('d.unitid = '.$bussinessunitid.' AND d.isactive = 1')
						->order('d.deptname');
	
		return $this->fetchAll($select)->toArray();
            }
            else 
                return array();
	
	}
	
	public function checkCodeDuplicates($code,$id)
	{
		if($id) $row = $this->fetchRow("deptcode = '".$code."' AND id <> ".$id.' AND isactive=1'); 
		else	$row = $this->fetchRow("deptcode = '".$code."' AND isactive=1");
		if(!$row){
			return false;
		}else{
		return true;
		}
	}
	
	public function getTotalDepartmentList()
	{
	  $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('d'=>'main_departments'),array('d.id','d.deptname'))
					    ->where('d.isactive = 1')
						->order('d.deptname');
	
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function getParicularDepartmentId($id)
	{
	  $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('d'=>'main_departments'),array('d.id','d.deptname'))
					    ->where('d.id = '.$id.' AND d.isactive = 1');
	
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function getUniqueDepartmentList($deptidarr,$businessunitid)
	{
	    $where = '';
	    if($deptidarr !='')
		{
		  $where = "and d.id NOT IN(".$deptidarr.") ";
		}
	    $db = Zend_Db_Table::getDefaultAdapter();
        
        $query = "select d.id,d.deptname from main_departments d where d.isactive = 1 and d.unitid =".$businessunitid."  $where ";
        $result = $db->query($query)->fetchAll();
	    return $result;
		
	}
	
	public function getUniqueDepartments($querystring)
	{
	 $db = Zend_Db_Table::getDefaultAdapter();
	 $query = "$querystring";
	 $result = $db->query($query)->fetchAll();
	 return $result;
	}
	
	public function checkExistance($deptname, $unitid,$id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
        if(!empty($id)) $where = "and id <> ".$id; else $where= '';
		$query = "select count(*) as count from main_departments where isactive = 1 and unitid=".$unitid." and deptname = '".$deptname."' ".$where;
        $result = $db->query($query)->fetchAll();
	    return $result[0]['count'];
	}
	
	public function checkemployeestodepartment($deptid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select count(*) as count from main_employees where department_id = ".$deptid." AND isactive = 1";
		$result = $db->query($query)->fetch();
	    return $result['count'];
	}
	
	public function getbusinessunitname($unitid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select unitname as unitname from main_businessunits where id = ".$unitid."";
		$result = $db->query($query)->fetch();
	    return $result['unitname'];
	}
	//this method is used while creating department head also
	public function getEmpdepartmentdetails($deptId)
	{
		if($deptId != '')
		{
			$empDept_arr=array();
			$db = Zend_Db_Table::getDefaultAdapter();
			$query = "select country,state,city,address1,address2,address3,unitid from main_departments where id =".$deptId." and isactive=1;";
			$result = $db->query($query);
			$empDept_arr = $result->fetchAll();
			return $empDept_arr;
		}
		else 
			return array();
	}
	
	public function getDepartmentNameFromDeptString($departmentIdArr)
	{
	    if(!empty($departmentIdArr))
		{
	     $a = '-';
		 $departmentArray = array();
	     $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('d'=>'main_departments'),array('d.id','deptname'=>'concat(d.deptname," (",d.deptcode,")")'))
						->joinLeft(array('b'=>'main_businessunits'), 'b.id=d.unitid',array('unitcode'=>'if(b.unitcode != "000",concat(b.unitcode,"","'.$a.'"),"")'))
						
	                    ->where('d.id IN(?)',$departmentIdArr);
          
		 $result =  $this->fetchAll($select)->toArray();
		   if(!empty($result))
			{
				foreach($result as $val)
				{
					$departmentArray[$val['id']]= $val['deptname'];
				}
			}
			return $departmentArray;
	    }
	
	}
	
	public function getDepartmentWithCodeList()
	{
	 $a = '-';
	  $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('d'=>'main_departments'),array('d.id','deptname'=>'concat(d.deptname," (",d.deptcode,")")'))
						->joinLeft(array('b'=>'main_businessunits'), 'b.id=d.unitid',array('unitcode'=>'if(b.unitcode != "000",concat(b.unitcode,"","'.$a.'"),"")'))
					    ->where('d.isactive = 1')
						->order('d.deptname');
	
		return $this->fetchAll($select)->toArray();
	
	}
        
        public function getDepartmentWithCodeList_bu($bu_id)
	{
            if($bu_id != '')
            {
                $a = '-';
                $select = $this->select()
                            ->setIntegrityCheck(false)
                            ->from(array('d'=>'main_departments'),array('d.id','deptname'=>'concat(d.deptname," (",d.deptcode,")")'))
                            ->joinLeft(array('b'=>'main_businessunits'), 'b.id=d.unitid',array('unitcode'=>'if(b.unitcode != "000",concat(b.unitcode,"","'.$a.'"),"")'))
                            ->where('d.isactive = 1 and d.unitid in ('.$bu_id.')')
                            ->order('d.deptname');

                return $this->fetchAll($select)->toArray();	
            }
            else 
                return array();
	}
	
	public function getEmpForDepartment($deptid,$pageNo,$perPage)
	{
		if($pageNo != 0)
		$limitpage = (($pageNo-1)*$perPage);
		else
		$limitpage = 0;
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select * from main_employees_summary where department_id = ".$deptid." LIMIT ".$limitpage.", ".$perPage;
		$result = $db->query($query)->fetchAll();
		return $result;
	}
	
	public function getEmpCountForDepartment($deptid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select * from main_employees_summary where department_id = ".$deptid." ";
		$result = $db->query($query)->fetchAll();
		return count($result);
	}
	
	public function getDeptHeads()
	{
		$managementUsers = array();
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "SELECT id FROM main_roles WHERE isactive = 1 AND group_id = ".MANAGEMENT_GROUP;
		$roles = $db->query($query)->fetchAll();
		
		$roleids = implode(', ', array_map(function ($entry) {
								return $entry['id'];
					}, $roles));
		if($roleids != '')
		{
			$usersquery = "SELECT id,userfullname FROM main_users WHERE isactive = 1 AND emprole in (".$roleids.") AND userstatus = 'old' order by userfullname ASC";
			$managementUsers = $db->query($usersquery)->fetchAll();
		}
		return $managementUsers;
	}
	
	public function getDepartmenttHead($id)
	{
		$managementUsers = array();
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select user_id,userfullname from main_employees_summary 
				  where isactive=1";
		$managementUsers = $db->query($query)->fetchAll();
		
		
		return $managementUsers;
	}
	public function checkDuplicateDeptName($unitName)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$qry = "select count(*) as count from main_departments b where b.deptname='".$unitName."' AND b.isactive=1 ";
		$res = $db->query($qry)->fetchAll();
		return $res;
	}
}