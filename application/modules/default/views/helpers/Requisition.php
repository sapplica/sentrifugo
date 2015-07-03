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

Class Zend_View_Helper_Requisition extends Zend_View_Helper_Abstract{
	
	public function requisition(){
		return $this;
	}
	
	// To get configuration link HTML
	public function getConfigurationLink($controller='', $label='', $link_text=''){
		$domain = BASE_URL;
        if($controller == 'employmentstatus'){
			echo <<<EOT
			<span class="add-coloum" onclick="displaydeptform('{$domain}{$controller}/addpopup/boxid/emp_type/fromcontroller/requisition', '{$label}');"> {$link_text} </span>			
EOT;
        }else{
            echo <<<EOT
<span class="add-coloum" onclick="displaydeptform('{$domain}{$controller}/addpopup', '{$label}');"> {$link_text} </span>			
EOT;
        }
	}
	
	public function getContainer($controller=''){
		$base_url = DOMAIN;
		echo <<<EOT
<div id="{$controller}Container"  style="display: none; overflow: auto;">
	<div class="heading">
		<a href="javascript:void(0)">
		<img src="{$base_url}public/media/images/close.png" name="" align="right"
			border="0" hspace="3" vspace="5" class="closeAttachPopup"
			style="margin: -24px 8px 0 0;"> </a>
	</div>

		<iframe id="{$controller}Cont" class="business_units_iframe" frameborder="0"></iframe>

</div>
EOT;
	}
	
	// To dispaly data in the Report HTML table
	public function displayData($emp_data, $column_key){
            if($column_key == 'created_on')
            {
                echo isset($emp_data['created_on'])?  sapp_Global::change_date($emp_data['created_on'],"view"):"";
            }elseif($column_key == 'onboard_date'){
                echo isset($emp_data['onboard_date'])?  sapp_Global::change_date($emp_data['onboard_date'],"view"):"";
            }else 
            {
                echo (isset($emp_data[$column_key]) && !empty($emp_data[$column_key]))?$emp_data[$column_key]:"--";
            }
	}
}