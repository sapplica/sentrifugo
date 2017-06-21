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

class Default_Model_Disciplinaryincident extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_disciplinary_incident';
    protected $_primary = 'id';
    
           
    public function getDisciplinaryIncidents($sort, $by, $pageNo, $perPage,$searchQuery,$flag)
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $loginUserGroup = $auth->getStorage()->read()->group_id;
            $loginUserRole = $auth->getStorage()->read()->emprole;
        }
        if($flag=='myincident') {
            $where = " i.employee_id = $loginUserId and i.isactive = 1";
            if (!defined('sentrifugo_gilbert')) {
                $where.= " and CURDATE()<=i.violation_expiry";
            }
        }elseif($flag=='teamincident'){
            $where = " i.employee_rep_mang_id = $loginUserId and i.isactive = 1";
        }elseif($flag=='managementincident'){
            $where = " i.incident_raised_by = $loginUserId and i.isactive = 1";
        }elseif($flag=='allincident' && ($loginUserGroup==MANAGEMENT_GROUP || $loginUserGroup==HR_GROUP || $loginUserRole==SUPERADMINROLE)){
            $where = "i.isactive = 1";
        }
        if($searchQuery) {
                   $where .= " AND ".$searchQuery;
                }   
              $myDisciplinaryData = $this->select()
                            ->setIntegrityCheck(false)  
                            ->from(array('i'=>'main_disciplinary_incident'),array('i.*','employee_appeal'=>'if(i.employee_appeal = 1,"Yes","No")'
                                        ,'cao_verdict'=>'if(i.cao_verdict = 1,"Guilty","Not Guilty")',new Zend_Db_Expr("if(CURDATE()<=i.violation_expiry,'notexpired','expired') as dateexpired")
                                        ,'date_of_occurrence'=>'DATE_FORMAT(date_of_occurrence,"'.DATEFORMAT_MYSQL.'")'))
                            ->joinLeft(array('mu'=>'main_users'), 'mu.id=i.employee_id',array('employeename'=>'mu.userfullname','mu.employeeId','employeeemail'=>'mu.emailaddress'))
                            ->joinLeft(array('bu'=>'main_businessunits'), 'bu.id=i.employee_bu_id',array('bu.unitname'))
                            ->joinLeft(array('d'=>'main_departments'), 'd.id=i.employee_dept_id',array('d.deptname'))
                            ->joinLeft(array('v'=>'main_disciplinary_violation_types'), 'v.id=i.violation_id',array('v.violationname'))
                            ->where($where)
                            ->order("$by $sort") 
                            ->limitPage($pageNo, $perPage);

                return $myDisciplinaryData;
                
    }
        
    public function getIncidentData($id) {
        $incidentData = $this->select()
                            ->setIntegrityCheck(false)  
                            ->from(array('i'=>'main_disciplinary_incident'),array('i.*','employee_appeal'=>'if(i.employee_appeal = 1,"Yes","No")'
                                        ,'cao_verdict'=>'if(i.cao_verdict = 1,"Guilty","Not Guilty")',new Zend_Db_Expr("if(CURDATE()<=i.violation_expiry,'notexpired','expired') as dateexpired")))
                            ->joinLeft(array('u'=>'main_users'), 'u.id=i.employee_rep_mang_id',array('reportingmanagername'=>'u.userfullname','reportingmanageremail'=>'u.emailaddress'))
                            ->joinLeft(array('mu'=>'main_users'), 'mu.id=i.employee_id',array('employeename'=>'mu.userfullname','mu.employeeId','employeeemail'=>'mu.emailaddress'))
                            ->joinLeft(array('mus'=>'main_users'), 'mus.id=i.incident_raised_by',array('incidentraisedby'=>'mus.userfullname','managementemail'=>'mus.emailaddress'))
                            ->joinLeft(array('bu'=>'main_businessunits'), 'bu.id=i.employee_bu_id',array('bu.unitname'))
                            ->joinLeft(array('d'=>'main_departments'), 'd.id=i.employee_dept_id',array('d.deptname'))
                            ->joinLeft(array('v'=>'main_disciplinary_violation_types'), 'v.id=i.violation_id',array('v.violationname'))
                            ->joinLeft(array('j'=>'main_jobtitles'), 'j.id=i.employee_job_title_id',array('j.jobtitlename'))
                            ->where('i.id='.$id.' and i.isactive=1 ');
        return $this->fetchAll($incidentData)->toArray();                            
    }
        
        public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$flag='',$b='',$c='',$d='')
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
                    $val = htmlspecialchars($val);
                    if($key == "employeename")
                        $searchQuery .= " mu.userfullname like '%".$val."%' AND ";
                    elseif($key == 'date_of_occurrence')
                        $searchQuery .= "  ".$key." like '%".  sapp_Global::change_date($val,'database')."%' AND ";
                    else                                                 
                        $searchQuery .= $key." like '%".$val."%' AND ";                

                    $searchArray[$key] = $sval;
                }
                $searchQuery = rtrim($searchQuery," AND");                  
            }
                if($flag=='myincident') {   
                    $objName = 'disciplinarymyincidents';
                    $tablecontent = $this->getDisciplinaryIncidents($sort, $by, $pageNo, $perPage,$searchQuery,$flag);  
                }elseif($flag=='teamincident'){
                    $objName = 'disciplinaryteamincidents';
                    $tablecontent = $this->getDisciplinaryIncidents($sort, $by, $pageNo, $perPage,$searchQuery,$flag);  
                }elseif($flag=='managementincident'){
                    $objName = 'disciplinaryincident';
                    $tablecontent = $this->getDisciplinaryIncidents($sort, $by, $pageNo, $perPage,$searchQuery,$flag);
                }elseif($flag=='allincident'){
                    $objName = 'disciplinaryallincidents';
                    $tablecontent = $this->getDisciplinaryIncidents($sort, $by, $pageNo, $perPage,$searchQuery,$flag);
                }
        if($flag=='myincident') {
            $tableFields = array('action'=>'Action','unitname' => 'Business Unit','deptname' => 'Department',
                                'violationname' => 'Violation Name','date_of_occurrence'=>'Date Of Occurence',
                                'employee_appeal'=>'Appealed','cao_verdict' => 'Verdict');
        }else{        
            $tableFields = array('action'=>'Action','employeename'=>'Employee Name','unitname' => 'Business Unit',
                            'deptname' => 'Department','violationname' => 'Violation Name','date_of_occurrence'=>'Date Of Occurence',
                            'employee_appeal'=>'Appealed','cao_verdict' => 'Verdict');
        }   
        
        $verdict_array = array('' => 'All',1 =>'Guilty',2 => 'Not Guilty');
        $employee_appeal_array = array('' => 'All',1 =>'Yes',2 => 'No');
        
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
                'cao_verdict' => array(
                    'type' => 'select',
                    'filter_data' => $verdict_array,
                ),
                'employee_appeal' => array(
                    'type' => 'select',
                    'filter_data' => $employee_appeal_array,
                ),
                'date_of_occurrence' => array('type' => 'datepicker'),
            )
        );
        return $dataTmp;
    }
    
    //function to get the list of employees on businessunit_id and department_id
    public function getemployees($businessunit_id,$department_id)
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
        }
        $where_condition = " where user_id!=$loginUserId";
        if(is_numeric($businessunit_id))
        {
            $where_condition .= " and businessunit_id = $businessunit_id ";    
        }
        if(is_numeric($department_id) && $department_id != 0)
        {
            $where_condition .= " and department_id = $department_id ";   
        }
        
        $db = Zend_Db_Table::getDefaultAdapter();
        //$query ="select * from main_employees_summary $where_condition";
        $query ="select user_id,userfullname,profileimg,jobtitle_id,jobtitle_name,employeeId,reporting_manager,reporting_manager_name from main_employees_summary $where_condition";
        $employee_data = $result = $db->query($query)->fetchAll();
        return $employee_data;
    }
    //function to save/update incident
        public function SaveorUpdateIncidentData($data, $where)
    {
        if($where != ''){
            $this->update($data, $where);
            return 'update';
        } else {
            $this->insert($data);
            $id=$this->getAdapter()->lastInsertId('main_disciplinary_incident');
            return $id;
        }
    }
}