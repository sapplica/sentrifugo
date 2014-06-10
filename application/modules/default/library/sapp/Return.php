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
	  // echo "suresh";exit;
	   $zendobj = Zend_Controller_Front::getInstance();       
    }

	public function test()
	{
		//echo 'suresh';exit;
		$testString = array('status'=>'0','message'=>'Created an action for restful web service.','result'=>'');
		//echo $testString;exit;
		//$this->_helper->json($testString);
		//echo "<pre>";print_r($testString);exit;
		return $testString;
	}
	
	
	
	
	public function login($postarray)
	{
		//echo 'suresh';exit;		
		//$postarray = $this->getRequest()->getParams();
		
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
		//$this->_helper->json($userDetJson);
		return $userDetJson;
	}
	
	public function forgotpwd($postarray)
	{
		//echo 'suresh';exit;		
		//$postarray = $this->getRequest()->getParams();
		
		
    	if(isset($postarray['email']) && $postarray['email'] != '') {	
	
				
        		$user= new Login_Model_Users($db);
				$data = $user->getUserObject($postarray['email']);
				
				if(!$data){				
					$userDetJson = array('status'=>'0','message'=>'The information you entered is incorrect. Please try again.','result'=>'');				
				}
				else
				{				
				
					if($data['user_role_id'] != 2)
					{
						$userDetJson = array('status'=>'0','message'=>'Not a priviliged user.','result'=>'');
					}
					else if($data['is_blocked'] == 1)
					{
						$userDetJson = array('status'=>'0','message'=>'User has been blocked.','result'=>'');
					}
					else if($data['is_active'] == 0)
					{
						$userDetJson = array('status'=>'0','message'=>'Not an active user.','result'=>'');
					}
					else
					{					
					
						$email = $data['email'];
						$options['subject'] = 'STUBSTATS: Password change request';
						$options['header'] = 'Password change request';
						$options['fromEmail'] = Zend_Registry::get('donotreply');
						$options['fromName'] = 'Do-not-reply';
						$options['toEmail'] = $data['email'];  
						$options['toName'] = $data['first_name'];
						$date = Zend_Date::now();
						$password = md5($date);
						
							//Confirmation Link
							$zendobj = Zend_Controller_Front::getInstance();
						
							$uniqueId = $this->guid();
							
							$base_url = $zendobj->getRequest()->getHttpHost() . $zendobj->getRequest()->getBaseUrl();
							
							//echo $base_url;exit;
							$link = "http://".$base_url."/services/services/confirmlink/auth/db666a136c1d2c6f54fa650c432a1018/param/".$uniqueId;
							$passwordModel = new Login_Model_Password();
							$dataConfirm['email'] = $data['email'];
							$dataConfirm['token'] = $uniqueId;
							$dataConfirm['is_confirmed'] = 0;
							$dataConfirm['created'] = Zend_Registry::get('currentdate');
							$dataConfirm['modified'] =Zend_Registry::get('currentdate');
							$passwordModel->confirmLinkInsert($dataConfirm);
						//End of Confirmation Link
						
						$logo = "http://".$base_url.Zend_Registry::get('logo_url');
						
						//email templates
						//$view = $this->getHelper('ViewRenderer')->view;
						
						$this->view->name=$data['first_name'];
						$this->view->logo = $logo;
						$this->view->link = $link;
						//$html = $view->render('login/templates/forgotpasswordmail.phtml');
							$html = '<tr><td bgcolor="#F1F1F1" style="border-bottom:0;" align="left"><div style="padding: 15px 8px;text-align: left;">
		  Hello <strong>'.ucfirst($this->name).' </strong>,<br />
		  <div style="padding:20px 0 20px 0;">We received a request for change of password. To confirm your request for change of password, please <a href="'.$this->link.'" target="_blank">click here</a>.</div>
			<div>If you haven\'t made this request, please ignore this message.</div>
			</div>
			</td>
	</tr>';
						// end email templates
						
						$options['message'] = $html;					
						sapp_Mail::_email($options);
					
					
						$msg = "You will receive an email to the registered email address ".$email." shortly .";
					
						$userDetJson = array('status'=>'1','message'=>$msg,'result'=>$data);
					}					
				
				}
			
		}
		else
		{
			$userDetJson = array('status'=>'0','message'=>'Email should not be empty.','result'=>'');
		}		
		
		//$testString = array(''=>'','result'=>'Created an action for restful web service.');
		//echo $testString;exit;
		return $userDetJson;
	}
	
	
	
	public function confirmlink($postarray){
		//$token = $this->_request->getParam('param');	
		if(isset($postarray['param']) && $postarray['param'] != '')
		{
			$token = $postarray['param'];
			$passwordModel = new Login_Model_Password();
			$data = $passwordModel->getObjectByToken($token);
			if(!$data){	
				$userDetJson = array('status'=>'0','message'=>'Link expired or not a valid link.','result'=>'');						
			}else{
				
				$id = $data['id'];
				$dataConfirm = array();
				$dataConfirm['is_confirmed'] = 1;
				$dataConfirm['modified'] = Zend_Registry::get('currentdate');
				$passwordModel->updatePasswordActivity($dataConfirm, $id);
				//Send password mail
				$user = new Login_Model_Users();
				$dataUser = $user->getUserObject($data['email']);
				
				//Update user table
				$dataUserUpdate = array();
				$dataConfirm['email'] = $dataUser['email'];
				$dateObj = new Zend_Date();
				$date = $dateObj->getTimestamp();
				
				$passwordPlain = sapp_Global::generatePassword();
				$password = md5($passwordPlain);
				
				$dataUserUpdate['user_password'] = $password ;
				$dataUserUpdate['modified'] = Zend_Registry::get('currentdate');
				$userID = $dataUser['id'];
				$user->updatePassword($dataUserUpdate,$dataUser['email']);
				
				//password mail
				$options['subject'] = 'STUBSTATS : Your new login credentials';
				$options['header'] = 'Your new login credentials';
				$options['fromEmail'] = Zend_Registry::get('donotreply');
				$options['fromName'] = 'Do-not-reply';
				$options['toEmail'] = $dataUser['email'];  
				$options['toName'] = $dataUser['first_name'];
				
				$zendobj = Zend_Controller_Front::getInstance();
				
				$base_url = $zendobj->getRequest()->getHttpHost() . $zendobj->getRequest()->getBaseUrl();
				$loginURL = "http://".$zendobj->getRequest()->getHttpHost() . $zendobj->getRequest()->getBaseUrl();
				
				$logo = "http://".$base_url.Zend_Registry::get('logo_url');
						
				$view = $this->getHelper('ViewRenderer')->view;
						
				$this->view->dataUser = $dataUser;
				$this->view->loginURL = $loginURL;
				$this->view->logo=$logo;
				$this->view->passwordPlain=$passwordPlain;
				//$html = $view->render('templates/confirmationlink.phtml');
				
				$html = '<div class="tabscontainer"><div class="con_side"><div class="curvedContainer"><div  id="gridblock" class="activationsuccess">	<div class="activecomplete"><?php echo $this->message;?></div><div class="thankstxt">Please <a href="'.$this->baseUrl("").'" class="activationsuccesslink">click here</a> to login.</div></div></div></div></div>';
					
				$options['message'] = $html;					
				sapp_Mail::_email($options);			
				//$this->_helper->flashMessenger->addMessage("Confirmation message.");
				//$this->_redirect('/login?flag=1');
				$msg = 'A new password has been sent to your registered e-mail address.';
				$userDetJson = array('status'=>'1','message'=>$msg,'result'=>$data);
			}
		}
		else
		{
			$userDetJson = array('status'=>'0','message'=>'No parameters found from email.','result'=>'');
		}
		return $userDetJson;
	}
	
	public function userstats($postarray)
	{
		//$postarray = $this->getRequest()->getParams();
		
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
		//$postarray = $this->getRequest()->getParams();		
		
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
		//$postarray = $this->getRequest()->getParams();			
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
			
			
			
			//$eventsDetails[count($eventsDetails)]['fastestfight'] =   $fastestFight;
			$fastestfightsArray = array();
			$fastestfightsArray = $fastestFight;
			//$final_array = array_merge($eventsDetails,$fastestfightsArray);
			
			//echo "<pre>";print_r($final_array);exit;
			
			//$userDetJson = array('status'=>'1','message'=>'Success','result'=>$final_array);
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
		//$postarray = $this->getRequest()->getParams();	
		
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
			
			//echo "<pre>suresh";print_r($eventsDetails);exit;
			
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
			//echo "<pre>";print_r($dataNew);exit;
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
		//$postarray = $this->getRequest()->getParams();	
		
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
			
			//echo "<pre>suresh";print_r($eventsDetails);exit;
			
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
		//$postarray = $this->getRequest()->getParams();	
		
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
			
				//echo "<pre>suresh";print_r($eventsDetails);exit;
				
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
		//$postarray = $this->getRequest()->getParams();		
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
			//echo "<pre>suresh";print_r($eventsDetails);exit;			
			$userDetJson = array('status'=>'1','message'=>'Success','result'=>$fightPlaces);					
		}
		else
		{
			$userDetJson = array('status'=>'0','message'=>'User Id should not be empty.','result'=>'');
		}
		return $userDetJson;	
	}
	
	
	public function addevent($postarray)
	{	
		//$postarray = $this->getRequest()->getParams();		
		if(isset($postarray['userid']) && $postarray['userid'] != '')
		{	

			if(isset($postarray['eventid']) && $postarray['eventid'] != '')
			{
				$usereventsmodel = new User_Model_UserEvents();
				$usermodel = new User_Model_Users();
				
				$eventid = $postarray['eventid'];
								
				$date=new Zend_Date();
				$usereventsmodeldata = $usereventsmodel->checkAddeddevent($postarray['userid'], $eventid);
				
				
				$data=array(
							'user_id'=>$postarray['userid'],
							'event_id'=>$eventid,
							'watched_on'=>null,
							'is_active'=>1,
							'created'=>$date->get('yyyy-MM-dd HH:mm:ss'),
							'modified'=>$date->get('yyyy-MM-dd HH:mm:ss'));
				
				if($usereventsmodeldata[0]['count']>0){
					unset($data['created']);
					$where=array('user_id=?'=>$postarray['userid'],
								'event_id=?'=>$eventid);	
				}else{
					$where='';
				}
				$usereventsname = $usereventsmodel->checkEventName($eventid);
				$userfacebooknotification = $usermodel->getUserNotificationForFb($postarray['userid']);
				//echo "<pre>";print_r($userfacebooknotification);exit;
				
				$id=$usereventsmodel->addOrUpdateUserEvents($data, $where);
				
				//add new user activity
				$activitymodel = new Admin_Model_Activities();
				
				$userDetails = $usermodel->getParticularUserDetailsByID($postarray['userid']);
					  
				$activity_data=array(
							'user_id'=>$postarray['userid'],
							'activity_id'=>12,
							'record_id'=>$eventid,
							'text1'=>$userDetails[0]['first_name']." added an event ".$usereventsname[0]['eventname']
							);
								
				$activitymodel->inserActivity($activity_data);
				//End add new user activity
			
				$message = $usereventsname[0]['eventname']. " event added successfully.";
						
				$userDetJson = array('status'=>'1','message'=>'Success','result'=>$message);
			}
			else
			{
				$userDetJson = array('status'=>'0','message'=>'Event Id should not be empty.','result'=>'');
			}
		}
		else
		{
			$userDetJson = array('status'=>'0','message'=>'User Id should not be empty.','result'=>'');
		}
		return $userDetJson;	
	}
	
	
	
	public function register($postarray)
	{
		//echo "<pre>register";print_r($postarray);exit;
		
		if(isset($postarray) && isset($postarray['email']) &&  $postarray['email'] != '' && isset($postarray['password'])  && $postarray['password'] != '')
		{
			$usermodel = new User_Model_Users();
			$emailexist = $usermodel->checkEmailExists($postarray['email']);
			if($emailexist[0]['cnt']<1)
			{		
		
				if(isset($postarray['first_name']) &&  $postarray['first_name'] != '' && isset($postarray['last_name']) &&  $postarray['last_name'] != '')
				{
					$first_name = $postarray['first_name'];
					$last_name = $postarray['last_name'];    			
					$email = $postarray['email'];
					if(isset($postarray['fbid']))
						$fbid = $postarray['fbid'];
					else
						$fbid = '';
					if(isset($postarray['fbfriends']))
						$fbfriends = $postarray['fbfriends'];
					else
						$fbfriends = '';
					if(isset($postarray['access_token']))
						$access_token = $postarray['access_token'];
					else
						$access_token = '';
					if(isset($postarray['country_id']))
						$country_id = $postarray['country_id'];
					else
						$country_id = '';
					if(isset($postarray['state_id']))
						$state_id = $postarray['state_id'];
					else
						$state_id = '';
						
						
					$uploadresult = array();
					$profile_photo = '';
					if(isset($_FILES['profile_photo']['name']) && $_FILES['profile_photo']['name'] != '')
					{				
						$uploadresult = sapp_Global::imageUploadAfterResize();
					}	
					if(isset($uploadresult['img']) && $uploadresult['img'] != '')
					{
						$profile_photo = $uploadresult['img'];
					}	
						
						
					$user_password = $postarray['password'];
					
					$data = array(
							'first_name'=>$first_name,
							'last_name'=>$last_name,
							'forum_user_name'=>($forum_user_name !='')?$forum_user_name:NULL,
							'email'=>$email,
							'fbid' =>($fbid !='')?$fbid:NULL,
							'fbfriends' =>($fbfriends !='')?$fbfriends:NULL,
							'access_token' =>($access_token !='')?$access_token:NULL,						
							'country_id'=>($country_id !='')?$country_id:NULL,
							'state_id'=>($state_id !='')?$state_id:NULL,						
							'user_role_id'=> 2,						
							'user_password'=>md5($user_password),
							'is_activated'=> 0,
							'is_active'=> 1,
							'created'=>Zend_Registry::get('currentdate'),
							'modified'=>Zend_Registry::get('currentdate'),
							'profile_photo'=>($profile_photo !='')?$profile_photo:NULL
					);
					
					
					$where = "";
					$membermodel = new Admin_Model_Members();

					//if($newPassword == $passwordAgain){
						
						$userId = $membermodel->addOrUpdateRegistration($data, $where);
						
						$activitymodel = new Admin_Model_Activities();
			  
						$activity_data=array(
									'user_id'=>$userId,
									'activity_id'=>1,
									'record_id'=>$userId
									);
						if($fbid != ''){
							$activity_data['activity_id']=2;	
						}
									
						$activitymodel->inserActivity($activity_data);
			  
						//Mail
						$zendobj = Zend_Controller_Front::getInstance();				
						$base_url = $zendobj->getRequest()->getHttpHost() . $zendobj->getRequest()->getBaseUrl();
						
						$link = "http://".$base_url."/services/index/service/useractivation/param/".sapp_Global::_encrypt($userId."_sapplica");
						
						$text = "<tr><td bgcolor='#F1F1F1' style='border-bottom:0;' align='left'><div style='padding: 15px 8px;text-align: left;'>	  Hello <strong>".ucfirst($first_name." ".$last_name)." </strong>,<br /><div style='padding:20px 0 10px 0;'>Please click on the below link to activate your account.</div><div style='padding:20px 0 10px 0;'>".$link."</div><div>If you haven't made this request, please ignore this message.</div></div></td></tr>";
						
						$options['subject'] = 'StubStats: User Activation';
						$options['header'] = 'Message';
						$options['fromEmail'] = Zend_Registry::get('donotreply');
						$options['fromName'] = 'Do-not-reply';
						$options['toEmail'] = $email; 
						$options['message'] = $text;
						sapp_Mail::_email($options);
					
						$userDetJson = array('status'=>'1','message'=>'Success','result'=>'User Registration completed successfully.');
						
				}
				else
				{
					$userDetJson = array('status'=>'0','message'=>'First Name and Last Name fields should not be empty.','result'=>'');
				}
			}
			else
			{
				$userDetJson = array('status'=>'0','message'=>'Email already exists.','result'=>'');
			}
		
		}
		else
		{
			$userDetJson = array('status'=>'0','message'=>'Email and Password fields should not be empty.','result'=>'');
		}

		
		return $userDetJson; 
	}
	public function useractivation($postarray)
	{	
		//$postarray = $this->getRequest()->getParams();		
		if(isset($postarray['param']) && $postarray['param'] != '')
		{			
			$paramString = sapp_Global::_decrypt($postarray['param']);
			$paramArray = explode('_', $paramString);
			$membermodel = new Admin_Model_Members();
			
			$membermodeldata=$membermodel->getUserdataById($paramArray[0]);
			$email = $membermodeldata[0]['email'];
			if($membermodeldata[0]['is_activated']==1){
				$message='Your account is already activated.';
				$flag='no';
			}elseif($membermodeldata[0]['is_blocked']==1){
				$message='Your account is blocked.';
				$flag='yes';
			}else{
				
				$password=sapp_Global::generatePassword();
				
				$data = array(
							//'user_password'=>md5($password),
							'is_activated'=> 1,
							'modified'=>Zend_Registry::get('currentdate')
						);
				$where = array('id = ?' => $paramArray[0]);
				
				
				$flag=$membermodel->addOrUpdateRegistration($data, $where);
				
				//add new user activity
				  $activitymodel = new Admin_Model_Activities();
				  
				  $session_data=array(
							'user_id'=>$membermodeldata[0]['id'],
							'activity_id'=>11,
							'record_id'=>$membermodeldata[0]['id'],
							'text1'=>$membermodeldata[0]['first_name']." profile has been activated "
							);
				  
				  $activitymodel->inserActivity($session_data);
				  //End add new user activity
				  
				if($flag!=''){					
					$memberdata=$membermodeldata;
					$password=$password;
					$text = " <tr><td bgcolor='#F1F1F1' style='border-bottom:0;' align='left'><div style='padding: 15px 8px;text-align: left;'>	  Hello <strong>".ucfirst($memberdata[0]['first_name']." ".$this->memberdata[0]['last_name'])." </strong>,<br /> <div style='padding:20px 0 10px 0;'>Your account has been activated successfully.</div><div style='padding:5px 0 10px 0;'>Please <a href=".sapp_Global::_getHostBaseURL().">click here</a>login.</div><div>If you haven't made this request, please ignore this message.</div></div></td></tr>";
					$options['subject'] = 'StubStats: Account Activation';
					$options['header'] = 'Account Activated';
					$options['fromEmail'] = Zend_Registry::get('donotreply');
					$options['fromName'] = 'Do-not-reply';
					$options['toEmail'] = $email; 
					$options['message'] = $text;
					sapp_Mail::_email($options);
				}
				$message='Your account has been activated successfully.';
				$flag='no';	
			}			
			$userDetJson = array('status'=>'1','message'=>'Success','result'=>$message);					
		}
		else
		{
			$userDetJson = array('status'=>'0','message'=>'Parameter Should not be empty.','result'=>'');
		}
		return $userDetJson;	
	}
	
	
	public function settings($postarray)
	{	
		//$postarray = $this->getRequest()->getParams();
		//echo "<pre>"; print_r($postarray); exit;	
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