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

class Default_Model_Candidatedetails extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_candidatedetails';
    protected $_primary = 'id';
    
    /**
     * This function gives data for grid view.
     * @parameters
     * @param $sort_order          = ascending or descending
     * @param $sort_field            = name of field which to be sort  
     * @param $pageNo        = page number
     * @param $perPage       = no.of records per page
     * @param $searchQuery   = search string
     * 
     * @return  ResultSet;
     */
    public function getCandidatesData($sort_order, $sort_field, $pageNo, $perPage,$searchQuery)
    {
        $where = "c.isactive = 1 and c.cand_status != 'Recruited'";

        if($searchQuery){
            $where .= " AND ".$searchQuery;       
        }
                        
        $roleData =  $this->select()
                        ->setIntegrityCheck(false)
                        ->from(array('c'=>$this->_name),
                        		array(
                        			'id'=>'c.id',
                        			'candidate_name'=>'c.candidate_name',
                        			//'candidate_lastname'=>'c.candidate_lastname', 
				                    'emailid'=>'c.emailid', 
				                    'cand_resume'=>'c.cand_resume',
				                    'cand_status'=>'c.cand_status', 
				                    'contact_number'=>'c.contact_number', 
				                    'skillset'=>'c.skillset'  ,  
                        				
                        		)
                        	)
                        ->joinLeft(array('r'=>'main_requisition_summary'), "r.req_id = c.requisition_id and r.isactive = 1",
                        		array(
                        			'requisition_code'=>'if(c.requisition_id != 0,r.requisition_code,"Not Applicable")',
									'jobtitle_name'=>'r.jobtitle_name'
                        		)
                        	)
                        	
                        	
                        	->joinLeft(array('y'=>'main_users'), "y.id = c.createdby and y.isactive = 1",
                        			array(
                        					'userfullname' => 'y.userfullname'
                        					
                        			)
                        			)
                        			
                        			
                        			
                        ->where($where)
                        ->order("$sort_field $sort_order") 
                        ->limitPage($pageNo, $perPage);    
        return $roleData;       		
    }
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
    public function getCandidatesData_schedule($sort, $by, $pageNo, $perPage,$searchQuery)
    {
        $where = "c.isactive = 1 and c.cand_status = 'Scheduled'";

        if($searchQuery)
            $where .= " AND ".$searchQuery;       

        $roleData = $this->select()
                        ->setIntegrityCheck(false)
                        ->from(array('c'=>$this->_name),array('c.*',))
                        ->joinInner(array('r'=>'main_requisition'), "r.id = c.requisition_id and r.isactive = 1",array('requisition_code'=>'r.requisition_code'))
                        ->where($where)
                        ->order("$by $sort") 
                        ->limitPage($pageNo, $perPage);        
        return $roleData;       		
    }
    
    /**
     * This function gives data for grid view.
     * @parameters
     * @param $sort          = ascending or descending
     * @param $by            = name of field which to be sort  
     * @param $pageNo        = page number
     * @param $perPage       = no.of records per page
     * @param $searchQuery   = search string
     * @param $req_id        = requisition id.
     * 
     * @return  ResultSet;
     */
    public function getCandidatesData_requisition($sort, $by, $pageNo, $perPage,$searchQuery,$req_id)
    {
        $where = "c.isactive = 1 and c.requisition_id = ".$req_id;

        if($searchQuery)
            $where .= " AND ".$searchQuery;       

        $roleData = $this->select()
                        ->setIntegrityCheck(false)
                        ->from(array('c'=>$this->_name),array('c.*',))
                        
                        ->where($where)
                        ->order("$by $sort") 
                        ->limitPage($pageNo, $perPage);        
        return $roleData;       		
    }
    
    /**
     * This function is used to save/update data in database.
     * @parameters
     * @param Array $data  =  array of form data.
     * @param String $where =  where condition in case of update.
     * 
     * @return  Primary id when new record inserted,'update' string when a record updated.
     */
    public function SaveorUpdateCandidateData($data, $where='')
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
    /**
     * This function gives array of values for drop down.
     * @return Array Array of candidate's name,id 
     */
    public function getCandidatesNamesForUsers()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select c.id,c.candidate_name,c.emailid from main_candidatedetails c 
                    where c.isactive = 1 and c.cand_status = 'Selected' and c.backgroundchk_status in ('Yet to start','Completed') and c.id not in (select rccandidatename from main_users 
                    where isactive = 1 and rccandidatename is not null)";
        $result = $db->query($query);
        $data = array();
        while($row = $result->fetch())
        {
            
            $data[$row['id']] = $row['candidate_name'];
            
        }
        return $data;
    }
    
    public function getCandidateForView($id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select cd.id rec_id,cd.*,r.requisition_code,r.jobtitle_name,ct.city_name city_name,c.country_name country_name,
                  s.state_name state_name from main_candidatedetails cd inner join main_requisition_summary r on 
                  cd.requisition_id = r.req_id and r.isactive = 1 left join tbl_countries c on c.id = cd.country 
                   left join tbl_states s on s.id = cd.state  left join tbl_cities ct on ct.id = cd.city  where cd.isactive = 1 and cd.id = ".$id." ";
        $result = $db->query($query);
        $row = $result->fetch();
        return $row;
    }
    public function getViewforNotapplicableCandidates($id)
	{
		  $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select cd.id rec_id,cd.*,ct.city_name city_name,c.country_name country_name,
                  s.state_name state_name from main_candidatedetails cd
                  left join tbl_countries c on c.id = cd.country 
                   left join tbl_states s on s.id = cd.state  left join tbl_cities ct on ct.id = cd.city  where cd.isactive = 1 and cd.id = ".$id." ";
        $result = $db->query($query);
        $row = $result->fetch();
        return $row;
	}
    /**
     * This function is used to get candidate details by its Id.
     * @param Integer $cand_id  = id of candidate.
     * @return Array Array of candidate details.
     */
    public function getCandidateById($cand_id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select cd.*,r.requisition_code,r.id req_id from main_candidatedetails cd,main_requisition r 
                  where cd.isactive = 1 and cd.id = ".$cand_id." and cd.requisition_id = r.id 
                  and r.isactive = 1";
        $result = $db->query($query);
        $row = $result->fetch();
        return $row;
    }
	
	public function getcandidateData($id)
	{
		$row = $this->fetchRow("id = '".$id."' and isactive = 1");
        if (!$row) 
        {
            throw new Exception("Could  not find row $id");
        }
        return $row->toArray();
	}
	
	public function getnotscheduledcandidateData($req_id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
        $query = "select id,candidate_name from main_candidatedetails where cand_status = 'Not Scheduled' and( requisition_id = ".$req_id." or requisition_id=0) and isactive = 1 order by candidate_name;";
        $result = $db->query($query);
        $row = $result->fetchAll();
        return $row;
	}	
	
	public function getcountofrecords($req_id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
        $query = "select id,candidate_name from main_candidatedetails where requisition_id = ".$req_id.";";
        $result = $db->query($query);
        $row = $result->fetchAll();
        return $row;
	}

		
	public function SaveorUpdateUserData($data, $where)
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
     * This function gives all content for grid view.
     * @parameters
     * @param $sort          = ascending or descending
     * @param $by            = name of field which to be sort  
     * @param $pageNo        = page number
     * @param $perPage       = no.of records per page
     * @param $searchData    = search string
     * @param $call          = type of call like ajax. 
     * @return  Array;
     */
    public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$p1,$p2,$p3,$p4)
    {
        $searchQuery = '';
        $searchArray = array();
        $data = array();
        $db = Zend_Db_Table::getDefaultAdapter();
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
        
        $objName = 'candidatedetails';

        $tableFields = array('action'=>'Action',
                             'requisition_code' => 'Requisition Code',
							 'jobtitle_name' => 'Job Title',
                             'candidate_name' => 'Candidate Name',
        					// 'candidate_lastname' => 'Candidate Last Name',
        		            'cand_status' => 'Status',
                            // 'emailid' => 'Email',
        					 'cand_resume' => 'Resume',
							 
                             'contact_number' => 'Mobile',
                             'skillset' => 'Skill Set', 
        		              'userfullname'=>'Added By'
                            );

        $tablecontent = $this->getCandidatesData($sort, $by, $pageNo, $perPage,$searchQuery);     
        $cand_status_opt = array('' => 'All','Shortlisted' => 'Shortlisted','Selected' => 'Selected','Rejected' => 'Rejected',
                                'On hold' => 'On hold','Disqualified' => 'Disqualified','Scheduled' => 'Scheduled',
                                'Not Scheduled' => 'Not Scheduled','Recruited' => 'Recruited','Requisition Closed/Completed' => 'Requisition Closed/Completed');
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
                'call'=>$call,
				'dashboardcall'=>$dashboardcall,
                'search_filters' => array(
                    'cand_status' => array(
                        'type' => 'select',
                        'filter_data' => $cand_status_opt,
                    ),
                ),
        );
        return $dataTmp;
    }
    
    // To get content of word document
    public function read_file_docx($filename){
		
		$striped_content = '';
		$content = '';
		
		if(!$filename || !file_exists($filename)) return false;
		
		$zip = zip_open($filename);
		
		if (!$zip || is_numeric($zip)) return false;
	
	
		while ($zip_entry = zip_read($zip)) {
			
			if (zip_entry_open($zip, $zip_entry) == FALSE) continue;
			
			if (zip_entry_name($zip_entry) != "word/document.xml") continue;

			$content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
			
			zip_entry_close($zip_entry);
		}// end while
	
		zip_close($zip);
		
			
		
		$content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $content);
		$content = str_replace('</w:r></w:p>', "\r\n", $content);
		$striped_content = strip_tags($content);

		return $striped_content;
	}
	
    public function getContentWord($filename=null){
		$content = read_file_docx($filename);
		if($content !== false){
			echo nl2br($content);	
		}else{
			echo 'Couldn\'t get the file. Please check that file.';
		}
    }
    
    // To save uploaded file contents
    public function saveUploadedFile($files=array()){
    
		$max_size = 1024;			// maxim size for image file, in KiloBytes

		// Allowed image types
		$allowtype = array('doc', 'docx', 'pdf');

		/** Uploading the image **/

		$rezultat = '';
		$result_status = 'error';
		$result_msg = '';
		// if is received a valid file
		
		if (isset ($files['resume-file'])) {
		  // checks to have the allowed extension
		  $type = explode(".", strtolower($files['resume-file']['name']));
		  $ext = array_pop($type);
		  $file_name = implode('.', $type);
		  if(in_array($ext, $allowtype)){
			// check its size
			if ($files['resume-file']['size']<=$max_size*1024) {
			  // if no errors
			  if ($files['resume-file']['error'] == 0) {
			  	$date = new DateTime();
				$timestamp = $date->getTimestamp();
			  
			  	$newname = uniqid('resume_').'_'.$timestamp.'.'.$ext;
			  	
			  	// Folder to upload resumes
				$newfilename = UPLOAD_PATH_RESUMES . "/" . $newname;  

				// Check file permissions
				$permissions = substr(sprintf('%o', fileperms(UPLOAD_PATH_RESUMES)), -3);
				
				/* if($permissions!=777){
					$result_msg = "Failed to upload the file";
					// To log warning in the logs/application.log
					move_uploaded_file($files['resume-file']['tmp_name'], $newfilename);
				}else */
				if (!move_uploaded_file ($files['resume-file']['tmp_name'], $newfilename)) {
				  
				  $result_msg = "Failed to upload the file"; // To show error in one line, the above error message was replaced to this one.
				}else{
			      $rezultat = $newname;
				  $result_status = 'success';
				}
			  }
			}else{ 
				
				$result_msg = 'Invalid file'; // To show error in one line, the above error message was replaced to this one.
			}
		  }
		  else 
		  { 
			
			$result_msg = 'Invalid file'; // To show error in one line, the above error message was replaced to this one.
			
		  }
		}
		else 
		  { 
			
			$result_msg = 'Failed to upload the file'; // To show error in one line, the above error message was replaced to this one.
			
		  }

		$result = array(
			'result'=>$result_status,
			'file_name'=>$rezultat,
			'msg'=>$result_msg
		);
		return $result;
    }
    
    public function insertMultipleRecords($fields=array(), $records=array()){
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$query = 'INSERT INTO main_candidatedetails ('.implode(',', $fields).') VALUES '.$records;
    	$db->query($query);
        $id=$this->getAdapter()->lastInsertId($this->_name);
        return $id;
    }
    
    // To get login user ID
    public function getLoginUserId(){
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            return $loginUserId = $auth->getStorage()->read()->id;
        }
    }

	// To generate report
	public function getReportData($param_arr,$per_page,$page_no,$sort_name,$sort_type){
		
            $search_str = "c.isactive = 1";
			
            unset($param_arr['search_criteria']);
			unset($param_arr['previous_search']);
			if(!empty($param_arr)){
            
	            foreach($param_arr as $key => $value)
	            {
	                    if($value != '')
	                    {
	                            if($key == 'date_of_joining')
	                            $search_str .= " and ".$key." = '".sapp_Global::change_date ($value,'database')."'";				
	                            else
	                            $search_str .= " and ".$key." = '".$value."'";
	                    }
	            }
			}
            
            // To get count of total requisitions
            $db = Zend_Db_Table::getDefaultAdapter();
            $count_query = "select count(c.id) cnt from main_candidatedetails c 
                            LEFT JOIN main_requisition_summary r ON c.requisition_id = r.req_id where ".$search_str;
            $count_result = $db->query($count_query);
            $count_row = $count_result->fetch();
            
            // To get requisitions data
            $count = $count_row['cnt'];
            $page_cnt = ceil($count/$per_page);
            
            if($sort_name == 'cand_status'){
                $sort_name = "cast(cand_status as char(100))";
            }
            
            $offset = ($per_page*$page_no) - $per_page;
            $limit_str = " limit ".$per_page." offset ".$offset;
            $query = "select r.requisition_code requisition_code, r.jobtitle_name jobtitle_name,
                      c.candidate_name candidate_name, c.emailid emailid, c.cand_resume cand_resume, 
                      c.cand_status cand_status, c.contact_number contact_number, c.skillset skillset 
                      from main_candidatedetails c
                      LEFT JOIN main_requisition_summary r ON c.requisition_id = r.req_id 
                      where $search_str order by $sort_name $sort_type $limit_str";
            $result = $db->query($query);
            $rows = $result->fetchAll();
            return array('rows' => $rows,'page_cnt' => $page_cnt);
    }
	//get the selected candidatedetails
    public function getSelectedCandidatesDetails()
    {
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$query = "select c.id,c.candidate_name,c.emailid from main_candidatedetails c
                    where c.isactive = 1 and c.cand_status = 'Selected' and c.backgroundchk_status in ('Yet to start','Completed') and c.id not in (select rccandidatename from main_users
                    where isactive = 1 and rccandidatename is not null)";
    	 
    	$result = $db->query($query);
    	$candidateData= $result->fetchAll();
    	return $candidateData;
    	 
    }
}//end of class