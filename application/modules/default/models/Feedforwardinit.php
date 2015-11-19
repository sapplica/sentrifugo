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

class Default_Model_Feedforwardinit extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_pa_ff_initialization';
    protected $_primary = 'id';
	
	public function getFeedforwardInitData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "fi.isactive = 1 ";
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$ffInitData = $this->select()
                            ->setIntegrityCheck(false)	
                            ->from(array('fi'=>'main_pa_ff_initialization'),array('fi.id','statusval'=>'fi.status','ff_due_date'=>'DATE_FORMAT(ff_due_date,"'.DATEFORMAT_MYSQL.'")',
                                new Zend_Db_Expr("concat(ff_from_year,'-',ff_to_year) as fin_year"),'fi.ff_mode',
                                new Zend_Db_Expr("if(fi.status =1,'Open','Closed') as status"),
                                new Zend_Db_Expr("case when fi.ff_mode = 'Quarterly' then concat('Q',fi.ff_period) when fi.ff_mode = 'Half-yearly' then concat('H',fi.ff_period) when fi.ff_mode = 'Yearly' then 'Yearly' end as app_period"),
                                new Zend_Db_Expr("case when fi.initialize_status = 1 then case when fi.enable_to = 1 then 'All Employees' when fi.enable_to = 0 then 'Appraisal Employees' end when fi.initialize_status = 2 then 'Initialize later' end as ff_process_status"),
                                ))
                            ->joinLeft(array('b' => 'main_businessunits'),"b.id = fi.businessunit_id and b.isactive=1",array('unitname'))
                            ->joinLeft(array('d' => 'main_departments'),"d.id = fi.department_id and d.isactive=1",array('deptname'))
                            ->where($where)
                            ->order("$by $sort") 
                            ->limitPage($pageNo, $perPage);
    					   
		return $ffInitData;       		
	}
	
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$a='',$b='',$c='',$d='')
	{		
        $searchQuery = '';
        $searchArray = array();
        $data = array();
		
		if($searchData != '' && $searchData!='undefined')
			{
				$searchValues = json_decode($searchData);
				foreach($searchValues as $key => $val)
				{
					$sval = $val;
					$val = mysql_real_escape_string($val);				
					if($key == 'fin_year')
                    	$searchQuery .= " concat(from_year,'-',to_year) like '%".$val."%' AND ";
                	else if($key == 'app_period')
	                    $searchQuery .= " lower(case when fi.ff_mode = 'Quarterly' then concat('Q',fi.ff_period) when fi.ff_mode = 'Half-yearly' then concat('H',fi.ff_period) when fi.ff_mode = 'Yearly' then 'Yearly' end)  like '%".strtoupper($val)."%' AND ";
	             	else if($key == 'ff_due_date')
						$searchQuery .= "  ".$key." like '%".  sapp_Global::change_date($val,'database')."%' AND ";
					else if($key == 'ff_process_status'){
						if($val == 1)
							$searchQuery .= " initialize_status = 2  AND ";
						if($val == 2)
							$searchQuery .= " enable_to = 0 AND ";
						if($val == 3)
							$searchQuery .= " enable_to = 1 AND ";
					}
                	else                                    
	                    $searchQuery .= $key." like '%".$val."%' AND ";
				}
				$searchQuery = rtrim($searchQuery," AND");					
			}
			
		$objName = 'feedforwardinit';
		
		$tableFields = array('action'=>'Action','unitname' => 'Business Unit','deptname' => 'Department','fin_year' => 'Financial Year',
                    'ff_mode'=>'Mode','app_period' => 'Period','ff_due_date' => 'Due Date','status' => 'Appraisal Status','ff_process_status' => 'Process Status');
		
		$tablecontent = $this->getFeedforwardInitData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
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
			'actionparam'=>'yes',
			'search_filters' => array(
                            'status' =>array(
                                'type'=>'select',
                                'filter_data' => array(''=>'All','1' => 'Open','2' => 'Closed'),
                            ),
                            'ff_mode' => array(
                                'type' => 'select',
                                'filter_data' => array('' => 'All','Quarterly' => 'Quarterly','Half-yearly' => 'Half-yearly','Yearly' => 'Yearly',),
                            ),
                            'ff_due_date' => array('type' => 'datepicker'),
                            'ff_process_status' => array(
                                'type' => 'select',
                                'filter_data' => array('' => 'All','1' => 'Initialize later','2' => 'Appraisal employees','3' => 'All employees'),
                            ),
                        ),
		);
		return $dataTmp;
		
	}
	
	public function getAppDataForFF($notIn,$businessUnitId='',$departmentId='')
	{
		$where = '';
		if($notIn == 'yes')
			$where .= ' AND ai.id not in (select appraisal_id from main_pa_ff_initialization where isactive = 1) ';
     		
		$select = $this->select()
						->setIntegrityCheck(false)	
						->from(array('ai'=>'main_pa_initialization'),array('ai.id',
                                //new Zend_Db_Expr("CONCAT(b.unitcode,' - ',IF(d.deptcode is null,'',CONCAT(d.deptcode,' - ')),ai.from_year,'-',ai.to_year,' - ',ai.appraisal_mode) as app_mode")
                                new Zend_Db_Expr("CONCAT(ai.from_year,'-',ai.to_year,' ',ai.appraisal_mode,
                                (case when ai.appraisal_mode = 'Quarterly' then concat('(Q',ai.appraisal_period,') ') when ai.appraisal_mode = 'Half-yearly' then concat('(H',ai.appraisal_period,') ') when ai.appraisal_mode = 'Yearly' then ' ' end),
                                b.unitname,IF(d.deptname is null,'',CONCAT('(',d.deptname,')'))) as app_mode")
                      		))
                        ->joinLeft(array('b' => 'main_businessunits'),"b.id = ai.businessunit_id and b.isactive=1",array())
                        ->joinLeft(array('d' => 'main_departments'),"d.id = ai.department_id and d.isactive=1",array())
					    ->where('ai.isactive = 1 AND ai.status = 2 and ai.enable_step = 2 '.$where);
		return $this->fetchAll($select)->toArray();
	}
	
	public function SaveorUpdateFeedforwardInitData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_pa_ff_initialization');
			return $id;
		}
	}
	
	public function getQuestionsFeedforward($qs_csv='')
	{
     	$where = " isactive = 1 AND module_flag = 2 ";
		$db = Zend_Db_Table::getDefaultAdapter();

		if($qs_csv)
			$where .= ' AND id in ('.$qs_csv.') ';
		
        $query = "select * from main_pa_questions where".$where;
        $result = $db->query($query)->fetchAll();
       	return $result;
	}
	
	public function getFFInitData($id)
	{
            $select = $this->select()
                            ->setIntegrityCheck(false)
                            ->from(array('fi'=>'main_pa_ff_initialization'),array('fi.*'))
                            ->where('fi.isactive = 1 AND fi.id='.$id.' ');
            return $this->fetchAll($select)->toArray();		
	}
	
	public function getFFInitViewData($id)
	{
            $select = $this->select()
                            ->setIntegrityCheck(false)
                            ->from(array('fi'=>'main_pa_ff_initialization'),array('fi.*',
                            	new Zend_Db_Expr("case when fi.initialize_status = 1 then case when fi.enable_to = 1 then 'All Employees' when fi.enable_to = 0 then 'Appraisal Employees' end when fi.initialize_status = 2 then 'Initialize later' end as ff_process_status"),))
                            ->joinLeft(array('b' => 'main_businessunits'),"b.id = fi.businessunit_id and b.isactive=1",array('unitname'))
                        	->joinLeft(array('d' => 'main_departments'),"d.id = fi.department_id and d.isactive=1",array('deptname'))
                            ->where('fi.isactive = 1 AND fi.id='.$id.' ');
            return $this->fetchAll($select)->toArray();		
	}
	
	public function getEmpsFromAppEmpRat($appInitId)
	{
            $select = $this->select()
                            ->setIntegrityCheck(false)
                            ->from(array('ae'=>'main_pa_employee_ratings'),array('ae.employee_id','ae.line_manager_1'))
                            ->where('ae.isactive = 1 AND ae.pa_initialization_id='.$appInitId.' ');
            return $this->fetchAll($select)->toArray();		
	}
	
	public function getEmpsFromSummary($notInAppEmp)
	{
            $select = $this->select()
                            ->setIntegrityCheck(false)
                            ->from(array('es'=>'main_employees_summary'),array('es.user_id','es.reporting_manager'))
                            ->where('es.isactive = 1 AND es.user_id not in ('.$notInAppEmp.') ');
            return $this->fetchAll($select)->toArray();		
	}
	
	public function getFFbyBUDept($id='',$open='')
	{
		$where = '';
		if($id)
			$where .= ' AND fi.id = '.$id;
		if($open == 'yes')
			$where .= ' AND fi.status = 1 AND fi.initialize_status = 1';
		else
			$where .= ' AND fi.status = 2 ';
            $select = $this->select()
                            ->setIntegrityCheck(false)
                            ->from(array('fi'=>'main_pa_ff_initialization'),array('fi.*',
                            	//new Zend_Db_Expr("CONCAT(b.unitcode,' - ',IF(d.deptcode is null,'',CONCAT(d.deptcode,' - ')),fi.ff_from_year,'-',fi.ff_to_year,' - ',fi.ff_mode) as mode")
                            	new Zend_Db_Expr("CONCAT(fi.ff_from_year,'-',fi.ff_to_year,' ',fi.ff_mode,
                                (case when fi.ff_mode = 'Quarterly' then concat('(Q',fi.ff_period,') ') when fi.ff_mode = 'Half-yearly' then concat('(H',fi.ff_period,') ') when fi.ff_mode = 'Yearly' then ' ' end),
                                b.unitname,IF(d.deptname is null,'',CONCAT('(',d.deptname,')'))) as mode")
                            ))
                            ->joinLeft(array('b' => 'main_businessunits'),"b.id = fi.businessunit_id and b.isactive=1",array('unitname'))
                        	->joinLeft(array('d' => 'main_departments'),"d.id = fi.department_id and d.isactive=1",array('deptname'))
                            ->where('fi.isactive = 1 '.$where);
            return $this->fetchAll($select)->toArray();		
	}
	
	public function getManagerRatingsByFFId($id,$mgrId='')
	{
		$where = '';
		if($mgrId)
			$where .= ' AND fe.manager_id = '.$mgrId;
            $select = $this->select()
                            ->setIntegrityCheck(false)
                            ->from(array('fe'=>'main_pa_ff_employee_ratings'),array(new Zend_Db_Expr('avg(fe.consolidated_rating) as con_rating'),'fe.manager_id'))
                            ->joinInner(array('es'=>'main_employees_summary'), 'es.user_id = fe.manager_id AND es.isactive = 1', 
                            	array('es.userfullname', 'es.employeeId', 'es.jobtitle_name', 'es.reporting_manager_name', 'es.department_name', 'es.businessunit_name', 'es.profileimg'))  
							->joinInner(array('ei'=>'main_pa_ff_initialization'), 'ei.id = fe.ff_initialization_id AND ei.isactive = 1', 
                            	array('ei.id'))
							->joinInner(array('pr'=>'main_pa_ratings'), ' pr.pa_initialization_id = ei.pa_configured_id AND pr.isactive = 1', 
                            	array('pr.rating_type'))
                            ->where('fe.isactive = 1 AND fe.ff_status = 2 AND fe.ff_initialization_id = '.$id.$where)
                            ->group('fe.manager_id');
            return $this->fetchAll($select)->toArray();		
	}
	
	public function getDetailEmpsDataByMgrId($ffId, $mgrId, $empId='')
	{
		$where = '';
		if($empId)
			$where .= ' AND fe.employee_id = '.$empId;
            $select = $this->select()
                            ->setIntegrityCheck(false)
                            ->from(array('fe'=>'main_pa_ff_employee_ratings'),array('fe.question_ids','fe.employee_response','fe.additional_comments'))
                            ->joinInner(array('es'=>'main_employees_summary'), 'es.user_id = fe.employee_id AND es.isactive = 1', 
                            	array('es.user_id','es.userfullname', 'es.employeeId', 'es.jobtitle_name', 'es.reporting_manager_name', 'es.department_name', 'es.businessunit_name', 'es.profileimg'))
                            ->where('fe.isactive = 1 AND fe.ff_status = 2 AND fe.ff_initialization_id = '.$ffId.' AND fe.manager_id = '.$mgrId.$where);
            return $this->fetchAll($select)->toArray();		
	}
	
	public function getCountOfEmps($ffId)
	{
            $select = $this->select()
                            ->setIntegrityCheck(false)
                            ->from(array('fe'=>'main_pa_ff_employee_ratings'),array(new Zend_Db_Expr('ifnull(sum(fe.ff_status=2),0) as completed'),
                            		new Zend_Db_Expr('count(*) as total')))
                            ->where('fe.isactive = 1 AND fe.ff_initialization_id = '.$ffId);
            return $this->fetchAll($select)->toArray();		
	}
	
	public function getEmpIdforFF($bunit,$dept)
	{
        $result = array();
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$str_dept = '';
    	if($dept != '')
    	$str_dept = " department_id = $dept and ";
        $query = "select user_id as employeeId from main_employees_summary where businessunit_id = $bunit and $str_dept isactive=1 order by user_id;";
        $result = $db->query($query)->fetchAll();
        return $result;	
	}
	public function getAppemployeeIDs($appraisalid)
    {
    	$result = array();
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select employee_id as employeeId from main_pa_employee_ratings where pa_initialization_id = $appraisalid and isactive=1 ";
        $result = $db->query($query)->fetchAll();
        return $result;
    }
	public function getUserDetailsByIds($employeeid='')
    {
    	$result = array();
    	$db = Zend_Db_Table::getDefaultAdapter();
        $query = "select emailaddress from main_employees_summary where user_id in($employeeid) and isactive=1;";
        $result = $db->query($query)->fetchAll();
        return $result;
    }
}