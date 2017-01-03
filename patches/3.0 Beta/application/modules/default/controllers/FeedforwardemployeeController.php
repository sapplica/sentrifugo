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

class Default_FeedforwardemployeeController extends Zend_Controller_Action
{
    private $options;
										
	public function preDispatch()
	{		 
		
	}
	
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('save', 'json')->initContext();
    }

    public function indexAction()
    {
    	$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
		try
		{
			$ffEmpRatingsModel = new Default_Model_Feedforwardemployeeratings();
			$ffEmpRatingsData = $ffEmpRatingsModel->getFFDataByEmpID($loginUserId);	
			
			if(sizeof($ffEmpRatingsData)>0 && $ffEmpRatingsData[0]['employee_id'] == $loginUserId && $ffEmpRatingsData[0]['status'] == 1){
				if($ffEmpRatingsData[0]['ff_status'] =='Pending employee ratings')
				{
					$this->_redirect('feedforwardemployee/edit');
				}
				else
				{
					$ques_csv = '';
					if($ffEmpRatingsData[0]['qs_privileges']){
						$ques_csv .= $ffEmpRatingsData[0]['questions'];
					}
					
					// get all questions data based on above question ids
					$questions_data = $ffEmpRatingsModel->getFFQuesDataByIDs($ques_csv);
					
					// Employee response
					$emp_response = array();
					if($ffEmpRatingsData[0]['employee_response'])
						$emp_response = json_decode($ffEmpRatingsData[0]['employee_response'],true);
						
					// get rating details using configuration id
					$ratingsData = $ffEmpRatingsModel->getAppRatingsDataByConfgId($ffEmpRatingsData[0]['appraisal_id']);
					$ratingType = $ratingsData[0]['rating_type'];
					$ratingText = array();
					$ratingTextDisplay = array();
					$ratingValues = array();
					foreach ($ratingsData as $rd)
					{
						$ratingText[] = $rd['rating_text'];
						$ratingTextDisplay[$rd['id']] = $rd['rating_text'];
						$ratingValues[$rd['id']] = $rd['rating_value']; 
					}
					$this->view->ffEmpRatingsData = $ffEmpRatingsData;					
					$this->view->questions_data = $questions_data;
					$this->view->ratingType = $ratingType;
					$this->view->ratingTextDisplay = $ratingTextDisplay;
					$this->view->ratingText = json_encode($ratingText);
					$this->view->ratingValues = $ratingValues;
					$this->view->emp_response = $emp_response;
					$this->view->check_ratings_exists = $ratingsData;
				}	
			}
		    else
			{
				$this->view->rowexist = "norows";
           	}
		}
		catch(Exception $e)
		{
			echo $e->getMessage();
			$this->view->rowexist = "norows";
		}				    	
    }
    
	public function editAction()
    {
    	$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		try
		{   
			$message = $this->_getParam('msg');
			$flags = $this->_getParam('flags'); 
	    	if($message == 'saved')
	    	{
	    		if($flags == 'draft')
	    		{
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>'Employee response drafted successfully'));
					$this->_redirect('feedforwardemployee/edit');
	    		}
	    	}
			$ffEmpRatingsModel = new Default_Model_Feedforwardemployeeratings();
			$ffEmpRatingsData = $ffEmpRatingsModel->getFFDataByEmpID($loginUserId);
			if(sizeof($ffEmpRatingsData)>0 && $ffEmpRatingsData[0]['employee_id'] == $loginUserId && $ffEmpRatingsData[0]['status'] == 1 && $ffEmpRatingsData[0]['ff_status'] == APP_PENDING_EMP)
			{
				$ques_csv = '';
				if($ffEmpRatingsData[0]['qs_privileges']){
					$ques_csv .= $ffEmpRatingsData[0]['questions'];
				}
				// get all questions data based on above question ids
				$questions_data = $ffEmpRatingsModel->getFFQuesDataByIDs($ques_csv);
				// Employee response
				$emp_response = array();
				if($ffEmpRatingsData[0]['employee_response'])
					$emp_response = json_decode($ffEmpRatingsData[0]['employee_response'],true);

				$question_previs = array();
				if($ffEmpRatingsData[0]['qs_privileges'])
					$question_previs = json_decode($ffEmpRatingsData[0]['qs_privileges'],true);
				
				// get rating details using configuration id
				$ratingsData = $ffEmpRatingsModel->getAppRatingsDataByConfgId($ffEmpRatingsData[0]['appraisal_id']);
				$ratingType = $ratingsData[0]['rating_type'];
				$ratingText = array();
				$ratingTextDisplay = array();
				$ratingValues = array();
				foreach ($ratingsData as $rd){
					$ratingText[] = $rd['rating_text'];
					$ratingTextDisplay[$rd['id']] = $rd['rating_text'];
					$ratingValues[$rd['id']] = $rd['rating_value']; 
				}

				$this->view->ffEmpRatingsData = $ffEmpRatingsData;					
				$this->view->questions_data = $questions_data;
				$this->view->ratingType = $ratingType;
				$this->view->ratingTextDisplay = $ratingTextDisplay;
				$this->view->ratingText = json_encode($ratingText);
				$this->view->ratingValues = $ratingValues;
				$this->view->emp_response = $emp_response;
				$this->view->question_previs = $question_previs;
				$this->view->check_ratings_exists = $ratingsData;
				$this->view->messages = $this->_helper->flashMessenger->getMessages();
			}
		    else{
				$this->view->rowexist = "norows";
           	}
		}
		catch(Exception $e)
		{
			$this->view->rowexist = "norows";
		}		
    }
    
	public function saveAction()
    {
    	$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
			$loginuserFullName = $auth->getStorage()->read()->userfullname;
			$loginuserProfileImg = $auth->getStorage()->read()->profileimg;
		}
    	try
    	{
    		$ffEmpRatingsModel = new Default_Model_Feedforwardemployeeratings();
    		
			$id = $this->_request->getParam('id');
    		$employee_id = $this->_request->getParam('employee_id');
    		$initialization_id = $this->_request->getParam('initialization_id');
    		$config_id = $this->_request->getParam('config_id');
    		$flag = $this->_request->getParam('flag');
    		
            $ratingsData = $ffEmpRatingsModel->getAppRatingsDataByConfgId($config_id);
    		$ratingValues = array();
			foreach ($ratingsData as $rd)
				$ratingValues[$rd['id']] = $rd['rating_value']; 
            
            $appData = array(
            				'additional_comments'=>trim($this->_request->getParam('additional_comments')),	
							'modifiedby'=>$loginUserId,
							'modifiedby_role'=>$loginuserRole,
							'modifiedby_group'=>$loginuserGroup,
							'modifieddate'=>gmdate("Y-m-d H:i:s"),
					);
			
            	$emp_rating_arr = $this->_request->getParam('emp_rating');
            	$emp_comment_arr = $this->_request->getParam('emp_comment');
            	$emp_response = array();
            	$rating_sum = 0;
            	$ratings_given = 0;
	            if(sizeof($emp_rating_arr)>0 || sizeof($emp_comment_arr)>0){
	            	foreach($emp_rating_arr as $qid=>$val){
	            		if(isset($emp_rating_arr[$qid])){
	            			$rating_id = array_search($emp_rating_arr[$qid], $ratingValues);
	            			$rating_sum += $emp_rating_arr[$qid];
	            			$ratings_given++; 
	            		}
	            		else
	            			$rating_id = '';
	            		$emp_response[$qid] = array('comment'=>$emp_comment_arr[$qid],'rating_id'=>$rating_id);
	            	}
	            }
	            
	            $appData['employee_response'] = json_encode($emp_response,true);
            
			if($flag == 'submit'){
            	$appData['ff_status'] = 2;
            	$appData['consolidated_rating'] = $rating_sum/$ratings_given;
			}
			
			$appWhere = array('id=?'=>$id);
			$result1 = $ffEmpRatingsModel->SaveorUpdateFFEmpRatingsData($appData, $appWhere);
			
			
			if($result1)
			{
				$msg = 'saved';
				if($flag != 'submit'){
				$this->_redirect('feedforwardemployee/edit/msg/saved/flags/draft');
				}
				else {
					//Logs storing
					$tableid  = '';
					$actionflag = 1;
					$menuID = APPRAISE_YOUR_MANAGER;
					$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
			
					$this->_redirect('feedforwardemployee');
				}
				
			}
			else {
				$msg = 'err';
			}
    	}
    	catch(Exception $e)
        {
        	$msg = "Something went wrong, please try again.";
        }
        //$this->_helper->json(array('msg'=>$msg));
    }
}