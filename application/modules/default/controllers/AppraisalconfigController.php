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

class Default_AppraisalconfigController extends Zend_Controller_Action
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
    	$appraisalconfigmodel = new Default_Model_Appraisalconfig();	
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
			$sort = 'DESC';$by = 'c.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else 
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'c.modifieddate';
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
		$dataTmp = $appraisalconfigmodel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);		 		
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
		$this->render('commongrid/index', null, true);
		
    }
	
    
	public function addAction()
	{
	   $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;
					$loginuserGroup = $auth->getStorage()->read()->group_id;
			}
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
			
		$appConfigModel = new Default_Model_Appraisalconfig();
		$bunitdata = $appConfigModel ->filterBU();
	   if(!empty($bunitdata))
	   {	
			$appraisalconfigform = new Default_Form_Appraisalconfig($bunitdata);
			$msgarray = array();
			$appraisalconfigform->setAttrib('action',BASE_URL.'appraisalconfig/add');
			$this->view->form = $appraisalconfigform; 
			$this->view->msgarray = $msgarray;
			$this->view->ermsg = '';
				if($this->getRequest()->getPost()){
					 $result = $this->save($appraisalconfigform);			 
					 $this->view->msgarray = $result; 
				}  		
			$this->render('form');	
	   }
	   else
	   {
	   	$appraisalconfigform = new Default_Form_Appraisalconfig();
		$msgarray = array();
		$appraisalconfigform->setAttrib('action',BASE_URL.'appraisalconfig/add');
		$this->view->form = $appraisalconfigform; 
		$this->view->msgarray = $msgarray; 
		$this->view->ermsg = 'All Business units are configured for appraisal process.';
		$this->render('form');	
	   	
	   }
	}
