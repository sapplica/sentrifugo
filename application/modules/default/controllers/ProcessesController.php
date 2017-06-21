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

class Default_ProcessesController extends Zend_Controller_Action
{

    private $options;
	public function preDispatch()
	{
		$auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity())
            $this->_redirect('default');
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
      //  $ajaxContext->addActionContext('displaycomments', 'html')->initContext();
		
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
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		} 
		
		$empscreeningModel = new Default_Model_Processes();
		$empModel = new Default_Model_Empscreening();
		$call = $this->_getParam('call');
		$popup = $this->getRequest()->getParam('popup');
		if($call == 'ajaxcall' || $popup == 1)
				$this->_helper->layout->disableLayout();		
		$dashboardcall = $this->_getParam('dashboardcall');
		$view = Zend_Layout::getMvcInstance()->getView();		
		$objname = $this->_getParam('objname');
		$refresh = $this->_getParam('refresh');
		$unitId = $this->_getParam('unitId');
		
		if(!isset($unitId))$unitId = '';
		$data = array();
		$searchArray = array();
		if($refresh == 'refresh')
		{
			if($dashboardcall == 'Yes')
				$perPage = DASHBOARD_PERPAGE;
			else	
				$perPage = PERPAGE;
			$sort = 'DESC';$by = 'b.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else 
		{
			if($dashboardcall == 'Yes')
				$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
			else $perPage = $this->_getParam('per_page',PERPAGE);
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'b.modifieddate';			
			$pageNo = $this->_getParam('page', 1);
			$searchData = $this->_getParam('searchData');
			$searchData = rtrim($searchData,',');
			$searchQuery = '';
			$searchArray = array();
			$tablecontent='';
			if($searchData != '' && $searchData!='undefined')
			{	
				$searchValues = json_decode($searchData);
				foreach($searchValues as $key => $val)
				{
					if($key == 'isactive')
					$searchQuery .= " b.".$key." like '%".$val."%' AND ";
					else if($key == 'startdate')
					{
						$searchQuery .= " b.createddate like '%".  sapp_Global::change_date(urldecode($val),'database')."%' AND ";
					}
					else if($key == 'enddate')
					{
						$searchQuery .= " b.modifieddate like '%".  sapp_Global::change_date(urldecode($val),'database')."%' AND b.process_status != 'In process' AND ";
					}
					else if($key == 'recentlycommenteddate')
					{
						$searchQuery .= " b.recentlycommenteddate like '%".  sapp_Global::change_date(urldecode($val),'database')."%' AND ";
					}
					else
					$searchQuery .= " ".$key." like '%".$val."%' AND ";
					$searchArray[$key] = $val;
				}
				$searchQuery = rtrim($searchQuery," AND");		
			}			
		}
		$idArr = array();
		
		$idArr = explode('-',$unitId);
		
		if(sizeof($idArr)>1)
		{
			$specimenId = intVal($idArr[0]);
			$flag = intVal($idArr[1]); 			
		}else{
			$specimenId ='';$flag = '';
		}	
		
		$personalData = $empModel->getEmpPersonalData($specimenId,$flag);		
		$objName = 'processes';
		$tableFields = array('action'=>'Action','type' => 'Check Type','agencyname' =>'Agency Name','email'=>'POC Email','process_status'=>'Process Status','explanation'=>'Explanation','isactive'=>'Active Status','startdate'=> 'Started On','enddate'=>'Ended On','recentlycommenteddate'=>'Recently Commented On');
		
	   
		$tablecontent = $empscreeningModel->getProcessesData($sort, $by, $pageNo, $perPage,$searchQuery,$unitId,$loginUserId,$loginuserGroup);     
		
		if(isset($unitId) && $unitId != '') $formgrid = 'true'; else $formgrid = '';
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
			'menuName' => 'Background check Process',
			'formgrid' => $formgrid,
			'unitId'=>$unitId,
			'empstatus'=>$personalData[0]['ustatus'],
			'add'=>'add',
			'search_filters' => array(
                    'isactive' => array('type'=>'select',
                        'filter_data'=>array(''=>'All',1 => 'Active','2'=>'Agency deleted','3'=>'Agency User deleted','4'=>'POC deleted',0 => 'Process deleted' )),
					'startdate' =>array('type'=>'datepicker'),
					'enddate' =>array('type'=>'datepicker'),
					'recentlycommenteddate' =>array('type'=>'datepicker')
                ),
			'dashboardcall'=>$dashboardcall,
			'call'=>$call
		);			
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
	}
	
	public function addpopupAction()
	{
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
		$hrem = 'true';
		$mngmntem = 'true';
		$idData = $this->getRequest()->getParam('unitId');
		$idArr = array();
		$idArr = explode('-',$idData);
		$specimen_id = $idArr[0];
		$userflag = $idArr[1]; 
		$bgstatus = '';
		$agencyids = '';
		$empmodel = new Default_Model_Empscreening();
		$emparraydata = $empmodel->checkbgstatus($specimen_id,$userflag,'status');
		if(!empty($emparraydata))
		{
			$bgstatus = $emparraydata[0]['bgcheck_status'];
		}
		$empscreeningform = new Default_Form_empscreening();
		$empscreeningform->setAttrib('id','processform');
		$submitButon = $empscreeningform->getElement("submit");
        $submitButon->setAttrib('style', 'display:none;');
		$empscreeningform->setAttrib('action',BASE_URL.'processes/addpopup/unitId/'.$idData);
		$empscreeningform->removeElement("employee");
		$processdata = array();
		$empscreeningform->removeElement("bgcheck_status");
		if($loginuserGroup != HR_GROUP && $loginuserGroup != '' && $loginuserGroup != MANAGEMENT_GROUP)
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
		/* */
		/* Checking for configurations - screening type and agencies*/
		$msgarray = array(); $errorflag = 'true';
		$checktypeModal = new Default_Model_Bgscreeningtype();
		$agencymodel = new Default_Model_Agencylist();
	    $typesData = $checktypeModal->fetchAll('isactive=1','type')->toArray();		
		$agencyData = $agencymodel->fetchAll('isactive=1','agencyname')->toArray();
		$personalData = $empmodel->getEmpPersonalData($specimen_id,$userflag);
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
		if(!empty($typesData) && !empty($agencyData))
		{
			$this->view->configure = '';
		}else{
			$this->view->configure = 'notconfigured';
		}
		$bid = '';
		if(isset($personalData[0]['businessid']))
			$bid = $personalData[0]['businessid'];
			
		if (defined('BG_CHECKS_HR_'.$bid) && $bid !='')
		   $hremailId = explode(",",constant('BG_CHECKS_HR_'.$bid));
		else {  
		   $hrem = 'false';
		   $hremailId = array();	   
		}
		if (defined('BG_CHECKS_MNGMNT_'.$bid) && $bid !='')
		   $mngmntemailId = explode(",",constant('BG_CHECKS_MNGMNT_'.$bid));
		else {  
		   $mngmntem = 'false';
		   $mngmntemailId = array();	   
		}
		if($specimen_id == $loginUserId)
			$displaymsg = 'nodata';
		else
			$displaymsg = '';
		$this->view->msgarray = $msgarray;
		$this->view->hrEmail = $hrem;
		$this->view->mngmntEmail = $mngmntem;
		$this->view->displaymsg = $displaymsg;
		/* */
        $this->view->form = $empscreeningform;
		$this->view->bgstatus = $bgstatus;
		if($this->getRequest()->getPost())
		{
			$agencyids = $this->_request->getParam('agencyids');
		    $result = $this->save($empscreeningform,$processdata);	
			$this->view->msgarray = $result; 
			$this->view->messages = $result;
			$this->view->agencyids = $agencyids;
			$this->view->contactRadio = $this->_request->getParam('contactRadio');
        }
	}
	
	public function editpopupAction()
	{
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
		$id = $this->getRequest()->getParam('id');
		$idData = $this->getRequest()->getParam('unitId');
		$hrem = 'true';$mngmntem = 'true';
		$idArr = array();
		$idArr = explode('-',$idData);
		$specimen_id = $idArr[0];
		$userflag = $idArr[1]; 
		
		$statusFlag = '';$useridsArr = array();$usernamesArr = array();$usernames = '';
		$processesform = new Default_Form_processes();
		$processesmodel = new Default_Model_Processes();
		$commentsModel = new Default_Model_Comments();
		$empmodel = new Default_Model_Empscreening();
		$emparraydata = $empmodel->checkdetailedbgstatus('','','',$id);
		$personalData = $empmodel->getEmpPersonalData($specimen_id,$userflag);
		if($emparraydata['isactive'] != '0' && $emparraydata['isactive'] != 0)
		{		
			$commentsData = $commentsModel->getComments($id,'2');		
			$processdata = $processesmodel->getsinglecheckDetailData($id);					
			$j = 0;
			for($i=0; $i<sizeof($commentsData);$i++)
			{
				if(!in_array($commentsData[$i]['from_id'],$useridsArr)){
					$useridsArr[$j] = $commentsData[$i]['from_id'];
					$j++;
				}
			}
			$userids = implode(',',$useridsArr);
			if($userids != '')
			{
				$usernamesArr = $commentsModel->getuserNames($userids);	
			}
			for($i=0; $i<sizeof($usernamesArr);$i++)
			{
				$usernames[$usernamesArr[$i]['id']]= $usernamesArr[$i]['userfullname'];
			}	
			
			$this->view->usernames = $usernames;
			$actualcommentData = $commentsModel->getComments($id,'all');
			$this->view->processdata = $processdata;
			$this->view->actualcommentData = $actualcommentData;
			$this->view->commentsData = $commentsData;
			$this->view->loginuserid = $loginUserId;
			if($id)
			{
				$processesform->setAttrib('action',BASE_URL.'processes/editpopup/id/'.$id.'/unitId/'.$idData);							
				if(isset($processdata[0]['process_status']))
				$processesform->setDefault('process_status', $processdata[0]['process_status']);
				if($this->getRequest()->getPost())
				{
					$result = $this->save($processesform,$processdata);	
					$this->view->msgarray = $result; 
					$this->view->messages = $result;	
				}
			}
			if($loginuserGroup != HR_GROUP && $loginuserGroup != '' && $loginuserGroup != MANAGEMENT_GROUP)
			{
				$processesform->removeElement("submit");
				$elements = $processesform->getElements();
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
			
			$bid = '';
			if(isset($personalData[0]['businessid']))
				$bid = $personalData[0]['businessid'];
				
			if (defined('BG_CHECKS_HR_'.$bid) && $bid !='')
			   $hremailId = explode(",",constant('BG_CHECKS_HR_'.$bid));
			else {  
			   $hrem = 'false';
			   $hremailId = array();	   
			}
			if (defined('BG_CHECKS_MNGMNT_'.$bid) && $bid !='')
			   $mngmntemailId = explode(",",constant('BG_CHECKS_MNGMNT_'.$bid));
			else {  
			   $mngmntem = 'false';
			   $mngmntemailId = array();	   
			}			
			
			if($specimen_id == $loginUserId)
				$displaymsg = 'nodata';
			else
				$displaymsg = '';
			$this->view->hrEmail = $hrem;
			$this->view->mngmntEmail = $mngmntem;
			$this->view->displaymsg = $displaymsg;
			$this->view->form = $processesform;
			
			// To show feedback file name and give delete option to user
			$this->view->feedback_file = $emparraydata['feedback_file'];
			$this->view->rec_id = $emparraydata['id'];
			$this->view->loginuserGroup = $loginuserGroup;
			
			$this->view->ermsg = '';			
		}else{
			$this->view->ermsg = 'deleted';
		}
	}
	
	public function save($processesform,$processdata)
	{ 
		$baseUrl = BASE_URL;
		$baseUrl = rtrim($baseUrl,'/');		
		
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserRole = $auth->getStorage()->read()->emprole;	
		} 
		$usermodel = new Default_Model_Users();
		$hrem = 'true';$mngmntem = 'true';
		$agencyEmail = '';$lmanager1Email = '';$lmanager1Name = 'Manager';$agencysalutationName = 'Agency';
		$id = $this->getRequest()->getParam('id');
		$idData = $this->getRequest()->getParam('unitId');	
		$idArr = array();
		$idArr = explode('-',$idData);
		$specimen_id = $idArr[0];
		$userflag = $idArr[1]; 
		$msgarray = array(); 
		if($idArr[1] == 2) {	$flag = 'cand'; $person='candidate'; }else { $flag = 'emp'; $person = 'employee'; }
		$processesmodel = new Default_Model_Processes();
		$empscreeningModel = new Default_Model_Empscreening();
		$statusFlag = ''; $close = '';$controllername = 'processes';
		$errorflag = 'true';
		$BGStatus = '';$oldbgstatus = '';$mailsentflag = 0;	
		if(!empty($processdata)) 
		{			
			$newStatus = $this->getRequest()->getParam('process_status');
			$previousstatus = $processdata[0]['process_status'];
			$agencyid = $processdata[0]['agencyid'];	
			$checktype = $processdata[0]['checktypeid'];	
			$processStatus = $processesmodel->getProcessStatus($specimen_id,$userflag,$agencyid,$checktype);		
			if(!empty($processStatus) && sizeof($processStatus)>0) 
			{
				$oldstatus = $processStatus[0]['process_status'];
				$BGStatus = $processStatus[0]['bgcheck_status'];			
				/* Check - Adding a record with same userid, agencyid and check type */
				$exists = 'false';
				for($i=0;$i<sizeof($processStatus);$i++)
				{
					if(($processStatus[$i]['process_status']== 'On hold' || $processStatus[$i]['process_status'] == 'In process') && $processStatus[$i]['process_status'] == $newStatus)
					$exists = 'true';							
				}
				if($newStatus == $previousstatus)
				{
					
					$msgarray['StatusError'] = "Please change the status.";
					$errorflag = "false"; 
				}
				else if($BGStatus != 'Complete' && $exists == 'true') 
				{
					$msgarray['StatusError'] = "The ".$person." is already assigned to the selected agency. Please re-assign the ".$person." to another agency.";
					$errorflag = "false"; 
				}
				/* END */
			}
		}
		else 
		{
			$checktype = $this->_request->getParam('checktype');
			$radio_pocId = $this->_request->getParam('contactRadio');			
			$agencyid = $this->_request->getParam('agencyids');
			$newStatus = 'In process';
			$agencyData = array();$agencyPOCData = array();		
		
			if(empty($checktype) && $checktype == '')
			{
				$msgarray['checktype'] = "Please check atleast one screening type";
				$errorflag = "false"; 
			}
			else if(empty($radio_pocId) || $radio_pocId == '')
			{
				$msgarray['contactRadio'] = "Please select point of contact";
				$errorflag = "false"; 
			}
			if($checktype)
			{
				$agencyArr = array();
				$agencyArr = $checktype;
				$agencyData = $empscreeningModel->getAgencyData($agencyArr,'','');				
			}
			if(!isset($agencyid) || $agencyid == '') 
			{
				$agencyid = $this->getRequest()->getParam('defaultagencyid');			
			}
			if(!isset($agencyid) || $agencyid == '')
			{
				$msgarray['agencyids'] = "Please select agency.";
				$errorflag = "false"; 
			}			
			if($agencyid)
			$agencyPOCData = $empscreeningModel->getAgencyPOCData($agencyid);
			$this->view->agencyData = $agencyData;
			$this->view->agencyPOCData = $agencyPOCData;
			/* Checking for configurations - screening type and agencies*/			
			$checktypeModal = new Default_Model_Bgscreeningtype();
			$agencymodel = new Default_Model_Agencylist();
			$typesData = $checktypeModal->fetchAll('isactive=1','type')->toArray();		
			$agencyData = $agencymodel->fetchAll('isactive=1','agencyname')->toArray();
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
			/* Check - Adding a record with same userid, agencyid and check type (for each check type ) */
			if(is_array($checktype) && $errorflag != "false")
			{
				for($i=0;$i<sizeof($checktype);$i++)
				{
					$processStatus = array();
					$processStatus = $processesmodel->getProcessStatus($specimen_id,$userflag,$agencyid,$checktype[$i]);		
					if(!empty($processStatus) && sizeof($processStatus > 0))
					{
						$exists = 'false';
						for($i=0;$i<sizeof($processStatus);$i++)
						{
							if($processStatus[$i]['process_status']== 'On hold' || $processStatus[$i]['process_status'] == 'In process')
							$exists = 'true';							
						}						
						$oldstatus = $processStatus[0]['process_status'];
						$BGStatus = $processStatus[0]['bgcheck_status']; 
						if($exists == 'true'  && $BGStatus != 'Complete')  
						{
							$msgarray['StatusError'] = "The ".$person." is already assigned to the selected agency. Please re-assign the ".$person." to another agency.";
							$errorflag = "false"; 							
						}
					}
					$oldbgstatusData = $processesmodel->getProcessStatus($specimen_id,$userflag,'','');		
					if(!empty($oldbgstatusData))
						$oldbgstatus = $oldbgstatusData[0]['bgcheck_status'];						
					else $oldbgstatus = '';
				}
			}
			/* END */			
		}		
		if($processesform->isValid($this->_request->getPost()) && $errorflag != 'false')
		{
			$date = new Zend_Date();
			$actionflag = '';
			$tableid  = '';				
			/*	A New process is created for a user whose background check status is in 'Complete' status. Then updating the bg status to 'In process' */
			if(($BGStatus == 'Complete' || $oldbgstatus == 'Complete' || $BGStatus == 'On hold' || $oldbgstatus == 'On hold' ) && ($newStatus == 'In process'))
			{
				$totalstatusData = array(
						'bgcheck_status'	=>		'In process',
						'modifiedby'		=>		$loginUserId,
						'modifieddate'		=>		gmdate("Y-m-d H:i:s")
					);
				$totalstatusWhere = array(
						'specimen_id=?'		=>		$specimen_id,
						'flag=?'			=>		$userflag
					);
				$empscreeningModel->SaveorUpdateDetails($totalstatusData, $totalstatusWhere);		
				
				/* Updating back ground check status to In process in employees/candidates status */
				if($userflag == 1){
					$empData = array(
							'backgroundchk_status' => 	'In process',
							'modifiedby'		=>		$loginUserId,
							'modifieddate'		=>		gmdate("Y-m-d H:i:s")
						);
					$empWhere = array('id=?'=>$specimen_id);
					$usermodel->addOrUpdateUserModel($empData, $empWhere);
				}
				else {
					$candModel = new Default_Model_Candidatedetails();
					$candData = array(
							'backgroundchk_status' => 	'In process',
							'modifiedby'		=>		$loginUserId,
							'modifieddate'		=>		gmdate("Y-m-d H:i:s")
						);
					$candWhere = array('id=?'=>$specimen_id);
					$candModel->SaveorUpdateUserData($candData, $candWhere);
				}
				/* END */
				
				/* Mail to HRD, L1 and L2 managers that the background check has been re-opened*/
				if($BGStatus != 'On hold' && $oldbgstatus != 'On hold' )
				{
					$empData = $empscreeningModel->getEmpPersonalData($specimen_id,$userflag);	
					if($userflag == 1)
					{
						$username = $empData[0]['name'];
						$lmanager1Email = $empData[0]['rmanager_email'];
						$lmanager1Name = $empData[0]['reporting_manager'];
					}
					else {
					$username = $empData[0]['candidate_name'];
					$lmanager1Email = '';
					}
					$bid = '';
					if(isset($empData[0]['businessid'])) 
						$bid = $empData[0]['businessid'];

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
					else {
						$mngmntem = 'false';
					    $mngmntemailId = array();
					}
					
					if (defined('BG_CHECKS_HR_'.$bid) && $bid !='')
					   $hremailId = explode(",",constant('BG_CHECKS_HR_'.$bid));
					else {  
					   $hrem = 'false';
					   $hremailId = array();	   
					}			
					
					$emailArr =  array_merge($agencyemail1,$manager1,$mngmntemailId,$hremailId);
					for($i=0;$i<sizeof($emailArr);$i++)
					{	
						$salutation = 'Dear Sir/Madam,';
						if($i == 0)
						{
							$salutation = 'Dear '.ucfirst($agencysalutationName).',';
							$options['toName'] = ucfirst($agencysalutationName);
						}
						if($i == 1)
						{
							$salutation = 'Dear '.ucfirst($lmanager1Name).',';
							$options['toName'] = ucfirst($lmanager1Name);
						}
						if($i == 2)
						{
							$salutation = 'Dear Management,';
							$options['toName'] = 'Management';
						}
						if($i == 3)
						{
							$salutation = 'Dear HR,';
							$options['toName'] = 'HR';
						}
						$options['subject'] = APPLICATION_NAME.' : Background check re-opened';	
						$options['header'] = 'Background check re-opened';
						$options['toEmail'] = $emailArr[$i];  
						
						$createdbyName = $usermodel->getUserDetails($loginUserId);		
						if($i == 0)
						{		
							$mailsentflag = 1;
							$options['message'] = '<div>'.$salutation.'<div>The background check for '.ucfirst($username).'
													has been re-opened by '.$createdbyName[0]['userfullname'].'. </div>
													<div style="padding:20px 0 10px 0;">Please <a href="'.$baseUrl.'/index/popup" target="_blank" style="color:#b3512f;">click here</a> to login and check the details.</div>
												</div>';
						}else{
							$options['message'] = '<div>'.$salutation.'<div>The background check for '.ucfirst($username).'
													has been re-opened by '.$createdbyName[0]['userfullname'].'. </div>
													<div style="padding:20px 0 10px 0;">Please <a href="'.$baseUrl.'/index/popup" target="_blank" style="color:#b3512f;">click here</a> to login and check the details.</div>
												</div>';	
						}
						$options['cron'] = 'yes';
						sapp_Global::_sendEmail($options); 
					}
				}
				/* END */
			}	
			/* END */
			$data = array(  
						'specimen_id'		=>		$specimen_id,
						'flag'				=>		$userflag,
						'bgagency_id'		=>		$agencyid,
						'modifiedby'		=>		$loginUserId,
						'modifieddate'		=>		gmdate("Y-m-d H:i:s")
					);
			if($id!='') 
			{
				$data['process_status']	=	$newStatus;
				$data['explanation']	=	NULL;
				$where = array('id=?'=>$id);  
				$actionflag = 2;
			}else 
			{
				$data['process_status']	= $newStatus;				
				$data['bgagency_pocid'] = $radio_pocId;
				$data['createdby'] = $loginUserId;
				$data['createddate'] = gmdate("Y-m-d H:i:s");
				$data['isactive'] = 1;
				$where = '';
				$actionflag = 1;				
					
				if($newStatus == 'In process' && ($BGStatus != 'Complete' || $oldbgstatus != 'Complete' || $BGStatus != 'On hold' || $oldbgstatus != 'On hold') && $mailsentflag == 0)
				{
					$empData = $empscreeningModel->getEmpPersonalData($specimen_id,$userflag);	
					$table = '';
					if($userflag == 1)
					{
						$username = $empData[0]['name'];
						$lmanager1Email = $empData[0]['rmanager_email'];
						$lmanager1Name = $empData[0]['reporting_manager'];
						$table = '<div>
											<table border="1" style="border-collapse:collapse;">
												<tr><td>Employee ID</td><td>'.$empData[0]['employee_id'].'</td></tr>
												<tr><td>Employee Email</td><td>'.$empData[0]['email_id'].'</td></tr>
												<tr><td>Employee Designation</td><td>'.$empData[0]['designation'].'</td></tr>
											</table>
										</div>';
					}
					else {
					$username = $empData[0]['candidate_name'];
					$lmanager1Email = '';
					$table = '<div>Candidate Email : '.$empData[0]['email'].'</div>';
					}
					if($agencysalutationName != '')
					{
						$salutation = 'Dear '.ucfirst($agencysalutationName).',';
						$options['toName'] = ucfirst($agencysalutationName);
					}
					else
					{
						$salutation = 'Dear Sir/Madam,';
						$options['toName'] = 'Agency';
					}
					$createdbyName = $usermodel->getUserDetails($loginUserId);	
					$options['subject'] = APPLICATION_NAME.' : Background check initiated';	
					$options['header'] = 'Background check initiated';
					$options['toEmail'] = $agencyEmail;  
					$options['message'] = '<div>'.$salutation.'<div>'.ucfirst($username).' has been sent for background check by '.ucfirst($createdbyName[0]['userfullname']).'. Please find the details below.</div>'.$table.'
					                    
												<div style="padding:20px 0 10px 0;">Please <a href="'.$baseUrl.'/index/popup" target="_blank" style="color:#b3512f;">click here</a> to login and check the details.</div>
											</div>';
					$options['cron'] = 'yes';
					sapp_Global::_sendEmail($options); 
				}
			}			
			if(is_array($checktype))
			{
				for($i=0;$i<sizeof($checktype);$i++)
				{
					$data['bgcheck_type']	=	$checktype[$i];
					$detailId = $empscreeningModel->SaveorUpdateDetails($data, $where);
				}
			}
			else	
			{
				$data['bgcheck_type']	=	$checktype;
				$detailId = $empscreeningModel->SaveorUpdateDetails($data, $where);
			}
			/* If all the processes are in 'Complete' status, then updating the background check status to 'Complete' and sending mail to HR, reporting manager  */
			$checkAllprocesses = $processesmodel->getProcessStatus($specimen_id,$userflag);							
			$completecount = 0;$onholdcount = 0;
			for($i=0;$i<sizeof($checkAllprocesses);$i++)
			{					
				if($checkAllprocesses[$i]['process_status'] == 'Complete'){
					$completecount = $completecount+1;
				}				
			}			
			if($completecount == sizeof($checkAllprocesses))
			{	
				$totalstatusData = array(
						'bgcheck_status'	=>		'Complete',
						'modifiedby'		=>		$loginUserId,
						'modifieddate'		=>		gmdate("Y-m-d H:i:s")
					);
				$totalstatusWhere = array(
						'specimen_id=?'		=>		$specimen_id,
						'flag=?'			=>		$userflag
					);
				$empscreeningModel->SaveorUpdateDetails($totalstatusData, $totalstatusWhere);
				
				/* Updating back ground check status to Completed in employees/candidates status */
				if($userflag == 1){
					$usermodel = new Default_Model_Users();
					$empData = array(
							'backgroundchk_status' => 	'Completed',
							'modifiedby'		=>		$loginUserId,
							'modifieddate'		=>		gmdate("Y-m-d H:i:s")
						);
					$empWhere = array('id=?'=>$specimen_id);
					$usermodel->addOrUpdateUserModel($empData, $empWhere);
				}
				else {
					$candModel = new Default_Model_Candidatedetails();
					$candData = array(
							'backgroundchk_status' => 	'Completed',
							'modifiedby'		=>		$loginUserId,
							'modifieddate'		=>		gmdate("Y-m-d H:i:s")
						);
					$candWhere = array('id=?'=>$specimen_id);
					$candModel->SaveorUpdateUserData($candData, $candWhere);
				}
				/* END */
				
				/* Mail to HRD, L1 and L2 managers*/
				if($newStatus == 'Complete')
				{				
					$empData = $empscreeningModel->getEmpPersonalData($specimen_id,$userflag);	
					if($userflag == 1)
					{
						$username = $empData[0]['name'];
						$lmanager1Email = $empData[0]['rmanager_email'];
						$lmanager1Name = $empData[0]['reporting_manager'];
					}
					else {
					$username = $empData[0]['name'];
					$lmanager1Email = '';
					}
					
					$bid = '';
					if(isset($empData[0]['businessid'])) 
						$bid = $empData[0]['businessid'];

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
					for($i=0;$i<sizeof($emailArr);$i++)
					{
						$salutation = 'Dear Sir/Madam,';
						if($i == 0)
						{
							$salutation = 'Dear '.ucfirst($lmanager1Name).',';
							$options['toName'] = ucfirst($lmanager1Name);  
						}
						else if($i == 1)
						{
							$salutation = 'Dear Management,';
							$options['toName'] = 'Management';  
						}
						else{
							$salutation = 'Dear HR,';
							$options['toName'] = 'HR';  
						}
						$options['subject'] = APPLICATION_NAME.' : Background check completed';	
						$options['header'] = 'Background check completed';
						$options['toEmail'] = $emailArr[$i];  
						
						$options['message'] = '<div>'.$salutation.'<div>The background check for '.ucfirst($username).'
													has been completed.</div>
													<div style="padding:20px 0 10px 0;">Please <a href="'.$baseUrl.'/index/popup" target="_blank" style="color:#b3512f;">click here</a> to login and check the details.</div>
												</div>';	
						$options['cron'] = 'yes';
						sapp_Global::_sendEmail($options); 
					}
				}

				/* END */
			}			
			/* End */
			/* If all the processes are in 'On hold' status, then updating the background check status to 'On hold' */
			for($i=0;$i<sizeof($checkAllprocesses);$i++)
			{					
				if($checkAllprocesses[$i]['process_status'] == 'On hold'){
					$onholdcount = $onholdcount+1;
				}				
			}
			if($onholdcount == sizeof($checkAllprocesses))
			{
				$totalstatusData = array(
						'bgcheck_status'	=>		'On hold',
						'modifiedby'		=>		$loginUserId,
						'modifieddate'		=>		gmdate("Y-m-d H:i:s")
					);
				$totalstatusWhere = array(
						'specimen_id=?'		=>		$specimen_id,
						'flag=?'			=>		$userflag
					);
				$empscreeningModel->SaveorUpdateDetails($totalstatusData, $totalstatusWhere);
				/* Updating back ground check status to Completed in employees/candidates status */
				if($userflag == 1){
					$usermodel = new Default_Model_Users();
					$empData = array(
							'backgroundchk_status' => 	'On hold',
							'modifiedby'		=>		$loginUserId,
							'modifieddate'		=>		gmdate("Y-m-d H:i:s")
						);
					$empWhere = array('id=?'=>$specimen_id);
					$usermodel->addOrUpdateUserModel($empData, $empWhere);
				}
				else {
					$candModel = new Default_Model_Candidatedetails();
					$candData = array(
							'backgroundchk_status' => 	'On hold',
							'modifiedby'		=>		$loginUserId,
							'modifieddate'		=>		gmdate("Y-m-d H:i:s")
						);
					$candWhere = array('id=?'=>$specimen_id);
					$candModel->SaveorUpdateUserData($candData, $candWhere);
				}
				/* END */
			}
			/* END */
			
			if($detailId == 'update')
			{
				$tableid = $id;
				$this->view->eventact = 'updated';
			}
			else
			{
				$tableid = $detailId; 	
				$this->view->eventact = 'added';
			}
			$actionflag = 2;	 //Edit of the candidate or employee
			$menuID = EMPSCREENING;
			$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$specimen_id.'-'.$userflag);
			
			$close = 'close';
			$this->view->popup=$close;				
		}
		else
		{
			$messages = $processesform->getMessages();
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
				}
			}
			return $msgarray;	
		}
		$this->view->statusFlag = $statusFlag;		
	}
	
	public function viewpopupAction()
	{
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->getRequest()->getParam('id');
		$idData = $this->getRequest()->getParam('unitId');	
		
		$idArr = array();
		$idArr = explode('-',$idData);
		$specimen_id = $idArr[0];
		$userflag = $idArr[1]; 
		
		$statusFlag = ''; $useridsArr = array();$usernamesArr = array();$usernames = '';
		$processesform = new Default_Form_processes();
		$processesmodel = new Default_Model_Processes();
		$commentsModel = new Default_Model_Comments();		
		
		$commentsData = $commentsModel->getComments($id);
		$j = 0;
		for($i=0; $i<sizeof($commentsData);$i++)
		{
			if(!in_array($commentsData[$i]['from_id'],$useridsArr)){
				$useridsArr[$j] = $commentsData[$i]['from_id'];
				$j++;
			}
		}
		$userids = implode(',',$useridsArr);
		if($userids != '')
		{
			$usernamesArr = $commentsModel->getuserNames($userids);	
		}
		for($i=0; $i<sizeof($usernamesArr);$i++)
		{
			$usernames[$usernamesArr[$i]['id']]= $usernamesArr[$i]['userfullname'];
		}		
		
		$elements = $processesform->getElements();
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)
			{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
				$element->setAttrib("disabled", "disabled");
					}
        	}
        }		
		$processdata = $processesmodel->getsinglecheckDetailData($id);
		
		if(!empty($processdata))
		$processesform->setDefault('process_status',$processdata[0]['process_status']);
		
		if($specimen_id == $loginUserId)
			$displaymsg = 'nodata';
		else
			$displaymsg = '';
			
		$this->view->displaymsg = $displaymsg;
		$this->view->processdata = $processdata;
		$this->view->commentsData = $commentsData;
		$this->view->usernames = $usernames;
		$this->view->form = $processesform;
		$this->view->loginuserid = $loginUserId;
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
		 $completedDetails = 0;$onholdDetails = 0;$specimen_id = 0;$flag=1;
		    if($id)
			{
	    	  $empscreeningModel = new Default_Model_Empscreening();		
			  $empmodel = new Default_Model_Empscreening();
			  $emparraydata = $empmodel->checkdetailedbgstatus('','','',$id);			  		  
			 
			  if($emparraydata['isactive'] != '0' && $emparraydata['isactive'] != 0)
			  {
				  $checkemparraydata = $empmodel->checkdetailedbgstatus('','','',$id,'findcompleted');			 
				  for($i=0;$i<sizeof($checkemparraydata);$i++)
				  {
					$specimen_id = $checkemparraydata[0]['specimen_id'];
					$flag = $checkemparraydata[0]['flag'];
					if($checkemparraydata[$i]['process_status'] == 'Complete')
					{
						$completedDetails = $completedDetails + 1;						
					}
					else if($checkemparraydata[$i]['process_status'] == 'On hold')
						$onholdDetails = $onholdDetails + 1;
				  }	
				 
				  if($completedDetails == sizeof($checkemparraydata))
				  {
						$empmodel->updatebgstatus('complete',$specimen_id,$flag);	
				  }	
				  if($onholdDetails == sizeof($checkemparraydata))
				  {
						$empmodel->updatebgstatus('onhold',$specimen_id,$flag);	
				  }	
				  $data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
				  $where = array('id=?'=>$id);
				  $Id = $empscreeningModel->SaveorUpdateDetails($data, $where);
					if($Id == 'update')
					{
					   $menuID = EMPSCREENING;
					   $messages['message'] = 'Process deleted successfully';
					   $messages['msgtype'] = 'success';
					   $messages['flagtype'] = 'process';					   
					}   
					else
					{
					   $messages['message'] = 'Process cannot be deleted';	
					   $messages['msgtype'] = 'error';
					   $messages['flagtype'] = 'process';
					}
				}else{
					   $messages['message'] = 'As the process has been made inactive, you cannot delete it.';	
					   $messages['msgtype'] = 'error';
					   $messages['flagtype'] = 'process';
					   $messages['redirect'] = 'no';
				}
			}
			else
			{ 
			 $messages['message'] = 'Process cannot be deleted';
			 $messages['msgtype'] = 'error';
			 $messages['flagtype'] = 'process';
			}
			$this->_helper->json($messages);
		
	}
	
	public function savecommentsAction()
	{
		try{
			$auth = Zend_Auth::getInstance();
			if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;	
					$loginuserGroup = $auth->getStorage()->read()->group_id;
				}
			$detailId = $this->_request->getParam('detailid');
			$comment = $this->_request->getParam('comment');
			$agency_id = $this->_request->getParam('agency_id');
			$hr_id = $this->_request->getParam('hr_id');
			$emp_screening_model = new Default_Model_Empscreening();
			if(isset($comment) && $comment != '')
			{
				if($loginuserGroup == HR_GROUP || $loginuserGroup == MANAGEMENT_GROUP) $toId = $agency_id; else $toId = $hr_id;
				$date = new Zend_Date();	
				$data = array(  
								'bgdet_id'			=>		$detailId,
								'comment'			=>		$comment,
								'from_id'			=>		$loginUserId,
								'to_id'				=>		$toId,
								'createddate'		=>		gmdate("Y-m-d H:i:s")						
							);
				$where = '';
				$commentsModel = new Default_Model_Comments();		
				$tableid = $commentsModel->SaveorUpdateComments($data, $where);
				$commData = array(
								'recentlycommentedby'		=> 	$loginUserId,
								'recentlycommenteddate'		=>	gmdate("Y-m-d H:i:s")
						);
				$commwhere = "id='$detailId'";
				$emp_screening_model->SaveorUpdateDetails($commData, $commwhere);
				$actualcommentData = $commentsModel->getComments($detailId,'all');
				if(sizeof($actualcommentData) > 2)	$updateresult['commentcount'] = 'morethan2';
				else $updateresult['commentcount'] = '';
				$updateresult['result'] = 'saved';
			}else{
				$updateresult['commentcount'] = '';
				$updateresult['result'] = 'error';
			}
			$this->_helper->json($updateresult);		

		}catch(Exception $e){
			exit($e->getMessage());
		}

		
	}
	
	public function displaycommentsAction()
	{
		$this->_helper->layout->disableLayout();
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
				$loginUserId = $auth->getStorage()->read()->id;
				$loginuserRole = $auth->getStorage()->read()->emprole;	
		}
		$useridsArr = array();
		$usernamesArr = array();
		$usernames = array();
		$userids = '';
		$commentsModel = new Default_Model_Comments();	
		$id = $this->_request->getParam('detailid');
		$flag = $this->_request->getParam('dispFlag');
		$limitcount = $this->_request->getParam('limcount');
		if($limitcount == '2')
		$commentsData = $commentsModel->getComments($id,'2');	
		else
		$commentsData = $commentsModel->getComments($id,'100');
		
		$actualcommentData = $commentsModel->getComments($id,'all');
		
		$j = 0;
		for($i=0; $i<sizeof($commentsData);$i++)
		{
			if(!in_array($commentsData[$i]['from_id'],$useridsArr)){
				$useridsArr[$j] = $commentsData[$i]['from_id'];
				$j++;
			}
		}
		$userids = implode(',',$useridsArr);
		if($userids != '' || $userids != ','){
			$usernamesArr = $commentsModel->getuserNames($userids);	
		}
			for($i=0; $i<sizeof($usernamesArr);$i++)
			{
				$usernames[$usernamesArr[$i]['id']]= $usernamesArr[$i]['userfullname'];
			}	
		
		$this->view->actualcount = count($actualcommentData);
		$this->view->limitcount = $limitcount;
		$this->view->usernames = $usernames;
		$this->view->commentsData = $commentsData;
		$this->view->loginuserid = $loginUserId;		
	}
	
	public function savefeedbackAction(){
		try{
			$auth = Zend_Auth::getInstance();
			if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;	
					$loginuserGroup = $auth->getStorage()->read()->group_id;
				}
			$detailId = $this->_request->getParam('detailid');
			$emp_screening_model = new Default_Model_Empscreening();
			
			// To save feedback file name in the database
			$feedback_file = $this->_request->getParam('feedback_file', NULL);
			if(!empty($feedback_file)){
				$data = array('feedback_file'=>$feedback_file);
				$where = "id='$detailId'";			
				$result = $emp_screening_model->SaveorUpdateDetails($data, $where);
				$updateresult['feedback_file'] = $result;
			}

			$updateresult['result'] = 'saved';
			$this->_helper->json($updateresult);		

		}catch(Exception $e){
			exit($e->getMessage());
		}

		
	}
	
}