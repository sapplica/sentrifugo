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

class Default_Model_Reports extends Zend_Db_Table_Abstract
{
	protected $_name = 'main_leaverequest';
	
	public function getEmpLeaveHistory($sort, $by,$pageNo, $perPage,$searchQuery='')
	{
		
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$where = 'l.isactive = 1 ';
		if($sort == 'l.leavestatus'){
		
		$sort = 'CAST(l.leavestatus AS CHAR)';
		}
		if($searchQuery !='')
		$where .= 'AND '.$searchQuery;
                
		$leaveStatusData = $this->select()
		->setIntegrityCheck(false)
		->from(array('l'=>'main_leaverequest_summary'),
		array( 'l.*','from_date'=>'DATE_FORMAT(l.from_date,"'.DATEFORMAT_MYSQL.'")',
								         'to_date'=>'DATE_FORMAT(l.to_date,"'.DATEFORMAT_MYSQL.'")',
										 'applieddate'=>'DATE_FORMAT(l.createddate,"'.DATEFORMAT_MYSQL.'")',
                                         'leaveday'=>'if(l.leaveday = 1,"Full Day","Half Day")', 										 
		))
		
		->where($where)
		->order("$sort $by ")
		->limitPage($pageNo, $perPage);
		
		
		return $this->fetchAll($leaveStatusData)->toArray();

	}

	public function getEmpLeaveHistoryCount($searchQuery=''){
		$where = 'l.isactive = 1 ';
		if($searchQuery !='')
		$where .= 'AND '.$searchQuery;
			
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('l'=>'main_leaverequest_summary'), array('count'=>'COUNT(l.id)'))
		
		->where($where);
			
