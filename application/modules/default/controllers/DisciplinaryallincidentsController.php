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

class Default_DisciplinaryallincidentsController extends Zend_Controller_Action {

    private $options;

    public function preDispatch() {
        
    }

    public function init() {
        $this->_options = $this->getInvokeArg('bootstrap')->getOptions();
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('save', 'json')->initContext();
    }

    public function indexAction() {
        $disciplinaryIncidentModel = new Default_Model_Disciplinaryincident();
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
            $by = 'i.modifieddate';
            $pageNo = 1;
            $searchData = '';
            $searchQuery = '';
            $searchArray = '';
        }
        else {
            $sort = ($this->_getParam('sort') != '') ? $this->_getParam('sort') : 'DESC';
            $by = ($this->_getParam('by') != '') ? $this->_getParam('by') : 'i.modifieddate';
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
        $flag='allincident';
        $dataTmp = $disciplinaryIncidentModel->getGrid($sort, $by, $perPage, $pageNo, $searchData, $call, $dashboardcall,$flag);

        array_push($data, $dataTmp);
        $this->view->dataArray = $data;
        $this->view->call = $call;
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        $this->render('disciplinarymyincidents/index', null, true);
    }

    public function viewAction() {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $loginUserId = $auth->getStorage()->read()->id;
            $loginUserGroup = $auth->getStorage()->read()->group_id;
            $loginUserRole = $auth->getStorage()->read()->emprole;
        }
		$objName = 'disciplinaryallincidents';
        $id = $this->getRequest()->getParam('id');
        try {
            $disciplinaryIncidentModel = new Default_Model_Disciplinaryincident();
            if ($id != '') {
                if (is_numeric($id) && $id > 0) {
                    $data = $disciplinaryIncidentModel->getIncidentData($id);
                    if(!empty($data) && ($loginUserGroup==MANAGEMENT_GROUP || $loginUserGroup==HR_GROUP || $loginUserRole==SUPERADMINROLE)) {
                    	$this->view->data = $data[0];
                    	$this->view->controllername = 'disciplinaryallincidents';
                    	$this->view->loginUserId = $loginUserId;
                    	if(!defined('sentrifugo_gilbert')) {
	                    	$disciplineHistoryModel = new Default_Model_Disciplineincidenthistory();
	                    	$incident_history = $disciplineHistoryModel->getDisciplineIncidentHistory($id);
						  	$this->view->incident_history = $incident_history;
	                    }
                    }	
                    else
                    	$this->view->rowexist = "norows";	
                } else {
                    $this->view->rowexist = "norows";
                }
            } else {
                $this->view->rowexist = "norows";
            }
        } catch (Exception $e) {
            $this->view->rowexist = "norows";
        }
		$this->view->controllername = $objName;
        $this->render('disciplinarymyincidents/view', null, true);
    }

}
