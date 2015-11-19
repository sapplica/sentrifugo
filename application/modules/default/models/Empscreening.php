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

class Default_Model_Empscreening extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_bgcheckdetails';
    protected $_primary = 'id';
	
	
	
	public function getEmpScreeningData($sort, $by, $pageNo, $perPage,$searchQuery,$filter='1')
	{
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
		/* Added on 17032015 
		 * to resolve employees count issue for background check.
		 */
		if($filter == '1')
		{
			$where = " me.user_id <> ".$loginUserId;
		}else{	
			$where = " me.id <> ".$loginUserId;
		}
		/* commented on 17032015 
		 * to resolve employees count issue for background check.
		 * $where = " me.id <> ".$loginUserId;
		 */
		
		if($loginuserGroup == USERS_GROUP)
			$where .= " AND al.user_id =".$loginUserId;
		if($searchQuery)
			$where .= " AND ".$searchQuery;		
		$db = Zend_Db_Table::getDefaultAdapter();
		if($filter == '1')
		{			
			$where .= " AND me.backgroundchk_status <> 'Not Applicable' AND me.backgroundchk_status <> 'Yet to start'";
			
			$empScreeningData = $this->select()
								->setIntegrityCheck(false)
								//->distinct()
								->from(array('dt'=>'main_bgcheckdetails'),array())
								->joinLeft(array('me' => 'main_employees_summary'),'dt.specimen_id = me.user_id',array('id'=>'distinct concat(me.user_id,"-1")','backgroundchk_status'=>'if(me.backgroundchk_status="Completed","Complete",me.backgroundchk_status)','userfullname'=>'me.userfullname','createddate'=>'me.createddate','isactive'=>'if(me.isactive = 1, "Active",if(me.isactive = 2 , "Resigned",if (me.isactive = 3,"Left",if(me.isactive = 4,"Suspended",if(me.isactive = 5,"Deleted","Inactive")))))','jobtitle_name'=>'me.jobtitle_name','emailaddress'=>'me.emailaddress' ))
								->joinLeft(array('al'=>'main_bgagencylist'),'al.id = dt.bgagency_id',array())
								->where($where)
								->order("$by $sort") 
								->limitPage($pageNo, $perPage); 
		}else{			
			$where .= " AND me.backgroundchk_status <> 'Not Applicable' AND me.backgroundchk_status <> 'Yet to start' ";
			$empScreeningData = $this->select()
						->setIntegrityCheck(false)
						->from(array('me' => 'main_candidatedetails'),array('id'=>'distinct concat(me.id,"-2")','backgroundchk_status'=>'if(me.backgroundchk_status="Completed","Complete",me.backgroundchk_status)','candidate_name'=>'me.candidate_name','isactive'=>'if(me.isactive = 1, "Active","Inactive")','createddate'=>'me.createddate','cand_location'=>'me.cand_location'))
						
						->joinLeft(array('ct'=>'tbl_cities'),'me.city=ct.id',array('city_name'=>'ct.city_name'))
						->joinLeft(array('st'=>'tbl_states'),'me.state=st.id',array('state_name'=>'st.state_name'))
						->joinLeft(array('cnt'=>'tbl_countries'),'me.country=cnt.id',array('country_name'=>'cnt.country_name'))
						->joinLeft(array('dt'=>'main_bgcheckdetails'),'dt.specimen_id = me.id and flag = 2',array())
						->joinLeft(array('al'=>'main_bgagencylist'),'al.id = dt.bgagency_id',array())
						->where($where)
						->order("$by $sort") 
						->limitPage($pageNo, $perPage);			
		}				
		
		return $empScreeningData;    
	}
	
 public function getEmpScreeningDataCount($sort, $by, $pageNo, $perPage,$searchQuery,$filter='1')
	{
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
		
		/* Added on 14-05-2015 
		 * to resolve employees count issue for background check.
		 */
		
		if($filter == '1')
		{
			$where = " me.user_id <> ".$loginUserId;
		}else{	
			$where = " me.id <> ".$loginUserId;
		}
		if($loginuserGroup == USERS_GROUP)
			$where .= " AND al.user_id =".$loginUserId;
		if($searchQuery)
			$where .= " AND ".$searchQuery;		
		$db = Zend_Db_Table::getDefaultAdapter();
		if($filter == '1')
		{			
			$where .= " AND me.backgroundchk_status <> 'Not Applicable' AND me.backgroundchk_status <> 'Yet to start'";
			
			$empScreeningData = $this->select()
								->setIntegrityCheck(false)
								
								->from(array('me' => 'main_employees_summary'),array('id'=>'distinct concat(me.user_id,"-1")'))
								->joinLeft(array('dt'=>'main_bgcheckdetails'),'dt.specimen_id = me.user_id',array())
								->joinLeft(array('al'=>'main_bgagencylist'),'al.id = dt.bgagency_id',array())
								->where($where)
								->order("$by $sort"); 
								return count($this->fetchAll($empScreeningData)->toArray());
		}else{			
			$where .= " AND me.backgroundchk_status <> 'Not Applicable' AND me.backgroundchk_status <> 'Yet to start' ";
			$empScreeningData = $this->select()
						->setIntegrityCheck(false)
						->from(array('me' => 'main_candidatedetails'),array('id'=>'distinct concat(me.id,"-2")'))
						
						->joinLeft(array('ct'=>'tbl_cities'),'me.city=ct.id',array('city_name'=>'ct.city_name'))
						->joinLeft(array('st'=>'tbl_states'),'me.state=st.id',array('state_name'=>'st.state_name'))
						->joinLeft(array('cnt'=>'tbl_countries'),'me.country=cnt.id',array('country_name'=>'cnt.country_name'))
						->joinLeft(array('dt'=>'main_bgcheckdetails'),'dt.specimen_id = me.id and flag = 2',array())
						->joinLeft(array('al'=>'main_bgagencylist'),'al.id = dt.bgagency_id',array())
						->where($where)
						->order("$by $sort");
								
		}				
		
		return count($this->fetchAll($empScreeningData)->toArray());   
	}
	
		
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$queryflag,$statusidstring,$formgrid,$unitId)
	{
		$searchQuery = '';
        $searchArray = array();
        $data = array();
		if($searchData != '' && $searchData!='undefined')
		{
			$searchValues = json_decode($searchData);
			foreach($searchValues as $key => $val)
			{
				if($key == 'bgcheck_status')
				{
					$key = 'backgroundchk_status';
					$searchQuery .= " me.".$key." like '%".$val."%' AND ";
				}
				else if($key == 'isactive')
				{
					$key = 'isactive';
					$searchQuery .= " me.".$key." like '%".$val."%' AND ";
				}					
				else $searchQuery .= " ".$key." like '%".$val."%' AND ";
				$searchArray[$key] = $val;
			}
			$searchQuery = rtrim($searchQuery," AND");					
		}
		$objName = 'empscreening';
		if($queryflag == '2')
		$tableFields = array('action'=>'Action','candidate_name' =>'Name','backgroundchk_status'=>'Background Check Status','cand_location' => 'Location','city_name'=>'City','state_name'=>'State','country_name'=>'Country','isactive'=>'Candidate Status');		
		else
		$tableFields = array('action'=>'Action','userfullname' =>'Name','backgroundchk_status'=>'Background Check Status','jobtitle_name' => 'Job Title','emailaddress'=>'Email','isactive'=>'Employee Status');

		$tablecontent = $this->getEmpScreeningData($sort, $by, $pageNo, $perPage,$searchQuery,$queryflag);    
		$count =  $this->getEmpScreeningDataCount($sort, $by, $pageNo, $perPage,$searchQuery,$queryflag);    
		$bg_arr = array('' => 'All','In process' => 'In process','Completed' => 'Complete',
                                'On hold' => 'On hold');
		$dataTmp = array(
			'sort' => $sort,
			'by' => $by,
			'pageNo' => $pageNo,
			'perPage' => $perPage,				
			'tablecontent' => $tablecontent,
			'objectname' => $objName,
			'extra' => array(),
			'menuName' => 'Employee/Candidate Screening',
			'tableheader' => $tableFields,
			'jsGridFnName' => 'getAjaxgridData',
			'jsFillFnName' => '',
			'searchArray' => $searchArray,
			'unitId'=>$statusidstring,			
			'formgrid' => $formgrid,
			'call'=>$call,
			'add' => 'add',
		    'empscreentotalcount' =>$count,
			'dashboardcall'=>$dashboardcall,
			'search_filters' => array(
                            'isactive' => array(
                                'type'=>'select',
                                'filter_data'=>array(''=>'All',1 => 'Active',0 => 'Inactive',
                                            2=>'Resigned',3=>'Left',4=>'Suspended',5=>'Deleted')
                                ),
                            'backgroundchk_status' => array(
                                'type' => 'select',
                                'filter_data' => $bg_arr,
                            ),
                        ),
		);
		return $dataTmp;
	}	
		
	public function getEmployeesForScreening()
	{
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$db = Zend_Db_Table::getDefaultAdapter();
		
                $query = "SELECT CONCAT('emp','-',u.user_id) AS id,u.emprole, u.userfullname AS name , u.jobtitle_name 
                                        as jobtitle, u.profileimg
                                        FROM main_employees_summary u                                        
                                        INNER JOIN main_roles r on r.id = u.emprole
                                        WHERE u.user_id != ".$loginUserId."  AND u.isactive = 1 AND r.group_id != ".MANAGEMENT_GROUP."
                                        AND (u.backgroundchk_status = 'Yet to start') order by u.userfullname;";
                
                $empData = $db->query($query);
		$empResult= $empData->fetchAll();
		return $empResult;
	}
	
	public function getCandidatesForScreening()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$candData = $db->query("select concat('cand','-',c.id) as id,c.candidate_name as name from main_candidatedetails c 
		INNER JOIN main_requisition r on r.id = c.requisition_id
		where c.isactive = 1 AND c.cand_status = 'Selected' AND (backgroundchk_status = 'Yet to start') AND r.isactive = 1
		order by candidate_name;");
		$candResult = $candData->fetchAll();
		return $candResult;
	}
	
	public function getEmpData($id,$con)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		if($con=='cand' || $con == 2)
		{
			$Data = $db->query("SELECT c.*,c.candidate_name as name,c.emailid as email,c.contact_number as contactnumber,
								wd.company_name as companyname, wd.contact_number as companynumber, wd.company_address as companyaddress,
								wd.company_website as companywebsite, wd.cand_designation as designation,DATE_FORMAT(wd.cand_fromdate,'".DATEFORMAT_MYSQL."') as fromdate,
								DATE_FORMAT(wd.cand_todate,'".DATEFORMAT_MYSQL."') as todate,
								c.cand_location as location,ct.city as ccity,st.state as cstate,cn.country as ccountry 
								FROM main_candidatedetails c
								LEFT JOIN main_candworkdetails wd on c.id=wd.cand_id
								LEFT JOIN main_cities ct on c.city=ct.id
								LEFT JOIN main_states st on c.state=st.id
								LEFT JOIN main_countries cn on c.country = cn.id 
								where c.id=".$id." and c.isactive = 1;");							
			$data = $Data->fetchAll();
		}
		else if($con=='emp' || $con == 1){ 
			$Data = $db->query("SELECT e.*,e.emp_name as name,e.emp_image as profileImage,mu.userfullname as reporting_manager,mu.emailaddress as rmanager_email,e.emp_email as email,e.emp_contactNumber as contactnumber,
								wd.company_name as companyname, wd.contact_number as companynumber, wd.company_address as companyaddress,
								wd.company_website as companywebsite, wd.emp_designation as designation,DATE_FORMAT(wd.emp_fromdate,'".DATEFORMAT_MYSQL."') as fromdate,
								DATE_FORMAT(wd.emp_todate,'".DATEFORMAT_MYSQL."') as todate,
								e.emp_location as location, ct.city as ccity,st.state as cstate,cn.country as ccountry 
								FROM main_employees e
								LEFT JOIN main_users mu on e.reporting_manager=mu.id
								LEFT JOIN main_empworkdetails wd on e.id=wd.emp_id
								LEFT JOIN main_cities ct on e.city=ct.id
								LEFT JOIN main_states st on e.state=st.id
								LEFT JOIN main_countries cn on e.country = cn.id 
								where e.id=".$id." AND e.isactive = 1;");	
			$data = $Data->fetchAll();
		}
		$result = $data;
		return $result;
	}
	
	public function getAgencyData($agencyArr,$limit,$page)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$where = ' a.isactive = 1 and u.isactive = 1 ';
		for($i = 0;$i < sizeof($agencyArr);$i++)
		{
			$where .= " AND FIND_IN_SET('$agencyArr[$i]',a.bg_checktype) ";
		}		
		$agencyData = $db->query("select a.id,a.agencyname from main_bgagencylist a
									INNER JOIN main_users u on u.id = a.user_id
									where ".$where." ORDER BY a.agencyname ASC");//LIMIT ".$page.", ".$limit
		$agencyResult = $agencyData->fetchAll();
		return $agencyResult;
	}
	
	public function getAgencyDataCount($agencyArr)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$where = ' isactive = 1';
		for($i = 0;$i < sizeof($agencyArr);$i++)
		{
			$where .= " AND FIND_IN_SET('$agencyArr[$i]',bg_checktype) ";
		}echo "select count(id) as count from main_bgagencylist where ".$where." ORDER BY createddate DESC</br>";
		$agencyData = $db->query("select count(id) as count from main_bgagencylist where ".$where);
		$agencyResult = $agencyData->fetch();
		return $agencyResult['count'];
	}
	
	public function getAgencyPOCData($agencyid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$agencyData = $db->query("SELECT a.id as agencyid, p.id as pocid,ct.city_name as ccity,st.state_name as cstate,cn.country_name as ccountry,p.contact_type as contact_type,p.contact_no,p.email,p.location, a.*,p.* 
									FROM main_bgagencylist a 
									INNER JOIN main_bgpocdetails p ON p.bg_agencyid = a.id
									LEFT JOIN tbl_cities ct on p.city=ct.id
									LEFT JOIN tbl_states st on p.state=st.id
									LEFT JOIN tbl_countries cn on p.country = cn.id 
									WHERE a.id=".$agencyid." AND a.isactive = 1 AND p.isactive = 1 ORDER BY p.contact_type ASC;");	
									
		$result= $agencyData->fetchAll();
		return $result; 
	}
	
	public function SaveorUpdateDetails($data, $where)
	{
		
		if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_bgcheckdetails');
			return $id;
		}
	}
	
	public function getsingleEmpscreeningData($id,$userflag)
	{
		$row = $this->fetchRow("specimen_id = '".$id."' AND flag = '".$userflag."'");
		if (!$row) {
			return 'norows';
		}
		return $row->toArray();
	}
	
	public function checkbgstatus($specimenId,$empFlag,$con)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		if($empFlag == 1) { 
			$tableName = 'main_users';  $columnName='userfullname';
			$leftjoinData = 'LEFT JOIN main_users mu ON mu.id = tb.reporting_manager ';
			$leftjoinColumns = ', mu.userfullname as reporting_manager,mu.emailaddress as rmanager_email ';
		} else { 
			$tableName = 'main_candidatedetails'; $columnName='candidate_name'; 
			$leftjoinColumns = '';
			$leftjoinData = '';
		}
		if($con == 'completedata')
		{	
			if($empFlag == 1){
			$query = $db->query("select bg.explanation, mu.userfullname as reporting_manager,mu.emailaddress as rmanager_email ,
				bg.id,bg.process_status, bg.bgcheck_status,bg.specimen_id as specimentId,bg.flag as flag, ct.type,
				ag.agencyname,tb.userfullname as username, tb.employeeId employee_id, tb.emailaddress email_id, j.jobtitlename designation FROM main_bgcheckdetails bg 
				LEFT JOIN main_users tb ON tb.id = bg.specimen_id 
				INNER JOIN main_bgchecktype ct ON ct.id = bg.bgcheck_type 
				INNER JOIN main_bgagencylist ag ON ag.id = bg.bgagency_id 
				LEFT JOIN main_employees me on tb.id = me.user_id
				LEFT JOIN main_jobtitles j ON j.id = me.jobtitle_id
				LEFT JOIN main_users mu ON mu.id =me.reporting_manager 
				WHERE bg.isactive = 1 AND bg.specimen_id =".$specimenId." AND bg.flag=1 AND bg.explanation IS NULL;");
			}
			else {
			$query = $db->query("select bg.explanation".$leftjoinColumns.",bg.id,bg.process_status,
					bg.bgcheck_status,bg.specimen_id as specimentId,bg.flag as flag,
					ct.type,ag.agencyname,tb.".$columnName." as username
					FROM main_bgcheckdetails bg
					LEFT JOIN ".$tableName." tb ON tb.id = bg.specimen_id					
					INNER JOIN main_bgchecktype ct ON ct.id = bg.bgcheck_type
					INNER JOIN main_bgagencylist ag ON ag.id = bg.bgagency_id
					 ".$leftjoinData." 
					WHERE bg.isactive = 1 AND bg.specimen_id =".$specimenId." AND bg.flag=".$empFlag." AND
					bg.explanation IS NULL");		
			}	
		}
		else if($con == 'status')
		{	
			$query = $db->query("select * from main_bgcheckdetails where specimen_id =".$specimenId." AND isactive = 1 AND flag=".$empFlag);
		}		
		else
		{
			$query = $db->query("select * from main_bgcheckdetails where specimen_id =".$specimenId." AND flag=".$empFlag);
		}
		$result = $query->fetchAll();
		return $result;
	}
	
	public function checkdetailedbgstatus($specimenId,$empFlag,$con,$detailid,$flag = '')
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		if($flag == 'findcompleted')
		{
			$query = $db->query("select * from main_bgcheckdetails where specimen_id = 
							(select specimen_id from main_bgcheckdetails where id = ".$detailid.") 
							and flag = (select flag from main_bgcheckdetails where id = ".$detailid.") and isactive = 1 and id <> ".$detailid.";");
			$result = $query->fetchAll();
		}
		else 
		{
			if($specimenId != '' && $empFlag != '')
			{
				$query = $db->query("select * from main_bgcheckdetails where specimen_id =".$specimenId." AND flag=".$empFlag." AND id=".$detailid);
			}else {
				$query = $db->query("select * from main_bgcheckdetails where id=".$detailid);
			}
			$result = $query->fetch();
		}
		return $result;
	}
	
	public function getEmpPersonalData($id,$con)
	{		
		$db = Zend_Db_Table::getDefaultAdapter();
		if($con=='cand' || $con == 2)
		{
			$Data = $db->query("SELECT r.businessunit_id as businessid,c.*,c.candidate_name as name,c.profileimg as profileImage,c.backgroundchk_status as backgroundchk_status,c.emailid as email,c.contact_number as contactnumber, if(c.isactive = 1,'Active','Inactive') as ustatus							
								FROM main_candidatedetails c	
								LEFT JOIN main_requisition r on r.id = c.requisition_id
								where c.id=".$id.";");							
			$data = $Data->fetchAll();
		}
		else if($con=='emp' || $con == 1){ 
			$Data = $db->query("select u.id,u.userfullname as name, u.employeeId employee_id, u.emailaddress email_id, j.jobtitlename designation, u.profileimg as profileImage,u.backgroundchk_status as backgroundchk_status,
								if(u.isactive = 1,'Active',if(u.isactive = 2,'Resigned',if(u.isactive = 3,'Left',if(u.isactive = 4,'Suspended',if(u.isactive = 5,'Deleted','Inactive'))))) as ustatus,
								u.emailaddress as email,u.contactnumber,me.businessunit_id as businessid,
								mus.emailaddress as rmanager_email,mus.userfullname as reporting_manager,r.group_id
								FROM main_users u
								LEFT JOIN main_roles r on r.id = u.emprole
								LEFT JOIN main_employees me on me.user_id=u.id
								LEFT JOIN main_jobtitles j ON j.id = me.jobtitle_id
								LEFT JOIN main_users mus on me.reporting_manager=mus.id
								where u.id=".$id.";");	
			$data = $Data->fetchAll();
		}
		$result = $data;
		return $result;
	}
	
	public function getEmpAddressData($id,$con)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		if($con=='cand' || $con == 2)
		{
			$Data = $db->query("SELECT c.cand_location as location, c.pincode as pincode,ct.city_name as ccity,st.state_name as cstate,cn.country_name as ccountry 
								FROM main_candidatedetails c
								LEFT JOIN tbl_cities ct on c.city=ct.id
								LEFT JOIN tbl_states st on c.state=st.id
								LEFT JOIN tbl_countries cn on c.country = cn.id 
								where c.id=".$id.";");							
			$data = $Data->fetchAll();
		}
		else if($con=='emp' || $con == 1){ 
			$Data = $db->query("select ec.perm_streetaddress as location,ec.perm_pincode as pincode,
								ct.city_name as ccity,st.state_name as cstate,cn.country_name as ccountry
								FROM main_users u
								LEFT JOIN main_empcommunicationdetails ec on ec.user_id=u.id
								LEFT JOIN tbl_cities ct on ec.perm_city=ct.id
								LEFT JOIN tbl_states st on ec.perm_state=st.id
								LEFT JOIN tbl_countries cn on ec.perm_country = cn.id
								where u.id=".$id.";");	
			$data = $Data->fetchAll();
		}
		$result = $data;
		return $result;
	}
	
	public function getEmpCompanyData($id,$con)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		if($con=='cand' || $con == 2)
		{
			$Data = $db->query("SELECT c.id,wd.company_name as companyname, wd.contact_number as companynumber, wd.company_address as companyaddress,
								wd.company_website as companywebsite, wd.cand_designation as designation,DATE_FORMAT(wd.cand_fromdate,'".DATEFORMAT_MYSQL."') as fromdate,
								DATE_FORMAT(wd.cand_todate,'".DATEFORMAT_MYSQL."') as todate
								FROM main_candidatedetails c
								LEFT JOIN main_candworkdetails wd on c.id=wd.cand_id
								where c.id=".$id." AND wd.isactive = 1;");							
			$data = $Data->fetchAll();
		}
		else if($con=='emp' || $con == 1){ 
			$Data = $db->query("select u.id,wd.comp_name as companyname,
								wd.comp_website as companywebsite, wd.designation as designation,DATE_FORMAT(wd.from_date,'".DATEFORMAT_MYSQL."') as fromdate,
								DATE_FORMAT(wd.to_date,'".DATEFORMAT_MYSQL."') as todate
								FROM main_users u
								LEFT JOIN main_empexperiancedetails wd on u.id=wd.user_id
								where u.id=".$id." AND wd.isactive = 1;");	
			$data = $Data->fetchAll();
		}
		$result = $data;
		return $result;
	}
	
	public function checkallprocesses($id)
	{	
		$db = Zend_Db_Table::getDefaultAdapter();
		$details = $db->query('select flag,specimen_id from main_bgcheckdetails where id='.$id);
		$dedata = $details->fetch();
		$flag = $dedata['flag'];$userid = $dedata['specimen_id'];
		$inactiveData = $db->query("select count(id) as count from main_bgcheckdetails where isactive = 0 and specimen_id=".$userid." and flag=".$flag.";");
		$indata = $inactiveData->fetch();
		$inactiveCount = $indata['count'];
		$allData = $db->query("select count(id) as count from main_bgcheckdetails where isactive = 0 and specimen_id=".$userid." and flag=".$flag.";");
		$alldata = $allData->fetch();
		$allCount = $alldata['count'];
		if($inactiveCount == $allCount)	{
			if($flag == 1)
			{				
				$Data = $db->query("UPDATE main_users
						SET backgroundchk_status = 'Yet to start' WHERE id=".$userid.";");						
			}else{
			$Data = $db->query("UPDATE main_candidatedetails
						SET backgroundchk_status = 'Yet to start' WHERE id=".$userid.";");				
			}
			return 'redirect';
		}else{
			return 'ignore';
		}		
	}
	public function updateusersondelete_normalquery($userid,$flag)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		if($flag == 1)
			{				
				$Data = $db->query("UPDATE main_users
						SET backgroundchk_status = 'Yet to start' WHERE id=".$userid.";");						
			}else{
			$Data = $db->query("UPDATE main_candidatedetails
						SET backgroundchk_status = 'Yet to start' WHERE id=".$userid.";");				
			}
	}
	
	public function updateusersondelete($userid,$flag)
	{ 
		$db = Zend_Db_Table::getDefaultAdapter();
		$usermodel = new Default_Model_Users();
		$candmodel = new Default_Model_Candidatedetails();
		
		$userdata = array(
				'backgroundchk_status' => 'Yet to start',
				'modifieddate' => gmdate("Y-m-d H:i:s"),
				'modifiedby' => $this->getLoginUserId()
		);
		$userwhere = "id=".$userid;		
		if($flag == 1)
			{				
				$usermodel->addOrUpdateUserModel($userdata,$userwhere);
			}else{				
				$candmodel->SaveorUpdateCandidateData($userdata,$userwhere);
			}
	}
	
	public function getempscreeningstatusArray()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$Data = $db->query('select if(isactive = 1, "active",if(isactive = 2 , "resigned",if (isactive = 3,"left",if(isactive = 4,"suspended",if(isactive = 5,"deleted","Inactive"))))) as isactive from main_users where backgroundchk_status <> "Not Applicable" AND backgroundchk_status <> "Yet to start";');	
		return $data = $Data->fetchAll();
	}
	
	public function updatebgstatus_normalquery($con,$specimenid,$flag)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		if($con == 'onhold')
		{
			if($flag == 1)
			{
				$Data1 = $db->query("UPDATE main_users
						SET backgroundchk_status = 'On hold' WHERE id=".$specimenid.";");	
			}else{
				$Data1 = $db->query("UPDATE main_candidatedetails
						SET backgroundchk_status = 'On hold' WHERE id=".$specimenid.";");							
			}
			$Data2 = $db->query("UPDATE main_bgcheckdetails
						SET bgcheck_status = 'On hold' WHERE flag = ".$flag." AND specimen_id=".$specimenid.";");	
		}
		if($con == 'complete')
		{
			if($flag == 1)
			{
				$Data1 = $db->query("UPDATE main_users
						SET backgroundchk_status = 'Completed' WHERE id=".$specimenid.";");		
			}else{
				$Data1 = $db->query("UPDATE main_candidatedetails
						SET backgroundchk_status = 'Completed' WHERE id=".$specimenid.";");			
			}
			$Data2 = $db->query("UPDATE main_bgcheckdetails
						SET bgcheck_status = 'Complete' WHERE flag = ".$flag." AND specimen_id=".$specimenid.";");	
		}
	}
    
	public function updatebgstatus($con,$specimenid,$flag)
	{	
		$db = Zend_Db_Table::getDefaultAdapter();
		$screeningmodel = new Default_Model_Empscreening();
		$usermodel = new Default_Model_Users();
		$candmodel = new Default_Model_Candidatedetails();
		if($con == 'onhold')
		{
			$userdata = array(
				'backgroundchk_status' => 'On hold',
				'modifieddate' => gmdate("Y-m-d H:i:s"),
				'modifiedby' => $this->getLoginUserId()
			);
			$userwhere = "id=".$specimenid;
			if($flag == 1)
			{	
				$usermodel->addOrUpdateUserModel($userdata,$userwhere);
			}else{				
				$candmodel->SaveorUpdateCandidateData($userdata,$userwhere);
			}
			
			$data = array(
				'bgcheck_status' => 'On hold',
				'modifieddate' => gmdate("Y-m-d H:i:s"),
				'modifiedby' => $this->getLoginUserId()
			);
			$where = "flag = ".$flag." AND specimen_id=".$specimenid;
			$screeningmodel->SaveorUpdateDetails($data,$where);
		}
		if($con == 'complete')
		{
			$userdata = array(
				'backgroundchk_status' => 'Completed',
				'modifieddate' => gmdate("Y-m-d H:i:s"),
				'modifiedby' => $this->getLoginUserId()
			);
			$userwhere = "id=".$specimenid;
			
			if($flag == 1)
			{
				$usermodel->addOrUpdateUserModel($userdata,$userwhere);
			}else{
				$candmodel->SaveorUpdateCandidateData($userdata,$userwhere);
			}
			
			$data = array(
				'bgcheck_status' => 'Complete',
				'modifieddate' => gmdate("Y-m-d H:i:s"),
				'modifiedby' => $this->getLoginUserId()
			);
			$where = "flag = ".$flag." AND specimen_id=".$specimenid;
			$screeningmodel->SaveorUpdateDetails($data,$where);
		}
	}
	
    // To save uploaded file contents
    public function saveUploadedFile($files=array()){
    
		$max_size = 1024;			// maxim size for image file, in KiloBytes

		// Allowed image types
		$allowtype = array('doc', 'docx', 'pdf', 'txt', 'xls', 'xlsx');

		/** Uploading the image **/

		$rezultat = '';
		$result_status = '';
		$result_msg = '';
		// if is received a valid file
		
		if (isset ($files['feedback-file'])) {
		  // checks to have the allowed extension
		  $type = explode(".", strtolower($files['feedback-file']['name']));
		  $ext = array_pop($type);
		  $file_name = implode('.', $type);
		  if(in_array($ext, $allowtype)){
			// check its size
			if ($files['feedback-file']['size']<=$max_size*1024) {
			  // if no errors
			  if ($files['feedback-file']['error'] == 0) {
			  	$date = new DateTime();
				$timestamp = $date->getTimestamp();
			  
			  	$newname = uniqid('feedback_').'_'.$timestamp.'.'.$ext;
			  	
			  	// Folder to upload resumes
				$newfilename = UPLOAD_PATH_FEEDBACK . "/" . $newname;  
									   
				if (!move_uploaded_file ($files['feedback-file']['tmp_name'], $newfilename)) {
				  $rezultat = '';
				  $result_status = 'error';
				  
				  $result_msg = "Failed to upload"; // To show error in one line, the above error message was replaced to this one.
				}else{
			      $rezultat = $newname;
				  $result_status = 'success';
				}
			  }
			}else{
				$rezultat = ''; 
				$result_status = 'error';
				
				$result_msg = 'Invalid file'; // To show error in one line, the above error message was replaced to this one.
			}
		  }
		  else 
		  { 
			$rezultat = ''; 
			$result_status = 'error';
			
			$result_msg = 'Invalid file'; // To show error in one line, the above error message was replaced to this one.
			
		  }
		}
		else 
		  { 
			$rezultat = ''; 
			$result_status = 'error';
			
			$result_msg = 'Failed to upload'; // To show error in one line, the above error message was replaced to this one.
			
		  }

		$result = array(
			'result'=>$result_status,
			'file_name'=>$rezultat,
			'msg'=>$result_msg
		);
		return $result;
    }
    
    // To get login user ID
    public function getLoginUserId(){
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            return $loginUserId = $auth->getStorage()->read()->id;
        }else{
			return 1;
		}
    }
        	
}