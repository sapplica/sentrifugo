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

class Default_Model_Employee extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_employees';
    protected $_primary = 'id';		

	/*
	   I. This query fetches employees data based on roles.
	   II. If roles are not configured then to eliminate users and other vendors we are using jobtitle clause.
	       As for jobtitle id for vendors and users will always be null.
	*/
     public function getEmployeesData($sort,$by,$pageNo,$perPage,$searchQuery,$managerid='',$loginUserId)
    {
    	$auth = Zend_Auth::getInstance();
    	$request = Zend_Controller_Front::getInstance();
     	if($auth->hasIdentity()){
					$loginUserGroup = $auth->getStorage()->read()->group_id;
					$loginUserRole = $auth->getStorage()->read()->emprole;
		}
		$controllerName = $request->getRequest()->getControllerName();
        //the below code is used to get data of employees from summary table.
        $employeesData=""; 
        if($controllerName=='employee' && ($loginUserRole == SUPERADMINROLE || $loginUserGroup == HR_GROUP || $loginUserGroup == MANAGEMENT_GROUP))                            
        	$where = "  e.isactive != 5 AND e.user_id != ".$loginUserId." ";
        else	  
        	$where = "  e.isactive = 1 AND e.user_id != ".$loginUserId." ";
        
        if($managerid !='')
            $where .= " AND e.reporting_manager = ".$managerid." ";
        if($searchQuery != '')
            $where .= " AND ".$searchQuery;

        $employeesData = $this->select()
                                ->setIntegrityCheck(false)	                                
                                ->from(array('e' => 'main_employees_summary'),
                                        array('*','id'=>'e.user_id','extn'=>new Zend_Db_Expr('case when e.extension_number is not null then concat(e.office_number," (ext ",e.extension_number,")") when e.extension_number is null then e.office_number end'),'astatus'=> new Zend_Db_Expr('case when e.isactive = 0 then "Inactive" when e.isactive = 1 then "Active" when e.isactive = 2 then "Resigned"  when e.isactive = 3 then "Left" when e.isactive = 4 then "Suspended" end')
                                            ))                               
                                ->where($where)
                                ->order("$by $sort") 
                                ->limitPage($pageNo, $perPage);
        
        
                    
       
        return $employeesData;       		
    }
	 
	public function getEmployees($managerid='',$loginUserId,$limit,$offset,$search_val,$search_str,$role_id)
    {
    	$auth = Zend_Auth::getInstance();
    	$request = Zend_Controller_Front::getInstance();
     	if($auth->hasIdentity()){
					$loginUserGroup = $auth->getStorage()->read()->group_id;
					$loginUserRole = $auth->getStorage()->read()->emprole;
		}
		$controllerName = $request->getRequest()->getControllerName();
        //the below code is used to get data of employees from summary table.
        $employeesData=""; 
        if($controllerName=='employee' && ($loginUserRole == SUPERADMINROLE || $loginUserGroup == HR_GROUP || $loginUserGroup == MANAGEMENT_GROUP))                            
        	$where = "  e.isactive != 5 AND e.user_id != ".$loginUserId." ";
        else	  
        	$where = "  e.isactive = 1 AND e.user_id != ".$loginUserId." ";
        
        if($managerid !='')
            $where .= " AND e.reporting_manager = ".$managerid." ";
		if($search_val !='')
		{
			if($search_val == 'emp_name')
			{
				$search_str_l=ltrim($search_str);
				$search_str_r=rtrim($search_str_l);
				$where .= " AND e.userfullname like '%".$search_str_r."%' ";
			}
			else if($search_val == 'emp_id')
			{
				$where .= " AND e.employeeId = '".$search_str."' ";
			}
			else
			{
				$where .= " AND e.emprole = '".$role_id."' ";
			}
		}
	
       $employeesData = $this->select()
                                ->setIntegrityCheck(false)	                                
                                ->from(array('e' => 'main_employees_summary'),
                                        array('*','id'=>'e.user_id','extn'=>new Zend_Db_Expr('case when e.extension_number is not null then concat(e.office_number," (ext ",e.extension_number,")") when e.extension_number is null then e.office_number end'),'astatus'=> new Zend_Db_Expr('case when e.isactive = 0 then "Inactive" when e.isactive = 1 then "Active" when e.isactive = 2 then "Resigned"  when e.isactive = 3 then "Left" when e.isactive = 4 then "Suspended" end')
                                            ))                               
                                ->where($where)
                                ->limit($limit, $offset)
								->order('e.modifieddate desc');
   
        return $this->fetchAll($employeesData)->toArray();  		
    }
	public function getUserRole()
	{	
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$usersData = $db->query("select GROUP_CONCAT(r.id) as roles from main_roles As r Inner join main_groups As g  on r.group_id=g.id 
        where r.isactive=1 AND g.id IN(".MANAGER_GROUP.",".HR_GROUP.",".EMPLOYEE_GROUP.",".SYSTEMADMIN_GROUP.",".MANAGEMENT_GROUP.")");
      
		$usersResult = $usersData->fetchAll();
		
		return $usersResult;
	}
	/**
         * This function gives full employee details based on user id.
         * @param integer $id  = id of employee
         * @return array  Array of employee details.
         */
	public function getsingleEmployeeData($id)
	{
		
            $db = Zend_Db_Table::getDefaultAdapter();
            $empData = $db->query("SELECT e.*,u.*,p.prefix,p.isactive as active_prefix FROM main_employees e 
                INNER JOIN main_users u ON e.user_id = u.id left JOIN main_prefix p ON e.prefix_id = p.id
                           WHERE e.user_id = ".$id."   AND  u.isactive IN (1,2,3,4,0) AND u.userstatus ='old'");
            $res = $empData->fetchAll();
            if (isset($res) && !empty($res)) 
            {	
                return $res;
            }
            else
                return 'norows';
	}
 
        /**
         * This function is used to get data in employees report.
         * @param array $param_arr   = array of parameters.
         * @param integer $per_page  = no.of records per page
         * @param integer $page_no   = page number
         * @param string $sort_name  = name of the column to be sort
         * @param string $sort_type  = descending or ascending
         * @return array  Array of all employees.
         */
        public function getdata_emp_report($param_arr,$per_page,$page_no,$sort_name,$sort_type)
        {
	        $search_str = " e.isactive != 5 ";
	  
            foreach($param_arr as $key => $value)
            {
                    if($value != '')
                    {
                            if($key == 'date_of_joining')
                   
			    $search_str .= " and ".$key." = '".sapp_Global::change_date ($value,'database')."'";
                            	
                            if($key == 'isactive')
                            $search_str .= " and e.".$key." = '".$value."'";
                            if( ($key == 'businessunit_id' || $key === 'department_id'))
                            {
                                if(is_array($value))
                                {                                    
                                    $search_str .= " and ".$key." in (".  implode(',', $value).")";
                                }
                            }
                            else
                            {
                            	if($key != 'isactive'&& $key != 'date_of_joining')
                            		$search_str .= " and ".$key." = '".$value."'";
                            }	
                    }
            }
            
            
            $offset = ($per_page*$page_no) - $per_page;
         
            $db = Zend_Db_Table::getDefaultAdapter();
            $limit_str = " limit ".$per_page." offset ".$offset;
            
            $count_query = "select count(*) cnt from main_employees_summary e where ".$search_str;
      
            $count_result = $db->query($count_query);
            $count_row = $count_result->fetch();
            $count = $count_row['cnt'];
            $page_cnt = ceil($count/$per_page);
            
            $query = "select e.*,es.salary,p.freqtype,c.currencyname, case when e.isactive = 0 then 'Inactive' when e.isactive = 1 then 'Active' when e.isactive = 2 then 'Resigned'  when e.isactive = 3 then 'Left' when e.isactive = 4 then 'Suspended' end isactive"
                    . " from main_employees_summary e 
                        left join main_empsalarydetails es on es.user_id = e.user_id  "
                    . " left join main_currency c on c.id = es.currencyid "
                    . " left join main_payfrequency p on p.id = es.salarytype "
                    . "where ".$search_str." "
                    . "order by ".$sort_name." ".$sort_type." ".$limit_str;
            $result = $db->query($query);
            $rows = $result->fetchAll();
            return array('rows' => $rows,'page_cnt' => $page_cnt, 'count_emp' => $count);
        }
    /**
     * This function is used to get data for pop up in groups,roles and employees report
     * @param Integer $group_id    = id of the group
     * @param Integer $role_id     = id of the role
     * @param Integer $page_no     = page number
     * @param String $sort_name    = field name to be sort
     * @param String $sort_type    = sort type like asc,desc
     * @return Array Array of employees of given role and group
     */
    public function emprolesgrouppopup($group_id,$role_id,$page_no,$sort_name,$sort_type,$per_page)
    {
        $offset = ($per_page*$page_no) - $per_page;
        $db = Zend_Db_Table::getDefaultAdapter();
        $limit_str = " limit ".$per_page." offset ".$offset;
        if($group_id == USERS_GROUP)
        {
            if($role_id != '')
            {
                $role_str = " and emprole in (".$role_id.")";
            }
            else 
            {
                $role_str = " and emprole in (select id from main_roles where group_id = ".$group_id." and isactive = 1)";
            }
            $count_query = "select count(*) cnt from main_users where isactive = 1 ".$role_str;
            $count_result = $db->query($count_query);
            $count_row = $count_result->fetch();
            $count = $count_row['cnt'];
            $page_cnt = ceil($count/$per_page);
            $query = "select r.rolename rolename_p,u.userfullname,u.employeeId,u.emailaddress from main_users u,main_roles r where r.id = u.emprole and u.isactive = 1 ".$role_str." order by ".$sort_name." ".$sort_type." ".$limit_str;
            $result = $db->query($query);
            $rows = $result->fetchAll();
            return array('rows' => $rows,'page_cnt' => $page_cnt);
        }
        else 
        {
            if($role_id != '')
            {
                $role_str = " and emprole in (".$role_id.")";
            }
            else 
            {
                $role_str = " and emprole in (select id from main_roles where group_id = ".$group_id." and isactive = 1)";
            }
            $count_query = "select count(*) cnt from main_employees_summary where isactive = 1 ".$role_str;
            $count_result = $db->query($count_query);
            $count_row = $count_result->fetch();
            $count = $count_row['cnt'];
            $page_cnt = ceil($count/$per_page);
            $query = "select * from main_employees_summary where isactive = 1 ".$role_str." order by ".$sort_name." ".$sort_type." ".$limit_str;
            $result = $db->query($query);
            $rows = $result->fetchAll();
            return array('rows' => $rows,'page_cnt' => $page_cnt);
        }
    }
	public function SaveorUpdateEmployeeData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_employees');
			return $id;
		}
		
	}
	
	public function getActiveEmployeeData($id)
    {
    	$result =  $this->select()
    				->setIntegrityCheck(false) 	
    				->from(array('e'=>'main_employees'),array('e.*'))
 	  				->where("e.isactive = 1 AND e.user_id = ".$id);
	//echo $result;
    	return $this->fetchAll($result)->toArray();
    }
    public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$exParam1='',$exParam2='',$exParam3='',$exParam4='')
    {
        $searchQuery = '';
        $tablecontent = '';
        $emptyroles=0;
        $empstatus_opt = array();
        $searchArray = array();
        $data = array();
        $id='';
        $dataTmp = array();
        
    	$auth = Zend_Auth::getInstance();
    	$request = Zend_Controller_Front::getInstance();
     	if($auth->hasIdentity()){
					$loginUserGroup = $auth->getStorage()->read()->group_id;
					$loginUserRole = $auth->getStorage()->read()->emprole;
		}
        $controllerName = $request->getRequest()->getControllerName();
		if($controllerName=='employee' && ($loginUserRole == SUPERADMINROLE || $loginUserGroup == HR_GROUP || $loginUserGroup == MANAGEMENT_GROUP))
			$filterArray = array(''=>'All',1 => 'Active',0 => 'Inactive',2 => 'Resigned',3 => 'Left',4 => 'Suspended');
		else
			$filterArray = array(''=>'All',1 => 'Active');
		
        if($searchData != '' && $searchData!='undefined')
        {
            $searchValues = json_decode($searchData);
			
            foreach($searchValues as $key => $val)
            {				
               
                    if($key == 'astatus')
                    $searchQuery .= " e.isactive like '%".$val."%' AND ";
                else if($key == 'extn')					
                    $searchQuery .= " concat(e.office_number,' (ext ',e.extension_number,')') like '%".$val."%' AND ";
                else 
                    $searchQuery .= $key." like '%".$val."%' AND ";				
                $searchArray[$key] = $val;
            }
            $searchQuery = rtrim($searchQuery," AND");					
        }
        $objName = 'employee';
				        
			
        $tableFields = array('action'=>'Action','firstname'=>'First Name','lastname'=>'Last Name','emailaddress'=>'Email',
                             'employeeId' =>'Employee ID','businessunit_name' => 'Business Unit','department_name' => 'Department','astatus' =>'User Status','extn'=>'Work Phone',
                             'jobtitle_name'=>'Job Title','reporting_manager_name'=>'Reporting Manager','contactnumber'=>'Contact Number',
                             'emp_status_name' =>'Employment Status','emprole_name'=>"Role");
		   
        $tablecontent = $this->getEmployeesData($sort,$by,$pageNo,$perPage,$searchQuery,'',$exParam1);  
			
        if($tablecontent == "emptyroles")
        {
            $emptyroles=1;
        }
        else
        {	
            $employmentstatusModel = new Default_Model_Employmentstatus();
            $employmentStatusData = $employmentstatusModel->getempstatuslist();	
            
            if(count($employmentStatusData) >0)
            {
                foreach($employmentStatusData as $empsdata)
                {
                    $empstatus_opt[$empsdata['workcodename']] = $empsdata['statusname'];
                }
            }
        }
		
        $dataTmp = array(
                        'userid'=>$id,
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
                        'menuName' => 'Employees',
                        'dashboardcall'=>$dashboardcall,
                        'add'=>'add',
                        'call'=>$call,
                        'search_filters' => array(
                                                'astatus' => array('type'=>'select',
                                                'filter_data'=>$filterArray),
                                                'emp_status_id'=>array(
                                                                        'type'=>'select',
                                                                        'filter_data' => array(''=>'All')+$empstatus_opt),
                                                ),
                        'emptyroles'=>$emptyroles
                    );	
				
        return $dataTmp;
    }
	
    public function getAutoReportEmp($search_str)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select * from ((select u.id user_id,u.profileimg,concat(u.userfullname,if(j.jobtitlename is null,'',concat(' , ',j.jobtitlename))) emp_name,
                  case when u.userfullname like '".$search_str."%' then 4  when u.userfullname like '__".$search_str."%' then 2 
                  when u.userfullname like '_".$search_str."%' then 3 when u.userfullname like '%".$search_str."%' then 1 
                  else 0 end emp 
                  from main_users u left join main_jobtitles j on j.id = u.jobtitle_id  
                  and j.jobtitlename like '%".$search_str."%'  where  u.isactive =1 and u.jobtitle_id is not null 
                  and (u.userfullname like '%".$search_str."%' or u.emailaddress like '%".$search_str."%') 
                    )
                    union (select u.id user_id,u.profileimg,concat(u.userfullname,', Super Admin') emp_name ,
                    case when u.userfullname like '".$search_str."%' then 4 when u.userfullname like '__".$search_str."%' then 2 when u.userfullname like '_".$search_str."%' then 3 
                    when u.userfullname like '%".$search_str."%' then 1 else 0 end emp from main_users u where u.id = 1 and 
                    (u.userfullname like '%".$search_str."%' or 'Super Admin' like '%".$search_str."%' or u.emailaddress like '%".$search_str."%') )
                    ) a
                  order by emp desc
                  limit 0,10";
        $result = $db->query($query);
        $emp_arr = array();
        $emp_arr = $result->fetchAll();
        return $emp_arr;
    }
	
    /**
     * 
     * Show auto suggestions to user in employees reporing to manager report
     * @param $search_str
     */
    public function getAutoReportEmployee($params)
    {
    	$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
     	}				
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select u.id user_id, u.emailaddress, u.userfullname, u.profileimg,u.employeeId FROM main_users u 
        		  inner join main_employees_summary e on u.id=e.user_id	
        		  WHERE u.".$params['field']." LIKE '%".$params['term']."%' AND e.reporting_manager = $loginUserId
        		  order by u.emailaddress desc limit 0,10";
        $result = $db->query($query);
        $emp_arr = array();
        $emp_arr = $result->fetchAll();
        return $emp_arr;
    }
    	
	public function getEmployeesUnderRM($empid,$bunit='',$deptid='',$eligibility='')
	{
		/***
		*** edited on 12-03-2015 , soujanya
		*** for filtering employees based on business unit id in Initialize appraisal > step -2
		***/
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select * from main_employees_summary where reporting_manager = ".$empid." and isactive = 1 ";
		if(!empty($bunit)) $query.=' and businessunit_id = '.$bunit;
		if(!empty($deptid)) $query.=' and department_id = '.$deptid;
		if(!empty($eligibility)) $query.=' and emp_status_id IN ('.$eligibility.') ';
		
		$result = $db->query($query);
        $emp_arr = array();
        $emp_arr = $result->fetchAll();
        return $emp_arr;
	}
	
	public function getCurrentOrgHead()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select user_id from main_employees where is_orghead = 1 and isactive = 1";
		$result = $db->query($query);
        $emp_arr = array();
        $emp_arr = $result->fetchAll();
        return $emp_arr;
	}
	
	public function changeRM($oldRM,$newRM,$status,$ishead)
	{		
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$db = Zend_Db_Table::getDefaultAdapter();	
		$db->beginTransaction();
		$oldRMData = $this->getsingleEmployeeData($oldRM);
		try
		{				
			if($status == 'active')
			{
				$data = array(
					'isactive' => 1,
					'emptemplock' => 0,
					'modifieddate' => gmdate("Y-m-d H:i:s"),
					'modifiedby' => $loginUserId
				);
                            $Query1 = "UPDATE main_employees SET isactive = 1, modifieddate = '".gmdate("Y-m-d H:i:s")."', modifiedby = ".$loginUserId." WHERE user_id=".$oldRM." ;";				
                            $db->query($Query1);
			}
			else if($status == 'inactive')
			{
				$data = array(
					'isactive' => 0,
					'emptemplock' => 1,
					'modifieddate' => gmdate("Y-m-d H:i:s"),
					'modifiedby' => $loginUserId
				);
			}
			$where = "id = ".$oldRM;
			$user_model =new Default_Model_Usermanagement();
			$result = $user_model->SaveorUpdateUserData($data, $where);
			
			if($status == 'inactive')
			{
				$empQuery1 = "UPDATE main_employees SET reporting_manager = ".$newRM.", modifieddate = '".gmdate("Y-m-d H:i:s")."', modifiedby = ".$loginUserId." WHERE reporting_manager=".$oldRM." and isactive = 1 AND user_id <> ".$newRM.";";
				
				$empQuery2 = "UPDATE main_employees SET reporting_manager = ".$oldRMData[0]['reporting_manager'].", modifieddate = '".gmdate("Y-m-d H:i:s")."', modifiedby = ".$loginUserId." WHERE reporting_manager=".$oldRM." and isactive = 1 AND user_id = ".$newRM.";";
				
				
				if($ishead == '1')
				{
					$orgQuery1 = "UPDATE main_employees SET is_orghead = 0,isactive = 0, reporting_manager= ".$newRM.", modifieddate = '".gmdate("Y-m-d H:i:s")."', modifiedby = ".$loginUserId." WHERE user_id=".$oldRM." ;";				
					$db->query($orgQuery1);
					
					$orgQuery2 = "UPDATE main_employees SET is_orghead = 1,reporting_manager= 0, modifieddate = '".gmdate("Y-m-d H:i:s")."', modifiedby = ".$loginUserId." WHERE user_id=".$newRM." ;";				
					$db->query($orgQuery2);
				}
				$db->query($empQuery1);
				$db->query($empQuery2);
			}
			$db->commit();
			return 'success';
		}
		catch(Exception $e)
		{			
			return 'failed';
			$db->rollBack();
		}
	}
	
	public function getEmployeesForOrgHead($userid = '')
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		if($userid == '')
		{
			$qry_str = " SELECT u.id, concat(u.userfullname,if(j.jobtitlename is null,'',concat(' , ',j.jobtitlename))) as name,u.profileimg 
                                FROM main_users u 
                                INNER JOIN main_roles r ON u.emprole = r.id 
                                INNER JOIN main_employees e ON u.id = e.user_id 
                                LEFT join main_jobtitles j on j.id = e.jobtitle_id 
                                WHERE  r.group_id IN (".MANAGEMENT_GROUP.")  AND u.userstatus='old' AND u.isactive=1 AND r.isactive=1 order by name asc";
		}
		else
		{
			$qry_str = " SELECT u.id, concat(u.userfullname,if(j.jobtitlename is null,'',concat(' , ',j.jobtitlename))) as name,u.profileimg 
                                FROM main_users u 
                                INNER JOIN main_roles r ON u.emprole = r.id 
                                INNER JOIN main_employees e ON u.id = e.user_id 
                                LEFT join main_jobtitles j on j.id = e.jobtitle_id 
                                WHERE  r.group_id IN (".MANAGEMENT_GROUP.")  AND u.userstatus='old' AND u.isactive=1 AND r.isactive=1 AND u.id <> ".$userid." order by name asc";
		}
		$reportingManagersData = $db->query($qry_str);
        $res = $reportingManagersData->fetchAll();
		return $res;
	}
	
	public function getEmployeesForServiceDesk($bunitid='',$deptid='')
	{
		$where = 'e.isactive=1 AND r.group_id IN (2,3,4,6)';
		if($bunitid != '' && $bunitid !='null')
			$where .= ' AND e.businessunit_id = '.$bunitid.'';
		if($deptid !='' && $deptid !='null')
			$where .= ' AND e.department_id = '.$deptid.'';	
		
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$qry = "select e.userfullname,e.user_id,e.emprole from main_employees_summary e 
				left join main_roles r on r.id=e.emprole and r.isactive=1
				left join main_privileges p  on e.emprole = p.role  and p.isactive=1 and p.object = ".SERVICEDESK." where ".$where." group by e.user_id order by e.userfullname asc";
		$res = $db->query($qry)->fetchAll();
		return $res;
		
	}
	
	public function getApproverForServiceDesk($bunitid='',$deptid='',$empstring='')
	{
		$where = 'e.isactive=1 AND r.group_id =1';
			   
			   if($empstring !='' && $empstring !='null')
			    $where.=' AND e.user_id NOT IN('.$empstring.')';	
		
		$db = Zend_Db_Table::getDefaultAdapter();
		/*** modified on 18-08-2015 ***
		*** to fix the issue when job title is empty ***
		*** query is returning empty userfullname ***
		***/
		$qry = "select case when e.jobtitle_name !='' then concat(e.userfullname,concat(' , ',e.jobtitle_name)) else e.userfullname end as userfullname,e.user_id,e.emprole from main_employees_summary e 
				inner join main_roles r on r.id=e.emprole and r.isactive=1
				inner join main_privileges p  on e.emprole = p.role  and p.isactive=1 and p.object = ".SERVICEDESK." where ".$where." group by e.id order by e.userfullname asc";
		
		
		$res = $db->query($qry)->fetchAll();
		return $res;
		
	}
	
	public function getEmployeeDetails($useridstring='')
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$emparr = array();
		if($useridstring !='' && $useridstring !='null')
		{
			$qry = "select e.userfullname,e.user_id,e.emprole from main_employees_summary e where e.user_id IN(".$useridstring.")";
			$res = $db->query($qry)->fetchAll();
		}
		if(!empty($res))
		{
			foreach($res as $resArr)
			{
				$emparr[$resArr['user_id']]= $resArr['userfullname'];
			}
		}
		return $emparr;
		
	}
	
	public function getIndividualEmpDetails($userid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
			$qry = "select e.userfullname,e.user_id,e.emprole from main_employees_summary e where e.user_id = ".$userid." ";
			$res = $db->query($qry)->fetch();
		return $res;
		
	}
        public function getEmp_from_summary($userid)
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $qry = "select e.* from main_employees_summary e where e.user_id = ".$userid." ";
            $res = $db->query($qry)->fetch();
            return $res;		
	}
        
        public function getPrefix_emp_excel()
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $parray = array();
            $query = "select prefix,id from main_prefix where isactive = 1";
            $res = $db->query($query)->fetchAll();
            if(!empty($res))
            {
                foreach($res as $row)
                {
                    $parray[strtolower($row['prefix'])] = $row['id'];
                }
            }
            return $parray;
        }
        
        public function getRoles_emp_excel()
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $parray = array();
            $query = "select roletype,id from main_roles where isactive = 1";
            $res = $db->query($query)->fetchAll();
            if(!empty($res))
            {
                foreach($res as $row)
                {
                    $parray[strtolower($row['roletype'])] = $row['id'];
                }
            }
            return $parray;
        }
        
        public function getMngRoles_emp_excel()
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $parray = array();
            $query = "select roletype,id from main_roles where isactive = 1 and group_id = 1";
            $res = $db->query($query)->fetchAll();
            if(!empty($res))
            {
                foreach($res as $row)
                {                    
                    $parray[$row['id']] = strtolower($row['roletype']);
                }
            }
            return $parray;
        }
        
        public function getBU_emp_excel()
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $parray = array();
            $query = "select unitcode,id from main_businessunits where isactive = 1";
            $res = $db->query($query)->fetchAll();
            if(!empty($res))
            {
                foreach($res as $row)
                {
                    $parray[strtolower($row['unitcode'])] = $row['id'];
                }
            }
            return $parray;
        }
        
        public function getPayfrequency_excel()
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $parray = array();
            $query = "select id,freqcode from main_payfrequency where isactive = 1";
            $res = $db->query($query)->fetchAll();
            if(!empty($res))
            {
                foreach($res as $row)
                {
                    $parray[strtolower($row['freqcode'])] = $row['id'];
                }
            }
            return $parray;
        }
        public function getCurrency_excel()
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $parray = array();
            $query = "select id,currencycode from main_currency where isactive = 1";
            $res = $db->query($query)->fetchAll();
            if(!empty($res))
            {
                foreach($res as $row)
                {
                    $parray[strtolower($row['currencycode'])] = $row['id'];
                }
            }
            return $parray;
        }
        
        public function getDep_emp_excel()
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $parray = array();
            $query = "select deptcode,id from main_departments where isactive = 1";
            $res = $db->query($query)->fetchAll();
            if(!empty($res))
            {
                foreach($res as $row)
                {
                    $parray[strtolower($row['deptcode'])] = $row['id'];
                }
            }
            return $parray;
        }
        
        public function getJobs_emp_excel()
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $parray = array();
            $query = "select jobtitlecode,id from main_jobtitles where isactive = 1";
            $res = $db->query($query)->fetchAll();
            if(!empty($res))
            {
                foreach($res as $row)
                {
                    $parray[strtolower($row['jobtitlecode'])] = $row['id'];
                }
            }
            return $parray;
        }
        
        public function getPositions_emp_excel()
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $parray = array();
            $query = "select positionname,id from main_positions where isactive = 1";
            $res = $db->query($query)->fetchAll();
            if(!empty($res))
            {
                foreach($res as $row)
                {
                    $parray[strtolower($row['positionname'])] = $row['id'];
                }
            }
            return $parray;
        }
        
        public function getUsers_emp_excel()
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $parray = array();
            $query = "select employeeId,user_id from main_employees_summary where isactive = 1";
            $res = $db->query($query)->fetchAll();
            if(!empty($res))
            {
                foreach($res as $row)
                {
                    $parray[strtolower($row['employeeId'])] = $row['user_id'];
                }
            }
            return $parray;
        }
        
        public function getEstat_emp_excel()
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $parray = array();
            $query = "select workcode,workcodename from main_employmentstatus where isactive = 1";
            $res = $db->query($query)->fetchAll();
            if(!empty($res))
            {
                foreach($res as $row)
                {
                    $parray[strtolower($row['workcode'])] = $row['workcodename'];
                }
            }
            return $parray;
        }
        public function getDOLEstat_emp_excel()
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $parray = array();
            $query = "select workcode,workcodename from main_employmentstatus where isactive = 1 and workcodename in (8,9,10)";
            $res = $db->query($query)->fetchAll();
            if(!empty($res))
            {
                foreach($res as $row)
                {
                    $parray[$row['workcodename']] = strtolower($row['workcode']);
                }
            }
            return $parray;
        }
        
        public function getEmps_emp_excel()
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $earray = array();
            $iarray = array();
            // $query = "select user_id,emailaddress,employeeId from main_employees_summary where isactive = 1";
            $query = "select user_id,emailaddress,employeeId from main_employees_summary";
            $res = $db->query($query)->fetchAll();
            if(!empty($res))
            {
                foreach($res as $row)
                {                    
                    $iarray[$row['user_id']] = strtolower($row['employeeId']);
                }
            }
            
            $query = "select user_id,emailaddress,employeeId from main_employees_summary";
            $res = $db->query($query)->fetchAll();
            if(!empty($res))
            {
                foreach($res as $row)
                {
                    $earray[$row['user_id']] = strtolower($row['emailaddress']);                    
                }
            }
            return array('email' => $earray,'ids' => $iarray);
        }
        
        public function getEmpsDeptWise()
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $emp_array = array();
            $query = "select ifnull(department_id,0) department_id,employeeId from main_employees_summary where isactive = 1";
            $res = $db->query($query)->fetchAll();
            if(!empty($res))
            {
                foreach($res as $row)
                {                    
                    $emp_array[$row['department_id']][] = strtolower($row['employeeId']);
                }
            }
            return $emp_array;
        }
        
        public function getDeptBUWise()
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $emp_array = array();
            $query = "select unitid,deptcode from main_departments where isactive = 1";
            $res = $db->query($query)->fetchAll();
            if(!empty($res))
            {
                foreach($res as $row)
                {                    
                    $emp_array[$row['unitid']][] = strtolower($row['deptcode']);
                }
            }
            return $emp_array;
        }
        
        public function getPosJTWise()
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $emp_array = array();
            $query = "select jobtitleid,positionname from main_positions where isactive =1 order by jobtitleid";
            $res = $db->query($query)->fetchAll();
            if(!empty($res))
            {
                foreach($res as $row)
                {                    
                    $emp_array[$row['jobtitleid']][] = strtolower($row['positionname']);
                }
            }
            return $emp_array;
        }
        
        /**
         * 
         * Get count of employees reporing to manager
         * @$manager_id interger - ID of reporting manager
         */
        public function getCountEmpReporting($manager_id = "") {
        	$db = Zend_Db_Table::getDefaultAdapter();
			$count_query = "select count(id) cnt from main_employees_summary e where e.isactive != 5 and reporting_manager = '$manager_id'";
            $count_result = $db->query($count_query);
            $count_row = $count_result->fetch();
            return $count_row['cnt'];        	
        }
		public function getMngmntEmployees()
		{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "	select es.id,es.user_id,es.userfullname,es.emailaddress from main_employees_summary es where es.user_id in (
					select s.user_id from main_employees_summary s 
					inner join main_roles r on s.emprole = r.id where r.group_id = 1  and r.isactive=1 ) and es.isactive =1 ;";
		$data = $db->query($query)->fetchAll();
		return $data;
		
		}
    public function checkemployeeidexist($employeeId,$where_condition)
    {
      $db = Zend_Db_Table::getDefaultAdapter();
      $query = "select count(id) as emp_count from main_users where employeeId='".$employeeId."' $where_condition ";
      $result = $db->query($query);
      $data = $result->fetch();
      return $data['emp_count'];
    }
    // to get hr employees
    public function getHrEmployees()
    {
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$query = "	select es.id,es.user_id,es.userfullname from main_employees_summary es where es.user_id in (
					select s.user_id from main_employees_summary s
					inner join main_roles r on s.emprole = r.id where r.group_id = 3  and r.isactive=1 ) and es.isactive =1 ;";
    	$data = $db->query($query)->fetchAll();
    	return $data;
    
    }

}
?>