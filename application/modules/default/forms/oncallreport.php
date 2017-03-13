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

class Default_Form_oncallreport extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'oncallreport');


        $id = new Zend_Form_Element_Hidden('id');
		
		$employeename = new Zend_Form_Element_Text('employeename');
		$employeename->setLabel('On Call Applied By');
        $employeename->setAttrib('onblur', 'clearautocompletename(this)');		
        		
		$department = new Zend_Form_Element_Select('department');
		$department->setLabel('Department');
		$department->addMultiOption('','Select Department');
        $department->setAttrib('class', 'selectoption');
        $department->setRegisterInArrayValidator(false);
              
        $oncallstatus = new Zend_Form_Element_Select('oncallstatus');
		$oncallstatus->setLabel('On Call Status');
        $oncallstatus->setMultiOptions(array(
                            ''=>'Select On Call Status',
							'1'=>'Pending for approval' ,
							'2'=>'Approved',
							'3'=>'Rejected',
							'4'=>'Cancel',
							));
        $oncallstatus->setRegisterInArrayValidator(false);	

        $from_date = new ZendX_JQuery_Form_Element_DatePicker('from_date');
		$from_date->setLabel('From Date');
		$from_date->setAttrib('readonly', 'true');
		$from_date->setAttrib('onfocus', 'this.blur()');
		$from_date->setOptions(array('class' => 'brdr_none'));	

        $to_date = new ZendX_JQuery_Form_Element_DatePicker('to_date');
		$to_date->setLabel('To Date');
		$to_date->setAttrib('readonly', 'true');
		$to_date->setAttrib('onfocus', 'this.blur()');
		$to_date->setOptions(array('class' => 'brdr_none'));	
		
        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		$this->addElements(array($id,$employeename,$department,$oncallstatus,$from_date,$to_date,$submit));
        $this->setElementDecorators(array('ViewHelper'));
        $this->setElementDecorators(array(
                    'UiWidgetElement',
        ),array('from_date'));   		 
        $this->setElementDecorators(array(
                    'UiWidgetElement',
        ),array('to_date'));   		 
	}
}