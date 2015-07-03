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

class Default_Form_employeeleavetypes extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'employeeleavetypes/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'employeeleavetypes');


        $id = new Zend_Form_Element_Hidden('id');
		
		$leavetype = new Zend_Form_Element_Text('leavetype');
        $leavetype->setAttrib('maxLength', 50);
        $leavetype->addFilter(new Zend_Filter_StringTrim());
        $leavetype->setRequired(true);
        $leavetype->addValidator('NotEmpty', false, array('messages' => 'Please enter leave type.'));
		$leavetype->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 
							 'pattern'=> '/^(?=.*[a-zA-Z])([^ ][a-zA-Z0-9\-\s]*)$/',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid leave type.'
								 )
							 )
						 )
					 )); 
		$leavetype->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_employeeleavetypes',
                                                        'field'=>'leavetype',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $leavetype->getValidator('Db_NoRecordExists')->setMessage('Leave type already exists.'); 
        
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
		
		$leavecode = new Zend_Form_Element_Text('leavecode');
        $leavecode->setAttrib('maxLength', 50);
        $leavecode->addFilter(new Zend_Filter_StringTrim());
		$leavecode->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[a-zA-Z][a-zA-Z0-9\_\s]+$/i',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid leave short code.'
								 )
							 )
						 )
					 ));
		$leavecode->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_employeeleavetypes',
                                                        'field'=>'leavecode',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $leavecode->getValidator('Db_NoRecordExists')->setMessage('Leave Code already exists.'); 
        
		$leavepreallocated = new Zend_Form_Element_Select('leavepreallocated');
		$leavepreallocated->setRegisterInArrayValidator(false);
		$leavepreallocated->setMultiOptions(array(							
							'1'=>'Yes' ,
							'2'=>'No',
							));
		
        $leavepredeductable = new Zend_Form_Element_Select('leavepredeductable');
		$leavepredeductable->setRegisterInArrayValidator(false);
		$leavepredeductable->setMultiOptions(array(							
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

		 $this->addElements(array($id,$leavetype,$numberofdays,$leavecode,$leavepreallocated,$leavepredeductable,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}