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

class Default_Form_employeeoncalltypes extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'employeeoncalltypes/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'employeeoncalltypes');


        $id = new Zend_Form_Element_Hidden('id');
		
		$oncalltype = new Zend_Form_Element_Text('oncalltype');
        $oncalltype->setAttrib('maxLength', 50);
        $oncalltype->addFilter(new Zend_Filter_StringTrim());
        $oncalltype->setRequired(true);
        $oncalltype->addValidator('NotEmpty', false, array('messages' => 'Please enter oncall type.'));
		$oncalltype->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_employeeoncalltypes',
                                                        'field'=>'oncalltype',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $oncalltype->getValidator('Db_NoRecordExists')->setMessage('On call type already exists.'); 
        
        $numberofdays = new Zend_Form_Element_Text('numberofdays');
        $numberofdays->setAttrib('maxLength', 2);
        $numberofdays->addFilter(new Zend_Filter_StringTrim());
        $numberofdays->setRequired(true);
        $numberofdays->addValidator('NotEmpty', false, array('messages' => 'Please enter number of days.')); 
		$numberofdays->addValidator("regex",true,array(
                           'pattern'=>'/^[0-9]+$/', 
                          
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter only numbers.'
                           )
        	));  		
		
		$oncallcode = new Zend_Form_Element_Text('oncallcode');
        $oncallcode->setAttrib('maxLength', 50);
        $oncallcode->addFilter(new Zend_Filter_StringTrim());
		$oncallcode->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_employeeoncalltypes',
                                                        'field'=>'oncallcode',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $oncallcode->getValidator('Db_NoRecordExists')->setMessage('On call Code already exists.'); 
        
		$oncallpreallocated = new Zend_Form_Element_Select('oncallpreallocated');
		$oncallpreallocated->setRegisterInArrayValidator(false);
		$oncallpreallocated->setMultiOptions(array(							
							'1'=>'Yes' ,
							'2'=>'No',
							));
		
        $oncallpredeductable = new Zend_Form_Element_Select('oncallpredeductable');
		$oncallpredeductable->setRegisterInArrayValidator(false);
		$oncallpredeductable->setMultiOptions(array(							
							'1'=>'Yes' ,
							'2'=>'No',
							));		
          	
		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 $this->addElements(array($id,$oncalltype,$numberofdays,$oncallcode,$oncallpreallocated,$oncallpredeductable,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}