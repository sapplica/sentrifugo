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
 * 
 * @name IndexController
 * 
 * IndexController is responsible to make service based on the given inputs
 * 
 * @author Suresh Babu Bhupathi
 * @version 1.0

 *
 */
class Services_IndexController extends Zend_Rest_Controller
{	
	
    public function init()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->getHelper('layout')->disableLayout();
        $this->usergroup = 5;
    }
	
    public function preDispatch()
    {
		
    }
	
    private function authAccepted($auth)
    {
        $keys = Zend_Registry::get('config.services');		
        $this->clientKey = $keys['testapp']['secret'];		
        $client = new sapp_HttpClient("testapp","sapplica");		
        $signature = $client->signArguments($keys);		
        if($signature == $auth)		
            return true;

        return false;
    }	
	
	/**
	 * The index action handles index/list requests; it should respond with a
	 * list of the requested resources.
	 */
	public function indexAction() 
	{
		//HTTP code 500 might not good choice here.
		$paramsarray = $this->getRequest ()->getParams();
		
		$this->getResponse ()->setHttpResponseCode ( 500 );
		$testString = array('status'=>'0','message'=>'No list/index allowed.','result'=>'');
		$this->_handleStruct($testString);//$this->_helper->json($testString);
	}	
	
	/**
	 * The get action handles GET requests and receives parameters; it
	 * should respond with the server resource state of the resource identified
	 * by the parameters value.
	 */
	public function getAction() 
	{
            $paramsarray = $this->getRequest ()->getParams();
		//echo "<pre>";print_r($paramsarray);exit;
		$servicetocall = $paramsarray['service'];

		if (isset($paramsarray['service']) && $paramsarray['service'] != '') 
		{
		    //$servicesModel = new Services_Model_Services();
			$result = $this->$servicetocall($paramsarray);
			//$result = $servicesModel->$servicetocall($paramsarray);
		    $this->getResponse ()->setHttpResponseCode ( 200 );
		} 			
		else 
		{
			$result= new sapp_ErrorCode('no parameters!');
			//prevent the service is not found.
			$this->getResponse ()->setHttpResponseCode ( 200 );
		}
       	//echo "<pre>";print_r($result);exit;	
		$this->_handleStruct( $result );
		
	}
	
	/**
	 * The post action handles POST requests; it should accept and digest a
	 * POSTed resource representation and persist the resource state.
	 */
	public function postAction() 
	{
		$paramsarray = $this->getRequest ()->getParams();
		//echo "<pre>";print_r($paramsarray);exit;
		$servicetocall = $paramsarray['service'];
		if (isset($paramsarray['service']) && $paramsarray['service'] != '') 
		{
		    //$servicesModel = new Services_Model_Services();
			$result = $this->$servicetocall($paramsarray);
			//$result = $servicesModel->$servicetocall($paramsarray);
		    $this->getResponse ()->setHttpResponseCode ( 200 );
		} 			
		else 
		{
			$result= new sapp_ErrorCode('no parameters!');
			//prevent the service is not found.
			$this->getResponse ()->setHttpResponseCode ( 200 );
		}
       	//echo "<pre>";print_r($result);exit;	
		$this->_handleStruct( $result );

	}
	
	/**
	 * The put action handles PUT requests and receives parameters; it
	 * should update the server resource state of the resource identified by
	 * the parameters value.
	 */
	public function putAction() 
	{	
		
	}
	
	/**
	 * The delete action handles DELETE requests and receives an 'id'
	 * parameter; it should update the server resource state of the resource
	 * identified by the 'id' value.
	 */
	public function deleteAction() {
	
	}		
	 /**
	 * Handle an array or object result
	 *
	 * @param array|object $struct Result Value
	 * @return string XML Response
	 */
	protected function _handleStruct($struct)
	{
            $testString = $struct;
		//echo $testString;exit;
            $this->_helper->json($testString);
	}
        public function deptroleOnchange($params_arr)
        {
            $result = array();$status = 0;$message = "Invalid value.";
            if(isset($params_arr['role_id']) && $params_arr['role_id'] != '' && 
                isset($params_arr['group_id']) && $params_arr['group_id'] != '' &&
                isset($params_arr['selected_group']) && $params_arr['selected_group'] != '' &&
                isset($params_arr['dept_id']) && $params_arr['dept_id'] != '' &&
                isset($params_arr['employee_id'])  &&
                isset($params_arr['userid']) && $params_arr['userid'] != ''
            )
            {
                $role_id = $params_arr['role_id'];
                $group_id = $params_arr['group_id'];
                $userid = $params_arr['userid'];
                $dept_id = $params_arr['dept_id'];
                $employee_id = $params_arr['employee_id'];
                $employee_group = $params_arr['selected_group'];
                
                if($role_id == SUPERADMINROLE || $group_id == MANAGEMENT_GROUP || $group_id == HR_GROUP)
                {
                    $privilege_flag = sapp_Global::_checkprivileges(EMPLOYEE,$group_id,$role_id,'add');
                    if($privilege_flag == 'Yes')
                    {
                        $usersModel = new Default_Model_Users();
                        $reportingManagerData = $usersModel->getReportingManagerList_employees($dept_id,$employee_id,$employee_group);
                        $emp_str = sapp_Global::selectOptionBuilder("", "Select Reporting Manager");
                        if(!empty($reportingManagerData))
                        {
                            $status = 1;
                            $message = "success";
                            //echo "<pre>";print_r($reportingManagerData);echo "</pre>";
                            foreach($reportingManagerData as $data)
                            {
                                $emp_str .= sapp_Global::selectOptionBuilder($data['id'], $data['name'], $data['profileimg']);
                            }
                        }
                        else 
                        {
                            $status = 0;
                            $message = "Employees are not added yet.";
                        }
                        $result['reporting_managers'] = $emp_str;
                    }
                }
            }
            return array('status' => $status,'message' => $message,'result' => $result);
            
        }
        /**
         * This function acts as a service for onchange of mode of employment to get candidates or referers.
         * @param array $params_arr  = array of parameters
         * @return array  Array of options.
         */
        public function modeempOnchange($params_arr)
        {
            $result = array();$status = 0;$message = "Invalid mode of employment.";
            if(isset($params_arr['role_id']) && $params_arr['role_id'] != '' && 
                isset($params_arr['group_id']) && $params_arr['group_id'] != '' &&
                isset($params_arr['modeofentry']) && $params_arr['modeofentry'] != '' &&
                isset($params_arr['userid']) && $params_arr['userid'] != ''
            )
            {
                $role_id = $params_arr['role_id'];
                $group_id = $params_arr['group_id'];
                $userid = $params_arr['userid'];
                $modeofentry = $params_arr['modeofentry'];
                
                if($role_id == SUPERADMINROLE || $group_id == MANAGEMENT_GROUP || $group_id == HR_GROUP)
                {
                    $privilege_flag = sapp_Global::_checkprivileges(EMPLOYEE,$group_id,$role_id,'add');
                    if($privilege_flag == 'Yes')
                    {
                        if($modeofentry == "Interview" || $modeofentry == 'Other')
                        {
                            $candidate_model = new Default_Model_Candidatedetails();
                            $candidate_options = $candidate_model->getCandidatesNamesForUsers();
                            $candidate_str = sapp_Global::selectOptionBuilder("", "Select Candidate");
                            //echo "<pre>";print_r($candidate_options);echo "</pre>";
                            if(!empty($candidate_options))
                            {
                                $status = 1;
                                $message = "success";
                                foreach($candidate_options as $id => $name)
                                {
                                    $candidate_str .= sapp_Global::selectOptionBuilder($id, $name);
                                }
                                
                            }
                            else 
                            {
                                $status = 0;
                                $message = "No selected candidates.";
                            }
                            $result['candidates'] = $candidate_str;
                        }
                        else if($modeofentry == "Reference")
                        {
                            $user_model = new Default_Model_Usermanagement();
                            $referedby_options = $user_model->getRefferedByForUsers();
                            
                            $refered_str = sapp_Global::selectOptionBuilder("", "Select Referred By");
                            //echo "<pre>";print_r($candidate_options);echo "</pre>";
                            if(!empty($referedby_options))
                            {
                                $status = 1;
                                $message = "success";
                                foreach($referedby_options as $id => $name)
                                {
                                    $refered_str .= sapp_Global::selectOptionBuilder($id, $name);
                                }
                                
                            }
                            else 
                            {
                                $status = 0;
                                $message = "Employees are not added yet.";
                            }
                            $result['referred_by'] = $refered_str;
                        }
                    }
                }
            }
            return array('status' => $status,'message' => $message,'result' => $result);
        }
        /**
         * This function acts as a service for onchange of job title to get positions.
         * @param array $params_arr  = array of parameters
         * @return array  Array of positions options.
         */
        public function jobtitleOnchange($params_arr)
        {
            $result = array();$status = 0;$message = "Invalid job title.";
            if(isset($params_arr['role_id']) && $params_arr['role_id'] != '' && 
                isset($params_arr['group_id']) && $params_arr['group_id'] != '' &&
                isset($params_arr['jobtitle_id']) && $params_arr['jobtitle_id'] != '' &&
                isset($params_arr['userid']) && $params_arr['userid'] != ''
            )
            {
                $role_id = $params_arr['role_id'];
                $group_id = $params_arr['group_id'];
                $userid = $params_arr['userid'];
                $jobtitle_id = $params_arr['jobtitle_id'];
                
                if($role_id == SUPERADMINROLE || $group_id == MANAGEMENT_GROUP || $group_id == HR_GROUP)
                {
                    $privilege_flag = sapp_Global::_checkprivileges(EMPLOYEE,$group_id,$role_id,'add');
                    if($privilege_flag == 'Yes')
                    {
                        $status = 1;
                        $message = "success";
                        $positionsmodel = new Default_Model_Positions();
                        $positionlistArr = $positionsmodel->getPositionList($jobtitle_id);
                        $positions_str = sapp_Global::selectOptionBuilder("", "Select Position");
                        //echo "<pre>";print_r($positionlistArr);echo "</pre>";
                        if(!empty($positionlistArr))
                        {
                            foreach($positionlistArr as $data)
                            {
                                $positions_str .= sapp_Global::selectOptionBuilder($data['id'], $data['positionname']);
                            }
                            $result['positions'] = $positions_str;
                        }
                        else
                        {
                            $status = 0;
                            $message = "Positions are not configured yet.";
                        }
                    }
                }
            }
            return array('status' => $status,'message' => $message,'result' => $result);
        }
        /**
         * This function acts as a service for onchange of business unit to get departments.
         * @param array $params_arr  = array of parameters
         * @return array  Array of department options.
         */
        public function bunitOnchange($params_arr)
        {
            $result = array();$status = 0;$message = "Invalid business unit.";
            if(isset($params_arr['role_id']) && $params_arr['role_id'] != '' && 
                isset($params_arr['group_id']) && $params_arr['group_id'] != '' &&
                isset($params_arr['businessunit_id']) && $params_arr['businessunit_id'] != '' &&
                isset($params_arr['userid']) && $params_arr['userid'] != ''
            )
            {
                $role_id = $params_arr['role_id'];
                $group_id = $params_arr['group_id'];
                $userid = $params_arr['userid'];
                $businessunit_id = $params_arr['businessunit_id'];
                
                if($role_id == SUPERADMINROLE || $group_id == MANAGEMENT_GROUP || $group_id == HR_GROUP)
                {
                    $privilege_flag = sapp_Global::_checkprivileges(EMPLOYEE,$group_id,$role_id,'add');
                    if($privilege_flag == 'Yes')
                    {
                        $status = 1;
                        $message = "success";
                        $departmentsmodel = new Default_Model_Departments();
                        $departmentlistArr = $departmentsmodel->getDepartmentList($businessunit_id);
                        $dept_str = sapp_Global::selectOptionBuilder("", "Select Department");
                        //echo "<pre>";print_r($departmentlistArr);echo "</pre>";
                        if(!empty($departmentlistArr))
                        {
                            foreach($departmentlistArr as $data)
                            {
                                $dept_str .= sapp_Global::selectOptionBuilder($data['id'], $data['deptname']);
                            }
                            $result['departments'] = $dept_str;
                        }
                        else
                        {
                            $status = 0;
                            $message = "Departments are not configured yet.";
                        }
                    }
                }
            }
            return array('status' => $status,'message' => $message,'result' => $result);
        }
        /**
         * This function acts as a service for onchange of state to get cities(ex: in interviews).
         * @param array $params_arr  = array of parameters
         * @return array  Array of city options.
         */
        public function stateOnchange($params_arr)
        {
            $result = array();$status = 0;$message = "Invalid state.";
            if(isset($params_arr['state']) && $params_arr['state'] != '')
            {
                $state_id = $params_arr['state'];
                $status = 1;
                $message = "success";
                $citiesmodel = new Default_Model_Cities();
                $cities_data = $citiesmodel->getCitiesList($state_id);
                if(!empty($cities_data))
                {
                    $cities_str = sapp_Global::selectOptionBuilder("", "Select City");
                    foreach($cities_data as $sdata)
                    {
                        $cities_str .= sapp_Global::selectOptionBuilder($sdata['id'], $sdata['city_name']);
                    }
                    $result['city'] = $cities_str;
                }
                else 
                {
                    $status = 0;
                    $message = "Cities are not configured yet.";
                }
            }
            return array('status' => $status,'message' => $message,'result' => $result);
        }
        /**
         * This function acts as a service for onchange of country to get states(ex: in interviews).
         * @param array $params_arr  = array of parameters
         * @return array  Array of state options.
         */
        public function countryOnchange($params_arr)
        {
            $result = array();$status = 0;$message = "Invalid country.";
            if(isset($params_arr['country']) && $params_arr['country'] != '')
            {
                $country_id = $params_arr['country'];
                $status = 1;
                $message = "success";
                $statesmodel = new Default_Model_States();
                $states_data = $statesmodel->getStatesList($country_id);
                if(!empty($states_data))
                {
                    $states_str = sapp_Global::selectOptionBuilder("", "Select State");
                    foreach($states_data as $sdata)
                    {
                        $states_str .= sapp_Global::selectOptionBuilder($sdata['id'], $sdata['state_name']);
                    }
                    $result['state'] = $states_str;
                }
                else 
                {
                    $status = 0;
                    $message = "States are not configured yet.";
                }
                
                //echo "<pre>";print_r($states_data);echo "</pre>";
            }
            return array('status' => $status,'message' => $message,'result' => $result);
        }
        /**
         * This function acts as a service for onchange of requisition code in interviews.
         * @param array $params_arr  = array of parameters
         * @return array  Array of candidates,interviewers options.
         */
        public function requisitionOnchange($params_arr)
        {
            $result = array();$status = 0;$message = "Invalid requisition code.";
            if(isset($params_arr['role_id']) && $params_arr['role_id'] != '' && 
                isset($params_arr['group_id']) && $params_arr['group_id'] != '' &&
                isset($params_arr['req_id']) && $params_arr['req_id'] != '' &&
                isset($params_arr['userid']) && $params_arr['userid'] != ''
            )
            {
                $role_id = $params_arr['role_id'];
                $group_id = $params_arr['group_id'];
                $userid = $params_arr['userid'];
                $req_id = $params_arr['req_id'];
                
                if($req_id != '')
                {
                    if($role_id == SUPERADMINROLE || $group_id == MANAGEMENT_GROUP || $group_id == HR_GROUP)
                    {
                        $privilege_flag = sapp_Global::_checkprivileges(SCHEDULEINTERVIEWS,$group_id,$role_id,'add');
                        if($privilege_flag == 'Yes')
                        {
                            $status = 1;
                            $message = "success";
                            $req_model = new Default_Model_Requisition();
                            $data = $req_model->getcandidates_forinterview($req_id, $group_id, $userid);
                            if($data['candidates'] == 'nocandidates')
                            {
                                $message = "Candidates are not added yet.";
                                $status = 0;
                            }
                            else if($data['managers'] == 'nomanagers')
                            {
                                $message = "No Interviewers.";
                                $status = 0;
                            }
                            else 
                                $result = $data;
                        }
                    }
                }
            }
            return array('status' => $status,'message' => $message,'result' => $result);
        }
        /**
         * This function acts as a service for adding a interview.
         * @param array $params_arr  = array of parameters
         * @return array  Array of results.
         */
        public function addinterview($params_arr)
        {
            $result = array();$status = 0;$message = "No access to add interview.";
            if(isset($params_arr['role_id']) && $params_arr['role_id'] != '' && 
                isset($params_arr['group_id']) && $params_arr['group_id'] != '' &&
                isset($params_arr['userid']) && $params_arr['userid'] != ''
            )
            {
                $role_id = $params_arr['role_id'];
                $group_id = $params_arr['group_id'];
                $userid = $params_arr['userid'];
                
                if($role_id == SUPERADMINROLE || $group_id == MANAGEMENT_GROUP || $group_id == HR_GROUP)
                {
                    $privilege_flag = sapp_Global::_checkprivileges(SCHEDULEINTERVIEWS,$group_id,$role_id,'add');
                    if($privilege_flag == 'Yes')
                    {
                        $status = 1;
                        $message = "success";
                        $req_model = new Default_Model_Requisition();
                        $countryModal = new Default_Model_Countries();
                        
                        $req_data = $req_model->getReqForInterviews();
                        $req_options = $requisition_options = array();$countries_options = $cntry_options = array();
                        foreach($req_data as $req)
                        {
                            $req_options[$req['id']] = $req['requisition_code'].' - '.$req['jobtitlename'];
                        }
                        
                        $requisition_options = array(''=>'Select Requisition ID') + $req_options;
                        
                        $countriesData = $countryModal->fetchAll('isactive=1','country');
                        
                        foreach ($countriesData->toArray() as $data)
                        {
                            $cntry_options[trim($data['country_id_org'])] = $data['country'];
                        }
                        $countries_options = array('' => 'Select country') + $cntry_options;
                        
                        $interview_type_options = array('' => 'Select Interview Type',
                                               'In person' => 'In person',
                                               'Phone' => 'Phone',
                                               'Video conference' => 'Video conference');
                        if(empty($req_options) && count($req_options) == 0)
                        {
                            $status = 0;
                            $message = "No approved requisitions.";
                        }
                        else if(empty($cntry_options) && count($cntry_options) == 0)
                        {
                            $status = 0;
                            $message = "Countries are not configured yet.";
                        }
                        else 
                        {
                            $result['req_id'] = $requisition_options; 
                            $result['country'] = $countries_options; 
                            $result['interview_mode'] = $interview_type_options; 
                        }
                        
                    }
                }
            }
            return array('status' => $status,'message' => $message,'result' => $result);
        }
        /**
         * This function acts as a service to get interview list.
         * @param array $params_arr  = array of parameters
         * @return array  Array of interviews.
         */
	public function getiroundslist($params_arr)
        {
            $result = array();$status = 0;$message = "No data found.";
            $add_flag = 'No';$page_cnt = 1;
            if(isset($params_arr['role_id']) && $params_arr['role_id'] != '' && 
                isset($params_arr['group_id']) && $params_arr['group_id'] != '' &&
                isset($params_arr['userid']) && $params_arr['userid'] != ''
            )
            {
                $role_id = $params_arr['role_id'];
                $group_id = $params_arr['group_id'];
                $userid = $params_arr['userid'];
                $page_no = 1;
                $per_page = PERPAGE;
                
                if(isset($params_arr['page_no']) && $params_arr['page_no'] != '')
                    $page_no = $params_arr['page_no'];
                if(isset($params_arr['per_page']) && $params_arr['per_page'] != '')
                    $per_page = $params_arr['per_page'];
                
                $privilege_flag = sapp_Global::_check_menu_access(SCHEDULEINTERVIEWS,$group_id,$role_id);
                if($privilege_flag == 'Yes')
                {
                    $add_flag = sapp_Global::_checkprivileges(SCHEDULEINTERVIEWS,$group_id,$role_id,"add");
                    $emp_model = new Services_Model_Employee();
                    $total_data = $emp_model->getiroundslist($page_no,$per_page,$userid,$group_id);
                    
                    $interview_data = $total_data['rows'];
                    if(!empty($interview_data) && count($interview_data) > 0)
                    {
                        $page_cnt = $total_data['page_cnt'];
                        $message = "success";
                        $status = 1;
                        $result = $interview_data;
                    }
                }
            }
            return array('status' => $status,'message' => $message,'page_cnt' => $page_cnt,'result' => $result,'add_flag' => $add_flag);
        }
        
        /**
         * This function acts as a service to get employees list.
         * @param array $params_arr  = array of parameters
         * @return array  Array of employees.
         */
        public function getemplist($params_arr)
        {
            $result = array();$status = 0;$message = "No data found.";
            $add_flag = 'No';$page_cnt = 1;
            if(isset($params_arr['role_id']) && $params_arr['role_id'] != '' && isset($params_arr['group_id']) && $params_arr['group_id'] != '')
            {
                $role_id = $params_arr['role_id'];
                $group_id = $params_arr['group_id'];
                $page_no = 1;
                $per_page = PERPAGE;
                
                if(isset($params_arr['page_no']) && $params_arr['page_no'] != '')
                    $page_no = $params_arr['page_no'];
                if(isset($params_arr['per_page']) && $params_arr['per_page'] != '')
                    $per_page = $params_arr['per_page'];
                
                $privilege_flag = sapp_Global::_check_menu_access(EMPLOYEE,$group_id,$role_id);
                if($privilege_flag == 'Yes')
                {
                    $emp_model = new Services_Model_Employee();
                    $total_data = $emp_model->getemplist($page_no,$per_page);
                    $emp_data = $total_data['rows'];
                    if(!empty($emp_data) && count($emp_data) > 0)
                    {
                        $page_cnt = $total_data['page_cnt'];
                        foreach($emp_data as $edata)
                        {
                            $edata['date_of_joining'] = sapp_Global::change_date($edata['date_of_joining'], "view");
                            $edata['profileimg'] = sapp_Global::_getHostBaseURL()."public/uploads/profile/".$edata['profileimg'];
                            $result[] = $edata;
                        }
                        $message = "success";
                        $status = 1;
                    }
                    $add_flag = sapp_Global::_checkprivileges(EMPLOYEE,$group_id,$role_id,"add");
                }
            }
            return array('status' => $status,'message' => $message,'page_cnt' => $page_cnt,'result' => $result,'add_flag' => $add_flag);
        }
        /**
         * This function acts as a service to get business units list.
         * @param array $params_arr  = array of parameters
         * @return array  Array of business units.
         */
        public function bunitslist($params_arr)
        {
            $result = array();$status = 0;$message = "No data found.";$page_cnt = 1;
            if(isset($params_arr['role_id']) && $params_arr['role_id'] != '' && isset($params_arr['group_id']) && $params_arr['group_id'] != '')
            {
                $role_id = $params_arr['role_id'];
                $group_id = $params_arr['group_id'];
                $page_no = 1;
                $per_page = PERPAGE;
                
                if(isset($params_arr['page_no']) && $params_arr['page_no'] != '')
                    $page_no = $params_arr['page_no'];
                if(isset($params_arr['per_page']) && $params_arr['per_page'] != '')
                    $per_page = $params_arr['per_page'];
                
                $privilege_flag = sapp_Global::_check_menu_access(BUSINESSUNITS,$group_id,$role_id);
                if($privilege_flag == 'Yes')
                {
                    $bunit_model = new Default_Model_Businessunits();
                    $total_data = $bunit_model->getunitlist_service($page_no,$per_page);
                    
                    $bunit_data = $total_data['rows'];
                    if(!empty($bunit_data) && count($bunit_data) > 0)
                    {
                        $message = "success";
                        $status = 1;
                        $page_cnt = $total_data['page_cnt'];
                        foreach($bunit_data as $bdata)
                        {                            
                            $add_arr = array();
                            if($bdata['address1'] != '')
                                $add_arr[] = $bdata['address1'];
                            if($bdata['city'] != '')
                                $add_arr[] = $bdata['city'];                            
                            if($bdata['state'] != '')
                                $add_arr[] = $bdata['state'];
                            if($bdata['country'] != '')
                                $add_arr[] = $bdata['country'];
                            
                            $address = '';
                            if(count($add_arr) > 0)
                            {
                                $address = implode(",", $add_arr);
                            }
                            $result[] = array('id' => $bdata['id'],'unitname' => $bdata['unitname'],
                                            'unitcode' => $bdata['unitcode'],'address' => $address);
                        }
                        //echo "<pre>";print_r($result);echo "</pre>";
                    }
                }
            }
            return array('status' => $status,'message' => $message,'page_cnt' => $page_cnt,'result' => $result);
        }
        /**
         * This function acts as a service to get menu of a particular employee.
         * @param array $params_arr  = array of parameters
         * @return array  Array of menu items.
         */
        public function getmenu($params_arr)
        {
            $result = array();$status = 0;$message = "No data found.";
                                    
            if(isset($params_arr['role_id']) && $params_arr['role_id'] != '' && isset($params_arr['group_id']) && $params_arr['group_id'] != '')
            {
                $role_id = $params_arr['role_id'];
                $group_id = $params_arr['group_id'];
                $menu_model = new Default_Model_Menu();
                $group_menu = $menu_model->getgroupmenu_service($group_id, $role_id);
                
                if(count($group_menu) >0)
                {
                    $result = $group_menu;
                    $message = "success";
                    $status = 1;
                }
                //echo "<pre>";print_r($new_gmenu);echo "</pre>";
            }
            return array('status' => $status,'message' => $message,'result' => $result);
        }
        /**
         * This function act as a service to get employee details.
         * @param Array $params_arr = Array of parameters.
         * @return array Array of details of user.
         */
	public function getempdetails($params_arr)
        {
            $status = 0;$message = "No data found.";$result = array();
            if(isset($params_arr['userid']) && $params_arr['userid'] != '')
            {
                $userid = trim($params_arr['userid']);
                $emp_model = new Services_Model_Employee();
                $emp_data = $emp_model->getempdetails($userid);
                
                if(count($emp_data) > 0)
                {
                    $result = $emp_data;
                    $result['dob'] = sapp_Global::change_date($result['dob'], "view");
                    $result['date_of_joining'] = sapp_Global::change_date($result['date_of_joining'], "view");
                    $result['profileimg'] = sapp_Global::_getHostBaseURL()."public/uploads/profile/".$result['profileimg'];
                    $status = 1;
                    $message = "success";
                }
            }
            return array('status' => $status,'message' => $message,'result' => $result);
        }
	/*
		* This function returns the employee details based on employee Id and password
		* Input Parameters : EmployeeID,Password
    */	
	public function login($paramsarray)
	{
	   if(!empty($paramsarray))
	   {
		   $servicetocall = $paramsarray['service'];
		   $loginModel = new Services_Model_Login();
		   $result = $loginModel->$servicetocall($paramsarray);
		   
		    return $result;
	   } 	   
	  
	}
	
	public function myholidaycalender($paramsarray)
	{
	   if(!empty($paramsarray))
	   {
		   $servicetocall = $paramsarray['service'];
		   $role_id = $paramsarray['role_id'];
           $group_id = $paramsarray['group_id'];
		   $privilege_flag = sapp_Global::_check_menu_access(MYHOLIDAYCALENDAR,$group_id,$role_id);
		    if($privilege_flag == 'Yes')
			{
			   $holidayModel = new Services_Model_Holiday();
			   $result = $holidayModel->$servicetocall($paramsarray);
			}
			 else
			{
			     $result = array('status'=>'0','message'=>'No data found.','result' => '');
			}	
		   
		    return $result;
	   } 	   
	  
	}
	
	public function changepassword($paramsarray)
	{
	   if(!empty($paramsarray))
	   {
		   $servicetocall = $paramsarray['service'];
		   $loginModel = new Services_Model_Login();
		   $result = $loginModel->$servicetocall($paramsarray);
		   
		    return $result;
	   } 	   
	  
	}
		
	public function organisationinfo($paramsarray)
	{
	   if(!empty($paramsarray))
	   {
		   $servicetocall = $paramsarray['service'];
		   $role_id = $paramsarray['role_id'];
           $group_id = $paramsarray['group_id'];
		   $privilege_flag = sapp_Global::_check_menu_access(ORGANISATIONINFO,$group_id,$role_id);
		   if($privilege_flag == 'Yes')
			{
			   $orgInfoModel = new Default_Model_Organisationinfo();
			   $result = $orgInfoModel->getSelecteOrgRecords();
			   if(!empty($result))
				 $data = array('status'=>'1','message'=>'Success.','result' => $result);
				else
				 $data = array('status'=>'0','message'=>'Organization is not added yet.','result' => '');	
            } else
			{
			     $data = array('status'=>'0','message'=>'No data found.','result' => '');
			}	
		   //echo"<pre>";print_r($getorgData[0]);exit;
		    return $data;
	   } 	   
	  
	}

	/**
	 * This function acts as a service to get employees or candidates list (based on filter) who are sent for background check for logged in agency.
	 * @param array $params_arr  = array of parameters
	 * userid, groupid, roleid, specimentype, page_no, per_page are the input parameters
	 * @return array of employees/candidates.
	 */
	public function getempscreeninglist($params_arr)
	{
		$result = array();$status = 0;$message = "No data found.";
		$add_flag = 'No';$page_cnt = 1;
		if(isset($params_arr['role_id']) && $params_arr['role_id'] != '' && isset($params_arr['group_id']) && $params_arr['group_id'] == $this->usergroup && isset($params_arr['userid']))
		{
			$role_id = $params_arr['role_id'];			
			$group_id = $params_arr['group_id'];
			$loginid = $params_arr['userid']; // display the employees who are assigned to particular logged in agency
			$page_no = 1;
			$per_page = PERPAGE;
			$filter = '1';
			
			/* Filter to display either candidates or employees */
			if(isset($params_arr['specimentype']))
				$filter = $params_arr['specimentype'];
				
			if(isset($params_arr['page_no']) && $params_arr['page_no'] != '')
				$page_no = $params_arr['page_no'];
			if(isset($params_arr['per_page']) && $params_arr['per_page'] != '')
				$per_page = $params_arr['per_page'];
			
			$privilege_flag = sapp_Global::_check_menu_access(EMPSCREENING,$group_id,$role_id);
			if($privilege_flag == 'Yes')
			{
				$empscreening_model = new Services_Model_Empscreening();
				$total_data = $empscreening_model->getEmpScreeningData($page_no,$per_page,$filter,$loginid);				
				$emp_data = $total_data['rows'];
				if(!empty($emp_data) && count($emp_data) > 0)
				{
					$page_cnt = $total_data['page_cnt'];
					foreach($emp_data as $edata)
					{						
						$result[] = $edata;
					}
					$message = "success";
					$status = 1;
				}
				$edit_flag = sapp_Global::_checkprivileges(EMPSCREENING,$group_id,$role_id,"edit");
			}
		}
		return array('status' => $status,'message' => $message,'page_cnt' => $page_cnt,'result' => $result,'edit_flag' => $edit_flag);
	}

	public function myteam($params_arr)
        {
            $result = array();$status = 0;$message = "No data found.";
            $add_flag = 'No';$page_cnt = 1;
            if(isset($params_arr['role_id']) && $params_arr['role_id'] != '' && isset($params_arr['group_id']) && $params_arr['group_id'] != '')
            {
                $role_id = $params_arr['role_id'];
                $group_id = $params_arr['group_id'];
                $page_no = 1;
                $per_page = PERPAGE;
                
                if(isset($params_arr['page_no']) && $params_arr['page_no'] != '')
                    $page_no = $params_arr['page_no'];
                if(isset($params_arr['per_page']) && $params_arr['per_page'] != '')
                    $per_page = $params_arr['per_page'];
                
                $privilege_flag = sapp_Global::_check_menu_access(MYEMPLOYEES,$group_id,$role_id);
                if($privilege_flag == 'Yes')
                {
                    $emp_model = new Services_Model_Employee();
                    $total_data = $emp_model->getmyemplist($page_no,$per_page,$params_arr['userid']);
                    $emp_data = $total_data['rows'];
                    if(!empty($emp_data) && count($emp_data) > 0)
                    {
                        $page_cnt = $total_data['page_cnt'];
                        foreach($emp_data as $edata)
                        {
                            $edata['date_of_joining'] = sapp_Global::change_date($edata['date_of_joining'], "view");
                            $edata['profileimg'] = sapp_Global::_getHostBaseURL()."public/uploads/profile/".$edata['profileimg'];
                            $result[] = $edata;
                        }
                        $message = "success";
                        $status = 1;
                    }
                    //$add_flag = sapp_Global::_checkprivileges(EMPLOYEE,$group_id,$role_id,"add");
                }
            }
			//echo "<pre>";print_r($result);exit;
            return array('status' => $status,'message' => $message,'page_cnt' => $page_cnt,'result' => $result);
        }
		
	public function viewleavedetails($params_arr)
        {
            $result = array();
			$status = 0;
			$message = "No data found.";
            $add_flag = 'No';
			$edit_flag = 'No';
			$page_cnt = 1;
			$status_flag = 2;
            if(isset($params_arr['role_id']) && $params_arr['role_id'] != '' && isset($params_arr['group_id']) && $params_arr['group_id'] != '')
            {
                $role_id = $params_arr['role_id'];
                $group_id = $params_arr['group_id'];
                $page_no = 1;
                $per_page = PERPAGE;
                
                if(isset($params_arr['page_no']) && $params_arr['page_no'] != '')
                    $page_no = $params_arr['page_no'];
                if(isset($params_arr['per_page']) && $params_arr['per_page'] != '')
                    $per_page = $params_arr['per_page'];
				
				if(isset($params_arr['leaveflag']))
				{
					if($params_arr['leaveflag'] == 'pending')	
					   $status_flag = 1;
					else if($params_arr['leaveflag'] == 'rejected') 
					   $status_flag = 3;
					else if($params_arr['leaveflag'] == 'cancel') 
					   $status_flag = 4;	
					else if($params_arr['leaveflag'] == 'approved') 
					   $status_flag = 2;   
				}
                
                $privilege_flag = sapp_Global::_check_menu_access(LEAVES,$group_id,$role_id);
                if($privilege_flag == 'Yes')
                {
					if(isset($params_arr['userid']) && $params_arr['userid'] !='')
					{
						$leavesmodel = new Services_Model_Leaves();
						$total_data = $leavesmodel->getLeavedata($page_no,$per_page,$params_arr['userid'],$status_flag);
						if(!empty($total_data['rows']) && count($total_data['rows']) > 0)
						{
						    $result = $total_data['rows'];
							$role = $total_data['role'];
							$page_cnt = $total_data['page_cnt'];
							$message = "success";
							$status = 1;
						}
					}else
					{
					    $message = "User Id cannot be empty.";
						$status = 0;
					}
					if($status_flag == 1)
					{
						$add_flag = sapp_Global::_checkprivileges(LEAVEREQUEST,$group_id,$role_id,"add");
						$edit_flag = sapp_Global::_checkprivileges(PENDINGLEAVES,$group_id,$role_id,"delete");
					}	
					else
					{
						$add_flag = sapp_Global::_checkprivileges(LEAVEREQUEST,$group_id,$role_id,"add");
						//$edit_flag = sapp_Global::_checkprivileges(PENDINGLEAVES,$group_id,$role_id,"delete");
					}	
                }
            }
			//echo "<pre>";print_r($result);exit;
            return array('status' => $status,'message' => $message,'page_cnt' => $page_cnt,'result' => $result,'role' =>$role,'add_flag' => $add_flag,'edit_flag' => $edit_flag);
        }

		public function leaverequest($params_arr)
        {
            $result = array();
			$status = 0;
			$message = "No data found.";
			$messagearray = array();
			
            if(isset($params_arr['role_id']) && $params_arr['role_id'] != '' && isset($params_arr['group_id']) && $params_arr['group_id'] != '')
            {
                $role_id = $params_arr['role_id'];
                $group_id = $params_arr['group_id'];
              
                
                $privilege_flag = sapp_Global::_check_menu_access(LEAVEREQUEST,$group_id,$role_id);
                if($privilege_flag == 'Yes')
                {
					if(isset($params_arr['userid']) && $params_arr['userid'] !='')
					{
						$leavesmodel = new Services_Model_Leaves();
						$employee_data = $leavesmodel->getempdetails($params_arr['userid']);
						//echo "<pre>";print_r($employee_data);exit;
						if(!empty($employee_data) && count($employee_data) > 0)
						{
						    if(empty($employee_data['repmanagerdetails']) || empty($employee_data['availableleaves']) || empty($employee_data['leavetypes']))
							{
								if(empty($employee_data['repmanagerdetails']))
									$messagearray['repmanager'] = 'Reporting manager is not assigned yet. Please contact your HR.';
								if(empty($employee_data['availableleaves']))
									$messagearray['availableleaves'] = 'You have not been allotted leaves for this financial year. Please contact your HR.';
								if(empty($employee_data['leavetypes']))
									$messagearray['leavetypes'] = 'Leave types are not configured yet.';
							}else
							{
							    $messagearray['success']='success';
							}	
						    $result = $employee_data;
							$message = $messagearray;
							$status = 1;
						}
					}else
					{
					    $message = "User Id cannot be empty.";
						$status = 0;
					}
					
                }
            }
			//echo "<pre>";print_r($result);exit;
            return array('status' => $status,'message' => $message,'result' => $result);
        }

		public function saveleaverequest($params_arr)
        {
            $result = array();
			$status = 0;
			$message = "No data found.";
			$messagearray = array();
			$leavetypeid = '';
			$leaverequestdata = array();
			
            if(isset($params_arr['role_id']) && $params_arr['role_id'] != '' && isset($params_arr['group_id']) && $params_arr['group_id'] != '')
            {
                $role_id = $params_arr['role_id'];
                $group_id = $params_arr['group_id'];
              
                
                $privilege_flag = sapp_Global::_check_menu_access(LEAVEREQUEST,$group_id,$role_id);
                if($privilege_flag == 'Yes')
                {
				    $leavesmodel = new Services_Model_Leaves();
					//$employee_data = $leavesmodel->saveleaverequest($params_arr['userid']);
					if(isset($params_arr['userid']) && $params_arr['userid'] !='')
					{
					    $leaverequestdata = $leavesmodel->saveleaverequest($params_arr['userid'],$params_arr['reason'],$params_arr['leave_type_id'],$params_arr['leave_day'],$params_arr['from_date'],$params_arr['to_date'],$params_arr['leavecount']);
					    $status = $leaverequestdata['status'];
						$message = $leaverequestdata['message'];
						$result = $leaverequestdata['result'];
						//echo "<pre>";print_r($leaverequestdata);exit;
						
					}else
					{
					    $message = "User Id cannot be empty.";
						$status = 0;
					}
					
                }
            }
			//echo "<pre>";print_r($result);exit;
            return array('status' => $status,'message' => $message,'result' => $result);
        }

		public function manageemployeeleaves($params_arr)
        {
            $result = array();
			$status = 0;
			$message = "No data found.";
            $add_flag = 'No';
			$edit_flag = 'No';
			$page_cnt = 1;
			$status_flag = 1;
            if(isset($params_arr['role_id']) && $params_arr['role_id'] != '' && isset($params_arr['group_id']) && $params_arr['group_id'] != '')
            {
                $role_id = $params_arr['role_id'];
                $group_id = $params_arr['group_id'];
                $page_no = 1;
                $per_page = PERPAGE;
                
                if(isset($params_arr['page_no']) && $params_arr['page_no'] != '')
                    $page_no = $params_arr['page_no'];
                if(isset($params_arr['per_page']) && $params_arr['per_page'] != '')
                    $per_page = $params_arr['per_page'];
				
				if(isset($params_arr['leaveflag']))
				{
					if($params_arr['leaveflag'] == 'pending')	
					   $status_flag = 1;
					else if($params_arr['leaveflag'] == 'rejected') 
					   $status_flag = 3;
					else if($params_arr['leaveflag'] == 'cancel') 
					   $status_flag = 4;	
					else if($params_arr['leaveflag'] == 'approved') 
					   $status_flag = 2;   
				}
                
                $privilege_flag = sapp_Global::_check_menu_access(MANAGEREMPLOYEEVACATIONS,$group_id,$role_id);
                if($privilege_flag == 'Yes')
                {
					if(isset($params_arr['userid']) && $params_arr['userid'] !='')
					{
						$leavesmodel = new Services_Model_Leaves();
						$total_data = $leavesmodel->getEmployeeLeavedata($page_no,$per_page,$params_arr['userid'],$status_flag);
						if(!empty($total_data['rows']) && count($total_data['rows']) > 0)
						{
						    $result = $total_data['rows'];
							$page_cnt = $total_data['page_cnt'];
							$message = "success";
							$status = 1;
						}
					}else
					{
					    $message = "User Id cannot be empty.";
						$status = 0;
					}
					if($status_flag == 1)
					{
						$edit_flag = sapp_Global::_checkprivileges(MANAGEREMPLOYEEVACATIONS,$group_id,$role_id,"edit");
					}	
						
                }
            }
			
			//echo "<pre>";print_r($result);exit;
            return array('status' => $status,'message' => $message,'page_cnt' => $page_cnt,'result' => $result,'edit_flag' => $edit_flag);
        }	
		
		
		public function manageindividualempleave($params_arr)
        {
            $result = array();
			$status = 0;
			$message = "No data found.";
			$edit_flag = 'No';
            if(isset($params_arr['role_id']) && $params_arr['role_id'] != '' && isset($params_arr['group_id']) && $params_arr['group_id'] != '')
            {
                $role_id = $params_arr['role_id'];
                $group_id = $params_arr['group_id'];
               
                
               
                
                $privilege_flag = sapp_Global::_check_menu_access(MANAGEREMPLOYEEVACATIONS,$group_id,$role_id);
				$edit_flag = sapp_Global::_checkprivileges(MANAGEREMPLOYEEVACATIONS,$group_id,$role_id,"edit");
                if($privilege_flag == 'Yes' && $edit_flag == 'Yes')
                {
					if(isset($params_arr['userid']) && $params_arr['userid'] !='' && isset($params_arr['recordid']) && $params_arr['recordid'] !='' && isset($params_arr['employeeid']) && $params_arr['employeeid'] !='')
					{
						$leavesmodel = new Services_Model_Leaves();
						$total_data = $leavesmodel->getIndividulEmpLeavedata($params_arr['userid'],$params_arr['recordid'],$params_arr['employeeid']);
						if(!empty($total_data['rows']) && count($total_data['rows']) > 0)
						{
						    $result = $total_data['rows'];
						 	$message = "success";
							$status = 1;
						}
					}else
					{
					    if($params_arr['userid'] == '')
							$message = "User Id cannot be empty.";
						else if($params_arr['recordid'] == '')
						    $message = "Record Id cannot be empty.";
						else if($params_arr['employeeid'] == '')
						    $message = "Employee Id cannot be empty.";	
					}
						
						
                }else
				{
				    $message = "you are not authorized to access this page";
				}
            }
			
			//echo "<pre>";print_r($result);exit;
            return array('status' => $status,'message' => $message,'result' => $result,'edit_flag' => $edit_flag);
        }
		
		public function viewindividualleave($params_arr)
        {
            $result = array();
			$status = 0;
			$message = "No data found.";
			$cancel_flag = 'No';
			$status_flag = 2;
            if(isset($params_arr['role_id']) && $params_arr['role_id'] != '' && isset($params_arr['group_id']) && $params_arr['group_id'] != '')
            {
                $role_id = $params_arr['role_id'];
                $group_id = $params_arr['group_id'];
               
                $privilege_flag = sapp_Global::_check_menu_access(LEAVES,$group_id,$role_id);
				
                if($privilege_flag == 'Yes')
                {
					if(isset($params_arr['userid']) && $params_arr['userid'] !='' && isset($params_arr['recordid']) && $params_arr['recordid'] !='' && isset($params_arr['leaveflag']) && $params_arr['leaveflag'] !='')
					{
						if(isset($params_arr['leaveflag']))
						{
							if($params_arr['leaveflag'] == 'pending')	
							   $status_flag = 1;
							else if($params_arr['leaveflag'] == 'rejected') 
							   $status_flag = 3;
							else if($params_arr['leaveflag'] == 'cancel') 
							   $status_flag = 4;	
							else if($params_arr['leaveflag'] == 'approved') 
							   $status_flag = 2;   
						}
						$leavesmodel = new Services_Model_Leaves();
						$total_data = $leavesmodel->getIndividulLeavedata($params_arr['userid'],$params_arr['recordid']);
						//echo "<pre>";print_r($total_data);exit;
						if(!empty($total_data['rows']) && count($total_data['rows']) > 0)
						{
						    $result = $total_data['rows'];
						 	$message = "success";
							$status = 1;
							if($status_flag == 1)
							{
								$cancel_flag = sapp_Global::_checkprivileges(PENDINGLEAVES,$group_id,$role_id,"delete");
							}
						}
							
					}else
					{
					    if($params_arr['userid'] == '')
							$message = "User Id cannot be empty.";
						else if($params_arr['recordid'] == '')
						    $message = "Record Id cannot be empty.";
						else if($params_arr['leaveflag'] == '')
						    $message = "Leave flag cannot be empty.";	
					}
						
						
                }else
				{
				    $message = "you are not authorized to access this page";
				}
            }
			
			//echo "<pre>";print_r($result);exit;
            return array('status' => $status,'message' => $message,'result' => $result,'cancel_flag' => $cancel_flag);
        }
		
		public function approveempleave($params_arr)
        {
            $result = array();
			$status = 0;
			$message = "No data found.";
			$actionstatus = ''; 
			$total_data = array();
            if(isset($params_arr['role_id']) && $params_arr['role_id'] != '' && isset($params_arr['group_id']) && $params_arr['group_id'] != '')
            {
                $role_id = $params_arr['role_id'];
                $group_id = $params_arr['group_id'];
               
                $privilege_flag = sapp_Global::_check_menu_access(MANAGEREMPLOYEEVACATIONS,$group_id,$role_id);
				$edit_flag = sapp_Global::_checkprivileges(MANAGEREMPLOYEEVACATIONS,$group_id,$role_id,"edit");
                if($privilege_flag == 'Yes' && $edit_flag == 'Yes')
                {
					if(isset($params_arr['userid']) && $params_arr['userid'] !='' && isset($params_arr['recordid']) && $params_arr['recordid'] !='' && isset($params_arr['employeeid']) && $params_arr['employeeid'] !='' && isset($params_arr['actionflag']) && $params_arr['actionflag'] !='')
					{
					    if($params_arr['actionflag'] == 'approve')
						   $actionstatus = 2; 
						if($params_arr['actionflag'] == 'reject')
						   $actionstatus = 3;    
						$leavesmodel = new Services_Model_Leaves();
						if($actionstatus == 2 || $actionstatus == 3)
						   $total_data = $leavesmodel->approveEmpLeavedata($params_arr['userid'],$params_arr['recordid'],$params_arr['employeeid'],$actionstatus);
						
						    $result = $total_data['result'];
						 	$message = $total_data['message'];
							$status = $total_data['status'];
					
					}else
					{
					    if($params_arr['userid'] == '')
							$message = "User Id cannot be empty.";
						else if($params_arr['recordid'] == '')
						    $message = "Record Id cannot be empty.";
						else if($params_arr['employeeid'] == '')
						    $message = "Employee Id cannot be empty.";	
						else if(!isset($params_arr['actionflag']) || $params_arr['actionflag'] == '')
						    $message = "Action flag cannot be empty.";	
					}
						
						
                }else
				{
				    $message = "you are not authorized to access this page";
				}
            }
			
			//echo "<pre>";print_r($result);exit;
            return array('status' => $status,'message' => $message,'result' => $result);
        }
		
		public function cancelleave($params_arr)
        {
            $result = array();
			$status = 0;
			$message = "No data found.";
			$total_data = array();
            if(isset($params_arr['role_id']) && $params_arr['role_id'] != '' && isset($params_arr['group_id']) && $params_arr['group_id'] != '')
            {
                $role_id = $params_arr['role_id'];
                $group_id = $params_arr['group_id'];
               
                $privilege_flag = sapp_Global::_check_menu_access(PENDINGLEAVES,$group_id,$role_id);
				$delete_flag = sapp_Global::_checkprivileges(PENDINGLEAVES,$group_id,$role_id,"delete");
                if($privilege_flag == 'Yes' && $delete_flag == 'Yes')
                {
					if(isset($params_arr['userid']) && $params_arr['userid'] !='' && isset($params_arr['recordid']) && $params_arr['recordid'] !='')
					{   
						$leavesmodel = new Services_Model_Leaves();
						$total_data = $leavesmodel->cancelleave($params_arr['userid'],$params_arr['recordid']);
						
						    $result = $total_data['result'];
						 	$message = $total_data['message'];
							$status = $total_data['status'];
					
					}else
					{
					    if($params_arr['userid'] == '')
							$message = "User Id cannot be empty.";
						else if($params_arr['recordid'] == '')
						    $message = "Record Id cannot be empty.";
							
					}
						
						
                }else
				{
				    $message = "you are not authorized to access this page";
				}
            }
			
			//echo "<pre>";print_r($result);exit;
            return array('status' => $status,'message' => $message,'result' => $result);
        }
		
		
		public function calculatedays($params_arr)
        {
            $result = array();
			$status = 0;
			$message = "No data found.";
			$messagearray = array();
			$leavetypeid = '';
			$daysdata = array();
			$selectorid = 1;
			
            if(isset($params_arr['role_id']) && $params_arr['role_id'] != '' && isset($params_arr['group_id']) && $params_arr['group_id'] != '')
            {
			    $leavedayArr = array(1,2);
                $role_id = $params_arr['role_id'];
                $group_id = $params_arr['group_id'];
				$userid = $params_arr['userid'];
                $leavetypeid= $params_arr['leavetypeid'];
				$leaveday = $params_arr['leaveday'];
				$from_date = $params_arr['from_date'];
                $to_date = $params_arr['to_date'];
				$selectorid = isset($params_arr['selectorid'])?$params_arr['selectorid']:1;
                $privilege_flag = sapp_Global::_check_menu_access(LEAVEREQUEST,$group_id,$role_id);
                if($privilege_flag == 'Yes')
                {
				    $leavesmodel = new Services_Model_Leaves();
					//$employee_data = $leavesmodel->saveleaverequest($params_arr['userid']);
					if(isset($userid) && $userid !='' && isset($leavetypeid) && $leavetypeid !='' && isset($leaveday) && $leaveday !='' && isset($from_date) && $from_date !='' && isset($to_date) && $to_date !='')
					{
					    if(in_array($leaveday, $leavedayArr))
						{
						    $leavetypedata =  $leavesmodel->getleavetypedata($leavetypeid);
							if(!empty($leavetypedata))
							{
							    $leavetypetext = $leavetypedata['leavetype'];
								$leavetypelimit = $leavetypedata['numberofdays'];
							    $daysdata = $leavesmodel->calculatedays($userid,$leavetypetext,$leavetypelimit,$leaveday,$from_date,$to_date,$selectorid);
								
								$result = $daysdata['noOfDays'];
								$message = $daysdata['message'];;
								$status = $daysdata['status'];
							}else
							{
							    $message = "Wrong inputs given for leave type.";
								$status = 0;
							}
							//echo "<pre>";print_r($daysdata);exit;
							
						}else
						{
							 $message = "Wrong inputs given for leave day.";
						     $status = 0;
						}	
					    						
					}else
					{
					    $message = "Some parameters missing.";
						$status = 0;
					}
					
                }
            }
			//echo "<pre>";print_r($result);exit;
            return array('status' => $status,'message' => $message,'result' => $result);
        }
		
	/**
	* This function acts as a service to get background check details of each employee / candidate
	* @param array $params_arr  = array of parameters
	* userid, groupid, roleid, specimentype, specimenid, page_no, per_page are the input parameters
	* @return array of employee/candidate background check details.
	*/		
	public function individualempscreeningdata($params_arr)
	{
		$result = array();$specimenDataArr = Array();
		$status = 0;
		$message = "No data found.";
		$edit_flag = 'No';
		$page_no = 1;
		$per_page = PERPAGE;
		
		if(isset($params_arr['role_id']) && $params_arr['role_id'] != '' && isset($params_arr['group_id']) && $params_arr['group_id'] != '' && $params_arr['group_id'] == $this->usergroup)
		{
			$role_id = $params_arr['role_id'];
			$group_id = $params_arr['group_id'];
			$loginid = $params_arr['userid'];
			$specimentype = $params_arr['specimentype'];
			$specimenid = $params_arr['specimenid'];
			if(isset($params_arr['page_no']) && $params_arr['page_no'] != '')
				$page_no = $params_arr['page_no'];
			if(isset($params_arr['per_page']) && $params_arr['per_page'] != '')
				$per_page = $params_arr['per_page'];
			
			$privilege_flag = sapp_Global::_check_menu_access(EMPSCREENING,$group_id,$role_id);
			$edit_flag = sapp_Global::_checkprivileges(EMPSCREENING,$group_id,$role_id,"edit");
			if($privilege_flag == 'Yes' && $edit_flag == 'Yes')
			{
				if(isset($params_arr['userid']) && $params_arr['userid'] !='' && isset($specimentype) && $specimentype !='' && isset($specimenid) && $specimenid !='')
				{
					$empscreening_model = new Services_Model_Empscreening();
					
					$personalData = $empscreening_model->getEmpPersonalData($specimenid,$specimentype);
					$addressData = $empscreening_model->getEmpAddressData($specimenid,$specimentype);
					$companyData = $empscreening_model->getEmpCompanyData($specimenid,$specimentype);
					$processesData = $empscreening_model->getProcessesData($specimenid,$specimentype,$page_no,$per_page,$loginid);			
					
					if(!empty($personalData) && count($personalData) > 0 && !empty($processesData) && count($processesData) > 0)
					{
						$specimenDataArr['personalData'] = $personalData;
						$specimenDataArr['addressData'] = $addressData;
						if(!empty($companyData) && count($companyData) > 0)
						$specimenDataArr['companyData'] = $companyData;
						else $specimenDataArr['companyData'] = array();
						$specimenDataArr['processesData'] = $processesData;
						$message = "Success.";
					}
				}else
				{
					if($params_arr['userid'] == '')
						$message = "User Id cannot be empty.";						
					 if($params_arr['specimenid'] == '')
						$message = "Specimen Id cannot be empty.";		
				}				
			}else
			{
				$message = "you are not authorized to access this page";
			}
		}
		$result = $specimenDataArr;
		//echo "<pre>";print_r($result);exit;
		return array('status' => $status,'message' => $message,'result' => $result,'edit_flag' => $edit_flag);
	}
	
	/**
	* This function acts as a service to get comments done by agency for particular screening detail(isactive is active) of each employee / candidate 
	* @param array $params_arr  = array of parameters
	* userid, groupid, roleid, specimentype, specimenid, recordid, page_no, per_page are the input parameters
	* @return array of comments.
	*/
	public function displayeachscreeningdetail($params_arr)
	{
		$result = array();$commentsData = Array();
		$status = 0;
		$message = "No data found.";
		$edit_flag = 'No';
		$page_no = 1;
		$per_page = PERPAGE;
		if(isset($params_arr['role_id']) && $params_arr['role_id'] != '' && isset($params_arr['group_id']) && $params_arr['group_id'] != '' && $params_arr['group_id'] == $this->usergroup && isset($params_arr['detailid']))
		{
			$role_id = $params_arr['role_id'];
			$group_id = $params_arr['group_id'];
			$loginid = $params_arr['userid'];			
			$detailid = $params_arr['detailid'];
			if(isset($params_arr['page_no']) && $params_arr['page_no'] != '')
				$page_no = $params_arr['page_no'];
			if(isset($params_arr['per_page']) && $params_arr['per_page'] != '')
				$per_page = $params_arr['per_page'];
			
			$empscreening_model = new Services_Model_Empscreening();			
			$privilege_flag = sapp_Global::_check_menu_access(EMPSCREENING,$group_id,$role_id);
			$edit_flag = sapp_Global::_checkprivileges(EMPSCREENING,$group_id,$role_id,"edit");
			$checkarr = $empscreening_model->checkagencyfordetail($detailid,$loginid);
			
			if($privilege_flag == 'Yes' && $edit_flag == 'Yes' && $checkarr != 'rows')
			{				
				$commentsData = $empscreening_model->getcomments($detailid,$page_no,$per_page);
				if(!empty($commentsData))
				{
					$message = "Success.";
				}
			}
			else
			{
				$message = "you are not authorized to access this page";
			}
		}
		
		$result = $commentsData;
		return array('status' => $status,'message' => $message,'result' => $result,'edit_flag' => $edit_flag);
	}
	
	
	/**
	* This function acts as a service to save comments given by agency for particular screening detail(isactive is active) of each employee / candidate 
	* @param array $params_arr  = array of parameters
	* userid, groupid, roleid, detailid, comment are the input parameters
	* @return array of comments.
	*/
	public function saveagencycomment($params_arr)
	{
		$result = array();
		$status = 0;
		$message = "Message cannot be saved.";
		$edit_flag = 'No';		
		if(isset($params_arr['role_id']) && $params_arr['role_id'] != '' && isset($params_arr['group_id']) && $params_arr['group_id'] != '' && $params_arr['group_id'] == $this->usergroup && isset($params_arr['detailid']))
		{
			$role_id = $params_arr['role_id'];
			$group_id = $params_arr['group_id'];
			$loginid = $params_arr['userid'];			
			$detailid = $params_arr['detailid'];
			$comment = $params_arr['comment'];
			
			$empscreening_model = new Services_Model_Empscreening();			
			$privilege_flag = sapp_Global::_check_menu_access(EMPSCREENING,$group_id,$role_id);
			$edit_flag = sapp_Global::_checkprivileges(EMPSCREENING,$group_id,$role_id,"edit");
			$checkarr = $empscreening_model->checkagencyfordetail($detailid,$loginid);
			if($privilege_flag == 'Yes' && $edit_flag == 'Yes' && $checkarr != 'norows')
			{				
				$commentflag = $empscreening_model->savecomment($detailid,$loginid,$comment);
				if($commentflag)
				{
					$message = "Success.";
				}else{
					$message = "Message cannot be saved.";
				}
			}
			else
			{
				$message = "you are not authorized to access this page";
			}
		}		
		return array('status' => $status,'message' => $message,'result' => $result,'edit_flag' => $edit_flag);
	}
}

