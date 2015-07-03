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

class Default_Form_workeligibilitydoctypes extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'workeligibilitydoctypes/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'workeligibilitydoctypes');


        $id = new Zend_Form_Element_Hidden('id');
		
		$documenttype = new Zend_Form_Element_Text('documenttype');
        $documenttype->setAttrib('maxLength', 50);
        $documenttype->setRequired(true);
        $documenttype->addValidator('NotEmpty', false, array('messages' => 'Please enter document type.'));  
		$documenttype->addValidator("regex",true,array(
									'pattern'=> '/^(?=.*[a-zA-Z])([^ ][a-zA-Z0-9\-\s]*)$/',
								   'messages'=>array(
									   'regexNotMatch'=>'Please enter valid document type.'
								   )
					));
		$documenttype->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_workeligibilitydoctypes',
                                                        'field'=>'documenttype',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $documenttype->getValidator('Db_NoRecordExists')->setMessage('Document type already exists.');	

        $issuingauthority = new Zend_Form_Element_Select('issuingauthority');
        $issuingauthority->setRegisterInArrayValidator(false);
		$issuingauthority->setMultiOptions(array(
                            ''=>'Select issuing authority',		
							'1'=>'Country' ,
							'2'=>'State',
							'3'=>'City',
							));
        $issuingauthority->setRequired(true);
		$issuingauthority->addValidator('NotEmpty', false, array('messages' => 'Please select issuing authority.'));		
   	
		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 $this->addElements(array($id,$documenttype,$issuingauthority,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}