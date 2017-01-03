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

class Default_FeedforwardmanagerController extends Zend_Controller_Action
{

    private $options;
										
	public function preDispatch()
	{		 
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('getmanagersratings', 'html')->initContext();
        $ajaxContext->addActionContext('getdetailedratings', 'html')->initContext();
        $ajaxContext->addActionContext('getdetailedratingsbyemp', 'html')->initContext();
        $ajaxContext->addActionContext('getdetailedratingsbyques', 'html')->initContext();
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

        $ffinitModel = new Default_Model_Feedforwardinit();
        $ffDataArr = $ffinitModel->getFFbyBUDept();
        $this->view->ffdataarr = $ffDataArr;
	}
	
	public function getmanagersratingsAction()
	{
    	$id = $this->_request->getParam('id');

    	$ffinitModel = new Default_Model_Feedforwardinit();
    	$ffdata = $ffinitModel->getFFbyBUDept($id);
    	$mgrRatData = $ffinitModel->getManagerRatingsByFFId($id);
	    if($ffdata[0]['ff_mode'] == 'Quarterly'){
	    $ffdata[0]['ff_period']="Q".$ffdata[0]['ff_period'];
        }
		else if($ffdata[0]['ff_mode'] == 'Half-yearly'){
		$ffdata[0]['ff_period']= "H".$ffdata[0]['ff_period'];
		}
		else {
		$ffdata[0]['ff_period']="Yearly";
		}
        $this->view->ffData = $ffdata[0];
        $this->view->mgrRatData = $mgrRatData;
	}
    
	public function getdetailedratingsAction()
	{
    	$ff_id = $this->_request->getParam('ff_id');
    	$mgr_id = $this->_request->getParam('mgr_id');
    	$pa_conf_id = $this->_request->getParam('pa_conf_id');
    	$emp_view_flag = $this->_request->getParam('emp_view_flag');
    	$flag = $this->_request->getParam('flag');

    	$ffinitModel = new Default_Model_Feedforwardinit();
    	$ffEmpRatModel = new Default_Model_Feedforwardemployeeratings();
    	
    	$mgrRatData = $ffinitModel->getManagerRatingsByFFId($ff_id, $mgr_id);
    	$empsData = $ffinitModel->getDetailEmpsDataByMgrId($ff_id, $mgr_id);
    	$quesData = $ffEmpRatModel->getFFQuesDataByIDs($empsData[0]['question_ids']);
    	
		// Employee response
		$emp_response = array();
		if($empsData[0]['employee_response'])
			$emp_response = json_decode($empsData[0]['employee_response'],true);
			
		// get rating details using configuration id
		$ratingsData = $ffEmpRatModel->getAppRatingsDataByConfgId($pa_conf_id);
		$ratingType = $ratingsData[0]['rating_type'];
		
		$ratingText = array();
		$ratingTextDisplay = array();
		$ratingValues = array();
		foreach ($ratingsData as $rd){
			$ratingText[] = $rd['rating_text'];
			$ratingTextDisplay[$rd['id']] = $rd['rating_text'];
			$ratingValues[$rd['id']] = $rd['rating_value']; 
		}
    	
        $this->view->mgrRatData = $mgrRatData[0];
        $this->view->empsData = $empsData;
        $this->view->quesData = $quesData;
		$this->view->ratingType = $ratingType;
		$this->view->ratingTextDisplay = $ratingTextDisplay;
		$this->view->ratingText = $ratingText; //json_encode($ratingText);
		$this->view->ratingValues = $ratingValues;
		$this->view->emp_response = $emp_response;
		$this->view->ff_id = $ff_id;
		$this->view->mgr_id = $mgr_id;
		$this->view->pa_conf_id = $pa_conf_id;
		$this->view->emp_view_flag = $emp_view_flag;
		$this->view->flag = $flag;
		if($flag == 'by_employee')
			$this->render('getratingsbyemployees');
    }
    
