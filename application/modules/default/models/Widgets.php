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

class Default_Model_Widgets extends Zend_Db_Table_Abstract
{
	private $db;
	 
	public function __construct() {
		$this->db = Zend_Db_Table::getDefaultAdapter();	
	}
	
	public function getWidgets($userid,$role_id)
	{	
		$menuDetails=array();$objectCsv="";
		$db = Zend_Db_Table::getDefaultAdapter();
		$menu = $db->query("SELECT menuid from main_settings WHERE isactive = 1 AND flag = 1 AND userid = ".$userid);
		$menuids = $menu->fetch();
		if(!empty($menuids) && $menuids['menuid']!= '')
		{	
			$menuIdArr = explode(',',$menuids['menuid']);
			$privilige_spl_menu = array(LEAVEREQUEST,SITEPREFERENCE,EMPLOYEETABS); //array for menu Items (which have no priviliges for view)
			$total_menu_items = array_intersect($privilige_spl_menu,$menuIdArr);
			$count_menu =  count($total_menu_items) ; //checking above Ids exist or not
			$splprivilegedMenusArr = array();
			if($count_menu > 0)  //if exists query to fetch details of menu Items
			{
				$privilige_spl_menu_list = implode(',',$total_menu_items);
				$splmenuPrivilegesquery = "select object from main_privileges where (addpermission = 'Yes' or editpermission = 'Yes') and isactive = 1  and object in(".$privilige_spl_menu_list.") and role =".$role_id;
				$result = $db->query($splmenuPrivilegesquery);
				$splprivilegedMenusArr = $result->fetchAll();
					
			}
			
			//Checking whether the logged in role has privileges for configured menus in widgets...
			$menuPrivilegesquery = "select object from main_privileges where viewpermission = 'Yes' and isactive = 1  and object in(".$menuids['menuid'].") and role =".$role_id;
			$result = $db->query($menuPrivilegesquery);
			$privilegedMenusArr = $result->fetchAll();
			if(!empty($splprivilegedMenusArr))
			$privilegedMenusArr = array_merge($splprivilegedMenusArr,$privilegedMenusArr);
			$privilegedMenusArr = array_filter(array_unique($privilegedMenusArr,SORT_REGULAR));
			
		}
		
		if(!empty($privilegedMenusArr))
		{	
			//Build csv for objects....
			for($i=0;$i<sizeof($privilegedMenusArr);$i++)
			 $objectCsv .= $privilegedMenusArr[$i]['object'].",";
			 $objectCsv = trim($objectCsv,",");
			 $widgets =  $db->query("SELECT * FROM main_menu WHERE  FIND_IN_SET(id,'".$objectCsv."');");
			$menuDetails = $widgets->fetchAll();
		}					
		 return $menuDetails;
	}
	
	public function getWidgetData($sort, $by, $pageNo, $perPage,$searchQuery,$tableName)
	{	
		$where = "isactive = 1 ";
		
		if($searchQuery)
			$where = $searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();	 
		if(!empty($by) && !empty($sort))	{
		
			$widgetData = $db->select()
							   ->from($tableName)
							   ->where($where)
							   ->order("$by $sort") 
							   ->limitPage($pageNo, $perPage);
			}
			else
			{	
				$widgetData = $db->select()
							   ->from($tableName)
							   ->where($where)
							  ->limitPage($pageNo, $perPage);
			}
			
		return $widgetData;   	
	}
	
