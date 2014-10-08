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
 * 
 * @name IndexController
 * 
 * IndexController is responsible to make service based on the given inputs
 * 
 * @author Suresh Babu Bhupathi
 * @version 1.0

 *
 */
class Services_ServicesController extends Zend_Controller_Action
{
	/**
	 * Init
	 * 
	 * @see Zend_Controller_Action::init()
	 */
    public function init()
    {
	  //echo "suresh";exit;
       $this->_helper->viewRenderer->setNoRender(true);
	   $this->_helper->getHelper('layout')->disableLayout();
    }
	
	 public function preDispatch()
    {
      //$this->config = new Zend_Registry::get("../");	
	  if(! $this->authAccepted())
	  {
		//echo "suresh";exit;
		//throw new Exception("Your Private Key is invalid.");
		$testString = array('status'=>'0','message'=>'Your Private Key is invalid.','result'=>'');
		//echo $testString;exit;
		$this->_helper->json($testString);
	  }
    }
	
	public function testAction()
	{
		//echo 'suresh';exit;
		$testString = array('result'=>'Created an action for restful web service.');
		//echo $testString;exit;
		$this->_helper->json($testString);
	}
	
	
	private function authAccepted()
	{
		$keys = Zend_Registry::get('config.services');		
		$this->clientKey = $keys['testapp']['secret'];		
		$client = new sapp_HttpClient("testapp","sapplica");		
		$signature = $client->signArguments($keys);	
		if($signature == 'f229e1329e659c4011540b04a22a29d3')
			return true;			
		
		return false;
	}
	
	
	public function loginAction()
	{
		//echo 'suresh';exit;		
		$postarray = $this->getRequest()->getParams();
		
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
		
		//$testString = array(''=>'','result'=>'Created an action for restful web service.');
		//echo $testString;exit;
		$this->_helper->json($userDetJson);
	}
	
	
	
	
	
	
	
	public function userstatsAction()
	{
		$postarray = $this->getRequest()->getParams();
		
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
		$this->_helper->json($userDetJson);	
	
	}
	
	public function tracklistAction()
	{	
		$postarray = $this->getRequest()->getParams();		
		
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
			
			//echo "<pre>";print_r($final_arr);exit;
		
			$userDetJson = array('status'=>'1','message'=>'Success','result'=>$final_arr);
		}
    	else
		{
			$userDetJson = array('status'=>'0','message'=>'User Id should not be empty.','result'=>'');
		}
		$this->_helper->json($userDetJson);
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

