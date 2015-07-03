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

class Zend_View_Helper_Servicegrid extends Zend_View_Helper_Abstract 
{
    public $view = null;
    public $extra = array();
    private $output; // Container to hold the Grid
    
    public function servicegrid($dataArray)
    {                                		                                
        $actnArr = array();
        $sortStr = isset($dataArray['by'])?$dataArray['by']:"";
        		                					
        if(isset($dataArray['menuName']) && $dataArray['menuName'] != '')
            $menuName = $dataArray['menuName'];
        $gridFieldsArr=array();$tmpActionsArr=array();
        
        array_pop($tmpActionsArr);	//last element of actions array is menuname so delete that & check the privileges are empty or not...
		
        $actnArr = $tmpActionsArr;
        if(($key = array_search('add', $actnArr)) !== false) 
        {
            unset($actnArr[$key]);
        }
        if(empty($tmpActionsArr))	
        {
            unset($gridFieldsArr['action']);
            $gridFieldsArr = $dataArray['tableheader'];            
            $dataArray['tableheader']=$gridFieldsArr;
        }		
		           
        if(isset($dataArray['menuName']))
            $menuName = $dataArray['menuName'];	
				
        $page_adapter = new Zend_Paginator_Adapter_DbSelect($dataArray['tablecontent']);
        
        $page_adapter->setRowCount($dataArray['row_count']);
        $paginator = new Zend_Paginator($page_adapter);                
                
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
                                		
        $formgridVal = '';                                            
        $extra['action'] = array();
                                                    
        $extra['options'] = array(); 
        $addaction= '';  		
        if(isset($dataArray['add']) && $dataArray['add'] !='')
        {
            $addaction = $dataArray['add'];
            $addpermission = "true";
        }
        else
        {
            $addaction = '';
            $addpermission = "false";
        }
        	
        return $this->generateGrid($dataArray,$paginator,$extra,true,$formgridVal,$addaction,$menuName,$addpermission,
                $sortStr,$actnArr);		
    }
	
    /**
     *
     * @param string $name
     * @param array $fields
     * @param Zend_Paginator_Instance $paginator
     * @param array $extracolumn
     * @param Bool  $sorting
     *
     * @return string
     */

