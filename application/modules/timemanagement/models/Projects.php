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
 * @model Projects Model
 * @author sagarsoft
 *
 */
class Timemanagement_Model_Projects extends Zend_Db_Table_Abstract
{
	/**
	 * The default table name
	 */
	protected $_name = 'tm_projects';
	protected $_primary = 'id';

/* This is used in Advances for getting projects based on employee*/
	
		public function getProjectByEmpId($to_id){
		$sql="SELECT p.project_name FROM tm_project_employees pe
		INNER JOIN tm_projects p ON p.id = pe.project_id
					WHERE emp_id=$to_id"; 			
	
		$project_data  = $this->_db->fetchAll($sql,array("param1"=>$to_id,"param2"=>1));
		return $project_data;
	}
	
	
	
	
	
	
	/**
	 * T
	 *
	 * @param string $sort
	 * @param string $by
	 * @param number $pageNo
	 * @param number $perPage
	 * @param string $searchQuery
	 *
	 * @return array $projectsData
	 */
	 
	public function getProjectsData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = " p.is_active = 1 ";
		if(Zend_Registry::get( 'tm_role' ) == 'Manager'){
			$auth = Zend_Auth::getInstance();
			if($auth->hasIdentity()){
				$loginUserId = $auth->getStorage()->read()->id;
			}
			$where .= " AND pe.emp_id = '".$loginUserId."' AND pe.is_active = 1";
		}

		if($searchQuery)
		$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();

		$projectsData = $this->select()->distinct()
		->setIntegrityCheck(false)
		->from(array('p' => $this->_name),array('id'=>'p.id','project_name'=>'p.project_name','project_status'=>'if(p.project_status = "initiated", "Initiated",if(p.project_status = "draft" , "Draft",if (p.project_status = "in-progress","In Progress",if(p.project_status = "hold","Hold",if(p.project_status = "completed","Completed","")))))','start_date'=>'p.start_date','end_date'=>'p.end_date','parent_project'=>'p2.project_name','project_type'=>'IF(p.project_type="billable","Billable",IF(p.project_type="non_billable","Non billable","Revenue generation"))'))
		->joinLeft(array('p2' => $this->_name),'p.base_project = p2.id',array())
		->joinLeft(array('c'=>'tm_clients'),'p.client_id=c.id',array('client_name'=>'c.client_name'))
		->joinLeft(array('cur'=>'main_currency'),'p.currency_id = cur.id',array('currencyname'=>'cur.currencyname'));
		if(Zend_Registry::get( 'tm_role' ) == 'Manager'){
			$projectsData->joinLeft(array('pe'=>'tm_project_employees'),'pe.project_id = p.id',array());
		}
		$projectsData->where($where)
		->order("$by $sort")
		->limitPage($pageNo, $perPage);
		//echo $projectsData;exit;

