<?php
/********************************************************************************* 
 *  This file is part of Sentrifugo.
 *  Copyright (C) 2014 Sapplica
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

class Default_FeedforwardinitController extends Zend_Controller_Action
{
    private $options;
    public function preDispatch()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('getdept', 'json')->initContext();
        $ajaxContext->addActionContext('getappraisaldetails', 'html')->initContext();
    }
	
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
    }

    
	public function indexAction()
    {
		$feedforwardInitModel = new Default_Model_Feedforwardinit();	
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
			$sort = 'DESC';$by = 'fi.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else 
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'fi.modifieddate';
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
				
		$dataTmp = $feedforwardInitModel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);		 		
					
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
		$this->render('commongrid/performanceindex', null, true);		
    }
	
    public function addAction()
    {
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $businessunit_id = $auth->getStorage()->read()->businessunit_id;
            $department_id = $auth->getStorage()->read()->department_id;            
        }
        $callval = $this->getRequest()->getParam('call');
        if($callval == 'ajaxcall')
            $this->_helper->layout->disableLayout();

        $errorMsg = '';
		$feedforwardInitModel = new Default_Model_Feedforwardinit();
        $feedforwardInitForm = new Default_Form_Feedforwardinit();
        
        $appDataWithoutFF = $feedforwardInitModel->getAppDataForFF('yes');
    	foreach ($appDataWithoutFF as $appdata){
			$feedforwardInitForm->appraisal_mode->addMultiOption($appdata['id'],$appdata['app_mode']);
		}
        
        $getQuestions = $feedforwardInitModel->getQuestionsFeedforward();
      	$this->view->getQuestions = $getQuestions;
        
        $msgarray = array();
        $msgarray['check_array'] = array();
        $feedforwardInitForm->setAttrib('action',DOMAIN.'feedforwardinit/add');
        $this->view->form = $feedforwardInitForm; 
        $this->view->msgarray = $msgarray; 
        $this->view->ermsg = $errorMsg;
		
        if($this->getRequest()->getPost())
        {
            $result = $this->save($feedforwardInitForm);	
            $this->view->msgarray = $result; 
        }
        $this->render('form');	
    }//end of add action
    
    public function getappraisaldetailsAction(){
    	$id = $this->_request->getParam('app_id');
    	$appraisalinitmodel = new Default_Model_Appraisalinit();
		$data = $appraisalinitmodel->getappdata_forview($id);
        $this->view->appData = $data;
    }

	public function save($feedforwardInitForm)
    {
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
        } 
        $feedforwardInitModel = new Default_Model_Feedforwardinit();
        
        $msgarray = array();
        $check_array = array();
       				
		$id = $this->_request->getParam('id');
        $appraisal_mode = $this->_request->getParam('appraisal_mode');
        $ff_due_date = sapp_Global::change_date($this->_request->getParam('ff_due_date'),'database');
        $employee_name_view = $this->_request->getParam('employee_name_view');
        $enable_to = $this->_request->getParam('enable_to');
		$initialize_status = $this->_request->getParam('initialize_status'); 
		$status = $this->_request->getParam('status');
		
		$x_init_status = $this->_request->getParam('x_init_status');
		
		$check = $this->_request->getParam('check');
		$empcmnt = $this->_request->getParam('empcmnt');
		
		if($empcmnt)
			$empcmnt_keys = array_keys($empcmnt);
		else
			$empcmnt_keys = array();
    	
		if(sizeof($check)>0){
			foreach($check as $qid){
				if(in_array($qid, $empcmnt_keys))
					$check_array[$qid] = array('EC'=>1,'ER'=>1);
				else
					$check_array[$qid] = array('EC'=>0,'ER'=>1);
			}								
		}
		
		if(in_array(1, $enable_to))
			$enable_to = 1;
		else
			$enable_to = 0;

		if($x_init_status != 1)
		{
	        if($feedforwardInitForm->isValid($this->_request->getPost()))
	        {
	            try
	            {                
	              	$menumodel = new Default_Model_Menu();
					$actionflag = '';
					$tableid  = '';
					$appraisal_id  = '';
	
				   	$data = array(
	                                            'ff_due_date'=> $ff_due_date,
				   								'employee_name_view' => $employee_name_view,
				   								'enable_to'=>$enable_to,						   								   	
	                                            'status'=>$status,
				   								'initialize_status' => $initialize_status,
					   							'questions'=>($check!='')?implode(',',$check):NULL,
		                                    	'qs_privileges'=>(count($check_array)>0)?json_encode($check_array,true):NULL,
	                                            'modifiedby'=>$loginUserId,
	                                            'modifiedby_role'=>$loginuserRole,
	                                            'modifiedby_group'=>$loginuserGroup,
	                                            'modifieddate'=>gmdate("Y-m-d H:i:s")
						);									
						
					if($id!=''){
						$where = array('id=?'=>$id);  
						$actionflag = 2;
						$appraisal_id = $this->_request->getParam('appraisal_id');
					}
					else
					{	
						$appraisalinitmodel = new Default_Model_Appraisalinit();
						$appData = $appraisalinitmodel->getappdata_forview($appraisal_mode);
						
						$data['pa_configured_id']=$appData['pa_configured_id'];
						$data['businessunit_id']=$appData['businessunit_id'];
	                    $data['department_id']=$appData['department_id'];
	                    $data['ff_mode']=$appData['appraisal_mode'];
	                    $data['ff_period']=$appData['appraisal_period'];
	                    $data['ff_from_year']=$appData['from_year'];
	                    $data['ff_to_year']=$appData['to_year'];					
	                    $data['appraisal_id']=$appraisal_mode;
						
						$data['createdby_role'] = $loginuserRole;
						$data['createdby_group'] = $loginuserGroup;					
						$data['createdby'] = $loginUserId;
						$data['createddate'] = gmdate("Y-m-d H:i:s");
						$data['isactive'] = 1;
						$where = '';
						$actionflag = 1;
						$appraisal_id = $appraisal_mode;
					}
					$Id = $feedforwardInitModel->SaveorUpdateFeedforwardInitData($data, $where);
					
					if($Id == 'update')
					{
					   $tableid = $id;
					   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Initialization updated successfully."));
					}   
					else
					{
					   	$tableid = $Id;
						$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Initialization added successfully."));					   
					}
	
					if($initialize_status == 1)
					{
						sapp_PerformanceHelper::update_QsParmas_Allemps($data['questions'],'');
						$this->ffinitialize($appraisal_id,$tableid,$enable_to,$check);
					}	
					
					$menuidArr = $menumodel->getMenuObjID('/feedforwardinit');
					$menuID = $menuidArr[0]['id'];
					$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
	                                
					$this->_redirect('feedforwardinit');	
				}
	        	catch(Exception $e)
	          	{	
	            	$msgarray['appraisal_mode'] = "Something went wrong, please try again.";
	             	return $msgarray;
	          	}
			} else {
				$messages = $feedforwardInitForm->getMessages();
				foreach ($messages as $key => $val)
					{
						foreach($val as $key2 => $val2)
						 {
							$msgarray[$key] = $val2;
							break;
						 }
					}
				$msgarray['check_array'] = $check_array;	
				return $msgarray;	
			}	
		} else {
			$data = array('status'=>$status,'ff_due_date'=> $ff_due_date,);
			$where = array('id=?'=>$id);
			$Id = $feedforwardInitModel->SaveorUpdateFeedforwardInitData($data, $where);
			if($status == 2){
				if($Id == 'update')
				{
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Initialization closed successfully."));
					$this->_redirect('feedforwardinit');
				}
			}else{
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Initialization updated successfully."));
				$this->_redirect('feedforwardinit');
			}
		}
	}
	
	public function editAction()
    {	
    	$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $businessunit_id = $auth->getStorage()->read()->businessunit_id;
            $department_id = $auth->getStorage()->read()->department_id;            
        }
	 	
        $id = $this->getRequest()->getParam('id');
        $callval = $this->getRequest()->getParam('call');
        if($callval == 'ajaxcall')
            $this->_helper->layout->disableLayout();
		
        $feedforwardInitForm = new Default_Form_Feedforwardinit();
        try
        {		
            if($id != '')
            {
                if(is_numeric($id) && $id>0)
                {
                	$feedforwardInitModel = new Default_Model_Feedforwardinit();
                	$data = $feedforwardInitModel->getFFInitData($id);
			        
                    if(!empty($data) && $data[0]['status'] != 2)// && $data[0]['initialize_status'] == 2
                    {
                    	$appDataWithoutFF = $feedforwardInitModel->getAppDataForFF('no');
				    	foreach ($appDataWithoutFF as $appdata){
							$feedforwardInitForm->appraisal_mode->addMultiOption($appdata['id'],$appdata['app_mode']);
						}
				        
                        $data = $data[0];
                        $feedforwardInitForm->populate($data);
                        
                        if($data['enable_to'] == 1)
							$feedforwardInitForm->enable_to->setValue(array(0,1));
						else
							$feedforwardInitForm->enable_to->setValue(0);
                        
						$feedforwardInitForm->appraisal_mode->setValue($data['appraisal_id']);
						$feedforwardInitForm->appraisal_mode->setAttrib('readonly', 'readonly');
						$feedforwardInitForm->appraisal_mode->setRequired(false);
						
						if($data['initialize_status'] == 1){
							$feedforwardInitForm->status->addMultiOption(2,'Close');
							$feedforwardInitForm->employee_name_view->setAttrib('disable', 'disable');
							$feedforwardInitForm->enable_to->setAttrib('disable', 'disable');
							$feedforwardInitForm->ff_due_date->setAttrib('readonly', 'readonly');
						}
						
                        $feedforwardInitForm->setAttrib('action',DOMAIN.'feedforwardinit/edit/id/'.$id);
                        
                        $check_array['check_array'] = array();
                        if($data['qs_privileges'])
                        	$check_array['check_array'] = json_decode($data['qs_privileges'],true);
                        
                        $getQuestions = $feedforwardInitModel->getQuestionsFeedforward();
                        $getQuestionsView = $feedforwardInitModel->getQuestionsFeedforward($data['questions']);
				      	$this->view->getQuestions = $getQuestions;
				      	$this->view->getQuestionsView = $getQuestionsView;
                        $this->view->data = $data;
                        $this->view->msgarray = $check_array;
                    } 
                    else 
                    {
                    	if($data[0]['status'] == 2)
                    		$this->view->ermsg = 'closed';
                    	else
                        	$this->view->ermsg = 'norecord';
                    }
                }
                else
                {
                    $this->view->ermsg = 'norecord';
                }				
            }
            else
            {
                $this->view->ermsg = '';
            }
        }	
        catch(Exception $e)
        {
            $this->view->ermsg = 'nodata';
        }
        $this->view->form = $feedforwardInitForm;
        if($this->getRequest()->getPost())
        {
            $result = $this->save($feedforwardInitForm);	
            $this->view->msgarray = $result; 
        }
        $this->render('form');	
    }
	
    public function viewAction()
    {
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
        }
		
        $id = $this->getRequest()->getParam('id');
        $callval = $this->getRequest()->getParam('call');
        if($callval == 'ajaxcall')
            $this->_helper->layout->disableLayout();
        $objName = 'feedforwardinit';
        
        try
        {
            if($id)
            {
                if(is_numeric($id) && $id>0)
                {
                    $feedforwardInitModel = new Default_Model_Feedforwardinit();
                    $data = $feedforwardInitModel->getFFInitViewData($id);
                    if(!empty($data))
                    {                     
                    	$empsCount = $feedforwardInitModel->getCountOfEmps($id);
                    	$check_array = array();
                        if($data[0]['qs_privileges'])
                        	$check_array = json_decode($data[0]['qs_privileges'],true);
                        
                        $getQuestions = $feedforwardInitModel->getQuestionsFeedforward($data[0]['questions']);
				      	$this->view->getQuestions = $getQuestions;
				      	$this->view->check_array = $check_array;                          
                        $this->view->data = $data[0];
                        $this->view->empsCount = $empsCount[0];
                    }
                    else
                    {
                        $this->view->ermsg = 'norecord';
                    }
                } 
                else
                {
                    $this->view->ermsg = 'norecord';
                }				
            }
            else
            {
                $this->view->ermsg = 'norecord';
            } 			
        }
        catch(Exception $e)
        {
            $this->view->ermsg = 'nodata';
        }		
        $this->view->controllername = $objName;
        $this->view->id = $id;		
    }

    public function ffinitialize($appInitId,$tableid,$enable_to,$check)
    {
    	if($tableid)
    	{
	    	$auth = Zend_Auth::getInstance();
	     	if($auth->hasIdentity())
	        {
	            $loginUserId = $auth->getStorage()->read()->id;
	            $loginuserRole = $auth->getStorage()->read()->emprole;
	            $loginuserGroup = $auth->getStorage()->read()->group_id;
	        }
	        
    		$ffEmpRatingsModel = new Default_Model_Feedforwardemployeeratings();
    		$feedforwardInitModel = new Default_Model_Feedforwardinit();
    		$appraisalEmpsData = $feedforwardInitModel->getEmpsFromAppEmpRat($appInitId);
    		$appEmpIDs = array();
    		
    		if(sizeof($appraisalEmpsData)>0){
    			foreach ($appraisalEmpsData as $appE)
    			{    				
    				$appEmpIDs[] = $appE['employee_id'];
    				
    				$where = '';
    				$data = array(
    						'ff_initialization_id'=>$tableid,
    						'question_ids'=>($check!='')?implode(',',$check):NULL,
		    				'manager_id'=>$appE['line_manager_1'],
		    				'employee_id'=>$appE['employee_id'],
    						'ff_status'=>1,
    						'createdby'=>$loginUserId,
							'createdby_role'=>$loginuserRole,
							'createdby_group'=>$loginuserGroup,
							'createddate'=>gmdate("Y-m-d H:i:s"),    				
    				);
    				$ffEmpRatingsModel->SaveorUpdateFFEmpRatingsData($data, $where);
    			}
    		}
    		if(sizeof($appEmpIDs)>0 && $enable_to == 1){
    			$appEmpIDsCsv = implode(',', $appEmpIDs);
    			$allEmpsData = $feedforwardInitModel->getEmpsFromSummary($appEmpIDsCsv);
    			
	    		if(sizeof($allEmpsData)>0){
	    			foreach ($allEmpsData as $allE)
	    			{    				
	    				$where1 = '';
	    				$data1 = array(
	    						'ff_initialization_id'=>$tableid,
	    						'question_ids'=>($check!='')?implode(',',$check):NULL,
			    				'manager_id'=>$allE['reporting_manager'],
			    				'employee_id'=>$allE['user_id'],
	    						'ff_status'=>1,
	    						'createdby'=>$loginUserId,
								'createdby_role'=>$loginuserRole,
								'createdby_group'=>$loginuserGroup,
								'createddate'=>gmdate("Y-m-d H:i:s"),    				
	    				);
	    				$ffEmpRatingsModel->SaveorUpdateFFEmpRatingsData($data1, $where1);
	    			}
	    		}
    		}
    	}	
    }
    
	public function deleteAction()
	{
	     $auth = Zend_Auth::getInstance();
     		if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;
				 	$loginuserGroup = $auth->getStorage()->read()->group_id;
				}
		 $id = $this->_request->getParam('objid');
		 $messages['message'] = '';
		 $messages['msgtype'] = '';
		 $count = 0;
		 $actionflag = 3;
	    if($id)
		{
			$feedforwardInitModel = new Default_Model_Feedforwardinit();
          	$ffdata = $feedforwardInitModel->getFFInitViewData($id);			
			$menumodel = new Default_Model_Menu();
			
			if($ffdata[0]['initialize_status'] == 2)	
			{
				  $data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"),'modifiedby_role'=>$loginuserRole,
								 'modifiedby_group'=>$loginuserGroup,'modifiedby'=>$loginUserId);
				  $where = array('id=?'=>$id);
				  $Id = $feedforwardInitModel->SaveorUpdateFeedforwardInitData($data, $where);
				    if($Id == 'update')
					{
						$menuidArr = $menumodel->getMenuObjID('/feedforwardinit');
						$menuID = $menuidArr[0]['id'];
						$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
						/***
						** commented on 29-04-2015 by sapplica
						** need to integrate mail template							
							$configmail = sapp_Global::send_configuration_mail('Feed Forward',$ffdata[0]['ff_mode']);		
						***/							
						$messages['message'] = 'Feed Forward deleted successfully.';
						$messages['msgtype'] = 'success';
					}   
					else
					{
	                   $messages['message'] = 'Feed Forward cannot be deleted.';
	                   $messages['msgtype'] = 'error';
	                }
			  }else
			  {
			  	   $messages['message'] = 'Feed Forward cannot be deleted as the process is initialized/completed.';
                   $messages['msgtype'] = 'error';
			  } 				   
		}
		else
		{ 
		 $messages['message'] = 'Feed Forward cannot be deleted.';
		 $messages['msgtype'] = 'error';
		}
		$this->_helper->json($messages);
	}	
}