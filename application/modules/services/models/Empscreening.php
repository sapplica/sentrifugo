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

class Services_Model_Empscreening extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_bgcheckdetails';
    protected $_primary = 'id';
	
	public function getEmpScreeningData($page_no, $per_page,$filter='1',$loginid = '')
	{	
		$offset = ($per_page*$page_no) - $per_page;
        $limit_str = " limit ".$per_page." offset ".$offset;       
		
		$db = Zend_Db_Table::getDefaultAdapter();
		if($filter == '1')
		{		
			$query_cnt = "SELECT COUNT(*) cnt FROM main_employees_summary AS me  
							LEFT JOIN main_bgcheckdetails AS dt ON dt.specimen_id = me.user_id 
							LEFT JOIN main_bgagencylist AS al ON al.id = dt.bgagency_id 
							WHERE ( me.backgroundchk_status <> 'Not Applicable' AND me.backgroundchk_status <> 'Yet to start') AND al.user_id =".$loginid;
			$result_cnt = $db->query($query_cnt);
			$row_cnt = $result_cnt->fetch();
			$total_cnt = $row_cnt['cnt'];
			$page_cnt = ceil($total_cnt/$per_page);		
			
			$query = "SELECT distinct concat(me.user_id,'-1') AS id,
			if(me.backgroundchk_status='Completed','Complete',me.backgroundchk_status) AS backgroundchk_status,
			me.userfullname, me.createddate, 
			if(me.isactive = 1, 'Active',if(me.isactive = 2 , 'Resigned',if (me.isactive = 3,'Left',if(me.isactive = 4,'Suspended',if(me.isactive = 5,'Deleted','Inactive'))))) AS isactive, 
			me.jobtitle_name, me.emailaddress 
			FROM main_employees_summary AS me 
			LEFT JOIN main_bgcheckdetails AS dt ON dt.specimen_id = me.user_id 
			LEFT JOIN main_bgagencylist AS al ON al.id = dt.bgagency_id 
			WHERE ( me.backgroundchk_status <> 'Not Applicable' AND me.backgroundchk_status <> 'Yet to start') AND al.user_id =".$loginid." 
			ORDER BY me.modifieddate DESC ".$limit_str ;			
		}
		else
		{
			$query_cnt = "SELECT COUNT(*) cnt
				FROM main_candidatedetails AS me 
				LEFT JOIN tbl_cities AS ct ON me.city=ct.id 
				LEFT JOIN tbl_states AS st ON me.state=st.id LEFT JOIN tbl_countries AS cnt ON me.country=cnt.id 
				LEFT JOIN main_bgcheckdetails AS dt ON dt.specimen_id = me.id and flag = 2 
				LEFT JOIN main_bgagencylist AS al ON al.id = dt.bgagency_id 
				WHERE ( me.backgroundchk_status <> 'Not Applicable' AND me.backgroundchk_status <> 'Yet to start' ) AND al.user_id =".$loginid;
			$result_cnt = $db->query($query_cnt);
			$row_cnt = $result_cnt->fetch();
			$total_cnt = $row_cnt['cnt'];
			$page_cnt = ceil($total_cnt/$per_page);		
			
			$query = "SELECT distinct concat(me.id,'-2') AS id,
			if(me.backgroundchk_status='Completed','Complete',me.backgroundchk_status) AS backgroundchk_status,
			me.candidate_name, if(me.isactive = 1, 'Active','Inactive') AS isactive, me.createddate,
			me.cand_location, ct.city_name, st.state_name, cnt.country_name 
			FROM main_candidatedetails AS me 
			LEFT JOIN tbl_cities AS ct ON me.city=ct.id 
			LEFT JOIN tbl_states AS st ON me.state=st.id LEFT JOIN tbl_countries AS cnt ON me.country=cnt.id 
			LEFT JOIN main_bgcheckdetails AS dt ON dt.specimen_id = me.id and flag = 2 
			LEFT JOIN main_bgagencylist AS al ON al.id = dt.bgagency_id 
			WHERE ( me.backgroundchk_status <> 'Not Applicable' AND me.backgroundchk_status <> 'Yet to start' ) AND
			al.user_id =".$loginid." 
			ORDER BY me.modifieddate DESC ".$limit_str ;	
		}
		$result = $db->query($query);
		$data = $result->fetchAll();
		return array('rows' => $data,'page_cnt' => $page_cnt); 
		 
	}
	
	public function getsingleEmpscreeningData($id,$userflag)
	{
		$row = $this->fetchRow("specimen_id = '".$id."' AND flag = '".$userflag."'");
		if (!$row) {
			return 'norows';
		}
		return $row->toArray();
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
			$Data = $db->query("select u.id,u.userfullname as name,u.profileimg as profileImage,u.backgroundchk_status as backgroundchk_status,
								if(u.isactive = 1,'Active',if(u.isactive = 2,'Resigned',if(u.isactive = 3,'Left',if(u.isactive = 4,'Suspended',if(u.isactive = 5,'Deleted','Inactive'))))) as ustatus,
								u.emailaddress as email,u.contactnumber,me.businessunit_id as businessid,
								mus.emailaddress as rmanager_email,mus.userfullname as reporting_manager
								FROM main_users u
								LEFT JOIN main_employees me on me.user_id=u.id
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
			$data = $Data->fetch();
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
			$data = $Data->fetch();
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
		
	public function getProcessesData($specimenid, $specimentype,$page_no,$per_page,$agencyid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$offset = ($per_page*$page_no) - $per_page;
        $limit_str = " limit ".$per_page." offset ".$offset;       
		if($specimentype == 1)
		{
			$query_cnt = "SELECT count(*) as cnt FROM main_bgcheckdetails AS b 
							INNER JOIN main_bgagencylist AS a ON b.bgagency_id = a.id 
							INNER JOIN main_bgpocdetails AS p ON b.bgagency_pocid = p.id 
							INNER JOIN main_users AS mu ON b.specimen_id = mu.id 
							INNER JOIN main_bgchecktype AS t ON b.bgcheck_type = t.id 
							WHERE ( b.specimen_id = ".$specimenid." AND b.flag = 1 AND a.user_id = ".$agencyid.")";
			$result_cnt = $db->query($query_cnt);
			$row_cnt = $result_cnt->fetch();
			$total_cnt = $row_cnt['cnt'];
			$page_cnt = ceil($total_cnt/$per_page);		
			$query = "SELECT b.id, if(b.isactive=1,'Active',if(b.isactive=2,'Agency deleted',if(b.isactive=3,'Agency User deleted',if(b.isactive=4,'POC deleted','Process deleted')))) AS isactive, b.process_status, b.bgcheck_status, b.explanation, if(b.process_status='Complete',DATE_FORMAT(b.modifieddate,'%d %M %Y'),if(b.process_status='On hold',DATE_FORMAT(b.modifieddate,'%d %M %Y'),'')) AS enddate, DATE_FORMAT(b.createddate,'%d %M %Y') AS startdate, DATE_FORMAT(b.recentlycommenteddate,'%d %M %Y') AS recentlycommenteddate, a.agencyname, p.email, mu.userfullname AS username, t.type 
				FROM main_bgcheckdetails AS b 
				INNER JOIN main_bgagencylist AS a ON b.bgagency_id = a.id 
				INNER JOIN main_bgpocdetails AS p ON b.bgagency_pocid = p.id 
				INNER JOIN main_users AS mu ON b.specimen_id = mu.id 
				INNER JOIN main_bgchecktype AS t ON b.bgcheck_type = t.id 
				WHERE ( b.specimen_id = ".$specimenid." AND b.flag = 1 AND a.user_id = ".$agencyid.") ORDER BY b.modifieddate DESC ".$limit_str ;			
		}else{
			$query_cnt = "SELECT count(*) as cnt FROM main_bgcheckdetails AS b 
					INNER JOIN main_bgagencylist AS a ON b.bgagency_id = a.id 
					INNER JOIN main_bgpocdetails AS p ON b.bgagency_pocid = p.id 
					INNER JOIN main_candidatedetails AS c ON b.specimen_id = c.id 
					INNER JOIN main_bgchecktype AS t ON b.bgcheck_type = t.id 
					WHERE ( 1=1 AND b.specimen_id = ".$specimenid." AND b.flag = 2 AND a.user_id = ".$agencyid.")";
			$result_cnt = $db->query($query_cnt);
			$row_cnt = $result_cnt->fetch();
			$total_cnt = $row_cnt['cnt'];
			$page_cnt = ceil($total_cnt/$per_page);		
			
			$query = "SELECT b.id, if(b.isactive=1,'Active',if(b.isactive=2,'Agency deleted',if(b.isactive=3,'Agency User deleted',if(b.isactive=4,'POC deleted','Process deleted')))) AS isactive, b.process_status, b.bgcheck_status, b.explanation, if(b.process_status='Complete',DATE_FORMAT(b.modifieddate,'%d %M %Y'),if(b.process_status='On hold',DATE_FORMAT(b.modifieddate,'%d %M %Y'),'')) AS enddate, DATE_FORMAT(b.createddate,'%d %M %Y') AS startdate, DATE_FORMAT(b.recentlycommenteddate,'%d %M %Y') AS recentlycommenteddate, a.agencyname, p.email, c.candidate_name AS username, t.type 
			FROM main_bgcheckdetails AS b 
			INNER JOIN main_bgagencylist AS a ON b.bgagency_id = a.id 
			INNER JOIN main_bgpocdetails AS p ON b.bgagency_pocid = p.id 
			INNER JOIN main_candidatedetails AS c ON b.specimen_id = c.id 
			INNER JOIN main_bgchecktype AS t ON b.bgcheck_type = t.id 
			WHERE ( 1=1 AND b.specimen_id = ".$specimenid." AND b.flag = 2 AND a.user_id = ".$agencyid.") ORDER BY b.modifieddate DESC ".$limit_str ;	
		}
		$Data = $db->query($query);
		$result = $Data->fetchAll();
		//return $result;
		return array('rows' => $result,'page_cnt' => $page_cnt); 
	}
	
	public function getcomments($detailid,$page_no,$per_page)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$offset = ($per_page*$page_no) - $per_page;
        $limit_str = " limit ".$per_page." offset ".$offset;
		
		$query_cnt = "SELECT count(*) as cnt FROM main_bgcheckcomments AS b WHERE ( b.isactive = 1 AND b.bgdet_id = ".$detailid.") ";		
		$result_cnt = $db->query($query_cnt);
		$row_cnt = $result_cnt->fetch();
		$total_cnt = $row_cnt['cnt'];
		$page_cnt = ceil($total_cnt/$per_page);	
		
		$query = "SELECT b.id, b.bgdet_id AS detail_id, b.comment, b.from_id, b.to_id, b.createddate FROM main_bgcheckcomments AS b WHERE ( b.isactive = 1 AND b.bgdet_id = ".$detailid.") ORDER BY b.createddate DESC ".$limit_str;
		$Data = $db->query($query);
		$result = $Data->fetchAll();		
		return array('rows' => $result,'page_cnt' => $page_cnt); 
	}
	
	public function checkagencyfordetail($detailid, $agencyid)
	{
		$row = $this->fetchRow("id = '".$detailid."' AND bgagency_id = '".$agencyid."'");
		if (!$row) {
			return 'norows';
		}
		return $row->toArray();
	}
	
	public function savecomment($detailid,$loginid,$comment)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$agencyData = $db->query("insert into main_bgcheckcomments(bgdet_id,comment,from_id,to_id,createddate) values(".$detailid.",'".$comment."',".$loginid.",'0','".gmdate("Y-m-d H:i:s")."');");
		return true;
	}
}