		return $projectsData;
	}

	/**
	 * This will fetch all the project details based on the search paramerters passed with pagination.
	 *
	 * @param string $sort
	 * @param string $by
	 * @param number $perPage
	 * @param number $pageNo
	 * @param JSON $searchData
	 * @param string $call
	 * @param string $dashboardcall
	 *
	 * @return array
	 */
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall)
	{
		$searchQuery = '';
		$searchArray = array();
		$data = array();

		if($searchData != '' && $searchData!='undefined')
		{
			$searchValues = json_decode($searchData);
			foreach($searchValues as $key => $val)
			{
				if($key == 'client') $key = 'client_id';
				if($key == 'currency') $key = 'currency_id';
				if($key == 'parent_project'){
					$searchQuery .= " p.base_project = '".$val."' AND ";
				}else if($key == 'client_name'){
					$searchQuery .= " c.id = '".$val."' AND ";
				}else if($key == 'currencyname'){
					$searchQuery .= " cur.id = '".$val."' AND ";
				}else{
					$searchQuery .= " p.".$key." like '%".$val."%' AND ";
				}
				$searchArray[$key] = $val;
			}
			$searchQuery = rtrim($searchQuery," AND");
		}

		$objName = 'projects';

		$tableFields = array('action'=>'Action','project_name' => 'Project','project_status'=>'Status','parent_project'=>'Base Project','client_name' => 'Client','currencyname'=>'Currency','project_type'=>'Project Type');

		$tablecontent = $this->getProjectsData($sort, $by, $pageNo, $perPage,$searchQuery);

		$clientModel = new Timemanagement_Model_Clients();
		$clientData = $clientModel->getActiveClientsData();
		$clientArray = array(''=>'All');
		if(sizeof($clientData) > 0)
		{
			foreach ($clientData as $client){
				$clientArray[$client['id']] = $client['client_name'];
			}

		}

		$base_projectData = $this->getProjectList();
		$base_projectArray = array(''=>'All');
		if(sizeof($base_projectData) > 0)
		{
			foreach ($base_projectData as $base_project){
				$base_projectArray[$base_project['id']] = $base_project['project_name'];
			}
		}

		$currencyModel = new Default_Model_Currency();
		$currencyData = $currencyModel->getCurrencyList();
		$currencyArray = array(''=>'All');
		if(sizeof($currencyData) > 0)
		{
			foreach ($currencyData as $currency){
				$currencyArray[$currency['id']] = $currency['currency'];
			}
		}

		$dataTmp = array(
			'sort' => $sort,
			'by' => $by,
			'pageNo' => $pageNo,
			'perPage' => $perPage,				
			'tablecontent' => $tablecontent,
			'objectname' => $objName,
		    'menuName' => 'Projects',
			'extra' => array(),
			'tableheader' => $tableFields,
			'jsGridFnName' => 'getAjaxgridData',
			'jsFillFnName' => '',
			'searchArray' => $searchArray,
			'call'=>$call,
			'dashboardcall'=>$dashboardcall,
		    'search_filters' => array(
                    'client_name' => array(
                        'type' => 'select',
                        'filter_data' => $clientArray,
		),
                    'currencyname' => array(
                        'type' => 'select',
                        'filter_data' => $currencyArray,
		),
					'parent_project' => array(
			                        'type' => 'select',
			                        'filter_data' => $base_projectArray,
		),
                    'category' => array(
                        'type' => 'select',
                        'filter_data' => array(''=>'All','billable' => 'Billable','non_billable' => 'Non Billable','revenue' => 'Revenue generation'),
		),
					 'project_status' => array(
			                        'type' => 'select',
			                        'filter_data' => array(''=>'All','initiated' => 'Initiated','draft' => 'Draft','in-progress' => 'In Progress','hold'=>'Hold','completed'=>'Completed'),
		),
					'project_type' => array('type' => 'select',
						                      'filter_data' => array(''=>'All','billable' => 'Billable','non_billable' => 'Non Billable','revenue' => 'Revenue generation'),
		),
		//'start_date'=>array('type'=>'datepicker'),
		//	  'end_date'=>array('type'=>'datepicker')
		),
		);
		return $dataTmp;
	}

	public function getSingleProjectData($id){
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('p'=>$this->_name),array('p.*'))
		->joinLeft(array('c'=>'tm_clients'),'p.client_id=c.id',array('client_name'=>'c.client_name'))
		->joinLeft(array('cur'=>'main_currency'),'p.currency_id=cur.id',array('currencyname'=>'cur.currencyname','currencycode'=>'cur.currencycode'))
		->where('p.is_active = 1 AND p.id='.$id.' ');
		$res = $this->fetchAll($select)->toArray();
		if (isset($res) && !empty($res))
		{
			return $res;
		}
		else
		return 'norows';
	}

	public function SaveorUpdateProjectsData($data, $where)
	{
		if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId($this->_name);
			return $id;
		}
	}


	//check wether project is assigned to employee time sheet or not
	public function chkProjAssigned($project_id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select count(*) as count from tm_emp_timesheets where project_id = ".$project_id." ";
		$result = $db->query($query)->fetch();
		return $result['count'];
	}

	/**
	 * This method returns all active clients to show in projects screen
	 *
	 * @return array
	 */
	public function getProjectList()
	{
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('p'=>$this->_name),array('p.id','project_name'))
		//->where('p.is_active = 1 ')
		->order('p.project_name');
		return $this->fetchAll($select)->toArray();
	}
	/**
	 * This will fetch all the project details based on the search paramerters passed with pagination.
	 *
	 * @param string $sort
	 * @param string $by
	 * @param number $perPage
	 * @param number $pageNo
	 * @param JSON $searchData
	 * @param string $call
	 * @param string $dashboardcall
	 *
	 * @return array
	 */
	public function getEmpGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$employee_id,$tm_role = '')
	{
		$searchQuery = '';
		$searchArray = array();
		$data = array();

		if($searchData != '' && $searchData!='undefined')
		{
			$searchValues = json_decode($searchData);
			foreach($searchValues as $key => $val)
			{
				if($key == 'client') $key = 'client_id';
				if($key == 'currency') $key = 'currency_id';
				if($key == 'start_date'){ 
					$searchQuery .= " ".$key." like '%".  sapp_Global::change_date($val,'database')."%' AND ";
				}else if($key == 'end_date'){ 
					$searchQuery .= " ".$key." like '%".  sapp_Global::change_date($val,'database')."%' AND ";
				}else{
					$searchQuery .= " ".$key." like '%".$val."%' AND ";
				}
				$searchArray[$key] = $val;
			}
			$searchQuery = rtrim($searchQuery," AND");
		}

		$objName = 'employeeprojects';
		if($tm_role == 'Lead'){
		 $objName = 'leadprojects';
		}

		$tableFields = array('action'=>'Action','project_name' => 'Project','start_date' => 'Start Date','end_date' => 'End Date','client_name' => 'Client');

		$tablecontent = $this->getEmpProjectsData($sort, $by, $pageNo, $perPage,$searchQuery,$employee_id);

		$clientModel = new Timemanagement_Model_Clients();
		$clientData = $clientModel->getActiveClientsData();
		$clientArray = array(''=>'All');
		if(sizeof($clientData) > 0)
		{
			foreach ($clientData as $client){
				$clientArray[$client['client_name']] = $client['client_name'];
			}

		}

		$base_projectData = $this->getProjectList();
		$base_projectArray = array(''=>'All');
		if(sizeof($base_projectData) > 0)
		{
			foreach ($base_projectData as $base_project){
				$base_projectArray[$base_project['id']] = $base_project['project_name'];
			}
		}

		$currencyModel = new Default_Model_Currency();
		$currencyData = $currencyModel->getCurrencyList();
		$currencyArray = array(''=>'All');
		if(sizeof($currencyData) > 0)
		{
			foreach ($currencyData as $currency){
				$currencyArray[$currency['currency']] = $currency['currency'];
			}
		}

		$dataTmp = array(
			'sort' => $sort,
			'by' => $by,
			'pageNo' => $pageNo,
			'perPage' => $perPage,				
			'tablecontent' => $tablecontent,
			'objectname' => $objName,
		    'menuName' => 'Projects',
			'extra' => array(),
			'tableheader' => $tableFields,
			'jsGridFnName' => 'getAjaxgridData',
			'jsFillFnName' => '',
			'searchArray' => $searchArray,
			'call'=>$call,
			'dashboardcall'=>$dashboardcall,
		    'search_filters' => array(
                    'client_name' => array(
                        'type' => 'select',
                        'filter_data' => $clientArray,
		),
                    'currencyname' => array(
                        'type' => 'select',
                        'filter_data' => $currencyArray,
		),
					'base_project' => array(
			                        'type' => 'select',
			                        'filter_data' => $base_projectArray,
		),
                    'category' => array(
                        'type' => 'select',
                        'filter_data' => array(''=>'All','billable' => 'Billable','non_billable' => 'Non Billable','revenue' => 'Revenue generation'),
		),
					 'project_status' => array(
			                        'type' => 'select',
			                        'filter_data' => array(''=>'All','initiated' => 'Initiated','draft' => 'Draft','in-progress' => 'In Progress','hold'=>'Hold','completed'=>'Completed'),
		),
		 'start_date'=>array('type'=>'datepicker'),
						  'end_date'=>array('type'=>'datepicker')
		),
		);
		return $dataTmp;
	}
	public function getEmpProjectsData($sort, $by, $pageNo, $perPage,$searchQuery,$employee_id)
	{
		$where = " p.is_active = 1 ";
		if($searchQuery)
		$where .= " AND ".$searchQuery;
		if($employee_id>0)
		$where .= " AND p.project_status!='draft' and tpe.is_active = 1 AND tpe.emp_id = ".$employee_id;
		$db = Zend_Db_Table::getDefaultAdapter();
		$projectsData = $this->select()
		->setIntegrityCheck(false)
		->from(array('tpe' => 'tm_project_employees'),array('tpe.*'))
		->joinLeft(array('p' => $this->_name),'tpe.project_id=p.id',array('id'=>'p.id','project_name'=>'p.project_name','project_status'=>'p.project_status','start_date'=>'p.start_date','end_date'=>'p.end_date','base_project'=>'p.base_project','project_type'=>'IF(p.project_type="billable","Billable",IF(p.project_type="non_billable","Non billable","Revenue generation"))'))
		->joinLeft(array('c'=>'tm_clients'),'p.client_id=c.id',array('client_name'=>'c.client_name'))
		->joinLeft(array('cur'=>'main_currency'),'p.currency_id = cur.id',array('currencyname'=>'cur.currencyname'))
		->where($where)
		->order("$by $sort")
		->limitPage($pageNo, $perPage);
		// echo $projectsData; //exit;
		return $projectsData;
	}
	
	public function getEmployeeProjects($emp_id)
	{
		$where = " p.is_active = 1 ";
		
		$where .= " AND p.project_status!='draft' and tpe.is_active = 1 AND tpe.emp_id = ".$emp_id;
		$db = Zend_Db_Table::getDefaultAdapter();
		$projectsData = $this->select()
		->setIntegrityCheck(false)
		->from(array('tpe' => 'tm_project_employees'),array('project_emp_id'=>'tpe.id'))
		->joinLeft(array('p' => $this->_name),'tpe.project_id=p.id',array('id'=>'p.id','project_name'=>'p.project_name'))
		->where($where);
		
		 //echo $projectsData; exit;
		//return $projectsData;
		return $this->fetchAll($projectsData)->toArray();
	}

	/**
	 * This method is used to fetch the project details based on the user Role.
	 *
	 * Added by Manju for reports.
	 */
	public function getProjectsListByRole(){
		$storage = new Zend_Auth_Storage_Session();
		$sessionData = $storage->read();
		$result = array();
		$tm_role = Zend_Registry::get('tm_role');
		if($tm_role == "Admin") {
			$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('p'=>$this->_name),array('p.id','project_name'))
			->where('p.is_active = 1 ')
			->order('p.project_name asc');
			$result = $this->fetchAll($select)->toArray();
		}else{
			$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('p' => $this->_name),array('id'=>'p.id','project_name' => 'p.project_name',))
			->joinLeft(array('tpe'=>'tm_project_employees'), 'tpe.project_id=p.id AND tpe.is_active=1',array())
			->where('p.is_active=1 AND tpe.emp_id ='.$sessionData['id'])
			->order("p.project_name asc")
			->group('p.id');
			$result = $this->fetchAll($select)->toArray();
		}
		return $result;

	}
	//function to get last weekend week days
	public function getprevmonthweeks($selmn,$day)
	{
		$j=1;
		$master=array();
		$week=array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
		$date=date($selmn.'-01');
		$dt_arr=preg_split('/-/',$date);
		$lsday=cal_days_in_month(CAL_GREGORIAN, $dt_arr[1], $dt_arr[0]);
		$wday=date('l',mktime(0,0,0,$dt_arr[1],$dt_arr[2],$dt_arr[0]));
		$wval=  array_search($wday, $week);
		for($i=0;$i<42;$i++)
		{
			$master[$i]=0;
			if($i>=$wval && $i<($lsday+$wval))
			{
				$master[$i]=$j;
				$j++;
			}
		}
		$k=0;
		$weeks=array();       
		 for($i=1;$i<=6;$i++)
		 {            
			 for($j=1;$j<=7;$j++)
			 {
				 if($master[$k]!=0)
				 {
					 $weeks[$i][]=$selmn."-".str_pad($master[$k],2,'0',STR_PAD_LEFT);
					 if($master[$k]==$day)
						 $pre_week=$i;
				 }
				$k++;     
			 }           
		  }
		   
		  $weeknew=array();
		  for($i=1;$i<$pre_week;$i++)
		  {
			  $weeknew[$i]=$weeks[$i];
		  }
		 
		  if($pre_week!=1)
			return  $weeknew;             
		  else 
		  {  
			  $ndate=new DateTime($date);
			  $ndate->sub(new DateInterval('P1M'));
			  $new_selmn=$ndate->format('Y-m');
			  $lsday_new=cal_days_in_month(CAL_GREGORIAN, $ndate->format('m'), $ndate->format('Y'));
			  return $this->getprevmonthweeks($new_selmn, $lsday_new);
		  } 
	}
	//previous weeks submit status.
	public function prev_weeksubmit_status($prevweeks,$emp_id,$emp_details)
	{
	   $fin_str=array();
	   $month_name='';
	   $prev_str='';
		if(sizeof($prevweeks) >0)
		{
			//To get dates of timesheet filled by user of the given duration
			
			$notificationModel = new Timemanagement_Model_Notifications();
			$weekend_date = date('Y-m-d', strtotime('last saturday'));

			for($i=1;$i<=count($prevweeks);$i++)
			{
				$timesheet_date_array = array();
				$weekno=$i;
				$hidstartweek_date=min($prevweeks[$i]);
				$for_month=new DateTime($hidstartweek_date);
				$yr_mnth=$for_month->format('Y-m');
				$month_name=$for_month->format('F');
				$hidendweek_date=max($prevweeks[$i]);
				$hidemp=$emp_id;
				$emp_dept_id = $emp_details['department_id'];
				
				$doj_date = strtotime($emp_details['date_of_joining']);
				$created_date = strtotime($emp_details['createddate']);
				if($created_date < $doj_date)
				{
					$join_date = new DateTime($emp_details['date_of_joining']);
					$join_date = $join_date->format('Y-m-d');
				}
				else{
					$join_date = new DateTime($emp_details['createddate']);
					$join_date = $join_date->format('Y-m-d');
				}
			
		
				/* $join_date = new DateTime($emp_details['date_of_joining']);
				$join_date = $join_date->format('Y-m-d'); */
				
				
				
				$resultData = array();
				$savedTimeSheets = array();
				$resultData = $notificationModel->getTimesheetStatus($emp_id,$hidendweek_date);
				$savedTimeSheets = $notificationModel->getSavedTimesheets($emp_id,$hidendweek_date);

				//End
				if(count($resultData)>0)
				{
					$timesheet_dates_with_status = '';
					foreach($resultData as $array)
					{
						$timesheet_dates_with_status.= $array['ts_week_dates'];
						$timesheet_dates_with_status.='$';
					}
					$ts_date_array = array();
					$ts_date_array = array_filter(explode('$',$timesheet_dates_with_status));
					$timesheetEnteredDates = implode('#',$ts_date_array);
					//echo $timesheetEnteredDates;
					$timesheetEnteredDatesArray = explode('#',$timesheetEnteredDates);
					$statusArray = array('saved','enabled','rejected','blocked','no_entry','submitted','approved');
					$enteredDateStatusArr = array();
					$timesheet_date_arr=array();
					if(count($timesheetEnteredDatesArray)>0)
					{
						foreach($timesheetEnteredDatesArray as $date)
						{
							if(!in_array($date,$statusArray))
							{
								$timesheet_date_arr[] = $date;
							}
							else
							{
								$enteredDateStatusArr[] = $date;
							}
						}
					}
					foreach($timesheet_date_arr as $key => $ts_date)
					{
						//echo 'date'.$ts_date.'-----'.$enteredDateStatusArr[$key].'<br/>';
						if($ts_date>=date('Y-m-01') && $ts_date<=date('Y-m-t') && isset($enteredDateStatusArr[$key]) && ($enteredDateStatusArr[$key]=='approved' || $enteredDateStatusArr[$key]=='submitted'))
						{
							$timesheet_date_array[] = $ts_date;
						}
					}
				}
				$saved_timesheet_date_array = array();
				$timesheet_date_arry=array();
				$enteredDateStatusArray = array();
				if(count($savedTimeSheets)>0)
				{
					$saved_timesheet_dates = '';
					foreach($savedTimeSheets as $arrayy)
					{
						$saved_timesheet_dates.= $arrayy['ts_week_dates'];
						$saved_timesheet_dates.='$';
					}
					$saved_ts_date_arra = array();
					$saved_ts_date_arra = array_filter(explode('$',$saved_timesheet_dates));
					$timesheetEnteredDatesArr = array();
					$timesheetEnteredDate='';
					$timesheetEnteredDate = implode('#',$saved_ts_date_arra);
					//echo $timesheetEnteredDates;
					$timesheetEnteredDatesArr = explode('#',$timesheetEnteredDate);
					$statusArray = array('saved','enabled','rejected','blocked','no_entry','submitted','approved');
					$timesheet_date_arry=array();
					$enteredDateStatusArray = array();
					if(count($timesheetEnteredDatesArr)>0)
					{
						foreach($timesheetEnteredDatesArr as $dates)
						{
							if(!in_array($dates,$statusArray))
							{
								$timesheet_date_arry[] = $dates;
							}
							else
							{
								$enteredDateStatusArray[] = $dates;
							}
						}
					}
					foreach($timesheet_date_arry as $key => $saved_date)
					{
						if($saved_date>=date('Y-m-01') && $saved_date<=date('Y-m-t') && isset($enteredDateStatusArray[$key]) && ($enteredDateStatusArray[$key]=='approved' || $enteredDateStatusArray[$key]=='submitted'))
						if($saved_date>=date('Y-m-01') && $saved_date<=date('Y-m-t') && isset($enteredDateStatusArray[$key]) && ($enteredDateStatusArray[$key]=='approved' || $enteredDateStatusArray[$key]=='submitted'))
						{
							$saved_timesheet_date_array[] = $saved_date;
						}
					}
				}
				$weekDatesArray = array();
				//To get day in the week
				$weekDatesArray = sapp_Global::createDateRangeArray($hidstartweek_date,$hidendweek_date);
				//End
				

				//To get Holidays for the given duration
				
				$holidays = array();
				$holidayDateslistArr = array();
				$loginUserGroupId = $emp_details['holiday_group'];
				if( isset($hidemp) && $hidemp !=''){
					$holidaydatesmodel = new Default_Model_Holidaydates();
					if($loginUserGroupId>0)
					{
						$holidayDateslistArr = $holidaydatesmodel->getHolidayDatesListForGroup($loginUserGroupId);
					}
					if(!empty($holidayDateslistArr))
					{
						for($j=0;$j<sizeof($holidayDateslistArr);$j++)
						{
							$holidays[$j] = $holidayDateslistArr[$j]['holidaydate'];
						}
					}
				}
				//End

				//To get Leaves applied by user for the given duration
				
				$employeeLeaves = array();
				$usersModel = new Timemanagement_Model_Users();
				$employeeLeaves = $usersModel->getEmpLeaves($hidemp,$hidstartweek_date,$hidendweek_date);
				$emp_leave_days = array();
				foreach($employeeLeaves as $empleave)
				{
					$emplev_start_date = $empleave['from_date'];
					$emplev_endt_date = $empleave['to_date'];
					$emp_leave_days[] = sapp_Global::createDateRangeArray($emplev_start_date,$emplev_endt_date);
				}
				$employee_leave_days = array();
				foreach($emp_leave_days as $lev_days)
				{
					foreach($lev_days as $days)
					{
						$employee_leave_days[] = $days;
					}
				}	
				//End
				$empWeekends = array();
				//To get default not working days(saturday and sunday)
				$empWeekends = $usersModel->getWeekend($hidstartweek_date,$hidendweek_date,$emp_dept_id);
				//End
				
				//combine all holidays , leaves, weekends
				$hol_leav_weknd = array();
				$hol_leav_weknd = array_merge($holidays,$employee_leave_days,$empWeekends);
				
				$no_entry_weekennds = array();
				if(count($timesheet_date_arry)>0)
				{
					//if employee work on holiday , weekend
					foreach($timesheet_date_arry as $key =>$val)
					{
						if(in_array($val,$hol_leav_weknd) && $enteredDateStatusArray[$key]=='no_entry')
						{
							$no_entry_weekennds[] = $val;
						}
					}
				}
				//remove holidays,weekend,leaves from between days
				$working_days = array();
				$working_days = array_diff($weekDatesArray, $hol_leav_weknd);
				$emptyDataDatesArry=array();
				$emptyDataDatesArray = array();
				$holiday_timesheets = array();
				$holiday_timesheets = array_diff($working_days,$timesheet_date_array);
				$emptyDataDatesArry = array_merge($holiday_timesheets,$saved_timesheet_date_array);
				$emptyDataDatesArray = array_diff($emptyDataDatesArry,$no_entry_weekennds);
				if(count($emptyDataDatesArray)>0)
				{
					$newemptyDataDatesArray=array();
					foreach($emptyDataDatesArray as $edate)
					{
						if($edate>=$join_date)
							$newemptyDataDatesArray[]=$edate;
					}
					$emptyDataDatesArray=$newemptyDataDatesArray;
				}
				if(count($emptyDataDatesArray) >0)
				{
					$fin_str[]= $weekno;
				}
			}
		}
		if(count($fin_str)>0)
		{
		   $prev_str=  " Week- ".implode(',', $fin_str).' timesheet(s) of '.$month_name."@@@".$yr_mnth;
		}
		return $prev_str;
	}
	//function to get previous pending submissions
	public function getPreviousDaysTSStatus($prevweeks,$emp_id,$emp_details)
	{
		$fin_str=array();
	   $month_name='';
	   $prev_str='';
	   $final_display_array = array();
	  
		if(sizeof($prevweeks) >0)
		{
			//To get dates of timesheet filled by user of the given duration
			$resultData = array();
			$timesheet_date_array = array();
			$enteredDateStatusArr = array();
			$notificationModel = new Timemanagement_Model_Notifications();
			$weekend_date = date('Y-m-d', strtotime('last saturday'));
			for($i=1;$i<=count($prevweeks);$i++)
			{
				 
				$savedTimeSheetArray = array();
				$savedTimeSheetblckedArray = array();
				$weekno=$i;
				$hidstartweek_date=min($prevweeks[$i]);
				$for_month=new DateTime($hidstartweek_date);
				$yr_mnth=$for_month->format('Y-m');
				$month_name=$for_month->format('F');
				$hidendweek_date=max($prevweeks[$i]);
				 $mnth = date('m',strtotime($hidendweek_date));
				 $year = date('Y',strtotime($hidendweek_date));
				$hidemp=$emp_id;
				$emp_dept_id = $emp_details['department_id'];
				$join_date=new DateTime($emp_details['date_of_joining']);
				$join_date=$join_date->format('Y-m-d');
			$resultData = $notificationModel->getpreviousTimesheetStatus($emp_id,$hidendweek_date,$mnth,$year);
			$savedTimeSheets = $notificationModel->getpreviousSavedTimesheets($emp_id,$hidendweek_date,$mnth,$year);
			//End
			if(count($resultData)>0)
			{
				$timesheet_dates_with_status = '';
				foreach($resultData as $array)
				{
					$timesheet_dates_with_status.= $array['ts_week_dates'];
					$timesheet_dates_with_status.='$';
				}
				$timesheet_date_with_status_array = array_filter(explode('$',$timesheet_dates_with_status));
				$timesheetEnteredDatesArray = array();
				if(count($timesheet_date_with_status_array)>0)
				{
					$timesheetEnteredDates = implode('#',$timesheet_date_with_status_array);
					$timesheetEnteredDatesArray = explode('#',$timesheetEnteredDates);
				}
				$enteredDatesArray = array();
				$statusArray = array('saved','enabled','rejected','blocked','no_entry','submitted','approved');
				$enteredDateStatusArr = array();
				$timesheet_date_array = array();
				if(count($timesheetEnteredDatesArray)>0)
				{
					foreach($timesheetEnteredDatesArray as $date)
					{
						if(!in_array($date,$statusArray))
						{
							$timesheet_date_array[] = $date;
						}
						else
						{
							$enteredDateStatusArr[] = $date;
						}
					}
				}
				
			}
			if(count($timesheet_date_array) > 0 && (count($timesheet_date_array) == count($enteredDateStatusArr)))
			{
				foreach($timesheet_date_array as $key=>$value)
				{
					$status = 'No Entry';
					if(isset($key))
					{
						$status = $key;
					}
					$savedTimeSheetArray[$value] = ($enteredDateStatusArr[$status]=='no_entry')?'No Entry':$enteredDateStatusArr[$status];
				}
			}

			$saved_timesheet_date_array = array();
			if(count($savedTimeSheets)>0)
			{
				$saved_timesheet_dates = '';
				foreach($savedTimeSheets as $arrayy)
				{
					$saved_timesheet_dates.= $arrayy['ts_week_dates'];
					$saved_timesheet_dates.='$';
				}
				$timesheet_date_with_status_array = array_filter(explode('$',$saved_timesheet_dates));
				$timesheetEnteredDatesArr = array();
				if(count($timesheet_date_with_status_array)>0)
				{
					$timesheetSavedEnteredDates = implode('#',$timesheet_date_with_status_array);
					$timesheetEnteredDatesArr = explode('#',$timesheetSavedEnteredDates);
				}
				$enteredDatesArray = array();
				$statusArr = array('saved','enabled','rejected','blocked','no_entry','submitted','approved');
				$enteredDateStatusArry = array();
				foreach($timesheetEnteredDatesArr as $dates)
				{
					if(!in_array($dates,$statusArr))
					{
						$saved_timesheet_date_array[] = $dates;
					}
					else
					{
						$enteredDateStatusArry[] = $dates;
					}
				}
			}
			if(count($saved_timesheet_date_array)>0 && count($enteredDateStatusArry)>0)
			{
				foreach($saved_timesheet_date_array as $keyy=>$valuee)
				{
					$savedTimeSheetblckedArray[$valuee] = $enteredDateStatusArry[$keyy];
				}
			}
				//To get day in the week
				$weekDatesArray = sapp_Global::createDateRangeArray($hidstartweek_date,$hidendweek_date);
				//End
				//To get Holidays for the given duration
				
				$holidays = array();
				$holidayDateslistArr = array();
				$loginUserGroupId = $emp_details['holiday_group'];
				if( isset($hidemp) && $hidemp !=''){
					$holidaydatesmodel = new Default_Model_Holidaydates();
					if($loginUserGroupId>0)
					{
						$holidayDateslistArr = $holidaydatesmodel->getHolidayDatesListForGroup($loginUserGroupId);
					}
					if(!empty($holidayDateslistArr))
					{
						for($j=0;$j<sizeof($holidayDateslistArr);$j++)
						{
							$holidays[$j] = $holidayDateslistArr[$j]['holidaydate'];
						}
					}
				}
				$holidaysStatusArray = array();
				if(count($holidays)>0)
				{
					foreach($holidays as $holidayDate){
						//if(!in_array($holidayDate,$timesheet_date_array) && !in_array($holidayDate,$saved_timesheet_date_array) && ($holidayDate>=$hidstartweek_date && $holidayDate<=$hidendweek_date))
						if(!(isset($savedTimeSheetArray[$holidayDate]) && $savedTimeSheetArray[$holidayDate]!='No Entry') && !(isset($savedTimeSheetblckedArray[$holidayDate]) && $savedTimeSheetblckedArray[$holidayDate]!='No Entry') && ($holidayDate>=$hidstartweek_date && $holidayDate<=$hidendweek_date))	
						{
							$holidaysStatusArray[$holidayDate] = 'Holiday';
						}
					}
				}
				//End

				//To get Leaves applied by user for the given duration
				
				$employeeLeaves = array();
				$usersModel = new Timemanagement_Model_Users();
				$employeeLeaves = $usersModel->getEmpLeaves($hidemp,$hidstartweek_date,$hidendweek_date);
				$emp_leave_days = array();
				foreach($employeeLeaves as $empleave)
				{
					$emplev_start_date = $empleave['from_date'];
					$emplev_endt_date = $empleave['to_date'];
					$emp_leave_days[] = sapp_Global::createDateRangeArray($emplev_start_date,$emplev_endt_date);
				}
				$employee_leave_days = array();
				$employee_leave_ts_array = array();
				foreach($emp_leave_days as $lev_days)
				{
					foreach($lev_days as $days)
					{
						$employee_leave_days[] = $days;
						$employee_leave_ts_array[$days]='Leave';
					}
				}	
				//End

				//To get default not working days(saturday and sunday)
				$empWeekends = $usersModel->getWeekend($hidstartweek_date,$hidendweek_date,$emp_dept_id);
				//End	
				$weekend_array = array();
				foreach($empWeekends as $weekend)
				{
					if(!(isset($savedTimeSheetArray[$weekend]) && $savedTimeSheetArray[$weekend]!='No Entry') && !in_array($weekend,$saved_timesheet_date_array))
					{
						$weekend_array[$weekend] = 'Weekend';
					}
				}
				
				//combine all holidays , leaves, weekends
				$hol_leav_weknd = array();
				$hol_leav_weknd = array_merge($holidays,$employee_leave_days,$empWeekends);
				
				//remove holidays,weekend,leaves from between days
				$working_days = array_diff($weekDatesArray, $hol_leav_weknd);
				$emptyDataDatesArray = array();
				$holiday_timesheets = array_diff($working_days,$timesheet_date_array);
				$emptyDataDatesArray = array_merge($holiday_timesheets,$saved_timesheet_date_array);
				$holiday_ts_array = array();
				foreach($holiday_timesheets as $holidayts)
				{
					$holiday_ts_array[$holidayts] = 'No Entry';
				}
				$final_array = array_merge($savedTimeSheetArray,$holiday_ts_array,$savedTimeSheetblckedArray,$holidaysStatusArray,$employee_leave_ts_array,$weekend_array);
		
				
				$final_holadys_removed_array = array();
				foreach($final_array as $finalkey => $finalstatus)
				{
					if(!(in_array($finalkey,$hol_leav_weknd) && $final_array[$finalkey] == 'No Entry'))
					{
						$final_holadys_removed_array[$finalkey] = $finalstatus;
					}
				}
				$final_display_array[$weekno] = $final_holadys_removed_array;
			}
		}
		return $final_display_array;
	}
	
	/**
	 * This method returns all projects under client
	 *
	 * @return array
	 */
	public function getProjectListByClientID($clientID)
	{
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('p'=>$this->_name),array('p.id','project_name'))
		->where('p.is_active = 1 and p.client_id = '.$clientID)
		->order('p.project_name');
		return $this->fetchAll($select)->toArray();
	}	
	
	public function getEmpProjects($to_id){
		
		 $select = $this->select()
		->setIntegrityCheck(false)
		->from(array('pe'=>'tm_project_employees'))
		->joinInner(array('p'=>'tm_projects'), 'pe.project_id=p.id AND pe.is_active=1',array('p.id','p.project_name'))
		->where('pe.emp_id = '.$to_id)
		;
		return $this->fetchAll($select)->toArray();
		
	}
	
}