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

class Default_Form_Dependencydetails extends Zend_Form
{ 
	public function init()
	{
		
		$this->setMethod('post');		
        $this->setAttrib('id', 'formid');
        $this->setAttrib('name','dependencydetails');
       
		
        $id = new Zend_Form_Element_Hidden('id');
        $user_id = new Zend_Form_Element_Hidden('user_id');
	   	
		
       //Dependent Name ... 
        
        $dependent_name = new Zend_Form_Element_Text('dependent_name');
        $dependent_name->addFilter(new Zend_Filter_StringTrim());
        $dependent_name->setRequired(true);
		$dependent_name->setAttrib("maxlength",50);
        $dependent_name->addValidator('NotEmpty', false, array('messages' => 'Please enter dependent name.'));
		
		$dependent_name->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[a-zA-Z\s]+$/i',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter only alphabets.'
								 )
							 )
						 )
					 )); 
		
		//Disablity Type 
		
		$dependent_relation = new Zend_Form_Element_Select('dependent_relation');
       	$dependent_relation->setRequired(true)->addErrorMessage('Please select dependent relation.');
		$dependent_relation->addValidator('NotEmpty', false, array('messages' => 'Please select dependent relation.')); 
		
		//dependent_custody....
		$dependent_custody = new Zend_Form_Element_Select('dependent_custody');
		$dependent_custody->addValidator('NotEmpty', false, array('messages' => 'Please select dependent custody code.')); 
		$dependent_custody->setRequired(true)->addErrorMessage('Please select dependent custody code.');
		$dependent_custody->addMultiOptions(array(''=>'Select Dependent Custody Code',
						        'both parents'=>'Both Parents',
								'former spouse'=>'Former Spouse',
								'subscriber only'=>'Subscriber Only',
								'other Or unknown'=>'Other Or Unknown'
									));
		//Dependent DOB...
        $dependent_dob = new ZendX_JQuery_Form_Element_DatePicker('dependent_dob');
		$dependent_dob->setOptions(array('class' => 'brdr_none'));	
		$dependent_dob->setAttrib('onchange', 'calcDays("dependent_dob","",this,1)');
		$dependent_dob->setRequired(true);
		$dependent_dob->setAttrib('readonly', 'true');
		$dependent_dob->setAttrib('onfocus', 'this.blur()');
        $dependent_dob->addValidator('NotEmpty', false, array('messages' => 'Please select date.'));		
		
		//dependent_age ... 
		$dependent_age = new Zend_Form_Element_Text('dependent_age');
      	$dependent_age->addFilter(new Zend_Filter_StringTrim());
        
		$dependent_age->setAttrib("maxlength",3);
		$dependent_age->setAttrib('readonly', 'true');
		$dependent_age->setAttrib('onfocus', 'this.blur()');
		
      
		//Form Submit....
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		$this->addElements(array($id,$user_id,$dependent_name,$dependent_relation,$dependent_custody,$dependent_dob,$dependent_age,$submit));
		$this->setElementDecorators(array('ViewHelper')); 
       $this->setElementDecorators(array(
                    'UiWidgetElement',
        ),array('dependent_dob'));
		
        
	}
	
}
         