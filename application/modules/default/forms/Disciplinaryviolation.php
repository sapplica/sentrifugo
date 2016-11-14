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

class Default_Form_Disciplinaryviolation extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		//$this->setAttrib('action',BASE_URL.'language/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'disciplinaryviolation');

        $id = new Zend_Form_Element_Hidden('id');
        
		$disciplinaryviolation = new Zend_Form_Element_Text('violationname');
		$disciplinaryviolation->setLabel("Violation Type");
		$disciplinaryviolation->setAttrib('maxLength', 30);
		$disciplinaryviolation->addFilter(new Zend_Filter_StringTrim());
		$disciplinaryviolation->setRequired(true);
        $disciplinaryviolation->addValidator('NotEmpty', false, array('messages' => 'Please enter violation type.'));
		$disciplinaryviolation->addValidator("regex",true,array(                           
                          'pattern'=> '/^(?=.*[a-zA-Z])([^ ][a-zA-Z0-9 ]*)$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid violation type.'
                           )
        	));
        $disciplinaryviolation->addValidator(new Zend_Validate_Db_NoRecordExists(
	                                            array(  'table'=>'main_disciplinary_violation_types',
	                                                     'field'=>'violationname',
	                                                     'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" AND isactive=1',    
	
	                                                      ) ) );
		$disciplinaryviolation->getValidator('Db_NoRecordExists')->setMessage('Violation type already exists.');	

		$description = new Zend_Form_Element_Textarea('description');
		$description->setLabel("Description");
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 $this->addElements(array($id,$disciplinaryviolation,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}