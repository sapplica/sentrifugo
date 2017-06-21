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

class Timemanagement_IndexController extends Zend_Controller_Action
{
	private $options;
	public function preDispatch()
	{
		// $userModel = new Timemanagement_Model_Users();
		// $checkTmEnable = $userModel->checkTmEnable();

		// if(!$checkTmEnable){
			// $this->_redirect('error');
		// }
		
		
		//check Time management module enable
		if(!sapp_Helper::checkTmEnable())
			$this->_redirect('error');

		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('getapprovedtimesheet', 'html')->initContext();
		$ajaxContext->addActionContext('closeapprovealert', 'json')->initContext();
		$ajaxContext->addActionContext('save', 'json')->initContext();
		$ajaxContext->addActionContext('submit', 'json')->initContext();
		$ajaxContext->addActionContext('eraseweek', 'json')->initContext();
		$ajaxContext->addActionContext('converdate', 'json')->initContext();
	}

	public function init()
	{
		$this->_options= $this->getInvokeArg('bootstrap')->getOptions();

	}
	/**
	 * default action
	 */
	public function indexAction()
	{
		$usersModel = new Timemanagement_Model_Users();
		$storage = new Zend_Auth_Storage_Session();
		$data = $storage->read();
			
		$selYrMon = $this->_getParam('selYrMon');

		//$startYrMon = $this->_getParam('startYrMon');
		$flag = $this->_getParam('flag');
			
		//        $month = $this->_getParam('month');
		//    	$year = $this->_getParam('year');
			
		//$empid = $this->_getParam('empid');
		//echo " MONTH ".$month. "  YEAR ".$year; exit;

		$approvedAlert = $usersModel->getEmpApprovalStatusDteails($data->id);
			
		$empDoj = $usersModel->getEmployeeDoj($data->id);
		
		
		//$this->view->empDoj=$empDoj['date_of_joining'];
		
		
		$doj_date = strtotime($empDoj['date_of_joining']);
		$created_date = strtotime($empDoj['createddate']);
		
		if($created_date < $doj_date)
		{
			$this->view->empDoj=$empDoj['date_of_joining'];
			$dateEmpDoj = date('Y-m',strtotime($empDoj['date_of_joining']));
		}
		else{
			$this->view->empDoj=$empDoj['createddate'];
			$dateEmpDoj = date('Y-m',strtotime($empDoj['createddate']));
		}
		
		//$dateEmpDoj = date('Y-m',strtotime($empDoj['date_of_joining']));
			
		//    	$datestring='2011-01 last month';
		//		$dt=date_create($datestring);
		//		echo $dt->format('Y-m'); //2011-02
		//	$YrMonths = array();
		$now = new DateTime();
		//		if($flag == 'next' || $flag == '') {
		//			$YrMonths[3] = ($startYrMon != '')?$startYrMon: $now->format('Y-m');
		//			for($i = 2,$j=0; $i >=0; $i--) {
		//				$dt=date_create($YrMonths[3-$j].'last month');
		//				$YrMonths[$i] = $dt->format('Y-m');
		//				$j++;
		//			}
		//		} else {
		//			$YrMonths[0] = ($startYrMon != '')?$startYrMon: $now->format('Y-m');
		//			for($i = 1; $i <=3; $i++) {
		//				$dt=date_create($YrMonths[$i-1].'next month');
		//				$YrMonths[$i] = $dt->format('Y-m');
		//			}
		//		}
		//$YrMonths[3] = ($startYrMon != '')?$startYrMon: $now->format('Y-m');
		if($selYrMon != '') {
			$selYrMon = date('Y-m',strtotime($selYrMon));
			if($flag == 'next') {
				if($selYrMon < $now->format('Y-m'))
				$selYrMon = date_create($selYrMon.'next month')->format('Y-m');
			}else if($flag == 'pre'){
				//echo " selYrMon ".$selYrMon." dateEmpDoj ".$dateEmpDoj;
				if($selYrMon > $dateEmpDoj)
				$selYrMon = date_create($selYrMon.'last month')->format('Y-m');
				else
				$selYrMon  = $dateEmpDoj;
			}

		}

		$selYrMon = ($selYrMon != '')?$selYrMon:$now->format('Y-m');

		//ksort($YrMonths);
			
		$yrMon = explode('-', $selYrMon);
		$empTSModel = new Timemanagement_Model_MyTimesheet();
		$empMonthTSData = $empTSModel->getMonthTimesheetData($data->id, $yrMon[0],$yrMon[1]);
		//print_r($empMonthTSData); exit;
		$empHolidaysWeekendsData = $usersModel->getEmployeeHolidaysNWeekends($data->id, $yrMon[0],$yrMon[1]);
		//print_r($empHolidaysWeekendsData); exit;
			
		//echo $selYrMon;
		$firstday = $yrMon[0]."-".$yrMon[1].'-01';
		$noOfDaysMonth = date("t", mktime(0, 0, 0, $yrMon[1], 1, $yrMon[0]));
		$lastday =   $yrMon[0]."-".$yrMon[1]."-".$noOfDaysMonth;
			
		$empLeavesData = $usersModel->getEmpLeaves($data->id,$firstday,$lastday,'all');

		$cronDetails = $empTSModel->getCronDetailsForMonth($yrMon[0],$yrMon[1]);
		$cronStartDay = "";
		$cronEndDay = "";
		if(!empty($cronDetails)) {
			$cronStartDay = date('d',strtotime($cronDetails[0]['ts_start_date']));
			if($cronStartDay != 1) {
				$cronStartDay = 1;
				$mon = $yrMon[1]+1;
				$yr = ($mon == 12)?($yrMon[0] +1):$yrMon[0];
				$nextMonCronDetails = $empTSModel->getCronDetailsForMonth($yr,$mon);
				if(!empty($nextMonCronDetails)) {
					$cronEndDay = $noOfDaysMonth;
				} else {
					$cronEndDay = date('d',strtotime($cronDetails[0]['ts_end_date']));
				}
			}
		} else {
			$mon = $yrMon[1]+1;
			$yr = ($mon == 12)?($yrMon[0] +1):$yrMon[0];
			$nextMonCronDetails = $empTSModel->getCronDetailsForMonth($yr,$mon);

			if(!empty($nextMonCronDetails)) {
				if($yrMon[1]  == date('m',strtotime($nextMonCronDetails[0]['ts_start_date'])))
				$cronStartDay = date('d',strtotime($nextMonCronDetails[0]['ts_start_date']));
				$cronEndDay = $noOfDaysMonth;
			}

		}

		$this->view->empMonthTSData = $empMonthTSData;
		$this->view->empHolidaysWeekends = $empHolidaysWeekendsData[0];
		//	$this->view->month = $month;
		//	$this->view->year = $year;
		//$this->view->YrMonths = $YrMonths;
		$this->view->selYrMon =  $selYrMon;
		$this->view->leavesData = $empLeavesData;
		$this->view->cronStartDay = $cronStartDay;
		$this->view->cronEndDay = $cronEndDay;
		$this->view->approvedAlert =  $approvedAlert;



		/*Leave request code starts*/
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$leaverequestform = new Default_Form_leaverequest();
		$leaverequestform->setAttrib('action',BASE_URL.'leaverequest');
		$leaverequestmodel = new Default_Model_Leaverequest();
		$employeeleavetypemodel = new Default_Model_Employeeleavetypes();
		$leavemanagementmodel = new Default_Model_Leavemanagement();
		$usersmodel = new Default_Model_Users();
		$employeesmodel = new Default_Model_Employees();
		$weekdaysmodel = new Default_Model_Weekdays();
		$holidaydatesmodel = new Default_Model_Holidaydates();
		$msgarray = array(); 
		$dateofjoiningArr = array();
		$holidayDateslistArr = array();
		$rMngr = 'No';
		$availableleaves = '';
		$rep_mang_id = '';
		$employeeemail = '';
		$reportingManageremail = '';
		$week_startday = '';
		$week_endday = '';
		$ishalf_day = '';
		$userfullname = '';
		$reportingmanagerName = '';
		$businessunitid = '';
		$hremailgroup = '';
		
		/* Start
		   Queries to fetch user details,reporting manager details and weekend details from users table and employees table
		*/
		    if($loginUserId !='' && $loginUserId != NULL)
			{
				$loggedinEmpId = $usersmodel->getUserDetailsByID($loginUserId);
				$loggedInEmployeeDetails = $employeesmodel->getLoggedInEmployeeDetails($loginUserId);
				
				if(!empty($loggedInEmployeeDetails))
					{
					    if($loggedInEmployeeDetails[0]['date_of_joining'] !='')
						{
						    $date = new DateTime($loggedInEmployeeDetails[0]['date_of_joining']);
                            $datofjoiningtimestamp =  $date->getTimestamp();
							$dateofjoining = explode("-",$loggedInEmployeeDetails[0]['date_of_joining']);
							
							$year = $dateofjoining[0];
							$month = $dateofjoining[1];
							$day = $dateofjoining[2];
							$dateofjoiningArr = array('year'=> $year,'month'=> $month,'day'=> $day,'datetimestamp'=>$datofjoiningtimestamp);
						}
						$reportingmanagerId = $loggedInEmployeeDetails[0]['reporting_manager'];
						$employeeDepartmentId = $loggedInEmployeeDetails[0]['department_id'];
						$employeeEmploymentStatusId = $loggedInEmployeeDetails[0]['emp_status_id'];
						$employeeHolidayGroupId = $loggedInEmployeeDetails[0]['holiday_group'];
						
						$reportingManagerDetails = $usersmodel->getUserDetailsByID($reportingmanagerId);
						$weekendDatailsArr = $leavemanagementmodel->getWeekendDetails($employeeDepartmentId);
                        $employeeemail = $loggedinEmpId[0]['emailaddress'];
						$userfullname = $loggedinEmpId[0]['userfullname'];
						$businessunitid = $loggedInEmployeeDetails[0]['businessunit_id'];
                        if(!empty($reportingManagerDetails))
						{
							$leaverequestform->rep_mang_id->setValue($reportingManagerDetails[0]['userfullname']); 
							$reportingManageremail = $reportingManagerDetails[0]['emailaddress'];
							$reportingmanagerName = $reportingManagerDetails[0]['userfullname'];
							$rep_mang_id = $reportingManagerDetails[0]['id']; 
							$rMngr = 'Yes';
						}
						else
						{
						   $msgarray['rep_mang_id'] = 'Reporting manager is not assigned yet. Please contact your HR.';
						}
						
						if(!empty($weekendDatailsArr))
						{
							$week_startday = $weekendDatailsArr[0]['weekendstartday'];
							$week_endday = $weekendDatailsArr[0]['weekendday'];
							$ishalf_day = $weekendDatailsArr[0]['is_halfday'];
							$isskip_holidays = $weekendDatailsArr[0]['is_skipholidays'];
							
                        }
                        else
						{
						   $msgarray['from_date'] = 'Leave management options are not configured yet.';
						   $msgarray['to_date'] = 'Leave management options are not configured yet.';
						}

						if($employeeHolidayGroupId !='' && $employeeHolidayGroupId != NULL)
							$holidayDateslistArr = $holidaydatesmodel->getHolidayDatesListForGroup($employeeHolidayGroupId	);
							
                        if (defined('LV_HR_'.$businessunitid))
							$hremailgroup = 'hremailgroupexists';
						else
						    $hremailgroup = '';

						/* Search Filters */    
						$isReportingManagerFlag = 'false';
						$searchRepFlag = 'false';
						$searchMeFlag = 'true';
						
				    	$filter = $this->_request->getParam('filter');
				    	if(!empty($filter)) {
				    	  if(in_array(2, $filter))		
				    	  	$searchRepFlag = 'true';
				    	  	
				    	  if(in_array(1, $filter))	
				    	  	$searchMeFlag = 'true';
				    	  else
				    	  	$searchMeFlag = 'false';	
				    	}	  	
				    	
				    	if($searchMeFlag == 'true')
							$leaverequestdetails = $leaverequestmodel->getUserApprovedOrPendingLeavesData($loginUserId);
						/* Start -For Checking if logged in user is reporting manager */
						$isReportingManager = $employeesmodel->CheckIfReportingManager($loginUserId);
						if(!empty($isReportingManager) && $isReportingManager[0]['count']>0) {
							if($searchRepFlag=='true')
								$managerrequestdetails = $leaverequestmodel->getManagerApprovedOrPendingLeavesData($loginUserId);
							$isReportingManagerFlag = 'true';
						}
						/* End */	
						$this->view->userfullname = $userfullname; 					
						$this->view->loggedinEmpId = $loggedinEmpId;
						$this->view->weekendDatailsArr = $weekendDatailsArr;
						$this->view->reportingManagerDetails = $reportingManagerDetails;  
						$this->view->rMngr = $rMngr;
						$this->view->hremailgroup = $hremailgroup;
						$this->view->dateofjoiningArr = $dateofjoiningArr;
						$this->view->leaverequestdetails = !empty($leaverequestdetails)?$leaverequestdetails:array();
						$this->view->holidayDateslistArr = $holidayDateslistArr;																								
						$this->view->managerrequestdetails = !empty($managerrequestdetails)?$managerrequestdetails:array();
						$this->view->isReportingManagerFlag = $isReportingManagerFlag;
						$this->view->searchRepFlag = $searchRepFlag;
						$this->view->searchMeFlag = $searchMeFlag;
					}
                    else
					{
					   $msgarray['rep_mang_id'] = 'Reporting manager is not assigned yet. Please contact your HR.';
					   $msgarray['from_date'] = 'Leave management options are not configured yet.';
					   $msgarray['to_date'] = 'Leave management options are not configured yet.';
					}   					
			}
		/* End */
		
		/* 
		 Start
		 Query to fetch and build multioption for Leavetype dropdown
		*/
		$leavetype = $employeeleavetypemodel->getactiveleavetype();
   		if(!empty($leavetype))
		    {
				if(sizeof($leavetype) > 0)
				{
					foreach ($leavetype as $leavetyperes){
						$leaverequestform->leavetypeid->addMultiOption($leavetyperes['id'].'!@#'.$leavetyperes['numberofdays'].'!@#'.utf8_encode($leavetyperes['leavetype']),utf8_encode($leavetyperes['leavetype']));
					}
				}
			}
		else
			{
				$msgarray['leavetypeid'] = ' Leave types are not configured yet.';
			}
			$this->view->leavetype = $leavetype;
		/* End */
		
		/*
		START
		Query to get the number of available leaves for the employee 
		*/   
      	   $getavailbaleleaves = $leaverequestmodel->getAvailableLeaves($loginUserId);
		     if(!empty($getavailbaleleaves))
			   {
			    $leaverequestform->no_of_days->setValue($getavailbaleleaves[0]['remainingleaves']);
				$availableleaves = $getavailbaleleaves[0]['remainingleaves'];
		       }
			   else
				{
				   $msgarray['no_of_days'] = 'You have not been allotted leaves for this financial year. Please contact your HR.';
				}
			$this->view->getavailbaleleaves = $getavailbaleleaves;	
	    /* END */
		
		
		$this->view->form = $leaverequestform; 
		$this->view->msgarray = $msgarray;
		$this->view->loginUserId = $loginUserId;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
		/*leave request code ends*/
		
		
		//START code to show pending weeks for submit in current month

		$projmodel = new Timemanagement_Model_Projects();
		$prevweeks = $projmodel->getprevmonthweeks(date('Y-m') , date('d'));
		 $end_date = max($prevweeks[count($prevweeks)]);//get previous week end date
		 $start_date = min($prevweeks[count($prevweeks)]);//get previous week start date
				       $prev_str='';
		if($start_date>=date('Y-m-01') && $start_date<=date('Y-m-t'))
		{
		// echo " TM Role >> ".Zend_Registry::get('tm_role');
				$prev_week_status=$projmodel->prev_weeksubmit_status($prevweeks,$data->id,$empDoj);

		
		       if($prev_week_status!='')
		       {
		          $this->view->prev_str=$prev_week_status;
		       }
		}	   
		//END code to show pending weeks for submit in current month

	}

