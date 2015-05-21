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

class Default_AppraisalratingsController extends Zend_Controller_Action
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
    	$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $businessunit_id = $auth->getStorage()->read()->businessunit_id;
            $department_id = $auth->getStorage()->read()->department_id; 
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
        }	
    	$errorMsg = '';
    	$msgarray = array();
    	$bunitdataArr = array();
    	$flag = '';
    	$implementationflag = '';
        $appInitModel = new Default_Model_Appraisalinit();
        $appraisalratingsmodel = new Default_Model_Appraisalratings();
       	if($loginuserRole != SUPERADMINROLE && $loginuserGroup != MANAGEMENT_GROUP)
        {
            $appImpleData = sapp_PerformanceHelper::check_per_implmentation($businessunit_id, $department_id);
            $implementationflag = $appImpleData['performance_app_flag'];
           
         }    
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
					$sort = 'DESC';$by = 'i.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
				}
				else 
				{
					$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
					$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'i.modifieddate';
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
				$dataTmp = $appraisalratingsmodel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall,$implementationflag);
				//echo '<pre>';print_r($dataTmp);exit;
				array_push($data,$dataTmp);
				$this->view->dataArray = $data;
				$this->view->call = $call ;
				$this->view->messages = $this->_helper->flashMessenger->getMessages();
				$this->render('commongrid/performanceindex', null, true);          
                
        $this->view->msgarray = $msgarray; 
        $this->view->ermsg = $errorMsg;
        $this->view->flag = $flag;
    }
     
    public function index_oldAction()
    {
      
    $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;
					$loginuserGroup = $auth->getStorage()->read()->group_id;
					$businessUnit = $auth->getStorage()->read()->businessunit_id;
					$department = $auth->getStorage()->read()->department_id;
					}
		 
		$appraisalratingsmodel = new Default_Model_Appraisalratings();	
		$res = $appraisalratingsmodel->checkAccessAddratings($businessUnit,$department); 
		if(!empty($res))
		{
			
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
		$dataTmp = $appraisalratingsmodel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
		$this->render('commongrid/index', null, true);
		}
		else
		{
			$msg = "Sorry,Your Businessunit not configured yet";
			$this->view->ermsg = $msg;
		}
		
    }
    
public function addratingsAction()
    {
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('addratings', 'html')->initContext();
    	
    	$department_id = $this->_request->getParam('deptid');
    	$businessunit_id = $this->_request->getParam('bunitid');
    	$performanceappflag = $this->_request->getParam('perf_app_flag');	
        $appqsprivilegesModel = new Default_Model_Appraisalqsmain();
        $appempModel = new Default_Model_Appraisalgroupemployees();
        $appInitModel = new Default_Model_Appraisalinit();
        $appraisalratingsmodel = new Default_Model_Appraisalratings();
        $employeeIds = '';
        $msgarray = array();
        $check = array();
        $checkActiveApp = array();
        $appraisal_rating = '';
        $errorMsg = '';
        
        	$appImpleData = sapp_PerformanceHelper::check_per_implmentation($businessunit_id, $department_id);
            if(count($appImpleData) > 0)
            {                
                $this->view->imple_data = $appImpleData;
        		$checkActiveApp = $appInitModel->checkAppraisalExists($businessunit_id, $department_id,$performanceappflag);
        		if(count($checkActiveApp) > 0)
                {
                	$checkActiveApp = $checkActiveApp[0];
                	
						if($checkActiveApp['employee_response'] == 1 && $checkActiveApp['status'] == 1)
						{
							$implementationid = $appImpleData['id'];
							$initializationid = $checkActiveApp['id'];
							$check = $appraisalratingsmodel->getAppraisalRatingsbyInitId($initializationid);
							$appraisal_rating = ($appImpleData['appraisal_ratings'] == 1 ? 5:10);
							
						}
						else
						{
							if($checkActiveApp['enable_step'] == 1)
								$errorMsg = 'Appraisal process is enabled to employees.';
							if($checkActiveApp['status'] == 2)	
								$errorMsg = 'Appraisal process is closed.';
							if($checkActiveApp['employee_response'] == 2)	
								$errorMsg = 'Ratings can not be edited as employees have started giving response.';	
						}
                	
                }else 
                {
                	$errorMsg = 'Appraisal process is not yet initialized.';
                }
            } 
            else 
            {
                $errorMsg = 'Appraisal process is not yet configured.';
            }    
        
        //$this->view->flag = $flag;
        $this->view->msgarray = $msgarray; 
        $this->view->ermsg = $errorMsg;
        $this->view->appraisal_rating = $appraisal_rating;                    
		$this->view->data = $check;
		$this->view->checkActiveApp = $checkActiveApp;
		$this->view->businessunit_id = $businessunit_id;
		$this->view->department_id = $department_id;
    }
        
