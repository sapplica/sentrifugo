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

class Default_Form_identitydocuments extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'identitydocuments');


        $id = new Zend_Form_Element_Hidden('id');
		
		$identitydocuments = new Zend_Form_Element_MultiCheckbox('identitydoc');
		$identitydocuments->setLabel('Identity Documents');
	    //$identitydocuments->setRegisterInArrayValidator(false);
		$identitydocuments->setMultiOptions(array(
                           	'1'=>'Passport',
							'2'=>'SSN',
							'3'=>'Aadhaar',
                            '4'=>'Pan Card',
                            '5'=>'Driving License',
							));
		$identitydocuments->setRequired(true);
		$identitydocuments->addValidator('NotEmpty', false, array('messages' => 'Please select at least one identity document type.'));
		$identitydocuments->setSeparator(PHP_EOL);	
		
		$othercheck = new Zend_Form_Element_Checkbox('othercheck');
		$othercheck->setLabel('Other Documents');
		$othercheck->setAttrib('onclick', 'displayotherdocumentdiv(this)');
	 
		
		$otherdocument = new Zend_Form_Element_Text('otherdocument');
		$otherdocument->setAttrib('maxlength',50);
		$otherdocument->setAttrib('onblur', 'validate_otherdocument(this)');
		$otherdocument->setLabel('Document Name');
		$otherdocument->addValidator("regex",true,array(
                            //'pattern'=>'/^[a-zA-Z][a-zA-Z0-9\-\. ]*$/', 
							'pattern'=> '/^(?=.*[a-zA-Z])([^ ][a-zA-Z0-9\-\s]*)$/',
                           //'pattern'=>"!~^?%`",
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid document name.'
                           )
        	));
		//$otherdocument->addFilters(array('StringTrim'));
		
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 //$this->addElements(array($id,$natinalityid,$dateformatid,$timeformatid,$timezoneid,$currencyid,$passwordid,$description,$submit));
		 $this->addElements(array($id,$identitydocuments,$othercheck,$otherdocument,$submit));
        $this->setElementDecorators(array('ViewHelper')); 
	}
}