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
 
class Default_Form_Workeligibilitydetails extends Zend_Form
{ 
	public function init()
	{
       $this->setMethod('post');		
       $this->setAttrib('id', 'formid');
       $this->setAttrib('name','workeligibilitydetails');
       
		
        $id = new Zend_Form_Element_Hidden('id');
        $userid = new Zend_Form_Element_Hidden('user_id');
		$emptyflag = new Zend_Form_Element_Hidden('emptyFlag');
		$issuingauthflag = new Zend_Form_Element_Hidden('issuingauthflag');
        //Document type Id....	
		$docType = new Zend_Form_Element_Select('documenttype_id');
		$docType->setRegisterInArrayValidator(false);	
		$docType->setAttrib('onchange', 'checkissuingauthority(this)');
		$docType->addMultiOption('','Select Document Type');
		
	 	//Document Issue Date...
		$doc_issue_date = new ZendX_JQuery_Form_Element_DatePicker('doc_issue_date');
		$doc_issue_date->setOptions(array('class' => 'brdr_none'));		
		$doc_issue_date->setAttrib('readonly', 'true');	
		$doc_issue_date->setAttrib('onfocus', 'this.blur()');	
		
		// Document Expiry Date...
		$doc_expiry_date = new ZendX_JQuery_Form_Element_DatePicker('doc_expiry_date');
		$doc_expiry_date->setAttrib('readonly', 'true');	
		$doc_expiry_date->setAttrib('onfocus', 'this.blur()');
		$doc_expiry_date->setOptions(array('class' => 'brdr_none'));		
		// Expiration Date should be greater than today's date...
		
		
		// issuing authority name...
		$issueAuth_name = new Zend_Form_Element_Text('issuingauth_name');
        $issueAuth_name->setAttrib('maxLength', 50);
        $issueAuth_name->addFilter(new Zend_Filter_StringTrim());
		$issueAuth_name->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[a-zA-Z][a-zA-Z0-9\-\&\s]+$/i',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid name.'
								 )
							 )
						 )
					 )); 
		//issuing authority country.....
		$country = new Zend_Form_Element_Select('issuingauth_country');
		$country->setAttrib('onchange', 'displayParticularState(this,"","issuingauth_state","")');
		$country->setRegisterInArrayValidator(false);
		
		//issuing authority state.....
        $state = new Zend_Form_Element_Select('issuingauth_state');
        $state->setAttrib('onchange', 'displayParticularCity(this,"","issuingauth_city","")');
        $state->setRegisterInArrayValidator(false);
		$state->addMultiOption('','Select State');
        
	
		
		//issuing authority city.....
		$city = new Zend_Form_Element_Select('issuingauth_city');
        $city->setRegisterInArrayValidator(false);
        $city->addMultiOption('','Select City');
		
		
		
		//issuing authority postal code .....
                   
		$issuingAuth_pcode = new Zend_Form_Element_Text('issuingauth_postalcode');
		$issuingAuth_pcode->addFilter(new Zend_Filter_StringTrim());
		$issuingAuth_pcode->setAttrib("maxlength",10);
		$issuingAuth_pcode->addValidators(array(array('StringLength',false,
                                  array('min' => 3,
                                  		'max' => 10,
                                        'messages' => array(
                                        Zend_Validate_StringLength::TOO_LONG =>
                                        'Issuing authority postal code must contain at most %max% characters.',
                                        Zend_Validate_StringLength::TOO_SHORT =>
                                        'Issuing authority postal code must contain at least %min% characters.')))));
		$issuingAuth_pcode->addValidators(array(
					 array(
						 'validator'   => 'Regex',
						 'breakChainOnFailure' => true,
						 'options'     => array( 
						 
						 'pattern'=>'/^(?!0{3})[0-9a-zA-Z]+$/',
							 'messages' => array(
									 'regexNotMatch'=>'Please enter valid postal code.'
							 )
						 )
					 )
				 )); 
			
         // Form Submit ......... 
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submitbutton');
        $submit->setLabel('Save');

        $this->addElements(array($id,$userid,$issuingauthflag,$docType,$doc_issue_date,$doc_expiry_date,$issueAuth_name,$country,$state,$city,$issuingAuth_pcode,$emptyflag,$submit));
		
		$this->setElementDecorators(array('ViewHelper')); 
		
		$this->setElementDecorators(array(
                    'UiWidgetElement',
        ),array('doc_issue_date','doc_expiry_date'));
		
        }
}
?>