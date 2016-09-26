<?php

/* ********************************************************************************* 
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
 * ****************************************************************************** */

/**
 * Model of manager initiallisation.
 *
 * @author ramakrishna
 */
class Default_Model_Appraisalmanager extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_pa_questions_privileges';
    protected $_primary = 'id';
    
    /**
     * This method is used to delete group created by manager.
     * @param integer $appraisal_id   =  id of appraisal
     * @param integer $manager_id     =  id of manager
     * @param integer $group_id       = id of group
     */
    public function deletemanagergroup($appraisal_id,$manager_id,$group_id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query1 = "update main_pa_groups set isactive = 0 where id = '".$group_id."'";
        $query2 = "update main_pa_questions_privileges set manager_group_id = null,manager_qs = null,"
                . "manager_qs_privileges = null where pa_initialization_id ='".$appraisal_id."' "
                . "and line_manager_1 = '".$manager_id."' and manager_group_id = '".$group_id."'";
        $db->query($query1);
        $db->query($query2);
    }
    /**
     * This method is used to get all groups created by manager.
     * @param integer $appraisal_id = id of appraisal
     * @param integer $manager_id   = id of manager
     * @return array Array of groups with name and no.of employees,no.of questions.
     */
    public function getManagergroups($appraisal_id,$manager_id)
    {
        $result = array();
        if($appraisal_id != '')
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select q.manager_group_id,count(q.employee_id) as empcount,group_concat(q.employee_id) as empids, "
                    . "q.manager_qs,g.group_name,CHAR_LENGTH(q.manager_qs) - CHAR_LENGTH(REPLACE(q.manager_qs, ',', '')) + 1 "
                    . "as qscount from main_pa_questions_privileges q inner join main_pa_groups g on q.manager_group_id=g.id "
                    . "and g.isactive=1 where q.isactive=1 and q.pa_initialization_id = '".$appraisal_id."' and "
                    . "q.module_flag=1 and q.line_manager_1 = '".$manager_id."' group by q.manager_group_id;";
            $result = $db->query($query)->fetchAll();
        }
        return $result;
    }
    /**
     * This method is to get employees under manager to create new group.
     * @param integer $appraisal_id = id of appraisal
     * @param integer $manager_id   = id of manager
     * @param integer $group_id     = id of group
     * @return array Array of employees and their details.
     */
    public function  getmanager_emp($appraisal_id,$manager_id,$group_id = '')
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $group_str = " is null ";
        if($group_id != '')
            $group_str = " = '".$group_id."' ";
        
        $query = "select pa.manager_qs,pa.manager_qs_privileges,es.user_id,es.employeeId,es.profileimg,es.jobtitle_name,es.userfullname "
                . "from main_pa_questions_privileges pa inner join main_employees_summary es on es.user_id = pa.employee_id "
                . "and es.isactive = 1 where pa.isactive = 1 and pa.pa_initialization_id = '".$appraisal_id."' "
                . "and pa.line_manager_1 = '".$manager_id."' and pa.manager_group_id ".$group_str." order by es.userfullname";
        $result = $db->query($query)->fetchAll();
        return $result;
    }

    /**
     * This action is used to build grid view in index action.It will take maximum parameters to support dashboard call
     * also.
     * @param string $sort              = type of sort like asc,desc
     * @param string $by                = column name to be sort
     * @param integer $perPage          = records per page
     * @param integer $pageNo           = page number 
     * @param json $searchData          = search data in json format
     * @param string $call              = type of call like normal,ajax
     * @param string $dashboardcall     = whether the call is from dashboard or not
     * @param mixed $a                  = extra parameter
     * @param mixed $b                  = extra parameter
     * @param mixed $c                  = extra parameter
     * @param mixed $d                  = extra parameter
     * @return array Array of details.
     */
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
                if($key == 'fin_year')
                    $searchQuery .= " concat(from_year,'-',to_year) like '%".$val."%' AND ";
                else if($key == 'app_period')
                    $searchQuery .= " lower(case when ai.appraisal_mode = 'Quarterly' then concat('Q',ai.appraisal_period) when ai.appraisal_mode = 'Half-yearly' then concat('H',ai.appraisal_period) when ai.appraisal_mode = 'Yearly' then 'Yearly' end)  like '%".strtoupper($val)."%' AND ";
                else                                    
                    $searchQuery .= $key." like '%".$val."%' AND ";                
                
                $searchArray[$key] = $val;
            }
            $searchQuery = rtrim($searchQuery," AND");					
        }
			
        $objName = 'appraisalmanager';

        $tableFields = array('action'=>'Action','unitname' => 'Business Unit','deptname' => 'Department','fin_year' => 'Financial Year',
                    'appraisal_mode'=>'Appraisal Mode','app_period' => 'Period','status' => 'Appraisal Status','appraisal_process_status' => 'Process Status');

        $tablecontent = $this->getAppraisalManagerData($sort, $by, $pageNo, $perPage,$searchQuery);     

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
                //'encrypt_status' => 'yes',
                'dashboardcall'=>$dashboardcall,
                'search_filters' => array(
                    'status' =>array(
                        'type'=>'select',
                        'filter_data' => array(''=>'All','1' => 'Open','2' => 'Closed','3' => 'Force Closed'),
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
     * This method will support getgrid method to get details.
     * @param string $sort         = type of sort like asc,desc
     * @param string $by           = column name to be sort
     * @param type $pageNo         = page number 
     * @param type $perPage        = records per page
     * @param string $searchQuery  = query generated by using search values.
     * @return Resultset Resultset of appraisal manager data.
     */
    public function getAppraisalManagerData($sort, $by, $pageNo, $perPage,$searchQuery)
    {
        $appraisalinit_model = new Default_Model_Appraisalinit();
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
        if($loginuserRole != SUPERADMINROLE && $loginuserGroup != MANAGEMENT_GROUP)
        {
            $init_data = $appraisalinit_model->checkAppraisalExists($businessunit_id, $department_id);
            $where .= " and ai.businessunit_id = '".$businessunit_id."'  ";
			$flag = isset($init_data[0]['performance_app_flag'])?$init_data[0]['performance_app_flag']:'';
            if($flag == 0)
			{
				$where .= " and ai.department_id = '".$department_id."' ";
			}
        }
        if($searchQuery)
		{
            $where .= " AND ".$searchQuery;
		}
        $db = Zend_Db_Table::getDefaultAdapter();		
		
       $appInitData = $this->select()
					->setIntegrityCheck(false)	
					->from(array('ai'=>'main_pa_initialization'),array('ai.id','ai.enable_step','org_status' =>'ai.status','managerid_status'=>'find_in_set('.$loginUserId.',manager_ids)',
						new Zend_Db_Expr("concat(from_year,'-',to_year) as fin_year"),'ai.appraisal_mode',
						new Zend_Db_Expr("CASE WHEN ai.status=1 THEN 'Open' WHEN ai.status=2 THEN 'Closed' ELSE 'Force Closed' END as status"),
						new Zend_Db_Expr("case when initialize_status = 1 then case when ai.enable_step = 1 then 'Enabled to Managers' when ai.enable_step = 2 then 'Enabled to Employees' end when initialize_status = 2 then 'Initialize later' when initialize_status is null then 'In progress' end as appraisal_process_status"),
						new Zend_Db_Expr("case when ai.appraisal_mode = 'Quarterly' then concat('Q',ai.appraisal_period) when ai.appraisal_mode = 'Half-yearly' then concat('H',ai.appraisal_period) when ai.appraisal_mode = 'Yearly' then 'Yearly' end as app_period"),
						))
					->joinInner(array('p' => 'main_pa_questions_privileges'),"p.isactive = 1 and p.pa_initialization_id = ai.id and p.line_manager_1 = ".$loginUserId,array())
					->joinLeft(array('b' => 'main_businessunits'),"b.id = ai.businessunit_id and b.isactive=1",array('unitname'))
					->joinLeft(array('d' => 'main_departments'),"d.id = ai.department_id and d.isactive=1",array('deptname'))                            
					->where($where)
					->group('ai.id')
					->order("$by $sort") 
					->limitPage($pageNo, $perPage);
        return $appInitData;       		
    }     
    
    /**
     * This function is used in my team appraisal to display employees for viewing/adding line managers comments.
     * @param integer $searchval = string search
     * @param integer $manager_id   = id of manager.
     * @return array Array of employees.
     */
    public function getEmpdata_managerapp($manager_id,$searchval='')
    {
        $final_arr = array();
        if($manager_id != '')
        {
			$status_arr = array(0 => 'Pending employee ratings',1 => 'Pending L1 ratings',2 => 'Pending L2 ratings',
				3 => 'Pending L3 ratings',4 => 'Pending L4 ratings',5 => 'Pending L5 ratings',6 => 'Completed');
			$db = Zend_Db_Table::getDefaultAdapter();
			$query = "select er.consolidated_rating,pi.pa_configured_id,pi.id init_id,pi.appraisal_ratings,es.userfullname,es.employeeId,es.jobtitle_name,er.appraisal_status,er.line_rating_1,
                      er.line_rating_2,er.line_rating_3,er.line_rating_4,er.line_rating_5,es.profileimg
                      ,qp.line_manager_1,qp.line_manager_2,qp.line_manager_3,qp.line_manager_4,qp.line_manager_5,
                      es.user_id ,er.line_comment_1,
                      er.line_comment_2,er.line_comment_3,er.line_comment_4,er.line_comment_5,es.businessunit_name,es.department_name,pi.appraisal_mode,pi.appraisal_period,pi.from_year,pi.to_year 
                      from main_pa_questions_privileges qp 
					  inner join main_pa_employee_ratings er on er.pa_initialization_id = qp.pa_initialization_id and qp.employee_id = er.employee_id and qp.isactive = 1 
					  inner join main_employees_summary es on es.user_id = qp.employee_id and es.isactive = 1 
					  inner join main_pa_initialization pi on pi.id = qp.pa_initialization_id and pi.status = 1 and pi.initialize_status = 1 and pi.enable_step =2 
					  where qp.isactive = 1 $searchval "
                    . "and ".$manager_id." in (qp.line_manager_1,qp.line_manager_2,qp.line_manager_3,qp.line_manager_4,"
                    . "qp.line_manager_5) order by er.modifieddate desc";
            $result_set = $db->query($query)->fetchAll();
                        
            foreach($result_set as $key => $emp)
            {
                $final_arr[$key] = $emp;
                $line_unique_arr = array_filter(array_unique(array(1 => $emp['line_manager_1'],2 => $emp['line_manager_2'],3 => $emp['line_manager_3'],4 => $emp['line_manager_4'],5 => $emp['line_manager_5'])),'strlen');
                $line_status = array_search($manager_id, $line_unique_arr);
                $editing_status = array_search($emp['appraisal_status'], $status_arr);
                $manager_editing_status = ($editing_status == $line_status)?"add_edit":"view";
                $final_arr[$key]['line_status'] = $line_status;
                $final_arr[$key]['total_lines'] = count($line_unique_arr);
                $final_arr[$key]['manager_editing_status'] = $manager_editing_status;
                
            }
            //echo "<pre>";print_r($final_arr);echo "</pre>";
        }
        return $final_arr;
    }
    /**
     * This function is used in my team history to display employees for viewing their appraisal comments/ratings.
     * @param integer $searchval = string search
     * @param integer $manager_id   = id of manager.
     * @return array Array of employees.
     */
    public function getEmpdata_managerapphistory($manager_id,$searchval='',$init_id)
    {
        $final_arr = array();
        if($manager_id != '')
        {
			$status_arr = array(0 => 'Pending employee ratings',1 => 'Pending L1 ratings',2 => 'Pending L2 ratings',
				3 => 'Pending L3 ratings',4 => 'Pending L4 ratings',5 => 'Pending L5 ratings',6 => 'Completed');
			$db = Zend_Db_Table::getDefaultAdapter();
			$query = "select er.consolidated_rating,pi.pa_configured_id,pi.id init_id,pi.appraisal_ratings,es.userfullname,es.employeeId,es.jobtitle_name,er.appraisal_status,er.line_rating_1,
                      er.line_rating_2,er.line_rating_3,er.line_rating_4,er.line_rating_5,es.profileimg
                      ,qp.line_manager_1,qp.line_manager_2,qp.line_manager_3,qp.line_manager_4,qp.line_manager_5,
                      es.user_id ,er.line_comment_1,
                      er.line_comment_2,er.line_comment_3,er.line_comment_4,er.line_comment_5,es.businessunit_name,es.department_name,pi.appraisal_mode,pi.appraisal_period,pi.from_year,pi.to_year 
                      from main_pa_questions_privileges qp 
					  inner join main_pa_employee_ratings er on er.pa_initialization_id = qp.pa_initialization_id and qp.employee_id = er.employee_id and qp.isactive = 1 
					  inner join main_employees_summary es on es.user_id = qp.employee_id and es.isactive = 1 
					  inner join main_pa_initialization pi on pi.id = qp.pa_initialization_id and pi.status = 2 
					  where qp.isactive = 1 and qp.pa_initialization_id=$init_id $searchval "
                    . "and ".$manager_id." in (qp.line_manager_1,qp.line_manager_2,qp.line_manager_3,qp.line_manager_4,"
                    . "qp.line_manager_5) order by er.modifieddate desc";
            $result_set = $db->query($query)->fetchAll();
                        
            foreach($result_set as $key => $emp)
            {
                $final_arr[$key] = $emp;
                $line_unique_arr = array_filter(array_unique(array(1 => $emp['line_manager_1'],2 => $emp['line_manager_2'],3 => $emp['line_manager_3'],4 => $emp['line_manager_4'],5 => $emp['line_manager_5'])),'strlen');
                $line_status = array_search($manager_id, $line_unique_arr);
                $editing_status = array_search($emp['appraisal_status'], $status_arr);
                $manager_editing_status = ($editing_status == $line_status)?"add_edit":"view";
                $final_arr[$key]['line_status'] = $line_status;
                $final_arr[$key]['total_lines'] = count($line_unique_arr);
                $final_arr[$key]['manager_editing_status'] = $manager_editing_status;
                
            }
            //echo "<pre>";print_r($final_arr);echo "</pre>";
        }
        return $final_arr;
    }
    
    public function getempcontent($appraisal_id,$manager_id,$user_id,$flag,$app_config_id)
    {
    	$auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
        }   
        $final_arr = array();
        $edit_flag = 'false';
        if($appraisal_id != '' && $manager_id != '' && $user_id != '' && $flag != '')
        {   

            $db = Zend_Db_Table::getDefaultAdapter();
            $response_str = "";
            //if($flag == 'add_edit')
              //  $response_str = " and er.manager_response is null ";
            
            $query = "select * from main_pa_employee_ratings er "
                    . "where er.pa_initialization_id = '".$appraisal_id."' and er.employee_id = '".$user_id."' "
                    . "and er.isactive = 1 ".$response_str;
            
            $result = $db->query($query)->fetch();
			$employee_response = (!empty($result['employee_response']))?json_decode($result['employee_response'], true):"";
            $manager_response = (!empty($result['manager_response']))?json_decode($result['manager_response'], true):"";
            if(!empty($result))
            {
            	if(isset($result['line_manager_1']))
            	{
            		if($result['line_manager_1'] == $loginUserId)
            		  $edit_flag = 'true';
            	}
            }
            $emp_ques = (!empty($employee_response))?array_keys($employee_response):"";
			$category_arr = array();
			if(!empty($emp_ques))
			{
					$ques_query = "select q.id question_id,q.description,c.category_name,q.question from main_pa_questions q 
							   inner join main_pa_category c on q.pa_category_id = c.id and c.isactive = 1 
							   where q.isactive = 1 and q.module_flag = 1 and q.id in (". implode(',', $emp_ques).")";
					$ques_result = $db->query($ques_query)->fetchAll();
				if(!empty($ques_result))
				{
					foreach($ques_result as $ques)
					{
						$category_arr[$ques['category_name']][] = array('question_id' => $ques['question_id'],'question' => $ques['question'],'description' => $ques['description']); 
					}
				}
            }
            $rating_query = "select id,rating_text,rating_value from main_pa_ratings "
                    . "where pa_initialization_id = '".$appraisal_id."' and isactive = 1";
            $rating_result = $db->query($rating_query)->fetchAll();
            $rating_arr = array();
            if(!empty($rating_result))
			{
				foreach($rating_result as $rating)
				{
					$rating_arr[$rating['id']]['rating_text'] = $rating['rating_text'];
					$rating_arr[$rating['id']]['rating_value'] = $rating['rating_value'];
				}
            }
            $final_arr['employee_response'] = $employee_response;
            $final_arr['category_arr'] = $category_arr;
            $final_arr['rating_arr'] = $rating_arr;
            
            $skill_query = "select id,skill_name from main_pa_skills where isactive = 1";
            $skill_result = $db->query($skill_query)->fetchAll();
            $skill_arr = array();
            
            foreach($skill_result as $skill)
            {
                $skill_arr[$skill['id']] = $skill['skill_name'];
            }
            
            $final_arr['skill_arr'] = $skill_arr;
            $final_arr['manager_response'] = $manager_response;
            $final_arr['ratings_data'] = $result;
            $final_arr['edit_flag'] = $edit_flag;
            //echo "<pre>";print_r($final_arr);echo "</pre>";
        }
        return $final_arr;
    }
    
	public function getManagerGroupCount($appraisalid,$managerid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$options = array();
		
		if($appraisalid !='')
		{
		 	$query = "select count(*) as empcount from main_pa_questions_privileges 
		 			  where pa_initialization_id = $appraisalid and line_manager_1=$managerid 
		 			  and module_flag=1 and  isactive=1 and manager_group_id IS NULL; ";
            $result = $db->query($query);
            $options = $result->fetchAll();
		}   
            
            return $options;
	}
	
	
/**
     * This function is used in my team appraisal to display employees for viewing/adding line managers comments with search string.
     * @param integer $appraisal_id = id of appraisal
     * @param integer $manager_id   = id of manager.
     * @return array Array of employees.
     */
public function getSearchEmpdata_managerapp($manager_id,$searchval)
    {
        $final_arr = array();
        /*if($searchstring!='')
        {
        	$searchval = ' and es.userfullname like "%'.$searchstring.'%" '; 
        }*/
        if($manager_id != '')
        {
            $status_arr = array(0 => 'Pending employee ratings',1 => 'Pending L1 ratings',2 => 'Pending L2 ratings',
                3 => 'Pending L3 ratings',4 => 'Pending L4 ratings',5 => 'Pending L5 ratings',6 => 'Completed');
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select er.consolidated_rating,pi.pa_configured_id,pi.id init_id,pi.appraisal_ratings,es.userfullname,es.employeeId,es.jobtitle_name,er.appraisal_status,er.line_rating_1,
                      er.line_rating_2,er.line_rating_3,er.line_rating_4,er.line_rating_5,es.profileimg
                      ,qp.line_manager_1,qp.line_manager_2,qp.line_manager_3,qp.line_manager_4,qp.line_manager_5,
                      es.user_id ,er.line_comment_1,
                      er.line_comment_2,er.line_comment_3,er.line_comment_4,er.line_comment_5  
                      from main_pa_questions_privileges qp inner join main_pa_employee_ratings er 
                      on er.pa_initialization_id = qp.pa_initialization_id and qp.employee_id = er.employee_id 
                      and qp.isactive = 1 inner join main_employees_summary es on es.user_id = qp.employee_id 
                      and es.isactive = 1 inner join main_pa_initialization pi on pi.id = qp.pa_initialization_id and
                      pi.status = 1 and pi.initialize_status = 1 and pi.enable_step =2 where qp.isactive = 1 $searchval "
                    . "and ".$manager_id." in (qp.line_manager_1,qp.line_manager_2,qp.line_manager_3,qp.line_manager_4,"
                    . "qp.line_manager_5) order by er.modifieddate desc";
                    
            $result_set = $db->query($query)->fetchAll();                        
            foreach($result_set as $key => $emp)
            {
                $final_arr[$key] = $emp;
                $line_unique_arr = array_unique(array(1 => $emp['line_manager_1'],2 => $emp['line_manager_2'],3 => $emp['line_manager_3'],4 => $emp['line_manager_4'],5 => $emp['line_manager_5']));
                $line_status = array_search($manager_id, $line_unique_arr);
                $editing_status = array_search($emp['appraisal_status'], $status_arr);
                $manager_editing_status = ($editing_status == $line_status)?"add_edit":"view";
                $final_arr[$key]['line_status'] = $line_status;
                $final_arr[$key]['total_lines'] = count($line_unique_arr);
                $final_arr[$key]['manager_editing_status'] = $manager_editing_status;
                
            }
            //echo "<pre>";print_r($final_arr);echo "</pre>";
        }
        return $final_arr;
    }
  
    
/**
     * This function is used in my team appraisal to display employees for viewing/adding line managers comments with Status.
     * @param integer $appraisal_id = id of appraisal
     * @param integer $manager_id   = id of manager.
     * @return array Array of employees.
     */
    public function getStatusEmpdata_managerapp($manager_id,$appraisalstatus)
    {
        $final_arr = array();
        $appwhere = '';
        /*if($searchstring!='')
        {
        	$searchval = ' and es.userfullname like "%'.$searchstring.'%" '; 
        }*/
        if($manager_id != '')
        {
        	if($appraisalstatus)
        	  $appwhere = ' and er.appraisal_status='.$appraisalstatus.' ';	
        	  
            $status_arr = array(0 => 'Pending employee ratings',1 => 'Pending L1 ratings',2 => 'Pending L2 ratings',
                3 => 'Pending L3 ratings',4 => 'Pending L4 ratings',5 => 'Pending L5 ratings',6 => 'Completed');
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select er.consolidated_rating,pi.pa_configured_id,pi.id init_id,pi.appraisal_ratings,es.userfullname,es.employeeId,es.jobtitle_name,er.appraisal_status,er.line_rating_1,
                      er.line_rating_2,er.line_rating_3,er.line_rating_4,er.line_rating_5,es.profileimg
                      ,qp.line_manager_1,qp.line_manager_2,qp.line_manager_3,qp.line_manager_4,qp.line_manager_5,
                      es.user_id ,er.line_comment_1,
                      er.line_comment_2,er.line_comment_3,er.line_comment_4,er.line_comment_5  
                      from main_pa_questions_privileges qp inner join main_pa_employee_ratings er 
                      on er.pa_initialization_id = qp.pa_initialization_id and qp.employee_id = er.employee_id 
                      and qp.isactive = 1 inner join main_employees_summary es on es.user_id = qp.employee_id 
                      and es.isactive = 1 inner join main_pa_initialization pi on pi.id = qp.pa_initialization_id and
                      pi.status = 1 and pi.initialize_status = 1 and pi.enable_step =2 where qp.isactive = 1 $appwhere "
                    . "and ".$manager_id." in (qp.line_manager_1,qp.line_manager_2,qp.line_manager_3,qp.line_manager_4,"
                    . "qp.line_manager_5) order by er.modifieddate desc";
                    
            $result_set = $db->query($query)->fetchAll();
            foreach($result_set as $key => $emp)
            {
                $final_arr[$key] = $emp;
                $line_unique_arr = array_unique(array(1 => $emp['line_manager_1'],2 => $emp['line_manager_2'],3 => $emp['line_manager_3'],4 => $emp['line_manager_4'],5 => $emp['line_manager_5']));
				$line_status = array_search($manager_id, $line_unique_arr);
                $editing_status = array_search($emp['appraisal_status'], $status_arr);
                $manager_editing_status = ($editing_status == $line_status)?"add_edit":"view";
                $final_arr[$key]['line_status'] = $line_status;
                $final_arr[$key]['total_lines'] = count($line_unique_arr);
                $final_arr[$key]['manager_editing_status'] = $manager_editing_status;
                
            }
           }
        return $final_arr;
    }
	//function to get the line1 managers count for an appraisal
	public function getLineManagers($initialization_id,$line_manager_id)
	{
		$query = "select count(line_manager_1) as manager_count from main_pa_questions_privileges  
		where pa_initialization_id=$initialization_id and line_manager_1=$line_manager_id and isactive=1 
		group by line_manager_1";
		$db = Zend_Db_Table::getDefaultAdapter();
		return $db->fetchRow($query);
	}
	public function getBunitDept($appraisal_id)
	{
		$db = Zend_Db_Table::getDefaultAdapter(); 
		$query = "select businessunit_id,if(department_id is null,'',department_id) department_id from main_pa_initialization where id=$appraisal_id and isactive=1; ";
		
		return $db->query($query)->fetch();
	}
	
	public function getUserDetailsByID($unit_id,$department_id)
    {
     	
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$str_dept = '';
    	if($department_id != '')
    	$str_dept = " department_id = $department_id and ";
    	 $qry = "select es.id,es.user_id,es.userfullname,es.emailaddress from main_employees_summary es where es.user_id in (
				select s.user_id from main_employees_summary s 
				inner join main_roles r on s.emprole = r.id where s.businessunit_id=$unit_id 
				and $str_dept ( r.group_id = ".HR_GROUP.") and r.isactive=1 ) and es.isactive =1 ;"; 
    	$result = $this->_db->query($qry)->fetchAll();
    	 return $result;
    }
	public function getLineMgr($init_id,$employee_id)
    {
     	
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$qry = "select line_manager_1 from main_pa_questions_privileges where pa_initialization_id = $init_id and employee_id = $employee_id and isactive=1; "; 
    	$result = $this->_db->query($qry)->fetch();
    	 return $result;
    }
	public function getUserDetailsByEmpID($employee_id)
    {
     	
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$qry = "select user_id,emailaddress,userfullname,employeeId from main_employees_summary where user_id in($employee_id) and isactive=1;  "; 
    	$result = $this->_db->query($qry)->fetchAll();
    	 return $result;
    }
    
	public function getNextLineMgr($init_id,$employee_id,$next)
    {
     	
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$qry = "select line_manager_$next from main_pa_questions_privileges where pa_initialization_id = $init_id and employee_id = $employee_id and isactive=1; "; 
    	$result = $this->_db->query($qry)->fetch();
    	 return $result;
    }
}//end of class