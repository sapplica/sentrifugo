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

class Default_Form_empjobhistory extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'empjobhistory');
		
		$id = new Zend_Form_Element_Hidden('id');
				
		$userid = new Zend_Form_Element_Hidden('user_id');
				
		$positionheld = new Zend_Form_Element_Select('positionheld');
		$positionheld->setLabel('Position');
		$positionheld->setRegisterInArrayValidator(false);
		$positionheld->setRequired(true);
	    $positionheld->addValidator('NotEmpty', false, array('messages' => 'Please select position.'));
									
					
		$department = new Zend_Form_Element_Select('department');
		$department->setLabel('Department');
		$department->setRegisterInArrayValidator(false);
		$department->setRequired(true);
		$department->addValidator('NotEmpty', false, array('messages' => 'Please select department.'));
		

        $jobtitleid = new Zend_Form_Element_Select('jobtitleid');
		$jobtitleid->setLabel('Job Title');
		$jobtitleid->setRegisterInArrayValidator(false);
		$jobtitleid->setRequired(true);
		$jobtitleid->addValidator('NotEmpty', false, array('messages' => 'Please select job title.'));
		
	
        $start_date = new ZendX_JQuery_Form_Element_DatePicker('start_date');
		$start_date->setLabel('From');
		$start_date->setOptions(array('class' => 'brdr_none'));	
		$start_date->setAttrib('readonly', 'true');	
		$start_date->setAttrib('onfocus', 'this.blur()');
		
		$end_date = new ZendX_JQuery_Form_Element_DatePicker('end_date');
		$end_date->setLabel('To');
		$end_date->setOptions(array('class' => 'brdr_none'));	
		$end_date->setAttrib('readonly', 'true');	
		$end_date->setAttrib('onfocus', 'this.blur()');
		
		
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');
		
		$this->addElements(array($id,$userid,$positionheld,$jobtitleid,$department,$start_date,$end_date,$submit));
        $this->setElementDecorators(array('ViewHelper'));
         $this->setElementDecorators(array(
                    'UiWidgetElement',
        ),array('start_date','end_date')); 		
	}
}