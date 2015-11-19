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

class Default_ActivitylogController extends Zend_Controller_Action
{

	private $options;
	private $activitylog_model;
	private $perPage;

	/**
	 * Init
	 *
	 * @see Zend_Controller_Action::init()
	 */
	public function init()
	{
		$this->_options= $this->getInvokeArg('bootstrap')->getOptions();
		$this->activitylog_model = new Default_Model_Activitylog();
		$this->perPage = PERPAGE;
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
		$tablecontent = $this->activitylog_model->getLogManagerData();
		$logManagerCount = $this->activitylog_model->getLogManagerCount();
		$menuArray = array();
		$userArray = array();
		$logArray = array();
		$pageNo = 1;
		
		if(!empty($tablecontent))
		{
			foreach($tablecontent as $key => $curr)
			{
				if (!in_array($curr['menuId'], $menuArray)) {
					array_push($menuArray,$curr['menuId']);
				}
				$logArray[$key]['menuId'] = $curr['menuId'];
				$logArray[$key]['userAction'] = $curr['user_action'];

				$logdetails = '{"testjson":['.$curr['log_details'].']}';
				$logarr = @get_object_vars(json_decode($logdetails));
				$lastModRecord = @get_object_vars(array_pop($logarr['testjson']));

				$logArray[$key]['userId'] = isset($lastModRecord['userid'])? $lastModRecord['userid'] : '1';
				if (!in_array($logArray[$key]['userId'], $userArray)) {
					array_push($userArray,$logArray[$key]['userId']);
				}

				$logArray[$key]['recordId'] = isset($lastModRecord['recordid'])? $lastModRecord['recordid'] : '';
				$logArray[$key]['date'] = isset($lastModRecord['date'])? $lastModRecord['date'] : '';
			}
		}

		$menuNameArray = $this->activitylog_model->getMenuNamesByIds($menuArray);
		$userNameArray = $this->activitylog_model->getuserNamesByIds($userArray);
		$userActionArray = array('1'=> 'Add','2' => 'Edit','3' => 'Delete');

		$this->view->activityLogArray = $logArray;
		$this->view->menuNameArray = $menuNameArray ;
		$this->view->userNameArray = $userNameArray ;
		$this->view->userActionArray = $userActionArray ;
		$this->view->pageNo = $pageNo;
		$this->view->perpage = $this->perPage;
		$this->view->totalCount = $logManagerCount;
	}

	public function activitylogajaxAction(){
		if($this->getRequest()->getPost()){
			$pageNo = $this->_request->getParam('pageNo');
			$order = $this->_request->getParam('order');
	        $sortfield = $this->_request->getParam('sortby');
			
			$tablecontent = $this->activitylog_model->getLogManagerDataSort($pageNo,$this->perPage,$sortfield,$order);

			$menuArray = array();
			$userArray = array();
			$logArray = array();

			if(!empty($tablecontent))
			{
				foreach($tablecontent as $key => $curr)
				{
					if (!in_array($curr['menuId'], $menuArray)) {
						array_push($menuArray,$curr['menuId']);
					}
					$logArray[$key]['menuId'] = $curr['menuId'];
					$logArray[$key]['userAction'] = $curr['user_action'];

					$logdetails = '{"testjson":['.$curr['log_details'].']}';
					$logarr = @get_object_vars(json_decode($logdetails));
					$lastModRecord = @get_object_vars(array_pop($logarr['testjson']));

					$logArray[$key]['userId'] = isset($lastModRecord['userid'])? $lastModRecord['userid'] : '1';
					if (!in_array($logArray[$key]['userId'], $userArray)) {
						array_push($userArray,$logArray[$key]['userId']);
					}

					$logArray[$key]['recordId'] = isset($lastModRecord['recordid'])? $lastModRecord['recordid'] : '';
					$logArray[$key]['date'] = isset($lastModRecord['date'])? $lastModRecord['date'] : '';
				}

				$menuNameArray = $this->activitylog_model->getMenuNamesByIds($menuArray);
				$userNameArray = $this->activitylog_model->getuserNamesByIds($userArray);
				$userActionArray = array('1'=> 'Add','2' => 'Edit','3' => 'Delete');

				$html = '';
				foreach($logArray as $log){

					$recordUrl = '';
					$userName = isset($userNameArray[$log['userId']])?$userNameArray[$log['userId']]:"";
					$userAction = isset($userActionArray[$log['userAction']])?$userActionArray[$log['userAction']]:'';
					$menuName = isset($menuNameArray[$log['menuId']])?$menuNameArray[$log['menuId']]['name']:'';
					if(isset($menuNameArray[$log['menuId']]) && $log['userAction'] != 3){
						$path = ltrim($menuNameArray[$log['menuId']]['url'],'/');
						$recordUrl = BASE_URL.$path.'/edit/id/'.$log['recordId'];
						$urlDiv = '<td class="cellDiv"><a href="'.$recordUrl.'">View Record</a></td>';
					}else{
						$urlDiv = '<td class="cellDiv"></td>';
					}

					$html.= '<tr class="rowDiv"><td class="cellDiv">'.$userAction.'</td><td class="cellDiv">'.$userName.'</td><td class="cellDiv">'.$menuName.'</td>'.$urlDiv.'<td class="cellDivHeader lastCell">'.sapp_Global::change_date($log['date'], 'view').' at '.sapp_Global::change_time($log['date'], 'view').'</td></tr>';					
				
   				}
				echo $html; exit;
			}else{
				echo 'No more logs to display'; exit;
			}
		}
	}

