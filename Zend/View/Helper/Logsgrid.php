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

class Zend_View_Helper_Logsgrid extends Zend_View_Helper_Abstract {

	public $view = null;

	public $extra = array();

	private $output; // Container to hold the Grid

	public function setView(Zend_View_Interface $view) {

		$this->view = $view;

		return $this;

	}

	public function logsgrid ($dataArray)
	{
	    $request = Zend_Controller_Front::getInstance();
        $params = $request->getRequest()->getParams();		
		$view = Zend_Layout::getMvcInstance()->getView();
		$menu_model = new Default_Model_Menu();
		$session=new Zend_Auth_Storage_Session();
		$data=$session->read();
		$role_id = $data['emprole'];
		$menunamestr = '';$sortStr ='';
		$sortStr = $dataArray['by'];
		$controllers_arr = $menu_model->getControllersByRole($role_id);
		$actionsobjname = $dataArray['objectname'];
		if(isset($controllers_arr[$actionsobjname."controller.php"]))
		{
			$actions_arr = array();
			
			$menuName = '';

		}
		else
		$actions_arr = array();
			
		
		$gridFieldsArr=array();$tmpActionsArr=array();
		$tmpActionsArr = $actions_arr;
		array_pop($tmpActionsArr);	//last element of actions array is menuname so delete that & check the privileges are empty or not...
		

		if(empty($tmpActionsArr))
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

		if(isset($dataArray['formgrid']) && $dataArray['formgrid'] == 'true')
		{
			
			
		    if(isset($dataArray['unitId']))
				$con = '/unitId/'.$dataArray['unitId'];
			else
			{
				if(isset($params['id']))
				$con = '/unitId/'.$params['id'];
			}

			$formgridVal = $dataArray['formgrid'];

			if($dataArray['objectname'] == 'departments'){
				$viewaction = 'view';
			}else			{
				$viewaction = 'viewpopup';
			}
			$editaction = 'editpopup';
			if(isset($dataArray['menuName']) && $dataArray['menuName'] !='')
			$menunamestr = $dataArray['menuName'];

			$viewpopup_str = '';
			$editpopup_str = '';
			$deletepopup_str = '';


			if(!in_array('view',$actions_arr) && !in_array('edit',$actions_arr) && !in_array('delete',$actions_arr))
			{
				if($dataArray['objectname'] == 'processes')
				{

					$extra['action'] = array('name' => 'edit', 'value' =>'<div class="grid-action-align">
								<a onclick="displaydeptform(\''.BASE_URL.$dataArray['objectname'].'/'.$viewaction.'/id/{{id}}'.$con.'/popup/1\',\''.$menunamestr.'\')" name="{{id}}" class="sprite view"  title=\'View\'></a>
								<a onclick="displaydeptform(\''.BASE_URL.$dataArray['objectname'].'/'.$editaction.'/id/{{id}}'.$con.'/popup/1\',\''.$menunamestr.'\')" name="{{id}}" class="sprite edit"  title=\'Edit\' ></a>
								<a name="{{id}}" id="{{id}}" onclick= changestatus(\''.$dataArray['objectname'].'\',\'{{id}}\',\''.$msgdta.'\')	href= javascript:void(0) title=\'Delete\' class="sprite delete" ></a>
							</div>'); 
				}
				else
				$extra['action'] =array();
			}else
			{

				if($dataArray['objectname'] ==  'empleavesummary' || $dataArray['objectname'] ==  'empscreening')
				{
					$view_str = '<a href= "'.BASE_URL.$dataArray['objectname'].'/view/id/{{id}}" name="{{id}}" class="sprite view"  title=\'View\'></a>';
					$edit_str = '<a href= "'.BASE_URL.$dataArray['objectname'].'/edit/id/{{id}}" name="{{id}}" class="sprite edit"  title=\'Edit\'></a>';
					$delete_str = '<a name="{{id}}" onclick= changestatus(\''.$dataArray['objectname'].'\',\'{{id}}\',\''.$msgdta.'\')	href= javascript:void(0) title=\'Delete\' class="sprite delete" ></a>';
					$extra['action'] = array('name' => 'edit', 'value' =>'<div class="grid-action-align">
										'.((in_array('view',$actions_arr)?$view_str:'')).'
										'.((in_array('edit',$actions_arr)?$edit_str:'')).'
										'.((in_array('delete',$actions_arr)?$delete_str:'')).'
									</div>');
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
			$view_str = '';
			$edit_str = '';
			$delete_str = '';

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
		if(isset($dataArray['unitId'])) $unitId = $dataArray['unitId'];

		return $this->generateGrid($dataArray['objectname'],$dataArray['tableheader'],$paginator,$extra,true,$dataArray['jsGridFnName'], $dataArray['perPage'],$dataArray['pageNo'],$dataArray['jsFillFnName'],$dataArray['searchArray'],$formgridVal,$addaction,$menuName,$unitId,$addpermission,$menunamestr,isset($dataArray['call'])?$dataArray['call']:"",$sortStr,isset($dataArray['search_filters'])?$dataArray['search_filters']:"",isset($dataArray['dashboardcall'])?$dataArray['dashboardcall']:"No",isset($dataArray['sort'])?$dataArray['sort']:"",isset($dataArray['by'])?$dataArray['by']:"");

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

	public function generateGrid ($name, $fields = null,$paginator=null,$extracolumn=array(),$sorting=false,$jsGridFnname='', $perPage='5',$page='1', $jsFillFnName='',$searchArray='',$formgrid='false',$addaction='',$menuName='',$unitId,$addpermission,$menunamestr,$call='',$sortStr='',$search_filters = '',$dashboardCall = 'No',$sortname='',$by='') {

		$request = Zend_Controller_Front::getInstance();
        $params = $request->getRequest()->getParams();	
		$viewLinkArray = array('Cities','States');
			
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
			
		if($call != "ajaxcall")		$sortIconStr = "";

		

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
			$urlString = $_SERVER['REQUEST_URI'];
			$urlData = explode('/',$urlString);
			if($unitId != '')
			$con = 'unitId/'.$unitId;
			else if(sizeof($urlData) > 4)
			{
				
				
			    if(isset($params['id']))
				   $con = 'unitId/'.$params['id'];
			}
			
			if($name == 'empscreening')
			{
				$empaction = 'add';
				$output ="";
			}
			else
			$output ="";
		}
		else
		{
			$output ="";
		}

		if($addpermission == 'false')
		{

			$output ="<div class='table-header'><span>".$menuName."</span></div>";
		}
		$output .="<div id='".$name."' class='details_data_display_block newtablegrid'>";
		$output .= "<table class='grid' align='center'  width='100%' cellspacing='0' cellpadding='4' border='0'><thead><tr>";
		// this foreach loop display the column header  in ?th? tag.

		$colinr = 0;
		if(!empty($fields))
		{
			$tabindx = 0;
			foreach ($fields as $key => $value) {
				
				if($value != 'Profile'  && $value != 'ID' && $value != 'Url'){
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
								$output .= "<a href='javascript:void(0);' onclick=javascript:paginationndsorting('".BASE_URL.$name."/index/sort/".$sort."/by/".$key."/objname/".$name."/page/".$page."/per_page/".$perPage."/call/ajaxcall/$con/');>".$value."</a>";
								//For Sort Icons....
								if($key == $sortStr)
								$output .= $sortIconStr;
							}
							else if($welcome == 'true')
							{
								$output .= "<a href='javascript:void(0);' onclick=javascript:paginationndsorting('".BASE_URL.$name."/index/sort/".$sort."/by/".$key."/objname/".$name."/page/".$page."/per_page/".$perPage."/call/ajaxcall/$con/');>".$value."</a>";
								//For Sort Icons....
								if($key == $sortStr)
								$output .= $sortIconStr;
							}
							else
							{
								$output .= "<a href='javascript:void(0);' onclick=javascript:paginationndsorting('".$this->view->url(array('sort'=>$sort,'by'=>$key,'objname'=> $name,'page' => $page,'per_page'=>$perPage))."');>".$value."</a>";
								//For Sort Icons....
								if($key == $sortStr)
								$output .= $sortIconStr;
							}

						}
					}  else {
						//For Sort Icons....
						if($key == $sortStr)
						$output .= $sortIconStr;
						$output .= $value;

					}

					$output .= "</th>";
					$colinr++;
					$tabindx++;
				}
			}//end of for each loop
			$output .= "</tr><tr id='search_tr_$name'>";
			$tabindx = 0;
			foreach ($fields as $key => $value) {
				if($key != 'profileimg' && $key != 'id' && $key != 'menuUrl'){
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
								if(is_array($searchArray)) { if(array_key_exists($key,$searchArray)) $sText = urldecode($searchArray[$key]); else $sText = ''; }
								
								if(isset($search_filters[$key]))
								{
									$search_function =  'getsearchdata("'.$name.'","",this.id,event';
									$output .= sapp_Global::grid_data($search_filters,$key,$name,$display,$sText,$tabindx,$search_function);
								}
								else{
									if($key != 'key_flag'){
										$output .= "<input tabIndex=$tabindx type='text' name='$name' id='$key' style='$display' class='searchtxtbox_$name table_inputs grid_search_inputs' value='$sText' onkeydown='getsearchdata(\"$name\",\"\",this.id,event,\"text\")' />";
									}


								}
							}
						}
					}  else {
						//For Sort Icons....
						if($key == $sortStr)
						$output .= $sortIconStr;
						$output .= $value;

					}

					$output .= "</th>";
					$colinr++;
					$tabindx++;
				}
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
					$imgtag = '';
					if($k == 'profileimg' || $k == 'id' || $k == 'menuUrl'){
						continue;
					}
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
						if($k == 'description' && $menuName == 'Screening Types')
						$characterlimit = 80;
						$output .= "<td {$tdclass}>";
					}
					// Check to see if this Field is in Extra Columns
					if(isset($this->extra[$k]['value'])) {
						$output .= $this->_parseExtra($k,$p);
					} else {
						if( $bodyCount== 0 && $jsFillFnName != '')
						{
							$valToInclude = (strlen(trim($p[$k]))>$characterlimit)? substr(trim($p[$k]),0,$characterlimit)."..":trim($p[$k]);
							
							$output .= "<a onclick= ".$jsFillFnName."(\"/id/$p[id]\") href= 'javascript:void(0)' title='".htmlentities (trim($p[$k]), ENT_QUOTES, "UTF-8")."' >".  htmlentities ($valToInclude, ENT_QUOTES, "UTF-8")."</a>";
						}
						else{

							$p = (array)$p;
							if(isset($p[$k])) {

							 $valToInclude = (strlen(trim($p[$k]))>$characterlimit)? substr(trim($p[$k]),0,$characterlimit)."..":trim($p[$k]);
							 	
							 if($k == 'user_action'){
							 	switch($p[$k]){
							 		case '1':
							 			$valToInclude = 'ADD';
							 			break;
							 		case '2':$valToInclude = "Edit";break;
							 		case '3':$valToInclude = "Delete";break;
							 	}
							 }
							 else if($k == 'userfullname'){
							 	$errorImg = DOMAIN.'public/media/images/profile_pic.png';
							 	if(isset($p['profileimg']) && !empty($p['profileimg'])){
							 		$imageUrl = DOMAIN.'public/uploads/profile/'.$p['profileimg'];
							 	}else{
							 		$imageUrl = DOMAIN.'public/media/images/profile_pic.png';
							 	}
							 	$imgtag ='<img src="'.$imageUrl.'" onerror="this.src=\''.$errorImg.'\'" class="pic-grid">';
							 }
							 else if($k == 'last_modifieddate' || $k == 'logindatetime'){
							 	$valToInclude = sapp_Global::getDisplayDate($valToInclude);
							 }
							 else if($k == 'empipaddress' && $valToInclude == '::1'){
							 	$valToInclude = '127.0.0.1';
							 }

							 if($k == 'status' && $p[$k] == 'Complete' && $menuName == 'Employee Screening') $dataclass = 'class="greendata"'; else $dataclass = '';
							 
							 if($k == 'user_action'){
							 	switch($p[$k]){
							 		case '1':$menunamestr = $p['menuName'].' - Add';
							 		$output.= "<a  name='' class=''  title='Add' onclick='displaydeptform(\"".BASE_URL."logmanager/view/id/".$p['id']."\",\"".$menunamestr."\")'>Add</a>";
							 			
							 		break;
							 		case '2':$menunamestr = $p['menuName'].' - Edit';
							 		$output.= "<a  name='' class=''  title='Edit' onclick='displaydeptform(\"".BASE_URL."logmanager/view/id/".$p['id']."\",\"".$menunamestr."\")'>Edit</a>";
							 		break;
							 		case '3':$valToInclude = "Delete";
							 		$output .= " <span ".$dataclass." title='Delete' class='emp-name' >".htmlentities($valToInclude, ENT_QUOTES, "UTF-8")."</span>";		break;
							 		case '5':$valToInclude = "Cancel";
							 		$output .= " <span ".$dataclass." title='Cancel' class='emp-name' >".htmlentities($valToInclude, ENT_QUOTES, "UTF-8")."</span>";		break;
							 	}
							 }else if($k == 'key_flag'){
							 	$modurl = ltrim($p['menuUrl'],'/');
							 	if($p['user_action'] != 3  && $p['user_action'] != 5 && $p['menuName'] != 'Leave Request' && $p['menuName'] != 'Manage Employee Leaves' && $p['menuName'] != 'Manage Modules'){
							 		if(in_array($p['menuName'],$viewLinkArray)){
							 			$urlink = BASE_URL.$modurl.'/view/id/'.$p[$k];	
							 		}
							 		else{
							 			$urlink = BASE_URL.$modurl.'/edit/id/'.$p[$k];
							 		}
							 		$output .= '<a href= "'.$urlink.'" target="_blank" name="" class=""  title="View Record">View Record</a>';
							 	}else if($p['menuName'] == 'Manage Modules'){ 
							 	  $output .= "<span ".$dataclass." title=".$p[$k]." class='emp-name' >".htmlentities($p[$k], ENT_QUOTES, "UTF-8")."</span>";
							 	}else{
							 		$output .= '';
							 	}
							 }else{
							     if($k == 'last_modifieddate' || $k == 'logindatetime'){
							         $output .= $imgtag." <span ".$dataclass." title='".htmlentities($valToInclude, ENT_QUOTES, "UTF-8")."' class='emp-name' >".htmlentities($valToInclude, ENT_QUOTES, "UTF-8")."</span>";
							     }else{
							 	     $output .= $imgtag." <span ".$dataclass." title='".htmlentities($p[$k], ENT_QUOTES, "UTF-8")."' class='emp-name' >".htmlentities($valToInclude, ENT_QUOTES, "UTF-8")."</span>";
							     }
							 }
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
			$params['dashboardcall'] = $dashboardCall;
			$params['sortname'] = $sortname;
			$params['by'] = $by;

			$output.= $this->view->paginationControl($paginator,

                    'Sliding',

                    'partials/pagination.phtml',$params);

			
		}
		$output .= "<script type='text/javascript' language='javascript'>$('#$name').slimScrollHorizontal({
									  alwaysVisible: false,
									  start: 'left',
									  position: 'bottom',
									 
									}).css({ background: '#ccc', paddingBottom: '10px' }); </script>";
		$output .= "<script type='text/javascript' language='javascript'>
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
