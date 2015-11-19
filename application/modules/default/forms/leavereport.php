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

class Default_Form_leavereport extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'leavereport');


        $id = new Zend_Form_Element_Hidden('id');
		
		$employeename = new Zend_Form_Element_Text('employeename');
		$employeename->setLabel('Leave Applied By');
        $employeename->setAttrib('onblur', 'clearautocompletename(this)');		
        		
		$department = new Zend_Form_Element_Select('department');
		$department->setLabel('Department');
		$department->addMultiOption('','Select Department');
        $department->setAttrib('class', 'selectoption');
        $department->setRegisterInArrayValidator(false);
              
        $leavestatus = new Zend_Form_Element_Select('leavestatus');
		$leavestatus->setLabel('Leave Status');
        $leavestatus->setMultiOptions(array(
                            ''=>'Select Leave Status',
							'1'=>'Pending for approval' ,
							'2'=>'Approved',
							'3'=>'Rejected',
							'4'=>'Cancel',
							));
        $leavestatus->setRegisterInArrayValidator(false);	

        $from_date = new ZendX_JQuery_Form_Element_DatePicker('from_date');
		$from_date->setLabel('Applied Date');
		$from_date->setAttrib('readonly', 'true');
		$from_date->setAttrib('onfocus', 'this.blur()');
		$from_date->setOptions(array('class' => 'brdr_none'));	
		
        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		$this->addElements(array($id,$employeename,$department,$leavestatus,$from_date,$submit));
        $this->setElementDecorators(array('ViewHelper'));
        $this->setElementDecorators(array(
                    'UiWidgetElement',
        ),array('from_date'));   		 
	}
}