	public function getdetailedratingsbyempAction()
	{
    	$ff_id = $this->_request->getParam('ff_id');
    	$mgr_id = $this->_request->getParam('mgr_id');
    	$pa_conf_id = $this->_request->getParam('pa_conf_id');
    	$user_id = $this->_request->getParam('user_id');

    	$ffinitModel = new Default_Model_Feedforwardinit();
    	$ffEmpRatModel = new Default_Model_Feedforwardemployeeratings();
    	
    	$empsData = $ffinitModel->getDetailEmpsDataByMgrId($ff_id, $mgr_id, $user_id);
    	$quesData = $ffEmpRatModel->getFFQuesDataByIDs($empsData[0]['question_ids']);
    	
		// Employee response
		$emp_response = array();
		if($empsData[0]['employee_response'])
			$emp_response = json_decode($empsData[0]['employee_response'],true);
			
		// get rating details using configuration id
		$ratingsData = $ffEmpRatModel->getAppRatingsDataByConfgId($pa_conf_id);
		$ratingType = $ratingsData[0]['rating_type'];
		
		$ratingText = array();
		$ratingTextDisplay = array();
		$ratingValues = array();
		foreach ($ratingsData as $rd){
			$ratingText[] = $rd['rating_text'];
			$ratingTextDisplay[$rd['id']] = $rd['rating_text'];
			$ratingValues[$rd['id']] = $rd['rating_value']; 
		}
    	
        $this->view->empsData = $empsData;
        $this->view->quesData = $quesData;
		$this->view->ratingType = $ratingType;
		$this->view->ratingTextDisplay = $ratingTextDisplay;
		$this->view->ratingText = json_encode($ratingText);
		$this->view->ratingValues = $ratingValues;
		$this->view->emp_response = $emp_response;
		$this->view->user_id = $user_id;
    }
    
	public function getdetailedratingsbyquesAction()
	{
    	$ff_id = $this->_request->getParam('ff_id');
    	$mgr_id = $this->_request->getParam('mgr_id');
    	$pa_conf_id = $this->_request->getParam('pa_conf_id');
    	$ques_id = $this->_request->getParam('ques_id');

    	$ffinitModel = new Default_Model_Feedforwardinit();
    	$ffEmpRatModel = new Default_Model_Feedforwardemployeeratings();
    	
    	$empsData = $ffinitModel->getDetailEmpsDataByMgrId($ff_id, $mgr_id);
    	$quesData = $ffEmpRatModel->getFFQuesDataByIDs($empsData[0]['question_ids']);
    	
		// Employee response
		$emp_response = array();
		if($empsData[0]['employee_response'])
			$emp_response = json_decode($empsData[0]['employee_response'],true);
			
		// get rating details using configuration id
		$ratingsData = $ffEmpRatModel->getAppRatingsDataByConfgId($pa_conf_id);
		$ratingType = $ratingsData[0]['rating_type'];
		
		$ratingText = array();
		$ratingTextDisplay = array();
		$ratingValues = array();
		foreach ($ratingsData as $rd){
			$ratingText[] = $rd['rating_text'];
			$ratingTextDisplay[$rd['id']] = $rd['rating_text'];
			$ratingValues[$rd['id']] = $rd['rating_value']; 
		}
    	
        $this->view->empsData = $empsData;
        $this->view->quesData = $quesData;
		$this->view->ratingType = $ratingType;
		$this->view->ratingTextDisplay = $ratingTextDisplay;
		$this->view->ratingText = json_encode($ratingText);
		$this->view->ratingValues = $ratingValues;
		$this->view->emp_response = $emp_response;
		$this->view->ques_id = $ques_id;
		$this->view->emp_view_flag = $this->_request->getParam('emp_view_flag');
		
		//Condition is for checking of additional questions click event
		$this->view->additional_Qflag = '';
		if($ques_id == '')
		{
			$this->view->additional_Qflag = 'additional_flag';
		}
    }
	
}