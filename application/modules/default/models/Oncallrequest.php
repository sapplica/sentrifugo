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

class Default_Model_Oncallrequest extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_oncallrequest';
    
	
	
	public function getAvailableOncalls($loginUserId)
	{
	 	$select = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('e'=>'main_employeeoncalls'),array('oncalllimit'=>'e.emp_oncall_limit','remainingoncalls'=>new Zend_Db_Expr('e.emp_oncall_limit - e.used_oncalls')))
						   ->where('e.user_id='.$loginUserId.' AND e.alloted_year = now() AND e.isactive = 1');  		   					   				
		return $this->fetchAll($select)->toArray();   
	
	}
	
	
	public function getsinglePendingOncallsData($id)
	{
		$result =  $this->select()
    				->setIntegrityCheck(false) 	
    				->from(array('l'=>'main_oncallrequest'),array('l.*'))
 	  				->where("l.isactive = 1 AND l.id = ".$id);
	
    	return $this->fetchAll($result)->toArray();
	}
	
	public function getUserOncallsData($id)
	{
		$result =  $this->select()
    				->setIntegrityCheck(false) 	
    				->from(array('l'=>'main_oncallrequest'),array('l.*'))
 	  				->where("l.isactive = 1 AND l.user_id = ".$id);
		
    	return $this->fetchAll($result)->toArray();
	}
	
	public function getUserApprovedOrPendingOncallsData($id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
       
		
		$query = "SELECT `l`.*,IF(l.oncallstatus = 'Approved', 'A', 'P') as status FROM `main_oncallrequest` AS `l` WHERE (l.isactive = 1 AND l.user_id = '$id' and l.oncallstatus IN(1,2))";
		
        $result = $db->query($query)->fetchAll();
	    return $result;
	}
	
	public function getManagerApprovedOrPendingOncallsData($id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
       
		
		$query = "SELECT `l`.*,IF(l.oncallstatus = 'Approved', 'A', 'P') as status,u.userfullname
				 FROM `main_oncallrequest` AS `l` left join main_users u on u.id=l.user_id 
				 WHERE (l.isactive = 1 AND l.rep_mang_id = '$id' and l.oncallstatus IN(1,2))";
		
        $result = $db->query($query)->fetchAll();
	    return $result;
	}
	
	public function getReportingManagerId($id)
	{
	    $result =  $this->select()
    				->setIntegrityCheck(false) 	
    				->from(array('l'=>'main_oncallrequest'),array('repmanager'=>'l.rep_mang_id'))
 	  				->where("l.isactive = 1 AND l.id = ".$id);
	
    	return $this->fetchAll($result)->toArray();
	}
	
	public function SaveorUpdateOncallRequest($data, $where)
	{
	    if($where != '')
		{
			$this->update($data, $where);
			return 'update';
		}
		else
		{
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_oncallrequest');
			return $id;
		}
	}
	
	public function getOncallStatusHistory($sort, $by, $pageNo, $perPage,$searchQuery,$queryflag='',$loggedinuser,$managerstring='')
	{	
	    $auth = Zend_Auth::getInstance();
			if($auth->hasIdentity()){
				$loginUserId = $auth->getStorage()->read()->id;
		}  
		if($loggedinuser == '') 
		 $loggedinuser = $loginUserId;
		 
		/* Removing isactive checking from configuration table */ 
		if($managerstring !='')
		{
		  
		  $where = "l.isactive = 1 ";
		}  
		else 
        {		
	      
		  $where = "l.isactive = 1 AND l.user_id = ".$loggedinuser." ";
		}  
		if($queryflag !='')
		{
		   if($queryflag == 'pending')
		   {
		     $where .=" AND l.oncallstatus = 1 ";
		   }
		   else if($queryflag == 'approved')
		   {
		     $where .=" AND l.oncallstatus = 2 ";
		   }
		   else if($queryflag == 'cancel')
		   {
		     $where .=" AND l.oncallstatus = 4 ";
		   }
		   else if($queryflag == 'rejected')
		   {
		     $where .=" AND l.oncallstatus = 3 ";
		   }
		
		}else
		{
		  $where .=" AND l.oncallstatus = 2 ";
		}
		
			
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$oncallStatusData = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('l'=>'main_oncallrequest'),
						          array( 'l.*','from_date'=>'DATE_FORMAT(l.from_date,"'.DATEFORMAT_MYSQL.'")',
								         'to_date'=>'DATE_FORMAT(l.to_date,"'.DATEFORMAT_MYSQL.'")',
										 'applieddate'=>'DATE_FORMAT(l.createddate,"'.DATEFORMAT_MYSQL.'")',
                                         'oncallday'=>'if(l.oncallday = 1,"Full Day","Half Day")',
						          		 new Zend_Db_Expr("CASE WHEN l.oncallstatus=2 and CURDATE()>=l.from_date THEN 'no' WHEN l.oncallstatus=1 THEN 'yes' WHEN l.oncallstatus IN (3,4) THEN 'no' ELSE 'yes' END as approved_cancel_flag "), 										 
								       ))
						   ->joinLeft(array('et'=>'main_employeeoncalltypes'), 'et.id=l.oncalltypeid',array('oncalltype'=>'et.oncalltype'))	
                           ->joinLeft(array('u'=>'main_users'), 'u.id=l.rep_mang_id',array('reportingmanagername'=>'u.userfullname'))
                           ->joinLeft(array('mu'=>'main_users'), 'mu.id=l.user_id',array('employeename'=>'mu.userfullname'))						                 			   						   
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		
		return $oncallStatusData;
		
	}
	
	
	public function getEmployeeOncallRequest($sort, $by, $pageNo, $perPage,$searchQuery,$loginUserId)
	{	
		$where = "l.isactive = 1 AND l.oncallstatus IN(1,2) AND u.isactive=1 AND l.rep_mang_id=".$loginUserId." ";
        //$where = "l.isactive = 1 AND l.oncallstatus IN(1,2) AND u.isactive=1 AND l.rep_mang_id=".$loginUserId." OR l.hr_id=".$loginUserId." and l.user_id!=".$loginUserId." ";
		
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
	    $employeeoncallData = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('l'=>'main_oncallrequest'),
						          array( 'l.*','from_date'=>'DATE_FORMAT(l.from_date,"'.DATEFORMAT_MYSQL.'")',
								         'to_date'=>'DATE_FORMAT(l.to_date,"'.DATEFORMAT_MYSQL.'")',
										 'applieddate'=>'DATE_FORMAT(l.createddate,"'.DATEFORMAT_MYSQL.'")',
                                         'oncallday'=>'if(l.oncallday = 1,"Full Day","Half Day")', 										 
								       ))
						   ->joinLeft(array('et'=>'main_employeeoncalltypes'), 'et.id=l.oncalltypeid',array('oncalltype'=>'et.oncalltype'))	
						   ->joinLeft(array('u'=>'main_users'), 'u.id=l.user_id',array('userfullname'=>'u.userfullname'))						   						 		   						   
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $employeeoncallData;       		
	}
	
	public function updateemployeeoncalls($appliedoncallscount,$employeeid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->query("update main_employeeoncalls  set used_oncalls = used_oncalls+".$appliedoncallscount." where user_id = ".$employeeid." AND alloted_year = year(now()) AND isactive = 1 ");		
	
	}
	
	public function updatecancelledemployeeoncalls($appliedoncallscount,$employeeid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->query("update main_employeeoncalls  set used_oncalls = used_oncalls-".$appliedoncallscount." where user_id = ".$employeeid." AND alloted_year = year(now()) AND isactive = 1 ");		
	
	}
	
	public function getUserID($id)
    {
    	$result =  $this->select()
    				->setIntegrityCheck(false) 	
    				->from(array('l'=>'main_oncallrequest'),array('l.user_id'))
 	  				->where("l.isactive = 1 AND l.id = ".$id);
	
    	return $this->fetchAll($result)->toArray();
    }
	
	public function getOncallRequestDetails($id)
    {
    	$result =  $this->select()
    				->setIntegrityCheck(false) 	
    				->from(array('l'=>'main_oncallrequest'),array('l.*'))
 	  				->where("l.isactive = 1 AND l.id = ".$id);
	
    	return $this->fetchAll($result)->toArray();
    }
	
	public function checkdateexists($from_date, $to_date,$loginUserId)
	{
	    $db = Zend_Db_Table::getDefaultAdapter();
        
		
		$query = "select count(l.id) as dateexist from main_oncallrequest l where l.user_id=".$loginUserId." and l.oncallstatus IN(1,2) and l.isactive = 1
        and (l.from_date between '".$from_date."' and '".$to_date."' OR l.to_date between '".$from_date."' and '".$to_date."' )";
		
        $result = $db->query($query)->fetchAll();
	    return $result;
	
	}
	
	/* This function is common to manager employee oncalls, employee oncalls , approved,cancel,pending and rejected oncalls
       Here differentiation is done based on objname. 
    */	   
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$objName,$queryflag,$unitId='',$statusidstring='')
	{	
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		}	
        $searchQuery = '';
        $searchArray = array();
        $data = array();
		if($objName == 'manageremployeevacations')
		{
		       if($searchData != '' && $searchData!='undefined')
				{
					$searchValues = json_decode($searchData);
					foreach($searchValues as $key => $val)
					{
						if($key == 'applieddate')
						 $searchQuery .= " l.createddate like '%".  sapp_Global::change_date($val,'database')."%' AND ";	
						else if($key == 'from_date' || $key == 'to_date')
						{
							$searchQuery .= " ".$key." like '%".  sapp_Global::change_date($val,'database')."%' AND ";
						} 
						else 
						  $searchQuery .= " ".$key." like '%".$val."%' AND ";
						$searchArray[$key] = $val;
					}
					$searchQuery = rtrim($searchQuery," AND");					
				}
				
				$tableFields = array('action'=>'Action','userfullname' => 'Employee','oncalltype' => 'On call Type',
                    'from_date' => 'From','to_date' => 'To','appliedoncallscount' => 'Days','oncallstatus' => 'On call Status');
		
		        $oncall_arr = array('' => 'All',1 =>'Full Day',2 => 'Half Day');

                $tablecontent = $this->getEmployeeOncallRequest($sort, $by, $pageNo, $perPage,$searchQuery,$loginUserId);      				
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
					'dashboardcall'=>$dashboardcall,
								'search_filters' => array(
									'from_date' =>array('type'=>'datepicker'),
									'to_date' =>array('type'=>'datepicker'),
									'applieddate'=>array('type'=>'datepicker'),
									'oncallday' => array(
										'type' => 'select',
										'filter_data' => $oncall_arr,
									),
								)
				);
		}
		else if($objName == 'emponcallsummary')
		{
		        $managerstring= "true";
				 
		         if($searchData != '' && $searchData!='undefined')
					{
						$searchValues = json_decode($searchData);
						foreach($searchValues as $key => $val)
						{
						  if($key !='oncallstatus')
						  {
							if($key == 'reportingmanagername')
							 $searchQuery .= " u.userfullname like '%".$val."%' AND ";
							else if($key == 'employeename')
							 $searchQuery .= " mu.userfullname like '%".$val."%' AND "; 
							else if($key == 'applieddate')
							{
							$searchQuery .= " l.createddate  like '%".  sapp_Global::change_date($val,'database')."%' AND ";
							}
							else if($key == 'from_date' || $key == 'to_date')
							{
								$searchQuery .= " ".$key." like '%".  sapp_Global::change_date($val,'database')."%' AND ";
							}
							else 
							 $searchQuery .= " ".$key." like '%".$val."%' AND ";
							
							}
							$searchArray[$key] = $val;
						}
						$searchQuery = rtrim($searchQuery," AND");					
					}
					
				    $statusid = '';				
			        if($queryflag !='')
					{
					   $statusid = $queryflag;
						   if($statusid == 1)
							  $queryflag = 'pending';
						   else if($statusid == 2)
							  $queryflag = 'approved'; 
						   else if($statusid == 3)
							  $queryflag = 'rejected'; 
						   else if($statusid == 4)
							  $queryflag = 'cancel'; 
					}
					else
					{
						$queryflag = 'approved';
					}
					

            //$tableFields = array('action'=>'Action','employeename' => 'Oncall Applied By','oncalltype' => 'Oncall Type','oncallday' => 'Oncall Duration','from_date' => 'From Date','to_date' => 'To Date','reason' => 'Reason','approver_comments' => 'Comments','reportingmanagername'=>'Reporting Manager','appliedoncallscount' => 'Oncall Count','applieddate' => 'Applied On');
            $tableFields = array('action'=>'Action','employeename' => 'Employee',
            'oncalltype' => 'On call Type','from_date' => 'From Date','to_date' => 'To Date',
            'appliedoncallscount' => 'On call Count','applieddate' => 'Applied On');						 
				 
			$oncall_arr = array('' => 'All',1 =>'Full Day',2 => 'Half Day');	 
			
			$search_filters = array(
										'from_date' =>array('type'=>'datepicker'),
										'to_date' =>array('type'=>'datepicker'),
										'applieddate'=>array('type'=>'datepicker'),
										'oncallday' => array(
															'type' => 'select',
															'filter_data' => $oncall_arr,
														),
										);
										
			
            /* This is for dashboard call.
               Here one additional column Status is build by passing it to table fields
            */ 			   
			if($dashboardcall == 'Yes')
            {
					$tableFields['oncallstatus'] = "Status";
					$search_filters['oncallstatus'] = array(
					'type' => 'select',
					'filter_data' => array('pending' => 'Pending for approval','approved'=>'Approved','rejected'=>'Rejected','cancel'=>'Cancelled',),
				);
				if(isset($searchArray['oncallstatus']))
				{
					$queryflag = $searchArray['oncallstatus'];
					 if($queryflag =='')
					 {
						$queryflag = 'pending';
					 }	
				}
				
			}
			
			$tablecontent = $this->getOncallStatusHistory($sort, $by, $pageNo, $perPage,$searchQuery,$queryflag,$loginUserId,$managerstring);    
			
			
			if(isset($queryflag) && $queryflag != '') 
		      $formgrid = 'true';
			else 
		      $formgrid = '';  
			  
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
				'formgrid' => $formgrid,
				'unitId'=>sapp_Global::_encrypt($statusid),
				'call'=>$call,
				'dashboardcall'=>$dashboardcall,
				'search_filters' => $search_filters
			);
		
		}
		else
		{
				if($searchData != '' && $searchData!='undefined')
					{
						$searchValues = json_decode($searchData);
						foreach($searchValues as $key => $val)
						{
							if($key == 'reportingmanagername')
							 $searchQuery .= " u.userfullname like '%".$val."%' AND ";					
							else if($key == 'applieddate')
							{
								
								$searchQuery .= " l.createddate  like '%".  sapp_Global::change_date($val,'database')."%' AND ";
							}
							else if($key == 'from_date' || $key == 'to_date')
							{
								$searchQuery .= " ".$key." like '%".  sapp_Global::change_date($val,'database')."%' AND ";
							}
							else 
							 $searchQuery .= " ".$key." like '%".$val."%' AND ";
							$searchArray[$key] = $val;
						}
						$searchQuery = rtrim($searchQuery," AND");					
					}
				
				/*$tableFields = array('oncalltype' => 'Oncall Type','oncallday' => 'Oncall Duration',
							'from_date' => 'From Date','to_date' => 'To Date','reason' => 'Reason','approver_comments' => 'Comments',
							"reportingmanagername"=>"Reporting Manager",'appliedoncallscount' => 'Oncall Count',
							'applieddate' => 'Applied On','action'=>'Action',);*/
				if($objName=='pendingoncalls' || $objName=='canceloncalls') {	
					$tableFields = array('action'=>'Action','oncalltype' => 'On call Type','reason' => 'Reason',
							'from_date' => 'From Date','to_date' => 'To Date','appliedoncallscount' => 'Days',
							'applieddate' => 'Applied On');
				}else{
					$tableFields = array('action'=>'Action','oncalltype' => 'On call Type','reason' => 'Reason',
							'from_date' => 'From Date','to_date' => 'To Date','appliedoncallscount' => 'Days',
							'applieddate' => 'Applied On','modifieddate' => 'Approved/Rejected On');
				}	
				$oncall_arr = array('' => 'All',1 =>'Full Day',2 => 'Half Day');	
				
				$tablecontent = $this->getOncallStatusHistory($sort, $by, $pageNo, $perPage,$searchQuery,$queryflag,$loginUserId);    
				
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
					'call'=> $call,
					'dashboardcall'=>$dashboardcall,
					'search_filters' => array(
									'from_date' =>array('type'=>'datepicker'),
									'to_date' =>array('type'=>'datepicker'),
									'applieddate'=>array('type'=>'datepicker'),
									'oncallday' => array(
										'type' => 'select',
										'filter_data' => $oncall_arr,
									),
								)
				);
        }
		
		return $dataTmp;
	}
	
	public function getUsersAppliedOncalls($userId)
	{
		$result =  $this->select()
    				->setIntegrityCheck(false) 	
    				->from(array('l'=>'main_oncallrequest'),array('l.from_date','l.to_date'))
 	  				->where("l.isactive = 1 AND l.user_id = ".$userId." AND l.oncallstatus IN(1,2)");
		
    	return $this->fetchAll($result)->toArray();
	}
	
	public function checkOncallExists($applied_from_date,$applied_to_date,$from_date, $to_date,$loginUserId)
	{
	    $db = Zend_Db_Table::getDefaultAdapter();
        
		
		$query = "select count(l.id) as oncallexist from main_oncallrequest l where l.user_id=".$loginUserId." and l.oncallstatus IN(1,2) and l.isactive = 1
        and ('".$from_date."' between '".$applied_from_date."' and '".$applied_to_date."' OR '".$to_date."' between '".$applied_from_date."' and '".$applied_to_date."' )";
		
        $result = $db->query($query)->fetchAll();
	    return $result;
	
	}
	
public function getOncallDetails($id)
	{
		$result =  $this->select()
    				->setIntegrityCheck(false) 	
    				->from(array('l'=>'main_oncallrequest_summary'),array('l.*'))
 	  				->where("l.isactive = 1 AND l.oncall_req_id = ".$id." ");
					
    	return $this->fetchAll($result)->toArray();
	}
	
	public function getOncallsCount($userid,$status='') {
		$db = Zend_Db_Table::getDefaultAdapter();
        $oncallstatus = "";
        if($status != '')
            $oncallstatus = " and l.oncallstatus = $status ";
        
        $query = "select count(*) cnt from main_oncallrequest l 
                  where l.isactive = 1 and l.user_id = $userid ".$oncallstatus;
        $result = $db->query($query)->fetch();
        return $result['cnt'];
		
	}
}