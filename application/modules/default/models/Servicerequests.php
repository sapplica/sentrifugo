<?php

/* * ******************************************************************************* 
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
 * ****************************************************************************** */

/**
 * Model of Servicerequests
 *
 * @author ramakrishna
 */
class Default_Model_Servicerequests extends Zend_Db_Table_Abstract 
{
    /**
     *Name of the table
     * @var string 
     */
    protected $_name = 'main_sd_requests';
    /**
     * Name of the primary key field.
     * @var string 
     */
    protected $_primary = 'id';
    
    /**
    * This function gives all content for grid view.    
    * @param string $sort          = ascending or descending
    * @param string $by            = name of field which to be sort
    * @param integer $pageNo        = page number
    * @param integer $perPage       = no.of records per page
    * @param array $searchData    = search string
    * @param string $call          = type of call like ajax.
    * @return array  Array of data.
    */
   public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$grid_type,$status_value,$p4,$p5)
   {
   	
   	
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;            
        }
        $status_value_arr = array(1 => 'Open',2 => 'Closed',3 => 'Cancelled',4 => 'Overdue',5 => 'Duetoday',
                                6 => 'To approve',7 => 'Approved',8 => 'Rec_pending',9 => 'Rec_wapproval',
                                10 => 'Rec_toapprove',11 => 'To approve',12 => 'App_approved',13 => 'To manager approve',
                                14 => 'Rept_approved',15 => 'Req_pending',16 => 'Rejected',17 => 'Raiser_open',18 => 'Manager approved',
                                19 => 'Manager rejected',20 => 'Rec_app_approved',21 => 'Rec_app_rejected',
                                22 => 'Rec_app_closed',23 => 'To management approve',24 => 'Management approved',25 => 'Management rejected'
            );
        
        $status_search_arr = array(6 =>" (status = 'To management approve' or status = 'To manager approve') ",
            9 => " (status = 'To management approve' or status = 'To manager approve') ",
            7 => " (status = 'Manager approved' or status = 'Management Approved' or status = 'Manager rejected' or status = 'Management Rejected') ",
            8 => " (status = 'Manager approved' or status = 'Management Approved' or status = 'Manager rejected' or status = 'Management Rejected') ",
            10 => " (reporting_manager_id = '".$loginUserId."' and status ='To manager approve') ",
            12 => " (status = 'Approved' or status = 'Rejected') ",
            14 => " (status = 'Manager approved' or status = 'Manager rejected') ",
            15 => " (status != 'Cancelled' and status != 'Closed') ",
            5 => " date_add(date(r.createddate),interval 10 day) = date(now()) ",
            4 => " date_add(date(r.createddate),interval 10 day) < date(now()) and status='Open'",
            17 => " ( status not in ('Closed','Cancelled','Rejected')) ",
            20 => " (status = 'Manager approved' or status = 'Management Approved') ",
            21 => " (status = 'Manager rejected' or status = 'Management Rejected')" ,
            22 => " (status = 'Closed' or status = 'Rejected')",
            );
        
        $grid_type_arr = $this->getGridtypearr();
        
        $grid_type = sapp_Global::_decrypt($grid_type);
        
       // echo $grid_type;exit;
        
        $status_value = sapp_Global::_decrypt($status_value);
        $searchQuery = '';
        $searchArray = array();        
        
        if($searchData != '' && $searchData!='undefined')
        {
            $searchValues = json_decode($searchData);
       
            if(count($searchValues) >0)
            {
            	
            	
                foreach($searchValues as $key => $val)
                {    
                	
                	
                    if($key == 'createddate')                    
                        $searchQuery .= " date(".$key.") = '".  sapp_Global::change_date($val,'database')."' AND ";					
                    else if($key == 'category_name')
                        	$searchQuery .= " r.service_desk_name like '%".$val."%' OR r.service_request_name like '%".$val."%' OR ac.name like '%".$val."%' AND ";
                    else if($key == 'request_name')
                        		$searchQuery .= " r.service_request_name like '%".$val."%' OR a.name like '%".$val."%' AND ";
                   else if($key == 'description')
                        		$searchQuery .= " r.description like '%".$val."%' AND ";  
                   else if($key == 'request_for')
                        		$searchQuery .= " r.request_for = ".$val." AND ";      		   		
                    			
                    else
                        $searchQuery .= " ".$key." like '%".$val."%' AND ";
                    
                    $searchArray[$key] = $val;
                }
             
                $searchQuery = rtrim($searchQuery," AND");
            }
        }
                        
        if(is_numeric($status_value) && $status_value > 0 && array_key_exists($status_value, $status_value_arr) )
        {
            if(!array_key_exists($status_value,$status_search_arr))
            {                 
                $newsearchQuery = " status = '".$status_value_arr[$status_value]."'";                               
            }            
            else 
            {
                $newsearchQuery = $status_search_arr[$status_value];
            }
            if($searchQuery != '')
            {
                $searchQuery .= " and ".$newsearchQuery;
            }
            else 
            {
                $searchQuery .= $newsearchQuery; 
            }
        }
        
        $objName = 'servicerequests';

        $tableFields = array('action'=>'Action',
                        'ticket_number' => 'Ticket#',
        		        'request_for'=>'Request For',
                        'category_name' => 'Category',
                        'request_name' => 'Request Type/Asset Name',
                        'priority' => 'Priority',
                        'description' => 'Description',
                        'raised_by_name' => 'Raised By',
                        'createddate' => 'Raised On',
                        'status' => 'Status',
                        );
        
        if($status_value != '')
            unset($tableFields['status']);
        $bool_arr = array('' => 'All',1=>'Low',2=>'Medium',3=>'High');                
 
        $tablecontent = $this->getRequestData($sort, $by, $pageNo, $perPage,$searchQuery,$grid_type,$status_value);                    
        
        $menu_name_arr = $this->getServicemenunames();        
        $menuName = $menu_name_arr[$grid_type];
        
        $dataTmp = array(
            'sort' => $sort,
            'by' => $by,
            
            'menuName' =>$menuName,
            'pageNo' => $pageNo,
            'perPage' => $perPage,				
            'tablecontent' => $tablecontent['table_content'],
            'row_count' => $tablecontent['count'],
            'objectname' => $objName,
            'extra' => array(),
            'tableheader' => $tableFields,
            'jsGridFnName' => 'getAjaxgridData',
            'jsFillFnName' => '',
            'searchArray' => $searchArray,
            'call'=>$call,   
            'grid_type' => $grid_type,
            'status_value' => $status_value,
            'view_link' => BASE_URL.'servicerequests/view/id/{{id}}/t/'.sapp_Global::_encrypt($grid_type).($status_value != ''?"/v/".sapp_Global::_encrypt($status_value):""),
            'add_link' => BASE_URL.'servicerequests/add/t/'.sapp_Global::_encrypt($grid_type).($status_value != ''?"/v/".sapp_Global::_encrypt($status_value):""),
            'dashboardcall' => $dashboardcall,
            'search_filters' => array(
                            'priority' => array(
                                'type' => 'select',
                                'filter_data' => $bool_arr,
                            ),
                            'createddate'=>array('type'=>'datepicker'),
            	         	'request_for'=> array(
            				'type' => 'select',
            				'filter_data' => array('' => 'All',1 => 'Service', 2 => 'Asset'),
            		),
                        ),
        );
        if($grid_type_arr[$grid_type] == 'request')             
            $dataTmp['add'] ='add';
        return $dataTmp;
   }
   /**
    * This function is used to give menu names for different scenarios.
    * @return array Array of menu names.
    */
   public function getServicemenunames()
   {
       return array(1=> 'My request summary',2=>'My action summary',3=>'My action summary', 
                    4 => 'My action summary',5=> 'My action summary',6 => 'My action summary',
                    7=> 'My action summary',8 => 'My action summary',9 => 'All request summary');
   }
   /**
    * This function will return array of grid types
    * @return array Array of grid types.
    */
   public function getGridtypearr()
   {
       return array(1 =>'request',2 => 'receiver',3 => 'all',4 => 'reporting',5=> 'approver',6=> 'rec_rept',7 => 'all_rec_rept',8 => 'rept_app',9 => 'org_head');
   }
   /**
    * 
    * @return array Array of grid types in reverse order
    */
   public function getGridtypearr_rev()
   {
       return array('request' => 1,'receiver' => 2,'all' => 3,'reporting' => 4,'approver' => 5,'rec_rept' => 6,'all_rec_rept' => 7,'rept_app' => 8,'org_head' => 9);
   }
   /**
    * This function gives data for grid view.    
    * @param string $sort          = ascending or descending
    * @param string $by            = name of field which to be sort
    * @param integer $pageNo        = page number
    * @param integer $perPage       = no.of records per page
    * @param string $searchQuery   = search string
    * @param string $grid_type     = type of grid like my action summary,all requests ,my request summary.
    * @return  array ResultSet;
    */
    public function getRequestData($sort, $by, $pageNo, $perPage,$searchQuery,$grid_type,$status_value)
    {   
    	
        $grid_type_arr = $this->getGridtypearr();
        //echo $grid_type_arr[$grid_type];exit;
        $db = Zend_Db_Table::getDefaultAdapter();
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;            
            $login_bu = $auth->getStorage()->read()->businessunit_id;
            $login_dept = $auth->getStorage()->read()->department_id;
        }        
        
        $where = " r.isactive = 1 ";
     //   echo $grid_type_arr[$grid_type];
        if(array_key_exists($grid_type, $grid_type_arr))
        {
            if($grid_type_arr[$grid_type] == 'request')
                $where .= " and r.raised_by = ".$loginUserId." ";
            else if($grid_type_arr[$grid_type] == 'action')
               $where .= " and r.executor_id = ".$loginUserId." ";
            else if($grid_type_arr[$grid_type] == 'reporting')
              $where .= " and r.reporting_manager_id = ".$loginUserId." and r.status in ('To manager approve','Closed','Manager approved','Manager rejected')";                        
            else if($grid_type_arr[$grid_type] == 'rept_app')
            {
                                                
                if($status_value == 21)                    
                    $where .= " and (r.reporting_manager_id = ".$loginUserId." or case when approver_1 = ".$loginUserId." and approver_status_1 is not null then 1=1 when approver_2 = ".$loginUserId." and approver_status_2 is not null then 1=1 when approver_3 = ".$loginUserId." and approver_status_3 is not null then 1=1   end) and status not in ('Open','Cancelled')";
                else if($status_value == 20)
                    
                  $where .= " and (r.reporting_manager_id = ".$loginUserId." or case when approver_1 = ".$loginUserId." and approver_status_1 is not null then 1=1 when approver_2 = ".$loginUserId." and approver_status_2 is not null then 1=1 when approver_3 = ".$loginUserId." and approver_status_3 is not null then 1=1   end) and status not in ('Open','Cancelled')";
                else if($status_value == 22)
                   $where .= " and (r.reporting_manager_id = ".$loginUserId." or case when approver_1 = ".$loginUserId." and approver_status_1 is not null then 1=1 when approver_2 = ".$loginUserId." and approver_status_2 is not null then 1=1 when approver_3 = ".$loginUserId." and approver_status_3 is not null then 1=1   end) and status not in ('Open','Cancelled')";
                    
                else 
                   $where .= " and (r.reporting_manager_id = ".$loginUserId." or ".$loginUserId." in (approver_1,approver_2,approver_3) ) and status not in ('Open','Cancelled')";
            }       
            else if($grid_type_arr[$grid_type] == 'approver')
            {
               
               
                
               
                if($status_value == 25)
                    $where .= " and ( case when approver_1 = ".$loginUserId." and approver_status_1 is not null then 1=1 when approver_2 = ".$loginUserId." and approver_status_2 is not null then 1=1 when approver_3 = ".$loginUserId." and approver_status_3 is not null then 1=1   end) and status not in ('Open','Cancelled')";
                else if($status_value == 24)
                    $where .= " and ( case when approver_1 = ".$loginUserId." and approver_status_1 is not null then 1=1 when approver_2 = ".$loginUserId." and approver_status_2 is not null then 1=1 when approver_3 = ".$loginUserId." and approver_status_3 is not null then 1=1   end) and status not in ('Open','Cancelled')";
                else if($status_value == 23)
                   $where .= " and ( case when approver_1 = ".$loginUserId." and approver_status_1 is not null then 1=1 when approver_2 = ".$loginUserId." and approver_status_2 is not null then 1=1 when approver_3 = ".$loginUserId." and approver_status_3 is not null then 1=1   end) and status not in ('Open','Cancelled')";
                    
                else 
                    $where .= " and ( ".$loginUserId." in (approver_1,approver_2,approver_3) ) and status not in ('Open','Cancelled')";                                                      
            }
        }
       
        if($searchQuery != '')
          $where .= " AND ".$searchQuery;
              
                
        if($grid_type_arr[$grid_type] == 'rec_rept' || $grid_type_arr[$grid_type] == 'receiver')
        {
        	
    $requestData = $this->select()
                ->setIntegrityCheck(false)
                ->from(array('r'=>'main_sd_requests_summary'),array('r.service_desk_name','r.request_for'=>'request_for','r.service_request_name','r.raised_by_name','id' => 'r.sd_requests_id','priority' =>new Zend_Db_Expr("case  when r.priority = 1 then 'Low' when r.priority = 2 then 'Medium' when r.priority = 3 then 'High' end "),
                    'description'=>"CONCAT(UCASE(LEFT(r.description, 1)), SUBSTRING(r.description, 2))",'status' => 'r.status','ticket_number'=>'r.ticket_number',
                    'executor_comments'=>"r.executor_comments",'createddate' => 'DATE_FORMAT(r.createddate,"'.DATEFORMAT_MYSQL.'")','r.modifieddate','request_for'=>'if(r.request_for=1,"Service","Asset")','category_name'=>'if(r.request_for=1,r.service_desk_name,a.name)','request_name'=>'if(r.request_for=1,r.service_request_name,ac.name)'))                                
                ->joinInner(array('sdc' => 'main_sd_configurations'), "sdc.id = r.service_desk_conf_id and sdc.isactive = 1 and find_in_set(".$loginUserId.",sdc.request_recievers)",array())
                 ->joinLeft(array('ac'=>'assets'), 'r.service_desk_id = ac.id and ac.isactive=1 ', array('ac.name'))
               ->joinLeft(array('a'=>'assets_categories'), 'r.service_request_id = a.id AND a.is_active=1 and a.parent=0 ', array('a.name'))
                ->where($where." and r.createdby != ".$loginUserId);
            
            if($grid_type_arr[$grid_type] == 'rec_rept')
            {
            
                $rec_rept_arr = array(10,18,19,22);
                $sql1 = $requestData;
                 $sql2 = $this->select()
                        ->setIntegrityCheck(false)
                        ->from(array('r'=>'main_sd_requests_summary'),array('r.service_desk_name','r.request_for'=>'request_for','r.service_request_name','r.raised_by_name','id' => 'r.sd_requests_id','priority' =>new Zend_Db_Expr("case  when r.priority = 1 then 'Low' when r.priority = 2 then 'Medium' when r.priority = 3 then 'High' end "),
                    'description'=>"CONCAT(UCASE(LEFT(r.description, 1)), SUBSTRING(r.description, 2))",'status' => 'r.status','ticket_number'=>'r.ticket_number',
                    'executor_comments'=>"r.executor_comments",'createddate' => 'DATE_FORMAT(r.createddate,"'.DATEFORMAT_MYSQL.'")','r.modifieddate','request_for'=>'if(r.request_for=1,"Service","Asset")','category_name'=>'if(r.request_for=1,r.service_desk_name,a.name)','request_name'=>'if(r.request_for=1,r.service_request_name,ac.name)'))
                     ->joinLeft(array('ac'=>'assets'), 'r.service_desk_id = ac.id and ac.isactive=1 ', array('ac.name'))
               		->joinLeft(array('a'=>'assets_categories'), 'r.service_request_id = a.id AND a.is_active=1 and a.parent=0 ', array('a.name'))
                        ->where($where." and r.reporting_manager_id = ".$loginUserId." and r.status in ('To manager approve','Manager approved','Manager rejected','Rejected','Closed')");
                if($status_value == '')
                {     
                    $requestData = $this->select()
                            ->setIntegrityCheck(false)
                            ->union(array($sql1,$sql2));
                }
                else if(in_array($status_value, $rec_rept_arr))
                {
                    $requestData = $sql2;
                }
                else if($status_value != '' && !in_array($status_value, $rec_rept_arr))
                {
                    $requestData = $sql1;
                }
                $count_query = $requestData;
                $result = $db->query($count_query);
                $count = $result->rowCount();
                //$by = " r.".$by;
                $requestData ->order("$by $sort")
                        ->limitPage($pageNo, $perPage);
            }
            else 
            {
            	 
                $count_query = $requestData;
                $result = $db->query($count_query);
                $count = $result->rowCount();
                $by = " r.".$by;
                $requestData->order("$by $sort")
                ->limitPage($pageNo, $perPage);
                 
            }
            
             //echo $requestData;exit;
        }        
        else 
        {       	
        	
        	$requestData = $this->select()
                ->setIntegrityCheck(false)                   
                ->from(array('r'=>'main_sd_requests_summary'),array('r.service_desk_name','r.request_for'=>'request_for','r.service_request_name','r.raised_by_name','id' => 'r.sd_requests_id','priority' =>new Zend_Db_Expr("case  when r.priority = 1 then 'Low' when r.priority = 2 then 'Medium' when r.priority = 3 then 'High' end "),
                    'description'=>"CONCAT(UCASE(LEFT(r.description, 1)), SUBSTRING(r.description, 2))",'r.ticket_number','status' => 'r.status','ticket_number'=>'r.ticket_number',
                    'executor_comments'=>"r.executor_comments",'createddate' => 'DATE_FORMAT(r.createddate,"'.DATEFORMAT_MYSQL.'")','r.modifieddate','request_for'=>'if(r.request_for=1,"Service","Asset")','category_name'=>'if(r.request_for=1,r.service_desk_name,a.name)','request_name'=>'if(r.request_for=1,r.service_request_name,ac.name)'))
                ->joinLeft(array('ac'=>'assets'), 'r.service_desk_id = ac.id and ac.isactive=1 ', array('ac.name'))
               ->joinLeft(array('a'=>'assets_categories'), 'r.service_request_id = a.id AND a.is_active=1 and a.parent=0 ', array('a.name'))
                ->where($where);
            $count_query = $requestData;
            $result = $db->query($count_query);
                $count = $result->rowCount();
               $requestData ->order("$by $sort")
                ->limitPage($pageNo, $perPage);
                
                // echo $requestData;exit;
           
        }      
       
        return array('count' => $count,'table_content' => $requestData);
    }
    
    /**
     * This function will give count of service requests of a particular employee.
     * @param integer $emp_id  = id of employee.
     * @param string $grid_type = type of grid like request,action,all.
     * @return array Array of counts of different status.
     */
    public function getRequestsCnt($emp_id,$grid_type)
    {
        $counts = array();
        if($emp_id != '')
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            if($grid_type == 'request')
            {
                $query = "select status,count(id) cnt from main_sd_requests "
                        . "where isactive = 1 and raised_by = ".$emp_id." group by status";
                $result = $db->query($query);
                $rows = $result->fetchAll();
                $counts = $rows;
            }
            else if($grid_type == 'rec_rept' || $grid_type == 'receiver')
            {                                
                $executor_id = " and (executor_id = ".$emp_id;
                if($grid_type == 'rec_rept')
                {
                    $executor_id .= " or reporting_manager_id = ".$emp_id." ";
                }
                $executor_id .= " )";
                $tot_act_cnt = 0;
                $query = "SELECT count(r.id) cnt,r.status FROM `main_sd_requests_summary` AS `r` "
                        . "INNER JOIN `main_sd_configurations` AS `sdc` ON sdc.id = r.service_desk_conf_id "
                        . "and find_in_set(".$emp_id.",sdc.request_recievers) "
                        . "WHERE ( r.isactive = 1 and r.createdby != ".$emp_id.") group by r.status";
                $result = $db->query($query);
                $rows = $result->fetchAll();
                
                if(count($rows) > 0)
                {
                    foreach($rows as $cnt)
                    {                        
                        $counts[$cnt['status']] = $cnt['cnt'];
                        $tot_act_cnt += $cnt['cnt'];
                    }
                }
                
                if($grid_type == 'rec_rept')
                {
                    $toapprove_query = "select count(id) cnt,status from main_sd_requests "
                            . "where isactive = 1 and reporting_manager_id = ".$emp_id." and status in "
                            . "('To manager approve','Manager approved','Manager rejected','Rejected','Closed') group by status";
                    $toapprove_result = $db->query($toapprove_query);
                    $toapprove_row = $toapprove_result->fetchAll();
                    
                    if(count($toapprove_row) > 0)
                    {
                        $disp_st = array('To manager approve' => 'to_approve','Manager approved' => 'manager_approved',
                            'Manager rejected' => 'manager_rejected','Rejected' => 'mrejected','Closed' => 'mclosed');
                        foreach($toapprove_row as $cnt)
                        {                              
                            $counts[$disp_st[$cnt['status']]] = $cnt['cnt'];
                            $tot_act_cnt += $cnt['cnt'];
                        }
                    }                                                 
                }
                
                $over_due_query = "select count(sdr.id) cnt 
                                   from main_sd_requests sdr inner join main_sd_configurations sdc 
                                   on sdc.id = sdr.service_desk_conf_id and sdc.isactive = 1 
                                   and find_in_set(".$emp_id.",sdc.request_recievers)"
                                   . "where sdr.isactive = 1 and sdr.status='Open' "
                        . "and sdr.createdby != ".$emp_id." and date_add(date(sdr.createddate),interval 10 day) < date(now())";
                $over_due_result = $db->query($over_due_query);
                $over_due_row = $over_due_result->fetch();
                $counts['overdue'] = $over_due_row['cnt'];                
                
                $today_due_query = "select count(sdr.id) cnt 
                                    from main_sd_requests sdr inner join main_sd_configurations sdc 
                                    on sdc.id = sdr.service_desk_conf_id and sdc.isactive = 1 
                                    and find_in_set(".$emp_id.",sdc.request_recievers)"
                                    . "where sdr.isactive = 1 and sdr.status='Open' "
                        . "and sdr.createdby != ".$emp_id." and date_add(date(sdr.createddate),interval 10 day) = date(now())";
                $today_due_result = $db->query($today_due_query);
                $today_due_row = $today_due_result->fetch();
                $counts['duetoday'] = $today_due_row['cnt']; 
                
                $counts['all'] = $tot_act_cnt;                                
            }
            else if($grid_type == 'org_head')
            {
                $tot_act_cnt = 0;
                $query = "select sdr.status,count(sdr.id) cnt 
                          from main_sd_requests sdr  
                          where sdr.isactive = 1 group by sdr.status";
                $result = $db->query($query);
                $rows = $result->fetchAll();
                
                if(count($rows) > 0)
                {
                    foreach($rows as $cnt)
                    {
                        $counts[$cnt['status']] = $cnt['cnt'];
                        $tot_act_cnt += $cnt['cnt'];
                    }
                }
                $over_due_query = "select count(sdr.id) cnt 
                                   from main_sd_requests sdr "
                                   . " where sdr.isactive = 1 and sdr.status='Open' and date_add(date(sdr.createddate),interval 10 day) < date(now())";
                $over_due_result = $db->query($over_due_query);
                $over_due_row = $over_due_result->fetch();
                $counts['overdue'] = $over_due_row['cnt'];                
                
                $today_due_query = "select count(sdr.id) cnt 
                                    from main_sd_requests sdr "
                                    . "where sdr.isactive = 1 and sdr.status='Open' and date_add(date(sdr.createddate),interval 10 day) = date(now())";
                $today_due_result = $db->query($today_due_query);
                $today_due_row = $today_due_result->fetch();
                $counts['duetoday'] = $today_due_row['cnt'];                                
                
                $counts['all'] = $tot_act_cnt;
            }
            else if($grid_type == 'all' || $grid_type == 'all_rec_rept')
            {
                $tot_act_cnt = 0;
                $query = "select sdr.status,count(sdr.id) cnt 
                          from main_sd_requests sdr inner join main_sd_configurations sdc 
                          on sdc.id = sdr.service_desk_conf_id and sdc.isactive = 1 
                          and find_in_set(".$emp_id.",sdc.request_recievers) 
                          where sdr.isactive = 1 and sdr.createdby != ".$emp_id." group by sdr.status";
                $result = $db->query($query);
                $rows = $result->fetchAll();
                
                if(count($rows) > 0)
                {
                    foreach($rows as $cnt)
                    {
                        $counts[$cnt['status']] = $cnt['cnt'];
                        $tot_act_cnt += $cnt['cnt'];
                    }
                }
                $over_due_query = "select count(sdr.id) cnt 
                                   from main_sd_requests sdr inner join main_sd_configurations sdc 
                                   on sdc.id = sdr.service_desk_conf_id and sdc.isactive = 1 
                                   and find_in_set(".$emp_id.",sdc.request_recievers)"
                                   . "where sdr.isactive = 1 and sdr.status='Open' and sdr.createdby != ".$emp_id." and date_add(date(sdr.createddate),interval 10 day) < date(now())";
                $over_due_result = $db->query($over_due_query);
                $over_due_row = $over_due_result->fetch();
                $counts['overdue'] = $over_due_row['cnt'];                
                
                $today_due_query = "select count(sdr.id) cnt 
                                    from main_sd_requests sdr inner join main_sd_configurations sdc 
                                    on sdc.id = sdr.service_desk_conf_id and sdc.isactive = 1 
                                    and find_in_set(".$emp_id.",sdc.request_recievers)"
                                    . "where sdr.isactive = 1 and sdr.status='Open' and sdr.createdby != ".$emp_id." and date_add(date(sdr.createddate),interval 10 day) = date(now())";
                $today_due_result = $db->query($today_due_query);
                $today_due_row = $today_due_result->fetch();
                $counts['duetoday'] = $today_due_row['cnt'];                                
                
                $counts['all'] = $tot_act_cnt;
            }            
            else if($grid_type == 'reporting')
            {
                $executor_id = " and reporting_manager_id = ".$emp_id;
                $tot_act_cnt = 0;
                $query = "select status,count(id) cnt from main_sd_requests "
                        . "where isactive = 1 ".$executor_id." and status in ('To manager approve','Closed','Manager approved','Manager rejected','Rejected') group by status";
                $result = $db->query($query);
                $rows = $result->fetchAll();
                
                if(count($rows) > 0)
                {
                    foreach($rows as $cnt)
                    {                        
                        $counts[$cnt['status']] = $cnt['cnt'];
                        $tot_act_cnt += $cnt['cnt'];
                    }
                }
                                
                $counts['all'] = $tot_act_cnt;
            }
            
            else if($grid_type == 'approver' || $grid_type == 'rept_app')
            {
                $tot_act_cnt = 0;
                $rept_app_str = "";
                $status_str = "'Closed','Rejected','To management approve','Management approved','Management rejected'";
                if($grid_type == 'rept_app')
                {
                    $rept_app_str = " or reporting_manager_id = ".$emp_id;
                    $status_str .= ",'To manager approve','Manager approved','Manager rejected'"; 
                }
                
                $query = "select count(id) cnt,status from main_sd_requests "
                        . "where isactive = 1 and (".$emp_id." in (approver_1,approver_2,approver_3) ".$rept_app_str." ) and status in (".$status_str.") "
                        . "group by status";
                $result = $db->query($query);
                $rows = $result->fetchAll();
                
                if(count($rows) > 0)
                {
                    foreach($rows as $cnt)
                    {                        
                        $counts[$cnt['status']] = $cnt['cnt'];
                        $tot_act_cnt += $cnt['cnt'];
                    }
                }
                                
                $counts['all'] = $tot_act_cnt;
            }
        }
        return $counts;
    }//end of getRequestsCnt
    
    /**
     * This function gives options to selection box based on department and business unit ids.
     * @param integer $login_bu          = id of business unit of login user.
     * @param integer $login_dept        = id of department of login user.
     * @param integer $service_desk_flag = service desk flag of business unit of login user.
     * @return array  Array of service desk departments and their ids.
     */
    public function getServiceTypes($login_bu,$login_dept,$service_desk_flag)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select sc.id,sd.service_desk_name,sd.id service_desk_id "
                . "from main_sd_configurations sc inner join main_sd_depts sd on sd.id = sc.service_desk_id and sd.isactive = 1 "
                . "where sc.isactive = 1 and sc.businessunit_id = ".$login_bu." and sc.service_desk_flag = ".$service_desk_flag." ";
        if($service_desk_flag == 0 && $login_dept!='')
            $query .= " and sc.department_id = ".$login_dept;
        $query .= " group by sd.id order by sd.service_desk_name asc";
        
        $result = $db->query($query);
        $rows = $result->fetchAll();
        return $rows;
    }// end of getServiceTypes
    
    /**
    * This function is used to save/update data in database.    
    * @param array $data   =  array of form data.
    * @param string $where =  where condition in case of update.
    *
    * @return string  Primary id when new record inserted,'update' string when a record updated.
    */
    public function SaveorUpdateRequestData($data, $where)
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
    }// end of SaveorUpdateRequestData
    
    /**
     * This function is used to check login id user is a request receiver or not.
     * @param integer $emp_id             = id of login user
     * @param integer $businessunit_id    = id of business unit
     * @param integer $service_desk_flag  = service desk flag value
     * @return string  Yes/No
     */
    public function check_receiver($emp_id,$businessunit_id)
    {
        $status = "no";
        if($emp_id != '')
        {
            if($businessunit_id == 0)
                $service_desk_flag = '0';
            else 
            {
                $bu_model = new Default_Model_Businessunits();
                $bu_data = $bu_model->getSingleUnitData($businessunit_id);
                $service_desk_flag = $bu_data['service_desk_flag'];            
            }
            if($service_desk_flag != '' && $businessunit_id != '')
            {
                $db = Zend_Db_Table::getDefaultAdapter();
                $query = "select count(*) cnt from main_sd_configurations "
                        . "where isactive = 1 and businessunit_id = ".$businessunit_id." and service_desk_flag = ".$service_desk_flag." "
                        . "and find_in_set(".$emp_id.",request_recievers) ";
                
                $result = $db->query($query);
                $row = $result->fetch();
                if($row['cnt'] != '' && $row['cnt'] > 0)
                    $status = "yes";
            }
        }
        return $status;
    }// end of check_receiver
    
    /**
     * This function gives all data of the request based on the id.
     * @param integer $id  = id of service request
     * @return array Array of data
     */
    public function getRequestById($id)
    {
        $data = array();
        if($id != '')
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            
            $query = "select * from main_sd_requests_summary where sd_requests_id = ".$id;
            $result = $db->query($query);
            $data = $result->fetch();
        }
        return $data;
    }//end of getRequestById
    
    /**
     * This function is used to check login user is a reporting manager or not.
     * @param int $emp_id = id of login employee
     * @return string Yes/no.
     */
    public function check_reporting($emp_id)
    {
        $status = "no";
        if($emp_id != '')
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select count(id) cnt from main_employees_summary "
                    . "where isactive = 1 and reporting_manager = ".$emp_id;
            $result = $db->query($query);
            $row = $result->fetch();
            if($row['cnt'] > 0)
                $status = "yes";
        }
        return $status;
    }//end of check_reporting
    
    /**
     * This function is used to check login user is a approver or not.
     * @param int $emp_id = id of login employee
     * @return string Yes/no.
     */
    public function check_approver($emp_id)
    {
        $status = "no";
        if($emp_id != '')
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select count(id) cnt from main_sd_requests where ".$emp_id." in (approver_1,approver_2,approver_3) and isactive = 1  ";
            $result = $db->query($query);
            $row = $result->fetch();
            if($row['cnt'] > 0)
                $status = "yes";
        }
        return $status;
    }//end of check_approver
    /**
     * This function gives reporting manager id based on service request id.
     * @param integer $request_id  = id of service desk request
     * @return integer
     */
    public function getReptId($request_id)
    {
        $return = '';
        if($request_id != '')
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select es.reporting_manager from main_sd_requests sdr "
                    . "inner join main_employees_summary es on es.user_id = sdr.raised_by and es.isactive =1 "
                    . "where sdr.id = ".$request_id." and sdr.isactive = 1 and sdr.status = 'Open'";
            $result = $db->query($query);
            $row = $result->fetch();
            $return = $row['reporting_manager'];
        }
        return $return;
    }//end of getreptid
    
    /**
     * This function gives list of approvers of a particular service desk request.
     * @param int $request_id  = id of service desk request
     * @param string $flag     = type of call
     * @return array Array of approvers.
     */
    public function getApprovers($request_id,$flag)
    {
        $appr_arr = array();
        if($request_id != '')
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            if($flag == 'request')
            {
                $query = "select sdc.approver_1,sdc.approver_2,sdc.approver_3 "
                        . "from main_sd_requests sdr "
                        . "inner join main_sd_configurations sdc on sdc.isactive = 1 "
                        . "and sdc.id = sdr.service_desk_conf_id "
                        . "where sdr.isactive = 1 and sdr.id = ".$request_id." and sdr.status = 'Open'";
            
            }
            else 
            {
                $query = "select sdc.approver_1,sdc.approver_2,sdc.approver_3 "
                        . "from main_sd_configurations sdc "                                                
                        . "where sdc.isactive = 1 and sdc.id = ".$request_id." ";
            }
            $result = $db->query($query);
            $row = $result->fetch();
            
            if(count($row) > 0 && !empty($row))
            {
                foreach($row as $key => $value)
                {
                    if($value != '')
                        $appr_arr[$key] = $value;
                }
            }
        }
        return $appr_arr;
    }//end of getApprovers
    
    /**
     * This function give approver order based on employee id and request id
     * @param integer $emp_id     = id of the employee
     * @param integer $request_id = id of the request
     * @return array/string  Array or string
     */
    public function getApproverLevel($emp_id,$request_id)
    {
        $approver_level = "view";
        if($emp_id != '' && $request_id != '')
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $app_query = "select approver_1,approver_2,approver_3,approver_status_1,approver_status_2,approver_status_3 "
                    . "from main_sd_requests where id = ".$request_id." and isactive = 1 and status = 'To management approve'";
            $app_result = $db->query($app_query);
            $app_row = $app_result->fetch();
            
            if(count($app_row) > 0)
            {
                if($pos = array_search($emp_id,$app_row))
                {                    
                    $app_pos = substr($pos,-1);
                    if($app_pos == 1)
                    {
                        if($app_row['approver_status_'.$app_pos] == '')
                            $approver_level = $app_pos;
                        else 
                            $approver_level = 'view';
                    }
                    else
                    {
                        if($app_row['approver_status_'.$app_pos] == '' && $app_row['approver_status_'.($app_pos-1)] == 'Approve')
                            $approver_level = $app_pos;
                        else 
                            $approver_level = "view";
                    }
                    $max_app = 0;
                    for($i=1;$i<=3;$i++)
                    {
                        if($app_row['approver_'.$i] != '')
                            $max_app++;
                    }
                    if($approver_level != 'view')
                    {
                        $approver_level = array('app_pos' => $app_pos,'max_app' => $max_app);
                    }
                }
            }
        }
        return $approver_level;
    }
    
    /**
     * This function will give email id of cc receivers and request receivers.
     * @param integer $config_id   = id of service desk configuration
     * @return array Array of email ids
     */
    public function getCC_mails($config_id)
    {
        $mails_arr = array();
        if($config_id != '')
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select request_recievers,cc_mail_recievers "
                    . "from main_sd_configurations where id = ".$config_id." and isactive = 1";
            $result = $db->query($query);
            $mails_arr = $result->fetch();
        }
        return $mails_arr;
    }
    
    /**
     * This function gives email ids based on user ids.
     * @param string $total_ids   = user ids in comma separated
     * @return array  Array of mail ids.
     */
    public function getEmailIds($total_ids)
    {
        $mails_arr = array();
        if($total_ids != '')
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select user_id,emailaddress from main_employees_summary "
                    . "where user_id in (".$total_ids.")";
            $result = $db->query($query);
            $row = $result->fetchAll();
            if(count($row) > 0)
            {
                foreach($row as $data)
                {
                    $mails_arr[$data['user_id']] = $data['emailaddress'];
                }
            }
        }
        return $mails_arr;
    }
    
    /**
     * This function gets total data based on request id.
     * @param integer $req_id  = id of service desk request.
     * @return array Array of request data.
     */
    public function getDataSummary($req_id)
    {
        $sdata = array();
        if($req_id != '')
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select * from main_sd_requests_summary where sd_requests_id = ".$req_id;
            $result = $db->query($query);
            $sdata = $result->fetch();
        }
        return $sdata;
    }
    
    /**
     * This function is used to check whether request raiser is equal to request receiver,if yes
     * it will restrict from saving the details.
     * @param integer $service_desk_conf_id = id of service desk configuration
     * @param integer $loginUserId          = id of request raiser 
     * @return string  Yes/no
     */
    public function check_raiser($service_desk_conf_id,$loginUserId)
    {
        $proceed = 'yes';
        if($service_desk_conf_id != '' && $loginUserId != '')
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select request_recievers from main_sd_configurations "
                    . "where id = ".$service_desk_conf_id." and isactive = 1";
            $result = $db->query($query);
            $row = $result->fetch();
            $ids = explode(',', $row['request_recievers']);
            
            if(count($ids) > 0)
            {
                if(in_array($loginUserId,$ids))
                {
                    if(count($ids) == 1 && $ids[0] == $loginUserId)
                        $proceed = 'no';
                }
            }
                        
        }
        return $proceed;
    }//end of check_raiser
    
    /**
     * This function get history of a request.
     * @param integer $request_id  = id of the request.
     * @return array Array of history.
     */
    public function getRequestHistory($request_id)
    {
        $history = array();
        if($request_id != '')
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select concat(description,emp_name) history,emp_profileimg,date(createddate) hdate,time(createddate) htime,comments "
                    . "from main_request_history where request_id = ".$request_id." order by createddate desc";
            $result = $db->query($query);
            $history = $result->fetchAll();
        }
        return $history;
    }//end of getrequesthistory
    
    public function getActiveCategoriesData()
    {
    	 $userdata=	$this->select()
    	->setIntegrityCheck(false)
    	->from(array('c'=>'assets_categories'),array('c.*'))
    	->order('c.id')
    	->where('c.is_active =1 and c.parent=0 ');
    	return $this->fetchAll($userdata)->toArray();
    
    } 
  public function getuserallocatedAssetData($userid){
  	
  	$db = Zend_Db_Table::getDefaultAdapter();
 	/*$query = "SELECT s.id AS service_conf_id, a.*  FROM assets a 
			INNER JOIN main_sd_configurations s ON s.service_desk_id = a.category AND s.request_for = 2 
			WHERE a.isactive=1 AND a.allocated_to=".$userid." 
			AND a.category IN(SELECT DISTINCT service_desk_id FROM main_sd_configurations sc WHERE sc.isactive=1 AND sc.request_for = 2)";*/
 	
  	$query='';
  	if($userid!='') {
 	$query = "SELECT name,id,category FROM assets WHERE category 
			  IN(SELECT service_desk_id  FROM main_sd_configurations WHERE request_for=2 AND isactive=1 AND 
			  service_desk_id IN(SELECT DISTINCT category FROM assets WHERE allocated_to=$userid)) AND isactive=1 AND allocated_to=$userid";
  	}
  	$result = $db->query($query);
  	$userassets = $result->fetchAll();
  
  	return $userassets;
  }
  
  
  
  
/*  public function getuserallocatedAssetcatData($asset_id){
 	$db = Zend_Db_Table::getDefaultAdapter();
 	$query ="
		 SELECT `c`.*, sc.id as 'service_conf_id'
		 FROM `assets` AS `c`
		 LEFT JOIN `main_sd_configurations` AS `sc` ON c.category = sc.service_desk_id
		 WHERE (sc.isactive=1 AND c.isactive=1 AND c.id=".$asset_id." )";
 	$result = $db->query($query);
 	$userassetcat = $result->fetchAll();
 	return $userassetcat;
 
 } */
    
}//end of class