public function addAction()
	{
	   $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;
					$loginuserGroup = $auth->getStorage()->read()->group_id;
					$businessUnit = $auth->getStorage()->read()->businessunit_id;
					$department = $auth->getStorage()->read()->department_id;
					
		}
		$callval = $this->getRequest()->getParam('call');
		$errorMsg = '';
		$msgarray = array();
		$buOptions = '';
        $deptOptions = '';
        $performanceappflag = '';
        $appraisalratingsform = new Default_Form_Appraisalratings();
        $appInitModel = new Default_Model_Appraisalinit();
		if($loginuserRole != SUPERADMINROLE && $loginuserGroup != MANAGEMENT_GROUP)
        {
            $appImpleData = sapp_PerformanceHelper::check_per_implmentation($businessUnit, $department);
            if(count($appImpleData) > 0)
            {
            	$this->view->imple_data = $appImpleData;
                $checkActiveApp = $appInitModel->checkAppraisalExists($businessUnit, $department,$appImpleData['performance_app_flag']);
                
                if(count($checkActiveApp) > 0)
                {
                		$checkActiveApp = $checkActiveApp[0];
						$appraisalratingsmodel = new Default_Model_Appraisalratings();	
						$appraisal_rating = $appImpleData['appraisal_ratings'];									
						$id = $appImpleData['id'];
						$initializationid = $checkActiveApp['id'];
						$appraisal_rating = ($appraisal_rating == 1 ? 5:10);		
							$data = $appraisalratingsmodel->getAppraisalRatingsbyInitId($initializationid);
							 if(!empty($data))
							 {
							 	//$edit = self::editAction($initializationid);
							 	$appInitdata = $appInitModel->getConfigData($data[0]['pa_initialization_id']);
								$appraisalratingsform->setAttrib('action',DOMAIN.'appraisalratings/edit/id/'.$id);
		                        $this->view->data = $data;
		                        $this->view->checkActiveApp = $appInitdata[0];
		                       
							 }	
							 else{	
								$appraisalratingsform->setAttrib('action',DOMAIN.'appraisalratings/add');
								$this->view->appraisal_rating = $appraisal_rating;
								$this->view->checkActiveApp = $checkActiveApp;
					 			}
					 			
					 			if($this->getRequest()->getPost())
					 			{
								 $result = $this->save($appraisalratingsform);	
								 $this->view->msgarray = $result; 
								} 
								
								/* Fetch business unit and department name*/
									$appImpleData = sapp_PerformanceHelper::check_per_implmentation($businessUnit, $department);
					        		$performanceappflag = $appImpleData['performance_app_flag'];
					        		$optionsArray = $this->buildoptions($businessUnit,$department,$performanceappflag);
					        		$buOptions = $optionsArray['buoptions'];
					        		$deptOptions = isset($optionsArray['deptoptions'])?$optionsArray['deptoptions']:'';
								/* End */
					        	$this->view->performanceappflag = $performanceappflag;	
					        	$this->view->buOptions = $buOptions;
        						$this->view->deptOptions = $deptOptions;
								$this->view->form = $appraisalratingsform; 
								$this->view->msgarray = $msgarray;
								 $this->view->appraisal_rating = $appraisal_rating;
                }
            	else 
                {
                    $errorMsg = 'Active Appraisal process is not there.';
                }		 
            } 
            else 
            {
                $errorMsg = 'Appraisal process is not yet configured.';
            }
            $this->view->ermsg = $errorMsg; 
            $this->render('form');	
        }else
        {
        	$bunitModel = new Default_Model_Businessunits();
        	$flag = 1;
			//$bunitdataArr = $bunitModel->fetchAll('isactive=1','unitname')->toArray();
			$bunitdataArr = $appInitModel->getbusinnessunits_initialized('');
			$this->view->bunitdataarr = $bunitdataArr;
			$this->view->flag = $flag;
        	if($this->getRequest()->getPost())
 			{
			 $result = $this->save($appraisalratingsform);	
			 $this->view->msgarray = $result; 
			}
			$this->render('managementform');
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
            $login_group_id = $auth->getStorage()->read()->group_id;
            $login_role_id = $auth->getStorage()->read()->emprole;
            $businessUnit = $auth->getStorage()->read()->businessunit_id;
			$department = $auth->getStorage()->read()->department_id;
        }
        
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		$objName = 'appraisalratings';
		$appraisalratingsmodel = new Default_Model_Appraisalratings();
		$appInitModel = new Default_Model_Appraisalinit();
		$businessunitmodel = new Default_Model_Businessunits();
        $deptmodel = new Default_Model_Departments();
        $buname = '';
        $deptname = '';
        $performanceappflag = '';
		try
		{
		    if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					$data = $appraisalratingsmodel->getAppraisalRatingsbyInitId($id);
					$previ_data = sapp_Global::_checkprivileges(APPRAISALRATINGS,$login_group_id,$login_role_id,'edit');
					
					if(!empty($data))
					{
						//$data = $data[0]; 
                        $appInitdata = $appInitModel->getConfigData($data[0]['pa_initialization_id']);   
                        if($login_role_id == SUPERADMINROLE || $login_group_id == MANAGEMENT_GROUP)
                        {
                        	if(!empty($appInitdata))
                        	  {
	                        	 $businessUnit = $appInitdata[0]['businessunit_id'];
								 $department = $appInitdata[0]['department_id'];
                        	  }
                        }	
                        	$appImpleData = sapp_PerformanceHelper::check_per_implmentation($businessUnit, $department);
                        	if(!empty($appImpleData))
					        	$performanceappflag = $appImpleData['performance_app_flag'];
                        		
                        		$buDataArr = $businessunitmodel->getSingleUnitData($businessUnit);
                        		if(!empty($buDataArr))
                        		{
                        			$buname = $buDataArr['unitname'];
                        		}
			        		
                        		if($performanceappflag == 0)
                        		{
					        		if($department!='')
					        		{
					        			$deptArr = $deptmodel->getSingleDepartmentData($department);
					        			if(!empty($deptArr))
					        			{
					        				$deptname = $deptArr['deptname'];
					        			}
					        		}
                        		}
                        
                        $this->view->checkActiveApp = $appInitdata[0];                    
					    $this->view->data = $data;
					    $this->view->ermsg = ''; 
                    	$this->view->previ_data = $previ_data;
                   	 	$this->view->id = $id;
                    	$this->view->controllername = $objName;
                    	$this->view->buname = $buname;
                    	$this->view->deptname = $deptname;
                                                
        			
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
		
	}
	
	/**
	 * 
	 * Edit function to prepopulate the data.
	 * 
	 */
	public function editAction($id='')
	{
	    $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;
					$loginuserGroup = $auth->getStorage()->read()->group_id;
					$businessUnit = $auth->getStorage()->read()->businessunit_id;
					$department = $auth->getStorage()->read()->department_id;
					
		}
		$appInitModel = new Default_Model_Appraisalinit();
		$msgarray = array();
		$buOptions = '';
        $deptOptions = '';
		if($id == '')
		$id = $this->getRequest()->getParam('id');
		
		if($loginuserRole != SUPERADMINROLE && $loginuserGroup != MANAGEMENT_GROUP)
        {
				$callval = $this->getRequest()->getParam('call');
				if($callval == 'ajaxcall')
					$this->_helper->layout->disableLayout();
				$appraisalratingsform = new Default_Form_Appraisalratings();
				$appraisalratingsmodel = new Default_Model_Appraisalratings();
        		$performanceappflag = '';
				$appraisalratingsform->submit->setLabel('Update');
				$res = $appraisalratingsmodel->checkAccessAddratings($businessUnit,$department); 
				foreach($res as $result)
							{
								$appraisal_rating = $result['appraisal_ratings'];
							}
				
					$appraisal_rating = ($appraisal_rating == 1 ? 5:10);
				try
		        {		
					if($id)
					{
					    if(is_numeric($id) && $id>0)
						{
							$data = $appraisalratingsmodel->getAppraisalRatingsbyInitId($id);
						if(!empty($data))
							{
								//$data = $data[0]; 
		                         //echo "<pre>"; print_r($data);echo "</pre>";                      
								// $appraisal_rating = 1;
								$appInitdata = $appInitModel->getConfigData($data[0]['pa_initialization_id']);
								$appraisalratingsform->setAttrib('action',DOMAIN.'appraisalratings/edit/id/'.$id);
								
								/* Fetch business unit and department name*/
									$appImpleData = sapp_PerformanceHelper::check_per_implmentation($businessUnit, $department);
					        		$performanceappflag = $appImpleData['performance_app_flag'];
					        		$optionsArray = $this->buildoptions($businessUnit,$department,$performanceappflag);
					        		$buOptions = $optionsArray['buoptions'];
					        		$deptOptions = isset($optionsArray['deptoptions'])?$optionsArray['deptoptions']:'';
								/* End */
					        	$this->view->performanceappflag = $performanceappflag;	
					        	$this->view->buOptions = $buOptions;
        						$this->view->deptOptions = $deptOptions;	
		                        $this->view->data = $data;
		                        $this->view->checkActiveApp = $appInitdata[0];
		                        $this->view->appraisal_rating = $appraisal_rating;
		                         
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
				$this->view->form = $appraisalratingsform;
				if($this->getRequest()->getPost()){
		      		$result = $this->save($appraisalratingsform);	
				    $this->view->msgarray = $result; 
				}
				$this->render('form');	
        }else
        {
        	$buOptions = '';
        	$deptOptions = '';
        	$businessUnitId = '';
        	$department = '';
        	$deptArr = array();
        	$flag = 2;
        	$appraisaldataArr = $appInitModel->getAppDataById($id);
        	if(!empty($appraisaldataArr))
        	{
        		$businessUnitId = $appraisaldataArr['businessunit_id'];
        		$departmentId = ($appraisaldataArr['department_id']!='null'?$appraisaldataArr['department_id']:'');
        		$appImpleData = sapp_PerformanceHelper::check_per_implmentation($businessUnitId, $departmentId);
        		$performanceappflag = $appImpleData['performance_app_flag'];
        		$optionsArray = $this->buildoptions($businessUnitId,$departmentId,$performanceappflag);
        		$buOptions = $optionsArray['buoptions'];
        		$deptOptions = isset($optionsArray['deptoptions'])?$optionsArray['deptoptions']:'';
        	}
        	if($this->getRequest()->getPost()){
		      		$result = $this->save($appraisalratingsform);	
				    $this->view->msgarray = $result; 
				}
        	
        	$this->view->appraislaid = $id;
        	$this->view->performanceappflag = $performanceappflag;
        	$this->view->businessUnitId = $businessUnitId;
        	$this->view->departmentId = $departmentId;
        	$this->view->buOptions = $buOptions;
        	$this->view->deptOptions = $deptOptions;
        	$this->view->flag = $flag;
        	$this->render('managementform');
        }		
	}
	
public function buildoptions($businessUnitId,$departmentId,$performanceappflag)
{
			$businessunitmodel = new Default_Model_Businessunits();
        	$deptmodel = new Default_Model_Departments();
        	$deptOptions = '';
				$buDataArr = $businessunitmodel->getSingleUnitData($businessUnitId);
        		if(!empty($buDataArr))
        		{
        			$buOptions = "<option value=".$buDataArr['id'].">".utf8_encode($buDataArr['unitname'])."</option>";
        		}
        		
        		if($performanceappflag == 0)
        		{
	        		if($departmentId!='')
	        		{
	        			$deptArr = $deptmodel->getSingleDepartmentData($departmentId);
	        		}
        		}

        		if(!empty($deptArr))
        		{
        			$deptOptions = "<option value=".$deptArr['id'].">".utf8_encode($deptArr['deptname'])."</option>";
        		}
        		
        return array('buoptions' => $buOptions,'deptoptions'=> $deptOptions);		
}	
	   
public function save($appraisalratingsform)
	{	
	  $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
				 $loginUserId = $auth->getStorage()->read()->id;
				 $loginRole = $auth->getStorage()->read()->emprole;
				 $loginGroup = $auth->getStorage()->read()->group_id;
				 $businessUnit = $auth->getStorage()->read()->businessunit_id;
				 $department = $auth->getStorage()->read()->department_id;
				} 
	    $appraisalratingsmodel = new Default_Model_Appraisalratings();
	    $app_init_model = new Default_Model_Appraisalinit();
	    if($loginRole != SUPERADMINROLE && $loginGroup != MANAGEMENT_GROUP)
        {
        	$businessUnit = $businessUnit;
        	$department = $department;
        }else 
        {
        	$businessUnit = $this->_request->getParam('businessunit');
        	$department = $this->_request->getParam('departmentid');
        }
	   //$res = $appraisalratingsmodel->checkAccessAddratings($businessUnit,$department); 
	   $res = $app_init_model->check_per_implmentation($businessUnit, $department);
				$implementation = $res['performance_app_flag'];
				$rating_type  = $res['appraisal_ratings'];
				$configured_id  = $res['id'];
			
	   $msgarray = array();
		$resultArr = array();
		
		$errorflag = "true";
		 $id = $this->_request->getParam('id');
		 
		 $appraisalid = $this->_request->getParam('appraisalid');
		  
		// $update_id = $this->_request->getParam('update_id');
		
          $appraisal_ratings = ($rating_type == 1 ) ? 5:10;
         
          $db = Zend_Db_Table::getDefaultAdapter();		
       	 $db->beginTransaction();
		  $actionflag = '';
            try{
            if($id!=''){
            	for($i=0;$i<$appraisal_ratings;$i++)
	            {
	            	$rating_text =  $this->_request->getParam('rating_text_'.($i+1));
	            	$update_id = $this->_request->getParam('update_id_'.($i+1));
	            	/* for Update record  */
					$where = array('id=?'=>$update_id);
					$actionflag = 2; 
					$menumodel = new Default_Model_Menu();
					$tableid  = '';
					$data = array(
			            		 'rating_text'=> $rating_text,
								 'modifiedby_role' => $loginRole,
								 'modifiedby_group' => $loginGroup,
			                	 'modifiedby'=>$loginUserId,
							  	 'modifieddate'=>gmdate("Y-m-d H:i:s"),
					 			  );	
					 			  //$where = 'isactive=1';
					$Id = $appraisalratingsmodel->SaveorUpdateAppraisalRatingsData($data, $where);
				}
            }
            else
				{
					
	            for($i=0;$i<$appraisal_ratings;$i++)
	            {
	            $rating_text =  $this->_request->getParam('rating_text_'.$i);
				$menumodel = new Default_Model_Menu();
				$tableid  = '';
				$data = array(
			                 'pa_configured_id'=>$configured_id, 
							 'pa_initialization_id' => $appraisalid,	
							 'rating_type'=>$rating_type,
							 'rating_value'=> $i+1,
							 'rating_text'=>$rating_text,
							 'createdby_role' => $loginRole,	
							 'createdby_group' => $loginGroup,	
							 'modifiedby_role' => $loginRole,
							 'modifiedby_group' => $loginGroup,
			                 'modifiedby'=>$loginUserId,
							  'modifieddate'=>gmdate("Y-m-d H:i:s"),
            				  'createdby' => $loginUserId,
							  'createddate' => gmdate("Y-m-d H:i:s"),
							  'isactive' => 1
					);
				$where = '';
				$actionflag = 1;
				
				 $Id = $appraisalratingsmodel->SaveorUpdateAppraisalRatingsData($data, $where);
               }
			}	 
				  
				if($Id == 'update')
				{
				   $tableid = $id;
				   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Ratings updated successfully."));
				}   
				else
				{
				   $tableid = $Id; 	
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Ratings added successfully."));					   
				}   
				$menuidArr = $menumodel->getMenuObjID('/appraisalratings');
				$menuID = $menuidArr[0]['id'];
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
				/** Start
					 * Sending Mails to employees
					 */
						$appraisalconfigmodel = new Default_Model_Appraisalconfig();
				       if($implementation == 0)
						$employeeDetailsArr = $appraisalconfigmodel->getUserDetailsByID($businessUnit,$department);
						else
						$employeeDetailsArr = $appraisalconfigmodel->getUserDetailsByID($businessUnit,'');
						
						$msg_add_update = ($Id == 'update') ? "updated" : "added" ;
           			 		    //Sending mail to Super admin
								$options['subject'] = APPLICATION_NAME.': Performance Appraisal Ratings '.ucfirst($msg_add_update);
                                $options['header'] = 'Performance Appraisal Configuration';
                                $options['toEmail'] = SUPERADMIN_EMAIL;  
                                $options['toName'] = 'Super Admin';
                                $options['message'] = 'Dear Super Admin, performance appraisal configuration '.$msg_add_update;
                               // $mail_id =  sapp_Global::_sendEmail($options); 
						// Sending mail to others
						if(!empty($employeeDetailsArr))
						{
							foreach($employeeDetailsArr as $emp)
							{
								$options['subject'] = APPLICATION_NAME.': Performance Appraisal Settings Added.';
                                $options['header'] = 'Performance Appraisal Configuration';
                                $options['toEmail'] = $emp['emailaddress'];  
                                $options['toName'] = $emp['userfullname'];
                                $options['message'] = 'Dear '.$emp['userfullname'].', performance appraisal configuration '.$msg_add_update;
                              //  $mail_id =  sapp_Global::_sendEmail($options); 
							}
						}
					/**
					 * End
					 */	
				$db->commit();
				$this->_redirect('appraisalratings');	
				//throw new Exception("Some error message");
                  }
        catch(Exception $e)
          {
          	 $db->rollBack();
          	 echo $e->getMessage();
			echo $e->getTraceAsString();
          	return $msgarray;
          }
		
	
	}
	
    
	
	
}

