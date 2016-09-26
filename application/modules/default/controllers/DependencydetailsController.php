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

class Default_DependencydetailsController extends Zend_Controller_Action
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
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);

		 if(in_array('dependency_details',$empOrganizationTabs)){
		 	$userID="";
		 	$auth = Zend_Auth::getInstance();
		 	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginUserRole = $auth->getStorage()->read()->emprole;
					$loginUserGroup = $auth->getStorage()->read()->group_id;
		 	}
		 	$userid = $this->getRequest()->getParam('userid');	//This is User_id taking from URL
		 	$employeeData=array();$empdata=array();
		 	$conText = "";
		 	$call = $this->_getParam('call');
		 	if($call == 'ajaxcall')
		 	{
		 		$this->_helper->layout->disableLayout();
		 		$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
		 		$conText = ($this->_getParam('context') !='')? $this->_getParam('context'):$this->getRequest()->getParam('context');
		 	}
		 	if($userid == '') $userid = $userID;
		 	$Uid = ($userid)?$userid:$userID;

		 	$dependencydetailsModel = new Default_Model_Dependencydetails();
		 	$employeeModal = new Default_Model_Employee();
		 	try
		 	{
			    if($Uid && is_numeric($Uid) && $Uid>0)
				{
					$empdata = $employeeModal->getsingleEmployeeData($Uid);
					if($empdata == 'norows')
					{
						$this->view->rowexist = "norows";
						$this->view->empdata = "";
					}
					else
					{
						$this->view->rowexist = "rows";
						if(!empty($empdata))
						{
							$view = Zend_Layout::getMvcInstance()->getView();
							$objname = $this->_getParam('objname');
							$refresh = $this->_getParam('refresh');

							$data = array();	$searchQuery = '';	$searchArray = array();		$tablecontent = '';

							if($refresh == 'refresh')
							{
								$sort = 'DESC';$by = 'modifieddate';$perPage = 10;$pageNo = 1;$searchData = '';
							}
							else
							{
								$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
								$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'modifieddate';
								$perPage = $this->_getParam('per_page',10);
								$pageNo = $this->_getParam('page', 1);
								$searchData = $this->_getParam('searchData');
								$searchData = rtrim($searchData,',');
								/** search from grid - START **/
								$searchData = $this->_getParam('searchData');
								if($searchData != '' && $searchData!='undefined')
								{
									$searchValues = json_decode($searchData);
									foreach($searchValues as $key => $val)
									{
										if($key == 'dependent_dob')
										{
											$searchQuery .= " ".$key." like '%".  sapp_Global::change_date($val,'database')."%' AND ";
										}
										else
										$searchQuery .= " ".$key." like '%".$val."%' AND ";
										$searchArray[$key] = $val;
									}
									$searchQuery = rtrim($searchQuery," AND");
								}
								/** search from grid - END **/
							}
							$objName = 'dependencydetails';
							$tableFields = array('action'=>'Action','dependent_name'=>'Dependent Name','dependent_relation'=>'Dependent Relation','dependent_dob'=>'Dependent DOB');

							$tablecontent = $dependencydetailsModel->getdependencydetailsData($sort, $by, $pageNo, $perPage,$searchQuery,$Uid);
							$dependencyRelationsArr = array(''=>'All',
									'brother'=>"Brother" ,
									'child'=>"Child",
									'ex spouse'=>"Ex Spouse",
									'father'=>"Father",
									'granddaughter'=>"Grand Daughter",
									'grandfather'=>"Grand Father",
									'grandmother'=>'Grand Mother',
									'grandson'=>"Grand Son",
									'lifepartner mother'=>'LifePartner Mother',
									'sister'=>"Sister",
									'spouse'=>'Spouse'							
									);
									$dataTmp = array('userid'=>$Uid,
										'sort' => $sort,
										'by' => $by,
										'pageNo' => $pageNo,
										'perPage' => $perPage,				
										'tablecontent' => $tablecontent,
										'objectname' => $objName,
										'extra' => array(),
										'tableheader' => $tableFields,
										'jsGridFnName' => 'getEmployeeAjaxgridData',
										'jsFillFnName' => '',
										'searchArray' => $searchArray,
										'add'=>'add',
										'menuName'=>'Dependency',
										'formgrid' => 'true',
										'unitId'=>$Uid,
										'call'=>$call,
										'context'=>$conText,
										'search_filters' => array(
										'dependent_dob' =>array('type'=>'datepicker'),
																				'dependent_relation' => array(
																					'type' => 'select',
																					'filter_data' => $dependencyRelationsArr,
									),
									)
									);

									array_push($data,$dataTmp);
									$this->view->id=$Uid;	
									$this->view->controllername = $objName;
									$this->view->dataArray = $data;
									$this->view->call = $call;
									$this->view->employeedata = $empdata[0];
									$this->view->messages = $this->_helper->flashMessenger->getMessages();
						}
						$this->view->empdata = $empdata;
					}
				}	
				else
				{
				   $this->view->rowexist = "norows";
				}
		 	}
		 	catch(Exception $e)
		 	{
		 		$this->view->rowexist = "norows";
		 	}
		 }else{
		 	$this->_redirect('error');
		 }
		}else{
			$this->_redirect('error');
		}
	}

	public function editAction()
	{
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);

		 if(in_array('dependency_details',$empOrganizationTabs)){
		 	$userID="";
		 	$auth = Zend_Auth::getInstance();
		 	if($auth->hasIdentity()){
		 		$loginUserId = $auth->getStorage()->read()->id;
		 		$loginUserRole = $auth->getStorage()->read()->emprole;
		 		$loginUserGroup = $auth->getStorage()->read()->group_id;
		 	}
		 	$userid = $this->getRequest()->getParam('userid');	
		 	$employeeData=array();$empdata=array();
		 	$conText = "";
		 	$call = $this->_getParam('call');
		 	if($call == 'ajaxcall')
		 	{
		 		$this->_helper->layout->disableLayout();
		 		$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
		 		$conText = ($this->_getParam('context') !='')? $this->_getParam('context'):$this->getRequest()->getParam('context');
		 	}
		 	if($userid == '') $userid = $userID;

		 	$dependencydetailsModel = new Default_Model_Dependencydetails();
		 	$employeeModal = new Default_Model_Employee();

		 	try
		 	{
			    if($userid && is_numeric($userid) && $userid>0 && $userid!=$loginUserId)
				{ 
		 		$isrowexist = $employeeModal->getsingleEmployeeData($userid);
		 		if($isrowexist == 'norows')
		 		$this->view->rowexist = "norows";
		 		else
		 		$this->view->rowexist = "rows";

		 		$empdata = $employeeModal->getActiveEmployeeData($userid);
		 		if(!empty($empdata))
		 		{
		 			$view = Zend_Layout::getMvcInstance()->getView();
		 			$objname = $this->_getParam('objname');
		 			$refresh = $this->_getParam('refresh');

		 			$data = array();	$searchQuery = '';	$searchArray = array();		$tablecontent = '';

		 			if($refresh == 'refresh')
		 			{
		 				$sort = 'DESC';$by = 'modifieddate';$perPage = 10;$pageNo = 1;$searchData = '';
		 			}
		 			else
		 			{
		 				$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
		 				$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'modifieddate';
		 				$perPage = $this->_getParam('per_page',10);
		 				$pageNo = $this->_getParam('page', 1);
		 				$searchData = $this->_getParam('searchData');
		 				$searchData = rtrim($searchData,',');
		 				/** search from grid - START **/
		 				$searchData = $this->_getParam('searchData');
		 				if($searchData != '' && $searchData!='undefined')
		 				{
		 					$searchValues = json_decode($searchData);
		 					foreach($searchValues as $key => $val)
		 					{
		 						$searchQuery .= " ".$key." like '%".$val."%' AND ";
		 						$searchArray[$key] = $val;
		 					}
		 					$searchQuery = rtrim($searchQuery," AND");
		 				}
		 				/** search from grid - END **/
		 			}
		 			$objName = 'dependencydetails';
		 			$Uid = ($userid)?$userid:$userID;

		 			$tableFields = array('action'=>'Action','dependent_name'=>'Dependent Name','dependent_relation'=>'Dependent Relation','dependent_dob'=>'Dependent DOB');

		 			$tablecontent = $dependencydetailsModel->getdependencydetailsData($sort, $by, $pageNo, $perPage,$searchQuery,$Uid);
		 			if($Uid != "")
		 			{
		 				$usersModel = new Default_Model_Users();
		 				$employeeData = $usersModel->getUserDetailsByIDandFlag($Uid);
		 			}
		 			$dependencyRelationsArr = array(''=>'All',
								'brother'=>"Brother" ,
						        'child'=>"Child",
								'ex spouse'=>"Ex Spouse",
								'father'=>"Father",
								'granddaughter'=>"Grand Daughter",
								'grandfather'=>"Grand Father",
								'grandmother'=>'Grand Mother',
								'grandson'=>"Grand Son",
								'lifepartner mother'=>'LifePartner Mother',
								'sister'=>"Sister",
								'spouse'=>'Spouse'							
								);
								$dataTmp = array('userid'=>$Uid,
					'sort' => $sort,
					'by' => $by,
					'pageNo' => $pageNo,
					'perPage' => $perPage,				
					'tablecontent' => $tablecontent,
					'objectname' => $objName,
					'extra' => array(),
					'tableheader' => $tableFields,
					'jsGridFnName' => 'getEmployeeAjaxgridData',
					'jsFillFnName' => '',
					'searchArray' => $searchArray,
					'add'=>'add',
					'menuName'=>'Dependency',
					'formgrid' => 'true',
					'unitId'=>$Uid,
					'call'=>$call,
					'context'=>$conText,
					'search_filters' => array(
									'dependent_dob' =>array('type'=>'datepicker'),
                                            'dependent_relation' => array(
                                                'type' => 'select',
                                                'filter_data' => $dependencyRelationsArr,
								),
								)
								);

								array_push($data,$dataTmp);
								$this->view->id=$Uid;	
								$this->view->controllername = $objName;
								$this->view->dataArray = $data;
								$this->view->call = $call ;
								$this->view->employeedata = $employeeData[0];
								$this->view->messages = $this->_helper->flashMessenger->getMessages();
		 		}
		 		$this->view->empdata = $empdata;
				}	
				else
				{
				   $this->view->rowexist = "norows";
				}
		 	}
		 	catch(Exception $e)
		 	{
		 		$this->view->rowexist = "norows";
		 	}
		 }else{
		 	$this->_redirect('error');
		 }
		}else{
			$this->_redirect('error');
		}
	}

	public function viewAction()
	{
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);

		 if(in_array('dependency_details',$empOrganizationTabs)){
		    $auth = Zend_Auth::getInstance();
		 	if($auth->hasIdentity()){
		 		$loginUserId = $auth->getStorage()->read()->id;
		 		$loginUserRole = $auth->getStorage()->read()->emprole;
		 		$loginUserGroup = $auth->getStorage()->read()->group_id;
		 	}
		 	$userid = $this->getRequest()->getParam('userid');	
		 	$employeeData=array();$empdata=array();$userID ='';
		 	$conText = "";
		 	$call = $this->_getParam('call');
		 	if($call == 'ajaxcall')
		 	{
		 		$this->_helper->layout->disableLayout();
		 		$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
		 		$conText = ($this->_getParam('context') !='')? $this->_getParam('context'):$this->getRequest()->getParam('context');
		 	}
		 	if($userid == '') $userid = $userID;
		 	$dependencydetailsModel = new Default_Model_Dependencydetails();
		 	$employeeModal = new Default_Model_Employee();
		 	try
		 	{
			    if($userid && is_numeric($userid) && $userid>0 && $userid!=$loginUserId)
				{
		 		$isrowexist = $employeeModal->getsingleEmployeeData($userid);
		 		if($isrowexist == 'norows')
		 		$this->view->rowexist = "norows";
		 		else
		 		$this->view->rowexist = "rows";
		 		$empdata = $employeeModal->getActiveEmployeeData($userid);
		 		if(!empty($empdata))
		 		{
		 			$view = Zend_Layout::getMvcInstance()->getView();

		 			$objname = $this->_getParam('objname');
		 			$refresh = $this->_getParam('refresh');

		 			$data = array();	$searchQuery = '';	$searchArray = array();		$tablecontent = '';

		 			if($refresh == 'refresh')
		 			{
		 				$sort = 'DESC';$by = 'modifieddate';$perPage = 10;$pageNo = 1;$searchData = '';
		 			}
		 			else
		 			{
		 				$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
		 				$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'modifieddate';
		 				$perPage = $this->_getParam('per_page',10);
		 				$pageNo = $this->_getParam('page', 1);
		 				$searchData = $this->_getParam('searchData');
		 				$searchData = rtrim($searchData,',');
		 				/** search from grid - START **/
		 				$searchData = $this->_getParam('searchData');
		 				if($searchData != '' && $searchData!='undefined')
		 				{
		 					$searchValues = json_decode($searchData);
		 					foreach($searchValues as $key => $val)
		 					{
		 						$searchQuery .= " ".$key." like '%".$val."%' AND ";
		 						$searchArray[$key] = $val;
		 					}
		 					$searchQuery = rtrim($searchQuery," AND");
		 				}
		 				/** search from grid - END **/
		 			}

		 			$objName = 'dependencydetails';

		 			$Uid = ($userid)?$userid:$userID;

		 			$tableFields = array('action'=>'Action','dependent_name'=>'Dependent Name','dependent_relation'=>'Dependent Relation','dependent_dob'=>'Dependent DOB');


		 			$tablecontent = $dependencydetailsModel->getdependencydetailsData($sort, $by, $pageNo, $perPage,$searchQuery,$Uid);
		 			if($Uid != "")
		 			{
		 				$usersModel = new Default_Model_Users();
		 				$employeeData = $usersModel->getUserDetailsByIDandFlag($Uid);
		 			}
		 			$dataTmp = array('userid'=>$Uid,
					'sort' => $sort,
					'by' => $by,
					'pageNo' => $pageNo,
					'perPage' => $perPage,				
					'tablecontent' => $tablecontent,
					'objectname' => $objName,
					'extra' => array(),
					'tableheader' => $tableFields,
					'jsGridFnName' => 'getEmployeeAjaxgridData',
					'jsFillFnName' => '',
					'searchArray' => $searchArray,
					'add'=>'add',
					'menuName'=>'Dependency',
					'formgrid' => 'true',
					'unitId'=>$Uid,
					'call'=>$call,
					'context'=>$conText);			

		 			array_push($data,$dataTmp);
		 			$this->view->id=$userid;	
		 			$this->view->controllername = $objName;
		 			$this->view->dataArray = $data;
		 			$this->view->call = $call ;
		 			$this->view->employeedata = $employeeData[0];
		 			$this->view->messages = $this->_helper->flashMessenger->getMessages();
		 		}
		 		$this->view->empdata = $empdata;
				}	
				else
				{
				   $this->view->rowexist = "norows";
				}
		 	}
		 	catch(Exception $e)
		 	{
		 		$this->view->rowexist = "norows";
		 	}
		 }else{
		 	$this->_redirect('error');
		 }
		}else{
			$this->_redirect('error');
		}
	}
	public function addpopupAction()
	{
		$msgarray = array();
		$dependencyRelationsArr = array(''=>'Select Dependent Relation',
								'brother'=>"Brother" ,
						        'child'=>"Child",
								'ex spouse'=>"Ex Spouse",
								'father'=>"Father",
								'granddaughter'=>"Grand Daughter",
								'grandfather'=>"Grand Father",
								'grandmother'=>'Grand Mother',
								'grandson'=>"Grand Son",
								'lifepartner mother'=>'LifePartner Mother',
								'sister'=>"Sister",
								'spouse'=>'Spouse'							
								);
								$auth = Zend_Auth::getInstance();
								if($auth->hasIdentity())
								{
									$loginUserId = $auth->getStorage()->read()->id;
								}
								$userId = $this->getRequest()->getParam('unitId');
								$userId = ($userId != "")?$userId:$loginUserId;
								Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");

								$dependencyDetailsform = new Default_Form_Dependencydetails();
								$dependencyDetailsform->setAttrib('action',BASE_URL.'dependencydetails/addpopup/unitId/'.$userId);
								$dependencyDetailsModel = new Default_Model_Dependencydetails();
								$dependentRel = $dependencyDetailsModel->getdependencyrelations($userId);
								if(!empty($dependentRel) && isset($dependentRel))
								{
									for($i=0;$i<sizeof($dependentRel);$i++)
									{
										if(array_key_exists($dependentRel[$i]['dependent_relation'],$dependencyRelationsArr))
										{
											unset($dependencyRelationsArr[$dependentRel[$i]['dependent_relation']]);
										}
									}
								}
								$dependencyDetailsform->dependent_relation->addMultiOptions($dependencyRelationsArr);
								$this->view->form = $dependencyDetailsform;
								$this->view->msgarray = $msgarray;
								if($this->getRequest()->getPost())
								{
									$result = $this->save($dependencyDetailsform,$userId);
									$this->view->form = $dependencyDetailsform;
									$this->view->msgarray = $result;
								}
	}
	public function editpopupAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$dependencyRelationsArr = array(''=>'Select Dependent Relation',
								'brother'=>"Brother" ,
						        'child'=>"Child",
								'ex spouse'=>"Ex Spouse",
								'father'=>"Father",
								'granddaughter'=>"Grand Daughter",
								'grandfather'=>"Grand Father",
								'grandmother'=>'Grand Mother',
								'grandson'=>"Grand Son",
								'lifepartner mother'=>'LifePartner Mother',
								'sister'=>"Sister",
								'spouse'=>'Spouse'							
								);
								Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
								$id = $this->_request->getParam('id');	//Taking Id(Primary key in table) from form....
								$user_id = $this->getRequest()->getParam('unitId');	//This is User_id taking from URL set to form...

								$user_id = ($user_id != "")?$user_id:$loginUserId;

								$dependencyDetailsform = new Default_Form_Dependencydetails();
								$dependencyDetailsModel = new Default_Model_Dependencydetails();
								if($id)
								{
									$data = $dependencyDetailsModel->getdependencydetailsRecord($id);
									$dependentRel = $dependencyDetailsModel->getdependencyrelations($user_id);
									if(!empty($dependentRel) && isset($dependentRel))
									{
										for($i=0;$i<sizeof($dependentRel);$i++)
										{
											if(($data[0]["dependent_relation"] != $dependentRel[$i]['dependent_relation']) && array_key_exists($dependentRel[$i]['dependent_relation'],$dependencyRelationsArr))
											{
												unset($dependencyRelationsArr[$dependentRel[$i]['dependent_relation']]);
											}
										}
									}
									$dependencyDetailsform->dependent_relation->addMultiOptions($dependencyRelationsArr);
									$dependencyDetailsform->setDefault("id",$data[0]["id"]);
									$dependencyDetailsform->setDefault("user_id",$user_id);
									$dependencyDetailsform->setDefault("dependent_name",$data[0]["dependent_name"]);
									$dependencyDetailsform->setDefault("dependent_relation",$data[0]["dependent_relation"]);
									$dependencyDetailsform->setDefault("dependent_custody",$data[0]["dependent_custody"]);
									$dependentdob = sapp_Global::change_date($data[0]["dependent_dob"], 'view');
									$dependencyDetailsform->setDefault('dependent_dob', $dependentdob);
									$dependencyDetailsform->setDefault("dependent_age",$data[0]["dependent_age"]);
									$dependencyDetailsform->setAttrib('action',BASE_URL.'dependencydetails/editpopup/unitId/'.$user_id);
									$this->view->id=$user_id;
								}
								$dependencyDetailsform->setAttrib('action',BASE_URL.'dependencydetails/editpopup/id/'.$id.'/unitId/'.$user_id);
								$this->view->form = $dependencyDetailsform;
								if($this->getRequest()->getPost())
								{
									$result = $this->save($dependencyDetailsform,$user_id);

									$this->view->msgarray = $result;
								}
	}

	public function save($dependencyDetailsform,$user_id)
	{
		$result ="";
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$date = new Zend_Date();
		if($dependencyDetailsform->isValid($this->_request->getPost()))
		{
			$dependencyDetailsModel = new Default_Model_Dependencydetails();
			$id = $this->getRequest()->getParam('id');	//This is id taking from URL set to form...
			$dependent_name = $this->_request->getParam('dependent_name');
			$dependent_relation = $this->_request->getParam('dependent_relation');
			$dependent_custody = $this->_request->getParam('dependent_custody');
			$dependentdob = $this->_request->getParam('dependent_dob',null);
			$dependentDOB = sapp_Global::change_date($dependentdob, 'database');
			$dependent_age = $this->_request->getParam('dependent_age');
			$data = array(  'dependent_name'=>$dependent_name,
								'dependent_relation'=>$dependent_relation,
								'dependent_custody'=>$dependent_custody,
								'dependent_dob'=>$dependentDOB,
								'dependent_age'=>$dependent_age,
								'user_id'=>$user_id,
								'modifiedby'=>$loginUserId,
			                    'modifieddate'=>gmdate("Y-m-d H:i:s")
			);
			if($id!='')
			{
				$where = array('id=?'=>$id);
				$actionflag = 2;
			}
			else
			{
				$data['createdby'] = $loginUserId;
				$data['createddate'] = gmdate("Y-m-d H:i:s");
				$where = '';
				$actionflag = 1;
			}
			$Id = $dependencyDetailsModel->SaveorUpdateEmployeedependencyData($data,$where);
			if($Id == 'update')
			{
				$tableid = $id;
				$this->view->successmessage = 'Employee dependency details updated successfully.';
			}
			else
			{
				$tableid = $Id;
				$this->view->successmessage = 'Employee depedency details added successfully.';
			}
			$menuID = EMPLOYEE;
			$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$user_id);
			Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
			$close = 'close';
			$this->view->popup=$close;
			$this->view->controllername = 'dependencydetails';
		}
		else
		{
			$messages = $dependencyDetailsform->getMessages();
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
	public function viewpopupAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$dependencyRelationsArr = array('brother'=>"Brother" ,
						        'child'=>"Child",
								'ex spouse'=>"Ex Spouse",
								'father'=>"Father",
								'granddaughter'=>"Grand Daughter",
								'grandfather'=>"Grand Father",
								'grandmother'=>'Grand Mother',
								'grandson'=>"Grand Son",
								'lifepartner mother'=>'LifePartner Mother',
								'sister'=>"Sister",
								'spouse'=>'Spouse'							
								);
								Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
								$id = $this->_request->getParam('id');	//Taking Id(Primary key in table) from form....
								$user_id = $this->getRequest()->getParam('unitId');	//This is User_id taking from URL set to form...
								$user_id = ($user_id != "")?$user_id:$loginUserId;
								$dependencyDetailsform = new Default_Form_Dependencydetails();
								$dependencyDetailsModel = new Default_Model_Dependencydetails();
								$dependencyDetailsform->removeElement("submit");
								$elements = $dependencyDetailsform->getElements();
								if(count($elements)>0)
								{
									foreach($elements as $key=>$element)
									{
										if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
											$element->setAttrib("disabled", "disabled");
										}
									}
								}
								if($id)
								{
									$data = $dependencyDetailsModel->getdependencydetailsRecord($id);
									$dependentRel = $dependencyDetailsModel->getdependencyrelations($user_id);
									if(!empty($dependentRel) && isset($dependentRel))
									{
										for($i=0;$i<sizeof($dependentRel);$i++)
										{
											if(($data[0]["dependent_relation"] != $dependentRel[$i]['dependent_relation']) && array_key_exists($dependentRel[$i]['dependent_relation'],$dependencyRelationsArr))
											{
												unset($dependencyRelationsArr[$dependentRel[$i]['dependent_relation']]);
											}
										}
									}
									$dependencyDetailsform->dependent_relation->addMultiOptions($dependencyRelationsArr);
									$dependencyDetailsform->setDefault("id",$data[0]["id"]);
									$dependencyDetailsform->setDefault("user_id",$user_id);
									$dependencyDetailsform->setDefault("dependent_name",$data[0]["dependent_name"]);
									$dependencyDetailsform->setDefault("dependent_relation",$data[0]["dependent_relation"]);
									$dependencyDetailsform->setDefault("dependent_custody",$data[0]["dependent_custody"]);
									$dependentdob = sapp_Global::change_date($data[0]["dependent_dob"], 'view');
									$dependencyDetailsform->setDefault('dependent_dob', $dependentdob);
									$dependencyDetailsform->setDefault("dependent_age",$data[0]["dependent_age"]);
									$dependencyDetailsform->setAttrib('action',BASE_URL.'dependencydetails/editpopup/unitId/'.$user_id);
									$this->view->id=$user_id;
								}
								$dependencyDetailsform->setAttrib('action',BASE_URL.'dependencydetails/editpopup/id/'.$id.'/unitId/'.$user_id);
								$this->view->form = $dependencyDetailsform;
								$this->view->data=$data;
								if($this->getRequest()->getPost())
								{
									$result = $this->save($dependencyDetailsform,$user_id);
									$this->view->msgarray = $result;
								}
	}
	public function deleteAction()
	{
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);

		 if(in_array('dependency_details',$empOrganizationTabs)){

		 	$auth = Zend_Auth::getInstance();
		 	if($auth->hasIdentity()){
		 		$loginUserId = $auth->getStorage()->read()->id;
		 	}
		 	$id = $this->_request->getParam('objid');

		 	$messages['message'] = '';$messages['msgtype'] = '';
		 	$actionflag = 3;
		 	if($id)
		 	{
		 		$dependencydetailsModel = new Default_Model_Dependencydetails();
		 		$data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
		 		$where = array('id=?'=>$id);
		 		$Id = $dependencydetailsModel->SaveorUpdateEmployeedependencyData($data,$where);
		 		if($Id == 'update')
		 		{
					$menuID = EMPLOYEE;
		 			$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
		 			$messages['message'] = 'Employee dependency details deleted successfully.';
		 			$messages['msgtype'] = 'success';
		 		}
		 		else{
		 			$messages['message'] = 'Employee dependency details  cannot be deleted.';
		 			$messages['msgtype'] = 'error';	
		 		}
		 	}
		 	else
		 	{
		 		$messages['message'] = 'Employee dependency details cannot be deleted.';
		 		$messages['msgtype'] = 'error';
		 	}
		 	$this->_helper->json($messages);
		 }else{
		 	$this->_redirect('error');
		 }
		}
	}

}


