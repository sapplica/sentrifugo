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

class Default_Form_Identitycodes extends Zend_Form
{
    public function init()
    {
		$this->setMethod('post');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'identitycodes');


       $id = new Zend_Form_Element_Hidden('id');
		
		//Employee Code
		$empCode = new Zend_Form_Element_Text('employee_code');
		
        $empCode->addFilter(new Zend_Filter_StringTrim());
		$empCode->setAttrib('maxLength', 5);
		$empCode->setRequired(true);
        $empCode->addValidator('NotEmpty', false, array('messages' => 'Please enter employee code.')); 
		$empCode->addValidators(array(array('StringLength',false,
								array('min' => 1,
									  'max' => 5,
									  'messages' => array(
									   Zend_Validate_StringLength::TOO_LONG =>
									  'Employee code must contain at most %max% characters.',
									  Zend_Validate_StringLength::TOO_SHORT =>
									  'Employee code must contain at least %min% characters.')))));
		$empCode->addValidators(array(
			         array(
			             'validator'   => 'Regex',
			             'breakChainOnFailure' => true,
			             'options'     => array( 
			             //'pattern' => '/^[A-Za-z]+$/',
			         	 'pattern'=> '/^[A-Za-z][a-zA-Z@\-]*$/',	
			                 'messages' => array(
			                     Zend_Validate_Regex::NOT_MATCH =>'Please enter valid employee code.'
			                 )
			             )
			         )
			     )); 
		
		// Background Agency Code
		$bgCode = new Zend_Form_Element_Text('bg_code');
	    $bgCode->addFilter(new Zend_Filter_StringTrim());
		$bgCode->setRequired(true);
		$bgCode->setAttrib('maxLength', 5);
        $bgCode->addValidator('NotEmpty', false, array('messages' => 'Please enter background agency code.')); 
		$bgCode->addValidators(array(array('StringLength',false,
								array('min' => 1,
									  'max' => 5,
									  'messages' => array(
									   Zend_Validate_StringLength::TOO_LONG =>
									  'Background Agency code must contain at most %max% characters.',
									  Zend_Validate_StringLength::TOO_SHORT =>
									  'Background Agency code must contain at least %min% characters.')))));
		$bgCode->addValidators(array(
			         array(
			             'validator'   => 'Regex',
			             'breakChainOnFailure' => true,
			             'options'     => array( 
			             'pattern'=> '/^[A-Za-z][a-zA-Z@\-]*$/',
			                 'messages' => array(
			                     Zend_Validate_Regex::NOT_MATCH =>'Please enter valid background agency code.'
			                 )
			             )
			         )
			     ));
       
	   // Vendors Code
		$vendorsCode = new Zend_Form_Element_Text('vendor_code');
       
	    $vendorsCode->addFilter(new Zend_Filter_StringTrim());
		$vendorsCode->setAttrib('maxLength', 5);
		$vendorsCode->setRequired(true);
        $vendorsCode->addValidator('NotEmpty', false, array('messages' => 'Please enter vendor code.')); 
		$vendorsCode->addValidators(array(array('StringLength',false,
								array('min' => 1,
									  'max' => 5,
									  'messages' => array(
									   Zend_Validate_StringLength::TOO_LONG =>
									  'Vendor code must contain at most %max% characters.',
									  Zend_Validate_StringLength::TOO_SHORT =>
									  'Vendor code must contain at least %min% characters.')))));
		$vendorsCode->addValidators(array(
			         array(
			             'validator'   => 'Regex',
			             'breakChainOnFailure' => true,
			             'options'     => array( 
			             'pattern'=> '/^[A-Za-z][a-zA-Z@\-]*$/',
			                 'messages' => array(
			                     Zend_Validate_Regex::NOT_MATCH =>'Please enter valid vendor code.'
			                 )
			             )
			         )
			     ));
       
	   // Staffing Code
		$staffingCode = new Zend_Form_Element_Text('staffing_code');
		$staffingCode->setAttrib('maxLength', 5);
	    $staffingCode->addFilter(new Zend_Filter_StringTrim());
		$staffingCode->setRequired(true);
        $staffingCode->addValidator('NotEmpty', false, array('messages' => 'Please enter staffing code.')); 
		$staffingCode->addValidators(array(array('StringLength',false,
								array('min' => 1,
									  'max' => 5,
									  'messages' => array(
									   Zend_Validate_StringLength::TOO_LONG =>
									  'Staffing code must contain at most %max% characters.',
									  Zend_Validate_StringLength::TOO_SHORT =>
									  'Staffing code must contain at least %min% characters.')))));
		$staffingCode->addValidators(array(
			         array(
			             'validator'   => 'Regex',
			             'breakChainOnFailure' => true,
			             'options'     => array( 
			             'pattern'=> '/^[A-Za-z][a-zA-Z@\-]*$/',
			                 'messages' => array(
			                     Zend_Validate_Regex::NOT_MATCH =>'Please enter valid staffing code.'
			                 )
			             )
			         )
			     ));
		
                
        $users_code = new Zend_Form_Element_Text('users_code');		
        $users_code->setAttrib('maxLength', 5);
        $users_code->addFilter(new Zend_Filter_StringTrim());
        $users_code->setRequired(true);
        $users_code->addValidator('NotEmpty', false, array('messages' => 'Please enter users code.')); 
        $users_code->addValidators(array(array('StringLength',false,
                                        array(
                                            'min' => 1,
                                            'max' => 5,
                                            'messages' => array(
                                                        Zend_Validate_StringLength::TOO_LONG =>'Users code must contain at most %max% characters.',
                                                        Zend_Validate_StringLength::TOO_SHORT =>'Users code must contain at least %min% characters.')
                                            )
                                        )
                                    )
                                );
        $users_code->addValidators(array(
			         array(
			             'validator'   => 'Regex',
			             'breakChainOnFailure' => true,
			             'options'     => array( 
			             'pattern'=> '/^[A-Za-z][a-zA-Z@\-]*$/',
			                 'messages' => array(
			                     Zend_Validate_Regex::NOT_MATCH =>'Please enter valid users code.'
			                 )
			             )
			         )
			     ));
        
        $requisition_code = new Zend_Form_Element_Text('requisition_code');		
        $requisition_code->setAttrib('maxLength', 5);
        $requisition_code->addFilter(new Zend_Filter_StringTrim());
        $requisition_code->setRequired(true);
        $requisition_code->addValidator('NotEmpty', false, array('messages' => 'Please enter requisition code.')); 
        $requisition_code->addValidators(array(array('StringLength',false,
                                        array(
                                            'min' => 1,
                                            'max' => 5,
                                            'messages' => array(
                                                        Zend_Validate_StringLength::TOO_LONG =>'Requisition code must contain at most %max% characters.',
                                                        Zend_Validate_StringLength::TOO_SHORT =>'Requisition code must contain at least %min% characters.')
                                            )
                                        )
                                    )
                                );
        $requisition_code->addValidators(array(
			         array(
			             'validator'   => 'Regex',
			             'breakChainOnFailure' => true,
			             'options'     => array( 
			             'pattern'=> '/^[A-Za-z][a-zA-Z@\-]*$/',
			                 'messages' => array(
			                     Zend_Validate_Regex::NOT_MATCH =>'Please enter valid requisition code.'
			                 )
			             )
			         )
			     ));
		
                
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submitbutton');
        $submit->setLabel('Save');

        $this->addElements(array($id,$empCode,$bgCode,$vendorsCode,$staffingCode,$submit,$users_code,$requisition_code));
        $this->setElementDecorators(array('ViewHelper')); 
    }
}