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

class Default_Model_Appraisalemployeeratings extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_pa_employee_ratings';
    protected $_primary = 'id';
	
    public function getRatingsByInitId($init_id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select id,rating_value from main_pa_ratings where isactive = 1 and pa_initialization_id = '".$init_id."'";
        $result = $db->query($query)->fetchAll();
        $fin_arr = array();
        if(count($result) > 0)
        {
            foreach($result as $res)
            {
                $fin_arr[$res['rating_value']] = $res['id'];
            }
        }
        return $fin_arr;
    }
	public function getAppraisalEmpStatusData($sort, $by, $pageNo, $perPage, $searchQuery, $a, $b)
	{
		$where = "aer.isactive = 1 AND ai.enable_step = 2 AND ai.status = 1 ";
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$servicedeskDepartmentData = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('aer'=>'main_pa_employee_ratings'),array('es.userfullname','es.employeeId','es.reporting_manager_name','es.department_name','aer.appraisal_status'))
                           ->joinInner(array('es'=>'main_employees_summary'), 'es.user_id = aer.employee_id')
                           ->joinInner(array('ai'=>'main_pa_initialization'), 'ai.id = aer.pa_initialization_id')
                           ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		return $servicedeskDepartmentData;       		
	}
	
	public function getAppraisalEmployeeStatusGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$a='',$b='',$c='',$d='')
	{		
        $searchQuery = '';
        $searchArray = array();
        $data = array();
		
		if($searchData != '' && $searchData!='undefined')
			{
				$searchValues = json_decode($searchData);
				foreach($searchValues as $key => $val)
				{
							$searchQuery .= " ".$key." like '%".$val."%' AND ";
                           $searchArray[$key] = $val;
				}
				$searchQuery = rtrim($searchQuery," AND");					
			}
			
		$objName = 'appraisalstatusemployee';
		
		$tableFields = array('employeeId' => 'Employee ID','userfullname' => 'Employee Name','reporting_manager_name' => 'Line Manager','department_name' => 'Department','appraisal_status' => 'Status');
		
		$tablecontent = $this->getAppraisalEmpStatusData($sort, $by, $pageNo, $perPage, $searchQuery, $a, $b);     
		
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
		);
		return $dataTmp;
	}
	
	public function getSelfAppraisalData($sort, $by, $pageNo, $perPage, $searchQuery, $employeeId)
	{
		$where = "aer.isactive = 1 AND ai.status = 1 AND ai.enable_step = 2 AND aer.employee_id = ".$employeeId;
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$selfAppData = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('aer'=>'main_pa_employee_ratings'),array('aer.id','ai.appraisal_mode','es.userfullname','es.employeeId','es.jobtitle_name','es.department_name','aer.appraisal_status','ai.status'))
                           ->joinInner(array('es'=>'main_employees_summary'), 'es.user_id = aer.employee_id', array())
                           ->joinInner(array('ai'=>'main_pa_initialization'), 'ai.id = aer.pa_initialization_id', array())
                           ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
   
		return $selfAppData;       		
	}
	
	public function getSelfAppraisalGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$employeeId)
	{		
        $searchQuery = '';
        $searchArray = array();
        $data = array();
		
		if($searchData != '' && $searchData!='undefined')
		{
			$searchValues = json_decode($searchData);
			foreach($searchValues as $key => $val)
			{
				if($key == 'appraisal_status')
					$searchQuery .= " ".$key." = ".$val." AND ";
				else
					$searchQuery .= " ".$key." like '%".$val."%' AND ";
				$searchArray[$key] = $val;
			}
			$searchQuery = rtrim($searchQuery," AND");					
		}
			
		$objName = 'appraisalself';
		
		$tableFields = array('action'=>'Action','appraisal_mode' => 'Appraisal Period','employeeId' => 'Employee ID','userfullname' => 'Employee Name','jobtitle_name' => 'Job Title','department_name' => 'Department','appraisal_status' => 'Status');
		
		$tablecontent = $this->getSelfAppraisalData($sort, $by, $pageNo, $perPage, $searchQuery, $employeeId);     
		
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
			'search_filters' => 
						array(
                            'appraisal_status' => array(
                                'type' => 'select',
                                'filter_data' => array(''=>'All',1=>APP_PENDING_EMP,2=>APP_PENDING_L1,
                                						3=>APP_PENDING_L2,4=>APP_PENDING_L3,
														5=>APP_PENDING_L4,6=>APP_PENDING_L5,7=>APP_COMPLETED),
                           	),
                        ),
		);
		return $dataTmp;
	}
	
	public function SaveorUpdateAppraisalSkillsData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_pa_employee_ratings');
			return $id;
		}	
	} 

	public function checkEmployeeExists($appraisalid,$employeeid)
	{
		$select = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('main_pa_employee_ratings'),array('empcount'=>'count(*)'))
                           ->where("isactive = 1 AND pa_initialization_id = $appraisalid AND employee_id=$employeeid");
		                           
		return $this->fetchAll($select)->toArray();       		
	}
	public function getEmpAppraisalDoc($employee_id,$appraisal_id)
	{
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('aer'=>'main_pa_employee_ratings'),array('aer.id','aer.line_manager_1','aer.line_manager_2','aer.line_manager_3','aer.line_manager_4','aer.line_manager_5'))
		->where("isactive = 1 AND pa_initialization_id = $appraisal_id AND employee_id=$employee_id");
		 
		return $this->fetchAll($select)->toArray();
	}
	public function getAnalyticsEmpAppraisalPdfData($employee_id,$appraisal_id)
	{
	
	    $select = $this->select()
		->setIntegrityCheck(false)
		->from(array('aer'=>'main_pa_employee_ratings'))
		->joinInner(array('es'=>'main_employees_summary'), 'es.user_id = aer.employee_id',array('es.userfullname','es.employeeId','es.jobtitle_name','es.department_name','es.businessunit_name','ai.appraisal_period','ai.from_year','ai.to_year','ai.appraisal_mode'))
        ->joinInner(array('ai'=>'main_pa_initialization'), 'ai.id = aer.pa_initialization_id', array())
        ->where("aer.isactive = 1 AND aer.pa_initialization_id = $appraisal_id AND aer.employee_id=$employee_id");
			
		return $this->fetchAll($select)->toArray();
	}
	
	public function checkEmployeeResponse($appraisalid)
	{
		$select = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('main_pa_employee_ratings'),array('empresponse'=>'count(*)'))
                           ->where("isactive = 1 AND pa_initialization_id = $appraisalid AND appraisal_status!=7");
		                           
		return $this->fetchAll($select)->toArray();       		
	}

	public function getSelfAppraisalDataByEmpID($employeeId)
	{
		$where = "aer.isactive = 1 AND ai.status = 1 AND ai.enable_step = 2 AND aer.employee_id = ".$employeeId;
		$select = $this->select()
    					   ->setIntegrityCheck(false)
                           ->from(array('aer'=>'main_pa_employee_ratings'),array('aer.id','aer.employee_id','ai.appraisal_mode','es.userfullname','es.employeeId','es.jobtitle_name','es.department_name',
                           													'aer.appraisal_status','ai.status','ai.category_id','aer.pa_initialization_id','ai.pa_configured_id','aer.line_manager_1','aer.line_manager_2','aer.line_manager_3','aer.line_manager_4','aer.line_manager_5','aer.line_rating_1',
                           													'aer.line_rating_2','aer.line_rating_3','aer.line_rating_4','aer.line_rating_5','aer.line_comment_1','aer.line_comment_2',
                           													'aer.line_comment_3','aer.line_comment_4','aer.line_comment_5','aer.employee_response','aer.manager_response','es.businessunit_name',
                           													'ai.appraisal_period','ai.from_year','ai.to_year','ai.employees_due_date','es.profileimg','aer.consolidated_rating','aer.skill_response','ai.appraisal_ratings'))
                           ->joinInner(array('es'=>'main_employees_summary'), 'es.user_id = aer.employee_id', array())
                           ->joinInner(array('ai'=>'main_pa_initialization'), 'ai.id = aer.pa_initialization_id', array())
                           ->where($where);
                           
		return $this->fetchAll($select)->toArray();       		
	}
        
        public function getSelfAppraisalHistoryDataByAppID($id)
	{
		$where = "aer.id = ".$id;
		$select = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('aer'=>'main_pa_employee_ratings'),array('aer.id','aer.employee_id','ai.appraisal_mode','es.userfullname','es.employeeId','es.jobtitle_name','es.department_name',
                           													'aer.appraisal_status','ai.status','ai.category_id','aer.pa_initialization_id','ai.pa_configured_id','aer.line_manager_1','aer.line_manager_2','aer.line_manager_3','aer.line_manager_4','aer.line_manager_5','aer.line_rating_1',
                           													'aer.line_rating_2','aer.line_rating_3','aer.line_rating_4','aer.line_rating_5','aer.line_comment_1','aer.line_comment_2',
                           													'aer.line_comment_3','aer.line_comment_4','aer.line_comment_5','aer.employee_response','aer.manager_response','es.businessunit_name',
                           													'ai.appraisal_period','ai.from_year','ai.to_year','ai.employees_due_date','es.profileimg','aer.consolidated_rating','aer.skill_response','ai.appraisal_ratings'))
                           ->joinInner(array('es'=>'main_employees_summary'), 'es.user_id = aer.employee_id', array())
                           ->joinInner(array('ai'=>'main_pa_initialization'), 'ai.id = aer.pa_initialization_id', array())
                           ->where($where);
                           
		return $this->fetchAll($select)->toArray();       		
	}
	
	public function getFeedforwardDataByEmpID($employeeId)
	{
		$where = "fer.isactive = 1 AND fi.status = 1 AND fer.employee_id = ".$employeeId;
		 $select = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('fer'=>'main_pa_ff_employee_ratings'),array('fer.id','fer.employee_id','fi.ff_mode','es.userfullname','es.employeeId','es.jobtitle_name','es.department_name',
                           													'fer.ff_status','fi.status','fer.ff_initialization_id','fi.pa_configured_id','fer.employee_response','es.businessunit_name',
                           													'fi.ff_period','fi.ff_from_year','fi.ff_to_year','fi.ff_due_date','es.profileimg','fer.consolidated_rating'))
                           ->joinInner(array('es'=>'main_employees_summary'), 'es.user_id = fer.employee_id', array())
                           ->joinInner(array('fi'=>'main_pa_ff_initialization'), 'fi.id = fer.ff_initialization_id', array())
                           ->where($where);
                           
		return $this->fetchAll($select)->toArray();       		
	}
	
	public function getAppEmpQuesPrivData($init_id, $emp_id)
	{
		 $select = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('aqp'=>'main_pa_questions_privileges'),array('aqp.*'))
                           ->where("aqp.isactive = 1 AND aqp.pa_initialization_id = ".$init_id." AND aqp.employee_id = ".$emp_id);
                           
		return $this->fetchAll($select)->toArray();       		
	}
	
	public function getAppQuesDataByIDs($ques_ids)
	{
		$select = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('aq'=>'main_pa_questions'),array('aq.id','aq.pa_category_id','aq.question','aq.description'))
                           ->where("aq.isactive = 1 AND aq.module_flag = 1 AND aq.id in (".$ques_ids.")");
                           
		return $this->fetchAll($select)->toArray();       		
	}
	
	public function getAppCateDataByIDs($cate_ids)
	{
		$select = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('ac'=>'main_pa_category'),array('ac.id','ac.category_name'))
                           ->where("ac.isactive = 1 AND ac.id in (".$cate_ids.")");
		                           
		return $this->fetchAll($select)->toArray();       		
	}
	
	public function getAppRatingsDataByConfgId($config_id,$init_id)
	{
		$select = $this->select()
                           ->setIntegrityCheck(false)	
                           ->from(array('ar'=>'main_pa_ratings'),array('ar.id','ar.rating_type','ar.rating_value','ar.rating_text'))
                           ->where("ar.isactive = 1 and ar.pa_initialization_id = '".$init_id."'");                         
		return $this->fetchAll($select)->toArray();       		
	}
	
	public function getUserNamesByIDs($user_ids)
	{
		 $select = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('u'=>'main_users'),array('u.id','u.userfullname'))
                           ->where("u.isactive = 1 AND u.id in (".$user_ids.")");
		                           
		return $this->fetchAll($select)->toArray();       		
	}
	
	public function getSkillNamesByIDs($skill_ids)
	{
		$select = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('s'=>'main_pa_skills'),array('s.id','s.skill_name'))
                           ->where("s.isactive = 1 AND s.id in (".$skill_ids.")");
		                           
		return $this->fetchAll($select)->toArray();       		
	}
	
	/**
	 * 
	 * Get 'My Team Appraisal - Employee' skills
	 */
	public function getAppEmpSkills($pa_init_id, $emp_id) {
		$select = $this->select()
    					   ->setIntegrityCheck(false)
                           ->from(array('er'=>'main_pa_employee_ratings'),array('er.skill_response'))
                           ->where("er.isactive = 1 AND er.pa_initialization_id = '".$pa_init_id."' AND employee_id = '".$emp_id."'")
                           ->limit(1, 0);
		                           
		$skills_result = $this->fetchRow($select)->toArray();
		$arr_skills = (array)json_decode(array_shift($skills_result));

		return implode(",", array_keys($arr_skills));
	}
	
	public function getAppHistoryData($empId,$appInitID)
	{
		 $select = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('h'=>'main_pa_appraisalhistory'),array('h.*','hdate'=>'date(createddate)','htime'=>'time(createddate)',))
                           ->where("h.isactive = 1 AND h.employee_id = $empId AND h.pa_initialization_id = $appInitID");
		                           
		return $this->fetchAll($select)->toArray();       		
	}
	
	public function getEmployeeIds($appInitID,$cron='',$appstatus='')
	{
		$where = '';
		if($cron)
			$where = ' AND appraisal_status!=7 ';
		if($appstatus)
		    $where = ' AND appraisal_status= '.$appstatus.'';	
		
		$select = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('main_pa_employee_ratings'),array('employee_id','appraisal_status'))
                           ->where("isactive = 1 AND pa_initialization_id = $appInitID $where");
		                           
		return $this->fetchAll($select)->toArray();       		
	}

	public function getEmpRatings($startyear=0,$endyear=0,$department,$reportingmanager,$businessunit,$appraisalData,$employeeid)
	{
		$appraisalIds = array();
		if(!empty($appraisalData))
		{
			for($a = 0; $a < sizeof($appraisalData); $a++)
			{
				array_push($appraisalIds,$appraisalData[$a]['appraisalIds']);				
			}
			$appraisalIds = implode(",",$appraisalIds);
		}
		$wherecondition = '';
		if(isset($reportingmanager) && is_numeric($reportingmanager) != '')
		{
			$wherecondition .= " AND b.reporting_manager=$reportingmanager ";
		}
		if(isset($businessunit) && !empty($businessunit))
		{
			$bunitcondition = '';
			foreach($businessunit as $bunit)
			{
				$bunitcondition .= $bunit.',';
			}
			$bunitcondition = rtrim($bunitcondition,',');
			$wherecondition .= " AND businessunit_id IN ($bunitcondition) ";
		}		
		if(isset($department) && !empty($department))
		{
			$deptcondition = '';
			foreach($department as $dept)
			{
				$deptcondition .= $dept.',';
			}
			$deptcondition = rtrim($deptcondition,',');
			$wherecondition .= " AND department_id IN ($deptcondition) ";
		}
		if(isset($employeeid) && is_numeric($employeeid))
		{
			$wherecondition .= " AND a.employee_id=$employeeid ";
		}
		if(!empty($appraisalIds))
		{
			$appSql = "select a.employee_id,b.employeeId,round(avg(a.consolidated_rating),2) as consolidated_rating ,floor(avg(a.consolidated_rating)) as consolidated_floor_rating,b.businessunit_name,b.department_name,b.firstname,b.lastname,b.profileimg,b.reporting_manager,b.reporting_manager_name,a.appraisal_status,b.jobtitle_name
			from main_pa_employee_ratings a 
			inner join main_employees_summary b on a. employee_id = b.user_id 
			where a.pa_initialization_id in (".$appraisalIds.") and a.isactive = 1 and a.appraisal_status = 'Completed' $wherecondition 
			group by a.employee_id 
			order by round(avg(a.consolidated_rating),2) DESC";
			$db = Zend_Db_Table::getDefaultAdapter();
			$result = $db->query($appSql);
			$empRatingsData = $result->fetchAll();
			return $empRatingsData;
		}
		else
		{ 
			return array();
		}
	}
	//function to get the count of employees group by consolidated ratings
	public function getEmpCountWithConsolidatedRatings($startyear=0,$endyear=0,$department,$reportingmanager,$businessunit,$appraisalData,$employeeid)
	{
		$appraisalIds = array();
		if(!empty($appraisalData))
		{
			for($a = 0; $a < sizeof($appraisalData); $a++)
			{
				array_push($appraisalIds,$appraisalData[$a]['appraisalIds']);				
			}
			$appraisalIds = implode(",",$appraisalIds);
		}
		$wherecondition = '';
		if(isset($reportingmanager) && is_numeric($reportingmanager) != '')
		{
			$wherecondition .= " AND b.reporting_manager=$reportingmanager ";
		}
		if(isset($businessunit) && !empty($businessunit))
		{
			$bunitcondition = '';
			foreach($businessunit as $bunit)
			{
				$bunitcondition .= $bunit.',';
			}
			$bunitcondition = rtrim($bunitcondition,',');
			$wherecondition .= " AND businessunit_id IN ($bunitcondition) ";
		}
		if(isset($department) && !empty($department))
		{
			$deptcondition = '';
			foreach($department as $dept)
			{
				$deptcondition .= $dept.',';
			}
			$deptcondition = rtrim($deptcondition,',');
			$wherecondition .= " AND department_id IN ($deptcondition) ";
		}
		if(isset($employeeid) && is_numeric($employeeid))
		{
			$wherecondition .= " AND a.employee_id=$employeeid ";
		}		
		if(!empty($appraisalIds))
		{
			$appSql = "select count(x.employee_id) as emp_count,x.consolidated_rating from
						(
						select a.employee_id,floor(avg(a.consolidated_rating)) as consolidated_rating 
						from main_pa_employee_ratings a 
						inner join main_employees_summary b on a. employee_id = b.user_id 
						where a.pa_initialization_id in (".$appraisalIds.") and a.isactive = 1 and a.appraisal_status = 'Completed' $wherecondition 
						group by a.employee_id
						order by floor(avg(a.consolidated_rating)) DESC
						) as x
						group by x.consolidated_rating ";
			$db = Zend_Db_Table::getDefaultAdapter();
			$result = $db->query($appSql);
			$empRatingsData = $result->fetchAll();

			return $empRatingsData;
		}
		else
		{ 
			return array();
		}
	}
	
	public function getappraisalidsforperformance($startyear=0,$endyear=0,$department,$reportingmanager,$businessunit)
	{
		$wherecondition = '';
		if(isset($businessunit) && !empty($businessunit))
		{
			$bunitcondition = '';
			foreach($businessunit as $bunit)
			{
				$bunitcondition .= $bunit.',';
			}
			$bunitcondition = rtrim($bunitcondition,',');
			$wherecondition .= " AND businessunit_id IN ($bunitcondition) ";
		}
		if(is_numeric($startyear) && is_numeric($endyear))
		{
			$wherecondition .= " and from_year=$startyear and to_year=$endyear ";
		}
		$sql = "select id as appraisalIds,from_year,appraisal_mode,appraisal_period 
				from main_pa_initialization  
				where status = 2 $wherecondition";
		$sql .= " order by from_year DESC";
		$db = Zend_Db_Table::getDefaultAdapter();
		$res = $db->query($sql);
		return $res->fetchAll();	
	}
	
	public function getEmpAppraisalData($empId){

		if(!empty($empId))
		{
			$res = $this->select()
					->setIntegrityCheck(false)
					->from(array('a'=> 'main_pa_employee_ratings'),array('group_concat(concat_ws("_",b.id,b.appraisal_period,a.consolidated_rating)) as appraisalRatings','b.appraisal_mode', 'b.from_year','b.to_year',))
					->joinInner(array('b' => 'main_pa_initialization'),'a.pa_initialization_id = b. id',array())
					->where('a.pa_initialization_id = b. id and a.employee_id = '.$empId.' and a.isactive = 1 and b.isactive = 1 and b.status = 2  AND a.appraisal_status = "Completed"')
				    ->group('b.from_year')
					->group('b.to_year');
			//and b.status = 2
			return $this->fetchAll($res)->toArray();
		}
	}

	public function getSelectedAppraisalData_notused($appId,$empId)
	{

		if(!empty($appId) && !empty($empId))
		{
			$res = $this->select()
				->setIntegrityCheck(false)
				->from(array('a' => 'main_pa_employee_ratings'),array('a.*',new Zend_Db_Expr("a.appraisal_status+0 as appstatus")))
				->where('a.isactive = 1 and a.employee_id = '.$empId.' and a.pa_initialization_id = '.$appId);
			return $this->fetchRow($res)->toArray();
		}
	}

	public function getSelectedAppraisalData($appId,$empId,$period)
	{
		if(!empty($appId) && !empty($empId) && !empty($period))
		{
			$res = $this->select()
					->setIntegrityCheck(false)
					->from(array('b' => 'main_pa_initialization'),array('a.employee_response','a.manager_response','a.line_manager_1','a.line_manager_2','a.line_manager_3','a.line_manager_4','a.line_manager_5','a.line_comment_1','a.line_comment_2','a.line_comment_3','a.line_comment_4','a.line_comment_5','a.line_rating_1','a.line_rating_2','a.line_rating_3','a.line_rating_4','a.line_rating_5','a.skill_response','b.appraisal_mode','b.appraisal_period','b.from_year','b.to_year','b.category_id','b.pa_configured_id'))
					->joinInner(array('a' => 'main_pa_employee_ratings'),'a.pa_initialization_id = b.id',array())
//					->joinInner(array('ra' => 'main_pa_ratings'), 'ra.pa_initialization_id = b.id',array('ra.rating_type'))
					->where('a.employee_id = '.$empId.' and a.pa_initialization_id = '.$appId.' and b.appraisal_period = '.$period );
			return $this->fetchAll($res)->toArray();
		}
	}

	public function getCategories($strCategories)
	{
		if(!empty($strCategories))
		{
			$res = $this->select()
					->setIntegrityCheck(false)
					->from(array('a' => 'main_pa_category'),array('a.*'))
					->where('a.id in ('.$strCategories.') and a.isactive = 1');
			return $this->fetchAll($res)->toArray();
		}
	}

	public function getEmployeeData($strEmpIds)
	{
		if(!empty($strEmpIds))
		{
			$res = $this->select()
					->setIntegrityCheck(false)
					->from(array('a' => 'main_employees_summary'),  array('a.user_id','a.businessunit_name','a.department_name','a.jobtitle_name','a.position_name','a.userfullname'))
					->where('user_id in ('.$strEmpIds.')');

			return $this->fetchAll($res)->toArray();
		}
	}

	public function getQuestionsData($strQuestions)
	{
		if(!empty($strQuestions))
		{
			 $res = $this->select()
					->setIntegrityCheck(false)
					->from(array('a' => 'main_pa_questions'),  array('a.*'))
					->where('a.id in ('.$strQuestions.')');
			return $this->fetchAll($res)->toArray();
		}
	}

	public function getRatingsData($ratingIdsStr)
	{
		try
		{
			if(!empty($ratingIdsStr))
			{
				$res = $this->select()
						->setIntegrityCheck(false)
						->from(array('a' => 'main_pa_ratings'),array('a.*'))	
						->where('a.id in ('.$ratingIdsStr.')')
						->order('a.rating_value');
				return $this->fetchAll($res)->toArray();
			}
		}
		catch(Exception $e)
		{
			
		}

	}
	public function get_total_rating($appraisal_id,$employee_id,$line_mgr_P,$line_mgr_C)//$line_mgr_P means $line_rating_1+..., $line_mgr_C means $line_rating_1,
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = "select $line_mgr_C coalesce(sum($line_mgr_P) ,$line_mgr_C 0) as total_ratings 
				from main_pa_employee_ratings where employee_id=$employee_id and pa_initialization_id=$appraisal_id; ";
		$res =  $db->query($sql)->fetch(); 
		return $res;
		
	}
	
	public function get_total_lineMgr_count($appraisal_id,$employee_id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = "select line_manager_1,line_manager_2,line_manager_3,line_manager_4,line_manager_5 from main_pa_employee_ratings 
				where employee_id = $employee_id and pa_initialization_id=$appraisal_id and isactive=1;";
		$res =  $db->query($sql)->fetch();
		$res = sizeof(array_filter($res)); 
		return $res;
	 	
	}
	
	
}