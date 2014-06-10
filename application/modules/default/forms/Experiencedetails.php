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

class Default_Form_Experiencedetails extends Zend_Form
{ 
	public function init()
	{
		
		$this->setMethod('post');		
        $this->setAttrib('id', 'formid');
        $this->setAttrib('name','experiencedetails');
        //$this->setAttrib('action',DOMAIN.'experiencedetails/addpopup/');
		
        $id = new Zend_Form_Element_Hidden('id');
		$user_id = new Zend_Form_Element_Hidden('user_id');
		
		
        //company_name ... 
        
        $company_name = new Zend_Form_Element_Text('comp_name');
        $company_name->addFilter(new Zend_Filter_StringTrim());
        $company_name->setRequired(true);
		$company_name->setAttrib("maxlength",50);
        $company_name->addValidator('NotEmpty', false, array('messages' => 'Please enter company name.'));
		/*$company_name->addValidators(array(array('StringLength',false,
									  array('max' => 50,
											'messages' => array(
											Zend_Validate_StringLength::TOO_LONG =>
											'Company name should be maximum of %max% characters',
											)))));*/
		$company_name->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[a-zA-Z][a-zA-Z0-9\-\s]+$/i',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid company name.'
								 )
							 )
						 )
					 )); 
		
		//Company website ... 
        
		$comp_website = new Zend_Form_Element_Text('comp_website');
		//$comp_website->addFilters(array('StringTrim', 'StripTags'));
		$comp_website->addFilter(new Zend_Filter_StringTrim());
		$comp_website->setAttrib('maxLength', 50);
		$comp_website->setRequired(true);
		$comp_website->addValidator('NotEmpty', false, array('messages' => 'Please enter company website.')); 
		//$comp_website->addValidator(new Zend_Validate_Uri());
		$comp_website->addValidator("regex",true,array(                           
                           'pattern'=>'/^(http:\/\/www|https:\/\/www|www)+\.([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,3})$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid URL.'
                           )
        	));
		
		       
       // designation...
        $designation = new Zend_Form_Element_Text('designation');
        $designation->addFilter(new Zend_Filter_StringTrim());
		$designation->setAttrib("maxlength",50);
        $designation->setRequired(true);
        $designation->addValidator('NotEmpty', false, array('messages' => 'Please enter designation.'));
        /*$designation->addValidators(array(array('StringLength',false,
									  array('max' => 50,
											'messages' => array(
											Zend_Validate_StringLength::TOO_LONG =>
											'Designation should be maximum of %max% characters',
											)))));*/
		$designation->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[a-zA-Z\.\-\s]+$/i',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid designation.'
								 )
							 )
						 )
					 )); 
        
		//from_date..
        $from_date = new ZendX_JQuery_Form_Element_DatePicker('from_date');
		//$from_date->addValidator(new Zend_Validate_Date(array("format" => "MM-dd-yyyy")));		
		//$from_date->setOptions(array('class' => 'brdr_none'));	
		$from_date->setRequired(true);
        $from_date->addValidator('NotEmpty', false, array('messages' => 'Please select from date.'));
        $from_date->setAttrib('readonly', 'true');
        $from_date->setAttrib('onfocus', 'this.blur()');  
		
        //to_date
        $to_date = new ZendX_JQuery_Form_Element_DatePicker('to_date');
		//$to_date->addValidator(new Zend_Validate_Date(array("format" => "MM-dd-yyyy")));		
		//$to_date->setOptions(array('class' => 'brdr_none'));	
		$to_date->setRequired(true);
        $to_date->addValidator('NotEmpty', false, array('messages' => 'Please select to date.'));
        $to_date->setAttrib('readonly', 'true');	
		$to_date->setAttrib('onfocus', 'this.blur()');  
		
        // reason_for_leaving ....
		$reason_for_leaving = new Zend_Form_Element_Textarea('reason_for_leaving');
        $reason_for_leaving->setAttrib('rows', 10);
        $reason_for_leaving->setAttrib('cols', 50);	
		$reason_for_leaving->setRequired(true);
        $reason_for_leaving->addValidator('NotEmpty', false, array('messages' => 'Please enter reason for leaving.'));
		/*$reason_for_leaving->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[a-zA-Z0-9\-\,\.\&\:\"\'\s]+$/i',
								 'messages' => array(
										 'regexNotMatch'=>'Please start reason with alphabets.'
								 )
							 )
						 )
					 ));*/
        
		// Reference  person Details....
		
		//Referer name ....
		$reference_name = new Zend_Form_Element_Text('reference_name');
        $reference_name->addFilter(new Zend_Filter_StringTrim());
        $reference_name->setRequired(true);
		$reference_name->setAttrib("maxlength",50);
        $reference_name->addValidator('NotEmpty', false, array('messages' => 'Please enter referrer name.'));
		/*$reference_name->addValidators(array(array('StringLength',false,
									  array('max' => 50,
											'messages' => array(
											Zend_Validate_StringLength::TOO_LONG =>
											'Referer name should be maximum of %max% characters',
											)))));*/
		$reference_name->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[a-zA-Z][a-zA-Z0-9\-\s]+$/i',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid referrer name.'
								 )
							 )
						 )
					 )); 
		//Referer mobile number ....
		$reference_contact = new Zend_Form_Element_Text('reference_contact');
        $reference_contact->addFilter(new Zend_Filter_StringTrim());
        $reference_contact->setRequired(true);
		$reference_contact->setAttrib("maxlength",10);
        $reference_contact->addValidator('NotEmpty', false, array('messages' => 'Please enter referrer contact.'));
		$reference_contact->addValidators(array(array('StringLength',false,
									  array('min' => 10,
											'max' => 10,
											'messages' => array(
											Zend_Validate_StringLength::TOO_LONG =>
											'Referrer contact must contain at most of 10 numbers.',
											Zend_Validate_StringLength::TOO_SHORT =>
											'Referrer contact  must contain at least of %min% characters.'
											)))));
											
		$reference_contact->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[0-9]+$/i',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter only numbers.'
								 )
							 )
						 )
					 )); 
		
		
		 //Referer Email....
         $reference_email = new Zend_Form_Element_Text('reference_email');
         $reference_email->addFilters(array('StringTrim', 'StripTags'));
         $reference_email->setRequired(true);
		 $reference_email->setAttrib("maxlength",50);
         $reference_email->addValidator('NotEmpty', false, array('messages' => 'Please enter referrer email.')); 
         /*$reference_email->addValidator('EmailAddress', true, array('messages'=>array(
									'emailAddressInvalid'=>'Please enter valid email.',
									'emailAddressInvalidFormat'=>'Please enter valid email.',
									'emailAddressInvalidHostname'=>'Please enter valid email.',
									'emailAddressInvalidMxRecord'=>'Please enter valid email.',
									'emailAddressInvalidSegment'=>'Please enter valid email.',
									'emailAddressDotAtom'=>'Please enter valid email.',
									'emailAddressQuotedString'=>'Please enter valid email.',
									'emailAddressInvalidLocalPart'=>'Please enter valid email.',
									'emailAddressLengthExceeded'=>'Please enter valid email.'
									)));*/
         $reference_email->addValidator("regex",true,array(
                           // 'pattern'=>'/^(?!.*\.{2})[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$\//gi',                            		   
						    'pattern'=>'/^(?!.*\.{2})[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/',                            
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid email.'
                           )
        	));
        
         
	   //Form Submit....
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		$this->addElements(array($id,$user_id,$company_name, $comp_website,$designation,$from_date,$to_date,$reason_for_leaving,$reference_name,$reference_contact,$reference_email,
							$submit));
       $this->setElementDecorators(array('ViewHelper')); 
		
		$this->setElementDecorators(array(
                    'UiWidgetElement',
        ),array('from_date','to_date'));
		
		
        
	}
	
}
         