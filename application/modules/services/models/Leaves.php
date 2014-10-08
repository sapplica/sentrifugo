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

/**
 * Login_Model_Users
 *
 * @author Enrico Zimuel (enrico@zimuel.it)
 */
class Services_Model_Leaves extends Zend_Db_Table_Abstract
{
	protected $_name = 'main_leaverequest';
	protected $_primary = 'id';

	public function getLeavedata($page_no,$per_page,$userid,$statusflag)
    {
	   $result = array();
	  if(isset($userid) && $userid != '')
	  {
	    $db = Zend_Db_Table::getDefaultAdapter();
        $query_cnt = "select count(*) as count from main_leaverequest l
					  left join main_employeeleavetypes e on e.id=l.leavetypeid 
					  where (l.user_id=".$userid." and l.leavestatus=".$statusflag." and l.isactive=1)";
        $result_cnt = $db->query($query_cnt)->fetch();
        $total_cnt = $result_cnt['count'];
		
		$offset = ($per_page*$page_no) - $per_page;
        $limit_str = " limit ".$per_page." offset ".$offset;
        $page_cnt = ceil($total_cnt/$per_page);
		
		$query = "select l.id,l.user_id,l.appliedleavescount,l.from_date,l.to_date,e.leavetype,l.leavestatus from main_leaverequest l
				  left join main_employeeleavetypes e on e.id=l.leavetypeid where (l.user_id=".$userid." and l.leavestatus=".$statusflag." and l.isactive=1) ".$limit_str;
        $result = $db->query($query);
        $total_data = $result->fetchAll();
		
		$role_query = "select u.emprole,r.rolename,r.group_id from main_users u
				      inner join main_roles r on u.emprole = r.id and r.isactive=1 where (u.id=".$userid." and u.isactive=1)";
        $role_result = $db->query($role_query);
        $role_data = $role_result->fetch();
		//echo"<pre>";print_r($role_data);exit;
		
		$data = array('rows' => $total_data,'page_cnt' => $page_cnt,'role' => $role_data);

	     
	  }else
	  {
	     $data = array('status'=>'0','message'=>'User Id cannot be empty.','result' => $result);
	  }
	  //echo "<pre>";print_r($data);exit;
	  return $data;
    }
	
	public function getEmployeeLeavedata($page_no,$per_page,$userid,$statusflag)
    {
	   $result = array();
	  if(isset($userid) && $userid != '')
	  {
	    $db = Zend_Db_Table::getDefaultAdapter();
        $query_cnt = "select count(*) as count from main_leaverequest l
					  left join main_employeeleavetypes e on e.id=l.leavetypeid
					  LEFT JOIN `main_users` AS `u` ON u.id=l.user_id	
					  where (l.rep_mang_id=".$userid." and l.leavestatus=".$statusflag." and l.isactive=1 and u.isactive=1)";
        $result_cnt = $db->query($query_cnt)->fetch();
        $total_cnt = $result_cnt['count'];
		
		$offset = ($per_page*$page_no) - $per_page;
        $limit_str = " limit ".$per_page." offset ".$offset;
        $page_cnt = ceil($total_cnt/$per_page);
		
		$query = "select l.id,l.user_id,u.userfullname, e.leavetype, if(l.leaveday = 1,'Full Day','Half Day') AS leaveday,
				  DATE_FORMAT(l.from_date,'".DATEFORMAT_MYSQL."') as from_date,DATE_FORMAT(l.to_date,'".DATEFORMAT_MYSQL."') as to_date,
				  l.leavestatus,l.reason,DATE_FORMAT(l.createddate,'".DATEFORMAT_MYSQL."') as applieddate,
				  l.appliedleavescount
				  from main_leaverequest l
				  left join main_employeeleavetypes e on e.id=l.leavetypeid 
				  LEFT JOIN `main_users` AS `u` ON u.id=l.user_id 
				  where (l.rep_mang_id=".$userid." and l.leavestatus=".$statusflag." and l.isactive=1 and u.isactive=1) ".$limit_str;
        $result = $db->query($query);
        $total_data = $result->fetchAll();
		
		$data = array('rows' => $total_data,'page_cnt' => $page_cnt);

	     
	  }else
	  {
	     $data = array('status'=>'0','message'=>'User Id cannot be empty.','result' => $result);
	  }
	  //echo "<pre>";print_r($data);exit;
	  return $data;
    }
	
	public function getIndividulLeavedata($userid,$recordid)
	{
		$result = array();
	    if($userid != '' && $recordid !='')
	    {
	    $db = Zend_Db_Table::getDefaultAdapter();
        		
		$query = "select l.id,l.user_id,u.userfullname, e.leavetype, if(l.leaveday = 1,'Full Day','Half Day') AS leaveday,
				  DATE_FORMAT(l.from_date,'".DATEFORMAT_MYSQL."') as from_date,DATE_FORMAT(l.to_date,'".DATEFORMAT_MYSQL."') as to_date,
				  l.leavestatus,l.reason,DATE_FORMAT(l.createddate,'".DATEFORMAT_MYSQL."') as applieddate,
				  l.appliedleavescount
				  from main_leaverequest l
				  left join main_employeeleavetypes e on e.id=l.leavetypeid 
				  LEFT JOIN `main_users` AS `u` ON u.id=l.user_id 
				  where (l.id=".$recordid." and l.user_id =".$userid." and l.isactive=1 and u.isactive=1) ";
        $result = $db->query($query);
        $total_data = $result->fetchAll();
		
		$data = array('rows' => $total_data);

	     
	  }else
	  {
	     $data = array('status'=>'0','message'=>'User Id cannot be empty.','result' => $result);
	  }
	  //echo "<pre>";print_r($data);exit;
	  return $data;
	}
	
