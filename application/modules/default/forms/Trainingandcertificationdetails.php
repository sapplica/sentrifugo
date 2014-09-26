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

class Default_Form_Trainingandcertificationdetails extends Zend_Form
{ 
	public function init()
	{
		
		$this->setMethod('post');		
        $this->setAttrib('id', 'formid');
        $this->setAttrib('name','trainingandcertificationdetails');
        
		
        $id = new Zend_Form_Element_Hidden('id');
		$user_id = new Zend_Form_Element_Hidden('user_id');
		
		
        //course_name ... 
        
        $course_name = new Zend_Form_Element_Text('course_name');
        $course_name->addFilter(new Zend_Filter_StringTrim());
        $course_name->setRequired(true);
		$course_name->setAttrib('maxLength', 50);
        $course_name->addValidator('NotEmpty', false, array('messages' => 'Please enter course name.'));
		
		$course_name->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[a-zA-Z0-9\-\s]+$/i',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid course name.'
								 )
							 )
						 )
					 )); 
		
		   
		// course_level...
        $course_level = new Zend_Form_Element_Text('course_level');
        $course_level->addFilter(new Zend_Filter_StringTrim());
        $course_level->setRequired(true);
		$course_level->setAttrib('maxLength', 50);
        $course_level->addValidator('NotEmpty', false, array('messages' => 'Please enter course level.'));
        
		$course_level->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[a-zA-Z0-9\.\-\s]+$/i',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid course level.'
								 )
							 )
						 )
					 )); 
        
        //issued_date
        $issued_date = new ZendX_JQuery_Form_Element_DatePicker('issued_date');
		$issued_date->setOptions(array('class' => 'brdr_none'));	
		$issued_date->setAttrib('readonly', 'true');
        $issued_date->setAttrib('onfocus', 'this.blur()');		
        
        // description ....
		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);	
		
       
        
		
		
		//course_offered_by ....
		$course_offered_by = new Zend_Form_Element_Text('course_offered_by');
        $course_offered_by->addFilter(new Zend_Filter_StringTrim());
        $course_offered_by->setRequired(true);
		$course_offered_by->setAttrib('maxLength', 50);
        $course_offered_by->addValidator('NotEmpty', false, array('messages' => 'Please enter course offered by.'));
		
		$course_offered_by->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[a-zA-Z0-9\-\s]+$/i',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid name.'
								 )
							 )
						 )
					 )); 
		//Referer mobile number ....
		$certification_name = new Zend_Form_Element_Text('certification_name');
        $certification_name->addFilter(new Zend_Filter_StringTrim());
       
       
	   $certification_name->setAttrib('maxLength', 50);
		
		$certification_name->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[a-zA-z0-9\-\#\.\s]+$/i',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid certification name.'
								 )
							 )
						 )
					 )); 
		
		$certificationNameStr = Zend_Controller_Front::getInstance()->getRequest()->getParam('certification_name',null);
		//If certification is done then should enter the issue date......
		if($certificationNameStr != "")
		{
			$issued_date->setRequired(true);
			$issued_date->addValidator('NotEmpty', false, array('messages' => 'Please select date.'));
		}
	   //Form Submit....
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		$this->addElements(array($id,$user_id,$certification_name,$course_offered_by,$description,$issued_date,$course_level,$course_name,$submit));
       $this->setElementDecorators(array('ViewHelper')); 
		
		$this->setElementDecorators(array(
                    'UiWidgetElement',
        ),array('issued_date'));
		
		
        
	}
	
}
         