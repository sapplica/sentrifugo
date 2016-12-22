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

class Default_Model_Shortlistedcandidates extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_candidatedetails';
    protected $_primary = 'id';
	
	public function getShortlistedData($sort, $by, $pageNo, $perPage,$searchQuery,$filter='')
	{
		$where = "c.isactive = 1 AND (c.cand_status = 1 OR c.cand_status = 2 OR c.cand_status = 3) AND m.interview_status = 2";
		
		if($filter !='')
			{
			   if($filter == 'Selected'){
				 $where .=" AND c.cand_status = 2 ";
			   }else if($filter == 'Shortlisted'){
				 $where .=" AND c.cand_status = 1 ";
			   }else if($filter == 'Rejected'){
				 $where .=" AND c.cand_status = 3 ";
			   }			   
			   else $where .= " AND c.cand_status in (2,1,3) ";
			}else{
				$where .= " AND c.cand_status in (2,1,3) ";
			}		
		if($searchQuery)
            $where .= " AND ".$searchQuery; 
		 $shortlistedData = $this->select()
                        ->setIntegrityCheck(false)
                        ->from(array('c'=>$this->_name),array('id'=>'c.id','cand_status'=>'c.cand_status','candidate_name'=>'c.candidate_name','emailid'=>'c.emailid','contact_number'=>'c.contact_number'))
                        ->joinInner(array('r'=>'main_requisition'), "r.id = c.requisition_id and r.isactive = 1",array('requisition_code'=>'r.requisition_code'))
                        ->joinLeft(array('p'=>'main_positions'), "p.id = r.position_id",array('positionname'=>'p.positionname'))
                        ->joinLeft(array('j'=>'main_jobtitles'), "j.id = r.jobtitle",array('jobtitlename'=>'j.jobtitlename'))
                        ->joinInner(array('m'=>'main_interviewdetails'), "m.candidate_id = c.id and m.isactive = 1",array('interview_status'=>'m.interview_status'))
                        ->where($where)
                        ->order("$by $sort") 
                        ->limitPage($pageNo, $perPage); 
		
        return $shortlistedData;       		
	}
	//function to get count of shortlisted,selected ,rejected candidates
	public function getEmployeeCount($flag)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		
		if($flag=='Shortlisted')
		{
			$where = "c.isactive = 1  AND  c.cand_status in (1) AND c.requisition_id!=0";
		}
		else if($flag=='Selected')
		{
			$where = "c.isactive = 1  AND  c.cand_status in (2) AND c.requisition_id!=0";
		}
		else if($flag=='Rejected')
		{
			$where = "c.isactive = 1  AND  c.cand_status in (3) AND c.requisition_id!=0";
		}
		else{
			$where = "c.isactive = 1 AND c.cand_status in (2,1,3) AND c.requisition_id!=0";
		}
		
		$candidateData = $this->select()
		->setIntegrityCheck(false)
		->from(array('c'=>$this->_name), array("count"=>"count(*)"))
		->where($where);
		return $db->fetchRow($candidateData);;
		
	}
	
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$queryflag,$statusidstring,$formgrid,$a='')
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
		$objName = 'shortlistedcandidates';

        $tableFields = array('action'=>'Action',
                             'requisition_code'=>'Requisition Code',
							 'jobtitlename'=>'Job Title',
							 'candidate_name' => 'Candidate Name',
                             'emailid' => 'Email',
                             'contact_number' => 'Contact Number',
							 'cand_status' => 'Status'
                            );
	   $tablecontent = $this->getShortlistedData($sort, $by, $pageNo, $perPage,$searchQuery,$queryflag);     
	   
	   $search_filters = array();
	   
            $search_filters = array(
                'cand_status' => array(
                    'type' => 'select',
                    'filter_data' => array('' => 'All','Selected' => 'Selected','Shortlisted' => 'Shortlisted','Rejected'=>'Rejected'),
                ),
            );
      
	  
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
                'add' =>'add',
                'searchArray' => $searchArray,
                'menuName'=>'Shortlisted & Selected Candidates',
                'formgrid' => $formgrid,
                'unitId'=>$statusidstring,
                'call'=>$call,
                'search_filters' => $search_filters,
        );			
		return $dataTmp;
	}
	
	public function getcandidateData($id)
	{
		$row = $this->fetchRow("id = '".$id."'");
		if (!$row) {
			throw new Exception("Could not find row $id");
		}
		return $row->toArray();
	}
	
	public function getinterviewData($reqid,$candid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();	
		$intervwData = $db->query("select * from main_interviewdetails where isactive = 1 AND req_id = ".$reqid." AND candidate_id = ".$candid.";");
		return $data = $intervwData->fetch();
	}
	
	public function getinterviewrounds($intrid,$reqid,$candid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();	
		
		$intervwData = $db->query("select *,u.userfullname as interviewer,ct.city,s.state,c.country from main_interviewrounddetails i 
									left join main_users u on u.id = i.interviewer_id
									left join main_countries c on c.country_id_org = i.int_country
									left join main_states s on s.state_id_org = i.int_state
									left join main_cities ct on ct.city_org_id = i.int_city
									where i.isactive = 1 AND i.interview_id = ".$intrid." AND i.req_id = ".$reqid." 
									AND i.candidate_id = ".$candid.";");
		return $data = $intervwData->fetchAll();
	}
	
	 public function SaveorUpdateCandidateDetails($data, $where)
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
    
}