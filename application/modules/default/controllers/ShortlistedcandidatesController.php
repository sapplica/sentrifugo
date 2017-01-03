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

class Default_ShortlistedcandidatesController extends Zend_Controller_Action
{

    private $_options;
    public function preDispatch()
    {	
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
    }
	
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();		
    }
	
	public function indexAction()
	{
		$candidatesmodel = new Default_Model_Shortlistedcandidates();	
        $call = $this->_getParam('call');
        if($call == 'ajaxcall')
            $this->_helper->layout->disableLayout();
		
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		} 
		$dashboardcall = $this->_getParam('dashboardcall');
		$queryflag = '';
        $refresh = $this->_getParam('refresh');
        $data = array();
        $searchQuery = '';
        $searchArray = array();
        $tablecontent='';
		
		
		if($call == 'ajaxcall')
				$this->_helper->layout->disableLayout();
		
		$view = Zend_Layout::getMvcInstance()->getView();
		$statusidstring = $this->_request->getParam('status');
		$unitId = '';
		if(!isset($statusidstring) || $statusidstring=='')
		{
			$unitId = $this->_request->getParam('unitId');
			$statusidstring = $unitId;
		}
		
		$formgrid = 'false';
		if(isset($unitId) && $unitId != '') $formgrid = 'true';                
		$statusid =  sapp_Global::_decrypt($statusidstring);
                $queryflag = '';
		unset($_SESSION['short_status']);
				if($statusid !='' && is_numeric($statusid))
				{
                    $_SESSION['short_status'] = $statusidstring;
					if($statusid == 0)
						$queryflag = 'All';
					else if($statusid == 2)
						$queryflag = 'Selected';
					else if($statusid == 3)
						$queryflag = 'Rejected';  
					else if($statusid == 1)
					$queryflag = 'Shortlisted';             
                }
                else
                {
                    $queryflag = 'All';
                    $statusid = 0;
                    $statusidstring = sapp_Global::_encrypt('0');
                }
			
		
        if($refresh == 'refresh')
        {
			if($dashboardcall == 'Yes')
				$perPage = DASHBOARD_PERPAGE;
			else	
				$perPage = PERPAGE;
            $sort = 'DESC';$by = 'c.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';
            $searchArray = array();
        }
        else 
        {
            $sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
            $by = ($this->_getParam('by')!='')? $this->_getParam('by'):'c.modifieddate';
            if($dashboardcall == 'Yes')
				$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
			else $perPage = $this->_getParam('per_page',PERPAGE);
            $pageNo = $this->_getParam('page', 1);
            $searchData = $this->_getParam('searchData');	           
        }
		
		$allcandidates=$candidatesmodel->getEmployeeCount('All');
		$allcandidatescount= $allcandidates['count'];
	
        $shortlistcandidates=$candidatesmodel->getEmployeeCount('Shortlisted');
		$shortlistcandidatescount= $shortlistcandidates['count'];
		
		
	    $selectedcandidates=$candidatesmodel->getEmployeeCount('Selected');
		$selectedcandidatescount= $selectedcandidates['count'];
		 
		 
	    $rejectedcandidates=$candidatesmodel->getEmployeeCount('Rejected');
		$rejectedcandidatescount= $rejectedcandidates['count'];
		
		
        $dataTmp = $candidatesmodel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall,$queryflag,$statusidstring,$formgrid);
        
        array_push($data,$dataTmp);
        $this->view->dataArray = $dataTmp;
        $this->view->call = $call ;
		$this->view->statusidstring = $statusidstring;
		$this->view->rejectedcandidatescount = $rejectedcandidatescount;
		$this->view->selectedcandidatescount = $selectedcandidatescount;
		$this->view->shortlistcandidatescount = $shortlistcandidatescount;
		$this->view->allcandidatescount = $allcandidatescount;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
	}
	
	public function editAction()
	{		
		$id = $this->getRequest()->getParam('id');
		$candidatesmodel = new Default_Model_Shortlistedcandidates();
		$requi_model = new Default_Model_Requisition();	
		$jobtitleModel = new Default_Model_Jobtitles();
		$form = new Default_Form_shortlistedcandidates();
		$form->setAttrib('action',BASE_URL.'shortlistedcandidates/edit/id/'.$id);
		$intrvwroundsData = array();$intrvwData = array();$cand_status = '';
		$requisitionData = array();$jobtitle = '';$requisitionData['jobtitlename'] = '';
                $cancel_name = 'Cancel';
		try{
			$candidateData = $candidatesmodel->getcandidateData($id);			
			$cand_status = $candidateData['cand_status'];
			$req_id = $candidateData['requisition_id'];
			try{	
				$requisitionData = $requi_model->getRequisitionDataById($req_id);
				$requisitionData['jobtitlename'] = '';
				$jobttlArr = $jobtitleModel->getsingleJobTitleData($requisitionData['jobtitle']);
				if(!empty($jobttlArr) && $jobttlArr != 'norows')
				{
					$jobtitle = $jobttlArr[0]['jobtitlename'];
					$requisitionData['jobtitlename'] = $jobttlArr[0]['jobtitlename'];
				}
				
				$intrvwData = $candidatesmodel->getinterviewData($requisitionData['id'],$candidateData['id']);								
				if(!empty($intrvwData) && ($cand_status == 'Shortlisted' || $cand_status == 'Selected' || $cand_status == 'Rejected'))
				{
					$intrvwroundsData = $candidatesmodel->getinterviewrounds($intrvwData['id'],$requisitionData['id'],$candidateData['id']);										
					if($cand_status == 'Selected'){
						$form->setDefault('selectionstatus','2');
						$form->selectionstatus->setAttrib("disabled", "disabled");
						$form->removeElement('submit');
                                                $cancel_name = 'Back';
					}else if($cand_status == 'Rejected'){
						$form->setDefault('selectionstatus','3');
						$form->selectionstatus->setAttrib("disabled", "disabled");
						$form->removeElement('submit');
                                                $cancel_name = 'Back';
					}
					$this->view->form = $form;
					$this->view->candidateData = $candidateData;
					$this->view->requisitionData = $requisitionData;
					$this->view->intrvwrounds = $intrvwroundsData;
					$this->view->intrvwData = $intrvwData;
                                        $this->view->cancel_name = $cancel_name;
					if($this->getRequest()->getPost())
					{
						$result = $this->save($form);							
						$this->view->msgarray = $result; 
						$this->view->messages = $result;	
					}
					$this->view->ermsg = '';
				}else{
					$this->view->ermsg = 'nodata';	
				}	
				$this->view->canstatus = $cand_status;
			}catch(Exception $e){
				$this->view->ermsg = 'nodata';
			}			
		}catch(Exception $e){
			$this->view->ermsg = 'nodata';
		}		
	}
	
	public function viewAction()
	{		
		$id = $this->getRequest()->getParam('id');
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		} 
		$candidatesmodel = new Default_Model_Shortlistedcandidates();
		$requi_model = new Default_Model_Requisition();		
		$jobtitleModel = new Default_Model_Jobtitles();		
		$intrvwroundsData = array();$intrvwData = array();
		$jobtitle = '';
		try{
			$candidateData = $candidatesmodel->getcandidateData($id);			
			$cand_status = $candidateData['cand_status'];				
			$req_id = $candidateData['requisition_id'];
			//to show requisition history in view
			$reqh_model = new Default_Model_Requisitionhistory();
			$requisition_history = $reqh_model->getRequisitionHistoryforCandidate($id);
			
			try{			
				$requisitionData = $requi_model->getRequisitionDataById($req_id);				
				$requisitionData['jobtitlename'] = '';
				$jobttlArr = $jobtitleModel->getsingleJobTitleData($requisitionData['jobtitle']);
				if(!empty($jobttlArr) && $jobttlArr != 'norows')
				{
					$jobtitle = $jobttlArr[0]['jobtitlename'];
					$requisitionData['jobtitlename'] = $jobttlArr[0]['jobtitlename'];
				}
				
				$intrvwData = $candidatesmodel->getinterviewData($requisitionData['id'],$candidateData['id']);				
				if(!empty($intrvwData) && ($cand_status == 'Shortlisted' || $cand_status == 'Selected' || $cand_status == 'Rejected'))
				{
					$intrvwroundsData = $candidatesmodel->getinterviewrounds($intrvwData['id'],$requisitionData['id'],$candidateData['id']);					
					$this->view->candidateData = $candidateData;
					$this->view->requisitionData = $requisitionData;
					$this->view->intrvwrounds = $intrvwroundsData;
					$this->view->intrvwData = $intrvwData;				
					$this->view->ermsg = '';
					$this->view->id = $id;
					$this->view->requisition_history=$requisition_history;
				}else{
					$this->view->ermsg = 'nodata';	
				}				
			}catch(Exception $e){
				$this->view->ermsg = 'nodata';		
			}			
		}catch(Exception $e){
			$this->view->ermsg = 'nodata';	
                        
		}	
		$this->view->loginuserGroup = $loginuserGroup;
		$this->view->controllername = "shortlistedcandidates";
	}
	
	public function addAction()
	{
	}
	
	public function save($form)
	{
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		} 
		$candidatesmodel = new Default_Model_Shortlistedcandidates();
                $cand_model = new Default_Model_Candidatedetails();
		$requimodel = new Default_Model_Requisition();
                $user_model = new Default_Model_Usermanagement();
		if($form->isValid($this->_request->getPost()))
		{
			$id = $this->getRequest()->getParam('id');
			$status = $this->getRequest()->getParam('selectionstatus');
			
			$candidateData = $candidatesmodel->getcandidateData($id);
			$req_id = $candidateData['requisition_id'];			
			$data = array(
                                        'cand_status' => trim($status),
                                        'modifiedby' => trim($loginUserId),						
                                        'modifieddate' => gmdate("Y-m-d H:i:s")
					);
			$where = "id = ".$id;			
			$result = $candidatesmodel->SaveorUpdateCandidateDetails($data,$where);
			 //for candidate history
			 
				if($result == 'update')
				{ 
			         if($status ==2)
					 {
						 $statustext ='Selected';
					 }
					 if($status ==3)
					 {
						 $statustext ='Rejected';
					 }
			       	$candidateData = $candidatesmodel->getcandidateData($id);

					$history = 'Candidate:'.$candidateData["candidate_name"].' has been '.$statustext.' by ';
                    $createdby = $loginUserId;
					$modifiedby = $loginUserId;
					
					 $reqh_model = new Default_Model_Requisitionhistory();
					$requisition_history = array(
                                        'candidate_id' =>$id,	
                                        'candidate_name'=>  $candidateData['candidate_name'],	
										'requisition_id' =>$candidateData['requisition_id'],
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
			
			if(($status == '2' || $status == 'Selected' ) && ($result != ''))
			{	
                            //increment selected members count
			    $requimodel->incrementselected_members($req_id);
                            //start of mailing
                            $cand_data = $cand_model->getCandidateById($id);
                            $requisition_data = $requimodel->getRequisitionDataById($req_id);                            
                            $report_person_data = $user_model->getUserDataById($requisition_data['reporting_id']);
                            $mail_arr = array(  'HR'=>  defined('REQ_HR_'.$requisition_data['businessunit_id'])?constant('REQ_HR_'.$requisition_data['businessunit_id']):"",
                                                 'Management'=>defined("REQ_MGMT_".$requisition_data['businessunit_id'])?constant("REQ_MGMT_".$requisition_data['businessunit_id']):"",
                                                $report_person_data['userfullname'] => $report_person_data['emailaddress']
                                );
                            $cstat_arr = array(		
							'0'	=>	'Select status',
							'2'	=>	'Selected' ,
							'3'	=>	'Rejected'
							);
							
                            foreach($mail_arr as $ename => $email)
                            {
                                $base_url = 'http://'.$this->getRequest()->getHttpHost() . $this->getRequest()->getBaseUrl();
                                $view = $this->getHelper('ViewRenderer')->view;
                                $this->view->emp_name = $ename;                           
                                $this->view->base_url=$base_url;
                                $this->view->requisition_code = $requisition_data['requisition_code'];
                                $this->view->candidate_name = $cand_data['candidate_name'];
                                $this->view->status = $cstat_arr[$status];
                                $text = $view->render('mailtemplates/selectedcandidates.phtml');
                                $options['subject'] = APPLICATION_NAME.': Candidate '.$cstat_arr[$status];
                                $options['header'] = 'Candidate '.$cstat_arr[$status];
                                $options['toEmail'] = $email;  
                                $options['toName'] = $ename;
                                $options['message'] = $text;
                                $options['cron'] = 'yes';
                                
                                sapp_Global::_sendEmail($options);
                            }
                            //end of mailing     
			}
			
			
			
			$actionflag = 2;
			$tableid = $id;
			$menumodel = new Default_Model_Menu();
			$objidArr = $menumodel->getMenuObjID('/shortlistedcandidates');
			$objID = $objidArr[0]['id'];
			$result = sapp_Global::logManager($objID,$actionflag,$loginUserId,$tableid);
			$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Requisition updated successfully."));
			$this->_redirect('/shortlistedcandidates');		
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
	
	public function deleteAction()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
        }
        $id = $this->_request->getParam('objid');
        $messages['message'] = '';
        $actionflag = 3;
        if($id)
        {
            $candidatesmodel = new Default_Model_Shortlistedcandidates();
            $data = array('isactive'=>0);
            $where = array('id=?'=>$id);
			$Id = $candidatesmodel->SaveorUpdateCandidateDetails($data,$where);
            if($Id == 'update')
            {
				$menuID = SHORTLISTEDCANDIDATES;
                sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);  
                $messages['message'] = 'Record deleted successfully.';
				$messages['msgtype'] = 'success';
            }   
            else{
               $messages['message'] = 'Record cannot be deleted.';				
			   $messages['msgtype'] = 'error';
			}
        }
        else
        { 
            $messages['message'] = 'Record cannot be deleted.';
			$messages['msgtype'] = 'error';
        }
        $this->_helper->json($messages);
    }

}