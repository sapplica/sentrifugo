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

class Default_ReportsController extends Zend_Controller_Action
{

	private $options;
	private $userlog_model;
	private $printable_interview_arr = array('interview_mode','interview_round_number','interview_round_name','created_by_name','interview_feedback','interview_comments','round_status');
	public function preDispatch()
	{

		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('empauto', 'json')->initContext();
		$ajaxContext->addActionContext('holidaygroupreports', 'html')->initContext();
		$ajaxContext->addActionContext('leavesreport', 'html')->initContext();
		$ajaxContext->addActionContext('leavemanagementreport', 'html')->initContext();
		$ajaxContext->addActionContext('businessunits', 'html')->initContext();
		$ajaxContext->addActionContext('departments', 'html')->initContext();
		$ajaxContext->addActionContext('getempreportdata', 'html')->initContext();
		$ajaxContext->addActionContext('leavesreporttabheader', 'json')->initContext();
		$ajaxContext->addActionContext('userlogreport', 'html')->initContext();
		$ajaxContext->addActionContext('activitylogreport', 'html')->initContext();
		$ajaxContext->addActionContext('getactiveuserdata', 'html')->initContext();
		$ajaxContext->addActionContext('rolesgroupdata', 'html')->initContext();
		$ajaxContext->addActionContext('emprolesgroupdata', 'html')->initContext();
		$ajaxContext->addActionContext('requisitonstatusreport', 'html')->initContext();
		$ajaxContext->addActionContext('agencylistreport', 'html')->initContext();
		$ajaxContext->addActionContext('getinterviewroundsdata', 'html')->initContext();
		$ajaxContext->addActionContext('empscreening', 'html')->initContext();		
	//	$ajaxContext->addActionContext('empauto', 'json')->initContext();
	//	$ajaxContext->addActionContext('holidaygroupreports', 'html')->initContext();
		//$ajaxContext->addActionContext('leavesreport', 'html')->initContext();
		//$ajaxContext->addActionContext('businessunits', 'html')->initContext();
		//$ajaxContext->addActionContext('departments', 'html')->initContext();
		//$ajaxContext->addActionContext('getempreportdata', 'html')->initContext();
		//$ajaxContext->addActionContext('leavesreporttabheader', 'json')->initContext();
		//$ajaxContext->addActionContext('userlogreport', 'html')->initContext();
		//$ajaxContext->addActionContext('activitylogreport', 'html')->initContext();
		//$ajaxContext->addActionContext('getactiveuserdata', 'html')->initContext();
		//$ajaxContext->addActionContext('rolesgroupdata', 'html')->initContext();
        $ajaxContext->addActionContext('getrolepopup', 'html')->initContext();
	//	$ajaxContext->addActionContext('emprolesgroupdata', 'html')->initContext();
	//	$ajaxContext->addActionContext('requisitonstatusreport', 'html')->initContext();
	//	$ajaxContext->addActionContext('agencylistreport', 'html')->initContext();
	//	$ajaxContext->addActionContext('getinterviewroundsdata', 'html')->initContext();
	//	$ajaxContext->addActionContext('empscreening', 'html')->initContext();
        $ajaxContext->addActionContext('emprolesgrouppopup', 'html')->initContext();
		$ajaxContext->addActionContext('getsddata', 'html')->initContext();
		$ajaxContext->addActionContext('getselectedappraisaldata','html')->initContext();
		$ajaxContext->addActionContext('performancereport', 'html')->initContext();
	}

	/**
	 * Init
	 *
	 * @see Zend_Controller_Action::init()
	 */
	public function init()
	{
            $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
            $this->userlog_model = new Default_Model_Userloginlog();
	}
        /**
         * This will use in groups and roles report when we click on role count.
         */
        public function getrolepopupAction()
        {
            $group_id = $this->_getParam('group_id',null);
            $sort_name = $this->_getParam('sort_name','rolename');
            $sort_type = $this->_getParam('sort_type','asc');
            Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
            
            $roles_model = new Default_Model_Roles();
            $role_data = $roles_model->getdata_for_rolesgroup_popup($group_id,$sort_name,$sort_type);
            
            $this->view->role_data = $role_data;
            $this->view->sort_name = $sort_name;
            $this->view->sort_type = $sort_type;
            $this->view->group_id = $group_id;
        }
        /**
         * This function is used in employee,group and roles for employees popup
         */
        public function emprolesgrouppopupAction()
        {
            $group_id = $this->_getParam('group_id',null);
            $role_id = $this->_getParam('role_id',null);
            $page_no = $this->_getParam('page_no',1);
            $sort_name = $this->_getParam('sort_name','userfullname');
            $sort_type = $this->_getParam('sort_type','asc');
            $per_page = $this->_getParam('per_page','asc');
            
            Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
            $emp_model = new Default_Model_Employee();
            $emp_data = $emp_model->emprolesgrouppopup($group_id,$role_id,$page_no,$sort_name,$sort_type,$per_page);
            $page_cnt = $emp_data['page_cnt'];
            $employee_data = $emp_data['rows'];
            
            $this->view->per_page = $per_page;
            $this->view->page_no = $page_no;
            $this->view->sort_name = $sort_name;
            $this->view->sort_type = $sort_type;
            $this->view->page_cnt = $page_cnt;
            $this->view->employee_data = $employee_data;
            if($group_id != USERS_GROUP)
            {
                $this->view->column_arr = $this->empreport_heplper1('mandatory');
            }
            else 
            {
                $this->view->column_arr = array(
                    'userfullname' => 'User',
                    'employeeId' => 'User ID',
                    'emailaddress' => 'Email',
                    'rolename_p' => 'Role',
                );
            }
            $this->view->group_id = $group_id;
            $this->view->role_id = $role_id;
        }
	/** 
	** performance appraisal reports
	** by default displays all employee ratings for the current year
	** parameterized by year, business unit, department, quarter, reporting manager 
	**/
	public function performancereportAction()
	{
		try{
			$form = new Default_Form_performancereport();
			$norec_arr = array();
			$dept_model = new Default_Model_Departments();
			$dept_data = $dept_model->getdepts_interview_report();
			$appraisalRatings_model = new Default_Model_Appraisalemployeeratings();
			$department = $this->_request->getParam('department_id');
			$businessunit = $this->_request->getParam('businessunit_id');
			$reportingmanager = $this->_request->getParam('reporting_manager');
			$fromyear = $this->_request->getParam('fromyear');
			$toyear = $this->_request->getParam('toyear');
			$employeeid = $this->_request->getParam('hiddenemployeename');
			$nofilters = 0;
			$appraisalData = array();
			$curdate = date('Y-m-d');
			//if all the filters are empty
			if($department == '' && $reportingmanager == '' && ($businessunit == '' || $businessunit == 0) && $fromyear == '' && $toyear == '' && $employeeid == '')
			{
				$fromyear = date('Y');
				$toyear = date('Y',strtotime("$curdate +1 year"));
				$appraisalData = $appraisalRatings_model->getappraisalidsforperformance($fromyear,$toyear,$department,$reportingmanager,$businessunit,$employeeid);
				//when no filters are there and if there is no data for current year, checking for previous year
				if(empty($appraisalData))
				{
					$fromyear = $fromyear-1;
					$toyear = $toyear-1;
					$appraisalData = $appraisalRatings_model->getappraisalidsforperformance($fromyear,$toyear,$department,$reportingmanager,$businessunit,$employeeid);
				}
			}
			else
			{
				$appraisalData = $appraisalRatings_model->getappraisalidsforperformance($fromyear,$toyear,$department,$reportingmanager,$businessunit,$employeeid);
			}
			//getting the employees count with consolidated rating group by consolidated rating
			$emp_count_data = $appraisalRatings_model->getEmpCountWithConsolidatedRatings($fromyear,$toyear,$department,$reportingmanager,$businessunit,$appraisalData,$employeeid);
			$maxrating = 0;
			$noofboxes = 0;
			$ratingsarray = array();
			if(isset($emp_count_data) && !empty($emp_count_data))
			{
				//making the array with rating as key
				foreach($emp_count_data as $emp_count)
				{
					$ratingsarray[$emp_count['consolidated_rating']] = $emp_count;
				}
			}
			//get the max rating to calculate the number of boxes
			if(isset($ratingsarray) && !empty($ratingsarray))
			{
				$maxrating = max(array_keys($ratingsarray));
				if($maxrating <= 5)
				{
					$noofboxes = 5;
				}
				else
				{
					$noofboxes = 10;				
				}
			}		
			//getting the employees data
			$emp_data = $appraisalRatings_model->getEmpRatings($fromyear,$toyear,$department,$reportingmanager,$businessunit,$appraisalData,$employeeid);
			$emp_grouped_data = array();
			if($noofboxes > 0)
			{
				for($i=1;$i<=$noofboxes;$i++)
				{
					$emp_inner_data = array();
					foreach($emp_data as $emp)
					{
						if($emp['consolidated_floor_rating']==$i)
						{
							array_push($emp_inner_data,$emp);
						}
					}
					$emp_grouped_data[$i] = $emp_inner_data;
				}
			}
			//getting business units
			$bu_model = new Default_Model_Businessunits();
            $bu_arr = $bu_model->getBU_report();
            if(!empty($bu_arr))
            {
                foreach ($bu_arr as $bu)
                {
                    $form->businessunit_id->addMultiOption($bu['id'],utf8_encode($bu['bu_name']));
                }
            }
            else
            {
                $norec_arr['businessunit_id'] = 'Business Units are not added yet.';
            }			
			$this->view->emp_data = $emp_data;
			$this->view->emp_grouped_data = $emp_grouped_data;
			$this->view->ratingsarray = $ratingsarray;
			$this->view->noofboxes = $noofboxes;
			$this->view->messages = $norec_arr;
			$this->view->fromyear = $fromyear;
			$this->view->toyear = $toyear;
			$this->view->form = $form;
		}
		catch(Exception $e)
		{
			print_r($e);
		}
	}
	
	/**
	** displays previous emp appraisals based on emp id,
	** on click of individual emp record in heat map report
	**/
	public function previousappraisalsAction($empId="")
	{
		try
		{
			if(empty($empId))
			{
				$empId = $this->_request->getParam('id'); 
			}
			
			/** 
			** 1. get employee details
			**/
				$empModel = new Default_Model_Employee();
				$employeeDetails = $empModel->getEmp_from_summary($empId);
				
			/** 
			** 2. get employee appraisal details
			**/
				$empAppraisalModel = new Default_Model_Appraisalemployeeratings();
				$empAppraisalDetails = $empAppraisalModel->getEmpAppraisalData($empId);
				
			/** 
			** 3. display employee graph
			**/

			/** 
			** 4. By default, display latest appraisal ratings and questions
			**/

			$this->view->employeeData = $employeeDetails;
			$this->view->empAppraisalDetails = $empAppraisalDetails;			
		}
		catch(Exception $e)
		{
			print_r($e);
		}
	}

	/**
	** get individual 
	**/
	public function getselectedappraisaldataAction()
	{
		try
		{
			$appId = $this->_request->getParam('appId');
			$empId = $this->_request->getParam('empId');
			$period = $this->_request->getParam('period');
			$empAppraisalData = "";$questionsData = "";$categoriesData = "";$empData = "";$ratingsData = "";
			if($appId && $empId)
			{
				$empAppraisalModel = new Default_Model_Appraisalemployeeratings();
				$empAppraisals = $empAppraisalModel->getSelectedAppraisalData($appId,$empId,$period);
				$configId = isset($empAppraisals[0]['pa_configured_id'])?$empAppraisals[0]['pa_configured_id']:0;
				// get rating details using configuration id
				$appEmpRatingsModel = new Default_Model_Appraisalemployeeratings();
				$ratingsData = $appEmpRatingsModel->getAppRatingsDataByConfgId($configId,$appId);
				$ratingType = "";
				if(!empty($ratingsData))
					$ratingType = $ratingsData[0]['rating_type'];
				
				$ratingText = array();
				$ratingTextDisplay = array();
				$ratingValues = array();
				foreach ($ratingsData as $rd){
					$ratingText[] = $rd['rating_text'];
					$ratingTextDisplay[$rd['id']] = $rd['rating_text'];
					$ratingValues[$rd['id']] = $rd['rating_value']; 
				}				
				//$empAppraisalData = $empAppraisalModel->getEmpQuestionsData($appId,$empId);
				if(!empty($empAppraisals))
				{
					if(!empty($empAppraisals[0]['employee_response']))
					{
						$empResponse = json_decode($empAppraisals[0]['employee_response']);
						$empResponseArray = get_object_vars($empResponse);
						$strQuestionIds = implode(",",array_keys($empResponseArray));
						$questionsData = $empAppraisalModel->getQuestionsData($strQuestionIds);
											
						$tmpRatingIdsObject = array_values($empResponseArray);
						$tmpRatingIdsArr = array();
						foreach($tmpRatingIdsObject as $ratingArr)
						{
							$tmpRatings = get_object_vars($ratingArr);
							$tmpRatingIdsArr[] = $tmpRatings['rating_id'];
						}
						if(!empty($empAppraisals[0]['manager_response']))
						{
							$managerResponse = json_decode($empAppraisals[0]['manager_response']);
							$managerResponseArray = get_object_vars($managerResponse);
							$managerRatingIdsObject = array_values($managerResponseArray);
							
							foreach($managerRatingIdsObject as $ratingArr)
							{
								$tmpRatings = get_object_vars($ratingArr);
								$tmpRatingIdsArr[] = $tmpRatings['rating'];
							}
						}
						$tmpRatingIdsStr = (!empty($tmpRatingIdsArr))?implode(",",$tmpRatingIdsArr):"";
						if(!empty($tmpRatingIdsStr))
						{
							$ratingsData = $empAppraisalModel->getRatingsData($tmpRatingIdsStr);
						}

						if(!empty($ratingsData))
						{
							$r = 0;
							foreach($ratingsData as $rdata)
							{
								$ratingsData[$rdata['id']] = $rdata;
								unset($ratingsData[$r]);
								$r++;
							}
						}						
					}
					$strCategories = $empAppraisals[0]['category_id'];
					$categoriesData = $empAppraisalModel->getCategories($strCategories);

					

					/** for getting line managers name, business unit, department details
					**
					$strEmpId = $empId.',';

					if(!empty($empAppraisals[0]['line_manager_1']))
					{
							$strEmpId .= $empAppraisals[0]['line_manager_1'];
							$strEmpId .= ',';
					}
					if(!empty($empAppraisals[0]['line_manager_2']))
					{
						$strEmpId .= $empAppraisals[0]['line_manager_2'];
						$strEmpId .= ',';
					}
					if(!empty($empAppraisals[0]['line_manager_3']))
					{
						$strEmpId .= $empAppraisals[0]['line_manager_3'];
						$strEmpId .= ',';
					}
					if(!empty($empAppraisals[0]['line_manager_4']))
					{
						$strEmpId .= $empAppraisals[0]['line_manager_4'];
						$strEmpId .= ',';
					}
					if(!empty($empAppraisals[0]['line_manager_5']))
					{
						$strEmpId .= $empAppraisals[0]['line_manager_5'];
						$strEmpId .= ',';
					}
					
					$strEmpId = trim($strEmpId,",");
					$empData = $empAppraisalModel->getEmployeeData($strEmpId);
					**/

				}
			}
			$appSkillsModel = new Default_Model_Appraisalskills();
			$skills = array();
			$skills = $appSkillsModel->getAppraisalSkillsData();
			$skills_arr = array();
			foreach($skills as $skill)
			{
				$skills_arr[$skill['id']] = $skill; 
			}
			$this->view->skills_arr = $skills_arr;
			$this->view->selectedAppraisals = $empAppraisals;
			$this->view->categoriesData = $categoriesData;
			$this->view->empData = $empData;
			$this->view->questionsData = $questionsData;
			$this->view->ratingsData = $ratingsData;
			$this->view->ratingType = $ratingType;
			$this->view->ratingTextDisplay = $ratingTextDisplay;
			$this->view->ratingText = json_encode($ratingText);
			$this->view->ratingValues = $ratingValues;		
			$this->view->user_id = $empId;
			$this->view->appraisal_id = $appId;
		}
		catch(Exception $e)
		{
			print_r($e);
		}
	}

	public function getinterviewroundsdataAction()
	{
		$param_arr = array();
		$param_arr['interview_date'] = $this->_getParam('interview_date',null);
		$param_arr['requisition_id'] = $this->_getParam('req_id',null);
		$param_arr['interviewer_id'] = $this->_getParam('interviewer_id',null);
		$param_arr['department_id'] = $this->_getParam('department_id',null);
		$param_arr['ins.created_by'] = $this->_getParam('created_by',null);
		$page_no = $this->_getParam('page_no',1);
		$per_page = $this->_getParam('per_page',PERPAGE);
		$sort_name = $this->_getParam('sort_name',null);
		$sort_type = $this->_getParam('sort_type',null);
		$cols_param_arr = $this->_getParam('cols_arr',array());

		$req_model = new Default_Model_Requisition();
		$req_data = $req_model->getdata_for_interviewrpt($param_arr, $sort_name, $sort_type, $page_no, $per_page);
		$page_cnt = $req_data['page_cnt'];
		$req_arr = $req_data['rows'];


		$all_columns_arr = array(
                        'candidate_name' => 'Candidate',
                        'requisition_code' => 'Requisition Code',                        
                        'position_name' => 'Position',
                        'reporting_manager_name' => 'Reporting Manager',
                        'businessunit_name' => 'Business Unit',                                                                        
                        'department_name' => 'Department',
                        'req_status' => 'Requisition Status',
                        'candidate_status' => 'Candidate Status',
                        'interview_status' => 'Interview Status',
                        'interviewer_name' => 'Interviewer',
                        'interview_time' => 'Interview Time',
                        'interview_date' => 'Interview Date' ,
                        'interview_mode' => 'Interview Mode',
                        'interview_round_number' => 'Interview Round Number',
                        'interview_round_name' => 'Interview Round',                
                        'interview_location' => 'Interview Location',
                        'interview_city_name' => 'Interview City',
                        'interview_state_name' => 'Interview State',
                        'interview_country_name' => 'Interview Country',
                        'created_by_name' => 'Interview Scheduled By',
                        'interview_feedback' => 'Interview Feedback',
                        'interview_comments' => 'Interview Comments',
                        'round_status' => 'Round Status',
		);

		if(count($cols_param_arr) == 0)
		{
			$cols_param_arr = array(
                        'candidate_name' => 'Candidate',
                        'requisition_code' => 'Requisition Code',                                                                        
                        'businessunit_name' => 'Business Unit',                                                                        
                        'department_name' => 'Department',                        
                        'candidate_status' => 'Candidate Status',
                        'interview_status' => 'Interview Status',
                        'interviewer_name' => 'Interviewer',
                        'interview_time' => 'Interview Time',
                        'interview_date' => 'Interview Date' ,
                        'interview_mode' => 'Interview Mode',
                        'interview_round_number' => 'Interview Round Number',
                        'interview_round_name' => 'Interview Round',                                                                                                                
                        'created_by_name' => 'Interview Scheduled By',
                        'interview_feedback' => 'Interview Feedback',
                        'interview_comments' => 'Interview Comments',
                        'round_status' => 'Round Status',
			);
		}
		$this->view->req_arr = $req_arr;
		$this->view->per_page = $per_page;
		$this->view->page_no = $page_no;
		$this->view->sort_name = $sort_name;
		$this->view->sort_type = $sort_type;
		$this->view->cols_param_arr = $cols_param_arr;
		$this->view->page_cnt = $page_cnt;
		$this->view->all_columns_arr = $all_columns_arr;
	}
	public function interviewroundsAction()
	{
		$norec_arr = array();
		$form = new Default_Form_Interviewroundrpt();
		$req_model = new Default_Model_Requisition();
		$dept_model = new Default_Model_Departments();
		$req_data = $req_model->getreq_for_interviewrpt();
		$dept_data = $dept_model->getdepts_interview_report();

		if(count($dept_data) >0)
		{
			$dept_opt = array();
			foreach($dept_data as $dept)
			{
				$dept_opt[$dept['dept_id']] = $dept['dept_name'];
			}
			$form->department_id->addMultiOptions(array('' => 'Select Department') + $dept_opt);
		}
		else
		{
			$norec_arr['department_id'] = "Departments are not configured yet.";
		}
		if(count($req_data) >0)
		{
			$req_opt = array();
			foreach($req_data as $req)
			{
				$req_opt[$req['req_id']] = $req['req_name'];
			}
			$form->req_id->addMultiOptions(array('' => 'Select Requisition Code') + $req_opt);
		}
		else
		{
			$norec_arr['req_id'] = "No approved requisitions.";
		}

		$this->view->messages = $norec_arr;
		$this->view->form = $form;
	}
	public function rolesgroupAction()
	{

	}
	/**
	 * This function is used to export employee roles group report into excel.
	 */
	public function exportemprolesgroupAction()
	{
		$this->_helper->layout->disableLayout();
		$sort_name = $this->_getParam('sort_name',null);
		$sort_type = $this->_getParam('sort_type',null);
		$cols_param_arr = array('group_name' => 'Group','rolename' => 'Role','user_cnt' => 'Users count');
		$roles_model = new Default_Model_Roles();
		$emproles_data = $roles_model->getdata_emprolesgroup_rpt($sort_name,$sort_type);
		$emproles_arr = array();
		if(count($emproles_data) > 0)
		{
			foreach($emproles_data as $emp)
			{
				$emproles_arr[$emp['group_name']][$emp['rolename']] = $emp['user_cnt'];
			}
		}

		require_once 'Classes/PHPExcel.php';
		require_once 'Classes/PHPExcel/IOFactory.php';
		$objPHPExcel = new PHPExcel();

		$letters = range('A','Z');
		$count =0;
		$filename = "Roles&Group&Employees.xlsx";
		$cell_name="";
		$objPHPExcel->getActiveSheet()->getColumnDimension($letters[$count])->setWidth(60);
		
		foreach ($cols_param_arr as $names)
		{
			$i = 1;
			$cell_name = $letters[$count].$i;
			$names = html_entity_decode($names,ENT_QUOTES,'UTF-8');

			$objPHPExcel->getActiveSheet()->SetCellValue($cell_name, $names);
			// Make bold cells
			$objPHPExcel->getActiveSheet()->getStyle($cell_name)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle($cell_name)->applyFromArray( array(
									        'fill' => array(
									            'type' => PHPExcel_Style_Fill::FILL_SOLID,
									            'color' => array('rgb' => '82CAFF')
			)
			)
			);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letters[$count])->setAutoSize(true);
			$i++;
			$count++;
		}

		// Display field/column values in Excel.
		$i = 2;
		foreach($emproles_arr as $key => $emproles)
		{
			$count1 =0;
			$objPHPExcel->getActiveSheet()->getColumnDimension($letters[0])->setWidth(60);
			// display field/column values
			$cell_name = $letters[$count1].$i;
			$value = $key;
			$mcell_name = $letters[$count1].($i+count($emproles));
			$value = html_entity_decode($value,ENT_QUOTES,'UTF-8');
			$prange = $cell_name.":".$mcell_name;
			$objPHPExcel->getActiveSheet()->mergeCells($prange);
			$objPHPExcel->getActiveSheet()->SetCellValue($cell_name, $value);

			$count1++;
			//for role count
			$cell_name = $letters[$count1].$i;
			$objPHPExcel->getActiveSheet()->SetCellValue($cell_name, count($emproles));
			//end of role count
			//for employee count
			$cell_name = $letters[$count1+1].($i);
			$objPHPExcel->getActiveSheet()->SetCellValue($cell_name, array_sum($emproles));
			//end of for employee count
			//for role names
			$j = $count1;
			$k = $i+1;
			foreach($emproles as $role => $cnt)
			{
				$cell_name = $letters[$j].$k;
				$value = html_entity_decode($role,ENT_QUOTES,'UTF-8');
				$objPHPExcel->getActiveSheet()->SetCellValue($cell_name, $value);
				$cell_name = $letters[$j+1].$k;
				$value = html_entity_decode($cnt,ENT_QUOTES,'UTF-8');
				$objPHPExcel->getActiveSheet()->SetCellValue($cell_name, $value);
				$k++;
			}
			//end of for role names
			$i = $i +count($emproles)+1;
		}
		sapp_Global::clean_output_buffer();
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header('Cache-Control: max-age=0');
		sapp_Global::clean_output_buffer();
			
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');

