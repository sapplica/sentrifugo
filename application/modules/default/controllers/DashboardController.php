<?php
/********************************************************************************* 
 *  This file is part of Sentrifugo.
 *  Copyright (C) 2015 Sapplica
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

class Default_DashboardController extends Zend_Controller_Action{

	private $options;
	public function preDispatch()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('viewsettings', 'html')->initContext();
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
    
	public function indexAction(){
		$activeDashboard=null;
		$employee = Zend_Registry::get('employee');
		$userdashboard = $employee->getUserid()->getMain_userdashboardUseridAssocUsermanagementId()->first();
			
		$roledashboards = $employee->getUserid()->getEmprole()->getMain_dashboardpriviledgesRoleidAssocRolesid();

		if($roledashboards->count()){
			$dashsettings = array();
			foreach($roledashboards as $roledashboard){
				if($roledashboard->getActive()==1){
					$activeDashboard = $roledashboard->getDashboardid();
					$dashsettings[$activeDashboard->getName()]='<a href="#">'.$activeDashboard->getName().' *</a>';
				}else{

					$dashsettings[$roledashboard->getDashboardid()->getName()]='<a href="'.BASE_URL.'dashboard/saveuserdashboard/id/'.$roledashboard->getDashboardid()->getId().'" >'.$roledashboard->getDashboardid()->getName().'</a>';
				}
			}

			if(!$userdashboard){
				$widgets = $activeDashboard->getMain_dashboard_widgetsDashboard_idAssocMain_dashboardsId();
				$dashname = $activeDashboard->getName();
			}else{
				$widgets = $userdashboard->getDashboardid()->getMain_dashboard_widgetsDashboard_idAssocMain_dashboardsId();
				$dashname = $userdashboard->getDashboardid()->getName();
				$dashsettings[$activeDashboard->getName()]='<a href="'.BASE_URL.'dashboard/saveuserdashboard/id/'.$activeDashboard->getId().'">'.$activeDashboard->getName().'</a>';
			}
			$dashsettings[$dashname]='<a href="#">'.$dashname.' *</a>';

			$dashsettings = implode('',$dashsettings);

			$this->view->widgets = $widgets;
			$this->view->dashboards = $dashsettings;
			$this->view->dashboard = $dashname;
		}
	}

	public function saveuserdashboardAction(){
		$id = $this->_getParam("id");

		$employee = Zend_Registry::get('employee');
		$userdashboard = $employee->getUserid()->getMain_userdashboardUseridAssocUsermanagementId()->first();

		if(!$userdashboard){
			$userdashboard = new Entities\Main_userdashboard();
		}
		$dashboard = $this->em->getRepository("Entities\Main_dashboards")->find($id);
		$userdashboard->setDashboardid($dashboard);
		$userdashboard->setUserid($employee->getUserid());
		try{
			$this->em->persist($userdashboard);
			$this->em->flush();

			$this->_helper->redirector("index", "dashboard");
			exit;
		}catch (Exception $e){
			echo $e;
			exit;
		}

	}

	public function getwidgtesAction(){

		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();

		$employee = Zend_Registry::get('employee');
			
		$roledashboards = $employee->getUserid()->getEmprole()->getMain_dashboardpriviledgesRoleidAssocRolesid();

		if($roledashboards->count()){
			$dashboardsettings = array();
			foreach($roledashboards as $roledashboard){
				if($roledashboard->getActive()==1){
					$activeDashboard = $roledashboard->getDashboardid();
					$widgets = $activeDashboard->getMain_dashboard_widgetsDashboard_idAssocMain_dashboardsId();
					$dashboardsettings["name"]=$activeDashboard->getName();
					$dashboardsettings["layout"]=$activeDashboard->getLayout();
					if($widgets->count()){
						$dashboardsettings["widgets"]["count"] = $widgets->count();
						$rownum = 0;
						foreach ($widgets as $widget){
							$viewid = $widget->getViewid();
							$GridMxmlarray = $this->_helper->HtmlFormCodeGeneratorHelper->getObjectMxml($viewid);
							$fieldcount = 0;
							$lookupcount = 0;
							$this->gridFields["Fields"]= array();
							$this->gridFields["Lookup"]= array();
							foreach($GridMxmlarray['fields'] as $field){


								if($field['dataType']!='LOOKUP' && $field['dataType']!='GRID' && $field['gridField']==1){
									if($field['fieldName']){
										$this->gridFields["Fields"][$fieldcount]['FieldName'] = $field['fieldName'];
										$this->gridFields["Fields"][$fieldcount]['FieldLabel'] = $field['fieldLabel'];
										$fieldcount++;
									}
								}elseif($field['dataType'] == 'LOOKUP' && $field['gridField']==1){

									$this->gridFields["Lookup"][$lookupcount]['FieldName'] = $field['fieldName'];
									$this->gridFields["Lookup"][$lookupcount]['FieldLabel'] = $field['fieldLabel'];
									$this->gridFields["Lookup"][$lookupcount]['LookupColumn'] = $field['column'];
									$lookupcount++;
								}
							}
							$entityname = ucfirst($GridMxmlarray['objectName']);
							$genderObj = $this->em->getRepository("Entities\\$entityname")->findBy(array('sentrifugo_status' => 1));
							$genderGridJson = array();
							$genderGridJson['count'] = count($genderObj);
							$genderGridJson['dashboardname'] = $widget->getTitle();
							$genderGridJson['datafields'] = array();
							$genderGridJson['rows'] = array();
							$j=0;
							if($genderObj){
								foreach($genderObj as $row){
									for($i = 0; $i < count($this->gridFields["Fields"]); $i++){
										if($j == 0){
											$genderGridJson['datafields'][$this->gridFields["Fields"][$i]["FieldName"]] = $this->gridFields["Fields"][$i]["FieldLabel"];
										}
										$ucfirctcol = 'get'.ucfirst($this->gridFields["Fields"][$i]["FieldName"]);

											
										if(is_object($row->$ucfirctcol())){
											$genderGridJson['rows'][$j][$this->gridFields["Fields"][$i]["FieldName"]] = $row->$ucfirctcol()->format(DATEFORMAT_PHP);
										}else{
											$genderGridJson['rows'][$j][$this->gridFields["Fields"][$i]["FieldName"]] = $row->$ucfirctcol();
										}
									}

									for($i = 0; $i < count($this->gridFields["Lookup"]); $i++){
										if($j == 0){
											$genderGridJson['datafields'][$this->gridFields["Lookup"][$i]["FieldName"]] = $this->gridFields["Lookup"][$i]["FieldLabel"];
										}
										$ucfirctcols = 'get'.ucfirst($this->gridFields["Lookup"][$i]["FieldName"]);
										$lookupucfirctcol = 'get'.ucfirst($this->gridFields["Lookup"][$i]["LookupColumn"]);
										if($row->$ucfirctcols()){
											if(is_object($row->$ucfirctcols()->$lookupucfirctcol())){
												$genderGridJson['rows'][$j][$this->gridFields["Lookup"][$i]["FieldName"]] = $row->$ucfirctcols()->$lookupucfirctcol()->format(DATEFORMAT_PHP);
											}else{
												$genderGridJson['rows'][$j][$this->gridFields["Lookup"][$i]["FieldName"]] = $row->$ucfirctcols()->$lookupucfirctcol();
											}
										}else{
											$genderGridJson['rows'][$j][$this->gridFields["Lookup"][$i]["FieldName"]] = '';
										}
									}
									$j++;
								}
							}
							$xmlobj = $this->em->getRepository("Entities\Main_menu")->findOneByObjectId($viewid);
							$dashboardsettings["widgets"]["rows"][substr($xmlobj->getUrl(),1).'#'.$xmlobj->getId()] = $genderGridJson;
							$rownum++;
						}
					}
					break;
				}
					
			}
		}
		echo json_encode($dashboardsettings);
		exit;
	}
	
		public function upgradeapplicationAction()
        {
         $auth = Zend_Auth::getInstance();
     		if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;
					$loginuserGroup = $auth->getStorage()->read()->group_id;
     		}	
        }
		public function emailsettingsAction()
		{
			$auth = Zend_Auth::getInstance();
			$data = '';
			if($auth->hasIdentity())
			{                                
				$loginuser_role = $auth->getStorage()->read()->emprole;
			}
			if($loginuser_role == SUPERADMINROLE)
			{  
				$email_form = new Default_Form_Emailsettings();
				if(isset($_POST['submit']))
				{
					$data = $_POST;
				}
				else
				{
					$usersmodel = new Default_Model_Users();          
					$data = $usersmodel->getMailSettingsData();
					if(!empty($data))
						$data = $data[0];
				}
				
				if(!empty($data))
				{
					
				    $email_form->populate($data);
					if(!empty($data['PORT']))
					{
						$email_form->setDefault('port',$data['PORT']);
					}
				    $email_form->password->renderPassword = true;
				    $email_form->setDefault('auth',$data['auth']);
					$this->view->auth = !empty($data['auth'])?$data['auth']:'true';
				}
				else
				{
					$this->view->auth = 'true';
				}
				
				$this->view->form = $email_form;
				$this->view->messages = $this->_helper->flashMessenger->getMessages();

			
				if($this->getRequest()->getPost())
				{
					$result = $this->save_email_settings($email_form);
					$this->view->msgarray = $result;                
				}
			}
			else 
			{                            
				$this->_redirect('error');
			}
		}
		
    public function save_email_settings($form)
	{
		
            $auth = Zend_Auth::getInstance();
            if($auth->hasIdentity())
            {
                $loginUserId = $auth->getStorage()->read()->id;
                $loginuserGroup = $auth->getStorage()->read()->group_id;
                $loginuserName = $auth->getStorage()->read()->userfullname;
            }
            $msgarray = array();
            $errorflag = 'true';
         
		   /* if(extension_loaded('openssl'))
               $errorflag= 'true';
            else
            {
              $msgarray['tls'] = 'Openssl is not configured';	
              $errorflag = 'false';
            }    */
			$auth = $this->_request->getParam('auth');
			$username = trim($this->_request->getParam('username'));
			$password = trim($this->_request->getParam('password'));
			if($auth == 'false')
			{
				$msgarray['username'] = '';
				$msgarray['password'] = '';
			}
			else
			{
				if(empty($username)){
					$msgarray['username'] = 'Please enter username.';
					$errorflag = 'false';
				}
				if(empty($password)){
					$msgarray['password'] = 'Please enter password.';
					$errorflag = 'false';
				}
			}

			if($form->isValid($this->_request->getPost()) && $errorflag == 'true')
            {
            	$id = $this->_request->getParam('id');
				
				$server_name = $this->_request->getParam('server_name');
				$tls = $this->_request->getParam('tls');
				$port = $this->_request->getParam('port');
				
				$usersModel = new Default_Model_Users();
				$options = array();
							

				$options['username'] = !empty($username)?$username:"";
				$options['password'] = !empty($password)?$password:"";
				$options['server_name'] = $server_name;
				$options['tls'] = !empty($tls)?$tls:"";
				$options['auth'] = $auth;
				$options['port'] = $port;
				$options['subject'] = 'Test Mail Checking';
				$options['header'] = 'Test Mail';
				$options['fromEmail'] = DONOTREPLYEMAIL;
				$options['fromName'] = DONOTREPLYNAME;
				$options['toEmail'] = SUPERADMIN_EMAIL;
				$options['toName'] = $loginuserName;
				$options['message'] = '<div>
								<div>Hi '.ucfirst($loginuserName).',</div><br/>
								<div>This is a test email to check the new mail settings provided for '.APPLICATION_NAME.'.</div></div>';	
				   
					$result = sapp_Mail::_checkMail($options);
					if($result === true)
					{
           			 $data = array( 'username'=>$username,
				                 'password'=>$password,
				   				 'server_name'=>$server_name,
				   				 'auth'=>$auth,	
				                 'tls'=>$tls,
				   				 'port'=>$port,
 								 'modifieddate'=>gmdate("Y-m-d H:i:s")
						);
					if($id!=''){
						$where = array('id=?'=>$id);  
				
					}
					else
					{
					  	$data['createddate'] = gmdate("Y-m-d H:i:s");
						$where = '';
					}
					
					$Id = $usersModel->addOrUpdateSettingsData($data, $where);
					sapp_Global::writeEMailSettingsconstants($tls,$auth,$port,$username,$password,$server_name);
					
					if($Id == 'update')
					{
					   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Mail Settings updated successfully."));
					}   
					else
					{
                        $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Mail Settings added successfully."));					   
					}   
				}
				else
				{
					 $this->_helper->getHelper("FlashMessenger")->addMessage(array("error"=>"Invalid parameters."));
				}
                    $this->_redirect('dashboard/emailsettings');
            }
            else
            {
				
                $messages = $form->getMessages();
                foreach ($messages as $key => $val)
                {
                    foreach($val as $key2 => $val2)
                    {
                        $msgarray[$key] = $val2;
                        break;
                    }
                }
				 return $msgarray;
            }
        }
        
        
	    
	public function changepasswordAction()
	{
		
		$form = new Default_Form_changepassword();
		$sitepreferencemodel = new Default_Model_Sitepreference();
		$sitepreferenceArr = $sitepreferencemodel->SitePreferanceData();
                $auth    = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) 
                {
                    $identity = $auth->getIdentity();                                				
                    $login_role = $identity->emprole;
                }
		$this->view->form=$form;
                $this->view->login_role = $login_role;
		if(!empty($sitepreferenceArr))
		   $this->view->passwordid=$sitepreferenceArr[0]['passwordid'];
		else
           $this->view->passwordid= 1;  		
		$this->view->message = 'This is change password page';
	}

	public function editpasswordAction()
    {
		$auth    = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
				 $identity = $auth->getIdentity();
				 $id = $identity->id;
				 $email = $identity->emailaddress;
				 $employeid = $identity ->employeeId;
			}
		$password = trim($this->_request->getParam('password'));
		$newpassword = trim($this->_request->getParam('newpassword'));
		$confpassword = trim($this->_request->getParam('passwordagain'));
		$password = preg_replace( '/\s+/', ' ', $password );
		$newpassword = preg_replace( '/\s+/', ' ', $newpassword );
		$confpassword = preg_replace( '/\s+/', ' ', $confpassword );
		$pwd = md5($password);
		$newpwd = md5($newpassword);
		$confpwd = md5($confpassword);
		$loginmodel = new Default_Model_Users();
		$userpassword = $loginmodel->getLoggedInUserPwd($id,$email,$employeid);
		$sespwd = $userpassword['emppassword'];
		$changepasswordform = new Default_Form_changepassword();
		$sitepreferencemodel = new Default_Model_Sitepreference();
		$sitepreferenceArr = $sitepreferencemodel->SitePreferanceData();
		/*
		    Pattern Used for alphanumeric expression 
			   'pattern'=> '/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]+)$/',
				  -> Here the first bracket() inside the pattern specifies that atleast one number should be there in the expression.
				  -> Second bracket() specifies that atleast one alphabet should be present in the expression.
				  -> Third bracket() specifies the allowed set of characters in the expression.
				  
			Pattern Used for alphanumeric and special characters 
			    'pattern'=> '/^(?=.*[0-9])(?=.*[a-zA-Z])(?=.*[.\-#$@&\_*])([a-zA-Z0-9.\-#$@&\_*]+)$/',
				  -> Here the first bracket() inside the pattern specifies that atleast one number should be there in the expression.
				  -> Second bracket() specifies that atleast one alphabet should be present in the expression.
				  -> Third bracket() specifies that atleast one special character should be present in the expression.
				  -> Fourth bracket() specifies the allowed set of characters in the expression.

            Pattern Used for numbers and special characters 
			    'pattern'=> '/^(?=.*[0-9])(?=.*[.\-#$@&\_*])([0-9.\-#$@&\_*]+)$/',
				  -> Here the first bracket() inside the pattern specifies that atleast one number should be there in the expression.
				  -> Second bracket() specifies that atleast one special character should be present in the expression.
				  -> Third bracket() specifies the allowed set of characters in the expression.				  
		*/
		if(!empty($sitepreferenceArr))
		{
				if($sitepreferenceArr[0]['passwordid'] == 1)
				{ 
				
					$changepasswordform->newpassword->addValidator("regex",true,array(
									'pattern'=> '/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]+)$/',
								   'messages'=>array(
									   'regexNotMatch'=>'Please enter only alphanumeric characters.'
								   )
					)); 
					
					$changepasswordform->passwordagain->addValidator("regex",true,array(
									'pattern'=> '/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]+)$/',
								   'messages'=>array(
									   'regexNotMatch'=>'Please enter only alphanumeric characters.'
								   )
					));			
				} 
				
				else if($sitepreferenceArr[0]['passwordid'] == 2)
				{
					$changepasswordform->newpassword->addValidator("regex",true,array(
								  'pattern'=> '/^(?=.*[0-9])(?=.*[a-zA-Z])(?=.*[.\-#$@&\_*])([a-zA-Z0-9.\-#$@&\_*]+)$/',
								   'messages'=>array(
									   'regexNotMatch'=>'Please enter only characters,numbers and special characters.'
								   )
					));
					
					$changepasswordform->passwordagain->addValidator("regex",true,array(
								  'pattern'=> '/^(?=.*[0-9])(?=.*[a-zA-Z])(?=.*[.\-#$@&\_*])([a-zA-Z0-9.\-#$@&\_*]+)$/',
								   'messages'=>array(
									   'regexNotMatch'=>'Please enter only characters,numbers and special characters.'
								   )
					));
				}
				else if($sitepreferenceArr[0]['passwordid'] == 3)
				{
					$changepasswordform->newpassword->addValidator("regex",true,array(
								   'pattern'=>'/^[0-9]+$/', 
								   'messages'=>array(
									   'regexNotMatch'=>'Please enter numbers only.'
								   )
					));
					
					$changepasswordform->passwordagain->addValidator("regex",true,array(
								   'pattern'=>'/^[0-9]+$/', 
								   'messages'=>array(
									   'regexNotMatch'=>'Please enter numbers only.'
								   )
					));
				}
				else
				{
					$changepasswordform->newpassword->addValidator("regex",true,array(
									'pattern'=> '/^(?=.*[0-9])(?=.*[.\-#$@&\_*])([0-9.\-#$@&\_*]+)$/',
								   'messages'=>array(
									   'regexNotMatch'=>'Please enter only numbers and special characters.'
								   )
					)); 
					
					$changepasswordform->passwordagain->addValidator("regex",true,array(
									'pattern'=> '/^(?=.*[0-9])(?=.*[.\-#$@&\_*])([0-9.\-#$@&\_*]+)$/',
								   'messages'=>array(
									   'regexNotMatch'=>'Please enter only numbers and special characters.'
								   )
					));			
				}
		}
		else
        {
		    $changepasswordform->newpassword->addValidator("regex",true,array(
									'pattern'=> '/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]+)$/', 
								   'messages'=>array(
									   'regexNotMatch'=>'Please enter only alphanumeric characters.'
								   )
					)); 
					
			$changepasswordform->passwordagain->addValidator("regex",true,array(
							'pattern'=> '/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]+)$/', 
						   'messages'=>array(
							   'regexNotMatch'=>'Please enter only alphanumeric characters.'
						   )
			));	
        } 		

        /* Logic ends for site preference password validation
           END
        */ 
	
		if($this->getRequest()->getPost()){
   			 
    		if($changepasswordform->isValid($this->_request->getPost())&& ($sespwd == $pwd) && $newpwd==$confpwd && $pwd!=$newpwd ){
					$loginmodel->editadminPassword($newpwd,$id,$email,$employeid);
					$this->_helper->json(array('result'=>'saved','message'=>"Password changed successfully."));	
				
    		}else{
    			$messages = $changepasswordform->getMessages();
    			if(($sespwd != $pwd) && $password!=''){
    				$messages['password']=array('Wrong password. Please enter correct password.');
    			}
		    	if(($newpwd!=$confpwd)&& $newpassword !='' && $confpassword!=''){
		    		$messages['passwordagain']=array('New password and confirm password did not match.');
		    	}
				if(($pwd==$newpwd)&& $newpassword !='' && $password!=''){
		    		$messages['newpassword']=array('Please choose a different password.');
		    	}
    			$messages['result']='error';
    			$this->_helper->json($messages);
    		}
				
		}
	}
	
    public function updateAction()
    {	 
	    $userid = $this->_request->getParam('user_id');             
        $imagepath = $this->_request->getParam('profile_photo');
        if($imagepath !='')
        {
            if(file_exists(USER_PREVIEW_UPLOAD_PATH.'//'.$imagepath))
            {
                copy(USER_PREVIEW_UPLOAD_PATH.'//'.$imagepath, USER_UPLOAD_PATH.'//'.$imagepath);
                unlink(USER_PREVIEW_UPLOAD_PATH.'//'.$imagepath);                
            } 
   			
        }              				
        $usermodel = new Default_Model_Users();                   					
        $data = array('profileimg'=>$imagepath,
                      
					  );							  				               
        $where = array("id=?" => $userid);                                    
        $status = $usermodel->addOrUpdateProfileImage($data,$where); 
        if($status == 'update')
        {
            $update_query = "update main_employees_summary set profileimg = '".$imagepath."',
                             modifieddate = utc_timestamp() where user_id = '".$userid."'";
            $update_requesthistory_query = "update main_request_history set emp_profileimg = '".$imagepath."',
                             modifieddate = utc_timestamp() where emp_id = '".$userid."'";
            $db = Zend_Db_Table::getDefaultAdapter();
            $result = $db->query($update_query);
            $db->query($update_requesthistory_query);
            $auth = Zend_Auth::getInstance();
            if($auth->hasIdentity())
            {
                $auth->getStorage()->read()->profileimg = $imagepath;
                
            }
        } 		
     
        $this->_helper->json($status); 
	}
	
	public function uploadpreviewAction(){
    	$result = $this->imageupload();	
			
		$this->_helper->json($result);
		
    }
	
	public function imageupload()
	{
		// folder for upload
		$savefolder = USER_PREVIEW_UPLOAD_PATH;		
		
		// maxim size for image file, in KiloBytes
		$max_size = 1024;			

		// Allowed image types
		$allowtype = array('gif', 'jpg', 'jpeg', 'png');

		/** Uploading the image **/

		$rezultat = '';
		$result_status = '';
		$result_msg = '';

		// if uploaded file is a valid file
		if (isset ($_FILES['profile_photo'])) {

			// checks to have the allowed extension
			$type = explode(".", strtolower($_FILES['profile_photo']['name']));

			if (in_array($type[1], $allowtype)) {
				// check its size
				if ($_FILES['profile_photo']['size']<=$max_size*1024) {
					// if no errors
					if ($_FILES['profile_photo']['error'] == 0) {
						$newname = 'preview_'.date("His").'.'.$type[1];
						$thefile = $savefolder . "/" . $_FILES['profile_photo']['name'];
						$newfilename = $savefolder . "/" . $newname;
						$filename = $newname;

						if (!move_uploaded_file ($_FILES['profile_photo']['tmp_name'], $newfilename)) {
							$rezultat = '';
							$result_status = 'error';
							$result_msg = 'The file cannott be uploaded, try again.';
						}
						else {
							$rezultat = $filename;
							$image = new Zend_Resize($newfilename);
							$image-> resizeImage(200, 200, 'crop');
							$image->saveImage($newfilename, 100);

							$result_status = 'success';
							$result_msg = '';
						}
					}
				}
				else 
				{ 
					$rezultat = ''; 
					$result_status = 'error';
					$result_msg = 'The file exceeds the maximum permitted size '. $max_size. ' KB.';
				}
			}
			else 
			{ 
				$rezultat = ''; 
				$result_status = 'error';
				$result_msg = 'Please upload only .gif, .jpg, .jpeg, .png images.';

			}
		}
		else 
		{ 
			$rezultat = ''; 
			$result_status = 'error';
			$result_msg = 'Please upload only .gif, .jpg, .jpeg, .png images.';
		}

		$result = array(
			'result'=>$result_status,
			'img'=>$rezultat,
			'msg'=>$result_msg
		);
		return $result;
	}
	
	public function viewprofileAction()
	{		
		$id = '';
		$username = '';
		$email = '';
		$profileimage = '';
		$role = '';
		
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$auth = $auth->getStorage()->read();
			$id = $auth->id;	
	        $login_user_role = $auth->emprole;
			$username = $auth->userfullname;	
			$email = $auth->emailaddress;
		}

		if($id == SUPERADMIN)
		{
			$role = 'true';
			
		}		
		$viewprofileform = new Default_Form_viewprofile();
		$usermodel = new Default_Model_Users();
		$getuserdetails = $usermodel->getUserDetails($id);
			$username = $getuserdetails[0]['userfullname'];
			$firstname = $getuserdetails[0]['firstname'];
			$lastname = $getuserdetails[0]['lastname'];
			$email = $getuserdetails[0]['emailaddress'];
			$profileimage = $getuserdetails[0]['profileimg'];
			
		$viewprofileform->populate($getuserdetails[0]);
			
		$this->view->id = $id;	
		$this->view->username = $username;
		$this->view->firstname = $firstname;
		$this->view->lastname = $lastname;	
		$this->view->email = $email;
		$this->view->profileimage = $profileimage;
        $this->view->login_user_role = $login_user_role;
        $this->view->role = $role;
        $this->view->form = $viewprofileform; 
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
		if($this->getRequest()->getPost()){
		     $result = $this->saveProfileDetails($viewprofileform);	
		     $this->view->msgarray = $result; 
        }
	}
	
	public function saveProfileDetails($viewprofileform)
	{
			$auth = Zend_Auth::getInstance();
	     	if($auth->hasIdentity()){
						$loginUserId = $auth->getStorage()->read()->id;
			}
			
			if($viewprofileform->isValid($this->_request->getPost())){
			    $id = $this->_request->getParam('id');
			    $firstname = $this->_request->getParam('firstname');
			    $lastname = $this->_request->getParam('lastname');
			    $userfullname = $firstname.' '.$lastname;
				$emailaddress = $this->_request->getParam('emailaddress');
				$usersModel = new Default_Model_Users();
				$date = new Zend_Date();
				$actionflag = '';
				$tableid  = ''; 
				   $data = array('firstname'=>$firstname,
				   				 'lastname'=>$lastname,
				   				 'userfullname'=>$userfullname,
				                 'emailaddress'=>$emailaddress,
 								 'modifiedby'=>$loginUserId,
								 'modifieddate'=>gmdate("Y-m-d H:i:s")
						);
					if($id!=''){
						$where = array('id=?'=>$id);  
						$actionflag = 2;
					}
					else
					{
					    $data['createdby'] = $loginUserId;
						$data['createddate'] = gmdate("Y-m-d H:i:s");
						$data['isactive'] = 1;
						$where = '';
						$actionflag = 1;
					}
					$Id = $usersModel->addOrUpdateUserModel($data, $where);
					sapp_Global::writeApplicationConstants($emailaddress,APPLICATION_NAME);
					
		            if($auth->hasIdentity())
		            {
		                $auth->getStorage()->read()->userfullname = $userfullname;
		                
		            }
					if($Id == 'update')
					{
					   $tableid = $id;
					   $this->_helper->getHelper("FlashMessenger")->addMessage("Profile details updated successfully.");
					}   
					else
					{
                       $tableid = $Id; 	
                        $this->_helper->getHelper("FlashMessenger")->addMessage("Profile details saved successfully.");					   
					}   
					
    			    $this->_redirect('dashboard/viewprofile');		
			}else
			{
			     $messages = $viewprofileform->getMessages();
				foreach ($messages as $key => $val)
					{
						foreach($val as $key2 => $val2)
						 {
							$msgarray[$key] = $val2;
							break;
						 }
					}
				return $msgarray;	
			
			}
	}
	
	public function viewsettingsAction()
	{
	    $layoutflag = $this->_request->getParam('layout');
        if($layoutflag == 'layout')
				$this->_helper->layout->disableLayout();	
		$auth = Zend_Auth::getInstance()->getStorage()->read();
		if(!empty($auth))
		{
			$userid = $auth->id;		
			$role_id = $auth->emprole;
		}
		$settingsflag = $this->_request->getParam('tab');
		if(is_string($settingsflag))
		$settingsflag = intval($settingsflag);
		$settingsmodel = new Default_Model_Settings();
		$widgetsArr = '';
		$shortcutArr = '';
		$widgets = array();
		$shortcuts = array();
		$flag = '';
		$menuflag = '';
		$menucount = '';
		$shortflag = '';
		$shortcount = '';
		$flag = '';
		$menuids2 = array();$mids = array();
		$menuids = $settingsmodel->getallmenuids($userid);	
		$menuidsString = '';
		$shortcuts = array();
		$widgets = array();
		$menuids_SC='';
		$menuids_W='';
		if(!empty($menuids))
		{
			for($i=0;$i<sizeof($menuids);$i++)
			{
				$menuidsString .= $menuids[$i]['menuid'].',';
				if($menuids[$i]['flag'] == 1) //widgets
					$menuids_W = explode(',',$menuids[$i]['menuid']);
				if($menuids[$i]['flag'] == 2) //short cuts
					$menuids_SC = explode(',',$menuids[$i]['menuid']);		
			}
			$idCsv=1;	//flag 
			$menuIdsArr=array();
			$menuIdsCsv="";
			$menusString="";
			$privilege_model = new Default_Model_Privileges();
			$privilegesofObj = $privilege_model->getObjPrivileges(trim($menuidsString,','),"",$role_id,$idCsv);
			$menuwithaddprivilegeArr = array(SITEPREFERENCE,LEAVEREQUEST,IDENTITYCODES,IDENTITYDOCUMENTS);
			if(!empty($privilegesofObj) && isset($privilegesofObj))
			{
				for($i=0;$i<sizeof($privilegesofObj);$i++)
				{
						if($privilegesofObj[$i]['isactive'] == 1)
							array_push($menuIdsArr,$privilegesofObj[$i]['object']);
				}
				$menuIdsCsv= implode(",",$menuIdsArr);
			}
				
			array_push($mids,$menuids_SC);	
			array_push($mids,$menuids_W);
			$menuidsString = rtrim($menuidsString,','); 
			if($menuIdsCsv != "")	$menuidsString =$menuIdsCsv;
			$menuidnamesData = $settingsmodel->getallmenunames($menuidsString,1);
			for($i=0;$i<sizeof($menuidnamesData);$i++)
			{	
				if(!empty($menuids_SC))
				{	
					 $size = (sizeof($menuids_SC) > 16) ? 16:sizeof($menuids_SC);
					for($k=0;$k<$size ;$k++)
					{	
						if($menuidnamesData[$i]['id'] == $menuids_SC[$k])
						{
							$shortcuts[$k]['id'] = $menuids_SC[$k];
							$shortcuts[$k]['menuName'] = $menuidnamesData[$i]['menuName'];					
							$shortcuts[$k]['iconPath'] = $menuidnamesData[$i]['iconPath'];					
						}
					}
				}
				
				if(!empty($menuids_W))
				{
					$size = (sizeof($menuids_W) > 10) ? 10:sizeof($menuids_W);
					for($j=0;$j<$size;$j++)
					{
						if($menuidnamesData[$i]['id'] == $menuids_W[$j])
						{
							$widgets[$j]['id'] = $menuids_W[$j];
							$widgets[$j]['menuName'] = $menuidnamesData[$i]['menuName'];					
							$widgets[$j]['iconPath'] = $menuidnamesData[$i]['iconPath'];
						}
					}
				}
			}
			$this->view->menuidcount=1;
			$this->view->iconidcount=1; 
			$this->view->menunamearray=$widgets;
			$this->view->iconnamearray=$shortcuts;
			$this->view->message = $userid; 
		}
		$this->view->layoutflag=$layoutflag;
		$this->view->settingsflag=$settingsflag;
        $this->view->role_id = $role_id;
	}
	public function prepareGrid($menuName,$objectName,$objectId,$con)
	{
		try
		{
			if($objectName == 'myholidaycalendar')
			{
				$objectName = 'Empcalendardates';
			}
			else if($objectName == 'pendingvacationhistory')
			{
				$objectName = 'Employeeleaves';
			}
			$gridFields = $this->buildgridfields($objectId);
			$gridHelper = $this->_helper->GridHelper;
			$this->_config = new Zend_Config_Ini("../application/configs/grid.ini", "production");
			$grid1 = "";
			$grid1 = new Bvb_Grid_Deploy_JqGrid($this->_config->toArray());
			if($con == 1)
				$gridHelper->configG1($grid1, $menuName, 'wg_'.$objectName,$gridFields, $objectName);
			else if($con == 2)
				$gridHelper->configG1($grid1, $menuName, 'sh_'.$objectName,$gridFields, $objectName);
			return $grid1->deploy();		
		
		}
		catch(Exception $e)
		{
			echo $e->getMessage();
		}
	}
	public function buildgridfields($objectId,$objectName)
	{
		$GridMxmlarray = $this->_helper->HtmlFormCodeGeneratorHelper->getObjectMxml($objectId,$objectName);
		$fieldcount = 0;
		$lookupcount = 0;
		$gridFields["Fields"]= array();
		$gridFields["Lookup"]= array();
		foreach($GridMxmlarray['fields'] as $field){
			if($field['dataType']!='LOOKUP' && $field['dataType']!='GRID' && $field['gridField']==1){
				if($field['fieldName']){
					$gridFields["Fields"][$fieldcount]['FieldName'] = $field['fieldName'];
					$gridFields["Fields"][$fieldcount]['FieldLabel'] = $field['fieldLabel'];
					$fieldcount++;
				}
			}elseif($field['dataType'] == 'LOOKUP' && $field['gridField']==1){

				$gridFields["Lookup"][$lookupcount]['FieldName'] = $field['fieldName'];
				$gridFields["Lookup"][$lookupcount]['FieldLabel'] = $field['fieldLabel'];
				$gridFields["Lookup"][$lookupcount]['LookupColumn'] = $field['column'];
				$lookupcount++;
			}
		}
		return $gridFields;
	}
	
	
	public function savemenuwidgetsAction()
	{
	
	  $auth = Zend_Auth::getInstance()->getStorage()->read();
	  $userid = $auth->id;
	  
	  $date = new Zend_Date();
	  $totalarray = $this->_request->getParam('totalarray');
	  $arraytype = $totalarray[0];
	  if(is_array($arraytype)){
		  $menuidstring = implode(",", $totalarray[0]);
		  $menutype = $totalarray[1];
	  }else{
	      $menuidstring = '';
		  $menutype = $totalarray[0];
	  }
	  $flag = ''; 
	    if($menutype == 'Widgets'){
	     $flag = 1;
		} 
		 if($menutype == 'Shortcuts'){
         $flag = 2;
        }		 
	  $successmessage['result']= '';

	  $settingsmodel = new Default_Model_Settings();

       $getdatacount = $settingsmodel->getActiveCountSettings($userid,$flag);
	   $where='';
	   if($getdatacount[0]['count']>0){
		      $where = array('userid=?'=>$userid,
						           'flag=?'=>$flag,
						           'isactive=?'=>1 
								);

		      $data = array(		
                            'menuid'=>$menuidstring, 
                    		'modified'=>$date->get('yyyy-MM-dd HH:mm:ss')
    					 );
          $id = $settingsmodel->addOrUpdateMenus($data, $where);
		  $successmessage['result'] = 'update';

       }else{
		   $data = array(		
    							'userid'=>$userid,
                                'menuid'=>$menuidstring, 
                                'flag'=>$flag, 
       							'isactive'=> 1,
    							'created'=>$date->get('yyyy-MM-dd HH:mm:ss'),
    							'modified'=>$date->get('yyyy-MM-dd HH:mm:ss')
    					 );
          $id = $settingsmodel->addOrUpdateMenus($data, $where);
		  if($id !='')
		     $successmessage['result']= 'save';

       }
	  $this->_helper->json($successmessage);
   
	}

	public function getmenunameAction(){
       $menuid = $this->_request->getParam('menuid');
	   $menuurl = $this->_request->getParam('menuurl');
       $successmessage['result']= ''; 

   	   $settingsmodel = new Default_Model_Settings();
	   $getmenuname = $settingsmodel->getMenuName($menuid);
	  
       if(!empty($getmenuname))
           $successmessage['result']= 'success';
       else
		   $successmessage['result']= 'error';
	   $data = array('menuname'=>$getmenuname[0]['menuName'],
                      'message'=>$successmessage['result'],
                      
        );
	   $this->_helper->json($data);
	}
	
	public function fetchmenunameAction(){
		$auth = Zend_Auth::getInstance();
		$role_id = 1;
            if($auth->hasIdentity())
            {                                
                $role_id = $auth->getStorage()->read()->emprole;
            }
            
	   $menuid = $this->_request->getParam('menuid');
	   $tabFlag = $this->_request->getParam('tabFlag');
	   $successmessage['result']= ''; 
	   $idCsv = 0;
	   
	   $privilege_model = new Default_Model_Privileges();
	     $settingsmodel = new Default_Model_Settings();
	   if(is_numeric($menuid))
	   {
	   $privilegesofObj = $privilege_model->getObjPrivileges($menuid,"",$role_id,$idCsv);	
	   $getmenuname = $settingsmodel->fetchMenuName($menuid);	
	   if($privilegesofObj['isactive'] == 1)
	   {
	       if(!empty($getmenuname)){
	            $data = array('menuname'=>$getmenuname[0]['menuName'],
		                 'menuicon'=>$getmenuname[0]['iconPath'], 
						  'menuurl'=>$getmenuname[0]['url'], 
	                      'message'=>'success'
	                      );
			}	
	       else{
			   $data = array(
	                      'message'=>'error'
	            );
			}
	   }else
	   {
	    $data = array(
	                      'message'=>'error'
	            );	
	   }         
		/**
			*	Added By	:	Sapplica.
			*	Date of Modification	:	30/08/2013
			*	Purpose	:	Some of the menus should not be draggable for widgets
			*	Modified By	:	MAINAK.		
		**/
		//	Only for Widgets, Organisation Info,Organisation structure,Organisation hierarchy, Site Preferences,leave request,My details,Identity codes and some menus should not be draggable ....
		//Removed: Manager status
	   if($tabFlag != "" && $tabFlag == "widgets")
		{
			$menusNotdraggable = array(REPORTS,ORGANISATIONINFO,STRUCTURE,HEIRARCHY,IDENTITYCODES,IDENTITYDOCUMENTS,ANNOUNCEMENTS,INITIALIZE_APPRAISAL,APPRAISALRATINGS,APPRAISAL_SETTINGS,INITIALIZE_FEEDFORWARD,MANAGER_FEEDFORWARD,MY_TEAM_APPRAISAL,EXPENSES,EXPENSE_CATEGORY,EXPENSE_PAYMENTMODE,EXPENSE_PAYMENTMODE,RECEIPTS,TRIPS,ADVANCES,MYADVANCES,EMPLOYEEADVANCES,SUB_EXPENSES,MYEMPLOYEEEXPENSES,ASSETS,ASSETCATEGORIES,SUBASSET,VIOLATION_TYPE,RAISE_INCIDENT,MY_INCIDENT,TEAM_INCIDENT);
			if(in_array($menuid,$menusNotdraggable))
				$data = array('message'=>'error');
		}
		
	   }else
	   {
	   	     $data = array('message'=>'error');
	   }	
	   

	   $this->_helper->json($data);

	}
	/** test function for breadcrumbs **/

	public function getnavidsAction()
	{
		$settingsmodel = new Default_Model_Settings();

		$result = $settingsmodel->getNavigationIds();
		$menuIdsArr = array();
		$navIdsArr = array();
		$parentMenuIdsArr = array();
		for($m=0;$m < sizeof($result); $m++)
		{
			$menuIdsArr[$result[$m]['menuId']] = $result[$m]['parent'];			
		}
		for($i = 0; $i < sizeof($result); $i++)
		{
			echo "<pre>";
			if($result[$i]['parent'] == 0)
			{
				$navIdsArr[$result[$i]['menuId']] = ",".$result[$i]['menuId'].",";
			}
			else if(isset($menuIdsArr[$result[$i]['parent']]))
			{
				if($menuIdsArr[$result[$i]['parent']] == 0)
					$navIdsArr[$result[$i]['menuId']] = ",".$result[$i]['parent'].",".$result[$i]['menuId'].",";
				else if($menuIdsArr[$menuIdsArr[$result[$i]['parent']]] != 0)
					$navIdsArr[$result[$i]['menuId']] = ",".$menuIdsArr[$menuIdsArr[$result[$i]['parent']]].",".$menuIdsArr[$menuIdsArr[$result[$i]['menuId']]].",".$result[$i]['parent'].",".$result[$i]['menuId'].",";
				else if($menuIdsArr[$menuIdsArr[$result[$i]['parent']]] == 0)
					$navIdsArr[$result[$i]['menuId']] = ",".$menuIdsArr[$menuIdsArr[$result[$i]['menuId']]].",".$result[$i]['parent'].",".$result[$i]['menuId'].",";
			}
		}
		
		
		for($p = 0;$p < sizeof($result);$p++)
		{
			if(isset($navIdsArr[$result[$p]['menuId']]))
			{
				
				
				
				$settingsmodel->insertnavid($result[$p]['menuId'],$navIdsArr[$result[$p]['menuId']]);
			}
		}

		exit();
	}
	
	public function getopeningpositondateAction()
	{
	   $settingsmodel = new Default_Model_Settings();
	   $openingpositiondate = $settingsmodel->getOpeningPositinDate();
	    $successmessage['result']= ''; 
	   if(sizeof($openingpositiondate) > 1){
	        $successmessage['result']= 'success'; 
	   }else{
	         $successmessage['result']= 'error'; 
	   }
	    $this->_helper->json($successmessage['result']);
	}
	public function menuworkAction()
	{
		$this->_helper->layout->disableLayout();
		$this->view->message = 'Menu work is in progress';
	}
	
	 public function employeeimageupdateAction()
    {	
	
       $userid = $this->_request->getParam('user_id');             
        $profileimagepath = $this->_request->getParam('profile_image');
        if($profileimagepath !='')
        {
           if(file_exists(USER_PREVIEW_UPLOAD_PATH.'//'.$profileimagepath))
           {
                copy(USER_PREVIEW_UPLOAD_PATH.'//'.$profileimagepath, USER_UPLOAD_PATH.'//'.$profileimagepath);
               unlink(USER_PREVIEW_UPLOAD_PATH.'//'.$profileimagepath);                
           }    			
        }              				
        $usermodel = new Default_Model_Users();                   					
        $data = array('profileimg'=>$profileimagepath,
                      
					  );							  				               
        $where = array("id=?" => $userid);                                    
        $status = $usermodel->addOrUpdateProfileImage($data,$where); 
        
        
        if($status == 'update')
        {
        	$update_query = "update main_employees_summary set profileimg = '".$profileimagepath."',
                             modifieddate = utc_timestamp() where user_id = '".$userid."'";
        	$update_requesthistory_query = "update main_request_history set emp_profileimg = '".$profileimagepath."',
                             modifieddate = utc_timestamp() where emp_id = '".$userid."'";
        	$db = Zend_Db_Table::getDefaultAdapter();
        	$result = $db->query($update_query);
        	$db->query($update_requesthistory_query);
        	
        }
         
        $this->_helper->json($status);
        
	}

}