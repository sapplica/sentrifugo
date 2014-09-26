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

class Default_Model_Agencylist extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_bgagencylist';
    protected $_primary = 'id';
	
	public function getagencylistData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "b.isactive = 1 ";
		if($searchQuery)
			$where .= " AND ".$searchQuery;		
		$agencylistdata = $this->select()
    					   ->setIntegrityCheck(false)	
						   ->from(array('b' => 'main_bgagencylist'))
						   ->joinInner(array('u'=>'main_users'), "u.id = b.user_id and u.isactive = 1",array('userid'=>'u.id','employeeId'=>'employeeId'))
							->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $agencylistdata;       		
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
				$searchQuery .= " ".$key." like '%".$val."%' AND ";
				$searchArray[$key] = $val;
			}
			$searchQuery = rtrim($searchQuery," AND");					
		}
		$objName = 'agencylist';		
		$tableFields = array('action'=>'Action','agencyname' => 'Agency Name','primaryphone' => 'Phone','address' => 'Address','website_url' => 'Website URL','employeeId'=>'User ID');
		$tablecontent = $this->getagencylistData($sort, $by, $pageNo, $perPage,$searchQuery);     
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
			'call' => $call,
			'dashboardcall'=>$dashboardcall,
		);		
		return $dataTmp;
	}
	
	public function getSingleAgencyData($id)
	{
		$row = $this->fetchRow("id = '".$id."'");
		if (!$row) {
			throw new Exception("Could not find row $id");
		}
		return $row->toArray();
	}
	
	public function SaveorUpdateAgency($data, $where)
	{
		if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_bgagencylist');
			return $id;
		}
	}
	
	public function SaveorUpdatePOCDetails($data, $where)
	{
		if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_bgpocdetails');
			return $id;
		}
	}
	
	public function getSingleagencyPOCData($id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$agencyData = $db->query("SELECT a.id as agencyid, p.id as pocid, a.*,p.* FROM main_bgagencylist a 
									RIGHT JOIN main_bgpocdetails p
									ON p.bg_agencyid = a.id
									WHERE a.id=".$id." AND a.isactive = 1 AND p.isactive = 1;");									
		$result= $agencyData->fetchAll();
		return $result; 
		
	}
	
	public function checkSiteDuplicates($website,$id)
	{
		
		$db = Zend_Db_Table::getDefaultAdapter();
		if($id)
		{
			$agencyData = $db->query("SELECT b.*,b.isactive as isactive,u.employeeId from main_bgagencylist b
									left join main_users u on b.user_id = u.id
									WHERE b.website_url = '".$website."' AND b.id <> ".$id);									
		}else{
			$agencyData = $db->query("SELECT b.*,b.isactive as isactive,u.employeeId from main_bgagencylist b
									left join main_users u on b.user_id = u.id
									WHERE b.website_url = '".$website."'");
		}
		$result= $agencyData->fetch();
		return $result; 
	}
	
	public function deleteAgencyData($id,$userid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();		
		$agencyData = $db->query("UPDATE main_bgagencylist a, main_bgpocdetails p
									SET p.isactive=3,
									a.isactive=0,
									a.modifieddate = '".gmdate("Y-m-d H:i:s")."',
									p.modifieddate = '".gmdate("Y-m-d H:i:s")."',
									a.modifiedby = ".$userid.",
									p.modifiedby = ".$userid."
									WHERE a.id=p.bg_agencyid
									AND a.id=".$id);
		$agencyData = $db->query("UPDATE main_users SET isactive = 0 WHERE id = (SELECT user_id FROM 				
									main_bgagencylist where id = ".$id." AND isactive = 0);");
		$pocIds = $db->query("select id from main_bgpocdetails where bg_agencyid = ".$id.";");
		$result= $pocIds->fetchAll();
		$ids = array_map(function($item) { return $item['id']; }, $result);
		$output = implode(',', $ids);		
		return $output;
	}
	
	public function deleteBGcheckdetails_normalquery($agencyid,$userid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$agencyData = $db->query("update main_bgcheckdetails set 
								isactive=2,modifieddate = '".gmdate("Y-m-d H:i:s")."',
								modifiedby = ".$userid."
								where isactive = 1 and bgagency_id = ".$agencyid.";");		
	}
	
	public function deleteBGcheckdetails($agencyid,$userid)
	{
		$screeningmodel = new Default_Model_Empscreening();
		$data = array(
			'isactive' => 2,
			'modifieddate' => gmdate("Y-m-d H:i:s"),
			'modifiedby' => $userid
		);
		$where = "isactive = 1 and bgagency_id = ".$agencyid;
		$screeningmodel->SaveorUpdateDetails($data,$where);
	}
	
	public function getagencyrole()
	{
		$db = Zend_Db_Table::getDefaultAdapter();	
		
		$agencyrole = $db->query("select r.id,r.rolename from main_privileges p
							inner join main_roles r on r.id = p.role
							where object = ".EMPSCREENING." and role is not null and p.isactive = 1 and p.group_id = ".USERS_GROUP.";");
		$data = $agencyrole->fetchAll();
		return $data;
	}
	
	public function getAllHRManagementEMails()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$emailData = $db->query("select ec.group_id, eg.group_name, ec.groupEmail from main_emailcontacts  ec
									inner join main_emailgroups eg on eg.id = ec.group_id
									where eg.group_code in 	('BG_CHECKS_HR','BG_CHECKS_MNGMNT') and ec.isactive = 1;");
		$data = $emailData->fetchAll();
		return $data;
	}
	
	public function getAgencyEmail($id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$emailData = $db->query("select u.emailaddress,u.userfullname,u.emprole from  main_users u
						join main_bgagencylist b on u.id = b.user_id 
						where b.id = ".$id);
		$data = $emailData->fetch();
		return $data;
	}	
}