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

class Default_LogmanagerController extends Zend_Controller_Action
{

	private $options;
	private $activitylog_model;
	private $logJsonCount;
	private $modArray = array();
	private $userNameArray = array();


	/**
	 * Init
	 *
	 * @see Zend_Controller_Action::init()
	 */
	public function init()
	{
		$this->_options= $this->getInvokeArg('bootstrap')->getOptions();
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
		$ajaxContext->addActionContext('activitylogreport', 'html')->initContext();
	}

	/**
	 * @name indexAction
	 *
	 * This method is used to display the activity log info
	 *
	 *  @author Deepthi
	 *  @version 1.0
	 */

	public function indexAction()
	{
		$activitylog_model = new Default_Model_Activitylog();

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
			$sort = 'DESC';$by = 'r.last_modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';
			$searchArray = array();
		}
		else
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'r.last_modifieddate';
			if($dashboardcall == 'Yes')
			$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
			else
			$perPage = $this->_getParam('per_page',PERPAGE);
			$pageNo = $this->_getParam('page', 1);
			$searchData = $this->_getParam('searchData');
		}

		$dataTmp = $activitylog_model->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,'','','','','');
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
	}

	/**
	 * @name viewAction
	 *
	 * This method is used to display the detail log info
	 *
	 *  @author Deepthi
	 *  @version 1.0
	 */

	public function viewAction()
	{
		$activitylog_model = new Default_Model_Activitylog();
		$logmanager_model = new Default_Model_Logmanager();
		$menu_model = new Default_Model_Menu();

		//Pop up layout
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");

		$perPage = PERPAGE;
        $this->view->managemodule = 0; 
		if($this->getRequest()){
			$id = $this->_request->getParam('id');
			$viewLinkArray = array('101','102');// menu with view links

			$logManagarRecord = $logmanager_model->getLogManagerDataByID($id);

			$menuId = $logManagarRecord[0]['menuId'];
			$action = $logManagarRecord[0]['user_action'];
			switch($action){
				case 1:$userAction = 'add'; break;
				case 2:$userAction = 'edit';break;
				default:$userAction = ''; break;
			}

			$url = $menu_model->getMenusDataById($menuId);
			$modurl = ltrim($url[0]['url'],'/');

			if(isset($logManagarRecord[0]['log_details']) && !empty($logManagarRecord[0]['log_details'])){

				$logdetails = '{"testjson":['.$logManagarRecord[0]['log_details'].']}';
				$logarr = @get_object_vars(json_decode($logdetails));

				if(!empty($logarr))
				{
					$userArray = array();
					$logarr['testjson'] = array_reverse($logarr['testjson']);
					$this->logJsonCount = count($logarr['testjson']);
					foreach($logarr['testjson'] as $key =>$curr)
					{
						$currArray = @get_object_vars($curr);
							
						$this->modArray[$key]['userid'] = $currArray['userid'];
						 if($menuId != '142'){
						   if(in_array($menuId,$viewLinkArray)){
								$this->modArray[$key]['recordid'] = isset($currArray['recordid'])?BASE_URL.$modurl.'/view/id/'.$currArray['recordid']:'0';
							}else{
								$this->modArray[$key]['recordid'] = isset($currArray['recordid'])?BASE_URL.$modurl.'/edit/id/'.$currArray['recordid']:'0';
							}
					    }else{
					       $this->view->managemodule = 1; 
					       $this->modArray[$key]['recordid'] = $currArray['childrecordid'];
					    }
						
						$this->modArray[$key]['date'] = $currArray['date'];

						if (!in_array($currArray['userid'], $userArray)) {
							array_push($userArray,$currArray['userid']);
						}
					}
					$this->userNameArray = $activitylog_model->getuserNamesByIds($userArray);
						
					if($menuId != '61' && $menuId != '65'){
						$this->view->viewrRecordFlag = true;
					}else{
						$this->view->viewrRecordFlag = false;
					}
					$this->view->mainArray = $this->modArray;
					$this->view->userNameArray = $this->userNameArray;
					$this->view->totalJsonCount = $this->logJsonCount;
					$this->view->pageNo = 1;
					$this->view->perPage = $perPage;
				}else{
					$this->view->message ="No records found";
					exit;
				}

			}
		}
	}


	/**
	 * @name empnamewithidautoAction
	 *
	 * This method is used to send json output of employee names with ID for autocomplete field in activity log report
	 *
	 *  @author Deepthi
	 *  @version 1.0
	 */
	public function empnamewithidautoAction(){
		$logmanager_model = new Default_Model_Logmanager();
		$term = $this->_getParam('term',null);
		$output = array(array('id' => '','value' => 'No records','label' => 'No records'));

		if($term != '')
		{
			$emp_arr = $logmanager_model->getAutoReportEmpnameWithId($term);

			if(count($emp_arr)>0)
			{
				$output = array();
				foreach($emp_arr as $emp)
				{
					$output[] = array('id' => $emp['id'],'value' => $emp['emp_name'],'label' => $emp['emp_name'],'profile_img' => $emp['profileimg']);
				}
			}
		}
		$this->_helper->json($output);
	}

}