		return $this->fetchAll($select)->toArray();

	}
	
	public function getLeaveManagementSummary($sort, $by,$pageNo, $perPage,$searchQuery='')
	{
	    
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$where = 'l.isactive = 1 ';
		if($searchQuery !='')
		$where .= 'AND '.$searchQuery;
		
     	$leaveManagementData = 	$this->select()
								->setIntegrityCheck(false)
								->from(array('l'=>'main_leavemanagement_summary'),
								array( 'l.*',
									   'halfday'=>'if(l.is_halfday = 1,"Yes","No")',
									   'leavetransfer'=>'if(l.is_leavetransfer = 1,"Yes","No")',
									   'skipholidays'=>'if(l.is_skipholidays = 1,"Yes","No")', 			   
								))
								->where($where)
								->order("$sort $by ")
								->limitPage($pageNo, $perPage);
		
		return $this->fetchAll($leaveManagementData)->toArray();
	}
	
	public function get_sd_report($param_arr, $per_page, $page_no, $sort_name, $sort_type,$request_for='')
    {
    	$search_str = 'r.isactive = 1';
    	if($request_for!='')
    		$search_str .=' AND r.request_for='.$request_for.'';
    	//echo '<pre>';
    	//print_r($param_arr);
    	//echo "REQ".$request_for;//exit;
		    	
        foreach($param_arr as $key => $value)
        {
        	if($value != '')
            {
            	if($key == 'raised_date')
                	$search_str .= " and DATE(createddate) = '".sapp_Global::change_date($value,'database')."'";
                else if($key=='service_desk_id') {
                	if($request_for==1) {
                		$search_str .= " and r.service_desk_id = '".$value."'";
                	}	
                	else { 	
                		$search_str .= " and ac.category = '".$value."'";
                	}	
                }
             	else if($key=='service_request_id') {
                	if($request_for==1) {
                		$search_str .= " and r.service_request_id = '".$value."'";
                	}	
                	else { 	
                		$search_str .= " and ac.id = '".$value."'";
                	}	
                }						
               	else
                	$search_str .= " and ".$key." = '".$value."'";
       		}
		}
     	$offset = ($per_page*$page_no) - $per_page;
        $db = Zend_Db_Table::getDefaultAdapter();
        $limit_str = " limit ".$per_page." offset ".$offset;
        $count_query = "select count(*) cnt from main_sd_requests_summary r 
        				left join assets ac ON r.service_desk_id = ac.id and ac.isactive=1 
               			left join assets_categories a ON r.service_request_id = a.id AND a.is_active=1 and a.parent=0
               			where ".$search_str;
        $count_result = $db->query($count_query);
        $count_row = $count_result->fetch();
        $count = $count_row['cnt'];
        $page_cnt = ceil($count/$per_page);
            
       	$query = "select if(r.request_for=1,r.service_desk_name,a.name) as service_desk_name,if(r.request_for=1,r.service_request_name,ac.name) as service_request_name,r.id,
         r.request_for,r.sd_requests_id,r.service_desk_id,r.service_desk_conf_id,r.service_request_id,r.priority,r.description,r.attachment,r.status,r.raised_by,r.raised_by_name,
         r.raised_by_empid,r.ticket_number,r.executor_id,r.executor_name,r.executor_comments,r.reporting_manager_id,r.reporting_manager_name,r.approver_status_1,r.approver_status_2,
         r.approver_status_3,r.reporting_manager_status,r.approver_1,r.approver_1_name,r.approver_2,r.approver_2_name,r.approver_3,r.approver_1_comments,r.approver_2_comments,
         r.approver_3_comments,r.reporting_manager_comments,r.to_mgmt_comments,r.to_manager_comments,r.approver_3_name,r.isactive,r.createdby,r.modifiedby,r.createddate,r.modifieddate,     		
         	  DATE_FORMAT(r.createddate,'".DATEFORMAT_MYSQL."') as createddate from main_sd_requests_summary r 
        		 	 left join assets ac ON r.service_desk_id = ac.id and ac.isactive=1 
               		left join assets_categories a ON r.service_request_id = a.id AND a.is_active=1 and a.parent=0 	
        			where ".$search_str." order by ".$sort_name." ".$sort_type." ".$limit_str;
       //exit;
        $result = $db->query($query);
        $rows = $result->fetchAll();
        return array('rows' => $rows,'page_cnt' => $page_cnt);
	}
	
	public function getLeaveManagementCount($searchQuery='')
	{
		$where = 'l.isactive = 1 ';
		if($searchQuery !='')
		$where .= 'AND '.$searchQuery;
			
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('l'=>'main_leavemanagement_summary'), array('count'=>'COUNT(l.id)'))
		->where($where);
			
		return $this->fetchAll($select)->toArray();
	}

	public function getEmpLeaveNamesByIds($empleavetypesArray)
	{
		if(!empty($empleavetypesArray))
		{
			$empLeavetypeArray = array();
			$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('et'=>'main_employeeleavetypes'),array('et.id','leavetype'=>'et.leavetype'))
			->where('et.id IN(?)',$empleavetypesArray);

			$result =  $this->fetchAll($select)->toArray();
		 
			if(!empty($result))
			{
				foreach($result as $val)
				{
					$empLeavetypeArray[$val['id']]= $val['leavetype'];
				}
			}
			return $empLeavetypeArray;
		}
		
	}

	public function getUserNamesByIds($usersArray)
	{
		if(!empty($usersArray))
		{
			$usernameArray = array();
			$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('mu'=>'main_users'),array('mu.id','employeename'=>'mu.userfullname'))
			->where('mu.id IN(?)',$usersArray);

			$result =  $this->fetchAll($select)->toArray();
		 
			if(!empty($result))
			{
				foreach($result as $val)
				{
					$usernameArray[$val['id']]= $val['employeename'];
				}
			}
			return $usernameArray;
		}
		
	}

	public function getRepManagerNamesByIds($repmanagerArray)
	{
		if(!empty($repmanagerArray))
		{
			$repmanagernameArray = array();
			$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('u'=>'main_users'),array('u.id','reportingmanagername'=>'u.userfullname'))
			->where('u.id IN(?)',$repmanagerArray);

			$result =  $this->fetchAll($select)->toArray();
		 
			if(!empty($result))
			{
				foreach($result as $val)
				{
					$repmanagernameArray[$val['id']]= $val['reportingmanagername'];
				}
			}
			return $repmanagernameArray;
		}
		
	}

	

	public function getBusinessUnitsInfo($sortby,$by,$pageNo,$perPage,$searchQuery,$funorder='')
	{
		if($searchQuery != '')
		$where = " WHERE ".$searchQuery;
		else $where = ' WHERE 1=1 ';
		$where .= ' AND b.id <> 0';

		if($pageNo != 0)
		$limitpage = (($pageNo-1)*$perPage);
		else
		$limitpage = 0;

		$db = Zend_Db_Table::getDefaultAdapter();
		if($funorder != '')
		{
			$query = "SELECT b.*,COUNT(d.id) AS deptcount,
						if(b.isactive = 0,'Inactive','Active') as status,
						DATE_FORMAT(b.startdate,'".DATEFORMAT_MYSQL."') as startdate					
						FROM main_businessunits b
						LEFT JOIN main_departments d ON b.id = d.unitid 					
						".$where." GROUP BY b.id ORDER BY ".$funorder." LIMIT ".$limitpage.", ".$perPage;
		}
		else
		{
			$query = "SELECT b.*,COUNT(d.id) AS deptcount,
						if(b.isactive = 0,'Inactive','Active') as status,
						DATE_FORMAT(b.startdate,'".DATEFORMAT_MYSQL."') as startdate					
						FROM main_businessunits b
						LEFT JOIN main_departments d ON b.id = d.unitid 					
						".$where." GROUP BY b.id ORDER BY ".$sortby." ".$by." LIMIT ".$limitpage.", ".$perPage;
		}
		
		$result = $db->query($query)->fetchAll();
		return $result;
	}

	public function getBusinessUnitsCount($searchQuery)
	{
		if($searchQuery != '')
		$where = " WHERE ".$searchQuery;
		else $where = ' WHERE 1=1 ';
		$where .= ' AND b.id <> 0';

		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "SELECT count(*) AS count
					FROM main_businessunits b
					LEFT JOIN main_departments d ON b.id = d.unitid 					
					".$where." GROUP BY b.id";	
		
		$result = $db->query($query)->fetchAll();
		return count($result);
	}

	public function getDepartmentsInfo($sortby,$by,$pageNo,$perPage,$searchQuery,$funorder='')
	{
		if($searchQuery != '')
		$where = " WHERE ".$searchQuery;
		else $where = ' WHERE 1=1 ';

		if($pageNo != 0)
		$limitpage = (($pageNo-1)*$perPage);
		else
		$limitpage = 0;

		$db = Zend_Db_Table::getDefaultAdapter();
		if($funorder != '')
		{
			$query = "SELECT d.*,count(e.id) AS empcount,
						IF(d.isactive = 0,'Inactive','Active') AS status,
						DATE_FORMAT(d.startdate,'".DATEFORMAT_MYSQL."') AS startdate
						FROM main_departments d
						LEFT JOIN main_employees e ON d.id = e.department_id
						".$where." GROUP BY d.id ORDER BY ".$funorder." LIMIT ".$limitpage.", ".$perPage;
		}
		else
		{
			$query = "SELECT d.*,count(e.id) AS empcount,
						IF(d.isactive = 0,'Inactive','Active') AS status,
						DATE_FORMAT(d.startdate,'".DATEFORMAT_MYSQL."') AS startdate
						FROM main_departments d
						LEFT JOIN main_employees e ON d.id = e.department_id
						".$where." GROUP BY d.id ORDER BY ".$sortby." ".$by." LIMIT ".$limitpage.", ".$perPage;
		}
		
		$result = $db->query($query)->fetchAll();
		return $result;
	}

	public function getDepartmentsCount($searchQuery)
	{
		if($searchQuery != '')
		$where = " WHERE ".$searchQuery;
		else $where = ' WHERE 1=1 ';

		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "SELECT count(*) AS count
					FROM main_departments d
					LEFT JOIN main_employees e ON d.id = e.department_id			
					".$where." GROUP BY d.id";	
		
		$result = $db->query($query)->fetchAll();
		return count($result);
	}



	

	public function getAutoReportBunit($search_str,$flag = '')
	{
		if($flag == '')
		$where = ' and b.id<>0 ';
		else $where = '';
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select b.id bid,b.unitname,b.unitcode,
					case 
						when b.unitname like '".$search_str."%' then 4  
						when b.unitname like '__".$search_str."%' then 2 
						when b.unitname like '_".$search_str."%' then 3 
						when b.unitname like '%".$search_str."%' then 1 
					else 0 end uname 
					from main_businessunits b
					where b.unitname like '%".$search_str."%'
					".$where." order by uname desc
					limit 0,10";		
		$result = $db->query($query);
		$res_arr = array();
		$res_arr = $result->fetchAll();
		return $res_arr;
	}

	public function getAutoReportBunitcode($search_str)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select b.id bid,b.unitcode,
					case 
						when b.unitcode like '".$search_str."%' then 4  
						when b.unitcode like '__".$search_str."%' then 2 
						when b.unitcode like '_".$search_str."%' then 3 
						when b.unitcode like '%".$search_str."%' then 1 
					else 0 end ucode 
					from main_businessunits b
					where b.unitcode like '%".$search_str."%'
					and b.id<>0 order by ucode desc
					limit 0,10";

		$result = $db->query($query);
		$res_arr = array();
		$res_arr = $result->fetchAll();
		return $res_arr;
	}

	public function getAutoReportDeptcode($search_str)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select d.id did,d.deptcode,
					case 
						when d.deptcode like '".$search_str."%' then 4  
						when d.deptcode like '__".$search_str."%' then 2 
						when d.deptcode like '_".$search_str."%' then 3 
						when d.deptcode like '%".$search_str."%' then 1 
					else 0 end dcode 
					from main_departments d
					where d.deptcode like '%".$search_str."%'
					and d.id<>0 order by dcode desc
					limit 0,10";

		$result = $db->query($query);
		$res_arr = array();
		$res_arr = $result->fetchAll();
		return $res_arr;
	}

	public function getAutoReportDept($search_str)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select d.id did,d.deptname,d.deptcode,b.unitcode,
					case 
						when d.deptname like '".$search_str."%' then 4  
						when d.deptname like '__".$search_str."%' then 2 
						when d.deptname like '_".$search_str."%' then 3 
						when d.deptname like '%".$search_str."%' then 1 
					else 0 end dname 
					from main_departments d
					left join main_businessunits b on b.id = d.unitid
					where d.deptname like '%".$search_str."%'
					and d.id<>0 order by dname desc
					limit 0,10";

		$result = $db->query($query);
		$res_arr = array();
		$res_arr = $result->fetchAll();
		return $res_arr;
	}

	public function getCityOrder($order)
	{
		$qry = "select city_org_id from main_cities order by city {$order}";
		$db = Zend_Db_Table::getDefaultAdapter();
		$sqlRes = $db->query($qry);
		$cityArr = $sqlRes->fetchAll();

		foreach($cityArr as $key => $cityid){
			$cityArray[] = $cityid['city_org_id'];

		}
		return $cityArray;
	}

	public function getStateOrder($order)
	{
		$qry = "select state_id_org from main_states order by state {$order}";
		$db = Zend_Db_Table::getDefaultAdapter();
		$sqlRes = $db->query($qry);
		$stateArr = $sqlRes->fetchAll();

		foreach($stateArr as $key => $stateid){
			$stateArray[] = $stateid['state_id_org'];

		}
		return $stateArray;
	}
	public function getCountryOrder($order)
	{
		$qry = "select country_id_org from main_countries order by country {$order}";
		$db = Zend_Db_Table::getDefaultAdapter();
		$sqlRes = $db->query($qry);
		$countryArr = $sqlRes->fetchAll();

		foreach($countryArr as $key => $countryid){
			$countryArray[] = $countryid['country_id_org'];

		}
		return $countryArray;
	}
	public function getEmpRoleOrder($order)
	{
		$emprole = "";
		$qry = "select id from main_roles order by rolename {$order}";
		$db = Zend_Db_Table::getDefaultAdapter();
		$sqlRes = $db->query($qry);
		$emprole = $sqlRes->fetchAll();

		$roleorderidArray = array();
		foreach($emprole as $key => $roleid){
			$roleorderidArray[] = $roleid['id'];

		}

		return $roleorderidArray;
	}
	public function getEmpgroupOrder($order)
	{
		$emprole = "";
		$qry = "select id from main_groups order by group_name {$order}";
		$db = Zend_Db_Table::getDefaultAdapter();
		$sqlRes = $db->query($qry);
		$empgroup = $sqlRes->fetchAll();

		$grouporderidArray = array();
		foreach($empgroup as $key => $groupid){
			$grouporderidArray[] = $groupid['id'];

		}

		return $grouporderidArray;
	}

	/**
	 * This function gives data for report.
	 * @parameters
	 * @sort          = ascending or descending
	 * @by            = name of field which to be sort
	 * @pageNo        = page number
	 * @perPage       = no.of records per page
	 * @searchQuery   = search string
	 *
	 * @return  ResultSet;
	 */
	public function getUserWiseLogData($by="logindatetime",$order="desc",$pageNo=1, $perPage=20,$searchQuery=1,$selectfield=array('*'))
	{
		$where = "1";
		$by = trim($by);

		if($searchQuery)
		$where .= " AND ".$searchQuery;

		if($by == ''){

			$select = $this->select()
			->setIntegrityCheck(false)
			->from(array($this->_name),$selectfield)
			->where($where)
			->order($order)
			->limitPage($pageNo, $perPage);
		}else{
			$select = $this->select()
			->setIntegrityCheck(false)
			->from(array($this->_name),$selectfield)
			->where($where)
			->order("$by $order")
			->limitPage($pageNo, $perPage);
		}

		
		$userLogRecords = $this->fetchAll($select)->toArray();
		

		return $userLogRecords;
	}

	public function getUserLogCount($searchQuery = 1)
	{
		$where = "1";

		if($searchQuery)
		$where .= " AND ".$searchQuery;

		$qry = "select count(*) as count from main_userloginlog where {$where}";
		$db = Zend_Db_Table::getDefaultAdapter();
		$sqlRes = $db->query($qry);
		$count = $sqlRes->fetchAll();
			
		return $count[0]['count'];
	}
	/**
	 ** requisition stats report.
	 **/
	public function getRequisitionStats()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select count(case when req_status = 'Initiated' then (id) end) as initiated_req, count(case when req_status = 'Approved' then (id) end) as approved_req, count(case when req_status = 'Rejected' then (id) end) as rejected_req,count(case when req_status = 'Closed' then (id) end) as closed_req,
