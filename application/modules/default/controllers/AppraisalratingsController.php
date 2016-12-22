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
		$call = $this->_getParam('call');
		if($call == 'ajaxcall')
		{
			$this->_helper->layout->disableLayout();
		}
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
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
		$this->render('commongrid/performanceindex', null, true);
        $this->view->msgarray = $msgarray; 
        $this->view->ermsg = $errorMsg;
        $this->view->flag = $flag;
    }
	//function for add ratings(save ratings)
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
		$checkActiveApp = $appInitModel->checkAppraisalExists($businessunit_id,$department_id,$performanceappflag);
		if(count($checkActiveApp) > 0)
		{
			$checkActiveApp = $checkActiveApp[0];
			if($checkActiveApp['employee_response'] == 1 && $checkActiveApp['status'] == 1)
			{
				$initializationid = !empty($checkActiveApp['id'])?$checkActiveApp['id']:0;
				$check = $appraisalratingsmodel->getAppraisalRatingsbyInitId($initializationid);
				$appraisal_rating = ((isset($checkActiveApp['appraisal_ratings']) && $checkActiveApp['appraisal_ratings'] == 1)?5:10);
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
		}
		else 
		{
			$errorMsg = 'Appraisal process is not yet initialized.';
		}
        $this->view->msgarray = $msgarray; 
        $this->view->ermsg = $errorMsg;
        $this->view->appraisal_rating = $appraisal_rating;                    
		$this->view->data = $check;
		$this->view->checkActiveApp = $checkActiveApp;
		$this->view->businessunit_id = $businessunit_id;
		$this->view->department_id = $department_id;
    }
    //function to save ratings 
	public function addAction()
	{
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
		{
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
        $appraisal_rating = '';
        $appraisalratingsform = new Default_Form_Appraisalratings();
        $appInitModel = new Default_Model_Appraisalinit();
		if($loginuserRole != SUPERADMINROLE && $loginuserGroup != MANAGEMENT_GROUP && $loginuserGroup != HR_GROUP)
        {
			$checkActiveApp = $appInitModel->checkAppraisalExists($businessUnit,$department);
			if(count($checkActiveApp) > 0)
			{
				$checkActiveApp = !empty($checkActiveApp[0])?$checkActiveApp[0]:array();
				$config_id = !empty($checkActiveApp['id'])?$checkActiveApp['id']:0;
				$appraisalratingsmodel = new Default_Model_Appraisalratings();
				//Using configuration Id getting appraisal settings data 
				$ratings = $appInitModel->getConfigData($config_id); 
				if(!empty($ratings))
				{
					$appraisal_rating = !empty($ratings[0]['appraisal_ratings'])?$ratings[0]['appraisal_ratings']:0;  
					$appraisal_rating = ($appraisal_rating == 1 ? 5:10);
					$data = $appraisalratingsmodel->getAppraisalRatingsbyInitId($config_id);
					if(!empty($data))
					{
						$appInitdata = $appInitModel->getConfigData($data[0]['pa_initialization_id']);
						$appraisalratingsform->setAttrib('action',BASE_URL.'appraisalratings/edit/id/'.$config_id);
						$this->view->data = $data;
						$this->view->checkActiveApp = $appInitdata[0];
					}	
					 else
					{	
						$appraisalratingsform->setAttrib('action',BASE_URL.'appraisalratings/add');
						$this->view->appraisal_rating = $appraisal_rating;
						$this->view->checkActiveApp = $checkActiveApp;
					}
					if($this->getRequest()->getPost())
					{
						$result = $this->save($appraisalratingsform);	
						$this->view->msgarray = $result; 
					}
					/* Fetch business unit and department name*/
					$businessUnit = $checkActiveApp['businessunit_id'];
					$department = $checkActiveApp['department_id'];
					$optionsArray = $this->buildoptions($businessUnit,$department);
					$buOptions = $optionsArray['buoptions'];
					$deptOptions = isset($optionsArray['deptoptions'])?$optionsArray['deptoptions']:'';
					/* End */
					$performanceappflag = ($department == '') ? 1:0; //performanceappflag is assigning based on initialization table
					$this->view->performanceappflag = $performanceappflag;	
					$this->view->buOptions = $buOptions;
					$this->view->deptOptions = $deptOptions;
					$this->view->form = $appraisalratingsform; 
					$this->view->msgarray = $msgarray;
					$this->view->appraisal_rating = $appraisal_rating;
				}
			}
			else 
			{
				$errorMsg = 'Active Appraisal process is not there.';
			}
			$this->view->form = $appraisalratingsform; 
            $this->view->ermsg = $errorMsg; 
            $this->render('form');
        }
		else
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
					   	$performanceappflag = !empty($appInitdata[0]['performance_app_flag'])?$appInitdata[0]['performance_app_flag']:'';
						$buDataArr = $businessunitmodel->getSingleUnitData($businessUnit);
						if(!empty($buDataArr))
						{
							$buname = $buDataArr['unitname'];
						}
						if($performanceappflag == 0)
						{
							if($appInitdata[0]['department_id'] != '')
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
		{
			$id = $this->getRequest()->getParam('id');
		}
		$appraisalratingsform = new Default_Form_Appraisalratings();
		if($loginuserRole != SUPERADMINROLE && $loginuserGroup != MANAGEMENT_GROUP)
        {
			$callval = $this->getRequest()->getParam('call');
			if($callval == 'ajaxcall')
				$this->_helper->layout->disableLayout();
			$appraisalratingsmodel = new Default_Model_Appraisalratings();
			$performanceappflag = '';
			$appraisalratingsform->submit->setLabel('Update');
			$res = $appInitModel->getConfigData($id);
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
							$appInitdata = $appInitModel->getConfigData($data[0]['pa_initialization_id']);
							$appraisalratingsform->setAttrib('action',BASE_URL.'appraisalratings/edit/id/'.$id);
							/* Fetch business unit and department name*/
							$department = !empty($appInitdata[0]['department_id'])?$appInitdata[0]['department_id']:0;
							$performanceappflag = ($department == '') ? 1:0; //performanceappflag is assigning based on initialization table
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
			if($this->getRequest()->getPost())
			{
				$result = $this->save($appraisalratingsform);	
				$this->view->msgarray = $result; 
			}
			$this->render('form');	
        }
		else
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
        		$departmentId = $appraisaldataArr['department_id'];
        		$performanceappflag = ($departmentId == '') ? 1:0;
        		$optionsArray = $this->buildoptions($businessUnitId,$departmentId,$performanceappflag);
        		$buOptions = $optionsArray['buoptions'];
        		$deptOptions = isset($optionsArray['deptoptions'])?$optionsArray['deptoptions']:'';
        	}
        	if($this->getRequest()->getPost())
			{
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
	public function buildoptions($businessUnitId,$departmentId,$performanceappflag = '') //($businessUnitId,$departmentId,$performanceappflag)
	{
		$businessunitmodel = new Default_Model_Businessunits();
		$deptmodel = new Default_Model_Departments();
		$deptOptions = '';
		$buDataArr = $businessunitmodel->getSingleUnitData($businessUnitId);
		if(!empty($buDataArr))
		{
			$buOptions = "<option value=".$buDataArr['id'].">".utf8_encode($buDataArr['unitname'])."</option>";
		}
		//if($performanceappflag == 0)
		//{
			if($departmentId!='')
			{
				$deptArr = $deptmodel->getSingleDepartmentData($departmentId);
				if(!empty($deptArr))
				{
				$deptOptions = "<option value=".$deptArr['id'].">".utf8_encode($deptArr['deptname'])."</option>";
				}
			}
		//}
		return array('buoptions' => $buOptions,'deptoptions'=> $deptOptions);		
	}	
	   
	public function save($appraisalratingsform)
	{
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
			$loginRole = $auth->getStorage()->read()->emprole;
			$loginGroup = $auth->getStorage()->read()->group_id;
			$businessUnit = $auth->getStorage()->read()->businessunit_id;
			$department = $auth->getStorage()->read()->department_id;
			$loginUserfullname = $auth->getStorage()->read()->userfullname;
			$loginUserEmpId = $auth->getStorage()->read()->employeeId;
		} 
	    $appraisalratingsmodel = new Default_Model_Appraisalratings();
	    $app_init_model = new Default_Model_Appraisalinit();
	    if($loginRole != SUPERADMINROLE && $loginGroup != MANAGEMENT_GROUP && $loginGroup != HR_GROUP)
        {
        	$businessUnit = $businessUnit;
        	$department = $department;
        }
		else 
        {
        	$businessUnit = $this->_request->getParam('businessunit');
        	$department = $this->_request->getParam('departmentid');
        }
		$res = $app_init_model->checkAppraisalExists($businessUnit,$department);
		$implementation = isset($res[0]['performance_app_flag'])?$res[0]['performance_app_flag']:NULL;
		$rating_type  = !empty($res[0]['appraisal_ratings'])?$res[0]['appraisal_ratings']:NULL;
		$configured_id  = 0;
		$msgarray = array();
		$resultArr = array();
		$errorflag = "true";
		$id = $this->_request->getParam('id');
		$dept_id = $this->_request->getParam('departmentid');
		$implementation =  (is_numeric($dept_id) && $dept_id>0) ? 0 : 1;
		$appraisal_ratings =  $this->_request->getParam('appraisal_rating');
		$appraisalid = $this->_request->getParam('appraisalid');
		$db = Zend_Db_Table::getDefaultAdapter();		
		$db->beginTransaction();
		$actionflag = '';
		try
		{
            if($id!='')
			{
            	for($i=0;$i<$appraisal_ratings;$i++)
	            {
	            	$rating_text =  $this->_request->getParam('rating_text_'.($i+1));
	            	$update_id = $this->_request->getParam('update_id_'.($i+1));
	            	/* for Update record  */
					$where = array('id=?'=>$update_id);
					$actionflag = 2; 
					$tableid  = '';
					$data = array(
			            		 'rating_text'=> $rating_text,
								 'modifiedby_role' => $loginRole,
								 'modifiedby_group' => $loginGroup,
			                	 'modifiedby'=>$loginUserId,
							  	 'modifieddate'=>gmdate("Y-m-d H:i:s"),
					 			  );;
					$Id = $appraisalratingsmodel->SaveorUpdateAppraisalRatingsData($data, $where);
				}
            }
            else
			{
				for($i=0;$i<$appraisal_ratings;$i++)
				{
					$rating_text =  $this->_request->getParam('rating_text_'.$i);
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
			/*
			 *   Logs Storing
			 */
			$menuID = APPRAISALRATINGS;
			$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
			/*
			 *  Logs storing ends
			 */
			//to get initialization details using appraisal Id for Business Unit,Department,To Year
			$appraisal_details = $appraisalratingsmodel->getappdata($appraisalid);
			if(!empty($appraisal_details))
			{
				$bunit = $appraisal_details['unitname'];
				$dept = $appraisal_details['deptname'];
				$to_year = $appraisal_details['to_year'];
			}
			
			/** Start
			* Sending Mails to employees
			*/
			$appraisalconfigmodel = new Default_Model_Appraisalconfig();
			if($implementation == 0)
			{
				$employeeDetailsArr = $appraisalconfigmodel->getUserDetailsByID($businessUnit,$department);
			}
			else
			{
				$employeeDetailsArr = $appraisalconfigmodel->getUserDetailsByID($businessUnit,'');
			}
			$msg_add_update = ($Id == 'update') ? "updated" : "configured" ;
			$dept_str = ($dept == '') ? " ":"and department <b>$dept</b> "; 
			$emp_id_str = ($loginRole == SUPERADMINROLE) ? " ":"($loginUserEmpId)";
			//Preparing Employee array for Bcc
			$empArr = array();
			if(!empty($employeeDetailsArr))
			{
				$empArrList = '';
				foreach($employeeDetailsArr as $emp)
				{
					array_push($empArr,$emp['emailaddress']);
				}
				
			}
			//echo "<pre>";print_r($empArr);die();
			$options['subject'] = APPLICATION_NAME.': Performance Appraisal Ratings '.ucfirst($msg_add_update);
			$options['header'] 	= 'Performance Appraisal : Ratings';
			$options['bcc'] 	= $empArr; 
			$options['toEmail'] = SUPERADMIN_EMAIL; 
			$options['toName'] = 'Super Admin'; 
			$options['message'] = "<div style='padding: 0; text-align: left; font-size:14px; font-family:Arial, Helvetica, sans-serif;'>				
			<span style='color:#3b3b3b;'>Hi,</span><br />
			<div style='padding:20px 0 0 0;color:#3b3b3b;'>Performance appraisal ratings have been $msg_add_update for the year <b>$to_year</b> for business unit <b>$bunit</b> $dept_str by ".$loginUserfullname.$emp_id_str." </div>
			<div style='padding:20px 0 10px 0;'>Please <a href=".BASE_URL." target='_blank' style='color:#b3512f;'>click here</a> to login  to your <b>".APPLICATION_NAME."</b> account.</div>
			</div> ";
			$mail_id =  sapp_Global::_sendEmail($options);
			/**
			* End mails sending
			*/	
			$db->commit();
			$this->_redirect('appraisalratings');
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

