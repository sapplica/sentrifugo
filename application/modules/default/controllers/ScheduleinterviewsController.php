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

class Default_ScheduleinterviewsController extends Zend_Controller_Action
{
    private $_options;
    public function preDispatch()
    {
        $auth = Zend_Auth::getInstance();
        $session = sapp_Global::_readSession();
        if(!isset($session))
        {
            if($this->getRequest()->isXmlHttpRequest())
            {
                echo Zend_Json::encode( array('login' => 'failed') );
                die();	
            }
            else
            {
                $this->_redirect('');
            }
        }
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('getgroupmenu', 'html')->initContext();
    }
	
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();		
    }
    public function candidatepopupAction()
    {
        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
        $req_model = new Default_Model_Requisition();      
		$jobtitleModel = new Default_Model_Jobtitles();
        $cand_model = new Default_Model_Candidatedetails();
        $candwork_model = new Default_Model_Candidateworkdetails();
        $country_model = new Default_Model_Countries();
        $role_model = new Default_Model_Roles();
        $vendorsmodel= new Default_Model_Vendors();
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $login_group_id = $auth->getStorage()->read()->group_id;
            $login_role_id = $auth->getStorage()->read()->emprole;
        }
        $id = $this->getRequest()->getParam('cand_id');    
		$form = new Default_Form_Candidatedetails();   
       
        $req_data = array();$jobtitle = '';$req_data['jobtitlename'] = '';    
		try{
			$candidateData = $cand_model->getcandidateData($id);
            $req_data = $req_model->getRequisitionDataById($candidateData['requisition_id']);
            
            $req_data['cand_resume'] = (!empty($candidateData['cand_resume']))?$candidateData['cand_resume']:'';
            $req_data['rec_id'] = $id; 
                        
			$jobttlArr = $jobtitleModel->getsingleJobTitleData($req_data['jobtitle']);
			if(!empty($jobttlArr) && $jobttlArr != 'norows')
			{
				$jobtitle = $jobttlArr[0]['jobtitlename'];
				$req_data['jobtitlename'] = $jobttlArr[0]['jobtitlename'];
			}
		    //for vendors
		   
		  if($candidateData['source']=="Vendor"  )
			{
				$vendorsmodel= new Default_Model_Vendors();
				$vendorsdataArr=$vendorsmodel->getVendorsList();
				$req_options = array();
				foreach($vendorsdataArr as $vendorsdata){
					$form->vendors->addMultiOption($vendorsdata['id'],utf8_encode($vendorsdata['name']));
				}
				
				$form->vendors->setValue($candidateData['source_val']);
			}
			if($candidateData['source']=="Website" )
			{
					
				$form->referalwebsite->setValue($candidateData['source_val']);
			}
			if($candidateData['source']=="Referal"  )
			{
				$form->referal->setValue($candidateData['source_val']);
			}
		    
		  
            $req_options = array();
            $req_options[$req_data['id']] = $req_data['requisition_code'];
			try{
				$candidateworkData = $candwork_model->getcandidateworkData($id);
				$countryId = $candidateData['country'];$stateId = $candidateData['state'];$cityId = $candidateData['city'];
				if($countryId && $stateId)
				{
					$statesmodel = new Default_Model_States();
					$citiesmodel = new Default_Model_Cities();
					$statesData = $statesmodel->getStatesList($countryId);
					$citiesData = $citiesmodel->getCitiesList($stateId);
					foreach($statesData as $res) 
					$form->state->addMultiOption($res['id'],utf8_encode($res['state_name']));
					foreach($citiesData as $res) 
					$form->city->addMultiOption($res['id'],utf8_encode($res['city_name']));
					
					$form->setDefault('country',$countryId);
					$form->setDefault('state',$stateId);
					$form->setDefault('city',$cityId);		
					$form->setDefault('job_title',$jobtitle);	
				}
				
				$countrieslistArr = $country_model->getTotalCountriesList();
                                if(sizeof($countrieslistArr)>0){
                                    $form->country->addMultiOption('0','Select Country');
                                    foreach($countrieslistArr as $countrieslistres)
                                    {
                                            $form->country->addMultiOption($countrieslistres['id'],utf8_encode($countrieslistres['country_name']) );
                                    }
                                }else{
                                        $msgarray['country'] = 'Countries not configured yet';
                                }
				$form->requisition_id->addMultiOptions(array(''=>'Select Requisition ID')+$req_options);
				
				if($id)
				{
					$form->submit->setLabel('Update');                			                						            						
					$form->populate($candidateworkData);
					$form->populate($candidateData);
				}
                                $elements = $form->getElements();
                                if(count($elements)>0)
                                {
                                        foreach($elements as $key=>$element)
                                        {
                                                if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
                                                $element->setAttrib("disabled", "disabled");
                                                        }
                                }
                                }
                                $previ_data = sapp_Global::_checkprivileges(CANDIDATEDETAILS,$login_group_id,$login_role_id,'edit');
				$this->view->form = $form; 
                $this->view->previ_data = $previ_data;
				$this->view->workdata = $candidateworkData;
				$this->view->ermsg = ''; 
                $this->view->req_data = $req_data;
                $objName = 'candidatedetails';
                $this->view->id = $id;
                $this->view->controllername = $objName;
                $this->view->candidate_data = $candidateData;
			}catch(Exception $e){
				$this->view->nodata = 'nodata';
			}
		}catch(Exception $e){
			$this->view->nodata = 'nodata';
		}
    }
    public function indexAction()
    {
        $intrvw_model = new Default_Model_Interviewdetails();	
        $call = $this->_getParam('call');
        if($call == 'ajaxcall')
            $this->_helper->layout->disableLayout();
		
        $view = Zend_Layout::getMvcInstance()->getView();		
        $objname = $this->_getParam('objname');
        $refresh = $this->_getParam('refresh');
        $data = array();
        $searchQuery = '';
        $searchArray = array();
        $tablecontent='';
        $dashboardcall = $this->_getParam('dashboardcall');
        if($refresh == 'refresh')
        {
            if($dashboardcall == 'Yes')
                $perPage = DASHBOARD_PERPAGE;
            else	
                $perPage = PERPAGE;
            $sort = 'DESC';$by = 'c.createddate';$pageNo = 1;$searchData = '';$searchQuery = '';
            $searchArray = array();
        }
        else 
        {
            $sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
            $by = ($this->_getParam('by')!='')? $this->_getParam('by'):'c.createddate';
            if($dashboardcall == 'Yes')
                $perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
            else 
                $perPage = $this->_getParam('per_page',PERPAGE);
            $pageNo = $this->_getParam('page', 1);
            $searchData = $this->_getParam('searchData');	          
        }
       
        $dataTmp = $intrvw_model->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);
        array_push($data,$dataTmp);
		$this->view->dataArray = $dataTmp;
        $this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
    }

    public function viewAction()
    {	        
        $cand_model = new Default_Model_Candidatedetails();  
        $requi_model = new Default_Model_Requisition();
        $interview_model = new Default_Model_Interviewdetails();
        $interview_round_model = new Default_Model_Interviewrounddetails();
		$jobtitleModel = new Default_Model_Jobtitles();
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
        }
                        
        $id = $this->getRequest()->getParam('id');
		$intId = $id;
        $callval = $this->getRequest()->getParam('call');
        if($callval == 'ajaxcall')
            $this->_helper->layout->disableLayout();
		$previousroundstatus = '';
        $form = new Default_Form_Interviewrounds();   
        $form->setAction(BASE_URL.'scheduleinterviews/edit/id/'.$id);
        $form->id->setValue($id);
		$form->removeElement('req_id');		$form->removeElement('candidate_name');
		$form->removeElement('interviewer_id');		$form->removeElement('interview_mode');
		$form->removeElement('int_location');		$form->removeElement('country');
		$form->removeElement('state');		$form->removeElement('city');
		$form->removeElement('interview_time');		$form->removeElement('interview_date');
		$form->removeElement('interview_round');
        $data = array();$jobtitle = '';
		$elements = $form->getElements();
		if(count($elements)>0)
        {
            foreach($elements as $key=>$element)
            {
                if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments"))
                {
                    $element->setAttrib("disabled", "disabled");
                }
            }
        }
		try
		{
                    if($id >0 && is_numeric($id))
                    {
			$interviewData = $interview_model->getSingleInterviewData($intId);
			$reqstatusArray = array('On hold','Closed','Complete');
				$previousroundstatus = $interview_model->getinterviewroundnumber($intId);
				$previousroundstatus = $previousroundstatus['round_status'];
				$data = $interview_model->getCandidateDetailsById($intId);
				if(!empty($data) && $data['interview_status'] != 'Requisition Closed/Completed' && $data['interview_status'] != 'Completed' && $data['interview_status'] != 'Completed' && !in_array($data['req_status'],$reqstatusArray))
				{
				
				   if(($data['interview_status'] == 'On hold') && ($loginuserGroup == MANAGER_GROUP || $loginuserGroup == EMPLOYEE_GROUP || $loginuserGroup == SYSTEMADMIN_GROUP ))
				   {
					$this->view->ermsg = 'nodata';	
				   }
				   else 
				   {
					$data['jobtitlename'] = '';
                                        
					$round_count = $interview_round_model->getRoundCnt($data['id'],$id);
					$irData = $this->interviewRoundsGrid($id,$interviewData['interview_status']);				
					$jobttlArr = $jobtitleModel->getsingleJobTitleData($data['jobtitle']);
					if(!empty($jobttlArr) && $jobttlArr != 'norows')
					{
						$jobtitle = $jobttlArr[0]['jobtitlename'];
						$data['jobtitlename'] = $jobttlArr[0]['jobtitlename'];
					}
				 //to show requisition history in view
				    $candidateDetails=  $interview_model ->getReqByintrvwID($id);
					
                    $reqh_model = new Default_Model_Requisitionhistory();
	                $requisition_history = $reqh_model->getRequisitionHistoryforCandidate($candidateDetails['candidate_id']);
					
					$this->view->requisition_history =$requisition_history;
					$this->view->dataArray = $irData[0];
					$form->setDefault('interview_status',$interviewData['interview_status']);
					$form->setDefault('cand_status',$data['cand_status']);					
					$this->view->form = $form;                                                
					$this->view->data = $data;
                                        $this->view->id = $id;
					$this->view->round_count = $round_count;					
					$this->view->ermsg = '';
					if($loginuserGroup == MANAGER_GROUP || $interviewData['interview_status'] == 'Completed')
					{
						$form->removeElement('submit');
						$elements = $form->getElements();
						if(count($elements)>0)
						{
							foreach($elements as $key=>$element)
							{
								if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments"))
								{
									$element->setAttrib("disabled", "disabled");
								}
							}
						}
						if($interviewData['interview_status'] == 'Completed')
						{
							$this->view->ermsg = 'interviewcompleted';
						}
					}
					}
				}else {
					$this->view->ermsg = 'nodata';				
				}
                    }
                    else {
					$this->view->ermsg = 'nodata';				
				}
		}catch(EXception $e){
			$this->view->ermsg = 'nodata';			
		}
		$this->view->previousroundstatus = $previousroundstatus;  
		$this->view->controllername = "scheduleinterviews"; 		
    }
    public function addAction()
    {        
        $req_model = new Default_Model_Requisition();                
        $candsmodel = new Default_Model_Candidatedetails();		
        
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginUserGroup = $auth->getStorage()->read()->group_id;
        }
        $popConfigPermission = array();
        
    	if(sapp_Global::_checkprivileges(COUNTRIES,$loginUserGroup,$loginuserRole,'add') == 'Yes'){
				array_push($popConfigPermission,'country');
		}
		if(sapp_Global::_checkprivileges(STATES,$loginUserGroup,$loginuserRole,'add') == 'Yes'){
				array_push($popConfigPermission,'state');
		}
		if(sapp_Global::_checkprivileges(CITIES,$loginUserGroup,$loginuserRole,'add') == 'Yes'){
				array_push($popConfigPermission,'city');
		}
		
        $managerStr = '';
        $messages = array();  
        $inter_options = array();
        $cid=$this->getRequest()->getParam('cid');
        $id = $this->getRequest()->getParam('id');
        $callval = $this->getRequest()->getParam('call');
        if($callval == 'ajaxcall')
            $this->_helper->layout->disableLayout();

        $form = new Default_Form_Interviewrounds();   
        $form->setAction(BASE_URL.'scheduleinterviews/add');
        $form->id->setValue($id);
        $form->removeElement('cand_status');
        $form->removeElement('interview_status');
        $form->submit->setLabel('Save');
        $data = array();
        //for timezone
        $systempreferencemodel = new Default_Model_Sitepreference();
        $sitepreferencedata= $systempreferencemodel->getActiveRecord();
        if(!empty($sitepreferencedata[0]['timezone']))
        {
        	$form->timezone->setValue($sitepreferencedata[0]['timezone']);
        	$form->timezone->setAttrib("readonly", "true");
        }
        $req_data = $req_model->getReqForInterviews();
        $req_options = array();
        foreach($req_data as $req){
            $req_options[$req['id']] = $req['requisition_code'].' - '.$req['jobtitlename'];
        }
        $form->req_id->addMultiOptions(array(''=>'Select Requisition ID')+$req_options);	        
        if(count($req_options) == 0)
        {
            $messages['req_id'] = "No approved requisitions.";
            $messages['candidate_name'] = "Candidates are not added yet.";
            $messages['interviewer_id'] = "Interviewers are not added yet.";
        }
        if(count($form->country->getMultiOptions()) == 1)
        {
            $messages['country'] = "Countries are not configured yet.";
            $messages['state'] = "States are not configured yet.";
            $messages['city'] = "Cities are not configured yet.";
        }
        if(isset($_POST['req_id']) && $_POST['req_id'] !='')
        {            
            $candsData = $candsmodel->getnotscheduledcandidateData($_POST['req_id']);
            $cand_data_opt = array();
            if(count($candsData)>0)
            {
                foreach($candsData as $cand)
                {
                    $cand_data_opt[$cand['id']] = $cand['candidate_name'];
                }
            }
            $form->candidate_name->addMultiOptions(array('' => 'Select Candidate')+$cand_data_opt);            
            if(isset($req['department_id']))
            {
                $repmanData = $req_model->getReportingmanagers($loginUserGroup,$loginUserId,'',$req['department_id'],'interviewer');
                $managers_data_opt = array();
                if(!empty($repmanData))
                {					
                    foreach($repmanData as $rep)
                    {                        
                        $inter_options[] = array('id' => $rep['id'],'name' => $rep['name'],'profileimg' => $rep['profileimg']);
                    }
                                
                }
            }
            else
            {
                $managerStr = "nomanagers";
            }            
        }
        
        if(isset($_POST['country']) && $_POST['country']!='')
        {
            $statesmodel = new Default_Model_States();
            $statesmodeldata = $statesmodel->getBasicStatesList(intval($_POST['country']));
            $st_opt = array();
            if(count($statesmodeldata) > 0)
            {
                foreach($statesmodeldata as $dstate)
                {
                    $st_opt[$dstate['state_id_org']] = $dstate['state'];
                }
            }
            $form->state->addMultiOptions(array(''=>'Select State')+$st_opt);
        }
        if(isset($_POST['state']) && $_POST['state']!='')
        {
            $citiesmodel = new Default_Model_Cities();
            $citiesmodeldata = $citiesmodel->getBasicCitiesList(intval($_POST['state']));
            $ct_opt = array();
            if(count($citiesmodeldata) > 0)
            {
                foreach($citiesmodeldata as $dcity)
                {
                    $ct_opt[$dcity['city_org_id']] = $dcity['city'];
                }
            }
            $form->city->addMultiOptions(array(''=>'Select City')+$ct_opt);
        }
        if(!empty($cid))
        {
        	$candidatedetails=$candsmodel->getcandidateData($cid);
        	if($candidatedetails['requisition_id']>0)
        	{
        	$reqDetails=$req_model->getRequisitionDataById($candidatedetails['requisition_id']);
        	}
        	$form->setDefault('req_id',$candidatedetails['requisition_id']);
        	$candsData = $candsmodel->getnotscheduledcandidateData($candidatedetails['requisition_id']);
        	$cand_data_opt = array();
        	if(count($candsData)>0)
        	{
        		foreach($candsData as $cand)
        		{
        			$cand_data_opt[$cand['id']] = $cand['candidate_name'];
        		}
        	}
        	$form->candidate_name->addMultiOptions(array('' => 'Select Candidate')+$cand_data_opt);
        	$form->setDefault('candidate_name',$cid);
        	
        	
        	
        	if(isset($reqDetails['department_id']))
        	{
        		$repmanData = $req_model->getReportingmanagers($loginUserGroup,$loginUserId,'',$reqDetails['department_id'],'interviewer');
        		$managers_data_opt = array();
        		if(!empty($repmanData))
        		{
        			foreach($repmanData as $rep)
        			{
        				$inter_options[] = array('id' => $rep['id'],'name' => $rep['name'],'profileimg' => $rep['profileimg']);
        			}
        	
        		}
        	}
        	else
        	{
        		$managerStr = "nomanagers";
        	}
			if($candidatedetails['requisition_id']==0)
			{
				$repmanData = $req_model->getReportingmanagers($loginUserGroup,$loginUserId,'','','interviewer');
        		$managers_data_opt = array();
        		if(!empty($repmanData))
        		{
        			foreach($repmanData as $rep)
        			{
        				$inter_options[] = array('id' => $rep['id'],'name' => $rep['name'],'profileimg' => $rep['profileimg']);
        			}
        	
        		}
			}
        	
			
        }
                                   
        $this->view->form = $form;                                                       
        $this->view->round_count = 0;
        $this->view->messages = $messages;
        $this->view->inter_options = $inter_options;
        $this->view->popConfigPermission = $popConfigPermission;
        if($this->getRequest()->getPost())
        {
            $result = $this->save($form,$data);
            $this->view->msgarray = $result; 
            $this->view->messages = $result;
        }
    }
    /**
     * This is used to download resume in schedule interviews screen.
     */
    public function downloadresumeAction(){
		$cand_details_model = new Default_Model_Candidatedetails();
    	$result = $cand_details_model->getcandidateData($this->_getParam('id'));
    	if(!empty($result['cand_resume'])){
    		$status = array();
			$file = BASE_PATH.'/uploads/resumes/'.$result['cand_resume'];
			$status = sapp_Global::downloadFile($file);
			if(!empty($status['message'])){
				$this->_helper->FlashMessenger()->setNamespace('down_resume')->addMessage($status['message']);
			}
    	}
  		$this->_redirect('scheduleinterviews/edit/id/'.$this->_getParam('int_id'));
    }
    /**
     * This action is used for adding/updating data.
     * @parameters
     * @param $id  =  id of candidate (optional)
     * 
     * @return Zend_Form.
     */
    public function editAction()
    {    
        $cand_model = new Default_Model_Candidatedetails();  
        $requi_model = new Default_Model_Requisition();
        $interview_model = new Default_Model_Interviewdetails();
        $interview_round_model = new Default_Model_Interviewrounddetails();
        $jobtitleModel = new Default_Model_Jobtitles();
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
        }
                        
        $id = $this->getRequest()->getParam('id');
		$intId = $id;
        $callval = $this->getRequest()->getParam('call');
        if($callval == 'ajaxcall')
            $this->_helper->layout->disableLayout();
		$previousroundstatus = '';
		$cancel_name = "Cancel";
        $form = new Default_Form_Interviewrounds();   
        $form->setAction(BASE_URL.'scheduleinterviews/edit/id/'.$id);
        $form->id->setValue($id);
		
		$submitButon = $form->getElement("submit");
		$submitButon->setAttrib('style', 'display:none;');	
		
		$form->removeElement('req_id');		$form->removeElement('candidate_name');
		$form->removeElement('interviewer_id');		$form->removeElement('interview_mode');
		$form->removeElement('int_location');		$form->removeElement('country');
		$form->removeElement('state');		$form->removeElement('city');
		$form->removeElement('interview_time');		$form->removeElement('interview_date');
		$form->removeElement('interview_round');
        $data = array();$jobtitle = ''; $interviewData = array();
		try
		{
                    if($id>0 && is_numeric($id))
                    {
			$interviewData = $interview_model->getSingleInterviewData($intId);
                        
			$reqstatusArray = array('On hold','Closed','Complete');
				$previousroundstatus = $interview_model->getinterviewroundnumber($intId);
				$previousroundstatus = $previousroundstatus['round_status'];
				$data = $interview_model->getCandidateDetailsById($intId);				
                                
				if(!empty($data) && $data['interview_status'] != 'Requisition Closed/Completed' && $data['interview_status'] != 'Completed' && !in_array($data['req_status'],$reqstatusArray))
				{
				if(($data['interview_status'] == 'On hold') && ($loginuserGroup == MANAGER_GROUP || $loginuserGroup == EMPLOYEE_GROUP || $loginuserGroup == SYSTEMADMIN_GROUP ))
				   {
					$this->view->ermsg = 'nodata';	
				   }
				   else 
				   {
					$data['jobtitlename'] = '';
					$round_count = $interview_round_model->getRoundCnt($data['id'],$id);
					$jobttlArr = $jobtitleModel->getsingleJobTitleData($data['jobtitle']);
					if(!empty($jobttlArr)  && $jobttlArr != 'norows')
					{
						$jobtitle = $jobttlArr[0]['jobtitlename'];
						$data['jobtitlename'] = $jobttlArr[0]['jobtitlename'];
					}
					$irData = $this->interviewRoundsGrid($id,$interviewData['interview_status']);				
					$this->view->dataArray = $irData[0];
					$form->setDefault('interview_status',$interviewData['interview_status']);
					$cand_arr = array();
					if($interviewData['interview_status'] == 'In process')
					{
						$cand_arr['Scheduled'] = 'Scheduled';
					}
					elseif($interviewData['interview_status'] == 'Completed')
					{
						$cand_arr['Disqualified'] = 'Disqualified';
						$cand_arr['Shortlisted'] = 'Shortlisted';
					}
					elseif($interviewData['interview_status'] == 'On hold')
					{
						$cand_arr['On hold'] = 'On hold';
					}
					$form->cand_status->clearMultiOptions();
					$form->cand_status->addMultiOptions(array('' => 'Select status')+$cand_arr);
					$form->setDefault('cand_status',$data['cand_status']);					
					$this->view->form = $form;                                                
					$this->view->data = $data;
                                        $this->view->id = $id;
					$this->view->round_count = $round_count;
					if($this->getRequest()->getPost())
					{
						$result = $this->save($form,$data);
						$this->view->msgarray = $result; 
						$this->view->messages = $result;            
					}
					$this->view->ermsg = '';
					if($loginuserGroup == MANAGER_GROUP || $loginuserGroup == EMPLOYEE_GROUP || $loginuserGroup == SYSTEMADMIN_GROUP || $interviewData['interview_status'] == 'Completed')
					{
						$form->removeElement('submit');
                                                $cancel_name = "Back";
						$elements = $form->getElements();
						if(count($elements)>0)
						{
							foreach($elements as $key=>$element)
							{
								if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments"))
								{
									$element->setAttrib("disabled", "disabled");
								}
							}
						}
						if($interviewData['interview_status'] == 'Completed')
						{
							$this->view->ermsg = 'interviewcompleted';
						}
					}
					}
				}else {
                                   
					$this->view->ermsg = 'nodata';				
				}
                    }
                    else {
                                   
					$this->view->ermsg = 'nodata';				
				}
                 $this->view->interview_status = $interviewData['interview_status'];
		$this->view->previousroundstatus = $previousroundstatus;
		$this->view->loginuserGroup = $loginuserGroup;
                $this->view->cancel_name = $cancel_name;   
		}catch(EXception $e){ 
			$this->view->ermsg = 'nodata';			
		}
		
    }
    
	public function save($form,$data)
	{
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
        }	
		$cand_model = new Default_Model_Candidatedetails();  
        $requi_model = new Default_Model_Requisition();
        $interview_model = new Default_Model_Interviewdetails();
            $user_model = new Default_Model_Usermanagement();
        $interview_round_model = new Default_Model_Interviewrounddetails();

		$cand_status = $this->_getParam('cand_status',null);	
		$interview_status = $this->_getParam('interview_status',null);		
		$flag = 'true';
		if($interview_status == 'On hold' && $cand_status != 'On hold')
		{
			$msgarray['statusErr'] = 'Since the interview status is onhold, the candidate status should be onhold.';
			$flag = 'false';
		}	
		if($interview_status == 'Completed' && ($cand_status != 'Disqualified' && $cand_status != 'Shortlisted'))
		{
			$msgarray['statusErr'] = 'Since interview status is completed, the candidate status can be either disqualified or shortlisted.';
			$flag = 'false';
		}	
		if($interview_status == 'In process' && ($cand_status == 'Disqualified' || $cand_status == 'Shortlisted'))
		{
			$msgarray['statusErr'] = 'Since the interview status is in process, the candidate cannot be shortlisted or disqualified.';
			$flag = 'false';
		}	
		if($form->isValid($this->_request->getPost()) && $flag != 'false')
		{ 
			$id = $this->_getParam('id',null);
			$requisition_id = $this->_getParam('req_id',null);
			$candidate_id = $this->_getParam('candidate_name',null);			
			$interviewer_id = $this->_getParam('interviewer_id',null);
			$int_location = $this->_getParam('int_location',null);	
			$country = $this->_getParam('country',null);
			$state = $this->_getParam('state',null);
			$city = $this->_getParam('city',null);
			$interview_mode = $this->_getParam('interview_mode',null);
			$interview_time = $this->_getParam('interview_time',null);
			$interview_date = $this->_getParam('interview_date',null);	
			$interview_round = $this->_getParam('interview_round',null);
			if(!isset($candidate_id))  $candidate_id = $data['id']; 	
			
			if(empty($data)){ 			
			    $getExistingCandidateRecord = $interview_model->getCandidateInInterviewProcess(trim($candidate_id));
			 
			    if($getExistingCandidateRecord > 0){
			       $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Interview already scheduled for this candidate.');
			       $this->_redirect('/scheduleinterviews');	 
			    }			
			}

			
			if(empty($data))
			{		
				$idata = array(
					'req_id' => $requisition_id,
					'candidate_id' => trim($candidate_id),
					'interview_status' => trim($interview_status),				
					'isactive' => 1,
					'createdby' => trim($loginUserId),
					'modifiedby' => trim($loginUserId),
					'createddate' => gmdate("Y-m-d H:i:s"),
					'modifieddate' => gmdate("Y-m-d H:i:s"),
				);
				$idata['interview_status'] = 'In process';  
				$iwhere = "";
				$actionflag = 1;
				
				$iresult = $interview_model->SaveorUpdateInterviewData($idata, $iwhere);
				
			//update requisition_id in history table for not applicable candidates
			  $req_model = new Default_Model_Requisition();
			  $cand_model = new Default_Model_Candidatedetails(); 
			   $requisition_data= $req_model ->getRequisitionDataById($requisition_id);
			   $candidateDetails = $cand_model->getcandidateData($candidate_id);
			   //checking for not applicable candidate
			  if( $candidateDetails['requisition_id']== 0){
					if(!empty($candidateDetails))
					{
						if($candidateDetails['requisition_id']!= 0)
						{
						  $candidateData = $cand_model->getCandidateForView($candidate_id);
						}
						else
						{
						   $candidateData = $cand_model->getViewforNotapplicableCandidates($candidate_id);
						}
					}
					$chdata = array(
					        'createdby' =>trim($loginUserId),
							'modifiedby' => trim($loginUserId),
							'requisition_id'=>$requisition_id,		
							
					);	
					$where = "candidate_id = ".$candidate_id;
					$reqh_model = new Default_Model_Requisitionhistory();
					$history=$reqh_model->saveOrUpdateRequisitionHistory($chdata,$where);
					// to insert new recored
					$ch_data = array(
					        'createdby' =>trim($loginUserId),
							'modifiedby' => trim($loginUserId),
							'candidate_id' => trim($candidate_id),
							'candidate_name' => $candidateData["candidate_name"],
							'requisition_id'=>$requisition_id,		
							'createddate' => gmdate("Y-m-d H:i:s"),
					        'modifieddate' => gmdate("Y-m-d H:i:s"),
							'description' => 'Candidate:'.$candidateData["candidate_name"].' has been added to Requisition:'.$requisition_data['requisition_code'].' by ',
					);	
					$iwhere="";
					$history=$reqh_model->saveOrUpdateRequisitionHistory($ch_data,$iwhere);
			  }
			//end history update
			
			
			//for Scheduleinterview history
				if($iresult != 'update')
				{ 
				    $req_model = new Default_Model_Requisition();
					$cand_model = new Default_Model_Candidatedetails(); 
					
					
					$candidateDetails = $cand_model->getcandidateData($candidate_id);
					if(!empty($candidateDetails))
					{
						if($candidateDetails['requisition_id']!= 0)
						{
						  $candidateData = $cand_model->getCandidateForView($candidate_id);
						}
						else
						{
						   $candidateData = $cand_model->getViewforNotapplicableCandidates($candidate_id);
						}
					}
				
					$req_data= $req_model ->getRequisitionDataById($requisition_id);
					
					$history = 'Candidate:'.$candidateData["candidate_name"].' has been scheduled for an interview by ';
                    $createdby = $loginUserId;
					$modifiedby =$loginUserId;
					
					$reqh_model = new Default_Model_Requisitionhistory();
					$requisition_history = array(
                                        'candidate_id' =>$candidate_id,
                                         'interview_id' =>$iresult,										
                                        'candidate_name'=>  $candidateData['candidate_name'],	
										'requisition_id' =>$requisition_id,
										'description' => $history,
										'createdby' => $createdby,
										'modifiedby' => $modifiedby,
										'isactive' => 1,
										'createddate' =>gmdate("Y-m-d H:i:s"),
										'modifieddate'=> gmdate("Y-m-d H:i:s"),
									);
					$where = '';
					$historyId = $reqh_model->saveOrUpdateRequisitionHistory($requisition_history,$where); 
				}
		      
			//history end
		
				if($id == '')
                                {
                                    $tableid = $iresult;
                                   
                                }
				
				if($iresult != '')
				{
					
					$irdata = array(
							'interview_id' => $iresult,
							'req_id' => $requisition_id,
							'candidate_id' => $candidate_id,
							'interviewer_id' => $interviewer_id,
							'interview_time' => sapp_Global::change_time(trim($interview_time),'database'),
							'interview_date' => sapp_Global::change_date($interview_date, 'database'),
							'interview_mode' => $interview_mode,
							'interview_round_number' => 1,
							'interview_round' => trim($interview_round),
							'int_location' => trim($int_location),
							'int_country' => trim(intval($country)),
							'int_state'	=>trim(intval($state)),
							'int_city'	=>trim(intval($city)),					
							'isactive' => 1,
							'createdby' => trim($loginUserId),
							'modifiedby' => trim($loginUserId),
							'createddate' => gmdate("Y-m-d H:i:s"),
							'modifieddate' => gmdate("Y-m-d H:i:s"),
					);				
					$ir_result = $interview_round_model->SaveorUpdateInterviewroundData($irdata,'');
                                        $requisition_data = $requi_model->getRequisitionDataById($requisition_id);  
                                        $cand_data = $cand_model->getCandidateById($candidate_id);
                                        $report_person_data = $user_model->getUserDataById($interviewer_id);
                                        $mail_arr = array(  'HR'=>  defined('REQ_HR_'.$requisition_data['businessunit_id'])?constant('REQ_HR_'.$requisition_data['businessunit_id']):"",                                                        
                                                            $report_person_data['userfullname'] => $report_person_data['emailaddress']
                                            );
                                        $systempreferencemodel = new Default_Model_Sitepreference();
                                        $sitepreferencedata= $systempreferencemodel->getActiveRecord();
                                        if(!empty($sitepreferencedata[0]['timezone']))
                                        {
                                        	$timezone=$sitepreferencedata[0]['timezone'];
                                        
                                        }
                                        foreach($mail_arr as $ename => $email)
                                        {
                                            $base_url = 'http://'.$this->getRequest()->getHttpHost() . $this->getRequest()->getBaseUrl();
                                            $view = $this->getHelper('ViewRenderer')->view;
                                            $this->view->emp_name = $ename;                           
                                            $this->view->base_url=$base_url;
                                            $this->view->candidate_name = $cand_data['candidate_name'];
                                            $this->view->interview_type = $interview_mode;
                                            $this->view->interview_location = $int_location;
                                            $this->view->interview_date = $interview_date;
                                            $this->view->interview_time = sapp_Global::change_time($interview_time,'view');
                                            $this->view->requisition_code = $requisition_data['requisition_code'];
                                            $this->view->timezone = $timezone;
                                            $text = $view->render('mailtemplates/interviewrounds.phtml');
                                            $options['subject'] = APPLICATION_NAME.': Interview schedule';
                                            $options['header'] = 'Interview schedule';
                                            $options['toEmail'] = $email;  
                                            $options['toName'] = $ename;
                                            $options['message'] = $text;
                                            $options['cron'] = 'yes';
                                            sapp_Global::_sendEmail($options);
                                        }
					$candData = array(
							 'requisition_id'=>$requisition_id,
							'cand_status'=> 'Scheduled',						
							'modifiedby' => trim($loginUserId),
							'modifieddate' => gmdate("Y-m-d H:i:s")
					);				
					$where = "id = ".$candidate_id;
					
					$candResult = $cand_model->SaveorUpdateCandidateData($candData,$where);
					
				}
			}
			else
			{
				
				$idata = array(
					'interview_status' => trim($interview_status),				
					'isactive' => 1,
					'modifiedby' => trim($loginUserId),
					'modifieddate' => gmdate("Y-m-d H:i:s"),
				);
				$iwhere = "id = ".$id;
				$tableid = $id;
				$actionflag = 2;
				$iresult = $interview_model->SaveorUpdateInterviewData($idata, $iwhere);
				$candData = array(
						'cand_status'=> 'Scheduled',		
						'requisition_id'=>$data['req_id'],
						'modifiedby' => trim($loginUserId),
						'modifieddate' => gmdate("Y-m-d H:i:s")
				);
				
				if($cand_status && $cand_status != '0') $candData['cand_status'] = $cand_status;
				$where = "id = ".$candidate_id;
				
				$candResult = $cand_model->SaveorUpdateCandidateData($candData,$where);
			}
	 
			$menumodel = new Default_Model_Menu();
			$objidArr = $menumodel->getMenuObjID('/scheduleinterviews');
			$objID = $objidArr[0]['id'];
			$result = sapp_Global::logManager($objID,$actionflag,$loginUserId,$tableid);
                        if($id == '')
                            $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Interview scheduled successfully.'); 
                        else
                            $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Interview details updated successfully.'); 
			$this->_redirect('/scheduleinterviews');			
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
	
    public function interviewRoundsGrid($id,$int_status='')
    {	
            $interview_round_model = new Default_Model_Interviewrounddetails();
            $dashboardcall = $this->_getParam('dashboardcall');
			if($dashboardcall == 'Yes')
				$perPage = DASHBOARD_PERPAGE;
			else	
				$perPage = PERPAGE;
			$sort = 'ASC';$by = 'ir.interview_round_number';$pageNo = 1;$searchData = '';$searchArray=array();
            $objName = 'interviewrounds';
            $actns = '';
			if($int_status == 'Completed')
			{
				$actns = 'remove';
				 $tableFields = array('interview_round_number'=>'Interview Round','interview_round'=>'Interview Name','userfullname' => 'Interviewer','interview_date' =>'Interview Date','round_status'=>'Round Status');
            }else{
				 $tableFields = array('action'=>'Action','interview_round_number'=>'Interview Round','interview_round'=>'Interview Name','userfullname' => 'Interviewer','interview_date' =>'Interview Date','round_status'=>'Round Status');
			}
			$tablecontent = $interview_round_model->getInterviewRoundsData($sort, $by, $pageNo, $perPage,'',$id);     
            $data = array();
            $round_arr = array( '' => 'All','Schedule for next round' => 'Schedule for next round',
                                'Qualified' => 'Qualified','Selected' => 'Selected','Disqualified' => 'Disqualified',
                                'Decision pending' => 'Decision pending','On hold' => 'On hold',
                                'Incompetent' => 'Incompetent','Ineligible' => 'Ineligible',
                                'Candidate no show' => 'Candidate no show','Requisition Closed/Completed' => 'Requisition Closed/Completed'); 
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
                    'menuName' => 'Interview Rounds',
                    'add' => 'add',
                    'actns' => $actns,
                    'call'=>'',
					'dashboardcall'=>$dashboardcall,
                    'search_filters' => array(
                        'interview_date' =>array('type'=>'datepicker'),
                        'round_status' => array(
                            'type' => 'select',
                            'filter_data' => $round_arr,
                        ),
            ),
            );		
            array_push($data,$dataTmp);
            return $data;
    }
    /**
     * This action is called on onchange of requisition select box in schedule interviews.
     */
    public function getcandidatesAction()
    {
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $loginUserGroup = $auth->getStorage()->read()->group_id;
        }	
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('getcandidates', 'json')->initContext();
        $req_model = new Default_Model_Requisition();
        $req_id = $this->_request->getParam('req_id');		
                
        $cdata = $req_model->getcandidates_forinterview($req_id, $loginUserGroup, $loginUserId);
		
        $this->_helper->json($cdata);				
    }
    
    public function deleteAction()
	{	
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
				$loginUserId = $auth->getStorage()->read()->id;
			}
		 $id = $this->_request->getParam('objid');
		 $deleteflag= $this->_request->getParam('deleteflag');
		 $messages['message'] = '';$messages['msgtype'] = '';
		 $actionflag = 3;
		 $flag = 1;
		if($id)
		{
		    $interview_model = new Default_Model_Interviewdetails();
			$round_model = new Default_Model_Interviewrounddetails();
			$cand_model = new Default_Model_Candidatedetails(); 
			$candidateId = $interview_model->getCandidateIdById($id);
			if($candidateId){
				$datacand = array('cand_status'=>'Not Scheduled');
				$wherecand = array('id=?'=>$candidateId);
				$Idcand = $cand_model->SaveorUpdateCandidateData($datacand, $wherecand);
				if($Idcand == 'update'){
				  $data = array('isactive'=>0);
			      $where = array('id=?'=>$id);
			      $Id = $interview_model->SaveorUpdateInterviewData($data, $where);		

					if($Id == 'update')
					{
						$menuID = SCHEDULEINTERVIEWS;
						$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id); 
					    $messages['message'] = 'Schedule Interview deleted successfully.';
						$messages['msgtype'] = 'success';
						//$messages['flagtype'] = 'process';
					}else
					{
					    $flag = 0;
					}
				}else
				 { 
				   $flag = 0;
				 }
			
			}else
			 { 
			    $flag = 0;
			 }		
		}else
		{ 
		     $flag = 0;
		}
	
		if($flag == 0){
		   $messages['message'] = 'This schedule interview cannot be deleted';
			$messages['msgtype'] = 'error';
			$messages['flagtype'] = 'process';
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
	
}

