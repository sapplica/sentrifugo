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

class Default_Model_Usermanagement extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_users';
    protected $_primary = 'id';
    
    /**
     * This function gives data for grid view.     
     * @param string $sort          = ascending or descending
     * @param string $by            = name of field which to be sort  
     * @param integer $pageNo        = page number
     * @param integer $perPage       = no.of records per page
     * @param string $searchQuery   = search string
     * 
     * @return array  ResultSet;
     */
    public function getUsersData($sort, $by, $pageNo, $perPage,$searchQuery)
    {
        $where = "u.isactive != 5  and u.id > 1 ";

        if($searchQuery)
            $where .= " AND ".$searchQuery;       
		$flg = '0';
	
		if($by == 'u.isactive' && $sort == 'ASC')
		{
			$flg = '1';
			$sort = 'DESC';
		}
		if($by == 'u.isactive' && $sort == 'DESC' && $flg == '0')
		{
			$sort = 'ASC';
		}
	
        $userData = $this->select()
                        ->setIntegrityCheck(false)
                        ->from(array('u'=>$this->_name),array('u.*','u.isactive'=>"if(u.isactive = 1,'Active','Inactive')"))
                        ->joinInner(array('r'=>'main_roles'), 'r.id=u.emprole and r.isactive = 1 and r.group_id = '.USERS_GROUP." ",array('rolename'=>'r.rolename'))
                        
                        ->where($where)
                        ->order("$by $sort") 
                        ->limitPage($pageNo, $perPage);        
        return $userData;       		
    }//end of getUsersData
    
    /**
     * This function is used for getting maximum employee number.
     * @parameters   $emp_identity_code = identity code of employee.
     * 
     * @return  {Integer} Maximum employee id.
     */
    public function getMaxEmpId($emp_identity_code)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select max(cast(substr(employeeId,".(strlen($emp_identity_code)+1).") as unsigned)) max_empid 
                    from main_users where lower(substr(employeeId,1,".(strlen($emp_identity_code)).")) = lower('".$emp_identity_code."') and employeeId is not null and employeeId != ''";
        $result = $db->query($query);
        $row = $result->fetch();
        $maxid = $row['max_empid'];
        
        return ($maxid == 0)?1:($maxid+1);
    }
    public function getdata_user_report($search_arr,$per_page,$page_no,$sort_name,$sort_type)
    {
        if($sort_name == 'isactive')
            $sort_name = "u.isactive";
        if($sort_name == 'createddate')
            $sort_name = "u.createddate";
        $db = Zend_Db_Table::getDefaultAdapter();
        $search_str = "";
        $user_search = " where u.id != 1 ";
        foreach($search_arr as $key => $value)
        {
            if($value != '')
            {
                if($key != 'logindatetime')
                {
                    if( $key == 'createddate')
                        $search_str .= " date(u.".$key.") = '".  sapp_Global::change_date($value,'database')."' and";
                    else if($key == 'u.isactive')
                    {
                        if($value == 0)
                            $search_str .= " u.isactive != 1";
                        else 
                            $search_str .= " u.isactive = 1";
                    }
                    
                    else 
                        $search_str .= " ".$key." = '".$value."' and";
                }
            }
        }
        if($search_str != '')
        {
            $search_str = trim($search_str,"and");
            $search_str = " and ".$search_str;
        }
        $offset = ($per_page*$page_no) - $per_page;
        $limit_str = " limit ".$per_page." offset ".$offset;
        $tot_query = "select date(max(logindatetime)) lastlog from  main_users u
                  left join main_userloginlog l  on u.id = l.userid and l.userid != 1 and u.id != 1
                  inner join main_roles r on r.id = u.emprole ".$user_search." ".$search_str."
                  group by u.id ";
        if(isset($search_arr['logindatetime']) && $search_arr['logindatetime'] != '')
        {
            $tot_query = "select * from (".$tot_query.") res where date(lastlog) = '".  sapp_Global::change_date(sapp_Global::getGMTformatdate($search_arr['logindatetime']),'database')."' ";
        }
        $tot_result = $db->query($tot_query);
        $count = $tot_result->rowCount();        
        $page_cnt = ceil($count/$per_page);
        if($sort_name != 'u.isactive'){
        $query = "select u.userfullname,r.rolename,u.emailaddress,u.employeeId,date(max(logindatetime)) lastlog,
                  if(u.isactive = 1,1,0) isactive,date(u.createddate) createdate from main_users u  
                  left join main_userloginlog l  on u.id = l.userid and l.userid != 1 and u.id != 1
                  inner join main_roles r on r.id = u.emprole ".$user_search." ".$search_str."
                  group by u.id order by ".$sort_name." ".$sort_type." ";        
        }
        else{
        if($sort_type == 'DESC'){
          $str  = "when 0 then 0 when 1 then 1 when 2 then 0 when 3 then 0 when 4 then 0 when 5 then 0 else 99 end"; 
        }else if($sort_type == 'ASC'){
          $str  = "when 0 then 1 when 1 then 0 when 2 then 1 when 3 then 1 when 4 then 1 when 5 then 1 else 99 end"; 
        }
        $query = "select u.userfullname,r.rolename,u.emailaddress,u.employeeId,date(max(logindatetime)) lastlog,
                  if(u.isactive = 1,1,0) isactive,date(u.createddate) createdate from main_users u  
                  left join main_userloginlog l  on u.id = l.userid and l.userid != 1 and u.id != 1
                  inner join main_roles r on r.id = u.emprole ".$user_search." ".$search_str."
                  group by u.id order by case ".$sort_name." ".$str." ";
        }
       
        if(isset($search_arr['logindatetime']) && $search_arr['logindatetime'] != '')
        {
            $query = "select * from (".$query.") res where date(lastlog) = '".  sapp_Global::change_date(sapp_Global::getGMTformatdate($search_arr['logindatetime']),'database')."' ";
        }
        $query .= " ".$limit_str;
        $result = $db->query($query);
        $data = $result->fetchAll();
        return array('rows' => $data,'page_cnt' => $page_cnt);
    }
    /**
     * This function will return all data of user by passing its primay key id.
     * @parameters
     * @param id   =  id of user (primary key)
     * 
     * @return Array of user data.
     */
    public function getUserDataById($id)
    {
        if($id != '')
        {
            $row = $this->fetchRow("id = ".$id);
            if (!$row) 
            {
                throw new Exception("Could not find row ".$id." in user");
            }
            return $row->toArray();
        }
    }
    
    /**
     * This function is used to save/update data in database.
     * @parameters
     * @param data  =  array of form data.
     * @param where =  where condition in case of update.
     * 
     * @return  Primary id when new record inserted,'update' string when a record updated.
     */
    public function SaveorUpdateUserData($data, $where)
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
    public function getTableName()
    {
        return $this->_name;
    }
    public function getUserCntByRole($role_id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select count(*) cnt from main_users where  isactive = 1 and emprole = ".$role_id;
        $result = $db->query($query);
        $row = $result->fetch();
        return $row['cnt'];
    }
    public function getRefferedByForUsers()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select u.id,ifnull(u.userfullname,c.candidate_name) cname 
                  from main_users u 
                  left join main_candidatedetails c on c.id = u.rccandidatename and c.isactive = 1
                  inner join main_roles r on r.id = u.emprole and r.group_id is not null and r.group_id not in (".USERS_GROUP.")
                  where u.isactive = 1 and  u.userstatus = 'old' group by u.id";
        $result = $db->query($query);
        $options = array();
        while($row = $result->fetch())
        {
            $options[$row['id']] = $row['cname'];
        }
        return $options;
    }
    public function updateuserstatus($userid)
    {
	  $db = Zend_Db_Table::getDefaultAdapter();
		
	  $db->query("update main_users  set userstatus = 2 where id = '".$userid."' ");		

    }
    /**
     * This function gives all content for grid view.
     * @parameters
     * @param $sort          = ascending or descending
     * @param $by            = name of field which to be sort  
     * @param $pageNo        = page number
     * @param $perPage       = no.of records per page
     * @param $searchData    = search string
     * @param $call          = type of call like ajax. 
     * @return  Array;
     */
    public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$p2,$p3,$p4,$p5)
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
                        $searchQuery .= " ".$key." like '%".$val."%' AND ";
                        $searchArray[$key] = $val;
                }
                $searchQuery = rtrim($searchQuery," AND");					
            }                    
        }
        $objName = 'usermanagement';

        $tableFields = array('action'=>'Action',
                             'employeeId' => 'User ID',
                             'emailaddress' => 'Email',
                             'rolename' => 'Role',
                             'u.isactive' => 'Status'
                             
                            );

        $tablecontent = $this->getUsersData($sort, $by, $pageNo, $perPage,$searchQuery);     

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
                'call'=>$call,
                'dashboardcall' => $dashboardcall,
                'search_filters' => array(
                    'u.isactive' => array('type'=>'select',
                        'filter_data'=>array(''=>'All',1 => 'Active',0 => 'Inactive',
                                            ))
                ),
        );
        return $dataTmp;
    }
    
    public function getEmpForConfigMail()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select u.id,u.userfullname,u.emailaddress 
                  from main_users u inner join main_roles r on r.id = u.emprole 
                  and r.group_id in (".MANAGEMENT_GROUP.",".SYSTEMADMIN_GROUP.",".HR_GROUP.") 
                  and r.isactive = 1  where u.isactive = 1 group by u.id 
                  union 
                  select u.id,u.userfullname,u.emailaddress from main_users u 
                  inner join main_roles r on r.id = u.emprole and r.isactive = 1 
                  and u.emprole = ".SUPERADMINROLE." where u.isactive = 1 and u.id = ".SUPERADMIN." ";
        $result = $db->query($query);
        $emp_arr = array();
        while($row = $result->fetch())
        {
            $emp_arr[$row['id']]['userfullname'] = $row['userfullname'];
            $emp_arr[$row['id']]['emailaddress'] = $row['emailaddress'];            
        }

        return $emp_arr;
    }
	public function deleteAllagencydetails($agencyid,$userid)
	{
            if($agencyid != '' && $userid != '')
            {
		$db = Zend_Db_Table::getDefaultAdapter();
		$agencyData = $db->query("update main_bgagencylist set 
								isactive = 2, modifieddate = '".gmdate("Y-m-d H:i:s")."',
								modifiedby = ".$userid."
								where id = ".$agencyid.";");		
		$pocTab = $db->query("update main_bgpocdetails set 
								isactive = 2, modifieddate = '".gmdate("Y-m-d H:i:s")."',
								modifiedby = ".$userid."
								where isactive = 1 and bg_agencyid = ".$agencyid.";");	
		$detailsTab = $db->query("update main_bgcheckdetails set 
								isactive = 3, modifieddate = '".gmdate("Y-m-d H:i:s")."',
								modifiedby = ".$userid."
								where isactive = 1 and bgagency_id = ".$agencyid.";");		
            }
		
	}
	
	public function activateAllagencydetails($agencyid,$userid)
	{
            if($agencyid != '' && $userid != '')
            {
		$db = Zend_Db_Table::getDefaultAdapter();
		$agencyData = $db->query("update main_bgagencylist set 
								isactive = 1, modifieddate = '".gmdate("Y-m-d H:i:s")."',
								modifiedby = ".$userid."
								where id = ".$agencyid.";");		
		$pocTab = $db->query("update main_bgpocdetails set 
								isactive = 1, modifieddate = '".gmdate("Y-m-d H:i:s")."',
								modifiedby = ".$userid."
								where (isactive = 2 or isactive = 3) and bg_agencyid = ".$agencyid.";");	
            }
	}
	
	public function getAgencyData($userid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
                
		$emailData = $db->query("select b.id as agencyid,u.emailaddress,u.userfullname,u.emprole,u.isactive from  main_users u
						join main_bgagencylist b on u.id = b.user_id 
						where u.id = ".$userid);
		$data = $emailData->fetch();
		return $data;
	}	
}//end of class