/**
     * 
     * View function is used to populate the data for the particular ID.
     */
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
		$objName = 'appraisalconfig';
		$appraisalconfigform = new Default_Form_Appraisalconfig();
		$appraisalconfigmodel = new Default_Model_Appraisalconfig();
		$departmentsmodel = new Default_Model_Departments();
		$appraisalconfigform->removeElement("submit");
		$elements = $appraisalconfigform->getElements();
		
		try
		{
		    if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					$data = $appraisalconfigmodel->getAppraisalConfigbyID($id);
					if(!empty($data))
					{
						$data = $data[0]; 
						$previ_data = sapp_Global::_checkprivileges(APPRAISAL_SETTINGS,$loginuserGroup,$loginuserRole,'edit');
						$this->view->previ_data = $previ_data;

						if($data['department_id'] !='' && $data['department_id'] != 'NULL')
						{  
							$deptdata = $departmentsmodel->getSingleDepartmentData($data['department_id']);
							if(sizeof($deptdata) > 0)
							  $appraisalconfigform->department_id->addMultiOption($deptdata['id'],utf8_encode($deptdata['deptname']));
						}
						
						$bunitModel = new Default_Model_Businessunits();
						$bunitdata = $bunitModel->fetchAll('isactive=1','unitname');
						$appraisalconfigform->businessunit_id->addMultiOptions(array(''=>'Select Business unit','0'=>'No Business Unit'));

						foreach ($bunitdata->toArray() as $bdata){
							$appraisalconfigform->businessunit_id->addMultiOption($bdata['id'],$bdata['unitname']);
						}
					    $appraisalconfigform->setDefault('businessunit_id',$data['businessunit_id']);

					    $this->view->performance_app_flag = $data['performance_app_flag'];
					    $appraisalconfigform->populate($data);
						if(count($elements)>0)
						{
							foreach($elements as $key=>$element)
							{
								if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")){
									$element->setAttrib("disabled", "disabled");
								}
							}
						}

						/** to show/hide edit button **/
						$chkFlagForEdit = $appraisalconfigmodel->checkInitializationData($id);
						$this->view->chkFlagForEdit = $chkFlagForEdit;
					}else
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
	if(!empty($data['businessunit_id'])) {
						$buname = $bunitModel->getSingleUnitData($data['businessunit_id']);
						
						if(!empty($buname)){
							$data['businessunit_id'] = $buname['unitname'];
						}
					}
	  if(!empty($data['department_id'])) {
	  	$depname = $departmentsmodel->getSingleDepartmentData($data['department_id']);
			if(!empty($depname)){
			$data['department_id'] = $depname['deptname'];
				}
		   }
		   if($data['performance_app_flag']=='0'){
		   	$data['performance_app_flag']="Department wise";
		   }else{
		   		$data['performance_app_flag']="Business unit wise";
		   }
	 if($data['appraisal_ratings']=='1'){
		   	$data['appraisal_ratings']="1-5";
		   }else{
		   		$data['appraisal_ratings']="1-10";
		   }
		$this->view->controllername = $objName;
		$this->view->id = $id;
		$this->view->data = $data;
		$this->view->flag='view';
		$this->view->form = $appraisalconfigform;
		$this->render('form');	
	}
	
	/**
	 * 
	 * Edit function to prepopulate the data.
	 * 
	 */
	public function editAction()
	{	
	    $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;
					$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		$appraisalconfigform = new Default_Form_Appraisalconfig();
		$bunitModel = new Default_Model_Businessunits();
		$appraisalconfigmodel = new Default_Model_Appraisalconfig();
		$departmentsmodel = new Default_Model_Departments();
		$msgarray = array();
		$appraisalconfigform->submit->setLabel('Update');
		try
        {		
			if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					/** to show/hide edit button **/
					$chkFlagForEdit = $appraisalconfigmodel->checkInitializationData($id);
					$this->view->chkFlagForEdit = $chkFlagForEdit;
					if(empty($chkFlagForEdit))
					{
						$data = $appraisalconfigmodel->getAppraisalConfigbyID($id);
						if(!empty($data))
						{
							$data = $data[0]; 
													
							if($data['department_id'] !='' && $data['department_id'] != 'NULL'){  
								$deptdata = $departmentsmodel->getSingleDepartmentData($data['department_id']);
								if(sizeof($deptdata) > 0)
								  $appraisalconfigform->department_id->addMultiOption($deptdata['id'],utf8_encode($deptdata['deptname']));
							}
							
							$bunitData = $bunitModel->getSingleUnitData($data['businessunit_id']);
							if(!empty($bunitData))
							{
								$appraisalconfigform->businessunit_id->addMultiOption($bunitData['id'],utf8_encode($bunitData['unitname']));
							}
							
							if($data['department_id'] !='' && $data['department_id'] !='NULL')
							{
								$departmentlistArr = $departmentsmodel->getDepartmentList($data['businessunit_id']);
								$departmentlistArr = $departmentsmodel->getSingleDepartmentData($data['department_id']);
							}	 
							else
								$departmentlistArr = array();
							
							// Disable 'Business unit wise' option when 'No Business Unit' was selected
							if($data["businessunit_id"] == 0) {
								$appraisalconfigform->performance_app_flag->setOptions(array('disable' => array(1)));
							}
							
							$appraisalconfigform->populate($data);
							$appraisalconfigform->setDefault('businessunit_id',$data['businessunit_id']);
							if(sizeof($departmentlistArr) > 0)
							 $appraisalconfigform->setDefault('department_id',$data['department_id']);
							
							
							$this->view->performance_app_flag = $data['performance_app_flag']; 	
							$appraisalconfigform->setAttrib('action',BASE_URL.'appraisalconfig/edit/id/'.$id);
							$this->view->data = $data;
							
							/****
							
							$chk_cnt = $appraisalconfigmodel->check_act_init($id);
							$this->view->chk_cnt = $chk_cnt;
							if($chk_cnt > 0)
							{
								$appraisalconfigform->businessunit_id->setAttrib("disabled", "disabled");
								$appraisalconfigform->performance_app_flag->setAttrib("disabled", "disabled");
								$appraisalconfigform->department_id->setAttrib("disabled", "disabled");
								$appraisalconfigform->appraisal_mode->setAttrib("disabled", "disabled");
								$appraisalconfigform->appraisal_ratings->setAttrib("disabled", "disabled"); //added on 13-04-2015 by soujanya
							}
							
							*****/
                        }
                        else
						{
							$this->view->ermsg = 'norecord';
						}
					}
					else
					{
						$this->view->ermsg = 'noedit';
					}
				}
                else
				{
					$this->view->ermsg = 'norecord';
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
		$this->view->form = $appraisalconfigform;
		if($this->getRequest()->getPost()){
      		$result = $this->save($appraisalconfigform);	
		    $this->view->msgarray = $result; 
		}
		$this->render('form');	
	}
	   
public function save($appraisalconfigform)
	{	
	  $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
				 $loginUserId = $auth->getStorage()->read()->id;
				 $loginuserRole = $auth->getStorage()->read()->emprole;
				 $loginUserEmpId = $auth->getStorage()->read()->employeeId;
           		 $loginUserEmail = $auth->getStorage()->read()->emailaddress;
            	 $loginUsername = $auth->getStorage()->read()->userfullname;
           
		} 
	    $appraisalconfigmodel = new Default_Model_Appraisalconfig();
	    $departmentsmodel = new Default_Model_Departments();
	    $businessunitsmodel = new Default_Model_Businessunits();
		$msgarray = array();
		$resultArr = array();
		$arrData = array();
		$prevBusinessunit = '';
		$errorflag = "true";
		 $id = $this->_request->getParam('id');
         $businessunit_id = $this->_request->getParam('businessunit_id');
         $performance_app_flag = $this->_request->getParam('performance_app_flag');
         $department_id = $this->_request->getParam('department_id');
         $appraisal_mode = $this->_request->getParam('appraisal_mode');
         $approval_selection = $this->_request->getParam('approval_selection');
         $appraisal_ratings = $this->_request->getParam('appraisal_ratings');

		// Validation to check duplicate combinations
		$arrData = array("business_unit_id" => $businessunit_id, "department_id" => $department_id, "id" => $id);
		if ($appraisalconfigmodel->combinationExists($arrData)) {
			$msgarray['businessunit_id'] = 'Business unit or department configuration already exists.';
			$errorflag = "false";
		} 
         
		 /** Start
		  * Validating selection of department if implementaion is department wise
		  */
		   if($performance_app_flag == 0)
		   {
		   	 if($department_id == '')
		   	 {
		   	 	$msgarray['department_id'] = 'Please select department.';
			 	$errorflag = "false";
		   	 }
		   }
		 /**
			End validating selection of department
		  */
		        
		   	
		   
		   /** Start
              Validating unique service desk department
		    */
		        if($businessunit_id !='' && $id == '')
		        {
					$appraisalconigfArr = $appraisalconfigmodel->checkuniqueAppraisalConfigData($businessunit_id,$performance_app_flag,$department_id);
					if(!empty($appraisalconigfArr))
					{
						if($appraisalconigfArr[0]['count'] > 0)
						{
							$msgarray['department_id'] = 'Please select a different department.';
				 			$errorflag = "false";
						}
					}
		        }			   
		   /** End
		    * Validating uniques  department
		    */
		       
			
	  	$db = Zend_Db_Table::getDefaultAdapter();		
        $db->beginTransaction();
		  if($appraisalconfigform->isValid($this->_request->getPost()) && $errorflag == 'true'){
            try{
			$actionflag = '';
			$tableid  = '';
			$data = array('businessunit_id'=>$businessunit_id,
			                 'department_id'=>($department_id!=''?$department_id:NULL), 
							 'performance_app_flag'=>$performance_app_flag,
							 'appraisal_mode'=> $appraisal_mode,
							 
							 'appraisal_ratings' => $appraisal_ratings,
							 'module_flag' => 1,	
			                 'modifiedby'=>$loginUserId,
							  'modifieddate'=>gmdate("Y-m-d H:i:s")
					);
				if($id!=''){
					/* for Update record  */
					$where = array('id=?'=>$id);  
					$actionflag = 2;
				}
				else
				{
					/* for Insert new record  */
					$data['createdby'] = $loginUserId;
					$data['createddate'] = gmdate("Y-m-d H:i:s");
					$data['isactive'] = 1;
					$where = '';
					$actionflag = 1;
				}
				 
				  $Id = $appraisalconfigmodel->SaveorUpdateAppraisalConfigData($data, $where);
				
								
				if($Id == 'update')
				{
				   $tableid = $id;
				   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Appraisal settings updated successfully"));
				}   
				else
				{
				   $tableid = $Id; 	
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Appraisal settings added successfully"));					   
				}   
				
				/*
				 *   Logs Storing
				 */
				
				$menuID = APPRAISAL_SETTINGS;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
				/*
				 *  Logs storing ends
				 */
				
					/** Start
					 * Sending Mails to employees
					 */
						
				     if($performance_app_flag == 0)
				     {
						 $appraisal_details = $appraisalconfigmodel->getBunitDept($businessunit_id,$department_id);
						
				     }
					else
					{
						$appraisal_details = $appraisalconfigmodel->getBunit($businessunit_id);
					}
						
						$employeeDetailsArr = $appraisalconfigmodel->getUserDetailsByID($businessunit_id,$department_id);
					   
						if(!empty($appraisal_details))
						{
							$bunit = $appraisal_details['unitname'];
							$dept = $appraisal_details['deptname'];
						}
					
						
						$msg_add_update = ($Id == 'update') ? "Updated" : "Configured" ;
						$dept_str = ($dept == '') ? " ":"and <b>$dept</b> department"; 
						$emp_id_str = ($loginuserRole == SUPERADMINROLE) ? " ":"($loginUserEmpId)";
							
            					//Preparing Employee array for BCc
								$empArr = array();
								if(!empty($employeeDetailsArr))
								{
									$empArrList = '';
									foreach($employeeDetailsArr as $emp)
									{
										array_push($empArr,$emp['emailaddress']);
									}
									
								}
						
								  //Sending mail to Super admin					
								$options['subject'] = APPLICATION_NAME.': Performance Appraisal Configuration '.ucfirst($msg_add_update);
                                $options['header'] = 'Performance Appraisal';
                                $options['toEmail'] = SUPERADMIN_EMAIL; 
                                $options['bcc'] 	= $empArr;  
                                $options['toName'] = 'Super Admin';
                                $options['message'] = "<div style='padding: 0; text-align: left; font-size:14px; font-family:Arial, Helvetica, sans-serif;'>				
														<span style='color:#3b3b3b;'>Hi,</span><br />
														<div style='padding:20px 0 0 0;color:#3b3b3b;'>Performance appraisal settings are $msg_add_update for <b>$bunit</b> business unit $dept_str by ".$loginUsername.$emp_id_str." </div>
														<div style='padding:20px 0 10px 0;'>Please <a href=".BASE_URL." target='_blank' style='color:#b3512f;'>click here</a> to login  to <b>".APPLICATION_NAME."</b> and check the details.</div>
														</div> ";
                                $mail_id =  sapp_Global::_sendEmail($options); 
						
					/**
					 * End
					 */	
				$db->commit();
				$this->_redirect('appraisalconfig');	
				throw new Exception("Some error message");
                  }
        catch(Exception $e)
          {
          	 $db->rollBack();
          	 echo $e->getMessage();
			echo $e->getTraceAsString();
          	 
              return $msgarray;
          }
		}else
		{
			$messages = $appraisalconfigform->getMessages();
			foreach ($messages as $key => $val)
				{
					foreach($val as $key2 => $val2)
					 {
						$msgarray[$key] = $val2;
						break;
					 }
				}
			if(isset($businessunit_id) && $businessunit_id != '')
			{
				
				if($performance_app_flag == 0)
				{
					$departmentlistArr = $departmentsmodel->getDepartmentList($businessunit_id);
					if(!empty($departmentlistArr))
					{
						foreach($departmentlistArr as $departmentlist)
						{
							$appraisalconfigform->department_id->addMultiOption($departmentlist['id'],utf8_encode($departmentlist['deptname']));
						}
					}
					
				} 
				if(isset($department_id) && $department_id != 0 && $department_id != '')
					$appraisalconfigform->setDefault('department_id',$department_id);
			}
			
			

			return $msgarray;	
		}
	
	}
	
	public function getbunitimplementationAction()
	{
            $ajaxContext = $this->_helper->getHelper('AjaxContext');
            $ajaxContext->addActionContext('getbunitimplementation', 'json')->initContext();
            $businessunitsmodel = new Default_Model_Businessunits();
            $appraisalconfigmodel = new Default_Model_Appraisalconfig();
            $bunitid = $this->_request->getParam('bunitid');
            $result = array();
            $appInitModel = new Default_Model_Appraisalinit();
            if($bunitid !='')
            {
                $pendingRequestdata = $appraisalconfigmodel->getPendingAppraisalConfigData($bunitid);
                if(!empty($pendingRequestdata))
                    $result['count'] = $pendingRequestdata[0]['count'];
                $implementationdata = $appraisalconfigmodel->getconfig_bu($bunitid);

                if(!empty($implementationdata))
                {
                    $result['result'] = $implementationdata['performance_app_flag'];
                    //for initialization status of business unit
                    $initCnt = $appraisalconfigmodel->initializedCheck($bunitid,$implementationdata['performance_app_flag']);
                    $result['painitdata'] = $initCnt;
            	}
            }
            $this->_helper->_json($result);		
	}
	
	 
	
