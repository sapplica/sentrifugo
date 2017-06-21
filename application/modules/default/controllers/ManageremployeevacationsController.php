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

class Default_ManageremployeevacationsController extends Zend_Controller_Action
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
		$leaverequestmodel = new Default_Model_Leaverequest();	
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
			$sort = 'ASC';$by = 'leavestatus';$pageNo = 1;$searchData = '';
		}
		else 
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'ASC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'leavestatus';
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
		
							
		$objName = 'manageremployeevacations';
		$queryflag = '';
		$dataTmp = $leaverequestmodel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall,$objName,$queryflag);     
		
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
		$objName = 'manageremployeevacations';
		$managerleaverequestform = new Default_Form_managerleaverequest();
		$managerleaverequestform->removeElement("submit");
		$elements = $managerleaverequestform->getElements();
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
                	$this->_redirect('manageremployeevacations/edit/id/'.$id);				
					$leaverequestmodel = new Default_Model_Leaverequest();
					$usersmodel= new Default_Model_Users();
					$flag = 'true'; 
					
					$userid = $leaverequestmodel->getUserID($id);
					$getreportingManagerArr = $leaverequestmodel->getReportingManagerId($id);
					$reportingManager = $getreportingManagerArr[0]['repmanager'];
					$hrmanager = $getreportingManagerArr[0]['hrmanager'] ;
					if($reportingManager != $loginUserId && $hrmanager != $loginUserId)
					   $flag = 'false';
					if(!empty($userid))
					 $isactiveuser = $usersmodel->getUserDetailsByID($userid[0]['user_id']);
					else
					 $this->view->rowexist = "rows"; 
					 
					if(!empty($userid) && !empty($isactiveuser) && $flag == 'true')
					{ 
						$data = $leaverequestmodel->getLeaveRequestDetails($id);
						if(!empty($data) && $data[0]['leavestatus'] == 'Pending for approval')
							{
								$data = $data[0];
								$employeeleavetypemodel = new Default_Model_Employeeleavetypes();
								$usersmodel = new Default_Model_Users();
										
								$employeeleavetypeArr = $employeeleavetypemodel->getsingleEmployeeLeavetypeData($data['leavetypeid']);
								if($employeeleavetypeArr !='norows')
								{
									$managerleaverequestform->leavetypeid->addMultiOption($employeeleavetypeArr[0]['id'],utf8_encode($employeeleavetypeArr[0]['leavetype']));		   
								}
								  
								if($data['leaveday'] == 1)
								{
								  $managerleaverequestform->leaveday->addMultiOption($data['leaveday'],'Full Day');		   
								}
								else 
								{
								  $managerleaverequestform->leaveday->addMultiOption($data['leaveday'],'Half Day');
								}					
							   
								$employeenameArr = $usersmodel->getUserDetailsByID($data['user_id']);	
								$managerleaverequestform->populate($data);							
															
															$from_date = sapp_Global::change_date($data['from_date'], 'view');
															$to_date = sapp_Global::change_date($data['to_date'], 'view');
															$appliedon = sapp_Global::change_date($data['createddate'], 'view');
															
								$managerleaverequestform->from_date->setValue($from_date);
								$managerleaverequestform->to_date->setValue($to_date);
								$managerleaverequestform->createddate->setValue($appliedon);
								$managerleaverequestform->appliedleavesdaycount->setValue($data['appliedleavescount']);
								$managerleaverequestform->employeename->setValue($employeenameArr[0]['userfullname']);
								$managerleaverequestform->setDefault('leavetypeid',$data['leavetypeid']);
								$managerleaverequestform->setDefault('leaveday',$data['leaveday']);
								$this->view->controllername = $objName;
								$this->view->id = $id;
								$this->view->form = $managerleaverequestform;
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
                	$managerleaverequestform = new Default_Form_managerleaverequest();
					$leaverequestmodel = new Default_Model_Leaverequest();
					$usersmodel= new Default_Model_Users();
					$flag = 'true';
					$userid = $leaverequestmodel->getUserID($id);
					$getreportingManagerArr = $leaverequestmodel->getReportingManagerId($id);
					$reportingManager = $getreportingManagerArr[0]['repmanager'];
					$hrmanager = $getreportingManagerArr[0]['hrmanager'] ;
					if($reportingManager != $loginUserId && $hrmanager !=  $loginUserId )
					   $flag = 'false';
					if(!empty($userid))
					 $isactiveuser = $usersmodel->getUserDetailsByID($userid[0]['user_id']);
					else
					 $this->view->rowexist = "rows"; 				
				
					if(!empty($userid) && !empty($isactiveuser) && $flag=='true')
					{	
						$data = $leaverequestmodel->getLeaveRequestDetails($id);				
						if(!empty($data) && ($data[0]['leavestatus'] == 'Pending for approval' || $data[0]['leavestatus'] == 'Approved'))
							{ 
								$data = $data[0]; 
								$reason = $data['reason'];
								$appliedleavescount = $data['appliedleavescount'];
								$employeeid = $data['user_id']; 
								$leavetypeid = $data['leavetypeid'];
								$employeeleavetypemodel = new Default_Model_Employeeleavetypes();
								$usersmodel = new Default_Model_Users();
								$employeesmodel = new Default_Model_Employees();
								$businessunitid = '';
								$loggedInEmployeeDetails = $employeesmodel->getLoggedInEmployeeDetails($employeeid);
								 if($loggedInEmployeeDetails[0]['businessunit_id'] != '')
									$businessunitid = $loggedInEmployeeDetails[0]['businessunit_id'];
										
							$employeeleavetypeArr = $employeeleavetypemodel->getsingleEmployeeLeavetypeData($data['leavetypeid']);
								if($employeeleavetypeArr != 'norows')
								{
									$managerleaverequestform->leavetypeid->addMultiOption($employeeleavetypeArr[0]['id'],utf8_encode($employeeleavetypeArr[0]['leavetype']));
									$data['leavetypeid']=$employeeleavetypeArr[0]['leavetype'];	   
								}
								else
					            {
								   $data['leavetypeid'] ="...";
						        } 	
								
								if($data['leaveday'] == 1)
								{
								  $managerleaverequestform->leaveday->addMultiOption($data['leaveday'],'Full Day');	
								  $data['leaveday']=	'Full Day';    
								}
								else 
								{
								  $managerleaverequestform->leaveday->addMultiOption($data['leaveday'],'Half Day');	
								  $data['leaveday']='Half Day'; 				  
								}					
							   
								$employeenameArr = $usersmodel->getUserDetailsByID($data['user_id']);
								$employeeemail = $employeenameArr[0]['emailaddress'];					
								$employeename = $employeenameArr[0]['userfullname'];
								$managerleaverequestform->populate($data);
								
								if($data['leavestatus'] == 'Approved') {
									$managerleaverequestform->managerstatus->setLabel("Cancel");
									$managerleaverequestform->managerstatus->clearMultiOptions();
	                                $managerleaverequestform->managerstatus->addMultiOption(3,utf8_encode("Cancel"));
								}
																						
															$from_date = sapp_Global::change_date($data['from_date'], 'view');
															$to_date = sapp_Global::change_date($data['to_date'], 'view');
															$appliedon = sapp_Global::change_date($data['createddate'], 'view');
								 //to show Leave Request history in view
								 $leaverequesthistory_model = new Default_Model_Leaverequesthistory();
								 $leave_history = $leaverequesthistory_model->getLeaveRequestHistory($id);
							  	 $this->view->leave_history = $leave_history;
							    //end							
								$managerleaverequestform->from_date->setValue($from_date);
								$managerleaverequestform->to_date->setValue($to_date);
								$managerleaverequestform->createddate->setValue($appliedon);
								$managerleaverequestform->appliedleavesdaycount->setValue($data['appliedleavescount']);
								$managerleaverequestform->employeename->setValue($employeenameArr[0]['userfullname']);
								$managerleaverequestform->setDefault('leavetypeid',$data['leavetypeid']);
								$managerleaverequestform->setDefault('leaveday',$data['leaveday']);
								$this->view->id = $id;
								$this->view->form = $managerleaverequestform;
								$this->view->data = $data;
								$managerleaverequestform->setAttrib('action',BASE_URL.'manageremployeevacations/edit/id/'.$id);
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
      		$result = $this->save($managerleaverequestform,$appliedleavescount,$employeeemail,$employeeid,$employeename,$from_date,$to_date,$reason,$businessunitid,$leavetypeid,$data);	
		    $this->view->msgarray = $result; 
		}
	}
	
	public function save($managerleaverequestform,$appliedleavescount,$employeeemail,$employeeid,$userfullname,$from_date,$to_date,$reason,$businessunitid,$leavetypeid,$leavereqdata)
	{
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		} 		
     		if($managerleaverequestform->isValid($this->_request->getPost())){
			    $id = $this->_request->getParam('id'); 
			    $managerstatus = $this->_request->getParam('managerstatus');
			    $comments = $this->_request->getParam('comments');
				$date = new Zend_Date();
				$leaverequestmodel = new Default_Model_Leaverequest(); 
				$employeeleavetypesmodel = new Default_Model_Employeeleavetypes();
				$usersmodel = new Default_Model_Users();
				$actionflag = '';
				$tableid  = ''; 
				$status = '';
				$messagestr = '';
				$successmessagestr = '';
				//$leavetypetext = '';
				$leavetypeArr = $employeeleavetypesmodel->getLeavetypeDataByID($leavetypeid);
				$repManagerDetails = $usersmodel->getUserDetailsByID($leavereqdata['rep_mang_id']);
				if(!empty($repManagerDetails)) {
					$repMgrEmail = $repManagerDetails[0]['emailaddress'];
					$repMgrName = $repManagerDetails[0]['userfullname'];
				}
				if($managerstatus == 1 && !empty($leavetypeArr))
				{
				  if($leavetypeArr[0]['leavepredeductable'] == 1) {		
				  	$updateemployeeleave = $leaverequestmodel->updateemployeeleaves($appliedleavescount,$employeeid);
				  }	
				  $status = 2; 
				  $messagestr = "Leave request approved";
				 $successmessagestr  = "Leave request approved successfully.";
				  //$leavetypetext = $leavetypeArr[0]['leavetype'];
				}else if($managerstatus == 2)
				{
				  $status = 3;  
				  $messagestr = "Leave request rejected";
					$successmessagestr  = "Leave request rejected successfully.";
				}else if($managerstatus == 3 && !empty($leavetypeArr))
				{
					if($leavereqdata['leavestatus']=='Approved') {
						if($leavetypeArr[0]['leavepredeductable'] == 1) {		
					  		$updateemployeeleave = $leaverequestmodel->updatecancelledemployeeleaves($appliedleavescount,$employeeid);
					  	}
					}
					$status = 4;  
				  	$messagestr = "Leave request cancelled";
					$successmessagestr  = "Leave request cancelled successfully.";
				}
				  
				  if($managerstatus == 1 || $managerstatus == 2 || $managerstatus == 3)
				  {
				   $data = array( 'leavestatus'=>$status,
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
						$Id = $leaverequestmodel->SaveorUpdateLeaveRequest($data, $where);
						    if($Id == 'update')
							{
							   $tableid = $id;
							   $this->_helper->getHelper("FlashMessenger")->addMessage($successmessagestr);
							}   
							else
							{
							   $tableid = $Id; 	
								$this->_helper->getHelper("FlashMessenger")->addMessage($successmessagestr);					   
							}
								
							/** 
					leave request history 
					**/
					if($Id == 'update')
					{
						
						if($managerstatus == 1)
						{
							$leavestatus='Approved';
						}
						else if($managerstatus == 2)
						{
							$leavestatus='Rejected';
						}
						else
						{
							$leavestatus='Cancelled';
						}
						$history = 'Leave Request has been '.$leavestatus.' by ';
						$leaverequesthistory_model = new Default_Model_Leaverequesthistory();
						$leave_history = array(											
										'leaverequest_id' =>$id ,
										'description' => $history,
										'createdby' => $loginUserId,
										'modifiedby' => $loginUserId,
										'isactive' => 1,
										'createddate' =>gmdate("Y-m-d H:i:s"),
										'modifieddate'=>gmdate("Y-m-d H:i:s"),
									   );
					    $where = '';
						$leavehistory = $leaverequesthistory_model->saveOrUpdateLeaveRequestHistory($leave_history,$where); 
					}		
					
                            /** MAILING CODE **/
							
							if($to_date == '' || $to_date == NULL)
								$to_date = $from_date;
								
							
							/* Mail to Employee */
								$options['header'] = 'Leave Request';
								$options['toEmail'] = $employeeemail;
								$options['toName'] = $userfullname;
								if($messagestr ==  'Leave request approved'){
									$options['subject'] = $messagestr;
									$options['message'] = '<div>Hi,</div><div>The below leave(s) has been approved.</div>';
								}elseif($messagestr == 'Leave request rejected'){ 
									$options['subject'] = $messagestr;
									$options['message'] = '<div>Hi,</div><div>The below leave(s) has been rejected. </div>';
								}else{
									$options['subject'] = $messagestr;
									$options['message'] = '<div>Hi,</div><div>The below leave(s) has been cancelled. </div>';
								}	
								$options['message'] .= '<div>
                <table width="100%" cellspacing="0" cellpadding="15" border="0" style="border:3px solid #BBBBBB; font-size:16px; font-family:Arial, Helvetica, sans-serif; margin:30px 0 30px 0;" bgcolor="#ffffff">
                      <tbody><tr>
                        <td width="28%" style="border-right:2px solid #BBBBBB;">Employee Name</td>
                        <td width="72%">'.$userfullname.'</td>
                      </tr>
                      <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">No. of Day(s)</td>
                        <td>'.$appliedleavescount.'</td>
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
                        <td style="border-right:2px solid #BBBBBB;">Reason for Leave</td>
                        <td>'.$reason.'</td>
                  </tr>
                </tbody></table>

            </div>
            <div style="padding:20px 0 10px 0;">Please <a href="'.BASE_URL.'/index/popup" target="_blank" style="color:#b3512f;">click here</a> to login and check the leave details.</div>';	
                                $result = sapp_Global::_sendEmail($options);
							/* END */	
                                
                                
		/* Mail to Reporting Manager */
            if(!empty($repMgrEmail) && !empty($repMgrName)) {                    
								$options['header'] = 'Leave Request';
								$options['toEmail'] = $repMgrEmail;
								$options['toName'] = $repMgrName;
								if($messagestr == 'Leave request approved'){
									$options['subject'] = $messagestr;
									$options['message'] = '<div>Hi,</div><div>The below leave(s) has been approved.</div>';
								}elseif($messagestr == 'Leave request rejected'){ 
									$options['subject'] = $messagestr;
									$options['message'] = '<div>Hi,</div><div>The below leave(s) has been rejected. </div>';
								}else{
									$options['subject'] = $messagestr;
									$options['message'] = '<div>Hi,</div><div>The below leave(s) has been cancelled. </div>';
								}	
								$options['message'] .= '<div>
                <table width="100%" cellspacing="0" cellpadding="15" border="0" style="border:3px solid #BBBBBB; font-size:16px; font-family:Arial, Helvetica, sans-serif; margin:30px 0 30px 0;" bgcolor="#ffffff">
                      <tbody><tr>
                        <td width="28%" style="border-right:2px solid #BBBBBB;">Employee Name</td>
                        <td width="72%">'.$userfullname.'</td>
                      </tr>
                      <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">No. of Day(s)</td>
                        <td>'.$appliedleavescount.'</td>
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
                        <td style="border-right:2px solid #BBBBBB;">Reason for Leave</td>
                        <td>'.$reason.'</td>
                  </tr>
                </tbody></table>

            </div>
            <div style="padding:20px 0 10px 0;">Please <a href="'.BASE_URL.'/index/popup" target="_blank" style="color:#b3512f;">click here</a> to login and check the leave details.</div>';	
                                $result = sapp_Global::_sendEmail($options);
            }                    
							/* END */                                
							
							/* Mail to HR */	
								if (defined('LV_HR_'.$businessunitid) && $businessunitid !='')
								{
								$options['header'] = 'Leave Request';
								$options['toEmail'] = constant('LV_HR_'.$businessunitid);
								$options['toName'] = 'Leave Management';
								if($messagestr == 'Leave request approved'){
									$options['subject'] = $messagestr;
									$options['message'] = '<div>Hi,</div><div>The below leave(s) has been approved.</div>';
								}elseif($messagestr == 'Leave request rejected'){ 
									$options['subject'] = $messagestr;
									$options['message'] = '<div>Hi,</div><div>The below leave(s) has been rejected. </div>';
								}else{
									$options['subject'] = $messagestr;
									$options['message'] = '<div>Hi,</div><div>The below leave(s) has been cancelled. </div>';
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
	                        <td>'.$appliedleavescount.'</td>
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
	                        <td style="border-right:2px solid #BBBBBB;">Reason for Leave</td>
	                        <td>'.$reason.'</td>
	                      </tr>
                		</tbody>
                </table>

            </div>
            <div style="padding:20px 0 10px 0;">Please <a href="'.BASE_URL.'/index/popup" target="_blank" style="color:#b3512f;">click here</a> to login and check the leave details.</div>';	
                                $result = sapp_Global::_sendEmail($options);	
								}
							/* END */	
						$menuID = MANAGEREMPLOYEEVACATIONS;
						$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
	    			    $this->_redirect('manageremployeevacations');
					}	
							
			}else
			{
     			$messages = $managerleaverequestform->getMessages();
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

