
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

class Default_PendingoncallsController extends Zend_Controller_Action
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
			$oncallsArray = array('pendingoncalls','canceloncalls','approvedoncalls','rejectedoncalls');
			$objName = 'pendingoncalls';
			$queryflag = 'pending';
			if(!empty($flag)){
				if(in_array($flag, $oncallsArray)) {
					//$objName = ($flag=='approvedoncalls')?'pendingoncalls':$flag;
					$queryflag = substr($flag,0,-7);
				}
				if($flag=='all') {
					//$objName = 'pendingoncalls';
					$queryflag= 'all';
				}
			}
			$dataTmp = $oncallrequestmodel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall,$objName,$queryflag);     		
			
	        $oncallsCountArray = sapp_Helper::getOncallsCountByCategory($loginUserId);
			
			array_push($data,$dataTmp);
			$this->view->dataArray = $data;
			$this->view->call = $call ;
			$this->view->objName = $objName ;
			$this->view->flag = $flag ;
			$this->view->oncallsCountArray = $oncallsCountArray ;
			$this->view->messages = $this->_helper->flashMessenger->getMessages();
		}
    }
	
    public function viewAction()
	{	
	    $auth = Zend_Auth::getInstance();
     		if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					
			}
		$oncallrequestmodel = new Default_Model_Oncallrequest();	
		$id = $this->getRequest()->getParam('id');
		try
		{
			$useridArr = $oncallrequestmodel->getUserID($id);
		  
			if(!empty($useridArr))
			{
			  $user_id = $useridArr[0]['user_id'];
					if($user_id == $loginUserId)
					{
					$callval = $this->getRequest()->getParam('call');
					if($callval == 'ajaxcall')
						$this->_helper->layout->disableLayout();
					$objName = 'pendingoncalls';
					$oncallrequestform = new Default_Form_oncallrequest();
					$oncallrequestform->removeElement("submit");
					$elements = $oncallrequestform->getElements();
					if(count($elements)>0)
					{
						foreach($elements as $key=>$element)
						{
							if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
							$element->setAttrib("disabled", "disabled");
								}
						}
					}
						$data = $oncallrequestmodel->getsinglePendingOncallsData($id);
						$data = $data[0];
						//echo"<pre>";print_r($data);exit;
						//if(!empty($data) && $data['oncallstatus'] == 'Pending for approval')
						if(!empty($data))
							{
								$employeeoncalltypemodel = new Default_Model_Employeeoncalltypes();
								$usersmodel = new Default_Model_Users();
										
								$employeeoncalltypeArr = $employeeoncalltypemodel->getsingleEmployeeOncalltypeData($data['oncalltypeid']);
								if($employeeoncalltypeArr != 'norows')
								{
									$oncallrequestform->oncalltypeid->addMultiOption($employeeoncalltypeArr[0]['id'],$employeeoncalltypeArr[0]['oncalltype']);
									$data['oncalltypeid']=$employeeoncalltypeArr[0]['oncalltype'];
								
								}
								else
					            {
								   $data['oncalltypeid'] ="...";
						        } 	   
								
								if($data['oncallday'] == 1)
								{
								  $oncallrequestform->oncallday->addMultiOption($data['oncallday'],'Full Day');
									$data['oncallday']=	'Full Day';   
								}
								else 
								{
								  $oncallrequestform->oncallday->addMultiOption($data['oncallday'],'Half Day');
								  $data['oncallday']='Half Day'; 
								}					
							   
								$repmngrnameArr = $usersmodel->getUserDetailsByID($data['rep_mang_id'],'all');	
								$oncallrequestform->populate($data);
								
								
								$from_date = sapp_Global::change_date($data["from_date"], 'view');
								$to_date = sapp_Global::change_date($data["to_date"], 'view');
								$appliedon = sapp_Global::change_date($data["createddate"], 'view');
								
								$oncallrequestform->from_date->setValue($from_date);
								$oncallrequestform->to_date->setValue($to_date);
								$oncallrequestform->createddate->setValue($appliedon);
								$oncallrequestform->appliedoncallsdaycount->setValue($data['appliedoncallscount']);
								if(!empty($repmngrnameArr)){
								 $oncallrequestform->rep_mang_id->setValue($repmngrnameArr[0]['userfullname']);
								  $data['rep_mang_id']=$repmngrnameArr[0]['userfullname'];
								}
								else {
								  $oncallrequestform->rep_mang_id->setValue('');
								  $data['rep_mang_id']=$repmngrnameArr[0]['userfullname'];
								}
								
							  //to show Oncall Request history in view
								 $oncallrequesthistory_model = new Default_Model_Oncallrequesthistory();
								 $oncall_history = $oncallrequesthistory_model->getOncallRequestHistory($id);
							  	 $this->view->oncall_history = $oncall_history;
							  //end
								$oncallrequestform->setDefault('oncalltypeid',$data['oncalltypeid']);
								$oncallrequestform->setDefault('oncallday',$data['oncallday']);
								$this->view->controllername = $objName;
								$this->view->id = $id;
								$this->view->form = $oncallrequestform;
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
		 $oncalltypetext = '';
		    if($id)
			{
			$oncallrequestmodel = new Default_Model_Oncallrequest();
			$usersmodel = new Default_Model_Users();
			$employeesmodel = new Default_Model_Employees();
			$employeeoncalltypesmodel = new Default_Model_Employeeoncalltypes();
			
			$data = $oncallrequestmodel->getsinglePendingOncallsData($id);
			$data = $data[0];
			$oncalltypeArr = $employeeoncalltypesmodel->getOncalltypeDataByID($data['oncalltypeid']);
			if($data['oncallstatus']=='Approved') {
				if(isset($data['from_date'])) {
					$oncallDate = date($data['from_date']);
					$todayDate = date("Y-m-d");
					if(strtotime($todayDate)>=strtotime($oncallDate)) {
						$messages['message'] = 'On call request cannot be cancelled';  
						$messages['msgtype'] = 'error';
						$this->_helper->json($messages);
						return false;
					}
				}
			}
			if($data['oncallstatus']=='Rejected' || $data['oncallstatus']=='Cancel' || $loginUserId!=$data['user_id']) {
				$messages['message'] = 'On call request cannot be cancelled';  
				$messages['msgtype'] = 'error';
				$this->_helper->json($messages);
				return false;
			}
			
			$loggedInEmployeeDetails = $employeesmodel->getLoggedInEmployeeDetails($loginUserId);
				 if($loggedInEmployeeDetails[0]['businessunit_id'] != '')
					$businessunitid = $loggedInEmployeeDetails[0]['businessunit_id'];
								
			  $dataarr = array('oncallstatus'=>4,'modifieddate'=>gmdate("Y-m-d H:i:s"),'modifiedby'=>$loginUserId);
			  $where = array('id=?'=>$id);
			   
			  $Id = $oncallrequestmodel->SaveorUpdateOncallRequest($dataarr, $where);
			 if($data['oncallstatus']=='Approved') {
			  if(!empty($oncalltypeArr)) {
					if($oncalltypeArr[0]['oncallpredeductable'] == 1) {		
						$updateemployeeoncall = $oncallrequestmodel->updatecancelledemployeeoncalls($data['appliedoncallscount'],$data['user_id']);
					}
				}
			 }
				//saving in oncallrequest history table
			    $history = 'On Call Request has been Cancelled by ';
				
				 $oncallrequesthistory_model = new Default_Model_Oncallrequesthistory();
				 $oncall_history = array(											
								'oncallrequest_id' =>$id,
								'description' => $history,
								//'emp_name' =>  ucfirst($auth->getStorage()->read()->userfullname),
								'createdby' => $loginUserId,
								'modifiedby' =>$loginUserId,
								'isactive' => 1,
								'createddate' =>gmdate("Y-m-d H:i:s"),
								'modifieddate'=> gmdate("Y-m-d H:i:s"),
							);
				  $where = '';
				$oncallhistory = $oncallrequesthistory_model->saveOrUpdateOncallRequestHistory($oncall_history,$where); 
			  //end
			  //$data = $oncallrequestmodel->getsinglePendingOncallsData($id);
			  //$data = $data[0];
			  $appliedoncallsdaycount = $data['appliedoncallscount'];
			  $to_date = $data['to_date'];			  
			  $from_date = $data['from_date'];
			  $reason = $data['reason'];
			  $oncalltypeid = $data['oncalltypeid'];
			  $repmngrnameArr = $usersmodel->getUserDetailsByID($data['rep_mang_id']);
			  $reportingmanageremail = $repmngrnameArr[0]['emailaddress'];	
              $reportingmanagername	= $repmngrnameArr[0]['userfullname'];		  
			    if($Id == 'update')
				{
				   $menuID = PENDINGONCALLS;
				   $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id); 
				    /** MAILING CODE **/
					
					if($to_date == '' || $to_date == NULL)
				      $to_date = $from_date;
							/* Mail to Employee */
								$options['subject'] = 'On call request cancelled';
								$options['header'] = 'On Call Request';
								$options['toEmail'] = $loginUserEmail;	
								$options['toName'] = $loginUserName;
								$options['message'] = '<div>Hi,</div>
								<div>The below on call has been cancelled.</div>
								<div>
                <table width="100%" cellspacing="0" cellpadding="15" border="0" style="border:3px solid #BBBBBB; font-size:16px; font-family:Arial, Helvetica, sans-serif; margin:30px 0 30px 0;" bgcolor="#ffffff">
                      <tbody><tr>
                        <td width="28%" style="border-right:2px solid #BBBBBB;">Employee Name</td>
                        <td width="72%">'.$loginUserName.'</td>
                      </tr>
                      <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">No. of Day(s)</td>
                        <td>'.$appliedoncallsdaycount.'</td>
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
                        <td style="border-right:2px solid #BBBBBB;">Reason for Oncall</td>
                        <td>'.$reason.'</td>
                  </tr>
                </tbody></table>

            </div>
            <div style="padding:20px 0 10px 0;">Please <a href="'.BASE_URL.'/index/popup" target="_blank" style="color:#b3512f;">click here</a> to login and check the oncall details.</div>';	
								$result = sapp_Global::_sendEmail($options);
								/* End */
								
								/* Mail to Reporting Manager */
								$options['subject'] = 'On call request cancelled';
								$options['header'] = 'On Call Request';
								$options['toEmail'] = $reportingmanageremail;
								$options['toName'] = $reportingmanagername;
								$options['message'] = '<div>Hi,</div>
								<div>The below on call has been cancelled.</div>
								<div>
                <table width="100%" cellspacing="0" cellpadding="15" border="0" style="border:3px solid #BBBBBB; font-size:16px; font-family:Arial, Helvetica, sans-serif; margin:30px 0 30px 0;" bgcolor="#ffffff">
                      <tbody><tr>
                        <td width="28%" style="border-right:2px solid #BBBBBB;">Employee Name</td>
                        <td width="72%">'.$loginUserName.'</td>
                      </tr>
                      <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">No. of Day(s)</td>
                        <td>'.$appliedoncallsdaycount.'</td>
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
                        <td style="border-right:2px solid #BBBBBB;">Reason for Oncall</td>
                        <td>'.$reason.'</td>
                  </tr>
                </tbody></table>

            </div>
            <div style="padding:20px 0 10px 0;">Please <a href="'.BASE_URL.'/index/popup" target="_blank" style="color:#b3512f;">click here</a> to login and check the oncall details.</div>';	
								$result = sapp_Global::_sendEmail($options);
								/* End */
								
								/* Mail to HR */
								if (defined('LV_HR_'.$businessunitid) && $businessunitid !='')
								{
								
								$options['subject'] = 'On call request cancelled';
								$options['header'] = 'On Call Request';
								$options['toEmail'] = constant('LV_HR_'.$businessunitid);
								$options['toName'] = 'On call management';
								$options['message'] = '<div>Hi,</div>
								<div>The below on call has been cancelled by the Employee.</div>
								<div>
                <table width="100%" cellspacing="0" cellpadding="15" border="0" style="border:3px solid #BBBBBB; font-size:16px; font-family:Arial, Helvetica, sans-serif; margin:30px 0 30px 0;" bgcolor="#ffffff">
                      <tbody><tr>
                        <td width="28%" style="border-right:2px solid #BBBBBB;">Employee Name</td>
                        <td width="72%">'.$loginUserName.'</td>
                      </tr>
                      <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">No. of Day(s)</td>
                        <td>'.$appliedoncallsdaycount.'</td>
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
                        <td style="border-right:2px solid #BBBBBB;">Reason for Oncall</td>
                        <td>'.$reason.'</td>
                  </tr>
                </tbody></table>

            </div>
            <div style="padding:20px 0 10px 0;">Please <a href="'.BASE_URL.'/index/popup" target="_blank" style="color:#b3512f;">click here</a> to login and check the oncall details.</div>';	
								$options['cron'] = 'yes';
								$result = sapp_Global::_sendEmail($options);
								}
											
					$messages['message'] = 'On call request cancelled succesfully';  
					$messages['msgtype'] = 'success';				   
				}   
				else
				{
                   $messages['message'] = 'On call request cannot be cancelled';	
					$messages['msgtype'] = 'error';				   
				}
			}
			else
			{ 
			 $messages['message'] = 'On call request cannot be cancelled';
			 $messages['msgtype'] = 'error';
			}
			$this->_helper->json($messages);
		
	}
}
