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

class Default_Model_Requisition extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_requisition';
    protected $_primary = 'id';
    
    /**
     * This function gives data for grid view.
     * @parameters
     * @param sort          = ascending or descending
     * @param by            = name of field which to be sort  
     * @param pageNo        = page number
     * @param perPage       = no.of records per page
     * @param searchQuery   = search string
     * 
     * @return  ResultSet;
     */
    public function getRequisitionData($sort, $by, $pageNo, $perPage,$searchQuery,$userid,$usergroup,$req_type,$filter='')
    {
        $where = "r.isactive = 1 ";
		if($req_type == 3)
			$where .= " AND r.req_status = 3 ";
		else if($req_type == 2)
		{
			if($filter !='')
			{
			   if($filter == 'Approved'){
				 $where .=" AND r.req_status = 2 ";
			   }else if($filter == 'Closed'){
				 $where .=" AND r.req_status = 4 ";
			   }else if($filter == 'On hold'){
				 $where .=" AND r.req_status = 5 ";
			   }else if($filter == 'Complete'){
				 $where .=" AND r.req_status = 6 ";
			   }else if($filter == 'In process'){
				 $where .=" AND r.req_status = 7 ";
			   }
			   else $where .= " AND r.req_status in (2,4,5,6,7) ";
			}else{
				$where .= " AND r.req_status in (2,4,5,6,7) ";
			}	
		}
		else
			$where .= " AND r.req_status = 1 ";
			
        if($searchQuery)
            $where .= " AND ".$searchQuery; 
			
		if($usergroup == MANAGEMENT_GROUP || $usergroup == HR_GROUP)
			$where .= "";
		else if((  $usergroup == MANAGER_GROUP ) && $req_type == 1)
			$where .= " AND (r.createdby = ".$userid." or (".$userid." in (approver1,approver2,approver3) and 'Initiated' in (case when approver1 = ".$userid." then appstatus1 when approver2 = ".$userid." then appstatus2 when approver3 = ".$userid." then appstatus3 end)) )";
		else if($usergroup == MANAGER_GROUP && $req_type == 2)
			$where .= " AND r.createdby = ".$userid." ";
		else if($usergroup == MANAGER_GROUP && $req_type == 3)
			$where .= " AND r.createdby = ".$userid." ";
		

		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;			
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		} 
	
        $roleData = $this->select()
                        ->setIntegrityCheck(false)
                        ->from(array('r'=>"main_requisition_summary"),array('id'=>'req_id','requisition_code'=>'requisition_code',
                               'onboard_date'=>'date_format(r.onboard_date,"'.DATEFORMAT_MYSQL.'")',
                               'req_no_positions'=>'r.req_no_positions','req_status'=>'r.req_status',
                               'filled_positions'=>'r.filled_positions','r.createdon'=>'DATE_FORMAT(r.createdon,"'.DATEFORMAT_MYSQL.'")' ,
                                'appstatus1' => new Zend_Db_Expr("if(appstatus1 ='Initiated',null,appstatus1)"),
                                'appstatus2' => new Zend_Db_Expr("if(appstatus2 ='Initiated',null,appstatus2)"),
                                'appstatus3' => new Zend_Db_Expr("if(appstatus3 ='Initiated',null,appstatus3)"),
                                'position_name','reporting_manager_name','businessunit_name','department_name','jobtitle_name',
                                'emp_type_name','approver1_name','approver2_name','approver3_name','createdby_name'
                            ))
                       
                        ->where($where)
                        ->order("$by $sort") 
                        ->limitPage($pageNo, $perPage);
        return $roleData;       		
    }
    
    public function getOpeningrequisitionsCount($req_type)
    {
		
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$userid = $auth->getStorage()->read()->id;			
			$usergroup = $auth->getStorage()->read()->group_id;
		} 
		
    	$db = Zend_Db_Table::getDefaultAdapter();
 
    	if($req_type==2)
    	{
    		$where = "r.isactive = 1  AND r.req_status = 2";
    	}
    	if($req_type==3)
    	{
    		$where = "r.isactive = 1  AND r.req_status = 3";
    	}
    	if($req_type==1)
    	{
    		$where = "r.isactive = 1  AND r.req_status = 1";
    	}
		
		if($usergroup == MANAGEMENT_GROUP  || $usergroup == HR_GROUP)
			$where .= "";
		else if((  $usergroup == MANAGER_GROUP) && $req_type == 1)
			$where .= " AND (r.createdby = ".$userid." or (".$userid." in (approver1,approver2,approver3) and 'Initiated' in (case when approver1 = ".$userid." then appstatus1 when approver2 = ".$userid." then appstatus2 when approver3 = ".$userid." then appstatus3 end)) )";
		else if($usergroup == MANAGER_GROUP && $req_type == 2)
			$where .= " AND r.createdby = ".$userid." ";
		else if($usergroup == MANAGER_GROUP && $req_type == 3)
			$where .= " AND r.createdby = ".$userid." ";
		
		
    	$requisitionsData = $this->select()
		->setIntegrityCheck(false)
		->from(array('r'=>$this->_name), array("count"=>"count(*)"))
		->where($where);
		return $db->fetchRow($requisitionsData);
    }
	
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$loginUserId,$loginuserGroup,$reqtype,$objName,$dashboardcall)
	{
		$searchQuery = '';
        $searchArray = array();
        $data = array();
		if($searchData != '' && $searchData!='undefined')
		{
			$searchValues = json_decode($searchData);
			if(count($searchValues) >0)
			{
				foreach($searchValues as $key => $val)
				{
					if($key == 'onboard_date' || $key == 'r.createdon')
					{
							$searchQuery .= " date(".$key.") = '".  sapp_Global::change_date($val,'database')."' AND ";
					}
					else
							$searchQuery .= " ".$key." like '%".$val."%' AND ";
					$searchArray[$key] = $val;
				}
				$searchQuery = rtrim($searchQuery," AND");					
			}
		}		

        $tableFields = array('action'=>'Action',
                             'requisition_code' => 'Requisition Code',
                            'jobtitle_name' => 'Job Title',
                            'req_status'=>'Requisition Status',
                            'createdby_name'	=> 'Raised By',
                            //'reporting_manager_name' => 'Reporting Manager',
                            //'approver1_name' => 'Approver -1',
                             //'appstatus1' =>'Status',
                             //'approver2_name' => 'Approver -2',
                            // 'appstatus2' =>'Status',
                            // 'approver3_name' => 'Approver -3',
                             //'appstatus3' =>'Status',
                               'req_no_positions' => 'No. of Positions',
                             'filled_positions' => 'Filled Positions',
                          //  'r.createdon'=> 'Raised On',
                          //  'onboard_date' => 'Due Date',
                            );
		$tablecontent = $this->getRequisitionData($sort, $by, $pageNo, $perPage,$searchQuery,$loginUserId,$loginuserGroup,$reqtype);     
        $status_arr = array(''=>'All','Initiated' => 'Initiated','Approved' => 'Approved',
                            'Rejected' => 'Rejected','Closed' => 'Closed','On hold' => 'On hold',
                            'Complete' => 'Complete','In process' => 'In process');
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
                'add' =>'add',
				'menuName' => 'Openings/Positions',
                'searchArray' => $searchArray,
                'call'=>$call,
				'dashboardcall'=>$dashboardcall,
                'search_filters' => array(
                    'r.createdon' =>array('type'=>'datepicker'),
                    'onboard_date'=>array('type'=>'datepicker'),
                    'req_status' => array(
                        'type' => 'select',
                        'filter_data' => $status_arr,
                    ),
                    'appstatus1' => array(
                        'type' => 'select',
                        'filter_data' => $status_arr,
                    ),
                    'appstatus2' => array(
                        'type' => 'select',
                        'filter_data' => $status_arr,
                    ),
                    'appstatus3' => array(
                        'type' => 'select',
                        'filter_data' => $status_arr,
                    ),
                ),
        );	
		return $dataTmp;
	}
    public function getRequisitionForEdit($id,$login_id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select p.*,if(".$login_id." in (approver1,approver2,approver3),'approver','creator') aflag,
                  case when approver1 = ".$login_id." then '1' when approver2 = ".$login_id." then '2' when approver3 = ".$login_id." then '3' end aorder,j.client_name
                  from ".$this->_name." p
				 
				  left join tm_clients j on j.id = p.client_id and j.is_active = 1 
				  where p.isactive = 1 and p.id =".$id;
        $result = $db->query($query);
        $row = $result->fetch();
        return $row;
    }
    /**
     * This function will return all data of requisition by passing its primay key id.
     * @parameters
     * @param Integer $id   =  id of requisition (primary key)
     * 
     * @return Array  Array of requisition's data.
     */
    public function getRequisitionDataById($id)
    {
    	$row = $this->fetchRow("id = '".$id."' and isactive = 1 ");
        if (!$row) 
        {
            throw new Exception("Problem in requisition model");
        }
        return $row->toArray();
    }
    public function change_to_requisition_closed($id)
    {
        $candidate_model = new Default_Model_Candidatedetails();
        $interview_model = new Default_Model_Interviewdetails();
        $iround_model = new Default_Model_Interviewrounddetails();
        
        $candidate_data = array('cand_status' => 'Requisition Closed/Completed');
        $candidate_where = "requisition_id = ".$id." and isactive =1 and cand_status not in ('Recruited','Rejected','On hold','Disqualified')";

        $interview_data = array('interview_status' => 'Requisition Closed/Completed');
        $interview_where = "isactive = 1 and req_id = ".$id." and interview_status != 'Completed'";

        $iround_data = array('round_status' => 'Requisition Closed/Completed');
        $iround_where = "isactive = 1 and req_id = ".$id." and (round_status not in ('Schedule for next round','Qualified','Selected') or round_status is null)";
        
        if($id != '')
        {
            $candidate_model->SaveorUpdateCandidateData($candidate_data, $candidate_where);
            $interview_model->SaveorUpdateInterviewData($interview_data, $interview_where);
            $iround_model->SaveorUpdateInterviewroundData($iround_data, $iround_where);
        }
    }
    /**
     * This function is used to save/update data in database.
     * @parameters
     * @param data  =  array of form data.
     * @param where =  where condition in case of update.
     * 
     * @return {String}  Primary id when new record inserted,'update' string when a record updated.
     */
    public function SaveorUpdateRequisitionData($data, $where)
    {
		
        if($where != '')
        {
            $this->update($data, $where);
            return 'update';
        }
        else 
        {
            $this->insert($data);
            $id=$this->getAdapter()->lastInsertId($this->_name);
            return $id;
        }
    }
    
    /**
     * This function is used to get maximum requisition code.
     * @return {String} Max requisition code.
     */
    public function getMaxReqCode($prefix)
    {
		/** added on 10-08-2015 
		** fix for requisition id generation in add screen
		**/
		$len = strlen($prefix)+1;
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select max(cast(substr(requisition_code,".$len.") as unsigned))  req_id from main_requisition";
        $result = $db->query($query);
        $row = $result->fetch();
        $req_id = $row['req_id'];
        
        if($req_id == '')
        {
            $final_req_code = $prefix."001";
        }
        else 
        {
            $final_req_code = $prefix.  str_pad(($req_id +1), 3, '0', STR_PAD_LEFT);
        }
		return $final_req_code;
                
    }
    /**
     * This function gives all active requisition codes with specified status.
     * @param $req_status = requisition status.
     * @return Array Array of all requisition codes.
     */
    public function getReqCodes($req_status,$flag = '')
    {
        if($flag != '')
                 $data = $this->fetchAll("isactive = 1 and req_status in ('Approved','In process')")->toArray();
        else 
            $data = $this->fetchAll("isactive = 1 and req_status = '".$req_status."' ")->toArray();
        return $data;
    }
    
    /**
     * This function gives full data for approve requisition based on requisition id.
     * @param Integer $id = id of requisition.
     * @return array Array of data.
     */
    public function getAppReqById($id)
    {
        $req_arr = array();
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select r.onboard_date,r.req_skills,r.req_qualification,r.req_exp_years,
                  case when r.req_priority = 1 then 'High' when r.req_priority = 2 then 'Medium' 
                  when r.req_priority = 3 then 'Low' end req_priority ,e.emp_name,p.positionname,b.unitname,
                  d.deptname,j.jobtitlename,j.jobdescription,r.additional_info,es.workcodename emp_type,
                  r.req_no_positions 
                  from main_requisition r 
                  left join main_positions p on p.id = r.position_id and p.isactive = 1 
                  inner join main_employees e on e.id = r.reporting_id and e.isactive = 1 
                  inner join main_businessunits b on b.id = p.busineesunitid and b.isactive = 1 
                  inner join main_departments d on d.id = p.departmentid and d.isactive = 1 
                  left join main_jobtitles j on j.id = p.jobtitleid and j.isactive = 1 
                  inner join main_employmentstatus es on es.id = r.emp_type and es.isactive = 1 
                  where r.id = ".$id;
        $result = $db->query($query);
        $req_arr = $result->fetch();
        return $req_arr;
    }
	/**
	*	This function gives the list of all reporting managers, if the resource request is raised by HR
	*	and only his own data, if the request is raised by the project manager
	**/
    public function getReportingmanagers($role = '',$loginid,$id='',$dept='',$flag = '')
    {       
        $db = Zend_Db_Table::getDefaultAdapter();
		$roles = '';
		/* Get the roles who have requisition permissions */
		if($flag == 'interviewer')
		{
			$roleIdsData = $db->query("select role from main_privileges where editpermission = 'Yes' AND viewpermission = 'Yes' AND object = ".SCHEDULEINTERVIEWS." 
										AND role is not null AND group_id in (".MANAGER_GROUP.",".HR_GROUP.",".EMPLOYEE_GROUP.",".SYSTEMADMIN_GROUP.")");				
			
			$roleIds = $roleIdsData->fetchAll();
			$roles = '';
			foreach($roleIds as $row) {
				$roles .= ",".$row['role'];
			}
			$roles = substr($roles,1);
                        if($roles != '')
			$roles =  "AND r.id IN (".$roles.")";
		}
		/* Get the roles END */
		if($id!='')
		{
			$managerData = $db->query("select * from main_users where id = ".$id." AND userstatus='old';");
			return $emp_options = $managerData->fetch();
		}
		else
		{
			if($role == HR_GROUP || $role == '' || $role == MANAGEMENT_GROUP)
			{				
				if($loginid != '')
					$whereidText = " AND e.user_id != ".$loginid;
				else
					$whereidText = "";
				if($dept != '')
					$wheredeptText = " AND e.department_id = ".$dept;
				else
					$wheredeptText = "";
                                $query = "select app.id,app.name,app.profileimg from (SELECT u.id, concat(u.userfullname,if(j.jobtitlename is null,'',concat(' , ',j.jobtitlename))) as name,u.profileimg FROM main_users u 
						INNER JOIN main_roles r ON u.emprole = r.id 
						INNER JOIN main_employees e ON u.id = e.user_id 
                                                left join main_jobtitles j on j.id = e.jobtitle_id
						WHERE r.group_id IN (".MANAGER_GROUP.",".HR_GROUP.",".EMPLOYEE_GROUP.",".SYSTEMADMIN_GROUP.") 
						".$wheredeptText." ".$roles."
						AND u.userstatus='old' AND u.isactive=1 AND r.isactive=1  ".$whereidText."
						union 
						select u.id,concat(u.userfullname,if(j.jobtitlename is null,'',concat(' , ',j.jobtitlename))) as name,u.profileimg from main_users u
						INNER JOIN main_employees e ON u.id = e.user_id 											
						INNER JOIN main_roles r ON u.emprole = r.id 
                                                left join main_jobtitles j on j.id = e.jobtitle_id
						where r.group_id = ".MANAGEMENT_GROUP."  AND u.isactive=1 AND r.isactive=1 ".$whereidText.") app order by app.name asc";
                                
				$managerData = $db->query($query);
				$emp_options = array();
                                $emp_options = $managerData->fetchAll();
                                				
			}else{
				
                                
				$managerData = $db->query("select u.id,u.userfullname as name from main_users u
										join main_roles r on u.emprole = r.id where r.group_id = ".MANAGER_GROUP." AND userstatus='old';");// AND u.id=".$loginid."
										
				$emp_options = $managerData->fetch();
			}
			
			return $emp_options;
		}
	}
    public function getapprovers($rept_id,$dept_id)
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {            
            $loginUserId = $auth->getStorage()->read()->id;	
        } 
        $db = Zend_Db_Table::getDefaultAdapter();
        $roleIdsData = $db->query("select role from main_privileges 
                                  where editpermission = 'Yes' AND viewpermission = 'Yes' 
                                  AND object = ".RESOURCEREQUISITION." 
                                  AND role is not null AND group_id in (".MANAGER_GROUP.",".HR_GROUP.")");
        $roleIds = $roleIdsData->fetchAll();
        $roles = '';
        if(!empty($roleIds))
        {
            foreach($roleIds as $row) 
            {
                $roles .= ",".$row['role'];
            }
            $roles = substr($roles,1);
            $roles =  "AND r.id IN (".$roles.")";
        }
           $query = "select app.id,app.name,app.profileimg from (SELECT u.id, concat(u.userfullname,if(j.jobtitlename is null,'',concat(' , ',j.jobtitlename))) as name,u.profileimg FROM main_users u 
                    INNER JOIN main_roles r ON u.emprole = r.id 
                    INNER JOIN main_employees e ON u.id = e.user_id 
                    left join main_jobtitles j on j.id = e.jobtitle_id
                    WHERE r.group_id IN (".MANAGER_GROUP.",".HR_GROUP.") 
                    AND e.department_id = ".$dept_id." ".$roles."
                    AND u.userstatus='old' AND u.isactive=1 AND r.isactive=1   AND e.user_id not in (".$rept_id.",".$loginUserId.")
                    union 
                    select u.id,concat(u.userfullname,if(j.jobtitlename is null,'',concat(' , ',j.jobtitlename))) as name,u.profileimg from main_users u
                    INNER JOIN main_employees e ON u.id = e.user_id 											
                    INNER JOIN main_roles r ON u.emprole = r.id 
                    left join main_jobtitles j on j.id = e.jobtitle_id
                    where r.group_id = ".MANAGEMENT_GROUP."  AND u.isactive=1 
                        AND r.isactive=1 AND e.user_id not in (".$rept_id.")) app order by app.name asc;";

            $result = $db->query($query);
            $options = $result->fetchAll();
        
        return $options;
    }
	/**
	**/
	public function getEmpStatusOptions($id='')
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		if($id!=''){
			$empstatusData = $db->query("select mn.id,tb.employemnt_status from main_employmentstatus mn
							inner join tbl_employmentstatus tb on mn.workcodename = tb.id where mn.id=".$id.";");
			return $data = $empstatusData->fetch();
		}
		else
		{
			$empstatusData = $db->query("select mn.id,tb.employemnt_status from main_employmentstatus mn
						inner join tbl_employmentstatus tb on mn.workcodename = tb.id where mn.isactive = 1;");
			$data = $empstatusData->fetchAll();
			$options = array();
			foreach($data as $emp)
			{
				$options[$emp['id']] = $emp['employemnt_status'];
			}
			return $options;
		}
		
	}
        public function getStatusOptionsForRequi()
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select mn.id,tb.employemnt_status 
                      from main_employmentstatus mn
                      inner join tbl_employmentstatus tb on mn.workcodename = tb.id 
                      where mn.isactive = 1 and tb.id not in (5,7,8,9,10) order by tb.employemnt_status;"; //mn.isactive = 1 and 
            $empstatusData = $db->query($query);
            $data = $empstatusData->fetchAll();
            $options = array();
            if(count($data)>0)
            {
                foreach($data as $emp)
                {
                    $options[$emp['id']] = $emp['employemnt_status'];
                }
            }
            return $options;
        }
	/**
	**/
	public function getBusinessUnitsList($id='')
	{
		$db = Zend_Db_Table::getDefaultAdapter();	
		if($id != '')
		{
			$BUData = $db->query("select * from main_businessunits where isactive = 1 AND id=".$id." order by unitname;");
			return $data = $BUData->fetch();
		}
		else
		{
			$BUData = $db->query("select * from main_businessunits where isactive = 1 order by unitname;");
			$data = $BUData->fetchAll();
			$options_arr = array();
			foreach($data as $option)
			{
				$options_arr[$option['id']] = $option['unitname'];
			}			
			return $options_arr;
		}
	}	
	/**
	**/
	public function getJobTitleList($id='')
	{	
		$db = Zend_Db_Table::getDefaultAdapter();	
		if($id!='')
		{
			$titlesData = $db->query("select * from main_jobtitles where isactive = 1 AND id=".$id." order by jobtitlename;"); //isactive = 1 AND 
			return $data = $titlesData->fetch();
		}
		else
		{
			$titlesData = $db->query("select * from main_jobtitles where isactive = 1 order by jobtitlename;");//where isactive = 1
			$data = $titlesData->fetchAll();
			$options_arr = array();
			foreach($data as $option)
			{
				$options_arr[$option['id']] = $option['jobtitlename'];
			}
			return $options_arr;
		}
	}
	
	/**
	**/
	public function getDepartmentList($bussinessunitid,$id='')
	{
	  
		$db = Zend_Db_Table::getDefaultAdapter();	
		if($id!='')
		{
			$empstatusData = $db->query("select * from main_departments where isactive = 1 AND unitid = ".$bussinessunitid." AND id=".$id." order by deptname;");
			return $data = $empstatusData->fetch();
		}
		else
		{
			$empstatusData = $db->query("select * from main_departments where isactive = 1 AND unitid = ".$bussinessunitid." order by deptname;");
			$data = $empstatusData->fetchAll();
			$options_arr = array();
			foreach($data as $option)
			{
				$options_arr[$option['id']] = $option['deptname'];
			}
			return $options_arr;
		}
	}
	
	/**
	**/
	public function getPositionOptions($jobtitle_id,$id='')
	{	   
		$db = Zend_Db_Table::getDefaultAdapter();	
		if($id!='')
		{
			$empstatusData = $db->query("select * from main_positions where id = ".$id." AND jobtitleid = ".$jobtitle_id." order by positionname;"); //isactive = 1 AND 
			return $data = $empstatusData->fetch();
		}
		else
		{
			$empstatusData = $db->query("select * from main_positions where jobtitleid = ".$jobtitle_id." order by positionname;");//isactive = 1 AND 
			$data = $empstatusData->fetchAll();
			$options_arr = array();
			foreach($data as $option)
			{
				$options_arr[$option['id']] = $option['positionname'];
			}
			return $options_arr;
		}
	}
	
	public function getrequisitioncreatername($userid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();	
		$userData = $db->query("select * from main_users where id = ".$userid." ;");
		return $data = $userData->fetch();
	}
	public function incrementselected_members($req_id)
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $get_query = "select selected_members from main_requisition where id = ".$req_id;
            $get_result = $db->query($get_query);
            $get_row = $get_result->fetch();
            
            $selected_mem = $get_row['selected_members'] +1;
            $query = "update main_requisition set selected_members = ".$selected_mem." where id=".$req_id;
            
            $result = $db->query($query);
        }
	public function incrementfilledpositions($id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query	=	$db->query("update main_requisition set filled_positions = filled_positions+1 where id=".$id.";");	
		$reqData = $db->query("select * from main_requisition where id = ".$id." ;");
		return $data = $reqData->fetch();
	}
        public function getRequisitionsForCV($filters)
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT `r`.`id`, `r`.`requisition_code`,j.jobtitlename,req_status
						FROM `main_requisition` AS `r` 
						LEFT JOIN main_jobtitles as j on j.id = r.jobtitle
                        left JOIN `main_positions` AS `p` ON p.id = r.position_id and p.isactive = 1 
                        INNER JOIN `main_users` AS `mu` ON mu.id = r.createdby and mu.isactive = 1 
                        INNER JOIN `main_users` AS `u` ON u.id = r.reporting_id and u.isactive = 1 
                        WHERE (r.isactive = 1 AND r.req_status in (".$filters.") ) order by r.requisition_code desc";
            $result = $db->query($query);
            $rows = $result->fetchAll();
            return $rows;
        }
        public function getReqForInterviews()
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select r.id,r.requisition_code,j.jobtitlename,r.department_id 
                      from main_requisition r 
                      inner join main_candidatedetails c on c.isactive = 1 
                      and c.cand_status = 'Not Scheduled' and r.id = c.requisition_id 
                       left join main_jobtitles j on j.id = r.jobtitle
                      where r.isactive = 1 and r.req_status = 'In process' group by r.id order by r.id desc";
            $result = $db->query($query);
            $rows = $result->fetchAll();
            return $rows;
        }
        public function getReqDataForView($req_id)
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            
            
            $query = "select * from main_requisition_summary where req_id = ".$req_id." and isactive = 1";
            $result = $db->query($query);
            $rows = $result->fetchAll();
            return $rows;
        }
        public function getnamejobtitle($user_array)
        {
            $jobname_arr = array();
            if(count($user_array) >0)
            {
                $db = Zend_Db_Table::getDefaultAdapter();
                $user_str = implode(',', $user_array);
                $query = "select user_id ,concat(userfullname,' ,',jobtitle_name) uname 
                          from main_employees_summary where user_id in (".$user_str.")";
                
                $result = $db->query($query);
                while($row = $result->fetch())
                {
                    $jobname_arr[$row['user_id']] = $row['uname'];
                }
            }
            return $jobname_arr;
        }
        public function getreq_for_interviewrpt()
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select r.id req_id,concat(r.requisition_code,', ',j.jobtitlename) req_name 
                      from main_requisition r 
                      inner join main_candidatedetails c on c.isactive = 1 and c.requisition_id = r.id 
                      left join main_jobtitles j on j.id = r.jobtitle 
                      where r.isactive = 1 and r.req_status not in ('Rejected','Initiated') 
                      group by req_id order by req_id ;";
            $result = $db->query($query);
            $rows = $result->fetchAll();
            return $rows;
        }
        public function getdata_for_interviewrpt($param_arr,$sort_name,$sort_type,$page_no,$per_page)
        {
            $search_str = "";
            foreach($param_arr as $key => $value)
            {
                if($value != '')
                {
                    if($key == 'interview_date')
                        $search_str .= " and ".$key." = '".sapp_Global::change_date ($value,'database')."'";				
                    else
                        $search_str .= " and ".$key." = '".$value."'";
                }
            }
           
            $offset = ($per_page*$page_no) - $per_page;
            $limit_str = " limit ".$per_page." offset ".$offset;
            $db = Zend_Db_Table::getDefaultAdapter();
            $count_query = "select count(*) cnt 
                            from main_requisition_summary rs 
                            inner join main_interviewrounds_summary ins on rs.req_id = ins.requisition_id and ins.isactive = 1 
                            where rs.isactive = 1  ".$search_str;
            $count_result = $db->query($count_query);
            $count_row = $count_result->fetch();
            $count = $count_row['cnt'];
            $page_cnt = ceil($count/$per_page);
            
            if(in_array($sort_name, array('req_status','round_status','candidate_status','interview_status')))            
                $sort_name = " cast(".$sort_name." as char(100))";
                        
            
            $query = "select concat(rs.requisition_code,' ,',rs.jobtitle_name) requisition_code,
                      rs.position_name,rs.reporting_manager_name,
                      rs.businessunit_name,rs.department_name,rs.req_status,ins.candidate_name,ins.candidate_status,
                      ins.interview_status,ins.interviewer_name,ins.interview_time, date_format(ins.interview_date,'".DATEFORMAT_MYSQL."') interview_date ,
                      ins.interview_mode,ins.interview_round_number,ins.interview_round_name,ins.interview_location,
                      ins.interview_city_name,ins.interview_state_name,ins.interview_country_name,ins.created_by_name,
                      ins.interview_feedback,ins.interview_comments,ins.round_status 
                      from main_requisition_summary rs 
                      inner join main_interviewrounds_summary ins on rs.req_id = ins.requisition_id and ins.isactive = 1 
                      where rs.isactive = 1 ".$search_str." order by ".$sort_name." ".$sort_type." ".$limit_str;
            $result = $db->query($query);
            $rows = $result->fetchAll();
            return array('rows' => $rows,'page_cnt' => $page_cnt);
        }
	public function getRequisitionStats()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select count(case when req_status = 'Initiated' then (id) end) as initiated_req, count(case when req_status = 'Approved' then (id) end) as approved_req, count(case when req_status = 'Rejected' then (id) end) as rejected_req,count(case when req_status = 'Closed' then (id) end) as closed_req,
