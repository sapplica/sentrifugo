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

class Default_OncallrequestController extends Zend_Controller_Action
{

    private $options;
	public function preDispatch()
	{

			 $ajaxContext = $this->_helper->getHelper('AjaxContext');
			 $ajaxContext->addActionContext('gethalfdaydetails', 'json')->initContext();
			 $ajaxContext->addActionContext('saveoncallrequestdetails', 'json')->initContext();
			 $ajaxContext->addActionContext('updateoncalldetails', 'json')->initContext();

	}

    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();

    }

	public function indexAction()
    {
	    $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginUserdepartment_id = $auth->getStorage()->read()->department_id;
		}
		$oncallrequestform = new Default_Form_oncallrequest();
		$oncallrequestform->setAttrib('action',BASE_URL.'oncallrequest');
		$oncallrequestmodel = new Default_Model_Oncallrequest();
		$employeeoncalltypemodel = new Default_Model_Employeeoncalltypes();
		$oncallmanagementmodel = new Default_Model_Oncallmanagement();
		$usersmodel = new Default_Model_Users();
		$employeesmodel = new Default_Model_Employees();
		$weekdaysmodel = new Default_Model_Weekdays();
		$holidaydatesmodel = new Default_Model_Holidaydates();
		$msgarray = array();
		$dateofjoiningArr = array();
		$holidayDateslistArr = array();
		$rMngr = 'No';
		$availableoncalls = '';
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
		$managerrequestdetails = '';
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
						$weekendDatailsArr = $oncallmanagementmodel->getWeekendDetails($employeeDepartmentId);
                        $employeeemail = $loggedinEmpId[0]['emailaddress'];
						$userfullname = $loggedinEmpId[0]['userfullname'];
						$businessunitid = $loggedInEmployeeDetails[0]['businessunit_id'];
                        if(!empty($reportingManagerDetails))
						{
							$oncallrequestform->rep_mang_id->setValue($reportingManagerDetails[0]['userfullname']);
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
						   $msgarray['from_date'] = 'On call management options are not configured yet.';
						   $msgarray['to_date'] = 'On call management options are not configured yet.';
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
							$oncallrequestdetails = $oncallrequestmodel->getUserApprovedOrPendingOncallsData($loginUserId);
						/* Start -For Checking if logged in user is reporting manager */
						$isReportingManager = $employeesmodel->CheckIfReportingManager($loginUserId);
						if(!empty($isReportingManager) && $isReportingManager[0]['count']>0) {
							if($searchRepFlag=='true')
								$managerrequestdetails = $oncallrequestmodel->getManagerApprovedOrPendingOncallsData($loginUserId);
							$isReportingManagerFlag = 'true';
						}
						/* End */
						/* Start -For Checking if logged in user is hr manager for thar particular department*/

						//get hr_id from oncallmanagemnt table based on login user dept id
						$configure_hr_id=$oncallmanagementmodel->gethrDetails($loginUserdepartment_id);
						if(!empty($configure_hr_id))
						{
						  if($configure_hr_id[0]['hr_id'] == $loginUserId)
						  {
							  	if($searchRepFlag=='true')
								$managerrequestdetails = $oncallrequestmodel->getHrApprovedOrPendingOncallsData($loginUserId);
								$isReportingManagerFlag = 'true';
						  }
						}
						/* End */
						$this->view->userfullname = $userfullname;
						$this->view->loggedinEmpId = $loggedinEmpId;
						$this->view->weekendDatailsArr = $weekendDatailsArr;
						$this->view->reportingManagerDetails = $reportingManagerDetails;
						$this->view->rMngr = $rMngr;
						$this->view->hremailgroup = $hremailgroup;
						$this->view->dateofjoiningArr = $dateofjoiningArr;
						$this->view->oncallrequestdetails = !empty($oncallrequestdetails)?$oncallrequestdetails:array();
						$this->view->holidayDateslistArr = $holidayDateslistArr;
						$this->view->managerrequestdetails = !empty($managerrequestdetails)?$managerrequestdetails:array();
						$this->view->isReportingManagerFlag = $isReportingManagerFlag;
						$this->view->searchRepFlag = $searchRepFlag;
						$this->view->searchMeFlag = $searchMeFlag;
					}
                    else
					{
					   $msgarray['rep_mang_id'] = 'Reporting manager is not assigned yet. Please contact your HR.';
					   $msgarray['from_date'] = 'On call management options are not configured yet.';
					   $msgarray['to_date'] = 'On call management options are not configured yet.';
					}
			}
		/* End */

		/*
		 Start
		 Query to fetch and build multioption for Oncalltype dropdown
		*/
		$oncalltype = $employeeoncalltypemodel->getactiveoncalltype();
   		if(!empty($oncalltype))
		    {
				if(sizeof($oncalltype) > 0)
				{
					foreach ($oncalltype as $oncalltyperes){
						$oncallrequestform->oncalltypeid->addMultiOption($oncalltyperes['id'].'!@#'.$oncalltyperes['numberofdays'].'!@#'.$oncalltyperes['oncalltype'],$oncalltyperes['oncalltype']);
					}
				}
			}
		else
			{
				$msgarray['oncalltypeid'] = ' On call types are not configured yet.';
			}
			$this->view->oncalltype = $oncalltype;
		/* End */

		/*
		START
		Query to get the number of available oncalls for the employee
		*/
      	   $getavailbaleoncalls = $oncallrequestmodel->getAvailableOncalls($loginUserId);
		     if(!empty($getavailbaleoncalls))
			   {
			    $oncallrequestform->no_of_days->setValue($getavailbaleoncalls[0]['remainingoncalls']);
				$availableoncalls = $getavailbaleoncalls[0]['remainingoncalls'];
		       }
			   else
				{
				   $msgarray['no_of_days'] = 'You have not been allotted on call for this financial year. Please contact your HR.';
				}
			$this->view->getavailbaleoncalls = $getavailbaleoncalls;
	    /* END */


		$this->view->form = $oncallrequestform;
		$this->view->msgarray = $msgarray;
		$this->view->loginUserId = $loginUserId;

		if($this->getRequest()->getPost() && empty($filter)){
				$result = $this->saveoncallrequest($oncallrequestform,$availableoncalls,$rep_mang_id,$employeeemail,$reportingManageremail,$week_startday,$week_endday,$ishalf_day,$userfullname,$reportingmanagerName,$businessunitid);
				$this->view->msgarray = $result;
			}

		$this->view->messages = $this->_helper->flashMessenger->getMessages();
    }

	public function saveoncallrequestdetailsAction()
	{
	  $this->_helper->layout->disableLayout();
	  $auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		}
		$constantday = '';
		$days = '';
		$errorflag = 'true';
		$msgarray = array();
		$oncalltypecount = '';
		$oncalldayArr = array(1,2);
		$availableoncalls = '';
		$rep_mang_id = '';
		$employeeemail = '';
		$reportingManageremail = '';
		$week_startday = '';
		$week_endday = '';
		$ishalf_day = '';
		$userfullname = '';
		$reportingmanagerName = '';
		$businessunitid = '';
		$dateofjoining = '';
		$hremailgroup = '';
		$employeeDepartmentId = '';
		$reportingmanagerId = '';
		$oncalltypeArr = array();
		$oncallrequestform = new Default_Form_oncallrequest();
		$oncallrequestmodel = new Default_Model_Oncallrequest();
		$employeeoncalltypesmodel = new Default_Model_Employeeoncalltypes();
		$oncallmanagementmodel = new Default_Model_Oncallmanagement();
		$usersmodel = new Default_Model_Users();
		$employeesmodel = new Default_Model_Employees();
		$weekdaysmodel = new Default_Model_Weekdays();
		 if($loginUserId !='' && $loginUserId != NULL)
			{
				$loggedinEmpId = $usersmodel->getUserDetailsByID($loginUserId);
				$loggedInEmployeeDetails = $employeesmodel->getLoggedInEmployeeDetails($loginUserId);

				if(!empty($loggedInEmployeeDetails))
					{

						$reportingmanagerId = $loggedInEmployeeDetails[0]['reporting_manager'];
						$employeeDepartmentId = $loggedInEmployeeDetails[0]['department_id'];
						$employeeEmploymentStatusId = $loggedInEmployeeDetails[0]['emp_status_id'];
						$businessunitid = $loggedInEmployeeDetails[0]['businessunit_id'];
						$dateofjoining = $loggedInEmployeeDetails[0]['date_of_joining'];

						if($reportingmanagerId !='' && $reportingmanagerId != NULL)
						 $reportingManagerDetails = $usersmodel->getUserDetailsByID($reportingmanagerId);

						if($employeeDepartmentId !='' && $employeeDepartmentId != NULL)
						 $weekendDatailsArr = $oncallmanagementmodel->getWeekendDetails($employeeDepartmentId);
                        $employeeemail = $loggedinEmpId[0]['emailaddress'];
						$userfullname = $loggedinEmpId[0]['userfullname'];

                        if(!empty($reportingManagerDetails))
						{
							$oncallrequestform->rep_mang_id->setValue($reportingManagerDetails[0]['userfullname']);
							$reportingManageremail = $reportingManagerDetails[0]['emailaddress'];
							$reportingmanagerName = $reportingManagerDetails[0]['userfullname'];
							$rep_mang_id = $reportingManagerDetails[0]['id'];
							$rMngr = 'Yes';
						}
						else
						{
						   $msgarray['rep_mang_id'] = 'Reporting manager is not assigned yet. Please contact your HR.';
						   $errorflag = 'false';
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
						   $msgarray['from_date'] = 'On call management options are not configured yet.';
						   $msgarray['to_date'] = 'On call management options are not configured yet.';
						   $errorflag = 'false';
						}

					}
                    else
					{
					   $errorflag = 'false';
					   $msgarray['rep_mang_id'] = 'Reporting manager is not assigned yet. Please contact your HR.';
					   $msgarray['from_date'] = 'On call management options are not configured yet.';
					   $msgarray['to_date'] = 'On call management options are not configured yet.';
					}
			}

			/*START- Validating if employee has been allotted oncalls
			  Validating if employee has not been assigned any oncalls
			*/
			$getavailbaleoncalls = $oncallrequestmodel->getAvailableOncalls($loginUserId);

			 if(!empty($getavailbaleoncalls))
			   {
				$availableoncalls = $getavailbaleoncalls[0]['remainingoncalls'];
			   }
			   else
				{
				   $errorflag = 'false';
				   $msgarray['no_of_days'] = 'You have not been allotted on call for this financial year. Please contact your HR';
				}
			/*
			  END- Validating if employee has been allotted oncalls
			*/


		$id = $this->_request->getParam('id');
		$reason = $this->_request->getParam('reason'); // reason
		$oncalltypeparam = $this->_request->getParam('oncalltypeid');
		if(isset($oncalltypeparam) && $oncalltypeparam !='')
		{
			$oncalltypeArr = explode("!@#",$this->_request->getParam('oncalltypeid'));
			$oncalltypeid = $oncalltypeArr[0];
			$oncalltypeArr = $employeeoncalltypesmodel->getOncalltypeDataByID($oncalltypeid);
		}

		/*
		   START- Oncall Type Validation
		   Server side validation for oncalltype count based on user selection.
		   This is to validate or negate if user manipulates the data in the browser or firebug.
		*/
		if(!empty($oncalltypeArr))
		{
		   $oncalltypecount = $oncalltypeArr[0]['numberofdays'];
		   $oncalltypetext = $oncalltypeArr[0]['oncalltype'];
		}
		else
		{
		   if(isset($oncalltypeparam) && $oncalltypeparam !='')
		    {
			   $msgarray['oncalltypeid'] = 'Wrong inputs given.';
			   $errorflag = 'false';
			}
			else if($oncalltypeparam =='')
			{
				$msgarray['oncalltypeid'] = 'Please select on call type.';
			    $errorflag = 'false';
			}
			else
			{
				$msgarray['oncalltypeid'] = 'On call types are not configured yet.';
			    $errorflag = 'false';
			}
		}

		/*
		   END- Oncall Type Validation
		*/

		$oncallday = $this->_request->getParam('oncallday');
		/*
		   START- Oncall Day Validation
		   Server side validation for halfday and full day based on user selection.
		   This is to validate or negate if user manipulates the data in the browser or firebug.
		*/
		if (!in_array($oncallday, $oncalldayArr))
		{
		   $msgarray['oncallday'] = 'Wrong inputs given.';
		   $errorflag = 'false';
		}

		/*
		   END- Oncall Day Validation
		*/

		$from_date = $this->_request->getParam('from_date');
		$from_date = sapp_Global::change_date($from_date,'database');

		$to_date = $this->_request->getParam('to_date');
		$to_date = sapp_Global::change_date($to_date,'database');

		$appliedoncallsdaycount = $this->_request->getParam('appliedoncallsdaycount'); // no of oncalls applied


        /*
		   START- Day calculation and validations.
		   I. Calculation of days based on start date and end data.
		   II. Also checking whether Applied no of days is less than oncalltype configuration.
		   III. Also If oncallday is selected as full day then making todata as manadatory and
		        if oncall day is selected as half day then no mandatory validation for todate.
		*/
				if($from_date != '' && $to_date !='' && $oncalltypecount !='')
				{
					$days = $this->calculatebusinessdaysoncall($from_date,$to_date);
					if(is_numeric($days) && $oncalltypecount >= $days)
					{
					}
					else
					{
					  if(!is_numeric($days))
						{
							$msgarray['to_date'] = 'To date should be greater than from date.';
							$errorflag = 'false';
						}
					  else
						{
						   $msgarray['to_date'] = $oncalltypetext." permits maximum of ".$oncalltypecount." on call days per request.";
						   $errorflag = 'false';
						}
					}

				}else
				{
				    if($oncallday == 1)
				    {
					   if($to_date == '' && !empty($weekendDatailsArr))
					   {
						 $msgarray['to_date'] = "Please select date.";
						 $errorflag = 'false';
					   }
                    }
				}

		/*
		    END- Day calculation and validations.
        */

		/*
		    START- Validating Half day requests based on Oncall management options
		    Validation for half day oncalls.
		    If halfday oncall is configure in oncall management options then only half day oncall can be applied.
	    */
		if($ishalf_day == 2)
		{
		   if($oncallday == 2)
		   {
		    $errorflag = 'false';
			$msgarray['oncallday'] = 'Half day on call cannot be applied.';
		   }

		}

		/*
		    END- Validating Half day requests based on Oncall management options
		*/

		/*
		   START- Validating if oncall request has been previoulsy applied
		   I.Validating from and to dates to check whether previously
		   any oncall has been raised with the same dates.
		   II.If full day oncall is applied then fromdate and todate are passed as parameter to query.
		   III.If half day oncall is applied then fromdate and fromdate are passed as a parameter to query.
		*/

		$userAppliedOncalls = $oncallrequestmodel->getUsersAppliedOncalls($loginUserId);
		if(!empty($userAppliedOncalls)) {
				foreach($userAppliedOncalls as $oncall) {
					if($oncallday == 1)
						$oncallsDateExists = $oncallrequestmodel->checkOncallExists($oncall['from_date'],$oncall['to_date'],$from_date, $to_date, $loginUserId);
					else
						$oncallsDateExists = $oncallrequestmodel->checkOncallExists($oncall['from_date'],$oncall['to_date'],$from_date, $from_date, $loginUserId);
					if($oncallsDateExists[0]['oncallexist'] > 0)
					{
					   $errorflag = 'false';
					   $msgarray['from_date'] = ' On call has already been applied for the above dates.';
					   break;
					}
				}

		}

		/*
		  END- Validating if oncall request has been previoulsy applied
		*/

		/* START Validating whether applied date is prior to date of joining */
		if($dateofjoining >= $from_date && $from_date!='')
		{
			$errorflag = 'false';
			$msgarray['from_date'] = ' On call cannot be applied before date of joining.';
		}
		/* End */
		else
		{    
		    $date1 = date_parse_from_format("Y-m-d", $from_date);
		    $date2 = date_parse_from_format("Y-m-d", $to_date);
		    $month1 = $date1["month"];
		    $month2 = $date2["month"];

		    if($month1 != $month2)
		    {
			    $errorflag = 'false';
			    $msgarray['from_date'] = ' On call for different months must be requested separately.';
		    }
		}

		if($oncallday == 2)
		 $appliedoncallscount =  0.5;
		else if($oncallday == 1)
		 $appliedoncallscount = ($days !=''?$days:$appliedoncallsdaycount);


		//get hr_id from oncallmanagemnt table based on login user dept id
	    $configure_hr_id=$oncallmanagementmodel->gethrDetails($employeeDepartmentId);
		if(!empty($configure_hr_id))
		{
		  $hr_id=$configure_hr_id[0]['hr_id'];
		}

		if($this->getRequest()->getPost())
		{
		if($oncallrequestform->isValid($this->_request->getPost()) && $errorflag == 'true')
		    {

				$date = new Zend_Date();
				$actionflag = '';
				$tableid  = '';
				   $data = array('user_id'=>$loginUserId,
				                 'reason'=>$reason,
				                 'oncalltypeid'=>$oncalltypeid,
				                 'oncallday'=>$oncallday,
								 'from_date'=>$from_date,
								 'to_date'=>  ($to_date !='')?$to_date:$from_date,
								 'oncallstatus'=>1,
								 'rep_mang_id'=>$rep_mang_id,
								'hr_id'=>$hr_id,
				      			 'no_of_days'=>($availableoncalls>=0)?$availableoncalls:0,
								 'appliedoncallscount'=>$appliedoncallscount,
								 'modifiedby'=>$loginUserId,
								 'modifieddate'=>gmdate("Y-m-d H:i:s")
						);
					if($id!=''){
						$where = array('id=?'=>$id);
						$actionflag = 2;
					}
					else
					{
					    $data['createdby'] = $loginUserId;
						$data['createddate'] = gmdate("Y-m-d H:i:s");
						$data['isactive'] = 1;
						$where = '';
						$actionflag = 1;
					}
					$Id = $oncallrequestmodel->SaveorUpdateOncallRequest($data, $where);
					/**
					oncall request history
					**/
					if($Id != 'update')
					{
						$history = 'On call Request has been sent for Manager Approval by ';
						 $oncallrequesthistory_model = new Default_Model_Oncallrequesthistory();
						 $oncall_history = array(
										'oncallrequest_id' =>$Id,
										'description' => $history,
										//'emp_name' =>  ucfirst($auth->getStorage()->read()->userfullname),
										'createdby' =>$loginUserId,
										'modifiedby' =>$loginUserId,
										'isactive' => 1,
										'createddate' =>gmdate("Y-m-d H:i:s"),
										'modifieddate'=> gmdate("Y-m-d H:i:s"),
									);
					      $where = '';
						$oncallhistory = $oncallrequesthistory_model->saveOrUpdateOncallRequestHistory($oncall_history,$where);
					}
					if($Id == 'update')
					{
					   $tableid = $id;
					   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"On call request updated successfully."));
					}
					else
					{
                       $tableid = $Id;
                       $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"On call request added successfully."));
                            /** MAILING CODE **/
							//$hremail = explode(",",HREMAIL);
							/* Mail to Reporting manager */
							if($to_date == '' || $to_date == NULL)
							$to_date = $from_date;

							$toemailArr = $reportingManageremail; //$employeeemail
							if(!empty($toemailArr))
							{
								$options['subject'] = 'On call request for approval';
								$options['header'] = 'On Call Request';
								$options['toEmail'] = $toemailArr;
								$options['toName'] = $reportingmanagerName;
								$options['message'] = '<div>
												<div>Dear '.$reportingmanagerName.',</div>
												<div>The on call of the below employee is pending for approval:</div>
												<div>
                <table width="100%" cellspacing="0" cellpadding="15" border="0" style="border:3px solid #BBBBBB; font-size:16px; font-family:Arial, Helvetica, sans-serif; margin:30px 0 30px 0;" bgcolor="#ffffff">
                      <tbody><tr>
                        <td width="28%" style="border-right:2px solid #BBBBBB;">Employee Name</td>
                        <td width="72%">'.$userfullname.'</td>
                      </tr>
                      <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">No. of Day(s)</td>
                        <td>'.$appliedoncallscount.'</td>
                      </tr>
                      <tr>
                        <td style="border-right:2px solid #BBBBBB;">Remaining on call</td>
                        <td>'.$availableoncalls.'</td>
                      </tr>
                      <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">From</td>
                        <td>'.$from_date.'</td>
                      </tr>
                      <tr>
                        <td style="border-right:2px solid #BBBBBB;">To</td>
                        <td>'.$to_date.'</td>
                  </tr>
                      <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">Reason for on call</td>
                        <td>'.$reason.'</td>
                  </tr>
                  <tr>
                        <td style="border-right:2px solid #BBBBBB;">Reporting Manager</td>
                        <td>'.$reportingmanagerName.'</td>
                  </tr>
                </tbody></table>

            </div>
            <div style="padding:20px 0 10px 0;">Please <a href="'.BASE_URL.'/index/popup" target="_blank" style="color:#b3512f;">click here</a> to login and check the on call details.</div>
            </div>';
                                $result = sapp_Global::_sendEmail($options);
							}
							/* END */
							/* Mail to HR */
                            if (defined('LV_HR_'.$businessunitid) && $businessunitid !='')
						    {
							    $options['subject'] = 'On call request for approval';
								$options['header'] = 'On call Request ';
								$options['toEmail'] = constant('LV_HR_'.$businessunitid);
								$options['toName'] = 'On call management';
								$options['message'] = '<div>
												<div>Dear HR,</div>
												<div>The on call of the below employee is pending for approval:</div>
<div>
                <table width="100%" cellspacing="0" cellpadding="15" border="0" style="border:3px solid #BBBBBB; font-size:16px; font-family:Arial, Helvetica, sans-serif; margin:30px 0 30px 0;" bgcolor="#ffffff">
                      <tbody><tr>
                        <td width="28%" style="border-right:2px solid #BBBBBB;">Employee Name</td>
                        <td width="72%">'.$userfullname.'</td>
                      </tr>
                      <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">No. of Day(s)</td>
                        <td>'.$appliedoncallscount.'</td>
                      </tr>
                      <tr>
                        <td style="border-right:2px solid #BBBBBB;">Remaining on call</td>
                        <td>'.$availableoncalls.'</td>
                      </tr>
                      <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">From</td>
                        <td>'.$from_date.'</td>
                      </tr>
                      <tr>
                        <td style="border-right:2px solid #BBBBBB;">To</td>
                        <td>'.$to_date.'</td>
                  </tr>
                      <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">Reason for on call</td>
                        <td>'.$reason.'</td>
                  </tr>
                  <tr>
                        <td style="border-right:2px solid #BBBBBB;">Reporting Manager</td>
                        <td>'.$reportingmanagerName.'</td>
                  </tr>
                </tbody></table>

            </div>
            <div style="padding:20px 0 10px 0;">Please <a href="'.BASE_URL.'/index/popup" target="_blank" style="color:#b3512f;">click here</a> to login and check the on call details.</div>
            </div>';
								//$options['cron'] = 'yes';
                                $result = sapp_Global::_sendEmail($options);

							}

							/* END */
							/* Mail to the applied employee*/
								$toemailArr = $employeeemail;
								$options['subject'] = 'On call request for approval';
								$options['header'] = 'On call Request';
								$options['toEmail'] = $toemailArr;
								$options['toName'] = $userfullname;
								$options['message'] = '<div>
												<div>Hi,</div>
												<div>A on call request raised by you is sent for your managers approval.</div>
