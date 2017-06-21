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

class Default_EmpscreeningController extends Zend_Controller_Action
{

    private $options;
	public function preDispatch()
	{
		
	}
	
    public function init()
    {
		$orgInfoModel = new Default_Model_Organisationinfo();	
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();		
    }
	
	public function indexAction()
	{
		$empscreeningModel = new Default_Model_Empscreening();	
		 $call = $this->_getParam('call');
		if($call == 'ajaxcall')
				$this->_helper->layout->disableLayout();
		
		$view = Zend_Layout::getMvcInstance()->getView();		
		$objname = $this->_getParam('objname');
		$refresh = $this->_getParam('refresh');
		$data = array();
		$searchQuery = '';
		$searchArray = array();
		$tablecontent = '';
		$dashboardcall = $this->_getParam('dashboardcall');
		$statusidstring = $this->_request->getParam('status');
		$unitId = '';
		if(!isset($statusidstring) || $statusidstring=='')
		{
			$unitId = $this->_request->getParam('unitId');
			$statusidstring = $unitId;
		}
		$formgrid = 'true';
		if(isset($unitId) && $unitId != '') $formgrid = 'true';
		$statusid =  sapp_Global::_decrypt($statusidstring);
        if($statusid != '1' && $statusid != '2')
		{
			$statusidstring = sapp_Global::_encrypt(1);
		}	
		$queryflag = '';
		unset($_SESSION['emp_status']);
		if($statusid !='')
		{
			$_SESSION['emp_status'] = $statusidstring;
			if($statusid == '1'){
				$queryflag = '1';
				$this->view->ermsg = '';
			}else if($statusid == '2'){
				$queryflag = '2';		
				$this->view->ermsg = '';
			}else {
				$this->view->ermsg = 'nodata';
				$queryflag = '1';
			}
        }else $queryflag = '1';
		
		
		if($refresh == 'refresh')
		{
			if($dashboardcall == 'Yes')
				$perPage = DASHBOARD_PERPAGE;
			else	
				$perPage = PERPAGE;
			$sort = 'DESC';$by = 'me.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else 
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'me.modifieddate';
			if($dashboardcall == 'Yes')
				$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
			else $perPage = $this->_getParam('per_page',PERPAGE);
			$pageNo = $this->_getParam('page', 1);
			$searchData = $this->_getParam('searchData');	
			$searchData = rtrim($searchData,',');						
			$searchData = $this->_getParam('searchData');				
		}
		$dataTmp = $empscreeningModel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall, $queryflag,$statusidstring,$formgrid,$unitId);
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->statusidstring = $statusidstring;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
	}
	
	public function addAction()
	{
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$msgarray = array();$errorflag = 'true';
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		$checktypeModal = new Default_Model_Bgscreeningtype();
		$agencymodel = new Default_Model_Agencylist();
                $emp_check = 'yes';
	    $typesData = $checktypeModal->fetchAll('isactive=1','type')->toArray();		
		$agencyData = $agencymodel->fetchAll('isactive=1','agencyname')->toArray();
		$empscreeningModel = new Default_Model_Empscreening();
		$empscreeningform = new Default_Form_empscreening();
		$empscreeningform->setAttrib('action',BASE_URL.'empscreening/add');
			/* Add multi-options to the employee dropdown */
			$employeeData = $empscreeningModel->getEmployeesForScreening();
			$candidateData = $empscreeningModel->getCandidatesForScreening();
			$specimenArr = array();$empArr = array();$candArr = array();
	    	foreach ($employeeData as $eData){								
				$empArr[] = array(
						'id' =>$eData['id'],
						'name' => $eData['name'].', '.$eData['jobtitle'],
						'profileimg' => $eData['profileimg']
					);
	    	}					
			foreach ($candidateData as $cData){				
				$candArr[] = array(
						'id' =>$cData['id'],
						'name' => $cData['name'],
						'profileimg' => ''
					);
	    	}		
			if(sapp_Global::_isactivemodule(RESOURCEREQUISITION))
			{	
				$specimenArr = array_merge($empArr,$candArr);		
			}else{
				$specimenArr = $empArr;		
			}
			$specimenArr = sapp_Global::aasort($specimenArr,'name');			
			$this->view->specimenArr = $specimenArr;
			/* */
			
			$candid = $this->getRequest()->getParam('candid');
			$empid = $this->getRequest()->getParam('empid');			
			$personalData = '';$addressData = '';$companyData = '';
			if(isset($candid) && $candid != '')
			{			
				$empscreeningform->setAttrib('action',BASE_URL.'empscreening/add/candid/'.$candid);
				$empscreeningform->setDefault('employee','cand-'.$candid);
				$personalData = $empscreeningModel->getEmpPersonalData($candid,2);
				$addressData = $empscreeningModel->getEmpAddressData($candid,2);
				$companyData = $empscreeningModel->getEmpCompanyData($candid,2);
				$empscreeningform->removeElement('employee');
                                $emp_check = 'no';
			}elseif(isset($empid) && $empid != ''){
				$empscreeningform->setAttrib('action',BASE_URL.'empscreening/add/empid/'.$empid);
				$empscreeningform->setDefault('employee','emp-'.$empid);
				$personalData = $empscreeningModel->getEmpPersonalData($empid,1);
				$addressData = $empscreeningModel->getEmpAddressData($empid,1);
				$companyData = $empscreeningModel->getEmpCompanyData($empid,1);
				$empscreeningform->removeElement('employee');
                                $emp_check = 'no';
			}	
			if(sapp_Global::_isactivemodule(RESOURCEREQUISITION))
			{
				if(!empty($personalData))
				{			
					if(($personalData[0]['backgroundchk_status'] != 'Yet to start' && $personalData[0]['backgroundchk_status'] != 'Not Applicable')) 
					{  
						$this->view->bgstatus = 'notapplicable'; 
					}else $this->view->bgstatus = '';
				}
				else
				{
					if((isset($empid) && $empid != '') || isset($candid) && $candid != '')
					$this->view->bgstatus = 'no data';
					else
					$this->view->bgstatus = '';
				}
			}else{
				$this->view->bgstatus = 'no data';
			}
			$hrEmail = 'false';$mngmntEmail = 'false';	
			if(!empty($personalData))
			{	
				if(isset($personalData[0]['businessid']))
				{
					if (defined('BG_CHECKS_HR_'.$personalData[0]['businessid'])) {
						$hrEmail = constant('BG_CHECKS_HR_'.$personalData[0]['businessid']);
					}
					if (defined('BG_CHECKS_MNGMNT_'.$personalData[0]['businessid'])) {
						$mngmntEmail = constant('BG_CHECKS_MNGMNT_'.$personalData[0]['businessid']);
					}	
				}
			}
			$this->view->hrEmail = $hrEmail;
			$this->view->mngmntEmail = $mngmntEmail;
			/*  Check for HR and Management emails END */
			$this->view->personalData = $personalData;
			$this->view->addressData = $addressData;
			$this->view->companyData = $companyData;
			$this->view->candid = $candid;
			$this->view->empid = $empid;
			$empscreeningform->removeElement("bgcheck_status");
			$this->view->form = $empscreeningform;			
		if(!empty($typesData) && !empty($agencyData))
		{
			$this->view->configure = '';
		}else{
			$this->view->configure = 'notconfigured';
		}
		if(empty($typesData))
		{
			$msgarray['checktype'] = 'Screening types are not configured yet.';
			$errorflag = 'false';
		}		
		if(empty($candidateData) && empty($employeeData))
		{
			$msgarray['employee'] = 'Employees/candidates are not added yet.';
			$errorflag = 'false';
		}
                if($emp_check == 'yes')
                {
                if(count($empscreeningform->employee->getMultiOptions()) == 1 && $emp_check == 'yes')//like only one option "select employee/candidate"
		{
			$msgarray['employee'] = 'Employees/candidates are not added yet.';
			$errorflag = 'false';
		}
                }
		if(empty($agencyData))
		{
			$msgarray['agencyids'] = 'Agencies are not added yet.';
			$errorflag = 'false';
		}
		$this->view->msgarray = $msgarray;
		$agencyids = '';
		if($this->getRequest()->getPost())
		{
		    $result = $this->save($empscreeningform);	
			$agencyids = $this->_request->getParam('agencyids');
			$this->view->msgarray = $result; 
			$this->view->messages = $result;	
			$this->view->agencyids = $agencyids;
			$this->view->contactRadio = $this->_request->getParam('contactRadio');
        }
	}
	
	public function save($empscreeningform)
	{
		
        $baseUrl = BASE_URL;
		$baseUrl = rtrim($baseUrl,'/');		
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserRole = $auth->getStorage()->read()->emprole;
		}		
		$agencyEmail = '';$lmanager1Email = '';$lmanager1Name = 'Manager';$agencysalutationName = 'Agency';
		$empscreeningmodel = new Default_Model_Empscreening();
		$usermodel = new Default_Model_Users();
		$processesmodel = new Default_Model_Processes();
		$errorflag = "true";
		$id = $this->_request->getParam('id');	
		$checktype = $this->_request->getParam('checktype');
		$radio_pocId = $this->_request->getParam('contactRadio');
		$agencyids = $this->_request->getParam('agencyids');
		$bgcheck_status = $this->_request->getParam('bgcheck_status');
		$employee = $this->_request->getParam('employee'); 
		$candid = $this->_request->getParam('candid'); 
		$empid = $this->getRequest()->getParam('empid');
		$specimenId = '';$empFlag = 1;$bgtypes = '';$BGStatus = '';$newStatus = 'In process';$inprocess = 'no';
		$options = array();
		if($employee)
		{
			$empArr = explode('-',$employee);
			$empFlag = ($empArr[0] == 'emp') ? 1 : 2;
			$specimenId = $empArr[1];
		}
		if(isset($candid))
		{
			$empFlag = 2;
			$empArr[0] = 2;
			$specimenId = $candid;
		}else if(isset($empid)){
			$empFlag = 1;
			$empArr[0] = 1;
			$specimenId = $empid;
		}
		if(empty($checktype))
		{
			$msgarray['checktype'] = "Please check at least one screening type.";
			$errorflag = "false"; 
		}
		if(empty($radio_pocId) || $radio_pocId == '')
		{
			$msgarray['contactRadio'] = "Please select point of contact.";
			$errorflag = "false"; 
		}
		if(empty($employee))
		{
			$msgarray['employee'] = "Please select employee.";
			$errorflag = "false"; 
		}
		if(!isset($agencyids) || $agencyids == '') 
		{
			$agencyids = $this->getRequest()->getParam('defaultagencyid');			
		}
		if(!isset($agencyids) || $agencyids == '')
		{
			$msgarray['agencyids'] = "Please select agency.";
			$errorflag = "false"; 
		}
		$checktypeModal = new Default_Model_Bgscreeningtype();
		$agencymodel = new Default_Model_Agencylist();
	    $checktypesData = $checktypeModal->fetchAll('isactive=1','type')->toArray();		
		$checkagencyData = $agencymodel->fetchAll('isactive=1','agencyname')->toArray();
		$employeeData = $empscreeningmodel->getEmployeesForScreening();
		$candidateData = $empscreeningmodel->getCandidatesForScreening();
		if(empty($checktypesData))
		{
			$msgarray['checktype'] = 'Screening types are not configured yet.';
			$errorflag = 'false';
		}
		if(empty($checkagencyData))
		{
			$msgarray['agencyids'] = 'Agencies are not added yet.';
			$errorflag = 'false';
		}
		if(empty($candidateData) && empty($employeeData))
		{
			$msgarray['employee'] = 'Employees/candidates are not added yet.';
			$errorflag = 'false';
		}
		/* Check - Adding a record with same userid, agencyid and check type (for each check type ) */
		if(is_array($checktype) && $errorflag != "false")
		{
			for($i=0;$i<sizeof($checktype);$i++)
			{
				$processStatus = array();
				$processStatus = $processesmodel->getProcessStatus($specimenId,$empFlag,$agencyids,$checktype[$i]);		
				if(!empty($processStatus) && sizeof($processStatus > 0))
				{
					$oldstatus = $processStatus[0]['process_status'];
					$BGStatus = $processStatus[0]['bgcheck_status'];	
					if(($oldstatus == 'On hold' || $oldstatus == 'In process') && $newStatus == $oldstatus && $BGStatus != 'Complete')
					{
						$msgarray['StatusError'] = "Already a record with the given data exists. Please insert a new record";
						$errorflag = "false"; 							
					}
				}
			}
		}
		/* END */
		$agencyData = array();	$agencyPOCData = array();	$empData = array();$personalData = array(); $addressData = array(); $companyData = array();
		$empscreeningModel = new Default_Model_Empscreening();	
		if(!empty($checktype))
		{
			$agencyArr = array();
			$agencyArr = $checktype;
			$agencyData = $empscreeningModel->getAgencyData($agencyArr,'','');			
		}	

		if($agencyids)
			$agencyPOCData = $empscreeningModel->getAgencyPOCData($agencyids);		
		if(isset($specimenId) && isset($empArr[0]))
		{
			$personalData = $empscreeningModel->getEmpPersonalData($specimenId,$empArr[0]);
			$addressData = $empscreeningModel->getEmpAddressData($specimenId,$empArr[0]);
			$companyData = $empscreeningModel->getEmpCompanyData($specimenId,$empArr[0]);
		}
		if(isset($id) && $id!='')
		{			
			$idArr = array();
			$idArr = explode('-',$id);
			$specimenid = $idArr[0];$userflag = $idArr[1]; 
			$personalData = $empscreeningModel->getEmpPersonalData($specimenid,$userflag);
			$addressData = $empscreeningModel->getEmpAddressData($specimenid,$userflag);
			$companyData = $empscreeningModel->getEmpCompanyData($specimenid,$userflag);
			
			if(isset($personalData[0]['ustatus']))
			$processData = $this->processesGrid($id,$personalData[0]['ustatus']);
			else
			$processData = $this->processesGrid($id,'');
			$this->view->dataArray = $processData;
		}
		$hrEmail = 'false';$mngmntEmail = 'false';
		if(isset($personalData[0]['businessid']))
		{
			if (defined('BG_CHECKS_HR_'.$personalData[0]['businessid'])) {
				$hrEmail = constant('BG_CHECKS_HR_'.$personalData[0]['businessid']);
			}
			if (defined('BG_CHECKS_MNGMNT_'.$personalData[0]['businessid'])) {
				$mngmntEmail = constant('BG_CHECKS_MNGMNT_'.$personalData[0]['businessid']);
			}	
		}
		$this->view->hrEmail = $hrEmail;
		$this->view->mngmntEmail = $mngmntEmail;
		$this->view->personalData = $personalData;
		$this->view->addressData = $addressData;
		$this->view->companyData = $companyData;
		$this->view->agencyData = $agencyData;
		$this->view->agencyPOCData = $agencyPOCData;
		if($id != '' )
		{
			$errorflag = 'true';
			if($bgcheck_status == '0')
			{
				$msgarray['bgcheck_status'] = "Please select status";
				$errorflag = "false"; 
			}			
		}
		if($empscreeningform->isValid($this->_request->getPost()) && $errorflag != 'false')
		{	
			$date = new Zend_Date();
			$actionflag = '';
			$tableid  = '';	
			if($id == '')
			{
				/*	A New process is created for a user whose background check status is in 'Complete' status. Then updating the bg status to 'In process' */
				if($BGStatus == 'Complete' && ($newStatus == 'In process'))
				{
					$totalstatusData = array(
							'bgcheck_status'	=>		'In process',
							'modifiedby'		=>		$loginUserId,
							'modifieddate'		=>		gmdate("Y-m-d H:i:s")
						);
					$totalstatusWhere = array(
							'specimen_id=?'		=>		$specimenId,
							'flag=?'			=>		$empFlag
						);
					$empscreeningModel->SaveorUpdateDetails($totalstatusData, $totalstatusWhere);					
				}	
				/* END */
			
				$data = array(  
						'specimen_id'		=>		$specimenId,
						'flag'				=>		$empFlag,
						'bgagency_id'		=>		$agencyids,
						'bgagency_pocid'	=>		$radio_pocId,
						'modifiedby'		=>		$loginUserId,
						'modifieddate'		=>		gmdate("Y-m-d H:i:s")
				);		
				$data['process_status'] = $newStatus;
				$data['createdby'] = $loginUserId;
				$data['createddate'] = gmdate("Y-m-d H:i:s");
				$data['isactive'] = 1;
				$where = '';
				$actionflag = 1;
				
				if(is_array($checktype))
				{
					for($i=0;$i<sizeof($checktype);$i++)
					{
						$data['bgcheck_type']	=	$checktype[$i];
						$detailId = $empscreeningModel->SaveorUpdateDetails($data, $where);
					}
				}
				/* Updating back ground check status to In process in employees/candidates status */
				if($empFlag == 1){
					$empInsertionData = array(
							'backgroundchk_status' => 	'In process',
							'modifiedby'		=>		$loginUserId,
							'modifieddate'		=>		gmdate("Y-m-d H:i:s")
						);
					$empWhere = array('id=?'=>$specimenId);
					$usermodel->addOrUpdateUserModel($empInsertionData, $empWhere,$specimenId);
					$options['subject'] = APPLICATION_NAME.' :: Employee Screening';				
				}
				else {
					$candModel = new Default_Model_Candidatedetails();
					$candData = array(
							'backgroundchk_status' => 	'In process',
							'modifiedby'		=>		$loginUserId,
							'modifieddate'		=>		gmdate("Y-m-d H:i:s")
						);
					$candWhere = array('id=?'=>$specimenId);
					$candModel->SaveorUpdateUserData($candData, $candWhere);
					$options['subject'] = APPLICATION_NAME.' :: Candidate Screening';					
				}
				/* END */
				for($i=0;$i<sizeof($agencyPOCData);$i++)
				{
					if($agencyPOCData[$i]['id']  ==  $radio_pocId)
					{
						$agencyEmail = $agencyPOCData[$i]['email'];
						$agencyfname = $agencyPOCData[$i]['first_name'];
						$agencylname = $agencyPOCData[$i]['last_name'];
						$agencysalutationName = $agencyfname.' '.$agencylname;
					}
				}
				$hremail = explode(",",HREMAIL);
				if($empFlag == 1)
				{
					 $lmanager1Email = $personalData[0]['rmanager_email'];
					 $lmanager1Name = $personalData[0]['reporting_manager'];
				}
				
				$bid = '';
				if(isset($personalData[0]['businessid'])) 
					$bid = $personalData[0]['businessid'];

				if(isset($lmanager1Email) && $lmanager1Email != '')	
				{	
					$manager1 = array($lmanager1Email);
				}
				else $manager1 = array();
				
				if(isset($agencyEmail) && $agencyEmail != '')	
				{	
					$agencyemail1 = array($agencyEmail);
				}
				else $agencyemail1 = array();
				
				if (defined('BG_CHECKS_MNGMNT_'.$bid) && $bid !='')
				   $mngmntemailId = explode(",",constant('BG_CHECKS_MNGMNT_'.$bid));
				else   
				   $mngmntemailId = array();
				
				if (defined('BG_CHECKS_HR_'.$bid) && $bid !='')
				   $hremailId = explode(",",constant('BG_CHECKS_HR_'.$bid));
				else   
				   $hremailId = array();	   
				
				
				$emailArr =  array_merge($manager1,$agencyemail1,$mngmntemailId,$hremailId);
				for($i = 0; $i < sizeof($emailArr); $i++)
				{		
					$salutation = 'Dear Sir/Madam,';
					if($i == 0)
					{
						$salutation = 'Dear '.ucfirst($lmanager1Name).',';
						$options['toName'] = ucfirst($lmanager1Name);  	
					}
					else if($i == 1)
					{
						$salutation = 'Dear '.ucfirst($agencysalutationName).',';
						$options['toName'] = ucfirst($agencysalutationName);  	
					}
					else if($i == 2)
					{
						$salutation = 'Dear Management,';
						$options['toName'] = 'Management';  						
					}
					else if($i == 3)
					{
						$salutation = 'Dear HR,';
						$options['toName'] = 'HR';  
					}
					$createdbyName = $usermodel->getUserDetails($loginUserId);					
					$options['subject'] = APPLICATION_NAME.' : Background check initiated';	
					$options['header'] = 'Background check initiated';
					$options['toEmail'] = $emailArr[$i];  				
					$options['message'] = '<div>'.$salutation.'<div>'.ucfirst($personalData[0]['name']).' has been sent for background check by '.ucfirst($createdbyName[0]['userfullname']).'.';
					if(!empty($personalData[0]['employee_id'])){
							$options['message'] .= ' Please find the details below.</div>
										<div>
											<table border="1" style="border-collapse:collapse;">
												<tr><td>Employee ID</td><td>'.$personalData[0]['employee_id'].'</td></tr>
												<tr><td>Employee Email</td><td>'.$personalData[0]['email_id'].'</td></tr>
												<tr><td>Employee Designation</td><td>'.$personalData[0]['designation'].'</td></tr>
											</table>
										</div>';										
						
					}
					$options['message'] .= '<div style="padding:20px 0 10px 0;">Please <a href="'.$baseUrl.'/index/popup" target="_blank" style="color:#b3512f;">click here</a> to login and check the details.</div>
											</div>';	
					$options['cron'] = 'yes';
					$result = sapp_Global::_sendEmail($options);	
				}
				
			}
			if($id!='')
			{
				for($i=0;$i<sizeof($agencyPOCData);$i++)
				{
					if($agencyPOCData[$i]['id']  ==  $radio_pocId)
					{
						$agencyEmail = $agencyPOCData[$i]['email'];
						$agencyfname = $agencyPOCData[$i]['first_name'];
						$agencylname = $agencyPOCData[$i]['last_name'];
					}
				}
				$data = array(  						
						'modifiedby'		=>		$loginUserId,
						'modifieddate'		=>		gmdate("Y-m-d H:i:s")
				);		
				$empArr = explode('-',$id);
				$specimenId = $empArr[0];
				$empFlag = $empArr[1];
				$checkstatuses = $empscreeningModel->checkbgstatus($specimenId,$empFlag,'status');		
				if(!empty($checkstatuses) && $checkstatuses[0]['bgcheck_status'] != $bgcheck_status)
				{
					if($bgcheck_status == 'Complete')
					{
						$inprocess = 'no';
						for($i=0;$i<sizeof($checkstatuses);$i++)
						{
							if(($checkstatuses[$i]['process_status'] == 'In process' || $checkstatuses[$i]['process_status'] == 'On hold') && $checkstatuses[$i]['explanation'] == '' && $checkstatuses[$i]['bgcheck_status'] != 'Complete')
							{
								$inprocess = 'yes';
							}							
						}
						if($inprocess != 'yes')
						{
							$data['bgcheck_status'] = $bgcheck_status;
							$where = array('specimen_id=?'=>$specimenId,'flag=?'=>$empFlag,'isactive = 1');				 
							$actionflag = 2;
							$detailId = $empscreeningModel->SaveorUpdateDetails($data, $where);
							/* Updating back ground check status to Completed in employees/candidates status */
							if($empFlag == 1){
								$usermodel = new Default_Model_Users();
								$empinsertionData = array(
										'backgroundchk_status' => 	'Completed',
										'modifiedby'		=>		$loginUserId,
										'modifieddate'		=>		gmdate("Y-m-d H:i:s")
									);
								$empWhere = array('id=?'=>$specimenId);
								$usermodel->addOrUpdateUserModel($empinsertionData, $empWhere,$specimenId);
							}
							else {
								$candModel = new Default_Model_Candidatedetails();
								$candData = array(
										'backgroundchk_status' => 	'Completed',
										'modifiedby'		=>		$loginUserId,
										'modifieddate'		=>		gmdate("Y-m-d H:i:s")
									);
								$candWhere = array('id=?'=>$specimenId);
								$candModel->SaveorUpdateUserData($candData, $candWhere);
							}
							/* END */
							/* Mail to HRD, L1 and L2 managers*/
							$hremail = explode(",",HREMAIL);
							if($empFlag == 1)
							{
								 $lmanager1Email = $personalData[0]['rmanager_email'];
								 $lmanager1Name = $personalData[0]['reporting_manager'];
							}
							
							if(isset($lmanager1Email) && $lmanager1Email != '')	
							{	
								$manager1 = array($lmanager1Email);
							}
							else $manager1 = array();			
							
							
							if (defined('BG_CHECKS_MNGMNT_'.$bid) && $bid !='')
							   $mngmntemailId = explode(",",constant('BG_CHECKS_MNGMNT_'.$bid));
							else   
							   $mngmntemailId = array();
							
							if (defined('BG_CHECKS_HR_'.$bid) && $bid !='')
							   $hremailId = explode(",",constant('BG_CHECKS_HR_'.$bid));
							else   
							   $hremailId = array();	
							   
							$emailArr =  array_merge($manager1,$mngmntemailId,$hremailId);							
							for($i = 0;$i<sizeof($emailArr);$i++)
							{
								$salutation = 'Dear Sir/Madam,';
								if($i == 0)
								{
									$salutation = 'Dear '.ucfirst($lmanager1Name).',';
									$options['toName'] = ucfirst($lmanager1Name);  
								}
								if($i == 1)
								{
									$salutation = 'Dear Management,';
									$options['toName'] = 'Management';  
								}
								if($i == 2)
								{
									$salutation = 'Dear HR,';
									$options['toName'] = 'HR';  
								}
								$options['subject'] = APPLICATION_NAME.' : Background check completed';	
								$options['header'] = 'Background check completed';
								$options['toEmail'] = $emailArr[$i];  
								$options['message'] = '<div>'.$salutation.'<div>Background check for '.ucfirst($personalData[0]['name']).' has been completed.';
								if(!empty($personalData[0]['employee_id'])){
									$options['message'] .= ' Please find the details below.</div>
										<div>
											<table border="1" style="border-collapse:collapse;">
												<tr><td>Employee ID</td><td>'.$personalData[0]['employee_id'].'</td></tr>
												<tr><td>Employee Email</td><td>'.$personalData[0]['email_id'].'</td></tr>
												<tr><td>Employee Designation</td><td>'.$personalData[0]['designation'].'</td></tr>
											</table>
										</div>';										
						
								}															
								$options['message'] .= '<div style="padding:20px 0 10px 0;">Please <a href="'.$baseUrl.'/index/popup" target="_blank" style="color:#b3512f;">click here</a> to login and check the details.</div>
														</div>';
								$options['cron'] = 'yes';
								$result = sapp_Global::_sendEmail($options);	
							}
							/* END */
							
						}					
					}
					else
					{
						$data['bgcheck_status'] = $bgcheck_status;
						$where = array('specimen_id=?'=>$specimenId,'flag=?'=>$empFlag,'isactive = 1');				 
						$actionflag = 2;
						$detailId = $empscreeningModel->SaveorUpdateDetails($data, $where);
						
						if($empFlag == 1){
							$usermodel = new Default_Model_Users();
							$empInsertionData = array(
									'backgroundchk_status' => 	$bgcheck_status,
									'modifiedby'		=>		$loginUserId,
									'modifieddate'		=>		gmdate("Y-m-d H:i:s")
								);
							$empWhere = array('id=?'=>$specimenId);
							$usermodel->addOrUpdateUserModel($empInsertionData, $empWhere,$specimenId);
							$options['subject'] = APPLICATION_NAME.' :: Employee Screening';				
						}
						else {
							$candModel = new Default_Model_Candidatedetails();
							$candData = array(
									'backgroundchk_status' => 	$bgcheck_status,
									'modifiedby'		=>		$loginUserId,
									'modifieddate'		=>		gmdate("Y-m-d H:i:s")
								);
							$candWhere = array('id=?'=>$specimenId);
							$candModel->SaveorUpdateUserData($candData, $candWhere);
						}
						
					}
				}
				else
					$inprocess = 'samebgstatus';
			}			
			if($inprocess == 'yes' || $inprocess == 'samebgstatus')
			{
				$this->view->inprocess = $inprocess;
				$this->view->specimenId = $specimenId;
				$this->view->empFlag = $empFlag;
			}
			else
			{
				if($detailId == 'update')
					$tableid = $id;
				else
					$tableid = $detailId; 	
				$menuID = EMPSCREENING;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$specimenId.'-'.$empFlag);			
				
				if($detailId == 'update')
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Background check process is updated successfully."));
				else
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Background check process is added successfully."));					   
				
				if($empFlag == 2)
				$this->_redirect('empscreening/con/pQ==');
				else 
				$this->_redirect('empscreening/con/pA==');
			}
		}
		else
		{
			$messages = $empscreeningform->getMessages();
			foreach ($messages as $key => $val)
			{
				foreach($val as $key2 => $val2)
				{
					$msgarray[$key] = $val2;
					break;
				}
				if(empty($checktypesData))
				{
					$msgarray['checktype'] = 'Screening types are not configured yet.';
				}if(empty($checkagencyData)){
					$msgarray['agencyids'] = 'Agencies are not added yet.';
				}if(empty($candidateData) && empty($employeeData))
				{
					$msgarray['employee'] = 'Employees/candidates are not added yet.';
					$errorflag = 'false';
				}
			}
			return $msgarray;	
		}
		$this->view->lgnrole = $loginuserRole;
	}
	
	public function processesGrid($id,$empstatus='')
	{	
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		} 
		$dashboardcall = $this->_getParam('dashboardcall');
		if($dashboardcall == 'Yes')
				$perPage = DASHBOARD_PERPAGE;
			else	
				$perPage = PERPAGE;
		$empscreeningModel = new Default_Model_Processes();
		$sort = 'DESC';$by = 'b.modifieddate';$pageNo = 1;$searchData = '';$searchArray=array();
		$objName = 'processes';
		$tableFields = array('action'=>'Action','type' => 'Check Type','agencyname' =>'Agency Name','email'=>'POC Email','process_status'=>'Process Status','explanation'=>'Explanation','isactive'=>'Active Status','startdate'=> 'Started On','enddate'=>'Ended On', 'recentlycommenteddate'=>'Recently Commented On');
		$tablecontent = $empscreeningModel->getProcessesData($sort, $by, $pageNo, $perPage,'',$id,$loginUserId,$loginuserGroup);     
		$data = array();
		$dataTmp = array(
			'sort' => $sort,
			'by' => $by,
			'pageNo' => $pageNo,
			'perPage' => $perPage,				
			'tablecontent' => $tablecontent,
			'objectname' => $objName,
			'extra' => array(),
			'tableheader' => $tableFields,
			'jsGridFnName' => 'getAjaxgridData',
			'jsFillFnName' => '',
			'searchArray' => $searchArray,
			'formgrid' => 'true',
			'menuName' => 'Background check Process',
			'add' => 'add',
			'dashboardcall'=>$dashboardcall,
			'unitId' => $id,
			'empstatus'=>$empstatus,
			'search_filters' => array(
                    'isactive' => array('type'=>'select',
                         'filter_data'=>array(''=>'All',1 => 'Active','2'=>'Agency deleted','3'=>'Agency User deleted','4'=>'POC deleted',0 => 'Process deleted' )),
					'startdate' =>array('type'=>'datepicker'),
					'enddate' =>array('type'=>'datepicker'),
					'recentlycommenteddate' =>array('type'=>'datepicker')	
            )
		);		
		array_push($data,$dataTmp);
		return $data;
	}
	public function editAction()
	{		
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
		$idData = $this->getRequest()->getParam('id');
		/* Checking for configurations - screening type and agencies*/
		$msgarray = array(); $errorflag = 'true';
		$checktypeModal = new Default_Model_Bgscreeningtype();
		$agencymodel = new Default_Model_Agencylist();
	    $typesData = $checktypeModal->fetchAll('isactive=1','type')->toArray();		
		$agencyData = $agencymodel->fetchAll('isactive=1','agencyname')->toArray();
		
		if(empty($typesData))
		{
			$msgarray['checktype'] = 'Screening types are not configured yet.';
			$errorflag = 'false';
		}
		if(empty($agencyData))
		{
			$msgarray['agencyids'] = 'Agencies are not added yet.';
			$errorflag = 'false';
		}
		$this->view->msgarray = $msgarray;
		
		/* */
		$empscreeningform = new Default_Form_empscreening();
	    $empscreeningModel = new Default_Model_Empscreening();			
		$submitButon = $empscreeningform->getElement("submit");
		$submitButon->setAttrib('style', 'display:none;');	
		$idArr = array();
		$idArr = explode('-',$idData);
		if(sizeof($idArr)>1)
		{
			$id = intVal($idArr[0]);
			$userflag = intVal($idArr[1]); 
			$idData = $id.'-'.$userflag;
		}else{
			$id ='';$userflag = '';$idData = '';
		}	
		$errorpagedata = '';
		if($userflag == 2) $flag = 'cand'; else $flag = 'emp';
		if($userflag == 1 || ($userflag == 2 && sapp_Global::_isactivemodule(RESOURCEREQUISITION))) 
		{
			if($id && $id != $loginUserId)
			{				
				$data = $empscreeningModel->getsingleEmpscreeningData($id,$userflag);
				if(!empty($data) && $data != 'norows')
				{
					$empscreeningform->setAttrib('action',BASE_URL.'empscreening/edit/id/'.$idData);			
					$empscreeningform->removeElement("employee");
					$empscreeningform->removeElement("checktype");
					$empscreeningform->removeElement("checkagency");
					$empscreeningform->populate($data);			
					$specimenId = $data['specimen_id'];
					$hrEmail = 'false';$mngmntEmail = 'false';
					$empData = array();		$personalData = array(); 	$addressData = array();	$companyData = array();
					if(isset($specimenId) && isset($flag))
					{
						$personalData = $empscreeningModel->getEmpPersonalData($specimenId,$flag);
						$addressData = $empscreeningModel->getEmpAddressData($specimenId,$flag);
						$companyData = $empscreeningModel->getEmpCompanyData($specimenId,$flag);						
						if(isset($personalData[0]['businessid']))
						{
							if (defined('BG_CHECKS_HR_'.$personalData[0]['businessid'])) {
								$hrEmail = constant('BG_CHECKS_HR_'.$personalData[0]['businessid']);
							}
							if (defined('BG_CHECKS_MNGMNT_'.$personalData[0]['businessid'])) {
								$mngmntEmail = constant('BG_CHECKS_MNGMNT_'.$personalData[0]['businessid']);
							}	
						}						
					}
					$checkstatuses = $empscreeningModel->checkbgstatus($specimenId,$userflag,'status');	
					$inprocess = 'no';
					for($i=0;$i<sizeof($checkstatuses);$i++)
					{
						if(($checkstatuses[$i]['process_status'] == 'In process' || $checkstatuses[$i]['process_status'] == 'On hold') && $checkstatuses[$i]['explanation'] == '' && $checkstatuses[$i]['bgcheck_status'] != 'Complete')
						{
							$inprocess = 'yes';
						}							
					}
					$this->view->inprocessStatus = $inprocess;
					$this->view->personalData = $personalData;
					$this->view->addressData = $addressData;
					$this->view->companyData = $companyData;
					$this->view->hrEmail = $hrEmail;
					$this->view->mngmntEmail = $mngmntEmail;
					$this->view->errorpagedata = '';
				}else{
					$this->view->ermsg = 'nodata';
				}
			}	
			if($loginuserGroup != '' && $loginuserGroup != HR_GROUP && $loginuserGroup != MANAGEMENT_GROUP)
			{
				$elements = $empscreeningform->getElements();
				if(count($elements)>0)
				{
					foreach($elements as $key=>$element)
					{
						if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
						$element->setAttrib("disabled", "disabled");
							}
					}
				}
			}
			if(!empty($personalData))
			{
				if($personalData[0]['backgroundchk_status'] == 'Completed')
				$empscreeningform->setDefault('bgcheck_status','Complete');
				if($personalData[0]['backgroundchk_status'] == 'In process')
				$empscreeningform->setDefault('bgcheck_status','In process');
				if($personalData[0]['backgroundchk_status'] == 'On hold')
				$empscreeningform->setDefault('bgcheck_status','On hold');	
				$processData = $this->processesGrid($idData,$personalData[0]['ustatus']);
				$this->view->dataArray = $processData;
				$this->view->form = $empscreeningform;
				$this->view->ermsg = '';
			}else{
				$this->view->ermsg = 'nodata';
			}
			if($this->getRequest()->getPost())
			{
				$result = $this->save($empscreeningform);	
				$this->view->msgarray = $result; 
				$this->view->messages = $result;
			}
		}else {		
			$errorpagedata = 'nodata';
			$this->view->ermsg = $errorpagedata;
		}
	}
	public function viewAction()
	{
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$idData = $this->getRequest()->getParam('id');
		$objName='empscreening';
		$empscreeningform = new Default_Form_empscreening();
	    $empscreeningModel = new Default_Model_Empscreening();			
		$processData = array();	
		$idArr = array();
		$idArr = explode('-',$idData);
		if(sizeof($idArr)>1)
		{
			$id = intVal($idArr[0]);
			$userflag = intVal($idArr[1]); 
			$idData = $id.'-'.$userflag;
		}else{
			$id ='';$userflag = '';$idData = '';
		}		
		if($userflag == 2) $flag = 'cand'; else $flag = 'emp';
		if($userflag == 1 || ($userflag == 2 && sapp_Global::_isactivemodule(RESOURCEREQUISITION)))  
		{			
			if($id && $id != $loginUserId)
			{
				$data = $empscreeningModel->getsingleEmpscreeningData($id,$userflag);
				if(!empty($data) && $data != 'norows')
				{
					$empscreeningform->setAttrib('action',BASE_URL.'empscreening/edit/id/'.$idData);			
					$empscreeningform->removeElement("employee");
					$empscreeningform->removeElement("checktype");
					$empscreeningform->removeElement("checkagency");
					$empscreeningform->populate($data);	
					$elements = $empscreeningform->getElements();
					if(count($elements)>0)
					{
						foreach($elements as $key=>$element)
						{
							if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
							$element->setAttrib("disabled", "disabled");
								}
						}
					}
					$specimenId = $data['specimen_id'];
					$empData = array();			
					if(isset($specimenId) && isset($flag))
					{
						$personalData = $empscreeningModel->getEmpPersonalData($specimenId,$flag);
						$addressData = $empscreeningModel->getEmpAddressData($specimenId,$flag);
						$companyData = $empscreeningModel->getEmpCompanyData($specimenId,$flag);
					}
					$this->view->personalData = $personalData; 
					$this->view->addressData = $addressData;
					$this->view->companyData = $companyData;
					$this->view->controllername=$objName;
					$this->view->id = $idData;
					$this->view->ermsg = '';
					if($idData!='')
					$processData = $this->processesGrid($idData,$personalData[0]['ustatus']);
					$this->view->dataArray = $processData;
					$this->view->form = $empscreeningform;
				}else{					
					$this->view->ermsg = 'nodata';
				}
			}else{
				$this->view->ermsg = 'nodata';
			}			
		}else{
			$this->view->ermsg = 'nodata';
		}
	}
	public function getemployeedataAction()
	{
		$this->_helper->layout->disableLayout();
		$empid = $this->_request->getParam('empid');
		$empArr = explode('-',$empid);
		$con = $empArr[0]; $id = $empArr[1];
		$empscreeningform = new Default_Form_empscreening();
		$empscreeningModel = new Default_Model_Empscreening();	
		$personalData = $empscreeningModel->getEmpPersonalData($id,$con);
		$hrEmail = 'false';$mngmntEmail = 'false';
		if(isset($personalData[0]['businessid']))
		{
			if (defined('BG_CHECKS_HR_'.$personalData[0]['businessid'])) {
				$hrEmail = 'true';
			}
			if (defined('BG_CHECKS_MNGMNT_'.$personalData[0]['businessid'])) {
				$mngmntEmail = 'true';
			}	
		}		
		$addressData = $empscreeningModel->getEmpAddressData($id,$con);
		$companyData = $empscreeningModel->getEmpCompanyData($id,$con);		
		$this->view->personalData = $personalData;
		$this->view->addressData = $addressData;
		$this->view->companyData = $companyData;
		$this->view->hrEmail = $hrEmail;
		$this->view->mngmntEmail = $mngmntEmail;
	}
	
	public function getagencylistAction()
	{
		$limit = 1; $page = 1;
		$checktypes = $this->_request->getParam('checktypeid');
		$this->_helper->layout->disableLayout();
		$agencyArr = array();
		$agencyArr = explode(',',$checktypes);
		$empscreeningModel = new Default_Model_Empscreening();	
		$agencyData = array();$agencyPOCData = array();
		$agencyData = $empscreeningModel->getAgencyData($agencyArr,$limit,$page);
		if(!empty($agencyData))
		{
			$agencyid = $agencyData[0]['id'];
			$agencyPOCData = $empscreeningModel->getAgencyPOCData($agencyid);			
		}
		$this->view->agencyData = $agencyData;
		$this->view->agencyCount = sizeof($agencyData);
		$this->view->agencyPOCData = $agencyPOCData;
		$this->view->limit = $limit;
		$this->view->page = $page;
	}
	
	public function getpocdataAction()
	{
		$agencyid = $this->_request->getParam('agencyid');
		$this->_helper->layout->disableLayout();
		$empscreeningModel = new Default_Model_Empscreening();	
		$agencyPOCData = $empscreeningModel->getAgencyPOCData($agencyid);
		$this->view->agencyPOCData = $agencyPOCData;
	}
	
	public function forcedfullupdateAction()
	{
		$baseUrl = BASE_URL;
		$baseUrl = rtrim($baseUrl,'/');		
		
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		} 	
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$specimenId = $this->_request->getParam('specimenId');
		$empFlag = $this->_request->getParam('empFlag');
		$count = intval($this->_request->getParam('count'));
		$username = '';
		$empscreeningModel = new Default_Model_Empscreening();
		$getAllRecords = $empscreeningModel->checkbgstatus($specimenId,$empFlag,'completedata');
		$username = $getAllRecords[0]['username'];
		if(isset($getAllRecords[0]['rmanager_email'])) $reportmanagerEmail = $getAllRecords[0]['rmanager_email'];
		else $reportmanagerEmail ='';
		if(isset($getAllRecords[0]['reporting_manager']))$reportmanagername = $getAllRecords[0]['reporting_manager'];
		else $reportmanagername = '';
			
		if($count == '')
		{			
			$this->view->dataArray = $getAllRecords;
		}
		$close = '';
		if($count != '')
		{
			for($i = 0;$i < $count;$i++)
			{
				$id = intval($this->_request->getParam('id'.$i));				
				$explanation = $this->_request->getParam('text'.$i);
				$date = new Zend_Date();
				$data = array( 
						'process_status'	=>      'Complete',  
						'explanation'		=>		$explanation,
						'modifiedby'		=>		$loginUserId,
						'modifieddate'		=>		gmdate("Y-m-d H:i:s")
				);	
				$where = array('specimen_id=?'=>$specimenId,'flag=?'=>$empFlag,'process_status=?'=>'In process','id=?'=>$id);
				$detailId = $empscreeningModel->SaveorUpdateDetails($data, $where);
				$where = array('specimen_id=?'=>$specimenId,'flag=?'=>$empFlag,'process_status'=>'On hold','id=?'=>$id);
				$detailId = $empscreeningModel->SaveorUpdateDetails($data, $where);
				if($detailId == 'update')
					$tableid = $id;
				else
					$tableid = $detailId; 	
					
				$actionflag = 2;	
				$menuID = EMPSCREENING;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$specimenId.'-'.$empFlag);					
			}
			$bgdata = array(  
						'bgcheck_status'	=>		'Complete',
						'modifiedby'		=>		$loginUserId,
						'modifieddate'		=>		gmdate("Y-m-d H:i:s")
						
				);	
			$where = array('specimen_id=?'=>$specimenId,'flag=?'=>$empFlag,'isactive = 1');				 
			$actionflag = 2;
			$detailId = $empscreeningModel->SaveorUpdateDetails($bgdata, $where);
			/* Updating back ground check status to Completed in employees/candidates status */
			if($empFlag == 1){
			$usermodel = new Default_Model_Users();
			$empData = array(
					'backgroundchk_status' => 	'Completed',
					'modifiedby'		=>		$loginUserId,
					'modifieddate'		=>		gmdate("Y-m-d H:i:s")
				);
				
			$empWhere = array('id=?'=>$specimenId);
			$usermodel->addOrUpdateUserModel($empData, $empWhere,$specimenId);
			}
			else {
				$candModel = new Default_Model_Candidatedetails();
				$candData = array(
						'backgroundchk_status' => 	'Completed',
						'modifiedby'		=>		$loginUserId,
						'modifieddate'		=>		gmdate("Y-m-d H:i:s")
					);
				$candWhere = array('id=?'=>$specimenId);
				$candModel->SaveorUpdateUserData($candData, $candWhere);
			}
			/* END */	
			/* Mail to HRD, L1 and L2 managers*/
			$personalData = $empscreeningModel->getEmpPersonalData($specimenId,$empFlag);
			$bid = '';
			if(isset($personalData[0]['businessid'])) 
				$bid = $personalData[0]['businessid'];
			
			
			if(isset($reportmanagerEmail) && $reportmanagerEmail != '')	
			{	
				$manager1 = array($reportmanagerEmail);
			}
			else $manager1 = array();
			
			if (defined('BG_CHECKS_MNGMNT_'.$bid) && $bid !='')
			   $mngmntemailId = explode(",",constant('BG_CHECKS_MNGMNT_'.$bid));
			else   
			   $mngmntemailId = array();
			
			if (defined('BG_CHECKS_HR_'.$bid) && $bid !='')
			   $hremailId = explode(",",constant('BG_CHECKS_HR_'.$bid));
			else   
			   $hremailId = array();	
			
			$emailArr =  array_merge($manager1,$mngmntemailId,$hremailId);			
			for($i=0;$i<sizeof($emailArr);$i++)
			{
				$salutation = 'Dear Sir/Madam,';
				if($i == 0)
				{	
					$salutation = 'Dear '.ucfirst($reportmanagername).',';
					$options['toName'] = ucfirst($reportmanagername);
				}
				else if($i == 1)
				{
					$salutation = 'Dear Management,';
					$options['toName'] = 'Management';
				}
				else if($i == 2)
				{
					$salutation = 'Dear HR,';
					$options['toName'] = 'HR';
				}
				$options['subject'] = APPLICATION_NAME.' :: Background check';	
				$options['header'] = 'Background check completed';
				$options['toEmail'] = $emailArr[$i]; 	
				$options['message'] = '<div>'.$salutation.'<div>The background check for '.ucfirst($username).' has been completed.';
				if(!empty($personalData[0]['employee_id'])){
					$options['message'] .= ' Please find the details below.</div>
						<div>
							<table border="1" style="border-collapse:collapse;">
								<tr><td>Employee ID</td><td>'.$getAllRecords[0]['employee_id'].'</td></tr>
								<tr><td>Employee Email</td><td>'.$getAllRecords[0]['email_id'].'</td></tr>
								<tr><td>Employee Designation</td><td>'.$getAllRecords[0]['designation'].'</td></tr>
							</table>
						</div>';										
		
				}					
				$options['message'] .= '<div style="padding:20px 0 10px 0;">Please <a href="'.$baseUrl.'/index/popup" target="_blank" style="color:#b3512f;">click here</a> to login and check the details.</div>
										</div>';
				$options['cron'] = 'yes';
				$result = sapp_Global::_sendEmail($options);
			}

			/* END */
			$updateresult['result'] = 'saved';	
			$updateresult['popup'] = 'close';			
			$this->_helper->json($updateresult);
		}		
	}
	
	public function deleteAction()
	{
		 $auth = Zend_Auth::getInstance();
     		if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
				}
		 $idData = $this->_request->getParam('objid');
		 $deleteflag= $this->_request->getParam('deleteflag');
		 $idArr = array();
		 $idArr = explode('-',$idData);
		 $id = $idArr[0];$userflag = $idArr[1]; 
		 $messages['message'] = '';
		 $actionflag = 3;
		    if($id)
			{
	    	  $empscreeningModel = new Default_Model_Empscreening();
			  $data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
			  $where = array('specimen_id=?'=>$id,'flag=?'=>$userflag);
			  $Id = $empscreeningModel->SaveorUpdateDetails($data, $where);			
			  $empscreeningModel->updateusersondelete($id,$userflag);
			    if($Id == 'update')
				{
				   $menuID = EMPSCREENING;
				   $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id.'-'.$userflag); 
				   $messages['message'] = 'Process deleted successfully.';
				   $messages['msgtype'] = 'success';				   
				}   
				else
				{
                   $messages['message'] = 'Process cannot be deleted.';	
				   $messages['msgtype'] = 'error';				  
				}
			}
			else
			{ 
			 $messages['message'] = 'Process cannot be deleted.';
			 $messages['msgtype'] = 'error';			 
			}
			if($deleteflag==1)
			{
				if(	$messages['msgtype'] == 'error')
				{
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("error"=>$messages['message'],"msgtype"=>$messages['msgtype'] ,'deleteflag'=>$deleteflag));
				}
				if(	$messages['msgtype'] == 'success')
				{
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>$messages['message'],"msgtype"=>$messages['msgtype'],'deleteflag'=>$deleteflag));
				}
					
			}
			$this->_helper->json($messages);
		
	}
	
	public function checkscreeningstatusAction()
	{
		$candid = $this->getRequest()->getParam('candid');
		$empid = $this->getRequest()->getParam('empid');
		$empscreeningModel = new Default_Model_Empscreening();
		$personalData = '';$addressData = '';$companyData = '';
		if(isset($candid) && $candid != '')
		{	
			$candidateModel = new Default_Model_Candidatedetails();
			$status = $candidateModel->getcandidateData($candid);
			if($status['backgroundchk_status'] == 'Yet to start')
				$this->_redirect('empscreening/add/candid/'.$candid);
			else
				$this->_redirect('empscreening/edit/id/'.$candid.'-2');
		}
		elseif(isset($empid) && $empid != '')
		{
			$usersModel = new Default_Model_Users();
			$bgstatusArr = $usersModel->getBGstatus($empid);
			if($bgstatusArr[0]['backgroundchk_status'] == 'Yet to start' || $bgstatusArr[0]['backgroundchk_status'] == 'Not Applicable')	
			{
				$this->_redirect('empscreening/add/empid/'.$empid);
			}else{
				$this->_redirect('empscreening/edit/id/'.$empid.'-1');
			}
		}		
		
	}

    // To upload feedback file
    public function uploadfeedbackAction(){
    	$emp_screeing_model = new Default_Model_Empscreening();
    	$result = $emp_screeing_model->saveUploadedFile($_FILES);
    	$this->_helper->json($result);
    }
    
    // To download feedback file
    public function downloadAction(){
    	$feedback_file = $this->_getParam('feedback_file');
    	if(!empty($feedback_file)){
			$file = BASE_PATH.'/uploads/feedback/'.$feedback_file;
			$status = sapp_Global::downloadFile($file);
			if(!empty($status['message'])){
				$this->_forward('index');
				$this->_helper->FlashMessenger()->setNamespace('success')->addMessage($status['message']);
			}
    	}
    	$this->_forward('edit');
    }
    
    // To delete feedback file
    public function deletefeedbackAction(){
    	$emp_screeing_model = new Default_Model_Empscreening();
        $data = array('feedback_file'=>NULL, 'feedback_deletedby'=>$emp_screeing_model->getLoginUserId());
    	$where = 'id="'.$this->_getParam('rec_id').'"';
    	
    	// To empty feedback file name in DB
    	$message = $emp_screeing_model->SaveorUpdateDetails($data, $where);
    	
    	// To remove feedback file from folder
    	if($message=='update'){
    		@unlink(UPLOAD_PATH_FEEDBACK . "/" . $this->_getParam('feedback_file'));
    	}
		$this->_helper->json(array('action'=>$message));  
    }
    
}