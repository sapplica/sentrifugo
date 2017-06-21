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

class Default_Model_Appraisalinit extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_pa_initialization';
    protected $_primary = 'id';
	
    public function checkappadmin($businessunit_id,$department_id)
    {                
        $db = Zend_Db_Table::getDefaultAdapter();
        $dept_str = "";
        if($department_id != '')
            $dept_str = " and i.department_id = '".$department_id."' ";
        
        $query = "select count(id) cnt from main_pa_initialization i 
                  where i.isactive = 1 and i.businessunit_id = '".$businessunit_id."'  and i.status = 1 ".$dept_str;
        $result = $db->query($query)->fetch();
        return $result['cnt'];
    }
    
    public function getbusinnessunits_admin($businessunit_id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $bu_str = "";
        if($businessunit_id != '')
            $bu_str = " and id = '".$businessunit_id."' ";
        /*$query = "select bu.id,bu.unitname,pa.performance_app_flag 
                  from main_pa_implementation pa inner join main_businessunits bu on bu.id = pa.businessunit_id 
                  where pa.isactive = 1 ".$bu_str."  group by pa.businessunit_id";*/
		$query = "select id,unitname from main_businessunits where isactive = 1 ".$bu_str."";
        $result = $db->query($query)->fetchAll();
        return $result;
    }
    
	public function getbusinnessunits_initialized()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        // $query = "select bu.id,bu.unitname,pa.performance_app_flag from main_pa_implementation pa 
                  // inner join main_pa_initialization pai on pai.pa_configured_id = pa.id
                  // inner join main_businessunits bu on bu.id = pa.businessunit_id 
                  // where pa.isactive = 1 and pai.isactive= 1 and pai.status=1 group by pa.businessunit_id";
		$query = "select bu.id,bu.unitname,pa.performance_app_flag,rt.id as rt_id 
						from main_pa_initialization pa 
						inner join main_businessunits bu on bu.id = pa.businessunit_id 
						left join main_pa_ratings rt on rt.pa_initialization_id = pa.id 
						where pa.isactive = 1 and pa.status=1 and rt.id is NULL
						group by pa.businessunit_id";
        $result = $db->query($query)->fetchAll();
        return $result;
    }
    
    public function getdeparmentsadmin($businessunit_id,$enable_step='',$dept_str='')
    {
        $db = Zend_Db_Table::getDefaultAdapter();
		$where_condition = '';
		if(!empty($dept_str))
		{
			$where_condition = " and d.id in ($dept_str) ";
		}
        // Filter departments by 'Process Status' in 'Manager Status' and 'Employee Status' screens
        if (!empty($enable_step)) {
        	/*$query = "select distinct d.id,d.deptname from main_pa_implementation pa 
        	inner join main_pa_initialization initi on initi.pa_configured_id = pa.id AND initi.enable_step = '$enable_step'  
        	inner join main_departments d on d.id = pa.department_id and d.isactive = 1 
        	where pa.isactive = 1 and pa.performance_app_flag = 0 and pa.businessunit_id = '".$businessunit_id."'";*/
			$query = "select d.id,d.deptname 
					from main_pa_initialization initi 
					inner join main_departments d on d.id = initi.department_id and d.isactive = 1 
					where initi.enable_step = $enable_step and initi.isactive = 1 and initi.businessunit_id=$businessunit_id $where_condition group by d.id";
        } else {
        	/*$query = "select distinct d.id,d.deptname from main_pa_implementation pa 
        	inner join main_departments d on d.id = pa.department_id and d.isactive = 1 
        	where pa.isactive = 1 and pa.performance_app_flag = 0 and pa.businessunit_id = '".$businessunit_id."'";*/
			$query = "select d.id,d.deptname 
					from main_departments d 
					where d.unitid=$businessunit_id $where_condition and d.isactive = 1";
        }
        $result = $db->query($query)->fetchAll();
        return $result;
    }
    
    public function discardsteptwo($init_id,$management_appraisal,$loginUserId,$loginuserRole,$loginuserGroup)
    {
        $status = "failure";
        $message = "Something went wrong, please try again.";
        $result = array('status' => $status,'message' => $message);
        if($init_id != '' && $management_appraisal != '')
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $qtemp_query = "update main_pa_questions_privileges_temp set isactive = 0 "
                    . "where pa_initialization_id = '".$init_id."'";
            $db->query($qtemp_query);            
            
            $mng_str = "";
            if($management_appraisal != 1)
                $mng_str = ",manager_level_type = null ";
            
            $init_query = "update main_pa_initialization set group_settings = 0".$mng_str." where id = '".$init_id."' ";
            $db->query($init_query);
            $result = array('status' => "success",'message' => "Step - 2 discarded successfully.");            
        }
        return $result;
    }
    public function submitmanager($appraisal_id,$manager_id)
    {
        if($appraisal_id != '' && $manager_id != '')
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "update main_pa_initialization set manager_ids = concat(ifnull(manager_ids,''),"
                    . "if(manager_ids is null,'".$manager_id."',',".$manager_id."')) "
                    . "where id = '".$appraisal_id."' and isactive = 1 and status = 1 and initialize_status = 1";
            $db->query($query);
        }
    }

    public function check_delete($id)
    {
        if($id != '')
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select sum(tot.cnt) cnt from ( (select count(id) cnt from main_pa_questions_privileges "
                    . "where isactive = 1 and pa_initialization_id = '".$id."') union (select count(id) cnt "
                    . "from main_pa_questions_privileges_temp where isactive = 1 and pa_initialization_id = '".$id."')) tot";
            $result = $db->query($query)->fetch();
            return $result['cnt'];
            
        }
        return '';
    }
    public function getappdata_forview($init_id)
    {
        $data = array();
        if($init_id != '')
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select b.unitname,d.deptname,group_concat(distinct te.employemnt_status) eligibility_names,
                      group_concat(distinct pac.category_name) category_names,pa.* 
                      from main_pa_initialization pa inner join main_businessunits b on b.id = pa.businessunit_id 
                      left join main_departments d on d.id = pa.department_id 
                      inner join tbl_employmentstatus te on find_in_set(te.id,pa.eligibility) 
                      inner join main_pa_category pac on find_in_set(pac.id,pa.category_id) 
                      where pa.id = '".$init_id."' and pa.isactive = 1 group by pa.id";
            $data = $db->query($query)->fetch();
        }
        return $data;
    }
    public function getAppDataById($init_id)
    {
        $data = array();
        if($init_id != '')
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select * from main_pa_initialization where id = '".$init_id."'";
            $data = $db->query($query)->fetch();
        }
        return $data;
    }
    public function getManagers_report($init_id)
    {
        $result = array();
        $db = Zend_Db_Table::getDefaultAdapter();
        $init_data = $this->getAppDataById($init_id);
        
        if($init_data['initialize_status'] == 1)
            $table_name = "main_pa_questions_privileges";
        else 
            $table_name = "main_pa_questions_privileges_temp";
                
       /**
	   ** edited by soujanya 19-03-2015
	   ** changed e2 to e1 for bunit conjunction and department conjunction
	   ** for organization heirarchy in initialization > step 2
	   **/
		$dept_str = "";
        if(!empty($init_data['department_id']))
            $dept_str = " and e1.department_id = '".$init_data['department_id']."' ";
        
       if($init_data['initialize_status'] == 1)
       {
       	
       		$query = "select e1.reporting_manager user_id,e2.userfullname,e2.jobtitle_name,e2.profileimg,
	                  count(distinct e1.user_id) emp_cnt,q.manager_levels,e2.employeeId  
	                  from main_employees_summary e1 inner join main_employees_summary e2 on  e1.reporting_manager = e2.user_id
	                  inner join ".$table_name." q on q.employee_id=e1.user_id and q.pa_initialization_id = '".$init_id."' 
	                  and q.isactive = 1 and e1.reporting_manager = q.line_manager_1 
	                  where e1.isactive = 1 and find_in_set(e1.emp_status_id,'".$init_data['eligibility']."') and e1.reporting_manager > 1 and e1.businessunit_id = '".$init_data['businessunit_id']."' "
	                . " ".$dept_str." group by e1.reporting_manager";
       	
       }else
       {     
	       $query = "select e1.reporting_manager user_id,e2.userfullname,e2.jobtitle_name,e2.profileimg,
	                  count(distinct e1.user_id) emp_cnt,q.manager_levels,e2.employeeId  
	                  from main_employees_summary e1 inner join main_employees_summary e2 on  e1.reporting_manager = e2.user_id
	                  left join ".$table_name." q on q.pa_initialization_id = '".$init_id."' 
	                  and q.isactive = 1 and e1.reporting_manager = q.line_manager_1 
	                  where e1.isactive = 1 and find_in_set(e1.emp_status_id,'".$init_data['eligibility']."') and e1.reporting_manager > 1 and e1.businessunit_id = '".$init_data['businessunit_id']."' "
	                . " ".$dept_str." group by e1.reporting_manager";
       }
        $result = $db->query($query)->fetchAll();
        return $result;
    }
    public function deletelinemanager($init_id,$manager_id,$loginUserId,$loginuserRole,$loginuserGroup)
    {
    	
        $result = array();
        if(!empty($init_id) && !empty($manager_id))
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "update main_pa_questions_privileges_temp set isactive = 0 "
                    . "where pa_initialization_id = '".$init_id."' and line_manager_1 = '".$manager_id."' ";
            $qresult = $db->query($query);
            $db->query("update main_pa_groups  set isactive=0 where pa_initialization_id = ".$init_id." ");
            $cquery = "select count(distinct line_manager_1) cnt from main_pa_questions_privileges_temp "
                    . "where pa_initialization_id = ".$init_id." and isactive = 1 ";
            $cresult = $db->query($cquery)->fetch();
            if($cresult['cnt'] == 0)
            {
                $data = array(
                    'group_settings' => 0,
                    'manager_level_type' => null,
                    'modifiedby' => $loginUserId,
                    'modifiedby_role' => $loginuserRole,
                    'modifiedby_group' => $loginuserGroup,
                    'modifieddate' => gmdate("Y-m-d H:i:s"),
                );
                
                $this->SaveorUpdateAppraisalInitData($data, "id = ".$init_id);
            }                        
            $result = array('status' => 'success','message' => 'L1 Manager discarded successfully.');                        
        }
        return $result;
    }
    public function deletereportmanager($init_id,$manager_id,$loginUserId,$loginuserRole,$loginuserGroup)
    {
        $result = array();
        if(!empty($init_id) && !empty($manager_id))
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "update main_pa_questions_privileges_temp set isactive = 0 "
                    . "where pa_initialization_id = '".$init_id."' and line_manager_1 = '".$manager_id."' ";
            $qresult = $db->query($query);
            
            $cquery = "select count(distinct line_manager_1) cnt from main_pa_questions_privileges_temp "
                    . "where pa_initialization_id = ".$init_id." and isactive = 1 ";
            $cresult = $db->query($cquery)->fetch();
            if($cresult['cnt'] == 0)
            {
                $data = array(
					'group_settings' => 0,
                    'manager_level_type' => null,
                    'modifiedby' => $loginUserId,
                    'modifiedby_role' => $loginuserRole,
                    'modifiedby_group' => $loginuserGroup,
                    'modifieddate' => gmdate("Y-m-d H:i:s"),
                );
                
                $this->SaveorUpdateAppraisalInitData($data, "id = ".$init_id);
            } 
            $result = array('status' => 'success','message' => 'Line managers discarded successfully.');                        
        }
        return $result;
    }
    public function getLineManagers($init_id,$line1_id)
    {
        $result = array();
        if(!empty($init_id) && !empty($line1_id))
        {
            $init_data = $this->getAppDataById($init_id);
        
            if($init_data['initialize_status'] == 1)
                $table_name = "main_pa_questions_privileges";
            else 
                $table_name = "main_pa_questions_privileges_temp";
            
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select line_manager_2,line_manager_3,line_manager_4,line_manager_5 "
                    . "from ".$table_name." "
                    . "where line_manager_1='".$line1_id."' and pa_initialization_id = '".$init_id."' "
                    . "and isactive = 1 group by line_manager_1";
            $result = $db->query($query)->fetch();
        }
        return $result;
    }    
	public function getLineManagers_new($init_id,$line1_id)
    {
        $result = array();
        if(!empty($init_id) && !empty($line1_id))
        {
            $init_data = $this->getAppDataById($init_id);
        
            if($init_data['initialize_status'] == 1)
                $table_name = "main_pa_questions_privileges";
            else 
                $table_name = "main_pa_questions_privileges_temp";
            
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select line_manager_1,line_manager_2,line_manager_3,line_manager_4,line_manager_5 "
                    . "from ".$table_name." "
                    . "where employee_id='".$line1_id."' and pa_initialization_id = '".$init_id."' "
                    . "and isactive = 1 ";
            $result = $db->query($query)->fetch();
        }
        return $result;
    }

    public function getdisplayacontentacc($init_id,$manager_id)
    {
        $data = array();
        if($init_id !== '' && $manager_id !== '')
        {
            $init_data = $this->getAppDataById($init_id);
        
            if($init_data['initialize_status'] == 1)
                $table_name = "main_pa_questions_privileges";
            else 
                $table_name = "main_pa_questions_privileges_temp";
            
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select q.pa_initialization_id,e.user_id,case when e.user_id = q.line_manager_2 then 'line_2' 
                      when e.user_id = q.line_manager_3 then 'line_3' when e.user_id = q.line_manager_4 then 'line_4' 
                      when e.user_id = q.line_manager_5 then 'line_5' else 'employee' end emp_type,e.userfullname,
                      e.profileimg,e.employeeId,e.jobtitle_name from ".$table_name." q,main_employees_summary e 
                      where q.isactive = 1 and q.pa_initialization_id = '".$init_id."' and "
                    . "e.user_id in ( q.employee_id,line_manager_2,line_manager_3,line_manager_4,line_manager_5 ) "
                    . "and q.line_manager_1 = '".$manager_id."' group by e.user_id";
            $data = $db->query($query)->fetchAll();
        }
        return $data;
    }
    public function getdisplayacontentreportacc($init_id,$manager_id)
    {
        $data = array();
        if($init_id !== '' && $manager_id !== '')
        {
			/***
			*** edited on 12-03-2015 , soujanya
			*** for filtering employees based on business unit id in Initialize appraisal > step -2
			***/
			//echo $init_id.">>".$manager_id;die;
			$appInitializationData = $this->getConfigData($init_id);
			if(!empty($appInitializationData))
			{
				$businessUnitId = $appInitializationData[0]['businessunit_id'];
				$departmentId = $appInitializationData[0]['department_id'];
				$eligibility = $appInitializationData[0]['eligibility'];
		        $emp_model = new Default_Model_Employee();
	            $data = $emp_model->getEmployeesUnderRM($manager_id,$businessUnitId,$departmentId,$eligibility);
			}
        }
        return $data;
    }
    public function getexist_line($init_id)
    {
        $data = array();
        if($init_id !== '')
        {
            $init_data = $this->getAppDataById($init_id);
        
            if($init_data['initialize_status'] == 1)
                $table_name = "main_pa_questions_privileges";
            else 
                $table_name = "main_pa_questions_privileges_temp";
            
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select e.profileimg,e.employeeId,e.jobtitle_name,q.pa_initialization_id,e.user_id,e.userfullname,q.manager_levels,count(q.employee_id) emp_cnt "
                    . "from ".$table_name." q,main_employees_summary e "
                    . "where q.isactive = 1 and q.pa_initialization_id = '".$init_id."' "
                    . "and e.user_id = q.line_manager_1 group by q.line_manager_1";
            $data = $db->query($query)->fetchAll();
        }
        return $data;
    }
    public function getInitExistEmp($init_id,$line1_id)
    {
        $data = array();
        if($init_id !== '')
        {
            $init_data = $this->getAppDataById($init_id);
        
            if($init_data['initialize_status'] == 1)
                $table_name = "main_pa_questions_privileges";
            else 
                $table_name = "main_pa_questions_privileges_temp";
            
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select e.employeeId,e.jobtitle_name,e.user_id,concat(e.userfullname,ifnull(concat(' - ',e.jobtitle_name),'')) userfullname,e.profileimg "
                    . "from main_employees_summary e,".$table_name." q  where q.isactive = 1  and e.user_id = q.employee_id"
                    . " and q.pa_initialization_id = '".$init_id."' and q.line_manager_1 = '".$line1_id."' group by e.user_id order by userfullname";
            $data = $db->query($query)->fetchAll();
        }
        return $data;
    }
    public function getEmpInit($init_id,$init_data,$selected_managers_arr)
    {
        $data = array();
        if($init_id !== '')
        {                                   
            if($init_data['initialize_status'] == 1)
                $table_name = "main_pa_questions_privileges";
            else 
                $table_name = "main_pa_questions_privileges_temp";
            
            $businessunit_id = $init_data['businessunit_id'];
            $department_id = $init_data['department_id'];
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select employee_id "
                    . "from ".$table_name." where pa_initialization_id = '".$init_id."' and isactive = 1 ";
            $exist_result = $db->query($query)->fetchAll();
            $exist_emp = array();
            if(!empty($exist_result))
            {                
                foreach($exist_result as $exist)
                {                                                                
                    $exist_emp[] = $exist['employee_id'];                    
                }
            }
            $exist_emp = array_merge($exist_emp,$selected_managers_arr);
            $exist_str = '';
			$exist_emps = '';
			$exist_emps = implode(',', $exist_emp);
			$exist_emps = rtrim($exist_emps,',');
            if(!empty($exist_emp))
            {
                $exist_str = " and e.user_id not in (".$exist_emps.")";                
            }
            $emp_status_str = "";
            if(!empty($init_data))
            {
                $emp_status_str = " and find_in_set(e.emp_status_id,'".$init_data['eligibility']."') ";
            }
            $condition = "";
            if($businessunit_id != '')
                $condition .= " and e.businessunit_id = '".$businessunit_id."' ";
            if(!empty($department_id))
                $condition .= " and e.department_id = '".$department_id."' ";
            
				$query = "select e.employeeId,e.user_id,e.jobtitle_name,e.userfullname,e.profileimg "
                    . "from main_employees_summary e,main_roles r  where r.id = e.emprole and e.isactive = 1 "
                    . "and r.isactive = 1 and r.group_id NOT IN (".USERS_GROUP.",".MANAGEMENT_GROUP.") "
                    . $exist_str." ".$emp_status_str." ".$condition." group by e.user_id order by userfullname";

			$data = $db->query($query)->fetchAll();
        }
        return $data;
    }
    public function getRepManagers_report($line1_id,$init_id,$employeeids,$businessunit_id,$department_id)
    {
        $data = array();
        $empids = '';
        if($line1_id != '' && $init_id != '')
        {
            $init_data = $this->getAppDataById($init_id);
        
            if($init_data['initialize_status'] == 1)
                $table_name = "main_pa_questions_privileges";
            else 
                $table_name = "main_pa_questions_privileges_temp";
            $db = Zend_Db_Table::getDefaultAdapter();
            $exist_str = "";
			$tmpempIds = '';
			$tmpempIds = $line1_id.",".$employeeids;
			$tmpempIds = rtrim($tmpempIds,',');
			$exist_str = " and e.user_id not in (".$tmpempIds.")";
			$bstr = '';
			if(isset($businessunit_id) && $businessunit_id != '' && $businessunit_id != 0)
			{
				$bstr .= " and e.businessunit_id = ".$businessunit_id;
			}
			if(isset($department_id) && $department_id != '' && $department_id != 0)
			{
				$bstr .= " and e.department_id = ".$department_id;            
			}
			$query = "(select e.user_id,concat(e.userfullname,ifnull(concat(' - ',e.jobtitle_name),'')) userfullname "
                    . "from main_employees_summary e,main_roles r  where r.id = e.emprole and e.isactive = 1 "
                    . "and r.isactive = 1 and r.group_id in (".MANAGEMENT_GROUP.",".CUSTOM_GROUP.") ".$exist_str." group by e.user_id order by userfullname)
					union
					(select e.user_id,concat(e.userfullname,ifnull(concat(' - ',e.jobtitle_name),'')) userfullname "
                    . "from main_employees_summary e,main_roles r  where r.id = e.emprole and e.isactive = 1 "
                    . "and r.isactive = 1 and r.group_id =".MANAGER_GROUP." $exist_str $bstr group by e.user_id order by userfullname)";
            $data = $db->query($query)->fetchAll();            
        }
        return $data;
    }
    public function getRepManagers($type,$init_id,$init_data)
    {
        $data = array();
        if($type != '' && $init_id != '')
        {
            $init_data = $this->getAppDataById($init_id);
        
            if($init_data['initialize_status'] == 1)
                $table_name = "main_pa_questions_privileges";
            else 
                $table_name = "main_pa_questions_privileges_temp";
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select line_manager_1 "
                    . "from ".$table_name." where pa_initialization_id = '".$init_id."' and isactive = 1 "
                    . "group by line_manager_1";
            $exist_result = $db->query($query)->fetchAll();
            $exist_emp = array();
            if(!empty($exist_result))
            {                
                foreach($exist_result as $exist)
                {
                    for($i=1;$i<=5;$i++)
                    {
                        if(!empty($exist['line_manager_'.$i]))
                            $exist_emp[] = $exist['line_manager_'.$i];
                    }
                }
            }
            $exist_str = "";
            if(!empty($exist_emp))
            {
                $exist_str = " and e.user_id not in (".implode(',', $exist_emp).")";                
            }
            if($type === 'line')
            {                
                $bstr = "";
                if($init_data['businessunit_id'] != '')
                    $bstr .= " and e.businessunit_id = ".$init_data['businessunit_id'];
                if($init_data['department_id'] != '')
                    $bstr .= " and e.department_id = ".$init_data['department_id'];
                
                $query = "(select e.user_id,concat(e.userfullname,ifnull(concat(' - ',e.jobtitle_name),'')) userfullname "
                        . "from main_employees_summary e,main_roles r  where r.id = e.emprole and e.isactive = 1 "
                        . "and r.isactive = 1 and r.group_id = ".MANAGEMENT_GROUP." ".$exist_str." "
                        . "group by e.user_id order by userfullname) union ".
                        "  (select e.user_id,concat(e.userfullname,ifnull(concat(' - ',e.jobtitle_name),'')) userfullname "
                        . "from main_employees_summary e,main_roles r  where r.id = e.emprole and e.isactive = 1 "
                        . "and r.isactive = 1 and r.group_id in (".CUSTOM_GROUP.",".MANAGER_GROUP.") ".$exist_str." ".$bstr."  "
                        . "group by e.user_id order by userfullname)";                
                $data = $db->query($query)->fetchAll();
            }
        }
        return $data;
    }    
	public function getRepManagers_new($type,$init_id,$init_data,$employeeIds)
    {
        $data = array();
        if($type != '' && $init_id != '')
        {
            $init_data = $this->getAppDataById($init_id);
            $db = Zend_Db_Table::getDefaultAdapter();
            if($type === 'line')
            {                
                $bstr = "";
                $empwhere = "";
                if($init_data['businessunit_id'] != '')
                    $bstr .= " and e.businessunit_id = ".$init_data['businessunit_id'];
                if($init_data['department_id'] != '')
                    $bstr .= " and e.department_id = ".$init_data['department_id'];
                    
                if($employeeIds!='')
                	$empwhere .=" and e.user_id NOT IN($employeeIds)";    
                
                $query = "(select e.user_id,concat(e.userfullname,ifnull(concat(' - ',e.jobtitle_name),'')) userfullname "
                        . "from main_employees_summary e,main_roles r  where r.id = e.emprole and e.isactive = 1 "
                        . "and r.isactive = 1 and r.group_id = ".MANAGEMENT_GROUP." group by e.user_id order by userfullname) union ".
                        "  (select e.user_id,concat(e.userfullname,ifnull(concat(' - ',e.jobtitle_name),'')) userfullname "
                        . "from main_employees_summary e,main_roles r  where r.id = e.emprole and e.isactive = 1 "
                        . "and r.isactive = 1 $empwhere and r.group_id in (".CUSTOM_GROUP.",".MANAGER_GROUP.") ".$bstr."  "
                        . "group by e.user_id order by userfullname)";                
                $data = $db->query($query)->fetchAll();
            }
        }
        return $data;
    }
    public function getperiod($bunit,$from_year,$to_year,$mode,$dept_id='')
    {
		$where = '';
		if(is_numeric($dept_id) && $dept_id > 0)
		{
			$where = " and department_id=$dept_id ";
		}
        $db = Zend_Db_Table::getDefaultAdapter();
        /*$query = "select ifnull(max(ifnull(appraisal_period,0)),0)+1 app_period 
                  from main_pa_initialization where isactive = 1 and businessunit_id = '".$bunit."' $where
                  and appraisal_mode = '".$mode."' and from_year = '".$from_year."' and to_year = '".$to_year."' and status=2 ";*/
		$query = "select ifnull(max(ifnull(appraisal_period,0)),0)+1 app_period 
                  from main_pa_initialization where isactive = 1 and businessunit_id = '".$bunit."' $where
                   and from_year = '".$from_year."' and to_year = '".$to_year."' and status=2 ";
        $result = $db->query($query)->fetch();
        return $result['app_period'];
    }
    public function check_per_implmentation($businessunit_id,$department_id)
    {
    	$result = array();
    	$flag = '';
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select * from main_pa_implementation where businessunit_id = ".$businessunit_id." and isactive = 1";
        $result = $db->query($query)->fetch();
        if(!empty($result))
        {
        	$flag = $result['performance_app_flag'];
        }	
        if($flag!='' && $flag == 0)
        {
            $query1 = "select * from main_pa_implementation where businessunit_id = ".$businessunit_id." and department_id = ".$department_id." and isactive = 1";
            $result1 = $db->query($query1)->fetch();
            return $result1;
        }
        else 
            return $result;
        
    }
    
	public function check_performance_implmentation($businessunit_id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select * from main_pa_implementation "
            . "where businessunit_id = ".$businessunit_id." and isactive = 1";
        
        $result = $db->query($query)->fetch();
        return $result;
        
    }
    public function getAppraisalInitData($sort, $by, $pageNo, $perPage,$searchQuery)
    {
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $businessunit_id = $auth->getStorage()->read()->businessunit_id;
            $department_id = $auth->getStorage()->read()->department_id; 
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
        }
        $where = " ai.isactive = 1 ";
        if($loginuserRole != SUPERADMINROLE && $loginuserGroup != MANAGEMENT_GROUP && $loginuserGroup != HR_GROUP)
        {
            $init_data = $this->check_per_implmentation($businessunit_id, $department_id);
            $where .= " and ai.businessunit_id = '".$businessunit_id."'  ";
            if($init_data['performance_app_flag'] == 0)
                $where .= " and ai.department_id = '".$department_id."' ";
        }
        if($searchQuery)
            $where .= " AND ".$searchQuery;
        $db = Zend_Db_Table::getDefaultAdapter();
        $appInitData = $this->select()
                            ->setIntegrityCheck(false)	
                            ->from(array('ai'=>'main_pa_initialization'),array('ai.id','ai.status as statusval',
                                new Zend_Db_Expr("concat(from_year,'-',to_year) as fin_year"),'ai.appraisal_mode',
                                new Zend_Db_Expr("CASE WHEN ai.status=1 THEN 'Open' WHEN ai.status=2 THEN 'Closed' ELSE 'Force Closed' END as status "),
								new Zend_Db_Expr("case when initialize_status = 1 then case when ai.enable_step = 1 then 'Enabled to Managers' when ai.enable_step = 2 then 'Enabled to Employees' end when initialize_status = 2 then 'Initialize later' when initialize_status is null then 'In progress' end as appraisal_process_status"),
                                new Zend_Db_Expr("case when ai.appraisal_mode = 'Quarterly' then concat('Q',ai.appraisal_period) when ai.appraisal_mode = 'Half-yearly' then concat('H',ai.appraisal_period) when ai.appraisal_mode = 'Yearly' then 'Yearly' end as app_period"),
                                ))
                            ->joinLeft(array('b' => 'main_businessunits'),"b.id = ai.businessunit_id and b.isactive=1",array('unitname'))
                            ->joinLeft(array('d' => 'main_departments'),"d.id = ai.department_id and d.isactive=1",array('deptname'))
                            ->where($where)
                            ->order("$by $sort") 
                            ->limitPage($pageNo, $perPage);
       //echo $appInitData;
		return $appInitData;       		
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
				$sval = $val;
				$val = mysql_real_escape_string($val);
                if($key == 'fin_year')
                    $searchQuery .= " concat(from_year,'-',to_year) like '%".$val."%' AND ";
                else if($key == 'app_period')
                    $searchQuery .= " lower(case when ai.appraisal_mode = 'Quarterly' then concat('Q',ai.appraisal_period) when ai.appraisal_mode = 'Half-yearly' then concat('H',ai.appraisal_period) when ai.appraisal_mode = 'Yearly' then 'Yearly' end)  like '%".strtoupper($val)."%' AND ";
                else                                    
                    $searchQuery .= $key." like '%".$val."%' AND ";                
                
                $searchArray[$key] = $sval;
            }
            $searchQuery = rtrim($searchQuery," AND");					
        }
			
		$objName = 'appraisalinit';
		
		$tableFields = array('action'=>'Action','unitname' => 'Business Unit','deptname' => 'Department','fin_year' => 'Financial Year',
                    'appraisal_mode'=>'Appraisal Mode','app_period' => 'Period','status' => 'Appraisal Status','appraisal_process_status' => 'Process Status');
		
		$tablecontent = $this->getAppraisalInitData($sort, $by, $pageNo, $perPage,$searchQuery);  
		
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
			'actionparam'=>'yes',
			'dashboardcall'=>$dashboardcall,
                        'search_filters' => array(
                            'status' =>array(
                                'type'=>'select',
                                'filter_data' => array(''=>'All','1' => 'Open','2' => 'Closed',3=>'Force Closed'),
                            ),
                            'appraisal_mode' => array(
                                'type' => 'select',
                                'filter_data' => array('' => 'All','Quarterly' => 'Quarterly','Half-yearly' => 'Half-yearly','Yearly' => 'Yearly',),
                            ),
                        ),
		);
		return $dataTmp;
	}
	
        /**
         * This function will returns appraisal data by its id.
         * @param integer $id = id of appraisal.
         * @return array Array of appraisal data.
         */
	public function getConfigData($id)
	{
            $select = $this->select()
                            ->setIntegrityCheck(false)
                            ->from(array('pi'=>'main_pa_initialization'),array('pi.*'))
                            ->where('pi.isactive = 1 AND pi.id='.$id.' ');
            return $this->fetchAll($select)->toArray();		
	}
	
	public function getAppImplementationData($businessUnitId,$departmentId)
	{
		 $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('pim'=>'main_pa_implementation'),array('pim.*'))
					    ->where('pim.isactive = 1 AND pim.businessunit_id = '.$businessUnitId.' AND pim.department_id = '.$departmentId);
                 
		return $this->fetchAll($select)->toArray();		
	}
	/**
	 * This function is used to check any appraisal data is exists to a particular business unit/department.
	 * @param integer $businessUnitId         = id of businessunit
	 * @param integer $departmentId           = id of department
	 * @param integer $performance_app_flag   = performance applicability flag
	 * @param string $manager_flag            = calling flag(always null except from myteamappraisal call)
	 * @return array Array of appraisal data.
	 */
	public function checkAppraisalExists($businessUnitId,$departmentId='',$performance_app_flag='',$manager_flag = '')
	{            
		$dept_str = "";$manager_str = "";
		if($departmentId != '')
		{
			$dept_str = " AND (pi.department_id = '$departmentId' or pi.department_id is null) ";
		}
		if($manager_flag != '')
		{
			$manager_str = " and pi.initialize_status = 1 and pi.status = 1 ";
		}
		$select = $this->select()
				->setIntegrityCheck(false)
				->from(array('pi'=>'main_pa_initialization'),array('pi.*'))
				->where('pi.isactive = 1 AND pi.status in (0,1) AND pi.businessunit_id = '.$businessUnitId.$dept_str.$manager_str);    
		return $this->fetchAll($select)->toArray();
	}
	
	public function SaveorUpdateAppraisalInitData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_pa_initialization');
			return $id;
		}
		
	}

	/**
	 * 
	 * Update Appraisal setttings
	 * @param int $settingflag - Flag to update appraisal settings
	 * @param int $appraisalid - ID of appraisal (table: main_pa_initialization)
	 */
	public function updategroupsettings($settingflag,$appraisalid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		// Update Appraisal process status
		$db->query("update main_pa_initialization  set group_settings = $settingflag, initialize_status = NULL where id = $appraisalid and isactive =1");
		if($settingflag == 0)
		{
			$db->query("update main_pa_questions_privileges_temp  set group_id = NULL,hr_qs = NULL,hr_group_qs_privileges = NULL  where pa_initialization_id = ".$appraisalid." and isactive =1 and module_flag=1 ");
			$db->query("update main_pa_groups  set isactive=0 where pa_initialization_id = ".$appraisalid." ");
		}	
	}
	
