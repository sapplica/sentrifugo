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

class Default_Model_Appraisalquestions extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_pa_questions';
    protected $_primary = 'id';
	
    public function getAppraisalQuestionData($sort, $by, $pageNo, $perPage,$searchQuery)
    {
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {		
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
     	}
        $where = "aq.isactive = 1 and ac.isactive=1";
        if($searchQuery)
            $where .= " AND ".$searchQuery;
        if($loginuserRole != 1)
        {
            if($loginuserGroup != MANAGER_GROUP)
                $where .= " AND (aq.createdby_group in ( ".$loginuserGroup.") or aq.createdby_group is null ) ";
            else 
                $where .= " AND (aq.createdby_group in ( ".$loginuserGroup.") ) ";
        }
		
        $db = Zend_Db_Table::getDefaultAdapter();		
		
        $appQuestionsData = $this->select()
                                ->setIntegrityCheck(false)	
                                ->from(array('aq'=>'main_pa_questions'),array('aq.*'))
                                ->joinInner(array('ac'=>'main_pa_category'), 'aq.pa_category_id = ac.id', array('category_name' => 'ac.category_name'))
                                ->where($where)
                                ->order("$by $sort") 
                                ->limitPage($pageNo, $perPage);        
        return $appQuestionsData;       		
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
					if(isset($key) && $key == "category_name")
					{
						$searchQuery .= " ac.".$key." like '%".mysql_real_escape_string($val)."%' AND ";
					}
					else
					{
						$searchQuery .= " aq.".$key." like '%".mysql_real_escape_string($val)."%' AND ";
					}
					$searchArray[$key] = $val;
				}
				$searchQuery = rtrim($searchQuery," AND");					
			}
			
		$objName = 'appraisalquestions';
		
		$tableFields = array('action'=>'Action','category_name'=>'Parameter','question' => 'Question','description' => 'Description');
		
		$tablecontent = $this->getAppraisalQuestionData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
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
	
	public function getAppraisalQuestionbyID($id)
	{
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
		
		$loginuserRole = $auth->getStorage()->read()->emprole;
		$loginuserGroup = $auth->getStorage()->read()->group_id;
     	}
		$where = 'aq.isactive = 1 AND aq.id='.$id.' ';
		
            if($loginuserRole != 1)
            {
                if($loginuserGroup != MANAGER_GROUP)
                    $where .= " AND (aq.createdby_group = ".$loginuserGroup." or aq.createdby_group is null) ";
                else 
                    $where .= " AND (aq.createdby_group = ".$loginuserGroup." ) ";
            }
			
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('aq'=>'main_pa_questions'),array('aq.*'))
					    ->where($where);
		return $this->fetchAll($select)->toArray();	
	}
	
	public function getAppraisalQuestionsByCategotyID($categotyId)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('aq'=>'main_pa_questions'),array('aq.*'))
					    ->where('aq.isactive = 1 AND aq.pa_category_id='.$categotyId.' ');
		return $this->fetchAll($select)->toArray();	
	}
	
	public function UpdateAppraisalQuestionData($data)
	{
       
		$db = Zend_Db_Table::getDefaultAdapter();
	    $qry = "update main_pa_questions set isactive=0 where id in ($data)";
		$db->query($qry);
        
	}
	
	public function SaveorUpdateAppraisalQuestionData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_pa_questions');
			return $id;
		}	
	}
	
	public function checkDuplicateQuestionName($categoryId,$question)
	{
            $db = Zend_Db_Table::getDefaultAdapter();
            $qry = "select count(*) as count from main_pa_questions aq where aq.pa_category_id =".$categoryId." AND aq.question='".$question."' AND aq.isactive=1 ";
            $res = $db->query($qry)->fetchAll();
            return $res;
	}
	
        /**
         * This method will get questions by category/questions ids for HR and Manager login.
         * @param string $categoryIds = ids of question categories(comma separated)
         * @param string $qsids       = ids of questions(comma separated) 
         * @return array Array of questions.
         */
	public function getQuestionsByCategory($categoryIds,$qsids='')
	{
            $db = Zend_Db_Table::getDefaultAdapter();
            $auth = Zend_Auth::getInstance();
            $res = array();
            $loginuserGroup = '';
            if($auth->hasIdentity())
            {
                $loginuserRole = $auth->getStorage()->read()->emprole;
                $loginuserGroup = $auth->getStorage()->read()->group_id;
            }
            
            $request = Zend_Controller_Front::getInstance();
            $controllerName = $request->getRequest()->getControllerName();
            
     		if($controllerName == 'appraisalmanager')
     		{
     			$group_str = " (p.createdby_group = ".MANAGER_GROUP." OR p.createdby_group = ".MANAGEMENT_GROUP." OR  p.createdby_group IS NULL) ";
     		}else
     		{
	            if($loginuserGroup == HR_GROUP || $loginuserGroup == MANAGEMENT_GROUP || $loginuserGroup == CUSTOM_GROUP || $loginuserRole == SUPERADMINROLE)
	            {
	                $group_str = " (p.createdby_group IN (".HR_GROUP.",".MANAGEMENT_GROUP.") OR  p.createdby_group IS NULL) ";
	            }
	            else if($loginuserGroup == MANAGER_GROUP)
	            {
	                $group_str = " p.createdby_group = ".MANAGER_GROUP." ";
	            }
     		}
            $where = "p.isactive=1 and c.isactive=1 and p.module_flag = 1 
                        and p.pa_category_id IN($categoryIds) and $group_str ";
	     	
            if($qsids !='')
                $where.=" AND p.id IN($qsids)";
	     	
             $qry = "select p.id,p.pa_category_id,p.question,p.description,c.category_name from main_pa_questions p
                    inner join main_pa_category c on p.pa_category_id=c.id
                    where $where order by p.id ";
            $res = $db->query($qry)->fetchAll();            	
            return $res;
	}
	
	public function gethrquestionprivileges($appraisalid,$tablename,$groupid='')
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$options = array();
		$where = "e.pa_initialization_id = $appraisalid AND e.isactive=1 AND e.module_flag=1";
		if($groupid !='')
		{
			$where.=" AND group_id = $groupid ";
		}
		if($appraisalid !='')
		{
		 	$query = "select e.hr_qs,e.hr_group_qs_privileges from $tablename e
				    where $where order by e.id ";
            $result = $db->query($query);
            $options = $result->fetch();
		}   
            
            return $options;
	}
	
	public function getmanagerquestionprivileges($appraisalid,$tablename,$groupid='')
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$options = array();
		$where = "e.pa_initialization_id = $appraisalid AND e.isactive=1 AND e.module_flag=1";
		if($groupid !='')
		{
			$where.=" AND group_id = $groupid ";
		}
		if($appraisalid !='')
		{
		 	$query = "select e.manager_qs,e.manager_qs_privileges from $tablename e
				    where $where order by e.id ";
            $result = $db->query($query);
            $options = $result->fetch();
		}   
            
            return $options;
	}
	
	public function getGroupEmployeeCount($appraisalid,$tablename)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$options = array();
		
		if($appraisalid !='')
		{
		 	$query = "select q.group_id,q.id,count(q.employee_id) as empcount,group_concat(q.employee_id) as empids,
		 				q.hr_qs,g.group_name,CHAR_LENGTH(q.hr_qs) - CHAR_LENGTH(REPLACE(q.hr_qs, ',', '')) + 1 as qscount 
						from $tablename q
						inner join main_pa_groups g on q.group_id=g.id  and g.isactive=1
						where q.isactive=1 and q.pa_initialization_id =$appraisalid and q.module_flag=1 group by q.group_id; ";
            $result = $db->query($query);
            $options = $result->fetchAll();
		}   
            
            return $options;
	}
	
	
	public function getEmpGroupDetails($groupid='',$appraisalid,$tablename)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$options = array();
		$where = "e.isactive=1 AND t.pa_initialization_id = $appraisalid AND t.isactive=1 AND t.module_flag=1";
		if($groupid!='')
			$where.=" AND t.group_id = $groupid ";
		else
			$where.=" AND t.group_id IS NULL ";
		
		if($appraisalid !='')
		{
		 	$query = "select e.user_id,e.userfullname,e.businessunit_name,e.department_name,e.profileimg,e.employeeId,e.jobtitle_name 
						from main_employees_summary e 
						inner join $tablename t on t.employee_id=e.user_id
						where $where ";
            $result = $db->query($query);
            $options = $result->fetchAll();
		}   
            
            return $options;
	}
	
	public function getGroupCountDetails($appraisalid,$tablename)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$options = array();
		
		if($appraisalid !='')
		{
		 	$query = "select count(*) as empcount from $tablename t
		 			  inner join main_employees_summary e on e.user_id=t.employee_id	
		 			  where e.isactive=1 AND t.pa_initialization_id=$appraisalid AND t.module_flag=1  
		 			  AND t.isactive=1 AND group_id IS NULL ";
            $result = $db->query($query);
            $options = $result->fetchAll();
		}   
            
            return $options;
	}
	
	public function updatequestionprivileges($tablename,$questions, $qsprivileges,$appraisalid,$logindetails)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$group = $logindetails['loginusergroup'];
		if($logindetails['loginusergroup'] == '')
		$group = 'NULL';
		$db->query("update $tablename set hr_qs = '".$questions."',hr_group_qs_privileges = '".$qsprivileges."',modifiedby=".$logindetails['loginuserid'].",
					modifiedby_role=".$logindetails['loginuserrole'].",modifiedby_group=".$group.",modifieddate=now()  
					where pa_initialization_id = '".$appraisalid."' and module_flag = 1 and isactive =1");
	}
	
	public function updategroupqsprivileges($tablename,$groupid,$questions, $qsprivileges,$appraisalid,$empids,$logindetails)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$group = $logindetails['loginusergroup'];
		if($logindetails['loginusergroup'] == '')
		$group = 'NULL';
		$db->query("update $tablename set group_id=$groupid,hr_qs = '".$questions."',hr_group_qs_privileges = '".$qsprivileges."',modifiedby=".$logindetails['loginuserid'].",
					modifiedby_role=".$logindetails['loginuserrole'].",modifiedby_group=".$group.",modifieddate=now()  
					where pa_initialization_id = '".$appraisalid."' and employee_id IN ($empids) and module_flag = 1 and isactive =1");
	}
	
	public function removegroupqsprivileges($tablename,$appraisalid,$empid,$logindetails)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$group = $logindetails['loginusergroup'];
		if($logindetails['loginusergroup'] == '')
		$group = 'NULL';
		$db->query("update $tablename set hr_qs = NULL,hr_group_qs_privileges = NULL,group_id=NULL,modifiedby=".$logindetails['loginuserid'].",
					modifiedby_role=".$logindetails['loginuserrole'].",modifiedby_group=".$group.",modifieddate=now()  
					where pa_initialization_id = '".$appraisalid."' and employee_id = $empid and module_flag = 1 and isactive =1");
	}
	
	public function deletegroupqsprivileges($tablename,$appraisalid,$groupid,$logindetails)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$group = $logindetails['loginusergroup'];
		if($logindetails['loginusergroup'] == '')
		$group = 'NULL';
		$db->query("update $tablename set hr_qs = NULL,hr_group_qs_privileges = NULL,group_id=NULL,modifiedby=".$logindetails['loginuserid'].",
					modifiedby_role=".$logindetails['loginuserrole'].",modifiedby_group=".$group.",modifieddate=now()  
					where pa_initialization_id = $appraisalid and group_id = $groupid and module_flag = 1 and isactive =1");
	}
	
	
	public function insertQsData($appraisalid,$logindetails)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$group = $logindetails['loginusergroup'];
		if($logindetails['loginusergroup'] == '')
		$group = 'NULL';
		$db->query("insert into main_pa_questions_privileges 
					(pa_initialization_id, group_id, employee_id,hr_qs, hr_group_qs_privileges,  module_flag, line_manager_1, line_manager_2, line_manager_3, line_manager_4, line_manager_5, manager_levels, createdby, createdby_role, createdby_group, modifiedby, modifiedby_role, modifiedby_group, createddate, modifieddate, isactive)
					select pa_initialization_id,group_id,employee_id,hr_qs,hr_group_qs_privileges,module_flag,line_manager_1,line_manager_2,line_manager_3,
		 			  line_manager_4,line_manager_5,manager_levels,".$logindetails['loginuserid'].",".$logindetails['loginuserrole'].",".$group.",".$logindetails['loginuserid'].",".$logindetails['loginuserrole'].",".$group.",now(),now(),1  
		 			  from main_pa_questions_privileges_temp where pa_initialization_id=$appraisalid AND module_flag=1 
			          AND isactive=1");
	}
	
	
}