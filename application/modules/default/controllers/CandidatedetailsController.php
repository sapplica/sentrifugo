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

class Default_CandidatedetailsController extends Zend_Controller_Action
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
        $ajaxContext->addActionContext('chkcandidate', 'json')->initContext();
        $ajaxContext->addActionContext('getvendors', 'json')->initContext();
    }
	
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();		
    }

    public function indexAction()
    {
        $cand_model = new Default_Model_Candidatedetails();	
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
            /** search from grid - START **/
            $searchData = $this->_getParam('searchData');	
            
            /** search from grid - END **/
        }
        $dataTmp = $cand_model->getGrid($sort, $by, $perPage, $pageNo, $searchData, $call,$dashboardcall,'','','','');
        			
        array_push($data,$dataTmp);
        $this->view->dataArray = $dataTmp;
        $this->view->call = $call ;
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
    }

    public function viewAction()
    {	                        
        $cand_model = new Default_Model_Candidatedetails();
        $candwork_model = new Default_Model_Candidateworkdetails();        
        
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $login_group_id = $auth->getStorage()->read()->group_id;
            $login_role_id = $auth->getStorage()->read()->emprole;
        }
        $id = $this->getRequest()->getParam('id');    
	                
        try
        {
            $candidateDetails = $cand_model->getcandidateData($id);
			if(!empty($candidateDetails))
			{
				if($candidateDetails['requisition_id']!= 0)
				{
				  $candidateData = $cand_model->getCandidateForView($id);
				}
				else
				{
				   $candidateData = $cand_model->getViewforNotapplicableCandidates($id);
				}
			}
		
            if(!empty($candidateData))
            {
                try
                {
                    $candidateworkData = $candwork_model->getcandidateworkData($id);	                    
                    $previ_data = sapp_Global::_checkprivileges(CANDIDATEDETAILS,$login_group_id,$login_role_id,'edit');
                    //to show candidate history in view
                    $reqh_model = new Default_Model_Requisitionhistory();
	                $requisition_history = $reqh_model->getRequisitionHistoryforCandidate($id);
	                if($candidateData['source']=="Vendor"  )
	                {
	                	$vendorsmodel= new Default_Model_Vendors();
	                	$vendorsdataArr=$vendorsmodel->getsingleVendorsData($candidateData['source_val']);
	                	$this->view->vendorname=$vendorsdataArr['name'];
	                }
                    $this->view->previ_data = $previ_data;
                    $this->view->workdata = $candidateworkData;
                    $this->view->ermsg = ''; 
                    $this->view->cdata = $candidateData;

                    $objName = 'candidatedetails';
                    $this->view->id = $id;
					 $this->view->requisition_history=  $requisition_history;
                    $this->view->controllername = $objName;
					 $this->view->req_id=$candidateDetails['requisition_id'];
                }
                catch(Exception $e)
                {
                    $this->view->nodata = 'nodata';
                }
            }
            else 
            {
                $this->view->nodata = 'nodata';
            }
        }
        catch(Exception $e)
        {
            $this->view->nodata = 'nodata';
        }
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
        $req_model = new Default_Model_Requisition();  
		$jobtitleModel = new Default_Model_Jobtitles();		
        $cand_model = new Default_Model_Candidatedetails();
        $candwork_model = new Default_Model_Candidateworkdetails();
        $country_model = new Default_Model_Countries();
        $role_model = new Default_Model_Roles();
        $auth = Zend_Auth::getInstance();
        $data = array();$jobtitle = '';
     //   $req_data['jobtitlename'] = '';
        $popConfigPermission = array();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
        }
        
        if(sapp_Global::_checkprivileges(COUNTRIES,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
				array_push($popConfigPermission,'country');
		}
		if(sapp_Global::_checkprivileges(STATES,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
				array_push($popConfigPermission,'state');
		}
		if(sapp_Global::_checkprivileges(CITIES,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
				array_push($popConfigPermission,'city');
		}
		
        $id = trim($this->getRequest()->getParam('id'));
        if(is_numeric($id) && $id > 0)
		{
			try
			{			
			$candidateData = $cand_model->getcandidateData($id);  
			$form = new Default_Form_Candidatedetails($candidateData['requisition_id']);   
			$form->setAction(BASE_URL.'candidatedetails/edit/id/'.$id);		
			$statsflag = 'false';       		
				if(count($candidateData)>0)
				{
					try
					{        
						$candidateworkData = $candwork_model->getcandidateworkData($id);    
						if(($candidateData['requisition_id'])>0)
						{
						$req_data = $req_model->getRequisitionDataById($candidateData['requisition_id']);
						}
						else 
						{
							$req_data=array();
						}
						
						$form->source->setValue($candidateData['source']);
						
						if($candidateData['source']=="Website" )
						{
							
							$form->referalwebsite->setValue($candidateData['source_val']);
						}
						if($candidateData['source']=="Referal"  )
						{
							$form->referal->setValue($candidateData['source_val']);
						}
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
						// To show it on edit view
						$data['cand_resume'] = (isset($candidateData['cand_resume']))?$candidateData['cand_resume']:NULL;
						$data['rec_id'] = (isset($id))?$id:NULL;
						$data['selected_option'] = $this->_getParam('selected_option');
						if(count($req_data)>0)
						{
							$req_data['jobtitlename']  = '';
							$jobttlArr = $jobtitleModel->getsingleJobTitleData($req_data['jobtitle']);
							if(!empty($jobttlArr) && $jobttlArr != 'norows')
							{
								$jobtitle = $jobttlArr[0]['jobtitlename'];
								$req_data['jobtitlename'] = $jobttlArr[0]['jobtitlename'];
							}
							
							if(($req_data['req_status'] == 'Closed' || $req_data['req_status'] == 'On hold' || $req_data['req_status'] == 'Complete') && ($candidateData['cand_status'] == 'Requisition Closed/Completed' || $candidateData['cand_status'] == 'On hold' || $candidateData['cand_status'] == 'Not Scheduled')) //|| $candidateData['cand_status'] == 'Rejected' || $candidateData['cand_status'] == 'Disqualified'
							{
								$statsflag = 'true';
								$reqforcv_data = $req_model->getRequisitionsForCV("'Approved','In process'");
								$req_options = array();
								foreach($reqforcv_data as $req){
									$req_options[$req['id']] = $req['requisition_code'].' - '.$req['jobtitlename'];
								}
								$form->requisition_id->addMultiOptions(array(''=>'Select Requisition ID')+$req_options);
								
								$form->cand_status->addMultiOption('','Select Status');		
								$form->cand_status->addMultiOption('Not Scheduled','Not Scheduled');						
							}
							$data['requisition_code'] = $req_data['requisition_code'];
							$data['requisition_id'] = $req_data['id'];
							$data['jobtitlename'] = $req_data['jobtitlename'];
						}
						
						$countryId = $candidateData['country'];
                                                if(isset($_POST['country']))
                                                {
                                                    $countryId = $_POST['country'];
                                                }
                                                $stateId = $candidateData['state'];
                                                if(isset($_POST['state']))
                                                {
                                                    $stateId = $_POST['state'];
                                                }
                                                $cityId = $candidateData['city'];
                                                if(isset($_POST['city']))
                                                {
                                                    $cityId = $_POST['city'];
                                                }
						if($countryId != '')
						{
							   $statesmodel = new Default_Model_States();
                               $statesData = $statesmodel->getStatesList($countryId);
                                 foreach($statesData as $res) 
                                    $form->state->addMultiOption($res['id'],utf8_encode($res['state_name']));
                               $form->setDefault('country',$countryId);
						}
                        if($stateId != '')
						{
                                $citiesmodel = new Default_Model_Cities();
                                $citiesData = $citiesmodel->getCitiesList($stateId);
                                  foreach($citiesData as $res) 
 	                                 $form->city->addMultiOption($res['id'],utf8_encode($res['city_name']));
                                $form->setDefault('state',$stateId);
    	                }
                        $form->setDefault('city',$cityId);		
						$form->setDefault('job_title',$jobtitle);
						$countrieslistArr = $country_model->getTotalCountriesList();
						if(sizeof($countrieslistArr)>0)
						{
							$form->country->addMultiOption('','Select Country');
							foreach($countrieslistArr as $countrieslistres)
							{
								$form->country->addMultiOption($countrieslistres['id'],utf8_encode($countrieslistres['country_name']) );
							}
						}
						else
						{
							$msgarray['country'] = 'Countries are not configured yet.';
						}
					
					
						if($id)
						{
							$form->submit->setLabel('Update');   
                            $form->savesubmit->setLabel('Update and schedule');   							
				
							foreach($candidateData as $key=>$val)
							{
								$candidateData[$key] = htmlentities(addslashes($val), ENT_QUOTES, "UTF-8");
							}	
							$form->populate($candidateworkData);
							$form->populate($candidateData);
						}
						
						$this->view->candidate_data = $candidateData;
						$this->view->form = $form; 
						$this->view->workdata = $candidateworkData;
						$this->view->data = $data;
						$this->view->popConfigPermission = $popConfigPermission;
						$this->view->statsflag = $statsflag;
						$this->view->ermsg = ''; 
						if($this->getRequest()->getPost())
						{
							$result = $this->save($form);
							$this->view->msgarray = $result; 
							$this->view->messages = $result;	
						}
					}
					catch(Exception $e)
					{
						$this->view->ermsg = 'nodata';
					}
				}
				else
				{
					$this->view->ermsg = 'nodata';
				}
			}
			catch(Exception $e)
			{
				$this->view->ermsg = 'nodata';
			} 
		}else{
			 $this->view->ermsg = 'nodata';
		}
    }
    // add candidate in model window in schedule interview
    public function addpopupAction()
    {
    	Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
    	$req_model = new Default_Model_Requisition();
    	$cand_model = new Default_Model_Candidatedetails();
    	$cand_work_model = new Default_Model_Candidateworkdetails();
    	$countriesModel = new Default_Model_Countries();
    	$controllername = 'candidatedetails';
    	$auth = Zend_Auth::getInstance();
    	$msgarray = array();
    	$popConfigPermission = array();
    	if($auth->hasIdentity()){
    		$loginUserId = $auth->getStorage()->read()->id;
    		$loginuserRole = $auth->getStorage()->read()->emprole;
    		$loginuserGroup = $auth->getStorage()->read()->group_id;
    	}
    	
    	if(sapp_Global::_checkprivileges(COUNTRIES,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
    		array_push($popConfigPermission,'country');
    	}
    	if(sapp_Global::_checkprivileges(STATES,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
    		array_push($popConfigPermission,'state');
    	}
    	if(sapp_Global::_checkprivileges(CITIES,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
    		array_push($popConfigPermission,'city');
    	}
    	
    	$req_data = $req_model->getRequisitionsForCV("'Approved','In process'");
    	$req_options = array();
    	foreach($req_data as $req){
    		$req_options[$req['id']] = $req['requisition_code'].' - '.$req['jobtitlename'];
    	}
    	if(count($req_options)==0)
    	{
    		$msgarray['requisition_id'] = "No approved requisitions.";
    	}
    	$id = $this->getRequest()->getParam('id');
    	$callval = $this->getRequest()->getParam('call');
    	if($callval == 'ajaxcall')
    		$this->_helper->layout->disableLayout();
    	
    		$form = new Default_Form_Candidatedetails();
    		$form->setAction(BASE_URL.'candidatedetails/addpopup');
    		$countrieslistArr = $countriesModel->getTotalCountriesList();
    		if(sizeof($countrieslistArr)>0){
    			$form->country->addMultiOption('','Select Country');
    			foreach($countrieslistArr as $countrieslistres)
    			{
    				$form->country->addMultiOption($countrieslistres['id'],utf8_encode($countrieslistres['country_name']) );
    			}
    		}else{
    			$msgarray['country'] = 'Countries are not configured yet.';
    		}
    	
    		$form->requisition_id->addMultiOptions(array(''=>'Select Requisition ID')+$req_options);
    		if(isset($_POST['country']) && $_POST['country']!='')
    		{
    			$statesmodel = new Default_Model_States();
    			$statesmodeldata = $statesmodel->getStatesList(intval($_POST['country']));
    			$st_opt = array();
    			if(count($statesmodeldata) > 0)
    			{
    				foreach($statesmodeldata as $dstate)
    				{
    					$st_opt[$dstate['id'].'!@#'.$dstate['state_name']] = $dstate['state_name'];
    				}
    			}
    			$form->state->addMultiOptions(array(''=>'Select State')+$st_opt);
    		}
    		if(isset($_POST['state']) && $_POST['state']!='')
    		{
    			$citiesmodel = new Default_Model_Cities();
    			$citiesmodeldata = $citiesmodel->getCitiesList(intval($_POST['state']));
    			$ct_opt = array();
    			if(count($citiesmodeldata) > 0)
    			{
    				foreach($citiesmodeldata as $dcity)
    				{
    					$ct_opt[$dcity['id'].'!@#'.$dcity['city_name']] = $dcity['city_name'];
    				}
    			}
    			$form->city->addMultiOptions(array(''=>'Select City')+$ct_opt);
    		}
    		$data = array();
    	
    		$data['cand_resume'] = $this->_getParam('cand_resume');
    		$data['selected_option'] = $this->_getParam('selected_option');
    		$this->view->form = $form;
    		$this->view->data = $data;
    		$this->view->popConfigPermission = $popConfigPermission;
    		$this->view->msgarray = $msgarray;
  	  if($this->getRequest()->getPost())
   	 {
    		
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
        }
        $cand_model = new Default_Model_Candidatedetails();
        $candwork_model = new Default_Model_Candidateworkdetails();
        $req_model = new Default_Model_Requisition();
		$requisition_id = $this->_getParam('requisition_id',null);
		$cand_status = $this->_getParam('cand_status',null);
		$ststidflag1 = $this->_getParam('ststidflag1',null);
		$flag = 'true';
		if($ststidflag1 == 'true'){
			if($requisition_id == ''){
				$msgarray['requisition_id'] = 'Please select requisition id.';
				$flag = 'false';
			}
			if($cand_status == ''){
				$msgarray['cand_status'] = 'Please select status.';
				$flag = 'false';
			}
		}
		$this->view->ststidflag1 = $ststidflag1;
		$buttonval= $this->_getParam('submit',null);
		$country = $this->_getParam('country',null);
        $state = $this->_getParam('state',null);
        $city = $this->_getParam('city',null);
        if($form->isValid($this->_request->getPost()) && $flag == 'true')
        {            
            $id = $this->getRequest()->getParam('id');
            $requisition_id = $this->_getParam('requisition_id',null);
            $candidate_firstname = $this->_getParam('candidate_firstname',null);
            $candidate_lastname = $this->_getParam('candidate_lastname',null);
            $candidate_name = $candidate_firstname.' '.$candidate_lastname;
            $cand_resume = $this->_getParam('cand_resume',null);
            $emailid = $this->_getParam('emailid',null);
            $contact_number = $this->_getParam('contact_number',null);
            $qualification = $this->_getParam('qualification',null);
            $experience = $this->_getParam('experience',null);
            $skillset = $this->_getParam('skillset',null);
            $education_summary = $this->_getParam('education_summary',null);
            $summary = $this->_getParam('summary',null);
            $cand_location = $this->_getParam('cand_location',null);
            
            $pincode = $this->_getParam('pincode',null);
            $cand_status = $this->_getParam('cand_status',null);
            for($i=0;$i<3;$i++)
            {
                $txt_cname[] = $this->_getParam('txt_cname'.$i,null);
                $txt_desig[] = $this->_getParam('txt_desig'.$i,null);
                $txt_from[] = $this->_getParam('txt_from'.$i,null);
                $txt_to[] = $this->_getParam('txt_to'.$i,null);
                $txt_cnumber[] = $this->_getParam('txt_cnumber'.$i,null);
                $txt_website[] = $this->_getParam('txt_website'.$i,null);
                $txt_address[] = $this->_getParam('txt_address'.$i,null);
            }
            $hidworkdata = $this->_getParam('hidworkdata',null);
            
				$req_records = $cand_model->getcountofrecords($requisition_id);
				if(empty($req_records)) 
				{
					$rdata = array(
							'req_status' => 'In process',
							'modifiedby' => trim($loginUserId),
							'modifiedon' => gmdate("Y-m-d H:i:s")
					);
					$rwhere = ' id = '.$requisition_id;
					$req_model->SaveorUpdateRequisitionData($rdata, $rwhere);
				}
				$data = array(
                    'requisition_id' => $requisition_id,
					'candidate_firstname' => trim($candidate_firstname),
					'candidate_lastname' => trim($candidate_lastname),
                    'candidate_name' => trim($candidate_name),
                    'emailid' => trim($emailid),
                    'contact_number' => trim($contact_number)==''?NULL:trim($contact_number),
					'cand_resume' => $cand_resume,
                    'qualification' => trim($qualification),
                    'experience' => trim($experience),
                    'skillset' => trim($skillset),
                    'education_summary' => trim($education_summary),
                    'summary' => trim($summary),
                    'cand_location' => trim($cand_location),
                    'country' => intval($country),
                    'state' => intval($state),
                    'city' => intval($city),
					'pincode' => $pincode,
                    'cand_status' => $cand_status,
                    'isactive' => 1,
                    'createdby' => trim($loginUserId),
                    'modifiedby' => trim($loginUserId),
                    'createddate' => gmdate("Y-m-d H:i:s"),
                    'modifieddate' => gmdate("Y-m-d H:i:s"),
                );
                if(trim($contact_number)=='')
                    unset($data['contact_number']);
                if(trim($emailid)=='')
                    unset($data['emailid']);
                $where = "";
                $actionflag = 1;
                if($id != ''){
                    unset($data['createdby']);
                    unset($data['createdon']);
                    unset($data['isactive']);
                    $where = "id = ".$id;
                    $tableid = $id;
                    $actionflag = 2;
                }
                $result = $cand_model->SaveorUpdateCandidateData($data, $where);
                $cid=$result;
                if($id == '')
                    $tableid = $result;
                
                if($result != '')
                {
                    //saving of candidate work details
                    if(count($txt_cname)>0)
                    {
                        $k =0 ;
                        foreach($txt_cname as $cname)
                        {
                            if($cname != '')
                            {
                                $cdata = array(
                                    'cand_id' => $tableid,
                                    'company_name' => $cname,
                                    'contact_number' => $txt_cnumber[$k],
                                    'company_address' => $txt_address[$k],
                                    'company_website' => $txt_website[$k],
                                    'cand_designation' => $txt_desig[$k],
                                    'cand_fromdate' => sapp_Global::change_date($txt_from[$k],'database'),
                                    'cand_todate' => sapp_Global::change_date($txt_to[$k],'database'),
                                    'isactive' => 1,
                                    'createdby' => trim($loginUserId),
                                    'modifiedby' => trim($loginUserId),
                                    'createddate' => gmdate("Y-m-d H:i:s"),
                                    'modifieddate' => gmdate("Y-m-d H:i:s"), 
                                );
                                $cwhere = ($hidworkdata[$k]!='')?"id = ".$hidworkdata[$k]:"";
                                $candwork_model->SaveorUpdateCandidateWorkData($cdata, $cwhere);
                            }
                            $k++;
                        }
                    }
                    //end of saving of candidate work details
                    $menumodel = new Default_Model_Menu();
                    $objidArr = $menumodel->getMenuObjID('/candidatedetails');
                    $objID = $objidArr[0]['id'];
                    $result = sapp_Global::logManager($objID,$actionflag,$loginUserId,$tableid);
                  
                }
             
                
                
                if(isset($requisition_id))
                {
                	$candData = $cand_model->fetchAll('isactive = 1 and requisition_id = '.$requisition_id)->toArray();
                }
                else $candData = array();
                
                $opt ='';
                if(count($candData)>0)
                {
                   foreach($candData as $record)
                  {
                	$opt .= sapp_Global::selectOptionBuilder($record['id'], $record['candidate_name']);
                  }
                }
                
                $this->view->candData = $opt;
         
                $this->view->eventact = 'added';
                $close = 'close';
                $this->view->popup=$close;
                $this->view->controllername = $controllername;
                
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
	            if(isset($country) && $country != 0 && $country != '')
				{
					$statesmodel = new Default_Model_States();
					$statesmodeldata = $statesmodel->getStatesList(intval($country));
					$form->state->clearMultiOptions();
					$form->city->clearMultiOptions();
					$form->state->addMultiOption('','Select State');
					foreach($statesmodeldata as $res)
					$form->state->addMultiOption($res['id'],utf8_encode($res['state_name']));
					if(isset($state) && $state != 0 && $state != '')
					{
						$form->setDefault('state',$state);
					}	
				}
	            if(isset($state) && $state != 0 && $state != '')
				{
					$citiesmodel = new Default_Model_Cities();
					$citiesmodeldata = $citiesmodel->getCitiesList(intval($state));
						
					$form->city->addMultiOption('','Select City');
					foreach($citiesmodeldata as $res)
					$form->city->addMultiOption($res['id'],utf8_encode($res['city_name']));
					if(isset($city) && $city != 0 && $city != '')
					$form->setDefault('city',$city);
				}
				$this->view->msgarray = $msgarray;
				$this->view->messages = $msgarray;
             	
            }
          
          
    	}
    }
    /**
     * This action is used for adding data.    
     * @return Zend_Form.
     */
    public function addAction()
    {        
        $req_model = new Default_Model_Requisition();        
        $cand_model = new Default_Model_Candidatedetails();
        $cand_work_model = new Default_Model_Candidateworkdetails();
        $countriesModel = new Default_Model_Countries();
        $auth = Zend_Auth::getInstance();
        $msgarray = array();
        $popConfigPermission = array();
     	if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
            $loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
        }
        
        if(sapp_Global::_checkprivileges(COUNTRIES,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
				array_push($popConfigPermission,'country');
		}
		if(sapp_Global::_checkprivileges(STATES,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
				array_push($popConfigPermission,'state');
		}
		if(sapp_Global::_checkprivileges(CITIES,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
				array_push($popConfigPermission,'city');
		}
                
        $req_data = $req_model->getRequisitionsForCV("'Approved','In process'");
        $req_options = array();
        foreach($req_data as $req){
            $req_options[$req['id']] = $req['requisition_code'].' - '.$req['jobtitlename'];
        }
        if(count($req_options)==0)
        {
            $msgarray['requisition_id'] = "No approved requisitions.";
        }
        $id = $this->getRequest()->getParam('id');
		$approvedrequisition_id=$this->getRequest()->getParam('req_id');
		
        $callval = $this->getRequest()->getParam('call');
        if($callval == 'ajaxcall')
            $this->_helper->layout->disableLayout();
        $form = new Default_Form_Candidatedetails();   
        $form->setAction(BASE_URL.'candidatedetails/add');
		if(!empty($approvedrequisition_id))
		{
		$form->setDefault('requisition_id',$approvedrequisition_id);
		}
		$countrieslistArr = $countriesModel->getTotalCountriesList();
		if(sizeof($countrieslistArr)>0){
			$form->country->addMultiOption('','Select Country');
			foreach($countrieslistArr as $countrieslistres)
			{
				$form->country->addMultiOption($countrieslistres['id'],utf8_encode($countrieslistres['country_name']) );
			}
		}else{
			$msgarray['country'] = 'Countries are not configured yet.';
		}
		/**
       NA->Not applicable candidate value defined in constant
        **/
		$notapplicable=array(NA=>'Not Applicable Candidate');
		$form->requisition_id->addMultiOptions(array(''=>'Select Requisition ID')+$req_options+$notapplicable);
        if(isset($_POST['country']) && $_POST['country']!='')
        {
            $statesmodel = new Default_Model_States();
            $statesmodeldata = $statesmodel->getStatesList(intval($_POST['country']));
            $st_opt = array();
            if(count($statesmodeldata) > 0)
            {
                foreach($statesmodeldata as $dstate)
                {
                    $st_opt[$dstate['id'].'!@#'.$dstate['state_name']] = $dstate['state_name'];
                }
            }
            $form->state->addMultiOptions(array(''=>'Select State')+$st_opt);
        }
        if(isset($_POST['state']) && $_POST['state']!='')
        {
            $citiesmodel = new Default_Model_Cities();
            $citiesmodeldata = $citiesmodel->getCitiesList(intval($_POST['state']));
            $ct_opt = array();
            if(count($citiesmodeldata) > 0)
            {
                foreach($citiesmodeldata as $dcity)
                {
                    $ct_opt[$dcity['id'].'!@#'.$dcity['city_name']] = $dcity['city_name'];
                }
            }
            $form->city->addMultiOptions(array(''=>'Select City')+$ct_opt);
        }
		if(isset($_POST['source']) && $_POST['source']=='Vendor')
        {
         
			$vendorsmodel= new Default_Model_Vendors();
			$vendorsdataArr=$vendorsmodel->getVendorsList();
		    $vendors_array = array();
			if(sizeof($vendorsdataArr)>0){
				
				foreach($vendorsdataArr as $vendors)
				{
					  $vendors_array[$vendors['id']] = $vendors['name'];
				}
			}
               $form->vendors->addMultiOptions(array(''=>'Select Vendor')+$vendors_array);
        }
		
		
        $data = array();
        
		$data['cand_resume'] = $this->_getParam('cand_resume');       
		$data['selected_option'] = $this->_getParam('selected_option'); 
		$this->view->form = $form;                                                
        $this->view->data = $data;
        $this->view->popConfigPermission = $popConfigPermission;
        $this->view->msgarray = $msgarray;
        if($this->getRequest()->getPost())
        {
				$result = $this->save($form);
                $this->view->msgarray = $result; 
                $this->view->messages = $result;	
        }
    }		
	
    public function save($form)
    {
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
        }
        $cand_model = new Default_Model_Candidatedetails();
        $candwork_model = new Default_Model_Candidateworkdetails();
        $req_model = new Default_Model_Requisition();
		$requisition_id = $this->_getParam('requisition_id',null);
		$cand_status = $this->_getParam('cand_status',null);
		$ststidflag1 = $this->_getParam('ststidflag1',null);
		$flag = 'true';
		if($ststidflag1 == 'true'){
			if($requisition_id == ''){
				$msgarray['requisition_id'] = 'Please select requisition id.';
				$flag = 'false';
			}
			if($cand_status == ''){
				$msgarray['cand_status'] = 'Please select status.';
				$flag = 'false';
			}
		}
		$this->view->ststidflag1 = $ststidflag1;
		$buttonval= $this->_getParam('submit',null);
		$country = $this->_getParam('country',null);
        $state = $this->_getParam('state',null);
        $city = $this->_getParam('city',null);
        if($form->isValid($this->_request->getPost()) && $flag == 'true')
        {       
            $id = $this->getRequest()->getParam('id');
            $requisition_id = $this->_getParam('requisition_id',null);
            $candidate_firstname = $this->_getParam('candidate_firstname',null);
            $candidate_lastname = $this->_getParam('candidate_lastname',null);
            $candidate_name = $candidate_firstname.' '.$candidate_lastname;
            $cand_resume = $this->_getParam('cand_resume',null);
            $emailid = $this->_getParam('emailid',null);
            $contact_number = $this->_getParam('contact_number',null);
            $qualification = $this->_getParam('qualification',null);
            $experience = $this->_getParam('experience',null);
            $skillset = $this->_getParam('skillset',null);
            $education_summary = $this->_getParam('education_summary',null);
            $summary = $this->_getParam('summary',null);
            $cand_location = $this->_getParam('cand_location',null);
            $source=$this->_getParam('source',null);
          if($source=='Referal')
          {
            $sourceval=$this->_getParam('referal',null);
          }
          if($source=='Website')
          {
          	$sourceval=$this->_getParam('referalwebsite',null);
          }
          if($source=='Vendor')
          {
          	$sourceval=$this->_getParam('vendors',null);
          }
    
            $pincode = $this->_getParam('pincode',null);
            $cand_status = $this->_getParam('cand_status',null);
            for($i=0;$i<3;$i++)
            {
                $txt_cname[] = $this->_getParam('txt_cname'.$i,null);
                $txt_desig[] = $this->_getParam('txt_desig'.$i,null);
                $txt_from[] = $this->_getParam('txt_from'.$i,null);
                $txt_to[] = $this->_getParam('txt_to'.$i,null);
                $txt_cnumber[] = $this->_getParam('txt_cnumber'.$i,null);
                $txt_website[] = $this->_getParam('txt_website'.$i,null);
                $txt_address[] = $this->_getParam('txt_address'.$i,null);
            }
            $hidworkdata = $this->_getParam('hidworkdata',null);
            
				$req_records = $cand_model->getcountofrecords($requisition_id);
				if(empty($req_records)) 
				{
					$rdata = array(
							'req_status' => 'In process',
							'modifiedby' => trim($loginUserId),
							'modifiedon' => gmdate("Y-m-d H:i:s")
					);
					$rwhere = ' id = '.$requisition_id;
					$req_model->SaveorUpdateRequisitionData($rdata, $rwhere);
				}
				$data = array(
                    'requisition_id' => $requisition_id,
					'candidate_firstname' => trim($candidate_firstname),
					'candidate_lastname' => trim($candidate_lastname),
                    'candidate_name' => trim($candidate_name),
                    'emailid' => trim($emailid),
                    'contact_number' => trim($contact_number)==''?NULL:trim($contact_number),
					'cand_resume' => $cand_resume,
                    'qualification' => trim($qualification),
                    'experience' => trim($experience),
                    'skillset' => trim($skillset),
                    'education_summary' => trim($education_summary),
                    'summary' => trim($summary),
                    'cand_location' => trim($cand_location),
                    'country' => intval($country),
                    'state' => intval($state),
                    'city' => intval($city),
					'pincode' => $pincode,
                    'cand_status' => $cand_status,
					'source'=>	$source,
					'source_val'=>	$sourceval,
                    'isactive' => 1,
                    'createdby' => trim($loginUserId),
                    'modifiedby' => trim($loginUserId),
                    'createddate' => gmdate("Y-m-d H:i:s"),
                    'modifieddate' => gmdate("Y-m-d H:i:s"),
						
                );
			
                if(trim($contact_number)=='')
                    unset($data['contact_number']);
                if(trim($emailid)=='')
                    unset($data['emailid']);
                $where = "";
                $actionflag = 1;
                if($id != ''){
                    unset($data['createdby']);
                    unset($data['createdon']);
                    unset($data['isactive']);
                    $where = "id = ".$id;
                    $tableid = $id;
                    $actionflag = 2;
                }
			
                $result = $cand_model->SaveorUpdateCandidateData($data, $where);
				 /** saving candidate history **/
			 
				if($result != 'update')
				{ 
			       // $candidateData = $cand_model->getCandidateForView($result);
					$candidateDetails = $cand_model->getcandidateData($result);
					if(!empty($candidateDetails))
					{
						if($candidateDetails['requisition_id']!= 0)
						{
						  $candidateData = $cand_model->getCandidateForView($result);
						}
						else
						{
						   $candidateData = $cand_model->getViewforNotapplicableCandidates($result);
						}
					}
						 
					  if($requisition_id>0)
					  {
						$req_data= $req_model ->getRequisitionDataById($requisition_id);
						$history = 'Candidate:'.$candidateData["candidate_name"].' has been added to Requisition:'.$req_data['requisition_code'].' by ';
					  }
					  else
					  {
						  $history = 'Candidate:'.$candidateData["candidate_name"].' has been added as Not Applicable Candidate by ';
					  }
					
                    $createdby = $loginUserId;
					$modifiedby = $loginUserId;
					
					 $reqh_model = new Default_Model_Requisitionhistory();
					$requisition_history = array(
                                        'candidate_id' =>$result,	
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
				if($result=='update')
				{
					$cid=$id;
				}
				else
				{
					$cid= $result;
				}
                
                if($id == '')
                    $tableid = $result;
                
                if($result != '')
                {
                    //saving of candidate work details
                    if(count($txt_cname)>0)
                    {
                        $k =0 ;
                        foreach($txt_cname as $cname)
                        {
                            if($cname != '')
                            {
                                $cdata = array(
                                    'cand_id' => $tableid,
                                    'company_name' => $cname,
                                    'contact_number' => $txt_cnumber[$k],
                                    'company_address' => $txt_address[$k],
                                    'company_website' => $txt_website[$k],
                                    'cand_designation' => $txt_desig[$k],
                                    'cand_fromdate' => sapp_Global::change_date($txt_from[$k],'database'),
                                    'cand_todate' => sapp_Global::change_date($txt_to[$k],'database'),
                                    'isactive' => 1,
                                    'createdby' => trim($loginUserId),
                                    'modifiedby' => trim($loginUserId),
                                    'createddate' => gmdate("Y-m-d H:i:s"),
                                    'modifieddate' => gmdate("Y-m-d H:i:s"), 
                                );
                                $cwhere = ($hidworkdata[$k]!='')?"id = ".$hidworkdata[$k]:"";
                                $candwork_model->SaveorUpdateCandidateWorkData($cdata, $cwhere);
                            }
                            $k++;
                        }
                    }
                    //end of saving of candidate work details
                    $menumodel = new Default_Model_Menu();
                    $objidArr = $menumodel->getMenuObjID('/candidatedetails');
                    $objID = $objidArr[0]['id'];
                    $result = sapp_Global::logManager($objID,$actionflag,$loginUserId,$tableid);
                    if($id == '')
                    {
                        //$this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Candidate details added successfully.'); 
                        $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Candidate details added successfully."));
                    }    
                    else
                    {
                        //$this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Candidate details updated successfully.');
                        $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Candidate details updated successfully."));
                    }    
                    if($buttonval=="Save" || $buttonval=="Update")
                    {
                    	$this->_redirect('/candidatedetails');
                    }
                    else {
                    	$this->_redirect('/scheduleinterviews/add/cid/'.$cid);
                    }
                    
                }
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
	            if(isset($country) && $country != 0 && $country != '')
				{
					$statesmodel = new Default_Model_States();
					$statesmodeldata = $statesmodel->getStatesList(intval($country));
					$form->state->clearMultiOptions();
					$form->city->clearMultiOptions();
					$form->state->addMultiOption('','Select State');
					foreach($statesmodeldata as $res)
					$form->state->addMultiOption($res['id'],utf8_encode($res['state_name']));
					if(isset($state) && $state != 0 && $state != '')
					{
						$form->setDefault('state',$state);
					}	
				}
	            if(isset($state) && $state != 0 && $state != '')
				{
					$citiesmodel = new Default_Model_Cities();
					$citiesmodeldata = $citiesmodel->getCitiesList(intval($state));
						
					$form->city->addMultiOption('','Select City');
					foreach($citiesmodeldata as $res)
					$form->city->addMultiOption($res['id'],utf8_encode($res['city_name']));
					if(isset($city) && $city != 0 && $city != '')
					$form->setDefault('city',$city);
				}
                
               return $msgarray;
            }
	}
	
    /**
     * This action is used to delete candidate and their interview round data.
     * @parameters
     * @param objid    =   id of candidate.
     * 
     * @return   =   success/failure message 
     */
    public function deleteAction()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
        }
        
        $id = $this->_request->getParam('objid');
		$deleteflag= $this->_request->getParam('deleteflag');
        $messages['message'] = '';$messages['msgtype'] ="";
        $actionflag = 3;
        if($id)
        {
            $cand_model = new Default_Model_Candidatedetails();   
            $cand_work_model = new Default_Model_Candidateworkdetails();
            $interview_model = new Default_Model_Interviewdetails();
            $interview_round_model = new Default_Model_Interviewrounddetails();
            $menumodel = new Default_Model_Menu();
            $data = array(
                        'isactive' => 0,
                        'modifiedby' => $loginUserId,
                        'modifieddate' => gmdate("Y-m-d H:i:s"),
                );
            $where = array('id=?'=>$id);
            $Id = $cand_model->SaveorUpdateCandidateData($data, $where);
            if($Id == 'update')
            {
                $cand_work_data = array(
                        'isactive' => 0,
                        'modifiedby' => $loginUserId,
                        'modifieddate' => gmdate("Y-m-d H:i:s"),
                );
                $cand_work_where = "cand_id = ".$id;
                $cand_work_model->SaveorUpdateCandidateWorkData($cand_work_data, $cand_work_where);
                
                $interview_data = array(
                        'isactive' => 0,
                        'modifiedby' => $loginUserId,
                        'modifieddate' => gmdate("Y-m-d H:i:s"),
                );
                $interview_where = "candidate_id = ".$id;
                $interview_model->SaveorUpdateInterviewData($interview_data, $interview_where);
                
                $interview_round_data = array(
                        'isactive' => 0,
                        'modifiedby' => $loginUserId,
                        'modifieddate' => gmdate("Y-m-d H:i:s"),
                );
                $interview_round_where = "candidate_id = ".$id;
                $interview_round_model->SaveorUpdateInterviewroundData($interview_round_data, $interview_round_where);
                $objidArr = $menumodel->getMenuObjID('/candidatedetails');
                $objID = $objidArr[0]['id'];                    
                $result = sapp_Global::logManager($objID,$actionflag,$loginUserId,$id);  
                $messages['message'] = 'Candidate deleted successfully.';
				$messages['msgtype'] = 'success';	
            }   
            else
			{
               $messages['message'] = 'Candidate cannot be deleted.';				
			   $messages['msgtype'] = 'error';	
			}
        }
        else
        { 
            $messages['message'] = 'Candidate cannot be deleted.';
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
    }// end of delete action
    
    public function chkcandidateAction()
    {
    
       $id = $this->_getParam('id',null);
	   	$deleteflag= $this->_request->getParam('deleteflag');
        $cand_model = new Default_Model_Candidatedetails();
        $candidatDetails = $cand_model->getcandidateData($id);
        if($candidatDetails['requisition_id']!=0){
        $cand_data = $cand_model->getCandidateById($id);
        if($cand_data['cand_status'] != 'Not Scheduled')
            $status = 'no';
        else 
            $status = 'yes';
        }else 
        {
              	if($candidatDetails['cand_status'] != 'Not Scheduled')
        		$status = 'no';
        		else
        			$status = 'yes';
        }
		if($deleteflag==1)
		{
			if(	$status == 'no')
			{
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("error"=>"Candidate cannot be deleted.","msgtype"=>"error" ,"deleteflag"=>$deleteflag));
			}
			
		} 
        $this->_helper->_json(array('status'=>$status));
    }

    // To upload resume of the candidate
    public function uploadfileAction(){
    	$cand_model = new Default_Model_Candidatedetails();
    	$result = $cand_model->saveUploadedFile($_FILES);
    	$this->_helper->json($result);
    }
    
    // To delete resume
    public function deleteresumeAction(){
    	$cand_details_model = new Default_Model_Candidatedetails();
    	$data = array('cand_resume'=>NULL, 'cand_resume_deletedby'=>$cand_details_model->getLoginUserId());
    	$where = 'id="'.$this->_getParam('rec_id').'"';
    	
    	// To empty resume name in DB
    	$message = $cand_details_model->SaveorUpdateUserData($data, $where);
    	
    	// To remove resume file from folder
    	if($message=='update'){
    		@unlink(UPLOAD_PATH_RESUMES . "/" . $this->_getParam('resume_name'));
    	}
		$this->_helper->json(array('action'=>$message));   
    }
    
    // To download resume
    public function downloadAction(){
		$cand_details_model = new Default_Model_Candidatedetails();
    	$result = $cand_details_model->getcandidateData($this->_getParam('id'));
    	if(!empty($result['cand_resume'])){
    		$status = array();
			$file = BASE_PATH.'/uploads/resumes/'.$result['cand_resume'];
			$status = sapp_Global::downloadFile($file);
			if(!empty($status['message'])){				
                            $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>$status['message']));
			}
    	}
  		$this->_redirect('candidatedetails/index');
    }
    
    // To upload multiple resume data
    public function multipleresumeAction(){
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
            $loginUserId = trim($loginUserId);
        }
        $priv_check = sapp_Global::_checkprivileges(CANDIDATEDETAILS,'',$auth->getStorage()->read()->emprole,'add');
        if($priv_check == 'Yes'){
	        $msgarray = $req_options =array();
	    	$form = new Default_Form_Multipleresume();   
	        $form->setAction(BASE_URL.'candidatedetails/multipleresume');
	
	        // To show list of requisitions to user
	        $req_model = new Default_Model_Requisition();
	        $req_data = $req_model->getRequisitionsForCV("'Approved','In process'");
	        
	        foreach($req_data as $req){
	            $req_options[$req['id']] = $req['requisition_code'].' - '.$req['jobtitlename'];
	        }
	        
	        if(count($req_options)==0){
	            $msgarray['requisition_id'] = "No active requisitions are found.";
	        }
	        
			/**
		   NA->Not applicable candidate value defined in constant
			**/
			$notapplicable=array(NA=>'Not Applicable Candidate');
			$form->requisition_id->addMultiOptions(array(''=>'Select Requisition ID')+$req_options+$notapplicable);
			
			//$form->requisition_id->addMultiOptions(array(''=>'Select Requisition ID')+$req_options);
		
			$form_post_status = $this->_request->getPost();
			
			if($form_post_status){
	            $candidate_firstname = $this->_getParam('candidate_firstname',null);
	            $candidate_lastname = $this->_getParam('candidate_lastname',null);
	            $cand_resumes = $this->_getParam('cand_resume',null);
	            if($form->isValid($form_post_status)){
	        		$cand_details_model = new Default_Model_Candidatedetails();
		        	$requisition_id = $this->_getParam('requisition_id',null);
		            $cand_status = $this->_getParam('cand_status',null);
		            
					$req_records = $cand_details_model->getcountofrecords($requisition_id);
					
					$curr_date = gmdate("Y-m-d H:i:s");
					if(empty($req_records)){
						$rdata = array(
								'req_status' => 'In process',
								'modifiedby' => $loginUserId,
								'modifiedon' => $curr_date
						);
						$rwhere = ' id = '.$requisition_id;
						$req_model->SaveorUpdateRequisitionData($rdata, $rwhere);
					}
					
					
					// To insert records in a single query
					$records = array();
					foreach($candidate_firstname as $key=>$candidate_fname){
						$cfull_name =$candidate_fname.' '.$candidate_lastname[$key]; 
						$records[] = "($requisition_id, '$candidate_fname','$candidate_lastname[$key]','$cfull_name', '$cand_resumes[$key]', '$cand_status', 1, $loginUserId, $loginUserId, '$curr_date', '$curr_date')";
					} 
					$data_fields = array('requisition_id', 'candidate_firstname','candidate_lastname','candidate_name', 'cand_resume', 'cand_status', 'isactive', 'createdby', 'modifiedby', 'createddate', 'modifieddate');
				 
                    $last_insert_id=array();	
					for($i=0;$i<sizeof($records);$i++)
					{
						$last_insert_id[] = $cand_details_model->insertMultipleRecords($data_fields, $records[$i]);

					}
					/** saving candidate history **/
					$result=  $last_insert_id;

					for($j=0;$j<sizeof($result);$j++)
					{
					
						if($result != 'update')
						{ 
							$candidateDetails =  $cand_details_model->getcandidateData($result[$j]);
							if(!empty($candidateDetails))
							{
								if($candidateDetails['requisition_id']!= 0)
								{
								  $candidateData =  $cand_details_model->getCandidateForView($result[$j]);
								}
								else
								{
								   $candidateData =  $cand_details_model->getViewforNotapplicableCandidates($result[$j]);
								}
							}
								 
							  if($requisition_id>0)
							  {
								$req_data= $req_model ->getRequisitionDataById($requisition_id);
								$history = 'Candidate:'.$candidateData["candidate_name"].' has been added to Requisition:'.$req_data['requisition_code'].' by ';
							  }
							  else
							  {
								  $history = 'Candidate:'.$candidateData["candidate_name"].' has been added as Not Applicable Candidate by ';
							  }
							
							$createdby = $loginUserId;
							$modifiedby = $loginUserId;
							
							 $reqh_model = new Default_Model_Requisitionhistory();
							$requisition_history = array(
												'candidate_id' =>$result[$j],	
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
					}      
					//history end
		            
		            // Log status to Log manager and redirect to CV Management list page.
		            if($last_insert_id != ''){
		                $menumodel = new Default_Model_Menu();
		                $objidArr = $menumodel->getMenuObjID('/candidatedetails');
		                $objID = $objidArr[0]['id'];
		                $log_status = sapp_Global::logManager($objID,1,$loginUserId,$last_insert_id);
		                //$this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Candidate details added successfully.');
		                $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Candidate details added successfully.")); 
		                $this->_redirect('/candidatedetails');
					}
				
				}else{
					// To handle server validation, when Javascript is disabled
		            $messages = $form->getMessages();
		            foreach ($messages as $key => $val){
		                foreach($val as $key2 => $val2){
		                    $msgarray[$key] = $val2;
		                    break;
		                }
		            }
	            					
					$form->setDefault('candidate_firstname',$candidate_firstname[0]);
					$form->setDefault('cand_resume','');
				}
						
			}
				                
			$this->view->form = $form;                                                
	        $this->view->msgarray = $msgarray;
        }else{
            $this->_redirect('error');
        }
    }
    // for vendors dropsown in while adding a candidate
    public function getvendorsAction()
    {
    	
    	$vendorsmodel= new Default_Model_Vendors();
    	$vendorsdataArr=$vendorsmodel->getVendorsList();
    
    	$opt='<option value=\'\'>Select Vendor</option>';
 
    	if(sizeof($vendorsdataArr)>0){
    		
    		foreach($vendorsdataArr as $vendors)
    		{
    			$opt.="<option value='".$vendors['id']."'>".$vendors['name']."</option>";
    		}
    	}
    	
    	
    	    	
    	$this->_helper->json(array('options'=>utf8_encode($opt)));
    	
    	
    }
    //to show candidate details in model window
    public function viewpopupAction()
    {
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();
	    $objName = 'candidatedetails';
		
		$cand_model = new Default_Model_Candidatedetails();	
		$candidateData = $cand_model->getcandidateData($id); 

		$form = new Default_Form_Candidatedetails(); 
	  //   $form->source->setAttrib("readonly", "true");
		
        $form->source->setAttrib("disabled", "disabled");
		$form->referal->setAttrib("disabled", "disabled");
		$form->vendors->setAttrib("disabled", "disabled");
		$form->referalwebsite->setAttrib("disabled", "disabled");
		$form->emailid->setAttrib("disabled", "disabled");
		$form->contact_number->setAttrib("disabled", "disabled");
		$form->skillset->setAttrib("disabled", "disabled"); 

          $form->submit->setLabel('Update'); 
		$form->source->setValue($candidateData['source']);
	
		if($candidateData['source']=="Website" )
		{
			$form->referalwebsite->setValue($candidateData['source_val']);
		}
		if($candidateData['source']=="Referal"  )
		{
			$form->referal->setValue($candidateData['source_val']);
		}
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
		$cdata = array();
	    $cdata['selected_option'] = "candidatedetails"; 
		$this->view->cdata =  $cdata;
        $form->populate($candidateData);		
		$this->view->id = $id;
		$this->view->data = $candidateData;
		$this->view->form = $form;
		if($this->getRequest()->getPost())
		 {
			  
		    if($form->isValid($this->_request->getPost()))
            {    
			$candidate_firstname = $this->_getParam('candidate_firstname',null);
            $candidate_lastname = $this->_getParam('candidate_lastname',null);
            $candidate_name = $candidate_firstname.' '.$candidate_lastname;
			
			$data =array(
                   	'candidate_firstname' => trim($candidate_firstname),
					'candidate_lastname' => trim($candidate_lastname),
                    'candidate_name' => trim($candidate_name),
                    'modifieddate' => gmdate("Y-m-d H:i:s"),
                      );
				
			$where = "id = ".$id;
			$result = $cand_model->SaveorUpdateCandidateData($data, $where);
			  if(isset($id))
                {
					$candData  = $cand_model->getSelectedCandidatesDetails();
                }
                else $candData = array();
                $opt ='';
                if(count($candData)>0)
                {
                  foreach($candData as $record)
                  {
					
                	$opt .= sapp_Global::selectOptionBuilder($record['id'], $record['candidate_name']);
                  }
                }
				$this->view->candidateid =   $id ;
				$this->view->candData = $opt; 
                $this->view->eventact = 'updated';
                $close = 'close';
                $this->view->popup=$close;
                $this->view->controllername =$objName;
				$this->view->candidatename =trim($candidate_name);
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
				$this->view->msgarray = $msgarray;
		
			}
		 }
    }
	
       
}//end of class