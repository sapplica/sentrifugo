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

class Default_Model_Appraisalhistory extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_pa_appraisalhistory';
    protected $_primary = 'id';
	
	public function SaveorUpdateAppraisalHistoryData($data)
	{
		$this->insert($data);
		$id=$this->getAdapter()->lastInsertId('main_pa_appraisalhistory');
		return $id;
	}

	//function to get the appraisal history for a particular employee
	public function getEmpAppraisalHistory($employee_id)
	{
		$result = array();
		if(is_numeric($employee_id))
		{
			$sql_str = "select a.employee_id,group_concat(a.pa_initialization_id) as init_ids,c.from_year,c.to_year,format(COALESCE(avg(a.consolidated_rating),0),2) as consolidated_rating,case appraisal_mode when 'Quarterly' then 'Q1, Q2, Q3, Q4' when 'Half-yearly' then 'H1, H2' when 'Yearly' then 'Yearly' end as app_mode,group_concat(c.appraisal_period) as app_period    
			from main_pa_employee_ratings a 
			inner join main_employees_summary b on a.employee_id = b.user_id 
			inner join main_pa_initialization c on a.pa_initialization_id=c.id 
			where a.employee_id = $employee_id and a.isactive = 1 and a.appraisal_status = 'Completed' and c.status=2
			group by c.from_year,c.to_year
			order by c.from_year desc";
			$db = Zend_Db_Table::getDefaultAdapter();
            $result = $db->query($sql_str)->fetchAll();
		}
		return $result;
	}
        
        public function getSelfAppraisalHistory($sort, $by, $pageNo, $perPage,$searchQuery)
	{
                $auth = Zend_Auth::getInstance();
                if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
                
                $where = " a.employee_id = $loginUserId and a.isactive = 1 and a.appraisal_status = 'Completed' and c.status=2 ";
		if($searchQuery) {
                   $where .= " AND ".$searchQuery;
                }   
                $appHistoryData = $this->select()
                            ->setIntegrityCheck(false)	
                            ->from(array('a'=>'main_pa_employee_ratings'),array('a.id','a.pa_initialization_id','c.status as statusval',
                                new Zend_Db_Expr("concat(c.from_year,'-',c.to_year) as fin_year"),'c.appraisal_mode',
                                ))
                            //->joinLeft(array('b' => 'main_employees_summary'),"a.employee_id = b.user_id and b.isactive=1",array('b.businessunit_name','b.department_name',new Zend_Db_Expr("case when c.appraisal_mode = 'Quarterly' then concat('Q',c.appraisal_period) when c.appraisal_mode = 'Half-yearly' then concat('H',c.appraisal_period) when c.appraisal_mode = 'Yearly' then 'Yearly' end as app_period")))
                            ->joinLeft(array('c' => 'main_pa_initialization'),"a.pa_initialization_id = c.id and c.isactive=1",array('c.from_year','c.to_year',new Zend_Db_Expr("case when c.appraisal_mode = 'Quarterly' then concat('Q',c.appraisal_period) when c.appraisal_mode = 'Half-yearly' then concat('H',c.appraisal_period) when c.appraisal_mode = 'Yearly' then 'Yearly' end as app_period")))
                            ->joinLeft(array('bu' => 'main_businessunits'),"c.businessunit_id = bu.id",array('bu.unitname'))
                            ->joinLeft(array('du' => 'main_departments'),"c.department_id = du.id",array('du.deptname'))
                            ->where($where)
                            ->order("$by $sort") 
                            ->limitPage($pageNo, $perPage);

                return $appHistoryData;
                
	}
        
        public function getTeamAppraisalHistory($sort, $by, $pageNo, $perPage,$searchQuery)
	{
                $auth = Zend_Auth::getInstance();
                if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
                
                $where = " $loginUserId in (a.line_manager_1,a.line_manager_2,a.line_manager_3,a.line_manager_4,"
                    . "a.line_manager_5) and a.isactive = 1 and c.status=2 ";
		if($searchQuery) {
                   $where .= " AND ".$searchQuery;
                }   
                $appTeamHistoryData = $this->select()
                            ->setIntegrityCheck(false)	
                            ->from(array('a'=>'main_pa_employee_ratings'),array('c.id','a.pa_initialization_id','c.status as statusval',
                                new Zend_Db_Expr("concat(c.from_year,'-',c.to_year) as fin_year"),'c.appraisal_mode',
                                ))
                            //->joinLeft(array('b' => 'main_employees_summary'),"a.employee_id = b.user_id and b.isactive=1",array('b.businessunit_name','b.department_name',new Zend_Db_Expr("case when c.appraisal_mode = 'Quarterly' then concat('Q',c.appraisal_period) when c.appraisal_mode = 'Half-yearly' then concat('H',c.appraisal_period) when c.appraisal_mode = 'Yearly' then 'Yearly' end as app_period")))
                            ->joinLeft(array('c' => 'main_pa_initialization'),"a.pa_initialization_id = c.id and c.isactive=1",array('c.from_year','c.to_year',new Zend_Db_Expr("case when c.appraisal_mode = 'Quarterly' then concat('Q',c.appraisal_period) when c.appraisal_mode = 'Half-yearly' then concat('H',c.appraisal_period) when c.appraisal_mode = 'Yearly' then 'Yearly' end as app_period")))
                            ->joinLeft(array('bu' => 'main_businessunits'),"c.businessunit_id = bu.id",array('bu.unitname'))
                            ->joinLeft(array('du' => 'main_departments'),"c.department_id = du.id",array('du.deptname'))
                            ->where($where)
                            ->group("c.id") 
                            ->order("$by $sort") 
                            ->limitPage($pageNo, $perPage);

                return $appTeamHistoryData;
                
	}
        
        public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$flag='',$b='',$c='',$d='')
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
                                    $val = htmlspecialchars($val);
                    if($key == 'fin_year')
                        $searchQuery .= " concat(from_year,'-',to_year) like '%".$val."%' AND ";
                    else if($key == 'app_period')
                        $searchQuery .= " lower(case when c.appraisal_mode = 'Quarterly' then concat('Q',c.appraisal_period) when c.appraisal_mode = 'Half-yearly' then concat('H',c.appraisal_period) when c.appraisal_mode = 'Yearly' then 'Yearly' end)  like '%".strtoupper($val)."%' AND ";
                    else                                    
                        $searchQuery .= $key." like '%".$val."%' AND ";                

                    $searchArray[$key] = $sval;
                }
                $searchQuery = rtrim($searchQuery," AND");					
            }
		if($flag=='historyself') {	
                    $objName = 'appraisalhistoryself';
                    $tablecontent = $this->getSelfAppraisalHistory($sort, $by, $pageNo, $perPage,$searchQuery);  
                }else{
                    $objName = 'appraisalhistoryteam';
                    $tablecontent = $this->getTeamAppraisalHistory($sort, $by, $pageNo, $perPage,$searchQuery);  
                }
		$tableFields = array('action'=>'Action','unitname' => 'Business Unit','deptname' => 'Department','fin_year' => 'Financial Year',
                    'appraisal_mode'=>'Appraisal Mode','app_period' => 'Period');
		
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
			'actionparam'=>'yes',
			'dashboardcall'=>$dashboardcall,
                        'search_filters' => array(
                            'appraisal_mode' => array(
                                'type' => 'select',
                                'filter_data' => array('' => 'All','Quarterly' => 'Quarterly','Half-yearly' => 'Half-yearly','Yearly' => 'Yearly',),
                            ),
                        ),
		);
		return $dataTmp;
	}
}