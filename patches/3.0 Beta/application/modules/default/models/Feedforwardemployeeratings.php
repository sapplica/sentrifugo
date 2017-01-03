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

class Default_Model_Feedforwardemployeeratings extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_pa_ff_employee_ratings';
    protected $_primary = 'id';
	
	public function SaveorUpdateFFEmpRatingsData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_pa_ff_employee_ratings');
			return $id;
		}	
	}   

	public function getFFDataByEmpID($employeeId)
	{
		$where = "aer.isactive = 1 AND ai.status = 1 AND aer.employee_id = ".$employeeId;
		$select = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('aer'=>'main_pa_ff_employee_ratings'),array('aer.id','aer.ff_initialization_id','aer.additional_comments',
                           		'aer.manager_id','aer.employee_id','aer.employee_response','aer.ff_status','es.businessunit_name','es.profileimg',
                           		'es.userfullname','es.employeeId','es.jobtitle_name','es.department_name','ai.status','ai.pa_configured_id','ai.appraisal_id',
                           		'ai.ff_mode','ai.ff_period','ai.ff_from_year','ai.ff_to_year','ai.ff_due_date','ai.questions','ai.qs_privileges',
                           		))
                           ->joinInner(array('es'=>'main_employees_summary'), 'es.user_id = aer.manager_id AND es.isactive = 1', array())
                           ->joinInner(array('ai'=>'main_pa_ff_initialization'), 'ai.id = aer.ff_initialization_id AND ai.isactive = 1', array())
                           ->where($where);
                           
		return $this->fetchAll($select)->toArray();       		
	}
	
	public function getFFQuesDataByIDs($ques_ids)
	{
		$select = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('aq'=>'main_pa_questions'),array('aq.id','aq.pa_category_id','aq.question','aq.description'))
                           ->where("aq.isactive = 1 AND aq.module_flag = 2 AND aq.id in (".$ques_ids.")");
                           
		return $this->fetchAll($select)->toArray();       		
	}
	
	public function getAppRatingsDataByConfgId($config_id)
	{
		$select = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('ar'=>'main_pa_ratings'),array('ar.id','ar.rating_type','ar.rating_value','ar.rating_text'))
                           ->where("ar.isactive = 1 AND ar.pa_initialization_id = ".$config_id);
		                           
		return $this->fetchAll($select)->toArray();       		
	}
	
	public function getEmpsFFStatus($ffId,$flag='')
	{
		$where = '';
		if($flag)
			$where = ' AND fe.ff_status = 1 ';
            $select = $this->select()
                            ->setIntegrityCheck(false)
                            ->from(array('fe'=>'main_pa_ff_employee_ratings'),array('fe.ff_status'))
                            ->joinInner(array('es'=>'main_employees_summary'), 'es.user_id = fe.employee_id AND es.isactive = 1', 
                            	array('es.user_id','es.userfullname', 'es.employeeId', 'es.jobtitle_name', 'es.reporting_manager_name', 'es.department_name', 'es.businessunit_name', 'es.profileimg','es.emailaddress'))
                            ->where('fe.isactive = 1 AND fe.ff_initialization_id in( '.$ffId.')'.$where)
                            ->order("fe.ff_status");
            return $this->fetchAll($select)->toArray();		
	}
	
	public function getfeedforwardstatus($ffId,$status='')
	{
		$where = '';
		if($status)
			$where = ' AND fe.ff_status = '.$status.' ';
			
            $select = $this->select()
                            ->setIntegrityCheck(false)
                            ->from(array('fe'=>'main_pa_ff_employee_ratings'),array('fe.ff_status'))
                            ->joinInner(array('es'=>'main_employees_summary'), 'es.user_id = fe.employee_id AND es.isactive = 1', 
                            	array('es.user_id','es.userfullname', 'es.employeeId', 'es.jobtitle_name', 'es.reporting_manager_name', 'es.department_name', 'es.businessunit_name', 'es.profileimg','es.emailaddress'))
                            ->where('fe.isactive = 1 AND fe.ff_initialization_id = '.$ffId.$where)
                            ->order("fe.ff_status");
            return $this->fetchAll($select)->toArray();		
	}
}