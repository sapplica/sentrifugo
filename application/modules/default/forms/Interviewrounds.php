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
 * This gives candidate details form.
 */
class Default_Form_Interviewrounds extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setAttrib('id', 'formid');
        $this->setAttrib('name', 'frm_sinterview_rounds');

        $id = new Zend_Form_Element_Hidden('id');
       
	    $req_id = new Zend_Form_Element_Select("req_id");
        $req_id->setRegisterInArrayValidator(false); 
        $req_id->setRequired(true);
        $req_id->setAttrib("class", "formDataElement"); 
        $req_id->addValidator('NotEmpty', false, array('messages' => 'Please select requisition id.'));  
		$req_id->setAttrib('title', 'Interviewer');
		$req_id->setAttrib('onchange', 'displayParticularCandidates(this,"")');
    
        $candidate_name = new Zend_Form_Element_Select('candidate_name');
        $candidate_name->setAttrib('title', 'Candidate Name');        
        $candidate_name->addFilter(new Zend_Filter_StringTrim());
        $candidate_name->setRequired(true);
        $candidate_name->addValidator('NotEmpty', false, array('messages' => 'Please select candidate.'));
        
	
		$candidate_name->addMultiOption('','Select candidate');
			
		$candidate_name->setRegisterInArrayValidator(false);
	
        $interviewer_id = new Zend_Form_Element_Select("interviewer_id");
        $interviewer_id->setRegisterInArrayValidator(false);
		$interviewer_id->setAttrib("class", "formDataElement");         		
        $interviewer_id->setAttrib('title', 'Interviewer');
        
        $interview_mode = new Zend_Form_Element_Select("interview_mode");
        $interview_mode->setRegisterInArrayValidator(true);  
        $interview_mode->setAttrib("class", "formDataElement"); 
        $interview_mode->addMultiOptions(array('' => 'Select Interview Type',
                                               'In person' => 'In person',
                                               'Phone' => 'Phone',
                                               'Video conference' => 'Video conference'));
        $interview_mode->setAttrib('title', 'Interview Type');
                        
        $int_location = new Zend_Form_Element_Text('int_location');
        $int_location->setAttrib('maxLength', 100);
        $int_location->setAttrib('title', 'Location');        
        $int_location->addFilter(new Zend_Filter_StringTrim());
        
        $job_title = new Zend_Form_Element_Text('job_title');        
		$job_title->setAttrib('readonly', 'readonly');
		
		$country = new Zend_Form_Element_Select('country');
        $country->setLabel('country');	
		$country->setAttrib('onchange', 'displayParticularState_normal(this,"state","state","city")');
		    
		$country->setRegisterInArrayValidator(false);	
		$countryModal = new Default_Model_Countries();
	    	$countriesData = $countryModal->fetchAll('isactive=1','country');
	    $country->addMultiOption('','Select country');
	    	foreach ($countriesData->toArray() as $data){
		$country->addMultiOption(trim($data['country_id_org']),$data['country']);
	    	}
                
		$state = new Zend_Form_Element_Select('state');
        
        $state->setAttrib('onchange', 'displayParticularCity_normal(this,"city","city","")');
        $state->setRegisterInArrayValidator(false);
		$state->addMultiOption('','Select State');
        
		$city = new Zend_Form_Element_Select('city');
        $city->setAttrib('class', 'selectoption');
		$city->setAttrib('onchange', 'displayCityCode(this)');
        $city->setRegisterInArrayValidator(false);
        $city->addMultiOption('','Select City');
		
        $interview_time = new Zend_Form_Element_Text('interview_time');        
        $interview_time->setAttrib('title', 'Interview Time');        
        $interview_time->setAttrib('readonly', 'readonly'); 
        $interview_time->setAttrib('onfocus', 'this.blur()');		
        $interview_time->setAttrib('class', 'time');        
        $interview_time->addFilter(new Zend_Filter_StringTrim());
        
        $timezone = new Zend_Form_Element_Text('timezone');
        
        $interview_date = new Zend_Form_Element_Text('interview_date');
        $interview_date->setAttrib('readonly', 'readonly');
        $interview_date->setAttrib('onfocus', 'this.blur()');        
        $interview_date->setAttrib('title', 'Interview Date');        
        $interview_date->addFilter(new Zend_Filter_StringTrim());
        
        $interview_round = new Zend_Form_Element_Text('interview_round');
        $interview_round->setAttrib('title', 'Interview Round');        
        $interview_round->addFilter(new Zend_Filter_StringTrim());
        $interview_round->setAttrib('maxlength', 45); 
        
		$interview_feedback = new Zend_Form_Element_Textarea('interview_feedback');
        $interview_feedback->setAttrib('rows', 10);
        $interview_feedback->setAttrib('cols', 50);        
        $interview_feedback->setAttrib('maxlength', 300);        
        $interview_feedback->setAttrib('title', 'Feedback.');		

		$interview_comments = new Zend_Form_Element_Textarea('interview_comments');
        $interview_comments->setAttrib('rows', 10);
        $interview_comments->setAttrib('cols', 50);        
        $interview_comments->setAttrib('maxlength', 300);        
        $interview_comments->setAttrib('title', 'Comments.');		
		
		$round_status = new Zend_Form_Element_Select('round_status');
                $round_status->setRegisterInArrayValidator(false);
		$round_status->setLabel('Round status')	;
                $round_status->addMultiOptions(array(
							'' => 'Select status',
							'Schedule for next round'=>'Schedule for next round' ,
							'Qualified'=>'Qualified',
							'Selected'=> 'Selected',
							'Disqualified' => 'Disqualified',
							
							
							'Incompetent' => 'Incompetent',
							'Ineligible' => 'Ineligible',
							'Candidate no show' => 'Candidate no show'
					));
		$hid_round_status_val = Zend_Controller_Front::getInstance()->getRequest()->getParam('hid_round_status',null);	
		$interview_status = new Zend_Form_Element_Select('interview_status');		
		$interview_status->setLabel('Interview status')
		->setMultiOptions(array(
							'' => 'Select status',
							'In process'=>'In process' ,
							'Completed'=>'Complete',
							'On hold'=> 'On hold'						
							));
		$interview_status->setRegisterInArrayValidator(false);
			
		$cand_status = new Zend_Form_Element_Select('cand_status');		
		$cand_status->setLabel('Candidate status')
		
		->setMultiOptions(array(
							'' => 'Select status',							
							));
		$cand_status->setRegisterInArrayValidator(false);
		
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		} 
		$intrvid_val = Zend_Controller_Front::getInstance()->getRequest()->getParam('intrvid',null);	
		if(($loginuserGroup == MANAGER_GROUP || $loginuserGroup == EMPLOYEE_GROUP || $loginuserGroup == SYSTEMADMIN_GROUP) || (($loginuserGroup == HR_GROUP || $loginuserGroup == MANAGEMENT_GROUP) && $intrvid_val == $loginUserId))
		{
			$round_status->setRequired(true);
                        $round_status->addValidator('NotEmpty', false, array('messages' => 'Please select status.'));
			$interview_comments->setRequired(true);
			$interview_comments->addValidator('NotEmpty', false, array('messages' => 'Please select comments.'));
			$interview_feedback->setRequired(true);
			$interview_feedback->addValidator('NotEmpty', false, array('messages' => 'Please select feedback.'));
		}
		if(($loginuserGroup == HR_GROUP || $loginuserGroup == MANAGEMENT_GROUP || $loginuserGroup == '') && $intrvid_val != $loginUserId)
		{
                    if($hid_round_status_val != '' && ($loginuserGroup == HR_GROUP || $loginuserGroup == MANAGEMENT_GROUP || $loginuserGroup == ''))
                    {
                        $round_status->setRequired(true);
                        $round_status->addValidator('NotEmpty', false, array('messages' => 'Please select status.'));
                    }
                    else 
                    {
			$interview_status->setRequired(true);
			$interview_status->addValidator('NotEmpty', false, array('messages' => 'Please select interview status.'));
			$cand_status->setRequired(true);
			$cand_status->addValidator('NotEmpty', false, array('messages' => 'Please select candidate status.'));					
			$interviewer_id->setRequired(true);
			$interviewer_id->addValidator('NotEmpty', false, array('messages' => 'Please select interviewer.'));  
			$interview_mode->setRequired(true);
			$interview_mode->addValidator('NotEmpty', false, array('messages' => 'Please select interview type.'));  
			$int_location->setRequired(true);
			$int_location->addValidator('NotEmpty', false, array('messages' => 'Please enter location.'));  
			$country->setRequired(true);
			$country->addValidator('NotEmpty', false, array('messages' => 'Please select country.')); 
			$state->setRequired(true);
			$state->addValidator('NotEmpty', false, array('messages' => 'Please select state.')); 
			$city->setRequired(true);
			$city->addValidator('NotEmpty', false, array('messages' => 'Please select city.'));
			$interview_time->setRequired(true);
			$interview_time->addValidator('NotEmpty', false, array('messages' => 'Please select interview time.'));
			$interview_date->setRequired(true);
			$interview_date->addValidator('NotEmpty', false, array('messages' => 'Please select interview date.'));
			$interview_round->setRequired(true);
			$interview_round->addValidator('NotEmpty', false, array('messages' => 'Please enter interview name.'));
                    }
		}
        $submit = new Zend_Form_Element_Submit('submit');        
        $submit->setAttrib('id', 'submitbutton');
        $submit->setLabel('Update'); 
        $int_location->addValidator("regex",true,array(                           
                           'pattern'=>'/^[a-zA-Z.\- ?]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid location.'
                           )
        	));
        $interview_round->addValidator("regex",true,array(                           
                           'pattern'=>'/^[0-9a-zA-Z.\- ?]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please select valid interview name.'
                           )
        	));
        $this->addElements(array($req_id,$id,$candidate_name,$job_title,$interviewer_id,$interview_mode,$int_location,$country,$state,$city,$interview_time,$interview_date,$round_status,$interview_feedback,$interview_comments,$interview_round,$interview_status,$cand_status,$submit,$timezone));
        $this->setElementDecorators(array('ViewHelper')); 
    }
}