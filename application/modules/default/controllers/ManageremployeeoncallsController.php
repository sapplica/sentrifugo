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

class Default_ManageremployeeoncallsController extends Zend_Controller_Action
{

    private $options;
	public function preDispatch()
	{
		 
		
	}
	
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
		
    }

    public function indexAction()
    {
	    $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		}
		$oncallrequestmodel = new Default_Model_Oncallrequest();	
        $call = $this->_getParam('call');
		if($call == 'ajaxcall')
				$this->_helper->layout->disableLayout();
		
		$view = Zend_Layout::getMvcInstance()->getView();		
		$objname = $this->_getParam('objname');
		$refresh = $this->_getParam('refresh');
		$dashboardcall = $this->_getParam('dashboardcall');
		
		$data = array();
		$searchQuery = '';
		$searchArray = array();
		$tablecontent='';
		
		if($refresh == 'refresh')
		{
		    if($dashboardcall == 'Yes')
				$perPage = DASHBOARD_PERPAGE;
			else	
				$perPage = PERPAGE;
			$sort = 'ASC';$by = 'oncallstatus';$pageNo = 1;$searchData = '';
		}
		else 
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'ASC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'oncallstatus';
			if($dashboardcall == 'Yes')
				$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
			else 
			    $perPage = $this->_getParam('per_page',PERPAGE);
			$pageNo = $this->_getParam('page', 1);
			/** search from grid - START **/
			$searchData = $this->_getParam('searchData');	
			$searchData = rtrim($searchData,',');
			/** search from grid - END **/
		}
		
							
		$objName = 'manageremployeeoncalls';
		$queryflag = '';
		$dataTmp = $oncallrequestmodel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall,$objName,$queryflag);     
		
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
    }
	
     public function viewAction()
	{	
	    $auth = Zend_Auth::getInstance();
     		if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
			}
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		$objName = 'manageremployeeoncalls';
		$manageroncallrequestform = new Default_Form_manageroncallrequest();
		$manageroncallrequestform->removeElement("submit");
		$elements = $manageroncallrequestform->getElements();
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)
			{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
				$element->setAttrib("disabled", "disabled");
					}
        	}
        }
		
		try
		{
			    if($id && is_numeric($id) && $id>0)
                {
                	$this->_redirect('manageremployeeoncalls/edit/id/'.$id);				
					$oncallrequestmodel = new Default_Model_Oncallrequest();
					$usersmodel= new Default_Model_Users();
					$flag = 'true'; 
					
					$userid = $oncallrequestmodel->getUserID($id);
					$getreportingManagerArr = $oncallrequestmodel->getReportingManagerId($id);
					$reportingManager = $getreportingManagerArr[0]['repmanager'];
					if($reportingManager != $loginUserId)
					   $flag = 'false';
					if(!empty($userid))
					 $isactiveuser = $usersmodel->getUserDetailsByID($userid[0]['user_id']);
					else
					 $this->view->rowexist = "rows"; 
					 
					if(!empty($userid) && !empty($isactiveuser) && $flag == 'true')
					{ 
						$data = $oncallrequestmodel->getOncallRequestDetails($id);
						if(!empty($data) && $data[0]['oncallstatus'] == 'Pending for approval')
							{
								$data = $data[0];
								$employeeoncalltypemodel = new Default_Model_Employeeoncalltypes();
								$usersmodel = new Default_Model_Users();
										
								$employeeoncalltypeArr = $employeeoncalltypemodel->getsingleEmployeeOncalltypeData($data['oncalltypeid']);
								if($employeeoncalltypeArr !='norows')
								{
									$manageroncallrequestform->oncalltypeid->addMultiOption($employeeoncalltypeArr[0]['id'],$employeeoncalltypeArr[0]['oncalltype']);		   
								}
								  
								if($data['oncallday'] == 1)
								{
								  $manageroncallrequestform->oncallday->addMultiOption($data['oncallday'],'Full Day');		   
								}
								else 
								{
								  $manageroncallrequestform->oncallday->addMultiOption($data['oncallday'],'Half Day');
								}					
							   
								$employeenameArr = $usersmodel->getUserDetailsByID($data['user_id']);	
								$manageroncallrequestform->populate($data);							
															
															$from_date = sapp_Global::change_date($data['from_date'], 'view');
															$to_date = sapp_Global::change_date($data['to_date'], 'view');
															$appliedon = sapp_Global::change_date($data['createddate'], 'view');
															
								$manageroncallrequestform->from_date->setValue($from_date);
								$manageroncallrequestform->to_date->setValue($to_date);
								$manageroncallrequestform->createddate->setValue($appliedon);
								$manageroncallrequestform->appliedoncallsdaycount->setValue($data['appliedoncallscount']);
								$manageroncallrequestform->employeename->setValue($employeenameArr[0]['userfullname']);
								$manageroncallrequestform->setDefault('oncalltypeid',$data['oncalltypeid']);
								$manageroncallrequestform->setDefault('oncallday',$data['oncallday']);
								$this->view->controllername = $objName;
								$this->view->id = $id;
								$this->view->form = $manageroncallrequestform;
								$this->view->data = $data;
							}
						else
							{
								   $this->view->rowexist = "rows";
							}				
					}
					else
					{
						   $this->view->rowexist = "rows";
					}
				}
				else
				{
					   $this->view->rowexist = "rows";
				}
			
        }
        catch(Exception $e)
		{
			 $this->view->rowexist = 'norows';
		}  		
			
	}
	
	
	public function editAction()
	{	
	    $auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
				$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		
		
		try
		{
		        if($id && is_numeric($id) && $id>0)
                {
                	$manageroncallrequestform = new Default_Form_manageroncallrequest();
					$oncallrequestmodel = new Default_Model_Oncallrequest();
					$usersmodel= new Default_Model_Users();
					$flag = 'true';
					$userid = $oncallrequestmodel->getUserID($id);
					$getreportingManagerArr = $oncallrequestmodel->getReportingManagerId($id);
					$reportingManager = $getreportingManagerArr[0]['repmanager'];
					if($reportingManager != $loginUserId)
					   $flag = 'false';
					if(!empty($userid))
					 $isactiveuser = $usersmodel->getUserDetailsByID($userid[0]['user_id']);
					else
					 $this->view->rowexist = "rows"; 				
				
					if(!empty($userid) && !empty($isactiveuser) && $flag=='true')
					{	
						$data = $oncallrequestmodel->getOncallRequestDetails($id);				
						if(!empty($data) && ($data[0]['oncallstatus'] == 'Pending for approval' || $data[0]['oncallstatus'] == 'Approved'))
							{ 
								$data = $data[0]; 
								$reason = $data['reason'];
								$appliedoncallscount = $data['appliedoncallscount'];
								$employeeid = $data['user_id']; 
								$oncalltypeid = $data['oncalltypeid'];
								$employeeoncalltypemodel = new Default_Model_Employeeoncalltypes();
								$usersmodel = new Default_Model_Users();
								$employeesmodel = new Default_Model_Employees();
								$businessunitid = '';
								$loggedInEmployeeDetails = $employeesmodel->getLoggedInEmployeeDetails($employeeid);
								 if($loggedInEmployeeDetails[0]['businessunit_id'] != '')
									$businessunitid = $loggedInEmployeeDetails[0]['businessunit_id'];
										
							$employeeoncalltypeArr = $employeeoncalltypemodel->getsingleEmployeeOncalltypeData($data['oncalltypeid']);
								if($employeeoncalltypeArr != 'norows')
								{
									$manageroncallrequestform->oncalltypeid->addMultiOption($employeeoncalltypeArr[0]['id'],$employeeoncalltypeArr[0]['oncalltype']);
									$data['oncalltypeid']=$employeeoncalltypeArr[0]['oncalltype'];	   
								}
								else
					            {
								   $data['oncalltypeid'] ="...";
						        } 	
								
								if($data['oncallday'] == 1)
								{
								  $manageroncallrequestform->oncallday->addMultiOption($data['oncallday'],'Full Day');	
								  $data['oncallday']=	'Full Day';    
								}
								else 
								{
								  $manageroncallrequestform->oncallday->addMultiOption($data['oncallday'],'Half Day');	
								  $data['oncallday']='Half Day'; 				  
								}					
							   
								$employeenameArr = $usersmodel->getUserDetailsByID($data['user_id']);
								$employeeemail = $employeenameArr[0]['emailaddress'];					
								$employeename = $employeenameArr[0]['userfullname'];
								$manageroncallrequestform->populate($data);
								
								if($data['oncallstatus'] == 'Approved') {
									$manageroncallrequestform->managerstatus->setLabel("Cancel");
									$manageroncallrequestform->managerstatus->clearMultiOptions();
	                                $manageroncallrequestform->managerstatus->addMultiOption(3,"Cancel");
								}
																						
															$from_date = sapp_Global::change_date($data['from_date'], 'view');
															$to_date = sapp_Global::change_date($data['to_date'], 'view');
															$appliedon = sapp_Global::change_date($data['createddate'], 'view');
								 //to show Oncall Request history in view
								 $oncallrequesthistory_model = new Default_Model_Oncallrequesthistory();
								 $oncall_history = $oncallrequesthistory_model->getOncallRequestHistory($id);
							  	 $this->view->oncall_history = $oncall_history;
							    //end							
								$manageroncallrequestform->from_date->setValue($from_date);
								$manageroncallrequestform->to_date->setValue($to_date);
								$manageroncallrequestform->createddate->setValue($appliedon);
								$manageroncallrequestform->appliedoncallsdaycount->setValue($data['appliedoncallscount']);
								$manageroncallrequestform->employeename->setValue($employeenameArr[0]['userfullname']);
								$manageroncallrequestform->setDefault('oncalltypeid',$data['oncalltypeid']);
								$manageroncallrequestform->setDefault('oncallday',$data['oncallday']);
								$this->view->id = $id;
								$this->view->form = $manageroncallrequestform;
								$this->view->data = $data;
								$manageroncallrequestform->setAttrib('action',BASE_URL.'manageremployeeoncalls/edit/id/'.$id);
							}
							else
							{
								   $this->view->rowexist = "rows";
							}
					} 
					else
					{
						   $this->view->rowexist = "rows";
					}
                }
				else
				{
					   $this->view->rowexist = "rows";
				} 					
			
		}
		catch(Exception $e)
		{
			 $this->view->rowexist = 'norows';
		}
		if($this->getRequest()->getPost()){
      		$result = $this->save($manageroncallrequestform,$appliedoncallscount,$employeeemail,$employeeid,$employeename,$from_date,$to_date,$reason,$businessunitid,$oncalltypeid,$data);	
		    $this->view->msgarray = $result; 
		}
	}
	
	public function save($manageroncallrequestform,$appliedoncallscount,$employeeemail,$employeeid,$userfullname,$from_date,$to_date,$reason,$businessunitid,$oncalltypeid,$oncallreqdata)
	{
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		} 		
     		if($manageroncallrequestform->isValid($this->_request->getPost())){
			    $id = $this->_request->getParam('id'); 
			    $managerstatus = $this->_request->getParam('managerstatus');
			    $comments = $this->_request->getParam('comments');
				$date = new Zend_Date();
				$oncallrequestmodel = new Default_Model_Oncallrequest(); 
				$employeeoncalltypesmodel = new Default_Model_Employeeoncalltypes();
				$usersmodel = new Default_Model_Users();
				$actionflag = '';
				$tableid  = ''; 
				$status = '';
				$messagestr = '';
				//$oncalltypetext = '';
				$oncalltypeArr = $employeeoncalltypesmodel->getOncalltypeDataByID($oncalltypeid);
				$repManagerDetails = $usersmodel->getUserDetailsByID($oncallreqdata['rep_mang_id']);
				if(!empty($repManagerDetails)) {
					$repMgrEmail = $repManagerDetails[0]['emailaddress'];
					$repMgrName = $repManagerDetails[0]['userfullname'];
				}
				if($managerstatus == 1 && !empty($oncalltypeArr))
				{
				  if($oncalltypeArr[0]['oncallpredeductable'] == 1) {		
				  	$updateemployeeoncall = $oncallrequestmodel->updateemployeeoncalls($appliedoncallscount,$employeeid);
				  }	
				  $status = 2; 
				  $messagestr = "On call request approved.";
				  //$oncalltypetext = $oncalltypeArr[0]['oncalltype'];
				}else if($managerstatus == 2)
				{
				  $status = 3;  
				  $messagestr = "On call request rejected.";
				}else if($managerstatus == 3 && !empty($oncalltypeArr))
				{
					if($oncallreqdata['oncallstatus']=='Approved') {
						if($oncalltypeArr[0]['oncallpredeductable'] == 1) {		
					  		$updateemployeeoncall = $oncallrequestmodel->updatecancelledemployeeoncalls($appliedoncallscount,$employeeid);
					  	}
					}
					$status = 4;  
				  	$messagestr = "On call request cancelled.";
				}
				  
				  if($managerstatus == 1 || $managerstatus == 2 || $managerstatus == 3)
				  {
				   $data = array( 'oncallstatus'=>$status,
				   				  'approver_comments'=> $comments,	
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
						$Id = $oncallrequestmodel->SaveorUpdateOncallRequest($data, $where);
						    if($Id == 'update')
							{
							   $tableid = $id;
							   $this->_helper->getHelper("FlashMessenger")->addMessage($messagestr);
							}   
							else
							{
							   $tableid = $Id; 	
								$this->_helper->getHelper("FlashMessenger")->addMessage($messagestr);					   
							}
								
							/** 
					oncall request history 
					**/
					if($Id == 'update')
					{
						
						if($managerstatus == 1)
						{
							$oncallstatus='Approved';
						}
						else if($managerstatus == 2)
						{
							$oncallstatus='Rejected';
						}
						else
						{
							$oncallstatus='Cancelled';
						}
						$history = 'On Call Request has been '.$oncallstatus.' by ';
						$oncallrequesthistory_model = new Default_Model_Oncallrequesthistory();
						$oncall_history = array(											
										'oncallrequest_id' =>$id ,
										'description' => $history,
										'createdby' => $loginUserId,
										'modifiedby' => $loginUserId,
										'isactive' => 1,
										'createddate' =>gmdate("Y-m-d H:i:s"),
										'modifieddate'=>gmdate("Y-m-d H:i:s"),
									   );
					    $where = '';
						$oncallhistory = $oncallrequesthistory_model->saveOrUpdateOncallRequestHistory($oncall_history,$where); 
					}		
					
                            /** MAILING CODE **/
							
							if($to_date == '' || $to_date == NULL)
								$to_date = $from_date;
								
							
							/* Mail to Employee */
								$options['header'] = 'On Call Request';
								$options['toEmail'] = $employeeemail;
								$options['toName'] = $userfullname;
								if($messagestr == 'On call request approved.'){
									$options['subject'] = $messagestr;
									$options['message'] = '<div>Hi,</div><div>The below oncall(s) has been approved.</div>';
								}elseif($messagestr == 'On call request rejected.'){ 
									$options['subject'] = $messagestr;
									$options['message'] = '<div>Hi,</div><div>The below oncall(s) has been rejected. </div>';
								}else{
									$options['subject'] = $messagestr;
									$options['message'] = '<div>Hi,</div><div>The below oncall(s) has been cancelled. </div>';
								}	
								$options['message'] .= '<div>
                <table width="100%" cellspacing="0" cellpadding="15" border="0" style="border:3px solid #BBBBBB; font-size:16px; font-family:Arial, Helvetica, sans-serif; margin:30px 0 30px 0;" bgcolor="#ffffff">
                      <tbody><tr>
                        <td width="28%" style="border-right:2px solid #BBBBBB;">Employee Name</td>
                        <td width="72%">'.$userfullname.'</td>
                      </tr>
                      <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">No. of Day(s)</td>
                        <td>'.$appliedoncallscount.'</td>
                      </tr>
                      <tr>
                        <td style="border-right:2px solid #BBBBBB;">From</td>
                        <td>'.$from_date.'</td>
                      </tr>
                      <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">To</td>
                        <td>'.$to_date.'</td>
            	     </tr>
                      <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">Reason for On Call</td>
                        <td>'.$reason.'</td>
                  </tr>
                </tbody></table>

            </div>
            <div style="padding:20px 0 10px 0;">Please <a href="'.BASE_URL.'/index/popup" target="_blank" style="color:#b3512f;">click here</a> to login and check the oncall details.</div>';	
                                $result = sapp_Global::_sendEmail($options);
							/* END */	
                                
                                
		/* Mail to Reporting Manager */
            if(!empty($repMgrEmail) && !empty($repMgrName)) {                    
								$options['header'] = 'On Call Request';
								$options['toEmail'] = $repMgrEmail;
								$options['toName'] = $repMgrName;
								if($messagestr == 'On call request approved.'){
									$options['subject'] = $messagestr;
									$options['message'] = '<div>Hi,</div><div>The below oncall(s) has been approved.</div>';
								}elseif($messagestr == 'On call request rejected.'){ 
									$options['subject'] = $messagestr;
									$options['message'] = '<div>Hi,</div><div>The below oncall(s) has been rejected. </div>';
								}else{
									$options['subject'] = $messagestr;
									$options['message'] = '<div>Hi,</div><div>The below oncall(s) has been cancelled. </div>';
								}	
								$options['message'] .= '<div>
                <table width="100%" cellspacing="0" cellpadding="15" border="0" style="border:3px solid #BBBBBB; font-size:16px; font-family:Arial, Helvetica, sans-serif; margin:30px 0 30px 0;" bgcolor="#ffffff">
                      <tbody><tr>
                        <td width="28%" style="border-right:2px solid #BBBBBB;">Employee Name</td>
                        <td width="72%">'.$repMgrName.'</td>
                      </tr>
                      <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">No. of Day(s)</td>
                        <td>'.$appliedoncallscount.'</td>
                      </tr>
                      <tr>
                        <td style="border-right:2px solid #BBBBBB;">From</td>
                        <td>'.$from_date.'</td>
                      </tr>
                      <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">To</td>
                        <td>'.$to_date.'</td>
            	     </tr>
                      <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">Reason for On Call</td>
                        <td>'.$reason.'</td>
                  </tr>
                </tbody></table>

            </div>
            <div style="padding:20px 0 10px 0;">Please <a href="'.BASE_URL.'/index/popup" target="_blank" style="color:#b3512f;">click here</a> to login and check the oncall details.</div>';	
                                $result = sapp_Global::_sendEmail($options);
            }                    
							/* END */                                
							
							/* Mail to HR */	
								if (defined('LV_HR_'.$businessunitid) && $businessunitid !='')
								{
								$options['header'] = 'On Call Request';
								$options['toEmail'] = constant('LV_HR_'.$businessunitid);
								$options['toName'] = 'On Call Management';
								if($messagestr == 'On call request approved.'){
									$options['subject'] = $messagestr;
									$options['message'] = '<div>Hi,</div><div>The below oncall(s) has been approved.</div>';
								}elseif($messagestr == 'On call request rejected.'){ 
									$options['subject'] = $messagestr;
									$options['message'] = '<div>Hi,</div><div>The below oncall(s) has been rejected. </div>';
								}else{
									$options['subject'] = $messagestr;
									$options['message'] = '<div>Hi,</div><div>The below oncall(s) has been cancelled. </div>';
								}	
								$options['message'] .= '<div>
                <table width="100%" cellspacing="0" cellpadding="15" border="0" style="border:3px solid #BBBBBB; font-size:16px; font-family:Arial, Helvetica, sans-serif; margin:30px 0 30px 0;" bgcolor="#ffffff">
                      <tbody>
	                      <tr>
	                        <td width="28%" style="border-right:2px solid #BBBBBB;">Employee Name</td>
	                        <td width="72%">'.$userfullname.'</td>
	                      </tr>
	                      <tr bgcolor="#e9f6fc">
	                        <td style="border-right:2px solid #BBBBBB;">No. of Day(s)</td>
	                        <td>'.$appliedoncallscount.'</td>
	                      </tr>
	                      <tr>
	                        <td style="border-right:2px solid #BBBBBB;">From</td>
	                        <td>'.$from_date.'</td>
	                      </tr>
	                      <tr bgcolor="#e9f6fc">
	                        <td style="border-right:2px solid #BBBBBB;">To</td>
	                        <td>'.$to_date.'</td>
     	            	  </tr>
		    	          <tr bgcolor="#e9f6fc">
	                        <td style="border-right:2px solid #BBBBBB;">Reason for On Call</td>
	                        <td>'.$reason.'</td>
	                      </tr>
                		</tbody>
                </table>

            </div>
            <div style="padding:20px 0 10px 0;">Please <a href="'.BASE_URL.'/index/popup" target="_blank" style="color:#b3512f;">click here</a> to login and check the oncall details.</div>';	
                                $result = sapp_Global::_sendEmail($options);	
								}
							/* END */	
						$menuID = MANAGEREMPLOYEEONCALLS;
						$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
	    			    $this->_redirect('manageremployeeoncalls');
					}	
							
			}else
			{
     			$messages = $manageroncallrequestform->getMessages();
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
	
	public function deleteAction()
	{
	     $auth = Zend_Auth::getInstance();
     		if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
				}
		 $id = $this->_request->getParam('objid');
		 $messages['message'] = '';
		 $actionflag = 3;
		    if($id)
			{
			$holidaygroupsmodel = new Default_Model_Holidaygroups(); 
			  $data = array('isactive'=>0);
			  $where = array('id=?'=>$id);
			  $Id = $holidaygroupsmodel->SaveorUpdateGroupData($data, $where);
			    if($Id == 'update')
				{
				   $menuID = HOLIDAYGROUPS;
				   $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id); 
				   $messages['message'] = 'Holiday group deleted successfully.';
				}   
				else
                   $messages['message'] = 'Holiday group cannot be deleted.';				
			}
			else
			{ 
			 $messages['message'] = 'Holiday group cannot be deleted.';
			}
			$this->_helper->json($messages);
		
	}
	
	

}

