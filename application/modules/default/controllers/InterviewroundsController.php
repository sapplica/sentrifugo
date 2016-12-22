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

class Default_InterviewroundsController extends Zend_Controller_Action
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
    public function indexAction()
    {
		$interview_round_model = new Default_Model_Interviewrounddetails();
        $call = $this->_getParam('call');
        if($call == 'ajaxcall')
            $this->_helper->layout->disableLayout();
			
		$id = $this->_getParam('unitId');
		$unitId = $this->_getParam('unitId');
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
            $sort = 'ASC';$by = 'ir.interview_round_number';$pageNo = 1;$searchData = '';$searchQuery = '';
            $searchArray = array();
        }
        else 
        {
            $sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'ASC';
            $by = ($this->_getParam('by')!='')? $this->_getParam('by'):'ir.interview_round_number';
            if($dashboardcall == 'Yes')
				$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
			else $perPage = $this->_getParam('per_page',PERPAGE);
            $pageNo = $this->_getParam('page', 1);
            /** search from grid - START **/
            $searchData = $this->_getParam('searchData');	
            if($searchData != '' && $searchData!='undefined')
            {
                $searchValues = json_decode($searchData);
                if(count($searchValues) >0)
                {
                    foreach($searchValues as $key => $val)
                    {
                        if($key == 'interview_date')
                            $searchQuery .= " ".$key." like '%".sapp_Global::change_date($val,'database')."%' AND ";
                        else 
                            $searchQuery .= " ".$key." like '%".$val."%' AND ";
                        $searchArray[$key] = $val;
                    }
                    $searchQuery = rtrim($searchQuery," AND");					
                }
            }
            /** search from grid - END **/
        }
		$actns = '';
		$intrvw_statusData = $interview_round_model->getinterviewstatus($id);
		$intrvw_status = $intrvw_statusData['interview_status'];
		if($intrvw_status == 'Completed')
		{
			$actns = 'remove';
                    $tableFields = array('interview_round_number'=>'Interview Round','interview_round'=>'Interview Name','userfullname' => 'Interviewer','interview_date' =>'Interview Date','round_status'=>'Round Status');
		}else{
                    $tableFields = array('action'=>'Action','interview_round_number'=>'Interview Round','interview_round'=>'Interview Name','userfullname' => 'Interviewer','interview_date' =>'Interview Date','round_status'=>'Round Status');
		}
        $objName = 'interviewrounds';

       
        $round_arr = array( '' => 'All','Schedule for next round' => 'Schedule for next round',
                                'Qualified' => 'Qualified','Selected' => 'Selected','Disqualified' => 'Disqualified',
                                'Decision pending' => 'Decision pending','On hold' => 'On hold',
                                'Incompetent' => 'Incompetent','Ineligible' => 'Ineligible',
                                'Candidate no show' => 'Candidate no show','Requisition Closed/Completed' => 'Requisition Closed/Completed'); 
       $tablecontent = $interview_round_model->getInterviewRoundsData($sort, $by, $pageNo, $perPage,$searchQuery,$id);     
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
                'add' => 'add',
                'menuName' => 'Interview Rounds',
                'actns' => $actns,
                'formgrid' => $formgrid,
                'unitId'=>$id,
                'call'=>$call,
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
        $this->view->dataArray = $dataTmp;
        $this->view->call = $call ;
        
    }
    public function addpopupAction()
    {
        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
        $cand_model = new Default_Model_Candidatedetails();  
        $requi_model = new Default_Model_Requisition();
        $interview_model = new Default_Model_Interviewdetails();
        $interview_round_model = new Default_Model_Interviewrounddetails();
        $auth = Zend_Auth::getInstance();
        $inter_options = array();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
        }
        
        $deptid = $this->getRequest()->getParam('deptid');		
        if($deptid == '')
            $deptid = $this->getRequest()->getParam('deptidform');	
        $idData = $this->getRequest()->getParam('unitId');		 		
        $ir_form = new Default_Form_Interviewrounds();		
        $ir_form->setAttrib('action',BASE_URL.'interviewrounds/addpopup/unitId/'.$idData);
        $ir_form->removeElement("sel_inter_status");
        $ir_form->removeElement('req_id');
        $ir_form->removeElement('candidate_name');
        $ir_form->removeElement('cand_status');
        $ir_form->removeElement('interview_status');
        $data = array();
        //for timezone
        $systempreferencemodel = new Default_Model_Sitepreference();
        $sitepreferencedata= $systempreferencemodel->getActiveRecord();
        if(!empty($sitepreferencedata[0]['timezone']))
        {
        	$ir_form->timezone->setValue($sitepreferencedata[0]['timezone']);
        	$ir_form->timezone->setAttrib("disabled", "disabled");
        }
               
        if(isset($_POST['country']) && $_POST['country']!='')
        {
            $ir_form->country->setValue(intval($_POST['country']));
            $statesmodel = new Default_Model_States();
            $statesmodeldata = $statesmodel->getBasicStatesList(intval($_POST['country']));
            $st_opt = array();
            if(count($statesmodeldata) > 0)
            {
                foreach($statesmodeldata as $dstate)
                {
                    $st_opt[$dstate['state_id_org'].'!@#'.$dstate['state']] = $dstate['state'];
                }
            }
            $ir_form->state->addMultiOptions(array(''=>'Select State')+$st_opt);
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
                    $ct_opt[$dcity['city_org_id'].'!@#'.$dcity['city']] = $dcity['city'];
                }
            }
            $ir_form->city->addMultiOptions(array(''=>'Select City')+$ct_opt);
        }
        
        //giving only for hr,management and super admin
        if($loginuserGroup == HR_GROUP || $loginuserGroup == '' || $loginuserGroup == MANAGEMENT_GROUP){
          $ir_form->round_status->addMultiOptions(array('Decision pending' => 'Decision pending','On hold' => 'On hold',));
        }
        
        $data = $cand_model->getCandidateById($idData);	
		$allData = $interview_model->getCandidateDetailsById($idData);
		$interview_status = $allData['interview_status'];
		$previousroundstatus = $interview_model->getinterviewroundnumber($idData);
                $interview_max_date = $interview_round_model->getMaxRoundDateByInterviewId($idData);
		$previousroundstatus = $previousroundstatus['round_status'];
		if($interview_status != 'Completed' || $previousroundstatus == 'Schedule for next round')
		{
			$round_count = $interview_round_model->getRoundCnt($data['id'],$idData);
			$interviewer_data = $requi_model->getReportingmanagers('',$loginUserId,'',$deptid,'interviewer');
			$inter_options = $interviewer_data;
			$ir_form->submit->setLabel('Add');
			$this->view->form = $ir_form;
			$this->view->data = $data;
			$this->view->deptid = $deptid;
			$this->view->round_count = $round_count;
			$this->view->interview_status = $interview_status;
                        $this->view->interview_max_date = $interview_max_date;
                        $this->view->inter_options = $inter_options;
			if($this->getRequest()->getPost())
			{
				$result = $this->save($ir_form);
				$this->view->msgarray = $result; 
				$this->view->messages = $result;	
			}
		}else{
			if($interview_status == 'Completed')$this->view->ermsg = 'completed';
			else if($previousroundstatus == 'Schedule for next round')$this->view->ermsg = 'notscheduled';	
			else $this->view->ermsg = 'nodata';
		}
    }
	
    public function editpopupAction()
    {
        $auth = Zend_Auth::getInstance();
        $inter_options = array();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
            $loginuserRole = $auth->getStorage()->read()->emprole;
        }
        $editpermission = 'No';
        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
        $cand_model = new Default_Model_Candidatedetails();  
        $requi_model = new Default_Model_Requisition();
		$jobtitleModel = new Default_Model_Jobtitles();
        $intrvwModel = new Default_Model_Interviewdetails();
        $interview_round_model = new Default_Model_Interviewrounddetails();
        $user_model = new Default_Model_Usermanagement();
        $cancel_name = 'Cancel';
        $auth = Zend_Auth::getInstance();
		$jobtitle = '';
        $deptid = $this->getRequest()->getParam('deptid');		
        if($deptid == '')
            $deptid = $this->getRequest()->getParam('deptidform');	
		
        $intId = $this->getRequest()->getParam('unitId');	
        $roundId = $this->getRequest()->getParam('id');		
        $ir_form = new Default_Form_Interviewrounds();		
        $ir_form->setAttrib('action',BASE_URL.'interviewrounds/editpopup/unitId/'.$intId.'/id/'.$roundId);
        $intData = $intrvwModel->getReqByintrvwID($intId);
        $ir_form->removeElement('req_id');
        $ir_form->removeElement('candidate_name');
        $ir_form->removeElement('interview_status');
        $ir_form->removeElement('cand_status');
        //for timezone
        $systempreferencemodel = new Default_Model_Sitepreference();
        $sitepreferencedata= $systempreferencemodel->getActiveRecord();
        if(!empty($sitepreferencedata[0]['timezone']))
        {
        	$ir_form->timezone->setValue($sitepreferencedata[0]['timezone']);
        	$ir_form->timezone->setAttrib("disabled", "disabled");
        }
        $roundData = $interview_round_model->getSingleRoundData($roundId);	
        if($loginuserGroup == MANAGER_GROUP || $loginuserGroup == EMPLOYEE_GROUP || $loginuserGroup == SYSTEMADMIN_GROUP)
        {		
            $editpermission = sapp_Global::_checkprivileges(SCHEDULEINTERVIEWS,$loginuserGroup,$loginuserRole,'edit');	
        }
		if(($loginuserGroup == HR_GROUP || $loginuserGroup == MANAGEMENT_GROUP || $loginuserGroup == '') || $editpermission == 'Yes')
		{	
			$countryId = $roundData['int_country'];
                        
                        if(isset($_POST['country']))
                        {
                            $countryId = $_POST['country'];
                        }
                        $stateId = $roundData['int_state'];
                        if(isset($_POST['state']))
                        {
                            $stateId = $_POST['state'];
                        }
                        $cityId = $roundData['int_city'];
                        if(isset($_POST['city']))
                        {
                            $cityId = $_POST['city'];
                        }
			$country_name = '';$state_name = '';$city_name = '';
                        $countryModal = new Default_Model_Countries();
                        $countriesData = $countryModal->fetchAll('isactive=1','country');				
                        foreach($countriesData as $cdata)
                        {
                            if($roundData['int_country'] == $cdata['country_id_org'])
                            {
                                $country_name = $cdata['country'];
                                break;
                            }
                        }
                        $ir_form->setDefault('country',$countryId);
			if($countryId != '')
			{
                            $statesmodel = new Default_Model_States();								
                            $statesData = $statesmodel->getBasicStatesList($countryId);

                            foreach($statesData as $res)
                            {
                                $ir_form->state->addMultiOption($res['state_id_org'],utf8_encode($res['state']));
                                if($roundData['int_state'] == $res['state_id_org'])
                                {
                                    $state_name = $res['state'];
                                }
                            }																
			}
                        if($stateId != '')
			{
                            $citiesmodel = new Default_Model_Cities();
                            $citiesData = $citiesmodel->getBasicCitiesList($stateId);
                            
                            foreach($citiesData as $res) 
                            {
                                $ir_form->city->addMultiOption($res['city_org_id'],utf8_encode($res['city']));
                                if($roundData['int_city'] == $res['city_org_id'])
                                {
                                    $city_name = $res['city'];
                                }
                            }
                            $ir_form->setDefault('state',$stateId);
                            $ir_form->setDefault('city',$cityId);	
                        }
			$interviewer_data = $requi_model->getReportingmanagers('',$loginUserId,'',$deptid,'interviewer');
                        $inter_options = $interviewer_data;
                        $inter_data = $user_model->getUserDataById($roundData['interviewer_id']);
						if(!empty($inter_data['jobtitle_id']))
						{
							$jobttlArr = $jobtitleModel->getsingleJobTitleData($inter_data['jobtitle_id']);
						}
						if(!empty($jobttlArr)  && $jobttlArr != 'norows')
						{
							$jobtitle = ', '.$jobttlArr[0]['jobtitlename'];
							$data['jobtitlename'] = $jobttlArr[0]['jobtitlename'];
						};
						$roundData['interviewer_name'] = $inter_data['userfullname'].$jobtitle;
			$roundData['interview_date'] = sapp_Global::change_date($roundData['interview_date'], 'view');                        
			$roundData['interview_time'] = sapp_Global::change_time($roundData['interview_time'],'view');
			$ir_form->populate($roundData);
			
		// giving only for hr,management and super admin	
		if($loginuserGroup == HR_GROUP || $loginuserGroup == '' || $loginuserGroup == MANAGEMENT_GROUP){
          $ir_form->round_status->addMultiOptions(array('Decision pending' => 'Decision pending','On hold' => 'On hold',));
        }
        
			if($roundData['round_status']!='')
				$ir_form->round_status->setValue($roundData['round_status']);
			$future_rcnt = $interview_round_model->getFutureRoundCnt($roundData['interview_id'],$roundData['interview_round_number']);			
			if($roundData['round_status']!='') 
			{
				if(($loginuserGroup == MANAGER_GROUP || $loginuserGroup == EMPLOYEE_GROUP || $loginuserGroup == SYSTEMADMIN_GROUP) || (($loginuserGroup == HR_GROUP || $loginuserGroup == MANAGEMENT_GROUP) && $loginUserId ==  $roundData['interviewer_id']))
				{
					$elements = $ir_form->getElements();
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
					$ir_form->removeElement('submit');
                                        $cancel_name = 'Close';
				}
				else if($loginuserGroup == HR_GROUP || $loginuserGroup == '' || $loginuserGroup == MANAGEMENT_GROUP)
				{   					
					$elements = $ir_form->getElements();
					if(count($elements)>0)
					{
						if($future_rcnt == 0)
						{
							foreach($elements as $key=>$element)
							{  
								if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments") &&($key != "round_status") &&($key != "submit"))
								{
									$element->setAttrib("disabled", "disabled");                                                                                                                    
								}
							}
						}
						else 
						{
							foreach($elements as $key=>$element)
							{
								if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments"))
								{
									$element->setAttrib("disabled", "disabled");                                                                                                                    
								}
							}
							$ir_form->removeElement('submit');
                                                        $cancel_name = 'Close';
						}
					}					
				}
			}else{				
				if($roundData['interviewer_id'] != $loginUserId && ($loginuserGroup == MANAGER_GROUP || $loginuserGroup == EMPLOYEE_GROUP || $loginuserGroup == SYSTEMADMIN_GROUP))
				{
					$elements = $ir_form->getElements();
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
					$ir_form->removeElement('submit');
                                        $cancel_name = 'Close';
				}				
			}
		   $this->view->deptid = $deptid;
		   $this->view->data = $roundData;
		   $this->view->interviewer_data = $interviewer_data;
		   $this->view->country_name = $country_name;
		   $this->view->state_name = $state_name;
		   $this->view->city_name = $city_name;
                   $this->view->cancel_name = $cancel_name;
			$this->view->form = $ir_form;
                        $this->view->inter_options = $inter_options;
			if($this->getRequest()->getPost())
			{
				$result = $this->save($ir_form);
				$this->view->msgarray = $result; 
				$this->view->messages = $result;	
			}
			$this->view->ermsg = '';
		}else{
			$this->view->ermsg = 'nodata';
		}
		$this->view->loginuserGroup = $loginuserGroup;
		$this->view->loginUserId = $loginUserId;
	}
	
	public function viewpopupAction()
	{
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		} 
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
        $cand_model = new Default_Model_Candidatedetails();  
        $requi_model = new Default_Model_Requisition();
		$jobtitleModel = new Default_Model_Jobtitles();
        $intrvwModel = new Default_Model_Interviewdetails();
        $interview_round_model = new Default_Model_Interviewrounddetails();
        $user_model = new Default_Model_Usermanagement();
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
        }
		$jobtitle = '';
		$deptid = $this->getRequest()->getParam('deptid');		
		if($deptid == '')
		$deptid = $this->getRequest()->getParam('deptidform');	
        $intId = $this->getRequest()->getParam('unitId');	
		$roundId = $this->getRequest()->getParam('id');		
        $ir_form = new Default_Form_Interviewrounds();		        
		$elements = $ir_form->getElements();
		//for timezone
		$systempreferencemodel = new Default_Model_Sitepreference();
		$sitepreferencedata= $systempreferencemodel->getActiveRecord();
		if(!empty($sitepreferencedata[0]['timezone']))
		{
			$ir_form->timezone->setValue($sitepreferencedata[0]['timezone']);
			$ir_form->timezone->setAttrib("disabled", "disabled");
		}
	//giving only for hr,management and super admin
        if($loginuserGroup == HR_GROUP || $loginuserGroup == '' || $loginuserGroup == MANAGEMENT_GROUP){
          $ir_form->round_status->addMultiOptions(array('Decision pending' => 'Decision pending','On hold' => 'On hold',));
        }
        
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
		$intData = $intrvwModel->getReqByintrvwID($intId);
		$roundData = $interview_round_model->getSingleRoundData($roundId);
                $this->view->ermsg = '';
                $edit_flag = 'no';
                if(($roundData['interviewer_id'] == $loginUserId) && (empty($roundData['round_status'])))
                    $edit_flag = 'yes';
        if($loginuserGroup == MANAGER_GROUP || $loginuserGroup == HR_GROUP || $loginuserGroup == MANAGEMENT_GROUP || $loginuserGroup == '' || $loginuserGroup == EMPLOYEE_GROUP || $loginuserGroup == SYSTEMADMIN_GROUP)
		{	
			$countryId = $roundData['int_country'];$stateId = $roundData['int_state'];$cityId = $roundData['int_city'];
			$country_name = '';$state_name = '';$city_name = '';
			if($countryId && $stateId)
			{
				$statesmodel = new Default_Model_States();
				$citiesmodel = new Default_Model_Cities();
				$countryModal = new Default_Model_Countries();
				$countriesData = $countryModal->fetchAll('isactive=1','country');				
				foreach($countriesData as $cdata)
				{
					if($roundData['int_country'] == $cdata['country_id_org'])
					{
						$country_name = $cdata['country'];
						break;
					}
				}
				$statesData = $statesmodel->getBasicStatesList($countryId);
				$citiesData = $citiesmodel->getBasicCitiesList($stateId);
				foreach($statesData as $res)
				{
					$ir_form->state->addMultiOption($res['state_id_org'],utf8_encode($res['state']));
					if($roundData['int_state'] == $res['state_id_org'])
					{
						$state_name = $res['state'];
					}
				}
				foreach($citiesData as $res) 
				{
					$ir_form->city->addMultiOption($res['city_org_id'],utf8_encode($res['city']));
					if($roundData['int_city'] == $res['city_org_id'])
					{
						$city_name = $res['city'];
					}
				}
			}
           
			$interviewer_data = $user_model->getUserDataById($roundData['interviewer_id']);
			if(!empty($interviewer_data['jobtitle_id']))
			{
				$jobttlArr = $jobtitleModel->getsingleJobTitleData($interviewer_data['jobtitle_id']);
			}
			if(!empty($jobttlArr)  && $jobttlArr != 'norows')
			{
				$jobtitle = ', '.$jobttlArr[0]['jobtitlename'];
				$data['jobtitlename'] = $jobttlArr[0]['jobtitlename'];
			};
			$interviewer_name= $interviewer_data['userfullname'].$jobtitle;
			
			$ir_form->interviewer_id->addMultiOptions(array(''=>$interviewer_data['userfullname'].$jobtitle));
			
			$roundData['interview_date'] = sapp_Global::change_date($roundData['interview_date'], 'view');                       
			$roundData['interview_time'] = sapp_Global::change_time($roundData['interview_time'],'view');
			$ir_form->populate($roundData);
			$this->view->form = $ir_form;
			$this->view->intId = $intId;
			$this->view->deptid = $deptid;
			$this->view->roundId = $roundId;
			$this->view->interviewer_name = $interviewer_name;
			$this->view->ermsg = '';
			$this->view->country_name = $country_name;
		    $this->view->state_name = $state_name;
		    $this->view->city_name = $city_name;
		    $this->view->edit_flag = $edit_flag;
		}else{
			$this->view->ermsg = 'nodata';
		}
	}
	
	public function save($form)
	{
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		} 
                $requi_model = new Default_Model_Requisition();
		$round_model = new Default_Model_Interviewrounddetails();
		$intrvwModel = new Default_Model_Interviewdetails();
                $user_model = new Default_Model_Usermanagement();
                $cand_model = new Default_Model_Candidatedetails();
		$intrvwId = $this->getRequest()->getParam('unitId');		
		if($form->isValid($this->_request->getPost()))
		{
			$reqData = $intrvwModel->getReqByintrvwID($intrvwId);
			if(!empty($reqData))
			{
				$reqId = $reqData['req_id'];
				$candid = $reqData['candidate_id'];
				$id = $this->getRequest()->getParam('id');
				$interview_id = $intrvwId;
				$interviewer_id = $this->getRequest()->getParam('interviewer_id');
				$interview_round = $this->getRequest()->getParam('interview_round');
				$interview_mode = $this->getRequest()->getParam('interview_mode');
				$int_location = $this->getRequest()->getParam('int_location');
				$interview_time = $this->getRequest()->getParam('interview_time');
				$interview_date = $this->getRequest()->getParam('interview_date');
				$interview_feedback = $this->getRequest()->getParam('interview_feedback');
				$interview_comments = $this->getRequest()->getParam('interview_comments');
				$round_status = $this->getRequest()->getParam('round_status',null);
                                $hid_round_status = $this->getRequest()->getParam('hid_round_status',null);
				
				$data = array(
					'candidate_id'   => $candid,
					'req_id' 		=>$reqId,
					'interview_id' => trim($intrvwId),
					'interviewer_id' => trim($interviewer_id),
					'interview_mode' => trim($interview_mode),
					'int_location' => trim($int_location),
					'int_country' => trim($this->_getParam('country',null)),
					'int_state'	=>trim($this->_getParam('state',null)),
					'int_city'	=>trim($this->_getParam('city',null)),			
					'interview_time' => sapp_Global::change_time(trim($interview_time),'database'),
					'interview_date' => sapp_Global::change_date(trim($interview_date),'database'),
					'interview_round' =>trim($interview_round),
					'interview_feedback' => trim($interview_feedback),
					'interview_comments' => trim($interview_comments),
					'round_status' => trim($round_status),				
					'isactive' => 1,
					'createdby' => trim($loginUserId),
					'modifiedby' => trim($loginUserId),
					'createddate' => gmdate("Y-m-d H:i:s"),
					'modifieddate' => gmdate("Y-m-d H:i:s"),
				);
				$roundnumberData = $intrvwModel->getinterviewroundnumber($interview_id);
				$roundnumber = $roundnumberData['interview_round_number'];
				$data['interview_round_number'] = $roundnumber+1;
				
				$where = "";
				$actionflag = 1;
				if($id != '')
				{					
                                    unset($data['createdby']);
                                    unset($data['createdon']);
                                    unset($data['isactive']);
                                    unset($data['interview_round_number']);
                                    $where = array("id = ".$id);
                                    $tableid = $id;
                                    $actionflag = 2;
                                    if($hid_round_status != '')
                                    {
                                        $data = array();
                                        $data['round_status'] = trim($round_status);
                                    }
				}
				if(($loginuserGroup == MANAGER_GROUP || $loginuserGroup == SYSTEMADMIN_GROUP || $loginuserGroup == EMPLOYEE_GROUP)|| (($loginuserGroup == HR_GROUP || $loginuserGroup == MANAGEMENT_GROUP) && $interviewer_id == $loginUserId))
				{
					unset($data['interviewer_id']);
					unset($data['interview_mode']);
					unset($data['int_location']);
					unset($data['int_country']);
					unset($data['int_state']);
					unset($data['int_city']);
					unset($data['interview_time']);
					unset($data['interview_date']);
					unset($data['interview_round']);
				}
				$result = $round_model->SaveorUpdateInterviewroundData($data, $where);
				if($id == '')
				{
                                    //start of mailing
                                        $requisition_data = $requi_model->getRequisitionDataById($reqId);  
                                        $cand_data = $cand_model->getCandidateById($candid);
                                       
                                        $report_person_data = $user_model->getUserDataById($interviewer_id);
                                        $mail_arr = array(  'HR'=>  defined('REQ_HR_'.$requisition_data['businessunit_id'])?constant('REQ_HR_'.$requisition_data['businessunit_id']):"",                                                        
                                        $report_person_data['userfullname'] => $report_person_data['emailaddress']
                                            );
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
                                            $text = $view->render('mailtemplates/interviewrounds.phtml');
                                            $options['subject'] = APPLICATION_NAME.': Interview schedule';
                                            $options['header'] = 'Interview schedule';
                                            $options['toEmail'] = $email;  
                                            $options['toName'] = $ename;
                                            $options['message'] = $text;
                                            $options['cron'] = 'yes';
                                            sapp_Global::_sendEmail($options);
                                        }
                                    
                                    //end of mailing
					$tableid = $result;
					$this->view->eventact = 'scheduled';
				}
				else
				{
                                    if($loginuserGroup == MANAGER_GROUP || $loginuserGroup == SYSTEMADMIN_GROUP || $loginuserGroup == EMPLOYEE_GROUP || $loginuserGroup == MANAGEMENT_GROUP)
                                    {
                                        $requisition_data = $requi_model->getRequisitionDataById($reqId);  
                                        $cand_data = $cand_model->getCandidateById($candid);
                                        $round_data = $round_model->getSingleRoundData($id);
                                       
                                        $report_person_data = $user_model->getUserDataById($loginUserId);
                                        $mail_arr = array(  'HR'=>  defined('REQ_HR_'.$requisition_data['businessunit_id'])?constant('REQ_HR_'.$requisition_data['businessunit_id']):"",                                                        
                                                            $report_person_data['userfullname'] => $report_person_data['emailaddress']
                                            );
                                        foreach($mail_arr as $ename => $email)
                                        {
                                            $base_url = 'http://'.$this->getRequest()->getHttpHost() . $this->getRequest()->getBaseUrl();
                                            $view = $this->getHelper('ViewRenderer')->view;
                                            $this->view->emp_name = $ename;                           
                                            $this->view->base_url=$base_url;
                                            $this->view->candidate_name = $cand_data['candidate_name'];
                                            $this->view->interview_type = $round_data['interview_mode'];
                                            $this->view->interview_location = $round_data['int_location'];
                                            $this->view->interview_date = sapp_Global::change_date($round_data['interview_date'],'view');                                            
                                            $this->view->interview_time = sapp_Global::change_time($round_data['interview_time'],'view');
                                            $this->view->interview_feedback = $interview_feedback;
                                            $this->view->interview_comments = $interview_comments;
                                            $this->view->round_status = $round_status;
                                            $this->view->reporting_person = $report_person_data['userfullname'];
                                            $text = $view->render('mailtemplates/interviewrounds_feedback.phtml');
                                            $options['subject'] = APPLICATION_NAME.': Interview feedback';
                                            $options['header'] = 'Interview feedback';
                                            $options['toEmail'] = $email;  
                                            $options['toName'] = $ename;
                                            $options['message'] = $text;
                                            $options['cron'] = 'yes';
                                            sapp_Global::_sendEmail($options);
                                        }
                                    }
					$tableid = $id;
					$this->view->eventact = 'updated';
				}
				$menumodel = new Default_Model_Menu();
				$objidArr = $menumodel->getMenuObjID('/scheduleinterviews');
				$objID = $objidArr[0]['id'];
				$result = sapp_Global::logManager($objID,$actionflag,$loginUserId,$intrvwId);
				$close = 'close';
				$this->view->popup=$close;	
				$this->view->ermsg = '';
			}
			else $this->view->ermsg='nodata';
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
		if($auth->hasIdentity()){
				$loginUserId = $auth->getStorage()->read()->id;
			}
		 $id = $this->_request->getParam('objid');
		 $messages['message'] = '';$messages['msgtype'] = '';
		 $actionflag = 3;
		if($id)
		{
			$round_model = new Default_Model_Interviewrounddetails();
			$data = array('isactive'=>0);
			$where = array('id=?'=>$id);
			$Id = $round_model->SaveorUpdateInterviewroundData($data, $where);
			if($Id == 'update')
			{
				$menuID = SCHEDULEINTERVIEWS;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id); 
			    $messages['message'] = 'Round details deleted successfully';
				$messages['msgtype'] = 'success';
				$messages['flagtype'] = 'process';
			}   
			else
			{
			    $messages['message'] = 'Round details cannot be deleted';				
				$messages['msgtype'] = 'error';
				$messages['flagtype'] = 'process';
			}
		}
		else
		{ 
		    $messages['message'] = 'Round details cannot be deleted';
			$messages['msgtype'] = 'error';
			$messages['flagtype'] = 'process';
		}
		$this->_helper->json($messages);
	}
    
}//end of class