public function getdepartmentsAction()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('getdepartments', 'html')->initContext();
		$appraisalconfigmodel = new Default_Model_Appraisalconfig();
		$bunitid = $this->_request->getParam('bunitid');
		$deptid = $this->_request->getParam('deptid');
		$implementationData = array();
	
		if($bunitid !='')
		{
		 	$implementationData = $appraisalconfigmodel->getconfig_bu($bunitid);
		 	
			$this->view->implementationdata=$implementationData;
		}
		
		
	}
	
    public function deleteAction()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
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
            $model = new Default_Model_Appraisalconfig();
            $init_cnt = $model->check_delete($id);
            if($init_cnt == 0)
            {
                $data = array( 
                    'isactive' => 0,
                    'modifieddate' => gmdate("Y-m-d H:i:s"),                    
                    'modifiedby' => $loginUserId,
                );
                $where = array('id=?'=>$id);
                $Id = $model->SaveorUpdateAppraisalConfigData($data, $where);
                if($Id == 'update')
                {                                        
                    $result = sapp_Global::logManager(APPRAISAL_SETTINGS,$actionflag,$loginUserId,$id);		    
                    $messages['message'] = 'Appraisal setting deleted successfully.';
                    $messages['msgtype'] = 'success';
                }   
                else
                {
                    $messages['message'] = 'Appraisal setting cannot be deleted.';
                    $messages['msgtype'] = 'error';
                }
            }
            else
            {
                $messages['message'] = 'An active appraisal process exists for the selected appraisal setting. Appraisal Setting cannot be deleted.';
                $messages['msgtype'] = 'error';
            }
        }
        else
        { 
            $messages['message'] = 'Appraisal setting cannot be deleted.';
            $messages['msgtype'] = 'error';
        }
        $this->_helper->json($messages);
    }
	
}