	public function userlogAction()
	{
		$userlog = $this->activitylog_model->getUserLogManagerData();
		$userlogCount = $this->activitylog_model->getUserLogCount();

		$userArray = array();
		$empRoleArray = array();
		$groupArray = array();
		$pageNo = 1;

		if(!empty($userlog))
		{
			foreach($userlog as $key => $curr)
			{
				if (!in_array($curr['userid'], $userArray)) {
					array_push($userArray,$curr['userid']);
				}
					
				if (!in_array($curr['emprole'], $empRoleArray)) {
					array_push($empRoleArray,$curr['emprole']);
				}

				if (!in_array($curr['group_id'], $groupArray)) {
					array_push($groupArray,$curr['group_id']);
				}

			}

			$userNameArray = $this->activitylog_model->getuserNamesByIds($userArray);
			$roleNameArray = $this->activitylog_model->getEmpRoleNamesByIds($empRoleArray);
			$groupNameArray = $this->activitylog_model->getGroupNamesByIds($groupArray);

			$this->view->userlog = $userlog;
			$this->view->userNameArray = $userNameArray;
			$this->view->roleNameArray = $roleNameArray;
			$this->view->groupNameArray = $groupNameArray;
			$this->view->pageNo = $pageNo;
			$this->view->perpage = $this->perPage;
			$this->view->totalCount = $userlogCount;

		}
	}

	public function userlogajaxAction(){

	 if($this->getRequest()->getPost()){
	  $pageNo = $this->_request->getParam('pageNo');

	  $order = $this->_request->getParam('order');
	  $sortfield = $this->_request->getParam('sortby');

	  $userlog = $this->activitylog_model->getUserLogManagerSort($pageNo,$this->perPage,$sortfield,$order);


	  $userArray = array();
	  $empRoleArray = array();
	  $groupArray = array();

	  if(!empty($userlog))
	  {
	  	foreach($userlog as $key => $curr)
	  	{
	  		if (!in_array($curr['userid'], $userArray)) {
	  			array_push($userArray,$curr['userid']);
	  		}

	  		if (!in_array($curr['emprole'], $empRoleArray)) {
	  			array_push($empRoleArray,$curr['emprole']);
	  		}

	  		if (!in_array($curr['group_id'], $groupArray)) {
	  			array_push($groupArray,$curr['group_id']);
	  		}

	  	}

	  	$userNameArray = $this->activitylog_model->getuserNamesByIds($userArray);
	  	$roleNameArray = $this->activitylog_model->getEmpRoleNamesByIds($empRoleArray);
	  	$groupNameArray = $this->activitylog_model->getGroupNamesByIds($groupArray);

	  	$html = '';
	  	foreach($userlog as $user){

	  		$userName = isset($userNameArray[$user['userid']])?$userNameArray[$user['userid']]:"";
	  		$groupName = isset($groupNameArray[$user['group_id']])?$groupNameArray[$user['group_id']]:"";
	  		$roleName = isset($roleNameArray[$user['emprole']])?$roleNameArray[$user['emprole']]:"";

	  		$imageUrl = '';
	  		$imagetag = '';
	  		if($user['profileimg']){
	  			$imageUrl = DOMAIN.'public/uploads/preview/'.$user['profileimg'];
	  			$imagetag = '<img src="'.$imageUrl.'" height="30" width="30">';
	  		}

	  		$html.= '<div class="rowDiv"><div class="cellDiv">'.$imagetag.$user['userfullname'].' ('.$user['employeeId'].')</div><div class="cellDiv">'.$groupName.'</div><div class="cellDiv">'. $roleName.'</div><div class="cellDiv">'.$user['emailaddress'].'</div><div class="cellDiv">'. sapp_Global::change_date($user['logindatetime'], 'view').' at '.sapp_Global::change_time($user['logindatetime'], 'view').'</div><div class="cellDiv  lastCell">'.$user['empipaddress'].'</div>';
	  		
	  	}
	  	echo $html; exit;
	  	 
	  }else{
	  	echo 'No more logs to display'; exit;
	  }

	 }
	}


