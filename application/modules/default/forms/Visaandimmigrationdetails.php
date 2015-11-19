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
	
class Default_Form_Visaandimmigrationdetails extends Zend_Form
{ 
	public function init()
	{
       $this->setMethod('post');		
       $this->setAttrib('id', 'formid');
       $this->setAttrib('name','visaandimmigrationdetails');
     
		
        $id = new Zend_Form_Element_Hidden('id');
         $user_id = new Zend_Form_Element_Hidden('user_id');         
        //Passport number....(only alphanumerics)
                    
			$passport_num = new Zend_Form_Element_Text('passport_number');
			$passport_num->setRequired(true);
			$passport_num->addFilter(new Zend_Filter_StringTrim());
			$passport_num->setAttrib('maxLength', 20);
			$passport_num->addValidator('NotEmpty', false, array('messages' => 'Please enter passport number.'));
			$passport_num->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[a-zA-Z0-9]+$/i',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid passport number.'
								 )
							 )
						 )
					 )); 
			
        //passport_issue_date....
				
			$passport_issue_date = new ZendX_JQuery_Form_Element_DatePicker('passport_issue_date');
			
			$passport_issue_date->setOptions(array('class' => 'brdr_none'));	
			$passport_issue_date->setAttrib('readonly', 'true');
            $passport_issue_date->setAttrib('onfocus', 'this.blur()');			
		 
		//Passport Expiration Date ....
		$passport_expiry_date = new ZendX_JQuery_Form_Element_DatePicker('passport_expiry_date');
		$passport_expiry_date->setRequired(true);
		$passport_expiry_date->setOptions(array('class' => 'brdr_none'));	
		$passport_expiry_date->setAttrib('readonly', 'true');	
		$passport_expiry_date->setAttrib('onfocus', 'this.blur()');
		$passport_expiry_date->addValidator('NotEmpty', false, array('messages' => 'Please enter passport expire date.'));
		// Expiration Date should be greater than today's date...
		$passport_expiry_date->addValidator(new sapp_DateGreaterThanToday());
		
		//Visa Number .....
		$visaNum = new Zend_Form_Element_Text('visa_number');
		$visaNum ->setRequired(true);
		$visaNum->addFilters(array('StringTrim', 'StripTags'));
		$visaNum->setAttrib('maxLength', 20);
		$visaNum->addValidator('NotEmpty', false, array('messages' => 'Please enter visa number.'));
	    
		//visa_type....(alphanumerics with '-' as only spl character)
		$visaType = new Zend_Form_Element_Text('visa_type');
		$visaType->addFilter(new Zend_Filter_StringTrim());
		$visaType->setAttrib('maxLength', 3);
		$visaType->addValidators(array(array('StringLength',false,
								  array('min' => 2,
										'max' => 3,
										'messages' => array(
										Zend_Validate_StringLength::TOO_LONG =>
										'Visa type code must contain at most %max% characters',
										Zend_Validate_StringLength::TOO_SHORT =>
										'Visa type code  must contain at least %min% characters')))));
		$visaType->addValidators(array(
					 array(
						 'validator'   => 'Regex',
						 'breakChainOnFailure' => true,
						 'options'     => array( 
						 'pattern' =>'/^[a-zA-Z0-9\-\s]+$/i',
							 'messages' => array(
									 'regexNotMatch'=>'Please enter valid visa type code.'
							 )
						 )
					 )
				 )); 
	 
		//Visa issue_date....
		$visa_issue_date = new ZendX_JQuery_Form_Element_DatePicker('visa_issue_date');
		$visa_issue_date->setOptions(array('class' => 'brdr_none'));	
		$visa_issue_date->setAttrib('readonly', 'true');
        $visa_issue_date->setAttrib('onfocus', 'this.blur()'); 		
		 
		//Visa Expiration Date ....
		$visa_expiry_date = new ZendX_JQuery_Form_Element_DatePicker('visa_expiry_date');
		$visa_expiry_date ->setRequired(true);
		$visa_expiry_date->setOptions(array('class' => 'brdr_none'));	
		$visa_expiry_date->setAttrib('readonly', 'true');
        $visa_expiry_date->setAttrib('onfocus', 'this.blur()'); 		
        $visa_expiry_date->addValidator('NotEmpty', false, array('messages' => 'Please enter visa expiry date.'));
		// Expiration Date should be greater than today's date...
		$visa_expiry_date->addValidator(new sapp_DateGreaterThanToday());
	    
		//Inine_status .....
		$i_nine_status = new Zend_Form_Element_Text('inine_status');
		$i_nine_status->addFilter(new Zend_Filter_StringTrim());
		$i_nine_status->setAttrib('maxLength', 50);
		
		$i_nine_status->addValidators(array(
					 array(
						 'validator'   => 'Regex',
						 'breakChainOnFailure' => true,
						 'options'     => array( 
						 'pattern' =>'/^[a-zA-Z\s]+$/i',
							 'messages' => array(
									 'regexNotMatch'=>'Please enter only alphabets.'
							 )
						 )
					 )
				 )); 
	
		//  Inine_review_date....
		$i_nine_review_date = new ZendX_JQuery_Form_Element_DatePicker('inine_review_date');
		$i_nine_review_date->setOptions(array('class' => 'brdr_none'));	
		$i_nine_review_date->setAttrib('readonly', 'true');
        $i_nine_review_date->setAttrib('onfocus', 'this.blur()');		
		
		//issuing_authority  ... (only alphabets with spaces)
		$issue_auth = new Zend_Form_Element_Text('issuing_authority');
		$issue_auth->addFilter(new Zend_Filter_StringTrim());
		$issue_auth->setAttrib('maxLength', 50);
		
		$issue_auth->addValidators(array(
					 array(
						 'validator'   => 'Regex',
						 'breakChainOnFailure' => true,
						 'options'     => array( 
						 'pattern' =>'/^[a-zA-Z\s]+$/i',
							 'messages' => array(
									 'regexNotMatch'=>'Please enter only alphabets.'
							 )
						 )
					 )
				 )); 
		
		//Ininetyfour_status....
		$i_ninetyfour_status = new Zend_Form_Element_Text('ininetyfour_status');
		$i_ninetyfour_status->addFilter(new Zend_Filter_StringTrim());
		$i_ninetyfour_status->setAttrib('maxLength', 50);
		
		$i_nine_status->addValidators(array(
					 array(
						 'validator'   => 'Regex',
						 'breakChainOnFailure' => true,
						 'options'     => array( 
						 'pattern' =>'/^[a-zA-Z\s]+$/i',
							 'messages' => array(
									 'regexNotMatch'=>'Please enter only alphabets.'
							 )
						 )
					 )
				 )); 
	
			
		//Ininetyfour_expiry_date ...
	  	$i_ninetyfour_expiry_date = new ZendX_JQuery_Form_Element_DatePicker('ininetyfour_expiry_date');
		$i_ninetyfour_expiry_date->setOptions(array('class' => 'brdr_none'));	
		$i_ninetyfour_expiry_date->setAttrib('readonly', 'true');
        $i_ninetyfour_expiry_date->setAttrib('onfocus', 'this.blur()');		
		// Expiration Date should be greater than today's date...
		$i_ninetyfour_expiry_date->addValidator(new sapp_DateGreaterThanToday());
		  
         // Form Submit ......... 
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submitbutton');
        $submit->setLabel('Save');

        $this->addElements(array($id,$user_id,$passport_num,$passport_issue_date,$passport_expiry_date,$visaNum,$visaType,	$visa_issue_date,$visa_expiry_date,$i_nine_status,$i_nine_review_date,$issue_auth,				$i_ninetyfour_status,$i_ninetyfour_expiry_date,$submit));
		
		$this->setElementDecorators(array('ViewHelper')); 
		
		$this->setElementDecorators(array(
                    'UiWidgetElement',
        ),array('inine_review_date','ininetyfour_expiry_date','passport_issue_date','passport_expiry_date','visa_issue_date','visa_expiry_date'));
		
        }
}
?>