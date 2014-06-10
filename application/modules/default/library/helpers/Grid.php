<?php
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

class Zend_View_Helper_Grid extends Zend_View_Helper_Abstract {

	public $view = null;

	public $extra = array();

	private $output; // Container to hold the Grid

	public function setView(Zend_View_Interface $view) {

		$this->view = $view;

		return $this;

	}

	public function grid ($dataArray)
	{
		$view = Zend_Layout::getMvcInstance()->getView();	
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($dataArray['tablecontent']));
		$paginator->setItemCountPerPage($dataArray['perPage'])
		->setCurrentPageNumber($dataArray['pageNo']); 
				
		$extra['options'] = array();  
		$extra['action'] = array('name' => 'edit', 'value' =>'<div class="grid-action-align">
									<a href= "'.$dataArray['objectname'].'/view/id/{{id}}" name="{{id}}" class="sprite view" ></a>
									<a href= "'.$dataArray['objectname'].'/edit/id/{{id}}" name="{{id}}" class="sprite edit" ></a>
									<a name="{{id}}" onclick= changestatus(\''.$dataArray['objectname'].'\',\'{{id}}\',\'getwidgetGrid\')	href= javascript:void(0) title=\'{{sentrifugo_status}} \' class="sprite delete" ></a>
								</div>'); //onclick ="javascript:editlocdata(\'{{id}}\')" 	
		return $this->generateGrid($dataArray['objectname'],$dataArray['tableheader'],$paginator,$extra,true,$dataArray['jsGridFnName'], $dataArray['perPage'],$dataArray['jsFillFnName'],$dataArray['searchArray']);
		
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

	public function generateGrid ($name, $fields = null,$paginator=null,$extracolumn=array(),$sorting=false,$jsGridFnname='', $perPage='5', $jsFillFnName='',$searchArray='' ) {

		// Store Extra Columns
		$this->extra = $extracolumn;

		$sort = Zend_Controller_Front::getInstance()->getRequest()->getParam('sort','DESC');

		// checking and handling sorting.
		if ($sort  ==  'ASC') {
			$sort = 'DESC';
		}  else {
			$sort = 'ASC';
		}

		$output="<div id='".$name."' style='clear:both;' class='details_data_display_block newtablegrid'>";
		$output .="<div class='table-header'><span>".$name."</span><input type='button' onclick='window.location.href=\"".DOMAIN.$name."/add\"' value='Add Record' class='sprite addrecord' /></div>";
		$output .= "<table class='grid' align='center'  width='100%' cellspacing='0' cellpadding='4' border='0'><thead><tr>";

		// this foreach loop display the column header  in “th” tag.
		$colinr = 0;
		foreach ($fields as $key => $value) {
			//echo"<pre>";print_r($value);
			$align = (@$value['align'] != '')? 'align="'.$value['align'].'" ':'';
			$sortkey = (@$value['sortkey'] != '')? 'align="'.$value['sortkey'].'" ':'';
			
			$style = (@$value['style'] != '')? 'style="'.$value['style'].'" ':'';
			
			//echo $sortkey.'<br/>';
			$value = (is_array($value) && !isset($value['sortkey']))? $value['value']:$value;	
			if($value == 'Action') $width = 'width=90'; else $width = '';
			$output .= "<th ".$width.">";
			// Check if Sorting is set to True
			if($sorting) {

				// Disable Sorting if Key is in Extra Columns
				if(@$this->extra[$key]['name'] != '' && !is_array($value)) {
					$output .= $value;
				} else {
					if(is_array($value)){
						$key = $value['sortkey'];
						$value = $value['value'];
					} 
					$output .= "<a href='javascript:void(0);' onclick=javascript:paginationndsorting('".$this->view->url(array('sort'=>$sort,'by'=>$key,'objname'=> $name))."');>".$value."</a>";
					if($key != 'id')
					{
						//$output .= "<input type='text' class='searchtxtbox' value='' onkeyup=javascript:paginationndsorting('".$this->view->url(array('sort'=>$sort,'by'=>$key,'')). style='display:none;' />";
						if(!empty($searchArray)) $display = 'display: block;'; else $display = 'display: none;';
						if(array_key_exists($key,$searchArray)) $sText = $searchArray[$key]; else $sText = '';
						//$output .= "<input type='text' name='searchbox' id='$key' style='$display' class='searchtxtbox' value='$sText' onkeyup='getsearchdata(\"$key\",this.value,\"$name\")' />";
						$output .= "<input type='text' name='$name' id='$key' style='$display' class='searchtxtbox_$name table_inputs' value='$sText' onkeyup='getsearchdata(\"$name\")' />";
					}
				}
			}  else {
				$output .= $value;

			}

			$output .= "</th>";
			$colinr++;
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
			reset($fields);
			foreach($fields AS $k=>$v) {
				// Look for additional attributes
				$characterlimit = 15;
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
						$output .= "<a onclick= ".$jsFillFnName."(\"/id/$p[id]\") href= 'javascript:void(0)' title='".addslashes (htmlspecialchars(strip_tags ($p[$k])))."' >".addslashes (htmlspecialchars(strip_tags ($valToInclude)))."</a>";
					}
					else{
						$p = (array)$p;//Asma modification
						$valToInclude = (strlen($p[$k])>$characterlimit)? substr($p[$k],0,$characterlimit)."..":$p[$k];
						$output .= "<span  title='".addslashes (htmlspecialchars (strip_tags ($p[$k])))."' >".addslashes (htmlspecialchars (strip_tags($valToInclude)))."</span>";

						//$output .= $p[$k];
					}
				}

				$output .= "</td>";
				$bodyCount++;
			}
			// Close the Table Row
			$output.="</tr>";

		}
		if($ii == 0){
			$output.= "<tr><td colspan='$colinr' align='center'><p class='no-data'>No data found</p></td></tr>";
		}
		$output .= "</tbody>";
		$output .="</table></div>";
		if($ii == 0){
			$output .="<div style='height:50px;'>&nbsp;</div>";	
		}
		// Attach Pagination
		if($paginator) {

			//$output .="<tfoot>";

			// $output .="<td align='center' colspan='".count($fields)."'>";
			$params = array();
			$params['jsGridFnName'] = $jsGridFnname;
			$params['perPage'] = $perPage;
			$params['objname'] = $name;
			$params['searchArray'] = $searchArray;			
			
			$output.= $this->view->paginationControl($paginator,

                    'Sliding',

                    'partials/pagination.phtml',$params);

			//$output .="</tfoot>";
		}
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