    public function generateGrid($dataArray,$paginator,$extracolumn=array(),$sorting=false,$formgrid='false',$addaction='',$menuName='',$addpermission,$sortStr='',$actnArr) 
    {        
        $view_link = isset($dataArray['view_link'])?$dataArray['view_link']:"";
        $name = isset($dataArray['objectname'])?$dataArray['objectname']:"";
        $by = isset($dataArray['by'])?$dataArray['by']:"";        
        $search_filters = isset($dataArray['search_filters'])?$dataArray['search_filters']:array();
        $fields = isset($dataArray['tableheader'])?$dataArray['tableheader']:array();
        $searchArray = isset($dataArray['searchArray'])?$dataArray['searchArray']:array();
        $jsGridFnname = isset($dataArray['jsGridFnName'])?$dataArray['jsGridFnName']:"";
        $perPage = isset($dataArray['perPage'])?$dataArray['perPage']:"20";
        $page = isset($dataArray['pageNo'])?$dataArray['pageNo']:"1";
        $call = isset($dataArray['call'])?$dataArray['call']:"";
        $dashboardCall = isset($dataArray['dashboardcall'])?$dataArray['dashboardcall']:"No";                
        $sortname = isset($dataArray['sort'])?$dataArray['sort']:"";
        $grid_type = isset($dataArray['grid_type'])?$dataArray['grid_type']:"";
        $status_value = isset($dataArray['status_value'])?$dataArray['status_value']:"";
        $add_link = isset($dataArray['add_link'])?$dataArray['add_link']:"";
        
        $request = Zend_Controller_Front::getInstance();
        $params = $request->getRequest()->getParams();		
                
        $dataclass = '';
		// Store Extra Columns
        $this->extra = $extracolumn;
        
        $sortIconStr = "";

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
			
        if($call != "ajaxcall")		
            $sortIconStr = "";
				
        if($addaction !='')
        {
            $action = $addaction;            
        }         
		
        $con ='';
        
        $output ="<div class='table-header'><span>".$menuName."</span>";  
        if($addaction !='')
        {
            $output .= "<div class='add-btn-div'>";
            if($add_link != '')
                $output .= "<input type='button' title = 'Raise a Request' onclick='window.location.href=\"".$add_link."\"' value='Raise a Request' class='sprite addrequest' />";
            else 
                $output .= "<input type='button' title = 'Raise a Request' onclick='window.location.href=\"".BASE_URL.$name.'/'.$action."\"' value='Raise a Request' class='sprite addrequest' />";
            $output .= "</div>";
        }
        $output .= "</div>";
        
		
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
            foreach ($fields as $key => $value) 
            {				
                if(isset($value['align'])) 
                    $align = ($value['align'] != '')? 'align="'.$value['align'].'" ':'';
                if(isset($value['sortkey']))$sortkey = ($value['sortkey'] != '')? 'align="'.$value['sortkey'].'" ':'';
				
                if(isset($value['style']))$style = ($value['style'] != '')? 'style="'.$value['style'].'" ':'';
				
                $value = (is_array($value) && !isset($value['sortkey']))? $value['value']:$value;	
                if($value == 'Action') $width = 'width=90'; else $width =  '';
                $output .= "<th ".$width.">";
                // Check if Sorting is set to True
                if($sorting) 
                {
					// Disable Sorting if Key is in Extra Columns
                    if(@$this->extra[$key]['name'] != '' && !is_array($value)) 
                    {
                        if($value == "Action")	
                            $output .= "<span class='action-text'>Action</span>";
                        else
                            $output .= $value;						
                    } 
                    else 
                    {
                        if(is_array($value))
                        {
                            $key = $value['sortkey'];
                            $value = $value['value'];
                        } 
                        $welcome = 'false';
                        $urlString = $_SERVER['REQUEST_URI'];
                        if (strpos($urlString,'welcome') !== false) 
                        {
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
                }  
                else 
                {
					//For Sort Icons....
                    if($key == $sortStr)
                        $output .= $sortIconStr;
                    $output .= $value;
                }

                $output .= "</th>";
                $colinr++;
                $tabindx++;
            }//end of for each loop
            $output .= "</tr><tr id='search_tr_$name'>";
            $tabindx = 0;
            foreach ($fields as $key => $value) 
            {				
                if(isset($value['align'])) $align = (@$value['align'] != '')? 'align="'.$value['align'].'" ':'';
                if(isset($value['sortkey']))$sortkey = (@$value['sortkey'] != '')? 'align="'.$value['sortkey'].'" ':'';				
                if(isset($value['style']))$style = (@$value['style'] != '')? 'style="'.$value['style'].'" ':'';
				
                $value = (is_array($value) && !isset($value['sortkey']))? $value['value']:$value;	
                if($value == 'Action') $width = 'width=90'; else $width =  '';
                $output .= "<th ".$width.">";
				// Check if Sorting is set to True
                if($sorting) 
                {
                    // Disable Sorting if Key is in Extra Columns
                    
                    if(@$this->extra[$key]['name'] != '' && !is_array($value)) 
                    {
                        if($value == "Action")	
                            $output .= "<span class='action-text'></span>";
                        else
                            $output .= $value;						
                    } 
                    else 
                    {
                        if(is_array($value))
                        {
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
                                $search_function =  'getsearchdata("'.$name.'","",this.id,event';
                                $output .= sapp_Global::grid_data($search_filters,$key,$name,$display,$sText,$tabindx,$search_function);
                            }
                            else
                                $output .= "<input tabIndex=$tabindx type='text' name='$name' id='$key' style='$display' class='searchtxtbox_$name table_inputs grid_search_inputs' value='$sText' onkeydown='getsearchdata(\"$name\",\"\",this.id,event,\"text\")' />";
                        }
                    }
                }  
                else 
                {
					//For Sort Icons....
                    if($key == $sortStr)
                        $output .= $sortIconStr;
                    $output .= $value;
                }

                $output .= "</th>";
                $colinr++;
                $tabindx++;
            }//end of for each loop
        }
        if($grid_type != '')
        {
            $output .= "<input type='hidden' id='service_grid' value='".sapp_Global::_encrypt($grid_type)."' />";
        }
        if($status_value != '')
        {
            $output .= "<input type='hidden' id='service_grid_status' value='".sapp_Global::_encrypt($status_value)."' />";
        }
        $output .= "</tr></thead>";
        $output .="<tbody>";

		// Start Looping Data
        $ii=0;
            
        foreach($paginator as $p) 
        {
            
            $cell_color = ($ii % 2 == 0 ? "row1" : "row2");
            $ii++;$bodyCount = 0;
            
            $output.="<tr onclick='window.location=\"".$this->_parseString($view_link, $p)."\"' class='$cell_color cursor'>";
			// Reset Fields Array to Top
            if(!empty($fields)) 
            { 
                reset($fields); 
                foreach($fields AS $k=>$v) 
                {
                    $tdclass = '';
					// Look for additional attributes
                    $characterlimit = 40;
                    if(is_array($v)) 
                    {
                        $class = (@$v['class'] != '')? 'class="'.$v['class'].'" ':'';
                        $align = (@$v['align'] != '')? 'align="'.$v['align'].'" ':'';
                        $valign = (@$v['valign'] != '')? 'valign="'.$v['valign'].'" ':'';
                        if(isset($v['characterlimit']))
                            $characterlimit = $v['characterlimit'];
                        $output .= "<td {$tdclass}{$align}{$valign}>";
                    } 
                    else 
                    {
                        if($k == 'description' && $menuName == 'Screening Types')
                            $characterlimit = 80;
                        $output .= "<td {$tdclass}>";
                    }
					// Check to see if this Field is in Extra Columns
                    if(isset($this->extra[$k]['value'])) 
                    {
                        $output .= $this->_parseExtra($k,$p);
                    } 
                    else 
                    {					
                                                                          
                            $p = (array)$p;
                            if(isset($p[$k])) 
                            {
                                $valToInclude = (strlen(trim($p[$k]))>$characterlimit)? substr(trim($p[$k]),0,$characterlimit)."..":trim($p[$k]);								
                                                                									 	                               
                                $output .= "<span ".$dataclass." title='".htmlentities(trim($p[$k]), ENT_QUOTES, "UTF-8")."' >".htmlentities($valToInclude, ENT_QUOTES, "UTF-8")."</span>";	 	                            												
								// Customize grid fields data - END							
                            }
                        
                    }
                    $dataclass = '';
                    $output .= "</td>";
                    $bodyCount++;
                }
            }
			// Close the Table Row
            $output.="</tr>";
        }
        if($ii == 0)
        {
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
        if($paginator) 
        {		
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
    public function _parseExtra($column,$p) 
    {
        if(isset ($this->extra[$column])) 
        {
            $val = '';

            $characterlimit = 15;
            if(isset($this->extra[$column]['characterlimit']))
                $characterlimit = $this->extra[$column]['characterlimit'];
            preg_match_all('/\{\{(.*?)\}\}/', $this->extra[$column]['value'], $matches);
            if(count($matches[1]) > 0) 
            {
                $matches[1] = array_unique($matches[1]);
                $a = $this->extra[$column]['value'];
				
                foreach($matches[1] AS $match) 
                {
                    $p = (array)$p;
                    $a = str_replace('{{'.$match.'}}',$p[$match], $a);
                    preg_match_all('/\[\[(.*?)\]\]/', $a, $newMaches);
                    if(count($newMaches[1]) > 0) 
                    {
                        foreach($newMaches[1] AS $matchNew) 
                        {
                            $valToInclude = (strlen($p[$matchNew])>$characterlimit)? substr($p[$matchNew],0,$characterlimit)."..":$p[$matchNew];
                            $a = str_replace('[['.$matchNew.']]',$valToInclude, $a);
                        }
                    }
                }
                $val = $a;
            }
            return $val;
        }
        return '';
    }
    
    public function _parseString($str,$p) 
    {
        if($str != '') 
        {
            $val = '';

            $characterlimit = 15;
            
            preg_match_all('/\{\{(.*?)\}\}/', $str, $matches);
            if(count($matches[1]) > 0) 
            {
                $matches[1] = array_unique($matches[1]);
                $a = $str;
				
                foreach($matches[1] AS $match) 
                {
                    $p = (array)$p;
                    $a = str_replace('{{'.$match.'}}',$p[$match], $a);
                    preg_match_all('/\[\[(.*?)\]\]/', $a, $newMaches);
                    if(count($newMaches[1]) > 0) 
                    {
                        foreach($newMaches[1] AS $matchNew) 
                        {
                            $valToInclude = (strlen($p[$matchNew])>$characterlimit)? substr($p[$matchNew],0,$characterlimit)."..":$p[$matchNew];
                            $a = str_replace('[['.$matchNew.']]',$valToInclude, $a);
                        }
                    }
                }
                $val = $a;
            }
            return $val;
        }
        return '';
    }
}