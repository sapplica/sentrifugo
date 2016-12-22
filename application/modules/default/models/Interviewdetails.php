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

class Default_Model_Interviewdetails extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_interviewdetails';
    protected $_primary = 'id';
    
    /**
     * This function gives data for grid view.
     * @parameters
     * @param $sort          = ascending or descending
     * @param $by            = name of field which to be sort  
     * @param $pageNo        = page number
     * @param $perPage       = no.of records per page
     * @param $searchQuery   = search string
     * 
     * @return  ResultSet;
     */
    public function getCandidatesData($sort, $by, $pageNo, $perPage,$searchQuery)
    {
        $where = "c.isactive = 1 and c.interview_status not in ('Completed','Requisition Closed/Completed') and r.req_status not in ('On hold')";

        if($searchQuery)
            $where .= " AND ".$searchQuery;
        $auth = Zend_Auth::getInstance();
        $session = $auth->getStorage()->read();
        $loginUserId = $session->id;
        $loginGroupid = $session->group_id;
                
        $roleData = $this->select()
                        ->setIntegrityCheck(false)
                        ->from(array('c'=>$this->_name),array('c.*',))
                        ->joinInner(array('r'=>'main_requisition'), "r.id = c.req_id and r.isactive = 1",array('requisition_code'=>'r.requisition_code'))
                        ->joinInner(array('d'=>'main_candidatedetails'), "d.id = c.candidate_id and d.isactive = 1",array('candidate_name'=>'d.candidate_name','emailid'=>'d.emailid','contact_number'=>'d.contact_number','cand_status'=>'d.cand_status'))                        
                        ->joinLeft(array('j'=>'main_jobtitles'), "j.id = r.jobtitle",array('jobtitlename'=>'j.jobtitlename'))
                        
                        ->order("$by $sort") 
                        ->limitPage($pageNo, $perPage); 
        if($loginGroupid == MANAGER_GROUP || $loginGroupid == EMPLOYEE_GROUP || $loginGroupid == SYSTEMADMIN_GROUP)//for manager login
        {
            $roleData = $roleData->joinInner(array('ir' => 'main_interviewrounddetails'),"ir.isactive = 1 and ir.interview_id = c.id and ir.interviewer_id = ".$loginUserId,array('interviewer_id' => 'ir.interviewer_id'));
            $roleData = $roleData->group('d.id');
			
			$where .= " and c.interview_status not in ('On hold')";
        }
		$roleData = $roleData->where($where);
        
		return $roleData;       		
    }
 
    public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$a='',$b='',$c='',$d='')
	{
		$searchQuery = '';
        $searchArray = array();
        $data = array();
		if($searchData != '' && $searchData!='undefined')
		{
			$searchValues = json_decode($searchData);
			if(count($searchValues) >0)
			{
				foreach($searchValues as $key => $val)
				{
					$searchQuery .= " ".$key." like '%".$val."%' AND ";
					$searchArray[$key] = $val;
				}
				$searchQuery = rtrim($searchQuery," AND");					
			}
		}
		$objName = 'scheduleinterviews';

        $tableFields = array('action'=>'Action',
                             'requisition_code' => 'Requisition Code',
                             'jobtitlename' => 'Job Title',
                             'candidate_name' => 'Candidate Name',
                             'emailid' => 'Email',
                             'contact_number' => 'Mobile',
                             'interview_status' => 'Status',
                            );
		$tablecontent = $this->getCandidatesData($sort, $by, $pageNo, $perPage,$searchQuery);     
		$inter_arr = array( ''=>'All','In process' => 'In process','Completed' => 'Complete',
                            'On hold' => 'On hold','Requisition Closed/Completed' => 'Requisition Closed/Completed');
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
                'add' => 'add',
                'menuName' => 'Interviews',
                'call'=>$call,
                'search_filters' => array(
                    'interview_status' => array(
                        'type' => 'select',
                        'filter_data' => $inter_arr,
                    ),
                ),
        );	
		return $dataTmp;
	}
	
    /**
     * This function is used to save/update data in database.
     * @parameters
     * @param Array $data  =  array of form data.
     * @param String $where =  where condition in case of update.
     * 
     * @return  String Primary id when new record inserted,'update' string when a record updated.
     */
    public function SaveorUpdateInterviewData($data, $where)
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
    /**
     * This function is used to get table name.
     * @return String Name of table.
     */
    public function getTableName()
    {
        return $this->_name;
    }
  
    
	 public function getReqByintrvwID($intrvwid)
	 {
		$db = Zend_Db_Table::getDefaultAdapter();		
        $query = "select * from main_interviewdetails 
                  where isactive = 1 and id = ".$intrvwid.";";
        $result = $db->query($query);
        $row = $result->fetch();
        return $row;
	 }
	
	public function getCandidateDetailsById($intId)
	{
		$db = Zend_Db_Table::getDefaultAdapter();		
         $query = "select c.*,r.requisition_code,r.req_status,r.department_id as deptid,r.jobtitle as jobtitle,i.req_id,i.interview_status,i.candidate_id  from main_candidatedetails c
					inner join main_interviewdetails i on c.id=i.candidate_id
					inner join main_requisition r on r.id = i.req_id
					where i.id=".$intId." and r.isactive = 1 and c.isactive = 1 and i.isactive = 1";
        
        $result = $db->query($query);
        $row = $result->fetch();
        return $row;
	}
	
	public function getSingleInterviewData($id)
	{
		$row = $this->fetchRow("id = '".$id."' and isactive = 1");
		if (!$row) {
			throw new Exception("Could not find row $id");
		}
		return $row->toArray();
	}       
        public function getInterviewDataByCandidateId($cand_id)
	{
		$row = $this->fetchRow("candidate_id = '".$cand_id."' and isactive = 1");
		if (!$row) {
			
                    return array();
		}
                else 
		return $row->toArray();
	}
	public function getinterviewroundnumber($id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();		
        $query = "select interview_round_number,round_status from main_interviewrounddetails 
					where isactive = 1 and interview_id = ".$id." order by interview_round_number desc limit 1;";
        $result = $db->query($query);
        $row = $result->fetch();
        return $row;
	}
	
    public function getCandidateInInterviewProcess($candidateid)
	 {
		$db = Zend_Db_Table::getDefaultAdapter();		
        $query = "select count(*) cnt from main_interviewdetails 
                  where isactive = 1 and candidate_id = ".$candidateid.";";
        $result = $db->query($query);
        $row = $result->fetch();
        return $row['cnt'];
	 }
	 
    public function getCandidateIdById($intId)
	{
		$db = Zend_Db_Table::getDefaultAdapter();		
         $query = "select i.candidate_id candid from main_interviewdetails i where i.id=".$intId." and i.isactive = 1";
       
        $result = $db->query($query);
        $row = $result->fetch();
        return $row['candid'];
	}
	 
}//end of class