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

class Default_AppraisalhistoryteamController extends Zend_Controller_Action {

    private $options;

//    public $app_history_disc_array = array(1=>APP_TXT_EMP_SUBMIT, 2=>APP_TXT_L1_SUBMIT, 3=>APP_TXT_L2_SUBMIT, 4=>APP_TXT_L3_SUBMIT,
//										5=>APP_TXT_L4_SUBMIT, 6=>APP_TXT_L5_SUBMIT, 7=>APP_TXT_COMPLETED);
//										
//	public $app_status_array = array(1=>APP_PENDING_EMP, 2=>APP_PENDING_L1, 3=>APP_PENDING_L2, 4=>APP_PENDING_L3,
//										5=>APP_PENDING_L4, 6=>APP_PENDING_L5, 7=>APP_COMPLETED);
    public function preDispatch() {
        
    }

    public function init() {
        $this->_options = $this->getInvokeArg('bootstrap')->getOptions();
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('getsearchedempcontent', 'html')->initContext();
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
        $flag='historyteam';
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
            if ($id != '') {
                if (is_numeric($id) && $id > 0) {
                    $errorMsg = "";
                    $auth = Zend_Auth::getInstance();
                    if($auth->hasIdentity())
                    {
                        $loginUserId = $auth->getStorage()->read()->id;
                        $businessunit_id = $auth->getStorage()->read()->businessunit_id;
                        $department_id = $auth->getStorage()->read()->department_id; 
                        $loginuserRole = $auth->getStorage()->read()->emprole;
                        $loginuserGroup = $auth->getStorage()->read()->group_id;
                    }
                    $view = $this->view;                        
                    $model = new Default_Model_Appraisalmanager();
                    $app_rating_model = new Default_Model_Appraisalratings();
                    $ratingflag = 'false';
                    $linemangerids = '';
                    $managerprofileimgArr = array();
                    
                    $emp_data = $model->getEmpdata_managerapphistory($loginUserId,'',$id);
                    if(!empty($emp_data))
                    {
                            $checkRatingsExists = $app_rating_model->getAppraisalRatingsbyInitId($emp_data[0]['init_id']);
                            if(!empty($checkRatingsExists))
                            {
                                    $ratingflag = 'true';
                            }
                    }
                    if(!empty($emp_data))
                    {
                            foreach($emp_data as $key => $empval)
                            {
                                    for($i=1;$i<=5;$i++)
                                    {
                                            if(isset($empval['line_rating_'.$i]))
                                            {
                                                    $linemangerids.=$empval['line_manager_'.$i].',';
                                            }
                                    }
                                    if($linemangerids)
                                    {
                                                $linemangerids = rtrim($linemangerids,',');  
                                                    $managerprofileimgArr = $app_rating_model->getManagerProfileImg($linemangerids);
                                    }
                                    $emp_data[$key]= $emp_data[$key]+$managerprofileimgArr;
                                    $linemangerids = '';
                                    $managerprofileimgArr = array();

                            }
                    }
                    $view->app_init_id = $id;
                    $view->emp_data = $emp_data;
                    $view->manager_id = $loginUserId;                            
                    $view->error_msg = $errorMsg;
                    $view->ratingflag = $ratingflag;
                    $view->loginuserRole = $loginuserRole;
                    $view->loginuserGroup = $loginuserGroup;
                    
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
    
    public function getsearchedempcontentAction()
    {
        $errorMsg = "";
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $businessunit_id = $auth->getStorage()->read()->businessunit_id;
            $department_id = $auth->getStorage()->read()->department_id; 
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
        }
        $searchval = '';
        $searchstring = htmlspecialchars($this->_request->getParam('searchstring'));
        $app_init_id = sapp_Global::_decrypt(($this->_request->getParam('app_init_id')));
       // $searchstring = $this->_request->getParam('searchstring');
        $view = $this->view;                        
        $model = new Default_Model_Appraisalmanager();
        $app_rating_model = new Default_Model_Appraisalratings();
        $ratingflag = 'false';
        $linemangerids = '';
        $managerprofileimgArr = array();
		
        if($searchstring!='')
        {
        	$searchval = " and es.userfullname like '%$searchstring%'";
        }
        //$emp_data = $model->getSearchEmpdata_managerapp($loginUserId,$searchval);
        if(!empty($app_init_id)) {
            $emp_data = $model->getEmpdata_managerapphistory($loginUserId,$searchval,$app_init_id);
        }
    	if(!empty($emp_data))
        {
        	foreach($emp_data as $key => $empval)
        	{
        		for($i=1;$i<=5;$i++)
        		{
        			if(isset($empval['line_rating_'.$i]))
        			{
        				$linemangerids.=$empval['line_manager_'.$i].',';
        			}
        		}
        		if($linemangerids)
        		{
					$linemangerids = rtrim($linemangerids,',');  
					$managerprofileimgArr = $app_rating_model->getManagerProfileImg($linemangerids);
        		}
        		$emp_data[$key]= $emp_data[$key]+$managerprofileimgArr;
        		$linemangerids = '';
        		$managerprofileimgArr = array();
        		
        	}
        }
        
        $view->emp_data = $emp_data;
        $view->manager_id = $loginUserId;                            
        $view->error_msg = $errorMsg;
    }
}