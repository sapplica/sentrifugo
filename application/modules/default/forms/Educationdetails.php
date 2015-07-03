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

class Default_Form_Educationdetails extends Zend_Form
{ 
	public function init()
	{
		
		$this->setMethod('post');		
        $this->setAttrib('id', 'formid');
        $this->setAttrib('name','educationdetails');
        $this->setAttrib('action',BASE_URL.'educationdetails/addpopup/');
		
        $id = new Zend_Form_Element_Hidden('id');
		$user_id = new Zend_Form_Element_Hidden('user_id');
		
		
		$educationlevel = new Zend_Form_Element_Select('educationlevel');
		$educationlevel->setLabel("Education Level");
    	$educationlevel->setRegisterInArrayValidator(false);
		$educationlevel->setRequired(true);
		$educationlevel->addValidator('NotEmpty', false, array('messages' => 'Please select educational level.'));
		
        //institution_name ... 
        
        $institution_name = new Zend_Form_Element_Text('institution_name');
        $institution_name->addFilter(new Zend_Filter_StringTrim());
        $institution_name->setRequired(true);
		$institution_name->setAttrib("maxlength",50);
        $institution_name->addValidator('NotEmpty', false, array('messages' => 'Please enter institution name.'));
		
		$institution_name->addValidators(array(
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
		
		//course ... 
        
        $course = new Zend_Form_Element_Text('course');
        $course->addFilter(new Zend_Filter_StringTrim());
        $course->setRequired(true);
		$course->setAttrib("maxlength",50);
        $course->addValidator('NotEmpty', false, array('messages' => 'Please enter course name.'));
		
		$course->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[a-zA-Z0-9\-\.\s]+$/i',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid course name.'
								 )
							 )
						 )
					 )); 
		
		
		//from_date..
        $from_date = new ZendX_JQuery_Form_Element_DatePicker('from_date');
		
		$from_date->setOptions(array('class' => 'brdr_none'));	
		$from_date->setRequired(true);
		$from_date->setAttrib('readonly', 'true');
		$from_date->setAttrib('onfocus', 'this.blur()');  
        $from_date->addValidator('NotEmpty', false, array('messages' => 'Please select from date.'));
        
        //to_date
        $to_date = new ZendX_JQuery_Form_Element_DatePicker('to_date');
		
		$to_date->setOptions(array('class' => 'brdr_none'));	
		$to_date->setRequired(true);
		$to_date->setAttrib('readonly', 'true');
		$to_date->setAttrib('onfocus', 'this.blur()');  
        $to_date->addValidator('NotEmpty', false, array('messages' => 'Please select to date.'));
        
        // percentage...
        $percentage = new Zend_Form_Element_Text('percentage');
        $percentage->addFilter(new Zend_Filter_StringTrim());
        $percentage->setRequired(true);
		$percentage->setAttrib("maxlength",2);
        $percentage->addValidator('NotEmpty', false, array('messages' => 'Please enter percentage.'));
		$percentage->addValidator("regex",true,array(                           
                           
						   'pattern'=>'/^[0-9]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter only numbers.'
                           )
        	));
        
        
       
       	
	   //Form Submit....
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		$this->addElements(array($id,$user_id,$educationlevel,$from_date,$to_date,$percentage,$course,$institution_name,
							$submit));
       $this->setElementDecorators(array('ViewHelper')); 
		
		$this->setElementDecorators(array(
                    'UiWidgetElement',
        ),array('from_date','to_date'));
		
		
        
	}
	
}
         