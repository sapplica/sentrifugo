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
		$appraisalconfigform = new Default_Form_Appraisalconfig();
		$msgarray = array();
		$appraisalconfigform->setAttrib('action',DOMAIN.'appraisalconfig/add');
		$this->view->form = $appraisalconfigform; 
		$this->view->msgarray = $msgarray; 
		$this->view->ermsg = '';
			if($this->getRequest()->getPost()){
				 $result = $this->save($appraisalconfigform);	
				 $this->view->msgarray = $result; 
			}  		
		$this->render('form');	
	}
/**
     * 
     * View function is used to populate the data for the particular ID.
     */
    public function viewAction()
	{	
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
					//echo "<pre>";print_r($data);echo "</pre>";
					if(!empty($data))
					{
						$data = $data[0]; 
                                                
						if($data['department_id'] !='' && $data['department_id'] != 'NULL'){  
							$deptdata = $departmentsmodel->getSingleDepartmentData($data['department_id']);
							if(sizeof($deptdata) > 0)
							  $appraisalconfigform->department_id->addMultiOption($deptdata['id'],utf8_encode($deptdata['deptname']));
						}
						
						                   $bunitModel = new Default_Model_Businessunits();
                                                $bunitdata = $bunitModel->fetchAll('isactive=1','unitname');
                                        $appraisalconfigform->businessunit_id->addMultiOptions(array(''=>'Select Business unit',
                                                                    '0'=>'No Business Unit'));

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
		$this->view->controllername = $objName;
		$this->view->id = $id;
		$this->view->form = $appraisalconfigform;
		$this->render('form');	
	}
	
	/**
	 * 
	 * Edit function to prepopulate the data.
	 * In this action service desk id, department, request recievers, cc mail recivers and approver list is populated.
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
						
						$appraisalconfigform->populate($data);
						$appraisalconfigform->setDefault('businessunit_id',$data['businessunit_id']);
						if(sizeof($departmentlistArr) > 0)
						 $appraisalconfigform->setDefault('department_id',$data['department_id']);
						
						
						$this->view->performance_app_flag = $data['performance_app_flag']; 	
						$appraisalconfigform->setAttrib('action',DOMAIN.'appraisalconfig/edit/id/'.$id);
                        $this->view->data = $data;
                        $appraisalconfigform->businessunit_id->setAttrib("disabled", "disabled");
                        $appraisalconfigform->performance_app_flag->setAttrib("disabled", "disabled");
                        $appraisalconfigform->department_id->setAttrib("disabled", "disabled");
                        $appraisalconfigform->appraisal_mode->setAttrib("disabled", "disabled");
                        
                        //$approval_selection_edit = $appraisalconfigmodel->checkEnablestatus($businessunit_id,$performance_app_flag,$department_id); 
                        
                        $appraisalconfigform->approval_selection->setAttrib("disabled", "disabled");
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
				$this->view->ermsg = 'nodata';
			}
		}	
		catch(Exception $e)
		{
			   $this->view->ermsg = 'nodata';
		}	
		$this->view->form = $appraisalconfigform;
		//echo "<pre>";print_r($appraisalconfigform);echo "</pre>";
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
		} 
	    $appraisalconfigmodel = new Default_Model_Appraisalconfig();
	    $departmentsmodel = new Default_Model_Departments();
	    $businessunitsmodel = new Default_Model_Businessunits();
		$msgarray = array();
		$resultArr = array();
		//$empstring = '';
		//$prevBusinessunit = '';
		$errorflag = "true";
		 $id = $this->_request->getParam('id');
         $businessunit_id = $this->_request->getParam('businessunit_id');
         $performance_app_flag = $this->_request->getParam('performance_app_flag');
         $department_id = $this->_request->getParam('department_id');
         $appraisal_mode = $this->_request->getParam('appraisal_mode');
         $approval_selection = $this->_request->getParam('approval_selection');
         $appraisal_ratings = $this->_request->getParam('appraisal_ratings');
         		 
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
		        
		   	 /**
		    * End Approver level selection
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
		    * Validating uniques service desk department
		    */
		        /**
		    * Validating if pending requests are there when changing the implementation
		    */
		        if($businessunit_id !='')
		        {
			        $implementationdata = $businessunitsmodel->getSingleUnitData($businessunit_id);
			        //echo "<pre>"; print_r($implementationdata);echo "</pre>"; die;
			        if(!empty($implementationdata))
						$prevBusinessunit = $implementationdata['service_desk_flag'];
					if($performance_app_flag !='')
					{
						if($prevBusinessunit != $performance_app_flag)
						{
							$pendingRequestdata = $appraisalconfigmodel->getPendingAppraisalConfigData($businessunit_id);
								if(!empty($pendingRequestdata))
								{
								   if($pendingRequestdata[0]['count'] > 0)
								   {
								   	    $msgarray['performance_app_flag'] = 'Applicability cannot be changed as requests are in pending state.';
						 				$errorflag = "false";
								   }
								}   
						}
					}
		        }
		  /**
		   * End validating pending request checking 
		   */		
				     
	  	$db = Zend_Db_Table::getDefaultAdapter();		
        $db->beginTransaction();
		  if($appraisalconfigform->isValid($this->_request->getPost()) && $errorflag == 'true'){
            try{
            
			$menumodel = new Default_Model_Menu();
			$actionflag = '';
			$tableid  = '';
			$data = array('businessunit_id'=>$businessunit_id,
			                 'department_id'=>($department_id!=''?$department_id:NULL), 
							 'performance_app_flag'=>$performance_app_flag,
							 'appraisal_mode'=> $appraisal_mode,
							 'approval_selection'=>$approval_selection,
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
				   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Configuration updated successfully."));
				}   
				else
				{
				   $tableid = $Id; 	
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Configuration added successfully."));					   
				}   
				$menuidArr = $menumodel->getMenuObjID('/appraisalconfig');
				$menuID = $menuidArr[0]['id'];
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
				$db->commit();
				$this->_redirect('appraisalconfig');	
				//throw new Exception("Some error message");
                  }
        catch(Exception $e)
          {
          	 $db->rollBack();
          	 //echo $e->getMessage();
			// echo $e->getTraceAsString();
          	 
             //$msgarray['service_desk_id'] = "Something went wrong, please try again.";
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
						//echo "<pre>";print_r($departmentlistArr); echo "</pre>"; 
						foreach($departmentlistArr as $departmentlist)
						{
							$appraisalconfigform->department_id->addMultiOption($departmentlist['id'],utf8_encode($departmentlist['deptname']));
						}
					}
					
				} // $department_id = $departmentlist['deptname'];
				//echo "dept:".$department_id; die;
				//echo $performance_app_flag; die;
				if(isset($department_id) && $department_id != 0 && $department_id != '')
					$appraisalconfigform->setDefault('department_id',$department_id);
			}
			
			

			//echo "<pre>";print_r($msgarray);echo "</pre>"; die;
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
		if($bunitid !='')
		{
			$pendingRequestdata = $appraisalconfigmodel->getPendingAppraisalConfigData($bunitid);
			if(!empty($pendingRequestdata))
			   $result['count'] = $pendingRequestdata[0]['count'];
			$implementationdata = $businessunitsmodel->getSingleUnitData($bunitid);
			if(!empty($implementationdata))
				$result['result'] = $implementationdata['service_desk_flag'];
		}
		
		$this->_helper->_json($result);
		
	}
	
	 
	
