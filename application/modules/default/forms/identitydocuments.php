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
		
		/*$identitydocuments = new Zend_Form_Element_MultiCheckbox('identitydoc');
		$identitydocuments->setLabel('Identity Documents');
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
							'pattern'=> '/^(?=.*[a-zA-Z])([^ ][a-zA-Z0-9\-\s]*)$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid document name.'
                           )
        	));*/
        
        $documentname = new Zend_Form_Element_Text('document_name');
		$documentname->setAttrib('maxlength',50);
		$documentname->setLabel('Document Name');
		$documentname->setRequired(true);
        $documentname->addValidator('NotEmpty', false, array('messages' => 'Please enter document name.'));
		$documentname->addValidator("regex",true,array(
							'pattern'=> '/^(?=.*[a-zA-Z])([^ ][a-zA-Z0-9\-\s]*)$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid document name.'
                           )
        	));
		$documentname->addValidator(new Zend_Validate_Db_NoRecordExists(
	                                            array(  'table'=>'main_identitydocuments',
	                                                     'field'=>'document_name',
	                                                     'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" AND isactive=1',    
	
	                                                      ) ) );
		$documentname->getValidator('Db_NoRecordExists')->setMessage('Document name already exists.');
		
		$mandatory = new Zend_Form_Element_Radio('mandatory');
		$mandatory->setLabel("Mandatory");
        $mandatory->addMultiOptions(array(
										        '1' => 'Yes',
										        '0' => 'No',
    									   ));
		$mandatory->setRequired(true);
        $mandatory->addValidator('NotEmpty', false, array('messages' => 'Please select mandatory.'));    									   
		$mandatory->setSeparator('');    
		$mandatory->setValue(0);									   
		$mandatory->setRegisterInArrayValidator(false);
		
		$expiry = new Zend_Form_Element_Radio('expiry');
		$expiry->setLabel("Expiry");
        $expiry->addMultiOptions(array(
										        '1' => 'Yes',
										        '0' => 'No',
    									   ));
		$expiry->setRequired(true);
        $expiry->addValidator('NotEmpty', false, array('messages' => 'Please select expiry.'));    									   
		$expiry->setSeparator('');    
		$expiry->setValue(0);									   
		$expiry->setRegisterInArrayValidator(false);

		
		$description = new Zend_Form_Element_Textarea('description');
		$description->setLabel("Description");
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');
        	
		
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 $this->addElements(array($id,$documentname,$mandatory,$expiry,$description,$submit));
        $this->setElementDecorators(array('ViewHelper')); 
	}
}