	public function weekAction()
	{

		$usersModel = new Timemanagement_Model_Users();
		$storage = new Zend_Auth_Storage_Session();
		$now = new DateTime();
		$data = $storage->read();

		$selYrMon = $this->_getParam('selYrMon');
		$week = ($this->_getParam('week') != '')?$this->_getParam('week'):1;
		$calWeek = $this->_getParam('calWeek');
		$timeFlag = $this->_getParam('flag');
		$selDay = $this->_getParam('day');

		if($data->id == 1)
		$this->_redirect('error');


		$approvedAlert = $usersModel->getEmpApprovalStatusDteails($data->id);
		$selYrMon = ($selYrMon != '')?$selYrMon:$now->format('Y-m');
		$yrMon = explode('-', $selYrMon);

		if($timeFlag == 'time' && $selYrMon == $now->format('Y-m') && $calWeek == '') {

			$calWeek = strftime('%U',strtotime($selYrMon.'-'.$now->format('d')));
			$startCalWeek = strftime('%U',strtotime($selYrMon.'-01'));
			$week = ($calWeek- $startCalWeek) +1;
		}

		$empDoj = $usersModel->getEmployeeDoj($data->id);
		//$dateEmpDoj = date('Y-m',strtotime($empDoj['date_of_joining']));



		$selYrMonArray = explode('-', $selYrMon);

		//if($calWeek == null || $calWeek == '') {
		//$calWeekNo = gmstrftime('%U',strtotime($selYrMon.'-01'));

		if($selDay != '') {
			$calWeek = strftime('%U',strtotime($selYrMon.'-'.$selDay));
			$startCalWeek = strftime('%U',strtotime($selYrMon.'-01'));
			$week = ($calWeek- $startCalWeek) +1;
		} else {
			if($calWeek == '')
			$calWeek = strftime('%U',strtotime($selYrMon.'-01'));
		}
		//	}
		$myTsModel = new Timemanagement_Model_MyTimesheet();
		//$myTsWeekData = $myTsModel->getWeeklyTimesheetData($data->id,$selYrMonArray[0],$selYrMonArray[1],$week);
		if($timeFlag == 'time') {
			$myTsWeekData = $myTsModel->getWeeklyTimesheetData($data->id,$selYrMonArray[0],$selYrMonArray[1],$week);
		} else {
			$myTsWeekData = $myTsModel->getWeeklyTimesheetData($data->id,$selYrMonArray[0],$selYrMonArray[1],$week,'view');
		}
		//print_r($myTsWeekData);
		$empHolidaysWeekendsData = $usersModel->getEmployeeHolidaysNWeekends($data->id, $yrMon[0],$yrMon[1],$calWeek);
		//echo date("Y-m-d", strtotime("{$yrMon[0]}-W{$calWeek}-7"));
		
		//$startDate = date("Y-m-d", strtotime("{$yrMon[0]}-W{$calWeek}-7"));
		
		if($calWeek>=1 && $calWeek<=9)
			$calWeek = '0'.$calWeek;
		
		$startDate = date("Y-m-d", strtotime("{$yrMon[0]}-W{$calWeek}, -1 day"));
		
		//$startDate =  date("Y-m-d",strtotime('last sunday', strtotime($yrMon[0].'W'.str_pad($calWeek+1, 2, 0, STR_PAD_LEFT))));
		
		$endDate = date("Y-m-d",strtotime('next saturday',strtotime($startDate)));

		$empLeavesData = $usersModel->getEmpLeaves($data->id,$startDate,$endDate,'all');
		//print_r($empLeavesData);
		$weekNotes = $myTsModel->getWeekNotes($data->id,$selYrMonArray[0],$selYrMonArray[1],$week);

		//$weekDaysStatus =  $myTsModel->getWeekDaysStatus($data->id,$selYrMonArray[0],$calWeek);
		$weekDaysStatus =  $myTsModel->getWeekDaysStatus($data->id,$selYrMonArray[0],$selYrMonArray[1],$week);
		$weekDaysProjStatus =  $myTsModel->getWeekDaysProjStatus($data->id,$selYrMonArray[0],$selYrMonArray[1],$week);
		
		//print_r($weekDaysStatus);
		//print_r($weekNotes);
		$cronDetails = $myTsModel->getCronDetailsForMonth($selYrMonArray[0],$selYrMonArray[1]);
		$noOfDaysMonth = date("t", mktime(0, 0, 0, $selYrMonArray[1], 1, $selYrMonArray[0]));
		$cronStartDay = "";
		$cronEndDay = "";
		if(!empty($cronDetails)) {
			$cronStartDay = date('d',strtotime($cronDetails[0]['ts_start_date']));
			if($cronStartDay != 1) {
				$cronStartDay = 1;
				$mon = $yrMon[1]+1;
				$yr = ($mon == 12)?($selYrMonArray[0] +1):$selYrMonArray[0];
				$nextMonCronDetails = $myTsModel->getCronDetailsForMonth($yr,$mon);
				if(!empty($nextMonCronDetails)) {
					$cronEndDay = $noOfDaysMonth;
				} else {
					$cronEndDay = date('d',strtotime($cronDetails[0]['ts_end_date']));
				}
			}
		} else {
			$mon = $yrMon[1]+1;
			$yr = ($mon == 12)?($selYrMonArray[0] +1):$selYrMonArray[0];
			$nextMonCronDetails = $myTsModel->getCronDetailsForMonth($yr,$mon);
				
			if(!empty($nextMonCronDetails)) {
				if($yrMon[1]  == date('m',strtotime($nextMonCronDetails[0]['ts_start_date'])))
				$cronStartDay = date('d',strtotime($nextMonCronDetails[0]['ts_start_date']));
				$cronEndDay = $noOfDaysMonth;
			}
				
		}

		if($selDay != '') {
			$this->view->selDay =  date("D",strtotime($selYrMon.'-'.$selDay));
		}
		
		//$this->view->empDoj=$empDoj['date_of_joining'];
		
		$doj_date = strtotime($empDoj['date_of_joining']);
		$created_date = strtotime($empDoj['createddate']);
		
		if($created_date < $doj_date)
		{
			$this->view->empDoj=$empDoj['date_of_joining'];
			
		}
		else{
			$this->view->empDoj=$empDoj['createddate'];
			
		}
		
		
		
		$this->view->selYrMon =  $selYrMon;
		$this->view->selWeek =  $week;
		$this->view->myTsWeekData = $myTsWeekData;
		$this->view->weekNotesData = $weekNotes;
		$this->view->empHolidaysWeekends = $empHolidaysWeekendsData[0];
		$this->view->leavesData = $empLeavesData;
		$this->view->approvedAlert =  $approvedAlert;
		$this->view->weekDaysStatus = $weekDaysStatus;
		$this->view->weekDaysProjStatus = $weekDaysProjStatus;		
		$this->view->cronStartDay = $cronStartDay;
		$this->view->cronEndDay = $cronEndDay;

		//if($timeFlag == 'time') $this->renderScript('index/enter_time.phtml');


		//START code to show pending weeks for submit in current month

		$projmodel = new Timemanagement_Model_Projects();
		$prevweeks = $projmodel->getprevmonthweeks(date('Y-m') , date('d'));
		// echo " TM Role >> ".Zend_Registry::get('tm_role');
		//		$prev_week_status=$projmodel->prev_weeksubmit_status($prevweeks,$data->id,$empDoj);
		//        $prev_str='';
		//
		//        if($prev_week_status!='')
		//        {
		//           $this->view->prev_str=$prev_week_status;
		//        }
		//END code to show pending weeks for submit in current month
			
		/*Leave request code starts*/
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$leaverequestform = new Default_Form_leaverequest();
		$leaverequestform->setAttrib('action',BASE_URL.'leaverequest');
		$leaverequestmodel = new Default_Model_Leaverequest();
		$employeeleavetypemodel = new Default_Model_Employeeleavetypes();
		$leavemanagementmodel = new Default_Model_Leavemanagement();
		$usersmodel = new Default_Model_Users();
		$employeesmodel = new Default_Model_Employees();
		$weekdaysmodel = new Default_Model_Weekdays();
		$holidaydatesmodel = new Default_Model_Holidaydates();
		$msgarray = array(); 
		$dateofjoiningArr = array();
		$holidayDateslistArr = array();
		$rMngr = 'No';
		$availableleaves = '';
		$rep_mang_id = '';
		$employeeemail = '';
		$reportingManageremail = '';
		$week_startday = '';
		$week_endday = '';
		$ishalf_day = '';
		$userfullname = '';
		$reportingmanagerName = '';
		$businessunitid = '';
		$hremailgroup = '';
		
		/* Start
		   Queries to fetch user details,reporting manager details and weekend details from users table and employees table
		*/
		    if($loginUserId !='' && $loginUserId != NULL)
			{
				$loggedinEmpId = $usersmodel->getUserDetailsByID($loginUserId);
				$loggedInEmployeeDetails = $employeesmodel->getLoggedInEmployeeDetails($loginUserId);
				
				if(!empty($loggedInEmployeeDetails))
					{
					    if($loggedInEmployeeDetails[0]['date_of_joining'] !='')
						{
						    $date = new DateTime($loggedInEmployeeDetails[0]['date_of_joining']);
                            $datofjoiningtimestamp =  $date->getTimestamp();
							$dateofjoining = explode("-",$loggedInEmployeeDetails[0]['date_of_joining']);
							
							$year = $dateofjoining[0];
							$month = $dateofjoining[1];
							$day = $dateofjoining[2];
							$dateofjoiningArr = array('year'=> $year,'month'=> $month,'day'=> $day,'datetimestamp'=>$datofjoiningtimestamp);
						}
						$reportingmanagerId = $loggedInEmployeeDetails[0]['reporting_manager'];
						$employeeDepartmentId = $loggedInEmployeeDetails[0]['department_id'];
						$employeeEmploymentStatusId = $loggedInEmployeeDetails[0]['emp_status_id'];
						$employeeHolidayGroupId = $loggedInEmployeeDetails[0]['holiday_group'];
						
						$reportingManagerDetails = $usersmodel->getUserDetailsByID($reportingmanagerId);
						$weekendDatailsArr = $leavemanagementmodel->getWeekendDetails($employeeDepartmentId);
                        $employeeemail = $loggedinEmpId[0]['emailaddress'];
						$userfullname = $loggedinEmpId[0]['userfullname'];
						$businessunitid = $loggedInEmployeeDetails[0]['businessunit_id'];
                        if(!empty($reportingManagerDetails))
						{
							$leaverequestform->rep_mang_id->setValue($reportingManagerDetails[0]['userfullname']); 
							$reportingManageremail = $reportingManagerDetails[0]['emailaddress'];
							$reportingmanagerName = $reportingManagerDetails[0]['userfullname'];
							$rep_mang_id = $reportingManagerDetails[0]['id']; 
							$rMngr = 'Yes';
						}
						else
						{
						   $msgarray['rep_mang_id'] = 'Reporting manager is not assigned yet. Please contact your HR.';
						}
						
						if(!empty($weekendDatailsArr))
						{
							$week_startday = $weekendDatailsArr[0]['weekendstartday'];
							$week_endday = $weekendDatailsArr[0]['weekendday'];
							$ishalf_day = $weekendDatailsArr[0]['is_halfday'];
							$isskip_holidays = $weekendDatailsArr[0]['is_skipholidays'];
							
                        }
                        else
						{
						   $msgarray['from_date'] = 'Leave management options are not configured yet.';
						   $msgarray['to_date'] = 'Leave management options are not configured yet.';
						}

						if($employeeHolidayGroupId !='' && $employeeHolidayGroupId != NULL)
							$holidayDateslistArr = $holidaydatesmodel->getHolidayDatesListForGroup($employeeHolidayGroupId	);
							
                        if (defined('LV_HR_'.$businessunitid))
							$hremailgroup = 'hremailgroupexists';
						else
						    $hremailgroup = '';

						/* Search Filters */    
						$isReportingManagerFlag = 'false';
						$searchRepFlag = 'false';
						$searchMeFlag = 'true';
						
				    	$filter = $this->_request->getParam('filter');
				    	if(!empty($filter)) {
				    	  if(in_array(2, $filter))		
				    	  	$searchRepFlag = 'true';
				    	  	
				    	  if(in_array(1, $filter))	
				    	  	$searchMeFlag = 'true';
				    	  else
				    	  	$searchMeFlag = 'false';	
				    	}	  	
				    	
				    	if($searchMeFlag == 'true')
							$leaverequestdetails = $leaverequestmodel->getUserApprovedOrPendingLeavesData($loginUserId);
						/* Start -For Checking if logged in user is reporting manager */
						$isReportingManager = $employeesmodel->CheckIfReportingManager($loginUserId);
						if(!empty($isReportingManager) && $isReportingManager[0]['count']>0) {
							if($searchRepFlag=='true')
								$managerrequestdetails = $leaverequestmodel->getManagerApprovedOrPendingLeavesData($loginUserId);
							$isReportingManagerFlag = 'true';
						}
						/* End */	
						$this->view->userfullname = $userfullname; 					
						$this->view->loggedinEmpId = $loggedinEmpId;
						$this->view->weekendDatailsArr = $weekendDatailsArr;
						$this->view->reportingManagerDetails = $reportingManagerDetails;  
						$this->view->rMngr = $rMngr;
						$this->view->hremailgroup = $hremailgroup;
						$this->view->dateofjoiningArr = $dateofjoiningArr;
						$this->view->leaverequestdetails = !empty($leaverequestdetails)?$leaverequestdetails:array();
						$this->view->holidayDateslistArr = $holidayDateslistArr;																								
						$this->view->managerrequestdetails = !empty($managerrequestdetails)?$managerrequestdetails:array();
						$this->view->isReportingManagerFlag = $isReportingManagerFlag;
						$this->view->searchRepFlag = $searchRepFlag;
						$this->view->searchMeFlag = $searchMeFlag;
					}
                    else
					{
					   $msgarray['rep_mang_id'] = 'Reporting manager is not assigned yet. Please contact your HR.';
					   $msgarray['from_date'] = 'Leave management options are not configured yet.';
					   $msgarray['to_date'] = 'Leave management options are not configured yet.';
					}   					
			}
		/* End */
		
		/* 
		 Start
		 Query to fetch and build multioption for Leavetype dropdown
		*/
		$leavetype = $employeeleavetypemodel->getactiveleavetype();
   		if(!empty($leavetype))
		    {
				if(sizeof($leavetype) > 0)
				{
					foreach ($leavetype as $leavetyperes){
						$leaverequestform->leavetypeid->addMultiOption($leavetyperes['id'].'!@#'.$leavetyperes['numberofdays'].'!@#'.utf8_encode($leavetyperes['leavetype']),utf8_encode($leavetyperes['leavetype']));
					}
				}
			}
		else
			{
				$msgarray['leavetypeid'] = ' Leave types are not configured yet.';
			}
			$this->view->leavetype = $leavetype;
		/* End */
		
		/*
		START
		Query to get the number of available leaves for the employee 
		*/   
      	   $getavailbaleleaves = $leaverequestmodel->getAvailableLeaves($loginUserId);
		     if(!empty($getavailbaleleaves))
			   {
			    $leaverequestform->no_of_days->setValue($getavailbaleleaves[0]['remainingleaves']);
				$availableleaves = $getavailbaleleaves[0]['remainingleaves'];
		       }
			   else
				{
				   $msgarray['no_of_days'] = 'You have not been allotted leaves for this financial year. Please contact your HR.';
				}
			$this->view->getavailbaleleaves = $getavailbaleleaves;	
	    /* END */
		
		
		$this->view->form = $leaverequestform; 
		$this->view->msgarray = $msgarray;
		$this->view->loginUserId = $loginUserId;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
		/*leave request code ends*/


		if($timeFlag != '') {
			$this->_helper->viewRenderer('entertime');
		}

	}

