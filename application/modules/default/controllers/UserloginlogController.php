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

class Default_UserloginlogController extends Zend_Controller_Action
{

	private $options;
	private $userlog_model;

	/**
	 * Init
	 *
	 * @see Zend_Controller_Action::init()
	 */
	public function init()
	{
		$this->_options= $this->getInvokeArg('bootstrap')->getOptions();
		$this->userlog_model = new Default_Model_Userloginlog();
	}

	public function preDispatch()
	{
		$auth = Zend_Auth::getInstance();
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
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('userlogreport', 'html')->initContext();

	}

	/**
	 * @name indexAction
	 *
	 * This method is used to display the user log info
	 *
	 *  @author Deepthi
	 *  @version 1.0
	 */

	public function indexAction()
	{

		$call = $this->_getParam('call');
		if($call == 'ajaxcall')
		$this->_helper->layout->disableLayout();

		$objname = $this->_getParam('objname');
		$refresh = $this->_getParam('refresh');
		$dashboardcall = $this->_getParam('dashboardcall');
		$data = array();
		$searchQuery = '';
		$searchArray = array();
			
		if($refresh == 'refresh')
		{
			if($dashboardcall == 'Yes')
			$perPage = DASHBOARD_PERPAGE;
			else
			$perPage = PERPAGE;
			$sort = 'DESC';$by = 'r.logindatetime';$pageNo = 1;$searchData = '';$searchQuery = '';
			$searchArray = array();
		}
		else
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'r.logindatetime';
			if($dashboardcall == 'Yes')
			$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
			else
			$perPage = $this->_getParam('per_page',PERPAGE);
			$pageNo = $this->_getParam('page', 1);
			$searchData = $this->_getParam('searchData');
		}

		$dataTmp = $this->userlog_model->getUserLoginLogGrid($sort, $by, $perPage, $pageNo, $searchData,$call,'','','','','');
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
	}


	/**
	 * @name empnameautoAction
	 *
	 * This method is used to send json output of employee names for autocomplete field in userlog report
	 *
	 *  @author Deepthi
	 *  @version 1.0
	 */
	public function empnameautoAction(){
		$term = $this->_getParam('term',null);
		$output = array(array('id' => '','value' => 'No records','label' => 'No records'));
			
		if($term != '')
		{
			$emp_arr = $this->userlog_model->getAutoReportEmpname($term);

			if(count($emp_arr)>0)
			{
				$output = array();
				foreach($emp_arr as $emp)
				{
					$output[] = array('id' => $emp['emp_name'],'value' => $emp['emp_name'],'label' => $emp['emp_name'],'profile_img' => $emp['profileimg']);
				}
			}
		}
		$this->_helper->json($output);
	}

	/**
	 * @name empidautoAction
	 *
	 * This method is used to send json output of employee ID's for autocomplete field in userlog report
	 *
	 *  @author Deepthi
	 *  @version 1.0
	 */
	public function empidautoAction(){
		$term = $this->_getParam('term',null);
		$output = array(array('id' => '','value' => 'No records','label' => 'No records'));
			
		if($term != '')
		{
			$emp_arr = $this->userlog_model->getAutoReportEmpID($term);

			if(count($emp_arr)>0)
			{
				$output = array();
				foreach($emp_arr as $emp)
				{
					$output[] = array('id' => $emp['emp_id'],'value' => $emp['emp_id'],'label' => $emp['emp_id']);
				}
			}
		}
		$this->_helper->json($output);
	}

	/**
	 * @name empipaddressautoAction
	 *
	 * This method is used to send json output of employee IPaddress for autocomplete field in userlog report
	 *
	 *  @author Deepthi
	 *  @version 1.0
	 */
	public function empipaddressautoAction(){
		$term = $this->_getParam('term',null);
		$output = array(array('id' => '','value' => 'No records','label' => 'No records'));
			
		if($term != '')
		{
			$emp_arr = $this->userlog_model->getAutoReportEmpIpaddress($term);

			if(count($emp_arr)>0)
			{
				$output = array();
				foreach($emp_arr as $emp)
				{
					$output[] = array('id' => $emp['emp_ipaddress'],'value' => $emp['emp_ipaddress'],'label' => $emp['emp_ipaddress']);
				}
			}
		}
		$this->_helper->json($output);
	}

	/**
	 * @name empemailautoAction
	 *
	 * This method is used to send json output of employee Email for autocomplete field in userlog report
	 *
	 *  @author Deepthi
	 *  @version 1.0
	 */
	public function empemailautoAction(){
		$term = $this->_getParam('term',null);
		$output = array(array('id' => '','value' => 'No records','label' => 'No records'));
			
		if($term != '')
		{
			$emp_arr = $this->userlog_model->getAutoReportEmpEmail($term);

			if(count($emp_arr)>0)
			{
				$output = array();
				foreach($emp_arr as $emp)
				{
					$output[] = array('id' => $emp['emp_email'],'value' => $emp['emp_email'],'label' => $emp['emp_email']);
				}
			}
		}
		$this->_helper->json($output);
	}


}