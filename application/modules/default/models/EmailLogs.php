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

class Default_Model_EmailLogs extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_emaillogs';
    protected $_primary = 'id';
	
	
    public function SaveorUpdateEmailData($data, $where)
    {
        if($where != ''){
                    $this->update($data, $where);
                    return 'update';
            } else {
                    $this->insert($data);
                    $id=$this->getAdapter()->lastInsertId('main_emaillogs');
                    return $id;
            }


    }
    
    public function getNotSentMails()
    {		
        $email_data = $this->fetchAll("date(createddate) between  date(date_sub(now(),INTERVAL 1 MONTH)) and date(now()) and is_sent = 0")->toArray();
        return $email_data;        
    }
    
    public function getEmpDocExpiryData($calc_date)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select id,document_name from main_identitydocuments where isactive = 1 and expiry = 1";
        $result = $db->query($query);
        $doc_arr = array();
        while($data = $result->fetch())
        {        
            $doc_arr[$data['id']] = $data['document_name'];
        }
        
        $final_arr = array();
        if(count($doc_arr) > 0)
        {
            $query = "select p.identity_documents,e.userfullname,e.emailaddress from main_emppersonaldetails p,
                      main_employees_summary e where e.isactive = 1 and p.isactive = 1  and p.user_id = e.user_id 
                      and p.identity_documents is not null";
            $result = $db->query($query);
            $i = 0;
            while($data = $result->fetch())
            {
                
                $att_arr = get_object_vars(json_decode($data['identity_documents']));
                $fin_att_arr = array_intersect_key($att_arr, $doc_arr);
                foreach($fin_att_arr as $key => $value)
                {
                    $imp_val = explode(":",$value);                    
                    if($imp_val[1] != '' && $imp_val[1] == $calc_date)
                    {                        
                        $final_arr[$i]['name'] = $data['userfullname'];
                        $final_arr[$i]['email'] = $data['emailaddress'];
                        $final_arr[$i]['docs'][] = $doc_arr[$key];
                    }
                }
                $i++;                
            }            
        }
        return $final_arr;        
    }
    public function getEmpExpiryData($calc_date)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select v.user_id,case when v.passport_expiry_date = '".$calc_date."' 
                  then 'passport' when v.visa_expiry_date = '".$calc_date."' then 'visa' when 
                  v.ininetyfour_expiry_date = '".$calc_date."' then 'i94'  end etype 
                  from main_empvisadetails v  
                  where  '".$calc_date."' in (v.passport_expiry_date,v.ininetyfour_expiry_date,
                  v.visa_expiry_date) and v.isactive = 1";
        
        $result = $db->query($query);
        $visa_arr = array();
        $emp_arr = array();
        $final_arr = array();
        while($data = $result->fetch())
        {
            $visa_arr[$data['user_id']]['etype'] = $data['etype'];
            $emp_arr[] = $data['user_id'];
        }
        $emp_str = implode(',', $emp_arr);
        
        $emp_query = "select id,userfullname,emailaddress 
                      from main_users 
                      where isactive = 1 and id in (".$emp_str.") and id > 1";
        $emp_result = $db->query($emp_query);
        while($data = $emp_result->fetch())
        {
            $final_arr[$data['id']]['etype'] = $visa_arr[$data['id']]['etype'];
            $final_arr[$data['id']]['userfullname'] = $data['userfullname'];
            $final_arr[$data['id']]['emailaddress'] = $data['emailaddress'];
        }
        return $final_arr;
    }
    public function getLeaveApproveData($from_date,$to_date)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select l.rep_mang_id,l.user_id 
                  from main_leaverequest l                   
                  where l.leavestatus = 'Pending for approval' 
                  and l.createddate between '".$from_date."' and '".$to_date."' and l.isactive = 1 
                  group by l.user_id  
                  order by l.rep_mang_id asc";
        
        $result = $db->query($query);
        $final_arr = array();
        $leave_arr = array();
        $emp_arr = array();
        while($data = $result->fetch())
        {
            $leave_arr[] = array('user_id' => $data['user_id'],'rep_mang_id' => $data['rep_mang_id']);
            $emp_arr[] = $data['user_id'];
            $emp_arr[] = $data['rep_mang_id'];
        }
        
        $emp_str = implode(',', $emp_arr);
        
        $emp_query = "select id,userfullname,emailaddress, employeeId 
                      from main_users 
                      where isactive = 1 and id in (".$emp_str.") and id > 1";
        $emp_result = $db->query($emp_query);
        $emp_arr = array();
        while($data = $emp_result->fetch())
        {
            $emp_arr[$data['id']]['userfullname'] = $data['userfullname'];
            $emp_arr[$data['id']]['emailaddress'] = $data['emailaddress'];
            $emp_arr[$data['id']]['employeeId'] = $data['employeeId'];
        }
        
        foreach($leave_arr as $data)
        {
        	$final_arr[$data['rep_mang_id']]['mng_name'] = $emp_arr[$data['rep_mang_id']]['userfullname'];
            $final_arr[$data['rep_mang_id']]['mng_email'] = $emp_arr[$data['rep_mang_id']]['emailaddress'];            
            $final_arr[$data['rep_mang_id']]['team'][$emp_arr[$data['user_id']]['employeeId']] = $emp_arr[$data['user_id']]['userfullname'];
        }        
        
        return $final_arr;
    }
    
    public function getInactiveusersData($calc_date)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select ul.userid,ul.employeeId
                  from main_userloginlog ul                   
                  where date(ul.logindatetime) = '".$calc_date."' and ul.userid != 1 group by ul.userid";
        $result = $db->query($query);
        $final_arr = array();
        $emp_arr = array();
        
        while($data = $result->fetch())
        {                        
            $emp_arr[] = $data['userid'];
        }
        
        $emp_str = implode(',', $emp_arr);
        
        $emp_query = "select id,userfullname,emailaddress,employeeId 
                      from main_users 
                      where isactive = 1 and id in (".$emp_str.") and id > 1";
        $emp_result = $db->query($emp_query);
        $emp_arr = array();
        while($data = $emp_result->fetch())
        {
            $final_arr[$data['id']]['employeeId'] = $data['employeeId'];
            $final_arr[$data['id']]['emailaddress'] = $data['emailaddress'];
            $final_arr[$data['id']]['userfullname'] = $data['userfullname'];
        }
        return $final_arr;
    }
    public function getRequisitionData($calc_date)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select r.requisition_code,r.businessunit_id,j.jobtitlename from 
                  main_requisition r 
                  left join main_jobtitles j on j.id = r.jobtitle 
                  where r.onboard_date = '".$calc_date."'  and r.isactive  = 1 order by r.businessunit_id";
        $result = $db->query($query);
        $final_arr = array();
        
        while($data = $result->fetch())
        {            
            $final_arr[$data['businessunit_id']]['req'][] = $data['requisition_code']." - ".$data['jobtitlename'];
        }
        return $final_arr;
    }
    
    
}
?>