	public function userlogsearchajaxAction(){
		if($this->getRequest()->getPost()){
			$field = $this->_request->getParam('byField');
			$searchString = $this->_request->getParam('searchstring');
				
			switch($field){
				case 'userfullname': $where = "cc.userfullname like '%".$searchString."%'";
				break;
				case 'groupname': $groupIds = $this->activitylog_model->getgroupIdByString($searchString);	
				$groupIdCsv = @implode(",",$groupIds);				
				$where = "cc.group_id IN (".$groupIdCsv.")"; 
				break;
				case 'role': $empRoleIds = $this->activitylog_model->getemproleIdByString($searchString);	
				$empRoleIdCsv = @implode(",",$empRoleIds);				
				$where = "cc.emprole IN (".$empRoleIdCsv.")"; 
				break;
				case 'emailaddress': $where = "cc.emailaddress like '%".$searchString."%'";
				break;
				case 'logindatetime':$where = "cc.logindatetime like '%".$searchString."%'";
				break;
				case 'empipaddress':$where = "cc.empipaddress like '%".$searchString."%'";
				break;
			}
			$userlog = $this->activitylog_model->getUserLogManagerDataSearch(1,$this->perPage,$where);
			

	  $userArray = array();
	  $empRoleArray = array();
	  $groupArray = array();

	  if(!empty($userlog))
	  {
	  	foreach($userlog as $key => $curr)
	  	{
	  		if (!in_array($curr['userid'], $userArray)) {
	  			array_push($userArray,$curr['userid']);
	  		}

	  		if (!in_array($curr['emprole'], $empRoleArray)) {
	  			array_push($empRoleArray,$curr['emprole']);
	  		}

	  		if (!in_array($curr['group_id'], $groupArray)) {
	  			array_push($groupArray,$curr['group_id']);
	  		}

	  	}

	  	$userNameArray = $this->activitylog_model->getuserNamesByIds($userArray);
	  	$roleNameArray = $this->activitylog_model->getEmpRoleNamesByIds($empRoleArray);
	  	$groupNameArray = $this->activitylog_model->getGroupNamesByIds($groupArray);

	  	$html = '';
	  	foreach($userlog as $user){

	  		$userName = isset($userNameArray[$user['userid']])?$userNameArray[$user['userid']]:"";
	  		$groupName = isset($groupNameArray[$user['group_id']])?$groupNameArray[$user['group_id']]:"";
	  		$roleName = isset($roleNameArray[$user['emprole']])?$roleNameArray[$user['emprole']]:"";

	  		$imageUrl = '';
	  		$imagetag = '';
	  		if($user['profileimg']){
	  			$imageUrl = DOMAIN.'public/uploads/preview/'.$user['profileimg'];
	  			$imagetag = '<img src="'.$imageUrl.'" height="30" width="30">';
	  		}

	  		$html.= '<div class="rowDiv"><div class="cellDiv">'.$imagetag.$user['userfullname'].' ('.$user['employeeId'].')</div><div class="cellDiv">'.$groupName.'</div><div class="cellDiv">'. $roleName.'</div><div class="cellDiv">'.$user['emailaddress'].'</div><div class="cellDiv">'. sapp_Global::change_date($user['logindatetime'], 'view').' at '.sapp_Global::change_time($user['logindatetime'], 'view').'</div><div class="cellDiv  lastCell">'.$user['empipaddress'].'</div>';

	  	}
	  	echo $html; exit;
	  	 
	  }else{
	  	echo 'No matches found'; exit;
	  }


		}
	}

}