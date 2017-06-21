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

class Default_Model_Appraisalqsmain extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_pa_questions_privileges';
    protected $_primary = 'id';
    
    	
    
    /**
     * This function is used to save/update data in database.
     * @parameters
     * @param data  =  array of form data.
     * @param where =  where condition in case of update.
     * 
     * @return {String}  Primary id when new record inserted,'update' string when a record updated.
     */
    public function SaveorUpdatePrivilegeData($data, $where)
    {
        if($where != '')
        {
            $this->update($data, $where);
            return 'update';
        }
        else 
        {
            $this->insert($data);
            $id=$this->getAdapter()->lastInsertId($this->_name);
            return $id;
        }
    }
    
    public function getManagerIDs($appraisalid)
    {
    	$result = array();
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select line_manager_1 as mgr from main_pa_questions_privileges where pa_initialization_id = $appraisalid and isactive=1 group by line_manager_1";
        $result = $db->query($query)->fetchAll();
        return $result;
    }
    
	public function getemployeeIDs($appraisalid)
    {
    	$result = array();
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select employee_id,line_manager_1,line_manager_2,line_manager_3,line_manager_4,line_manager_5 from main_pa_questions_privileges where pa_initialization_id = $appraisalid and isactive=1 and module_flag=1";
        $result = $db->query($query)->fetchAll();
        return $result;
    }
    
	public function getLine1ManagerIdMain($appraisalid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$options = array();
		
		if($appraisalid !='')
		{
		 	$query = "select t.line_manager_1,e.userfullname,e.emailaddress from main_pa_questions_privileges t
					inner join main_employees_summary e on e.user_id = t.line_manager_1
					where t.pa_initialization_id=$appraisalid AND t.module_flag=1 
					AND t.isactive=1 group by line_manager_1";
            $result = $db->query($query);
            $options = $result->fetchAll();
		}   
            
            return $options;
	}
	
	public function getAllManagerIds($appraisalid,$employeeid)
    {
    	$result = array();
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select line_manager_1,line_manager_2,line_manager_3,line_manager_4,line_manager_5,manager_levels from main_pa_questions_privileges where pa_initialization_id = $appraisalid and employee_id = $employeeid and isactive=1 and module_flag=1";
        $result = $db->query($query)->fetchAll();
        return $result;
    }
    
	public function getAppraisalQuestionsMain($appraisalid)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('qp'=>'main_pa_questions_privileges'),array('qp.manager_qs'))
					    ->where('qp.isactive = 1 AND qp.pa_initialization_id='.$appraisalid.' ');
		return $this->fetchAll($select)->toArray();	
	}
   
	public function getManagerDetailsByIds($employeeid,$mgrStr)
    {
    	$result = array();
    	$db = Zend_Db_Table::getDefaultAdapter();
        $query = "select user_id,userfullname,employeeId,profileimg,emailaddress from main_employees_summary where user_id in($employeeid,$mgrStr) and isactive=1;";
        $result = $db->query($query)->fetchAll();
        return $result;
    }
	
    
    
}//end of class