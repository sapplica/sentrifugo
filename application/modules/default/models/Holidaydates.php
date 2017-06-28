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

class Default_Model_Holidaydates extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_holidaydates';
    protected $_primary = 'id';
	
	public function getHolidayDatesData($sort, $by, $pageNo, $perPage,$searchQuery,$groupid='')
	{
		$where = "h.isactive = 1 AND hg.isactive=1";
		if($groupid) 
		$where .= " AND h.groupid = ".$groupid." AND h.holidayyear between year(now())-1 and year(now())+1 ";
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$groupData = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('h'=>'main_holidaydates'),array('h.*','holidaydate'=>'DATE_FORMAT(holidaydate,"'.DATEFORMAT_MYSQL.'")'))
						   ->joinLeft(array('hg'=>'main_holidaygroups'), 'hg.id=h.groupid',array('hg.groupname'))						   
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		//echo 	$groupData ; exit;
		return $groupData;       		
	}
	
	
	/* This function is common to manage holiday dates, My holiday calender , Employee holidays
       Here differentiation is done based on objname. 
    */
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$objName,$unitId='',$userid='',$conText='')
	{
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		}  	
        $searchQuery = '';
        $searchArray = array();
        $data = array();
		$dataTmp = array();
		
		if($objName == 'holidaydates')
		{
				if($searchData != '' && $searchData!='undefined')
					{
						$searchValues = json_decode($searchData);
						foreach($searchValues as $key => $val)
						{
							if($key == "description")
								$searchQuery .= " h.".$key." like '%".$val."%' AND ";
							else if($key == 'holidaydate')
								$searchQuery .= "  ".$key." like '%".  sapp_Global::change_date($val,'database')."%' AND ";
							else
								$searchQuery .= " ".$key." like '%".$val."%' AND ";
							$searchArray[$key] = $val;
						}
						$searchQuery = rtrim($searchQuery," AND");					
					}
				
				$tableFields = array('action'=>'Action','holidayname' => 'Holiday','groupname' => 'Holiday Group','holidaydate' => 'Date','description' => 'Description');
				$tablecontent = $this->getHolidayDatesData($sort, $by, $pageNo, $perPage,$searchQuery,$unitId);     
				
				if(isset($unitId) && $unitId != '') 
					$formgrid = 'true'; 
				else 
				   $formgrid = '';
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
					'formgrid' => $formgrid,
					'unitId'=>$unitId,
					'menuName'=>'Manage Holidays',
					'call'=>$call,
					'dashboardcall'=>$dashboardcall,
								'search_filters' => array(
									'holidaydate' => array('type' => 'datepicker'),
								),
				);
        }	
        else if($objName == 'myholidaycalendar')
		{
		        if($unitId == '' )
				{
					$employeesModel = new Default_Model_Employees();
					$empholidaygroup = $employeesModel->getHolidayGroupForEmployee($loginUserId);  
					if(isset($empholidaygroup[0]['holiday_group']) && $empholidaygroup[0]['holiday_group'] != '')
					{
					   $unitId = $empholidaygroup[0]['holiday_group'];
					}
				}	   
                
				   if($searchData != '' && $searchData!='undefined')
					{
						$searchValues = json_decode($searchData);
						foreach($searchValues as $key => $val)
						{
						    if($key == "description")
						      $searchQuery .= " h.".$key." like '%".$val."%' AND ";
							else if($key == 'holidaydate')
								$searchQuery .= "  ".$key." like '%".  sapp_Global::change_date($val,'database')."%' AND ";  
							else  
							  $searchQuery .= " ".$key." like '%".$val."%' AND ";
							$searchArray[$key] = $val;
						}
						$searchQuery = rtrim($searchQuery," AND");					
					}
					
									
				
				$tableFields = array('action'=>'Action','holidayname' => 'Holiday','holidaydate' => 'Date','description' => 'Description');
				$tablecontent = $this->getHolidayDatesData($sort, $by, $pageNo, $perPage,$searchQuery,$unitId);     
				
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
					'menuName'=>'My Holiday Calendar',
					'add' =>'add',
					'call'=>$call,
					'dashboardcall'=>$dashboardcall,
					'search_filters' => array(
									'holidaydate' => array('type' => 'datepicker'),
								),
				);
        }else if($objName == 'empholidays')
        {
		    $groupname = '';
		        if($searchData != '' && $searchData!='undefined')
					{
						$searchValues = json_decode($searchData);
						foreach($searchValues as $key => $val)
						{
							if($key == 'holidaydate')
							{
								$searchQuery .= " ".$key." like '%".  sapp_Global::change_date($val,'database')."%' AND ";
							}else if($key == "description")
								$searchQuery .= " h.".$key." like '%".$val."%' AND ";
							else
								$searchQuery .= " ".$key." like '%".$val."%' AND ";
							$searchArray[$key] = $val;
						}
						$searchQuery = rtrim($searchQuery," AND");					
					}
		
			
			$tableFields = array('action'=>'Action','groupname' =>'Holiday Group','holidayname' => 'Holiday','holidaydate' => 'Date','description' => 'Description');
			$tablecontent = $this->getHolidayDatesData($sort, $by, $pageNo, $perPage,$searchQuery,$unitId);	
			
            $holidaygroupModel = new Default_Model_Holidaygroups(); 
            $groupnameArr = $holidaygroupModel->getParticularGroupData($unitId);
			if(!empty($groupnameArr))
			  $groupname = $groupnameArr[0]['groupname'].' - Holidays';
			else
              $groupname = 'Holidays'; 			
            
			$dataTmp = array('userid'=>$userid, 
							'sort' => $sort,
							'by' => $by,
							'pageNo' => $pageNo,
							'perPage' => $perPage,				
							'tablecontent' => $tablecontent,
							'objectname' => $objName,
							'extra' => array(),
							'tableheader' => $tableFields,
							'jsGridFnName' => 'getEmployeeAjaxgridData',
							'jsFillFnName' => '',
							'searchArray' => $searchArray,
							'add'=>'add',
							
							'menuName'=> $groupname,
							'formgrid'=>'true',
							'unitId'=>$userid,
							'dashboardcall'=>$dashboardcall,
							'call'=>$call,
							'context'=>$conText,
							'search_filters' => array(
										'holidaydate' =>array('type'=>'datepicker')										
										)	
					);

        }   		
		return $dataTmp;
	}
	
	public function getsingleHolidayDatesData($id)
	{
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select * from main_holidaydates where id = ".$id." AND isactive = 1";
		$result = $db->query($query)->fetch();
		return $result;
	}
	
	public function SaveorUpdateHolidayDates($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_holidaydates');
			return $id;
		}
		
	
	}
	
	public function getParticularHolidayDateData($id)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('h'=>'main_holidaydates'),array('h.*'))
					    ->where('h.isactive = 1 AND h.id='.$id.' ');
	                 
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function getTotalGroupDataWithId($id)
	{
	    $select = $this->select()
	                   
						->setIntegrityCheck(false)
						->from(array('d'=>'main_holidaydates'),array('count'=>'count(*)'))
					    ->where('d.groupid = '.$id.' AND d.isactive = 1 '); //AND d.holidayyear = year(now())
	                   
		return $this->fetchAll($select)->toArray();
	
	}
	//To get all holidays as list....
	public function getHolidayDatesList()
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('h'=>'main_holidaydates'),array('h.holidaydate'))
					    ->where('h.isactive = 1');
	                 
		$holidayDates = $this->fetchAll($select)->toArray();
		
		return $holidayDates;
	
	}
	
	public function getHolidayDatesListForGroup($groupid)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('h'=>'main_holidaydates'),array('h.holidaydate','h.holidayname'))
					    ->where('h.groupid = '.$groupid.' AND h.isactive = 1');
		$holidayDates = $this->fetchAll($select)->toArray();
		
		return $holidayDates;
	
	}
	
	public function getDuplicateHolidayName($holidayname,$groupid,$holidaydate)
	{
	   $db = Zend_Db_Table::getDefaultAdapter();
	   $rows = $db->query("select count(*) as count from main_holidaydates where lower(holidayname) = lower('".$holidayname."') and groupid = ".$groupid." and holidaydate = '".$holidaydate."' and isactive = 1 ");
	   return $rows->fetchAll();	
	}
	public function checkholidayname($holidayname,$groupid,$id='',$holidayyear ='')
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		if($id == '')		
		$rows = $db->query("select count(*) as count from main_holidaydates where lower(holidayname) = lower('".$holidayname."') and groupid = ".$groupid." and holidayyear=".$holidayyear." and isactive = 1 ");
		else
		$rows = $db->query("select count(*) as count from main_holidaydates where lower(holidayname) = lower('".$holidayname."') and groupid = ".$groupid." and holidayyear=".$holidayyear." and isactive = 1 and id != ".$id);
		
		return $rows->fetchAll();	
	}
	
}