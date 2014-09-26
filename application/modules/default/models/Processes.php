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

class Default_Model_Processes extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_bgcheckdetails';
    protected $_primary = 'id';
	
	public function getProcessesData($sort, $by, $pageNo, $perPage,$searchQuery, $idData,$userid,$logingroup)
	{	
		if($logingroup == USERS_GROUP)
		{
			$where = ' a.user_id = '.$userid;
		}else $where = ' 1=1 ' ;
		$idArr = array();
		$idArr = explode('-',$idData);
		$id = $idArr[0];$userflag = $idArr[1]; 
		
		
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		if(isset($id) && isset($userflag))
		$where .= ' AND b.specimen_id = '.$id.' AND b.flag = '.$userflag;
		if($userflag == 1)
		{
			$empProcessData = $this->select()
    					   ->setIntegrityCheck(false)	 
						   ->from(array('b' => 'main_bgcheckdetails'),array('id'=>'b.id','isactive'=>'if(b.isactive=1,"Active",if(b.isactive=2,"Agency deleted",if(b.isactive=3,"Agency User deleted",if(b.isactive=4,"POC deleted","Process deleted"))))','process_status'=>'b.process_status','bgcheck_status'=>'b.bgcheck_status','explanation'=>'b.explanation','enddate'=>'if(b.process_status="Complete",DATE_FORMAT(b.modifieddate,"'.DATEFORMAT_MYSQL.'"),if(b.process_status="On hold",DATE_FORMAT(b.modifieddate,"'.DATEFORMAT_MYSQL.'"),""))','startdate'=>'DATE_FORMAT(b.createddate,"'.DATEFORMAT_MYSQL.'")','recentlycommenteddate'=>'DATE_FORMAT(b.recentlycommenteddate,"'.DATEFORMAT_MYSQL.'")'))
						   ->joinInner(array('a'=>'main_bgagencylist'),'b.bgagency_id = a.id',array('agencyname'=>'a.agencyname'))
						   ->joinInner(array('p'=>'main_bgpocdetails'),'b.bgagency_pocid = p.id',array('email'=>'p.email'))
						   
						   ->joinInner(array('mu'=>'main_users'),'b.specimen_id = mu.id',array('username'=>'mu.userfullname'))
						   ->joinInner(array('t'=>'main_bgchecktype'),'b.bgcheck_type = t.id',array('type'=>'t.type'))
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		}else {
			$empProcessData = $this->select()
    					   ->setIntegrityCheck(false)	 
						  
						   ->from(array('b' => 'main_bgcheckdetails'),array('id'=>'b.id','isactive'=>'if(b.isactive=1,"Active",if(b.isactive=2,"Agency deleted",if(b.isactive=3,"Agency User deleted",if(b.isactive=4,"POC deleted","Process deleted"))))','process_status'=>'b.process_status','bgcheck_status'=>'b.bgcheck_status','explanation'=>'b.explanation','enddate'=>'if(b.process_status="Complete",DATE_FORMAT(b.modifieddate,"'.DATEFORMAT_MYSQL.'"),if(b.process_status="On hold",DATE_FORMAT(b.modifieddate,"'.DATEFORMAT_MYSQL.'"),""))','startdate'=>'DATE_FORMAT(b.createddate,"'.DATEFORMAT_MYSQL.'")','recentlycommenteddate'=>'DATE_FORMAT(b.recentlycommenteddate,"'.DATEFORMAT_MYSQL.'")'))
						   ->joinInner(array('a'=>'main_bgagencylist'),'b.bgagency_id = a.id',array('agencyname'=>'a.agencyname'))
						   ->joinInner(array('p'=>'main_bgpocdetails'),'b.bgagency_pocid = p.id',array('email'=>'p.email'))
						   ->joinInner(array('c'=>'main_candidatedetails'),'b.specimen_id = c.id',array('username'=>'c.candidate_name'))
						   ->joinInner(array('t'=>'main_bgchecktype'),'b.bgcheck_type = t.id',array('type'=>'t.type'))
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		}
		
		return $empProcessData;       	
	}
	
	public function getsinglecheckDetailData($id)
	{
		$empProcessData = $this->select()
    					   ->setIntegrityCheck(false)	 
						   ->from(array('b' => 'main_bgcheckdetails'),array('id'=>'b.id','isactive'=>'b.isactive','process_status'=>'b.process_status','bgcheck_status'=>'b.bgcheck_status','createdHRId'=>'createdby', 'feedback_file'=>'b.feedback_file'))
						   ->joinInner(array('a'=>'main_bgagencylist'),'b.bgagency_id = a.id',array('agencyid'=>'b.bgagency_id','agencyname'=>'a.agencyname'))
						   ->joinInner(array('p'=>'main_bgpocdetails'),'b.bgagency_pocid = p.id',array('pocid'=>'b.bgagency_pocid','email'=>'p.email','first_name'=>'p.first_name','last_name'=>'p.last_name','contact_no'=>'p.contact_no','location'=>'p.location'))
						   ->joinInner(array('t'=>'main_bgchecktype'),'b.bgcheck_type = t.id',array('checktypeid'=>'b.bgcheck_type','checktype'=>'t.type'))
						   
						   ->joinLeft(array('ct'=>'tbl_cities'),'p.city=ct.id',array('city'=>'ct.city_name'))
						   ->joinLeft(array('st'=>'tbl_states'),'p.state=st.id',array('state'=>'st.state_name'))
						   ->joinLeft(array('cnt'=>'tbl_countries'),'p.country=cnt.id',array('country'=>'cnt.country_name'))
						   ->where('b.id = '.$id);
		
		
		return $this->fetchAll($empProcessData)->toArray();

	}
	
	public function getProcessStatus($specimenid,$userflag,$agencyid='',$checktypeid='')
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		if($agencyid == '' && $checktypeid == '')
		{
			
			$resultData = $db->query("SELECT b.process_status,b.bgcheck_status,b.isactive FROM main_bgcheckdetails b 
			inner join main_bgagencylist a on b.bgagency_id = a.id
			inner join main_bgchecktype c on b.bgcheck_type = c.id
			WHERE b.isactive = 1 AND a.isactive = 1 AND c.isactive = 1 AND b.specimen_id = ".$specimenid." AND b.flag = ".$userflag.";");

		}
		else
		{

			$resultData = $db->query("SELECT b.process_status,b.bgcheck_status,b.isactive FROM main_bgcheckdetails b 
			inner join main_bgagencylist a on b.bgagency_id = a.id
			inner join main_bgchecktype c on b.bgcheck_type = c.id
			WHERE b.isactive = 1 AND a.isactive = 1 AND c.isactive = 1 AND b.specimen_id = ".$specimenid."
			AND b.bgagency_id = ".$agencyid." AND b.bgcheck_type = ".$checktypeid." AND b.flag = ".$userflag.";");
		}
		$result = $resultData->fetchAll();
		return $result;
	}	
	
}
?>