count(case when req_status = 'On hold' then (id) end) as onhold_req, count(case when req_status = 'Complete' then (id) end) as complete_req, count(case when req_status = 'In process' then (id) end) as inprocess_req from main_requisition where isactive = 1 and year(createdon) = year(now());";
		$result = $db->query($query);
		$rows = $result->fetchAll();
		return $rows;
	}

	/**
	 ** user login stats report.
	 **/
	public function getUserloginStats()
	{
            $db = Zend_Db_Table::getDefaultAdapter();
            
            $query = "select count(ul.id) as cnt, day(nn.thedate) as logindate 
                      from main_userloginlog ul 
                      right join ( select DATE_FORMAT(NOW() ,'%Y-%m-01') + INTERVAL n DAY AS thedate 
                      from numbers n where DATE_FORMAT(NOW() ,'%Y-%m-01') + INTERVAL n DAY <= last_day(now())) nn 
                      on nn.thedate = date(ul.logindatetime)  group by day(nn.thedate); ";
            $result = $db->query($query);
            $rows = $result->fetchAll();
            return $rows;
	}

    /**
	 ** Ateration Status report.
	 **/
	public function getAterationStats()
	{
            $db = Zend_Db_Table::getDefaultAdapter();
            
            $query = "SELECT count(`id`) as cnt ,YEAR(`date_of_leaving`) as yeear from main_employees where YEAR( `date_of_leaving`) >= YEAR(DATE_SUB(CURDATE(), INTERVAL 4 YEAR))
 GROUP BY YEAR( `date_of_leaving`) ORDER BY YEAR( `date_of_leaving`) DESC;";
            $result = $db->query($query);
            $rows = $result->fetchAll();
            return $rows;
	}

	public function getBusinessunitOrder($order)
	{
		$qry = "select id from main_businessunits order by unitname {$order}";
		$db = Zend_Db_Table::getDefaultAdapter();
		$sqlRes = $db->query($qry);
		$BUArr = $sqlRes->fetchAll();

		foreach($BUArr as $key => $BU){
			$BUArray[] = $BU['id'];

		}
		return $BUArray;
	}


	public function getEmpHolidayResult($sortby,$by,$pageNo,$perPage)
	{
		if($pageNo != 0)
		$limitpage = (($pageNo-1)*$perPage);
		else
		$limitpage = 0;

		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select g.id,g.groupname,count(distinct h.id) as datecount,count(distinct e.id) as empcount
		          from main_holidaygroups as g 
                  left join main_holidaydates as h on h.groupid = g.id and h.isactive=1
                  left join main_employees as e on e.holiday_group = g.id and e.isactive=1
                  where g.isactive =1 and h.holidayyear = year(now()) group by g.id ORDER BY ".$sortby." ".$by." LIMIT ".$limitpage.", ".$perPage;
		
		$result = $db->query($query);
		$data = $result->fetchAll();

		return $data;

	}

	public function getEmpHolidayCount()
	{


		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select count(*) as count
		          from main_holidaygroups as g 
                  left join main_holidaydates as h on h.groupid = g.id and h.isactive=1
                  left join main_employees as e on e.holiday_group = g.id and e.isactive=1
                  where g.isactive =1 and h.holidayyear = year(now()) group by g.id ";
		
		$result = $db->query($query);
		$data = $result->rowCount();

		return $data;

	}

	/**
	 ** Employee stats report.
	 **/
	public function getEmpStats()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select count(case when isactive = 1 then id end) as 'active',count(case when isactive = 0 then id end) as 'inactive',count(case when isactive = 2 then id end) as 'resigned',count(case when isactive = 3 then id end) as 'left',count(case when isactive = 4 then id end) as 'suspended',count(case when isactive = 5 then id end) as 'deleted'
