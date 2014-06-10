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

class Default_Model_Bgpocdetails extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_bgpocdetails';
    protected $_primary = 'id';
	
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
		$agencyData = $db->query("SELECT a.id as agencyid, a.agencyname, u.userfullname, u.emailaddress,p.id as pocid, 
									a.*,p.* FROM main_bgagencylist a 
									JOIN main_bgpocdetails p ON p.bg_agencyid = a.id
									JOIN main_users u on u.id = a.user_id									
									WHERE p.id=".$id." AND a.isactive = 1 AND p.isactive = 1;");									
		$result= $agencyData->fetchAll();
		return $result; 
		
	}
	
	public function getPrimaryPOCData($agency_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$agencyData = $db->query("SELECT bgpd.first_name, bgpd.last_name, bgpd.email
									FROM main_bgpocdetails bgpd WHERE bgpd.bg_agencyid=".$agency_id." AND bgpd.contact_type=1;");									
		$result= $agencyData->fetchAll();
		return $result; 
	}
	
	public function deleteBGcheckdetails_normalquery($agencyid,$pocid,$userid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$agencyData = $db->query("update main_bgcheckdetails set 
								isactive = 4, modifieddate = '".gmdate("Y-m-d H:i:s")."',
								modifiedby = ".$userid."
								where isactive = 1 and bgagency_pocid = ".$pocid." and bgagency_id = ".$agencyid.";");		
	}
	
	public function deleteBGcheckdetails($agencyid,$pocid,$userid)
	{
		$screeningmodel = new Default_Model_Empscreening();
		$data = array(
			'isactive' => 4,
			'modifieddate' => gmdate("Y-m-d H:i:s"),
			'modifiedby' => $this->getLoginUserId()
		);
		$where = "isactive = 1 and bgagency_pocid = ".$pocid." and bgagency_id = ".$agencyid;
		$screeningmodel->SaveorUpdateDetails($data,$where);
	}
	
	public function getPOCsData($sort, $by, $pageNo, $perPage,$searchQuery,$agencyid)
	{
				
	}
	
	public function deletePOCData($id,$agencyid)
	{
	}
	public function checkMobileDuplicates($id,$mobile)
	{
		if($id) $row = $this->fetchRow("contact_no = '".$mobile."' AND isactive = 1 AND id <> ".$id); 
		else	$row = $this->fetchRow("contact_no = '".$mobile."' AND isactive = 1");
		if(!$row){
			return false;
		}else{
		return true;
		}
	}
	
	public function checkEmailDuplicates($id,$email)
	{
		if($id) $row = $this->fetchRow("email = '".$email."' AND isactive = 1  AND id <> ".$id); 
		else	$row = $this->fetchRow("email = '".$email."' AND isactive = 1 ");
		if(!$row){
			return false;
		}else{
		return true;
		}
	}
	
	public function checkEmailInUsers($email,$agencyid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		if($agencyid)
		{
			$userquery = $db->query("SELECT user_id from main_bgagencylist where id = ".$agencyid);
			$userData = $userquery->fetch();
			$userid = $userData['user_id'];
			$agencyData = $db->query("SELECT * from main_users where emailaddress='".$email."' and id <> ".$userid);
		}else{
			$agencyData = $db->query("SELECT * from main_users where emailaddress='".$email."'");
		}
		$result= $agencyData->fetchAll();		
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
?>