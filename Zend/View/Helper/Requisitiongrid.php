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
 
 *
 */

class Zend_View_Helper_Requisitiongrid extends Zend_View_Helper_Abstract {

	public $view = null;

	public $extra = array();

	private $output; // Container to hold the Grid

	public function setView(Zend_View_Interface $view) {

		$this->view = $view;

		return $this;

	}

	public function requisitiongrid ($dataArray)
	{
		$request = Zend_Controller_Front::getInstance();
        $params = $request->getRequest()->getParams();	
		$view = Zend_Layout::getMvcInstance()->getView();	
		$menu_model = new Default_Model_Menu();
		$session=new Zend_Auth_Storage_Session();
		$data=$session->read();
		$role_id = $data['emprole'];
		$menunamestr = '';$sortStr = "";$actnArr = array();
		$sortStr = $dataArray['by'];
		$controllers_arr= $menu_model->getControllersByRole($role_id);
		//for isactive=0 menus for approved requisition and rejected requisitions
	    //$controllers_arr2= $menu_model->getControllersByRolesforRequisition($role_id);
       // $controllers_arr=array_merge($controllers_arr1,$controllers_arr2);
	
		if($dataArray['objectname'] == 'interviewrounds') $actionsobjname = 'scheduleinterviews';
		else $actionsobjname = $dataArray['objectname'];
		if(isset($controllers_arr[$actionsobjname."controller.php"]))
		{
			$actions_arr = $controllers_arr[$actionsobjname."controller.php"]['actions'];
			
			$menuName = $actions_arr[sizeof($actions_arr)-1];
			
		}
		else 
			$actions_arr = array();
			
		
		$gridFieldsArr=array();$tmpActionsArr=array();
		$tmpActionsArr = $actions_arr;
		array_pop($tmpActionsArr);	//last element of actions array is menuname so delete that & check the privileges are empty or not...
		
		$actnArr = $tmpActionsArr;
		if(($key = array_search('add', $actnArr)) !== false) 
		{
			unset($actnArr[$key]);
		}
		if(empty($tmpActionsArr) && $dataArray['objectname'] != 'apprreqcandidates')	
		{
			$gridFieldsArr = $dataArray['tableheader'];
			unset($gridFieldsArr['action']);
			$dataArray['tableheader']=$gridFieldsArr;
		}
		
		           
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
			
			$viewpopup_str = '<a onclick="displaydeptform(\''.BASE_URL.$dataArray['objectname'].'/'.$viewaction.'/id/{{id}}'.$con.'/popup/1\',\''.$menunamestr.'\')" name="{{id}}" class="sprite view"  title=\'View\'></a>';
			$editpopup_str = '<a onclick="displaydeptform(\''.BASE_URL.$dataArray['objectname'].'/'.$editaction.'/id/{{id}}'.$con.'/popup/1\',\''.$menunamestr.'\')" name="{{id}}" class="sprite edit"  title=\'Edit\' ></a>';
			$deletepopup_str = '<a name="{{id}}" onclick= changestatus(\''.$dataArray['objectname'].'\',\'{{id}}\',\''.$msgdta.'\')	href= javascript:void(0) title=\'Delete\' class="sprite delete" ></a>';
			
				if(!in_array('view',$actions_arr) && !in_array('edit',$actions_arr) && !in_array('delete',$actions_arr))
				{
					   
				  if($dataArray['objectname'] == 'interviewrounds')
				  {					
					 $extra['action'] = array('name' => 'edit', 'value' =>'<div class="grid-action-align">
								<a onclick="displaydeptform(\''.BASE_URL.$dataArray['objectname'].'/'.$viewaction.'/id/{{id}}'.$con.'/popup/1\',\''.$menunamestr.'\')" name="{{id}}" class="sprite view"  title=\'View\'></a>
								<a onclick="displaydeptform(\''.BASE_URL.$dataArray['objectname'].'/'.$editaction.'/id/{{id}}'.$con.'/popup/1\',\''.$menunamestr.'\')" name="{{id}}" class="sprite edit"  title=\'Edit\' ></a>
								<a name="{{id}}" onclick= changestatus(\''.$dataArray['objectname'].'\',\'{{id}}\',\''.$msgdta.'\')	href= javascript:void(0) title=\'Delete\' class="sprite delete" ></a>
							</div>'); 
				  }
                   else if($dataArray['objectname'] == 'apprreqcandidates')
				  {
                                      $extra['action'] = array('name' => 'edit', 'value' =>'<div class="grid-action-align"><a onclick="displaydeptform(\''.BASE_URL.$dataArray['objectname'].'/'.$viewaction.'/id/{{id}}'.$con.'/popup/1\',\''.$menunamestr.'\')" name="{{id}}" class="sprite view"  title=\'View\'></a></div>'); 
                  }
				  else 
                   {
					$extra['action'] =array(); 				  
                                        
                  }
				}else
				{
					   
					if($dataArray['objectname'] ==  'approvedrequisitions' || $dataArray['objectname'] ==  'shortlistedcandidates')
					{
                                            
						$view_str = '<a href= "'.BASE_URL.$dataArray['objectname'].'/view/id/{{id}}" name="{{id}}" class="sprite view"  title=\'View\'></a>'; 
                        $edit_str = '<a href= "'.BASE_URL.$dataArray['objectname'].'/edit/id/{{id}}" id="edit{{id}}" name="{{id}}" class="sprite edit"  title=\'Edit\'></a>';
                        $delete_str = '<a name="{{id}}" onclick= changestatus(\''.$dataArray['objectname'].'\',\'{{id}}\',\''.$msgdta.'\')	href= javascript:void(0) title=\'Delete\' class="sprite delete" ></a>';
						$extra['action'] = array('name' => 'edit', 'value' =>'<div class="grid-action-align">
										'.((in_array('view',$actions_arr)?$view_str:'')).'
										'.((in_array('edit',$actions_arr)?$edit_str:'')).'
										'.((in_array('delete',$actions_arr)?$delete_str:'')).'
									</div>');
						if(isset($dataArray['defined_actions']) && count($dataArray['defined_actions'])>0)
						{
							$extra['action'] = array('name' => 'edit', 'value' =>'<div class="grid-action-align">
								'.((in_array('view',$dataArray['defined_actions'])?$view_str:'')).'
								'.((in_array('edit',$dataArray['defined_actions'])?$edit_str:'')).'
								'.((in_array('delete',$dataArray['defined_actions'])?$delete_str:'')).'
							</div>');
						}
						if($dataArray['objectname'] ==  'approvedrequisitions')
						{
							
							$auth = Zend_Auth::getInstance();
							if($auth->hasIdentity())
							{
								$loginUserId = $auth->getStorage()->read()->id;
								$loginuserGroup = $auth->getStorage()->read()->group_id;
								$loginuserRole = $auth->getStorage()->read()->emprole;
							}
							if($loginuserRole == SUPERADMINROLE || $loginuserGroup == HR_GROUP || $loginuserGroup == MANAGEMENT_GROUP)
							{
								$addemployee = '<a href= "'.BASE_URL.'candidatedetails'.'/add/req_id/{{id}}" name="{{id}}" class="sprite add_candidate"  title=\'Add candidate\' id="addemployee{{id}}"></a>';
				
								$extra['action'] = array('name' => 'edit', 'value' =>'<div class="grid-action-align">
											'.((in_array('view',$actions_arr)?$view_str:'')).'
											'.((in_array('edit',$actions_arr)?$edit_str:'')).'
											'.((in_array('delete',$actions_arr)?$delete_str:'')).'
											'.((in_array('addcandidate',$actions_arr)?'':$addemployee)).'
										</div>');	
							}
							else{
								
								$extra['action'] = array('name' => 'edit', 'value' =>'<div class="grid-action-align">
											'.((in_array('view',$actions_arr)?$view_str:'')).'
											'.((in_array('edit',$actions_arr)?$edit_str:'')).'
											'.((in_array('delete',$actions_arr)?$delete_str:'')).'
										</div>');	
							}							
								
						}
							
					}
					else{
                                            
					  $extra['action'] = array('name' => 'edit', 'value' =>'<div class="grid-action-align">
									'.((in_array('view',$actions_arr)?$viewpopup_str:'')).'
									'.((in_array('edit',$actions_arr)?$editpopup_str:'')).'
									'.((in_array('delete',$actions_arr)?$deletepopup_str:'')).'
								</div>'); 
					}
				}
									
		}
		else
		{
			$formgridVal = '';
			            $view_str = '<a href= "'.BASE_URL.$dataArray['objectname'].'/view/id/{{id}}" name="{{id}}" class="sprite view"  title=\'View\'></a>'; 
                        $edit_str = '<a href= "'.BASE_URL.$dataArray['objectname'].'/edit/id/{{id}}" name="{{id}}" class="sprite edit"  title=\'Edit\'></a>';
                        $delete_str = '<a name="{{id}}" onclick= changestatus(\''.$dataArray['objectname'].'\',\'{{id}}\',\''.$msgdta.'\')	href= javascript:void(0) title=\'Delete\' class="sprite delete" ></a>';
			
						if(!in_array('view',$actions_arr) && !in_array('edit',$actions_arr) && !in_array('delete',$actions_arr))
						{
						  $extra['action'] =array(); 
						}else
						{
						  $extra['action'] = array('name' => 'edit', 'value' =>'<div class="grid-action-align">
										'.((in_array('view',$actions_arr)?$view_str:'')).'
										'.((in_array('edit',$actions_arr)?$edit_str:'')).'
										'.((in_array('delete',$actions_arr)?$delete_str:'')).'
									</div>'); 
						}		
						if($dataArray['objectname'] ==  'shortlistedcandidates')
						{
							
						 $view_str = '<a href= "'.BASE_URL.$dataArray['objectname'].'/view/id/{{id}}" name="{{id}}" class="sprite view"  title=\'View\'></a>'; 
						$edit_str = '<a href= "'.BASE_URL.$dataArray['objectname'].'/edit/id/{{id}}" name="{{id}}" id="edit{{id}}" class="sprite edit"  title=\'Edit\'></a>';
						$delete_str = '<a name="{{id}}" onclick= changestatus(\''.$dataArray['objectname'].'\',\'{{id}}\',\''.$msgdta.'\')	href= javascript:void(0) title=\'Delete\' class="sprite delete" ></a>';
					
							$auth = Zend_Auth::getInstance();
							if($auth->hasIdentity())
							{
								$loginUserId = $auth->getStorage()->read()->id;
								$loginuserGroup = $auth->getStorage()->read()->group_id;
								$loginuserRole = $auth->getStorage()->read()->emprole;
							}
							if($loginuserRole == SUPERADMINROLE || $loginuserGroup == HR_GROUP || $loginuserGroup == MANAGEMENT_GROUP)
							{
								$employee = '<a href= "'.BASE_URL.'employee'.'/add/cid/{{id}}" name="{{id}}" class="sprite add_candidate_as_an_employee"  title=\'Add candidate as an employee\' id="employee{{id}}"></a>';
								$extra['action'] = array('name' => 'edit', 'value' =>'<div class="grid-action-align">
										'.((in_array('view',$actions_arr)?$view_str:'')).'
										'.((in_array('edit',$actions_arr)?$edit_str:'')).'
										'.((in_array('delete',$actions_arr)?$delete_str:'')).'
								     	'.((in_array('candidateemployee',$actions_arr)?'':$employee)).'
									</div>');
							}
							else
							{
								
								$extra['action'] = array('name' => 'edit', 'value' =>'<div class="grid-action-align">
										'.((in_array('view',$actions_arr)?$view_str:'')).'
										'.((in_array('edit',$actions_arr)?$edit_str:'')).'
										'.((in_array('delete',$actions_arr)?$delete_str:'')).'
									</div>');
							}
							
						}	
						if($dataArray['objectname'] ==  'requisition')
						{
							$urls= "'".BASE_URL.'requisition/addpopup/id/{{id}}'."'";
						    $name="'"."Requisition"."'";
							$approvereject_str = '<a onclick="displaydeptform('.$urls.','.$name.')" name="{{id}}" id= "approvereject{{id}}" class="fa fa-ellipsis-v" title=\'Approve or Reject\'></a>';
			
							$extra['action'] = array('name' => 'edit', 'value' =>'<div class="grid-action-align">
										'.((in_array('view',$actions_arr)?$view_str:'')).'
										'.((in_array('edit',$actions_arr)?$edit_str:'')).'
										'.((in_array('delete',$actions_arr)?$delete_str:'')).'
								     	'.((in_array('approvereject',$actions_arr)?'':$approvereject_str)).'
									</div>');
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
		if(isset($dataArray['actns'])) $actns = $dataArray['actns']; else $actns = '';
		if(isset($dataArray['unitId'])) $unitId = $dataArray['unitId'];
		return $this->generateGrid($dataArray['objectname'],$dataArray['tableheader'],$paginator,$extra,true,$dataArray['jsGridFnName'], $dataArray['perPage'],$dataArray['pageNo'],$dataArray['jsFillFnName'],$dataArray['searchArray'],$formgridVal,$addaction,$menuName,$unitId,$addpermission,$actns,$menunamestr,isset($dataArray['call'])?$dataArray['call']:"",$sortStr,isset($dataArray['search_filters'])?$dataArray['search_filters']:"",isset($dataArray['dashboardcall'])?$dataArray['dashboardcall']:"No",$actnArr,isset($dataArray['sort'])?$dataArray['sort']:"",isset($dataArray['by'])?$dataArray['by']:"");
		
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

	public function generateGrid ($name, $fields = null,$paginator=null,$extracolumn=array(),$sorting=false,$jsGridFnname='', $perPage='5',$page='1', $jsFillFnName='',$searchArray='',$formgrid='false',$addaction='',$menuName='',$unitId,$addpermission,$actns='',$menunamestr,$call='',$sortStr='',$search_filters = '',$dashboardCall = 'No',$actnArr,$sortname='',$by='') {
        
		$request = Zend_Controller_Front::getInstance();
        $params = $request->getRequest()->getParams();		
        $menu_model = new Default_Model_Menu();
        $controllerName = $request->getRequest()->getControllerName();
        // Store Extra Columns
		$this->extra = $extracolumn;$sortIconStr = "";

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
		
		
		if($addaction !='')
		{
		  $action = $addaction;
		  $popupaction = 'addpopup';
		} else
		{
		  $action = "edit";
		  $popupaction = 'editpopup';
		}
		
		$con ='';
		if($formgrid != '')
		{
			
			if($unitId != '')
			$con = 'unitId/'.$unitId;
			else
			$con = 'unitId/'.$params['id'];
			$output ="<div class='table-header'><span>".$menuName."</span><input type='button' title = 'Add' onclick='displaydeptform(\"".BASE_URL.$name.'/'.$popupaction."/$con/popup/1\",\"".$menunamestr."\")' value='Add Record' class='sprite addrecord' /></div>";
		}
		else
		{
			$menuName = $menu_model->getMenuText($menuName);
            
    		$output ="<div class='table-header'><span>".$menuName."</span><input type='button' title = 'Add'  onclick='window.location.href=\"".BASE_URL.$name.'/'.$action."\"' value='Add Record' class='sprite addrecord' /></div>";
		} 
		
		if($addpermission == 'false')
		{	
		
			$output ="<div class='table-header'><span>".$menuName."</span></div>";
		}
		if($actns != '' && $actns == 'remove')
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
				
				if(isset($value['align'])) $align = (@$value['align'] != '')? 'align="'.$value['align'].'" ':'';
				if(isset($value['sortkey']))$sortkey = (@$value['sortkey'] != '')? 'align="'.$value['sortkey'].'" ':'';
				
				if(isset($value['style']))$style = (@$value['style'] != '')? 'style="'.$value['style'].'" ':'';
				
				$value = (is_array($value) && !isset($value['sortkey']))? $value['value']:$value;	
				if($value == 'Action') $width = 'width=90'; else $width =  '';
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
							$output .= "<a href='javascript:void(0);' onclick=javascript:paginationndsorting('".BASE_URL.$name."/index/sort/".$sort."/by/".$key."/objname/".$name."/page/".$page."/per_page/".$perPage."/call/ajaxcall/$con/');>".$value."</a>";
							//For Sort Icons....
								if($sortStr == $key)
								$output .= $sortIconStr;
						}
						else if($welcome == 'true')
						{
							$output .= "<a href='javascript:void(0);' onclick=javascript:paginationndsorting('".BASE_URL.$name."/index/sort/".$sort."/by/".$key."/objname/".$name."/page/".$page."/per_page/".$perPage."/call/ajaxcall/$con/');>".$value."</a>";
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
			}//end of for each function
                        $output .= "</tr><tr id='search_tr_$name'>";
                        $tabindx = 0;
			foreach ($fields as $key => $value) {
				
				if(isset($value['align'])) $align = (@$value['align'] != '')? 'align="'.$value['align'].'" ':'';
				if(isset($value['sortkey']))$sortkey = (@$value['sortkey'] != '')? 'align="'.$value['sortkey'].'" ':'';
				
				if(isset($value['style']))$style = (@$value['style'] != '')? 'style="'.$value['style'].'" ':'';
				
				$value = (is_array($value) && !isset($value['sortkey']))? $value['value']:$value;	
				if($value == 'Action') $width = 'width=90'; else $width =  '';
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
							
							if(!empty($searchArray)) $display = 'display: block;'; else $display = 'display: none;';
							if(is_array($searchArray)) { if(array_key_exists($key,$searchArray)) $sText = $searchArray[$key]; else $sText = ''; }
							
                                                        
                                                        if(isset($search_filters[$key]))
                                                        {
                                                            
                                                            $search_function = 'getsearchdata("'.$name.'","","'.$key.'",event';
                                                            $output .= sapp_Global::grid_data($search_filters,$key,$name,$display,$sText,$tabindx,$search_function);
                                                            
                                                        }
                                                        else
							$output .= "<input tabIndex=$tabindx type='text' name='$name' id='$key' style='$display' class='searchtxtbox_$name table_inputs grid_search_inputs' value='$sText' onkeydown='getsearchdata(\"$name\",\"\",\"$key\",event,\"text\")' />";
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
			}//end of for each function
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
							$valToInclude = (strlen(trim($p[$k]))>$characterlimit)? substr(trim($p[$k]),0,$characterlimit)."..":trim($p[$k]);
							$output .= "<a onclick= ".$jsFillFnName."(\"/id/$p[id]\") href= 'javascript:void(0)' title='".addslashes (htmlspecialchars(strip_tags (trim($p[$k]))))."' >".addslashes (htmlspecialchars(strip_tags ($valToInclude)))."</a>";
						}
						else{
							
							$p = (array)$p;
							if($controllerName == 'shortlistedcandidates')
							{
								$candidatesmodel = new Default_Model_Candidatedetails();
								$candidateData = $candidatesmodel->getcandidateData($p['id']);
								if($p['cand_status'] != 'Selected' || $candidateData['backgroundchk_status'] == 'Not Applicable' || $candidateData['backgroundchk_status'] == 'In process' || $candidateData['backgroundchk_status'] == 'On hold')
								{

									echo "<script type='text/javascript'>
													$(document).ready(function() {
								
													$('#employee'+".$p['id'].").remove();
													});
													</script>";
								}
								if($p['cand_status'] == 'Selected' || $p['cand_status'] == 'Rejected')
								{

									echo "<script type='text/javascript'>
													$(document).ready(function() {
								
													$('#edit'+".$p['id'].").remove();
													});
													</script>";
								} 
							}
							
							if($controllerName == 'requisition' || $controllerName == 'approvedrequisitions' )
							{
								$auth = Zend_Auth::getInstance();
								if($auth->hasIdentity())
								{
									$loginUserId = $auth->getStorage()->read()->id;
								}
								$requi_model = new Default_Model_Requisition();
								$dataforapprovereject = $requi_model->getRequisitionForEdit($p['id'],$loginUserId);
								$aorder=$dataforapprovereject['aorder'];
								$status="";
								if(!empty($aorder))
								{
								 $status=$dataforapprovereject['appstatus'.$aorder];
								}
								if($dataforapprovereject['aflag'] != 'approver' ||  $status != 'Initiated')		
								{
								echo "<script type='text/javascript'>
												$(document).ready(function() {
							
												$('#approvereject'+".$p['id'].").remove();
												});
												</script>";
								}
								if( $dataforapprovereject['req_status'] == 'Complete' || $dataforapprovereject['req_status'] == 'Closed' || $dataforapprovereject['req_status'] == 'On hold')
								{
									echo "<script type='text/javascript'>
												$(document).ready(function() {
				
												$('#addemployee'+".$p['id'].").remove();
												});
												</script>";
								}
								if( $dataforapprovereject['req_status'] == 'Complete')
								{
									echo "<script type='text/javascript'>
												$(document).ready(function() {
				
												$('#edit'+".$p['id'].").remove();
												});
												</script>";
								}
							
							}
							if(isset($p[$k])) {
							 $valToInclude = (strlen(trim($p[$k]))>$characterlimit)? substr(trim($p[$k]),0,$characterlimit)."..":trim($p[$k]);
							
                                                         $output .= "<span  title='".htmlentities(addslashes(trim($p[$k])), ENT_QUOTES, "UTF-8")."' >".htmlentities(addslashes($valToInclude), ENT_QUOTES, "UTF-8")."</span>";
							}
							
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
                                        
                                        $('#search_tr_".$name."').hide();	
                                    });
                </script>                     
</div>";
		
		// Attach Pagination
		if($paginator) {
		

			
			$params = array();
			$params['jsGridFnName'] = $jsGridFnname;
			$params['perPage'] = $perPage;
			$params['objname'] = $name;
			$params['searchArray'] = $searchArray;			
			$params['formgrid'] = $formgrid;
			$params['con'] = $con;
			$params['sortname'] = $sortname;
			$params['by'] = $by;
			$params['dashboardcall'] = $dashboardCall;
			
			$output.= $this->view->paginationControl($paginator,

                    'Sliding',

                    'partials/pagination.phtml',$params);

			
		}
		$output .= "<script type='text/javascript'>$('#$name').slimScrollHorizontal({
									  alwaysVisible: false,
									  start: 'left',
									  position: 'bottom',
									 
									}).css({ background: '#ccc', paddingBottom: '10px' }); </script>";
		$output .= "<script type='text/javascript'>
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
