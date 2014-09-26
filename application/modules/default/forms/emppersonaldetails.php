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

class Default_Form_emppersonaldetails extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'emppersonaldetails');
		
		$id = new Zend_Form_Element_Hidden('id');
				
		$userid = new Zend_Form_Element_Hidden('user_id');
				
		$genderid = new Zend_Form_Element_Select('genderid');
		$genderid->addMultiOption('','Select Gender');
    	$genderid->setRegisterInArrayValidator(false);
		$genderid->setRequired(true);
		$genderid->addValidator('NotEmpty', false, array('messages' => 'Please select gender.'));

        $maritalstatusid = new Zend_Form_Element_Select('maritalstatusid');
		$maritalstatusid->addMultiOption('','Select Marital Status');
        $maritalstatusid->setRegisterInArrayValidator(false);
		$maritalstatusid->setRequired(true);
		$maritalstatusid->addValidator('NotEmpty', false, array('messages' => 'Please select marital status.')); 
		
		$ethniccodeid = new Zend_Form_Element_Select('ethniccodeid');
		$ethniccodeid->addMultiOption('','Select Ethnic Code');
		$ethniccodeid->setLabel('Ethnic Code');
        $ethniccodeid->setRegisterInArrayValidator(false);
        
		

        $racecodeid = new Zend_Form_Element_Select('racecodeid');
		$racecodeid->addMultiOption('','Select Race Code');
		$racecodeid->setLabel('Race Code');
        $racecodeid->setRegisterInArrayValidator(false);
        
		
        
        $languageid = new Zend_Form_Element_Select('languageid');
		$languageid->addMultiOption('','Select Language');
		$languageid->setLabel('Language');
        $languageid->setRegisterInArrayValidator(false);
        
	

        $nationalityid = new Zend_Form_Element_Select('nationalityid');
		$nationalityid->addMultiOption('','Select Nationality');
        $nationalityid->setRegisterInArrayValidator(false);
        $nationalityid->setRequired(true);
		$nationalityid->addValidator('NotEmpty', false, array('messages' => 'Please select nationality.'));    		

        $dob = new ZendX_JQuery_Form_Element_DatePicker('dob');
		$dob->setOptions(array('class' => 'brdr_none'));	
		$dob->setRequired(true);
		$dob->setAttrib('readonly', 'true');
		$dob->setAttrib('onfocus', 'this.blur()');
        $dob->addValidator('NotEmpty', false, array('messages' => 'Please select date of birth.'));	
		//DOB should not be current date....
		
		
		
        $celebrated_dob = new ZendX_JQuery_Form_Element_DatePicker('celebrated_dob');
		$celebrated_dob->setOptions(array('class' => 'brdr_none'));	
		$celebrated_dob->setAttrib('readonly', 'true');
		$celebrated_dob->setAttrib('onfocus', 'this.blur()');
		
		$identitydocumentsModel = new Default_Model_Identitydocuments();	
	    $identityDocumentArr = $identitydocumentsModel->getIdentitydocumnetsrecord();
		
		$passport = new Zend_Form_Element_Text('passport');
		if(!empty($identityDocumentArr) && $identityDocumentArr[0]['passport'] == 1)
		   {
			 $passport->setRequired(true);
			 $passport->addValidator('NotEmpty', false, array('messages' => 'Please enter passport number.'));
		   }
		$passport->setAttrib('maxlength',20);
    	$passport->addValidator("regex",true,array(
                            'pattern'=>'/^[(a-zA-Z0-9)]+$/', 
                           
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter only alphanumeric characters.'
                           )
        	));
		
		$pancard_number = new Zend_Form_Element_Text('pancard_number');
		if(!empty($identityDocumentArr) && $identityDocumentArr[0]['pancard'] == 1)
		   {
		      $pancard_number->setRequired(true);
			  $pancard_number->addValidator('NotEmpty', false, array('messages' => 'Please enter pancard number.'));
		   }
		$pancard_number->setAttrib('maxlength',15);
    	$pancard_number->addValidator("regex",true,array(
                            'pattern'=>'/^[(a-zA-Z0-9)]+$/', 
                           
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter only alphanumeric characters.'
                           )
        	));
			
		$drivinglicence_number = new Zend_Form_Element_Text('drivinglicence_number');
		if(!empty($identityDocumentArr) && $identityDocumentArr[0]['drivinglicense'] == 1)
		   {
		       $drivinglicence_number->setRequired(true);
			   $drivinglicence_number->addValidator('NotEmpty', false, array('messages' => 'Please enter driving license number.'));
		   }
		$drivinglicence_number->setAttrib('maxlength',20);
    	$drivinglicence_number->addValidator("regex",true,array(
                            'pattern'=>'/^[(a-zA-Z0-9)]+$/', 
                           
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter only alphanumeric characters.'
                           )
        	));	
		
        $SSN_number = new Zend_Form_Element_Text('SSN_number');
		if(!empty($identityDocumentArr) && $identityDocumentArr[0]['ssn'] == 1)
		   {
		      $SSN_number->setRequired(true);
			  $SSN_number->addValidator('NotEmpty', false, array('messages' => 'Please enter social security number.'));
		   }
     	$SSN_number->setAttrib('maxlength',20);
    	$SSN_number->addValidator("regex",true,array(
                            'pattern'=>'/^[(a-zA-Z0-9)]+$/', 
                           
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter only alphanumeric characters.'
                           )
        	));

        $adhar_number = new Zend_Form_Element_Text('adhar_number');
		if(!empty($identityDocumentArr) && $identityDocumentArr[0]['aadhaar'] == 1)
		   {
		      $adhar_number->setRequired(true);
			  $adhar_number->addValidator('NotEmpty', false, array('messages' => 'Please enter aadhar number.'));
		   }
        $adhar_number->setAttrib('maxlength',20);		
    	$adhar_number->addValidator("regex",true,array(
                            'pattern'=>'/^[(a-zA-Z0-9)]+$/', 
                           
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter only alphanumeric characters.'
                           )
        	));
			
		$otheridentity = new Zend_Form_Element_Text('otheridentity');
		if(!empty($identityDocumentArr) && $identityDocumentArr[0]['others']!='')
		   {
		      $otheridentity->setRequired(true);
			  $otheridentity->addValidator('NotEmpty', false, array('messages' => 'Please enter '.strtolower($identityDocumentArr[0]['others']).'.'));
		   }
    	$otheridentity->setAttrib('maxlength',30);
    	$otheridentity->addValidator("regex",true,array(
                            'pattern'=>'/^[(a-zA-Z0-9)]+$/', 
                           
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter only alphanumeric characters.'
                           )
        	));	
       
        $bloodgroup = new Zend_Form_Element_Text('bloodgroup');
    	$bloodgroup->setAttrib('size',5); 
		$bloodgroup->setAttrib('maxlength',10);	
    	
				
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');
		
		$this->addElements(array($id,$userid,$genderid,$maritalstatusid,$nationalityid,$ethniccodeid,$racecodeid,$languageid,$dob,$celebrated_dob,$passport,$pancard_number,$drivinglicence_number,$SSN_number,$adhar_number,$bloodgroup,$otheridentity,$submit));
        $this->setElementDecorators(array('ViewHelper')); 
 		 $this->setElementDecorators(array(
                    'UiWidgetElement',
        ),array('dob','celebrated_dob')); 
	}
}