	public function saveAction()
	{
		
		
		$usersModel = new Timemanagement_Model_Users();
		$storage = new Zend_Auth_Storage_Session();
		$data = $storage->read();
		$empDoj = $usersModel->getEmployeeDoj($data->id);
		
		$doj = $empDoj['date_of_joining'];
		if($doj>date('Y-m-d'))
		{
			$this->view->status = 'failure';
		}else
		{	

		$tasksHrsData = $this->_getParam('data');
		$calWeek = $this->_getParam('calWeek');
		$week = $this->_getParam('week');
		$weekStartDate = $this->_getParam('weekStart');
		$weekEndDate = $this->_getParam('weekEnd');
		$selYrMon = $this->_getParam('selYrMon');
		$tasksData = explode(',',$tasksHrsData);
		//echo "tasksData ".sizeof($tasksData);
		$callval = $this->getRequest()->getParam('call');
		$sunNote = $this->_getParam('sun_note');
		$monNote = $this->_getParam('mon_note');
		$tueNote = $this->_getParam('tue_note');
		$wedNote = $this->_getParam('wed_note');
		$thuNote = $this->_getParam('thu_note');
		$friNote = $this->_getParam('fri_note');
		$satNote = $this->_getParam('sat_note');
		$weekNote = $this->_getParam('week_note');


		//$msgarray = array();
		$storage = new Zend_Auth_Storage_Session();
		$sessionData = $storage->read();

		$selYrMonArray = explode('-', $selYrMon);
		$myTsModel = new Timemanagement_Model_MyTimesheet();
		$projTasksArray = array();
		$projsArray = array();
		$date = new Zend_Date();

		$projectId = '';
		if($this->getRequest()->getPost() && $tasksHrsData != ''){
			foreach($tasksData as $data) {

				$taskDataArray = explode('#',$data);
				//echo " SUN ".$taskDataArray[2];
				$sun_hrs =($taskDataArray[2] != null)? $taskDataArray[2]:0;
				//echo " sun_hrs ".$sun_hrs;
				$sun_tot = 0;
				if(!empty($sun_hrs)) {
					$sun_dur = explode(':',$sun_hrs);
					$sun_tot += $sun_dur[0] * 60;
					//echo " Hrs ".$sun_dur[0];
					$sun_tot += $sun_dur[1];
				}
				//echo " sun_tot ".$sun_tot;
				$mon_hrs =($taskDataArray[3] != null)? $taskDataArray[3]:0;
				$mon_tot = 0;
				if(!empty($mon_hrs)) {
					$mon_dur = explode(':',$mon_hrs);
					$mon_tot += $mon_dur[0] * 60;
					$mon_tot += $mon_dur[1];
				}
				$tue_hrs =($taskDataArray[4] != null)? $taskDataArray[4]:0;
				$tue_tot = 0;
				if(!empty($tue_hrs)) {
					$tue_dur = explode(':',$tue_hrs);
					$tue_tot += $tue_dur[0] * 60;
					$tue_tot += $tue_dur[1];
				}

				$wed_hrs =($taskDataArray[5] != null)? $taskDataArray[5]:0;
				$wed_tot = 0;
				if(!empty($wed_hrs)) {
					$wed_dur = explode(':',$wed_hrs);
					$wed_tot += $wed_dur[0] * 60;
					$wed_tot += $wed_dur[1];
				}

				$thu_hrs =($taskDataArray[6] != null)? $taskDataArray[6]:0;
				$thu_tot = 0;
				if(!empty($thu_hrs)) {
					$thu_dur = explode(':',$thu_hrs);
					$thu_tot += $thu_dur[0] * 60;
					$thu_tot += $thu_dur[1];
				}

				$fri_hrs =($taskDataArray[7] != null)? $taskDataArray[7]:0;
				$fri_tot = 0;
				if(!empty($fri_hrs)) {
					$fri_dur = explode(':',$fri_hrs);
					$fri_tot += $fri_dur[0] * 60;
					$fri_tot += $fri_dur[1];
				}

				$sat_hrs =($taskDataArray[8] != null)? $taskDataArray[8]:0;

				$sat_tot = 0;
				if(!empty($sat_hrs)) {
					$sat_dur = explode(':',$sat_hrs);
					$sat_tot += $sat_dur[0] * 60;
					$sat_tot += $sat_dur[1];
				}
				$week_tot_time = (intval($sun_tot) + intval($mon_tot) + intval($tue_tot) + intval($wed_tot) + intval($thu_tot) + intval($fri_tot) + intval($sat_tot));
				$wkHrsPart = floor($week_tot_time / 60);
				$wkMinsPart = $week_tot_time % 60;
				if(strlen($wkHrsPart.'') == 1) $wkHrsPart = '0'.$wkHrsPart;
				if(strlen($wkMinsPart.'') == 1) $wkMinsPart = '0'.$wkMinsPart;

				//$week_tot = floor($week_tot_time / 60).':'.$week_tot_time % 60 ;
				$week_tot = $wkHrsPart.':'.$wkMinsPart;

				array_push($projTasksArray, $taskDataArray[0]);

				$data = array('emp_id'=>$sessionData->id,
			              'project_task_id'=>$taskDataArray[0],
						  'project_id'=>$taskDataArray[1],
						  'ts_year'=>$selYrMonArray[0],
						  'ts_month'=>$selYrMonArray[1],
						  'ts_week'=>$week,
						  'cal_week'=>$calWeek,
						  'sun_date'=>date('Y-m-d', strtotime($weekStartDate)),
						  'sun_duration'=>($taskDataArray[2] != null)?$taskDataArray[2]:'00:00',
						  'mon_date'=>date('Y-m-d', strtotime('+1 day', strtotime($weekStartDate))),
						  'mon_duration'=>($taskDataArray[3] != null)?$taskDataArray[3]:'00:00',
						  'tue_date'=>date('Y-m-d', strtotime('+2 day', strtotime($weekStartDate))),
						  'tue_duration'=> ($taskDataArray[4] != null)?$taskDataArray[4]:'00:00',
						  'wed_date'=>date('Y-m-d', strtotime('+3 day', strtotime($weekStartDate))),
						  'wed_duration'=>($taskDataArray[5] != null)?$taskDataArray[5]:'00:00',
			  			  'thu_date'=>date('Y-m-d', strtotime('+4 day', strtotime($weekStartDate))),
			   			  'thu_duration'=>($taskDataArray[6] != null)?$taskDataArray[6]:'00:00',
			  			  'fri_date'=>date('Y-m-d', strtotime('+5 day', strtotime($weekStartDate))),
			  			  'fri_duration'=>($taskDataArray[7] != null)?$taskDataArray[7]:'00:00',
			  			  'sat_date'=>date('Y-m-d', strtotime('+6 day', strtotime($weekStartDate))),
			  			  'sat_duration'=>($taskDataArray[8] != null)?$taskDataArray[8]:'00:00',			
						  'week_duration'=>$week_tot,
						  'created_by'=>$sessionData->id,
				//  'modified_by'=>NULL,
			        	  'is_active'=>1,
			  			  'created'=> Zend_Registry::get('currentdate') //gmdate("Y-m-d H:i:s")
				);
				$checkProjNull = $myTsModel->getProjNullRecordCountInTimeSheet($sessionData->id,$selYrMonArray[0],$selYrMonArray[1],$week);				
				
				if($checkProjNull != 0 ) {		
					$where = " ts_year = ".$selYrMonArray[0]." and ts_month = ".$selYrMonArray[1]." and ts_week = ".
						$week." and emp_id = ".$sessionData->id." and project_id is null";
					unset($data['created']);
					$data['modified'] = Zend_Registry::get('currentdate'); 				
					$myTsModel->updateTimesheetRecord($data,$where);
				} else {					
					$myTsModel->SaveOrUpdateTimesheet($data);
					//print_r($data);
					//exit;
				}	
				if($projectId != $taskDataArray[1]) {
					$projectId = $taskDataArray[1];
					array_push($projsArray,$taskDataArray[1]);

				}
			}
			foreach($projsArray as $proj) {

				$projHrsData = $myTsModel->getWeekProjectHrs($sessionData->id,$proj,$selYrMonArray[0],$selYrMonArray[1],$week);
				//	print_r($projHrsData); exit;

				$statusData = array('emp_id'=>$sessionData->id,
						  'project_id'=>$proj,
						  'ts_year'=>$selYrMonArray[0],
						  'ts_month'=>$selYrMonArray[1],
						  'ts_week'=>$week,
						  'cal_week'=>$calWeek,
						  'sun_date'=>date('Y-m-d', strtotime($weekStartDate)),
						  'sun_project_status'=>($projHrsData[0]['sun_duration'] !='00:00')?'saved':'no_entry',
						  'sun_status'=>($projHrsData[0]['sun_duration'] !='00:00')?'saved':'no_entry',
						  'sun_status_date'=>Zend_Registry::get('currentdate'),
						  'sun_reject_note'=>'',
					 	  'mon_date'=>date('Y-m-d', strtotime('+1 day', strtotime($weekStartDate))),
						  'mon_project_status'=>($projHrsData[0]['mon_duration'] !='00:00')?'saved':'no_entry',
						  'mon_status'=>($projHrsData[0]['mon_duration'] !='00:00')?'saved':'no_entry',
						  'mon_status_date'=>Zend_Registry::get('currentdate'),
						  'mon_reject_note'=>'',
						  'tue_date'=>date('Y-m-d', strtotime('+2 day', strtotime($weekStartDate))),
					 	  'tue_project_status'=>($projHrsData[0]['tue_duration'] !='00:00')?'saved':'no_entry',
						  'tue_status'=>($projHrsData[0]['tue_duration'] !='00:00')?'saved':'no_entry',
						  'tue_status_date'=>Zend_Registry::get('currentdate'),
						  'tue_reject_note'=>'',
						  'wed_date'=>date('Y-m-d', strtotime('+3 day', strtotime($weekStartDate))),
					 	  'wed_project_status'=>($projHrsData[0]['wed_duration'] !='00:00')?'saved':'no_entry',
						  'wed_status'=>($projHrsData[0]['wed_duration'] !='00:00')?'saved':'no_entry',
						  'wed_status_date'=>Zend_Registry::get('currentdate'),
						  'wed_reject_note'=>'',	
						  'thu_date'=>date('Y-m-d', strtotime('+4 day', strtotime($weekStartDate))),
					 	  'thu_project_status'=>($projHrsData[0]['thu_duration'] !='00:00')?'saved':'no_entry',
						  'thu_status'=>($projHrsData[0]['thu_duration'] !='00:00')?'saved':'no_entry',
						  'thu_status_date'=>Zend_Registry::get('currentdate'),
						  'thu_reject_note'=>'', 	
						  'fri_date'=>date('Y-m-d', strtotime('+5 day', strtotime($weekStartDate))),	
					 	  'fri_project_status'=>($projHrsData[0]['fri_duration'] !='00:00')?'saved':'no_entry',
						  'fri_status'=>($projHrsData[0]['fri_duration'] !='00:00')?'saved':'no_entry',
						  'fri_status_date'=>Zend_Registry::get('currentdate'),
						  'fri_reject_note'=>'', 	
						  'sat_date'=>date('Y-m-d', strtotime('+6 day', strtotime($weekStartDate))),		
						  'sat_project_status'=>($projHrsData[0]['sat_duration'] !='00:00')?'saved':'no_entry',
						  'sat_status'=>($projHrsData[0]['sat_duration'] !='00:00')?'saved':'no_entry',
						  'sat_status_date'=>Zend_Registry::get('currentdate'),
						  'sat_reject_note'=>'',		
						  'week_status'=>($projHrsData[0]['week_duration'] !='00:00')?'saved':'no_entry',
					 	  'created_by'=>$sessionData->id,
			        	  'is_active'=>1,
			  			  'created'=> Zend_Registry::get('currentdate') //gmdate("Y-m-d H:i:s")
				);
				//print_r($statusData); //exit;
				$checkProjNull = $myTsModel->getProjNullRecordCountInStatus($sessionData->id,$selYrMonArray[0],$selYrMonArray[1],$week);				
				
				if($checkProjNull != 0 ) {		
					$where = " ts_year = ".$selYrMonArray[0]." and ts_month = ".$selYrMonArray[1]." and ts_week = ".
						$week." and emp_id = ".$sessionData->id." and project_id is null";
					unset($statusData['created']);
					$statusData['modified'] = Zend_Registry::get('currentdate'); 				
					$myTsModel->updateStatusRecord($statusData,$where);
				} else {							
					$myTsModel->SaveOrUpdateTimesheetStatus($statusData);
				}
			}
			
			$notesData = array('emp_id'=>$sessionData->id,
						  'ts_year'=>$selYrMonArray[0],
						  'ts_month'=>$selYrMonArray[1],
						  'ts_week'=>$week,
						  'cal_week'=>$calWeek,
						  'sun_date'=>date('Y-m-d', strtotime($weekStartDate)),
						  'sun_note'=> $sunNote,
						  'sun_reject_note'=> '', 	
						  'mon_date'=>date('Y-m-d', strtotime('+1 day', strtotime($weekStartDate))),
						  'mon_note'=> $monNote,
						  'mon_reject_note'=> '',	
						  'tue_date'=>date('Y-m-d', strtotime('+2 day', strtotime($weekStartDate))),
						  'tue_note'=>$tueNote,
						  'tue_reject_note'=> '',
						  'wed_date'=>date('Y-m-d', strtotime('+3 day', strtotime($weekStartDate))),
						  'wed_note'=>$wedNote,
						  'wed_reject_note'=> '',	
						  'thu_date'=>date('Y-m-d', strtotime('+4 day', strtotime($weekStartDate))),
						  'thu_note'=>$thuNote,
						  'thu_reject_note'=> '',	
						  'fri_date'=>date('Y-m-d', strtotime('+5 day', strtotime($weekStartDate))),
						  'fri_note'=>$friNote,
						  'fri_reject_note'=> '', 	
						  'sat_date'=>date('Y-m-d', strtotime('+6 day', strtotime($weekStartDate))),
						  'sat_note'=>$satNote,
						  'sat_reject_note'=> '',							  			
						  'week_note'=>$weekNote,	
			 			  'created_by'=>$sessionData->id,					
			        	  'is_active'=>1,
			  			  'created'=> Zend_Registry::get('currentdate')//gmdate("Y-m-d H:i:s")
			);
			$myTsModel->SaveOrUpdateTimesheetNotes($notesData);
		}

		$empDeletedTasks = $myTsModel->empployeeDeletedTasks($sessionData->id, $selYrMonArray[0], $selYrMonArray[1], $week, $calWeek,$projTasksArray);
		//print_r($empDeletedTasks);
		//if(sizeof($empDeletedTasks) > 0 ) {
		if(is_array($empDeletedTasks)) {

			foreach($empDeletedTasks as $taskId) {
				$deleteTasksData = array('emp_id'=>$sessionData->id,
			              'project_task_id'=>$taskId,						  
						  'ts_year'=>$selYrMonArray[0],
						  'ts_month'=>$selYrMonArray[1],
						  'ts_week'=>$week,
						  'cal_week'=>$calWeek,						
						  'sun_duration'=>'00:00',						
						  'mon_duration'=>'00:00',						
						  'tue_duration'=> '00:00',						
						  'wed_duration'=>'00:00',			  			
			   			  'thu_duration'=>'00:00',			  			
			  			  'fri_duration'=>'00:00',			  			
			  			  'sat_duration'=>'00:00',			
						  'week_duration'=>'00:00',						
			        	  'is_active'=>0,
						  'modified_by'=>$sessionData->id,
			  			  'modified'=> Zend_Registry::get('currentdate') //gmdate("Y-m-d H:i:s")
				);

				$myTsModel->deleteTimesheetTask($sessionData->id,$deleteTasksData,$taskId, $selYrMonArray[0], $selYrMonArray[1], $week);
			}
		}
		$empDeletedProjects = $myTsModel->empployeeDeletedProjects($sessionData->id, $selYrMonArray[0], $selYrMonArray[1], $week, $calWeek,$projsArray);
		//	if(sizeof($empDeletedProjects) > 0 ) {
		if(is_array($empDeletedProjects)) {

			foreach($empDeletedProjects as $projId) {
				$deleteProjectsData = array('emp_id'=>$sessionData->id,
			              'project_id'=>$projId,						  
						  'ts_year'=>$selYrMonArray[0],
						  'ts_month'=>$selYrMonArray[1],
						  'ts_week'=>$week,
						  'cal_week'=>$calWeek,						
						  'sun_project_status'=>'no_entry',						
						  'mon_project_status'=>'no_entry',						
						  'tue_project_status'=> 'no_entry',						
						  'wed_project_status'=>'no_entry',			  			
			   			  'thu_project_status'=>'no_entry',			  			
			  			  'fri_project_status'=>'no_entry',			  			
			  			  'sat_project_status'=>'no_entry',
				//  'week_status'=>'no_entry',
			        	  'is_active'=>0,
						  'modified_by'=>$sessionData->id,
			  			  'modified'=> Zend_Registry::get('currentdate') //gmdate("Y-m-d H:i:s")
				);
				

				$myTsModel->deleteWeekProjectStatus($sessionData->id,$deleteProjectsData,$projId, $selYrMonArray[0], $selYrMonArray[1], $week);
			}
			//updateDayStatus($sessionData->id,$selYrMonArray[0], $selYrMonArray[1], $week);
		}
		$myTsModel->updateDayStatus($sessionData->id,$selYrMonArray[0], $selYrMonArray[1], $week);
		$myTsModel->updateWeekStatus($sessionData->id,$selYrMonArray[0], $selYrMonArray[1], $week);

		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();

		//		$usersModel = new Timemanagement_Model_Users();
		//		$storage = new Zend_Auth_Storage_Session();
		//        $data = $storage->read();
		//
		//        $selYrMon = $this->_getParam('selYrMon');
		//        $empDoj = $usersModel->getEmployeeDoj($data->id);
		//
		//    	$dateEmpDoj = date('Y-m',strtotime($empDoj['date_of_joining']));
		//
		//    	$selYrMon = ($selYrMon != '')?$selYrMon:$now->format('Y-m');
		//
		//		$month = $this->_getParam('month');
		//    	$year = $this->_getParam('year');
		//
		//    	$this->view->empDoj=$empDoj['date_of_joining'];
		//    	//$empid = $this->_getParam('empid');
		//$msgArray['status'] = 'success';
		$this->view->status = 'success';
		$this->view->message = 'Timesheet saved successfully.';
		//$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Time sheet saved successfully."));
		}
	}