	public function getIndividulEmpLeavedata($userid,$recordid,$employeeid)
	{
		$result = array();
	    if($userid != '' && $recordid !='' && $employeeid !='')
	    {
	    $db = Zend_Db_Table::getDefaultAdapter();
        		
		$query = "select l.id,l.user_id,u.userfullname, e.leavetype, if(l.leaveday = 1,'Full Day','Half Day') AS leaveday,
				  DATE_FORMAT(l.from_date,'".DATEFORMAT_MYSQL."') as from_date,DATE_FORMAT(l.to_date,'".DATEFORMAT_MYSQL."') as to_date,
				  l.leavestatus,l.reason,DATE_FORMAT(l.createddate,'".DATEFORMAT_MYSQL."') as applieddate,
				  l.appliedleavescount
				  from main_leaverequest l
				  left join main_employeeleavetypes e on e.id=l.leavetypeid 
				  LEFT JOIN `main_users` AS `u` ON u.id=l.user_id 
				  where (l.rep_mang_id=".$userid." and l.id=".$recordid." and l.user_id =".$employeeid." and l.isactive=1 and u.isactive=1) ";
        $result = $db->query($query);
        $total_data = $result->fetchAll();
		
		$data = array('rows' => $total_data);

	     
	  }else
	  {
	     $data = array('status'=>'0','message'=>'User Id cannot be empty.','result' => $result);
	  }
	  //echo "<pre>";print_r($data);exit;
	  return $data;
	}
	