count(case when req_status = 'On hold' then (id) end) as onhold_req, count(case when req_status = 'Complete' then (id) end) as complete_req, count(case when req_status = 'In process' then (id) end) as inprocess_req from main_requisition where isactive = 1 and year(createdon) = year(now());";
		$result = $db->query($query);
		$rows = $result->fetchAll();
		return $rows;
	}

	public function getUserloginStats()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select count(id) as cnt, day(logindatetime) as logindate from main_userloginlog group by day(logindatetime);"; 
		$result = $db->query($query);
		$rows = $result->fetchAll();
		return $rows;
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

	public function getCountRequisitions($searchQuery = 1)
	{
		$where = "1";

		if($searchQuery)
		$where .= " AND ".$searchQuery;

		$qry = "select count(*) as count from main_requisition where {$where}";
		$db = Zend_Db_Table::getDefaultAdapter();
		$sqlRes = $db->query($qry);
		$count = $sqlRes->fetchAll();
			
		return $count[0]['count'];
	}

	// To generate report
	
	/* 
	 * To get first year of Requisition raised
	 * To show it in Requisition Report filters 
	 */
	public function getYearFirstRequisitionRaised(){
		$db = Zend_Db_Table::getDefaultAdapter();
        $query = "select MIN(rs.createdon) first_requisition_raised_in from main_requisition_summary rs where 1";
        return $result = $db->query($query)->fetch();
	}
	
	public function getReportData($param_arr,$per_page,$page_no,$sort_name,$sort_type, $userid, $usergroup,$req_type)
	{
        $where = "isactive = 1 ";
			
		if($usergroup == MANAGEMENT_GROUP ){
			$where .= "";
		}else if((  $usergroup == MANAGER_GROUP || $usergroup == HR_GROUP) && $req_type == 1){
			$where .= " AND (rs.createdby = ".$userid." or (".$userid." in (approver1,approver2,approver3) and 'Initiated' in (case when approver1 = ".$userid." then appstatus1 when approver2 = ".$userid." then appstatus2 when approver3 = ".$userid." then appstatus3 end)) )";
		}else if($usergroup == MANAGER_GROUP && $req_type == 2){
			$where .= " AND rs.createdby = ".$userid." ";
		}else if($usergroup == MANAGER_GROUP && $req_type == 3){
			$where .= " AND rs.createdby = ".$userid." ";
		}
		
		// To remove unwanted post values in the query 
		$remove_elements = array('search_criteria', 'previous_search', 'hid_raised_by_name', 'hid_reporting_manager_name');
		foreach($remove_elements as $ele){
			if(isset($param_arr[$ele])){
				unset($param_arr[$ele]);
			}
		}
		
		if(!empty($param_arr)){
            foreach($param_arr as $field => $value){
	            if(!empty($value)){
		            switch($field){
		            	case 'createdon':
		                    $where .= " and YEAR(".$field.") = '".$value."'";
		                    break;
		            	default:
		                    $where .= " and ".$field." = '".$value."'";
		            }
                }
            }
		}
            
            
            // To get count of total requisitions
            if(!empty($per_page)){
	            $offset = ($per_page*$page_no) - $per_page;
	            $limit_str = " limit ".$per_page." offset ".$offset;
            }
            
            $count_query = "select count(id) cnt from main_requisition_summary rs where ".$where;
            
            $db = Zend_Db_Table::getDefaultAdapter();
            $count_result = $db->query($count_query);
            $count_row = $count_result->fetch();
            
            // To get requisitions data
            $count = $count_row['cnt'];
            $page_cnt = ceil($count/$per_page);

            // To sort Field of type Enum appropriately
            $enum_fields = array('req_status', 'appstatus1'); 
            if(in_array($sort_name, $enum_fields)){
            	$sort_name = 'CAST('.$sort_name.' AS CHAR)';
            }
            
            $query = "select rs.requisition_code requisition_code, rs.jobtitle_name job_title, rs.req_status req_status,  DATE_FORMAT(rs.onboard_date,'".DATEFORMAT_MYSQL."') onboard_date,  rs.reporting_manager_name reporting_manager_name, rs.approver1_name approver1, rs.appstatus1 appstatus1, rs.approver2_name approver2, rs.appstatus2 appstatus2, rs.approver3_name approver3, rs.appstatus3 appstatus3, rs.req_no_positions req_no_positions, rs.selected_members selected_members, rs.createdby_name created_by, DATE_FORMAT(rs.createdon,'".DATEFORMAT_MYSQL."') created_on from main_requisition_summary rs where $where order by $sort_name $sort_type $limit_str";
            $result = $db->query($query);
            $rows = $result->fetchAll();
            return array('rows' => $rows,'page_cnt' => $page_cnt);
    }      
    
    public function getAutoReportRequisition($search_str)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select DISTINCT(r.id) id, r.requisition_code requisition_code
                  from main_candidatedetails c LEFT JOIN main_requisition r ON c.requisition_id = r.id 
                  WHERE r.requisition_code like '%".$search_str."%' 
                  order by requisition_code desc
                  limit 0,10";
        $result = $db->query($query);
        $requisition_arr = array();
        $requisition_arr = $result->fetchAll();
        return $requisition_arr;
    }  
    
    /**
     * This function returns candidates and interviewers based on a particular requisition code.
     * @param integer $req_id          = id of requisition
     * @param integer $loginUserGroup  = id of group
     * @param integer $loginUserId     = id of logged user
     * @return array  Array of candidate options and interviewers options.
     */
    public function getcandidates_forinterview($req_id,$loginUserGroup,$loginUserId)
    {
        
        $jobtitleModel = new Default_Model_Jobtitles();
        $candsmodel = new Default_Model_Candidatedetails();			
				
        $reqData = $this->getRequisitionDataById($req_id);	
        $candsData = $candsmodel->getnotscheduledcandidateData($req_id);
        $candStr = '';$jobttlArr = array();$jobtitle = '';$managerStr = "";
		
        if(!empty($candsData))
        {
            foreach ($candsData as $cand)		                
                $candStr .= sapp_Global::selectOptionBuilder($cand['id'], $cand['candidate_name']);
        }
        else
        {
            $candStr = "nocandidates";
        }	
        
        if(!empty($reqData))
        {
            $reqData['jobtitlename'] = '';
            $jobttlArr = $jobtitleModel->getsingleJobTitleData($reqData['jobtitle']);
            if(!empty($jobttlArr) && $jobttlArr != 'norows')
            {	
                $jobtitle = $jobttlArr[0]['jobtitlename'];
                $reqData['jobtitlename'] = $jobttlArr[0]['jobtitlename'];
            }
			
            $repmanData = $this->getReportingmanagers($loginUserGroup,$loginUserId,'',$reqData['department_id'],'interviewer');
            if(!empty($repmanData))
            {				
                foreach ($repmanData as $rep)		                    
                    $managerStr .= sapp_Global::selectOptionBuilder($rep['id'], $rep['name'], $rep['profileimg']);
            }
            else
            {
                $managerStr = "nomanagers";
            }	
            
        }
        return array('candidates'=>$candStr,'managers'=>$managerStr,'jobtitle'=>$jobtitle);
    }
}//end of class