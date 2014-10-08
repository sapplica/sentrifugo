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

class Services_Model_Employee extends Zend_Db_Table_Abstract
{
    /**
     * This function will return all active interviews.
     * @param integer $page_no   = page number.
     * @param integer $per_page  = no.of record per page.
     * @param integer $user_id   = id of logged user.
     * @param integer $group_id  = id of group.
     * @return array Array of interview details.
     */
    public function getiroundslist($page_no,$per_page,$user_id,$group_id)
    {
        $interviewer_str = "";
        if($group_id == MANAGER_GROUP || $group_id == EMPLOYEE_GROUP || $group_id == SYSTEMADMIN_GROUP)
        {
           $interviewer_str = " and c.interviewer_id = ".$user_id." ";
        }
        
        $db = Zend_Db_Table::getDefaultAdapter();
        $query_cnt = "SELECT count(*) cnt
                        FROM `main_interviewrounds_summary` AS `c` 
                        INNER JOIN `main_requisition_summary` AS `r` ON r.req_id = c.requisition_id 
                           and r.isactive = 1 and r.req_status not in ('On hold') 
                        WHERE c.isactive = 1 and c.interview_status not in ('Completed','Requisition Closed/Completed') ".$interviewer_str." 
                        group by c.interview_id ORDER BY `c`.`created_date`";
        $result_cnt = $db->query($query_cnt);
        $row_cnt = $result_cnt->rowCount();
        $total_cnt = $row_cnt;

        $offset = ($per_page*$page_no) - $per_page;
        $limit_str = " limit ".$per_page." offset ".$offset;
        $page_cnt = ceil($total_cnt/$per_page);
        
        $query = "SELECT c.interview_id,c.candidate_name,c.candidate_id,c.interview_status, `r`.`requisition_code`,  
                  `c`.`candidate_status`, `r`.`jobtitle_name` 
                  FROM `main_interviewrounds_summary` AS `c` 
                  INNER JOIN `main_requisition_summary` AS `r` ON r.req_id = c.requisition_id 
                     and r.isactive = 1 and r.req_status not in ('On hold') 
                  WHERE c.isactive = 1 and c.interview_status not in ('Completed','Requisition Closed/Completed') ".$interviewer_str." 
                  group by c.interview_id ORDER BY `c`.`created_date` ".$limit_str;
        
        $result = $db->query($query);
        $data = $result->fetchAll();
        
        return array('rows' => $data,'page_cnt' => $page_cnt); 
    }
    /**
     * This function is used to get employee details by supplying user id.
     * @param Integer $user_id = user id of employee.
     * @return Array Array of employee details.
     */
    public function getempdetails($user_id)
    {
        $data = array();
        if($user_id != '')
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select employeeId,userfullname,emprole_name,emailaddress,reporting_manager_name,jobtitle_name,
                      position_name,date_of_joining,profileimg
                      from main_employees_summary where user_id = ".$user_id;
            $result = $db->query($query);
            $row = $result->fetch();
            
            $query_dob = "select dob,pancard_number from main_emppersonaldetails where user_id = ".$user_id;
            $result_dob = $db->query($query_dob);
            $data_dob = $result_dob->fetch();
            if(count($row) >0 && !empty($row))
            {
                $data = $row;
                if(count($data_dob) > 0 && !empty($data_dob))
                {
                    $data = $row + $data_dob;
                }
            }
        }
        return $data;
        
    }
    /**
     * This function will return all active employees
     * @param integer $page_no   = page number.
     * @param integer $per_page  = no.of record per page.
     * @return array Array of employees and their details
     */
    public function getemplist($page_no,$per_page)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query_cnt = "select count(*) cnt from main_employees_summary where isactive != 5";
        $result_cnt = $db->query($query_cnt);
        $row_cnt = $result_cnt->fetch();
        $total_cnt = $row_cnt['cnt'];

        $offset = ($per_page*$page_no) - $per_page;
        $limit_str = " limit ".$per_page." offset ".$offset;
        $page_cnt = ceil($total_cnt/$per_page);
        
        $query = "select employeeId,userfullname,emprole_name,emailaddress,reporting_manager_name,jobtitle_name,
                  position_name,date_of_joining,profileimg,concat(office_number,' (ext ',ifnull(extension_number,'--'),')') phone_number
                  from main_employees_summary where isactive != 5 order by userfullname".$limit_str;
        $result = $db->query($query);
        $data = $result->fetchAll();
        
        return array('rows' => $data,'page_cnt' => $page_cnt);            
    }
	
	public function getmyemplist($page_no,$per_page,$userid)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query_cnt = "select count(*) cnt from main_employees_summary where (isactive != 5 AND user_id != ".$userid." AND reporting_manager = ".$userid." )";
        $result_cnt = $db->query($query_cnt);
        $row_cnt = $result_cnt->fetch();
        $total_cnt = $row_cnt['cnt'];

        $offset = ($per_page*$page_no) - $per_page;
        $limit_str = " limit ".$per_page." offset ".$offset;
        $page_cnt = ceil($total_cnt/$per_page);
        
        $query = "select employeeId,userfullname,emprole_name,emailaddress,reporting_manager_name,jobtitle_name,
                  position_name,date_of_joining,profileimg,concat(office_number,' (ext ',extension_number,')') phone_number
                  from main_employees_summary where (isactive != 5 AND user_id != ".$userid." AND reporting_manager = ".$userid." )  order by userfullname".$limit_str;
        $result = $db->query($query);
        $data = $result->fetchAll();
        
        return array('rows' => $data,'page_cnt' => $page_cnt);            
    }
}