	public function approveEmpLeavedata($userid,$recordid,$employeeid,$actionstatus)
	{
		$result = array();
		$userfullname = '';
		$useremail = '';
		$businessunitid = '';
		$from_date = '';
		$to_date = '';
		$reason = '';
		$appliedleavescount = '';
		$text = '';
	    if($userid != '' && $recordid !='' && $employeeid !='')
	    {
	    $db = Zend_Db_Table::getDefaultAdapter();
        $user_query = "SELECT u.userfullname,u.emailaddress,e.businessunit_id FROM main_users AS u 
		               left join main_employees e on e.user_id=u.id
		               WHERE (u.isactive = 1 AND u.id=".$employeeid.")";
        $user_result = $db->query($user_query)->fetch();
		if(!empty($user_result))
		{
		   $userfullname = $user_result['userfullname'];
		   $useremail = $user_result['emailaddress'];
		   $businessunitid = $user_result['businessunit_id'];
		}
		
		
		$leave_query = "SELECT l.reason,l.appliedleavescount,DATE_FORMAT(l.from_date,'".DATEFORMAT_MYSQL."') as from_date,
		                DATE_FORMAT(l.to_date,'".DATEFORMAT_MYSQL."') as to_date FROM main_leaverequest AS l 
						where (l.rep_mang_id=".$userid." and l.id=".$recordid." and l.user_id =".$employeeid." and l.isactive=1)";
        $leave_result = $db->query($leave_query)->fetch();
		if(!empty($leave_result))
		{
		   $from_date = $leave_result['from_date'];
		   $to_date = $leave_result['to_date'];
		   if($to_date == '' || $to_date == NULL)
		   $to_date = $from_date;
		   $reason = $leave_result['reason'];
		   $appliedleavescount = $leave_result['appliedleavescount'];
		}
		$date = gmdate("Y-m-d H:i:s");
	
		//echo "<pre>";print_r($leave_result);exit;
		if($actionstatus == 2)
		{
		    $text = 'approved';
		    $db->query("update main_employeeleaves  set used_leaves = used_leaves+".$appliedleavescount." where user_id = ".$employeeid." AND alloted_year = year(now()) AND isactive = 1 ");
			$db->query("update main_leaverequest  set leavestatus = ".$actionstatus.",modifiedby = ".$userid.",
			            modifieddate = '".$date."' where (rep_mang_id=".$userid." and id=".$recordid." and user_id =".$employeeid." and isactive=1 )");
		}else if($actionstatus == 3)
		{
		
		   $text = 'rejected';
		   $db->query("update main_leaverequest  set leavestatus = ".$actionstatus.",modifiedby = ".$userid.",
            			modifieddate = '".$date."' where (rep_mang_id=".$userid." and id=".$recordid." and user_id =".$employeeid." and isactive=1)");
		}
		
		if($userfullname != ''&& $useremail != '' && $from_date != '' && $reason != '' && $appliedleavescount !='')
		{
	
				/* Mail to Employee */
				$options['subject'] = 'Leave Request';
				$options['header'] = 'Leave Request';
				$options['fromEmail'] = DONOTREPLYEMAIL;
				$options['fromName'] = DONOTREPLYNAME;
				$options['toEmail'] = $useremail;
				//$options['cc'] = $hremail;								
				$options['toName'] = $userfullname;
				if($text == 'approved')
				$options['message'] = '<div>The below leave(s) has been approved.</div>';	
				else 
				$options['message'] = '<div>The below leave(s) has been rejected.</div>';	
				$options['message'] .= '<div>												
								<div>Name : '.$userfullname.'</div>
								<div> No. of leaves applied : '.$appliedleavescount.'</div>
								<div>From : '.$from_date.'</div>
								<div>To : '.$to_date.'.</div>
								<div> Reason : '.$reason.'</div>
								</div>';	
				$result = sapp_Global::_sendEmail($options);
				/* End */
				
				/* Mail to HR */
				if (defined('LV_HR_'.$businessunitid) && $businessunitid !='')
				{
				
				$options['subject'] = 'Leave Request';
				$options['header'] = 'Leave Request';
				$options['fromEmail'] = DONOTREPLYEMAIL;
				$options['fromName'] = DONOTREPLYNAME;
				$options['toEmail'] = constant('LV_HR_'.$businessunitid);
				//$options['cc'] = $hremail;								
				$options['toName'] = 'Leave management';
				if($text == 'approved')
				$options['message'] = '<div>The below leave(s) has been approved.</div>';	
				else 
				$options['message'] = '<div>The below leave(s) has been rejected.</div>';	
				$options['message'] .= '<div>												
								<div>Name : '.$userfullname.'</div>
								<div> No. of leaves applied : '.$appliedleavescount.'</div>
								<div>From : '.$from_date.'</div>
								<div>To : '.$to_date.'.</div>
								<div> Reason : '.$reason.'</div>
								</div>';	
				$result = sapp_Global::_sendEmail($options);
				}
				/*END */
		}
		
		$data = array('status'=>'1','message'=>'Leave request '.$text.' successfully.','result' => 'success');

	     
	  }else
	  {
	     $data = array('status'=>'0','message'=>'User Id cannot be empty.','result' => $result);
	  }
	  //echo "<pre>";print_r($data);exit;
	  return $data;
	}
	
	
	public function getempdetails($userid)
    {
	   $result = array();
	   $repmanager_result = array();
	 
	   
	  if(isset($userid) && $userid != '')
	  {
	    $db = Zend_Db_Table::getDefaultAdapter();
		
        $emp_query = "SELECT e.date_of_joining,e.reporting_manager,e.businessunit_id,e.department_id FROM main_employees AS e WHERE (e.isactive = 1 AND e.user_id=".$userid.")";
        $emp_result = $db->query($emp_query)->fetch();
		
		$available_leaves_query = "SELECT `el`.`emp_leave_limit` AS `leavelimit`, el.emp_leave_limit - el.used_leaves AS `remainingleaves` FROM `main_employeeleaves` AS `el` WHERE (el.user_id=".$userid." AND el.alloted_year = now() AND el.isactive = 1)";
        $available_leaves_result = $db->query($available_leaves_query)->fetch();
		
		$leavetype_query = "SELECT `l`.`id`, `l`.`leavetype`, `l`.`numberofdays` from `main_employeeleavetypes` AS `l` WHERE (l.isactive = 1)";
        $leavetype_result = $db->query($leavetype_query)->fetch();
		
		
		if($emp_result['reporting_manager'] !='')
		{
		   $repmanager_query = "SELECT u.id,u.userfullname,u.emailaddress FROM main_users AS u WHERE (u.isactive = 1 AND u.id=".$emp_result['reporting_manager'].")";
           $repmanager_result = $db->query($repmanager_query)->fetch();
		}
		
		if($emp_result['department_id'] !='')
		{
		   $weekdetails_query = "SELECT  `l`.`is_halfday` FROM `main_leavemanagement` AS `l` 
								WHERE (l.department_id = ".$emp_result['department_id']." AND l.isactive = 1) ";
           $weekdetails_result = $db->query($weekdetails_query)->fetch();
		}
		
				
		$data = array('empdetails' => $emp_result,'repmanagerdetails' => $repmanager_result,'availableleaves' =>$available_leaves_result,'leavetypes' => $leavetype_result,'ishalfday' =>$weekdetails_result);

	     
	  }else
	  {
	     $data = array('status'=>'0','message'=>'User Id cannot be empty.','result' => $result);
	  }
	  //echo "<pre>";print_r($data);exit;
	  return $data;
    }
	
	
	public function saveleaverequest($userid,$reason,$leavetypeid='',$leaveday='',$fromdate='',$todate='',$appliedleavesdaycount)
    {
	   
	   $result = array();
	   $messagearray = array();
	   $leavedayArr = array(1,2);
	   $errorflag = "true";
	   $leavetypecount = '';
	   $leavetypetext = '';
	   $days = '';
	   $appliedleavescount = '';
	 
	   
	  if(isset($userid) && $userid != '' && isset($reason) && $reason != '' && isset($leavetypeid) && $leavetypeid != '' && isset($leaveday) && $leaveday != '')
	  {
	    $db = Zend_Db_Table::getDefaultAdapter();
        $user_query = "SELECT u.userfullname,u.emailaddress FROM main_users AS u WHERE (u.isactive = 1 AND u.id=".$userid.")";
        $user_result = $db->query($user_query)->fetch();
		if(!empty($user_result))
		{
		   $userfullname = $user_result['userfullname'];
		   $useremail = $user_result['emailaddress'];
		}else
		{
			$errorflag = 'false';
		}
		
	    $leavedetails = $this->getleavedetails($userid);
		//echo "<pre>";print_r($leavedetails);exit;
		if(!empty($leavedetails['repmanagerdetails']) && !empty($leavedetails['availableleaves']) && !empty($leavedetails['leavetypes']) && !empty($leavedetails['weekdetails']))
		{
		        $reportingmanagerid = $leavedetails['repmanagerdetails']['id'];
		        $reportingmanageremail = $leavedetails['repmanagerdetails']['emailaddress'];
				$reportingmanagername = $leavedetails['repmanagerdetails']['userfullname'];
				$availableleaves = $leavedetails['availableleaves']['remainingleaves'];
				$businessunitid = $leavedetails['empdetails']['businessunit_id'];
				$dateofjoining = $leavedetails['empdetails']['date_of_joining'];
		        /*
				   START- Leave Type Validation
				   Server side validation for leavetype count based on user selection.
				   This is to validate or negate if user manipulates the data in the browser or firebug.
				*/
				  if($leavetypeid !='')
				  {
					   $leavetypeArr = $this->getleavetypedata($leavetypeid);
					   //echo "<pre>";print_r($leavetypeArr);exit;
					   if(!empty($leavetypeArr))
					   {
							 $leavetypecount = $leavetypeArr['numberofdays'];
							 $leavetypetext = $leavetypeArr['leavetype'];
					   }
					   else
					   {
						 $messagearray['leave_type_id'] = 'Wrong inputs given.';
						 $errorflag = 'false';
					   }
				
				  }else
				  {
					  $messagearray['leavetypeid'] = 'Leave types are not configured yet.';
					  $errorflag = 'false';
				  }

				  
				/*
				   END- Leave Type Validation
				*/  
				  
				/*
				   START- Leave Day Validation
				   Server side validation for halfday and full day based on user selection.
				   This is to validate or negate if user manipulates the data in the browser or firebug.
				*/  
			   
				if($leaveday !='')
				{
				   if (!in_array($leaveday, $leavedayArr))
					{
					   $messagearray['leave_day'] = 'Wrong inputs given.';
					   $errorflag = 'false';
					}
				}else
				{
					$messagearray['leave_day'] = 'Please select leave day.';
					$errorflag = 'false';
				}
				
				/*
				   END- Leave Day Validation
				*/
				
				
				 /* 
				   START- Day calculation and validations.
				   I. Calculation of days based on start date and end data.
				   II. Also checking whether Applied no of days is less than leavetype configuration. 
				   III. Also If leaveday is selected as full day then making todata as manadatory and 
						if leave day is selected as half day then no mandatory validation for todate.
				*/
				
				$from_date = sapp_Global::change_date($fromdate,'database');
				$to_date = sapp_Global::change_date($todate,'database');
				//echo "<pre>";print_r($leavedetails);exit;	
				if($from_date != '' && $to_date !='' && $leavetypecount !='')
				{		 
					//$days = $this->calcBusinessDays($fromdate_obj,$todate_obj,$constantday); 
					$days = $this->calculatebusinessdays($from_date,$to_date,$userid);
					if(is_numeric($days) && $leavetypecount >= $days)
					{
							//$errorflag = 'true';
					}
					else
					{
					  if(!is_numeric($days))
						{
							//$messagearray['to_date'] = 'From date should be less than Todate.';
							$messagearray['to_date'] = 'To date should be greater than from date.';
							$errorflag = 'false';
						}
					  else
						{
						   $messagearray['to_date'] = $leavetypetext." permits maximum of ".$leavetypecount." leaves.";
						   $errorflag = 'false';
						}  				
					}
							   
				}else
				{
				    if($leaveday == 1)
				    {
					   if($from_date == '')
					   {
						 $messagearray['from_date'] = "Please select date.";
						 $errorflag = 'false'; 
					   }
					   if($to_date == '')
					   {
						 $messagearray['to_date'] = "Please select date.";
						 $errorflag = 'false'; 
					   } 
                    }else if($leaveday == 2)
					{
					   if($from_date == '')
					   {
						 $messagearray['from_date'] = "Please select date.";
						 $errorflag = 'false'; 
					   }
					}else
					{
						if($from_date == '')
					   {
						 $messagearray['from_date'] = "Please select date.";
						 $errorflag = 'false'; 
					   }
					   if($to_date == '')
					   {
						 $messagearray['to_date'] = "Please select date.";
						 $errorflag = 'false'; 
					   } 
					}	
				}
				/*
					END- Day calculation and validations.
				*/ 
				
				
				/*  
				START- Validating Half day requests based on Leave management options
				Validation for half day leaves. 
				If halfday leave is configure in leave management options then only half day leave can be applied. 
				*/	
				$ishalf_day = $leavedetails['weekdetails']['is_halfday'];	
				if($ishalf_day == 2)
				{
				   if($leaveday == 2)
				   {
					$errorflag = 'false';
					$messagearray['leave_day'] = 'Half day leave cannot be applied.';
				   }	
				  
				}
				
				/*  
					END- Validating Half day requests based on Leave management options
				*/
				
				
				/* 
				   START- Validating if leave request has been previoulsy applied
				   I.Validating from and to dates to check whether previously 
				   any leave has been raised with the same dates.
				   II.If full day leave is applied then fromdate and todate are passed as parameter to query.
				   III.If half day leave is applied then fromdate and fromdate are passed as a parameter to query.
				*/
				if($leaveday == 1)
				{
					$dateexists = $this->checkdateexists($from_date, $to_date,$userid);
					if(!empty($dateexists))
					{
						if($dateexists[0]['dateexist'] > 0)
						{
						   $errorflag = 'false';
						   $messagearray['to_date'] = ' Leave has already been applied for the above dates.';
						}
					}	
				}else if($leaveday == 2)
				{
					$dateexists = $this->checkdateexists($from_date, $from_date,$userid);
					if(!empty($dateexists))
					{
						if($dateexists[0]['dateexist'] > 0)
						{
						   $errorflag = 'false';
						   $messagearray['from_date'] = ' Leave has already been applied for the above date.';
						}
					}
				}
			
				/*
				  END- Validating if leave request has been previoulsy applied
				*/
				
				/* START Validating whether applied date is prior to date of joining */
					if($dateofjoining >= $from_date)
					{
						$errorflag = 'false';
						$messagearray['from_date'] = ' Leave cannot be applied before date of joining.';
					}
					/* End */
				
				if($leaveday == 2)
				 $appliedleavescount =  0.5;
				else if($leaveday == 1)
				 $appliedleavescount = ($days !=''?$days:$appliedleavesdaycount);
				 
				if($errorflag == 'true') 
				{
				    $data_array = array('user_id'=>$userid, 
				                 'reason'=>$reason,
				                 'leavetypeid'=>$leavetypeid,
				                 'leaveday'=>$leaveday,
								 'from_date'=>$from_date,
								 'to_date'=>$to_date,
								 'leavestatus'=>1,
								 'rep_mang_id'=>$reportingmanagerid,
				      			 'no_of_days'=>$availableleaves,
								 'appliedleavescount'=>$appliedleavescount,
								 'createdby' => $userid,
								 'createddate' => gmdate("Y-m-d H:i:s"),
								 'modifiedby'=>$userid,
								 'modifieddate'=>gmdate("Y-m-d H:i:s"),
								 'isactive'=> 1
						);
				    //$final_array = array($userid,$userfullname,$useremail,$reportingmanagerid,$reportingmanageremail,$reportingmanagername,$leavetypeid,$leaveday,$from_date,$to_date,$availableleaves,$appliedleavescount,$businessunitid);
					$Id = $this->saveleaverequestdetails($data_array); 
					/* Start Mailing Code */
							/* Mail to Reporting manager */
							$toemailArr = array($reportingmanageremail);
							if(!empty($toemailArr))
							{
								$options['subject'] = 'Leave request for approval';
								$options['header'] = 'Leave Request';
								$options['fromEmail'] = DONOTREPLYEMAIL;
								$options['fromName'] = DONOTREPLYNAME;
								$options['toEmail'] = $toemailArr;
                                //$options['cc'] = $hremail;								
								$options['toName'] = $reportingmanagername;
								$options['message'] = '<div>
												<div>Leave request has been raised for your approval.</div>
												<div>Name : '.$userfullname.'</div>
												<div> No. of leaves applied : '.$appliedleavescount.'</div>
												<div>From : '.$from_date.'</div>
												<div>To : '.$to_date.'.</div>
												<div> Reason : '.$reason.'</div>
												</div>';
								//$options['cron'] = 'yes';
                                $result = sapp_Global::_sendEmail($options);	
							}		
							/* END */
							
							/* Mail to the applied employee*/
								$empemailArr = array($useremail);
								$options['subject'] = 'Leave request';
								$options['header'] = 'Your leave details';
								$options['fromEmail'] = DONOTREPLYEMAIL;
								$options['fromName'] = DONOTREPLYNAME;
								$options['toEmail'] = $toemailArr;
                                //$options['cc'] = $hremail;								
								$options['toName'] = $userfullname;
								$options['message'] = '<div>
												<div>Following are your leave details. A mail has been sent to your project manager for approval.</div>
												<div>Name : '.$userfullname.'</div>
												<div> No. of leaves applied : '.$appliedleavescount.'</div>
												<div>From : '.$from_date.'</div>
												<div>To : '.$to_date.'.</div>
												<div> Reason : '.$reason.'</div>
												</div>';
								//$options['cron'] = 'yes';
                                $result = sapp_Global::_sendEmail($options);
							/* End */

							/* Mail to HR */
                            if (defined('LV_HR_'.$businessunitid) && $businessunitid !='')
						    {
							    $options['subject'] = 'Leave request for approval';
								$options['header'] = 'Leave Request ';
								$options['fromEmail'] = DONOTREPLYEMAIL;
								$options['fromName'] = DONOTREPLYNAME;
								$options['toEmail'] = constant('LV_HR_'.$businessunitid);
                                //$options['cc'] = $hremail;								
								$options['toName'] = 'Leave Management';
								$options['message'] = '<div>
												<div>Leave request has been raised.</div>
												<div>Name : '.$userfullname.'</div>
												<div>No. of leaves applied : '.$appliedleavescount.'</div>
												<div>From : '.$from_date.'</div>
												<div>To : '.$to_date.'.</div>
												<div>Reason : '.$reason.'</div>
												<div>Reporting manager : '.$reportingmanagerName.'</div>
												</div>';	
								$options['cron'] = 'yes';
                                $result = sapp_Global::_sendEmail($options);
							
							}
						 		
							/* END */	
					/* End Mailing Code */
					$data = array('status'=>'1','message'=>'Leave request saved successfully.','result' => 'success');
				}else
				{
					$data = array('status'=>'0','message'=>$messagearray,'result' => $result);
				}	
				
		  
		}else
		{
			if(empty($leavedetails['repmanagerdetails']))
				$messagearray['rep_mang_id'] = 'Reporting manager is not assigned yet. Please contact your HR.';
		    if(empty($leavedetails['availableleaves']))
				$messagearray['rep_mang_id'] = 'You have not been allotted leaves for this financial year. Please contact your HR.';
			 if(empty($leavedetails['leavetypes']))
				$messagearray['leave_type_id'] = 'Leave types are not configured yet.';
			 if(empty($leavedetails['weekdetails']))
			 {
				$messagearray['from_date'] = 'Leave management options are not configured yet.';	
				$messagearray['to_date'] = 'Leave management options are not configured yet.';
			 }
            $data = array('status'=>'0','message'=>$messagearray,'result' => $result);			 
		}
		

	     
	  }else
	  {  
		if($reason == '')
	       $messagearray['to_date'] = 'Please enter reason.';
		if($userid == '')  
           $messagearray['userid'] = 'User Id cannot be empty.';
		if($leavetypeid == '')  
           $messagearray['userid'] = 'Leave type Id cannot be empty.';
		if($leaveday == '')  
           $messagearray['userid'] = 'Leave day cannot be empty.';   
		   
	     $data = array('status'=>'0','message'=>$messagearray,'result' => $result);
	  }
	  //echo "<pre>";print_r($data);exit;
	  return $data;
    }
	
	public function saveleaverequestdetails($data)
	{

			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_leaverequest');
			return $id;
	}		
	
	public function getleavedetails($userid)
    {
	   $result = array();
	   $repmanager_result = array();
	 
	   
	  if(isset($userid) && $userid != '')
	  {
	    $db = Zend_Db_Table::getDefaultAdapter();
		
        $emp_query = "SELECT e.date_of_joining,e.reporting_manager,e.businessunit_id,e.department_id FROM main_employees AS e WHERE (e.isactive = 1 AND e.user_id=".$userid.")";
        $emp_result = $db->query($emp_query)->fetch();
		//echo "<pre>";print_r($emp_result);exit;
		$available_leaves_query = "SELECT `el`.`emp_leave_limit` AS `leavelimit`, el.emp_leave_limit - el.used_leaves AS `remainingleaves` FROM `main_employeeleaves` AS `el` WHERE (el.user_id=".$userid." AND el.alloted_year = now() AND el.isactive = 1)";
        $available_leaves_result = $db->query($available_leaves_query)->fetch();
		
		$leavetype_query = "SELECT `l`.`id`, `l`.`leavetype`, `l`.`numberofdays` from `main_employeeleavetypes` AS `l` WHERE (l.isactive = 1)";
		$leavetype_result = $db->query($leavetype_query)->fetchAll();
		//echo "<pre>";print_r($leavetype_result);exit;
		
		if($emp_result['reporting_manager'] !='')
		{
		   $repmanager_query = "SELECT u.id,u.userfullname,u.emailaddress FROM main_users AS u WHERE (u.isactive = 1 AND u.id=".$emp_result['reporting_manager'].")";
           $repmanager_result = $db->query($repmanager_query)->fetch();
		}
		
		if($emp_result['department_id'] !='')
		{
		   $weekdetails_query = "SELECT `l`.`weekend_startday` AS `weekendstartday`, `l`.`weekend_endday` AS `weekendday`, 
								`l`.`is_halfday`, `l`.`is_leavetransfer`, `l`.`is_skipholidays` FROM `main_leavemanagement` AS `l` 
								WHERE (l.department_id = ".$emp_result['department_id']." AND l.isactive = 1) ";
           $weekdetails_result = $db->query($weekdetails_query)->fetch();
		}
		
				
		$data = array('empdetails' => $emp_result,'repmanagerdetails' => $repmanager_result,'availableleaves' =>$available_leaves_result,'leavetypes' => $leavetype_result,'weekdetails' => $weekdetails_result);

	     
	  }else
	  {
	     $data = array('status'=>'0','message'=>'User Id cannot be empty.','result' => $result);
	  }
	  //echo "<pre>";print_r($data);exit;
	  return $data;
    }
	
	public function cancelleave($userid,$recordid)
	{
	    $businessunitid = '';
		$from_date = '';
		$to_date = '';
		$reason = '';
		$appliedleavescount = '';
		$userfullname = '';
		$useremail = '';
		$reportingmanagername = '';
		$reportingmanageremail = '';
		
	    $db = Zend_Db_Table::getDefaultAdapter();
		$date = gmdate("Y-m-d H:i:s");
		/* Fetching the record to be updated with cancel satus for email purpose and updating the status */
		if($userid !='' && $recordid !='')
		{
			$leave_query = "SELECT l.reason,l.appliedleavescount,DATE_FORMAT(l.from_date,'".DATEFORMAT_MYSQL."') as from_date,
							DATE_FORMAT(l.to_date,'".DATEFORMAT_MYSQL."') as to_date FROM main_leaverequest AS l 
							where (l.id=".$recordid." and l.user_id =".$userid." and l.isactive=1)";
			$leave_result = $db->query($leave_query)->fetch();
			//echo "<pre>";print_r($leave_result);exit;
			if(!empty($leave_result))
			{
			   $from_date = $leave_result['from_date'];
			   $to_date = $leave_result['to_date'];
			   if($to_date == '' || $to_date == NULL)
				$to_date = $from_date;
			   $reason = $leave_result['reason'];
			   $appliedleavescount = $leave_result['appliedleavescount'];
			}
					
			$db->query("update main_leaverequest  set leavestatus = 4,modifiedby = ".$userid.",
							modifieddate = '".$date."' where (id=".$recordid." and user_id =".$userid." and isactive=1 )");
							
			/* END */
			
			$user_query = "SELECT u.userfullname,u.emailaddress,e.businessunit_id,e.reporting_manager FROM main_users AS u 
						   left join main_employees e on e.user_id=u.id
						   WHERE (u.isactive = 1 AND u.id=".$userid.")";
			$user_result = $db->query($user_query)->fetch();
			if(!empty($user_result))
			{
				 $userfullname = $user_result['userfullname'];
				 $useremail = $user_result['emailaddress'];
				 $businessunitid = $user_result['businessunit_id'];
				 $reportingmanagerid = $user_result['reporting_manager'];
			}
			
			if($reportingmanagerid  !='')
			{
				$rep_mangr_query = "SELECT u.userfullname,u.emailaddress FROM main_users AS u 
									WHERE (u.isactive = 1 AND u.id=".$reportingmanagerid.")";
				$repmanager_result = $db->query($rep_mangr_query)->fetch();
				if(!empty($repmanager_result))
				{
					$reportingmanagername = $repmanager_result['userfullname'];
					$reportingmanageremail = $repmanager_result['emailaddress'];	
				}
			}

			if($userfullname != ''&& $useremail != '' && $from_date != '' && $reason != '' && $appliedleavescount !='')
			{
		
					/* Mail to Employee */
					$options['subject'] = 'Leave Request';
					$options['header'] = 'Leave Request';
					$options['fromEmail'] = DONOTREPLYEMAIL;
					$options['fromName'] = DONOTREPLYNAME;
					$options['toEmail'] = $useremail;
					//$options['cc'] = $hremail;								
					$options['toName'] = $userfullname;
					$options['message'] = '<div>												
											<div>Name : '.$userfullname.'</div>
											<div> No. of leaves applied : '.$appliedleavescount.'</div>
											<div>From : '.$from_date.'</div>
											<div>To : '.$to_date.'.</div>
											<div> Reason : '.$reason.'</div>
											</div>';	
					$result = sapp_Global::_sendEmail($options);
					/* End */
					
					/* Mail to Reporting Manager */
					$options['subject'] = 'Leave Request';
					$options['header'] = 'Leave Request';
					$options['fromEmail'] = DONOTREPLYEMAIL;
					$options['fromName'] = DONOTREPLYNAME;
					$options['toEmail'] = $reportingmanageremail;
					//$options['cc'] = $hremail;								
					$options['toName'] = $reportingmanagername;
					$options['message'] = '<div>												
											<div>Name : '.$userfullname.'</div>
											<div> No. of leaves applied : '.$appliedleavescount.'</div>
											<div>From : '.$from_date.'</div>
											<div>To : '.$to_date.'.</div>
											<div> Reason : '.$reason.'</div>
											</div>';	
					$result = sapp_Global::_sendEmail($options);
					/* End */
					
					/* Mail to HR */
					if (defined('LV_HR_'.$businessunitid) && $businessunitid !='')
					{
					
					$options['subject'] = 'Leave Request';
					$options['header'] = 'Leave Request';
					$options['fromEmail'] = DONOTREPLYEMAIL;
					$options['fromName'] = DONOTREPLYNAME;
					$options['toEmail'] = constant('LV_HR_'.$businessunitid);
					//$options['cc'] = $hremail;								
					$options['toName'] = 'Leave management';
					$options['message'] = '<div>												
									<div>Name : '.$userfullname.'</div>
									<div> No. of leaves applied : '.$appliedleavescount.'</div>
									<div>From : '.$from_date.'</div>
									<div>To : '.$to_date.'.</div>
									<div> Reason : '.$reason.'</div>
									</div>';
					//$options['cron'] = 'yes';
					$result = sapp_Global::_sendEmail($options);
					}
					/*END */
			}
			
			$data = array('status'=>'1','message'=>'Leave request cancelled successfully.','result' => 'success');
		}
		else
		  {
			 $data = array('status'=>'0','message'=>'User Id cannot be empty.','result' => $result);
		  }
		return $data;
	}
	
	public function getleavetypedata($leavetypeid)
	{
	    $data = array();
	    if($leavetypeid)
		{
			$db = Zend_Db_Table::getDefaultAdapter();
			
			$leavetype_query = "SELECT e.leavetype,e.numberofdays FROM main_employeeleavetypes AS e WHERE (e.isactive = 1 AND e.id=".$leavetypeid.")";
			$data = $db->query($leavetype_query)->fetch();
		}	
		return $data;
	}
	
	public function calculatebusinessdays($fromDate,$toDate,$userid)
	{
	    $db = Zend_Db_Table::getDefaultAdapter();
	    $noOfDays =0;
		$weekDay='';
		$employeeDepartmentId = '';
		$employeeGroupId = '';
		$weekend1 = '';
		$weekend2 = '';
		$holidayDatesArr = array();
		//$fromDate = $this->_request->getParam('fromDate');
		//$toDate = $this->_request->getParam('toDate');
			//Calculating the no of days in b/w from date & to date with out taking weekend & holidays....
			//$employeesmodel = new Default_Model_Employees();
			//$leavemanagementmodel = new Default_Model_Leavemanagement();
			//$holidaydatesmodel = new Default_Model_Holidaydates();	
			
			$emp_query = "SELECT e.holiday_group,e.department_id FROM main_employees AS e WHERE (e.isactive = 1 AND e.user_id=".$userid.")";
			$emp_result = $db->query($emp_query)->fetch();
			//$loggedInEmployeeDetails = $employeesmodel->getLoggedInEmployeeDetails($loginUserId);
			
			if(!empty($emp_result))
			{
				$employeeDepartmentId = $emp_result['department_id'];
				$employeeGroupId = $emp_result['holiday_group'];
				if($employeeDepartmentId !='' && $employeeDepartmentId !=NULL)
				{
					$weekend_query = "SELECT `l`.`weekend_startday` AS `weekendstartday`, `l`.`weekend_endday` AS `weekendday`, `l`.`is_halfday`, `l`.`is_leavetransfer`, `l`.`is_skipholidays`, `w`.`week_name` AS `daystartname`, `wk`.`week_name` AS `dayendname` FROM `main_leavemanagement` AS `l`
									 LEFT JOIN `tbl_weeks` AS `w` ON w.week_id=l.weekend_startday
									 LEFT JOIN `tbl_weeks` AS `wk` ON wk.week_id=l.weekend_endday WHERE (l.department_id = ".$employeeDepartmentId." AND l.isactive = 1)";
					$weekend_result = $db->query($weekend_query)->fetch();	
				}	
				//$weekendDetailsArr = $leavemanagementmodel->getWeekendNamesDetails($employeeDepartmentId);
				if(!empty($weekend_result))
				{
					if($weekend_result['is_skipholidays'] == 1 && isset($employeeGroupId) && $employeeGroupId !='')
					{
					  //$holidayDateslistArr = $holidaydatesmodel->getHolidayDatesListForGroup($employeeGroupId);
					  $holidaylist_query = "SELECT `h`.`holidaydate` FROM `main_holidaydates` AS `h` WHERE (h.groupid = ".$employeeGroupId." AND h.isactive = 1)";
					  $holidayDateslistArr = $db->query($holidaylist_query)->fetchAll();		
					  if(!empty($holidayDateslistArr))
					  {
						  for($i=0;$i<sizeof($holidayDateslistArr);$i++)
						   {
							  $holidayDatesArr[$i] = $holidayDateslistArr[$i]['holidaydate'];
						   }
					  } 
					}  
	
					$weekend1 = $weekend_result['daystartname'];
					$weekend2 = $weekend_result['dayendname'];
				}
				/*else
				{
				   $weekend1 = 'Saturday';
				   $weekend2 = 'Sunday'; 			   
				}*/
					
					
				$fromdate_obj = new DateTime($fromDate);
				$weekDay = $fromdate_obj->format('l');
				while($fromDate <= $toDate)
				{
					/*if(($weekDay != 'Saturday'||$weekDay != 'Sunday') && (!empty($holidayDates)) && (!in_array($fromDate,$holidayDates)))*/
					if(count($holidayDatesArr)>0)
					{
						if($weekDay != $weekend1 && $weekDay != $weekend2 && (!in_array($fromDate,$holidayDatesArr)))
						{
							$noOfDays++;
						}
					}
					else
					{
						if($weekDay != $weekend1 && $weekDay != $weekend2)
						{
							$noOfDays++;
						}
					}
					$fromdate_obj->add(new DateInterval('P1D'));	//Increment from date by one day...
					$fromDate = $fromdate_obj->format('Y-m-d');
					$weekDay = $fromdate_obj->format('l');
				}	
			}		
				
		return $noOfDays;
			
	}
	
	public function calculatedays($userid,$leavetypetext='',$leavetypelimit='',$leaveday,$from_date,$to_date='',$selectorid='')
	{
	    $messagearray = array();
		$db = Zend_Db_Table::getDefaultAdapter();
	    $noOfDays =0;
		$weekDay='';
		$employeeDepartmentId = '';
		$employeeGroupId = '';
		$weekend1 = '';
		$weekend2 = '';
		$holidayDatesArr = array();
		
		$fromDate = sapp_Global::change_date($from_date,'database');
		$from_obj = new DateTime($from_date);
		$from_date = $from_obj->format('Y-m-d');
		
		if($to_date !='')
		{
			$toDate = sapp_Global::change_date($to_date,'database');
			$to_obj = new DateTime($to_date);
		}
		else
		{
		  $toDate = sapp_Global::change_date($from_date,'database');
		  $to_obj = new DateTime($from_date);	
		}  
		$to_date = $to_obj->format('Y-m-d');
		
		if($leaveday == 1)
			{
				if($to_date >= $from_date)
				{
				    $emp_query = "SELECT e.holiday_group,e.department_id FROM main_employees AS e WHERE (e.isactive = 1 AND e.user_id=".$userid.")";
					$emp_result = $db->query($emp_query)->fetch();
					//$loggedInEmployeeDetails = $employeesmodel->getLoggedInEmployeeDetails($loginUserId);
					
					if(!empty($emp_result))
					{
						$employeeDepartmentId = $emp_result['department_id'];
						$employeeGroupId = $emp_result['holiday_group'];
						if($employeeDepartmentId !='' && $employeeDepartmentId !=NULL)
						{
						$weekend_query = "SELECT `l`.`weekend_startday` AS `weekendstartday`, `l`.`weekend_endday` AS `weekendday`, `l`.`is_halfday`, `l`.`is_leavetransfer`, `l`.`is_skipholidays`, `w`.`week_name` AS `daystartname`, `wk`.`week_name` AS `dayendname` FROM `main_leavemanagement` AS `l`
										 LEFT JOIN `tbl_weeks` AS `w` ON w.week_id=l.weekend_startday
										 LEFT JOIN `tbl_weeks` AS `wk` ON wk.week_id=l.weekend_endday WHERE (l.department_id = ".$employeeDepartmentId." AND l.isactive = 1)";
						$weekend_result = $db->query($weekend_query)->fetch();	
						}		
						//$weekendDetailsArr = $leavemanagementmodel->getWeekendNamesDetails($employeeDepartmentId);
						
						if(!empty($weekend_result))
						{
							if($weekend_result['is_skipholidays'] == 1 && isset($employeeGroupId) && $employeeGroupId !='')
							{
							  //$holidayDateslistArr = $holidaydatesmodel->getHolidayDatesListForGroup($employeeGroupId);
							  $holidaylist_query = "SELECT `h`.`holidaydate` FROM `main_holidaydates` AS `h` WHERE (h.groupid = ".$employeeGroupId." AND h.isactive = 1)";
							  $holidayDateslistArr = $db->query($holidaylist_query)->fetchAll();		
							  if(!empty($holidayDateslistArr))
							  {
								  for($i=0;$i<sizeof($holidayDateslistArr);$i++)
								   {
									  $holidayDatesArr[$i] = $holidayDateslistArr[$i]['holidaydate'];
								   }
							  } 
							}  
								$weekend1 = $weekend_result['daystartname'];
								$weekend2 = $weekend_result['dayendname'];
						}
						/*else
						{
						   $weekend1 = 'Saturday';
						   $weekend2 = 'Sunday'; 			   
						}*/
							
							
						$fromdate_obj = new DateTime($fromDate);
						$weekDay = $fromdate_obj->format('l');
						while($fromDate <= $toDate)
						{
							/*if(($weekDay != 'Saturday'||$weekDay != 'Sunday') && (!empty($holidayDates)) && (!in_array($fromDate,$holidayDates)))*/
							if(count($holidayDatesArr)>0)
							{
								if($weekDay != $weekend1 && $weekDay != $weekend2 && (!in_array($fromDate,$holidayDatesArr)))
								{
									$noOfDays++;
								}
							}
							else
							{
								if($weekDay != $weekend1 && $weekDay != $weekend2)
								{
									$noOfDays++;
								}
							}
							$fromdate_obj->add(new DateInterval('P1D'));	//Increment from date by one day...
							$fromDate = $fromdate_obj->format('Y-m-d');
							$weekDay = $fromdate_obj->format('l');
						}	
					}
					$data = array('status'=>'1','message'=>'success','noOfDays' => $noOfDays);
				}else
				{
				   if($selectorid !='' && $selectorid == 1)
					{
						$messagearray['from_date'] = 'From date should be less than to date.';
					}
					else if($selectorid !='' && $selectorid == 2)
					{
						$messagearray['to_date'] = 'To date should be greater than from date.';					
					}
					
					$data = array('status'=>'0','message'=>$messagearray,'noOfDays' => '');
				}
		}else if($leaveday == 2)
		{
		    $data = array('status'=>'1','message'=>'success','noOfDays' => 0.5);
		}
        return $data;		
	}
	
	public function checkdateexists($from_date, $to_date,$userid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$result = array();
		if($from_date!='' && $to_date != '')
		{
			$query = "select count(l.id) as dateexist from main_leaverequest l where l.user_id=".$userid." and l.leavestatus IN(1,2) and l.isactive = 1
			and (l.from_date between '".$from_date."' and '".$to_date."' OR l.to_date between '".$from_date."' and '".$to_date."' )";
			
			$result = $db->query($query)->fetchAll();
		}	
	    return $result;
	}
	
	

}