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

class Default_ApprreqcandidatesController extends Zend_Controller_Action
{

    private $_options;
    public function preDispatch()
    {	
        $session = sapp_Global::_readSession();
        if(!isset($session))
        {
            if($this->getRequest()->isXmlHttpRequest())
            {
                echo Zend_Json::encode( array('login' => 'failed') );
                die();	
            }
            else
            {
                $this->_redirect('');
            }
        }       
    }
	
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();		
    }
	
	public function indexAction()
	{
		
        $call = $this->_getParam('call');
        if($call == 'ajaxcall')
            $this->_helper->layout->disableLayout();

		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
		} 
		$id = $this->_request->getParam('unitId',null);
        $refresh = $this->_getParam('refresh');
        $data = array();
        $searchQuery = '';
        $searchArray = array();
        $tablecontent='';

        if($refresh == 'refresh')
        {
            $sort = 'DESC';$by = 'c.createddate';$perPage = 10;$pageNo = 1;$searchData = '';$searchQuery = '';$searchQuery = '';$searchArray='';
            $searchArray = array();
        }
        else 
        {
            $sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
            $by = ($this->_getParam('by')!='')? $this->_getParam('by'):'c.createddate';
            $perPage = $this->_getParam('per_page',10);
            $pageNo = $this->_getParam('page', 1);
            /** search from grid - START **/
            $searchData = $this->_getParam('searchData');	
            if($searchData != '' && $searchData!='undefined')
            {
                $searchValues = json_decode($searchData);
                if(count($searchValues) >0)
                {
                    foreach($searchValues as $key => $val)
                    {
                        $searchQuery .= " ".$key." like '%".$val."%' AND ";
                        $searchArray[$key] = $val;
                    }
                    $searchQuery = rtrim($searchQuery," AND");					
                }
            }
            /** search from grid - END **/
        }

        $objName = 'apprreqcandidates';

                        $tableFields = array('action'=>'Action',
                                             'candidate_name' => 'Candidate Name',
                                             'cand_status' => 'Candidate Status',
                                            );
                        $candidate_model = new Default_Model_Candidatedetails();
                        $tablecontent = $candidate_model->getCandidatesData_requisition($sort, $by, $pageNo, $perPage,$searchQuery,$id);     
                        $cand_status_opt = array('' => 'All','Shortlisted' => 'Shortlisted','Selected' => 'Selected','Rejected' => 'Rejected',
                                                'On hold' => 'On hold','Disqualified' => 'Disqualified','Scheduled' => 'Scheduled',
                                                'Not Scheduled' => 'Not Scheduled','Recruited' => 'Recruited','Requisition Closed/Completed' => 'Requisition Closed/Completed');
                        
                        $dataTmp = array(
                                'sort' => $sort,
                                'by' => $by,
                                'pageNo' => $pageNo,
                                'perPage' => $perPage,				
                                'tablecontent' => $tablecontent,
                                'objectname' => $objName,
                                'extra' => array(),
                                'tableheader' => $tableFields,
                                'jsGridFnName' => 'getAjaxgridData',
                                'jsFillFnName' => '',
                                'formgrid' => 'true',
                                'searchArray' => $searchArray,
                                'menuName' => 'Candidate details',								
                                'unitId'=>$id,
                                'search_filters' => array(
                                    'cand_status' => array(
                                        'type' => 'select',
                                        'filter_data' => $cand_status_opt,
                                    ),
                                ),
                        );			
                        array_push($data,$dataTmp);
                        $this->view->dataArray = $dataTmp;
                $this->view->call = $call ;		
		$this->view->messages = $this->_helper->flashMessenger->getMessages();	
	}
        
    public function viewpopupAction()
    {
        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
        $id = $this->_getParam('id',null);
        $candidate_model = new Default_Model_Candidatedetails();
        $interview_model = new Default_Model_Interviewdetails();
        $interview_round_model = new Default_Model_Interviewrounddetails();
        
        $candidate_data = $candidate_model->getCandidateById($id);
        $interview_data = $interview_model->getInterviewDataByCandidateId($id);
        $interview_round_data = $interview_round_model->getRoundDetailsByCandidateId($id);
        
        $this->view->candidate_data = $candidate_data;
        $this->view->interview_data = $interview_data;
        $this->view->interview_round_data = $interview_round_data;
    }
}
