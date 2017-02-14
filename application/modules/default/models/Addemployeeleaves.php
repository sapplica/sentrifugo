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

class Default_Model_Addemployeeleaves extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_employeeleaves';
    protected $_primary = 'id';		

	/*
	   I. This query fetches employees data based on roles.
	*/
    public function getEmployeesData($sort,$by,$pageNo,$perPage,$searchQuery,$managerid='',$loginUserId)
    {
        //the below code is used to get data of employees from summary table.
        $employeesData="";                             
        $where = "  e.isactive = 1 AND e.user_id != ".$loginUserId." 
        			and r.group_id NOT IN (".MANAGEMENT_GROUP.",".USERS_GROUP.")
        			AND el.alloted_year = year(now()) ";  
       
        if($searchQuery != '')
            $where .= " AND ".$searchQuery;

        $employeesData = $this->select()
                                ->setIntegrityCheck(false)	                                
                                ->from(array('e' => 'main_employees_summary'),array('id'=>'e.user_id','e.firstname','e.lastname','e.employeeId'))
                                ->joinLeft(array('r'=>'main_roles'), 'e.emprole=r.id',array())  
                                ->joinLeft(array('el'=>'main_employeeleaves'), 'el.user_id=e.user_id',array('el.emp_leave_limit','el.used_leaves','el.alloted_year','el.createddate','el.isleavetrasnferset','remainingleaves'=>new Zend_Db_Expr('el.emp_leave_limit - el.used_leaves')))                                        
                                ->where($where)
                                ->order("$by $sort") 
                                ->limitPage($pageNo, $perPage);
        return $employeesData;       		
    }
	
    public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$exParam1='',$exParam2='',$exParam3='',$exParam4='')
    {		
        $searchQuery = '';
        $tablecontent = '';
        $emptyroles=0;
        $empstatus_opt = array();
        $searchArray = array();
        $data = array();
        $id='';
        $dataTmp = array();
		
        if($searchData != '' && $searchData!='undefined')
        {
            $searchValues = json_decode($searchData);
			
            foreach($searchValues as $key => $val)
            {				
                $searchQuery .= $key." like '%".$val."%' AND ";				
                $searchArray[$key] = $val;
            }
            $searchQuery = rtrim($searchQuery," AND");					
        }
        $objName = 'addemployeeleaves';
				        
			
        $tableFields = array('action'=>'Action','firstname'=>'First Name','lastname'=>'Last Name',
                             'employeeId' =>'Employee ID','emp_leave_limit'=>'Allotted Leave Limit',
                             'used_leaves'=>'Used Leaves','remainingleaves'=>'Leave Balance','alloted_year'=>'Allotted Year');
		   
        $tablecontent = $this->getEmployeesData($sort,$by,$pageNo,$perPage,$searchQuery,'',$exParam1);  
			
        if($tablecontent == "emptyroles")
        {
            $emptyroles=1;
        }
		
        $dataTmp = array(
                        'userid'=>$id,
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
                        'menuName' => 'Employees',
                        'dashboardcall'=>$dashboardcall,
                        'add'=>'add',
                        'call'=>$call,
                        'emptyroles'=>$emptyroles
                    );	
				
        return $dataTmp;
    }
    
	public function getMultipleEmployees($dept_id)
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
            if($dept_id != '' && $loginUserId!='')
            {
                $select = $this->select()
                            ->setIntegrityCheck(false)
                            ->from(array('e' => 'main_employees_summary'),array('e.id','e.user_id','e.userfullname','e.firstname','e.lastname','e.employeeId','e.department_id'))
                            ->joinLeft(array('r'=>'main_roles'), 'e.emprole=r.id')
                            ->joinLeft(array('el'=>'main_employeeleaves'), 'el.user_id=e.user_id',array('el.emp_leave_limit','el.used_leaves','el.alloted_year','el.createddate','el.isleavetrasnferset'))
                            ->where('e.isactive = 1 and e.department_id in ('.$dept_id.') and e.user_id!='.$loginUserId.' and r.group_id NOT IN ('.MANAGEMENT_GROUP.','.USERS_GROUP.')')
							->group('e.user_id')
                            ->order('e.userfullname');

                return $this->fetchAll($select)->toArray();	
            }
            else 
                return array();
	}
	
}
?>