	public function getTableFields($tableName)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$columns = $db->query("SELECT menufields FROM main_menu WHERE url = '".$tableName."'");
		$columnData = $columns->fetch();                
		return $columnData;
	}

	public function getTodaysBirthdays($businessunit_id,$department_id,$isOrganizationHead='')
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$whereStr = '';
		if($isOrganizationHead != 1)
		{
			if(!empty($businessunit_id))
			{
				$whereStr .= " and e.businessunit_id = ".$businessunit_id ;
			}		
			if(!empty($department_id))
			{
				$whereStr .= " and e.department_id = ".$department_id ;
			}
		}
		$whereStr .= " and e.isactive = 1";
		$qryStr = "select id,firstname, lastname from main_users where id in (Select p.user_id from main_emppersonaldetails p inner join main_employees e on p.user_id = e.user_id  where day(now()) = day(p.dob) and month(now()) = month(p.dob) ".$whereStr.")";

		$qry = $db->query($qryStr);
		$res = $qry->fetchAll();
		return $res;
	}

	public function getUpcomingBirthdays($businessunit_id,$department_id,$isOrganizationHead='')
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$whereStr = '';
		if($isOrganizationHead != 1)
		{
			if(!empty($businessunit_id))
			{
				$whereStr .= " and e.businessunit_id = ".$businessunit_id ;
			}		
			if(!empty($department_id))
			{
				$whereStr .= " and e.department_id = ".$department_id ;
			}
		}
		/**
		* 
		* Get list of empoyees whose birthdates are on coming next 5 days.
		* Add difference of years between user's date of birth and current year to user's date of birth
		* Then compare whether user's date of birth falls in between created date and next 5 days
		* @var String $qryStr
		*/
		$qryStr = 'SELECT e.userfullname, p.dob FROM  main_emppersonaldetails p  
					inner join main_employees_summary e on p.user_id = e.user_id 
					where  DATE_ADD(p.dob, 
                	INTERVAL YEAR(CURDATE())-YEAR(p.dob)
                         + IF(DAYOFYEAR(CURDATE()) > DAYOFYEAR(p.dob),1,0) YEAR)  
            BETWEEN DATE_ADD(CURDATE(), INTERVAL 1 DAY) AND DATE_ADD(CURDATE(), INTERVAL 5 DAY) '.$whereStr.'
			order by DAY(p.dob) ASC, p.user_id asc';
		$qry = $db->query($qryStr);
		$res = $qry->fetchAll();
		return $res;
	}


	function format2($id='')
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$auth = Zend_Auth::getInstance();
        $session = $auth->getStorage()->read();
        $loginUserId = $session->id;
        $loginGroupid = $session->group_id;
		if($id != '')
		{
			
			if($id==23)
			{
				
			   $cand_screen_qry="  SELECT COUNT(me.id) AS cnt1 FROM main_candidatedetails AS me 
								LEFT JOIN tbl_cities AS ct ON me.city=ct.id 
								LEFT JOIN tbl_states AS st ON me.state=st.id 
								LEFT JOIN tbl_countries AS cnt ON me.country=cnt.id 
								LEFT JOIN main_bgcheckdetails AS dt ON dt.specimen_id = me.id and flag = 2 
								LEFT JOIN main_bgagencylist AS al ON al.id = dt.bgagency_id 
								WHERE ( me.id <>".$loginUserId." AND me.backgroundchk_status <> 'Not Applicable' AND me.backgroundchk_status <> 'Yet to start' ) union 
				SELECT count(distinct me.user_id) FROM main_employees_summary AS me LEFT JOIN main_bgcheckdetails AS dt ON dt.specimen_id = me.user_id LEFT JOIN main_bgagencylist AS al ON al.id = dt.bgagency_id WHERE (me.backgroundchk_status <> 'Not Applicable' AND me.backgroundchk_status <> 'Yet to start') ;";
				$res_arr = $db->query($cand_screen_qry)->fetchAll();
				$res = array();
				if(!empty($res_arr))
				{
					foreach($res_arr as $res1)
					{
						$cnt1 = $res1['cnt1'];
						array_push($res,$cnt1);
					}
				}
			}
		}
		
		return $res;
	}
	function format4($id='')
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$auth = Zend_Auth::getInstance();
        $session = $auth->getStorage()->read();
        $loginUserId = $session->id;
        $loginGroupid = $session->group_id;
		
		if($id !='')
		{
			if($id == 14 || $id == 34 )
			{
				
				$qryStr = "SELECT COUNT(r.user_id) param1,COUNT(case when r.isactive = 1 then r.user_id end) param2
							FROM main_employees_summary  r ";
				 if($id == 34)
				{
					 $qryStr .= " where r.reporting_manager = ".$loginUserId; 
				}
			}
			else if($id == 54)
			{           
						$str = '';
						$str1 = '';
						if($loginUserId ==1 || $loginGroupid == MANAGEMENT_GROUP)
						{
						 $str = " ";	
						}
						else if( $loginGroupid == MANAGER_GROUP || $loginGroupid == HR_GROUP)
						{
						 $str = " AND (r.isactive = 1 AND r.req_status = 1 AND (r.createdby = $loginUserId or ($loginUserId in (approver1,approver2,approver3) 
							and 'Initiated' in (case when approver1 = $loginUserId then appstatus1 when approver2 = $loginUserId then appstatus2 when approver3 = $loginUserId then appstatus3 end))) ) ";
						  if($loginGroupid == MANAGER_GROUP)
						  $str1 = "  AND r.createdby = $loginUserId";
						
						}
						 
						$qryStr = "SELECT COUNT(case when r.req_status =1 ".$str." then r.req_id end) param1,COUNT(case when r.req_status =2 ".$str1." then r.req_id end) param2,COUNT(case when r.req_status =3 ".$str1." then r.req_id end) param3 FROM main_requisition_summary AS r where r.isactive = 1;";
				
				
			}
			else if($id == 56)
			{
				$qryStr = "SELECT COUNT(case when c.cand_status = 1 then c.id end) param2, COUNT(case when c.cand_status = 2 then c.id end) param3,COUNT(case when c.cand_status = 3 then c.id end) param1 
				FROM main_candidatedetails AS c 
				INNER JOIN main_requisition AS r ON r.id = c.requisition_id and r.isactive = 1 
				INNER JOIN main_interviewdetails AS m ON m.candidate_id = c.id and m.isactive = 1 
				WHERE (c.isactive = 1 AND m.interview_status = 2 );";
			}
			$res1 = $db->query($qryStr)->fetch(); 
		
	}
		return $res1;
	}

	function format5($id='')
	{ 
		$db = Zend_Db_Table::getDefaultAdapter();
		$auth = Zend_Auth::getInstance();
        $session = $auth->getStorage()->read();
        $loginUserId = $session->id;
        $loginGroupid = $session->group_id;
        $loginuserRole = $session->emprole;
		$businesUnit = $session->businessunit_id;
		$department = $session->department_id;
		$res = '';
		$where = '';
		$table = '';
		$limit = '';
		$order = '';
		if($id!='')
		{	
			$limit = "LIMIT 0,5";
			$res_array = array();
		
		   if($id == 11 ||$id == 80 || $id == 86 || $id == 87 || $id == 88 || $id == 89 || $id == 90 || $id == 91 || $id == 92 || $id == 93 || $id == 100 || $id == 101 || $id == 102 || $id == 103 || $id == 107 || $id == 108 || $id ==	110 || $id == 114 || $id == 115 || $id == 116 || $id == 117 || $id == 118 || $id == 120 || $id == 121 || $id == 123 || $id == 124 || $id == 125 || $id == 126 || $id ==	127 || $id == 128 || $id == 132 || $id == 142 || $id == 144 || $id == 145 || $id == 150 || $id == 151 || $id ==152 || $id ==165 || $id == 166 || $id == 182)
			{
			  if($id == 11)
			   {
				$table = ' main_departments ';
				$item = ' deptname ';
			 }
			
			else if($id==80)
			{
				$table = ' main_timezone ';
				$item = ' timezone ';
			}
		    else if($id==86)
			{
				$table = ' main_gender ';
				$item = ' gendername ';
			}
			else if($id == 87)
			{
				$table = ' main_maritalstatus ';
				$item = ' maritalstatusname ';
			}
			else if($id == 88)
			{
				$table = ' main_prefix ';
				$item = ' prefix ';
			}
			else if($id == 89)
			{
				$table = ' main_racecode ';
				$item = ' racename ';
			}
			else if($id == 90)
			{
				$table = ' main_nationalitycontextcode ';
				$item = ' nationalitycontextcode ';
			}
			else if($id == 91)
			{
				$table = ' main_nationality ';
				$item = ' nationalitycode ';
			}
			else if($id == 92)
			{
				$table = ' main_accountclasstype ';
				$item = ' accountclasstype ';
			}
			else if($id == 93)
			{
				$table = ' main_licensetype ';
				$item = ' licensetype ';
			}
			else if($id == 100)
			{
				$table = ' main_countries ';
				$item = ' country ';
			}
			else if($id == 101)
			{
				$table = ' main_states ';
				$item = ' state ';
			}
			else if($id == 102)
			{
				$table = ' main_cities ';
				$item = ' city ';
			}
			else if($id == 103)
			{
				$where = ' g.isactive = 1 AND ';
				$table = ' main_geographygroup as g LEFT JOIN `main_currency` AS `c` ON g.currency=c.id ';
				$item = ' g.geographygroupname ';
				$order = ' g.modifieddate DESC ';
			}
			else if($id == 107)
			{
				$table = ' main_veteranstatus ';
				$item = ' veteranstatus ';
			}
			else if($id == 108)
			{
				$table = ' main_militaryservice ';
				$item = ' militaryservicetype ';
			}
			else if($id == 110)
			{
				$table = ' main_currency ';
				$item = ' currencyname ';
			}
			
			else if($id == 114)
			{
				$table = ' main_employmentstatus AS c LEFT JOIN tbl_employmentstatus AS es ON es.id=c.workcodename ';
				$item = ' es.employemnt_status ';
				$order = " c.modifieddate ";
			}
			else if($id == 115)
			{
				$table = ' main_eeoccategory ';
				$item = ' eeoccategory ';
			}
			else if($id == 116)
			{
				$table = ' main_jobtitles ';
				$item = ' jobtitlename ';
			}
			else if($id == 117)
			{
				$table = ' main_payfrequency ';
				$item = ' freqtype ';
			}
			else if($id == 118)
			{
				$table = ' main_remunerationbasis ';
				$item = ' remtype ';
			}
			else if($id == 120)
			{	
				
				$where = ' g.isactive = 1 AND ';
				$table = ' main_positions AS g LEFT JOIN main_jobtitles AS c ON c.id=g.jobtitleid ';
				$item = ' g.positionname ';
				$order = ' g.modifieddate DESC  ';
			}
			else if($id == 121)
			{
				$table = ' main_language ';
				$item = ' languagename ';
			}
			else if($id == 123)
			{
				$table = ' main_bankaccounttype ';
				$item = ' bankaccounttype ';
			}
			else if($id == 124)
			{
				$table = ' main_competencylevel ';
				$item = ' competencylevel ';
			}
			else if($id == 125)
			{
				$table = ' main_educationlevelcode ';
				$item = ' educationlevelcode ';
			}
			else if($id == 126)
			{
				$table = ' main_attendancestatuscode ';
				$item = ' attendancestatuscode ';
			}
			else if($id == 127)
			{
				$table = ' main_workeligibilitydoctypes ';
				$item = ' documenttype ';
			}
			else if($id == 128)
			{
				$table = ' main_employeeleavetypes ';
				$item = ' leavetype ';
			}
			else if($id == 132)
			{
				$table = ' main_numberformats ';
				$item = ' numberformattype ';
			}
			else if($id == 142)
			{
				//$defined_menus_arr = array(TIMEMANAGEMENT,RESOURCEREQUISITION,BGCHECKS,REPORTS,SERVICEDESK,PERFORMANCEAPPRAISAL);  MANAGE_MODULE_ARRAY
				$defined_menus_arr = unserialize(MANAGE_MODULE_ARRAY);
				$defined_menus_str= implode(',',$defined_menus_arr);
				$table = ' main_menu ';
				$item = " menuName as param1,if(isactive = 0,'Inacive','Active') param2 ";
				$where = " id in ($defined_menus_str) " ;
			}
			else if($id == 144)
			{
				$table = ' main_sd_depts ';
				$item = ' service_desk_name ';
			}
			else if($id == 145)
			{
				$where = ' sdr.isactive = 1 and ';
				$table = ' main_sd_reqtypes as sdr INNER JOIN main_sd_depts AS c ON sdr.service_desk_id=c.id ';
				$item = ' sdr.service_request_name ';
				$order = ' sdr.modifieddate DESC  ';
			}
			
			else if($id == 150)
			{
				$table = ' main_pa_category ';
				$item = ' category_name ';
			}
			else if($id == 151)
			{
				$table = ' main_pa_skills ';
				$item = ' skill_name ';
				if($loginuserRole != 1)
			        {
			            if($loginGroupid != MANAGER_GROUP)
			                $where = " (createdby_group in ( ".$loginGroupid.") or createdby_group is null ) and ";
			            else 
			                $where = " (createdby_group in ( ".$loginGroupid.") ) and ";
			        }
			}
			else if($id == 152 || $id == 166)
			{
				if($id == 152)
				{
					$where = " module_flag = 1 and ";
					if($loginuserRole != 1)
			        {
			            if($loginGroupid != MANAGER_GROUP)
			                $where .= " (createdby_group in ( ".$loginGroupid.") or createdby_group is null ) and ";
			            else 
			                $where .= " (createdby_group in ( ".$loginGroupid.") ) and ";
			        }
				}
				else {
					$where = " module_flag = 2 and ";
					if($loginuserRole != 1)
						$where .= " createdby_group = $loginGroupid and ";
					
				}
				$table = ' main_pa_questions ';
				$item = ' question ';
			}
			else if($id = 182)
			{
				$table = ' main_pd_categories ';
				$item = ' category ';
			}

			if($id == 103 || $id == 114 || $id == 120 || $id == 145)
			{
			 $qryString = "select ".$item." as param1 from ".$table." where ".$where." c.isactive=1  order by $order $limit"; 
			 $res_array = $db->query($qryString)->fetchAll();
			 $countQuery = "select COUNT($item) count from ".$table." where ".$where." c.isactive=1";
			 $res_array["count"] = $db->query($countQuery)->fetch();
			}
			else{
				if($id == 142)
				{
					
				$qryString = "select ".$item." from ".$table." where ".$where;
				$res_array = $db->query($qryString)->fetchAll(); 
				$res_array["count"]['count'] = sizeof($defined_menus_arr);
				}
				else if($id == 182)
				{

					$qryString = 'select c.category param1, CASE WHEN a.cnt is NULL THEN 0 ELSE a.cnt END as param2 from main_pd_categories c left join (select d.category_id, count(d.id) cnt from main_pd_documents d where d.isactive = 1 group by d.category_id) a on c.id = a.category_id where c.isactive = 1 order by c.category;'; 	
					$res_array = $db->query($qryString)->fetchAll(); 
					$res_array["count"]['count'] = sizeof($res_array);
				}
				else{
				$qryString = "select ".$item." as param1 from ".$table." where ".$where." isactive=1 ORDER BY modifieddate DESC $limit";
				$res_array = $db->query($qryString)->fetchAll();
				$countQuery = "select COUNT($item) count from ".$table." where ".$where." isactive=1";
				$res_array["count"] = $db->query($countQuery)->fetch();
				}
				
				
			} 
				
				
				// Get count of records in this case
				
				
				
		 } 
		else if($id==111)
			{ 
			  $queryStr = "select basecurrtext as param1,targetcurrtext as param2 from main_currencyconverter where isactive=1  ORDER BY modifieddate DESC $limit";
			  $res_array = $db->query($queryStr)->fetchAll();
				
				// Get count of records in this case
				$countQuery = "select COUNT(id) count from main_currencyconverter where isactive=1";
				$res_array["count"] = $db->query($countQuery)->fetch();			  

			}
			else if($id==136)
			{ 
			  $queryStr = " select c.groupEmail as param1,g.group_name as param2 from main_emailcontacts c 
							inner join main_emailgroups g on c.group_id=g.id 
							inner join main_businessunits bu on c.business_unit_id=bu.id 
							where c.isactive=1 and g.isactive=1 and bu.isactive = 1 order by g.group_name $limit";
			  $res_array = $db->query($queryStr)->fetchAll();
				
				// Get count of records in this case
				$countQuery = "select COUNT(c.id) count from main_emailcontacts c
							   inner join main_emailgroups g on c.group_id=g.id  
							   inner join main_businessunits bu on c.business_unit_id=bu.id 
							   where c.isactive=1 and g.isactive=1 and bu.isactive = 1; ";
				$res_array["count"] = $db->query($countQuery)->fetch();			  
			  

			}
			else if($id==146)
			{ 
			  $queryStr = " select s.service_desk_name param1 from main_sd_configurations c 
							left join main_sd_depts as s on c.service_desk_id = s.id 
							LEFT JOIN main_businessunits AS b ON c.businessunit_id = b.id 
							LEFT JOIN main_departments AS d ON c.department_id = d.id 
							where b.isactive=1 and s.isactive=1 and c.isactive=1 order by c.modifieddate desc $limit";
			  $res_array = $db->query($queryStr)->fetchAll();
				
				// Get count of records in this case
				$countQuery = " select COUNT(c.id) count from main_sd_configurations c 
								left join main_sd_depts as s on c.service_desk_id = s.id 
								LEFT JOIN main_businessunits AS b ON c.businessunit_id = b.id 
								LEFT JOIN main_departments AS d ON c.department_id = d.id 
								where b.isactive=1 and s.isactive=1 and c.isactive=1;";
				$res_array["count"] = $db->query($countQuery)->fetch();				  

			}
			else if($id==10)
			{ 
			  $queryStr = "select unitname as param1,case when a.dept_cnt is NULL then 0 else a.dept_cnt end as param2 
							from main_businessunits b left join
							(select unitid,count(deptname) dept_cnt from main_departments where isactive=1 group by unitid) a on a.unitid=b.id
							where b.isactive=1 and b.id <> 0
							order by unitname $limit";
			  $res_array = $db->query($queryStr)->fetchAll();
				
				// Get count of records in this case
				$countQuery = "select COUNT(distinct(b.id)) count from main_businessunits b left join
							(select unitid,count(deptname) dept_cnt from main_departments where isactive=1 group by unitid) a on a.unitid=b.id
							where b.isactive=1 and b.id <> 0 order by unitname";
				$res_array["count"] = $db->query($countQuery)->fetch();					  

			}
			else if($id == 20)
			{
				$queryStr = "select count(a.group_id) param2,g.group_name as param1 from main_groups g inner join main_roles a on g.id=a.group_id where a.isactive=1 and g.isactive=1 group by a.group_id ORDER BY a.modifieddate DESC $limit";
			  $res_array = $db->query($queryStr)->fetchAll();
				
				// Get count of records in this case
				$countQuery = "select COUNT(a.id) count from main_groups g inner join main_roles a on g.id=a.group_id where a.isactive=1 and g.isactive=1";
				$res_array["count"] = $db->query($countQuery)->fetch();				  

			}
			else if($id == 21)
			{
				$queryStr = "select group_concat(id) id from main_roles where group_id=".USERS_GROUP." and isactive=1";
				$id = $db->query($queryStr)->fetch();
				$ids = $id['id'];
				if($ids !='')
				{
					$qrystr2= "select * from main_identitycodes";
					$identity_rows = $db->query($qrystr2)->fetch();
					$backgroundagency_code = ($identity_rows['backgroundagency_code']) ? "%".$identity_rows['backgroundagency_code']."%" : "";
					$users_code = ($identity_rows['users_code']) ? "%".$identity_rows['users_code']."%" : "";
					$vendors_code = ($identity_rows['vendors_code']) ? "%".$identity_rows['vendors_code']."%" : "";
					$staffing_code = ($identity_rows['staffing_code']) ? "%".$identity_rows['staffing_code']."%" : "";
					
					$strQry3 = "select count(case when employeeId like '$backgroundagency_code'  then  employeeId end) backgroundagency, 
					count(case when employeeId like '$users_code' then  employeeId end) users, 
					count(case when employeeId like '$vendors_code' then employeeId end) vendors, 
					count(case when employeeId like '$staffing_code' then employeeId end) staffing 
					from main_users where emprole in(".$ids.") and isactive=1";
					$res_array = $db->query($strQry3)->fetch();
					
				}
			}
			else if($id == 41)
			   {
				$queryStr = "   SELECT hg.groupname param1,count(h.holidayname) param2 FROM main_holidaygroups as hg 
								LEFT JOIN main_holidaydates AS h ON h.groupid=hg.id
								WHERE hg.isactive = 1 and h.isactive=1 group by hg.groupname order by hg.modifieddate desc;";
				$res_array = $db->query($queryStr)->fetchAll();
				$countQuery = "select COUNT(id) count from main_holidaygroups where  isactive=1;";
				$res_array["count"] = $db->query($countQuery)->fetch();
			   }
			
			else if($id == 43)
			{
				 $queryStr = "   select holidayname param2,DATE_FORMAT(h.holidaydate,'".DATEFORMAT_MYSQL."') as param1 from main_holidaydates as h
								LEFT JOIN main_holidaygroups AS hg ON hg.id=h.groupid ";
				 $where =" WHERE (h.isactive = 1 AND hg.isactive=1 AND h.groupid in (SELECT holiday_group FROM main_employees AS e WHERE (isactive = 1 AND user_id =$loginUserId)) AND h.holidayyear = year(now()))	ORDER BY h.holidaydate DESC ";
				$queryStr .= $where.$limit; 
			 	$res_array = $db->query($queryStr)->fetchAll();

			   $countQuery = " select count(holidayname) count from main_holidaydates as h
								LEFT JOIN main_holidaygroups AS hg ON hg.id=h.groupid ";
			    	//$where =" WHERE (h.isactive = 1 AND hg.isactive=1 AND h.groupid in (SELECT holiday_group FROM main_employees AS e WHERE (isactive = 1 AND user_id =$loginUserId)) AND h.holidayyear = year(now()))	";
			   	//$where =" WHERE (h.isactive = 1 AND hg.isactive=1 )";
			   $countQuery.=$where; 
				$res_array["count"] = $db->query($countQuery)->fetch();

			}
			else if($id == 42)
			{
				 $queryStr = "   SELECT c.holidayname param2, DATE_FORMAT(c.holidaydate,'".DATEFORMAT_MYSQL."') AS param1 FROM main_holidaydates AS c 
									LEFT JOIN main_holidaygroups AS hg ON hg.id=c.groupid ";
				 $where =" where (c.isactive = 1 AND hg.isactive=1) ORDER BY c.modifieddate  ";
				  $queryStr .= $where.$limit; 
			 	$res_array = $db->query($queryStr)->fetchAll();

			   $countQuery = " select count(c.holidayname) count from main_holidaydates as c LEFT JOIN main_holidaygroups AS hg ON hg.id=c.groupid ";
			    	
			   $countQuery.=$where; 
				$res_array["count"] = $db->query($countQuery)->fetch();

			}
			else if($id == 159 || $id == 174)
			{
				$order = '';
				
				
					if($loginUserId == 1 || $loginGroupid == MANAGEMENT_GROUP)
					{  
						$order = " order by rand() limit 0,1;";
					}	
					else
					{ 
					    $where = " and businessunit_id=$businesUnit and (case when department_id is not NULL then department_id =$department else department_id is NULL end )";
					}	
					
					$check_initData = "select * from main_pa_initialization where isactive=1 and status=1 and enable_step =2". $where.$order; 
					$result = $db->query($check_initData)->fetch();
					if(!empty($result))
					{   
						$initId = $result['id'];
						$Bunit = $result['businessunit_id'];
						$Dept = $result['department_id']; 
						$res_array['from_year'] = $result['from_year'];
						$res_array['to_year'] = $result['to_year'];
						$res_array['appraisal_mode'] = $result['appraisal_mode'];
						$appraisal_period = $result['appraisal_period'];
						if($res_array['appraisal_mode'] == 'Quarterly')
						{
							if($appraisal_period == 1)
							$res_array['appraisal_period'] = 'Q1';
							else if($appraisal_period == 2)
							$res_array['appraisal_period'] = 'Q2';
							else if($appraisal_period == 3)
							$res_array['appraisal_period'] = 'Q3';
							else
							$res_array['appraisal_period'] = 'Q4';
						}
						else if($res_array['appraisal_mode'] == 'Half-yearly')
						{
							$res_array['appraisal_period'] = ($appraisal_period == 1) ?  'H1':'H2';
						}
						else 
						{
							$res_array['appraisal_period'] = 'Yearly';
						}
						$bunit = "select unitname from main_businessunits where id = $Bunit and isactive=1;";
						$result = $db->query($bunit)->fetch();
						$res_array['businessUnit']= $result['unitname'];
						 if($Dept !='')
						 {
						  $dept = "select deptname from main_departments where id = $Dept and unitid=$Bunit and isactive=1;" ;
						  $result = $db->query($dept)->fetch();
						  $res_array['department']= $result['deptname'];
						
						 }
						
					     if($id == 159)
					     {	 
					 		$queryStr = "  SELECT count(case when appraisal_status=1 then employee_id end)pending_employee_ratings,
											count(case when appraisal_status=7 then employee_id end)completed,
											count(case when (appraisal_status!=7 and appraisal_status!=1) then employee_id end)pending_manager_ratings 
											FROM main_pa_employee_ratings 
											WHERE isactive = 1 AND pa_initialization_id = $initId;";
					     }
					     else if($id == 174)
					     {
					     	
					     	$queryStr = "SELECT er.pa_initialization_id,count(case when er.appraisal_status=1 then er.employee_id end)pending_employee_ratings, 
										count(case when er.appraisal_status=7 then er.employee_id end)completed, 
										count(case when (er.appraisal_status!=7 and er.appraisal_status!=1) then er.employee_id end)pending_manager_ratings 
										from  main_pa_employee_ratings er  
										inner join main_pa_initialization pi on  er.pa_initialization_id = pi.id
										where  $loginUserId in (er.line_manager_1,er.line_manager_2,er.line_manager_3,er.line_manager_4,er.line_manager_5) 
										and  pi.status = 1 and pi.initialize_status = 1 and pi.enable_step =2 
										order by er.modifieddate desc;";
					     }
					     if($id == 174 && $loginUserId == 1)
					     {
					     	$res_array = '';
					     }
					     else
					     {
						 $res_array['ratings'] = $db->query($queryStr)->fetch();
					     }
					}	
			}
		else if($id == 158)
		{
				$order = '';
				if($loginUserId == 1 || $loginGroupid == MANAGEMENT_GROUP)
				{  
					$order = " order by rand() limit 0,1;";
				}	
				else
				{ 
				    $where = " and businessunit_id=$businesUnit and (case when department_id is not NULL then department_id =$department else department_id is NULL end )";
				}	
					
					$check_initData = "SELECT * FROM main_pa_initialization  WHERE isactive = 1 AND status in (0,1) and enable_step=1 AND initialize_status = 1 ". $where.$order; 
					$result = $db->query($check_initData)->fetch();
				   if(!empty($result))
					{
						$initId = $result['id'];
						$Bunit = $result['businessunit_id'];
						$Dept = $result['department_id']; 
						$res_array['from_year'] = $result['from_year'];
						$res_array['to_year'] = $result['to_year'];
						$res_array['appraisal_mode'] = $result['appraisal_mode'];
						$appraisal_period = $result['appraisal_period'];
					  if($res_array['appraisal_mode'] == 'Quarterly')
						{
							if($appraisal_period == 1)
							$res_array['appraisal_period'] = 'Q1';
							else if($appraisal_period == 2)
							$res_array['appraisal_period'] = 'Q2';
							else if($appraisal_period == 3)
							$res_array['appraisal_period'] = 'Q3';
							else
							$res_array['appraisal_period'] = 'Q4';
						}
						else if($res_array['appraisal_mode'] == 'Half-yearly')
						{
							$res_array['appraisal_period'] = ($appraisal_period == 1) ?  'H1':'H2';
						}
						else 
						{
							$res_array['appraisal_period'] = 'Yearly';
						}
						$manager_ids = $result['manager_ids'];
						$mngrTotArr = isset($manager_ids)? explode(",",$manager_ids):array();
						$bunit = "select unitname from main_businessunits where id = $Bunit and isactive=1;";
						$result = $db->query($bunit)->fetch();
						$res_array['businessUnit']= $result['unitname'];
					 if($Dept !='')
					 {
					  $dept = "select deptname from main_departments where id = $Dept and unitid=$Bunit and isactive=1;" ;
					  $result = $db->query($dept)->fetch();
					  $res_array['department']= $result['deptname'];
					
					 }		
					 $qryStr = "select line_manager_1 as mgr from main_pa_questions_privileges where pa_initialization_id = $initId and isactive=1 group by line_manager_1;";
					 $result = $db->query($qryStr)->fetchAll(); 
					 $mgrIdArr = array();
					 $mgrIdList = '';
					  if(!empty($result))
					  {
					  	foreach($result as $mgrId)
					  	{ 
					  		$mgrId = $mgrId['mgr'];
					  		array_push($mgrIdArr,$mgrId);
					  	}
					  	$res_array['compltedmgrIdArr'] = sizeof($mgrIdArr);
					  	$notcompletedMngrArr = array_diff($mngrTotArr,$mgrIdArr);
					  	$res_array['notCompltedmgrIdArr'] = sizeof($notcompletedMngrArr);
					  }
					 
					
					 
					}
		}
		
		else if($id == 172)
			{
				$order = '';
				
					if($loginUserId == 1 || $loginGroupid == MANAGEMENT_GROUP)
					{  
						$order = " order by rand() limit 0,1;";
					}	
					else
					{ 
					    $where = " and businessunit_id=$businesUnit and (case when department_id is not NULL then department_id =$department else department_id is NULL end )";
					}	
					
					$check_initData = "select * from main_pa_ff_initialization where isactive=1 and status=1 and enable_to =0". $where.$order; 
					$result = $db->query($check_initData)->fetch();
					if(!empty($result))
					{   
						$initId = $result['id'];
						$Bunit = $result['businessunit_id'];
						$Dept = $result['department_id']; 
						$res_array['ff_from_year'] = $result['ff_from_year'];
						$res_array['ff_to_year'] = $result['ff_to_year'];
						$res_array['ff_mode'] = $result['ff_mode'];
						$res_array['ff_period'] = $result['ff_period'];
						$ff_period = $result['ff_mode'];
						if($res_array['ff_mode'] == 'Quarterly')
						{
							if($ff_period == 1)
							$res_array['ff_period'] = 'Q1';
							else if($ff_period == 2)
							$res_array['ff_period'] = 'Q2';
							else if($ff_period == 3)
							$res_array['ff_period'] = 'Q3';
							else
							$res_array['ff_period'] = 'Q4';
						}
						else if($res_array['ff_period'] == 'Half-yearly')
						{
							$res_array['ff_period'] = ($ff_period == 1) ?  'H1':'H2';
						}
						else 
						{
							$res_array['ff_period'] = 'Yearly';
						}
						$bunit = "select unitname from main_businessunits where id = $Bunit and isactive=1;";
						$result = $db->query($bunit)->fetch();
						$res_array['businessUnit']= $result['unitname'];
						 if($Dept !='')
						 {
						  $dept = "select deptname from main_departments where id = $Dept and unitid=$Bunit and isactive=1;" ;
						  $result = $db->query($dept)->fetch();
						  $res_array['department']= $result['deptname'];
						
						 }
						
					     	$queryStr = "   SELECT count(case when ff_status=1 then employee_id end)pending_employee_ratings,
											count(case when ff_status=2 then employee_id end)completed
											FROM main_pa_ff_employee_ratings 
											WHERE isactive = 1 AND ff_initialization_id = $initId;";
					     	 $res_array['ratings'] = $db->query($queryStr)->fetch();
					     
					}
		
		
			
		 }
		 
		 else if($id == 131)
		 {
		 	$queryStr = 'SELECT  d.example AS date_example,tm.example AS time_example,
						concat(c.currencyname," ",c.currencycode) AS currency,  concat(z.timezone," [",z.timezone_abbr,"]") AS timezone, 
						pw.passwordtype FROM main_sitepreference AS s 
						LEFT JOIN main_dateformat AS d ON d.id=s.dateformatid 
						LEFT JOIN main_timeformat AS tm ON tm.id=s.timeformatid 
						LEFT JOIN main_currency AS c ON c.id=s.currencyid 
						LEFT JOIN main_timezone AS z ON z.id=s.timezoneid 
						LEFT JOIN tbl_password AS pw ON pw.id=s.passwordid 
						WHERE (s.isactive = 1  AND  c.isactive=1 AND pw.isactive=1);';
		 	$res_array = $db->query($queryStr)->fetch();
		 	//echo "<pre>";print_r($res_array);die();
		 }
		 else if($id == 55)
		 {
		 	$queryStr = "SELECT  count(case when cand_status like '%Scheduled%' then c.id end ) scheduled,
        				 		 count(case when cand_status like '%Not Scheduled%' then c.id end ) not_scheduled,
        						 count(case when cand_status like '%On hold%' then c.id end ) on_hold,	
								 count(case when cand_status like '%Shortlisted%' then c.id end ) shortlisted,
								 count(case when cand_status like '%Selected%' then c.id end ) selected
								 FROM main_candidatedetails AS c 
							LEFT JOIN main_requisition_summary AS r ON r.req_id = c.requisition_id and r.isactive = 1 
							WHERE (c.isactive = 1 and c.cand_status != 'Recruited' ) ORDER BY c.modifieddate DESC;";
		 	$res_array = $db->query($queryStr)->fetch();
		 	
		 	 $countQuery = "   select count(c.id) cnt FROM main_candidatedetails AS c 
								LEFT JOIN main_requisition_summary AS r ON r.req_id = c.requisition_id and r.isactive = 1 
								WHERE (c.isactive = 1 and c.cand_status != 'Recruited' ) ; ";
			 $res_array["count"] = $db->query($countQuery)->fetch();
		 }
		}
			return $res_array;
	}

	function format1()  
	{
		$auth = Zend_Auth::getInstance();
        $session = $auth->getStorage()->read();
        $loginUserId = $session->id;
        $loginGroupid = $session->group_id;
		$db = Zend_Db_Table::getDefaultAdapter();
		$qryStr = "select c.id,r.requisition_id,c.candidate_name,c.emailid,c.contact_number,c.cand_resume,r.interview_id,r.interviewer_name,r.interview_time,r.interview_mode from main_interviewrounds_summary r inner join main_candidatedetails c  on r.candidate_id = c.id INNER JOIN main_requisition AS req ON req.id = r.requisition_id and req.isactive = 1 where r.isactive = 1  and c.isactive = 1 and r.interview_date = date(now())  and r.interview_status not in 
		('On hold','Completed','Requisition Closed/Completed') and req.req_status not in ('On hold') ";

		if($loginUserId!=1)
		$qryStr .=" and	r.interviewer_id=".$loginUserId;
		$qryStr .=" ORDER BY r.created_date DESC limit 0,3 ";
		$res = $db->query($qryStr)->fetchAll();
		return $res;
	}

	function format3($id='')
	{
		$auth = Zend_Auth::getInstance();
        $session = $auth->getStorage()->read();
        $loginUserId = $session->id;
        $loginGroupid = $session->group_id;
        $businesUnit = $session->businessunit_id;
		$department = $session->department_id;
		$db = Zend_Db_Table::getDefaultAdapter();
		$res = '';
		$where = '';
		$table = '';
		
		if($id !='')
		{
			if($id !=61 && $id !=65 && $id!=45 && $id !=161 && $id != 170)
			{
				if($id == 62 || $id == 63 || $id == 64 || $id == 135 )
				{
					$table = " main_leaverequest_summary ";
					$id_var = " id ";
					$where2 = " AND user_id = ".$loginUserId." AND isactive=1; ";
					if($id == 62)
					$where = " leavestatus=1 ". $where2;
					else if($id == 63)
					$where = " leavestatus=2 ".$where2;
					else if($id == 64)
					$where = " leavestatus=4 ".$where2;
					else
					$where = " leavestatus=3 ".$where2;
				}
				else if($id == 68)
				{	
					$id_var = " id ";
					$table = " main_bgchecktype ";
					$where = " isactive=1;";
				}
				else if($id == 69)
				{   
					$id_var =  " b.id ";
					$table = " main_bgagencylist AS b INNER JOIN main_users AS u ON u.id = b.user_id   ";
					$where = " b.isactive = 1 and u.isactive = 1; ";
				}
				else if($id == 85)
				{
					$id_var = " id ";
					$table = " main_ethniccode ";
					$where = " isactive=1;";
				}
				
				else if($id == 134 || $id == 138)
				{  
					$id_var = " id ";
					$table = " main_requisition_summary ";
					if($id == 134)
					$status = 2;
					else 
					$status =3;
					$where = " req_status=".$status." and isactive=1 ";
					if($loginGroupid == MANAGER_GROUP)
					$where .=" AND createdby = ".$loginUserId." ";
				}
				else if($id == 139)
				{	
					$id_var = " id ";
					$table = " main_identitydocuments ";
					$where = " isactive=1;";
				}
				else if($id == 143)
				{	
					$id_var = " id ";
					$table = " main_sd_requests ";
					$where = " isactive=1 and raised_by = $loginUserId;";
				}
				else if($id == 148)
				{	
					$id_var = " id ";
					$table = " main_sd_requests ";
					$where = " isactive=1 and reporting_manager_id = $loginUserId and status in ('To manager approve','Closed','Manager approved','Manager rejected','Rejected');";
				}
				else if($id == 169)
				{	
					$id_var = " case when initialize_status = 1 and enable_step = 1 and status =1  then id end ";
					$table = " main_pa_initialization ";
					$where = " isactive=1 ";
				  if($loginUserId !=1 && $loginGroupid != MANAGEMENT_GROUP)
					{
					  $where .= " and businessunit_id = $businesUnit and department_id = $department ";
					}
				}
				
				$qryStr = "select count(".$id_var.") cnt from". $table ." where ". $where ;
			}
			else
			{
				if($id == 65)
				{
				    $qryStr = " SELECT COUNT(et.id) AS cnt FROM main_leaverequest AS l 
							LEFT JOIN main_employeeleavetypes AS et ON et.id=l.leavetypeid 
							LEFT JOIN main_users AS u ON u.id=l.rep_mang_id and u.id = l.user_id
							WHERE (l.isactive = 1 AND l.rep_mang_id = ".$loginUserId." AND l.leavestatus = 1 );";
				}
				else if($id == 45)
				{
					$qryStr = " SELECT COUNT(l.id) AS cnt FROM main_leaverequest_summary AS l 
								LEFT JOIN main_employeeleavetypes AS et ON et.id=l.leavetypeid 
								LEFT JOIN main_users AS u ON u.id=l.rep_mang_id and u.id = l.user_id 
								LEFT JOIN main_users AS mu ON mu.id=l.user_id 
								WHERE (l.isactive = 1 AND l.leavestatus = 2 )";
				}
				else if($id == 161)
				{
					$qryStr = " SELECT aer.appraisal_status as cnt
								FROM main_pa_employee_ratings AS aer 
								INNER JOIN main_pa_initialization AS ai ON ai.id = aer.pa_initialization_id 
								WHERE (aer.isactive = 1 AND ai.status = 1 AND ai.enable_step = 2 AND aer.employee_id = $loginUserId);";
				}
				else if($id == 170)
				{
					$qryStr = " SELECT ffer.ff_status as cnt FROM main_pa_ff_employee_ratings AS ffer 
								INNER JOIN main_pa_ff_initialization AS ffi ON ffi.id = ffer.ff_initialization_id 
								WHERE (ffer.isactive = 1 AND ffi.status = 1  AND ffer.employee_id = $loginUserId);"; 
				}
				else if($id == 61)
				{
					$qryStr = " select (emp_leave_limit-used_leaves) as cnt from main_employeeleaves where user_id = $loginUserId order by modifieddate desc; ";
				}
			}
			
		
		$res = $db->query($qryStr)->fetch();
		
		}
		return $res;
	}

	function format6($id='')  
	{
		$auth = Zend_Auth::getInstance();
        $session = $auth->getStorage()->read();
        $loginUserId = $session->id;
        $loginGroupid = $session->group_id;
		$db = Zend_Db_Table::getDefaultAdapter();
		if($loginUserId != 1)
		{
		  $join = " LEFT JOIN main_employees AS e ON e.businessunit_id = l.businessunit_id ";
		 $con = " AND e.user_id = ".$loginUserId ;
		}
		else
		{
		$con = '';
		$join='';
		}
		$qryStr = "SELECT  if(l.is_halfday = 1,'Yes','No') AS is_halfday, 
					if(l.is_leavetransfer = 1,'Yes','No') AS is_leavetransfer, 
					w.week_name as weekend_start , wk.week_name as weekend_end, m.month_name, b.unitname,d.deptname 
					FROM main_leavemanagement AS l 
					LEFT JOIN tbl_weeks AS w ON w.week_id=l.weekend_startday 
					LEFT JOIN tbl_weeks AS wk ON wk.week_id=l.weekend_endday 
					LEFT JOIN tbl_months AS m ON m.monthid=l.cal_startmonth 
					LEFT JOIN main_departments AS d ON d.id=l.department_id 
					LEFT JOIN main_businessunits AS b ON b.id=l.businessunit_id ".$join. " 					
					WHERE (l.isactive = 1 AND d.isactive=1 AND b.isactive=1 ".$con." ) ORDER BY l.modifieddate DESC LIMIT 1;";

		$res = $db->query($qryStr)->fetchAll();
		return $res;
	}
	
	function format7($id='')  
	{
		$auth = Zend_Auth::getInstance();
        $session = $auth->getStorage()->read();
        $loginUserId = $session->id;
        $loginGroupid = $session->group_id;
		$db = Zend_Db_Table::getDefaultAdapter();
		
			$qryStr = 'SELECT u.employeeId,u.emailaddress,j.jobtitlename,
			if(u.contactnumber is null," ",u.contactnumber) as contact,u.profileimg,concat(if(p.prefix is null," ",p.prefix)," ",u.userfullname) as empname FROM main_employees e 
			INNER JOIN main_users u ON e.user_id = u.id 
			left JOIN main_prefix p ON e.prefix_id = p.id left JOIN main_jobtitles j ON j.id = u.jobtitle_id
			WHERE e.user_id = "'.$loginUserId.'" AND u.isactive IN (1,2,3,4,0) AND u.userstatus ="old";';

		$res = $db->query($qryStr)->fetch();
		return $res;
	}

	function getUrl($menu_id= '')
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$res='';
		if($menu_id !='')
		{
			$qryStr = "select url from main_menu where id=$menu_id and isactive=1 limit 0,1;";
			$res = $db->query($qryStr)->fetch();
			return $res;
		}
	}
	
	/**
	 * 
	 * Get job title name
	 * @param Integer $job_title_id - Job title ID
	 */
	public function getJobTitleName($job_title_id="") {
		
		$query = "SELECT jobtitlename FROM main_jobtitles WHERE id = $job_title_id LIMIT 0,1";
		return $this->db->query($query)->fetch();
	}
 
} 
?>