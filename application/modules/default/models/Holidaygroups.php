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

class Default_Model_Holidaygroups extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_holidaygroups';
    protected $_primary = 'id';
	
	public function getGroupData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "isactive = 1";
		
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$groupData = $this->select()
    					   ->setIntegrityCheck(false)	   
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $groupData;       		
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
		$objName = 'holidaygroups';
		$tableFields = array('action'=>'Action','groupname' => 'Group Name','description' => 'Description');
		$tablecontent = $this->getGroupData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
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
			'call'=>$call
		);		
		return $dataTmp;
	}
	public function getsingleGroupData($id)
	{
		$row = $this->fetchRow("id = '".$id."'");
		if (!$row) {
			throw new Exception("Could not find row $id");
		}
		return $row->toArray();
	}
	
	public function SaveorUpdateGroupData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_holidaygroups');
			return $id;
		}
		
	
	}
	
	public function getAllGroupData()
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('h'=>'main_holidaygroups'),array('h.id','h.groupname'))
					    ->where('h.isactive = 1')
						->order('h.groupname');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function getParticularGroupData($id)
	{
            if($id != '')
            {
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('h'=>'main_holidaygroups'),array('h.*'))
					    ->where('h.isactive = 1 AND h.id='.$id.' ');
		return $this->fetchAll($select)->toArray();
            }
            else 
                return array();
	
	}
	
	public function getEmpNamesForGroupId($groupid,$pageNo,$perPage)
	{
		if($pageNo != 0)
		$limitpage = (($pageNo-1)*$perPage);
		else
		$limitpage = 0;
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select e.user_id,u.userfullname,h.groupname from main_employees e 
		          left join main_holidaygroups h on h.id=e.holiday_group
				  left join main_users u on u.id=e.user_id 
				  where e.isactive=1 and u.userstatus = 'old' 
				  and u.emptemplock = 0 and e.isactive=1 and e.holiday_group = ".$groupid." 
				  group by e.user_id  LIMIT ".$limitpage.", ".$perPage;
		$result = $db->query($query)->fetchAll();
	    return $result;
	}
	
	public function getEmpCountForGroups($groupid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select count(distinct e.id) as empcount
		          from main_holidaygroups as g 
				  left join main_holidaydates as h on h.groupid = g.id and h.isactive=1
                  left join main_employees as e on e.holiday_group = g.id and e.isactive=1
                  where g.isactive =1 and h.holidayyear = year(now()) and g.id= ".$groupid." group by g.id ";
		
		$result = $db->query($query)->fetchAll();
	    return $result;
	}
	
	public function getHolidayNamesForGroupId($groupid,$pageNo,$perPage)
	{
		if($pageNo != 0)
		$limitpage = (($pageNo-1)*$perPage);
		else
		$limitpage = 0;
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select hd.holidayname,DATE_FORMAT(hd.holidaydate,'".DATEFORMAT_MYSQL."') as holidaydate,hd.holidayyear,h.groupname from main_holidaydates hd 
		          left join main_holidaygroups h on hd.groupid=h.id
				  where hd.isactive=1 and hd.holidayyear = year(now()) 
				  and h.isactive = 1 and hd.groupid = ".$groupid." 
				  group by hd.id  LIMIT ".$limitpage.", ".$perPage;
		$result = $db->query($query)->fetchAll();
	    return $result;
	}
	
	public function getHolidayCountForGroups($groupid)
	{
	   $db = Zend_Db_Table::getDefaultAdapter();
		$query = "select count(*) as count
		          from main_holidaygroups as g 
                  left join main_holidaydates as h on h.groupid = g.id and h.isactive=1
				  where g.isactive =1 and g.id = ".$groupid." and h.holidayyear = year(now()) group by g.id ";
		
		$result = $db->query($query)->fetchAll();

		return $result;
	}
	
	public function checkDuplicateGroupName($groupName)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$qry = "select count(*) as count from main_holidaygroups h where h.groupname='".$groupName."' AND h.isactive=1 ";
		$res = $db->query($qry)->fetchAll();
		return $res;
	}
}