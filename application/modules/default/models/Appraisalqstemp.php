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

class Default_Model_Appraisalqstemp extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_pa_questions_privileges_temp';
    protected $_primary = 'id';
    
    	
    
    /**
     * This function is used to save/update data in database.
     * @parameters
     * @param data  =  array of form data.
     * @param where =  where condition in case of update.
     * 
     * @return {String}  Primary id when new record inserted,'update' string when a record updated.
     */
	public function getAppraisalQuestions($appraisalid)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('qp'=>'main_pa_questions_privileges_temp'),array('qp.hr_qs'))
					    ->where('qp.isactive = 1 AND qp.pa_initialization_id='.$appraisalid.' ');
		return $this->fetchAll($select)->toArray();	
	}
    public function SaveorUpdateData($data, $where)
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
    
	public function getAssignedGroupsData($appraisalid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$options = array();
		
		if($appraisalid !='')
		{
		 	$query = "select group_id,employee_id,hr_qs,hr_group_qs_privileges,line_manager_1,line_manager_2,line_manager_3,
		 			  line_manager_4,line_manager_5,manager_levels  from main_pa_questions_privileges_temp where pa_initialization_id=$appraisalid AND module_flag=1 
			          AND isactive=1 ";
            $result = $db->query($query);
            $options = $result->fetchAll();
		}   
            
            return $options;
	}
	
	public function getLine1ManagerId($appraisalid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$options = array();
		
		if($appraisalid !='')
		{
		 	$query = "select t.line_manager_1,e.userfullname,e.emailaddress from main_pa_questions_privileges_temp t
					inner join main_employees_summary e on e.user_id = t.line_manager_1
					where t.pa_initialization_id=$appraisalid AND t.module_flag=1 
					AND t.isactive=1 group by line_manager_1";
            $result = $db->query($query);
            $options = $result->fetchAll();
		}   
            
            return $options;
	}
	
	public function updateQsTempData($appraisalid,$logindetails)
	{
		$group = $logindetails['loginusergroup'];
		if($logindetails['loginusergroup'] == '')
		$group = 'NULL';
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->query("update main_pa_questions_privileges_temp set isactive=0,modifiedby=".$logindetails['loginuserid'].",
					modifiedby_role=".$logindetails['loginuserrole'].",modifiedby_group=".$group.",modifieddate=now()  
					where pa_initialization_id = $appraisalid and module_flag = 1");
	}
	
	public function getTempEmployeeIds($appraisalid,$line1id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$options = array();
		
		if($appraisalid !='' && $line1id!='')
		{
		 	$query = "select group_concat(t.employee_id) empids from main_pa_questions_privileges_temp t
					where t.pa_initialization_id=$appraisalid AND t.module_flag=1 
					AND t.line_manager_1=$line1id AND t.isactive=1 group by line_manager_1";
            $result = $db->query($query);
            $options = $result->fetchAll();
		}   
            
            return $options;
	}
    
    
}//end of class