<div>
                <table width="100%" cellspacing="0" cellpadding="15" border="0" style="border:3px solid #BBBBBB; font-size:16px; font-family:Arial, Helvetica, sans-serif; margin:30px 0 30px 0;" bgcolor="#ffffff">
                      <tbody><tr>
                        <td width="28%" style="border-right:2px solid #BBBBBB;">Employee Name</td>
                        <td width="72%">'.$userfullname.'</td>
                      </tr>
                      <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">No. of Day(s)</td>
                        <td>'.$appliedoncallscount.'</td>
                      </tr>
                      <tr>
                        <td style="border-right:2px solid #BBBBBB;">From</td>
                        <td>'.$from_date.'</td>
                      </tr>
                      <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">To</td>
                        <td>'.$to_date.'</td>
            	     </tr>
	    	          <tr>
    	                 <td style="border-right:2px solid #BBBBBB;">On Call Type</td>
                        <td>'.$oncalltypetext.'</td>
                  </tr>
                      <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">Reason for on call</td>
                        <td>'.$reason.'</td>
                  </tr>
                </tbody></table>

            </div>
            <div style="padding:20px 0 10px 0;">Please <a href="'.BASE_URL.'/index/popup" target="_blank" style="color:#b3512f;">click here</a> to login and check the on call details.</div>
            </div>';
                                $result = sapp_Global::_sendEmail($options);

					}
					$menuID = ONCALLREQUEST;
					$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
                    $this->_helper->json(array('result'=>'saved',
												'message'=>'On call request applied successfully.',
												'controller'=>'pendingoncalls'
										));
			}
			else
			{
     			$messages = $oncallrequestform->getMessages();
				if(isset($msgarray['rep_mang_id'])){

    				$messages['rep_mang_id']= array($msgarray['rep_mang_id']);
    			}
				if(isset($msgarray['from_date'])){

    				$messages['from_date']= array($msgarray['from_date']);
    			}
				if(isset($msgarray['to_date'])){

    				$messages['to_date']= array($msgarray['to_date']);
    			}
				if(isset($msgarray['oncallday'])){

    				$messages['oncallday']= array($msgarray['oncallday']);
    			}
				if(isset($msgarray['oncalltypeid'])){

    				$messages['oncalltypeid']= array($msgarray['oncalltypeid']);
    			}
				if(isset($msgarray['no_of_days'])){

    				$messages['no_of_days']= array($msgarray['no_of_days']);
    			}
    			$messages['result']='error';
    			$this->_helper->json($messages);

			}
		}
	}

	function calcBusinessDays($dDate1,$dDate2,$constantday)
	{
	   $iWeeks = '';
	   $iDateDiff = '';
	   $iAdjust = 0;
        if (strtotime($dDate2) - strtotime($dDate1) < 0)
		{
		  return "From date should be less than To date"; // error code if dates transposed
		}
      	 $iWeekday1 = date("j", strtotime($dDate1));
		 $iWeekday2 = date("j", strtotime($dDate2));
        $iWeekday1 = ($iWeekday1 == 0) ? 7 : $iWeekday1; // change Sunday from 0 to 7
        $iWeekday2 = ($iWeekday2 == 0) ? 7 : $iWeekday2;
        if (($iWeekday1 > $constantday) && ($iWeekday2 > $constantday))
		 $iAdjust = 1; // adjustment if both days on weekend

        $iWeekday1 = ($iWeekday1 > $constantday) ? $constantday : $iWeekday1; // only count weekdays
        $iWeekday2 = ($iWeekday2 > $constantday) ? $constantday : $iWeekday2;

        // calculate differnece in weeks ( 60sec * 60min * 24hrs * 7 days = 604800)

        $iWeeks = floor((strtotime($dDate2) - strtotime($dDate1)) / 604800);

        if ($iWeekday1 <= $iWeekday2) {
          $iDateDiff = ($iWeeks * $constantday) + ($iWeekday2 - $iWeekday1);
        }
		else
		{
          $iDateDiff = (($iWeeks + 1) * $constantday) - ($iWeekday1 - $iWeekday2);
        }

        $iDateDiff -= $iAdjust; // take into account both days on weekend

        return ($iDateDiff + 1); // add 1 because dates are inclusive

	}

	public function calculatebusinessdaysoncall($fromDate,$toDate)
	{
	    $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}

		$noOfDays =0;
		$weekDay='';
		$employeeDepartmentId = '';
		$employeeGroupId = '';
		$weekend1 = '';
	    $weekend2 = '';
		$holidayDatesArr = array();
			//Calculating the no of days in b/w from date & to date with out taking weekend & holidays....
			$employeesmodel = new Default_Model_Employees();
			$oncallmanagementmodel = new Default_Model_Oncallmanagement();
			$holidaydatesmodel = new Default_Model_Holidaydates();


			$loggedInEmployeeDetails = $employeesmodel->getLoggedInEmployeeDetails($loginUserId);
			if(!empty($loggedInEmployeeDetails))
			{
				$employeeDepartmentId = $loggedInEmployeeDetails[0]['department_id'];
				$employeeGroupId = $loggedInEmployeeDetails[0]['holiday_group'];

				if($employeeDepartmentId !='' && $employeeDepartmentId != NULL)
				 $weekendDetailsArr = $oncallmanagementmodel->getWeekendNamesDetails($employeeDepartmentId);

				if(!empty($weekendDetailsArr))
				{
					if($weekendDetailsArr[0]['is_skipholidays'] == 1 && isset($employeeGroupId) && $employeeGroupId !='')
					{
					  $holidayDateslistArr = $holidaydatesmodel->getHolidayDatesListForGroup($employeeGroupId);
					  if(!empty($holidayDateslistArr))
					  {
						  for($i=0;$i<sizeof($holidayDateslistArr);$i++)
						   {
							  $holidayDatesArr[$i] = $holidayDateslistArr[$i]['holidaydate'];
						   }
					  }
					}
						$weekend1 = $weekendDetailsArr[0]['daystartname'];
						$weekend2 = $weekendDetailsArr[0]['dayendname'];
				}


				$fromdate_obj = new DateTime($fromDate);
				$weekDay = $fromdate_obj->format('l');
				while($fromDate <= $toDate)
				{
					$noOfDays++;
					$fromdate_obj->add(new DateInterval('P1D'));	//Increment from date by one day...
					$fromDate = $fromdate_obj->format('Y-m-d');
					$weekDay = $fromdate_obj->format('l');
				}
			}

		return $noOfDays;

	}


	function gethalfdaydetailsAction()
	{
	    $this->_helper->layout->disableLayout();
		$result['result'] = '';
	    $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$employeesmodel = new Default_Model_Employees();
		$oncallmanagementmodel = new Default_Model_Oncallmanagement();
		$loggedInEmployeeDetails = $employeesmodel->getLoggedInEmployeeDetails($loginUserId);
			$ishalf_day='';
		if(!empty($loggedInEmployeeDetails))
			{
			    $employeeDepartmentId = $loggedInEmployeeDetails[0]['department_id'];
  			    $weekendDatailsArr = $oncallmanagementmodel->getWeekendDetails($employeeDepartmentId);
				if(!empty($weekendDatailsArr))
				   $ishalf_day = $weekendDatailsArr[0]['is_halfday'];
				else
                   $ishalf_day = 'error';
			}
        $result['result'] =  $ishalf_day;
	    $this->_helper->_json($result);
	}

	public function editpopupAction()
	{
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->getRequest()->getParam('id');
		$userid = $this->getRequest()->getParam('unitId');
		if($id == '')
		$id = $loginUserId;

		$oncallrequestmodel = new Default_Model_Oncallrequest();
		$oncallrequestform = new Default_Form_oncallrequest();
		$user_logged_in = 'true';
		$manager_logged_in = 'false';
		$cancel_flag = 'true';
		$approve_flag = 'true';
		$reject_flag = 'true';
		if($id && is_numeric($id) && $id>0)
 		{
			$oncall_details = $oncallrequestmodel->getOncallDetails($id);
			if(!empty($oncall_details)) {
				$oncall_details = call_user_func_array('array_merge', $oncall_details);

				if($oncall_details['user_id']==$loginUserId) {
					if($oncall_details['oncallstatus']=='Approved') {
						if(isset($oncall_details['from_date'])) {
							$oncallDate = date($oncall_details['from_date']);
							$todayDate = date("Y-m-d");
							if(strtotime($todayDate)>=strtotime($oncallDate)) {
								$cancel_flag = 'false';
							}
						}
					}
					$approve_flag = 'false';
					$reject_flag = 'false';
				}

				if($oncall_details['rep_mang_id']==$loginUserId || $oncall_details['hr_id']==$loginUserId ) {
					if($oncall_details['oncallstatus']=='Approved') {
						$approve_flag = 'false';
						$reject_flag = 'false';
					}
					$manager_logged_in = 'true';
				}
			}

 		}
 		else
 		{
 			$this->view->rowexist = "norows";
 		}

		$this->view->form = $oncallrequestform;
		$this->view->controllername = 'oncallrequest';
		$this->view->oncall_details = $oncall_details;
		$this->view->user_logged_in = $user_logged_in;
		$this->view->manager_logged_in = $manager_logged_in;
		$this->view->cancel_flag = $cancel_flag;
		$this->view->approve_flag = $approve_flag;
		$this->view->reject_flag = $reject_flag;

	}

	public function updateoncalldetailsAction()
	{

		$this->_helper->layout->disableLayout();
		$result['result'] = 'success';
		$result['msg'] = '';
		$oncallstatus = '';
		$subject='';
		$message='';
		$successmsg='';
		$actionflag = 2;
		$user_logged_in = 'true';
		$manager_logged_in = 'false';
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}

		$id = $this->_request->getParam('id');
		$status = $this->_request->getParam('status');
		$comments = $this->_request->getParam('comments');
		$oncallrequestmodel = new Default_Model_Oncallrequest();
		$employeeoncalltypesmodel = new Default_Model_Employeeoncalltypes();
		$usersmodel = new Default_Model_Users();
		if($id && is_numeric($id) && $id>0)
 		{
 			$oncall_details = $oncallrequestmodel->getOncallDetails($id);
 			if(!empty($oncall_details)) {
				$oncall_details = call_user_func_array('array_merge', $oncall_details);
				$oncalltypeArr = $employeeoncalltypesmodel->getOncalltypeDataByID($oncall_details['oncalltypeid']);
				if($oncall_details['user_id']==$loginUserId) {
					if(sapp_Global::_decrypt($status)=='Cancel') {
						$oncallstatus = 4;
						if($oncall_details['oncallstatus']=='Approved') {
							if(!empty($oncalltypeArr)) {
								if($oncalltypeArr[0]['oncallpredeductable'] == 1) {
							  		$updateemployeeoncall = $oncallrequestmodel->updatecancelledemployeeoncalls($oncall_details['appliedoncallscount'],$oncall_details['user_id']);
							  	}
							}
						}
						$successmsg ='On call request cancelled succesfully.';
						$subject = 'On call request cancelled';
						$message = '<div>Hi,</div><div>The below on call has been cancelled.</div>';
					}
				}elseif($oncall_details['rep_mang_id']==$loginUserId || ($oncall_details['hr_id']==$loginUserId )) {
					if(sapp_Global::_decrypt($status)=='Cancel') {
						$oncallstatus = 4;
						if($oncall_details['oncallstatus']=='Approved') {
							if(!empty($oncalltypeArr)) {
								if($oncalltypeArr[0]['oncallpredeductable'] == 1) {
							  		$updateemployeeoncall = $oncallrequestmodel->updatecancelledemployeeoncalls($oncall_details['appliedoncallscount'],$oncall_details['user_id']);
							  	}
							}
						}
						$successmsg ='On call request cancelled succesfully.';
						$subject = 'On call request cancelled';
						$message = '<div>Hi,</div><div>The below on call has been cancelled.</div>';
					}elseif(sapp_Global::_decrypt($status)=='Approved'){
						$oncallstatus =2;
						if(!empty($oncalltypeArr)) {
							if($oncalltypeArr[0]['oncallpredeductable'] == 1) {
							  	$updateemployeeoncall = $oncallrequestmodel->updateemployeeoncalls($oncall_details['appliedoncallscount'],$oncall_details['user_id']);
							  }
						}
						$successmsg ='On call request approved succesfully.';
						$subject = 'On call request approved';
						$message = '<div>Hi,</div><div>The below on call has been approved.</div>';
					}elseif(sapp_Global::_decrypt($status)=='Rejected'){
						$oncallstatus = 3;
						$successmsg ='On call request rejected succesfully.';
						$subject = 'On call request rejected';
						$message = '<div>Hi,</div><div>The below on call has been rejected.</div>';
					}
					$manager_logged_in = 'true';
				}

				if(!empty($oncallstatus)) {
					$data = array( 'oncallstatus'=>$oncallstatus,
				   				  'approver_comments'=> !empty($comments)?$comments:NULL,
				                  'modifiedby'=>$loginUserId,
								  'modifieddate'=>gmdate("Y-m-d H:i:s")
						);
					$where = array('id=?'=>$id);
					$Id = $oncallrequestmodel->SaveorUpdateOncallRequest($data, $where);


					/**
					oncall request history
					**/
					if($Id == 'update')
					{
						$oncall_status=sapp_Global::_decrypt($status);
						if($oncall_status=='Approved')
						{
							$oncallstatus='Approved';
						}
						else if($oncall_status=='Rejected')
						{
							$oncallstatus='Rejected';
						}
						else
						{
							$oncallstatus='Cancelled';
						}
						$history = 'On call Request has been '.$oncallstatus.' by ';
						$oncallrequesthistory_model = new Default_Model_Oncallrequesthistory();
						$oncall_history = array(
										'oncallrequest_id' =>$id ,
										'description' => $history,
										'createdby' => $loginUserId,
										'modifiedby' => $loginUserId,
										'isactive' => 1,
										'createddate' =>gmdate("Y-m-d H:i:s"),
										'modifieddate'=>gmdate("Y-m-d H:i:s"),
									   );
					    $where = '';
						$oncallhistory = $oncallrequesthistory_model->saveOrUpdateOncallRequestHistory($oncall_history,$where);
					}


					$businessunitid = $oncall_details['bunit_id'];
					$userDetails = $usersmodel->getUserDetailsByID($oncall_details['user_id']);
					$employeename = $userDetails[0]['userfullname'];
					for($i=1;$i<=3;$i++) {
						$toEmail = '';
						$toName = '';
						if($i==1) {
							$userDetails = $usersmodel->getUserDetailsByID($oncall_details['user_id']);
							$toEmail = $userDetails[0]['emailaddress'];
							$toName = $userDetails[0]['userfullname'];
						}
						elseif($i==2) {
							$repManagerDetails = $usersmodel->getUserDetailsByID($oncall_details['rep_mang_id']);
							$toEmail = $repManagerDetails[0]['emailaddress'];
							$toName = $repManagerDetails[0]['userfullname'];
						}
						elseif($i==3) {
							if (defined('LV_HR_'.$businessunitid) && $businessunitid !='')
							{
							$toEmail = constant('LV_HR_'.$businessunitid);
							$toName = 'On call management';
							}
						}

						if($toEmail!='' && $toName!='') {
							$options['header'] = 'On call Request';
							$options['toEmail'] = $toEmail;
							$options['toName'] = $toName;
							$options['subject'] = $subject;
							$options['message'] = $message;
							$options['message'] .= '<div>
                			<table width="100%" cellspacing="0" cellpadding="15" border="0" style="border:3px solid #BBBBBB; font-size:16px; font-family:Arial, Helvetica, sans-serif; margin:30px 0 30px 0;" bgcolor="#ffffff">
	                      	<tbody>
		                      <tr>
		                        <td width="28%" style="border-right:2px solid #BBBBBB;">Employee Name</td>
		                        <td width="72%">'.$employeename.'</td>
		                      </tr>
		                      <tr bgcolor="#e9f6fc">
		                        <td style="border-right:2px solid #BBBBBB;">No. of Day(s)</td>
		                        <td>'.$oncall_details['appliedoncallscount'].'</td>
		                      </tr>
		                      <tr>
		                        <td style="border-right:2px solid #BBBBBB;">From</td>
		                        <td>'.$oncall_details['from_date'].'</td>
		                      </tr>
		                      <tr bgcolor="#e9f6fc">
		                        <td style="border-right:2px solid #BBBBBB;">To</td>
		                        <td>'.$oncall_details['to_date'].'</td>
		            	      </tr>
		                      <tr bgcolor="#e9f6fc">
		                        <td style="border-right:2px solid #BBBBBB;">Reason for on call</td>
		                        <td>'.$oncall_details['reason'].'</td>
	                  		  </tr>
                			</tbody>
                			</table>
							</div>
            				<div style="padding:20px 0 10px 0;">Please <a href="'.BASE_URL.'/index/popup" target="_blank" style="color:#b3512f;">click here</a> to login and check the on call details.</div>';
                            sapp_Global::_sendEmail($options);

						}
					}

					$menuID = ($manager_logged_in=='true')?MANAGEREMPLOYEEVACATIONS:PENDINGONCALLS;
					sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
					$result['msg'] = $successmsg;
				}
			}
 		}
 		else{
 			$result['result'] ='fail';
 			$result['msg'] = '';
 		}

		$this->_helper->json($result);
	}

}
