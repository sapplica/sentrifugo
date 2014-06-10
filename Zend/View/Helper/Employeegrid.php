<?php
/********************************************************************************* 
 *  This file is part of Sentrifugo.
 *  Copyright (C) 2014 Sapplica
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

/**
 * RapidHosts.com Zend Grid View Helper
 *
 * A View Helper that allows you to easily create Grids with Pagination
 *
 * @uses Zend_View_Helper_Abstract
 * @subpackage Grid
 * @copyright Copyright (c) 2010 Eric Haskins <admin@rapidhostsllc.com>
 *
 */

class Zend_View_Helper_Employeegrid extends Zend_View_Helper_Abstract {

	public $view = null;

	public $extra = array();
	
	private $output; // Container to hold the Grid

	public function setView(Zend_View_Interface $view) {

		$this->view = $view;

		return $this;

	}

	public function employeegrid ($dataArray)
	{
		$request = Zend_Controller_Front::getInstance();
        $params = $request->getRequest()->getParams();		
		$employeeTabs = array('employee','dependencydetails','creditcarddetails','visaandimmigrationdetails','workeligibilitydetails','disabilitydetails','empcommunicationdetails','empskills','empleaves','empholidays','medicalclaims','educationdetails','experiencedetails','trainingandcertificationdetails','emppersonaldetails','myemployees','empperformanceappraisal','emppayslips','empbenefits','emprenumerationdetails','emprequisitiondetails','empadditionaldetails','empsecuritycredentials','empsalarydetails','empjobhistory');	
		$controllerNamesArr = array('empleaves','empholidays','myemployees');
		$request = Zend_Controller_Front::getInstance();
        $dynamiccontrollerName = $request->getRequest()->getControllerName();
		//Give all 24tabs of employee.
		$view = Zend_Layout::getMvcInstance()->getView();	
		/*** 13-09-2013 
		$menu_model = new Default_Model_Menu();
		13-09-2013 ***/
		$session=new Zend_Auth_Storage_Session();
		$data=$session->read();
		//echo "Session Data : <pre>";print_r($data);die;
		$role_id = $data['emprole'];
		$group_id = $data['group_id'];
		$loggedinEmpId = $data['id'];
		$useridFromURL = $dataArray['userid'];	
		$controllerName = $dataArray['objectname'];	
		$menunamestr = '';		$sortStr ="";$objId='';$context ="";$actnArr = array();
		$sortStr = $dataArray['by'];
		$actions_arr=array();$privilegesofObj=array();
		/*** 13-09-2013
		
		$controllers_arr = $menu_model->getControllersByRole($role_id);
		$controllerName =$dataArray['objectname']."controller.php";
		if(isset($controllers_arr[$dataArray['objectname']."controller.php"]))
		{
			$actions_arr = $controllers_arr[$dataArray['objectname']."controller.php"]['actions'];
			$menuName = $actions_arr[sizeof($actions_arr)-1];
			//echo "<pre>";print_r($actions_arr);echo "</pre>";	die;	
			
		}
		else 
		13-09-2013 ***/
		
		if(in_array($dataArray['objectname'],$employeeTabs))
		{	
			/*** 13-09-2013
			$role_model = new Default_Model_Roles();
			13-09-2013 ***/
			/*
				Based on context (employees/Mydetails/Myemployees...) menuid/objectid should change...
				My Employees/My Team :	Only view...
				My Details :	get privileges based on roles
				HR >> Employees :	get privileges based on roles
			*/
				if(array_key_exists("context",$dataArray))
				{	
					$context = $dataArray['context'];
					if($dataArray['context'] == "mydetails")	$objId=MYDETAILS;
					else if($dataArray['context'] == "myteam")	$objId=MYEMPLOYEES;
					else if($dataArray['context'] == "")	$objId=EMPLOYEE;
				}
				else	{	$objId=EMPLOYEE;	}
				
				//echo " context ".$context.">> Obj Id >>".$objId;
			if($role_id != "")
			{
				/*** 13-09-2013 $getgroup_name = $role_model->getRolegroup($role_id);
				//echo "<pre>";print_r($getgroup_name);die;
				if(!empty($getgroup_name))
					$groupId = $getgroup_name[$role_id];
				if($groupId == 3)
				{
					// HR Group .....
					//Get privileges for HR group.....
				13-09-2013 *****/	
				
				$privilege_model = new Default_Model_Privileges();
				//For grids... From mydetails controller,privileges are sending to grid in dataArray
				if(array_key_exists("actions_arr",$dataArray))
				{
					//echo "<pre>";print_r($dataArray['actions_arr']);
					$actions_arr = $dataArray['actions_arr'];
					array_push($actions_arr,$dataArray['menuName']);
				}
				else
				{	
					$idCsv=0;	//flag 	- No id is single id not Csv... in dashboard we are sending CSV....
					$privilegesofObj = $privilege_model->getObjPrivileges($objId,"",$role_id,$idCsv);
					//echo "<br/>objId ".$objId." >> role >> ".$role_id;
					//echo "<br/>Privileges <pre>";print_r($privilegesofObj);	
					if(!empty($privilegesofObj))
					{
						//If the logged in employee is HR or Superadmin(role =1) or Employee himself want to edit his record.....
						/*if($group_id == HR_GROUP||$role_id == SUPERADMINROLE||$loggedinEmpId == $useridFromURL)
						{*/
							if($privilegesofObj['viewpermission'] == 'Yes')	
								array_push($actions_arr,'view');  
							//For Leaves,Holidays there shoould be only view privilege..... 
							if(!in_array($controllerName,$controllerNamesArr))
							{
								if($privilegesofObj['editpermission'] == 'Yes')	
								  array_push($actions_arr,'edit');
								if($privilegesofObj['deletepermission'] == 'Yes')
								  array_push($actions_arr,'delete');
								if($privilegesofObj['addpermission'] == 'Yes')	
								  array_push($actions_arr,'add');
								if($privilegesofObj['uploadattachments'] == 'Yes')
								  array_push($actions_arr,'upload');
								if($privilegesofObj['viewattachments'] == 'Yes')
								  array_push($actions_arr,'uploadview');
								
							}  
							array_push($actions_arr,$dataArray['menuName']);
						/*}
						else
						{
							$actions_arr[0]='view'; 
							$actions_arr[1]=$dataArray['menuName'];
						}*/
					}
						else
						{
							$actions_arr[0]='view';
							$actions_arr[1]=$dataArray['menuName'];
						}
					}
				/* 13-09-2013
				}
				else
				{
					$actions_arr[0]='view'; $actions_arr[1]=$dataArray['menuName'];
				}
				13-09-2013 */
			}	
			
		}
		else	{	$actions_arr =array();}
		
		/*
			Purpose:	If the privileges are empty then, action column should not build in grid..
			Modified Date:	11/7/2013.
			Modified By:	Yamini.
		*/
		$gridFieldsArr=array();$tmpActionsArr=array();
		$tmpActionsArr = $actions_arr;
		array_pop($tmpActionsArr);	//last element of actions array is menuname so delete that & check the privileges are empty or not...
		//print_r($tmpActionsArr);//die;
		$actnArr = $tmpActionsArr;
		if(($key = array_search('add', $actnArr)) !== false) 
		{
			unset($actnArr[$key]);
		}
		if(empty($tmpActionsArr))	
		{
			$gridFieldsArr = $dataArray['tableheader'];
			unset($gridFieldsArr['action']);
			 $dataArray['tableheader']=$gridFieldsArr;
		}
		//echo "<pre>";print_r($gridFieldsArr);print_r($dataArray['tableheader']);echo "</pre>";
		
		if(isset($dataArray['menuName']))
			$menuName = $dataArray['menuName'];	
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($dataArray['tablecontent']));                
		$paginator->setItemCountPerPage($dataArray['perPage'])
		->setCurrentPageNumber($dataArray['pageNo']); 
		if(empty($dataArray['tableheader']))
		{
			$widgetsModel = new Default_Model_Widgets();
			$columnData = $widgetsModel->getTableFields('/'.$dataArray['objectname']);
			$dataArray['tableheader'] = json_decode($columnData['menufields'],true);				
		}
		$msgtitle = $dataArray['objectname'].'_delete';
		$msgtitle = strtoupper($msgtitle);
		$msgflag = constant($msgtitle);
		$msgAr = explode(' ',$msgflag);
		$msgdta = implode('@#$',$msgAr);
		if(isset($dataArray['formgrid']) && $dataArray['formgrid'] == 'true') 
		{	
			$urlString = $_SERVER['REQUEST_URI'];
			$urlData = explode('/',$urlString);$con ='';
			//print_r($urlData);echo sizeof($urlData);
				/*if(sizeof($urlData) > 5)
				$con = '/unitId/'.$urlData[5];	*/
			/*$domainName = trim(DOMAIN,'/');
			if(!in_array($domainName,$urlData))
			{
				if(sizeof($urlData) > 4)
					$con = '/unitId/'.$urlData[4];
				else
					$con = '/unitId/'.$dataArray['unitId'];	
			}
			else
			{
				if(sizeof($urlData) > 5)
					$con = '/unitId/'.$urlData[5];
				else
						$con = '/unitId/'.$dataArray['unitId'];	
			}
			//echo $con;
			$formgridVal = $dataArray['formgrid'];*/
			/*$domainName = trim(DOMAIN,'/');
			if(!in_array($domainName,$urlData))
			{
				if(sizeof($urlData) > 16 && $urlData[15] == 'unitId')
				{	$con = '/unitId/'.$urlData[16];		 }
				else if(sizeof($urlData) > 4 && $urlData[4] != 'html')
				{	$con = '/unitId/'.$urlData[4];				}
				else
				{	$con = '/unitId/'.$dataArray['unitId'];	}
			}
			else
			{
				if(sizeof($urlData) > 13 && $urlData[12] == 'unitId')
				{	$con = '/unitId/'.$urlData[13];		}
				else if(sizeof($urlData) > 17 && $urlData[16] == 'unitId')
				{	$con = '/unitId/'.$urlData[17];		}
				else if(sizeof($urlData) > 5 && $urlData[5] != 'html')
				{	$con = '/unitId/'.$urlData[5];		}
				else
				{		$con = '/unitId/'.$dataArray['unitId']; }
			}*/
			if(isset($dataArray['unitId']))
				$con = '/unitId/'.$dataArray['unitId'];
			else{
			    if(isset($params['id'])){
				   $con = '/unitId/'.$params['id'];
			    }
			}
			
			$formgridVal = $dataArray['formgrid'];
			
			if($dataArray['objectname'] == 'departments'){
			  $viewaction = 'view';			
			}else			{
			  $viewaction = 'viewpopup';
			}
			
			$editaction = 'editpopup';		
			if($dataArray['menuName'] !='')
			  $menunamestr = $dataArray['menuName'];
			
			$viewpopup_str = '<a onclick="displaydeptform(\''.DOMAIN.$dataArray['objectname'].'/'.$viewaction.'/id/{{id}}'.$con.'/popup/1\',\''.$menunamestr.'\')" name="{{id}}" class="sprite view"  title=\'View\'></a>';
			$editpopup_str = '<a onclick="displaydeptform(\''.DOMAIN.$dataArray['objectname'].'/'.$editaction.'/id/{{id}}'.$con.'/popup/1\',\''.$menunamestr.'\')" name="{{id}}" class="sprite edit"  title=\'Edit\' ></a>';
			$deletepopup_str = '<a name="{{id}}" onclick= changeEmployeestatus(\''.$dataArray['objectname'].'\',\'{{id}}\',\''.$msgdta.'\',\''.$dataArray['userid'].'\')	href= javascript:void(0) title=\'Delete\' class="sprite delete" ></a>';
			
			
			if(!in_array('view',$actions_arr) && !in_array('edit',$actions_arr) && !in_array('delete',$actions_arr))
			{
				if($dataArray['objectname'] == 'processes')
				{					
					 $extra['action'] = array('name' => 'edit', 'value' =>'<div class="grid-action-align">
								<a onclick="displaydeptform(\''.DOMAIN.$dataArray['objectname'].'/'.$viewaction.'/id/{{id}}'.$con.'/popup/1\',\''.$menunamestr.'\')" name="{{id}}" class="sprite view"  title=\'View\'></a>
								<a onclick="displaydeptform(\''.DOMAIN.$dataArray['objectname'].'/'.$editaction.'/id/{{id}}'.$con.'/popup/1\',\''.$menunamestr.'\')" name="{{id}}" class="sprite edit"  title=\'Edit\' ></a>
								<a name="{{id}}" onclick= changeEmployeestatus(\''.$dataArray['objectname'].'\',\'{{id}}\',\''.$msgdta.'\',\''.$dataArray['userid'].'\')	href= javascript:void(0) title=\'Delete\' class="sprite delete" ></a>
							</div>'); 
				  }
				  else 
					$extra['action'] =array(); 				  
				}else
				{
				
				  $extra['action'] = array('name' => 'edit', 'value' =>'<div class="grid-action-align">
								'.((in_array('view',$actions_arr)?$viewpopup_str:'')).'
								'.((in_array('edit',$actions_arr)?$editpopup_str:'')).'
								'.((in_array('delete',$actions_arr)?$deletepopup_str:'')).'
							</div>'); //onclick ="javascript:editlocdata(\'{{id}}\')" 
				}
			
			/*$extra['action'] = array('name' => 'edit', 'value' =>'<div class="grid-action-align">
									<a onclick="displaydeptform(\''.DOMAIN.$dataArray['objectname'].'/'.$viewaction.'/id/{{id}}'.$con.'/popup/1\')" name="{{id}}" class="sprite view"  title=\'View\'></a>
									<a onclick="displaydeptform(\''.DOMAIN.$dataArray['objectname'].'/'.$editaction.'/id/{{id}}'.$con.'/popup/1\')" name="{{id}}" class="sprite edit"  title=\'Edit\' ></a>
									<a name="{{id}}" onclick= changeEmployeestatus(\''.$dataArray['objectname'].'\',\'{{id}}\',\''.$msgdta.'\')	href= javascript:void(0) title=\'Delete\' class="sprite delete" ></a>
								</div>'); */			
		}
		else
		{
			$formgridVal = '';
			            $view_str = '<a href= "'.DOMAIN.$dataArray['objectname'].'/view/id/{{id}}" name="{{id}}" class="sprite view"  title=\'View\'></a>'; 
                        $edit_str = '<a href= "'.DOMAIN.$dataArray['objectname'].'/edit/id/{{id}}" name="{{id}}" class="sprite edit"  title=\'Edit\'></a>';
						if($dataArray['objectname'] == 'employee')
						{
							//$delete_str = '<a name="{{id}}" onclick= changestatus(\''.$dataArray['objectname'].'\',\'{{id}}\',\'getwidgetGrid\')	href= javascript:void(0) title=\'Delete\' class="sprite delete" ></a>';
							$delete_str  = '';
						}
						else
                        $delete_str = '<a name="{{id}}" onclick= changeEmployeestatus(\''.$dataArray['objectname'].'\',\'{{id}}\',\''.$msgdta.'\',\''.$dataArray['userid'].'\')	href= javascript:void(0) title=\'Delete\' class="sprite delete" ></a>';
			/*$extra['action'] = array('name' => 'edit', 'value' =>'<div class="grid-action-align">
									<a href= "'.DOMAIN.$dataArray['objectname'].'/view/id/{{id}}" name="{{id}}" class="sprite view" ></a>
									'.(($role_id != 1)?(in_array('edit',$actions_arr)?$edit_str:''):$edit_str).'
									'.(($role_id != 1)?(in_array('delete',$actions_arr)?$delete_str:''):$delete_str).'
								</div>'); //onclick ="javascript:editlocdata(\'{{id}}\')"
                        */
						if(!in_array('view',$actions_arr) && !in_array('edit',$actions_arr) && !in_array('delete',$actions_arr))
						{
						  $extra['action'] =array(); 
						}else
						{
						  $extra['action'] = array('name' => 'edit', 'value' =>'<div class="grid-action-align">
										'.((in_array('view',$actions_arr)?$view_str:'')).'
										'.((in_array('edit',$actions_arr)?$edit_str:'')).'
										'.((in_array('delete',$actions_arr)?$delete_str:'')).'
									</div>'); //onclick ="javascript:editlocdata(\'{{id}}\')" 
						}		
		}
		$extra['options'] = array(); 
        $addaction= '';  		
		if(isset($dataArray['add']) && $dataArray['add'] !='')
		{
		  $addaction = $dataArray['add'];
		}
		else
		{
		  $addaction = '';
		}
		$unitId = '';
		
		if(in_array('add',$actions_arr))
		{
			$addpermission = "true";
		}
		else
		{
		 $addpermission = "false";
		}
		//echo " >  call ".$dataArray['call'];
		if(isset($dataArray['unitId'])) $unitId = $dataArray['unitId'];
		return $this->generateGrid($dataArray['objectname'],$dataArray['tableheader'],$paginator,$extra,true,$dataArray['jsGridFnName'], $dataArray['perPage'],$dataArray['pageNo'],$dataArray['jsFillFnName'],$dataArray['searchArray'],$formgridVal,$addaction,$menuName,$unitId,$addpermission,$menunamestr,isset($dataArray['call'])?$dataArray['call']:"",$sortStr,$context,isset($dataArray['search_filters'])?$dataArray['search_filters']:"",isset($dataArray['dashboardcall'])?$dataArray['dashboardcall']:"No",$actnArr,isset($dataArray['sort'])?$dataArray['sort']:"",isset($dataArray['by'])?$dataArray['by']:"");
		
	}
	
	/**
	 *
	 * @param string $name
	 * @param array $fields
	 * @param Zend_Paginator Instance $paginator
	 * @param array $extracolumn
	 * @param Bool  $sorting
	 *
	 * @return string
	 */

	public function generateGrid ($name, $fields = null,$paginator=null,$extracolumn=array(),$sorting=false,$jsGridFnname='', $perPage='5',$page='1', $jsFillFnName='',$searchArray='',$formgrid='false',$addaction='',$menuName='',$unitId,$addpermission,$menunamestr,$call='',$sortStr='',$conText="",$search_filters = '',$dashboardCall='No',$actnArr,$sortname='',$by='') {        
		$request = Zend_Controller_Front::getInstance();
        $params = $request->getRequest()->getParams();		
        //echo "<pre>";print_r($params);echo "</pre>";
		// Store Extra Columns
		$this->extra = $extracolumn;	$sortIconStr ="";

		$sort = Zend_Controller_Front::getInstance()->getRequest()->getParam('sort','DESC');

		// checking and handling sorting.
		if($sort == "")
		{
			$sortIconStr = "<span class='s-ico'>
			<span class='ui-icon-desc ui-state-disabled ui-icon ui-icon-triangle-1-n'></span>
			<span class='ui-icon-asc ui-state-disabled ui-icon ui-icon-triangle-1-s'></span></span>";
		}
		else if ($sort  ==  'ASC')
		{
			$sort = 'DESC';
			//For Sort Icons....
			$sortIconStr = "<span class='s-ico'>
			<span class='ui-icon-desc ui-icon ui-icon-triangle-1-n'></span>
			</span>";
		}  
		else 
		{
			$sort = 'ASC';
			//For Sort Icons....
			$sortIconStr = "<span class='s-ico'>
				<span class='ui-icon-asc  ui-icon ui-icon-triangle-1-s'></span></span>";
		}
		if($call != 'ajaxcall')		$sortIconStr="";
		
		if($addaction !='')
		{
		  $action = $addaction;
		  $popupaction = 'addpopup';
		} else
		{
		  $action = "edit";
		  $popupaction = 'editpopup';
		}
		//$output = '<script language="JavaScript" type="text/javascript" src="'.MEDIA_PATH.'jquery/js/slimScrollHorizontal.js"></script>';		
		$con ='';
		//echo "form grid > ". $formgrid;
		if($formgrid != '')
		{
			$urlString = $_SERVER['REQUEST_URI'];
			$urlData = explode('/',$urlString);
			//echo "<pre>";print_r($urlData);die;
			//echo $unitId;
			if($unitId != '')
			{
				$con = 'unitId/'.$unitId;
			}
			else 
			{
				$con = 'unitId/'.$params['userid'];
				/*$domainName = trim(DOMAIN,'/');
				if(!in_array($domainName,$urlData))
				{
					if(sizeof($urlData) > 4 )
						$con = 'unitId/'.$urlData[4];
					else
						$con = 'unitId/'.$unitId;	
				}
				else
				{
					if(sizeof($urlData) > 5)
						$con = 'unitId/'.$urlData[5];
					else
							$con = 'unitId/'.$unitId;
				}*/
			}
			/*else if(sizeof($urlData) > 5)
			{
				if($urlData[5] != 'html')
					$con = 'unitId/'.$urlData[5];
				else 
					$con = 'unitId/'.$unitId;
			}*/
			//echo $con;
			$output ="<div class='table-header'><span>".$menuName."</span><input type='button'   title = 'Add' onclick='displaydeptform(\"".DOMAIN.$name.'/'.$popupaction."/$con/popup/1\",\"".$menunamestr."\")' value='Add Record' class='sprite addrecord' /></div>";
		}
		else
		{	
			/*
				Purpose:	From dash board,if employee grid is congifured as widget & click on add icon it should redirect to 	add action in employee controller.
				Modified Date:	09/10/2013.
				Modified By:	yamini.
			*/
			//echo $name." >> ".$action;
			if($name == "employee" && $action == "edit")
			{	
				$actionStr = 'add';
				$output ="<div class='table-header'><span>".$menuName."</span><input type='button' title = 'Add' onclick='window.location.href=\"".DOMAIN.$name.'/'.$actionStr."\"' value='Add Record' class='sprite addrecord' /></div>";
			}
			else
			{	
				$output ="<div class='table-header'><span>".$menuName."</span><input type='button' title = 'Add' onclick='window.location.href=\"".DOMAIN.$name.'/'.$action."\"' value='Add Record' class='sprite addrecord' /></div>";
			}
		} 
		
		if($addpermission == 'false')
		{		 
		  $output ="<div class='table-header'><span>".$menuName."</span></div>";
		}
		$output .="<div id='".$name."' class='details_data_display_block newtablegrid'>";
		$output .= "<table class='grid' align='center'  width='100%' cellspacing='0' cellpadding='4' border='0'><thead><tr>";
		// this foreach loop display the column header  in �th� tag.
		$colinr = 0;
		
		if(!empty($fields)) 
		{	
			$tabindx = 0;
			if(empty($actnArr)) unset($fields['action']);
			foreach ($fields as $key => $value) {	
				//echo"<pre>";print_r($value);
				if(isset($value['align'])) $align = (@$value['align'] != '')? 'align="'.$value['align'].'" ':'';
				if(isset($value['sortkey']))$sortkey = (@$value['sortkey'] != '')? 'align="'.$value['sortkey'].'" ':'';
				
				if(isset($value['style']))$style = (@$value['style'] != '')? 'style="'.$value['style'].'" ':'';
				
				$value = (is_array($value) && !isset($value['sortkey']))? $value['value']:$value;	
				if($value == 'Action') $width = 'width=90'; else $width =  '';//'width='.$eachColumnWidth;
				$output .= "<th ".$width.">";
				// Check if Sorting is set to True
				if($sorting) {

					// Disable Sorting if Key is in Extra Columns
					if(@$this->extra[$key]['name'] != '' && !is_array($value)) {
						if($value == "Action")	
							$output .= "<span class='action-text'>Action</span>";
						else
							$output .= $value;
					} else {
						if(is_array($value)){
							$key = $value['sortkey'];
							$value = $value['value'];
						} 
						$welcome = 'false';
						$urlString = $_SERVER['REQUEST_URI'];
						if (strpos($urlString,'welcome') !== false) {
							$welcome = 'true';
						}
						
						if($formgrid=='true')
						{		
							$output .= "<a href='javascript:void(0);' onclick=javascript:paginationndsorting('".DOMAIN.$name."/index/sort/".$sort."/by/".$key."/objname/".$name."/page/".$page."/per_page/".$perPage."/call/ajaxcall/$con/context/".$conText."');>".$value."</a>";
							//For Sort Icons....
							if($sortStr == $key)
								$output .= $sortIconStr;						
						}
						else if($welcome == 'true')
						{	
							// For HR >> Employees & ESS > My Team we are using index action not edit action .. 
							if($name == "employee" || $name = "myemployees")
							{	
								$output .= "<a href='javascript:void(0);' onclick=javascript:paginationndsorting('".DOMAIN.$name."/index/sort/".$sort."/by/".$key."/objname/".$name."/page/".$page."/per_page/".$perPage."/call/ajaxcall/$con/');>".$value."</a>";
							}
							else
							{
								$output .= "<a href='javascript:void(0);' onclick=javascript:paginationndsorting('".DOMAIN.$name."/index/sort/".$sort."/by/".$key."/objname/".$name."/page/".$page."/per_page/".$perPage."/call/ajaxcall/$con/');>".$value."</a>";
							}
								//For Sort Icons....
								if($sortStr == $key)
								$output .= $sortIconStr;							
							}
						else {
							$output .= "<a href='javascript:void(0);' onclick=javascript:paginationndsorting('".$this->view->url(array('sort'=>$sort,'by'=>$key,'objname'=> $name,'page' => $page,'per_page'=>$perPage))."');>".$value."</a>";
							//For Sort Icons....
								if($sortStr == $key)
								$output .= $sortIconStr;						
						}
						
					}
				}  else {
					//For Sort Icons....
					if($sortStr == $key)
						$output .= $sortIconStr;						
					$output .= $value;

				}

				$output .= "</th>";
				$colinr++;
				$tabindx++;
			}//end of for each loop
                         $output .= "</tr><tr id='search_tr_$name'>";
                        $tabindx = 0;
			foreach ($fields as $key => $value) {	
				//echo"<pre>";print_r($value);
				if(isset($value['align'])) $align = (@$value['align'] != '')? 'align="'.$value['align'].'" ':'';
				if(isset($value['sortkey']))$sortkey = (@$value['sortkey'] != '')? 'align="'.$value['sortkey'].'" ':'';
				
				if(isset($value['style']))$style = (@$value['style'] != '')? 'style="'.$value['style'].'" ':'';
				
				$value = (is_array($value) && !isset($value['sortkey']))? $value['value']:$value;	
				if($value == 'Action') $width = 'width=90'; else $width =  '';//'width='.$eachColumnWidth;
				$output .= "<th ".$width.">";
				// Check if Sorting is set to True
				if($sorting) {

					// Disable Sorting if Key is in Extra Columns
					if(@$this->extra[$key]['name'] != '' && !is_array($value)) {
						if($value == "Action")	
							$output .= "<span class='action-text'></span>";
						else
							$output .= $value;
					} else {
						if(is_array($value)){
							$key = $value['sortkey'];
							$value = $value['value'];
						} 
						$welcome = 'false';
						$urlString = $_SERVER['REQUEST_URI'];
						
						if($key != 'id')
						{
							$sText = '';
							//$output .= "<input type='text' class='searchtxtbox' value='' onkeyup=javascript:paginationndsorting('".$this->view->url(array('sort'=>$sort,'by'=>$key,'')). style='display:none;' />";
							if(!empty($searchArray)) $display = 'display: block;'; else $display = 'display: none;';
							if(is_array($searchArray)) { if(array_key_exists($key,$searchArray)) $sText = $searchArray[$key]; else $sText = ''; }
							//$output .= "<input type='text' name='searchbox' id='$key' style='$display' class='searchtxtbox' value='$sText' onkeyup='getsearchdata(\"$key\",this.value,\"$name\")' />";
                                                        //echo "<pre>key==".$key;print_r($search_filters[$key]);print_r($search_filters[$key]);echo "<hr/></pre>";
							if(isset($search_filters[$key]))
							{
                                                            $search_function = 'getsearchdata("'.$name.'","'.$conText.'","'.$key.'",event';
								$output .= sapp_Global::grid_data($search_filters,$key,$name,$display,$sText,$tabindx,$search_function);
								
							}
							else
								$output .= "<input tabIndex=$tabindx type='text' name='$name' id='$key' style='$display' class='searchtxtbox_$name table_inputs grid_search_inputs' value='$sText' onkeydown='getsearchdata(\"$name\",\"$conText\",\"$key\",event,\"text\")' />";
						}
					}
				}  else {
					//For Sort Icons....
					if($sortStr == $key)
						$output .= $sortIconStr;						
					$output .= $value;

				}

				$output .= "</th>";
				$colinr++;
				$tabindx++;
			}//end of for each loop
		}
		$output .= "</tr>

        </thead>";

		$output .="<tbody>";

		// Start Looping Data
		$ii=0;
                
		foreach($paginator as $p) {
			$cell_color = ($ii % 2 == 0 ? "row1" : "row2");
			$ii++;$bodyCount = 0;
			$output.="<tr onclick='selectrow($name,this);' class='$cell_color'>";
			// Reset Fields Array to Top
			if(!empty($fields)) 
			{ 
				reset($fields); 
				foreach($fields AS $k=>$v) {
								$tdclass = '';
					// Look for additional attributes
					$characterlimit = 40;
					if(is_array($v)) {
						$class = (@$v['class'] != '')? 'class="'.$v['class'].'" ':'';
						$align = (@$v['align'] != '')? 'align="'.$v['align'].'" ':'';
						$valign = (@$v['valign'] != '')? 'valign="'.$v['valign'].'" ':'';
						if(isset($v['characterlimit']))
							$characterlimit = $v['characterlimit'];
						$output .= "<td {$tdclass}{$align}{$valign}>";
					} else {
						$output .= "<td {$tdclass}>";
					}
					// Check to see if this Field is in Extra Columns
					if(isset($this->extra[$k]['value'])) {
						$output .= $this->_parseExtra($k,$p);
					} else {					
						if( $bodyCount== 0 && $jsFillFnName != '')
						{
							$valToInclude = (strlen($p[$k])>$characterlimit)? substr($p[$k],0,$characterlimit)."..":$p[$k];
							//$output .= "<a onclick= ".$jsFillFnName."(\"/id/$p[id]\") href= 'javascript:void(0)' title='".addslashes (htmlspecialchars(strip_tags ($p[$k])))."' >".addslashes (htmlspecialchars(strip_tags ($valToInclude)))."</a>";
							$output .= "<a onclick= ".$jsFillFnName."(\"/id/$p[id]\") href= 'javascript:void(0)' title='".htmlentities ($p[$k], ENT_QUOTES, "UTF-8")."' >".htmlentities ($valToInclude, ENT_QUOTES, "UTF-8")."</a>";
						}
						else{
                                                    
							$p = (array)$p;//Asma modification
							if(isset($p[$k])) {
							 $valToInclude = (strlen($p[$k])>$characterlimit)? substr($p[$k],0,$characterlimit)."..":$p[$k];
							//$output .= "<span  title='".addslashes (htmlspecialchars (strip_tags ($p[$k])))."' >".addslashes (htmlspecialchars (strip_tags($valToInclude)))."</span>";
                                                         $output .= "<span  title='".htmlentities($p[$k], ENT_QUOTES, "UTF-8")."' >".htmlentities($valToInclude, ENT_QUOTES, "UTF-8")."</span>";
							}
							//$output .= $p[$k];
						}
					}

					$output .= "</td>";
					$bodyCount++;
				}
			}
			// Close the Table Row
			$output.="</tr>";

		}
		if($ii == 0){
			$output.= "<tr><td colspan='$colinr' class='no-data-td'><p class='no-data'>No data found</p></td></tr>";
		}
		$output .= "</tbody>";
		$output .="</table>
                    <script type='text/javascript' language='javascript'>
                $(document).ready(function(){
                                    
                                    
                                    if($('.searchtxtbox_".$name."').is(':visible'))
                                    {
                                        
                                        $('#search_tr_".$name."').show();	
                                    }
                                    else 
                                    {
                                        
                                        $('#search_tr_".$name."').hide();	
                                           
                                    }
                                    });
                </script> 
</div>";
		/*if($ii == 0){
			$output .="<div style='height:50px;'>&nbsp;</div>";	
		}*/
		// Attach Pagination
		if($paginator) {

			//$output .="<tfoot>";

			// $output .="<td align='center' colspan='".count($fields)."'>";
			$params = array();
			$params['jsGridFnName'] = $jsGridFnname;
			$params['perPage'] = $perPage;
			$params['objname'] = $name;
			$params['searchArray'] = $searchArray;			
			$params['formgrid'] = $formgrid;
			$params['con'] = $con;
			$params['context'] = $conText;
			$params['dashboardcall'] = $dashboardCall;
			$params['sortname'] = $sortname;
			$params['by'] = $by;
			
			$output.= $this->view->paginationControl($paginator,

                    'Sliding',

                    'partials/pagination.phtml',$params);

			//$output .="</tfoot>";
		}
		$output .= "<script>$('#$name').slimScrollHorizontal({
									  alwaysVisible: false,
									  start: 'left',
									  position: 'bottom',
									 
									}).css({ background: '#ccc', paddingBottom: '10px' }); </script>";
		$output .= "<script>
						var id = $('#columnId').val();
						var coldata = $('#'+id).val();
						var focusID = $('#columnId').val();
                                                var fval = $('#'+focusID).attr('data-focus');
                                                if(fval == '' || fval == null)
						$('#'+focusID).focus().val('').val(coldata);
					</script>";
		return $output;
	}

	/**
	 * Function that Parses Extra Column info
	 *
	 * Regex looks for {{field_name}}
	 *
	 * @param string $column
	 * @param array $p
	 * @return string
	 */
	public function _parseExtra($column,$p) {

		if(isset ($this->extra[$column])) {
			$val = '';

			$characterlimit = 15;
			if(isset($this->extra[$column]['characterlimit']))
						$characterlimit = $this->extra[$column]['characterlimit'];
			preg_match_all('/\{\{(.*?)\}\}/', $this->extra[$column]['value'], $matches);
			if(count($matches[1]) > 0) {
				$matches[1] = array_unique($matches[1]);
				$a = $this->extra[$column]['value'];
				//echo"<pre>";print_r($matches[1]);die;
				foreach($matches[1] AS $match) {
					$p = (array)$p;
					$a = str_replace('{{'.$match.'}}',$p[$match], $a);
					preg_match_all('/\[\[(.*?)\]\]/', $a, $newMaches);
					if(count($newMaches[1]) > 0) {
						foreach($newMaches[1] AS $matchNew) {

							$valToInclude = (strlen($p[$matchNew])>$characterlimit)? substr($p[$matchNew],0,$characterlimit)."..":$p[$matchNew];
							$a = str_replace('[['.$matchNew.']]',$valToInclude, $a);
						}
					}

				}
			}
			$val = $a;
			return $val;
		}

		return '';
	}
}
?>