	public function submitAction() {

		$storage = new Zend_Auth_Storage_Session();
		$sessionData = $storage->read();
		$myTsModel = new Timemanagement_Model_MyTimesheet();

		$selDay = $this->_getParam('day');
		$selYrMon = $this->_getParam('selYrMon');
		$callval = $this->getRequest()->getParam('call');
		$week = $this->_getParam('week');
		$calWeek = $this->_getParam('calWeek');
		$yrMon = explode('-', $selYrMon);
		if($selDay != '') {

			$calWeek = strftime('%U',strtotime($selYrMon.'-'.$selDay));
			$startCalWeek = strftime('%U',strtotime($selYrMon.'-01'));
			$week = ($calWeek- $startCalWeek) +1;
			$dayName = strtolower(date('D', strtotime($selYrMon.'-'.$selDay)));
		}
		$statusData = array('emp_id'=>$sessionData->id,
						  'ts_year'=>$yrMon[0],
						  'ts_month'=>$yrMon[1],
						  'ts_week'=>$week,
						  'cal_week'=>$calWeek,
						  'week_status'=>'submitted',
						  'modified_by' => $sessionData->id,
						  'modified'=> Zend_Registry::get('currentdate')				 	  
		);
		if($selDay != '') {
			//$statusData[$dayName.'_project_status']= 'submitted';
			$statusData[$dayName.'_status']= 'submitted';
			$statusData[$dayName.'_status_date'] = Zend_Registry::get('currentdate');
		} else {
			$weekDaysStatus =  $myTsModel->getWeekDaysDailyStatus($sessionData->id,$yrMon[0],$yrMon[1],$week);
			//print_r($weekDaysStatus);
			foreach ($weekDaysStatus[0] as $key => $value) {				
				if($value == 'saved') {
					//echo $key."   ".$value; 
					$dayName =  substr($key,0,3);
					//$statusData[$dayName.'_project_status']= 'submitted';
					$statusData[$dayName.'_status']= 'submitted';
					$statusData[$dayName.'_status_date'] = Zend_Registry::get('currentdate');
				}
			}			
		}
		$weekDaysProjStatus =  $myTsModel->getWeekDaysProjStatus($sessionData->id,$yrMon[0],$yrMon[1],$week);
		//	foreach ($weekDaysProjStatus[0] as $key => $value) {
		
		$projId = '';		
		foreach ($weekDaysProjStatus as $projStatusArray) {
			$projStatusData = array();					 			
			foreach ($projStatusArray as $key => $value) {				
				if($key == 'project_id') $projId = $value;
				if($value == 'saved') {
					$dayShortName =  substr($key,0,3);				
					if($selDay != '') {	
						$projStatusData[$dayName.'_project_status']= 'submitted';
					} else {									 
						$projStatusData[$dayShortName.'_project_status']= 'submitted';
					}
					$myTsModel->updateProjectSubmitStatus($sessionData->id,$yrMon[0], $yrMon[1], $week,$projStatusData,$projId);
				}	
			}
		}
	//	print_r($statusData); 
	//	print_r($weekDaysProjStatus);	
	//exit;
		$myTsModel->updateSubmitStatus($sessionData->id,$yrMon[0], $yrMon[1], $week,$statusData);
		$myTsModel->updateWeekStatus($sessionData->id,$yrMon[0], $yrMon[1], $week);
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();

		$this->view->status = 'success';
		if($selDay != '')
		$this->view->message = 'Timesheet submited successfully for Day '.$selDay;
		else
		$this->view->message = 'Timesheet submited successfully for week '.$week;
	}
	/**
	 * this action is used to get states based on country id.
	 */
	public function eraseweekAction() {

		$storage = new Zend_Auth_Storage_Session();
		$sessionData = $storage->read();
		
		$usersModel = new Timemanagement_Model_Users();
		$data = $storage->read();
		$empDoj = $usersModel->getEmployeeDoj($sessionData->id);
		
		$doj = $empDoj['date_of_joining'];
		if($doj>date('Y-m-d'))
		{
			$this->view->status = 'failure';
		}
		else
		{

			$myTsModel = new Timemanagement_Model_MyTimesheet();

			$selYrMon = $this->_getParam('selYrMon');
			$callval = $this->getRequest()->getParam('call');
			$week = $this->_getParam('week');
			$calWeek = $this->_getParam('calWeek');
			$yrMon = explode('-', $selYrMon);

			$statusData = array('modified_by' => $sessionData->id,
							  'modified'=> Zend_Registry::get('currentdate')				 	  
			);
			$TasksData = array('modified_by'=>$sessionData->id,
							  'modified'=> Zend_Registry::get('currentdate') //gmdate("Y-m-d H:i:s")
			);
			$notesData = array('modified_by'=>$sessionData->id,
							  'modified'=> Zend_Registry::get('currentdate')//gmdate("Y-m-d H:i:s")
			);
			$weekDaysStatus =  $myTsModel->getWeekDaysDailyStatus($sessionData->id,$yrMon[0],$yrMon[1],$week);
			foreach ($weekDaysStatus[0] as $key => $value) {
				if($value == 'saved') {
					$dayName =  substr($key,0,3);
					$statusData[$dayName.'_project_status']= 'no_entry';
					$statusData[$dayName.'_status']= 'no_entry';
					$statusData[$dayName.'_status_date'] = Zend_Registry::get('currentdate');

					$TasksData[$dayName.'_duration']='00:00';
					$notesData[$dayName.'_note'] = '';
				}
			}
			//		print_r($TasksData);
			//		print_r($statusData);
			//		print_r($notesData);

			if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();

			$myTsModel->updateTimesheet($sessionData->id,$yrMon[0], $yrMon[1], $week,$TasksData);
			$myTsModel->updateTimesheetStatus($sessionData->id,$yrMon[0], $yrMon[1], $week,$statusData);
			$myTsModel->updateTimesheetNotes($sessionData->id,$yrMon[0], $yrMon[1], $week,$notesData);
			$myTsModel->updateWeekStatus($sessionData->id,$yrMon[0], $yrMon[1], $week);
			$myTsModel->updateWeekDuration($sessionData->id,$yrMon[0], $yrMon[1], $week);

			$this->view->status = 'success';
			$this->view->message = 'Timesheet erased successfully for week '.$week.'.';
		}
	}
	public function getstatesAction()
	{
		$cnval = $this->_getParam('cnval');
		$statesModel = new Timemanagement_Model_States();
		$statesData = $statesModel->getStatesByCountryId($cnval);

		$opt='<option value=\'\'>Select State</option>';
		foreach($statesData as $state)
		{
			$opt.="<option value='".$state['id']."'>".$state['state_name']."</option>";
		}
		$this->_helper->json(array('options'=>utf8_encode($opt)));
	}
	//get notification alerts
	public function getapprovedtimesheetAction()
	{
		$storage = new Zend_Auth_Storage_Session();
		$data = $storage->read();
		$usersModel = new Timemanagement_Model_Users();
		$approvedAlert = $usersModel->getEmpApprovalStatusDteails($data->id);
		$this->view->approvalAlertData =  $approvedAlert;
	}
	//update notification alert as closed
	public function closeapprovealertAction()
	{
		$usersModel = new Timemanagement_Model_Users();
		$storage = new Zend_Auth_Storage_Session();
		$data = $storage->read();
		$emp_approval_status = $usersModel->addOrUpdateTstatusData($data->id);
	}
	public function converdateAction()
	{
		$selYrMon = $this->_getParam('day');
		$callval = $this->_getParam('yearmonth');
		$date = sapp_Global::change_date($callval,'view');
		$this->_helper->json(array('conerteddate'=>$date));
	}


}