public function getdepartmentsAction()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('getdepartments', 'html')->initContext();
		$businessunitsmodel = new Default_Model_Businessunits();
		$bunitid = $this->_request->getParam('bunitid');
		$deptid = $this->_request->getParam('deptid');
		$implementationData = array();
	
		if($bunitid !='')
		 	$implementationData = $businessunitsmodel->getSingleUnitData($bunitid);
			$this->_helper->_json($implementationData);
		
	}
	
/*public function deleteAction()
	{
	     $auth = Zend_Auth::getInstance();
     		if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
				}
		 $id = $this->_request->getParam('objid');
		 $messages['message'] = '';
		 $messages['msgtype'] = '';
		 $actionflag = 3;
		 //$count = 0;
		    if($id)
			{
			$appraisalconfigmodel = new Default_Model_Appraisalconfig();
			//$servicedeskconfmodel = new Default_Model_Servicedeskconf();
			$servicedeskdepartmentmodel = new Default_Model_Servicedeskdepartment();
			  $menumodel = new Default_Model_Menu();
			  $appraisalconfigdata = $appraisalconfigmodel->getAppraisalConfigbyID($id);
			  //if(!empty($servicedeskconfdata))
			  //	$pendingRequestdata = $servicedeskconfmodel->getPendingServiceReqData($servicedeskconfdata[0]['businessunit_id']);
			 // if(!empty($pendingRequestdata))
			  //	$count = $pendingRequestdata[0]['count'];
			  if($count < 1)
			  {	
				  $serviceDeptData = $servicedeskdepartmentmodel->getServiceDeskDepartmentDatabyID($servicedeskconfdata[0]['service_desk_id']);
				  $data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
				  $where = array('id=?'=>$id);
				  $Id = $servicedeskconfmodel->SaveorUpdateServiceConfData($data, $where);
				    if($Id == 'update')
					{
					   $menuidArr = $menumodel->getMenuObjID('/appraisalconfig');
					   $menuID = $menuidArr[0]['id'];
					   $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
					   if(!empty($serviceDeptData))
	                   $configmail = sapp_Global::send_configuration_mail('Setting',$serviceDeptData[0]['service_desk_name']);				   
					   $messages['message'] = 'Setting deleted successfully.';
					   $messages['msgtype'] = 'success';
					}   
					else
					{
	                   $messages['message'] = 'Setting cannot be deleted.';
	                   $messages['msgtype'] = 'error';
	                }	
			  }else
			  {
			  		$messages['message'] = 'Setting cannot be deleted as requests are in pending state.';
	                $messages['msgtype'] = 'error';
			  }			   
			}
			else
			{ 
			 $messages['message'] = 'Setting cannot be deleted.';
			 $messages['msgtype'] = 'error';
			}
			$this->_helper->json($messages);
		
	}*/
	
}

