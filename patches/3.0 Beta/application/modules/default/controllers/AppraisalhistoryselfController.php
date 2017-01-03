<?php

/* * ******************************************************************************* 
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

class Default_AppraisalhistoryselfController extends Zend_Controller_Action {

    private $options;
    public $app_history_disc_array = array(1 => APP_TXT_EMP_SUBMIT, 2 => APP_TXT_L1_SUBMIT, 3 => APP_TXT_L2_SUBMIT, 4 => APP_TXT_L3_SUBMIT,
        5 => APP_TXT_L4_SUBMIT, 6 => APP_TXT_L5_SUBMIT, 7 => APP_TXT_COMPLETED);
    public $app_status_array = array(1 => APP_PENDING_EMP, 2 => APP_PENDING_L1, 3 => APP_PENDING_L2, 4 => APP_PENDING_L3,
        5 => APP_PENDING_L4, 6 => APP_PENDING_L5, 7 => APP_COMPLETED);

    public function preDispatch() {
        
    }

    public function init() {
        $this->_options = $this->getInvokeArg('bootstrap')->getOptions();
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('save', 'json')->initContext();
    }

    public function indexAction() {
        $appraisalHistoryModel = new Default_Model_Appraisalhistory();
        $call = $this->_getParam('call');
        if ($call == 'ajaxcall')
            $this->_helper->layout->disableLayout();

        $refresh = $this->_getParam('refresh');
        $dashboardcall = $this->_getParam('dashboardcall');

        $data = array();
        $searchQuery = '';
        $searchArray = array();
        $tablecontent = '';

        if ($refresh == 'refresh') {
            if ($dashboardcall == 'Yes')
                $perPage = DASHBOARD_PERPAGE;
            else
                $perPage = PERPAGE;
            $sort = 'DESC';
            $by = 'a.modifieddate';
            $pageNo = 1;
            $searchData = '';
            $searchQuery = '';
            $searchArray = '';
        }
        else {
            $sort = ($this->_getParam('sort') != '') ? $this->_getParam('sort') : 'DESC';
            $by = ($this->_getParam('by') != '') ? $this->_getParam('by') : 'a.modifieddate';
            if ($dashboardcall == 'Yes')
                $perPage = $this->_getParam('per_page', DASHBOARD_PERPAGE);
            else
                $perPage = $this->_getParam('per_page', PERPAGE);
            $pageNo = $this->_getParam('page', 1);
            /** search from grid - START * */
            $searchData = $this->_getParam('searchData');
            $searchData = rtrim($searchData, ',');
            /** search from grid - END * */
        }
        $flag='historyself';
        $dataTmp = $appraisalHistoryModel->getGrid($sort, $by, $perPage, $pageNo, $searchData, $call, $dashboardcall,$flag);

        array_push($data, $dataTmp);
        $this->view->dataArray = $data;
        $this->view->call = $call;
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        $this->render('commongrid/performanceindex', null, true);
    }

    public function viewAction() {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $loginUserId = $auth->getStorage()->read()->id;
        }
        $id = $this->getRequest()->getParam('id');
        try {
            $appEmpRatingsModel = new Default_Model_Appraisalemployeeratings();
            $app_rating_model = new Default_Model_Appraisalratings();
            if ($id != '') {
                if (is_numeric($id) && $id > 0) {
                    $appEmpRatingsData = $appEmpRatingsModel->getSelfAppraisalHistoryDataByAppID($id); //configuration_id
                    if (sizeof($appEmpRatingsData) > 0 && $appEmpRatingsData[0]['employee_id'] == $loginUserId && $appEmpRatingsData[0]['status'] == 2) {
                        if ($appEmpRatingsData[0]['appraisal_status'] == 'Pending employee ratings') {
                            $this->_redirect('appraisalself/edit');
                        } else {
                            // Check rating exist for appraisal. 
                            $checkRatingsExists = $app_rating_model->getAppraisalRatingsbyInitId($appEmpRatingsData[0]['pa_initialization_id']);

                            // get all Categories Data based on category ids
                            $categories_data = $appEmpRatingsModel->getAppCateDataByIDs($appEmpRatingsData[0]['category_id']);

                            // get question previleges data of employee for that initialization
                            $appEmpQuesPrivData = $appEmpRatingsModel->getAppEmpQuesPrivData($appEmpRatingsData[0]['pa_initialization_id'], $appEmpRatingsData[0]['employee_id']);

                            // merging HR and Manager questions
                            $ques_csv = '';
                            if ($appEmpQuesPrivData[0]['hr_qs']) {
                                $ques_csv .= $appEmpQuesPrivData[0]['hr_qs'];
                            }
                            if ($appEmpQuesPrivData[0]['manager_qs']) {
                                if ($ques_csv) {
                                    $ques_csv .= ',';
                                }
                                $ques_csv .= $appEmpQuesPrivData[0]['manager_qs'];
                            }

                            // get all questions data based on above question ids
                            $questions_data = $appEmpRatingsModel->getAppQuesDataByIDs($ques_csv);

                            // Employee and Manager response
                            $emp_response = array();
                            $mgr_response = array();
                            if ($appEmpRatingsData[0]['employee_response'])
                                $emp_response = json_decode($appEmpRatingsData[0]['employee_response'], true);

                            if ($appEmpRatingsData[0]['manager_response'])
                                $mgr_response = json_decode($appEmpRatingsData[0]['manager_response'], true);

                            // get rating details using configuration id
                            $ratingsData = $appEmpRatingsModel->getAppRatingsDataByConfgId($appEmpRatingsData[0]['pa_configured_id'], $appEmpRatingsData[0]['pa_initialization_id']);
                            $ratingType = "";
                            if (!empty($ratingsData))
                                $ratingType = $ratingsData[0]['rating_type'];

                            $ratingText = array();
                            $ratingTextDisplay = array();
                            $ratingValues = array();
                            foreach ($ratingsData as $rd) {
                                $ratingText[] = $rd['rating_text'];
                                $ratingTextDisplay[$rd['id']] = $rd['rating_text'];
                                $ratingValues[$rd['id']] = $rd['rating_value'];
                            }

                            // building managers names from Emp-Ques-Privileges data
                            $managerIDs = array();
                            $managerNames = array();
                            $line_unique_arr = array_filter(array_unique(array(1 => $appEmpQuesPrivData[0]['line_manager_1'], 2 => $appEmpQuesPrivData[0]['line_manager_2'], 3 => $appEmpQuesPrivData[0]['line_manager_3'], 4 => $appEmpQuesPrivData[0]['line_manager_4'], 5 => $appEmpQuesPrivData[0]['line_manager_5'])), 'strlen');
                            if (!empty($line_unique_arr)) {
                                for ($i = 1; $i <= count($line_unique_arr); $i++) {
                                    $managerIDs[] = $line_unique_arr[$i];
                                }

                                $usersData = $appEmpRatingsModel->getUserNamesByIDs(implode($managerIDs, ','));

                                foreach ($managerIDs as $mi) {
                                    foreach ($usersData as $ud) {
                                        if ($ud['id'] == $mi)
                                        //	$managerNames[] = $ud['userfullname'];	
                                            $managerNames[$ud['id']] = $ud['userfullname'];
                                    }
                                }
                            }

                            // Skill response data
                            $skill_response = array();
                            if ($appEmpRatingsData[0]['skill_response']) {
                                $skill_response = json_decode($appEmpRatingsData[0]['skill_response'], true);
                                $skill_ids = array_keys($skill_response);
                                $skData = $appEmpRatingsModel->getSkillNamesByIDs(implode($skill_ids, ','));

                                $skillData = array();
                                foreach ($skData as $s)
                                    $skillData[$s['id']] = $s['skill_name'];

                                $this->view->skillData = $skillData;
                            }

                            // get Appraisal history details
//					$appHistoryData = $appEmpRatingsModel->getAppHistoryData($appEmpRatingsData[0]['employee_id'],$appEmpRatingsData[0]['pa_initialization_id']);

                            $this->view->skill_response = $skill_response;
//					$this->view->appHistoryData = $appHistoryData;
                            $this->view->managerNames = $managerNames;
                            $this->view->appEmpRatingsData = $appEmpRatingsData;
                            $this->view->categories_data = $categories_data;
                            $this->view->questions_data = $questions_data;
                            $this->view->ratingType = $ratingType;
                            $this->view->ratingTextDisplay = $ratingTextDisplay;
                            $this->view->ratingText = json_encode($ratingText);
                            $this->view->ratingValues = $ratingValues;
                            $this->view->emp_response = $emp_response;
                            $this->view->mgr_response = $mgr_response;
                            $this->view->check_ratings_exists = $checkRatingsExists;
                            $this->view->login_user_id = $loginUserId;
                        }
                    } else {

                        $this->view->rowexist = "norows";
                    }
                } else {
                    $this->view->rowexist = "norows";
                }
            } else {
                $this->view->rowexist = "norows";
            }
        } catch (Exception $e) {
            $this->view->rowexist = "norows";
        }
    }

}
