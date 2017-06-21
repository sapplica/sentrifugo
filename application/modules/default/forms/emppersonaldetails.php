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

        $maritalstatusid = new Zend_Form_Element_Select('maritalstatusid');
		$maritalstatusid->addMultiOption('','Select Marital Status');
        $maritalstatusid->setRegisterInArrayValidator(false);
		
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

        $dob = new ZendX_JQuery_Form_Element_DatePicker('dob');
		$dob->setOptions(array('class' => 'brdr_none'));	
		$dob->setAttrib('readonly', 'true');
		$dob->setAttrib('onfocus', 'this.blur()');
		//DOB should not be current date....
		
		/*
        $celebrated_dob = new ZendX_JQuery_Form_Element_DatePicker('celebrated_dob');
		$celebrated_dob->setOptions(array('class' => 'brdr_none'));	
		$celebrated_dob->setAttrib('readonly', 'true');
		$celebrated_dob->setAttrib('onfocus', 'this.blur()');
		*/
		
        $bloodgroup = new Zend_Form_Element_Text('bloodgroup');
    	$bloodgroup->setAttrib('size',5); 
		$bloodgroup->setAttrib('maxlength',10);	
    	
				
		/*$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');*/
		
		$submitadd = new Zend_Form_Element_Button('submitbutton');
		$submitadd->setAttrib('id', 'submitbuttons');
		$submitadd->setAttrib('onclick', 'validatedocumentonsubmit(this)');
		$submitadd->setLabel('Save');
		
		$this->addElements(array($id,$userid,$genderid,$maritalstatusid,$nationalityid,$ethniccodeid,$racecodeid,$languageid,$dob,$bloodgroup,$submitadd));
        $this->setElementDecorators(array('ViewHelper')); 
 		 $this->setElementDecorators(array(
                    'UiWidgetElement',
        ),array('dob')); 
	}
}