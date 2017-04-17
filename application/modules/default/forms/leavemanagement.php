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

class Default_Form_leavemanagement extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		//$this->setAttrib('action',BASE_URL.'language/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'leavemanagement');


        $id = new Zend_Form_Element_Hidden('id');
		
		
		$startmonth = new Zend_Form_Element_Select('cal_startmonth');
        $startmonth->setRegisterInArrayValidator(false);
        $startmonth->addMultiOption('','Select Calendar Start Month');
        $startmonth->setRequired(true);
		$startmonth->addValidator('NotEmpty', false, array('messages' => 'Please select calendar start month.'));
		
		$weekend_startday = new Zend_Form_Element_Select('weekend_startday');
        $weekend_startday->setRegisterInArrayValidator(false);
		$weekend_startday->addMultiOption('','Select Weekend Start Day');
        $weekend_startday->setRequired(true);
		$weekend_startday->addValidator('NotEmpty', false, array('messages' => 'Please select weekend day 1.'));
				
		$weekend_endday = new Zend_Form_Element_Select('weekend_endday');
        $weekend_endday->setRegisterInArrayValidator(false);
		$weekend_endday->addMultiOption('','Select Weekend End Day');
        $weekend_endday->setRequired(true);
		$weekend_endday->addValidator('NotEmpty', false, array('messages' => 'Please select weekend day 2.'));
		
		$businessunit = new Zend_Form_Element_Select('businessunit');
		$businessunit->setAttrib('onchange', 'displayEmployeeDepartments(this,"department_id","leavemanagement")');
		$businessunit->setRegisterInArrayValidator(false);	
      	$businessunit->setRequired(true);
		$businessunit->addValidator('NotEmpty', false, array('messages' => 'Please select business unit.'));
		
		$department_id = new Zend_Form_Element_Select('department_id');
		$department_id->addMultiOption('','Select Department');
        $department_id->setRegisterInArrayValidator(false);
        $department_id->setRequired(true);
		$department_id->addValidator('NotEmpty', false, array('messages' => 'Please select department.')); 
	
		$hours_day = new Zend_Form_Element_Text('hours_day');
        $hours_day->setAttrib('maxLength', 2);
        $hours_day->addFilter(new Zend_Filter_StringTrim());
        $hours_day->setRequired(true);
        $hours_day->addValidator('NotEmpty', false, array('messages' => 'Please enter number of working hours.')); 
		$hours_day->addValidator("regex",true,array(
                            'pattern'=>'/^([1-9]|[1][0-9]|[2][0-4])$/', 
                           'messages'=>array(
                               'regexNotMatch'=>'Working hours cannot be more than 24 hours and should not be equal to 0.'
                           )
        	));

        $halfday = new Zend_Form_Element_Select('is_halfday');
        $halfday->setRegisterInArrayValidator(false);
        $halfday->setMultiOptions(array(							
							'1'=>'Yes' ,
							'2'=>'No',
							));
        $halfday->setRequired(true);
		$halfday->addValidator('NotEmpty', false, array('messages' => 'Please select option.'));

        $leavetransfer = new Zend_Form_Element_Select('is_leavetransfer');
        $leavetransfer->setRegisterInArrayValidator(false);
        $leavetransfer->setMultiOptions(array(							
							'1'=>'Yes' ,
							'2'=>'No',
							));	
        $leavetransfer->setRequired(true);
		$leavetransfer->addValidator('NotEmpty', false, array('messages' => 'Please select option.'));

        $skipholidays = new Zend_Form_Element_Select('is_skipholidays');
        $skipholidays->setRegisterInArrayValidator(false);
        $skipholidays->setMultiOptions(array(							
							'1'=>'Yes' ,
							'2'=>'No',
							));
        $skipholidays->setRequired(true);
		$skipholidays->addValidator('NotEmpty', false, array('messages' => 'Please select option.'));
		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');
		
		
        $hrmanager = new Zend_Form_Element_Select('hrmanager');
        $hrmanager->setRegisterInArrayValidator(false);
        $hrmanager->setRequired(true);
        $hrmanager->addMultiOption('','Select Hr Manager');
		$hrmanager->addValidator('NotEmpty', false, array('messages' => 'Please select hr manager.')); 	

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 $this->addElements(array($id,$startmonth,$weekend_startday,$weekend_endday,$businessunit,$department_id,$hours_day,$halfday,$skipholidays,$leavetransfer,$description,$hrmanager,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}