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

class Default_DisciplinarymyincidentsController extends Zend_Controller_Action {

    private $options;

    public function preDispatch() {
       	$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('saveemployeeappeal', 'json')->initContext(); 
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
        $flag='myincident';
        $dataTmp = $disciplinaryIncidentModel->getGrid($sort, $by, $perPage, $pageNo, $searchData, $call, $dashboardcall,$flag);

        array_push($data, $dataTmp);
        $this->view->dataArray = $data;
        $this->view->call = $call;
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        
    }

    public function viewAction() {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $loginUserId = $auth->getStorage()->read()->id;
        }
		$objName = 'disciplinarymyincidents';
        $id = $this->getRequest()->getParam('id');
        try {
            $disciplinaryIncidentModel = new Default_Model_Disciplinaryincident();
            if ($id != '') {
                if (is_numeric($id) && $id > 0) {
                    $data = $disciplinaryIncidentModel->getIncidentData($id);
                   if(!empty($data) && $data[0]['employee_id']==$loginUserId) {
                    	$this->view->data = $data[0];
                    	$this->view->controllername = 'disciplinarymyincidents';
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
    }
    
public function editAction()
	{	
	    $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->getRequest()->getParam('id');
		$disciplinaryIncidentModel = new Default_Model_Disciplinaryincident();
		$errormsg = 'norecord';
		try
		{
			if($id)
			{
			    if(is_numeric($id) && $id>0)
				{
					$data = $disciplinaryIncidentModel->getIncidentData($id);
					if(!empty($data))
					{
					  $data = $data[0];	
					  if($data['employee_id']==$loginUserId && $data['incident_status']=='Initiated' && $data['dateexpired']=='notexpired')
					  {	
						  $this->view->data = $data;
						  if(!defined('sentrifugo_gilbert')) {
	                    	$disciplineHistoryModel = new Default_Model_Disciplineincidenthistory();
	                    	$incident_history = $disciplineHistoryModel->getDisciplineIncidentHistory($id);
						  	$this->view->incident_history = $incident_history;
	                      }
						  $errormsg = '';
					  }
					}
				}				
			}
			$this->view->ermsg = $errormsg;
		}
		catch(Exception $e)
		{
			   $this->view->ermsg = $errormsg;
		}
			
	}
	
public function saveemployeeappealAction()
	{
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->_getParam('id',null);
		$employee_appeal = $this->_getParam('employee_appeal',null);
		$employee_statement = $this->_getParam('employee_statement',null);
		$result = 'error';
		$msg = 'Something went wrong.';
		if(empty($id) || empty($employee_statement) || empty($employee_appeal)) {
			$this->_helper->json(array('result' =>$result,'msg'=>$msg));
		}
		$id= sapp_Global::_decrypt($id);
		$disciplinaryIncidentModel = new Default_Model_Disciplinaryincident();
			$db = Zend_Db_Table::getDefaultAdapter();	
			$db->beginTransaction();
			try
			{
				if(is_numeric($id) && $id>0) {
					$data = array(
						'employee_appeal' => sapp_Global::_decrypt($employee_appeal),
						'employee_statement' => trim($employee_statement),
						'incident_status' => 'Appealed',
						'modifiedby' => $loginUserId,
						'modifieddate'=>gmdate("Y-m-d H:i:s"),
					);
                    $where = array('id=?'=>$id,'employee_id=?'=>$loginUserId);
                    $Id = $disciplinaryIncidentModel->SaveorUpdateIncidentData($data, $where);
                    
                    // Mail code
                    $incidentData = $disciplinaryIncidentModel->getIncidentData($id);
					$mail_arr[0]['name'] = 'HR';
					$mail_arr[0]['email'] = defined('LV_HR_'.$incidentData[0]['employee_bu_id'])?constant('LV_HR_'.$incidentData[0]['employee_bu_id']):"";
					$mail_arr[0]['type'] = 'HR';
					$mail_arr[1]['name'] = $incidentData[0]['incidentraisedby'];
					$mail_arr[1]['email'] = $incidentData[0]['managementemail'];
					$mail_arr[1]['type'] = 'Management';
					$mail_arr[2]['name'] = $incidentData[0]['employeename'];
					$mail_arr[2]['email'] = $incidentData[0]['employeeemail'];
					$mail_arr[2]['type'] = 'Employee';
					$mail_arr[3]['name'] = $incidentData[0]['reportingmanagername'];
					$mail_arr[3]['email'] = $incidentData[0]['reportingmanageremail'];
					$mail_arr[3]['type'] = 'Manager';
					for($ii =0;$ii<count($mail_arr);$ii++)
					{
						$base_url = 'http://'.$this->getRequest()->getHttpHost() . $this->getRequest()->getBaseUrl();
						$view = $this->getHelper('ViewRenderer')->view;
						$this->view->emp_name = $mail_arr[$ii]['name'];
						$this->view->base_url=$base_url;
						$this->view->type = $mail_arr[$ii]['type'];
						$this->view->raised_name = $incidentData[0]['incidentraisedby'];
						$this->view->raised_against = $incidentData[0]['employeename'];
						$this->view->raised_against_employee_id = $incidentData[0]['employeeId'];
						$this->view->raised_by = 'Employee';
						$text = $view->render('mailtemplates/disciplineincident.phtml');
						$options['subject'] = APPLICATION_NAME.': New Disciplinary Incident Raised';
						$options['header'] = 'Disciplinary Incident';
						$options['toEmail'] = $mail_arr[$ii]['email'];
						$options['toName'] = $mail_arr[$ii]['name'];
						$options['message'] = $text;
	
						$options['cron'] = 'yes';
						if($options['toEmail'] != '')
						sapp_Global::_sendEmail($options);
					}
					// History Code
					if(!defined('sentrifugo_gilbert')) {
	                	$disciplineHistoryModel = new Default_Model_Disciplineincidenthistory();
	                	
						$incident_data = array(
						 'createdby' =>$loginUserId,
						 'createddate' =>gmdate("Y-m-d H:i:s"),
						 'incident_id'=>$id,
						 'action_emp_id'=>$loginUserId,
						 'description' => "Appeal statement has been provided by "
						  );
						$disciplineHistoryModel->saveOrUpdateDisciplineIncidentHistory($incident_data,'');
	                }
	                $db->commit();
                    $result = 'success';
                    $msg  = 'Employee appeal updated succesfully';
                }
			
			}
			catch(Exception $e)
			{			
				$db->rollBack();								
				$msg = $e->getMessage();
			}
			$this->_helper->json(array('result' =>$result,'msg'=>$msg));
		
	}

    public function getdisciplinaryincidentpdfAction()
    {
        $this->_helper->layout->disableLayout();
        $id = $this->_request->getParam('a');
        $auth = Zend_Auth::getInstance();
        $logged_in_user_id = 0;
        //getting logged in user details
        if($auth->hasIdentity())
        {
            $logged_in_user_id = $auth->getStorage()->read()->id;
        }
        try
        {
            $disciplinaryIncidentModel = new Default_Model_Disciplinaryincident();
            if ($id != '')
            {
                if (is_numeric($id) && $id > 0)
                {
                    $data = $disciplinaryIncidentModel->getIncidentData($id);
                    // if(!empty($data) && $data[0]['employee_id']==$logged_in_user_id)
                    // {
                        $this->view->data = $data[0];
                        $this->view->controllername = 'disciplinarymyincidents';
                    /*}   
                    else
                    {
                        $this->view->rowexist = "norows";       
                    }*/
                }
                else 
                {
                    $this->view->rowexist = "norows";
                }
            }
            else 
            {
                $this->view->rowexist = "norows";
            }
        } 
        catch (Exception $e) 
        {
            $this->view->rowexist = "norows";
        }
        $view = $this->getHelper('ViewRenderer')->view;
        $text = $view->render('disciplinaryincident/disciplinaryincidentpdf.phtml');
        //generating file name
        $file_name_params_array = array('Disciplinary_Incident');
        $file_name = $this->_helper->PdfHelper->generateFileName($file_name_params_array);
        //mpdf
        require_once 'MPDF57/mpdf.php';
        $mpdf=new mPDF('', 'A4', 14, '', 10, 10, 12, 12, 6, 6);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->list_indent_first_level = 0;
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->pagenumPrefix = 'Generated using Sentrifugo'.str_repeat(" ",72);
        $mpdf->pagenumSuffix = '';
        $mpdf->nbpgPrefix = ' of ';
        $mpdf->nbpgSuffix = '';
        $mpdf->SetFooter('{PAGENO}{nbpg}');
        $mpdf->AddPage();
        $mpdf->WriteHTML($text);
        $mpdf->Output((!empty($file_name)?$file_name:'appraisal').'.pdf','D');
        exit;   
    }
}