public function getEmployeeList($data,$employeeIds='',$flag)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$where = 'e.isactive=1';
		if(!empty($data))
		{
			if($data['businessunit_id'] !='' && $data['businessunit_id'] !='NULL')
			{
				$where.= ' AND e.businessunit_id = '.$data['businessunit_id'].'';
			}
			if($data['department_id'] !='' && $data['department_id'] !='NULL')
			{
				$where.= ' AND e.department_id = '.$data['department_id'].'';
			}	
			if($data['eligibility'] !='' && $data['eligibility'] !='NULL')
			{
				$where.= ' AND e.emp_status_id IN('.$data['eligibility'].')';
			}
		}
		if($employeeIds !='')
		{
			if($flag == 1)
				$where.=' AND e.user_id NOT IN('.$employeeIds.')';
			else if($flag == 2)
				$where.=' AND e.user_id IN('.$employeeIds.')';	
		}
		
		 $query = "select e.user_id,e.userfullname,e.businessunit_name,e.department_name,e.profileimg,g.id as groupid,r.rolename from main_employees_summary e
				   inner join main_roles r on r.id=e.emprole and r.isactive=1
				   inner join main_groups g on r.group_id = g.id and g.id IN(".MANAGER_GROUP.",".HR_GROUP.",".EMPLOYEE_GROUP.",".SYSTEMADMIN_GROUP.") 
				   where $where ";
            $result = $db->query($query);
            $options = $result->fetchAll();
            
            return $options;
	}
	
	
	public function getActiveAppraisals()
	{            
            
            $select = $this->select()
                            ->setIntegrityCheck(false)
                            ->from(array('pi'=>'main_pa_initialization'),array('pi.*'))
                            ->where('pi.isactive = 1 AND pi.status in (0,1) AND pi.initialize_status = 1');
            return $this->fetchAll($select)->toArray();
	}
	
	public function getAppraisalForMgrEmp($enablestep)
	{            
            
            $select = $this->select()
                            ->setIntegrityCheck(false)
                            ->from(array('pi'=>'main_pa_initialization'),array('pi.*'))
                            ->where('pi.isactive = 1 AND pi.status in (0,1) AND pi.initialize_status = 1 AND enable_step ="'.$enablestep.'"');
            return $this->fetchAll($select)->toArray();
	}
	
	//function to check whether appraisal exist or not
	public function isAppraisalExist($businessunit_id,$department_id,$from_year,$to_year,$condition)
	{
		$where = '';
		if(is_numeric($department_id) && $department_id > 0)
		{
			$where = " AND department_id=$department_id ";
		}
		$sql_str = "SELECT count(id) as initialization_count FROM main_pa_initialization WHERE businessunit_id=$businessunit_id $where AND (from_year=$from_year $condition to_year=$to_year) AND status!=3";
		$db = Zend_Db_Table::getDefaultAdapter();
		$res = $db->fetchRow($sql_str);
		return $res['initialization_count'];
	}
	//function to get employee's line managers
	public function getEmployeeLineManagers($employee_id,$initialization_id)
	{
		$sql_str = "select 
		coalesce(q.line_manager_1,'NA') as line_manager_1,coalesce(s1.userfullname,'NA') as line_manager_1_name,coalesce(s1.profileimg,'') as line_manager_1_profile_img,coalesce(s1.employeeId,'NA') as line_manager_1_employee_id,coalesce(s1.jobtitle_name,'NA') as line_manager_1_jobtitle_name,
		coalesce(q.line_manager_2,'NA') as line_manager_2,coalesce(s2.userfullname,'NA') as line_manager_2_name,coalesce(s2.profileimg,'') as line_manager_2_profile_img,coalesce(s2.employeeId,'NA') as line_manager_2_employee_id,coalesce(s2.jobtitle_name,'NA') as line_manager_2_jobtitle_name,
		coalesce(q.line_manager_3,'NA') as line_manager_3,coalesce(s3.userfullname,'NA') as line_manager_3_name,coalesce(s3.profileimg,'') as line_manager_3_profile_img,coalesce(s3.employeeId,'NA') as line_manager_3_employee_id,coalesce(s3.jobtitle_name,'NA') as line_manager_3_jobtitle_name,
		coalesce(q.line_manager_4,'NA') as line_manager_4,coalesce(s4.userfullname,'NA') as line_manager_4_name,coalesce(s4.profileimg,'') as line_manager_4_profile_img,coalesce(s4.employeeId,'NA') as line_manager_4_employee_id,coalesce(s4.jobtitle_name,'NA') as line_manager_4_jobtitle_name,
		coalesce(q.line_manager_5,'NA') as line_manager_5,coalesce(s5.userfullname,'NA') as line_manager_5_name, coalesce(s5.profileimg,'') as line_manager_5_profile_img,coalesce(s5.employeeId,'NA') as line_manager_5_employee_id,coalesce(s5.jobtitle_name,'NA') as line_manager_5_jobtitle_name 
		from main_pa_questions_privileges q 
		left join main_employees_summary s1 on q.line_manager_1=s1.user_id 
		left join main_employees_summary s2 on q.line_manager_2=s2.user_id 
		left join main_employees_summary s3 on q.line_manager_3=s3.user_id 
		left join main_employees_summary s4 on q.line_manager_4=s4.user_id 
		left join main_employees_summary s5 on q.line_manager_5=s5.user_id 
		where q.employee_id=$employee_id  and q.pa_initialization_id=$initialization_id";
		$db = Zend_Db_Table::getDefaultAdapter();
		$res = $db->fetchRow($sql_str);
		return $res;		
	}
	//function to get active records count from questions_previleges_temp table
	public function getActiveRecordsCountTemp($init_id)
	{
		$sql_str = "SELECT count(id) as record_count FROM main_pa_questions_privileges_temp WHERE pa_initialization_id=$init_id AND isactive=1";
		$db = Zend_Db_Table::getDefaultAdapter();
		$res = $db->fetchRow($sql_str);
		return isset($res['record_count'])?$res['record_count']:0;
	}
	//function to get active records count from questions_previleges table
	public function getActiveRecordsCount($init_id)
	{
		$sql_str = "SELECT count(id) as record_count FROM main_pa_questions_privileges WHERE pa_initialization_id=$init_id AND isactive=1";
		$db = Zend_Db_Table::getDefaultAdapter();
		$res = $db->fetchRow($sql_str);
		return isset($res['record_count'])?$res['record_count']:0;
	}
	//function to get the initialization data for organization hierarchy
    public function getdisplayacontentacc_rep($init_id,$manager_id)
    {
        $data = array();
        if($init_id !== '' && $manager_id !== '')
        {
            $init_data = $this->getAppDataById($init_id);
        
            if($init_data['initialize_status'] == 1)
                $table_name = "main_pa_questions_privileges";
            else 
                $table_name = "main_pa_questions_privileges_temp";
            
            $db = Zend_Db_Table::getDefaultAdapter();
			$query = "select q.pa_initialization_id,e.user_id,case when e.user_id = q.line_manager_2 then 'line_2' 
                      when e.user_id = q.line_manager_3 then 'line_3' when e.user_id = q.line_manager_4 then 'line_4' 
                      when e.user_id = q.line_manager_5 then 'line_5' else 'employee' end emp_type,e.userfullname,
                      e.profileimg,e.employeeId,e.jobtitle_name from ".$table_name." q
					  inner join main_employees_summary e on q.employee_id=e.user_id 
                      where q.isactive = 1 and q.pa_initialization_id = '".$init_id."' and "
                    . "e.user_id in ( q.employee_id,line_manager_2,line_manager_3,line_manager_4,line_manager_5 ) "
                    . "and q.line_manager_1 = '".$manager_id."' group by e.user_id";
            $data = $db->query($query)->fetchAll();
        }
        return $data;
    }	
	/*public function check_for_appraisal($businessunit_id,$department_id,$from_year,$to_year)
	{
		$where = '';
		if(is_numeric($dept_id) && $dept_id > 0)
		{
			$where = " and department_id=$department_id ";
		}
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select ifnull(max(ifnull(appraisal_period,0)),0)+1 app_period 
                  from main_pa_initialization where isactive = 1 and businessunit_id = '".$businessunit_id."' $where
                  and from_year = '".$from_year."' and to_year = '".$to_year."' and status=2 ";
        $result = $db->query($query)->fetch();
        return $result['app_period'];
	}*/
	public function getAppraisalPeriodOnBuDept($businessunit_id,$department_id,$from_year,$to_year,$condition='AND',$dept_flag=0)
	{
		$where = '';
		if(is_numeric($department_id) && $department_id > 0)
		{
			$where = (($dept_flag == 1)?" OR ":" AND ")." department_id=$department_id ";
		}
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select max(id) as id,businessunit_id,department_id,appraisal_mode,max(appraisal_period) as appraisal_period,performance_app_flag,appraisal_ratings      
                  from main_pa_initialization 
				  where isactive = 1 and (businessunit_id = '".$businessunit_id."' $where)
                  and (from_year = '".$from_year."' $condition to_year = '".$to_year."') and status=2 
				  group by businessunit_id,department_id";
        $result = $db->query($query)->fetchAll();
		if(!empty($result))
		{
			return $result;
		}
		else
		{
			return array();
		}
	}
	public function getexistingperformanceappflag($businessunit_id,$from_year,$to_year)
	{
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select performance_app_flag 
                  from main_pa_initialization 
				  where isactive = 1 and businessunit_id = '$businessunit_id' and (from_year = '$from_year' and to_year = '$to_year')";
        $result = $db->query($query)->fetch();	
		if(!empty($result))
		{
			return $result['performance_app_flag'];
		}
		else
		{
			return NULL;
		}
	}
	
}