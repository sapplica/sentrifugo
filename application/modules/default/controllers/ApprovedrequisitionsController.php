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

class Default_ApprovedrequisitionsController extends Zend_Controller_Action
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
        $requi_model = new Default_Model_Requisition();	
        $appr_model = new Default_Model_Approvedrequisitions();
        $call = $this->_getParam('call');
        if($call == 'ajaxcall')
            $this->_helper->layout->disableLayout();		
        
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
                $queryflag = 'Approved';
        $refresh = $this->_getParam('refresh');
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
            $sort = 'DESC';$by = 'r.modifiedon';$pageNo = 1;$searchData = '';$searchQuery = '';$searchQuery = '';$searchArray='';
            $searchArray = array();
        }
        else 
        {
            $sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'r.modifiedon';
            if($dashboardcall == 'Yes')
				$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
			else $perPage = $this->_getParam('per_page',PERPAGE);
            $pageNo = $this->_getParam('page', 1);
            /** search from grid - START **/
            $searchData = $this->_getParam('searchData');	
            
            /** search from grid - END **/
        }

        $dataTmp =  $appr_model->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$statusid,$a1='',$a2='',$a3='');
        array_push($data,$dataTmp);
        $this->view->dataArray = $dataTmp;
        $this->view->call = $call ;
        $this->view->statusidstring = $statusidstring;
        $this->view->messages = $this->_helper->flashMessenger->getMessages();	
    }

	
    public function viewAction()
    {
        $id = $this->getRequest()->getParam('id');
        $requi_model = new Default_Model_Requisition();		
		$clientsModel = new Default_Model_Clients();    
		$usersModel = new Default_Model_Users();            
        $data = array();
        try
        {
            if($id >0 && is_numeric($id))
            {			
                $data = $requi_model->getReqDataForView($id);                                        
               
			   if(count($data) >0  && $data[0]['req_status'] != 'Initiated' && $data[0]['req_status'] != 'Rejected')
                {
                    $data = $data[0];
                    $auth = Zend_Auth::getInstance();
                    if($auth->hasIdentity())
                    {
                        $loginUserId = $auth->getStorage()->read()->id;
                        $loginuserRole = $auth->getStorage()->read()->emprole;
                        $loginuserGroup = $auth->getStorage()->read()->group_id;
                    }
                    												 
                    $data['jobtitlename'] = '';			
                    $data['businessunit_name'] = $data['businessunit_name'];									
                    $data['dept_name'] = $data['department_name'];									
                    $data['titlename'] = $data['jobtitle_name'];									
                    $data['posname'] = $data['position_name'];									
                    $data['empttype'] = $data['emp_type_name'];						                       
                    $data['mngrname'] = $data['reporting_manager_name'];						
                    $data['raisedby'] = $data['createdby_name'];			                        
                    $data['app1_name'] = $data['approver1_name'];
                        
                    if($data['approver2'] != '')
                    {                        
                        $data['app2_name'] = $data['approver2_name'];
                    }
                    else 
                    {
                        $data['app2_name'] = 'No Approver';
                    }
                        
                    if($data['approver3'] != '')
                    {                        
                        $data['app3_name'] = $data['approver3_name'];
                    }
                    else 
                    {
                        $data['app3_name'] = 'No Approver';
                    }                        
					if($data['client_id'] != '')
					{
						$clien_data = $clientsModel->getClientDetailsById($data['client_id']);
					    $data['client_id']=$clien_data[0]['client_name'];
					}  
					if($data['recruiters'] != '')
					{
						$name = '';
						$recData=$usersModel->getUserDetailsforView($data['recruiters']);
						if(count($recData)>0)
						{
							foreach($recData as $dataname){
								$name = $name.','.$dataname['name'];
							}

						}
						$data['recruiters']=ltrim($name,',');
					}    
                    /* foreach($data as $key=>$val)
                    {
                        $data[$key] = htmlentities($val, ENT_QUOTES, "UTF-8");
                    }	 */            
                    $data['onboard_date'] = sapp_Global::change_date($data['onboard_date'], 'view');
			
                    $sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
                    $by = ($this->_getParam('by')!='')? $this->_getParam('by'):'c.createddate';
                    $perPage = $this->_getParam('per_page',10);
                    $pageNo = $this->_getParam('page', 1);            
                    $searchQuery = '';
                    $searchArray = array();
                    $tablecontent='';
                    /** search from grid - START **/
                    $searchData = $this->_getParam('searchData');	
                    if($searchData != '' && $searchData!='undefined')
                    {
                        $searchValues = json_decode($searchData);
                        if(count($searchValues) >0)
                        {
                            foreach($searchValues as $key => $val)
                            {
                                $searchQuery .= " ".$key." like '%".$val."%' AND ";
                                $searchArray[$key] = $val;
                            }
                            $searchQuery = rtrim($searchQuery," AND");					
                        }                    
                    }
                    /** search from grid - END **/
                    $objName = 'apprreqcandidates';
                    $tableFields = array('action'=>'Action',
                                         'candidate_name' => 'Candidate Name',
                                         'cand_status' => 'Candidate Status',
                                        );
                    $candidate_model = new Default_Model_Candidatedetails();
                    $tablecontent = $candidate_model->getCandidatesData_requisition($sort, $by, $pageNo, $perPage,$searchQuery,$id);     
                    $cand_status_opt = array('' => 'All','Shortlisted' => 'Shortlisted','Selected' => 'Selected','Rejected' => 'Rejected',
                                            'On hold' => 'On hold','Disqualified' => 'Disqualified','Scheduled' => 'Scheduled',
                                            'Not Scheduled' => 'Not Scheduled','Recruited' => 'Recruited','Requisition Closed/Completed' => 'Requisition Closed/Completed');
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
                            'formgrid' => 'true',
                            'searchArray' => $searchArray,
                            'menuName' => 'Candidate details',
                            'call'=>'',
                            'search_filters' => array(
                                'cand_status' => array(
                                    'type' => 'select',
                                    'filter_data' => $cand_status_opt,
                                ),
                            ),
                    );	
                if($data['req_priority'] == 1) {
                    	$data['req_priority']='High';
                    }else if($data['req_priority'] == 2) {
                    	$data['req_priority']='Medium';
                    }else {
                    $data['req_priority']='Low';
                    }		
                    array_push($data,$dataTmp);
					//to show requisition history in view
                    $reqh_model = new Default_Model_Requisitionhistory();
	                $requisition_history = $reqh_model->getRequisitionHistory($id);
					$this->view->controllername = "approvedrequisitions";
                    $this->view->dataArray = $dataTmp;
                    $this->view->data = $data;
                    $this->view->loginuserGroup = $loginuserGroup;
					$this->view->requisition_history = $requisition_history;
					$this->view->id = $id;		
                }
                else 
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
	
	public function editAction()
	{
		$id = $this->getRequest()->getParam('id');
		$data = array();$jobtitle = '';
		$auth = Zend_Auth::getInstance();
            if($auth->hasIdentity())
            {
                $loginUserId = $auth->getStorage()->read()->id;
                            $loginuserRole = $auth->getStorage()->read()->emprole;
                            $loginuserGroup = $auth->getStorage()->read()->group_id;
            }		
		$form = new Default_Form_Requisition();  
		$requi_model = new Default_Model_Requisition();  
        $user_model = new Default_Model_Usermanagement();
		$clientsModel = new Default_Model_Clients();
		$usersModel = new Default_Model_Users();
		$jobtitleModel = new Default_Model_Jobtitles();		
		$form->setAttrib('action',BASE_URL.'approvedrequisitions/edit/id/'.$id);	
		$form->submit->setLabel('Update'); 
		$elements = $form->getElements();
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)				{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")&&($key!="submit")&&($key!='req_status')&&($key!='onboard_date')){
					$form->removeElement($key);
				}
			}
		}
		try{
                    if($id>0 && is_numeric($id))
                    {
			$data = $requi_model->getRequisitionDataById($id);  
			if(count($data)>0 && $data['req_status'] != 'Initiated' && $data['req_status'] != 'Rejected')
                        {
			$data['jobtitlename'] = '';
			$business_units_list = $requi_model->getBusinessUnitsList($data['businessunit_id']);
			$data['businessunit_name']	=	$business_units_list['unitname'];			
			
			$departments_list = $requi_model->getDepartmentList($data['businessunit_id'],$data['department_id']);			
			$data['dept_name'] = $departments_list['deptname'];			
			
			$job_data = $requi_model->getJobTitleList($data['jobtitle']);
			$data['titlename'] = $job_data['jobtitlename'];			
			
			$pos_data = $requi_model->getPositionOptions($data['jobtitle'],$data['position_id']);
			$data['posname'] = $pos_data['positionname'];			
			
			$emptype_options = $requi_model->getEmpStatusOptions($data['emp_type']); 
			$data['empttype'] = $emptype_options['employemnt_status'];
						
                        $report_manager_options = $user_model->getUserDataById($data['reporting_id']);
			$data['mngrname'] = $report_manager_options['userfullname'];
			
			$raisedby = $requi_model->getrequisitioncreatername($data['createdby']);
			$data['raisedby'] = $raisedby['userfullname'];
			
                        $app1_data = $user_model->getUserDataById($data['approver1']);
                        $data['app1_name'] = $app1_data['userfullname'];
                        
                        if($data['approver2'] != '')
                        {
                            $app2_data = $user_model->getUserDataById($data['approver2']);
                            $data['app2_name'] = $app2_data['userfullname'];
                        }
                        else 
                        {
                            $data['app2_name'] = 'No Approver';
                        }
                        
                        if($data['approver3'] != '')
                        {
                            $app3_data = $user_model->getUserDataById($data['approver3']);
                            $data['app3_name'] = $app3_data['userfullname'];
                        }
                        else 
                        {
                            $data['app3_name'] = 'No Approver';
                        }
					if($data['client_id'] != '')
					{
						$clien_data = $clientsModel->getClientDetailsById($data['client_id']);
					    $data['client_id']=$clien_data[0]['client_name'];
					}  
					if($data['recruiters'] != '')
					{
						$name = '';
						$recData=$usersModel->getUserDetailsforView($data['recruiters']);
						if(count($recData)>0)
						{
							foreach($recData as $dataname){
								$name = $name.','.$dataname['name'];
							}

						}
						$data['recruiters']=ltrim($name,',');
					}     
                        
			$jobttlArr = $jobtitleModel->getsingleJobTitleData($data['jobtitle']);
			if(!empty($jobttlArr) && $jobttlArr != 'norows')
			{
				$jobtitle = $jobttlArr[0]['jobtitlename'];
				$data['jobtitlename'] = $jobttlArr[0]['jobtitlename'];
			}
			
			foreach($data as $key=>$val)
			{
				//$data[$key] = htmlentities(($val), ENT_QUOTES, "UTF-8");
			}
                        $onboard_date_org = $data['onboard_date'];
			$data['onboard_date'] = sapp_Global::change_date($data['onboard_date'], 'view');
			if($data['req_status'] == 'Approved')
			{
				$form->req_status->addMultiOptions(array(
								'Approved'				=>		'Approved',
								'Closed'		=>		'Closed',
								'On hold'		=> 		'On hold',
								'Complete'		=> 		'Complete'							
									));
			}else if($data['req_status'] == 'Complete'){
				$form->req_status->addMultiOptions(array(
								'Complete'		=> 		'Complete'							
							));
				$form->req_status->setAttrib('disabled','disabled');
				$form->removeElement('submit');
			}else{
				$form->req_status->addMultiOptions(array(
								''				=>		'Select status',
								'Closed'		=>		'Closed',
								'On hold'		=> 		'On hold',
								'Complete'		=> 		'Complete',
                                                                'In process' => 'In process',
									));
			}
			$form->req_status->setRequired(true);
			$form->req_status->addValidator('NotEmpty', false, array('messages' => 'Please select option.'));
			
			$form->populate($data);	
			$form->setDefault('req_status',$data['req_status']);		
			
			$this->view->data = $data;
			$this->view->loginuserGroup = $loginuserGroup;
			$this->view->form = $form; 
                        $this->view->id = $id;
                        $this->view->edit_duedate = 'no';
                        
                        if( ($onboard_date_org < date('Y-m-d')) && ($loginuserGroup == HR_GROUP || $loginuserGroup == MANAGEMENT_GROUP || $loginuserGroup == ''))
                        {
                            $this->view->edit_duedate = 'yes';
                        }
                        else
                        {
                            $form->removeElement('onboard_date');
                        }
                        $sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
                        $by = ($this->_getParam('by')!='')? $this->_getParam('by'):'c.createddate';
                        $perPage = $this->_getParam('per_page',10);
                        $pageNo = $this->_getParam('page', 1);            
                        $searchQuery = '';
                        $searchArray = array();
                        $tablecontent='';
                        /** search from grid - START **/
                        $searchData = $this->_getParam('searchData');	
                        if($searchData != '' && $searchData!='undefined')
                        {
                            $searchValues = json_decode($searchData);
                            if(count($searchValues) >0)
                            {
                                foreach($searchValues as $key => $val)
                                {
                                        $searchQuery .= " ".$key." like '%".$val."%' AND ";
                                        $searchArray[$key] = $val;
                                }
                                $searchQuery = rtrim($searchQuery," AND");					
                            }                    
                        }
                        /** search from grid - END **/


                        $objName = 'apprreqcandidates';

                        $tableFields = array('action'=>'Action',
                                             'candidate_name' => 'Candidate Name',
                                             'cand_status' => 'Candidate Status',
                                            );
                        $candidate_model = new Default_Model_Candidatedetails();
                        $tablecontent = $candidate_model->getCandidatesData_requisition($sort, $by, $pageNo, $perPage,$searchQuery,$id);     
                        $cand_status_opt = array('' => 'All','Shortlisted' => 'Shortlisted','Selected' => 'Selected','Rejected' => 'Rejected',
                                                'On hold' => 'On hold','Disqualified' => 'Disqualified','Scheduled' => 'Scheduled',
                                                'Not Scheduled' => 'Not Scheduled','Recruited' => 'Recruited','Requisition Closed/Completed' => 'Requisition Closed/Completed');
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
                                'formgrid' => 'true',
                                'searchArray' => $searchArray,
                                'menuName' => 'Candidate details',
                                'call'=>'',
                                'search_filters' => array(
                                    'cand_status' => array(
                                        'type' => 'select',
                                        'filter_data' => $cand_status_opt,
                                    ),
                                ),
                                
                        );			
                        array_push($data,$dataTmp);
                        $this->view->dataArray = $dataTmp;
			if($this->getRequest()->getPost())
			{
				$result = $this->save($form,$data);	
				$this->view->msgarray = $result; 
				$this->view->messages = $result;				
			}
			$this->view->nodata = '';
                        }
                        else 
                        {
                            $this->view->nodata = 'nodata';	
                        }
		}
                else 
                {
                    $this->view->nodata = 'nodata';		
                }
                }
                catch(Exception $e){
			$this->view->nodata = 'nodata';		
		}
		
	}
	
	public function save($requisitionform,$data)
	{
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		} 
		$requi_model = new Default_Model_Requisition(); 
                $user_model = new Default_Model_Usermanagement();
                
		$req_status	 = $this->_getParam('req_status',null);		
		$flag = 'true';
		if($requisitionform->isValid($this->_request->getPost()) && $flag != 'false')
		{
			$id = $this->_getParam('id',null);
			$req_status	 = $this->_getParam('req_status',null);	
			$onboard_date = $this->_getParam('onboard_date',null);
			$data = array(
					'req_status' 		=>	$req_status,
					'modifiedby' 		=> 	trim($loginUserId),
					'modifiedon' 		=> 	gmdate("Y-m-d H:i:s")
				);
                        if($onboard_date != '')
                        {
                            $data = $data + array('onboard_date' => sapp_Global::change_date($onboard_date,'database'));
                        }
			$where = "id = ".$id;
                        
			$result = $requi_model->SaveorUpdateRequisitionData($data, $where); 
			   //for Requisition history
				if($result == 'update')
				{
                    $requisition_id=$id;
 					$history = 'Requisition status has been changed as'.' '.$req_status.' '.'by ';
                    $createdby = $loginUserId;
					$modifiedby=$loginUserId;
				}
		       $reqh_model = new Default_Model_Requisitionhistory();
				$requisition_history = array(											
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
			//end
			$tableid = $id;
			$actionflag = 2;
			if($result != '')
			{
                            if($req_status == 'Complete' || $req_status == 'Closed')
                            {
                                $requi_model->change_to_requisition_closed($id);                                
                                $requisition_data = $requi_model->getReqDataForView($id);         
                                $requisition_data = $requisition_data[0];
                                $report_person_data = $user_model->getUserDataById($requisition_data['createdby']);
                                $closed_person_data = $user_model->getUserDataById($loginUserId);
                                $mail_arr[0]['name'] = 'HR';
                                $requisition_data['businessunit_id'];
                                $mail_arr[0]['email'] = defined('REQ_HR_'.$requisition_data['businessunit_id'])?constant('REQ_HR_'.$requisition_data['businessunit_id']):"";
                                $mail_arr[0]['type'] = 'HR';
                                $mail_arr[1]['name'] = 'Management';
                                $mail_arr[1]['email'] = defined('REQ_MGMT_'.$requisition_data['businessunit_id'])?constant('REQ_MGMT_'.$requisition_data['businessunit_id']):"";
                                $mail_arr[1]['type'] = 'Management';
                                $mail_arr[2]['name'] = $report_person_data['userfullname'];
                                $mail_arr[2]['email'] = $report_person_data['emailaddress'];
                                $mail_arr[2]['type'] = 'Raise';
                                for($ii =0;$ii<count($mail_arr);$ii++)
                                {
                                    $base_url = 'http://'.$this->getRequest()->getHttpHost() . $this->getRequest()->getBaseUrl();
                                    $view = $this->getHelper('ViewRenderer')->view;
                                    $this->view->emp_name = $mail_arr[$ii]['name'];                           
                                    $this->view->base_url=$base_url;
                                    $this->view->type = $mail_arr[$ii]['type'];
                                    $this->view->requisition_code = $requisition_data['requisition_code'];
                                    $this->view->req_status = $status = strtolower(($req_status=='Complete')?"Completed":$req_status);
                                    $this->view->raised_name = $report_person_data['userfullname'];
                                    $this->view->approver_str = $closed_person_data['userfullname'];
                                    $text = $view->render('mailtemplates/changedrequisition.phtml');
                                    $options['subject'] = APPLICATION_NAME.': Requisition is '.$status;
                                    $options['header'] = 'Requisition is '.$status;
                                    $options['toEmail'] = $mail_arr[$ii]['email'];  
                                    $options['toName'] = $mail_arr[$ii]['name'];
                                    $options['message'] = $text;
                                    $options['cron'] = 'yes';
                                    sapp_Global::_sendEmail($options);
                                }
                            }
				$menumodel = new Default_Model_Menu();
				$objidArr = $menumodel->getMenuObjID('/approvedrequisitions');
				$objID = $objidArr[0]['id'];
				$result = sapp_Global::logManager($objID,$actionflag,$loginUserId,$tableid);
				if($id != '')
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Requisition updated successfully."));
				else
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Requisition added successfully."));
				$this->_redirect('/approvedrequisitions');
			}
		}
		else
		{
			$messages = $requisitionform->getMessages();
			$msgarray = array();
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
}