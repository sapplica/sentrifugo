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
class Default_Form_Workschedule extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','workScheduleFrm');
		$this->setAttrib('name','workScheduleFrm');

		$businessunit_id = new Zend_Form_Element_Hidden('bunit_id');
    $department_id = new Zend_Form_Element_Hidden('dept_id');

    $businessunit_id = new Zend_Form_Element_Select('businessunit_id');
    $businessunit_id->setLabel("Business Unit");
 		$businessunit_id->setRequired(true);
		$businessunit_id->setRegisterInArrayValidator(false);
		$businessunit_id->addValidator('NotEmpty', false, array('messages' => 'Please select business unit.'));
                       
    $department_id = new Zend_Form_Element_Select('department_id');
    $department_id->setLabel("Department");
    $department_id->setRequired(true);
		$department_id->setRegisterInArrayValidator(false);
		$department_id->addMultiOptions(array('' => 'Select Department'));   
		$department_id->addValidator('NotEmpty', false, array('messages' => 'Please select department.'));
       
    $startdate = new Zend_Form_Element_Text('startdate');
		$startdate->setAttrib('readonly', 'true');
		$startdate->setAttrib('onfocus', 'this.blur()');
		$startdate->setOptions(array('class' => 'brdr_none'));	
		$startdate->setRequired(true);
    $startdate->addValidator('NotEmpty', false, array('messages' => 'Please select start date.'));			
       
    $enddate = new Zend_Form_Element_Text('enddate');
		$enddate->setAttrib('readonly', 'true');
		$enddate->setAttrib('onfocus', 'this.blur()');
		$enddate->setOptions(array('class' => 'brdr_none'));	
		$enddate->setRequired(true);
    $enddate->addValidator('NotEmpty', false, array('messages' => 'Please select end date.'));			

		$sun_duration = new Zend_Form_Element_Text('sun_duration');
    $sun_duration->setAttrib('maxLength', 2);
    $sun_duration->addFilter(new Zend_Filter_StringTrim());
		$sun_duration->addValidators(
			array(
				array(
					'validator'   => 'Regex',
					'breakChainOnFailure' => true,
					'options'     => array( 
						'pattern'=>'/^([0-9]|[1][0-9]|[2][0-4])$/', 
						'messages' => array(
							'regexNotMatch'=>'Please enter value from 0 to 24.'
						)
					)
				)
			)
		); 

		$mon_duration = new Zend_Form_Element_Text('mon_duration');
    $mon_duration->setAttrib('maxLength', 2);
    $mon_duration->addFilter(new Zend_Filter_StringTrim());
		$mon_duration->addValidators(
			array(
				array(
					'validator'   => 'Regex',
					'breakChainOnFailure' => true,
					'options'     => array( 
						'pattern'=>'/^([0-9]|[1][0-9]|[2][0-4])$/', 
						'messages' => array(
							'regexNotMatch'=>'Please enter value from 0 to 24.'
						)
					)
				)
			)
		); 

		$tue_duration = new Zend_Form_Element_Text('tue_duration');
    $tue_duration->setAttrib('maxLength', 2);
    $tue_duration->addFilter(new Zend_Filter_StringTrim());
		$tue_duration->addValidators(
			array(
				array(
					'validator'   => 'Regex',
					'breakChainOnFailure' => true,
					'options'     => array( 
						'pattern'=>'/^([0-9]|[1][0-9]|[2][0-4])$/', 
						'messages' => array(
							'regexNotMatch'=>'Please enter value from 0 to 24.'
						)
					)
				)
			)
		); 

		$wed_duration = new Zend_Form_Element_Text('wed_duration');
    $wed_duration->setAttrib('maxLength', 2);
    $wed_duration->addFilter(new Zend_Filter_StringTrim());
		$wed_duration->addValidators(
			array(
				array(
					'validator'   => 'Regex',
					'breakChainOnFailure' => true,
					'options'     => array( 
						'pattern'=>'/^([0-9]|[1][0-9]|[2][0-4])$/', 
						'messages' => array(
							'regexNotMatch'=>'Please enter value from 0 to 24.'
						)
					)
				)
			)
		); 

		$thu_duration = new Zend_Form_Element_Text('thu_duration');
    $thu_duration->setAttrib('maxLength', 2);
    $thu_duration->addFilter(new Zend_Filter_StringTrim());
		$thu_duration->addValidators(
			array(
				array(
					'validator'   => 'Regex',
					'breakChainOnFailure' => true,
					'options'     => array( 
						'pattern'=>'/^([0-9]|[1][0-9]|[2][0-4])$/', 
						'messages' => array(
							'regexNotMatch'=>'Please enter value from 0 to 24.'
						)
					)
				)
			)
		); 

		$fri_duration = new Zend_Form_Element_Text('fri_duration');
    $fri_duration->setAttrib('maxLength', 2);
    $fri_duration->addFilter(new Zend_Filter_StringTrim());
		$fri_duration->addValidators(
			array(
				array(
					'validator'   => 'Regex',
					'breakChainOnFailure' => true,
					'options'     => array( 
						'pattern'=>'/^([0-9]|[1][0-9]|[2][0-4])$/', 
						'messages' => array(
							'regexNotMatch'=>'Please enter value from 0 to 24.'
						)
					)
				)
			)
		); 

		$sat_duration = new Zend_Form_Element_Text('sat_duration');
    $sat_duration->setAttrib('maxLength', 2);
    $sat_duration->addFilter(new Zend_Filter_StringTrim());
		$sat_duration->addValidators(
			array(
				array(
					'validator'   => 'Regex',
					'breakChainOnFailure' => true,
					'options'     => array( 
						'pattern'=>'/^([0-9]|[1][0-9]|[2][0-4])$/', 
						'messages' => array(
							'regexNotMatch'=>'Please enter value from 0 to 24.'
						)
					)
				)
			)
		); 

		$submitBtn = new Zend_Form_Element_Submit('submit');
		$submitBtn->setAttrib('id','submitbutton');
		$submitBtn->setLabel('Save');

		$this->addElements(array($businessunit_id,$department_id,$startdate,$enddate,$sun_duration,$mon_duration,$tue_duration,$wed_duration,$thu_duration,$fri_duration,$sat_duration,$submitBtn));
		$this->setElementDecorators(array('ViewHelper'));
	}
}
?>