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

/**
 * A dummy class as return objec.
 * 
 * @author Yiyu Jia
 *
 */
class sapp_Return {

	public function init()
    {
	  
	   $zendobj = Zend_Controller_Front::getInstance();       
    }

	public function test()
	{
		
		$testString = array('status'=>'0','message'=>'Created an action for restful web service.','result'=>'');
		
		return $testString;
	}
	
	
	
	
	public function login($postarray)
	{
		
		
		
		if(isset($postarray['email']) && isset($postarray['pwd']) && $postarray['email'] != '' && $postarray['pwd'] != '')
		{
			$usermodel= new User_Model_Users();
			$userDet = $usermodel->userDetails($postarray['email'], md5($postarray['pwd']));
			if(count($userDet)>0)
			{			
				if($userDet[0]['user_role_id'] != 2)
				{
					$userDetJson = array('status'=>'0','message'=>'Not a privileged user.','result'=>'');
				}
				else if($userDet[0]['is_blocked'] == 1)
				{
					$userDetJson = array('status'=>'0','message'=>'User has been blocked.','result'=>'');
				}
				else if($userDet[0]['is_active'] == 0)
				{
					$userDetJson = array('status'=>'0','message'=>'Not an active user.','result'=>'');
				}
				else
				{
					unset($userDet[0]['user_role_id']);
					unset($userDet[0]['is_blocked']);
					unset($userDet[0]['is_active']);
					
					$userDetJson = array('status'=>'1','message'=>'Successfully logged in.','result'=>$userDet);
				}
			}
			else
			{
				$userDetJson = array('status'=>'0','message'=>'No user exist with the credentials.','result'=>'');
			}
		}
		else
		{
			$userDetJson = array('status'=>'0','message'=>'Email and Password should not be empty.','result'=>'');
		}		
		
		
		return $userDetJson;
	}
	
	
	
	public function userstats($postarray)
	{
		
		
		if(isset($postarray['eventid']) && $postarray['eventid'] != '')
		{
			$eventId = $postarray['eventid'];
    		$where = ' AND ue.event_id = '.$eventId;
		}
		else
		{
			$eventId = '';
			$where = '';
		}		
		if(isset($postarray['userid']) && $postarray['userid'] != '')
		{
			$alleventsModel = new User_Model_Events();
			// total event details	
			$alleventdetails =  $alleventsModel->userEventDetails($postarray['userid'], $where);
			$this->view->alleventdetails = $alleventdetails[0];
			
			// total method details	
			$alleventmethoddetails =  $alleventsModel->eventMethodDetails($postarray['userid'], $where);   	
			$this->view->alleventmethoddetails = $alleventmethoddetails[0];

			$final_arr = array_merge($alleventdetails[0],$alleventmethoddetails[0]);
			
			$userDetJson = array('status'=>'1','message'=>'Success','result'=>$final_arr);			
					
		}
		else
		{
			$userDetJson = array('status'=>'0','message'=>'User Id should not be empty.','result'=>'');
		}
		return $userDetJson;	
	
	}
	
	public function tracklist($postarray)
	{	
		
		
		if(isset($postarray['userid']) && $postarray['userid'] != '')
		{
			// total event details	
			$alleventsModel = new User_Model_Events();
			$alleventdetails =  $alleventsModel->userEventDetails($postarray['userid'], '');
			$this->view->alleventdetails = $alleventdetails;
			
			/* Queries for Friends */	
			 $Friendseventdetails = array();
			
			$trackModel = new User_Model_Tracks();			
			
			$trackDataCount = $trackModel->getCountOfUserTrack($postarray['userid']);
			
			$alleventdetails[0]['total_frnds_cnt'] = $trackDataCount[0]['count'];			
			
			if(isset($postarray['pageno']) && $postarray['pageno'] != '')
				$pageno = $postarray['pageno'];
			else
				$pageno=1;			
			
			$perpagecnt=10;
						
			$trackData = $trackModel->servcetogetUserTrackDetailsByID($postarray['userid'],$pageno,$perpagecnt);
			
			$trackUsers='';
			for($i=0;$i<sizeof($trackData);$i++)
			{
				$trackUsers .= $trackData[$i]['tracked_user_id'].',';
			}
			
			$trackUsers = trim($trackUsers,',');
			
			if($trackUsers !='')
			{
				$Friendseventdetails =  $alleventsModel->trackedUserStats($trackUsers);  
			}	
			
			$final_arr = array();
			$final_arr['user'] = $alleventdetails;
			$final_arr['friends'] = $Friendseventdetails;
			
			
		
			$userDetJson = array('status'=>'1','message'=>'Success','result'=>$final_arr);
		}
    	else
		{
			$userDetJson = array('status'=>'0','message'=>'User Id should not be empty.','result'=>'');
		}
		return $userDetJson;
	}
	
	
	