from main_users 
where emprole in (select id from main_roles where group_id <> ".USERS_GROUP.");";
		$res = $db->query($query);
		$empStats = $res->fetchAll();
		return $empStats;

	}

	/**
	 * This function gives data for report.
	 * @parameters
	 * @sort          = ascending or descending
	 * @by            = name of field which to be sort
	 * @pageNo        = page number
	 * @perPage       = no.of records per page
	 * @searchQuery   = search string
	 *
	 * @return  ResultSet;
	 */
	public function getAgencyListReportData($by="agencyname",$order="asc",$pageNo=1, $perPage=20,$searchQuery=1,$selectfield=array('*'))
	{
		$where = "isactive = 1";
		$by = trim($by);

		if($searchQuery)
		$where .= " AND ".$searchQuery;

		if($by == ''){

			$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('main_bgagencylist'),$selectfield)
			->where($where)
			->order($order)
			->limitPage($pageNo, $perPage);
		}
		else if($pageNo == ''){
			$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('main_bgagencylist'),$selectfield)
			->where($where);

		}else{
			$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('main_bgagencylist'),$selectfield)
			->where($where)
			->order("$by $order")
			->limitPage($pageNo, $perPage);
		}

		
		$agencyListReportRecords = $this->fetchAll($select)->toArray();
		

		return $agencyListReportRecords;
	}

	public function getAgencyListReportCount($searchQuery = 1)
	{
		$where = "isactive = 1";

		if($searchQuery)
		$where .= " AND ".$searchQuery;

		$qry = "select count(*) as count from main_bgagencylist where {$where}";
		$db = Zend_Db_Table::getDefaultAdapter();
		$sqlRes = $db->query($qry);
		$count = $sqlRes->fetchAll();
			
		return $count[0]['count'];
	}

	public function getBgCheckNamesByIds($bgcheckidArray){
		$resultstring = implode(',', $bgcheckidArray);
		$bgcheckArray = array();
		if($resultstring)
		{
			try
			{
				$qry = "select ob.id, ob.type from main_bgchecktype ob
                                        where ob.id IN (".$resultstring.") and ob.isactive = 1";		
				$db = Zend_Db_Table::getDefaultAdapter();
				$sqlRes = $db->query($qry);
				$bgcheckRes = $sqlRes->fetchAll();

				if(!empty($bgcheckRes))
				{
					foreach($bgcheckRes as $bgcheck)
					{
						$bgcheckArray[$bgcheck['id']]= $bgcheck['type'];
					}
				}
			}
			catch(Exception $e)
			{
				echo "Error Encountered - ".$e->getMessage();
			}
		}
		return $bgcheckArray;
	}

	/**
	 ** Employee leaves by day report.
	 **/
	public function getEmpLeavesByDay()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$yearStr = date('Y'); $monthStr = date('m');
		$fromDate = "'".$yearStr."-".$monthStr."-01'";
		$toDate = "'".$yearStr."-".$monthStr."-31'";
		$query = "select day(nn.thedate) theday,count(distinct user_id) cnt 
                          from main_leaverequest lr 
                          right join (select ".$fromDate." + INTERVAL n DAY AS thedate 
                          from numbers n where ".$fromDate." + INTERVAL n DAY <= ".$toDate.") nn on nn.thedate 
                          between from_date and to_date and leavestatus = 'Approved' group by nn.thedate;";
		$res = $db->query($query);
		$empleavesByDay = $res->fetchAll();
		return $empleavesByDay;

	}
	public function getAutoAgencyName($search_str)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select DISTINCT(u.agencyname),
                  case
                   when u.agencyname like '".$search_str."%' then 4
                   when u.agencyname like '__".$search_str."%' then 2 
                   when u.agencyname like '_".$search_str."%' then 3 
                   when u.agencyname like '%".$search_str."%' then 1 
                  else 0 end agency 
                  from main_bgagencylist u where (u.agencyname like '%".$search_str."%') and isactive = 1
                  order by agency desc
                  limit 0,10";
		$result = $db->query($query);
		$agency_arr = array();
		$agency_arr = $result->fetchAll();
		return $agency_arr;
	}

	public function getAutoAgencyWebsiteUrl($search_str)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select u.website_url,
                  case
                   when u.website_url like '".$search_str."%' then 4
                   when u.website_url like '__".$search_str."%' then 2 
                   when u.website_url like '_".$search_str."%' then 3 
                   when u.website_url like '%".$search_str."%' then 1 
                  else 0 end agency 
                  from main_bgagencylist u where (u.website_url like '%".$search_str."%') and isactive = 1
                  order by agency desc
                  limit 0,10";
		$result = $db->query($query);
		$agency_arr = array();
		$agency_arr = $result->fetchAll();
		return $agency_arr;
	}
	/**
	 ** Activity by Date report
	 **/
	public function getActivityByDate()
	{
            $db = Zend_Db_Table::getDefaultAdapter();
		
            $query = "select count(lm.id) cnt,day(nn.thedate) theday,ifnull(lm.user_action,1) user_action 
                      from main_logmanager lm right join ( select DATE_FORMAT(NOW() ,'%Y-%m-01') + INTERVAL n DAY AS thedate 
                      from numbers n where DATE_FORMAT(NOW() ,'%Y-%m-01') + INTERVAL n DAY <= last_day(now())) nn 
                      on nn.thedate = date(lm.last_modifieddate) and lm.menuId != 0 group by day(nn.thedate),lm.user_action;";
                
                
            $res = $db->query($query);
            $activityByDate = $res->fetchAll();

            return $activityByDate;
	}
	/**
	 ** Employees by Department report
	 **/
	
	public function getEmpByDepartment()
	{
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select count(a.id) cnt,d.deptname,d.deptcode from main_employees a 
                      inner join main_departments d on a.department_id = d.id group by d.id order by cnt desc limit 10; ";
            $res = $db->query($query);
            return $res->fetchAll();
	}
	
	public function getEmpscreeningInfo($sortby,$by,$pageNo,$perPage,$searchQuery)
	{
		if($searchQuery != '')
		$where = " WHERE ".$searchQuery;
		else $where = ' WHERE 1=1 ';
		
		if($pageNo != 0)
		$limitpage = (($pageNo-1)*$perPage);
		else
		$limitpage = 0;
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "SELECT *,DATE_FORMAT(createddate,'".DATEFORMAT_MYSQL."') as createddate	FROM main_bgchecks_summary $where GROUP BY id ORDER BY ".$sortby." ".$by." LIMIT ".$limitpage.", ".$perPage;		
		$result = $db->query($query)->fetchAll();
		return $result;		
	}
	
	public function getspecimenNames($search_str)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select distinct(specimen_name),id,
					case 
						when specimen_name like '".$search_str."%' then 4  
						when specimen_name like '__".$search_str."%' then 2 
						when specimen_name like '_".$search_str."%' then 3 
						when specimen_name like '%".$search_str."%' then 1 
					else 0 end sname 
					from main_bgchecks_summary 
					where specimen_name like '%".$search_str."%'
					group by sname order by sname desc
					limit 0,10";		
		$result = $db->query($query);
		$res_arr = array();
		$res_arr = $result->fetchAll();
		return $res_arr;
	}
	
	public function getagencyNames($search_str)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select agencyname,id,
					case 
						when agencyname like '".$search_str."%' then 4  
						when agencyname like '__".$search_str."%' then 2 
						when agencyname like '_".$search_str."%' then 3 
						when agencyname like '%".$search_str."%' then 1 
					else 0 end aname 
					from main_bgchecks_summary 
					where agencyname like '%".$search_str."%'
					group by aname order by aname desc
					limit 0,10";		
		$result = $db->query($query);
		$res_arr = array();
		$res_arr = $result->fetchAll();
		return $res_arr;
	}
	
	public function getEmpscreeningCount($searchQuery)
	{
		if($searchQuery != '')
		$where = " WHERE ".$searchQuery;
		else $where = ' WHERE 1=1 ';		

		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "SELECT *,DATE_FORMAT(createddate,'".DATEFORMAT_MYSQL."') as createddate	FROM main_bgchecks_summary $where GROUP BY id ";	
		
		$result = $db->query($query)->fetchAll();
		return count($result);
	}
	
}