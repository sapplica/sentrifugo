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

class Zend_View_Helper_Candidatedetails extends Zend_View_Helper_Abstract{

	public function __construct(){
	}

	public function candidatedetails(){
		return $this;
	}

	
	// Below methods are for ADD view
	public function checkViewResume($data=array(), $msgarray=array()){
		if((isset($data['selected_option']) && $data['selected_option']=='fill-up-form') || !empty($msgarray)){
			echo <<<EOT
style="display:none;"
EOT;
		}
	}

	public function checkViewForm($data=array(), $msgarray=array()){
		if((empty($data['selected_option']) || $data['selected_option']=='upload-resume') && empty($msgarray)){
			echo <<<EOT
style="display:none;"
EOT;
		}
	}

	public function viewResume($data=array(), $msgarray=array()){
		if((empty($data['selected_option']) && $data['selected_option']=='upload-resume') && empty($msgarray)){
			echo <<<EOT
class="act"
EOT;
		}
	}

	public function viewForm($data=array(), $msgarray=array()){
		if((!empty($data['selected_option']) || $data['selected_option']=='fill-up-form') || !empty($msgarray)){
			echo <<<EOT
class="act"
EOT;
		}
	}
	
	
	// Below methods are for EDIT view
	
	// To skip common elements in the form from validation (On both tab views - Upolad Resume && Update Candidate Details)
	public function trimMessages($msgarray=array()){
		unset($msgarray['requisition_id']);
		unset($msgarray['cand_status']);
		unset($msgarray['candidate_name']);
		return $msgarray;
	}
	
	public function checkViewResumeEdit($data=array(), $msgarray=array()){
		
		if(((empty($data['selected_option']) || $data['selected_option']=='fill-up-form') && empty($data['cand_resume'])) || !empty($msgarray)){
			echo <<<EOT
style="display:none;"
EOT;
			
		}
	}

	public function checkViewFormEdit($data=array(), $msgarray=array()){
		if(((isset($data['selected_option']) && $data['selected_option']=='upload-resume') || !empty($data['cand_resume'])) && empty($msgarray)){
			echo <<<EOT
style="display:none;"
EOT;
		}
	}

	public function viewResumeEdit($data=array(), $msgarray=array()){
		if(((isset($data['selected_option']) && $data['selected_option']=='upload-resume') || !empty($data['cand_resume'])) && empty($msgarray)){ 
			echo <<<EOT
class="act"
EOT;
		}
	}

	public function viewFormEdit($data=array(), $msgarray=array()){
		if(((empty($data['selected_option']) || $data['selected_option']=='fill-up-form') && empty($data['cand_resume']) || !empty($msgarray))){
			echo <<<EOT
class="act"
EOT;
		}
	}
	
	// To swap tabs - Upload Resume && Update Candidate Details
	public function checkSelectedOption($data=array()){
		if((empty($data['selected_option']) || $data['selected_option']=='upload-resume')){
			echo <<<EOT
upload-resume
EOT;
		}
		else if((empty($data['selected_option']) || $data['selected_option']=='candidatedetails')){
			echo <<<EOT
candidatedetails
EOT;
		}else{
			echo <<<EOT
fill-up-form
EOT;
		}
	}
	
	public function checkSelectedOptionEdit($data=array()){
	
		if(((empty($data['selected_option']) && empty($data['cand_resume'])) || $data['selected_option']=='fill-up-form')){
			echo <<<EOT
fill-up-form
EOT;
		}else{
			echo <<<EOT
upload-resume
EOT;
		}
	}	
	
    // To load Delete button HTML
	public function loadDeleteButton($candidate_data=array()){
		$show = '';
		$validation = true; 
		
		// To validate required fields in the form
		$optional_inputs = array('requisition_id', 'cand_status', 'candidate_firstname','candidate_lastname', 'emailid', 'contact_number', 'qualification', 'experience', 'skillset', 'cand_location', 'country', 'state', 'city', 'pincode', 'cand_resume');
		foreach($optional_inputs as $value){
			if(empty($candidate_data[$value])){
				$validation = false; 
			}			
		}
		
		if(!$validation){
			$show = "style=display:none;";
		}
    	echo <<<EOT
<span id='delete-resume' {$show} class="sprite delete" data="{$candidate_data['id']}"></span>
EOT;
	}

	public function loadResumeName($data=array()){
		$domain = BASE_URL;
		if(!empty($data['cand_resume'])){
			echo <<<EOT
<a href='{$domain}candidatedetails/download/id/{$data['rec_id']}'>{$data['cand_resume']}</a>
EOT;
		}else{
			echo <<<EOT
Resume is not available
EOT;
		} 	
	}
}