	public function allevents($postarray)
	{
		// all events		
		if(isset($postarray['userid']) && $postarray['userid'] != '')
		{
			$alleventsModel = new User_Model_Events();
			$allEvents = $alleventsModel->getallevents($postarray['userid']);					
			$userDetJson = array('status'=>'1','message'=>'Success','result'=>$allEvents);
		}
    	else
		{
			$userDetJson = array('status'=>'0','message'=>'User Id should not be empty.','result'=>'');
		}
		
		return $userDetJson;
	}
	
	public function userevents($postarray)
	{
		if(isset($postarray['userid']) && $postarray['userid'] != '')
		{
			$allusereventsModel = new User_Model_UserEvents();
			$alluserEvents = $allusereventsModel->getallUserEvents($postarray['userid']);			
			$userDetJson = array('status'=>'1','message'=>'Success','result'=>$alluserEvents);
		}
    	else
		{
			$userDetJson = array('status'=>'0','message'=>'User Id should not be empty.','result'=>'');
		}
		
		return $userDetJson;
	}
	
	
	 //Get all user fights  
    public function totalfights($postarray)
    {		
		
		if(isset($postarray['userid']) && $postarray['userid'] != '')
		{		
			if(isset($postarray['eventid']) && $postarray['eventid'] != '')
			{
				$eventId = $postarray['eventid'];
				$where = ' AND ue.event_id = '.$eventId;
				$where_eventid = " AND uae.event_id = ".$eventId;
			}
			else
			{
				$eventId = '';
				$where = '';
				$where_eventid = '';
			}
		
			$eventsModel = new User_Model_Events();
			
			$type = '';
			// total event details	
			$eventsDetails =  $eventsModel->getDashboardTotalFightsByUserId($postarray['userid'],$type,$where ,$where_eventid);
			$fastestFight = $eventsModel->getFastestFightByUserId($postarray['userid'],$where);
			
			
			
			
			$fastestfightsArray = array();
			$fastestfightsArray = $fastestFight;
			
			  $userDetJson = array('status'=>'1','message'=>'Success','result'=>$eventsDetails,'fastestfight'=>$fastestfightsArray);
					
		}
		else
		{
			$userDetJson = array('status'=>'0','message'=>'User Id should not be empty.','result'=>'');
		}
		return $userDetJson;
    	
    }
	
	//Get all user events 
	 public function totalevents($postarray)
    {		
		
		
		if(isset($postarray['userid']) && $postarray['userid'] != '')
		{		
			if(isset($postarray['eventid']) && $postarray['eventid'] != '')
			{
				$eventId = $postarray['eventid'];
				$where = ' AND ue.event_id = '.$eventId;
			}
			else
			{
				$eventId = '';
				$where = '';
			}
		
			$eventsModel = new User_Model_Events();
			$eventsDetails =  $eventsModel->getAllDashboardEventsDetailsByUserId($postarray['userid'],$where);
			
			
			
			$userDetJson = array('status'=>'1','message'=>'Success','result'=>$eventsDetails);			
					
		}
		else
		{
			$userDetJson = array('status'=>'0','message'=>'User Id should not be empty.','result'=>'');
		}
		return $userDetJson;
    	
    }
	
	
	//Get all user fighters 
	public function totalfighters($postarray)
    {		
	
		if(isset($postarray['userid']) && $postarray['userid'] != '')
		{		
			if(isset($postarray['eventid']) && $postarray['eventid'] != '')
			{
				$eventId = $postarray['eventid'];
				$where = ' AND ue.event_id = '.$eventId;
			}
			else
			{
				$eventId = '';
				$where = '';
			}
		
			$eventsModel = new User_Model_Events();
			$fightersDetails =  $eventsModel->getAllFighters($postarray['userid'],$where);
			
			$data = array();
		
			for($i=0; $i<sizeof($fightersDetails);$i++){				
				$data[] = $fightersDetails[$i]['fighter'];
			} 
				
			$dataNew = array();
			$dataNew = array_count_values($data);
			
			arsort($dataNew);
			
			$userDetJson = array('status'=>'1','message'=>'Success','result'=>$dataNew);			
					
		}
		else
		{
			$userDetJson = array('status'=>'0','message'=>'User Id should not be empty.','result'=>'');
		}
		return $userDetJson;
    	
    }
	
	
	//Get all user title fights 
	public function totaltitlefights($postarray)
    {		
		
		
		if(isset($postarray['userid']) && $postarray['userid'] != '')
		{		
			if(isset($postarray['eventid']) && $postarray['eventid'] != '')
			{
				$eventId = $postarray['eventid'];
				$where = ' AND ue.event_id = '.$eventId;
			}
			else
			{
				$eventId = '';
				$where = '';
			}
		
			$eventsModel = new User_Model_Events();
			$eventsDetails =  $eventsModel->getAllDashboardTitleFightersByUserId($postarray['userid'],$where);
			
			
			
			$userDetJson = array('status'=>'1','message'=>'Success','result'=>$eventsDetails);			
					
		}
		else
		{
			$userDetJson = array('status'=>'0','message'=>'User Id should not be empty.','result'=>'');
		}
		return $userDetJson;
    	
    }
	
	
	//Get all user graph details
	public function graphdetails($postarray)
    {		
		
		
		if(isset($postarray['userid']) && $postarray['userid'] != '')
		{	
			$where = '';
			if(isset($postarray['eventid']) && $postarray['eventid'] != '')
			{
				$eventId = $postarray['eventid'];
				$where .= ' AND ue.event_id = '.$eventId;
			}
			else
			{
				$eventId = '';
				$where = '';
			}
		
			
			//Getting all KO's.
			$eventsModel = new User_Model_Events();
			
			if(isset($postarray['type']) && $postarray['type'] != '')
			{
				$methodtype = $postarray['type'];
				
				if($methodtype == 'decision')
				{
					$type = ucfirst($methodtype);
				}
				else if($methodtype == 'submission')
				{
					$type = ucfirst($methodtype);
				}
				else if($methodtype == 'ko')
				{
					$type = ucwords($methodtype);
				}
				else if($methodtype == 'tko')
				{
					$type = ucwords($methodtype);
				}
				else
				{
					$userDetJson = array('status'=>'0','message'=>'Invalid Type','result'=>'');
				}
				
				if($type == 'Decision')
				{
					$where .= " AND ed.method NOT IN ('KO','TKO','Submission')";
				}
				else
					$where .= " AND ed.method = '".$type."'";
					
					
				$methodDetails = $eventsModel->getMethodByUserId($postarray['userid'],$where);
				$data = array();
				for($i=0; $i<sizeof($methodDetails);$i++)
					$data[] = htmlentities($methodDetails[$i]['method1']);
					
				$result = array_count_values($data);
			
				
				
				$userDetJson = array('status'=>'1','message'=>'Success','result'=>$result);					
					
					
			}
			else
			{
				$userDetJson = array('status'=>'0','message'=>'Not mentioned type of details you need.','result'=>'');
			}
    		
   						
		}
		else
		{
			$userDetJson = array('status'=>'0','message'=>'User Id should not be empty.','result'=>'');
		}
		return $userDetJson;
    	
    }
	
