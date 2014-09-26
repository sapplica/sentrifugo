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


class Default_IndexController extends Zend_Controller_Action
{

	private $options;
	public function preDispatch()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('editforgotpassword', 'json')->initContext();
		$ajaxContext->addActionContext('calculatedays', 'json')->initContext();
		$ajaxContext->addActionContext('calculatebusinessdays', 'json')->initContext();
		$ajaxContext->addActionContext('calculatecalendardays', 'json')->initContext();
		$ajaxContext->addActionContext('fromdatetodate', 'json')->initContext();
		$ajaxContext->addActionContext('fromdatetodateorg', 'json')->initContext();
		$ajaxContext->addActionContext('gettimeformat', 'json')->initContext();
		$ajaxContext->addActionContext('medicalclaimdates', 'json')->initContext();
		$ajaxContext->addActionContext('chkcurrenttime', 'json')->initContext();
		$ajaxContext->addActionContext('createorremoveshortcut', 'json')->initContext();
		$ajaxContext->addActionContext('parsecsv', 'json')->initContext();
		$ajaxContext->addActionContext('sessiontour', 'json')->initContext();
		$ajaxContext->addActionContext('getissuingauthority', 'json')->initContext();
		$ajaxContext->addActionContext('checkisactivestatus', 'json')->initContext();
		$ajaxContext->addActionContext('updatetheme', 'json')->initContext();
	}

	/**
	 * Init
	 *
	 * @see Zend_Controller_Action::init()
	 */
	public function init()
	{
		$this->_options= $this->getInvokeArg('bootstrap')->getOptions();
	}

	/**
	 * @name indexAction
	 *
	 * This method is used to display the login form and to display form errors based on given inputs
	 *
	 *  @author Mainak
	 *  @version 1.0
	 */
	public function indexAction()
	{
		$msg = $this->getRequest()->getParam('msg');
		if($msg !='')
		$this->view->msg = $msg;

		$this->view->messages = $this->_helper->flashMessenger->getMessages();
			
	}
	/**
	 * @name loginAction
	 *
	 * This method is used to display the login data errors
	 *
	 * @author Mainak
	 * @version 1.0
	 *
	 * values used in this method
	 * ==========================
	 * @param username => Email given in Login Form
	 * @param password => Password given in Login Form
	 */
        
	public function loginpopupsaveAction()
	{
		$emailParam = $this->getRequest()->getParam('username');
		$opt= array (
                    'custom' => array (
                    'timeout' => $this->_options['auth']['timeout']
		)
		);
		$options=array();
		$options['username']= $this->getRequest()->getParam('username');
		$options['user_password']= $this->getRequest()->getParam('password');

		$usersModel = new Default_Model_Users();
		$userData = $usersModel->isActiveUser($options['username']);
		$check = 0;
		foreach($userData as $user) {
			$check = ($user['count'] == 1)? 1: 0;
		}

		if(!$check)
		{
			$userStatusArr = $usersModel->getActiveStatus($options['username']);
			if(!empty($userStatusArr))
			{
				$userStatus = $userStatusArr[0]['status'];
				$islockaccount = $userStatusArr[0]['isaccountlock'];
				if($userStatus == 0)
				$this->_helper->getHelper("FlashMessenger")->addMessage("Login failed. Employee has been inactivated from the organization.");
				else if($userStatus == 2)
				$this->_helper->getHelper("FlashMessenger")->addMessage("Login failed. Employee has resigned from the organization.");
				else if($userStatus == 3)
				$this->_helper->getHelper("FlashMessenger")->addMessage("Login failed. Employee has left the organization.");
				else if($userStatus == 4)
				$this->_helper->getHelper("FlashMessenger")->addMessage("Login failed. Employee has been suspended from the organization.");
				else if($userStatus == 5)
				$this->_helper->getHelper("FlashMessenger")->addMessage("Login failed. Employee deleted.");
				else if($islockaccount == 1)
				$this->_helper->getHelper("FlashMessenger")->addMessage("Login failed. Employee has been locked.");
			}else
			{
				$this->_helper->getHelper("FlashMessenger")->addMessage("The username or password you entered is incorrect.");
			}
				
			$this->_redirect('index');
		}
			
		$auth= Zend_Auth::getInstance();

		try
		{
			$db = $this->getInvokeArg('bootstrap')->getResource('db');
			$user= new Default_Model_Users($db);

			if ($user->isLdapUser(sapp_Global::escapeString($options['username']))) {

				$options['ldap']= $this->_options['ldap'];
				$authAdapter= Login_Auth::_getAdapter('ldap', $options);
					
			} else {

				$options['db']= $db;
				$options['salt']= $this->_options['auth']['salt'];
				if($isemail = filter_var( $options['username'], FILTER_VALIDATE_EMAIL ))
					$authAdapter= Login_Auth::_getAdapter('email', $options);
				else
					$authAdapter= Login_Auth::_getAdapter('db', $options);
					
			}

			$result = $auth->authenticate($authAdapter);

			if ($result->isValid()) {

				$admin_data = $user->getUserObject($options['username']);
				
				$auth->getStorage()->write($admin_data);
				$storage = $auth->getStorage()->read();
					
				$dataTmp = array();

				$dataTmp['userid'] = ($storage->id)?$storage->id:0;
				$dataTmp['emprole'] = ($storage->emprole)?$storage->emprole:0;
				$dataTmp['group_id'] = ($storage->group_id)?$storage->group_id:0;
				$dataTmp['employeeId'] = ($storage->employeeId)?$storage->employeeId:0;
				$dataTmp['emailaddress'] = ($storage->emailaddress)?$storage->emailaddress:'';
				$dataTmp['userfullname'] = ($storage->userfullname)?$storage->userfullname:'';
				$dataTmp['logindatetime'] = gmdate("Y-m-d H:i:s");
				if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
					$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
				} else {
					$ip_address = $_SERVER['REMOTE_ADDR'];
				}
				if($ip_address == '::1'){
					$ip_address = '127.0.0.1';
				}
				$dataTmp['empipaddress'] = $ip_address;
				$dataTmp['profileimg'] = ($storage->profileimg)?$storage->profileimg:'';

				$lastRecordId = $usersModel->addUserLoginLogManager($dataTmp);

				
				$orgImg = $usersModel->getOrganizationImg(); 

				$organizationImg = new Zend_Session_Namespace('organizationinfo');
				if(empty($organizationImg->orgimg))
				{
					$organizationImg->orgimg = $orgImg;
				}
				if(!isset($organizationImg->hideshowmainmenu )){
				    $organizationImg->hideshowmainmenu = 1;
				}

				/*** Previous URL redirection after login - start ***/
				$prevUrl = new Zend_Session_Namespace('prevUrl');
				
				if(isset($prevUrl->prevUrlObject) && $prevUrl->prevUrlObject[0] !='/index/logout'){
					header('Location:'.$prevUrl->prevUrlObject[0]);
					Zend_Session::namespaceUnset('prevUrl');
					exit;
				 /*** Previous URL redirection after login - end ***/
				}
				else
				$this->_redirect('welcome');

			}
			else
			{
				$this->_helper->getHelper("FlashMessenger")->addMessage("The username or password you entered is incorrect.");
				$this->_redirect('index');
			}
		}
		catch(Exception $e)
		{
			echo $e->getMessage();
		}
	}

	public function welcomeAction()
	{
		try
		{
			$call = $this->_getParam('call');
			if($call == 'ajaxcall')
			$this->_helper->layout->disableLayout();

			$emptyRoles = 0;$dataemptyFlag='';$extraParam1='';$extraParam2='';$extraParam3='';$extraParam4='';
			$loginUserId ='';$loginRoleId ='';$loginuserGroup ='';$data = Array();$datacontent = '';
			$defaultOrderBy = "";

			$auth = Zend_Auth::getInstance();
			if($auth->hasIdentity())
			{
				$loginUserId = $auth->getStorage()->read()->id;
				$loginRoleId=$auth->getStorage()->read()->emprole;
				$loginuserGroup=$auth->getStorage()->read()->group_id;
			}
			$objname = $this->_getParam('objname');
			$refresh = $this->_getParam('refresh');
			$widgetsModel = new Default_Model_Widgets();

			if($call == 'ajaxcall')
			$widgetsArr = array($objname);
			else
			$widgetsArr = $widgetsModel->getWidgets($loginUserId,$loginRoleId);

			if(!empty($widgetsArr))
			{
				for($i = 0; $i < sizeof($widgetsArr); $i++)
				{
					//Url
					$url =$widgetsArr[$i]['url'];

					//objectname
					$objectName = ltrim($widgetsArr[$i]['url'],'/');

					//menuName
					$menuName =$widgetsArr[$i]['menuName'];

					//model name
					$modelName = $widgetsArr[$i]['modelName'];

					//Default order by
					if($widgetsArr[$i]['defaultOrderBy'] != "")
					$defaultOrderBy = $widgetsArr[$i]['defaultOrderBy'];
					else
					$defaultOrderBy = "";

					//	Flag for Leaves status....	like approved,rejected,pending,cancel
					if($url == "/rejectedleaves")
					{
						$extraParam1= "rejectedleaves";
						$extraParam2 = "rejected";
					}
					if($url == "/approvedleaves")
					{
						$extraParam1= "approvedleaves";
						$extraParam2 = "approved";
					}
					if($url == "/pendingleaves")
					{
						$extraParam1= "pendingleaves";
						$extraParam2 = "pending";
					}
					if($url == "/cancelleaves")
					{
						$extraParam1= "cancelleaves";
						$extraParam2 = "cancel";
					}
					if($url == "/manageremployeevacations")
					{
						$extraParam1= "manageremployeevacations";
						$extraParam2 = "";
					}

					if($url == "/myemployees")
					{
						$extraParam1=$loginUserId;
						$extraParam2=$loginUserId;
					}
					if($url == "/holidaydates")
					{
						$extraParam1="holidaydates";
						$extraParam2="";
					}
					if($url == "/myholidaycalendar")
					{
						$extraParam1= "myholidaycalendar";
						$extraParam2= "";
					}

					if($url == "/employee")
					{
						$extraParam='';
						$extraParam2= $loginUserId;
					}
					if($url == "/myemployees")
					{
						$extraParam1=$loginUserId;$extraParam2= $loginUserId;
							
					}
					if($url == "/empleavesummary")
					{
						$extraParam1= "empleavesummary";
							
					}
					if($url == "/approvedrequisitions")
					{
						$extraParam1= $loginuserGroup;
						$extraParam4 = 'Yes';$extraParam2=2;//reqType
					}
					if($url == "/requisition")
					{
						$extraParam1= $loginuserGroup;
						$extraParam4 = 'Yes';$extraParam2=1;//reqType
					}
					if($url == "/empscreening")
					{
						$extraParam1= 1;
					}
					if($url == "/rejectedrequisitions")
					 {
					 $extraParam1= $loginuserGroup;
					 $extraParam4 = 'Yes';$extraParam2=3;//reqType
					 }

					if($refresh == 'refresh')
					{
						$sort = 'DESC';$by = $defaultOrderBy;$perPage = DASHBOARD_PERPAGE;$pageNo = 1;$searchData = '';
					}
					else
					{
						$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
						$by = ($this->_getParam('by')!='')? $this->_getParam('by'):$defaultOrderBy;
						$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
						$pageNo = $this->_getParam('page', 1);
						$searchData = $this->_getParam('searchData');
					}
					$menuWidgetModel = new $modelName();
					if($objectName == 'employee')
					$extraParam1=$loginUserId;

if($modelName == 'Default_Model_Requisition')
	$dataTmp = $menuWidgetModel->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$loginUserId,$extraParam1,$extraParam2,'',$extraParam4);
	else 
	$dataTmp = $menuWidgetModel->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,'Yes',$extraParam1,$extraParam2,$extraParam3,$extraParam4);

					if($dataTmp['tablecontent'] == "emptyroles")
					$emptyRoles = 1;
					else
					$emptyRoles = 0;
					$dataTmp['emptyRoles'] = $emptyRoles;
					$dataTmp['objectname'] = $objectName;
					$dataTmp['dataemptyFlag'] = $dataemptyFlag;
					$dataTmp['menuName'] = $menuName;
					$dataTmp['userid'] = $loginUserId;
					$dataTmp['dashboardcall'] = 'Yes';

					array_push($data,$dataTmp);
				}
				$datacontent = 'full';
			}
			else
			$datacontent = 'null';
		}
		catch(Exception $e)
		{
			echo $e->getMessage();
		}

		$this->view->call = $call ;
		$this->view->datacontent = $datacontent;
		$this->view->dataArray = $data;
	}
	
	/**
	 * @name logoutAction
	 *
	 * logoutAction is used to clear the session data to make logout Action
	 *
	 * @author Mainak
	 * @version 1.0
	 *
	 * values used in this method
	 * ==========================
	 * user_id		=> Logged in user Id
	 * emailid		=> Logged in user Email Id
	 */
	public function logoutAction() {

		$sessionData = sapp_Global::_readSession();
		Zend_Session::namespaceUnset('recentlyViewed');
		Zend_Session::namespaceUnset('organizationinfo');
		$auth = Zend_Auth::getInstance();
		$auth->clearIdentity();
		$this->_redirect('index');
	}

	public function clearsessionarrayAction()
	{
            $pagename = $this->_request->getParam('name');
            $pagelink = $this->_request->getParam('link');
            $recentlyViewed = new Zend_Session_Namespace('recentlyViewed');
            $successmessage = array();
            $successmessage['result']= '';
            $successmessage['is_empty']= 'no';
            if(isset($recentlyViewed->recentlyViewedObject))
            {
                for($i=0;$i<sizeof($recentlyViewed->recentlyViewedObject);$i++)
                {
                    if($recentlyViewed->recentlyViewedObject[$i]['url'] == $pagelink )
                        unset($recentlyViewed->recentlyViewedObject[$i]);
                    $recentlyViewed->recentlyViewedObject = array_values($recentlyViewed->recentlyViewedObject);
                    $successmessage['result']= 'success';
                }
            }
            if(empty($recentlyViewed->recentlyViewedObject))
            {
                $successmessage['is_empty']= 'yes';
            }
            $this->_helper->json($successmessage);
	}



	public function forcelogoutAction(){
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('forcelogout', 'json')->initContext();

		$id = $this->_request->getParam('id');

		$usermodel = new Login_Model_Users();
		$usermodel->forcelogout($id);
			
		$this->_helper->json(array('result'=>'logged out'));
	}


	public function browserfailureAction(){

	}


	public function sendpasswordAction()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('sendpassword', 'json')->initContext();

		$emailaddress = $this->_request->getParam('emailaddress');
		$user= new Default_Model_Users();

		$result['result'] = '';
		$result['message'] = '';
			
		if($emailaddress)
		$isvalidemail = filter_var($emailaddress, FILTER_VALIDATE_EMAIL );

		if($emailaddress == '')
		{
			$result['result'] = 'error';
			$result['message'] = 'Please enter email.';
		}else if($emailaddress != $isvalidemail)
		{
			$result['result'] = 'error';
			$result['message'] = 'Please enter valid email.';
		}else
		{
			$emailexists = $user->getEmailAddressCount($emailaddress);
			$emailcount= $emailexists[0]['emailcount'];
			$username = $emailexists[0]['userfullname'];
			if($emailcount >0)
			{
				$generatedPswd = uniqid();
				$encodedPswd = md5($generatedPswd);
				$user->updatePwd($encodedPswd,$emailaddress);
				$options['subject'] = APPLICATION_NAME.' Password Change';
				$options['header'] = APPLICATION_NAME.' Password';
				$options['toName'] = $username;
				$options['toEmail'] = $emailaddress;
				$options['message'] = "<div>Hello ".$username.",</div>
												<div>Your password for ".APPLICATION_NAME." application has been changed. Following is the new password <b>".$generatedPswd."</b>.</div>";
				sapp_Mail::_email($options);
				$result['result'] = 'success';
				$result['message'] = 'New password is sent to given E-mail';
			}
			else
			{
				$empdetailsbyemailaddress = $user->getEmpDetailsByEmailAddress($emailaddress);
					
				if(!empty($empdetailsbyemailaddress))
				{
					$username = $empdetailsbyemailaddress[0]['userfullname'];
					$status = $empdetailsbyemailaddress[0]['isactive'];
					$isaccountlock = $empdetailsbyemailaddress[0]['emptemplock'];
					if($status == 0)
					{
						$result['result'] = 'error';
						$result['message']='Employee has been inactivated from the organization.';
					}
					else if($status == 2)
					{
						$result['result'] = 'error';
						$result['message']='Employee has resigned from the organization.';
					}
					else if($status == 3)
					{
						$result['result'] = 'error';
						$result['message']='Employee has left the organization.';
					}
					else if($status == 4)
					{
						$result['result'] = 'error';
						$result['message']='Employee has been suspended from the organization.';
					}
					else if($status == 5)
					{
						$result['result'] = 'error';
						$result['message']='Employee deleted.';
					}
					else if($isaccountlock == 1)
					{
						$result['result'] = 'error';
						$result['message']='Employee has been locked.';
					}
				}
				else
				{
					if($emailcount == 0)
					{
						$result['result'] = 'error';
						$result['message'] = 'Email does not exist.';
					}
				}

			}

		}
		$this->_helper->json($result);
	}

	public function updatecontactnumberAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->_request->getParam('id');
		$contactnumber = $this->_request->getParam('contactnumber');
		$messages['message'] = '';
		$actionflag = 2;
		if($id)
		{
			$usersModal = new Default_Model_Users();
			$menumodel = new Default_Model_Menu();
			$data = array('contactnumber'=>$contactnumber);
			$where = array('id=?'=>$id);
			$Id = $usersModal->addOrUpdateUserModel($data, $where);
			if($Id == 'update')
			{
				$menuidArr = $menumodel->getMenuObjID('/employee');
				$menuID = $menuidArr[0]['id'];
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
				$messages['message'] = 'Contact number updated successfully.';
			}
			else
			$messages['message'] = 'Contact number cannot be updated.';
		}
		else
		{
			$messages['message'] = 'Contact number cannot be updated.';
		}
		$this->_helper->json($messages);

	}

	public function getstatesAction()
	{

		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('getstates', 'html')->initContext();


		$country_id = $this->_request->getParam('country_id');
		$con = $this->_request->getParam('con');
		$statesform = new Default_Form_states();

		$statesmodel = new Default_Model_States();
		if($con == 'state')
		$statesmodeldata = $statesmodel->getBasicStatesList($country_id);
		else if($con == 'otheroption')
		{
		  $stateslistArr = $statesmodel->getBasicStatesList($country_id);
			 $stateids = '';
			 if(!empty($stateslistArr))
			 {
				foreach($stateslistArr as $states)
				{
				   $stateids.= $states['state_id_org'].',';
				}
				$stateids = rtrim($stateids,',');
			 }
		
		  $statesmodeldata = $statesmodel->getUniqueStatesList($country_id,$stateids);
		}
		else
		{
		   $statesmodeldata = $statesmodel->getStatesList($country_id);
		}   
		$this->view->statesform=$statesform;
		$this->view->con = $con;
		$this->view->statesmodeldata=$statesmodeldata;

	}
        public function getstatesnormalAction()
	{

		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('getstatesnormal', 'html')->initContext();


		$country_id = $this->_request->getParam('country_id');
		$con = $this->_request->getParam('con');
		$statesform = new Default_Form_states();

		$statesmodel = new Default_Model_States();
		if($con == 'state')
		$statesmodeldata = $statesmodel->getBasicStatesList($country_id);
		else
		$statesmodeldata = $statesmodel->getStatesList($country_id);
		$this->view->statesform=$statesform;
		$this->view->con = $con;
		$this->view->statesmodeldata=$statesmodeldata;

	}

	public function getcitiesAction()
	{

		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('getcities', 'html')->initContext();


		$state_idArr = explode("!@#",$this->_request->getParam('state_id'));
		$state_id = $state_idArr[0];
		$con = $this->_request->getParam('con');
		$state_id = intval($state_id);
		$citiesform = new Default_Form_cities();
		$citiesmodel = new Default_Model_Cities();

		if($con == 'city')
		{
		   $citiesmodeldata = $citiesmodel->getBasicCitiesList($state_id);
		}   
		else if($con == 'otheroption')
		{ 
			 $citieslistArr = $citiesmodel->getBasicCitiesList($state_id);
			 $cityids = '';
			 if(!empty($citieslistArr))
			 {
				foreach($citieslistArr as $cities)
				{
				   $cityids.= $cities['city_org_id'].',';
				}
				$cityids = rtrim($cityids,',');
			 }
			  $citiesmodeldata = $citiesmodel->getUniqueCitiesList($state_id,$cityids);
		}
		else 
		{
		  $citiesmodeldata = $citiesmodel->getCitiesList($state_id);
		}  

		$this->view->citiesform=$citiesform;
		$this->view->con = $con;
		$this->view->citiesmodeldata=$citiesmodeldata;

	}
        public function getcitiesnormalAction()
	{

		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('getcitiesnormal', 'html')->initContext();


		$state_idArr = explode("!@#",$this->_request->getParam('state_id'));
		$state_id = $state_idArr[0];
		$con = $this->_request->getParam('con');
		$state_id = intval($state_id);
		$citiesform = new Default_Form_cities();
		$citiesmodel = new Default_Model_Cities();

		if($con == 'city')
		$citiesmodeldata = $citiesmodel->getBasicCitiesList($state_id);
		else $citiesmodeldata = $citiesmodel->getCitiesList($state_id);

		$this->view->citiesform=$citiesform;
		$this->view->con = $con;
		$this->view->citiesmodeldata=$citiesmodeldata;

	}

	public function getdepartmentsAction()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('getdepartments', 'html')->initContext();


		$businessunit_id = $this->_request->getParam('business_id');
		$con = $this->_request->getParam('con');
		$employeeform = new Default_Form_employee();
		$leavemanagementform = new Default_Form_leavemanagement();
		$flag = '';
		$departmentsmodel = new Default_Model_Departments();
		if($con == 'leavemanagement')
		{
			$leavemanagementmodel = new Default_Model_Leavemanagement();
			$departmentidsArr = $leavemanagementmodel->getActiveDepartmentIds();
			$depatrmentidstr = '';
			$newarr = array();
			if(!empty($departmentidsArr))
			{
				$where = '';
				for($i=0;$i<sizeof($departmentidsArr);$i++)
				{
					$newarr1[] = array_push($newarr, $departmentidsArr[$i]['deptid']);

				}
				$depatrmentidstr = implode(",",$newarr);
				foreach($newarr as $deparr)
				{
					$where.= " id!= $deparr AND ";
				}
				$where = trim($where," AND");
				$querystring = "Select d.id,d.deptname from main_departments as d where d.unitid=$businessunit_id and d.isactive=1 and $where  ";
				$querystring .= "  order by d.deptname";
				$uniquedepartmentids = $departmentsmodel->getUniqueDepartments($querystring);
				if(empty($uniquedepartmentids))
				$flag = 'true';
					
				$this->view->uniquedepartmentids=$uniquedepartmentids;
			}
			else
			{
				$departmentlistArr = $departmentsmodel->getDepartmentList($businessunit_id);
				if(empty($departmentlistArr))
				$flag = 'true';
				$this->view->departmentlistArr=$departmentlistArr;
			}
		}
		else
		{
			$departmentlistArr = $departmentsmodel->getDepartmentList($businessunit_id);
			if(empty($departmentlistArr))
			$flag = 'true';
			$this->view->departmentlistArr=$departmentlistArr;
		}
		 
		$this->view->employeeform=$employeeform;
		$this->view->leavemanagementform=$leavemanagementform;
		$this->view->flag=$flag;
		if($con !='')
		$this->view->con=$con;

	}

	public function getpositionsAction()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('getpositions', 'html')->initContext();


		$jobtitle_id = $this->_request->getParam('jobtitle_id');
		$con = $this->_request->getParam('con');
		$employeeform = new Default_Form_employee();
		$positionsmodel = new Default_Model_Positions();
		$flag = '';
		$positionlistArr = $positionsmodel->getPositionList($jobtitle_id);
		if(empty($positionlistArr))
		$flag = 'true';
			
		$this->view->positionlistArr=$positionlistArr;
		$this->view->employeeform=$employeeform;
		$this->view->flag=$flag;
		if($con !='')
		$this->view->con=$con;

	}

	public function gettargetcurrencyAction()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('gettargetcurrency', 'html')->initContext();
		$basecurr_id = $this->_request->getParam('basecurr_id');
		$currencyconverterform = new Default_Form_currencyconverter();
		$currencymodel = new Default_Model_Currency();
		$targetcurrencydata = $currencymodel->getTargetCurrencyList($basecurr_id);
		$this->view->currencyconverterform=$currencyconverterform;
		$this->view->targetcurrencydata=$targetcurrencydata;

	}
	public function calculatedaysAction()
	{
		$holidayDates=array();$noOfDays =0;$weekDay='';
		$from_date = $this->_request->getParam('fromDate',null);
		$fromDate = sapp_Global::change_date($from_date,'database');
		$to_date = $this->_request->getParam('toDate',null);
		$toDate = sapp_Global::change_date($to_date,'database');
		$conText = $this->_request->getParam('conText',null);
		if($conText == 1 && $fromDate != "")
		{	//Calculating age based on DOB...
			$noOfDays =  floor((time() - strtotime($fromDate))/31556926);
		}
		$this->_helper->_json($noOfDays);
	}

	public function calculatebusinessdaysAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}

		$noOfDays =0;
		$weekDay='';
		$result['message'] = '';
		$result['days'] = '';
		$result['result'] = '';
		$employeeDepartmentId = '';
		$employeeGroupId = '';
		$weekend1 = '';
		$weekend2 = '';
		$availableleaves = '';
		$holidayDatesArr = array();
		$fromDatejs = $this->_request->getParam('fromDate');
		$fromDate = sapp_Global::change_date($fromDatejs,'database');

		$toDatejs = $this->_request->getParam('toDate');
		$toDate = sapp_Global::change_date($toDatejs,'database');


		$dayselected = $this->_request->getParam('dayselected');
		$leavetypelimit = $this->_request->getParam('leavetypelimit');
		$leavetypetext = $this->_request->getParam('leavetypetext');
		$ishalfday = $this->_request->getParam('ishalfday');
		$context = $this->_request->getParam('context');
		$selectorid = $this->_request->getParam('selectorid');
			

		$userId = $this->_request->getParam('userId',null);
		$loginUserId = ($userId != "")?$userId:$loginUserId;

		//Calculating the no of days in b/w from date & to date with out taking weekend & holidays....
		if($context == 1 )
		{
			$from_obj = new DateTime($fromDatejs);
			$from_date = $from_obj->format('Y-m-d');

			$to_obj = new DateTime($toDatejs);
			$to_date = $to_obj->format('Y-m-d');

			if($dayselected == 1)
			{
				if($to_date >= $from_date)
				{
					$employeesmodel = new Default_Model_Employees();
					$leavemanagementmodel = new Default_Model_Leavemanagement();
					$holidaydatesmodel = new Default_Model_Holidaydates();
					$leaverequestmodel = new Default_Model_Leaverequest();


					$loggedInEmployeeDetails = $employeesmodel->getLoggedInEmployeeDetails($loginUserId);
					$getavailbaleleaves = $leaverequestmodel->getAvailableLeaves($loginUserId);
					if(!empty($getavailbaleleaves))
					{
						$availableleaves = $getavailbaleleaves[0]['remainingleaves'];
					}	
					if(!empty($loggedInEmployeeDetails))
					{
						$employeeDepartmentId = $loggedInEmployeeDetails[0]['department_id'];
						$employeeGroupId = $loggedInEmployeeDetails[0]['holiday_group'];
						
						if($employeeDepartmentId !='' && $employeeDepartmentId != NULL)
							$weekendDetailsArr = $leavemanagementmodel->getWeekendNamesDetails($employeeDepartmentId);
							
						if(!empty($weekendDetailsArr))
						{
							if($weekendDetailsArr[0]['is_skipholidays'] == 1 && isset($employeeGroupId) && $employeeGroupId !='')
							{
								$holidayDateslistArr = $holidaydatesmodel->getHolidayDatesListForGroup($employeeGroupId);
								if(!empty($holidayDateslistArr))
								{
									for($i=0;$i<sizeof($holidayDateslistArr);$i++)
									{
										$holidayDatesArr[$i] = $holidayDateslistArr[$i]['holidaydate'];
									}
								}
							}
							$weekend1 = $weekendDetailsArr[0]['daystartname'];
							$weekend2 = $weekendDetailsArr[0]['dayendname'];
						}
							
						$fromdate_obj = new DateTime($fromDate);
						$weekDay = $fromdate_obj->format('l');
						while($fromDate <= $toDate)
						{
							if(count($holidayDatesArr)>0)
							{
								if($weekDay != $weekend1 && $weekDay != $weekend2 && (!in_array($fromDate,$holidayDatesArr)))
								{
									$noOfDays++;
								}
							}
							else
							{
								if($weekDay != $weekend1 && $weekDay != $weekend2)
								{
									$noOfDays++;
								}
							}
							$fromdate_obj->add(new DateInterval('P1D'));	//Increment from date by one day...
							$fromDate = $fromdate_obj->format('Y-m-d');
							$weekDay = $fromdate_obj->format('l');
						}
					}
					//echo $noOfDays;exit;
					if($leavetypelimit >= $noOfDays)
					{
						$result['result'] = 'success';
						$result['days'] = $noOfDays;
						$result['message'] = '';
						$result['availableleaves'] = $availableleaves;
					}else
					{
						$result['result'] = 'error';
						$result['days'] = '';
						$result['message'] = $leavetypetext.' leave type permits maximum of '.$leavetypelimit.' leaves.';
						$result['availableleaves'] = $availableleaves;
					}

				}
				else
				{
				    if($selectorid == 1)
					{
						$result['result'] = 'error';
						$result['days'] = '';
						$result['message'] = 'From date should be less than to date.';
						$result['availableleaves'] = $availableleaves;
					}
					else if($selectorid == 2)
					{
                        $result['result'] = 'error';
						$result['days'] = '';
						$result['message'] = 'To date should be greater than from date.';	
						$result['availableleaves'] = $availableleaves;				
					}	

				}
			}
			else
			{
				if($to_date == $from_date)
				{
					if($ishalfday == 1)
					{
						$result['result'] = 'success';
						$result['days'] = '0.5';
						$result['message'] = '';
						$result['availableleaves'] = $availableleaves;
					}else
					{
						$result['result'] = 'error';
						$result['days'] = '';
						$result['message'] = 'Half day leave cannot be applied.';
						$result['availableleaves'] = $availableleaves;
					}
				}else
				{
					$result['result'] = 'error';
					$result['days'] = '';
					$result['message'] = 'From Date and To Date should be same for Half day.';
					$result['availableleaves'] = $availableleaves;
				}
			}
			$this->_helper->_json($result);
		}else
		{
			$employeesmodel = new Default_Model_Employees();
			$leavemanagementmodel = new Default_Model_Leavemanagement();
			$holidaydatesmodel = new Default_Model_Holidaydates();


			$loggedInEmployeeDetails = $employeesmodel->getLoggedInEmployeeDetails($loginUserId);
			if(!empty($loggedInEmployeeDetails))
			{
				$employeeDepartmentId = $loggedInEmployeeDetails[0]['department_id'];
				$employeeGroupId = $loggedInEmployeeDetails[0]['holiday_group'];
				
				if($employeeDepartmentId !='' && $employeeDepartmentId != NULL)
				$weekendDetailsArr = $leavemanagementmodel->getWeekendNamesDetails($employeeDepartmentId);
				if(!empty($weekendDetailsArr))
				{
					if($weekendDetailsArr[0]['is_skipholidays'] == 1 && isset($employeeGroupId) && $employeeGroupId !='')
					{
						$holidayDateslistArr = $holidaydatesmodel->getHolidayDatesListForGroup($employeeGroupId);
						if(!empty($holidayDateslistArr))
						{
							for($i=0;$i<sizeof($holidayDateslistArr);$i++)
							{
								$holidayDatesArr[$i] = $holidayDateslistArr[$i]['holidaydate'];
							}
						}
					}

					$weekend1 = $weekendDetailsArr[0]['daystartname'];
					$weekend2 = $weekendDetailsArr[0]['dayendname'];
				}
					
				$fromdate_obj = new DateTime($fromDate);
				$weekDay = $fromdate_obj->format('l');
				while($fromDate <= $toDate)
				{
					if(count($holidayDatesArr)>0)
					{
						if($weekDay != $weekend1 && $weekDay != $weekend2 && (!in_array($fromDate,$holidayDatesArr)))
						{
							$noOfDays++;
						}
					}
					else
					{
						if($weekDay != $weekend1 && $weekDay != $weekend2)
						{
							$noOfDays++;
						}
					}
					$fromdate_obj->add(new DateInterval('P1D'));	//Increment from date by one day...
					$fromDate = $fromdate_obj->format('Y-m-d');
					$weekDay = $fromdate_obj->format('l');
				}
			}
			$this->_helper->_json($noOfDays);
		}


			
	}
	
	public function calculatecalendardaysAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}

		$noOfDays =0;
		$weekDay='';
		$result['message'] = '';
		$result['days'] = '';
		$result['from_date_view'] = '';
		$result['to_date_view'] = '';
		$result['result'] = '';
		$employeeDepartmentId = '';
		$employeeGroupId = '';
		$weekend1 = '';
		$weekend2 = '';
		$availableleaves = '';
		$holidayDatesArr = array();
		$fromDatejs = $this->_request->getParam('fromDate');
		$fromDate = sapp_Global::change_date($fromDatejs,'database');

		$toDatejs = $this->_request->getParam('toDate');
		$toDate = sapp_Global::change_date($toDatejs,'database');


		//Calculating the no of days in b/w from date & to date with out taking weekend & holidays....
		
			$from_obj = new DateTime($fromDatejs);
			$from_date = $from_obj->format('Y-m-d');

			$to_obj = new DateTime($toDatejs);
			$to_date = $to_obj->format('Y-m-d');

			
				if($to_date >= $from_date)
				{
					$employeesmodel = new Default_Model_Employees();
					$leavemanagementmodel = new Default_Model_Leavemanagement();
					$holidaydatesmodel = new Default_Model_Holidaydates();
					$leaverequestmodel = new Default_Model_Leaverequest();


					$loggedInEmployeeDetails = $employeesmodel->getLoggedInEmployeeDetails($loginUserId);
					$getavailbaleleaves = $leaverequestmodel->getAvailableLeaves($loginUserId);
					if(!empty($getavailbaleleaves))
					 $availableleaves = $getavailbaleleaves[0]['remainingleaves'];
					if(!empty($loggedInEmployeeDetails))
					{
						$employeeDepartmentId = $loggedInEmployeeDetails[0]['department_id'];
						$employeeGroupId = $loggedInEmployeeDetails[0]['holiday_group'];
						
						if($employeeDepartmentId !='' && $employeeDepartmentId != NULL)
						   $weekendDetailsArr = $leavemanagementmodel->getWeekendNamesDetails($employeeDepartmentId);
						if(!empty($weekendDetailsArr))
						{
							if($weekendDetailsArr[0]['is_skipholidays'] == 1 && isset($employeeGroupId) && $employeeGroupId !='')
							{
								$holidayDateslistArr = $holidaydatesmodel->getHolidayDatesListForGroup($employeeGroupId);
								if(!empty($holidayDateslistArr))
								{
									for($i=0;$i<sizeof($holidayDateslistArr);$i++)
									{
										$holidayDatesArr[$i] = $holidayDateslistArr[$i]['holidaydate'];
									}
								}
							}
							$weekend1 = $weekendDetailsArr[0]['daystartname'];
							$weekend2 = $weekendDetailsArr[0]['dayendname'];
						}
							
							
						$fromdate_obj = new DateTime($fromDate);
						$weekDay = $fromdate_obj->format('l');
						while($fromDate <= $toDate)
						{
							if(count($holidayDatesArr)>0)
							{
								if($weekDay != $weekend1 && $weekDay != $weekend2 && (!in_array($fromDate,$holidayDatesArr)))
								{
									$noOfDays++;
								}
							}
							else
							{
								if($weekDay != $weekend1 && $weekDay != $weekend2)
								{
									$noOfDays++;
								}
							}
							$fromdate_obj->add(new DateInterval('P1D'));	//Increment from date by one day...
							$fromDate = $fromdate_obj->format('Y-m-d');
							$weekDay = $fromdate_obj->format('l');
						}
					}
						$result['result'] = 'success';
						$result['days'] = $noOfDays;
						$result['message'] = '';
						$result['loginUserId'] =  $loginUserId;
						$result['availableleaves'] = $availableleaves;
					

				}
				
			$this->_helper->_json($result);
	
	}

	public function fromdatetodateAction()
	{
		$from_val = $this->_getParam('from_val',null);
		$to_val = $this->_getParam('to_val',null);
		$con = $this->_getParam('con',null);

		$from_obj = new DateTime($from_val);
		$from_date = $from_obj->format('Y-m-d');

		$to_obj = new DateTime($to_val);
		$to_date = $to_obj->format('Y-m-d');
			
		$result = 'yes';
		if($con == "future")
		{
			if($from_date <= $to_date)
			{
				$result = 'no';
			}
		}else if(is_numeric($con))
		{
			if($from_date > $to_date)
			{
				$result = 'no';
			}
		}
		else
		{
			if($from_date >= $to_date)
			{
				$result = 'no';
			}
		}
		$this->_helper->_json(array('result'=>$result));
	}
	
	public function fromdatetodateorgAction()
	{
		$from_val = $this->_getParam('from_val',null);
		$to_val = $this->_getParam('to_val',null);
		$con = $this->_getParam('con',null);

		$from_obj = new DateTime($from_val);
		$from_date = $from_obj->format('Y-m-d');

		$to_obj = new DateTime($to_val);
		$to_date = $to_obj->format('Y-m-d');
			
		$result = 'yes';
		if($con == "future")
		{
			if($from_date < $to_date && $from_date != $to_date)
			{
				$result = 'no';
			}
		}
		else
		{
			if($from_date > $to_date  && $from_date != $to_date)
			{
				$result = 'no';
			}
		}
		$this->_helper->_json(array('result'=>$result));
	}
	/*	TO validate date conjuntions in  employee medical claims form	*/
	public function medicalclaimdatesAction()
	{
		$from_val = $this->_getParam('from_val',null);
		$to_val = $this->_getParam('to_val',null);
		$new_to_val = $this->_getParam('new_to_val',null);
		$con = $this->_getParam('con',null);
		$claimtype = $this->_getParam('claimtype',null); 

		$new_to_obj ='';$new_to_date = ''; $result = 'yes';

		$from_obj = new DateTime($from_val);
		$from_date = $from_obj->format('Y-m-d');

		$to_obj = new DateTime($to_val);
		$to_date = $to_obj->format('Y-m-d');
		if($new_to_val != "")
		{
			$new_to_obj = new DateTime($new_to_val);
			$new_to_date = $new_to_obj->format('Y-m-d');
		}
		switch($con)
		{
			case 1:		//Injured Date should be greater than emp leave start date..
				if($claimtype != 'maternity' && $claimtype != 'paternity'){
					if($from_date > $to_date)
					{
						$result = 'no';
					}
				}else{
				   if($from_date < $to_date)
					{
						$result = 'no';
					}				
				}
				break;
			case 2:		//Check whether to date is greater than from date...
				if($from_date > $to_date)
				{
					$result = 'no';
				}
				break;
			case 3:		// Approved leave from date should be in between employee applied leave from & to dates.
				if($to_date < $from_date || $to_date >= $new_to_date)
				{
					$result = 'no';
				}
				break;
			case 4:		// Approved leave to date should be in between employee applied leave from & to dates.
				if($to_date < $from_date || $to_date > $new_to_date)
				{
					$result = 'no';
				}
				break;
			case 5:		// date of joining should be greater than date of injury/paternity/maternity/disability & employee applied leave end date.
				if($from_date > $to_date)
				{
					$result = 'no';
				}
				if(isset($new_to_date) && $new_to_date !='')
				{
					if($from_date > $new_to_date)
					{
						$result = 'no';
					}
				}
				break;
		}
		$this->_helper->_json(array('result'=>$result));
	}
	public function gettimeformatAction()
	{
		$sel_time = $this->_getParam('sel_time',null);
		$timeformat = '';
		if($sel_time != '')
		{
			$timeformat = sapp_Global::change_time($sel_time, 'view');
		}
		$this->_helper->_json(array('timeformat'=>$timeformat));
	}
	public function chkcurrenttimeAction()
	{
		$sel_time = $this->_getParam('sel_time',null);
		$sel_date = $this->_getParam('sel_date',null);

		$now_date = date('Y-m-d');
		$sel_date_obj = new DateTime($sel_date);
		$new_sel_date = $sel_date_obj->format('Y-m-d');

		$greater = 'no';
		if($new_sel_date == $now_date)
		{
			$now_time = date("H:i");
			$selected_time = date("H:i",  strtotime($sel_time));
			if($selected_time > $now_time)
			{
				$greater = 'yes';
			}
		}
		$timeformat = '';
		if($greater == 'no')
		{
			$timeformat = sapp_Global::change_time($sel_time, 'view');
		}
		$this->_helper->_json(array('timeformat'=>$timeformat,'greater' => $greater));
	}

	public function popupAction()
	{
		/*
		 * This action will be triggered when new user is opening the application from email.
		 * So the index page will open with Popup in a open state
		 */
	}


	public function createorremoveshortcutAction()
	{
		$auth = Zend_Auth::getInstance();
		$role_id = 1;
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$role_id = $auth->getStorage()->read()->emprole;
		}
		$this->_helper->layout->disableLayout();
		$settingsmodel = new Default_Model_Settings();
		$privilege_model = new Default_Model_Privileges();
		$menuid = $this->_request->getParam('menuid');
		$shortcutflag = $this->_request->getParam('shortcutflag');
		$date = new Zend_Date();
		$where='';
		$menuidstring = '';
		$error = '';
		$id = '';
		$idCsv = 0;
		$result = 'error';
		
		if($menuid)
		{
			$privilegesofObj = $privilege_model->getObjPrivileges($menuid,"",$role_id,$idCsv);
			if($privilegesofObj['isactive'] == 1)
	   		{
					if($shortcutflag == 1 || $shortcutflag == 2)
					{
						$settingsmenuArr = $settingsmodel->getMenuIds($loginUserId,2);
						if(!empty($settingsmenuArr))
						{
								
							$settingsmenustring = $settingsmenuArr[0]['menuid'];
								
							if(strlen($settingsmenustring) == 0)
							$settingsmenuArray = array();
							else
							$settingsmenuArray = explode(",",$settingsmenustring);
								if(sizeof($settingsmenuArray) == 16 && $shortcutflag != 2)
								{
								   $error= "Limit";
								}
								else
								{
									if(in_array($menuid,$settingsmenuArray))
									{
										$key = array_search($menuid, $settingsmenuArray);
										if ($key !== false) {
											unset($settingsmenuArray[$key]);
										}
									}
									else
									{
										array_push($settingsmenuArray,$menuid);
									}
										
									if(strlen($settingsmenustring) == 0)
									$menuidstring = $menuid;
									else
									$menuidstring = implode(",", $settingsmenuArray);
										
									$where = array('userid=?'=>$loginUserId,
												   'flag=?'=>2,
												   'isactive=?'=>1 
									);
		
									$data = array(
											'menuid'=>$menuidstring, 
											'modified'=>$date->get('yyyy-MM-dd HH:mm:ss')
									);
									$id = $settingsmodel->addOrUpdateMenus($data, $where);
								}	
								
						}
					}
					else if($shortcutflag == 3)
					{
						$data = array(
		    							'userid'=>$loginUserId,
		                                'menuid'=>$menuid, 
		                                'flag'=>2, 
		       							'isactive'=> 1,
		    							'created'=>$date->get('yyyy-MM-dd HH:mm:ss'),
		    							'modified'=>$date->get('yyyy-MM-dd HH:mm:ss')
						);
						$id = $settingsmodel->addOrUpdateMenus($data, $where);
					}
					 
					if($id !='')
					{
						if($id == 'update')
						$result = 'update';
						else
						$result = 'newrecord';
					}
					else
					{
					    if($error !='')
						$result = 'limit';
						else
						$result = 'error';
					}
					
	   		}else
	   		{
	   			$result = 'inactive';
	   		}		
			$this->_helper->_json(array('result'=>$result));
		}
	}


	public function sessiontourAction(){

		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$usermanagementModel = new Default_Model_Usermanagement();
		
		$status = $usermanagementModel->SaveorUpdateUserData(array('tourflag'=>1),"id=".$loginUserId);
		
		if($status == 'update'){
            $auth->getStorage()->read()->tourflag = 1;		    
        } 		
     
        $this->_helper->json($status); 
		
	}
	
	public function getissuingauthorityAction()
	{
	    $this->_helper->layout->disableLayout();
		$result['result'] = '';
		$workeligibilitydoctypesmodel = new Default_Model_Workeligibilitydoctypes();
		
		$doctypeid = $this->_request->getParam('doctypeid');
		
		$issuingauthorityArr = $workeligibilitydoctypesmodel->getIssuingAuthority($doctypeid);
		if(!empty($issuingauthorityArr))
		{
		  $issuingauthority = $issuingauthorityArr[0]['issuingauthority'];
		  $result['result'] = $issuingauthority;
		}  
		
		$this->_helper->json($result); 
	}
	
	public function setsessionvalAction()
	{
		$hideshow_mainmenu = $this->getRequest()->getParam('hideshow_mainmenu');

		$organizationImg = new Zend_Session_Namespace('organizationinfo');
		$organizationImg->hideshowmainmenu = $hideshow_mainmenu;

		echo $hideshow_mainmenu;
		exit;
	}
    
	public function checkisactivestatusAction()
	{
		$this->_helper->layout->disableLayout();
		$result['result'] = '';
		$status =  sapp_Global::_checkstatus();
		
		if($status == 'false')
		{
			$sessionData = sapp_Global::_readSession();
			Zend_Session::namespaceUnset('recentlyViewed');
			Zend_Session::namespaceUnset('organizationinfo');
			$auth = Zend_Auth::getInstance();
			$auth->clearIdentity();
		}
		$result['result'] = $status;
		
		$this->_helper->json($result); 
	}
	
	public function updatethemeAction()
	{    	
    	$this->_helper->layout->disableLayout();
    	if ($this->getRequest()->isPost())
    	{
    		$theme_name = $this->getRequest()->getParam('theme_name');    		
    		$usersModel = new Default_Model_Users();
    		
		    $user_id = sapp_Global::_readSession('id');
		    
		    $where = array('id = ?' => $user_id);			
		    $data=array(
						'themes' => $theme_name,
    					'createddate'=> gmdate("Y-m-d H:i:s"),
    					'modifieddate' => gmdate("Y-m-d H:i:s"),
					);
			$usersModel->addOrUpdateUserModel($data, $where);
    		
			sapp_Global::_writeSession('themes',$theme_name);
			$this->_helper->json(array('result'=>'success'));
    	}    	
    }
	
}

