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
class Default_Form_Candidatedetails extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setAttrib('id', 'formid');
        $this->setAttrib('name', 'frm_candidate');

        $id = new Zend_Form_Element_Hidden('id');
        $id_val = Zend_Controller_Front::getInstance()->getRequest()->getParam('id',null);
        $requisition_id = new Zend_Form_Element_Select("requisition_id");
        $requisition_id->setRegisterInArrayValidator(false);
        $requisition_id->setLabel("Requisition ID");	
        $requisition_id->setAttrib("class", "formDataElement"); 
        
		$requisition_id->setAttrib('onchange', 'displayParticularCandidates(this,"cand")');
        $requisition_id->setAttrib('title', 'Requisition ID');

        // To give option to user to select either file upload OR fill up form.
		$selected_option = Zend_Controller_Front::getInstance()->getRequest()->getParam('selected_option',null);

		// Below condition is used to skip form validation if the user opt for 'file upload'. 
		
		
        if($id_val == '')
        {
            $requisition_id->setRequired(true);
            $requisition_id->addValidator('NotEmpty', false, array('messages' => 'Please select requisition id.')); 
        }               
        /*$candidate_name = new Zend_Form_Element_Text('candidate_name');
        $candidate_name->setAttrib('maxLength', 90);
        $candidate_name->setAttrib('title', 'Candidate Name');        
        $candidate_name->addFilter(new Zend_Filter_StringTrim());
        $candidate_name->setRequired(true);
        $candidate_name->addValidator('NotEmpty', false, array('messages' => 'Please enter candidate name.'));  
        $candidate_name->addValidator("regex",true,array(                           
                           'pattern'=>'/^[a-zA-Z.\- ?]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid candidate name.'
                           )
        	));*/

        $candidate_firstname = new Zend_Form_Element_Text('candidate_firstname');
        $candidate_firstname->setAttrib('maxLength', 50);
        $candidate_firstname->setAttrib('title', 'Candidate First Name');        
        $candidate_firstname->addFilter(new Zend_Filter_StringTrim());
        $candidate_firstname->setRequired(true);
        $candidate_firstname->addValidator('NotEmpty', false, array('messages' => 'Please enter candidate first name.'));  
        $candidate_firstname->addValidator("regex",true,array(                           
                           'pattern'=>'/^[a-zA-Z.\- ?]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid candidate first name.'
                           )
        	));

        $candidate_lastname = new Zend_Form_Element_Text('candidate_lastname');
        $candidate_lastname->setAttrib('maxLength', 50);
        $candidate_lastname->setAttrib('title', 'Candidate Last Name');        
        $candidate_lastname->addFilter(new Zend_Filter_StringTrim());
        $candidate_lastname->setRequired(true);
        $candidate_lastname->addValidator('NotEmpty', false, array('messages' => 'Please enter candidate last name.'));  
        $candidate_lastname->addValidator("regex",true,array(                           
                           'pattern'=>'/^[a-zA-Z.\- ?]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid candidate last name.'
                           )
        	));	
                
		$emailid = new Zend_Form_Element_Text('emailid');
		$emailid->setRequired(true);
	    $emailid->setAttrib('maxLength', 70);
        $emailid->setAttrib('title', 'Email');        
        $emailid->addFilter(new Zend_Filter_StringTrim());
        $emailid->addValidator('NotEmpty', false, array('messages' => 'Please enter email.'));  
        
        $emailid->addValidator("regex",true,array(
                           
						    'pattern'=>'/^(?!.*\.{2})[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/',                            
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid email.'
                           )
        	));
                 
        $emailid->addValidator(new Zend_Validate_Db_NoRecordExists(
        						array('table' => 'main_candidatedetails',
        						'field' => 'emailid',
                                                        'exclude'=>'id != "'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive != 0',
        						)));
        $emailid->getValidator('Db_NoRecordExists')->setMessage('Email already exists.');
          if($selected_option == 'candidatedetails'){
			 $emailid->setRequired(false);
		}
        
        $contact_number = new Zend_Form_Element_Text('contact_number');
        $contact_number->setRequired(true);
	/*	if($selected_option == 'upload-resume'){
        	$contact_number->setRequired(true);
		}*/
        
        $contact_number->setAttrib('maxLength', 10);
        $contact_number->setAttrib('title', 'Contact Number.');        
        $contact_number->addFilter(new Zend_Filter_StringTrim());
        $contact_number->addValidator('NotEmpty', false, array('messages' => 'Please enter contact number.'));  
        $contact_number->addValidator("regex",true,array(                           
                           
                           
                            'pattern'=>'/^(?!0{10})([0-9\+\-\)\(]{10})+$/',
                           'messages'=>array(
                           
                               'regexNotMatch'=>'Please enter valid contact number.'
                           )
        	));
        $contact_number->addValidator(new Zend_Validate_Db_NoRecordExists(
        						array('table' => 'main_candidatedetails',
        						'field' => 'contact_number',
                                                        'exclude'=>'id != "'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive != 0',
        						)));
        $contact_number->getValidator('Db_NoRecordExists')->setMessage('Contact number already exists.');
        if($selected_option == 'candidatedetails'){
			 $contact_number->setRequired(false);
		}
        $qualification = new Zend_Form_Element_Text('qualification');
        $qualification->setAttrib('maxLength', 90);
        $qualification->setAttrib('title', 'Qualification.');        
        $qualification->addFilter(new Zend_Filter_StringTrim());
        
        if($selected_option == 'fill-up-form'){
        	$qualification->setRequired(true);
        }
        
        $qualification->addValidator('NotEmpty', false, array('messages' => 'Please enter qualification.'));  
        $qualification->addValidator("regex",true,array(                         
                           'pattern'=>'/^[a-zA-Z.\- ?]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid qualification.'
                           )
        	));
        
        $experience = new Zend_Form_Element_Text('experience');
        $experience->setAttrib('maxLength', 5);
        $experience->setAttrib('title', 'Work Experience.');        
        $experience->addFilter(new Zend_Filter_StringTrim());
        
        if($selected_option == 'fill-up-form'){
        	$experience->setRequired(true);
        }
        
        $experience->addValidator('NotEmpty', false, array('messages' => 'Please enter work experience.'));  
        $experience->addValidator("regex",true,array(                           
                           'pattern'=>'/^([0-9]*\.?[0-9]{1,2})$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid experience.'
                           )
        	));
        
        $skillset = new Zend_Form_Element_Textarea('skillset');
        $skillset->setAttrib('rows', 10);
        $skillset->setAttrib('cols', 50);        
        $skillset->setAttrib('title', 'Skill Set');
        
       
        	$skillset->setRequired(true);
     
        $skillset->addValidator('NotEmpty', false, array('messages' => 'Please enter skill set.')); 
       if($selected_option == 'candidatedetails'){
			 $skillset->setRequired(false);
		}
        
        $source = new Zend_Form_Element_Select('source');
        $source->addMultiOptions(array(
        		 ''=>'select Source',
										   'Vendor' => 'Vendor',
										   'Website' => 'Website',
                                           'Referal' => 'Referral',
    									   ));
        $source->setAttrib('onchange', 'displayVendors(this)');
        
										   
		$source->setRegisterInArrayValidator(false);
		$source->setRequired(true);
		$source->addValidator('NotEmpty', false, array('messages' => 'Please select source.'));
		   if($selected_option == 'candidatedetails'){
			 $source->setRequired(false);
		}
		$vendors = new Zend_Form_Element_Select('vendors');
		$vendors->setRegisterInArrayValidator(false);
		$vendors->addMultiOption('','Select Vendor');
		if($source=="Vendor")
		{
		$vendors->setRequired(true);
		$vendors->addValidator('NotEmpty', false, array('messages' => 'Please select vendor.'));
		}
		 if($selected_option == 'candidatedetails'){
			 $vendors->setRequired(false);
		}
		
		$referal= new Zend_Form_Element_Text('referal');
		$referal->setAttrib('maxLength', 50);
		if($source=="Referal")
		{
		$referal->setRequired(true);
		$referal->addValidator('NotEmpty', false, array('messages' => 'Please enter referral name.'));
		}
	//	$referal->setAttrib('title', 'Referal.');
		$referal->addValidator("regex",true,array(
				'pattern'=>'/^[a-zA-Z.\- ?]+$/',
				'messages'=>array(
						'regexNotMatch'=>'Please enter valid referral name.'
				)
		));
		$referal->addFilter(new Zend_Filter_StringTrim());
		 if($selected_option == 'candidatedetails'){
			 $referal->setRequired(false);
		}
		
		$referalwebsite= new Zend_Form_Element_Text('referalwebsite');
		$referalwebsite->setAttrib('maxLength', 50);
		if($source=="Website")
		{
		$referalwebsite->setRequired(true);
		$referalwebsite->addValidator('NotEmpty', false, array('messages' => 'Please enter referral website.'));
		}
		  $referalwebsite->addValidator("regex",true,array(                           
                           'pattern'=>'/^(http:\/\/www|https:\/\/www|www)+\.([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,3})$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid URL.'
                           )
        	));
		$referalwebsite->addFilter(new Zend_Filter_StringTrim());
		
         if($selected_option == 'candidatedetails'){
			 $referalwebsite->setRequired(false);
		}
        $education_summary = new Zend_Form_Element_Textarea('education_summary');
        $education_summary->setAttrib('rows', 10);
        $education_summary->setAttrib('cols', 50);        
        $education_summary->setAttrib('title', 'Education Summary.');
        
        $summary = new Zend_Form_Element_Textarea('summary');
        $summary->setAttrib('rows', 10);
        $summary->setAttrib('cols', 50);        
        $summary->setAttrib('title', 'Summary.');
        
        $cand_location = new Zend_Form_Element_Textarea('cand_location');
        $cand_location->setAttrib('rows', 10);
        $cand_location->setAttrib('cols', 50);        
        $cand_location->setAttrib('maxlength', 150);        
        $cand_location->setAttrib('title', 'Location.');
        
        if($selected_option == 'fill-up-form'){
        	$cand_location->setRequired(true);
        }
        
        $cand_location->addValidator('NotEmpty', false, array('messages' => 'Please enter location.')); 
             
        $country = new Zend_Form_Element_Select("country");
        $country->setRegisterInArrayValidator(false);
        $country->setLabel("Country");		
        $country->setAttrib("class", "formDataElement"); 
        $country->setAttrib('onchange', 'displayParticularState_normal(this,"","state","city")');
        $country->setAttrib('title', 'Country');
        
        if($selected_option == 'fill-up-form'){
        	$country->setRequired(true);
        }
        
        $country->addValidator('NotEmpty', false, array('messages' => 'Please select country.'));
		
        $state = new Zend_Form_Element_Select("state");
        $state->setRegisterInArrayValidator(false);
        
        if($selected_option == 'fill-up-form'){
			$state->setRequired(true);
        }
        
		$state->addValidator('NotEmpty', false, array('messages' => 'Please select state.')); 
        $state->addMultiOptions(array(''=>'Select State'));
        $state->setLabel("State");		
        $state->setAttrib("class", "formDataElement"); 
        $state->setAttrib('onchange', 'displayParticularCity_normal(this,"","city","")');
        $state->setAttrib('title', 'State'); 		
        
        $city = new Zend_Form_Element_Select("city");
        $city->setRegisterInArrayValidator(false);
        $city->addMultiOptions(array(''=>'Select City'));
        $city->setLabel("City");		
        $city->setAttrib("class", "formDataElement");         
        $city->setAttrib('title', 'City');
        
        if($selected_option == 'fill-up-form'){
			$city->setRequired(true);
        }
        
		$city->addValidator('NotEmpty', false, array('messages' => 'Please select city.'));
        
        $pincode = new Zend_Form_Element_Text('pincode');
        $pincode->setAttrib('maxLength', 10);
        $pincode->setAttrib('title', 'Postal Code.');        
        $pincode->addFilter(new Zend_Filter_StringTrim());
        
        if($selected_option == 'fill-up-form'){
        $pincode->setRequired(true);
        }
        
        $pincode->addValidator('NotEmpty', false, array('messages' => 'Please enter postal code.')); 
		$pincode->addValidators(array(array('StringLength',false,
                                  array('min' => 3,
                                  		'max' => 10,
                                        'messages' => array(
                                        Zend_Validate_StringLength::TOO_LONG =>
                                        'Postal code must contain at most %max% characters.',
                                        Zend_Validate_StringLength::TOO_SHORT =>
                                        'Postal code must contain at least %min% characters.')))));
        $pincode->addValidator("regex",true,array(  
                            'pattern'=>'/^(?!0{3})[0-9a-zA-Z]+$/', 		
                           
                            'messages'=>array(
                               'regexNotMatch'=>'Please enter valid postal code.'
                           )
        	));
		
        $cand_status = new Zend_Form_Element_Select("cand_status");
        $cand_status->setRegisterInArrayValidator(false);
        
        $cand_status->setLabel("Status");
        $cand_status->setAttrib("class", "formDataElement");        
        $cand_status->setAttrib('title', 'Candidate status');
                        
        $submit = new Zend_Form_Element_Submit('submit');        
        $submit->setAttrib('id', 'submitbutton');
		$submit->setAttrib('class', 'cvsbmtbtn');
        $submit->setLabel('Save'); 
        
        
        
        $savesubmit = new Zend_Form_Element_Submit('savesubmit');
       
        $savesubmit->setLabel('Save and schedule');
        
        //start of candidate work details.
        for($i=0;$i<3;$i++)
        {            
            $company_name[$i] = new Zend_Form_Element_Text('txt_cname['.$i.']');
            
            $company_name[$i]->setAttrib('id', 'idtxt_cname'.$i);
            
            $company_name[$i]->setAttrib('maxlength', 70);
            $company_name[$i]->addValidator("regex",true,array(                           
                           'pattern'=>'/^[a-zA-Z.\-& ?]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid company name.'
                           )
        	));
            $this->addElement($company_name[$i]);
            
            $cdesignation[$i] = new Zend_Form_Element_Text('txt_desig['.$i.']');
            $cdesignation[$i]->setAttrib('id', 'idtxt_desig'.$i);
            
            $cdesignation[$i]->setAttrib('maxlength', 40);
            $cdesignation[$i]->addValidator("regex",true,array(                           
                           'pattern'=>'/^[a-zA-Z.\-& ?]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid designation.'
                           )
        	));
            $this->addElement($cdesignation[$i]);
            
            $from_date[$i] = new Zend_Form_Element_Text('txt_from['.$i.']');
            $from_date[$i]->setAttrib('id', 'idtxt_from'.$i);
            
            $from_date[$i]->setAttrib('readonly', 'readonly');            
            $this->addElement($from_date[$i]);
            
            $to_date[$i] = new Zend_Form_Element_Text('txt_to['.$i.']');
            $to_date[$i]->setAttrib('id', 'idtxt_to'.$i);
            
            $to_date[$i]->setAttrib('readonly', 'readonly');            
            $this->addElement($to_date[$i]);
            
            $cnumber[$i] = new Zend_Form_Element_Text('txt_cnumber['.$i.']');
            $cnumber[$i]->setAttrib('id', 'idtxt_cnumber'.$i);
           
            $cnumber[$i]->setAttrib('maxlength', 10);
            $cnumber[$i]->addValidator("regex",true,array(                           
                           
                           'pattern'=>'/^(?!0{10})([0-9\+\-\)\(]{10})+$/',
                           'messages'=>array(
                           
                               'regexNotMatch'=>'Please enter valid contact number.'
                           )
        	));
            $this->addElement($cnumber[$i]);
            
            $website[$i] = new Zend_Form_Element_Text('txt_website['.$i.']');
            $website[$i]->setAttrib('id', 'idtxt_website'.$i);
            
            $website[$i]->setAttrib('maxlength', 70);
            
            $website[$i]->addValidator("regex",true,array(                           
                           'pattern'=>'/^(http:\/\/www|https:\/\/www|www)+\.([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,3})$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid URL.'
                           )
        	));
            $this->addElement($website[$i]);
            
            $comp_address[$i] = new Zend_Form_Element_Textarea('txt_address['.$i.']');
            $comp_address[$i]->setAttrib('id', 'idtxt_address'.$i);
            
            
            $this->addElement($comp_address[$i]);
        }
        //end of candidate work details.
		$job_title = new Zend_Form_Element_Text('job_title');        
		$job_title->setAttrib('readonly', 'readonly');
		
        $this->addElements(array($job_title,$cand_status,$id,$requisition_id,$candidate_firstname,$candidate_lastname,$emailid,$contact_number,$qualification,$experience,
                                $skillset,$education_summary,$summary,$cand_location,$country,$state,$city,$pincode,$submit,$savesubmit,$source,$vendors,$referal,$referalwebsite));
        $this->setElementDecorators(array('ViewHelper')); 
    }
}