	public function fightplaces($postarray)
	{	
		
		if(isset($postarray['userid']) && $postarray['userid'] != '')
		{		
			if(isset($postarray['eventid']) && $postarray['eventid'] != '')
			{
				$eventId = $postarray['eventid'];
				$where = ' AND ue.event_id = '.$eventId;
			}
			else
			{
				$eventId = '';
				$where = '';
			}
		
			$usereventsModel = new User_Model_UserEvents();
			$fightPlaces = $usereventsModel->getFightPlaces($postarray['userid'], $where);			
			
			$userDetJson = array('status'=>'1','message'=>'Success','result'=>$fightPlaces);					
		}
		else
		{
			$userDetJson = array('status'=>'0','message'=>'User Id should not be empty.','result'=>'');
		}
		return $userDetJson;	
	}
	
	
	
	
	
	
	public function settings($postarray)
	{	
		
		
		if(isset($postarray['userid']) && $postarray['userid'] != '')
		{
			
			if((isset($postarray['password']) && $postarray['password'] != '') || (isset($_FILES['profile_photo']['name']) && $_FILES['profile_photo']['name'] != ''))
			{
				$uploadresult = array();
				$data = array();
				if(isset($_FILES['profile_photo']['name']) && $_FILES['profile_photo']['name'] != '')
				{				
					$uploadresult = sapp_Global::imageUploadAfterResize();
				}				
				
				if((isset($postarray['password']) && $postarray['password'] != ''))
				{
					$password = md5($postarray['password']);
					$data['user_password'] = $password;						
				}
				if(isset($uploadresult['img']) && $uploadresult['img'] != '')
				{
					$data['profile_photo'] = $uploadresult['img'];
				}
				$where = 'id = "'.$postarray['userid'].'"';
				$usersModel = new User_Model_Users();
				$changepwd = $usersModel->edituserPassword($data, $where);				
				$userDetJson = array('status'=>'1','message'=>'Success','result'=>'Updated successfully.');	
			}
			else
			{
				$userDetJson = array('status'=>'0','message'=>'Either Password or Profile Photo should change.','result'=>'');
			}
							
		}
		else
		{
			$userDetJson = array('status'=>'0','message'=>'User Id should not be empty.','result'=>'');
		}
		return $userDetJson;	
	}
	
	
    public 	function guid(){
	        
    	$charid = strtoupper(md5(uniqid(rand(), true)));
	    $hyphen = '';
        $uuid = substr($charid, 0, 8).$hyphen
	           .substr($charid, 8, 4).$hyphen
	           .substr($charid,12, 4).$hyphen
	           .substr($charid,16, 4).$hyphen
	           .substr($charid,20,12);
	    return $uuid;
	}
	
}

?>