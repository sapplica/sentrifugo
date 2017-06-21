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

Class Zend_View_Helper_Reports extends Zend_View_Helper_Abstract{
	
	public function reports(){
		return $this;	
	}
	
	public function displayCandidatesData($emp_data, $column_key, $title=false){
		if(isset($emp_data[$column_key])){
			if(!$title && strlen($emp_data[$column_key])>25){
				echo substr($emp_data[$column_key],0,22).'...';
			}else{
				echo $emp_data[$column_key];
			}
		}else{
			echo '--';
		}
	}
	
	public function displayInterviewsData($req, $column_key, $title=false){
		if(isset($req[$column_key])){
			if(!$title && strlen($req[$column_key])>25){
				echo substr($req[$column_key],0,22).'...';
			}else{
				echo $req[$column_key];
			}
		}else{
			echo '--';
		}
	}
	
}