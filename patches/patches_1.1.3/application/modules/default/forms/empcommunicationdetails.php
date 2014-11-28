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

class Default_Form_empcommunicationdetails extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
		
        $this->setAttrib('id', 'formid');
        $this->setAttrib('name', 'emplcommunicationdetails');
		
        $id = new Zend_Form_Element_Hidden('id');
				
        $userid = new Zend_Form_Element_Hidden('user_id');
			
        $personalemail = new Zend_Form_Element_Text('personalemail');
        $personalemail->setAttrib('maxLength', 50);
        $personalemail->addFilter('StripTags');
        $personalemail->addFilter('StringTrim');
		$personalemail->addValidator("regex",true,array(
                           
						    'pattern'=>'/^(?!.*\.{2})[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/',                            
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid email.'
                           )
        	));
				
        $perm_streetaddress = new Zend_Form_Element_Text('perm_streetaddress');
        $perm_streetaddress->setAttrib('maxLength', 100);
        $perm_streetaddress->addFilter(new Zend_Filter_StringTrim());
			
        $perm_country = new Zend_Form_Element_Select('perm_country');
		$perm_country->setAttrib('onchange', 'displayParticularState(this,"","perm_state","")');
        $perm_country->setRegisterInArrayValidator(false);
        
        $perm_state = new Zend_Form_Element_Select('perm_state');
		$perm_state->setAttrib('onchange', 'displayParticularCity(this,"","perm_city","")');
        $perm_state->setRegisterInArrayValidator(false);
        $perm_state->addMultiOption('','Select State');
		
        $perm_city = new Zend_Form_Element_Select('perm_city');
        $perm_city->setRegisterInArrayValidator(false);
        $perm_city->addMultiOption('','Select City');

        $perm_pincode = new Zend_Form_Element_Text('perm_pincode');
        $perm_pincode->setAttrib('maxLength', 10);
        $perm_pincode->addFilter(new Zend_Filter_StringTrim());
        $perm_pincode->addValidators(array(array('StringLength',false,
                                  array('min' => 3,
                                  		'max' => 10,
                                        'messages' => array(
                                        Zend_Validate_StringLength::TOO_LONG =>
                                        'Postal code must contain at most %max% characters.',
                                        Zend_Validate_StringLength::TOO_SHORT =>
                                        'Postal code must contain at least %min% characters.')))));
		
        $perm_pincode->addValidator("regex",true,array(
                           'pattern'=>'/^(?!0{3})[0-9a-zA-Z]+$/', 

                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid postal code.'
                           )
        	));
        			

        $current_streetaddress = new Zend_Form_Element_Text('current_streetaddress');
        $current_streetaddress->setAttrib('maxLength', 100);
        $current_streetaddress->addFilter(new Zend_Filter_StringTrim());
		
			
        $current_country = new Zend_Form_Element_Select('current_country');
       	
		$current_country->setAttrib('onchange', 'displayParticularState(this,"","current_state","")');
        $current_country->setRegisterInArrayValidator(false);
        $current_country->addMultiOption('','Select Country');

        $current_state = new Zend_Form_Element_Select('current_state');
        
		$current_state->setAttrib('onchange', 'displayParticularCity(this,"","current_city","")');
        $current_state->setRegisterInArrayValidator(false);
        $current_state->addMultiOption('','Select State');
       		
        $current_city = new Zend_Form_Element_Select('current_city');
        
        $current_city->setRegisterInArrayValidator(false);
        $current_city->addMultiOption('','Select City');
		

        $current_pincode = new Zend_Form_Element_Text('current_pincode');
        $current_pincode->setAttrib('maxLength', 10);
        $current_pincode->addFilter(new Zend_Filter_StringTrim());
        $current_pincode->addValidators(array(array('StringLength',false,
                                  array('min' => 3,
                                  		'max' => 10,
                                        'messages' => array(
                                        Zend_Validate_StringLength::TOO_LONG =>
                                        'Postal code must contain at most %max% characters.',
                                        Zend_Validate_StringLength::TOO_SHORT =>
                                        'Postal code must contain at least %min% characters.')))));
		
        $current_pincode->addValidator("regex",true,array(
		                    'pattern'=>'/^(?!0{3})[0-9a-zA-Z]+$/', 
                           
                          
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid postal code.'
                           )
        	));	
		
        $address_flag = new Zend_Form_Element_Checkbox('address_flag');
        $address_flag->setAttrib('onclick', 'populateCurrentAddress(this)');
		
		
        $emergency_number = new Zend_Form_Element_Text('emergency_number');
        $emergency_number->setAttrib('maxLength', 10);
        $emergency_number->addFilter(new Zend_Filter_StringTrim());
        $emergency_number->addValidator("regex",true,array(
                           'pattern'=>'/^(?!0{10})[0-9]+$/', 
                          
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid phone number.'
                           )
        	));
		
        $emergency_name = new Zend_Form_Element_Text('emergency_name');
        $emergency_name->setAttrib('maxLength', 50);
        $emergency_name->addFilter(new Zend_Filter_StringTrim());
        $emergency_name->addValidator("regex",true,array(                           
                                   'pattern'=>'/^([a-zA-Z.]+ ?)+$/',
                                   'messages'=>array(
                                       'regexNotMatch'=>'Please enter only alphabets.'
                                   )
                        ));
        $emergency_email = new Zend_Form_Element_Text('emergency_email');
        $emergency_email->setAttrib('maxLength', 50);
        $emergency_email->addFilter(new Zend_Filter_StringTrim());
		
        
		$emergency_email->addValidator("regex",true,array(
                           
						    'pattern'=>'/^(?!.*\.{2})[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/',                            
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid email.'
                           )
        	));
		
		 		
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submitbutton');
        $submit->setLabel('Save');
		
        $this->addElements(array($id,$userid,$personalemail,$perm_streetaddress,$perm_country,$perm_state,$perm_city,$perm_pincode,$current_streetaddress,$current_country,$current_state,$current_city,$current_pincode,$address_flag,$emergency_number,$emergency_name,$emergency_email,$submit));
        $this->setElementDecorators(array('ViewHelper')); 		
    }
}