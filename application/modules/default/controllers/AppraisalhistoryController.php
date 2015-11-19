<?php

/* ********************************************************************************* 
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
 * ****************************************************************************** */

/**
 * Appraisalhistory is used to get the appraisal history of an employee
 *
 * @author Rakesh
 */
class Default_AppraisalhistoryController extends Zend_Controller_Action
{
    private $_options;
    
    public function preDispatch()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('getselectedappraisaldata','html')->initContext();		
    }
	
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
    }
	
	public function indexAction()
	{
        $auth = Zend_Auth::getInstance();
		$loginUserId = 0;
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $businessunit_id = $auth->getStorage()->read()->businessunit_id;
            $department_id = $auth->getStorage()->read()->department_id; 
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
        }
		$obj_appraisal_history = new Default_Model_Appraisalhistory();
		$emp_appraisal_data = array();
		$emp_appraisal_data = $obj_appraisal_history->getEmpAppraisalHistory($loginUserId);
		//get the max rating value
		$max = 0;
		foreach( $emp_appraisal_data as $k => $v )
		{
			$max = max( array( $max, $v['consolidated_rating'] ) );
		}
		$this->view->max_rating = ($max>5)?10:5;
		$this->view->emp_appraisal_data = $emp_appraisal_data;
		$this->view->employee_id = $loginUserId;
	}
	/**
	** get individual 
	**/
	public function getselectedappraisaldataAction()
	{
		try
		{
			$appId = $this->_request->getParam('appId');
			$empId = $this->_request->getParam('empId');
			$period = $this->_request->getParam('period');
			$empAppraisalData = "";$questionsData = "";$categoriesData = "";$empData = "";$ratingsData = "";
			$empAppraisals = array();
			$ratingType = "";
			$ratingText = array();
			$ratingTextDisplay = array();
			$ratingValues = array();			
			if($appId && $empId && $period)
			{
				$empAppraisalModel = new Default_Model_Appraisalemployeeratings();
				$empAppraisals = $empAppraisalModel->getSelectedAppraisalData($appId,$empId,$period);
				$configId = isset($empAppraisals[0]['pa_configured_id'])?$empAppraisals[0]['pa_configured_id']:0;
				// get rating details using configuration id
				$appEmpRatingsModel = new Default_Model_Appraisalemployeeratings();
				$ratingsData = $appEmpRatingsModel->getAppRatingsDataByConfgId($configId,$appId);
				
				if(!empty($ratingsData))
					$ratingType = $ratingsData[0]['rating_type'];
				
				foreach ($ratingsData as $rd){
					$ratingText[] = $rd['rating_text'];
					$ratingTextDisplay[$rd['id']] = $rd['rating_text'];
					$ratingValues[$rd['id']] = $rd['rating_value']; 
				}
				if(!empty($empAppraisals))
				{
					if(!empty($empAppraisals[0]['employee_response']))
					{
						$empResponse = json_decode($empAppraisals[0]['employee_response']);
						$empResponseArray = get_object_vars($empResponse);
						$strQuestionIds = implode(",",array_keys($empResponseArray));
						$questionsData = $empAppraisalModel->getQuestionsData($strQuestionIds);
											
						$tmpRatingIdsObject = array_values($empResponseArray);
						$tmpRatingIdsArr = array();
						foreach($tmpRatingIdsObject as $ratingArr)
						{
							$tmpRatings = get_object_vars($ratingArr);
							$tmpRatingIdsArr[] = $tmpRatings['rating_id'];
						}
						if(!empty($empAppraisals[0]['manager_response']))
						{
							$managerResponse = json_decode($empAppraisals[0]['manager_response']);
							$managerResponseArray = get_object_vars($managerResponse);
							$managerRatingIdsObject = array_values($managerResponseArray);
							
							foreach($managerRatingIdsObject as $ratingArr)
							{
								$tmpRatings = get_object_vars($ratingArr);
								$tmpRatingIdsArr[] = $tmpRatings['rating'];
							}
						}
						$tmpRatingIdsStr = (!empty($tmpRatingIdsArr))?implode(",",$tmpRatingIdsArr):"";
						if(!empty($tmpRatingIdsStr))
						{
							$ratingsData = $empAppraisalModel->getRatingsData($tmpRatingIdsStr);
						}

						if(!empty($ratingsData))
						{
							$r = 0;
							foreach($ratingsData as $rdata)
							{
								$ratingsData[$rdata['id']] = $rdata;
								unset($ratingsData[$r]);
								$r++;
							}
						}						
					}
					$strCategories = $empAppraisals[0]['category_id'];
					$categoriesData = $empAppraisalModel->getCategories($strCategories);
				}
			}
			$appSkillsModel = new Default_Model_Appraisalskills();
			$skills = array();
			$skills = $appSkillsModel->getAppraisalSkillsData();
			$skills_arr = array();
			foreach($skills as $skill)
			{
				$skills_arr[$skill['id']] = $skill; 
			}
			$this->view->skills_arr = $skills_arr;			
			$this->view->selectedAppraisals = $empAppraisals;
			$this->view->categoriesData = $categoriesData;
			$this->view->empData = $empData;
			$this->view->questionsData = $questionsData;
			$this->view->ratingsData = $ratingsData;
			$this->view->ratingType = $ratingType;
			$this->view->ratingTextDisplay = $ratingTextDisplay;
			$this->view->ratingText = json_encode($ratingText);
			$this->view->ratingValues = $ratingValues;			
			$this->view->appraisalId = $appId;			
		}
		catch(Exception $e)
		{
			print_r($e);
		}
	}	
}
