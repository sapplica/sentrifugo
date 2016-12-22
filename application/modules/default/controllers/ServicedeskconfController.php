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

class Default_ServicedeskconfController extends Zend_Controller_Action
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
		$servicedeskconfmodel = new Default_Model_Servicedeskconf();	
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
		$dataTmp = $servicedeskconfmodel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);		 		
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
		$servicedeskconfform = new Default_Form_servicedeskconf();
		$servicedeskdepartmentmodel = new Default_Model_Servicedeskdepartment();
		$msgarray = array();
		$popConfigPermission = array();
	 	if(sapp_Global::_checkprivileges(SERVICEDESKDEPARTMENT,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
	 		array_push($popConfigPermission,'servicedeskdepartment');
	 	}
	 	$this->view->popConfigPermission = $popConfigPermission;
		$servicedeskconfform->setAttrib('action',BASE_URL.'servicedeskconf/add');
		$this->view->form = $servicedeskconfform; 
		$this->view->msgarray = $msgarray; 
		$this->view->ermsg = '';
			if($this->getRequest()->getPost()){
				
				 $result = $this->save($servicedeskconfform);	
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
		$objName = 'servicedeskconf';
		$servicedeskconfform = new Default_Form_servicedeskconf();
		$servicedeskconfmodel = new Default_Model_Servicedeskconf();
		$servicedeskdepartmentmodel = new Default_Model_Servicedeskdepartment();
		$departmentsmodel = new Default_Model_Departments();
		$employeemodel = new Default_Model_Employee();
		$approvingauthflag = '';
		$servicedeskconfform->removeElement("submit");
		$elements = $servicedeskconfform->getElements();
		$bunitModel = new Default_Model_Businessunits();
		try
		{
		    if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					$data = $servicedeskconfmodel->getServiceDeskConfbyID($id);
					if(!empty($data))
					{
						$data = $data[0]; 
                                                
						if($data['department_id'] !='' && $data['department_id'] != 'NULL'){  
							$deptdata = $departmentsmodel->getSingleDepartmentData($data['department_id']);
							if(sizeof($deptdata) > 0)
							{
							  $servicedeskconfform->department_id->addMultiOption($deptdata['id'],utf8_encode($deptdata['deptname']));
						     $data['department_id'] = $deptdata['deptname'];
							}
							else{
								 $data['department_id'] = "";
							}
						}
						
						if($data['service_desk_id'] !='' && $data['service_desk_id'] != 'NULL' &&   isset($data['businessunit_id'])) 
					    {
					    	if($data['request_for']=='1'){
								$serviceDeptData = $servicedeskdepartmentmodel->getServiceDeskDepartmentDatabyID($data['service_desk_id']);
								if(sizeof($serviceDeptData) > 0)
								{
								  $servicedeskconfform->service_desk_id->addMultiOption($serviceDeptData[0]['id'],utf8_encode($serviceDeptData[0]['service_desk_name']));
								  $data['service_desk_id']=$serviceDeptData[0]['service_desk_name'];
								}
								else
								{
									 $data['service_desk_id']= "";
								}
					    	}
					    	else{
					    		
					    		$catData=$servicedeskconfmodel->getCategoryBYId($data['service_desk_id']);
					    		if(sizeof($catData) > 0)
								{
					    			$servicedeskconfform->service_desk_id->addMultiOption($catData[0]['id'],utf8_encode($catData[0]['name']));
					    			$data['service_desk_id']=$catData[0]['name'];
								}
								else
								{
									$data['service_desk_id']="";
								}
					    		
					    	}
					          
					    }		  
							  
					   if($data['request_recievers'] !='' && $data['request_recievers'] != 'NULL') 
					    {
					    $reqrecieverdata = $employeemodel->getEmployeeDetails($data['request_recievers']);	
					     if(sizeof($reqrecieverdata) > 0)
					     {
					        $servicedeskconfform->request_recievers->setMultiOptions($reqrecieverdata);  
					     }
					     $req_recievers_arr = explode(',', $data['request_recievers']); 
					    // $data['request_recievers']=explode(',', $data['request_recievers']);
					    }	 

					    if($data['cc_mail_recievers'] !='' && $data['cc_mail_recievers'] != 'NULL') 
					    {
							$ccmaildata = $employeemodel->getEmployeeDetails($data['cc_mail_recievers']);	
						     if(sizeof($ccmaildata) > 0)
						     {
						        $servicedeskconfform->cc_mail_recievers->setMultiOptions($ccmaildata); 
						     }
					    }

					    if($data['approver_1'] !='' && $data['approver_1'] != 'NULL') 
					    {
					    	$approver1data = $employeemodel->getIndividualEmpDetails($data['approver_1']);
						     if(sizeof($approver1data) > 0)
						     {
								  $servicedeskconfform->approver_1->addMultiOption($approver1data['user_id'],utf8_encode($approver1data['userfullname']));
                                   $data['approver_1'] =$approver1data['userfullname'];
						     }
							 else
							 {
								  $data['approver_1'] ="";
							 }
						     $approvingauthflag = 1;
					    }
					    
					  if($data['approver_2'] !='' && $data['approver_2'] != 'NULL') 
					    {
					    	$approver2data = $employeemodel->getIndividualEmpDetails($data['approver_2']);	
						     if(sizeof($approver2data) > 0)
						     {
								  $servicedeskconfform->approver_2->addMultiOption($approver2data['user_id'],utf8_encode($approver2data['userfullname']));
						          $data['approver_2'] =$approver2data['userfullname'];
						     }
							 else
							 {
								  $data['approver_2'] ="";
							 }
						     $approvingauthflag = 2;
					    }
					    
					  if($data['approver_3'] !='' && $data['approver_3'] != 'NULL') 
					    {
					    	$approver3data = $employeemodel->getIndividualEmpDetails($data['approver_3']);	
						     if(sizeof($approver3data) > 0)
						     {
								  $servicedeskconfform->approver_3->addMultiOption($approver3data['user_id'],utf8_encode($approver3data['userfullname']));
						           $data['approver_3'] =$approver3data['userfullname'];
						     }
							  else
							 {
								  $data['approver_3'] = "";
							 }
						     $approvingauthflag = 3;
					    }
                       $bunitdata = $bunitModel->fetchAll('isactive=1','unitname');
					   $servicedeskconfform->businessunit_id->addMultiOptions(array(''=>'Select Business unit','0'=>'No Business Unit'));
						foreach ($bunitdata->toArray() as $bdata)
						{
							$servicedeskconfform->businessunit_id->addMultiOption($bdata['id'],$bdata['unitname']);
						}
					    $servicedeskconfform->setDefault('businessunit_id',$data['businessunit_id']);
					    $servicedeskconfform->setDefault('approvingauthority',$approvingauthflag);
					    $this->view->approvingauthflag = $approvingauthflag;
					    $this->view->service_desk_flag = $data['service_desk_flag'];
					    $this->view->request_recievers_value = $data['request_recievers'];
					    $this->view->cc_mail_recievers_value = ($data['cc_mail_recievers']!=''?$data['cc_mail_recievers']:'');
						$servicedeskconfform->populate($data);
					 if(count($elements)>0)
					{
						foreach($elements as $key=>$element)
						{
							if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
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
	     if(!empty($data['businessunit_id'])) {
			$buname = $bunitModel->getSingleUnitData($data['businessunit_id']);
			
			if(!empty($buname)){
				$data['businessunit_id'] = $buname['unitname'];
			}
			else
			{
				$data['businessunit_id'] = "";
			}
		}
		 if(isset($data['service_desk_flag'])){
			   if($data['service_desk_flag']=='0'){
				$data['service_desk_flag']="Department wise";
			   }else{
					$data['service_desk_flag']="Business unit wise";
			   }
		 }
		  if(isset($data['attachment'])){
			if($data['attachment']=='1'){
				$data['attachment']="yes";
			   }else{
					$data['attachment']="no";
			   }
		  }  
		  if(isset($data['request_for'])){ 
			   if($data['request_for']=='1'){
				$data['request_for']="Service";
			   }else{
				$data['request_for']="Asset";
			   }
		  }  
	   $reqreciever="";
		if(!empty($data['request_recievers'])){
            $reqrecieverdata=$employeemodel->getEmployeeDetails($data['request_recievers']);
            
			if(count($reqrecieverdata)>0)
			{
				
				foreach($reqrecieverdata as $executors)
				{
				$reqreciever.= $executors.',';
			   }
		
			}
           
			$data['request_recievers'] = rtrim ( $reqreciever, ',');
		}
			
		 $mailreciever="";
	  if(!empty($data['cc_mail_recievers']))
	  {
		  $mailrecieverdata=$employeemodel->getEmployeeDetails($data['cc_mail_recievers']);
			
			if(count($mailrecieverdata)>0)
			{
				
				foreach($mailrecieverdata as $executors)
				{
				$mailreciever.= $executors.',';
			   }
		
			}
				 
			$data['cc_mail_recievers'] = rtrim ( $mailreciever, ',');
		}
		$this->view->controllername = $objName;
		$this->view->id = $id;
		$this->view->data = $data;
		$this->view->flag = 'view';
		$this->view->form = $servicedeskconfform;
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
		$servicedeskconfform = new Default_Form_servicedeskconf();
		$bunitModel = new Default_Model_Businessunits();
		$servicedeskconfmodel = new Default_Model_Servicedeskconf();
		$servicedeskdepartmentmodel = new Default_Model_Servicedeskdepartment();
		$assetscategoryModel=new Default_Model_Servicerequests();
		$departmentsmodel = new Default_Model_Departments();
		$employeemodel = new Default_Model_Employee();
		$approvingauthflag = '';
		$msgarray = array();
		$emparray = array();
		$catarray=array();
		$empstring ='';
		
		$servicedeskconfform->submit->setLabel('Update');
		try
        {		
			if($id)
			{
				 if(is_numeric($id) && $id>0)
				{
					$data = $servicedeskconfmodel->getServiceDeskConfbyID($id);
					
					if(!empty($data))
					{
						$data = $data[0];
						//echo"<pre>";print_r($data);exit;
						$bunitData = $bunitModel->getSingleUnitData($data['businessunit_id']);
						if(!empty($bunitData))
						{
							$servicedeskconfform->businessunit_id->addMultiOption($bunitData['id'],utf8_encode($bunitData['unitname']));
						}
						
						$catData=$servicedeskconfmodel->getActiveCategoriesData();
						
						if(!empty($catData))
						{
							foreach($catData as $cat_name)
							{
								$catarray[$cat_name['id']]=$cat_name['name'];
							}
							if(!empty($catarray))
							{
								$servicedeskconfform->service_desk_id->setMultiOptions($catarray);
								
							}
						}
						
						
						$employeeData = $employeemodel->getEmployeesForServiceDesk($data['businessunit_id'],$data['department_id']);
						if($data['request_for']==1){
						$serviceDeptData = $servicedeskdepartmentmodel->getServiceDeskDepartmentDatabyID($data['service_desk_id']);
						}elseif($data['request_for']==2){
						$serviceDeptData = $assetscategoryModel->getuserallocatedAssetData($data['service_desk_id']);
						}
				
						if($data['department_id'] !='' && $data['department_id'] !='NULL')
						{
							$departmentlistArr = $departmentsmodel->getSingleDepartmentData($data['department_id']);
						}	 
						else
						    $departmentlistArr = array();
						if(!empty($employeeData))
						{ 
							foreach($employeeData as $empRes)
							{
								$emparray[$empRes['user_id']] = $empRes['userfullname'];
							}
							
							if(!empty($emparray))
							{
								$servicedeskconfform->request_recievers->setMultiOptions($emparray);
							    $servicedeskconfform->cc_mail_recievers->setMultiOptions($emparray); 
							}
						}
						
						if(sizeof($serviceDeptData) > 0)
						{
							if(sizeof($serviceDeptData) > 0)
							  $servicedeskconfform->service_desk_id->addMultiOption($serviceDeptData[0]['id'],utf8_encode($serviceDeptData[0]['service_desk_name']));
						}
						if(sizeof($departmentlistArr) > 0)
						{
							$servicedeskconfform->department_id->addMultiOption($departmentlistArr['id'],utf8_encode($departmentlistArr['deptname']));
						}
					  if($data['approver_1'] !='' && $data['approver_1'] != 'NULL') 
					    {
					    	if($data['approver_2'] !='' && $data['approver_2'] != 'NULL')
					    	$empstring =  $data['approver_2'];
					    	if($data['approver_3'] !='' && $data['approver_3'] != 'NULL')
					    	$empstring.=  ','.$data['approver_3'];
					    	$approver1data = $employeemodel->getApproverForServiceDesk($data['businessunit_id'],$data['department_id'],$empstring);
						     if(sizeof($approver1data) > 0)
						     {
						     	  foreach($approver1data as $approver1res)
						     	  {
								  	$servicedeskconfform->approver_1->addMultiOption($approver1res['user_id'],utf8_encode($approver1res['userfullname']));
						     	  }	
						     }
						     $approvingauthflag = 1;
					    }
					    
					  if($data['approver_2'] !='' && $data['approver_2'] != 'NULL') 
					    {
					    	if($data['approver_1'] !='' && $data['approver_1'] != 'NULL')
					    	$empstring =  $data['approver_1'];
					    	if($data['approver_3'] !='' && $data['approver_3'] != 'NULL')
					    	$empstring.=  ','.$data['approver_3'];
					    	$approver2data = $employeemodel->getApproverForServiceDesk($data['businessunit_id'],$data['department_id'],$empstring);
						     if(sizeof($approver2data) > 0)
						     {
						     	  foreach($approver2data as $approver2res)
						     	  {
								  	$servicedeskconfform->approver_2->addMultiOption($approver2res['user_id'],utf8_encode($approver2res['userfullname']));
						     	  }	
						     }
						     $approvingauthflag = 2;
					    }
					    
					  if($data['approver_3'] !='' && $data['approver_3'] != 'NULL') 
					    {
					    	if($data['approver_1'] !='' && $data['approver_1'] != 'NULL')
					    	$empstring =  $data['approver_1'];
					    	if($data['approver_2'] !='' && $data['approver_2'] != 'NULL')
					    	$empstring.=  ','.$data['approver_2'];
					    	$approver3data = $employeemodel->getApproverForServiceDesk($data['businessunit_id'],$data['department_id'],$empstring);	
						     if(sizeof($approver3data) > 0)
						     { 
						     	  foreach($approver3data as $approver3res)
						     	  {
								  	$servicedeskconfform->approver_3->addMultiOption($approver3res['user_id'],utf8_encode($approver3res['userfullname']));
						     	  }	
						     }
						     $approvingauthflag = 3;
					    }
					    
						$servicedeskconfform->populate($data);
						$servicedeskconfform->setDefault('businessunit_id',$data['businessunit_id']);
						$servicedeskconfform->setDefault('approvingauthority',$approvingauthflag);
						$servicedeskconfform->setDefault('request_for',$data['request_for']);
						if(sizeof($departmentlistArr) > 0)
						 $servicedeskconfform->setDefault('department_id',$data['department_id']);
						if($data['approver_1'] !='' && $data['approver_1'] != 'NULL') 
						 	$servicedeskconfform->setDefault('approver_1',$data['approver_1']);
						if($data['approver_2'] !='' && $data['approver_2'] != 'NULL') 
						  	$servicedeskconfform->setDefault('approver_2',$data['approver_2']);
						if($data['approver_3'] !='' && $data['approver_3'] != 'NULL')	
						  	$servicedeskconfform->setDefault('approver_3',$data['approver_3']);
						$this->view->approvingauthflag = $approvingauthflag; 
						$this->view->service_desk_flag = $data['service_desk_flag']; 	
						$this->view->request_recievers_value = $data['request_recievers'];
					    $this->view->cc_mail_recievers_value = ($data['cc_mail_recievers']!=''?$data['cc_mail_recievers']:'');  	
						$servicedeskconfform->setAttrib('action',BASE_URL.'servicedeskconf/edit/id/'.$id);
                        $this->view->data = $data;
                        $servicedeskconfform->businessunit_id->setAttrib("disabled", "disabled");
                        $servicedeskconfform->request_for->setAttrib("disabled", "disabled");
                        $servicedeskconfform->service_desk_flag->setAttrib("disabled", "disabled");
                        $servicedeskconfform->department_id->setAttrib("disabled", "disabled");
                        $servicedeskconfform->service_desk_id->setAttrib("disabled", "disabled");
                        $servicedeskconfform->approvingauthority->setAttrib("disabled", "disabled");
                        $servicedeskconfform->approver_1->setAttrib("disabled", "disabled");
                        $servicedeskconfform->approver_2->setAttrib("disabled", "disabled");
                        $servicedeskconfform->approver_3->setAttrib("disabled", "disabled");
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
				$this->view->ermsg = 'nodata';
			}
		}	
		catch(Exception $e)
		{
			   $this->view->ermsg = 'nodata';
		}	
		$this->view->form = $servicedeskconfform;
		if($this->getRequest()->getPost()){
			
      		$result = $this->save($servicedeskconfform);	
		    $this->view->msgarray = $result; 
		}
		$this->render('form');	
	}
	
	public function save($servicedeskconfform)
	{	
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
				 $loginUserId = $auth->getStorage()->read()->id;
		} 
		$servicedeskconfmodel = new Default_Model_Servicedeskconf();
	    $employeemodel = new Default_Model_Employee();
	    $departmentsmodel = new Default_Model_Departments();
	    $businessunitsmodel = new Default_Model_Businessunits();
        $servicedeskdepartmentmodel = new Default_Model_Servicedeskdepartment();
        $assetcategoriesModel = new Assets_Model_AssetCategories();

		$msgarray = array();
		$resultArr = array();
		$empstring = '';
	 	$servicedeskstring = '';
		$prevBusinessunit = '';
		$errorflag = "true";
		 $id = $this->_request->getParam('id');
         $businessunit_id = $this->_request->getParam('businessunit_id');
         $service_desk_flag = $this->_request->getParam('service_desk_flag');
         $department_id = $this->_request->getParam('department_id');
         $request_for = $this->_request->getParam('request_for');
         $service_desk_id = $this->_request->getParam('service_desk_id');
         $request_recievers = $this->_request->getParam('request_recievers');
         $approvingauthority = $this->_request->getParam('approvingauthority');
         $approver_1 = $this->_request->getParam('approver_1');
         $approver_2 = $this->_request->getParam('approver_2');
         $approver_3 = $this->_request->getParam('approver_3');
         $cc_mail_recievers = $this->_request->getParam('cc_mail_recievers');
         $attachment = $this->_request->getParam('attachment');
		 $description = trim($this->_request->getParam('description'));
		 $bunitid='';
		 $deptid='';
		 //validating category field
		 
	if(!empty($businessunit_id) && $request_for=='2' && $service_desk_id==''){
			
		 	$assetcategoriesModel = new Assets_Model_AssetCategories();
		 	$user_cat_data=$assetcategoriesModel->getActiveCategoriesData($bunitid,$deptid);
		    $asset_cat_array = array();
		 	if(count($user_cat_data) > 0)
		 	{
		 		 
		 		foreach($user_cat_data as $catdata)
		 		{
		 			$asset_cat_array[$catdata['id']] = $catdata['name'];
		 		}
		 	}
		 	
		 	$servicedeskconfform->service_desk_id->addMultiOptions(array(''=>'Select Category')+$asset_cat_array);
		 	
		}
	
		 
		 /** Start
		  * Validating Request reciever and CC reciever
		  * If both have same values then throwing an error
		  */
		
		if(!empty($request_recievers))
		 {
			  if(!empty($cc_mail_recievers))
			  {
			  	  $resultArr = array_intersect($request_recievers, $cc_mail_recievers);
			  }
			 
		 }
		 if(!empty($resultArr))
		 {
		 	 $msgarray['cc_mail_recievers'] = 'Executors and request viewers cannot be same.';
			 $errorflag = "false";
		 }
		 /**
           End validating Request reciever and CC reciever
		  */
		 
		 /** Start
		  * Validating selection of department if implementaion is department wise
		  */
		   if($service_desk_flag == 0)
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
		    * Validating approver level selection.
		    */
		   
		   if($approvingauthority !='')
		   {
		   	     if($approvingauthority == 1)
		   	     {
		   	     	if($approver_1 == '')
		   	     	{
			   	     	$msgarray['approver_1'] = 'Please select approver 1.';
				 		$errorflag = "false";
		   	     	}	
		   	     }else if($approvingauthority == 2)
		   	     {
			   	     if($approver_1 == '')
			   	     {
			   	     	$msgarray['approver_1'] = 'Please select approver 1.';
				 		$errorflag = "false";
			   	     }
		   	      	if($approver_2 == '')
			   	     {
			   	     	$msgarray['approver_2'] = 'Please select approver 2.';
				 		$errorflag = "false";
			   	     }
		   	     }else
		   	     {
		   	     	if($approver_1 == '')
			   	     {
			   	     	$msgarray['approver_1'] = 'Please select approver 1.';
				 		$errorflag = "false";
			   	     }
		   	      	if($approver_2 == '')
			   	     {
			   	     	$msgarray['approver_2'] = 'Please select approver 2.';
				 		$errorflag = "false";
			   	     }
		   	     	if($approver_3 == '')
			   	     {
			   	     	$msgarray['approver_3'] = 'Please select approver 3.';
				 		$errorflag = "false";
			   	     }
		   	     }
		   }
		   
		   /**
		    * End Approver level selection
		    */
		   
		   /** Start
              Validating unique service desk department
		    */
		  if(isset($service_desk_id)&& $id==''){
		   	$servicedeskstring = implode(",",$service_desk_id);
		$servicedeskidstringcomma = trim(str_replace("!@#", ",", $servicedeskstring),',');
		$servicedeskidstringArr = explode(",",$servicedeskidstringcomma);
	    foreach($servicedeskidstringArr as $key =>$val)
						{
							if (is_numeric($val))
							  $servicedeskidarr[] = $val;
							 
							else
							  $servicedeskname[] = $val;  						
						
						}
		
		$servicedeskids = implode(",",$servicedeskidarr);
		   }
		
		   	if($businessunit_id !='' && $service_desk_id !='' && $id == '')
		        {
		        	
					$serviceconfArr = $servicedeskconfmodel->checkuniqueServiceConfData($businessunit_id,$service_desk_flag,$servicedeskids,$department_id,$request_for);
					
					if(!empty($serviceconfArr))
					{
						if($serviceconfArr[0]['count'] > 0)
						{
							$msgarray['service_desk_id'] = 'Already configured.Please select a different category.';
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
			        if(!empty($implementationdata))
						$prevBusinessunit = $implementationdata['service_desk_flag'];
					if($service_desk_flag !='')
					{
						if($prevBusinessunit != $service_desk_flag)
						{
							$pendingRequestdata = $servicedeskconfmodel->getPendingServiceReqData($businessunit_id);
								if(!empty($pendingRequestdata))
								{
								   if($pendingRequestdata[0]['count'] > 0)
								   {
								   	    $msgarray['service_desk_flag'] = 'Applicability cannot be changed as requests are in pending state.';
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
        if($servicedeskconfform->isValid($this->_request->getPost()) && $errorflag == 'true'){
            
            try{
			$actionflag = '';
			$tableid  = ''; 
			$req_rec_string = '';
			$cc_string = '';
			if($id!=''){
			$servicedeskidarr='';
			}
			
			$req_rec_string = implode(',',$request_recievers);
			if(!empty($cc_mail_recievers))
			$cc_string = implode(',',$cc_mail_recievers);
            
			
	for($j=0;$j<sizeof($servicedeskidarr);$j++)
	 {	
	   if($id!=''){
	    	$data = array('businessunit_id'=>$businessunit_id,
			                 'department_id'=>($department_id!=''?$department_id:NULL), 
							 'service_desk_flag'=>$service_desk_flag,
			   				 'request_for'=>$request_for,
			   				 'service_desk_id'=>$service_desk_id,
			                 'request_recievers'=>$req_rec_string, 
							 'cc_mail_recievers'=>($cc_string !=''?$cc_string:NULL),
			   				 'approver_1'=>($approver_1 !=''?$approver_1:NULL),
			                 'approver_2'=>($approver_2 !=''?$approver_2:NULL),
			   				 'approver_3'=>($approver_3 !=''?$approver_3:NULL),
			   				 'attachment'=>($attachment !=''?$attachment:NULL),		 
							 'description'=>($description !=''?$description:NULL),
							  'modifiedby'=>$loginUserId,
							  'modifieddate'=>gmdate("Y-m-d H:i:s")
					);
					// $servicedeskconfform->request_for->setRequired(false);
					
	    }else{
			   $data = array('businessunit_id'=>$businessunit_id,
			                 'department_id'=>($department_id!=''?$department_id:NULL), 
							 'service_desk_flag'=>$service_desk_flag,
			   	           	 'request_for'=>$request_for,
			   				 'service_desk_id'=>$servicedeskidarr[$j],
			                 'request_recievers'=>$req_rec_string, 
							 'cc_mail_recievers'=>($cc_string !=''?$cc_string:NULL),
			   				 'approver_1'=>($approver_1 !=''?$approver_1:NULL),
			                 'approver_2'=>($approver_2 !=''?$approver_2:NULL),
			   				 'approver_3'=>($approver_3 !=''?$approver_3:NULL),
			   				 'attachment'=>($attachment !=''?$attachment:NULL),		 
							 'description'=>($description !=''?$description:NULL),
							  'modifiedby'=>$loginUserId,
							  'modifieddate'=>gmdate("Y-m-d H:i:s")
					);
	    }	
	    
					
				if($id!=''){
					$where = array('id=?'=>$id);  
					$actionflag = 2;
				}
				else
				{
					$data['createdby'] = $loginUserId;
					$data['createddate'] = gmdate("Y-m-d H:i:s");
					$data['isactive'] = 1;
					$where = '';
					$actionflag = 1;
				}
				
				$budata = array('service_desk_flag' => $service_desk_flag,
								'modifiedby'=>$loginUserId,
							  	'modifieddate'=>gmdate("Y-m-d H:i:s")
								);
				$buwhere = array('id=?'=>$businessunit_id);
				
				/* If implementation wise is changed then all the previous records are inactivated and new record is inserted 
				 * Else normal update - insert will take place. 
				 */ 
                if($prevBusinessunit != $service_desk_flag)
				{
					if(!empty($servicedeskidarr) && $j==0) {
						 $prevBUData = array('isactive'=>0,
						 					'modifiedby'=>$loginUserId,
								  			'modifieddate'=>gmdate("Y-m-d H:i:s")
						 					);
						 $prevBUwhere = array('businessunit_id=?'=>$businessunit_id);					
						  /*Inactivating previous records */
						  $prevBUId = $servicedeskconfmodel->SaveorUpdateServiceConfData($prevBUData, $prevBUwhere);
					} 
					  
					  $data['createdby'] = $loginUserId;
					  $data['createddate'] = gmdate("Y-m-d H:i:s");
					  $data['isactive'] = 1;
					  $where = '';
					  $actionflag = 1;
					  /* Inserting new record */
					
					  $Id = $servicedeskconfmodel->SaveorUpdateServiceConfData($data, $where);
				}
				else
				{
					 
					  /* Insert or update based on action */
				$Id = $servicedeskconfmodel->SaveorUpdateServiceConfData($data, $where);
				}
		
	 }
		
				/* Updating service desk flag in business unit table */
				$BId = $businessunitsmodel->SaveorUpdateBusinessUnits($budata, $buwhere);
				if($Id == 'update')
				{
				   $tableid = $id;
				   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Setting updated successfully."));
				}   
				else
				{
				   $tableid = $Id; 	
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Setting added successfully."));					   
				}   
	 
				$menuID = SERVICEDESKCONFIGURATION;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
				$db->commit();
				$this->_redirect('servicedeskconf');	
                  }
        catch(Exception $e)
          {
          	
          	 $db->rollBack();
             $msgarray['service_desk_id'] = "Something went wrong, please try again.";
             return $msgarray;
          }
		}
		
		else
		{
			$bunitid='';
			$deptid='';
			$messages = $servicedeskconfform->getMessages();
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
				
				$employeeData = $employeemodel->getEmployeesForServiceDesk($businessunit_id,$department_id);
				if($request_for==1) {
					$servicedeskData = $servicedeskconfmodel->getnotconfiguredActiveServiceDepartments($businessunit_id,$department_id,$request_for);
				}elseif($request_for==2){
					$servicedeskData = $assetcategoriesModel->getActiveCategoriesData($businessunit_id,$department_id);
				}
				
			  
				if($id == '')
					$servicedeskconfform->service_desk_id->clearMultiOptions();
				$servicedeskconfform->request_recievers->clearMultiOptions();
				$servicedeskconfform->cc_mail_recievers->clearMultiOptions();
				
				if(!empty($employeeData))
				{
					$servicedeskconfform->request_recievers->addMultiOption('','Select executor');
					$servicedeskconfform->cc_mail_recievers->addMultiOption('','Select request viewer');
					foreach($employeeData as $empres)
					{
						$servicedeskconfform->request_recievers->addMultiOption($empres['user_id'],utf8_encode($empres['userfullname']));
						$servicedeskconfform->cc_mail_recievers->addMultiOption($empres['user_id'],utf8_encode($empres['userfullname']));
					}
				}
				
				if($id == '')
				{
					if(!empty($servicedeskData))
					{
						$servicedeskconfform->service_desk_id->addMultiOption('','Select Category');
						foreach($servicedeskData as $servicedeskres)
						{
							if($request_for==1) {
								$servicedeskconfform->service_desk_id->addMultiOption($servicedeskres['id'].'!@#'.$servicedeskres['service_desk_name'],utf8_encode($servicedeskres['service_desk_name']));
							}elseif($request_for=2) {
								$servicedeskconfform->service_desk_id->addMultiOption($servicedeskres['id'].'!@#'.$servicedeskres['name'],utf8_encode($servicedeskres['name']));
							}
						}
					
					
					}
				}
				
				if($service_desk_flag == 0 && isset($servicedeskid))
				{
					for($j=0;$j<sizeof($servicedeskid);$j++)
	            {	
					$departmentlistArr = $departmentsmodel->getDepartmentList($businessunit_id);
					if(!empty($departmentlistArr))
					{
						foreach($departmentlistArr as $departmentlist)
						{
							$servicedeskconfform->department_id->addMultiOption($departmentlist['id'],utf8_encode($departmentlist['deptname']));
						}
					}
	            }
				}
			    $departmentlistArr = $departmentsmodel->getDepartmentList($businessunit_id);
					if(!empty($departmentlistArr))
					{
						foreach($departmentlistArr as $departmentlist)
						{
							$servicedeskconfform->department_id->addMultiOption($departmentlist['id'],utf8_encode($departmentlist['deptname']));
						}
					}
			
				if(isset($service_desk_id) && $service_desk_id != 0 && $service_desk_id != '' && $id == '')
					$servicedeskconfform->setDefault('service_desk_id',$service_desk_id);
				if(isset($department_id) && $department_id != 0 && $department_id != '')
					$servicedeskconfform->setDefault('department_id',$department_id);
			}

			if(isset($approvingauthority) && $approvingauthority != '')
			{
				if($approvingauthority == 1)
				{
					$approver1Data = $employeemodel->getApproverForServiceDesk($businessunit_id,$department_id,$empstring);
					if(!empty($approver1Data))
					{
						foreach ($approver1Data as $app1res)
						{
							$servicedeskconfform->approver_1->addMultiOption($app1res['user_id'],utf8_encode($app1res['userfullname']));
						}
					}
					
				}
				else if($approvingauthority == 2)
				{
					$approver1Data = $employeemodel->getApproverForServiceDesk($businessunit_id,$department_id,$empstring);
					if(!empty($approver1Data))
					{
						foreach ($approver1Data as $app1res)
						{
							$servicedeskconfform->approver_1->addMultiOption($app1res['user_id'],utf8_encode($app1res['userfullname']));
						}
					}
					
					if(isset($approver_1) && $approver_1 !='')
					{
						$empstring = $approver_1;
						$approver2Data = $employeemodel->getApproverForServiceDesk($businessunit_id,$department_id,$empstring);
						if(!empty($approver2Data))
						{
							foreach ($approver2Data as $app2res)
							{
								$servicedeskconfform->approver_2->addMultiOption($app2res['user_id'],utf8_encode($app2res['userfullname']));
							}
						}
					} 
				}
				else 
				{
					$approver1Data = $employeemodel->getApproverForServiceDesk($businessunit_id,$department_id,$empstring);
					if(!empty($approver1Data))
					{
						foreach ($approver1Data as $app1res)
						{
							$servicedeskconfform->approver_1->addMultiOption($app1res['user_id'],utf8_encode($app1res['userfullname']));
						}
					}
					
					if(isset($approver_1) && $approver_1 !='')
					{  
						$empstring = $approver_1;
						$approver2Data = $employeemodel->getApproverForServiceDesk($businessunit_id,$department_id,$empstring);
						if(!empty($approver2Data))
						{
							foreach ($approver2Data as $app2res)
							{
								$servicedeskconfform->approver_2->addMultiOption($app2res['user_id'],utf8_encode($app2res['userfullname']));
							}
						}
					}
					
					if(isset($approver_2) && $approver_2 !='')
					{  
						$empstring = $empstring.','.$approver_2;
						$approver3Data = $employeemodel->getApproverForServiceDesk($businessunit_id,$department_id,$empstring);
						if(!empty($approver3Data))
						{
							foreach ($approver3Data as $app3res)
							{
								$servicedeskconfform->approver_3->addMultiOption($app3res['user_id'],utf8_encode($app3res['userfullname']));
							}
						}
					}  
				}
				
				if(isset($approver_1) && $approver_1 != 0 && $approver_1 != '')
					$servicedeskconfform->setDefault('approver_1',$approver_1);
				if(isset($approver_2) && $approver_2 != 0 && $approver_2 != '')
					$servicedeskconfform->setDefault('approver_2',$approver_2);
				if(isset($approver_3) && $approver_3 != 0 && $approver_3 != '')
				$servicedeskconfform->setDefault('approver_3',$approver_3);					
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
		 $deleteflag=$this->_request->getParam('deleteflag');
		 $messages['message'] = '';
		 $messages['msgtype'] = '';
		 $actionflag = 3;
		 $count = 0;
		    if($id)
			{
			$servicedeskconfmodel = new Default_Model_Servicedeskconf();
			$servicedeskdepartmentmodel = new Default_Model_Servicedeskdepartment();
			  $servicedeskconfdata = $servicedeskconfmodel->getServiceDeskConfbyID($id);
			  if(!empty($servicedeskconfdata))
			  	$pendingRequestdata = $servicedeskconfmodel->getPendingServiceReqData($servicedeskconfdata[0]['businessunit_id']);
			  if(!empty($pendingRequestdata))
			  	$count = $pendingRequestdata[0]['count'];
			  if($count < 1)
			  {	
				  $serviceDeptData = $servicedeskdepartmentmodel->getServiceDeskDepartmentDatabyID($servicedeskconfdata[0]['service_desk_id']);
				  $data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
				  $where = array('id=?'=>$id);
				  $Id = $servicedeskconfmodel->SaveorUpdateServiceConfData($data, $where);
				    if($Id == 'update')
					{
					   $menuID = SERVICEDESKCONFIGURATION;
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
	
	
	public function getemployeesAction()
	{
		
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('getemployees', 'html')->initContext();
		$employeemodel = new Default_Model_Employee();
		$servicedeskconfmodel = new Default_Model_Servicedeskconf();
		$businessunitsmodel = new Default_Model_Businessunits();
		$assetcategoriesModel = new Assets_Model_AssetCategories();
		$elementid = $this->_request->getParam('elementid');
		$bunitid = $this->_request->getParam('bunitid');
		$deptid = $this->_request->getParam('deptid');
		$reqfor= $this->_request->getParam('reqfor');
		$employeeData = array();
		$servicedeskData = array();
		$implementationdata= array();
		$employeeData = $employeemodel->getEmployeesForServiceDesk($bunitid,$deptid);
		//echo '<pre>';
		if(isset($bunitid) && $bunitid != '')
			{
			if(!empty($reqfor)) {
				if($reqfor==1) {
					$servicedeskData = $servicedeskconfmodel->getnotconfiguredActiveServiceDepartments($bunitid,$deptid);
				}elseif($reqfor==2) {	
					$servicedeskData = $assetcategoriesModel->getActiveCategoriesData($bunitid,$deptid);
				}	
			}			
			//$servicedeskData = $servicedeskconfmodel->getActiveServiceDepartments($bunitid,$deptid,$reqfor);
			}
			if($bunitid !='')
			$implementationdata = $businessunitsmodel->getSingleUnitData($bunitid);
		//print_r($employeeData);
		//exit;
		$this->view->employeedata=$employeeData;
		$this->view->servicedeskData=$servicedeskData;
		$this->view->implementationdata=$implementationdata;
		$this->view->reqfor=$reqfor;
		
	}
	
	
	public function getapproverAction()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('getapprover', 'html')->initContext();
		$employeemodel = new Default_Model_Employee();
		$elementid = $this->_request->getParam('elementid');
		$bunitid = $this->_request->getParam('bunitid');
		$deptid = $this->_request->getParam('deptid');
		$approver_1 = $this->_request->getParam('approver_1');
		$approver_2 = $this->_request->getParam('approver_2');
		$employeeData = array();
		$approvercount ='';
		$empstring = '';
		if($elementid == 'approvingauthority')
		   $approvercount = 1;
		else if($elementid == 'approver_1')
		   $approvercount = 2;
		else 
		   $approvercount = 3;     

		if($approver_1 !='')
		  $empstring =  $approver_1;
		if($approver_2 !='')
		  $empstring.=','.$approver_2;	
		
	    $employeeData = $employeemodel->getApproverForServiceDesk($bunitid,$deptid,$empstring);  
		$this->view->employeedata=$employeeData;
		$this->view->approvercount=$approvercount;
		
	}
	
	public function getbunitimplementationAction()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('getbunitimplementation', 'json')->initContext();
		$businessunitsmodel = new Default_Model_Businessunits();
		$servicedeskconfmodel = new Default_Model_Servicedeskconf();
		$bunitid = $this->_request->getParam('bunitid');
		$result = array();
		if($bunitid !='')
		{
			$pendingRequestdata = $servicedeskconfmodel->getPendingServiceReqData($bunitid);
			if(!empty($pendingRequestdata))
			   $result['count'] = $pendingRequestdata[0]['count'];
			$implementationdata = $businessunitsmodel->getSingleUnitData($bunitid);
			if(!empty($implementationdata))
				$result['result'] = $implementationdata['service_desk_flag'];
		}
		
		$this->_helper->_json($result);
		
	}
	public function getassetsAction(){
		
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('getassets', 'html')->initContext();
		$auth = Zend_Auth::getInstance();
		$assetcategoriesModel = new Assets_Model_AssetCategories();
		$user_cat_data=$assetcategoriesModel->getActiveCategoriesData();
		$this->view->user_cat_data=$user_cat_data;
		
	      
	}
}