		exit;

	}
	/**
	 * This is used to export roles group report to excel.
	 */
	public function exportrolesgroupreportAction()
	{
		$this->_helper->layout->disableLayout();
		$roles_model = new Default_Model_Roles();
		$sort_name = $this->_getParam('sort_name',null);
		$sort_type = $this->_getParam('sort_type',null);
		$roles_data = $roles_model->getdata_for_rolesgroup_rpt($sort_name,$sort_type);
		$cols_param_arr = array('group_name' => 'Group','cnt' => 'No.Of Roles');
		sapp_Global::export_to_excel($roles_data,$cols_param_arr,"GroupRoles.xlsx");

		exit;
	}

	/**
	 * This is used to export schedule interviews report to excel.
	 */
	public function exportinterviewrptAction()
	{
		$this->_helper->layout->disableLayout();
		$param_arr = array();
		$param_arr['interview_date'] = $this->_getParam('interview_date',null);
		$param_arr['requisition_id'] = $this->_getParam('req_id',null);
		$param_arr['interviewer_id'] = $this->_getParam('interviewer_id',null);
		$param_arr['department_id'] = $this->_getParam('department_id',null);
		$param_arr['ins.created_by'] = $this->_getParam('created_by',null);
		$page_no = $this->_getParam('page_no',1);
		$per_page = $this->_getParam('per_page',PERPAGE);
		$sort_name = $this->_getParam('sort_name',null);
		$sort_type = $this->_getParam('sort_type',null);
		$cols_param_arr = $this->_getParam('cols_arr',array());
		foreach($cols_param_arr as $col_key => $col_val) {
			if(in_array($col_key,$this->printable_interview_arr)) {
				unset($cols_param_arr[$col_key]);
			}
		}

		$req_model = new Default_Model_Requisition();
		$req_data = $req_model->getdata_for_interviewrpt($param_arr, $sort_name, $sort_type, $page_no, $per_page);
		$page_cnt = $req_data['page_cnt'];
		$req_arr = $req_data['rows'];
		sapp_Global::export_to_excel($req_arr,$cols_param_arr,"ScheduleInterviews.xlsx");

		exit;
	}
	/**
	 * This action is used to export active user report into excel.
	 */
	public function exportactiveuserrptAction()
	{
		$this->_helper->layout->disableLayout();
		$search_arr = array();
		$search_arr['createddate'] = $this->_getParam('createddate',null);
		$search_arr['u.emprole'] = $this->_getParam('emprole',null);
		$search_arr['u.isactive'] = $this->_getParam('isactive',null);
		$search_arr['logindatetime'] = $this->_getParam('logindatetime',null);

		$page_no = $this->_getParam('page_no',1);

		$per_page = $this->_getParam('per_page',PERPAGE);
		$sort_name = $this->_getParam('sort_name',null);
		$sort_type = $this->_getParam('sort_type',null);

		$cols_param_arr = $this->_getParam('cols_arr',array());
		$user_model = new Default_Model_Usermanagement();

		$user_data_org = $user_model->getdata_user_report($search_arr,$per_page,$page_no,$sort_name,$sort_type);

		$page_cnt = $user_data_org['page_cnt'];
		$user_data = $user_data_org['rows'];
		$mod_user_data = array();
		foreach($user_data as $userd)
		{
			$userd['lastlog'] = sapp_Global::change_date($userd['lastlog'], 'view');
			$userd['createdate'] = sapp_Global::change_date($userd['createdate'], 'view');
			$userd['isactive'] = $userd['isactive'] == 1?"Active":"Inactive";
			$mod_user_data[] = $userd;
		}
		sapp_Global::export_to_excel($mod_user_data,$cols_param_arr,"Users&Employees.xlsx");

		exit;
	}
	/**
	 * This is used to export to excel.
	 */
	public function exportemployeereportAction()
	{
		$this->_helper->layout->disableLayout();
		$param_arr = $this->_getAllParams();
		

		$cols_param_arr = $this->_getParam('cols_arr',array());
		if(isset($param_arr['cols_arr']))	unset($param_arr['cols_arr']);
		$page_no = isset($param_arr['page_no'])?$param_arr['page_no']:1;
		$per_page = isset($param_arr['per_page'])?$param_arr['per_page']:PERPAGE;
		$sort_name = $param_arr['sort_name'];
		$sort_type = $param_arr['sort_type'];
		if(isset($param_arr['page_no']))unset($param_arr['page_no']);
		if(isset($param_arr['sort_name']))unset($param_arr['sort_name']);
		if(isset($param_arr['sort_type']))unset($param_arr['sort_type']);
		if(isset($param_arr['per_page']))unset($param_arr['per_page']);
		unset($param_arr['module']);unset($param_arr['controller']);unset($param_arr['action']);

		if(count($cols_param_arr) == 0)
		$cols_param_arr = $this->empreport_heplper1('mandatory');
		$employee_model = new Default_Model_Employee();
		$emp_data_org = $employee_model->getdata_emp_report($param_arr,$per_page,$page_no,$sort_name,$sort_type);
	
		$emp_arr = $emp_data_org['rows'];

		require_once 'Classes/PHPExcel.php';
		require_once 'Classes/PHPExcel/IOFactory.php';
		$objPHPExcel = new PHPExcel();

		$letters = range('A','Z');
		$count =0;
		$filename = "EmployeeReport.xlsx";
		$cell_name="";
			
		// Make first row Headings bold and highlighted in Excel.
		foreach ($cols_param_arr as $names)
		{
			$i = 1;
			$cell_name = $letters[$count].$i;
			$names = html_entity_decode($names,ENT_QUOTES,'UTF-8');

			$objPHPExcel->getActiveSheet()->SetCellValue($cell_name, $names);
			// Make bold cells
			$objPHPExcel->getActiveSheet()->getStyle($cell_name)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle($cell_name)->applyFromArray( array(
									        'fill' => array(
									            'type' => PHPExcel_Style_Fill::FILL_SOLID,
									            'color' => array('rgb' => '82CAFF')
			)
			)
			);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letters[$count])->setAutoSize(true);
			$i++;
			$count++;
		}

		// Display field/column values in Excel.
		$i = 2;
		foreach($emp_arr as $emp_data)
		{
			$count1 =0;
			foreach ($cols_param_arr as $column_key => $column_name)
			{
				// display field/column values
				$cell_name = $letters[$count1].$i;


				if($column_key == 'userfullname')
				{
					$value = isset($emp_data['userfullname'])?(!empty($emp_data['prefix_name'])?($emp_data['prefix_name'].". ".$emp_data['userfullname']):$emp_data['userfullname']):"";
				}
				elseif($column_key == 'date_of_joining')
				{
					$value = isset($emp_data['date_of_joining'])?  sapp_Global::change_date($emp_data['date_of_joining'],"view"):"";
				}
				else
				{
					$value = isset($emp_data[$column_key])?$emp_data[$column_key]:"";
				}
				$value = html_entity_decode($value,ENT_QUOTES,'UTF-8');
				
				$objPHPExcel->getActiveSheet()->SetCellValue($cell_name, $value);
				$count1++;
			}
			$i++;
		}
			
		sapp_Global::clean_output_buffer();
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header('Cache-Control: max-age=0');
		sapp_Global::clean_output_buffer();
			
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');

		exit;
	}
	/**
	 * This is used to export roles groups report to pdf.
	 */
	public function rolesgrouprptpdfAction()
	{
		$this->_helper->layout->disableLayout();
		$roles_model = new Default_Model_Roles();
		$sort_name = $this->_getParam('sort_name',null);
		$sort_type = $this->_getParam('sort_type',null);
		$roles_data = $roles_model->getdata_for_rolesgroup_rpt($sort_name,$sort_type);
		$cols_param_arr = array('group_name' => 'Group','cnt' => 'No.Of Roles');

		$field_names = array();
		$field_widths = array();
		$data['field_name_align'] = array();

		foreach($cols_param_arr as $column_key => $column_name)
		{
			$field_names[] = array(
                                        'field_name'=>$column_key,
                                        'field_label'=>$column_name
			);
			$field_widths[] = 25;
			$data['field_name_align'][] = 'C';
		}

		$data = array('grid_no'=>1, 'project_name'=>'', 'object_name'=>'Groups & Roles Report', 'grid_count'=>1,'file_name'=>'Groups&Roles.pdf');

		$pdf = $this->_helper->PdfHelper->generateReport($field_names, $roles_data, $field_widths, $data);
		$this->_helper->json(array('file_name'=>$data['file_name']));
	}
	public function activeuserrptpdfAction()
	{
		$this->_helper->layout->disableLayout();
		$search_arr = array();
		$search_arr['createddate'] = $this->_getParam('createddate',null);
		$search_arr['u.emprole'] = $this->_getParam('emprole',null);
		$search_arr['u.isactive'] = $this->_getParam('isactive',null);
		$search_arr['logindatetime'] = $this->_getParam('logindatetime',null);

		$page_no = $this->_getParam('page_no',1);

		$per_page = $this->_getParam('per_page',PERPAGE);
		$sort_name = $this->_getParam('sort_name',null);
		$sort_type = $this->_getParam('sort_type',null);

		$cols_param_arr = $this->_getParam('cols_arr',array());
		$user_model = new Default_Model_Usermanagement();

		$user_data_org = $user_model->getdata_user_report($search_arr,$per_page,$page_no,$sort_name,$sort_type);

		$page_cnt = $user_data_org['page_cnt'];
		$user_data = $user_data_org['rows'];
		$mod_user_data = array();
		foreach($user_data as $userd)
		{
			$userd['lastlog'] = sapp_Global::change_date($userd['lastlog'], 'view');
			$userd['createdate'] = sapp_Global::change_date($userd['createdate'], 'view');
			$userd['isactive'] = $userd['isactive'] == 1?"Active":"Inactive";
			$mod_user_data[] = $userd;
		}
		$field_names = array();
		$field_widths = array();
		$data['field_name_align'] = array();

		foreach($cols_param_arr as $column_key => $column_name)
		{
			$field_names[] = array(
                                        'field_name'=>$column_key,
                                        'field_label'=>$column_name
			);
			$field_widths[] = 25;
			$data['field_name_align'][] = 'C';
		}
		if(count($cols_param_arr) != 7)
		{
			$totalPresentFieldWidth = array_sum($field_widths);
			foreach($field_widths as $key => $width)
			{
				$field_widths[$key] = ($width*180)/$totalPresentFieldWidth;
			}
		}
		$data = array('grid_no'=>1, 'project_name'=>'', 'object_name'=>'Users & Employees Report', 'grid_count'=>1,'file_name'=>'Users&Employees.pdf');

		$pdf = $this->_helper->PdfHelper->generateReport($field_names, $mod_user_data, $field_widths, $data);

		
		$this->_helper->json(array('file_name'=>$data['file_name']));
	}
	/**
	 * This is used to export employee report in pdf.
	 */
	public function emprptpdfAction()
	{
		$this->_helper->layout->disableLayout();
		$param_arr = $this->_getAllParams();

		$cols_param_arr = $this->_getParam('cols_arr',array());
		if(isset($param_arr['cols_arr']))	unset($param_arr['cols_arr']);
		$page_no = isset($param_arr['page_no'])?$param_arr['page_no']:1;
		$per_page = isset($param_arr['per_page'])?$param_arr['per_page']:PERPAGE;
		$sort_name = $param_arr['sort_name'];
		$sort_type = $param_arr['sort_type'];
		if(isset($param_arr['page_no']))unset($param_arr['page_no']);
		if(isset($param_arr['sort_name']))unset($param_arr['sort_name']);
		if(isset($param_arr['sort_type']))unset($param_arr['sort_type']);
		if(isset($param_arr['per_page']))unset($param_arr['per_page']);
		unset($param_arr['module']);unset($param_arr['controller']);unset($param_arr['action']);
		if(count($cols_param_arr) == 0)
		$cols_param_arr = $this->empreport_heplper1('mandatory');
		$employee_model = new Default_Model_Employee();
		$emp_data_org = $employee_model->getdata_emp_report($param_arr,$per_page,$page_no,$sort_name,$sort_type);
		$emp_arr = $emp_data_org['rows'];
		$field_names = array();
		$field_widths = array();
		$data['field_name_align'] = array();

		foreach($cols_param_arr as $column_key => $column_name)
		{
			$field_names[] = array(
                                        'field_name'=>$column_key,
                                        'field_label'=>$column_name
			);
			$field_widths[] = 25;
			$data['field_name_align'][] = 'C';
		}
		if(count($cols_param_arr) != 7)
		{
			$totalPresentFieldWidth = array_sum($field_widths);
			foreach($field_widths as $key => $width)
			{
				$field_widths[$key] = ($width*180)/$totalPresentFieldWidth;
			}
		}
		$data = array('grid_no'=>1, 'project_name'=>'', 'object_name'=>'Employee Report', 'grid_count'=>1,'file_name'=>'EmployeeRpt.pdf');

		$pdf = $this->_helper->PdfHelper->generateReport($field_names, $emp_arr, $field_widths, $data);

		
		$this->_helper->json(array('file_name'=>$data['file_name']));
	}
	/**
	 * This is used to export schedule report in pdf.
	 */
	public function interviewrptpdfAction()
	{
		$this->_helper->layout->disableLayout();
		$param_arr = array();
		$param_arr['interview_date'] = $this->_getParam('interview_date',null);
		$param_arr['requisition_id'] = $this->_getParam('req_id',null);
		$param_arr['interviewer_id'] = $this->_getParam('interviewer_id',null);
		$param_arr['department_id'] = $this->_getParam('department_id',null);
		$param_arr['ins.created_by'] = $this->_getParam('created_by',null);
		$page_no = $this->_getParam('page_no',1);
		$per_page = $this->_getParam('per_page',PERPAGE);
		$sort_name = $this->_getParam('sort_name',null);
		$sort_type = $this->_getParam('sort_type',null);
		$cols_param_arr = $this->_getParam('cols_arr',array());
		foreach($cols_param_arr as $col_key => $col_val) {
			if(in_array($col_key,$this->printable_interview_arr)) {
				unset($cols_param_arr[$col_key]);
			}
		}
		
		$req_model = new Default_Model_Requisition();
		$req_data = $req_model->getdata_for_interviewrpt($param_arr, $sort_name, $sort_type, $page_no, $per_page);
		$page_cnt = $req_data['page_cnt'];
		$req_arr = $req_data['rows'];
		$field_names = array();
		$field_widths = array();
		$data['field_name_align'] = array();

		foreach($cols_param_arr as $column_key => $column_name)
		{
			$field_names[] = array(
                                    'field_name'=>$column_key,
                                    'field_label'=>$column_name
			);
			$field_widths[] = 25;
			$data['field_name_align'][] = 'C';
		}
		if(count($cols_param_arr) != 7)
		{
			$totalPresentFieldWidth = array_sum($field_widths);
			foreach($field_widths as $key => $width)
			{
				$field_widths[$key] = ($width*180)/$totalPresentFieldWidth;
			}
		}
		$data = array('grid_no'=>1, 'project_name'=>'', 'object_name'=>'Scheduled Interviews', 'grid_count'=>1,'file_name'=>'Scheduled_interviews.pdf');

		$pdf = $this->_helper->PdfHelper->generateReport($field_names, $req_arr, $field_widths, $data);

		$this->_helper->json(array('file_name'=>$data['file_name']));
	}

	public function rolesgroupdataAction()
	{
		$roles_model = new Default_Model_Roles();
		$sort_name = $this->_getParam('sort_name',null);
		$sort_type = $this->_getParam('sort_type',null);
		$roles_data = $roles_model->getdata_for_rolesgroup_rpt($sort_name,$sort_type);

		$this->view->roles_data = $roles_data;
		$this->view->sort_name = $sort_name;
		$this->view->sort_type = $sort_type;
	}
	public function emprolesgroupAction()
	{

	}
	public function emprolesgroupdataAction()
	{
            $roles_model = new Default_Model_Roles();
            $sort_name = $this->_getParam('sort_name',null);
            $sort_type = $this->_getParam('sort_type',null);

            $emproles_data = $roles_model->getdata_emprolesgroup_rpt($sort_name,$sort_type);
            $emproles_arr = array();
            if(count($emproles_data) > 0)
            {
                foreach($emproles_data as $emp)
                {
                    $emproles_arr[$emp['group_id']]['group_name'] = $emp['group_name'];
                    if(isset($emproles_arr[$emp['group_id']]['emp_cnt']))                        
                        $emproles_arr[$emp['group_id']]['emp_cnt'] += $emp['user_cnt'];
                    else 
                        $emproles_arr[$emp['group_id']]['emp_cnt'] = $emp['user_cnt'];
                    $emproles_arr[$emp['group_id']]['childs'][] = array('rolename' => $emp['rolename'],
                                                                         'user_cnt' => $emp['user_cnt'],
                                                                         'role_id' => $emp['role_id']);                    
                }
            }
            $cols_param_arr = array('group_name' => 'Group','rolename' => 'Role','user_cnt' => 'Users count');
            $this->view->emproles_arr = $emproles_arr;
            $this->view->sort_name = $sort_name;
            $this->view->sort_type = $sort_type;
            $this->view->cols_param_arr = $cols_param_arr;
	}
	public function activeuserAction()
	{
		$norec_arr = array();
		$form = new Default_Form_Activeuserreport();
		$roles_model = new Default_Model_Roles();
		$roles_data = $roles_model->getRolesList();
		if(count($roles_data) > 0)
		{
			$form->emprole->addMultiOptions(array(''=>'Select Role')+$roles_data);
		}
		else
		{
			$norec_arr['emprole'] = "Roles are not added yet.";
		}

		$this->view->form = $form;
		$this->view->messages = $norec_arr;

	}
	public function getactiveuserdataAction()
	{
		$search_arr = array();
		$search_arr['createddate'] = $this->_getParam('createddate',null);
		$search_arr['u.emprole'] = $this->_getParam('emprole',null);
		$search_arr['u.isactive'] = $this->_getParam('isactive',null);
		$search_arr['logindatetime'] = $this->_getParam('logindatetime',null);

		$page_no = $this->_getParam('page_no',1);

		$per_page = $this->_getParam('per_page',PERPAGE);
		$sort_name = $this->_getParam('sort_name',null);
		$sort_type = $this->_getParam('sort_type',null);

		$cols_param_arr = $this->_getParam('cols_arr',array());
		$user_model = new Default_Model_Usermanagement();

		$user_data_org = $user_model->getdata_user_report($search_arr,$per_page,$page_no,$sort_name,$sort_type);

		$page_cnt = $user_data_org['page_cnt'];
		$user_data = $user_data_org['rows'];

		$columns_arr = array(
                'userfullname' => 'Name',
                'rolename' => 'Role',
                'emailaddress' => 'Email',
                'employeeId' => 'ID',
                'lastlog' => 'Last Login',
                'isactive' => 'Status',
                'createdate' => 'Created On',

		);
		if(count($cols_param_arr) == 0)
		$cols_param_arr = $columns_arr;

		$this->view->user_data = $user_data;
		$this->view->per_page = $per_page;
		$this->view->page_no = $page_no;
		$this->view->sort_name = $sort_name;
		$this->view->sort_type = $sort_type;
		$this->view->cols_param_arr = $cols_param_arr;
		$this->view->page_cnt = $page_cnt;
		$this->view->columns_arr = $columns_arr;
	}
	public function getempreportdataAction()
	{
		$param_arr = $this->_getAllParams();
		$cols_param_arr = $this->_getParam('cols_arr',array());
		if(isset($param_arr['cols_arr']))
                    unset($param_arr['cols_arr']);
		$page_no = isset($param_arr['page_no'])?$param_arr['page_no']:1;
		$per_page = isset($param_arr['per_page'])?$param_arr['per_page']:PERPAGE;
		$sort_name = $param_arr['sort_name'];
		$sort_type = $param_arr['sort_type'];
		if(isset($param_arr['page_no']))unset($param_arr['page_no']);
		if(isset($param_arr['sort_name']))unset($param_arr['sort_name']);
		if(isset($param_arr['sort_type']))unset($param_arr['sort_type']);
		if(isset($param_arr['per_page']))unset($param_arr['per_page']);
		unset($param_arr['module']);unset($param_arr['controller']);unset($param_arr['action']);unset($param_arr['format']);

		$employee_model = new Default_Model_Employee();


		$emp_data_org = $employee_model->getdata_emp_report($param_arr,$per_page,$page_no,$sort_name,$sort_type);
		$page_cnt = $emp_data_org['page_cnt'];
		$emp_arr = $emp_data_org['rows'];
    	$columns_array = $this->empreport_heplper1('all');
		$mandatory_array = $this->empreport_heplper1('mandatory');
	    
		if(count($cols_param_arr)  == 0)
		$cols_param_arr = $mandatory_array;
		$mandatory_array = array_keys($mandatory_array);
		$this->view->emp_arr = $emp_arr;
		$this->view->page_cnt = $page_cnt;
		$this->view->per_page = $per_page;
		$this->view->page_no = $page_no;
		$this->view->cols_param_arr = $cols_param_arr;
		$this->view->sort_name = $sort_name;
		$this->view->sort_type = $sort_type;
		$this->view->columns_array = $columns_array;
	}
	public function empreport_heplper1($type)
	{
		$columns_array = array(
                        'employeeId' => 'Employee ID',
                        'userfullname' => 'Employee',
                        'emailaddress' => 'Email',
                        'contactnumber' => 'Mobile',
                        'emprole_name' => 'Role',
                        'reporting_manager_name' => 'Reporting Manager',
                        'date_of_joining' => 'Date of Joining',
                        'modeofentry' => 'Mode of Employment',
                        'jobtitle_name' => 'Job Title',
                        'position_name' => 'Position',
                        'businessunit_name' => 'Business Unit',
                        'department_name' => 'Department',
                        'emp_status_name' => 'Employment Status',
                        'date_of_leaving' => 'Date of Leaving',
                        'years_exp' => 'Years of Experience',
                        'holiday_group_name' => 'Holiday Group',
                        'office_number' => 'Work Phone',
                        'extension_number' => 'Extension Number',
                        'backgroundchk_status' => 'Background Check Status',
                        'other_modeofentry' => 'Mode of Entry(Other)',
                        'referer_name' => 'Referred By',
                        'currencyname' => 'Salary Currency',
                        'freqtype' => 'Pay Frequency',
                        'salary' => 'Salary',

		);
		$mandatory_array = array(
                        'employeeId' => 'Employee ID',
                        'userfullname' => 'Employee',
                        'emailaddress' => 'Email',
                        'contactnumber' => 'Mobile',
                        'emprole_name' => 'Role',
                        'reporting_manager_name' => 'Reporting Manager',
                        'jobtitle_name' => 'Job Title',
                        // 'position_name' => 'Position',
                        'businessunit_name' => 'Business Unit',
                        'department_name' => 'Department',
						/*'emp_status_name' => 'Employment Status',
                        'date_of_joining' => 'Date of Joining',  */                                   
		);
		/* if($type == 'all')
		return $columns_array;
		else */
		return $mandatory_array;
	}
	public function empautoAction()
	{
		$term = $this->_getParam('term',null);
		$output = array(array('id' => '','value' => 'No records','label' => 'No records'));
		$emp_model = new Default_Model_Employee();
		if($term != '')
		{
			$emp_arr = $emp_model->getAutoReportEmp($term);
			if(count($emp_arr)>0)
			{
				$output = array();
				foreach($emp_arr as $emp)
				{
					$output[] = array('id' => $emp['user_id'],'value' => $emp['emp_name'],'label' => $emp['emp_name'],'profile_img' => $emp['profileimg']);
				}
			}
		}
		$this->_helper->json($output);
	}
	
	public function servicedeskreportAction()
	{
		$norec_arr = array();		
		$form = new Default_Form_Servicedeskreport();		
		$servicedeskdepartmentmodel = new Default_Model_Servicedeskdepartment();
		$assetcategorymodel = new Assets_Model_AssetCategories();		
		
		$sdDepts = $servicedeskdepartmentmodel->getSDDepartmentData();
		$activeAssetCategories = $assetcategorymodel->getActiveAssetCategory();
		if(count($sdDepts)==0 && count($activeAssetCategories)==0)
			$norec_arr['service_desk_id'] = "Categories are not configured yet.";
		else {
			if(!empty($sdDepts)) {
				foreach($sdDepts as $option)
					$form->service_desk_id->addMultiOption($option['id'].'_1', $option['service_desk_name'].'- Service');
			}	
			if(!empty($activeAssetCategories)) {
				foreach($activeAssetCategories as $assetoption)
					$form->service_desk_id->addMultiOption($assetoption['id'].'_2', $assetoption['name'].'- Asset');
			}
		}		
		
		$this->view->form = $form;
		$this->view->messages = $norec_arr;
	}
	
	public function getsddataAction()
	{
		$param_arr = $this->_getAllParams();
		$cols_param_arr = $this->_getParam('cols_arr',array());
		if(isset($param_arr['cols_arr']))
		unset($param_arr['cols_arr']);
		$page_no = isset($param_arr['page_no'])?$param_arr['page_no']:1;
		$per_page = isset($param_arr['per_page'])?$param_arr['per_page']:PERPAGE;
		$sort_name = $param_arr['sort_name'];
		$sort_type = $param_arr['sort_type'];
		if(isset($param_arr['page_no']))unset($param_arr['page_no']);
		if(isset($param_arr['sort_name']))unset($param_arr['sort_name']);
		if(isset($param_arr['sort_type']))unset($param_arr['sort_type']);
		if(isset($param_arr['per_page']))unset($param_arr['per_page']);
		unset($param_arr['module']);unset($param_arr['controller']);unset($param_arr['action']);unset($param_arr['format']);
		$request_for='';
		if(!empty($param_arr['service_desk_id'])) {
			$service_desk_id = explode('_', $param_arr['service_desk_id']);
			$param_arr['service_desk_id'] = $service_desk_id[0];
			$request_for = $service_desk_id[1]; 
		}
		
		$reports_model = new Default_Model_Reports();
		$sd_data = $reports_model->get_sd_report($param_arr, $per_page, $page_no, $sort_name, $sort_type,$request_for);
		$page_cnt = $sd_data['page_cnt'];
		$sd_data_arr = $sd_data['rows'];		
		
		$columns_array = $this->sdreportcolumns('all');
		
		$mandatory_array = $this->sdreportcolumns('mandatory');
		
		if(count($cols_param_arr) == 0)
		$cols_param_arr = $mandatory_array;
		$mandatory_array = array_keys($mandatory_array);
		
		$this->view->sd_data_arr = $sd_data_arr;
		$this->view->page_cnt = $page_cnt;
		$this->view->per_page = $per_page;
		$this->view->page_no = $page_no;
		$this->view->cols_param_arr = $cols_param_arr;
		$this->view->sort_name = $sort_name;
		$this->view->sort_type = $sort_type;
		$this->view->columns_array = $columns_array;
	}
	
	public function sdreportcolumns($type)
	{
		$columns_array = array(
						'ticket_number' => 'Ticket#',
						'raised_by_name' => 'Raised by',
						'createddate' => 'Raised On',
                        'service_desk_name' => 'Category',
                        'service_request_name' => 'Request Type/Asset Name',
                        'priority' => 'Priority',
                        'description' => 'Description',
                        'status' => 'Status',
                        'executor_name' => 'Executor',
                        'executor_comments' => 'Executor Comments',
                        'reporting_manager_name' => 'Reporting Manager',
						'reporting_manager_status' => 'Reporting Manager Status',
						'approver_1_name' => 'Approver 1',
                        'approver_status_1' => 'Approver 1 Status',
						'approver_2_name' => 'Approver 2',
                        'approver_status_2' => 'Approver 2 Status',
						'approver_3_name' => 'Approver 3',
                        'approver_status_3' => 'Approver 3 Status',
		);
		$mandatory_array = array(
						'ticket_number' => 'Ticket#',
						'raised_by_name' => 'Raised by',
						'createddate' => 'Raised On',
                        'service_desk_name' => 'Category',
                        'service_request_name' => 'Request Type/Asset Name',
                        'priority' => 'Priority',
                        'description' => 'Description',
                        'status' => 'Status',
		);
		if($type == 'all')
			return $columns_array;
		else
			return $mandatory_array;
	}
	
	public function servicedeskpdfAction()
	{
		$this->_helper->layout->disableLayout();
		$param_arr = $this->_getAllParams();

		$cols_param_arr = $this->_getParam('cols_arr',array());
		if(isset($param_arr['cols_arr']))	unset($param_arr['cols_arr']);
		$page_no = isset($param_arr['page_no'])?$param_arr['page_no']:1;
		$per_page = isset($param_arr['per_page'])?$param_arr['per_page']:PERPAGE;
		$sort_name = $param_arr['sort_name'];
		$sort_type = $param_arr['sort_type'];
		if(isset($param_arr['page_no']))unset($param_arr['page_no']);
		if(isset($param_arr['sort_name']))unset($param_arr['sort_name']);
		if(isset($param_arr['sort_type']))unset($param_arr['sort_type']);
		if(isset($param_arr['per_page']))unset($param_arr['per_page']);
		unset($param_arr['module']);unset($param_arr['controller']);unset($param_arr['action']);
		
		$request_for='';
		if(!empty($param_arr['service_desk_id'])) {
			$service_desk_id = explode('_', $param_arr['service_desk_id']);
			$param_arr['service_desk_id'] = $service_desk_id[0];
			$request_for = $service_desk_id[1]; 
		}

		if(count($cols_param_arr) == 0)
			$cols_param_arr = $this->sdreportcolumns('mandatory');
		
		$reports_model = new Default_Model_Reports();
		$sd_data = $reports_model->get_sd_report($param_arr, $per_page, $page_no, $sort_name, $sort_type,$request_for);
		$sd_arr = $sd_data['rows'];
		
		for($x=0; $x<sizeof($sd_arr); $x++){
			if(array_key_exists("priority",$sd_arr[$x])){
				$pri = '';
	            if($sd_arr[$x]['priority'] == 1)
	            	$pri='Low';
	            elseif($sd_arr[$x]['priority'] == 2)
	            	$pri='Medium';
	            else
	            	$pri='High';
	            $sd_arr[$x]['priority'] = $pri;
			}
		}
		
		$field_names = array();
		$field_widths = array();
		$data['field_name_align'] = array();

		foreach($cols_param_arr as $column_key => $column_name)
		{
			$field_names[] = array(
									'field_name'=>$column_key,
                                    'field_label'=>$column_name
			);
			$field_widths[] = 25;
			$data['field_name_align'][] = 'C';
		}
		if(count($cols_param_arr) != 7)
		{
			$totalPresentFieldWidth = array_sum($field_widths);
			foreach($field_widths as $key => $width)
			{
				$field_widths[$key] = ($width*180)/$totalPresentFieldWidth;
			}
		}
		$data = array('grid_no'=>1, 'project_name'=>'', 'object_name'=>'Service Request Management Report', 'grid_count'=>1,'file_name'=>'ServiceDeskReport.pdf');

		$pdf = $this->_helper->PdfHelper->generateReport($field_names, $sd_arr, $field_widths, $data);

		$this->_helper->json(array('file_name'=>$data['file_name']));
	}
	
	public function servicedeskexcelAction()
	{
		$this->_helper->layout->disableLayout();
		$param_arr = $this->_getAllParams();

		$cols_param_arr = $this->_getParam('cols_arr',array());
		if(isset($param_arr['cols_arr']))	unset($param_arr['cols_arr']);
		$page_no = isset($param_arr['page_no'])?$param_arr['page_no']:1;
		$per_page = isset($param_arr['per_page'])?$param_arr['per_page']:PERPAGE;
		$sort_name = $param_arr['sort_name'];
		$sort_type = $param_arr['sort_type'];
		if(isset($param_arr['page_no']))unset($param_arr['page_no']);
		if(isset($param_arr['sort_name']))unset($param_arr['sort_name']);
		if(isset($param_arr['sort_type']))unset($param_arr['sort_type']);
		if(isset($param_arr['per_page']))unset($param_arr['per_page']);
		unset($param_arr['module']);unset($param_arr['controller']);unset($param_arr['action']);
		
		$request_for='';
		if(!empty($param_arr['service_desk_id'])) {
			$service_desk_id = explode('_', $param_arr['service_desk_id']);
			$param_arr['service_desk_id'] = $service_desk_id[0];
			$request_for = $service_desk_id[1]; 
		}

		if(count($cols_param_arr) == 0)
			$cols_param_arr = $this->sdreportcolumns('mandatory');
		
		$reports_model = new Default_Model_Reports();
		$sd_model_data = $reports_model->get_sd_report($param_arr, $per_page, $page_no, $sort_name, $sort_type,$request_for);
		$sd_arr = $sd_model_data['rows'];

		require_once 'Classes/PHPExcel.php';
		require_once 'Classes/PHPExcel/IOFactory.php';
		$objPHPExcel = new PHPExcel();

		$letters = range('A','Z');
		$count =0;
		$filename = "ServiceDeskReport.xlsx";
		$cell_name="";
			
		// Make first row Headings bold and highlighted in Excel.
		foreach ($cols_param_arr as $names)
		{
			$i = 1;
			$cell_name = $letters[$count].$i;
			$names = html_entity_decode($names,ENT_QUOTES,'UTF-8');

			$objPHPExcel->getActiveSheet()->SetCellValue($cell_name, $names);
			// Make bold cells
			$objPHPExcel->getActiveSheet()->getStyle($cell_name)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle($cell_name)->applyFromArray( array(
									        'fill' => array(
									            'type' => PHPExcel_Style_Fill::FILL_SOLID,
									            'color' => array('rgb' => '82CAFF')
			)
			)
			);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letters[$count])->setAutoSize(true);
			$i++;
			$count++;
		}

		// Display field/column values in Excel.
		$i = 2;
		foreach($sd_arr as $sd_data)
		{
			$count1 =0;
			foreach ($cols_param_arr as $column_key => $column_name)
			{
				// display field/column values
				$cell_name = $letters[$count1].$i;


				if($column_key == 'priority')
				{
					$pri = '';
	            	if($sd_data['priority'] == 1)
	            		$pri='Low';
	            	elseif($sd_data['priority'] == 2)
	            		$pri='Medium';
	            	else
	            		$pri='High';
					$value = isset($sd_data['priority'])?$pri:"";
				}
				else
				{
					$value = isset($sd_data[$column_key])?$sd_data[$column_key]:"";
				}
				$value = html_entity_decode($value,ENT_QUOTES,'UTF-8');
				$objPHPExcel->getActiveSheet()->SetCellValue($cell_name, $value);
				$count1++;
			}
			$i++;
		}
			
		sapp_Global::clean_output_buffer();
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header('Cache-Control: max-age=0');
		sapp_Global::clean_output_buffer();
			
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');

		exit;
	}
	
	public function employeereportAction()
	{
            $norec_arr = array();
            $form = new Default_Form_Employeereport();
            $requi_model = new Default_Model_Requisition();
            $employmentstatusModel = new Default_Model_Employmentstatus();
         
            $role_model = new Default_Model_Roles();
            $departmentsmodel = new Default_Model_Departments();
            $bu_model = new Default_Model_Businessunits();

            $roles_arr = $role_model->getRolesList_EMP();
            $job_data = $requi_model->getJobTitleList();
            $employmentStatusData = $employmentstatusModel->getempstatuslist();
            
            if(count($job_data)==0)
            {
                $norec_arr['jobtitle_id'] = "Job titles are not configured yet.";
                $norec_arr['position_id'] = "Positions are not configured yet.";
            }
            if(count($employmentStatusData)==0)
            {
                $norec_arr['emp_status_id'] = "Employment status is not configured yet.";
            }
            $form->jobtitle_id->addMultiOptions(array(''=>'Select Job Title')+$job_data);
            if(count($employmentStatusData) > 0)
            {
				$form->emp_status_id->addMultiOption('','Select Employment Status');
				foreach ($employmentStatusData as $employmentStatusres)
				{
						$form->emp_status_id->addMultiOption($employmentStatusres['workcodename'],$employmentStatusres['statusname']);
				}
            }
            if(sizeof($roles_arr) > 0)
            {
                    $form->emprole->addMultiOptions(array(''=>'Select Role')+$roles_arr);
            }
            else
            {
                    $norec_arr['emprole'] = 'Roles are not added yet.';
            }
                        
            $bu_arr = $bu_model->getBU_report();
            if(!empty($bu_arr))
            {
                foreach ($bu_arr as $bu)
                {
                    $form->businessunit_id->addMultiOption($bu['id'],utf8_encode($bu['bu_name']));
                }
            }
            else
            {
                $norec_arr['businessunit_id'] = 'Business Units are not added yet.';
            }
            
            $this->view->form = $form;
            $this->view->messages = $norec_arr;
	}
        
        public function getdeptsempAction()
        {
            $bu_id = $this->_getParam('bu_id',null);
            $options = "";
            if(!empty($bu_id))
            {
                $bu_id = implode(',', $bu_id);
                
                $dept_model = new Default_Model_Departments();
                $dept_data = $dept_model->getDepartmentWithCodeList_bu($bu_id);
                if(!empty($dept_data))
                {
                    foreach($dept_data as $dept)
                    {
                        $options .= sapp_Global::selectOptionBuilder($dept['id'], $dept['unitcode']." ".$dept['deptname']);
                    }
                }
            }
            $this->_helper->json(array('options' => $options));
        }
	/**
	 * @name indexAction
	 *
	 * This method is used to display the login form and to display form errors based on given inputs
	 *
	 *  @author Mainak
	 *  @version 1.0
	 */

	public function indexAction(){

		
		$repModel = new Default_Model_Reports();
		$reqStats = $repModel->getRequisitionStats();
		$loginscountStr = $daysStr = '';
		$initializedCount = 0;

		/**  Requisition Stats **/
		$reqStatsStr = '[]';
		if($reqStats)
		{
			$initializedCount = $reqStats[0]['initiated_req'] + $reqStats[0]['approved_req'] + $reqStats[0]['rejected_req'] + $reqStats[0]['closed_req'] + $reqStats[0]['onhold_req'] + $reqStats[0]['complete_req'] + $reqStats[0]['inprocess_req'];
                        if($initializedCount > 0)
                            $reqStatsStr = '["New",'.$reqStats[0]['initiated_req'].'],["Approved",'.$reqStats[0]['approved_req'].'],["Rejected",'.$reqStats[0]['rejected_req'].'],["Closed",'.$reqStats[0]['closed_req'].'],["On-hold",'.$reqStats[0]['onhold_req'].'],["Complete",'.$reqStats[0]['complete_req'].'],["In progress",'.$reqStats[0]['inprocess_req'].']';
                        else 
                            $reqStatsStr = '';
		}

		/**  User logins by day **/
		$userloginStats = $repModel->getUserloginStats();
		if($userloginStats)
		{
                    $k = 0;
                    for($i = 0; $i < sizeof($userloginStats); $i++)
                    {
                        if($userloginStats[$i]['cnt'] == 0) $k++; 
                        $loginscountStr.= $userloginStats[$i]['cnt'];
                        $daysStr.= $userloginStats[$i]['logindate'];

                        if($i < (sizeof($userloginStats) - 1))
                        {
                            $loginscountStr.= ',';
                            $daysStr.= ',';
                        }
                    }
                    if($k == count($userloginStats))
                    {
                        $loginscountStr = $daysStr = "";
                    }
		}

		/**  Employee Stats **/
		$empStats = $repModel->getEmpStats();
		$empStatsStr = '[]';
		if($empStats)
		{
                    $new_empstats = $empStats[0];
                    $stat_cnt = array_sum($new_empstats);
                    if($stat_cnt > 0)                        
                        $empStatsStr = '["Active",'.$empStats[0]['active'].'],["In-active",'.$empStats[0]['inactive'].'],["Left",'.$empStats[0]['left'].'],["Resigned",'.$empStats[0]['resigned'].'],["Suspended",'.$empStats[0]['suspended'].']';
                    else 
			$empStatsStr = "";
		}

		/**  Employee leaves by day **/
		$empLeavesByDay = $repModel->getEmpLeavesByDay();

		$empLeavesStr = ''; $empLeaveDaysStr = '';
		if($empLeavesByDay)
		{
                    $k = 0;
                    for($i = 0; $i < sizeof($empLeavesByDay); $i++)
                    {
                        if($empLeavesByDay[$i]['cnt'] == 0) $k++; 
                        $empLeavesStr.= $empLeavesByDay[$i]['cnt'];
                        $empLeaveDaysStr.= $empLeavesByDay[$i]['theday'];

                        if($i < (sizeof($empLeavesByDay) - 1))
                        {
                            $empLeavesStr.= ',';
                            $empLeaveDaysStr.= ',';
                        }
                    }
                    if($k == count($userloginStats))
                    {
                        $empLeaveDaysStr = $empLeavesStr = "";
                    }
		}

		/** Activity log by date **/
		$activityByDate = $repModel->getActivityByDate();
		$addActivityStr = $editActivityStr = $deleteActivityStr = '';
		if($activityByDate)
		{
			for($i = 0; $i < sizeof($activityByDate); $i++)
			{
				if($activityByDate[$i]['user_action'] == 1)
				{
					$addActivityStr .= '['.$activityByDate[$i]['theday'].','.$activityByDate[$i]['cnt'].'],';

				}
				else if($activityByDate[$i]['user_action'] == 2)
				{
					$editActivityStr .= '['.$activityByDate[$i]['theday'].','.$activityByDate[$i]['cnt'].'],';

				}
				else if($activityByDate[$i]['user_action'] == 3)
				{
					$deleteActivityStr .= '['.$activityByDate[$i]['theday'].','.$activityByDate[$i]['cnt'].'],';
				}

			}

			$addActivityStr = rtrim($addActivityStr, ",");
			$editActivityStr = rtrim($editActivityStr, ",");
			$deleteActivityStr = rtrim($deleteActivityStr, ",");
		}

		/** Emp by department **/
		$empDeptRes = $repModel->getEmpByDepartment();

		$empDeptStr = array();
		if($empDeptRes)
		{
			for($i =0; $i < sizeof($empDeptRes); $i++)
			{
				$empDeptStr[]= '["'.$empDeptRes[$i]['deptcode'].'",'.$empDeptRes[$i]['cnt'].']';
			}
		}				
		$empDeptStr = implode(',', $empDeptStr);
		
	    /**  Attrition Chart **/
		$empLeftCountStr = $yearsStr = '';
	    $aterationStats = $repModel->getAterationStats();
		
		$currentYear = date('Y');
		$pastfifthYear = date('Y')- 4;
		if($aterationStats)
		{
            $k = 0;
            for($i = 0; $i < 5; $i++)
            { 
              $flag = 0;
              foreach($aterationStats as $record){
                if($record['yeear'] == $pastfifthYear){
                  $empLeftCountStr.= $record['cnt'];
                  $yearsStr.= $record['yeear'];
                  $flag = 1;	
                }
              }
            
              if($flag == 0){
                $k++;
                $empLeftCountStr.= 0;
                $yearsStr.= $pastfifthYear;
              }
              if($i < 4)
              {
                 $empLeftCountStr.= ',';
                 $yearsStr.= ',';
              }
              $pastfifthYear = $pastfifthYear + 1;
            }
            if($k == count($aterationStats))
            {
               $empLeftCountStr = $yearsStr = "";
            }
		}
				
		$this->view->initializedCount = $initializedCount;
		$this->view->reqStatsStr = $reqStatsStr;
		$this->view->loginscountStr = $loginscountStr;
		$this->view->empStatsStr = $empStatsStr;
		$this->view->daysStr = $daysStr;
		$this->view->empLeavesStr = $empLeavesStr;
		$this->view->empLeaveDaysStr = $empLeaveDaysStr;
		$this->view->addActivityStr = $addActivityStr;
		$this->view->editActivityStr = $editActivityStr;
		$this->view->deleteActivityStr = $deleteActivityStr;
		$this->view->empDeptStr = $empDeptStr;
		$this->view->empLeftCountStr = $empLeftCountStr;
		$this->view->yearsStr = $yearsStr;
	}

	public function holidaygroupreportsAction()
	{
		$reportsmodel = new Default_Model_Reports();
		$pageno = $this->_request->getParam('pageno',1);
		$perpage = intval($this->_request->getParam('perpage',PERPAGE));
		if($perpage == 0)
		$perpage = PERPAGE;
		$by = $this->_request->getParam('by','Desc');
		$sortby = $this->_request->getParam('sortby','g.modifieddate');
		$columnby = $this->_request->getParam('columnby');
		$columnsortby = $this->_request->getParam('columnsortby');
		if($columnby !='')
		$by = $columnby;
		if($columnsortby !='')
		$sortby = $columnsortby;

		$getHolidayAndEmpResult = $reportsmodel->getEmpHolidayResult($sortby,$by,$pageno,$perpage);
		$getHolidayAndEmpCount = $reportsmodel->getEmpHolidayCount();
		if($getHolidayAndEmpCount > 0)
		{
			$totalcount = $getHolidayAndEmpCount;
			$lastpage =  ceil($totalcount/$perpage);
		}
		else
		{
			$totalcount = '';
			$lastpage = '';
		}
		$this->view->holidayreport = $getHolidayAndEmpResult;
		$this->view->holidayreportcount = $getHolidayAndEmpCount;
		$this->view->pageno = $pageno;
		$this->view->perpage = $perpage;
		$this->view->by = $by;
		$this->view->sortby = $sortby;
		$this->view->totalcount = $totalcount;
		$this->view->lastpage = $lastpage;
	}

	public function getpdfreportholidayAction()
	{
		$this->_helper->layout->disableLayout();
		$param_arr = $this->_getAllParams();
		if(isset($param_arr['cols_arr']))
		unset($param_arr['cols_arr']);
		$page_no = isset($param_arr['pageno'])?intval($param_arr['pageno']):1;
		$per_page = isset($param_arr['perpage'])?intval($param_arr['perpage']):PERPAGE;
		if($per_page == 0)
		$per_page = PERPAGE;
		if(isset($param_arr['columnsortby']) && $param_arr['columnsortby'] !='')
		$sort_name = $param_arr['columnsortby'];
		else
		$sort_name = $param_arr['sortby'];

		if(isset($param_arr['columnby']) && $param_arr['columnby'] !='')
		$sort_type = $param_arr['columnby'];
		else
		$sort_type = $param_arr['by'];

		$headersize = $param_arr['headersize'];
			
		$reportsmodel = new Default_Model_Reports();

		$cols_param = array('groupname','dates','employee');
		$selectColumns = $cols_param;
			
		$getHolidayAndEmpResult = $reportsmodel->getEmpHolidayResult($sort_name,$sort_type,$page_no,$per_page);
		$field_names = array();
		$field_widths = array();
		$fieldwidth = '';
		$data['field_name_align'] = array();
			
		foreach($selectColumns as $col){
			switch($col){
				case 'groupname':
					$field_names[] = array(
											'field_name'=>'groupname',
											'field_label'=>'Holiday Group'
											);
											$field_widths[] = 60;
											$data['field_name_align'][] = 'C';
											break;
				case 'dates' : $field_names[] = array(
											'field_name'=>'datecount',
											'field_label'=>'Holiday Count'
											);
											$field_widths[] = 60;
											$data['field_name_align'][] = 'C';
											break;
				case 'employee' : $field_names[] = array(
										'field_name'=>'empcount',
										'field_label'=>'Employee Count'
										);
										$field_widths[] = 60;
										$data['field_name_align'][] = 'C';
										break;
											
			}
		}


		if(count($selectColumns) != $headersize){
			$totalPresentFieldWidth = 	array_sum($field_widths);
			foreach($field_widths as $key => $width){
				$field_widths[$key] = ($width*180)/$totalPresentFieldWidth;
			}
		}
		$data = array('grid_no'=>1, 'project_name'=>'', 'object_name'=>'Holiday Report', 'grid_count'=>1,'file_name'=>'holidayreport.pdf');
		$pdf = $this->_helper->PdfHelper->generateReport($field_names, $getHolidayAndEmpResult, $field_widths, $data);
		return $this->_helper->json(array('file_name'=>$data['file_name']));
	}

	public function getexcelreportholidayAction()
	{
		$this->_helper->layout->disableLayout();
		$param_arr = $this->_getAllParams();
			
		$page_no = isset($param_arr['pageno'])?intval($param_arr['pageno']):1;
		$per_page = isset($param_arr['perpage'])?intval($param_arr['perpage']):PERPAGE;
		if($per_page == 0)
		$per_page = PERPAGE;

		if(isset($param_arr['columnsortby']) && $param_arr['columnsortby'] !='')
		$sort_name = $param_arr['columnsortby'];
		else
		$sort_name = $param_arr['sortby'];

		if(isset($param_arr['columnby']) && $param_arr['columnby'] !='')
		$sort_type = $param_arr['columnby'];
		else
		$sort_type = $param_arr['by'];

		$reportsmodel = new Default_Model_Reports();

		$cols_param_arr = array('groupname'=>'Holiday Group',
	                         'dates'=>'Holiday Count',
							 'employee'=>'Employee Count');

		$getHolidayAndEmpResult = $reportsmodel->getEmpHolidayResult($sort_name,$sort_type,$page_no,$per_page);
		$emp_arr = $getHolidayAndEmpResult;
		require_once 'Classes/PHPExcel.php';
		require_once 'Classes/PHPExcel/IOFactory.php';
		$objPHPExcel = new PHPExcel();

		$letters = range('A','Z');
		$count =0;
		$filename = "HolidayReport.xlsx";
		$cell_name="";
			
		// Make first row Headings bold and highlighted in Excel.
		foreach ($cols_param_arr as $names)
		{
			$i = 1;
			$cell_name = $letters[$count].$i;
			$names = html_entity_decode($names,ENT_QUOTES,'UTF-8');

			$objPHPExcel->getActiveSheet()->SetCellValue($cell_name, $names);
			// Make bold cells
			$objPHPExcel->getActiveSheet()->getStyle($cell_name)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle($cell_name)->applyFromArray( array(
									        'fill' => array(
									            'type' => PHPExcel_Style_Fill::FILL_SOLID,
									            'color' => array('rgb' => '82CAFF')
			)
			)
			);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letters[$count])->setAutoSize(true);
			$i++;
			$count++;
		}

		// Display field/column values in Excel.
		if(!empty($emp_arr))
		{
			$i = 2;
			foreach($emp_arr as $emp_data)
			{
				$count1 =0;
				foreach ($cols_param_arr as $column_key => $column_name)
				{
					// display field/column values
					$cell_name = $letters[$count1].$i;


					if($column_key == 'dates')
					{
						$value = isset($emp_data['datecount'])?$emp_data['datecount']:"";
					}
					else if($column_key == 'employee')
					{
						$value = isset($emp_data['empcount'])?$emp_data['empcount']:"";
					}
					else
					{
						$value = isset($emp_data[$column_key])?$emp_data[$column_key]:"";
					}
					$value = html_entity_decode($value,ENT_QUOTES,'UTF-8');
					$objPHPExcel->getActiveSheet()->SetCellValue($cell_name, $value);
					$count1++;
				}
				$i++;
			}
		}

		sapp_Global::clean_output_buffer();
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header('Cache-Control: max-age=0');
		sapp_Global::clean_output_buffer();
			
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');

		exit;

	}

	public function leavesreportAction()
	{
		$reportsmodel = new Default_Model_Reports();
		$departmentsmodel = new Default_Model_Departments();
		$leavestatusform = new Default_Form_leavereport();
		$msgarray = array();
		$selectColumns =array();
		$searchQuery = '';
			
		$employeename = $this->_request->getParam('employeename');
		$department = $this->_request->getParam('department');
		$leavestatus = $this->_request->getParam('leavestatus');
		$from_date = $this->_request->getParam('from_date');
		$pageno = $this->_request->getParam('pageno',1);
		$perpage = intval($this->_request->getParam('perpage',PERPAGE));
		if($perpage == 0)
		$perpage = PERPAGE;
		$by = $this->_request->getParam('by','Desc');
		$sortby = $this->_request->getParam('sortby','l.modifieddate');
		$columnby = $this->_request->getParam('columnby');
		$columnsortby = $this->_request->getParam('columnsortby');
		$checkedheaders = $this->_request->getParam('checkedheaders');
		$generatereport = $this->_request->getParam('generatereport');
		if($generatereport == 'pdf' || $generatereport == 'excel')
		$employeename = $this->_request->getParam('hiddenemployeename');
		if($checkedheaders != '')
		$selectColumns = explode(',',$checkedheaders);
			
		if($columnby !='')
		$by = $columnby;
		if($columnsortby !='')
		$sortby = $columnsortby;

		if($employeename !='')
		$searchQuery .= 'l.user_id = "'.$employeename.'" AND ';
		if($department !='')
		$searchQuery .= 'l.department_id = "'.$department.'" AND ';
		if($leavestatus !='')
		$searchQuery .= 'l.leavestatus = '.$leavestatus.' AND ';
		if($from_date !='')
		{
			$from_date = sapp_Global::change_date($from_date,'database');
			$searchQuery .= 'DATE(l.createddate) = "'.$from_date.'" AND ';
		}
		$searchQuery = rtrim($searchQuery," AND");
			
		$leavestatusArr = $reportsmodel->getEmpLeaveHistory($sortby, $by,$pageno,$perpage,$searchQuery);
		$leavestatusCount = $reportsmodel->getEmpLeaveHistoryCount($searchQuery);
		$departmentlistArr = $departmentsmodel->getDepartmentWithCodeList();
			
		if(!empty($departmentlistArr))
		{
			foreach ($departmentlistArr as $departmentlistres){
				$leavestatusform->department->addMultiOption($departmentlistres['id'],utf8_encode($departmentlistres['unitcode'].$departmentlistres['deptname']));

			}
		}
		else
		{
			$msgarray['department'] = 'Departments are not added yet.';
		}

		if($leavestatusCount[0]['count'] > 0)
		{
			$totalcount = $leavestatusCount[0]['count'];
			$lastpage =  ceil($totalcount/$perpage);
		}
		else
		{
			$totalcount = '';
			$lastpage = '';
		}

		$selectColumnLabels = array();
		$leavesheaderarr = array('employeename'=>'Leave Applied By',
	                         'leavetype'=>'Leave Type',
							 'leaveday'=>'Leave Duration',
							 'leavestatus'=>'Leave Status',
							 'deptname'=>'Department',
							 'from_date'=>'From Date',
							 'to_date'=>'To Date',
							 'reason'=>'Reason',
							 'approver_comments'=>'Comments',
							 'reportingmanagername'=>'Reporting Manager',
							 'appliedleavescount'=>'Leave Count',
							 'applieddate'=>'Applied On');		
			
		if(empty($selectColumns))
		{
			$selectColumns = array('employeename','leavetype','leaveday','leavestatus','deptname','from_date',
				'to_date','reason','approver_comments','reportingmanagername','appliedleavescount','applieddate');		
			$selectColumnLabels = $leavesheaderarr;
		}
		else
		{
			foreach($leavesheaderarr as $key=>$val)
			{
				foreach($selectColumns as $column)
				{
					if($column == $key)
					$selectColumnLabels[$key] = $val;
				}
			}

		}
			
		$this->view->selectColumnLabels = $selectColumnLabels;
		$this->view->leavesheaderarr = $leavesheaderarr;
		$this->view->leavestatusArr = $leavestatusArr;
		$this->view->totalCount = $totalcount;
		$this->view->pageno = $pageno;
		$this->view->perpage = $perpage;
		$this->view->by = $by;
		$this->view->sortby = $sortby;
		$this->view->totalcount = $totalcount;
		$this->view->lastpage = $lastpage;
		$this->view->msgarray = $msgarray;
		$this->view->form = $leavestatusform;
	}

	public function getpdfreportleavesAction()
	{
		$this->_helper->layout->disableLayout();
		$param_arr = $this->_getAllParams();
		if(isset($param_arr['cols_arr']))
		unset($param_arr['cols_arr']);
		$page_no = isset($param_arr['pageno'])?intval($param_arr['pageno']):1;
		$per_page = isset($param_arr['perpage'])?intval($param_arr['perpage']):PERPAGE;
		if($per_page == 0)
		$per_page = PERPAGE;
		$sort_name = $this->_getParam('columnsortby','l.modifieddate');
		$sort_type = $this->_getParam('columnby','DESC');
		if(isset($param_arr['hiddenemployeename']))
		$employeename = $param_arr['hiddenemployeename'];
		else
		$employeename = '';

		$department = $param_arr['department'];
		$leavestatus = $param_arr['leavestatus'];
		$from_date = $param_arr['from_date'];
		$headersize = $param_arr['headersize'];
		$cols_param = array();
		$searchQuery = '';
		$reportsmodel = new Default_Model_Reports();

		$cols_param = explode(',',$param_arr['checkedheaders']);
		$selectColumns = $cols_param;
		$leavesheaderarr = array('employeename'=>'Leave Applied By',
	                         'leavetype'=>'Leave Type',
							 'leaveday'=>'Leave Duration',
							 'leavestatus'=>'Leave Status',
							 'deptname'=>'Department',
							 'from_date'=>'From Date',
							 'to_date'=>'To Date',
							 'reason'=>'Reason',
							 'reportingmanagername'=>'Reporting Manager',
							 'appliedleavescount'=>'Leave Count',
							 'applieddate'=>'Applied On');
		$cols_param_arr = array();
		foreach($leavesheaderarr as $key=>$val)
		{
			foreach( $cols_param  as $col)
			{
				if($col == $key)
				$cols_param_arr[$key] = $val;
			}
		}
			
			
		if($employeename !='')
		$searchQuery .= 'l.user_id = "'.$employeename.'" AND ';
		if($department !='')
		$searchQuery .= 'l.department_id = "'.$department.'" AND ';
		if($leavestatus !='')
		$searchQuery .= 'l.leavestatus = '.$leavestatus.' AND ';
		if($from_date !='')
		{
			$from_date = sapp_Global::change_date($from_date,'database');
			$searchQuery .= 'DATE(l.createddate) = "'.$from_date.'" AND ';
		}
		$searchQuery = rtrim($searchQuery," AND");
		$leavestatusArr = $reportsmodel->getEmpLeaveHistory($sort_name, $sort_type,$page_no,$per_page,$searchQuery);
		$this->generateLeaveHistoryPDF($leavestatusArr,$selectColumns,$headersize);
			
	}

	public function generateLeaveHistoryPDF($finalArray,$selectColumns,$headersize)
	{
		$field_names = array();
		$field_widths = array();
		$fieldwidth = '';
		$data['field_name_align'] = array();
			
		foreach($selectColumns as $col){
			switch($col){
				case 'employeename':
					$field_names[] = array(
										'field_name'=>'user_name',
										'field_label'=>'Leave Applied By'
										);
										$field_widths[] = 20;
										$data['field_name_align'][] = 'C';
										break;
				case 'leavetype' : $field_names[] = array(
										'field_name'=>'leavetype_name',
										'field_label'=>'Leave Type'
										);
										$field_widths[] = 20;
										$data['field_name_align'][] = 'C';
										break;
				case 'leaveday' : $field_names[] = array(
									'field_name'=>'leaveday',
									'field_label'=>'Leave Duration'
									);
									$field_widths[] = 20;
									$data['field_name_align'][] = 'C';
									break;
				case 'leavestatus': $field_names[] = array(
									'field_name'=>'leavestatus',
									'field_label'=>'Leave Status'
									);
									$field_widths[] = 20;
									$data['field_name_align'][] = 'C';
									break;
				case 'deptname': $field_names[] = array(
										'field_name'=>'department_name',
										'field_label'=>'Department'
										);
										$field_widths[] = 12;
										$data['field_name_align'][] = 'C';
										break;
				case 'from_date':
					$field_names[] = array(
										'field_name'=>'from_date',
										'field_label'=>'From Date'
										);
										$field_widths[] = 12;
										$data['field_name_align'][] = 'C';
										break;
				case 'to_date': $field_names[] = array(
										'field_name'=>'to_date',
										'field_label'=>'To Date'
										);
										$field_widths[] = 12;
										$data['field_name_align'][] = 'C';
										break;
				case 'reason': $field_names[] = array(
										'field_name'=>'reason',
										'field_label'=>'Reason'
										);
										$field_widths[] = 20;
										$data['field_name_align'][] = 'C';
										break;
				case 'reportingmanagername': $field_names[] = array(
										'field_name'=>'rep_manager_name',
										'field_label'=>'Reporting Manager'
										);
										$field_widths[] = 20;
										$data['field_name_align'][] = 'C';
										break;
				case 'appliedleavescount': $field_names[] = array(
										'field_name'=>'appliedleavescount',
										'field_label'=>'Leave Count'
										);
										$field_widths[] = 12;
										$data['field_name_align'][] = 'C';
										break;
				case 'applieddate': $field_names[] = array(
										'field_name'=>'applieddate',
										'field_label'=>'Applied On'
										);
										$field_widths[] = 12;
										$data['field_name_align'][] = 'C';
										break;
			}
		}

		if(count($selectColumns) != $headersize){
			$totalPresentFieldWidth = 	array_sum($field_widths);
			foreach($field_widths as $key => $width){
				$field_widths[$key] = ($width*180)/$totalPresentFieldWidth;
			}
		}
			
		$data = array('grid_no'=>1, 'project_name'=>'', 'object_name'=>'Leaves Report', 'grid_count'=>1,'file_name'=>'leavesreport.pdf');
			
			
		$pdf = $this->_helper->PdfHelper->generateReport($field_names, $finalArray, $field_widths, $data);
		return $this->_helper->json(array('file_name'=>$data['file_name']));

	}

	public function getexcelreportleavesAction()
	{
		$this->_helper->layout->disableLayout();
		$param_arr = $this->_getAllParams();
		if(isset($param_arr['cols_arr']))
		unset($param_arr['cols_arr']);
		$page_no = isset($param_arr['pageno'])?intval($param_arr['pageno']):1;
		$per_page = isset($param_arr['perpage'])?intval($param_arr['perpage']):PERPAGE;
		if($per_page == 0)
		$per_page = PERPAGE;
		$sort_name = $this->_getParam('columnsortby','l.modifieddate');
		$sort_type = $this->_getParam('columnby','DESC');
		$employeename = $param_arr['hiddenemployeename'];
		$department = $param_arr['department'];
		$leavestatus = $param_arr['leavestatus'];
		$from_date = $param_arr['from_date'];
			
		$searchQuery = '';
		$reportsmodel = new Default_Model_Reports();

		$cols_param = explode(',',$param_arr['checkedheaders']);
		$leavesheaderarr = array('employeename'=>'Leave Applied By',
	                         'leavetype'=>'Leave Type',
							 'leaveday'=>'Leave Duration',
							 'leavestatus'=>'Leave Status',
							 'deptname'=>'Department',
							 'from_date'=>'From Date',
							 'to_date'=>'To Date',
							 'reason'=>'Reason',
							 'reportingmanagername'=>'Reporting Manager',
							 'appliedleavescount'=>'Leave Count',
							 'applieddate'=>'Applied On');
		$cols_param_arr = array();
		foreach($leavesheaderarr as $key=>$val)
		{
			foreach( $cols_param  as $col)
			{
				if($col == $key)
				$cols_param_arr[$key] = $val;
			}
		}
			
			
		if($employeename !='')
		$searchQuery .= 'l.user_id = "'.$employeename.'" AND ';
		if($department !='')
		$searchQuery .= 'l.department_id = "'.$department.'" AND ';
		if($leavestatus !='')
		$searchQuery .= 'l.leavestatus = '.$leavestatus.' AND ';
		if($from_date !='')
		{
			$from_date = sapp_Global::change_date($from_date,'database');
			$searchQuery .= 'DATE(l.createddate) = "'.$from_date.'" AND ';
		}
		$searchQuery = rtrim($searchQuery," AND");
		$leavestatusArr = $reportsmodel->getEmpLeaveHistory($sort_name, $sort_type,$page_no,$per_page,$searchQuery);

		$emp_arr = $leavestatusArr;
		require_once 'Classes/PHPExcel.php';
		require_once 'Classes/PHPExcel/IOFactory.php';
		$objPHPExcel = new PHPExcel();

		$letters = range('A','Z');
		$count =0;
		$filename = "LeavesReport.xlsx";
		$cell_name="";
			
		// Make first row Headings bold and highlighted in Excel.
		foreach ($cols_param_arr as $names)
		{
			$i = 1;
			$cell_name = $letters[$count].$i;
			$names = html_entity_decode($names,ENT_QUOTES,'UTF-8');

			$objPHPExcel->getActiveSheet()->SetCellValue($cell_name, $names);
			// Make bold cells
			$objPHPExcel->getActiveSheet()->getStyle($cell_name)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle($cell_name)->applyFromArray( array(
									        'fill' => array(
									            'type' => PHPExcel_Style_Fill::FILL_SOLID,
									            'color' => array('rgb' => '82CAFF')
			)
			)
			);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letters[$count])->setAutoSize(true);
			$i++;
			$count++;
		}

		// Display field/column values in Excel.
		$i = 2;
		foreach($emp_arr as $emp_data)
		{
			$count1 =0;
			foreach ($cols_param_arr as $column_key => $column_name)
			{
				// display field/column values
				$cell_name = $letters[$count1].$i;


				if($column_key == 'employeename')
				{
					$value = isset($emp_data['user_name'])?$emp_data['user_name']:"";
				}
				else if($column_key == 'leavetype')
				{
					$value = isset($emp_data['leavetype_name'])?$emp_data['leavetype_name']:"";
				}
				else if($column_key == 'deptname')
				{
					$value = isset($emp_data['buss_unit_name'])?$emp_data['buss_unit_name'].$emp_data['department_name']:$emp_data['department_name'];
				}
				else if($column_key == 'reportingmanagername')
				{
					$value = isset($emp_data['rep_manager_name'])?$emp_data['rep_manager_name']:"";
				}
				else
				{
					$value = isset($emp_data[$column_key])?$emp_data[$column_key]:"";
				}
				$value = html_entity_decode($value,ENT_QUOTES,'UTF-8');
				$objPHPExcel->getActiveSheet()->SetCellValue($cell_name, $value);
				$count1++;
			}
			$i++;
		}
			
		sapp_Global::clean_output_buffer();
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header('Cache-Control: max-age=0');
		sapp_Global::clean_output_buffer();
			
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');

		exit;

	}

	public function leavesreporttabheaderAction()
	{
		$this->_helper->layout->disableLayout();
		$checkedheaders = $this->_request->getParam('checkedheaders');

		if($checkedheaders !='')
		{
			$checkedheadersarray = explode(',',$checkedheaders);
			$leavesReportsession = new Zend_Session_Namespace('leavesreportsession');
			if(!empty($leavesReportsession->leavesReportObject) && isset($leavesReportsession->leavesReportObject))
			$this->_helper->layout->disableLayout();
			$checkedheaders = $this->_request->getParam('checkedheaders');
			$conf = $this->_request->getParam('conf');

			if($conf ==	'businessunits')
			{
				$businessunitsArr = array( 'unitname','unitcode','startdate','deptcount','address','ccity','sstate','ccountry','status');
				if($checkedheaders !='')
				{
					$checkedheadersarray = explode(',',$checkedheaders);
					$businessunits_session = new Zend_Session_Namespace('businessunits_session');
					if(!empty($businessunits_session->businessunitsObject) && isset($businessunits_session->businessunitsObject))
					{
						unset($businessunits_session->businessunitsObject);
						$businessunits_session->businessunitsObject = array();
						for($i=0;$i<sizeof($checkedheadersarray);$i++)
						{
							array_push($businessunits_session->businessunitsObject,$checkedheadersarray[$i]);
						}
						$result = 'success';
					}
				}
			}
			else if($conf == 'leavesreport')
			{
				$leavesheaderArray = array('Leave Applied By','Leave Type','Leave Duration','Leave Status','Department','From Date','To Date','Reason','Reporting Manager','Leave Count','Applied On');
				if($checkedheaders !='')
				{
					$checkedheadersarray = explode(',',$checkedheaders);
					$leavesReportsession = new Zend_Session_Namespace('leavesreportsession');
					if(!empty($leavesReportsession->leavesReportObject) && isset($leavesReportsession->leavesReportObject))
					{
						unset($leavesReportsession->leavesReportObject);
						$leavesReportsession->leavesReportObject = array();
						for($i=0;$i<sizeof($checkedheadersarray);$i++)
						{
							array_push($leavesReportsession->leavesReportObject,$checkedheadersarray[$i]);
						}
						$result = 'success';
					}
				}
			}
		}
		$this->_helper->json(array('result'=>$result));
	}


	public function leavemanagementreportAction()
	{
		$reportsmodel = new Default_Model_Reports();
		$departmentsmodel = new Default_Model_Departments();
		$monthslistmodel = new Default_Model_Monthslist();
		$weekdaysmodel = new Default_Model_Weekdays();
		$leavemanagementform = new Default_Form_leavemanagementreport();
		$msgarray = array();
		$selectColumns =array();
		$searchQuery = '';
			
		$department = $this->_request->getParam('department_id');
		$cal_startmonth = $this->_request->getParam('cal_startmonth');
		$weekend_startday = $this->_request->getParam('weekend_startday');
		$weekend_endday = $this->_request->getParam('weekend_endday');
		$pageno = $this->_request->getParam('pageno',1);
		$perpage = intval($this->_request->getParam('perpage',PERPAGE));
		if($perpage == 0)
		$perpage = PERPAGE;
		$by = $this->_request->getParam('by','Desc');
		$sortby = $this->_request->getParam('sortby','l.modifieddate');
		$columnby = $this->_request->getParam('columnby');
		$columnsortby = $this->_request->getParam('columnsortby');
		$checkedheaders = $this->_request->getParam('checkedheaders');
		$generatereport = $this->_request->getParam('generatereport');
		if($checkedheaders != '')
		$selectColumns = explode(',',$checkedheaders);
			
		if($columnby !='')
		$by = $columnby;
		if($columnsortby !='')
		$sortby = $columnsortby;

		if($department !='')
		$searchQuery .= 'l.department_id = "'.$department.'" AND ';
		if($cal_startmonth !='')
		$searchQuery .= 'l.cal_startmonth = '.$cal_startmonth.' AND ';
		if($weekend_startday !='')
		$searchQuery .= 'l.weekend_startday = '.$weekend_startday.' AND ';
		if($weekend_endday !='')
		$searchQuery .= 'l.weekend_endday = '.$weekend_endday.' AND ';
		$searchQuery = rtrim($searchQuery," AND");
			
		$leavemgmtArr = $reportsmodel->getLeaveManagementSummary($sortby, $by,$pageno,$perpage,$searchQuery);
		$leavemgmtCount = $reportsmodel->getLeaveManagementCount($searchQuery);
		$departmentlistArr = $departmentsmodel->getDepartmentWithCodeList();
		if(!empty($departmentlistArr))
		{
			foreach ($departmentlistArr as $departmentlistres){
				$leavemanagementform->department_id->addMultiOption($departmentlistres['id'],utf8_encode($departmentlistres['unitcode'].$departmentlistres['deptname']));

			}
		}
		else
		{
			$msgarray['department_id'] = 'Departments are not added yet.';
		}

		$monthslistdata = $monthslistmodel->getMonthlistData();
		if(sizeof($monthslistdata) > 0)
		{
			foreach ($monthslistdata as $monthslistres){
				$leavemanagementform->cal_startmonth->addMultiOption($monthslistres['month_id'],utf8_encode($monthslistres['month_name']));
			}
		}else
		{
			$msgarray['cal_startmonth'] = 'Months list is not configured yet.';
		}
			
		$weekdaysdata = $weekdaysmodel->getWeeklistData();
		if(sizeof($weekdaysdata) > 0)
		{
			foreach ($weekdaysdata as $weekdaysres){
				$leavemanagementform->weekend_startday->addMultiOption($weekdaysres['day_id'],utf8_encode($weekdaysres['day_name']));
				$leavemanagementform->weekend_endday->addMultiOption($weekdaysres['day_id'],utf8_encode($weekdaysres['day_name']));
			}
		}else
		{
			$msgarray['weekend_startday'] = 'Weekdays are not configured yet.';
			$msgarray['weekend_endday'] = 'Weekdays are not configured yet.';
		}

		if($leavemgmtCount[0]['count'] > 0)
		{
			$totalcount = $leavemgmtCount[0]['count'];
			$lastpage =  ceil($totalcount/$perpage);
		}
		else
		{
			$totalcount = '';
			$lastpage = '';
		}

		$selectColumnLabels = array();
		$leavesheaderarr = array('cal_startmonthname'=>'Start Month',
	                         'weekend_startday'=>'Week-end 1',
							 'weekend_endday'=>'Week-end 2',
							 'department_name'=>'Department',
							 'hours_day'=>'Hours',
							 'is_halfday'=>'Halfday',
							 'is_leavetransfer'=>'Leave Transferable',
							 'is_skipholidays'=>'Skip Holidays',
		);
			
		if(empty($selectColumns))
		{
			$selectColumns = array('cal_startmonthname','weekend_startday','weekend_endday',
			                       'department_name','hours_day','is_halfday','is_leavetransfer',
								   'is_skipholidays');		
			$selectColumnLabels = $leavesheaderarr;
		}
		else
		{
			foreach($leavesheaderarr as $key=>$val)
			{
				foreach($selectColumns as $column)
				{
					if($column == $key)
					$selectColumnLabels[$key] = $val;
				}
			}

		}


		$this->view->selectColumnLabels = $selectColumnLabels;
		$this->view->leavesheaderarr = $leavesheaderarr;
		$this->view->leavemgmtArr = $leavemgmtArr;
		$this->view->totalCount = $totalcount;
		$this->view->pageno = $pageno;
		$this->view->perpage = $perpage;
		$this->view->by = $by;
		$this->view->sortby = $sortby;
		$this->view->totalcount = $totalcount;
		$this->view->lastpage = $lastpage;
		$this->view->msgarray = $msgarray;
		$this->view->form = $leavemanagementform;
	}

	public function getpdfreportleavemanagementAction()
	{
		$this->_helper->layout->disableLayout();
		$param_arr = $this->_getAllParams();
		if(isset($param_arr['cols_arr']))
		unset($param_arr['cols_arr']);
		$page_no = isset($param_arr['pageno'])?intval($param_arr['pageno']):1;
		$per_page = isset($param_arr['perpage'])?intval($param_arr['perpage']):PERPAGE;
		if($per_page == 0)
		$per_page = PERPAGE;
		$sort_name = $this->_getParam('columnsortby','l.modifieddate');
		$sort_type = $this->_getParam('columnby','DESC');

		$department = $param_arr['department_id'];
		$cal_startmonth = $param_arr['cal_startmonth'];
		$weekend_startday = $param_arr['weekend_startday'];
		$weekend_endday = $param_arr['weekend_endday'];
		$headersize = $param_arr['headersize'];
		$cols_param = array();
		$searchQuery = '';
		$reportsmodel = new Default_Model_Reports();

		$cols_param = explode(',',$param_arr['checkedheaders']);
		$selectColumns = $cols_param;
		$leavesheaderarr = array('cal_startmonthname'=>'Start Month',
	                         'weekend_startday'=>'Week-end 1',
							 'weekend_endday'=>'Week-end 2',
							 'department_name'=>'Department',
							 'hours_day'=>'Hours',
							 'is_halfday'=>'Halfday',
							 'is_leavetransfer'=>'Leave Transferable',
							 'is_skipholidays'=>'Skip Holidays',
		);
		$cols_param_arr = array();
		foreach($leavesheaderarr as $key=>$val)
		{
			foreach( $cols_param  as $col)
			{
				if($col == $key)
				$cols_param_arr[$key] = $val;
			}
		}
			
			
		if($department !='')
		$searchQuery .= 'l.department_id = "'.$department.'" AND ';
		if($cal_startmonth !='')
		$searchQuery .= 'l.cal_startmonth = '.$cal_startmonth.' AND ';
		if($weekend_startday !='')
		$searchQuery .= 'l.weekend_startday = '.$weekend_startday.' AND ';
		if($weekend_endday !='')
		$searchQuery .= 'l.weekend_endday = '.$weekend_endday.' AND ';

		$searchQuery = rtrim($searchQuery," AND");
		$leavemgmtArr = $reportsmodel->getLeaveManagementSummary($sort_name, $sort_type,$page_no,$per_page,$searchQuery);
		$this->generateLeaveManagementHistoryPDF($leavemgmtArr,$selectColumns,$headersize);
			
	}

	public function generateLeaveManagementHistoryPDF($finalArray,$selectColumns,$headersize)
	{
		$field_names = array();
		$field_widths = array();
		$fieldwidth = '';
		$data['field_name_align'] = array();
			
		foreach($selectColumns as $col){
			switch($col){
				case 'cal_startmonthname':
					$field_names[] = array(
										'field_name'=>'cal_startmonthname',
										'field_label'=>'Start Month'
										);
										$field_widths[] = 20;
										$data['field_name_align'][] = 'C';
										break;
				case 'weekend_startday' : $field_names[] = array(
										'field_name'=>'weekend_startdayname',
										'field_label'=>'Week-end 1'
										);
										$field_widths[] = 20;
										$data['field_name_align'][] = 'C';
										break;
				case 'weekend_endday' : $field_names[] = array(
									'field_name'=>'weekend_enddayname',
									'field_label'=>'Week-end 2'
									);
									$field_widths[] = 20;
									$data['field_name_align'][] = 'C';
									break;
				case 'department_name': $field_names[] = array(
									'field_name'=>'department_name',
									'field_label'=>'Department'
									);
									$field_widths[] = 40;
									$data['field_name_align'][] = 'C';
									break;
				case 'hours_day': $field_names[] = array(
										'field_name'=>'hours_day',
										'field_label'=>'Hours'
										);
										$field_widths[] = 14;
										$data['field_name_align'][] = 'C';
										break;
				case 'is_halfday':
					$field_names[] = array(
										'field_name'=>'halfday',
										'field_label'=>'Half Day'
										);
										$field_widths[] = 16;
										$data['field_name_align'][] = 'C';
										break;
				case 'is_leavetransfer': $field_names[] = array(
										'field_name'=>'leavetransfer',
										'field_label'=>'Leave transferable'
										);
										$field_widths[] = 25;
										$data['field_name_align'][] = 'C';
										break;
				case 'is_skipholidays': $field_names[] = array(
										'field_name'=>'skipholidays',
										'field_label'=>'Skip Holidays'
										);
										$field_widths[] = 25;
										$data['field_name_align'][] = 'C';
										break;

			}
		}

		if(count($selectColumns) != $headersize){
			$totalPresentFieldWidth = 	array_sum($field_widths);
			foreach($field_widths as $key => $width){
				$field_widths[$key] = ($width*180)/$totalPresentFieldWidth;
			}
		}
			
		$data = array('grid_no'=>1, 'project_name'=>'', 'object_name'=>'Leave Management Report', 'grid_count'=>1,'file_name'=>'leavemanagementreport.pdf');
			
			
		$pdf = $this->_helper->PdfHelper->generateReport($field_names, $finalArray, $field_widths, $data);
		return $this->_helper->json(array('file_name'=>$data['file_name']));

	}

	public function getexcelreportleavemanagementAction()
	{
		$this->_helper->layout->disableLayout();
		$param_arr = $this->_getAllParams();
		if(isset($param_arr['cols_arr']))
		unset($param_arr['cols_arr']);
		$page_no = isset($param_arr['pageno'])?intval($param_arr['pageno']):1;
		$per_page = isset($param_arr['perpage'])?intval($param_arr['perpage']):PERPAGE;
		if($per_page == 0)
		$per_page = PERPAGE;
		$sort_name = $this->_getParam('columnsortby','l.modifieddate');
		$sort_type = $this->_getParam('columnby','DESC');
		$department = $param_arr['department_id'];
		$cal_startmonth = $param_arr['cal_startmonth'];
		$weekend_startday = $param_arr['weekend_startday'];
		$weekend_endday = $param_arr['weekend_endday'];
			
		$searchQuery = '';
		$reportsmodel = new Default_Model_Reports();

		$cols_param = explode(',',$param_arr['checkedheaders']);
		$leavesheaderarr = array('cal_startmonthname'=>'Start Month',
	                         'weekend_startday'=>'Week-end 1',
							 'weekend_endday'=>'Week-end 2',
							 'department_name'=>'Department',
							 'hours_day'=>'Hours',
							 'is_halfday'=>'Halfday',
							 'is_leavetransfer'=>'Leave Transferable',
							 'is_skipholidays'=>'Skip Holidays',
		);

		$cols_param_arr = array();
		foreach($leavesheaderarr as $key=>$val)
		{
			foreach( $cols_param  as $col)
			{
				if($col == $key)
				$cols_param_arr[$key] = $val;
			}
		}
			
			
		if($department !='')
		$searchQuery .= 'l.department_id = "'.$department.'" AND ';
		if($cal_startmonth !='')
		$searchQuery .= 'l.cal_startmonth = '.$cal_startmonth.' AND ';
		if($weekend_startday !='')
		$searchQuery .= 'l.weekend_startday = '.$weekend_startday.' AND ';
		if($weekend_endday !='')
		$searchQuery .= 'l.weekend_endday = '.$weekend_endday.' AND ';

		$searchQuery = rtrim($searchQuery," AND");
		$leavemgmtArr = $reportsmodel->getLeaveManagementSummary($sort_name, $sort_type,$page_no,$per_page,$searchQuery);

		$emp_arr = $leavemgmtArr;
		require_once 'Classes/PHPExcel.php';
		require_once 'Classes/PHPExcel/IOFactory.php';
		$objPHPExcel = new PHPExcel();

		$letters = range('A','Z');
		$count =0;
		$filename = "LeavesReport.xlsx";
		$cell_name="";
			
		// Make first row Headings bold and highlighted in Excel.
		foreach ($cols_param_arr as $names)
		{
			$i = 1;
			$cell_name = $letters[$count].$i;
			$names = html_entity_decode($names,ENT_QUOTES,'UTF-8');

			$objPHPExcel->getActiveSheet()->SetCellValue($cell_name, $names);
			// Make bold cells
			$objPHPExcel->getActiveSheet()->getStyle($cell_name)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle($cell_name)->applyFromArray( array(
									        'fill' => array(
									            'type' => PHPExcel_Style_Fill::FILL_SOLID,
									            'color' => array('rgb' => '82CAFF')
			)
			)
			);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letters[$count])->setAutoSize(true);
			$i++;
			$count++;
		}

		// Display field/column values in Excel.
		$i = 2;
		foreach($emp_arr as $emp_data)
		{
			$count1 =0;
			foreach ($cols_param_arr as $column_key => $column_name)
			{
				// display field/column values
				$cell_name = $letters[$count1].$i;


				if($column_key == 'weekend_startday')
				{
					$value = isset($emp_data['weekend_startdayname'])?$emp_data['weekend_startdayname']:"";
				}
				else if($column_key == 'weekend_endday')
				{
					$value = isset($emp_data['weekend_enddayname'])?$emp_data['weekend_enddayname']:"";
				}
				else if($column_key == 'department_name')
				{
					$value = isset($emp_data['businessunit_name'])?$emp_data['businessunit_name'].$emp_data['department_name']:$emp_data['department_name'];
				}
				else if($column_key == 'is_halfday')
				{
					$value = isset($emp_data['halfday'])?$emp_data['halfday']:"";
				}
				else if($column_key == 'is_leavetransfer')
				{
					$value = isset($emp_data['leavetransfer'])?$emp_data['leavetransfer']:"";
				}
				else if($column_key == 'is_skipholidays')
				{
					$value = isset($emp_data['skipholidays'])?$emp_data['skipholidays']:"";
				}
				else
				{
					$value = isset($emp_data[$column_key])?$emp_data[$column_key]:"";
				}
				$value = html_entity_decode($value,ENT_QUOTES,'UTF-8');
				$objPHPExcel->getActiveSheet()->SetCellValue($cell_name, $value);
				
				$count1++;
			}
			$i++;
		}
			
		sapp_Global::clean_output_buffer();
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header('Cache-Control: max-age=0');
		sapp_Global::clean_output_buffer();
			
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');

		exit;

	}

	public function bunitautoAction()
	{
		$flagterm = $this->_getParam('flag',null);
		$term = $this->_getParam('term',null);
		$output = array(array('id' => '','value' => 'No records','label' => 'No records'));
		$reportsmodel = new Default_Model_Reports();
		if($term != '')
		{
			if($flagterm == 'dname')
			{
				$DArr = $reportsmodel->getAutoReportDept($term);
				if(count($DArr)>0)
				{
					$output = array();
					foreach($DArr as $dept)
					{
						if($dept['unitcode'] != '000')
							$deptt =  $dept['unitcode'].'-'.$dept['deptname'].'('.$dept['deptcode'].')';
						else $deptt = $dept['deptname'].'('.$dept['deptcode'].')';
						$output[] = array('id' => $dept['did'],'value' => $deptt,'label' => $deptt);
					}
				}
			}
			else
			{
				$buArr = $reportsmodel->getAutoReportBunit($term,$flagterm);
				if(count($buArr)>0)
				{
					$output = array();
					foreach($buArr as $unit)
					{
						if($unit['unitcode'] != '000')
							$unitData =  $unit['unitname'].'-'.$unit['unitcode'];
						else $unitData =  $unit['unitname'];
						$output[] = array('id' => $unit['bid'],'value' => $unitData,'label' => $unitData);
					}
				}
			}
		}
		$this->_helper->json($output);
	}

	public function bunitcodeautoAction()
	{
		$term = $this->_getParam('term',null);
		$flagterm = $this->_getParam('flag',null);
		$output = array(array('id' => '','value' => 'No records','label' => 'No records'));
		$reportsmodel = new Default_Model_Reports();
		if($term != '')
		{
			if($flagterm == 'dcode')
			{
				$deArr = $reportsmodel->getAutoReportDeptcode($term);
				if(count($deArr)>0)
				{
					$output = array();
					foreach($deArr as $dept)
					{
						$output[] = array('id' => $dept['did'],'value' => $dept['deptcode'],'label' => $dept['deptcode']);
					}
				}
			}
			else
			{
				$buArr = $reportsmodel->getAutoReportBunitcode($term);
				if(count($buArr)>0)
				{
					$output = array();
					foreach($buArr as $unit)
					{
						$output[] = array('id' => $unit['bid'],'value' => $unit['unitcode'],'label' => $unit['unitcode']);
					}
				}
			}
		}
		$this->_helper->json($output);
	}

	public function getexcelreportbusinessunitAction()
	{
		$reportsmodel = new Default_Model_Reports();
		$this->_helper->layout->disableLayout();
		$param_arr = $this->_getAllParams();
		if(isset($param_arr['cols_arr']))	unset($param_arr['cols_arr']);
		$page_no = isset($param_arr['pageno'])?intval($param_arr['pageno']):1;
		$per_page = isset($param_arr['perpage'])?intval($param_arr['perpage']):PERPAGE;
		if($per_page == 0) $per_page = PERPAGE;
		$sort_name = $param_arr['sortby'];
		$sort_type = $param_arr['by'];
		$searchQuery = '';	$funorder = '';

		$columnby = $param_arr['columnby'];
		$columnsortby = $param_arr['columnsortby'];
		if($columnby !='')
		$sort_type = $columnby;
		if($columnsortby !='')
		$sort_name = $columnsortby;

			
		if($sort_name == 'ccity')
		{
			$cityorder = $reportsmodel->getCityOrder($sort_type);
			if(!empty($cityorder)){
				$inId = implode(',',$cityorder);
				$funorder = 'FIND_IN_SET(b.city,"'.$inId.'")';
			}
		}
		if($sort_name == 'sstate')
		{
			$stateorder = $reportsmodel->getStateOrder($sort_type);
			if(!empty($stateorder)){
				$inId = implode(',',$stateorder);
				$funorder = 'FIND_IN_SET(b.state,"'.$inId.'")';
			}
		}
			
			
		$cols_param = explode(',',$param_arr['checkedheaders']);
			
		$businessunitLabelsArr = array('unitname'=>'Business Unit','unitcode'=>'Code','startdate'=>'Started On','deptcount'=>'Department Count','address'=>'Address','ccity'=>'City','sstate'=>'State','ccountry'=>'Country','status'=>'Status');
		$cols_param_arr = array();
		foreach($businessunitLabelsArr as $key=>$val)
		{
			foreach( $cols_param  as $col)
			{
				if($col == $key)
				{
					if($key == 'address')
					$key = 'address1';
					$cols_param_arr[$key] = $val;
				}
			}
		}
		$bunitname = $param_arr['hiddenbunitname'];
		$bunitcode = $param_arr['hiddenbunitcode'];
		$country = $param_arr['country'];
		$startdate = $param_arr['startdate'];
		if($bunitname !='')
		$searchQuery .= 'd.unitid = "'.$bunitname.'" AND ';
		if($bunitcode !='')
			$searchQuery .= 'b.id = "'.$bunitcode.'" AND ';
		if($country !='')
		$searchQuery .= 'b.country = '.$country.' AND ';
		if($startdate !='')
		{
			$startdate = sapp_Global::change_date($startdate,'database');
			$searchQuery .= 'DATE(b.startdate) = "'.$startdate.'" AND ';
		}
		$searchQuery = rtrim($searchQuery," AND");
			
		$businessunitsArr = $reportsmodel->getBusinessUnitsInfo($sort_name,$sort_type,$page_no,$per_page,$searchQuery,$funorder);
		$finalArray = $this->createBusinessunitsReportFinalArray($businessunitsArr,$cols_param);
		sapp_Global::export_to_excel($finalArray,$cols_param_arr,"BusinessUnits.xlsx");
		exit;
	}

	public function getbusinessunitspdfAction()
	{

		$reportsmodel = new Default_Model_Reports();
		$this->_helper->layout->disableLayout();
		$param_arr = $this->_getAllParams();
		if(isset($param_arr['cols_arr']))	unset($param_arr['cols_arr']);
		$page_no = isset($param_arr['pageno'])?intval($param_arr['pageno']):1;
		$per_page = isset($param_arr['perpage'])?intval($param_arr['perpage']):PERPAGE;
		if($per_page == 0) $per_page = PERPAGE;
		$sort_name = $param_arr['sortby'];
		$sort_type = $param_arr['by'];
		$searchQuery = '';	$funorder = '';

		$columnby = $param_arr['columnby'];
		$columnsortby = $param_arr['columnsortby'];
		if($columnby !='')
		$sort_type = $columnby;
		if($columnsortby !='')
		$sort_name = $columnsortby;

			
		if($sort_name == 'ccity')
		{
			$cityorder = $reportsmodel->getCityOrder($sort_type);
			if(!empty($cityorder)){
				$inId = implode(',',$cityorder);
				$funorder = 'FIND_IN_SET(b.city,"'.$inId.'")';
			}
		}
		if($sort_name == 'sstate')
		{
			$stateorder = $reportsmodel->getStateOrder($sort_type);
			if(!empty($stateorder)){
				$inId = implode(',',$stateorder);
				$funorder = 'FIND_IN_SET(b.state,"'.$inId.'")';
			}
		}
			

		$cols_param = explode(',',$param_arr['checkedheaders']);
			

		$businessunitLabelsArr = array('unitname'=>'Business Unit','unitcode'=>'Code','startdate'=>'Started on','deptcount'=>'# Dept','address'=>'Address','ccity'=>'City','sstate'=>'State','ccountry'=>'Country','status'=>'Status');

		$cols_param_arr = array();
		foreach($businessunitLabelsArr as $key=>$val)
		{
			foreach( $cols_param  as $col)
			{
				if($col == $key)
				$cols_param_arr[$key] = $val;
			}
		}
		$bunitname = $param_arr['hiddenbunitname'];
		$bunitcode = $param_arr['hiddenbunitcode'];
		$country = $param_arr['country'];
		$startdate = $param_arr['startdate'];
		if($bunitname !='')
		$searchQuery .= 'd.unitid = "'.$bunitname.'" AND ';
		if($bunitcode !='')
			$searchQuery .= 'd.id = "'.$bunitcode.'" AND ';
		if($country !='')
		$searchQuery .= 'd.country = '.$country.' AND ';
		if($startdate !='')
		{
			$startdate = sapp_Global::change_date($startdate,'database');
			$searchQuery .= 'DATE(d.startdate) = "'.$startdate.'" AND ';
		}
		$searchQuery = rtrim($searchQuery," AND");
			
		$businessunitsArr = $reportsmodel->getBusinessUnitsInfo($sort_name,$sort_type,$page_no,$per_page,$searchQuery,$funorder);
		$finalArray = $this->createBusinessunitsReportFinalArray($businessunitsArr,$cols_param);
		$this->generateBusinessunitsPDF($finalArray,$cols_param);

	}

	public function generateBusinessunitsPDF($finalArray,$selectColumns)
	{
		$field_names = array();
		$field_widths = array();
		$fieldwidth = '';
		$data['field_name_align'] = array();

		foreach($selectColumns as $col){
			switch($col){
				case 'unitname': $field_names[] = array(
										'field_name'=>'unitname',
										'field_label'=>'Business Unit'
										);
										$field_widths[] = 25;
										$data['field_name_align'][] = 'C';
										break;
				case 'unitcode' : $field_names[] = array(
										'field_name'=>'unitcode',
										'field_label'=>'Code'
										);
										$field_widths[] = 12;
										$data['field_name_align'][] = 'C';
										break;
				case 'startdate' : $field_names[] = array(
									'field_name'=>'startdate',
									'field_label'=>'Started On'
									);
									$field_widths[] = 25;
									$data['field_name_align'][] = 'C';
									break;
				case 'deptcount': $field_names[] = array(
									'field_name'=>'deptcount',

									'field_label'=>'# Dept'

									);
									$field_widths[] = 15;
									$data['field_name_align'][] = 'C';
									break;
				case 'address': $field_names[] = array(
										'field_name'=>'address1',
										'field_label'=>'Address'
										);
										$field_widths[] = 25;
										$data['field_name_align'][] = 'C';
										break;
				case 'ccity': $field_names[] = array(
										'field_name'=>'ccity',
										'field_label'=>'City'
										);
										$field_widths[] = 25;
										$data['field_name_align'][] = 'C';
										break;
				case 'sstate': $field_names[] = array(
										'field_name'=>'sstate',
										'field_label'=>'State'
										);
										$field_widths[] = 20;
										$data['field_name_align'][] = 'C';
										break;
				case 'ccountry': $field_names[] = array(
										'field_name'=>'ccountry',
										'field_label'=>'Country'
										);
										$field_widths[] = 20;
										$data['field_name_align'][] = 'C';
										break;
				case 'status': $field_names[] = array(
										'field_name'=>'status',
										'field_label'=>'Status'
										);
										$field_widths[] = 20;
										$data['field_name_align'][] = 'C';
										break;
			}
		}

		if(count($selectColumns) != count($selectColumns)){
			$totalPresentFieldWidth = 	array_sum($field_widths);
			foreach($field_widths as $key => $width){
				$field_widths[$key] = ($width*180)/$totalPresentFieldWidth;
			}
		}

		$data = array('grid_no'=>1, 'project_name'=>'', 'object_name'=>'Business Unit Report', 'grid_count'=>1,'file_name'=>'businessunitreport.pdf');

		$pdf = $this->_helper->PdfHelper->generateReport($field_names, $finalArray, $field_widths, $data);
		return $this->_helper->json(array('file_name'=>$data['file_name']));
	}

	public function businessunitsAction()
	{
		$businessunitsform = new Default_Form_businessunitsreport();
		try
		{
			$reportsmodel = new Default_Model_Reports();
			$msgarray = array();
			$selectColumns = array();
			$searchQuery = '';

			$bunitname = $this->_request->getParam('bunitname');
			$bunitcode = $this->_request->getParam('bunitcode');
			$startdate = $this->_request->getParam('startdate');
			$country = $this->_request->getParam('country');
			$pageno = intval($this->_request->getParam('pageno',1));
			$perPage = intval($this->_request->getParam('perpage',PERPAGE));
			if($perPage == 0) $perPage = PERPAGE;
			$by = $this->_request->getParam('by','Desc');
			$sortby = $this->_request->getParam('sortby','b.modifieddate');
			$columnby = $this->_request->getParam('columnby');
			$columnsortby = $this->_request->getParam('columnsortby');
			$funorder = '';
			$checkedheaders = $this->_request->getParam('checkedheaders');

			if($checkedheaders != '')
			$selectColumns = explode(',',$checkedheaders);

			if($columnby !='')
			$by = $columnby;
			if($columnsortby !='')
			$sortby = $columnsortby;

			if($bunitname !='')
			$searchQuery .= 'b.id = "'.$bunitname.'" AND ';
			if($bunitcode !='')
			$searchQuery .= 'b.id = "'.$bunitcode.'" AND ';
			if($country !='')
			$searchQuery .= 'b.country = '.$country.' AND ';
			if($startdate !='')
			{
				$startdate = sapp_Global::change_date($startdate,'database');
				$searchQuery .= 'DATE(b.startdate) = "'.$startdate.'" AND ';
			}
			$searchQuery = rtrim($searchQuery," AND");
			$selectColumnLabels = array();

			$businessunitLabelsArr = array('unitname'=>'Business Unit','unitcode'=>'Code','startdate'=>'Started on','deptcount'=>'# Dept','address'=>'Address','ccity'=>'City','sstate'=>'State','ccountry'=>'Country','status'=>'Status');
			
            $countryModal = new Default_Model_Countries();
	    	$countriesData = $countryModal->fetchAll('isactive=1','country')->toArray();
			
			if(!empty($countriesData))
			{
			    $businessunitsform->country->addMultiOption('','All');
				foreach ($countriesData as $data){
			       $businessunitsform->country->addMultiOption($data['country_id_org'],$data['country']);
				}
			}else
            {
			       $businessunitsform->country->addMultiOption('','Select Country'); 
			       $msgarray['country']='Countries are not configured yet.';
            }  			

			if(empty($selectColumns))
			{
				$selectColumns = array('unitname','unitcode','startdate','deptcount','address','ccity','sstate'
				,'ccountry','status');
				$selectColumnLabels = $businessunitLabelsArr;
			}
			else
			{
				foreach($businessunitLabelsArr as $key=>$val)
				{
					foreach($selectColumns as $column)
					{
						if($column == $key)
						$selectColumnLabels[$key] = $val;
					}
				}

			}

			/* Column sort for city state country */
			if($sortby == 'ccity')
			{
				$cityorder = $reportsmodel->getCityOrder($by);
				if(!empty($cityorder)){
					$inId = implode(',',$cityorder);
					$funorder = 'FIND_IN_SET(b.city,"'.$inId.'")';
				}
			}
			if($sortby == 'sstate')
			{
				$stateorder = $reportsmodel->getStateOrder($by);
				if(!empty($stateorder)){
					$inId = implode(',',$stateorder);
					$funorder = 'FIND_IN_SET(b.state,"'.$inId.'")';
				}
			}
			/* END */
			$businessunitsArr = $reportsmodel->getBusinessUnitsInfo($sortby,$by,$pageno,$perPage,$searchQuery,$funorder);
			$businessunitsCount = $reportsmodel->getBusinessUnitsCount($searchQuery);
			if($businessunitsCount > 0)
			{
				$lastpage =  ceil($businessunitsCount/$perPage);
			}
			else
			{
				$lastpage = '';
				$businessunitsCount = '';
			}
			$finalArray = $this->createBusinessunitsReportFinalArray($businessunitsArr,$selectColumns);
			$businessunitsArr = $finalArray;
			$this->view->selectColumnLabels = $selectColumnLabels;
			$this->view->businessunitLabels = $businessunitLabelsArr;
			$this->view->businessunitsArr = $businessunitsArr;
			$this->view->pageno = $pageno;
			$this->view->perpage = $perPage;
			$this->view->lastpage = $lastpage;
			$this->view->by = $by;
			$this->view->sortby = $sortby;
			$this->view->totalcount = $businessunitsCount;
			$this->view->msgarray = $msgarray;
			$this->view->form = $businessunitsform;
		}
		catch(Exception $e)
		{
			echo$e->getMessage(); die;
		}
	}



	public function createLeaveHistoryReportFinalArray($dataArray,$columnArray)
	{
		$empleavetypesArray = array();
		$usersArray = array();
		$repmanagerArray = array();
		$departmentArray = array();

		$reportsModel = new Default_Model_Reports();
		$departmentsmodel = new Default_Model_Departments();
		if(!empty($dataArray))
		{
			foreach($dataArray as $key => $curr)
			{
				if(isset($curr['leavetypeid'])){
					if (!in_array($curr['leavetypeid'], $empleavetypesArray)) {
						array_push($empleavetypesArray,$curr['leavetypeid']);
					}
				}

				if(isset($curr['user_id'])){
					if (!in_array($curr['user_id'], $usersArray)) {
						array_push($usersArray,$curr['user_id']);
					}
				}

				if(isset($curr['rep_mang_id'])){
					if (!in_array($curr['rep_mang_id'], $repmanagerArray)) {
						array_push($repmanagerArray,$curr['rep_mang_id']);
					}
				}

				if(isset($curr['departmentid'])){
					if (!in_array($curr['departmentid'], $departmentArray)) {
						array_push($departmentArray,$curr['departmentid']);
					}
				}
			}
		}

		if(!empty($empleavetypesArray)){
			$empleavetypesArray = $reportsModel->getEmpLeaveNamesByIds($empleavetypesArray);
		}

		if(!empty($usersArray)){
			$userNameArray = $reportsModel->getUserNamesByIds($usersArray);
		}
		if(!empty($repmanagerArray)){
			$repManagerNameArray = $reportsModel->getRepManagerNamesByIds($repmanagerArray);
		}
		if(!empty($departmentArray)){
			$departmentnamesArr = $departmentsmodel->getDepartmentNameFromDeptString($departmentArray);
		}

		$finalArray = array();
		if(!empty($dataArray))
		{
			foreach($dataArray as $key => $curr)
			{
				if(array_search('employeename',$columnArray) !== false){
					$finalArray[$key]['employeename'] = isset($userNameArray[$curr['user_id']])?$userNameArray[$curr['user_id']]:'';
				}
				if(array_search("reportingmanagername", $columnArray) !== false){
					$finalArray[$key]['reportingmanagername'] = isset($repManagerNameArray[$curr['rep_mang_id']])?$repManagerNameArray[$curr['rep_mang_id']]:'';
				}
				if(array_search("leavetype", $columnArray) !== false){
					$finalArray[$key]['leavetype'] = isset($empleavetypesArray[$curr['leavetypeid']])?$empleavetypesArray[$curr['leavetypeid']]:'';
				}
				if(array_search("deptname", $columnArray) !== false){
					$finalArray[$key]['deptname'] = isset($departmentnamesArr[$curr['departmentid']])?$departmentnamesArr[$curr['departmentid']]:'';
				}
				if(array_search("leaveday", $columnArray) !== false){
					$finalArray[$key]['leaveday'] = $curr['leaveday'];
				}
				if(array_search("leavestatus", $columnArray) !== false){
					$finalArray[$key]['leavestatus'] = $curr['leavestatus'];
				}

				if(array_search("from_date", $columnArray) !== false){
					$finalArray[$key]['from_date'] = $curr['from_date'];
				}
				if(array_search("to_date", $columnArray) !== false){
					$finalArray[$key]['to_date'] = $curr['to_date'];
				}
				if(array_search("reason", $columnArray) !== false){
					$finalArray[$key]['reason'] = $curr['reason'];
				}
				if(array_search("appliedleavescount", $columnArray) !== false){
					$finalArray[$key]['appliedleavescount'] = $curr['appliedleavescount'];
				}
				if(array_search("applieddate", $columnArray) !== false){
					$finalArray[$key]['applieddate'] = $curr['applieddate'];
				}
			}
		}
		return $finalArray;
	}

	public 	function createBusinessunitsReportFinalArray($dataArray,$columnArray)
	{
		$cityArray = array();
		$stateArray = array();
		$countryArray = array();
		$timezoneArray = array();
		if(!empty($dataArray))
		{
			foreach($dataArray as $key => $curr)
			{
				if(isset($curr['city'])){
					if (!in_array($curr['city'], $cityArray)) {
						array_push($cityArray,$curr['city']);
					}
				}

				if(isset($curr['state'])){
					if (!in_array($curr['state'], $stateArray)) {
						array_push($stateArray,$curr['state']);
					}
				}

				if(isset($curr['country'])){
					if (!in_array($curr['country'], $countryArray)) {
						array_push($countryArray,$curr['country']);
					}
				}
			}
		}
		if(!empty($cityArray)){
			$cityModel = new Default_Model_Cities();
			$cityNameArray = $cityModel->getCityNamesByIds($cityArray);
		}
		if(!empty($stateArray)){
			$stateModel = new Default_Model_States();
			$stateNameArray = $stateModel->getStateNamesByIds($stateArray);
		}
		if(!empty($countryArray)){
			$countryModel = new Default_Model_Countries();
			$countryNameArray = $countryModel->getCountryNamesByIds($countryArray);
		}
		$finalArray = array();
		if(!empty($dataArray))
		{
			foreach($dataArray as $key => $curr)
			{				
				$finalArray[$key]['id'] = $curr['id'];			
				if(in_array("unitname", $columnArray)){
					$finalArray[$key]['unitname'] = $curr['unitname'];
				}
				if(in_array("unitcode", $columnArray)){
					$finalArray[$key]['unitcode'] = $curr['unitcode'];
				}
				if(in_array("deptcount", $columnArray)){
					$finalArray[$key]['deptcount'] = $curr['deptcount'];
				}
				if(in_array("startdate", $columnArray)){
					$finalArray[$key]['startdate'] = $curr['startdate'];
				}
				if(in_array("address", $columnArray)){
					$finalArray[$key]['address1'] = $curr['address1'];
				}

				if(in_array("ccity", $columnArray)){
					$finalArray[$key]['ccity'] = isset($cityNameArray[$curr['city']])?$cityNameArray[$curr['city']]:'';
				}
				if(in_array("sstate", $columnArray)){
					$finalArray[$key]['sstate'] = isset($stateNameArray[$curr['state']])?$stateNameArray[$curr['state']]:'';
				}
				if(in_array("ccountry", $columnArray)){
					$finalArray[$key]['ccountry'] = isset($countryNameArray[$curr['country']])?$countryNameArray[$curr['country']]:'';
				}
				if(in_array("status", $columnArray)){
					$finalArray[$key]['status'] = $curr['status'];
				}
			}
		}
		return $finalArray;
	}


	/**
	 * @name userlogreportAction
	 *
	 * This method is used to create userlogreport based on filters selected
	 *
	 *  @author Deepthi
	 *  @version 1.0
	 */


	public function userlogreportAction(){
		try{

			$dashboardcall = $this->_getParam('dashboardcall');
			if($dashboardcall == 'Yes')
			$perPage = DASHBOARD_PERPAGE;
			else
			$perPage = PERPAGE;
			$msgarray = array();

			$selectFields = array('userfullname'=>'User','employeeId'=>'Employee ID','group_id'=>'Group','emprole'=>'Role','emailaddress'=>'Email','empipaddress'=>'Ip Address');
			$selectColumns = array_keys($selectFields);
			
				if (($this->_request->getParam('fields') != '')){
					$selectColumns = explode(',',$this->_request->getParam('fields')); 
					array_push($selectColumns,'logindatetime');
				}
		
				$searchQuery = '';
				if($this->_request->getParam('emp_role') != ''){
					$searchQuery .= ' emprole ='.$this->_request->getParam('emp_role') .' AND';
				}
				if($this->_request->getParam('group') != ''){
					$searchQuery .= ' group_id ='.$this->_request->getParam('group') .' AND';
				}
				if($this->_request->getParam('employeeIdf') != ''){
					$searchQuery .= ' employeeId ="'.$this->_request->getParam('employeeIdf') .'" AND';
				}
				if($this->_request->getParam('username') != ''){
					$searchQuery .= ' userfullname ="'.$this->_request->getParam('username') .'" AND';
				}
				if($this->_request->getParam('emailId') != ''){
					$searchQuery .= ' emailaddress ="'.$this->_request->getParam('emailId') .'" AND';
				}
				if($this->_request->getParam('logindate') != ''){
					$logindate = sapp_Global::getGMTformatdate($this->_request->getParam('logindate'));
		
					$searchQuery .= ' DATE(logindatetime) ="'.$logindate .'" AND';
				}
				if($this->_request->getParam('ipaddress') != ''){
					$searchQuery .= ' empipaddress ="'.$this->_request->getParam('ipaddress') .'" AND';
				}
				if($searchQuery != ''){
					$searchQuery = rtrim($searchQuery," AND");
				}
		
		
				$pageNo = 1;
		
				if($this->_request->getParam('pageno') != ''){
					$pageNo = intval($this->_request->getParam('pageno'));
				}
				if($this->_request->getParam('per_page') != ''){
					$perPage = intval($this->_request->getParam('per_page'));
				}
		
				/*sort field request*/
				$by = $sortby = "logindatetime";
				$order = "Desc";
		
				if($this->_request->getParam('sortby') != ''){
					$by = $sortby = $this->_request->getParam('sortby');
					$order= $funorder = $this->_request->getParam('order');
					if($sortby == 'emprole'){
						$by = '';
						$emproleorder = $this->userlog_model->getEmpRoleOrder($order);
						if(!empty($emproleorder)){
							$inId = implode(',',$emproleorder);
							$funorder = 'FIND_IN_SET(emprole,"'.$inId.'")';
						}
					}
					if($sortby == 'group_id'){
						$by = '';
						$empgrouporder = $this->userlog_model->getEmpgroupOrder($order);
						if(!empty($empgrouporder)){
							$inId = implode(',',$empgrouporder);
							if($funorder == 'asc'){
								$inId = '0,'.$inId;
							}else{
								$inId = $inId.',0';
							}
							$funorder = 'FIND_IN_SET(group_id,"'.$inId.'")';
						}
					}
		
				}
			if($this->getRequest()->getPost() || $this->_request->getParam('generatereport',null) != ''){
				$this->_helper->layout->disableLayout();


				$userLogData = $this->userlog_model->getUserWiseLogData($by,$funorder,$pageNo, $perPage, $searchQuery,$selectColumns);
				$userLogCount = $this->userlog_model->getUserLogCount($searchQuery);
			}else{

				$pageNo = 1;
				$order = 'Desc';
				$sortby = 'logindatetime';
				$perPage = 20;
					
				$searchData = '';
				$selectColumns = array( 'userfullname','employeeId','group_id','emprole','emailaddress','empipaddress','logindatetime');
				$userLogData = $this->userlog_model->getUserWiseLogData($sortby,$order,$pageNo, $perPage, $searchData,$selectColumns);
				$userLogCount = $this->userlog_model->getUserLogCount();


			}

			$lastpage =  ceil($userLogCount/$perPage);

			$finalArray = $this->createreportuserlogfinalArray($userLogData,$selectColumns);


			if($this->getRequest()->getPost()){

				if($this->_request->getParam('generatereport') == 'pdf'){
					if (($this->_request->getParam('fields') != '')){
						$selectColumns = explode(',',$this->_request->getParam('fields')); 
						array_push($selectColumns,'logindatetime');
					}
					$field_names = array();
					$field_widths = array();
					$fieldwidth = '';
					$data['field_name_align'] = array();

					$data = array('grid_no'=>1, 'project_name'=>'', 'object_name'=>'UserLog Report', 'grid_count'=>1,'file_name'=>'userlogreport.pdf');
					$selectColumns = array_unique($selectColumns);
					foreach($selectColumns as $col){
						switch($col){
							case 'userfullname': $field_names[] = array(
										'field_name'=>'userfullname',
										'field_label'=>'User'
										);
										$field_widths[] = 25;
										$data['field_name_align'][] = 'C';
										break;
							case 'employeeId' : $field_names[] = array(
										'field_name'=>'employeeId',
										'field_label'=>'Employee ID'
										);
										$field_widths[] = 20;
										$data['field_name_align'][] = 'C';
										break;
							case 'group_id' : $field_names[] = array(
									'field_name'=>'group_id',
									'field_label'=>'Group'
									);
									$field_widths[] = 15;
									$data['field_name_align'][] = 'C';
									break;
							case 'emprole': $field_names[] = array(
									'field_name'=>'emprole',
									'field_label'=>'Role'
									);
									$field_widths[] = 20;
									$data['field_name_align'][] = 'C';
									break;
							case 'emailaddress': $field_names[] = array(
										'field_name'=>'emailaddress',
										'field_label'=>'Email'
										);
										$field_widths[] = 40;
										$data['field_name_align'][] = 'C';
										break;
							case 'logindatetime': $field_names[] = array(
										'field_name'=>'logindatetime',
										'field_label'=>'Login Time'
										);
										$field_widths[] = 40;
										$data['field_name_align'][] = 'C';
										break;
							case 'empipaddress': $field_names[] = array(
										'field_name'=>'empipaddress',
										'field_label'=>'Ip Address'
										);
										$field_widths[] = 30;
										$data['field_name_align'][] = 'C';
										break;
						}
					}

					if(count($selectColumns) != 7){
						$totalPresentFieldWidth = 	array_sum($field_widths);
						foreach($field_widths as $key => $width){
							$field_widths[$key] = ($width*180)/$totalPresentFieldWidth;
						}
					}

					$message = $this->_helper->PdfHelper->generateReport($field_names, $finalArray, $field_widths, $data);
					$this->_helper->json(array('file_name'=>$data['file_name']));

				}

			}
			if($this->_request->getParam('generatereport') == 'xcel'){ //xcel generation
				if (($this->_request->getParam('fields') != '')){
					$selectColumns = explode(',',$this->_request->getParam('fields'));
					array_push($selectColumns,'logindatetime');
				}
				foreach($selectFields as $key=>$val)
				{
					foreach($selectColumns as $column)
					{
						if($column == $key)
						$selectColumnLabels[$key] = $val;
					}
				}
				$selectColumnLabels['logindatetime'] = 'Login Time';
				sapp_Global::export_to_excel($finalArray,$selectColumnLabels,'Userlog Report.xlsx');

				exit;
			}

			$logreport_form = new Default_Form_logreport();
			$this->view->form = $logreport_form;

			$this->view->totalselectfields = $selectFields;
			$this->view->tabkeys = implode(',',$selectColumns);
			$this->view->userlogData = $finalArray;
			$this->view->totalCount = $userLogCount;
			$this->view->pageNo = $pageNo;
			$this->view->perPage = $perPage;
			$this->view->sortBy = $sortby;
			$this->view->order = $order;
			$this->view->lastPageNo = $lastpage;
			$this->view->msgarray = $msgarray;


		}catch(Exception $e){
			echo $e->getMessage(); exit;
		}
	}

	public function createreportuserlogfinalArray($userLogData,$selectColumns){

		$userArray = array();
		$empRoleArray = array();
		$groupArray = array();

		if(!empty($userLogData))
		{
			foreach($userLogData as $key => $curr)
			{
				if(isset($curr['userid'])){
					if (!in_array($curr['userid'], $userArray)) {
						array_push($userArray,$curr['userid']);
					}
				}

				if(isset($curr['emprole'])){
					if (!in_array($curr['emprole'], $empRoleArray)) {
						array_push($empRoleArray,$curr['emprole']);
					}
				}

				if(isset($curr['group_id'])){
					if (!in_array($curr['group_id'], $groupArray)) {
						array_push($groupArray,$curr['group_id']);
					}
				}

			}
		}

		if(!empty($empRoleArray)){
			$roleModel = new Default_Model_Roles();
			$roleNameArray = $roleModel->getEmpRoleNamesByIds($empRoleArray);
		}

		if(!empty($groupArray)){
			$groupModel = new Default_Model_Groups();
			$groupNameArray = $groupModel->getGroupNamesByIds($groupArray);
		}

		$finalArray = array();
		if(!empty($userLogData))
		{
			foreach($userLogData as $key => $curr)
			{
				if(in_array("userfullname", $selectColumns)){
					$finalArray[$key]['userfullname'] = $curr['userfullname'];
				}
				if(in_array("employeeId", $selectColumns)){
					$finalArray[$key]['employeeId'] = $curr['employeeId'];
				}
				if(in_array("group_id", $selectColumns)){
					$finalArray[$key]['group_id'] = isset($groupNameArray[$curr['group_id']])?$groupNameArray[$curr['group_id']]:'';
				}
				if(in_array("emprole", $selectColumns)){
					$finalArray[$key]['emprole'] = isset($roleNameArray[$curr['emprole']])?$roleNameArray[$curr['emprole']]:'';
				}
				if(in_array("emailaddress", $selectColumns)){
					$finalArray[$key]['emailaddress'] = $curr['emailaddress'];
				}
				if(in_array("logindatetime", $selectColumns)){
					$finalArray[$key]['logindatetime'] = sapp_Global::getDisplayDate($curr['logindatetime']);
				}
				if(in_array("empipaddress", $selectColumns)){
					$curr['empipaddress'] = ($curr['empipaddress'] == '::1')?'127.0.01':$curr['empipaddress'];
					$finalArray[$key]['empipaddress'] = $curr['empipaddress'];
				}

			}
		}
		return $finalArray;
	}

	public function departmentsAction()
	{
		$form = new Default_Form_departmentsreport();
		

		$reportsmodel = new Default_Model_Reports();
		$msgarray = array();
		$selectColumns = array();
		$searchQuery = '';

		$deptname = intval($this->_request->getParam('deptname'));
		$dcode = intval($this->_request->getParam('dcode'));
		$bunitname = $this->_request->getParam('bunitname');
		$startdate = $this->_request->getParam('startdate');
		$country = $this->_request->getParam('country');
		$pageno = intval($this->_request->getParam('pageno',1));
		$perpage = intval($this->_request->getParam('perpage',PERPAGE));
		if($perpage == 0) $perpage = PERPAGE;
		$by = $this->_request->getParam('by','Desc');
		$sortby = $this->_request->getParam('sortby','d.modifieddate');
		$columnby = $this->_request->getParam('columnby');
		$columnsortby = $this->_request->getParam('columnsortby');
		$funorder = '';
		$checkedheaders = $this->_request->getParam('checkedheaders');
		$gr = $this->_request->getParam('generatereport');
		if($gr == 'pdf' || $gr == 'excel')
		{
			$bunitname = intval($this->_request->getParam('hiddenbuname'));
			$dcode = intval($this->_request->getParam('hiddendeptcode'));
			$deptname = intval($this->_request->getParam('hiddendeptname'));
		}

		if($checkedheaders != '')
		$selectColumns = explode(',',$checkedheaders);
			
		if($columnby !='')
		$by = $columnby;
		if($columnsortby !='')
		$sortby = $columnsortby;

		if($deptname !='')
		$searchQuery .= 'd.id = "'.$deptname.'" AND ';
		if(isset($bunitname) && $bunitname != '')
		$searchQuery .= 'd.unitid = "'.$bunitname.'" AND ';
		if($dcode !='')
		$searchQuery .= 'd.id = "'.$dcode.'" AND ';
		if($country !='')
		$searchQuery .= 'd.country = '.$country.' AND ';
		if($startdate !='')
		{
			$startdate = sapp_Global::change_date($startdate,'database');
			$searchQuery .= 'DATE(d.startdate) = "'.$startdate.'" AND ';
		}

		$searchQuery = rtrim($searchQuery," AND"); 
		$selectColumnLabels = array();

		$DepartmentLabelsArr = array('deptname'=>'Department','deptcode'=>'Code','unitname'=>'Business Unit','startdate'=>'Started on','empcount'=>'# Emp','address'=>'Address','ccity'=>'City','sstate'=>'State','ccountry'=>'Country','status'=>'Status');
		
		$countryModal = new Default_Model_Countries();
	    	$countriesData = $countryModal->fetchAll('isactive=1','country')->toArray();
			
			if(!empty($countriesData))
			{
			    $form->country->addMultiOption('','All');
				foreach ($countriesData as $data){
			       $form->country->addMultiOption($data['country_id_org'],$data['country']);
				}
			}else
            {
			       $form->country->addMultiOption('','Select Country');
			       $msgarray['country']='Countries are not configured yet.';
            }


		if(empty($selectColumns))
		{
			$selectColumns = array('deptname','deptcode','unitname','startdate','empcount','address','ccity','sstate','ccountry','status');
			$selectColumnLabels = $DepartmentLabelsArr;
		}
		else
		{
			foreach($DepartmentLabelsArr as $key=>$val)
			{
				foreach($selectColumns as $column)
				{
					if($column == $key)
					$selectColumnLabels[$key] = $val;
				}
			}

		}
		/* Column sort for city state country */
		if($sortby == 'unitname')
		{
			$buOrder = $reportsmodel->getBusinessunitOrder($by);
			if(!empty($buOrder)){
				$inId = implode(',',$buOrder);
				$funorder = 'FIND_IN_SET(d.unitid,"'.$inId.'")';
			}
		}
		if($sortby == 'ccity')
		{
			$cityorder = $reportsmodel->getCityOrder($by);
			if(!empty($cityorder)){
				$inId = implode(',',$cityorder);
				$funorder = 'FIND_IN_SET(d.city,"'.$inId.'")';
			}
		}
		if($sortby == 'sstate')
		{
			$stateorder = $reportsmodel->getStateOrder($by);
			if(!empty($stateorder)){
				$inId = implode(',',$stateorder);
				$funorder = 'FIND_IN_SET(d.state,"'.$inId.'")';
			}
		}

		/* END */
		$departmentsArr = $reportsmodel->getDepartmentsInfo($sortby,$by,$pageno,$perpage,$searchQuery,$funorder);
		$departmentsCount = $reportsmodel->getDepartmentsCount($searchQuery);
		if($departmentsCount > 0)
		{
			$lastpage =  ceil($departmentsCount/$perpage);
		}
		else
		{
			$lastpage = '';
			$departmentsCount = '';
		}
		$finalArray = $this->createDepartmentsReportFinalArray($departmentsArr,$selectColumns);
		$departmentsArr = $finalArray;

		if($this->getRequest()->getPost())
		{
			if($this->_request->getParam('generatereport') == 'pdf')
			{

				
			}
			else if($this->_request->getParam('generatereport') == 'excel')
			{
				$this->generateExcel($departmentsArr,$selectColumns,$DepartmentLabelsArr);
				return;
			}
		}
		$this->view->selectColumnLabels = $selectColumnLabels;
		$this->view->departmentLabelsArr = $DepartmentLabelsArr;
		$this->view->departmentsArr = $departmentsArr;
		$this->view->pageno = $pageno;
		$this->view->perpage = $perpage;
		$this->view->lastpage = $lastpage;
		$this->view->by = $by;
		$this->view->sortby = $sortby;
		$this->view->totalcount = $departmentsCount;
		$this->view->msgarray = $msgarray;
		$this->view->form = $form;
	}
	public function exportdepartmentpdfAction()
	{
		$this->_helper->layout->disableLayout();
		$reportsmodel = new Default_Model_Reports();
		$param_arr = $this->_getAllParams();
		if(isset($param_arr['cols_arr']))	unset($param_arr['cols_arr']);
		$page_no = isset($param_arr['pageno'])?intval($param_arr['pageno']):1;
		$per_page = isset($param_arr['perpage'])?intval($param_arr['perpage']):PERPAGE;
		if($per_page == 0) $per_page = PERPAGE;
		$sort_name = $param_arr['sortby'];
		$sort_type = $param_arr['by'];
		$searchQuery = '';	$funorder = '';

		$columnby = $param_arr['columnby'];
		$columnsortby = $param_arr['columnsortby'];
		if($columnby !='')
		$sort_type = $columnby;
		if($columnsortby !='')
		$sort_name = $columnsortby;

		if($sort_name == 'unitname')
		{
			$buOrder = $reportsmodel->getBusinessunitOrder($sort_type);
			if(!empty($buOrder)){
				$inId = implode(',',$buOrder);
				$funorder = 'FIND_IN_SET(d.unitid,"'.$inId.'")';
			}
		}
		if($sort_name == 'ccity')
		{
			$cityorder = $reportsmodel->getCityOrder($sort_type);
			if(!empty($cityorder)){
				$inId = implode(',',$cityorder);
				$funorder = 'FIND_IN_SET(d.city,"'.$inId.'")';
			}
		}
		if($sort_name == 'sstate')
		{
			$stateorder = $reportsmodel->getStateOrder($sort_type);
			if(!empty($stateorder)){
				$inId = implode(',',$stateorder);
				$funorder = 'FIND_IN_SET(d.state,"'.$inId.'")';
			}
		}
			
			
		$cols_param = explode(',',$param_arr['checkedheaders']);

		$DepartmentLabelsArr = array('deptname'=>'Department','deptcode'=>'Code','unitname'=>'Business Unit','startdate'=>'Started on','empcount'=>'# Emp','address'=>'Address','ccity'=>'City','sstate'=>'State','ccountry'=>'Country','status'=>'Status');
		$cols_param_arr = array();
		foreach($DepartmentLabelsArr as $key=>$val)
		{
			foreach( $cols_param  as $col)
			{
				if($col == $key)
				$cols_param_arr[$key] = $val;
			}
		}
		$deptname = $param_arr['hiddendeptname'];
		$bunitname = $param_arr['hiddenbuname'];
		$dcode = $param_arr['hiddendeptcode'];
		$country = $param_arr['country'];
		$startdate = $param_arr['startdate'];
		if($deptname !='')
		$searchQuery .= 'd.id = "'.$deptname.'" AND ';
		if($bunitname !='')
		$searchQuery .= 'd.unitid = "'.$bunitname.'" AND ';
		if($dcode !='')
		$searchQuery .= 'd.id = "'.$dcode.'" AND ';
		if($country !='')
		$searchQuery .= 'd.country = '.$country.' AND ';
		if($startdate !='')
		{
			$startdate = sapp_Global::change_date($startdate,'database');
			$searchQuery .= 'DATE(d.startdate) = "'.$startdate.'" AND ';
		}
		$searchQuery = rtrim($searchQuery," AND");
			
		$departmentsArr = $reportsmodel->getDepartmentsInfo($sort_name,$sort_type,$page_no,$per_page,$searchQuery,$funorder);
			
		$finalArray = $this->createDepartmentsReportFinalArray($departmentsArr,$cols_param);

		$this->generateDepartmentsPdf($finalArray,$cols_param);
	}

	public function generateDepartmentsPdf($finalArray,$selectColumns)
	{
		$field_names = array();
		$field_widths = array();
		$fieldwidth = '';
		$data['field_name_align'] = array();
			
		foreach($selectColumns as $col){
			switch($col){
				case 'deptname': $field_names[] = array(
									'field_name'=>'deptname',
									'field_label'=>'Department'
									);
									$field_widths[] = 30;
									$data['field_name_align'][] = 'C';
									break;
				case 'deptcode' : $field_names[] = array(
									'field_name'=>'deptcode',
									'field_label'=>'Code'
									);
									$field_widths[] = 15;
									$data['field_name_align'][] = 'C';
									break;
				case 'unitname' : $field_names[] = array(
								'field_name'=>'unitname',
								'field_label'=>'Business Unit'
								);
								$field_widths[] = 30;
								$data['field_name_align'][] = 'C';
								break;
				case 'startdate' : $field_names[] = array(
								'field_name'=>'startdate',
								'field_label'=>'Started On'
								);
								$field_widths[] = 28;
								$data['field_name_align'][] = 'C';
								break;
				case 'empcount': $field_names[] = array(
								'field_name'=>'empcount',

								'field_label'=>'# Emp'

								);
								$field_widths[] = 14;
								$data['field_name_align'][] = 'C';
								break;
				case 'address': $field_names[] = array(
									'field_name'=>'address1',
									'field_label'=>'Address'
									);
									$field_widths[] = 25;
									$data['field_name_align'][] = 'C';
									break;
				case 'ccity': $field_names[] = array(
									'field_name'=>'ccity',
									'field_label'=>'City'
									);
									$field_widths[] = 20;
									$data['field_name_align'][] = 'C';
									break;
				case 'sstate': $field_names[] = array(
									'field_name'=>'sstate',
									'field_label'=>'State'
									);
									$field_widths[] = 18;
									$data['field_name_align'][] = 'C';
									break;
				case 'ccountry': $field_names[] = array(
									'field_name'=>'ccountry',
									'field_label'=>'Country'
									);
									$field_widths[] = 22;
									$data['field_name_align'][] = 'C';
									break;
				case 'status': $field_names[] = array(
									'field_name'=>'status',
									'field_label'=>'Status'
									);
									$field_widths[] = 20;
									$data['field_name_align'][] = 'C';
									break;
			}
		}

		if(count($selectColumns) != 7){
			$totalPresentFieldWidth = 	array_sum($field_widths);
			foreach($field_widths as $key => $width){
				$field_widths[$key] = ($width*180)/$totalPresentFieldWidth;
			}
		}
			
		$data = array('grid_no'=>1, 'project_name'=>'', 'object_name'=>'Departments', 'grid_count'=>1,'file_name'=>'departmentreport.pdf');

		$pdf = $this->_helper->PdfHelper->generateReport($field_names, $finalArray, $field_widths, $data);
		return $this->_helper->json(array('file_name'=>$data['file_name']));
	}

	public function getexcelreportdepartmentAction()
	{
		$reportsmodel = new Default_Model_Reports();
		$this->_helper->layout->disableLayout();
		$param_arr = $this->_getAllParams();
			
		if(isset($param_arr['cols_arr']))	unset($param_arr['cols_arr']);
		$page_no = isset($param_arr['pageno'])?intval($param_arr['pageno']):1;
		$per_page = isset($param_arr['perpage'])?intval($param_arr['perpage']):PERPAGE;
		if($per_page == 0) $per_page = PERPAGE;
		$sort_name = $param_arr['sortby'];
		$sort_type = $param_arr['by'];
		$searchQuery = '';	$funorder = '';

		$columnby = $param_arr['columnby'];
		$columnsortby = $param_arr['columnsortby'];
		if($columnby !='')
		$sort_type = $columnby;
		if($columnsortby !='')
		$sort_name = $columnsortby;

		if($sort_name == 'unitname')
		{
			$buOrder = $reportsmodel->getBusinessunitOrder($sort_type);
			if(!empty($buOrder)){
				$inId = implode(',',$buOrder);
				$funorder = 'FIND_IN_SET(d.unitid,"'.$inId.'")';
			}
		}
		if($sort_name == 'ccity')
		{
			$cityorder = $reportsmodel->getCityOrder($sort_type);
			if(!empty($cityorder)){
				$inId = implode(',',$cityorder);
				$funorder = 'FIND_IN_SET(d.city,"'.$inId.'")';
			}
		}
		if($sort_name == 'sstate')
		{
			$stateorder = $reportsmodel->getStateOrder($sort_type);
			if(!empty($stateorder)){
				$inId = implode(',',$stateorder);
				$funorder = 'FIND_IN_SET(d.state,"'.$inId.'")';
			}
		}
			
			
		$cols_param = explode(',',$param_arr['checkedheaders']);
			

		$DepartmentLabelsArr = array('deptname'=>'Department','deptcode'=>'Code','unitname'=>'Business Unit','startdate'=>'Started on','empcount'=>'# Emp','address'=>'Address','ccity'=>'City','sstate'=>'State','ccountry'=>'Country','status'=>'Status');

		$cols_param_arr = array();
		foreach($DepartmentLabelsArr as $key=>$val)
		{
			foreach( $cols_param  as $col)
			{
				if($col == $key)
				$cols_param_arr[$key] = $val;
			}
		}
		$deptname = $param_arr['hiddendeptname'];
		$bunitname = $param_arr['hiddenbuname'];
		$dcode = $param_arr['hiddendeptcode'];
		$country = $param_arr['country'];
		$startdate = $param_arr['startdate'];
		if($deptname !='')
		$searchQuery .= 'd.id = "'.$deptname.'" AND ';
		if($bunitname !='')
		$searchQuery .= 'd.unitid = "'.$bunitname.'" AND ';
		if($dcode !='')
		$searchQuery .= 'd.id = "'.$dcode.'" AND ';
		if($country !='')
		$searchQuery .= 'd.country = '.$country.' AND ';
		if($startdate !='')
		{
			$startdate = sapp_Global::change_date($startdate,'database');
			$searchQuery .= 'DATE(d.startdate) = "'.$startdate.'" AND ';
		}
		$searchQuery = rtrim($searchQuery," AND");
			
		$departmentsArr = $reportsmodel->getDepartmentsInfo($sort_name,$sort_type,$page_no,$per_page,$searchQuery,$funorder);
			
		$finalArray = $this->createDepartmentsReportFinalArray($departmentsArr,$cols_param);

		$emp_arr = $finalArray;
		sapp_Global::export_to_excel($finalArray,$cols_param_arr,"DepartmentsReport.xlsx");
		exit;
	}


	// Requisitions report START

	/**
	 * This is used to export to excel.
	 */
	public function candidaterptexcelAction()
	{
		$this->_helper->layout->disableLayout();
		$param_arr = $this->_getAllParams();
		            
		$default_fields = array(
	            'requisition_code' => 'Requisition Code',
				'jobtitle_name' => 'Job Title',
				'candidate_name' => 'Candidate',
	            'emailid' => 'Email',
	        	'cand_status' => 'Status',
	        	'contact_number' => 'Mobile',
	        	'skillset' => 'Skill Set'
	        	);
		$cols_param_arr = $this->_getParam('cols_arr', $default_fields);
			            		
		if(isset($param_arr['cols_arr']))	unset($param_arr['cols_arr']);
		$page_no = isset($param_arr['page_no'])?$param_arr['page_no']:1;
		$per_page = isset($param_arr['per_page'])?$param_arr['per_page']:PERPAGE;
		$sort_name = $param_arr['sort_name'];
		$sort_type = $param_arr['sort_type'];
		if(isset($param_arr['page_no']))unset($param_arr['page_no']);
		if(isset($param_arr['sort_name']))unset($param_arr['sort_name']);
		if(isset($param_arr['sort_type']))unset($param_arr['sort_type']);
		if(isset($param_arr['per_page']))unset($param_arr['per_page']);
		unset($param_arr['total_grid_columns']);
		unset($param_arr['module']);unset($param_arr['controller']);unset($param_arr['action']);

		$candidatedetails_model = new Default_Model_Candidatedetails();
		$candidates_data = $candidatedetails_model->getReportData($param_arr,$per_page,$page_no,$sort_name,$sort_type);
		$candidates_data = $candidates_data['rows']; 

		require_once 'Classes/PHPExcel.php';
		require_once 'Classes/PHPExcel/IOFactory.php';
		$objPHPExcel = new PHPExcel();

		$letters = range('A','Z');
		$count =0;
		$filename = "Requisitions_report.xlsx";
		$cell_name="";
			
		// Make first row Headings bold and highlighted in Excel.
		foreach ($cols_param_arr as $names)
		{
			$i = 1;
			$cell_name = $letters[$count].$i;
			$names = html_entity_decode($names,ENT_QUOTES,'UTF-8');

			$objPHPExcel->getActiveSheet()->SetCellValue($cell_name, $names);
			// Make bold cells
			$objPHPExcel->getActiveSheet()->getStyle($cell_name)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle($cell_name)->applyFromArray( array(
									        'fill' => array(
									            'type' => PHPExcel_Style_Fill::FILL_SOLID,
									            'color' => array('rgb' => '82CAFF')
			)
			)
			);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letters[$count])->setAutoSize(true);
			$i++;
			$count++;
		}

		// Display field/column values in Excel.
		$i = 2;
		foreach($candidates_data as $candidate)
		{
			$count1 =0;
			foreach ($cols_param_arr as $column_key => $column_name)
			{
				// display field/column values
				$cell_name = $letters[$count1].$i;


				if($column_key == 'userfullname')
				{
					$value = isset($candidate['userfullname'])?$candidate['prefix_name'].". ".$candidate['userfullname']:"";
				}
				elseif($column_key == 'date_of_joining')
				{
					$value = isset($candidate['date_of_joining'])?  sapp_Global::change_date($candidate['date_of_joining'],"view"):"";
				}
				else
				{
					$value = isset($candidate[$column_key])?$candidate[$column_key]:"";
				}
				$value = html_entity_decode($value,ENT_QUOTES,'UTF-8');
				$objPHPExcel->getActiveSheet()->SetCellValue($cell_name, $value);
				$count1++;
			}
			$i++;
		}
			
		sapp_Global::clean_output_buffer();
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header('Cache-Control: max-age=0');
		sapp_Global::clean_output_buffer();
			
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');

		exit;
	}

	/**
	 * This is used to export requisition report in pdf.
	 */
	public function candidaterptpdfAction()
	{
		$this->_helper->layout->disableLayout();
		$param_arr = $this->_getAllParams();

		$defaultu_fields = array(
            'requisition_code' => 'Requisition Code',
			'jobtitle_name' => 'Job Title',
            'candidate_name' => 'Candidate',
            'emailid' => 'Email',
			'cand_status' => 'Status',
            'contact_number' => 'Mobile',
            'skillset' => 'Skill Set'  
            );
            $cols_param_arr = $this->_getParam('cols_arr', $defaultu_fields);
            $total_grid_columns = $this->_getParam('total_grid_columns', NULL);
            $page_no = $this->_getParam('page_no', 1);
            $per_page = $this->_getParam('per_page', PERPAGE);
            $sort_name = $param_arr['sort_name'];
            $sort_type = $param_arr['sort_type'];

            if(isset($param_arr['cols_arr']))	unset($param_arr['cols_arr']);
            if(isset($param_arr['page_no']))unset($param_arr['page_no']);
            if(isset($param_arr['sort_name']))unset($param_arr['sort_name']);
            if(isset($param_arr['sort_type']))unset($param_arr['sort_type']);
            if(isset($param_arr['per_page']))unset($param_arr['per_page']);
            unset($param_arr['total_grid_columns']);
            unset($param_arr['module']);unset($param_arr['controller']);unset($param_arr['action']);

            $candidatedetails_model = new Default_Model_Candidatedetails();
            $candidates_data = $candidatedetails_model->getReportData($param_arr,$per_page,$page_no,$sort_name,$sort_type);
            $candidates_data = $candidates_data['rows'];

            $field_names = array();
            $field_widths = array();
            $data['field_name_align'] = array();


            if(!empty($cols_param_arr)){
            	foreach($cols_param_arr as $column_key => $column_name)
            	{
            		$field_names[] = array(
                                        'field_name'=>$column_key,
                                        'field_label'=>$column_name
            		);

            		switch($column_key){
            			case 'requisition_code':
            				$field_widths[] = 23;
            				$data['field_name_align'][] = 'C';
            				break;
            			case 'jobtitle_name':
            				$field_widths[] = 26;
            				$data['field_name_align'][] = 'C';
            				break;
            			case 'candidate_name':
            				$field_widths[] = 26;
            				$data['field_name_align'][] = 'C';
            				break;
            			case 'emailid':
            				$field_widths[] = 31;
            				$data['field_name_align'][] = 'C';
            				break;
            			case 'cand_status':
            				$field_widths[] = 23;
            				$data['field_name_align'][] = 'C';
            				break;
            			case 'contact_number':
            				$field_widths[] = 20;
            				$data['field_name_align'][] = 'C';
            				break;
            			case 'skillset':
            				$field_widths[] = 31;
            				$data['field_name_align'][] = 'C';
            				break;
            				 
            		}
            		 
            	}
            }


            if(count($cols_param_arr) != $total_grid_columns)
            {
            	$totalPresentFieldWidth = array_sum($field_widths);
            	foreach($field_widths as $key => $width)
            	{
            		$field_widths[$key] = ($width*180)/$totalPresentFieldWidth;
            	}
            }

            $data = array('grid_no'=>1, 'project_name'=>'', 'object_name'=>'Candidates Report', 'grid_count'=>1,'file_name'=>'Candidates_report.pdf');
            $pdf = $this->_helper->PdfHelper->generateReport($field_names, $candidates_data, $field_widths, $data);
            $this->_helper->json(array('file_name'=>$data['file_name']));
	}

	public function getcandidatesreportdataAction(){

		$this->_helper->layout->disableLayout();
		$param_arr = $this->_getAllParams();
		$cols_param_arr = $this->_getParam('cols_arr',array());
		if(isset($param_arr['cols_arr']))
		unset($param_arr['cols_arr']);
		$page_no = isset($param_arr['page_no'])?$param_arr['page_no']:1;
		$per_page = isset($param_arr['per_page'])?$param_arr['per_page']:PERPAGE;
		$sort_name = $param_arr['sort_name'];
		$sort_type = $param_arr['sort_type'];
		if(isset($param_arr['page_no']))unset($param_arr['page_no']);
		if(isset($param_arr['sort_name']))unset($param_arr['sort_name']);
		if(isset($param_arr['sort_type']))unset($param_arr['sort_type']);
		if(isset($param_arr['per_page']))unset($param_arr['per_page']);
		unset($param_arr['total_grid_columns']);
		unset($param_arr['module']);unset($param_arr['controller']);unset($param_arr['action']);unset($param_arr['format']);

		$candidatedetails_model = new Default_Model_Candidatedetails();
		$candidates_data = $candidatedetails_model->getReportData($param_arr,$per_page,$page_no,$sort_name,$sort_type);

		$page_cnt = $candidates_data['page_cnt'];
		$candidates_data = $candidates_data['rows'];

		$columns_array = array(
            'requisition_code' => 'Requisition Code',
			'jobtitle_name' => 'Job Title',
            'candidate_name' => 'Candidate',
            'emailid' => 'Email',
			'cand_status' => 'Status',
            'contact_number' => 'Mobile',
            'skillset' => 'Skill Set'  
            );

            $mandatory_array = array(
            'requisition_code' => 'Requisition Code',
			'jobtitle_name' => 'Job Title',
            'candidate_name' => 'Candidate',
            'emailid' => 'Email',
			'cand_status' => 'Status',
            'contact_number' => 'Mobile',
            'skillset' => 'Skill Set'  
            );
            if(count($cols_param_arr) == 0)
            $cols_param_arr = $mandatory_array;
            $mandatory_array = array_keys($mandatory_array);

            $this->view->requisition_arr = $candidates_data;
            $this->view->columns_array = $columns_array;
            $this->view->mandatory_array = $mandatory_array;
            $this->view->page_cnt = $page_cnt;
            $this->view->per_page = $per_page;
            $this->view->page_no = $page_no;
            $this->view->cols_param_arr = $cols_param_arr;
            $this->view->sort_name = $sort_name;
            $this->view->sort_type = $sort_type;

	}

	public function candidatesreportAction(){
		try
		{
			$norec_arr = array();
			$form = new Default_Form_Candidatesreport();
			$candidate_model = new Default_Model_Candidatedetails();

			$columns_array = array(
	            'requisition_code' => 'Requisition Code',
				'jobtitle_name' => 'Job Title',
				'candidate_name' => 'Candidate',
	            'emailid' => 'Email',
	        	'cand_status' => 'Status',
	        	'contact_number' => 'Mobile',
	        	'skillset' => 'Skill Set'
	        	);

	        	$mandatory_array = array(
	            'requisition_code' => 'Requisition Code',
				'jobtitle_name' => 'Job Title',
	        	'candidate_name' => 'Candidate',
	            'emailid' => 'Email',
	        	'cand_status' => 'Status',
	        	'contact_number' => 'Mobile',
	        	'skillset' => 'Skill Set'
	        	);
	        	 
	        	$mandatory_array = array_keys($mandatory_array);
	        	$this->view->columns_array = $columns_array;
	        	$this->view->mandatory_array = $mandatory_array;

	        	$this->view->form = $form;
	        	$this->view->messages = $norec_arr;
		}catch(Exception $e){
			echo$e->getMessage(); die;
		}
	}

	public function requisitionautoAction()
	{
		$term = $this->_getParam('term',null);
		$output = array(array('id' => '','value' => 'No records','label' => 'No records'));
		$requisition_model = new Default_Model_Requisition();
		if($term != '')
		{
			$requisition_arr = $requisition_model->getAutoReportRequisition($term);
			if(count($requisition_arr)>0)
			{
				$output = array();
				foreach($requisition_arr as $requisition)
				{
					$output[] = array('id' => $requisition['id'],'value' => $requisition['requisition_code'],'label' => $requisition['requisition_code']);
				}
			}
		}
		$this->_helper->json($output);
	}

	/**
	 * This is used to export to excel.
	 */
	public function requisitionrptexcelAction()
	{
		$this->_helper->layout->disableLayout();
		$param_arr = $this->_getAllParams();

		$default_fields = array(
			            'requisition_code' => 'Requisition Code',
			            'job_title' => 'Job title',
			            'req_status' => 'Requisition Status',
			            'created_by' => 'Raised By',
						'reporting_manager_name' => 'Reporting Manager',
			        	'approver1' => 'Approver -1',
			        	'appstatus1' => 'Status',
			        	'req_no_positions' => 'No.of positions',
			        	'selected_members' => 'Filled positions',
			            'created_on' => 'Raised On',
			            'onboard_date' => 'Due Date'
			            );
			            $cols_param_arr = $this->_getParam('cols_arr', $default_fields);
			            if(isset($param_arr['cols_arr']))	unset($param_arr['cols_arr']);
			            $page_no = isset($param_arr['page_no'])?$param_arr['page_no']:1;
			            $per_page = isset($param_arr['per_page'])?$param_arr['per_page']:PERPAGE;
			            $sort_name = $param_arr['sort_name'];
			            $sort_type = $param_arr['sort_type'];
			            if(isset($param_arr['page_no']))unset($param_arr['page_no']);
			            if(isset($param_arr['sort_name']))unset($param_arr['sort_name']);
			            if(isset($param_arr['sort_type']))unset($param_arr['sort_type']);
			            if(isset($param_arr['per_page']))unset($param_arr['per_page']);
			            unset($param_arr['module']);unset($param_arr['controller']);unset($param_arr['action']);
			            unset($param_arr['total_grid_columns']);

			            $requisition_model = new Default_Model_Requisition();
				
				        $auth = Zend_Auth::getInstance();
				     	if($auth->hasIdentity()){
							$loginUserId = $auth->getStorage()->read()->id;
							$loginuserGroup = $auth->getStorage()->read()->group_id;
						}
						 			            
			            $requisition_data = $requisition_model->getReportData($param_arr,$per_page,$page_no,$sort_name,$sort_type, $loginUserId, $loginuserGroup, 1);
			            $requisition_data = $requisition_data['rows'];

			            require_once 'Classes/PHPExcel.php';
			            require_once 'Classes/PHPExcel/IOFactory.php';
			            
			            $objPHPExcel = new PHPExcel();

			            $letters = range('A','Z');
			            $count =0;
			            $filename = "Requisitions_report.xlsx";
			            $cell_name="";

			            // Make first row Headings bold and highlighted in Excel.
			            foreach ($cols_param_arr as $names)
			            {
			            	$i = 1;
			            	$cell_name = $letters[$count].$i;
			            	$names = html_entity_decode($names,ENT_QUOTES,'UTF-8');

			            	$objPHPExcel->getActiveSheet()->SetCellValue($cell_name, $names);
			            	// Make bold cells
			            	$objPHPExcel->getActiveSheet()->getStyle($cell_name)->getFont()->setBold(true);
			            	$objPHPExcel->getActiveSheet()->getStyle($cell_name)->applyFromArray( array(
									        'fill' => array(
									            'type' => PHPExcel_Style_Fill::FILL_SOLID,
									            'color' => array('rgb' => '82CAFF')
			            	)
			            	)
			            	);
			            	$objPHPExcel->getActiveSheet()->getColumnDimension($letters[$count])->setAutoSize(true);
			            	$i++;
			            	$count++;
			            }

			            // Display field/column values in Excel.
			            $i = 2;
			            foreach($requisition_data as $requisition_data)
			            {
			            	$count1 =0;
			            	foreach ($cols_param_arr as $column_key => $column_name)
			            	{
			            		// display field/column values
			            		$cell_name = $letters[$count1].$i;


			            		if($column_key == 'userfullname')
			            		{
			            			$value = isset($requisition_data['userfullname'])?$requisition_data['prefix_name'].". ".$requisition_data['userfullname']:"";
			            		}
			            		elseif($column_key == 'date_of_joining')
			            		{
			            			$value = isset($requisition_data['date_of_joining'])?  sapp_Global::change_date($requisition_data['date_of_joining'],"view"):"";
			            		}
			            		else
			            		{
			            			$value = isset($requisition_data[$column_key])?$requisition_data[$column_key]:"";
			            		}
			            		$value = html_entity_decode($value,ENT_QUOTES,'UTF-8');
			            		$objPHPExcel->getActiveSheet()->SetCellValue($cell_name, $value);
			            		$count1++;
			            	}
			            	$i++;
			            }

			            sapp_Global::clean_output_buffer();
			            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			            header("Content-Disposition: attachment; filename=\"$filename\"");
			            header('Cache-Control: max-age=0');
			            sapp_Global::clean_output_buffer();

			            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			            $objWriter->save('php://output');

			            exit;
	}

	/**
	 * This is used to export requisition report in pdf.
	 */
	public function requisitionrptpdfAction()
	{
		$this->_helper->layout->disableLayout();
		$param_arr = $this->_getAllParams();

		$default_fields = array(
			            'requisition_code' => 'Requisition Code',
			            'job_title' => 'Job title',
			            'req_status' => 'Requisition Status',
			            'created_by' => 'Raised By',
						'reporting_manager_name' => 'Reporting Manager',
			        	'approver1' => 'Approver -1',
			        	'appstatus1' => 'Status',
			        	'req_no_positions' => 'No.of positions',
			        	'selected_members' => 'Filled positions',
			            'created_on' => 'Raised On',
			            'onboard_date' => 'Due Date'
			            );
			            $cols_param_arr = $this->_getParam('cols_arr', $default_fields);
			            $page_no = $this->_getParam('page_no', 1);
			            $per_page = $this->_getParam('per_page', PERPAGE);isset($param_arr['per_page'])?$param_arr['per_page']:PERPAGE;
			            $sort_name = $this->_getParam('sort_name', NULL);
			            $sort_type = $this->_getParam('sort_type', NULL);
			            $total_grid_columns = $this->_getParam('total_grid_columns', NULL);

			            if(isset($param_arr['cols_arr'])){
			            	unset($param_arr['cols_arr']);
			            }
			            if(isset($param_arr['page_no']))unset($param_arr['page_no']);
			            if(isset($param_arr['sort_name']))unset($param_arr['sort_name']);
			            if(isset($param_arr['sort_type']))unset($param_arr['sort_type']);
			            if(isset($param_arr['per_page']))unset($param_arr['per_page']);
			            unset($param_arr['total_grid_columns']);
			            unset($param_arr['module']);unset($param_arr['controller']);unset($param_arr['action']);

			            $requisition_model = new Default_Model_Requisition();
				
				        $auth = Zend_Auth::getInstance();
				     	if($auth->hasIdentity()){
							$loginUserId = $auth->getStorage()->read()->id;
							$loginuserGroup = $auth->getStorage()->read()->group_id;
						} 
					            
			            $requisition_data = $requisition_model->getReportData($param_arr,$per_page,$page_no,$sort_name,$sort_type, $loginUserId, $loginuserGroup, 1);
			            $requisition_data = $requisition_data['rows'];

			            $field_names = array();
			            $field_widths = array();
			            $data['field_name_align'] = array();

			            if(!empty($cols_param_arr)){
			            	foreach($cols_param_arr as $column_key => $column_name)
			            	{
			            		$field_names[] = array(
	                                        'field_name'=>$column_key,
	                                        'field_label'=>$column_name
			            		);

			            		switch($column_key){
			            			case 'requisition_code':
			            				$field_widths[] = 16;
			            				$data['field_name_align'][] = 'C';
			            				break;
			            			case 'job_title':
			            				$field_widths[] = 17;
			            				$data['field_name_align'][] = 'C';
			            				break;
			            			case 'req_status':
			            				$field_widths[] = 16;
			            				$data['field_name_align'][] = 'C';
			            				break;
			            			case 'created_by':
			            				$field_widths[] = 17;
			            				$data['field_name_align'][] = 'C';
			            				break;
			            			case 'reporting_manager_name':
			            				$field_widths[] = 16;
			            				$data['field_name_align'][] = 'C';
			            				break;
			            			case 'approver1':
			            				$field_widths[] = 17;
			            				$data['field_name_align'][] = 'C';
			            				break;
			            			case 'appstatus1':
			            				$field_widths[] = 16;
			            				$data['field_name_align'][] = 'C';
			            				break;
			            			case 'req_no_positions':
			            				$field_widths[] = 16;
			            				$data['field_name_align'][] = 'C';
			            				break;
			            			case 'selected_members':
			            				$field_widths[] = 16;
			            				$data['field_name_align'][] = 'C';
			            				break;
			            			case 'created_on':
			            				$field_widths[] = 17;
			            				$data['field_name_align'][] = 'C';
			            				break;
			            			case 'onboard_date':
			            				$field_widths[] = 16;
			            				$data['field_name_align'][] = 'C';
			            				break;
			            		}

			            	}
			            }


			            if(count($cols_param_arr) != $total_grid_columns)
			            {
			            	$totalPresentFieldWidth = array_sum($field_widths);
			            	foreach($field_widths as $key => $width)
			            	{
			            		$field_widths[$key] = ($width*180)/$totalPresentFieldWidth;
			            	}
			            }
			            $data = array('grid_no'=>1, 'project_name'=>'', 'object_name'=>'Requisition Report', 'grid_count'=>1,'file_name'=>'Requisition_report.pdf');
			            $pdf = $this->_helper->PdfHelper->generateReport($field_names, $requisition_data, $field_widths, $data);
			            $this->_helper->json(array('file_name'=>$data['file_name']));
	}

	public function getrequisitionsstatusreportdataAction(){

		try{
			$this->_helper->layout->disableLayout();
			$param_arr = $this->_getAllParams();
			$cols_param_arr = $this->_getParam('cols_arr',array());
			
			if(isset($param_arr['cols_arr']))
				unset($param_arr['cols_arr']);
			
			$page_no = isset($param_arr['page_no'])?$param_arr['page_no']:1;
			$per_page = isset($param_arr['per_page'])?$param_arr['per_page']:PERPAGE;
			$sort_name = $this->_getParam('sort_name', NULL);
			$sort_type = $this->_getParam('sort_type', NULL);
			$total_grid_columns = $this->_getParam('total_grid_columns', NULL);
	
			if(isset($param_arr['page_no']))unset($param_arr['page_no']);
			if(isset($param_arr['sort_name']))unset($param_arr['sort_name']);
			if(isset($param_arr['sort_type']))unset($param_arr['sort_type']);
			if(isset($param_arr['per_page']))unset($param_arr['per_page']);
			if(isset($param_arr['total_grid_columns']))unset($param_arr['total_grid_columns']);
			unset($param_arr['module']);unset($param_arr['controller']);unset($param_arr['action']);unset($param_arr['format']);
	
			$requisition_model = new Default_Model_Requisition();
					
	        $auth = Zend_Auth::getInstance();
	     	if($auth->hasIdentity()){
				$loginUserId = $auth->getStorage()->read()->id;
				$loginuserGroup = $auth->getStorage()->read()->group_id;
			} 
			$requisition_data = $requisition_model->getReportData($param_arr,$per_page,$page_no,$sort_name,$sort_type, $loginUserId, $loginuserGroup, 1);
			$page_cnt = $requisition_data['page_cnt'];
			$requisition_data = $requisition_data['rows'];
			
			$columns_array = array(
	            'requisition_code' => 'Requisition Code',
	            'job_title' => 'Job Title',
	            'req_status' => 'Requisition Status',
	            'created_by' => 'Raised By',
				'reporting_manager_name' => 'Reporting Manager',
	        	'approver1' => 'Approver -1',
	        	'appstatus1' => 'Status',
	        	'req_no_positions' => 'No.of positions',
	        	'selected_members' => 'Filled positions',
	            'created_on' => 'Raised On',
		        'onboard_date' => 'Due Date'
	        );
	
	        $mandatory_array = array(
	            'requisition_code' => 'Requisition Code',
	            'job_title' => 'Job title',
	            'req_status' => 'Requisition Status',
	            'created_by' => 'Raised By',
	            'reporting_manager_name' => 'Reporting Manager',
		        'approver1' => 'Approver -1',
		        'appstatus1' => 'Status',
		        'req_no_positions' => 'No.of positions',
		        'selected_members' => 'Filled positions',
	            'created_on' => 'Raised On',
	            'onboard_date' => 'Due Date'
	        );
	
	        if(count($cols_param_arr) == 0)
	            $cols_param_arr = $mandatory_array;
	            
	        $mandatory_array = array_keys($mandatory_array);
	        $this->view->requisition_arr = $requisition_data;
	        $this->view->columns_array = $columns_array;
	        $this->view->mandatory_array = $mandatory_array;
	        $this->view->page_cnt = $page_cnt;
	        $this->view->per_page = $per_page;
	        $this->view->page_no = $page_no;
	        $this->view->cols_param_arr = $cols_param_arr;
	        $this->view->sort_name = $sort_name;
	        $this->view->sort_type = $sort_type;
	        $this->view->total_grid_columns = $total_grid_columns;
		}catch(Exception $e){
			exit($e->getMessage());
		}
	}

	public function requisitionstatusreportAction(){
		try
		{
			$norec_arr = array();
			$form = new Default_Form_Requisitionsstatusreport();
			$requi_model = new Default_Model_Requisition();
			$employmentstatusModel = new Default_Model_Employmentstatus();

			$job_data = $requi_model->getJobTitleList();

			if(count($job_data)==0){
				$norec_arr['jobtitle'] = "Job titles are not configured yet.";
			}

			$form->jobtitle->addMultiOptions(array(''=>'Select Job Title')+$job_data);
			
			// Requisiton's Raised in limit from  past 10 years to next two years 
			$raised_in_years1 = range(date('Y', strtotime('-10 years')), date('Y', strtotime('+2 years')));
			$raised_in_years2 = array_combine($raised_in_years1, $raised_in_years1);
			$form->createdon->addMultiOptions(array('' => 'Select Requisition Year'));
			$form->createdon->addMultiOptions($raised_in_years2);

			$columns_array = array(
	            'requisition_code' => 'Requisition Code',
	            'job_title' => 'Job title',
	            'req_status' => 'Requisition Status',
	            'created_by' => 'Raised By',
				'reporting_manager_name' => 'Reporting Manager',
	        	'approver1' => 'Approver -1',
	        	'appstatus1' => 'Status',
	        	'req_no_positions' => 'No.of positions',
	        	'selected_members' => 'Filled positions',
	            'created_on' => 'Raised On',
	            'onboard_date' => 'Due Date'
		    );

            $mandatory_array = array(
	            'requisition_code' => 'Requisition Code',
	            'job_title' => 'Job title',
	            'req_status' => 'Requisition Status',
	            'created_by' => 'Raised By',
	            'reporting_manager_name' => 'Reporting Manager',
	        	'approver1' => 'Approver -1',
	        	'appstatus1' => 'Status',
	        	'req_no_positions' => 'No.of positions',
	        	'selected_members' => 'Filled positions',
	            'created_on' => 'Raised On',                                 
	            'onboard_date' => 'Due Date'
            );
            
            $this->view->columns_array = $columns_array;
            $this->view->form = $form;
            $this->view->messages = $norec_arr;
		}catch(Exception $e){
			exit($e->getMessage());
		}
	}
	// Requisitions report END

	public function createDepartmentsReportFinalArray($dataArray,$columnArray)
	{
		$businessunitArray = array();
		$cityArray = array();
		$stateArray = array();
		$countryArray = array();

		if(!empty($dataArray))
		{
			foreach($dataArray as $key => $curr)
			{
				if(isset($curr['unitid'])){
					if (!in_array($curr['unitid'], $businessunitArray)) {
						array_push($businessunitArray,$curr['unitid']);
					}
				}
				if(isset($curr['city'])){
					if (!in_array($curr['city'], $cityArray)) {
						array_push($cityArray,$curr['city']);
					}
				}

				if(isset($curr['state'])){
					if (!in_array($curr['state'], $stateArray)) {
						array_push($stateArray,$curr['state']);
					}
				}
				if(isset($curr['country'])){
					if (!in_array($curr['country'], $countryArray)) {
						array_push($countryArray,$curr['country']);
					}
				}
			}
		}
		if(!empty($businessunitArray)){
			$businessunitModel = new Default_Model_Businessunits();
			$businessNameArray = $businessunitModel->getBusinessunitNamesByIds($businessunitArray);
		}
		if(!empty($cityArray)){
			$cityModel = new Default_Model_Cities();
			$cityNameArray = $cityModel->getCityNamesByIds($cityArray);
		}
		if(!empty($stateArray)){
			$stateModel = new Default_Model_States();
			$stateNameArray = $stateModel->getStateNamesByIds($stateArray);
		}
		if(!empty($countryArray)){
			$countryModel = new Default_Model_Countries();
			$countryNameArray = $countryModel->getCountryNamesByIds($countryArray);
		}
		$finalArray = array();
		if(!empty($dataArray))
		{
			foreach($dataArray as $key => $curr)
			{
				$finalArray[$key]['id'] = $curr['id'];
				if(in_array("deptname", $columnArray)){
					$finalArray[$key]['deptname'] = $curr['deptname'];
				}
				if(in_array("unitname", $columnArray)){
					$finalArray[$key]['unitname'] = isset($businessNameArray[$curr['unitid']])?$businessNameArray[$curr['unitid']]:'';
				}
				if(in_array("deptcode", $columnArray)){
					$finalArray[$key]['deptcode'] = $curr['deptcode'];
				}
				if(in_array("empcount", $columnArray)){
					$finalArray[$key]['empcount'] = $curr['empcount'];
				}
				if(in_array("startdate", $columnArray)){
					$finalArray[$key]['startdate'] = $curr['startdate'];
				}
				if(in_array("address", $columnArray)){
					$finalArray[$key]['address1'] = $curr['address1'];
					$finalArray[$key]['address'] = $curr['address1'];
				}

				if(in_array("ccity", $columnArray)){
					$finalArray[$key]['ccity'] = isset($cityNameArray[$curr['city']])?$cityNameArray[$curr['city']]:'';
				}
				if(in_array("sstate", $columnArray)){
					$finalArray[$key]['sstate'] = isset($stateNameArray[$curr['state']])?$stateNameArray[$curr['state']]:'';
				}
				if(in_array("ccountry", $columnArray)){
					$finalArray[$key]['ccountry'] = isset($countryNameArray[$curr['country']])?$countryNameArray[$curr['country']]:'';
				}
				if(in_array("status", $columnArray)){
					$finalArray[$key]['status'] = $curr['status'];
				}

			}
		}
		return $finalArray;
	}

	/*activity log report*/
	public function activitylogreportAction(){
		try{
			$activitylog_model = new Default_Model_Activitylog();
			$logmanager_model = new Default_Model_Logmanager();
			$dashboardcall = $this->_getParam('dashboardcall');
			if($dashboardcall == 'Yes')
			$perPage = DASHBOARD_PERPAGE;
			else
			$perPage = PERPAGE;

			$selectFields = array('menuname'=>'Menu','username'=>'User Name','empId'=>'Employee ID','useraction'=>'Action','modifieddate'=>'Modified Date');
			$selectColumns = array_keys($selectFields);

			$action = array('1'=>'Add','2' => 'Edit', '3' => 'Delete','5' =>'Cancel');
			$activityLogData = array();$splitArray = array();

			$pageNo = 1;
			$order = $funorder = 'Desc';
			$by = $sortby = 'last_modifieddate';
			$searchData = '';
			$lastpage = 0;

			if($this->getRequest()->getPost()){
				$this->_helper->layout->disableLayout();}
				if($this->_request->getParam('pageNo') != ''){
					$pageNo = intval($this->_request->getParam('pageNo'));
				}

				if($this->_request->getParam('per_page') != ''){
					$perPage = intval($this->_request->getParam('per_page'));
				}
				if (($this->_request->getParam('fields') != '')){
					$selectColumns = explode(',',$this->_request->getParam('fields')); 
				}
				$finalArray = array();
				//POST with empty search fields
				if($this->_request->getParam('hiddenusername') == '' && $this->_request->getParam('menu') == '' && $this->_request->getParam('useraction') == '' && $this->_request->getParam('modifieddate') == ''){

					if($this->_request->getParam('sortby') != ''){
						$by = $sortby = $this->_request->getParam('sortby');
						$order= $funorder= $this->_request->getParam('order');
						if($sortby == 'userfullname'){
							$by = '';
							$usernameorder = $logmanager_model->getUsernameOrderForLog($order);

							if(!empty($usernameorder)){
								$funorder = 'FIND_IN_SET(last_modifiedby,"'.$usernameorder.'")';
							}
						}

						if($sortby == 'employeeId'){
							$by = '';
							$empidorder = $logmanager_model->getEmpidOrderForLog($order);

							if(!empty($empidorder)){
								$funorder = 'FIND_IN_SET(last_modifiedby,"'.$empidorder.'")';
							}
						}

						if($sortby == 'menuname'){
							$by = '';
							$menunameorder = $logmanager_model->getMenuOrderForLog($order);

							if(!empty($menunameorder)){
								$funorder = 'FIND_IN_SET(menuId,"'.$menunameorder.'")';
							}
						}
					}
					$activityLogData = $logmanager_model->getLogManagerDataReport($by,$funorder,$pageNo, $perPage, $searchData,array('*'));
					$activityLogCount = $activitylog_model->getLogManagerCount();
$menuArray = array();
						$userArray = array();

					if(count($activityLogData) > 0){
						$lastpage =  ceil($activityLogCount/$perPage);

						
						foreach($activityLogData as $activitylog)
						{
							if(isset($activitylog['menuId'])){
								if (!in_array($activitylog['menuId'], $menuArray)) {
									array_push($menuArray,$activitylog['menuId']);
								}
							}

							if(isset($activitylog['last_modifiedby'])){
								if (!in_array($activitylog['last_modifiedby'], $userArray)) {
									array_push($userArray,$activitylog['last_modifiedby']);
								}
							}
						}
					}

					$menuNameArray = $activitylog_model->getMenuNamesByIds($menuArray);
					$userNameArray = $activitylog_model->getuserNamesByIds($userArray);

					if(count($activityLogData) > 0){
						foreach($activityLogData as $key =>$activitylog)
						{
							if(in_array('menuname',$selectColumns))
							$finalArray[$key]['menuname'] = isset($menuNameArray[$activitylog['menuId']])?$menuNameArray[$activitylog['menuId']]['name']:'';
							if(in_array('username',$selectColumns))
							$finalArray[$key]['username'] = isset($userNameArray[$activitylog['last_modifiedby']])?$userNameArray[$activitylog['last_modifiedby']]['userfullname']:'';
							if(in_array('empId',$selectColumns))
							$finalArray[$key]['empId'] = isset($userNameArray[$activitylog['last_modifiedby']])?$userNameArray[$activitylog['last_modifiedby']]['employeeId']:'';
							if(in_array('useraction',$selectColumns))
							$finalArray[$key]['useraction'] = $action[$activitylog['user_action']];
							if(in_array('modifieddate',$selectColumns))
							$finalArray[$key]['modifieddate'] = sapp_Global::getDisplayDate($activitylog['last_modifieddate']);
						}
					}
				}else{ // with search values from form
					$searchQuery = '';

					if($this->_request->getParam('menu') != ''){
						$searchQuery .= ' menuId ='.intval($this->_request->getParam('menu')).' AND';
					}
					if($this->_request->getParam('useraction') != ''){

						$searchQuery .= ' user_action ='.intval($this->_request->getParam('useraction')).' AND';
					}
					if($this->_request->getParam('hiddenusername') != ''){
						$username  = $this->_request->getParam('hiddenusername'); //userid in request
						$searchQuery .= ' log_details Like \'%"userid":"'.$username.'"%\' AND';
					}
					if($this->_request->getParam('modifieddate') != ''){
						$date = sapp_Global::change_date($this->_request->getParam('modifieddate'),'database');
						$date1 = str_replace('-', '/', $date);
						$onedayafter = date('Y-m-d',strtotime($date1 . "+1 days"));
						$onedaybefore = date('Y-m-d',strtotime($date1 . "-1 days"));
						$searchQuery .= '(log_details Like "%'.$onedaybefore.'%" or log_details Like "%'.$date.'%" or log_details Like "%'.$onedayafter.'%")';
					}
					if($searchQuery != ''){
						$searchQuery = rtrim($searchQuery," AND");
					}

					$activityLogData = $logmanager_model->getLogManagerDataReport('last_modifieddate','Desc','', '', $searchQuery,array('*'));

					/*looping jsonlogs */
					if(count($activityLogData) > 0){
						$userArray = array();
						$menuArray = array();
						$logJsonArray = array();
						$jsonCount = 0;
						$index = 0;
						foreach($activityLogData as $activitylog){ 

							$logdetails = '{"testjson":['.$activitylog['log_details'].']}';
							$logarr = @get_object_vars(json_decode($logdetails));

							if(!empty($logarr))
							{
								$logarr['testjson'] = array_reverse($logarr['testjson']);
								$jsonCount = count($logarr['testjson']);
							}

							if($jsonCount > 0 && isset($logarr['testjson']) && !empty($logarr['testjson'])){ 
								foreach($logarr['testjson'] as $key =>$curr)
								{
									$currArray = @get_object_vars($curr);

									/*userid and date check with form and json values*/
									$flag = false;
									if($this->_request->getParam('modifieddate') != ''){
										$currArrayTemp = sapp_Global::getGMTformatdate($currArray['date']);
										$pos = strpos($currArrayTemp, $date);
										$flag = ($pos !== false)?false:true;
									}

									if(($this->_request->getParam('hiddenusername') != '' && $username != $currArray['userid']) || $flag){
										continue;
									}
									/*end userid and date check*/

									$logJsonArray[$index]['userid'] = ($currArray['userid'] != '')?$currArray['userid']:1;
									$logJsonArray[$index]['date'] = $currArray['date'];
									$datesort[$index] = $currArray['date']; // to sort by date
									if (!in_array($currArray['userid'], $userArray)) {
										array_push($userArray,$currArray['userid']);
									}

									if(isset($activitylog['menuId'])){
										if (!in_array($activitylog['menuId'], $menuArray)) {
											array_push($menuArray,$activitylog['menuId']);
										}
									}
									$logJsonArray[$index]['menu'] = $activitylog['menuId'];
									$logJsonArray[$index]['action'] = $action[(string)$activitylog['user_action']];
									$actionsort[$index] = $action[(string)$activitylog['user_action']];
									$index ++;
								}
							}
						}
						$menuNameArray = $activitylog_model->getMenuNamesByIds($menuArray);
						$userNameArray = $activitylog_model->getuserNamesByIds($userArray);
						$lastpage =  ceil(count($logJsonArray)/$perPage);
						$endIndex = intval($perPage);

						if($pageNo != 1){
							$startIndex = (intval($pageNo)-1) * intval($perPage);
						}else{
							$startIndex = 0;
						}

						if(count($logJsonArray) > 0){
							if($this->_request->getParam('sortby') != ''){
								$sortby = $this->_request->getParam('sortby');
								$order = $this->_request->getParam('order');
								$orderby = ($order == 'asc')?SORT_ASC:SORT_DESC;
								if($sortby == 'user_action' || $sortby == 'last_modifieddate'){ // can sort by logJsonArray
									if($sortby == 'user_action'){
										array_multisort($actionsort, $orderby,$logJsonArray);
									}
									if($sortby == 'last_modifieddate'){
										array_multisort($datesort, $orderby,$logJsonArray);
									}

									/* only perpage no of rows */
									$splitArray = array_slice($logJsonArray,$startIndex,$endIndex);
									foreach($splitArray as $key => $logjson){
										if(in_array('menuname',$selectColumns))
										$finalArray[$key]['menuname'] = isset($menuNameArray[(string)$logjson['menu']])?$menuNameArray[(string)$logjson['menu']]['name']:'';
										if(in_array('username',$selectColumns))
										$finalArray[$key]['username'] = isset($userNameArray[(string)$logjson['userid']])?$userNameArray[(string)$logjson['userid']]['userfullname']:'';
										if(in_array('empId',$selectColumns))
										$finalArray[$key]['empId'] = isset($userNameArray[(string)$logjson['userid']])?$userNameArray[(string)$logjson['userid']]['employeeId']:'';
										if(in_array('useraction',$selectColumns))
										$finalArray[$key]['useraction'] = $logjson['action'];
										if(in_array('modifieddate',$selectColumns))
										$finalArray[$key]['modifieddate'] = sapp_Global::getDisplayDate($logjson['date']);
									}

								}else{
									$totalArray = array();
									foreach($logJsonArray as $key => $logjson){
										if(in_array('menuname',$selectColumns))
										$totalArray[$key]['menuname'] = isset($menuNameArray[(string)$logjson['menu']])?$menuNameArray[(string)$logjson['menu']]['name']:'';
										$menunameSort[$key] = $totalArray[$key]['menuname'];
										if(in_array('username',$selectColumns))
										$totalArray[$key]['username'] = isset($userNameArray[(string)$logjson['userid']])?$userNameArray[(string)$logjson['userid']]['userfullname']:'';
										$usernameSort[$key] = $totalArray[$key]['username'];
										if(in_array('empId',$selectColumns))
										$totalArray[$key]['empId'] = isset($userNameArray[(string)$logjson['userid']])?$userNameArray[(string)$logjson['userid']]['employeeId']:'';
										$empIdSort[$key] = $totalArray[$key]['empId'];
										if(in_array('useraction',$selectColumns))
										$totalArray[$key]['useraction'] = $logjson['action'];
										if(in_array('modifieddate',$selectColumns))
										$totalArray[$key]['modifieddate'] = sapp_Global::getDisplayDate($logjson['date']);
									}
									if($sortby == 'userfullname'){
										array_multisort($usernameSort, $orderby,$totalArray);
									}
									if($sortby == 'employeeId'){
										array_multisort($empIdSort, $orderby,$totalArray);
									}
									if($sortby == 'menuname'){
										array_multisort($menunameSort, $orderby,$totalArray);
									}

									/* only perpage no of rows */
									if(count($totalArray) >= intval($perPage)){
										$finalArray = array_slice($totalArray,$startIndex,$endIndex);
									}else{
										$finalArray = $totalArray;
									}

								}
							}
						}

					}
				}

				if($this->getRequest()->getPost()){
					// To generate PDF START
					if($this->_request->getParam('generatereport') == 'pdf'){ 
						$this->generateActivityLogPdf($finalArray,$selectColumns);
					}

				}

				if($this->_request->getParam('generatereport') == 'xcel'){ //xcel generation

					foreach($selectFields as $key=>$val)
					{
						foreach($selectColumns as $column)
						{
							if($column == $key)
							$selectColumnLabels[$key] = $val;
						}
					}
					sapp_Global::export_to_excel($finalArray,$selectColumnLabels,'Activitylog Report.xlsx');
					exit;
				}
				$activitylogreport_form = new Default_Form_activitylogreport();
				$this->view->form = $activitylogreport_form;
				$this->view->totalselectfields = $selectFields;
				$this->view->tabkeys = implode(',',$selectColumns);

				$this->view->activitylogData = $finalArray;
				$this->view->pageNo = $pageNo;
				$this->view->perPage = $perPage;
				$this->view->sortBy = $sortby;
				$this->view->order = $order;
				$this->view->lastPageNo = $lastpage;
		}catch(Exception $e){
			echo $e->getMessage();
		}

	}

	/*activity log pdf*/
	public function generateActivityLogPdf($finalArray,$selectColumns){
		$field_names = array();
		$field_widths = array();
		$fieldwidth = '';
		$data['field_name_align'] = array();

		$data = array('grid_no'=>1, 'project_name'=>'', 'object_name'=>'Activitylog Report', 'grid_count'=>1,'file_name'=>'activitylogreport.pdf');

		foreach($selectColumns as $col){
			switch($col){
				case 'menuname': $field_names[] = array(
										'field_name'=>'menuname',
										'field_label'=>'Menu'
										);
										$field_widths[] = 40;
										$data['field_name_align'][] = 'C';
										break;
				case 'username' : $field_names[] = array(
										'field_name'=>'username',
										'field_label'=>'User'
										);
										$field_widths[] = 30;
										$data['field_name_align'][] = 'C';
										break;
				case 'empId' : $field_names[] = array(
									'field_name'=>'empId',
									'field_label'=>'Employee ID'
									);
									$field_widths[] = 30;
									$data['field_name_align'][] = 'C';
									break;
				case 'useraction': $field_names[] = array(
									'field_name'=>'useraction',
									'field_label'=>'Action'
									);
									$field_widths[] = 15;
									$data['field_name_align'][] = 'C';
									break;
				case 'modifieddate': $field_names[] = array(
										'field_name'=>'modifieddate',
										'field_label'=>'Modified Date'
										);
										$field_widths[] = 55;
										$data['field_name_align'][] = 'C';
										break;
			}
		}

		if(count($selectColumns) != 5){
			$totalPresentFieldWidth = 	array_sum($field_widths);
			foreach($field_widths as $key => $width){
				$field_widths[$key] = ($width*180)/$totalPresentFieldWidth;
			}
		}
		$message = $this->_helper->PdfHelper->generateReport($field_names, $finalArray, $field_widths, $data);
		$this->_helper->json(array('file_name'=>$data['file_name']));
	}

	// To download pdf
	public function downloadreportAction(){
		$file_name = $this->_getParam('file_name', NULL);
		if(!empty($file_name)){
			$file = BASE_PATH.'/downloads/reports/'.$this->_getParam('file_name');
			$status = sapp_Global::downloadReport($file);
		}

	}

	public function agencylistreportAction()
	{
		try{
			$reportsmodel = new Default_Model_Reports();
			$msgarray = array();

			$dashboardcall = $this->_getParam('dashboardcall');
			if($dashboardcall == 'Yes')
			$perPage = DASHBOARD_PERPAGE;
			else
			$perPage = PERPAGE;
			$bgchecksort = false;

			$selectFields = array('agencyname'=>'Agency','website_url'=>'Website Url ID','primaryphone'=>'Primary Phone','secondaryphone'=>'Secondary Phone','address'=>'Address','bg_checktype'=>'Screening Type');
			$selectColumns = array_keys($selectFields);

			$checktypeModal = new Default_Model_Bgscreeningtype();
			$typesData = $checktypeModal->fetchAll('isactive=1','type')->toArray();
			if(empty($typesData))
			{
				$msgarray['bg_checktypef'] = 'Screening types are not configured yet.';
			}

			if (($this->_getParam('fields') != '')){
				$selectColumns = explode(',',$this->_request->getParam('fields')); 
			}


			$searchQuery = '';
			if($this->_request->getParam('agencynamef') != ''){
				$searchQuery .= ' agencyname ="'.$this->_request->getParam('agencynamef') .'" AND';
			}
			if($this->_request->getParam('website_urlf') != ''){
				$searchQuery .= ' website_url ="'.$this->_request->getParam('website_urlf') .'" AND';
			}
			if($this->_request->getParam('primaryphonef') != ''){
				$searchQuery .= ' primaryphone ="'.$this->_request->getParam('primaryphonef') .'" AND';
			}
			if($this->_request->getParam('bg_checktypef') != '' && ($this->_request->getParam('bg_checktypef') != 'null')){
				$searchbgcheck = '(';
				if(is_array($this->_request->getParam('bg_checktypef')))
				$bgcheckarray = $this->_request->getParam('bg_checktypef');
				else
				$bgcheckarray = explode(',',$this->_request->getParam('bg_checktypef'));

				if(count($bgcheckarray) > 1){
					foreach($bgcheckarray as $bgcheck){
						$searchbgcheck = $searchbgcheck.'bg_checktype like "'.$bgcheck.',%" OR bg_checktype like "%,'.$bgcheck.'" OR bg_checktype like "%,'.$bgcheck.',%" OR ';
					}
					$searchbgcheck = ($searchbgcheck != '')?substr($searchbgcheck, 0, -3).')':$searchbgcheck;
				}else if(count($bgcheckarray) == 1){
				    foreach($bgcheckarray as $bgcheck){
						$searchbgcheck = $searchbgcheck.' bg_checktype like "%'.$bgcheck.'%" OR';
					}
				  $searchbgcheck = ($searchbgcheck != '')?substr($searchbgcheck, 0, -3).')':$searchbgcheck;				
				}
				$searchQuery .= $searchbgcheck .' AND';
			}

			if($searchQuery != ''){
				$searchQuery = rtrim($searchQuery," AND");
			}

			$pageNo = 1;

			if($this->_request->getParam('pageno') != ''){
				$pageNo = intval($this->_request->getParam('pageno'));
			}
			if($this->_request->getParam('per_page') != ''){
				$perPage = intval($this->_request->getParam('per_page'));
			}

			/*sort field request*/
			$by = $sortby = "modifieddate";
			$order =$funorder= "desc";

			if($this->_request->getParam('sortby') != ''){
				$by = $sortby = $this->_request->getParam('sortby');
				$order= $funorder = $this->_request->getParam('order');
				if($sortby == 'bg_checktype'){
					$bgchecksort = true;
				}
			}
			if($this->getRequest()->getPost() || $this->_request->getParam('generatereport',null) != ''){
				$this->_helper->layout->disableLayout();


				if(!$bgchecksort){
					$agencylistData = $reportsmodel->getAgencyListReportData($by,$funorder,$pageNo, $perPage, $searchQuery,$selectColumns); //echo '<pre>';print_r($agencylistData); exit;
					$agencylistCount = $reportsmodel->getAgencyListReportCount($searchQuery);
				}
				else{//bg_check sort
					$agencylistData = $reportsmodel->getAgencyListReportData($by,$funorder,'', $perPage, $searchQuery,$selectColumns); //echo '<pre>';print_r($agencylistData); exit;
					$agencylistCount = $reportsmodel->getAgencyListReportCount($searchQuery);
					$totalArraySortByScreeningtype = $this->createagencylistreportfinalArray($agencylistData,$selectColumns,$order);

					$endIndex = intval($perPage);

					if($pageNo != 1){
						$startIndex = (intval($pageNo)-1) * intval($perPage);
					}else{
						$startIndex = 0;
					}
					$finalArray = array_slice($totalArraySortByScreeningtype,$startIndex,$endIndex);
				}

			}else{

				$pageNo = 1;
				$order = 'desc';
				$sortby = 'modifieddate';
				$perPage = 20;
					
				$searchData = '';
				$selectColumns = array( 'agencyname','website_url','primaryphone','secondaryphone','address','bg_checktype');
				$agencylistData = $reportsmodel->getAgencyListReportData($sortby,$order,$pageNo, $perPage, $searchData,$selectColumns);
				$agencylistCount = $reportsmodel->getAgencyListReportCount();
			}

			$lastpage =  ceil($agencylistCount/$perPage);
			if(!$bgchecksort){
				$finalArray = $this->createagencylistreportfinalArray($agencylistData,$selectColumns);
			}

			if($this->getRequest()->getPost()){
				// To generate PDF START
				if (($this->_getParam('fields') != '')){
					$selectColumns = explode(',',$this->_request->getParam('fields')); // exit;
				}
				if($this->_request->getParam('generatereport') == 'pdf'){ 
					$this->generateAgencyListPdf($finalArray,$selectColumns);
				}

			}
			if($this->_getParam('generatereport') == 'xcel'){ //xcel generation
				if (($this->_getParam('fields') != '')){
					$selectColumns = explode(',',$this->_request->getParam('fields')); 
				}
				foreach($selectFields as $key=>$val)
				{
					foreach($selectColumns as $column)
					{
						if($column == $key)
						$selectColumnLabels[$key] = $val;
					}
				}
				sapp_Global::export_to_excel($finalArray,$selectColumnLabels,'Agencylist Report.xlsx');
				exit;
			}

			$agencylistreport_form = new Default_Form_agencylistreport();
			$this->view->form = $agencylistreport_form;

			$this->view->totalselectfields = $selectFields;
			$this->view->tabkeys = implode(',',$selectColumns);
			$this->view->agencylistData = $finalArray;
			$this->view->totalCount = $agencylistCount;
			$this->view->pageNo = $pageNo;
			$this->view->perPage = $perPage;
			$this->view->sortBy = $sortby;
			$this->view->order = $order;
			$this->view->lastPageNo = $lastpage;
			$this->view->msgarray = $msgarray;


		}catch(exception $e){
			echo $e->getMessage();
		}

	}

	public function createagencylistreportfinalArray($agencylistData,$selectColumns,$bgchecksortOrder = ''){

		$reportsmodel = new Default_Model_Reports();
		$finalArray = array();
		$bgcheckArray = array();
		if(count($agencylistData) > 0){
			$bg_checktypeSortArray = array();
			foreach($agencylistData as $agencylist){
				if(isset($agencylist['bg_checktype'])){
					$aglistbgcheck = explode(',',$agencylist['bg_checktype']);
					if(count($aglistbgcheck) > 0){
						foreach($aglistbgcheck as $bgcheck){
							if(!in_array($bgcheck, $bgcheckArray)) {
								array_push($bgcheckArray,$bgcheck);
							}
						}
					}
				}
			}

			if(!empty($bgcheckArray)){
				$bgcheckNameArray = $reportsmodel->getBgCheckNamesByIds($bgcheckArray);
			}

			foreach($agencylistData as $key => $agencylist)
			{
				if(in_array("agencyname", $selectColumns)){
					$finalArray[$key]['agencyname'] = $agencylist['agencyname'];
				}
				if(in_array("website_url", $selectColumns)){
					$finalArray[$key]['website_url'] = $agencylist['website_url'];
				}
				if(in_array("primaryphone", $selectColumns)){
					$finalArray[$key]['primaryphone'] = $agencylist['primaryphone'];
				}
				if(in_array("secondaryphone", $selectColumns)){
					$finalArray[$key]['secondaryphone'] = $agencylist['secondaryphone'];
				}
				if(in_array("address", $selectColumns)){
					$finalArray[$key]['address'] = strip_tags($agencylist['address']);
				}
				if(in_array("bg_checktype", $selectColumns)){
					$screeningtype = '';
					if(isset($agencylist['bg_checktype'])){ 
						$screeninglistarray = explode(',',$agencylist['bg_checktype']);
						foreach($screeninglistarray as $bgcheck){
							if(isset($bgcheckNameArray[$bgcheck])){
								$screeningtype = $screeningtype.$bgcheckNameArray[$bgcheck].' ,';
							}
						}
						$screeningtype = ($screeningtype != '')?substr($screeningtype, 0, -1):$screeningtype;
						$finalArray[$key]['bg_checktype'] = ucfirst($screeningtype);
						$bg_checktypeSortArray[$key] = ucfirst($screeningtype);
					}
				}
			}
		}
		if($bgchecksortOrder != ''){
			$bgcheckorder = ($bgchecksortOrder == 'asc')?SORT_ASC:SORT_DESC;
			array_multisort($bg_checktypeSortArray, $bgcheckorder,$finalArray);
		}
		return $finalArray;
	}

	/*agency list pdf*/
	public function generateAgencyListPdf($finalArray,$selectColumns){
		$field_names = array();
		$field_widths = array();
		$fieldwidth = '';
		$data['field_name_align'] = array();

		$data = array('grid_no'=>1, 'project_name'=>'', 'object_name'=>'Agency list Report', 'grid_count'=>1,'file_name'=>'agencylistreport.pdf');

		foreach($selectColumns as $col){
			switch($col){
				case 'agencyname': $field_names[] = array(
										'field_name'=>'agencyname',
										'field_label'=>'Agency'
										);
										$field_widths[] = 30;
										$data['field_name_align'][] = 'C';
										break;
				case 'website_url' : $field_names[] = array(
										'field_name'=>'website_url',
										'field_label'=>'Website Url'
										);
										$field_widths[] = 25;
										$data['field_name_align'][] = 'C';
										break;
				case 'primaryphone' : $field_names[] = array(
									'field_name'=>'primaryphone',
									'field_label'=>'Primary Phone'
									);
									$field_widths[] = 20;
									$data['field_name_align'][] = 'C';
									break;
				case 'secondaryphone': $field_names[] = array(
									'field_name'=>'secondaryphone',
									'field_label'=>'Secondary Phone'
									);
									$field_widths[] = 20;
									$data['field_name_align'][] = 'C';
									break;
				case 'address': $field_names[] = array(
									'field_name'=>'address',
									'field_label'=>'Address'
									);
									$field_widths[] = 30;
									$data['field_name_align'][] = 'C';
									break;
				case 'bg_checktype': $field_names[] = array(
										'field_name'=>'bg_checktype',
										'field_label'=>'Screening Types'
										);
										$field_widths[] = 45;
										$data['field_name_align'][] = 'C';
										break;
			}
		}

		if(count($selectColumns) != 6){
			$totalPresentFieldWidth = 	array_sum($field_widths);
			foreach($field_widths as $key => $width){
				$field_widths[$key] = ($width*180)/$totalPresentFieldWidth;
			}
		}
		$message = $this->_helper->PdfHelper->generateReport($field_names, $finalArray, $field_widths, $data);
		$this->_helper->json(array('file_name'=>$data['file_name']));
	}

	public function agencynameautoAction(){
		$reportsmodel = new Default_Model_Reports();
		$term = $this->_getParam('term',null);
		$output = array(array('id' => '','value' => 'No records','label' => 'No records'));
			
		if($term != '')
		{
			$emp_arr = $reportsmodel->getAutoAgencyName($term);

			if(count($emp_arr)>0)
			{
				$output = array();
				foreach($emp_arr as $emp)
				{
					$output[] = array('id' => $emp['agencyname'],'value' => $emp['agencyname'],'label' => $emp['agencyname']);
				}
			}
		}
		$this->_helper->json($output);
	}

	public function agencysebsiteautoAction(){
		$reportsmodel = new Default_Model_Reports();
		$term = $this->_getParam('term',null);
		$output = array(array('id' => '','value' => 'No records','label' => 'No records'));
			
		if($term != '')
		{
			$emp_arr = $reportsmodel->getAutoAgencyWebsiteUrl($term);

			if(count($emp_arr)>0)
			{
				$output = array();
				foreach($emp_arr as $emp)
				{
					$output[] = array('id' => $emp['website_url'],'value' => $emp['website_url'],'label' => $emp['website_url']);
				}
			}
		}
		$this->_helper->json($output);
	}

	public function empscreeningAction()
	{
		$form = new Default_Form_empscreeningreport();
		$msgarray = array();
		$reportsmodel = new Default_Model_Reports();
		$checktypeModal = new Default_Model_Bgscreeningtype();

		$specimen = $this->_request->getParam('specimen');
		$empname = $this->_request->getParam('empname');
		$agencyname = $this->_request->getParam('agencyname');
		$screeningtype = $this->_request->getParam('screeningtype');
		$process_status = $this->_request->getParam('process_status');
		$month = $this->_request->getParam('month');
		$year = $this->_request->getParam('year');

		$pageno = intval($this->_request->getParam('pageno',1));
		$perPage = intval($this->_request->getParam('perpage',PERPAGE));
		if($perPage == 0) $perPage = PERPAGE;
		$by = $this->_request->getParam('by','Desc');
		$sortby = $this->_request->getParam('sortby','modifieddate');
		$columnby = $this->_request->getParam('columnby');
		$columnsortby = $this->_request->getParam('columnsortby');
		$checkedheaders = $this->_request->getParam('checkedheaders');
		$typesData = $checktypeModal->fetchAll('isactive=1','type')->toArray();		
		if(empty($typesData)){
			$msgarray['screeningtype'] = 'Screening types are not configured yet.';
		}else{
			$msgarray['screeningtype'] = '';
		}
		$searchQuery = '';
		if($checkedheaders != '')
		$selectColumns = explode(',',$checkedheaders);

		if($columnby !='')
		$by = $columnby;
		if($columnsortby !='')
		$sortby = $columnsortby;

		if($specimen !='')
		$searchQuery .= 'specimen_flag = "'.$specimen.'" AND ';
		if($empname !='')
		$searchQuery .= 'specimen_name = "'.$empname.'" AND ';
		if($agencyname !='')
		$searchQuery .= 'agencyname = "'.$agencyname.'" AND ';
		if($screeningtype != '' && ($screeningtype != 'null'))
		{
			$searchbgcheck = '(';
			$bgcheckarray = explode(',',$screeningtype);

			if(count($bgcheckarray) > 0)
			{
				foreach($bgcheckarray as $bgcheck)
				{
					$searchbgcheck = $searchbgcheck.'screeningtypeid = '.$bgcheck.' OR screeningtypeid = '.$bgcheck.' OR screeningtypeid = '.$bgcheck.' OR ';
				}
				$searchbgcheck = ($searchbgcheck != '')?substr($searchbgcheck, 0, -3).')':$searchbgcheck;
			}
			$searchQuery .= $searchbgcheck .' AND ';
		}
		if($process_status !='')
		$searchQuery .= 'process_status = "'.$process_status.'" AND ';
		if($month !='')
		$searchQuery .= 'month_name = "'.$month.'" AND ';
		if($year !='')
		$searchQuery .= 'year_year = "'.$year.'" AND ';

		$searchQuery = rtrim($searchQuery," AND ");
		$selectColumnLabels = array();
		$empscreeningLabelsArr = array('specimen_name'=>'Specimen','specimen_flag_name'=>'Specimen type','agencyname'=>'Agency','screeningtype_name'=>'Screening type','process_status'=>'Process Status','createdname'=>'Sent by','createddate'=>'Sent on','isactive_text'=>'Status');

		if(empty($selectColumns))
		{
			$selectColumns = array('specimen_name','specimen_flag_name','agencyname','screeningtype_name','process_status','createdname','createddate','isactive_text');
			$selectColumnLabels = $empscreeningLabelsArr;
		}
		else
		{
			foreach($empscreeningLabelsArr as $key=>$val)
			{
				foreach($selectColumns as $column)
				{
					if($column == $key) 
					$selectColumnLabels[$key] = $val;
				}
			}

		}
		$empscreeningArr = $reportsmodel->getEmpscreeningInfo($sortby,$by,$pageno,$perPage,$searchQuery);
		$empscreeningCount = $reportsmodel->getEmpscreeningCount($searchQuery);
		if($empscreeningCount > 0)
		{
			$lastpage =  ceil($empscreeningCount/$perPage);
		}
		else
		{
			$lastpage = '';
			$empscreeningCount = '';
		}
		$finalArray = $this->createEmpscreeningReportFinalArray($empscreeningArr,$selectColumns);

		$empscreeningArr = $finalArray;
		$this->view->selectColumnLabels = $selectColumnLabels;
		$this->view->empscreeningLabels = $empscreeningLabelsArr;
		$this->view->empscreeningArr = $empscreeningArr;
		$this->view->pageno = $pageno;
		$this->view->perpage = $perPage;
		$this->view->lastpage = $lastpage;
		$this->view->by = $by;
		$this->view->sortby = $sortby;
		$this->view->totalcount = $empscreeningCount;
		$this->view->msgarray = $msgarray;

		$this->view->form = $form;

	}

	public function createEmpscreeningReportFinalArray($dataArray,$columnArray)
	{
		$finalArray = array();
		if(!empty($dataArray))
		{
			foreach($dataArray as $key => $curr)
			{
				if(in_array("specimen_name", $columnArray)){
					$finalArray[$key]['specimen_name'] = $curr['specimen_name'];
				}
				if(in_array("specimen_flag_name", $columnArray)){
					$finalArray[$key]['specimen_flag_name'] = $curr['specimen_flag_name'];
				}
				if(in_array("screeningtype_name", $columnArray)){
					$finalArray[$key]['screeningtype_name'] = $curr['screeningtype_name'];
				}
				if(in_array("agencyname", $columnArray)){
					$finalArray[$key]['agencyname'] = $curr['agencyname'];
				}
				if(in_array("process_status", $columnArray)){
					$finalArray[$key]['process_status'] = $curr['process_status'];
				}

				if(in_array("createdname", $columnArray)){
					$finalArray[$key]['createdname'] = $curr['createdname'];
				}

				if(in_array("createddate", $columnArray)){
					$finalArray[$key]['createddate'] = $curr['createddate'];
				}

				if(in_array("isactive_text", $columnArray)){
					$finalArray[$key]['isactive_text'] = $curr['isactive_text'];
				}
			}
		}
		return $finalArray;
	}

	public function getspecimennamesAction()
	{
		$flagterm = $this->_getParam('flag',null);
		$term = $this->_getParam('term',null);
		$output = array(array('id' => '','value' => 'No records','label' => 'No records'));
		$reportsmodel = new Default_Model_Reports();
		if($term != '')
		{
			$buArr = $reportsmodel->getspecimenNames($term);
			if(count($buArr)>0)
			{
				$output = array();
				foreach($buArr as $unit)
				{
					$output[] = array('id' => $unit['id'],'value' => $unit['specimen_name'],'label' => $unit['specimen_name']);
				}
			}
		}
		$this->_helper->json($output);
	}

	public function getagencynamesAction()
	{
		$flagterm = $this->_getParam('flag',null);
		$term = $this->_getParam('term',null);
		$output = array(array('id' => '','value' => 'No records','label' => 'No records'));
		$reportsmodel = new Default_Model_Reports();
		if($term != '')
		{
			$buArr = $reportsmodel->getagencyNames($term);
			if(count($buArr)>0)
			{
				$output = array();
				foreach($buArr as $unit)
				{
					$output[] = array('id' => $unit['id'],'value' => $unit['agencyname'],'label' => $unit['agencyname']);
				}
			}
		}
		$this->_helper->json($output);
	}

	public function getexcelreportempscreeningAction()
	{
		$reportsmodel = new Default_Model_Reports();
		$this->_helper->layout->disableLayout();
		$param_arr = $this->_getAllParams();
		$ar = $this->getexportdata($param_arr);
		sapp_Global::export_to_excel($ar ['finalar'],$ar['cols_arr'],"Empscreening.xlsx");
		exit;
	}

	public function getexportdata($param_arr)
	{
		$reportsmodel = new Default_Model_Reports();
		if(isset($param_arr['cols_arr']))	unset($param_arr['cols_arr']);
		$page_no = isset($param_arr['pageno'])?intval($param_arr['pageno']):1;
		$per_page = isset($param_arr['perpage'])?intval($param_arr['perpage']):PERPAGE;
		if($per_page == 0) $per_page = PERPAGE;
		$sort_name = $param_arr['sortby'];
		$sort_type = $param_arr['by'];
		$searchQuery = '';	$funorder = '';

		$columnby = $param_arr['columnby'];
		$columnsortby = $param_arr['columnsortby'];
		if($columnby !='')
		$sort_type = $columnby;
		if($columnsortby !='')
		$sort_name = $columnsortby;
			
			
		$cols_param = explode(',',$param_arr['checkedheaders']);
			
		$empscreeningLabelsArr = array('specimen_name'=>'Specimen','specimen_flag_name'=>'Specimen type','agencyname'=>'Agency','screeningtype_name'=>'Screening type','process_status'=>'Process Status','createdname'=>'Sent by','createddate'=>'Sent on','isactive_text'=>'Status');
		$cols_param_arr = array();
		foreach($empscreeningLabelsArr as $key=>$val)
		{
			foreach( $cols_param  as $col)
			{
				if($col == $key)
				{
					$cols_param_arr[$key] = $val;
				}
			}
		}
		$specimen = $param_arr['specimen'];
		$empname = $param_arr['empname'];
		$agencyname = $param_arr['agencyname'];

		$screeningtype = isset($param_arr['screeningtype']) ? $param_arr['screeningtype'] : array();
		
		$process_status = $param_arr['process_status'];
		$month = $param_arr['month'];
		$year = $param_arr['year'];

		if($specimen !='')
		$searchQuery .= 'specimen_flag = "'.$specimen.'" AND ';
		if($empname !='')
		$searchQuery .= 'specimen_name = "'.$empname.'" AND ';
		if($agencyname !='')
		$searchQuery .= 'agencyname = "'.$agencyname.'" AND ';
		if(!empty($screeningtype))
		{
			$searchbgcheck = '(';

			//$bgcheckarray = explode(',',$screeningtype);
			$bgcheckarray = $screeningtype;

			if(count($bgcheckarray) > 0)
			{
				foreach($bgcheckarray as $bgcheck)
				{
					$searchbgcheck = $searchbgcheck.'screeningtypeid = '.$bgcheck.' OR screeningtypeid = '.$bgcheck.' OR screeningtypeid = '.$bgcheck.' OR ';
				}
				$searchbgcheck = ($searchbgcheck != '')?substr($searchbgcheck, 0, -3).')':$searchbgcheck;
			}
			$searchQuery .= $searchbgcheck .' AND ';
		}
		if($process_status !='')
		$searchQuery .= 'process_status = "'.$process_status.'" AND ';
		if($month !='')
		$searchQuery .= 'month_name = "'.$month.'" AND ';
		if($year !='')
		$searchQuery .= 'year_year = "'.$year.'" AND ';

		$searchQuery = rtrim($searchQuery," AND ");
			
		$empscreeningArr = $reportsmodel->getEmpscreeningInfo($sort_name,$sort_type,$page_no,$per_page,$searchQuery);
		$finalArray = $this->createEmpscreeningReportFinalArray($empscreeningArr,$cols_param);
		return array('finalar'=>$finalArray,'cols'=>$cols_param,'cols_arr'=>$cols_param_arr);
	}

	public function getempscreeningpdfAction()
	{
		$this->_helper->layout->disableLayout();
		$param_arr = $this->_getAllParams();
		$ar = $this->getexportdata($param_arr);
		$this->generateEmpscreeningPDF($ar ['finalar'],$ar['cols']);
	}
	public function generateEmpscreeningPDF($finalArray,$selectColumns)
	{
		$empscreeningLabelsArr = array('specimen_name'=>'Specimen','specimen_flag_name'=>'Specimen type','agencyname'=>'Agency','screeningtype_name'=>'Screening type','process_status'=>'Process Status','createdname'=>'Sent by','createddate'=>'Sent on','isactive_text'=>'Status');
		$field_names = array();
		$field_widths = array();
		$fieldwidth = '';
		$data['field_name_align'] = array();

		foreach($selectColumns as $col){
			switch($col){
				case 'specimen_name': $field_names[] = array(
										'field_name'=>'specimen_name',
										'field_label'=>'Specimen'
										);
										$field_widths[] = 25;
										$data['field_name_align'][] = 'C';
										break;
				case 'specimen_flag_name' : $field_names[] = array(
										'field_name'=>'specimen_flag_name',
										'field_label'=>'Specimen type'
										);
										$field_widths[] = 20;
										$data['field_name_align'][] = 'C';
										break;
				case 'agencyname' : $field_names[] = array(
									'field_name'=>'agencyname',
									'field_label'=>'Agency'
									);
									$field_widths[] = 25;
									$data['field_name_align'][] = 'C';
									break;
				case 'screeningtype_name': $field_names[] = array(
									'field_name'=>'screeningtype_name',

									'field_label'=>'Screening type'

									);
									$field_widths[] = 20;
									$data['field_name_align'][] = 'C';
									break;
				case 'process_status': $field_names[] = array(
										'field_name'=>'process_status',
										'field_label'=>'Process Status'
										);
										$field_widths[] = 20;
										$data['field_name_align'][] = 'C';
										break;
				case 'createdname': $field_names[] = array(
										'field_name'=>'createdname',
										'field_label'=>'Sent by'
										);
										$field_widths[] = 25;
										$data['field_name_align'][] = 'C';
										break;
				case 'createddate': $field_names[] = array(
										'field_name'=>'createddate',
										'field_label'=>'Sent on'
										);
										$field_widths[] = 22;
										$data['field_name_align'][] = 'C';
										break;
				case 'isactive_text': $field_names[] = array(
										'field_name'=>'isactive_text',
										'field_label'=>'status'
										);
										$field_widths[] = 20;
										$data['field_name_align'][] = 'C';
										break;
			}
		}

		if(count($selectColumns) != count($selectColumns)){
			$totalPresentFieldWidth = 	array_sum($field_widths);
			foreach($field_widths as $key => $width){
				$field_widths[$key] = ($width*180)/$totalPresentFieldWidth;
			}
		}

		$data = array('grid_no'=>1, 'project_name'=>'', 'object_name'=>'Employee/Candidate Screening Report', 'grid_count'=>1,'file_name'=>'empscreening.pdf');

		$pdf = $this->_helper->PdfHelper->generateReport($field_names, $finalArray, $field_widths, $data);
		return $this->_helper->json(array('file_name'=>$data['file_name']));
	}

	
}