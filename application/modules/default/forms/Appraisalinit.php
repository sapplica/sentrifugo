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

class Default_Form_Appraisalinit extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'appraisalinit');

        $id = new Zend_Form_Element_Hidden('id');
        $businessunit_id = new Zend_Form_Element_Hidden('businessunit_id');
        $department_id = new Zend_Form_Element_Hidden('department_id');
		
        $businessunit_name = new Zend_Form_Element_Text('businessunit_name');
		$businessunit_name->setLabel("Business Unit");
		$businessunit_name->setAttrib('readonly', 'true');
		$businessunit_name->setAttrib('onfocus', 'this.blur()');
        $businessunit_name->setOptions(array('class' => 'brdr_none'));
		
		$department_name = new Zend_Form_Element_Text('department_name');
		$department_name->setLabel("Department");
		$department_name->setAttrib('readonly', 'true');
		$department_name->setAttrib('onfocus', 'this.blur()');
        $department_name->setOptions(array('class' => 'brdr_none'));
        
		$appraisal_mode = new Zend_Form_Element_Text('appraisal_mode');
		$appraisal_mode->setLabel("Period");
		$appraisal_mode->setAttrib('readonly', 'true');
		$appraisal_mode->setAttrib('onfocus', 'this.blur()');
        $appraisal_mode->setOptions(array('class' => 'brdr_none'));
		
		
		
		$status = new Zend_Form_Element_Select('status');
		$status->setLabel("Status");
        $status->setAttrib('class', 'selectoption');
        $status->setMultiOptions(array(''=>'Select Status','1'=>'Open','2'=>'close'));
        $status->setRegisterInArrayValidator(false);
        $status->setRequired(true);
		$status->addValidator('NotEmpty', false, array('messages' => 'Please select status.'));
		
		$eligibility = new Zend_Form_Element_Multiselect('eligibility');
		$eligibility->setLabel("Eligibility");
        $eligibility->setAttrib('class', 'selectoption');
        $eligibility->setMultiOptions(array(''=>'Select Eligibility'));
        $eligibility->setRegisterInArrayValidator(false);
        $eligibility->setRequired(true);
		$eligibility->addValidator('NotEmpty', false, array('messages' => 'Please select eliblity.'));
		
		$enable = new Zend_Form_Element_Select('enable_step');
		$enable->setLabel("Enable To");
        $enable->setAttrib('class', 'selectoption');
        $enable->setMultiOptions(array(''=>'Select Enable To','1'=>'Managers','2'=>'Employees'));
        $enable->setRegisterInArrayValidator(false);
        $enable->setRequired(true);
		$enable->addValidator('NotEmpty', false, array('messages' => 'Please select enable to.'));
		$enable->setAttrib('onchange', 'enableOnChange(this)');
		
		$mgr_due_date = new Zend_Form_Element_Text('manager_due_date');
		$mgr_due_date->setLabel("Due Date");
        $mgr_due_date->setAttrib('readonly', 'true');
        $mgr_due_date->setAttrib('onfocus', 'this.blur()');
        $mgr_due_date->setOptions(array('class' => 'brdr_none'));
        
		$mgr_due_date->addValidator('NotEmpty', false, array('messages' => 'Please select due date.'));
		
		$emp_due_date = new Zend_Form_Element_Text('employee_due_date');
		$emp_due_date->setLabel("Due Date");
        $emp_due_date->setAttrib('readonly', 'true');
        $emp_due_date->setAttrib('onfocus', 'this.blur()');
        $emp_due_date->setOptions(array('class' => 'brdr_none'));
        
		$emp_due_date->addValidator('NotEmpty', false, array('messages' => 'Please select due date.'));
		
        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save'); 		
		$this->addElements(array($id,$businessunit_id,$department_id,$businessunit_name,$department_name,
							$appraisal_mode,$status,$eligibility,$enable,$mgr_due_date,$emp_due_date,$submit));
        $this->setElementDecorators(array('ViewHelper'));
	}
}