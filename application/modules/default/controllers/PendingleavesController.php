
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

class Default_PendingleavesController extends Zend_Controller_Action
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
		$flag = $this->_request->getParam('flag');
		if(!empty($flag) && $flag=='delete') {
			$this->deleteAction();
		}else 
		{
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
					
				$sort = 'DESC';$by = 'modifieddate';$pageNo = 1;$searchData = '';
			}
			else 
			{
				$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
				$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'modifieddate';
				if($dashboardcall == 'Yes')
					$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
				else 
				    $perPage = $this->_getParam('per_page',PERPAGE);
				$pageNo = $this->_getParam('page', 1);
				// search from grid - START 
				$searchData = $this->_getParam('searchData');	
				$searchData = rtrim($searchData,',');
				// search from grid - END 
			}
			$leavesArray = array('pendingleaves','cancelleaves','approvedleaves','rejectedleaves');
			$objName = 'pendingleaves';
			$queryflag = 'pending';
			if(!empty($flag)){
				if(in_array($flag, $leavesArray)) {
					//$objName = ($flag=='approvedleaves')?'pendingleaves':$flag;
					$queryflag = substr($flag,0,-6);
				}
				if($flag=='all') {
					//$objName = 'pendingleaves';
					$queryflag= 'all';
				}
			}
			$dataTmp = $leaverequestmodel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall,$objName,$queryflag);     		
			
	        $leavesCountArray = sapp_Helper::getLeavesCountByCategory($loginUserId);
			
			array_push($data,$dataTmp);
			$this->view->dataArray = $data;
			$this->view->call = $call ;
			$this->view->objName = 'all';
			$this->view->flag = $flag ;
			$this->view->leavesCountArray = $leavesCountArray ;
			$this->view->messages = $this->_helper->flashMessenger->getMessages();
		}
    }
	
    public function viewAction()
	{	
	    $auth = Zend_Auth::getInstance();
     		if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					
			}
		$leaverequestmodel = new Default_Model_Leaverequest();	
		$id = $this->getRequest()->getParam('id');
		try
		{
			$useridArr = $leaverequestmodel->getUserID($id);
		  
			if(!empty($useridArr))
			{
			  $user_id = $useridArr[0]['user_id'];
					if($user_id == $loginUserId)
					{
					$callval = $this->getRequest()->getParam('call');
					if($callval == 'ajaxcall')
						$this->_helper->layout->disableLayout();
					$objName = 'pendingleaves';
					$leaverequestform = new Default_Form_leaverequest();
					$leaverequestform->removeElement("submit");
					$elements = $leaverequestform->getElements();
					if(count($elements)>0)
					{
						foreach($elements as $key=>$element)
						{
							if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
							$element->setAttrib("disabled", "disabled");
								}
						}
					}
						$data = $leaverequestmodel->getsinglePendingLeavesData($id);
						$data = $data[0];
						//echo"<pre>";print_r($data);exit;
						//if(!empty($data) && $data['leavestatus'] == 'Pending for approval')
						if(!empty($data))
							{
								$employeeleavetypemodel = new Default_Model_Employeeleavetypes();
								$usersmodel = new Default_Model_Users();
										
								$employeeleavetypeArr = $employeeleavetypemodel->getsingleEmployeeLeavetypeData($data['leavetypeid']);
								if($employeeleavetypeArr != 'norows')
								{
									$leaverequestform->leavetypeid->addMultiOption($employeeleavetypeArr[0]['id'],utf8_encode($employeeleavetypeArr[0]['leavetype']));
									$data['leavetypeid']=$employeeleavetypeArr[0]['leavetype'];
								
								}
								else
					            {
								   $data['leavetypeid'] ="...";
						        } 	   
								
								if($data['leaveday'] == 1)
								{
								  $leaverequestform->leaveday->addMultiOption($data['leaveday'],'Full Day');
									$data['leaveday']=	'Full Day';   
								}
								else 
								{
								  $leaverequestform->leaveday->addMultiOption($data['leaveday'],'Half Day');
								  $data['leaveday']='Half Day'; 
								}					
							   
								$repmngrnameArr = $usersmodel->getUserDetailsByID($data['rep_mang_id'],'all');	
								$leaverequestform->populate($data);
								
								
								$from_date = sapp_Global::change_date($data["from_date"], 'view');
								$to_date = sapp_Global::change_date($data["to_date"], 'view');
								$appliedon = sapp_Global::change_date($data["createddate"], 'view');
								
								$leaverequestform->from_date->setValue($from_date);
								$leaverequestform->to_date->setValue($to_date);
								$leaverequestform->createddate->setValue($appliedon);
								$leaverequestform->appliedleavesdaycount->setValue($data['appliedleavescount']);
								if(!empty($repmngrnameArr)){
								 $leaverequestform->rep_mang_id->setValue($repmngrnameArr[0]['userfullname']);
								  $data['rep_mang_id']=$repmngrnameArr[0]['userfullname'];
								}
								else {
								  $leaverequestform->rep_mang_id->setValue('');
								  $data['rep_mang_id']=$repmngrnameArr[0]['userfullname'];
								}
								
							  //to show Leave Request history in view
								 $leaverequesthistory_model = new Default_Model_Leaverequesthistory();
								 $leave_history = $leaverequesthistory_model->getLeaveRequestHistory($id);
							  	 $this->view->leave_history = $leave_history;
							  //end
								$leaverequestform->setDefault('leavetypeid',$data['leavetypeid']);
								$leaverequestform->setDefault('leaveday',$data['leaveday']);
								$this->view->controllername = $objName;
								$this->view->id = $id;
								$this->view->form = $leaverequestform;
								$this->view->data = $data;
								$this->view->reportingmanagerStatus = (!empty($repmngrnameArr))?$repmngrnameArr[0]['isactive']:'';
							}	
						
						else
						{
							$this->view->rowexist = "rows";
						}
				}else
				{
					$this->view->rowexist = "rows";
				}
			}else
			{
			   $this->view->rowexist = "norows";
			}  
        }
        catch(Exception $e){
			    $this->view->rowexist = "norows";
		    } 		
	}
	
	public function deleteAction()
	{
	     $auth = Zend_Auth::getInstance();
     		if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginUserEmail = $auth->getStorage()->read()->emailaddress;
					$loginUserName = $auth->getStorage()->read()->userfullname;
				}
		 $id = $this->_request->getParam('objid');
		 $messages['message'] = '';
		 $actionflag = 5;
		 $businessunitid = '';
		 $leavetypetext = '';
		    if($id)
			{
			$leaverequestmodel = new Default_Model_Leaverequest();
			$usersmodel = new Default_Model_Users();
			$employeesmodel = new Default_Model_Employees();
			$employeeleavetypesmodel = new Default_Model_Employeeleavetypes();
			
			$data = $leaverequestmodel->getsinglePendingLeavesData($id);
			$data = $data[0];
			$leavetypeArr = $employeeleavetypesmodel->getLeavetypeDataByID($data['leavetypeid']);
			if($data['leavestatus']=='Approved') {
				if(isset($data['from_date'])) {
					$leaveDate = date($data['from_date']);
					$todayDate = date("Y-m-d");
					if(strtotime($todayDate)>=strtotime($leaveDate)) {
						$messages['message'] = 'Leave request cannot be cancelled';  
						$messages['msgtype'] = 'error';
						$this->_helper->json($messages);
						return false;
					}
				}
			}
			if($data['leavestatus']=='Rejected' || $data['leavestatus']=='Cancel' || $loginUserId!=$data['user_id']) {
				$messages['message'] = 'Leave request cannot be cancelled';  
				$messages['msgtype'] = 'error';
				$this->_helper->json($messages);
				return false;
			}
			
			$loggedInEmployeeDetails = $employeesmodel->getLoggedInEmployeeDetails($loginUserId);
				 if($loggedInEmployeeDetails[0]['businessunit_id'] != '')
					$businessunitid = $loggedInEmployeeDetails[0]['businessunit_id'];
								
			  $dataarr = array('leavestatus'=>4,'modifieddate'=>gmdate("Y-m-d H:i:s"),'modifiedby'=>$loginUserId);
			  $where = array('id=?'=>$id);
			   
			  $Id = $leaverequestmodel->SaveorUpdateLeaveRequest($dataarr, $where);
			 if($data['leavestatus']=='Approved') {
			  if(!empty($leavetypeArr)) {
					if($leavetypeArr[0]['leavepredeductable'] == 1) {		
						$updateemployeeleave = $leaverequestmodel->updatecancelledemployeeleaves($data['appliedleavescount'],$data['user_id']);
					}
				}
			 }
				//saving in leaverequest history table
			    $history = 'Leave Request has been Cancelled by ';
				
				 $leaverequesthistory_model = new Default_Model_Leaverequesthistory();
				 $leave_history = array(											
								'leaverequest_id' =>$id,
								'description' => $history,
								//'emp_name' =>  ucfirst($auth->getStorage()->read()->userfullname),
								'createdby' => $loginUserId,
								'modifiedby' =>$loginUserId,
								'isactive' => 1,
								'createddate' =>gmdate("Y-m-d H:i:s"),
								'modifieddate'=> gmdate("Y-m-d H:i:s"),
							);
				  $where = '';
				$leavehistory = $leaverequesthistory_model->saveOrUpdateLeaveRequestHistory($leave_history,$where); 
			  //end
			  //$data = $leaverequestmodel->getsinglePendingLeavesData($id);
			  //$data = $data[0];
			  $appliedleavesdaycount = $data['appliedleavescount'];
			  $to_date = $data['to_date'];			  
			  $from_date = $data['from_date'];
			  $reason = $data['reason'];
			  $leavetypeid = $data['leavetypeid'];
			  $repmngrnameArr = $usersmodel->getUserDetailsByID($data['rep_mang_id']);
			  $reportingmanageremail = $repmngrnameArr[0]['emailaddress'];	
              $reportingmanagername	= $repmngrnameArr[0]['userfullname'];		  
			    if($Id == 'update')
				{
				   $menuID = PENDINGLEAVES;
				   $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id); 
				    /** MAILING CODE **/
					
					if($to_date == '' || $to_date == NULL)
				      $to_date = $from_date;
							/* Mail to Employee */
								$options['subject'] = 'Leave request cancelled';
								$options['header'] = 'Leave Request';
								$options['toEmail'] = $loginUserEmail;	
								$options['toName'] = $loginUserName;
								$options['message'] = '<div>Hi,</div>
								<div>The below leave(s) has been cancelled.</div>
								<div>
                <table width="100%" cellspacing="0" cellpadding="15" border="0" style="border:3px solid #BBBBBB; font-size:16px; font-family:Arial, Helvetica, sans-serif; margin:30px 0 30px 0;" bgcolor="#ffffff">
                      <tbody><tr>
                        <td width="28%" style="border-right:2px solid #BBBBBB;">Employee Name</td>
                        <td width="72%">'.$loginUserName.'</td>
                      </tr>
                      <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">No. of Day(s)</td>
                        <td>'.$appliedleavesdaycount.'</td>
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
								/* End */
								
								/* Mail to Reporting Manager */
								$options['subject'] = 'Leave request cancelled';
								$options['header'] = 'Leave Request';
								$options['toEmail'] = $reportingmanageremail;
								$options['toName'] = $reportingmanagername;
								$options['message'] = '<div>Hi,</div>
								<div>The below leave(s) has been cancelled.</div>
								<div>
                <table width="100%" cellspacing="0" cellpadding="15" border="0" style="border:3px solid #BBBBBB; font-size:16px; font-family:Arial, Helvetica, sans-serif; margin:30px 0 30px 0;" bgcolor="#ffffff">
                      <tbody><tr>
                        <td width="28%" style="border-right:2px solid #BBBBBB;">Employee Name</td>
                        <td width="72%">'.$loginUserName.'</td>
                      </tr>
                      <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">No. of Day(s)</td>
                        <td>'.$appliedleavesdaycount.'</td>
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
								/* End */
								
								/* Mail to HR */
								if (defined('LV_HR_'.$businessunitid) && $businessunitid !='')
								{
								
								$options['subject'] = 'Leave request cancelled';
								$options['header'] = 'Leave Request';
								$options['toEmail'] = constant('LV_HR_'.$businessunitid);
								$options['toName'] = 'Leave management';
								$options['message'] = '<div>Hi,</div>
								<div>The below leave(s) has been cancelled by the Employee.</div>
								<div>
                <table width="100%" cellspacing="0" cellpadding="15" border="0" style="border:3px solid #BBBBBB; font-size:16px; font-family:Arial, Helvetica, sans-serif; margin:30px 0 30px 0;" bgcolor="#ffffff">
                      <tbody><tr>
                        <td width="28%" style="border-right:2px solid #BBBBBB;">Employee Name</td>
                        <td width="72%">'.$loginUserName.'</td>
                      </tr>
                      <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">No. of Day(s)</td>
                        <td>'.$appliedleavesdaycount.'</td>
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
								$options['cron'] = 'yes';
								$result = sapp_Global::_sendEmail($options);
								}
											
					$messages['message'] = 'Leave request cancelled succesfully';  
					$messages['msgtype'] = 'success';				   
				}   
				else
				{
                   $messages['message'] = 'Leave request cannot be cancelled';	
					$messages['msgtype'] = 'error';				   
				}
			}
			else
			{ 
			 $messages['message'] = 'Leave request cannot be cancelled';
			 $messages['msgtype'] = 'error';
			}
			$this->_helper->json($messages);
		
	}
}
